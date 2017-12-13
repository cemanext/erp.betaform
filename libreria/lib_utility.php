<?php
function creaTinyUrl($url){
    $ch = curl_init("http://tinyurl.com/api-create.php?url=".$url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}
$url="www.cemanext.it";

function GiraData($DataDaGirare) {
$Data = explode("-", $DataDaGirare);
$DataGirata = "$Data[2]-$Data[1]-$Data[0]";
return $DataGirata;
}

function GiraDataItaBarra($DataDaGirare) {
$Data = explode("-", $DataDaGirare);
$DataGirata = "$Data[2]/$Data[1]/$Data[0]";
return $DataGirata;
}

function GiraDataIta($DataDaGirareIta) {
$Data = explode("/", $DataDaGirareIta);
$DataDaGirareIta = "$Data[2]-$Data[1]-$Data[0]";
return $DataDaGirareIta;
}

function after ($questo, $inthat){
    if (!is_bool(strpos($inthat, $questo)))
    return substr($inthat, strpos($inthat,$questo)+strlen($questo));
}

function after_last ($questo, $inthat){
    if (!is_bool(strrevpos($inthat, $questo)))
    return substr($inthat, strrevpos($inthat, $questo)+strlen($questo));
}

function before ($questo, $inthat){
    return substr($inthat, 0, strpos($inthat, $questo));
}

function before_last ($questo, $inthat){
    return substr($inthat, 0, strrevpos($inthat, $questo));
}

function between ($questo, $that, $inthat){
    return before ($that, after($questo, $inthat));
}

function between_last ($questo, $that, $inthat){
    return after_last($questo, before_last($that, $inthat));
}

// use strrevpos function in case your php version does not include it
function strrevpos($instr, $needle){
    $rev_pos = strpos (strrev($instr), strrev($needle));
    if ($rev_pos===false) return false;
    else return strlen($instr) - $rev_pos - strlen($needle);
}

function securityLogut() {
    header("Location: ".BASE_URL."/logout.php");
}

function inputTendina($label, $idInput, $nameInput, $sql){
    global $dblink;
    echo '<div class="form-group">
    <label class="control-label col-md-6">'.$label.':</label>
    <div class="col-md-6">
    <select class="form-control input-sm select2" id="'.$idInput.'" name="'.$nameInput.'">';

    $res = $dblink->get_results($sql);

    echo '<option  id="'.$idInput.$_SESSION['id_contatto_utente'].'" name="'.$nameInput.$_SESSION['id_contatto_utente'].'" value="'.$_SESSION['id_contatto_utente'].'">'.$_SESSION['cognome_nome_utente'].' ['.$_SESSION['id_contatto_utente'].']</option>';
    echo '<option  id="'.$idInput.'0" name="'.$nameInput.'0" value="Non Selezionato">Selezionare...</option>';
    foreach ($res as $row) {
        echo '<option  id="'.$idInput.$row['id_professionista'].'" name="'.$nameInput.$row['id_professionista'].'" value="'.$row['id_professionista'].'">'.$row['cognome'].' '.$row['nome'].' ['.$row['id_professionista'].']</option>';
    }

    echo '</select>
    </div>
    </div>';
}

function salvaGenerale(){
    global $dblink;

        //echo '<h1>siamo qui</h1>';
        //print_r($_REQUEST);
        //echo '<hr>';
        //print_r($_POST);

        $arrayCampi = $_POST;
        $nome_id = $arrayCampi['txt_id'];
        //ECHO '<H1>$nome_id = '.$nome_id.'</H1>';

        $nome_tabella = $arrayCampi['txt_tabella'];
        //ECHO '<H1>$nome_tabella = '.$nome_tabella.'</H1>';

        $nome_where = $arrayCampi['txt_where'];
        //ECHO '<H1>$nome_where = '.$nome_where.'</H1>';

        $nome_referer = recupera_referer($arrayCampi['txt_referer']);
        //ECHO '<H1>$nome_referer = '.$nome_referer.'</H1>';

        $conto = 0;

        $tuttiCampi = array();
        $nomeCampoFile = null;
        
        foreach ($arrayCampi as $key => $value) {
             $arrayCampi[$key] = trim(str_replace("`", "", $value));
             $pos = strpos($key, "txt_");
             if ($pos === false) {
                 switch ($key) {
                    case "id":
                        //non salvare
                    break;
                     
                    case "dataagg":
                        $tuttiCampi[$key]=date("Y-m-d H:i:s");
                    break;

                    case "scrittore":
                        $tuttiCampi[$key]=$dblink->filter($_SESSION['cognome_nome_utente']);
                    break;

                    default:
                        if(strpos($key,"_directoryFile")){
                            $nomeCampoFile = str_replace("_directoryFile", "", $key);
                            
                            if(!empty($_FILES)){
                                
                                $dir = $arrayCampi[$key];
                                if(isset($_FILES[$nomeCampoFile]) and strlen($_FILES[$nomeCampoFile]["name"])>3 ){

                                    $allegato = $_FILES[$nomeCampoFile]["name"];

                                    /*	UPLOAD IMMAGINE     */
                                    if(!is_dir(substr($dir, 0, -1))){
                                        mkdir(substr($dir, 0, -1), 0777);
                                    }

                                    if($_FILES[$nomeCampoFile]["error"] > 0 and strlen($_FILES[$nomeCampoFile]["name"])>1){
                                        //$testo_debug .= "<li>Return Code: " . $_FILES[$nomeCampoFile]["error"] . "</li>";
                                    }else{

                                       if (file_exists("".$dir. $_FILES[$nomeCampoFile]["name"])){

                                            chmod($dir. $_FILES[$nomeCampoFile]["name"], 0777);
                                            move_uploaded_file($_FILES[$nomeCampoFile]["tmp_name"],
                                            "".$dir. $_FILES[$nomeCampoFile]["name"]);

                                            $tuttiCampi[$nomeCampoFile] = $dblink->filter(trim(str_replace("`", "", $_FILES[$nomeCampoFile]["name"])));

                                        }else{
                                            
                                            $fileUnlink = $dblink->get_field("SELECT $nomeCampoFile FROM $nome_tabella WHERE $nome_where");
                                            
                                            unlink($dir.$fileUnlink);
                                            
                                            move_uploaded_file($_FILES[$nomeCampoFile]["tmp_name"],
                                            "".$dir. $_FILES[$nomeCampoFile]["name"]);

                                            $tuttiCampi[$nomeCampoFile] = $dblink->filter(trim(str_replace("`", "", $_FILES[$nomeCampoFile]["name"])));
                                        }

                                   }

                                   $nomeCampoFile = null;
                                }
                            }
                            
                        }else{
                            $isData = substr($key, 0, 4);
                            if(strtolower($isData) == "data"){
                                $tuttiCampi[$key] = $dblink->filter(GiraDataOra(trim(str_replace("`", "", $value))));
                            }else{
                                $tuttiCampi[$key] = $dblink->filter(trim(str_replace("`", "", $value)));
                            }
                        }
                    break;
                }
                 //echo '<li style="color:red;">'.$key.' = '.$arrayCampi[$key].'</li>';             
             }else{
                 //echo '<li style="color:green;">'.$key.' = '.$arrayCampi[$key].'</li>';
                 unset($arrayCampi['txt_tbl']);
                 unset($arrayCampi['txt_id']);
                 unset($arrayCampi['txt_where']);
                 unset($arrayCampi['txt_tabella']);
                 unset($arrayCampi['txt_referer']);
                 unset($arrayCampi['txt_num_campi']);

             }
         }

    unset($tuttiCampi['_wysihtml5_mode']);
         
    if($nome_id!=0){
        $ok = $dblink->updateWhere($nome_tabella, $tuttiCampi, $nome_where);
         //if($ok) header("Location:".$nome_referer."");
         //       else echo "error updateWhere";
    }else{
        $ok = $dblink->insert($nome_tabella, $tuttiCampi);
           //if($ok) header("Location:".$nome_referer."");
           //     else echo "error insert";
    }
    
    return $ok;
}

function cancellaGenerale($tabella = false, $where = false){
    global $dblink;
    
    //RECUPERO LA TABELLA
    if(isset($_GET['tbl']) && !$tabella){
        $tabella = $_GET['tbl'];
    }

    //RECUPERO L'ID
    if(isset($_GET['id']) && !$where){
        $id = $_GET['id'];
        $where = "id='".$_GET['id']."'";
    }elseif(!$where){
        $id = '';
        $where = "1";
    }
    
    $ok = $dblink->deleteWhere($tabella, $where, 1);
    
    return $ok;
}

function footerJSload($core=true, $page=true, $theme=true){
     ?>
     <!--[if lt IE 9]>
    <script src="<?=BASE_URL?>/assets/global/plugins/respond.min.js"></script>
    <script src="<?=BASE_URL?>/assets/global/plugins/excanvas.min.js"></script>
    <![endif]-->
    <?php

    if($core){ ?>
        <!-- BEGIN CORE PLUGINS -->
        <script src="<?=BASE_URL?>/assets/global/plugins/moment.min.js" type="text/javascript"></script>
        <script src="<?=BASE_URL?>/assets/global/plugins/jquery.min.js" type="text/javascript"></script>
        <script src="<?=BASE_URL?>/assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
        <script src="<?=BASE_URL?>/assets/global/plugins/js.cookie.min.js" type="text/javascript"></script>
        <script src="<?=BASE_URL?>/assets/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
        <script src="<?=BASE_URL?>/assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
        <script src="<?=BASE_URL?>/assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
        <script src="<?=BASE_URL?>/assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
        <!-- END CORE PLUGINS -->
        <?php
    }
    if($page){ ?>
        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <script src="<?=BASE_URL?>/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
        <script src="<?=BASE_URL?>/assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="<?=BASE_URL?>/assets/global/plugins/jquery.pulsate.min.js" type="text/javascript"></script>
        <script src="<?=BASE_URL?>/assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
        <script src="<?=BASE_URL?>/assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="<?=BASE_URL?>/assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="<?=BASE_URL?>/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <!-- END PAGE LEVEL PLUGINS -->
        <?php
    }
    if($theme){ ?>
        <!-- BEGIN THEME GLOBAL SCRIPTS -->
        <script src="<?=BASE_URL?>/assets/global/scripts/app.min.js" type="text/javascript"></script>
        <!-- END THEME GLOBAL SCRIPTS -->
        <?php
    }
 }
 
 function pageFooterCopy(){
    $ret = '<!-- BEGIN FOOTER -->
            <div class="page-footer">
                <div class="page-footer-inner"> Copyright &copy; <?= date("Y") ?> powered by '.COPYRIGHT.' - '.VERSIONE.' - Ultimo aggiornamento '.LAST_UPDATE.'
                <?php
                //echo  $testo_debug;
                ?>
                </div>
                <div class="scroll-to-top">
                    <i class="icon-arrow-up"></i>
                </div>
            </div>
            <!-- END FOOTER -->';
    return $ret;
 }


function get_pagina_titolo($idMenu,$whereListaMenu){
    global $dblink;
    
    if(isset($idMenu) && !empty($idMenu)){
        $row_1 = $dblink->get_row("SELECT * FROM lista_menu WHERE id='".$idMenu."' AND stato='Attivo' $whereListaMenu ORDER BY ordine ASC",true);
        echo '<h1 class="page-title">'.$row_1['nome'].' <small>'.$row_1['descrizione_breve'].' - '.$row_1['descrizione'].'</small></h1>';
    }else{
        echo '<h1 class="page-title"><small></small></h1>';
    }
    
}

function getNomeAgente($idAgente){
    global $dblink;
    
    $row_1 = $dblink->get_row("SELECT nome, cognome FROM lista_password WHERE id='".$idAgente."' AND stato='Attivo' ORDER BY id DESC",true);
    
    return $row_1['nome']." ".$row_1['cognome'];
}

function getNomeProfessionista($idProfessionista){
    global $dblink;
    
    $row_1 = $dblink->get_row("SELECT nome, cognome FROM lista_professionisti WHERE id='".$idProfessionista."' ORDER BY id DESC",true);
    
    return $row_1['nome']." ".$row_1['cognome'];
}

function getNomeAzienda($idAzienda){
    global $dblink;
    
    $row_1 = $dblink->get_row("SELECT ragione_sociale, forma_giuridica FROM lista_aziende WHERE id='".$idAzienda."' ORDER BY id DESC",true);
    
    return $row_1['ragione_sociale']." ".$row_1['forma_giuridica'];
}

function getIdTipoMarketing($nomeTipoMarketing){
    global $dblink;
    
    $row_1 = $dblink->get_row("SELECT id FROM lista_tipo_marketing WHERE nome LIKE '".$nomeTipoMarketing."' AND stato='Attivo' ORDER BY id DESC",true);
    
    return $row_1['id'];
}

function ottieniNomeCampagna($idCampagna){
    global $dblink;
    
    return $dblink->get_field("SELECT nome FROM lista_campagne WHERE id = '".$idCampagna."' ORDER BY id DESC");
}

function ottieniIdCampagna($nomeCampagna){
    global $dblink;
    
    return $dblink->get_field("SELECT id FROM lista_campagne WHERE nome LIKE '".$nomeCampagna."' AND stato='Attivo' ORDER BY id DESC");
}

function ottieniIdAzienda($idProfessionista){
    global $dblink;
    
    $row_1 = $dblink->get_row("SELECT id_azienda FROM `matrice_aziende_professionisti` WHERE id_professionista='".$idProfessionista."' ORDER BY stato ASC",true);
    
    if($row_1['id_azienda']>0){
        return $row_1['id_azienda'];
    }else{
        return 0;
    }
}

function ottieniNomeClasse($idClasse){
    global $dblink;
    
    return $dblink->get_field("SELECT nome FROM lista_classi WHERE id = '".$idClasse."' ORDER BY id DESC");
}

function ordinaDataAgg($dataAgg){
    $tmp = explode(" ", $dataAgg);
    $tmpData = explode("-", $tmp[0]);
    $data = "$tmpData[2]-$tmpData[1]-$tmpData[0]";
    $ora = $tmp[1];
    
    return "$data $ora";
}

function pulisciCampoEtichetta($stringa = ""){

    if(is_array($stringa)){
        foreach ($stringa as $key => $value) {
            $value = str_replace("_", " ", $value);
            $value = str_replace("-", " ", $value);

            $ret[$key] = $value;
        }
    }else{
        $stringa = str_replace("_", " ", $stringa);
        $stringa = str_replace("-", " ", $stringa);
        $ret = $stringa;
    }

    return $ret;
}

function in_array_r($needle, $haystack, $strict = false) {
    foreach ($haystack as $item) {
        if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_r($needle, $item, $strict))) {
            return true;
        }
    }

    return false;
}

function GiraDataOra($data,$chrData = "-", $chrOra = ":" ){
    if(!empty($data)){
        $tmp = explode(" ", $data);
        if(strpos($tmp[0], $chrOra) !== false) {
            $data = substr($tmp[0],0,8);
        }else{
           $dataTmp = explode($chrData, $tmp[0]);
           $data = $dataTmp[2]."-".$dataTmp[1]."-".$dataTmp[0];
           if(!empty($tmp[1])){
                if(strpos($tmp[1], $chrOra) !== false) { 
                    $data.= " ".substr($tmp[1],0,8);
                }
           }
        }
    }
    
    return $data;
}

function recupera_referer($referer = ""){
    if($referer==="") $referer = $_SERVER['HTTP_REFERER'];
    $nome_referer = str_replace("&res=1", "",$referer);
    $nome_referer = str_replace("&res=0", "",$nome_referer);
    $nome_referer = str_replace("&res=2", "",$nome_referer);
    $nome_referer = str_replace("&res=3", "",$nome_referer);
    $nome_referer = str_replace("&res=4", "",$nome_referer);
    $nome_referer = str_replace("&res=5", "",$nome_referer);
    $nome_referer = str_replace("&res=6", "",$nome_referer);
    $nome_referer = str_replace("&res=7", "",$nome_referer);
    $nome_referer = str_replace("&res=8", "",$nome_referer);
    $nome_referer = str_replace("&res=9", "",$nome_referer);
    $nome_referer = str_replace("&tab=prof", "",$nome_referer);
    $nome_referer = str_replace("&tab=aziende", "",$nome_referer);
    $nome_referer = str_replace("#tab_prof", "",$nome_referer);
    $nome_referer = str_replace("#tab_aziende", "",$nome_referer);

    return $nome_referer;
}

function generaPassword( $length = 9, $count = 0 ) {

    $password = '';

    $possibleChars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_?+!#@-:/'; 

    $i = 0; 

    while ($i < $length) { 
        
        switch (mt_rand(1,4)) {
            case "1":
                $possibleChars = '?!%$#@'; 
            break;

            case "2":
                $possibleChars = 'abcdefghijklmnopqrstuvwxyz'; 
            break;

            case "3":
                $possibleChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'; 
            break;

            default:
                $possibleChars = '0123456789'; 
            break;
        }

        $char = substr($possibleChars, mt_rand(0, strlen($possibleChars)-1), 1);

        if (!strstr($password, $char)) { 
            $password .= $char;
            $i++;
        }

    }

    if(preg_match("((?=.*[0-9])(?=.*[a-z])(?=.*[?!%$#@])(?=.*[A-Z]).{9,})", $password)){
        //echo "PASSWORD: ".$password."<br>";
    }else if($count > 20){
        //echo "OLTRE IL COUNT DI 20 CICLI<br>";
        $password = substr('abcdefghijklmnopqrstuvwxyz', mt_rand(0, strlen('abcdefghijklmnopqrstuvwxyz')-1), 1);
        $password.= substr('ABCDEFGHIJKLMNOPQRSTUVWXYZ', mt_rand(0, strlen('ABCDEFGHIJKLMNOPQRSTUVWXYZ')-1), 1);
        $password.= substr('?!%$#@Ababcdefghijklmnopqrstuvwxyz0123456789', mt_rand(0, strlen('?!%$#@Ababcdefghijklmnopqrstuvwxyz0123456789')-1), 1);
        $password.= substr('0123456789', mt_rand(0, strlen('0123456789')-1), 1);
        $password.= substr('0123456789?!%$#@AbCdEfGhIjKlMnOpQrStUvWxYz', mt_rand(0, strlen('0123456789?!%$#@AbCdEfGhIjKlMnOpQrStUvWxYz')-1), 1);
        $password.= substr('ABCDEFGHIJKLMNOPQRSTUVWXYZ', mt_rand(0, strlen('ABCDEFGHIJKLMNOPQRSTUVWXYZ')-1), 1);
        $password.= substr('?!%$#@', mt_rand(0, strlen('?!%$#@')-1), 1);
        $password.= substr('0123456789', mt_rand(0, strlen('0123456789')-1), 1);
        $password.= substr('abcdefghijklmnopqrstuvwxyz', mt_rand(0, strlen('abcdefghijklmnopqrstuvwxyz')-1), 1);
    }else{
        //echo "CICLO OLD: $count - ";
        $count++;
        //echo "CICLO: $count<br>";
        $password = generaPassword($length, $count);
    }
    
    return $password;
}

function controllaRichiesteMultiple($idRichiesta){
    global $dblink;
    
    $nuovaRichiesta = $dblink->get_row("SELECT * FROM calendario WHERE id='$idRichiesta'", true);
    
    //VERIFICO SE HO L'ID PROFESIONISTA O UNA RICHIESTA APERTA SU TELEFONO O MAIL.
    $sql_001 = "SELECT * FROM calendario WHERE etichetta='Nuova Richiesta' AND id != '$idRichiesta' AND id_prodotto='".$nuovaRichiesta['id_prodotto']."' AND ("
            . " (id_professionista='".$nuovaRichiesta['id_professionista']."' AND id_professionista!='0' AND (stato = 'Richiamare' OR stato = 'Mai Contattato' OR stato = 'In Attesa di Controllo' OR stato = 'Nuovo Nominativo In Attesa di Controllo'))"
            . " OR "
            . " (campo_4='".$nuovaRichiesta['campo_4']."' AND campo_4!='' AND (stato = 'Richiamare' OR stato = 'Mai Contattato' OR stato = 'In Attesa di Controllo' OR stato = 'Nuovo Nominativo In Attesa di Controllo'))"
            . " OR "
            . " (campo_5='".$nuovaRichiesta['campo_5']."' AND campo_5!='' AND (stato = 'Richiamare' OR stato = 'Mai Contattato' OR stato = 'In Attesa di Controllo' OR stato = 'Nuovo Nominativo In Attesa di Controllo'))"
            . ")";
    $rows = $dblink->get_results($sql_001);
    
    if(!empty($rows)){
        foreach ($rows as $row) {
            $updateNuova = array(
               "dataagg" => date("Y-m-d H:i:s"),
               "scrittore" => $dblink->filter($_SESSION['cognome_nome_utente']),
               "etichetta" => "Nuova Richiesta Accorpata",
               "stato" => "Accorpata"
            );
            $dblink->update("calendario",$updateNuova,array("id"=>$idRichiesta));

            $updateAperta = array(
               "dataagg" => date("Y-m-d H:i:s"),
               "scrittore" => $dblink->filter($_SESSION['cognome_nome_utente']),
               "messaggio" => "CONCAT('Richiesta Accorpata del: ".GiraDataOra($nuovaRichiesta['data'])." ".GiraDataOra($nuovaRichiesta['ora'])."\\n".$dblink->filter($nuovaRichiesta['messaggio'])." \\n\\n', messaggio)"
            );
            $dblink->update("calendario",$updateAperta,array("id"=>$row['id']));
           
            if($_SESSION['livello_utente']=='commerciale'){
                $row_0002 = $dblink->get_row("SELECT stato FROM calendario WHERE id='".$row['id']."'", true);
                if($row_0002['stato']=="In Attesa di Controllo"){
                    $updateComm = array(
                        "stato" => $nuovaRichiesta['stato'],
                        "id_agente"=>$nuovaRichiesta['id_agente'],
                        "destinatario"=>$nuovaRichiesta['destinatario']
                    );
                    $dblink->update("calendario", $updateComm, array("id"=>$row['id']));
                }
            }
           
           $idCalReturn = $row['id'];
        }
        
        return $idCalReturn;
    }else{
        return $idRichiesta;
    }
}

function stampaDelleInfoPhpSviluppo(){
    echo "<h3>DEBUG PERMORMANCE</h3>";
    
    echo "<li>UTILIZZO MEMORIA: ".round(((memory_get_usage()/ 1024) / 1024),2)." MB</li>";
    echo "<li>UTILIZZO MASSIMO MEMORIA: ".round(((memory_get_peak_usage()/ 1024) / 1024),2)." MB</li>";
    echo "<li>LIMITE MASSIMO MEMORIA: ".ini_get('memory_limit')."</li>";
    list($cpu) = sys_getloadavg();
    echo "<li>SERVER CPU USAGE: ".$cpu."</li>"; 
}

function verificaEmail($emailVerifica){
    global $dblink;
    
    return preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i", $emailVerifica);
}

function modificaAccentate($str) {
	$search = explode(",",
"á,é,í,ó,ú,à,è,ì,ò,ù,ä,ë,ï,ö,ü,ÿ,â,ê,î,ô,û,å,ø,Ø,Å,Á,À,Â,Ä,È,É,Ê,Ë,Í,Î,Ï,Ì,Ò,Ó,Ô,Ö,Ú,Ù,Û,Ü,Ÿ,Ç,Æ,Œ");
	$replace = explode(",",
"&aacute;,&eacute;,&iacute;,&oacute;,&uacute;,&agrave;,&egrave;,&igrave;,&ograve;,&ugrave;,a,e,i,o,u,y,a,e,i,o,u,a,o,O,&Aacute;,&Agrave;,A,A,A,&Eacute;,&Egrave;,E,E,&Iacute;,I,I,&Igrave;,&Ograve;,&Oacute;,O,O,&Uacute;,&Ugrave;,U,U,Y,C,AE,OE");
	return str_replace($search, $replace, $str);
}
?>
