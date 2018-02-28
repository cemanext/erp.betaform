<?php

/** FUNZIONI DI CROCCO */
function Stampa_HTML_index_Fatture($tabella){
    global $dblink, $table_listaFatture, $table_listaFattureInvioMultiplo,
           $table_listaFattureRecuperoCrediti, $where_lista_fatture;

    switch($tabella){
    
        case 'lista_fatture_invio_multiplo':
                $tabella = "lista_fatture";
                ECHO '<FORM action="salva.php?fn=inviaFattureMultiple" method="POST">';
                $campi_visualizzati = $table_listaFattureInvioMultiplo['index']['campi'];
                $where = $table_listaFattureInvioMultiplo['index']['where'];
                $ordine = $table_listaFattureInvioMultiplo['index']['order'];
                $titolo = 'Invio Fatture Multiplo';
                $sql_0001 = "SELECT ".$campi_visualizzati." FROM lista_fatture WHERE $where $ordine LIMIT 1";
                //stampa_table_static_basic($sql_0001, '', 'Elenco Fatture', '', 'fa-fa-handshake-o');
                stampa_table_datatables_ajax($sql_0001, '#datatable_ajax', $titolo, '');
                //stampa_table_datatables_responsive($sql_0001, $titolo, 'tabella_base');
                echo '<center><button type="submit" class="btn blue-steel btn-lg"><i class="fa fa-paper-plane"> Invia Selezionate</i></button></center>';
                ECHO '</FORM>';
        break;
        
        case 'lista_fatture_recupero_crediti':
                $tabella = "lista_fatture";
            
                $where = $table_listaFattureRecuperoCrediti['index']['where'];

                $campi_visualizzati = $table_listaFattureRecuperoCrediti['index']['campi'];

                $ordine = $table_listaFattureRecuperoCrediti['index']['order'];
            
                $titolo = 'Elenco Fatture In Attesa di Pagamento';
                $sql_0001 = "SELECT ".$campi_visualizzati." FROM ".$tabella." WHERE $where $ordine LIMIT 1";
                //echo '<li>$sql_0001 = '.$sql_0001.'</li>';
                //stampa_table_datatables_responsive($sql_0001, $titolo, 'tabella_base');
                stampa_table_datatables_ajax($sql_0001, '#datatable_ajax', $titolo, '');
        break;
    
        case 'lista_fatture':
                $tabella = "lista_fatture";
                
                if(isset($_GET['whr_state'])){
                    if($_GET['whr_state']=='d24b24ffc6859354a67488859971308f'){
                        $where = " (MD5(stato_invio)=('".$_GET['whr_state']."'))".$where_lista_fatture;
                    }else{
                        $where = " (MD5(stato)=('".$_GET['whr_state']."'))".$where_lista_fatture;
                    }
		}else{
		    $where = $table_listaFatture['index']['where'];
		}

                $campi_visualizzati = $table_listaFatture['index']['campi'];

                $ordine = $table_listaFatture['index']['order'];
            
                $titolo = 'Elenco Fatture';
                $sql_0001 = "SELECT ".$campi_visualizzati." FROM ".$tabella." WHERE $where $ordine LIMIT 1";
                //echo '<li>$sql_0001 = '.$sql_0001.'</li>';
                //stampa_table_datatables_responsive($sql_0001, $titolo, 'tabella_base');
                stampa_table_datatables_ajax($sql_0001, '#datatable_ajax', $titolo, '');
        break;
        
//crocco
        case 'crea_utenti_moodle':
            $tabella = "lista_password";
            $titolo = 'Crea Utenti Moodle e Password';
            //CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"".BASE_URL."/moduli/fatture/dettaglio.php?tbl=lista_fatture&id=',id_fattura,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',
            
            $sql_0001 = "CREATE TEMPORARY TABLE utentiDaAttivare(SELECT DISTINCT 
            CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"".BASE_URL."/moduli/anagrafiche/dettaglio.php?tbl=lista_professionisti&id=',id_professionista,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-user',
            CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"".BASE_URL."/moduli/anagrafiche/modifica.php?tbl=lista_professionisti&id=',id_professionista,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',
            id_professionista AS 'idProfessionista',
            (SELECT DISTINCT CONCAT('<h3>',cognome,' ',nome,'</h3>') FROM lista_professionisti WHERE id = id_professionista LIMIT 1) AS 'Professionista',
            (SELECT DISTINCT id_classe FROM lista_professionisti WHERE id = id_professionista LIMIT 1) AS 'idClasse',
            (SELECT DISTINCT CONCAT('<h5>',email,'</h5>') FROM lista_professionisti WHERE id = id_professionista LIMIT 1) AS 'Email', 
            (SELECT DISTINCT id FROM lista_password WHERE lista_password.id_professionista = lista_fatture_dettaglio.id_professionista LIMIT 1) AS 'idPassword',
            CONCAT('<center><a href=\"".BASE_URL."/moduli/fatture/salva.php?idProfessionista=',id_professionista,'&fn=creaUtenteTotale\" class=\"btn btn-icon btn-outline blue-ebonyclay\"><i class=\"fa fa-users\"></i> CREA UTENTE </a></center>') AS 'fa-password'
            FROM lista_fatture_dettaglio
            WHERE ( stato LIKE 'In Attesa di Emissione' OR  stato LIKE 'In Attesa') AND lista_fatture_dettaglio.id_professionista > 0
            AND NOT EXISTS (SELECT DISTINCT lista_password.id_professionista FROM lista_password WHERE livello LIKE 'cliente' AND lista_fatture_dettaglio.id_professionista=lista_password.id_professionista));";
            //AND id_professionista NOT IN (SELECT DISTINCT id_professionista FROM lista_password WHERE livello LIKE 'cliente'));";
            $rs_0001 = $dblink->query($sql_0001);
            
            $sql_00000000 = "SELECT * FROM utentiDaAttivare WHERE 1";
            stampa_table_datatables_responsive($sql_00000000, $titolo, 'tabella_base');
        break;
        
        case 'lista_fatture_multiple':
            $tabella = "lista_fatture";
            $titolo = 'Fatture Multiple';
            
                $sql_0001 = "CREATE TEMPORARY TABLE fattureDaAccorpare(SELECT DISTINCT 
                CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"dettaglio.php?tbl=lista_fatture_multiple&idAzienda=',id_azienda,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',
                COUNT(*) AS Conteggio, 
                (SELECT CONCAT('<CENTER><b>',ragione_sociale,'</b></CENTER>') FROM lista_aziende WHERE id=`id_azienda`) AS Aienda,
                sezionale,
                stato FROM `lista_fatture` WHERE stato LIKE 'In Attesa di Emissione' AND id_azienda > 0 AND sezionale NOT LIKE '%CN%' 
                GROUP BY id_azienda, sezionale);";
                //$sql_0 = "SELECT * FROM fattureDaAccorpare WHERE Conteggio>1";
                //stampa_table_datatables_responsive($sql_0001, $titolo, 'tabella_base');
                $rs_0001 = $dblink->query($sql_0001);
                
                
                $sql_00000000 = "SELECT * FROM fattureDaAccorpare WHERE Conteggio>=2";
                stampa_table_datatables_responsive($sql_00000000, $titolo, 'tabella_base');
                //stampa_table_datatables_ajax($sql_0001, '#datatable_ajax', $titolo, '');
        break;
        
        case 'lista_attivazioni':
            $tabella = "lista_fatture";
            $titolo = 'Attivazioni ed Iscrizioni ai Corsi';
            
            $sql_aggiorno_id_classe_da_professionisti = "UPDATE lista_password , lista_professionisti
            SET lista_password.id_classe = lista_professionisti.id_classe
            WHERE lista_password.id_professionista = lista_professionisti.id
            AND lista_password.id_classe<=0
            AND lista_professionisti.id_classe>0";
            $rs_aggiorno_id_classe_da_professionisti = $dblink->query($sql_aggiorno_id_classe_da_professionisti);
            
            
                $sql_0001 = "CREATE TEMPORARY TABLE corsi(SELECT DISTINCT lista_fatture_dettaglio.id_professionista,
                lista_fatture_dettaglio.id_fattura AS 'idFattura',
                lista_fatture_dettaglio.id AS 'idFatturaDettaglio',
                lista_corsi.id AS 'idCorso',
                lista_fatture_dettaglio.id_prodotto AS 'idProdotto',
                id_corso_moodle AS 'idCorsoMoodle',
                'corso' as tipo_attivazione,
                id_moodle_user AS 'idUtenteMoodle',
                lista_password.id_classe AS 'idClasseMoodle',
                CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"salva.php?tbl=lista_fatture&idFattura=',lista_fatture_dettaglio.id_fattura,'&idFatturaDettaglio=',lista_fatture_dettaglio.id,'&idCorso=',lista_corsi.id,'&idCorsoMoodle=',id_corso_moodle,'&idUtenteMoodle=',id_moodle_user,'&idClasseMoodle=',lista_password.id_classe,'&fn=attivaCorsoFattura&idProfessionista=',lista_fatture_dettaglio.id_professionista,'\" title=\"ISCRIVI SINGOLO CORSO\" alt=\"ISCRIVI SINGOLO CORSO\"><i class=\"fa fa-thumb-tack\"></i></a>') AS 'bottone',
                
                CONVERT(lista_corsi.nome_prodotto USING utf8) AS 'Prodotto'
                FROM lista_corsi INNER JOIN lista_fatture_dettaglio
                ON lista_corsi.id_prodotto = lista_fatture_dettaglio.id_prodotto 
                INNER JOIN lista_password ON lista_password.id_professionista = lista_fatture_dettaglio.id_professionista
                WHERE lista_fatture_dettaglio.id_professionista>0
                AND lista_password.id_moodle_user>0
                AND lista_corsi.id_corso_moodle>0
                AND NOT EXISTS (SELECT DISTINCT id_fattura_dettaglio FROM lista_iscrizioni WHERE 1 AND lista_fatture_dettaglio.id=lista_iscrizioni.id_fattura_dettaglio));";
                //AND lista_fatture_dettaglio.id NOT IN (SELECT DISTINCT id_fattura_dettaglio FROM lista_iscrizioni WHERE 1));";
                $rs_0001 = $dblink->query($sql_0001);
                
                $sql_0002 = "CREATE TEMPORARY TABLE abbonamenti(SELECT DISTINCT lista_fatture_dettaglio.id_professionista,
                lista_fatture_dettaglio.id_fattura AS 'idFattura',
                lista_fatture_dettaglio.id AS 'idFatturaDettaglio',
                '' AS 'idCorso',
                lista_fatture_dettaglio.id_prodotto AS 'idProdotto',
                '' AS 'idCorsoMoodle',
                'abbonamento' as tipo_attivazione,
                id_moodle_user AS 'idUtenteMoodle',
                lista_password.id_classe AS 'idClasseMoodle',
                IF(lista_password.id_classe<=0,'ATTENZIONE SELEZIONARE CLASSE',CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"salva.php?tbl=lista_fatture&idFattura=',lista_fatture_dettaglio.id_fattura,'&idFatturaDettaglio=',lista_fatture_dettaglio.id,'&idUtenteMoodle=',id_moodle_user,'&idClasseMoodle=',lista_password.id_classe,'&fn=attivaAbbonamentoFattura&idProfessionista=',lista_fatture_dettaglio.id_professionista,'\" title=\"ISCRIVI ABBONAMENTO\" alt=\"ISCRIVI ABBONAMENTO\"><i class=\"fa fa-thumb-tack\"></i></a>')) AS 'bottone',
                
                CONVERT(lista_prodotti.nome USING utf8) AS 'Prodotto'
                FROM lista_fatture_dettaglio INNER JOIN lista_prodotti ON lista_fatture_dettaglio.id_prodotto = lista_prodotti.id 
                INNER JOIN lista_password ON lista_password.id_professionista = lista_fatture_dettaglio.id_professionista
                WHERE  lista_fatture_dettaglio.id_professionista>0
                AND lista_password.id_moodle_user>0
                AND  lista_prodotti.gruppo LIKE 'ABBONAMENTO'
                AND NOT  EXISTS (SELECT DISTINCT id_fattura_dettaglio FROM lista_iscrizioni WHERE 1 AND lista_fatture_dettaglio.id=lista_iscrizioni.id_fattura_dettaglio));";
                //AND lista_fatture_dettaglio.id NOT IN (SELECT DISTINCT id_fattura_dettaglio FROM lista_iscrizioni WHERE 1));";
                $rs_0002 = $dblink->query($sql_0002);
                
                $sql_0002Bis = "CREATE TEMPORARY TABLE pacchetto (SELECT DISTINCT lista_fatture_dettaglio.id_professionista,
                lista_fatture_dettaglio.id_fattura AS 'idFattura',
                lista_fatture_dettaglio.id AS 'idFatturaDettaglio',
                lista_corsi.id AS 'idCorso',
                lista_fatture_dettaglio.id_prodotto AS 'idProdotto',
                id_corso_moodle AS 'idCorsoMoodle',
                'pacchetto' as tipo_attivazione,
                id_moodle_user AS 'idUtenteMoodle',
                lista_password.id_classe AS 'idClasseMoodle',
                CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"salva.php?tbl=lista_fatture&idFattura=',lista_fatture_dettaglio.id_fattura,'&idFatturaDettaglio=',lista_fatture_dettaglio.id,'&idProdotto=',lista_fatture_dettaglio.id_prodotto,'&idCorso=',lista_corsi.id,'&idCorsoMoodle=',id_corso_moodle,'&idUtenteMoodle=',id_moodle_user,'&idClasseMoodle=',lista_password.id_classe,'&fn=attivaPacchettoFattura&idProfessionista=',lista_fatture_dettaglio.id_professionista,'\" title=\"ISCRIVI CORSO DEL PACCHETTO\" alt=\"ISCRIVI CORSO DEL PACCHETTO\"><i class=\"fa fa-thumb-tack\"></i></a>') AS 'bottone',
                
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
                //AND lista_fatture_dettaglio.id NOT IN (SELECT DISTINCT id_fattura_dettaglio FROM lista_iscrizioni WHERE 1));";
                $rs_0002Bis = $dblink->query($sql_0002Bis);
                
                $sql_0003 = "CREATE TEMPORARY TABLE attivazioniIscrizioni SELECT * FROM corsi
                UNION 
                SELECT *  FROM abbonamenti 
                UNION 
                SELECT *  FROM pacchetto;";
                $rs_0003 = $dblink->query($sql_0003);
                
                $sql_00000000 = "SELECT DISTINCT idFattura, idFatturaDettaglio, idCorso, idProdotto, idCorsoMoodle, tipo_attivazione, idUtenteMoodle, idClasseMoodle,
                
                (SELECT DISTINCT CONCAT(cognome, ' ', nome) FROM lista_professionisti WHERE id = id_professionista) as Professionista, Prodotto, bottone AS 'fa fa-thumb-tack' 
                FROM attivazioniIscrizioni WHERE 1 ORDER BY idFattura DESC";
                
                $sql_00000000 = "SELECT DISTINCT 
                CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"".BASE_URL."/moduli/anagrafiche/dettaglio.php?tbl=lista_professionisti&id=',id_professionista,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-user',
                CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"".BASE_URL."/moduli/anagrafiche/modifica.php?tbl=lista_professionisti&id=',id_professionista,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',
                (SELECT DISTINCT CONCAT('<h3><b>',cognome, ' ', nome,'</b></h3>') FROM lista_professionisti WHERE id = id_professionista) as Professionista, 
                IF(LCASE(tipo_attivazione) LIKE 'abbonamento','<span class=\"btn sbold uppercase btn-outline blue-steel\">Abbonamento</span>',
                IF(LCASE(tipo_attivazione) LIKE 'pacchetto','<span class=\"btn sbold uppercase btn-outline blue-hoki\">Pacchetto</span>','<span class=\"btn sbold uppercase btn-outline green-seagreen\">Singolo Corso</span>')) AS 'Tipo',
                CONCAT('<h3>',Prodotto,'</h3>') AS Prodotto,
                (SELECT DISTINCT nome FROM lista_classi WHERE id = idClasseMoodle) AS 'Classe',
                bottone AS 'fa fa-thumb-tack' 
                FROM attivazioniIscrizioni WHERE 1 ORDER BY idFattura DESC";
                stampa_table_datatables_responsive($sql_00000000, $titolo, 'tabella_base');
                //stampa_table_datatables_ajax($sql_0001, '#datatable_ajax', $titolo, '');
        break;
    }
}

function Stampa_HTML_Dettaglio_Fatture($tabella,$id){
    global $table_listaFatture, $dblink;
    
    switch($tabella){
        
        case 'lista_fatture_multiple':
            $idProfessionista = $_GET['idProfessionista'];
            $idAzienda = $_GET['idAzienda'];
            $tabella = "lista_fatture";
            
            $sql_007_aggiorna_codice_preventivo = "UPDATE lista_fatture_dettaglio, lista_preventivi
            SET lista_fatture_dettaglio.codice_preventivo = CONCAT(lista_preventivi.sezionale,'/', lista_preventivi.codice)
            WHERE lista_preventivi.id = lista_fatture_dettaglio.id_preventivo";
            $rs_007_aggiorna_codice_preventivo = $dblink->query($sql_007_aggiorna_codice_preventivo);
            
            $sql_fattura_principale = "SELECT id AS idFatturaPrincipale FROM lista_fatture WHERE id_azienda='".$idAzienda."' 
            AND stato LIKE 'In Attesa di Emissione' ORDER BY id ASC LIMIT 1";
            $idFatturaPrincipale = $dblink->get_field($sql_fattura_principale);
            
            /*
            echo '<div class="row"><div class="col-md-12 col-sm-12">';
            $sql_0001 = "SELECT CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"".BASE_URL."/moduli/anagrafiche/dettaglio.php?tbl=lista_professionisti&id=',id,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',
            cognome, nome, email FROM lista_professionisti WHERE id = " . $idProfessionista;
            stampa_table_static_basic($sql_0001, '', 'Dettaglio Professionista', '', 'fa fa-user');
            echo '</div></div>';
             */   
            echo '<div class="row"><div class="col-md-12 col-sm-12">';
            $sql_0001 = "SELECT CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"".BASE_URL."/moduli/anagrafiche/dettaglio.php?tbl=lista_aziende&id=',id,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',
            CONCAT('<H3>',ragione_sociale,'</h3>') AS Ragione_Sociale, email FROM lista_aziende WHERE id = " . $idAzienda;
            stampa_table_static_basic($sql_0001, '', 'Dettaglio Azienda', '', 'fa fa-user');
            echo '</div></div>';
                      
            $sql_007_aggiorna_id_fattura_0 = "UPDATE lista_fatture_dettaglio 
            SET id_fattura_0 = id_fattura
            WHERE id_fattura_0<=0";
            $rs_007_aggiorna_id_fattura_0 = $dblink->query($sql_007_aggiorna_id_fattura_0);
            
            $sql_007_aggiorna_id_azienda = "UPDATE lista_fatture_dettaglio, lista_fatture
            SET lista_fatture_dettaglio.id_azienda = lista_fatture.id_azienda
            WHERE 1
            AND lista_fatture_dettaglio.id_fattura_0 = lista_fatture.id";
            $rs_007_aggiorna_id_azienda = $dblink->query($sql_007_aggiorna_id_azienda);
            //lista_fatture_dettaglio.id_azienda<=0
            
            $sql_007_aggiorna_id_professionista = "UPDATE lista_fatture_dettaglio, lista_fatture
            SET lista_fatture_dettaglio.id_professionista = lista_fatture.id_professionista
            WHERE lista_fatture_dettaglio.id_professionista <= 0
            AND lista_fatture_dettaglio.id_fattura_0 = lista_fatture.id";
            $rs_007_aggiorna_id_professionista = $dblink->query($sql_007_aggiorna_id_professionista);
            //lista_fatture_dettaglio.id_professionista<=0
            
                echo '<div class="row"><div class="col-md-12 col-sm-12">';
                ECHO '<FORM action="salva.php?fn=accorpaFatture&idFatturaPrincipale='.$idFatturaPrincipale.'&idAzienda='.$idAzienda.'" method="POST">';
                ECHO '<INPUT VALUE="'.$idFatturaPrincipale.'" TYPE="HIDDEN" id="idFatturaPrincipale" name="idFatturaPrincipale">';
                ECHO '<INPUT VALUE="'.$idAzienda.'" TYPE="HIDDEN" id="idAzienda" name="idAzienda">';
                /*
                $sql_0001 = "SELECT DISTINCT lista_fatture_dettaglio.id, 
                lista_fatture_dettaglio.id_fattura_0, 
                (SELECT CONCAT(cognome, ' ', nome) FROM lista_professionisti WHERE id=lista_fatture_dettaglio.id_professionista) AS 'Professionista',
                (SELECT ragione_sociale FROM lista_aziende WHERE id=lista_fatture_dettaglio.id_azienda) AS 'Azienda',
                lista_fatture_dettaglio.codice_preventivo, id_fattura, lista_fatture_dettaglio.nome_prodotto, 
                lista_fatture_dettaglio.prezzo_prodotto, lista_fatture_dettaglio.iva_prodotto,
                lista_fatture_dettaglio.quantita, lista_fatture_dettaglio.id AS selezione
                FROM lista_fatture_dettaglio INNER JOIN lista_fatture ON lista_fatture_dettaglio.id_fattura = lista_fatture.id
                WHERE lista_fatture_dettaglio.id_azienda = " . $idAzienda."
                AND lista_fatture.id_azienda = " . $idAzienda."
                AND lista_fatture.stato='In Attesa di Emissione' 
                AND lista_fatture_dettaglio.stato NOT LIKE 'Accorpata'
                ORDER BY codice_preventivo ASC";
                */
                $sql_0001 = "SELECT 
                CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"".BASE_URL."/moduli/fatture/dettaglio.php?tbl=lista_fatture&id=',lista_fatture_dettaglio.id_fattura,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',
                lista_fatture_dettaglio.codice_preventivo,
                (SELECT CONCAT(cognome, ' ', nome) FROM lista_professionisti WHERE id=lista_fatture_dettaglio.id_professionista) AS 'Professionista',
                lista_fatture_dettaglio.nome_prodotto, 
                lista_fatture_dettaglio.prezzo_prodotto, lista_fatture_dettaglio.iva_prodotto,
                lista_fatture_dettaglio.quantita, lista_fatture_dettaglio.id AS selezione
                FROM lista_fatture_dettaglio INNER JOIN lista_fatture ON lista_fatture_dettaglio.id_fattura = lista_fatture.id
                WHERE lista_fatture_dettaglio.id_azienda = '" . $idAzienda."'
                AND lista_fatture.id_azienda = '" . $idAzienda."'
                AND lista_fatture.stato='In Attesa di Emissione' 
                AND lista_fatture_dettaglio.stato NOT LIKE 'Accorpata'
                ORDER BY codice_preventivo ASC";
                stampa_table_static_basic($sql_0001, '', 'Dettaglio Fatture', '', 'fa fa-exclamation-triangle');
                echo '<center><button type="submit" class="btn purple-intense"><i class="fa fa-link"> ('.$idFatturaPrincipale.') Accorpa Selezionate</i></button></center>';
                ECHO '</FORM><br><br>';
                echo '</div></div>';
                
               echo '<div class="row"><div class="col-md-12 col-sm-12">';
                $sql_0001 = "SELECT CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"".BASE_URL."/moduli/preventivi/dettaglio.php?tbl=lista_preventivi&id=',lista_preventivi.id,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',
                lista_preventivi.data_firma, CONCAT(lista_preventivi.sezionale,'/',lista_preventivi.codice) AS Codice, lista_preventivi.imponibile, lista_preventivi.importo, lista_preventivi.stato 
                FROM lista_preventivi INNER JOIN lista_fatture ON lista_preventivi.id = lista_fatture.id_preventivo
                WHERE 1
                AND lista_fatture.id_azienda = '" . $idAzienda."'
                AND lista_preventivi.id_professionista = lista_fatture.id_professionista
                AND lista_fatture.stato='In Attesa di Emissione' ORDER BY codice ASC";
                stampa_table_static_basic($sql_0001, '', 'Ordini In Attesa di Fatturazione', 'green-jungle', 'fa fa-money');
                echo '</div></div>';
                
                /*
                echo '<div class="row"><div class="col-md-12 col-sm-12">';
                $sql_0001 = "SELECT id, sezionale, codice, id_azienda, id_professionista FROM lista_fatture WHERE id_azienda='$idAzienda' AND stato='In Attesa di Emissione'";
                stampa_table_static_basic($sql_0001, '', 'Fatture', 'red-thunderbird', 'fa-fa-handshake-o');
                echo '</div></div>';
                */
        break;
        
        case 'lista_fatture':
            $tabella = "lista_fatture";
            
            $sql_007_aggiorna_id_calendario = "UPDATE lista_fatture, lista_preventivi
            SET lista_fatture.id_calendario = lista_preventivi.id_calendario, 
            lista_fatture.id_campagna = lista_preventivi.id_campagna
            WHERE lista_fatture.id = '".$id."'
            AND lista_fatture.id_preventivo = lista_preventivi.id";
            $rs_007_aggiorna_id_calendario = $dblink->query($sql_007_aggiorna_id_calendario);

            $sql_007_aggiorna_nome_prodotto = "UPDATE lista_fatture_dettaglio, lista_prodotti
            SET lista_fatture_dettaglio.nome_prodotto = lista_prodotti.nome
            WHERE lista_fatture_dettaglio.id_fattura = '".$id."'
            AND lista_fatture_dettaglio.id_prodotto = lista_prodotti.id";
            $rs_007_aggiorna_nome_prodotto = $dblink->query($sql_007_aggiorna_nome_prodotto);			
            
            $sql_007_aggiorna_id_professionsita_fattura = "UPDATE lista_fatture, lista_fatture_dettaglio
            SET lista_fatture_dettaglio.id_professionista = lista_fatture.id_professionista
            WHERE lista_fatture.id = '".$id."'
            AND lista_fatture.id = lista_fatture_dettaglio.id_fattura_0
            AND lista_fatture_dettaglio.id_professionista<=0
            AND lista_fatture.id = ".$id;
            $rs_007_aggiorna_id_professionsita_fattura = $dblink->query($sql_007_aggiorna_id_professionsita_fattura); 
            
            $sql_007_aggiorna_importi = "SELECT 
            SUM((prezzo_prodotto*quantita)) AS imponibile, 
            SUM((prezzo_prodotto*(1+(iva_prodotto/100)))*quantita) AS 'importo' 
            FROM lista_fatture_dettaglio WHERE id_fattura=".$id;
            $rs_007_aggiorna_importi = $dblink->get_results($sql_007_aggiorna_importi); 
            if (!empty($rs_007_aggiorna_importi)) {
                foreach ($rs_007_aggiorna_importi as $row_007_aggiorna_importi) {
                    $totale_imponibile = $row_007_aggiorna_importi['imponibile'];
                    $totale_importo = $row_007_aggiorna_importi['importo'];
                }
            }
			
			
            $sql_fattura_pagata_parziale_da_costi = "SELECT SUM(entrate) as totale_pagato_parziale FROM lista_costi WHERE id_fattura ='".$id."' AND stato='Pagata Parziale'";
            $totale_pagato_parziale = $dblink->get_field($sql_fattura_pagata_parziale_da_costi); 
			
			
            $differenza_totale_pagato_parziale = $totale_importo - $totale_pagato_parziale;

//          echo '<h1>$totale_importo = '.$totale_importo.'</h1>';
//          echo '<h1>$totale_pagato_parziale = '.$totale_pagato_parziale.'</h1>';
//          echo '<h1>$differenza_totale_pagato_parziale = '.$differenza_totale_pagato_parziale.'</h1>';
			
			
            $sql_id_fattura_nota_di_credito_parziale = "SELECT id_fattura_nota_credito FROM lista_fatture  WHERE id='".$id."'";
            $id_fattura_nota_credito_trovata = $dblink->get_field($sql_id_fattura_nota_di_credito_parziale); 
			
            //echo '<h1>$id_fattura_nota_credito_trovata = '.$id_fattura_nota_credito_trovata.'</h1>';
			
            if($id_fattura_nota_credito_trovata>0){
                $sql_fattura_nota_di_credito_parziale_da_fatture = "SELECT SUM(importo) as totale_nota_di_credito_parziale FROM lista_fatture WHERE id ='".$id_fattura_nota_credito_trovata."' AND stato='Nota di Credito Parziale'";
                $totale_nota_di_credito_parziale = $dblink->get_field($sql_fattura_nota_di_credito_parziale_da_fatture); 
            }else{
                $totale_nota_di_credito_parziale = 0;
            }
			
            //echo '<h1>$totale_nota_di_credito_parziale = '.$totale_nota_di_credito_parziale.'</h1>';
            $differenza_totale_nota_di_credito_parziale = $totale_importo - abs($totale_nota_di_credito_parziale);
            //echo '<h1>$differenza_totale_nota_di_credito_parziale = '.$differenza_totale_nota_di_credito_parziale.'</h1>';
			
			
            $sql_conto_fatture_dettaglio = "SELECT count(id) AS conto FROM lista_fatture_dettaglio WHERE id_fattura ='".$id."'";
            $conto_fatture_dettaglio = $dblink->get_field($sql_conto_fatture_dettaglio);

            if($conto_fatture_dettaglio<=0){
                $sql_inserisci_fattura_dettaglio = "INSERT INTO lista_fatture_dettaglio (`id`, `id_fattura`, `id_sezionale`, `sezionale`, `id_professionista`, `id_azienda`, `id_preventivo`, `codice_preventivo`, `codice_fattura`,  `prezzo_prodotto`, `iva_prodotto`,`quantita`, `scrittore`, `stato`, `id_campagna`, `id_calendario` ) SELECT '', `id`, `id_sezionale`, `sezionale`, `id_professionista`, `id_azienda`, `id_preventivo`, `codice_preventivo`, `codice`, `imponibile`, `iva`, '1', '".addslashes($_SESSION['cognome_nome_utente'])."', `stato`, `id_campagna`, `id_calendario` FROM lista_fatture WHERE id='".$id."'";
                $rs_inserisci_fattura_dettaglio = $dblink->query($sql_inserisci_fattura_dettaglio);
            }
			
            
            $sql_007_aggiorna_fattura = "UPDATE lista_fatture 
            SET imponibile ='".$totale_imponibile."',
            importo = '".$totale_importo."'
            WHERE id='".$id."' AND stato LIKE 'In Attesa di Emissione'";
			//WHERE id='".$id."' AND (scrittore NOT LIKE '%2017.xls%' OR stato LIKE 'In Attesa di Emissione'";
            $rs_007_aggiorna_fattura = $dblink->query($sql_007_aggiorna_fattura); 
            
            $sql_007_aggiorna_fattura_nome_sezionale = "UPDATE lista_fatture 
            SET sezionale = (SELECT nome FROM lista_fatture_sezionali WHERE id = id_sezionale)
            WHERE id='".$id."' AND LENGTH(sezionale)>=0 AND id_sezionale>0
            AND stato LIKE 'In Attesa di Emissione'";
			//WHERE id='".$id."' AND (scrittore NOT LIKE '%2017.xls%' OR stato LIKE 'In Attesa di Emissione'";
            $rs_007_aggiorna_fattura_nome_sezionale = $dblink->query($sql_007_aggiorna_fattura_nome_sezionale); 
            
            
            /*$sql_00001 = "SELECT id_professionista, id_azienda,  id_campagna, id_calendario,
            IF(stato LIKE 'Nota di Credito Totale' OR stato LIKE 'In Attesa' OR stato LIKE 'Pagata' OR stato LIKE 'Annullata'  OR stato LIKE 'Pagata Parziale%',0,1) as controllo, stato, tipo, importo FROM lista_fatture WHERE id='" . $id . "' LIMIT 1";
            */
            
            //tolto controllo stato fattura richiesta di martina
            $sql_00001 = "SELECT id_professionista, id_azienda,  id_campagna, id_calendario,
            1 as controllo, stato, tipo, importo FROM lista_fatture WHERE id='" . $id . "' LIMIT 1";
            $rs_00001 = $dblink->get_results($sql_00001);
            if (!empty($rs_00001)) {
                foreach ($rs_00001 as $row_00001) {
                    $idProfessionista = $row_00001['id_professionista'];
                    $idAzienda = $row_00001['id_azienda'];
                    $fattura_controllo_stato = $row_00001['controllo'];
                    $fattura_stato = $row_00001['stato'];
                    $idCalendarioRichiesta = $row_00001['id_calendario'];
                    $tipoFattura = $row_00001['tipo'];
                    $importoFattura = $row_00001['importo'];
                }
            }
            
            //echo '------------>'.$tipoFattura;
            if($tipoFattura=='Fattura' and $fattura_controllo_stato>0){
                $stile_form = "";
                //$stile_form = " display:none; visibility:hidden;";
            }else{
                //$stile_form = "";
                $stile_form = " display:none; visibility:hidden;";
            }
            
            echo '<div class="row">';
            echo '<div class="col-md-6 col-sm-6">';
            
            //$sql_controlla_accorpate = "SELECT id FROM lista_fatture_dettaglio WHERE id_fattura_0 = '".$id."' AND stato='Accorpata'";
            $sql_controlla_accorpate = "SELECT id_professionista FROM lista_fatture_dettaglio WHERE id_fattura = '".$id."'";
            $rs_controlla_accorpate = $dblink->num_rows($sql_controlla_accorpate);
            if($rs_controlla_accorpate>1){
                $sql_0002 = "SELECT 
                CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"".BASE_URL."/moduli/anagrafiche/modifica.php?tbl=lista_professionisti&id=',id_professionista,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',
                (SELECT CONCAT('<h4>',cognome,' ',nome,'</h4>') FROM lista_professionisti WHERE id = id_professionista) AS '',
				(SELECT CONCAT('<span class=\"btn sbold uppercase btn-outline blue\">',codice,'</span>') FROM lista_professionisti WHERE id = id_professionista) AS  'Cod. Cliente',
				CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"".BASE_URL."/moduli/anagrafiche/dettaglio.php?tbl=lista_professionisti&id=',id_professionista,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search'
                FROM lista_fatture_dettaglio
                WHERE id_fattura = '".$id."'";
                stampa_table_static_basic($sql_0002,'','PROFESSIONISTI', 'green-seagreen');
            }else{
                $sql_0002 = "SELECT 
                CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"".BASE_URL."/moduli/anagrafiche/modifica.php?tbl=lista_professionisti&id=',lista_professionisti.id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',
                CONCAT('<h2>',cognome,' ',nome,'</h2>') AS '',
                            CONCAT('<span class=\"btn sbold uppercase btn-outline blue\">',lista_professionisti.codice,'</span>') AS 'Cod. Cliente',
                            CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"".BASE_URL."/moduli/anagrafiche/dettaglio.php?tbl=lista_professionisti&id=',id_professionista,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search'
                FROM lista_professionisti INNER JOIN lista_fatture
                ON lista_professionisti.id = lista_fatture.id_professionista
                WHERE lista_fatture.id= ".$id;
                stampa_table_static_basic($sql_0002,'','PROFESSIONISTA', 'green-seagreen');
            }
            echo '</div>';
            echo '<div class="col-md-6 col-sm-6">';
            $sql_0003 = "SELECT 
            CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"".BASE_URL."/moduli/anagrafiche/modifica.php?tbl=lista_aziende&id=',lista_aziende.id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',
            CONCAT('<h2>',ragione_sociale,'</h2>
            Cod. Fiscale: ',codice_fiscale,' - PIva: ',partita_iva,'
            <br>Indirizzo: ',indirizzo,'<br>',citta,' ',cap,' (', UPPER(provincia),')') AS ''
            FROM lista_aziende INNER JOIN lista_fatture
            ON lista_aziende.id = lista_fatture.id_azienda
            WHERE lista_fatture.id= ".$id;
            stampa_table_static_basic($sql_0003,'','DATI FATTURAZIONE', 'green-meadow');
            echo '<center><a href="#aziendedisponibili" class="btn btn-icon green-meadow"><i class="fa fa-plus"></i> Aziende Disponibili</a></center>';
            echo '<br></div>';
            echo '</div>';

            echo '<div class="row"><div class="col-md-12 col-sm-12">';
            $sql_0004 = "SELECT 
               IF($fattura_controllo_stato>0 OR tipo LIKE 'Nota di Credito%',CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"modifica.php?tbl=lista_fatture&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>'),'') AS 'fa-edit',
               IF(tipo LIKE 'Fattura',CONCAT('<span class=\"btn sbold uppercase btn-outline blue\">',tipo,'</span>'),CONCAT('<span class=\"btn sbold uppercase btn-outline red-thunderbird\">',tipo,'</span>')) AS 'Tipo',
               CONCAT('<a class=\"btn btn-circle btn-icon-only red btn-outline\" href=\"printFatturaPDF.php?idFatt=',`id`,'&idA=',id_area,'\" TARGET=\"_BLANK\" title=\"STAMPA\" alt=\"STAMPA\"><i class=\"fa fa-file-pdf-o\"></i></a>') AS 'PDF',
               CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"inviaFatt.php?idFatt=',id,'\" data-target=\"#ajax\" data-url=\"inviaFatt.php?idFatt=',id,'\" data-toggle=\"modal\" title=\"INVIA\" alt=\"INVIA\"><i class=\"fa fa-paper-plane\"></i></a>') AS 'Invia',
               DATE(data_creazione) AS 'Creato il',
               DATE(data_scadenza) AS 'Scade il',
               codice_ricerca AS codice,
               (SELECT CONCAT('<b>',cognome,' ',nome,'</b><br><small>',(SELECT CONCAT('',ragione_sociale,'') FROM lista_aziende WHERE id=`id_azienda`) ,'</small>') FROM lista_professionisti WHERE id=`id_professionista`) AS 'Contatto',
               IF(id_calendario>0,CONCAT('<a class=\"btn btn-circle btn-icon-only red btn-outline\" href=\"".BASE_URL."/moduli/anagrafiche/dettaglio_tab.php?tbl=calendario&id=',id_calendario,'#tab_azienda\" title=\"SCHEDA\" alt=\"SCHEDA\"><i class=\"fa fa-book\"></i></a>'),IF(id_professionista>0,CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"".BASE_URL."/moduli/anagrafiche/dettaglio_tab.php?tbl=lista_professionisti&id=',id_professionista,'\" title=\"SCHEDA\" alt=\"SCHEDA\"><i class=\"fa fa-book\"></i></a>'),'')) AS 'fa-book',
               CONCAT(importo,'<br><small>',imponibile,' +iva</small>') AS 'Importo &euro;',
               stato,
               IF(sezionale!='',IF(lista_fatture.stato LIKE 'In Attesa di Emissione',CONCAT('<a class=\"btn btn-circle btn-icon-only green-jungle\" href=\"salva.php?tbl=lista_fatture&idFatturaEmettere=',id,'&codSezionale=',sezionale,'&fn=emettiFattura\" title=\"EMETTI\" alt=\"EMETTI\"><i class=\"fa fa-sign-out\"></i></a>'),''),'<b>Manca il Sezionale</b>') AS 'EMETTI'
               FROM lista_fatture WHERE id=".$id;
            stampa_table_static_basic($sql_0004,'',''.$tipoFattura.'', '');
            echo '</div></div>';
            if($fattura_stato=='In Attesa' OR $fattura_stato=='Pagata Parziale' OR $fattura_stato=='Pagata'){
                echo '<div class="row"><div class="col-md-12 col-sm-12">';
                $sql_00000000000001 = "SELECT 
                IF(stato LIKE 'In Attesa',CONCAT('<a href=\"salva.php?tbl=lista_fatture&idFatturaPagata=',id,'&fn=fatturaPagata&statoPagamento=Totale&importoTotale=".$differenza_totale_nota_di_credito_parziale."\"><span class=\"btn sbold uppercase btn-outline green-meadow\">PAGA TOTALE</span></a>'),
                IF($id_fattura_nota_credito_trovata > 0,'',CONCAT('<a href=\"salva.php?tbl=lista_fatture&idFatturaPagata=',id,'&fn=fatturaResettaPagamento\"><span class=\"btn sbold uppercase btn-outline purple-seance\">RESETTA PAGAMENTO</span></a>'))) AS 'Paga Totale',
                IF(stato NOT LIKE 'Pagata',CONCAT('<a href=\"salva.php?tbl=lista_fatture&idFatturaPagata=',id,'&fn=fatturaPagata&statoPagamento=Parziale\"><span class=\"btn sbold uppercase btn-outline green\">PAGA PARZIALE</span></a>'),'') AS 'Paga Parziale',
                IF(stato LIKE 'Pagata Parziale',CONCAT('<a href=\"salva.php?tbl=lista_fatture&idFatturaPagataTotaleParziale=',id,'&fn=fatturaPagataTotaleParziale&importoTotale=".$differenza_totale_pagato_parziale."\"><span class=\"btn sbold uppercase btn-outline green\">PAGA TOTALE da PARZIALE</span></a>'),'') AS 'Paga Totale da Parziale',
                importo AS 'fa fa-eur', 
                CONCAT('<a href=\"salva.php?tbl=lista_fatture&idFatturaPagata=',id,'&codSezionale=',sezionale,'&fn=notaDiCredito&statoPagamento=Totale\"><span class=\"btn sbold uppercase btn-outline red-thunderbird\">Nota di Credito Totale</span></a>') AS 'Nota di Credito Totale',
                CONCAT('<a href=\"salva.php?tbl=lista_fatture&idFatturaPagata=',id,'&codSezionale=',sezionale,'&fn=notaDiCredito&statoPagamento=Parziale\"><span class=\"btn sbold uppercase btn-outline red\">Nota di Credito Parziale</span></a>') AS 'Nota di Credito Parziale'
                FROM lista_fatture WHERE id=".$id;
                stampa_table_static_basic($sql_00000000000001,'','Pagamenti', 'blue-sharp');
                echo '</div></div>';
            }

            //echo str_replace('-','/',GiraDataOra('2018-08-01'));
            echo '<form method="POST" action="salva.php?idFatt=' . $id . '&fn=SalvaFatturaDettaglio">';
            if($fattura_controllo_stato>0){
                $sql_0005 = "SELECT id,
                CONCAT('<a class=\"btn btn-circle btn-icon-only red btn-outline\" href=\"cancella.php?tbl=lista_fatture_dettaglio&id=',id,'\" title=\"ELIMINA\" alt=\"ELIMINA\"><i class=\"fa fa-trash\"></i></a>') AS 'fa-trash',
                CONCAT(id_professionista,'|',$idAzienda) AS 'id_professionista',
                id_prodotto AS 'nome_prodotto',
                note AS 'Descrizione Aggiuntiva', 
                prezzo_prodotto AS 'Prezzo',
                iva_prodotto AS 'Iva',
                quantita AS 'Qnt',
                id_provvigione AS partner
                FROM lista_fatture_dettaglio WHERE id_fattura=".$id;
                stampa_table_static_basic_input('lista_fatture_dettaglio',$sql_0005,'','Dettaglio '.$tipoFattura, '');
            }else{
                echo '<div class="row"><div class="col-md-12 col-sm-12">';
                $sql_0006 = "SELECT
				IF(dataagg LIKE '0000-00-%' OR tipo LIKE 'Nota di Credito',CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"".BASE_URL."/moduli/fatture/modifica.php?tbl=lista_fatture_dettaglio&id=',lista_fatture_dettaglio.id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>'),'') AS 'fa-edit',
                dataagg,
                codice_preventivo AS 'Codice',
                (SELECT CONCAT(cognome, ' ', nome) FROM lista_professionisti WHERE id = id_professionista) AS 'fa-user',
                CONCAT('<b>',nome_prodotto,'</b>') AS 'Prodotto',
                note AS 'Descrizione Aggiuntiva', 
                prezzo_prodotto AS 'Prezzo',
                iva_prodotto AS 'Iva',
                quantita AS 'Qnt'
                FROM lista_fatture_dettaglio WHERE id_fattura=".$id;
                stampa_table_static_basic($sql_0006,'','Dettaglio '.$tipoFattura, '');
                echo '</div></div>';
                
            }
            //stampa_table_static_basic($sql_0001,'','Dettaglio Fattura', '');
            echo '<div class="row">';
			//echo '------------->'.$stile_form;
            echo '<center><a href="salva.php?tbl=lista_fatture_dettaglio&id='.$id.'&fn=nuovoFattureDettaglio" class="btn green-meadow">
                Aggiungi Voce
                <i class="fa fa-plus"></i>
                </a>
                <input class="btn green-sharp" value="Salva" type="submit"></center>';
            echo '</div><br>';
            
            
            //07.12.2017 AGGIUNTA POSSIBILITA DI ISCRIVERE AL CALENDARIO CORSI O ESAMI
             $sql_00001_prodotto = "SELECT id_prodotto, nome_prodotto, id_preventivo, id_professionista FROM lista_fatture_dettaglio WHERE id_fattura='" . $id . "' ORDER BY nome_prodotto";
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
                        CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"".BASE_URL."/moduli/corsi/salva.php?tbl=calendario_esami&idCalendario=',id,'&idProfessionista=".$idProfessionista."&idProdotto=',id_prodotto,'&idPreventivo=".$idPreventivo."&fn=iscriviCorsoUtente\" title=\"ISCRIVI CORSO\" alt=\"ISCRIVI CORSO\"><i class=\"fa fa-user-plus\"></i></a>') AS 'fa-user-plus', 
                        (SELECT IF(id_calendario_0<=0,CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"".BASE_URL."/moduli/corsi/salva.php?tbl=calendario_esami&idCalendario=',id,'&idProfessionista=".$idProfessionista."&idProdotto=',id_prodotto,'&idPreventivo=".$idPreventivo."&fn=iscriviCorsoUtente\" title=\"ISCRIVI CORSO\" alt=\"ISCRIVI CORSO\"><i class=\"fa fa-user-plus\"></i></a>'),
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
            
            $sql_00001_prodotto = "SELECT id_prodotto, nome_prodotto, id_preventivo, id_professionista FROM lista_fatture_dettaglio WHERE id_fattura='" . $id . "' ORDER BY nome_prodotto";
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
                        CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"".BASE_URL."/moduli/corsi/salva.php?tbl=calendario_esami&idCalendario=',id,'&idProfessionista=".$idProfessionista."&idProdotto=',id_prodotto,'&idPreventivo=".$idPreventivo."&fn=iscriviEsameUtente\" title=\"ISCRIVI ESAME\" alt=\"ISCRIVI ESAME\"><i class=\"fa fa-user-plus\"></i></a>') AS 'fa-user-plus', 
                        (SELECT IF(id_calendario_0<=0,CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"".BASE_URL."/moduli/corsi/salva.php?tbl=calendario_esami&idCalendario=',id,'&idProfessionista=".$idProfessionista."&idProdotto=',id_prodotto,'&idPreventivo=".$idPreventivo."&fn=iscriviEsameUtente\" title=\"ISCRIVI ESAME\" alt=\"ISCRIVI ESAME\"><i class=\"fa fa-user-plus\"></i></a>'),
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
                
            echo '</form>';
            
            
            //echo '<lI>$id = '.$id.'</li>';
            //echo '<lI>$tipoFattura = '.$tipoFattura.'</li>';
            //echo '<lI>$importoFattura = '.$importoFattura.'</li>';
            
            if($tipoFattura=='Nota di Credito'){
                $sql_0001_aggiorna = "UPDATE lista_costi, lista_fatture
                SET lista_costi.uscite = lista_fatture.importo,
                lista_costi.dataagg = NOW()
                WHERE lista_costi.id_fattura = '".$id."'
                AND  lista_fatture.id = '".$id."'";
                $rs_0001_aggiorna = $dblink->query($sql_0001_aggiorna);
            }
            
            
            echo '<div class="row"><div class="col-md-12 col-sm-12">';
            $sql_0001 = "SELECT   
            CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"modifica.php?tbl=lista_costi&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',
            CONCAT('<a class=\"btn btn-circle btn-icon-only red btn-outline\" href=\"cancella.php?tbl=lista_costi&id=',id,'\" title=\"ELIMINA\" alt=\"ELIMINA\"><i class=\"fa fa-trash\"></i></a>') AS 'fa-trash',
            dataagg,
            scrittore, entrate AS 'Importo &euro;', stato
            FROM lista_costi 
            WHERE 
            lista_costi.id_fattura = '" . $id."'
            OR
            lista_costi.id_fattura = (SELECT id_fattura_nota_credito FROM lista_fatture WHERE lista_fatture.id = '".$id."') ORDER BY id DESC";
            stampa_table_static_basic($sql_0001, '', 'Entrate / Uscite', 'green', 'fa fa-id-card');
            echo '</div></div>';

            if($id_fattura_nota_credito_trovata>0){
                echo '<div class="row"><div class="col-md-12 col-sm-12">';
                $sql_0004 = "SELECT 
                   CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"dettaglio.php?tbl=lista_fatture&id=',id,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',
                   IF(tipo LIKE 'Fattura',CONCAT('<span class=\"btn sbold uppercase btn-outline blue\">',tipo,'</span>'),CONCAT('<span class=\"btn sbold uppercase btn-outline red-thunderbird\">',tipo,'</span>')) AS 'Tipo',
                   CONCAT('<a class=\"btn btn-circle btn-icon-only red btn-outline\" href=\"printFatturaPDF.php?idFatt=',`id`,'&idA=',id_area,'\" TARGET=\"_BLANK\" title=\"STAMPA\" alt=\"STAMPA\"><i class=\"fa fa-file-pdf-o\"></i></a>') AS 'PDF',
                   CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"inviaFatt.php?idFatt=',id,'\" data-target=\"#ajax\" data-url=\"inviaFatt.php?idFatt=',id,'\" data-toggle=\"modal\" title=\"INVIA\" alt=\"INVIA\"><i class=\"fa fa-paper-plane\"></i></a>') AS 'Invia',
                   DATE(data_creazione) AS 'Creato il',
                   CONCAT('<b>',`codice`,'/', sezionale ,'</b>') AS codice,
                   CONCAT(importo,'<br><small>',imponibile,' +iva</small>') AS 'Importo &euro;',
                   stato
                   FROM lista_fatture WHERE id=".$id_fattura_nota_credito_trovata;
                stampa_table_static_basic($sql_0004,'','Note di Credito', 'red');
                echo '</div></div>';
            }

            echo '<BR><div class="row"><div class="col-md-12 col-sm-12">';
            if($fattura_controllo_stato>0){
                $sql_0007 = "SELECT  
                IF(id_azienda != '".$idAzienda."',CONCAT('<a href=\"salva.php?tbl=lista_fatture&id=$id&idAzienda=',id_azienda,'&fn=settaAziendaFattura\" title=\"SETTA\" alt=\"SETTA\"><button type=\"button\" class=\"btn green btn-warning mt-ladda-btn ladda-button btn-circle btn-icon-only\"><i class=\"fa fa-thumb-tack\"></i></button></a>'),'<button type=\"button\" class=\"btn green-jungle btn-warning mt-ladda-btn ladda-button btn-circle btn-icon-only\"><i class=\"fa fa-check\"></i></button>') AS 'fa-thumb-tack',
                (SELECT DISTINCT CONCAT('<H4>',ragione_sociale,'</H4>') FROM lista_aziende WHERE id = `matrice_aziende_professionisti`.id_azienda) AS 'Rag. Soc.',
                `matrice_aziende_professionisti`.stato,
                IF(id_azienda != '".$idAzienda."',CONCAT('<a class=\"btn btn-circle btn-icon-only red btn-outline\" href=\"cancella.php?tbl=matrice_aziende_professionisti&id=',id,'\" title=\"ELIMINA\" alt=\"ELIMINA\"><i class=\"fa fa-trash\"></i></a>'),'') as 'fa-trash'
                FROM `matrice_aziende_professionisti` WHERE  id_professionista='$idProfessionista'";
                stampa_table_static_basic($sql_0007, '', 'Aziende Disponibili <a name="aziendedisponibili"></a>', '');
            }else{
                $sql_0008 = "SELECT  

                (SELECT DISTINCT CONCAT('<H4>',ragione_sociale,'</H4>') FROM lista_aziende WHERE id = `matrice_aziende_professionisti`.id_azienda) AS 'Rag. Soc.',
                `matrice_aziende_professionisti`.stato,
                IF(id_azienda != '".$idAzienda."',CONCAT('<a class=\"btn btn-circle btn-icon-only red btn-outline\" href=\"cancella.php?tbl=matrice_aziende_professionisti&id=',id,'\" title=\"ELIMINA\" alt=\"ELIMINA\"><i class=\"fa fa-trash\"></i></a>'),'') as 'fa-trash'
                FROM `matrice_aziende_professionisti` WHERE  id_professionista='$idProfessionista'";
                stampa_table_static_basic($sql_0008, '', 'Aziende Disponibili <a name="aziendedisponibili"></a>', '');
            }
            echo '<center><a href="'.BASE_URL.'/moduli/anagrafiche/dettaglio_tab.php?tbl=calendario&id='.$idCalendarioRichiesta.'#tab_azienda" class="btn btn-icon green-meadow"><i class="fa fa-plus"></i> Aggiungi Azienda</a></center>';
            echo '</div></div>';
            
            
            $sql_007_010 = "UPDATE lista_password, lista_professionisti 
                            SET lista_password.id_professionista = lista_professionisti.id
                            WHERE lista_professionisti.email = lista_password.email AND lista_password.id_professionista <= 0";
            $dblink->query($sql_007_010);
        
            /*
            echo '<BR><div class="row"><div class="col-md-12 col-sm-12">';
            $sql_00011 = "SELECT * FROM lista_iscrizioni WHERE id_fattura=".$id;
            stampa_table_static_basic($sql_00011, '', 'test iscrizioni', 'red');
            echo '</div></div>';
            */
        break;
    }
}

?>
