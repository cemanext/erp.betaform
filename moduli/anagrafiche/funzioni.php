<?php

$anagrafiche_default = "lista_aziende";

/** FUNZIONI DI CROCCO */
function Stampa_HTML_index_Anagrafica($tabella) {
    global $table_listaAziende, $table_listaProfessionisti, $table_listaProfessioni,
    $table_listaAlbiProfessionali, $dblink;
    switch ($tabella) {

        default:
            securityLogut();
            break;

        case 'lista_aziende':
            $campi_visualizzati = $table_listaAziende['index']['campi'];
            $where = $table_listaAziende['index']['where'];
            $ordine = $table_listaAziende['index']['order'];
            $tabella = "lista_aziende";
            $titolo = 'Elenco Aziende';
            $limite = ' LIMIT 1';
            $sql_0001 = "SELECT " . $campi_visualizzati . " FROM " . $tabella . " WHERE $where $ordine $limite";
            stampa_table_datatables_ajax($sql_0001, "datatable_ajax", $titolo, '', '', false);
            break;

        case 'lista_professionisti':
            $tabella = "lista_professionisti";
            $campi_visualizzati = $table_listaProfessionisti['index']['campi'];

            $campi_visualizzati = "CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"dettaglio.php?tbl=lista_professionisti&id=',id,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',
                                        CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"modifica.php?tbl=lista_professionisti&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',
                                        CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"dettaglio_tab.php?tbl=lista_professionisti&id=',id,'\" title=\"RICHIESTA\" alt=\"RICHIESTA\"><i class=\"fa fa-book\"></i></a>') AS 'fa-book',
                                        CONCAT('<b>',`cognome`,' ',`nome`,'</b>') AS 'professionista', codice_fiscale AS 'codice fiscale', 
                                        CONCAT('Cel: ',cellulare,'<br>Tel: ', telefono) AS Telefono, email";
            $where = $table_listaProfessionisti['index']['where'];
            $ordine = $table_listaProfessionisti['index']['order'];
            $titolo = 'Elenco Professionisti';
            $limite = ' LIMIT 1';
            $sql_0001 = "SELECT " . $campi_visualizzati . " FROM " . $tabella . " WHERE $where $ordine $limite";
            stampa_table_datatables_ajax($sql_0001, "datatable_ajax", $titolo, '', '', false);
            break;

        case 'lista_professioni':
            $tabella = "lista_professioni";
            $campi_visualizzati = $table_listaProfessioni['index']['campi'];

            $where = $table_listaProfessioni['index']['where'];
            $ordine = $table_listaProfessioni['index']['order'];
            $titolo = 'Elenco Professioni';
            $limite = ' LIMIT 0,100';
            $sql_0001 = "SELECT " . $campi_visualizzati . " FROM " . $tabella . " WHERE $where $ordine $limite";
            stampa_table_datatables_responsive($sql_0001, $titolo);
            break;

        case 'lista_albi_professionali':
            $tabella = "lista_albi_professionali";
            $campi_visualizzati = $table_listaAlbiProfessionali['index']['campi'];

            $where = $table_listaAlbiProfessionali['index']['where'];
            $ordine = $table_listaAlbiProfessionali['index']['order'];
            $titolo = 'Elenco Albi Professionali';
            $limite = ' LIMIT 0,100';
            $sql_0001 = "SELECT " . $campi_visualizzati . " FROM " . $tabella . " WHERE $where $ordine $limite";
            stampa_table_datatables_responsive($sql_0001, $titolo);
            break;
        
        case 'verifica_codice_fiscale':
            $tabella = "lista_professionisti";
            
            $allProf = $dblink->get_results("SELECT * FROM $tabella WHERE LENGTH(codice_fiscale)=16 AND codice_fiscale NOT LIKE '%@%' AND campo_3!='@'");
            foreach ($allProf as $checkProf) {
                if(!controlloCodiceFiscale($checkProf['codice_fiscale'])){
                    $dblink->query("UPDATE $tabella SET campo_3 = '@' WHERE id = '".$checkProf['id']."'");
                }
            }
            
            $campi_visualizzati = $table_listaProfessionisti['index']['campi'];

            $campi_visualizzati = "CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"dettaglio.php?tbl=lista_professionisti&id=',id,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',
                                        CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"modifica.php?tbl=lista_professionisti&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',
                                        CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"dettaglio_tab.php?tbl=lista_professionisti&id=',id,'\" title=\"RICHIESTA\" alt=\"RICHIESTA\"><i class=\"fa fa-book\"></i></a>') AS 'fa-book',
                                        CONCAT('<b>',`cognome`,' ',`nome`,'</b>') AS 'professionista', codice_fiscale AS 'codice fiscale', 
                                        CONCAT('Cel: ',cellulare,'<br>Tel: ', telefono) AS Telefono, email";
            $where = " 1 AND (LENGTH(codice_fiscale)<16 OR LENGTH(codice_fiscale)>16 OR codice_fiscale LIKE '%@%' OR campo_3 = '@') ";
            $ordine = $table_listaProfessionisti['index']['order'];
            $titolo = 'Elenco Professionisti con Codice Fiscale Errato!';
            $limite = ' ';
            $sql_0001 = "SELECT " . $campi_visualizzati . " FROM " . $tabella . " WHERE $where $ordine $limite";
            
            stampa_table_datatables_responsive($sql_0001, $titolo, 'tabella_base', 'red-intense');
        break;
    }
}

function Stampa_HTML_Dettaglio_Anagrafica($tabella, $id) {
    global $dblink;

    switch ($tabella) {

        case 'lista_aziende':
            /*
              echo '<div class="row"><div class="col-md-12 col-sm-12">';
              stampa_bootstrap_form_horizontal($tabella, $id, 'Dettaglio Anagrafica Modifica');
              echo '</div></div>';
             */

            echo '<div class="row"><div class="col-md-12 col-sm-12">';

            $sql_0001 = "SELECT 
            CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"" . BASE_URL . "/moduli/anagrafiche/modifica.php?tbl=lista_aziende&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',
            IF(stato LIKE '%Elimi%',CONCAT('<span class=\"btn sbold uppercase btn-outline red-flamingo\">',stato,'</span>'),CONCAT('<span class=\"btn sbold uppercase btn-outline blue\">',stato,'</span>')) AS 'Stato',
            CONCAT('<H3>',ragione_sociale,' ',forma_giuridica,'</h3>') AS 'Ragione Sociale'
            FROM lista_aziende WHERE id = " . $id;
            stampa_table_static_basic($sql_0001, '', 'Dettaglio Azienda', '', 'fa fa-user');
            echo '</div></div>';

            echo '<div class="row">';
            echo '<div class="col-md-6 col-sm-6">';
            $sql_0002 = "SELECT CONCAT('<a href=\"modifica.php?tbl=lista_professionisti&id=',lista_professionisti.id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><button type=\"button\" class=\"btn blue btn-warning mt-ladda-btn ladda-button btn-circle btn-icon-only\"><i class=\"fa fa-edit\"></i></button></a>') AS 'fa-edit',
                    cognome, nome, email FROM lista_professionisti INNER JOIN matrice_aziende_professionisti
                    ON lista_professionisti.id = matrice_aziende_professionisti.id_professionista
                    WHERE matrice_aziende_professionisti.id_azienda = " . $id;
                    stampa_table_static_basic($sql_0002, '', 'Lista Professionisti', 'yellow');
                    echo '<a href="modifica.php?tbl=lista_aziende&id=' . 0 . '" class="btn green-meadow">
                    Aggiungi Professionista
                    <i class="fa fa-plus"></i>
                    </a>';
            echo '</div>';
            echo '<div class="col-md-6 col-sm-6">';
            $sql_0002 = "SELECT CONCAT('<a href=\"modifica.php?tbl=lista_indirizzi&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><button type=\"button\" class=\"btn blue btn-warning mt-ladda-btn ladda-button btn-circle btn-icon-only\"><i class=\"fa fa-edit\"></i></button></a>') AS 'fa-edit',
                    indirizzo, citta, provincia, "
                    . "IF(tipo='Predefinito', tipo, CONCAT('<a href=\"salva.php?tbl=lista_indirizzi&id=',id,'&id_azienda=',id_azienda,'&fn=settaPredefinito\" title=\"SETTA A PREDEFINITO\" alt=\"SETTA A PREDEFINITO\"><button type=\"button\" class=\"btn blue btn-warning mt-ladda-btn ladda-button btn-circle btn-icon-only\"><i class=\"fa fa-check\"></i></button></a>')) AS 'fa-check'"
                    . " FROM lista_indirizzi WHERE id_azienda = $id ORDER BY tipo DESC, id ASC";
                    stampa_table_static_basic($sql_0002, '', 'Lista Indirizzi', 'green');
                    echo '<a href="'.BASE_URL.'/moduli/base/modifica.php?tbl=lista_indirizzi&id=' . 0 . '" class="btn green-meadow">
                    Aggiungi Indirizzo
                    <i class="fa fa-plus"></i>
                    </a>';
            echo '</div>';
            echo '</div>';


            echo '<br><br><div class="row"><div class="col-md-12 col-sm-12">';
            $sql_0004 = "SELECT
            CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"" . BASE_URL . "/moduli/fatture/dettaglio.php?tbl=lista_fatture&id=',id,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',
            CONCAT('<a class=\"btn btn-circle btn-icon-only red btn-outline\" href=\"" . BASE_URL . "/moduli/fatture/printFatturaPDF.php?idFatt=',`id`,'\" TARGET=\"_BLANK\" title=\"STAMPA\" alt=\"STAMPA\"><i class=\"fa fa-file-pdf-o\"></i></a>') AS 'PDF',
            CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"" . BASE_URL . "/moduli/fatture/inviaFatt.php?idFatt=',id,'\" data-target=\"#ajax\" data-url=\"" . BASE_URL . "/moduli/fatture/inviafatt.php?idFatt=',id,'\" data-toggle=\"modal\" title=\"INVIA\" alt=\"INVIA\"><i class=\"fa fa-paper-plane\"></i></a>') AS 'Invia',
            sezionale,
            CONCAT('<span class=\"btn sbold uppercase btn-outline blue-chambray\">',codice_ricerca,'</span>') AS codice,
            DATE_FORMAT(DATE(data_creazione), '%d-%m-%Y') AS 'Creata', DATE_FORMAT(DATE(data_scadenza), '%d-%m-%Y') AS 'Scadenza',  
            causale, 
            CONCAT(IF(id_fatture_banche>0,(SELECT DISTINCT nome FROM lista_fatture_banche WHERE id=id_fatture_banche),''),'<br>',pagamento) AS 'pagamento',
            importo, imponibile, (SELECT CONCAT(cognome, ' ', nome) FROM lista_password WHERE id = id_agente) AS 'Commerciale', stato FROM lista_fatture WHERE id_azienda = $id ORDER BY dataagg DESC";
            //stampa_table_static_basic($sql_0004, '', 'Fatture', 'green-meadow', 'fa fa-file-text');
            stampa_table_datatables_responsive($sql_0004, 'Fatture', 'tabella_base4', 'green-meadow', 'fa fa-file-text');
            echo '</div></div>';
            break;

        case 'lista_professionisti':

            $sql_007_aggiorna_id_calendario = "UPDATE lista_fatture, lista_professionisti
            SET lista_fatture.codice_ricerca = CONCAT(lista_fatture.codice,'".SEPARATORE_FATTURA."',lista_fatture.sezionale)
            WHERE lista_professionisti.id = '" . $id . "'
            AND lista_fatture.id_professionista = lista_professionisti.id";
            $rs_007_aggiorna_id_calendario = $dblink->query($sql_007_aggiorna_id_calendario);

            echo '<div class="row"><div class="col-md-12 col-sm-12">';
            $checkRichiesta = $dblink->num_rows("SELECT id FROM calendario WHERE id_professionista = '" . $id . "' AND etichetta='Nuova Richiesta'");

            $sql_0001 = "SELECT 
            IF($checkRichiesta>0,CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"" . BASE_URL . "/moduli/anagrafiche/dettaglio_tab.php?tbl=lista_professionisti&id=',id,'\" target=\"_blank\" title=\"SCHEDA\" alt=\"SCHEDA\"><i class=\"fa fa-book\"></i></a>'), '') AS 'fa-book',
            CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"" . BASE_URL . "/moduli/anagrafiche/modifica.php?tbl=lista_professionisti&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',
            CONCAT('<span class=\"btn sbold uppercase btn-outline blue\">',codice,'</span>') AS 'Codice Cliente',
            IF(stato LIKE '%Elimi%',CONCAT('<span class=\"btn sbold uppercase btn-outline red-flamingo\">',stato,'</span>'),CONCAT('<span class=\"btn sbold uppercase btn-outline blue\">',stato,'</span>')) AS 'Stato',
            CONCAT('<H3>',cognome,'</h3>') AS Cognome, 
            CONCAT('<H3>',nome,'</h3>') AS Nome, 
            (SELECT nome FROM lista_classi WHERE id = id_classe) AS Classe, professione, attestato_classe AS 'Usa Classe per Attestato',
            email , id_moodle_user AS 'Id Moodle'
            FROM lista_professionisti WHERE id = " . $id;
            stampa_table_static_basic($sql_0001, '', 'Dettaglio Professionista', '', 'fa fa-user');
            $rowUtente = $dblink->get_row($sql_0001, true);
            echo '</div></div>';
            //echo '<li>Classe = '.$rowUtente['Classe'].'</li>';
            echo '<div class="row"><div class="col-md-12 col-sm-12">';
            $sql_0032 = "SELECT mdl_user.`id` AS 'Id Moodle', mdl_user.`username`, mdl_user.`email`, "
                    . "(FROM_UNIXTIME(mdl_user.`lastlogin`)) AS 'Ultimo Login', "
                    . "(FROM_UNIXTIME(mdl_user.`lastaccess`)) AS 'Ultimo Accesso', "
                    . "IF(mdl_user_info_data.data='', 'Abbonamento_Disattivato', mdl_user_info_data.data) AS 'Data Scadenza Abbonamento',
                    (SELECT IF(data LIKE '" . $rowUtente['Classe'] . "',CONCAT('<span class=\"btn sbold uppercase btn-outline green-jungle\">',data,'</span>'),CONCAT('<span class=\"btn sbold uppercase btn-outline red-flamingo\"><i class=\"fa fa-thumbs-o-down\"></i><h1>',data,'</h1></span>')) FROM " . MOODLE_DB_NAME . ".`mdl_user_info_data` WHERE `mdl_user_info_data`.`userid` = mdl_user.`id` AND `fieldid` = 2 ) AS CLASSE_MANUALE
                    FROM " . MOODLE_DB_NAME . ".`mdl_user` "
                    . "INNER JOIN " . MOODLE_DB_NAME . ".`mdl_user_info_data`"
                    . "ON mdl_user.id = mdl_user_info_data.userid "
                    . "WHERE mdl_user.`id` = " . $rowUtente['Id Moodle'] . " AND mdl_user_info_data.fieldid='1'";
            stampa_table_static_basic($sql_0032, '', 'Stato Utente attuale su Moodle', 'green-jungle', 'fa fa-user');
            $idUtente_per_iframe = $rowUtente['Id Moodle'];
            //echo '--> '.$rowUtente['Id Moodle'];
            //echo '--> '.$idUtente_per_iframe;
            echo '<iframe frameborder="0" border="0" width="100%" height="0px;" src="'.BASE_URL.'/libreria/automazioni/autoRecuperaCorsiUtentiMoodle_Multiplo.php?idUtente='.$idUtente_per_iframe.'"></iframe>';
            echo '<iframe frameborder="0" border="0" width="100%" height="0px;" src="'.BASE_URL.'/libreria/automazioni/autoCorsiIniziati_Multiplo.php?idUtente='.$idUtente_per_iframe.'"></iframe>';
            
            echo '</div></div>';
            echo '<div class="row"><div class="col-md-12 col-sm-12">';
            $sql_0031 = "SELECT mdl_user.`id` AS 'Id Moodle', mdl_user.`username`, mdl_user.`email`, "
                    . "DATE_FORMAT(FROM_UNIXTIME(mdl_user.`lastlogin`),'%d-%m-%Y') AS 'Ultimo Login', "
                    . "DATE_FORMAT(FROM_UNIXTIME(mdl_user.`lastaccess`),'%d-%m-%Y') AS 'Ultimo Accesso', "
                    . "IF(mdl_user_info_data.data='', '--Abbonamento Disattivato', mdl_user_info_data.data) AS 'Scadenza Abbonamento', 
                    (SELECT IF(data LIKE '" . $rowUtente['Classe'] . "',CONCAT('<span class=\"btn sbold uppercase btn-outline green-jungle\">',data,'</span>'),CONCAT('<span class=\"btn sbold uppercase btn-outline red-flamingo\"><i class=\"fa fa-thumbs-o-down\"></i><h1>',data,'</h1></span>')) FROM " . str_replace("_dev", "", MOODLE_DB_NAME) . "_dev_test.`mdl_user_info_data` WHERE `mdl_user_info_data`.`userid` = mdl_user.`id` AND `fieldid` = 2 ) AS CLASSE_MANUALE
                    FROM " . str_replace("_dev", "", MOODLE_DB_NAME) . "_dev_test.`mdl_user` "
                    . "INNER JOIN " . str_replace("_dev", "", MOODLE_DB_NAME) . "_dev_test.`mdl_user_info_data`"
                    . "ON mdl_user.id = mdl_user_info_data.userid "
                    . "WHERE mdl_user.`id` = " . $rowUtente['Id Moodle'] . " AND mdl_user_info_data.fieldid=1";
            stampa_table_static_basic($sql_0031, '', 'Stato Utente Su Leolearning al 31/07/2017', 'red-flamingo', 'fa fa-user');
            echo '</div></div>';

            echo '<div class="row"><div class="col-md-12 col-sm-12">';
            $sql_0007 = "SELECT dataagg, username, email, passwd, DATE(data_creazione) AS Creazione, DATE(data_scadenza) AS Scadenza, (SELECT nome FROM lista_classi WHERE id = id_classe ) AS Classe, 
            IF(stato LIKE '%Elimi%',CONCAT('<span class=\"btn sbold uppercase btn-outline red-flamingo\">',stato,'</span>'),CONCAT('<span class=\"btn sbold uppercase btn-outline blue\">',stato,'</span>')) AS 'Stato',
            CONCAT('<a class=\"btn btn-circle btn-icon-only purple-studio btn-outline\" href=\"salva.php?fn=resettaPassword&tbl=lista_password&id=',id_professionista,'\" onclick=\"javascript: return confirm(\'Si è sicuri di eseguire il reset della password ?\')\" title=\"RESETTA PASSWORD\" alt=\"RESETTA PASSWORD\"><i class=\"fa fa-key\"></i></a>') AS 'fa-key' 
            FROM lista_password WHERE id_professionista = '" . $id . "' ORDER BY dataagg DESC";
            stampa_table_static_basic($sql_0007, '', 'Credenziali Password', '', 'fa fa-mail');
            echo '</div></div>';

            echo '<div class="row"><div class="col-md-12 col-sm-12">';
            $sql_0002 = "SELECT
            CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"dettaglio_tab.php?tbl=lista_professionisti&id=',id_professionista,'\" title=\"RICHIESTA\" alt=\"RICHIESTA\"><i class=\"fa fa-book\"></i></a>') AS 'fa-book',
            etichetta, oggetto, DATE(datainsert) AS 'Data', TIME(orainsert) AS 'Ora', stato FROM calendario WHERE id_professionista = '" . $id . "' AND etichetta='Nuova Richiesta'";
            //stampa_table_static_basic($sql_0002, '', 'Richieste', 'blue', 'fa fa-envelope-o');
            stampa_table_datatables_responsive($sql_0002, 'Richieste', 'tabella_base2', 'blue', 'fa fa-envelope-o');
            echo '</div></div>';


            echo '<div class="row"><div class="col-md-12 col-sm-12">';
            $sql_0003 = "SELECT
            CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"" . BASE_URL . "/moduli/preventivi/dettaglio.php?tbl=lista_preventivi&id=',id,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',
            CONCAT('<a class=\"btn btn-circle btn-icon-only red btn-outline\" href=\"" . BASE_URL . "/moduli/preventivi/printPreventivoPDF.php?id=',`id`,'&idA=',id_area,'\" TARGET=\"_BLANK\" title=\"STAMPA\" alt=\"STAMPA\"><i class=\"fa fa-file-pdf-o\"></i></a>') AS 'PDF',
            CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"" . BASE_URL . "/moduli/preventivi/inviaPrev.php?idPrev=',id,'\" data-target=\"#ajax\" data-url=\"inviaPrev.php?idPrev=',id,'\" data-toggle=\"modal\" title=\"INVIA\" alt=\"INVIA\"><i class=\"fa fa-paper-plane\"></i></a>') AS 'Invia',
            DATE(data_creazione) AS 'Data', codice, imponibile, cognome_nome_agente AS 'Commerciale', stato  FROM lista_preventivi WHERE id_professionista = $id ORDER BY dataagg DESC";
            //stampa_table_static_basic($sql_0003, '', 'Ordini', 'blue', 'fa fa-edit');
            stampa_table_datatables_responsive($sql_0003, 'Ordini', 'tabella_base3', 'blue', 'fa fa-edit');

            //AGGIUNGI NUOVO PREVENTIVO
            if($_SESSION['livello_utente'] == "betaadmin" || $_SESSION['livello_utente'] == "amministratore"){
                echo '<CENTER><a href="' . BASE_URL . '/moduli/preventivi/salva.php?tbl=lista_preventivi&idProfessionista=' . $id . '&fn=nuovoPreventivoProfessionista" class="btn green-meadow"><i class="fa fa-plus"></i>  NUOVO ORDINE</a></CENTER><br><br>';
            }
            
            echo '</div></div>';
            echo '<div class="row"><div class="col-md-12 col-sm-12">';
            $sql_0004 = "SELECT
            CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"" . BASE_URL . "/moduli/fatture/dettaglio.php?tbl=lista_fatture&id=',id,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',
            CONCAT('<a class=\"btn btn-circle btn-icon-only red btn-outline\" href=\"" . BASE_URL . "/moduli/fatture/printFatturaPDF.php?idFatt=',`id`,'\" TARGET=\"_BLANK\" title=\"STAMPA\" alt=\"STAMPA\"><i class=\"fa fa-file-pdf-o\"></i></a>') AS 'PDF',
            CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"" . BASE_URL . "/moduli/fatture/inviaFatt.php?idFatt=',id,'\" data-target=\"#ajax\" data-url=\"" . BASE_URL . "/moduli/fatture/inviafatt.php?idFatt=',id,'\" data-toggle=\"modal\" title=\"INVIA\" alt=\"INVIA\"><i class=\"fa fa-paper-plane\"></i></a>') AS 'Invia', 
IF(tipo LIKE 'Fattura',CONCAT('<span class=\"btn sbold uppercase btn-outline blue\">',tipo,'</span>'),CONCAT('<span class=\"btn sbold uppercase btn-outline red-thunderbird\">',tipo,'</span>')) AS 'Tipo',			
			CONCAT('<span class=\"btn sbold uppercase btn-outline blue-chambray\">',codice_ricerca,'</span>') AS 'Codice', DATE(data_creazione) AS 'Creata', DATE_FORMAT(DATE(data_scadenza), '%d-%m-%Y') AS 'Scadenza', imponibile, 
		(SELECT CONCAT(cognome, ' ', nome) FROM lista_password WHERE id = id_agente) AS 'Commerciale', stato FROM lista_fatture WHERE id_professionista = $id ORDER BY dataagg DESC";
            //stampa_table_static_basic($sql_0004, '', 'Fatture', 'green-meadow', 'fa fa-file-text');
            stampa_table_datatables_responsive($sql_0004, 'Fatture', 'tabella_base4', 'green-meadow', 'fa fa-file-text');
            
            //AGGIUNGI NUOVO PREVENTIVO
            if($_SESSION['livello_utente'] == "betaadmin" || $_SESSION['livello_utente'] == "amministratore"){
                echo '<CENTER><a href="' . BASE_URL . '/moduli/fatture/salva.php?tbl=lista_fatture&idProfessionista=' . $id . '&fn=nuovaFatturaProfessionista" class="btn green-meadow"><i class="fa fa-plus"></i>  NUOVA FATTURA</a></CENTER><br><br>';
            }
            
            echo '</div></div>';
            echo '<div class="row"><div class="col-md-12 col-sm-12">';
            /* (SELECT CONCAT(cognome, ' ', nome) FROM lista_professionisti WHERE id = id_professionista) AS 'Professionista', */
            $sql_0005 = "SELECT
            CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"" . BASE_URL . "/moduli/iscrizioni/dettaglio.php?tbl=lista_iscrizioni_partecipanti&id=',id,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',
            CONCAT('<a class=\"btn btn-circle btn-icon-only red btn-outline\" href=\"" . BASE_URL . "/moduli/corsi/printAttestatoPDF.php?idIscrizione=',`id`,'\" TARGET=\"_BLANK\" title=\"STAMPA\" alt=\"STAMPA\"><i class=\"fa fa-file-pdf-o\"></i></a>') AS 'PDF',
            CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"" . BASE_URL . "/moduli/iscrizioni/inviaAttestato.php?idIscrizione=',id,'\" data-target=\"#ajax\" data-url=\"" . BASE_URL . "/moduli/fatture/inviafatt.php?idFatt=',id,'\" data-toggle=\"modal\" title=\"INVIA\" alt=\"INVIA\"><i class=\"fa fa-paper-plane\"></i></a>') AS 'Invia', 
            (SELECT nome_prodotto FROM lista_corsi WHERE id = id_corso) AS 'Corso',
            (SELECT nome FROM lista_classi WHERE id = id_classe) AS 'Classe',
            data_inizio_iscrizione, data_fine_iscrizione,
            DATE(data_inizio) AS 'Data Inizio', 
            IF(data_completamento LIKE '000%',DATE(data_fine), DATE(data_completamento)) AS 'Data Fine', 
            stato, avanzamento_completamento AS 'Perc.',
            CONCAT('<a class=\"btn btn-circle btn-icon-only red btn-outline\" onclick=\"javascript: return confirm(\'Sei sicuro di rigenerare questo attestato ?\');\" href=\"" . BASE_URL . "/moduli/corsi/printAttestatoPDF.php?idIscrizione=',`id`,'&force=1\" TARGET=\"_BLANK\" title=\"FORZA RIGENERA\" alt=\"FORZA RIGENERA\"><i class=\"fa fa-repeat\"></i></a>') AS 'RIGENERA PDF',
            id AS 'selezione'
            FROM lista_iscrizioni WHERE id_professionista = '$id' ORDER BY dataagg DESC";
            //stampa_table_static_basic($sql_0005, '', 'Iscrizioni Corsi', 'green-meadow', 'fa fa-university');
            stampa_table_datatables_responsive($sql_0005, 'Iscrizioni Corsi', 'tabella_base5', 'green-meadow', 'fa fa-university');
            echo '</div>'
                .'<div style="text-align: center; margin-bottom: 15px;"> 
                    <button id="associaProfessionista" type="button" class="btn btn-icon purple-studio" alt="CAMBIA PROFESSIONISTA" title="CAMBIA PROFESSIONISTA"><i class="fa fa-sign-in"></i> Cambia Proprietario del Corso</a></button>
                </div>'
            . '</div>';

            echo '<div class="row"><div class="col-md-12 col-sm-12">';
            $sql_0006 = "SELECT
            CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"dettaglio_tab.php?tbl=lista_professionisti&id=',id_professionista,'\" title=\"RICHIESTA\" alt=\"RICHIESTA\"><i class=\"fa fa-book\"></i></a>') AS 'fa-book',
            etichetta, oggetto, REPLACE(messaggio,'\\n','<br>') AS 'Messaggio', DATE(datainsert) AS 'Data', TIME(orainsert) AS 'Ora', stato 
            FROM calendario WHERE id_professionista = '" . $id . "' AND etichetta!='Nuova Richiesta Accorpata'";
            //stampa_table_static_basic($sql_0006, '', 'Storico', 'grey-mint', 'fa fa-history');
            stampa_table_datatables_responsive($sql_0006, 'Storico Richieste', 'tabella_base6', 'grey-mint', 'fa fa-history');
            echo '</div></div>';

            echo '<div class="row"><div class="col-md-12 col-sm-12">';
            $sql_0007 = "SELECT
            CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"modifica.php?tbl=lista_indirizzi_email&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit', 
            CONCAT('<a class=\"btn btn-circle btn-icon-only red-intense btn-outline\" href=\"cancella.php?tbl=lista_indirizzi_email&id=',id,'\" title=\"CANCELLA\" alt=\"CANCELLA\"><i class=\"fa fa-trash\"></i></a>') AS 'fa-trash', 
            dataagg, stato, email
            FROM lista_indirizzi_email WHERE id_professionista = '" . $id."' ORDER BY dataagg DESC";
            stampa_table_static_basic($sql_0007, '', 'Indirizzi Email', '', 'fa fa-envelope');
            echo '</div></div>';

            echo '<div class="row"><div class="col-md-12 col-sm-12">';
            $sql_00071 = "SELECT
            CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"modifica.php?tbl=lista_numeri_telefono&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit', 
            CONCAT('<a class=\"btn btn-circle btn-icon-only red-intense btn-outline\" href=\"cancella.php?tbl=lista_numeri_telefono&id=',id,'\" title=\"CANCELLA\" alt=\"CANCELLA\"><i class=\"fa fa-trash\"></i></a>') AS 'fa-trash', 
            dataagg, stato, telefono
            FROM lista_numeri_telefono WHERE id_professionista = '" . $id."' ORDER BY dataagg DESC";
            stampa_table_static_basic($sql_00071, '', 'Numeri di Telefono', '', 'fa fa-phone');
            echo '</div></div>';
            
			/*echo '<div class="row"><div class="col-md-12 col-sm-12">';
            $sql_0007 = "SELECT CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"modifica.php?tbl=lista_numeri_telefono&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit', 
            dataagg, stato, telefono
            FROM lista_numeri_telefono WHERE id_professionista = '" . $id."' ORDER BY dataagg DESC";
            stampa_table_static_basic($sql_0007, '', 'Numeri Telefono', '', 'fa fa-tel');
            echo '</div></div>';*/
            
            echo '<div class="row"><div class="col-md-12 col-sm-12">';
            /*
              `id`, `dataagg`, `scrittore`, `stato`, `cognome`, `nome`, `azienda`, `email`, `numero_ordine`,
              `data_ordine`, `stato_ordine`, `commerciale`, `sezionale`, `tipo_marketing`,
              `classe`, `id_classe`, `prodotto`, `id_prodotto`, `codice_prodotto`, `ordine_omaggio`,
              `id_utente_moodle`, `data_creazione`, `data_scadenza`, `note`, `password`, `id_corso_moodle`, `id_preventivo`, `id_fattura`, `id_calendario`, `nome_file`


              SELECT * FROM `lista_attivazioni_manuale` WHERE 1
             */
            $sql_0007 = "SELECT 
            `dataagg`, `scrittore`, `stato`, `email`, `numero_ordine`,  `data_ordine`,`commerciale`, `tipo_marketing`,  `classe`, `prodotto`, `nome_file`
            FROM lista_attivazioni_manuale 
            WHERE id_utente_moodle>0 AND (id_utente_moodle = '" . $rowUtente['Id Moodle'] . "'  OR email = '" . $rowUtente['email'] . "')ORDER BY dataagg DESC";
            stampa_table_static_basic($sql_0007, '', 'Attivazioni Manuali Tramite Excel', 'green-steel', 'fa fa-file-excel-o');
            echo '</div></div>';
            break;

        default:
            $campi_visualizzati = "";
            $campi     = 	$dblink->list_fields("SELECT * FROM ".$tabella."");
            foreach ($campi as $nome_colonna) {
                 $campi_visualizzati.= "`".$nome_colonna->name."`, ";
            }
            $campi_visualizzati = substr($campi_visualizzati, 0, -2);
            stampa_table_static_basic("SELECT $campi_visualizzati FROM " . $tabella . " WHERE id = '" . $id . "'", '', 'Dettaglio', '');
            break;
    }
    return;
}

?>
