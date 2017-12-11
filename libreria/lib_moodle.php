<?php

//CREO LISTA_PASSWORD E UTENTE MOODLE
function creaUtenteTotale($idProfessionista) {
    global $dblink, $moodle;
    
    $sql_0006 = "UPDATE lista_professionisti
                SET codice = CONCAT('".SUFFISSO_CODICE_CLIENTE."',RIGHT(concat('0000000000',id),6)) 
                WHERE codice NOT LIKE '".SUFFISSO_CODICE_CLIENTE."'";
    $dblink->query($sql_0006);
    
    $esisteInListaPassword = $dblink->num_rows("SELECT id FROM lista_password WHERE id_professionista = '" . $idProfessionista . "' AND livello='cliente' ");
    
    if($esisteInListaPassword >= 1){
        resetPasswordUtenteMoodle($idProfessionista);
        $ok = true;
    }else{
        $passwordUser = generaPassword(9);
        $sql_00001 = "INSERT INTO `lista_password` (`id`, `dataagg`, `scrittore`, `id_professionista`, `id_classe`, `livello`, `nome`, `cognome`, `username`, `passwd`, `cellulare`, `email`, `stato`, `data_creazione`, `data_scadenza`)
                SELECT DISTINCT '', NOW(), '" . addslashes($_SESSION['cognome_nome_utente']) . "', `id`, `id_classe`, 'cliente', `nome`, `cognome`, LCASE(codice), '" . $passwordUser . "', `cellulare`, `email`, 'In Attesa di Moodle', CURDATE(), DATE_ADD(CURDATE(), INTERVAL " . DURATA_ABBONAMENTO . " DAY) FROM lista_professionisti WHERE id=" . $idProfessionista;
        $ok = $dblink->query($sql_00001);
    }
    if ($ok) {
        $row_0001 = $dblink->get_row("SELECT username, email, nome, cognome, passwd FROM lista_password WHERE id_professionista = '" . $idProfessionista . "' ", true);
        //SERVONO PARAMETRI
        $return = $moodle->creaUtenteMoodle($row_0001['username'], $row_0001['email'], $row_0001['nome'], $row_0001['cognome'], $row_0001['passwd'], $idProfessionista);
        if ($return > 0) {
            $sql_update_password = "UPDATE lista_password 
                    SET `id_moodle_user` = '" . $return . "',
                    stato = 'Attivo - Inviare Password'
                    WHERE id_professionista = '" . $idProfessionista . "'";
            $rs_update_password = $dblink->query($sql_update_password);
            if ($rs_update_password) {

                $sql_update_professionisti = "UPDATE lista_professionisti 
                        SET `id_moodle_user` = '" . $return . "'
                        WHERE id = '" . $idProfessionista . "'";
                $rs_update_professionisti = $dblink->query($sql_update_professionisti);
                /*
                $stato_email = inviaEmailTemplate_Base($idProfessionista, 'creaUtenteTotale');
                if ($stato_email) {
                    return true;
                } else {
                    return false;
                }*/
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function resetPasswordUtenteMoodle($idProfessionista, $forzaReset = false){
    global $dblink, $moodle, $log;
    
    $sql_0006 = "UPDATE lista_professionisti
                SET codice = CONCAT('".SUFFISSO_CODICE_CLIENTE."',RIGHT(concat('0000000000',id),6)) 
                WHERE codice NOT LIKE '".SUFFISSO_CODICE_CLIENTE."'";
    $dblink->query($sql_0006);
    
    if($forzaReset){
        $statoUtente = "Attivo";
        $sql_cerca_in_lista_password = "SELECT DISTINCT * FROM lista_password WHERE id_professionista = '".$idProfessionista."' AND livello='cliente'";
    }else{
        $statoUtente = "Attivo - Inviare Password";
        $sql_cerca_in_lista_password = "SELECT DISTINCT * FROM lista_password WHERE id_professionista = '".$idProfessionista."' AND livello='cliente' "
            . "AND (DATE(data_creazione)<DATE_SUB(CURDATE(), INTERVAL ".DURATA_PASSWORD_UTENTE." DAY) OR (passwd IS NULL OR LENGTH(passwd)<=0) OR username LIKE '%@%')";
    //echo "<br>";
    }
    $row_cerca_in_lista_password = $dblink->get_row($sql_cerca_in_lista_password,true);
    

    if(!empty($row_cerca_in_lista_password)){
        
        $rowProfessionista = $dblink->get_row("SELECT codice, id_moodle_user, email FROM lista_professionisti WHERE id = '".$idProfessionista."' AND codice LIKE '%".SUFFISSO_CODICE_CLIENTE."%'", true);
        
        if(!empty($rowProfessionista)){
            $dblink->update(MOODLE_DB_NAME.".mdl_user", array("username" => $rowProfessionista['codice']), array("id" => $rowProfessionista['id_moodle_user'], "idnumber" => $idProfessionista));
            $usernameUpdate = "username = LCASE('".$rowProfessionista['codice']."'), email = LCASE('".$rowProfessionista['email']."'),";
        }else{
            $usernameUpdate = "";
        }
        
        $password = generaPassword(9);
        
        if (DISPLAY_DEBUG) {
            echo '<li style="color:;">Aggiorniamo Password ! </li>';
            //echo '<br>'.$password = $row_cerca_in_lista_password['passwd'];
            echo '<br>'.$idPassword = $row_cerca_in_lista_password['id'];
        }else{
            $idPassword = $row_cerca_in_lista_password['id'];
        }
        
        $sql_aggiorna_pass = "UPDATE lista_password 
        SET 
        dataagg = NOW(),
        scrittore = 'lib_moodle',
        ".$usernameUpdate."
        passwd='".$password."',
        data_scadenza = DATE_ADD(CURDATE(), INTERVAL ".DURATA_ABBONAMENTO." DAY)
        WHERE id = ".$idPassword;
        $ok = $dblink->query($sql_aggiorna_pass);

        $sql_cerca_in_lista_password = "SELECT DISTINCT * FROM lista_password WHERE id_professionista = '".$idProfessionista."'";
        $row_cerca_in_lista_password = $dblink->get_row($sql_cerca_in_lista_password,true);
        if (DISPLAY_DEBUG) {
            echo '<br>'.$username = $row_cerca_in_lista_password['username'];
            echo '<br>'.$email = $row_cerca_in_lista_password['email'];
            echo '<br>'.$firstname = $row_cerca_in_lista_password['nome'];
            echo '<br>'.$lastname = $row_cerca_in_lista_password['cognome'];
            echo '<br>'.$password = $row_cerca_in_lista_password['passwd'];
            echo '<br>'.$idnumber = $row_cerca_in_lista_password['id_professionista'];
        }else{
            $username = $row_cerca_in_lista_password['username'];
            $email = $row_cerca_in_lista_password['email'];
            $firstname = $row_cerca_in_lista_password['nome'];
            $lastname = $row_cerca_in_lista_password['cognome'];
            $password = $row_cerca_in_lista_password['passwd'];
            $idnumber = $row_cerca_in_lista_password['id_professionista'];
        }

        $idUtenteMoodle = $moodle->creaUtenteMoodle($username, $email, $firstname, $lastname, $password, $idnumber);

        if($idUtenteMoodle>0){
            $sql_aggiorna_password = "UPDATE lista_password 
            SET id_moodle_user = '".$idUtenteMoodle."' , 
            dataagg = NOW(),
            data_creazione = NOW(),
            stato = '".$statoUtente."'
            WHERE id_professionista = '".$idProfessionista."'";
            $ok = $dblink->query($sql_aggiorna_password);
            $log->log_all_errors('lib_moodle.php -> Password utente resettata correttamente [idProfessionista = '.$idProfessionista.']','OK');
        }else{
            if (DISPLAY_DEBUG) echo '<li style="color: RED;"> KO !</li>';
            $log->log_all_errors('lib_moodle.php -> Password utente NON resettata [idProfessionista = '.$idProfessionista.']','ERRORE');
        }
    }
    
}

//CREO ATTIVAZIONE CORSO
function attivaCorsoFattura($idProfessionista, $idFattura, $idFatturaDettaglio, $idCorso, $idUtenteMoodle, $idCorsoMoodle, $notifica = true) {
    global $dblink, $moodle, $log;
    
    $sqlConfigurazioneCorso = "SELECT * FROM lista_iscrizioni WHERE id_utente_moodle='".$idUtenteMoodle."' AND id_professionista='$idProfessionista' AND abbonamento='0' AND id_corso='$idCorso' AND (stato LIKE 'In Attesa' OR stato LIKE 'In Corso' OR stato LIKE 'In Attesa di Moodle')";
    $rowConfigurazioneCorso = $dblink->get_row($sqlConfigurazioneCorso,true);
    if(!empty($rowConfigurazioneCorso)){
        $dataScadenzaCorso = $rowConfigurazioneCorso['data_fine_iscrizione'];
        $sql_update_corso = "UPDATE lista_iscrizioni SET dataagg=NOW(), scrittore='annullatoPrimaDellaScadenzaPerRinnovo', stato='Scaduto e Disattivato', data_fine_iscrizione = DATE_SUB(NOW(), INTERVAL 1 DAY) WHERE id_utente_moodle='".$idUtenteMoodle."' AND id_professionista='$idProfessionista' AND abbonamento='0' AND id_corso='$idCorso' AND (stato LIKE 'In Corso' OR stato LIKE 'In Attesa')";
        $dblink->query($sql_update_corso);
        //include_once(BASE_ROOT.'libreria/automazioni/autoAnnullaCorsiAbbonamenti.php');
        //sleep(1);
        $dateTimeScadenzaCorso = new DateTime($dataScadenzaCorso);
        $datetimeOggi = new DateTime(date("Y-m-d"));    
        $interval = $datetimeOggi->diff($dateTimeScadenzaCorso);
        $giorniDifferenza = $interval->format('%a');
    }
    
    resetPasswordUtenteMoodle($idProfessionista);
    
    $sql_00001_01 = "SELECT DISTINCT id, id_classe, (SELECT DISTINCT nome FROM lista_classi WHERE id = id_classe) AS nomeClasse,
            DATE_ADD(CURDATE(), INTERVAL ".DURATA_CORSO." DAY) AS data_scadenza_corso,
            UNIX_TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL " . (DURATA_CORSO+$giorniDifferenza) . " DAY)) AS data_scadenza_corso_timestamp,
            (SELECT DISTINCT codice_esterno FROM lista_classi WHERE id = id_classe) AS codiceClasse
            FROM lista_professionisti WHERE id_moodle_user ='" . $idUtenteMoodle . "' LIMIT 1";
    $row_00001_01 = $dblink->get_row($sql_00001_01, true);
    if (!empty($row_00001_01)) {
        $nomeClasse = $row_00001_01['nomeClasse'];
        $data_scadenza_corso = str_replace('-', '/', GiraDataOra($row_00001_01['data_scadenza_corso']));
        $data_scadenza_corso_timestamp = $row_00001_01['data_scadenza_corso_timestamp'];
    }

    $tipoVendita = 'Singolo';
    $nomeClasse = '';
    $var = $moodle->iscrizioneCorsoMoodle($idUtenteMoodle, $idCorsoMoodle, $nomeClasse, $tipoVendita, $data_scadenza_corso_timestamp);

    if ($var === true) {

        $sql_00001 = "SELECT DISTINCT *, DATE_ADD(CURDATE(), INTERVAL " . (DURATA_CORSO+$giorniDifferenza) . " DAY) AS data_scadenza_corso 
            FROM lista_fatture_dettaglio WHERE id='" . $idFatturaDettaglio . "' LIMIT 1";
        $rs_00001 = $dblink->get_results($sql_00001);
        
        if (!empty($rs_00001)) {
            foreach ($rs_00001 as $row_00001) {
                //$data_scadenza_corso = str_replace('-', '/', GiraDataOra($row_00001['data_scadenza_corso']));
                //`id`, `dataagg`, `scrittore`, `stato`, `id_corso`, `id_classe`, `id_professionista`, `data_inizio_iscrizione`, `data_fine_iscrizione`, `data_inizio`, `data_fine`, `data_completamento`, `stato_completamento`, `avanzamento_completamento`, `nome_corso`, `nome_classe`, `cognome_nome_professionista`, `id_fattura`, `id_fattura_dettaglio`
                
                $sql_iscrivi_corso = "INSERT INTO `lista_iscrizioni` (`id`, `dataagg`, `scrittore`, `stato`, `id_corso`, `id_professionista`, `data_inizio_iscrizione`, `data_fine_iscrizione`,
                                        `id_fattura`, `id_fattura_dettaglio`, id_utente_moodle) SELECT DISTINCT '', NOW(), '" . addslashes($_SESSION['cognome_nome_utente']) . "', 'In Attesa di Moodle',  '" . $idCorso . "', `id_professionista`, CURDATE(), DATE_ADD(CURDATE(), INTERVAL " . DURATA_CORSO . " DAY),  `id`, '" . $idFatturaDettaglio . "', '" . $idUtenteMoodle . "'
                                        FROM lista_fatture WHERE id=" . $idFattura;
                $rs_iscrivi_corso = $dblink->query($sql_iscrivi_corso);
                if ($rs_iscrivi_corso) {
                    /*$sql_007_update = "UPDATE lista_iscrizioni, lista_professionisti
                        SET lista_iscrizioni.id_classe = lista_professionisti.id_classe,
                        lista_iscrizioni. cognome_nome_professionista = CONCAT(lista_professionisti.cognome,' ',lista_professionisti.nome)
                        WHERE lista_iscrizioni.id_professionista = lista_professionisti.id";
                    $rs_007_update = mysql_query($sql_007_update);

                    $sql_007_update = "UPDATE lista_iscrizioni, lista_classi
                        SET lista_iscrizioni. nome_classe = lista_classi.nome
                        WHERE lista_iscrizioni.id_classe = lista_classi.id";
                    $rs_007_update = mysql_query($sql_007_update);*/

                    $sql_007_update = "UPDATE lista_iscrizioni, lista_corsi
                        SET lista_iscrizioni.nome_corso = lista_corsi.nome_prodotto
                        WHERE lista_iscrizioni.id_corso = lista_corsi.id AND lista_iscrizioni.abbonamento='0'";
                    $rs_007_update = $dblink->query($sql_007_update);

                    if ($notifica === false) {
                        return true;
                    } else {
                        $stato_email = inviaEmailTemplate_Base($idProfessionista, 'attivaCorsoFattura', $idFatturaDettaglio);
                        if ($stato_email) {
                            return true;
                        } else {
                            inviaEmailTemplate_Base($idProfessionista, 'attivaCorsoFatturaErroreMail', $idFatturaDettaglio);
                            return true;
                        }
                    }
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }
    } else {
        return false;
    }
}


function attivaPacchettoFattura($idProfessionista, $idFattura, $idFatturaDettaglio, $idProdotto, $idCorso, $idUtenteMoodle, $idCorsoMoodle, $notifica = true) {
    global $dblink, $moodle, $log;
    
    $sqlConfigurazioneCorso = "SELECT * FROM lista_iscrizioni WHERE id_utente_moodle='".$idUtenteMoodle."' AND id_professionista='$idProfessionista' AND abbonamento='$idProdotto' AND id_corso='$idCorso' AND (stato LIKE 'In Attesa' OR stato LIKE 'In Corso' OR stato LIKE 'In Attesa di Moodle')";
    $rowConfigurazioneCorso = $dblink->get_row($sqlConfigurazioneCorso,true);
    if(!empty($rowConfigurazioneCorso)){
        $dataScadenzaCorso = $rowConfigurazioneCorso['data_fine_iscrizione'];
        $sql_update_corso = "UPDATE lista_iscrizioni SET dataagg=NOW(), scrittore='annullatoPrimaDellaScadenzaPerRinnovo', stato='Scaduto e Disattivato', data_fine_iscrizione = DATE_SUB(NOW(), INTERVAL 1 DAY) WHERE id_utente_moodle='".$idUtenteMoodle."' AND id_professionista='$idProfessionista' AND abbonamento='$idProdotto' AND id_corso='$idCorso' AND (stato LIKE 'In Corso' OR stato LIKE 'In Attesa')";
        $dblink->query($sql_update_corso);
        //include_once(BASE_ROOT.'libreria/automazioni/autoAnnullaCorsiAbbonamenti.php');
        //sleep(1);
        $dateTimeScadenzaCorso = new DateTime($dataScadenzaCorso);
        $datetimeOggi = new DateTime(date("Y-m-d"));    
        $interval = $datetimeOggi->diff($dateTimeScadenzaCorso);
        $giorniDifferenza = $interval->format('%a');
    }
    
    resetPasswordUtenteMoodle($idProfessionista);
    
    $sql_00001_01 = "SELECT DISTINCT id, id_classe, (SELECT DISTINCT nome FROM lista_classi WHERE id = id_classe) AS nomeClasse,
            DATE_ADD(CURDATE(), INTERVAL ".DURATA_CORSO." DAY) AS data_scadenza_corso,
            UNIX_TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL " . (DURATA_CORSO+$giorniDifferenza) . " DAY)) AS data_scadenza_corso_timestamp,
            (SELECT DISTINCT codice_esterno FROM lista_classi WHERE id = id_classe) AS codiceClasse
            FROM lista_professionisti WHERE id_moodle_user ='" . $idUtenteMoodle . "' LIMIT 1";
    $row_00001_01 = $dblink->get_row($sql_00001_01, true);
    if (!empty($row_00001_01)) {
        $nomeClasse = $row_00001_01['nomeClasse'];
        $data_scadenza_corso = str_replace('-', '/', GiraDataOra($row_00001_01['data_scadenza_corso']));
        $data_scadenza_corso_timestamp = $row_00001_01['data_scadenza_corso_timestamp'];
    }

    $tipoVendita = 'Singolo';
    $nomeClasse = '';
    $var = $moodle->iscrizioneCorsoMoodle($idUtenteMoodle, $idCorsoMoodle, $nomeClasse, $tipoVendita, $data_scadenza_corso_timestamp);

    if ($var === true) {

        $sql_00001 = "SELECT DISTINCT *, DATE_ADD(CURDATE(), INTERVAL " . (DURATA_CORSO+$giorniDifferenza) . " DAY) AS data_scadenza_corso 
            FROM lista_fatture_dettaglio WHERE id='" . $idFatturaDettaglio . "' LIMIT 1";
        $rs_00001 = $dblink->get_results($sql_00001);
        
        if (!empty($rs_00001)) {
            foreach ($rs_00001 as $row_00001) {
                //$data_scadenza_corso = str_replace('-', '/', GiraDataOra($row_00001['data_scadenza_corso']));
                //`id`, `dataagg`, `scrittore`, `stato`, `id_corso`, `id_classe`, `id_professionista`, `data_inizio_iscrizione`, `data_fine_iscrizione`, `data_inizio`, `data_fine`, `data_completamento`, `stato_completamento`, `avanzamento_completamento`, `nome_corso`, `nome_classe`, `cognome_nome_professionista`, `id_fattura`, `id_fattura_dettaglio`
                
                $sql_iscrivi_corso = "INSERT INTO `lista_iscrizioni` (`id`, `dataagg`, `scrittore`, `stato`, `id_corso`, `id_professionista`, `data_inizio_iscrizione`, `data_fine_iscrizione`,
                                        `id_fattura`, `id_fattura_dettaglio`, id_utente_moodle, abbonamento) SELECT DISTINCT '', NOW(), '" . addslashes($_SESSION['cognome_nome_utente']) . "', 'In Attesa di Moodle',  '" . $idCorso . "', `id_professionista`, CURDATE(), DATE_ADD(CURDATE(), INTERVAL " . DURATA_CORSO . " DAY),  `id`, '" . $idFatturaDettaglio . "', '" . $idUtenteMoodle . "', '$idProdotto'
                                        FROM lista_fatture WHERE id=" . $idFattura;
                $rs_iscrivi_corso = $dblink->query($sql_iscrivi_corso);
                if ($rs_iscrivi_corso) {
                    /*$sql_007_update = "UPDATE lista_iscrizioni, lista_professionisti
                        SET lista_iscrizioni.id_classe = lista_professionisti.id_classe,
                        lista_iscrizioni. cognome_nome_professionista = CONCAT(lista_professionisti.cognome,' ',lista_professionisti.nome)
                        WHERE lista_iscrizioni.id_professionista = lista_professionisti.id";
                    $rs_007_update = mysql_query($sql_007_update);

                    $sql_007_update = "UPDATE lista_iscrizioni, lista_classi
                        SET lista_iscrizioni. nome_classe = lista_classi.nome
                        WHERE lista_iscrizioni.id_classe = lista_classi.id";
                    $rs_007_update = mysql_query($sql_007_update);*/

                    $sql_007_update = "UPDATE lista_iscrizioni, lista_corsi
                        SET lista_iscrizioni.nome_corso = lista_corsi.nome_prodotto
                        WHERE lista_iscrizioni.id_corso = lista_corsi.id AND lista_iscrizioni.abbonamento='$idProdotto'";
                    $rs_007_update = $dblink->query($sql_007_update);

                    if($notifica === false) {
                        return true;
                    } else {
                        $stato_email = inviaEmailTemplate_Base($idProfessionista, 'attivaCorsoFattura', $idFatturaDettaglio);
                        if ($stato_email) {
                            return true;
                        } else {
                            inviaEmailTemplate_Base($idProfessionista, 'attivaCorsoFatturaErroreMail', $idFatturaDettaglio);
                            return true;
                        }
                    }
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }
    } else {
        return false;
    }
}



//CREO ATTIVAZIONE ABBONAMENTO
function attivaAbbonamentoFattura($idProfessionista, $idFattura, $idFatturaDettaglio, $idUtenteMoodle, $notifica = true) {
    global $dblink, $moodle, $log;

    $sqlConfigurazione = "SELECT * FROM lista_iscrizioni WHERE id_utente_moodle='".$idUtenteMoodle."' AND id_professionista='$idProfessionista' AND abbonamento='1' AND stato LIKE 'Configurazione'";
    $rowConfigurazione = $dblink->get_row($sqlConfigurazione,true);
    
    $giorniDifferenza = 0;
    
    if(!empty($rowConfigurazione)){
        $dataScadenzaAbb = $rowConfigurazione['data_fine_iscrizione'];
        $sql_update_corso = "UPDATE lista_iscrizioni SET dataagg=NOW(), scrittore='annullatoPrimaDellaScadenzaPerRinnovo', stato='Scaduto e Disattivato', data_fine_iscrizione = DATE_SUB(NOW(), INTERVAL 1 DAY) WHERE id_utente_moodle='".$idUtenteMoodle."' AND id_professionista='$idProfessionista' AND abbonamento='1' AND (stato LIKE 'In Corso' OR stato LIKE 'In Attesa')";
        $dblink->query($sql_update_corso);
        $sql_update_configurazione = "UPDATE lista_iscrizioni SET dataagg=NOW(), scrittore='annullatoPrimaDellaScadenzaPerRinnovo', stato='Configurazione Scaduta e Disattivata', data_fine_iscrizione = DATE_SUB(NOW(), INTERVAL 1 DAY) WHERE id_utente_moodle='".$idUtenteMoodle."' AND id_professionista='$idProfessionista' AND abbonamento='1' AND (stato LIKE 'Configurazione')";
        $dblink->query($sql_update_configurazione);
        //include_once(BASE_ROOT.'libreria/automazioni/autoAnnullaCorsiAbbonamenti.php');
        //sleep(1);
        $dateTimeScadenzaAbb = new DateTime($dataScadenzaAbb);
        $datetimeOggi = new DateTime(date("Y-m-d"));
        $interval = $datetimeOggi->diff($dateTimeScadenzaAbb);
        $giorniDifferenza = $interval->format('%a');
        
    }
    
    resetPasswordUtenteMoodle($idProfessionista);
    
    $sql_update_professionisti = "UPDATE lista_professionisti 
            SET `id_moodle_user` = '" . $idUtenteMoodle . "'
            WHERE id = '" . $idProfessionista . "'";
    $rs_update_professionisti = $dblink->query($sql_update_professionisti);

    $sql_00001_01 = "SELECT DISTINCT id, id_classe, (SELECT DISTINCT nome FROM lista_classi WHERE id = id_classe) AS nomeClasse,
            (SELECT DISTINCT codice_esterno FROM lista_classi WHERE id = id_classe) AS codiceClasse,
            DATE_ADD(CURDATE(), INTERVAL " . (DURATA_ABBONAMENTO+$giorniDifferenza) . " DAY) AS data_scadenza_abbonamento
            FROM lista_professionisti WHERE id_moodle_user ='" . $idUtenteMoodle . "' 
            AND id = '" . $idProfessionista . "'
            LIMIT 1";
    $row_00001_01 = $dblink->get_row($sql_00001_01, true);
    
    if (!empty($row_00001_01)) {
        //while ($row_00001_01 = mysql_fetch_array($rs_00001_01, MYSQL_BOTH)) {
            $nomeClasse = $row_00001_01['nomeClasse'];
            $data_scadenza_abbonamento = str_replace('-', '/', GiraDataOra($row_00001_01['data_scadenza_abbonamento']));
        //}
    }

    $var = $moodle->iscrizioneAbbonamentoMoodle($idUtenteMoodle, $nomeClasse, $data_scadenza_abbonamento);

    if ($var == true) {
        //RECUPERO TUTTI I CORSI DELL'ABBONAMENTO
        /*
          $sql_0000000001 = "SELECT DISTINCT lista_corsi.id, lista_corsi.id_corso_moodle, lista_corsi.nome_prodotto
          FROM `lista_prodotti_dettaglio` INNER JOIN lista_corsi ON lista_prodotti_dettaglio.id_prodotto = lista_corsi.id_prodotto
          INNER JOIN lista_fatture_dettaglio ON lista_prodotti_dettaglio.id_prodotto_0 = lista_fatture_dettaglio.id_prodotto
          WHERE lista_fatture_dettaglio.id='".$idFatturaDettaglio."' ORDER BY `lista_corsi`.`nome_prodotto` ASC ";
         */
        //CREO UN SOLA RIGA DI CONFIGURAZIONE
        $sql_0000000001 = "SELECT DISTINCT * 
                 FROM lista_fatture_dettaglio WHERE id='" . $idFatturaDettaglio . "'";
        $rs_0000000001 = $dblink->get_results($sql_0000000001);
        if (!empty($rs_0000000001)) {
            foreach ($rs_0000000001 as $row_0000000001) {

                //INSERIVO TUTTI I CORSI DI QUELL'ABBONAMENTO
                /*
                  $idCorso = $row_0000000001['id'];
                  $sql_iscrivi_corso = "INSERT INTO `lista_iscrizioni` (`id`, `dataagg`, `scrittore`, `stato`, `id_corso`, `id_professionista`, `data_inizio_iscrizione`, `data_fine_iscrizione`,
                  `id_fattura`, `id_fattura_dettaglio`, `abbonamento`) SELECT DISTINCT '', NOW(), '".addslashes($_SESSION['cognome_nome_utente'])."', 'In Attesa',  '".$idCorso."', '".$idProfessionista."', CURDATE(), DATE_ADD(CURDATE(), INTERVAL ".DURATA_ABBONAMENTO." DAY),  `id_fattura`,  `id`, '1'
                  FROM lista_fatture_dettaglio WHERE id=".$idFatturaDettaglio;
                 */
                
                $sql_iscrivi_corso = "INSERT INTO `lista_iscrizioni` (`id`, `dataagg`, `scrittore`, `stato`, `id_professionista`, `data_inizio_iscrizione`, `data_fine_iscrizione`,
                             `id_fattura`, `id_fattura_dettaglio`, `abbonamento`, id_utente_moodle) SELECT DISTINCT '', NOW(), '" . addslashes($_SESSION['cognome_nome_utente']) . "', 'Configurazione',  '" . $idProfessionista . "', CURDATE(), DATE_ADD(CURDATE(), INTERVAL " . (DURATA_ABBONAMENTO+$giorniDifferenza) . " DAY),  `id_fattura`,  `id`, '1', '" . $idUtenteMoodle . "'
                             FROM lista_fatture_dettaglio WHERE id=" . $idFatturaDettaglio;
                $rs_iscrivi_corso = $dblink->query($sql_iscrivi_corso);
                $idInsert = $dblink->lastid();
                
                if ($rs_iscrivi_corso) {

                    $sql_007_update = "UPDATE lista_iscrizioni, lista_professionisti
                            SET lista_iscrizioni.id_classe = lista_professionisti.id_classe,
                            lista_iscrizioni. cognome_nome_professionista = CONCAT(lista_professionisti.cognome,' ',lista_professionisti.nome)
                            WHERE lista_iscrizioni.id_professionista = lista_professionisti.id AND lista_iscrizioni.abbonamento='1'
                            AND lista_iscrizioni.id = '$idInsert'";
                    $rs_007_update = $dblink->query($sql_007_update);

                    $sql_007_update = "UPDATE lista_iscrizioni, lista_classi
                            SET lista_iscrizioni. nome_classe = lista_classi.nome
                            WHERE lista_iscrizioni.id_classe = lista_classi.id AND lista_iscrizioni.abbonamento='1'
                            AND lista_iscrizioni.id = '$idInsert'";
                    $rs_007_update = $dblink->query($sql_007_update);

                    $sql_007_update = "UPDATE lista_iscrizioni, lista_corsi
                            SET lista_iscrizioni.nome_corso = lista_corsi.nome_prodotto
                            WHERE lista_iscrizioni.id_corso = lista_corsi.id AND lista_iscrizioni.abbonamento='1'
                            AND lista_iscrizioni.id = '$idInsert'";
                    $rs_007_update = $dblink->query($sql_007_update);
                    if ($notifica === false) {
                        return true;
                    }
                } else {
                    $log->log_all_errors('attivaAbbonamentoFattura -> sql_iscrivi_corso', 'ERRORE');
                    return false;
                }
            }
            $stato_email = inviaEmailTemplate_Base($idProfessionista, 'attivaAbbonamentoFattura', $idFatturaDettaglio);
            if ($stato_email) {
                $log->log_all_errors('attivaAbbonamentoFattura ->  Email Abbonamento Inviata Correttamente [idFatturaDettaglio = ' . $idFatturaDettaglio . ']', 'OK');
                return true;
            } else {
                inviaEmailTemplate_Base($idProfessionista, 'attivaAbbonamentoFatturaErroreMail', $idFatturaDettaglio);
                $log->log_all_errors('attivaAbbonamentoFattura -> Email Abbonamento NON Inviata [idFatturaDettaglio = ' . $idFatturaDettaglio . ']', 'ERRORE');
                return true;
            }
        } else {
            $log->log_all_errors('attivaAbbonamentoFattura -> sql_0000000001', 'ERRORE');
            return false;
        }
    } else {
        $log->log_all_errors('attivaAbbonamentoFattura -> ERRORE  iscrizioneAbbonamentoMoodle', 'ERRORE');
        return false;
    }
}

function corso_completato($course_id, $user_id) {
    global $dblink;

    $sql = "SELECT cc.id 
            FROM " . DB_NAME_MOODLE . ".mdl_course_completions cc
            WHERE
              cc.userid = '$user_id'
            AND 
              cc.course = '$course_id'
            AND 
              cc.timecompleted IS NOT NULL 
            AND 
              cc.timecompleted > 0 
            ";
    if ($dblink->num_rows($sql) > 0) {
        return true;
    }

    return false;
}

function data_inizio_corso($course_id, $user_id, $classe) {
    global $dblink;

    $enrolment_date = null;
    $sql = "SELECT DISTINCT ue.id, 
              ue.timestart AS enrolmentdate,
              r.shortname AS userrole
            FROM " . DB_NAME_MOODLE . ".mdl_user_enrolments ue
            JOIN " . DB_NAME_MOODLE . ".mdl_user u ON ue.userid = u.id
            JOIN " . DB_NAME_MOODLE . ".mdl_enrol e ON ue.enrolid = e.id
            JOIN " . DB_NAME_MOODLE . ".mdl_role_assignments ra ON ue.userid = ra.userid
            JOIN " . DB_NAME_MOODLE . ".mdl_role r ON ra.roleid = r.id
            JOIN " . DB_NAME_MOODLE . ".mdl_context co ON ra.contextid = co.id 
            JOIN " . DB_NAME_MOODLE . ".mdl_cohort ch ON e.customint5 = ch.id
            WHERE u.deleted = 0
            AND co.contextlevel = 50
            AND ch.name = '$classe' 
            AND e.courseid = '$course_id'
            AND ue.userid = '$user_id'
            ";
    $enrolment = $dblink->get_row($sql, true);

    if (isset($enrolment['enrolmentdate']) && !empty($enrolment['enrolmentdate'])) {
        $enrolment_date = $enrolment['enrolmentdate'];
    } else {

        /*
         * Se non trovo la data di inizio corso, verifico se questo utente è valido nel contesto di questo corso.
         * Allora verifica se il campo è presente nel campo Corte del profilo utente.
         * Quindi provo nuovamente ad estrarre la data di inizio del corso.
         */

        $sql = "SELECT DISTINCT ue.id, 
              ue.timestart AS enrolmentdate,
              r.shortname AS userrole
            FROM " . DB_NAME_MOODLE . ".mdl_user_enrolments ue
            JOIN " . DB_NAME_MOODLE . ".mdl_user u ON ue.userid = u.id
            JOIN " . DB_NAME_MOODLE . ".mdl_enrol e ON ue.enrolid = e.id
            JOIN " . DB_NAME_MOODLE . ".mdl_role_assignments ra ON ue.userid = ra.userid
            JOIN " . DB_NAME_MOODLE . ".mdl_role r ON ra.roleid = r.id
            JOIN " . DB_NAME_MOODLE . ".mdl_context co ON ra.contextid = co.id 
            JOIN " . DB_NAME_MOODLE . ".mdl_user_info_data uid on uid.userid = u.id 
            JOIN " . DB_NAME_MOODLE . ".mdl_user_info_field uif on uid.fieldid = uif.id 
            WHERE u.deleted = 0
            AND co.contextlevel = 50
            AND uif.name = 'Cohort'  
            AND uid.data = '$classe' 
            AND e.courseid = '$course_id' 
            AND ue.userid = '$user_id'
            ";
        $enrolment = $dblink->get_row($sql, true);

        if (isset($enrolment['enrolmentdate']) && !empty($enrolment['enrolmentdate'])) {
            $enrolment_date = $enrolment['enrolmentdate'];
        }
    }
    return $enrolment_date;
}

function giorni_rimanenti_validita_corso($enrolment_date, $months = MONTHS_INTERVAL) {
    date_default_timezone_set('Europe/Rome');
    $d1 = new \DateTime();
    $d2 = new \DateTime();
    $d2->setTimestamp($enrolment_date);
    $d2->setTime(0, 0, 0);
    $d2->add(new \DateInterval('P' . $months . 'M'));
    $d2->add(new \DateInterval('P1D'));
    $interval = date_diff($d1, $d2);
    $days = $interval->format('%a');
    return array($days, $interval->invert);
}

?>