<?php
include_once($_SERVER['DOCUMENT_ROOT'].dirname($_SERVER[PHP_SELF]).'/config/connDB.php');
include_once(BASE_ROOT.'config/confAccesso.php');
include_once(BASE_ROOT.'config/confPermessi.php');
if(strtolower($_SESSION['livello_utente'])=='commerciale'){
    //header("Location:".BASE_URL."/moduli/commerciali/homeCommerciale.php?idMenu=34");
    header("Location:".BASE_URL."/risultatiRicerca.php");
    die();
}elseif(strtolower($_SESSION['livello_utente'])=='assistenza'){
    header("Location:".BASE_URL."/risultatiRicerca.php");
    die();
}else{
    header("Location:".BASE_URL."/risultatiRicerca.php");
    //header("Location:".BASE_URL."/moduli/anagrafiche/home.php?idMenu=4");
    die();
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
        <title><?=$site_name;?> | Dashboard</title>
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
        <link href="<?= BASE_URL ?>/assets/global/plugins/morris/morris.css" rel="stylesheet" type="text/css" />
        <link href="<?= BASE_URL ?>/assets/global/plugins/fullcalendar/fullcalendar.min.css" rel="stylesheet" type="text/css" />
        <link href="<?= BASE_URL ?>/assets/global/plugins/jqvmap/jqvmap/jqvmap.css" rel="stylesheet" type="text/css" />
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
        <link rel="shortcut icon" href="favicon.ico" />
    </head>
    <!-- END HEAD -->

    <body class="page-header-fixed page-sidebar-closed-hide-logo page-content-white page-sidebar-fixed">
        <!-- BEGIN HEADER -->
        <?php include(BASE_ROOT . '/assets/header_risultatiRicerca.php'); ?>
        <!-- END HEADER -->
        <!-- BEGIN HEADER & CONTENT DIVIDER -->
        <div class="clearfix"> </div>
        <!-- END HEADER & CONTENT DIVIDER -->
        <!-- BEGIN CONTAINER -->
        <div class="page-container">
            <!-- BEGIN SIDEBAR -->
            <?php include(BASE_ROOT . 'assets/sidebar.php'); ?>
            <!-- END SIDEBAR -->
            <!-- BEGIN CONTENT -->
            <div class="page-content-wrapper">
                <!-- BEGIN CONTENT BODY -->
                <div class="page-content">
                    <!-- BEGIN PAGE HEADER-->
                    <!-- BEGIN THEME PANEL TODO DA CANCELLARE -->

                    <!-- END THEME PANEL -->
                    <!-- BEGIN PAGE BAR -->
                    <!-- END PAGE BAR -->
                    <!-- BEGIN PAGE TITLE-->
                    <?php include(BASE_ROOT . 'assets/search_form.php'); ?>
                    <!-- END PAGE TITLE-->
                    <!-- END PAGE HEADER-->
                    <!-- BEGIN DASHBOARD STATS 1-->
                    <div class="row">
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <?php
                            $sql_007 = "SELECT count(*) as conteggio FROM lista_professionisti WHERE 1";
                            $titolo = 'Professionisti';
                            $icona = 'fa fa-users';
                            $colore = 'blue';
                            $link = '/moduli/anagrafiche/index.php?tbl=lista_professionisti&idMenu=3';
                            stampa_dashboard_stat_v2($sql_007, $titolo, $icona, $colore, $link)
                            ?>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <?php
                            $sql_007 = "SELECT count(*) as conteggio FROM lista_aziende WHERE 1";
                            $titolo = 'Totale Aziende';
                            $icona = 'fa fa-building';
                            $colore = 'grey';
                            $link = '/moduli/anagrafiche/index.php?tbl=lista_aziende&idMenu=5';
                            stampa_dashboard_stat_v2($sql_007, $titolo, $icona, $colore, $link)
                            ?>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <?php
                            $sql_007 = "SELECT count(*) as conteggio FROM lista_preventivi WHERE 1";
                            $titolo = 'Offerte';
                            $icona = 'fa fa-file-text';
                            $colore = 'red';
                            $link = '/moduli/preventivi/index.php?tbl=lista_preventivi&idMenu=16';
                            stampa_dashboard_stat_v2($sql_007, $titolo, $icona, $colore, $link)
                            ?>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <?php
                            $sql_007 = "SELECT count(*) as conteggio FROM lista_prodotti WHERE 1";
                            $titolo = 'Prodotti';
                            $icona = 'fa fa-files-o';
                            $colore = 'yellow';
                            $link = 'moduli/base/index.php?tbl=lista_prodotti&idMenu=25';
                            stampa_dashboard_stat_v2($sql_007, $titolo, $icona, $colore, $link)
                            ?>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <!-- START CALENDAR-->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="portlet light portlet-fit bordered calendar">
                                <div class="portlet-title">
                                </div>
                                <div class="portlet-body">
                                    <div class="row">
                                        <!-- BEGIN DRAGGABLE EVENTS PORTLET -->
                                        <!-- END DRAGGABLE EVENTS PORTLET-->
                                        <div class="col-md-12 col-sm-12">
                                            <div id="calendar" class="has-toolbar"> </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- END CALENDAR-->
                    <!-- END DASHBOARD STATS 1-->
                    <div class="row">
                        <div class="col-md-6 col-sm-6">
                            <?php
                            $sql_007 = "SELECT dataagg AS 'data', CONCAT('<i class=\"fa fa-search\" aria-hidden=\"true\"></i> ',ragione_sociale) AS 'campo_1', CONCAT('/moduli/anagrafiche/dettaglio.php?tbl=lista_aziende&id=',id,'') AS 'link', '<i class=\"fa fa-search\" aria-hidden=\"true\"></i>' AS 'nome_link' FROM lista_aziende  ORDER BY dataagg DESC LIMIT 10";
                            $titolo = 'Ultime Aziende';
                            $stile = '';
                            $colore = 'green';
                            stampa_widget_tasks_1($sql_007, $titolo, $stile, $colore);
                            ?>
                        </div>
                        <div class="col-md-6 col-sm-6">
                            <div class="portlet box red-thunderbird tasks-widget">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class="fa fa-cogs"></i>
                                        <span class="caption-subject bold uppercase">Cose da Fare Scadute</span>
                                        <span class="caption-helper"></span>
                                    </div>
                                    <div class="actions">
                                        
                                    </div>
                                </div>
                                <div class="portlet-body">
                                    <div class="task-content">
                                        <div class="scroller" style="height: 312px;" data-always-visible="1" data-rail-visible1="1">
                                            <!-- START TASK LIST -->
                                            <ul class="task-list">
                                                

                                            </ul>
                                            <!-- END START TASK LIST -->
                                        </div>
                                    </div>
                                    <!--<div class="task-footer">
                                        <div class="btn-arrow-link pull-right">
                                            <a href="javascript:;">See All Records</a>
                                            <i class="icon-arrow-right"></i>
                                        </div>
                                    </div>-->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <!-- END DASHBOARD STATS 1-->
                    <div class="row">
                        <div class="col-md-6 col-sm-6">
                            <div class="portlet light tasks-widget bordered">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class="fa fa-cogs font-dark"></i>
                                        <span class="caption-subject font-dark bold uppercase">Ultimi Tasks</span>
                                        <span class="caption-helper"></span>
                                    </div>
                                    <div class="actions">
                                        
                                    </div>
                                </div>
                                <div class="portlet-body">
                                    <div class="task-content">
                                        <div class="scroller" style="height: 312px;" data-always-visible="1" data-rail-visible1="1">
                                            <!-- START TASK LIST -->
                                            <ul class="task-list">
                                            
                                            </ul>
                                            <!-- END START TASK LIST -->
                                        </div>
                                    </div>
                                    <!--<div class="task-footer">
                                        <div class="btn-arrow-link pull-right">
                                            <a href="javascript:;">See All Records</a>
                                            <i class="icon-arrow-right"></i>
                                        </div>
                                    </div>-->
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-6">
                            <div class="portlet light bordered">
                                <div class="portlet-title tabbable-line">
                                    <div class="caption">
                                        <i class="fa fa-comments-o font-dark"></i>
                                        <span class="caption-subject font-dark bold uppercase">Ultimi Commenti</span>
                                    </div>
                                    <ul class="nav nav-tabs">
                                        <li class="active">
                                            <a href="#portlet_comments_1" data-toggle="tab"><i class="fa fa-long-arrow-down font-red-thunderbird" aria-hidden="true"></i> Ricevuti </a>
                                        </li>
                                        <li>
                                            <a href="#portlet_comments_2" data-toggle="tab"> Inviati <i class="fa fa-long-arrow-up font-green-jungle" aria-hidden="true"></i></a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="portlet-body">
                                    <div class="scroller" style="height: 312px;" data-always-visible="1" data-rail-visible1="1">
                                        <div class="tab-content">
                                            <div class="tab-pane active" id="portlet_comments_1">
                                                <!-- BEGIN: Comments -->
                                        
                                                <!-- END: Comments -->
                                            </div>
                                            <div class="tab-pane" id="portlet_comments_2">
                                                <!-- BEGIN: Comments -->
                                               
                                                <!-- END: Comments -->
                                            </div>
                                    </div></div>
                                </div>
                            </div>
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
        <?=pageFooterCopy();?>
        <!--[if lt IE 9]>
        <script src="assets/global/plugins/respond.min.js"></script>
        <script src="assets/global/plugins/excanvas.min.js"></script>
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
        <script src="<?= BASE_URL ?>/assets/global/plugins/morris/morris.min.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/morris/raphael-min.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/counterup/jquery.waypoints.min.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/counterup/jquery.counterup.min.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/amcharts/amcharts/amcharts.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/amcharts/amcharts/serial.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/amcharts/amcharts/pie.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/amcharts/amcharts/radar.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/amcharts/amcharts/themes/light.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/amcharts/amcharts/themes/patterns.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/amcharts/amcharts/themes/chalk.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/amcharts/ammap/ammap.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/amcharts/ammap/maps/js/worldLow.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/amcharts/amstockcharts/amstock.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/fullcalendar/fullcalendar.min.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/fullcalendar/lang/it.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/horizontal-timeline/horizontal-timeline.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/flot/jquery.flot.min.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/flot/jquery.flot.resize.min.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/flot/jquery.flot.categories.min.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/jquery-easypiechart/jquery.easypiechart.min.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/jquery.sparkline.min.js" type="text/javascript"></script>
        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN THEME GLOBAL SCRIPTS -->
        <script src="<?= BASE_URL ?>/assets/global/scripts/app.min.js" type="text/javascript"></script>
        <!-- END THEME GLOBAL SCRIPTS -->
        <!-- BEGIN PAGE LEVEL SCRIPTS -->
        <!--<script src="assets/pages/scripts/dashboard.min.js" type="text/javascript"></script>-->
        <?php
        /*$sql_007 = "SELECT ragione_sociale AS 'oggetto', DATE(data_creazione) AS 'data_inizio', '10:00:00' AS 'ora_inizio', DATE(dataagg) AS 'data_fine', '12:00:00' AS 'ora_fine', CONCAT('/moduli/anagrafiche/dettaglio.php?tbl=lista_aziende&id=',id,'') AS 'link', 'red' AS 'colore_sfondo'
        FROM lista_aziende LIMIT 100";

        $sql_007 = "SELECT oggetto AS 'oggetto', DATE(data) AS 'data_inizio', ora AS 'ora_inizio', DATE(data) AS 'data_fine', (ora+1) AS 'ora_fine', CONCAT('/moduli/anagrafiche/dettaglio_tab.php?tbl=calendario&id=',id,'') AS 'link', 'green' AS 'colore_sfondo'
        FROM calendario WHERE 1 $where_calendario AND etichetta='Nuova Richiesta' LIMIT 100";
        $defaultView = 'month';
        $stile = '';
        $colore = 'green';
        stampa_calendario_1($sql_007, $defaultView, $stile, $colore);*/
        ?>

        <!-- END PAGE LEVEL SCRIPTS -->
        <!-- BEGIN THEME LAYOUT SCRIPTS -->
        <script src="<?= BASE_URL ?>/assets/layouts/layout/scripts/layout.min.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/layouts/global/scripts/quick-sidebar.min.js" type="text/javascript"></script>
        <!-- END THEME LAYOUT SCRIPTS -->
    </body>
</html>
