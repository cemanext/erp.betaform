<?php

/** FUNZIONI DI CROCCO */
function Stampa_HTML_index_Base($tabella){
    global $dblink, $table_listaPassword, $table_listaRichiesteStati, $table_listaProdotti, 
           $table_listaProdottiCategorie, $table_listaProdottiTipologie, $table_listaProdottiGruppi,
           $table_listaPasswordUtenti, $where_lista_prodotti;

    switch($tabella){
    
        case 'lista_iscritti':
            $tabella = "lista_iscrizioni";
            $titolo = 'Elenco Utenti Attivi';
            
            $sql_0001 = "SELECT 'fa-search', 'tipo', 'data_fine_iscrizione', 'partecipante', 'classe', 'email', 'telefono' FROM $tabella WHERE 1";
            
            stampa_table_datatables_ajax($sql_0001, "datatable_ajax", $titolo, '', 'green-turquoise', false);
            //stampa_table_datatables_responsive($sql_0001, $titolo, 'tabella_base');
        break;
    
        case 'lista_disattivi':
            $tabella = "lista_iscrizioni";
            $titolo = 'Elenco Utenti Disattivi';
            
            $sql_0001 = "SELECT 'fa-search', 'tipo', 'data_fine_iscrizione', 'partecipante', 'classe', 'email', 'telefono' FROM $tabella WHERE 1";
            
            stampa_table_datatables_ajax($sql_0001, "datatable_ajax", $titolo, '', 'red-intense', false);
            //stampa_table_datatables_responsive($sql_0001, $titolo, 'tabella_base');
        break;
        
        case 'lista_prodotti_gruppi':
            $tabella = "lista_prodotti_gruppi";
            $campi_visualizzati = $table_listaProdottiGruppi['index']['campi'];
            $where = $table_listaProdottiGruppi['index']['where'];
            $ordine = $table_listaProdottiGruppi['index']['order'];
            $titolo = 'Elenco Gruppi Prodotto';
            $sql_0001 = "SELECT ".$campi_visualizzati." FROM ".$tabella." WHERE $where $ordine";
            stampa_table_datatables_responsive($sql_0001, $titolo);
        break;

        case 'lista_prodotti_tipologie':
            $tabella = "lista_prodotti_tipologie";
            $campi_visualizzati = $table_listaProdottiTipologie['index']['campi'];
            $where = $table_listaProdottiTipologie['index']['where'];
            $ordine = $table_listaProdottiTipologie['index']['order'];
            $titolo = 'Tipologie Prodotti';
            $sql_0001 = "SELECT ".$campi_visualizzati." FROM ".$tabella." WHERE $where $ordine";
            stampa_table_datatables_responsive($sql_0001, $titolo);
        break;
        
        case 'lista_prodotti_categorie':
            $tabella = "lista_prodotti_categorie";
            $campi_visualizzati = $table_listaProdottiCategorie['index']['campi'];
            $where = $table_listaProdottiCategorie['index']['where'];
            $ordine = $table_listaProdottiCategorie['index']['order'];
            $titolo = 'Categorie Prodotti';
            $sql_0001 = "SELECT ".$campi_visualizzati." FROM ".$tabella." WHERE $where $ordine";
            stampa_table_datatables_responsive($sql_0001, $titolo);
        break;
    
        case 'lista_password':
            $tabella = "lista_password";
            $campi_visualizzati = $table_listaPassword['index']['campi'];
            $where = $table_listaPassword['index']['where'];
            $ordine = $table_listaPassword['index']['order'];
            $titolo = 'Elenco Password';
            $sql_0001 = "SELECT ".$campi_visualizzati." FROM ".$tabella." WHERE $where $ordine LIMIT 1";
            stampa_table_datatables_ajax($sql_0001, "datatable_ajax", $titolo, '', '', false);
            //stampa_table_datatables_responsive($sql_0001, $titolo, 'tabella_base');
        break;
    
        case 'lista_password_utenti':
            $tabella = "lista_password";
            $campi_visualizzati = $table_listaPasswordUtenti['index']['campi'];
            $where = " livello='cliente' ";
            $ordine = $table_listaPasswordUtenti['index']['order'];
            $titolo = 'Elenco Password';
            $sql_0001 = "SELECT ".$campi_visualizzati." FROM ".$tabella." WHERE $where $ordine LIMIT 1";
            stampa_table_datatables_ajax($sql_0001, "datatable_ajax", $titolo, '', '', false);
            //stampa_table_datatables_responsive($sql_0001, $titolo, 'tabella_base');
        break;
    
        case "lista_richieste_stati":
            $tabella = "lista_richieste_stati";
            $campi_visualizzati = $table_listaRichiesteStati['index']['campi'];
            $where = $table_listaRichiesteStati['index']['where'];
            $ordine = $table_listaRichiesteStati['index']['order'];
            $titolo = 'Elenco Richieste Stati';
            $sql_0001 = "SELECT ".$campi_visualizzati." FROM ".$tabella." WHERE $where $ordine";
            stampa_table_datatables_responsive($sql_0001, $titolo, 'tabella_base');
        break;
        
         case "lista_prodotti":
            $tabella = "lista_prodotti";
            if(isset($_GET['whr_gru'])){
                $whr_gru = $_GET['whr_gru'];
                switch($whr_gru){
                    case 1:
                        $campi_visualizzati = $table_listaProdotti['index']['campi'];
                        $where = " gruppo LIKE 'ABBONAMENTO'";
                        $ordine = $table_listaProdotti['index']['order'];
                        $titolo = 'Elenco Abbonamenti';                             
                    break;
                    
                    case 2:
                        $campi_visualizzati = $table_listaProdotti['index']['campi'];
                        $where = " gruppo LIKE 'CORSO'";
                        $ordine = $table_listaProdotti['index']['order'];
                        $titolo = 'Elenco Prodotti Singoli / Corsi';                             
                    break;
                    
                    case 3:
                        $campi_visualizzati = $table_listaProdotti['index']['campi'];
                        $where = " gruppo LIKE 'PACCHETTO'";
                        $ordine = $table_listaProdotti['index']['order'];
                        $titolo = 'Elenco Pacchetti';                             
                    break;
                    
                    case 5:
                        $campi_visualizzati = $table_listaProdotti['index']['campi'];
                        $where = " gruppo LIKE 'ESAME'";
                        $ordine = $table_listaProdotti['index']['order'];
                        $titolo = 'Elenco Esami';                             
                    break;
                
                }
            }else{
                $campi_visualizzati = $table_listaProdotti['index']['campi'];
                $where = $table_listaProdotti['index']['where'];
                $ordine = $table_listaProdotti['index']['order'];
                $titolo = 'Elenco Prodotti / Corsi';           
            }

            $sql_0001 = "SELECT ".$campi_visualizzati." FROM ".$tabella." WHERE $where $where_lista_prodotti $ordine";
            stampa_table_datatables_responsive($sql_0001, $titolo, 'tabella_base');
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
?>
