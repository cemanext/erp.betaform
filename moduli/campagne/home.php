<?php
include_once('../../config/connDB.php');
include_once(BASE_ROOT . 'config/confAccesso.php');
require_once(BASE_ROOT.'config/confPermessi.php');

//RECUPERO LA VARIABILE POST DAL FORM defaultrange
$togliVuote = true;

if(isset($_POST['id_agente']) && count($_POST['id_agente'])>0){
    $whereCommerciale = "AND (";
    $whereCommercialeAll = "AND (";
    $whereCommercialeCal = "AND (";
    $whereCommercialeCalAll = "AND (";
    $whereCommercialeFatt = "AND (";
    $whereCommercialeFattAll = "AND (";
    
    foreach ($_POST['id_agente'] as $idAgente) {
        $whereCommerciale.= "id_agente='".$idAgente."' OR ";
        $whereCommercialeAll.= "lp.id_agente='".$idAgente."' OR ";
        $whereCommercialeCal.= "id_agente='".$idAgente."' OR ";
        $whereCommercialeCalAll.= "ca.id_agente='".$idAgente."' OR ";
        $whereCommercialeFatt.= "lista_fatture.id_agente='".$idAgente."' OR ";
        $whereCommercialeFattAll.= "lf.id_agente='".$idAgente."' OR ";
    }
    
    $whereCommerciale = substr($whereCommerciale, 0, -4). ")";
    $whereCommercialeAll = substr($whereCommercialeAll, 0, -4).")";
    $whereCommercialeCal = substr($whereCommercialeCal, 0, -4).")";
    $whereCommercialeCalAll = substr($whereCommercialeCalAll, 0, -4).")";
    $whereCommercialeFatt = substr($whereCommercialeFatt, 0, -4).")";
    $whereCommercialeFattAll = substr($whereCommercialeFattAll, 0, -4).")";
    /*$whereCommerciale = "AND id_agente='".$_POST['id_agente'] ."'";
    $whereCommercialeAll = "AND lp.id_agente='".$_POST['id_agente'] ."'";
    $whereCommercialeCal = "AND id_agente='".$_POST['id_agente'] ."'";
    $whereCommercialeCalAll = "AND ca.id_agente='".$_POST['id_agente'] ."'";
    $whereCommercialeFatt = "AND lista_fatture.id_agente='".$_POST['id_agente'] ."'";
    $whereCommercialeFattAll = "AND lf.id_agente='".$_POST['id_agente'] ."'";*/
    
    $id_agente_post = $_POST['id_agente'];
}else{
    $whereCommerciale = "";
    $whereCommercialeAll = "";
    $whereCommercialeCal = "";
    $whereCommercialeCalAll = "";
    $whereCommercialeFatt = "";
    $whereCommercialeFattAll = "";
    
    $id_agente_post = array();
}

if(isset($_POST['id_campagna']) && count($_POST['id_campagna'])>0){
    $togliVuote = false;
    $whereCampagnaIdTipoMK = "AND ag.id IN (SELECT ac.id_tipo_marketing FROM lista_campagne AS ac WHERE (";
    $whereCampagnaId = "AND (";
    $whereCampagna = "AND (";
    $whereCampagnaAll = "AND (";
    $whereCampagnaCal = "AND (";
    $whereCampagnaCalAll = "AND (";
    $whereCampagnaFatt = "AND (";
    $whereCampagnaFattAll = "AND (";
    
    foreach ($_POST['id_campagna'] as $idCampagna) {
        $whereCampagnaIdTipoMK.= "ac.id='".$idCampagna."' OR ";
        $whereCampagnaId.= "id='".$idCampagna."' OR ";
        $whereCampagna.= "id_campagna='".$idCampagna."' OR ";
        $whereCampagnaAll.= "lp.id_campagna='".$idCampagna."' OR ";
        $whereCampagnaCal.= "id_campagna='".$idCampagna."' OR ";
        $whereCampagnaCalAll.= "ca.id_campagna='".$idCampagna."' OR ";
        $whereCampagnaFatt.= "lista_fatture.id_campagna='".$idCampagna."' OR ";
        $whereCampagnaFattAll.= "lf.id_campagna='".$idCampagna."' OR ";
    }
    
    $whereCampagnaIdTipoMK = substr($whereCampagnaIdTipoMK, 0, -4). "))";
    $whereCampagnaId = substr($whereCampagnaId, 0, -4). ")";
    $whereCampagna = substr($whereCampagna, 0, -4). ")";
    $whereCampagnaAll = substr($whereCampagnaAll, 0, -4).")";
    $whereCampagnaCal = substr($whereCampagnaCal, 0, -4).")";
    $whereCampagnaCalAll = substr($whereCampagnaCalAll, 0, -4).")";
    $whereCampagnaFatt = substr($whereCampagnaFatt, 0, -4).")";
    $whereCampagnaFattAll = substr($whereCampagnaFattAll, 0, -4).")";
    
    /*$whereCampagnaId = "AND id='".$_POST['id_campagna'] ."'";
    $whereCampagna = "AND id_campagna='".$_POST['id_campagna'] ."'";
    $whereCampagnaAll = "AND lp.id_campagna='".$_POST['id_campagna'] ."'";
    $whereCampagnaCal = "AND id_campagna='".$_POST['id_campagna'] ."'";
    $whereCampagnaCalAll = "AND cp.id_campagna='".$_POST['id_campagna'] ."'";
    $whereCampagnaFatt = "AND lista_fatture.id_campagna='".$_POST['id_campagna'] ."'";
    $whereCampagnaFattAll = "AND lf.id_campagna='".$_POST['id_campagna'] ."'";*/
    
    $id_campagna_post = $_POST['id_campagna'];
}else{
    $whereCampagna = "";
    $whereCampagnaId = "";
    $whereCampagnaIdTipoMK = "";
    $whereCampagnaAll = "";
    $whereCampagnaCal = "";
    $whereCampagnaCalAll = "";
    $whereCampagnaFatt = "";
    $whereCampagnaFattAll = "";
    
    $id_campagna_post = array();
}

if(isset($_POST['id_tipo_marketing']) && count($_POST['id_tipo_marketing'])>0){
    
    $whereTipoMarketingId = "AND (";
    $whereTipoMarketing = "AND (";
    $whereTipoMarketingAll = "AND (";
    $whereTipoMarketingCal = "AND (";
    $whereTipoMarketingCalAll = "AND (";
    $whereTipoMarketingFatt = "AND (";
    $whereTipoMarketingFattAll = "AND (";
    
    foreach ($_POST['id_tipo_marketing'] as $idTipoMarketing) {
        $whereTipoMarketingId.= "ag.id='".$idTipoMarketing."' OR ";
        $whereTipoMarketing.= "ag.id_tipo_marketing='".$idTipoMarketing."' OR ";
        $whereTipoMarketingAll.= "lp.id_tipo_marketing='".$idTipoMarketing."' OR ";
        $whereTipoMarketingCal.= "id_tipo_marketing='".$idTipoMarketing."' OR ";
        $whereTipoMarketingCalAll.= "cp.id_tipo_marketing='".$idTipoMarketing."' OR ";
        $whereTipoMarketingFatt.= "lista_fatture.id_tipo_marketing='".$idTipoMarketing."' OR ";
        $whereTipoMarketingFattAll.= "lf.id_tipo_marketing='".$idTipoMarketing."' OR ";
    }
    
    $whereTipoMarketingId = substr($whereTipoMarketingId, 0, -4). ")";
    $whereTipoMarketing = substr($whereTipoMarketing, 0, -4). ")";
    $whereTipoMarketingAll = substr($whereTipoMarketingAll, 0, -4).")";
    $whereTipoMarketingCal = substr($whereTipoMarketingCal, 0, -4).")";
    $whereTipoMarketingCalAll = substr($whereTipoMarketingCalAll, 0, -4).")";
    $whereTipoMarketingFatt = substr($whereTipoMarketingFatt, 0, -4).")";
    $whereTipoMarketingFattAll = substr($whereTipoMarketingFattAll, 0, -4).")";
    
    /*$whereTipoMarketing = "AND ag.id_tipo_marketing='".$_POST['id_tipo_marketing'] ."'";
    $whereTipoMarketingAll = "AND lp.id_tipo_marketing='".$_POST['id_tipo_marketing'] ."'";
    $whereTipoMarketingCal = "AND ca.id_tipo_marketing='".$_POST['id_tipo_marketing'] ."'";
    $whereTipoMarketingCalAll = "AND cp.id_tipo_marketing='".$_POST['id_tipo_marketing'] ."'";
    $whereTipoMarketingFatt = "AND lista_fatture.id_tipo_marketing='".$_POST['id_tipo_marketing'] ."'";
    $whereTipoMarketingFattAll = "AND lf.id_tipo_marketing='".$_POST['id_tipo_marketing'] ."'";*/
    
    $id_tipo_marketing_post = $_POST['id_tipo_marketing'];
}else{
    $whereTipoMarketingId = "";
    $whereTipoMarketing = "";
    $whereTipoMarketingAll = "";
    $whereTipoMarketingCal = "";
    $whereTipoMarketingCalAll = "";
    $whereTipoMarketingFatt = "";
    $whereTipoMarketingFattAll = "";
    
    $id_tipo_marketing_post = array();
}

 if(isset($_POST['id_prodotto']) && count($_POST['id_prodotto'])>0){
    
    $whereProdotto = "AND (";
    $whereProdottoAll = "AND (";
    $whereProdottoCal = "AND (";
    $whereProdottoCalAll = "AND (";
    $whereProdottoFatt = "AND (";
    $whereProdottoFattAll = "AND (";
    
    foreach ($_POST['id_prodotto'] as $idProdotto) {
        $whereProdotto.= "id IN (SELECT lpd.id_preventivo FROM lista_preventivi_dettaglio AS lpd WHERE lpd.id_prodotto='".$idProdotto."' GROUP BY lpd.id_preventivo) OR ";
        $whereProdottoAll.= "lp.id IN (SELECT lpd.id_preventivo FROM lista_preventivi_dettaglio AS lpd WHERE lpd.id_prodotto='".$idProdotto."' GROUP BY lpd.id_preventivo) OR ";
        $whereProdottoCal.= "id_prodotto='".$idProdotto."' OR ";
        $whereProdottoCalAll.= "ca.id_prodotto='".$idProdotto."' OR ";
        $whereProdottoFatt.= "lista_fatture.id IN (SELECT lfd.id_fattura FROM lista_fatture_dettaglio AS lfd WHERE lfd.id_prodotto='".$idProdotto."' GROUP BY lfd.id_fattura) OR ";
        $whereProdottoFattAll.= "lf.id IN (SELECT lfd.id_fattura FROM lista_fatture_dettaglio AS lfd WHERE lfd.id_prodotto='".$idProdotto."' GROUP BY lfd.id_fattura) OR ";
    }
    
    $whereProdotto = substr($whereProdotto, 0, -4). ")";
    $whereProdottoAll = substr($whereProdottoAll, 0, -4).")";
    $whereProdottoCal = substr($whereProdottoCal, 0, -4).")";
    $whereProdottoCalAll = substr($whereProdottoCalAll, 0, -4).")";
    $whereProdottoFatt = substr($whereProdottoFatt, 0, -4).")";
    $whereProdottoFattAll = substr($whereProdottoFattAll, 0, -4).")";
     
    /*$whereProdotto = "AND id IN (SELECT lpd.id_preventivo FROM lista_preventivi_dettaglio AS lpd WHERE lpd.id_prodotto='".$_POST['id_prodotto'] ."' GROUP BY lpd.id_preventivo)";
    $whereProdottoAll = "AND lp.id IN (SELECT lpd.id_preventivo FROM lista_preventivi_dettaglio AS lpd WHERE lpd.id_prodotto='".$_POST['id_prodotto'] ."' GROUP BY lpd.id_preventivo)";
    $whereProdottoCal = "AND id_prodotto='".$_POST['id_prodotto'] ."'";
    $whereProdottoCalAll = "AND ca.id_prodotto='".$_POST['id_prodotto'] ."'";
    $whereProdottoFatt = "AND lista_fatture.id IN (SELECT lfd.id_fattura FROM lista_fatture_dettaglio AS lfd WHERE lfd.id_prodotto='".$_POST['id_prodotto'] ."' GROUP BY lfd.id_fattura)";
    $whereProdottoFattAll = "AND lf.id IN (SELECT lfd.id_fattura FROM lista_fatture_dettaglio AS lfd WHERE lfd.id_prodotto='".$_POST['id_prodotto'] ."' GROUP BY lfd.id_fattura)";*/
    
    $id_prodotto_post = $_POST['id_prodotto'];
}else{
    $whereProdotto = "";
    $whereProdottoAll = "";
    $whereProdottoCal = "";
    $whereProdottoCalAll = "";
    $whereProdottoFatt = "";
    $whereProdottoFattAll = "";
    
    $id_prodotto_post = array();
}

if(isset($_POST['escludi_FREE']) && $_POST['escludi_FREE'] == "0"){
    $whereSezionaleFREE = "";
    $whereSezionaleFREEall = "";
    $whereSezionaleFREEfatt = "";
    $whereSezionaleFREEfattAll = "";
}else{
    $whereSezionaleFREE = " AND sezionale NOT LIKE 'FREE'";
    $whereSezionaleFREEall = " AND lp.sezionale NOT LIKE 'FREE'";
    $whereSezionaleFREEfatt = " AND lista_fatture.sezionale NOT LIKE 'FREE'";
    $whereSezionaleFREEfattAll = " AND lf.sezionale NOT LIKE 'FREE'";
}

if(isset($_POST['escludi_DISATTIVE']) && $_POST['escludi_DISATTIVE'] == "0"){
    $whereCampagneDisattive = "";
    $whereCampagneDisattiveAll = "";
    /*$whereCampagneDisattiveFatt = "";
    $whereCampagneDisattiveFattAll = "";*/
}else{
    $whereCampagneDisattive = " AND (ag.stato NOT LIKE '%Terminata%' OR ag.stato NOT LIKE '%Non Attiva%')";
    $whereCampagneDisattiveAll = " AND ag.id IN (SELECT id_tipo_marketing FROM lista_campagne WHERE ag.stato NOT LIKE '%Terminata%' OR ag.stato NOT LIKE '%Non Attiva%')";
    /*$whereCampagneDisattiveFatt = " AND lista_fatture.sezionale NOT LIKE 'FREE'";
    $whereCampagneDisattiveFattAll = " AND lf.sezionale NOT LIKE 'FREE'";*/
}

if(isset($_POST['intervallo_data'])) {
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
        $where_intervallo_tot = " $whereCommercialeCal $whereCampagnaCal $whereProdottoCal $whereTipoMarketingCal AND datainsert =  '" . GiraDataOra($data_in) . "' ";
        $where_intervallo = " $whereCommerciale $whereCampagna $whereProdotto AND dataagg =  '" . GiraDataOra($data_in) . "' ";
        $where_intervallo_all = " $whereCommercialeAll $whereCampagnaAll $whereProdottoAll $whereSezionaleFREEall AND lp.data_firma =  '" . GiraDataOra($data_in) . "' ";
        $where_intervallo_negativo_all = " $whereCommercialeAll $whereCampagnaAll $whereProdottoAll $whereSezionaleFREEall AND lp.data_firma =  '" . GiraDataOra($data_in) . "' ";
        //$where_intervallo_calenario = " $whereCommercialeCal $whereCampagnaCal $whereProdottoCal AND dataagg =  '" . GiraDataOra($data_in) . "' ";
        $where_intervallo_calendario_all = " $whereCommercialeCalAll $whereCampagnaCalAll $whereProdottoCalAll $whereSezionaleFREEall AND lp.dataagg =  '" . GiraDataOra($data_in) . "' ";
        $where_intervallo_fatture = " $whereCommercialeFatt $whereCampagnaFatt $whereProdottoFatt $whereSezionaleFREEfatt AND lista_fatture.data_creazione =  '" . GiraDataOra($data_in) . "' ";
        $where_intervallo_fatture_all = " $whereCommercialeFattAll $whereCampagnaFattAll $whereProdottoFattAll $whereSezionaleFREEfattAll AND lf.data_creazione =  '" . GiraDataOra($data_in) . "' ";
    }else{
        $where_intervallo_tot = " $whereCommercialeCal $whereCampagnaCal $whereProdottoCal $whereTipoMarketingCal AND datainsert BETWEEN  '" . GiraDataOra($data_in) . "' AND  '" . GiraDataOra($data_out) . "'";
        $where_intervallo = " $whereCommerciale $whereCampagna $whereProdotto AND dataagg BETWEEN  '" . GiraDataOra($data_in) . "' AND  '" . GiraDataOra($data_out) . "'";
        $where_intervallo_all = " $whereCommercialeAll $whereCampagnaAll $whereProdottoAll $whereSezionaleFREEall AND lp.data_firma BETWEEN  '" . GiraDataOra($data_in) . "' AND  '" . GiraDataOra($data_out) . "'";
        $where_intervallo_negativo_all = " $whereCommercialeAll $whereCampagnaAll $whereProdottoAll $whereSezionaleFREEall AND lp.data_firma BETWEEN  '" . GiraDataOra($data_in) . "' AND  '" . GiraDataOra($data_out) . "'";
        //$where_intervallo_calenario = " $whereCommercialeCal $whereCampagnaCal $whereProdottoCal AND dataagg BETWEEN  '" . GiraDataOra($data_in) . "' AND  '" . GiraDataOra($data_out) . "'";
        $where_intervallo_calendario_all = " $whereCommercialeCalAll $whereCampagnaCalAll $whereProdottoCalAll $whereSezionaleFREEall AND lp.dataagg BETWEEN  '" . GiraDataOra($data_in) . "' AND  '" . GiraDataOra($data_out) . "'";
        $where_intervallo_fatture = " $whereCommercialeFatt $whereCampagnaFatt $whereProdottoFatt $whereSezionaleFREEfatt AND lista_fatture.data_creazione BETWEEN  '" . GiraDataOra($data_in) . "' AND  '" . GiraDataOra($data_out) . "'";
        $where_intervallo_fatture_all = " $whereCommercialeFattAll $whereCampagnaFattAll $whereProdottoFattAll $whereSezionaleFREEfattAll AND lf.data_creazione BETWEEN  '" . GiraDataOra($data_in) . "' AND  '" . GiraDataOra($data_out) . "'";
    }
    //echo '<h1>$intervallo_data = '.$intervallo_data.'</h1>';
} else {
    $where_intervallo_tot = " $whereCommercialeCal $whereCampagnaCal $whereProdottoCal $whereTipoMarketingCal AND YEAR(datainsert)=YEAR(CURDATE()) AND MONTH(datainsert)=MONTH(CURDATE())";
    $where_intervallo = " $whereCommerciale $whereCampagna $whereProdotto AND YEAR(dataagg)=YEAR(CURDATE()) AND MONTH(dataagg)=MONTH(CURDATE())";
    $where_intervallo_all = " $whereCommercialeAll $whereCampagnaAll $whereProdottoAll $whereSezionaleFREEall AND YEAR(lp.data_firma)=YEAR(CURDATE()) AND MONTH(lp.data_firma)=MONTH(CURDATE())";
    $where_intervallo_negativo_all = " $whereCommercialeAll $whereCampagnaAll $whereProdottoAll $whereSezionaleFREEall AND YEAR(lp.data_firma)=YEAR(CURDATE()) AND MONTH(lp.data_firma)=MONTH(CURDATE())";
    //$where_intervallo_calenario = " $whereCommercialeCal $whereCampagnaCal $whereProdottoCal AND YEAR(dataagg)=YEAR(CURDATE()) AND MONTH(dataagg)=MONTH(CURDATE())";
    $where_intervallo_calendario_all = " $whereCommercialeCalAll $whereCampagnaCalAll $whereProdottoAll $whereSezionaleFREEall AND YEAR(lp.dataagg)=YEAR(CURDATE()) AND MONTH(lp.dataagg)=MONTH(CURDATE())";
    $where_intervallo_fatture = "  $whereCommercialeFatt $whereCampagnaFatt $whereProdottoFatt $whereSezionaleFREEfatt AND YEAR(lista_fatture.data_creazione)=YEAR(CURDATE()) AND MONTH(lista_fatture.data_creazione)=MONTH(CURDATE())";
    $where_intervallo_fatture_all = " $whereCommercialeFattAll $whereCampagnaFattAll $whereProdottoFattAll $whereSezionaleFREEfattAll AND YEAR(lf.data_creazione)=YEAR(CURDATE()) AND MONTH(lf.data_creazione)=MONTH(CURDATE())";
    
    $titolo_intervallo = " del mese in corso";
    $_POST['intervallo_data'] = "01-".date("m-Y")." al ".date("t-m-Y");
    $setDataCalIn = date("d-m-Y");
    $setDataCalOut = date("t-m-Y");
    
    $_POST['id_campagna'] = "";
    $_POST['id_prodotto'] = "";
    $_POST['id_tipo_marketing'] = "";
    $_POST['id_agente'] = "";
    $_POST['escludi_FREE'] = "1";
    $_POST['escludi_DISATTIVE'] = "1";
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
                                            <center><small>Risultati <?= $titolo_intervallo; ?></small></center>
                                        </div>
                                        <div class="col-md-6">
                                            <?=print_multi_select("SELECT id AS valore, nome as nome FROM lista_campagne WHERE 1 ORDER BY nome ASC", "id_campagna[]", $id_campagna_post, "", false, 'mt-multiselect', 'data-none-selected="Seleziona Campagna"') ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-6">
                                            <?=print_multi_select("SELECT id_prodotto AS valore, nome_prodotto AS nome FROM lista_preventivi_dettaglio WHERE id_prodotto > 0 GROUP BY id_prodotto ORDER BY nome_prodotto ASC", "id_prodotto[]", $id_prodotto_post, "", false, 'mt-multiselect', 'data-none-selected="Seleziona Prodotto"') ?>
                                        </div>
                                        <div class="col-md-6">
                                            <?=print_multi_select("SELECT id as valore, (CONCAT(cognome,' ', nome)) as nome FROM lista_password WHERE stato='Attivo' AND livello LIKE 'commerciale' ORDER BY cognome, nome ASC", "id_agente[]", $id_agente_post, "", false, 'mt-multiselect', 'data-none-selected="Seleziona Commerciale"') ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-4">
                                            <?=print_multi_select("SELECT id AS valore, nome AS nome FROM lista_tipo_marketing WHERE 1 ORDER BY nome ASC", "id_tipo_marketing[]", $id_tipo_marketing_post, "", false, 'mt-multiselect', 'data-none-selected="Seleziona Tipo Marketing"') ?>
                                        </div>
                                        <div class="col-md-8">
                                            <label class="control-label col-md-3" style="padding: 0px;">Escludi Campagne Disattive</label>
                                            <div class="col-md-3"><?=print_select_static(array("1"=>"SI", "0" => "NO"), "escludi_DISATTIVE", $_POST['escludi_DISATTIVE']); ?></div>
                                            <label class="control-label col-md-3" style="padding: 0px;">Escludi Sezionale FREE</label>
                                            <div class="col-md-3"><?=print_select_static(array("1"=>"SI", "0" => "NO"), "escludi_FREE", $_POST['escludi_FREE']); ?></div>
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
                    <div class="row" style="display: none;">
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <?php
                            $sql_001 = "SELECT COUNT(*) AS conteggio FROM calendario WHERE (stato LIKE 'Mai Contattato' OR stato LIKE 'Richiamare') $whereCommercialeCal $whereCampagnaCal $whereProdottoCal $whereTipoMarketingCal";
                            $titolo = 'Totale Richiami/Mai Contattati<br>Ancora da Gestire';
                            $icona = 'fa fa-line-chart';
                            $colore = 'yellow-lemon';
                            $link = BASE_URL.'/moduli/calendario/index.php?whrStato=ed59fefc520e30eacbb5fd110761555b&idMenu=36';
                            stampa_dashboard_stat_v2($sql_001, $titolo, $icona, $colore, $link)
                            ?>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <?php
                            $sql_001 = "SELECT COUNT(*) AS conteggio FROM calendario WHERE (stato LIKE 'In Attesa di Controllo') $whereCommercialeCal $whereCampagnaCal $whereProdottoCal $whereTipoMarketingCal ";
                            $titolo = 'Totale In Attesa di Controllo<br>Ancora da Gestire';
                            $icona = 'fa fa-line-chart';
                            $colore = 'yellow-casablanca';
                            $link = BASE_URL.'/moduli/calendario/index.php?whrStato=a7d7ab5bee5f267d23e0ff28a162bafb&idMenu=36';
                            stampa_dashboard_stat_v2($sql_001, $titolo, $icona, $colore, $link)
                            ?>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <?php
                            $sql_004 = "SELECT COUNT(*) AS conteggio FROM calendario WHERE (stato LIKE 'Venduto') $where_intervallo_calenario ";
                            $titolo = 'Totale Iscritti<br>'.$titolo_intervallo;
                            $icona = 'fa fa-line-chart';
                            $colore = 'blue-steel';
                            $link = BASE_URL.'/moduli/calendario/index.php?whrStato=0dcf93d17feb1a4f6efe62d5d2f270b2&idMenu=36';
                            stampa_dashboard_stat_v2($sql_004, $titolo, $icona, $colore, $link)
                            ?>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <?php
                            $sql_004 = "SELECT COUNT(*) AS conteggio FROM calendario WHERE (stato LIKE 'Negativo') $where_intervallo_calenario ";
                            $titolo = 'Totale Negativi<br>'.$titolo_intervallo;
                            $icona = 'fa fa-line-chart';
                            $colore = 'red-flamingo';
                            $link = BASE_URL.'/moduli/calendario/index.php?whrStato=31aa0b940088855f8a9b72946dc495ab&idMenu=36';
                            stampa_dashboard_stat_v2($sql_004, $titolo, $icona, $colore, $link)
                            ?>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="row" style="display: none;">
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <?php
                            $sql_002 = "SELECT SUM(imponibile) AS conteggio FROM lista_preventivi WHERE (stato LIKE 'Venduto' OR stato LIKE 'Chiuso') " . $where_intervallo;
                            $titolo = 'Totale Ordini Iscritti<br>' . $titolo_intervallo;
                            $icona = 'fa fa-area-chart';
                            $colore = 'blue';
                            $link = '#';
                            stampa_dashboard_stat_v2($sql_002, $titolo, $icona, $colore, $link)
                            ?>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <?php
                            $sql_003 = "SELECT SUM(imponibile) AS conteggio FROM lista_fatture WHERE (stato LIKE 'In Attesa' OR stato LIKE 'Pagata') " . $where_intervallo_fatture;
                            $titolo = 'Totale Ordini Fatturati<br>' . $titolo_intervallo;
                            $icona = 'fa fa-area-chart';
                            $colore = 'green-jungle';
                            $link = '#';
                            stampa_dashboard_stat_v2($sql_003, $titolo, $icona, $colore, $link)
                            ?>
                        </div>
                        
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <?php
                            $sql_003 = "SELECT SUM(imponibile) AS conteggio FROM lista_preventivi WHERE (stato LIKE 'Negativo') " . $where_intervallo;
                            $titolo = 'Totale Ordini Negativi<br>' . $titolo_intervallo;
                            $icona = 'fa fa-area-chart';
                            $colore = 'red-flamingo';
                            $link = '#';
                            stampa_dashboard_stat_v2($sql_003, $titolo, $icona, $colore, $link)
                            ?>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <?php
                            $sql_003 = "SELECT SUM(imponibile) AS conteggio FROM lista_preventivi WHERE (stato LIKE 'In Attesa') " . $where_intervallo;
                            $titolo = 'Totale Ordini in Trattativa<br>' . $titolo_intervallo;
                            $icona = 'fa fa-area-chart';
                            $colore = 'yellow-lemon';
                            $link = '#';
                            stampa_dashboard_stat_v2($sql_003, $titolo, $icona, $colore, $link)
                            ?>
                        </div>
                    </div>
                    
                    <div class="clearfix"></div>
                    
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <?php
                            
                            $sql_0028 = "CREATE TEMPORARY TABLE stat_marketing_home_2 (SELECT  
                            nome AS tipo_marketing,
                            SUM((SELECT COUNT(*) AS conteggio FROM calendario AS ca WHERE etichetta='Nuova Richiesta' AND ca.id_tipo_marketing = ag.id $where_intervallo_tot)) AS Tutte_Richieste,
                            SUM((SELECT COUNT(*) AS conteggio FROM calendario AS ca WHERE etichetta='Nuova Richiesta' AND (ca.stato LIKE 'Mai Contattato' OR ca.stato LIKE 'Richiamare') AND ca.id_tipo_marketing = ag.id $whereCommercialeCal $whereCampagnaCal $whereProdottoCal $whereTipoMarketingCal)) AS Richiami,
                            SUM((SELECT COUNT(*) AS conteggio FROM calendario AS ca WHERE etichetta='Nuova Richiesta' AND (ca.stato LIKE 'Mai Contattato' OR ca.stato LIKE 'Richiamare') AND ca.id_tipo_marketing = ag.id $where_intervallo_tot)) AS Tel_Richiami,
                            SUM((SELECT COUNT(*) AS conteggio_gestite FROM lista_preventivi AS lp WHERE (lp.stato LIKE 'Negativo') AND lp.id_campagna IN (SELECT ac.id FROM lista_campagne AS ac WHERE ac.id_tipo_marketing = ag.id) $where_intervallo_negativo_all)) AS Negativo,
                            SUM((SELECT COUNT(*) AS conteggio_venduti FROM lista_preventivi AS lp WHERE (lp.stato LIKE 'Chiuso') AND lp.id_campagna IN (SELECT ac.id FROM lista_campagne AS ac WHERE ac.id_tipo_marketing = ag.id) $where_intervallo_all)) AS Confermati,
                            SUM((SELECT IF(SUM(lp.imponibile)>0, SUM(lp.imponibile), 0) AS conteggio_preventivi FROM lista_preventivi AS lp WHERE (lp.stato LIKE 'Chiuso') AND lp.id_campagna IN (SELECT ac.id FROM lista_campagne AS ac WHERE ac.id_tipo_marketing = ag.id) $where_intervallo_all)) AS Ordinato_Lordo,
                            SUM((SELECT IF(SUM(ABS(lf.imponibile))>0, SUM(ABS(lf.imponibile)), 0) AS conteggio_annullate FROM lista_fatture AS lf WHERE (lf.stato LIKE 'Nota di Credito%') AND lf.tipo LIKE 'Nota di Credito%' AND lf.id_campagna IN (SELECT ac.id FROM lista_campagne AS ac WHERE ac.id_tipo_marketing = ag.id) $where_intervallo_fatture_all)) AS Fatture_Annullate,
                            SUM((SELECT IF(SUM(ABS(lf.imponibile))>0, SUM(ABS(lf.imponibile)), 0) AS conteggio_annullate FROM lista_fatture AS lf WHERE (lf.stato LIKE 'In Attesa' OR lf.stato LIKE 'Pagata%') AND lf.tipo LIKE 'Fattura%' AND lf.id_campagna IN (SELECT ac.id FROM lista_campagne AS ac WHERE ac.id_tipo_marketing = ag.id) $where_intervallo_fatture_all)) AS Fatturato
                            FROM lista_tipo_marketing AS ag WHERE 1 $whereCampagnaIdTipoMK $whereTipoMarketingId $whereCampagneDisattiveAll GROUP BY ag.id);";
                            $dblink->query($sql_0028, true);
                            //echo $dblink->get_query();
                            
                            $sql_0029 = "CREATE TEMPORARY TABLE stat_marketing_home_totale_tmp (SELECT tipo_marketing, Richiami, Tutte_Richieste, Tel_Richiami+Negativo+Confermati AS 'Tel_Gestite',"
                                    . " Confermati, Negativo, Ordinato_Lordo, Fatture_Annullate, (Ordinato_Lordo-Fatture_Annullate) AS Ordinato_Netto, "
                                    . " Fatturato AS Fatturato_Lordo, (Fatturato-Fatture_Annullate) AS Fatturato_Netto, IF(Confermati>0,ROUND((Confermati*100)/(Negativo+Confermati), 2),0) AS Realizzato,"
                                    . " IF(Ordinato_Lordo>0, ROUND((Ordinato_Lordo-Fatture_Annullate)/Confermati,2), 0) AS Media_part_su_Ordinato,"
                                    . " (Richiami+Tutte_Richieste+Tel_Richiami+Negativo+Confermati+Negativo) AS elimina_vuote"
                                    . " FROM stat_marketing_home_2);";
                            $dblink->query($sql_0029, true);
                            
                            $sql_0036 = "CREATE TEMPORARY TABLE stat_marketing_home_no_id_tmp (SELECT 
                            'Nessun Tipo Marketing' AS tipo_marketing,
                            (SELECT COUNT(*) AS conteggio FROM calendario AS ca WHERE etichetta='Nuova Richiesta' AND ca.id_campagna='0' $where_intervallo_tot) AS Tutte_Richieste,
                            (SELECT COUNT(*) AS conteggio FROM calendario AS ca WHERE etichetta='Nuova Richiesta' AND (ca.stato LIKE 'Mai Contattato' OR ca.stato LIKE 'Richiamare') AND ca.id_campagna='0') AS Richiami,
                            (SELECT COUNT(*) AS conteggio FROM calendario AS ca WHERE etichetta='Nuova Richiesta' AND (ca.stato LIKE 'Mai Contattato' OR ca.stato LIKE 'Richiamare') AND ca.id_campagna='0' $where_intervallo_tot) AS Tel_Richiami,
                            (SELECT COUNT(*) AS conteggio_gestite FROM lista_preventivi AS lp WHERE (lp.stato LIKE 'Negativo') AND lp.id_campagna='0' AND lp.id_calendario = ag.id $where_intervallo_negativo_all) AS Negativo,
                            (SELECT COUNT(*) AS conteggio_venduti FROM lista_preventivi AS lp WHERE (lp.stato LIKE 'Chiuso') AND lp.id_campagna='0' AND lp.id_calendario = ag.id $where_intervallo_all) AS Confermati,
                            (SELECT IF(SUM(lp.imponibile)>0, SUM(lp.imponibile), 0) AS conteggio_preventivi FROM lista_preventivi AS lp WHERE (lp.stato LIKE 'Chiuso') AND lp.id_campagna='0' AND lp.id_calendario = ag.id $where_intervallo_all) AS Ordinato_Lordo,
                            (SELECT IF(SUM(ABS(lf.imponibile))>0, SUM(ABS(lf.imponibile)), 0) AS conteggio_annullate FROM lista_fatture AS lf WHERE (lf.stato LIKE 'Nota di Credito%') AND lf.tipo LIKE 'Nota di Credito%' AND lf.id_campagna='0' AND lf.id_calendario = ag.id $where_intervallo_fatture_all) AS Fatture_Annullate,
                            (SELECT IF(SUM(ABS(lf.imponibile))>0, SUM(ABS(lf.imponibile)), 0) AS conteggio_annullate FROM lista_fatture AS lf WHERE (lf.stato LIKE 'In Attesa' OR lf.stato LIKE 'Pagata%') AND lf.tipo LIKE 'Fattura%' AND lf.id_campagna='0' AND lf.id_calendario = ag.id $where_intervallo_fatture_all) AS Fatturato
                            FROM calendario as ag WHERE id_tipo_marketing = 0 GROUP BY id_tipo_marketing);";
                            $dblink->query($sql_0036, true);
                            //echo $dblink->get_query();
                            
                            $sql_0037 = "CREATE TEMPORARY TABLE stat_marketing_home_no_id (SELECT tipo_marketing, Richiami, SUM(Tutte_Richieste) AS Tutte_Richieste, SUM(Tel_Richiami+Negativo+Confermati) AS 'Tel_Gestite',"
                                    . " SUM(Confermati) AS Confermati, SUM(Negativo) AS Negativo, SUM(Ordinato_Lordo) AS Ordinato_Lordo, SUM(Fatture_Annullate) AS Fatture_Annullate, SUM(Ordinato_Lordo-Fatture_Annullate) AS Ordinato_Netto, "
                                    . " SUM(Fatturato) AS Fatturato_Lordo, SUM(Fatturato-Fatture_Annullate) AS Fatturato_Netto, IF(Confermati>0,ROUND((Confermati*100)/(Negativo+Confermati), 2),0) AS Realizzato,"
                                    . " IF(Ordinato_Lordo>0, ROUND((Ordinato_Lordo-Fatture_Annullate)/Confermati,2), 0) AS Media_part_su_Ordinato"
                                    . " FROM stat_marketing_home_no_id_tmp GROUP BY tipo_marketing);";
                            $dblink->query($sql_0037, true);
                            
                            $sql_00261 = "CREATE TEMPORARY TABLE stat_marketing_tutte_le_righe (SELECT * FROM stat_marketing_home_2);";
                            $dblink->query($sql_00261, true);
                            $sql_00262 = "INSERT INTO stat_marketing_tutte_le_righe (SELECT * FROM stat_marketing_home_no_id_tmp);";
                            $dblink->query($sql_00262, true);
                            
                            /*$sql_0030 = "CREATE TEMPORARY TABLE stat_marketing_home_totale_tot_tmp (SELECT 'TOTALE', SUM(Richiami) AS Richiami, SUM(Tutte_Richieste) AS Tutte_Richieste, SUM(Tel_Richiami+Negativo+Confermati) AS 'Tel_Gestite', SUM(Confermati) AS Confermati,"
                                    . " SUM(Negativo) AS Negativo, SUM(Ordinato_Lordo) AS Ordinato_Lordo, SUM(Fatture_Annullate) AS Fatture_Annullate, SUM((Ordinato_Lordo-Fatture_Annullate)) AS Ordinato_Netto,"
                                    . " SUM(Fatturato) AS Fatturato_Lordo, SUM((Fatturato-Fatture_Annullate)) AS Fatturato_Netto, 0 AS Realizzato,"
                                    . " 0 AS Media_part_su_Fattura FROM stat_marketing_tutte_le_righe);";
                            $dblink->query($sql_0030, true);
                            
                            $sql_0031 = "CREATE TEMPORARY TABLE stat_marketing_home_totale_tot (SELECT '<b>TOTALE</b>', Richiami, Tutte_Richieste, Tel_Gestite,"
                                    . " Confermati, Negativo, Ordinato_Lordo, Fatture_Annullate, Ordinato_Netto, Fatturato_Lordo, Fatturato_Netto, ROUND((Confermati*100)/(Negativo+Confermati), 2) AS Realizzato,"
                                    . " IF(Ordinato_Lordo>0, ROUND((Ordinato_Lordo-Fatture_Annullate)/Confermati,2), 0) AS Media_part_su_Fattura"
                                    . " FROM stat_marketing_home_totale_tot_tmp);";
                            $dblink->query($sql_0031, true);*/
                            
                            $sql_0032 = "CREATE TEMPORARY TABLE stat_marketing_home_totale (SELECT tipo_marketing, Richiami, Tutte_Richieste, Tel_Gestite,"
                                    . " Confermati, Negativo, Ordinato_Lordo, Fatture_Annullate, Ordinato_Netto, "
                                    . " Fatturato_Lordo, Fatturato_Netto, Realizzato,"
                                    . " Media_part_su_Ordinato"
                                    . " FROM stat_marketing_home_totale_tmp WHERE elimina_vuote ".($togliVuote ? ">" : ">=")." 0);";
                            $dblink->query($sql_0032, true);
                            
                            stampa_table_datatables_responsive("SELECT * FROM stat_marketing_home_no_id UNION SELECT * FROM stat_marketing_home_totale", "Statistiche per TIPO MARKETING".$titolo_intervallo, "tabella_base1", COLORE_PRIMARIO, true);
                            
                            ?>
                        </div>
                    </div>
                    
                    <div class="clearfix"></div>
                    
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <?php
                            $sql_0024 = "CREATE TEMPORARY TABLE stat_campagna_home_2 (SELECT 
                            nome as Nome_Campagna,
                            (SELECT nome FROM lista_tipo_marketing WHERE id = ag.id_tipo_marketing) AS tipo_marketing,
                            (SELECT COUNT(*) AS conteggio FROM calendario AS ca WHERE etichetta='Nuova Richiesta' AND ca.id_campagna=ag.id $where_intervallo_tot) AS Tutte_Richieste,
                            (SELECT COUNT(*) AS conteggio FROM calendario AS ca WHERE etichetta='Nuova Richiesta' AND (ca.stato LIKE 'Mai Contattato' OR ca.stato LIKE 'Richiamare') AND ca.id_campagna=ag.id $whereCommercialeCal $whereCampagnaCal $whereProdottoCal $whereTipoMarketingCal) AS Richiami,
                            (SELECT COUNT(*) AS conteggio FROM calendario AS ca WHERE etichetta='Nuova Richiesta' AND (ca.stato LIKE 'Mai Contattato' OR ca.stato LIKE 'Richiamare') AND ca.id_campagna=ag.id $where_intervallo_tot) AS Tel_Richiami,
                            (SELECT COUNT(*) AS conteggio_gestite FROM lista_preventivi AS lp WHERE (lp.stato LIKE 'Negativo') AND lp.id_campagna=ag.id $where_intervallo_negativo_all) AS Negativo,
                            (SELECT COUNT(*) AS conteggio_venduti FROM lista_preventivi AS lp WHERE (lp.stato LIKE 'Chiuso') AND lp.id_campagna=ag.id $where_intervallo_all) AS Confermati,
                            (SELECT IF(SUM(lp.imponibile)>0, SUM(lp.imponibile), 0) AS conteggio_preventivi FROM lista_preventivi AS lp WHERE (lp.stato LIKE 'Chiuso') AND lp.id_campagna=ag.id $where_intervallo_all) AS Ordinato_Lordo,
                            (SELECT IF(SUM(ABS(lf.imponibile))>0, SUM(ABS(lf.imponibile)), 0) AS conteggio_annullate FROM lista_fatture AS lf WHERE (lf.stato LIKE 'Nota di Credito%') AND lf.tipo LIKE 'Nota di Credito%' AND lf.id_campagna=ag.id $where_intervallo_fatture_all) AS Fatture_Annullate,
                            (SELECT IF(SUM(ABS(lf.imponibile))>0, SUM(ABS(lf.imponibile)), 0) AS conteggio_annullate FROM lista_fatture AS lf WHERE (lf.stato LIKE 'In Attesa' OR lf.stato LIKE 'Pagata%') AND lf.tipo LIKE 'Fattura%' AND lf.id_campagna=ag.id $where_intervallo_fatture_all) AS Fatturato
                            FROM lista_campagne AS ag WHERE 1 $whereCampagnaId $whereTipoMarketing $whereCampagneDisattive);";
                            $dblink->query($sql_0024, true);
                            
                            $sql_0025 = "CREATE TEMPORARY TABLE stat_campagna_home_totale_tmp (SELECT Nome_Campagna, tipo_marketing, Richiami, Tutte_Richieste, Tel_Richiami+Negativo+Confermati AS 'Tel_Gestite',"
                                    . " Confermati, Negativo, Ordinato_Lordo, Fatture_Annullate, (Ordinato_Lordo-Fatture_Annullate) AS Ordinato_Netto, "
                                    . " Fatturato AS Fatturato_Lordo, (Fatturato-Fatture_Annullate) AS Fatturato_Netto, IF(Confermati>0,ROUND((Confermati*100)/(Negativo+Confermati), 2),0) AS Realizzato,"
                                    . " IF(Ordinato_Lordo>0, ROUND((Ordinato_Lordo-Fatture_Annullate)/Confermati,2), 0) AS Media_part_su_Ordinato,"
                                    . " (Richiami+Tutte_Richieste+Tel_Richiami+Negativo+Confermati+Negativo) AS elimina_vuote"
                                    . " FROM stat_campagna_home_2);";
                            $dblink->query($sql_0025, true);
                            
                            $sql_0034 = "CREATE TEMPORARY TABLE stat_campagna_home_no_id_tmp (SELECT 
                            'Nessuna Campagna' as Nome_Campagna,
                            'Nessun Tipo Marketing' AS tipo_marketing,
                            (SELECT COUNT(*) AS conteggio FROM calendario AS ca WHERE etichetta='Nuova Richiesta' AND ca.id_campagna='0' $where_intervallo_tot) AS Tutte_Richieste,
                            (SELECT COUNT(*) AS conteggio FROM calendario AS ca WHERE etichetta='Nuova Richiesta' AND (ca.stato LIKE 'Mai Contattato' OR ca.stato LIKE 'Richiamare') AND ca.id_campagna='0') AS Richiami,
                            (SELECT COUNT(*) AS conteggio FROM calendario AS ca WHERE etichetta='Nuova Richiesta' AND (ca.stato LIKE 'Mai Contattato' OR ca.stato LIKE 'Richiamare') AND ca.id_campagna='0' $where_intervallo_tot) AS Tel_Richiami,
                            (SELECT COUNT(*) AS conteggio_gestite FROM lista_preventivi AS lp WHERE (lp.stato LIKE 'Negativo') AND lp.id_campagna='0' $where_intervallo_negativo_all) AS Negativo,
                            (SELECT COUNT(*) AS conteggio_venduti FROM lista_preventivi AS lp WHERE (lp.stato LIKE 'Chiuso') AND lp.id_campagna='0' $where_intervallo_all) AS Confermati,
                            (SELECT IF(SUM(lp.imponibile)>0, SUM(lp.imponibile), 0) AS conteggio_preventivi FROM lista_preventivi AS lp WHERE (lp.stato LIKE 'Chiuso') AND lp.id_campagna='0' $where_intervallo_all) AS Ordinato_Lordo,
                            (SELECT IF(SUM(ABS(lf.imponibile))>0, SUM(ABS(lf.imponibile)), 0) AS conteggio_annullate FROM lista_fatture AS lf WHERE (lf.stato LIKE 'Nota di Credito%') AND lf.tipo LIKE 'Nota di Credito%' AND lf.id_campagna='0' $where_intervallo_fatture_all) AS Fatture_Annullate,
                            (SELECT IF(SUM(ABS(lf.imponibile))>0, SUM(ABS(lf.imponibile)), 0) AS conteggio_annullate FROM lista_fatture AS lf WHERE (lf.stato LIKE 'In Attesa' OR lf.stato LIKE 'Pagata%') AND lf.tipo LIKE 'Fattura%' AND lf.id_campagna='0' $where_intervallo_fatture_all) AS Fatturato
                            WHERE 1);";
                            $sql_0034 = "CREATE TEMPORARY TABLE stat_campagna_home_no_id_tmp (SELECT 
                            'Nessuna Campagna' as Nome_Campagna,
                            'Nessun Tipo Marketing' AS tipo_marketing,
                            (SELECT COUNT(*) AS conteggio FROM calendario AS ca WHERE etichetta='Nuova Richiesta' AND ca.id_campagna='0' $where_intervallo_tot) AS Tutte_Richieste,
                            (SELECT COUNT(*) AS conteggio FROM calendario AS ca WHERE etichetta='Nuova Richiesta' AND (ca.stato LIKE 'Mai Contattato' OR ca.stato LIKE 'Richiamare') AND ca.id_campagna='0') AS Richiami,
                            (SELECT COUNT(*) AS conteggio FROM calendario AS ca WHERE etichetta='Nuova Richiesta' AND (ca.stato LIKE 'Mai Contattato' OR ca.stato LIKE 'Richiamare') AND ca.id_campagna='0' $where_intervallo_tot) AS Tel_Richiami,
                            (SELECT COUNT(*) AS conteggio_gestite FROM lista_preventivi AS lp WHERE (lp.stato LIKE 'Negativo') AND lp.id_campagna='0' AND lp.id_calendario = ag.id $where_intervallo_negativo_all) AS Negativo,
                            (SELECT COUNT(*) AS conteggio_venduti FROM lista_preventivi AS lp WHERE (lp.stato LIKE 'Chiuso') AND lp.id_campagna='0' AND lp.id_calendario = ag.id $where_intervallo_all) AS Confermati,
                            (SELECT IF(SUM(lp.imponibile)>0, SUM(lp.imponibile), 0) AS conteggio_preventivi FROM lista_preventivi AS lp WHERE (lp.stato LIKE 'Chiuso') AND lp.id_campagna='0' AND lp.id_calendario = ag.id $where_intervallo_all) AS Ordinato_Lordo,
                            (SELECT IF(SUM(ABS(lf.imponibile))>0, SUM(ABS(lf.imponibile)), 0) AS conteggio_annullate FROM lista_fatture AS lf WHERE (lf.stato LIKE 'Nota di Credito%') AND lf.tipo LIKE 'Nota di Credito%' AND lf.id_campagna='0' AND lf.id_calendario = ag.id $where_intervallo_fatture_all) AS Fatture_Annullate,
                            (SELECT IF(SUM(ABS(lf.imponibile))>0, SUM(ABS(lf.imponibile)), 0) AS conteggio_annullate FROM lista_fatture AS lf WHERE (lf.stato LIKE 'In Attesa' OR lf.stato LIKE 'Pagata%') AND lf.tipo LIKE 'Fattura%' AND lf.id_campagna='0' AND lf.id_calendario = ag.id $where_intervallo_fatture_all) AS Fatturato
                            FROM calendario as ag WHERE id_campagna = 0 GROUP BY id_campagna);";
                            $dblink->query($sql_0034, true);
                            
                            $sql_0035 = "CREATE TEMPORARY TABLE stat_campagna_home_no_id (SELECT Nome_Campagna, tipo_marketing, Richiami, Tutte_Richieste, Tel_Richiami+Negativo+Confermati AS 'Tel_Gestite',"
                                    . " Confermati, Negativo, Ordinato_Lordo, Fatture_Annullate, (Ordinato_Lordo-Fatture_Annullate) AS Ordinato_Netto, "
                                    . " Fatturato AS Fatturato_Lordo, (Fatturato-Fatture_Annullate) AS Fatturato_Netto, IF(Confermati>0,ROUND((Confermati*100)/(Negativo+Confermati), 2),0) AS Realizzato,"
                                    . " IF(Ordinato_Lordo>0, ROUND((Ordinato_Lordo-Fatture_Annullate)/Confermati,2), 0) AS Media_part_su_Ordinato"
                                    . " FROM stat_campagna_home_no_id_tmp GROUP BY Nome_Campagna);";
                            $dblink->query($sql_0035, true);
                            
                            $sql_00261 = "CREATE TEMPORARY TABLE stat_tutte_le_righe (SELECT * FROM stat_campagna_home_2);";
                            $dblink->query($sql_00261, true);
                            $sql_00262 = "INSERT INTO stat_tutte_le_righe (SELECT * FROM stat_campagna_home_no_id_tmp);";
                            $dblink->query($sql_00262, true);
                            
                            $sql_0026 = "CREATE TEMPORARY TABLE stat_campagna_home_totale_tot_tmp (SELECT 'TOTALE', 'TUTTI', SUM(Richiami) AS Richiami, SUM(Tutte_Richieste) AS Tutte_Richieste, SUM(Tel_Richiami+Negativo+Confermati) AS 'Tel_Gestite', SUM(Confermati) AS Confermati,"
                                    . " SUM(Negativo) AS Negativo, SUM(Ordinato_Lordo) AS Ordinato_Lordo, SUM(Fatture_Annullate) AS Fatture_Annullate, SUM((Ordinato_Lordo-Fatture_Annullate)) AS Ordinato_Netto,"
                                    . " SUM(Fatturato) AS Fatturato_Lordo, SUM((Fatturato-Fatture_Annullate)) AS Fatturato_Netto, 0 AS Realizzato,"
                                    . " 0 AS Media_part_su_Fattura FROM stat_tutte_le_righe );";
                            $dblink->query($sql_0026, true);
                            
                            $sql_0027 = "CREATE TEMPORARY TABLE stat_campagna_home_totale_tot (SELECT '<b>TOTALE</b>', 'TUTTI', SUM(Richiami), SUM(Tutte_Richieste), SUM(Tel_Gestite),"
                                    . " SUM(Confermati), SUM(Negativo), SUM(Ordinato_Lordo), SUM(Fatture_Annullate), SUM(Ordinato_Netto), SUM(Fatturato_Lordo), SUM(Fatturato_Netto), ROUND((SUM(Confermati)*100)/(SUM(Negativo)+SUM(Confermati)), 2) AS Realizzato,"
                                    . " IF(SUM(Ordinato_Lordo)>0, ROUND((SUM(Ordinato_Lordo)-SUM(Fatture_Annullate))/SUM(Confermati),2), 0) AS Media_part_su_Fattura"
                                    . " FROM stat_campagna_home_totale_tot_tmp);";
                            $dblink->query($sql_0027, true);
                            
                            $sql_0033 = "CREATE TEMPORARY TABLE stat_campagna_home_totale (SELECT Nome_Campagna, tipo_marketing, Richiami, Tutte_Richieste, Tel_Gestite,"
                                    . " Confermati, Negativo, Ordinato_Lordo, Fatture_Annullate, Ordinato_Netto, "
                                    . " Fatturato_Lordo, Fatturato_Netto, Realizzato,"
                                    . " Media_part_su_Ordinato"
                                    . " FROM stat_campagna_home_totale_tmp WHERE elimina_vuote ".($togliVuote ? ">" : ">=")." 0);";
                            $dblink->query($sql_0033, true);
                            
                            
                            stampa_table_datatables_responsive("SELECT * FROM stat_campagna_home_no_id UNION SELECT * FROM stat_campagna_home_totale;", "Statistiche per CAMPAGNA".$titolo_intervallo, "tabella_base_home", COLORE_PRIMARIO, true);
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
            
            $('#id_agente, #id_prodotto, #id_campagna, #id_tipo_marketing, #escludi_FREE, #escludi_DISATTIVE').on('change', function(ev, picker) {
                document.formIntervallo.submit();
            });
            
            $('#intervallo_data').on('change', function(ev, picker) {
                document.formIntervallo.submit();
            });
            
            ComponentsMultiselectCampagneHome.init(); 
            
            /*$('.multiselect-search').on('input', function(ev) {
                
                ev.preventDefault();
                ev.stopPropagation();
                ev.stopImmediatePropagation();
                
                console.clear();
                
                var target = $(this);
                var search = target.val().toLowerCase();
                
                if(!search){
                    $('.multiselect-container li').show();
                    return false;
                }
                
                if(search.indexOf(" ") > -1){
                    var ricerca = search.split(" ");
                    var ricercaLen = ricerca.length;

                    $('.multiselect-container li').not('.multiselect-filter, .multiselect-all').each(function() {
                        
                        var text = $(this).text().toLowerCase();
                        var trovato = true;
                        for (i = 0; i < ricercaLen; i++) {
                            var match = text.indexOf(ricerca[i]) > -1;
                            if(match){
                                trovato = trovato && true;
                            }else{
                                trovato = trovato && false;
                            }
                            
                        }
                        if(trovato){
                            console.log(text);
                            $(this).show().removeClass('multiselect-filter-hidden');
                        }
                    });
                }else{
                    $('.multiselect-container li').not('.multiselect-filter, .multiselect-all').each(function() {
                        
                        var text = $(this).text().toLowerCase();
                        var match = text.indexOf(search) > -1;
                        $(this).toggle(match);
                        $(this).show().removeClass('multiselect-filter-hidden');
                    });
                }
                
            });*/
            
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
                                        enableCaseInsensitiveFiltering: true,
                                        enableFullValueFiltering: false,
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
    <script src="<?= BASE_URL ?>/moduli/campagne/scripts/funzioni.js" type="text/javascript"></script>
    <!-- END THEME LAYOUT SCRIPTS -->
</body>
</html>
