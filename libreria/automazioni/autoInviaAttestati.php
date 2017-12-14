<?php
ini_set('max_execution_time', 290); //5 minuti - 10 secondi
ini_set('memory_limit', '2048M'); // 2 Giga
//ob_start();
session_start();
include_once($_SERVER['DOCUMENT_ROOT'].'/config/connDB.php');
include_once(BASE_ROOT.'libreria/libreria.php');

iF(DISPLAY_DEBUG){
    echo '<li>DB_HOST = '.DB_HOST.'</li>';
    echo '<li>DB_USER = '.DB_USER.'</li>';
    //echo '<li>DB_PASS = '.DB_PASS.'</li>';
    echo '<li>DB_NAME = '.DB_NAME.'</li>';
    echo '<li>'.date('Y-m-d H:i:s').'</li>';
}

$sql_lista_iscrizioni_invia = "SELECT * FROM lista_iscrizioni WHERE stato_invio_attestato LIKE 'In Attesa di Invio' LIMIT 20";
$rs_lista_iscrizioni_invia = $dblink->get_results($sql_lista_iscrizioni_invia);
foreach ($rs_lista_iscrizioni_invia AS $row_lista_iscrizioni_invia){
        
        $idIscrizione = $row_lista_iscrizioni_invia['id'];
        
        creaAttestatoPDF($idIscrizione, false);
        
        iF(DISPLAY_DEBUG){
            echo '<li>$idIscrizione = '.$idIscrizione.'</li>';
        }
        $ret = inviaEmailAttestatoDaIdIscrizione($idIscrizione);
        iF(DISPLAY_DEBUG){
            echo '<li>$ret = '.$ret.'</li>';
        }
        
        if($ret){
            $sql_00002 = "UPDATE lista_iscrizioni
            SET stato_invio_attestato = 'Inviata', 
            dataagg = NOW(),
            data_invio_attestato = NOW()
            WHERE stato_invio_attestato LIKE 'In Attesa di Invio'
            AND id=".$idIscrizione;
            $ok = $dblink->query($sql_00002);
            if($ok){
                iF(DISPLAY_DEBUG){
                    echo '<li style="color:green;">idIscrizione = '.$idIscrizione.' Inviata !</li>';
                }
                /*if($_SESSION['autoInviaFattureCount']>0){
                    //echo ' <meta http-equiv="refresh" content="2; url='.BASE_URL."/libreria/automazioni/autoInviaFattureEmesse.php".'"><h3>rimangono '.$_SESSION['autoInviaFattureCount'].' fatture da inviare </h3>';
                }else{
                    unset($_SESSION['autoInviaFattureCount']);
                }*/
            }else{
                iF(DISPLAY_DEBUG){
                    echo '<li style="color:red;">idIscrizione = '.$idIscrizione.' NON Inviata e NON Aggiornata !</li>';
                }
            }
        }
        iF(DISPLAY_DEBUG){
            echo '<hr>';   
        }
        //ob_flush();
        sleep(1);
    }
/*}else{
    unset($_SESSION['autoInviaFattureCount']);
}*/
iF(DISPLAY_DEBUG){
    echo '<br>'.date("H:i:s");
}
?>
