<?php
header('Content-Type: text/plain');
include_once('../../../config/connDB.php');
include_once(BASE_ROOT . 'config/confAccesso.php');

if (isset($_GET['tbl'])) {
    $tabella = $_GET['tbl'];
}

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
    case "lista_costi":
        $campi_visualizzati = $table_listaCosti['index']['campi'];
        if(isset($_GET['whr_state']) && $_GET['whr_state']!="null"){
            $where = " (MD5(stato)=('".$_GET['whr_state']."'))".$where_lista_costi;
        }else{
            $where = $table_listaCosti['index']['where'];
        }
        if(!empty($arrayCampoRicerca)){
            foreach ($arrayCampoRicerca as $campoRicerca) {
                $where.= " AND (`cognome_nome_professionista` LIKE '%".$campoRicerca."%' OR `nome_banca` LIKE '%".$campoRicerca."%'";
                $where.= " OR `ragione_sociale_azienda` LIKE '%".$campoRicerca."%' OR `data_creazione` LIKE '%".$campoRicerca."%'";
                $where.= " OR `entrate` LIKE '%".$campoRicerca."%' OR `stato` LIKE '%".$campoRicerca."%')";
            }
        }
        $ordine = $table_listaCosti['index']['order'];
    break;
    
    default:
    break;
}

$sql_0001 = "SELECT count(id) AS conto FROM ".$tabella." WHERE $where ";

$numRowRes = $dblink->get_row($sql_0001,true);
$numRow = $numRowRes['conto'];

if(is_array($_REQUEST['order'][0])){
    $nome_colonna = $fields[$_REQUEST['order'][0]['column']]->orgname;
    if (strlen($nome_colonna) < 1)
        $nome_colonna = $fields[$_REQUEST['order'][0]['column']]->name;
    if(strpos(strtolower($nome_colonna), "fa-")===false){
        $ordine = " ORDER BY '".$nome_colonna."' ".$_REQUEST['order'][0]['dir'];
    }
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

$rs_0001 = $dblink->get_results($sql_0001);
$fields = $dblink->list_fields($sql_0001);

$r = 0;

foreach ($rs_0001 as $row) {
    $c = 0;
    foreach ($row as $column) {
        if (strtolower($fields[$c]->name) == "stato") {
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
        }
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
