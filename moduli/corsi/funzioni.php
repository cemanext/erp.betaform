<?php

/** FUNZIONI DI CROCCO */
function Stampa_HTML_index_Corsi($tabella){
    global $table_listaCorsi, $table_listaClassi, $table_calendarioEsami, $table_documentiAttestati;
    
    switch($tabella){

        case 'lista_attestati':
            $tabella = "lista_attestati";
            $campi_visualizzati = $table_documentiAttestati['index']['campi'];
            $where = $table_documentiAttestati['index']['where'];
            $ordine = $table_documentiAttestati['index']['order'];
            $titolo = 'Elenco Attestati';
            $sql_0001 = "SELECT ".$campi_visualizzati." FROM ".$tabella." WHERE $where $ordine";
            //echo '<li>$sql_0001 = '.$sql_0001.'</li>';
            stampa_table_datatables_ajax($sql_0001, "datatable_ajax", $titolo, '', '', false);
            //stampa_table_datatables_responsive($sql_0001, $titolo, 'tabella_base');
        break;
        
        case 'calendario_esami':
            $tabella = "calendario";
            $campi_visualizzati = $table_calendarioEsami['index']['campi'];
            $where = $table_calendarioEsami['index']['where'];
            $ordine = $table_calendarioEsami['index']['order'];
            $titolo = 'Calendario Corsi / Esami';
            $sql_0001 = "SELECT ".$campi_visualizzati." FROM ".$tabella." WHERE $where $ordine";
            //echo '<li>$sql_0001 = '.$sql_0001.'</li>';
            stampa_table_datatables_ajax($sql_0001, "datatable_ajax", $titolo, '', '', false);
            //stampa_table_datatables_responsive($sql_0001, $titolo, 'tabella_base');
        break;
        
        case 'lista_corsi':
            $tabella = "lista_corsi";
            $campi_visualizzati = $table_listaCorsi['index']['campi'];
            $where = $table_listaCorsi['index']['where'];
            $ordine = $table_listaCorsi['index']['order'];
            $titolo = 'Elenco Corsi';
            $sql_0001 = "SELECT ".$campi_visualizzati." FROM ".$tabella." WHERE $where $ordine";
            stampa_table_datatables_ajax($sql_0001, "datatable_ajax", $titolo, '', '', false);
            //stampa_table_datatables_responsive($sql_0001, $titolo, 'tabella_base');
        break;
    
        case 'lista_classi':
            $tabella = "lista_classi";
            $campi_visualizzati = $table_listaClassi['index']['campi'];
            $where = $table_listaClassi['index']['where'];
            $ordine = $table_listaClassi['index']['order'];
            $titolo = 'Elenco Classi';
            $sql_0001 = "SELECT ".$campi_visualizzati." FROM ".$tabella." WHERE $where $ordine";
            stampa_table_datatables_ajax($sql_0001, "datatable_ajax", $titolo, '', '', false);
            //stampa_table_datatables_responsive($sql_0001, $titolo, 'tabella_base');
        break;

    }
}

function Stampa_HTML_Dettaglio_Corsi($tabella,$id){
    global $table_listaCorsi, $dblink;
    
    switch ($tabella) {
        case 'calendario_esami':
        $idProdotto = $_GET['idProdotto'];
        $idCalendario = $_GET['id'];
        $idCorso = $_GET['idCorso'];
        
            echo '<div class="row"><div class="col-md-12 col-sm-12">';            
            $sql_0001 = "SELECT CONCAT('<H3>',nome,'</H3>') AS 'Corso',  tipologia, categoria, gruppo,
            codice AS 'Codice', 
            codice_esterno AS 'ID MOODLE' 
            FROM `lista_prodotti` WHERE id ='" . $idProdotto."'";
            stampa_table_static_basic($sql_0001, '', 'Corso', 'green-haze');
            echo '</div></div>';
            
            echo '<form method="POST" action="salva.php?idCalendario=' . $id . '&fn=SalvaDocenteCorso">';
            echo '<div class="row"><div class="col-md-12 col-sm-12">';
            $sql_0001 = "SELECT id, id_docente AS 'Docente' 
            FROM `matrice_corsi_docenti` WHERE id_prodotto ='" . $idProdotto."' AND id_calendario='".$idCalendario."'";
            //stampa_table_static_basic_input($sql_0001, '', 'Docenti Disponibili', '');
            stampa_table_static_basic_input('matrice_corsi_docenti',$sql_0001,'','Docenti Assegnati', '');
            echo '<div class="row">';
			//echo '------------->'.$stile_form;
            echo '<center><a href="salva.php?tbl=matrice_corsi_docenti&id='.$id.'&idProdotto='.$idProdotto.'&fn=nuovoDocenteCorso" class="btn green-meadow">
                Aggiungi Docente
                <i class="fa fa-plus"></i>
                </a> <input class="btn green-sharp" value="Salva" type="submit"></center>';
            echo '</div><br>';
            echo '</div></div>';
            echo '</form>';
            
            if($_GET['esame']==0){
            echo '<div class="row"><div class="col-md-12 col-sm-12">';
            $sql_0001 = "SELECT   CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"modifica.php?tbl=calendario_esami&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',
            data, ora,  IF(etichetta LIKE 'Calendario Esami',CONCAT('<span class=\"btn sbold uppercase btn-outline blue\">',etichetta,'</span>'),CONCAT('<span class=\"btn sbold uppercase btn-outline red-thunderbird\">',etichetta,'</span>')) AS 'Tipo', oggetto, numerico_10 AS 'Iscritti', numerico_4  AS 'Costo Aula', numerico_5 AS 'Costo Docenti', stato
            FROM calendario
            WHERE id_prodotto='" . $idProdotto."'
            AND id = '".$id."'
            AND etichetta LIKE 'Calendario Corsi'
            ORDER BY data DESC, ora ASC";
            stampa_table_datatables_responsive($sql_0001, 'Edizioni Disponibili', 'tabella_base1', 'blue');
            echo '</div></div>';
            
            echo '<div class="row"><div class="col-md-12 col-sm-12">';
            $sql_0001 = "SELECT 
            data, ora, etichetta As Tipo, oggetto, 
            (SELECT CONCAT(cognome, ' ', nome) FROM lista_professionisti WHERE id=id_professionista) AS 'Iscritto', stato,
            CONCAT('<a class=\"btn btn-circle btn-icon-only red-thunderbird btn-outline\" href=\"cancella.php?tbl=calendario_corsi&idCalendario=',id,'&idCalendarioCorso=',id_calendario_0,'&idIscrizione=',id_iscrizione,'\" title=\"DISISCRIVI DAL CORSO\" alt=\"DISISCRIVI DAL CORSO\"><i class=\"fa fa-user-times\"></i></a>') AS 'fa-user-times' 
            FROM calendario
            WHERE id_prodotto='" . $idProdotto."'
            AND id_calendario_0 = '".$id."'
            AND etichetta LIKE 'Iscrizione Corso'
            ORDER BY data DESC, ora ASC";
            stampa_table_datatables_responsive($sql_0001, 'Edizioni - Iscrizioni', 'tabella_base2',  'green-steel');
            echo '</div></div>';
            }else{
            echo '<div class="row"><div class="col-md-12 col-sm-12">';
            $sql_0001 = "SELECT   CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"modifica.php?tbl=calendario_esami&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',
            data, ora,  IF(etichetta LIKE 'Calendario Esami',CONCAT('<span class=\"btn sbold uppercase btn-outline blue\">',etichetta,'</span>'),CONCAT('<span class=\"btn sbold uppercase btn-outline red-thunderbird\">',etichetta,'</span>')) AS 'Tipo', oggetto, numerico_10 AS 'Iscritti', numerico_4  AS 'Costo Aula', numerico_5 AS 'Costo Docenti', stato
            FROM calendario
            WHERE id_prodotto='" . $idProdotto."'
            AND id = '".$id."'
            AND etichetta LIKE 'Calendario Esami'
            ORDER BY data DESC, ora ASC";
            stampa_table_datatables_responsive($sql_0001, 'Esami Disponibili', 'tabella_base3', 'blue-steel');
            echo '</div></div>';
         
            echo '<div class="row"><div class="col-md-12 col-sm-12">';
            $sql_0001 = "SELECT 
            data, ora, etichetta As Tipo, oggetto, 
            (SELECT CONCAT(cognome, ' ', nome) FROM lista_professionisti WHERE id=id_professionista) AS 'Iscritto', stato,
            CONCAT('<a class=\"btn btn-circle btn-icon-only red-thunderbird btn-outline\" href=\"cancella.php?tbl=calendario_esami&idCalendario=',id,'&idCalendarioCorso=',id_calendario_0,'&idIscrizione=',id_iscrizione,'\" title=\"DISISCRIVI DAL CORSO\" alt=\"DISISCRIVI DAL CORSO\"><i class=\"fa fa-user-times\"></i></a>') AS 'fa-user-times' 
            FROM calendario
            WHERE id_prodotto='" . $idProdotto."'
            AND id_calendario_0 = '".$id."'
            AND etichetta LIKE 'Iscrizione Esame'
            ORDER BY data DESC, ora ASC";
            stampa_table_datatables_responsive($sql_0001, 'Esami - Iscrizioni', 'tabella_base4', 'green');
            echo '</div></div>';
            }
        break;
        
        case 'lista_corsi':
            echo '<div class="row"><div class="col-md-12 col-sm-12">';
            $sql_00001 = "UPDATE lista_corsi, lista_prodotti
            SET lista_corsi.nome_prodotto = lista_prodotti.nome
            WHERE lista_corsi.id_prodotto = lista_prodotti.id";
            $rs_00001 = $dblink->query($sql_00001);
            
            $sql_00001 = "SELECT SUM(durata) as durata_corso FROM lista_corsi_dettaglio WHERE id_corso='".$id."'";
            $durata_corso = $dblink->get_field($sql_00001);
            
            $sql_00002 = "UPDATE lista_corsi
            SET lista_corsi.durata = '".$durata_corso."'
            WHERE lista_corsi.id=".$id."";
            $rs_00002 = $dblink->query($sql_00002);

            $sql_0001 = "SELECT CONCAT('<H3>',nome_prodotto,'</H3>') AS 'Corso', 
            (SELECT codice FROM lista_prodotti WHERE id = id_prodotto LIMIT 1) AS 'Codice', 
            (SELECT codice_esterno FROM lista_prodotti WHERE id = id_prodotto LIMIT 1) AS 'ID MOODLE', durata, stato 
            FROM `lista_corsi` WHERE id ='".$id."'";
            stampa_table_static_basic($sql_0001, '', 'Corso', 'green-haze');
            echo '</div></div>';
            echo '<div class="row"><div class="col-md-12 col-sm-12">';
            $sql_0001 = "SELECT id, 
            CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"modifica.php?tbl=lista_corsi_configurazioni&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa fa-edit',
            CONCAT('<a class=\"btn btn-circle btn-icon-only red btn-outline\" href=\"cancella.php?tbl=lista_corsi_configurazioni&id=',id,'\" title=\"ELIMINA\" alt=\"ELIMINA\"><i class=\"fa fa-trash\"></i></a>') AS 'fa fa-trash', 
            codice_corso AS 'Codice', id_classe AS 'Classe', professione, data_inizio, data_fine, crediti, durata_corso, avanzamento,  codice_accreditamento AS 'Cod. Accr.',
            id_attestato AS 'Attestato PDF' 
            FROM `lista_corsi_configurazioni` WHERE id_corso ='".$id."' AND titolo NOT LIKE 'Base'";
            echo "<form enctype=\"multipart/form-data\" role=\"form\" action=\"salva.php?tbl=lista_corsi_configurazioni&idCorso=' . $id . '&fn=salvaConfigurazioneCorso\" method=\"POST\">";
            stampa_table_static_basic_input('lista_corsi_configurazioni', $sql_0001, '', 'Configurazione', 'green-haze', false, 1);
            echo '<center><a href="salva.php?tbl=lista_corsi&idCorso=' . $id . '&fn=aggiungiConfigurazioneCorso" class="btn blue-steel">
                        <i class="fa fa-plus"></i> Aggiungi Configurazione
                        </a>&nbsp;&nbsp;&nbsp;<button type="submit" class="btn green-meadow">
                        <i class="fa fa-save"></i>
                        Salva Configurazione
                        </button></center><br />';
            echo '</div></div>';
            
            echo '<div class="row"><div class="col-md-12 col-sm-12">';
            $numRowBase = $dblink->num_rows("SELECT * FROM lista_corsi_configurazioni WHERE id_corso = '$id' AND titolo LIKE 'Base'");
            if($numRowBase <= 0){
                $dblink->query("INSERT INTO `lista_corsi_configurazioni` (`id`, `dataagg`, `scrittore`, `stato`, `id_corso`, `id_prodotto`,titolo, avanzamento, id_attestato, messaggio, firma) SELECT '', NOW(), '".addslashes($_SESSION['cognome_nome_utente'])."', 'Non Attivo', id, id_prodotto, 'Base', '80.00', '9', '<h2>ATTESTATO di FREQUENZA</h2>Si attesta che<br>nel periodo dal _XXX_DATA_INIZIO_XXX_  al _XXX_DATA_FINE_XXX_ <br><br><h1>_XXX_TITOLO_XXX_ _XXX_COGNOME_XXX_ _XXX_NOME_XXX_</h1>nato a _XXX_LUOGO_NASCITA_XXX_  (_XXX_PROV_NASCITA_XXX_) il _XXX_DATA_NASCITA_XXX_ <br><br><br>ha frequentato il corso di<h3>\" _XXX_NOME_CORSO_XXX_ \"</h3>Durata del percorso formativo: <b>_XXX_ORE_CORSO_XXX_ ore</b><br>Codice: <b>_XXX_CODICE_ACCREDITAMENTO_XXX_</b><br>Crediti Formativi Professionali: <b>_XXX_NUMERO_CREDITI_XXX_</b>', '<b>Lugo (RA), _XXX_DATA_FIRMA_XXX_</b>' FROM lista_corsi WHERE id='".$id."'");
            }
            $sql_0001 = "SELECT id, 
            CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"modifica.php?tbl=lista_corsi_configurazioni&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa fa-edit',
            codice_corso AS 'Codice', crediti, durata_corso, avanzamento,  codice_accreditamento AS 'Cod. Accr.',
            id_attestato AS 'Attestato PDF' 
            FROM `lista_corsi_configurazioni` WHERE id_corso ='".$id."' AND titolo LIKE 'Base'";
            echo "<form enctype=\"multipart/form-data\" role=\"form\" action=\"salva.php?tbl=lista_corsi_configurazioni&idCorso=' . $id . '&fn=salvaConfigurazioneCorso\" method=\"POST\">";
            stampa_table_static_basic_input('lista_corsi_configurazioni', $sql_0001, '', 'Configurazione Base', 'blue-hoki');
            echo '<center><button type="submit" class="btn green-meadow">
                        <i class="fa fa-save"></i>
                        Salva Configurazione
                        </button></center><br />';
            echo '</div></div>';
            
             echo '<div class="row"><div class="col-md-12 col-sm-12">';
            $sql_0001 = "SELECT CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"modifica.php?tbl=calendario_esami&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',
            data, ora, oggetto, stato
            FROM calendario
            WHERE id_corso='".$id."' 
            AND etichetta LIKE 'Calendario Esami'
            ORDER BY data DESC, ora ASC";
            stampa_table_static_basic($sql_0001, '', 'Esami', 'blue-steel');
            echo '<center><a href="salva.php?tbl=lista_corsi&idCorsoNuovoEsame=' . $id . '&fn=aggiungiEsameCorso" class="btn green-meadow">
                        Aggiungi Esame
                        <i class="fa fa-plus"></i>
                        </a></center><br />';
            echo '</div></div>';
            
             echo '<div class="row"><div class="col-md-12 col-sm-12">';
            //$sql_0001 = "SELECT id, `ordine`, `id_modulo`, `url`, `name`, `instance`, `visible`, `modicon`, `modname`, `modplural`, `availability`, `indent`
            //FROM lista_corsi_dettaglio WHERE id_corso=".$id;
            $sql_0001 = "SELECT 
            CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"modifica.php?tbl=lista_corsi_dettaglio&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit', 
            CONCAT('<H4>',`name`,'</H4>') AS 'Nome', 
            `modname` AS 'Tipo', durata,
            IF(`visible`>=1,'Attivo','Non Attivo') AS 'Stato',
             `ordine`, `id_modulo`,          
              `instance` AS 'ISTANCE MOODLE'
            FROM lista_corsi_dettaglio WHERE id_corso='".$id."' ORDER BY ordine ASC";
            stampa_table_static_basic($sql_0001, '', 'Moduli', 'green-haze');
            echo '</div></div>';
            echo '<div class="row">';
            echo '<div class="col-md-6 col-sm-6">';
            $sql_0002 = "SELECT SUM((prezzo_prodotto*quantita)) AS '&euro;' "
                    . "FROM lista_preventivi_dettaglio "
                    . "WHERE id_preventivo= '".$id."'";
            //stampa_table_static_basic($sql_0002, '', 'Totale Imponibile', 'red');
            echo '</div>';
            echo '<div class="col-md-6 col-sm-6">';
            $sql_0002 = "SELECT SUM((prezzo_prodotto*(1+(iva_prodotto/100)))*quantita) AS '&euro;' "
                    . "FROM lista_preventivi_dettaglio "
                    . "WHERE id_preventivo= '".$id."'";
            //stampa_table_static_basic($sql_0002, '', 'Totale Importo', 'green-meadow');
            echo '</div>';
            
            /*$sql_00006 = "SELECT CONCAT('<H3>',nome_prodotto,'</H3>') AS 'corso', 
            (SELECT codice FROM lista_prodotti WHERE id = id_prodotto LIMIT 1) AS 'codice', 
            (SELECT codice_esterno FROM lista_prodotti WHERE id = id_prodotto LIMIT 1) AS 'id_moodle', id_prodotto, durata, stato 
            FROM `lista_corsi` WHERE id ='".$id."' LIMIT 1";
            $rs_00006 = mysql_query($sql_00006);
            if ($rs_00006) {

                while ($row_00006 = mysql_fetch_array($rs_00006, MYSQL_BOTH)) {
                    $nome_Corso = $row_00006['corso'];
                    $codice_corso = $row_00006['codice'];
                    $id_moodle = $row_00006['id_moodle'];
                    $id_prodotto = $row_00006['id_prodotto'];
                }
            }*/
            
            /*echo '<center><a href="'.BASE_URL.'/libreria/automazioni/sinc_moodle.php?fn=lista_corsi_dettaglio&id='.$id_moodle.'&idProd='.$id_prodotto.'&idCorso='.$id.'" class="btn yellow-gold">
                Aggiorna Corso
                <i class="fa fa-refresh"></i>
                </a></center>';*/
            break;

    }
    return;
}
?>
