<?php
include_once('../../config/connDB.php');
include_once(BASE_ROOT.'config/confAccesso.php');

$referer = recupera_referer();

if(isset($_GET['fn'])){
    switch ($_GET['fn']) {
        
        //http://dev.betaformazione.com/moduli/corsi/salva.php?tbl=lista_domande&idCorsoDettaglio=8880&fn=aggiungiDomandaCorso
        
        case 'aggiungiDomandaCorso':
            $ok = true;
            $dblink->begin();
            $idCorsoDettaglio = $_GET['idCorsoDettaglio'];
            
            $sql_00001 = "INSERT INTO lista_domande (id, dataagg, scrittore, id_corso, id_corso_dettaglio, id_modulo, instance, id_corso_moodle) 
            SELECT '', NOW(), '".$_SESSION['cognome_nome_utente']."',  id_corso, id, id_modulo, instance, id_corso_moodle FROM lista_corsi_dettaglio WHERE id='".$idCorsoDettaglio."'";
            $ok = $ok && $dblink->query($sql_00001);
            $lastId=$dblink->insert_id();
            
            if($ok){
                $ok = 1;
                $dblink->commit();
            }else{
                $ok = 0;
                $dblink->rollback();
            }
            header("Location:".$referer."&ret=$ok");
        break;
        
        case 'iscriviEsameUtente':
            $ok = true;
            $dblink->begin();
            
            $idCalendario = $_GET['idCalendario'];
            $idProfessionista = $_GET['idProfessionista'];
            $idProdotto = $_GET['idProdotto'];
            $idPreventivo = $_GET['idPreventivo'];
            
            $sql_iscrivi_corso_utente = "INSERT INTO calendario (id, dataagg, scrittore, id_preventivo, id_calendario_0, etichetta, id_professionista, stato, id_prodotto, oggetto, data, ora) 
            SELECT '', NOW(), '".$_SESSION['cognome_nome_utente']."',  '".$idPreventivo."', '".$idCalendario."', 'Iscrizione Esame', '".$idProfessionista."', 'Iscritto', '".$idProdotto."', oggetto, data, ora FROM calendario WHERE id='".$idCalendario."'";
            $ok = $ok && $dblink->query($sql_iscrivi_corso_utente);
            $lastId=$dblink->insert_id();
            
            $numero_iscritti_al_corso = $dblink->get_field("SELECT COUNT(*) AS conteggio FROM calendario WHERE id_calendario_0='".$idCalendario."' AND etichetta='Iscrizione Esame'");
            
            $sql_002 = "UPDATE calendario SET numerico_10 = '".$numero_iscritti_al_corso."' WHERE id='".$idCalendario."' AND etichetta LIKE 'Calendario Esami'";
            $ok = $ok && $dblink->query($sql_002);
            
            if($ok){
                $ok = 1;
                $dblink->commit();
            }else{
                $ok = 0;
                $dblink->rollback();
            }
            header("Location:".$referer."&ret=$ok");
        break;
        
        case 'iscriviCorsoUtente':
            $ok = true;
            $dblink->begin();
            
            $idCalendario = $_GET['idCalendario'];
            $idProfessionista = $_GET['idProfessionista'];
            $idProdotto = $_GET['idProdotto'];
            $idPreventivo = $_GET['idPreventivo'];
            
            $sql_iscrivi_corso_utente = "INSERT INTO calendario (id, dataagg, scrittore, id_preventivo, id_calendario_0, etichetta, id_professionista, stato, id_prodotto, oggetto, data, ora) 
            SELECT '', NOW(), '".$_SESSION['cognome_nome_utente']."', '".$idPreventivo."',  '".$idCalendario."', 'Iscrizione Corso', '".$idProfessionista."', 'Iscritto', '".$idProdotto."', oggetto, data, ora FROM calendario WHERE id='".$idCalendario."'";
            $ok = $ok && $dblink->query($sql_iscrivi_corso_utente);
            $lastId=$dblink->insert_id();
            
            $numero_iscritti_al_corso = $dblink->get_field("SELECT COUNT(*) AS conteggio FROM calendario WHERE id_calendario_0='".$idCalendario."' AND etichetta='Iscrizione Corso'");
            
            $sql_002 = "UPDATE calendario SET numerico_10 = '".$numero_iscritti_al_corso."' WHERE id='".$idCalendario."' AND etichetta LIKE 'Calendario Corsi'";
            $ok = $ok && $dblink->query($sql_002);
            
            if($ok){
                $ok = 1;
                $dblink->commit();
            }else{
                $ok = 0;
                $dblink->rollback();
            }
            header("Location:".$referer."&ret=$ok");
        break;
        
        case 'SalvaDocenteCorso':
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
                    //print_r($tuttiCampi[$r]);
                    $ok = $dblink->update("matrice_corsi_docenti", $tuttiCampi[$r], array("id" => $idWhere));
                    //if (!$ok)
                        //echo "errore Database";
                    //echo $dblink->get_query();
                    //echo "<br>";
                }
            }

            header("Location:$referer");
        break;
            
        case "nuovoDocenteCorso":
        $idProdotto = $_GET['idProdotto'];
        $idCalendario = $_GET['id'];
            $ok = true;
            $dblink->begin();
            //DAV IDE RIFAI QUESTO CON TUO CODICE
            $sql_inserisci_docente_corso = "INSERT INTO `matrice_corsi_docenti` (`id`, `data_creazione`, `dataagg`, `id_calendario`, `id_prodotto`,`scrittore`,`stato`)   
            SELECT '', NOW(), NOW(), '".$idCalendario."', '".$idProdotto."', '" .addslashes($_SESSION['cognome_nome_utente']) . "', 'Attivo' FROM lista_prodotti WHERE id=" . $_GET['idProdotto'];
            $ok = $dblink->query($sql_inserisci_docente_corso);
            $lastId = $dblink->insert_id();
            if ($ok) {
                $ok = 1;
                $dblink->commit();
            } else {
                $ok = 0;
                $dblink->rollback();
            }
            //header("Location:modifica.php?tbl=lista_preventivi_dettaglio&id=$lastId&res=$ok");
            header("Location:" . $referer . "");
        break;
            
        case "inviaAttestatiMultipli":
            $arrayCampi = $_POST;
            unset($arrayCampi['txt_checkbox_all']);
            $conto = 0;
            foreach ($arrayCampi as $key => $value) {
                $pos = strpos($key, "txt_checkbox_");
                if ($pos === false) {
                    //echo '<li style="color:red;">' . $key . ' = ' . $arrayCampi[$key] . '</li>';
                } else {
                    $idIscrizione = $arrayCampi[$key];
                    //echo '<li style="color:green;">idIscrizione = ' . $idIscrizione . '</li>';
                    $sql_0001 = "UPDATE lista_iscrizioni
                        SET dataagg=NOW(),
                        scrittore='" . addslashes($_SESSION['cognome_nome_utente']) . "',
                        stato_invio_attestato = 'In Attesa di Invio'
                        WHERE id='" . $idIscrizione . "'";
                    $rs_0001 = $dblink->query($sql_0001);
                }
            }
            header("Location:" . $referer . "&res=6");
        break;
        
        case "aggiungiConfigurazioneCorso":
            $ok = true;
            $dblink->begin();
            $idCorso = $_GET['idCorso'];
            $sql_aggiungiConfigurazioneCorso = "INSERT INTO `lista_corsi_configurazioni` (`id`, `dataagg`, `scrittore`, `stato`, `id_corso`, `id_prodotto`) SELECT '', NOW(), '".addslashes($_SESSION['cognome_nome_utente'])."', 'Non Attivo', id, id_prodotto FROM lista_corsi WHERE id='".$idCorso."'";
            $ok = $ok && $dblink->query($sql_aggiungiConfigurazioneCorso);
            $lastId=$dblink->insert_id();
            if($ok){
                $ok = 1;
                $dblink->commit();
            }else{
                $ok = 0;
                $dblink->rollback();
            }
            header("Location:".$referer."");
        break;
        
         case "aggiungiEsameCorso":
            $ok = true;
            $dblink->begin();
            $idCorsoNuovoEsame = $_GET['idCorsoNuovoEsame'];
            $sql_aggiungiEsameCorso = "INSERT INTO `calendario` (`id`, `dataagg`, `scrittore`, `stato`, `id_corso`, etichetta, oggetto) 
            SELECT '', NOW(), '".addslashes($_SESSION['cognome_nome_utente'])."', 'Non Attivo', id, 'Calendario Esami', CONCAT('Esame ',nome_prodotto) FROM lista_corsi WHERE id='".$idCorsoNuovoEsame."'";
            $ok = $ok && $dblink->query($sql_aggiungiEsameCorso);
            $lastId=$dblink->insert_id();
            if($ok){
                $ok = 1;
                $dblink->commit();
            }else{
                $ok = 0;
                $dblink->rollback();
            }
            header("Location:".$referer."");
        break;
        
        case "salvaConfigurazioneCorso":  
        case "salvaDomandeCorso":        
            $idCorso = $_GET['idCorso'];
            $tabella = $_GET['tbl'];
            
            
            $arrayRisultati = $_POST;
            
            $conto = 0;

            $tuttiCampi = array();
            foreach ($arrayRisultati as $key => $value) {
                //echo "<br>KEY: $key<br>";
                $pos_001 = strpos($key, "txt_");
                //echo "POS: ".$pos_001."<br>";
                if($pos_001 === false) {
                    
                }else{
                    $tmp = explode("_", $key);
                    //print_r($tmp);
                    $nome_campo = substr($key, (strlen("txt_".$tmp[1]."_")));
                    if(strpos($nome_campo, "data")!==false){
                        if(strlen($value)>0){
                            $tuttiCampi[$tmp[1]][$nome_campo] = GiraDataOra(trim(str_replace("`", "", $value)));
                        }else{
                            $tuttiCampi[$tmp[1]][$nome_campo] = "";
                        }
                    }else{
                        //echo "<br>$nome_campo<br>";

                        $tuttiCampi[$tmp[1]][$nome_campo] = $dblink->filter(trim(str_replace("`", "", $value)));
                    }
                }
                $conto++;
            }
            
            
            //print_r($tuttiCampi);
            
            $count = 0;
            
            foreach($tuttiCampi as $record){
                $count++;
                /*foreach($record as $nomi_colonne => $valore){
                    echo '<lI>$nomi_colonne = '.$nomi_colonne.' / $valore = '.$valore.'</li>';
                }*/
            }
            
            
            for($r=0;$r<$count;$r++){
                
                $tuttiCampi[$r]['dataagg'] = date("Y-m-d H:i:s");
                $tuttiCampi[$r]['scrittore'] = $dblink->filter($_SESSION['cognome_nome_utente']);
                
                if($tuttiCampi[$r]['id']>0){
                    $idWhere = $tuttiCampi[$r]['id'];
                    //echo "<br>";
                    unset($tuttiCampi[$r]['id']);
                    
                    //print_r($tuttiCampi[$r]);
                    $ok = $dblink->update($tabella, $tuttiCampi[$r], array("id"=>$idWhere)); 
                    
                    //echo $dblink->get_query();
                    //echo "<br>";
                }
            
            }
            
            //die;
            header("Location:".$referer."");
        break;
    }
}

if(isset($_POST['txt_tbl'])){
    switch ($_POST['txt_tbl']) {    
        case "lista_corsi":
            salvaGenerale();

            $sql_0002 = "SELECT nome as nome_prodotto FROM lista_prodotti WHERE id=".$_POST['id_prodotto'];
            $row_0002 = $dblink->get_row($sql_0002, true);

            $dblink->update("lista_corsi", $row_0002, array("id" => $_POST['txt_id']));

            header("Location:".$referer."");
        break;

        default:
            salvaGenerale();
            $referer = $_POST['txt_referer'];
            header("Location:".$referer."");
        break;
    }
}
?>