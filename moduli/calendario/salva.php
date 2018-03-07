<?php
include_once('../../config/connDB.php');
include_once(BASE_ROOT.'config/confAccesso.php');;

$referer = recupera_referer();

if(isset($_GET['fn'])){
    switch ($_GET['fn']) {
        case "inserisciCommerciale":
            $ok = true;
            //$dblink->begin();
            
            $row_0001 = $dblink->get_row("SELECT id as id_agente, CONCAT(cognome,' ', nome) as cognome_nome_agente FROM lista_password WHERE id='".$_POST['id_commerciale']."'", true);
            $row_0001["dataagg"] = date("Y-m-d H:i:s");
            $row_0001["scrittore"] = $dblink->filter($_SESSION['cognome_nome_utente']);
            /*if($_POST['txt_id_professionista']>0){
                //$ok = $ok && $dblink->update("lista_professionisti", $row_0001, array("id"=>$_POST['txt_id_professionista']));
            }else{ 
                //SE NON E PRESENTE IN DB SETTA STATO A: MAI CONTATTATO
                //$labelStato = "Mai Contattato";
            }*/
            
            $row_0002 = $dblink->get_row("SELECT stato FROM calendario WHERE id='".$_POST['txt_id_calendario']."'", true);
            if($row_0002['stato']=="In Attesa di Controllo"){
                $dblink->update("calendario", array("stato"=>"Mai Contattato"), array("id"=>$_POST['txt_id_calendario']));
            }
            
            $ok = $ok && $dblink->update("calendario", array("dataagg" => date("Y-m-d H:i:s"),"scrittore" => $dblink->filter($_SESSION['cognome_nome_utente']), "id_agente"=>$row_0001["id_agente"], "destinatario"=>$row_0001["cognome_nome_agente"]), array("id"=>$_POST['txt_id_calendario']));
            if($ok){
                $ok = "OK";
                //$dblink->commit();
            }else{
                $ok = "KO";
                //$dblink->rollback();
            }
            echo "data: {\"status\": $ok";
            echo "}";
        break;
        
        case "prendiInCaricoRichiesta":
            $ok = true;
            $id_calendario = $_GET['id'];
            $id_agente = $_GET['idAgenteNew'];
            $id_agente_old = $_GET['idAgenteOld'];
            $id_professionista = $_GET['idProf'];
            
            if($id_calendario>0 && $id_agente>0 && $id_agente_old>0){
            
                $row_0002 = $dblink->get_row("SELECT id as id_agente, CONCAT(cognome,' ', nome) as destinatario FROM lista_password WHERE id='".$id_agente_old."'", true);
                
                $ok = $ok && $dblink->duplicateWhere("calendario", "id='$id_calendario'", 1);
                //echo $dblink->get_query();
                //echo "<br />";
                $insetIdCalendario = $dblink->lastid();

                $updateCal = array(
                    "dataagg" => date("Y-m-d H:i:s"),
                    "scrittore" => $dblink->filter($_SESSION['cognome_nome_utente']),
                    "etichetta" => "Nuova Richiesta Trasferita",
                    "stato" => "Trasferito",
                    "messaggio" => "CONCAT('Presa in carico da: <b>".$dblink->filter($_SESSION['cognome_nome_utente'])."</b>\\nAgente precedente: <b><i>".$dblink->filter($row_0002['destinatario'])."</i></b>\\n\\nMESSAGGIO ORIGINALE:\\n', messaggio)"
                );

                $ok = $ok && $dblink->update("calendario", $updateCal, array("id"=>$insetIdCalendario));
                //echo $dblink->get_query();
                //echo "<br />";
                
                $row_0001 = $dblink->get_row("SELECT id as id_agente, CONCAT(cognome,' ', nome) as destinatario FROM lista_password WHERE id='".$id_agente."'", true);
                $row_0001["dataagg"] = date("Y-m-d H:i:s");
                $row_0001["scrittore"] = $dblink->filter($_SESSION['cognome_nome_utente']);

                $ok = $ok && $dblink->update("calendario", $row_0001, array("id"=>$id_calendario));

                //$ok = $ok && $dblink->update("lista_preventivi", array("dataagg" => date("Y-m-d H:i:s"), "scrittore" => $dblink->filter($_SESSION['cognome_nome_utente']), "id_calendario"=>$insetIdCalendario, "id_agente"=>$id_agente), array("id_calendario"=>$id_calendario));
                //$ok = $ok && $dblink->update("lista_preventivi_dettaglio", array("dataagg" => date("Y-m-d H:i:s"), "scrittore" => $dblink->filter($_SESSION['cognome_nome_utente']), "id_calendario"=>$insetIdCalendario), array("id_calendario"=>$id_calendario));
                //echo $dblink->get_query();
                //echo "<br />";
                if($ok) header("Location:".BASE_URL."/moduli/anagrafiche/dettaglio_tab.php?tbl=calendario&id=$id_calendario&res=4");
                else header("Location:".$referer."&res=0");
            }elseif($id_calendario>0 && $id_agente>0){
                
                $row_0001 = $dblink->get_row("SELECT id as id_agente, CONCAT(cognome,' ', nome) as destinatario FROM lista_password WHERE id='".$id_agente."'", true);
                $row_0001["dataagg"] = date("Y-m-d H:i:s");
                $row_0001["scrittore"] = $dblink->filter($_SESSION['cognome_nome_utente']);
                $row_0001["stato"] = "Mai Contattato";

                $ok = $ok && $dblink->update("calendario", $row_0001, array("id"=>$id_calendario));
                if($ok) header("Location:".BASE_URL."/moduli/anagrafiche/dettaglio_tab.php?tbl=calendario&id=$id_calendario&res=4");
                else header("Location:".$referer."&res=0");
            }else{ 
                header("Location:".$referer."&res=0");
            }
            
        break;
        
        case "associaCommerciale":
            $ok = true;
            $ArrayIdCalendario = strlen($_POST['idCal'])>0 ? explode(":",$_POST['idCal']) : 0;
            $id_agente = strlen($_POST['id_commerciale'])>0 ? $_POST['id_commerciale'] : 0;
            
            if(is_array($ArrayIdCalendario) && $id_agente>0){
            
                foreach($ArrayIdCalendario as $id_calendario) {
                    
                    $row_0001 = $dblink->get_row("SELECT id as id_agente, CONCAT(cognome,' ', nome) as cognome_nome_agente FROM lista_password WHERE id='".$id_agente."'", true);
                    $row_0001["dataagg"] = date("Y-m-d H:i:s");
                    $row_0001["scrittore"] = $dblink->filter($_SESSION['cognome_nome_utente']);
                    $row_0002 = $dblink->get_row("SELECT stato FROM calendario WHERE id='".$id_calendario."'", true);
                    if($row_0002['stato']=="In Attesa di Controllo"){
                        $dblink->update("calendario", array("stato"=>"Mai Contattato"), array("id"=>$id_calendario));
                    }
                    $ok = $ok && $dblink->update("calendario", array("dataagg" => date("Y-m-d H:i:s"),"scrittore" => $dblink->filter($_SESSION['cognome_nome_utente']), "id_agente"=>$row_0001["id_agente"], "destinatario"=>$row_0001["cognome_nome_agente"]), array("id"=>$id_calendario));
                }
                if($ok){
                    echo "OK:OK";
                }else{
                    echo "KO:KO";
                }
            }else{
                echo "KO2:KO2";
            }
        break;
        
        case "associaProdotti":
            $ok = true;
            $ArrayIdCalendario = strlen($_POST['idCal'])>0 ? explode(":",$_POST['idCal']) : 0;
            $id_prodotto = strlen($_POST['id_prodotto'])>0 ? $_POST['id_prodotto'] : 0;
            
            if(is_array($ArrayIdCalendario) && $id_prodotto>0){
            
                foreach($ArrayIdCalendario as $id_calendario) {
                    $ok = $ok && $dblink->update("calendario", array("dataagg" => date("Y-m-d H:i:s"),"scrittore" => $dblink->filter($_SESSION['cognome_nome_utente']), "id_prodotto"=>$id_prodotto), array("id"=>$id_calendario));
                }
                if($ok){
                    echo "OK:OK";
                }else{
                    echo "KO:KO";
                }
            }else{
                echo "KO2:KO2";
            }
        break;

        case 'cercaProfessionista':
            $ok = true;
            
            if(strpos($_GET['key_search']," ")){
                $key=explode(" ", $_GET['key_search']);
            }else{
                $key=$_GET['key_search'];
            }
            $array = array();
            $sql_01 = "SELECT id, nome, cognome, CONCAT(nome, ' ', cognome) AS ragione_sociale, codice_fiscale, telefono, email FROM lista_professionisti WHERE 1 ";
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
                
                $sql_02 = "SELECT * FROM calendario WHERE id_professionista = '".$row['id']."' AND etichetta LIKE 'Nuova Richiesta' AND (stato LIKE 'Richiamare' OR stato LIKE 'Mai Contattato' OR stato LIKE 'In Attesa di Controllo') ORDER BY dataagg DESC";
                $rows_02 = $dblink->get_results($sql_02);
                
                $array[] = array(
                    "id_professionista" => $row['id'],
                    "ragione_sociale" => $row['ragione_sociale']." - C.F.: ".$row['codice_fiscale']." - Tel: ".$row['telefono'],
                    "codice_fiscale" => $row['codice_fiscale'],
                    "nome" => $row['nome'],
                    "cognome" => $row['cognome'],
                    "telefono" => $row['telefono'],
                    "email" => $row['email'],
                    "avviso" => $rows_02,
                    "id_agente" => $_SESSION['id_utente'],
                    "livello_agente" => $_SESSION['livello_utente']
                    );
            }
            
            if(empty($array)){
                $array = array("codice_fiscale" => $_GET['key_search']);
            }
            
            echo json_encode($array);
        break;
        
        case "nuovaRichiestaCalendario":
            $ok = true;
            $arrayCampi = $_POST;
            
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
            
            $count = 0;
            
            foreach($tuttiCampi as $record){
                $count++;
                /*foreach($record as $nomi_colonne => $valore){
                    echo '<li>$nomi_colonne = '.$nomi_colonne.' / $valore = '.$valore.'</li>';
                }*/
            }
            
            $numCheckTel = $dblink->num_rows("SELECT * FROM lista_numeri_telefono_controlli WHERE telefono = '".$tuttiCampi['calendario']['campo_4']."'");
            if($numCheckTel>0){
                $tuttiCampi['calendario']['campo_4'] = "";
            }
            
            if(strlen($tuttiCampi['calendario']['campo_3'])==16 && $tuttiCampi['calendario']['id_professionista']==0){
                $sql_00001 = "SELECT id FROM lista_professionisti WHERE codice_fiscale='".$tuttiCampi['calendario']['campo_3']."'";
                $row_00001 = $dblink->get_row($sql_00001,true);
                if($row_00001['id']>0){
                    $id_professionista = $row_00001['id'];
                    //ragione_sociale, forma_giuridica, partita_iva, codice_fiscale, indirizzo, cap, citta, provincia, nazione, telefono, cellulare, web, email, settore, categoria
                    $sql_00002 = "SELECT id_azienda FROM `matrice_aziende_professionisti` WHERE id_professionista='".$id_professionista."'";
                    $row_00002 = $dblink->get_row($sql_00002, true);

                    if($row_00002['id_azienda']>0){
                        $tuttiCampi['calendario']['id_azienda'] = $row_00002['id_azienda'];
                    } else { $tuttiCampi['calendario']['id_azienda'] = 0; }
                    
                    $tuttiCampi['calendario']['id_professionista'] = $id_professionista;
                }else{
                    $insert = array(
                        "dataagg" => date("Y-m-d H:i:s"),
                        "scrittore"=>$dblink->filter($_SESSION['cognome_nome_utente']),
                        "codice_fiscale"=>$tuttiCampi['calendario']['campo_3'],
                        "nome"=>$tuttiCampi['calendario']['campo_1'],
                        "cognome"=>$tuttiCampi['calendario']['campo_2'],
                        "telefono"=>$tuttiCampi['calendario']['campo_4'],
                        "email"=>$tuttiCampi['calendario']['campo_5']
                    );
                    
                    $ok = $ok && $dblink->insert("lista_professionisti",$insert);
                    $id_professionista = $dblink->lastid();
                    if($id_professionista>0){
                        $tuttiCampi['calendario']['id_professionista'] = $id_professionista;
                        
                        $sql_00003 = "SELECT id_azienda FROM `matrice_aziende_professionisti` WHERE id_professionista='".$id_professionista."'";
                        $row_00003 = $dblink->get_row($sql_00003, true);

                        if($row_00003['id_azienda']>0){
                            $tuttiCampi['calendario']['id_azienda'] = $row_00003['id_azienda'];
                        } else { $tuttiCampi['calendario']['id_azienda'] = 0; }
                    }
                }
            }
            
            if(strlen($tuttiCampi['calendario']['campo_4'])>0 && $tuttiCampi['calendario']['id_professionista']==0){
                //VERIFICO TELEFONO
                $sql_00005 = "SELECT * FROM lista_professionisti "
                        . "WHERE telefono='".$tuttiCampi['calendario']['campo_4']."' "
                        . "OR cellulare = '".$tuttiCampi['calendario']['campo_4']."'";
                $row_00005 = $dblink->get_row($sql_00005,true);
                if($row_00005['id']>0){
                    $sql_00002 = "SELECT id_azienda FROM `matrice_aziende_professionisti` WHERE id_professionista='".$row_00005['id']."'";
                    $row_00002 = $dblink->get_row($sql_00002, true);

                    if($row_00002['id_azienda']>0){
                        $tuttiCampi['calendario']['id_azienda'] = $row_00002['id_azienda'];
                    } else { $tuttiCampi['calendario']['id_azienda'] = 0; }
                    
                    $tuttiCampi['calendario']['id_professionista'] = $row_00005['id'];
                }
            }
            
            if(strlen($tuttiCampi['calendario']['campo_5'])>0 && $tuttiCampi['calendario']['id_professionista']==0){
                //VERIFICO EMAIL
                $sql_00007 = "SELECT * FROM lista_professionisti WHERE email='".$tuttiCampi['calendario']['campo_5']."'";
                $row_00007 = $dblink->get_row($sql_00007,true);
                if($row_00007['id_professionista']>0){
                    $sql_00008 = "SELECT id_azienda FROM `matrice_aziende_professionisti` WHERE id_professionista='".$row_00007['id_professionista']."'";
                    $row_00008 = $dblink->get_row($sql_00008, true);

                    if($row_00008['id_azienda']>0){
                        $tuttiCampi['calendario']['id_azienda'] = $row_00008['id_azienda'];
                    } else { $tuttiCampi['calendario']['id_azienda'] = 0; }

                    $tuttiCampi['calendario']['id_professionista'] = $row_00007['id_professionista'];
                }else{
                    $sql_00004 = "SELECT * FROM lista_indirizzi_email WHERE email='".$tuttiCampi['calendario']['campo_5']."'";
                    $row_00004 = $dblink->get_row($sql_00004,true);
                    if($row_00004['id_professionista']>0){
                        $sql_00002 = "SELECT id_azienda FROM `matrice_aziende_professionisti` WHERE id_professionista='".$row_00004['id_professionista']."'";
                        $row_00002 = $dblink->get_row($sql_00002, true);

                        if($row_00002['id_azienda']>0){
                            $tuttiCampi['calendario']['id_azienda'] = $row_00002['id_azienda'];
                        } else { $tuttiCampi['calendario']['id_azienda'] = 0; }

                        $tuttiCampi['calendario']['id_professionista'] = $row_00004['id_professionista'];
                    }
                }
            }
            
            if($tuttiCampi['calendario']['id_professionista'] > 0){
                $sql_00003 = "SELECT id_azienda FROM `matrice_aziende_professionisti` WHERE id_professionista='".$tuttiCampi['calendario']['id_professionista']."'";
                $row_00003 = $dblink->get_row($sql_00003, true);

                if($row_00003['id_azienda']>0){
                    $tuttiCampi['calendario']['id_azienda'] = $row_00003['id_azienda'];
                } else { $tuttiCampi['calendario']['id_azienda'] = 0; }
            }
            
            $tuttiCampi['calendario']['dataagg'] = "NOW()";
            $tuttiCampi['calendario']['scrittore'] = $dblink->filter($_SESSION['cognome_nome_utente']);
            
            $rowCampagna = $dblink->get_row("SELECT id_tipo_marketing, nome, id_prodotto FROM lista_campagne WHERE id = '".$tuttiCampi['calendario']['id_campagna']."'", true);
            $rowMarketing = $dblink->get_row("SELECT nome FROM lista_tipo_marketing WHERE id = '".$rowCampagna['id_tipo_marketing']."'", true);
            
            $tmpData = explode("-",$tuttiCampi['calendario']['data']);
            $tuttiCampi['calendario']['giorno'] = $tmpData[2];
            $tuttiCampi['calendario']['mese'] = $tmpData[1];
            $tuttiCampi['calendario']['anno'] = $tmpData[0];
            $tuttiCampi['calendario']['datainsert'] = "NOW()";
            $tuttiCampi['calendario']['orainsert'] = "NOW()";
            $tuttiCampi['calendario']['mittente'] = $tuttiCampi['calendario']['campo_2']." ".$tuttiCampi['calendario']['campo_1'];
            $tuttiCampi['calendario']['priorita'] = "Normale";
            $tuttiCampi['calendario']['notifica_email'] = "Si";
            $tuttiCampi['calendario']['notifica_sms'] = "No";
            $tuttiCampi['calendario']['id_prodotto'] = $rowCampagna['id_prodotto'];
            $tuttiCampi['calendario']['id_tipo_marketing'] = $rowCampagna['id_tipo_marketing'];
            $tuttiCampi['calendario']['oggetto'] = "Richiesta ".$rowMarketing['nome']." del ".GiraDataOra($tuttiCampi['calendario']['data'])." ora ".$tuttiCampi['calendario']['ora'];
            $tuttiCampi['calendario']['nome'] = $tuttiCampi['calendario']['campo_1'];
            $tuttiCampi['calendario']['cognome'] = $tuttiCampi['calendario']['campo_2'];
            $tuttiCampi['calendario']['telefono'] = $tuttiCampi['calendario']['campo_4'];
            $tuttiCampi['calendario']['email'] = $tuttiCampi['calendario']['campo_5'];
            $tuttiCampi['calendario']['campo_6'] = $rowMarketing['nome'];
            $tuttiCampi['calendario']['tipo_marketing'] = $rowMarketing['nome'];
            $tuttiCampi['calendario']['campo_7'] = $rowCampagna['nome'];

            if($_SESSION['livello_utente']=='commerciale'){
                $tuttiCampi['calendario']['id_agente'] = $_SESSION['id_utente'];
                $tuttiCampi['calendario']['destinatario'] = $dblink->filter($_SESSION['cognome_nome_utente']);
            }else{
                $tuttiCampi['calendario']['id_agente'] = 0;
                if($tuttiCampi['calendario']['id_professionista']>0){
                    $tuttiCampi['calendario']['stato'] = "In Attesa di Controllo";
                }else{
                    $tuttiCampi['calendario']['stato'] = "In Attesa di Controllo";
                }
            }

            $ok = $dblink->insert("calendario", $tuttiCampi['calendario']);  
            //echo $dblink->get_query();
            
            $idCalendario = $dblink->lastid();
            
            $idCal = controllaRichiesteMultiple($idCalendario);
            
            if($ok===true) header("Location:".BASE_URL."/moduli/anagrafiche/dettaglio_tab.php?tbl=calendario&id=".$idCal."&res=1");
            //else if($ok===2) header("Location:".BASE_URL."/moduli/anagrafiche/dettaglio_tab.php?tbl=calendario&id=".$idCalendario."&res=1");
            else header("Location:".$referer."&res=0");
            
        break;
        
        case "nuovaNotaCalendario":
            $ok = true;
            $arrayCampi = $_POST;
            //print_r($arrayCampi);
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
            
            $count = 0;
            
            foreach($tuttiCampi as $record){
                $count++;
                /*foreach($record as $nomi_colonne => $valore){
                    echo '<li>$nomi_colonne = '.$nomi_colonne.' / $valore = '.$valore.'</li>';
                }*/
            }
            
            $idCal = $tuttiCampi['calendario']['id'];
            
            if($tuttiCampi['calendario']['id'] > 0){
                $rowMessaggio = $dblink->get_row("SELECT messaggio FROM calendario WHERE id = '".$tuttiCampi['calendario']['id']."'", true);
                
                $messaggio = "Nuova Nota del ".date("d-m-Y"). " di " .$dblink->filter($_SESSION['cognome_nome_utente']). "\\n".$tuttiCampi['calendario']['messaggio']."\\n\\n".$rowMessaggio['messaggio'];
                
                $ok = $dblink->update("calendario", array("dataagg" => date("Y-m-d H:i:s"),"scrittore" => $dblink->filter($_SESSION['cognome_nome_utente']),"messaggio" => $messaggio),array("id" => $tuttiCampi['calendario']['id']));  
            }
            
            if($ok===true) header("Location:".BASE_URL."/moduli/anagrafiche/dettaglio_tab.php?tbl=calendario&id=".$idCal."&res=1");
            //else if($ok===2) header("Location:".BASE_URL."/moduli/anagrafiche/dettaglio_tab.php?tbl=calendario&id=".$idCalendario."&res=1");
            else header("Location:".BASE_URL."/moduli/anagrafiche/dettaglio_tab.php?tbl=calendario&id=".$idCal."&res=0");
            
        break;
        
        case "chiudiRichiesta":
            $ok = true;
            $idPreventivo =  $_GET['idPrev'];
            $idCalendario =  $_GET['id'];
            
            if($idPreventivo>0){
                //$ok = $ok && $dblink->delete("lista_preventivi",array("id"=>$idPreventivo));
                //$ok = $ok && $dblink->delete("lista_preventivi_dettaglio",array("id_preventivo"=>$idPreventivo));
            }
            if($idCalendario>0){
                $labelStato = "Chiusa In Attesa di Controllo";
                $ok = $ok && $dblink->update("calendario", array("dataagg" => date("Y-m-d H:i:s"),"scrittore" => $dblink->filter($_SESSION['cognome_nome_utente']),"stato"=>"$labelStato"), array("id"=>$idCalendario));
            }
            if($ok===true && $idCalendario>0) header("Location:".$referer."&res=1");
            else header("Location:".$referer."&res=0");
        break;
        
        case "chiudiRichiestaConferma":
            $ok = true;
            $idPreventivo =  $_GET['idPrev'];
            $idCalendario =  $_GET['id'];
            
            if($idPreventivo>0){
                $ok = $ok && $dblink->delete("lista_preventivi",array("id"=>$idPreventivo));
                $ok = $ok && $dblink->delete("lista_preventivi_dettaglio",array("id_preventivo"=>$idPreventivo));
            }
            if($idCalendario>0){
                $labelStato = "Fatto";
                $ok = $ok && $dblink->update("calendario", array("dataagg" => date("Y-m-d H:i:s"),"scrittore" => $dblink->filter($_SESSION['cognome_nome_utente']),"stato"=>"$labelStato"), array("id"=>$idCalendario));
            }
            if($ok===true && $idCalendario>0) header("Location:".$referer."&res=1");
            else header("Location:".$referer."&res=0");
        break;
        
        case "salvaNomeObiezioneInCalendario":
            $ok = true;
            $idPreventivo =  $_POST['txt_id_preventivo'];
            $idCalendario =  $_POST['txt_id_calendario'];
            $idObiezione =  $_POST['txt_id_obiezione'];
            
            if($idObiezione > 0){
                $nomeObiezione = $dblink->get_field("SELECT nome FROM lista_obiezioni WHERE id = '$idObiezione'");

                if($idPreventivo>0){
                    $ok = $ok && $dblink->update("lista_preventivi",array("dataagg" => date("Y-m-d H:i:s"),"scrittore" => $dblink->filter($_SESSION['cognome_nome_utente']),"nome_obiezione"=> $dblink->filter($nomeObiezione), "id_obiezione"=>"$idObiezione"),array("id"=>$idPreventivo));
                    //echo $dblink->get_query();
                }
                if($idCalendario>0){
                    $ok = $ok && $dblink->update("calendario", array("dataagg" => date("Y-m-d H:i:s"),"scrittore" => $dblink->filter($_SESSION['cognome_nome_utente']),"nome_obiezione"=> $dblink->filter($nomeObiezione) , "id_obiezione"=>"$idObiezione"), array("id"=>$idCalendario));
                    //echo $dblink->get_query();
                }
                //die();
                if($ok===true) header("Location:".$referer."&res=1");
                else header("Location:".$referer."&res=0");
            }else{
                header("Location:".$referer."&res=0");
            }
        break;
        
        case "chiudiNegativo":
            $ok = true;
            $ArrayIdCalendario = strlen($_POST['idCal'])>0 ? explode(":",$_POST['idCal']) : 0;
            $id_obiezione = strlen($_POST['idObiezione'])>0 ? $_POST['idObiezione'] : 0;
            
            if(is_array($ArrayIdCalendario) && $id_obiezione>0){
            
                foreach($ArrayIdCalendario as $id_calendario) {
                    
                    
                    $nomeObiezione = $dblink->get_field("SELECT nome FROM lista_obiezioni WHERE id = '$id_obiezione'");
            
                    $rowCalendario = $dblink->get_row("SELECT id_professionista, id_azienda, id_agente, id_campagna, id_preventivo FROM calendario WHERE id='$id_calendario'", true);

                    $id_preventivo = $rowCalendario['id_preventivo'];
                    
                    if($id_preventivo > 0){
                    
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
                    }
                    
                }
                if($ok){
                    echo "OK:OK";
                }else{
                    echo "KO:KO";
                }
            }else{
                echo "KO2:KO2";
            }
        break;
    
        default:
            echo "data: {\"status\": ERRORE";
            echo "}";
        break;
    }
}
?>