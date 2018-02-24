<?php

/** FUNZIONI DI CROCCO */
function Stampa_HTML_index_Commerciali($tabella){
    global $table_listaCommerciali, $table_listaConsuntivoVendite, $where_lista_consuntivo_vendite, $table_listaEsamiCorsiCommerciali;

    switch($tabella){
    
        case 'lista_esami_corsi_commerciali':
            $tabella = "calendario";
            $campi_visualizzati = $table_listaEsamiCorsiCommerciali['index']['campi'];
            $where = $table_listaEsamiCorsiCommerciali['index']['where'];
            $ordine = $table_listaEsamiCorsiCommerciali['index']['order'];
            $titolo = 'Calendario Esami & Corsi';
            $limit = "LIMIT 1";
            $sql_0001 = "SELECT ".$campi_visualizzati." FROM ".$tabella." WHERE $where $ordine $limit";
            stampa_table_datatables_ajax($sql_0001, "datatable_ajax", $titolo, '', '', false);        
        break;
    
        case 'lista_consuntivo_vendite':
            $tabella = "lista_preventivi";
            $campi_visualizzati = $table_listaConsuntivoVendite['index']['campi'];
            $where = $table_listaConsuntivoVendite['index']['where'];
            $ordine = $table_listaConsuntivoVendite['index']['order'];
            $titolo = 'Consuntivo Vendite';
            $limit = "LIMIT 1";
            $sql_0001 = "SELECT ".$campi_visualizzati." FROM ".$tabella." WHERE $where $ordine $limit";
            stampa_table_datatables_ajax($sql_0001, "datatable_ajax", $titolo, '', '', false);
        break;
        
        case 'lista_password':
            $tabella = "lista_password";
            $campi_visualizzati = $table_listaCommerciali['index']['campi'];
            $where = $table_listaCommerciali['index']['where'];
            $ordine = $table_listaCommerciali['index']['order'];
            $titolo = 'Elenco Commerciali';
            $sql_0001 = "SELECT ".$campi_visualizzati." FROM ".$tabella." WHERE $where $ordine";
            stampa_table_datatables_responsive($sql_0001, $titolo, 'tabella_base');
        break;
    }
}

function Stampa_HTML_Dettaglio_Commerciali($tabella, $id) {
    global $dblink, $where_calendario, $where_intervallo_all, $where_intervallo_fatture_all, $titolo_intervallo,
            $where_lista_preventivi, $where_lista_fatture;
    
    switch ($tabella) {
        case 'lista_password':
            echo '<div class="row"><div class="col-md-12 col-sm-12">';
            $sql_0001 = "SELECT CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"modifica.php?id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',  `livello`, `nome`, `cognome`, `cellulare`, `email`, `stato` 
            FROM lista_password WHERE id=" . $id;
            stampa_table_static_basic($sql_0001, '', 'Commerciale', '');
            echo '</div></div>';
            
            echo '<div class="row">';
            echo '<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">';
                //AND MONTH(datainsert)=MONTH(CURDATE())
                //AND YEAR(datainsert)=YEAR(CURDATE())
                
                $sql_007 = "SELECT COUNT(etichetta) AS conteggio FROM calendario 
                WHERE 1 $where_calendario 
                AND etichetta LIKE 'Nuova Richiesta'
                AND (stato LIKE 'Mai Contattato' OR stato LIKE 'Richiamare')
                AND id_agente='".$id."' GROUP BY id_agente";
                //echo '<li>$sql_007 = '.$sql_007.'</li>';
                $titolo = 'Richieste da Gestire';
                $icona = 'fa fa-building';
                $colore = 'yellow-saffron';
                $link = '';
                stampa_dashboard_stat_v2($sql_007, $titolo, $icona, $colore, $link);
            echo '</div>';
            echo '<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">';
                $sql_007 = "SELECT COUNT(etichetta) AS conteggio FROM calendario 
                WHERE 1 $where_calendario 
                AND YEAR(datainsert)=YEAR(CURDATE())
                AND etichetta LIKE 'Nuova Richiesta'
                AND id_agente='".$id."' GROUP BY id_agente";
                $titolo = 'Nuove Richieste ANNO';
                $icona = 'fa fa-building';

                $colore = 'yellow';
                $link = '';
                stampa_dashboard_stat_v2($sql_007, $titolo, $icona, $colore, $link);
            echo '</div>';
            echo '<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">';
                $sql_008 = "SELECT SUM(imponibile) AS conteggio FROM lista_preventivi WHERE id_agente='".$id."' AND (stato LIKE 'Chiuso' OR stato LIKE 'Venduto') GROUP BY id_agente ";
                $titolo = 'Tot. Ordinato';
                $icona = 'fa fa-building';
                $colore = 'red';
                $link = '';
                stampa_dashboard_stat_v2($sql_008, $titolo, $icona, $colore, $link);
            echo '</div>';
            echo '<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">';
                $sql_009 = "SELECT SUM(imponibile) AS conteggio FROM lista_fatture WHERE id_agente='".$id."' AND (stato LIKE 'In Attesa' OR stato LIKE 'Pagata') GROUP BY id_agente ";
                $titolo = 'Tot. Fatturato';
                $icona = 'fa fa-building';
                $colore = 'purple';
                $link = '';
                stampa_dashboard_stat_v2($sql_009, $titolo, $icona, $colore, $link);
            echo '</div></div>';
            echo '<div class="row"><div class="col-md-12 col-sm-12">';
            $sql_0010 = "SELECT 
                            (SELECT COUNT(*) AS conteggio_gestite FROM lista_preventivi AS lp WHERE (lp.stato LIKE 'Negativo' OR lp.stato LIKE 'Venduto' OR lp.stato LIKE 'Chiuso') AND lp.id_agente=ag.id $where_intervallo_all) AS Gestite,
                            (SELECT COUNT(*) AS conteggio_venduti FROM lista_preventivi AS lp WHERE (lp.stato LIKE 'Venduto' OR lp.stato LIKE 'Chiuso') AND lp.id_agente=ag.id $where_intervallo_all) AS Venduti,
                            (SELECT IF(SUM(lp.imponibile)>0, SUM(lp.imponibile), 0) AS conteggio_preventivi FROM lista_preventivi AS lp WHERE (lp.stato LIKE 'Venduto' OR lp.stato LIKE 'Chiuso') AND lp.id_agente=ag.id $where_intervallo_all) AS Venduto_Netto,
                            (SELECT IF(SUM(lf.imponibile)>0, SUM(lf.imponibile), 0) AS conteggio_fatture FROM lista_fatture AS lf WHERE (lf.stato LIKE 'In Attesa' OR lf.stato LIKE 'Pagata') AND lf.id_agente=ag.id $where_intervallo_fatture_all) AS Fatturato_Netto,
                            (SELECT IF(SUM(lf.imponibile)>0, (0-SUM(lf.imponibile)), 0) AS conteggio_annullate FROM lista_fatture AS lf WHERE (lf.stato LIKE 'Nota di Credito%') AND lf.tipo LIKE 'Nota di Credito%' AND lf.id_agente=ag.id $where_intervallo_fatture_all) AS Fatture_Annullate,
                            (SELECT ROUND((COUNT(id)*100)/Gestite, 2) AS conteggio_venduti FROM lista_preventivi AS lp WHERE (lp.stato LIKE 'Venduto' OR lp.stato LIKE 'Chiuso') AND lp.id_agente=ag.id $where_intervallo_all) AS 'Realizzato %',
                            (SELECT IF(SUM(lf.imponibile)>0, ROUND(SUM(lf.imponibile)/Gestite,2), 0) AS media_fatture FROM lista_fatture AS lf WHERE (lf.stato LIKE 'In Attesa' OR lf.stato LIKE 'Pagata') AND lf.id_agente=ag.id $where_intervallo_fatture_all) AS 'Media_di_Vendita',
                            (SELECT IF(SUM(lf.imponibile)>0, SUM(lf.imponibile), 0) AS fatture_incassate FROM lista_fatture AS lf WHERE (lf.stato LIKE 'Pagata') AND lf.id_agente=ag.id $where_intervallo_fatture_all) AS Incassato,
                            (SELECT IF(SUM(lf.imponibile)>0, SUM(lf.imponibile), 0) AS fatture_da_incassate FROM lista_fatture AS lf WHERE (lf.stato LIKE 'In Attesa') AND lf.id_agente=ag.id $where_intervallo_fatture_all) AS Da_Incassare
                            FROM lista_password AS ag WHERE ag.livello='commerciale' AND ag.stato = 'Attivo' AND id='$id'";
                            stampa_table_datatables_responsive($sql_0010, "Statistiche del commerciale".$titolo_intervallo, "");
            echo '</div></div>';
            /*echo '<div class="row"><div class="col-md-12 col-sm-12">';
            $sql_temp_001 = "CREATE TEMPORARY TABLE tmp_stat_preventivi SELECT DISTINCT DAY(dataagg) AS giorno, MONTH(dataagg) AS mese, YEAR(dataagg) AS anno,
                            (SELECT COUNT(*) AS conteggio_gestite FROM lista_preventivi AS lp WHERE (lp.stato LIKE 'Negativo' OR lp.stato LIKE 'Venduto' OR lp.stato LIKE 'Chiuso') AND lp.id_agente=ag.id_agente AND YEAR(lp.dataagg)=YEAR(ag.dataagg) AND MONTH(lp.dataagg)=MONTH(ag.dataagg) AND DAY(lp.dataagg)=DAY(ag.dataagg)) AS gestite,
                            (SELECT COUNT(*) AS conteggio_venduti FROM lista_preventivi AS lp WHERE (lp.stato LIKE 'Venduto' OR lp.stato LIKE 'Chiuso') AND lp.id_agente=ag.id_agente AND YEAR(lp.dataagg)=YEAR(ag.dataagg) AND MONTH(lp.dataagg)=MONTH(ag.dataagg) AND DAY(lp.dataagg)=DAY(ag.dataagg)) AS venduti,
                            (SELECT IF(SUM(lp.imponibile)>0, SUM(lp.imponibile), 0) AS conteggio_preventivi FROM lista_preventivi AS lp WHERE (lp.stato LIKE 'Venduto' OR lp.stato LIKE 'Chiuso') AND lp.id_agente=ag.id_agente AND YEAR(lp.dataagg)=YEAR(ag.dataagg) AND MONTH(lp.dataagg)=MONTH(ag.dataagg) AND DAY(lp.dataagg)=DAY(ag.dataagg)) AS venduto_netto,
                            (SELECT if(gestite>0,ROUND((COUNT(id)*100)/gestite, 2),0) AS conteggio_venduti FROM lista_preventivi AS lp WHERE (lp.stato LIKE 'Venduto' OR lp.stato LIKE 'Chiuso') AND lp.id_agente=ag.id_agente AND YEAR(lp.dataagg)=YEAR(ag.dataagg) AND MONTH(lp.dataagg)=MONTH(ag.dataagg) AND DAY(lp.dataagg)=DAY(ag.dataagg)) AS realizzato,
                            0 AS fatturato_netto, 0 AS fatture_annullate, 0 AS incassate, 0 AS da_incassare
                            FROM lista_preventivi AS ag WHERE id_agente='$id' GROUP BY DAY(dataagg), MONTH(dataagg), YEAR(dataagg);";
            $dblink->query($sql_temp_001);
            
            //stampa_table_datatables_responsive("SELECT * FROM tmp_statatistiche", "Statistiche del commerciale".$titolo_intervallo);
                            
            $sql_temp_002 = "CREATE TEMPORARY TABLE tmp_stat_fatture SELECT DISTINCT DAY(dataagg) AS giorno, MONTH(dataagg) AS mese, YEAR(dataagg) AS anno, 0 AS gestite, 0 AS venduti, 0 AS venduto_netto, 0 AS realizzato,
                            (SELECT IF(SUM(lf.imponibile)>0, SUM(lf.imponibile), 0) AS conteggio_fatture FROM lista_fatture AS lf WHERE (lf.stato LIKE 'In Attesa' OR lf.stato LIKE 'Pagata') AND lf.id_agente=ag.id_agente AND YEAR(lf.dataagg)=YEAR(ag.dataagg) AND MONTH(lf.dataagg)=MONTH(ag.dataagg) AND DAY(lf.dataagg)=DAY(ag.dataagg)) AS fatturato_netto,
                            (SELECT IF(SUM(lf.imponibile)>0, SUM(lf.imponibile), 0) AS conteggio_annullate FROM lista_fatture AS lf WHERE (lf.stato LIKE 'Nota di Credito%') AND lf.tipo LIKE 'Nota di Credito%' AND lf.id_agente=ag.id_agente AND YEAR(lf.dataagg)=YEAR(ag.dataagg) AND MONTH(lf.dataagg)=MONTH(ag.dataagg) AND DAY(lf.dataagg)=DAY(ag.dataagg)) AS fatture_annullate,
                            (SELECT IF(SUM(lf.imponibile)>0, SUM(lf.imponibile), 0) AS fatture_incassate FROM lista_fatture AS lf WHERE (lf.stato LIKE 'Pagata') AND lf.id_agente=ag.id_agente AND YEAR(lf.dataagg)=YEAR(ag.dataagg) AND MONTH(lf.dataagg)=MONTH(ag.dataagg) AND DAY(lf.dataagg)=DAY(ag.dataagg)) AS incassate,
                            (SELECT IF(SUM(lf.imponibile)>0, SUM(lf.imponibile), 0) AS fatture_da_incassate FROM lista_fatture AS lf WHERE (lf.stato LIKE 'In Attesa') AND lf.id_agente=ag.id_agente AND YEAR(lf.dataagg)=YEAR(ag.dataagg) AND MONTH(lf.dataagg)=MONTH(ag.dataagg) AND DAY(lf.dataagg)=DAY(ag.dataagg)) AS da_incassare
                            FROM lista_fatture AS ag WHERE id_agente='$id' GROUP BY DAY(dataagg), MONTH(dataagg), YEAR(dataagg);";
            $dblink->query($sql_temp_002);
            
            $sql_temp_003 = "CREATE TEMPORARY TABLE tmp_statatistiche (SELECT * FROM tmp_stat_preventivi) UNION (SELECT * FROM tmp_stat_fatture) ORDER BY giorno, mese, anno;";
            $dblink->query($sql_temp_003);          
            
            $sql_0020 = "SELECT mese, anno, SUM(gestite) as Richieste_Gestite, SUM(venduti) as Richieste_Vendute, SUM(venduto_netto) AS venduto_netto, SUM(fatturato_netto) AS fatturato_netto, (0-SUM(fatture_annullate)) AS fatture_annullate, ROUND((SUM(venduti)*100)/SUM(gestite), 2) AS 'Realizzato %', SUM(incassate) AS incassate, SUM(da_incassare) AS da_incassare FROM tmp_statatistiche GROUP BY mese, anno ORDER BY giorno, mese, anno;";
            
            stampa_table_datatables_responsive($sql_0020, "Andamento per mese".$titolo_intervallo, "tabella_base1");
            echo '</div></div>';
            */
            echo '<div class="row"><div class="col-md-12 col-sm-12">';
            echo "<form enctype=\"multipart/form-data\" role=\"form\" action=\"#\" method=\"POST\">";
                $sql_0002 = "SELECT IF(id_professionista>0,CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"".BASE_URL."/moduli/anagrafiche/dettaglio_tab.php?tbl=lista_professionisti&id=',id_professionista,'\" title=\"SCHEDA\" alt=\"SCHEDA\"><i class=\"fa fa-book\"></i></a>'),
                        CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"".BASE_URL."/moduli/anagrafiche/dettaglio_tab.php?tbl=calendario&id=',id,'\" title=\"SCHEDA\" alt=\"SCHEDA\"><i class=\"fa fa-book\"></i></a>')) AS 'fa-book',
                        id_agente AS 'Commerciale',
                        dataagg AS 'Data',
                        oggetto AS 'Oggetto', 
                        IF(id_professionista>0,(SELECT CONCAT(cognome,' ', nome) FROM lista_professionisti WHERE id = id_professionista),mittente) AS 'Mittente', 
                        campo_5 AS 'E-Mail', campo_4 AS 'Telefono', stato,
                        CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"modifica.php?tbl=calendario&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',
                        CONCAT('<a class=\"btn btn-circle btn-icon-only red btn-outline\" href=\"cancella.php?tbl=calendario&id=',id,'\" title=\"ELIMINA\" alt=\"ELIMINA\"><i class=\"fa fa-trash\"></i></a>') AS 'fa-trash' ,
                        id AS 'selezione'
                        FROM `calendario` 
                        WHERE 1 $where_calendario 
                        AND etichetta LIKE 'Nuova Richiesta'
                        AND ( stato LIKE 'Mai Contattato' OR stato LIKE 'Richiamare')
                        AND id_agente=" . $id." ORDER BY dataagg DESC LIMIT 1";
                //stampa_table_static_basic($sql_0002, '', 'Richieste - RIchiamare / Mai Contattato', 'red');
                stampa_table_datatables_ajax($sql_0002, "#datatable_ajax_1", 'Richieste - RIchiamare / Mai Contattato', 'datatable_ajax_1', 'red');
                //stampa_table_static_basic_input('calendario', $sql_0002, '', 'Nuove Richieste - '.$stato_nuova_richiesta, 'green-haze');
            echo '</form>';
            echo '</div><div style="text-align: center; margin-bottom: 15px;"> 
                    <button id="associaCommerciale" type="button" class="btn btn-icon purple-studio" alt="ASSOCIA COMMERCIALE" title="ASSOCIA COMMERCIALE"><i class="fa fa-sign-in"></i> Associa Commerciale</a></button>
                </div></div>';
            
            echo '<div class="row"><div class="col-md-12 col-sm-12">';
            
            $sql_0003 = "SELECT IF(id_professionista>0,CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"".BASE_URL."/moduli/anagrafiche/dettaglio_tab.php?tbl=lista_professionisti&id=',id_professionista,'\" title=\"SCHEDA\" alt=\"SCHEDA\"><i class=\"fa fa-book\"></i></a>'),
                        CONCAT('<a class=\"btn btn-circle btn-icon-only green btn-outline\" href=\"".BASE_URL."/moduli/anagrafiche/dettaglio_tab.php?tbl=calendario&id=',id,'\" title=\"SCHEDA\" alt=\"SCHEDA\"><i class=\"fa fa-book\"></i></a>')) AS 'fa-book',
                        id_agente AS 'Commerciale',
                        dataagg AS 'Data',
                        oggetto AS 'Oggetto', 
                        IF(id_professionista>0,(SELECT CONCAT(cognome,' ', nome) FROM lista_professionisti WHERE id = id_professionista),mittente) AS 'Mittente', 
                        campo_5 AS 'E-Mail', campo_4 AS 'Telefono', stato,
                        CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"modifica.php?tbl=calendario&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',
                        CONCAT('<a class=\"btn btn-circle btn-icon-only red btn-outline\" href=\"cancella.php?tbl=calendario&id=',id,'\" title=\"ELIMINA\" alt=\"ELIMINA\"><i class=\"fa fa-trash\"></i></a>') AS 'fa-trash' 
                        FROM `calendario` 
                        WHERE 1 $where_calendario 
                        AND etichetta LIKE 'Nuova Richiesta'
                        AND ( stato NOT LIKE 'Mai Contattato' AND stato NOT LIKE 'Richiamare')
                        AND id_agente=" . $id." ORDER BY dataagg DESC LIMIT 1";
            //stampa_table_static_basic($sql_0003, '', 'Altre Richieste', '');
            stampa_table_datatables_ajax($sql_0003, "#datatable_ajax_2", 'Altre Richieste', 'datatable_ajax_2', '');
            //stampa_table_static_basic_input('calendario', $sql_0002, '', 'Nuove Richieste - '.$stato_nuova_richiesta, 'green-haze');
            echo '</div></div>';
            echo '<div class="row"><div class="col-md-12 col-sm-12">';
            $sql_0004 = "SELECT 
                        CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"".BASE_URL."/moduli/preventivi/dettaglio.php?tbl=lista_preventivi&id=',id,'\" title=\"DETTAFLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',
                        CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"".BASE_URL."/moduli/preventivi/modifica.php?tbl=lista_preventivi&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',
                        data_creazione AS 'Creato',
                        codice AS 'Cod.',
                        imponibile,
                        stato
                        FROM `lista_preventivi` 
                        WHERE 1 $where_lista_preventivi 
                        AND id_agente='" . $id."' ORDER BY dataagg DESC LIMIT 1";
            //stampa_table_datatables_responsive($sql_0004, 'Ordini', 'tabella_base3');
            stampa_table_datatables_ajax($sql_0004, "#datatable_ajax_3", 'Ordini', 'datatable_ajax_3');
            echo '</div></div>';
            
            echo '<div class="row"><div class="col-md-12 col-sm-12">';
            $sql_0005 = "SELECT 
                        CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"".BASE_URL."/moduli/fatture/dettaglio.php?tbl=lista_fatture&id=',id,'\" title=\"DETTAFLIO\" alt=\"DETTAGLIO\"><i class=\"fa fa-search\"></i></a>') AS 'fa-search',
                        CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"".BASE_URL."/moduli/fatture/modifica.php?tbl=lista_fatture&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',
                        data_creazione AS 'Creato',
                        codice AS 'Cod.',
                        imponibile,
                        stato
                        FROM `lista_fatture` 
                        WHERE 1 $where_lista_fatture 
                        AND id_agente='" . $id."' ORDER BY dataagg DESC LIMIT 1";
            //stampa_table_datatables_responsive($sql_0005, 'Fatture', 'tabella_base4', 'blue-steel');
            stampa_table_datatables_ajax($sql_0005, "#datatable_ajax_4", 'Fatture', 'datatable_ajax_4', 'blue-steel');
            echo '</div></div>';
            /*
            $sql_test ="SELECT `".MOODLE_DB_NAME."`.`mdl_user`.`id` , `".MOODLE_DB_NAME."`.`mdl_user`.email,

`betaform_erp`.`lista_password`.id, `betaform_erp`.`lista_password`.email

FROM `".MOODLE_DB_NAME."`.`mdl_user` INNER JOIN `betaform_erp`.`lista_password` ON `".MOODLE_DB_NAME."`.`mdl_user`.id= `betaform_erp`.`lista_password`.id_moodle_user 

WHERE 1 LIMIT 0,10";
stampa_table_static_basic($sql_test, '', 'Ordini', '');*/
            
            //SELECT `id`, `dataagg`, `scrittore`, `stato`, `id_iscrizione`, `id_corso`, `id_classe`, `id_professionista`, `id_modulo`, `completato`, `cmid`, `modname`, `instance`, `state`, `timecompleted`, `tracking` FROM `lista_iscrizioni_dettaglio` WHERE 1
        break;
    }
}
?>
