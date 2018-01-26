<?php
include_once('../../config/connDB.php');
include_once(BASE_ROOT . 'config/confAccesso.php');

if (DISPLAY_DEBUG) {
    echo "START: ".date("H:i:s");
    echo "<br>";
}

/* AGGIORNAMENTI LISTA COSTI */

// AGGIORNO COGNOME E NOME PROFESSIONISTA
$sql_0001 = "UPDATE lista_costi, lista_professionisti SET "
        . "lista_costi.cognome_nome_professionista = CONCAT(lista_professionisti.cognome,' ',lista_professionisti.nome) "
        . "WHERE lista_costi.id_professionista = lista_professionisti.id "
        . "AND lista_costi.cognome_nome_professionista = '' ";
$dblink->query($sql_0001);

$sql_0021 = "UPDATE lista_professionisti SET "
        . " codice_fiscale = UPPER(codice_fiscale) "
        . " WHERE 1 ";
$dblink->query($sql_0021);

// AGGIORNO RAGIONE SOCIALE AZIENDA
$sql_0002 = "UPDATE lista_costi, lista_aziende SET "
        . "lista_costi.ragione_sociale_azienda = CONCAT(lista_aziende.ragione_sociale,' ',lista_aziende.forma_giuridica) "
        . "WHERE lista_costi.id_azienda = lista_aziende.id "
        . "AND lista_costi.ragione_sociale_azienda = '' ";
$dblink->query($sql_0002);

$sql_0022 = "UPDATE lista_aziende SET "
        . " codice_fiscale = UPPER(codice_fiscale) "
        . " WHERE 1 ";
$dblink->query($sql_0022);

/* AGGIORNAMENTI LISTA PASSWORD */

// AGGIORNO ID CLASSE
/*
$sql_0003 = "UPDATE lista_password, lista_professionisti SET "
        . "lista_password.id_classe = lista_professionisti.id_classe "
        . "WHERE lista_password.id_professionista = lista_professionisti.id ";
$dblink->query($sql_0003);
*/

// AGGIORNO ID CLASSE
$sql_0004 = "UPDATE lista_professionisti, lista_password SET "
        . "lista_professionisti.id_moodle_user = lista_password.id_moodle_user "
        . "WHERE lista_password.id_professionista = lista_professionisti.id 
        AND lista_professionisti.id_moodle_user<=0";
$dblink->query($sql_0004);

// AGGIORNO ID UTENTE MOODLE
$sql_0005 = "UPDATE lista_iscrizioni, lista_password 
            SET lista_iscrizioni.id_utente_moodle = lista_password.id_moodle_user 
            WHERE lista_password.id_professionista = lista_iscrizioni.id_professionista 
            AND lista_iscrizioni.id_utente_moodle<=0";
$dblink->query($sql_0005);

// AGGIORNO CODICE CLIENTE PROFESSIONISTA
$sql_0006 = "UPDATE lista_professionisti
            SET codice = CONCAT('".SUFFISSO_CODICE_CLIENTE."',RIGHT(concat('0000000000',id),6)) 
            WHERE codice NOT LIKE '".SUFFISSO_CODICE_CLIENTE."'";
$dblink->query($sql_0006);

//AGGIORNO DATI DI RICERCA IN FATTURE
$sql_0007 = "UPDATE lista_fatture
            SET codice_ricerca = CONCAT(codice,'/',sezionale) 
            WHERE codice_ricerca = '' AND tipo='Fattura' AND codice > 0";
$dblink->query($sql_0007);

$sql_0008 = "UPDATE lista_fatture
            SET cognome_nome_agente = (SELECT CONCAT(lista_password.cognome,' ', lista_password.nome) FROM lista_password WHERE lista_password.id = lista_fatture.id_agente) 
            WHERE cognome_nome_agente = '' AND tipo='Fattura' AND id_agente > 0";
$dblink->query($sql_0008);

$sql_0009 = "UPDATE lista_fatture
            SET cognome_nome_professionista = (SELECT CONCAT(lista_professionisti.cognome,' ', lista_professionisti.nome) FROM lista_professionisti WHERE lista_professionisti.id = lista_fatture.id_professionista) 
            WHERE cognome_nome_professionista = '' AND tipo='Fattura' AND id_professionista > 0";
$dblink->query($sql_0009);

$sql_0010 = "UPDATE lista_fatture
            SET ragione_sociale_azienda = (SELECT CONCAT(lista_aziende.ragione_sociale,' ', lista_aziende.forma_giuridica) FROM lista_aziende WHERE lista_aziende.id = lista_fatture.id_azienda) 
            WHERE ragione_sociale_azienda = '' AND tipo='Fattura' AND id_azienda > 0";
$dblink->query($sql_0010);

$sql_0011 = "UPDATE lista_fatture
            SET nome_campagna = (SELECT lista_campagne.nome FROM lista_campagne WHERE lista_campagne.id = lista_fatture.id_campagna) 
            WHERE nome_campagna = '' AND tipo='Fattura' AND id_campagna > 0";
$dblink->query($sql_0011);

$sql_0012 = "UPDATE lista_fatture
            SET banca_pagamento = (SELECT lista_fatture_banche.nome FROM lista_fatture_banche WHERE lista_fatture_banche.id = lista_fatture.id_fatture_banche) 
            WHERE banca_pagamento = '' AND tipo='Fattura' AND id_fatture_banche > 0";
$dblink->query($sql_0012);

$sql_0018 = "UPDATE lista_fatture
            SET data_preventivo = (SELECT lista_preventivi.data_firma FROM lista_preventivi WHERE lista_preventivi.id = lista_fatture.id_preventivo) 
            WHERE data_preventivo = '0000-00-00' AND tipo='Fattura' AND id_preventivo > 0";
$dblink->query($sql_0018);

//AGGIORNO DATI DI RICERCA IN PREVENTIVI
$sql_0013 = "UPDATE lista_preventivi
            SET codice_ricerca = CONCAT(codice,'/',sezionale) 
            WHERE codice_ricerca = '' AND codice > 0";
$dblink->query($sql_0013);

$sql_0014 = "UPDATE lista_preventivi
            SET cognome_nome_agente = (SELECT CONCAT(lista_password.cognome,' ', lista_password.nome) FROM lista_password WHERE lista_password.id = lista_preventivi.id_agente) 
            WHERE (cognome_nome_agente = '' OR cognome_nome_agente = 'In Attesa di Assegnazione') AND id_agente > 0";
$dblink->query($sql_0014);

$sql_0015 = "UPDATE lista_preventivi
            SET cognome_nome_professionista = (SELECT CONCAT(lista_professionisti.cognome,' ', lista_professionisti.nome) FROM lista_professionisti WHERE lista_professionisti.id = lista_preventivi.id_professionista) 
            WHERE cognome_nome_professionista = '' AND id_professionista > 0";
$dblink->query($sql_0015);

$sql_0016 = "UPDATE lista_preventivi
            SET ragione_sociale_azienda = (SELECT CONCAT(lista_aziende.ragione_sociale,' ', lista_aziende.forma_giuridica) FROM lista_aziende WHERE lista_aziende.id = lista_preventivi.id_azienda) 
            WHERE ragione_sociale_azienda = '' AND id_azienda > 0";
$dblink->query($sql_0016);

$sql_0017 = "UPDATE lista_preventivi
            SET nome_campagna = (SELECT lista_campagne.nome FROM lista_campagne WHERE lista_campagne.id = lista_preventivi.id_campagna) 
            WHERE nome_campagna = '' AND id_campagna > 0";
$dblink->query($sql_0017);

//AGGIORNAMENTO CAMPAGNE
$sql_0019 = "UPDATE lista_campagne
            SET stato = 'Attiva'
            WHERE (data_inizio <= CURDATE() AND data_fine >= CURDATE()) AND data_inizio != '0000-00-00' ";
$dblink->query($sql_0019);

$sql_0020 = "UPDATE lista_campagne
            SET stato = 'Terminata'
            WHERE data_fine < CURDATE() AND data_fine != '0000-00-00' ";
$dblink->query($sql_0020);

if (DISPLAY_DEBUG) echo "END: ".date("H:i:s");

?>
