<?php
include_once('../../config/connDB.php');
include_once(BASE_ROOT.'config/confAccesso.php');


if(isset($_GET['mese']) && isset($_GET['anno']) && isset($_GET['id_provvigione'])){
    $mese = $_GET['mese'];
    $anno = $_GET['anno'];
    $id_provvigione = $_GET['id_provvigione'];
}else{
    $id_provvigione = "0";
    $mese = date("m");
    if($mese == "01"){
        $mese = "12";
        $anno = date('Y', strtotime('-1 years'));
    }else{
        $mese = date("m", strtotime('-1 month'));
        $anno = date('Y');
    }
}

if(isset($_GET['iscritti'])){
    $groupby = "GROUP BY id_professionista";
    $sql_0001 = "SELECT
            (SELECT codice FROM lista_provvigioni WHERE id = '$id_provvigione') AS codice_partner,
            cognome_nome_professionista,
            nome_classe,
            data_inizio_iscrizione AS data_attivazione,
            data_fine_iscrizione AS data_scadenza,
            email AS email_professionista,
            cellulare AS cellulare_professionista,
            provincia_di_nascita AS provincia,
            professione,
            provincia_albo,
            numero_albo
            FROM lista_iscrizioni INNER JOIN lista_professionisti ON (lista_iscrizioni.id_professionista = lista_professionisti.id) 
            WHERE 
            lista_iscrizioni.stato NOT LIKE '%Scadut%'
            AND id_fattura_dettaglio IN (SELECT id FROM lista_fatture_dettaglio WHERE id_provvigione = '$id_provvigione')
            AND id_fattura IN (SELECT id FROM lista_fatture WHERE MONTH(data_creazione) = '$mese' AND YEAR(data_creazione) = '$anno' AND sezionale NOT LIKE '%CN%')
            $groupby
        ";
}else{
    $sql_0001 = "SELECT
            (SELECT codice FROM lista_provvigioni WHERE id = '$id_provvigione') AS codice_partner,
            nome_corso AS corso,
            cognome_nome_professionista,
            nome_classe,
            data_inizio_iscrizione AS data_attivazione,
            data_fine_iscrizione AS data_scadenza,
            DATE(data_inizio) AS data_inizio,
            DATE(data_completamento) AS data_completamento,
            avanzamento_completamento AS 'avanzamento_%',
            lista_iscrizioni.stato,
            email AS email_professionista,
            cellulare AS cellulare_professionista,
            provincia_di_nascita AS provincia,
            professione,
            provincia_albo,
            numero_albo
            FROM lista_iscrizioni INNER JOIN lista_professionisti ON (lista_iscrizioni.id_professionista = lista_professionisti.id) 
            WHERE 
            lista_iscrizioni.stato NOT LIKE 'Configurazione'
            AND lista_iscrizioni.stato NOT LIKE '%Scadut%'
            AND id_fattura_dettaglio IN (SELECT id FROM lista_fatture_dettaglio WHERE id_provvigione = '$id_provvigione')
            AND id_fattura IN (SELECT id FROM lista_fatture WHERE MONTH(data_creazione) = '$mese' AND YEAR(data_creazione) = '$anno' AND sezionale NOT LIKE '%CN%')
        ";
}



//$sql_0001 = creaSQLesporta();
/*$sql_0001 = "SELECT IF(tipo='Fattura', 'Fatt. Imm.', 'N. C.') AS 'Tipo doc.', sezionale AS 'Sz.', codice AS 'Nr.doc.', DATE_FORMAT(DATE(data_creazione), '%d/%m/%Y') AS 'Data Doc.',
            (SELECT CONCAT(ragione_sociale,' ',forma_giuridica) AS rag_soc FROM lista_aziende WHERE id = id_azienda) AS 'Ragione Sociale Anagrafica',
            REPLACE(ABS(imponibile), '.', ',') AS 'Tot. imponibile', REPLACE((ABS(importo)-ABS(imponibile)), '.', ',') AS 'Tot. Iva', REPLACE(ABS(importo), '.', ',') AS 'Tot. Documento',
            (SELECT CONCAT(cognome,' ',nome) FROM lista_password WHERE id = id_agente) AS 'Nome Commerciale'
            FROM lista_fatture 
            WHERE MONTH(data_creazione) = '$mese' AND YEAR(data_creazione) = '$anno' AND tipo LIKE '".$tipo."' AND sezionale NOT LIKE '%CN%' AND (stato LIKE 'In Attesa' OR stato LIKE 'Pagata%' OR stato LIKE 'Nota di%') ".$whereCommerciale;*/
$titolo = "Esportazione Partner del ".date("d/m/Y H:i:s");
//stampa_table_datatables_responsive($sql_0001, $titolo, 'tabella_base');

$result = $dblink->get_results($sql_0001);
$fields = $dblink->list_fields($sql_0001);

//header info for browser
header("Content-Type: application/xls");    
header("Content-Disposition: attachment; filename=$titolo.xls");
header("Pragma: no-cache"); 
header("Expires: 0");

$sep = "\t"; //tabbed character
//start of printing column names as names of MySQL fields
foreach ($fields as $field) {
    echo $field->name . "\t";
}
print("\n");    
//end of printing column names  
//start while loop to get data
    foreach($result as $rows)
    {
        $schema_insert = "";
        foreach($rows as $row)
        {
            if(!isset($row))
                $schema_insert .= "NULL".$sep;
            elseif ($row != "")
                $schema_insert .= "".mb_convert_encoding(htmlspecialchars_decode(html_entity_decode(modificaAccentate($row), ENT_QUOTES ,"ISO-8859-1")), "ISO-8859-1", "HTML-ENTITIES").$sep;
            else
                $schema_insert .= "".$sep;
        }
        $schema_insert = str_replace($sep."$", "", $schema_insert);
        $schema_insert = preg_replace("/\r\n|\n\r|\n|\r/", " ", $schema_insert);
        $schema_insert .= "\t";
        print(trim($schema_insert));
        print "\n";
    }

?>
