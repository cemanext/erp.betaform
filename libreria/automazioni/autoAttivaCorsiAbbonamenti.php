<?php
include_once('../../config/connDB.php');
include_once(BASE_ROOT . 'config/confAccesso.php');
include_once(BASE_ROOT . 'classi/webservice/client.php');

$moodle = new moodleWebService();

if (DISPLAY_DEBUG) {
    echo '<hr>'.date("H:i:s");
    echo '<li>DB_HOST = '.DB_HOST.'</li>';
    echo '<li>DB_USER = '.DB_USER.'</li>';
    echo '<li>DB_PASS = '.DB_PASS.'</li>';
    echo '<li>DB_NAME = '.DB_NAME.'</li>';
    echo '<li>DB_NAME = '.MOODLE_DB_NAME.'</li>';
    echo '<li>DB_NAME = '.DURATA_CORSO_INGEGNERI.'</li>';
    echo '<li>DB_NAME = '.DURATA_ABBONAMENTO.'</li>';
    echo '<li>DB_NAME = '.DURATA_CORSO.'</li>';
    echo '<hr>';
}

$sql_0001 = "CREATE TEMPORARY TABLE corsi(SELECT DISTINCT lista_fatture_dettaglio.id_professionista AS idProfessionista,
        lista_fatture_dettaglio.id_fattura AS 'idFattura',
        lista_fatture_dettaglio.id AS 'idFatturaDettaglio',
        lista_corsi.id AS 'idCorso',
        lista_fatture_dettaglio.id_prodotto AS 'idProdotto',
        id_corso_moodle AS 'idCorsoMoodle',
        id_moodle_user AS 'idUtenteMoodle',
        'SINGOLO CORSO' AS tipo_attivazione,
        lista_password.id_classe AS 'idClasseMoodle',
        IF(id_moodle_user<=0,'NON ATTIVARE', 'ATTIVARE') AS 'controllo',
        CONVERT(lista_corsi.nome_prodotto USING utf8) AS 'Prodotto'
        FROM lista_corsi INNER JOIN lista_fatture_dettaglio
        ON lista_corsi.id_prodotto = lista_fatture_dettaglio.id_prodotto 
        INNER JOIN lista_password ON lista_password.id_professionista = lista_fatture_dettaglio.id_professionista
        WHERE lista_fatture_dettaglio.id_professionista>0
        AND lista_password.id_moodle_user>0
        AND lista_corsi.id_corso_moodle>0
        AND lista_fatture_dettaglio.id NOT IN (SELECT DISTINCT id_fattura_dettaglio FROM lista_iscrizioni WHERE 1));";
$rs_0001 = $dblink->query($sql_0001);

$sql_0002 = "CREATE TEMPORARY TABLE abbonamenti(SELECT DISTINCT lista_fatture_dettaglio.id_professionista  AS idProfessionista,
        lista_fatture_dettaglio.id_fattura AS 'idFattura',
        lista_fatture_dettaglio.id AS 'idFatturaDettaglio',
        '' AS 'idCorso',
        lista_fatture_dettaglio.id_prodotto AS 'idProdotto',
        '' AS 'idCorsoMoodle',
        id_moodle_user AS 'idUtenteMoodle',
        'ABBONAMENTO' AS tipo_attivazione,
        lista_password.id_classe AS 'idClasseMoodle',
        IF(lista_password.id_classe<=0,'NON ATTIVARE','ATTIVARE') AS 'controllo',
        CONVERT(lista_prodotti.nome USING utf8) AS 'Prodotto'
        FROM lista_fatture_dettaglio INNER JOIN lista_prodotti ON lista_fatture_dettaglio.id_prodotto = lista_prodotti.id 
        INNER JOIN lista_password ON lista_password.id_professionista = lista_fatture_dettaglio.id_professionista
        WHERE  lista_fatture_dettaglio.id_professionista>0
        AND lista_password.id_moodle_user>0
        AND  lista_prodotti.gruppo LIKE 'ABBONAMENTO'
        AND lista_fatture_dettaglio.id NOT IN (SELECT DISTINCT id_fattura_dettaglio FROM lista_iscrizioni WHERE 1));";
$rs_0002 = $dblink->query($sql_0002);

$sql_0002bis = "CREATE TEMPORARY TABLE pacchetto (SELECT DISTINCT lista_fatture_dettaglio.id_professionista,
            lista_fatture_dettaglio.id_fattura AS 'idFattura',
            lista_fatture_dettaglio.id AS 'idFatturaDettaglio',
            lista_corsi.id AS 'idCorso',
            lista_fatture_dettaglio.id_prodotto AS 'idProdotto',
            id_corso_moodle AS 'idCorsoMoodle',
            id_moodle_user AS 'idUtenteMoodle',
            'PACCHETTO' AS tipo_attivazione,
            lista_password.id_classe AS 'idClasseMoodle',
            IF(lista_password.id_moodle_user<=0,'NON ATTIVARE','ATTIVARE') AS 'controllo',

            CONVERT(lista_corsi.nome_prodotto USING utf8) AS 'Prodotto'
            FROM lista_fatture_dettaglio LEFT JOIN lista_prodotti
            ON lista_prodotti.id = lista_fatture_dettaglio.id_prodotto
            INNER JOIN lista_prodotti_dettaglio
            ON lista_prodotti_dettaglio.id_prodotto_0 = lista_fatture_dettaglio.id_prodotto
            INNER JOIN lista_corsi ON lista_corsi.id_prodotto = lista_prodotti_dettaglio.id_prodotto
            INNER JOIN lista_password ON lista_password.id_professionista = lista_fatture_dettaglio.id_professionista
            WHERE lista_fatture_dettaglio.id_professionista>0
            AND lista_password.id_moodle_user>0
            AND lista_corsi.id_corso_moodle>0
            AND lista_prodotti.gruppo LIKE 'PACCHETTO'
            AND NOT EXISTS (SELECT DISTINCT id_fattura_dettaglio FROM lista_iscrizioni WHERE 1 AND lista_fatture_dettaglio.id=lista_iscrizioni.id_fattura_dettaglio AND lista_iscrizioni.id_corso = lista_corsi.id ));";
$rs_0002bis = $dblink->query($sql_0002bis);

$sql_0003 = "CREATE TEMPORARY TABLE attivazioniIscrizioni SELECT * FROM corsi
        UNION 
        SELECT *  FROM abbonamenti
        UNION 
        SELECT *  FROM pacchetto;";
$rs_0003 = $dblink->query($sql_0003);

$sql_00000000 = "SELECT DISTINCT idFattura, idFatturaDettaglio, idCorso, idProdotto, idCorsoMoodle, idUtenteMoodle, tipo_attivazione, idClasseMoodle,

        (SELECT DISTINCT CONCAT(cognome, ' ', nome) FROM lista_professionisti WHERE id = id_professionista) as Professionista, Prodotto, controllo 
        FROM attivazioniIscrizioni WHERE 1 ORDER BY idFattura DESC";

$sql_00000000 = "SELECT DISTINCT 
        (SELECT DISTINCT CONCAT('<h3><b>',cognome, ' ', nome,'</b></h3>') FROM lista_professionisti WHERE id = id_professionista) as Professionista, 
        tipo_attivazione AS 'Tipo'
        CONCAT('<h3>',Prodotto,'</h3>') AS Prodotto,
        (SELECT DISTINCT nome FROM lista_classi WHERE id = idClasseMoodle) AS 'Classe',
        controllo
        FROM attivazioniIscrizioni WHERE 1 ORDER BY idFattura DESC";

//IF(LCASE(Prodotto) LIKE 'abbonamento%','ABBONAMENTO','SINGOLO CORSO') AS 'Tipo',

$sql_00000000 = "SELECT DISTINCT *,
        tipo_attivazione AS 'Tipo'
        FROM attivazioniIscrizioni WHERE 1 ORDER BY idFattura ASC LIMIT 10";

//IF(LCASE(Prodotto) LIKE 'abbonamento%','ABBONAMENTO','SINGOLO CORSO') AS 'Tipo'

if (DISPLAY_DEBUG) stampa_table_datatables_responsive($sql_00000000, $titolo, 'tabella_base');

$rs_00000000 = $dblink->get_results($sql_00000000);
if (DISPLAY_DEBUG) echo '<ol>';
foreach ($rs_00000000 AS $row_00000000) {
    //echo '<lI>'.$row_00000000['Professionista'].' ----> '.$row_00000000['Tipo'].' ----> '.$row_00000000['controllo'].' strlen(bottone)='.strlen($row_00000000['controllo']).'</li>';
    $idProfessionista = $row_00000000['idProfessionista'];
    $idFattura = $row_00000000['idFattura'];
    $idFatturaDettaglio = $row_00000000['idFatturaDettaglio'];
    $idCorso = $row_00000000['idCorso'];
    $idProdotto = $row_00000000['idProdotto'];
    $idUtenteMoodle = $row_00000000['idUtenteMoodle'];
    $idCorsoMoodle = $row_00000000['idCorsoMoodle'];


    if ($row_00000000['controllo'] == 'ATTIVARE') {
        if ($row_00000000['Tipo'] == 'SINGOLO CORSO') {
            $ok = attivaCorsoFattura($idProfessionista, $idFattura, $idFatturaDettaglio, $idCorso, $idUtenteMoodle, $idCorsoMoodle);
            if ($ok) {
                if (DISPLAY_DEBUG) echo '<li style="color: green;"> attivaCorsoFattura --> OK !</li>';
                $log->log_all_errors('attivaCorsoFattura -> Corso Attivato Correttamente [idCorsoMoodle = ' . $idCorsoMoodle . ']', 'OK');
            } else {
                if (DISPLAY_DEBUG) echo '<li style="color: RED;"> attivaCorsoFattura --> KO !</li>';
                $log->log_all_errors('attivaCorsoFattura -> Corso NON Attivato [idCorsoMoodle = ' . $idCorsoMoodle . ']', 'ERRORE');
            }
        } elseif ($row_00000000['Tipo'] == 'ABBONAMENTO') {
            $ok = attivaAbbonamentoFattura($idProfessionista, $idFattura, $idFatturaDettaglio, $idUtenteMoodle);
            if ($ok) {
                if (DISPLAY_DEBUG) echo '<li style="color: green;"> attivaAbbonamentoFattura --> OK !</li>';
                $log->log_all_errors('attivaAbbonamentoFattura -> Abbonamento Attivato Correttamente', 'OK');
            } else {
                if (DISPLAY_DEBUG) echo '<li style="color: RED;"> attivaAbbonamentoFattura --> KO !</li>';
                $log->log_all_errors('attivaAbbonamentoFattura -> Abbonamento NON Attivato', 'ERRORE');
            }
        } elseif ($row_00000000['Tipo'] == 'PACCHETTO') {
            $ok = attivaPacchettoFattura($idProfessionista, $idFattura, $idFatturaDettaglio, $idProdotto, $idCorso, $idUtenteMoodle, $idCorsoMoodle);
            if ($ok) {
                if (DISPLAY_DEBUG) echo '<li style="color: green;"> attivaAbbonamentoFattura --> OK !</li>';
                $log->log_all_errors('attivaAbbonamentoFattura -> Abbonamento Attivato Correttamente', 'OK');
            } else {
                if (DISPLAY_DEBUG) echo '<li style="color: RED;"> attivaAbbonamentoFattura --> KO !</li>';
                $log->log_all_errors('attivaAbbonamentoFattura -> Abbonamento NON Attivato', 'ERRORE');
            }
        }
        
    } else {
        
    }
    sleep(5);
}
if (DISPLAY_DEBUG) echo '</ol>';
if (DISPLAY_DEBUG) echo date("H:i:s");
?>