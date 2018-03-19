<?php
ini_set('max_execution_time', 290); //5 minuti - 10 secondi
ini_set('memory_limit', '2048M'); // 2 Giga
//ob_start();
session_start();
include_once($_SERVER['DOCUMENT_ROOT'].'/config/connDB.php');
include_once(BASE_ROOT.'libreria/libreria.php');
//include_once(BASE_ROOT.'classi/webservice/client.php');


echo '<li>DB_HOST = '.DB_HOST.'</li>';
echo '<li>DB_USER = '.DB_USER.'</li>';
//echo '<li>DB_PASS = '.DB_PASS.'</li>';
echo '<li>DB_NAME = '.DB_NAME.'</li>';

//$sqlCrocco = "SELECT IF(NOW()>'2017-08-09 12:00:00', '1', '0' ) AS result FROM lista_menu LIMIT 1";
//$rowCrocco = $dblink->get_row($sqlCrocco);

//if($_SESSION['autoInviaiscrizioniCount'] > 0){

    //echo '<h1>MODIFICARE $destinatario = simone.crocco@cemanext.it IN LIB_MAIL</h1>';

    echo '<li>'.date('Y-m-d H:i:s').'</li>';
    echo $sql_lista_iscrizioni_invia = "SELECT * FROM lista_iscrizioni 
    WHERE stato_invio_completato NOT LIKE 'Inviato' 
    AND `stato_completamento` LIKE 'Completato'
    AND `data_completamento` >= DATE_SUB(CURDATE(), INTERVAL 1 DAY)
    LIMIT 20";
    $rs_lista_iscrizioni_invia = $dblink->get_results($sql_lista_iscrizioni_invia);
    foreach ($rs_lista_iscrizioni_invia AS $row_lista_iscrizioni_invia){
        
        $idIscrizione = $row_lista_iscrizioni_invia['id'];
        
        echo '<li>nome_corso = '.$row_lista_iscrizioni_invia['nome_corso'].'</li>';
        
        //creaAttestatoPDF($idIscrizione, false);
        
        echo '<li>$idIscrizione = '.$idIscrizione.'</li>';
        //inviaCorsoCompletato
        //$ret = inviaEmailCorsoCompletato($idIscrizione,false);
        $ret = true;
        echo '<li>$ret = '.$ret.'</li>';
        //$ret = false;
        
        if($ret){
            $sql_00002 = "UPDATE lista_iscrizioni 
            SET stato_invio_completato = 'Inviato', 
            dataagg = NOW(),
            data_invio_completato = NOW()
            WHERE 1
            AND id=".$idIscrizione;
            $ok = $dblink->query($sql_00002);
            if($ok){
                echo '<li style="color:green;">idIscrizione = '.$idIscrizione.' Inviato !</li>';
                /*if($_SESSION['autoInviaiscrizioniCount']>0){
                    //echo ' <meta http-equiv="refresh" content="2; url='.BASE_URL."/libreria/automazioni/autoInviaiscrizioniEmesse.php".'"><h3>rimangono '.$_SESSION['autoInviaiscrizioniCount'].' iscrizioni da inviare </h3>';
                }else{
                    unset($_SESSION['autoInviaiscrizioniCount']);
                }*/
            }else{
                echo '<li style="color:red;">idIscrizione = '.$idIscrizione.' NON Inviato e NON Aggiornata !</li>';
            }
        }
        echo '<hr>';
        //ob_flush();
        sleep(2);
    }
/*}else{
    unset($_SESSION['autoInviaiscrizioniCount']);
}*/

echo '<br>'.date("H:i:s");
?>
