<?php
include_once('../../config/connDB.php');
include_once(BASE_ROOT . 'config/confAccesso.php');
require_once(BASE_ROOT.'config/confPermessi.php');

//RECUPERO LA VARIABILE POST DAL FORM defaultrange
if (isset($_POST['intervallo_data'])) {
    $intervallo_data = $_POST['intervallo_data'];
    $data_in = before(' al ', $intervallo_data);
    $data_out = after(' al ', $intervallo_data);
    
    $setDataCalIn = $data_in;
    $setDataCalOut = $data_out;
    
    $where_intervallo = " AND tipo LIKE 'Fattura' AND lista_fatture.dataagg BETWEEN  '" . GiraDataOra($data_in) . "' AND  '" . GiraDataOra($data_out) . "'";
    $titolo_intervallo = " dal  " . $data_in . " al  " . $data_out . "";
    //echo '<h1>$intervallo_data = '.$intervallo_data.'</h1>';
} else {
    $where_intervallo = " AND tipo LIKE 'Fattura' AND YEAR(lista_fatture.dataagg)=YEAR(CURDATE()) AND MONTH(lista_fatture.dataagg)=MONTH(CURDATE())";
    $titolo_intervallo = " del mese in corso";
    $intervallo_data = "01-".date("m-Y")." al ".date("t-m-Y");
    
    $setDataCalIn = "01-".date("m-Y");
    $setDataCalOut = date("t-m-Y");
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
                            <form action="?idMenu=<?=$_GET['idMenu']?>" class="form-horizontal form-bordered" method="POST" id="formIntervallo" name="formIntervallo">
                                <div class="form-body">
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Intervallo </label>
                                        <div class="col-md-9">
                                            <div class="input-group" id="dataRangeHome" name="dataRangeHome">
                                                <input type="text" class="form-control" id="intervallo_data" name="intervallo_data" onChange="submit();" value="<?=$intervallo_data?>">
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
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <?php
                            $sql_007 = "SELECT SUM(imponibile) AS conteggio FROM lista_fatture WHERE stato LIKE 'In Attesa' AND id IN (SELECT id_fattura FROM lista_fatture_dettaglio WHERE id_provvigione > 0) " . $where_intervallo;
                            $titolo = 'Totale Fatture In Attesa<br>' . $titolo_intervallo;
                            $icona = 'fa fa-line-chart';
                            $colore = 'yellow-lemon';
                            $link = '/moduli/fatture/index.php?tbl=lista_fatture&idMenu=41';
                            stampa_dashboard_stat_v2($sql_007, $titolo, $icona, $colore, $link)
                            ?>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <?php
                            $sql_007 = "SELECT SUM(imponibile) AS conteggio FROM lista_fatture WHERE stato LIKE 'Pagata' AND id IN (SELECT id_fattura FROM lista_fatture_dettaglio WHERE id_provvigione > 0) " . $where_intervallo;
                            $titolo = 'Totale Fatture Pagate<br>' . $titolo_intervallo;
                            $icona = 'fa fa-area-chart';
                            $colore = 'green-jungle';
                            $link = '/moduli/fatture/index.php?tbl=lista_fatture&idMenu=41';
                            stampa_dashboard_stat_v2($sql_007, $titolo, $icona, $colore, $link)
                            ?>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    
                    <?php
                    echo '<div class="row"><div class="col-md-12 col-sm-12">';
                        $sql_0001 = "SELECT 
                        (SELECT nome FROM lista_provvigioni WHERE id = id_provvigione) as Partner,
                        YEAR(data_creazione) AS Anno, MONTH(data_creazione) AS Mese, SUM(imponibile) AS Imponibile, COUNT(lista_fatture.stato) AS CONTEGGIO, lista_fatture.tipo, lista_fatture.sezionale, lista_fatture.stato 
                        FROM lista_fatture_dettaglio INNER JOIN lista_fatture ON lista_fatture.id = lista_fatture_dettaglio.id_fattura WHERE id_provvigione > 0
                        GROUP BY YEAR(data_creazione), MONTH(data_creazione), lista_fatture.tipo, lista_fatture.sezionale, lista_fatture.stato 
                        ORDER BY YEAR(data_creazione) DESC, MONTH(data_creazione) DESC, lista_fatture.tipo, lista_fatture.sezionale, lista_fatture.stato ASC;";
                        stampa_table_static_basic($sql_0001, 'tab2_fatture_home', 'Andamento Fatture Partners', '', 'fa fa-user');
                    echo '</div></div>';
                    
                    echo '<div class="row"><div class="col-md-12 col-sm-12">';
                        $sql_0001 = "SELECT 
                        (SELECT nome FROM lista_provvigioni WHERE id = id_provvigione) as Partner,
                        YEAR(data_firma) AS Anno, MONTH(data_firma) AS Mese, SUM(imponibile) AS Imponibile, COUNT(lista_preventivi.stato) AS CONTEGGIO, lista_preventivi.tipo, lista_preventivi.sezionale, lista_preventivi.stato 
                        FROM lista_preventivi_dettaglio INNER JOIN lista_preventivi ON lista_preventivi.id = lista_preventivi_dettaglio.id_preventivo WHERE id_provvigione > 0
                        GROUP BY YEAR(data_firma), MONTH(data_firma), lista_preventivi.tipo, lista_preventivi.sezionale, lista_preventivi.stato 
                        ORDER BY YEAR(data_firma) DESC, MONTH(data_firma) DESC, lista_preventivi.tipo, lista_preventivi.sezionale, lista_preventivi.stato ASC;";
                        stampa_table_static_basic($sql_0001, 'tab3_fatture_home', 'Andamento Ordini Partners', 'blue-steel', 'fa fa-user');
                    echo '</div></div>';
                    
                    /*echo '<div class="row"><div class="col-md-12 col-sm-12">';
                        $sql_0001 = "SELECT 
                        YEAR(data_creazione) AS Anno, MONTH(data_creazione) AS Mese, SUM(imponibile) AS Imponibile, COUNT(stato) AS CONTEGGIO, tipo, sezionale, stato 
                        FROM lista_fatture WHERE id IN (SELECT id_fattura FROM lista_fatture_dettaglio WHERE id_provvigione > 0)
                        GROUP BY YEAR(data_creazione), MONTH(data_creazione), tipo, sezionale, stato 
                        ORDER BY YEAR(data_creazione) DESC, MONTH(data_creazione) DESC, tipo, sezionale, stato ASC;";
                        stampa_table_static_basic($sql_0001, 'tab2_fatture_home', 'Andamento Partners', '', 'fa fa-user');
                        echo '</div></div>';
				
                    echo '<div class="row"><div class="col-md-12 col-sm-12">';
                        $sql_0001 = "SELECT 
                        IF(stato LIKE 'In Attesa' OR stato LIKE 'Pagata%', CONCAT('<a class=\"btn btn-circle btn-icon-only blue-steel btn-outline\" href=\"".BASE_URL."/moduli/fatture/printFattureXML.php?anno=',YEAR(data_creazione),'&mese=',MONTH(data_creazione),'&sezionale=',sezionale,'&stato=',stato,'\" target=\"_blank\" title=\"XML COMMERCIALISTA\" alt=\"XML COMMERCIALISTA\"><i class=\"fa fa-file-code-o\"></i></a>') ,'') as 'fa-file-code-o',
                        YEAR(data_creazione) AS Anno, MONTH(data_creazione) AS Mese, SUM(imponibile) AS Imponibile, CONCAT('<div href=\"#\" style=\"width:',((SUM(imponibile)*100)/500000),'%; background-color:blue; color:white;\">',(SUM(imponibile)),'</div>') AS CONTEGGIO, tipo, sezionale, stato 
                        FROM lista_fatture WHERE YEAR(data_creazione) = YEAR(CURDATE()) AND tipo LIKE 'Fattura'
                        AND id IN (SELECT id_fattura FROM lista_fatture_dettaglio WHERE id_provvigione > 0) 
                        GROUP BY YEAR(data_creazione), MONTH(data_creazione), tipo, sezionale, stato 
                        ORDER BY sezionale ASC, YEAR(data_creazione) ASC, MONTH(data_creazione) ASC, tipo, stato ASC;";
                        stampa_table_static_basic($sql_0001, 'tab3_fatture_home', 'Andamento Partners', '', 'fa fa-user');
                    echo '</div></div>';*/
				?>
                </div>
            </div>
            <!-- END CONTENT BODY -->
        </div>
        <!-- END CONTENT -->
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
    <script src="<?= BASE_URL ?>/assets/global/plugins/bootstrap-toastr/toastr.min.js" type="text/javascript"></script>
    
    <script src="<?= BASE_URL ?>/assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
    <script src="<?= BASE_URL ?>/assets/global/scripts/datatable.js" type="text/javascript"></script>
    <script src="<?= BASE_URL ?>/assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
    <script src="<?= BASE_URL ?>/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
    
    <!-- END PAGE LEVEL PLUGINS -->
    <!-- BEGIN THEME GLOBAL SCRIPTS -->
    <script src="<?= BASE_URL ?>/assets/global/scripts/app.min.js" type="text/javascript"></script>
    <!-- END THEME GLOBAL SCRIPTS -->
    <!-- BEGIN PAGE LEVEL SCRIPTS -->
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
                minDate: '01/10/2017',
            },
                function (startDate, endDate) {
                    $('#intervallo_data').val(startDate.format('DD-MM-YYYY') + ' al ' + endDate.format('DD-MM-YYYY'));
                }
            );
            $('#dataRangeHome').on('apply.daterangepicker', function(ev, picker) {
                document.formIntervallo.submit();
            }); 

            $('#intervallo_data').on('change', function(ev, picker) {
                document.formIntervallo.submit();
            });

        });
    </script>
    <script src="<?= BASE_URL ?>/assets/pages/scripts/table-datatables-responsive.js" type="text/javascript"></script>
    <script src="<?= BASE_URL ?>/assets/pages/scripts/ui-toastr.min.js" type="text/javascript"></script>
    <!-- END PAGE LEVEL SCRIPTS -->
    <!-- BEGIN THEME LAYOUT SCRIPTS -->
    <script src="<?= BASE_URL ?>/assets/apps/scripts/php.min.js" type="text/javascript"></script>
    <script src="<?= BASE_URL ?>/assets/apps/scripts/utility.js" type="text/javascript"></script>
    <script src="<?= BASE_URL ?>/assets/layouts/layout/scripts/layout.min.js" type="text/javascript"></script>
    <script src="<?= BASE_URL ?>/assets/layouts/global/scripts/quick-sidebar.min.js" type="text/javascript"></script>
    <script src="<?= BASE_URL ?>/moduli/provvigioni/scripts/funzioni.js" type="text/javascript"></script>
    <!-- END THEME LAYOUT SCRIPTS -->
</body>
</html>
