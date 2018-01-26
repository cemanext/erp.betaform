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

$where_data = "";

if($_GET['whr_state']!="" && $_GET['whr_state']!="null"){
    if (!empty($_SESSION['intervallo_data_preventivi'])) {
        $intervallo_data_preventivi = $_SESSION['intervallo_data_preventivi'];
        $data_in = (before(' al ', $intervallo_data_preventivi));
        $data_out = (after(' al ', $intervallo_data_preventivi));

        if($data_in == $data_out){
            $where_data_preventivi = " AND DATE(dataagg) = '" . GiraDataOra($data_in) . "'";
            $where_data_preventivi_iscritto = " AND DATE(data_iscrizione) = '" . GiraDataOra($data_in) . "'";
            $where_data_preventivi_confermato = " AND DATE(data_firma) = '" . GiraDataOra($data_in) . "'";
        }else{
            $where_data_preventivi = " AND dataagg BETWEEN  '" . GiraDataOra($data_in) . "' AND  '" . GiraDataOra($data_out) . "'";
            $where_data_preventivi_iscritto = " AND data_iscrizione BETWEEN  '" . GiraDataOra($data_in) . "' AND  '" . GiraDataOra($data_out) . "'";
            $where_data_preventivi_confermato = " AND data_firma BETWEEN  '" . GiraDataOra($data_in) . "' AND  '" . GiraDataOra($data_out) . "'";
        }
    } else {
        $where_data_preventivi = " AND YEAR(dataagg)=YEAR(CURDATE()) AND MONTH(dataagg)=MONTH(CURDATE())";
        $where_data_preventivi_iscritto = " AND YEAR(data_iscrizione)=YEAR(CURDATE()) AND MONTH(data_iscrizione)=MONTH(CURDATE())";
        $where_data_preventivi_confermato = " AND YEAR(data_firma)=YEAR(CURDATE()) AND MONTH(data_firma)=MONTH(CURDATE())";
    }
    
    if($_GET['whr_state']=="8b7bbcc20d4857c20045195274f7d0dc"){ //In Attesa
        $where_data = $where_data_preventivi;
    }else if($_GET['whr_state']=="0dcf93d17feb1a4f6efe62d5d2f270b2"){ //Venduto
        $where_data = $where_data_preventivi_iscritto;
    }else if($_GET['whr_state']=="31aa0b940088855f8a9b72946dc495ab"){ //Negativi
        $where_data = $where_data_preventivi_iscritto;
    }else if($_GET['whr_state']=="13d261a138ce95e3c007931d9653e951"){ //Chiuso
        $where_data = $where_data_preventivi_confermato;
    }else{
        $where_data = "";
    }
    
}

switch($tabella){
    case "lista_preventivi":
        $campi_visualizzati = $table_listaPreventivi['index']['campi'];
        
        if(isset($_GET['whr_state']) && $_GET['whr_state']!="null"){
            
            if($_GET['whr_state']=="8b7bbcc20d4857c20045195274f7d0dc"){ //In Attesa
                $campi_visualizzati = str_replace("data_iscrizione,", "dataagg AS data_ultima_modifica,", $campi_visualizzati);
            }else if($_GET['whr_state']=="13d261a138ce95e3c007931d9653e951"){
                $campi_visualizzati = str_replace("data_iscrizione,", "data_firma AS data_firma,", $campi_visualizzati);
                $campi_visualizzati = str_replace("DATE_FORMAT(DATE(data_creazione), '%d-%m-%Y') AS `Creato_il`,", "DATE_FORMAT(DATE(data_iscrizione), '%d-%m-%Y') AS `Iscritto_il`,", $campi_visualizzati);
            }
            
            $where = " (MD5(stato)=('".$_GET['whr_state']."'))".$where_lista_preventivi;
        }else{
            $where = $table_listaPreventivi['index']['where'];
        }
        if(!empty($arrayCampoRicerca)){
            foreach ($arrayCampoRicerca as $campoRicerca) {
                $where.= " AND (cognome_nome_professionista LIKE '%".$campoRicerca."%' OR `codice_ricerca` LIKE '%".$campoRicerca."%'";
                $where.= " OR `ragione_sociale_azienda` LIKE '%".$campoRicerca."%' OR `nome_campagna` LIKE '%".$campoRicerca."%'";
                $where.= " OR `data_creazione` LIKE '%".$campoRicerca."%' OR `data_iscrizione` LIKE '%".$campoRicerca."%'";
                $where.= " OR `cognome_nome_agente` LIKE '%".$campoRicerca."%' OR `data_scadenza` LIKE '%".$campoRicerca."%'";
                $where.= " OR `imponibile` LIKE '%".$campoRicerca."%' OR `stato` LIKE '%".$campoRicerca."%')";
            }
        }
        $ordine = $table_listaPreventivi['index']['order'];
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

$sql_0001 = "SELECT count(id) AS conto FROM ".$tabella." WHERE $where $where_data";

$numRowRes = $dblink->get_row($sql_0001,true);
$numRow = $numRowRes['conto'];

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
$sql_0001 = "SELECT ".$campi_visualizzati." FROM ".$tabella." WHERE $where $where_data $ordine $limite";

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
                    if(strlen($fields[$c]->orgname)>0) $nomeCampo = strtolower($fields[$c]->orgname);
                    else $nomeCampo = strtolower($fields[$c]->name);
                    if(strtolower($fields[$c]->name)=="selezione"){
                        $records["data"][$r][] = '<label class="mt-checkbox mt-checkbox-outline"><input name="txt_checkbox_' . $r . '" id="txt_checkbox_' . $r . '" type="checkbox"  value="' . $column . '"><span></span></label>'; 
                    }else if (strtolower($nomeCampo) == "data" || strtolower($nomeCampo) == "data_iscrizione" || strtolower($nomeCampo) == "data_creazione" || strtolower($nomeCampo) == "creato_il") {
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