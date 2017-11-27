<?php
include_once('../../config/connDB.php');
include_once(BASE_ROOT . 'config/confAccesso.php');
include_once(BASE_ROOT . 'classi/webservice/client.php');

$moodle = new moodleWebService();

if (DISPLAY_DEBUG) {
    echo '<hr>'.date("H:i:s");
    echo '<li>DB_HOST = '.DB_HOST.'</li>';
    echo '<li>DB_USER = '.DB_USER.'</li>';
    echo '<li>DB_PASS = '.DB_PASS.'</li>';
    echo '<li>DB_NAME = '.DB_NAME.'</li>';
    echo '<li>DB_NAME = '.MOODLE_DB_NAME.'</li>';
    echo '<li>DB_NAME = '.DURATA_CORSO_INGEGNERI.'</li>';
    echo '<li>DB_NAME = '.DURATA_ABBONAMENTO.'</li>';
    echo '<li>DB_NAME = '.DURATA_CORSO.'</li>';
    echo '<hr>';
}

$sql_000555= "UPDATE lista_iscrizioni 
SET lista_iscrizioni.stato = 'Scaduto'
WHERE lista_iscrizioni.data_fine_iscrizione < CURDATE()
AND (stato = 'In Attesa' OR stato = 'In Corso')";
$dblink->query($sql_000555);

$sql_000555= "UPDATE lista_iscrizioni 
SET lista_iscrizioni.stato = 'Configurazione Scaduta'
WHERE lista_iscrizioni.data_fine_iscrizione < CURDATE()
AND (stato = 'Configurazione')";
$dblink->query($sql_000555);

//ANNULLIAMO I CORSI
$sql_lista_iscrizioni_annulla = "SELECT id AS idIscrizione, id_utente_moodle, abbonamento, id_classe, 
(SELECT nome FROM lista_classi WHERE id = id_classe LIMIT 1) as nomeClasse, 
(SELECT id_corso_moodle FROM lista_corsi WHERE id = id_corso LIMIT 1) as id_corso_moodle 
FROM lista_iscrizioni WHERE stato='Scaduto'
AND abbonamento!='1'";
$rs_lista_iscrizioni_annulla = $dblink->get_results($sql_lista_iscrizioni_annulla);

foreach ($rs_lista_iscrizioni_annulla AS $row_lista_iscrizioni_annulla){
    
    if (DISPLAY_DEBUG) {
        echo '<br>id_utente_moodle = '.$id_utente_moodle = $row_lista_iscrizioni_annulla['id_utente_moodle'];
        echo '<br>abbonamento = '.$abbonamento = $row_lista_iscrizioni_annulla['abbonamento'];
        echo '<br>id_classe = '.$id_classe = $row_lista_iscrizioni_annulla['id_classe'];
        echo '<br>nomeClasse = '.$nome_classe = $row_lista_iscrizioni_annulla['nomeClasse'];
        echo '<br>id_corso_moodle = '.$id_corso_moodle = $row_lista_iscrizioni_annulla['id_corso_moodle'];
        echo '<br>idIscrizione = '.$idIscrizione = $row_lista_iscrizioni_annulla['idIscrizione'];
    }else{
        $id_utente_moodle = $row_lista_iscrizioni_annulla['id_utente_moodle'];
        $abbonamento = $row_lista_iscrizioni_annulla['abbonamento'];
        $id_classe = $row_lista_iscrizioni_annulla['id_classe'];
        $nome_classe = $row_lista_iscrizioni_annulla['nomeClasse'];
        $id_corso_moodle = $row_lista_iscrizioni_annulla['id_corso_moodle'];
        $idIscrizione = $row_lista_iscrizioni_annulla['idIscrizione'];
    }
    
    $ok = $moodle->annullaCorsoMoodle($id_utente_moodle, $id_corso_moodle);
        if($ok){
            if (DISPLAY_DEBUG) echo '<li style="color: GREEN;"> OK !</li>';
            
            $sql_aggiorno_iscrizione = "UPDATE lista_iscrizioni 
            SET stato='Scaduto e Disattivato',
            dataagg=NOW(),
            scrittore = 'autoAnnullaCorsiAbbonamenti'
            WHERE stato='Scaduto' 
            AND id='".$idIscrizione."'
            AND abbonamento!='1'";
            $dblink->query($sql_aggiorno_iscrizione);
            
            $log->log_all_errors('autoAnnullaCorsiAbbonamenti.php -> corso annullato correttamente [id_utente_moodle = '.$id_utente_moodle.']','OK');
        }else{
            if (DISPLAY_DEBUG) echo '<li style="color: RED;"> KO !</li>';
            
            $log->log_all_errors('autoAnnullaCorsiAbbonamenti.php -> impossibile annullare corso [id_utente_moodle = '.$id_utente_moodle.']','ERRORE');
        }

    echo '<hr>';
}


//ANNULLIAMO GLI ABBONAMENTI
$sql_lista_iscrizioni_annulla = "SELECT id_utente_moodle, abbonamento, id_classe, 
(SELECT nome FROM lista_classi WHERE id = id_classe LIMIT 1) as nomeClasse
FROM lista_iscrizioni WHERE stato='Configurazione Scaduta'
AND abbonamento='1'";
$rs_lista_iscrizioni_annulla = $dblink->get_results($sql_lista_iscrizioni_annulla);

foreach ($rs_lista_iscrizioni_annulla AS $row_lista_iscrizioni_annulla){
    
    if (DISPLAY_DEBUG) {
        echo '<br>id_utente_moodle = '.$id_utente_moodle = $row_lista_iscrizioni_annulla['id_utente_moodle'];
        echo '<br>abbonamento = '.$abbonamento = $row_lista_iscrizioni_annulla['abbonamento'];
        echo '<br>id_classe = '.$id_classe = $row_lista_iscrizioni_annulla['id_classe'];
        echo '<br>nomeClasse = '.$nome_classe = $row_lista_iscrizioni_annulla['nomeClasse'];
    }else{
        $id_utente_moodle = $row_lista_iscrizioni_annulla['id_utente_moodle'];
        $abbonamento = $row_lista_iscrizioni_annulla['abbonamento'];
        $id_classe = $row_lista_iscrizioni_annulla['id_classe'];
        $nome_classe = $row_lista_iscrizioni_annulla['nomeClasse'];
    }
    $ok = $moodle->annullaAbbonamentoMoodle($id_utente_moodle, $nome_classe);
    if($ok){
        if (DISPLAY_DEBUG) echo '<li style="color: GREEN;"> OK !</li>';

        $sql_aggiorno_iscrizione = "UPDATE lista_iscrizioni 
        SET stato='Scaduto e Disattivato',
        dataagg=NOW(),
        scrittore = 'autoAnnullaCorsiAbbonamenti'
        WHERE stato='Scaduto' 
        AND id_utente_moodle='".$id_utente_moodle."' 
        AND id_classe='".$id_classe."' 
        AND abbonamento='1'";
        $dblink->query($sql_aggiorno_iscrizione);

        $sql_aggiorno_iscrizione = "UPDATE lista_iscrizioni 
        SET stato='Configurazione Scaduta e Disattivata',
        dataagg=NOW(),
        scrittore = 'autoAnnullaCorsiAbbonamenti'
        WHERE stato='Configurazione Scaduta' 
        AND id_utente_moodle='".$id_utente_moodle."' 
        AND id_classe='".$id_classe."' 
        AND abbonamento='1'";
        $dblink->query($sql_aggiorno_iscrizione);

        $log->log_all_errors('autoAnnullaCorsiAbbonamenti.php -> abbonamento annullato correttamente [id_utente_moodle = '.$id_utente_moodle.']','OK');
    }else{
        if (DISPLAY_DEBUG) echo '<li style="color: RED;"> KO !</li>';
        $log->log_all_errors('autoAnnullaCorsiAbbonamenti.php -> impossibile annullare abbonamento [id_utente_moodle = '.$id_utente_moodle.']','ERRORE');
    }
    
    if (DISPLAY_DEBUG) echo '<hr>';
}

if (DISPLAY_DEBUG) echo date("H:i:s");
?>