<?php

/** FUNZIONI DI CROCCO */
function Stampa_HTML_index_Preventivi($tabella) {
    global $table_listaPreventivi, $where_lista_preventivi;

    $tabella = 'lista_preventivi';
    switch ($tabella) {

        case 'lista_preventivi':
            $tabella = "lista_preventivi";
            if (isset($_GET['whr_state'])) {
                $whr_state = $_GET['whr_state'];
                $where = " (MD5(stato)=('" . $whr_state . "'))" . $where_lista_preventivi;
            } else {
                $whr_state = "";
                $where = $table_listaPreventivi['index']['where'];
            }

            $campi_visualizzati = $table_listaPreventivi['index']['campi'];

            if ($whr_state == "8b7bbcc20d4857c20045195274f7d0dc") { //In Attesa
                $titolo = 'Elenco Ordini / Preventivi / Offerte - In Attesa';
                $campi_visualizzati = str_replace("data_iscrizione,", "dataagg AS data_ultima_modifica,", $campi_visualizzati);
                $colore = 'yellow-saffron';
            } else if ($whr_state == "0dcf93d17feb1a4f6efe62d5d2f270b2") { //Venduto
                $titolo = 'Elenco Ordini / Preventivi / Offerte - Venduti';
                $colore = 'blue-steel';
            } else if ($whr_state == "31aa0b940088855f8a9b72946dc495ab") { //Negativi
                $titolo = 'Elenco Ordini / Preventivi / Offerte - Negativi';
                $campi_visualizzati = str_replace("data_iscrizione,", "data_iscrizione AS data_negativo,", $campi_visualizzati);
                $colore = 'red-intense';
            } else if ($whr_state == "13d261a138ce95e3c007931d9653e951") { //Chiuso
                $titolo = 'Elenco Ordini / Preventivi / Offerte - Chiusi';
                $campi_visualizzati = str_replace("data_iscrizione,", "data_firma AS data_firma,", $campi_visualizzati);
                $campi_visualizzati = str_replace("DATE_FORMAT(DATE(data_creazione), '%d-%m-%Y') AS `Creato_il`,", "DATE_FORMAT(DATE(data_iscrizione), '%d-%m-%Y') AS `Iscritto_il`,", $campi_visualizzati);
                $colore = 'green-jungle';
            } else {
                $titolo = 'Elenco Ordini / Preventivi / Offerte';
                $colore = COLORE_PRIMARIO;
            }

            $ordine = $table_listaPreventivi['index']['order'];

            $sql_0001 = "SELECT " . $campi_visualizzati . " FROM " . $tabella . " WHERE $where $ordine LIMIT 1";
            //stampa_table_datatables_responsive($sql_0001, $titolo, 'tabella_base');
            stampa_table_datatables_ajax($sql_0001, '#datatable_ajax', $titolo, 'datatable_ajax', $colore);
            break;
    }
}

function Stampa_HTML_Modifica_Preventivi($tabella, $id, $titolo) {

    switch ($tabella) {
        case "lista_preventivi":
            stampa_bootstrap_form_horizontal($tabella, $id, $titolo);
            break;

        case "lista_preventivi_dettaglio":
            stampa_bootstrap_form_horizontal($tabella, $id, $titolo, 'salva.php?fn=salvaPreventivoDettaglio');
            break;

        default:
            break;
    }
}

function Stampa_HTML_Dettaglio_Preventivi($tabella, $id) {
    global $dblink;
    
    switch ($tabella) {
        case 'lista_preventivi':
            echo '<div class="row">';
            echo '<div class="col-md-6 col-sm-6">';

            $sql_aggionro_prev_dett = "UPDATE lista_preventivi_dettaglio, lista_preventivi
            SET lista_preventivi_dettaglio.id_professionista = lista_preventivi.id_professionista, 
            lista_preventivi_dettaglio.id_azienda = lista_preventivi.id_azienda,
            lista_preventivi_dettaglio.id_calendario = lista_preventivi.id_calendario,
            lista_preventivi_dettaglio.id_campagna = lista_preventivi.id_campagna
            WHERE lista_preventivi_dettaglio.id_preventivo = lista_preventivi.id
            AND lista_preventivi.id= '" . $id . "'";
            $rs_aggionro_prev_dett = $dblink->query($sql_aggionro_prev_dett);

            $sql_0002 = "SELECT 
            CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"" . BASE_URL . "/moduli/anagrafiche/modifica.php?tbl=lista_professionisti&id=',lista_professionisti.id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',
            CONCAT('<h2>',cognome,' ',nome,'</h2>
            Cod. Fiscale: ',codice_fiscale,'
            <br>altri campi') AS ''
            FROM lista_professionisti INNER JOIN lista_preventivi
            ON lista_professionisti.id = lista_preventivi.id_professionista
            WHERE lista_preventivi.id= '" . $id . "'";
            stampa_table_static_basic($sql_0002, '', 'PROFESSIONISTA', 'green-seagreen');

            echo '</div>';
            echo '<div class="col-md-6 col-sm-6">';
            $sql_0003 = "SELECT 
            CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"" . BASE_URL . "/moduli/anagrafiche/modifica.php?tbl=lista_aziende&id=',lista_aziende.id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',
            CONCAT('<h2>',ragione_sociale,'</h2>
            Cod. Fiscale: ',codice_fiscale,' - PIva: ',partita_iva,'
            <br>Indirizzo: ',indirizzo,'<br>',citta,' ',cap,' (', UPPER(provincia),')
            <br>altri campi') AS ''
            FROM lista_aziende INNER JOIN lista_preventivi
            ON lista_aziende.id = lista_preventivi.id_azienda
            WHERE lista_preventivi.id= " . $id;
            stampa_table_static_basic($sql_0003, '', 'DATI FATTURAZIONE', 'green-meadow');
            echo '</div>';
            echo '</div>';



            $sql_0001_controllo_classe = "SELECT id_moodle_user AS idMoodle, id_classe, (SELECT nome FROM lista_classi WHERE id = id_classe) as Classe FROM lista_professionisti 
            INNER JOIN lista_preventivi ON lista_professionisti.id = lista_preventivi.id_professionista
            WHERE lista_preventivi.id = '" . $id . "' AND lista_preventivi.id_professionista>0";
            $rowUtente = $dblink->get_row($sql_0001_controllo_classe, true);

            if (!empty($rowUtente)) {
                echo '<div class="row"><div class="col-md-12 col-sm-12">';
                $sql_0001_controllo_classe_moodle = "SELECT data AS ClasseMoodle FROM " . MOODLE_DB_NAME . ".`mdl_user_info_data` WHERE `mdl_user_info_data`.`userid` ='" . $rowUtente['idMoodle'] . "' AND `fieldid` = 2";
                //$rowUtenteMoodle = $dblink->get_row($sql_0001_controllo_classe_moodle, true);

                //echo '<li>Classe ERP = '.$rowUtente['Classe'].'</li>';
                //echo '<li>Classe Moodle = '.$rowUtenteMoodle['ClasseMoodle'].'</li>';

                $sql_0032 = "SELECT mdl_user.`id` AS 'Id Moodle', mdl_user.`username`, mdl_user.`email`, "
                        . "DATE_FORMAT(FROM_UNIXTIME(mdl_user.`lastlogin`),'%d-%m-%Y') AS 'Ultimo Login', "
                        . "DATE_FORMAT(FROM_UNIXTIME(mdl_user.`lastaccess`),'%d-%m-%Y') AS 'Ultimo Accesso', "
                        . "IF(mdl_user_info_data.data='', 'Abbonamento_Disattivato', mdl_user_info_data.data) AS 'Data Scadenza Abbonamento',
                       (SELECT IF(data LIKE '" . $rowUtente['Classe'] . "',CONCAT('<span class=\"btn sbold uppercase btn-outline green-jungle\">',data,'</span>'),CONCAT('<span class=\"btn sbold uppercase btn-outline red-flamingo\"><i class=\"fa fa-thumbs-o-down\"></i><h1>',data,'</h1></span>')) FROM " . MOODLE_DB_NAME . ".`mdl_user_info_data` WHERE `mdl_user_info_data`.`userid` = mdl_user.`id` AND `fieldid` = 2 ) AS 'VERIFICA CLASSE MANUALE'
                       FROM " . MOODLE_DB_NAME . ".`mdl_user` "
                        . "INNER JOIN " . MOODLE_DB_NAME . ".`mdl_user_info_data`"
                        . "ON mdl_user.id = mdl_user_info_data.userid "
                        . "WHERE mdl_user.`id` = " . $rowUtente['idMoodle'] . " AND mdl_user_info_data.fieldid='1'";
                //stampa_table_static_basic($sql_0032, '', 'Stato Utente attuale su Moodle', 'green-jungle', 'fa fa-user');
                echo '</div></div>';
            } else {
                $rowUtente['id_classe'] = 0;
            }

            echo '<div class="row"><div class="col-md-12 col-sm-12">';
            $statoFattura = $dblink->get_row("SELECT lista_fatture.stato FROM lista_fatture WHERE lista_fatture.id_preventivo = '$id'", true);
            $sql_0001 = "SELECT
            CONCAT('<span class=\"btn sbold uppercase btn-outline blue\">PREVENTIVO</span>') AS Tipo, 
                IF(id_professionista>0,
                IF(stato LIKE 'Negativo' OR stato LIKE 'Chiuso','',CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"modifica.php?tbl=lista_preventivi&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>')),'') AS 'fa-edit',
                CONCAT('<a class=\"btn btn-circle btn-icon-only red btn-outline\" href=\"printPreventivoPDF.php?id=',`id`,'&idA=',id_area,'\" TARGET=\"_BLANK\" title=\"STAMPA\" alt=\"STAMPA\"><i class=\"fa fa-file-pdf-o\"></i></a>') AS 'PDF',
		CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"inviaPrev.php?idPrev=',id,'\" data-target=\"#ajax\" data-url=\"inviaPrev.php?idPrev=',id,'\" data-toggle=\"modal\" title=\"INVIA\" alt=\"INVIA\"><i class=\"fa fa-paper-plane\"></i></a>') AS 'Invia',
		DATE_FORMAT(DATE(data_creazione), '%d-%m-%Y') AS 'Creato il',
		IF(codice LIKE 'xxx',CONCAT('<small>',codice_interno,'</small>'),CONCAT('<B>',codice,'/',sezionale,'</B>')) AS 'Codice',
                IF(id_professionista<=0,(SELECT CONCAT('<i class=\"fa fa-exclamation-triangle btn btn-icon-only red btn-outline\" style=\"width: 20px; height: 20px; line-height: 0.5;\" onclick=\"javascritp: alert(\\'Professionista non presente nel database!\\');\"></i> ',mittente,'') FROM calendario WHERE id = id_calendario),(SELECT CONCAT('<b>',cognome,' ',nome,'</b><br><small>',IF(id_azienda>0,(SELECT CONCAT('',ragione_sociale,'') FROM lista_aziende WHERE id=`id_azienda`),'') ,'</small>') FROM lista_professionisti WHERE id=`id_professionista`)) AS `Contatto`,
                IF(id_professionista>0,CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"" . BASE_URL . "/moduli/anagrafiche/dettaglio_tab.php?tbl=lista_professionisti&id=',id_professionista,'\" target=\"_blank\" title=\"SCHEDA\" alt=\"SCHEDA\"><i class=\"fa fa-book\"></i></a>'),CONCAT('<a class=\"btn btn-circle btn-icon-only red-pink btn-outline\" target=\"_blank\" href=\"" . BASE_URL . "/moduli/anagrafiche/dettaglio_tab.php?tbl=calendario&id=',id_calendario,'\" title=\"SCHEDA RICHIESTA\" alt=\"SCHEDA RICHIESTA\"><i class=\"fa fa-book\"></i></a>')) AS 'fa-book',
		imponibile AS 'Imponibile &euro;',
		importo AS 'Importo &euro;',
		(SELECT CONCAT(cognome,' ',nome) AS UTENTE FROM lista_password WHERE id=id_agente) AS 'Gestore',
		IF(`stato` = 'Chiuso','<span class=\"badge bg-green-jungle bg-font-green-jungle bold\">Chiuso</span>',
                    IF(`stato` = 'In Attesa',CONCAT('<span class=\"badge bg-yellow-saffron bg-font-yellow-saffron bold\">In Attesa</span>'),
                        IF(`stato` = 'Negativo','<span class=\"badge bg-red-thunderbird bg-font-red-thunderbird\">Negativo</span>',stato)
                    )
                ) AS stato,
                IF(id_professionista>0,
                    IF(" . $rowUtente['id_classe'] . ">0,
                        IF(LENGTH(sezionale)>0,
                            IF(lista_preventivi.stato LIKE 'In Attesa' OR lista_preventivi.stato LIKE 'Venduto' ,
                                CONCAT('<a class=\"btn btn-circle btn-icon-only green-jungle\" href=\"salva.php?tbl=lista_preventivi&idPreventivoFirmato=',id,'&codSezionale=',sezionale,'&fn=preventivoFirmato\" title=\"CHIUSO\" alt=\"CHIUSO\"><i class=\"fa fa-check\"></i></a><a class=\"btn btn-circle btn-icon-only red-thunderbird\" href=\"salva.php?tbl=lista_preventivi&idPreventivoNegativo=',id,'&codSezionale=',sezionale,'&fn=preventivoNegativo\" title=\"NEGATIVO\" alt=\"NEGATIVO\"><i class=\"fa fa-times\"></i></a>'),
                                ''
                            ),
                            CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"modifica.php?tbl=lista_preventivi&id=',id,'\" title=\"SEZIONALE\" alt=\"SEZIONALE\"><i class=\"fa fa-barcode\"></i></a>')
                        ),
                        CONCAT('<a class=\"btn btn-circle red-pink btn-outline\" target=\"_blank\" href=\"" . BASE_URL . "/moduli/anagrafiche/dettaglio_tab.php?tbl=calendario&id=',id_calendario,'\" title=\"SCHEDA RICHIESTA\" alt=\"SCHEDA RICHIESTA\"><i class=\"fa fa-edit\"></i> Classe Professionista</a>')
                    ),
                    CONCAT('<a class=\"btn btn-circle red-pink btn-outline\" target=\"_blank\" href=\"" . BASE_URL . "/moduli/anagrafiche/dettaglio_tab.php?tbl=calendario&id=',id_calendario,'\" title=\"SCHEDA RICHIESTA\" alt=\"SCHEDA RICHIESTA\"><i class=\"fa fa-plus\"></i> Professionista/Azienda</a>')
                ) AS ''
                FROM lista_preventivi WHERE id='" . $id . "'";

            /*
              if( '".$statoFattura['stato']."' = 'In Attesa di Emissione',
              CONCAT('<a class=\"btn btn-circle btn-icon-only red-intense\" href=\"javascript: eliminaPreventivo(\'',id,'\');\" title=\"ELIMINA\" alt=\"ELIMINA\"><i class=\"fa fa-trash\"></i></a>'),
              ''
              )
             */

            stampa_table_static_basic($sql_0001, '', 'Preventivo', '');

            $sql_00001 = "SELECT id, IF(stato LIKE 'Negativo' OR stato LIKE 'Chiuso',0,1) as controllo FROM lista_preventivi WHERE id='" . $id . "' LIMIT 1";
            list($preventivo_controllo_stato) = $dblink->get_row($sql_00001);
            
            echo '</div></div>';
            echo '<div class="row"><div class="col-md-12 col-sm-12">';
            $stile_form = "";
            if ($preventivo_controllo_stato == "0") {
                //IF(stato LIKE 'Negativo' OR stato LIKE 'Chiuso','', CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"modifica.php?tbl=lista_preventivi_dettaglio&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>')) AS 'fa-edit',
                //IF(stato LIKE 'Negativo' OR stato LIKE 'Chiuso','', CONCAT('<a class=\"btn btn-circle btn-icon-only red btn-outline\" href=\"cancella.php?tbl=lista_preventivi_dettaglio&id=',id,'\" title=\"ELIMINA\" alt=\"ELIMINA\"><i class=\"fa fa-trash\"></i></a>')) AS 'fa-trash',
                $sql_0002 = "SELECT
                            dataagg, codice_prodotto AS 'Codice',
                            CONCAT('<b>',nome_prodotto,'</b>') AS 'Prodotto',
                            note AS 'Descrizione Aggiuntiva',
                            prezzo_prodotto AS 'Prezzo',
                            iva_prodotto AS 'Iva',
                            quantita AS 'Qnt'
                            FROM lista_preventivi_dettaglio WHERE id_preventivo='" . $id . "'";
                stampa_table_static_basic($sql_0002, '', 'Dettaglio Preventivo / Prodotti', '');
                if ($preventivo_controllo_stato > 0) {
                    $stile_form = "";
                } else {
                    $stile_form = " display:none; visibility:hidden;";
                }
            }
            echo '</div></div>';

            echo '<form method="POST" id="modifica_preventivo_dettaglio" action="salva.php?idPrev=' . $id . '&fn=SalvaPreventiviDettaglio" style="border:0px solid red; padding:20px; ' . $stile_form . '">';
            echo '<div class="row"><div class="col-md-12 col-sm-12">';
            echo '<a name="#modifica_preventivo_dettaglio"></a>';
            $sql_0003 = "SELECT
                        IF(stato lIKE 'Negativo' OR  stato LIKE 'Chiuso','',CONCAT('<a class=\"btn btn-circle btn-icon-only red btn-outline\" href=\"cancella.php?tbl=lista_preventivi_dettaglio&id=',id,'\" title=\"ELIMINA\" alt=\"ELIMINA\"><i class=\"fa fa-trash\"></i></a>')) AS 'elimina',
                        id, 
                        id_prodotto as nome_prodotto,
                        note AS 'Descrizione Aggiuntiva',
                        prezzo_prodotto AS prezzo, 
                        iva_prodotto AS IVA, quantita AS Qta,
                        id_provvigione AS partner
                        FROM lista_preventivi_dettaglio WHERE id_preventivo='" . $id . "'";
            stampa_table_static_basic_input('lista_preventivi_dettaglio', $sql_0003, '', 'Modifica Preventivo / Prodotti', '');
            echo '<center><a href="salva.php?tbl=lista_preventivi&id_preventivo=' . $id . '&fn=inserisciProdotto" class="btn green-meadow" style="' . $stile_form . '">
                        Aggiungi Prodotto
                        <i class="fa fa-plus"></i>
                        </a> <input class="btn green-sharp" value="Salva" type="submit"></center>';
            


            $sql_00001_prodotto = "SELECT id_prodotto, nome_prodotto, id_preventivo, id_professionista FROM lista_preventivi_dettaglio WHERE id_preventivo='" . $id . "' ORDER BY nome_prodotto";
            $rs_00001_prodotto = $dblink->get_results($sql_00001_prodotto);
                if (!empty($rs_00001_prodotto)) {
                    foreach ($rs_00001_prodotto as $row_00001_prodotto) {
                    $idProdotto = $row_00001_prodotto['id_prodotto'];
                    $idPreventivo = $row_00001_prodotto['id_preventivo'];
                    $nomeProdotto = $row_00001_prodotto['nome_prodotto'];
                    $idProfessionista = $row_00001_prodotto['id_professionista'];
                        echo '<BR><div class="row"><div class="col-md-12 col-sm-12">';
                        $sql_0001 = "SELECT (SELECT id_preventivo FROM calendario WHERE id_professionista = '".$idProfessionista."' AND id_prodotto='" . $idProdotto."' AND etichetta LIKE 'Iscrizione Corso') AS 'fa-o-doc',
                        CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"".BASE_URL."/moduli/corsi/dettaglio.php?tbl=calendario_esami&id=',id,'&idProdotto=',id_prodotto,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',
                        (SELECT IF(id_calendario_0>0,CONCAT('<span class=\"btn sbold uppercase btn-outline green\">ISCRITTO</span>'),CONCAT('<span class=\"btn sbold uppercase btn-outline red-thunderbird\">NON ISCRITTO</span>')) AS 'Tipo' FROM calendario WHERE id_professionista = '".$idProfessionista."' AND id_prodotto='" . $idProdotto."' AND etichetta LIKE 'Iscrizione Corso') AS stato,
                        CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"".BASE_URL."/moduli/corsi/salva.php?tbl=calendario_esami&idCalendario=',id,'&idProfessionista=".$idProfessionista."&idProdotto=',id_prodotto,'&idPreventivo=".$id."&fn=iscriviCorsoUtente\" title=\"ISCRIVI CORSO\" alt=\"ISCRIVI CORSO\"><i class=\"fa fa-user-plus\"></i></a>') AS 'fa-user-plus', 
                        (SELECT IF(id_calendario_0<=0,CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"".BASE_URL."/moduli/corsi/salva.php?tbl=calendario_esami&idCalendario=',id,'&idProfessionista=".$idProfessionista."&idProdotto=',id_prodotto,'&idPreventivo=".$id."&fn=iscriviCorsoUtente\" title=\"ISCRIVI CORSO\" alt=\"ISCRIVI CORSO\"><i class=\"fa fa-user-plus\"></i></a>'),
                        CONCAT('<a class=\"btn btn-circle btn-icon-only red btn-outline\" href=\"".BASE_URL."/moduli/corsi/cancella.php?tbl=calendario_corsi&idCalendario=',id,'&idCalendarioCorso=',id_calendario_0,'\" title=\"DISISCRIVI CORSO\" alt=\"DISISCRIVI CORSO\"><i class=\"fa fa-user-times\"></i></a>')) FROM calendario WHERE id_professionista = '".$idProfessionista."' AND id_prodotto='" . $idProdotto."' AND etichetta LIKE 'Iscrizione Corso') AS 'fa-user-times',
                            data, ora, IF(etichetta LIKE 'Calendario Esami',CONCAT('<span class=\"btn sbold uppercase btn-outline blue\">',etichetta,'</span>'),CONCAT('<span class=\"btn sbold uppercase btn-outline red-thunderbird\">',etichetta,'</span>')) AS 'Tipo', oggetto, numerico_10 AS 'Iscritti'
                            FROM calendario
                            WHERE id_prodotto='" . $idProdotto."'
                            AND etichetta LIKE 'Calendario Corsi'
                            ORDER BY data DESC, ora ASC";
                            $numero_edizioni_disponibili = $dblink->num_rows($sql_0001);
                            if($numero_edizioni_disponibili > 0) {
                                stampa_table_static_basic($sql_0001, '', strtoupper($nomeProdotto).' - Edizioni Disponibili', 'blue');
                            }
                        echo '</div></div>';
                    }
                }
            
            $sql_00001_prodotto = "SELECT id_prodotto, nome_prodotto, id_preventivo, id_professionista FROM lista_preventivi_dettaglio WHERE id_preventivo='" . $id . "' ORDER BY nome_prodotto";
            $rs_00001_prodotto = $dblink->get_results($sql_00001_prodotto);
                if (!empty($rs_00001_prodotto)) {
                    foreach ($rs_00001_prodotto as $row_00001_prodotto) {
                    $idProdotto = $row_00001_prodotto['id_prodotto'];
                    $idPreventivo = $row_00001_prodotto['id_preventivo'];
                    $nomeProdotto = $row_00001_prodotto['nome_prodotto'];
                    $idProfessionista = $row_00001_prodotto['id_professionista'];
                        echo '<BR><div class="row"><div class="col-md-12 col-sm-12">';
                        $sql_0001 = "SELECT (SELECT id_preventivo FROM calendario WHERE id_professionista = '".$idProfessionista."' AND id_prodotto='" . $idProdotto."' AND etichetta LIKE 'Iscrizione Esame') AS 'fa-o-doc',
                        CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"".BASE_URL."/moduli/corsi/dettaglio.php?tbl=calendario_esami&id=',id,'&idProdotto=',id_prodotto,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',
                        (SELECT IF(id_calendario_0>0,CONCAT('<span class=\"btn sbold uppercase btn-outline green\">ISCRITTO</span>'),CONCAT('<span class=\"btn sbold uppercase btn-outline red-thunderbird\">NON ISCRITTO</span>')) AS 'Tipo' FROM calendario WHERE id_professionista = '".$idProfessionista."' AND id_prodotto='" . $idProdotto."' AND etichetta LIKE 'Iscrizione Esame') AS stato,
                        CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"".BASE_URL."/moduli/corsi/salva.php?tbl=calendario_esami&idCalendario=',id,'&idProfessionista=".$idProfessionista."&idProdotto=',id_prodotto,'&idPreventivo=".$id."&fn=iscriviEsameUtente\" title=\"ISCRIVI ESAME\" alt=\"ISCRIVI ESAME\"><i class=\"fa fa-user-plus\"></i></a>') AS 'fa-user-plus', 
                        (SELECT IF(id_calendario_0<=0,CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"".BASE_URL."/moduli/corsi/salva.php?tbl=calendario_esami&idCalendario=',id,'&idProfessionista=".$idProfessionista."&idProdotto=',id_prodotto,'&idPreventivo=".$id."&fn=iscriviEsameUtente\" title=\"ISCRIVI ESAME\" alt=\"ISCRIVI ESAME\"><i class=\"fa fa-user-plus\"></i></a>'),
                        CONCAT('<a class=\"btn btn-circle btn-icon-only red btn-outline\" href=\"".BASE_URL."/moduli/corsi/cancella.php?tbl=calendario_esami&idCalendario=',id,'&idCalendarioCorso=',id_calendario_0,'\" title=\"DISISCRIVI ESAME\" alt=\"DISISCRIVI ESAME\"><i class=\"fa fa-user-times\"></i></a>')) FROM calendario WHERE id_professionista = '".$idProfessionista."' AND id_prodotto='" . $idProdotto."' AND etichetta LIKE 'Iscrizione Esame') AS 'fa-user-times',
                            data, ora, IF(etichetta LIKE 'Calendario Esami',CONCAT('<span class=\"btn sbold uppercase btn-outline blue\">',etichetta,'</span>'),CONCAT('<span class=\"btn sbold uppercase btn-outline red-thunderbird\">',etichetta,'</span>')) AS 'Tipo', oggetto, numerico_10 AS 'Iscritti'
                            FROM calendario
                            WHERE id_prodotto='" . $idProdotto."'
                            AND etichetta LIKE 'Calendario Esami'
                            ORDER BY data DESC, ora ASC";
                           $numero_esami_disponibili = $dblink->num_rows($sql_0001);
                            if($numero_esami_disponibili > 0) {
                                stampa_table_static_basic($sql_0001, '', strtoupper($nomeProdotto).' - Esami Disponibili', 'green');
                            }
                        echo '</div></div>';
                    }
                }

            
            echo '</div></div>';
            echo '</form>';

            echo '<div class="row">';
            echo '<div class="col-md-6 col-sm-6">';
            $sql_0004 = "SELECT SUM((prezzo_prodotto*quantita)) AS '&euro;' "
                    . "FROM lista_preventivi_dettaglio "
                    . "WHERE id_preventivo= '" . $id . "'";
            stampa_table_static_basic($sql_0004, '', 'Totale Imponibile', 'red');
            echo '</div>';
            echo '<div class="col-md-6 col-sm-6">';
            $sql_0005 = "SELECT SUM((prezzo_prodotto*(1+(iva_prodotto/100)))*quantita) AS '&euro;' "
                    . "FROM lista_preventivi_dettaglio "
                    . "WHERE id_preventivo= '" . $id . "'";
            stampa_table_static_basic($sql_0005, '', 'Totale Importo', 'green-meadow');
            echo '</div>';
            echo '</div>';
            
            echo '<div class="row"><div class="col-md-12 col-sm-12">';
            $sql_0004_1 = "SELECT data, ora, mittente, destinatario, etichetta, REPLACE(messaggio,'\\n','<br>') as 'Note' FROM calendario INNER JOIN lista_preventivi ON calendario.id=lista_preventivi.id_calendario 
            WHERE lista_preventivi.id='" . $id . "'";
            stampa_table_static_basic($sql_0004_1, '', 'Richiesta', '');
            echo '</div></div>';

            echo '<div class="row"><div class="col-md-12 col-sm-12">';
            $sql_0004 = "SELECT 
               IF(tipo LIKE 'Fattura',CONCAT('<span class=\"btn sbold uppercase btn-outline blue\">',tipo,'</span>'),CONCAT('<span class=\"btn sbold uppercase btn-outline red-thunderbird\">',tipo,'</span>')) AS 'Tipo',
               CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"" . BASE_URL . "/moduli/fatture/dettaglio.php?tbl=lista_fatture&id=',id,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',
               CONCAT('<a class=\"btn btn-circle btn-icon-only red btn-outline\" href=\"" . BASE_URL . "/moduli/fatture/printFatturaPDF.php?idFatt=',`id`,'&idA=',id_area,'\" TARGET=\"_BLANK\" title=\"STAMPA\" alt=\"STAMPA\"><i class=\"fa fa-file-pdf-o\"></i></a>') AS 'PDF',
               CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"" . BASE_URL . "/moduli/fatture/inviaFatt.php?idFatt=',id,'\" data-target=\"#ajax\" data-url=\"inviaFatt.php?idFatt=',id,'\" data-toggle=\"modal\" title=\"INVIA\" alt=\"INVIA\"><i class=\"fa fa-paper-plane\"></i></a>') AS 'Invia',
               DATE(data_creazione) AS 'Creato il',
               DATE(data_scadenza) AS 'Scade il',
               CONCAT('<b>',`codice`,'/', sezionale ,'</b>') AS codice,
               (SELECT CONCAT('<b>',cognome,' ',nome,'</b><br><small>',(SELECT CONCAT('',ragione_sociale,'') FROM lista_aziende WHERE id=`id_azienda`) ,'</small>') FROM lista_professionisti WHERE id=`id_professionista`) AS 'Contatto',
               IF(id_calendario>0,CONCAT('<a class=\"btn btn-circle btn-icon-only red btn-outline\" href=\"" . BASE_URL . "/moduli/anagrafiche/dettaglio_tab.php?tbl=calendario&id=',id_calendario,'#tab_azienda\" title=\"SCHEDA\" alt=\"SCHEDA\"><i class=\"fa fa-book\"></i></a>'),IF(id_professionista>0,CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"" . BASE_URL . "/moduli/anagrafiche/dettaglio_tab.php?tbl=lista_professionisti&id=',id_professionista,'\" title=\"SCHEDA\" alt=\"SCHEDA\"><i class=\"fa fa-book\"></i></a>'),'')) AS 'fa-book',
               CONCAT(importo,'<br><small>',imponibile,' +iva</small>') AS 'Importo &euro;',
               stato
               FROM lista_fatture WHERE id_preventivo='" . $id . "'";
            stampa_table_static_basic($sql_0004, '', 'Fattura', '');
            echo '</div></div>';

            echo '<div class="row"><div class="col-md-12 col-sm-12">';

            $statoFattura = $dblink->get_field("SELECT stato FROM lista_fatture WHERE id_preventivo = '$id'");


            $sql_0005 = "SELECT
                if(stato LIKE 'Venduto', CONCAT('<a href=\"javascript: annullaStatoVenduto(\'" . $id . "\');\"><span class=\"btn sbold uppercase btn-outline blue-steel\">Annulla Venduto</span></a>'), if(stato LIKE 'Negativo', CONCAT('<a href=\"javascript: annullaStatoNegativo(\'" . $id . "\');\"><span class=\"btn sbold uppercase btn-outline red-intense\">Annulla Negativo</span></a>'),'')) AS 'Annulla Venduto / Negativo',
                if(stato LIKE 'Negativo', CONCAT('<a href=\"javascript: cambiaStatoInVenduto(\'" . $id . "\');\"><span class=\"btn sbold uppercase btn-outline blue-steel\">Cambia in Venduto</span></a>'), if(stato LIKE 'Venduto', CONCAT('<a href=\"javascript: cambiaStatoInNegativo(\'" . $id . "\');\"><span class=\"btn sbold uppercase btn-outline red-intense\">Cambia in Negativo</span></a>'),'')) AS 'Cambia in Venduto / Negativo',
                IF('$statoFattura'='In Attesa di Emissione',
                    if(stato LIKE 'Chiuso', CONCAT('<a href=\"javascript: annullaStatoChiuso(\'" . $id . "\');\"><span class=\"btn sbold uppercase btn-outline green-dark\">Annulla Chiuso</span></a>'), ''),
                '') AS 'Annulla Chiuso',
                IF('$statoFattura'='In Attesa di Emissione', CONCAT('<a href=\"javascript: eliminaPreventivo(\'" . $id . "\');\"><span class=\"btn sbold uppercase btn-outline red-thunderbird\"><i class=\"fa fa-trash\"></i> Cancella Preventivo</span></a>'),
                    if(stato LIKE 'In Attesa',CONCAT('<a href=\"javascript: eliminaPreventivoBase(\'" . $id . "\');\"><span class=\"btn sbold uppercase btn-outline red\"><i class=\"fa fa-trash\"></i> Cancella solo il Preventivo</span></a>'),'')
                ) AS 'Cancella Preventivo'
                FROM lista_preventivi WHERE id='" . $id . "'";

            stampa_table_static_basic($sql_0005, '', 'Gestione Avanzata', 'blue-steel');
            echo '</div></div>';

            break;
    }
}

?>
