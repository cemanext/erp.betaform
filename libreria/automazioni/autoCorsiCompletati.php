<?php
include_once('../../config/connDB.php');
include_once(BASE_ROOT . 'config/confAccesso.php');
include_once(BASE_ROOT . 'classi/webservice/client.php');

if (DISPLAY_DEBUG) {
    echo '<hr>'.date("H:i:s");
    echo '<li>DB_HOST = '.DB_HOST.'</li>';
    echo '<li>DB_USER = '.DB_USER.'</li>';
    echo '<li>DB_PASS = '.DB_PASS.'</li>';
    echo '<li>DB_NAME = '.DB_NAME.'</li>';
    echo '<li>MOODLE_DB_NAME = '.MOODLE_DB_NAME.'</li>';
    echo '<li>DURATA_CORSO_INGEGNERI = '.DURATA_CORSO_INGEGNERI.'</li>';
    echo '<li>DURATA_ABBONAMENTO = '.DURATA_ABBONAMENTO.'</li>';
    echo '<li>DURATA_CORSO = '.DURATA_CORSO.'</li>';
    echo '<hr>';
}
/*
// AGGIORNO ID UTENTE MOODLE
$sql_0005 = "UPDATE lista_iscrizioni, lista_password 
            SET lista_iscrizioni.id_utente_moodle = lista_password.id_moodle_user 
            WHERE lista_password.id_professionista = lista_iscrizioni.id_professionista 
            AND lista_iscrizioni.id_utente_moodle<=0";
$dblink->query($sql_0005);
*/

$sql_004 = "SELECT lista_iscrizioni.id, lista_iscrizioni.id_professionista, lista_iscrizioni.id_utente_moodle,
        lista_corsi.id_corso_moodle, lista_iscrizioni.id_corso, lista_iscrizioni.id_classe 
        FROM lista_iscrizioni  INNER JOIN lista_corsi
        ON lista_corsi.id=lista_iscrizioni.id_corso
        WHERE lista_iscrizioni.stato = 'In Corso' OR (avanzamento_completamento >= '80' AND lista_iscrizioni.stato NOT LIKE '%Configurazione%' AND lista_iscrizioni.stato NOT LIKE '%Completato%' AND lista_iscrizioni.stato NOT LIKE '%Scaduto%') ";
$rowsIscrizioni = $dblink->get_results($sql_004);

foreach ($rowsIscrizioni as $rowIscrizione) {

    $sql_000_moodle_001 = "SELECT cs.section, cs.sequence 
        FROM ".MOODLE_DB_NAME.".mdl_course_sections cs 
        WHERE cs.course = ".$rowIscrizione['id_corso_moodle']."
        AND cs.sequence != '' 
        ORDER BY cs.section DESC LIMIT 1";

    $row_000_moodle_001 = $dblink->get_row($sql_000_moodle_001, true);
    if(!empty($row_000_moodle_001)){
        $cmsequence = $row_000_moodle_001['sequence'];
        //in PHP, parse the comma separated list of cm ids and get the last cm id within the section
        $cmarray = explode(',', $cmsequence);
        $lastcmid = array_slice($cmarray, -1)[0];

        $sql_001_moodle_001 = "SELECT cm.id, cm.timemodified 
            FROM ".MOODLE_DB_NAME.".mdl_course_modules_completion cm 
            WHERE cm.coursemoduleid = $lastcmid 
            AND cm.userid = ".$rowIscrizione['id_utente_moodle']."
            AND cm.completionstate = 1";

        $rowCompleto = $dblink->get_row($sql_001_moodle_001, true);

        if($dblink->num_rows($sql_001_moodle_001)){
            
            $rowConfig = $dblink->get_row("SELECT id_fattura, id_fattura_dettaglio FROM lista_iscrizioni WHERE data_fine_iscrizione >= '".date("Y-m-d", $rowCompleto['timemodified'])."' AND data_inizio_iscrizione <= '".date("Y-m-d", $rowCompleto['timemodified'])."' AND abbonamento = '1' AND id_professionista = '".$rowIscrizione['id_professionista']."' AND id_classe = '".$rowIscrizione['id_classe']."' AND stato LIKE 'Configurazione' ", true);
            
            $updateIscrizione = array(
                "dataagg" => date("Y-m-d H:i:s"),
                "scrittore"=>$dblink->filter("autoCorsiCompletati"),
                "stato" => "Completato",
                "data_completamento" => date("Y-m-d H:i:s", $rowCompleto['timemodified']),
                //"data_fine" => date("Y-m-d H:i:s", $rowCompleto['timemodified']),
                "stato_completamento" => "Completato"
            );
            
            if(!empty($rowConfig)){
                $updateIscrizione['id_fattura'] = $rowConfig['id_fattura'];
                $updateIscrizione['id_fattura_dettaglio'] = $rowConfig['id_fattura_dettaglio'];
            }
            
            $ok = $dblink->update("lista_iscrizioni", $updateIscrizione, array("id"=>$rowIscrizione['id']));
            
            //CORSO COMPLETATO
             if($ok){
                if (DISPLAY_DEBUG) echo '<li style="color: GREEN;"> OK ! -> ID ISCRIZIONE : '.$rowIscrizione['id'].'</li>';
                $log->log_all_errors('autoCorsiCompletati.php -> corso  completato correttamente [id_corso_moodle = '.$rowIscrizione['id_corso_moodle'].']','OK');
            }else{
                if (DISPLAY_DEBUG) echo '<li style="color: RED;"> KO ! -> ID ISCRIZIONE : '.$rowIscrizione['id'].'</li>';
                $log->log_all_errors('autoCorsiCompletati.php -> corso NON completato [id_corso_moodle = '.$rowIscrizione['id_corso_moodle'].']','ERRORE');
            }
        }/*else{
            $rowConfig = $dblink->get_row("SELECT id_fattura, id_fattura_dettaglio FROM lista_iscrizioni WHERE data_fine_iscrizione >= '".date("Y-m-d")."' AND data_inizio_iscrizione <= '".date("Y-m-d")."' AND abbonamento = '1' AND id_professionista = '".$rowIscrizione['id_professionista']."' AND id_classe = '".$rowIscrizione['id_classe']."' AND stato LIKE 'Configurazione' AND id_fattura > 0 AND id_fattura_dettaglio > 0 ", true);
            
            if(!empty($rowConfig)){
                if (DISPLAY_DEBUG) echo '<li>CONFIGURAZIONE: '.$dblink->get_query()."</li>";
                $percentuale_corso_utente = recupero_percentuale_avanzamento_corso_utente($rowIscrizione['id_utente_moodle'], $rowIscrizione['id_corso_moodle'], true);
                if (DISPLAY_DEBUG){ 
                    echo '<li>$id_utente_moodle = ' . $rowIscrizione['id_utente_moodle'] . '</li>'; 
                    echo '<li>$id_corso_moodle = ' . $rowIscrizione['id_corso_moodle'] . '</li>'; 
                    echo '<li>$percentuale_corso_utente = ' . $percentuale_corso_utente . '</li>'; 
                }
                
                $updateIscrizione['avanzamento_completamento'] = $percentuale_corso_utente;
                $updateIscrizione['id_fattura'] = $rowConfig['id_fattura'];
                $updateIscrizione['id_fattura_dettaglio'] = $rowConfig['id_fattura_dettaglio'];
                
                $ok = $dblink->update("lista_iscrizioni", $updateIscrizione, array("id"=>$rowIscrizione['id']));
                if (DISPLAY_DEBUG) echo '<li>UPDATE: '.$dblink->get_query()."</li><hr>";
            }
            
        }*/
    }
}

if (DISPLAY_DEBUG) echo '<hr>'.date("H:i:s");
?>