<?php
include_once('../../config/connDB.php');
include_once(BASE_ROOT . 'config/confAccesso.php');
require_once(BASE_ROOT.'config/confPermessi.php');
include_once(BASE_ROOT . 'moduli/iscrizioni/funzioni.php');

if(isset($_GET['idMenu'])){
    $idMenu = $_GET['idMenu'];
}else{
    $idMenu = "";
}

if(isset($_POST['intervallo_data'])) {
    $intervallo_data = $_POST['intervallo_data'];
    $data_in = before(' al ', $intervallo_data);
    $data_out = after(' al ', $intervallo_data);
    
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
    
    if($data_in == $data_out){
        $where_intervallo_calendario = " AND data_inizio_iscrizione = '" . GiraDataOra($data_in) . "' ";
    }else{
        $where_intervallo_calendario = " AND data_inizio_iscrizione BETWEEN '" . GiraDataOra($data_in) . "' AND  '" . GiraDataOra($data_out) . "'";
   }
    //echo '<h1>$intervallo_data = '.$intervallo_data.'</h1>';
} else {
    
    $where_intervallo_calendario = " AND YEAR(data_inizio_iscrizione)=YEAR(CURDATE()) AND MONTH(data_inizio_iscrizione)=MONTH(CURDATE())";
    
    //$titolo_intervallo = " del mese in corso";
    $titolo_intervallo = " dal  01-" . date("m-Y") . " al  " . date("t-m-Y") . "";
    $_POST['intervallo_data'] = "01-" . date("m-Y")." al ".date("t-m-Y");
    $setDataCalIn = "01-".date("m-Y") ;
    $setDataCalOut = date("t-m-Y") ;
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
        <title><?php echo $site_name; ?> | </title>
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
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <form action="?idMenu=<?=$_GET['idMenu']?>" class="form-horizontal form-bordered" method="POST" id="formIntervallo" name="formIntervallo">
                                <div class="form-body">
                                    <div class="form-group">
                                        <label class="control-label col-md-1">Intervallo </label>
                                        <div class="col-md-5">
                                            <div class="input-group" id="dataRangeHome" name="dataRangeHome">
                                                <input type="text" class="form-control" id="intervallo_data" name="intervallo_data" value="<?=$_POST['intervallo_data']?>">
                                                <span class="input-group-btn">
                                                    <button class="btn default date-range-toggle" type="submit">
                                                        <i class="fa fa-calendar"></i>
                                                    </button>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <center><small>Risultati <?= $titolo_intervallo; ?></small></center>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- END PAGE BAR -->
                    <!-- BEGIN PAGE TITLE-->
                    <?php //include(BASE_ROOT.'/assets/search_form.php'); ?>
                    <?php
                    get_pagina_titolo($idMenu, $where_lista_menu);
                    ?>
                    <!-- END PAGE TITLE-->
                    <!-- END PAGE HEADER-->
                    <!-- START ROW 2 - 4 COLUMN-->
                    <div class="row">

                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <?php
                            $sql_007 = "SELECT COUNT(id) AS conteggio FROM lista_iscrizioni WHERE MONTH(data_inizio)=MONTH(CURDATE()) AND YEAR(data_inizio)=YEAR(CURDATE())";
                            $titolo = 'Iscrizioni del Mese';
                            $icona = 'fa fa-graduation-cap';
                            $colore = 'green-meadow';
                            $link = '/moduli/iscrizioni/index.php?tbl=lista_iscrizioni&idMenu=59';
                            stampa_dashboard_stat_v2($sql_007, $titolo, $icona, $colore, $link)
                            ?>
                        </div>

                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <?php
                            $sql_007 = "SELECT COUNT(*) AS conteggio FROM lista_iscrizioni WHERE YEAR(data_inizio_iscrizione)=YEAR(CURDATE()) AND (stato LIKE 'In Attesa' OR stato LIKE 'In Corso')";
                            $titolo = 'Iscritti Anno ';
                            $icona = 'fa fa-graduation-cap';
                            $colore = 'green-seagreen';
                            $link = '/moduli/iscrizioni/index.php?tbl=lista_iscrizioni&idMenu=59';
                            stampa_dashboard_stat_v2($sql_007, $titolo, $icona, $colore, $link)
                            ?>
                        </div>

                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <?php
                            $sql_007 = "SELECT COUNT(id) AS conteggio FROM lista_iscrizioni_dettaglio WHERE MONTH(data_completamento)=MONTH(CURDATE()) AND YEAR(data_completamento)=YEAR(CURDATE())";
                            $titolo = 'Completati del Mese';
                            $icona = 'fa fa-graduation-cap';
                            $colore = 'green-haze';
                            $link = '/moduli/iscrizioni/index.php?tbl=lista_iscrizioni&idMenu=59';
                            stampa_dashboard_stat_v2($sql_007, $titolo, $icona, $colore, $link)
                            ?>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <?php
                            $sql_007 = "SELECT COUNT(id) AS conteggio FROM lista_iscrizioni_dettaglio WHERE YEAR(data_completamento)=YEAR(CURDATE())";
                            $titolo = 'Completati Annuali';
                            $icona = 'fa fa-graduation-cap';
                            $colore = 'green-jungle';
                            $link = '/moduli/iscrizioni/index.php?tbl=lista_iscrizioni&idMenu=59';
                            stampa_dashboard_stat_v2($sql_007, $titolo, $icona, $colore, $link)
                            ?>
                        </div>

                    </div>
                    <!-- END ROW 2 - 4 COLUMN-->
                    <!-- START ROW 2 - 4 COLUMN-->
                    <div class="row">

                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <?php
                            $sql_007 = "SELECT COUNT(stato) as conteggio FROM `lista_iscrizioni` WHERE 1 AND stato='In Attesa' GROUP BY stato";
                            $titolo = 'Totale Corsi - In Attesa';
                            $icona = 'fa fa-users';
                            $colore = 'yellow-saffron';
                            $link = '/moduli/iscrizioni/index.php?tbl=table_listaIscrizioniInAttesa&idMenu=105';
                            stampa_dashboard_stat_v2($sql_007, $titolo, $icona, $colore, $link);
                            ?>
                        </div>

                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                        <?php
                            $sql_007 = "SELECT COUNT(stato) as conteggio FROM `lista_iscrizioni` WHERE 1 AND stato='In Corso' GROUP BY stato";
                            $titolo = 'Totale Corsi - In Corso';
                            $icona = 'fa fa-users';
                            $colore = 'yellow-gold';
                            $link = '/moduli/iscrizioni/index.php?tbl=table_listaIscrizioniInCorso&idMenu=104';
                            stampa_dashboard_stat_v2($sql_007, $titolo, $icona, $colore, $link);
                        ?>
                        </div>

                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                        <?php
                            $sql_007 = "SELECT COUNT(stato) as conteggio FROM `lista_iscrizioni` WHERE 1 AND stato='Completato' GROUP BY stato";
                            $titolo = 'Totale Corsi - Completati';
                            $icona = 'fa fa-users';
                            $colore = 'green-jungle';
                            $link = '/moduli/iscrizioni/index.php?tbl=table_listaIscrizioniCompletati&idMenu=106';
                            stampa_dashboard_stat_v2($sql_007, $titolo, $icona, $colore, $link);
                        ?>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                        <?php
                            $sql_007 = "SELECT COUNT(*) as conteggio FROM `lista_iscrizioni` WHERE 1 AND stato='Configurazione'";
                            $titolo = 'Abbonamenti Attivi';
                            $icona = 'fa fa-users';
                            $colore = 'green-jungle';
                            $link = '';
                            stampa_dashboard_stat_v2($sql_007, $titolo, $icona, $colore, $link);
                        ?>
                        </div>
                            <div class="col-md-6 col-sm-6">
                            <?php
                            $sql_007 = "SELECT COUNT(*) as conteggio FROM `lista_iscrizioni` WHERE 1 AND stato LIKE 'Configurazione Scadut%'";
                            $titolo = 'Abbonamenti Scaduti';
                            $icona = 'fa fa-users';
                            $colore = 'red-thunderbird';
                            $link = '';
                            stampa_dashboard_stat_v2($sql_007, $titolo, $icona, $colore, $link);
                            ?>
                            </div>
                            <div class="col-md-6 col-sm-6">
                            <?php
                            $sql_007 = "SELECT COUNT(*) as conteggio FROM `lista_iscrizioni` WHERE abbonamento='0' AND stato LIKE 'Scaduto e Disattivato'";
                            $titolo = 'Corsi Singoli Scaduti';
                            $icona = 'fa fa-users';
                            $colore = 'red-flamingo';
                            $link = '';
                            stampa_dashboard_stat_v2($sql_007, $titolo, $icona, $colore, $link);
                            ?>
                            </div>
                    </div>
                    
                    <div class="clearfix"></div>
                    
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <?php
                            
                            $sql_0001 = "CREATE TEMPORARY TABLE stat_iscrizioni_attive_tmp (SELECT
                            CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"".BASE_URL."/moduli/anagrafiche/dettaglio.php?tbl=lista_professionisti&id=',lc.id_professionista,'\" title=\"DETTAGLIO PROFESSIONISTA\" alt=\"DETTAGLIO PROFESSIONISTA\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',
                            IF(abbonamento = 1, CONCAT('abb_',LCASE(nome_classe))
                                ,(SELECT IF(LENGTH(codice)>=1, codice, nome_prodotto) AS risultato FROM lista_corsi WHERE id = lc.id_corso)
                            ) AS sigla_corso,
                            data_inizio_iscrizione AS data_iscrizione,
                            data_fine_iscrizione AS data_scadenza,
                            (SELECT nome FROM lista_professionisti WHERE id = lc.id_professionista) AS nome,
                            (SELECT cognome FROM lista_professionisti WHERE id = lc.id_professionista) AS cognome,
                            (SELECT professione FROM lista_professionisti WHERE id = lc.id_professionista) AS professione,
                            (SELECT provincia_di_nascita FROM lista_professionisti WHERE id = lc.id_professionista) AS provincia_di_nascita,
                            (SELECT telefono FROM lista_professionisti WHERE id = lc.id_professionista) AS telefono,
                            (SELECT cellulare FROM lista_professionisti WHERE id = lc.id_professionista) AS cellulare,
                            IF(abbonamento = 1, 'ABBONAMENTO', 'CORSO') AS tipo_iscrizione
                            FROM lista_iscrizioni AS lc WHERE ((stato LIKE 'Configurazione' AND abbonamento = 1) OR abbonamento = 0) AND stato NOT LIKE '%Scadu%'
                            $where_intervallo_calendario)";
                            $dblink->query($sql_0001, true);
                            
                            stampa_table_datatables_responsive("SELECT * FROM stat_iscrizioni_attive_tmp", "Utenti Attivi $titolo_intervallo", "tabella_base1");
                            ?>
                        </div>
                    </div>
                    
                    <div class="clearfix"></div>
                    
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <?php
                            
                            $sql_0001 = "CREATE TEMPORARY TABLE stat_iscrizioni_x_territorio_tmp (SELECT
                            IF(abbonamento = 1, CONCAT('abb_',LCASE(nome_classe))
                                ,(SELECT IF(LENGTH(codice)>=1, codice, nome_prodotto) AS risultato FROM lista_corsi WHERE id = lc.id_corso)
                            ) AS sigla_corso,
                            (SELECT provincia_di_nascita FROM lista_professionisti WHERE id = lc.id_professionista) AS provincia_di_nascita,
                            nome_classe,
                            IF(abbonamento = 1, 'ABBONAMENTO', 'CORSO') AS tipo_iscrizione
                            FROM lista_iscrizioni AS lc WHERE ((stato LIKE 'Configurazione' AND abbonamento = 1) OR abbonamento = 0) AND stato NOT LIKE '%Scadu%'
                            $where_intervallo_calendario)";
                            $dblink->query($sql_0001, true);
                            
                            stampa_table_datatables_responsive("SELECT sigla_corso, provincia_di_nascita AS provincia, COUNT(provincia_di_nascita), (SELECT regione_province FROM lista_province WHERE sigla_province = provincia_di_nascita) AS regione, nome_classe FROM stat_iscrizioni_x_territorio_tmp GROUP BY nome_classe, provincia_di_nascita", "Utenti Attivi per Territorio $titolo_intervallo", "tabella_base2");
                            ?>
                        </div>
                    </div>


                    <!-- END ROW 3-->
                </div>
                <!-- END CONTENT BODY -->
            </div>
            <!-- END CONTENT -->
            <!-- BEGIN QUICK SIDEBAR -->
            <!-- END QUICK SIDEBAR -->
        </div>
        <!-- END CONTAINER -->
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
        <script src="<?= BASE_URL ?>/assets/global/plugins/bootstrap-multiselect/js/bootstrap-multiselect.js" type="text/javascript"></script>
        <script src="//www.google.com/jsapi" type="text/javascript"></script>
        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN THEME GLOBAL SCRIPTS -->
        <script src="<?= BASE_URL ?>/assets/global/scripts/app.min.js" type="text/javascript"></script>
        <!-- END THEME GLOBAL SCRIPTS -->
        <!-- BEGIN PAGE LEVEL SCRIPTS
        <script src="<?= BASE_URL ?>/assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>-->

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

                $('#id_agente, #id_prodotto, #id_campagna, #id_tipo_marketing, #escludi_FREE').on('change', function(ev, picker) {
                    document.formIntervallo.submit();
                });

                $('#intervallo_data').on('change', function(ev, picker) {
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
        <script src="<?= BASE_URL ?>/moduli/iscrizioni/scripts/funzioni.js" type="text/javascript"></script>
    </body>
</html>
