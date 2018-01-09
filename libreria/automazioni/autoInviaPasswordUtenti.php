<?php
ini_set('max_execution_time', 290); //5 minuti - 10 secondi
ini_set('memory_limit', '2048M'); // 2 Giga

include_once('../../config/connDB.php');
include_once(BASE_ROOT . 'config/confAccesso.php');
include_once(BASE_ROOT . 'classi/webservice/client.php');

$moodle = new moodleWebService();

if (DISPLAY_DEBUG) {
    echo date("H:i:s");

    echo '<h1>autoInvaPasswordUtenti</h1>';
    echo '<li>DB_HOST = ' . DB_HOST . '</li>';
    echo '<li>DB_USER = ' . DB_USER . '</li>';
    echo '<li>DB_PASS = ' . DB_PASS . '</li>';
    echo '<li>DB_NAME = ' . DB_NAME . '</li>';
    echo '<li>DB_NAME = ' . MOODLE_DB_NAME . '</li>';
    echo '<li>DB_NAME = ' . DURATA_CORSO_INGEGNERI . '</li>';
    echo '<li>DB_NAME = ' . DURATA_ABBONAMENTO . '</li>';
    echo '<li>DB_NAME = ' . DURATA_CORSO . '</li>';
    echo '<hr>';
}

//$sqlCrocco = "SELECT IF(NOW()>'2017-08-09 12:00:00', '1', '0' ) AS result FROM lista_menu LIMIT 1";
//$rowCrocco = $dblink->get_row($sqlCrocco);

//if($rowCrocco[0]=="1"){

    //echo '<h1>MODIFICARE $destinatario = simone.crocco@cemanext.it IN LIB_MAIL</h1>';

    
    $sql_lista_password_manuale = "SELECT * FROM lista_password 
    WHERE livello LIKE 'cliente' AND stato='Attivo - Inviare Password' AND data_scadenza >= NOW()
    ORDER BY dataagg DESC
    LIMIT 50";
    $rs_lista_password_manuale = $dblink->get_results($sql_lista_password_manuale);
    foreach ($rs_lista_password_manuale AS $row_lista_password_manuale){
        //PER OGNI RIGA TRAMITE EMAIL VADO A CERCARE UTENTE IN MOODLE
        if (DISPLAY_DEBUG) echo '<h1>email = '.$row_lista_password_manuale['email'].'</h1>';
        $idListaPassword = $row_lista_password_manuale['id'];
        $email = $row_lista_password_manuale['email'];
        $username = $row_lista_password_manuale['username'];
        $password = $row_lista_password_manuale['passwd'];
        $firstname = $row_lista_password_manuale['nome'];
        $lastname = $row_lista_password_manuale['cognome'];
        $idnumber = $row_lista_password_manuale['id_professionista'];

        $ret = $moodle->creaUtenteMoodle($username, $email, $firstname, $lastname, $password, $idnumber);
        if($ret>0){
            $stato_email = inviaEmailTemplate_Password($idListaPassword, 'inviaPassword');
            if($stato_email){
                if(DISPLAY_DEBUG) echo '<li style="color: GREEN;"> OK !</li>';
                $sql_00002 = "UPDATE lista_password 
                SET stato = 'Attivo', 
                dataagg = NOW(),
                scrittore = 'autoInviaPasswordUtenti'
                WHERE id=".$idListaPassword;
                $rs_00002 = $dblink->query($sql_00002);
                if($rs_00002){
                    if(DISPLAY_DEBUG) echo '<li style="color:GREEN;">idListaPassword = '.$idListaPassword.' Aggiornata !</li>';
                    $log->log_all_errors('lista_password ->stato = ATTIVO  [idListaPassword = '.$idListaPassword.']','OK');
                }else{
                    if(DISPLAY_DEBUG) echo '<li style="color: RED;">idListaPassword = '.$idListaPassword.' NON Aggiornata !</li>';
                    $log->log_all_errors('lista_password ->stato = NON ATTIVO [idListaPassword = '.$idListaPassword.']','ERRORE');
                }
                $log->log_all_errors('stato_email ->email INVIATA correttamente [email = '.$email.']','OK');
            }else{
                if(DISPLAY_DEBUG) echo '<li style="color: RED;"> KO !</li>';
                $log->log_all_errors('stato_email -> email NON inviata = '.$email.']','ERRORE');
                
                $stato_email = inviaEmailTemplate_Password($idListaPassword, 'inviaPasswordErroreMail');
                if($stato_email){
                    if(DISPLAY_DEBUG) echo '<li style="color: GREEN;"> OK !</li>';
                    $sql_00003 = "UPDATE lista_password 
                    SET stato = 'Attivo', 
                    dataagg = NOW(),
                    scrittore = 'autoInviaPasswordUtenti'
                    WHERE id=".$idListaPassword;
                    $rs_00003 = $dblink->query($sql_00003);
                    
                    if($rs_00003){
                        if(DISPLAY_DEBUG) echo '<li style="color:GREEN;">idListaPassword = '.$idListaPassword.' Aggiornata !</li>';
                        $log->log_all_errors('lista_password ->stato = ATTIVO - ERRORE MAIL  [idListaPassword = '.$idListaPassword.']','OK');
                    }else{
                        if(DISPLAY_DEBUG) echo '<li style="color: RED;">idListaPassword = '.$idListaPassword.' NON Aggiornata !</li>';
                        $log->log_all_errors('lista_password ->stato = NON ATTIVO - ERRORE MAIL [idListaPassword = '.$idListaPassword.']','ERRORE');
                    }
                    
                }else{
                    if(DISPLAY_DEBUG) echo '<li style="color: RED;"> KO !</li>';
                    $log->log_all_errors('stato_email -> email di ERRORE NON inviata ad assitenza@betaformazione.com = '.$email.']','ERRORE');
                }
                
            }
        }
        if(DISPLAY_DEBUG) echo '<hr>';
        
        sleep(3);
    }
//}
?>