<?php
include_once('../../config/connDB.php');
include_once(BASE_ROOT.'config/confAccesso.php');
require_once(BASE_ROOT.'config/confPermessi.php');

//RECUPERO LA VARIABILE POST DAL FORM defaultrange
if (isset($_POST['intervallo_data'])) {
    $intervallo_data = $_POST['intervallo_data'];
    $data_in = before('|', $intervallo_data);
    $data_out = after('|', $intervallo_data);
    $where_intervallo = " AND lista_preventivi.dataagg BETWEEN  '" . $data_in . "' AND  '" . $data_out . "'";
    $titolo_intervallo = " dal  " . $data_in . " al  " . $data_out . "";
    //echo '<h1>$intervallo_data = '.$intervallo_data.'</h1>';
} else {
    $where_intervallo = " AND YEAR(lista_preventivi.dataagg)=YEAR(CURDATE()) AND MONTH(lista_preventivi.dataagg)=MONTH(CURDATE())";
    $titolo_intervallo = " del mese in corso";
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
                    <div class="row">

                        <div class="col-md-6">
                            <form action="?" class="form-horizontal form-bordered" method="POST" id="formIntervallo" name="formIntervallo">
                                <div class="form-body">
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Intervallo </label>
                                        <div class="col-md-9">
                                            <div class="input-group" id="defaultrange" name="defaultrange">
                                                <input type="text" class="form-control" id="intervallo_data" name="intervallo_data" onChange="submit();">
                                                <span class="input-group-btn">
                                                    <button class="btn default date-range-toggle" type="submit">
                                                        <i class="fa fa-calendar"></i>
                                                    </button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>


                                </div>
                            </form>
                        </div>
                        <div class="col-md-6" style="vertical-align: middle;"><center><small>Risultati <?= $titolo_intervallo; ?></small></center>
                        </div>

                    </div>
                    <!-- END PAGE BAR -->
                    <!-- BEGIN PAGE TITLE-->
                    <!-- END PAGE TITLE-->
                    <!-- END PAGE HEADER-->
                    <!-- BEGIN DASHBOARD STATS 1-->
                    <div class="row">
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                            <?php
                            $sql_007 = "SELECT SUM(imponibile) AS conteggio FROM lista_preventivi WHERE stato LIKE 'In Attesa' " . $where_intervallo;
                            $titolo = 'Totale Ordini In Attesa<br>' . $titolo_intervallo;
                            $icona = 'fa fa-line-chart';
                            $colore = 'yellow-lemon';
                            $link = '/moduli/preventivi/index.php?tbl=lista_preventivi&idMenu=16';
                            stampa_dashboard_stat_v2($sql_007, $titolo, $icona, $colore, $link)
                            ?>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                            <?php
                            $sql_007 = "SELECT SUM(imponibile) AS conteggio FROM lista_preventivi WHERE stato LIKE 'Chiuso' " . $where_intervallo;
                            $titolo = 'Totale Ordini Chiusi<br>' . $titolo_intervallo;
                            $icona = 'fa fa-area-chart';
                            $colore = 'green-jungle';
                            $link = '/moduli/preventivi/index.php?tbl=lista_preventivi&idMenu=16';
                            stampa_dashboard_stat_v2($sql_007, $titolo, $icona, $colore, $link)
                            ?>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                            <?php
                            $sql_007 = "SELECT SUM(imponibile) AS conteggio FROM lista_preventivi WHERE stato LIKE 'Venduto' " . $where_intervallo;
                            $titolo = 'Totale Ordini Venduti<br>' . $titolo_intervallo;
                            $icona = 'fa fa-area-chart';
                            $colore = 'blue';
                            $link = '/moduli/preventivi/index.php?tbl=lista_preventivi&idMenu=16';
                            stampa_dashboard_stat_v2($sql_007, $titolo, $icona, $colore, $link)
                            ?>
                        </div>
                    </div>
                    <div class="clearfix"></div>

                    <!-- END DASHBOARD STATS 1-->
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <div class="portlet">
                                <div class="portlet-body">
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
                                                <!-- INIZIO TABELLA-->
                                                <div class="portlet box blue">
                                                    <div class="portlet-title">
                                                        <div class="caption">
                                                            <i class="fa fa-list"></i>
                                                            <span class="caption-subject bold uppercase"><?= $titolo_intervallo; ?></span>
                                                        </div>
                                                        <div class="tools"> </div>
                                                    </div>
                                                    <div class="portlet-body">
                                                        <table class="table table-striped table-bordered table-hover dt-responsive" width="100%" id="tab1_per_commessa">
                                                            <thead>
                                                                <tr>
                                                                    <th class="min-phone-l"><i class="fa fa-search"></i></th>
                                                                    <th class="all">Codice</th>
                                                                    <th class="min-phone-l"> Cliente</th>
                                                                    <th class="min-phone-l">Imponibile &euro;</th>
                                                                    <th class="min-phone-l">Stato</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                $sql_001 = "SELECT
                                                                  CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"" . BASE_URL . "/moduli/preventivi/dettaglio.php?tbl=lista_preventivi&id=',id,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'dettaglio',
                                                                  IF(codice LIKE 'xxx',CONCAT('<small>',codice_interno,'</small>'),CONCAT('<B>',codice,'/',sezionale,'</B>')) AS 'codice', 
                                                                  (SELECT CONCAT('<B>',cognome,' ',nome,'</B>') FROM lista_professionisti WHERE id = id_professionista) as cliente, 
                                                                imponibile, 
                                                                  (SELECT CONCAT('<span class=\"badge bold bg-',colore_sfondo,' bg-font-',colore_sfondo,'\"> ',nome,' </span>') FROM `lista_preventivi_stati` WHERE `lista_preventivi_stati`.nome LIKE lista_preventivi.stato) AS stato
                                                                  FROM lista_preventivi WHERE 1 " . $where_intervallo . " ORDER BY dataagg DESC LIMIT 100";
                                                                $rs_001 = $dblink->get_results($sql_001);
                                                                if ($rs_001) {
                                                                    foreach ($rs_001 as $row_001) {
                                                                        echo '<tr>
                                                                            <td>' . $row_001['dettaglio'] . '</td>
                                                                            <td>' . $row_001['codice'] . '</td>
                                                                            <td>' . $row_001['cliente'] . '</td>
                                                                          <td>' . $row_001['imponibile'] . '</td>
                                                                          <td>' . $row_001['stato'] . '</td>
                                                                        </tr>';
                                                                    }
                                                                }
                                                                ?>

                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <!-- FINE TABELLA-->
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <!-- ESEMPIO STAMPIA GOOGLE CHART -->
                            <div class="portlet light portlet-fit bordered">
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
    <script src="//www.google.com/jsapi" type="text/javascript"></script>
    <!-- END PAGE LEVEL PLUGINS -->
    <!-- BEGIN THEME GLOBAL SCRIPTS -->
    <script src="<?= BASE_URL ?>/assets/global/scripts/app.min.js" type="text/javascript"></script>
    <!-- END THEME GLOBAL SCRIPTS -->
    <!-- BEGIN PAGE LEVEL SCRIPTS
    <script src="<?= BASE_URL ?>/assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>-->
    <?php
    $sql_0006 = "SELECT LEFT(dataagg,7) as anno_mese,
              SUM(IF(stato LIKE 'Chiuso' OR stato LIKE 'Venduto',imponibile,0)) AS chiuso,
              SUM(IF(stato LIKE 'In Attesa',imponibile,0)) AS in_attesa,
              SUM(IF(stato LIKE 'Negativo',imponibile,0)) AS negativo,
              SUM(IF(stato LIKE 'Venduto',imponibile,0)) AS venduto
              FROM lista_preventivi
              WHERE 1 " . $where_intervallo . "
              GROUP BY LEFT(dataagg,7)";
    $title = 'Offerte / Ordini';
    $vAxis = 'Totale €';
    $hAxis = 'Anno-Mese';
    $stile = '';
    $colore = 'blue';
    //stampa_gchart_col_1($sql_0006, $title, $vAxis, $hAxis, $stile, $colore);
    stampa_gchart_col_1_preventivi($sql_0006, $title, $vAxis, $hAxis, $stile, $colore);
    ?>
    <!-- STAMPA GOOGLE CHART BAR -->
    <?php
    $sql_0007 = "SELECT COUNT(stato) as conto, stato FROM calendario WHERE destinatario='" . $_SESSION['cognome_nome_utente'] . "' $where_calendario GROUP BY destinatario,stato ORDER BY stato";
    $title = '';
    $stile = '';
    $colore = 'blue';
    stampa_gchart_pie_1($sql_0007, $title, $stile, $colore);
    ?>
    <script type="text/javascript">
        $('#defaultrange').daterangepicker({
            opens: (App.isRTL() ? 'left' : 'right'),
            format: 'YYYY-MM-DD',
            separator: ' to ',
            startDate: moment().subtract(29, 'days'),
            endDate: moment(),
            ranges: {
                'Oggi': [moment(), moment()],
                'Ieri': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Ultimi 7 giorni': [moment().subtract(6, 'days'), moment()],
                'Ultimi 30 giorni': [moment().subtract(29, 'days'), moment()],
                'Questo mese': [moment().startOf('month'), moment().endOf('month')],
                'Scorso Mese': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
        },
                function (startDate, endDate) {
                    $('#defaultrange input').val(startDate.format('YYYY-MM-DD') + '|' + endDate.format('YYYY-MM-DD'));
                    $('#defaultrange input').html(startDate.format('DD-MM-YYYY') + ' a ' + endDate.format('DD-MM-YYYY'));
                }
        );
        $('#defaultrange').on('apply.daterangepicker', function(ev, picker) {
              //console.log(picker.startDate.format('YYYY-MM-DD'));
              //console.log(picker.endDate.format('YYYY-MM-DD'));
              document.formIntervallo.submit();
            });
    </script>
    <script src="<?= BASE_URL ?>/assets/pages/scripts/table-datatables-responsive.js" type="text/javascript"></script>
    <!--<script src="<?= BASE_URL ?>/moduli/preventivi/scripts/funzioni.js" type="text/javascript"></script>-->
    <!-- END PAGE LEVEL SCRIPTS -->
    <!-- BEGIN THEME LAYOUT SCRIPTS -->
    <script src="<?= BASE_URL ?>/assets/layouts/layout/scripts/layout.min.js" type="text/javascript"></script>
    <script src="<?= BASE_URL ?>/assets/layouts/layout/scripts/demo.min.js" type="text/javascript"></script>
    <script src="<?= BASE_URL ?>/assets/layouts/global/scripts/quick-sidebar.min.js" type="text/javascript"></script>
    <!-- END THEME LAYOUT SCRIPTS -->
</body>
</html>
