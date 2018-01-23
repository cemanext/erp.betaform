<?php
include_once('../../config/connDB.php');
include_once(BASE_ROOT.'config/confAccesso.php');

global $dblink;

$referer = recupera_referer();

if(isset($_GET['fn'])){
    switch ($_GET['fn']) {
        
        case 'cambiaStatoInVenduto':
            $ok = true;
            $dblink->begin();
            $idPreventivo = $_GET['idPrev'];
            
            $idCalendario = $dblink->get_field("SELECT id_calendario FROM lista_preventivi WHERE id = '$idPreventivo'");
            
            $updatePrev = array(
                "dataagg" => date("Y-m-d H:i:s"),
                "scrittore" => $dblink->filter($_SESSION['cognome_nome_utente']),
                "stato" => "Venduto",
                "data_iscrizione" => date("Y-m-d"),
                "data_firma" => "0000-00-00"
            );
            $ok = $ok && $dblink->update("lista_preventivi", $updatePrev, array("id"=>$idPreventivo));
            
            $updatePrevDett = array(
                "dataagg" => date("Y-m-d H:i:s"),
                "scrittore" => $dblink->filter($_SESSION['cognome_nome_utente']),
                "stato" => "Venduto"
            );
            $ok = $ok && $dblink->update("lista_preventivi_dettaglio", $updatePrevDett, array("id_preventivo"=>$idPreventivo));
            
            $ok = $ok && $dblink->deleteWhere("calendario", "id_preventivo = '".$idPreventivo."' AND etichetta LIKE 'Ordini' AND messaggio LIKE '%Negativo%'", 1);
            
            $updateCalendario = array(
                "dataagg" => date("Y-m-d H:i:s"),
                "scrittore" => $dblink->filter($_SESSION['cognome_nome_utente']),
                "stato" => "Venduto"
            );
            $ok = $ok && $dblink->update("calendario", $updateCalendario, array("id"=>$idCalendario));
            
            $sql_43 = "INSERT INTO calendario (`id`, `scrittore`, `dataagg`, `datainsert`, `orainsert`, id_contatto, id_professionista, id_azienda, id_agente, id_preventivo, id_commessa, `data`, `ora`, `etichetta`, `oggetto`, `messaggio`, `mittente`, `destinatario`, `priorita`, `stato`)
            SELECT '', '".addslashes($_SESSION['cognome_nome_utente'])."', NOW(), NOW(), NOW(), id_contatto, id_professionista, id_azienda, id_agente, '".$idPreventivo."', '', CURDATE(), TIME(NOW()), 'Ordini', CONCAT('Ordine n ', id ,''), CONCAT('Ordine n ', id ,': Venduto il ',NOW(),''), '".addslashes($_SESSION['cognome_nome_utente'])."', '', 'Normale', 'Fatto' FROM lista_preventivi WHERE id='".$idPreventivo."'";
            $ok = $ok && $dblink->query($sql_43);
            
            if($ok){
                $ok = 1;
                $dblink->commit();
            }else{
                $ok = 0;
                $dblink->rollback();
            }
            
            header("Location:".$referer."&ret=$ok");
        break;
        
        case 'cambiaStatoInNegativo':
            $ok = true;
            $dblink->begin();
            $idPreventivo = $_GET['idPrev'];
            
            $idCalendario = $dblink->get_field("SELECT id_calendario FROM lista_preventivi WHERE id = '$idPreventivo'");
            
            $updatePrev = array(
                "dataagg" => date("Y-m-d H:i:s"),
                "scrittore" => $dblink->filter($_SESSION['cognome_nome_utente']),
                "stato" => "Negativo",
                "data_iscrizione" => date("Y-m-d"),
                "data_firma" => "0000-00-00"
            );
            $ok = $ok && $dblink->update("lista_preventivi", $updatePrev, array("id"=>$idPreventivo));
            
            $updatePrevDett = array(
                "dataagg" => date("Y-m-d H:i:s"),
                "scrittore" => $dblink->filter($_SESSION['cognome_nome_utente']),
                "stato" => "Negativo"
            );
            $ok = $ok && $dblink->update("lista_preventivi_dettaglio", $updatePrevDett, array("id_preventivo"=>$idPreventivo));
            
            $ok = $ok && $dblink->deleteWhere("calendario", "id_preventivo = '".$idPreventivo."' AND etichetta LIKE 'Ordini' AND messaggio LIKE '%Venduto%'", 1);
            
            $updateCalendario = array(
                "dataagg" => date("Y-m-d H:i:s"),
                "scrittore" => $dblink->filter($_SESSION['cognome_nome_utente']),
                "stato" => "Negativo"
            );
            $ok = $ok && $dblink->update("calendario", $updateCalendario, array("id"=>$idCalendario));
            
            $sql_43 = "INSERT INTO calendario (`id`, `scrittore`, `dataagg`, `datainsert`, `orainsert`, id_contatto, id_professionista, id_azienda, id_agente, id_preventivo, id_commessa, `data`, `ora`, `etichetta`, `oggetto`, `messaggio`, `mittente`, `destinatario`, `priorita`, `stato`)
            SELECT '', '".addslashes($_SESSION['cognome_nome_utente'])."', NOW(), NOW(), NOW(), id_contatto, id_professionista, id_azienda, id_agente, '".$idPreventivo."', '', CURDATE(), TIME(NOW()), 'Ordini', CONCAT('Ordine n ', id ,''), CONCAT('Ordine n ', id ,': Negativo il ',NOW(),''), '".addslashes($_SESSION['cognome_nome_utente'])."', '', 'Normale', 'Fatto' FROM lista_preventivi WHERE id='".$idPreventivo."'";
            $ok = $ok && $dblink->query($sql_43);
            
            if($ok){
                $ok = 1;
                $dblink->commit();
            }else{
                $ok = 0;
                $dblink->rollback();
            }
            
            header("Location:".$referer."&ret=$ok");
        break;
    
        case 'annullaStatoNegativo':
            $ok = true;
            $dblink->begin();
            $idPreventivo = $_GET['idPrev'];
            
            $idCalendario = $dblink->get_field("SELECT id_calendario FROM lista_preventivi WHERE id = '$idPreventivo'");
            
            $updatePrev = array(
                "dataagg" => date("Y-m-d H:i:s"),
                "scrittore" => $dblink->filter($_SESSION['cognome_nome_utente']),
                "stato" => "In Attesa",
                "data_iscrizione" => "0000-00-00",
                "data_firma" => "0000-00-00"
            );
            $ok = $ok && $dblink->update("lista_preventivi", $updatePrev, array("id"=>$idPreventivo));
            
            $updatePrevDett = array(
                "dataagg" => date("Y-m-d H:i:s"),
                "scrittore" => $dblink->filter($_SESSION['cognome_nome_utente']),
                "stato" => "In Attesa"
            );
            $ok = $ok && $dblink->update("lista_preventivi_dettaglio", $updatePrevDett, array("id_preventivo"=>$idPreventivo));
            
            $ok = $ok && $dblink->deleteWhere("calendario", "id_preventivo = '".$idPreventivo."' AND etichetta LIKE 'Ordini' AND messaggio LIKE '%Negativo%'", 1);
            
            $updateCalendario = array(
                "dataagg" => date("Y-m-d H:i:s"),
                "scrittore" => $dblink->filter($_SESSION['cognome_nome_utente']),
                "stato" => "Richiamare"
            );
            $ok = $ok && $dblink->update("calendario", $updateCalendario, array("id"=>$idCalendario));
            
            if($ok){
                $ok = 1;
                $dblink->commit();
            }else{
                $ok = 0;
                $dblink->rollback();
            }
            
            header("Location:".$referer."&ret=$ok");
        break;
        
        case 'annullaStatoVenduto':
            $ok = true;
            $dblink->begin();
            $idPreventivo = $_GET['idPrev'];
            
            $idCalendario = $dblink->get_field("SELECT id_calendario FROM lista_preventivi WHERE id = '$idPreventivo'");
            
            $updatePrev = array(
                "dataagg" => date("Y-m-d H:i:s"),
                "scrittore" => $dblink->filter($_SESSION['cognome_nome_utente']),
                "stato" => "In Attesa",
                "data_iscrizione" => "0000-00-00",
                "data_firma" => "0000-00-00"
            );
            $ok = $ok && $dblink->update("lista_preventivi", $updatePrev, array("id"=>$idPreventivo));
            
            $updatePrevDett = array(
                "dataagg" => date("Y-m-d H:i:s"),
                "scrittore" => $dblink->filter($_SESSION['cognome_nome_utente']),
                "stato" => "In Attesa"
            );
            $ok = $ok && $dblink->update("lista_preventivi_dettaglio", $updatePrevDett, array("id_preventivo"=>$idPreventivo));
            
            $ok = $ok && $dblink->deleteWhere("calendario", "id_preventivo = '".$idPreventivo."' AND etichetta LIKE 'Ordini' AND messaggio LIKE '%Venduto%'", 1);
            
            $updateCalendario = array(
                "dataagg" => date("Y-m-d H:i:s"),
                "scrittore" => $dblink->filter($_SESSION['cognome_nome_utente']),
                "stato" => "Richiamare"
            );
            $ok = $ok && $dblink->update("calendario", $updateCalendario, array("id"=>$idCalendario));
            
            if($ok){
                $ok = 1;
                $dblink->commit();
            }else{
                $ok = 0;
                $dblink->rollback();
            }
            
            header("Location:".$referer."&ret=$ok");
        break;
        
        case 'annullaStatoChiuso':
            $ok = true;
            $dblink->begin();
            $idPreventivo = $_GET['idPrev'];
            $idFattura = $dblink->get_field("SELECT id FROM lista_fatture WHERE id_preventivo = '$idPreventivo'");
            $idCalendario = $dblink->get_field("SELECT id_calendario FROM lista_preventivi WHERE id = '$idPreventivo'");
            
            $ok = $ok && $dblink->delete("lista_fatture_dettaglio", array("id_fattura"=>$idFattura), 1);
            $ok = $ok && $dblink->delete("lista_fatture", array("id"=>$idFattura), 1);
            
            $updatePrev = array(
                "dataagg" => date("Y-m-d H:i:s"),
                "scrittore" => $dblink->filter($_SESSION['cognome_nome_utente']),
                "stato" => "In Attesa",
                "data_iscrizione" => "0000-00-00",
                "data_firma" => "0000-00-00"
            );
            $ok = $ok && $dblink->update("lista_preventivi", $updatePrev, array("id"=>$idPreventivo));
            
            $updatePrevDett = array(
                "dataagg" => date("Y-m-d H:i:s"),
                "scrittore" => $dblink->filter($_SESSION['cognome_nome_utente']),
                "stato" => "In Attesa"
            );
            $ok = $ok && $dblink->update("lista_preventivi_dettaglio", $updatePrevDett, array("id_preventivo"=>$idPreventivo));
            $ok = $ok && $dblink->deleteWhere("calendario", "id_preventivo = '".$idPreventivo."' AND etichetta LIKE 'Ordini' AND messaggio LIKE '%Venduto%'", 1);
            $ok = $ok && $dblink->deleteWhere("calendario", "id_preventivo = '".$idPreventivo."' AND etichetta LIKE 'Ordini' AND messaggio LIKE '%Firmato%'", 1);
            
            $updateCalendario = array(
                "dataagg" => date("Y-m-d H:i:s"),
                "scrittore" => $dblink->filter($_SESSION['cognome_nome_utente']),
                "stato" => "Richiamare"
            );
            $ok = $ok && $dblink->update("calendario", $updateCalendario, array("id"=>$idCalendario));
            
            if($ok){
                $ok = 1;
                $dblink->commit();
            }else{
                $ok = 0;
                $dblink->rollback();
            }
            
            header("Location:".$referer."&ret=$ok");
        break;
        
        case 'nuovoPreventivoProfessionista':
        $idProfessionista = $_GET['idProfessionista'];
        echo '<li>$idProfessionista = '.$idProfessionista.'</li>';
        $sql_00001 = "INSERT INTO lista_preventivi (id, dataagg, data_creazione, scrittore, id_professionista, stato) VALUES ('',NOW(), CURDATE(), '".addslashes($_SESSION['cognome_nome_utente'])."', '".$idProfessionista."', 'In Attesa')";
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
            header("Location: ".BASE_URL."/moduli/preventivi/dettaglio.php?tbl=lista_preventivi&id=".$lastId); 
        break;
        
        case "inserisciProdotto":
            $ok = true;
            $dblink->begin();
            //DAV IDE RIFAI QUESTO CON TUO CODICE
            $sql_inserisci_prodotto_in_preventivo = "INSERT INTO lista_preventivi_dettaglio (id, dataagg, scrittore, id_preventivo, quantita) VALUES ('', NOW(), '".addslashes($_SESSION['cognome_nome_utente'])."','".$_GET['id_preventivo']."',1)";
            $ok = $dblink->query($sql_inserisci_prodotto_in_preventivo);
            $lastId=$dblink->insert_id();
            if($ok){
                $ok = 1;
                $dblink->commit();
            }else{
                $ok = 0;
                $dblink->rollback();
            }
            //header("Location:modifica.php?tbl=lista_preventivi_dettaglio&id=$lastId&res=$ok");
            header("Location:".$referer."#modifica_preventivo_dettaglio");
        break;

        case "salvaPreventivoDettaglio":
            $ok = true;
            $dblink->begin();
            $ok = $ok && salvaGenerale();

            $sql_0001 = "SELECT SUM((prezzo_prodotto*quantita)) AS imponibile, SUM((prezzo_prodotto*(1+(iva_prodotto/100)))*quantita) AS 'importo' FROM lista_preventivi_dettaglio WHERE id_preventivo='".$_POST['id_preventivo']."'";
            $row_0001 = $dblink->get_row($sql_0001, true);

            $update=array(
                "dataagg" => date("Y-m-d H:i:s"),
                "scrittore" => $dblink->filter($_SESSION['cognome_nome_utente']),
                "importo"=>$row_0001['importo'],
                "imponibile"=>$row_0001['imponibile']
            );

            $ok = $ok && $dblink->update("lista_preventivi", $update, array("id"=>$_POST['id_preventivo']));

            if($ok){
                $ok = 1;
                $dblink->commit();
            }else{
                $ok = 0;
                $dblink->rollback();
            }
            header("Location:dettaglio.php?tbl=lista_preventivi&id=$_POST[id_preventivo]&res=$ok");
        break;

        case 'SalvaPreventiviDettaglio':
            //print_r($_POST);
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

                    //echo "<br>$nome_campo<br>";

                    $tuttiCampi[$tmp[1]][$nome_campo] = $dblink->filter(trim(str_replace("`", "", $value)));
                }
                $conto++;
            }


            /*print_r($tuttiCampi);*/



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

                    $sql_0002 = "SELECT nome as nome_prodotto, descrizione as descrizione_breve_prodotto, codice as codice_prodotto, id_prodotto_0, barcode as barcode_prodotto FROM lista_prodotti WHERE id='".$tuttiCampi[$r]['id_prodotto']."'";
                    $row_0002 = $dblink->get_row($sql_0002, true);

                    $tuttiCampi[$r]['nome_prodotto'] = $row_0002['nome_prodotto'];
                    $tuttiCampi[$r]['descrizione_breve_prodotto'] = $row_0002['descrizione_breve_prodotto'];
                    $tuttiCampi[$r]['codice_prodotto'] = $row_0002['codice_prodotto'];
                    $tuttiCampi[$r]['id_prodotto_0'] = $row_0002['id_prodotto_0'];
                    $tuttiCampi[$r]['barcode_prodotto'] = $row_0002['barcode_prodotto'];

                    //print_r($tuttiCampi[$r]);
                    $ok = $dblink->update("lista_preventivi_dettaglio", $tuttiCampi[$r], array("id"=>$idWhere));
                    if(!$ok) echo "errore Database";
                    /*echo $dblink->get_query();
                    echo "<br>";*/
                }

            }

            $sql_0001 = "SELECT SUM((prezzo_prodotto*quantita)) AS imponibile, SUM((prezzo_prodotto*(1+(iva_prodotto/100)))*quantita) AS 'importo' FROM lista_preventivi_dettaglio WHERE id_preventivo='".$_GET['idPrev']."'";
            $row_0001 = $dblink->get_row($sql_0001, true);

            $update=array(
                "dataagg" => date("Y-m-d H:i:s"),
                "scrittore" => $dblink->filter($_SESSION['cognome_nome_utente']),
                "importo"=>$row_0001['importo'],
                "imponibile"=>$row_0001['imponibile']
            );

            $ok = $ok && $dblink->update("lista_preventivi", $update, array("id"=>$_GET['idPrev']));

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
            }*/
        break;

        case "preventivoFirmato":
            $idPreventivoFirmato = $_GET['idPreventivoFirmato'];
            $codSezionale = $_GET['codSezionale'];


            $preventivo_nuovo = nuovoCodicePreventivo($idPreventivoFirmato, $codSezionale);

            $ok = true;
            $dblink->begin();
            //INSERIMENTO IN Fatture
            $sql_4 = "SELECT * FROM lista_preventivi WHERE id='".$idPreventivoFirmato."' LIMIT 1";
            $row_4 = $dblink->get_row($sql_4, true);
            
            if(!empty($row_4) and strlen($preventivo_nuovo)>0){
                $idCalendarioRichiesta = $row_4['id_calendario'];
                
                $sql_41 = "INSERT INTO `lista_fatture` (`id`, `id_area`, `id_sezionale`, `sezionale`, `dataagg`, `data_preventivo`, `data_creazione`, `data_scadenza`, `id_contatto`,`id_professionista`, `id_azienda`, `id_preventivo`, `codice_preventivo`, `gestore`, `codice`, `barcode`, `causale`, `pagamento`, `importo`, `imponibile`, `sconto`, `iva`, `note`, `scrittore`, `tipo`, `stato`, `id_agente`)
                SELECT '', `id_area`, `id_sezionale`, `sezionale`, NOW(), CURDATE(), '', '', `id_contatto` , `id_professionista`, `id_azienda`, id, codice, gestore, 'xxx', '', campo_1, 'Bonifico Bancario', `importo`, `imponibile`, `sconto`, `iva`,  `note`, `scrittore`, 'Fattura', 'In Attesa di Emissione', `id_agente`
                FROM lista_preventivi WHERE id='".$idPreventivoFirmato."' LIMIT 1";
                $ok = $ok && $dblink->query($sql_41);
                
                $id_fattura_sql_41 = $dblink->lastid();
                $sql_42 = "INSERT INTO `lista_fatture_dettaglio` (`id`, `id_area`, `id_sezionale`, `sezionale`, `dataagg`, `id_fattura`, `codice_fattura`, `barcode_fattura`, `id_preventivo`, `codice_preventivo`, `barcode_preventivo`, `tipo_prodotto`, `nome_prodotto`, `codice_prodotto`, `barcode_prodotto`, `id_prodotto`, `costo_prodotto`, `id_fornitore`, `costo_fornitore_prodotto`, `prezzo_prodotto`, `iva_prodotto`, `quantita`, `note`, `scrittore`, `tipo`, `stato`,  `id_professionista`, `id_azienda`, `id_provvigione`)
                SELECT '', `id_area`, `id_sezionale`, `sezionale`, NOW(), '$id_fattura_sql_41', 'xxx', '', `id_preventivo`, `codice_preventivo`, `barcode_preventivo`, `tipo_prodotto`, `nome_prodotto`, `codice_prodotto`, `barcode_prodotto`, `id_prodotto`, `costo_prodotto`, `id_fornitore`, `costo_fornitore_prodotto`, (`prezzo_prodotto`), `iva_prodotto`, `quantita`, `note`, `scrittore`, `tipo`, 'In Attesa di Emissione',  `id_professionista`, `id_azienda`, `id_provvigione`
                FROM lista_preventivi_dettaglio WHERE id_preventivo='".$idPreventivoFirmato."' ORDER BY tipo_prodotto DESC, nome_prodotto ASC";
                $ok = $ok && $dblink->query($sql_42);
                    
                $sql_43 = "INSERT INTO calendario (`id`, `scrittore`, `dataagg`, `datainsert`, `orainsert`, id_contatto, id_professionista, id_azienda, id_preventivo, id_commessa, `data`, `ora`, `etichetta`, `oggetto`, `messaggio`, `mittente`, `destinatario`, `priorita`, `stato`)
                SELECT '', '".addslashes($_SESSION['cognome_nome_utente'])."', NOW(), NOW(), NOW(), id_contatto, id_professionista, id_azienda, '".$idPreventivoFirmato."', '', CURDATE(), TIME(NOW()), 'Ordini', CONCAT('Ordine n ', id ,''), CONCAT('Ordine n ', id ,': Firmato il ',NOW(),''), '".addslashes($_SESSION['cognome_nome_utente'])."', '', 'Normale', 'Fatto' FROM lista_preventivi WHERE id='".$idPreventivoFirmato."'";
                $ok = $ok && $dblink->query($sql_43);
                        
                $sql_44 = "UPDATE lista_preventivi
                SET stato='Chiuso',
                dataagg=NOW(),
                data_firma=CURDATE(),
                data_iscrizione=IF(data_iscrizione LIKE '0000-00-00', CURDATE(), data_iscrizione),
                codice = '".$preventivo_nuovo."'
                WHERE id='".$idPreventivoFirmato."'";
                $ok = $ok && $dblink->query($sql_44);

                $sql_45 ="UPDATE calendario SET stato = 'Venduto', dataagg=NOW(), scrittore='".addslashes($_SESSION['cognome_nome_utente'])."'
                WHERE id=".$idCalendarioRichiesta;
                $ok = $ok && $dblink->query($sql_45);
            }
            
            if($ok){
                $dblink->commit();
                if(empty($referer)){
                    header("Location:".$_SESSION['NOSTRO_HTTP_REFERER']);
                }else{
                    header("Location:$referer");
                }
            }else{
                $dblink->rollback();
                header("Location:$referer");
            }

        break;

        case 'nuovoCodicePreventivo':
            $idPreventivo = $_GET['idPreventivo'];
            $codSezionale = $_GET['codSezionale'];
            if(nuovoCodicePreventivo($idPreventivo, $codSezionale)){
                header("Location:$referer");
            }else{
                echo '<li>Errore: nuovoCodicePreventivo !</li>';
            }
        break;

        case "preventivoNegativo":
            $ok = true;
            $dblink->begin();
            
            $idPreventivoNegativo = $_GET['idPreventivoNegativo'];
            $codSezionale = $_GET['codSezionale'];
            $preventivo_nuovo = nuovoCodicePreventivo($idPreventivoNegativo, $codSezionale);
            
            $sql_4 = "SELECT * FROM lista_preventivi WHERE id='".$idPreventivoNegativo."' LIMIT 1";
            $row_4 = $dblink->get_row($sql_4, true);
            
            if(!empty($row_4) && strlen($preventivo_nuovo)>0) {
                $idCalendarioRichiesta = $row_4['id_calendario'];
                
                $sql_43 = "INSERT INTO calendario (`id`, `datainsert`, `orainsert`, id_contatto, id_professionista, id_azienda, id_preventivo, id_commessa, `data`, `ora`, `etichetta`, `oggetto`, `messaggio`, `mittente`, `destinatario`, `priorita`, `stato`)
                SELECT '', NOW(), NOW(), id_contatto, id_professionista, id_azienda, '".$idPreventivoNegativo."', '', CURDATE(), TIME(NOW()), 'Ordini', CONCAT('Ordine n ', id ,''), CONCAT('Ordine n ', id ,': Negativo il ',NOW(),''), '".addslashes($_SESSION['cognome_nome_utente'])."', '', 'Normale', 'Fatto' FROM lista_preventivi WHERE id='".$idPreventivoNegativo."'";
                $ok = $ok && $dblink->query($sql_43);

                $sql_44 = "UPDATE lista_preventivi
                SET stato='Negativo',
                dataagg=NOW(),
                data_firma=CURDATE(),
                data_iscrizione=IF(data_iscrizione LIKE '0000-00-00', CURDATE(), data_iscrizione),
                codice = '".$preventivo_nuovo."'
                WHERE id='".$idPreventivoNegativo."'";
                $ok = $ok && $dblink->query($sql_44);

                $sql_45 ="UPDATE calendario SET stato = 'Negativo', dataagg=NOW(), scrittore='".addslashes($_SESSION['cognome_nome_utente'])."'
                WHERE id=".$idCalendarioRichiesta;
                $ok = $ok && $dblink->query($sql_45);
            }
            
            if($ok){
                $dblink->commit();
                header("Location:$referer");
            }else{
                $dblink->rollback();
                header("Location:$referer");
            }
        break;
        case 'inviaEmailPreventivo':

            //$idFattura = $_GET['idFatt'];
            $wMitt = $_POST['mitt'];
            $wDest = $_POST['dest'];
            $wDestCC = $_POST['dest_cc'];
            $wDestBCC = "";
            $wOgg = $_POST['ogg'];
            $wMess = $_POST['mess'];
            $PasswdEmailUtente = "";

            $wAllegato_1 = $_POST['fileDoc'];
            $wAllegato_2 = "";

            $testo_debug = "";
            
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
                    if(!is_dir(BASE_ROOT . "media")){
                        mkdir(BASE_ROOT . "media", 0777);
                    }
                    if(!is_dir(BASE_ROOT . "/lista_documenti")){
                        mkdir(BASE_ROOT . "/lista_documenti", 0777);
                    }
                    $percorso_tabella = BASE_ROOT."media/lista_documenti/".$_SESSION['id_utente'];
                    $testo_debug .= '<li>$percorso_tabella = '.$percorso_tabella.'</li>';
                    //echo '<li>$percorso_tabella = '.$percorso_tabella.'</li>';
                    
                    if($_FILES["documentoAllegato1"]["error"] > 0 and strlen($_FILES["documentoAllegato1"]["name"])>1){
                        $testo_debug .= "<li>Return Code: " . $_FILES["documentoAllegato1"]["error"] . "</li>";
                    }else{
                        /* echo "<li>Upload: " . $_FILES["file"]["name"] . "</li>";
                        echo "<li>Type: " . $_FILES["file"]["type"] . "</li>";
                        echo "<li>Size: " . ($_FILES["file"]["size"] / 1024) . " Kb</li>";
                        echo "<li>Temp file: " . $_FILES["file"]["tmp_name"] . "</li>"; */

                        //@mkdir("".$percorso_tabella."");
                        if(!is_dir(BASE_ROOT . "/lista_documenti/".$_SESSION['id_utente'])){
                            if(!mkdir($percorso_tabella, 0777, true)) {
                              //die('Failed to create folders...');
                              $testo_debug .= "Failed to create folders...";
                            }
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
            header("Location:$referer");

        break;

        default:

        break;
    }
}
?>
