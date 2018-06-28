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

$sql_0001 = "CREATE TEMPORARY TABLE utentiDaAttivare(SELECT DISTINCT 
CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"".BASE_URL."/moduli/anagrafiche/dettaglio.php?tbl=lista_professionisti&id=',id_professionista,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-user',
CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"".BASE_URL."/moduli/anagrafiche/modifica.php?tbl=lista_professionisti&id=',id_professionista,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',
id_professionista AS 'idProfessionista',
(SELECT DISTINCT CONCAT('<h3>',cognome,' ',nome,'</h3>') FROM lista_professionisti WHERE id = id_professionista LIMIT 1) AS 'Professionista',
(SELECT DISTINCT id_classe FROM lista_professionisti WHERE id = id_professionista LIMIT 1) AS 'idClasse',
(SELECT DISTINCT email FROM lista_professionisti WHERE id = id_professionista LIMIT 1) AS 'Email', 
(SELECT DISTINCT id FROM lista_password WHERE lista_password.id_professionista = lista_fatture_dettaglio.id_professionista LIMIT 1) AS 'idPassword',
CONCAT('<center><a href=\"".BASE_URL."/moduli/fatture/salva.php?idProfessionista=',id_professionista,'&fn=creaUtenteTotale\" class=\"btn btn-icon btn-outline blue-ebonyclay\"><i class=\"fa fa-users\"></i> CREA UTENTE </a></center>') AS 'fa-password'
FROM lista_fatture_dettaglio
WHERE ( stato LIKE 'In Attesa di Emissione' OR  stato LIKE 'In Attesa') AND lista_fatture_dettaglio.id_professionista > 0
AND id_professionista NOT IN (SELECT DISTINCT id_professionista FROM lista_password WHERE livello LIKE 'cliente' AND id_moodle_user > 0));";
$rs_0001 = $dblink->query($sql_0001);

$sql_00000000 = "SELECT * FROM utentiDaAttivare WHERE Email != '' ORDER BY RAND() LIMIT 10";
if (DISPLAY_DEBUG) stampa_table_datatables_responsive($sql_00000000, $titolo, 'tabella_base');

$rs_00000000 = $dblink->get_results($sql_00000000);
foreach ($rs_00000000 AS $row_00000000){
    $idProfessionista = $row_00000000['idProfessionista'];
    $ok = creaUtenteTotale($idProfessionista);
    if($ok){
        if (DISPLAY_DEBUG) echo '<li style="color: green;"> OK !</li>';
        $log->log_all_errors('creaUtenteTotale -> utente creato correttamente [idProfessionista = '.$idProfessionista.']','OK');
    }else{
       if (DISPLAY_DEBUG) echo '<li style="color: RED;"> KO !</li>';
       $log->log_all_errors('creaUtenteTotale -> utente NON creato [idProfessionista = '.$idProfessionista.']','ERRORE');
    }

    $sql_aggiorna_lista_professionista = "UPDATE `lista_professionisti`, `lista_password`
    SET `lista_professionisti`.`id_moodle_user` = `lista_password`.`id_moodle_user`
    WHERE `lista_professionisti`.`id` = `lista_password`.`id_professionista`
    AND `lista_professionisti`.`id`='".$idProfessionista."'
    AND `lista_professionisti`.`id_moodle_user`<=0";
    $rs_aggiorna_lista_professionista = $dblink->query($sql_aggiorna_lista_professionista);
}

if (DISPLAY_DEBUG) echo date("H:i:s");
?>