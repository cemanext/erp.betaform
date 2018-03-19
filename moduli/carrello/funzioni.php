<?php

/** FUNZIONI DI CROCCO */
function Stampa_HTML_index_Ordini($tabella){
    global $table_listaOrdini;
    
    $tabella = 'lista_ordini';
    switch($tabella){
    
        case 'lista_ordini':
                $tabella = "lista_ordini";
                if(isset($_GET['whr_state'])){
                    $where = " (MD5(stato)=('".$_GET['whr_state']."'))".$where_lista_ordini;
                }else{
                    $where = $table_listaOrdini['index']['where'];
                }
              
                $campi_visualizzati = $table_listaOrdini['index']['campi'];
                $ordine = $table_listaOrdini['index']['order'];
                
                $titolo = 'Elenco Ordini Shop';
                $sql_0001 = "SELECT ".$campi_visualizzati." FROM ".$tabella." WHERE $where $ordine LIMIT 1";
                //stampa_table_datatables_responsive($sql_0001, $titolo, 'tabella_base');
                stampa_table_datatables_ajax($sql_0001, '#datatable_ajax', $titolo, 'datatable_ajax');
        break;
    }
}

function Stampa_HTML_Modifica_Ordini($tabella,$id,$titolo){
    global $table_listaOrdini;
    switch ($tabella) {
        case "lista_ordini":
            stampa_bootstrap_form_horizontal($tabella,$id,$titolo);
        break;

        case "lista_ordini_dettaglio":
            stampa_bootstrap_form_horizontal($tabella,$id,$titolo,'salva.php?fn=salvaOrdineDettaglio');
        break;
        
        default:
            break;
    }
    
}

function Stampa_HTML_Dettaglio_Ordini($tabella, $id) {
global $table_listaOrdini;
switch ($tabella) {
    case 'lista_ordini':
            echo '<div class="row"><div class="col-md-12 col-sm-12">';
            
            $sql_0001 = "SELECT id, dataagg, sezionale, codice, campo_20, (SELECT nome FROM lista_campagne WHERE id = id_campagna LIMIT 1) AS nome_campagna, stato FROM lista_ordini WHERE id=" . $id;
            stampa_table_static_basic($sql_0001, '', 'Ordine', '');
             
            $sql_0001 = "SELECT id_ordine, nome_prodotto, prezzo_prodotto, iva_prodotto, quantita, (SELECT nome FROM lista_campagne WHERE id = id_campagna LIMIT 1) AS nome_campagna, note FROM lista_ordini_dettaglio WHERE id_ordine=" . $id;
            stampa_table_static_basic($sql_0001, '', 'Ordine Dettaglio', '');
            
            $sql_0001 = "SELECT CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"".BASE_URL."/moduli/preventivi/dettaglio.php?tbl=lista_preventivi&id=',id,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',"
                    . " dataagg, data_iscrizione, sezionale, codice, stato FROM lista_preventivi WHERE id_ordine=" . $id;
            stampa_table_static_basic($sql_0001, '', 'Preventivi', '');
            
            echo '</div></div>';
            
        break;
        }
}
?>
