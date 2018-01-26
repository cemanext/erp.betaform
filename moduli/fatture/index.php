<?php
include_once('../../config/connDB.php');
include_once(BASE_ROOT.'config/confAccesso.php');
require_once(BASE_ROOT.'config/confPermessi.php');
include_once(BASE_ROOT . 'moduli/fatture/funzioni.php');

if(isset($_GET['idMenu'])){
    $idMenu = $_GET['idMenu'];
}else{
    $idMenu = "";
}

/* 	fine post ricerca sinistra	 */

if (isset($_GET['tbl'])) {
    $tabella = $_GET['tbl'];
}

if(isset($_GET['whr_state']) && $_GET['whr_state']!=""){
    if (!empty($_GET['intervallo_data'])) {
        $intervallo_data = $_GET['intervallo_data'];
        $_SESSION['intervallo_data_fattura'] = $_GET['intervallo_data'];
        $data_in = before(' al ', $intervallo_data);
        $data_out = after(' al ', $intervallo_data);
        
        $_GET['intervallo_data'] = ($data_in)." al ".($data_out);

        /*$tmp_in = explode("-",$data_in);
        $tmp_out = explode("-",$data_out);
        $setDataCalIn = $tmp_in[1]."/".$tmp_in[2]."/".$tmp_in[0];
        $setDataCalOut = $tmp_out[1]."/".$tmp_out[2]."/".$tmp_out[0];*/
        
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
        
        //echo '<h1>$intervallo_data = '.$intervallo_data.'</h1>';
    } else {
        //$richiestaAperta = $dblink->get_row("SELECT data FROM lista_fatture WHERE etichetta='Nuova Richiesta' AND (stato LIKE 'In Attesa di Controllo' OR stato LIKE 'Richiamare' OR stato LIKE 'Mai Contattato') ORDER BY data asc LIMIT 1", true);
        $titolo_intervallo = " del mese in corso";
        $_SESSION['intervallo_data_fattura'] = "01-".date("m-Y")." al ".date("t-m-Y");
        $_GET['intervallo_data'] = "01-".date("m-Y")." al ".date("t-m-Y");
        //$setDataCalOut = date("m")."/".date("t")."/".date("Y");
        //$setDataCalIn = date("m")."/01/".date("Y");
        //$tmp_in = explode("-",$richiestaAperta['data']);
        //$setDataCalIn = $tmp_out[1]."/".$tmp_out[2]."/".$tmp_out[0];
        $setDataCalIn = "01-".date("m-Y");
        $setDataCalOut = date("t-m-Y");
    }
}else{
    $_SESSION['intervallo_data_fattura'] = null;
    unset($_SESSION['intervallo_data_fattura']);
}

$sql_007 = "UPDATE `lista_preventivi`, `lista_fatture`
SET `lista_fatture`.`sezionale`=`lista_preventivi`.`sezionale`
WHERE `lista_fatture`.`id_preventivo`=`lista_preventivi`.`id` AND LENGTH(`lista_fatture`.`sezionale`)<=0";
$rs_007 = $dblink->query($sql_007);

$sql_007 = "UPDATE `lista_fatture`, `lista_fatture_dettaglio` 
SET `lista_fatture_dettaglio`.`sezionale`=`lista_fatture`.`sezionale`
WHERE `lista_fatture`.`id`=`lista_fatture_dettaglio`.`id_preventivo`
AND LENGTH(`lista_fatture`.`sezionale`)>0";
$rs_007 = $dblink->query($sql_007);
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
        <style type="text/css">
            .dataTables_extended_wrapper {
                margin-top: 0px !important;
            }
            .dataTables_extended_wrapper .table.dataTable {
                margin: 0px 0!important;
            }
        </style>
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
                    <?php if(isset($_GET['whr_state']) && $_GET['whr_state']!=""){ ?>
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
                    <!-- END PAGE HEADER-->
                    
                    <div style="text-align: right; margin-top: -50px; margin-bottom: 15px;"> 
                        <button id="cancellaRicarcaTabella" type="button" class="btn btn-icon btn-outline green-steel" alt="CANCELLA RICERCA" title="CANCELLA RICERCA"><i class="fa fa-eraser"></i> Cancella Ricerca</button>
                    </div>
                    
                    <?php 
                    //echo '<li>$tabella = '.$tabella.'</li>';
                    //echo '<li>$id = '.$id.'</li>';
                    Stampa_HTML_index_Fatture($tabella); ?>
                    <div class="form-actions right" style="display:none; visibility:hidden;">
                        <button onclick="window.location.href = 'modifica.php?tbl=<?= filter_input(INPUT_GET, 'tbl') ?>&id=0'" type="submit" class="btn btn-circle btn-lg green-jungle"><i class="fa fa-plus"></i> Aggiungi Nuovo</button>
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
        <script src="<?= BASE_URL ?>/assets/global/plugins/bootstrap-toastr/toastr.min.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/typeahead/typeahead.bundle.min.js" type="text/javascript"></script>
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
        <script src="<?= BASE_URL ?>/assets/global/plugins/bootstrap-wysihtml5/wysihtml5-0.3.0.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.js" type="text/javascript"></script>

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
        <script src="<?= BASE_URL ?>/assets/layouts/layout/scripts/demo.min.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/layouts/global/scripts/quick-sidebar.min.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/moduli/fatture/scripts/funzioni.js" type="text/javascript"></script>
        <!-- END THEME LAYOUT SCRIPTS -->
        <div class="modal fade" id="ajax" role="basic" aria-hidden="true">
            <div class="modal-dialog" style="width: 70%;">
                <div class="modal-content" style="width: 100%;"></div>
            </div>
        </div>
    </body>
</html>
