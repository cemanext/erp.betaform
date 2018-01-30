<?php
header('Content-Type: text/plain');
include_once('../../../config/connDB.php');
include_once(BASE_ROOT.'config/confAccesso.php');

if (isset($_GET['tbl'])) {
    $tabella = $_GET['tbl'];
}

if(isset($_GET['escludiDisattive']) && $_GET['escludiDisattive'] == "0"){
    $whereCampagneDisattive = "";
}else{
    $whereCampagneDisattive = " AND stato NOT LIKE '%Terminata%' AND stato NOT LIKE '%Non Attiva%'";
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
    case "lista_campagne":
        $campi_visualizzati = $table_listaCampagne['index']['campi'];
        if(isset($_GET['whr_state'])){
            $where = " (MD5(stato)=('".$_GET['whr_state']."'))".$where_lista_campagne;
        }else{
            $where = $table_listaCampagne['index']['where'];
        }
        
        $where = $where . $whereCampagneDisattive;

        if(!empty($arrayCampoRicerca)){
            foreach ($arrayCampoRicerca as $campoRicerca) {
                $where.= " AND (nome LIKE '%".$campoRicerca."%' OR descrizione LIKE '%".$campoRicerca."%'";
                $where.= " OR data_inizio LIKE '%".$campoRicerca."%' OR data_fine LIKE '%".$campoRicerca."%'";
                $where.= " OR id_tipo_marketing IN (SELECT id FROM lista_tipo_marketing WHERE nome LIKE '%".$campoRicerca."%') ";
                $where.= " OR stato LIKE '%".$campoRicerca."%' OR numerico_5 LIKE '%".$campoRicerca."%' OR id LIKE '$campoRicerca' )";
            }
        }
        $ordine = $table_listaCampagne['index']['order'];
    break;

    default:
        $campi_visualizzati = "";
        $campi     = 	$dblink->list_fields("SELECT * FROM ".$tabella."");
        foreach ($campi as $nome_colonna) {
             $campi_visualizzati.= "`".$nome_colonna->name."`, ";
        }


        $campi_visualizzati = substr($campi_visualizzati, 0, -2);
        $campi_visualizzati = "
        CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"dettaglio.php?tbl=".$tabella."&id=',id,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',
        CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"modifica.php?tbl=".$tabella."&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',
        CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"duplica.php?tbl=".$tabella."&id=',id,'\" title=\"DUPLICA\" alt=\"DUPLICA\"><i class=\"fa fa-copy\"></i></a>') AS 'fa-copy',
        ".$campi_visualizzati.",
        CONCAT('<a class=\"btn btn-circle btn-icon-only red btn-outline\" href=\"cancella.php?tbl=".$tabella."&id=',id,'\" title=\"ELIMINA\" alt=\"ELIMINA\"><i class=\"fa fa-trash\"></i></a>') AS 'fa-trash'
        ";
         $where = " 1 ";
        if(!empty($arrayCampoRicerca)){
            foreach ($arrayCampoRicerca as $campoRicerca) {
                $where.= " AND (";
                foreach ($campi as $nome_colonna) {
                    $where.= $nome_colonna->name." LIKE '%".$campoRicerca."%' OR ";
                }
                $where = substr($where, 0, -4);
                $where.= ")";
            }
        }
        $ordine = " ORDER BY id DESC";
    break;
}

$sql_0001 = "SELECT COUNT(id) AS conto FROM ".$tabella." WHERE $where $ordine";

$numRow = $dblink->get_field($sql_0001);

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

$rs_0001 = $dblink->get_results($sql_0001);
$numRow = $dblink->num_rows($sql_0001);
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
                    }else if (strtolower($fields[$c]->name) == "%") {
                        $records["data"][$r][] = '<h3>'.$column.' %</h3>';
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