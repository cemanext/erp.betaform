<?php

/** FUNZIONI DI CROCCO */
function Stampa_HTML_index_Calendario(){
    global $table_calendario, $where_calendario, $dblink;
    
    $sql = "SELECT COUNT(id) AS conto FROM calendario WHERE stato LIKE 'Negativo' AND etichetta LIKE 'Nuova Richiesta' AND nome_obiezione = '' $where_calendario";
    if($dblink->get_field($sql) > 0 && $_SESSION['livello_utente']=="commerciale"){
        
        $tabella = "calendario INNER JOIN lista_preventivi ON calendario.id = lista_preventivi.id_calendario";
        $campi_visualizzati = "
                        CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" target=\"_blank\" href=\"".BASE_URL."/moduli/anagrafiche/dettaglio_tab.php?tbl=calendario&id=',calendario.id,'\" title=\"SCHEDA\" alt=\"SCHEDA\"><i class=\"fa fa-book\"></i></a>') AS 'fa-book',
                        CONCAT(calendario.id,'|',lista_preventivi.id) AS 'nome_obiezione', 
                        IF(calendario.id_azienda>0,CONCAT('<i class=\"fa fa-user btn btn-icon-only green-jungle btn-outline\" style=\"display: inline; padding: 3px; line-height: 0.5;\"></i>'),CONCAT('<i class=\"fa fa-user-times btn btn-icon-only red-flamingo btn-outline\" style=\"display: inline; padding: 3px; line-height: 0.5;\"></i>')) AS 'fa-user', 
                        calendario.mittente AS 'Mittente',
                        IF(calendario.id_professionista>0,(SELECT CONCAT(lista_professionisti.cognome,' ',lista_professionisti.nome) FROM lista_professionisti WHERE lista_professionisti.id=calendario.id_professionista ),'') AS Professionista,
                        (SELECT lista_prodotti.nome FROM lista_prodotti WHERE lista_prodotti.id=calendario.id_prodotto) AS 'Corso', lista_preventivi.data_iscrizione AS 'Data Negativo', lista_preventivi.imponibile AS Importo, 
                        (SELECT nome FROM lista_tipo_marketing WHERE id = id_tipo_marketing) AS Marketing,
                        (SELECT nome FROM lista_campagne WHERE id = calendario.id_campagna) AS Campagna";
        
        //$where = $table_calendario['index']['where'];
        $where = " calendario.stato='Negativo' $where_calendario AND calendario.nome_obiezione = ''";
        //$ordine = $table_calendario['index']['order'];
        $ordine = " ORDER BY datainsert DESC, orainsert ASC";
        $limite = "LIMIT 10";
        $titolo = 'Elenco Richieste Negative - Senza Obiezione';
        $colore = "red-intense";
        $sql_0001 = "SELECT ".$campi_visualizzati." FROM ".$tabella." WHERE $where $ordine ";//$limite
        
        stampa_table_static_basic($sql_0001, '', $titolo, $colore);
    }else{
    
        $tabella = 'calendario';
        switch($tabella){
            case 'calendario':
                if(isset($_GET['whrStato'])){
                    $tabella = "calendario";
                    switch ($_GET['whrStato']) {
                        case MD5('In Attesa di Controllo'):
                            $campi_visualizzati = "id AS 'selezione', CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"".BASE_URL."/moduli/anagrafiche/dettaglio_tab.php?tbl=calendario&id=',id,'\" title=\"SCHEDA\" alt=\"SCHEDA\"><i class=\"fa fa-book\"></i></a>') AS 'fa-book',
                                            CONCAT('<a class=\"btn btn-circle btn-icon-only purple-seance btn-outline\" onclick=\"associaCommercialeARichesta(',id,',',id_professionista,');\" title=\"ASSOCIA COMMERCIALE\" alt=\"ASSOCIA COMMERCIALE\"><i class=\"fa fa-sign-in\"></i></a>') AS 'Ass. Comm.',
                                            'fa-user', 'Messaggio', 
                                            'Marketing', 'Data Inserimento', 'Ora Inserimento',
                                            CONCAT('<a class=\"btn btn-circle btn-icon-only red btn-outline\" href=\"cancella.php?tbl=calendario&id=',id,'\" title=\"ELIMINA\" alt=\"ELIMINA\"><i class=\"fa fa-trash\"></i></a>') AS 'fa-trash'";

                            /*
                            $campi_visualizzati = "datainsert AS 'Data', orainsert AS 'Ora', oggetto AS 'Origine / Tipo marketing', mittente AS 'Nominativo', campo_4 AS 'Tel.', campo_5 AS 'Email', 
                            CONCAT('<span class=\"bg-green-jungle  bg-font-green-jungle font-lg sbold\">Si o No</div>') AS 'Cliente',
                            CONCAT('<span class=\"label label-sm label-warning\">',stato,'</div>') AS 'Stato'";
                            */
                            //$where = $table_calendario['index']['where'];
                            $where = " MD5(stato)='".$_GET['whrStato']."' $where_calendario";
                            //$ordine = $table_calendario['index']['order'];
                            $ordine = " ORDER BY datainsert DESC, orainsert ASC";
                            $limite = "LIMIT 1";
                            $titolo = 'Elenco Richieste in Attesa di Controllo';
                            $stile = "datatable_ajax";
                            $colore = "yellow-saffron";
                            $sql_0001 = "SELECT ".$campi_visualizzati." FROM ".$tabella." WHERE $where $ordine $limite";
                        break;

                        case MD5('Mai Contattato'):
                        case MD5('Richiamare'):
                            if($_SESSION['livello_utente']=="commerciale"){
                                $campi_visualizzati = "'fa-book', IF(id_agente='".$_SESSION['id_utente']."',
                                                CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"".BASE_URL."/moduli/anagrafiche/dettaglio_tab.php?tbl=calendario&id=',id,'\" title=\"SCHEDA\" alt=\"SCHEDA\"><i class=\"fa fa-book\"></i></a>'),
                                                CONCAT('<a class=\"btn btn-circle btn-icon-only purple-seance btn-outline\" onclick=\"associaCommercialeARichesta(',id,',',id_professionista,');\" title=\"PRENDI IN CARICO\" alt=\"PRENDI IN CARICO\"><i class=\"fa fa-sign-in\"></i></a>')) AS 'Ass. Comm.',
                                                (SELECT CONCAT(lista_password.nome,' ',lista_password.cognome) FROM lista_password WHERE lista_password.id=calendario.id_agente) AS 'Commerciale', 
                                                stato, 'fa-user', mittente AS 'Mittente',
                                                'Professionista', 
                                                (SELECT lista_prodotti.nome FROM lista_prodotti WHERE lista_prodotti.id=calendario.id_prodotto) AS 'Prodotto', data AS 'Data Richiamo', ora AS 'Ora Richiamo', 
                                                (SELECT nome FROM lista_tipo_marketing WHERE id = id_tipo_marketing) AS Marketing";
                            }else{
                                $campi_visualizzati = "CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"".BASE_URL."/moduli/anagrafiche/dettaglio_tab.php?tbl=calendario&id=',id,'\" title=\"SCHEDA\" alt=\"SCHEDA\"><i class=\"fa fa-book\"></i></a>') AS 'fa-book',
                                                (SELECT CONCAT(lista_password.nome,' ',lista_password.cognome) FROM lista_password WHERE lista_password.id=calendario.id_agente) AS 'Commerciale', 
                                                stato, 'fa-user', mittente AS 'Mittente',
                                                'Professionista', 
                                                (SELECT lista_prodotti.nome FROM lista_prodotti WHERE lista_prodotti.id=calendario.id_prodotto) AS 'Prodotto', data AS 'Data Richiamo', ora AS 'Ora Richiamo', 
                                                (SELECT nome FROM lista_tipo_marketing WHERE id = id_tipo_marketing) AS Marketing";
                            }
                            //$where = $table_calendario['index']['where'];
                            $where = " MD5(stato)='".$_GET['whrStato']."' $where_calendario";
                            //$ordine = $table_calendario['index']['order'];
                            $ordine = " ORDER BY datainsert DESC, orainsert ASC";
                            $limite = "LIMIT 1";
                            $titolo = ($_GET['whrStato']==MD5('Richiamare')) ? "Elenco Richieste Da Richiamare" : 'Elenco Richieste Mai Contattati';
                            $stile = "datatable_ajax";
                            $colore = ($_GET['whrStato']==MD5('Richiamare')) ? "green-jungle" : "red-flamingo";
                            $sql_0001 = "SELECT ".$campi_visualizzati." FROM ".$tabella." WHERE $where $ordine $limite";
                        break;

                        case MD5('Venduto'):
                            if($_SESSION['livello_utente']=="commerciale"){
                                $campi_visualizzati = "'fa-book', IF(id_agente='".$_SESSION['id_utente']."',
                                                CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"".BASE_URL."/moduli/anagrafiche/dettaglio_tab.php?tbl=calendario&id=',id,'\" title=\"SCHEDA\" alt=\"SCHEDA\"><i class=\"fa fa-book\"></i></a>'),
                                                CONCAT('<a class=\"btn btn-circle btn-icon-only purple-seance btn-outline\" onclick=\"associaCommercialeARichesta(',id,',',id_professionista,');\" title=\"PRENDI IN CARICO\" alt=\"PRENDI IN CARICO\"><i class=\"fa fa-sign-in\"></i></a>')) AS 'Ass. Comm.',
                                                (SELECT CONCAT(lista_password.nome,' ',lista_password.cognome) FROM lista_password WHERE lista_password.id=calendario.id_agente) AS 'Commerciale', 
                                                stato, 'fa-user', mittente AS 'Mittente',
                                                'Professionista', 
                                                (SELECT lista_prodotti.nome FROM lista_prodotti WHERE lista_prodotti.id=calendario.id_prodotto) AS 'Prodotto', data AS 'Data Iscritto', 'Importo', 
                                                (SELECT nome FROM lista_tipo_marketing WHERE id = id_tipo_marketing) AS Marketing";
                            }else{
                                $campi_visualizzati = "CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"".BASE_URL."/moduli/anagrafiche/dettaglio_tab.php?tbl=calendario&id=',id,'\" title=\"SCHEDA\" alt=\"SCHEDA\"><i class=\"fa fa-book\"></i></a>') AS 'fa-book',
                                                (SELECT CONCAT(lista_password.nome,' ',lista_password.cognome) FROM lista_password WHERE lista_password.id=calendario.id_agente) AS 'Commerciale', 
                                                stato, 'fa-user', mittente AS 'Mittente',
                                                'Professionista', 
                                                (SELECT lista_prodotti.nome FROM lista_prodotti WHERE lista_prodotti.id=calendario.id_prodotto) AS 'Prodotto', data AS 'Data Iscritto', 'Importo', 
                                                (SELECT nome FROM lista_tipo_marketing WHERE id = id_tipo_marketing) AS Marketing";
                            }
                            //$where = $table_calendario['index']['where'];
                            $where = " MD5(stato)='".$_GET['whrStato']."' $where_calendario";
                            //$ordine = $table_calendario['index']['order'];
                            $ordine = " ORDER BY datainsert DESC, orainsert ASC";
                            $limite = "LIMIT 1";
                            $titolo = ($_GET['whrStato']==MD5('Venduto')) ? "Elenco Richieste Vendute" : 'Elenco Richieste Negative';
                            $stile = "datatable_ajax";
                            $colore = ($_GET['whrStato']==MD5('Venduto')) ? "blue-steel" : "red-intense";
                            $sql_0001 = "SELECT ".$campi_visualizzati." FROM ".$tabella." WHERE $where $ordine $limite";
                        break;
                        
                        case MD5('Negativo'):
                            if($_SESSION['livello_utente']=="commerciale"){
                                $campi_visualizzati = "'fa-book', IF(id_agente='".$_SESSION['id_utente']."',
                                                CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"".BASE_URL."/moduli/anagrafiche/dettaglio_tab.php?tbl=calendario&id=',id,'\" title=\"SCHEDA\" alt=\"SCHEDA\"><i class=\"fa fa-book\"></i></a>'),
                                                CONCAT('<a class=\"btn btn-circle btn-icon-only purple-seance btn-outline\" onclick=\"associaCommercialeARichesta(',id,',',id_professionista,');\" title=\"PRENDI IN CARICO\" alt=\"PRENDI IN CARICO\"><i class=\"fa fa-sign-in\"></i></a>')) AS 'Ass. Comm.',
                                                (SELECT CONCAT(lista_password.nome,' ',lista_password.cognome) FROM lista_password WHERE lista_password.id=calendario.id_agente) AS 'Commerciale', 
                                                stato, 'fa-user', mittente AS 'Mittente',
                                                'Professionista', 
                                                (SELECT lista_prodotti.nome FROM lista_prodotti WHERE lista_prodotti.id=calendario.id_prodotto) AS 'Prodotto', data AS 'Data Negativo', 'Importo', 
                                                (SELECT nome FROM lista_tipo_marketing WHERE id = id_tipo_marketing) AS Marketing";
                            }else{
                                $campi_visualizzati = "CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"".BASE_URL."/moduli/anagrafiche/dettaglio_tab.php?tbl=calendario&id=',id,'\" title=\"SCHEDA\" alt=\"SCHEDA\"><i class=\"fa fa-book\"></i></a>') AS 'fa-book',
                                                (SELECT CONCAT(lista_password.nome,' ',lista_password.cognome) FROM lista_password WHERE lista_password.id=calendario.id_agente) AS 'Commerciale', 
                                                stato, 'fa-user', mittente AS 'Mittente',
                                                'Professionista', 
                                                (SELECT lista_prodotti.nome FROM lista_prodotti WHERE lista_prodotti.id=calendario.id_prodotto) AS 'Prodotto', data AS 'Data Negativo', 'Importo', 
                                                (SELECT nome FROM lista_tipo_marketing WHERE id = id_tipo_marketing) AS Marketing";
                            }
                            //$where = $table_calendario['index']['where'];
                            $where = " MD5(stato)='".$_GET['whrStato']."' $where_calendario";
                            //$ordine = $table_calendario['index']['order'];
                            $ordine = " ORDER BY datainsert DESC, orainsert ASC";
                            $limite = "LIMIT 1";
                            $titolo = ($_GET['whrStato']==MD5('Venduto')) ? "Elenco Richieste Vendute" : 'Elenco Richieste Negative';
                            $stile = "datatable_ajax";
                            $colore = ($_GET['whrStato']==MD5('Venduto')) ? "blue-steel" : "red-intense";
                            $sql_0001 = "SELECT ".$campi_visualizzati." FROM ".$tabella." WHERE $where $ordine $limite";
                        break;

                        case MD5('Chiusa In Attesa di Controllo'):
                            $campi_visualizzati = "id AS 'selezione', CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"".BASE_URL."/moduli/anagrafiche/dettaglio_tab.php?tbl=calendario&id=',id,'\" title=\"SCHEDA\" alt=\"SCHEDA\"><i class=\"fa fa-book\"></i></a>') AS 'fa-book',
                                            'Commerciale',
                                            'fa-user', 'Messaggio', 
                                            'fa-check', 'Data Inserimento', 'Ora Inserimento'";
                            //$where = $table_calendario['index']['where'];
                            $where = " MD5(stato)='".$_GET['whrStato']."' $where_calendario";
                            //$ordine = $table_calendario['index']['order'];
                            $ordine = " ORDER BY datainsert DESC, orainsert ASC";
                            $limite = "LIMIT 1";
                            $titolo = 'Elenco Richieste Chiuse in Attesa di Controllo';
                            $stile = "datatable_ajax";
                            $colore = "yellow-soft";
                            $sql_0001 = "SELECT ".$campi_visualizzati." FROM ".$tabella." WHERE $where $ordine $limite";
                        break;
                    
                        case MD5('Note di Credito'):
                            $campi_visualizzati = "CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"".BASE_URL."/moduli/anagrafiche/dettaglio_tab.php?tbl=calendario&id=',id,'\" title=\"SCHEDA\" alt=\"SCHEDA\"><i class=\"fa fa-book\"></i></a>') AS 'fa-book',
                                            (SELECT CONCAT(lista_password.nome,' ',lista_password.cognome) FROM lista_password WHERE lista_password.id=calendario.id_agente) AS 'Commerciale', 
                                            stato, 'fa-user', mittente AS 'Mittente',
                                            'Professionista', 
                                            (SELECT lista_prodotti.nome FROM lista_prodotti WHERE lista_prodotti.id=calendario.id_prodotto) AS 'Prodotto', 
                                            data AS 'Data Nota di Credito', 
                                            '' AS 'Cod. Fattura',
                                            'Importo', 
                                            (SELECT nome FROM lista_tipo_marketing WHERE id = id_tipo_marketing) AS Marketing";
                            //$where = $table_calendario['index']['where'];
                            $where = " MD5(stato)='".$_GET['whrStato']."'";
                            //$ordine = $table_calendario['index']['order'];
                            $ordine = " ORDER BY datainsert DESC, orainsert ASC";
                            $limite = "LIMIT 1";
                            $titolo = 'Elenco Richieste con Note di Credito';
                            $stile = "datatable_ajax";
                            $colore = "red-intense";
                        $sql_0001 = "SELECT ".$campi_visualizzati." FROM ".$tabella." WHERE $where $ordine $limite";
                        break;

                        case MD5('Iscritto FREE'):
                            $campi_visualizzati = "CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"".BASE_URL."/moduli/anagrafiche/dettaglio_tab.php?tbl=calendario&id=',id,'\" title=\"SCHEDA\" alt=\"SCHEDA\"><i class=\"fa fa-book\"></i></a>') AS 'fa-book',
                                            (SELECT CONCAT(lista_password.nome,' ',lista_password.cognome) FROM lista_password WHERE lista_password.id=calendario.id_agente) AS 'Commerciale', 
                                            stato, 'fa-user', mittente AS 'Mittente',
                                            'Professionista', 
                                            (SELECT lista_prodotti.nome FROM lista_prodotti WHERE lista_prodotti.id=calendario.id_prodotto) AS 'Prodotto',
                                            'Data Iscrizione',
                                            (SELECT nome FROM lista_tipo_marketing WHERE id = id_tipo_marketing) AS Marketing";
                            //$where = $table_calendario['index']['where'];
                            $where = " MD5(stato)='".$_GET['whrStato']."'";
                            //$ordine = $table_calendario['index']['order'];
                            $ordine = " ORDER BY datainsert DESC, orainsert ASC";
                            $limite = "LIMIT 1";
                            $titolo = 'Elenco Richieste con Iscritto FREE';
                            $stile = "datatable_ajax";
                            $colore = "blue";
                        $sql_0001 = "SELECT ".$campi_visualizzati." FROM ".$tabella." WHERE $where $ordine $limite";
                        break;


                        case MD5('richiesteSerena'):
                        $campi_visualizzati = "CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"".BASE_URL."/moduli/anagrafiche/dettaglio_tab.php?tbl=calendario&id=',id,'\" title=\"SCHEDA\" alt=\"SCHEDA\"><i class=\"fa fa-book\"></i></a>') AS 'fa-book',
                                        id as idRichiesta,
                                        if(id_professionista<=0,mittente,'Cliente') As 'Mittente',
                                        if(id_professionista<=0,cognome, (SELECT cognome from lista_professionisti WHERE lista_professionisti.id = id_professionista)) AS cognome,
                                        if(id_professionista<=0,nome, (SELECT nome from lista_professionisti WHERE lista_professionisti.id = id_professionista)) AS nome,
                                        if(id_professionista<=0,professione, (SELECT professione from lista_professionisti WHERE lista_professionisti.id = id_professionista)) AS professione,
                                        if(id_professionista<=0,luogo_di_nascita, (SELECT luogo_di_nascita from lista_professionisti WHERE lista_professionisti.id = id_professionista)) AS citta,
                                        if(id_professionista<=0,telefono, (SELECT telefono from lista_professionisti WHERE lista_professionisti.id = id_professionista)) AS telefono,
                                        if(id_professionista<=0,cellulare, (SELECT cellulare from lista_professionisti WHERE lista_professionisti.id = id_professionista)) AS cellulare,
                                        if(id_professionista<=0,email, (SELECT email from lista_professionisti WHERE lista_professionisti.id = id_professionista)) AS email,
                                        (SELECT nome from lista_prodotti WHERE lista_prodotti.id = id_prodotto) AS prodotto,
                                        (SELECT importo from lista_preventivi WHERE lista_preventivi.id = id_preventivo) AS importo,
                                        (SELECT concat(cognome,' ',nome) from lista_password WHERE lista_password.id = id_agente) AS commerciale,
                                        (SELECT nome from lista_tipo_marketing WHERE lista_tipo_marketing.id = id_tipo_marketing) AS tipo_mkt,
                                        (SELECT nome from lista_campagne WHERE lista_campagne.id = id_campagna) AS campagna_mkt,
                                        'link',
                                        messaggio as note,
                                        calendario.stato as stato,
                                        datainsert as data_richiesta,
                                        orainsert as ora_richiesta";
                            //$where = $table_calendario['index']['where'];
                            $where = " `etichetta` LIKE '%richiesta%'";
                            //$ordine = $table_calendario['index']['order'];
                            $ordine = " ORDER BY datainsert ASC, orainsert ASC";
                            $limite = "LIMIT 1";
                            $titolo = 'Esporta Richieste x SERENA';
                            $stile = "datatable_ajax";
                            $colore = "";
                        $sql_0001 = "SELECT ".$campi_visualizzati." FROM ".$tabella." WHERE $where $ordine $limite";
                        break;

                        case MD5('esportaBenedetto'):
                        $campi_visualizzati = "CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"".BASE_URL."/moduli/anagrafiche/dettaglio_tab.php?tbl=calendario&id=',calendario.id,'\" title=\"SCHEDA\" alt=\"SCHEDA\"><i class=\"fa fa-book\"></i></a>') AS 'fa-book',
                        'idIscrizione','idFattura','idRichiesta','idCorso','NomeCorso', 'DataRichiesta','DataIscrizione','DataInizio','DataCompletamento', 'StatoIscrizione', 'StatoRichiesta', 'Cognome',
                        'Nome', 'Professione', 'telefono', 'cellulare', 'indirizzo', 'cap', 'citta', 'prov', 'regione', 'Email', 'Comm.le', 'Partner'";
                            //$where = $table_calendario['index']['where'];
                            $where = " calendario.etichetta LIKE '%richiesta%' AND lista_iscrizioni.id_corso >0 AND lista_fatture.sezionale NOT LIKE 'CN%'";
                            //$ordine = $table_calendario['index']['order'];
                            $ordine = " ORDER BY calendario.datainsert ASC";
                            $limite = "LIMIT 1";
                            $titolo = 'Esporta Totale x BENEDETTO';
                            $stile = "datatable_ajax";
                            $colore = "";
                        $sql_0001 = "SELECT ".$campi_visualizzati." FROM lista_iscrizioni INNER JOIN lista_fatture ON lista_iscrizioni.id_fattura = lista_fatture.id INNER JOIN calendario ON lista_fatture.id_calendario = calendario.id WHERE $where $ordine $limite";
                        break;

                        default:
                            if($_SESSION['livello_utente']=="commerciale"){
                                $campi_visualizzati = "'fa-book',
                                                'Commerciale', stato, 'fa-user', mittente AS 'Mittente',
                                                'Professionista', 
                                                (SELECT lista_prodotti.nome FROM lista_prodotti WHERE lista_prodotti.id=calendario.id_prodotto) AS 'Prodotto', data AS 'Data Richiamo', ora AS 'Ora Richiamo', 
                                                (SELECT nome FROM lista_tipo_marketing WHERE id = id_tipo_marketing) AS Marketing";
                                $where = " 1 ";
                            }else{
                                $campi_visualizzati = $table_calendario['index']['campi'];
                                $where = $table_calendario['index']['where'];
                            }
                            $ordine = $table_calendario['index']['order'];
                            $limite = "LIMIT 1";
                            $titolo = 'Elenco Richieste';
                            $stile = "datatable_ajax";
                            $colore = COLORE_PRIMARIO;
                            $sql_0001 = "SELECT ".$campi_visualizzati." FROM ".$tabella." WHERE $where $ordine $limite";
                        break;
                    }
                }
                
                stampa_table_datatables_ajax($sql_0001, "datatable_ajax", $titolo, $stile, $colore, false);

                //stampa_table_datatables_responsive($sql_0001, $titolo, $stile, $colore);
            break;
                    /*
            default:
                $campi_visualizzati = "";
                $campi     = 	$dblink->list_fields("SELECT * FROM ".$tabella."");
                foreach ($campi as $nome_colonna) {
                     $campi_visualizzati.= "`".$nome_colonna->name."`, ";
                }

                $where = " 1 ";
                $ordine = " ORDER BY id DESC";
                $titolo = "Elenco ".$tabella;
                $stile = "tabella_base";
                $colore_tabella = COLORE_PRIMARIO;
                $sql_0001 = "SELECT 
                CONCAT('<a href=\"dettaglio.php?tbl=".$tabella."&id=',id,'\" title=\"DETTAGLIO\" alt=\"DETTAGLIO\"><button type=\"button\" class=\"btn yellow btn-warning mt-ladda-btn ladda-button btn-circle btn-icon-only\"><i class=\"fa fa-search\"></i></button></a>') AS '.:',
                CONCAT('<a href=\"modifica.php?tbl=".$tabella."&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><button type=\"button\" class=\"btn blue btn-warning mt-ladda-btn ladda-button btn-circle btn-icon-only\"><i class=\"fa fa-edit\"></i></button></a>') AS '::',
                CONCAT('<a href=\"duplica.php?tbl=".$tabella."&id=',id,'\" title=\"DUPLICA\" alt=\"DUPLICA\"><button type=\"button\" class=\"btn green btn-warning mt-ladda-btn ladda-button btn-circle btn-icon-only\"><i class=\"fa fa-copy\"></i></button></a>') AS '::',
                ".$campi_visualizzati.",
                CONCAT('<a href=\"cancella.php?tbl=".$tabella."&id=',id,'\" title=\"ELIMINA\" alt=\"ELIMINA\"><button type=\"button\" class=\"btn red btn-warning mt-ladda-btn ladda-button btn-circle btn-icon-only\"><i class=\"fa fa-trash\"></i></button></a>') AS ':. ,'
                FROM ".$tabella." WHERE $where $ordine";
                //echo '<li>$sql_0001 = '.$sql_0001.'</li>';
                stampa_table_datatables_responsive($sql_0001, $titolo, $stile, $colore_tabella);
            break;
                    */
        }
    }
}
?>
