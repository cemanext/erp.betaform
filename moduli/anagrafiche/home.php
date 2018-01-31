<?php
include_once('../../config/connDB.php');
include_once(BASE_ROOT.'config/confAccesso.php');
require_once(BASE_ROOT . 'config/confPermessi.php');

if(isset($_POST['intervallo_data'])) {
    $intervallo_data = $_POST['intervallo_data'];
    $data_in = before('|', $intervallo_data);
    $data_out = after('|', $intervallo_data);
    
    $tmp_in = explode("-",$data_in);
    $tmp_out = explode("-",$data_out);
    $setDataCalIn = $tmp_in[1]."/".$tmp_in[2]."/".$tmp_in[0];
    $setDataCalOut = $tmp_out[1]."/".$tmp_out[2]."/".$tmp_out[0];
    
    $where_intervallo = " AND dataagg BETWEEN  '" . $data_in . "' AND  '" . $data_out . "'";
    $where_intervallo_all = " AND lp.dataagg BETWEEN  '" . $data_in . "' AND  '" . $data_out . "'";
    $titolo_intervallo = " dal  " . $data_in . " al  " . $data_out . "";
    //echo '<h1>$intervallo_data = '.$intervallo_data.'</h1>';
} else {
    $where_intervallo = " AND YEAR(dataagg)=YEAR(CURDATE()) AND MONTH(dataagg)=MONTH(CURDATE())";
    $where_intervallo_all = " AND YEAR(lp.dataagg)=YEAR(CURDATE()) AND MONTH(lp.dataagg)=MONTH(CURDATE())";
    $titolo_intervallo = " del mese in corso";
    $_POST['intervallo_data'] = date("Y-m")."-01|".date("Y-m-t");
    $setDataCalIn = date("m")."/01/".date("Y");
    $setDataCalOut = date("m-t-Y");
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
        <title><?php echo $site_name; ?> | Dashboard</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <meta content="" name="description" />
        <meta content="" name="author" />
        <!-- BEGIN GLOBAL MANDATORY STYLES -->
        <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
        <link href="<?=BASE_URL?>/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <link href="<?=BASE_URL?>/assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
        <link href="<?=BASE_URL?>/assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?=BASE_URL?>/assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
        <!-- END GLOBAL MANDATORY STYLES -->
        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <link href="<?=BASE_URL?>/assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css" rel="stylesheet" type="text/css" />
        <link href="<?=BASE_URL?>/assets/global/plugins/morris/morris.css" rel="stylesheet" type="text/css" />
        <link href="<?=BASE_URL?>/assets/global/plugins/fullcalendar/fullcalendar.min.css" rel="stylesheet" type="text/css" />
        <link href="<?=BASE_URL?>/assets/global/plugins/jqvmap/jqvmap/jqvmap.css" rel="stylesheet" type="text/css" />
        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN THEME GLOBAL STYLES -->
        <link href="<?=BASE_URL?>/assets/global/css/components.min.css" rel="stylesheet" id="style_components" type="text/css" />
        <link href="<?=BASE_URL?>/assets/global/css/plugins.min.css" rel="stylesheet" type="text/css" />
        <!-- END THEME GLOBAL STYLES -->
        <!-- BEGIN THEME LAYOUT STYLES -->
        <link href="<?=BASE_URL?>/assets/layouts/layout/css/layout.min.css" rel="stylesheet" type="text/css" />
        <link href="<?=BASE_URL?>/assets/layouts/layout/css/themes/darkblue.min.css" rel="stylesheet" type="text/css" id="style_color" />
        <link href="<?=BASE_URL?>/assets/layouts/layout/css/custom.min.css" rel="stylesheet" type="text/css" />
        <!-- END THEME LAYOUT STYLES -->
        <link rel="shortcut icon" href="favicon.ico" />
      </head>
    <!-- END HEAD -->

    <body class="page-header-fixed page-sidebar-closed-hide-logo page-content-white page-sidebar-fixed">
        <!-- BEGIN HEADER -->
        <?php include(BASE_ROOT.'/assets/header.php'); ?>
        <!-- END HEADER -->
        <!-- BEGIN HEADER & CONTENT DIVIDER -->
        <div class="clearfix"> </div>
        <!-- END HEADER & CONTENT DIVIDER -->
        <!-- BEGIN CONTAINER -->
        <div class="page-container">
            <!-- BEGIN SIDEBAR -->
            <?php include(BASE_ROOT.'/assets/sidebar.php'); ?>
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

                    <!--<h3 class="page-title"> Dashboard
                        <small>& Statistiche</small>
                    </h3>-->
                    <!-- END PAGE TITLE-->
                    <!-- END PAGE HEADER-->
                    <!-- BEGIN DASHBOARD STATS 1-->
                    <div class="row">
                      <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <?php
                        $sql_009 = "SELECT count(*) as conteggio FROM lista_professionisti WHERE 1";
                        $titolo = 'Totale Professionisti';
                        $icona = 'fa fa-users';
                        $colore = 'blue';
                        $link = '/moduli/anagrafiche/index.php?tbl=lista_professionisti&idMenu=3';
                        stampa_dashboard_stat_v2($sql_009, $titolo, $icona, $colore, $link)
                        ?>
                      </div>
                      <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <?php
                        $sql_010 = "SELECT count(*) as conteggio FROM lista_aziende WHERE 1";
                        $titolo = 'Totale Aziende';
                        $icona = 'fa fa-building';
                        $colore = 'grey';
                        $link = '/moduli/anagrafiche/index.php?tbl=lista_aziende&idMenu=5';
                        stampa_dashboard_stat_v2($sql_010, $titolo, $icona, $colore, $link)
                        ?>
                      </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="row">
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <?php
                            $sql_011 = "SELECT SUM(imponibile) AS conteggio FROM lista_fatture WHERE (stato LIKE 'In Attesa di Emissione') ";
                            $titolo = 'Totale Fatture<br>in attesa di Emissione';
                            $icona = 'fa fa-area-chart';
                            $colore = 'yellow-lemon';
                            $link = '#';
                            stampa_dashboard_stat_v2($sql_011, $titolo, $icona, $colore, $link)
                            ?>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <?php
                            $sql_012 = "SELECT SUM(imponibile) AS conteggio FROM lista_fatture WHERE (stato LIKE 'In Attesa') ";
                            $titolo = 'Totale Fatture<br>in attesa di Pagamento';
                            $icona = 'fa fa-area-chart';
                            $colore = 'blue';
                            $link = '#';
                            stampa_dashboard_stat_v2($sql_012, $titolo, $icona, $colore, $link)
                            ?>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <?php
                            $sql_013 = "SELECT SUM(imponibile) AS conteggio FROM lista_fatture WHERE (stato LIKE 'Pagata') ";
                            $titolo = 'Totale Fatture<br>Pagate';
                            $icona = 'fa fa-area-chart';
                            $colore = 'green-jungle';
                            $link = '#';
                            stampa_dashboard_stat_v2($sql_013, $titolo, $icona, $colore, $link)
                            ?>
                        </div>
                        
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <?php
                            $sql_014 = "SELECT (0-SUM(imponibile)) AS conteggio FROM lista_fatture WHERE (stato LIKE 'Nota di Credito%') AND tipo LIKE 'Nota di Credito%' ";
                            $titolo = 'Totale<br>Note di Credito';
                            $icona = 'fa fa-area-chart';
                            $colore = 'red-flamingo';
                            $link = '#';
                            stampa_dashboard_stat_v2($sql_014, $titolo, $icona, $colore, $link)
                            ?>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="row">
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <?php
                            $sql_005 = "SELECT SUM(imponibile) AS conteggio FROM lista_preventivi WHERE (stato LIKE 'In Attesa') ";
                            $titolo = 'Totale<br>Ordini in Trattativa';
                            $icona = 'fa fa-area-chart';
                            $colore = 'yellow-lemon';
                            $link = '#';
                            stampa_dashboard_stat_v2($sql_005, $titolo, $icona, $colore, $link)
                            ?>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <?php
                            $sql_006 = "SELECT SUM(imponibile) AS conteggio FROM lista_preventivi WHERE (stato LIKE 'Venduto') ";
                            $titolo = 'Totale<br>Ordini Venduti<br>';
                            $icona = 'fa fa-area-chart';
                            $colore = 'blue';
                            $link = '#';
                            stampa_dashboard_stat_v2($sql_006, $titolo, $icona, $colore, $link)
                            ?>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <?php
                            $sql_007 = "SELECT SUM(imponibile) AS conteggio FROM lista_preventivi WHERE (stato LIKE 'Chiuso') ";
                            $titolo = 'Totale<br>Ordini Chiusi';
                            $icona = 'fa fa-area-chart';
                            $colore = 'green-jungle';
                            $link = '#';
                            stampa_dashboard_stat_v2($sql_007, $titolo, $icona, $colore, $link)
                            ?>
                        </div>
                        
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <?php
                            $sql_008 = "SELECT SUM(imponibile) AS conteggio FROM lista_preventivi WHERE (stato LIKE 'Negativo') ";
                            $titolo = 'Totale<br>Ordini Negativi';
                            $icona = 'fa fa-area-chart';
                            $colore = 'red-flamingo';
                            $link = '#';
                            stampa_dashboard_stat_v2($sql_008, $titolo, $icona, $colore, $link)
                            ?>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="row">
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <?php
                            $sql_001 = "SELECT COUNT(*) AS conteggio FROM calendario WHERE (stato LIKE 'Mai Contattato' OR stato LIKE 'Richiamare') ";
                            $titolo = 'Totale Richiami/Mai Contattati<br>Ancora da Gestire';
                            $icona = 'fa fa-line-chart';
                            $colore = 'yellow-lemon';
                            $link = BASE_URL.'/moduli/calendario/index.php?whrStato=ed59fefc520e30eacbb5fd110761555b&idMenu=36';
                            stampa_dashboard_stat_v2($sql_001, $titolo, $icona, $colore, $link)
                            ?>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <?php
                            $sql_002 = "SELECT COUNT(*) AS conteggio FROM calendario WHERE (stato LIKE 'In Attesa di Controllo') ";
                            $titolo = 'Totale In Attesa di Controllo<br>Ancora da Gestire';
                            $icona = 'fa fa-line-chart';
                            $colore = 'yellow-casablanca';
                            $link = BASE_URL.'/moduli/calendario/index.php?whrStato=a7d7ab5bee5f267d23e0ff28a162bafb&idMenu=36';
                            stampa_dashboard_stat_v2($sql_002, $titolo, $icona, $colore, $link)
                            ?>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <?php
                            $sql_003 = "SELECT COUNT(*) AS conteggio FROM calendario WHERE (stato LIKE 'Venduto') ";
                            $titolo = 'Totale Venduti';
                            $icona = 'fa fa-line-chart';
                            $colore = 'blue-steel';
                            $link = BASE_URL.'/moduli/calendario/index.php?whrStato=0dcf93d17feb1a4f6efe62d5d2f270b2&idMenu=36';
                            stampa_dashboard_stat_v2($sql_003, $titolo, $icona, $colore, $link)
                            ?>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <?php
                            $sql_004 = "SELECT COUNT(*) AS conteggio FROM calendario WHERE (stato LIKE 'Negativo') ";
                            $titolo = 'Totale Negativi';
                            $icona = 'fa fa-line-chart';
                            $colore = 'red-flamingo';
                            $link = BASE_URL.'/moduli/calendario/index.php?whrStato=31aa0b940088855f8a9b72946dc495ab&idMenu=36';
                            stampa_dashboard_stat_v2($sql_004, $titolo, $icona, $colore, $link)
                            ?>
                        </div>
                    </div>
                </div>
                <!-- END CONTENT BODY -->
            </div>
            <!-- END CONTENT -->
        </div>
        <!-- END CONTAINER -->
        <?=pageFooterCopy();?>
        <!--[if lt IE 9]>
        <script src="<?=BASE_URL?>/assets/global/plugins/respond.min.js"></script>
        <script src="<?=BASE_URL?>/assets/global/plugins/excanvas.min.js"></script>
        <![endif]-->
        <!-- BEGIN CORE PLUGINS -->
        <script src="<?=BASE_URL?>/assets/global/plugins/jquery.min.js" type="text/javascript"></script>
        <script src="<?=BASE_URL?>/assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
        <script src="<?=BASE_URL?>/assets/global/plugins/js.cookie.min.js" type="text/javascript"></script>
        <script src="<?=BASE_URL?>/assets/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
        <script src="<?=BASE_URL?>/assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
        <script src="<?=BASE_URL?>/assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
        <script src="<?=BASE_URL?>/assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
        <!-- END CORE PLUGINS -->
        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <script src="<?=BASE_URL?>/assets/global/plugins/moment.min.js" type="text/javascript"></script>
        <script src="<?=BASE_URL?>/assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js" type="text/javascript"></script>
        <script src="<?=BASE_URL?>/assets/global/plugins/morris/morris.min.js" type="text/javascript"></script>
        <script src="<?=BASE_URL?>/assets/global/plugins/morris/raphael-min.js" type="text/javascript"></script>
        <script src="<?=BASE_URL?>/assets/global/plugins/counterup/jquery.waypoints.min.js" type="text/javascript"></script>
        <script src="<?=BASE_URL?>/assets/global/plugins/counterup/jquery.counterup.min.js" type="text/javascript"></script>
        <script src="<?=BASE_URL?>/assets/global/plugins/fullcalendar/fullcalendar.min.js" type="text/javascript"></script>
        <script src="<?=BASE_URL?>/assets/global/plugins/fullcalendar/lang/it.js" type="text/javascript"></script>
        <script src="<?=BASE_URL?>/assets/global/plugins/horizontal-timeline/horizontal-timeline.min.js" type="text/javascript"></script>
        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN THEME GLOBAL SCRIPTS -->
        <script src="<?=BASE_URL?>/assets/global/scripts/app.min.js" type="text/javascript"></script>
        <!-- END THEME GLOBAL SCRIPTS -->
        <!-- BEGIN PAGE LEVEL SCRIPTS -->
        <!-- END PAGE LEVEL SCRIPTS -->
        <!-- BEGIN THEME LAYOUT SCRIPTS -->
        <script src="<?=BASE_URL?>/assets/layouts/layout/scripts/layout.min.js" type="text/javascript"></script>
        <script src="<?=BASE_URL?>/assets/layouts/layout/scripts/demo.min.js" type="text/javascript"></script>
        <script src="<?=BASE_URL?>/assets/layouts/global/scripts/quick-sidebar.min.js" type="text/javascript"></script>
        <!-- END THEME LAYOUT SCRIPTS -->
    </body>
</html>
