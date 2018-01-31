<?php
include_once('../../config/connDB.php');
include_once(BASE_ROOT . 'config/confAccesso.php');
require_once(BASE_ROOT.'config/confPermessi.php');

//RECUPERO LA VARIABILE POST DAL FORM defaultrange
if (isset($_POST['intervallo_data'])) {
    $intervallo_data = $_POST['intervallo_data'];
    $data_in = before(' al ', $intervallo_data);
    $data_out = after(' al ', $intervallo_data);
    
    /*$tmp_in = explode("-",$data_in);
    $tmp_out = explode("-",$data_out);
    $setDataCalIn = $tmp_in[1]."/".$tmp_in[2]."/".$tmp_in[0];
    $setDataCalOut = $tmp_out[1]."/".$tmp_out[2]."/".$tmp_out[0];
    */
    $setDataCalIn = $data_in;
    $setDataCalOut = $data_out;
    
    if($_POST['escludi_rinnovi'] == "0"){
        $whereTmkRinnovi = "";
        $whereTmkRinnoviAll = "";
    }else{
        $whereTmkRinnovi = " AND id_campagna != 166";
        $whereTmkRinnoviAll = " AND lp.id_campagna != 166";
    }
    
    if($_POST['escludi_tmk_negativi'] == "0"){
        $whereTmkNegativi = "";
        $whereTmkNegativiAll = "";
    }else{
        $whereTmkNegativi = " AND id_campagna != 181 ";
        $whereTmkNegativiAll = " AND lp.id_campagna != 181 ";
    }
    
    if($data_in == $data_out){
        $where_intervallo_cal = " $whereTmkRinnovi $whereTmkNegativi AND id_agente='".$_SESSION['id_utente'] ."' AND DATE(dataagg) = '" . GiraDataOra($data_in) . "'";
        $where_intervallo_cal_richiami = " $whereTmkRinnovi $whereTmkNegativi AND DATE(datainsert) = '" . GiraDataOra($data_in) . "'";
        $where_intervallo = " $whereTmkRinnovi $whereTmkNegativi AND lista_preventivi.sezionale NOT LIKE 'FREE' AND lista_preventivi.id_agente='".$_SESSION['id_utente'] ."' AND DATE(lista_preventivi.data_iscrizione) = '" . GiraDataOra($data_in) . "'";
        $where_intervallo_all = " $whereTmkRinnoviAll $whereTmkNegativiAll AND lp.sezionale NOT LIKE 'FREE' AND DATE(lp.data_iscrizione)  =  '" . GiraDataOra($data_in) . "'";
        $where_intervallo_negativo_all = " $whereTmkRinnoviAll $whereTmkNegativiAll AND lp.sezionale NOT LIKE 'FREE' AND DATE(lp.data_iscrizione)  =  '" . GiraDataOra($data_in) . "'";
    }else{
        $where_intervallo_cal = " $whereTmkRinnovi $whereTmkNegativi AND id_agente='".$_SESSION['id_utente'] ."' AND DATE(dataagg) BETWEEN  '" . GiraDataOra($data_in) . "' AND  '" . GiraDataOra($data_out) . "'";
        $where_intervallo_cal_richiami = " $whereTmkRinnovi $whereTmkNegativi AND DATE(datainsert) BETWEEN  '" . GiraDataOra($data_in) . "' AND  '" . GiraDataOra($data_out) . "'";
        $where_intervallo = " $whereTmkRinnovi $whereTmkNegativi AND lista_preventivi.sezionale NOT LIKE 'FREE' AND lista_preventivi.id_agente='".$_SESSION['id_utente'] ."' AND lista_preventivi.data_iscrizione BETWEEN  '" . GiraDataOra($data_in) . "' AND  '" . GiraDataOra($data_out) . "'";
        $where_intervallo_all = " $whereTmkRinnoviAll $whereTmkNegativiAll AND lp.sezionale NOT LIKE 'FREE' AND lp.data_iscrizione BETWEEN  '" . GiraDataOra($data_in) . "' AND  '" . GiraDataOra($data_out) . "'";
        $where_intervallo_negativo_all = " $whereTmkRinnoviAll $whereTmkNegativiAll AND lp.sezionale NOT LIKE 'FREE' AND lp.data_iscrizione BETWEEN  '" . GiraDataOra($data_in) . "' AND  '" . GiraDataOra($data_out) . "'";
    }
    
    if("01-".date("m-Y")." al ".date("t-m-Y") == $intervallo_data){
        $titolo_intervallo = " del mese in corso";
    }else if(date("d-m-Y", strtotime("-29 days"))." al ".date('d-m-Y') == $intervallo_data) {
        $titolo_intervallo = " utlimi 30 gioni";
    }else if(date("d-m-Y", strtotime("-6 days"))." al ".date('d-m-Y') == $intervallo_data) {
        $titolo_intervallo = " utlimi 7 gioni";
    }else if(date("d-m-Y", strtotime("-1 days"))." al ".date('d-m-Y', strtotime("-1 days")) == $intervallo_data) {
        $titolo_intervallo = " ieri";
    }elseif(date("d-m-Y")." al ".date('d-m-Y') == $intervallo_data) {
        $titolo_intervallo = " oggi";
    }else{
        $titolo_intervallo = " dal  " . $data_in . " al  " . $data_out . "";
    }
    
    //echo '<h1>$intervallo_data = '.$intervallo_data.'</h1>';
} else {
    /*$whereTmkRinnovi = " AND id_campagna != 166";
    $whereTmkRinnoviAll = " AND lp.id_campagna != 166";
    $whereTmkNegativi = " AND id_campagna != 181 ";
    $whereTmkNegativiAll = " AND lp.id_campagna != 181 ";*/
    
    $where_intervallo_cal = " $whereTmkRinnovi $whereTmkNegativi AND id_agente='".$_SESSION['id_utente'] ."'  AND YEAR(dataagg)=YEAR(CURDATE()) AND MONTH(dataagg)=MONTH(CURDATE()) AND DAY(dataagg)=DAY(CURDATE())";
    $where_intervallo_cal_richiami = " $whereTmkRinnovi $whereTmkNegativi AND YEAR(datainsert)=YEAR(CURDATE()) AND MONTH(datainsert)=MONTH(CURDATE()) AND DAY(datainsert)=DAY(CURDATE())";
    $where_intervallo = " $whereTmkRinnovi $whereTmkNegativi AND lista_preventivi.sezionale NOT LIKE 'FREE' AND lista_preventivi.id_agente='".$_SESSION['id_utente'] ."'  AND YEAR(lista_preventivi.data_iscrizione)=YEAR(CURDATE()) AND MONTH(lista_preventivi.data_iscrizione)=MONTH(CURDATE()) AND DAY(lista_preventivi.data_iscrizione)=DAY(CURDATE())";
    $where_intervallo_all = " $whereTmkRinnoviAll $whereTmkNegativiAll AND lp.sezionale NOT LIKE 'FREE' AND YEAR(lp.data_iscrizione)=YEAR(CURDATE()) AND MONTH(lp.data_iscrizione)=MONTH(CURDATE()) AND DAY(lp.data_iscrizione)=DAY(CURDATE())";
    $where_intervallo_negativo_all = " $whereTmkRinnoviAll $whereTmkNegativiAll AND lp.sezionale NOT LIKE 'FREE' AND YEAR(lp.data_iscrizione)=YEAR(CURDATE()) AND MONTH(lp.data_iscrizione)=MONTH(CURDATE()) AND DAY(lp.data_iscrizione)=DAY(CURDATE())";
    $titolo_intervallo = " di oggi";
    //$_POST['intervallo_data'] = date("Y-m")."-01|".date("Y-m-t");
    $_POST['intervallo_data'] = date("d-m-Y")." al ".date("d-m-Y");
    /*$setDataCalIn = date("m")."/".date("d")."/".date("Y");
    $setDataCalOut = date("m")."/".date("d")."/".date("Y");*/
    $setDataCalIn = date("d-m-Y");
    $setDataCalOut = date("d-m-Y");
    
    $_POST['escludi_rinnovi'] = "0";
    $_POST['escludi_tmk_negativi'] = "0";
}

if (isset($_POST['intervallo_data'])) {
    $intervallo_data = $_POST['intervallo_data'];
    $data_in = before(' al ', $intervallo_data);
    $data_out = after(' al ', $intervallo_data);
    
    if($_POST['escludi_rinnovi'] == "0"){
        $whereTmkRinnoviFatt = "";
        $whereTmkRinnoviFattAll = "";
    }else{
        $whereTmkRinnoviFatt = " AND lista_fatture.id_campagna != 166";
        $whereTmkRinnoviFattAll = " AND lf.id_campagna != 166";
    }
    
    if($_POST['escludi_tmk_negativi'] == "0"){
        $whereTmkNegativiFatt = "";
        $whereTmkNegativiFattAll = "";
    }else{
        $whereTmkNegativiFatt = " AND lista_fatture.id_campagna != 181 ";
        $whereTmkNegativiFattAll = " AND lf.id_campagna != 181 ";
    }
    
    if($data_in == $data_out){
        $where_intervallo_fatture = " $whereTmkRinnoviFatt $whereTmkNegativi AND lista_fatture.sezionale NOT LIKE 'FREE' AND lista_fatture.id_agente='".$_SESSION['id_utente'] ."' AND DATE(lista_fatture.data_creazione) = '" . GiraDataOra($data_in) . "'";
        $where_intervallo_fatture_all = " $whereTmkRinnoviFattAll $whereTmkNegativiAll AND lf.sezionale NOT LIKE 'FREE' AND DATE(lf.data_creazione)  =  '" . GiraDataOra($data_in) . "'";
        $where_intervallo_fatture_pagamento_all = " $whereTmkRinnoviFattAll $whereTmkNegativiAll AND lf.sezionale NOT LIKE 'FREE' AND DATE(lf.data_pagamento)  =  '" . GiraDataOra($data_in) . "'";
    }else{
        $where_intervallo_fatture = " $whereTmkRinnoviFatt $whereTmkNegativi AND lista_fatture.sezionale NOT LIKE 'FREE' AND lista_fatture.id_agente='".$_SESSION['id_utente'] ."' AND lista_fatture.data_creazione BETWEEN  '" . GiraDataOra($data_in) . "' AND  '" . GiraDataOra($data_out) . "'";
        $where_intervallo_fatture_all = " $whereTmkRinnoviFattAll $whereTmkNegativiAll AND lf.sezionale NOT LIKE 'FREE' AND lf.data_creazione BETWEEN  '" . GiraDataOra($data_in) . "' AND  '" . GiraDataOra($data_out) . "'";
        $where_intervallo_fatture_pagamento_all = " $whereTmkRinnoviFattAll $whereTmkNegativiAll AND lf.sezionale NOT LIKE 'FREE' AND lf.data_pagamento BETWEEN  '" . GiraDataOra($data_in) . "' AND  '" . GiraDataOra($data_out) . "'";
    }
    
    if("01-".date("m-Y")." al ".date("t-m-Y") == $intervallo_data){
        $titolo_intervallo_fatture = " del mese in corso";
    }else if(date("d-m-Y", strtotime("-29 days"))." al ".date('d-m-Y') == $intervallo_data) {
        $titolo_intervallo_fatture = " utlimi 30 gioni";
    }else if(date("d-m-Y", strtotime("-6 days"))." al ".date('d-m-Y') == $intervallo_data) {
        $titolo_intervallo_fatture = " utlimi 7 gioni";
    }else if(date("d-m-Y", strtotime("-1 days"))." al ".date('d-m-Y', strtotime("-1 days")) == $intervallo_data) {
        $titolo_intervallo_fatture = " ieri";
    }elseif(date("d-m-Y")." al ".date('d-m-Y') == $intervallo_data) {
        $titolo_intervallo_fatture = " oggi";
    }else{
        $titolo_intervallo_fatture = " dal  " . $data_in . " al  " . $data_out . "";
    }
    //echo '<h1>$intervallo_data = '.$intervallo_data.'</h1>';
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
        <title><?php echo $site_name; ?> |</title>
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
        <link href="<?= BASE_URL ?>/assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css" rel="stylesheet" type="text/css" />
        <link href="<?= BASE_URL ?>/assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
        <link href="<?= BASE_URL ?>/assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css" rel="stylesheet" type="text/css" />
        <link href="<?= BASE_URL ?>/assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css" />
        <link href="<?= BASE_URL ?>/assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?= BASE_URL ?>/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" type="text/css" />
        <link href="<?= BASE_URL ?>/assets/global/plugins/bootstrap-toastr/toastr.min.css" rel="stylesheet" type="text/css">
        <link href="<?= BASE_URL ?>/assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="<?= BASE_URL ?>/assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN THEME GLOBAL STYLES -->
        <link href="<?= BASE_URL ?>/assets/global/css/components.min.css" rel="stylesheet" id="style_components" type="text/css" />
        <link href="<?= BASE_URL ?>/assets/global/css/plugins.min.css" rel="stylesheet" type="text/css" />
        <!-- END THEME GLOBAL STYLES -->
        <!-- BEGIN THEME LAYOUT STYLES -->
        <link href="<?= BASE_URL ?>/assets/layouts/layout/css/layout.min.css" rel="stylesheet" type="text/css" />
        <link href="<?= BASE_URL ?>/assets/layouts/layout/css/themes/darkblue.min.css" rel="stylesheet" type="text/css" id="style_color" />
        <link href="<?= BASE_URL ?>/assets/layouts/layout/css/custom.min.css" rel="stylesheet" type="text/css" />
        <!-- END THEME LAYOUT STYLES -->
        <link rel="shortcut icon" href="favicon.ico" /> </head>
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
                    <!-- BEGIN THEME PANEL TODO DA CANCELLARE -->

                    <!-- END THEME PANEL -->
                    <!-- BEGIN PAGE BAR -->
                    <?php include(BASE_ROOT . '/assets/page_bar.php'); ?>
                    <!-- END PAGE BAR -->
                    <!-- BEGIN PAGE BAR
                    <div class="page-bar">
                        <ul class="page-breadcrumb">
                            <li>
                                <a href="home.php">Home</a>
                                <i class="fa fa-circle"></i>
                            </li>
                            <li>
                                <span>Dashboard</span>
                            </li>
                        </ul>
                        <div class="page-toolbar">
                            <div id="dashboard-report-range" class="pull-right tooltips btn btn-sm" data-container="body" data-placement="bottom" data-original-title="Change dashboard date range">
                                <i class="icon-calendar"></i>&nbsp;
                                <span class="thin uppercase hidden-xs"></span>&nbsp;
                                <i class="fa fa-angle-down"></i>
                            </div>
                        </div>
                    </div>-->
                    <form action="?idMenu=<?=$_GET['idMenu']?>" class="form-horizontal form-bordered" method="POST" id="formIntervallo" name="formIntervallo">
                    <div class="row">
                        <div class="col-md-6">
                                <div class="form-body">
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Intervallo </label>
                                        <div class="col-md-9">
                                            <div class="input-group" id="dataRangeHome" name="dataRangeHome">
                                                <input type="text" class="form-control" id="intervallo_data" name="intervallo_data" value="<?=$_POST['intervallo_data']?>">
                                                <span class="input-group-btn">
                                                    <button class="btn default date-range-toggle" type="submit">
                                                        <i class="fa fa-calendar"></i>
                                                    </button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>


                                </div>
                        </div>
                        <div class="col-md-6" style="vertical-align: middle;"><center><small>Risultati <?= $titolo_intervallo; ?></small></center>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label col-md-9">Escludi i RINNOVI</label>
                                <div class="col-md-3"><?=print_select_static(array("1"=>"SI", "0" => "NO"), "escludi_rinnovi", $_POST['escludi_rinnovi']); ?></div>
                                
                            </div>
                        </div>
                            
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label col-md-9">Escludi i TMK NEGATIVI</label>
                                <div class="col-md-3"><?=print_select_static(array("1"=>"SI", "0" => "NO"), "escludi_tmk_negativi", $_POST['escludi_tmk_negativi']); ?></div>
                                
                            </div>
                        </div>
                    </div>
                    </form>
                    <!-- END PAGE BAR -->
                    <!-- BEGIN PAGE TITLE-->
                    <?php //include('search_form.php'); ?>

                    <!--<h3 class="page-title"> Dashboard
                        <small>& Statistiche</small>
                    </h3>-->
                    <!-- END PAGE TITLE-->
                    <!-- END PAGE HEADER-->
                    <!-- BEGIN DASHBOARD STATS 1-->
                    <div class="row">
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                            <?php
                            $sql_001 = "SELECT COUNT(*) AS conteggio FROM calendario WHERE (stato LIKE 'Mai Contattato' OR stato LIKE 'Richiamare') AND id_agente='".$_SESSION['id_utente']."' ";
                            $titolo = 'Totale Richiami e Mai Contattati<br>Ancora da Gestire';
                            $icona = 'fa fa-line-chart';
                            $colore = 'yellow-lemon';
                            //$link = '/moduli/anagrafiche/index.php?tbl=lista_professionisti&idMenu=3';
                            $link = BASE_URL.'/moduli/calendario/index.php?whrStato=ed59fefc520e30eacbb5fd110761555b&idMenu=36';
                            stampa_dashboard_stat_v2($sql_001, $titolo, $icona, $colore, $link)
                            ?>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                            <?php
                            $sql_002 = "SELECT SUM(imponibile) AS conteggio FROM lista_preventivi WHERE (stato LIKE 'Venduto' OR stato LIKE 'Chiuso') " . $where_intervallo;
                            $titolo = 'Totale Ordini Iscritto<br>' . $titolo_intervallo;
                            $icona = 'fa fa-area-chart';
                            $colore = 'blue';
                            //$link = '/moduli/anagrafiche/index.php?tbl=lista_professionisti&idMenu=3';
                            $link = '#';
                            stampa_dashboard_stat_v2($sql_002, $titolo, $icona, $colore, $link)
                            ?>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                            <?php
                            $sql_003 = "SELECT SUM(imponibile) AS conteggio FROM lista_fatture WHERE (stato LIKE 'In Attesa' OR stato LIKE 'Pagata') " . $where_intervallo_fatture;
                            $titolo = 'Totale Ordini Fatturati<br>' . $titolo_intervallo_fatture;
                            $icona = 'fa fa-area-chart';
                            $colore = 'green-jungle';
                            //$link = '/moduli/anagrafiche/index.php?tbl=lista_professionisti&idMenu=3';
                            $link = '#';
                            stampa_dashboard_stat_v2($sql_003, $titolo, $icona, $colore, $link)
                            ?>
                        </div>
                    </div>
                    <div class="clearfix"></div>

                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <?php
                            
                            $sql_0020 = "CREATE TEMPORARY TABLE stat_commerciali_home_2 (SELECT CONCAT(ag.cognome,' ',ag.nome) as Commerciale, 
                            (SELECT COUNT(*) AS conteggio FROM calendario AS ca WHERE etichetta='Nuova Richiesta' AND (ca.stato LIKE 'Mai Contattato' OR ca.stato LIKE 'Richiamare') AND ca.id_agente=ag.id $whereTmkRinnovi) AS Richiami,
                            (SELECT COUNT(*) AS conteggio FROM calendario AS ca WHERE etichetta='Nuova Richiesta' AND (ca.stato NOT LIKE 'Fatto') AND ca.id_agente=ag.id $where_intervallo_cal_richiami) AS Richieste_Attribuite,
                            (SELECT COUNT(*) AS conteggio FROM calendario AS ca WHERE etichetta='Nuova Richiesta' AND (ca.stato LIKE 'Mai Contattato' OR ca.stato LIKE 'Richiamare') AND ca.id_agente=ag.id $where_intervallo_cal_richiami) AS Tel_Richiami,
                            (SELECT COUNT(*) AS conteggio FROM calendario AS ca WHERE etichetta='Nuova Richiesta' AND ca.id_agente=ag.id $where_intervallo_cal) AS telgestite,
                            (SELECT COUNT(*) AS conteggio_gestite FROM lista_preventivi AS lp WHERE (lp.stato LIKE 'Negativo') AND lp.id_agente=ag.id $where_intervallo_negativo_all) AS Negativo,
                            (SELECT COUNT(*) AS conteggio_venduti FROM lista_preventivi AS lp WHERE (lp.stato LIKE 'Venduto' OR lp.stato LIKE 'Chiuso') AND lp.id_agente=ag.id $where_intervallo_all) AS Iscritti,
                            (SELECT IF(SUM(lp.imponibile)>0, SUM(lp.imponibile), 0) AS conteggio_preventivi FROM lista_preventivi AS lp WHERE (lp.stato LIKE 'Venduto' OR lp.stato LIKE 'Chiuso') AND lp.id_agente=ag.id $where_intervallo_all) AS Iscritto_Lordo,
                            (SELECT IF(SUM(ABS(lf.imponibile))>0, (SUM(ABS(lf.imponibile))), 0) AS conteggio_annullate FROM lista_fatture AS lf WHERE (lf.stato LIKE 'Nota di Credito%') AND lf.tipo LIKE 'Nota di Credito%' AND lf.id_agente=ag.id $where_intervallo_fatture_all) AS Iscritto_Annulato,
                            (SELECT IF(SUM(lp.imponibile)>0, SUM(lp.imponibile), 0) AS conteggio_preventivi FROM lista_preventivi AS lp WHERE (lp.stato LIKE 'Venduto' OR lp.stato LIKE 'Chiuso') AND lp.id_agente=ag.id $where_intervallo_all) AS Iscritto_Netto,
                            (SELECT IF(SUM(lf.imponibile)>0, SUM(lf.imponibile), 0) AS fatture_incassate FROM lista_fatture AS lf WHERE (lf.stato LIKE 'Pagata') AND lf.id_agente=ag.id $where_intervallo_fatture_pagamento_all) AS Incassato,
                            (SELECT IF(SUM(lf.imponibile)>0, SUM(lf.imponibile), 0) AS fatture_da_incassate FROM lista_fatture AS lf WHERE (lf.stato LIKE 'In Attesa') AND lf.id_agente=ag.id ) AS Da_Incassare
                            FROM lista_password AS ag WHERE ag.livello='commerciale' AND ag.stato = 'Attivo');";
                            $dblink->query($sql_0020, true);
                            
                            $sql_0021 = "CREATE TEMPORARY TABLE stat_commerciali_home_totale (SELECT Commerciale, Richiami, Richieste_Attribuite, telgestite AS 'Tel_Gestite',"
                                    . " Iscritti, Iscritto_Lordo, Iscritto_Annulato, (Iscritto_Netto-Iscritto_Annulato) AS Iscritto_Netto, IF(Iscritti>0,ROUND((Richieste_Attribuite)/Iscritti, 2),0) AS Realizzato,"
                                    . " IF(Iscritto_Netto>0, ROUND(Iscritto_Netto/Iscritti,2), 0) AS Media_part_su_Fattura, Incassato, Da_Incassare"
                                    . " FROM stat_commerciali_home_2);";
                            $dblink->query($sql_0021, true);
                            
                            /*$sql_0022 = "CREATE TEMPORARY TABLE stat_commerciali_home_totale_tot_tmp (SELECT 'TOTALE', SUM(Richiami) AS Richiami, SUM(Richieste_Attribuite) AS Richieste_Attribuite, SUM(Tel_Richiami+Negativo+Iscritti) AS 'Tel_Gestite', SUM(Iscritti) AS Iscritti,"
                                    . " SUM(Iscritto_Lordo) AS Iscritto_Lordo, SUM(Iscritto_Annulato) AS Iscritto_Annulato,  SUM(Iscritto_Netto)-SUM(Iscritto_Annulato) AS Iscritto_Netto, 0 AS Realizzato,"
                                    . " 0 AS Media_part_su_Fattura, SUM(Incassato) AS Incassato, SUM(Da_Incassare) AS Da_Incassare FROM stat_commerciali_home_2);";
                            $dblink->query($sql_0022, true);
                            
                            $sql_0023 = "CREATE TEMPORARY TABLE stat_commerciali_home_totale_tot (SELECT '<b>TOTALE</b>', Richiami, Richieste_Attribuite, Tel_Gestite,"
                                    . " Iscritti, Iscritto_Lordo, Iscritto_Annulato, Iscritto_Netto, ROUND(Richieste_Attribuite/Iscritti, 2) AS Realizzato,"
                                    . " IF(Iscritto_Netto>0, ROUND(Iscritto_Netto/Iscritti,2), 0) AS Media_part_su_Fattura, Incassato, Da_Incassare"
                                    . " FROM stat_commerciali_home_totale_tot_tmp);";
                            $dblink->query($sql_0023, true);*/
                            
                            stampa_table_datatables_responsive("SELECT * FROM stat_commerciali_home_totale", "Statistiche per commerciale".$titolo_intervallo, "tabella_base_home", COLORE_PRIMARIO, true);
                            /*
                            $sql_0010 = "CREATE TEMPORARY TABLE stat_commerciali_home (SELECT CONCAT(ag.cognome,' ',ag.nome) as Commerciale, 
                            (SELECT COUNT(*) AS conteggio FROM calendario AS ca WHERE etichetta='Nuova Richiesta' AND (ca.stato LIKE 'Mai Contattato' OR ca.stato LIKE 'Richiamare') AND ca.id_agente=ag.id) AS Richiami,
                            (SELECT (COUNT(*) + (SELECT COUNT(*) AS conteggio FROM calendario AS ca WHERE etichetta='Nuova Richiesta' AND (ca.stato LIKE 'Richiamare') AND ca.id_agente=ag.id $where_intervallo_cal)) AS conteggio_gestite FROM lista_preventivi AS lp WHERE (lp.stato LIKE 'Negativo' OR lp.stato LIKE 'Venduto' OR lp.stato LIKE 'Chiuso') AND lp.id_agente=ag.id $where_intervallo_all) AS Tel_Gestite,
                            (SELECT COUNT(*) AS conteggio_venduti FROM lista_preventivi AS lp WHERE (lp.stato LIKE 'Venduto' OR lp.stato LIKE 'Chiuso') AND lp.id_agente=ag.id $where_intervallo_all) AS Iscritti,
                            (SELECT IF(SUM(lp.imponibile)>0, SUM(lp.imponibile), 0) AS conteggio_preventivi FROM lista_preventivi AS lp WHERE (lp.stato LIKE 'Venduto' OR lp.stato LIKE 'Chiuso') AND lp.id_agente=ag.id $where_intervallo_all) AS Fatt_Iscritto_Netto,
                            (SELECT IF(SUM(lf.imponibile)>0, SUM(lf.imponibile), 0) AS conteggio_fatture FROM lista_fatture AS lf WHERE (lf.stato LIKE 'In Attesa' OR lf.stato LIKE 'Pagata') AND lf.id_agente=ag.id $where_intervallo_fatture_all) AS Fatturato_Netto,
                            (SELECT IF(SUM(lf.imponibile)>0, (0-SUM(lf.imponibile)), 0) AS conteggio_annullate FROM lista_fatture AS lf WHERE (lf.stato LIKE 'Nota di Credito%') AND lf.tipo LIKE 'Nota di Credito%' AND lf.id_agente=ag.id $where_intervallo_fatture_all) AS Fatture_Annullate,
                            (SELECT ROUND((COUNT(id)*100)/Tel_Gestite, 2) AS conteggio_venduti FROM lista_preventivi AS lp WHERE (lp.stato LIKE 'Venduto' OR lp.stato LIKE 'Chiuso') AND lp.id_agente=ag.id $where_intervallo_all) AS 'Realizzato',
                            (SELECT IF(SUM(lf.imponibile)>0, ROUND(SUM(lf.imponibile)/Tel_Gestite,2), 0) AS media_fatture FROM lista_fatture AS lf WHERE (lf.stato LIKE 'In Attesa' OR lf.stato LIKE 'Pagata') AND lf.id_agente=ag.id $where_intervallo_fatture_all) AS 'Media_part_su_Fattura',
                            (SELECT IF(SUM(lf.imponibile)>0, SUM(lf.imponibile), 0) AS fatture_incassate FROM lista_fatture AS lf WHERE (lf.stato LIKE 'Pagata') AND lf.id_agente=ag.id $where_intervallo_fatture_all) AS Incassato,
                            (SELECT IF(SUM(lf.imponibile)>0, SUM(lf.imponibile), 0) AS fatture_da_incassate FROM lista_fatture AS lf WHERE (lf.stato LIKE 'In Attesa') AND lf.id_agente=ag.id $where_intervallo_fatture_all) AS Da_Incassare
                            FROM lista_password AS ag WHERE ag.livello='commerciale' AND ag.stato = 'Attivo');";
                            $test = $dblink->query($sql_0010,true);
                            
                            $sql_0011 = "CREATE TEMPORARY TABLE stat_commerciali_home_totali (SELECT '<b>TOTALI</b>' as Commerciale, 
                            (SELECT COUNT(*) AS conteggio FROM calendario AS ca WHERE etichetta='Nuova Richiesta' AND (ca.stato LIKE 'Mai Contattato' OR ca.stato LIKE 'Richiamare')) AS Richiami,
                            (SELECT (COUNT(*) + (SELECT COUNT(*) AS conteggio FROM calendario AS ca WHERE etichetta='Nuova Richiesta' AND (ca.stato LIKE 'Richiamare') AND ca.id_agente=ag.id $where_intervallo_cal)) AS conteggio_gestite FROM lista_preventivi AS lp WHERE (lp.stato LIKE 'Negativo' OR lp.stato LIKE 'Venduto' OR lp.stato LIKE 'Chiuso') $where_intervallo_all) AS Tel_Gestite,
                            (SELECT COUNT(*) AS conteggio_venduti FROM lista_preventivi AS lp WHERE (lp.stato LIKE 'Venduto' OR lp.stato LIKE 'Chiuso') $where_intervallo_all) AS Iscritti,
                            (SELECT IF(SUM(lp.imponibile)>0, SUM(lp.imponibile), 0) AS conteggio_preventivi FROM lista_preventivi AS lp WHERE (lp.stato LIKE 'Venduto' OR lp.stato LIKE 'Chiuso') $where_intervallo_all) AS Fatt_Iscritto_Netto,
                            (SELECT IF(SUM(lf.imponibile)>0, SUM(lf.imponibile), 0) AS conteggio_fatture FROM lista_fatture AS lf WHERE (lf.stato LIKE 'In Attesa' OR lf.stato LIKE 'Pagata') $where_intervallo_fatture_all) AS Fatturato_Netto,
                            (SELECT IF(SUM(lf.imponibile)>0, (0-SUM(lf.imponibile)), 0) AS conteggio_annullate FROM lista_fatture AS lf WHERE (lf.stato LIKE 'Nota di Credito%') AND lf.tipo LIKE 'Nota di Credito%' $where_intervallo_fatture_all) AS Fatture_Annullate,
                            (SELECT ROUND((COUNT(id)*100)/Tel_Gestite, 2) AS conteggio_venduti FROM lista_preventivi AS lp WHERE (lp.stato LIKE 'Venduto' OR lp.stato LIKE 'Chiuso') $where_intervallo_all) AS 'Realizzato',
                            (SELECT IF(SUM(lf.imponibile)>0, ROUND(SUM(lf.imponibile)/Tel_Gestite,2), 0) AS media_fatture FROM lista_fatture AS lf WHERE (lf.stato LIKE 'In Attesa' OR lf.stato LIKE 'Pagata') $where_intervallo_fatture_all) AS 'Media_part_su_Fattura',
                            (SELECT IF(SUM(lf.imponibile)>0, SUM(lf.imponibile), 0) AS fatture_incassate FROM lista_fatture AS lf WHERE (lf.stato LIKE 'Pagata') $where_intervallo_fatture_all) AS Incassato,
                            (SELECT IF(SUM(lf.imponibile)>0, SUM(lf.imponibile), 0) AS fatture_da_incassate FROM lista_fatture AS lf WHERE (lf.stato LIKE 'In Attesa') $where_intervallo_fatture_all) AS Da_Incassare
                            FROM lista_password AS ag WHERE ag.livello='commerciale' AND ag.stato = 'Attivo' GROUP BY livello);";
                            
                            $dblink->query($sql_0011, true);
                            
                            stampa_table_datatables_responsive("SELECT Commerciale, Richiami, Tel_Gestite, Iscritti, Fatt_Iscritto_Netto, Fatturato_Netto, Fatture_Annullate, ROUND((Iscritti*100)/(SELECT COUNT(*) AS conteggio_gestite FROM lista_preventivi AS lp WHERE (lp.stato LIKE 'Negativo' OR lp.stato LIKE 'Venduto' OR lp.stato LIKE 'Chiuso') $where_intervallo_all), 2) AS conteggio_venduti, Media_part_su_Fattura, Incassato, Da_Incassare FROM stat_commerciali_home UNION SELECT Commerciale, Richiami, Tel_Gestite, Iscritti, Fatt_Iscritto_Netto, Fatturato_Netto, Fatture_Annullate, ROUND((Iscritti*100)/(SELECT COUNT(*) AS conteggio_gestite FROM lista_preventivi AS lp WHERE (lp.stato LIKE 'Negativo' OR lp.stato LIKE 'Venduto' OR lp.stato LIKE 'Chiuso') $where_intervallo_all), 2) AS conteggio_venduti, Media_part_su_Fattura, Incassato, Da_Incassare FROM stat_commerciali_home_totali;", "Statistiche per commerciale".$titolo_intervallo, "tabella_base_home");
                            */
                            ?>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <?php
                    $sql_0004 = "SELECT 
                                                IF(stato LIKE 'In Attesa' OR stato LIKE 'Pagata%', CONCAT('<a class=\"btn btn-circle btn-icon-only blue-steel btn-outline\" href=\"".BASE_URL."/moduli/fatture/printFattureXLS.php?anno=',YEAR(data_creazione),'&mese=',MONTH(data_creazione),'&tipo=',tipo,'&idCommerciale=',id_agente,'\" target=\"_blank\" title=\"XLS FATTURE CON COMMERCIALE\" alt=\"XLS FATTURE CON COMMERCIALE\"><i class=\"fa fa-file-excel-o\"></i></a>') ,
                                                    CONCAT('<a class=\"btn btn-circle btn-icon-only red-intense btn-outline\" href=\"".BASE_URL."/moduli/fatture/printFattureXLS.php?anno=',YEAR(data_creazione),'&mese=',MONTH(data_creazione),'&tipo=',tipo,'&idCommerciale=',id_agente,'\" target=\"_blank\" title=\"XLS FATTURE CON COMMERCIALE\" alt=\"XLS FATTURE CON COMMERCIALE\"><i class=\"fa fa-file-excel-o\"></i></a>')) as 'fa-file-excel-o',
                                                YEAR(data_creazione) AS Anno, MONTH(data_creazione) AS Mese, SUM(imponibile) AS Imponibile, COUNT(stato) AS CONTEGGIO, tipo 
                                                FROM lista_fatture 
                                                WHERE sezionale NOT LIKE '%CN%' AND (stato LIKE 'In Attesa' OR stato LIKE 'Pagata%' OR stato LIKE 'Nota di%')
                                                AND id_agente = '".$_SESSION['id_utente']."'
                                                GROUP BY YEAR(data_creazione), MONTH(data_creazione), tipo
                                                ORDER BY YEAR(data_creazione) DESC, MONTH(data_creazione) DESC, tipo, sezionale, stato ASC;";
                                                
                                                stampa_table_static_basic($sql_0004, 'tab4_fatture_home', 'Esporta Fatture XLS', '', 'fa fa-user');
                                                ?>
                    </div>
                    </div>
                    <!--<div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <?php
                            /*$sql_0010 = "CREATE TEMPORARY TABLE stat_commerciali_home (SELECT CONCAT(ag.cognome,' ',ag.nome) as Commerciale, 
                            (SELECT COUNT(*) AS conteggio FROM calendario AS ca WHERE (ca.stato LIKE 'Mai Contattato' OR ca.stato LIKE 'Richiamare') AND ca.id_agente=ag.id) AS 'Richieste Aperte',
                            (SELECT SUM(lp.imponibile) AS conteggio_preventivi FROM lista_preventivi AS lp WHERE (lp.stato LIKE 'Venduto' OR lp.stato LIKE 'Chiuso') AND lp.id_agente=ag.id $where_intervallo_all) AS 'Iscritto',
                            (SELECT SUM(lf.imponibile) AS conteggio_fatture FROM lista_fatture AS lf WHERE (lf.stato LIKE 'In Attesa' OR lf.stato LIKE 'Pagata') AND lf.id_agente=ag.id $where_intervallo_fatture_all) AS 'Fatturato'
                            FROM lista_password AS ag WHERE ag.livello='commerciale' AND ag.stato = 'Attivo');";
                            $test = $dblink->query($sql_0010,true);
                            
                            $sql_0011 = "CREATE TEMPORARY TABLE stat_commerciali_home_totali (SELECT '<b>TOTALI</b>' as Commerciale, 
                            (SELECT COUNT(*) AS conteggio FROM calendario AS ca WHERE (ca.stato LIKE 'Mai Contattato' OR ca.stato LIKE 'Richiamare')) AS 'Richieste Aperte',
                            (SELECT SUM(lp.imponibile) AS conteggio_preventivi FROM lista_preventivi AS lp WHERE (lp.stato LIKE 'Venduto' OR lp.stato LIKE 'Chiuso') $where_intervallo_all) AS 'Iscritto',
                            (SELECT SUM(lf.imponibile) AS conteggio_fatture FROM lista_fatture AS lf WHERE (lf.stato LIKE 'In Attesa' OR lf.stato LIKE 'Pagata') $where_intervallo_fatture_all) AS 'Fatturato'
                            FROM lista_password AS ag WHERE ag.livello='commerciale' AND ag.stato = 'Attivo' GROUP BY livello);";
                            
                            $dblink->query($sql_0011, true);
                            
                            stampa_table_datatables_responsive("SELECT * FROM stat_commerciali_home UNION SELECT * FROM stat_commerciali_home_totali;", "Statistiche per commerciale".$titolo_intervallo, "tabella_base_home");
                            */?>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <!-- END DASHBOARD STATS 1-->
                    <!--<div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <div class="portlet">
                                <!--<div class="portlet-title">
                                    <div class="caption">
                                        <i class="fa fa-search"></i>Trovati "X" risultati
                                    </div>
                                    <div class="actions"> </div>
                                </div>-->
                    <!--            <div class="portlet-body">
                                    <ul class="nav nav-pills">
                                        <li class="active">
                                            <a href="#tab_per_commesse" data-toggle="tab" aria-expanded="true"> Offerte / Ordini </a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="tab-content">
                                    <div class="tab-pane fade active in" id="tab_per_commesse">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <?php
                                                /*$sql_001 = "SELECT
                                                IF(codice LIKE 'xxx',CONCAT('<small>',codice_interno,'</small>'),CONCAT('<B>',codice,'/',sezionale,'</B>')) AS 'Codice', imponibile, stato FROM lista_preventivi WHERE 1 " . $where_intervallo . " ORDER BY dataagg DESC LIMIT 30";
                                                stampa_table_datatables_responsive($sql_001, $titolo_intervallo, "tabella_base2", "blue-steel");
                                                */?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <!-- ESEMPIO STAMPIA GOOGLE CHART -->
                            <!--<div class="portlet light portlet-fit bordered">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class="fa fa-bar-chart"></i>
                                        <span class="caption-subject bold uppercase"><?= $titolo_intervallo; ?></span>
                                    </div>
                                    <div class="actions">
                                    </div>
                                </div>
                                <div class="portlet-body">
                                    <div id="gchart_col_1" style="height:500px;"></div>
                                </div>
                            </div>

                        </div>
                    </div>-->

                </div>
            </div>
            <!-- END CONTENT BODY -->
        </div>
        <!-- END CONTENT -->
        <!-- BEGIN QUICK SIDEBAR -->
        <?php include(BASE_ROOT . '/assets/quick_sidebar.php'); ?>
        <!-- END QUICK SIDEBAR -->
    </div>
    <!-- END CONTAINER -->
    <?=pageFooterCopy();?>
    <!--[if lt IE 9]>
    <script src="<?= BASE_URL ?>/assets/global/plugins/respond.min.js"></script>
    <script src="<?= BASE_URL ?>/assets/global/plugins/excanvas.min.js"></script>
    <![endif]-->
    <!-- BEGIN CORE PLUGINS -->
    <script src="<?= BASE_URL ?>/assets/global/plugins/jquery.min.js" type="text/javascript"></script>
    <script src="<?= BASE_URL ?>/assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="<?= BASE_URL ?>/assets/global/plugins/js.cookie.min.js" type="text/javascript"></script>
    <script src="<?= BASE_URL ?>/assets/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
    <script src="<?= BASE_URL ?>/assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
    <script src="<?= BASE_URL ?>/assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
    <script src="<?= BASE_URL ?>/assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
    <!-- END CORE PLUGINS -->
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <script src="<?= BASE_URL ?>/assets/global/plugins/moment.min.js" type="text/javascript"></script>
    <script src="<?= BASE_URL ?>/assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js" type="text/javascript"></script>
    <script src="<?= BASE_URL ?>/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
    <script src="<?= BASE_URL ?>/assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js" type="text/javascript"></script>
    <script src="<?= BASE_URL ?>/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
    <script src="<?= BASE_URL ?>/assets/global/plugins/counterup/jquery.waypoints.min.js" type="text/javascript"></script>
    <script src="<?= BASE_URL ?>/assets/global/plugins/counterup/jquery.counterup.min.js" type="text/javascript"></script>
    <script src="<?= BASE_URL ?>/assets/global/scripts/datatable.js" type="text/javascript"></script>
    <script src="<?= BASE_URL ?>/assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
    <script src="<?= BASE_URL ?>/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
    <script src="<?= BASE_URL ?>/assets/global/plugins/bootstrap-toastr/toastr.min.js" type="text/javascript"></script>
    <script src="<?= BASE_URL ?>/assets/global/plugins/typeahead/typeahead.bundle.min.js" type="text/javascript"></script>
    <script src="<?= BASE_URL ?>/assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
    <script src="<?= BASE_URL ?>/assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
    <script src="//www.google.com/jsapi" type="text/javascript"></script>
    <!-- END PAGE LEVEL PLUGINS -->
    <!-- BEGIN THEME GLOBAL SCRIPTS -->
    <script src="<?= BASE_URL ?>/assets/global/scripts/app.min.js" type="text/javascript"></script>
    <!-- END THEME GLOBAL SCRIPTS -->
    <!-- BEGIN PAGE LEVEL SCRIPTS
    <script src="<?= BASE_URL ?>/assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>-->
<?php
/*$sql_0006 = "SELECT LEFT(lista_preventivi.dataagg,7) as anno_mese,
          SUM(IF(lista_preventivi.stato LIKE 'Chiuso' OR lista_preventivi.stato LIKE 'Venduto',lista_preventivi.imponibile,0)) AS 'venduti',
          SUM(IF(lista_preventivi.stato LIKE 'In Attesa',lista_preventivi.imponibile,0)) AS 'in_attesa',
          SUM(IF(lista_preventivi.stato LIKE 'Negativo',lista_preventivi.imponibile,0)) AS 'negativi',
          SUM(IF(lista_fatture.stato LIKE 'In Attesa' OR lista_fatture.stato LIKE 'Pagata',lista_fatture.imponibile,0)) AS 'fatturati'
          FROM lista_preventivi LEFT JOIN lista_fatture ON(lista_fatture.id_agente)
          WHERE 1 " . $where_intervallo . "
          GROUP BY LEFT(lista_preventivi.dataagg,7)";*/

$sql_0006 = "SELECT LEFT(dataagg,7) as anno_mese,
          SUM(IF(stato LIKE 'Chiuso' OR stato LIKE 'Venduto',imponibile,0)) AS 'venduti',
          SUM(IF(stato LIKE 'In Attesa',imponibile,0)) AS 'in_attesa',
          SUM(IF(stato LIKE 'Negativo',imponibile,0)) AS 'negativi'
          FROM lista_preventivi
          WHERE 1 " . $where_intervallo . "
          GROUP BY LEFT(dataagg,7)";
          
$sql_0007 = "SELECT LEFT(data_creazione,7) as anno_mese,
          SUM(IF(stato LIKE 'In Attesa' OR stato LIKE 'Pagata',imponibile,0)) AS 'fatturati'
          FROM lista_fatture
          WHERE 1 " . $where_intervallo_fatture . "
          GROUP BY LEFT(data_creazione,7)";

$charDataPreventivi = $dblink->get_results($sql_0006);
//echo $dblink->get_query(); echo "<br>";
$charDataFatture = $dblink->get_results($sql_0007);
//echo $dblink->get_query(); echo "<br>";

//var_dump($charDataPreventivi);
//var_dump($charDataFatture);

$data = "[
       ['Mese/Anno', 'Fatturati', 'Iscritti', 'In Trattativa', 'Negativi']";
$r = 0;
foreach ($charDataPreventivi as $dataPrev) {
    foreach ($charDataFatture as $dataFatt) {
        if($dataPrev['anno_mese']==$dataFatt['anno_mese']){
            $data.=",['".$dataPrev['anno_mese']."', ".$dataFatt['fatturati'].", ".$dataPrev['venduti'].", ".$dataPrev['in_attesa'].", ".$dataPrev['negativi']."]";
            break;
        }
    }
    $r++;
}

$data.= "]";

$title = 'Offerte / Ordini';
$vAxis = 'Totale €';
$hAxis = 'Anno-Mese';
$stile = 'bars';
$colors = "['green','blue', 'orange', 'red']";
//stampa_gchart_col_1($sql_0006, $title, $vAxis, $hAxis, $stile, $colore);
stampa_gchart_colonne_data("gchart_col_1", $data, $title, $vAxis, $hAxis, $stile, $colors);
?>
    <!-- STAMPA GOOGLE CHART BAR -->
    <?php
    /*$sql_0007 = "SELECT COUNT(stato) as conto, stato FROM calendario WHERE destinatario='" . $_SESSION['cognome_nome_utente'] . "' $where_calendario GROUP BY destinatario,stato ORDER BY stato";
    $title = '';
    $stile = '';
    $colore = 'blue';
    stampa_gchart_pie_1($sql_0007, $title, $stile, $colore);*/
    ?>
    <script type="text/javascript">
        $(document).ready(function() {    
            $('#dataRangeHome').daterangepicker({
                opens: (App.isRTL() ? 'left' : 'right'),
                format: 'DD-MM-YYYY',
                separator: ' al ',
                startDate: '<?=$setDataCalIn?>',
                endDate: '<?=$setDataCalOut?>',
                ranges: {
                    'Oggi': [moment(), moment()],
                    'Ieri': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Ultimi 7 giorni': [moment().subtract(6, 'days'), moment()],
                    'Ultimi 30 giorni': [moment().subtract(29, 'days'), moment()],
                    'Questo mese': [moment().startOf('month'), moment().endOf('month')],
                    'Scorso Mese': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                },
                locale: {
                    format: 'DD-MM-YYYY',
                    separator: ' al ',
                    applyLabel: 'Filtra',
                    cancelLabel: 'Resetta',
                    fromLabel: 'Dal',
                    toLabel: 'Al',
                    customRangeLabel: 'Date Personalizzate',
                    daysOfWeek: ['Do', 'Lu', 'Ma', 'Me', 'Gi', 'Ve', 'Sa'],
                    monthNames: ['Gennaio', 'Febbraio', 'Marzo', 'Aprile', 'Maggio', 'Giugno', 'Luglio', 'Agosto', 'Settembre', 'Ottobre', 'Novembre', 'Dicembre'],
                    firstDay: 1
                },
                minDate: '07/01/2017',
            },
                function (startDate, endDate) {
                    $('#intervallo_data').val(startDate.format('DD-MM-YYYY') + ' al ' + endDate.format('DD-MM-YYYY'));
                    //$('#defaultrange input').val(startDate.format('YYYY-MM-DD') + '|' + endDate.format('YYYY-MM-DD'));
                    //$('#defaultrange input').html(startDate.format('DD-MM-YYYY') + ' a ' + endDate.format('DD-MM-YYYY'));
                }
            );
            $('#dataRangeHome').on('apply.daterangepicker', function(ev, picker) {
                //console.log(picker.startDate.format('YYYY-MM-DD'));
                //console.log(picker.endDate.format('YYYY-MM-DD'));
                document.formIntervallo.submit();
            });
            
            $('#intervallo_data').on('change', function(ev, picker) {
                document.formIntervallo.submit();
            });
            
            $('#escludi_rinnovi').on('change', function(ev, picker) {
                document.formIntervallo.submit();
            });
            
            $('#escludi_tmk_negativi').on('change', function(ev, picker) {
                document.formIntervallo.submit();
            });
        });
    </script>
    <script src="<?= BASE_URL ?>/assets/pages/scripts/components-select2.min.js" type="text/javascript"></script>
    <script src="<?= BASE_URL ?>/assets/pages/scripts/ui-toastr.min.js" type="text/javascript"></script>
    <!--<script src="<?= BASE_URL ?>/moduli/preventivi/scripts/funzioni.js" type="text/javascript"></script>-->
    <!-- END PAGE LEVEL SCRIPTS -->
    <!-- BEGIN THEME LAYOUT SCRIPTS -->
    <script src="<?= BASE_URL ?>/assets/apps/scripts/php.min.js" type="text/javascript"></script>
    <script src="<?= BASE_URL ?>/assets/apps/scripts/utility.js" type="text/javascript"></script>
    <script src="<?= BASE_URL ?>/assets/layouts/layout/scripts/layout.min.js" type="text/javascript"></script>
    <script src="<?= BASE_URL ?>/assets/layouts/layout/scripts/demo.min.js" type="text/javascript"></script>
    <script src="<?= BASE_URL ?>/assets/layouts/global/scripts/quick-sidebar.min.js" type="text/javascript"></script>
    <script src="<?= BASE_URL ?>/moduli/commerciali/scripts/funzioni.js" type="text/javascript"></script>
    <!-- END THEME LAYOUT SCRIPTS -->
</body>
    <?php
    echo '<br>' . $variabili_data_1;
    echo '<br>' . $variabili_data_2;
    echo '<br>' . $numero_record;
    ?>
</html>
