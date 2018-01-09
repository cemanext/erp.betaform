<?php

/** FUNZIONI DI CROCCO */
function Stampa_HTML_index_Iscrizioni($tabella){
    global $table_listaIscrizioni, $table_listaIscrizioniPartecipanti, $table_listaIscrizioniInCorso, 
           $table_listaIscrizioniInAttesa, $table_listaIscrizioniCompletati, $table_listaIscrizioniPartecipantiCompletatiPagati, $table_listaIscrizioniPartecipantiCompletatiNonPagati, $table_listaIscrizioniConfigurazioni,
           $table_listaIscrizioniControlloDoppi, $table_listaIscrizioniPartecipantiCompletati, $dblink;
    
    $sql_0007 = "UPDATE lista_iscrizioni, lista_corsi
    SET lista_iscrizioni.nome_corso = lista_corsi.nome_prodotto
    WHERE lista_iscrizioni.id_corso = lista_corsi.id
    AND LENGTH(lista_iscrizioni.nome_corso)<=0";
    $rs_00007 = $dblink->query($sql_0007);

    $sql_0007 = "UPDATE lista_iscrizioni, lista_classi
    SET lista_iscrizioni.nome_classe = lista_classi.nome
    WHERE lista_iscrizioni.id_classe = lista_classi.id
    AND LENGTH(lista_iscrizioni.nome_classe)<=0";
    $rs_00007 = $dblink->query($sql_0007);

    $sql_0007 = "UPDATE lista_iscrizioni, lista_professionisti
    SET lista_iscrizioni.cognome_nome_professionista = CONCAT(lista_professionisti.cognome,' ', lista_professionisti.nome)
    WHERE lista_iscrizioni.id_professionista = lista_professionisti.id";
    $rs_00007 = $dblink->query($sql_0007);

    switch($tabella){
    
        case 'table_listaIscrizioniCompletati':
             $tabella = "lista_iscrizioni";
            $campi_visualizzati = $table_listaIscrizioniCompletati['index']['campi'];
            $where = $table_listaIscrizioniCompletati['index']['where'];
            $ordine = $table_listaIscrizioniCompletati['index']['order'];
            $titolo = 'Elenco Iscrizioni - Completati';
            $sql_0001 = "SELECT ".$campi_visualizzati." FROM ".$tabella." WHERE $where $ordine LIMIT 1";
            $sql_0001_1 = "SELECT COUNT(id) AS conto FROM ".$tabella." WHERE $where $ordine";
            $conto = $dblink->get_field($sql_0001_1);
            stampa_table_datatables_ajax($sql_0001, "datatable_ajax", $titolo.' ['.$conto.' Record]', '', '', false);
        break;
        
        case 'table_listaIscrizioniCompletatiPagati':
             $tabella = "lista_iscrizioni";
            $campi_visualizzati = $table_listaIscrizioniPartecipantiCompletatiPagati['index']['campi'];
            $where = $table_listaIscrizioniPartecipantiCompletatiPagati['index']['where'];
            $ordine = $table_listaIscrizioniPartecipantiCompletatiPagati['index']['order'];
            $titolo = 'Elenco Attestati - Completati e Pagati';
            $sql_0001 = "SELECT ".$campi_visualizzati." FROM ".$tabella." WHERE $where $ordine LIMIT 1";
            $sql_0001_1 = "SELECT COUNT(id) AS conto FROM ".$tabella." WHERE $where $ordine";
            $conto = $dblink->get_field($sql_0001_1);
            stampa_table_datatables_ajax($sql_0001, "datatable_ajax", $titolo.' ['.$conto.' Record]', '', '', false);
        break;
        
        case 'table_listaIscrizioniCompletatiNonPagati':
             $tabella = "lista_iscrizioni";
            $campi_visualizzati = $table_listaIscrizioniPartecipantiCompletatiNonPagati['index']['campi'];
            $where = $table_listaIscrizioniPartecipantiCompletatiNonPagati['index']['where'];
            $ordine = $table_listaIscrizioniPartecipantiCompletatiNonPagati['index']['order'];
            $titolo = 'Elenco Attestati - Completati e NON Pagati';
            $sql_0001 = "SELECT ".$campi_visualizzati." FROM ".$tabella." WHERE $where $ordine LIMIT 1";
            $sql_0001_1 = "SELECT COUNT(id) AS conto FROM ".$tabella." WHERE $where $ordine";
            $conto = $dblink->get_field($sql_0001_1);
            stampa_table_datatables_ajax($sql_0001, "datatable_ajax", $titolo.' ['.$conto.' Record]', '', 'red-thunderbird', false);
        break;
        
        case 'table_listaIscrizioniInAttesa':
             $tabella = "lista_iscrizioni";
            $campi_visualizzati = $table_listaIscrizioniInAttesa['index']['campi'];
            $where = $table_listaIscrizioniInAttesa['index']['where'];
            $ordine = $table_listaIscrizioniInAttesa['index']['order'];
            $titolo = 'Elenco Iscrizioni - In Attesa';
            $sql_0001 = "SELECT ".$campi_visualizzati." FROM ".$tabella." WHERE $where $ordine LIMIT 1";
            $sql_0001_1 = "SELECT COUNT(id) AS conto FROM ".$tabella." WHERE $where $ordine";
            $conto = $dblink->get_field($sql_0001_1);
            stampa_table_datatables_ajax($sql_0001, "datatable_ajax", $titolo.' ['.$conto.' Record]', '', '', false);
        break;
        
        case 'table_listaIscrizioniInCorso':
             $tabella = "lista_iscrizioni";
            $campi_visualizzati = $table_listaIscrizioniInCorso['index']['campi'];
            $where = $table_listaIscrizioniInCorso['index']['where'];
            $ordine = $table_listaIscrizioniInCorso['index']['order'];
            $titolo = 'Elenco Iscrizioni - In Corso';
            $sql_0001 = "SELECT ".$campi_visualizzati." FROM ".$tabella." WHERE $where $ordine LIMIT 1";
            $sql_0001_1 = "SELECT COUNT(id) AS conto FROM ".$tabella." WHERE $where $ordine";
            $conto = $dblink->get_field($sql_0001_1);
            stampa_table_datatables_ajax($sql_0001, "datatable_ajax", $titolo.' ['.$conto.' Record]', '', '', false);
        break;
        
        case 'lista_iscrizioni':
            $tabella = "lista_iscrizioni";
            $campi_visualizzati = $table_listaIscrizioni['index']['campi'];
            $where = $table_listaIscrizioni['index']['where']. " AND stato NOT LIKE '%Configurazione%' ";
            $ordine = $table_listaIscrizioni['index']['order'];
            $titolo = 'Elenco Iscrizioni';
            $sql_0001 = "SELECT ".$campi_visualizzati." FROM ".$tabella." WHERE $where $ordine LIMIT 1";
            $sql_0001_1 = "SELECT COUNT(id) AS conto FROM ".$tabella." WHERE $where $ordine";
            $conto = $dblink->get_field($sql_0001_1);
            stampa_table_datatables_ajax($sql_0001, "datatable_ajax", $titolo.' ['.$conto.' Record]', '', '', false);
            //stampa_table_datatables_responsive($sql_0001, $titolo, 'tabella_base');
        break;
    
        case 'lista_iscrizioni_configurazioni':
            $tabella = "lista_iscrizioni";
            $campi_visualizzati = $table_listaIscrizioniConfigurazioni['index']['campi'].", 'Giorni Alla Scadenza'";
            $where = $table_listaIscrizioniConfigurazioni['index']['where'];
            $ordine = $table_listaIscrizioniConfigurazioni['index']['order'];
            $titolo = 'Elenco Configurazioni Partecipanti';
            $sql_0001 = "SELECT ".$campi_visualizzati." FROM ".$tabella." WHERE $where $ordine LIMIT 1";
            stampa_table_datatables_ajax($sql_0001, "datatable_ajax", $titolo, '', '', false);
            //stampa_table_datatables_responsive($sql_0001, $titolo, 'tabella_base');
        break;
    
        case 'lista_iscrizioni_controllo_doppi':
            $tabella = "lista_iscrizioni";
            $campi_visualizzati = $table_listaIscrizioniControlloDoppi['index']['campi'];
            $where = $table_listaIscrizioniControlloDoppi['index']['where'];
            $ordine = $table_listaIscrizioniControlloDoppi['index']['order'];
            $titolo = 'Elenco Configurazioni Partecipanti';
            $sql_0001 = "SELECT ".$campi_visualizzati." FROM ".$tabella." WHERE $where $ordine LIMIT 1";
            stampa_table_datatables_ajax($sql_0001, "datatable_ajax", $titolo, '', '', false);
            //stampa_table_datatables_responsive($sql_0001, $titolo, 'tabella_base');
        break;

        case 'lista_iscrizioni_partecipanti':
            $tabella = "lista_iscrizioni";
            $campi_visualizzati = $table_listaIscrizioniPartecipanti['index']['campi'];
            $where = $table_listaIscrizioniPartecipanti['index']['where'];
            $ordine = $table_listaIscrizioniPartecipanti['index']['order'];
            $titolo = 'Elenco Partecipanti';
            $sql_0001 = "SELECT ".$campi_visualizzati." FROM ".$tabella." WHERE $where $ordine LIMIT 1";
            stampa_table_datatables_ajax($sql_0001, "datatable_ajax", $titolo, '', '', false);
            //stampa_table_datatables_responsive($sql_0001, $titolo, 'tabella_base');
        break;
    
        case 'lista_iscrizioni_partecipanti_completati':
            $tabella = "lista_iscrizioni";
            $campi_visualizzati = $table_listaIscrizioniPartecipantiCompletati['index']['campi'];
            $where = $table_listaIscrizioniPartecipantiCompletati['index']['where'];
            $ordine = $table_listaIscrizioniPartecipantiCompletati['index']['order'];
            $titolo = 'Elenco Corsi Completati per Partecipante';
            $sql_0001 = "SELECT ".$campi_visualizzati." FROM ".$tabella." WHERE $where $ordine LIMIT 1";
            stampa_table_datatables_ajax($sql_0001, "datatable_ajax", $titolo, '', '', false);
            //stampa_table_datatables_responsive($sql_0001, $titolo, 'tabella_base');
        break;
    }
}

function Stampa_HTML_Dettaglio_Iscrizioni($tabella, $id) {
    global $dblink;
    
      switch ($tabella) {
        case 'lista_iscrizioni':
		if(isset($_GET['whrStato']) and $_GET['whrStato']>0){
			switch($_GET['whrStato']){
				case 1:
					$whrStato = " AND stato='In Corso'";
					$whrStatoTitolo = " - In Corso";
                                        $whrOrderBy = "dataagg DESC";
				break;
				
				case 2:
					$whrStato = " AND stato='In Attesa'";
					$whrStatoTitolo = " - In Attesa";
                                        $whrOrderBy = "dataagg DESC";
				break;
				
				case 3:
					$whrStato = " AND stato='Completato' AND data_completamento>=DATE_SUB(CURDATE(), INTERVAL 270 DAY)";
					$whrStatoTitolo = " - Completati Ultimi 270 Giorni";
                                        $whrOrderBy = "data_completamento DESC";
				break;
			}
		}else{
		    $whrOrderBy = "dataagg DESC";
		}
            echo '<div class="row"><div class="col-md-12 col-sm-12">';
            $sql_0001 = "SELECT CONCAT('<H3>',nome_prodotto,'</H3>') AS 'Corso'
            FROM lista_corsi
            WHERE id='".$id."'";
            stampa_table_static_basic($sql_0001, '', 'CORSO', 'green-haze');
            echo '</div></div>';
            //CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"modifica.php?tbl=lista_iscrizioni&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',

            echo '<div class="row"><div class="col-md-12 col-sm-12">';
            $sql_0001 = "SELECT
            CONCAT('<a class=\"btn btn-circle btn-icon-only yellow btn-outline\" href=\"dettaglio.php?tbl=lista_iscrizioni_partecipanti&id=',id,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',
            IF(id_professionista>0,(SELECT CONCAT('<h3><b>',cognome, ' ', nome,'</b></h3><small>', IF(id_classe>0,(SELECT nome FROM lista_classi WHERE id = id_classe LIMIT 1),'') ,'</small>')  FROM lista_professionisti WHERE id = id_professionista LIMIT 1), CONCAT('<i class=\"fa fa-user-times btn btn-icon-only red-flamingo btn-outline\"></i><br>', cognome_nome_professionista,'<br><small>',IF(id_classe>0,(SELECT nome FROM lista_classi WHERE id = id_classe LIMIT 1),''),'</small>')) AS 'Partecipante',
            (SELECT nome FROM lista_classi WHERE id = id_classe LIMIT 1) AS 'Classe',
            IF(stato='Completato',data_completamento,CONCAT('<small>dal ',data_inizio_iscrizione,' al ',data_fine_iscrizione,'</small>')) AS 'Validit&agrave;', stato,
            CONCAT('<span class=\"btn sbold uppercase btn-circle btn-outline green-sharp\">',avanzamento_completamento,'%</span>') AS 'Perc.',
            (SELECT lista_professionisti.email FROM lista_professionisti WHERE lista_professionisti.id = lista_iscrizioni.id_professionista ) AS 'E-Mail',
            (SELECT IF(LENGTH(cellulare)>3, cellulare, telefono) AS numero_telefono FROM lista_professionisti WHERE lista_professionisti.id = lista_iscrizioni.id_professionista ) AS 'Telefono',
            IF(stato='Completato',CONCAT('<a class=\"btn btn-circle btn-icon-only red btn-outline\" href=\"".BASE_URL."/moduli/corsi/printAttestatoPDF.php?idIscrizione=',id,'\" title=\"STAMPA\" alt=\"STAMPA\" target=\"_blank\"><i class=\"fa fa-file-pdf-o\"></i></a>'),'') AS 'fa-file-pdf-o'
            FROM lista_iscrizioni
            WHERE id_corso='".$id."' $whrStato ORDER BY $whrOrderBy";
            stampa_table_datatables_responsive($sql_0001, 'Iscritti '.$whrStatoTitolo, 'tabella_base1', '');
            echo '</div></div>';


            echo '<div class="row"><div class="col-md-12 col-sm-12">';
            $sql_0001 = "SELECT CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"modifica.php?tbl=calendario&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',
            data, ora, oggetto, stato
            FROM calendario
            WHERE id_corso=" . $id."
            AND etichetta LIKE 'Calendario Esami'
            ORDER BY data DESC, ora ASC";
            stampa_table_static_basic($sql_0001, '', 'Esami', 'blue-steel');
            echo '</div></div>';

        break;

        case 'lista_iscrizioni_partecipanti';
            /*
                echo '<div class="row"><div class="col-md-12 col-sm-12">';
                $sql_0001 = "SELECT (select distinct codice_esterno from lista_prodotti inner join lista_corsi on lista_prodotti.id=lista_corsi.id_prodotto where  lista_corsi.id = lista_iscrizioni.id_corso) as id_corso_moodle,
                (select id_moodle_user FROM lista_professionisti WHERE id = id_professionista) AS id_utente_moodle
                FROM `lista_iscrizioni` WHERE id=" . $id;
                stampa_table_static_basic($sql_0001, '', 'Dati Moodle', 'red');
                echo '</div></div>';
            */

            $sql_aggiorna_id_utente_moodle_iscrizioni = "UPDATE lista_iscrizioni, lista_professionisti 
            SET lista_iscrizioni.id_utente_moodle = lista_professionisti.id_moodle_user
            WHERE lista_iscrizioni.id_professionista = lista_professionisti.id
            AND lista_iscrizioni.id_utente_moodle<=0";
            $rs_aggiorna_id_utente_moodle_iscrizioni = $dblink->query($sql_aggiorna_id_utente_moodle_iscrizioni);

            $sql_0006 = "SELECT `lista_iscrizioni`.*,
            (SELECT codice_esterno FROM lista_prodotti INNER JOIN lista_corsi ON lista_prodotti.id=lista_corsi.id_prodotto WHERE lista_corsi.id = lista_iscrizioni.id_corso LIMIT 1) as id_corso_moodle,
            IF(id_professionista>0,(SELECT id_moodle_user FROM lista_professionisti WHERE id = id_professionista LIMIT 1),id_utente_moodle) AS id_utente_moodle,
            UNIX_TIMESTAMP(data_fine_iscrizione) AS data_scadenza_corso_timestamp,
            (SELECT nome FROM lista_classi WHERE id = id_classe LIMIT 1) AS 'nome_classe'
            FROM `lista_iscrizioni` WHERE id='".$id."'";
            $rs_00006 = $dblink->get_results($sql_0006);
            if (!empty($rs_00006)) {
                foreach ($rs_00006 as $row_00006) {
                    $id_moodle = $row_00006['id_corso_moodle'];
                    $id_corso_moodle = $row_00006['id_corso_moodle'];
                    $idUtenteMoodle = $row_00006['id_utente_moodle'];
                    $id_corso = $row_00006['id_corso'];
                    $id_prodotto = $row_00006['id_prodotto'];
                    $id_professionista = $row_00006['id_professionista'];
                    $NomeClasse = $row_00006['nome_classe'];
                    $idClasse = $row_00006['id_classe'];
                    $DataFineIscrizione = GiraDataItaBarra($row_00006['data_fine_iscrizione']);
                    $data_scadenza_corso_timestamp = $row_00006['data_scadenza_corso_timestamp'];
                    $idFattura= $row_00006['id_fattura'];
                    $tipoAbbonamento = $row_00006['abbonamento'];
                    $dataCompletamento = $row_00006['data_completamento'];
                    $statoIscrizione = $row_00006['stato'];
                }
            }

            //echo '<h1>$tipoAbbonamento = '.$tipoAbbonamento.'</h1>';
            //echo '<h1>$idUtenteMoodle = '.$idUtenteMoodle.'</h1>';
            //echo '<h1>$id_corso_moodle = '.$id_corso_moodle.'</h1>';
            //echo '<h1>$statoIscrizione = '.$statoIscrizione.'</h1>';


            $percentuale_corso_utente = recupero_percentuale_avanzamento_corso_utente($idUtenteMoodle, $id_corso_moodle, true);
            $sql_007_aggionro_percentuale = "UPDATE lista_iscrizioni
            SET `avanzamento_completamento` = '".$percentuale_corso_utente."'
            WHERE id=".$id." AND ((stato LIKE 'In Attesa' OR stato LIKE 'In Corso') OR (stato LIKE 'Completato' AND avanzamento_completamento < 100))";
            $rs_007_aggionro_percentuale = $dblink->query($sql_007_aggionro_percentuale);

            echo '<div class="row"><div class="col-md-12 col-sm-12">';
            /*
            $sql_0001 = "SELECT
            CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"modifica.php?tbl=lista_iscrizioni_partecipanti&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',
            IF(abbonamento=1,'<span class=\"btn sbold uppercase btn-outline blue-steel\">Abbonamento</span>', '<span class=\"btn sbold uppercase btn-outline green-seagreen\">Singolo Corso</span>') AS 'Tipo',
            (SELECT DISTINCT CONCAT('<h3>',cognome, ' ', nome,'</h3>')  FROM lista_professionisti WHERE id = id_professionista) AS 'Partecipante',
            (SELECT DISTINCT nome FROM lista_classi WHERE id = id_classe) AS 'Classe',
            (SELECT DISTINCT CONCAT('<H4>',nome_prodotto,'</H4>') FROM lista_corsi WHERE id = id_corso) AS 'Corso',
            CONCAT('<small>dal ',data_inizio_iscrizione,' al ',data_fine_iscrizione,'</small>') AS 'Validit&agrave;',
            CONCAT('<small>dal ',data_inizio,' al ',data_fine,'</small>') AS 'In Corso',
            stato,
            CONCAT('<h4>',avanzamento_completamento,'%</h4>') AS 'Perc.'
            FROM lista_iscrizioni
            WHERE id=" . $id." ORDER BY data_fine  ASC";
            */
            
            /*
            IF(abbonamento=1,
                IF(stato='Configurazione',
                IF(data_fine_iscrizione>=CURDATE(),CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"modifica.php?tbl=lista_iscrizioni_partecipanti&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>'),'')
                ,''
                ) 
            ,IF(stato!='Completato',
                IF(data_fine_iscrizione>=CURDATE(),CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"modifica.php?tbl=lista_iscrizioni_partecipanti&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>'),'')
                ,''
            )
            ) AS 'fa-edit',
            
             */

            $sql_0001 = "SELECT
            IF(stato NOT LIKE '%Scadu%',
                IF(data_fine_iscrizione>=CURDATE(),CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"modifica.php?tbl=lista_iscrizioni_partecipanti&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>'),'')
                ,''
            ) AS 'fa-edit',
            IF(abbonamento=1,'<span class=\"btn sbold uppercase btn-outline blue-steel\">Abbonamento</span>',
                IF(abbonamento>1,'<span class=\"btn sbold uppercase btn-outline blue-hoki\">Pacchetto</span>',
                '<span class=\"btn sbold uppercase btn-outline green-seagreen\">Singolo Corso</span>')
            ) AS 'Tipo',
            IF(id_professionista>0,(SELECT CONCAT('<h3><b>',cognome, ' ', nome,'</b></h3><small>', IF(id_classe>0,(SELECT nome FROM lista_classi WHERE id = id_classe LIMIT 1),'') ,'</small>')  FROM lista_professionisti WHERE id = id_professionista LIMIT 1), CONCAT('<i class=\"fa fa-user-times btn btn-icon-only red-flamingo btn-outline\"></i><br>', cognome_nome_professionista,'<br><small>',IF(id_classe>0,(SELECT nome FROM lista_classi WHERE id = id_classe LIMIT 1),''),'</small>')) AS 'Partecipante',
            (SELECT CONCAT('<H3>',nome_prodotto,'</H3>') FROM lista_corsi WHERE id = id_corso LIMIT 1) AS 'Corso',
            stato,
            CONCAT('<span class=\"btn sbold uppercase btn-circle btn-outline green-sharp\">',avanzamento_completamento,'%</span>') AS 'Perc.'
            FROM lista_iscrizioni
            WHERE id=" . $id." ORDER BY data_fine  ASC";
            stampa_table_static_basic($sql_0001, '', 'Partecipante', '');
            if($tipoAbbonamento==1){
                echo '</div></div>';
                echo '<div class="row">';
                echo '<div class="col-md-6 col-sm-12">';
                $sql_0001 = "SELECT data_inizio_iscrizione AS 'Attivazione', data_fine_iscrizione AS 'Scadenza'
                FROM lista_iscrizioni WHERE lista_iscrizioni.id='".$id."'";
                stampa_table_static_basic($sql_0001, '', 'Durata Abbonamento', '');
                echo '</div>';
                 echo '<div class="col-md-6 col-sm-12">';
                 $sql_0001 = "SELECT data_inizio AS 'Inizio', data_fine AS 'Scadenza', data_completamento AS 'Completamento'
                FROM lista_iscrizioni WHERE lista_iscrizioni.id='".$id."'";
                stampa_table_static_basic($sql_0001, '', 'Dettaglio Corso', '');
                echo '</div>';
                echo '</div>';
            }

            $dataCompletamentoTime = strtotime($dataCompletamento);
            $dataCompletamentoStartTime = strtotime("2017-10-18");
            if($dataCompletamentoTime >= $dataCompletamentoStartTime){
                echo '<div class="row"><div class="col-md-12 col-sm-12">';
                if($tipoAbbonamento==1){
                    //CONCAT('<a class=\"btn btn-circle btn-icon-only red btn-outline\" href=\"#####.php?tbl=lista_documenti&id=',id_professionista,'\" title=\"ATTESTATO\" alt=\"ATTESTATO\"><i class=\"fa fa fa-file-pdf-o\"></i></a>') AS 'fa-file-text',
                    $sql_0001 = "SELECT
                    IF(lista_iscrizioni.id_classe>0,(SELECT nome FROM lista_classi WHERE id = lista_iscrizioni.id_classe LIMIT 1),'') AS 'Classe',
                    CONCAT('<a class=\"btn btn-circle btn-icon-only red btn-outline\" href=\"".BASE_URL."/moduli/corsi/printAttestatoPDF.php?idIscrizione=',lista_iscrizioni.id,'\" target=\"_blank\" title=\"ATTESTATO\" alt=\"ATTESTATO\"><i class=\"fa fa fa-file-pdf-o\"></i></a>') AS 'fa-file-pdf-o',
                    lista_corsi_configurazioni.`crediti`,
                    CONCAT(lista_corsi_configurazioni.`avanzamento`,'%') AS 'Perc. Completamento',
                    IF(lista_iscrizioni.avanzamento_completamento >= lista_corsi_configurazioni.`avanzamento`,'<i class=\"fa fa-thumbs-o-up\"></i>','<i class=\"fa fa-thumbs-o-down\"></i>')  AS 'fa-graduation-cap'
                    FROM lista_corsi_configurazioni INNER JOIN lista_iscrizioni
                    ON lista_corsi_configurazioni.id_corso = lista_iscrizioni.id_corso
                    WHERE lista_iscrizioni.id='".$id."' 
                    AND (lista_iscrizioni.id_classe = lista_corsi_configurazioni.id_classe
                    OR lista_corsi_configurazioni.titolo LIKE 'Base')
                    ORDER BY lista_corsi_configurazioni.id_classe DESC, lista_iscrizioni.data_fine ASC LIMIT 1";
                }else{
                    $sql_0001 = "SELECT
                   lista_iscrizioni.data_inizio_iscrizione AS 'Attivazione', 
                   lista_iscrizioni.data_fine_iscrizione AS 'Scadenza', 
                   lista_iscrizioni.data_inizio AS 'Inizio', 
                   lista_iscrizioni.data_completamento AS 'Completato'
                   FROM lista_iscrizioni
                   WHERE lista_iscrizioni.id='".$id."'";
                }
                stampa_table_static_basic($sql_0001, '', 'Attestati', '');
                echo '</div></div>';
            }

            if(($tipoAbbonamento==1 && $statoIscrizione=='Configurazione') || ($statoIscrizione!="Scaduto" && $statoIscrizione!='Completato' && $statoIscrizione!='Scaduto e Disattivato')){
            echo '<div class="row"><div class="col-md-12 col-sm-12">';

                if($tipoAbbonamento==1){
                    
                    list($idIscConfig,$idFatturaConfig,$idUtenteMoodleConfig,$DataFineIscrizioneConfig,$NomeClasseConfig) = $dblink->get_row("SELECT `lista_iscrizioni`.id, `lista_iscrizioni`.id_fattura,
                        IF(id_professionista>0,(SELECT id_moodle_user FROM lista_professionisti WHERE id = id_professionista LIMIT 1),id_utente_moodle) AS id_utente_moodle,
                        data_fine_iscrizione AS data_scadenza_corso_timestamp,
                        (SELECT nome FROM lista_classi WHERE id = id_classe LIMIT 1) AS 'nome_classe'
                        FROM `lista_iscrizioni` WHERE abbonamento = '1' AND (stato = 'Configurazione' OR stato = 'Abbonamento Disabilitato') AND id_professionista = '$id_professionista' AND id_classe = '$idClasse'");
                    
                    $DataFineIscrizioneConfig = GiraDataItaBarra($DataFineIscrizioneConfig);
                    
                    $sql_0001 = "SELECT
                    CONCAT('<a href=\"".BASE_URL."/moduli/iscrizioni/salva.php?idIscrizione=".$idIscConfig."&idFattura=".$idFatturaConfig."&idUtenteMoodle=".$idUtenteMoodleConfig."&NomeClasse=".$NomeClasseConfig."&fn=annullaAbbonamentoMoodle\"><span class=\"btn sbold uppercase btn-outline red\">Disabilita Abbonamento</span></a>') AS 'Disabilita Abbonamento',
                    CONCAT('<a href=\"".BASE_URL."/moduli/iscrizioni/salva.php?idIscrizione=".$idIscConfig."&idFattura=".$idFatturaConfig."&idUtenteMoodle=".$idUtenteMoodleConfig."&NomeClasse=".$NomeClasseConfig."&DataFineIscrizione=".$DataFineIscrizioneConfig."&fn=riabilitaAbbonamentoMoodle\"><span class=\"btn sbold uppercase btn-outline green-jungle\">Abilita Abbonamento</span></a>') AS 'Abilita Abbonamento',
                    CONCAT('<a href=\"".BASE_URL."/moduli/iscrizioni/salva.php?idIscrizione=".$idIscConfig."&idFattura=".$idFatturaConfig."&idUtenteMoodle=".$idUtenteMoodleConfig."&NomeClasse=".$NomeClasseConfig."&DataFineIscrizione=".$DataFineIscrizioneConfig."&fn=riabilitaAbbonamentoMoodle\"><span class=\"btn sbold uppercase btn-outline green\">Proroga Abbonamento</span></a>') AS 'Proroga Abbonamento'
                    FROM lista_iscrizioni WHERE id='".$id."' LIMIT 1";
                    /*
                    CONCAT('<a href=\"".BASE_URL."/moduli/iscrizioni/salva.php?idUtenteMoodle=".$idUtenteMoodle."&fn=disabilitaUtente\"><span class=\"btn sbold uppercase btn-outline red\">Disabilita Utente</span></a>') AS 'Disabilita Utente', 
                    CONCAT('<a href=\"".BASE_URL."/moduli/iscrizioni/salva.php?idUtenteMoodle=".$idUtenteMoodle."&fn=abilitaUtente\"><span class=\"btn sbold uppercase btn-outline green-jungle\">Abilita Utente</span></a>') AS 'Abilita Utente'
                    */
                }else{
                    $sql_0001 = "SELECT
                    CONCAT('<a href=\"".BASE_URL."/moduli/iscrizioni/salva.php?idIscrizione=".$id."&idUtenteMoodle=".$idUtenteMoodle."&idCorso=".$id_corso_moodle."&fn=disabilitaCorso\"><span class=\"btn sbold uppercase btn-outline red\">Disabilita Corso</span></a>') AS 'Disabilita Corso',
                    CONCAT('<a href=\"".BASE_URL."/moduli/iscrizioni/salva.php?idIscrizione=".$id."&idUtenteMoodle=".$idUtenteMoodle."&idCorso=".$id_corso_moodle."&dataScadenza=".$data_scadenza_corso_timestamp."&fn=abilitaProrogaCorso\"><span class=\"btn sbold uppercase btn-outline green-jungle\">Abilita Corso</span></a>') AS 'Abilita Corso',
                    CONCAT('<a href=\"".BASE_URL."/moduli/iscrizioni/salva.php?idIscrizione=".$id."&idUtenteMoodle=".$idUtenteMoodle."&idCorso=".$id_corso_moodle."&dataScadenza=".$data_scadenza_corso_timestamp."&fn=abilitaProrogaCorso\"><span class=\"btn sbold uppercase btn-outline green\">Proroga Corso</span></a>') AS 'Proroga Corso'
                    FROM lista_iscrizioni WHERE id='".$id."' LIMIT 1";
                }
                /*
                CONCAT('<a href=\"".BASE_URL."/moduli/iscrizioni/salva.php?idUtenteMoodle=".$idUtenteMoodle."&fn=disabilitaUtente\"><span class=\"btn sbold uppercase btn-outline red\">Disabilita Utente</span></a>') AS 'Disabilita Utente', 
                CONCAT('<a href=\"".BASE_URL."/moduli/iscrizioni/salva.php?idUtenteMoodle=".$idUtenteMoodle."&fn=abilitaUtente\"><span class=\"btn sbold uppercase btn-outline green-jungle\">Abilita Utente</span></a>') AS 'Abilita Utente'
                */
                stampa_table_static_basic($sql_0001, '', 'Configurazioni MOODLE', 'red');
                echo '</div></div>';
            }
            echo '<div class="row"><div class="col-lg-6 col-md-6 col-sm-12" style="display:none; visibility:hidden;">';
             $sql_0001 = "SELECT
             CONCAT('<a class=\"btn btn-circle btn-icon-only red btn-outline\" href=\"#####.php?tbl=lista_documenti&id=',id_professionista,'\" title=\"ATTESTATO\" alt=\"ATTESTATO\"><i class=\"fa fa fa-file-pdf-o\"></i></a>') AS 'fa-file-pdf-o',
            CONCAT('<a class=\"btn btn-circle btn-icon-only red btn-outline\" href=\"#####.php?tbl=lista_documenti&id=',id_professionista,'\" title=\"ATTESTATO\" alt=\"ATTESTATO\"><i class=\"fa fa fa-file-pdf-o\"></i></a>') AS 'fa-file-text',
            lista_corsi_configurazioni.`crediti`,
            lista_corsi_configurazioni.`durata_corso`,
            lista_corsi_configurazioni.`avanzamento`
            FROM lista_corsi_configurazioni INNER JOIN lista_iscrizioni
            ON lista_corsi_configurazioni.id_corso = lista_iscrizioni.id_corso
            WHERE lista_iscrizioni.id='".$id."'
            AND lista_corsi_configurazioni.id_classe = lista_iscrizioni.id_classe
            ORDER BY lista_corsi_configurazioni.data_fine  ASC";
            stampa_table_static_basic($sql_0001, '', 'Configurazioni', '');
            echo '</div>';
            echo '<div class="col-lg-6 col-md-6 col-sm-12" style="display:none; visibility:hidden;">';
            echo '<div class="portlet box red">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-cogs"></i>Configurazioni MOODLE</div>
                        <div class="tools">
                        </div>
                  </div>
                  <div class="portlet-body">
                  <center>';

                  if($tipoAbbonamento==1){
                  echo'<a href="'.BASE_URL.'/moduli/iscrizioni/salva.php?idFattura='.$idFattura.'&idUtenteMoodle='.$idUtenteMoodle.'&NomeClasse='.$NomeClasse.'&fn=annullaAbbonamentoMoodle" class="btn red"><i class="fa fa-exclamation-triangle"></i> Disabilita Abbonamento</a>
                      <a href="'.BASE_URL.'/moduli/iscrizioni/salva.php?idFattura='.$idFattura.'&idUtenteMoodle='.$idUtenteMoodle.'&NomeClasse='.$NomeClasse.'&DataFineIscrizione='.$DataFineIscrizione.'&fn=riabilitaAbbonamentoMoodle" class="btn green-jungle"><i class="fa fa-exclamation-triangle"></i> Abilita Abbonamento</a>
                      <a href="'.BASE_URL.'/moduli/iscrizioni/salva.php?idFattura='.$idFattura.'&idUtenteMoodle='.$idUtenteMoodle.'&NomeClasse='.$NomeClasse.'&DataFineIscrizione='.$DataFineIscrizione.'&fn=riabilitaAbbonamentoMoodle" class="btn green"><i class="fa fa-exclamation-triangle"></i> Proroga Abbonamento</a>';
                }else{
                echo '<a href="'.BASE_URL.'/moduli/iscrizioni/salva.php?idIscrizione='.$id.'&idUtenteMoodle='.$idUtenteMoodle.'&idCorso='.$id_corso_moodle.'&fn=disabilitaCorso" class="btn red"><i class="fa fa-exclamation-triangle"></i> Disabilita Corso</a>
                      <a href="'.BASE_URL.'/moduli/iscrizioni/salva.php?idIscrizione='.$id.'&idUtenteMoodle='.$idUtenteMoodle.'&idCorso='.$id_corso_moodle.'&dataScadenza='.$data_scadenza_corso_timestamp.'&fn=abilitaProrogaCorso" class="btn green-jungle"><i class="fa fa-exclamation-triangle"></i> Abilita Corso</a>
                      <a href="'.BASE_URL.'/moduli/iscrizioni/salva.php?idIscrizione='.$id.'&idUtenteMoodle='.$idUtenteMoodle.'&idCorso='.$id_corso_moodle.'&dataScadenza='.$data_scadenza_corso_timestamp.'&fn=abilitaProrogaCorso" class="btn green"><i class="fa fa-exclamation-triangle"></i> Proroga Corso</a>';
                }   
                  echo '<br><br><br><a href="'.BASE_URL.'/moduli/iscrizioni/salva.php?idUtenteMoodle='.$idUtenteMoodle.'&fn=disabilitaUtente" class="btn red-thunderbird"><i class="fa fa-exclamation-triangle"></i> Disabilita Utente</a>
                      <a href="'.BASE_URL.'/moduli/iscrizioni/salva.php?idUtenteMoodle='.$idUtenteMoodle.'&fn=abilitaUtente" class="btn green-jungle"><i class="fa fa-exclamation-triangle"></i> Abilita Utente</a>
                      <!-- 
                      <br><br><br>
                      <a href="#####" class="btn red-pink"><i class="fa fa-exclamation-triangle"></i> Resetta Attivitá Corso</a> -->';

                  echo '</center></div></div>';
            echo '</div></div>';

/*          07.12.2017 CRO -> COMMENTATO PERCHE C è DA CAPIRE BENE VISTO CHE ORA ISCRIVONO AL CALENDARIO DA PREV E FATTURE E NON DA CONFIGURAZIONE

            echo '<div class="row"><div class="col-md-12 col-sm-12">';
            $sql_0006 = "SELECT id FROM calendario WHERE id_corso = '".$id_corso."' AND id_professionista = '".$id_professionista."'";
            $rs_00006 = $dblink->num_rows($sql_0006);
            if ($rs_00006>0) {
                    $sql_0001 = "SELECT data, ora, oggetto, stato, CONCAT('<a class=\"btn btn-circle btn-icon-only red-thunderbird btn-outline\" href=\"".BASE_URL."/moduli/corsi/cancella.php?tbl=calendario_esami&idCalendario=',id,'&idCalendarioCorso=',id_calendario_0,'&idIscrizione=',id_iscrizione,'\" title=\"DISISCRIVI DAL CORSO\" alt=\"DISISCRIVI DAL CORSO\"><i class=\"fa fa-user-times\"></i></a>') AS 'fa-user-times' 
                    FROM calendario
                    WHERE id_corso=" . $id_corso."
                    AND id_professionista = '".$id_professionista."'
                    AND etichetta LIKE 'Iscrizione Esame'
                    ORDER BY data DESC, ora ASC";
                    stampa_table_static_basic($sql_0001, '', 'Esami - Iscrizioni', 'green');
                }else{
                    $sql_0001 = "SELECT data, ora, oggetto, stato,
                    CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"salva.php?tbl=calendario_esami&idCalendario=',id,'&idProfessionista=".$id_professionista."&idIscrizione=".$id."&fn=iscriviEsameUtente\" title=\"ISCRIVI ESAME\" alt=\"ISCRIVI ESAME\"><i class=\"fa fa-user-plus\"></i></a>') AS 'fa-user-plus'
                    FROM calendario
                    WHERE id_corso=" . $id_corso."
                    AND etichetta LIKE 'Calendario Esami'
                    ORDER BY data DESC, ora ASC";
                    stampa_table_static_basic($sql_0001, '', 'Esami Disponibili', 'blue-steel');
                }                
                
                
            echo '</div></div>';
*/

            echo '<div class="row"><div class="col-md-12 col-sm-12">';
            $sql_0002 = "SELECT
           `lista_corsi_dettaglio`. `ordine`,
             CONCAT('<h3>', `lista_corsi_dettaglio`. `nome`,'</h3>') AS 'Modulo',
               `lista_corsi_dettaglio`. `modname` AS 'Tipo',
               IF(completionstate>=1,'Si','No') AS 'Completato',
            FROM_UNIXTIME(`mdl_course_modules_completion`.timemodified) AS 'Data Completamento',
            `lista_corsi_dettaglio`. `durata`,
            id_modulo, `lista_corsi_dettaglio`.instance
            FROM ".MOODLE_DB_NAME." .`mdl_course_modules_completion`
            INNER JOIN ".MOODLE_DB_NAME." .`mdl_course_modules` ON ".MOODLE_DB_NAME." .`mdl_course_modules_completion`. `coursemoduleid` = `mdl_course_modules` .id
            INNER JOIN ".MOODLE_DB_NAME." .`mdl_scorm` ON ".MOODLE_DB_NAME." .`mdl_scorm`.`id`=`mdl_course_modules`.`instance`
            INNER JOIN `betaform_erp`.`lista_corsi_dettaglio` ON `betaform_erp`.`lista_corsi_dettaglio`.id_modulo = `mdl_course_modules` .id
            AND `mdl_course_modules`.`course`='".$id_moodle."'
            AND `mdl_course_modules_completion`.`userid`='".$idUtenteMoodle."'
            AND completionstate>=1
            ORDER BY `betaform_erp`.`lista_corsi_dettaglio`.`ordine`";
            $rs_00002 = $dblink->num_rows($sql_0002);
            if($rs_00002>=1){
                stampa_table_static_basic($sql_0002, '', 'Moduli Completati', 'green-jungle');
            }
            /*
              (SELECT (FROM_UNIXTIME(`mdl_scorm_scoes_track`.timemodified)) AS 'data_completamento' FROM ".MOODLE_DB_NAME.".`mdl_scorm_scoes_track`
                WHERE ".MOODLE_DB_NAME.".`mdl_scorm_scoes_track`.element='cmi.core.lesson_status'
                AND `mdl_scorm_scoes_track`. `userid`='".$idUtenteMoodle."'
                AND  `mdl_scorm_scoes_track`.scormid = `mdl_scorm`.`id`
                ORDER BY DATE(FROM_UNIXTIME(`mdl_scorm_scoes_track`.value)) ASC) AS TEST, 
            */
            $sql_0002 = "SELECT `lista_corsi_dettaglio`. `ordine`,
             CONCAT('<h3>', `lista_corsi_dettaglio`. `nome`,'</h3>') AS 'Modulo',
               `lista_corsi_dettaglio`. `modname` AS 'Tipo',
               IF(completionstate>=1,'Si','No') AS 'Completato',
            FROM_UNIXTIME(`mdl_course_modules_completion`.timemodified) AS 'Data Completamento',
            `lista_corsi_dettaglio`. `durata`,
            id_modulo, `lista_corsi_dettaglio`.instance
            FROM ".MOODLE_DB_NAME." .`mdl_course_modules_completion`
            INNER JOIN ".MOODLE_DB_NAME." .`mdl_course_modules` ON  ".MOODLE_DB_NAME." .`mdl_course_modules_completion`. `coursemoduleid` = `mdl_course_modules` .id
            INNER JOIN ".MOODLE_DB_NAME." .`mdl_scorm` ON ".MOODLE_DB_NAME." .`mdl_scorm`.`id`=`mdl_course_modules`.`instance`
            INNER JOIN ".MOODLE_DB_NAME.".`mdl_scorm_scoes_track` ON ".MOODLE_DB_NAME." .`mdl_scorm`.`id`=`mdl_scorm_scoes_track`.`scormid`
            INNER JOIN `betaform_erp`.`lista_corsi_dettaglio` ON `betaform_erp`.`lista_corsi_dettaglio`.id_modulo = `mdl_course_modules` .id
            WHERE `mdl_course_modules_completion`.`userid`='".$idUtenteMoodle."'
            AND ".MOODLE_DB_NAME.".`mdl_scorm_scoes_track`.`userid` ='".$idUtenteMoodle."'
            AND `mdl_course_modules`.`course`='".$id_moodle."'
            AND ".MOODLE_DB_NAME.".`mdl_scorm_scoes_track`.element='x.start.time'
            AND completionstate<=0";
            $rs_00002 = $dblink->num_rows($sql_0002);
            if($rs_00002>=1){
                stampa_table_static_basic($sql_0002, '', 'Moduli Iniziati', 'yellow');
            }
            echo '</div></div>';
            
            echo '<div class="row"><div class="col-md-12 col-sm-12">';
            /*
            $sql_0002 = "SELECT mdl_quiz.intro,
           `lista_corsi_dettaglio`. `ordine`,
             CONCAT('<h3>', `lista_corsi_dettaglio`. `nome`,'</h3>') AS 'Modulo',
               `lista_corsi_dettaglio`. `modname` AS 'Tipo',
               IF(completionstate>=1,'Si','No') AS 'Completato',
            FROM_UNIXTIME(`mdl_course_modules_completion`.timemodified) AS 'Data Completamento',
            `lista_corsi_dettaglio`. `durata`,
            id_modulo, `lista_corsi_dettaglio`.instance, mdl_quiz_grades.grade
            FROM ".MOODLE_DB_NAME." .`mdl_course_modules_completion`
            INNER JOIN ".MOODLE_DB_NAME." .`mdl_course_modules` ON ".MOODLE_DB_NAME." .`mdl_course_modules_completion`. `coursemoduleid` = `mdl_course_modules` .id
            INNER JOIN ".MOODLE_DB_NAME." .`mdl_scorm` ON ".MOODLE_DB_NAME." .`mdl_scorm`.`id`=`mdl_course_modules`.`instance`
            INNER JOIN `betaform_erp`.`lista_corsi_dettaglio` ON `betaform_erp`.`lista_corsi_dettaglio`.id_modulo = `mdl_course_modules` .id
            INNER JOIN ".MOODLE_DB_NAME.".mdl_quiz ON ".MOODLE_DB_NAME.".mdl_quiz.course = `mdl_course_modules`.`course`
            INNER JOIN ".MOODLE_DB_NAME.".mdl_quiz_grades ON mdl_quiz_grades.quiz = ".MOODLE_DB_NAME.".mdl_quiz.id
            AND `mdl_course_modules`.`course`='".$id_moodle."'
            AND `mdl_quiz`.`course`='".$id_moodle."'
            AND `mdl_course_modules_completion`.`userid`='".$idUtenteMoodle."'
            AND `mdl_quiz_grades`.`userid`='".$idUtenteMoodle."'
            AND `lista_corsi_dettaglio`. `modname`='quiz'
            ORDER BY `betaform_erp`.`lista_corsi_dettaglio`.`ordine`";

            
            $sql_0002 = " SELECT CONCAT('<h3>',name,'</h3>') AS 'Quiz', intro,  mdl_grade_items.grademax, mdl_grade_items.gradepass, 
             `mdl_quiz`.grade AS 'Voto Max', `mdl_quiz_grades`.grade AS 'Voto', FROM_UNIXTIME(mdl_quiz_grades.timemodified) AS 'Data Completamento'
            FROM ".MOODLE_DB_NAME.".mdl_quiz
            INNER JOIN ".MOODLE_DB_NAME.".mdl_quiz_grades ON mdl_quiz_grades.quiz = ".MOODLE_DB_NAME.".mdl_quiz.id
            INNER JOIN ".MOODLE_DB_NAME.".mdl_grade_items ON mdl_grade_items.iteminstance = mdl_quiz.id
            AND `mdl_quiz_grades`.`userid`='".$idUtenteMoodle."'
            AND `mdl_quiz`.`course`='".$id_moodle."'
            AND mdl_grade_items.itemmodule='quiz'";
            */
            $sql_0002 = " SELECT CONCAT('<h3>',name,'</h3>') AS 'Quiz', intro,  mdl_grade_items.grademax, mdl_grade_items.gradepass, 
             `mdl_quiz`.grade AS 'Voto Max', `mdl_quiz_grades`.grade AS 'Voto', 
             FROM_UNIXTIME(mdl_quiz_grades.timemodified) AS 'Data Completamento',
             IF(mdl_grade_items.gradepass<=`mdl_quiz_grades`.grade,'Si','No') AS 'Completato'
            FROM ".MOODLE_DB_NAME.".mdl_quiz
            INNER JOIN ".MOODLE_DB_NAME.".mdl_quiz_grades ON mdl_quiz_grades.quiz = ".MOODLE_DB_NAME.".mdl_quiz.id
            INNER JOIN ".MOODLE_DB_NAME.".mdl_grade_items ON mdl_grade_items.iteminstance = mdl_quiz.id
            AND `mdl_quiz_grades`.`userid`='".$idUtenteMoodle."'
            AND `mdl_quiz`.`course`='".$id_moodle."'
            AND mdl_grade_items.itemmodule='quiz'";
            stampa_table_static_basic($sql_0002, '', 'Quiz Completati', 'green-jungle');
            
            $sql_0002 = " SELECT CONCAT('<h3>',name,'</h3>') AS 'Quiz', intro,  
            mdl_grade_items.grademax, 
            mdl_grade_items.gradepass, ('No') AS 'Completato'
            FROM ".MOODLE_DB_NAME.".mdl_quiz
             INNER JOIN ".MOODLE_DB_NAME.".mdl_grade_items ON mdl_grade_items.iteminstance = mdl_quiz.id
            WHERE `mdl_quiz`.`course`='".$id_moodle."'
            AND mdl_grade_items.itemmodule='quiz'
            AND mdl_quiz.id NOT IN (SELECT quiz FROM ".MOODLE_DB_NAME.".mdl_quiz_grades WHERE `mdl_quiz_grades`.`userid`='".$idUtenteMoodle."')";
            //stampa_table_static_basic($sql_0002, '', 'Quiz NON Completati', 'red');
            
            $sql_0002 = "SELECT CONCAT('<h3>',name,'</h3>') AS 'Feedback', intro,
            FROM_UNIXTIME(mdl_feedback_completed.timemodified) AS 'Data Completamento',
            ('Si') AS 'Completato'
            FROM ".MOODLE_DB_NAME.".`mdl_feedback`
            INNER JOIN ".MOODLE_DB_NAME.".`mdl_feedback_completed` ON `mdl_feedback`.id = `mdl_feedback_completed`.feedback
            WHERE `mdl_feedback`.`course`='".$id_moodle."'
            AND `mdl_feedback_completed`.`userid`='".$idUtenteMoodle."'";
            stampa_table_static_basic($sql_0002, '', 'Feedback', 'yellow');
            echo '</div></div>';
        break;
    }
}
?>
