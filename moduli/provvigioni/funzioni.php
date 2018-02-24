<?php

/** FUNZIONI DI CROCCO */
function Stampa_HTML_index_Provvigioni($tabella){
    global $dblink, $table_listaProvvigioni;

    switch($tabella){
        
        case "lista_provvigioni":
            $tabella = "lista_provvigioni";
            $where = $table_listaProvvigioni['index']['where'];
            $campi_visualizzati = $table_listaProvvigioni['index']['campi'];
            $titolo = 'Elenco Partner';
            $colore = COLORE_PRIMARIO;
            $ordine = $table_listaProvvigioni['index']['order'];
            $sql_0001 = "SELECT " . $campi_visualizzati . " FROM " . $tabella . " WHERE $where $ordine LIMIT 1";
            stampa_table_datatables_ajax($sql_0001, '#datatable_ajax', $titolo, 'datatable_ajax', $colore);
        break;

        default:
            
            $campi_visualizzati = "";
            $campi     =    $dblink->list_fields("SELECT * FROM ".$tabella."");
            foreach ($campi as $nome_colonna) {
                 $campi_visualizzati.= "`".$nome_colonna->name."`, ";
            }


            $campi_visualizzati = substr($campi_visualizzati, 0, -2);
            $where = " 1 ";
            $ordine = " ORDER BY id DESC";
            $titolo = "Elenco ".$tabella;
            $stile = "tabella_base";
            $colore_tabella = COLORE_PRIMARIO;
            $sql_0001 = "SELECT
            CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"dettaglio.php?tbl=".$tabella."&id=',id,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',
            CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"modifica.php?tbl=".$tabella."&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',
            CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"duplica.php?tbl=".$tabella."&id=',id,'\" title=\"DUPLICA\" alt=\"DUPLICA\"><i class=\"fa fa-copy\"></i></a>') AS 'fa-copy',
            ".$campi_visualizzati.",
            CONCAT('<a class=\"btn btn-circle btn-icon-only red btn-outline\" href=\"cancella.php?tbl=".$tabella."&id=',id,'\" title=\"ELIMINA\" alt=\"ELIMINA\"><i class=\"fa fa-trash\"></i></a>') AS 'fa-trash'
            FROM ".$tabella." WHERE $where $ordine LIMIT 1";
            //echo '<li>$sql_0001 = '.$sql_0001.'</li>';
            stampa_table_datatables_ajax($sql_0001, "datatable_ajax", $titolo, '', '', false);
            //stampa_table_datatables_responsive($sql_0001, $titolo, $stile, $colore_tabella);
        break;

    }
}

function Stampa_HTML_Dettaglio_Provvigioni($tabella, $id){
    global $dblink, $table_listaProvvigioni;
    
    switch ($tabella) {
        case 'lista_provvigioni':
            echo '<div class="row"><div class="col-md-12 col-sm-12">';
                $tabella = "lista_provvigioni";
                $where = "id = '$id'";
                $campi_visualizzati = "CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"modifica.php?tbl=lista_provvigioni&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',
                                        CONCAT('<span class=\"btn btn-lg sbold uppercase btn-outline blue-madison\">',codice,'</span>') AS 'Codice',
                                        CONCAT('<span class=\"btn sbold uppercase btn-outline blue-dark\">',`nome`,'</span>') AS Nome,
                                        descrizione,
                                        (SELECT DISTINCT CONCAT('<B>',`nome` ,'</B><BR><SMALL>',codice,'</SMALL>')FROM `lista_prodotti` WHERE `id` = `id_prodotto`) AS 'Prodotto',
                                        prezzo_sconto,
                                        provvigione AS 'Provvigione &euro;',
                                        provvigione_percentuale AS 'Provvigione %',
                                        `stato`";
                $titolo = 'Dettaglio Partner';
                $colore = COLORE_PRIMARIO;
                $ordine = $table_listaProvvigioni['index']['order'];
                $sql_0001 = "SELECT " . $campi_visualizzati . " FROM " . $tabella . " WHERE $where $ordine LIMIT 1";
                stampa_table_static_basic($sql_0001, '', $titolo, '');
            echo '</div></div>';
            
            echo '<div class="row"><div class="col-md-12 col-sm-12">';
                $sql_0002 = "SELECT 
                CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"".BASE_URL."/moduli/provvigioni/printXLS.php?anno=',YEAR(data_creazione),'&mese=',MONTH(data_creazione),'&id_provvigione=',id_provvigione,'\" target=\"_blank\" title=\"XLS CODICI PARTNER\" alt=\"XLS CODICI PARTNER\"><i class=\"fa fa-file-excel-o\"></i></a>') as 'fa-file-excel-o',
                CONCAT('<a class=\"btn btn-circle btn-icon-only blue-sharp btn-outline\" href=\"".BASE_URL."/moduli/provvigioni/printXLS.php?anno=',YEAR(data_creazione),'&mese=',MONTH(data_creazione),'&id_provvigione=',id_provvigione,'&iscritti=1\" target=\"_blank\" title=\"XLS PROFESSIONISTI ISCRITTI PER PARTNER\" alt=\"XLS PROFESSIONISTI ISCRITTI PER PARTNER\"><i class=\"fa fa-file-o\"></i> ',COUNT(lista_fatture.id_professionista),'</a>') as 'Iscritti',
                (SELECT nome FROM lista_provvigioni WHERE id = id_provvigione) as Partner,
                YEAR(data_creazione) AS Anno, MONTH(data_creazione) AS Mese, SUM(imponibile) AS Imponibile, COUNT(lista_fatture.stato) AS CONTEGGIO_FATTURE, lista_fatture.tipo, lista_fatture.sezionale, lista_fatture.stato 
                FROM lista_fatture_dettaglio INNER JOIN lista_fatture ON lista_fatture.id = lista_fatture_dettaglio.id_fattura WHERE id_provvigione = '$id'
                GROUP BY YEAR(data_creazione), MONTH(data_creazione), lista_fatture.tipo, lista_fatture.sezionale, lista_fatture.stato 
                ORDER BY YEAR(data_creazione) DESC, MONTH(data_creazione) DESC, lista_fatture.tipo, lista_fatture.sezionale, lista_fatture.stato ASC;";
                stampa_table_static_basic($sql_0002, 'tabella_base2', 'Andamento Fatture Partners', '', 'fa fa-user');
            echo '</div></div>';
            
            /*echo '<div class="row"><div class="col-md-12 col-sm-12">';
            $sql_0001 = "SELECT CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"modifica.php?tbl=lista_docenti&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',
            CONCAT('<b>',`cognome`,' ',`nome`,'</b>') AS 'Docente', codice_fiscale, cellulare, telefono, email, stato
            FROM lista_docenti
            WHERE id='".$id."'";
            stampa_table_static_basic($sql_0001, '', 'Docente', '');
            echo '</div></div>';
            //CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"modifica.php?tbl=lista_iscrizioni&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',

            echo '<div class="row"><div class="col-md-12 col-sm-12">';
            $sql_0001 = "SELECT id, data
            FROM calendario
            WHERE id_docente = '".$id."' ORDER BY id DESC";
            stampa_table_static_basic($sql_0001, '', 'Esami', '');
            echo '</div></div>';*/
        break;
        
        default:
        break;
    }
}
?>
