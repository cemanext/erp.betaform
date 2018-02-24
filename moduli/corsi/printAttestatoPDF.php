<?php
include_once('../../config/connDB.php');
include_once(BASE_ROOT . 'config/confAccesso.php');

$browser = strpos($_SERVER['HTTP_USER_AGENT'], "iPhone");
if ($browser == true) {
    //echo 'Code You Want To Execute';
}

if(isset($_GET['force']) && $_GET['force'] == "1"){
    $forzaRigenerazione = true;
}else{
    $forzaRigenerazione = false;
}

creaAttestatoPDF($_GET['idIscrizione'], true, $forzaRigenerazione);

?>