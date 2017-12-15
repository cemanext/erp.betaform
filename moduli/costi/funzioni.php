<?php

/** FUNZIONI DI CROCCO */
function Stampa_HTML_index_Costi($tabella){
    global $table_listaCosti, $dblink;
    
    $tabella = 'lista_costi';
    switch($tabella){
    
        case 'lista_costi':
                $tabella = "lista_costi";
                if(isset($_GET['whr_state'])){
                    $where = " (MD5(stato)=('".$_GET['whr_state']."'))".$where_lista_costi;
                }else{
                    $where = $table_listaCosti['index']['where'];
                }
              
                $campi_visualizzati = $table_listaCosti['index']['campi'];
                $ordine = $table_listaCosti['index']['order'];
                
                
                $sql_000001 = "UPDATE lista_costi, lista_fatture
                SET lista_costi.id_fatture_banche = lista_fatture.id_fatture_banche
                WHERE lista_costi.id_fattura = lista_fatture.id AND lista_costi.id_fatture_banche <= 0";
                $rs_000001 = $dblink->query($sql_000001);
                
                $sql_000001 = "UPDATE lista_costi, lista_fatture_banche
                SET lista_costi.nome_banca = lista_fatture_banche.nome
                WHERE lista_costi.id_fatture_banche = lista_fatture_banche.id";
                $rs_000001 = $dblink->query($sql_000001);
                
                $titolo = 'Elenco Entrate / Uscite';
                $sql_0001 = "SELECT ".$campi_visualizzati." FROM ".$tabella." WHERE $where $ordine LIMIT 1";
                //stampa_table_datatables_responsive($sql_0001, $titolo, 'tabella_base');
                stampa_table_datatables_ajax($sql_0001, '#datatable_ajax', $titolo, '');
        break;
    }
}

function Stampa_HTML_Modifica_Costi($tabella,$id,$titolo){
    global $table_listaCosti;
    switch ($tabella) {
        case "lista_costi":
            stampa_bootstrap_form_horizontal($tabella,$id,$titolo, "salva.php?fn=salvaGenerale");
        break;

        case "lista_costi_dettaglio":
            stampa_bootstrap_form_horizontal($tabella,$id,$titolo,'salva.php?fn=salvaPreventivoDettaglio');
        break;
        
        default:
            break;
    }
    
}

function Stampa_HTML_Dettaglio_Costi($tabella, $id) {
global $table_listaCosti;
switch ($tabella) {
        case 'lista_costi':
            echo '<div class="row"><div class="col-md-12 col-sm-12">';
            $sql_0001 = "SELECT CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"modifica.php?tbl=lista_costi&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',
            CONCAT('<a class=\"btn btn-circle btn-icon-only red btn-outline\" href=\"cancella.php?tbl=lista_costi&id=',id,'\" title=\"ELIMINA\" alt=\"ELIMINA\"><i class=\"fa fa-trash\"></i></a>') AS 'fa-trash',
            DATE_FORMAT(DATE(data_creazione), '%d-%m-%Y') AS 'Creazione',
            DATE_FORMAT(DATE(data_scadenza), '%d-%m-%Y') AS 'Scadenza',
            (SELECT CONCAT('<h4>',cognome,' ',nome,'</h4>') FROM lista_professionisti WHERE id = `id_professionista`) AS 'Professionista',
            (SELECT CONCAT('<h4>',ragione_sociale,'</h4>') FROM lista_aziende WHERE id = `id_azienda`) AS 'Azienda',
             IF(`entrate`>0,CONCAT('<FONT COLOR=\"GREEN\">',`entrate`,'</FONT>'),CONCAT('<FONT COLOR=\"RED\">',`uscite`,'</FONT>')) AS imponibile,
             `tipo`, `stato` FROM lista_costi WHERE id='".$id."'";
            stampa_table_static_basic($sql_0001, '', 'Dettaglio', 'green');
            echo '</div></div>';
        break;
    }
}
?>
