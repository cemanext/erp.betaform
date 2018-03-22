<?php
include_once('../../config/connDB.php');
include_once(BASE_ROOT . 'config/confAccesso.php');

if (DISPLAY_DEBUG) {
    echo '<li>'.date('Y-m-d H:i:s').'</li>';
    echo '<li>DB_HOST = '.DB_HOST.'</li>';
    echo '<li>DB_USER = '.DB_USER.'</li>';
    echo '<li>DB_PASS = '.DB_PASS.'</li>';
    echo '<li>DB_NAME = '.DB_NAME.'</li>';
    echo '<hr>';
}

$filename="cdr.log";
            
if(!is_dir(BASE_ROOT . "media/lista_telefonate")){
    mkdir(BASE_ROOT . "media/lista_telefonate", 0777);
}

$path = BASE_ROOT.'media/lista_telefonate/'.$filename;

// readlink(BASE_ROOT.'media/lista_telefonate/'.$filename);
//echo is_link($path);
//echo is_dir(BASE_ROOT.'media/lista_telefonate/');
//echo file_exists($path);

if(file_exists($path)) {

    $countOK = 0;
    $countKO = 0;
    $countSkip = 0;
    $row = 0;
    $headers = [];
    $filepath = $path;
    if (($handle = fopen($filepath, "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 100000, "\n")) !== FALSE) {
            //if($countOK>30) exit;
            $row = explode(",",$data[0]);
            if(strlen(trim($row[2]))>0){
                  /*echo "<pre>";
                  var_dump($row);
                  echo "</pre>";*/
            
                /*if(strlen($row[0])>0){
                    $idCampagna = $dblink->get_row("SELECT id FROM lista_password WHERE LCASE(numerico_1) LIKE LCASE('".$row[0]."')", true);
                }else{
                    $idCampagna['id'] = 0;
                }

                if(strlen($row[1])>0){
                    $idTipoMarketing = $dblink->get_row("SELECT id FROM lista_tipo_marketing WHERE LCASE(nome) LIKE LCASE('".$row[1]."')", true);
                }else{
                    $idTipoMarketing['id'] = 0;
                }

                if(strlen($row[9])>0){
                    $idClasse = $dblink->get_row("SELECT id FROM lista_classi WHERE LCASE(nome) LIKE LCASE('".$row[10]."')", true);
                }else{
                    $idClasse['id'] = 0;
                }

                if(strlen($row[8])>0){
                    $idProdotto = $dblink->get_row("SELECT id, codice, codice_esterno AS id_prod_moodle FROM lista_prodotti WHERE LCASE(codice) = LCASE('".trim($row[8])."')", true);
                    $nomeProdotto = $dblink->filter($row[8]);
                }else{
                    $idProdotto['id'] = 0;
                    $idProdotto['codice'] = "";
                    $nomeProdotto = "";
                }
                */
                /*if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",trim($row[10])) && trim($row[10])!="0000-00-00") {
                    $dataInsert = true;
                } else {
                    $dataInsert = false;
                }*/
                
                $tmpID = explode(" ", $row[0]);                         //historyid
                $idCodiceEsterno = $tmpID[1];                           
                $durataChiamata = $row[2];                              //duration
                $dataInizioChiamata = str_replace("/", "-", $row[3]);   //time-start
                $dataInizioTelefonata = str_replace("/", "-", $row[4]); //time-answred
                $dataFineTelefonata = str_replace("/", "-", $row[5]);   //time-end
                $stato = $row[6];                                       //reason-terminated
                $fromNumber = $row[7];                                  //from-no
                $toNumber = $row[8];                                    //to-no
                $fromDn = $row[9];                                      //from-dn
                $toDn = $row[10];                                       //to-dn
                $dialNo = $row[11];                                     //dial-no
                $reasonChanged = $row[12];                              //reason-changed
                $finalNumber = $row[13];                               //final-number
                $finalDn = $row[14];                                    //final-dn
                $billCode = $row[15];                                   //bill-code
                $billRate = $row[16];                                   //bill-rate
                $billCost = $row[17];                                   //bill-cost
                $billName = $row[18];                                   //bill-name
                $chain = $row[19];                                      //chain
                
                if(strpos($chain, "BETA IMPRESE")!==false){
                    continue; //Salto la riga se è BetaImprese
                }
                
                $campo_1 = $data[0];                                    //salvo tutto il record
                
                if(strpos($fromNumber, "Ext.")!==false && $fromNumber!='Ext.MakeCall'){
                    
                    if($toDn == '10002'){
                        continue; //Salto la riga se è BetaImprese
                    }
                    
                    $mittente = substr($fromNumber, 4);
                    $idCommerciale = $dblink->get_field("SELECT id FROM lista_password WHERE LCASE(numerico_1) LIKE LCASE('".$mittente."')");
                    
                    if(is_numeric($toDn) && $toDn!='10000' && $toDn!='10002'){
                        $destinatario = $toDn;
                    }else{
                        $destinatario = $dialNo;
                    }
                    
                    if($reasonChanged == 'ReplacedSrc'){
                        $destinatario = substr($finalNumber, 4);
                    }
                    
                    
                }else if($fromNumber=='Ext.MakeCall'){
                    
                    if($toDn == '10002'){
                        continue; //Salto la riga se è BetaImprese
                    }
                    
                    $mittente = substr($toNumber, 4);
                    $idCommerciale = $dblink->get_field("SELECT id FROM lista_password WHERE LCASE(numerico_1) LIKE LCASE('".$mittente."')");
                    
                    if(is_numeric($toDn) && $toDn!='10000' && $toDn!='10002'){
                        $destinatario = $toDn;
                    }else{
                        $destinatario = $dialNo;
                    }
                    
                    if($reasonChanged == 'ReplacedSrc'){
                        $destinatario = substr($finalNumber, 4);
                    }
                    
                }else{
                    //Chiamata in Ingresso
                    $mittente = $fromNumber;
                    
                    if($fromDn == '10002'){
                        continue; //Salto la riga se è BetaImprese
                    }
                    
                    $destinatario = substr($toNumber, 4);
                    
                    if($reasonChanged == 'ReplacedDst'){
                        $destinatario = substr($finalNumber, 4);
                    }
                    
                    $idCommerciale = $dblink->get_field("SELECT id FROM lista_password WHERE LCASE(numerico_1) LIKE LCASE('".$destinatario."')");
                    
                }
                
                
                $rowEsiste = $dblink->get_row("SELECT id FROM lista_telefonate WHERE codice_esterno = '$idCodiceEsterno'",true);
                
                if(empty($rowEsiste)){
                    $insert = array(
                      "dataagg" => date("Y-m-d H:i:s"),
                      "scrittore" => $dblink->filter("importTelefonate"),
                      "stato" => $dblink->filter($stato),
                      "id_password" => $idCommerciale,
                      "mittente" => $dblink->filter($mittente),
                      "destinatario" => $dblink->filter($destinatario),
                      "codice_esterno" => $idCodiceEsterno,
                      "data_chiamata_inizio" => $dataInizioTelefonata,
                      "data_chiamata_fine" => $dataFineTelefonata,
                      "durata_chiamata" => $durataChiamata,
                      "costo_chiamata" => $billCost,
                      "campo_1" => $campo_1,
                    );

                    $ok = $dblink->insert("lista_telefonate", $insert);
                    if($ok) $countOK ++;
                    else $countKO ++;
                }else{
                    $countSkip++;
                }
              }else{
                $countSkip++;
              }
        }
        fclose($handle);
    }

    //$log->log_all_errors("IMPORTAZIONE FILE ($fileName) - RIGHE IMPORTATE: $countOK - RIGHE CON ERRORI: $countKO - RIGHE SALTATE: $countSkip","OK");

    //unlink($path);

    //header("Location:$referer&ok=$countOK&errore=$countKO&skip=$countSkip");
}else{
    echo "FILE NON ESISTE";
}
    
if (DISPLAY_DEBUG) echo '</pl>'.date("H:i:s");
?>