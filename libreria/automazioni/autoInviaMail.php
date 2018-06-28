<?php
ini_set('max_execution_time', 290); //5 minuti - 10 secondi
ini_set('memory_limit', '2048M'); // 2 Giga

include_once('../../config/connDB.php');
include_once(BASE_ROOT . 'config/confAccesso.php');

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

if(isset($_GET['priorita'])){
    $where = " AND priorita LIKE '".$_GET['priorita']."'";
}else{
    $where = "";
}

$sqlMail = "SELECT id FROM lista_invio_mail WHERE stato LIKE 'Da Inviare' $where ORDER BY priorita ASC";
$rsMail = $dblink->get_results($sqlMail);
foreach ($rsMail AS $rowMail){
    $stato_email = inviaMailDaTabella($rowMail['id']);
    if($stato_email){
        $update = array(
            "dataagg" => date("Y-m-d H:i:s"),
            "scrittore" => "autoInviaMail",
            "stato" => "Inviata",
            "data_invio" => date("Y-m-d H:i:s"),
        );

        $ok = $dblink->update("lista_invio_mail", $update, array("id" => $rowMail['id']));

        if($ok){
            if(DISPLAY_DEBUG) echo '<li style="color:GREEN;">idInvioMail = '.$rowMail['id'].' Inviata !</li>';
            $log->log_all_errors('lista_invio_mail ->stato = INVIATA MAIL [idInvioMail = '.$rowMail['id'].']','OK');
        }else{
            if(DISPLAY_DEBUG) echo '<li style="color: RED;">idInvioMail = '.$rowMail['id'].' NON Inviata !</li>';
            $log->log_all_errors('lista_invio_mail ->stato = INVIATA MAIL - ERRORE SALVATAGGIO [idInvioMail = '.$rowMail['id'].']','ERRORE');
        }
    }else{
        if(DISPLAY_DEBUG) echo '<li style="color: RED;">idInvioMail = '.$rowMail['id'].' MAIL NON Inviata !</li>';
        $log->log_all_errors('lista_invio_mail = ERRORE INVIO MAIL [idInvioMail = '.$rowMail['id'].']','ERRORE');
    }
    
    if(DISPLAY_DEBUG) echo '<hr>';
    sleep(2);
}
?>