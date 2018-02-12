<?php
session_start();
include_once('../../config/connDB.php');
include_once(BASE_ROOT . 'config/confAccesso.php');
require_once(BASE_ROOT . 'config/confPermessi.php');
include_once(BASE_ROOT . 'moduli/anagrafiche/funzioni.php');

if(isset($_GET['idMenu'])){
    $idMenu = $_GET['idMenu'];
}else{
    $idMenu = "";
}

/* 	fine post ricerca sinistra	 */
if (isset($_GET['id']) && $_GET['id'] != ""  && $_GET['id'] != "0") {
    $id = $_GET['id'];
} else {
    $id = "";
    $log->log_all_errors('Impossibile accedere per mancanza del parametro: [id = '.$id.']','ERRORE');
    header("Location:".recupera_referer());
    die;
}

if (isset($_GET['tbl']) && $_GET['tbl'] != "") {
    $tabella = $_GET['tbl'];
} else {
    $tabella = "";
    
    $log->log_all_errors('Impossibile accedere per mancanza del parametro: [tabella = '.$tabella.']','ERRORE');
    header("Location:".recupera_referer());
    die;
}

if($_SESSION['livello_utente']=='commerciale'){
    $livelloCommerciale = true;
}else{
    $livelloCommerciale = false;
}

if($_SESSION['livello_utente']=='betaadmin' || $_SESSION['livello_utente']=='amministratore' || $_SESSION['livello_utente']=='backoffice'){
    $livelloAdmin = true;
}else{
    $livelloAdmin = false;
}

switch ($tabella) {
    case 'calendario';
        $sql_00004 = "SELECT * FROM calendario WHERE id='".$id."' AND etichetta = 'Nuova Richiesta'";
        $row_00004 = $dblink->get_row($sql_00004, true);
        $idCalendario_daPassare = $row_00004['id'];
        $id_professionista_presente = $row_00004['id_professionista'];
        $id_azienda_presente = $row_00004['id_azienda'];
        $id_agente_presente = $row_00004['id_agente'];
        $statoAttuale = $row_00004['stato'];
        $richiestaReadonly = false;
        if(strtolower($statoAttuale)!="richiamare" && strtolower($statoAttuale)!="mai contattato" && strtolower($statoAttuale)!="in attesa di controllo"){
            $richiestaReadonly = true;
        }
        
        if($livelloAdmin && strtolower($statoAttuale)=="chiusa in attesa di controllo"){
            $richiestaReadonly = false;
        }
        
        if($idCalendario_daPassare > 0 && $id_professionista_presente > 0) {
            $dblink->update("lista_preventivi", array("id_professionista" => $id_professionista_presente, "id_azienda"=>$id_azienda_presente), array("id_calendario"=>$idCalendario_daPassare, "id_professionista"=>'0'));
        }
        
        if($row_00004['id_professionista']>0){
            $sql_00001 = "SELECT * FROM lista_professionisti WHERE id=" . $row_00004['id_professionista'];
            $row_00001 = $dblink->get_row($sql_00001, true);
        }

        if($row_00004['id_azienda']>0){
            $sql_00002 = "SELECT lista_aziende.* FROM lista_aziende
                        INNER JOIN matrice_aziende_professionisti
                        ON lista_aziende.id = matrice_aziende_professionisti.id_azienda
                        WHERE matrice_aziende_professionisti.id_azienda=" . $row_00004['id_azienda']. "
                        AND (matrice_aziende_professionisti.stato='Attivo' OR matrice_aziende_professionisti.stato='')";
            $row_00002 = $dblink->get_row($sql_00002, true);
        }else{
            $row_00002 = array(
                "ragione_sociale" => "",
                "forma_giuridica" => "",
                "partita_iva" => "",
                "codice_fiscale" => "",
                "indirizzo" => "",
                "cap" => "",
                "citta" => "",
                "nazione" => "",
                "provincia" => "",
                "telefono" => "",
                "cellulare" => "",
                "fax" => "",
                "email" => "",
                "web" => "",
                "settore" => "",
                "categoria" => ""
            );
        }

        $sql_00003 = "SELECT * FROM calendario WHERE id='" . $id . "' AND etichetta = 'Nuova Richiesta' ORDER BY id DESC";
        $row_00003 = $dblink->get_row($sql_00003, true);
        //print_r($row_00003);
        break;

    case 'lista_professionisti';
        //rinominiamo campi input per professionisti professionisti_txt_epoinomecolonna
        $sql_00001 = "SELECT * FROM lista_professionisti WHERE id='".$id."'";
        $row_00001 = $dblink->get_row($sql_00001, true);
        //print_r($row_00001);
        //rinominiamo campi input per aziende aziende_txt_epoinomecolonna
        $sql_00002 = "SELECT lista_aziende.* FROM lista_aziende
                        INNER JOIN matrice_aziende_professionisti
                        ON lista_aziende.id = matrice_aziende_professionisti.id_azienda
                        WHERE matrice_aziende_professionisti.id_professionista='" . $id. "'
                        AND (matrice_aziende_professionisti.stato='Attivo' OR matrice_aziende_professionisti.stato='')";
        $row_00002 = $dblink->get_row($sql_00002, true);
        //print_r($row_00002);

        $sql_00003 = "SELECT * FROM calendario WHERE id_professionista='" . $id . "' AND etichetta = 'Nuova Richiesta' ORDER BY id DESC";
        $row_00003 = $dblink->get_row($sql_00003, true);
        $idCalendario_daPassare = $row_00003['id'];
        $id_professionista_presente = $id;
        $id_azienda_presente = $row_00002['id'];
        $id_agente_presente = $row_00003['id_agente'];
        $statoAttuale = $row_00003['stato'];
        $richiestaReadonly = false;
        if(strtolower($statoAttuale)!="richiamare" && strtolower($statoAttuale)!="mai contattato" && strtolower($statoAttuale)!="in attesa di controllo"){
            $richiestaReadonly = true;
        }
        
        if($livelloAdmin && strtolower($statoAttuale)=="chiusa in attesa di controllo"){
            $richiestaReadonly = false;
        }
        //print_r($row_00003);
        break;
}

    //echo '<h1>$idCalendario_daPassare = '.$idCalendario_daPassare.'</h1>';
    $sql_00010 = "UPDATE calendario, lista_campagne
    SET calendario.id_prodotto = lista_campagne.id_prodotto,
    calendario.id_tipo_marketing = lista_campagne.id_tipo_marketing
    WHERE calendario.id_campagna>0
    AND calendario.id_prodotto<=0
    AND calendario.id_tipo_marketing<=0
    AND calendario.id_campagna = lista_campagne.id";
    $rs_000010 = $dblink->query($sql_00010, true);

    //AGGIORNO ID_AGENTE SUL PREVENTIVO                
    $sql_00011 = "UPDATE lista_preventivi, calendario
    SET  lista_preventivi.id_agente = calendario.id_agente
    WHERE lista_preventivi.id_agente<=0
    AND calendario.id = lista_preventivi.id_calendario AND lista_preventivi.id_calendario > 0";
    $rs_000011 = $dblink->query($sql_00011, true);


    //echo '<h1>idCalendario_daPassare = '.$idCalendario_daPassare.'</h1>';

    if($idCalendario_daPassare>0){
 
        $sql_0012 = "SELECT lista_preventivi.id FROM lista_preventivi INNER JOIN calendario ON lista_preventivi.id_calendario = calendario.id
        WHERE lista_preventivi.id_calendario='".$idCalendario_daPassare."'
        AND calendario.id = '".$idCalendario_daPassare."'
        AND calendario.etichetta='Nuova Richiesta'
        AND lista_preventivi.stato='In Attesa'";
        $conta_preventivi = $dblink->num_rows($sql_0012);
        //echo '<h1>conta_preventivi = '.$conta_preventivi.'</h1>';
        if($conta_preventivi<=0 && (($id_professionista_presente>=0 && $livelloCommerciale) OR ($id_professionista_presente>=0 && !$livelloCommerciale)) && !$richiestaReadonly){

            $sql_0013 = "INSERT INTO lista_preventivi (dataagg, data_creazione, data_scadenza, codice, `id_professionista`, `id_azienda`, `id_campagna`, `id_calendario`, `imponibile`, `importo`,  `scrittore`, `id_agente`, `stato`, note, nome_campagna, cognome_nome_professionista, id_sezionale, sezionale)
                        SELECT NOW(), datainsert, DATE_ADD(datainsert, INTERVAL 10 DAY), 'xxx',  `id_professionista`, `id_azienda`, `id_campagna`, `id`, '','', '".addslashes($_SESSION['cognome_nome_utente'])."', `id_agente`, 'In Attesa', messaggio, campo_7, mittente, (SELECT IF(id_sezionale = 8, id_sezionale, 3) FROM lista_campagne WHERE id = id_campagna), (SELECT IF(id_sezionale = 8, 'FREE', '00') FROM lista_campagne WHERE id = id_campagna)
                        FROM calendario WHERE id=".$idCalendario_daPassare." AND calendario.etichetta='Nuova Richiesta' AND calendario.id_professionista>=0";
            $rs_00013 = $dblink->query($sql_0013, true);
            $idAutoPreventivoCampagna = $dblink->lastid();
            //echo '<h1>idAutoPreventivoCampagna = '.$idAutoPreventivoCampagna.'</h1>';

            $sql_0014 = "INSERT INTO lista_preventivi_dettaglio (dataagg, id_preventivo, id_prodotto, quantita, id_campagna, id_calendario, scrittore, stato, id_professionista, id_sezionale, sezionale)
                        SELECT NOW(), '".$idAutoPreventivoCampagna."',  id_prodotto, '1', id_campagna, id, '".addslashes($_SESSION['cognome_nome_utente'])."', 'In Attesa' , `id_professionista`, (SELECT IF(id_sezionale = 8, id_sezionale, 3) FROM lista_campagne WHERE id = id_campagna), (SELECT IF(id_sezionale = 8, 'FREE', '00') FROM lista_campagne WHERE id = id_campagna)
                        FROM calendario WHERE id=".$idCalendario_daPassare." AND calendario.etichetta='Nuova Richiesta'";
            $rs_00014 = $dblink->query($sql_0014, true);
            
            $sql_00015 = "UPDATE lista_preventivi_dettaglio, lista_prodotti
                        SET lista_preventivi_dettaglio.nome_prodotto = lista_prodotti.nome,
                       lista_preventivi_dettaglio.prezzo_prodotto = lista_prodotti.prezzo_pubblico,
                       lista_preventivi_dettaglio.codice_prodotto = lista_prodotti.codice,
                       lista_preventivi_dettaglio.iva_prodotto = lista_prodotti.iva
                        WHERE LENGTH(lista_preventivi_dettaglio.nome_prodotto)<=1
                        AND lista_preventivi_dettaglio.id_prodotto = lista_prodotti.id";
                        $rs_000015 = $dblink->query($sql_00015, true);
            
            $sql_000100 = "SELECT SUM((prezzo_prodotto*quantita)) AS imponibile, SUM((prezzo_prodotto*(1+(iva_prodotto/100)))*quantita) AS 'importo' FROM lista_preventivi_dettaglio WHERE id_preventivo=".$idAutoPreventivoCampagna;
            $row_000100 = $dblink->get_row($sql_000100, true);
            //echo $dblink->get_query();
            //echo "<br>";
            $updatePreventivo=array(
                "importo"=>$row_000100['importo'],
                "imponibile"=>$row_000100['imponibile']
            );

            $dblink->update("lista_preventivi", $updatePreventivo, array("id"=>$idAutoPreventivoCampagna));
            
        }elseif((($id_professionista_presente>=0 && $livelloCommerciale) OR ($id_professionista_presente>=0 && !$livelloCommerciale)) && !$richiestaReadonly){
            //echo '<li>Abbiamo preventivo !</li>';
            $sql_00015 = "UPDATE lista_preventivi_dettaglio, lista_prodotti
                        SET lista_preventivi_dettaglio.nome_prodotto = lista_prodotti.nome,
                       lista_preventivi_dettaglio.prezzo_prodotto = lista_prodotti.prezzo_pubblico,
                       lista_preventivi_dettaglio.codice_prodotto = lista_prodotti.codice,
                       lista_preventivi_dettaglio.iva_prodotto = lista_prodotti.iva
                        WHERE LENGTH(lista_preventivi_dettaglio.nome_prodotto)<=1
                        AND lista_preventivi_dettaglio.id_prodotto = lista_prodotti.id";
                        $rs_000015 = $dblink->query($sql_00015, true);

                        
            $sql_0015 = "UPDATE lista_preventivi
            SET cognome_nome_professionista = (SELECT CONCAT(lista_professionisti.cognome,' ', lista_professionisti.nome) FROM lista_professionisti WHERE lista_professionisti.id = lista_preventivi.id_professionista) 
            WHERE id_professionista > 0";
            $dblink->query($sql_0015);
            
            $sql_0020 = "UPDATE calendario, lista_preventivi_dettaglio SET 
            lista_preventivi_dettaglio.id_prodotto = calendario.id_prodotto
            WHERE lista_preventivi_dettaglio.id_calendario = '$idCalendario_daPassare'
            AND calendario.id = '$idCalendario_daPassare'
            AND calendario.etichetta='Nuova Richiesta'
            AND calendario.id_prodotto>0
            AND lista_preventivi_dettaglio.id_prodotto<=0";

            $dblink->query($sql_0020);

            $row_00016 = $dblink->get_row($sql_0012, true);
            $idAutoPreventivoCampagna = $row_00016['id'];
        }
        
        $sql_00020 = "UPDATE calendario, lista_preventivi_dettaglio SET 
        calendario.id_prodotto = lista_preventivi_dettaglio.id_prodotto
        WHERE lista_preventivi_dettaglio.id_calendario = '$idCalendario_daPassare'
        AND calendario.id = '$idCalendario_daPassare'
        AND calendario.etichetta='Nuova Richiesta'
        AND lista_preventivi_dettaglio.id_prodotto>0
        AND calendario.id_prodotto<=0";
        
        $dblink->query($sql_00020);
    }
  
    
    $sql_0050 = "SELECT lista_preventivi.id FROM lista_preventivi INNER JOIN calendario ON lista_preventivi.id_calendario = calendario.id
        WHERE lista_preventivi.id_calendario='".$idCalendario_daPassare."' 
        AND calendario.id = '".$idCalendario_daPassare."'
        AND calendario.etichetta='Nuova Richiesta'
        AND lista_preventivi.stato='In Attesa'";
        $conta_preventivi = $dblink->num_rows($sql_0050);
    
if($id_professionista_presente<=0){
    $nomeTabProfessionista = "Dati Contatto";
    $tabellaProfessionista = "calendario";
    
    $sql_00001 = "SELECT * FROM calendario WHERE id='".$row_00004['id']."'";
    $row_00001 = $dblink->get_row($sql_00001, true);
}else{
    $nomeTabProfessionista = "Dati Participante";
    $tabellaProfessionista = "lista_professionisti";
}

if(($id_agente_presente!=$_SESSION['id_utente'] && $livelloCommerciale) || $_SESSION['livello_utente']=='assistenza' ){
    $richiestaReadonlyCommerciale = true;
}else{
    $richiestaReadonlyCommerciale = false;
}

if($richiestaReadonly===false){
    //AGGIORNO ID_AZIENDA SUL PREVENTIVO                
    $sql_00011_0000002 = "UPDATE lista_preventivi, calendario
    SET  lista_preventivi.id_azienda = calendario.id_azienda,
    calendario.id_preventivo = lista_preventivi.id
    WHERE 1
    AND lista_preventivi.id_calendario = calendario.id AND lista_preventivi.id_calendario > 0";
    $dblink->query($sql_00011_0000002);
    
    //AGGIORNO ID_AZIENDA E ID_PROFESSIONISTA  SUL PREVENTIVO_DETTAGLIO           
    $sql_00011_0000001 = "UPDATE lista_preventivi_dettaglio, lista_preventivi 
    SET  lista_preventivi_dettaglio.id_azienda = lista_preventivi.id_azienda,
     lista_preventivi_dettaglio.id_professionista = lista_preventivi.id_professionista
    WHERE 1
    AND lista_preventivi_dettaglio.id_preventivo = lista_preventivi.id";
    $dblink->query($sql_00011_0000001);
}
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
    <!--<![endif]-->
    <!-- BEGIN HEAD -->
    <head>
        <meta charset="utf-8" />
        <title><?php echo $site_name; ?> | MODIFICA</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <meta content="" name="description" />
        <meta content="" name="author" />
        <!-- BEGIN GLOBAL MANDATORY STYLES -->
        <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
        <link href="<?= BASE_URL ?>/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <link href="<?= BASE_URL ?>/assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
        <link href="<?= BASE_URL ?>/assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?= BASE_URL ?>/assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
        <!-- END GLOBAL MANDATORY STYLES -->
        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <link href="<?= BASE_URL ?>/assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
        <link href="<?= BASE_URL ?>/assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="<?= BASE_URL ?>/assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?= BASE_URL ?>/assets/global/plugins/typeahead/typeahead.css" rel="stylesheet" type="text/css">

        <link href="<?= BASE_URL ?>/assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css" rel="stylesheet" type="text/css" />
        <link href="<?= BASE_URL ?>/assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
        <link href="<?= BASE_URL ?>/assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css" rel="stylesheet" type="text/css" />
        <link href="<?= BASE_URL ?>/assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css" />
        <link href="<?= BASE_URL ?>/assets/global/plugins/clockface/css/clockface.css" rel="stylesheet" type="text/css" />
        <link href="<?= BASE_URL ?>/assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" type="text/css" />
        <link href="<?= BASE_URL ?>/assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />
        <link href="<?= BASE_URL ?>/assets/global/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.css" rel="stylesheet" type="text/css" />
        <link href="<?= BASE_URL ?>/assets/global/plugins/bootstrap-toastr/toastr.min.css" rel="stylesheet" type="text/css">
        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN THEME GLOBAL STYLES -->
        <link href="<?= BASE_URL ?>/assets/global/css/components.min.css" rel="stylesheet" id="style_components" type="text/css" />
        <link href="<?= BASE_URL ?>/assets/global/css/plugins.min.css" rel="stylesheet" type="text/css" />
        <!-- END THEME GLOBAL STYLES -->
        <!-- BEGIN PAGE LEVEL STYLES -->

        <!-- END PAGE LEVEL STYLES -->
        <!-- BEGIN THEME LAYOUT STYLES -->
        <link href="<?= BASE_URL ?>/assets/layouts/layout/css/layout.min.css" rel="stylesheet" type="text/css" />
        <link href="<?= BASE_URL ?>/assets/layouts/layout/css/themes/darkblue.min.css" rel="stylesheet" type="text/css" id="style_color" />
        <link href="<?= BASE_URL ?>/assets/layouts/layout/css/custom.min.css" rel="stylesheet" type="text/css" />
        <!-- END THEME LAYOUT STYLES -->
        <link rel="shortcut icon" href="favicon.ico" />
        <script type="text/javascript">
            function eliminaDettaglioPreventivo(idDelDettPrev, idDelPrev) {
                if (confirm("SEI SICURO DI VOLER ELIMINARE QUESTO PRODOTTO DAL PREVENTIVO ?")) {
                    document.location = 'cancella.php?tbl=lista_preventivi_dettaglio&id=' + idDelDettPrev + '&idPreventivo=' + idDelPrev;
                }
                return false;
            }
        </script>
    </head>

    <!-- END HEAD -->
    <body class="page-header-fixed page-sidebar-closed-hide-logo page-content-white page-sidebar-fixed">
        <!-- BEGIN HEADER -->
        <?php include(BASE_ROOT . '/assets/header.php'); ?>
        <!-- END HEADER -->
        <!-- BEGIN HEADER & CONTENT DIVIDER -->
        <div class="clearfix"> </div>
        <!-- END HEADER & CONTENT DIVIDER -->
        <!-- BEGIN CONTAINER -->
        <div class="page-container">
            <!-- BEGIN SIDEBAR -->
            <?php include(BASE_ROOT . '/assets/sidebar.php'); ?>
            <!-- END SIDEBAR -->
            <!-- BEGIN CONTENT -->
            <div class="page-content-wrapper">
                <!-- BEGIN CONTENT BODY -->
                <div class="page-content">
                    <!-- BEGIN PAGE HEADER-->
                    <!-- BEGIN THEME PANEL -->
                    <!-- END THEME PANEL -->
                    <!-- BEGIN PAGE BAR -->
                    <?php include(BASE_ROOT . '/assets/page_bar.php'); ?>
                    <!-- END PAGE BAR -->
                    <!-- START PAGE TITLE -->
                    <?php
                    get_pagina_titolo($idMenu, $where_lista_menu);
                    ?>
                    <!-- END PAGE TITLE -->
                    <!-- END PAGE HEADER-->
                    <!-- INIZIO ROW TABELLA-->
                    <form id="formDettaglioTabAnagrafica" name="formDettaglioTabAnagrafica" class="form-horizontal form-bordered" enctype="multipart/form-data" role="form" action="<?= BASE_URL ?>/moduli/anagrafiche/salva.php?fn=salvaDettaglio" method="POST">
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <div class="row">
                                <div class="col-md-8 col-sm-8">
                                    <!-- BEGIN PROFILE CONTENT -->
                                        <div class="profile-content">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="portlet light bordered">
                                                        <div class="portlet-title">
                                                            <div class="caption caption-lg">
                                                                <i class="fa fa-user theme-font"></i>
                                                                <span class="caption-subject bold uppercase" style="font-size:24px;"><?php echo $row_00001['cognome'] . '  ' . $row_00001['nome']; ?> <?php if($id_professionista_presente>0){ echo "(".$row_00001['codice'].")"; /*if(!$livelloCommerciale){ */echo "&nbsp;<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"dettaglio.php?tbl=lista_professionisti&id=".$id_professionista_presente."\" title=\"DETTAGLIO PROFESSIONISTA\" alt=\"DETTAGLIO PROFESSIONISTA\"><i class=\"fa fa-search\"></i></a>"; /*}*/  } ?></span>
                                                                <!--<h1 class="bold uppercase"><i class="fa fa-user theme-font"></i> <?php echo $row_00001['cognome'] . '  ' . $row_00001['nome']; ?></h1>-->
                                                                <br><span class="caption-helper" style="display:inline; margin-top:6px;"> Data Inserimento: <strong class="font-blue-chambray"><?php echo GiraDataOra($row_00003['datainsert']); ?></strong> - Ora Inserimento: <strong class="font-blue-chambray"><?=GiraDataOra($row_00003['orainsert']);?></strong> <?php if($livelloAdmin) { ?>ID Prodotto: <?=$row_00003['id_prodotto'];?></span><?php } ?>
                                                                <br><span class="caption-helper" style="display:inline; margin-top:6px;"> Data Agg.: <strong class="font-blue-chambray"><?php echo ordinaDataAgg($row_00003['dataagg']); ?></strong> - Assegnato a: <strong class="font-blue-chambray"><?=getNomeAgente($row_00003['id_agente'])?></strong></span>
                                                            </div>
                                                        </div>
                                                        <div class="portlet-body form">
                                                            <ul class="nav nav-pills">
                                                                <li class="active">
                                                                    <a href="#tab_prof" data-toggle="tab"><?=$nomeTabProfessionista?></a>
                                                                </li>
                                                                <?php if($id_professionista_presente>0){ ?>
                                                                <li>
                                                                    <a href="#tab_azienda" data-toggle="tab">Dati Fatturazione</a>
                                                                </li>
                                                                <li>
                                                                    <a href="#tab_fatture" data-toggle="tab">Fatture</a>
                                                                </li>
                                                                <li>
                                                                    <a href="#tab_corsi" data-toggle="tab">Corsi</a>
                                                                </li>
                                                                <?php } ?>
                                                            </ul>
                                                            <div class="tab-content">
                                                                <!-- PERSONAL INFO TAB -->
                                                                <div class="tab-pane active" id="tab_prof">
                                                                    <div class="row" style="margin-bottom:10px;">
                                                                        <div class="col-md-4">
                                                                            <?=print_select2("SELECT nome as valore, nome AS nome FROM lista_titoli", $tabellaProfessionista."_txt_titolo", $row_00001['titolo'], "", false, 'tooltips select_titolo-allow-clear', 'data-container="body" data-placement="top" data-original-title="TITOLO"'); ?>
                                                                        </div>
                                                                        <div class="col-md-4" style="padding: 0px;">
                                                                            <input name="<?=$tabellaProfessionista?>_txt_cognome" id="<?=$tabellaProfessionista?>_txt_cognome" type="text" class="form-control tooltips" placeholder="Cognome" value="<?php echo ucwords(strtolower($row_00001['cognome'])); ?>" data-container="body" data-placement="top" data-original-title="COGNOME"> </div>
                                                                        <div class="col-md-4">
                                                                            <input name="<?=$tabellaProfessionista?>_txt_nome" id="<?=$tabellaProfessionista?>_txt_nome" type="text" class="form-control tooltips" placeholder="Nome" value="<?php echo ucwords(strtolower($row_00001['nome'])); ?>" data-container="body" data-placement="top" data-original-title="NOME"> </div>
                                                                    </div>

                                                                    <div class="row" style="margin-bottom:10px;">
                                                                        <div class="col-md-4">
                                                                            <input name="lista_professionisti_txt_codice_fiscale" id="lista_professionisti_txt_codice_fiscale" maxlength="16" minlength="16" data-inputmask-regex="^[a-zA-Z]{6}[0-9]{2}[abcdehlmprstABCDEHLMPRST]{1}[0-9]{2}([a-zA-Z]{1}[0-9]{3})[a-zA-Z]{1}$" type="text" class="form-control tooltips" placeholder="Codice Fiscale" value="<?php echo strtoupper($row_00001['codice_fiscale']); ?>" data-container="body" data-placement="top" data-original-title="CODICE FISCALE" <?=($id_professionista_presente>0 ? (strlen($row_00001['codice_fiscale'])==16 ? "readonly" : "") : "readonly")?>> </div>
                                                                        <div class="col-md-2" style="padding: 0px;">
                                                                            <input name="<?=$tabellaProfessionista?>_txt_data_di_nascita" data-inputmask="'mask': 'd-m-y'" id="<?=$tabellaProfessionista?>_txt_data_di_nascita" type="text" class="form-control tooltips date-picker" placeholder="Data di Nascita" value="<?php echo GiraDataOra($row_00001['data_di_nascita']); ?>" data-container="body" data-placement="top" data-original-title="DATA DI NASCITA" data-date-format="dd-mm-yyyy"> </div>
                                                                        <div class="col-md-4" style="padding-right: 0px;">
                                                                            <input name="<?=$tabellaProfessionista?>_txt_luogo_di_nascita" id="<?=$tabellaProfessionista?>_txt_luogo_di_nascita" type="text" class="form-control tooltips" placeholder="Luogo di Nascita" value="<?php echo $row_00001['luogo_di_nascita']; ?>" data-container="body" data-placement="top" data-original-title="LUOGO DI NASCITA"> </div>
                                                                        <div class="col-md-2">
                                                                            <input name="<?=$tabellaProfessionista?>_txt_provincia_di_nascita" data-inputmask="'mask': 'a', 'repeat': 2" id="<?=$tabellaProfessionista?>_txt_provincia_di_nascita" type="text" class="form-control tooltips" placeholder="Provincia Nascita" value="<?php echo strtoupper($row_00001['provincia_di_nascita']); ?>" data-container="body" data-placement="top" data-original-title="PROVINCIA DI NASCITA"> </div>
                                                                    </div>

                                                                    <div class="row" style="margin-bottom:10px;">
                                                                        <div class="col-md-3">
                                                                            <?=print_select2("SELECT nome as valore, nome AS nome FROM lista_professioni", $tabellaProfessionista."_txt_professione", $row_00001['professione'], "", false, 'tooltips select_professione-allow-clear', 'data-container="body" data-placement="top" data-original-title="PROFESSIONE"'); ?>
                                                                            <!--<input name="<?=$tabellaProfessionista?>_txt_professione" id="<?=$tabellaProfessionista?>_txt_professione" type="text" class="form-control tooltips" placeholder="Professione" value="<?php echo $row_00001['professione']; ?>" data-container="body" data-placement="top" data-original-title="PROFESSIONE">--> </div>
                                                                        <div class="col-md-3" style="padding: 0px;">
                                                                            <?=print_select2("SELECT id as valore, nome AS nome FROM lista_classi ORDER BY nome", $tabellaProfessionista."_txt_id_classe", $row_00001['id_classe'], "", false, 'tooltips select_albo-allow-clear', 'data-container="body" data-placement="top" data-original-title="TIPO ALBO"'); ?>
                                                                            <!--<input name="<?=$tabellaProfessionista?>_txt_id_classe" id="<?=$tabellaProfessionista?>_txt_id_classe" type="text" class="form-control tooltips" placeholder="Tipo Albo" value="<?php echo $row_00001['id_classe']; ?>" data-container="body" data-placement="top" data-original-title="TIPO ALBO">--> </div>
                                                                        <div class="col-md-3" style="padding-right: 0px;">
                                                                            <?=print_select2("SELECT sigla_province as valore, sigla_province AS nome FROM lista_province", $tabellaProfessionista."_txt_provincia_albo", $row_00001['provincia_albo'], "", false, 'tooltips select_prov_ablo-allow-clear', 'data-container="body" data-placement="top" data-original-title="PROVINCIA ALBO"'); ?>
                                                                            <!--<input name="<?=$tabellaProfessionista?>_txt_provincia_albo" id="<?=$tabellaProfessionista?>_txt_provincia_albo" type="text" class="form-control tooltips" placeholder="Provincia Albo" value="<?php echo $row_00001['provincia_albo']; ?>" data-container="body" data-placement="top" data-original-title="PROVINCIA ALBO">--> </div>
                                                                        <div class="col-md-3">
                                                                            <input name="<?=$tabellaProfessionista?>_txt_numero_albo" id="<?=$tabellaProfessionista?>_txt_numero_albo" type="text" class="form-control tooltips" placeholder="Numero Albo" value="<?php echo $row_00001['numero_albo']; ?>" data-container="body" data-placement="top" data-original-title="NUMERO ALBO"> </div>
                                                                    </div>

                                                                    <div class="row" style="margin-bottom:10px;">

                                                                        <div class="col-md-4">
                                                                            <input name="<?=$tabellaProfessionista?>_txt_cellulare" id="<?=$tabellaProfessionista?>_txt_cellulare" type="text" class="form-control tooltips" placeholder="Cellulare" value="<?php echo $row_00001['cellulare']; ?>" data-container="body" data-placement="top" data-original-title="CELLULARE"> </div>

                                                                        <div class="col-md-4" style="padding: 0px;">
                                                                            <div class="input-group">
                                                                                <span class="input-group-addon" style="background-color: #fff;"><a href="tel:<?=$row_00001['telefono']?>" target="_blank"><i class="fa fa-phone-square font-grey-mint"></i></a></span>
                                                                                <input name="<?=$tabellaProfessionista?>_txt_telefono" id="<?=$tabellaProfessionista?>_txt_telefono" type="text" class="form-control tooltips" placeholder="Telefono" value="<?php echo $row_00001['telefono']; ?>" data-container="body" data-placement="top" data-original-title="TELEFONO"></div></div>

                                                                        <div class="col-md-4">
                                                                            <input name="<?=$tabellaProfessionista?>_txt_fax" id="<?=$tabellaProfessionista?>_txt_fax" type="text" class="form-control tooltips" placeholder="Fax" value="<?php echo $row_00001['fax']; ?>" data-container="body" data-placement="top" data-original-title="FAX"> </div>
                                                                    </div>

                                                                    <div class="row" style="margin-bottom:10px;">
                                                                        <div class="col-md-6" style="padding-right: 0px;">
                                                                            <div class="input-group">
                                                                                <span class="input-group-addon" style="background-color: #fff;"><i class="fa fa-globe font-grey-mint"></i></span>
                                                                                <input name="<?=$tabellaProfessionista?>_txt_web"  id="<?=$tabellaProfessionista?>_txt_web" type="text" class="form-control tooltips" placeholder="Sito Web" value="<?php echo strtolower($row_00001['web']); ?>" data-container="body" data-placement="top" data-original-title="SITO WEB">
                                                                                <span class="input-group-btn"><a href="http://<?php echo $web; ?>" class="btn grey-mint" target="_blank"> Vedi <i class="fa fa-external-link"></i></a>
                                                                                </span>
                                                                            </div></div>
                                                                        <div class="col-md-6">
                                                                            <!-- INIZIO /btn-group -->
                                                                            <div class="input-group">
                                                                                <span class="input-group-addon" style="background-color: #fff;"><i class="fa fa-envelope font-grey-mint"></i></span>
                                                                                <input name="<?=$tabellaProfessionista?>_txt_email" id="<?=$tabellaProfessionista?>_txt_email" type="text" class="form-control tooltips" placeholder="Email" value="<?php echo strtolower($row_00001['email']); ?>" data-container="body" data-placement="top" data-original-title="EMAIL">
                                                                                <!--<div class="input-group-btn">
                                                                                    <button type="button" class="btn grey-mint dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Azioni
                                                                                        <i class="fa fa-angle-down"></i>
                                                                                    </button>
                                                                                    <ul class="dropdown-menu pull-right">
                                                                                        <li>
                                                                                            <a href="tab#tab_invia_email_presentazione"><i class="fa fa-envelope font-grey-mint"></i> Invia Email di Presentazione</a>
                                                                                        </li>
                                                                                        <li>
                                                                                            <a href="#tab_invia_documentazione"><i class="fa fa-file-pdf-o font-grey-mint"></i> Invia Documentazione </a>
                                                                                        </li>

                                                                                    </ul>
                                                                                </div>-->

                                                                            </div>
                                                                            <!-- FINE /btn-group -->
                                                                        </div>
                                                                    </div>

                                                                    <?php if($id_professionista_presente==0 && !$richiestaReadonlyCommerciale){?>
                                                                    <button id="cercaProfessionista" type="button" class="btn btn-icon green-jungle" alt="NUOVA PROFESSIONISTA" title="NUOVA PROFESSIONISTA"><i class="fa fa-plus"></i> Aggiungi Professionista</a></button>
                                                                    <?php } ?>
                                                                    
                                                                    <?php if($id_professionista_presente>0 && !$richiestaReadonly && !$richiestaReadonlyCommerciale){?>
                                                                    <button id="cambiaProfessionista" type="button" class="btn btn-icon blue" alt="CAMBIA/AGGIUNGI PROFESSIONISTA" title="CAMBIA/AGGIUNGI PROFESSIONISTA"><i class="fa fa-search"></i> Cambia/Aggiungi Professionista</a></button>
                                                                    <?php } ?>
                                                                    
                                                                    <!-- INIZIO ORDINE -->
                                                                    <?php
                                                                    
                                                                    if($idCalendario_daPassare>0 && $conta_preventivi>0 && !$richiestaReadonlyCommerciale) {
                                                                        $sql_0006 = "SELECT id, codice, importo, imponibile "
                                                                                . "FROM lista_preventivi "
                                                                                . "WHERE id_calendario= '" . $idCalendario_daPassare . "' AND stato='In Attesa' ORDER BY dataagg DESC";
                                                                        $row_0006 = $dblink->get_row($sql_0006,true);
                                                                        ?>
                                                                        <div class="form-actions right">
                                                                            <div class="row">
                                                                                <div class="col-md-offset-3 col-md-9">
                                                                                    <a href="<?=BASE_URL?>/moduli/preventivi/inviaPrev.php?idPrev=<?=$row_0006['id']?>" class="btn btn-icon btn-outline yellow" data-target="#ajax" data-url="<?=BASE_URL?>/moduli/preventivi/inviaPrev.php?idPrev=<?=$row_0006['id']?>" data-toggle="modal" title="INVIA" alt="INVIA"><i class="fa fa-paper-plane"></i> INVIA MAIL</a>
                                                                                    <button type="submit" class="btn btn-icon green-jungle"><i class="fa fa-check"></i> Salva</button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <hr>
                                                                        <?php
                                                                        
                                                                        $idPreventivo_daPassare = $row_0006['id'];
                                                                        
                                                                        $sql_0022 = "SELECT id_prodotto
                                                                        FROM lista_preventivi_dettaglio
                                                                        WHERE id_calendario= '" . $idCalendario_daPassare . "'  AND id_preventivo= '" . $row_0006['id'] . "' AND id_prodotto!='0' ORDER BY dataagg DESC";
                                                                        $row_0022 = $dblink->get_row($sql_0022,true);
                                                                                //stampa_table_datatables_responsive($sql_0001, 'Ordini', '', 'grey');
                                                                        
                                                                        if(!$livelloCommerciale){
                                                                            $titoloOrdiniInCorso = "Ordine: ".$row_0006['id']." - Imponibile: ".$row_0006['imponibile']." &euro; - Totale: ".round($row_0006['imponibile']*1.22, 2)." &euro;";
                                                                        }else{
                                                                            $titoloOrdiniInCorso = "Elenco prodotti di interesse - Totale prodotti: ".$row_0006['imponibile']."";
                                                                        }
                                                                        
                                                                        if($livelloAdmin){
                                                                            $livelloCommercialeTipo = 'amministratore';
                                                                        }else{
                                                                            $livelloCommercialeTipo = 'commerciale';
                                                                        }
                                                                        
                                                                        
                                                                        $sql_0007 = "SELECT @i:=@i+1 AS iterator,   
                                                                                if('".$livelloCommercialeTipo."'='amministratore',CONCAT('<a class=\"btn btn-circle btn-icon-only red btn-outline\" onClick=\"return eliminaDettaglioPreventivo(',id,',',id_preventivo,');\" href=\"cancella.php?tbl=lista_preventivi_dettaglio&id=',id,'&idPreventivo=',id_preventivo,'\" title=\"ELIMINA\" alt=\"ELIMINA\"><i class=\"fa fa-trash\"></i></a>') ,
                                                                                if(@i>1,CONCAT('<a class=\"btn btn-circle btn-icon-only red btn-outline\" onClick=\"return eliminaDettaglioPreventivo(',id,',',id_preventivo,');\" href=\"cancella.php?tbl=lista_preventivi_dettaglio&id=',id,'&idPreventivo=',id_preventivo,'\" title=\"ELIMINA\" alt=\"ELIMINA\"><i class=\"fa fa-trash\"></i></a>'),'')) AS 'elimina',
                                                                                   id, id_prodotto as nome_prodotto, prezzo_prodotto AS euro, quantita as qta, id_provvigione AS partner "
                                                                                . "FROM lista_preventivi_dettaglio as p, (SELECT @i:=0) as foo "
                                                                                . "WHERE id_calendario= " . $idCalendario_daPassare . " AND stato='In Attesa' ORDER BY id ASC";
                                                                                //stampa_table_datatables_responsive($sql_0001, 'Ordini', '', 'grey');
                                                                                stampa_table_static_basic_input('lista_preventivi_dettaglio', $sql_0007, '', $titoloOrdiniInCorso, 'green');
                                                                        ?>
                                                                            <?php if((($id_professionista_presente>=0 && $livelloCommerciale) OR ($id_professionista_presente>=0 && !$livelloCommerciale)) && !$richiestaReadonly){?>
                                                                            <a href="salva.php?idCalendario=<?=$idCalendario_daPassare?>&idPrev=<?=$row_0006['id']?>&fn=NuovoDettaglioOrdineProfessionista" class="btn btn-icon green-jungle" alt="NUOVO ORDINE" title="NUOVO ORDINE" style="margin-right: 100px"><i class="fa fa-plus"></i> AGGIUNGI PRODOTTO</a>
                                                                            <?php }
                                                                            if((($id_professionista_presente>=0 && $livelloCommerciale) OR ($id_professionista_presente>=0 && !$livelloCommerciale)) && (($row_0022['id_prodotto']>0 && $livelloCommerciale) OR ($row_0022['id_prodotto']>0 && !$livelloCommerciale)) && !$richiestaReadonly){?>
                                                                            <a href="salva.php?idPreventivo=<?=$row_0006['id']?>&idCalendario=<?=$idCalendario_daPassare?>&fn=preventivoVenduto" class="btn btn-icon blue-steel" alt="ISCRITTO" title="ISCRITTO" style="margin-right: 10px"><i class="fa fa-check"></i> ISCRITTO</a>
                                                                            <a href="salva.php?idPreventivo=<?=$row_0006['id']?>&idCalendario=<?=$idCalendario_daPassare?>&fn=preventivoNegativo" class="btn btn-icon red-intense"  id='richiestaNegativa2' alt="NEGATIVO" title="NEGATIVO"><i class="fa fa-close"></i> NEGATIVO</a>
                                                                            <?php } ?>
                                                                            <hr>
                                                                        <?php
                                                                    }else if($richiestaReadonlyCommerciale){
                                                                        
                                                                        echo "<hr>";
                                                                        
                                                                        $sql_0080 = "SELECT nome_prodotto, prezzo_prodotto AS euro, quantita as qta, id_provvigione AS partner "
                                                                                . "FROM lista_preventivi_dettaglio "
                                                                                . "WHERE id_calendario= '" . $idCalendario_daPassare . "' AND stato='In Attesa' ORDER BY dataagg DESC";;
                                                                          //stampa_table_datatables_responsive($sql_0001, 'Ordini', '', 'grey');
                                                                          stampa_table_static_basic($sql_0080, '', 'Elenco Prodotti Richiesti', 'green');
                                                                        
                                                                    }
                                                                    
                                                                    if((($id_professionista_presente>=0 && $livelloCommerciale) OR ($id_professionista_presente>=0 && !$livelloCommerciale))){
                                                                        
                                                                        if(!$richiestaReadonlyCommerciale && $richiestaReadonly){
                                                                        ?>
                                                                        <div class="form-actions right">
                                                                            <div class="row">
                                                                                <div class="col-md-offset-3 col-md-9">
                                                                                    <a href="<?=BASE_URL?>/moduli/preventivi/inviaPrev.php?idPrev=<?=$row_0006['id']?>" class="btn btn-icon btn-outline yellow" data-target="#ajax" data-url="<?=BASE_URL?>/moduli/preventivi/inviaPrev.php?idPrev=<?=$row_0006['id']?>" data-toggle="modal" title="INVIA" alt="INVIA"><i class="fa fa-paper-plane"></i> INVIA MAIL</a>
                                                                                    <button type="submit" class="btn btn-icon green-jungle"><i class="fa fa-check"></i> Salva</button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <hr>
                                                                        <?php
                                                                        }
                                                                        
                                                                        $sql_0008 = "SELECT
                                                                        IF(LCASE('".$_SESSION['livello_utente']."') LIKE 'amministratore' OR LCASE('".$_SESSION['livello_utente']."') LIKE 'betaadmin',CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"".BASE_URL."/moduli/preventivi/dettaglio.php?tbl=lista_preventivi&id=',id,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>'),
                                                                            CONCAT('<a class=\"btn btn-circle btn-icon-only red btn-outline\" href=\"".BASE_URL."/moduli/preventivi/printPreventivoPDF.php?id=',`id`,'&idA=',id_area,'\" TARGET=\"_BLANK\" title=\"STAMPA\" alt=\"STAMPA\"><i class=\"fa fa-file-pdf-o\"></i></a>')) AS 'fa-search',
                                                                            IF(codice LIKE 'xxx',id,CONCAT(codice,'/',sezionale)) AS codice, 
                                                                            IF(data_iscrizione LIKE '0000%', dataagg, data_iscrizione) AS data, 
                                                                            CONCAT((SELECT GROUP_CONCAT(lista_preventivi_dettaglio.nome_prodotto,' (', lista_preventivi_dettaglio.codice_prodotto ,')' SEPARATOR '<br>') FROM lista_preventivi_dettaglio WHERE lista_preventivi_dettaglio.id_preventivo = lista_preventivi.id),'<br>') AS Prodotti, imponibile, stato "
                                                                          . "FROM lista_preventivi "
                                                                          . "WHERE ((id_professionista= '" . $id_professionista_presente . "' AND id_professionista!=0) OR (id_calendario = '".$idCalendario_daPassare."' AND id_professionista=0)) AND stato!='In Attesa' ORDER BY dataagg DESC";
                                                                          //stampa_table_datatables_responsive($sql_0001, 'Ordini', '', 'grey');
                                                                          stampa_table_static_basic($sql_0008, '', 'Storico Ordini', 'grey');
                                                                    }
                                                                    ?>

                                                                  <!-- FINE ORDINE -->

                                                                </div>
                                                                <!-- END PERSONAL INFO TAB -->
                                                                <div class="tab-pane" id="tab_azienda">
                                                                    <!-- START AZIENDA INFO TAB -->
                                                                    <div class="row" style="margin-bottom:10px;">
                                                                        <div class="col-md-6">
                                                                            <input name="lista_aziende_txt_ragione_sociale" id="lista_aziende_txt_ragione_sociale" type="text" class="form-control tooltips" placeholder="Ragione Sociale" value="<?php echo $row_00002['ragione_sociale']; ?>" data-container="body" data-placement="top" data-original-title="RAGIONE SOCIALE" <?=($id_azienda_presente>0 ? (strlen($row_00002['ragione_sociale'])>0 ? "readonly" : "") : "readonly")?>> </div>
                                                                        <div class="col-md-6">
                                                                            <?php if($id_azienda_presente>0 && strlen($row_00002['forma_giuridica'])==0){ ?>
                                                                            <?=print_select_static(array("SS"=>"SS", "SNC"=>"SNC", "SAS"=>"SAS", "SRL"=>"SRL", "SPA"=>"SPA", "SAPA"=> "SAPA", "Soc. Coop."=>"Soc. Coop.", "Ditta Individuale"=>"Ditta Individuale", "Libero Professionista"=>"Libero Professionista"), "lista_aziende_txt_forma_giuridica", $row_00002['forma_giuridica'], '', false, 'select_forma_giuridica tooltips', 'placeholder="Forma Giuridica" data-container="body" data-placement="top" data-original-title="FORMA GIURIDICA"',"Selezionare Forma Giuridica") ?>
                                                                            <?php }else{ ?>
                                                                            <input name="lista_aziende_txt_forma_giuridica" id="lista_aziende_txt_forma_giuridica" type="text" class="form-control tooltips" placeholder="Forma Giuridica" value="<?php echo $row_00002['forma_giuridica']; ?>"  <?=($id_azienda_presente>0 ? (strlen($row_00002['forma_giuridica'])>0 ? "readonly" : "") : "readonly")?>>
                                                                            <?php } ?></div>
                                                                    </div>

                                                                    <div class="row" style="margin-bottom:10px;">
                                                                        <div class="col-md-6">
                                                                            <input name="lista_aziende_txt_partita_iva" id="lista_aziende_txt_partita_iva" type="text" class="form-control tooltips" placeholder="Partita Iva" value="<?php echo strtoupper($row_00002['partita_iva']); ?>" data-container="body" data-placement="top" data-original-title="PARTITA IVA" <?=($id_azienda_presente>0 ? (strlen($row_00002['partita_iva'])>0 ? "readonly" : "") : "readonly")?>> </div>
                                                                        <div class="col-md-6">
                                                                            <input name="lista_aziende_txt_codice_fiscale" id="lista_aziende_txt_codice_fiscale" type="text" class="form-control tooltips" placeholder="Codice Fiscale" value="<?php echo strtoupper($row_00002['codice_fiscale']); ?>" data-container="body" data-placement="top" data-original-title="CODICE FISCALE" <?=($id_azienda_presente>0 ? (strlen($row_00002['codice_fiscale'])>0 ? "readonly" : "") : "readonly")?>> </div>
                                                                    </div>

                                                                    <div class="row" style="margin-bottom:10px;">
                                                                        <div class="col-md-4">
                                                                            <input name="lista_aziende_txt_indirizzo" id="lista_aziende_txt_indirizzo" type="text" class="form-control tooltips" placeholder="Indirizzo" value="<?php echo $row_00002['indirizzo']; ?>" data-container="body" data-placement="top" data-original-title="INDIRIZZO" <?=($id_azienda_presente>0 ? "" : "readonly")?>> </div>
                                                                        <div class="col-md-2">
                                                                            <input name="lista_aziende_txt_cap" id="lista_aziende_txt_cap" type="text" class="form-control tooltips" placeholder="CAP" value="<?php echo $row_00002['cap']; ?>" data-container="body" data-placement="top" data-original-title="CAP" <?=($id_azienda_presente>0 ? "" : "readonly")?>> </div>
                                                                        <div class="col-md-2">
                                                                            <input name="lista_aziende_txt_citta" id="lista_aziende_txt_citta" type="text" class="form-control tooltips" placeholder="Cittá" value="<?php echo $row_00002['citta']; ?>" data-container="body" data-placement="top" data-original-title="CITTÁ" <?=($id_azienda_presente>0 ? "" : "readonly")?>> </div>
                                                                        <div class="col-md-2">
                                                                            <?=print_select2("SELECT sigla_province as valore, sigla_province AS nome FROM lista_province", "lista_aziende_txt_provincia", $row_00002['provincia'], "", false, 'tooltips select_prov', 'data-container="body" data-placement="top" data-original-title="PROVINCIA"'); ?>
                                                                            <!--<input name="lista_aziende_txt_provincia" id="lista_aziende_txt_provincia" type="text" class="form-control tooltips" placeholder="Provincia" value="<?php echo $row_00002['provincia']; ?>" data-container="body" data-placement="top" data-original-title="PROVINCIA" <?=($id_azienda_presente>0 ? "" : "readonly")?>>--> </div>
                                                                        <div class="col-md-2">
                                                                            <input name="lista_aziende_txt_nazione" id="lista_aziende_txt_nazione" type="text" class="form-control tooltips" placeholder="Nazione" value="<?php echo $row_00002['nazione']; ?>" data-container="body" data-placement="top" data-original-title="NAZIONE" <?=($id_azienda_presente>0 ? "" : "readonly")?>> </div>
                                                                    </div>

                                                                    <div class="row" style="margin-bottom:10px;">
                                                                        <div class="col-md-4">
                                                                            <div class="input-group">
                                                                                <span class="input-group-addon" style="background-color: #fff;"><i class="fa fa-phone-square font-grey-mint"></i></span>
                                                                                <input name="lista_aziende_txt_telefono" id="lista_aziende_txt_telefono" type="text" class="form-control tooltips" placeholder="Telefono" value="<?php echo $row_00002['telefono']; ?>" data-container="body" data-placement="top" data-original-title="TELEFONO" <?=($id_azienda_presente>0 ? "" : "readonly")?>></div></div>
                                                                        <div class="col-md-4">
                                                                            <input name="lista_aziende_txt_fax" id="lista_aziende_txt_fax" type="text" class="form-control tooltips" placeholder="Fax" value="<?php echo $row_00002['fax']; ?>" data-container="body" data-placement="top" data-original-title="FAX" <?=($id_azienda_presente>0 ? "" : "readonly")?>> </div>
                                                                        <div class="col-md-4">
                                                                            <input name="lista_aziende_txt_cellulare" id="lista_aziende_txt_cellulare" type="text" class="form-control tooltips" placeholder="Cellulare" value="<?php echo $row_00002['cellulare']; ?>" data-container="body" data-placement="top" data-original-title="CELLULARE" <?=($id_azienda_presente>0 ? "" : "readonly")?>> </div>
                                                                    </div>

                                                                    <div class="row" style="margin-bottom:10px;">
                                                                        <div class="col-md-6">
                                                                            <div class="input-group">
                                                                                <span class="input-group-addon" style="background-color: #fff;"><i class="fa fa-globe font-grey-mint"></i></span>
                                                                                <input name="lista_aziende_txt_web" id="lista_aziende_txt_web" type="text" class="form-control tooltips" placeholder="Sito Web" value="<?php echo strtolower($row_00002['web']); ?>" data-container="body" data-placement="top" data-original-title="SITO WEB" <?=($id_azienda_presente>0 ? "" : "readonly")?>>
                                                                                <span class="input-group-btn"><a href="<?php echo $row_00002['web']; ?>" class="btn grey-mint" target="_blank"> Vedi <i class="fa fa-external-link"></i></a>
                                                                                </span>
                                                                            </div></div>
                                                                        <div class="col-md-6">
                                                                            <!-- INIZIO /btn-group -->
                                                                            <div class="input-group">
                                                                                <span class="input-group-addon" style="background-color: #fff;"><i class="fa fa-envelope font-grey-mint"></i></span>
                                                                                <input name="lista_aziende_txt_email" id="lista_aziende_txt_email" type="text" class="form-control tooltips" placeholder="Email" value="<?php echo strtolower($row_00002['email']); ?>" data-container="body" data-placement="top" data-original-title="EMAIL" <?=($id_azienda_presente>0 ? "" : "readonly")?>>
                                                                                <!--<div class="input-group-btn">
                                                                                    <button type="button" class="btn grey-mint dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Azioni
                                                                                        <i class="fa fa-angle-down"></i>
                                                                                    </button>
                                                                                    <ul class="dropdown-menu pull-right">
                                                                                        <li>
                                                                                            <a href="tab#tab_invia_email_presentazione"><i class="fa fa-envelope font-grey-mint"></i> Invia Email di Presentazione</a>
                                                                                        </li>
                                                                                        <li>
                                                                                            <a href="#tab_invia_documentazione"><i class="fa fa-file-pdf-o font-grey-mint"></i> Invia Documentazione </a>
                                                                                        </li>

                                                                                    </ul>
                                                                                </div>-->

                                                                            </div>
                                                                            <!-- FINE /btn-group -->
                                                                        </div>
                                                                    </div>
                                                                    <div class="row" style="margin-bottom:10px;">
                                                                        <div class="col-md-6">
                                                                            <input name="lista_aziende_txt_settore" id="lista_aziende_txt_settore" type="text" class="form-control tooltips" placeholder="Settore" value="<?php echo $row_00002['settore']; ?>" data-container="body" data-placement="top" data-original-title="SETTORE" <?=($id_azienda_presente>0 ? "" : "readonly")?>></div>
                                                                        <div class="col-md-6">
                                                                            <input name="lista_aziende_txt_categoria" id="lista_aziende_txt_categoria" type="text" class="form-control tooltips" placeholder="Categoria" value="<?php echo $row_00002['categoria']; ?>" data-container="body" data-placement="top" data-original-title="CATEGORIA" <?=($id_azienda_presente>0 ? "" : "readonly")?>> </div>
                                                                    </div>
                                                                    <!-- END AZIENDA INFO TAB -->
                                                                    <?php if($id_professionista_presente>0 && $id_azienda_presente>=0){?>
                                                                    <button style="margin-right: 20px;" id="cercaAziendaAggiungi" type="button" class="btn btn-icon green-jungle" alt="NUOVI DATI FATTURAZIONE" title="NUOVI DATI FATTURAZIONE"><i class="fa fa-plus"></i> Aggiungi Dati Fatturazione</a>
                                                                    <?php } ?>
                                                                    <?php if($id_azienda_presente>0){?>
                                                                    <button id="cercaAzienda" type="button" class="btn btn-icon blue" alt="CERCA DATI FATTURAZIONE" title="CERCA DATI FATTURAZIONE"><i class="fa fa-search"></i> Cerca Dati Fatturazione</a>
                                                                    <?php } ?>

                                                                </div>

                                                                <div class="tab-pane" id="tab_fatture">
                                                                <?php
                                                                $sql_0018 = "SELECT IF(LCASE('".$_SESSION['livello_utente']."') LIKE 'amministratore', CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"" . BASE_URL . "/moduli/fatture/dettaglio.php?tbl=lista_fatture&id=',id,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>'),
                                                                    CONCAT('<a class=\"btn btn-circle btn-icon-only red btn-outline\" href=\"".BASE_URL."/moduli/fatture/printFatturaPDF.php?idFatt=',`id`,'&idA=',id_area,'\" TARGET=\"_BLANK\" title=\"STAMPA\" alt=\"STAMPA\"><i class=\"fa fa-file-pdf-o\"></i></a>')) AS 'fa-search',
                                                                codice, data_creazione, data_scadenza, imponibile, stato "
                                                                        . "FROM lista_fatture "
                                                                        . "WHERE id_professionista= '" . $id_professionista_presente . "' AND stato!='Accorpata' ORDER BY codice DESC";
                                                                stampa_table_datatables_responsive($sql_0018, 'Fatture', '', 'grey');
                                                                ?>

                                                                </div>
                                                                <div class="tab-pane" id="tab_corsi">
                                                                    <?php
                                                                    
                                                                    echo '<div class="row"><div class="col-md-12 col-sm-12">';
                                                                    /*(SELECT CONCAT(cognome, ' ', nome)  FROM lista_professionisti WHERE id = id_professionista) AS 'Professionista',*/
                                                                    $sql_0005 = "SELECT
                                                                    (SELECT nome_prodotto FROM lista_corsi WHERE id = id_corso) AS 'Corso',
                                                                    data_inizio_iscrizione, data_fine_iscrizione,
                                                                    DATE(data_inizio) AS 'Data Inizio', DATE(data_fine) AS 'Data Fine', 
                                                                    (SELECT nome FROM lista_classi WHERE id = id_classe) AS 'Classe',
                                                                    stato
                                                                    FROM lista_iscrizioni WHERE id_professionista = '$id_professionista_presente' ORDER BY dataagg DESC";
                                                                    //stampa_table_static_basic($sql_0005, '', 'Iscrizioni Corsi', 'green-meadow', 'fa fa-university');
                                                                    stampa_table_datatables_responsive($sql_0005, 'Iscrizioni Corsi', 'tabella_base5', 'green-meadow', 'fa fa-university');
                                                                    echo '</div></div>';
                                                                    
                                                                    echo '<div class="row"><div class="col-md-12 col-sm-12">';
                                                                    $sql_0007 = "SELECT dataagg, username, passwd, (SELECT nome FROM lista_classi WHERE id = id_classe ) AS Classe, 
                                                                    IF(stato LIKE '%Elimi%',CONCAT('<span class=\"btn sbold uppercase btn-outline red-flamingo\">',stato,'</span>'),CONCAT('<span class=\"btn sbold uppercase btn-outline blue\">',stato,'</span>')) AS 'Stato',
                                                                    CONCAT('<a class=\"btn btn-circle btn-icon-only purple-studio btn-outline\" href=\"salva.php?fn=resettaPassword&tbl=lista_password&id=',id_professionista,'\" onclick=\"javascript: return confirm(\'Si è sicuri di eseguire il reset della password ?\')\" title=\"RESETTA PASSWORD\" alt=\"RESETTA PASSWORD\"><i class=\"fa fa-key\"></i></a>') AS 'fa-key' 
                                                                    FROM lista_password WHERE id_professionista = '" . $id_professionista_presente."' ORDER BY dataagg DESC";
                                                                    stampa_table_static_basic($sql_0007, '', 'Credenziali Password', '', 'fa fa-mail');
                                                                    echo '</div></div>';
                                                                    ?>
                                                                </div>
                                                                
                                                            </div>
                                                            <?php if(!$richiestaReadonlyCommerciale){ ?>
                                                            <div class="form-actions right">
                                                                <div class="row">
                                                                    <div class="col-md-offset-3 col-md-9">
                                                                        <button type="submit" class="btn btn-icon green-jungle"><i class="fa fa-check"></i> Salva</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <?php } ?>

                                                            <input type="hidden" name="calendario_txt_id" id="calendario_txt_id" value="<?php echo $idCalendario_daPassare; ?>" class="form-control" readonly/>
                                                            <input type="hidden" name="lista_professionisti_txt_id" id="lista_professionisti_txt_id" value="<?php echo $id_professionista_presente; ?>" class="form-control" readonly/>
                                                            <input type="hidden" name="lista_aziende_txt_id" id="lista_aziende_txt_id" value="<?php echo $id_azienda_presente; ?>" class="form-control" readonly/>
                                                            <input type="hidden" name="lista_preventivi_txt_id" id="lista_preventivi_txt_id" value="<?php echo $idAutoPreventivoCampagna; ?>" class="form-control" readonly/>
                                                            <!--<input type="hidden" name="codice" id="codice" value="<?php echo $codice; ?>" class="form-control" readonly/>-->
                                                            <input type="hidden" name="dataagg" id="dataagg" value="<?php echo date("Y-m-d H:i:s"); ?>" class=form-control" readonly/>
                                                            <input type="hidden" name="scrittore" id="scrittore" value="<?php echo $_SESSION['cognome_nome_utente']; ?>" class="form-control" readonly/>
                                                            <!--<input type="hidden" name="wIdCommessaTLM" id="wIdCommessaTLM" value="<?php echo $_SESSION['idCommessaTLM']; ?>" class="form-control" />
                                                            <input type="hidden" name="wIdProcessoTLM" id="wIdProcessoTLM" value="<?php echo $_SESSION['idProcessoTLM']; ?>" class="form-control" />-->
                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- fine form qui-->
                                            </div>
                                        </div>

                                </div>
                                <div class="col-md-4 col-sm-4">
                                    <div class="portlet box yellow-crusta">
                                        <div class="portlet-title">
                                            <div class="caption font-light">
                                                <i class="fa fa-clock-o"></i>
                                                <span class="caption-subject bold uppercase">Ultima Richiesta</span>
                                                <span class="caption-helper"> </span>
                                            </div>
                                        </div>
                                        <div class="portlet-body" style="padding:25px;">
                                            <!-- START TIPO CHIUSURA-->
                                            <div class="row" style="margin-top: -25px;">
                                                <div class="col-md-12">
                                                    <div class="form-group " style="">
                                                        <label class="control-label font-dark bold uppercase">STATO</label>
                                                        <?php if(!$richiestaReadonly && !$richiestaReadonlyCommerciale) {
                                                            print_bs_select("SELECT nome as valore, nome, colore_sfondo as colore FROM lista_richieste_stati WHERE stato='Attivo' $where_lista_richieste_stati", "calendario_txt_stato", $row_00003['stato'], "", true);
                                                        }else{
                                                            print_input("calendario_txt_stato", $row_00003['stato'],"Stato",true);
                                                        } ?>
                                                        <!--<select class="bs-select form-control" data-show-subtext="true"  id="calendario_txt_tipoChiusura" name="calendario_txt_tipoChiusura" >
                                                            <option value="<?= $row_00003['stato'] ?>"data-content="<span class='label bg-purple-studio bg-font-purple-studio bold' > <?= $row_00003['stato'] ?> </span>" selected><?= $row_00003['stato'] ?></option>
                                                            <!--<option  value="Venduto" data-content="<span class='label bg-blue bg-font-blue bold'>Venduto </span>">Venduto</option>
                                                            <option  value="Appuntamento" data-content="<span class='label bg-green-jungle bg-font-green-jungle bold'>Appuntamento </span>">Appuntamento</option>
                                                            <option  value="Documentazione" data-content="<span class='label bg-yellow-lemon bg-font-yellow-lemon bold'>Documentazione </span>">Documentazione</option>
                                                            <option  value="Contatto" data-content="<span class='label bg-green bg-font-green bold'>Contatto Telefonico </span>">Contatto Telefonico</option>
                                                            <option  value="Richiamare" data-content="<span class='label bg-yellow-gold bg-font-yellow-gold bold'>Richiamare </span>">Richiamare</option>
                                                            <option  value="Non Interessa" data-content="<span class='label bg-red-thunderbird bg-font-red-thunderbird bold'>Non Interessa </span>">Non Interessa</option>-->
                                                        <!--</select>-->
                                                    </div>
                                                </div>
                                                <div class="col-md-12" style="margin-top: -10px; margin-bottom: -10px;">
                                                    <div class="form-group " style="">
                                                        <label class="control-label font-dark bold">Campagna</label>
                                                        <?php if($livelloAdmin){ ?>
                                                        <?php   //print_bs_select("SELECT nome as valore, nome, colore_sfondo as colore FROM lista_tipo_marketing WHERE stato='Attivo' ORDER BY nome", "calendario_txt_tipo_marketing", $row_00003['tipo_marketing'], "", true); ?>
                                                        <?php   print_select2("SELECT id as valore, nome FROM lista_campagne WHERE stato='Attiva' OR stato='In Corso' ORDER BY nome", "calendario_txt_id_campagna", $row_00003['id_campagna'], "", true); ?>
                                                        <?php }else{ ?>
                                                                <?php //print_input("calendario_txt_tipo_marketing", $row_00003['tipo_marketing'],"Canale Marketing",true, false); ?>
                                                                <?=print_hidden("calendario_txt_id_campagna", $row_00003['id_campagna'],true); ?>
                                                                <br><span class="col-md-12  btn uppercase btn-outline grey-mint"><?=ottieniNomeCampagna($row_00003['id_campagna'])?></span>
                                                        <?php } ?>
                                                        <!--<select class="bs-select form-control" data-show-subtext="true"  id="calendario_txt_tipoChiusura" name="calendario_txt_tipoChiusura" >
                                                            <option  value="E_ABBING_17" data-content="<span class='label bg-green-jungle bg-font-green-jungle bold'>E_ABBING_17 </span>" selected>E_ABBING_17</option>
                                                          <!--<option  value="Documentazione" data-content="<span class='label bg-yellow-lemon bg-font-yellow-lemon bold'>Documentazione </span>">Documentazione</option>
                                                            <option  value="Contatto" data-content="<span class='label bg-green bg-font-green bold'>Contatto Telefonico </span>">Contatto Telefonico</option>
                                                            <option  value="Richiamare" data-content="<span class='label bg-yellow-gold bg-font-yellow-gold bold'>Richiamare </span>">Richiamare</option>
                                                            <option  value="Non Interessa" data-content="<span class='label bg-red-thunderbird bg-font-red-thunderbird bold'>Non Interessa </span>">Non Interessa</option>-->
                                                        <!--</select>-->

                                                    </div>
                                                </div>

                                            </div>
                                            <!-- END TIPO CHIUSURA-->
                                            <!-- START DATA - ORA-->
                                            <div class="row" style="margin-bottom: -20px;">
                                                <div class="col-md-6">
                                                    <div class="form-group" style="padding-right:5px;">
                                                        <label class="control-label bold">Data</label>
                                                        <div class="input-icon">
                                                            <i class="fa fa-calendar"></i>
                                                            <input type="text" class="form-control <?=(($richiestaReadonly || $richiestaReadonlyCommerciale) ? "" : "date-picker")?> contatti-data-chiusura tooltips" placeholder="Data..."  id="calendario_txt_data" name="calendario_txt_data" data-container="body" data-placement="top" data-original-title="DATA " data-date-format="dd-mm-yyyy" value="<?= GiraDataOra($row_00003['data']) ?>" <?=(($richiestaReadonly || $richiestaReadonlyCommerciale) ? "readonly" : "")?>>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--/span-->
                                                <div class="col-md-6">
                                                    <div class="form-group" style="padding-left:5px;">
                                                        <label class="control-label bold">Ora</label>
                                                        <div class="input-group">
                                                            <input type="text" class="form-control timepicker timepicker-24-ora-inizio tooltips" id="calendario_txt_ora" name="calendario_txt_ora" data-container="body" data-placement="top" data-original-title="ORA" value="<?=$row_00003['ora']?>" <?=(($richiestaReadonly || $richiestaReadonlyCommerciale) ? "readonly" : "")?>>
                                                            <span class="input-group-btn">
                                                                <button class="btn default" type="button">
                                                                    <i class="fa fa-clock-o"></i>
                                                                </button>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--<div class="col-md-3">
                                                    <div class="form-group">
                                                        <label class="control-label">Costo</label>
                                                        <div class="input-group">
                                                            <input type="text" class="form-control tooltips"  id="calendario_txt_costo" name="calendario_txt_costo" value="95<?php //echo $row_00001['costo']; ?>" data-container="body" data-placement="top" data-original-title="COSTO">
                                                        </div>
                                                    </div>
                                                </div>-->

                                            </div>
                                            <!-- END DATA - ORA-->
                                            <hr>

                                            <!-- START DESCRIZIONE CHIUSURA-->
                                            <div class="row" style="margin-bottom:15px;">
                                                <div class="form-group">
                                                    <div class="col-md-12">
                                                    <textarea class="form-control" rows="10" placeholder="Note..."  id="calendario_txt_messaggio" name="calendario_txt_messaggio" <?=(($richiestaReadonly || $richiestaReadonlyCommerciale) ? "readonly" : "")?>><?=$row_00003['messaggio'] ?></textarea>
                                                        
                                                        <!--<textarea class="form-control" rows="10" placeholder="Note..."  id="calendario_txt_messaggio" name="calendario_txt_messaggio"><?=$row_00003['messaggio'] ?></textarea>-->
                                                        <input type="hidden" id="copiaNome" name="copiaNome" value="<?=$row_00003['campo_1']?>">
                                                        <input type="hidden" id="copiaCognome" name="copiaCognome" value="<?=$row_00003['campo_2']?>">
                                                        <input type="hidden" id="copiaCodiceFiscale" name="copiaCodiceFiscale" value="<?=$row_00003['campo_3']?>">
                                                        <input type="hidden" id="copiaTelefono" name="copiaTelefono" value="<?=$row_00003['campo_4']?>">
                                                        <input type="hidden" id="copiaEmail" name="copiaEmail" value="<?=$row_00003['campo_5']?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- END DESCRIZIONE CHIUSURA -->
                                            <div class="row" style="margin-bottom:0px;">
                                            <?php
                                            if($idCalendario_daPassare>0){
                                                $sql_0019 = "SELECT id, codice, importo, imponibile 
                                                FROM lista_preventivi 
                                                WHERE id_calendario= '" . $idCalendario_daPassare . "' AND stato='In Attesa' ORDER BY dataagg DESC";
                                                $row_0019 = $dblink->get_row($sql_0019,true);
                                            
                                                $sql_0021 = "SELECT id_prodotto
                                                FROM lista_preventivi_dettaglio
                                                WHERE id_calendario= '" . $idCalendario_daPassare . "'  AND id_preventivo= '" . $row_0019['id'] . "' AND id_prodotto!='0' ORDER BY dataagg DESC";
                                                $row_0021 = $dblink->get_row($sql_0021,true);
                                            }
                                            ?>
                                            
                                            <?php /*if($row_00003['etichetta']!="Nuova Richiesta"){ ?>
                                            <a href="salva.php?id=<?= $id ?>&fn=NuovaNotaProfessionista" class="btn btn-icon-only green-jungle" alt="NUOVA NOTA" title="NUOVA NOTA"><i class="fa fa-plus"></i></a>
                                            <?php }*/ ?>
                                            
                                            <?php if($_SESSION['livello_utente']!='assistenza' && (($id_professionista_presente>=0 && $livelloCommerciale && !$richiestaReadonlyCommerciale) OR ($id_professionista_presente>=0 && !$livelloCommerciale)) && (($row_0021['id_prodotto']>0 && $livelloCommerciale && !$richiestaReadonlyCommerciale) OR ($row_0021['id_prodotto']>0 && !$livelloCommerciale)) && ($statoAttuale=="Richiamare" || $statoAttuale=="Mai Contattato")){ ?>
                                            <a href="salva.php?idPreventivo=<?=$row_0019['id']?>&idCalendario=<?=$idCalendario_daPassare?>&fn=preventivoVenduto" class="btn btn-icon blue-steel" alt="ISCRITTO" title="ISCRITTO" style="margin-right: 10px"><i class="fa fa-check"></i> ISCRITTO</a>
                                            <a href="salva.php?idPreventivo=<?=$row_0019['id']?>&idCalendario=<?=$idCalendario_daPassare?>&fn=preventivoNegativo" id='richiestaNegativa1' class="btn btn-icon red-intense" alt="NEGATIVO" title="NEGATIVO"><i class="fa fa-close"></i> NEGATIVO</a>
                                            <hr>
                                                <?php if($id_professionista_presente>0){ ?>
                                                    <a href="salva.php?idCalendario=<?=$idCalendario_daPassare?>&fn=ripristinaContatto" class="btn btn-icon grey-mint" alt="RIPORTA A CONTATTO" title="RIPORTA A CONTATTO"><i class="fa fa-exclamation-circle"></i> RIPORTA A CONTATTO</a>
                                                    <hr>
                                                <?php } ?>
                                            <?php } ?>
                                            
                                            <?php if($richiestaReadonly){ ?>
                                            <button style="margin-bottom: 5px" onclick="window.location.href = '../calendario/nuovo_tab.php?idProf=<?=$id_professionista_presente?>'" type="button" class="btn btn-icon green-jungle"><i class="fa fa-plus"></i> Aggiungi Nuova Richiesta</button>
                                            <?php } ?>
                                            
                                            <?php if($richiestaReadonlyCommerciale && !$richiestaReadonly){ ?>
                                            <button style="margin-bottom: 5px" onclick="window.location.href = '../calendario/nuovo_tab.php?idProf=<?=$id_professionista_presente?>&idCal=<?=$idCalendario_daPassare?>'" type="button" class="btn btn-icon green-jungle"><i class="fa fa-plus"></i> Aggiungi Nota alla Richiesta</button>
                                            <?php } ?>
                                            
                                            <?php if($id_professionista_presente==0 && strlen($row_00003['campo_3'])==16  && !$richiestaReadonly && !$richiestaReadonlyCommerciale){ ?>
                                            <button style="margin-bottom: 5px" name="copiaValoriRichiestaInPartecipante" id="copiaValoriRichiestaInPartecipante" type="button" class="btn btn-icon blue" alt="COPIA DATI" title="COPIA DATI"><i class="fa fa-copy"></i> Copia Dati</button>
                                            <?php }else if($id_professionista_presente==0 && $row_00003['campo_3']==""  && !$richiestaReadonly && !$richiestaReadonlyCommerciale){ ?>
                                            <button style="margin-bottom: 5px" name="cercaCodiceUtentePartecipante" id="cercaCodiceUtentePartecipante" type="button" class="btn btn-icon blue" alt="CERCA" title="CERCA"><i class="fa fa-copy"></i> Cerca</button>
                                            <?php } ?>
                                            
                                            <?php if(($row_0019['id']==0 || $row_0021['id_prodotto']==false) && !$richiestaReadonly && !$richiestaReadonlyCommerciale){ ?>
                                            <button style="margin-bottom: 5px" name="chiudiRichiesta" id="chiudiRichiesta" onclick="window.location.href = '../calendario/salva.php?id=<?=$idCalendario_daPassare?>&idPrev=<?=$row_0019['id']?>&fn=chiudiRichiesta'" type="button" class="btn btn-icon yellow-gold" alt="CHIUDI RICHIESTA" title="CHIUDI RICHIESTA"><i class="fa fa-close"></i> Chiudi Richiesta</button>
                                            <?php } ?>
                                            
                                            <?php if(!$richiestaReadonly && $_SESSION['livello_utente']!='assistenza'){ ?>
                                            <?php if($id_agente_presente==$_SESSION['id_utente'] || !$livelloCommerciale){ ?><button name="trasferisciRichiesta" id="trasferisciRichiesta" type="button" class="btn btn-icon purple-studio" alt="TRASFERISCI RICHIESTA" title="TRASFERISCI RICHIESTA"><i class="fa fa-sign-in"></i> Trasferisci Richiesta</button><?php } ?>
                                            <?php if($id_agente_presente!=$_SESSION['id_utente'] && $livelloCommerciale){ ?><button name="prendiInCaricoRichiesta" id="prendiInCaricoRichiesta" type="button" onclick="javascript: prendiInCaricoRichesta(<?=$idCalendario_daPassare?>,<?=$id_agente_presente?>,<?=$_SESSION['id_utente']?>);" class="btn btn-icon purple-studio" alt="PRENDI IN CARICO RICHIESTA" title="PRENDI IN CARICO RICHIESTA"><i class="fa fa-sign-in"></i> Prendi In Carico Richiesta</button><?php } ?>
                                            <?php }else if(!$livelloCommerciale && $_SESSION['livello_utente']!='assistenza'){ ?>
                                                <button style="margin-bottom: 5px" name="trasferisciRichiesta" id="trasferisciRichiesta" type="button" class="btn btn-icon purple-studio" alt="TRASFERISCI RICHIESTA" title="TRASFERISCI RICHIESTA"><i class="fa fa-sign-in"></i> Trasferisci Richiesta</button>
                                            <?php } ?>
                                            </div>
                                            <!-- END CHIUSURA RAPIDA-->
                                        </div>  <!--end portlet body -->
                                    </div>
                                    <!--FINE PORTLET CHIUSURE -->
                                </div>
                            
                            </div>
                            <!-- END PROFILE CONTENT -->
                        </div>

                    </div>
                    </form>
                    <!-- FINE ROW TABELLA-->
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                        <?php
                        if($idCalendario_daPassare>0){
                            $sql_0020 = "SELECT data, ora, etichetta, REPLACE(messaggio,'\\n','<br>') AS messaggio, mittente, stato "
                                    . "FROM calendario "    
                                    . "WHERE ((id_professionista > 0 AND id_professionista= '" . $id_professionista_presente . "') OR LCASE(mittente)=LCASE('".$dblink->filter($row_00003['mittente'])."')) AND id!=".$idCalendario_daPassare." 
                                    ORDER BY dataagg DESC, id DESC LIMIT 0,100000";
                            stampa_table_datatables_responsive($sql_0020, 'Storico Richieste', '', 'grey');
                        }
                        ?>
                        </div>
                    </div>
                </div>
                <!-- END CONTENT BODY -->
            </div>
            <!-- END CONTENT -->
            <!-- BEGIN QUICK SIDEBAR -->

            <!-- END QUICK SIDEBAR -->
        </div>
        <!-- END CONTAINER -->
        <?=pageFooterCopy()?>
        <!--[if lt IE 9]>
        <script src="<?= BASE_URL ?>/assets/global/plugins/respond.min.js"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/excanvas.min.js"></script>
        <![endif]-->
        <!-- BEGIN CORE PLUGINS -->
        <script src="<?= BASE_URL ?>/assets/global/plugins/moment.min.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/jquery.min.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/js.cookie.min.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/bootstrap-toastr/toastr.min.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/typeahead/typeahead.bundle.min.js" type="text/javascript"></script>
        <!-- END CORE PLUGINS -->
        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <script src="<?= BASE_URL ?>/assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <!--<script src="<?= BASE_URL ?>/assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js" type="text/javascript"></script>-->

        <script src="<?= BASE_URL ?>/assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/clockface/js/clockface.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/jquery.pulsate.min.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/jquery.sparkline.min.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/bootstrap-summernote/summernote.min.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/bootstrap-wysihtml5/wysihtml5-0.3.0.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.js" type="text/javascript"></script>
        <!--<script src="<?= BASE_URL ?>/assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/jquery-validation/js/additional-methods.min.js" type="text/javascript"></script>-->
        <script src="<?= BASE_URL ?>/assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js" type="text/javascript"></script>

        <!--<script src="<?= BASE_URL ?>/assets/pages/scripts/form-input-mask.min.js" type="text/javascript"></script>-->
        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN THEME GLOBAL SCRIPTS -->
        <script src="<?= BASE_URL ?>/assets/global/scripts/app.min.js" type="text/javascript"></script>
        <!-- END THEME GLOBAL SCRIPTS -->
        <!-- BEGIN PAGE LEVEL SCRIPTS -->

        <script src="<?= BASE_URL ?>/assets/pages/scripts/components-select2.min.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/apps/scripts/dettaglio_tab.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/pages/scripts/ui-toastr.min.js" type="text/javascript"></script>
        <!-- END PAGE LEVEL SCRIPTS -->
        <!-- BEGIN THEME LAYOUT SCRIPTS -->
        <script src="<?= BASE_URL ?>/assets/apps/scripts/php.min.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/apps/scripts/utility.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/layouts/layout/scripts/layout.min.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/layouts/global/scripts/quick-sidebar.min.js" type="text/javascript"></script>
        <!-- END THEME LAYOUT SCRIPTS -->
        <script src="<?= BASE_URL ?>/assets/pages/scripts/ui-bootbox.min.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/moduli/anagrafiche/scripts/funzioni.js" type="text/javascript"></script>

        <div id="myModalCodiceFiscale" class="modal fade">
            <div class="modal-dialog">
               <div class="modal-content">
                 <!-- dialog body -->
                 <div class="modal-body">
                   <button type="button" class="close" data-dismiss="modal">&times;</button>
                   Cerca Utente per codice Utente o Codice Fiscale
                   <form class="form-horizontal form-bordered" enctype="multipart/form-data" id="idFromCercaCodiceUtente">
                   <div class="form-body">
                   <div class="form-group">
                   <label></label>
                   <div class="col-md-12">
                       <?=print_hidden("id_calendario", $idCalendario_daPassare)?>
                       <?=print_input("codice_utente", "", "Codice Utente o Codice Fiscale", false) ?>
                   </div></div></div></form>
                 </div>
                 <!-- dialog buttons -->
                 <div class="modal-footer"><button type="button" id="annullaButton" class="btn btn-primary red">ANNULLA</button><button type="button" id="okButton" class="btn btn-primary">CONFERMA</button></div>
               </div>
             </div>
        </div>

        <div id="myModalAggiungiAzienda" class="modal fade">
            <div class="modal-dialog">
               <div class="modal-content">
                 <!-- dialog body -->
                 <div class="modal-body">
                   <button type="button" class="close" data-dismiss="modal">&times;</button>
                   Cerca / Inserisci Azienda per Partita IVA
                   <form class="form-horizontal form-bordered" enctype="multipart/form-data" id="idFromAggiungiAzienda">
                   <div class="form-body">
                   <div class="form-group">
                   <label></label>
                   <div class="col-md-12">
                       <?=print_hidden("id_calendario", $idCalendario_daPassare)?>
                       <?=print_hidden("id_professionista", $id_professionista_presente)?>
                       <?=print_input("partita_iva", "", "Partita IVA", false) ?>
                   </div></div></div></form>
                 </div>
                 <!-- dialog buttons -->
                 <div class="modal-footer"><button type="button" id="annullaButtonAggiungiAzienda" class="btn btn-primary red">ANNULLA</button><button type="button" id="okButtonAggiungiAzienda" class="btn btn-primary">CONFERMA</button></div>
               </div>
             </div>
        </div>

        <div id="myModalCercaAzienda" class="modal fade">
            <div class="modal-dialog">
               <div class="modal-content">
                 <!-- dialog body -->
                 <div class="modal-body">
                   <button type="button" class="close" data-dismiss="modal">&times;</button>
                   Cerca / Inserisci Azienda per Partita IVA
                   <form class="form-horizontal form-bordered" enctype="multipart/form-data" method="POST" onsubmit="return false;" id="idFromCercaAzienda">
                   <div class="form-body">
                   <div class="form-group">
                   <label></label>
                   <div class="col-md-12">
                       <?=print_hidden("id_calendario", $idCalendario_daPassare)?>
                       <?=print_hidden("id_professionista", $id_professionista_presente)?>
                       <?=print_hidden("id_azienda", $id_azienda_presente)?>
                       <?=print_hidden("partita_iva", "")?>
                       <?=print_input("cerca_azienda", "", "Cerca Azienda", false) ?>
                   </div></div></div></form>
                    <b>IMPORTANTE:</b> Se non si trova l'azienda, inserire sempre e solo la<br /><b><i>PARTITA IVA</i></b> dell'azienda per poterla inserire nella banca dati.
                 </div>
                 <!-- dialog buttons -->
                 <div class="modal-footer"><button type="button" id="annullaButtonCercaAzienda" class="btn btn-primary red">ANNULLA</button><button type="button" id="okButtonCercaAzienda" class="btn btn-primary">CONFERMA</button></div>
               </div>
             </div>
        </div>
        
        <div id="myModalCercaProfessionista" class="modal fade">
            <div class="modal-dialog">
               <div class="modal-content">
                 <!-- dialog body -->
                 <div class="modal-body">
                   <button type="button" class="close" data-dismiss="modal">&times;</button>
                   Cerca / Inserisci Professionista
                   <form class="form-horizontal form-bordered" enctype="multipart/form-data" method="POST" onsubmit="return false;" id="idFromCercaProfessionista">
                   <div class="form-body">
                   <div class="form-group">
                   <label></label>
                   <div class="col-md-12">
                       <?=print_hidden("id_calendario", $idCalendario_daPassare)?>
                       <?=print_hidden("id_professionista", $id_professionista_presente)?>
                       <?=print_hidden("id_azienda", $id_azienda_presente)?>
                       <?=print_hidden("codice_fiscale", "")?>
                       <?=print_input("cerca_professionista", "", "Cerca Professionista", false) ?>
                   </div></div></div></form>
                   <b>IMPORTANTE:</b> Se non si trova il professionista, inserire sempre e solo il<br /><b><i>CODICE FISCALE</i></b> del professionista per poterlo inserire nella banca dati.
                 </div>
                 <!-- dialog buttons -->
                 <div class="modal-footer"><button type="button" id="annullaButtonCercaProfessionista" class="btn btn-primary red">ANNULLA</button><button type="button" id="okButtonCercaProfessionista" class="btn btn-primary">CONFERMA</button></div>
               </div>
             </div>
        </div>
        
        <div id="myModalAssociaCommerciale" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <!-- dialog body -->
                    <div class="modal-body">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        Seleziona il commerciale a cui trasferire la richiesta
                        <form class="form-horizontal form-bordered" enctype="multipart/form-data" id="idFromCommerciale">
                            <div class="form-body">
                                <div class="form-group">
                                    <label></label>
                                    <div class="col-md-12">
                                        <?php print_hidden("idCal", "$idCalendario_daPassare");?>
                                        <?php print_hidden("idAgenteOld", "$id_agente_presente");?>
                                        <?=print_select2("SELECT id as valore, CONCAT(cognome,' ', nome) as nome FROM lista_password WHERE stato='Attivo' AND livello LIKE 'commerciale' AND id!='$id_agente_presente' ORDER BY cognome, nome ASC", "idAgenteNew", "", "", false, 'tooltips select_commerciale', 'data-container="body" data-placement="top" data-original-title="SELEZIONA COMMERCIALE"') ?>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- dialog buttons -->
                    <div class="modal-footer"><button type="button" id="annullaButtonAssociaCommerciale" class="btn btn-primary red">ANNULLA</button><button type="button" id="okButtonAssociaCommerciale" class="btn btn-primary">CONFERMA</button></div>
                </div>
            </div>
        </div>
        
        <div id="myModalPrendiInCarico" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <!-- dialog body -->
                    <div class="modal-body">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h3>Prendi in Carico Richiesta</h3>
                        <form class="form-horizontal form-bordered" enctype="multipart/form-data" id="idFromPrendiInCaricoCommerciale">
                            <div class="form-body">
                                <div class="form-group">
                                    <label></label>
                                    <div class="col-md-12">
                                        Sei sicuro di voler prendere in carico questa richiesta?
                                    </div></div></div></form>
                    </div>
                    <!-- dialog buttons -->
                    <div class="modal-footer"><button type="button" id="annullaButtonPrendiInCarico" class="btn btn-primary red">ANNULLA</button><button type="button" id="okButtonPrendiInCarico" class="btn btn-primary">CONFERMA</button></div>
                </div>
            </div>
        </div>
        <!-- MODAL INVIA PREVENTIVO -->
        <div class="modal fade" id="ajax" role="basic" aria-hidden="true">
            <div class="modal-dialog" style="width: 70%;">
                <div class="modal-content" style="width: 100%;"></div>
            </div>
        </div>
        <!-- FINE MODAL INVIA PREVENTIVO -->
        
        <div id="myModalRichiestaNegativa" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <!-- dialog body -->
                    <div class="modal-body">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        Indicare la motivazione per la quale si dichiara che l'offerta è negativa!
                        <form class="form-horizontal form-bordered" enctype="multipart/form-data" id="idFromRichiestaNegativa">
                            <div class="form-body">
                                <div class="form-group">
                                    <label></label>
                                    <div class="col-md-12">
                                        <?php print_hidden("idCalendario", "$idCalendario_daPassare");?>
                                        <?php print_hidden("idPreventivo", "$idPreventivo_daPassare");?>
                                        <?=print_select2("SELECT id as valore, nome as nome FROM lista_obiezioni WHERE stato='Attivo' ORDER BY nome ASC", "idObiezione", "", "", false, 'tooltips select_obiezione', 'data-container="body" data-placement="top" data-original-title="SELEZIONA OBIEZIONE"') ?>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- dialog buttons -->
                    <div class="modal-footer"><button type="button" id="annullaButtonRichiestaNegativa" class="btn btn-primary red">ANNULLA</button><button type="button" id="okButtonRichiestaNegativa" class="btn btn-primary">CONFERMA</button></div>
                </div>
            </div>
        </div>
        
    </body>
<?php
/*
  echo '<div style="text-align:right; padding:30px; background-color:#FFF; color: red;">';
  echo '$variabili_data_1 = '.$variabili_data_1;
  echo '</div>';
 */
?>
</html>
