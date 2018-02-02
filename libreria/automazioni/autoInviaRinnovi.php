<?php
ini_set('max_execution_time', 290); //5 minuti - 10 secondi
ini_set('memory_limit', '2048M'); // 2 Giga
//ob_start();
session_start();
include_once($_SERVER['DOCUMENT_ROOT'].'/config/connDB.php');
include_once(BASE_ROOT.'libreria/libreria.php');
//include_once(BASE_ROOT.'classi/webservice/client.php');

if(DISPLAY_DEBUG){
    echo '<li>DB_HOST = '.DB_HOST.'</li>';
    echo '<li>DB_USER = '.DB_USER.'</li>';
    //echo '<li>DB_PASS = '.DB_PASS.'</li>';
    echo '<li>DB_NAME = '.DB_NAME.'</li>';
    echo '<li>'.date('Y-m-d H:i:s').'</li>';
}

//$sqlCrocco = "SELECT IF(NOW()>'2017-08-09 12:00:00', '1', '0' ) AS result FROM lista_menu LIMIT 1";
//$rowCrocco = $dblink->get_row($sqlCrocco);

//if($_SESSION['autoInviaiscrizioniCount'] > 0){

    //echo '<h1>MODIFICARE $destinatario = simone.crocco@cemanext.it IN LIB_MAIL</h1>';

    
    $sql_rinnovi_invia = "SELECT * FROM calendario WHERE etichetta LIKE 'Nuovo Rinnovo' AND stato LIKE 'In Attesa di Invio' LIMIT 500";
    if(DISPLAY_DEBUG) {
        echo "<li>$sql_rinnovi_invia</li>"; 
    }
    $rs_rinnovi_invia = $dblink->get_results($sql_rinnovi_invia);
    foreach ($rs_rinnovi_invia AS $row_rinnovi_invia){
        
        $idCalendario = $row_rinnovi_invia['id'];
        
        if(DISPLAY_DEBUG) echo '<li>$idCalendario = '.$idCalendario.'</li>';
        $ret = inviaEmailRinnovoAbbonamento($idCalendario, 'mailRinnovoAbbonamento');
        if(DISPLAY_DEBUG) echo '<li>$ret = '.$ret.'</li>';
        
        if($ret){
            $sql_00002 = "UPDATE calendario 
            SET stato = 'Inviato', 
            dataagg = NOW(),
            scrittore = 'autoInviaRinnovi'
            WHERE stato LIKE 'In Attesa di Invio'
            AND id=".$idCalendario;
            $ok = $dblink->query($sql_00002);
            if($ok){
                if(DISPLAY_DEBUG) echo '<li style="color:green;">$idCalendario = '.$idCalendario.' Inviato !</li>';
                /*if($_SESSION['autoInviaiscrizioniCount']>0){
                    //echo ' <meta http-equiv="refresh" content="2; url='.BASE_URL."/libreria/automazioni/autoInviaiscrizioniEmesse.php".'"><h3>rimangono '.$_SESSION['autoInviaiscrizioniCount'].' iscrizioni da inviare </h3>';
                }else{
                    unset($_SESSION['autoInviaiscrizioniCount']);
                }*/
            }else{
                if(DISPLAY_DEBUG) echo '<li style="color:red;">$idCalendario = '.$idCalendario.' NON Inviato e NON Aggiornata !</li>';
            }
        }
        if(DISPLAY_DEBUG) echo '<hr>';
        //ob_flush();
        sleep(6);
    }
/*}else{
    unset($_SESSION['autoInviaiscrizioniCount']);
}*/

if(DISPLAY_DEBUG) echo '<br>'.date("H:i:s");
?>
