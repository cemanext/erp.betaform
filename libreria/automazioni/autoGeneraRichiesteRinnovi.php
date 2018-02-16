<?php
include_once('../../config/connDB.php');
include_once(BASE_ROOT . 'config/confAccesso.php');
include_once(BASE_ROOT . 'classi/webservice/client.php');

if (DISPLAY_DEBUG) {
    echo '<li>'.date('Y-m-d H:i:s').'</li>';
    echo '<li>DB_HOST = '.DB_HOST.'</li>';
    echo '<li>DB_USER = '.DB_USER.'</li>';
    echo '<li>DB_PASS = '.DB_PASS.'</li>';
    echo '<li>DB_NAME = '.DB_NAME.'</li>';
    echo '<hr>';
}

$Sql_tmp_0002 = "CREATE TEMPORARY TABLE listaRinnoviAbbonamentiDaInviare (SELECT id as id_iscrizione, scrittore, id_professionista, abbonamento, id_classe, id_corso, data_fine_iscrizione FROM lista_iscrizioni WHERE data_fine_iscrizione = DATE_ADD(CURDATE(), INTERVAL 30 DAY) AND (stato LIKE 'Configurazione') AND abbonamento=1 GROUP BY id_professionista, abbonamento ORDER BY `lista_iscrizioni`.`data_fine_iscrizione` ASC);";
$dblink->query($Sql_tmp_0002);

$sql_000002 = "SELECT * FROM listaRinnoviAbbonamentiDaInviare";// UNION SELECT * FROM listaRinnoviCorsiDaCaricare";

$rs_00000002 = $dblink->get_results($sql_000002);
if (DISPLAY_DEBUG) echo '<ol>';
foreach ($rs_00000002 AS $row_00000001) {
    
    $idProfessionista = $row_00000001['id_professionista'];
    $idIscrizione = $row_00000001['id_iscrizione'];
    
    $sql_template = "SELECT * FROM lista_template_email WHERE nome = 'mailRinnovoAbbonamento'";
    $rs_template = $dblink->get_results($sql_template);
    foreach ($rs_template as $row_template) {
        $oggetto_da_inviare = $row_template['oggetto'];
        $messaggio_da_inviare = html_entity_decode($row_template['messaggio']);
    }
    
    $rowProfessionista = $dblink->get_row("SELECT * FROM lista_professionisti WHERE id='".$idProfessionista."'", true);
    
    $id_azienda =  ottieniIdAzienda($idProfessionista);
    
    $rowCampagna = $dblink->get_row("SELECT id AS id_campagna, id_tipo_marketing, nome, id_prodotto, url_1 FROM lista_campagne WHERE id = '754'", true);
    $rowMarketing = $dblink->get_row("SELECT id AS id_tipo_marketing, nome FROM lista_tipo_marketing WHERE id = '86'", true);
    if($row_00000001['abbonamento']=="1"){
        $rowProdotto = $dblink->get_row("SELECT * FROM lista_prodotti WHERE codice_esterno = 'abb_".$row_00000001['id_classe']."'", true);
        if (DISPLAY_DEBUG) echo "<br>".$dblink->get_query();
    }else{
        $rowProdotto = $dblink->get_row("SELECT * FROM lista_prodotti INNER JOIN lista_corsi ON lista_prodotti.id = lista_corsi.id_prodotto WHERE lista_corsi.id = '".$row_00000001['id_corso']."'", true);
        if (DISPLAY_DEBUG) echo "<br>".$dblink->get_query();
    }
    if(empty($rowCampagna['url_1'])){
    
        if($id_azienda <= 0){
            $variabili = base64_encode("/carrello/dati-fattura/?betaformazione_utente_id=$idProfessionista&betaformazione_fatturazione_id=$id_azienda|".$rowProdotto['prezzo_min']);
        }else{
            $variabili = base64_encode("/carrello/pagamento/?betaformazione_utente_id=$idProfessionista&betaformazione_fatturazione_id=$id_azienda|".$rowProdotto['prezzo_min']);
        }
        $linkShop = "<a href=\"".WP_DOMAIN_NAME."/carrello/?a=".$rowProdotto['id']."&idCampagna=754&r=$variabili\">Voglio rinnovare il mio abbonamento</a><br /><br />Oppure copia e incolla questo link:<br>".WP_DOMAIN_NAME."/carrello/?a=".$rowProdotto['id']."&r=$variabili";
    
    }else{
        $linkShop = "<a href=\"".$rowCampagna['url_1']."\">Voglio rinnovare il mio abbonamento</a><br /><br />Oppure copia e incolla questo link:<br>".$rowCampagna['url_1'];
    }
    
    $messaggio_da_inviare = str_replace('_XXX_NOME_CLIENTE_XXX_', $rowProfessionista['cognome']." ".$rowProfessionista['nome'], $messaggio_da_inviare);
    $messaggio_da_inviare = str_replace('_XXX_NOME_ABBONAMENTO_XXX_', $rowProdotto['nome'], $messaggio_da_inviare);
    $messaggio_da_inviare = str_replace('_XXX_DATA_FINE_ABBONAMENTO_XXX_', GiraDataOra($row_00000001['data_fine_iscrizione']), $messaggio_da_inviare);
    $messaggio_da_inviare = str_replace('_XXX_LINK_SHOP_ONLINE_XXX_', $linkShop, $messaggio_da_inviare);
    
    $rowCalendario = $dblink->get_row("SELECT id FROM calendario WHERE numerico_1 = '$idIscrizione' OR (id_professionista='".$idProfessionista."' AND id_campagna='".$rowCampagna['id_campagna']."' AND id_prodotto='".$rowProdotto['id']."' AND (stato LIKE 'Richiamare' OR stato LIKE 'Mai Contattato' OR stato LIKE 'In Attesa di Controllo' OR stato LIKE 'Accorpata'))",true);
    /*echo "<br>".$dblink->get_query();
    echo "<pre>";
    print_r($rowCalendario);
    echo "</pre>";*/
    if(empty($rowCalendario)){
        if (DISPLAY_DEBUG) echo "<li>id_corso = ".$row_00000001['id_corso']."</li>";
        $insert = array(
            "dataagg" => date("Y-m-d H:i:s"),
            "scrittore" => $dblink->filter("autoGeneraRichiesteRinnovi30gg"),
            "datainsert" => date("Y-m-d"),
            "orainsert" => date("H:i:s"),
            "data" => date("Y-m-d"),
            "ora" => date("H:i:s"),
            "etichetta" => 'Nuovo Rinnovo',
            "oggetto" => $dblink->filter($oggetto_da_inviare),
            "messaggio" => $dblink->filter($messaggio_da_inviare),
            "mittente" => $dblink->filter($rowProfessionista['cognome'])." ".$dblink->filter($rowProfessionista['nome']),
            "destinatario" => "",
            "priorita" => "Alta",
            "stato" => "In Attesa di Invio",
            "tipo_marketing" => $dblink->filter(strtoupper($rowMarketing['nome'])),
            "id_campagna" => $rowCampagna['id_campagna'],
            "id_prodotto" => $rowProdotto['id'],
            "id_azienda" => $id_azienda,
            "id_professionista" => $idProfessionista,
            "id_tipo_marketing" => $rowMarketing['id_tipo_marketing'],
            "giorno" => date("d"),
            "mese" => date("m"),
            "anno" => date("Y"),
            "campo_1" => $dblink->filter($rowProfessionista['nome']),
            "campo_2" => $dblink->filter($rowProfessionista['cognome']),
            "campo_3" => $dblink->filter($rowProfessionista['codice_fiscale']),
            "campo_4" => $dblink->filter($rowProfessionista['telefono']),
            "campo_5" => $dblink->filter($rowProfessionista['email']),
            "campo_6" => $dblink->filter($rowMarketing['nome']),
            "campo_7" => $dblink->filter($rowCampagna['nome']),
            "campo_8" => "",
            "campo_9" => $dblink->filter($rowProfessionista['codice']),
            "numerico_1" => $dblink->filter($idIscrizione),
            "id_classe" => $dblink->filter($row_00000001['id_classe']),
            "nome" => $dblink->filter($rowProfessionista['nome']),
            "cognome" => $dblink->filter($rowProfessionista['cognome']),
            "telefono" => $dblink->filter($rowProfessionista['telefono']),
            "email" => $dblink->filter($rowProfessionista['email']),
            "notifica_email" => "Si",
            "notifica_sms" => "No"
        );

        $ok = true;
        
        $ok = $ok && $dblink->insert("calendario", $insert);
    }
    
}

$Sql_tmp_0001 = "CREATE TEMPORARY TABLE listaRinnoviAbbonamentiDaCaricare (SELECT id as id_iscrizione, scrittore, id_professionista, abbonamento, id_classe, id_corso, data_fine_iscrizione FROM lista_iscrizioni WHERE data_fine_iscrizione = DATE_ADD(CURDATE(), INTERVAL 4 DAY) AND (stato LIKE 'Configurazione') AND abbonamento=1 GROUP BY id_professionista, abbonamento ORDER BY `lista_iscrizioni`.`data_fine_iscrizione` ASC);";
$dblink->query($Sql_tmp_0001);
//$Sql_tmp_0002 = "CREATE TEMPORARY TABLE listaRinnoviCorsiDaCaricare (SELECT scrittore, id_professionista, abbonamento, id_classe, id_corso, data_fine_iscrizione FROM lista_iscrizioni WHERE data_fine_iscrizione = DATE_ADD(CURDATE(), INTERVAL 30 DAY) AND (stato LIKE 'In Corso' OR stato LIKE 'In Attesa%') AND abbonamento=0 GROUP BY id_professionista, id_corso ORDER BY `lista_iscrizioni`.`data_fine_iscrizione` ASC);";
//$dblink->query($Sql_tmp_0002);
//
//SELECT * FROM lista_iscrizioni WHERE data_fine_iscrizione BETWEEN '2017-09-07' AND DATE_ADD(CURDATE(), INTERVAL 30 DAY) AND (stato LIKE 'Configurazione') AND abbonamento=1 GROUP BY id_professionista, abbonamento ORDER BY `lista_iscrizioni`.`data_fine_iscrizione` ASC
//SELECT * FROM lista_iscrizioni WHERE data_fine_iscrizione BETWEEN '2017-09-07' AND DATE_ADD(CURDATE(), INTERVAL 30 DAY) AND (stato LIKE 'In Corso' OR stato LIKE 'In Attesa%') AND abbonamento=0 GROUP BY id_professionista, id_corso ORDER BY `lista_iscrizioni`.`data_fine_iscrizione` ASC

$sql_000001 = "SELECT * FROM listaRinnoviAbbonamentiDaCaricare";// UNION SELECT * FROM listaRinnoviCorsiDaCaricare";

$rs_00000001 = $dblink->get_results($sql_000001);
if (DISPLAY_DEBUG) echo '<ol>';
foreach ($rs_00000001 AS $row_00000001) {
    
    $idProfessionista = $row_00000001['id_professionista'];
    $idIscrizione = $row_00000001['id_iscrizione'];
    
    $rowProfessionista = $dblink->get_row("SELECT * FROM lista_professionisti WHERE id='".$idProfessionista."'", true);
    
    $id_azienda =  ottieniIdAzienda($idProfessionista);
    
    $rowCampagna = $dblink->get_row("SELECT id AS id_campagna, id_tipo_marketing, nome, id_prodotto FROM lista_campagne WHERE id = '166'", true);
    $rowMarketing = $dblink->get_row("SELECT id AS id_tipo_marketing, nome FROM lista_tipo_marketing WHERE id = '49'", true);
    if($row_00000001['abbonamento']=="1"){
        $rowProdotto = $dblink->get_row("SELECT * FROM lista_prodotti WHERE codice_esterno = 'abb_".$row_00000001['id_classe']."'", true);
        if (DISPLAY_DEBUG) echo "<br>".$dblink->get_query();
    }else{
        $rowProdotto = $dblink->get_row("SELECT * FROM lista_prodotti INNER JOIN lista_corsi ON lista_prodotti.id = lista_corsi.id_prodotto WHERE lista_corsi.id = '".$row_00000001['id_corso']."'", true);
        if (DISPLAY_DEBUG) echo "<br>".$dblink->get_query();
    }
    
    $rowCalendario = $dblink->get_row("SELECT id FROM calendario WHERE numerico_1 = '$idIscrizione' OR (id_professionista='".$idProfessionista."' AND id_campagna='".$rowCampagna['id_campagna']."' AND id_prodotto='".$rowProdotto['id']."' AND (stato LIKE 'Richiamare' OR stato LIKE 'Mai Contattato' OR stato LIKE 'In Attesa di Controllo' OR stato LIKE 'Accorpata'))",true);
    /*echo "<br>".$dblink->get_query();
    echo "<pre>";
    print_r($rowCalendario);
    echo "</pre>";*/
    if(empty($rowCalendario)){
        if (DISPLAY_DEBUG) echo "<li>id_corso = ".$row_00000001['id_corso']."</li>";
        $insert = array(
            "dataagg" => date("Y-m-d H:i:s"),
            "scrittore" => $dblink->filter("autoGeneraRichiesteRinnovi4gg"),
            "datainsert" => date("Y-m-d"),
            "orainsert" => date("H:i:s"),
            "data" => date("Y-m-d"),
            "ora" => date("H:i:s"),
            "etichetta" => 'Nuova Richiesta',
            "oggetto" => $dblink->filter($rowCampagna['nome']),
            "messaggio" => "Nome: ".$dblink->filter($rowProfessionista['nome'])."\\nCognome: ".$dblink->filter($rowProfessionista['cognome'])."\\nCodice Cliente: ".$dblink->filter($rowProfessionista['codice'])."\\nTelefono: ".$dblink->filter($rowProfessionista['telefono'])."\\nE-Mail: ".$dblink->filter($rowProfessionista['email'])."\\n\\nTipo Marketing: ".$dblink->filter($rowMarketing['nome'])."\\nNome Campagna: ".$dblink->filter($rowCampagna['nome'])."\\nURL: ".$dblink->filter($_POST['referer'])."\\n\\nMESSAGGIO\\n".$dblink->filter("RICHIESTA DI RINNOVO AUTOMATICO"),
            "mittente" => $dblink->filter($rowProfessionista['cognome'])." ".$dblink->filter($rowProfessionista['nome']),
            "destinatario" => "",
            "priorita" => "Alta",
            "stato" => "In Attesa di Controllo",
            "tipo_marketing" => $dblink->filter(strtoupper($rowMarketing['nome'])),
            "id_campagna" => $rowCampagna['id_campagna'],
            "id_prodotto" => $rowProdotto['id'],
            "id_azienda" => $id_azienda,
            "id_professionista" => $idProfessionista,
            "id_tipo_marketing" => $rowMarketing['id_tipo_marketing'],
            "giorno" => date("d"),
            "mese" => date("m"),
            "anno" => date("Y"),
            "campo_1" => $dblink->filter($rowProfessionista['nome']),
            "campo_2" => $dblink->filter($rowProfessionista['cognome']),
            "campo_3" => $dblink->filter($rowProfessionista['codice_fiscale']),
            "campo_4" => $dblink->filter($rowProfessionista['telefono']),
            "campo_5" => $dblink->filter($rowProfessionista['email']),
            "campo_6" => $dblink->filter($rowMarketing['nome']),
            "campo_7" => $dblink->filter($rowCampagna['nome']),
            "campo_8" => "",
            "campo_9" => $dblink->filter($rowProfessionista['codice']),
            "nome" => $dblink->filter($rowProfessionista['nome']),
            "cognome" => $dblink->filter($rowProfessionista['cognome']),
            "telefono" => $dblink->filter($rowProfessionista['telefono']),
            "email" => $dblink->filter($rowProfessionista['email']),
            "notifica_email" => "Si",
            "notifica_sms" => "No"
        );

        $ok = true;
        /*echo "<h4>INSERIMENTO</h4>";
        echo "<pre>";
        print_r($insert);
        echo "</pre>";*/
        //die;
        $ok = $ok && $dblink->insert("calendario", $insert);
        $richiesta_id_calendario = $dblink->lastid();
        controllaRichiesteMultiple($richiesta_id_calendario);
    }else{
        $rowCalendario2 = $dblink->get_row("SELECT id FROM calendario WHERE numerico_1 = '$idIscrizione'",true);
    
        if(!empty($rowCalendario2)){
        
            $update = array(
                "dataagg" => date("Y-m-d H:i:s"),
                "scrittore" => $dblink->filter("autoGeneraRichiesteRinnovi4gg"),
                "datainsert" => date("Y-m-d"),
                "orainsert" => date("H:i:s"),
                "data" => date("Y-m-d"),
                "ora" => date("H:i:s"),
                "etichetta" => 'Nuova Richiesta',
                "oggetto" => $dblink->filter($rowCampagna['nome']),
                "messaggio" => "Nome: ".$dblink->filter($rowProfessionista['nome'])."\\nCognome: ".$dblink->filter($rowProfessionista['cognome'])."\\nCodice Cliente: ".$dblink->filter($rowProfessionista['codice'])."\\nTelefono: ".$dblink->filter($rowProfessionista['telefono'])."\\nE-Mail: ".$dblink->filter($rowProfessionista['email'])."\\n\\nTipo Marketing: ".$dblink->filter($rowMarketing['nome'])."\\nNome Campagna: ".$dblink->filter($rowCampagna['nome'])."\\nURL: ".$dblink->filter($_POST['referer'])."\\n\\nMESSAGGIO\\n".$dblink->filter("RICHIESTA DI RINNOVO AUTOMATICO"),
                "mittente" => $dblink->filter($rowProfessionista['cognome'])." ".$dblink->filter($rowProfessionista['nome']),
                "destinatario" => "",
                "priorita" => "Alta",
                "stato" => "In Attesa di Controllo",
                "tipo_marketing" => $dblink->filter(strtoupper($rowMarketing['nome'])),
                "id_campagna" => $rowCampagna['id_campagna'],
                "id_prodotto" => $rowProdotto['id'],
                "id_azienda" => $id_azienda,
                "id_professionista" => $idProfessionista,
                "id_tipo_marketing" => $rowMarketing['id_tipo_marketing'],
                "giorno" => date("d"),
                "mese" => date("m"),
                "anno" => date("Y"),
                "campo_1" => $dblink->filter($rowProfessionista['nome']),
                "campo_2" => $dblink->filter($rowProfessionista['cognome']),
                "campo_3" => $dblink->filter($rowProfessionista['codice_fiscale']),
                "campo_4" => $dblink->filter($rowProfessionista['telefono']),
                "campo_5" => $dblink->filter($rowProfessionista['email']),
                "campo_6" => $dblink->filter($rowMarketing['nome']),
                "campo_7" => $dblink->filter($rowCampagna['nome']),
                "campo_8" => "",
                "campo_9" => $dblink->filter($rowProfessionista['codice']),
                "nome" => $dblink->filter($rowProfessionista['nome']),
                "cognome" => $dblink->filter($rowProfessionista['cognome']),
                "telefono" => $dblink->filter($rowProfessionista['telefono']),
                "email" => $dblink->filter($rowProfessionista['email']),
                "notifica_email" => "Si",
                "notifica_sms" => "No"
            );

            $ok = true;

            $ok = $ok && $dblink->update("calendario", $update, array("id" => $rowCalendario['id']));
            $richiesta_id_calendario = $rowCalendario['id'];
            controllaRichiesteMultiple($richiesta_id_calendario);
        }
    }
    
}
    
if (DISPLAY_DEBUG) echo '</pl>'.date("H:i:s");
?>