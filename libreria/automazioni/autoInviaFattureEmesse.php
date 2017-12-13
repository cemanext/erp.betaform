<?php
ini_set('max_execution_time', 290); //5 minuti - 10 secondi
ini_set('memory_limit', '2048M'); // 2 Giga
//ob_start();
session_start();
include_once($_SERVER['DOCUMENT_ROOT'].'/config/connDB.php');
include_once(BASE_ROOT.'libreria/libreria.php');
//include_once(BASE_ROOT.'classi/webservice/client.php');

/*if(!isset($_SESSION['autoInviaFattureCount'])){
 $_SESSION['autoInviaFattureCount'] = 20;
}else{
    $_SESSION['autoInviaFattureCount'] = ($_SESSION['autoInviaFattureCount']-1);
}*/

//AND lista_password.id_moodle_user=152
//$moodle = new moodleWebService();

iF(DISPLAY_DEBUG){
    echo '<li>DB_HOST = '.DB_HOST.'</li>';
    echo '<li>DB_USER = '.DB_USER.'</li>';
    //echo '<li>DB_PASS = '.DB_PASS.'</li>';
    echo '<li>DB_NAME = '.DB_NAME.'</li>';
}
//$sqlCrocco = "SELECT IF(NOW()>'2017-08-09 12:00:00', '1', '0' ) AS result FROM lista_menu LIMIT 1";
//$rowCrocco = $dblink->get_row($sqlCrocco);

//if($_SESSION['autoInviaFattureCount'] > 0){

    //echo '<h1>MODIFICARE $destinatario = simone.crocco@cemanext.it IN LIB_MAIL</h1>';

$sql_aggiorna_sezionale_pa = "UPDATE lista_fatture SET stato_invio = '' 
WHERE stato_invio LIKE 'In Attesa di Invio' AND sezionale='PA'";
$ok = $dblink->query($sql_aggiorna_sezionale_pa);

    echo '<li>'.date('Y-m-d H:i:s').'</li>';
    echo $sql_lista_fatture_invia = "SELECT * FROM lista_fatture WHERE stato_invio LIKE 'In Attesa di Invio' LIMIT 20";
    $rs_lista_fatture_invia = $dblink->get_results($sql_lista_fatture_invia);
    foreach ($rs_lista_fatture_invia AS $row_lista_fatture_invia){
        
        $idFattura = $row_lista_fatture_invia['id'];
        
        echo '<li>creaFatturaPDF SEZIONALE = '.$row_lista_fatture_invia['codice_ricerca'].'</li>';
        
        creaFatturaPDF($idFattura, false);
        
        echo '<li>$idFattura = '.$idFattura.'</li>';
        $ret = inviaEmailFatturaDaId($idFattura,false);
        echo '<li>$ret = '.$ret.'</li>';
        
        if($ret){
            $sql_00002 = "UPDATE lista_fatture 
            SET stato_invio = 'Inviata', 
            dataagg = NOW(),
            data_invio = NOW()
            WHERE stato_invio LIKE 'In Attesa di Invio'
            AND id=".$idFattura;
            $ok = $dblink->query($sql_00002);
            if($ok){
                echo '<li style="color:green;">idFattura = '.$idFattura.' Inviata !</li>';
                /*if($_SESSION['autoInviaFattureCount']>0){
                    //echo ' <meta http-equiv="refresh" content="2; url='.BASE_URL."/libreria/automazioni/autoInviaFattureEmesse.php".'"><h3>rimangono '.$_SESSION['autoInviaFattureCount'].' fatture da inviare </h3>';
                }else{
                    unset($_SESSION['autoInviaFattureCount']);
                }*/
            }else{
                echo '<li style="color:red;">idFattura = '.$idFattura.' NON Inviata e NON Aggiornata !</li>';
            }
        }
        echo '<hr>';
        //ob_flush();
        sleep(1);
    }
/*}else{
    unset($_SESSION['autoInviaFattureCount']);
}*/

echo '<br>'.date("H:i:s");
?>
