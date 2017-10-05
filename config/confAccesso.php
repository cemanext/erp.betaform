<?php

if ($_SESSION['livello_utente'] == 'amministratore') {
    $where_scrittore = '';
    $where_lista_esercizi = '';
    $where_lista_listini = "";
    $where_lista_commesse_dettaglio = "";
    $where_lista_fatture = "";
    $where_calendario = "";
    $where_calendario_all = "";
    $where_lista_richieste_stati = "";
    $where_lista_password = " AND livello!='cliente' ";
    $where_lista_fatture = "";
    $where_lista_preventivi = "";
    $where_lista_aziende = "";
    $where_lista_professionisti = "";
    $where_lista_professioni = "";
    $where_lista_albi_professionali = "";
    $where_lista_ticket = "";
    $where_lista_ticket_stati = "";
    $where_lista_preventivi_dettaglio = "";
    $where_lista_fatture_dettaglio = "";
    $where_lista_campagne = "";
    $where_lista_prodotti = "";
    $where_lista_corsi = "";
    $where_lista_corsi_dettaglio = "";
    $where_lista_iscrizioni = "";
    $where_lista_iscrizioni_configurazione = "";
    $where_lista_classi = "";
    $where_lista_ordini = "";
    $where_lista_costi = "";
    $where_calendario_esami = "";
    $where_calendario_esami_iscrizioni = "";
    $where_lista_attestati = "";
    $where_lista_prodotti_categorie = "";
    $where_lista_prodotti_tipologie = "";
    $where_lista_prodotti_gruppi = "";
    $where_lista_template_email = "";
    $where_lista_docenti = "";
    $where_lista_aule = "";
    $where_lista_consuntivo_vendite = " AND sezionale NOT LIKE 'CN%' ";
} elseif ($_SESSION['livello_utente'] == 'betaadmin') {
    $where_scrittore = '';
    $where_lista_esercizi = '';
    $where_lista_listini = "";
    $where_lista_commesse_dettaglio = "";
    $where_lista_fatture = " AND sezionale NOT LIKE 'CN%' ";
    $where_calendario = " AND calendario.id_agente!='7' ";
    $where_calendario_all = " AND calendario.id_agente!='7' ";
    $where_lista_richieste_stati = " AND lista_richieste_stati.livello LIKE 'betaadmin'";
    $where_lista_password = " AND (livello!='cliente' AND livello!='amministratore') ";
    $where_lista_fatture = " AND id_agente!='7' ";
    $where_lista_preventivi = " AND sezionale NOT LIKE 'CN%' AND id_agente!='7' ";
    $where_lista_consuntivo_vendite = " AND sezionale NOT LIKE 'CN%' AND id_agente!='7' ";
} elseif ($_SESSION['livello_utente'] == 'backoffice') {
    $where_scrittore = '';
    $where_lista_esercizi = '';
    $where_lista_listini = "";
    $where_lista_commesse_dettaglio = "";
    $where_lista_fatture = " AND sezionale NOT LIKE 'CN%' ";
    $where_calendario = " AND calendario.id_agente!='7' ";
    $where_calendario_all = " AND calendario.id_agente!='7' ";
    $where_lista_richieste_stati = " AND lista_richieste_stati.livello LIKE 'betaadmin'";
    $where_lista_password = " AND (livello!='cliente' AND livello!='amministratore') ";
    $where_lista_fatture = " AND id_agente!='7' ";
    $where_lista_preventivi = " AND sezionale NOT LIKE 'CN%' AND id_agente!='7' ";
    $where_lista_consuntivo_vendite = " AND sezionale NOT LIKE 'CN%' AND id_agente!='7' ";
} elseif ($_SESSION['livello_utente'] == 'backoffice_commerciale') {
    $where_scrittore = '';
    $where_lista_esercizi = '';
    $where_lista_listini = "";
    $where_lista_commesse_dettaglio = "";
    $where_lista_fatture = " AND sezionale NOT LIKE 'CN%' ";
    $where_calendario = " AND calendario.id_agente!='7' ";
    $where_calendario_all = " AND calendario.id_agente!='7' ";
    $where_lista_richieste_stati = " AND lista_richieste_stati.livello LIKE 'betaadmin'";
    $where_lista_password = " AND (livello!='cliente' AND livello!='amministratore') ";
    $where_lista_fatture = " AND id_agente!='7' ";
    $where_lista_preventivi = " AND sezionale NOT LIKE 'CN%' AND id_agente!='7' ";
    $where_lista_consuntivo_vendite = " AND sezionale NOT LIKE 'CN%' AND id_agente!='7' ";
} elseif ($_SESSION['livello_utente'] == 'assistenza') {
    $where_scrittore = " AND md5(scrittore)='" . MD5($_SESSION['cognome_nome_utente']) . "'";
    $where_lista_esercizi = '';
    $where_lista_preventivi = " AND sezionale NOT LIKE 'CN%' AND (lista_preventivi.id_agente='" . $_SESSION['id_utente'] . "' OR md5(lista_preventivi.cognome_nome_agente)='" . MD5($_SESSION['cognome_nome_utente']) . "')";
    $where_lista_consuntivo_vendite = " AND sezionale NOT LIKE 'CN%' AND (lista_preventivi.id_agente='" . $_SESSION['id_utente'] . "' OR md5(lista_preventivi.cognome_nome_agente)='" . MD5($_SESSION['cognome_nome_utente']) . "')";
    $where_lista_listini = " AND (gruppo_agente='" . $_SESSION['gruppo_utente'] . "')";
    $where_lista_commesse_dettaglio = " AND  lista_commesse_dettaglio.id_operatore = " . $_SESSION['id_contatto_utente'] . "";
    $where_lista_fatture = " AND sezionale NOT LIKE 'CN%' AND lista_fatture.id_agente='" . $_SESSION['id_utente'] . "'";
    $where_calendario = "";
    $where_calendario = " AND etichetta LIKE 'Nuova Richiesta'";
    $where_calendario_all = " AND calendario.id_agente!='7' ";
    $where_lista_richieste_stati = " AND lista_richieste_stati.livello LIKE 'betaadmin'";
    $where_lista_password = " AND (livello!='cliente' AND livello!='amministratore') ";
} elseif ($_SESSION['livello_utente'] == 'commerciale') {
    $where_scrittore = " AND md5(scrittore)='" . MD5($_SESSION['cognome_nome_utente']) . "'";
    $where_lista_esercizi = '';
    $where_lista_preventivi = " AND sezionale NOT LIKE 'CN%' AND (lista_preventivi.id_agente='" . $_SESSION['id_utente'] . "' OR md5(lista_preventivi.cognome_nome_agente)='" . MD5($_SESSION['cognome_nome_utente']) . "')";
    $where_lista_consuntivo_vendite = " AND sezionale NOT LIKE 'CN%' AND (lista_preventivi.id_agente='" . $_SESSION['id_utente'] . "' OR md5(lista_preventivi.cognome_nome_agente)='" . MD5($_SESSION['cognome_nome_utente']) . "')";
    $where_lista_listini = " AND (gruppo_agente='" . $_SESSION['gruppo_utente'] . "')";
    $where_lista_commesse_dettaglio = " AND  lista_commesse_dettaglio.id_operatore = " . $_SESSION['id_contatto_utente'] . "";
    $where_lista_fatture = " AND sezionale NOT LIKE 'CN%' AND lista_fatture.id_agente='" . $_SESSION['id_utente'] . "'";
    $where_calendario = " AND (md5(mittente)='" . MD5($_SESSION['cognome_nome_utente']) . "' OR md5(destinatario)='" . MD5($_SESSION['cognome_nome_utente']) . "')";
    $where_calendario = " AND calendario.id_agente='" . $_SESSION['id_utente'] . "' AND etichetta LIKE 'Nuova Richiesta'";
    $where_calendario_all = " AND calendario.id_agente!='7' ";
    $where_lista_richieste_stati = " AND lista_richieste_stati.livello LIKE '" . $_SESSION['livello_utente'] . "'";
    $where_lista_password = " AND (livello!='cliente' AND livello!='amministratore') ";
} elseif ($_SESSION['livello_utente'] == 'gestore') {
    $where_scrittore = " AND md5(scrittore)='" . MD5($_SESSION['cognome_nome_utente']) . "'";
    $where_lista_esercizi = '';
    $where_lista_preventivi = " AND (lista_preventivi.id_agente='" . $_SESSION['id_utente'] . "' OR md5(lista_preventivi.cognome_nome_agente)='" . MD5($_SESSION['cognome_nome_utente']) . "')";
    $where_lista_consuntivo_vendite = " AND (lista_preventivi.id_agente='" . $_SESSION['id_utente'] . "' OR md5(lista_preventivi.cognome_nome_agente)='" . MD5($_SESSION['cognome_nome_utente']) . "')";
    $where_lista_listini = " AND (gruppo_agente='" . $_SESSION['gruppo_utente'] . "')";
    $where_lista_commesse_dettaglio = " AND  lista_commesse_dettaglio.id_operatore = " . $_SESSION['id_contatto_utente'] . "";
    $where_lista_richieste_stati = " AND lista_richieste_stati.livello LIKE '" . $_SESSION['livello_utente'] . "'";
    $where_lista_password = " AND (livello!='cliente' AND livello!='amministratore') ";
} elseif ($_SESSION['livello_utente'] == 'agente') {
    $where_scrittore = " AND md5(scrittore)='" . MD5($_SESSION['cognome_nome_utente']) . "'";
    $where_lista_esercizi = '';
    $where_lista_prodotti = " AND (id_agente='" . $_SESSION['id_utente'] . "' OR gruppo_agente='" . $_SESSION['gruppo_utente'] . "')";
    $where_lista_nominativi = " AND (id_agente='" . $_SESSION['id_utente'] . "')";
    $where_lista_preventivi = " AND (lista_preventivi.id_agente='" . $_SESSION['id_utente'] . "' OR md5(lista_preventivi.cognome_nome_agente)='" . MD5($_SESSION['cognome_nome_utente']) . "')";
    $where_lista_listini = " AND (gruppo_agente='" . $_SESSION['gruppo_utente'] . "')";
    $where_lista_commesse_dettaglio = " AND  lista_commesse_dettaglio.id_operatore = " . $_SESSION['id_contatto_utente'] . "";
    $where_calendario = " AND (md5(mittente)='" . MD5($_SESSION['cognome_nome_utente']) . "' OR md5(destinatario)='" . MD5($_SESSION['cognome_nome_utente']) . "')";
    $where_lista_richieste_stati = " AND lista_richieste_stati.livello LIKE '" . $_SESSION['livello_utente'] . "'";
} elseif ($_SESSION['livello_utente'] == 'operatore') {
    $where_scrittore = " AND md5(scrittore)='" . MD5($_SESSION['cognome_nome_utente']) . "'";
    $where_lista_esercizi = " AND md5(scrittore)='" . MD5($_SESSION['cognome_nome_utente']) . "'";
    $where_lista_preventivi = " AND (lista_preventivi.id_agente='" . $_SESSION['id_utente'] . "' OR md5(lista_preventivi.cognome_nome_agente)='" . MD5($_SESSION['cognome_nome_utente']) . "')";
    $where_lista_listini = " AND (gruppo_agente='" . $_SESSION['gruppo_utente'] . "')";
    $where_lista_commesse_dettaglio = " AND  lista_commesse_dettaglio.id_operatore = " . $_SESSION['id_contatto_utente'] . "";
    $where_calendario = " AND (md5(mittente)='" . MD5($_SESSION['cognome_nome_utente']) . "' OR md5(destinatario)='" . MD5($_SESSION['cognome_nome_utente']) . "')";
    $where_lista_richieste_stati = " AND lista_richieste_stati.livello LIKE '" . $_SESSION['livello_utente'] . "'";
} elseif (strtolower($_SESSION['livello_utente']) == 'operatore_tlm') {
    $where_scrittore = " AND md5(scrittore)='" . MD5($_SESSION['cognome_nome_utente']) . "'";
    $where_lista_esercizi = " AND md5(scrittore)='" . MD5($_SESSION['cognome_nome_utente']) . "'";
    $where_lista_preventivi = " AND (lista_preventivi.id_agente='" . $_SESSION['id_utente'] . "' OR md5(lista_preventivi.cognome_nome_agente)='" . MD5($_SESSION['cognome_nome_utente']) . "')";
    $where_lista_listini = " AND (gruppo_agente='" . $_SESSION['gruppo_utente'] . "')";
    $where_lista_commesse_dettaglio = " AND lista_commesse_dettaglio.id_operatore = '" . $_SESSION['id_contatto_utente'] . "' ";
    $where_lista_commesse_dettaglio = " AND  lista_commesse_dettaglio.id_operatore = " . $_SESSION['id_contatto_utente'] . "";
    $where_calendario = " AND (md5(mittente)='" . MD5($_SESSION['cognome_nome_utente']) . "' OR md5(destinatario)='" . MD5($_SESSION['cognome_nome_utente']) . "')";
    $where_lista_richieste_stati = " AND lista_richieste_stati.livello LIKE '" . $_SESSION['livello_utente'] . "'";
} elseif (strtolower($_SESSION['livello_utente']) == 'cliente') {
    $_SESSION['cognome_nome_utente']    = null;
    $_SESSION['utente']                 = null;
    $_SESSION['id_utente']              = null;
    $_SESSION['livello_utente']         = null;
    $_SESSION['nome_utente']            = null;
    $_SESSION['cognome_utente']         = null;
    $_SESSION['username_utente']        = null;
    $_SESSION['cellulare_utente']       = null;
    $_SESSION['cognome_nome_utente']    = null;
    $_SESSION['email_utente']           = null;
    $_SESSION['stato_utente']           = null;

    header("Location: http://elearning.betaformazione.com/login/index.php");
    die();
} else {

    $where_scrittore = " AND md5(scrittore)='" . MD5($_SESSION['cognome_nome_utente']) . "'";
    $where_lista_esercizi = " AND md5(scrittore)='" . MD5($_SESSION['cognome_nome_utente']) . "'";
    $where_lista_listini = " AND (gruppo_agente='" . $_SESSION['gruppo_utente'] . "')";
    $where_lista_commesse_dettaglio = " AND  lista_commesse_dettaglio.id_operatore = " . $_SESSION['id_contatto_utente'] . "";
    $where_calendario = " AND (md5(mittente)='" . MD5($_SESSION['cognome_nome_utente']) . "' OR md5(destinatario)='" . MD5($_SESSION['cognome_nome_utente']) . "')";
    $where_lista_richieste_stati = " AND lista_richieste_stati.livello LIKE '" . $_SESSION['livello_utente'] . "'";
}

$where_lista_menu = " AND `lista_menu`.livello='" . $_SESSION['livello_utente'] . "'";

/* MODULO CONTATTI - CONFIGURAZIONE */

$title_contatti = "Gestione Contatti";
$description_contatti = "Modulo per la gestione dei contatti";
$keywords_contatti = "";

$site_name = "Beta Formazione";
$author = "CEMA NEXT";

$config_tipo_lista_menu = "betaform_erp";

include_once(BASE_ROOT . 'config/confTabelle.php');
include_once(BASE_ROOT . 'libreria/libreria.php');
?>
