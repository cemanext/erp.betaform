<?php
include_once('../../config/connDB.php');
include_once(BASE_ROOT . 'config/confAccesso.php');
require_once(BASE_ROOT.'config/confPermessi.php');
include_once(BASE_ROOT . 'moduli/base/funzioni.php');

if(isset($_GET['idMenu'])){
    $idMenu = $_GET['idMenu'];
}else{
    $idMenu = "";
}

/* 	fine post ricerca sinistra	 */

if (isset($_GET['tbl'])) {
    $tabella = $_GET['tbl'];
}


if (!empty($_GET['intervallo_data'])) {
    $intervallo_data = $_GET['intervallo_data'];
    $_SESSION['intervallo_data_base'] = $_GET['intervallo_data'];
    $data_in = before(' al ', $intervallo_data);
    $data_out = after(' al ', $intervallo_data);

    $_GET['intervallo_data'] = ($data_in)." al ".($data_out);

    $setDataCalIn = $data_in;
    $setDataCalOut = $data_out;

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
} else {
    //$richiestaAperta = $dblink->get_row("SELECT data FROM lista_fatture WHERE etichetta='Nuova Richiesta' AND (stato LIKE 'In Attesa di Controllo' OR stato LIKE 'Richiamare' OR stato LIKE 'Mai Contattato') ORDER BY data asc LIMIT 1", true);
    $titolo_intervallo = " del mese in corso";
    $_SESSION['intervallo_data_base'] = "01-".date("m-Y")." al ".date("t-m-Y");
    $_GET['intervallo_data'] = "01-".date("m-Y")." al ".date("t-m-Y");
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
        <title><?php echo $site_name; ?> | INDEXRECORD</title>
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
        <link href="<?= BASE_URL ?>/assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="<?= BASE_URL ?>/assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?= BASE_URL ?>/assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />
        <link href="<?= BASE_URL ?>/assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?= BASE_URL ?>/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" type="text/css" />
        <link href="<?= BASE_URL ?>/assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />
        <link href="<?= BASE_URL ?>/assets/global/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.css" rel="stylesheet" type="text/css" />
        <link href="<?= BASE_URL ?>/assets/global/plugins/bootstrap-toastr/toastr.min.css" rel="stylesheet" type="text/css">
        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN THEME GLOBAL STYLES -->
        <link href="<?= BASE_URL ?>/assets/global/css/components.min.css" rel="stylesheet" id="style_components" type="text/css" />
        <link href="<?= BASE_URL ?>/assets/global/css/plugins.min.css" rel="stylesheet" type="text/css" />
        <!-- END THEME GLOBAL STYLES -->
        <!-- BEGIN PAGE LEVEL STYLES -->
        <link href="<?= BASE_URL ?>/assets/apps/css/todo-2.min.css" rel="stylesheet" type="text/css" />
        <!-- END PAGE LEVEL STYLES -->
        <!-- BEGIN THEME LAYOUT STYLES -->
        <link href="<?= BASE_URL ?>/assets/layouts/layout/css/layout.min.css" rel="stylesheet" type="text/css" />
        <link href="<?= BASE_URL ?>/assets/layouts/layout/css/themes/darkblue.min.css" rel="stylesheet" type="text/css" id="style_color" />
        <link href="<?= BASE_URL ?>/assets/layouts/layout/css/custom.min.css" rel="stylesheet" type="text/css" />
        <!-- END THEME LAYOUT STYLES -->
        <link rel="shortcut icon" href="favicon.ico" />
        <?php if(filter_input(INPUT_GET, 'tbl')!="lista_prodotti"){ ?>
        <style type="text/css">
            .dataTables_extended_wrapper {
                margin-top: 0px !important;
            }
            .dataTables_extended_wrapper .table.dataTable {
                margin: 0px 0!important;
            }
        </style>
        <?php } ?> 
    </head>

    <?php
    // fPer velocizzare la pagina
    flush();
    ?>

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
                    <?php if($tabella == "lista_disattivi" || $tabella == "lista_iscritti"){ ?>
                    <div class="clearfix"></div>
                    <div class="row" style="margin-top: 10px; margin-bottom: -20px;">
                        <form action="?" class="form-horizontal form-bordered" method="GET" id="formIntervallo" name="formIntervallo">
                            <input type="hidden" name="tbl" id="whrStato" value="<?=$_GET['tbl']?>">
                            <input type="hidden" name="whr_state" id="whrStato" value="<?=$_GET['whr_state']?>">
                            <input type="hidden" name="idMenu" id="idMenu" value="<?=$_GET['idMenu']?>">
                        <div class="col-md-6">
                            <div class="form-body">
                                <div class="form-group">
                                    <label class="control-label col-md-3">Intervallo </label>
                                    <div class="input-group" id="dataRangeHome" name="dataRangeHome">
                                        <input type="text" class="form-control" id="intervallo_data" name="intervallo_data" value="<?=$_GET['intervallo_data']?>" readonly="true">
                                        <span class="input-group-btn">
                                            <button class="btn default date-range-toggle" type="submit">
                                                <i class="fa fa-calendar"></i>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6" style="vertical-align: middle;"><center><small>Risultati <?= $titolo_intervallo; ?></small></center></div>
                        </form>
                    </div>
                    <?php } ?>
                    <!-- START PAGE TITLE -->
                    <?php
                    get_pagina_titolo($idMenu, $where_lista_menu);
                    ?>
                    <!-- END PAGE TITLE -->
                    <div style="text-align: right; margin-top: -50px; margin-bottom: 15px;"> 
                        <button id="cancellaRicarcaTabella" type="button" class="btn btn-icon btn-outline green-steel" alt="CANCELLA RICERCA" title="CANCELLA RICERCA"><i class="fa fa-eraser"></i> Cancella Ricerca</button>
                    </div>
                    <!-- END PAGE HEADER-->
                    <?php Stampa_HTML_index_Base($tabella); ?>
                    <div class="form-actions left">
                        <a href="modifica.php?tbl=<?=$tabella?>&id=0" class="btn btn-circle btn-lg green-jungle"><i class="fa fa-plus"></i> Aggiungi Nuovo</a>
                    <?php if($tabella=="lista_prodotti" && filter_input(INPUT_GET, 'whr_gru')=="2"){ ?>
                        <a href="<?=BASE_URL?>/libreria/automazioni/sinc_moodle.php?fn=<?=$tabella?>" class="btn btn-circle btn-lg blue" style="margin-left: 20px;"><i class="fa fa-exchange"></i> Sincronizza Corsi con Moodle</a>
                    <?php }elseif($tabella=="lista_prodotti" && filter_input(INPUT_GET, 'whr_gru')=="1"){ ?>
                        <a href="<?=BASE_URL?>/libreria/automazioni/sinc_moodle.php?fn=recupera_abbonamenti" class="btn btn-circle btn-lg blue" style="margin-left: 20px;"><i class="fa fa-exchange"></i> Sincronizza Abbonamenti con Moodle</a>
                    <?php } ?>
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
        <!-- END CORE PLUGINS -->
        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <script src="<?= BASE_URL ?>/assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/jquery.pulsate.min.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/bootstrap-toastr/toastr.min.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/typeahead/typeahead.bundle.min.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/bootstrap-wysihtml5/wysihtml5-0.3.0.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.js" type="text/javascript"></script>

        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN THEME GLOBAL SCRIPTS -->
        <script src="<?= BASE_URL ?>/assets/global/scripts/app.min.js" type="text/javascript"></script>
        <!-- END THEME GLOBAL SCRIPTS -->
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

                $('#id_agente').on('change', function(ev, picker) {
                    document.formIntervallo.submit();
                });

                $('#intervallo_data').on('change', function(ev, picker) {
                    document.formIntervallo.submit();
                });

            });
        </script>
        <!-- BEGIN PAGE LEVEL SCRIPTS -->
        <!--<script src="<?= BASE_URL ?>/assets/pages/scripts/table-datatables-responsive.js" type="text/javascript"></script>-->
<!--<script src="<?= BASE_URL ?>/assets/apps/scripts/todo-2.min.js" type="text/javascript"></script>-->
        <script src="<?= BASE_URL ?>/assets/pages/scripts/components-select2.min.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/pages/scripts/ui-toastr.min.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/apps/scripts/index.js" type="text/javascript"></script>
        <!-- END PAGE LEVEL SCRIPTS -->
        <!-- BEGIN THEME LAYOUT SCRIPTS -->
        <script src="<?= BASE_URL ?>/assets/apps/scripts/php.min.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/apps/scripts/utility.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/layouts/layout/scripts/layout.min.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/layouts/global/scripts/quick-sidebar.min.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/moduli/base/scripts/funzioni.js" type="text/javascript"></script>
        <!-- END THEME LAYOUT SCRIPTS -->
    </body>
</html>
