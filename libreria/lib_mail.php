<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require BASE_ROOT.'classi/phpmailer/src/Exception.php';
require BASE_ROOT.'classi/phpmailer/src/PHPMailer.php';
require BASE_ROOT.'classi/phpmailer/src/SMTP.php';


define("SERVER_HOST_MAIL", BASE_SERVER_HOST_MAIL);
define("SECURE_SMTP_MAIL", BASE_SECURE_SMTP_MAIL);
define("PORT_MAIL", BASE_PORT_MAIL);
define("PASS_MAIL", BASE_PASS_MAIL);
define("USER_MAIL", BASE_USER_MAIL);

/*
define("SERVER_HOST_MAIL", "tls://smtp.office365.com");
define("SECURE_SMTP_MAIL", "tls");
define("PORT_MAIL", "587");
if(strlen($_SESSION['passwd_email_utente'])>2 && strpos($_SESSION['email_utente'],"@betaformazione.com")>0){
    define("PASS_MAIL", $_SESSION['passwd_email_utente']);
    define("USER_MAIL", $_SESSION['email_utente']);
}else{
    define("USER_MAIL", "erp@betaformazione.com");
    define("PASS_MAIL", 'Moda5221');
}*/

//inviare email fattura
function inviaEmailPreventivo($mitt, $dest, $dest_cc, $dest_bcc, $ogg, $mess, $allegato_1, $allegato_2, $PasswdEmailUtente) {
    //$verifica = preg_match("^[^@ ]+@[^@ ]+\.[^@ \.]+$", $mitt);
    $verifica = preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i", $mitt);

    if ($verifica) {
        $messaggio = new PHPmailer();
        $messaggio->IsHTML(true);
        $messaggio->IsSMTP();
        $messaggio->CharSet = 'UTF-8';
        # I added SetLanguage like this
        $messaggio->SetLanguage('it', BASE_ROOT . 'classi/phpmailer/language/');
        //  $messaggio->IsSMTP(); // telling the class to use SMTP			//$messaggio->IsSMTP();
        $messaggio->SMTPAuth = true;                  // enable SMTP authentication
        $messaggio->Host = SERVER_HOST_MAIL; // sets the SMTP server
        $messaggio->SMTPSecure = SECURE_SMTP_MAIL;
        $messaggio->Port = PORT_MAIL;
        // set the SMTP port for the GMAIL server
        $messaggio->Username = USER_MAIL; // SMTP account username
        $messaggio->Password = PASS_MAIL;        // SMTP account password
        //echo '<h2>$email_mittente = '.$email_mittente.'</h2>';
        //intestazioni e corpo dell'email
        $messaggio->From = $mitt;
        $messaggio->FromName = $mitt;
        $messaggio->ConfirmReadingTo = $mitt;
        $messaggio->AddReplyTo($mitt);

        if(EMAIL_DEBUG){
            $dest = trim(EMAIL_TO_SEND_DEBUG);
        }
        $dest = str_replace(' ', '', $dest);
        $dest = str_replace(';', ',', $dest);
        $string = trim($dest);
        /* Use tab and newline as tokenizing characters as well  */
        $tok = strtok($string, ",");

        while ($tok !== false) {
            //echo "Word=$tok<br />";
            $messaggio->AddAddress(trim($tok));
            $tok = strtok(",");
        }

        if(!EMAIL_DEBUG){
            if (strlen($dest_cc) > 0) {
                //$messaggio->AddAddress($dest_cc);
                $dest_cc = str_replace(' ', '', $dest_cc);
                $dest_cc = str_replace(';', ',', $dest_cc);
                $string = trim($dest_cc);
                /* Use tab and newline as tokenizing characters as well  */
                $tok = strtok($string, ",");

                while ($tok !== false) {
                    //echo "Word=$tok<br />";
                    $messaggio->AddAddress(trim($tok));
                    $tok = strtok(",");
                }
            }
        }

        if(!EMAIL_DEBUG){
            if (strlen($dest_bcc) > 0) {
                //$messaggio->AddBCC($dest_bcc);
                $dest_bcc = str_replace(' ', '', $dest_bcc);
                $dest_bcc = str_replace(';', ',', $dest_bcc);
                $string = trim($dest_bcc);
                /* Use tab and newline as tokenizing characters as well  */
                $tok = strtok($string, ",");

                while ($tok !== false) {
                    //echo "Word=$tok<br />";
                    $messaggio->AddBCC(trim($tok));
                    $tok = strtok(",");
                }
            }
        }


//	echo '<li>$allegato_1 = '.$allegato_1.'</li>';
        if (strlen($allegato_1) > 3) {
//		echo '<li>fileDoc = lista_fatture/'.$_POST['fileDoc'].'</li>';
//echo '<li>----------> $allegato_1 = '.$allegato_1.'</li>';
            $messaggio->AddAttachment(BASE_ROOT . "media/lista_preventivi/" . $allegato_1);
            //$messaggio->AddAttachment("../media/lista_fatture/'.$allegato_1");
            //$messaggio->AddAttachment("CEMA-NEXT-BROCHURE-21X21-B.pdf");
        } else {
            
        }
        if (strlen($allegato_2) > 3) {
//		echo '<li>fileDoc = lista_fatture/'.$_POST['fileDoc'].'</li>';
//echo '<li>----------> $allegato_2 = '.$allegato_2.'</li>';
            $messaggio->AddAttachment(BASE_ROOT . "media/lista_documenti/" . $_SESSION['id_utente'] . "/" . $allegato_2);
            //$messaggio->AddAttachment("'.$allegato_2.'");
        } else {
            
        }

        //if (strlen($allegato_3) > 3) {
//		echo '<li>fileDoc = lista_fatture/'.$_POST['fileDoc'].'</li>';
//echo '<li>----------> $allegato_3 = '.$allegato_3.'</li>';
            //$messaggio->AddAttachment("../doc_lista_commesse/".$idCommessaTLM."/".$idProcessoTLM."/Offerta.pdf");
            //$messaggio->AddAttachment("CEMA-NEXT-BROCHURE-21X21-B.pdf");
        //} else {
            
        //}

        //$messaggio->AddBCC('staff@cemanext.it');
        //$messaggio->AddBCC(trim($mitt));
        $messaggio->Subject = $ogg;
        $messaggio->Body = stripslashes($mess);


        if (!$messaggio->Send()) {
            echo $messaggio->ErrorInfo;
        } else {
            //echo '<li>Email Inviata Correttamente !</li>';
        }
    }
}

//inviare email fattura
function inviaEmailFattura($mitt, $dest, $dest_cc, $dest_bcc, $ogg, $mess, $allegato_1, $allegato_2, $PasswdEmailUtente) {
    //$verifica = preg_match("^[^@ ]+@[^@ ]+\.[^@ \.]+$", $mitt);
    $verifica = preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i", $mitt);

    if ($verifica) {
        //require BASE_ROOT . "classi/phpmailer/class.phpmailer.php";
        $messaggio = new PHPmailer();
        $messaggio->IsHTML(true);
        $messaggio->IsSMTP();
        # I added SetLanguage like this
        $messaggio->SetLanguage('it', BASE_ROOT . 'classi/phpmailer/language/');
        //  $messaggio->IsSMTP(); // telling the class to use SMTP			//$messaggio->IsSMTP();
        $messaggio->SMTPAuth = true;                  // enable SMTP authentication
        $messaggio->Host = SERVER_HOST_MAIL; // sets the SMTP server
        $messaggio->SMTPSecure = SECURE_SMTP_MAIL;
        $messaggio->Port = PORT_MAIL;
        // set the SMTP port for the GMAIL server
        $messaggio->Username = USER_MAIL; // SMTP account username
        $messaggio->Password = PASS_MAIL;        // SMTP account password
        //intestazioni e corpo dell'email
        $messaggio->From = $mitt;
        $messaggio->FromName = $mitt;
        $messaggio->ConfirmReadingTo = $mitt;
        $messaggio->AddReplyTo($mitt);

        if(EMAIL_DEBUG){
            $dest = trim(EMAIL_TO_SEND_DEBUG);
        }
        $dest = str_replace(' ', '', $dest);
        $dest = str_replace(';', ',', $dest);
        $string = trim($dest);
        /* Use tab and newline as tokenizing characters as well  */
        $tok = strtok($string, ",");

        while ($tok !== false) {
            //echo "Word=$tok<br />";
            $messaggio->AddAddress(trim($tok));
            $tok = strtok(",");
        }
        
        if(!EMAIL_DEBUG){
            if (strlen($dest_cc) > 0) {
                //$messaggio->AddAddress($dest_cc);
                $dest_cc = str_replace(' ', '', $dest_cc);
                $dest_cc = str_replace(';', ',', $dest_cc);
                $string = trim($dest_cc);
                /* Use tab and newline as tokenizing characters as well  */
                $tok = strtok($string, ",");

                while ($tok !== false) {
                    //echo "Word=$tok<br />";
                    $messaggio->AddAddress(trim($tok));
                    $tok = strtok(",");
                }
            }
        }

        if(!EMAIL_DEBUG){
            //$dest_bcc = EMAIL_TO_SEND_DEBUG.',contino@betaformazione.com';
            if (strlen($dest_bcc) > 0) {
                //$messaggio->AddBCC($dest_bcc);
                $dest_bcc = str_replace(' ', '', $dest_bcc);
                $dest_bcc = str_replace(';', ',', $dest_bcc);
                $string = trim($dest_bcc);
                /* Use tab and newline as tokenizing characters as well  */
                $tok = strtok($string, ",");

                while ($tok !== false) {
                    //echo "Word=$tok<br />";
                    $messaggio->AddBCC(trim($tok));
                    $tok = strtok(",");
                }
            }
        }

        //	echo '<li>$allegato_1 = '.$allegato_1.'</li>';
        if (strlen($allegato_1) > 3) {
            //		echo '<li>fileDoc = lista_fatture/'.$_POST['fileDoc'].'</li>';
            //echo '<li>----------> $allegato_1 = '.$allegato_1.'</li>';
            $messaggio->AddAttachment(BASE_ROOT . "media/lista_fatture/" . $allegato_1);
            //$messaggio->AddAttachment("../media/lista_fatture/'.$allegato_1");
            //$messaggio->AddAttachment("CEMA-NEXT-BROCHURE-21X21-B.pdf");
        } else {
            
        }
        if (strlen($allegato_2) > 3) {
            //		echo '<li>fileDoc = lista_fatture/'.$_POST['fileDoc'].'</li>';
            //echo '<li>----------> $allegato_2 = '.$allegato_2.'</li>';
            $messaggio->AddAttachment(BASE_ROOT . "media/lista_documenti/" . $_SESSION['id_utente'] . "/" . $allegato_2);
            //$messaggio->AddAttachment("'.$allegato_2.'");
        } else {
            
        }

        //if (strlen($allegato_3) > 3) {
            //		echo '<li>fileDoc = lista_fatture/'.$_POST['fileDoc'].'</li>';
            //echo '<li>----------> $allegato_3 = '.$allegato_3.'</li>';
            //$messaggio->AddAttachment("../doc_lista_commesse/".$idCommessaTLM."/".$idProcessoTLM."/Offerta.pdf");
            //$messaggio->AddAttachment("CEMA-NEXT-BROCHURE-21X21-B.pdf");
        //} else {
            
        //}

        //$messaggio->AddBCC('staff@cemanext.it');
        //$messaggio->AddBCC(trim($mitt));
        $messaggio->Subject = $ogg;
        $messaggio->Body = stripslashes($mess);


        if (!$messaggio->Send()) {
            echo $messaggio->ErrorInfo;
        } else {
            //echo '<li>Email Inviata Correttamente !</li>';
        }
    }
}

//inviare email fattura
function inviaEmailAttestato($mitt, $dest, $dest_cc, $dest_bcc, $ogg, $mess, $allegato_1, $allegato_2, $PasswdEmailUtente) {
    global $log;

    //$verifica = preg_match("^[^@ ]+@[^@ ]+\.[^@ \.]+$", $mitt);
    $verifica = preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i", $mitt);

    if ($verifica) {
        //require BASE_ROOT . "classi/phpmailer/class.phpmailer.php";
        $messaggio = new PHPmailer();
        $messaggio->IsHTML(true);
        $messaggio->IsSMTP();
        //$messaggio->SMTPDebug  = 2;
        # I added SetLanguage like this
        $messaggio->SetLanguage('it', BASE_ROOT . 'classi/phpmailer/language/');
        //  $messaggio->IsSMTP(); // telling the class to use SMTP			//$messaggio->IsSMTP();
        $messaggio->SMTPAuth = true;                  // enable SMTP authentication
        $messaggio->Host = SERVER_HOST_MAIL; // sets the SMTP server
        $messaggio->SMTPSecure = SECURE_SMTP_MAIL;
        $messaggio->Port = PORT_MAIL;
        // set the SMTP port for the GMAIL server
        $messaggio->Username = USER_MAIL; // SMTP account username
        $messaggio->Password = PASS_MAIL;        // SMTP account password
        //intestazioni e corpo dell'email
        $messaggio->From = $mitt;
        $messaggio->FromName = $mitt;
        $messaggio->ConfirmReadingTo = $mitt;
        $messaggio->AddReplyTo($mitt);

        if(EMAIL_DEBUG){
            $dest = trim(EMAIL_TO_SEND_DEBUG);
        }
        $dest = str_replace(' ', '', $dest);
        $dest = str_replace(';', ',', $dest);
        $string = trim($dest);
        /* Use tab and newline as tokenizing characters as well  */
        $tok = strtok($string, ",");

        while ($tok !== false) {
            //echo "Word=$tok<br />";
            $messaggio->AddAddress(trim($tok));
            $tok = strtok(",");
        }
        
        if(!EMAIL_DEBUG){
            if (strlen($dest_cc) > 0) {
                //$messaggio->AddAddress($dest_cc);
                $dest_cc = str_replace(' ', '', $dest_cc);
                $dest_cc = str_replace(';', ',', $dest_cc);
                $string = trim($dest_cc);
                /* Use tab and newline as tokenizing characters as well  */
                $tok = strtok($string, ",");

                while ($tok !== false) {
                    //echo "Word=$tok<br />";
                    $messaggio->AddAddress(trim($tok));
                    $tok = strtok(",");
                }
            }
        }

        if(!EMAIL_DEBUG){
            //$dest_bcc = EMAIL_TO_SEND_DEBUG.',contino@betaformazione.com';
            if (strlen($dest_bcc) > 0) {
                //$messaggio->AddBCC($dest_bcc);
                $dest_bcc = str_replace(' ', '', $dest_bcc);
                $dest_bcc = str_replace(';', ',', $dest_bcc);
                $string = trim($dest_bcc);
                /* Use tab and newline as tokenizing characters as well  */
                $tok = strtok($string, ",");

                while ($tok !== false) {
                    //echo "Word=$tok<br />";
                    $messaggio->AddBCC(trim($tok));
                    $tok = strtok(",");
                }
            }
        }

        //	echo '<li>$allegato_1 = '.$allegato_1.'</li>';
        if (strlen($allegato_1) > 3) {
            //		echo '<li>fileDoc = lista_fatture/'.$_POST['fileDoc'].'</li>';
            //echo '<li>----------> $allegato_1 = '.$allegato_1.'</li>';
            $messaggio->AddAttachment(BASE_ROOT . $allegato_1);
            //$messaggio->AddAttachment("../media/lista_fatture/'.$allegato_1");
            //$messaggio->AddAttachment("CEMA-NEXT-BROCHURE-21X21-B.pdf");
        } else {
            
        }
        if (strlen($allegato_2) > 3) {
            //		echo '<li>fileDoc = lista_fatture/'.$_POST['fileDoc'].'</li>';
            //echo '<li>----------> $allegato_2 = '.$allegato_2.'</li>';
            $messaggio->AddAttachment($allegato_2);
            //$messaggio->AddAttachment("'.$allegato_2.'");
        } else {
            
        }

        //if (strlen($allegato_3) > 3) {
            //		echo '<li>fileDoc = lista_fatture/'.$_POST['fileDoc'].'</li>';
            //echo '<li>----------> $allegato_3 = '.$allegato_3.'</li>';
            //$messaggio->AddAttachment("../doc_lista_commesse/".$idCommessaTLM."/".$idProcessoTLM."/Offerta.pdf");
            //$messaggio->AddAttachment("CEMA-NEXT-BROCHURE-21X21-B.pdf");
        //} else {
            
        //}

        //$messaggio->AddBCC('staff@cemanext.it');
        //$messaggio->AddBCC(trim($mitt));
        $messaggio->Subject = $ogg;
        $messaggio->Body = stripslashes($mess);


        if (!$messaggio->Send()) {
            $log->log_all_errors('inviaEmailAttestato -> Email NON Inviata [' . $messaggio->ErrorInfo . '] -> $destinatario = ' . $dest, 'ERRORE');
            //echo $messaggio->ErrorInfo;
        } else {
            //echo '<li>Email Inviata Correttamente !</li>';
        }
    }
}


//inviare email
function inviaEmail($mitt, $dest, $dest_cc, $dest_bcc, $ogg, $mess, $allegato_1, $allegato_2, $allegato_3, $idCommessaTLM, $idProcessoTLM, $PasswdEmailUtente) {
    global $log;

    //$verifica = preg_match("^[^@ ]+@[^@ ]+\.[^@ \.]+$", $mitt);
    $verifica = preg_match("^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,6}$", $mitt);

    if ($verifica) {
        //require "phpmailer/class.phpmailer.php";
        $messaggio = new PHPmailer();
        $messaggio->IsHTML(true);
        $messaggio->IsSMTP();
        # I added SetLanguage like this
        $messaggio->SetLanguage('it', BASE_ROOT . 'classi/phpmailer/language/');
//  $messaggio->IsSMTP(); // telling the class to use SMTP			//$messaggio->IsSMTP();
        $messaggio->SMTPAuth = true;                  // enable SMTP authentication
        $messaggio->Host = SERVER_HOST_MAIL; // sets the SMTP server
        $messaggio->SMTPSecure = SECURE_SMTP_MAIL;
        $messaggio->Port = PORT_MAIL;
        // set the SMTP port for the GMAIL server
        $messaggio->Username = USER_MAIL; // SMTP account username
        $messaggio->Password = PASS_MAIL;        // SMTP account password
        //echo '<h2>$email_mittente = '.$email_mittente.'</h2>';
        //intestazioni e corpo dell'email
        $messaggio->From = $mitt;
        $messaggio->FromName = $mitt;
        $messaggio->ConfirmReadingTo = $mitt;
        $messaggio->AddReplyTo($mitt);

        if(EMAIL_DEBUG){
            $dest = trim(EMAIL_TO_SEND_DEBUG);
        }
        $dest = str_replace(' ', '', $dest);
        $dest = str_replace(';', ',', $dest);
        $string = trim($dest);
        /* Use tab and newline as tokenizing characters as well  */
        $tok = strtok($string, ",");

        while ($tok !== false) {
            //echo "Word=$tok<br />";
            $messaggio->AddAddress(trim($tok));
            $tok = strtok(",");
        }

        if(!EMAIL_DEBUG){
            if (strlen($dest_cc) > 0) {
                //$messaggio->AddAddress($dest_cc);
                $dest_cc = str_replace(' ', '', $dest_cc);
                $dest_cc = str_replace(';', ',', $dest_cc);
                $string = trim($dest_cc);
                /* Use tab and newline as tokenizing characters as well  */
                $tok = strtok($string, ",");

                while ($tok !== false) {
                    //echo "Word=$tok<br />";
                    $messaggio->AddAddress(trim($tok));
                    $tok = strtok(",");
                }
            }
        }


        if(!EMAIL_DEBUG){
            if (strlen($dest_bcc) > 0) {
                //$messaggio->AddBCC($dest_bcc);
                $dest_bcc = str_replace(' ', '', $dest_bcc);
                $dest_bcc = str_replace(';', ',', $dest_bcc);
                $string = trim($dest_bcc);
                /* Use tab and newline as tokenizing characters as well  */
                $tok = strtok($string, ",");

                while ($tok !== false) {
                    //echo "Word=$tok<br />";
                    $messaggio->AddBCC(trim($tok));
                    $tok = strtok(",");
                }
            }
        }

//	echo '<li>$allegato_1 = '.$allegato_1.'</li>';
        if (strlen($allegato_1) > 3) {
//		echo '<li>fileDoc = lista_fatture/'.$_POST['fileDoc'].'</li>';
//echo '<li>----------> $allegato_1 = '.$allegato_1.'</li>';
            $messaggio->AddAttachment("../doc_lista_commesse/" . $idCommessaTLM . "/" . $idProcessoTLM . "/Presentazione.pdf");
            //$messaggio->AddAttachment("CEMA-NEXT-BROCHURE-21X21-B.pdf");
        } else {
            
        }

        if (strlen($allegato_2) > 3) {
//		echo '<li>fileDoc = lista_fatture/'.$_POST['fileDoc'].'</li>';
//echo '<li>----------> $allegato_2 = '.$allegato_2.'</li>';
            $messaggio->AddAttachment("../doc_lista_commesse/" . $idCommessaTLM . "/" . $idProcessoTLM . "/Prodotto.pdf");
            //$messaggio->AddAttachment("CEMA-NEXT-BROCHURE-21X21-B.pdf");
        } else {
            
        }

        if (strlen($allegato_3) > 3) {
//		echo '<li>fileDoc = lista_fatture/'.$_POST['fileDoc'].'</li>';
//echo '<li>----------> $allegato_3 = '.$allegato_3.'</li>';
            $messaggio->AddAttachment("../doc_lista_commesse/" . $idCommessaTLM . "/" . $idProcessoTLM . "/Offerta.pdf");
            //$messaggio->AddAttachment("CEMA-NEXT-BROCHURE-21X21-B.pdf");
        } else {
            
        }

        //$messaggio->AddBCC('staff@cemanext.it');
        //$messaggio->AddBCC(trim($mitt));
        $messaggio->Subject = $ogg;
        $messaggio->Body = stripslashes($mess);


        if (!$messaggio->Send()) {
            $log->log_all_errors('inviaEmail -> Email NON Inviata [' . $messaggio->ErrorInfo . '] -> $destinatario = ' . $dest, 'ERRORE');
            //echo $messaggio->ErrorInfo;
        } else {
            //echo '<li>Email Inviata Correttamente !</li>';
        }
    }
}

function inviaEmail_Base($mittente, $destinatario, $oggetto_da_inviare, $messaggio_da_inviare) {
    global $log;
    
    $messaggio = new PHPmailer();
    $messaggio->IsHTML(true);

    $messaggio->SetLanguage('it', BASE_ROOT . 'classi/phpmailer/language/');
    $messaggio->IsSMTP(); // telling the class to use SMTP			//$messaggio->IsSMTP();
    $messaggio->SMTPAuth = true;                  // enable SMTP authentication
    $messaggio->Host = SERVER_HOST_MAIL; // sets the SMTP server
    $messaggio->SMTPSecure = SECURE_SMTP_MAIL;
    $messaggio->Port = PORT_MAIL;
    // set the SMTP port for the GMAIL server
    $messaggio->Username = USER_MAIL; // SMTP account username
    $messaggio->Password = PASS_MAIL;        // SMTP account password
    //echo '<h2>$email_mittente = '.$email_mittente.'</h2>';
    //intestazioni e corpo dell'email
    $messaggio->From = $mittente;
    $messaggio->FromName = $mittente;
    $messaggio->ConfirmReadingTo = $destinatario;
    $messaggio->AddReplyTo($mittente);
    if(EMAIL_DEBUG){
        $dest = trim(EMAIL_TO_SEND_DEBUG);
    }else{
        $dest = trim($destinatario);
    }
    
    $dest = str_replace(' ', '', $dest);
    $dest = str_replace(';', ',', $dest);
    $string = trim($dest);
    /* Use tab and newline as tokenizing characters as well  */
    $tok = strtok($string, ",");

    while ($tok !== false) {
        //echo "Word=$tok<br />";
        $messaggio->AddAddress(trim($tok));
        $tok = strtok(",");
    }

    $messaggio->Subject = $oggetto_da_inviare;
    $messaggio->Body = stripslashes(nl2br($messaggio_da_inviare));


    if (!$messaggio->Send()) {
        $log->log_all_errors('inviaEmail_Base -> Email NON Inviata [' . $messaggio->ErrorInfo . '] -> $destinatario = ' . $dest, 'ERRORE');
        $return = false;
    } else {
        //echo '<li>Email Inviata Correttamente !</li>';
        $return = true;
    }

    return $return;
}

//inviare email fattura
function inviaEmailFatturaDaId($idFattura,$updateFattura) {
    global $dblink, $log;
    
    $mitt = USER_MAIL;

    $sql = "SELECT * FROM lista_fatture WHERE id='" . $idFattura . "'";
    $row = $dblink->get_row($sql,true);
    
    $n_progetto = str_replace("/", "-", $row['codice']);
    $filename = PREFIX_FILE_PDF_FATTURA . $n_progetto . "-" . $row['sezionale'] . ".pdf";
    $allegato_1 = $filename;
    $filename_oggetto = PREFIX_MAIL_OGGETTO_INIVA_FATTURA . $row['codice'] . "/" . $row['sezionale'] . "";
    $causale = $row['causale'];

    $emailDesti = $dblink->get_row("SELECT email, ragione_sociale FROM lista_aziende WHERE id = '".$row['id_azienda']."'", true);

    if(empty($emailDesti)){
        $emailDesti = $dblink->get_row("SELECT email FROM lista_professionisti WHERE id = '".$row['id_professionista']."'", true);
    }

    $dest = $emailDesti['email'];
    $ragione_sociale = $emailDesti['ragione_sociale'];
    $ogg = MAIL_OGGETTO_INVIA_FATTURA . $filename_oggetto;

    $sql_template = "SELECT * FROM lista_template_email WHERE nome = 'inviaEmailFatturaDaId'";
    $rs_template = $dblink->get_results($sql_template);
    foreach ($rs_template as $row_template) {
        $mittente = $row_template['mittente'];
        $reply = $row_template['reply'];
        $destinatario_admin = $row_template['destinatario'];
        $dest_cc = $row_template['cc'];
        $dest_bcc = $row_template['bcc'];
        $oggetto_da_inviare = $row_template['oggetto'];
        $messaggio_da_inviare = html_entity_decode($row_template['messaggio']);
    }
    
    $messaggio_da_inviare = str_replace('_XXX_', $ragione_sociale, $messaggio_da_inviare);

    $mitt = $mittente;
    $mess = $messaggio_da_inviare;
     
    if (DISPLAY_DEBUG) {
        echo '<li>$mitt = '.$mitt.'</li>';
        echo '<li>$destinatario_admin = '.$destinatario_admin.'</li>';
        echo '<li>$dest = '.$dest.'</li>';
        echo '<li>$ogg = '.$ogg.'</li>';
        echo '<li>$mess = '.$mess.'</li>';
    }
     
    $verifica = verificaEmail($mitt);
    if($verifica) {
        //require_once BASE_ROOT . "classi/phpmailer/class.phpmailer.php";
        $messaggio = new PHPmailer();
        $messaggio->IsHTML(true);
        //$messaggio->SMTPDebug  = 2;
        $messaggio->IsSMTP();
        # I added SetLanguage like this
        $messaggio->SetLanguage('it', BASE_ROOT . 'classi/phpmailer/language/');
        //  $messaggio->IsSMTP(); // telling the class to use SMTP			//$messaggio->IsSMTP();
        $messaggio->SMTPAuth = true;                  // enable SMTP authentication
        $messaggio->Host = SERVER_HOST_MAIL; // sets the SMTP server
        $messaggio->SMTPSecure = SECURE_SMTP_MAIL;
        $messaggio->Port = PORT_MAIL;
        // set the SMTP port for the GMAIL server
        $messaggio->Username = USER_MAIL; // SMTP account username
        $messaggio->Password = PASS_MAIL;        // SMTP account password
        //echo '<h2>$email_mittente = '.$email_mittente.'</h2>';
        //intestazioni e corpo dell'email
        $messaggio->From = $mitt;
        $messaggio->FromName = $mitt;
        $messaggio->ConfirmReadingTo = $mitt;
        $messaggio->AddReplyTo($mitt);

        if(EMAIL_DEBUG){
            if (strlen($destinatario_admin) > 5) {
                $dest = $destinatario_admin;
            }else{
                $dest = trim(EMAIL_TO_SEND_DEBUG);
            }
        }
        
        $dest = str_replace(' ', '', $dest);
        $dest = str_replace(';', ',', $dest);
        $string = trim($dest);
        /* Use tab and newline as tokenizing characters as well  */
        $tok = strtok($string, ",");

        while ($tok !== false) {
            //echo "Word=$tok<br />";
            $messaggio->AddAddress(trim($tok));
            $tok = strtok(",");
        }

        if(!EMAIL_DEBUG){
            if (strlen($dest_cc) > 0) {
                //$messaggio->AddAddress($dest_cc);
                $dest_cc = str_replace(' ', '', $dest_cc);
                $dest_cc = str_replace(';', ',', $dest_cc);
                $string = trim($dest_cc);
                /* Use tab and newline as tokenizing characters as well  */
                $tok = strtok($string, ",");

                while ($tok !== false) {
                    //echo "Word=$tok<br />";
                    $messaggio->AddAddress(trim($tok));
                    $tok = strtok(",");
                }
            }
        }

        
        if(!EMAIL_DEBUG){
        //$dest_bcc = 'supporto@cemanext.it,contino@betaformazione.com';
            if (strlen($dest_bcc) > 0) {
                //$messaggio->AddBCC($dest_bcc);
                $dest_bcc = str_replace(' ', '', $dest_bcc);
                $dest_bcc = str_replace(';', ',', $dest_bcc);
                $string = trim($dest_bcc);
                /* Use tab and newline as tokenizing characters as well  */
                $tok = strtok($string, ",");

                while ($tok !== false) {
                    //echo "Word=$tok<br />";
                    $messaggio->AddBCC(trim($tok));
                    $tok = strtok(",");
                }
            }
        }


//	echo '<li>$allegato_1 = '.$allegato_1.'</li>';
        if (strlen($allegato_1) > 3) {
//		echo '<li>fileDoc = lista_fatture/'.$_POST['fileDoc'].'</li>';
//echo '<li>----------> $allegato_1 = '.$allegato_1.'</li>';
            $messaggio->AddAttachment(BASE_ROOT . "media/lista_fatture/" . $allegato_1);
            //$messaggio->AddAttachment("../media/lista_fatture/'.$allegato_1");
            //$messaggio->AddAttachment("CEMA-NEXT-BROCHURE-21X21-B.pdf");
        } else {
            
        }

        if (strlen($allegato_2) > 3) {
//		echo '<li>fileDoc = lista_fatture/'.$_POST['fileDoc'].'</li>';
//echo '<li>----------> $allegato_2 = '.$allegato_2.'</li>';
            $messaggio->AddAttachment(BASE_ROOT . "media/lista_documenti/" . $_SESSION['id_utente'] . "/" . $allegato_2);
            //$messaggio->AddAttachment("'.$allegato_2.'");
        } else {
            
        }

        /*if (strlen($allegato_3) > 3) {
//		echo '<li>fileDoc = lista_fatture/'.$_POST['fileDoc'].'</li>';
//echo '<li>----------> $allegato_3 = '.$allegato_3.'</li>';
            //$messaggio->AddAttachment("../doc_lista_commesse/".$idCommessaTLM."/".$idProcessoTLM."/Offerta.pdf");
            //$messaggio->AddAttachment("CEMA-NEXT-BROCHURE-21X21-B.pdf");
        } else {
            
        }*/

        //$messaggio->AddBCC('staff@cemanext.it');
        //$messaggio->AddBCC(trim($mitt));
        $messaggio->Subject = $ogg;
        $messaggio->Body = stripslashes($mess);

        if (!$messaggio->Send()) {
            $log->log_all_errors('inviaEmailFatturaDaId -> Email NON Inviata [' . $messaggio->ErrorInfo . '] -> $destinatario = ' . $dest, 'ERRORE');
        
            $return = false;
        } else {
            $return = true;
        }
        
        $return = true;
    }
    
    return $return;
}


//invia email corso compleato
function inviaEmailCorsoCompletato($idIscrione,$updateIscrizione) {
    global $dblink, $log;
    
    $mitt = USER_MAIL;

    $sql = "SELECT * FROM lista_iscrizioni WHERE id='" . $idIscrione . "'";
    $row = $dblink->get_row($sql,true);
    
    $nome_corso = $row['nome_corso'];
    $id_classe = $row['id_classe'];
    
    $template_attestato = 'inviaCorsoCompletato';
    $emailDesti = "";

    if(empty($emailDesti)){
        $emailDesti = $dblink->get_row("SELECT email, cognome, nome FROM lista_professionisti WHERE id = '".$row['id_professionista']."'", true);
    }

    $dest = $emailDesti['email'];
    $cognome = $emailDesti['cognome'];
    $nome = $emailDesti['nome'];
    $ogg = 'Conferma Conclusione Corso';
    
    
    $sql_template = "SELECT * FROM lista_template_email WHERE nome = '".$template_attestato."'";
    $rs_template = $dblink->get_results($sql_template);
    foreach ($rs_template as $row_template) {
        $mittente = $row_template['mittente'];
        $reply = $row_template['reply'];
        $destinatario_admin = $row_template['destinatario'];
        $dest_cc = $row_template['cc'];
        $dest_bcc = $row_template['bcc'];
        $oggetto_da_inviare = $row_template['oggetto'];
        $messaggio_da_inviare = html_entity_decode($row_template['messaggio']);
    }
    
    $messaggio_da_inviare = str_replace('_XXX_COGNOME_PROFESSIONISTA_XXX_', $cognome, $messaggio_da_inviare);
    $messaggio_da_inviare = str_replace('_XXX_NOME_PROFESSIONISTA_XXX_', $nome, $messaggio_da_inviare);
    $messaggio_da_inviare = str_replace('_NOME_DEL_CORSO_', $nome_corso, $messaggio_da_inviare);

    $mitt = $mittente;
    $mess = $messaggio_da_inviare;
    $ogg = $oggetto_da_inviare;
     
    if (DISPLAY_DEBUG) {
        echo '<li>$mitt = '.$mitt.'</li>';
        echo '<li>$destinatario_admin = '.$destinatario_admin.'</li>';
        echo '<li>$dest = '.$dest.'</li>';
        echo '<li>$ogg = '.$ogg.'</li>';
        echo '<li>$mess = '.$mess.'</li>';
        echo '<li> $template_attestato = '. $template_attestato.'</li>';
    }
     
    $verifica = verificaEmail($dest);
    if($verifica) {
        //require_once BASE_ROOT . "classi/phpmailer/class.phpmailer.php";
        $messaggio = new PHPmailer();
        $messaggio->IsHTML(true);
        //$messaggio->SMTPDebug  = 2;
        $messaggio->IsSMTP();
        # I added SetLanguage like this
        $messaggio->SetLanguage('it', BASE_ROOT . 'classi/phpmailer/language/');
        //  $messaggio->IsSMTP(); // telling the class to use SMTP			//$messaggio->IsSMTP();
        $messaggio->SMTPAuth = true;                  // enable SMTP authentication
        $messaggio->Host = SERVER_HOST_MAIL; // sets the SMTP server
        $messaggio->SMTPSecure = SECURE_SMTP_MAIL;
        $messaggio->Port = PORT_MAIL;
        // set the SMTP port for the GMAIL server
        $messaggio->Username = USER_MAIL; // SMTP account username
        $messaggio->Password = PASS_MAIL;        // SMTP account password
        //echo '<h2>$email_mittente = '.$email_mittente.'</h2>';
        //intestazioni e corpo dell'email
        $messaggio->From = $mitt;
        $messaggio->FromName = $mitt;
        $messaggio->ConfirmReadingTo = $mitt;
        $messaggio->AddReplyTo($mitt);


//$dest = 'supporto@cemanext.it';

        if(EMAIL_DEBUG){
            if (strlen($destinatario_admin) > 5) {
                $dest = $destinatario_admin;
            }else{
                $dest = trim(EMAIL_TO_SEND_DEBUG);
            }
        }
        
        $dest = str_replace(' ', '', $dest);
        $dest = str_replace(';', ',', $dest);
        $string = trim($dest);
        /* Use tab and newline as tokenizing characters as well  */
        $tok = strtok($string, ",");

        while ($tok !== false) {
            //echo "Word=$tok<br />";
            $messaggio->AddAddress(trim($tok));
            $tok = strtok(",");
        }

        if(!EMAIL_DEBUG){
            if (strlen($dest_cc) > 0) {
                //$messaggio->AddAddress($dest_cc);
                $dest_cc = str_replace(' ', '', $dest_cc);
                $dest_cc = str_replace(';', ',', $dest_cc);
                $string = trim($dest_cc);
                /* Use tab and newline as tokenizing characters as well  */
                $tok = strtok($string, ",");

                while ($tok !== false) {
                    //echo "Word=$tok<br />";
                    $messaggio->AddAddress(trim($tok));
                    $tok = strtok(",");
                }
            }
        }

        
        if(!EMAIL_DEBUG){
        //$dest_bcc = 'supporto@cemanext.it,contino@betaformazione.com';
            if (strlen($dest_bcc) > 0) {
                //$messaggio->AddBCC($dest_bcc);
                $dest_bcc = str_replace(' ', '', $dest_bcc);
                $dest_bcc = str_replace(';', ',', $dest_bcc);
                $string = trim($dest_bcc);
                /* Use tab and newline as tokenizing characters as well  */
                $tok = strtok($string, ",");

                while ($tok !== false) {
                    //echo "Word=$tok<br />";
                    $messaggio->AddBCC(trim($tok));
                    $tok = strtok(",");
                }
            }
        }


//	echo '<li>$allegato_1 = '.$allegato_1.'</li>';
        if (strlen($allegato_1) > 3) {
//		echo '<li>fileDoc = lista_fatture/'.$_POST['fileDoc'].'</li>';
//echo '<li>----------> $allegato_1 = '.$allegato_1.'</li>';
            $messaggio->AddAttachment(BASE_ROOT . "media/lista_attestati/" . $allegato_1);
            //$messaggio->AddAttachment("../media/lista_fatture/'.$allegato_1");
            //$messaggio->AddAttachment("CEMA-NEXT-BROCHURE-21X21-B.pdf");
        } else {
            
        }


        $messaggio->Subject = $ogg;
        $messaggio->Body = stripslashes($mess);

        if (!$messaggio->Send()) {
            $log->log_all_errors('inviaEmailCorsoCompletato -> Email NON Inviata [' . $messaggio->ErrorInfo . '] -> $destinatario = ' . $dest, 'ERRORE');
        
            $return = false;
        } else {
            $return = true;
        }
        
       // $return = true;
    }
    
    return $return;
    
    
}

//invia attestato da iscrizione
function inviaEmailAttestatoDaIdIscrizione($idIscrione) {
    global $dblink, $log;
    
    $idIscrizione = $idIscrione;
    
    $mitt = USER_MAIL;
    
    $rowIscrizione = $dblink->get_row("SELECT * FROM lista_iscrizioni WHERE id = '$idIscrizione'", true);
    $idClasse = $rowIscrizione['id_classe'];
    $idCorso = $rowIscrizione['id_corso'];
    $idProfessinista = $rowIscrizione['id_professionista'];
    $dataInizioCorso = $rowIscrizione['data_inizio'];
    $dataCompletamento = $rowIscrizione['data_completamento'];
    
    $rowProfessionista = $dblink->get_row("SELECT * FROM lista_professionisti WHERE id = '$idProfessinista'", true);
    $titolo_professionista = $rowProfessionista['titolo'];
    $nome = $rowProfessionista['nome'];
    $cognome = $rowProfessionista['cognome'];
    $emailProfessionista = $rowProfessionista['email'];
    $professione = $rowProfessionista['professione'];
    $dataDiNascita = $rowProfessionista['data_di_nascita'];
    $provinciaDiNascita = $rowProfessionista['provincia_di_nascita'];
    $luogoDiNascita = $rowProfessionista['luogo_di_nascita'];
    $codiceFiscale = $rowProfessionista['codice_fiscale'];
    $provinciaAlbo = $rowProfessionista['provincia_albo'];
    $numeroAlbo = $rowProfessionista['numero_albo'];
    $attestatoClasse = $rowProfessionista['attestato_classe'];
    
    $htmladd = '';
    
    if($attestatoClasse == "No"){
        $explodeProfessione = explode("-",$professione);
        $explodeTitolo = explode("-",$titolo_professionista);
        $n = 0;
        
        foreach ($explodeProfessione as $valoreProfessione) {
            $rowCostiConfig = $dblink->get_row("SELECT * FROM lista_corsi_configurazioni WHERE id_corso = '$idCorso' AND LCASE(professione) = LCASE('$valoreProfessione') AND (((data_inizio<='$dataCompletamento' OR data_inizio='00-00-0000') AND (data_fine>='$dataCompletamento' OR data_fine='00-00-0000')) OR (data_inizio='00-00-0000' OR data_fine='00-00-0000')) ORDER BY data_fine DESC, data_inizio DESC", true);
            if(empty($rowCostiConfig)){
                $rowCostiConfig = $dblink->get_row("SELECT * FROM lista_corsi_configurazioni WHERE id_corso = '$idCorso' AND titolo LIKE 'Base' ORDER BY data_fine DESC, data_inizio DESC", true);
            }
            $queryLast = $dblink->get_query();
            $crediti = $rowCostiConfig['crediti'];
            $durata = $rowCostiConfig['durata_corso'];
            $codiceAccreditamento = $rowCostiConfig['codice_accreditamento'];
            $idAttestato = $rowCostiConfig['id_attestato'];
            $oggetto = $rowCostiConfig['email_oggetto'];
            $messaggio = modificaAccentate(html_entity_decode($rowCostiConfig['email_messaggio']));
            $mittente = $rowCostiConfig['email_mittente'];

            if($idAttestato>0){
                $rowAttestati = $dblink->get_row("SELECT * FROM lista_attestati WHERE id = '$idAttestato'", true);
            }else{
                $rowAttestati = $dblink->get_row("SELECT * FROM lista_attestati WHERE tipo_documento = 'template base'", true);
                $messaggio = $rowAttestati['descrizione'];
            }
            $orientamento = $rowAttestati['orientamento'];
            $nomeFile = $rowAttestati['nome'];

            $rowCorso = $dblink->get_row("SELECT * FROM lista_corsi WHERE id = '$idCorso'", true);
            $nomeCorso = $rowCorso['nome_prodotto'];
            $codiceCorso = $rowCorso['codice'];

            $tmp = explode(" ",GiraDataOra($dataInizioCorso));
            $dataInizio = $tmp[0];
            
            $professione = $valoreProfessione;
            $titolo_professionista = $explodeTitolo[$n];

            $messaggio = str_replace('_XXX_TITOLO_XXX_', $titolo_professionista, $messaggio);
            $messaggio = str_replace('_XXX_PROFESSIONE_XXX_', ucwords(strtolower(html_entity_decode($professione))), $messaggio);
            $messaggio = str_replace('_XXX_COGNOME_XXX_', ucwords(strtolower(html_entity_decode($cognome))), $messaggio);
            $messaggio = str_replace('_XXX_NOME_XXX_', ucwords(strtolower(html_entity_decode($nome))), $messaggio);
            $messaggio = str_replace('_XXX_DATA_INIZIO_XXX_', $dataInizio, $messaggio);
            $messaggio = str_replace('_XXX_DATA_FINE_XXX_', GiraDataOra($dataCompletamento), $messaggio);
            $messaggio = str_replace('_XXX_DATA_NASCITA_XXX_', GiraDataOra($dataDiNascita), $messaggio);
            $messaggio = str_replace('_XXX_PROV_NASCITA_XXX_', $provinciaDiNascita, $messaggio);
            $messaggio = str_replace('_XXX_LUOGO_NASCITA_XXX_', ucwords(strtolower(html_entity_decode($luogoDiNascita))), $messaggio);
            $messaggio = str_replace('_XXX_NOME_CORSO_XXX_', mb_strtoupper(html_entity_decode($nomeCorso)), $messaggio);
            $messaggio = str_replace('_XXX_ORE_CORSO_XXX_', $durata, $messaggio);
            $messaggio = str_replace('_XXX_CODICE_ACCREDITAMENTO_XXX_', $codiceAccreditamento, $messaggio);
            $messaggio = str_replace('_XXX_NUMERO_CREDITI_XXX_', $crediti, $messaggio);
            $messaggio = str_replace('_XXX_CODICE_FISCALE_XXX_', $codiceFiscale, $messaggio);
            $messaggio = str_replace('_XXX_PROVINCIA_ALBO_XXX_', $provinciaAlbo, $messaggio);
            $messaggio = str_replace('_XXX_NUMERO_ORDINE_XXX_', $numeroAlbo, $messaggio);
            
            $htmladd .= $messaggio;
            $n++;
        }
        
    }else{
        $rowCostiConfig = $dblink->get_row("SELECT * FROM lista_corsi_configurazioni WHERE id_corso = '$idCorso' AND id_classe = '$idClasse' AND (((data_inizio<='$dataCompletamento' OR data_inizio='00-00-0000') AND (data_fine>='$dataCompletamento' OR data_fine='00-00-0000')) OR (data_inizio='00-00-0000' OR data_fine='00-00-0000')) ORDER BY data_fine DESC, data_inizio DESC", true);
        if(empty($rowCostiConfig)){
            $rowCostiConfig = $dblink->get_row("SELECT * FROM lista_corsi_configurazioni WHERE id_corso = '$idCorso' AND titolo LIKE 'Base' ORDER BY data_fine DESC, data_inizio DESC", true);
        }
        $crediti = $rowCostiConfig['crediti'];
        $durata = $rowCostiConfig['durata_corso'];
        $codiceAccreditamento = $rowCostiConfig['codice_accreditamento'];
        $idAttestato = $rowCostiConfig['id_attestato'];
        $oggetto = $rowCostiConfig['email_oggetto'];
        $messaggio = modificaAccentate(html_entity_decode($rowCostiConfig['email_messaggio']));
        $mittente = $rowCostiConfig['email_mittente'];
        
        if($idAttestato>0){
            $rowAttestati = $dblink->get_row("SELECT * FROM lista_attestati WHERE id = '$idAttestato'", true);
        }else{
            $rowAttestati = $dblink->get_row("SELECT * FROM lista_attestati WHERE tipo_documento = 'template base'", true);
            $messaggio = $rowAttestati['descrizione'];
        }
        $orientamento = $rowAttestati['orientamento'];
        $nomeFile = $rowAttestati['nome'];

        $rowCorso = $dblink->get_row("SELECT * FROM lista_corsi WHERE id = '$idCorso'", true);
        $nomeCorso = $rowCorso['nome_prodotto'];
        $codiceCorso = $rowCorso['codice'];

        $tmp = explode(" ",GiraDataOra($dataInizioCorso));
        $dataInizio = $tmp[0];

        $messaggio = str_replace('_XXX_TITOLO_XXX_', $titolo_professionista, $messaggio);
        $messaggio = str_replace('_XXX_PROFESSIONE_XXX_', ucwords(strtolower(html_entity_decode($professione))), $messaggio);
        $messaggio = str_replace('_XXX_COGNOME_XXX_', ucwords(strtolower(html_entity_decode($cognome))), $messaggio);
        $messaggio = str_replace('_XXX_NOME_XXX_', ucwords(strtolower(html_entity_decode($nome))), $messaggio);
        $messaggio = str_replace('_XXX_DATA_INIZIO_XXX_', $dataInizio, $messaggio);
        $messaggio = str_replace('_XXX_DATA_FINE_XXX_', GiraDataOra($dataCompletamento), $messaggio);
        $messaggio = str_replace('_XXX_DATA_NASCITA_XXX_', GiraDataOra($dataDiNascita), $messaggio);
        $messaggio = str_replace('_XXX_PROV_NASCITA_XXX_', $provinciaDiNascita, $messaggio);
        $messaggio = str_replace('_XXX_LUOGO_NASCITA_XXX_', ucwords(strtolower(html_entity_decode($luogoDiNascita))), $messaggio);
        $messaggio = str_replace('_XXX_NOME_CORSO_XXX_', mb_strtoupper(html_entity_decode($nomeCorso)), $messaggio);
        $messaggio = str_replace('_XXX_ORE_CORSO_XXX_', $durata, $messaggio);
        $messaggio = str_replace('_XXX_CODICE_ACCREDITAMENTO_XXX_', $codiceAccreditamento, $messaggio);
        $messaggio = str_replace('_XXX_NUMERO_CREDITI_XXX_', $crediti, $messaggio);
        $messaggio = str_replace('_XXX_CODICE_FISCALE_XXX_', $codiceFiscale, $messaggio);
        $messaggio = str_replace('_XXX_PROVINCIA_ALBO_XXX_', $provinciaAlbo, $messaggio);
        $messaggio = str_replace('_XXX_NUMERO_ORDINE_XXX_', $numeroAlbo, $messaggio);
        
        $htmladd .= $messaggio;
        
    }
    
    $dataArray = explode("-",$dataCompletamento);
    $anno = $dataArray[0];
    $mese = $dataArray[1];
    $filename = "{$cognome}-{$nome}-{$anno}-{$mese}-{$codiceCorso}.pdf";
    
    $sql = "SELECT * FROM lista_iscrizioni WHERE id='" . $idIscrione . "'";
    $row = $dblink->get_row($sql,true);
    
    $nome_corso = $nomeCorso;
    $id_classe = $idClasse;
    $allegato_1 = "".$codiceCorso."/".$anno."/".$mese."/" . $filename;
    $filename_oggetto = $oggetto ." ". $nomeCorso ."";
    
    $dest = $emailProfessionista;
    $ogg = $filename_oggetto;
    
    /*$sql_template = "SELECT * FROM lista_template_email WHERE nome = '".$template_attestato."'";
    $rs_template = $dblink->get_results($sql_template);
    foreach ($rs_template as $row_template) {
        $mittente = $row_template['mittente'];
        $reply = $row_template['reply'];
        $destinatario_admin = $row_template['destinatario'];
        $dest_cc = $row_template['cc'];
        $dest_bcc = $row_template['bcc'];
        $oggetto_da_inviare = $row_template['oggetto'];
        $messaggio_da_inviare = html_entity_decode($row_template['messaggio']);
    }
    
    $messaggio_da_inviare = str_replace('_XXX_COGNOME_PROFESSIONISTA_XXX_', $cognome, $messaggio_da_inviare);
    $messaggio_da_inviare = str_replace('_XXX_NOME_PROFESSIONISTA_XXX_', $nome, $messaggio_da_inviare);
    $messaggio_da_inviare = str_replace('_NOME_DEL_CORSO_', $nome_corso, $messaggio_da_inviare);
    */ 
    $mitt = $mittente;
    $mess = $htmladd;
     
    if (DISPLAY_DEBUG) {
        echo '<li>$mitt = '.$mitt.'</li>';
        echo '<li>$destinatario_admin = '.$destinatario_admin.'</li>';
        echo '<li>$dest = '.$dest.'</li>';
        echo '<li>$ogg = '.$ogg.'</li>';
        echo '<li>$mess = '.$mess.'</li>';
        echo '<li> $template_attestato = '. $template_attestato.'</li>';
    }
     
    $verifica = verificaEmail($dest);
    if($verifica) {
        //require_once BASE_ROOT . "classi/phpmailer/class.phpmailer.php";
        $messaggio = new PHPmailer();
        $messaggio->IsHTML(true);
        //$messaggio->SMTPDebug  = 2;
        $messaggio->IsSMTP();
        # I added SetLanguage like this
        $messaggio->SetLanguage('it', BASE_ROOT . 'classi/phpmailer/language/');
        //  $messaggio->IsSMTP(); // telling the class to use SMTP			//$messaggio->IsSMTP();
        $messaggio->SMTPAuth = true;                  // enable SMTP authentication
        $messaggio->Host = SERVER_HOST_MAIL; // sets the SMTP server
        $messaggio->SMTPSecure = SECURE_SMTP_MAIL;
        $messaggio->Port = PORT_MAIL;
        // set the SMTP port for the GMAIL server
        $messaggio->Username = USER_MAIL; // SMTP account username
        $messaggio->Password = PASS_MAIL;        // SMTP account password
        //echo '<h2>$email_mittente = '.$email_mittente.'</h2>';
        //intestazioni e corpo dell'email
        $messaggio->From = $mitt;
        $messaggio->FromName = $mitt;
        $messaggio->ConfirmReadingTo = $mitt;
        $messaggio->AddReplyTo($mitt);


        //$dest = 'simone.crocco@cemanext.it';
        if(EMAIL_DEBUG){
            if (strlen($destinatario_admin) > 5) {
                $dest = $destinatario_admin;
            }else{
                $dest = trim(EMAIL_TO_SEND_DEBUG);
            }
        }
        
        $dest = str_replace(' ', '', $dest);
        $dest = str_replace(';', ',', $dest);
        $string = trim($dest);
        /* Use tab and newline as tokenizing characters as well  */
        $tok = strtok($string, ",");

        while ($tok !== false) {
            //echo "Word=$tok<br />";
            $messaggio->AddAddress(trim($tok));
            $tok = strtok(",");
        }

        if(!EMAIL_DEBUG){
            if (strlen($dest_cc) > 0) {
                //$messaggio->AddAddress($dest_cc);
                $dest_cc = str_replace(' ', '', $dest_cc);
                $dest_cc = str_replace(';', ',', $dest_cc);
                $string = trim($dest_cc);
                /* Use tab and newline as tokenizing characters as well  */
                $tok = strtok($string, ",");

                while ($tok !== false) {
                    //echo "Word=$tok<br />";
                    $messaggio->AddAddress(trim($tok));
                    $tok = strtok(",");
                }
            }
        }

        
        if(!EMAIL_DEBUG){
        $dest_bcc = 'cucchi@betaformazione.com';
            if (strlen($dest_bcc) > 0) {
                //$messaggio->AddBCC($dest_bcc);
                $dest_bcc = str_replace(' ', '', $dest_bcc);
                $dest_bcc = str_replace(';', ',', $dest_bcc);
                $string = trim($dest_bcc);
                /* Use tab and newline as tokenizing characters as well  */
                $tok = strtok($string, ",");

                while ($tok !== false) {
                    //echo "Word=$tok<br />";
                    $messaggio->AddBCC(trim($tok));
                    $tok = strtok(",");
                }
            }
        }


        //echo '<li>$allegato_1 = '.$allegato_1.'</li>';
        if (strlen($allegato_1) > 3) {
            //echo '<li>fileDoc = lista_fatture/'.$_POST['fileDoc'].'</li>';
            //echo '<li>----------> $allegato_1 = '.$allegato_1.'</li>';
            $messaggio->AddAttachment(BASE_ROOT . "media/lista_attestati/" . $allegato_1);
            //$messaggio->AddAttachment("../media/lista_fatture/'.$allegato_1");
            //$messaggio->AddAttachment("CEMA-NEXT-BROCHURE-21X21-B.pdf");
        } else {
            
        }


        $messaggio->Subject = $ogg;
        $messaggio->Body = stripslashes($mess);

        $return = true;
        
        if (!$messaggio->Send()) {
            $log->log_all_errors('inviaEmailAttestatoDaIdIscrizione -> Email NON Inviata [' . $messaggio->ErrorInfo . '] -> $destinatario = ' . $dest, 'ERRORE');
        
            $return = false;
        } else {
            $return = true;
        }
        
    }
    
    return $return;
    
    
}

//INVIO EMAIL BASE DA TEMPLATE
function inviaEmailTemplate_Base($idProfessionista, $nome_tamplate, $idFatturaDettaglio = 0, $idOrdine = 0){
    global $dblink, $moodle, $log;
    //require_once BASE_ROOT . "classi/phpmailer/class.phpmailer.php";
    $messaggio = new PHPmailer();
    $messaggio->IsHTML(true);

    $messaggio->SetLanguage('it', BASE_ROOT . 'classi/phpmailer/language/');
    $messaggio->IsSMTP(); // telling the class to use SMTP			//$messaggio->IsSMTP();
    $messaggio->SMTPAuth = true;                  // enable SMTP authentication
    $messaggio->Host = SERVER_HOST_MAIL; // sets the SMTP server
    $messaggio->SMTPSecure = SECURE_SMTP_MAIL;
    $messaggio->Port = PORT_MAIL;
    // set the SMTP port for the GMAIL server
    $messaggio->Username = USER_MAIL; // SMTP account username
    $messaggio->Password = PASS_MAIL;        // SMTP account password
    //echo '<h2>$email_mittente = '.$email_mittente.'</h2>';
    //SELECT `id`, `dataagg`, `scrittore`, `stato`, `nome`, `mittente`, `reply`, `destinatario`, `cc`, `bcc`, `oggetto`, `messaggio`, `allegato_1`, `allegato_2`, `allegato_3` FROM `lista_template_email` WHERE 1
    $sql_template = "SELECT * FROM lista_template_email WHERE nome = '" . $nome_tamplate . "'";
    $row_template = $dblink->get_row($sql_template, true);
    //while ($row_template = mysql_fetch_array($rs_template, MYSQL_BOTH)) {
        $mittente = $row_template['mittente'];
        $reply = $row_template['reply'];
        $destinatario_admin = $row_template['destinatario'];
        $dest_cc = $row_template['cc'];
        $dest_bcc = $row_template['bcc'];
        $oggetto_da_inviare = $row_template['oggetto'];
        $messaggio_da_inviare = html_entity_decode($row_template['messaggio']);
    //}

    $sql_professionista = "SELECT * FROM lista_professionisti WHERE id = '" . $idProfessionista . "'";
    $row_professionista = $dblink->get_row($sql_professionista, true);
    //while ($row_professionista = mysql_fetch_array($rs_professionista, MYSQL_BOTH)) {
        $destinatario = $row_professionista['email'];
        $cognome = $row_professionista['cognome'];
        $nome = $row_professionista['nome'];
        $cognome_nome_professionista = $row_professionista['cognome'] . ' ' . $row_professionista['nome'];
    //}

    $sql_password = "SELECT * FROM lista_password WHERE id_professionista = '" . $idProfessionista . "'";
    $row_password = $dblink->get_row($sql_password, true);
    //while ($row_password = mysql_fetch_array($rs_password, MYSQL_BOTH)) {
        $username = $row_password['username'];
        $passwd = $row_password['passwd'];
        $dati_credenziali = "
        Indirizzo: " . MOODLE_DOMAIN_NAME . "
        Username: " . $username . "
        Password: " . $passwd . "
        ";
    //}

    if ($idFatturaDettaglio > 0) {
        $sql_nome_corso = "SELECT * FROM lista_fatture_dettaglio WHERE id = '" . $idFatturaDettaglio . "'";
        $rs_nome_corso = $dblink->get_results($sql_nome_corso);
        $nome_del_corso = "";
        foreach ($rs_nome_corso as $row_nome_corso) {
            $nome_del_corso.= $row_nome_corso['nome_prodotto'] . ' [' . $row_nome_corso['codice_prodotto'] . ']<br>';
        }
    }
    
    if ($idOrdine > 0) {
        $sql_ordine = "SELECT * FROM lista_ordini WHERE id = '" . $idOrdine . "'";
        $rs_ordine = $dblink->get_results($sql_ordine);
        $nome_del_corso = "";
        foreach ($rs_ordine as $row_ordine) {
            $importoOrdine = $row_ordine['importo'];
            $dataCreazione = GiraDataOra($row_ordine['data_iscrizione']);
            $nome_del_corso.= $row_nome_corso['nome_prodotto'] . ' [' . $row_nome_corso['codice_prodotto'] . ']<br>';
            $ordine_testo = "Numero Ordine: <b>$idOrdine/01 del $dataCreazione</b><br>Totale Ordine: <b>$importoOrdine € (Iva Inclusa)</b>";
        }
    }else{
        $ordine_testo = "";
    }

    
    $messaggio_da_inviare = str_replace('_XXX_RIEPILOGO_ACQUISTO_XXX_', $ordine_testo, $messaggio_da_inviare);
    $messaggio_da_inviare = str_replace('_XXX_EMAIL_XXX_', $destinatario, $messaggio_da_inviare);
    $messaggio_da_inviare = str_replace('_XXX_NOME_CLIENTE_XXX_', $cognome_nome_professionista, $messaggio_da_inviare);
    $messaggio_da_inviare = str_replace('_XXX_', $cognome_nome_professionista, $messaggio_da_inviare);
    $messaggio_da_inviare = str_replace('_CREDENZIALI_', $dati_credenziali, $messaggio_da_inviare);
    $messaggio_da_inviare = str_replace('_NOME_DEL_CORSO_', $nome_del_corso, $messaggio_da_inviare);
    $messaggio_da_inviare = str_replace('_NOME_ABBONAMENTO_', $nome_del_corso, $messaggio_da_inviare);

    
    if(EMAIL_DEBUG || $nome_tamplate == 'attivaAbbonamentoFatturaErroreMail' || $nome_tamplate == 'attivaCorsoFatturaErroreMail'){
        if (strlen($destinatario_admin) > 5) {
            $destinatario = $destinatario_admin;
        }else{
            $destinatario = trim(EMAIL_TO_SEND_DEBUG);
        }
    }
    
    //intestazioni e corpo dell'email
    $messaggio->From = $mittente;
    $messaggio->FromName = $mittente;
    $messaggio->ConfirmReadingTo = $destinatario;
    $messaggio->AddReplyTo($reply);
    //$messaggio->AddAddress(trim($destinatario));

    if (strlen($destinatario) > 0) {
        $destinatario = str_replace(' ', '', $destinatario);
        $destinatario = str_replace(';', ',', $destinatario);
        $string = trim($destinatario);
        /* Use tab and newline as tokenizing characters as well  */
        $tok = strtok($string, ",");

        while ($tok !== false) {
            //echo "Word=$tok<br />";
            $messaggio->AddAddress(trim($tok));
            $tok = strtok(",");
        }
    }

    if(!EMAIL_DEBUG){
        if (strlen($dest_cc) > 0) {
            //$messaggio->AddAddress($dest_cc);
            $dest_cc = str_replace(' ', '', $dest_cc);
            $dest_cc = str_replace(';', ',', $dest_cc);
            $string = trim($dest_cc);
            /* Use tab and newline as tokenizing characters as well  */
            $tok = strtok($string, ",");

            while ($tok !== false) {
                //echo "Word=$tok<br />";
                $messaggio->AddAddress(trim($tok));
                $tok = strtok(",");
            }
        }
    }

    if(!EMAIL_DEBUG){
        //$dest_bcc = 'simone.crocco@cemanext.it';
        if (strlen($dest_bcc) > 0) {
            //$messaggio->AddBCC($dest_bcc);
            $dest_bcc = str_replace(' ', '', $dest_bcc);
            $dest_bcc = str_replace(';', ',', $dest_bcc);
            $string = trim($dest_bcc);
            /* Use tab and newline as tokenizing characters as well  */
            $tok = strtok($string, ",");

            while ($tok !== false) {
                //echo "Word=$tok<br />";
                $messaggio->AddBCC(trim($tok));
                $tok = strtok(",");
            }
        }
    }

    $messaggio->Subject = $oggetto_da_inviare;
    $messaggio->Body = stripslashes(nl2br($messaggio_da_inviare));


    if (!$messaggio->Send()) {
        $log->log_all_errors('attivaCorsoFattura -> Email NON Inviata [' . $messaggio->ErrorInfo . '] -> $destinatario = ' . $destinatario, 'ERRORE');
        $return = false;
    } else {
        $return = true;
    }

    return $return;
}

//INVIO EMAIL RICHIESTA DA TEMPLATE
function inviaEmailTemplate_Richiesta($idCalendario, $nome_tamplate) {
    global $dblink, $moodle, $log;
    //require_once BASE_ROOT . "classi/phpmailer/class.phpmailer.php";
    $messaggio = new PHPmailer();
    $messaggio->IsHTML(true);

    # I added SetLanguage like this
    $messaggio->SetLanguage('it', BASE_ROOT . 'classi/phpmailer/language/');
    $messaggio->IsSMTP(); // telling the class to use SMTP			//$messaggio->IsSMTP();
    $messaggio->SMTPAuth = true;                  // enable SMTP authentication
    $messaggio->Host = SERVER_HOST_MAIL; // sets the SMTP server
    $messaggio->SMTPSecure = SECURE_SMTP_MAIL;
    $messaggio->Port = PORT_MAIL;
    // set the SMTP port for the GMAIL server
    $messaggio->Username = USER_MAIL; // SMTP account username
    $messaggio->Password = PASS_MAIL;        // SMTP account password
    //SELECT `id`, `dataagg`, `scrittore`, `stato`, `nome`, `mittente`, `reply`, `destinatario`, `cc`, `bcc`, `oggetto`, `messaggio`, `allegato_1`, `allegato_2`, `allegato_3` FROM `lista_template_email` WHERE 1
    $sql_template = "SELECT * FROM lista_template_email WHERE nome = '" . $nome_tamplate . "'";
    $row_template = $dblink->get_row($sql_template, true);
    //while ($row_template = mysql_fetch_array($rs_template, MYSQL_BOTH)) {
        $mittente = $row_template['mittente'];
        $reply = $row_template['reply'];
        $destinatario_admin = $row_template['destinatario'];
        $dest_cc = $row_template['cc'];
        $dest_bcc = $row_template['bcc'];
        $oggetto_da_inviare = $row_template['oggetto'];
        $messaggio_da_inviare = html_entity_decode($row_template['messaggio'])."<br><br>";
    //}

    $sql_calendario = "SELECT messaggio FROM calendario WHERE id = '" . $idCalendario . "'";
    $row_calendario = $dblink->get_row($sql_calendario, true);
    //while ($row_calendario = mysql_fetch_array($rs_calendario, MYSQL_BOTH)) {
        $messaggio_da_inviare .= $row_calendario['messaggio'];
    //}

    if(EMAIL_DEBUG || $nome_tamplate=="nuovaRichiesta"){
        if (strlen($destinatario_admin) > 5) {
            $destinatario = $destinatario_admin;
        }else{
            $destinatario = trim(EMAIL_TO_SEND_DEBUG);
        }
    }

    //$messaggio_da_inviare = str_replace('_XXX_', $cognome_nome_professionista, $messaggio_da_inviare);
    //$messaggio_da_inviare = str_replace('_CREDENZIALI_', $dati_credenziali, $messaggio_da_inviare);
    //$messaggio_da_inviare = str_replace('_NOME_DEL_CORSO_', $nome_del_corso, $messaggio_da_inviare);
    //$messaggio_da_inviare = str_replace('_NOME_ABBONAMENTO_', $nome_del_corso, $messaggio_da_inviare);

    //intestazioni e corpo dell'email
    $messaggio->From = $mittente;
    $messaggio->FromName = $mittente;
    $messaggio->ConfirmReadingTo = $destinatario;
    $messaggio->AddReplyTo($reply);
    //$messaggio->AddAddress(trim($destinatario));

    if (strlen($destinatario) > 0) {
        $destinatario = str_replace(' ', '', $destinatario);
        $destinatario = str_replace(';', ',', $destinatario);
        $string = trim($destinatario);
        /* Use tab and newline as tokenizing characters as well  */
        $tok = strtok($string, ",");

        while ($tok !== false) {
            //echo "Word=$tok<br />";
            $messaggio->AddAddress(trim($tok));
            $tok = strtok(",");
        }
    }

    if(!EMAIL_DEBUG){
        if (strlen($dest_cc) > 0) {
            //$messaggio->AddAddress($dest_cc);
            $dest_cc = str_replace(' ', '', $dest_cc);
            $dest_cc = str_replace(';', ',', $dest_cc);
            $string = trim($dest_cc);
            /* Use tab and newline as tokenizing characters as well  */
            $tok = strtok($string, ",");

            while ($tok !== false) {
                //echo "Word=$tok<br />";
                $messaggio->AddAddress(trim($tok));
                $tok = strtok(",");
            }
        }
    }

    if(!EMAIL_DEBUG){
        if (strlen($dest_bcc) > 0) {
            //$messaggio->AddBCC($dest_bcc);
            $dest_bcc = str_replace(' ', '', $dest_bcc);
            $dest_bcc = str_replace(';', ',', $dest_bcc);
            $string = trim($dest_bcc);
            /* Use tab and newline as tokenizing characters as well  */
            $tok = strtok($string, ",");

            while ($tok !== false) {
                //echo "Word=$tok<br />";
                $messaggio->AddBCC(trim($tok));
                $tok = strtok(",");
            }
        }
    }

    $messaggio->Subject = $oggetto_da_inviare;
    $messaggio->Body = stripslashes(nl2br($messaggio_da_inviare));


    if (!$messaggio->Send()) {
        $log->log_all_errors('attivaCorsoFattura -> Email NON Inviata [' . $messaggio->ErrorInfo . '] -> $destinatario = ' . $destinatario, 'ERRORE');
        $return = false;
    } else {
        $return = true;
    }

    return $return;
}

//INVIO EMAIL PASSWORD DA TEMPLATE
function inviaEmailTemplate_Password($idListaPassword, $nome_tamplate) {
    global $dblink, $moodle, $log;
    
    //require_once BASE_ROOT . "classi/phpmailer/class.phpmailer.php";
    $messaggio = new PHPmailer();
    $messaggio->IsHTML(true);

    # I added SetLanguage like this
    $messaggio->SetLanguage('it', BASE_ROOT . 'classi/phpmailer/language/');
    $messaggio->IsSMTP(); // telling the class to use SMTP			//$messaggio->IsSMTP();
    $messaggio->SMTPAuth = true;                  // enable SMTP authentication
    $messaggio->Host = SERVER_HOST_MAIL; // sets the SMTP server
    $messaggio->SMTPSecure = SECURE_SMTP_MAIL;
    $messaggio->Port = PORT_MAIL;
    // set the SMTP port for the GMAIL server
    $messaggio->Username = USER_MAIL; // SMTP account username
    $messaggio->Password = PASS_MAIL;        // SMTP account password
    //SELECT `id`, `dataagg`, `scrittore`, `stato`, `nome`, `mittente`, `reply`, `destinatario`, `cc`, `bcc`, `oggetto`, `messaggio`, `allegato_1`, `allegato_2`, `allegato_3` FROM `lista_template_email` WHERE 1
    $sql_template = "SELECT * FROM lista_template_email WHERE nome = '" . $nome_tamplate . "'";
    $row_template = $dblink->get_row($sql_template, true);
    //while ($row_template = mysql_fetch_array($rs_template, MYSQL_BOTH)) {
        $mittente = $row_template['mittente'];
        $reply = $row_template['reply'];
        $destinatario_admin = $row_template['destinatario'];
        $dest_cc = $row_template['cc'];
        $dest_bcc = $row_template['bcc'];
        $oggetto_da_inviare = $row_template['oggetto'];
        $messaggio_da_inviare = html_entity_decode($row_template['messaggio']);
    //}

    $sql_password = "SELECT * FROM lista_password WHERE id = '" . $idListaPassword . "'";
    $row_password = $dblink->get_row($sql_password, true);
    //while ($row_password = mysql_fetch_array($rs_password, MYSQL_BOTH)) {
        $username = $row_password['username'];
        $passwd = $row_password['passwd'];
        $cognome_nome_professionista = $row_password['cognome'] . ' ' . $row_password['nome'];
        $dati_credenziali = "
        Indirizzo: " . MOODLE_DOMAIN_NAME . "
        Username: <b>" . $username . "</b>
        Password: " . $passwd . "
        ";
        $destinatario = $row_password['email'];
    //}
    
        
    $messaggio_da_inviare = str_replace('_XXX_EMAIL_XXX_', $destinatario, $messaggio_da_inviare);
    $messaggio_da_inviare = str_replace('_XXX_', $cognome_nome_professionista, $messaggio_da_inviare);
    $messaggio_da_inviare = str_replace('_CREDENZIALI_', $dati_credenziali, $messaggio_da_inviare);
    //$messaggio_da_inviare = str_replace('_NOME_DEL_CORSO_', $nome_del_corso, $messaggio_da_inviare);
    //$messaggio_da_inviare = str_replace('_NOME_ABBONAMENTO_', $nome_del_corso, $messaggio_da_inviare);

    if(EMAIL_DEBUG || $nome_tamplate == 'inviaPasswordErroreMail'){
        if (strlen($destinatario_admin) > 5) {
            $destinatario = $destinatario_admin;
        }else{
            $destinatario = trim(EMAIL_TO_SEND_DEBUG);
        }
    }

    //intestazioni e corpo dell'email
    $messaggio->From = $mittente;
    $messaggio->FromName = $mittente;
    $messaggio->ConfirmReadingTo = $destinatario;
    $messaggio->AddReplyTo($reply);
    
    //$messaggio->AddAddress(trim($destinatario));

    if (strlen($destinatario) > 0) {
        $destinatario = str_replace(' ', '', $destinatario);
        $destinatario = str_replace(';', ',', $destinatario);
        $string = trim($destinatario);
        /* Use tab and newline as tokenizing characters as well  */
        $tok = strtok($string, ",");

        while ($tok !== false) {
            //echo "Word=$tok<br />";
            $messaggio->AddAddress(trim($tok));
            $tok = strtok(",");
        }
    }

    if(!EMAIL_DEBUG){
        if (strlen($dest_cc) > 0) {
            //$messaggio->AddAddress($dest_cc);
            $dest_cc = str_replace(' ', '', $dest_cc);
            $dest_cc = str_replace(';', ',', $dest_cc);
            $string = trim($dest_cc);
            /* Use tab and newline as tokenizing characters as well  */
            $tok = strtok($string, ",");

            while ($tok !== false) {
                //echo "Word=$tok<br />";
                $messaggio->AddAddress(trim($tok));
                $tok = strtok(",");
            }
        }
    }

    if(!EMAIL_DEBUG){
        if (strlen($dest_bcc) > 0) {
            //$messaggio->AddBCC($dest_bcc);
            $dest_bcc = str_replace(' ', '', $dest_bcc);
            $dest_bcc = str_replace(';', ',', $dest_bcc);
            $string = trim($dest_bcc);
            /* Use tab and newline as tokenizing characters as well  */
            $tok = strtok($string, ",");

            while ($tok !== false) {
                //echo "Word=$tok<br />";
                $messaggio->AddBCC(trim($tok));
                $tok = strtok(",");
            }
        }
    }

    $messaggio->Subject = $oggetto_da_inviare;
    $messaggio->IsHTML(true);
    $messaggio->Body = stripslashes(nl2br($messaggio_da_inviare));


    if (!$messaggio->Send()) {
        $log->log_all_errors('inviaEmailTemplate_Password -> Email NON Inviata [' . $messaggio->ErrorInfo . '] -> $destinatario = ' . $destinatario, 'ERRORE');
        $return = false;
    } else {
        $return = true;
    }

    return $return;
}

function inviaEmailTemplate_Ticket($idTicket, $nome_tamplate) {
    global $dblink, $moodle, $log;
    
    //require_once BASE_ROOT . "classi/phpmailer/class.phpmailer.php";
    $messaggio = new PHPmailer();
    $messaggio->IsHTML(true);

    # I added SetLanguage like this
    $messaggio->SetLanguage('it', BASE_ROOT . 'classi/phpmailer/language/');
    $messaggio->IsSMTP(); // telling the class to use SMTP			//$messaggio->IsSMTP();
    $messaggio->SMTPAuth = true;                  // enable SMTP authentication
    $messaggio->Host = SERVER_HOST_MAIL; // sets the SMTP server
    $messaggio->SMTPSecure = SECURE_SMTP_MAIL;
    $messaggio->Port = PORT_MAIL;
    // set the SMTP port for the GMAIL server
    $messaggio->Username = USER_MAIL; // SMTP account username
    $messaggio->Password = PASS_MAIL;        // SMTP account password
    //SELECT `id`, `dataagg`, `scrittore`, `stato`, `nome`, `mittente`, `reply`, `destinatario`, `cc`, `bcc`, `oggetto`, `messaggio`, `allegato_1`, `allegato_2`, `allegato_3` FROM `lista_template_email` WHERE 1
    $sql_template = "SELECT * FROM lista_template_email WHERE nome = '" . $nome_tamplate . "'";
    $row_template = $dblink->get_row($sql_template, true);
    //while ($row_template = mysql_fetch_array($rs_template, MYSQL_BOTH)) {
        $mittente = $row_template['mittente'];
        $reply = $row_template['reply'];
        $destinatario_admin = $row_template['destinatario'];
        $dest_cc = $row_template['cc'];
        $dest_bcc = $row_template['bcc'];
        $oggetto_da_inviare = $row_template['oggetto'];
        $messaggio_da_inviare = html_entity_decode($row_template['messaggio']);
    //}

    if($nome_tamplate == "nuovoTicketSupporto"){
        $sql_ticket = "SELECT * FROM lista_ticket WHERE id = '" . $idTicket . "'";
        $row_ticket = $dblink->get_row($sql_ticket, true);
        
        $destinatario = $destinatario_admin;

        $oggetto_da_inviare = str_replace('_TICKET_', "Ticket [ID:".$row_ticket['id']."]", $oggetto_da_inviare);
        $messaggio_da_inviare = str_replace('_URL_TICKET_', "<a href=\"".BASE_URL."/moduli/ticket/dettaglio.php?tbl=lista_ticket&id=".$row_ticket['id']."\">Vedi il Ticket [ID:".$row_ticket['id']."]<a>", $messaggio_da_inviare);
        $messaggio_da_inviare = str_replace('_OGGETTO_TICKET_', $row_ticket['oggetto'], $messaggio_da_inviare);
        $messaggio_da_inviare = str_replace('_MITTENTE_TICKET_', $row_ticket['mittente'], $messaggio_da_inviare);
        $messaggio_da_inviare = str_replace('_DATA_TICKET_', GiraDataOra($row_ticket['dataagg']), $messaggio_da_inviare);
    }else{
        $sql_ticket_dett = "SELECT * FROM lista_ticket_dettaglio WHERE id = '" . $idTicket . "'";
        $row_ticket_dett = $dblink->get_row($sql_ticket_dett, true);
        
        if($_SESSION['livello_utente']=='amministratore'){
            $sql_ticket = "SELECT id_mittente FROM lista_ticket WHERE id = '" . $row_ticket_dett['id_ticket'] . "'";
            $row_ticket = $dblink->get_row($sql_ticket, true);
            $destRisp = $dblink->get_row("SELECT email FROM lista_password WHERE id = '" . $row_ticket['id_mittente'] . "'", true);
            $destinatario = $destRisp['email'];
        }else{
            $destinatario = $destinatario_admin;
        }

        $oggetto_da_inviare = str_replace('_TICKET_', "Ticket [ID:".$row_ticket_dett['id_ticket']."]", $oggetto_da_inviare);
        $messaggio_da_inviare = str_replace('_URL_TICKET_', "<a href=\"".BASE_URL."/moduli/ticket/dettaglio.php?tbl=lista_ticket&id=".$row_ticket_dett['id_ticket']."\">Vedi il Ticket [ID:".$row_ticket_dett['id_ticket']."]<a>", $messaggio_da_inviare);
        $messaggio_da_inviare = str_replace('_OGGETTO_TICKET_', $row_ticket_dett['oggetto'], $messaggio_da_inviare);
        $messaggio_da_inviare = str_replace('_MESSAGGIO_TICKET_', $row_ticket_dett['messaggio'], $messaggio_da_inviare);
        $messaggio_da_inviare = str_replace('_MITTENTE_TICKET_', $row_ticket_dett['mittente'], $messaggio_da_inviare);
        $messaggio_da_inviare = str_replace('_DATA_TICKET_', GiraDataOra($row_ticket_dett['dataagg']), $messaggio_da_inviare);
    }
    
    $mittBcc = $dblink->get_row("SELECT email FROM lista_password WHERE id = '" . $_SESSION['id_utente'] . "'", true);
    $dest_bcc = $mittBcc['email'];
    
    if(EMAIL_DEBUG){
        if (strlen($destinatario_admin) > 5) {
            $destinatario = $destinatario_admin;
        }else{
            $destinatario = trim(EMAIL_TO_SEND_DEBUG);
        }
    }

    //$messaggio_da_inviare = str_replace('_XXX_', $cognome_nome_professionista, $messaggio_da_inviare);
    //$messaggio_da_inviare = str_replace('_CREDENZIALI_', $dati_credenziali, $messaggio_da_inviare);
    //$messaggio_da_inviare = str_replace('_NOME_DEL_CORSO_', $nome_del_corso, $messaggio_da_inviare);
    //$messaggio_da_inviare = str_replace('_NOME_ABBONAMENTO_', $nome_del_corso, $messaggio_da_inviare);

    //intestazioni e corpo dell'email
    $messaggio->From = $mittente;
    $messaggio->FromName = $mittente;
    $messaggio->ConfirmReadingTo = $destinatario;
    $messaggio->AddReplyTo($reply);
    //$messaggio->AddAddress(trim($destinatario));

    if (strlen($destinatario) > 0) {
        $destinatario = str_replace(' ', '', $destinatario);
        $destinatario = str_replace(';', ',', $destinatario);
        $string = trim($destinatario);
        /* Use tab and newline as tokenizing characters as well  */
        $tok = strtok($string, ",");

        while ($tok !== false) {
            //echo "Word=$tok<br />";
            $messaggio->AddAddress(trim($tok));
            $tok = strtok(",");
        }
    }

    if(!EMAIL_DEBUG){
        if (strlen($dest_cc) > 0) {
            //$messaggio->AddAddress($dest_cc);
            $dest_cc = str_replace(' ', '', $dest_cc);
            $dest_cc = str_replace(';', ',', $dest_cc);
            $string = trim($dest_cc);
            /* Use tab and newline as tokenizing characters as well  */
            $tok = strtok($string, ",");

            while ($tok !== false) {
                //echo "Word=$tok<br />";
                $messaggio->AddAddress(trim($tok));
                $tok = strtok(",");
            }
        }
    }

    if(!EMAIL_DEBUG){
        if (strlen($dest_bcc) > 0) {
            //$messaggio->AddBCC($dest_bcc);
            $dest_bcc = str_replace(' ', '', $dest_bcc);
            $dest_bcc = str_replace(';', ',', $dest_bcc);
            $string = trim($dest_bcc);
            /* Use tab and newline as tokenizing characters as well  */
            $tok = strtok($string, ",");

            while ($tok !== false) {
                //echo "Word=$tok<br />";
                $messaggio->AddBCC(trim($tok));
                $tok = strtok(",");
            }
        }
    }

    $messaggio->Subject = $oggetto_da_inviare;
    $messaggio->Body = stripslashes(nl2br($messaggio_da_inviare));


    if (!$messaggio->Send()) {
        //echo $messaggio->ErrorInfo;
        $log->log_all_errors('attivaCorsoFattura -> Email NON Inviata [' . $messaggio->ErrorInfo . '] -> $destinatario = ' . $destinatario, 'ERRORE');
        $return = false;
    } else {
        //echo '<li>Email Inviata Correttamente !</li>';
        $return = true;
    }

    return $return;
}

function inviaEmailRinnovoAbbonamento($idCalendario, $template_attestato) {
    global $dblink, $log;
    
    $mitt = USER_MAIL;
    
    $sql = "SELECT * FROM calendario WHERE id='" . $idCalendario . "'";
    $row = $dblink->get_row($sql,true);
    
    $dest = $row['email'];
    $oggetto_da_inviare = $row['oggetto'];
    $messaggio_da_inviare = html_entity_decode($row['messaggio']);
    
    $sql_template = "SELECT * FROM lista_template_email WHERE nome = '".$template_attestato."'";
    $rs_template = $dblink->get_results($sql_template);
    foreach ($rs_template as $row_template) {
        $mittente = $row_template['mittente'];
        $reply = $row_template['reply'];
        $destinatario_admin = $row_template['destinatario'];
        $dest_cc = $row_template['cc'];
        $dest_bcc = $row_template['bcc'];
        //$oggetto_da_inviare = $row_template['oggetto'];
        //$messaggio_da_inviare = html_entity_decode($row_template['messaggio']);
    }
    
    $mitt = $mittente;
    $ogg = $oggetto_da_inviare;
    $mess = $messaggio_da_inviare;
     
    if (DISPLAY_DEBUG) {
        echo '<li>$mitt = '.$mitt.'</li>';
        echo '<li>$destinatario_admin = '.$destinatario_admin.'</li>';
        echo '<li>$dest = '.$dest.'</li>';
        echo '<li>$ogg = '.$ogg.'</li>';
        echo '<li>$mess = '.$mess.'</li>';
        echo '<li> $template_attestato = '. $template_attestato.'</li>';
    }
     
    $verifica = verificaEmail($dest);
    if($verifica) {
        //require_once BASE_ROOT . "classi/phpmailer/class.phpmailer.php";
        $messaggio = new PHPmailer();
        $messaggio->IsHTML(true);
        //$messaggio->SMTPDebug  = 2;
        $messaggio->IsSMTP();
        # I added SetLanguage like this
        $messaggio->SetLanguage('it', BASE_ROOT . 'classi/phpmailer/language/');
        //  $messaggio->IsSMTP(); // telling the class to use SMTP			//$messaggio->IsSMTP();
        $messaggio->SMTPAuth = true;                  // enable SMTP authentication
        $messaggio->Host = SERVER_HOST_MAIL; // sets the SMTP server
        $messaggio->SMTPSecure = SECURE_SMTP_MAIL;
        $messaggio->Port = PORT_MAIL;
        // set the SMTP port for the GMAIL server
        $messaggio->Username = USER_MAIL; // SMTP account username
        $messaggio->Password = PASS_MAIL;        // SMTP account password
        //echo '<h2>$email_mittente = '.$email_mittente.'</h2>';
        //intestazioni e corpo dell'email
        $messaggio->From = $mitt;
        $messaggio->FromName = $mitt;
        $messaggio->ConfirmReadingTo = $mitt;
        $messaggio->AddReplyTo($mitt);


        //$dest = 'simone.crocco@cemanext.it';
        if(EMAIL_DEBUG){
            if (strlen($destinatario_admin) > 5) {
                $dest = $destinatario_admin;
            }else{
                $dest = trim(EMAIL_TO_SEND_DEBUG);
            }
        }
        
        $dest = str_replace(' ', '', $dest);
        $dest = str_replace(';', ',', $dest);
        $string = trim($dest);
        /* Use tab and newline as tokenizing characters as well  */
        $tok = strtok($string, ",");

        while ($tok !== false) {
            //echo "Word=$tok<br />";
            $messaggio->AddAddress(trim($tok));
            $tok = strtok(",");
        }

        if(!EMAIL_DEBUG){
            if (strlen($dest_cc) > 0) {
                //$messaggio->AddAddress($dest_cc);
                $dest_cc = str_replace(' ', '', $dest_cc);
                $dest_cc = str_replace(';', ',', $dest_cc);
                $string = trim($dest_cc);
                /* Use tab and newline as tokenizing characters as well  */
                $tok = strtok($string, ",");

                while ($tok !== false) {
                    //echo "Word=$tok<br />";
                    $messaggio->AddAddress(trim($tok));
                    $tok = strtok(",");
                }
            }
        }

        
        if(!EMAIL_DEBUG){
        //$dest_bcc = 'cucchi@betaformazione.com';
            if (strlen($dest_bcc) > 0) {
                //$messaggio->AddBCC($dest_bcc);
                $dest_bcc = str_replace(' ', '', $dest_bcc);
                $dest_bcc = str_replace(';', ',', $dest_bcc);
                $string = trim($dest_bcc);
                /* Use tab and newline as tokenizing characters as well  */
                $tok = strtok($string, ",");

                while ($tok !== false) {
                    //echo "Word=$tok<br />";
                    $messaggio->AddBCC(trim($tok));
                    $tok = strtok(",");
                }
            }
        }
        
        $messaggio->Subject = $ogg;
        $messaggio->Body = stripslashes($mess);

        $return = true;
        
        if (!$messaggio->Send()) {
            $log->log_all_errors('inviaEmailRinnovoAbbonamento -> Email NON Inviata [' . $messaggio->ErrorInfo . '] -> $destinatario = ' . $dest, 'ERRORE');
        
            $return = false;
        } else {
            $return = true;
        }
        
    }
    
    return $return;
    
}

?>
