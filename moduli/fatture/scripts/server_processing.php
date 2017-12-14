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

$where_data_fattura = "";
$where_data_fattura_attesa_emissione = "";

if(isset($_GET['whr_state']) && $_GET['whr_state']!="" && $_GET['whr_state']!="null"){
    if (!empty($_SESSION['intervallo_data_fattura'])) {
        $intervallo_data = $_SESSION['intervallo_data_fattura'];
        $data_in = before(' al ', $intervallo_data);
        $data_out = after(' al ', $intervallo_data);

        if($data_in == $data_out){
            $where_data_fattura = " AND DATE(data_creazione) = '" . GiraDataOra($data_in) . "'";
            $where_data_fattura_attesa_emissione = " AND DATE(dataagg) = '" . GiraDataOra($data_in) . "'";
        }else{
            $where_data_fattura = " AND data_creazione BETWEEN  '" . GiraDataOra($data_in) . "' AND  '" . GiraDataOra($data_out) . "'";
            $where_data_fattura_attesa_emissione = " AND dataagg BETWEEN  '" . GiraDataOra($data_in) . "' AND  '" . GiraDataOra($data_out) . "'";
        }
    } else {
        $where_data_fattura = " AND YEAR(data_creazione)=YEAR(CURDATE()) AND MONTH(data_creazione)=MONTH(CURDATE())";
        $where_data_fattura_attesa_emissione = " AND YEAR(dataagg)=YEAR(CURDATE()) AND MONTH(dataagg)=MONTH(CURDATE())";
    }
}

switch($tabella){
    case 'lista_fatture_invio_multiplo':
        $tabella = 'lista_fatture';
        $campi_visualizzati = $table_listaFattureInvioMultiplo['index']['campi'];
        if(isset($_GET['whr_state']) && $_GET['whr_state']!="null"){
            $where = " (MD5(stato)=('".$_GET['whr_state']."'))".$where_lista_fatture;
        }else{
            $where = $table_listaFattureInvioMultiplo['index']['where'];
        }
        if(!empty($arrayCampoRicerca)){
            foreach ($arrayCampoRicerca as $campoRicerca) {
                $campoRicerca = $dblink->filter($campoRicerca);
                $where.= " AND (cognome_nome_professionista LIKE '%".$campoRicerca."%' OR `codice_ricerca` LIKE '%".$campoRicerca."%'";
                $where.= " OR `ragione_sociale_azienda` LIKE '%".$campoRicerca."%' OR `nome_campagna` LIKE '%".$campoRicerca."%'";
                $where.= " OR `banca_pagamento` LIKE '%".$campoRicerca."%' OR `data_creazione` LIKE '%".$campoRicerca."%'";
                $where.= " OR `cognome_nome_agente` LIKE '%".$campoRicerca."%' OR `data_scadenza` LIKE '%".$campoRicerca."%'";
                $where.= " OR `imponibile` LIKE '%".$campoRicerca."%' OR `stato` LIKE '%".$campoRicerca."%')";
            }
        }
        $ordine = $table_listaFattureInvioMultiplo['index']['order'];
    break;
    
    case "lista_fatture_recupero_crediti":
        $tabella = 'lista_fatture';
        $campi_visualizzati = $table_listaFattureRecuperoCrediti['index']['campi'];
        
        $where = $table_listaFattureRecuperoCrediti['index']['where'];
        
        $where = $where.$where_data_fattura;
        
        if(!empty($arrayCampoRicerca)){
            foreach ($arrayCampoRicerca as $campoRicerca) {
                $campoRicerca = $dblink->filter($campoRicerca);
                $where.= " AND (cognome_nome_professionista LIKE '%".$campoRicerca."%' OR `codice_ricerca` LIKE '%".$campoRicerca."%'";
                $where.= " OR `ragione_sociale_azienda` LIKE '%".$campoRicerca."%' OR `nome_campagna` LIKE '%".$campoRicerca."%'";
                $where.= " OR `banca_pagamento` LIKE '%".$campoRicerca."%' OR `data_creazione` LIKE '%".$campoRicerca."%'";
                $where.= " OR `cognome_nome_agente` LIKE '%".$campoRicerca."%' OR `data_scadenza` LIKE '%".$campoRicerca."%'";
                $where.= " OR `imponibile` LIKE '%".$campoRicerca."%' OR `stato` LIKE '%".$campoRicerca."%')";
            }
        }
        $ordine = $table_listaFattureRecuperoCrediti['index']['order'];
    break;
    
    case "lista_fatture":
        $campi_visualizzati = $table_listaFatture['index']['campi'];
        
       if(isset($_GET['whr_state']) && !empty($_GET['whr_state']) && $_GET['whr_state']!="null"){
            if($_GET['whr_state']=='d24b24ffc6859354a67488859971308f'){
                $where = " (MD5(stato_invio)=('".$_GET['whr_state']."'))".$where_lista_fatture;
            }else if ($_GET['whr_state']=='8b7bbcc20d4857c20045195274f7d0dc'){
                 $where = " (MD5(stato)=('".$_GET['whr_state']."') OR stato LIKE 'Pagata %')".$where_lista_fatture;
            }else{
                $where = " (MD5(stato)=('".$_GET['whr_state']."'))".$where_lista_fatture;
                if($_GET['whr_state'] == "01f71d0bfed57b81b522dbdf5f21d288"){
                   $where.=" AND tipo LIKE 'Nota di Credito'";
                }
            }
        }else{
            $where = $table_listaFatture['index']['where'];
        }
        
        if($_GET['whr_state']=='0c5d1191eb5033b241de0c655ceac356'){
            $where = $where.$where_data_fattura_attesa_emissione;
        }else{
            $where = $where.$where_data_fattura;
        }
        
        if(!empty($arrayCampoRicerca)){
            foreach ($arrayCampoRicerca as $campoRicerca) {
                $campoRicerca = $dblink->filter($campoRicerca);
                $where.= " AND (cognome_nome_professionista LIKE '%".$campoRicerca."%' OR `codice_ricerca` LIKE '%".$campoRicerca."%'";
                $where.= " OR `ragione_sociale_azienda` LIKE '%".$campoRicerca."%' OR `nome_campagna` LIKE '%".$campoRicerca."%'";
                $where.= " OR `banca_pagamento` LIKE '%".$campoRicerca."%' OR `data_creazione` LIKE '%".$campoRicerca."%'";
                $where.= " OR `cognome_nome_agente` LIKE '%".$campoRicerca."%' OR `data_scadenza` LIKE '%".$campoRicerca."%'";
                $where.= " OR `imponibile` LIKE '%".$campoRicerca."%' OR `stato` LIKE '%".$campoRicerca."%')";
            }
        }
        $ordine = $table_listaFatture['index']['order'];
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
                $campoRicerca = $dblink->filter($campoRicerca);
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

$sql_0001 = "SELECT COUNT(id) as conto FROM ".$tabella." WHERE $where $ordine";

$numRowRes = $dblink->get_row($sql_0001,true);
$numRow = $numRowRes['conto'];
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
        if (strtolower($fields[$c]->name) == "stato") {
            //echo $column;
            if (strpos($column, "|") !== false) {
                $tmpStato = explode("|", $column);
                $classeColore = array($tmpStato[0]);
                $column = $tmpStato[1];
            } else {
                $classeColore = $dblink->get_row("SELECT colore_sfondo FROM lista_fatture_stati WHERE nome='" . $dblink->filter($column) . "' AND colore_sfondo!=''");
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