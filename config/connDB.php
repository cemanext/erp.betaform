<?php
session_start();
ini_set('display_errors', '1');
error_reporting(E_ALL & ~E_NOTICE);

/* VERSIONE */
define("VERSIONE", "v2.0.0 (AREA SVILUPPO PHP ".phpversion().")");
define("COPYRIGHT", "CEMA NEXT Srl");
define("LAST_UPDATE", date("d-m-Y"));

/* SERVER GESTIONALE */
define("BASE_URL", (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME']."" : "http://".$_SERVER['SERVER_NAME']."");
define("BASE_ROOT", $_SERVER['DOCUMENT_ROOT']."/");
define('ERP_DOMAIN_NAME', 'http://dev.xxxxxxxx.com');

define( 'DB_HOST', 'localhost' ); // set database host
define( 'DB_USER', 'root' ); // set database user

define( 'DB_PASS', 'root' ); // set database password
define( 'DB_NAME', 'erp_betaform' ); // set database name

define('DURATA_CORSO', "190"); //6 MESI IN GIORNI
define('DURATA_CORSO_INGEGNERI', "190"); //6 MESI IN GIORNI
define('DURATA_ABBONAMENTO', "370"); //12 MESI IN GIORNI
define('DURATA_PASSWORD_UTENTE', "190"); //6 MESI IN GIORNI

/* ERP - CONFIGURAZIONE */
define('SITE_NAME', "Beta FORM");
define('AUTHOR', "CEMA NEXT");
define('CONFIG_TIPO_LISTA_MENU', "betaform_erp");

//** CONFIGURAZIONE MAIL **/
define("BASE_SERVER_HOST_MAIL", "");
define("BASE_SECURE_SMTP_MAIL", "");
define("BASE_PORT_MAIL", "25");
define("BASE_PASS_MAIL", "");
define("BASE_USER_MAIL", "");
define('DOMINIO_MAIL_AUTENTICATE', '@betaform.com');

define('SUFFISSO_CODICE_CLIENTE', "BF"); //BF - Betaformazione
define('SEPARATORE_FATTURA', "/"); // CODICE/SEZIONALE
define('PREFIX_FILE_PDF_FATTURA', "BetaFormazione_Fattura_");
define('MAIL_DA_INVIA_FATTURA', "erp@betaformazione.com");
define('PREFIX_MAIL_OGGETTO_INIVA_FATTURA', "Fattura ");
define('MAIL_OGGETTO_INVIA_FATTURA', "Beta Formazione s.r.l. - ");
define('MAIL_TESTO_INVIA_FATTURA', 'Gentile Cliente,<br>
            in allegato alla presente Le inviamo la copia (in formato PDF) della fattura relativa ai servizi da noi forniti.<br>
            Il presente invio SOSTITUISCE INTEGRALMENTE quello effettuato in modo tradizionale a mezzo servizio postale .<br>
            Tale operazione &egrave; ammessa dalla normativa fiscale in essere, relativa alla "Trasmissione delle Fatture" per via Telematica:<br>
            - R.M. n. 571134 del 19/07/88 - (posta elettronica)<br>
            - R.M. n. 450217 del 30/07/90 - (procedure informatizzate)<br>
            - R.M. n. 107 del 04/07/01 - (trasmissione fatture)<br>
            - R.M. n. 202/E del 04/12/01 - (archiviazione fatture)<br>
            Risoluzioni che forniscono chiarimenti in ordine alle condizioni necessarie per l\'ammissibilit&agrave; ai sensi dell\'art. 21 D.P.R. 26/10/72 n. 633 della procedura di trasmissione e memorizzazione delle fatture mediante sistemi combinati fondati sull\'impiego congiunto di supporti informatici, telefax e posta elettronica.<br>
            La normativa nazionale italiana ad oggi NON consente l\'archiviazione di alcun documento contabile in formato digitale.<br>
            Quindi &egrave; necessario GENERARNE UNA STAMPA e procedere alla relativa archiviazione come da prassi a norma di legge<br>
            Il file &egrave; in formato pdf di seguito il link del software gratuito per la visualizzazione e la stampa di questo formato:<br>
            http://get.adobe.com/it/reader/<br><br>

	<br><img src="http://landing.betaformazione.com/wp-content/uploads/2017/03/logo-head.png" alt="Beta Formazione s.r.l." title="Beta Formazione s.r.l.">
	<br>
	<h5>Ente n° 8124, accreditato alla Regione Emilia-Romagna<br>
	con D.G.R. n. 481 dell\'11 Aprile 2011<br>
	Azienda operante con sistema di<br>
	Qualit&agrave; Certificato da QGest<br>
	Certificato n. Q/00522/14</h5>
        <b>Sede legale e operativa :via Piratello, 66/68  48022 Lugo (RA)<b><br>
        Tel. <b>0545 916279</b> - Fax <b>0545 030139</b> - <a href="http://www.betaformazione.com/">www.betaformazione.com</a>
        <h6>Le informazioni contenute in questa e-mail e negli eventuali allegati sono riservate e destinate esclusivamente alla persona sopraindicata. Qualora non foste il destinatario, siete pregati di distruggere questo messaggio e notificarci il problema immediatamente.<br>
        In ogni caso, non dovrete spedire a terzi, copiare, usare o diffondere il contenuto di questa e-mail e degli eventuali allegati. Si ricorda che la diffusione l\'utilizzo e/o la conservazione dei dati ricevuti per errore costituiscono violazione alle disposizioni del D.lgs. n. 196/2003 (Codice in materia di protezione dei dati personali) oltre a costituire violazione di carattere penale ai sensi dell\'art. 616 C.P.
        </h6>');

define('PREFIX_FILE_PDF_PREVENTIVO', "BetaFormazione_Ordine_");
define('MAIL_DA_INVIA_PREVENTIVO', "erp@betaformazione.com");
define('PREFIX_MAIL_OGGETTO_INIVA_PREVENTIVO', "Ordine ");
define('MAIL_OGGETTO_INVIA_PREVENTIVO', "Beta Formazione s.r.l. - ");
define('MAIL_TESTO_INVIA_PREVENTIVO', 'Gentile Cliente,<br>
            in allegato alla presente Le inviamo la copia (in formato PDF) dell\'ordine relativa ai servizi da noi forniti.<br>
            Il presente invio SOSTITUISCE INTEGRALMENTE quello effettuato in modo tradizionale a mezzo servizio postale .<br>
            Tale operazione &egrave; ammessa dalla normativa fiscale in essere, relativa alla "Trasmissione delle Fatture" per via Telematica:<br>
            - R.M. n. 571134 del 19/07/88 - (posta elettronica)<br>
            - R.M. n. 450217 del 30/07/90 - (procedure informatizzate)<br>
            - R.M. n. 107 del 04/07/01 - (trasmissione fatture)<br>
            - R.M. n. 202/E del 04/12/01 - (archiviazione fatture)<br>
            Risoluzioni che forniscono chiarimenti in ordine alle condizioni necessarie per l\'ammissibilit&agrave; ai sensi dell\'art. 21 D.P.R. 26/10/72 n. 633 della procedura di trasmissione e memorizzazione delle fatture mediante sistemi combinati fondati sull\'impiego congiunto di supporti informatici, telefax e posta elettronica.<br>
            La normativa nazionale italiana ad oggi NON consente l\'archiviazione di alcun documento contabile in formato digitale.<br>
            Quindi &egrave; necessario GENERARNE UNA STAMPA e procedere alla relativa archiviazione come da prassi a norma di legge<br>
            Il file &egrave; in formato pdf di seguito il link del software gratuito per la visualizzazione e la stampa di questo formato:<br>
            http://get.adobe.com/it/reader/<br><br>

	<br><img src="http://landing.betaformazione.com/wp-content/uploads/2017/03/logo-head.png" alt="Beta Formazione s.r.l." title="Beta Formazione s.r.l.">
	<br>
	<h5>Ente n° 8124, accreditato alla Regione Emilia-Romagna<br>
	con D.G.R. n. 481 dell\'11 Aprile 2011<br>
	Azienda operante con sistema di<br>
	Qualit&agrave; Certificato da QGest<br>
	Certificato n. Q/00522/14</h5>
        <b>Sede legale e operativa :via Piratello, 66/68  48022 Lugo (RA)<b><br>
        Tel. <b>0545 916279</b> - Fax <b>0545 030139</b> - <a href="http://www.betaformazione.com/">www.betaformazione.com</a>
        <h6>Le informazioni contenute in questa e-mail e negli eventuali allegati sono riservate e destinate esclusivamente alla persona sopraindicata. Qualora non foste il destinatario, siete pregati di distruggere questo messaggio e notificarci il problema immediatamente.<br>
        In ogni caso, non dovrete spedire a terzi, copiare, usare o diffondere il contenuto di questa e-mail e degli eventuali allegati. Si ricorda che la diffusione l\'utilizzo e/o la conservazione dei dati ricevuti per errore costituiscono violazione alle disposizioni del D.lgs. n. 196/2003 (Codice in materia di protezione dei dati personali) oltre a costituire violazione di carattere penale ai sensi dell\'art. 616 C.P.
        </h6>');

define('TESTO_CONFIGURAZIONE_ATTESTATO','<h2>ATTESTATO di FREQUENZA</h2>Si attesta che<br>nel periodo dal _XXX_DATA_INIZIO_XXX_  al _XXX_DATA_FINE_XXX_ <br><br><h1>_XXX_TITOLO_XXX_ _XXX_COGNOME_XXX_ _XXX_NOME_XXX_</h1>nato a _XXX_LUOGO_NASCITA_XXX_  (_XXX_PROV_NASCITA_XXX_) il _XXX_DATA_NASCITA_XXX_ <br><br><br>ha frequentato il corso di<h3>\" _XXX_NOME_CORSO_XXX_ \"</h3>Durata del percorso formativo: <b>_XXX_ORE_CORSO_XXX_ ore</b><br>Codice: <b>_XXX_CODICE_ACCREDITAMENTO_XXX_</b><br>Crediti Formativi Professionali: <b>_XXX_NUMERO_CREDITI_XXX_</b>');
define('FIRMA_CONFIGURAZIONE_ATTESTATO','<b>Lugo (RA), _XXX_DATA_FIRMA_XXX_</b>');
define('EMAIL_DA_CONFIGURAZIONE_ATTESTATO','attestati@betaformazione.com');
define('EMAIL_TESTO_CONFIGURAZIONE_ATTESTATO','&lt;div&gt;Gentile &lt;b&gt;_XXX_NOME_XXX_ _XXX_COGNOME_XXX_&lt;/b&gt;,&lt;br&gt;&lt;/div&gt;&lt;div&gt;la presente per confermarle che sono state formalizzate le comunicazioni al CNAPPC per i crediti formativi maturati.&lt;br&gt;&lt;br&gt;&lt;/div&gt;&lt;div&gt;Nei prossimi giorni il consiglio nazionale provveder&agrave; a validare le singole richieste e sar&agrave; quindi possibile visionare i propri crediti direttamente sul portale.&lt;br&gt;&lt;br&gt;&lt;/div&gt;&lt;div&gt;Per visualizzare i crediti, una volta che sono stati validati, potrebbe essere necessario completare il feedback, una voce nuova, che &egrave; stata aggiunta sulle piattaforma di im@teria nel mese di marzo.&lt;br&gt;&lt;br&gt;&lt;/div&gt;&lt;div&gt;Deve entrare nella sezione Formazione di im@teria, posizionarsi ne I miei corsi, (accanto a Corsi Disponibili).&lt;/div&gt;&lt;div&gt;Trova il corso seguito con un triangolino giallo che dice &quot;Feedback mancante&quot;.&lt;br&gt;&lt;br&gt;&lt;/div&gt;&lt;div&gt;Vicino al titolo del corso trova un&#039;icona quadrata, con due freccette che guardano una verso l&#039;alto e una verso il basso, deve clikkarla.&lt;br&gt;&lt;br&gt;&lt;/div&gt;&lt;div&gt;L&#039;ultima voce &egrave; Lascia Il feedback, a quel punto salva e i crediti Le risultano immediatamente&lt;/div&gt;&lt;div&gt;Nel frattempo, le inviamo in allegato attestato riportante numero di crediti e codice&lt;/div&gt;&lt;div&gt;RingraziandoLa fin d&#039;ora e rimanendo a disposizione per qualsiasi delucidazione, porgo cordiali saluti&lt;br&gt;&lt;br&gt;&lt;/div&gt;&lt;div&gt;Dott.ssa Valentina Cucchi&lt;/div&gt;&lt;div&gt;Tel: 0545 916279&lt;/div&gt;&lt;div&gt;Fax: 0545 030139&lt;/div&gt;&lt;div&gt;Sede legale: Via Piratello n. 66/68 - 48022 Lugo (RA)&lt;/div&gt;');
define('ID_ATTESTATO_BASE', '9');
define('PERTENTUALE_ATTESTATO_BASE', '80.00');

/** CONFIGURAZIONE MOODLE DI BETAFORMAZIONE **/
define('MOODLE_DOMAIN_NAME', 'http://mdl.xxxxxxxx.com');
define('MOODLE_DB_NAME', 'dev_el_betaform');
define('MOODLE_TOKEN', '');

/** CONFIGURAZIONE SITO WORDPRESS DI BETAFORMAZIONE **/
define('WP_DOMAIN_NAME', 'http://demo.xxxxxxxx.com');
define('WP_DB_NAME', 'wp_betaform');

/** CONFIGURAZIONE RINNOVI **/
define('ID_CAMPAGNA_TELEFONATE', '1');
define('ID_CAMPAGNA_RINNOVI', '166');
define('ID_TIPO_MARKETING_RINNOVI', '49');
define('ID_CAMPAGNA_RINNOVI_AUTOMATICI', '754');
define('ID_TIPO_MARKETING_RINNOVI_AUTOMATICI', '86');

define( 'SEND_ERRORS_TO', '' ); //set email notification email address
define( 'DISPLAY_DEBUG', TRUE ); //display db errors?
require_once( BASE_ROOT.'classi/class.db.php' );

//NUOVA CONNESSIONE DATABASE VIA CLASSE
global $dblink;
$dblink = DB::getInstance();

//NUOVA CLASSE CRON
require_once( BASE_ROOT.'classi/class.cron.php' );
global $cronErp;
$cronErp = cronerp::getInstance(BASE_ROOT."libreria/automazioni/", $dblink);

define( 'COLORE_PRIMARIO', 'yellow-soft' );
define( 'COLORE_PRIMARIO_FONT', 'font-yellow-soft' );
define( 'COLORE_PRIMARIO_FONT_BACKGROUND', 'bg-yellow-soft bg-font-yellow-soft' );
define( 'COLORE_PRIMARIO_FONT_BORDER', 'border-yellow-soft' );

define('LOG_DEBUG_ALL', true ); //STAMPA AVVISO, OK ed ERRORE - False: Stampa solo ERRORE
require_once( BASE_ROOT.'classi/class.log.php' );
$log = new logerp();

define('EMAIL_DEBUG', true);
define("EMAIL_TO_SEND_DEBUG", "supporto@cemanext.it");

define('NOME_CLIENTE_TICKET', 'betaform');

?>
