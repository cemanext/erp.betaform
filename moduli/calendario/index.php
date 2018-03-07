<?php
include_once('../../config/connDB.php');
include_once(BASE_ROOT.'config/confAccesso.php');
require_once(BASE_ROOT.'config/confPermessi.php');
include_once(BASE_ROOT.'moduli/calendario/funzioni.php');
/* 	fine post ricerca sinistra	 */

global $dblink;

if(isset($_GET['idMenu'])){
    $idMenu = $_GET['idMenu'];
}else{
    $idMenu = "";
}

if (isset($_GET['tbl'])) {
    $tabella = $_GET['tbl'];
}else{
    $tabella = "calendario";
}

if(isset($_GET['whrStato']) && $_GET['whrStato']!="0e902aba617fb11d469e1b90f57fd79a" && $_GET['whrStato']!=""){
    
    if(isset($_GET['id_campagna']) && count($_GET['id_campagna'])>0){
        $id_campagna_get = $_GET['id_campagna'];
        $_SESSION['id_campagna_get'] = $_GET['id_campagna'];
    }else{
        $whereCampagnaId = "";

        $id_campagna_get = "";
        $_SESSION['id_campagna_get'] = "";
    }
    
    if (!empty($_GET['intervallo_data'])) {
        $intervallo_data = $_GET['intervallo_data'];
        $_SESSION['intervallo_data'] = $_GET['intervallo_data'];
        $data_in = before(' al ', $intervallo_data);
        $data_out = after(' al ', $intervallo_data);
        
        $_GET['intervallo_data'] = ($data_in)." al ".($data_out);

        //$tmp_in = explode("-",$data_in);
        //$tmp_out = explode("-",$data_out);
        //$setDataCalIn = $tmp_in[1]."/".$tmp_in[2]."/".$tmp_in[0];
        $setDataCalIn = $data_in;
        //$setDataCalOut = $tmp_out[1]."/".$tmp_out[2]."/".$tmp_out[0];
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

        //$titolo_intervallo = " dal  " . $data_in . " al  " . $data_out . "";
        //echo '<h1>$intervallo_data = '.$intervallo_data.'</h1>';
    } else {
        $richiestaAperta = $dblink->get_row("SELECT data FROM calendario WHERE etichetta='Nuova Richiesta' AND (stato LIKE 'In Attesa di Controllo' OR stato LIKE 'Richiamare' OR stato LIKE 'Mai Contattato') ORDER BY data asc LIMIT 1", true);
        $titolo_intervallo = " dal ".GiraDataOra($richiestaAperta['data'])." al ".date("d-m-Y");
        $_SESSION['intervallo_data'] = GiraDataOra($richiestaAperta['data'])." al ".date("d-m-Y");
        $_GET['intervallo_data'] = GiraDataOra($richiestaAperta['data'])." al ".date("d-m-Y");
        //$setDataCalOut = date("m")."/".date("d")."/".date("Y");
        $setDataCalOut = date("d-m-Y");
        //$tmp_in = explode("-",$richiestaAperta['data']);
        //$setDataCalIn = $tmp_in[1]."/".$tmp_in[2]."/".$tmp_in[0];
        $setDataCalIn = GiraDataOra($richiestaAperta['data']);
    }
}else{
    $_SESSION['intervallo_data'] = null;
    unset($_SESSION['intervallo_data']);
    $_SESSION['id_campagna_get'] = null;
    unset($_SESSION['id_campagna_get']);
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
        <link href="<?= BASE_URL ?>/assets/global/plugins/bootstrap-toastr/toastr.min.css" rel="stylesheet" type="text/css">
        <link href="<?= BASE_URL ?>/assets/global/plugins/bootstrap-multiselect/css/bootstrap-multiselect.css" rel="stylesheet" type="text/css" />
        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN THEME GLOBAL STYLES -->
        <link href="<?= BASE_URL ?>/assets/global/css/components.min.css" rel="stylesheet" id="style_components" type="text/css" />
        <link href="<?= BASE_URL ?>/assets/global/css/plugins.min.css" rel="stylesheet" type="text/css" />
        <!-- END THEME GLOBAL STYLES -->
        <!-- BEGIN PAGE LEVEL STYLES -->
        <!--<link href="<?= BASE_URL ?>/assets/apps/css/todo-2.min.css" rel="stylesheet" type="text/css" />-->
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
                    <?php if(isset($_GET['whrStato']) && $_GET['whrStato']!="0e902aba617fb11d469e1b90f57fd79a" && $_GET['whrStato']!=""){ ?>
                    <div class="clearfix"></div>
                    <div class="row" style="margin-top: 10px; margin-bottom: -20px;">
                        <form action="?" class="form-horizontal form-bordered" method="GET" id="formIntervallo" name="formIntervallo">
                            <input type="hidden" name="whrStato" id="whrStato" value="<?=$_GET['whrStato']?>">
                            <input type="hidden" name="idMenu" id="idMenu" value="<?=$_GET['idMenu']?>">
                        <div class="col-md-6">
                                <div class="form-body">
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Intervallo </label>
                                        <div class="input-group" id="dataRangeHome" name="dataRangeHome">
                                            <input type="text" class="form-control" id="intervallo_data" name="intervallo_data" value="<?=$_SESSION['intervallo_data']?>" readonly="true">
                                            <span class="input-group-btn">
                                                <button class="btn default date-range-toggle" type="button">
                                                    <i class="fa fa-calendar"></i>
                                                </button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                        </div>
                        <?php if($_GET['whrStato']==MD5('In Attesa di Controllo') ){ ?>
                            <div class="col-md-6">
                                <?=print_select2("SELECT id AS valore, nome as nome FROM lista_campagne WHERE id IN (SELECT id_campagna FROM calendario WHERE etichetta = 'Nuova Richiesta' AND stato = 'In Attesa di Controllo' GROUP BY id_campagna) ORDER BY nome ASC", "id_campagna", $id_campagna_get, "", false, 'select_campagna-allow-clear', 'data-none-selected="Seleziona Campagna"') ?>
                                <br>
                                <center>Risultati <?= $titolo_intervallo; ?></center>
                            </div>
                        <?php }else{ ?>
                            <div class="col-md-6" style="vertical-align: middle;"><center>Risultati <?= $titolo_intervallo; ?></center></div>
                        <?php } ?>
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
                    
                    <?php Stampa_HTML_index_Calendario(); ?>
                    <?php if($_GET['whrStato']==MD5('In Attesa di Controllo') ){ ?>
                        <div style="text-align: center; margin-bottom: 15px;"> 
                            <button id="associaCommerciale" type="button" class="btn btn-icon purple-studio" alt="ASSOCIAZIONE MULTIPLA COMMERCIALE" title="ASSOCIAZIONE MULTIPLA COMMERCIALE"><i class="fa fa-sign-in"></i> Associazione Multipla Commerciale</button>
                            <button id="associaProdotti" type="button" class="btn btn-icon red-mint" alt="ASSOCIAZIONE MULTIPLA PRODOTTO" title="ASSOCIAZIONE MULTIPLA PRODOTTO"><i class="fa fa-sign-in"></i> Associazione Multipla Prodotto</button>
                        </div>
                    <?php }else if(($_GET['whrStato']==MD5('Richiamare') || $_GET['whrStato']==MD5('Mai Contattato')) && ($_SESSION['livello_utente'] == 'amministratore' || $_SESSION['livello_utente'] == 'betaadmin')){ ?>
                        <div style="text-align: center; margin-bottom: 15px;"> 
                            <button id="chiudiNegativo" type="button" class="btn btn-icon red-mint" alt="CHIUDI A NEGATIVO" title="CHIUDI A NEGATIVO"><i class="fa fa-times"></i> CHIUDI A NEGATIVO</button>
                        </div>
                    <?php } ?>
                    <div class="form-actions right">
                        <button onclick="window.location.href = 'nuovo_tab.php'" type="submit" class="btn btn-circle btn-lg green-jungle"><i class="fa fa-plus"></i> Aggiungi Nuova Richiesta</button>
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
        <!-- END CORE PLUGINS -->
        <!-- BEGIN PAGE LEVEL PLUGINS -->
        
        <script src="<?= BASE_URL ?>/assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/jquery.pulsate.min.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/datatables/plugins/moment.min.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/datatables/plugins/datetime-moment.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/bootstrap-toastr/toastr.min.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/typeahead/typeahead.bundle.min.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/jquery-validation/js/additional-methods.min.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/bootstrap-multiselect/js/bootstrap-multiselect.js" type="text/javascript"></script>

        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN THEME GLOBAL SCRIPTS -->
        <script src="<?= BASE_URL ?>/assets/global/scripts/app.min.js" type="text/javascript"></script>
        <!-- END THEME GLOBAL SCRIPTS -->
        <!-- BEGIN PAGE LEVEL SCRIPTS -->
        <!--<script src="<?= BASE_URL ?>/assets/pages/scripts/table-datatables-responsive.js" type="text/javascript"></script>-->
        <!--<script src="<?= BASE_URL ?>/assets/apps/scripts/todo-2.min.js" type="text/javascript"></script>-->
        
        <script type="text/javascript">
            $(document).ready(function() {
                $('#dataRangeHome').daterangepicker({
                    opens: (App.isRTL() ? 'left' : 'right'),
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
                        //$('#intervallo_data').val(startDate.format('YYYY-MM-DD') + '|' + endDate.format('YYYY-MM-DD'));
                        //$('#defaultrange input').val(startDate.format('YYYY-MM-DD') + '|' + endDate.format('YYYY-MM-DD'));
                        $('#intervallo_data').val(startDate.format('DD-MM-YYYY') + ' al ' + endDate.format('DD-MM-YYYY'));
                    }
                );
                $('#dataRangeHome').on('apply.daterangepicker', function(ev, picker) {
                    //console.log(picker.startDate.format('YYYY-MM-DD'));
                    //console.log(picker.endDate.format('YYYY-MM-DD'));
                    document.formIntervallo.submit();
                }); 

                $('#id_agente, #id_campagna').on('change', function(ev, picker) {
                    document.formIntervallo.submit();
                });

                $('#intervallo_data').on('change', function(ev, picker) {
                    document.formIntervallo.submit();
                });

            });
        </script>
        
        <script src="<?= BASE_URL ?>/assets/pages/scripts/components-select2.min.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/apps/scripts/index.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/global/plugins/bootbox/bootbox.min.js" type="text/javascript"></script>
        <!-- END PAGE LEVEL SCRIPTS -->
        <!-- BEGIN THEME LAYOUT SCRIPTS -->
        <script src="<?= BASE_URL ?>/assets/apps/scripts/php.min.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/apps/scripts/utility.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/layouts/layout/scripts/layout.min.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/assets/layouts/global/scripts/quick-sidebar.min.js" type="text/javascript"></script>
        <!-- END THEME LAYOUT SCRIPTS -->
        <script src="<?= BASE_URL ?>/assets/pages/scripts/ui-bootbox.min.js" type="text/javascript"></script>
        <script src="<?= BASE_URL ?>/moduli/calendario/scripts/funzioni.js" type="text/javascript"></script>
        
        <div id="myModal" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <!-- dialog body -->
                    <div class="modal-body">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        Seleziona il commerciale di riferimento
                        <form class="form-horizontal form-bordered" enctype="multipart/form-data" id="idFromCommerciale">
                            <div class="form-body">
                                <div class="form-group">
                                    <label></label>
                                    <div class="col-md-12">
                                        <?=print_select2("SELECT id as valore, CONCAT(cognome,' ', nome) as nome FROM lista_password WHERE stato='Attivo' AND livello LIKE 'commerciale' ORDER BY cognome, nome ASC", "id_commerciale", "", "", false, 'tooltips select_commerciale', 'data-container="body" data-placement="top" data-original-title="SELEZIONA COMMERCIALE"') ?>
                                    </div></div></div></form>
                    </div>
                    <!-- dialog buttons -->
                    <div class="modal-footer"><button type="button" id="annullaButton" class="btn btn-primary red">ANNULLA</button><button type="button" id="okButton" class="btn btn-primary">CONFERMA</button></div>
                </div>
            </div>
        </div>
        
        <div id="myModalAssociaCommerciale" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <!-- dialog body -->
                    <div class="modal-body">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        Seleziona il commerciale di riferimento
                        <form class="form-horizontal form-bordered" enctype="multipart/form-data" id="idFromCommercialeMultiplo">
                            <div class="form-body">
                                <div class="form-group">
                                    <label></label>
                                    <div class="col-md-12">
                                        <?php print_hidden("idCal", "");?>
                                        <?=print_select2("SELECT id as valore, CONCAT(cognome,' ', nome) as nome FROM lista_password WHERE stato='Attivo' AND livello LIKE 'commerciale' ORDER BY cognome, nome ASC", "id_commerciale", "", "", false, 'tooltips select_commerciale', 'data-container="body" data-placement="top" data-original-title="SELEZIONA COMMERCIALE"') ?>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- dialog buttons -->
                    <div class="modal-footer"><button type="button" id="annullaButton" class="btn btn-primary red">ANNULLA</button><button type="button" id="okButton" class="btn btn-primary">CONFERMA</button></div>
                </div>
            </div>
        </div>
        
        <div id="myModalAssociaProdotti" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <!-- dialog body -->
                    <div class="modal-body">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        Seleziona il prodottp da inserire
                        <form class="form-horizontal form-bordered" enctype="multipart/form-data" id="idFromProdottoMultiplo">
                            <div class="form-body">
                                <div class="form-group">
                                    <label></label>
                                    <div class="col-md-12">
                                        <?php print_hidden("idCal", "");?>
                                        <?=print_select2("SELECT id as valore, nome FROM lista_prodotti WHERE stato='Attivo' ORDER BY nome ASC", "id_prodotto", "", "", false, 'tooltips select_prodotto', 'data-container="body" data-placement="top" data-original-title="SELEZIONA PRODOTTO"') ?>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- dialog buttons -->
                    <div class="modal-footer"><button type="button" id="annullaButton" class="btn btn-primary red">ANNULLA</button><button type="button" id="okButton" class="btn btn-primary">CONFERMA</button></div>
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
        
        <div id="myModalRichiestaNegativa" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <!-- dialog body -->
                    <div class="modal-body">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        Indicare la motivazione per la quale si dichiara che l'offerta è negativa!
                        <form method="POST" action="salva.php?fn=salvaNomeObiezioneInCalendario" class="form-horizontal form-bordered" enctype="multipart/form-data" id="formSalvaNomeObiezioneInCalendario">
                            <div class="form-body">
                                <div class="form-group">
                                    <label></label>
                                    <div class="col-md-12">
                                        <?php print_hidden("txt_id_calendario", "");?>
                                        <?php print_hidden("txt_id_preventivo", "");?>
                                        <?php print_hidden("txt_id_obiezione", "");?>
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
        
        <div id="myModalChiudiNegativo" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <!-- dialog body -->
                    <div class="modal-body">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        Indicare la motivazione per la quale si dichiara che la richiesta è negativa!
                        <form class="form-horizontal form-bordered" enctype="multipart/form-data" id="idFromChiudiNegativo">
                            <div class="form-body">
                                <div class="form-group">
                                    <label></label>
                                    <div class="col-md-12">
                                        <?php print_hidden("idCal", "");?>
                                        <?=print_select2("SELECT id as valore, nome as nome FROM lista_obiezioni WHERE stato='Attivo' ORDER BY nome ASC", "idObiezione", "", "", false, 'tooltips select_obiezione', 'data-container="body" data-placement="top" data-original-title="SELEZIONA OBIEZIONE"') ?>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- dialog buttons -->
                    <div class="modal-footer"><button type="button" id="annullaButtonChiudiNegativo" class="btn btn-primary red">ANNULLA</button><button type="button" id="okButtonChiudiNegativo" class="btn btn-primary">CONFERMA</button></div>
                </div>
            </div>
        </div>
    </body>
</html>
