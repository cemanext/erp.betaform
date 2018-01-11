<?php
ini_set('max_execution_time', 290); //5 minuti - 10 secondi
ini_set('memory_limit', '2048M'); // 2 Giga

include_once('../../config/connDB.php');
include_once(BASE_ROOT . 'config/confAccesso.php');
include_once(BASE_ROOT . 'classi/webservice/client.php');

$moodle = new moodleWebService();

if (DISPLAY_DEBUG) {
    echo '<li>'.date('Y-m-d H:i:s').'</li>';
    echo '<li>DB_HOST = '.DB_HOST.'</li>';
    echo '<li>DB_USER = '.DB_USER.'</li>';
    echo '<li>DB_PASS = '.DB_PASS.'</li>';
    echo '<li>DB_NAME = '.DB_NAME.'</li>';
    echo '<li>DB_NAME = '.MOODLE_DB_NAME.'</li>';
    echo '<li>DB_NAME = '.DURATA_CORSO_INGEGNERI.'</li>';
    echo '<li>DB_NAME = '.DURATA_ABBONAMENTO.'</li>';
    echo '<li>DB_NAME = '.DURATA_CORSO.'</li>';
    //AND lista_password.id_moodle_user=152
}

if(isset($_GET['idUtente'])){
    $idUtente = $_GET['idUtente'];
    if(DISPLAY_DEBUG) echo '<li style="color:green;">$idUtente = '.$idUtente.'</li>';
    
    $rs_utente_entrato = $dblink->get_results("SELECT id, username FROM ".MOODLE_DB_NAME.".mdl_user WHERE id='".$idUtente."'");
}else{
    $rs_utente_entrato = $dblink->get_results("SELECT id, username FROM ".MOODLE_DB_NAME.".mdl_user WHERE DATE(FROM_UNIXTIME(lastaccess))=CURDATE() AND HOUR(FROM_UNIXTIME(lastaccess)) = HOUR(NOW()) ORDER BY RAND() LIMIT 1");
}

foreach($rs_utente_entrato as $row_utente_entrato){
    $id_utente_entrato = $row_utente_entrato['id'];
    $username_utente_entrato = $row_utente_entrato['username'];
    if(DISPLAY_DEBUG){ 
        echo '<li style="color:blue;">$id_utente_entrato = '.$id_utente_entrato.'</li>';
        echo '<li style="color:blue;">$username_utente_entrato = '.$username_utente_entrato.'</li>'; 
    }
    $sql_successiva = "SELECT id FROM ".MOODLE_DB_NAME.".mdl_user 
    WHERE YEAR(DATE(FROM_UNIXTIME(lastaccess)))=YEAR(CURDATE()) 
    AND MONTH(DATE(FROM_UNIXTIME(lastaccess))) >=10 
    AND id!='".$id_utente_entrato."' ORDER BY RAND() LIMIT 1";
    $rs_utente_successivo = $dblink->get_results($sql_successiva);
    //echo '<li>$sql_successiva = '.$sql_successiva.'</li>';

    foreach($rs_utente_successivo as $row_utente_successivo){
        $id_utente_successivo = $row_utente_successivo['id'];
        if(DISPLAY_DEBUG) echo '<li>$id_utente_successivo = '.$id_utente_successivo.'</li>';
    }

    if(DISPLAY_DEBUG) echo '<h1>$id_utente_entrato = '.$id_utente_entrato.'</h1>';

    $sql_lista_attivazioni_manuale = "SELECT  mdl_enrol.courseid, 
    mdl_user_enrolments.userid , 
    mdl_user_enrolments.timeend,
    mdl_user_enrolments.timestart,
    mdl_user_enrolments.timecreated,
    lista_iscrizioni.id_classe,
    lista_iscrizioni.id_professionista,
    lista_iscrizioni.data_fine_iscrizione AS data_scadenza,
    lista_iscrizioni.stato AS stato_iscrizione,
    lista_iscrizioni.id_user_enrolments AS id_utente_enrolments,
    mdl_user_enrolments.id AS 'id_user_enrolments',
    (IF(id_classe=10,DATE_ADD(FROM_UNIXTIME(mdl_user_enrolments.timestart), INTERVAL ".DURATA_CORSO_INGEGNERI." DAY),DATE_ADD(FROM_UNIXTIME(mdl_user_enrolments.timecreated), INTERVAL ".DURATA_ABBONAMENTO." DAY))) AS 'data_scadenza_corso',
    IF(id_classe>0,DATE_SUB(lista_iscrizioni.data_fine_iscrizione, INTERVAL ".DURATA_ABBONAMENTO." DAY),FROM_UNIXTIME(mdl_user_enrolments.timestart)) AS 'data_inizio_iscrizione_manuale',
    (IF(id_classe>0,lista_iscrizioni.data_fine_iscrizione,DATE_ADD(FROM_UNIXTIME(mdl_user_enrolments.timestart), INTERVAL ".DURATA_CORSO." DAY))) AS 'data_fine_iscrizione_manuale',
    FROM_UNIXTIME(mdl_user_enrolments.timeend) AS 'data_scadenza_corso_singolo',
    FROM_UNIXTIME(mdl_user_enrolments.timestart) AS 'data_inizio_corso_singolo'
    FROM ".MOODLE_DB_NAME.".`mdl_user_enrolments`
    INNER JOIN ".MOODLE_DB_NAME.".mdl_enrol ON (mdl_user_enrolments.`enrolid`=mdl_enrol.id) 
    INNER JOIN ".DB_NAME.".lista_iscrizioni ON mdl_user_enrolments.userid = lista_iscrizioni.id_utente_moodle
    WHERE 
    (
        (lista_iscrizioni.stato = 'In Attesa di Moodle')
            OR
        (lista_iscrizioni.id_user_enrolments<=0)
    )
    AND lista_iscrizioni.id_utente_moodle='".$id_utente_entrato."'
    AND lista_iscrizioni.data_fine_iscrizione >= CURDATE()
    ORDER BY lista_iscrizioni.id_utente_moodle DESC";
    //AND DATE(FROM_UNIXTIME(mdl_user_enrolments.timecreated))=CURDATE()
    //AND lista_password.id_moodle_user=37129

    //echo '$sql_lista_attivazioni_manuale = '.$sql_lista_attivazioni_manuale;

    //AND DATE(FROM_UNIXTIME(mdl_user_enrolments.timecreated)) = CURDATE()


    $rs_lista_attivazioni_manuale = $dblink->get_results($sql_lista_attivazioni_manuale);

    foreach ($rs_lista_attivazioni_manuale AS $row_lista_attivazioni_manuale){
        
        if (DISPLAY_DEBUG) {
            echo '<br>$id_utente_moodle = '.$id_utente_moodle = $row_lista_attivazioni_manuale['userid'];
            echo '<br>$id_corso_moodle = '.$id_corso_moodle = $row_lista_attivazioni_manuale['courseid'];
            echo '<br>$idClasse = '.$idClasse = $row_lista_attivazioni_manuale['id_classe']; 
            echo '<br>$idProfessionista = '.$idProfessionista = $row_lista_attivazioni_manuale['id_professionista'];
            echo '<br>$dataScadenza = '.$dataScadenza = $row_lista_attivazioni_manuale['data_scadenza'];
            echo '<br>$timestart = '.$timestart = $row_lista_attivazioni_manuale['timestart'];
            echo '<br>$timeend = '.$timeend = $row_lista_attivazioni_manuale['timeend'];
            echo '<br>$timecreated = '.$timecreated = $row_lista_attivazioni_manuale['timecreated'];
            echo '<br>$data_scadenza_corso_singolo = '.$data_scadenza_corso_singolo = $row_lista_attivazioni_manuale['data_scadenza_corso_singolo'];
            echo '<br>$data_inizio_corso_singolo = '.$data_inizio_corso_singolo = $row_lista_attivazioni_manuale['data_inizio_corso_singolo'];
            echo '<br>$data_scadenza_corso = '.$data_scadenza_corso = $row_lista_attivazioni_manuale['data_scadenza_corso'];
            echo '<br>$id_user_enrolments = '.$id_user_enrolments = $row_lista_attivazioni_manuale['id_user_enrolments'];
            echo '<br>$data_inizio_iscrizione_manuale = '.$data_inizio_iscrizione_manuale = $row_lista_attivazioni_manuale['data_inizio_iscrizione_manuale'];
            echo '<br>$data_fine_iscrizione_manuale = '.$data_fine_iscrizione_manuale = $row_lista_attivazioni_manuale['data_fine_iscrizione_manuale'];
            echo '<br>$id_utente_enrolments = '.$id_utente_enrolments = $row_lista_attivazioni_manuale['id_user_enrolments'];
            
            echo "<br><br>";
        }else{
            $id_utente_moodle = $row_lista_attivazioni_manuale['userid'];
            $id_corso_moodle = $row_lista_attivazioni_manuale['courseid'];
            $idClasse = $row_lista_attivazioni_manuale['id_classe']; 
            $idProfessionista = $row_lista_attivazioni_manuale['id_professionista'];
            $dataScadenza = $row_lista_attivazioni_manuale['data_scadenza'];
            $timestart = $row_lista_attivazioni_manuale['timestart'];
            $timeend = $row_lista_attivazioni_manuale['timeend'];
            $timecreated = $row_lista_attivazioni_manuale['timecreated'];
            $data_scadenza_corso_singolo = $row_lista_attivazioni_manuale['data_scadenza_corso_singolo'];
            $data_inizio_corso_singolo = $row_lista_attivazioni_manuale['data_inizio_corso_singolo'];
            $data_scadenza_corso = $row_lista_attivazioni_manuale['data_scadenza_corso'];
            $id_user_enrolments = $row_lista_attivazioni_manuale['id_user_enrolments'];
            $data_inizio_iscrizione_manuale = $row_lista_attivazioni_manuale['data_inizio_iscrizione_manuale'];
            $data_fine_iscrizione_manuale = $row_lista_attivazioni_manuale['data_fine_iscrizione_manuale'];
            $id_utente_enrolments = $row_lista_attivazioni_manuale['id_user_enrolments'];
        }

        $id_corso = $dblink->get_row("SELECT id, id_prodotto, nome_prodotto FROM lista_corsi WHERE id_corso_moodle = '".$id_corso_moodle."' LIMIT 1", true);
        $id_classe = $dblink->get_row("SELECT nome FROM lista_classi WHERE id = '".$idClasse."' LIMIT 1", true);

        $sql_controllo_completati = "SELECT id, stato_completamento FROM lista_iscrizioni 
        WHERE id_utente_moodle = '".$id_utente_moodle."'
        AND id_corso = '".$id_corso['id']."'
        AND stato = 'Completato' 
        AND id_user_enrolments = '".$id_utente_enrolments."'";
        if (DISPLAY_DEBUG) echo $sql_controllo_completati."<br><br>";
        $numCompletati = $dblink->num_rows($sql_controllo_completati);

        if($numCompletati<=0){
        
            $sql_controllo_se_attivazione_presente = "SELECT id, stato_completamento FROM lista_iscrizioni 
            WHERE id_utente_moodle = '".$id_utente_moodle."'
            AND id_corso = '".$id_corso['id']."'
            AND data_fine_iscrizione>=CURDATE()";
            //echo $sql_controllo_se_attivazione_presente."<br>";

            $controllo = $dblink->get_row($sql_controllo_se_attivazione_presente, true); 
            if($controllo['id']>0){
                   //update
                $percentuale_corso_utente = recupero_percentuale_avanzamento_corso_utente($id_utente_moodle, $id_corso_moodle, true); 
                $ok = $dblink->update(DB_NAME.".lista_iscrizioni", array("avanzamento_completamento" => $percentuale_corso_utente), array("id" => $controllo['id']));

                $update = array(
                    "dataagg" => date("Y-m-d H:i:s"),
                    "scrittore" => "autoRecuperaCorsiUtentiMoodle",
                    "id_corso" => $id_corso['id'],
                    "id_classe" => 0,
                    "id_professionista" => $idProfessionista,
                    //"data_inizio_iscrizione" => $data_inizio_corso_singolo,
                    //"data_fine_iscrizione" => $data_scadenza_corso_singolo,
                    "data_inizio" =>  date("Y-m-d H:i:s", $timestart),
                    "data_fine" => date("Y-m-d H:i:s", $timeend),
                    "nome_corso" => $dblink->filter($id_corso['nome_prodotto']),
                    "nome_classe" => "",
                    "abbonamento" => '0',
                    "id_utente_moodle" => $id_utente_moodle,
                    "id_user_enrolments" => $id_user_enrolments,
                    "stato" => "In Attesa",
                );
                //echo '<pre>$update = '.print_r($update).'</pre>';
                if($controllo['stato_completamento'] != 'Completato'){
                   //$ok = $dblink->update(DB_NAME.".lista_iscrizioni", $update, array("id" => $controllo['id'], "stato"=>"In Attesa"));
                   $ok = $dblink->update(DB_NAME.".lista_iscrizioni", $update, array("id" => $controllo['id'], "stato"=>"In Attesa di Moodle"));
                   if($ok){
                       //if (DISPLAY_DEBUG) echo '<li style="color: GREEN;"> $dblink->update OK !</li>';
                       echo '<li style="color: GREEN;"> $dblink->update OK !</li>';
                       if (DISPLAY_DEBUG) echo $dblink->get_query()."<br>";
                       //echo $dblink->get_query()."<br>";
                       $log->log_all_errors('autoRecuperaCorsiUtentiMoodleManuale.php -> $dblink->update OK ! [id_utente_moodle = '.$id_utente_moodle.']','OK');
                   }
                }else{
                   $ok = true;
                }


            }else{
                //insert
                $insert = array(
                    "dataagg" => date("Y-m-d H:i:s"),
                    "scrittore" => "autoRecuperaCorsiUtentiMoodle",
                    "id_corso" => $id_corso['id'],
                    "id_classe" => $idClasse,
                    "id_professionista" => $idProfessionista,
                    "data_inizio_iscrizione" => $data_inizio_iscrizione_manuale,
                    "data_fine_iscrizione" => $data_fine_iscrizione_manuale,
                    "data_inizio" =>  $idClasse==0 ? $data_inizio_iscrizione_manuale :  date("Y-m-d H:i:s", $timestart),
                    "data_fine" => $idClasse==0 ? $data_fine_iscrizione_manuale :  $data_scadenza_corso,
                    "nome_corso" => $dblink->filter($id_corso['nome_prodotto']),
                    "nome_classe" => $dblink->filter($id_classe['nome']),
                    "abbonamento" => $idClasse==0 ? '0' : '1',
                    "id_utente_moodle" => $id_utente_moodle,
                    "id_user_enrolments" => $id_user_enrolments,
                    "stato" => "In Attesa",
                );

                $ok = $dblink->insert(DB_NAME.".lista_iscrizioni", $insert);
                if (DISPLAY_DEBUG) echo '<pre>$insert = '.print_r($insert).'</pre>';
                //echo '<pre>$insert = '.print_r($insert).'</pre>';
            }


           if($ok){
               if (DISPLAY_DEBUG) echo '<li style="color: GREEN;"> OK !</li>';
               $log->log_all_errors('autoRecuperaCorsiUtentiMoodleManuale.php -> attivazione creata correttamente [id_utente_moodle = '.$id_utente_moodle.']','OK');
           }else{
               if (DISPLAY_DEBUG) echo '<li style="color: RED;"> KO !</li>';
               $log->log_all_errors('autoRecuperaCorsiUtentiMoodleManuale.php -> attivazione NON creata [id_utente_moodle = '.$id_utente_moodle.']','ERRORE');
           }


           if (DISPLAY_DEBUG) echo '<hr>';
       }
    }
}

if (DISPLAY_DEBUG) echo '<li>'.date('Y-m-d H:i:s').'</li>';

if($id_utente_successivo>0){
    if(DISPLAY_DEBUG) echo '<center>Attendere 10 Secondi ... </center><META HTTP-EQUIV="Refresh" CONTENT="10; url='.BASE_URL.'/libreria/automazioni/autoRecuperaCorsiUtentiMoodle_Multiplo.php?idUtente='.$id_utente_successivo.'">';
}else{
    if(DISPLAY_DEBUG) echo '<li>FINITO</li>';
}
?>