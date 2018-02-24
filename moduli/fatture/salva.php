<?php
include_once('../../config/connDB.php');
include_once(BASE_ROOT.'config/confAccesso.php');
include_once(BASE_ROOT.'classi/webservice/client.php');

$moodle = new moodleWebService();

global $dblink;

$referer = recupera_referer();

if (isset($_GET['fn'])) {
    switch ($_GET['fn']) {
        case 'nuovaFatturaProfessionista':
        $idProfessionista = $_GET['idProfessionista'];
        //echo '<li>$idProfessionista = '.$idProfessionista.'</li>';
        $sql_00001 = "INSERT INTO lista_fatture (id, dataagg, scrittore, id_professionista, tipo, stato) VALUES ('',NOW(), '".addslashes($_SESSION['cognome_nome_utente'])."', '".$idProfessionista."', 'Fattura', 'In Attesa di Emissione')";
        $ok = true;
        $dblink->begin();
        $ok = $dblink->query($sql_00001);
            $lastId=$dblink->insert_id();
            if($ok){
                $ok = 1;
                $dblink->commit();
            }else{
                $ok = 0;
                $dblink->rollback();
            }
            header("Location: ".BASE_URL."/moduli/fatture/dettaglio.php?tbl=lista_fatture&id=".$lastId); 
        break;
        
        case 'inviaFattureMultiple':
            $arrayCampi = $_POST;
            unset($arrayCampi['txt_checkbox_all']);
            $conto = 0;
            foreach ($arrayCampi as $key => $value) {
                $pos = strpos($key, "txt_checkbox_");
                if ($pos === false) {
                    //echo '<li style="color:red;">' . $key . ' = ' . $arrayCampi[$key] . '</li>';
                } else {
                    $idFattura = $arrayCampi[$key];
                    //echo '<li style="color:green;">idFattura = ' . $idFattura . '</li>';
                    $sql_0001 = "UPDATE lista_fatture
                        SET dataagg=NOW(),
                        scrittore='" . addslashes($_SESSION['cognome_nome_utente']) . "',
                        stato_invio = 'In Attesa di Invio'
                        WHERE id='" . $idFattura . "'";
                    $rs_0001 = $dblink->query($sql_0001);
                }
            }
            header("Location:" . $referer . "&res=6");
            break;

        case 'emettiFattureMultiple':
            $arrayCampi = $_POST;
            $dataCreazioneEmissione = $arrayCampi['dataCreazioneEmissione'];
            $dataScadenzaEmissione = $arrayCampi['dataScadenzaEmissione'];
            $tipoBanca = $arrayCampi['tipoBanca'];
            $tipoPagamento = $arrayCampi['tipoPagamento'];
            //print_r($_POST);
            unset($arrayCampi['txt_checkbox_all']);
            unset($arrayCampi['dataCreazioneEmissione']);
            unset($arrayCampi['dataScadenzaEmissione']);
            unset($arrayCampi['tipoBanca']);
            unset($arrayCampi['tipoPagamento']);
            //print_r($arrayCampi);
            foreach ($arrayCampi as $key => $value) {
                
                //echo '<li style="color:orange;">dataCreazioneEmissione = ' . $dataCreazioneEmissione . '</li>';
                //echo '<li style="color:orange;">dataScadenzaEmissione = ' . $dataScadenzaEmissione . '</li>';
                //echo '<li style="color:orange;">tipoBanca = ' . $tipoBanca . '</li>';
                //echo '<li style="color:orange;">tipoPagamento = ' . $tipoPagamento . '</li>';

                $pos = strpos($key, "txt_checkbox_");
                if ($pos === false) {
                   // echo '<li style="color:red;">' . $key . ' = ' . $arrayCampi[$key] . '</li>';
                } else {
                    //echo '<li style="color:green;">' . $key . ' = ' . $arrayCampi[$key] . '</li>';
                    $idFattura = $arrayCampi[$key];
                    $sql_00001_01 = "SELECT id, codice, sezionale, importo, stato FROM lista_fatture WHERE id='" . $idFattura . "'";
                    $rs_00001_01 = $dblink->get_results($sql_00001_01);
                    if (!empty($rs_00001_01)) {
                        //StampaSQL($sql_00001_01, '', 'Fattura con id = ' . $idFattura);
                        foreach ($rs_00001_01 as $row_00001_01) {
                            $codSezionale = $row_00001_01['sezionale'];
                            //echo '<li style="color:orange;">codSezionale = ' . $codSezionale . '</li>';
                            //QUI INSERIRE RECUPERO CODICE
                            if (nuovoCodiceFattura($idFattura, $codSezionale)) {
                                $fattura_nuova = nuovoCodiceFattura($idFattura, $codSezionale);
                                //echo '<li style="color:blue;">fattura_nuova = ' . $fattura_nuova . '</li>';
                                $sql_007 = "UPDATE lista_fatture SET
                                dataagg = NOW(),
                                data_creazione = '" . GiraDataOra($dataCreazioneEmissione) . "',
                                data_scadenza = '" . GiraDataOra($dataScadenzaEmissione) . "',
                                codice = '" . $fattura_nuova . "',
                                codice_ricerca = CONCAT('" . $fattura_nuova . SEPARATORE_FATTURA ."',sezionale),
                                pagamento = '" . $tipoPagamento . "',
                                id_fatture_banche = '" . $tipoBanca . "',
                                scrittore = '" . addslashes($_SESSION['cognome_nome_utente']) . "',
                                cognome_nome_agente = (SELECT CONCAT(lista_password.cognome,' ', lista_password.nome) FROM lista_password WHERE lista_password.id = lista_fatture.id_agente),
                                cognome_nome_professionista = (SELECT CONCAT(lista_professionisti.cognome,' ', lista_professionisti.nome) FROM lista_professionisti WHERE lista_professionisti.id = lista_fatture.id_professionista),
                                ragione_sociale_azienda = (SELECT CONCAT(lista_aziende.ragione_sociale,' ', lista_aziende.forma_giuridica) FROM lista_aziende WHERE lista_aziende.id = lista_fatture.id_azienda),
                                nome_campagna = (SELECT lista_campagne.nome FROM lista_campagne WHERE lista_campagne.id = lista_fatture.id_campagna),
                                banca_pagamento = (SELECT lista_fatture_banche.nome FROM lista_fatture_banche WHERE lista_fatture_banche.id = lista_fatture.id_fatture_banche),
                                stato='In Attesa'
                                WHERE id ='" . $idFattura . "'
                                AND sezionale = '" . $codSezionale."'";
                                $rs_007 = $dblink->query($sql_007);
                                if ($rs_007) {
                                    $sql_007_1 = "UPDATE lista_fatture_dettaglio SET
                                    dataagg = NOW(),
                                    codice_fattura = '" . $fattura_nuova . "',
                                    scrittore = '" . addslashes($_SESSION['cognome_nome_utente']) . "',
                                    stato='In Attesa'
                                    WHERE id_fattura ='" . $idFattura . "'";
                                    $rs_007_1 = $dblink->query($sql_007_1);
                                    if ($rs_007_1) {
                                        $sql1 = "INSERT INTO calendario (`id`, `id_professionista`, `id_azienda`, `id_preventivo`, `id_fattura`, `dataagg`, `datainsert`, `orainsert`, `data`, `ora`, `etichetta`, `oggetto`, `messaggio`, `mittente`, `destinatario`, `priorita`, stato)
                                        SELECT '', `id_professionista`, `id_azienda`, `id_preventivo`, `id`, NOW(), NOW(), NOW(), CURDATE(), TIME(NOW()), 'Fatture', CONCAT('Fattura n ', codice ,''), CONCAT('Fattura n ', codice ,': Emessa il ',NOW()), '" . addslashes($_SESSION['cognome_nome_utente']) . "', '" . addslashes($_SESSION['cognome_nome_utente']) . "', 'Normale', 'Fatto'
                                        FROM lista_fatture WHERE id='" . $idFattura . "'";
                                        $rs1 = $dblink->query($sql1);
                                        if ($rs1) {
                                            
                                        }
                                    }
                                }
                            } else {
                               // echo '<li>Errore: nuovoCodiceFattura !</li>';
                            }
                        }
                    }
                }
                //echo '<hr>';
            }
            
            header("Location:" . $referer . "&res=7");
            break;

        case 'accorpaFatture':
            $idFatturaPrincipale = $_GET['idFatturaPrincipale'];
            $idAzienda = $_GET['idAzienda'];
            //echo '<li>idFatturaPrincipale = ' . $idFatturaPrincipale . '</li>';
            //echo '<li>idAzienda = ' . $idAzienda . '</li>';

            //print_r($_POST);

            $arrayCampi = $_POST;
            $conto = 0;
            foreach ($arrayCampi as $key => $value) {
                $pos = strpos($key, "txt_checkbox_");
                if ($pos === false) {
                    //echo '<li style="color:red;">'.$key.' = '.$arrayCampi[$key].'</li>';
                } else {
                    //echo '<li style="color:green;">'.$key.' = '.$arrayCampi[$key].'</li>';
                    $id_fattura_dettaglio_selezionata = $arrayCampi[$key];
                    //echo '<li style="color:green;">id_fattura_dettaglio_selezionata = ' . $id_fattura_dettaglio_selezionata . '</li>';
                    $sql_0001 = "UPDATE lista_fatture_dettaglio
                        SET dataagg=NOW(),
                        scrittore='" . addslashes($_SESSION['cognome_nome_utente']) . "',
                        id_fattura = '" . $idFatturaPrincipale . "',
                        stato = 'Accorpata'
                        WHERE id='" . $id_fattura_dettaglio_selezionata . "'
                        AND id_fattura !='" . $idFatturaPrincipale . "'
                        AND id_azienda='" . $idAzienda . "'
                        AND stato NOT LIKE 'Accorpata'";
                    $rs_0001 = $dblink->query($sql_0001);
                    if ($rs_0001) {
                        //echo '<li style="color:green;">id_fattura_dettaglio_selezionata --> Aggiornata :)</li>';
                    } else {
                        //echo '<li style="color:red;">id_fattura_dettaglio_selezionata --> NON Aggiornata :(</li>';
                    }
                }
            }

            //echo '<hr>';

            $sql_00001_01 = "SELECT id FROM lista_fatture WHERE id_azienda='" . $idAzienda . "'
            AND id !='" . $idFatturaPrincipale . "'
            AND stato LIKE 'In Attesa di Emissione'";
            $rs_00001_01 = $dblink->get_results($sql_00001_01);
            if (!empty($rs_00001_01)) {
                //StampaSQL($sql_00001_01, '', '');
                foreach ($rs_00001_01 as $row_00001_01) {
                    //echo '<hr>';
                    $sql_00001_02 = "SELECT * FROM lista_fatture_dettaglio WHERE id_fattura='" . $row_00001_01['id'] . "'";
                    $rs_00001_02 = $dblink->num_rows($sql_00001_02);
                    //if ($rs_00001_02) {
                        //StampaSQL($sql_00001_02, '', '');
                        if ($rs_00001_02 <= 0) {
                            //echo '<li>NON ABBIAMO DETTAGLIO PER id_fattura  = ' . $row_00001_01['id'] . '</li>';
                            $sql_0002 = "UPDATE lista_fatture
                            SET dataagg=NOW(),
                            scrittore='" . addslashes($_SESSION['cognome_nome_utente']) . "',
                            stato='Accorpata'
                            WHERE id='" . $row_00001_01['id'] . "'
                            AND id_azienda='" . $idAzienda . "'";
                            $rs_0002 = $dblink->query($sql_0002);
                            if ($rs_0002) {
                                //echo '<li style="color:green;">Fattura Stato da Accorpare --> Aggiornata :)</li>';
                            } else {
                                //echo '<li style="color:red;">Fattura Stato da Accorpare --> NON Aggiornata :(</li>';
                            }
                        }
                    //}
                }
            }

            $sql_007_aggiorna_importi = "SELECT 
            SUM((prezzo_prodotto*quantita)) AS imponibile, 
            SUM((prezzo_prodotto*(1+(iva_prodotto/100)))*quantita) AS 'importo' 
            FROM lista_fatture_dettaglio WHERE id_fattura=" . $idFatturaPrincipale;
            $rs_007_aggiorna_importi = $dblink->get_results($sql_007_aggiorna_importi);
            if ($rs_007_aggiorna_importi) {
                foreach ($rs_007_aggiorna_importi as $row_007_aggiorna_importi) {
                    $totale_imponibile = $row_007_aggiorna_importi['imponibile'];
                    $totale_importo = $row_007_aggiorna_importi['importo'];
                }
            }

            $sql_007_aggiorna_fattura = "UPDATE lista_fatture 
            SET imponibile ='" . $totale_imponibile . "',
            importo = '" . $totale_importo . "'
            WHERE id=" . $idFatturaPrincipale;
            $rs_007_aggiorna_fattura = $dblink->query($sql_007_aggiorna_fattura);

            header("Location:" . BASE_URL . "/moduli/fatture/dettaglio.php?tbl=lista_fatture&id=" . $idFatturaPrincipale . "");
            break;

        case "nuovoFattureDettaglio":
            $ok = true;
            $dblink->begin();
            //DAV IDE RIFAI QUESTO CON TUO CODICE
            $sql_inserisci_prodotto_in_fattura = "INSERT INTO lista_fatture_dettaglio (id, dataagg, scrittore, id_fattura, id_preventivo, quantita)  SELECT '', NOW(), '" . addslashes($_SESSION['cognome_nome_utente']) . "', id, id_preventivo, 1 FROM lista_fatture WHERE id=" . $_GET['id'];
            $ok = $dblink->query($sql_inserisci_prodotto_in_fattura);
            $lastId = $dblink->insert_id();
            if ($ok) {
                $ok = 1;
                $dblink->commit();
            } else {
                $ok = 0;
                $dblink->rollback();
            }
            //header("Location:modifica.php?tbl=lista_preventivi_dettaglio&id=$lastId&res=$ok");
            header("Location:" . $referer . "#modifica_preventivo_dettaglio");
            break;

        case 'settaAziendaFattura':
            $ok = true;
            $dblink->begin();
            //salva.php?tbl=lista_fatture&id=$id&idAzienda=',id_azienda,'&fn=settaAziendaFattura
            $idFattura = $_GET['id'];
            $idAzienda = $_GET['idAzienda'];
            $sql_007 = "UPDATE lista_fatture SET dataagg=NOW(), id_azienda=" . $idAzienda . " WHERE id=" . $idFattura;
            $ok = $dblink->query($sql_007);
            $lastId = $dblink->insert_id();
            if ($ok) {
                $ok = 1;
                $dblink->commit();
            } else {
                $ok = 0;
                $dblink->rollback();
            }
            header("Location:" . $referer . "");
            break;

        case 'inviaEmailFattura':

            //$idFattura = $_GET['idFatt'];
            $wMitt = $_POST['mitt'];
            $wDest = $_POST['dest'];
            $wDestCC = $_POST['dest_cc'];
            $wDestBCC = "";
            $wOgg = $_POST['ogg'];
            $wMess = $_POST['mess'];

            $wAllegato_1 = $_POST['fileDoc'];
            $wAllegato_2 = "";
            
            $PasswdEmailUtente = "";
            $testo_debug = "";

            if (strlen($wDest) > 5 and $wOgg != 'BETAFORMAZIONE - ') {

                /*  echo '<li>$wMitt = '.$wMitt.'</li>';
                  echo '<li>$wDest = '.$wDest.'</li>';
                  echo '<li>$wDestCC = '.$wDestCC.'</li>';
                  echo '<li>$wOgg = '.$wOgg.'</li>';
                  //echo '<li>$wMess = '.$wMess.'</li>';
                  echo '<li>$wAllegato_1 = '.$wAllegato_1.'</li>';
                  echo '<li>$wAllegato_2 = '.$wAllegato_2.'</li>';
                  echo '<li>$wAllegato_2 = '.$wAllegato_2.'</li>';
                  echo '<li>$idFattura = '.$idFattura.'</li>'; */
                if (isset($_FILES['documentoAllegato1']) and strlen($_FILES["documentoAllegato1"]["name"]) > 3) {
                    $testo_debug .= '<h1>UPLOAD</h1>';
                    $wAllegato_2 = $_FILES["documentoAllegato1"]["name"];
                    $testo_debug .= '<li>$nome_documentoAllegato1 = ' . $nome_documentoAllegato1 . '</li>';

                    /** 		UPLOAD IMMAGINE		 */
                    $percorso_tabella = BASE_ROOT . "media/lista_documenti/" . $_SESSION['id_utente'];
                    $testo_debug .= '<li>$percorso_tabella = ' . $percorso_tabella . '</li>';
                    //echo '<li>$percorso_tabella = '.$percorso_tabella.'</li>';
                    if ($_FILES["documentoAllegato1"]["error"] > 0 and strlen($_FILES["documentoAllegato1"]["name"]) > 1) {
                        $testo_debug .= "<li>Return Code: " . $_FILES["documentoAllegato1"]["error"] . "</li>";
                    } else {
                        /* 	echo "<li>Upload: " . $_FILES["file"]["name"] . "</li>";
                          echo "<li>Type: " . $_FILES["file"]["type"] . "</li>";
                          echo "<li>Size: " . ($_FILES["file"]["size"] / 1024) . " Kb</li>";
                          echo "<li>Temp file: " . $_FILES["file"]["tmp_name"] . "</li>"; */

                        //@mkdir("".$percorso_tabella."");

                        if (!mkdir($percorso_tabella, 0777, true)) {
                            //die('Failed to create folders...');
                            $testo_debug .= "Failed to create folders...";
                        }

                        if (strlen($_FILES["documentoAllegato1"]["name"]) > 3) {
                            if (file_exists("" . $percorso_tabella . "/" . $_FILES["documentoAllegato1"]["name"])) {
                                //echo "<li>".$_FILES["file"]["name"] . " already exists.</li>";
                                move_uploaded_file($_FILES["documentoAllegato1"]["tmp_name"], "" . $percorso_tabella . "/" . $_FILES["documentoAllegato1"]["name"]);
                                //echo "<li>Stored in: " . "".$percorso_tabella."/" . $_FILES["file"]["name"]."</li>";
                            } else {
                                move_uploaded_file($_FILES["documentoAllegato1"]["tmp_name"], "" . $percorso_tabella . "/" . $_FILES["documentoAllegato1"]["name"]);
                                $testo_debug .= "<li>Stored in: " . "" . $percorso_tabella . "/" . $_FILES["documentoAllegato1"]["name"] . "</li>";
                            }
                        }
                    }
                    /** 		FINE	UPLOAD IMMAGINE		 */
                } else {
                    $testo_debug .= "<h3>NESSUN FILE RECUPERATO</h3>";
                }

                inviaEmailFattura($wMitt, $wDest, $wDestCC, $wDestBCC, $wOgg, $wMess, $wAllegato_1, $wAllegato_2, $PasswdEmailUtente);
            }
            header("Location:$referer");

        break;

        case 'SalvaFatturaDettaglio':
            //print_r($_POST);
            $arrayRisultati = $_POST;

            $conto = 0;

            $tuttiCampi = array();
            foreach ($arrayRisultati as $key => $value) {
                //echo "<br>KEY: $key<br>";
                $pos_001 = strpos($key, "txt_");
                //echo "POS: ".$pos_001."<br>";
                if ($pos_001 === false) {
                    
                } else {
                    $tmp = explode("_", $key);
                    //print_r($tmp);
                    $nome_campo = substr($key, (strlen("txt_" . $tmp[1] . "_")));
                    //echo "<br>$nome_campo<br>";
                    $tuttiCampi[$tmp[1]][$nome_campo] = $dblink->filter(trim(str_replace("`", "", $value)));
                }
                $conto++;
            }
            /* print_r($tuttiCampi); */
            $count = 0;

            foreach ($tuttiCampi as $record) {
                $count++;
                /* foreach($record as $nomi_colonne => $valore){
                  echo '<lI>$nomi_colonne = '.$nomi_colonne.' / $valore = '.$valore.'</li>';
                  } */
            }

            for ($r = 0; $r < $count; $r++) {
                $tuttiCampi[$r]['dataagg'] = date("Y-m-d H:i:s");
                $tuttiCampi[$r]['scrittore'] = $dblink->filter($_SESSION['cognome_nome_utente']);

                if ($tuttiCampi[$r]['id'] > 0) {
                    $idWhere = $tuttiCampi[$r]['id'];
                    //echo "<br>";
                    unset($tuttiCampi[$r]['id']);
                    if($tuttiCampi[$r]['id_prodotto']>0){
                        $sql_0002 = "SELECT nome as nome_prodotto, descrizione as descrizione_breve_prodotto, codice as codice_prodotto, barcode as barcode_prodotto FROM lista_prodotti WHERE id=" . $tuttiCampi[$r]['id_prodotto'];
                        $row_0002 = $dblink->get_row($sql_0002, true);

                        $tuttiCampi[$r]['nome_prodotto'] = $row_0002['nome_prodotto'];
                        $tuttiCampi[$r]['codice_prodotto'] = $row_0002['codice_prodotto'];
                        $tuttiCampi[$r]['barcode_prodotto'] = $row_0002['barcode_prodotto'];
                    }
                    //print_r($tuttiCampi[$r]);
                    $ok = $dblink->update("lista_fatture_dettaglio", $tuttiCampi[$r], array("id" => $idWhere));
                    //if (!$ok)
                        //echo "errore Database";
                    //echo $dblink->get_query();
                    //echo "<br>";
                }
            }

            $sql_0001 = "SELECT SUM((prezzo_prodotto*quantita)) AS imponibile, SUM((prezzo_prodotto*(1+(iva_prodotto/100)))*quantita) AS 'importo' FROM lista_fatture_dettaglio WHERE id_fattura=" . $_GET['idFatt'];
            $row_0001 = $dblink->get_row($sql_0001, true);

            $update = array(
                "dataagg" => date("Y-m-d H:i:s"),
                "scrittore" => $dblink->filter($_SESSION['cognome_nome_utente']),
                "importo" => $row_0001['importo'],
                "imponibile" => $row_0001['imponibile']
            );

            $ok = $ok && $dblink->update("lista_fatture", $update, array("id" => $_GET['idFatt']));

            header("Location:$referer");

            //echo $ok;

            /*
              $numero = 0;
              for($numero=0;$numero<=40;$numero++){
              foreach($arrayRisultati as $nomi_colonne => $valore){
              if(!isset($_POST['txt_'.$numero.'_id'])){
              break;
              }
              if(str_replace('txt_'.$numero.'_','',$nomi_colonne)=='id'){
              echo '--> RECORD';
              }
              echo '<lI>$nomi_colonne = '.$nomi_colonne.' / $valore = '.$valore.'</li>';
              $nome_colonna_pulito = str_replace('txt_'.$numero.'_','',$nomi_colonne);
              echo '<li>$nome_colonna_pulito = '.$nome_colonna_pulito.' </li>';

              }
              } */
            break;

        case 'emettiFattura':
            $idFattura = $_GET['idFatturaEmettere'];
            $codSezionale = $_GET['codSezionale'];

            if (nuovoCodiceFattura($idFattura, $codSezionale)) {
                $fattura_nuova = nuovoCodiceFattura($idFattura, $codSezionale);
                $sql_007 = "UPDATE lista_fatture SET
                dataagg = NOW(),
                data_creazione = CURDATE(),
                data_scadenza = CURDATE(),
                codice = '" . $fattura_nuova . "',
                codice_ricerca = CONCAT('" . $fattura_nuova . SEPARATORE_FATTURA ."',sezionale),
                scrittore = '" . addslashes($_SESSION['cognome_nome_utente']) . "',
                cognome_nome_agente = (SELECT CONCAT(lista_password.cognome,' ', lista_password.nome) FROM lista_password WHERE lista_password.id = lista_fatture.id_agente),
                cognome_nome_professionista = (SELECT CONCAT(lista_professionisti.cognome,' ', lista_professionisti.nome) FROM lista_professionisti WHERE lista_professionisti.id = lista_fatture.id_professionista),
                ragione_sociale_azienda = (SELECT CONCAT(lista_aziende.ragione_sociale,' ', lista_aziende.forma_giuridica) FROM lista_aziende WHERE lista_aziende.id = lista_fatture.id_azienda),
                nome_campagna = (SELECT lista_campagne.nome FROM lista_campagne WHERE lista_campagne.id = lista_fatture.id_campagna),
                banca_pagamento = (SELECT lista_fatture_banche.nome FROM lista_fatture_banche WHERE lista_fatture_banche.id = lista_fatture.id_fatture_banche),
                stato='In Attesa'
                WHERE id ='" . $idFattura . "'
                AND sezionale = '" . $codSezionale."'";
                $rs_007 = $dblink->query($sql_007);
                if ($rs_007) {
                    $sql_007_1 = "UPDATE lista_fatture_dettaglio SET
                    dataagg = NOW(),
                    codice_fattura = '" . $fattura_nuova . "',
                    scrittore = '" . addslashes($_SESSION['cognome_nome_utente']) . "',
                    stato='In Attesa'
                    WHERE id_fattura ='" . $idFattura . "'";
                    $rs_007_1 = $dblink->query($sql_007_1);
                    if ($rs_007_1) {
                        $sql1 = "INSERT INTO calendario (`id`, `id_professionista`, `id_azienda`, `id_preventivo`, `id_fattura`, `dataagg`, `datainsert`, `orainsert`, `data`, `ora`, `etichetta`, `oggetto`, `messaggio`, `mittente`, `destinatario`, `priorita`, stato)
                        SELECT '', `id_professionista`, `id_azienda`, `id_preventivo`, `id`, NOW(), NOW(), NOW(), CURDATE(), TIME(NOW()), 'Fatture', CONCAT('Fattura n ', codice ,''), CONCAT('Fattura n ', codice ,': Emessa il ',NOW()), '" . addslashes($_SESSION['cognome_nome_utente']) . "', '" . addslashes($_SESSION['cognome_nome_utente']) . "', 'Normale', 'Fatto'
                        FROM lista_fatture WHERE id='" . $idFattura . "'";
                        $rs1 = $dblink->query($sql1);
                        if ($rs1) {
                            header("Location:" . $referer . "");
                        }
                    }
                }
            } else {
                //echo '<li>Errore: nuovoCodiceFattura !</li>';
            }
            break;

        case "fatturaPagataTotaleCosto":
            $idCosto = $_GET['idCosto'];
            ;
            $idFattura = $_GET['idFatturaPagata'];
            $statoPagamento = $_GET['statoPagamento'];
            $etichettaPagamento = ' Totale';

            $sql2 = "INSERT INTO calendario (`id`, `id_professionista`, `id_azienda`, `id_preventivo`, `id_fattura`, `dataagg`, `datainsert`, `orainsert`, `data`, `ora`, `etichetta`, `oggetto`, `messaggio`, `mittente`, `destinatario`, `priorita`, stato)
            SELECT '', `id_professionista`, `id_azienda`, `id_preventivo`, `id`, NOW(), NOW(), NOW(), CURDATE(), TIME(NOW()), 'Fatture', CONCAT('Fattura n ', codice ,''), CONCAT('Fattura n ', codice ,' pagata" . $etichettaPagamento . "'), '" . addslashes($_SESSION['cognome_nome_utente']) . "', '" . addslashes($_SESSION['cognome_nome_utente']) . "', 'Normale', 'Fatto'
            FROM lista_fatture WHERE id='" . $idFattura . "'";
            $rs2 = $dblink->query($sql2);
            if ($rs2) {
                $sql_3 = "UPDATE lista_fatture_dettaglio
                            SET stato='Pagata',
                            dataagg = NOW(),
                            scrittore = '" . addslashes($_SESSION['cognome_nome_utente']) . "'
                            WHERE id_fattura='" . $idFattura . "'";
                $rs_3 = $dblink->query($sql_3);
                if ($rs_3) {
                    $sql_4 = "UPDATE lista_fatture
                                    SET stato='Pagata',
                                    dataagg = NOW(),
                                    data_pagamento = CURDATE(),
                                    scrittore = '" . addslashes($_SESSION['cognome_nome_utente']) . "',
                                    note=CONCAT(note,'<li>',NOW(),': Fattura pagata</li>')
                                    WHERE id=" . $idFattura;
                    $rs_4 = $dblink->query($sql_4);
                    if ($rs_4) {
                        $sql_5 = "UPDATE lista_costi
                                        SET stato='Pagata',
                                        uscite = 0,
                                        tipo = 'Fattura Pagata',
                                        categoria = 'Fattura Pagata',
                                        dataagg = NOW(),
                                        scrittore = '" . addslashes($_SESSION['cognome_nome_utente']) . "',
                                        descrizione=CONCAT(descrizione,'<li>',NOW(),': Fattura pagata</li>')
                                        WHERE id=" . $idCosto;
                        $rs_5 = $dblink->query($sql_5);
                        if ($rs_5) {
                            //echo $sql_5;
                            header("Location:" . $referer . "");
                        }
                    }
                }
            }
            break;

        case  "fatturaResettaPagamento":
            $idFattura = $_GET['idFatturaPagata'];
            
            $ok = $dblink->delete("lista_costi", array("id_fattura" => $idFattura));
            $ok = $dblink->delete("calendario", array("id_fattura" => $idFattura));
            
            if($ok){
                 $sql_3 = "UPDATE lista_fatture_dettaglio
                            SET stato='In Attesa',
                            dataagg = NOW(),
                            scrittore = '" . addslashes($_SESSION['cognome_nome_utente']) . "'
                            WHERE id_fattura='" . $idFattura . "'";
                $rs_3 = $dblink->query($sql_3);
                if ($rs_3) {
                    $sql_4 = "UPDATE lista_fatture
                                SET stato='In Attesa',
                                dataagg = NOW(),
                                data_pagamento = '0000-00-00',
                                scrittore = '" . $dblink->filter($_SESSION['cognome_nome_utente']) . "',
                                note=CONCAT(note,'<li>',NOW(),': Resettato Pagamento Fattura (".$dblink->filter($_SESSION['cognome_nome_utente']).")</li>')
                                WHERE id=" . $idFattura;
                    $rs_4 = $dblink->query($sql_4);
                    
                    header("Location:" . $referer . "");
                }
            }
            
            header("Location:" . $referer . "");
            
        break;
            
        case 'fatturaPagata':
            $idFattura = $_GET['idFatturaPagata'];
            $statoPagamento = $_GET['statoPagamento'];
            $importoTotale = $_GET['importoTotale'];

            if ($statoPagamento == 'Parziale') {
                $etichettaPagamento = ' Parziale';
            } else {
                $etichettaPagamento = '';
            }

            if ($importoTotale != 0) {
                $sql = "INSERT INTO lista_costi (`id`, `id_area`, `dataagg`, `data_creazione`, `data_scadenza`, `id_fattura`,  `id_preventivo`, `id_professionista`,  `id_azienda`,  `id_documento`, `tipo_documento`, `categoria`, `descrizione`, `entrate`, `note`, `scrittore`, `tipo`, `stato`)
                    SELECT '', `id_area`, NOW(), CURDATE(), CURDATE(), id, `id_preventivo`, `id_professionista`,  `id_azienda`, '', 'Fattura Pagata" . $etichettaPagamento . "', 'Fattura Pagata" . $etichettaPagamento . "', CONCAT('Fattura Pagata" . $etichettaPagamento . " n ',codice,' - Preventivo ',codice_preventivo), '" . $importoTotale . "', note, '" . addslashes($_SESSION['cognome_nome_utente']) . "', 'Fattura Pagata" . $etichettaPagamento . "', 'Pagata" . $etichettaPagamento . "' 
                    FROM lista_fatture WHERE id='" . $idFattura . "'";
            } else {
                $sql = "INSERT INTO lista_costi (`id`, `id_area`, `dataagg`, `data_creazione`, `data_scadenza`, `id_fattura`,  `id_preventivo`, `id_professionista`,  `id_azienda`,  `id_documento`, `tipo_documento`, `categoria`, `descrizione`, `entrate`, `uscite`, `imponibile`, `iva`, `imposta`, `note`, `scrittore`, `tipo`, `stato`)
                    SELECT '', `id_area`, NOW(), CURDATE(), CURDATE(), id, `id_preventivo`, `id_professionista`,  `id_azienda`, '', 'Fattura Pagata" . $etichettaPagamento . "', 'Fattura Pagata" . $etichettaPagamento . "', CONCAT('Fattura Pagata" . $etichettaPagamento . " n ',codice,' - Preventivo ',codice_preventivo), importo, '', imponibile, iva, '', note, '" . addslashes($_SESSION['cognome_nome_utente']) . "', 'Fattura Pagata" . $etichettaPagamento . "', 'Pagata" . $etichettaPagamento . "' 
                    FROM lista_fatture WHERE id='" . $idFattura . "'";
            }

            $rs = $dblink->query($sql);
            if ($rs) {
                $id_costo_inserito = $dblink->lastid();
                $sql2 = "INSERT INTO calendario (`id`, `id_professionista`, `id_azienda`, `id_preventivo`, `id_fattura`, `dataagg`, `datainsert`, `orainsert`, `data`, `ora`, `etichetta`, `oggetto`, `messaggio`, `mittente`, `destinatario`, `priorita`, stato)
                    SELECT '', `id_professionista`, `id_azienda`, `id_preventivo`, `id`, NOW(), NOW(), NOW(), CURDATE(), TIME(NOW()), 'Fatture', CONCAT('Fattura n ', codice ,''), CONCAT('Fattura n ', codice ,' pagata" . $etichettaPagamento . "'), '" . addslashes($_SESSION['cognome_nome_utente']) . "', '" . addslashes($_SESSION['cognome_nome_utente']) . "', 'Normale', 'Fatto'
                    FROM lista_fatture WHERE id='" . $idFattura . "'";
                $rs2 = $dblink->query($sql2);
                if ($rs2) {
                    $sql_3 = "UPDATE lista_fatture_dettaglio
                            SET stato='Pagata" . $etichettaPagamento . "',
                            dataagg = NOW(),
                            scrittore = '" . addslashes($_SESSION['cognome_nome_utente']) . "'
                            WHERE id_fattura='" . $idFattura . "'";
                    $rs_3 = $dblink->query($sql_3);
                    if ($rs_3) {
                        $sql_4 = "UPDATE lista_fatture
                                    SET stato='Pagata" . $etichettaPagamento . "',
                                    dataagg = NOW(),
                                    data_pagamento = CURDATE(),
                                    scrittore = '" . addslashes($_SESSION['cognome_nome_utente']) . "',
                                    note=CONCAT(note,'<li>',NOW(),': Fattura pagata</li>')
                                    WHERE id=" . $idFattura;
                        $rs_4 = $dblink->query($sql_4);
                        if ($rs_4) {
                            if ($statoPagamento == 'Parziale') {
                                if ($id_costo_inserito <= 0) {
                                    //echo '<li>fatturaPagata -> Parziale  id_costo_inserito Errore:  !!!!</li>';
                                } else {
                                    header("Location: " . BASE_URL . "/moduli/costi/modifica.php?tbl=lista_costi&id=" . $id_costo_inserito);
                                }
                            } else {
                                header("Location:" . $referer . "");
                            }
                        }
                    }
                }
            }
            break;

        case 'fatturaPagataTotaleParziale':
            $idFatturaPagataTotaleParziale = $_GET['idFatturaPagataTotaleParziale'];
            $importoTotale = $_GET['importoTotale'];
            $sql_3 = "UPDATE lista_fatture_dettaglio
                        SET stato='Pagata',
                        dataagg = NOW(),
                        scrittore = '" . addslashes($_SESSION['cognome_nome_utente']) . "'
                        WHERE id_fattura='" . $idFatturaPagataTotaleParziale . "'";
            $rs_3 = $dblink->query($sql_3);
            if ($rs_3) {

                $sql_4 = "UPDATE lista_fatture
                        SET stato='Pagata',
                        dataagg = NOW(),
                        data_pagamento = CURDATE(),
                        scrittore = '" . addslashes($_SESSION['cognome_nome_utente']) . "',
                        note=CONCAT(note,'<li>',NOW(),': Fattura Pagata Totale da Parziale</li>')
                        WHERE id=" . $idFatturaPagataTotaleParziale;
                $rs_4 = $dblink->query($sql_4);
                if ($rs_4) {
                    $sql = "INSERT INTO lista_costi (`id`, `id_area`, `dataagg`, `data_creazione`, `data_scadenza`, `id_fattura`,  `id_preventivo`, `id_professionista`,  `id_azienda`,  `id_documento`, `tipo_documento`, `categoria`, `descrizione`, `entrate`, `uscite`, `imponibile`, `iva`, `imposta`, `note`, `scrittore`, `tipo`, `stato`)
                    SELECT '', `id_area`, NOW(), CURDATE(), CURDATE(), id, `id_preventivo`, `id_professionista`,  `id_azienda`, '', 'Fattura Pagata Totale da Parziale', 'Fattura Pagata Totale da Parziale', CONCAT('Fattura Pagata Totale da Parziale n ',codice,' - Preventivo ',codice_preventivo), '" . $importoTotale . "', '', imponibile, iva, '', note, '" . addslashes($_SESSION['cognome_nome_utente']) . "', 'Fattura Pagata Totale da Parziale', 'Fattura Pagata Totale da Parziale' 
                    FROM lista_fatture WHERE id='" . $idFatturaPagataTotaleParziale . "'";
                    $rs = $dblink->query($sql);
                    if ($rs) {
                        $id_costo_inserito = $dblink->insert_id();
                        header("Location: " . BASE_URL . "/moduli/costi/modifica.php?tbl=lista_costi&id=" . $id_costo_inserito);
                    }
                }
            }

        break;

        case 'notaDiCredito':
            $idFattura = $_GET['idFatturaPagata'];
            $statoPagamento = $_GET['statoPagamento'];
            $codSezionale = $_GET['codSezionale'];

            $fattura_nuova = nuovoCodiceFattura($idFattura, $codSezionale);

            $sql_000001 = "INSERT INTO lista_fatture (`id`, `id_fattura_nota_credito`, `id_area`, `id_fatture_banche`, `id_sezionale`, `sezionale`, `dataagg`, `data_creazione`, `data_scadenza`, `id_contatto`, `id_professionista`, `id_azienda`, `id_preventivo`, `codice_preventivo`, `gestore`, `codice`, `codice_ricerca`, `barcode`, `causale`, `pagamento`, `importo`, `imponibile`, `sconto`, `iva`, `note`, `scrittore`, `tipo`, `stato`, `id_agente`, `cognome_nome_agente`, `campo_1`, `campo_2`, `campo_3`, `numerico_1`, `numerico_2`, `id_campagna`, `id_calendario`, `data_invio`, `stato_invio`, `cognome_nome_professionista`, `ragione_sociale_azienda`, `nome_campagna`, `banca_pagamento`) 
                    SELECT '', `id_fattura_nota_credito`, `id_area`, `id_fatture_banche`, `id_sezionale`, `sezionale`, NOW(), CURDATE(), CURDATE(), `id_contatto`, `id_professionista`, `id_azienda`, `id_preventivo`, `codice_preventivo`, `gestore`, '" . $fattura_nuova . "', '" . $fattura_nuova . SEPARATORE_FATTURA ."" . $codSezionale . "', `barcode`, `causale`, `pagamento`, (0 - `importo`), (0 - `imponibile`), `sconto`, `iva`, `note`, '" . addslashes($_SESSION['cognome_nome_utente']) . "', 'Nota di Credito', 'Nota di Credito " . $statoPagamento . "', `id_agente`, `cognome_nome_agente`, `campo_1`, `campo_2`, `campo_3`, `numerico_1`, `numerico_2`, `id_campagna`, `id_calendario`, `data_invio`, `stato_invio`, `cognome_nome_professionista`, `ragione_sociale_azienda`, `nome_campagna`, `banca_pagamento` 
                    FROM lista_fatture WHERE id = '" . $idFattura . "'";
            $rs_000001 = $dblink->query($sql_000001);
            if ($rs_000001) {
                $id_Fattura_Nota_di_Credito_inserita = $dblink->insert_id();
                $sql_000002 = "INSERT INTO lista_fatture_dettaglio (`id`, `id_fattura_0`, `id_area`, `id_sezionale`, `sezionale`, `dataagg`, `id_fattura`, `codice_fattura`, `barcode_fattura`, `id_preventivo`, `codice_preventivo`, `barcode_preventivo`, `tipo_prodotto`, `nome_prodotto`, `codice_prodotto`, `barcode_prodotto`, `id_prodotto`, `costo_prodotto`, `id_fornitore`, `costo_fornitore_prodotto`, `prezzo_prodotto`, `iva_prodotto`, `quantita`, `note`, `scrittore`, `tipo`, `stato`, `id_campagna`, `id_calendario`, `id_azienda`, `id_professionista`, `id_provvigione`) 
                        SELECT '', `id_fattura_0`, `id_area`, `id_sezionale`, `sezionale`, `dataagg`, '" . $id_Fattura_Nota_di_Credito_inserita . "', '" . $fattura_nuova . "', `barcode_fattura`, `id_preventivo`, `codice_preventivo`, `barcode_preventivo`, `tipo_prodotto`, `nome_prodotto`, `codice_prodotto`, `barcode_prodotto`, `id_prodotto`, `costo_prodotto`, `id_fornitore`, `costo_fornitore_prodotto`, (0 - `prezzo_prodotto`), `iva_prodotto`, `quantita`, `note`, `scrittore`, 'Nota di Credito " . $statoPagamento . "', 'Nota di Credito " . $statoPagamento . "', `id_campagna`, `id_calendario`, `id_azienda`, `id_professionista`, `id_provvigione`
                        FROM lista_fatture_dettaglio
                        WHERE id_fattura = '" . $idFattura . "'";
                $rs_000002 = $dblink->query($sql_000002);
                if ($rs_000002) {


                    if ($statoPagamento == 'Totale') {
                        $sql_000003 = "UPDATE lista_fatture 
                                    SET id_fattura_nota_credito = '" . $id_Fattura_Nota_di_Credito_inserita . "',
                                    stato = 'Nota di Credito " . $statoPagamento . "',
                                    dataagg = NOW(),
                                    scrittore = '" . addslashes($_SESSION['cognome_nome_utente']) . "'
                                    WHERE id = '" . $idFattura . "'";
                    } else {
                        $sql_000003 = "UPDATE lista_fatture 
                                    SET id_fattura_nota_credito = '" . $id_Fattura_Nota_di_Credito_inserita . "',
                                    dataagg = NOW(),
                                    scrittore = '" . addslashes($_SESSION['cognome_nome_utente']) . "'
                                    WHERE id = '" . $idFattura . "'";
                    }


                    $rs_000003 = $dblink->query($sql_000003);
                    if ($rs_000003) {
                        if ($statoPagamento == 'Totale') {
                            header("Location:" . $referer . "&res=8");
                        } elseif ($statoPagamento == 'Parziale') {
                            header("Location: " . BASE_URL . "/moduli/fatture/dettaglio.php?tbl=lista_fatture&id=" . $id_Fattura_Nota_di_Credito_inserita);
                        }
                    }
                } 
            }

            break;

        case 'attivaCorsoFattura':
//          print_r($_GET);
            $idFattura = $_GET['idFattura'];
            $idFatturaDettaglio = $_GET['idFatturaDettaglio'];
            $idCorso = $_GET['idCorso'];
            $idUtenteMoodle = $_GET['idUtenteMoodle'];
            $idCorsoMoodle = $_GET['idCorsoMoodle'];
            $idProfessionista = $_GET['idProfessionista'];

            $ok = attivaCorsoFattura($idProfessionista, $idFattura, $idFatturaDettaglio, $idCorso, $idUtenteMoodle, $idCorsoMoodle);
            if ($ok) {
                $log->log_all_errors('attivaCorsoFattura -> Corso Attivato Correttamente [idCorsoMoodle = ' . $idCorsoMoodle . ']', 'OK');
                header("Location:" . $referer . "&res=3");
            } else {
                $log->log_all_errors('attivaCorsoFattura -> Corso NON Attivato [idCorsoMoodle = ' . $idCorsoMoodle . ']', 'ERRORE');
                header("Location:" . $referer . "&res=0");
            }
            break;
            
        case 'attivaPacchettoFattura':
//          print_r($_GET);
            $idFattura = $_GET['idFattura'];
            $idFatturaDettaglio = $_GET['idFatturaDettaglio'];
            $idCorso = $_GET['idCorso'];
            $idProdotto = $_GET['idProdotto'];
            $idUtenteMoodle = $_GET['idUtenteMoodle'];
            $idCorsoMoodle = $_GET['idCorsoMoodle'];
            $idProfessionista = $_GET['idProfessionista'];

            $ok = attivaPacchettoFattura($idProfessionista, $idFattura, $idFatturaDettaglio, $idProdotto, $idCorso, $idUtenteMoodle, $idCorsoMoodle);
            if ($ok) {
                $log->log_all_errors('attivaPacchettoFattura -> Corso Attivato Correttamente [idCorsoMoodle = ' . $idCorsoMoodle . ']', 'OK');
                header("Location:" . $referer . "&res=3");
            } else {
                $log->log_all_errors('attivaPacchettoFattura -> Corso NON Attivato [idCorsoMoodle = ' . $idCorsoMoodle . ']', 'ERRORE');
                header("Location:" . $referer . "&res=0");
            }
            break;

        case 'attivaAbbonamentoFattura':
            $idFattura = $_GET['idFattura'];
            $idFatturaDettaglio = $_GET['idFatturaDettaglio'];
            $idUtenteMoodle = $_GET['idUtenteMoodle'];
            $idProfessionista = $_GET['idProfessionista'];

            $ok = attivaAbbonamentoFattura($idProfessionista, $idFattura, $idFatturaDettaglio, $idUtenteMoodle);
            if ($ok) {
                $log->log_all_errors('attivaAbbonamentoFattura -> Abbonamento Attivato Correttamente', 'OK');
                header("Location:" . $referer . "&res=3");
            } else {
                $log->log_all_errors('attivaAbbonamentoFattura -> Abbonamento NON Attivato', 'ERRORE');
                header("Location:" . $referer . "&res=0");
            }
        break;

        /* case 'creaPasswordUtenteDaFattura':
          $idProfessionista = $_GET['idProfessionista'];
          $passwordUser = generaPassword(9);
          $sql_00001 = "INSERT INTO `lista_password` (`id`, `dataagg`, `scrittore`, `id_professionista`, `id_classe`, `livello`, `nome`, `cognome`, `username`, `passwd`, `cellulare`, `email`, `stato`, `data_creazione`, `data_scadenza`)
          SELECT '', NOW(), '".addslashes($_SESSION['cognome_nome_utente'])."', `id`, `id_classe`, 'cliente', `nome`, `cognome`, `email`, '".$passwordUser."', `cellulare`, `email`, 'In Attesa di Moodle', CURDATE(), DATE_ADD(CURDATE(), INTERVAL ".DURATA_ABBONAMENTO." DAY) FROM lista_professionisti WHERE id=".$idProfessionista;
          $ok = $dblink->query($sql_00001);
          if($ok){
          $log->log_all_errors('creaPasswordUtenteDaFattura -> password utente creato correttamente [idProfessionista = '.$idProfessionista.']','OK');
          header("Location:".$referer."&res=3");
          }else{
          $log->log_all_errors('creaPasswordUtenteDaFattura -> password utente NON creato [idProfessionista = '.$idProfessionista.']','ERRORE');
          header("Location:".$referer."&res=0");
          }
          break; */

        case 'creaUtenteTotale':
            $idProfessionista = $_GET['idProfessionista'];
            $ok = creaUtenteTotale($idProfessionista);
            if ($ok) {
                $log->log_all_errors('creaUtenteTotale -> utente creato correttamente [idProfessionista = ' . $idProfessionista . ']', 'OK');
                header("Location:" . $referer . "&res=3");
            } else {
                $log->log_all_errors('creaUtenteTotale -> utente NON creato [idProfessionista = ' . $idProfessionista . ']', 'ERRORE');
                header("Location:" . $referer . "&res=0");
            }
        break;

        case "salvaFattura":
            $ok = true;
            $dblink->begin();
            $idFattura = $_POST['txt_id'];
            list($idSezionaleOld, $sezionaleOld) = $dblink->get_row("SELECT id_sezionale, sezionale FROM lista_fatture WHERE id = '$idFattura'");
            
            $ok = salvaGenerale();
            
            if($idFattura > 0){
                
                list($sezionaleNuovo) = $dblink->get_row("SELECT nome FROM lista_fatture_sezionali WHERE id = '".$_POST['id_sezionale']."'");
                
                $sql_0100 = "UPDATE lista_fatture SET sezionale = '$sezionaleNuovo' WHERE id = '$idFattura' AND stato LIKE 'In Attesa di Emissione'";
                $ok = $ok && $dblink->query($sql_0100);
                
                $sql_0101 = "UPDATE lista_fatture SET id_sezionale = '$idSezionaleOld', sezionale = '$sezionaleOld' WHERE id = '$idFattura' AND stato NOT LIKE 'In Attesa di Emissione'";
                $ok = $ok && $dblink->query($sql_0101);
                
                list($idPreventivo, $idSezionale, $sezionale) = $dblink->get_row("SELECT id_preventivo, id_sezionale, sezionale FROM lista_fatture WHERE id = '$idFattura'");
                
                $sql_0102 = "UPDATE lista_fatture_dettaglio SET id_sezionale = '$idSezionale', sezionale = '$sezionale' WHERE id_fattura = '$idFattura' ";
                $ok = $ok && $dblink->query($sql_0102);
                
                $sql_0103 = "UPDATE lista_fatture SET codice_ricerca = CONCAT(codice,'".SEPARATORE_FATTURA."',sezionale) WHERE id = '$idFattura'";
                $ok = $ok && $dblink->query($sql_0103);
                
                list($idSezPrev, $sezPrev) = $dblink->get_row("SELECT id_sezionale, sezionale FROM lista_preventivi WHERE id = '".$idPreventivo."'");
                
                if($idSezPrev != $idSezionale){
                    $codPrevNum = nuovoCodicePreventivo($idPreventivo, $sezionale);
                    $codPrev = "codice = '$codPrevNum',";
                }else{
                    $codPrev = "";
                }
                
                $sql_0104 = "UPDATE lista_preventivi SET $codPrev id_sezionale = '$idSezionale', sezionale = '$sezionale' WHERE id = '$idPreventivo'";
                $ok = $ok && $dblink->query($sql_0104);
                
                $sql_0105 = "UPDATE lista_preventivi_dettaglio SET id_sezionale = '$idSezionale', sezionale = '$sezionale' WHERE id_preventivo = '$idPreventivo'";
                $ok = $ok && $dblink->query($sql_0105);
            }
            
            if(isset($_POST['txt_referer']) && !empty($_POST['txt_referer'])){
                $referer = recupera_referer($_POST['txt_referer']);
            }
            
            if ($ok) {
                $dblink->commit();
                header("Location:" . $referer . "&res=1");
            } else {
                $dblink->rollback();
                header("Location:" . $referer . "&res=0");
            }
        break;

        default:

        break;
    }
}
?>
