<?php
include_once('../../config/connDB.php');
include_once(BASE_ROOT.'config/confAccesso.php');
include_once(BASE_ROOT.'classi/webservice/client.php');

$moodle = new moodleWebService();

$referer = recupera_referer();

switch ($_GET['fn']) {

    case 'creaUtenteMoodle':
        $idProfessionista = $_GET['idProfessionista'];
        $tabella = $_GET['tbl'];
        $row_0001 = $dblink->get_row("SELECT username, email, nome, cognome, passwd FROM $tabella WHERE id_professionista = '$idProfessionista' ",true);
        //SERVONO PARAMETRI
        $return = $moodle->creaUtenteMoodle($row_0001['username'], $row_0001['email'], $row_0001['nome'], $row_0001['cognome'], $row_0001['passwd'], $idProfessionista);
        if (DISPLAY_DEBUG) {
            $return = objToArray($return);
            var_dump($return);
        }
        //
        //DA INSERIRE DAVIDE 10-07-2017
        //MAIL CREA UTENTE MOODLE

        header("Location:$referer");
    break;

    case 'annullaAbbonamentoMoodle':
        $idUtenteMoodle = $_GET['idUtenteMoodle'];
        $NomeClasse = $_GET['NomeClasse'];

        $return = $moodle->annullaAbbonamentoMoodle($idUtenteMoodle, $NomeClasse);
        if (DISPLAY_DEBUG) {
            $return = objToArray($return);
            var_dump($return);
        }
        //
        //@edo -> notifiche o altre cose da aggiungere nel processo?

        header("Location:$referer");
    break;

    case 'modificaScadenzaCorso':
        $idUtenteMoodle = $_GET['idUtenteMoodle'];
        $idCorso = $_GET['idCorso'];
        $NomeClasse = $_GET['NomeClasse'];
        $DataFineIscrizione = $_GET['DataFineIscrizione'];

        $return = $moodle->iscrizioneCorsoMoodle($idUtenteMoodle, $idCorso, $NomeClasse, $DataFineIscrizione);
        if (DISPLAY_DEBUG) {
            $return = objToArray($return);
            var_dump($return);
        }
        //
        //@edo -> notifiche o altre cose da aggiungere nel processo?

        header("Location:$referer");
    break;

    case "prova_davide":
        $corsi = $dblink->get_results("SELECT * FROM ".MOODLE_DB_NAME.".mdl_course WHERE id='10'");
        
        foreach ($corsi as $corso) {
            $moduli = $dblink->get_results("SELECT * FROM ".MOODLE_DB_NAME.".mdl_course_modules WHERE course='$corso[id]'");
            foreach ($moduli as $modulo) {
                $var = json_decode($modulo['availability'] , true);
                if (DISPLAY_DEBUG) {
                    echo "<li>INSTANCE: $modulo[instance]<br><br>".
                    "<pre>".var_export($var,true). "</pre></li>";
                }
            }
        }
    break;
    
    case "recupera_abbonamenti":
        $abbonamenti = $dblink->get_results("SELECT * FROM ".MOODLE_DB_NAME.".mdl_cohort");
        
        foreach ($abbonamenti as $abbonamento) {
            
            $rowClassi = $dblink->get_row("SELECT id FROM lista_classi WHERE codice_esterno = '".$abbonamento['id']."'",true);

            if($rowClassi['id']>0){
                $updateClasse = array(
                    "dataagg" => date("Y-m-d H:i:s"),
                    "scrittore" => $dblink->filter("autoImport"),
                    "nome" => $dblink->filter($abbonamento['name']),
                    "codice_esterno" => $abbonamento['id']
                );

                $whereClasse = array(
                    "id" => $rowClassi['id']
                );

                $dblink->update("lista_classi", $updateClasse, $whereClasse);
                $idClasse = $rowClassi['id'];
                if(DISPLAY_DEBUG){
                    echo $dblink->get_query();
                    echo "<br />";
                }

            }else{

                $insertClasse = array(
                    "dataagg" => date("Y-m-d H:i:s"),
                    "scrittore" => $dblink->filter("autoImport"),
                    "nome" => $dblink->filter($abbonamento['name']),
                    "stato" => ($abbonamento['visible']) ? "Attivo" : "Non Attivo",
                    "codice_esterno" => $abbonamento['id']
                );

                $dblink->insert("lista_classi", $insertClasse);
                $idClasse = $dblink->lastid();
                if(DISPLAY_DEBUG){
                    echo $dblink->get_query();
                    echo "<br />";
                }
            }
            
            $rowAbbonamenti = $dblink->get_row("SELECT id FROM lista_prodotti WHERE codice_esterno = 'abb_".$abbonamento['id']."'",true);

            if($rowAbbonamenti['id']>0){
                $update = array(
                    "dataagg" => date("Y-m-d H:i:s"),
                    "scrittore" => $dblink->filter("autoImport"),
                    "stato" => ($abbonamento['visible']) ? "Attivo" : "Non Attivo",
                    "gruppo" => "ABBONAMENTO",
                    "categoria" => "ABBONAMENTI",
                    "tipologia" => "e-learning",
                    "codice_esterno" => "abb_".$abbonamento['id']
                );

                $where = array(
                    "id" => $rowAbbonamenti['id']
                );

                $dblink->update("lista_prodotti", $update, $where);
                $idAbbonamento = $rowAbbonamenti['id'];
                if(DISPLAY_DEBUG){
                    echo $dblink->get_query();
                    echo "<br />";
                }

            }else{

                $insert = array(
                    "dataagg" => date("Y-m-d H:i:s"),
                    "scrittore" => $dblink->filter("autoImport"),
                    "nome" => "Abbonamento ".$dblink->filter($abbonamento['name']),
                    "descrizione" => "Abbonamento ".$dblink->filter($abbonamento['name']),
                    "descrizione_breve" => "Abbonamento ".$dblink->filter($abbonamento['name']),
                    "stato" => ($abbonamento['visible']) ? "Attivo" : "Non Attivo",
                    "gruppo" => "ABBONAMENTO",
                    "categoria" => "ABBONAMENTI",
                    "tipologia" => "e-learning",
                    "prezzo_pubblico" => "144",
                    "codice_esterno" => "abb_".$abbonamento['id']
                );

                $dblink->insert("lista_prodotti", $insert);
                $idAbbonamento = $dblink->lastid();
                if(DISPLAY_DEBUG){
                    echo $dblink->get_query();
                    echo "<br />";
                }
            }

            $corsi = $dblink->get_results("SELECT * FROM ".MOODLE_DB_NAME.".mdl_course WHERE id IN (SELECT courseid FROM ".MOODLE_DB_NAME.".mdl_enrol WHERE customint5 = '".$abbonamento['id']."')");

            foreach ($corsi as $corso) {
                if(strlen($corso['id'])<=1) continue;

                $rowProdotti = $dblink->get_row("SELECT id FROM lista_prodotti WHERE codice_esterno = '".$corso['id']."'",true);

                $rowImmagine = $dblink->get_row("SELECT * FROM ".MOODLE_DB_NAME.".mdl_files AS f LEFT JOIN ".MOODLE_DB_NAME.".mdl_files_reference AS r ON f.referencefileid = r.id WHERE f.contextid IN (SELECT id FROM ".MOODLE_DB_NAME.".mdl_context WHERE instanceid='".$corso['id']."') AND f.filesize > 0 AND f.component = 'course' AND f.filearea='overviewfiles' ORDER BY f.filename", true);

                if(!empty($rowImmagine)){
                    $urlImmagine = MOODLE_DOMAIN_NAME."/pluginfile.php/".$rowImmagine['contextid']."/".$rowImmagine['component']."/".$rowImmagine['filearea']."/".$rowImmagine['filename'];
                } else {
                    $urlImmagine = "";
                }
                
                $nomeCategoria = $dblink->get_row("SELECT name FROM ".MOODLE_DB_NAME.".mdl_course_categories WHERE id='".$corso['category']."' LIMIT 1");
                $nomeCategoria = $nomeCategoria[0];
                
                $prezzo = $dblink->get_row("SELECT prezzo_listino FROM elenco_prodotti WHERE LCASE(codice_prodotto)=LCASE('".$corso['betaformazione_courseid']."') LIMIT 1", true);

                if($rowProdotti['id']>0){
                    $update = array(
                        "dataagg" => date("Y-m-d H:i:s"),
                        "scrittore" => $dblink->filter("autoImport"),
                        "nome" => $dblink->filter($corso['shortname']),
                        "descrizione" => $dblink->filter($corso['summary']),
                        "descrizione_breve" => $dblink->filter($corso['fullname']),
                        "stato" => ($corso['visible'] && strlen($corso['betaformazione_courseid'])>0) ? "Attivo" : "Non Attivo",
                        "gruppo" => "CORSO",
                        "categoria" => $nomeCategoria,
                        "tipologia" => "e-learning",
                        "codice" => $corso['betaformazione_courseid'],
                        "codice_esterno" => $corso['id'],
                        "tempo_1" => $corso['time_to_complete'],
                        "url_immagine" => $urlImmagine
                    );

                    $where = array(
                        "id" => $rowProdotti['id']
                    );

                    $dblink->update("lista_prodotti", $update, $where);
                    $idProdotto = $rowProdotti['id'];
                    if(DISPLAY_DEBUG){
                        echo $dblink->get_query();
                        echo "<br />";
                    }

                }else{

                    $insert = array(
                        "dataagg" => date("Y-m-d H:i:s"),
                        "scrittore" => $dblink->filter("autoImport"),
                        "nome" => $dblink->filter($corso['shortname']),
                        "descrizione" => $dblink->filter($corso['summary']),
                        "descrizione_breve" => $dblink->filter($corso['fullname']),
                        "stato" => ($corso['visible'] && strlen($corso['betaformazione_courseid'])>0) ? "Attivo" : "Non Attivo",
                        "gruppo" => "CORSO",
                        "categoria" => $nomeCategoria,
                        "tipologia" => "e-learning",
                        "prezzo_pubblico" => $prezzo['prezzo_listino'],
                        "codice" => $corso['betaformazione_courseid'],
                        "codice_esterno" => $corso['id'],
                        "tempo_1" => $corso['time_to_complete'],
                        "url_immagine" => $urlImmagine
                    );

                    $dblink->insert("lista_prodotti", $insert);
                    $idProdotto = $dblink->lastid();
                    if(DISPLAY_DEBUG){
                        echo $dblink->get_query();
                        echo "<br />";
                    }
                }

                $rowProdottiDettaglio = $dblink->get_row("SELECT id FROM lista_prodotti_dettaglio WHERE id_prodotto_0 = '".$idAbbonamento."' AND id_prodotto='$idProdotto'",true);

                if($rowProdottiDettaglio['id']>0){
                    $updateProdottiDett = array(
                        "dataagg" => date("Y-m-d H:i:s"),
                        "scrittore" => $dblink->filter("autoImport"),
                        "nome" => $dblink->filter($corso['shortname']),
                        "descrizione" => $dblink->filter($corso['summary']),
                        "stato" => ($corso['visible'] && strlen($corso['betaformazione_courseid'])>0) ? "Attivo" : "Non Attivo",
                        "gruppo" => "CORSO",
                        "codice" => $corso['betaformazione_courseid'],
                        "id_prodotto_0" => $idAbbonamento,
                        "id_prodotto" => $idProdotto,
                        "url" => $urlImmagine
                    );

                    $whereProdottiDett = array(
                        "id" => $rowProdottiDettaglio['id']
                    );

                    $dblink->update("lista_prodotti_dettaglio", $updateProdottiDett, $whereProdottiDett);
                    if(DISPLAY_DEBUG){
                        echo $dblink->get_query();
                        echo "<br />";
                    }

                }else{
                    $insertProdottiDett = array(
                        "dataagg" => date("Y-m-d H:i:s"),
                        "scrittore" => $dblink->filter("autoImport"),
                        "nome" => $dblink->filter($corso['shortname']),
                        "descrizione" => $dblink->filter($corso['summary']),
                        "stato" => ($corso['visible'] && strlen($corso['betaformazione_courseid'])>0) ? "Attivo" : "Non Attivo",
                        "gruppo" => "CORSO",
                        "codice" => $corso['betaformazione_courseid'],
                        "id_prodotto_0" => $idAbbonamento,
                        "id_prodotto" => $idProdotto,
                        "url" => $urlImmagine
                    );

                    $dblink->insert("lista_prodotti_dettaglio", $insertProdottiDett);
                    if(DISPLAY_DEBUG){
                        echo $dblink->get_query();
                        echo "<br />";
                    }
                }
            }
            if(DISPLAY_DEBUG){
                echo "<hr />";
            }
        }
        
        header("Location:$referer");
        
    break;

    case "lista_prodotti":
        //$corsi = $moodle->get_all_course();
        $corsi = $dblink->get_results("SELECT * FROM ".MOODLE_DB_NAME.".mdl_course");
        if (DISPLAY_DEBUG) {
            /*echo "<pre>";
            print_r($corsi);
            echo "<pre/><br>";*/
        }
        //die;
        foreach ($corsi as $corso) {
            if(strlen($corso['id'])<=1) continue;

            $rowProdotti = $dblink->get_row("SELECT id FROM lista_prodotti WHERE codice_esterno = '".$corso['id']."'",true);

            $rowImmagine = $dblink->get_row("SELECT * FROM ".MOODLE_DB_NAME.".mdl_files AS f LEFT JOIN ".MOODLE_DB_NAME.".mdl_files_reference AS r ON f.referencefileid = r.id WHERE f.contextid IN (SELECT id FROM ".MOODLE_DB_NAME.".mdl_context WHERE instanceid='".$corso['id']."') AND f.filesize > 0 AND f.component = 'course' AND f.filearea='overviewfiles' ORDER BY f.filename", true);
                    
            if(!empty($rowImmagine)){
                $urlImmagine = MOODLE_DOMAIN_NAME."/pluginfile.php/".$rowImmagine['contextid']."/".$rowImmagine['component']."/".$rowImmagine['filearea']."/".$rowImmagine['filename'];
            } else {
                $urlImmagine = "";
            }
            
            $nomeCategoria = $dblink->get_row("SELECT name FROM ".MOODLE_DB_NAME.".mdl_course_categories WHERE id='".$corso['category']."' LIMIT 1");
            $nomeCategoria = $nomeCategoria[0];
            
            $prezzo = $dblink->get_row("SELECT prezzo_listino FROM elenco_prodotti WHERE LCASE(codice_prodotto)=LCASE('".$corso['betaformazione_courseid']."') LIMIT 1", true);

            if($rowProdotti['id']>0){
                $update = array(
                    "dataagg" => date("Y-m-d H:i:s"),
                    "scrittore" => $dblink->filter("autoImport"),
                    "nome" => $dblink->filter($corso['shortname']),
                    "descrizione" => $dblink->filter($corso['summary']),
                    "descrizione_breve" => $dblink->filter($corso['fullname']),
                    "stato" => ($corso['visible'] && strlen($corso['betaformazione_courseid'])>0) ? "Attivo" : "Non Attivo",
                    "gruppo" => "CORSO",
                    "categoria" => $nomeCategoria,
                    "tipologia" => "e-learning",
                    "codice" => $corso['betaformazione_courseid'],
                    "codice_esterno" => $corso['id'],
                    "tempo_1" => $corso['time_to_complete'],
                    "url_immagine" => $urlImmagine
                );

                $where = array(
                    "id" => $rowProdotti['id']
                );

                $dblink->update("lista_prodotti", $update, $where);
                $idProdotto = $rowProdotti['id'];
                if(DISPLAY_DEBUG){
                    echo $dblink->get_query();
                    echo "<br />";
                }

            }else{

                $insert = array(
                    "dataagg" => date("Y-m-d H:i:s"),
                    "scrittore" => $dblink->filter("autoImport"),
                    "nome" => $dblink->filter($corso['shortname']),
                    "descrizione" => $dblink->filter($corso['summary']),
                    "descrizione_breve" => $dblink->filter($corso['fullname']),
                    "stato" => ($corso['visible'] && strlen($corso['betaformazione_courseid'])>0) ? "Attivo" : "Non Attivo",
                    "gruppo" => "CORSO",
                    "categoria" => $nomeCategoria,
                    "prezzo_pubblico" => $prezzo['prezzo_listino'],
                    "tipologia" => "e-learning",
                    "codice" => $corso['betaformazione_courseid'],
                    "codice_esterno" => $corso['id'],
                    "tempo_1" => $corso['time_to_complete'],
                    "url_immagine" => $urlImmagine
                );

                $dblink->insert("lista_prodotti", $insert);
                $idProdotto = $dblink->lastid();
                if(DISPLAY_DEBUG){
                    echo $dblink->get_query();
                    echo "<br />";
                }
            }

            $rowCorsi = $dblink->get_row("SELECT id FROM lista_corsi WHERE id_corso_moodle = '".$corso['id']."'",true);

            if($rowCorsi['id']>0){
                $updateCorsi = array(
                    "dataagg" => date("Y-m-d H:i:s"),
                    "scrittore" => $dblink->filter("autoImport"),
                    "nome_prodotto" => $dblink->filter($corso['shortname']),
                    "stato" => ($corso['visible'] && strlen($corso['betaformazione_courseid'])>0) ? "Attivo" : "Non Attivo",
                    "durata" => $corso['time_to_complete'],
                    "id_prodotto" => $idProdotto,
                    "id_corso_moodle" => $corso['id'],
                );

                $whereCorsi = array(
                    "id" => $rowCorsi['id']
                );

                $dblink->update("lista_corsi", $updateCorsi, $whereCorsi);
                $idCorso = $rowCorsi['id'];
                if(DISPLAY_DEBUG){
                    echo $dblink->get_query();
                    echo "<br />";
                }

            }else{
                $insertCorsi = array(
                    "dataagg" => date("Y-m-d H:i:s"),
                    "scrittore" => $dblink->filter("autoImport"),
                    "nome_prodotto" => $dblink->filter($corso['shortname']),
                    "stato" => ($corso['visible'] && strlen($corso['betaformazione_courseid'])>0) ? "Attivo" : "Non Attivo",
                    "durata" => $corso['time_to_complete'],
                    "id_prodotto" => $idProdotto,
                    "id_corso_moodle" => $corso['id'],
                );

                $dblink->insert("lista_corsi", $insertCorsi);
                $idCorso = $dblink->lastid();
                if(DISPLAY_DEBUG){
                    echo $dblink->get_query();
                    echo "<br />";
                }
            }

            $arrayCredits = json_decode($corso['credits']);
            if(!empty($arrayCredits)){
                foreach($arrayCredits as $arrayCredit) {
                    $idClasse = false;
                    foreach ($arrayCredit as $key => $value) {
                        $tmp = explode("_",$key);
                        $idClasse = $tmp[1];
                        $crediti = $value;
                    }
                    if($idClasse!==false && !is_array($idClasse)){

                        $rowConf = $dblink->get_row("SELECT id FROM lista_corsi_configurazioni WHERE id_corso = '".$idCorso."' AND id_prodotto = '".$idProdotto."' AND id_classe = '".$idClasse."'",true);

                        if($rowConf['id']>0){
                            /*$updateConf = array(
                                "dataagg" => date("Y-m-d H:i:s"),
                                "scrittore" => $dblink->filter("autoImport"),
                                "id_corso" => $idCorso,
                                "id_prodotto" => $idProdotto,
                                "id_classe" => $idClasse,
                                "crediti" => $crediti,
                                "avanzamento" => "80.0",
                                "stato" => "Attivo",
                            );

                            $whereConf = array(
                                "id"=>$rowConf['id']
                            );

                            $dblink->update("lista_corsi_configurazioni", $updateConf, $whereConf);
                            if(DISPLAY_DEBUG){
                                echo $dblink->get_query();
                                echo "<br />";
                            }*/
                        }else{
                            /*$insertConf = array(
                                "dataagg" => date("Y-m-d H:i:s"),
                                "scrittore" => $dblink->filter("autoImport"),
                                "id_corso" => $idCorso,
                                "id_prodotto" => $idProdotto,
                                "id_classe" => $idClasse,
                                "crediti" => $crediti,
                                "avanzamento" => "80.0",
                                "stato" => "Attivo",
                            );

                            $dblink->insert("lista_corsi_configurazioni", $insertConf);
                            if(DISPLAY_DEBUG){
                                echo $dblink->get_query();
                                echo "<br />";
                            }*/
                        }
                    }
                }
            }

            $moduli = $moodle->get_all_lesson($corso['id']);
            $ordine = 1;
            $timeCorso = 0;
            foreach ($moduli as $lezioni) {
                if(count($lezioni->modules)>=1) {
                    foreach ($lezioni->modules as $lezione) {
                        $row = $dblink->get_row('SELECT id_modulo FROM lista_corsi_dettaglio WHERE id_modulo='.$lezione->id.' AND id_corso='.$idCorso.' AND id_prodotto='.$idProdotto, true);

                        $arrayTimeModules = json_decode($corso['time_to_complete_modules']);

                        foreach ($arrayTimeModules as $arrayTimeModule) {
                            //if(strlen($corso->id)<1) continue;
                            if(DISPLAY_DEBUG){
                                echo "<li>ID_NUMBER: ".$arrayTimeModule->istance_id." == ID_CORSO: ".$lezione->instance."</li>";
                                echo "<br />";
                            }
                            if("".$arrayTimeModule->istance_id == "".$lezione->instance){
                                $timeCorso = $arrayTimeModule->value;
                                if($timeCorso>0) break;
                            }
                        }

                        if($row['id_modulo']>0){
                            $update = array(
                                "dataagg" => date("Y-m-d H:i:s"),
                                "scrittore" => $dblink->filter("autoImport"),
                                "stato" => "Attivo",
                                "ordine" => $ordine,
                                "gruppo" => "MODULO",
                                "durata" => $timeCorso,
                                "nome" => $dblink->filter($lezione->name),
                                "descrizione" => $dblink->filter($lezione->description),
                                "url" => $lezione->url,
                                "name" => $dblink->filter($lezione->name),
                                "instance" => $lezione->instance,
                                "visible" => $lezione->visible,
                                "modicon" => $lezione->modicon,
                                "modname" => $lezione->modname,
                                "modplural" => $lezione->modplural,
                                "availability" => $dblink->filter($lezione->availability),
                                "indent" => $lezione->indent
                            );
                            $where = array(
                                "id_modulo" => $lezione->id,
                                "id_corso" => $idCorso,
                                "id_prodotto" => $idProdotto
                            );
                            $dblink->update("lista_corsi_dettaglio", $update, $where);
                            if(DISPLAY_DEBUG){
                                echo $dblink->get_query();
                                echo "<br />";
                            }
                        }else{
                            $insert = array(
                                "dataagg" => date("Y-m-d H:i:s"),
                                "scrittore" => $dblink->filter("autoImport"),
                                "stato" => "Attivo",
                                "ordine" => $ordine,
                                "gruppo" => "MODULO",
                                "durata" => $timeCorso,
                                "id_modulo" => $lezione->id,
                                "id_corso" => $idCorso,
                                "id_prodotto" => $idProdotto,
                                "nome" => $dblink->filter($lezione->name),
                                "descrizione" => $dblink->filter($lezione->description),
                                "url" => $lezione->url,
                                "name" => $dblink->filter($lezione->name),
                                "instance" => $lezione->instance,
                                "visible" => $lezione->visible,
                                "modicon" => $lezione->modicon,
                                "modname" => $lezione->modname,
                                "modplural" => $lezione->modplural,
                                "availability" => $dblink->filter($lezione->availability),
                                "indent" => $lezione->indent
                            );
                            $dblink->insert("lista_corsi_dettaglio", $insert);
                            if(DISPLAY_DEBUG){
                                echo $dblink->get_query();
                                echo "<br />";
                            }
                        }
                        $timeCorso = 0;
                        $ordine++;
                    }
                }
            }

            /*$update = array(
                "codice_esterno" => $corso->id
            );
            $where = array(
                "codice" => $corso->idnumber
            );

            $dblink->update("lista_prodotti", $update, $where);
            echo $dblink->get_query();
            echo "<br />";*/
            if(DISPLAY_DEBUG){
                echo "<hr />";
            }
        }
        //echo "FINE";
        //die();
        header("Location:$referer");

        //print_r($corsi);
    break;

    case "lista_corsi_dettaglio":
        if(isset($_GET['id']) && $_GET['id']>0 && isset($_GET['idProd']) && $_GET['idProd']>0 && isset($_GET['idCorso']) && $_GET['idCorso']>0){
            $moduli = $moodle->get_all_lesson($_GET['id']);
            $corsi = $moodle->get_all_course();
            if (DISPLAY_DEBUG) {
                //print_r($corsi);
                //echo "<br />";
                //print_r($moduli);
                //die;
            }
            echo "<br />";
            $ordine = 1;
            $timeCorso = 0;
            foreach ($moduli as $lezioni) {
                if(count($lezioni->modules)>=1) {
                    foreach ($lezioni->modules as $lezione) {
                        $row = $dblink->get_row('SELECT id_modulo FROM lista_corsi_dettaglio WHERE id_modulo='.$lezione->id.' AND id_corso='.$_GET['idCorso'].' AND id_prodotto='.$_GET['idProd'], true);

                        /*foreach ($corsi as $corso) {
                            //if(strlen($corso->id)<1) continue;
                            echo "ID_NUMBER: ".$corso->id." == ID_CORSO: ".$_GET['id']."<br>";
                            if($corso->id == $_GET['id']){
                                print_r($corso->time_to_complete_modules);
                                $arrayTimelesson = json_decode($corso->time_to_complete_modules);
                                print_r($arrayTimelesson);
                                foreach ($arrayTimelesson as $arrayTime) {
                                    print_r($arrayTime);
                                    if($arrayTime->istance_id == $lezione->id){
                                        $timeCorso = $arrayTime->value;
                                        break 2;
                                    }
                                }
                                if($timeCorso>0) break;
                            }
                        }*/

                        if($row['id_modulo']>0){
                            $update = array(
                                "dataagg" => date("Y-m-d H:i:s"),
                                "scrittore" => $dblink->filter($_SESSION['cognome_nome_utente']),
                                "stato" => "Attivo",
                                "ordine" => $ordine,
                                "gruppo" => "MODULO",
                                //"numerico_1" => $timeCorso,
                                "nome" => $dblink->filter($lezione->name),
                                "descrizione" => $dblink->filter($lezione->description),
                                "url" => $lezione->url,
                                "name" => $dblink->filter($lezione->name),
                                "instance" => $lezione->instance,
                                "visible" => $lezione->visible,
                                "modicon" => $lezione->modicon,
                                "modname" => $lezione->modname,
                                "modplural" => $lezione->modplural,
                                "availability" => $dblink->filter($lezione->availability),
                                "indent" => $lezione->indent
                            );
                            $where = array(
                                "id_modulo" => $lezione->id,
                                "id_corso" => $_GET['idCorso'],
                                "id_prodotto" => $_GET['idProd']
                            );
                            $dblink->update("lista_corsi_dettaglio", $update, $where);
                            if(DISPLAY_DEBUG){
                                echo $dblink->get_query();
                                echo "<br />";
                            }
                        }else{
                            $insert = array(
                                "dataagg" => date("Y-m-d H:i:s"),
                                "scrittore" => $dblink->filter($_SESSION['cognome_nome_utente']),
                                "stato" => "Attivo",
                                "ordine" => $ordine,
                                "gruppo" => "MODULO",
                                //"numerico_1" => $timeCorso,
                                "id_modulo" => $lezione->id,
                                "id_corso" => $_GET['idCorso'],
                                "id_prodotto" => $_GET['idProd'],
                                "nome" => $dblink->filter($lezione->name),
                                "descrizione" => $dblink->filter($lezione->description),
                                "url" => $lezione->url,
                                "name" => $dblink->filter($lezione->name),
                                "instance" => $lezione->instance,
                                "visible" => $lezione->visible,
                                "modicon" => $lezione->modicon,
                                "modname" => $lezione->modname,
                                "modplural" => $lezione->modplural,
                                "availability" => $dblink->filter($lezione->availability),
                                "indent" => $lezione->indent
                            );
                            $dblink->insert("lista_corsi_dettaglio", $insert);
                            if(DISPLAY_DEBUG){
                                echo $dblink->get_query();
                                echo "<br />";
                            }
                        }
                        $timeCorso = 0;
                        $ordine++;
                        //print_r($lezione);
                    }
                }
            }

        }

        header("Location:$referer");
    break;

    /*case "lista_iscrizioni_dettaglio":
        $dettUtente = $moodle->get_activities_completion($_GET['id'], $_GET['idUtente']);
        //print_r($dettUtente);

        foreach ($dettUtente->statuses as $lezione) {
            print_r($lezione);
            $row = $dblink->get_row('SELECT id_modulo FROM lista_iscrizioni_dettaglio WHERE id_modulo='.$lezione->cmid.' AND id_corso='.$_GET['idCorso'].' AND id_iscrizione='.$_GET['idIscrizione'].' AND id_professionista='.$_GET['idProf'], true);

            if($row['id_modulo']>0){
                $update = array(
                    "dataagg" => date("Y-m-d H:i:s"),
                    "scrittore" => $dblink->filter($_SESSION['cognome_nome_utente']),
                    "stato" => $lezione->timecompleted>0 ? "Completato" : "Non Completato",
                    "id_classe" => $_GET['idClasse'],
                    "completato" => $lezione->timecompleted>0 ? "Si" : "No",
                    "data_completamento" => date("Y-m-d H:i:s",$lezione->timecompleted),
                    "cmid" => $lezione->cmid,
                    "modname" => $dblink->filter($lezione->modname),
                    "instance" => $lezione->instance,
                    "state" => $lezione->state,
                    "timecompleted" => $lezione->timecompleted,
                    "tracking" => $lezione->tracking
                );
                $where = array(
                    "id_iscrizione" => $_GET['idIscrizione'],
                    "id_modulo" => $lezione->cmid,
                    "id_corso" => $_GET['idCorso'],
                    "id_professionista" => $_GET['idProf']
                );
                $dblink->update("lista_iscrizioni_dettaglio", $update, $where);
                echo $dblink->get_query();
                echo "<br />";
            }else{
                $insert = array(
                    "dataagg" => date("Y-m-d H:i:s"),
                    "scrittore" => $dblink->filter($_SESSION['cognome_nome_utente']),
                    "stato" => $lezione->timecompleted>0 ? "Completato" : "Non Completato",
                    "id_iscrizione" => $_GET['idIscrizione'],
                    "id_corso" => $_GET['idCorso'],
                    "id_classe" => $_GET['idClasse'],
                    "id_professionista" => $_GET['idProf'],
                    "id_modulo" => $lezione->cmid,
                    "completato" => $lezione->timecompleted>0 ? "Si" : "No",
                    "data_completamento" => date("Y-m-d H:i:s",$lezione->timecompleted),
                    "cmid" => $lezione->cmid,
                    "modname" => $dblink->filter($lezione->modname),
                    "instance" => $lezione->instance,
                    "state" => $lezione->state,
                    "timecompleted" => $lezione->timecompleted,
                    "tracking" => $lezione->tracking
                );
                $dblink->insert("lista_iscrizioni_dettaglio", $insert);
                echo $dblink->get_query();
                echo "<br />";
            }
        }
        //print_r($lezione);

    break;*/

    case "lista_prodotti_dettaglio":
        if(isset($_GET['id']) && $_GET['id']>0 && isset($_GET['idProd']) && $_GET['idProd']>0){
            $moduli = $moodle->get_all_lesson($_GET['id']);
            $corsi = $moodle->get_all_course();
            if(DISPLAY_DEBUG){
                print_r($corsi);
            }
            $ordine = 1;
            foreach ($moduli as $lezioni) {
                if(count($lezioni->modules)>=1) {
                    foreach ($lezioni->modules as $lezione) {
                        $row = $dblink->get_row('SELECT id_modulo FROM lista_prodotti_dettaglio WHERE id_modulo='.$lezione->id.' AND id_prodotto='.$_GET['idProd'], true);

                        $timeCorso = 0;

                        foreach ($corsi as $corso) {
                            if(strlen($corso->id)<1) continue;
                            if (DISPLAY_DEBUG) echo "ID_NUMBER: ".$corso->id." == ID_CORSO: ".$_GET['id']."<br>";
                            if($corso->id == $_GET['id']){
                                if (DISPLAY_DEBUG) print_r($corso->time_to_complete_modules);
                                $arrayTimelesson = json_decode($corso->time_to_complete_modules);
                                if (DISPLAY_DEBUG) print_r($arrayTimelesson);
                                foreach ($arrayTimelesson as $arrayTime) {
                                    if (DISPLAY_DEBUG) print_r($arrayTime);
                                    if($arrayTime->istance_id == $lezione->id){
                                        $timeCorso = $arrayTime->value;
                                        break 2;
                                    }
                                }
                            }
                        }

                        if($row['id_modulo']>0){
                            $update = array(
                                "dataagg" => date("Y-m-d H:i:s"),
                                "scrittore" => $dblink->filter($_SESSION['cognome_nome_utente']),
                                "stato" => "Attivo",
                                "ordine" => $ordine,
                                "gruppo" => "MODULO",
                                "numerico_1" => $timeCorso,
                                "nome" => $dblink->filter($lezione->name),
                                "descrizione" => $dblink->filter($lezione->description),
                                "url" => $lezione->url,
                                "name" => $dblink->filter($lezione->name),
                                "instance" => $lezione->instance,
                                "visible" => $lezione->visible,
                                "modicon" => $lezione->modicon,
                                "modname" => $lezione->modname,
                                "modplural" => $lezione->modplural,
                                "availability" => $dblink->filter($lezione->availability),
                                "indent" => $lezione->indent
                            );
                            $where = array(
                                "id_modulo" => $lezione->id,
                                "id_prodotto" => $_GET['idProd']
                            );
                            $dblink->update("lista_prodotti_dettaglio", $update, $where);
                            if(DISPLAY_DEBUG){
                                echo $dblink->get_query();
                                echo "<br />";
                            }
                        }else{
                            $insert = array(
                                "dataagg" => date("Y-m-d H:i:s"),
                                "scrittore" => $dblink->filter($_SESSION['cognome_nome_utente']),
                                "stato" => "Attivo",
                                "ordine" => $ordine,
                                "gruppo" => "MODULO",
                                "numerico_1" => $timeCorso,
                                "id_modulo" => $lezione->id,
                                "id_prodotto" => $_GET['idProd'],
                                "nome" => $dblink->filter($lezione->name),
                                "descrizione" => $dblink->filter($lezione->description),
                                "url" => $lezione->url,
                                "name" => $dblink->filter($lezione->name),
                                "instance" => $lezione->instance,
                                "visible" => $lezione->visible,
                                "modicon" => $lezione->modicon,
                                "modname" => $lezione->modname,
                                "modplural" => $lezione->modplural,
                                "availability" => $dblink->filter($lezione->availability),
                                "indent" => $lezione->indent
                            );
                            $dblink->insert("lista_prodotti_dettaglio", $insert);
                            if(DISPLAY_DEBUG){
                                echo $dblink->get_query();
                                echo "<br />";
                            }
                        }
                        $ordine++;
                        if(DISPLAY_DEBUG){
                            print_r($lezione);
                        }
                    }
                }
            }

        }

        header("Location:$referer");
    break;

    default:
    break;
}

function objToArray($obj, &$arr){

    if(!is_object($obj) && !is_array($obj)){
        $arr = $obj;
        return $arr;
    }

    foreach ($obj as $key => $value)
    {
        if (!empty($value))
        {
            $arr[$key] = array();
            objToArray($value, $arr[$key]);
        }
        else
        {
            $arr[$key] = $value;
        }
    }
    return $arr;
}
?>
