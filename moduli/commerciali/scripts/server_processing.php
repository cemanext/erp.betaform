<?php
header('Content-Type: text/plain');
include_once('../../../config/connDB.php');
include_once(BASE_ROOT . 'config/confAccesso.php');

if (isset($_GET['tbl'])) {
    $tabella = $_GET['tbl'];
}

if (isset($_GET['fn'])) {
    $funzione = $_GET['fn'];
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
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

switch($funzione){
    case "tabella1":
        $campi_visualizzati = "CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"".BASE_URL."/moduli/anagrafiche/dettaglio_tab.php?tbl=calendario&id=',id,'\" title=\"SCHEDA\" alt=\"SCHEDA\"><i class=\"fa fa-book\"></i></a>') AS 'fa-book',
                        CONCAT((SELECT CONCAT(cognome,' ', nome) FROM lista_password WHERE id = id_agente),' (',id_agente,')') AS 'Commerciale',
                        dataagg AS 'Data',
                        oggetto AS 'Oggetto', 
                        IF(id_professionista>0,(SELECT CONCAT(cognome,' ', nome) FROM lista_professionisti WHERE id = id_professionista),mittente) AS 'Mittente', 
                        campo_5 AS 'E-Mail', campo_4 AS 'Telefono', stato,
                        CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"modifica.php?tbl=calendario&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',
                        CONCAT('<a class=\"btn btn-circle btn-icon-only red btn-outline\" href=\"cancella.php?tbl=calendario&id=',id,'\" title=\"ELIMINA\" alt=\"ELIMINA\"><i class=\"fa fa-trash\"></i></a>') AS 'fa-trash' ,
                        id AS 'selezione'";
        $where = "1 $where_calendario";
        $where.= " AND etichetta LIKE 'Nuova Richiesta'";
        $where.= " AND (stato LIKE 'Mai Contattato' OR stato LIKE 'Richiamare')";
        $where.= " AND id_agente=" . $id."";
        if(!empty($arrayCampoRicerca)){
            foreach ($arrayCampoRicerca as $campoRicerca) {
                $where.= " AND (dataagg LIKE '%".$campoRicerca."%' OR oggetto LIKE '%".$campoRicerca."%'";
                $where.= " OR mittente LIKE '%".$campoRicerca."%' OR campo_5 LIKE '%".$campoRicerca."%'";
                $where.= " OR campo_4 LIKE '%".$campoRicerca."%' OR stato LIKE '%".$campoRicerca."%')";
            }
        }
        $ordine = "ORDER BY dataagg DESC";
    break;
    
    case "tabella2":
        $campi_visualizzati = "CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"".BASE_URL."/moduli/anagrafiche/dettaglio_tab.php?tbl=calendario&id=',id,'\" title=\"SCHEDA\" alt=\"SCHEDA\"><i class=\"fa fa-book\"></i></a>') AS 'fa-book',
                        CONCAT((SELECT CONCAT(cognome,' ', nome) FROM lista_password WHERE id = id_agente),' (',id_agente,')') AS 'Commerciale',
                        dataagg AS 'Data',
                        oggetto AS 'Oggetto', 
                        IF(id_professionista>0,(SELECT CONCAT(cognome,' ', nome) FROM lista_professionisti WHERE id = id_professionista),mittente) AS 'Mittente', 
                        campo_5 AS 'E-Mail', campo_4 AS 'Telefono', stato,
                        CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"modifica.php?tbl=calendario&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',
                        CONCAT('<a class=\"btn btn-circle btn-icon-only red btn-outline\" href=\"cancella.php?tbl=calendario&id=',id,'\" title=\"ELIMINA\" alt=\"ELIMINA\"><i class=\"fa fa-trash\"></i></a>') AS 'fa-trash' ,
                        id AS 'selezione'";
        $where = "1 $where_calendario";
        $where.= " AND etichetta LIKE 'Nuova Richiesta'";
        $where.= " AND (stato NOT LIKE 'Mai Contattato' AND stato NOT LIKE 'Richiamare')";
        $where.= " AND id_agente=" . $id."";
        if(!empty($arrayCampoRicerca)){
            foreach ($arrayCampoRicerca as $campoRicerca) {
                $where.= " AND (dataagg LIKE '%".$campoRicerca."%' OR oggetto LIKE '%".$campoRicerca."%'";
                $where.= " OR mittente LIKE '%".$campoRicerca."%' OR campo_5 LIKE '%".$campoRicerca."%'";
                $where.= " OR campo_4 LIKE '%".$campoRicerca."%' OR stato LIKE '%".$campoRicerca."%')";
            }
        }
        $ordine = "ORDER BY dataagg DESC";
    break;
    
    case "tabella3":
        $campi_visualizzati = "CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"".BASE_URL."/moduli/preventivi/dettaglio.php?tbl=lista_preventivi&id=',id,'\" title=\"DETTAFLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',
                        CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"".BASE_URL."/moduli/preventivi/modifica.php?tbl=lista_preventivi&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',
                        data_creazione AS 'Creato',
                        codice AS 'Cod.',
                        imponibile,
                        stato";
        $where = "1 $where_lista_preventivi";
        $where.= " AND id_agente='" . $id."'";
        if(!empty($arrayCampoRicerca)){
            foreach ($arrayCampoRicerca as $campoRicerca) {
                $where.= " AND (dataagg LIKE '%".$campoRicerca."%' OR data_creazione LIKE '%".$campoRicerca."%'";
                $where.= " OR codice LIKE '%".$campoRicerca."%' OR imponibile LIKE '%".$campoRicerca."%'";
                $where.= " OR codice_ricerca LIKE '%".$campoRicerca."%' OR stato LIKE '%".$campoRicerca."%')";
            }
        }
        $ordine = "ORDER BY dataagg DESC";
    break;
    
    case "tabella4":
        $campi_visualizzati = "CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"".BASE_URL."/moduli/fatture/dettaglio.php?tbl=lista_fatture&id=',id,'\" title=\"DETTAFLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',
                        CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"".BASE_URL."/moduli/fatture/modifica.php?tbl=lista_fatture&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',
                        data_creazione AS 'Creato',
                        codice AS 'Cod.',
                        imponibile,
                        stato";
        $where = "1 $where_lista_fatture";
        $where.= " AND id_agente='" . $id."'";
        if(!empty($arrayCampoRicerca)){
            foreach ($arrayCampoRicerca as $campoRicerca) {
                $where.= " AND (dataagg LIKE '%".$campoRicerca."%' OR data_creazione LIKE '%".$campoRicerca."%'";
                $where.= " OR codice LIKE '%".$campoRicerca."%' OR imponibile LIKE '%".$campoRicerca."%'";
                $where.= " OR codice_ricerca LIKE '%".$campoRicerca."%' OR stato LIKE '%".$campoRicerca."%')";
            }
        }
        $ordine = "ORDER BY dataagg DESC";
    break;

    default:
        switch ($tabella) {
            case "lista_esami_corsi_commerciali":
                $tabella = "calendario";
                $campi_visualizzati = $table_listaEsamiCorsiCommerciali['index']['campi'];
                if($_SESSION['livello_utente'] == 'commerciale'){
                    //$where = " (lista_preventivi.stato LIKE 'Venduto' OR lista_preventivi.stato LIKE 'Chiuso') AND sezionale NOT LIKE 'CN%' AND (lista_preventivi.id_agente='" . $_SESSION['id_utente'] . "' OR md5(lista_preventivi.cognome_nome_agente)='" . MD5($_SESSION['cognome_nome_utente']) . "') ";
                    $where = $table_listaEsamiCorsiCommerciali['index']['where'];
                }else{
                    $where = $table_listaEsamiCorsiCommerciali['index']['where'];
                }
                if(!empty($arrayCampoRicerca)){
                    foreach ($arrayCampoRicerca as $campoRicerca) {
                        if(!empty($campoRicerca)){
                            //  $campoRicerca = $dblink->filter($campoRicerca);
                            $where.= " AND ( oggetto LIKE '%".$campoRicerca."%' OR da LIKE '%".$campoRicerca."%'";
                            //$where.= " OR id_agente IN (SELECT id FROM lista_password WHERE nome LIKE '%".$campoRicerca."%' OR cognome LIKE '%".$campoRicerca."%' )";
                            //$where.= " OR id_professionista IN (SELECT id FROM lista_professionisti WHERE nome LIKE '%".$campoRicerca."%' OR cognome LIKE '%".$campoRicerca."%' )";
                            //$where.= " OR id IN (SELECT id_preventivo FROM lista_fatture WHERE lista_fatture.stato LIKE '%".$campoRicerca."%' OR lista_fatture.data_creazione LIKE '%".$campoRicerca."%' OR lista_fatture.imponibile LIKE '%".$campoRicerca."%' )";
                            //$where.= " OR codice_ricerca LIKE '%".$campoRicerca."%' OR imponibile LIKE '%".$campoRicerca."%')";
                        }
                    }
                }
                $ordine = $table_listaEsamiCorsiCommerciali['index']['order'];
            break;
            
            case "lista_consuntivo_vendite":
                $tabella = "lista_preventivi";
                $campi_visualizzati = $table_listaConsuntivoVendite['index']['campi'];
                if($_SESSION['livello_utente'] == 'commerciale'){
                    $where = " (lista_preventivi.stato LIKE 'Venduto' OR lista_preventivi.stato LIKE 'Chiuso') AND sezionale NOT LIKE 'CN%' AND (lista_preventivi.id_agente='" . $_SESSION['id_utente'] . "' OR md5(lista_preventivi.cognome_nome_agente)='" . MD5($_SESSION['cognome_nome_utente']) . "') ";
                }else{
                    $where = $table_listaConsuntivoVendite['index']['where'];
                }
                if(!empty($arrayCampoRicerca)){
                    foreach ($arrayCampoRicerca as $campoRicerca) {
                        if(!empty($campoRicerca)){
                            //  $campoRicerca = $dblink->filter($campoRicerca);
                            $where.= " AND ( data_iscrizione LIKE '%".$campoRicerca."%' OR codice LIKE '%".$campoRicerca."%'";
                            $where.= " OR id_agente IN (SELECT id FROM lista_password WHERE nome LIKE '%".$campoRicerca."%' OR cognome LIKE '%".$campoRicerca."%' )";
                            $where.= " OR id_professionista IN (SELECT id FROM lista_professionisti WHERE nome LIKE '%".$campoRicerca."%' OR cognome LIKE '%".$campoRicerca."%' )";
                            $where.= " OR id IN (SELECT id_preventivo FROM lista_fatture WHERE lista_fatture.stato LIKE '%".$campoRicerca."%' OR lista_fatture.data_creazione LIKE '%".$campoRicerca."%' OR lista_fatture.imponibile LIKE '%".$campoRicerca."%' )";
                            $where.= " OR codice_ricerca LIKE '%".$campoRicerca."%' OR imponibile LIKE '%".$campoRicerca."%')";
                        }
                    }
                }
                $ordine = $table_listaConsuntivoVendite['index']['order'];
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
        
    break;
}

$sql_0001 = "SELECT count(id) AS conto FROM ".$tabella." WHERE $where $ordine";

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
$sql_0001 = "SELECT ".$campi_visualizzati." FROM ".$tabella." WHERE $where $ordine $limite";

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