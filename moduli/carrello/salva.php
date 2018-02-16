<?php
include_once('../../config/connDB.php');
include_once(BASE_ROOT . 'config/confAccesso.php');

if(isset($_GET['fn'])){
    switch ($_GET['fn']) {
        case "aggiungiCarrelloWeb":
           //print_r($_GET);
           $betaformazione_utente_carrello = $_GET['betaformazione_utente_carrello'];
           $idProdotto = $_GET['idProdotto'];
           if(isset($_GET['r'])){
               $sql_r0001 = "SELECT id FROM lista_ordini WHERE campo_20='".$betaformazione_utente_carrello."' AND (stato='In Corso' OR stato='In Attesa')";
               $rIdOrdine = $dblink->get_field($sql_r0001);
               
               $dblink->deleteWhere("lista_ordini_dettaglio", "id_ordine = '$rIdOrdine'");
               
               $redirect = base64_decode($_GET['r']);
           }else{
               $redirect = false;
           }
           if(isset($_GET['idCampagna']) && !empty($_GET['idCampagna'])){
                $idCampagna = $_GET['idCampagna'];
           }else{
                $idCampagna = 169;
           }
           $valore_del_cookie = $betaformazione_utente_carrello;
           $sql_0001 = "SELECT id FROM lista_ordini WHERE campo_20='".$valore_del_cookie."' AND stato='In Corso'";
           $conteggio_0001 = $dblink->num_rows($sql_0001);
           if($conteggio_0001<=0){
                //echo '<LI>$conteggio_0001 = '.$conteggio_0001.'</LI>';
                //echo '<LI>$valore_del_cookie = '.$valore_del_cookie.'</LI>';
                $sql_0002 = "INSERT INTO lista_ordini (id, dataagg, data_creazione, sezionale, campo_20, stato, id_campagna) VALUES ('', NOW(), NOW(), '01', '".$valore_del_cookie."', 'In Corso','".$idCampagna."')";
                $dblink->query($sql_0002);
                $idOrdine = $dblink->lastid();
           }else{
                //echo '<LI>$conteggio_0001 = '.$conteggio_0001.'</LI>';
                //echo '<LI>$valore_del_cookie = '.$valore_del_cookie.'</LI>';   
                $idOrdine = $dblink->get_field($sql_0001);
           }
           
           
           $sql_0001 = "SELECT id FROM lista_ordini WHERE campo_20='".$valore_del_cookie."' AND stato='In Corso'";
           $idOrdine = $dblink->get_field($sql_0001);
            
           $sql_0004 = "INSERT INTO lista_ordini_dettaglio (id, id_ordine,dataagg, id_prodotto, quantita, stato) VALUES ('', '".$idOrdine."', NOW(), '".$idProdotto."',1, 'In Corso')";
           $rs_0004 = $dblink->query($sql_0004);
           //echo "<br>";
           $idOrdineDettaglio = $dblink->lastid();
           if($rs_0004){
            //StampaSQL("SELECT * FROM lista_ordini WHERE campo_20='".$valore_del_cookie."'","","");
                $sql_0005 = "UPDATE `lista_ordini_dettaglio`, lista_prodotti SET lista_ordini_dettaglio.nome_prodotto = lista_prodotti.nome, lista_ordini_dettaglio.descrizione_prodotto = lista_prodotti.descrizione, 
                lista_ordini_dettaglio.codice_prodotto = lista_prodotti.codice,
                lista_ordini_dettaglio.prezzo_prodotto = lista_prodotti.prezzo_pubblico,
                lista_ordini_dettaglio.quantita = 1,
                lista_ordini_dettaglio.iva_prodotto = lista_prodotti.iva
                WHERE lista_ordini_dettaglio.id_prodotto = lista_prodotti.id AND lista_ordini_dettaglio.prezzo_prodotto <= 0";
                $rs_0005 = $dblink->query($sql_0005);
                //echo "<br>";
                if($idCampagna > 0 && $idCampagna != 169){
                    $resPromo = $dblink->get_row("SELECT * FROM lista_campagne WHERE id='".$idCampagna."'",true);
                    $sql_0006 = "UPDATE `lista_ordini_dettaglio` SET 
                                lista_ordini_dettaglio.prezzo_prodotto = '".$resPromo['prezzo_sconto']."',
                                lista_ordini_dettaglio.quantita = 1
                                WHERE lista_ordini_dettaglio.id = '".$idOrdineDettaglio."' ";
                                $dblink->query($sql_0006);
                }
                
                if($redirect != false){
                    $tmpDati = explode("|", $redirect);
                    $redirect = $tmpDati[0];
                    $chiaveValore = explode("&", $redirect);
                    $valProf = explode("=", $chiaveValore[0]);
                    $valAzienda = explode("=", $chiaveValore[1]);
                    $valPrezzo = $tmpDati[1];
                    $sql_1005 = "UPDATE lista_ordini SET id_professionista = '".$valProf[1]."', id_azienda='".$valAzienda[1]."'
                    WHERE campo_20='".$valore_del_cookie."' AND (stato='In Corso' OR stato='In Attesa')";
                    $rs_1005 = $dblink->query($sql_1005);
                    
                    if(!empty($valPrezzo) && $valPrezzo > 0){
                        $sql_1006 = "UPDATE `lista_ordini_dettaglio` SET 
                                    lista_ordini_dettaglio.prezzo_prodotto = '".$valPrezzo."',
                                    lista_ordini_dettaglio.quantita = 1
                                    WHERE lista_ordini_dettaglio.id = '".$idOrdineDettaglio."' ";
                        $dblink->query($sql_1006);
                    }
                    
                    header('Location:'.WP_DOMAIN_NAME.''.$redirect);
                }else{
                    header('Location:'.WP_DOMAIN_NAME.'/carrello/?betaformazione_utente_carrello='.$betaformazione_utente_carrello);
                }
           }
                                                                    
        break;
        
        case "loginCarrelloUtenteWeb":
            $richiesta_codice_fiscale = $_POST['codice_utente'];
            $valore_del_cookie = $_POST['betaformazione_utente_carrello'];

            if(strlen($richiesta_codice_fiscale)>10){
                //echo '<h1>PRIORITA 1) CONTROLLO  CODICE FISCALE oppure CODICE_CLIENTE</h1>Cerco lista_professionisti campi codice_fiscale oppure codice, se presente setto id_professionista';
                //echo '<h2>$richiesta_codice_fiscale = '.$richiesta_codice_fiscale.'</h2>';
                $sql_00002 = "SELECT * FROM lista_professionisti WHERE codice_fiscale='$richiesta_codice_fiscale'"; //OR codice = '$richiesta_codice_fiscale'
                $row_00002 = $dblink->get_row($sql_00002, true);
                //echo '<li>$sql_00002 = '.$sql_00002.'</li>';
                //echo '<li>$conto_00002 = '.$conto_00002.'</li>';
                //print_r($row_00002);
                //StampaSQL($sql_00002,'','');
                if($row_00002['id']>0){
                    $sql_0005 = "UPDATE lista_ordini SET id_professionista = '".$row_00002['id']."'
                    WHERE campo_20='".$valore_del_cookie."' AND (stato='In Corso' OR stato='In Attesa')";
                    $rs_0005 = $dblink->query($sql_0005);
                }
                
                $idProff = $row_00002['id'];
            }else{
                $idProff = "";
            }
            
            header('Location:'.WP_DOMAIN_NAME.'/carrello/dati-utente-partecipante/?betaformazione_utente_id='.$idProff);
        break;
        
        case "salvaDatiUtenteCarrelloWeb":
            $ok = true;
            $id_utente = $_POST['betaformazione_utente_id'];
            $valore_del_cookie = $_POST['betaformazione_utente_carrello'];
            $codice_fiscale = $_POST['codice_fiscale'];
            if(empty($id_utente)){
                if(strlen($codice_fiscale)==16){

                    $sql_00001 = "SELECT id FROM lista_professionisti WHERE codice_fiscale='$codice_fiscale' AND codice_fiscale!=''";
                    $row_00001 = $dblink->get_row($sql_00001,true);
                    if($row_00001['id']>0){
                        $id_utente = $row_00001['id'];
                        $update= array(
                            "dataagg" => date("Y-m-d H:i:s"),
                            "scrittore"=>$dblink->filter("carrelloWeb"),
                            "telefono"=>$_POST['telefono'],
                            "email"=>$_POST['email'],
                            "cellulare"=>$_POST['cellulare'],
                            "fax"=>$_POST['fax'],
                            "data_di_nascita"=>GiraDataOra($_POST['data_di_nascita']),
                            "luogo_di_nascita"=>$dblink->filter($_POST['luogo_di_nascita']),
                            "provincia_di_nascita"=>$_POST['provincia_di_nascita'],
                            "id_classe"=>$_POST['id_classe'],
                            "provincia_albo"=>$_POST['provincia_albo'],
                            "numero_albo"=>$_POST['numero_albo'],
                            "stato" => "Attivo"
                        );

                        $ok = $ok && $dblink->update("lista_professionisti",$update, array("id"=>$id_utente));

                    }else{

                        if(preg_match('/^[a-zA-Z]{6}[0-9]{2}[a-zA-Z][0-9]{2}[a-zA-Z][0-9]{3}[a-zA-Z]{1}$/i', trim($codice_fiscale))) {
                            $insert= array(
                                "dataagg" => date("Y-m-d H:i:s"),
                                "scrittore"=>$dblink->filter("carrelloWeb"),
                                "codice_fiscale"=>$codice_fiscale,
                                "nome"=>$dblink->filter($_POST['nome']),
                                "cognome"=>$dblink->filter($_POST['cognome']),
                                "telefono"=>$_POST['telefono'],
                                "email"=>$_POST['email'],
                                "cellulare"=>$_POST['cellulare'],
                                "fax"=>$_POST['fax'],
                                "data_di_nascita"=>GiraDataOra($_POST['data_di_nascita']),
                                "luogo_di_nascita"=>$dblink->filter($_POST['luogo_di_nascita']),
                                "provincia_di_nascita"=>$_POST['provincia_di_nascita'],
                                "id_classe"=>$_POST['id_classe'],
                                "provincia_albo"=>$_POST['provincia_albo'],
                                "numero_albo"=>$_POST['numero_albo'],
                                "stato" => "Attivo"
                            );

                            $ok = $ok && $dblink->insert("lista_professionisti",$insert);
                            $id_utente = $dblink->lastid();
                        }else{
                            header('Location:'.WP_DOMAIN_NAME.'/carrello/dati-utente-partecipante/?errore=codice_fiscale|errore%20formattazione');
                            die();
                        }
                    }
                }else{
                    header('Location:'.WP_DOMAIN_NAME.'/carrello/dati-utente-partecipante/?errore=codice_fiscale|errore%20formattazione');
                    die();
                }
            }else{
                $update= array(
                    "dataagg" => date("Y-m-d H:i:s"),
                    "scrittore"=>$dblink->filter("carrelloWeb"),
                    "telefono"=>$_POST['telefono'],
                    "email"=>$_POST['email'],
                    "cellulare"=>$_POST['cellulare'],
                    "fax"=>$_POST['fax'],
                    "data_di_nascita"=>GiraDataOra($_POST['data_di_nascita']),
                    "luogo_di_nascita"=>$dblink->filter($_POST['luogo_di_nascita']),
                    "provincia_di_nascita"=>$_POST['provincia_di_nascita'],
                    "id_classe"=>$_POST['id_classe'],
                    "provincia_albo"=>$_POST['provincia_albo'],
                    "numero_albo"=>$_POST['numero_albo'],
                    "stato" => "Attivo"
                );

                $ok = $ok && $dblink->update("lista_professionisti",$update, array("id"=>$id_utente));
                
            }
            
            $sql_00003 = "SELECT id_azienda FROM `matrice_aziende_professionisti` WHERE id_professionista='".$id_utente."' AND stato='Attivo'";
            $row_00003 = $dblink->get_row($sql_00003, true);
            
            if($id_utente>0){
                $sql_0005 = "UPDATE lista_ordini SET id_professionista = '".$id_utente."'
                WHERE campo_20='".$valore_del_cookie."' AND (stato='In Corso' OR stato='In Attesa')";
                $rs_0005 = $ok = $ok && $dblink->query($sql_0005);
            }
            
            if($row_00003['id_azienda']>0){
                $sql_0005 = "UPDATE lista_ordini SET id_azienda = '".$row_00003['id_azienda']."'
                WHERE campo_20='".$valore_del_cookie."' AND id_professionista='$id_utente' AND (stato='In Corso' OR stato='In Attesa')";
                $ok = $ok && $dblink->query($sql_0005);
            }
            
            header('Location:'.WP_DOMAIN_NAME.'/carrello/dati-fattura/?betaformazione_utente_id='.$id_utente.'&betaformazione_fatturazione_id='.$row_00003['id_azienda']);
            die;
        break;
        
        case "salvaDatiAziendaCarrelloWeb":
            $ok = true;
            $id_utente = $_POST['betaformazione_utente_id'];
            $id_azienda = $_POST['betaformazione_fatturazione_id'];
            $valore_del_cookie = $_POST['betaformazione_utente_carrello'];
            $partita_iva = $_POST['partita_iva'];
            $codice_fiscale = $_POST['codice_fiscale'];
            
            if(strlen($partita_iva)<7){
                $partita_iva = $codice_fiscale;
            }
            
            //print_r($_POST);
            
            //echo '<li>$id_utente = '.$id_utente.'</li>';
            //echo '<li>$id_azienda = '.$id_azienda.'</li>';
            //echo '<li>$valore_del_cookie = '.$valore_del_cookie.'</li>';
            if($id_utente>0){
                if(empty($id_azienda)){
                    if(strlen($partita_iva)>10){

                        //echo "SIAMO QUI NUOVA AZIENDA<br>";
                        $sql_00001 = "SELECT id FROM lista_aziende WHERE partita_iva='$partita_iva' AND partita_iva!=''";
                        $row_00001 = $dblink->get_row($sql_00001,true);
                        if($row_00001['id']>0){
                            //echo "AZIENDA TROVATA CON P.IVA ($partita_iva)<br>";
                            $id_azienda = $row_00001['id'];
                            //ragione_sociale, forma_giuridica, partita_iva, codice_fiscale, indirizzo, cap, citta, provincia, nazione, telefono, cellulare, web, email, settore, categoria
                            $sql_00002 = "SELECT id_azienda FROM `matrice_aziende_professionisti` WHERE id_professionista='".$id_utente."' AND id_azienda='$id_azienda'";
                            $row_00002 = $dblink->get_row($sql_00002, true);
                            //echo $dblink->get_query();
                            //echo "<br>";

                            $ok = $ok && $dblink->update("matrice_aziende_professionisti",array("dataagg" => date("Y-m-d H:i:s"), "scrittore"=>$dblink->filter("carrelloWeb"),"stato"=>"Non Attivo"), array("id_professionista"=>$id_utente));

                            if($row_00002['id_azienda']>0){
                                $ok = $ok && $dblink->update("matrice_aziende_professionisti",array("dataagg" => date("Y-m-d H:i:s"), "scrittore"=>$dblink->filter("carrelloWeb"), "stato"=>"Attivo"), array("id_professionista"=>$id_utente, "id_azienda"=>$row_00002['id_azienda']));
                                //echo $dblink->get_query();
                                //echo "<br>";
                            }else{
                                $ok = $ok && $dblink->insert("matrice_aziende_professionisti",array("id_azienda"=>$id_azienda, "id_professionista"=>$id_utente, "dataagg" => date("Y-m-d H:i:s"), "scrittore"=>$dblink->filter("carrelloWeb"), "stato"=>"Attivo"));
                                //echo $dblink->get_query();
                                //echo "<br>";
                            }

                        }else{
                            echo "CREO NUOVA AZIENDA CON P.IVA ($partita_iva)<br>";
                            //if(preg_match("/^(IT){0,1}[0-9]{8,11}$/i", trim($partita_iva))) {
                                //echo "CHECK IVA OK<br>";
                                $insert= array(
                                    "dataagg" => date("Y-m-d H:i:s"),
                                    "scrittore"=>$dblink->filter("carrelloWeb"),
                                    "partita_iva"=>$partita_iva,
                                    "codice_fiscale"=>$codice_fiscale,
                                    "ragione_sociale"=>$dblink->filter($_POST['ragione_sociale']),
                                    "forma_giuridica"=>$_POST['forma_giuridica'],
                                    "telefono"=>$_POST['telefono'],
                                    "email"=>$_POST['email'],
                                    "indirizzo"=>$dblink->filter($_POST['indirizzo']),
                                    "cap"=>$_POST['cap'],
                                    "provincia"=>$dblink->filter($_POST['provincia']),
                                    "citta"=>$dblink->filter($_POST['citta']),
                                    "fax"=>$_POST['fax'],
                                    "cellulare"=>$_POST['cellulare'],
                                    "web"=>$dblink->filter($_POST['web']),
                                    "stato" => "Attivo"
                                );

                                $ok = $ok && $dblink->insert("lista_aziende",$insert);
                                //echo $dblink->get_query();
                                //echo "<br>";
                                $id_azienda = $dblink->lastid();

                                $ok = $ok && $dblink->update("matrice_aziende_professionisti",array("dataagg" => date("Y-m-d H:i:s"), "scrittore"=>$dblink->filter("carrelloWeb"),"stato"=>"Non Attivo"), array("id_professionista"=>$id_utente));
                                //echo $dblink->get_query();
                                //echo "<br>";
                                $ok = $ok && $dblink->insert("matrice_aziende_professionisti",array("id_azienda"=>$id_azienda, "id_professionista"=>$id_utente, "dataagg" => date("Y-m-d H:i:s"), "scrittore"=>$dblink->filter("carrelloWeb"), "stato"=>"Attivo"));
                                //echo $dblink->get_query();
                                //echo "<br>";
                            //}else{
                                //echo "QUI PARTITA IVA ERRATA ($partita_iva)<br>";
                                //die;
                            //    header('Location:'.WP_DOMAIN_NAME.'/carrello/dati-fattura/?betaformazione_utente_id='.$id_utente.'&errore=partita_iva|errore%20formattazione');
                            //    die;
                            //}
                        }
                    }else{
                        header('Location:'.WP_DOMAIN_NAME.'/carrello/dati-fattura/?betaformazione_utente_id='.$id_utente.'&errore=partita_iva|errore%20formattazione');
                        die;
                    }
                }else{
                    //echo "AGGIORNO AZIENDA ($id_azienda)<br>";
                    $update= array(
                        "dataagg" => date("Y-m-d H:i:s"),
                        "scrittore"=>$dblink->filter("carrelloWeb"),
                        "codice_fiscale"=>$_POST['codice_fiscale'],
                        "ragione_sociale"=>$dblink->filter($_POST['ragione_sociale']),
                        "forma_giuridica"=>$_POST['forma_giuridica'],
                        "telefono"=>$_POST['telefono'],
                        "email"=>$_POST['email'],
                        "indirizzo"=>$dblink->filter($_POST['indirizzo']),
                        "cap"=>$_POST['cap'],
                        "provincia"=>$dblink->filter($_POST['provincia']),
                        "citta"=>$dblink->filter($_POST['citta']),
                        "fax"=>$_POST['fax'],
                        "cellulare"=>$_POST['cellulare'],
                        "web"=>$dblink->filter($_POST['web']),
                        "stato" => "Attivo"
                    );

                    $ok = $ok && $dblink->update("lista_aziende",$update, array("id"=>$id_azienda));
                    //echo $dblink->get_query();
                    //echo "<br>";
                    $sql_00002 = "SELECT id_azienda FROM `matrice_aziende_professionisti` WHERE id_professionista='".$id_utente."' AND id_azienda='$id_azienda'";
                    $row_00002 = $dblink->get_row($sql_00002, true);

                    $ok = $ok && $dblink->update("matrice_aziende_professionisti",array("dataagg" => date("Y-m-d H:i:s"), "scrittore"=>$dblink->filter("carrelloWeb"),"stato"=>"Non Attivo"), array("id_professionista"=>$id_utente));

                    if($row_00002['id_azienda']>0){
                        $ok = $ok && $dblink->update("matrice_aziende_professionisti",array("dataagg" => date("Y-m-d H:i:s"), "scrittore"=>$dblink->filter("carrelloWeb"), "stato"=>"Attivo"), array("id_professionista"=>$id_utente, "id_azienda"=>$row_00002['id_azienda']));
                        //echo $dblink->get_query();
                        //echo "<br>";
                    }else{
                        $ok = $ok && $dblink->insert("matrice_aziende_professionisti",array("id_azienda"=>$id_azienda, "id_professionista"=>$id_utente, "dataagg" => date("Y-m-d H:i:s"), "scrittore"=>$dblink->filter("carrelloWeb"), "stato"=>"Attivo"));
                    }
                }
            }else{
                header('Location:'.WP_DOMAIN_NAME.'/carrello/dati-utente-partecipante/?errore=codice_fiscale|errore%20formattazione');
                die;
            }
            
            $sql_0005 = "UPDATE lista_ordini SET id_azienda = '".$id_azienda."'
                    WHERE campo_20='".$valore_del_cookie."' AND id_professionista='$id_utente' AND (stato='In Corso' OR stato='In Attesa')";
            $ok = $ok && $dblink->query($sql_0005);
            
            $sql_00003 = "SELECT id_azienda FROM `matrice_aziende_professionisti` WHERE id_professionista='".$id_utente."' AND stato='Attivo'";
            $row_00003 = $dblink->get_row($sql_00003, true);
            header('Location:'.WP_DOMAIN_NAME.'/carrello/pagamento/?betaformazione_utente_id='.$id_utente.'&betaformazione_fatturazione_id='.$row_00003['id_azienda']);
        break;
        
        case "ordineBonificoBancario":
            $ok = true;
            $dblink->begin();
            $id_utente = $_POST['betaformazione_utente_id'];
            $id_azienda = $_POST['betaformazione_fatturazione_id'];
            $valore_del_cookie = $_POST['betaformazione_utente_carrello'];
            $idOrdine = 0;
            
            if($id_azienda > 0 && $id_utente > 0 && strlen($valore_del_cookie)>2){
            
                $rowUtente = $dblink->get_row("SELECT id_classe FROM lista_professionisti WHERE id='".$id_utente."'", true);
                if($rowUtente['id_classe']=="666"){
                    $ok = $ok && $dblink->update("lista_professionisti", array("id_classe" => "0"), array("id" => $id_utente));
                }

                $rowOrdine = $dblink->get_row("SELECT * FROM lista_ordini WHERE campo_20='".$valore_del_cookie."' AND id_professionista='$id_utente' AND (stato='In Corso' OR stato='In Attesa')", true);
                
                if(!empty($rowOrdine) && $rowOrdine['id_professionista']>0 && $rowOrdine['id_azienda']>0 && $rowOrdine['imponibile']>0){
                    $idOrdine = $rowOrdine['id'];
                    $rowOrdine['id_ordine'] = $rowOrdine['id'];
                    //$rowOrdine['stato'] = "In Attesa";
                    $rowOrdine['dataagg'] = date("Y-m-d H:i:s");
                    $rowOrdine['data_iscrizione'] = date("Y-m-d");
                    $rowOrdine['scrittore'] = $dblink->filter("carrelloWeb");
                    $rowOrdine['stato'] = "Venduto";
                    $rowOrdine['id_agente'] = "37784";
                    $rowOrdine['sezionale'] = "01";
                    $rowOrdine['id_sezionale'] = "2";
                    unset($rowOrdine['id']);
                    unset($rowOrdine['notifica_email']);
                    $ok = $ok && $dblink->insert("lista_preventivi", $rowOrdine);
                    $idPrev = $dblink->lastid();
                    if($ok){
                        $ok = $ok && $dblink->update("lista_ordini", array("stato" => "Chiuso", "data_iscrizione"=>date("Y-m-d")), array("id" => $idOrdine));

                        $rowOrdineDettagli = $dblink->get_results("SELECT * FROM lista_ordini_dettaglio WHERE id_ordine='".$idOrdine."' AND quantita>0");
                        foreach ($rowOrdineDettagli as $rowOrdineDettaglio) {
                            if($ok){
                                unset($rowOrdineDettaglio['id']);
                                unset($rowOrdineDettaglio['id_ordine']);
                                unset($rowOrdineDettaglio['url_immagine']);
                                $rowOrdineDettaglio['dataagg'] = date("Y-m-d H:i:s");
                                $rowOrdineDettaglio['scrittore'] = $dblink->filter("carrelloWeb");
                                $rowOrdineDettaglio['id_preventivo'] = $idPrev;
                                $rowOrdineDettaglio['id_professionista'] = $id_utente;
                                $rowOrdineDettaglio['sezionale'] = "01";
                                $rowOrdineDettaglio['id_sezionale'] = "2";
                                $rowOrdineDettaglio['stato'] = "Venduto";
                                $rowOrdineDettaglio['codice_preventivo'] = $rowOrdineDettaglio['codice_ordine'];
                                unset($rowOrdineDettaglio['codice_ordine']);
                                $rowOrdineDettaglio['barcode_preventivo'] = $rowOrdineDettaglio['barcode_ordine'];
                                unset($rowOrdineDettaglio['barcode_ordine']);
                                $rowOrdineDettaglio['descrizione_breve_prodotto'] = $rowOrdineDettaglio['descrizione_prodotto'];
                                unset($rowOrdineDettaglio['descrizione_prodotto']);
                                $ok = $ok && $dblink->insert("lista_preventivi_dettaglio", $rowOrdineDettaglio);
                                /*echo $dblink->get_query(); 
                                $dblink->rollback();
                                die;*/
                            }
                        }
                        if($ok){
                            $ok = $ok && $dblink->update("lista_ordini", array("campo_20" => "", "id_agente"=>"37784"), array("id" => $idOrdine));
                            $ok = $ok && $dblink->update("lista_ordini_dettaglio", array("stato" => "Chiuso"), array("id_ordine" => $idOrdine));
                        }
                    }else{
                        $log->log_all_errors("ordineBonificoBancario -> Si è verificato un errore nelle query e non è partito l'ordine via BonificoBancario.","ERRORE");
                    }
                }else{
                   //$ok = false; 
                }
            
            }else{
                //$ok = false;
            }
            
            if($ok){
                if($idOrdine<=0){
                    $rowPreventivo = $dblink->get_row("SELECT * FROM lista_preventivi WHERE campo_20='".$valore_del_cookie."' AND id_professionista='$id_utente' AND id_ordine > 0 ORDER BY id DESC", true);
                    $idOrdine = $rowPreventivo['id_ordine'];
                }
                
                $dblink->commit();
                
                inviaEmailTemplate_Base($id_utente, "ordineWebBonificoConferma", 0, $idOrdine);
                
                header('Location:'.WP_DOMAIN_NAME.'/carrello/bonifico-bancario/?betaOrdId='.$idOrdine);
            }else{
                $dblink->rollback();
                header('Location:'.WP_DOMAIN_NAME.'/carrello/pagamento/?betaformazione_utente_id='.$id_utente.'&betaformazione_fatturazione_id='.$id_azienda);
            }
        break;
        
        case "ordinePayPal":
            $ok = true;
            $id_utente = $_POST['betaformazione_utente_id'];
            $id_azienda = $_POST['betaformazione_fatturazione_id'];
            $valore_del_cookie = $_POST['betaformazione_utente_carrello'];
            
            $codSezionale = '01';
            $preventivo_nuovo = nuovoCodicePreventivoWeb($codSezionale);
            
            $rowUtente = $dblink->get_row("SELECT id_classe FROM lista_professionisti WHERE id='".$id_utente."'", true);
            if($rowUtente['id_classe']==666){
                $ok = $ok && $dblink->update("lista_professionisti", array("id_classe" => "0"), array("id" => $id_utente));
            }
            
            $rowOrdine = $dblink->get_row("SELECT * FROM lista_ordini WHERE campo_20='".$valore_del_cookie."' AND id_professionista='$id_utente' AND (stato='In Corso' OR stato='In Attesa' OR stato='Chiuso')", true);
            
            $idOrdine = $rowOrdine['id'];
            $rowOrdine['id_ordine'] = $rowOrdine['id'];
            //$rowOrdine['stato'] = "In Attesa";
            $rowOrdine['dataagg'] = date("Y-m-d H:i:s");
            $rowOrdine['data_iscrizione'] = date("Y-m-d");
            $rowOrdine['scrittore'] = $dblink->filter("carrelloWeb");
            $rowOrdine['stato'] = "Venduto";
            $rowOrdine['codice'] = $preventivo_nuovo;
            $rowOrdine['id_agente'] = "37784";
            $rowOrdine['sezionale'] = "01";
            $rowOrdine['id_sezionale'] = "2";
            unset($rowOrdine['notifica_email']);
            unset($rowOrdine['id']);
            
            $ok = $ok && $dblink->insert("lista_preventivi", $rowOrdine);
            $idPrev = $dblink->lastid();
            if($ok){
                $ok = $ok && $dblink->update("lista_ordini", array("stato" => "Chiuso", "data_iscrizione"=>date("Y-m-d")), array("id" => $idOrdine));
                
                $rowOrdineDettagli = $dblink->get_results("SELECT * FROM lista_ordini_dettaglio WHERE id_ordine='".$idOrdine."' AND quantita>0");
                foreach ($rowOrdineDettagli as $rowOrdineDettaglio) {
                    if($ok){
                        unset($rowOrdineDettaglio['id']);
                        unset($rowOrdineDettaglio['id_ordine']);
                        unset($rowOrdineDettaglio['url_immagine']);
                        $rowOrdineDettaglio['dataagg'] = date("Y-m-d H:i:s");
                        $rowOrdineDettaglio['scrittore'] = $dblink->filter("carrelloWeb");
                        $rowOrdineDettaglio['id_preventivo'] = $idPrev;
                        $rowOrdineDettaglio['id_professionista'] = $id_utente;
                        $rowOrdineDettaglio['sezionale'] = "01";
                        $rowOrdineDettaglio['id_sezionale'] = "2";
                        $rowOrdineDettaglio['stato'] = "Venduto";
                        $rowOrdineDettaglio['codice_preventivo'] = $rowOrdineDettaglio['codice_ordine'];
                        unset($rowOrdineDettaglio['codice_ordine']);
                        $rowOrdineDettaglio['barcode_preventivo'] = $rowOrdineDettaglio['barcode_ordine'];
                        unset($rowOrdineDettaglio['barcode_ordine']);
                        $rowOrdineDettaglio['descrizione_breve_prodotto'] = $rowOrdineDettaglio['descrizione_prodotto'];
                        unset($rowOrdineDettaglio['descrizione_prodotto']);
                        $ok = $ok && $dblink->insert("lista_preventivi_dettaglio", $rowOrdineDettaglio);
                    }
                }
                if($ok){
                    $ok = $ok && $dblink->update("lista_ordini", array("id_agente"=>"37784"), array("id" => $idOrdine));
                    $ok = $ok && $dblink->update("lista_ordini_dettaglio", array("stato" => "Chiuso"), array("id_ordine" => $idOrdine));
                }
            }
            if($ok){
                //$rowProff = $dblink->get_row("SELECT * FROM lista_professionisti WHERE id='$id_utente'",true);
                //$rowAzienda = $dblink->get_row("SELECT * FROM lista_aziende WHERE id='$id_azienda'",true);
                $log->log_all_errors("ordinePayPal -> vado alla pagina salvaOrdinePayPal !!! ","OK");
                header("Location:".WP_DOMAIN_NAME."/moduli/carrello/salvaOrdinePayPal.php?id_utente=".$id_utente."&id_azienda=".$id_azienda."&id_ordine=".$idOrdine );
                ?>
                <!-- 
                <html>
                <head><title>::AREA CLIENTI BETA FORMAZIONE - PayPal::</title></head>
                <body onLoad="document.paypal_pagamento.submit();"><!--onLoad="document.paypal_pagamento.submit();"-->
                <!--<form action="<?=BASE_URL?>/paypal/process.php" method="post" id="paypal_pagamento" class="form-horizontal" name="paypal_pagamento" autocomplete="off">
                <input type="hidden" name="lastname" id="lastname" value="<?php echo $rowProff['cognome']; ?>">
                <input type="hidden" name="firstname" id="firstname" value="<?php echo $rowProff['nome']; ?>">
                <input type="hidden" name="address1" id="address1" value="<?php echo $rowAzienda['indirizzo']; ?>">
                <input type="hidden" name="city" id="city" value="<?php echo $rowAzienda['citta']; ?>">
                <input type="hidden" name="state" id="state" value="<?php echo $rowAzienda['provincia']; ?>">
                <input type="hidden" name="zip" id="zip" value="<?php echo $rowAzienda['cap']; ?>">
                <input type="hidden" name="phone1" id="phone1" value="<?php echo $rowAzienda['telefono']; ?>">
                <input type="hidden" name="email" id="email" value="<?php echo $rowAzienda['email']; ?>">
                <input type="hidden" name="item_name" id="item_name" value="<?php echo "Betfaormazione - Ordine n. ".$idOrdine."/".$rowOrdine['sezionale']; ?>">
                <input type="hidden" name="quantity" id="quantity" value="1">
                <input type="hidden" name="amount" id="amount" value="<?php echo round($rowOrdine['importo']*1.04,2); ?>"> 
                <center><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="333333">In Processo . . . </font></center>
                </form>
                </body>
                </html>
                -->
                <?php
            }else{
                $log->log_all_errors("ordinePayPal -> Si è verificato un errore nelle query e non è partito l'ordine via paypal.","ERRORE");
            }
            //header('Location:'.WP_DOMAIN_NAME.'/');
        break;
    }
}
?>
