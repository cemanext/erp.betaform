<?php
ini_set('display_errors', '1');
include_once('../../config/connDB.php');
include_once(BASE_ROOT.'config/confAccesso.php');

    $idCorsoMoodle = $_GET['idCorsoMoodle'];
    $instance = $_GET['a'];
    $videoInCorso = $_GET['numeroVideo'];
    $row_0001 = $dblink->get_row("SELECT domanda, risposta FROM lista_domande WHERE instance = '".$instance."' AND ordine<'".$videoInCorso."' ORDER BY RAND()",true);  
    echo json_encode($row_0001);
?>