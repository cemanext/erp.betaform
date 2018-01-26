<?php
include_once('../../config/connDB.php');
include_once(BASE_ROOT . 'config/confAccesso.php');
include_once(BASE_ROOT . 'classi/webservice/client.php');

$moodle = new moodleWebService();

if (DISPLAY_DEBUG) {
    echo date("H:i:s");

    echo '<h1>autoCorsiIniziati</h1>';
    echo '<li>DB_HOST = ' . DB_HOST . '</li>';
    echo '<li>DB_USER = ' . DB_USER . '</li>';
    echo '<li>DB_PASS = ' . DB_PASS . '</li>';
    echo '<li>DB_NAME = ' . DB_NAME . '</li>';
    echo '<li>MOODLE_DB_NAME = ' . MOODLE_DB_NAME . '</li>';
    echo '<li>DURATA_CORSO_INGEGNERI = ' . DURATA_CORSO_INGEGNERI . '</li>';
    echo '<li>DURATA_ABBONAMENTO = ' . DURATA_ABBONAMENTO . '</li>';
    echo '<li>DURATA_CORSO = ' . DURATA_CORSO . '</li>';
    echo '<hr>';
}
/*
  $sql_000555= "UPDATE lista_iscrizioni
  SET lista_iscrizioni.stato = 'Scaduto'
  WHERE lista_iscrizioni.data_fine_iscrizione<=NOW()
  AND stato = 'In Attesa' OR stato = 'In Corso'";
  $dblink->query($sql_000555);
 */

$sql_aggiorna_corsi_dettaglio = "UPDATE lista_corsi_dettaglio, lista_corsi
SET lista_corsi_dettaglio.id_corso_moodle = lista_corsi.id_corso_moodle
WHERE lista_corsi_dettaglio.id_corso = lista_corsi.id AND lista_corsi_dettaglio.id_corso_moodle<=0";
$rs_aggiorna_corsi_dettaglio = $dblink->query($sql_aggiorna_corsi_dettaglio);

//$rs_utente_entrato = $dblink->get_results("SELECT id FROM " . MOODLE_DB_NAME . ".mdl_user WHERE DATE(FROM_UNIXTIME(lastaccess))=CURDATE()");

if(isset($_GET['idUtente'])){
    $idUtente = $_GET['idUtente'];
    if(DISPLAY_DEBUG) echo '<li style="color:green;">$idUtente = '.$idUtente.'</li>';
    
    $rs_utente_entrato = $dblink->get_results("SELECT * FROM lista_iscrizioni WHERE id_utente_moodle = '".$idUtente."'");
}else{
    $rs_utente_entrato = $dblink->get_results("SELECT * FROM lista_iscrizioni WHERE stato LIKE 'In Attesa' ORDER BY RAND() LIMIT 200");
}
//$rs_utente_entrato = $dblink->get_results("SELECT * FROM lista_iscrizioni WHERE id='192805'");
if(DISPLAY_DEBUG) echo '<ol>';
//$rs_utente_entrato = $dblink->get_results("SELECT id FROM " . MOODLE_DB_NAME . ".mdl_user WHERE id=7996");
foreach ($rs_utente_entrato as $row_utente_entrato) {
    //$id_utente_entrato = $row_utente_entrato['id'];
    $id_utente_entrato = $row_utente_entrato['id_utente_moodle'];
    if(DISPLAY_DEBUG) echo '<li>$id_utente_entrato = '.$id_utente_entrato.'</li>';

    if(DISPLAY_DEBUG){ echo '<h1>$id_utente_entrato = ' . $id_utente_entrato . '</h1>';}

    $sql_iscritti = "SELECT id_utente_moodle, id_corso_moodle, id_modulo, instance 
    FROM lista_iscrizioni INNER JOIN lista_corsi_dettaglio ON lista_iscrizioni.id_corso = lista_corsi_dettaglio.id_corso 
    WHERE lista_iscrizioni.stato='In Attesa' AND lista_iscrizioni.id_utente_moodle='" . $id_utente_entrato . "'
    AND (ordine=1 OR ordine=2) ORDER BY lista_iscrizioni.id_utente_moodle ";
echo '<li>$sql_iscritti = '.$sql_iscritti.'</li>';
    $rs_iscritti = $dblink->get_results($sql_iscritti);
    foreach ($rs_iscritti as $row_iscritti) {
        
        $id_iscritto = $row_iscritti['id_utente_moodle'];
        $id_corso_moodle = $row_iscritti['id_corso_moodle'];
        $instance = $row_iscritti['instance'];

        $sql_00001 = "SELECT mdl_scorm_scoes_track.id as track_id, 
                `userid` AS 'id_utente_moodle',
                scormid,
                mdl_scorm.course AS 'id_corso_moodle',
                LEFT(FROM_UNIXTIME(`mdl_scorm_scoes_track`.value),19) AS 'data_ora_inizio',
                DATE(FROM_UNIXTIME(`mdl_scorm_scoes_track`.value)) AS 'data_inizio'
                FROM " . MOODLE_DB_NAME . ".`mdl_scorm_scoes_track` INNER JOIN " . MOODLE_DB_NAME . ".`mdl_scorm`
                ON " . MOODLE_DB_NAME . ".`mdl_scorm_scoes_track`.scormid = " . MOODLE_DB_NAME . ".`mdl_scorm`.id
                WHERE `userid`='" . $id_iscritto . "'
                AND `mdl_scorm_scoes_track`.scormid ='" . $instance . "'
                AND `mdl_scorm`.course ='" . $id_corso_moodle . "'
                AND " . MOODLE_DB_NAME . ".`mdl_scorm_scoes_track`.element='x.start.time'
                ORDER BY DATE(FROM_UNIXTIME(`mdl_scorm_scoes_track`.value)) ASC";
        //AND DATE(FROM_UNIXTIME(`mdl_scorm_scoes_track`.value))=CURDATE()
        //AND DATE(FROM_UNIXTIME(`mdl_scorm_scoes_track`.value))=CURDATE()
        //AND `userid`='35567'  
//echo '<li>$sql_00001 = '.$sql_00001.'</li>';
        $rs_00001 = $dblink->get_results($sql_00001);
        //print_r($row);
        if (DISPLAY_DEBUG){ StampaSQL($sql_00001, '', ''); }
        $conto_00001 = count($rs_00001);
        if (DISPLAY_DEBUG){ echo '<li>$conto_00001 = ' . $conto_00001 . '</li>';}
        $ok = false;
        foreach ($rs_00001 as $row_0) {

            if (DISPLAY_DEBUG) {
                echo '<br>mdl_scorm_scoes_track della tabella id = ' . $id_utente_moodle = $row_0['track_id'];
                echo '<br>id_utente_moodle = ' . $id_utente_moodle = $row_0['id_utente_moodle'];
                echo '<br>data_inizio_corso = ' . $data_inizio_corso = $row_0['data_inizio'];
                echo '<br>data_ora_inizio_corso = ' . $data_ora_inizio_corso = $row_0['data_ora_inizio'];
                echo '<br>id_instance_moodle = ' . $id_instance_moodle = $row_0['scormid'];
                echo '<br>id_corso_moodle = ' . $id_corso_moodle = $row_0['id_corso_moodle'];
            }else{
                $id_utente_moodle = $row_0['track_id'];
                $id_utente_moodle = $row_0['id_utente_moodle'];
                $data_inizio_corso = $row_0['data_inizio'];
                $data_ora_inizio_corso = $row_0['data_ora_inizio'];
                $id_instance_moodle = $row_0['scormid'];
                $id_corso_moodle = $row_0['id_corso_moodle'];
            }

            //RECUPERO NOSTRO ID DEL CORSO
            //$sql_00002_1 = "SELECT id,id_corso FROM lista_corsi_dettaglio WHERE instance =".$id_instance_moodle." AND id_modulo='' AND modname='scorm'";
            $sql_00002_1 = "SELECT id,id_corso FROM lista_corsi_dettaglio WHERE id_corso_moodle =" . $id_corso_moodle . " AND  instance =" . $id_instance_moodle . " AND id_modulo !=''";
            $row_00002_1 = $dblink->get_row($sql_00002_1, true);
            $id_corso_nostro = $row_00002_1['id_corso'];
            if (DISPLAY_DEBUG){ echo '<li>$id_corso_nostro = ' . $id_corso_nostro . ' ---> $sql_00002_1  ' . $sql_00002_1 . '</li>'; }

            $sql_00002 = "SELECT id,nome_prodotto FROM lista_corsi WHERE id =" . $id_corso_nostro;
            $row_00002 = $dblink->get_row($sql_00002, true);
            $id_corso_nostro = $row_00002['id'];
            $nome_corso_nostro = $row_00002['nome_prodotto'];
            if (DISPLAY_DEBUG){ echo '<li>$id_corso_nostro = ' . $id_corso_nostro . ' --> ' . $nome_corso_nostro . '</li>'; }

            //RECUPERO NOSTRO ID DEL PROFESSIONISTA
            $sql_00003 = "SELECT id, cognome, nome FROM lista_professionisti WHERE id_moodle_user =" . $id_utente_moodle;
            $row_00003 = $dblink->get_row($sql_00003, true);
            $id_professionista_nostro = $row_00003['id'];
            $cognome_professionista_nostro = $row_00003['cognome'];
            $nome_professionista_nostro = $row_00003['nome'];
            if (DISPLAY_DEBUG){ echo '<li>$id_professionista_nostro = ' . $id_professionista_nostro . ' --> ' . $cognome_professionista_nostro . ' ' . $nome_professionista_nostro . '</li>'; }

            $percentuale_corso_utente = recupero_percentuale_avanzamento_corso_utente($id_utente_moodle, $id_corso_moodle, true);
            if (DISPLAY_DEBUG){ echo '<li>$percentuale_corso_utente = ' . $percentuale_corso_utente . '</li>'; }
            
            $sql_dati_configurazione = "SELECT id, id_fattura, id_fattura_dettaglio FROM lista_iscrizioni 
            WHERE id_utente_moodle = '".$id_utente_moodle."'
            AND data_fine_iscrizione>=CURDATE() AND stato LIKE 'Configurazione'";

            $datiConfigurazione = $dblink->get_row($sql_dati_configurazione, true); 
            
            if ($id_professionista_nostro > 0) {
                /*
                  $sql_00004 = "SELECT * FROM lista_iscrizioni
                  WHERE id_professionista =".$id_professionista_nostro."
                  AND id_corso = ".$id_corso_nostro."
                  AND data_inizio_iscrizione <='".$data_inizio_corso."'
                  AND data_fine_iscrizione >='".$data_inizio_corso."'";
                 */
                $sql_00004 = "SELECT * FROM lista_iscrizioni 
                            WHERE id_professionista =" . $id_professionista_nostro . " 
                            AND id_corso = " . $id_corso_nostro . "
                            AND DATE(data_inizio) <='" . $data_inizio_corso . "'
                            AND DATE(data_fine) >='" . $data_inizio_corso . "'
                            AND stato = 'In Attesa'";
                            echo '---->'.$sql_00004;
                if (DISPLAY_DEBUG){ echo $sql_00004; }
                $rs_00004 = $dblink->get_results($sql_00004);
                if (!empty($rs_00004)) {
                    if (DISPLAY_DEBUG){ StampaSQL($sql_00004, '', ''); }
                    foreach ($rs_00004 as $row_00004) {
                        
                        $id_iscrizione = $row_00004['id'];
                        echo '<li>$id_iscrizione = '.$id_iscrizione.'</li>';
                        $controlloAbbonamento = $row_00004['abbonamento'];
                        $controlloClasse = $row_00004['id_classe'];

                        $sql_00005 = "UPDATE lista_iscrizioni
                            SET dataagg = NOW(),
                            scrittore = 'autoCorsiIniziati',
                            stato='In Corso',
                            `avanzamento_completamento` = '" . $percentuale_corso_utente . "',
                            data_inizio = '" . $data_ora_inizio_corso . "',
                            id_fattura = '".$datiConfigurazione['id_fattura']."',
                            id_fattura_dettaglio = '".$datiConfigurazione['id_fattura_dettaglio']."'
                            WHERE id = " . $id_iscrizione . "
                            AND id_corso = " . $id_corso_nostro . " 
                            AND id_professionista = " . $id_professionista_nostro . "
                            AND stato = 'In Attesa'";
                        $rs_00005 = $dblink->query($sql_00005);
                        if ($rs_00005) {

                            if ($controlloAbbonamento == 1 and $controlloClasse == 10) {
                                $sql_00006 = "UPDATE lista_iscrizioni SET data_fine = DATE_ADD(data_inizio, INTERVAL " . DURATA_CORSO_INGEGNERI . " DAY) 
                                                WHERE id = " . $id_iscrizione . "";
                                $rs_00006 = $dblink->query($sql_00006);
                            } elseif ($controlloAbbonamento == 1 and $controlloClasse != 10) {
                                $sql_00006 = "UPDATE lista_iscrizioni SET data_fine = data_fine_iscrizione
                                                WHERE id = " . $id_iscrizione . "";
                                $rs_00006 = $dblink->query($sql_00006);
                            } else {
                                $sql_00006 = "UPDATE lista_iscrizioni SET data_fine_iscrizione = DATE_ADD(data_inizio, INTERVAL " . DURATA_CORSO . " DAY) 
                                                WHERE id = " . $id_iscrizione . " AND data_inizio_iscrizione <= DATE_ADD(data_inizio, INTERVAL " . DURATA_CORSO . " DAY)";
                                $rs_00006 = $dblink->query($sql_00006);

                                if ($rs_00006) {
                                    $dataFineCorso = $dblink->get_row("SELECT UNIX_TIMESTAMP(data_fine_iscrizione) AS data_scadenza_corso_timestamp FROM lista_iscrizioni  WHERE id = '" . $id_iscrizione . "'", true);
                                    $data_scadenza_corso_timestamp = $dataFineCorso['data_scadenza_corso_timestamp'];
                                    $ret = $moodle->prorogaCorsoMoodle($id_utente_moodle, $id_corso_moodle, $data_scadenza_corso_timestamp);

                                    if ($ret == true) {
                                        $log->log_all_errors('autoCorsiIniziati.php -> Corso Prorogato con successo [id_utente_moodle = ' . $id_utente_moodle . '] [id_corso_moodle = ' . $id_corso_moodle . ']', 'OK');
                                    } else {
                                        $log->log_all_errors('autoCorsiIniziati.php -> Impossibile Prorogare il Corso per questo utente [id_utente_moodle = ' . $id_utente_moodle . '] [id_corso_moodle = ' . $id_corso_moodle . '] [data_scadenza_corso_timestamp = ' . $data_scadenza_corso_timestamp . ']', 'ERRORE');
                                    }
                                }
                            }
                        }
                    }
                }
            } else {
                /*
                  $sql_00004 = "SELECT * FROM lista_iscrizioni
                  WHERE id_utente_moodle =".$id_utente_moodle."
                  AND id_corso = ".$id_corso_nostro."
                  AND data_inizio_iscrizione <='".$data_inizio_corso."'
                  AND data_fine_iscrizione >='".$data_inizio_corso."'";
                 */
                $sql_000044 = "SELECT * FROM lista_iscrizioni 
                            WHERE id_utente_moodle =" . $id_utente_moodle . " 
                            AND id_corso = " . $id_corso_nostro . "
                            AND DATE(data_inizio) <='" . $data_inizio_corso . "'
                            AND DATE(data_fine) >='" . $data_inizio_corso . "'
                            AND stato = 'In Attesa'";
                if (DISPLAY_DEBUG){ echo $sql_000044; }
                $rs_000044 = $dblink->get_results($sql_000044);
                if (!empty($rs_000044)) {
                    if (DISPLAY_DEBUG){ StampaSQL($sql_000044, '', ''); }
                    foreach ($rs_000044 as $row_000044) {
                        
                        $id_iscrizione = $row_000044['id'];
                        $controlloAbbonamento = $row_000044['abbonamento'];
                        $controlloClasse = $row_000044['id_classe'];

                        $sql_00005 = "UPDATE lista_iscrizioni
                                    SET dataagg = NOW(),
                                    scrittore = 'autoCorsiIniziati',
                                    stato='In Corso',
                                     `avanzamento_completamento` = '" . $percentuale_corso_utente . "',
                                    data_inizio = '" . $data_ora_inizio_corso . "',
                                    id_fattura = '".$datiConfigurazione['id_fattura']."',
                                    id_fattura_dettaglio = '".$datiConfigurazione['id_fattura_dettaglio']."'
                                    WHERE id = " . $id_iscrizione . "
                                    AND id_corso = " . $id_corso_nostro . " 
                                    AND id_utente_moodle = " . $id_utente_moodle . "
                                    AND stato = 'In Attesa'";
                        $rs_00005 = $dblink->query($sql_00005);
                        if ($rs_00005) {

                            if ($controlloAbbonamento == 1 and $controlloClasse == 10) {
                                $sql_00006 = "UPDATE lista_iscrizioni SET data_fine = DATE_ADD(data_inizio, INTERVAL " . DURATA_CORSO_INGEGNERI . " DAY) 
                                                WHERE id = " . $id_iscrizione . "";
                                $rs_00006 = $dblink->query($sql_00006);
                            } elseif ($controlloAbbonamento == 1 and $controlloClasse != 10) {
                                $sql_00006 = "UPDATE lista_iscrizioni SET data_fine = data_fine_iscrizione
                                                WHERE id = " . $id_iscrizione . "";
                                $rs_00006 = $dblink->query($sql_00006);
                            } else {
                                $sql_00006 = "UPDATE lista_iscrizioni SET data_fine_iscrizione = DATE_ADD(data_inizio, INTERVAL " . DURATA_CORSO . " DAY) 
                                                WHERE id = " . $id_iscrizione . " AND data_inizio_iscrizione <= DATE_ADD(data_inizio, INTERVAL " . DURATA_CORSO . " DAY)";
                                $rs_00006 = $dblink->query($sql_00006);

                                if ($rs_00006) {
                                    $dataFineCorso = $dblink->get_row("SELECT UNIX_TIMESTAMP(data_fine_iscrizione) AS data_scadenza_corso_timestamp FROM lista_iscrizioni  WHERE id = '" . $id_iscrizione . "'", true);
                                    $data_scadenza_corso_timestamp = $dataFineCorso['data_scadenza_corso_timestamp'];
                                    $ret = $moodle->prorogaCorsoMoodle($id_utente_moodle, $id_corso_moodle, $data_scadenza_corso_timestamp);

                                    if ($ret == true) {
                                        $log->log_all_errors('autoCorsiIniziati.php -> Corso Prorogato con successo [id_utente_moodle = ' . $id_utente_moodle . '] [id_corso_moodle = ' . $id_corso_moodle . ']', 'OK');
                                    } else {
                                        $log->log_all_errors('autoCorsiIniziati.php -> Impossibile Prorogare il Corso per questo utente [id_utente_moodle = ' . $id_utente_moodle . '] [id_corso_moodle = ' . $id_corso_moodle . '] [data_scadenza_corso_timestamp = ' . $data_scadenza_corso_timestamp . ']', 'ERRORE');
                                    }
                                }
                            }
                        }
                    }
                }
            }
            
            if (DISPLAY_DEBUG){ echo '<hr>'; }
        }
    }
}
if(DISPLAY_DEBUG) echo '</ol>';

if(DISPLAY_DEBUG){ echo date("H:i:s"); }
?>