<?php
include_once('../../config/connDB.php');
include_once(BASE_ROOT.'config/confAccesso.php');
include_once(BASE_ROOT.'classi/webservice/client.php');

$moodle = new moodleWebService();

global $dblink;

$referer = recupera_referer();

if(isset($_GET['fn'])){
    switch ($_GET['fn']) {
        case "ripristinaContatto":
            $dblink->begin();
            $ok = true;
            $idCalendario = $_GET['idCalendario'];
            
            $ok = $ok && $dblink->update("calendario",array("id_professionista"=>"0","id_azienda"=>"0"), array("id" => $idCalendario));
            
            if($ok){
                $ok = 1;
                $dblink->commit();
            }else{
                $ok = 0;
                $dblink->rollback();
            }
            
            header("Location:".$referer."&res=$ok");
        break;
        
        case "eliminaUtenteMoodle":
            $dblink->begin();
            $ok = true;
            $idProfessionista = $_GET['idProfessionista'];
            $cercaQualcosa = $_GET['cercaQualcosa'];
            
            $ok = $ok && $dblink->update("lista_password",array("stato"=>"In Attesa di Eliminazione"), array("id_professionista" => $idProfessionista));
            $ok = $ok && $dblink->update("lista_professionisti",array("stato"=>"In Attesa di Eliminazione"), array("id" => $idProfessionista));
            
            if($ok){
                $ok = 1;
                $dblink->commit();
            }else{
                $ok = 0;
                $dblink->rollback();
            }
            
            header("Location:".$referer."&res=$ok");
        break;
        
        case "resettaPassword":
            $idProfessionista = $_GET['id'];
            resetPasswordUtenteMoodle($idProfessionista,true);
            
            $sql_lista_password_manuale = "SELECT * FROM lista_password 
            WHERE livello LIKE 'cliente' AND id_professionista = '$idProfessionista'";
            $rs_lista_password_manuale = $dblink->get_results($sql_lista_password_manuale);
            foreach ($rs_lista_password_manuale AS $row_lista_password_manuale){
                //PER OGNI RIGA TRAMITE EMAIL VADO A CERCARE UTENTE IN MOODLE
                $idListaPassword = $row_lista_password_manuale['id'];
                $email = $row_lista_password_manuale['email'];

                    $stato_email = inviaEmailTemplate_Password($idListaPassword, 'inviaPassword');
                    if($stato_email){
                        echo '<li style="color: GREEN;"> OK !</li>';
                        $sql_00002 = "UPDATE lista_password 
                        SET stato = 'Attivo', 
                        dataagg = NOW(),
                        scrittore = 'autoInviaPasswordUtenti'
                        WHERE id=".$idListaPassword;
                        $rs_00002 = $dblink->query($sql_00002);
                        if($rs_00002){
                            echo '<li style="color:GREEN;">idListaPassword = '.$idListaPassword.' Aggiornata !</li>';
                            $log->log_all_errors('lista_password ->stato = ATTIVO  [idListaPassword = '.$idListaPassword.']','OK');
                        }else{
                            echo '<li style="color: RED;">idListaPassword = '.$idListaPassword.' NON Aggiornata !</li>';
                            $log->log_all_errors('lista_password ->stato = NON ATTIVO [idListaPassword = '.$idListaPassword.']','ERRORE');
                        }
                        $log->log_all_errors('stato_email ->email INVIATA correttamente [email = '.$email.']','OK');
                    }else{
                        echo '<li style="color: RED;"> KO !</li>';
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
                echo '<hr>';
                sleep(5);
            }
            header("Location:$referer&res=1");
        break;
        
        case 'inviaEmailPreventivo':

            //$idFattura = $_GET['idFatt'];
            $wMitt = $_POST['mitt'];
            $wDest = $_POST['dest'];
            $wDestCC = $_POST['dest_cc'];
            $wOgg = $_POST['ogg'];
            $wMess = $_POST['mess'];

            $wAllegato_1 = $_POST['fileDoc'];
            //$wAllegato_2 = $_POST['documentoAllegato1'];

                 if(strlen($wDest)>5 and $wOgg !='BETAFORMAZIONE - '){

                  /*  echo '<li>$wMitt = '.$wMitt.'</li>';
                    echo '<li>$wDest = '.$wDest.'</li>';
                    echo '<li>$wDestCC = '.$wDestCC.'</li>';
                    echo '<li>$wOgg = '.$wOgg.'</li>';
                    //echo '<li>$wMess = '.$wMess.'</li>';
                    echo '<li>$wAllegato_1 = '.$wAllegato_1.'</li>';
                    echo '<li>$wAllegato_2 = '.$wAllegato_2.'</li>';
                    echo '<li>$wAllegato_2 = '.$wAllegato_2.'</li>';
                    echo '<li>$idFattura = '.$idFattura.'</li>';*/
                    if(isset($_FILES['documentoAllegato1']) and strlen($_FILES["documentoAllegato1"]["name"])>3 ){
                    $testo_debug .= '<h1>UPLOAD</h1>';
                    $wAllegato_2 = $_FILES["documentoAllegato1"]["name"];
                      $testo_debug .= '<li>$nome_documentoAllegato1 = '.$nome_documentoAllegato1.'</li>';

                      /**		UPLOAD IMMAGINE		*/
                      $percorso_tabella = BASE_ROOT."media/lista_documenti/".$_SESSION['id_utente'];
                          $testo_debug .= '<li>$percorso_tabella = '.$percorso_tabella.'</li>';
                      //echo '<li>$percorso_tabella = '.$percorso_tabella.'</li>';
                      if($_FILES["documentoAllegato1"]["error"] > 0 and strlen($_FILES["documentoAllegato1"]["name"])>1){
                          $testo_debug .= "<li>Return Code: " . $_FILES["documentoAllegato1"]["error"] . "</li>";
                      }else{
                    /* 	echo "<li>Upload: " . $_FILES["file"]["name"] . "</li>";
                        echo "<li>Type: " . $_FILES["file"]["type"] . "</li>";
                        echo "<li>Size: " . ($_FILES["file"]["size"] / 1024) . " Kb</li>";
                        echo "<li>Temp file: " . $_FILES["file"]["tmp_name"] . "</li>"; */

                        //@mkdir("".$percorso_tabella."");

                        if(!mkdir($percorso_tabella, 0777, true)) {
                          //die('Failed to create folders...');
                          $testo_debug .= "Failed to create folders...";
                        }

                      if(strlen($_FILES["documentoAllegato1"]["name"])>3){
                        if (file_exists("".$percorso_tabella."/" . $_FILES["documentoAllegato1"]["name"])){
                          //echo "<li>".$_FILES["file"]["name"] . " already exists.</li>";
                      move_uploaded_file($_FILES["documentoAllegato1"]["tmp_name"],
                      "".$percorso_tabella."/" . $_FILES["documentoAllegato1"]["name"]);
                      //echo "<li>Stored in: " . "".$percorso_tabella."/" . $_FILES["file"]["name"]."</li>";
                          }else{
                          move_uploaded_file($_FILES["documentoAllegato1"]["tmp_name"],
                          "".$percorso_tabella."/" . $_FILES["documentoAllegato1"]["name"]);
                            $testo_debug .= "<li>Stored in: " . "".$percorso_tabella."/" . $_FILES["documentoAllegato1"]["name"]."</li>";
                          }
                    }

                        }
                      /**		FINE	UPLOAD IMMAGINE		*/
                    }else{
                    $testo_debug .= "<h3>NESSUN FILE RECUPERATO</h3>";
                    }

                    inviaEmailPreventivo($wMitt, $wDest, $wDestCC, $wDestBCC, $wOgg, $wMess, $wAllegato_1, $wAllegato_2, $PasswdEmailUtente);

                 }
                 header("Location:$referer&res=6");

        break;
        
        case "settaPredefinito":
            $ok = true;
            $dblink->begin();
            $row = $dblink->get_row( "SELECT * FROM $_GET[tbl] WHERE id = $_GET[id]", true);
            $ok = $ok && $dblink->update($_GET['tbl'], array("dataagg" => date("Y-m-d H:i:s"), "scrittore" => $_SESSION['cognome_nome_utente'], "tipo"=>""), array("id_azienda" => $_GET['id_azienda']));
            $ok = $ok && $dblink->update($_GET['tbl'], array("dataagg" => date("Y-m-d H:i:s"), "scrittore" => $_SESSION['cognome_nome_utente'], "tipo"=>"Predefinito"), array("id" => $_GET['id']));
            
            $update = array(
                "dataagg" => date("Y-m-d H:i:s"),
                "scrittore" => $dblink->filter($_SESSION['cognome_nome_utente'])
            );
            if($row['indirizzo']!="") $update['indirizzo'] = $row['indirizzo'];
            if($row['citta']!="") $update['citta'] = $row['citta'];
            if($row['cap']!="") $update['cap'] = $row['cap'];
            if($row['provincia']!="") $update['provincia'] = $row['provincia'];
            if($row['nazione']!="") $update['nazione'] = $row['nazione'];
            if($row['telefono']!="") $update['telefono'] = $row['telefono'];
            if($row['cellulare']!="") $update['cellulare'] = $row['cellulare'];
            if($row['fax']!="") $update['fax'] = $row['fax'];
            if($row['email']!="") $update['email'] = $row['email'];
            if($row['web']!="") $update['web'] = $row['web'];
            
            $ok = $ok && $dblink->update("lista_aziende", $update, array("id" => $_GET['id_azienda']));
            if($ok){
                $ok = 1;
                $dblink->commit();
            }else{
                $ok = 0;
                $dblink->rollback();
            }
            header("Location:".$referer."&res=$ok");
        break;
        
        case "trasferisciCommerciale":
            $ok = true;
            $id_calendario = $_POST['idCal'];
            $id_agente = $_POST['idAgenteNew'];
            $id_agente_old = $_POST['idAgenteOld'];
            
            if($id_calendario>0 && $id_agente>0 && $id_agente_old>=0){
            
                $row_0002 = $dblink->get_row("SELECT id as id_agente, CONCAT(cognome,' ', nome) as destinatario FROM lista_password WHERE id='".$id_agente_old."'", true);
                $row_0003 = $dblink->get_row("SELECT id as id_agente, CONCAT(cognome,' ', nome) as destinatario FROM lista_password WHERE id='".$id_agente."'", true);
                
                $ok = $ok && $dblink->duplicateWhere("calendario", "id='$id_calendario'", 1);
                //echo $dblink->get_query();
                //echo "<br />";
                $insetIdCalendario = $dblink->lastid();

                $updateCal = array(
                    "dataagg" => date("Y-m-d H:i:s"),
                    "scrittore" => $dblink->filter($_SESSION['cognome_nome_utente']),
                    "etichetta" => "Nuova Richiesta Trasferita",
                    "stato" => "Trasferito",
                    "messaggio" => "CONCAT('Trasferita da: <b>".$dblink->filter($row_0002['destinatario'])."</b>\\nAssegna a: <b><i>".$dblink->filter($row_0003['destinatario'])."</i></b>\\n\\nMESSAGGIO ORIGINALE:\\n', messaggio)"
                );

                $ok = $ok && $dblink->update("calendario", $updateCal, array("id"=>$insetIdCalendario));
                //echo $dblink->get_query();
                //echo "<br />";
                
                
                
                $row_0001 = $dblink->get_row("SELECT id as id_agente, CONCAT(cognome,' ', nome) as destinatario FROM lista_password WHERE id='".$id_agente."'", true);
                $row_0002 = $dblink->get_row("SELECT stato FROM calendario WHERE id='".$id_calendario."'", true);
                $row_0001["dataagg"] = date("Y-m-d H:i:s");
                $row_0001["scrittore"] = $dblink->filter($_SESSION['cognome_nome_utente']);
                if($id_agente_old<=0 && $row_0002['stato']=='In Attesa di Controllo'){
                    $row_0001["stato"]='Mai Contattato';
                }

                $ok = $ok && $dblink->update("calendario", $row_0001, array("id"=>$id_calendario));

                $ok = $ok && $dblink->update("lista_preventivi", array("dataagg" => date("Y-m-d H:i:s"), "scrittore" => $dblink->filter($_SESSION['cognome_nome_utente']), "id_agente"=>$id_agente, "cognome_nome_agente"=>$row_0001['destinatario']), array("id_calendario"=>$id_calendario));
                //$ok = $ok && $dblink->update("lista_preventivi", array("dataagg" => date("Y-m-d H:i:s"), "scrittore" => $dblink->filter($_SESSION['cognome_nome_utente']), "id_calendario"=>$insetIdCalendario, "id_agente"=>$id_agente), array("id_calendario"=>$id_calendario));
                //$ok = $ok && $dblink->update("lista_preventivi_dettaglio", array("dataagg" => date("Y-m-d H:i:s"), "scrittore" => $dblink->filter($_SESSION['cognome_nome_utente']), "id_calendario"=>$insetIdCalendario), array("id_calendario"=>$id_calendario));
                //echo $dblink->get_query();
                //echo "<br />";
                if($ok) echo "OK:$id_calendario";
                else echo "KO";
            }else{
                echo "KO";
            }
            
        break;
        
        case "prendiInCaricoRichiesta":
            $ok = true;
            $id_calendario = $_GET['idCal'];
            $id_agente = $_GET['idAgenteNew'];
            $id_agente_old = $_GET['idAgenteOld'];
            
            if($id_calendario>0 && $id_agente>0 && $id_agente_old>0){
            
                $row_0002 = $dblink->get_row("SELECT id as id_agente, CONCAT(cognome,' ', nome) as destinatario FROM lista_password WHERE id='".$id_agente_old."'", true);
                $row_0003 = $dblink->get_row("SELECT id as id_agente, CONCAT(cognome,' ', nome) as destinatario FROM lista_password WHERE id='".$id_agente."'", true);
                
                $ok = $ok && $dblink->duplicateWhere("calendario", "id='$id_calendario'", 1);
                //echo $dblink->get_query();
                //echo "<br />";
                $insetIdCalendario = $dblink->lastid();

                $updateCal = array(
                    "dataagg" => date("Y-m-d H:i:s"),
                    "scrittore" => $dblink->filter($_SESSION['cognome_nome_utente']),
                    "etichetta" => "Nuova Richiesta Trasferita",
                    "stato" => "Trasferito",
                    "messaggio" => "CONCAT('Presa in carico da: <b>".$dblink->filter($row_0003['destinatario'])."</b>\\nAgente precedente: <b><i>".$dblink->filter($row_0002['destinatario'])."</i></b>\\n\\nMESSAGGIO ORIGINALE:\\n', messaggio)"
                );

                $ok = $ok && $dblink->update("calendario", $updateCal, array("id"=>$insetIdCalendario));
                //echo $dblink->get_query();
                //echo "<br />";
                
                $row_0001 = $dblink->get_row("SELECT id as id_agente, CONCAT(cognome,' ', nome) as destinatario FROM lista_password WHERE id='".$id_agente."'", true);
                $row_0001["dataagg"] = date("Y-m-d H:i:s");
                $row_0001["scrittore"] = $dblink->filter($_SESSION['cognome_nome_utente']);

                $ok = $ok && $dblink->update("calendario", $row_0001, array("id"=>$id_calendario));

                $ok = $ok && $dblink->update("lista_preventivi", array("dataagg" => date("Y-m-d H:i:s"), "scrittore" => $dblink->filter($_SESSION['cognome_nome_utente']), "id_agente"=>$id_agente, "cognome_nome_agente"=>$row_0001['destinatario']), array("id_calendario"=>$id_calendario));
                //$ok = $ok && $dblink->update("lista_preventivi", array("dataagg" => date("Y-m-d H:i:s"), "scrittore" => $dblink->filter($_SESSION['cognome_nome_utente']), "id_calendario"=>$insetIdCalendario, "id_agente"=>$id_agente), array("id_calendario"=>$id_calendario));
                //$ok = $ok && $dblink->update("lista_preventivi_dettaglio", array("dataagg" => date("Y-m-d H:i:s"), "scrittore" => $dblink->filter($_SESSION['cognome_nome_utente']), "id_calendario"=>$insetIdCalendario), array("id_calendario"=>$id_calendario));
                //echo $dblink->get_query();
                //echo "<br />";
                if($ok) echo "OK:$id_calendario";
                else echo "KO";
            }elseif($id_calendario>0 && $id_agente>0) {
                $row_0001 = $dblink->get_row("SELECT id as id_agente, CONCAT(cognome,' ', nome) as destinatario FROM lista_password WHERE id='".$id_agente."'", true);
                $row_0001["dataagg"] = date("Y-m-d H:i:s");
                $row_0001["scrittore"] = $dblink->filter($_SESSION['cognome_nome_utente']);

                $ok = $ok && $dblink->update("calendario", $row_0001, array("id"=>$id_calendario));
                
                if($dblink->num_rows("SELECT * FROM lista_preventivi WHERE id_calendario = '$id_calendario'")){
                    $ok = $ok && $dblink->update("lista_preventivi", array("dataagg" => date("Y-m-d H:i:s"), "scrittore" => $dblink->filter($_SESSION['cognome_nome_utente']), "id_agente"=>$id_agente, "cognome_nome_agente"=>$row_0001['destinatario']), array("id_calendario"=>$id_calendario));
                }
                
                if($ok) echo "OK:$id_calendario";
                else echo "KO";
            }else{
                echo "KO";
            }
            
        break;
        
        case 'inserisciIndirizzo':
            $ok = true;
            $dblink->begin();
            $id_azienda = $_GET['id'];
            $sql_0001 = "INSERT INTO `lista_indirizzi` (`id`, `dataagg`, `scrittore`, `id_azienda`, `stato`) 
            SELECT '', NOW(), '".$dblink->filter($_SESSION['cognome_nome_utente'])."', '".$id_azienda."', 'Attivo'
            FROM lista_aziende WHERE id =".$id_azienda;
            $ok = $ok && $dblink->query($sql_0001);
            $id_indirizzo_nuovo = $dblink->insert_id();
            if($ok){
                $ok = 1;
                $dblink->commit();
            }else{
                $ok = 0;
                $dblink->rollback();
            }
            header("Location:".BASE_URL."/moduli/base/modifica.php?tbl=lista_indirizzi&id=$id_indirizzo_nuovo&res=$ok");
        break;
        
        case 'inserisciProfessionista':
            $ok = true;
            $dblink->begin();
            $id_azienda = $_GET['id'];
            $sql_0001 = "INSERT INTO lista_professionisti (dataagg, scrittore, stato, cognome, nome) SELECT NOW(), '".$dblink->filter($_SESSION['cognome_nome_utente'])."', 'Attivo', ragione_sociale, ragione_sociale 
            FROM lista_aziende WHERE id =".$id_azienda;
            $ok = $ok && $dblink->query($sql_0001);
            $id_professionista_nuovo = $dblink->insert_id();
            $sql_0002 = "INSERT INTO matrice_aziende_professionisti (id_azienda, id_professionista) VALUES ($id_azienda, $id_professionista_nuovo)";
            $ok = $ok && $dblink->query($sql_0002);
            if($ok){
                $ok = 1;
                $dblink->commit();
            }else{
                $ok = 0;
                $dblink->rollback();
            }
            header("Location:".BASE_URL."/moduli/anagrafiche/modifica.php?tbl=lista_professionisti&id=$id_professionista_nuovo&res=$ok");
        break;
        
        case 'NuovaNotaProfessionista':
            $ok = true;
            $dblink->begin();
            $id_professionista = $_GET['id'];
            $sql_0001 = "INSERT INTO calendario (dataagg, data, ora, datainsert, orainsert, etichetta, scrittore, mittente, destinatario, id_professionista) SELECT NOW(), NOW(), NOW(), NOW(), NOW(), 'Nota', '".$dblink->filter($_SESSION['cognome_nome_utente'])."','".$dblink->filter($_SESSION['cognome_nome_utente'])."','".$dblink->filter($_SESSION['cognome_nome_utente'])."', '$id_professionista'
            FROM lista_professionisti WHERE id =".$id_professionista;
            $ok = $ok && $dblink->query($sql_0001);
            if($ok){
                $ok = 1;
                $dblink->commit();
            }else{
                $ok = 0;
                $dblink->rollback();
            }
            header("Location:".$referer."&res=$ok");
        break;
        
        case 'NuovoDettaglioOrdineProfessionista':
            $ok = true;
            $dblink->begin();
            $id_calendario = $_GET['idCalendario'];
            $id_preventivo = $_GET['idPrev'];
            $insertId = 0;
            
            $where = "id_calendario = $id_calendario";
            
            $ok = $ok && $dblink->duplicateWhere("lista_preventivi_dettaglio", $where, 1, "id DESC");
            $insertId = $dblink->lastid();
            if($insertId > 0){
                $update= array( 
                                "nome_prodotto"=>"",
                                "descrizione_breve_prodotto"=>"",
                                "codice_prodotto"=>"",
                                "barcode_prodotto"=>"",
                                "id_prodotto_0"=>"0",
                                "id_prodotto"=>"0",
                                "prezzo_prodotto"=>"0");

                $ok = $ok && $dblink->update("lista_preventivi_dettaglio", $update, array("id"=>$insertId), 1);
            }else{
                $sql_0001 = "INSERT INTO lista_preventivi_dettaglio (dataagg, id_preventivo, id_prodotto, quantita, id_campagna, id_calendario, scrittore, stato, id_professionista, id_sezionale, sezionale)
                            SELECT NOW(), '".$id_preventivo."',  id_prodotto, '1', id_campagna, id, '".addslashes($_SESSION['cognome_nome_utente'])."', 'In Attesa' , `id_professionista`, (SELECT IF(id_sezionale = 8, id_sezionale, 3) FROM lista_campagne WHERE id = id_campagna), (SELECT IF(id_sezionale = 8, 'FREE', '00') FROM lista_campagne WHERE id = id_campagna)
                            FROM calendario WHERE id=".$id_calendario." AND calendario.etichetta='Nuova Richiesta'";
                $ok = $ok && $dblink->query($sql_0001);
            }
            //echo $dblink->get_query();
            
            if($ok){
                $ok = 1;
                $dblink->commit();
            }else{
                $ok = 0;
                $dblink->rollback();
            }
            header("Location:".$referer."&res=$ok#tab_prof");
        break;
        
        /*case 'NuovoOrdineProfessionista':
            $ok = true;
            $dblink->begin();
            $id_professionista = $_GET['id'];
            
            $sql_0001 = "INSERT INTO lista_preventivi_dettaglio (dataagg, scrittore, id_professionista) SELECT NOW(), '".$dblink->filter($_SESSION['cognome_nome_utente'])."', '$id_professionista'
            FROM lista_professionisti WHERE id =".$id_professionista;
            $ok = $ok && $dblink->query($sql_0001);
            if($ok){
                $ok = 1;
                $dblink->commit();
            }else{
                $ok = 0;
                $dblink->rollback();
            }
            header("Location:".$referer."&res=$ok#tab_prof");
        break;*/
        
        case "preventivoVenduto":
            $ok = true;
            $dblink->begin();
            $id_calendario = $_GET['idCalendario'];
            $id_preventivo = $_GET['idPreventivo'];
            
            $rowCalendario = $dblink->get_row("SELECT id_professionista, id_azienda, id_agente, id_campagna FROM calendario WHERE id='$id_calendario'", true);
            
            list($idSezionale, $sezionale) = $dblink->get_row("SELECT id, nome FROM lista_fatture_sezionali WHERE id IN (SELECT id_sezionale FROM lista_campagne WHERE id = '".$rowCalendario['id_campagna']."' )");
            
            if($idSezionale == 8){
                //do nothing
            }else{
                $idSezionale = 3;
                $sezionale = "00";
            }
            
            $updateCalendario = array(
                "dataagg" => date("Y-m-d H:i:s"),
                "scrittore" => $dblink->filter($_SESSION['cognome_nome_utente']),
                "stato" => "Venduto",
            );
            
            $ok = $ok && $dblink->update("calendario", $updateCalendario, array("id"=>$id_calendario));
            
            $updatePreventivo = array(
                "dataagg" => date("Y-m-d H:i:s"),
                "data_iscrizione" => date("Y-m-d"),
                "scrittore" => $dblink->filter($_SESSION['cognome_nome_utente']),
                "stato" => "Venduto",
                "id_azienda" => $rowCalendario['id_azienda'],
                "id_professionista" => $rowCalendario['id_professionista'],
                "id_calendario" => $id_calendario,
                "id_agente" => $rowCalendario['id_agente'],
                "cognome_nome_agente" => getNomeAgente($rowCalendario['id_agente']),
                "id_sezionale" => $idSezionale,
                "sezionale" => $sezionale
            );
            
            $ok = $ok && $dblink->update("lista_preventivi", $updatePreventivo, array("id"=>$id_preventivo));
            
            $updatePreventivoDettaglio = array(
                "dataagg" => date("Y-m-d H:i:s"),
                "scrittore" => $dblink->filter($_SESSION['cognome_nome_utente']),
                "stato" => "Venduto",
                "id_azienda" => $rowCalendario['id_azienda'],
                "id_professionista" => $rowCalendario['id_professionista'],
                "id_calendario" => $id_calendario,
                "id_sezionale" => $idSezionale,
                "sezionale" => $sezionale
            );
            
            $ok = $ok && $dblink->update("lista_preventivi_dettaglio", $updatePreventivoDettaglio, array("id_preventivo"=>$id_preventivo));
            
            $sql_000001 = "INSERT INTO calendario (`id`, `dataagg`, `scrittore`, `datainsert`, `orainsert`, id_agente, id_campagna, id_contatto, id_professionista, id_azienda, id_preventivo, id_commessa, `data`, `ora`, `etichetta`, `oggetto`, `messaggio`, `mittente`, `destinatario`, `priorita`, `stato`) 
            SELECT '', NOW(), '".$dblink->filter($_SESSION['cognome_nome_utente'])."', NOW(), NOW(), id_agente, id_campagna, id_contatto, id_professionista, id_azienda, '".$id_preventivo."', '', CURDATE(), TIME(NOW()), 'Ordini', CONCAT('Ordine n ', id ,''), CONCAT('Ordine n ', id ,': Venduto il ',NOW(),''), '".$dblink->filter($_SESSION['cognome_nome_utente'])."', '', 'Normale', 'Fatto' FROM lista_preventivi WHERE id='".$id_preventivo."'";
            $ok = $ok && $dblink->query($sql_000001);
            
            if($ok){
                $ok = 1;
                $dblink->commit();
            }else{
                $ok = 0;
                $dblink->rollback();
            }
            
            if(empty($referer)){
                header("Location:".$_SESSION['NOSTRO_HTTP_REFERER']);
            }else{
                header("Location:".$referer."&res=$ok");
            }
        break;
    
        case "preventivoNegativo":
            $ok = true;
            $dblink->begin();
            $id_calendario = $_POST['idCalendario'];
            $id_preventivo = $_POST['idPreventivo'];
            $id_obiezione = $_POST['idObiezione'];
            
            $nomeObiezione = $dblink->get_field("SELECT nome FROM lista_obiezioni WHERE id = '$id_obiezione'");
            
            $rowCalendario = $dblink->get_row("SELECT id_professionista, id_azienda, id_agente, id_campagna FROM calendario WHERE id='$id_calendario'", true);
            
            list($idSezionale, $sezionale) = $dblink->get_row("SELECT id, nome FROM lista_fatture_sezionali WHERE id IN (SELECT id_sezionale FROM lista_campagne WHERE id = '".$rowCalendario['id_campagna']."' )");
            
            if($idSezionale == 8){
                //do nothing
            }else{
                $idSezionale = 3;
                $sezionale = "00";
            }
            
            $updateCalendario = array(
                "dataagg" => date("Y-m-d H:i:s"),
                "scrittore" => $dblink->filter($_SESSION['cognome_nome_utente']),
                "stato" => "Negativo",
                "id_obiezione" => "$id_obiezione",
                "nome_obiezione" => $dblink->filter($nomeObiezione)
            );
            
            $ok = $ok && $dblink->update("calendario", $updateCalendario, array("id"=>$id_calendario));
            
            $updatePreventivo = array(
                "dataagg" => date("Y-m-d H:i:s"),
                "data_iscrizione" => date("Y-m-d"),
                "data_firma" => date("Y-m-d"),
                "scrittore" => $dblink->filter($_SESSION['cognome_nome_utente']),
                "stato" => "Negativo",
                "id_azienda" => $rowCalendario['id_azienda'],
                "id_professionista" => $rowCalendario['id_professionista'],
                "id_calendario" => $id_calendario,
                "id_agente" => $rowCalendario['id_agente'],
                "cognome_nome_agente" => getNomeAgente($rowCalendario['id_agente']),
                "id_sezionale" => $idSezionale,
                "sezionale" => $sezionale,
                "id_obiezione" => "$id_obiezione",
                "nome_obiezione" => $dblink->filter($nomeObiezione)
            );
            
            $ok = $ok && $dblink->update("lista_preventivi", $updatePreventivo, array("id"=>$id_preventivo));
            
            $updatePreventivoDettaglio = array(
                "dataagg" => date("Y-m-d H:i:s"),
                "scrittore" => $dblink->filter($_SESSION['cognome_nome_utente']),
                "stato" => "Negativo",
                "id_azienda" => $rowCalendario['id_azienda'],
                "id_professionista" => $rowCalendario['id_professionista'],
                "id_calendario" => $id_calendario,
                "id_sezionale" => $idSezionale,
                "sezionale" => $sezionale
            );
            
            $ok = $ok && $dblink->update("lista_preventivi_dettaglio", $updatePreventivoDettaglio, array("id_preventivo"=>$id_preventivo));
            
            $sql_000001 = "INSERT INTO calendario (`id`, `dataagg`, `scrittore`, `datainsert`, `orainsert`, id_agente, id_campagna, id_contatto, id_professionista, id_azienda, id_preventivo, id_commessa, `data`, `ora`, `etichetta`, `oggetto`, `messaggio`, `mittente`, `destinatario`, `priorita`, `stato`, `id_obiezione`, `nome_obiezione`) 
            SELECT '', NOW(), '".$dblink->filter($_SESSION['cognome_nome_utente'])."', NOW(), NOW(), id_agente, id_campagna, id_contatto, id_professionista, id_azienda, '".$id_preventivo."', '', CURDATE(), TIME(NOW()), 'Ordini', CONCAT('Ordine n ', id ,''), CONCAT('Ordine n ', id ,': Negativo il ',NOW(),''), '".$dblink->filter($_SESSION['cognome_nome_utente'])."', '', 'Normale', 'Fatto', id_obiezione, nome_obiezione FROM lista_preventivi WHERE id='".$id_preventivo."'";
            $ok = $ok && $dblink->query($sql_000001);
            
            if($ok){
                $ok = 1;
                $dblink->commit();
            }else{
                $ok = 0;
                $dblink->rollback();
            }
            
            header("Location:".$referer."&res=$ok");
            
        break;
        
        case 'CercaSalvaProfessionista':
            
            $ok = false;
            $richiesta_codice = $_POST['codice_utente'];

            if(strlen($_POST['codice_utente'])==16){
                $richiesta_codice_fiscale = $_POST['codice_utente'];
            } else { 
                $richiesta_codice_utente = $_POST['codice_utente'];
            }
            $richiesta_id_calendario = $_POST['id_calendario'];
            
            if(strlen($richiesta_codice_fiscale)>5 or strlen($richiesta_codice_utente)>5){
                // , nome, cognome, codice_fiscale, data_di_nascita, luogo_di_nascita, provincia_di_nascita, professione, tipo_albo, provincia_albo, numero_albo, cellulare, telefono, fax, web, email
                $sql_00001 = "SELECT id FROM lista_professionisti WHERE codice_fiscale='$richiesta_codice' OR codice = '$richiesta_codice'";
                $row_00001 = $dblink->get_row($sql_00001,true);
                if($row_00001['id']>0){
                    $id_professionista = $row_00001['id'];
                    //ragione_sociale, forma_giuridica, partita_iva, codice_fiscale, indirizzo, cap, citta, provincia, nazione, telefono, cellulare, web, email, settore, categoria
                    $sql_00002 = "SELECT id_azienda FROM `matrice_aziende_professionisti` WHERE id_professionista='".$id_professionista."'";
                    $row_00002 = $dblink->get_row($sql_00002, true);

                    if($row_00002['id_azienda']>0){
                        $id_azienda = $row_00002['id_azienda'];
                    } else { $id_azienda = 0; }

                    $sql_00003 = "UPDATE calendario "
                            . "SET id_professionista='".$id_professionista."',"
                            . "id_azienda = '".$id_azienda."',"
                            . "dataagg = NOW(),"
                            . "scrittore = '".$dblink->filter($_SESSION['cognome_nome_utente'])."',"
                            . "campo_3 = '".$richiesta_codice_fiscale."',"
                            . "campo_9 = '".$richiesta_codice_utente."' "
                            . "WHERE id_professionista<=0 "
                            . "AND id=".$richiesta_id_calendario;
                    $ok = $dblink->query($sql_00003);
                    
                    if($dblink->num_rows("SELECT * FROM lista_preventivi WHERE id_calendario = '$richiesta_id_calendario' AND id_calendario > 0")){
                        $ok = $ok && $dblink->update("lista_preventivi", array("dataagg" => date("Y-m-d H:i:s"), "scrittore" => $dblink->filter($_SESSION['cognome_nome_utente']), "id_professionista" => $id_professionista, "cognome_nome_professionista"=>$dblink->filter(getNomeProfessionista($id_professionista)), "id_azienda" => $id_azienda, "ragione_sociale_azienda"=>$dblink->filter(getNomeAzienda($id_azienda))), array("id_calendario"=>$richiesta_id_calendario));
                    }
                }
            }
            
            if($ok) $ok="OK:OK";
            else $ok="KO:$richiesta_codice_fiscale";
            
            //$prefix = "";
            
            echo "$ok";
            
        break;
        
        case 'aggiungiAzienda':
            
            $ok = true;
            $continue = true;
            
            $partita_iva = $_POST['partita_iva'];
            $partita_iva_nuova = $_POST['cerca_azienda'];
            
            if(strlen($partita_iva_nuova)==16){
                $codice_fiscale = $partita_iva_nuova;
                if(!preg_match('/^[a-z]{6}[0-9]{2}[a-z][0-9]{2}[a-z][0-9]{3}[a-z]{1}$/i', trim($codice_fiscale))) {
                //if(!preg_match("^[A-Z]{6}[0-9]{2}[A-Z][0-9]{2}[A-Z][0-9]{3}[A-Z]{1}$", $codice_fiscale)){
                    $ok = "KO2:KO2";
                    $continue = false;
                }
                $where = "";
                $whereAdd = "";

                $richiesta_id_professionista = $_POST['id_professionista'];
                $richiesta_id_calendario = $_POST['id_calendario'];
                $richiesta_id_azienda = $_POST['id_azienda'];

                if($richiesta_id_azienda>0){
                    $where = "id='$richiesta_id_azienda'";
                }else{
                    $where = "partita_iva LIKE '$codice_fiscale'";
                }
            }else{
                if(strlen($partita_iva)<5){
                    if(!preg_match('/^[0-9]{8,11}$/i', trim($partita_iva_nuova))){
                        $ok = "KO2:KO2";
                        $continue = false;
                    }
                }

                $richiesta_id_professionista = $_POST['id_professionista'];
                $richiesta_id_calendario = $_POST['id_calendario'];
                $richiesta_id_azienda = $_POST['id_azienda'];

                if($richiesta_id_azienda>0 && strlen($partita_iva)>5){
                    $where = "id='$richiesta_id_azienda'";
                }else{
                    $where = "partita_iva='$partita_iva_nuova'";
                }
            }
            if((strlen($partita_iva_nuova)>5 || $richiesta_id_azienda>0) && $continue){
                // , nome, cognome, codice_fiscale, data_di_nascita, luogo_di_nascita, provincia_di_nascita, professione, tipo_albo, provincia_albo, numero_albo, cellulare, telefono, fax, web, email
                $sql_00001 = "SELECT id FROM lista_aziende WHERE $where";
                $row_00001 = $dblink->get_row($sql_00001,true);
                if($row_00001['id']>0){
                    $id_azienda = $row_00001['id'];
                    
                    $id_professionista = $row_00001['id'];
                    //ragione_sociale, forma_giuridica, partita_iva, codice_fiscale, indirizzo, cap, citta, provincia, nazione, telefono, cellulare, web, email, settore, categoria
                    $sql_00002 = "SELECT id_azienda FROM `matrice_aziende_professionisti` WHERE id_professionista='".$richiesta_id_professionista."' AND id_azienda='$id_azienda'";
                    $row_00002 = $dblink->get_row($sql_00002, true);

                    $ok = $ok && $dblink->update("matrice_aziende_professionisti",array("dataagg" => date("Y-m-d H:i:s"), "scrittore"=>$dblink->filter($_SESSION['cognome_nome_utente']), "id_scrittore"=>$_SESSION['id_utente'], "stato"=>"Non Attivo"), array("id_professionista"=>$richiesta_id_professionista));
                    
                    if($row_00002['id_azienda']>0){
                        $ok = $ok && $dblink->update("matrice_aziende_professionisti",array("dataagg" => date("Y-m-d H:i:s"), "scrittore"=>$dblink->filter($_SESSION['cognome_nome_utente']), "id_scrittore"=>$_SESSION['id_utente'], "stato"=>"Attivo"), array("id_professionista"=>$richiesta_id_professionista, "id_azienda"=>$id_azienda));
                        //$sql2 = $dblink->get_query();
                    }else{
                        $ok = $ok && $dblink->insert("matrice_aziende_professionisti",array("id_azienda"=>$id_azienda, "id_professionista"=>$richiesta_id_professionista, "dataagg" => date("Y-m-d H:i:s"), "scrittore"=>$dblink->filter($_SESSION['cognome_nome_utente']), "id_scrittore"=>$_SESSION['id_utente'], "stato"=>"Attivo"));
                    }

                    $sql_00003 = "UPDATE calendario "
                            . "SET id_azienda = '".$id_azienda."',"
                            . "dataagg = NOW(),"
                            . "scrittore = '".$dblink->filter($_SESSION['cognome_nome_utente'])."' "
                            . "WHERE id='".$richiesta_id_calendario."'";
                    $ok = $ok && $dblink->query($sql_00003);
                    //$sql = $dblink->get_query();
                    
                    if($dblink->num_rows("SELECT * FROM lista_preventivi WHERE id_calendario = '$richiesta_id_calendario' AND id_calendario > 0")){
                        $ok = $ok && $dblink->update("lista_preventivi", array("dataagg" => date("Y-m-d H:i:s"), "scrittore" => $dblink->filter($_SESSION['cognome_nome_utente']), "id_azienda" => $id_azienda, "ragione_sociale_azienda"=>$dblink->filter(getNomeAzienda($id_azienda))), array("id_calendario"=>$richiesta_id_calendario));
                    }
                    
                }else{
                    $ok = $ok && $dblink->insert("lista_aziende",array("dataagg" => date("Y-m-d H:i:s"), "scrittore"=>$dblink->filter($_SESSION['cognome_nome_utente']), "partita_iva"=>$partita_iva_nuova, "stato"=>"Attivo"));
                    $id_azienda = $dblink->lastid();
                    
                    $ok = $ok && $dblink->update("matrice_aziende_professionisti",array("dataagg" => date("Y-m-d H:i:s"), "scrittore"=>$dblink->filter($_SESSION['cognome_nome_utente']), "id_scrittore"=>$_SESSION['id_utente'], "stato"=>"Non Attivo"), array("id_professionista"=>$richiesta_id_professionista));
                    $ok = $ok && $dblink->insert("matrice_aziende_professionisti",array("id_azienda"=>$id_azienda, "id_professionista"=>$richiesta_id_professionista, "dataagg" => date("Y-m-d H:i:s"), "scrittore"=>$dblink->filter($_SESSION['cognome_nome_utente']), "id_scrittore"=>$_SESSION['id_utente'], "stato"=>"Attivo"));
                            
                    $sql_00004 = "UPDATE calendario "
                            . "SET id_azienda = '".$id_azienda."',"
                            . "dataagg = NOW(),"
                            . "scrittore = '".$dblink->filter($_SESSION['cognome_nome_utente'])."' "
                            . "WHERE id='".$richiesta_id_calendario."'";
                    
                    $ok = $ok && $dblink->query($sql_00004);
                    
                    if($dblink->num_rows("SELECT * FROM lista_preventivi WHERE id_calendario = '$richiesta_id_calendario' AND id_calendario > 0")){
                        $ok = $ok && $dblink->update("lista_preventivi", array("dataagg" => date("Y-m-d H:i:s"), "scrittore" => $dblink->filter($_SESSION['cognome_nome_utente']), "id_azienda" => $id_azienda, "ragione_sociale_azienda"=>$dblink->filter(getNomeAzienda($id_azienda))), array("id_calendario"=>$richiesta_id_calendario));
                    }
                    
                    if($ok) $ok = "OK2:OK2";
                }
            }
            
            if($ok===true) $ok="OK:OK";
            elseif($ok===false) $ok="KO:KO";
            
            echo "$ok";
            
        break;
        
        case 'cercaAzienda':
            $ok = true;
            
            if(strpos($_GET['key_search']," ")){
                $key=explode(" ", $_GET['key_search']);
            }else{
                $key=$_GET['key_search'];
            }
            $array = array();
            $sql_01 = "SELECT id, ragione_sociale, partita_iva, telefono FROM lista_aziende WHERE 1 ";
            if(is_array($key)){
                $where = "";
                foreach ($key as $value) {
                    $value = $dblink->filter($value);
                    $where.= "AND (partita_iva LIKE '%{$value}%' OR codice_fiscale LIKE '%{$value}%' OR ragione_sociale LIKE '%{$value}%' OR email LIKE '%{$value}%' OR telefono LIKE '%{$value}%')";
                }
            }else{
                $key = $dblink->filter($key);
                $where = "AND (partita_iva LIKE '%{$key}%' OR codice_fiscale LIKE '%{$key}%' OR ragione_sociale LIKE '%{$key}%' OR email LIKE '%{$key}%' OR telefono LIKE '%{$key}%')";
            }
            
            $result = $dblink->get_results($sql_01.$where);
            foreach ($result as $row) {
                $array[] = array(
                    "id_azienda" => $row['id'],
                    "ragione_sociale" => $row['ragione_sociale']." - P.IVA: ".$row['partita_iva']." - Tel: ".$row['telefono'],
                    "partita_iva" => $row['partita_iva']
                    );
            }
            echo json_encode($array);
        break;
        
        case 'aggiungiProfessionista':
            $ok = true;
            $continue = true;
            $codice_fiscale = strlen($_POST['codice_fiscale'])>5 ? $_POST['codice_fiscale'] : $_POST['cerca_professionista'];
            
            $where = "";
            $whereAdd = "";
            
            $richiesta_id_professionista = $_POST['id_professionista'];
            $richiesta_id_calendario = $_POST['id_calendario'];
            $richiesta_id_azienda = $_POST['id_azienda'];
            
            if($richiesta_id_professionista>0 && strlen($codice_fiscale)!=16){
                $where = "id='$richiesta_id_professionista'";
            }else{
                if(!preg_match('/^[a-z]{6}[0-9]{2}[a-z][0-9]{2}[a-z][0-9]{3}[a-z]{1}$/i', trim($codice_fiscale))) {
                    $ok = "KO2:KO2";
                    $continue = false;
                }
                
                if(!controlloCodiceFiscale(trim($codice_fiscale))){
                    $ok = "KO2:KO2";
                    $continue = false;
                }
                $where = "codice_fiscale LIKE '$codice_fiscale'";
            }
            if((strlen($codice_fiscale)==16 || $richiesta_id_professionista>0) && $continue){
                // , nome, cognome, codice_fiscale, data_di_nascita, luogo_di_nascita, provincia_di_nascita, professione, tipo_albo, provincia_albo, numero_albo, cellulare, telefono, fax, web, email
                $sql_00001 = "SELECT id FROM lista_professionisti WHERE $where";
                $row_00001 = $dblink->get_row($sql_00001,true);
                if($row_00001['id']>0){
                    if($richiesta_id_azienda>0){
                        $whereAdd = "AND id_azienda='$richiesta_id_azienda'";
                    }
                    
                    $id_professionista = $row_00001['id'];
                    
                    $update = array(
                        "id_professionista" => $id_professionista,
                        "dataagg" => date("Y-m-d H:i:s"),
                        "scrittore" => $dblink->filter($_SESSION['cognome_nome_utente']),
                    );
                    
                    $where = array(
                        //"id_professionista" => 0,
                        "id" => $richiesta_id_calendario
                    );
                    
                    //ragione_sociale, forma_giuridica, partita_iva, codice_fiscale, indirizzo, cap, citta, provincia, nazione, telefono, cellulare, web, email, settore, categoria
                    $sql_00002 = "SELECT id_azienda FROM `matrice_aziende_professionisti` WHERE id_professionista='".$id_professionista."' $whereAdd AND stato='Attivo'";
                    $row_00002 = $dblink->get_row($sql_00002, true);
                    
                    if($row_00002['id_azienda']>0){
                        $update['id_azienda'] = $row_00002['id_azienda'];
                    }
                    
                    $ok = $ok && $dblink->update("calendario", $update, $where);
                    
                    if($dblink->num_rows("SELECT * FROM lista_preventivi WHERE id_calendario = '$richiesta_id_calendario' AND id_calendario > 0")){
                        $ok = $ok && $dblink->update("lista_preventivi", array("dataagg" => date("Y-m-d H:i:s"), "scrittore" => $dblink->filter($_SESSION['cognome_nome_utente']), "id_professionista" => $id_professionista, "cognome_nome_professionista"=>$dblink->filter(getNomeProfessionista($id_professionista))), array("id_calendario"=>$richiesta_id_calendario));
                    }
                    
                    if($ok===true) $ok="OK:OK";
                    
                }else{
                    $sql_00003 = "SELECT * FROM `calendario` WHERE id='".$richiesta_id_calendario."' AND etichetta LIKE 'Nuova Richiesta'";
                    $row_00003 = $dblink->get_row($sql_00003, true);
                    
                    $insert= array(
                        "dataagg" => date("Y-m-d H:i:s"),
                        "scrittore"=>$dblink->filter($_SESSION['cognome_nome_utente']),
                        "codice_fiscale"=>$codice_fiscale,
                        "nome"=>$row_00003['nome'],
                        "cognome"=>$row_00003['cognome'],
                        "data_di_nascita"=>$row_00003['data_di_nascita'],
                        "luogo_di_nascita"=>$row_00003['luogo_di_nascita'],
                        "provincia_di_nascita"=>$row_00003['provincia_di_nascita'],
                        "professione"=>$row_00003['professione'],
                        "id_classe"=>$row_00003['id_classe'],
                        "provincia_albo"=>$row_00003['provincia_albo'],
                        "numero_albo"=>$row_00003['numero_albo'],
                        "cellulare"=>$row_00003['cellulare'],
                        "telefono"=>$row_00003['telefono'],
                        "fax"=>$row_00003['fax'],
                        "web"=>$row_00003['web'],
                        "email"=>$row_00003['email'],
                        "stato" => "Attivo"
                    );
                    
                    $ok = $ok && $dblink->insert("lista_professionisti",$insert);
                    $id_professionista = $dblink->lastid();
                    
                    $sql_0006 = "UPDATE lista_professionisti
                    SET codice = CONCAT('".SUFFISSO_CODICE_CLIENTE."',RIGHT(concat('0000000000',id),6)) 
                    WHERE id = '$id_professionista'";
                    
                    $ok = $ok && $dblink->query($sql_0006);
                    
                    $updatePreventivo = array(
                        "id_professionista" => $id_professionista,
                        "dataagg" => date("Y-m-d H:i:s"),
                        "scrittore" => $dblink->filter($_SESSION['cognome_nome_utente']),
                        "cognome_nome_professionista" => $dblink->filter(getNomeProfessionista($id_professionista))
                    );
                    
                    $ok = $ok && $dblink->update("lista_preventivi", $updatePreventivo, array("id_calendario"=>$richiesta_id_calendario));
                    
                    $updateCalendario = array(
                        "id_professionista" => $id_professionista,
                        "dataagg" => date("Y-m-d H:i:s"),
                        "scrittore" => $dblink->filter($_SESSION['cognome_nome_utente'])
                    );
                    
                    $whereCalendario = array(
                        "id_professionista" => 0,
                        "id" => $richiesta_id_calendario
                    );
                    
                    $ok = $ok && $dblink->update("calendario", $updateCalendario, $whereCalendario);
                    if($ok===true) $ok = "OK2:OK2";
                }
            }
            
            if($ok===false) $ok="KO:KO";
            
            echo "$ok";
            
        break;
        
        case 'cambiaProfessionistaIscrizione':
            $ok = true;
            $dblink->begin();
            
            $richiesta_id_professionista = $_POST['id_professionista'];
            $richiesta_idIscrizioni = $_POST['idIscrizioni'];
            $richiesta_id_azienda = $_POST['id_azienda'];
            
            $arrayIscrizioniId = explode(":",$richiesta_idIscrizioni);
            
            if(is_array($arrayIscrizioniId)){
                foreach ($arrayIscrizioniId as $idIscrizione) {
                    $updateIscrizione = array(
                        "id_professionista" => $richiesta_id_professionista,
                        "dataagg" => date("Y-m-d H:i:s"),
                        "scrittore" => $dblink->filter($_SESSION['cognome_nome_utente'])
                    );
                    
                    $whereIscrizione = array(
                        "id" => $idIscrizione
                    );
                    $ok = $ok && $dblink->update("lista_iscrizioni", $updateIscrizione, $whereIscrizione);
                }
            }else{
                $updateIscrizione = array(
                    "id_professionista" => $richiesta_id_professionista,
                    "dataagg" => date("Y-m-d H:i:s"),
                    "scrittore" => $dblink->filter($_SESSION['cognome_nome_utente'])
                );

                $whereIscrizione = array(
                    "id" => $richiesta_idIscrizioni
                );
                $ok = $ok && $dblink->update("lista_iscrizioni", $updateIscrizione, $whereIscrizione);
            }
            
            if($ok===true){
                $dblink->commit();
                $ok="OK:OK";
            }
            
            if($ok===false){
                $dblink->rollback();
                $ok="KO:KO";
            }
            
            echo "$ok";
        break;
        
        case 'cercaProfessionista':
            $ok = true;
            
            if(strpos($_GET['key_search']," ")){
                $key=explode(" ", $_GET['key_search']);
            }else{
                $key=$_GET['key_search'];
            }
            $array = array();
            $sql_01 = "SELECT id, CONCAT(nome, ' ', cognome) AS ragione_sociale, codice_fiscale, telefono, codice FROM lista_professionisti WHERE stato NOT LIKE 'In Attesa di Eliminazione' ";
            if(is_array($key)){
                $where = "";
                foreach ($key as $value) {
                    $value = $dblink->filter($value);
                    $where.= "AND (codice_fiscale LIKE '%{$value}%' OR nome LIKE '%{$value}%' OR cognome LIKE '%{$value}%' OR telefono LIKE '%{$value}%' OR cellulare LIKE '%{$value}%' OR email LIKE '%{$value}%')";
                }
            }else{
                $key = $dblink->filter($key);
                $where = "AND (codice_fiscale LIKE '%{$key}%' OR nome LIKE '%{$key}%' OR cognome LIKE '%{$key}%' OR telefono LIKE '%{$key}%' OR cellulare LIKE '%{$key}%' OR email LIKE '%{$key}%')";
            }
            $result = $dblink->get_results($sql_01.$where);
            foreach ($result as $row) {
                $array[] = array(
                    "id_professionista" => $row['id'],
                    "ragione_sociale" => $row['ragione_sociale']." (".$row['codice'].") - C.F.: ".$row['codice_fiscale']." - Tel: ".$row['telefono'],
                    "codice_fiscale" => $row['codice_fiscale']
                    );
            }
            echo json_encode($array);
        break;
        
        case "salvaDettaglio":
            
            $ok = true;
            $dblink->begin();
            
            $arrayCampi = $_POST;
            ksort($arrayCampi);
            
            $conto = 0;
            
            $tuttiCampi = array();
            foreach ($arrayCampi as $key => $value) {
                $pos = strpos($key, "copia");
                if ($pos === false) {
                    $pos_001 = strpos($key, "_txt_");
                    if($pos_001 == true) {
                        $tmpArray = explode("_txt_", $key);
                        $tbl = $tmpArray[0];
                        $campo = $tmpArray[1];
                        if(strpos($campo,"data")!==false){
                            $tuttiCampi[$tbl][$campo] = GiraDataOra(trim(str_replace("`", "", $value)));
                        }else{
                            $tuttiCampi[$tbl][$campo] = $dblink->filter(trim(str_replace("`", "", $value)));
                        }
                    }else{
                        switch ($key) {

                           case "dataagg":
                               $tuttiCampi[$key]=date("Y-m-d H:i:s");
                           break;

                           case "scrittore":
                               $tuttiCampi[$key]=$dblink->filter($_SESSION['cognome_nome_utente']);
                           break;

                           default:
                                $tmp = explode("_", $key);
                                $nome_campo = substr($key, (strlen("txt_".$tmp[1]."_")));
                                
                                $tuttiCampi['lista_preventivi_dettaglio'][$tmp[1]][$nome_campo] = $dblink->filter(trim(str_replace("`", "", $value)));
                           break;
                        }
                    }
                    //echo '<li style="color:red;">'.$key.' = '.$arrayCampi[$key].'</li>';             
                } 
            }
            
            $countPreventiviDettaglio = 0;
            
            foreach($tuttiCampi['lista_preventivi_dettaglio'] as $record){
                $countPreventiviDettaglio++;
                /*foreach($record as $nomi_colonne => $valore){
                    echo '<lI>$nomi_colonne = '.$nomi_colonne.' / $valore = '.$valore.'</li>';
                }*/
            }
            
            
            $tuttiCampi['calendario']['dataagg'] = $tuttiCampi['dataagg'];
            $tuttiCampi['calendario']['scrittore'] = $tuttiCampi['scrittore'];
            
            $tuttiCampi['lista_aziende']['dataagg'] = $tuttiCampi['dataagg'];
            $tuttiCampi['lista_aziende']['scrittore'] = $tuttiCampi['scrittore'];
            
            $tuttiCampi['lista_professionisti']['dataagg'] = $tuttiCampi['dataagg'];
            $tuttiCampi['lista_professionisti']['scrittore'] = $tuttiCampi['scrittore'];
            
            //VERIFICA AZIENDA E PROFESSIONISTA
            
            if($tuttiCampi['lista_professionisti']['id']>0){
                $idProfessionista = $tuttiCampi['lista_professionisti']['id'];
                unset($tuttiCampi['lista_professionisti']['id']);
                $ok = $dblink->update("lista_professionisti", $tuttiCampi['lista_professionisti'], array("id"=>$idProfessionista));    
                //echo $dblink->get_query();
                //die;
                
                $ok = $dblink->update("lista_password", array("id_classe" => $tuttiCampi['lista_professionisti']['id_classe'],"email" => $tuttiCampi['lista_professionisti']['email'], "nome" => $tuttiCampi['lista_professionisti']['nome'], "cognome" => $tuttiCampi['lista_professionisti']['cognome'], "id_classe"=>$tuttiCampi['lista_professionisti']['id_classe']), array("id_professionista" => $idProfessionista, 'livello' => 'cliente'));
                $ok = $dblink->update(MOODLE_DB_NAME.".mdl_user", array("email" => $tuttiCampi['lista_professionisti']['email'], "firstname" => $tuttiCampi['lista_professionisti']['nome'], "lastname" => $tuttiCampi['lista_professionisti']['cognome']), array("idnumber" => $idProfessionista));
                
            }else{
                /*if(strlen($tuttiCampi['lista_professionisti']['codice_fiscale'])==16){
                    unset($tuttiCampi['lista_professionisti']['id']);
                    $ok = $dblink->insert("lista_professionisti", $tuttiCampi['lista_professionisti']);
                    $tuttiCampi['lista_professionisti']['id'] = $dblink->insert_id();
                    
                    $id_professionista = $tuttiCampi['lista_professionisti']['id'];
                    //ragione_sociale, forma_giuridica, partita_iva, codice_fiscale, indirizzo, cap, citta, provincia, nazione, telefono, cellulare, web, email, settore, categoria
                    $sql_00002 = "SELECT id_azienda FROM `matrice_aziende_professionisti` WHERE id_professionista='".$id_professionista."'";
                    $row_00002 = $dblink->get_row($sql_00002, true);

                    if($row_00002['id_azienda']>0){
                        $id_azienda = $row_00002['id_azienda'];
                    } else { $id_azienda = 0; }

                    $sql_00003 = "UPDATE calendario "
                            . "SET id_professionista='".$id_professionista."',"
                            . "id_azienda = '".$id_azienda."',"
                            . "dataagg = NOW(),"
                            . "scrittore = '".$dblink->filter($_SESSION['cognome_nome_utente'])."'"
                            . "WHERE id_professionista<=0 "
                            . "AND id=".$tuttiCampi['calendario']['id'];
                    $ok = $dblink->query($sql_00003);
                }*/
            }
            
            if($tuttiCampi['lista_aziende']['id']>0){
                $idAzienda = $tuttiCampi['lista_aziende']['id'];
                unset($tuttiCampi['lista_aziende']['id']);
                $ok = $dblink->update("lista_aziende", $tuttiCampi['lista_aziende'], array("id"=>$idAzienda));                
            }else{
                if(strlen($tuttiCampi['lista_aziende']['partita_iva'])>8){
                    // IN TEORIA QUI NON CI DEVO MAI ARRIVARE
                    //unset($tuttiCampi['lista_aziende']['id']);
                    //$ok = $dblink->insert("lista_aziende", $tuttiCampi['lista_aziende']);
                }
            }
            
            if($tuttiCampi['calendario']['id']>0){
                $idCalendario = $tuttiCampi['calendario']['id'];
                if(empty($tuttiCampi['calendario']['id_classe']) && !empty($tuttiCampi['lista_professionisti']['id_classe'])){
                    $tuttiCampi['calendario']['id_classe'] = $tuttiCampi['lista_professionisti']['id_classe'];
                }
                unset($tuttiCampi['calendario']['id']);
                
                $rowCampagna = $dblink->get_row("SELECT id_tipo_marketing FROM lista_campagne WHERE id = '".$tuttiCampi['calendario']['id_campagna']."'", true);
                $rowMarketing = $dblink->get_row("SELECT nome FROM lista_tipo_marketing WHERE id = '".$rowCampagna['id_tipo_marketing']."'", true);
                $tuttiCampi['calendario']['id_tipo_marketing'] = $rowCampagna['id_tipo_marketing'];
                $tuttiCampi['calendario']['tipo_marketing'] = $rowMarketing['nome'];
                
                $ok = $dblink->update("calendario", $tuttiCampi['calendario'], array("id"=>$idCalendario));                
            }
            
            //print_r($tuttiCampi['lista_preventivi_dettaglio']);
            for($r=0;$r<$countPreventiviDettaglio;$r++){
                if(!empty($tuttiCampi['lista_preventivi_dettaglio'][$r])){
                    if($tuttiCampi['lista_preventivi_dettaglio'][$r]['id']>0 && $tuttiCampi['lista_preventivi_dettaglio'][$r]['id_prodotto']>0){

                        $tuttiCampi['lista_preventivi_dettaglio'][$r]['dataagg'] = $tuttiCampi['dataagg'];
                        $tuttiCampi['lista_preventivi_dettaglio'][$r]['scrittore'] = $tuttiCampi['scrittore'];
                        /*$tuttiCampi['lista_preventivi_dettaglio'][$r]['id_prodotto'] = $tuttiCampi['lista_preventivi_dettaglio'][$r]['nome_prodotto'];
                        $tuttiCampi['lista_preventivi_dettaglio'][$r]['prezzo_prodotto'] = $tuttiCampi['lista_preventivi_dettaglio'][$r]['euro'];
                        $tuttiCampi['lista_preventivi_dettaglio'][$r]['quantita'] = $tuttiCampi['lista_preventivi_dettaglio'][$r]['qta'];
                        unset($tuttiCampi['lista_preventivi_dettaglio'][$r]['nome_prodotto']);
                        unset($tuttiCampi['lista_preventivi_dettaglio'][$r]['euro']);
                        unset($tuttiCampi['lista_preventivi_dettaglio'][$r]['qta']);*/

                        $sql_0002 = "SELECT nome as nome_prodotto, descrizione as descrizione_breve_prodotto, codice as codice_prodotto, id_prodotto_0, barcode as barcode_prodotto FROM lista_prodotti WHERE id=".$tuttiCampi['lista_preventivi_dettaglio'][$r]['id_prodotto'];
                        $row_0002 = $dblink->get_row($sql_0002, true);

                        $tuttiCampi['lista_preventivi_dettaglio'][$r]['nome_prodotto'] = $row_0002['nome_prodotto'];
                        $tuttiCampi['lista_preventivi_dettaglio'][$r]['descrizione_breve_prodotto'] = $row_0002['descrizione_breve_prodotto'];
                        $tuttiCampi['lista_preventivi_dettaglio'][$r]['codice_prodotto'] = $row_0002['codice_prodotto'];
                        $tuttiCampi['lista_preventivi_dettaglio'][$r]['id_prodotto_0'] = $row_0002['id_prodotto_0'];
                        $tuttiCampi['lista_preventivi_dettaglio'][$r]['barcode_prodotto'] = $row_0002['barcode_prodotto'];

                        $idWhere = $tuttiCampi['lista_preventivi_dettaglio'][$r]['id'];
                        //echo "<br>";
                        unset($tuttiCampi['lista_preventivi_dettaglio'][$r]['id']);
                        unset($tuttiCampi['lista_preventivi_dettaglio'][$r]['id_preventivo']);

                        //print_r($tuttiCampi['lista_preventivi_dettaglio'][$r]);

                        $ok = $dblink->update("lista_preventivi_dettaglio", $tuttiCampi['lista_preventivi_dettaglio'][$r], array("id"=>$idWhere)); 
                        //echo $dblink->get_query();
                        //die;
                        //echo "<br>";
                        if(!$ok) echo "errore Database";
                        /*echo $dblink->get_query();
                        echo "<br>";*/ 

                    }
                }
            }
            
            if($tuttiCampi['lista_preventivi']['id']>0){
                $idPreventivo = $tuttiCampi['lista_preventivi']['id'];
                
                $sql_0001 = "SELECT SUM((prezzo_prodotto*quantita)) AS imponibile, SUM((prezzo_prodotto*(1+(iva_prodotto/100)))*quantita) AS 'importo' FROM lista_preventivi_dettaglio WHERE id_preventivo=".$idPreventivo;
                $row_0001 = $dblink->get_row($sql_0001, true);
                //echo $dblink->get_query();
                //echo "<br>";
                $updatePreventivo=array(
                    "dataagg" => date("Y-m-d H:i:s"),
                    "scrittore" => $dblink->filter($_SESSION['cognome_nome_utente']),
                    "importo"=>$row_0001['importo'],
                    "imponibile"=>$row_0001['imponibile'],
                    "cognome_nome_professionista" => $dblink->filter(getNomeProfessionista($idProfessionista)),
                    "ragione_sociale_azienda" => $dblink->filter(getNomeAzienda($idAzienda)),
                );
                
                if(isset($idAzienda) && $idAzienda>0){
                    $updatePreventivo['id_azienda'] = $idAzienda;
                }

                $ok = $ok && $dblink->update("lista_preventivi", $updatePreventivo, array("id"=>$idPreventivo));
                //echo $dblink->get_query();
                //die();
                //echo "<br>";
            }
            
            if($ok){
                $ok = 1;
                $dblink->commit();
            }else{
                $ok = 0;
                $dblink->rollback();
            }
            //die();
            
            //if($idProfessionista>0)
            //    header("Location: ".BASE_URL."/moduli/anagrafiche/dettaglio_tab.php?tbl=lista_professionisti&id=$idProfessionista&res=$ok");
            //else
            header("Location:".$referer."&res=$ok");
            
        break;

        case "salvaGeneraleAnagrafiche":
            $dblink->begin();
            $ok = true;
            
            $ok = salvaGenerale();
            
            $idProfessionista = $_POST['txt_id'];
            $referer = recupera_referer($_POST['txt_referer']);
            
            if($idProfessionista > 0){
                list($emailAggiornata, $nomeAggiornato, $cognomeAggiornato, $idClasseAggiornato) = $dblink->get_row("SELECT email, nome, cognome, id_classe FROM lista_professionisti WHERE id = '$idProfessionista'");

                $ok = $ok && $dblink->update("lista_password", array("email" => $emailAggiornata, "nome" => $nomeAggiornato, "cognome" => $cognomeAggiornato, "id_classe"=>$idClasseAggiornato), array("id_professionista" => $idProfessionista, 'livello' => 'cliente'));
                $ok = $ok && $dblink->update(MOODLE_DB_NAME.".mdl_user", array("email" => $emailAggiornata, "firstname" => $nomeAggiornato, "lastname" => $cognomeAggiornato), array("idnumber" => $idProfessionista));
            }
            
            if($ok){
                $ok = 1;
                $dblink->commit();
            }else{
                $ok = 0;
                $dblink->rollback();
            }
            
            
            header("Location:".$referer."&res=$ok");
        break;
        
        default:
        break;
    }
}

?>
