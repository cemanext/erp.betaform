<?php
header('Content-Type: text/plain');
include_once('../../../config/connDB.php');
include_once(BASE_ROOT . 'config/confAccesso.php');

if (isset($_GET['whrStato'])) {
    $whrStato = $_GET['whrStato'];
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

$where_data_calendario = "";

if($_GET['whrStato']!="0e902aba617fb11d469e1b90f57fd79a" && $_GET['whrStato']!="" && $_GET['whrStato']!="null"){
    
    if(!empty($_SESSION['id_campagna_get'])){
        $whereCampagnaId = " AND id_campagna='".$_SESSION['id_campagna_get']."'";
    }
    
    if (!empty($_SESSION['intervallo_data'])) {
        $intervallo_data = $_SESSION['intervallo_data'];
        $data_in = GiraDataOra(before(' al ', $intervallo_data));
        $data_out = GiraDataOra(after(' al ', $intervallo_data));

        if($data_in == $data_out){
            $where_data_calendario = " AND DATE(data) = '" . $data_in . "'";
            $where_data_calendario_inserimento = " AND DATE(datainsert) = '" . $data_in . "'";
            $where_data_calendario_iscritto = " AND DATE(data_iscrizione) = '" . $data_in . "'";
            $where_data_calendario_fattura = " AND DATE(data_creazione) = '" . $data_in . "'";
        }else{
            $where_data_calendario = " AND data BETWEEN  '" . $data_in . "' AND  '" . $data_out . "'";
            $where_data_calendario_inserimento = " AND datainsert BETWEEN  '" . $data_in . "' AND  '" . $data_out . "'";
            $where_data_calendario_iscritto = " AND data_iscrizione BETWEEN  '" . $data_in . "' AND  '" . $data_out . "'";
            $where_data_calendario_fattura = " AND data_creazione BETWEEN  '" . $data_in . "' AND  '" . $data_out . "'";
        }
    } else {
        $where_data_calendario = " AND YEAR(data)=YEAR(CURDATE()) AND MONTH(data)=MONTH(CURDATE())";
        $where_data_calendario_inserimento = " AND YEAR(datainsert)=YEAR(CURDATE()) AND MONTH(datainsert)=MONTH(CURDATE())";
        $where_data_calendario_iscritto = " AND YEAR(data_iscrizione)=YEAR(CURDATE()) AND MONTH(data_iscrizione)=MONTH(CURDATE())";
        $where_data_calendario_fattura = " AND YEAR(data_creazione)=YEAR(CURDATE()) AND MONTH(data_creazione)=MONTH(CURDATE())";
    }
}

$tabella = "calendario";

switch($whrStato){
    case MD5('In Attesa di Controllo'):
        //oggetto AS 'Oggetto', mittente AS 'Mittente', dataagg AS 'Data', campo_5 AS 'E-Mail', campo_4 AS 'Telefono', stato,
        $campi_visualizzati = "id AS 'selezione', CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"".BASE_URL."/moduli/anagrafiche/dettaglio_tab.php?tbl=calendario&id=',id,'\" title=\"SCHEDA\" alt=\"SCHEDA\"><i class=\"fa fa-book\"></i></a>') AS 'fa-book',
                            CONCAT('<a class=\"btn btn-circle btn-icon-only purple-seance btn-outline\" onclick=\"associaCommercialeARichesta(',id,',',id_professionista,');\" title=\"ASSOCIA COMMERCIALE\" alt=\"ASSOCIA COMMERCIALE\"><i class=\"fa fa-sign-in\"></i></a>') AS 'Ass. Comm.',
                            IF(id_azienda>0,CONCAT('<i class=\"fa fa-user btn btn-icon-only green-jungle btn-outline\" style=\"display: inline; padding: 3px; line-height: 0.5;\"></i>'),CONCAT('<i class=\"fa fa-user-times btn btn-icon-only red-flamingo btn-outline\" style=\"display: inline; padding: 3px; line-height: 0.5;\"></i>')) AS 'fa-user',
                            CONCAT('<div style=\"text-align:right;\"><small>Mittente: <b>',mittente,'</b> | Prodotto: <b>',IF(id_prodotto>0,(SELECT lista_prodotti.nome FROM lista_prodotti WHERE lista_prodotti.id=calendario.id_prodotto),'<span style=\"color: red;\">Non Specificato</span>'),'</b></small></div>
                            <div style=\"text-align:left; padding:10px;\">',REPLACE(IF(LENGTH(messaggio)>0, messaggio, ''), '\n', '<br />'),'</div>
                            <div style=\"text-align:right;\"><small>',DATE_FORMAT(DATE(datainsert), '%d-%m-%Y'),' | ',stato,'</small></div>') as 'Messaggio',
                            (SELECT nome FROM lista_tipo_marketing WHERE id = id_tipo_marketing) AS Marketing,
                            datainsert AS 'Data_Inserimento', orainsert AS 'Ora Inserimento', 
                            CONCAT('<a class=\"btn btn-circle btn-icon-only red btn-outline\" href=\"cancella.php?tbl=calendario&id=',id,'\" title=\"ELIMINA\" alt=\"ELIMINA\"><i class=\"fa fa-trash\"></i></a>') AS 'fa-trash'";
        
        /*
        $campi_visualizzati = "datainsert AS 'Data', orainsert AS 'Ora', oggetto AS 'Origine / Tipo marketing', mittente AS 'Nominativo', campo_4 AS 'Tel.', campo_5 AS 'Email', 
        CONCAT('<span class=\"bg-green-jungle  bg-font-green-jungle font-lg sbold\">Si o No</div>') AS 'Cliente',
        CONCAT('<span class=\"label label-sm label-warning\">',stato,'</div>') AS 'Stato'";
        */
        //$where = $table_calendario['index']['where'];
        $where = " MD5(stato)='".$_GET['whrStato']."' $where_calendario $where_data_calendario $whereCampagnaId";
        if(!empty($arrayCampoRicerca)){
            foreach ($arrayCampoRicerca as $campoRicerca) {
                if($campoRicerca=="iscritto") $campoRicerca = "venduto";
                $campoRicerca = $dblink->filter($campoRicerca);
                $where.= " AND (oggetto LIKE '%".$campoRicerca."%' OR mittente LIKE '%".$campoRicerca."%'";
                $where.= " OR dataagg LIKE '%".$campoRicerca."%' OR campo_5 LIKE '%".$campoRicerca."%'";
                $where.= " OR nome LIKE '%".$campoRicerca."%' OR cognome LIKE '%".$campoRicerca."%'";
                $where.= " OR email LIKE '%".$campoRicerca."%' OR campo_9 LIKE '%".$campoRicerca."%'";
                $where.= " OR messaggio LIKE '%".$campoRicerca."%'";
                $where.= " OR tipo_marketing LIKE '%".$campoRicerca."%' OR telefono LIKE '%".$campoRicerca."%'";
                $where.= " OR cellulare LIKE '%".$campoRicerca."%' OR professione LIKE '%".$campoRicerca."%'";
                $where.= " OR campo_4 LIKE '%".$campoRicerca."%' OR stato LIKE '%".$campoRicerca."%')";
            }
        }
        //$ordine = $table_calendario['index']['order'];
        $ordine = " ORDER BY datainsert DESC, orainsert ASC";
    break;
    
    case MD5('Chiusa In Attesa di Controllo'):
        //oggetto AS 'Oggetto', mittente AS 'Mittente', dataagg AS 'Data', campo_5 AS 'E-Mail', campo_4 AS 'Telefono', stato,
        $campi_visualizzati = "id AS 'selezione', CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"".BASE_URL."/moduli/anagrafiche/dettaglio_tab.php?tbl=calendario&id=',id,'\" title=\"SCHEDA\" alt=\"SCHEDA\"><i class=\"fa fa-book\"></i></a>') AS 'fa-book',
                            (SELECT CONCAT(lista_password.nome,' ',lista_password.cognome) FROM lista_password WHERE lista_password.id=calendario.id_agente LIMIT 1) AS 'Commerciale',
                            IF(id_azienda>0,CONCAT('<i class=\"fa fa-user btn btn-icon-only green-jungle btn-outline\" style=\"display: inline; padding: 3px; line-height: 0.5;\"></i>'),CONCAT('<i class=\"fa fa-user-times btn btn-icon-only red-flamingo btn-outline\" style=\"display: inline; padding: 3px; line-height: 0.5;\"></i>')) AS 'fa-user',
                            CONCAT('<div style=\"text-align:right;\"><small>Mittente: <b>',mittente,'</b> | Prodotto: <b>',IF(id_prodotto>0,(SELECT lista_prodotti.nome FROM lista_prodotti WHERE lista_prodotti.id=calendario.id_prodotto),'<span style=\"color: red;\">Non Specificato</span>'),'</b></small></div>
                            <div style=\"text-align:left; padding:10px;\">',REPLACE(IF(LENGTH(messaggio)>0, messaggio, ''), '\n', '<br />'),'</div>
                            <div style=\"text-align:right;\"><small>',DATE_FORMAT(DATE(dataagg), '%d-%m-%Y'),' | ',stato,'</small></div>') as 'Messaggio',
                            CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-blue\" href=\"".BASE_URL."/moduli/calendario/salva.php?idPrev=',(SELECT lista_preventivi.id FROM lista_preventivi WHERE lista_preventivi.id_calendario = calendario.id LIMIT 1),'&id=',id,'&fn=chiudiRichiestaConferma\" title=\"CHIUDI\" alt=\"CHIUDI\"><i class=\"fa fa-check\"></i></a>') AS 'fa-check',
                            datainsert AS 'Data_Inserimento', orainsert AS 'Ora Inserimento'";
        /*
        $campi_visualizzati = "datainsert AS 'Data', orainsert AS 'Ora', oggetto AS 'Origine / Tipo marketing', mittente AS 'Nominativo', campo_4 AS 'Tel.', campo_5 AS 'Email', 
        CONCAT('<span class=\"bg-green-jungle  bg-font-green-jungle font-lg sbold\">Si o No</div>') AS 'Cliente',
        CONCAT('<span class=\"label label-sm label-warning\">',stato,'</div>') AS 'Stato'";
        */
        //$where = $table_calendario['index']['where'];
        $where = " MD5(stato)='".$_GET['whrStato']."' $where_calendario $where_data_calendario";
        if(!empty($arrayCampoRicerca)){
            foreach ($arrayCampoRicerca as $campoRicerca) {
                if($campoRicerca=="iscritto") $campoRicerca = "venduto";
                $campoRicerca = $dblink->filter($campoRicerca);
                $where.= " AND (oggetto LIKE '%".$campoRicerca."%' OR mittente LIKE '%".$campoRicerca."%'";
                $where.= " OR dataagg LIKE '%".$campoRicerca."%' OR campo_5 LIKE '%".$campoRicerca."%'";
                $where.= " OR nome LIKE '%".$campoRicerca."%' OR cognome LIKE '%".$campoRicerca."%'";
                $where.= " OR email LIKE '%".$campoRicerca."%' OR campo_9 LIKE '%".$campoRicerca."%'";
                $where.= " OR messaggio LIKE '%".$campoRicerca."%'";
                $where.= " OR tipo_marketing LIKE '%".$campoRicerca."%' OR telefono LIKE '%".$campoRicerca."%'";
                $where.= " OR cellulare LIKE '%".$campoRicerca."%' OR professione LIKE '%".$campoRicerca."%'";
                $where.= " OR campo_4 LIKE '%".$campoRicerca."%' OR stato LIKE '%".$campoRicerca."%')";
            }
        }
        //$ordine = $table_calendario['index']['order'];
        $ordine = " ORDER BY datainsert DESC, orainsert ASC";
    break;

    case MD5('Mai Contattato'):
    case MD5('Richiamare'):
        if($_SESSION['livello_utente']=="commerciale"){
            $campi_visualizzati = "
                            CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"".BASE_URL."/moduli/anagrafiche/dettaglio_tab.php?tbl=calendario&id=',calendario.id,'\" title=\"SCHEDA\" alt=\"SCHEDA\"><i class=\"fa fa-book\"></i></a>') AS 'fa-book',
                            IF(id_agente='".$_SESSION['id_utente']."',
                            '',
                            CONCAT('<a class=\"btn btn-circle btn-icon-only purple-seance btn-outline\" onclick=\"associaCommercialeARichesta(',id,',',id_professionista,');\" title=\"PRENDI IN CARICO\" alt=\"PRENDI IN CARICO\"><i class=\"fa fa-sign-in\"></i></a>')) AS 'Ass. Comm.',
                            (SELECT CONCAT(lista_password.nome,' ',lista_password.cognome) FROM lista_password WHERE lista_password.id=calendario.id_agente) AS 'Commerciale', 
                            stato, IF(id_azienda>0,CONCAT('<i class=\"fa fa-user btn btn-icon-only green-jungle btn-outline\" style=\"display: inline; padding: 3px; line-height: 0.5;\"></i>'),CONCAT('<i class=\"fa fa-user-times btn btn-icon-only red-flamingo btn-outline\" style=\"display: inline; padding: 3px; line-height: 0.5;\"></i>')) AS 'fa-user',
                            mittente AS 'Mittente',
                            IF(id_professionista>0,(SELECT CONCAT(lista_professionisti.cognome,' ',lista_professionisti.nome) FROM lista_professionisti WHERE lista_professionisti.id=calendario.id_professionista ),'') AS Professionista,
                            (SELECT lista_prodotti.nome FROM lista_prodotti WHERE lista_prodotti.id=calendario.id_prodotto) AS 'Corso', data AS 'Data Richiamo', ora AS 'Ora Richiamo', 
                            (SELECT nome FROM lista_tipo_marketing WHERE id = id_tipo_marketing) AS Marketing";
        }else{
            //oggetto AS 'Oggetto', mittente AS 'Mittente', dataagg AS 'Data', campo_5 AS 'E-Mail', campo_4 AS 'Telefono', stato,
            $campi_visualizzati = "id AS 'selezione', CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"".BASE_URL."/moduli/anagrafiche/dettaglio_tab.php?tbl=calendario&id=',id,'\" title=\"SCHEDA\" alt=\"SCHEDA\"><i class=\"fa fa-book\"></i></a>') AS 'fa-book',
                            (SELECT CONCAT(lista_password.nome,' ',lista_password.cognome) FROM lista_password WHERE lista_password.id=calendario.id_agente) AS 'Commerciale', 
                            stato, IF(id_azienda>0,CONCAT('<i class=\"fa fa-user btn btn-icon-only green-jungle btn-outline\" style=\"display: inline; padding: 3px; line-height: 0.5;\"></i>'),CONCAT('<i class=\"fa fa-user-times btn btn-icon-only red-flamingo btn-outline\" style=\"display: inline; padding: 3px; line-height: 0.5;\"></i>')) AS 'fa-user',
                            mittente AS 'Mittente',
                            IF(id_professionista>0,(SELECT CONCAT(lista_professionisti.cognome,' ',lista_professionisti.nome) FROM lista_professionisti WHERE lista_professionisti.id=calendario.id_professionista ),'') AS Professionista,
                            (SELECT lista_prodotti.nome FROM lista_prodotti WHERE lista_prodotti.id=calendario.id_prodotto) AS 'Corso', data AS 'Data Richiamo', ora AS 'Ora Richiamo', 
                            (SELECT nome FROM lista_tipo_marketing WHERE id = id_tipo_marketing) AS Marketing";
        }
        //$where = $table_calendario['index']['where'];
        $where = " MD5(stato)='".$_GET['whrStato']."' $where_calendario $where_data_calendario";
        if(!empty($arrayCampoRicerca)){
            foreach ($arrayCampoRicerca as $campoRicerca) {
                if($campoRicerca=="iscritto") $campoRicerca = "venduto";
                $campoRicerca = $dblink->filter($campoRicerca);
                $where.= " AND (oggetto LIKE '%".$campoRicerca."%' OR mittente LIKE '%".$campoRicerca."%'";
                $where.= " OR dataagg LIKE '%".$campoRicerca."%' OR campo_5 LIKE '%".$campoRicerca."%'";
                $where.= " OR nome LIKE '%".$campoRicerca."%' OR cognome LIKE '%".$campoRicerca."%'";
                $where.= " OR email LIKE '%".$campoRicerca."%' OR campo_9 LIKE '%".$campoRicerca."%'";
                $where.= " OR messaggio LIKE '%".$campoRicerca."%'";
                $where.= " OR tipo_marketing LIKE '%".$campoRicerca."%' OR telefono LIKE '%".$campoRicerca."%'";
                $where.= " OR cellulare LIKE '%".$campoRicerca."%' OR professione LIKE '%".$campoRicerca."%'";
                $where.= " OR campo_4 LIKE '%".$campoRicerca."%' OR stato LIKE '%".$campoRicerca."%')";
            }
        }
        //$ordine = $table_calendario['index']['order'];
        $ordine = " ORDER BY datainsert DESC, orainsert ASC";
    break;
    
    case MD5('Venduto'):
        $tabella = "calendario INNER JOIN lista_preventivi ON calendario.id = lista_preventivi.id_calendario";
        if($_SESSION['livello_utente']=="commerciale"){
            $campi_visualizzati = "
                            CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"".BASE_URL."/moduli/anagrafiche/dettaglio_tab.php?tbl=calendario&id=',calendario.id,'\" title=\"SCHEDA\" alt=\"SCHEDA\"><i class=\"fa fa-book\"></i></a>') AS 'fa-book',
                            IF(calendario.id_agente='".$_SESSION['id_utente']."',
                            '',
                            CONCAT('<a class=\"btn btn-circle btn-icon-only purple-seance btn-outline\" onclick=\"associaCommercialeARichesta(',calendario.id,',',calendario.id_professionista,');\" title=\"PRENDI IN CARICO\" alt=\"PRENDI IN CARICO\"><i class=\"fa fa-sign-in\"></i></a>')) AS 'Ass. Comm.',
                            (SELECT CONCAT(lista_password.nome,' ',lista_password.cognome) FROM lista_password WHERE lista_password.id=calendario.id_agente) AS 'Commerciale', 
                            calendario.stato, IF(calendario.id_azienda>0,CONCAT('<i class=\"fa fa-user btn btn-icon-only green-jungle btn-outline\" style=\"display: inline; padding: 3px; line-height: 0.5;\"></i>'),CONCAT('<i class=\"fa fa-user-times btn btn-icon-only red-flamingo btn-outline\" style=\"display: inline; padding: 3px; line-height: 0.5;\"></i>')) AS 'fa-user', 
                            calendario.mittente AS 'Mittente',
                            IF(calendario.id_professionista>0,(SELECT CONCAT(lista_professionisti.cognome,' ',lista_professionisti.nome) FROM lista_professionisti WHERE lista_professionisti.id=calendario.id_professionista ),'') AS Professionista,
                            (SELECT lista_prodotti.nome FROM lista_prodotti WHERE lista_prodotti.id=calendario.id_prodotto) AS 'Corso', lista_preventivi.data_iscrizione AS 'Data Iscrizione', lista_preventivi.imponibile AS Importo, 
                            (SELECT nome FROM lista_tipo_marketing WHERE id = id_tipo_marketing) AS Marketing,
                            (SELECT nome FROM lista_campagne WHERE id = calendario.id_campagna) AS Campagna";
        }else{
            //oggetto AS 'Oggetto', mittente AS 'Mittente', dataagg AS 'Data', campo_5 AS 'E-Mail', campo_4 AS 'Telefono', stato,
            $campi_visualizzati = "CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"".BASE_URL."/moduli/anagrafiche/dettaglio_tab.php?tbl=calendario&id=',calendario.id,'\" title=\"SCHEDA\" alt=\"SCHEDA\"><i class=\"fa fa-book\"></i></a>') AS 'fa-book',
                            (SELECT CONCAT(lista_password.nome,' ',lista_password.cognome) FROM lista_password WHERE lista_password.id=calendario.id_agente) AS 'Commerciale', 
                            calendario.stato, IF(calendario.id_azienda>0,CONCAT('<i class=\"fa fa-user btn btn-icon-only green-jungle btn-outline\" style=\"display: inline; padding: 3px; line-height: 0.5;\"></i>'),CONCAT('<i class=\"fa fa-user-times btn btn-icon-only red-flamingo btn-outline\" style=\"display: inline; padding: 3px; line-height: 0.5;\"></i>')) AS 'fa-user', 
                            calendario.mittente,
                            IF(calendario.id_professionista>0,(SELECT CONCAT(lista_professionisti.cognome,' ',lista_professionisti.nome) FROM lista_professionisti WHERE lista_professionisti.id=calendario.id_professionista ),'') AS Professionista,
                            (SELECT lista_prodotti.nome FROM lista_prodotti WHERE lista_prodotti.id=calendario.id_prodotto) AS 'Corso', lista_preventivi.data_iscrizione AS 'Data Iscrizione', lista_preventivi.imponibile AS Importo, 
                            (SELECT nome FROM lista_tipo_marketing WHERE id = id_tipo_marketing) AS Marketing,
                            (SELECT nome FROM lista_campagne WHERE id = calendario.id_campagna) AS Campagna";
        }
        //$where = $table_calendario['index']['where'];
        $where = " (lista_preventivi.stato='Venduto' OR lista_preventivi.stato='Chiuso') AND MD5(calendario.stato)='".$_GET['whrStato']."' $where_calendario $where_data_calendario_iscritto";
        if(!empty($arrayCampoRicerca)){
            foreach ($arrayCampoRicerca as $campoRicerca) {
                if($campoRicerca=="iscritto") $campoRicerca = "venduto";
                $campoRicerca = $dblink->filter($campoRicerca);
                $where.= " AND (calendario.oggetto LIKE '%".$campoRicerca."%' OR calendario.mittente LIKE '%".$campoRicerca."%'";
                $where.= " OR lista_preventivi.data_iscrizione LIKE '%".$campoRicerca."%' OR calendario.campo_5 LIKE '%".$campoRicerca."%'";
                $where.= " OR calendario.nome LIKE '%".$campoRicerca."%' OR calendario.cognome LIKE '%".$campoRicerca."%'";
                $where.= " OR calendario.email LIKE '%".$campoRicerca."%' OR calendario.campo_9 LIKE '%".$campoRicerca."%'";
                $where.= " OR calendario.messaggio LIKE '%".$campoRicerca."%' OR lista_preventivi.cognome_nome_agente LIKE '%".$campoRicerca."%'";
                $where.= " OR calendario.tipo_marketing LIKE '%".$campoRicerca."%' OR calendario.telefono LIKE '%".$campoRicerca."%'";
                $where.= " OR calendario.cellulare LIKE '%".$campoRicerca."%' OR calendario.professione LIKE '%".$campoRicerca."%'";
                $where.= " OR calendario.campo_4 LIKE '%".$campoRicerca."%' OR calendario.stato LIKE '%".$campoRicerca."%')";
            }
        }
        //$ordine = $table_calendario['index']['order'];
        $ordine = " ORDER BY datainsert DESC, orainsert ASC";
    break;
    case MD5('Obiezione'):
    case MD5('Negativo'):
        $tabella = "calendario INNER JOIN lista_preventivi ON calendario.id = lista_preventivi.id_calendario";
        if($_SESSION['livello_utente']=="commerciale"){
            $campi_visualizzati = "
                            CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"".BASE_URL."/moduli/anagrafiche/dettaglio_tab.php?tbl=calendario&id=',calendario.id,'\" title=\"SCHEDA\" alt=\"SCHEDA\"><i class=\"fa fa-book\"></i></a>') AS 'fa-book',
                            IF(calendario.id_agente='".$_SESSION['id_utente']."',
                            '',
                            CONCAT('<a class=\"btn btn-circle btn-icon-only purple-seance btn-outline\" onclick=\"associaCommercialeARichesta(',calendario.id,',',calendario.id_professionista,');\" title=\"PRENDI IN CARICO\" alt=\"PRENDI IN CARICO\"><i class=\"fa fa-sign-in\"></i></a>')) AS 'Ass. Comm.',
                            (SELECT CONCAT(lista_password.nome,' ',lista_password.cognome) FROM lista_password WHERE lista_password.id=calendario.id_agente) AS 'Commerciale', 
                            calendario.nome_obiezione, 
                            IF(calendario.id_azienda>0,CONCAT('<i class=\"fa fa-user btn btn-icon-only green-jungle btn-outline\" style=\"display: inline; padding: 3px; line-height: 0.5;\"></i>'),CONCAT('<i class=\"fa fa-user-times btn btn-icon-only red-flamingo btn-outline\" style=\"display: inline; padding: 3px; line-height: 0.5;\"></i>')) AS 'fa-user', 
                            calendario.mittente AS 'Mittente',
                            IF(calendario.id_professionista>0,(SELECT CONCAT(lista_professionisti.cognome,' ',lista_professionisti.nome) FROM lista_professionisti WHERE lista_professionisti.id=calendario.id_professionista ),'') AS Professionista,
                            (SELECT lista_prodotti.nome FROM lista_prodotti WHERE lista_prodotti.id=calendario.id_prodotto) AS 'Corso', lista_preventivi.data_iscrizione AS 'Data Negativo', lista_preventivi.imponibile AS Importo, 
                            (SELECT nome FROM lista_tipo_marketing WHERE id = id_tipo_marketing) AS Marketing,
                            (SELECT nome FROM lista_campagne WHERE id = calendario.id_campagna) AS Campagna";
        }else{
            //oggetto AS 'Oggetto', mittente AS 'Mittente', dataagg AS 'Data', campo_5 AS 'E-Mail', campo_4 AS 'Telefono', stato,
            $campi_visualizzati = "CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"".BASE_URL."/moduli/anagrafiche/dettaglio_tab.php?tbl=calendario&id=',calendario.id,'\" title=\"SCHEDA\" alt=\"SCHEDA\"><i class=\"fa fa-book\"></i></a>') AS 'fa-book',
                            (SELECT CONCAT(lista_password.nome,' ',lista_password.cognome) FROM lista_password WHERE lista_password.id=calendario.id_agente) AS 'Commerciale', 
                            calendario.nome_obiezione, 
                            IF(calendario.id_azienda>0,CONCAT('<i class=\"fa fa-user btn btn-icon-only green-jungle btn-outline\" style=\"display: inline; padding: 3px; line-height: 0.5;\"></i>'),CONCAT('<i class=\"fa fa-user-times btn btn-icon-only red-flamingo btn-outline\" style=\"display: inline; padding: 3px; line-height: 0.5;\"></i>')) AS 'fa-user', 
                            calendario.mittente,
                            IF(calendario.id_professionista>0,(SELECT CONCAT(lista_professionisti.cognome,' ',lista_professionisti.nome) FROM lista_professionisti WHERE lista_professionisti.id=calendario.id_professionista ),'') AS Professionista,
                            (SELECT lista_prodotti.nome FROM lista_prodotti WHERE lista_prodotti.id=calendario.id_prodotto) AS 'Corso', lista_preventivi.data_iscrizione AS 'Data Negativo', lista_preventivi.imponibile AS Importo, 
                            (SELECT nome FROM lista_tipo_marketing WHERE id = id_tipo_marketing) AS Marketing,
                            (SELECT nome FROM lista_campagne WHERE id = calendario.id_campagna) AS Campagna";
        }
        //$where = $table_calendario['index']['where'];
        $where = " (lista_preventivi.stato='Negativo') AND calendario.stato='Negativo' $where_calendario $where_data_calendario_iscritto";
        if($whrStato == "ccd8ed1e063d333d633344cddc386f37") {
            $where .= " AND calendario.nome_obiezione = '' ";
        }
        if(!empty($arrayCampoRicerca)){
            foreach ($arrayCampoRicerca as $campoRicerca) {
                if($campoRicerca=="iscritto") $campoRicerca = "venduto";
                $campoRicerca = $dblink->filter($campoRicerca);
                $where.= " AND (calendario.oggetto LIKE '%".$campoRicerca."%' OR calendario.mittente LIKE '%".$campoRicerca."%'";
                $where.= " OR lista_preventivi.data_iscrizione LIKE '%".$campoRicerca."%' OR calendario.campo_5 LIKE '%".$campoRicerca."%'";
                $where.= " OR calendario.nome LIKE '%".$campoRicerca."%' OR calendario.cognome LIKE '%".$campoRicerca."%'";
                $where.= " OR calendario.email LIKE '%".$campoRicerca."%' OR calendario.campo_9 LIKE '%".$campoRicerca."%'";
                $where.= " OR calendario.messaggio LIKE '%".$campoRicerca."%' OR lista_preventivi.cognome_nome_agente LIKE '%".$campoRicerca."%'";
                $where.= " OR calendario.tipo_marketing LIKE '%".$campoRicerca."%' OR calendario.telefono LIKE '%".$campoRicerca."%'";
                $where.= " OR calendario.cellulare LIKE '%".$campoRicerca."%' OR calendario.professione LIKE '%".$campoRicerca."%'";
                $where.= " OR calendario.campo_4 LIKE '%".$campoRicerca."%' OR calendario.stato LIKE '%".$campoRicerca."%')";
            }
        }
        //$ordine = $table_calendario['index']['order'];
        $ordine = " ORDER BY datainsert DESC, orainsert ASC";
    break;
    
    case MD5('Note di Credito'):
        $tabella = "calendario INNER JOIN lista_fatture ON calendario.id = lista_fatture.id_calendario";
            //oggetto AS 'Oggetto', mittente AS 'Mittente', dataagg AS 'Data', campo_5 AS 'E-Mail', campo_4 AS 'Telefono', stato,
            $campi_visualizzati = "CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"".BASE_URL."/moduli/anagrafiche/dettaglio_tab.php?tbl=calendario&id=',calendario.id,'\" title=\"SCHEDA\" alt=\"SCHEDA\"><i class=\"fa fa-book\"></i></a>') AS 'fa-book',
                            (SELECT CONCAT(lista_password.nome,' ',lista_password.cognome) FROM lista_password WHERE lista_password.id=calendario.id_agente) AS 'Commerciale', 
                            calendario.stato, IF(calendario.id_azienda>0,CONCAT('<i class=\"fa fa-user btn btn-icon-only green-jungle btn-outline\" style=\"display: inline; padding: 3px; line-height: 0.5;\"></i>'),CONCAT('<i class=\"fa fa-user-times btn btn-icon-only red-flamingo btn-outline\" style=\"display: inline; padding: 3px; line-height: 0.5;\"></i>')) AS 'fa-user', 
                            calendario.mittente,
                            IF(calendario.id_professionista>0,(SELECT CONCAT(lista_professionisti.cognome,' ',lista_professionisti.nome) FROM lista_professionisti WHERE lista_professionisti.id=calendario.id_professionista ),'') AS Professionista,
                            (SELECT lista_prodotti.nome FROM lista_prodotti WHERE lista_prodotti.id=calendario.id_prodotto) AS 'Corso', 
                            lista_fatture.data_creazione AS 'Data Note di Credito', 
                            lista_fatture.codice_ricerca AS 'Cod. Fattura',
                            lista_fatture.imponibile AS Importo, 
                            (SELECT nome FROM lista_tipo_marketing WHERE id = id_tipo_marketing) AS Marketing";
        //$where = $table_calendario['index']['where'];
        $where = " (lista_fatture.stato LIKE 'Nota di Credito%' AND lista_fatture.tipo='Nota di Credito') $where_data_calendario_fattura";
        if(!empty($arrayCampoRicerca)){
            foreach ($arrayCampoRicerca as $campoRicerca) {
                if($campoRicerca=="iscritto") $campoRicerca = "venduto";
                $campoRicerca = $dblink->filter($campoRicerca);
                $where.= " AND (calendario.oggetto LIKE '%".$campoRicerca."%' OR calendario.mittente LIKE '%".$campoRicerca."%'";
                $where.= " OR calendario.campo_5 LIKE '%".$campoRicerca."%'";
                $where.= " OR calendario.nome LIKE '%".$campoRicerca."%' OR calendario.cognome LIKE '%".$campoRicerca."%'";
                $where.= " OR calendario.email LIKE '%".$campoRicerca."%' OR calendario.campo_9 LIKE '%".$campoRicerca."%'";
                $where.= " OR calendario.messaggio LIKE '%".$campoRicerca."%' OR lista_fatture.cognome_nome_agente LIKE '%".$campoRicerca."%'";
                $where.= " OR calendario.tipo_marketing LIKE '%".$campoRicerca."%' OR calendario.telefono LIKE '%".$campoRicerca."%'";
                $where.= " OR calendario.cellulare LIKE '%".$campoRicerca."%' OR calendario.professione LIKE '%".$campoRicerca."%'";
                $where.= " OR calendario.campo_4 LIKE '%".$campoRicerca."%' OR calendario.stato LIKE '%".$campoRicerca."%')";
            }
        }
        //$ordine = $table_calendario['index']['order'];
        $ordine = " ORDER BY lista_fatture.data_creazione DESC";
    break;
    
    case MD5('Iscritto FREE'):
        $tabella = "calendario INNER JOIN lista_preventivi ON calendario.id = lista_preventivi.id_calendario";
            //oggetto AS 'Oggetto', mittente AS 'Mittente', dataagg AS 'Data', campo_5 AS 'E-Mail', campo_4 AS 'Telefono', stato,
            $campi_visualizzati = "CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"".BASE_URL."/moduli/anagrafiche/dettaglio_tab.php?tbl=calendario&id=',calendario.id,'\" title=\"SCHEDA\" alt=\"SCHEDA\"><i class=\"fa fa-book\"></i></a>') AS 'fa-book',
                            (SELECT CONCAT(lista_password.nome,' ',lista_password.cognome) FROM lista_password WHERE lista_password.id=calendario.id_agente) AS 'Commerciale', 
                            calendario.stato, IF(calendario.id_azienda>0,CONCAT('<i class=\"fa fa-user btn btn-icon-only green-jungle btn-outline\" style=\"display: inline; padding: 3px; line-height: 0.5;\"></i>'),CONCAT('<i class=\"fa fa-user-times btn btn-icon-only red-flamingo btn-outline\" style=\"display: inline; padding: 3px; line-height: 0.5;\"></i>')) AS 'fa-user', 
                            calendario.mittente,
                            IF(calendario.id_professionista>0,(SELECT CONCAT(lista_professionisti.cognome,' ',lista_professionisti.nome) FROM lista_professionisti WHERE lista_professionisti.id=calendario.id_professionista ),'') AS Professionista,
                            (SELECT lista_prodotti.nome FROM lista_prodotti WHERE lista_prodotti.id=calendario.id_prodotto) AS 'Corso', 
                            lista_preventivi.data_iscrizione,
                            (SELECT nome FROM lista_tipo_marketing WHERE id = id_tipo_marketing) AS Marketing";
        //$where = $table_calendario['index']['where'];
        $where = " lista_preventivi.sezionale LIKE 'FREE' $where_data_calendario_iscritto";
        if(!empty($arrayCampoRicerca)){
            foreach ($arrayCampoRicerca as $campoRicerca) {
                if($campoRicerca=="iscritto") $campoRicerca = "venduto";
                $campoRicerca = $dblink->filter($campoRicerca);
                $where.= " AND (calendario.oggetto LIKE '%".$campoRicerca."%' OR calendario.mittente LIKE '%".$campoRicerca."%'";
                $where.= " OR lista_preventivi.data_iscrizione LIKE '%".$campoRicerca."%' OR calendario.campo_5 LIKE '%".$campoRicerca."%'";
                $where.= " OR calendario.nome LIKE '%".$campoRicerca."%' OR calendario.cognome LIKE '%".$campoRicerca."%'";
                $where.= " OR calendario.email LIKE '%".$campoRicerca."%' OR calendario.campo_9 LIKE '%".$campoRicerca."%'";
                $where.= " OR calendario.messaggio LIKE '%".$campoRicerca."%' OR lista_preventivi.cognome_nome_agente LIKE '%".$campoRicerca."%'";
                $where.= " OR calendario.tipo_marketing LIKE '%".$campoRicerca."%' OR calendario.telefono LIKE '%".$campoRicerca."%'";
                $where.= " OR calendario.cellulare LIKE '%".$campoRicerca."%' OR calendario.professione LIKE '%".$campoRicerca."%'";
                $where.= " OR calendario.campo_4 LIKE '%".$campoRicerca."%' OR calendario.stato LIKE '%".$campoRicerca."%')";
            }
        }
        //$ordine = $table_calendario['index']['order'];
        $ordine = " ORDER BY lista_preventivi.data_iscrizione DESC";
    break;
    
    case MD5('esportaBenedetto'):
        $tabella = " lista_iscrizioni INNER JOIN lista_fatture ON lista_iscrizioni.id_fattura = lista_fatture.id
INNER JOIN calendario ON lista_fatture.id_calendario = calendario.id";
            //oggetto AS 'Oggetto', mittente AS 'Mittente', dataagg AS 'Data', campo_5 AS 'E-Mail', campo_4 AS 'Telefono', stato,
            $campi_visualizzati = "CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"".BASE_URL."/moduli/anagrafiche/dettaglio_tab.php?tbl=calendario&id=',calendario.id,'\" title=\"SCHEDA\" alt=\"SCHEDA\"><i class=\"fa fa-book\"></i></a>') AS 'fa-book',
                           lista_iscrizioni.id as 'idIscrizione',
lista_iscrizioni.id_fattura AS 'idFattura',
calendario.id AS 'idRichiesta',
lista_iscrizioni.id_corso AS 'idCorso',
lista_iscrizioni.nome_corso AS 'NomeCorso', 
calendario.datainsert AS 'DataRichiesta',
(SELECT data_iscrizione FROM lista_preventivi WHERE lista_preventivi.id_calendario = calendario.id LIMIT 1) AS 'DataIscrizione',
lista_iscrizioni.data_inizio AS 'DataInizio',
lista_iscrizioni.data_completamento AS 'DataCompletamento',
lista_iscrizioni.stato AS 'StatoIscrizione',
calendario.stato AS 'StatoRichiesta',
(SELECT cognome from lista_professionisti WHERE lista_professionisti.id = lista_fatture.id_professionista LIMIT 1) AS Cognome,
(SELECT nome FROM   lista_professionisti WHERE lista_professionisti.id = lista_fatture.id_professionista LIMIT 1) AS Nome,
(SELECT professione FROM lista_professionisti WHERE lista_professionisti.id = lista_fatture.id_professionista LIMIT 1) AS Professione,
(SELECT telefono FROM lista_professionisti WHERE lista_professionisti.id = lista_fatture.id_professionista LIMIT 1) AS telefono,
(SELECT cellulare FROM lista_professionisti WHERE lista_professionisti.id = lista_fatture.id_professionista LIMIT 1) AS cellulare,
(SELECT indirizzo FROM lista_aziende WHERE lista_aziende.id = lista_fatture.id_azienda LIMIT 1) AS indirizzo,
(SELECT cap FROM lista_aziende WHERE lista_aziende.id = lista_fatture.id_azienda LIMIT 1) AS cap,
(SELECT citta FROM lista_aziende WHERE lista_aziende.id = lista_fatture.id_azienda LIMIT 1) AS citta,
(SELECT provincia FROM lista_aziende WHERE lista_aziende.id = lista_fatture.id_azienda LIMIT 1) AS prov,
(SELECT regione_province FROM lista_aziende INNER JOIN `lista_province` ON lista_aziende.provincia = `lista_province`.sigla_province WHERE lista_aziende.id = lista_fatture.id_azienda  LIMIT 1) AS regione,
(SELECT email FROM   lista_professionisti WHERE lista_professionisti.id = lista_fatture.id_professionista LIMIT 1) AS Email,
(SELECT CONCAT(lista_password.cognome,' ', lista_password.nome) FROM lista_password WHERE lista_password.id = calendario.id_agente LIMIT 1) AS 'Comm.le', 
(SELECT lista_provvigioni.nome FROM lista_provvigioni WHERE lista_provvigioni.id IN (SELECT lista_fatture_dettaglio.id_provvigione FROM lista_fatture_dettaglio WHERE  lista_fatture_dettaglio.id = lista_iscrizioni.id_fattura_dettaglio) LIMIT 1) AS 'Partner'";
        //$where = $table_calendario['index']['where'];
        $where = " calendario.etichetta LIKE '%richiesta%' AND lista_iscrizioni.id_corso >0 AND lista_fatture.sezionale NOT LIKE 'CN%'";
        if(!empty($arrayCampoRicerca)){
            foreach ($arrayCampoRicerca as $campoRicerca) {
                if($campoRicerca=="iscritto") $campoRicerca = "venduto";
                $campoRicerca = $dblink->filter($campoRicerca);
                $where.= " AND (calendario.oggetto LIKE '%".$campoRicerca."%' OR calendario.mittente LIKE '%".$campoRicerca."%'";
                $where.= " OR calendario.campo_5 LIKE '%".$campoRicerca."%'";
                $where.= " OR calendario.nome LIKE '%".$campoRicerca."%' OR calendario.cognome LIKE '%".$campoRicerca."%'";
                $where.= " OR calendario.email LIKE '%".$campoRicerca."%' OR calendario.campo_9 LIKE '%".$campoRicerca."%'";
                $where.= " OR calendario.messaggio LIKE '%".$campoRicerca."%' OR lista_fatture.cognome_nome_agente LIKE '%".$campoRicerca."%'";
                $where.= " OR calendario.tipo_marketing LIKE '%".$campoRicerca."%' OR calendario.telefono LIKE '%".$campoRicerca."%'";
                $where.= " OR calendario.cellulare LIKE '%".$campoRicerca."%' OR calendario.professione LIKE '%".$campoRicerca."%'";
                $where.= " OR calendario.campo_4 LIKE '%".$campoRicerca."%' OR calendario.stato LIKE '%".$campoRicerca."%')";
            }
        }
        //$ordine = $table_calendario['index']['order'];
        $ordine = " ORDER BY calendario.datainsert ASC";
    break;
    
    case MD5('richiesteSerena'):
        $tabella = "calendario";
            //oggetto AS 'Oggetto', mittente AS 'Mittente', dataagg AS 'Data', campo_5 AS 'E-Mail', campo_4 AS 'Telefono', stato,
            $campi_visualizzati = "CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"".BASE_URL."/moduli/anagrafiche/dettaglio_tab.php?tbl=calendario&id=',id,'\" title=\"SCHEDA\" alt=\"SCHEDA\"><i class=\"fa fa-book\"></i></a>') AS 'fa-book',
            id as idRichiesta,
if(id_professionista<=0,mittente,'Cliente') As 'Mittente',
if(id_professionista<=0,cognome, (SELECT cognome from lista_professionisti WHERE lista_professionisti.id = id_professionista)) AS cognome,
if(id_professionista<=0,nome, (SELECT nome from lista_professionisti WHERE lista_professionisti.id = id_professionista)) AS nome,
if(id_professionista<=0,professione, (SELECT professione from lista_professionisti WHERE lista_professionisti.id = id_professionista)) AS professione,
if(id_professionista<=0,luogo_di_nascita, (SELECT luogo_di_nascita from lista_professionisti WHERE lista_professionisti.id = id_professionista)) AS citta,
if(id_professionista<=0,telefono, (SELECT telefono from lista_professionisti WHERE lista_professionisti.id = id_professionista)) AS telefono,
if(id_professionista<=0,cellulare, (SELECT cellulare from lista_professionisti WHERE lista_professionisti.id = id_professionista)) AS cellulare,
if(id_professionista<=0,email, (SELECT email from lista_professionisti WHERE lista_professionisti.id = id_professionista)) AS email,
(SELECT nome from lista_prodotti WHERE lista_prodotti.id = id_prodotto) AS prodotto,
(SELECT importo from lista_preventivi WHERE lista_preventivi.id = id_preventivo) AS importo,
(SELECT concat(cognome,' ',nome) from lista_password WHERE lista_password.id = id_agente) AS commerciale,
(SELECT nome from lista_tipo_marketing WHERE lista_tipo_marketing.id = id_tipo_marketing) AS tipo_mkt,
(SELECT nome from lista_campagne WHERE lista_campagne.id = id_campagna) AS campagna_mkt,
SUBSTRING_INDEX(campo_8,'/',-2) AS link,
messaggio as note,
calendario.stato as stato,
datainsert as data_richiesta,
orainsert as ora_richiesta";
        //$where = $table_calendario['index']['where'];
        $where = " `etichetta` LIKE '%richiesta%' $where_data_calendario_inserimento";
        if(!empty($arrayCampoRicerca)){
            foreach ($arrayCampoRicerca as $campoRicerca) {
                if($campoRicerca=="iscritto") $campoRicerca = "venduto";
                $campoRicerca = $dblink->filter($campoRicerca);
                $where.= " AND (calendario.oggetto LIKE '%".$campoRicerca."%' OR calendario.mittente LIKE '%".$campoRicerca."%'";
                $where.= " OR calendario.campo_8 LIKE '%".$campoRicerca."%' OR calendario.campo_5 LIKE '%".$campoRicerca."%'";
                $where.= " OR calendario.nome LIKE '%".$campoRicerca."%' OR calendario.cognome LIKE '%".$campoRicerca."%'";
                $where.= " OR calendario.email LIKE '%".$campoRicerca."%' OR calendario.campo_9 LIKE '%".$campoRicerca."%'";
                $where.= " OR calendario.tipo_marketing LIKE '%".$campoRicerca."%' OR calendario.telefono LIKE '%".$campoRicerca."%'";
                $where.= " OR calendario.cellulare LIKE '%".$campoRicerca."%' OR calendario.professione LIKE '%".$campoRicerca."%'";
                $where.= " OR calendario.campo_4 LIKE '%".$campoRicerca."%' OR calendario.stato LIKE '%".$campoRicerca."%')";
            }
        }
        //$ordine = $table_calendario['index']['order'];
        $ordine = " ORDER BY datainsert ASC, orainsert ASC";
    break;

    default:
        if($_SESSION['livello_utente']=="commerciale"){
            $campi_visualizzati = "IF(stato LIKE 'In Attesa di Controllo', '',
                            CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"".BASE_URL."/moduli/anagrafiche/dettaglio_tab.php?tbl=calendario&id=',id,'\" title=\"SCHEDA\" alt=\"SCHEDA\"><i class=\"fa fa-book\"></i></a>')) AS 'fa-book',
                            (SELECT CONCAT(lista_password.nome,' ',lista_password.cognome) FROM lista_password WHERE lista_password.id=calendario.id_agente) AS 'Commerciale', 
                            stato, IF(id_azienda>0,CONCAT('<i class=\"fa fa-user btn btn-icon-only green-jungle btn-outline\" style=\"display: inline; padding: 3px; line-height: 0.5;\"></i>'),CONCAT('<i class=\"fa fa-user-times btn btn-icon-only red-flamingo btn-outline\" style=\"display: inline; padding: 3px; line-height: 0.5;\"></i>')) AS 'fa-user',
                            mittente AS 'Mittente',
                            IF(id_professionista>0,(SELECT CONCAT(lista_professionisti.cognome,' ',lista_professionisti.nome) FROM lista_professionisti WHERE lista_professionisti.id=calendario.id_professionista ),'') AS Professionista,
                            (SELECT lista_prodotti.nome FROM lista_prodotti WHERE lista_prodotti.id=calendario.id_prodotto) AS 'Corso', data AS 'Data Richiamo', ora AS 'Ora Richiamo', 
                            (SELECT nome FROM lista_tipo_marketing WHERE id = id_tipo_marketing) AS Marketing";
            $where = " 1 ".$where_calendario_all." AND etichetta LIKE 'Nuova Richiesta' "; //AND ( stato LIKE 'In Attesa di Controllo' OR  stato LIKE 'Richiamare' OR stato LIKE 'Mai Contattato' OR stato LIKE 'Venduto' OR stato LIKE 'Negativo')
        }else{
            $campi_visualizzati = $table_calendario['index']['campi'];
            $where = $table_calendario['index']['where']. " AND etichetta LIKE 'Nuova Richiesta' ";
        }
        $ordine = $table_calendario['index']['order'];
        
        /*if(!empty($orderColumn)){
            $ordine = "ORDER BY ";
            foreach ($orderColumn as $order) {
                $ordine.= (intval($order['column'])+1)." ".$order['dir'].", ";
            }
            $ordine = substr($ordine, 0, -2);
        }*/
        
        if(!empty($arrayCampoRicerca)){
            foreach ($arrayCampoRicerca as $campoRicerca) {
                if($campoRicerca=="iscritto") $campoRicerca = "venduto";
                $campoRicerca = $dblink->filter($campoRicerca);
                $where.= " AND (oggetto LIKE '%".$campoRicerca."%' OR mittente LIKE '%".$campoRicerca."%'";
                $where.= " OR dataagg LIKE '%".$campoRicerca."%' OR campo_5 LIKE '%".$campoRicerca."%'";
                $where.= " OR nome LIKE '%".$campoRicerca."%' OR cognome LIKE '%".$campoRicerca."%'";
                $where.= " OR email LIKE '%".$campoRicerca."%' OR campo_9 LIKE '%".$campoRicerca."%'";
                $where.= " OR messaggio LIKE '%".$campoRicerca."%' OR destinatario LIKE '%".$campoRicerca."%'";
                $where.= " OR tipo_marketing LIKE '%".$campoRicerca."%' OR telefono LIKE '%".$campoRicerca."%'";
                $where.= " OR cellulare LIKE '%".$campoRicerca."%' OR professione LIKE '%".$campoRicerca."%'";
                $where.= " OR campo_4 LIKE '%".$campoRicerca."%' OR stato LIKE '%".$campoRicerca."%')";
            }
        }
        //$where.= " AND partita_iva LIKE '%".$_REQUEST['order_partita_iva']."%' AND codice_fiscale LIKE '%".$_REQUEST['order_codice_fiscale']."%'";
        //$where.= " AND telefono LIKE '%".$_REQUEST['order_telefono']."%' AND stato LIKE '%".$_REQUEST['order_stato']."%'";
    break;
}

$sql_0001 = "SELECT count(calendario.id) AS conto FROM ".$tabella." WHERE $where";

$numRowRes = $dblink->get_row($sql_0001,true);
$numRow = $numRowRes['conto'];

//$numRow = $dblink->num_rows($sql_0001);
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
$sql_0002 = "SELECT ".$campi_visualizzati." FROM ".$tabella." WHERE $where $ordine $limite";

$rs_0002 = $dblink->get_results($sql_0002);
$fields = $dblink->list_fields($sql_0002);

$r = 0;

foreach ($rs_0002 as $row) {
    $c = 0;
    foreach ($row as $column) {
        if (strtolower($fields[$c]->name) == "stato") {
            //echo $column;
            if (strpos($column, "|") !== false) {
                $tmpStato = explode("|", $column);
                $classeColore = array($tmpStato[0]);
                $column = $tmpStato[1];
            } else {
                $classeColore = $dblink->get_row("SELECT colore_sfondo, nome_alias FROM lista_richieste_stati WHERE nome='" . $dblink->filter($column) . "' AND colore_sfondo!=''");
            }
        } else {
            $classeColore = false;
        }
        if ($classeColore != false) {
            $records["data"][$r][] = '<span class="badge bold bg-' . $classeColore[0] . ' bg-font-' . $classeColore[0] . '"> ' . (strlen($classeColore[1])>0 ? $classeColore[1] : $column) . ' </span>';
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
                    $records["data"][$r][] = '<span class="badge bold bg-blue-steel bg-font-blue-steel"> Iscritto </span>';
                break;
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
                    }else if (strtolower($nomeCampo) == "data" || strtolower($nomeCampo) == "data_creazione" || strtolower($nomeCampo) == "data_iscrizione" || strtolower($nomeCampo) == "datainsert") {
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
