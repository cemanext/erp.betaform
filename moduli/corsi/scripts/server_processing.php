<?php
header('Content-Type: text/plain');
include_once('../../../config/connDB.php');
include_once(BASE_ROOT . 'config/confAccesso.php');

if (isset($_GET['tbl'])) {
    $tabella = $_GET['tbl'];
}

$orderColumn = $_REQUEST['order']; //numero colonna

$arrayCampoRicerca = array();
$campoRicerca = $_REQUEST['search']['value'];
if(strpos($campoRicerca," ")!==false){
    $arrayCampoRicerca = explode(" ",$campoRicerca);
}else{
    if(strlen($campoRicerca)>0){
        $arrayCampoRicerca[] = $campoRicerca;
    }
}

switch($tabella){
    
        case 'lista_attestati':
            $tabella = "lista_attestati";
            $campi_visualizzati = $table_documentiAttestati['index']['campi'];
            $where = $table_documentiAttestati['index']['where'];
            
            if(!empty($arrayCampoRicerca)){
                foreach ($arrayCampoRicerca as $campoRicerca) {
                    $campoRicerca = $dblink->filter($campoRicerca);
                    $where.= " AND ( nome LIKE '%".$campoRicerca."%' OR orientamento LIKE '%".$campoRicerca."%'";
                    $where.= " OR tipo_documento LIKE '%".$campoRicerca."%' OR descrizione LIKE '%".$campoRicerca."%')";
                }
            }
            
            $ordine = $table_documentiAttestati['index']['order'];
        break;
        
        case 'attestati_inviati':
            $tabella = "lista_iscrizioni";
            $campi_visualizzati = $table_listaIscrizioniPartecipanti['index']['campi'];
            $campi_visualizzati = str_replace("data_inizio,", "data_completamento,data_invio_attestato,", $campi_visualizzati);
            $campi_visualizzati.= ", CONCAT('<a class=\"btn btn-circle btn-icon-only red-intense btn-outline\" href=\"".BASE_URL."/moduli/corsi/printAttestatoPDF.php?idIscrizione=',id,'\" target=\"_blank\" title=\"STAMPA ATTESTATO\" alt=\"STAMPA ATTESTATO\"><i class=\"fa fa-file-pdf-o\"></i></a>') AS 'fa-file-pdf-o'";
            $where = "stato_invio_attestato LIKE 'Inviata'";
            
            if(!empty($arrayCampoRicerca)){
                foreach ($arrayCampoRicerca as $campoRicerca) {
                    $campoRicerca = $dblink->filter($campoRicerca);
                    $where.= " AND ( nome_cognome_professionista LIKE '%".$campoRicerca."%' OR nome_corso LIKE '%".$campoRicerca."%'";
                    $where.= " OR data_completamento LIKE '%".$campoRicerca."%' OR data_invio_attestato LIKE '%".$campoRicerca."%')";
                }
            }
            
            $ordine = $table_documentiAttestati['index']['order'];
        break;
        
        case 'calendario_esami':
            $tabella = "calendario";
            $campi_visualizzati = $table_calendarioEsami['index']['campi'];
            $where = $table_calendarioEsami['index']['where'];
            
            if(!empty($arrayCampoRicerca)){
                foreach ($arrayCampoRicerca as $campoRicerca) {
                    $campoRicerca = $dblink->filter($campoRicerca);
                    $where.= " AND ( data LIKE '%".$campoRicerca."%' OR ora LIKE '%".$campoRicerca."%'";
                    $where.= " OR oggetto LIKE '%".$campoRicerca."%' OR stato LIKE '%".$campoRicerca."%')";
                }
            }
            
            $ordine = $table_calendarioEsami['index']['order'];
        break;
        
        case 'lista_corsi':
            $tabella = "lista_corsi";
            $campi_visualizzati = $table_listaCorsi['index']['campi'];
            $where = $table_listaCorsi['index']['where'];
            
            if(!empty($arrayCampoRicerca)){
                foreach ($arrayCampoRicerca as $campoRicerca) {
                    $campoRicerca = $dblink->filter($campoRicerca);
                    $where.= " AND ( nome_prodotto LIKE '%".$campoRicerca."%' OR Durata LIKE '%".$campoRicerca."%'";
                    $where.= " OR codice LIKE '%".$campoRicerca."%' OR stato LIKE '%".$campoRicerca."%' OR id_corso_moodle LIKE '%".$campoRicerca."%')";
                }
            }
            
            $ordine = $table_listaCorsi['index']['order'];
        break;
    
        case 'lista_classi':
            $tabella = "lista_classi";
            $campi_visualizzati = $table_listaClassi['index']['campi'];
            $where = $table_listaClassi['index']['where'];
            
            if(!empty($arrayCampoRicerca)){
                foreach ($arrayCampoRicerca as $campoRicerca) {
                    $campoRicerca = $dblink->filter($campoRicerca);
                    $where.= " AND ( nome LIKE '%".$campoRicerca."%' OR codice LIKE '%".$campoRicerca."%'";
                    $where.= " OR codice_esterno LIKE '%".$campoRicerca."%' OR stato LIKE '%".$campoRicerca."%')";
                }
            }
            
            $ordine = $table_listaClassi['index']['order'];
            
        break;

        default:
                //securityLogut();
        break;
}

$sql_0001 = "SELECT count(id) AS conto FROM ".$tabella." WHERE $where $ordine";

$numRowRes = $dblink->get_row($sql_0001,true);
$numRow = $numRowRes['conto'];
//$numRow = 40052;
//$fields = $dblink->list_fields($sql_0001);

if(!empty($orderColumn)){
    $ordine = "ORDER BY ";
    foreach ($orderColumn as $order) {
        $ordine.= (intval($order['column'])+1)." ".$order['dir'].", ";
    }
    $ordine = substr($ordine, 0, -2);
}

$iTotalRecords = $numRow;
$iDisplayLength = intval($_REQUEST['length']);
$iDisplayLength = $iDisplayLength <= 0 ? $iTotalRecords : $iDisplayLength; 
$iDisplayStart = intval($_REQUEST['start']);
$sEcho = intval($_REQUEST['draw']);

$records = array();
$records["data"] = array(); 

$end = $iDisplayStart + $iDisplayLength;
$end = $end > $iTotalRecords ? $iTotalRecords : $end;

$limite = ' LIMIT '.$iDisplayStart.','.$iDisplayLength;
$sql_0001 = "SELECT ".$campi_visualizzati." FROM ".$tabella." WHERE $where $ordine $limite";

if($_SESSION['livello_utente']=='amministratore'){
    //echo $sql_0001;
}

$rs_0001 = $dblink->get_results($sql_0001);
//$numRow = $dblink->num_rows($sql_0001);
$fields = $dblink->list_fields($sql_0001);

$r = 0;

foreach ($rs_0001 as $row) {
    $c = 0;
    foreach ($row as $column) {
        if(strtolower($fields[$c]->name)=="selezione"){
            $records["data"][$r][] = '<label class="mt-checkbox mt-checkbox-outline"><input name="txt_checkbox_' . $r . '" id="txt_checkbox_' . $r . '" type="checkbox"  value="' . $column . '"><span></span></label>'; 
        }else if (strpos(strtolower($fields[$c]->name), "data") !== false) {
            $records["data"][$r][] = '' . GiraDataOra($column) . '';
        }else{
            $records["data"][$r][] = '' . $column . '';
        }
        //$records["data"][$r][] = '' . $column . '';
        /*if (strtolower($fields[$c]->name) == "stato") {
            //echo $column;
            if (strpos($column, "|") !== false) {
                $tmpStato = explode("|", $column);
                $classeColore = array($tmpStato[0]);
                $column = $tmpStato[1];
            } else {
                $classeColore = $dblink->get_row("SELECT colore_sfondo FROM lista_richieste_stati WHERE nome='" . $dblink->filter($column) . "' AND colore_sfondo!=''");
            }
        } else {
            $classeColore = false;
        }
        if ($classeColore != false) {
            $records["data"][$r][] = '<span class="badge bold bg-' . $classeColore[0] . ' bg-font-' . $classeColore[0] . '"> ' . $column . ' </span>';
        } else {
            switch (strtolower($column)) {
                case "disponibile":
                case "richiamare":
                case "chiuso":
                case "attivo":
                case "attiva":
                case "pagata":
                case "lavorazione terminata":
                    $records["data"][$r][] = '<span class="badge bold bg-green-jungle bg-font-green-jungle"> ' . $column . ' </span>';
                    break;

                case "in attesa":
                case "in corso":
                case "in lavorazione":
                case "in attesa di controllo":
                case "nuovo nominativo in attesa di controllo":
                    $records["data"][$r][] = '<span class="badge bold bg-yellow-saffron bg-font-yellow-saffron"> ' . $column . ' </span>';
                    break;

                case "negativo":
                case "non disponibile":
                case "non attivo":
                case "non attiva":
                case "annullata":
                case "mai contattato":
                case "non interessa":
                case "non letto":
                    $records["data"][$r][] = '<span class="badge bold bg-red-thunderbird bg-font-red-thunderbird"> ' . $column . ' </span>';
                    break;

                case "venduto":
                case "terminata":
                    $records["data"][$r][] = '<span class="badge bold bg-blue-steel bg-font-blue-steel"> ' . $column . ' </span>';
                    break;

                case "img":
                    $records["data"][$r][] = '' . $column . '';
                    break;

                default:
                    if(strtolower($fields[$c]->name)=="selezione"){
                        $records["data"][$r][] = '<label class="mt-checkbox mt-checkbox-outline"><input name="txt_checkbox_' . $r . '" id="txt_checkbox_' . $r . '" type="checkbox"  value="' . $column . '"><span></span></label>'; 
                    }else if (strtolower($fields[$c]->name) == "data") {
                        $records["data"][$r][] = '' . GiraDataOra($column) . '';
                    }else{
                        $records["data"][$r][] = '' . $column . '';
                    }
                break;
            }
        }*/
        $c++;
    }
    $r++;
}

if (isset($_REQUEST["customActionType"]) && $_REQUEST["customActionType"] == "group_action") {
    $records["customActionStatus"] = "OK"; // pass custom message(useful for getting status of group actions)
    $records["customActionMessage"] = "Group action successfully has been completed. Well done!"; // pass custom message(useful for getting status of group actions)
}

$records["draw"] = $sEcho;
$records["recordsTotal"] = $iTotalRecords;
$records["recordsFiltered"] = $iTotalRecords;

echo json_encode($records);

?>