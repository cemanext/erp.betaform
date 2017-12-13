<?php
include_once('../../config/connDB.php');
include_once(BASE_ROOT . 'config/confAccesso.php');
require_once(BASE_ROOT.'config/confPermessi.php');

//RECUPERO LA VARIABILE POST DAL FORM defaultrange

if(isset($_POST['id_docente']) && count($_POST['id_docente'])>0){
    $whereDocente = "AND (";
    
    foreach ($_POST['id_agente'] as $idAgente) {
        $whereDocente.= "id_docente='".$idAgente."' OR ";
    }
    
    $whereDocente = substr($whereDocente, 0, -4). ")";
    
    $id_agente_post = $_POST['id_agente'];
}else{
    $whereDocente = "";
    
    $id_docente_post = array();
}

if(isset($_POST['id_corso']) && count($_POST['id_corso'])>0){
    
    $whereCorsoIdCal = "AND cal.id_prodotto IN (SELECT lc.id_prodotto FROM lista_corsi AS lc WHERE (";
    $whereCorsoId = "AND (";
    
    foreach ($_POST['id_corso'] as $idCorso) {
        $whereCorsoIdCal.= "lc.id='".$idCorso."' OR ";
        $whereCorsoId.= "id='".$idCorso."' OR ";
    }
    
    $whereCorsoIdCal = substr($whereCorsoIdCal, 0, -4). "))";
    $whereCorsoId = substr($whereCorsoId, 0, -4). ")";
    
    $id_corso_post = $_POST['id_corso'];
}else{
    $whereCampagna = "";
    $whereCorsoId = "";
    
    $id_corso_post = array();
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
        $where_intervallo_calendario = " AND data = '" . GiraDataOra($data_in) . "' ";
        $where_intervallo_fatture_all = " $whereDocenteFattAll $whereCampagnaFattAll $whereProdottoFattAll AND lf.data_creazione =  '" . GiraDataOra($data_in) . "' ";
    }else{
        $where_intervallo_calendario = " AND data BETWEEN '" . GiraDataOra($data_in) . "' AND  '" . GiraDataOra($data_out) . "'";
   }
    //echo '<h1>$intervallo_data = '.$intervallo_data.'</h1>';
} else {
    $dataMax = $dblink->get_field("SELECT MAX(data) FROM calendario WHERE etichetta LIKE 'Calendario%'");
    //$dataMin = $dblink->get_field("SELECT MIN(data) FROM calendario WHERE etichetta LIKE 'Calendario%' AND data_fine >= CURDATE()");
    $dataMin = date("Y-m-d");
    //$where_intervallo_calendario = " AND YEAR(data)=YEAR(CURDATE()) AND MONTH(data)=MONTH(CURDATE())";
    $where_intervallo_calendario = " AND data BETWEEN '" . $dataMin . "' AND  '" . $dataMax . "'";
    
    //$titolo_intervallo = " del mese in corso";
    $titolo_intervallo = " dal  " . GiraDataOra($dataMin) . " al  " . GiraDataOra($dataMax) . "";
    $_POST['intervallo_data'] = GiraDataOra($dataMin)." al ".GiraDataOra($dataMax);
    $setDataCalIn = GiraDataOra($dataMin);
    $setDataCalOut = GiraDataOra($dataMax);
    
    $_POST['id_corso'] = "";
    $_POST['id_docente'] = "";
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
        <link href="<?= BASE_URL ?>/assets/global/plugins/bootstrap-multiselect/css/bootstrap-multiselect.css" rel="stylesheet" type="text/css" />
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
        <link rel="shortcut icon" href="favicon.ico" /> 
        <style type="text/css">
            .multiselect-container>li {
                padding-left: 10px;
            }
        </style>
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
                                            <center><small>Risultati <?= $titolo_intervallo; ?></small></center>
                                        </div>
                                        <div class="col-md-6">
                                            <?=print_multi_select("SELECT id AS valore, nome_prodotto AS nome FROM lista_corsi WHERE 1 ORDER BY nome ASC", "id_corso[]", $id_corso_post, "", false, 'mt-multiselect', 'data-none-selected="Seleziona Corso"') ?>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <div class="clearfix"></div>
                    <!-- END PAGE BAR -->
                    <!-- BEGIN PAGE TITLE-->
                    <!-- END PAGE TITLE-->
                    <!-- END PAGE HEADER-->
                    <!-- BEGIN DASHBOARD STATS 1-->
                    
                    <div class="clearfix"></div>
                    
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <?php
                            
                            $sql_0001 = "CREATE TEMPORARY TABLE stat_corsi_elearning_tmp (SELECT
                            IF(LENGTH(codice)>=1, codice, nome_prodotto) AS sigla_corso,
                            data_creazione_corso,
                            (SELECT COUNT(*) AS conteggio FROM lista_iscrizioni AS li WHERE li.id_corso = lc.id AND avanzamento_completamento >= 50) AS numero_frequentanti,
                            round(durata/60,2) AS numero_ore_corso,
                            round((durata/60)*300,2) AS costo_didattica,
                            nome_docente AS Docente
                            FROM lista_corsi AS lc WHERE 1 AND id_corso_moodle > 0 AND stato LIKE 'Attivo' $whereCorsoId)
                            ";
                            $dblink->query($sql_0001, true);
                            
                            stampa_table_datatables_responsive("SELECT * FROM stat_corsi_elearning_tmp", "Analisi Corsi E-Learning", "tabella_base1");
                            ?>
                        </div>
                    </div>
                    
                    <div class="clearfix"></div>
                    
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <?php
                            
                            $sql_1001 = "
                            CREATE TEMPORARY TABLE stat_corsi_esami_aula_tmp (SELECT
                            (SELECT IF(LENGTH(codice)>=1, codice, nome) AS codice_corso FROM lista_prodotti AS lp WHERE lp.id = cal.id_prodotto) AS sigla_corso,
                            IF(cal.etichetta LIKE '%Corsi', cal.data, '') AS data_inizio,
                            data_fine,
                            IF(cal.etichetta LIKE '%Esami', cal.data, '') AS data_esame,
                            (SELECT IF(COUNT(*)>0,'SI','NO') AS risultato FROM matrice_corsi_docenti AS mcd WHERE mcd.id_calendario = cal.id AND mcd.stato LIKE 'Attivo' ) AS incarico_docente,
                            '' AS convocazione_inviata,
                            '' AS esito_questionari_di_gradimento,
                            (SELECT COUNT(*) AS conteggo FROM calendario AS ca WHERE ca.id_prodotto = cal.id_prodotto AND ca.etichetta LIKE 'Nuova Richiesta' AND ca.datainsert = CURDATE() ) AS tot_richieste_oggi,
                            (SELECT COUNT(*) AS conteggo FROM calendario AS ca WHERE ca.id_prodotto = cal.id_prodotto AND ca.etichetta LIKE 'Iscrizione%' AND ca.stato LIKE 'Iscritto' AND ca.datainsert = CURDATE() AND ca.data = cal.data ) AS tot_iscritti_oggi,
                            (SELECT COUNT(*) AS conteggo FROM calendario AS ca WHERE ca.id_prodotto = cal.id_prodotto AND ca.etichetta LIKE 'Nuova Richiesta' ) AS tot_richieste,
                            (SELECT COUNT(*) AS conteggo FROM calendario AS ca WHERE ca.id_prodotto = cal.id_prodotto AND ca.etichetta LIKE 'Iscrizione%' AND (ca.stato LIKE 'Iscritto' OR ca.stato LIKE 'Venduto') AND ca.data = cal.data ) AS tot_iscritti,
                            (SELECT COUNT(*) AS conteggo FROM calendario AS ca WHERE ca.id_prodotto = cal.id_prodotto AND ca.etichetta LIKE 'Nuova Richiesta' AND (ca.stato LIKE 'Richiamare' OR ca.stato LIKE 'Mai Contattato' )) AS tot_richiami,
                            (SELECT COUNT(*) AS conteggo FROM calendario AS ca WHERE ca.id_prodotto = cal.id_prodotto AND ca.etichetta LIKE 'Nuova Richiesta' AND ca.stato LIKE 'Negativo' ) AS tot_negativi,
                            (SELECT round(durata/60,2) AS conteggio FROM lista_corsi AS lc WHERE lc.id_prodotto = cal.id_prodotto) AS numero_ore_corso,
                            round(cal.numerico_3+cal.numerico_4+cal.numerico_5,2) AS costo_didattica,
                            '0' AS costo_del_marketing,
                            '0' AS costo_del_personale,
                            (SELECT IF(SUM(ABS(lfd.prezzo_prodotto))>0, SUM(ABS(lfd.prezzo_prodotto)), 0) AS conteggio FROM lista_fatture_dettaglio AS lfd INNER JOIN lista_fatture AS lf ON lfd.id_fattura = lf.id INNER JOIN calendario AS ca ON lf.id_preventivo = ca.id_preventivo WHERE (lf.stato LIKE 'In Attesa' OR lf.stato LIKE 'Pagata%') AND lf.tipo LIKE 'Fattura%' AND lfd.id_prodotto=cal.id_prodotto AND ca.etichetta LIKE 'Iscrizione%' AND ca.data=cal.data) AS Fatturato,
                            (SELECT IF(SUM(ABS(lfd.prezzo_prodotto))>0, SUM(ABS(lfd.prezzo_prodotto)), 0) AS conteggio FROM lista_fatture_dettaglio AS lfd INNER JOIN lista_fatture AS lf ON lfd.id_fattura = lf.id INNER JOIN calendario AS ca ON lf.id_preventivo = ca.id_preventivo WHERE lf.stato LIKE 'Nota di Credito%' AND lf.tipo LIKE 'Nota di Credito%' AND lfd.id_prodotto=cal.id_prodotto AND ca.etichetta LIKE 'Iscrizione%' AND ca.data=cal.data) AS Fatturato_Annullato,
                            (SELECT IF(SUM(ABS(lfd.prezzo_prodotto))>0, SUM(ABS(lfd.prezzo_prodotto)), 0) AS conteggio FROM lista_fatture_dettaglio AS lfd INNER JOIN lista_fatture AS lf ON lfd.id_fattura = lf.id INNER JOIN calendario AS ca ON lf.id_preventivo = ca.id_preventivo WHERE lf.stato LIKE 'Pagata%' AND lf.tipo LIKE 'Fattura%' AND lfd.id_prodotto=cal.id_prodotto AND ca.etichetta LIKE 'Iscrizione%' AND ca.data=cal.data) AS Fatturato_Incassato,
                            (SELECT IF(SUM(ABS(lfd.prezzo_prodotto))>0, SUM(ABS(lfd.prezzo_prodotto)), 0) AS conteggio FROM lista_fatture_dettaglio AS lfd INNER JOIN lista_fatture AS lf ON lfd.id_fattura = lf.id INNER JOIN calendario AS ca ON lf.id_preventivo = ca.id_preventivo WHERE lf.stato LIKE 'In Attesa' AND lf.tipo LIKE 'Fattura%' AND lfd.id_prodotto=cal.id_prodotto AND ca.etichetta LIKE 'Iscrizione%' AND ca.data=cal.data) AS Fatturato_da_Incassare
                            FROM calendario AS cal WHERE 1 AND cal.etichetta LIKE 'Calendario%' $where_intervallo_calendario $whereCorsoIdCal
                            );
                            ";
                            $dblink->query($sql_1001, true);
                            
                            $sql_1002 = "
                                CREATE TEMPORARY TABLE stat_corsi_esami_aula_ok (SELECT
                                sigla_corso,
                                data_inizio,
                                data_fine,
                                data_esame,
                                incarico_docente,
                                convocazione_inviata,
                                esito_questionari_di_gradimento,
                                tot_richieste_oggi,
                                tot_iscritti_oggi,
                                tot_richieste,
                                tot_iscritti,
                                tot_richiami,
                                tot_negativi,
                                numero_ore_corso,
                                costo_didattica,
                                round(15*tot_richieste, 2) AS costo_marketing,
                                round(Fatturato*0.2, 2) AS costo_personale,
                                Fatturato AS fatturato_lordo,
                                Fatturato_Annullato,
                                (Fatturato-Fatturato_Annullato) AS fatturato_netto,
                                Fatturato_Incassato,
                                Fatturato_da_Incassare,
                                round(((Fatturato-Fatturato_Annullato) - costo_didattica - round(15*tot_richieste, 2) - round(Fatturato*0.2, 2)), 2) AS primo_margine
                                FROM stat_corsi_esami_aula_tmp);
                            ";
                            $dblink->query($sql_1002, true);
                            
                            $sql_1003 = "
                                CREATE TEMPORARY TABLE stat_corsi_esami_aula_tot (SELECT
                                '<b>TUTTI</b>' AS sigla_corso,
                                '' AS data_inizio,
                                '' AS data_fine,
                                '' AS data_esame,
                                '' AS incarico_docente,
                                '' AS convocazione_inviata,
                                '' AS esito_questionari_di_gradimento,
                                CONCAT('<b>',SUM(tot_richieste_oggi),'</b>') AS tot_richieste_oggi,
                                CONCAT('<b>',SUM(tot_iscritti_oggi),'</b>') AS tot_iscritti_oggi,
                                CONCAT('<b>',SUM(tot_richieste),'</b>') AS tot_richieste,
                                CONCAT('<b>',SUM(tot_iscritti),'</b>') AS tot_iscritti,
                                CONCAT('<b>',SUM(tot_richiami),'</b>') AS tot_richiami,
                                CONCAT('<b>',SUM(tot_negativi),'</b>') AS tot_negativi,
                                CONCAT('<b>',SUM(numero_ore_corso),'</b>') AS numero_ore_corso,
                                CONCAT('<b>',SUM(costo_didattica),'</b>') AS costo_didattica,
                                CONCAT('<b>',SUM(costo_marketing),'</b>') AS costo_marketing,
                                CONCAT('<b>',SUM(costo_personale),'</b>') AS costo_personale,
                                CONCAT('<b>',SUM(fatturato_lordo),'</b>') AS fatturato_lordo,
                                CONCAT('<b>',SUM(Fatturato_Annullato),'</b>') AS Fatturato_Annullato,
                                CONCAT('<b>',SUM(fatturato_netto),'</b>') AS fatturato_netto,
                                CONCAT('<b>',SUM(Fatturato_Incassato),'</b>') AS Fatturato_Incassato,
                                CONCAT('<b>',SUM(Fatturato_da_Incassare),'</b>') AS Fatturato_da_Incassare,
                                CONCAT('<b>',SUM(primo_margine),'</b>') AS primo_margine
                                FROM stat_corsi_esami_aula_ok);
                            ";
                            $dblink->query($sql_1003, true);
                            
                            stampa_table_datatables_responsive("SELECT * FROM stat_corsi_esami_aula_ok UNION SELECT * FROM stat_corsi_esami_aula_tot;", "Analisi corsi in aula ed esami".$titolo_intervallo, "tabella_base2");
                            
                            ?>
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
            
            ComponentsMultiselectCampagneHome.init(); 
            
        });
        
        var ComponentsMultiselectCampagneHome = function () {

            return {
                //main function to initiate the module
                init: function () {
                        $('.mt-multiselect').each(function(){
                                var btn_class = $(this).attr('class');
                                var clickable_groups = ($(this).data('clickable-groups')) ? $(this).data('clickable-groups') : false ;
                                var collapse_groups = ($(this).data('collapse-groups')) ? $(this).data('collapse-groups') : false ;
                                var drop_right = ($(this).data('drop-right')) ? $(this).data('drop-right') : false ;
                                var drop_up = ($(this).data('drop-up')) ? $(this).data('drop-up') : false ;
                                var select_all = ($(this).data('select-all')) ? $(this).data('select-all') : false ;
                                var width = ($(this).data('width')) ? $(this).data('width') : '' ;
                                var height = ($(this).data('height')) ? $(this).data('height') : '' ;
                                var filter = ($(this).data('filter')) ? $(this).data('filter') : false ;
                                var noneText = ($(this).data('none-selected')) ? $(this).data('none-selected') : 'Nessuna dato selezionato' ;

                                // advanced functions
                                var onchange_function = function(option, checked, select) {
                                alert('Changed option ' + $(option).val() + '.');
                            }
                            var dropdownshow_function = function(event) {
                                alert('Dropdown shown.');
                            }
                            var dropdownhide_function = function(event) {
                                document.formIntervallo.submit();
                            }

                            // init advanced functions
                            var onchange = ($(this).data('action-onchange') == true) ? onchange_function : '';
                            var dropdownshow = ($(this).data('action-dropdownshow') == true) ? dropdownshow_function : '';
                            var dropdownhide = ($(this).data('action-dropdownhide') == true) ? dropdownhide_function : '';

                            // template functions
                            // init variables
                            var li_template;
                            if ($(this).attr('multiple')){
                                li_template = '<li class="mt-checkbox-list"><a href="javascript:void(0);"><label class="mt-checkbox"> <span></span></label></a></li>';
                                } else {
                                        li_template = '<li><a href="javascript:void(0);"><label></label></a></li>';
                                }

                            // init multiselect
                                $(this).multiselect({
                                        enableClickableOptGroups: clickable_groups,
                                        enableCollapsibleOptGroups: collapse_groups,
                                        disableIfEmpty: true,
                                        enableFiltering: filter,
                                        includeSelectAllOption: select_all,
                                        dropRight: drop_right,
                                        buttonWidth: width,
                                        maxHeight: height,
                                        onChange: onchange,
                                        onDropdownShow: dropdownshow,
                                        onDropdownHide: dropdownhide,
                                        buttonClass: btn_class,
                                        nonSelectedText: noneText,
                                        //optionClass: function(element) { return "mt-checkbox"; },
                                        //optionLabel: function(element) { console.log(element); return $(element).html() + '<span></span>'; },
                                        /*templates: {
                                        li: li_template,
                                    }*/
                                });   
                        });
                }
            };

        }();
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
    <script src="<?= BASE_URL ?>/moduli/corsi/scripts/funzioni.js" type="text/javascript"></script>
    <!-- END THEME LAYOUT SCRIPTS -->
</body>
</html>
