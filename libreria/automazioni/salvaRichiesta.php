<?php
session_start();
include_once($_SERVER['DOCUMENT_ROOT'].'/config/connDB.php');
include_once(BASE_ROOT.'libreria/libreria.php');

if(isset($_POST)){
    
    if(strlen($_POST['cognome'])>0 && strlen($_POST['nome'])>0 && strlen($_POST['email'])>0){
    
        $codiceFiscale = "";
        $codiceUtente = "";

        if(strlen($_POST['codice_cliente'])==16) $codiceFiscale = $_POST['codice_cliente'];
        else $codiceUtente = $_POST['codice_cliente'];

        if(empty($_POST['id_campagna'])){
            $_POST['id_campagna'] = 2; // SE ID CAMPAGNA VUOTO SETTO SEMPRE CAMPAGNA BASE 2
        }

        $rowCampagna = $dblink->get_row("SELECT id_tipo_marketing, nome, id_prodotto FROM lista_campagne WHERE id = '".$_POST['id_campagna']."'", true);
        $rowMarketing = $dblink->get_row("SELECT nome FROM lista_tipo_marketing WHERE id = '".$rowCampagna['id_tipo_marketing']."'", true);

        if(!empty($_POST['id_prodotto'])){
            $rowCampagna['id_prodotto'] = $_POST['id_prodotto']; // SE ID PRODOTTO NON E' VUOTO LO SOVRASCRIVO ALLA CAMPAGNA
        }
        
        $adMessaggio = "";
        
        if(!empty($_POST['professione'])){
            $adMessaggio.= "\\nProfessione: ".strip_tags($dblink->filter($_POST['professione']));
        }
        
        if(!empty($_POST['ora_contatto'])){
            $adMessaggio.= "\\nOra di Contatto: ".$dblink->filter($_POST['ora_contatto']);
        }
        
        if(!empty($_POST['privacy'])){
            $adMessaggio.= "\\nAcconsente Privacy: SI";
        }
        
        $insert = array(
            "dataagg" => date("Y-m-d H:i:s"),
            "scrittore" => $dblink->filter($_POST['cognome'])." ".$dblink->filter($_POST['nome']),
            "datainsert" => date("Y-m-d"),
            "orainsert" => date("H:i:s"),
            "data" => date("Y-m-d"),
            "ora" => date("H:i:s"),
            "etichetta" => 'Nuova Richiesta',
            "oggetto" => $dblink->filter($rowCampagna['nome']),
            "messaggio" => "Nome: ".$dblink->filter($_POST['nome'])."\\nCognome: ".$dblink->filter($_POST['cognome'])."\\nCodice Cliente: ".$dblink->filter($_POST['codice_cliente'])."\\nTelefono: ".$dblink->filter($_POST['telefono'])."\\nE-Mail: ".$dblink->filter($_POST['email'])."\\n\\nTipo Marketing: ".$dblink->filter($rowMarketing['nome'])."\\nNome Campagna: ".$dblink->filter($rowCampagna['nome']).$adMessaggio."\\nURL: ".$dblink->filter($_POST['referer'])."\\n\\nMESSAGGIO\\n".$dblink->filter($_POST['messaggio']),
            "mittente" => $dblink->filter($_POST['cognome'])." ".$dblink->filter($_POST['nome']),
            "destinatario" => "",
            "priorita" => "Alta",
            "stato" => "In Attesa di Controllo Automatico",
            "tipo_marketing" => $dblink->filter(strtoupper($rowMarketing['nome'])),
            "id_campagna" => $_POST['id_campagna'],
            "id_prodotto" => $rowCampagna['id_prodotto'],
            "id_tipo_marketing" => $rowCampagna['id_tipo_marketing'],
            "giorno" => date("d"),
            "mese" => date("m"),
            "anno" => date("Y"),
            "campo_1" => $dblink->filter($_POST['nome']),
            "campo_2" => $dblink->filter($_POST['cognome']),
            "campo_3" => $dblink->filter($codiceFiscale),
            "campo_4" => $dblink->filter($_POST['telefono']),
            "campo_5" => $dblink->filter($_POST['email']),
            "campo_6" => $dblink->filter($_POST['tipo_campagna']),
            "campo_7" => $dblink->filter($_POST['nome_campagna']),
            "campo_8" => $dblink->filter($_POST['referer']),
            "campo_9" => $dblink->filter($codiceUtente),
            "campo_11" => strip_tags($dblink->filter($_POST['professione'])),
            "campo_12" => $dblink->filter($_POST['ora_contatto']),
            "campo_13" => $dblink->filter($_POST['privacy']),
            "nome" => $dblink->filter($_POST['nome']),
            "cognome" => $dblink->filter($_POST['cognome']),
            "telefono" => $dblink->filter($_POST['telefono']),
            "email" => $dblink->filter($_POST['email']),
            "notifica_email" => "Si",
            "notifica_sms" => "No"
        );

        $ok = true;
        $ok = $ok && $dblink->insert("calendario", $insert);

        if($ok){
            include_once(BASE_ROOT.'libreria/automazioni/autoCampagneStatistiche.php');
            include_once(BASE_ROOT.'libreria/automazioni/autoNuovaRichiestaControllo.php');

            if(isset($_POST['nuovo_form_beta']) && $_POST['nuovo_form_beta'] == "1"){
                if(isset($_POST['thankyou_page']) && !empty($_POST['thankyou_page'])){
                    header("Location:".$_POST['thankyou_page']."?idCampagna=".$_POST['id_campagna']);
                }else{
                    header("Location:".WP_DOMAIN_NAME."/grazie-per-averci-contattato/?idCampagna=".$_POST['id_campagna']);
                }
            }else{
                header("Location:".ERP_DOMAIN_NAME."/libreria/automazioni/form_sito.php?ret=1");
            }
        }else{
            $log->log_all_errors("salvaRichiesta dal Sito -> Si è verificato un errore nella query non è stata inserita la richiesta: ".var_export($_POST,true), "ERRORE");
            if(isset($_POST['nuovo_form_beta']) && $_POST['nuovo_form_beta'] == "1"){
                header("Location:".$_POST['referer']);
            }else{
                header("Location:".ERP_DOMAIN_NAME."/libreria/automazioni/form_sito.php?ret=0");
            }
        }
    }else{
        if(isset($_POST['nuovo_form_beta']) && $_POST['nuovo_form_beta'] == "1"){
            header("Location:".$_POST['referer']);
        }else{
            header("Location:".ERP_DOMAIN_NAME."/libreria/automazioni/form_sito.php?ret=2");
        }
    }
}

?>
