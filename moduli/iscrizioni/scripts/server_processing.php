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

if(isset($tabella) && ($tabella=="lista_iscrizioni_partecipanti_completati" || $tabella=="verifica_corsi") && $tabella!="null"){
    if (!empty($_SESSION['intervallo_data_iscrizioni'])) {
        $intervallo_data = $_SESSION['intervallo_data_iscrizioni'];
        $data_in = before(' al ', $intervallo_data);
        $data_out = after(' al ', $intervallo_data);

        if($data_in == $data_out){
            $where_data_iscrizioni = " AND DATE(data_completamento) = '" . GiraDataOra($data_in) . "'";
        }else{
            $where_data_iscrizioni = " AND DATE(data_completamento) BETWEEN  '" . GiraDataOra($data_in) . "' AND  '" . GiraDataOra($data_out) . "'";
        }
    } else {
        $where_data_iscrizioni = " AND YEAR(data_completamento)=YEAR(CURDATE()) AND MONTH(data_completamento)=MONTH(CURDATE())";
    }
}

if(isset($tabella) && $tabella=="lista_iscrizioni_configurazioni" && $tabella!="null"){
    if (!empty($_SESSION['intervallo_data_iscrizioni'])) {
        $intervallo_data = $_SESSION['intervallo_data_iscrizioni'];
        $data_in = before(' al ', $intervallo_data);
        $data_out = after(' al ', $intervallo_data);

        if($data_in == $data_out){
            $where_data_iscrizioni = " AND DATE(data_fine_iscrizione) = '" . GiraDataOra($data_in) . "'";
        }else{
            $where_data_iscrizioni = " AND data_fine_iscrizione BETWEEN  '" . GiraDataOra($data_in) . "' AND  '" . GiraDataOra($data_out) . "'";
        }
    } else {
        $where_data_iscrizioni = " AND YEAR(data_fine_iscrizione)=YEAR(CURDATE()) AND MONTH(data_fine_iscrizione)=MONTH(CURDATE())";
    }
}


switch($tabella){

    case 'table_listaIscrizioniCompletati':
        $tabella = "lista_iscrizioni";
        $campi_visualizzati = $table_listaIscrizioniCompletati['index']['campi'];
        $where = $table_listaIscrizioniCompletati['index']['where'];
        if(!empty($arrayCampoRicerca)){
            foreach ($arrayCampoRicerca as $campoRicerca) {
                $where.= " AND (nome_corso LIKE '%".$campoRicerca."%' OR stato LIKE '%".$campoRicerca."%')";
            }
        }
        $groupby = " GROUP BY id_corso ";
        $ordine = $table_listaIscrizioniCompletati['index']['order'];
    break;
    
    case 'table_listaIscrizioniCompletatiPagati':
        $tabella = "lista_iscrizioni";
        $campi_visualizzati = $table_listaIscrizioniPartecipantiCompletatiPagati['index']['campi'];
        $where = $table_listaIscrizioniPartecipantiCompletatiPagati['index']['where'];
        if(!empty($arrayCampoRicerca)){
            foreach ($arrayCampoRicerca as $campoRicerca) {
                $where.= " AND (nome_corso LIKE '%".$campoRicerca."%' OR nome_classe LIKE '%".$campoRicerca."%'";
                $where.= " OR data_inizio_iscrizione LIKE '%".$campoRicerca."%' OR data_fine_iscrizione LIKE '%".$campoRicerca."%'";
                $where.= " OR stato_completamento LIKE '%".$campoRicerca."%' OR avanzamento_completamento LIKE '%".$campoRicerca."%'";
                $where.= " OR cognome_nome_professionista LIKE '%".$campoRicerca."%')";
            }
        }
        $groupby = "";
        $ordine = $table_listaIscrizioniPartecipantiCompletatiPagati['index']['order'];
    break;
    
    case 'table_listaIscrizioniCompletatiNonPagati':
        $tabella = "lista_iscrizioni";
        $campi_visualizzati = $table_listaIscrizioniPartecipantiCompletatiNonPagati['index']['campi'];
        $where = $table_listaIscrizioniPartecipantiCompletatiNonPagati['index']['where'];
         if(!empty($arrayCampoRicerca)){
            foreach ($arrayCampoRicerca as $campoRicerca) {
                $where.= " AND (nome_corso LIKE '%".$campoRicerca."%' OR nome_classe LIKE '%".$campoRicerca."%'";
                $where.= " OR data_inizio_iscrizione LIKE '%".$campoRicerca."%' OR data_fine_iscrizione LIKE '%".$campoRicerca."%'";
                $where.= " OR stato_completamento LIKE '%".$campoRicerca."%' OR avanzamento_completamento LIKE '%".$campoRicerca."%'";
                $where.= " OR cognome_nome_professionista LIKE '%".$campoRicerca."%')";
            }
        }
        $groupby = "";
        $ordine = $table_listaIscrizioniPartecipantiCompletatiNonPagati['index']['order'];
    break;
        
    case 'table_listaIscrizioniInAttesa':
        $tabella = "lista_iscrizioni";
        $campi_visualizzati = $table_listaIscrizioniInAttesa['index']['campi'];
        $where = $table_listaIscrizioniInAttesa['index']['where'];
        if(!empty($arrayCampoRicerca)){
            foreach ($arrayCampoRicerca as $campoRicerca) {
                $where.= " AND (nome_corso LIKE '%".$campoRicerca."%' OR stato LIKE '%".$campoRicerca."%')";
            }
        }
        $groupby = " GROUP BY id_corso ";
        $ordine = $table_listaIscrizioniInAttesa['index']['order'];
    break;
        
    case 'table_listaIscrizioniInCorso':
        $tabella = "lista_iscrizioni";
        $campi_visualizzati = $table_listaIscrizioniInCorso['index']['campi'];
        $where = $table_listaIscrizioniInCorso['index']['where'];
        if(!empty($arrayCampoRicerca)){
            foreach ($arrayCampoRicerca as $campoRicerca) {
                $where.= " AND (nome_corso LIKE '%".$campoRicerca."%' OR stato LIKE '%".$campoRicerca."%')";
            }
        }
        $groupby = " GROUP BY id_corso ";
        $ordine = $table_listaIscrizioniInCorso['index']['order'];
    break;
        
    case 'lista_iscrizioni_configurazioni':
        $tabella = "lista_iscrizioni";
        $campi_visualizzati = $table_listaIscrizioniConfigurazioni['index']['campi']." , DATEDIFF(CURDATE(), data_fine_iscrizione) as 'Giorni Alla Scadenza'";
        $where = $table_listaIscrizioniConfigurazioni['index']['where'].' '.$where_data_iscrizioni;
        if(!empty($arrayCampoRicerca)){
            foreach ($arrayCampoRicerca as $campoRicerca) {
                $where.= " AND (nome_corso LIKE '%".$campoRicerca."%' OR nome_classe LIKE '%".$campoRicerca."%'";
                $where.= " OR data_inizio_iscrizione LIKE '%".$campoRicerca."%' OR data_fine_iscrizione LIKE '%".$campoRicerca."%'";
                $where.= " OR stato_completamento LIKE '%".$campoRicerca."%' OR avanzamento_completamento LIKE '%".$campoRicerca."%'";
                $where.= " OR cognome_nome_professionista LIKE '%".$campoRicerca."%' OR stato LIKE '%".$campoRicerca."%')";
            }
        }
        $groupby = " ";
        $ordine = $table_listaIscrizioniConfigurazioni['index']['order'];
    break;
    
    case 'lista_iscrizioni':
        $tabella = "lista_iscrizioni";
        $campi_visualizzati = $table_listaIscrizioni['index']['campi'];
        $where = $table_listaIscrizioni['index']['where']. " AND stato NOT LIKE '%Configurazione%' ";
        if(!empty($arrayCampoRicerca)){
            foreach ($arrayCampoRicerca as $campoRicerca) {
                $where.= " AND (nome_corso LIKE '%".$campoRicerca."%' OR stato LIKE '%".$campoRicerca."%')";
            }
        }
        $groupby = " GROUP BY id_corso ";
        $ordine = $table_listaIscrizioni['index']['order'];
    break;

    case 'lista_iscrizioni_partecipanti':
        $tabella = "lista_iscrizioni";
        $campi_visualizzati = $table_listaIscrizioniPartecipanti['index']['campi'];
        $where = $table_listaIscrizioniPartecipanti['index']['where'];
        if(!empty($arrayCampoRicerca)){
            foreach ($arrayCampoRicerca as $campoRicerca) {
                $where.= " AND (nome_corso LIKE '%".$campoRicerca."%' OR nome_classe LIKE '%".$campoRicerca."%'";
                $where.= " OR data_inizio_iscrizione LIKE '%".$campoRicerca."%' OR data_fine_iscrizione LIKE '%".$campoRicerca."%'";
                $where.= " OR stato_completamento LIKE '%".$campoRicerca."%' OR avanzamento_completamento LIKE '%".$campoRicerca."%'";
                $where.= " OR cognome_nome_professionista LIKE '%".$campoRicerca."%' OR stato LIKE '%".$campoRicerca."%')";
            }
        }
        $groupby = "";
        $ordine = $table_listaIscrizioniPartecipanti['index']['order'];
    break;

    case 'lista_iscrizioni_partecipanti_completati':
        $tabella = "lista_iscrizioni";
        $campi_visualizzati = $table_listaIscrizioniPartecipantiCompletati['index']['campi'];
        $where = $table_listaIscrizioniPartecipantiCompletati['index']['where'].$where_data_iscrizioni;
        if(!empty($arrayCampoRicerca)){
            foreach ($arrayCampoRicerca as $campoRicerca) {
                $where.= " AND (nome_corso LIKE '%".$campoRicerca."%' OR nome_classe LIKE '%".$campoRicerca."%'";
                $where.= " OR data_inizio_iscrizione LIKE '%".$campoRicerca."%' OR data_fine_iscrizione LIKE '%".$campoRicerca."%'";
                $where.= " OR stato_completamento LIKE '%".$campoRicerca."%' OR avanzamento_completamento LIKE '%".$campoRicerca."%'";
                $where.= " OR cognome_nome_professionista LIKE '%".$campoRicerca."%')";
            }
        }
        $groupby = "";
        $ordine = $table_listaIscrizioniPartecipantiCompletati['index']['order'];
    break;
    
    case 'verifica_corsi':
        $tabella = "lista_iscrizioni";
        $campi_visualizzati = "CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"".BASE_URL."/moduli/iscrizioni/dettaglio.php?tbl=lista_iscrizioni_partecipanti&id=',id,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',";
        $campi_visualizzati.= $table_listaIscrizioniPartecipantiCompletati['index']['campi'];
        $where = $table_listaIscrizioniPartecipantiCompletati['index']['where']." AND (stato_verifica LIKE 'Da Verificare' OR stato_verifica LIKE 'Non Completato') ".$where_data_iscrizioni;
        if(!empty($arrayCampoRicerca)){
            foreach ($arrayCampoRicerca as $campoRicerca) {
                $where.= " AND (nome_corso LIKE '%".$campoRicerca."%' OR nome_classe LIKE '%".$campoRicerca."%'";
                $where.= " OR data_inizio_iscrizione LIKE '%".$campoRicerca."%' OR data_fine_iscrizione LIKE '%".$campoRicerca."%'";
                $where.= " OR stato_completamento LIKE '%".$campoRicerca."%' OR avanzamento_completamento LIKE '%".$campoRicerca."%'";
                $where.= " OR cognome_nome_professionista LIKE '%".$campoRicerca."%')";
            }
        }
        $groupby = "";
        $ordine = $table_listaIscrizioniPartecipantiCompletati['index']['order'];
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
        $groupby = "";
        $ordine = " ORDER BY id DESC";
    break;
}

$sql_0001 = "SELECT id_corso AS conto FROM ".$tabella." WHERE $where $groupby $ordine";

$numRow = $dblink->num_rows($sql_0001);
//$numRowRes = $dblink->get_row($sql_0001,true);
//$numRow = $numRowRes['conto'];

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
$sql_0001 = "SELECT ".$campi_visualizzati." FROM ".$tabella." WHERE $where $groupby $ordine $limite";
//echo $sql_0001;
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
                case "completato":
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
                case "scaduto e disattivato":
                case "scaduto":
                case "non letto":
                case "abbonamento disabilitato":
                case "corso disabilitato":
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