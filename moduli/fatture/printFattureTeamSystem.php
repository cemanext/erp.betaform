<?php
include_once('../../config/connDB.php');
include_once(BASE_ROOT.'config/confAccesso.php');

if($_GET['primanota']==1){
    include_once(BASE_ROOT.'libreria/esporta_pagamenti.php');
}else{
    include_once(BASE_ROOT.'libreria/esporta_fatture.php');
}

?>
