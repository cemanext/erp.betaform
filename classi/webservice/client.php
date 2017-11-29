<?php
ini_set('max_execution_time', 0);

/// SETUP PRODUZIONE - NEED TO BE CHANGED
//define('MOODLE_TOKEN', '72c0a2cdee20c06e3c53a9995d9082ed');
//define('MOODLE_DOMAIN_NAME', 'http://elearning.betaformazione.com');
//$domainname = 'http://elearning.betaformazione.com';

/// SETUP DEMO - NEED TO BE CHANGED
//define('MOODLE_TOKEN', '23382f64b84a8859c4c8bed01ed14abe');
//define('MOODLE_DOMAIN_NAME', 'http://moodle.betaformazione.com');

/// SETUP DEMO LOCALE CEMA - NEED TO BE CHANGED
//define('MOODLE_TOKEN', 'a2cb228c9790faab10052490609974d5');
//define('MOODLE_DOMAIN_NAME', 'http://192.168.2.36/Beta_Formazione_multitenant/moodle/');

/// SETUP DEMO LOCALE CEMA - NEED TO BE CHANGED
//define('MOODLE_TOKEN', 'a726c1830452513ad74dcbb971e5c8de');


/**
 * REST client for Moodle 2
 * Return JSON or XML format
 *
 * @authorr Jerome Mouneyrac
 */

class moodleWebService
{
    private $curl = null;
    private $token = null;
    private $domainName = null;
    private $restformat = null;
    private $log = null;

    public function __construct()
    {
        try {
            require_once(BASE_ROOT.'classi/webservice/curl.php');
            $this->curl = new curl;
            $this->token = MOODLE_TOKEN;
            $this->domainName = MOODLE_DOMAIN_NAME;
            $this->restformat = '&moodlewsrestformat=json';
            require_once(BASE_ROOT.'classi/class.log.php');
            $this->log = new logerp();
        } catch ( Exception $e ) {
            die( 'Unable to connect to database' );
        }
    }

    public function __destruct()
    {
        $this->curl = null;
        $this->token = null;
        $this->domainName = null;
        $this->restformat = null;
    }

    private function curl_post($functionName){
        $serverurl = $this->domainName . '/webservice/rest/server.php'. '?wstoken=' . $this->token . '&wsfunction='.$functionName;
        $resp = $this->curl->post($serverurl . $this->restformat, $params);
        return json_decode($resp);
    }

    private function curl_setopt_post($func, $functionObject){
        $serverurl = $this->domainName . '/local/'.$func.'/webservice.php'. '?requestObject=' . $functionObject;
        $resp = $this->curl->post($serverurl);
        return json_decode($resp);
    }

    public function get_all_course() {
        return $this->curl_post('core_course_get_courses');
    }

    public function get_all_lesson($idCourse) {
        return $this->curl_post('core_course_get_contents&courseid='.$idCourse);
    }

    public function get_activities_completion($idCourse,$idUser) {
        return $this->curl_post('core_completion_get_activities_completion_status&courseid='.$idCourse.'&userid='.$idUser);
    }

    public function get_cohort() {
        return $this->curl_post('core_cohort_get_cohorts');
    }

    public function get_user_by_field($fieldName, $fieldValue) {
        return $this->curl_post('core_user_get_users_by_field&field='.$fieldName.'&values[0]='.$fieldValue);
    }

    public function creaUtenteMoodle($username, $email, $firstname, $lastname, $password, $idnumber) {
        global $dblink;

        /*if(empty($firstname)){
            $this->log->log_all_errors("client.php->creaUtenteMoodle\tParametro \$firstname non settato.", "ERRORE");
            return false; //parametro nome mancante
        }
        if(empty($lastname)){
            $this->log->log_all_errors("client.php->creaUtenteMoodle\tParametro \$lastname non settato.", "ERRORE");
            return false; //parametro cognome mancante
        }*/
        if(empty($password)){
            $this->log->log_all_errors("client.php->creaUtenteMoodle\tParametro \$password non settato.", "ERRORE");
            return false; //parametro cognome mancante
        }
        if(empty($username)){
            $this->log->log_all_errors("client.php->creaUtenteMoodle\tParametro \$username non settato.", "ERRORE");
            return false; //parametro cognome mancante
        }
        /*if(empty($idnumber)){
            $this->log->log_all_errors("client.php->creaUtenteMoodle\tParametro \$idnumber non settato.", "ERRORE");
            return false; //parametro cognome mancante
        }*/
        if(!verificaEmailClient($email)){
            $this->log->log_all_errors("client.php->creaUtenteMoodle\tParametro \$email non settato.", "ERRORE");
            return false; //parametro email mancante o errato
        }
        
        $functionnameCreate = 'core_user_create_users';
        $functionnameUpdate = 'core_user_update_users';
        
        $webservice = $dblink->get_row("SELECT id FROM ".MOODLE_DB_NAME.".mdl_external_services WHERE shortname = 'betaformazione_webservice'",true);
        //$webservice = $DB->get_record('external_services',array('shortname'=>'betaformazione_webservice'));
        $token = $dblink->get_row("SELECT token FROM ".MOODLE_DB_NAME.".mdl_external_tokens WHERE externalserviceid = '".$webservice['id']."'",true);
        //$token = $DB->get_field('external_tokens','token',array('externalserviceid'=>$webservice->id));
        
        //$token['token'] = "c373bd610ef989d04c5f18eeaab413a7";
        
        $this->log->log_all_errors("DENTRO ALLA FUNZIONE DOPO IL RECUPERO TOKEN", "AVVISO");
        
        $sql_000 = "SELECT * FROM ".MOODLE_DB_NAME.".mdl_user WHERE idnumber = '$idnumber'";
        $moodleUserNumber = $dblink->get_row($sql_000,true);
        if(empty($moodleUserNumber)) {
        
            //$sql_001 = "SELECT * FROM ".MOODLE_DB_NAME.".mdl_user WHERE email = '$email'";
            //$moodleUser = $dblink->get_row($sql_001,true);
            //if(empty($moodleUser)) {

                $this->log->log_all_errors("OK->$sql_001", "AVVISO");

                $newuser = new stdClass();
                $newuser->username  = $username;
                $newuser->firstname = $firstname;
                $newuser->lastname  = $lastname;
                $newuser->email     = $email;
                $newuser->password  = $password;
                $newuser->idnumber  = $idnumber;

                $users = array($newuser);
                $params = array('users' => $users);

                $path = $this->domainName."/webservice/rest/server.php?wstoken=" . $token['token'] . '&wsfunction=' . $functionnameCreate;

                $this->log->log_all_errors("OK->CREO PATH WEBSERVICE->$path", "AVVISO");

                $resp = $this->curl->post($path, $params);

                $this->log->log_all_errors("OK->RISPOSTA POST WEBSERVICE->$resp", "AVVISO"); 
                $this->log->log_all_errors("OK->RISPOSTA PARAM: ".var_export($params,true)."", "AVVISO"); 

                $xml_resp = simplexml_load_string($resp);

                if($xml_resp['class'] == 'invalid_parameter_exception') {
                    $this->log->log_all_errors("client.php->creaUtenteMoodle\tErrore nel webservice->core_user_create_users\t".$xml_resp->MESSAGE, "ERRORE");
                    return false;
                } else {
                    $sql_002 = "SELECT id FROM ".MOODLE_DB_NAME.".mdl_user WHERE idNumber = '$idnumber'";
                    $u = $dblink->get_row($sql_002, true);

                    return $u['id'];
                }

            /*}else{
                $idUtenteMoodle = $moodleUser['id'];
                //LO TROVO PER ID NUMBER
                $this->log->log_all_errors("client.php->creaUtenteMoodle\twebservice->$functionnameUpdate\tUtente già presente email: $email", "OK");
                if($moodleUser['deleted']>0){
                    $this->log->log_all_errors("client.php->creaUtenteMoodle\twebservice->$functionnameUpdate\tUtente cancellato id_user_moodle: ".$moodleUser['id'], "OK");
                    $ret = $this->ripristinaUtenteMoodle($moodleUser['id']);
                    if($ret){
                        $this->log->log_all_errors("client.php->creaUtenteMoodle\twebservice->$functionnameUpdate\tUtente ripristinato id_user_moodle: ".$moodleUser['id'], "OK");
                        $idUtenteMoodle = $moodleUser['id'];
                    }else{
                        $this->log->log_all_errors("client.php->creaUtenteMoodle\twebservice->$functionnameUpdate\tImpossibile ripristinare l'utente id_user_moodle: ".$moodleUser['id'], "ERRORE");
                        return false;
                    }
                }

                if($moodleUser['suspended']>0){
                    $this->log->log_all_errors("client.php->creaUtenteMoodle\twebservice->$functionnameUpdate\tUtente disabilitato id_user_moodle: ".$moodleUser['id'], "OK");
                    $ret = $this->abilitaUtenteMoodle($moodleUser['id']);
                    if($ret){
                        $this->log->log_all_errors("client.php->creaUtenteMoodle\twebservice->$functionnameUpdate\tUtente abilitato id_user_moodle: ".$moodleUser['id'], "OK");
                        $idUtenteMoodle = $moodleUser['id'];
                    }else{
                        $this->log->log_all_errors("client.php->creaUtenteMoodle\twebservice->$functionnameUpdate\tImpossibile abilitare l'utente id_user_moodle: ".$moodleUser['id'], "ERRORE");
                        return false;
                    }
                }

                $newuser = new stdClass();
                $newuser->id  = $moodleUser['id'];
                $newuser->username  = $username;
                $newuser->firstname = $firstname;
                $newuser->lastname  = $lastname;
                $newuser->email     = $email;
                $newuser->password  = $password;
                $newuser->idnumber  = $idnumber;

                $users = array($newuser);
                $params = array('users' => $users);

                $path = $this->domainName."/webservice/rest/server.php?wstoken=" . $token['token'] . '&wsfunction=' . $functionnameUpdate;

                $this->log->log_all_errors("OK->CREO PATH WEBSERVICE->$path", "AVVISO");

                $resp = $this->curl->post($path, $params);

                $this->log->log_all_errors("OK->RISPOSTA POST WEBSERVICE->$resp", "AVVISO"); 
                $this->log->log_all_errors("OK->RISPOSTA PARAM: ".var_export($params,true)."", "AVVISO"); 

                $xml_resp = simplexml_load_string($resp);

                if($xml_resp['class'] == 'invalid_parameter_exception') {
                    $this->log->log_all_errors("client.php->creaUtenteMoodle\tErrore nel webservice->$functionnameUpdate\t".$xml_resp->MESSAGE, "ERRORE");
                    return $resp;
                } else {
                    $this->log->log_all_errors("client.php->creaUtenteMoodle\twebservice->$functionnameUpdate\tUtente già presente e ripristinato email: $email", "OK");
                    return $idUtenteMoodle; //UTENTE GIA PRESENTE
                }
            }*/
        }else{
            $idUtenteMoodle = $moodleUserNumber['id'];
            //LO TROVO PER ID NUMBER
            $this->log->log_all_errors("client.php->creaUtenteMoodle\twebservice->$functionnameUpdate\tUtente già presente idNumber: $idnumber", "OK");
            if($moodleUserNumber['deleted']>0){
                $this->log->log_all_errors("client.php->creaUtenteMoodle\twebservice->$functionnameUpdate\tUtente cancellato id_user_moodle: ".$moodleUserNumber['id'], "OK");
                $ret = $this->ripristinaUtenteMoodle($moodleUserNumber['id']);
                if($ret){
                    $this->log->log_all_errors("client.php->creaUtenteMoodle\twebservice->$functionnameUpdate\tUtente ripristinato id_user_moodle: ".$moodleUserNumber['id'], "OK");
                    $idUtenteMoodle = $moodleUserNumber['id'];
                }else{
                    $this->log->log_all_errors("client.php->creaUtenteMoodle\twebservice->$functionnameUpdate\tImpossibile ripristinare l'utente id_user_moodle: ".$moodleUserNumber['id'], "ERRORE");
                    return false;
                }
            }
            
            if($moodleUserNumber['suspended']>0){
                $this->log->log_all_errors("client.php->creaUtenteMoodle\twebservice->$functionnameUpdate\tUtente disabilitato id_user_moodle: ".$moodleUserNumber['id'], "OK");
                $ret = $this->abilitaUtenteMoodle($moodleUserNumber['id']);
                if($ret){
                    $this->log->log_all_errors("client.php->creaUtenteMoodle\twebservice->$functionnameUpdate\tUtente abilitato id_user_moodle: ".$moodleUserNumber['id'], "OK");
                    $idUtenteMoodle = $moodleUserNumber['id'];
                }else{
                    $this->log->log_all_errors("client.php->creaUtenteMoodle\twebservice->$functionnameUpdate\tImpossibile abilitare l'utente id_user_moodle: ".$moodleUserNumber['id'], "ERRORE");
                    return false;
                }
            }
            
            $newuser = new stdClass();
            $newuser->id  = $moodleUserNumber['id'];
            $newuser->username  = $username;
            $newuser->firstname = $firstname;
            $newuser->lastname  = $lastname;
            $newuser->email     = $email;
            $newuser->password  = $password;
            $newuser->idnumber  = $idnumber;
            
            $users = array($newuser);
            $params = array('users' => $users);

            $path = $this->domainName."/webservice/rest/server.php?wstoken=" . $token['token'] . '&wsfunction=' . $functionnameUpdate;

            $this->log->log_all_errors("OK->CREO PATH WEBSERVICE->$path", "AVVISO");

            $resp = $this->curl->post($path, $params);

            $this->log->log_all_errors("OK->RISPOSTA POST WEBSERVICE->$resp", "AVVISO"); 
            $this->log->log_all_errors("OK->RISPOSTA PARAM: ".var_export($params,true)."", "AVVISO"); 

            $xml_resp = simplexml_load_string($resp);

            if($xml_resp['class'] == 'invalid_parameter_exception') {
                $this->log->log_all_errors("client.php->creaUtenteMoodle\tErrore nel webservice->$functionnameUpdate\t".$xml_resp->MESSAGE, "ERRORE");
                return $resp;
            } else {
                $this->log->log_all_errors("client.php->creaUtenteMoodle\twebservice->$functionnameUpdate\tUtente già presente e ripristinato idNumber: $idnumber", "OK");
                return $idUtenteMoodle; //UTENTE GIA PRESENTE
            }
        }
    }

    public function annullaAbbonamentoMoodle($userid, $cohort) {
        global $dblink;

        $functionname = 'core_cohort_delete_cohort_members';

        $webservice = $dblink->get_row("SELECT id FROM ".MOODLE_DB_NAME.".mdl_external_services WHERE shortname='betaformazione_webservice'", true);
        $token = $dblink->get_row("SELECT token FROM ".MOODLE_DB_NAME.".mdl_external_tokens WHERE externalserviceid='".$webservice['id']."'", true);

        if(!isset($userid)) {
            $this->log->log_all_errors("client.php->annullaAbbonamentoMoodle\tParametro \$userid non settato.", "ERRORE");
            return false;
        }

        if(!isset($cohort)) {
            $this->log->log_all_errors("client.php->annullaAbbonamentoMoodle\tParametro \$cohort non settato.", "ERRORE");
            return false;
        }

        $sql_001 = "SELECT * FROM ".MOODLE_DB_NAME.".mdl_user WHERE id = '$userid' AND deleted='0'";
        $moodleUser = $dblink->get_row($sql_001,true);
        if($moodleUser) {
            $sql_002 = "SELECT * FROM ".MOODLE_DB_NAME.".mdl_cohort WHERE name = '$cohort'";
            $moodleCohort = $dblink->get_row($sql_002, true);
            if ($moodleCohort) {
                if($dblink->num_rows("SELECT * FROM ".MOODLE_DB_NAME.".mdl_cohort_members WHERE userid = '$userid' AND cohortid='".$moodleCohort['id']."'")==0) {
                    $this->log->log_all_errors("client.php->annullaAbbonamentoMoodle\tImpossibile annullare l'iscrizione all'abbonamento in quanto l'utente ($userid) risulta non iscritto alla corte ($cohort).", "AVVISO");
                }
                $member['cohortid'] = $moodleCohort['id'];
                $member['userid'] = $moodleUser['id'];
                $members = array($member);
                $params = array('members' => $members);

                $path = $this->domainName."/webservice/rest/server.php?wstoken=" . $token['token'] . '&wsfunction=' . $functionname;

                $this->curl->post($path, $params);


                if(!$this->curl->get_errno()) {
                    $sql_003 = "SELECT id FROM ".MOODLE_DB_NAME.".mdl_enrol WHERE customint5 = '".$moodleCohort['id']."'";
                    $enrols = $dblink->get_results($sql_003, true);
                    foreach($enrols as $enrol) {
                        $sql_004 = "SELECT id FROM ".MOODLE_DB_NAME.".mdl_user_enrolments WHERE enrolid = '".$enrol->id."' AND userid='".$moodleUser['id']."'";
                        $enrolment = $dblink->get_row($sql_004, true);
                        if(!empty($enrolment)) {
                            $dblink->update("".MOODLE_DB_NAME.".mdl_user_enrolments", array("status" => 1), array("id" => $enrolment['id']));
                        }
                    }

                    // -- Remove value for custom user profile field 'cohorts'.
                    $subcriptionFieldId = $dblink->get_row("SELECT id FROM ".MOODLE_DB_NAME.".mdl_user_info_field WHERE shortname='subscriptionexpiry'",true);
                    if(!empty($subcriptionFieldId)) {

                        $sql_006 = "SELECT id FROM ".MOODLE_DB_NAME.".mdl_user_info_data WHERE userid = '".$moodleUser['id']."' AND fieldid='".$subcriptionFieldId['id']."'";
                        $profile_exists = $dblink->get_row($sql_006,true);
                        if(!empty($profile_exists)) {
                            $dblink->update("".MOODLE_DB_NAME.".mdl_user_info_data", array("data" => ""), array("id"=>$profile_exists['id']));
                        }
                    }

                    $sql_007 = "SELECT id FROM ".MOODLE_DB_NAME.".mdl_user_info_field WHERE shortname = 'Cohort'";
                    $cohortsFieldId = $dblink->get_row($sql_007, true);

                    if(!empty($cohortsFieldId)) {
                        $sql_008 = "SELECT id FROM ".MOODLE_DB_NAME.".mdl_user_info_data WHERE userid = '".$moodleUser['id']."' AND fieldid='".$cohortsFieldId['id']."'";
                        $profile_exists = $dblink->get_row($sql_008,true);
                        if(!empty($profile_exists)) {
                            $dblink->update("".MOODLE_DB_NAME.".mdl_user_info_data", array("data" => ""), array('userid' => $moodleUser['id'], 'fieldid' => $cohortsFieldId['id']));
                        }
                    }
                } else {
                    $this->log->log_all_errors("client.php->annullaAbbonamentoMoodle\tErrore nella chiamata CURL ($path)\t".var_export($param, true)."", "ERRORE");
                    return false;
                }

                $this->log->log_all_errors("client.php->annullaAbbonamentoMoodle\tIscrizione all'abbonamento annullata.", "OK");
                return true;

            } else {
                $this->log->log_all_errors("client.php->annullaAbbonamentoMoodle\tCorte non valida ($corth).", "ERRORE");
                return false;
            }
	} else {
            $this->log->log_all_errors("client.php->annullaAbbonamentoMoodle\tId Utente non valido ($userid).", "ERRORE");
            return false;
	}

    }

    public function iscrizioneAbbonamentoMoodle($userid, $cohort, $data_fine_iscrizione) {
        global $dblink;

        $functionname = 'core_cohort_add_cohort_members';

        if(!isset($userid)) {
            $this->log->log_all_errors("client.php->iscrizioneAbbonamentoMoodle\tParametro \$userid non settato.", "ERRORE");
            return false;
        }

        if(!isset($cohort)) {
            $this->log->log_all_errors("client.php->iscrizioneAbbonamentoMoodle\tParametro \$cohort non settato.", "ERRORE");
            return false;
        }

        $webservice = $dblink->get_row("SELECT id FROM ".MOODLE_DB_NAME.".mdl_external_services WHERE shortname='betaformazione_webservice'", true);
        $token = $dblink->get_row("SELECT token FROM ".MOODLE_DB_NAME.".mdl_external_tokens WHERE externalserviceid='".$webservice['id']."'", true);

        $sql_001 = "SELECT * FROM ".MOODLE_DB_NAME.".mdl_user WHERE id = '$userid' AND deleted='0'";
        $moodleUser = $dblink->get_row($sql_001,true);
        if ($moodleUser) {
            if($dblink->num_rows("SELECT * FROM ".MOODLE_DB_NAME.".mdl_cohort_members WHERE userid = '$userid'")>0) {
                $this->log->log_all_errors("client.php->iscrizioneAbbonamentoMoodle\tUtente già associato alla corte.", "AVVISO");
                
                $sql_002 = "SELECT * FROM ".MOODLE_DB_NAME.".mdl_cohort WHERE name = '$cohort'";
                $moodleCohort = $dblink->get_row($sql_002, true);
                
                if(isset($data_fine_iscrizione) && !empty($data_fine_iscrizione)) {
                    $sub = $dblink->get_row("SELECT id FROM ".MOODLE_DB_NAME.".mdl_user_info_field WHERE shortname='subscriptionexpiry'",true);
                    if($sub['id']>0) {
                        
                        $date = $data_fine_iscrizione;
                        
                            $object = array();
                            $object['userid'] = $moodleUser['id'];
                            $object['fieldid'] = $sub['id'];
                            $object['data'] = $data_fine_iscrizione;
                            $object['dataformat'] = 0;

                            $sql_003 = "SELECT id FROM ".MOODLE_DB_NAME.".mdl_user_info_data WHERE userid = '".$moodleUser['id']."' AND fieldid = '".$sub['id']."'";
                            $expirydate_exists = $dblink->get_row($sql_003, true);
                            if(!empty($expirydate_exists)) {
                                //$object['id'] = $expirydate_exists['id'];
                                //echo "UPDATE ".MOODLE_DB_NAME.".mdl_user_info_data SET userid = '".$moodleUser['id']."', fieldid = '".$sub['id']."', data = '".$data_fine_iscrizione."', dataformat = '0' WHERE id = '".$expirydate_exists['id']."' ";
                                //$dblink->query("UPDATE ".MOODLE_DB_NAME.".mdl_user_info_data SET userid = '".$moodleUser['id']."', fieldid = '".$sub['id']."', data = '".$data_fine_iscrizione."', dataformat = '0' WHERE id = '".$expirydate_exists['id']."' ");
                                $dblink->update("".MOODLE_DB_NAME.".mdl_user_info_data", array("data"=>$data_fine_iscrizione), array("id"=>$expirydate_exists['id']));
                                //$DB->update_record('user_info_data', $object);
                            } else {
                                //echo "INSERT INTO ".MOODLE_DB_NAME.".mdl_user_info_data (`userid`, `fieldid`, `data`, `dataformat`)  VALUES  ('".$moodleUser['id']."', '".$sub['id']."', '".$data_fine_iscrizione."',  '0') ";
                                $dblink->query("INSERT INTO ".MOODLE_DB_NAME.".mdl_user_info_data (`userid`, `fieldid`, `data`, `dataformat`)  VALUES  ('".$moodleUser['id']."', '".$sub['id']."', '".$data_fine_iscrizione."',  '0') ");
                                //$dblink->insert("".MOODLE_DB_NAME.".mdl_user_info_data",$object);
                                //$DB->insert_record('user_info_data', $object);
                            }

                            // -- Add / update value of custom user profile field 'cohorts'.
                            if($cohortsFieldId = $dblink->get_row("SELECT id FROM ".MOODLE_DB_NAME.".mdl_user_info_field WHERE shortname='Cohort'", true)) {

                                $cohort = $dblink->get_row("SELECT name FROM ".MOODLE_DB_NAME.".mdl_cohort WHERE id='".$moodleCohort['id']."'", true);
                                $customFieldEntry = array();
                                $customFieldEntry['userid'] = $moodleUser['id'];
                                $customFieldEntry['fieldid'] = $cohortsFieldId['id'];
                                $customFieldEntry['data'] = $cohort['name'];
                                $customFieldEntry['dataformat'] = 0;

                                $sql_004 = "SELECT id FROM ".MOODLE_DB_NAME.".mdl_user_info_data WHERE userid = '".$moodleUser['id']."' AND fieldid = '".$cohortsFieldId['id']."'";
                                $profile_exists = $dblink->get_row($sql_004, true);

                                if(!empty($profile_exists)) {
                                    //$customFieldEntry->id = $profile_exists->id;
                                    $dblink->update("".MOODLE_DB_NAME.".mdl_user_info_data", $customFieldEntry, array("id"=>$profile_exists['id']));
                                    //$DB->update_record('user_info_data', $customFieldEntry);
                                } else {
                                    $dblink->insert("".MOODLE_DB_NAME.".mdl_user_info_data",$customFieldEntry);
                                    //$DB->insert_record('user_info_data', $customFieldEntry);
                                }
                            }

                            $this->log->log_all_errors("client.php->iscrizioneAbbonamentoMoodle\tIscritto alla corte e inserita la data di scadenza.", "OK");
                            //$response = array('result' => 'success');
                        //}
                    } else {
                        $this->log->log_all_errors("client.php->iscrizioneAbbonamentoMoodle\tIscritto alla corte e ma non è stata impostata una data di scadenza perchè non esiste il campo personalizzato 'subscriptionexpiry'.", "AVVISO");
                        //$response = array('result'=>'success','info'=>get_string('subexpfieldnotexist','local_cohortassignuser'));
                    }
                } else {
                    $this->log->log_all_errors("client.php->iscrizioneAbbonamentoMoodle\tIscritto alla corte ma non è stata impostata una data di scadenza.", "AVVISO");
                    //$response = array('result'=>'success','info'=>get_string('subexpirynotprovided','local_cohortassignuser'));
                }
                
                
                 /*
                JoeB 30/01/2017: additional fix to handle previously unsubscribed users -
                if adding them back to a cohort, also re-instate their previous course enrolments
                */
                $sql_005 = "SELECT id FROM ".MOODLE_DB_NAME.".mdl_enrol WHERE customint5 = '".$moodleCohort['id']."'";
                $enrols = $dblink->get_results($sql_005);
                foreach($enrols as $enrol) {
                    $sql_006 = "SELECT id FROM ".MOODLE_DB_NAME.".mdl_user_enrolments WHERE enrolid = '".$enrol['id']."' AND userid = '".$moodleUser['id']."'";
                    $enrolment = $dblink->get_row($sql_006, true);
                    if(!empty($enrolment)) {
                        $dblink->update(MOODLE_DB_NAME.'.mdl_user_enrolments', array("status"=>"0"), array("id"=>$enrolment['id']));
                    }
                }
                
                $this->log->log_all_errors("client.php->iscrizioneAbbonamentoMoodle\tRinnovato l'abbonamento con scadenza ($data_fine_iscrizione).", "OK");
                return true;
                
            }else{
                $sql_002 = "SELECT * FROM ".MOODLE_DB_NAME.".mdl_cohort WHERE name = '$cohort'";
                $moodleCohort = $dblink->get_row($sql_002, true);
                if ($moodleCohort) {
                    $member = new stdClass();
                    $member->cohorttype['type'] = 'id';
                    $member->cohorttype['value'] = $moodleCohort['id'];
                    $member->usertype['type' ]= 'id';
                    $member->usertype['value'] = $moodleUser['id'];
                    $members = array($member);
                    $params = array('members' => $members);

                    $path = $this->domainName."/webservice/rest/server.php?wstoken=" . $token['token'] . '&wsfunction=' . $functionname;

                    $resp = $this->curl->post($path, $params);

                    if(!$this->curl->get_errno()) {
                        if(isset($data_fine_iscrizione) && !empty($data_fine_iscrizione)) {
                            if($sub = $dblink->get_row("SELECT id FROM ".MOODLE_DB_NAME.".mdl_user_info_field WHERE shortname='subscriptionexpiry'",true)) {

                                $date = DateTime::createFromFormat("d/m/Y", $data_fine_iscrizione);

                                if(!($date !== false && !array_sum($date->getLastErrors()))) {
                                    $this->log->log_all_errors("client.php->iscrizioneAbbonamentoMoodle\tIscritto alla corta ma la data di scadenza non è stata impostata o è volutamente vuota.", "AVVISO");
                                    //$response = array('result'=>'success','info'=>get_string('subexpiryignored','local_cohortassignuser'));;
                                } else {
                                    $object = array();
                                    $object['userid'] = $moodleUser['id'];
                                    $object['fieldid'] = $sub['id'];
                                    $object['data'] = $date;
                                    $object['dataformat'] = 0;

                                    $sql_003 = "SELECT id FROM ".MOODLE_DB_NAME.".mdl_user_info_data WHERE userid = '".$moodleUser['id']."' AND fieldid = '".$sub['id']."'";
                                    $expirydate_exists = $dblink->get_row($sql_003, true);
                                    if(!empty($expirydate_exists)) {
                                        //$object['id'] = $expirydate_exists['id'];
                                        echo "UPDATE ".MOODLE_DB_NAME.".mdl_user_info_data SET userid = '".$moodleUser['id']."', fieldid = '".$sub['id']."', data = '".$data_fine_iscrizione."', dataformat = '0' WHERE id = '".$expirydate_exists['id']."' ";
                                        $dblink->query("UPDATE ".MOODLE_DB_NAME.".mdl_user_info_data SET userid = '".$moodleUser['id']."', fieldid = '".$sub['id']."', data = '".$data_fine_iscrizione."', dataformat = '0' WHERE id = '".$expirydate_exists['id']."' ");
                                        //$dblink->update("".MOODLE_DB_NAME.".mdl_user_info_data", $object, array("id"=>$expirydate_exists['id']));
                                        //$DB->update_record('user_info_data', $object);
                                    } else {
                                        echo "INSERT INTO ".MOODLE_DB_NAME.".mdl_user_info_data (`userid`, `fieldid`, `data`, `dataformat`)  VALUES  ('".$moodleUser['id']."', '".$sub['id']."', '".$data_fine_iscrizione."',  '0') ";
                                        $dblink->query("INSERT INTO ".MOODLE_DB_NAME.".mdl_user_info_data (`userid`, `fieldid`, `data`, `dataformat`)  VALUES  ('".$moodleUser['id']."', '".$sub['id']."', '".$data_fine_iscrizione."',  '0') ");
                                        //$dblink->insert("".MOODLE_DB_NAME.".mdl_user_info_data",$object);
                                        //$DB->insert_record('user_info_data', $object);
                                    }

                                    // -- Add / update value of custom user profile field 'cohorts'.
                                    if($cohortsFieldId = $dblink->get_row("SELECT id FROM ".MOODLE_DB_NAME.".mdl_user_info_field WHERE shortname='Cohort'", true)) {

                                        $cohort = $dblink->get_row("SELECT name FROM ".MOODLE_DB_NAME.".mdl_cohort WHERE id='".$moodleCohort['id']."'", true);
                                        $customFieldEntry = array();
                                        $customFieldEntry['userid'] = $moodleUser['id'];
                                        $customFieldEntry['fieldid'] = $cohortsFieldId['id'];
                                        $customFieldEntry['data'] = $cohort['name'];
                                        $customFieldEntry['dataformat'] = 0;

                                        $sql_004 = "SELECT id FROM ".MOODLE_DB_NAME.".mdl_user_info_data WHERE userid = '".$moodleUser['id']."' AND fieldid = '".$cohortsFieldId['id']."'";
                                        $profile_exists = $dblink->get_row($sql_004, true);

                                        if(!empty($profile_exists)) {
                                            //$customFieldEntry->id = $profile_exists->id;
                                            $dblink->update("".MOODLE_DB_NAME.".mdl_user_info_data", $customFieldEntry, array("id"=>$profile_exists['id']));
                                            //$DB->update_record('user_info_data', $customFieldEntry);
                                        } else {
                                            $dblink->insert("".MOODLE_DB_NAME.".mdl_user_info_data",$customFieldEntry);
                                            //$DB->insert_record('user_info_data', $customFieldEntry);
                                        }
                                    }

                                    $this->log->log_all_errors("client.php->iscrizioneAbbonamentoMoodle\tIscritto alla corte e inserita la data di scadenza.", "OK");
                                    //$response = array('result' => 'success');
                                }
                            } else {
                                $this->log->log_all_errors("client.php->iscrizioneAbbonamentoMoodle\tIscritto alla corte e ma non è stata impostata una data di scadenza perchè non esiste il campo personalizzato 'subscriptionexpiry'.", "AVVISO");
                                //$response = array('result'=>'success','info'=>get_string('subexpfieldnotexist','local_cohortassignuser'));
                            }
                        } else {
                            $this->log->log_all_errors("client.php->iscrizioneAbbonamentoMoodle\tIscritto alla corte ma non è stata impostata una data di scadenza.", "AVVISO");
                            //$response = array('result'=>'success','info'=>get_string('subexpirynotprovided','local_cohortassignuser'));
                        }

                        /*
                        JoeB 30/01/2017: additional fix to handle previously unsubscribed users -
                        if adding them back to a cohort, also re-instate their previous course enrolments
                        */
                        $sql_005 = "SELECT id FROM ".MOODLE_DB_NAME.".mdl_enrol WHERE customint5 = '".$moodleCohort['id']."'";
                        $enrols = $dblink->get_results($sql_005, true);
                        foreach($enrols as $enrol) {
                            $sql_006 = "SELECT id FROM ".MOODLE_DB_NAME.".mdl_user_enrolments WHERE enrolid = '".$enrol->id."' AND userid = '".$moodleUser['id']."'";
                            $enrolment = $dblink->get_row($sql_006, true);
                            if(!empty($enrolment)) {
                                //change the enrolment status back to 0 - active
                                $enrolmentUpadate = array();
                                $enrolmentUpadate['status'] = 0;
                                $dblink->update(MOODLE_DB_NAME.'.mdl_user_enrolments', $enrolmentUpadate, array("id"=>$enrolment['id']));
                            }
                        }

                    } else {
                        $this->log->log_all_errors("client.php->iscrizioneAbbonamentoMoodle\tErrore nella chiamata CURL ($path)\t".var_export($param, true)."", "ERRORE");
                        return false;
                    }

                    $this->log->log_all_errors("client.php->iscrizioneAbbonamentoMoodle\tIscritto all'abbonamento.", "OK");
                    return true;
                } else {
                    $this->log->log_all_errors("client.php->iscrizioneAbbonamentoMoodle\tCorte non valida ($corth).", "ERRORE");
                    return false;
                }
            }
        } else {
            $this->log->log_all_errors("client.php->iscrizioneAbbonamentoMoodle\tId Utente non valido ($userid).", "ERRORE");
            return false;
        }
    }
    
    /*public function prorogaAbbonamentoMoodle($userid, $cohort, $data_fine_iscrizione) {
        global $dblink;

        if(!isset($userid)) {
            $this->log->log_all_errors("client.php->prorogaAbbonamentoMoodle\tParametro \$userid non settato.", "ERRORE");
            return false;
        }

        if(!isset($cohort)) {
            $this->log->log_all_errors("client.php->prorogaAbbonamentoMoodle\tParametro \$cohort non settato.", "ERRORE");
            return false;
        }

        $sql_001 = "SELECT * FROM ".MOODLE_DB_NAME.".mdl_user WHERE id = '$userid' AND deleted='0'";
        $moodleUser = $dblink->get_row($sql_001,true);
        if ($moodleUser) {
            if($dblink->num_rows("SELECT * FROM ".MOODLE_DB_NAME.".mdl_cohort_members WHERE userid = '$userid'")>0) {
                
                $sql_002 = "SELECT * FROM ".MOODLE_DB_NAME.".mdl_cohort WHERE name = '$cohort'";
                $moodleCohort = $dblink->get_row($sql_002, true);
                
                if(isset($data_fine_iscrizione) && !empty($data_fine_iscrizione)) {
                    $sub = $dblink->get_row("SELECT id FROM ".MOODLE_DB_NAME.".mdl_user_info_field WHERE shortname='subscriptionexpiry'",true);
                    if($sub['id']>0) {

                        $sql_003 = "SELECT id FROM ".MOODLE_DB_NAME.".mdl_user_info_data WHERE userid = '".$moodleUser['id']."' AND fieldid = '".$sub['id']."'";
                        $expirydate_exists = $dblink->get_row($sql_003, true);
                        if(!empty($expirydate_exists)) {
                            //echo "UPDATE ".MOODLE_DB_NAME.".mdl_user_info_data SET userid = '".$moodleUser['id']."', fieldid = '".$sub['id']."', data = '".$data_fine_iscrizione."', dataformat = '0' WHERE id = '".$expirydate_exists['id']."' ";
                            $dblink->query("UPDATE ".MOODLE_DB_NAME.".mdl_user_info_data SET data = '".$data_fine_iscrizione."' WHERE id = '".$expirydate_exists['id']."' ");
                        }

                        if(isset($cohort) && !empty($cohort)){
                            // -- Add / update value of custom user profile field 'cohorts'.
                            $cohortsFieldId = $dblink->get_row("SELECT id FROM ".MOODLE_DB_NAME.".mdl_user_info_field WHERE shortname='Cohort'", true);
                            if(!empty($cohortsFieldId)) {

                                $sql_004 = "SELECT id FROM ".MOODLE_DB_NAME.".mdl_user_info_data WHERE userid = '".$moodleUser['id']."' AND fieldid = '".$cohortsFieldId['id']."'";
                                $profile_exists = $dblink->get_row($sql_004, true);

                                if(!empty($profile_exists)) {
                                    $dblink->update("".MOODLE_DB_NAME.".mdl_user_info_data", array("data"=>$cohort), array("id"=>$profile_exists['id']));
                                }
                            }
                        }

                        $this->log->log_all_errors("client.php->prorogaAbbonamentoMoodle\tAbbonamento prorogato con scadenza ($data_fine_iscrizione) UserMoodle ($userid).", "OK");
                    } else {
                        $this->log->log_all_errors("client.php->prorogaAbbonamentoMoodle\tImpossibile prorogare l'abbonamento. Non esiste il campo personalizzato 'subscriptionexpiry' sul Database.", "ERRORE");
                        return false;
                    }
                } else {
                    $this->log->log_all_errors("client.php->prorogaAbbonamentoMoodle\tData di proroga abbonamento non impostata UserMoodle ($userid).", "AVVISO");
                    return false;
                }
                
                 /*
                Verifico se ci sono corsi disattivati e provvedo alla riattivazione.
                */
        /*        $sql_005 = "SELECT id FROM ".MOODLE_DB_NAME.".mdl_enrol WHERE customint5 = '".$moodleCohort['id']."'";
                $enrols = $dblink->get_results($sql_005);
                foreach($enrols as $enrol) {
                    $sql_006 = "SELECT id FROM ".MOODLE_DB_NAME.".mdl_user_enrolments WHERE enrolid = '".$enrol['id']."' AND userid = '".$moodleUser['id']."'";
                    $enrolment = $dblink->get_row($sql_006, true);
                    if(!empty($enrolment)) {
                        $dblink->update('".MOODLE_DB_NAME.".mdl_user_enrolments', array("status" => "0"), array("id"=>$enrolment['id']));
                    }
                }
                
                $this->log->log_all_errors("client.php->prorogaAbbonamentoMoodle\tProrogato l'abbonamento con scadenza ($data_fine_iscrizione).", "OK");
                return true;
                
            }else{
                $this->log->log_all_errors("client.php->prorogaAbbonamentoMoodle\tId Utente non valido ($userid).", "ERRORE");
                return false;
            }
        } else {
            $this->log->log_all_errors("client.php->prorogaAbbonamentoMoodle\tId Utente non valido ($userid).", "ERRORE");
            return false;
        }
    }*/

    public function iscrizioneCorsoMoodle($userid, $courseId, $cohort, $tipoVendita, $data_fine_iscrizione) {
        global $dblink;

        $functionname = 'enrol_manual_enrol_users';

        $webservice = $dblink->get_row("SELECT id FROM ".MOODLE_DB_NAME.".mdl_external_services WHERE shortname='betaformazione_webservice'", true);
        $token = $dblink->get_row("SELECT token FROM ".MOODLE_DB_NAME.".mdl_external_tokens WHERE externalserviceid='".$webservice['id']."'", true);

        if(!isset($userid)) {
            $this->log->log_all_errors("client.php->iscrizioneCorsoMoodle\tParametro \$userid non settato.", "ERRORE");
            return false;
        }

        if(!isset($courseId)) {
            $this->log->log_all_errors("client.php->iscrizioneCorsoMoodle\tParametro \$courseId non settato.", "ERRORE");
            return false;
        }

        $sql_001 = "SELECT * FROM ".MOODLE_DB_NAME.".mdl_user WHERE id = '$userid' AND deleted='0'";
        $moodleUser = $dblink->get_row($sql_001,true);
        if ($moodleUser) {
            $sql_002 = "SELECT * FROM ".MOODLE_DB_NAME.".mdl_course WHERE id = '$courseId'";
            $moodleCourse = $dblink->get_row($sql_002, true);
            
            if ($moodleCourse) {
                $sql_003 = "SELECT * FROM ".MOODLE_DB_NAME.".mdl_enrol WHERE enrol = 'manual' AND courseid = '".$moodleCourse['id']."'";
                $manual = $dblink->get_row($sql_003, true);
                //Enrolment exists
                $sql_004 = "SELECT * FROM ".MOODLE_DB_NAME.".mdl_user_enrolments WHERE userid = '".$moodleUser['id']."' AND enrolid = '".$manual['id']."'";
                $enrolment = $dblink->get_row($sql_004, true);
                if(!empty($enrolment)) {
                    //Time end set, update enrolment instead of creating
                    if(isset($data_fine_iscrizione) && !empty($data_fine_iscrizione)) {
                        //$date = DateTime::createFromFormat('d/m/Y',$data_fine_iscrizione);
                        //$dblink->update(MOODLE_DB_NAME.'.mdl_user_enrolments',array("timeend"=>$date->getTimestamp()), array("id"=>$enrolment['id']));
                        $dblink->update(MOODLE_DB_NAME.'.mdl_user_enrolments',array("timeend"=>$data_fine_iscrizione), array("id"=>$enrolment['id']));
                        //$response = array('result'=>'success');
                    } else {
                        $this->log->log_all_errors("client.php->iscrizioneCorsoMoodle\tUtente ($userid) attivato per questo corso ($courseId) senza ggiornare la data di scadenza.", "AVVISO");
                    }
                    
                    $dblink->update(MOODLE_DB_NAME.'.mdl_user_enrolments',array("status"=>"0"), array("id"=>$enrolment['id']));
                    
                } else {
                        //Enrolment doesn't exist, create it
                        $enrolment = new stdClass();
                        $enrolment->roleid = 5;
                        $enrolment->userid = $moodleUser['id'];
                        $enrolment->courseid = $moodleCourse['id'];
                        $enrolment->timestart = time();

                        if(isset($data_fine_iscrizione)) {
                                //$date = DateTime::createFromFormat('d/m/Y',$data_fine_iscrizione);
                                //$enrolment->timeend = $date->getTimestamp();
                                $enrolment->timeend = $data_fine_iscrizione;
                        }
                        else {
                                $enrolment->timeend = 0;
                        }
                        //$enrolment->timeend = (isset($user_register->params->timeend)) ? $user_register->params->timeend : 0;

                        $params = array('enrolments' => array($enrolment));
                        $path = $this->domainName . "/webservice/rest/server.php?wstoken=" . $token['token'] . '&wsfunction=' . $functionname;

                        $this->log->log_all_errors("client.php->iscrizioneCorsoMoodle\tUtente ($userid) inserisci il corso ($courseId) su mdl_user_enrolments usando il webservice ($path) - PARAM: ".var_export($params,true).".", "AVVISO");
                        
                        $resp = $this->curl->post($path, $params);
                        
                        $this->log->log_all_errors("$resp","AVVISO");

                        if(!$this->curl->get_errno()){
                            $this->log->log_all_errors("client.php->iscrizioneCorsoMoodle\tUtente ($userid) inserisci il corso ($courseId) su mdl_user_enrolments iscritto con il webservice! - PARAM: ".var_export($params,true)."", "OK");
                            //$response = array('result'=>'success');
                        } else {
                            $this->log->log_all_errors("client.php->iscrizioneCorsoMoodle\tUtente ($userid) errore nell'iscrizone al corso ($courseId) riga su tabella mdl_user_enrolments non creata.", "ERRORE");
                            return false;
                            //$response = array('result'=>'fail','info'=>get_string('curlerror','local_enroluser'));
                        }
                }

                if(isset($cohort) && !empty($cohort)) {
                // -- Add / update value of custom user profile field 'cohorts'.
                    $sql_005 = "SELECT id FROM ".MOODLE_DB_NAME.".mdl_user_info_field WHERE shortname = 'Cohort'";
                    $cohortsFieldId = $dblink->get_row($sql_005, true);

                    if(!empty($cohortsFieldId)) {

                        $customFieldEntry = array();
                        $customFieldEntry['userid'] = $moodleUser['id'];
                        $customFieldEntry['fieldid'] = $cohortsFieldId['id'];
                        $customFieldEntry['data'] = $cohort;
                        $customFieldEntry['dataformat'] = 0;

                        $sql_006 = "SELECT id FROM ".MOODLE_DB_NAME.".mdl_user_info_data WHERE userid = '".$moodleUser['id']."' AND fieldid='".$cohortsFieldId['id']."'";
                        $profile_exists = $dblink->get_row($sql_006,true);
                        if(!empty($profile_exists)) {
                            //$customFieldEntry->id = $profile_exists->id;
                            $dblink->update("".MOODLE_DB_NAME.".mdl_user_info_data", $customFieldEntry, array("id"=>$profile_exists['id']));
                            //$DB->update_record('user_info_data', $customFieldEntry);
                        } else {
                            $dblink->insert("".MOODLE_DB_NAME.".mdl_user_info_data",$customFieldEntry);
                            //$DB->insert_record('user_info_data', $customFieldEntry);
                        }
                    }
                }
                
                if(isset($tipoVendita) && !empty($tipoVendita)) {
                    $sql_007 = "SELECT id FROM ".MOODLE_DB_NAME.".mdl_user_info_field WHERE shortname = 'tipovendita'";
                    $tipoVenditaFieldId = $dblink->get_row($sql_007, true);

                    if(!empty($tipoVenditaFieldId)) {

                        $customFieldEntry = array();
                        $customFieldEntry['userid'] = $moodleUser['id'];
                        $customFieldEntry['fieldid'] = $tipoVenditaFieldId['id'];
                        $customFieldEntry['data'] = $tipoVendita;
                        $customFieldEntry['dataformat'] = 0;

                        $sql_008 = "SELECT id FROM ".MOODLE_DB_NAME.".mdl_user_info_data WHERE userid = '".$moodleUser['id']."' AND fieldid='".$tipoVenditaFieldId['id']."'";
                        $profile_exists = $dblink->get_row($sql_008,true);
                        if(!empty($profile_exists)) {
                            //$customFieldEntry->id = $profile_exists->id;
                            $dblink->update("".MOODLE_DB_NAME.".mdl_user_info_data", $customFieldEntry, array("id"=>$profile_exists['id']));
                            //$DB->update_record('user_info_data', $customFieldEntry);
                        } else {
                            $dblink->insert("".MOODLE_DB_NAME.".mdl_user_info_data",$customFieldEntry);
                            //$DB->insert_record('user_info_data', $customFieldEntry);
                        }
                    }
                }
                
                /*if(isset($data_fine_iscrizione)) {
                // -- Add / update value of custom user profile field 'subscriptionexpiry'.
                    $sql_007 = "SELECT id FROM ".MOODLE_DB_NAME.".mdl_user_info_field WHERE shortname = 'subscriptionexpiry'";
                    $cohortsFieldId = $dblink->get_row($sql_007, true);

                    if(!empty($cohortsFieldId)) {

                        $customFieldEntry = array();
                        $customFieldEntry['userid'] = $moodleUser['id'];
                        $customFieldEntry['fieldid'] = $cohortsFieldId['id'];
                        $customFieldEntry['data'] = $data_fine_iscrizione;
                        $customFieldEntry['dataformat'] = 0;

                        $sql_008 = "SELECT id FROM ".MOODLE_DB_NAME.".mdl_user_info_data WHERE userid = '".$moodleUser['id']."' AND fieldid='".$cohortsFieldId['id']."'";
                        $profile_exists = $dblink->get_row($sql_008,true);

                        if(!empty($profile_exists)) {
                            //$customFieldEntry->id = $profile_exists->id;
                            $dblink->update("".MOODLE_DB_NAME.".mdl_user_info_data", $customFieldEntry, array("id"=>$profile_exists['id']));
                            //$DB->update_record('user_info_data', $customFieldEntry);
                        } else {
                            $dblink->insert("".MOODLE_DB_NAME.".mdl_user_info_data",$customFieldEntry);
                            //$DB->insert_record('user_info_data', $customFieldEntry);
                        }
                    }
                }*/
                $this->log->log_all_errors("client.php->iscrizioneCorsoMoodle\tIl corso ($courseId) è stato abilitato per l'utente ($userid).", "OK");
                return true;
            } else {
                $this->log->log_all_errors("client.php->iscrizioneCorsoMoodle\tIl corso ($courseId) non è presente nel database.", "ERRORE");
                return false;
            }
        } else {
            $this->log->log_all_errors("client.php->iscrizioneCorsoMoodle\tL'utente ($userid) non è presente nel database.", "ERRORE");
            return false;
        }
    }
    
    public function annullaCorsoMoodle($userid, $courseId, $data_fine_iscrizione = '') {
        global $dblink;

        if(!isset($userid)) {
            $this->log->log_all_errors("client.php->annullaCorsoMoodle\tParametro \$userid non settato.", "ERRORE");
            return false;
        }

        if(!isset($courseId)) {
            $this->log->log_all_errors("client.php->annullaCorsoMoodle\tParametro \$courseId non settato.", "ERRORE");
            return false;
        }

        $sql_001 = "SELECT * FROM ".MOODLE_DB_NAME.".mdl_user WHERE id = '$userid' AND deleted='0'";
        $moodleUser = $dblink->get_row($sql_001,true);
        if ($moodleUser) {
            $sql_002 = "SELECT * FROM ".MOODLE_DB_NAME.".mdl_course WHERE id = '$courseId'";
            $moodleCourse = $dblink->get_row($sql_002, true);
            
            if ($moodleCourse) {
                $sql_003 = "SELECT * FROM ".MOODLE_DB_NAME.".mdl_enrol WHERE enrol = 'manual' AND courseid = '".$moodleCourse['id']."'";
                $manual = $dblink->get_row($sql_003, true);
                //Enrolment exists
                $sql_004 = "SELECT * FROM ".MOODLE_DB_NAME.".mdl_user_enrolments WHERE userid = '".$moodleUser['id']."' AND enrolid = '".$manual['id']."'";
                $enrolment = $dblink->get_row($sql_004, true);
                if(!empty($enrolment)) {
                    //Time end set, update enrolment instead of creating
                    if(isset($data_fine_iscrizione) && !empty($data_fine_iscrizione)) {
                        $dblink->update(MOODLE_DB_NAME.'.mdl_user_enrolments',array("timeend"=>$data_fine_iscrizione), array("id"=>$enrolment['id']));
                    }
                    
                    $dblink->update(MOODLE_DB_NAME.'.mdl_user_enrolments',array("status"=>"1"), array("id"=>$enrolment['id']));
                } else {
                    $this->log->log_all_errors("client.php->annullaCorsoMoodle\tIl corso ($courseId) non è presente nella user_enrolments database.", "ERRORE");
                    return false;
                }
                $this->log->log_all_errors("client.php->annullaCorsoMoodle\tIl corso ($courseId) è stato disabilitato per l'utente ($userid).", "OK");
                return true;
            } else {
                $this->log->log_all_errors("client.php->annullaCorsoMoodle\tIl corso ($courseId) non è presente nel database.", "ERRORE");
                return false;
            }
        } else {
            $this->log->log_all_errors("client.php->annullaCorsoMoodle\tL'utente ($userid) non è presente nel database.", "ERRORE");
            return false;
        }
    }
    
    public function prorogaCorsoMoodle($userid, $courseId, $data_fine_iscrizione = '') {
        global $dblink;

        if(!isset($userid)) {
            $this->log->log_all_errors("client.php->prorogaCorsoMoodle\tParametro \$userid non settato.", "ERRORE");
            return false;
        }

        if(!isset($courseId)) {
            $this->log->log_all_errors("client.php->prorogaCorsoMoodle\tParametro \$courseId non settato.", "ERRORE");
            return false;
        }

        $sql_001 = "SELECT * FROM ".MOODLE_DB_NAME.".mdl_user WHERE id = '$userid' AND deleted='0'";
        $moodleUser = $dblink->get_row($sql_001,true);
        if ($moodleUser) {
            $sql_002 = "SELECT * FROM ".MOODLE_DB_NAME.".mdl_course WHERE id = '$courseId'";
            $moodleCourse = $dblink->get_row($sql_002, true);
            
            if ($moodleCourse) {
                $sql_003 = "SELECT * FROM ".MOODLE_DB_NAME.".mdl_enrol WHERE enrol = 'manual' AND courseid = '".$moodleCourse['id']."'";
                $manual = $dblink->get_row($sql_003, true);
                //Enrolment exists
                $sql_004 = "SELECT * FROM ".MOODLE_DB_NAME.".mdl_user_enrolments WHERE userid = '".$moodleUser['id']."' AND enrolid = '".$manual['id']."'";
                $enrolment = $dblink->get_row($sql_004, true);
                if(!empty($enrolment)) {
                    //Time end set, update enrolment instead of creating
                    if(isset($data_fine_iscrizione) && !empty($data_fine_iscrizione)) {
                        $dblink->update(MOODLE_DB_NAME.'.mdl_user_enrolments',array("timeend"=>$data_fine_iscrizione), array("id"=>$enrolment['id']));
                    }
                    
                    $dblink->update(MOODLE_DB_NAME.'.mdl_user_enrolments',array("status"=>"0"), array("id"=>$enrolment['id']));
                } else {
                    $this->log->log_all_errors("client.php->prorogaCorsoMoodle\tIl corso ($courseId) non è presente nella user_enrolments database.", "ERRORE");
                    return false;
                }
                $this->log->log_all_errors("client.php->prorogaCorsoMoodle\tIl corso ($courseId) è stato prorogato per l'utente ($userid).", "OK");
                return true;
            } else {
                $this->log->log_all_errors("client.php->prorogaCorsoMoodle\tIl corso ($courseId) non è presente nel database.", "ERRORE");
                return false;
            }
        } else {
            $this->log->log_all_errors("client.php->prorogaCorsoMoodle\tL'utente ($userid) non è presente nel database.", "ERRORE");
            return false;
        }
    }

    function disabilitaUtenteMoodle($userid) {
        global $dblink;
        
        if(!isset($userid)) {
            $this->log->log_all_errors("client.php->disabilitaUtenteMoodle\tParametro \$userid non settato.", "ERRORE");
            return false;
        }
        
        $sql_001 = "SELECT * FROM ".MOODLE_DB_NAME.".mdl_user WHERE id = '$userid' AND suspended='0'";
        $moodleUser = $dblink->get_row($sql_001,true);
        if ($moodleUser) {
            $dblink->update("".MOODLE_DB_NAME.".mdl_user", array("suspended"=>"1"), array("id"=>$userid));
            return true;
        }else{
            $this->log->log_all_errors("client.php->disabilitaUtenteMoodle\tUtente non presente nel database o non è in stato Abilitato Utente ($userid).", "ERRORE");
            return false;
        }
        
    }
    
    function abilitaUtenteMoodle($userid) {
        global $dblink;
        
        if(!isset($userid)) {
            $this->log->log_all_errors("client.php->abilitaUtenteMoodle\tParametro \$userid non settato.", "ERRORE");
            return false;
        }
        
        $sql_001 = "SELECT * FROM ".MOODLE_DB_NAME.".mdl_user WHERE id = '$userid' AND suspended='1'";
        $moodleUser = $dblink->get_row($sql_001,true);
        if ($moodleUser) {
            $dblink->update("".MOODLE_DB_NAME.".mdl_user", array("suspended"=>"0"), array("id"=>$userid));
            return true;
        }else{
            $this->log->log_all_errors("client.php->abilitaUtenteMoodle\tUtente non presente nel database o non è in stato Disabilitato Utente ($userid).", "ERRORE");
            return false;
        }
        
    }
    
    function ripristinaUtenteMoodle($userid) {
        global $dblink;
        
        if(!isset($userid)) {
            $this->log->log_all_errors("client.php->ripristinaUtenteMoodle\tParametro \$userid non settato.", "ERRORE");
            return false;
        }
        
        $sql_001 = "SELECT * FROM ".MOODLE_DB_NAME.".mdl_user WHERE id = '$userid' AND deleted='1'";
        $moodleUser = $dblink->get_row($sql_001,true);
        if ($moodleUser) {
            $dblink->update("".MOODLE_DB_NAME.".mdl_user", array("deleted"=>"0"), array("id"=>$userid));
            return true;
        }else{
            $this->log->log_all_errors("client.php->ripristinaUtenteMoodle\tUtente non presente nel database o non è in stato Disabilitato Utente ($userid).", "ERRORE");
            return false;
        }
    }
    
}

function recupero_percentuale_avanzamento_corso_utente($userid, $courseid, $percentage = false){
    global $dblink;

    $sql_001 = "SELECT id, time_to_complete_modules FROM ".MOODLE_DB_NAME.".mdl_course WHERE id = '$courseid'";

    $json_string = $dblink->get_row($sql_001,true);
    $arr_objs = json_decode($json_string['time_to_complete_modules'], true);

    $time_completed = 0;
    $total_time = 0;
    $percentage_completion = 0;

    $cohorts = array();
    $sql = "SELECT uid.id, uid.data AS cohort FROM ".MOODLE_DB_NAME.".mdl_user_info_data uid
            INNER JOIN ".MOODLE_DB_NAME.".mdl_user_info_field uif on uid.fieldid = uif.id
            WHERE uif.name = 'Cohort'
            AND uid.userid =$userid";
    $records = $dblink->get_results($sql,true);

    foreach ($records as $record){
        $cohorts[] = strtolower(trim($record->cohort));
    }

    if(!empty($arr_objs)){
        foreach ($arr_objs as $arr_obj) {
            $istanceid = $arr_obj['istance_id'];
            $sql_003 = "SELECT id FROM ".MOODLE_DB_NAME.".mdl_modules WHERE name = '".$arr_obj['module_name']."'";
            $mod = $dblink->get_row($sql_003,true);
            if(!empty($mod)) {
                $sql_004 = "SELECT id FROM ".MOODLE_DB_NAME.".mdl_course_modules WHERE course = '".$courseid."' AND module = '".$mod['id']."' AND instance = '$istanceid'";
                $cmod = $dblink->get_row($sql_004,true);
                if(!empty($cmod)) {

                    //echo "<li>userid = $userid</li>";
                    //echo "<li>cmod['id'] = ".$cmod['id']."</li>";
                    //echo "<li>cohorts = $cohorts</li>";
                    /*$cm = get_coursemodule_from_id(null, $cmod->id);
                    $modinfo = get_fast_modinfo($course);
                    $cm = $modinfo->get_cm($cm->id);*/

                    $is_available = betaformazioneCorsoCorteAttivi($userid, $cmod['id'], $cohorts);
                    if ($is_available === false)
                        continue;
                    
                    //echo "<li>is_available = $is_available</li>";
                    //echo "<li>arr_obj['value'] = ".$arr_obj['value']."</li>";

                    $total_time += intval($arr_obj['value']);
                    
                    //echo "<li>total_time = ".$total_time."</li>";

                    $sql = "SELECT cm.id AS coursemoduleid, cm.instance, m.name, cmc.id AS completionid, cmc.completionstate
                    FROM ".MOODLE_DB_NAME.".mdl_course_modules cm
                    INNER JOIN ".MOODLE_DB_NAME.".mdl_modules m ON cm.module = m.id
                    INNER JOIN ".MOODLE_DB_NAME.".mdl_course_modules_completion cmc ON cm.id = cmc.coursemoduleid
                    WHERE cm.course ='$courseid'
                    AND cm.instance ='$istanceid'
                    AND cmc.userid ='$userid'
                    AND cmc.completionstate = 1";

                    if ($dblink->num_rows($sql)>0) {
                        $time_completed += intval($arr_obj['value']);
                        //echo "<li>arr_obj['value'] = ".$arr_obj['value']."</li>";
                        //echo "<li>time_completed = ".$time_completed."</li>";
                    }
                }
            }
        }
    }

    $percentage_completion = ($total_time > 0) ? round((($time_completed * 100) / $total_time), 0, PHP_ROUND_HALF_ODD) : 0;
    if(!$percentage) $percentage_completion /= 100;
    $course_time = betaformazioneConvertiInOreMinuti($total_time);

    //return array($percentage_completion, $course_time);
    return $percentage_completion;
}

function betaformazioneConvertiInOreMinuti($time, $format = '%02d:%02d') {
    if ($time < 1) {
        return;
    }
    $hours = floor($time / 60);
    $minutes = ($time % 60);
    return sprintf($format, $hours, $minutes);
}

function betaformazioneRitornaClasse($user_id){
    global $dblink;
    $records = null;
    $cohorts = array();
    $sql1 = "SELECT cm.id,
              cm.cohortid,
              c.name AS cohort
            FROM ".MOODLE_DB_NAME.".mdl_cohort_members cm
              JOIN ".MOODLE_DB_NAME.".mdl_cohort c ON cm.cohortid = c.id
            WHERE
              cm.userid = '$user_id'
            ";
    $sql2 = "SELECT uid.id,
              uid.data AS cohort
            FROM ".MOODLE_DB_NAME.".mdl_user_info_data uid
              JOIN ".MOODLE_DB_NAME.".mdl_user_info_field uif on uid.fieldid = uif.id
            WHERE
              uid.userid = '$user_id'
            AND
              uif.name = 'Cohort'
            ";

    if($dblink->num_rows($sql1)>0){
        $records = $dblink->get_results($sql1, true);
        if(!empty($records)){
            foreach ($records as $record){
                $cohorts[$record->cohort] = $record->cohortid;
            }
        }
    }
    if($dblink->num_rows($sql2)>0){
        $records = $dblink->get_results($sql2, ture);
        foreach ($records as $key => $record){
            $sql_3 = "SELECT id FROM ".MOODLE_DB_NAME.".mdl_cohort WHERE name = '".$record->cohort."' LIMIT 1";
            $cohort_id = $dblink->get_row($sql_3,true);
            if(!empty($cohort_id))
                $record->cohortid = $cohort_id['id'];
            else
                unset($records[$key]);
        }
        if(!empty($records)){
            foreach ($records as $record){
                $cohorts[$record->cohort] = $record->cohortid;
            }
        }
    }

    return $cohorts;
}

function betaformazioneCorsoCorteAttivi($userid, $modid, $cohorts = null) {
    global $dblink;

    $is_available = null;

    /* JoeB 04/01/2017 - course page performance fix - remove the code below that looks up the table every single time */
    if ($cohorts == null) {
        $cohorts = array();
        $sql = "SELECT uid.id, uid.data AS cohort FROM ".MOODLE_DB_NAME.".mdl_user_info_data uid
                INNER JOIN ".MOODLE_DB_NAME.".mdl_user_info_field uif on uid.fieldid = uif.id
                WHERE uif.name = 'Cohort'
                AND uid.userid = '$userid'";
        $records = $dblink->get_results($sql,true);

        foreach ($records as $record){
            $cohorts[] = strtolower(trim($record->cohort));
        }
    }
    $cohorts = betaformazioneRitornaClasse($userid);
    $cohorts = array_flip($cohorts);

    if(!empty($cohorts)) {
        $sql_002 = "SELECT availability FROM ".MOODLE_DB_NAME.".mdl_course_modules WHERE id='".$modid."'";
        $json = $dblink->get_row($sql_002,true);
        if(!empty($json)) {
            $tree = json_decode($json['availability'], true);
            //print_object($tree);
            $o0 = $tree['op'];
            $is_cohort = false;
            $num_restrictions = count($tree['c']);
            if(isset($tree['c'])) {
                foreach ($tree['c'] as $s1) {
                    if (isset($s1['c'])) {
                        foreach ($s1['c'] as $s2) {
                            if (isset($s2['c'])) {
                                foreach ($s2['c'] as $s3) {
                                    if (isset($s3['cf']) && strtolower($s3['cf']) === 'cohort' && $s3['op'] === 'isequalto') {
                                        $is_cohort = true;
                                        $o2 = $s2['op'];
                                        $bReverse = (strpos($o2, '!') === false) ? false : true;
                                        if ($bReverse) {
                                            if (in_array($s3['v'], $cohorts)) {
                                                $is_available = false;
                                                break;
                                            } else {
                                                $is_available = true;
                                            }
                                        } else {
                                            if (in_array($s3['v'], $cohorts)) {
                                                $is_available = true;
                                                break;
                                            } else {
                                                $is_available = false;
                                            }
                                        }
                                        if ($num_restrictions > 1 && $o0 === '|') {
                                            $is_available = true;
                                        }
                                    }
                                }
                            } else {
                                if (isset($s2['cf']) && strtolower($s2['cf']) === 'cohort' && $s2['op'] === 'isequalto') {
                                    $is_cohort = true;
                                    $o1 = $s1['op'];
                                    $bReverse = (strpos($o1, '!') === false) ? false : true;
                                    if ($bReverse) {
                                        if (in_array($s2['v'], $cohorts)) {
                                            $is_available = false;
                                            break;
                                        } else {
                                            $is_available = true;
                                        }
                                    } else {
                                        if (in_array($s2['v'], $cohorts)) {
                                            $is_available = true;
                                            break;
                                        } else {
                                            $is_available = false;
                                        }
                                    }
                                }
                                if ($num_restrictions > 1 && $o0 === '|') {
                                    $is_available = true;
                                }
                            }
                        }
                    } else {
                        if (isset($s1['cf']) && strtolower($s1['cf']) === 'cohort' && $s1['op'] === 'isequalto') {
                            $is_cohort = true;
                            $bReverse = (strpos($o0, '!') === false) ? false : true;
                            if ($bReverse) {
                                if (in_array($s1['v'], $cohorts)) {
                                    $is_available = false;
                                    break;
                                } else {
                                    $is_available = true;
                                }
                            } else {
                                if (in_array($s1['v'], $cohorts)) {
                                    $is_available = true;
                                    break;
                                } else {
                                    $is_available = false;
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    return $is_available;
}

function verificaEmailClient($address) {

    return (preg_match('#^[-!\#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+'.
                 '(\.[-!\#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+)*'.
                  '@'.
                  '[-!\#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+\.'.
                  '[-!\#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+$#',
                  $address));
}