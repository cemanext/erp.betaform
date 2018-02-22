<?php

require_once(BASE_ROOT . 'classi/fpdf/fpdf.php');

class PDF extends FPDF {

    var $B;
    var $I;
    var $U;
    var $HREF;

    function __construct($orientation = 'P', $unit = 'mm', $format = 'A4') {
        //Call parent constructor
        parent::__construct($orientation, $unit, $format);
        //Initialization
        $this->B = 0;
        $this->I = 0;
        $this->U = 0;
        $this->HREF = '';
    }

    function Header() {
        //Logo
        //$this->Image('images/carta_intestata.jpg',0,0,210);
        //Times bold 15
        $this->SetFont('Times', 'B', 8);
        //Line break
        $this->Ln(45);
    }

    function Footer() {
        //Position at 1.5 cm from bottom
        $this->SetY(-5);
        //Times italic 8
        $this->SetFont('Times', 'I', 6);
        //Page number
        $this->Cell(0, 1, 'Pagina ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

}

function nuovoCodiceFattura($idFattura, $codSezionale) {
    global $dblink;
    //calcolo il numero del fattura
    $sql_numero_fattura = "SELECT `id`, YEAR(CURDATE()) AS anno, sezionale, codice AS numero_fattura 
	FROM lista_fatture 
	WHERE 1
	AND YEAR(data_creazione) = YEAR(CURDATE())
	AND lista_fatture.sezionale = '" . $codSezionale . "'
	AND codice>0
	ORDER BY codice DESC LIMIT 1";
    $rs_numero_fattura = $dblink->get_results($sql_numero_fattura);
    //echo '<li>$sql_numero_fattura = '.$sql_numero_fattura.'</li>';
    if (!empty($rs_numero_fattura)) {
        foreach ($rs_numero_fattura as $row_numero_fattura) {

            if ($row_numero_fattura['anno'] == date("Y")) {
                $numero_fattura_nuova = $row_numero_fattura['numero_fattura'] + 1;
                $sezionale_fattura_nuova = $codSezionale;

                //$fattura_nuova = ''.$anno_fattura_nuova.$mese_fattura_nuova.'-'.$numero_fattura_nuova.'/'.$sezionale_fattura_nuova;
                $fattura_nuova = $numero_fattura_nuova;
            } else {
                $numero_fattura_nuova = "1";
                $sezionale_fattura_nuova = $codSezionale;

                //$fattura_nuova = ''.$anno_fattura_nuova.$mese_fattura_nuova.'-'.$numero_fattura_nuova.'/'.$sezionale_fattura_nuova;
                $fattura_nuova = $numero_fattura_nuova;
            }
        }
    } else {
        $numero_fattura_nuova = "1";
        $sezionale_fattura_nuova = $codSezionale;

        //$fattura_nuova = ''.$anno_fattura_nuova.$mese_fattura_nuova.'-'.$numero_fattura_nuova.'/'.$sezionale_fattura_nuova;
        $fattura_nuova = $numero_fattura_nuova;
    }

    //echo '<li>$fattura_nuova = '.$fattura_nuova.'</li>';
    //echo '<li>$sezionale_fattura_nuova = '.$sezionale_fattura_nuova.'</li>';

    return $fattura_nuova;
}

function creaFatturaPDF($idFatt, $echo = false) {
    global $dblink, $id_area;

    if (isset($idFatt)) {
        $id_fattura = $idFatt;

        $sql_importo_fattura_nuova = "SELECT `id_fattura`, SUM(`prezzo_prodotto` * `quantita`) AS imponibile_fattura_nuova, SUM(`prezzo_prodotto` * `quantita`*(1+(iva_prodotto/100))) AS importo_fattura_nuova, iva_prodotto FROM  `lista_fatture_dettaglio` WHERE  `id_fattura` ='" . $id_fattura . "' GROUP BY id_fattura, iva_prodotto";

        $imponibile_fattura_nuova = 0;
        $importo_fattura_nuova = 0;

        $rs_importo_fattura_nuova = $dblink->get_results($sql_importo_fattura_nuova);
        if (!empty($rs_importo_fattura_nuova)) {
            
            //StampaSQL($sql_importo_fattura_nuova,'');
            foreach ($rs_importo_fattura_nuova as $row_importo_fattura_nuova) {
                $imponibile_fattura_nuova += $row_importo_fattura_nuova['imponibile_fattura_nuova'];
                $importo_fattura_nuova += $row_importo_fattura_nuova['importo_fattura_nuova'];
            }
        }


        $sql_0000001 = "UPDATE lista_fatture, lista_preventivi
        SET lista_fatture.codice_preventivo = CONCAT(lista_preventivi.sezionale,'/',lista_preventivi.codice)
        WHERE lista_fatture.id_preventivo = lista_preventivi.id
        AND lista_fatture.id = '" . $id_fattura . "'";
        $rs_0000001 = $dblink->query($sql_0000001);

        $queryTot_3 = "SELECT
        lista_fatture.*,
        lista_preventivi.id AS prev_ide_interno,
        lista_preventivi.codice_esterno AS prev_codice_esterno,
        lista_preventivi.data_esterna AS prev_data_esterna,


        lista_fatture_dettaglio.nome_prodotto,
        lista_fatture_dettaglio.note AS descrizione_aggiuntiva,
        lista_fatture_dettaglio.codice_prodotto,
        lista_fatture_dettaglio.prezzo_prodotto,
        lista_fatture_dettaglio.iva_prodotto,
        lista_fatture_dettaglio.quantita

        FROM lista_fatture INNER JOIN lista_fatture_dettaglio ON lista_fatture.id=lista_fatture_dettaglio.id_fattura

        LEFT JOIN lista_preventivi ON lista_fatture_dettaglio.id_preventivo=lista_preventivi.id

        WHERE lista_fatture.id='$id_fattura'";
        //ECHO '<LI>$queryTot_3 = '.$queryTot_3.'</LI>';
        $ris_totale_3 = $dblink->num_rows($queryTot_3);

        $sql_paginazione = "SELECT
        lista_fatture.*,
        lista_preventivi.id AS prev_ide_interno,
        lista_preventivi.codice_esterno AS prev_codice_esterno,
        lista_preventivi.data_esterna AS prev_data_esterna,


        lista_fatture_dettaglio.nome_prodotto,
        lista_fatture_dettaglio.note AS descrizione_aggiuntiva,
        lista_fatture_dettaglio.codice_prodotto,
        lista_fatture_dettaglio.prezzo_prodotto,
        lista_fatture_dettaglio.iva_prodotto,
        lista_fatture_dettaglio.quantita

        FROM lista_fatture INNER JOIN lista_fatture_dettaglio ON lista_fatture.id=lista_fatture_dettaglio.id_fattura

        LEFT JOIN lista_preventivi ON lista_fatture_dettaglio.id_preventivo=lista_preventivi.id

        WHERE lista_fatture.id='$id_fattura' AND (tipo_prodotto='Prodotto' OR lista_fatture.id_preventivo = 0)
        ORDER BY id, id_prodotto
        ";
        $conteggio_paginazione = $dblink->num_rows($sql_paginazione);
        
        $totale = $ris_totale_3 + ($conteggio_paginazione);

        $pageSize = 12;
        $pagina = 1;
        $begin = ($pagina - 1) * $pageSize;
        $countPages = ceil($totale / $pageSize);

        $html = '';

        $pdf = new PDF();
        $pdf->AliasNbPages();
        //$pdf->SetAutoPageBreak(true,10);


        for ($begin_a = 1; $begin_a <= $countPages; $begin_a++) {

            if ($begin_a > 1) {
                $begin_b = ($begin_a - 1) * $pageSize;
            } else {
                $begin_b = 0;
            }

            $sql = "SELECT
            lista_fatture.*,
            lista_preventivi.id AS prev_ide_interno,
            lista_preventivi.codice_esterno AS prev_codice_esterno,
            lista_preventivi.data_esterna AS prev_data_esterna,

            (SELECT lista_prodotti.descrizione_fattura FROM lista_prodotti WHERE lista_prodotti.id = lista_fatture_dettaglio.id_prodotto) AS descrizione_fattura,

            lista_fatture_dettaglio.nome_prodotto,
            lista_fatture_dettaglio.note AS descrizione_aggiuntiva,
            lista_fatture_dettaglio.codice_prodotto,
            lista_fatture_dettaglio.prezzo_prodotto,
            lista_fatture_dettaglio.iva_prodotto,
            lista_fatture_dettaglio.quantita,
            (SELECT CONCAT(cognome,' ',nome,' (',codice,')' ) FROM lista_professionisti WHERE id = lista_fatture_dettaglio.id_professionista) as nome_professionista


            FROM lista_fatture INNER JOIN lista_fatture_dettaglio ON lista_fatture.id=lista_fatture_dettaglio.id_fattura

            LEFT JOIN lista_preventivi ON lista_fatture_dettaglio.id_preventivo=lista_preventivi.id

            WHERE lista_fatture.id='$id_fattura' LIMIT $begin_b,$pageSize";
            $rs = $dblink->get_results($sql);


            if (count($rs) > 0) {
                $pdf->AddPage();
                $pdf->SetFont('Times', '', 8);
                $pdf->SetFillColor(255, 255, 255);
                $pdf->SetTextColor(0, 0, 0);

                if ($id_area == 1) {
                    $pdf->Image('images/carta_intestata_1.jpg', 0, 0, 210);
                } elseif ($id_area == 2) {
                    $pdf->Image('images/carta_intestata_2.jpg', 0, 0, 210);
                } elseif ($id_area == 3) {
                    $pdf->Image('images/carta_intestata_3.jpg', 0, 0, 210);
                } else {
                    $pdf->Image(BASE_ROOT.'moduli/fatture/carta_intestata_fattura.jpg', 0, 0, 210);
                }


                $margine_x = 104;
                $margine_y = 40;

                //DATI CLIENTE
                $sql_cliente = "SELECT lista_aziende.* FROM lista_aziende INNER JOIN lista_fatture 
                ON lista_aziende.id=lista_fatture.id_azienda WHERE lista_fatture.id='" . $id_fattura . "'";
                $rs_cliente = $dblink->get_results($sql_cliente);
                if (!empty($rs_cliente)) {
                    foreach ($rs_cliente as $row_cliente) {

                        $pdf->SetFillColor(255, 255, 255);
                        $pdf->SetTextColor(0, 0, 0);
                        $pdf->SetFont('Times', '', 8);

                        $margine_x_azienda = 125;
                        $margine_y_azienda = 10;

                        $pdf->SetXY($margine_x_azienda + 5, $margine_y_azienda);
                        if (strlen($row_cliente['ragione_sociale']) > 30) {
                            $pdf->SetFont('Times', '', 6);
                        } else {
                            $pdf->SetFont('Times', 'B', 8);
                        }
                        $pdf->Cell(50, 5, '' . utf8_decode(mb_convert_encoding($row_cliente['ragione_sociale'], "UTF-8", "HTML-ENTITIES")) . '', 0, 0, 'L', 0, 0);
                        $margine_y_azienda = $margine_y_azienda + 4;

                        $pdf->SetFont('Times', '', 8);
                        $pdf->SetXY($margine_x_azienda + 5, $margine_y_azienda);
                        $pdf->Cell(80, 5, '' . mb_convert_encoding($row_cliente['indirizzo'], "UTF-8", "HTML-ENTITIES") . '', 0, 0, 'L', 0, 0);
                        $margine_y_azienda = $margine_y_azienda + 4;

                        $pdf->SetXY($margine_x_azienda + 5, $margine_y_azienda);
                        $pdf->Cell(80, 5, '' . $row_cliente['cap'] . ' ' . mb_convert_encoding($row_cliente['citta'], "UTF-8", "HTML-ENTITIES") . ' (' . mb_convert_encoding($row_cliente['provincia'], "UTF-8", "HTML-ENTITIES") . ')', 0, 0, 'L', 0, 0);
                        $margine_y_azienda = $margine_y_azienda + 10;



                        $pdf->SetXY($margine_x_azienda + 5, $margine_y_azienda);
                        if (strlen($row_cliente['ragione_sociale']) > 30) {
                            $pdf->SetFont('Times', '', 6);
                        } else {
                            $pdf->SetFont('Times', 'B', 8);
                        }
                        $pdf->Cell(50, 5, '' . utf8_decode(mb_convert_encoding($row_cliente['ragione_sociale'], "UTF-8", "HTML-ENTITIES")) . '', 0, 0, 'L', 0, 0);
                        $margine_y_azienda = $margine_y_azienda + 4;

                        $pdf->SetFont('Times', '', 8);
                        $pdf->SetXY($margine_x_azienda + 5, $margine_y_azienda);
                        $pdf->Cell(80, 5, '' . mb_convert_encoding($row_cliente['indirizzo'], "UTF-8", "HTML-ENTITIES") . '', 0, 0, 'L', 0, 0);
                        $margine_y_azienda = $margine_y_azienda + 4;

                        $pdf->SetXY($margine_x_azienda + 5, $margine_y_azienda);
                        $pdf->Cell(80, 5, '' . $row_cliente['cap'] . ' ' . mb_convert_encoding($row_cliente['citta'], "UTF-8", "HTML-ENTITIES") . ' (' . mb_convert_encoding($row_cliente['provincia'], "UTF-8", "HTML-ENTITIES") . ')', 0, 0, 'L', 0, 0);
                        $margine_y_azienda = $margine_y_azienda + 4;

                        $margine_x_codicefiscale = 53;
                        $margine_y_codicefiscale = 51;

                        $pdf->SetXY(3, $margine_y_codicefiscale);
                        $pdf->Cell(15, 5, '' . $row_cliente['id'] . '', 0, 0, 'C', 0, 0);

                        $pdf->SetXY($margine_x_codicefiscale + 5, $margine_y_codicefiscale);
                        $pdf->Cell(45, 5, '' . $row_cliente['codice_fiscale'] . '', 0, 0, 'C', 0, 0);

                        
                        if(!preg_match('/^[a-z]{6}[0-9]{2}[a-z][0-9]{2}[a-z][0-9]{3}[a-z]{1}$/i', trim($row_cliente['partita_iva']))) {
                            $pdf->SetXY($margine_x_codicefiscale + 53, $margine_y_codicefiscale);
                            $pdf->Cell(30, 5, '' . $row_cliente['partita_iva'] . '', 0, 0, 'C', 0, 0);
                        }

                        //OGGETTO PREVENTIVO
                        //$codice_preventivo = $row_cliente['codice'];
                        //$imponibile_preventivo = $row_cliente['imponibile'];
                    }
                }
                
                $pdf->SetFont('Times', '', 8);
                //DATI PREVENTIVO/ORDINE
                $margine_x = 14;
                $margine_y = 40;
                $pdf->SetXY($margine_x, $margine_y);
                $sql_1 = "SELECT *, YEAR(data_creazione) AS anno_creazione_fattura,
                IF(MONTH(data_creazione)<=9,CONCAT('0',MONTH(data_creazione)),MONTH(data_creazione)) AS mese_creazione_fattura, CONCAT(IF(DAY(data_creazione)<=9,CONCAT('0',DAY(data_creazione)),DAY(data_creazione)),'/',IF(MONTH(data_creazione)<=9,CONCAT('0',MONTH(data_creazione)),MONTH(data_creazione)),'/',YEAR(data_creazione)) AS data_creazione, DATE(data_scadenza) AS data_scadenza_1, CONCAT(IF(DAY(data_scadenza)<=9,CONCAT('0',DAY(data_scadenza)),DAY(data_scadenza)),'/',IF(MONTH(data_scadenza)<=9,CONCAT('0',MONTH(data_scadenza)),MONTH(data_scadenza)),'/',YEAR(data_scadenza)) AS data_scadenza,
                (SELECT nome FROM lista_fatture_banche WHERE id=id_fatture_banche LIMIT 1) as 'nome_banca',
                (SELECT iban FROM lista_fatture_banche WHERE id=id_fatture_banche LIMIT 1) as 'iban_banca',
                lista_fatture.tipo
                FROM lista_fatture WHERE id='" . $id_fattura . "'";
                $rs_1 = $dblink->get_results($sql_1);
                if (!empty($rs_1)) {
                    foreach ($rs_1 as $row_1) {
                        $note_fattura = strip_tags($row_1['nota_documento']);
                        $pdf->SetFillColor(255, 255, 255);
                        $pdf->SetTextColor(0, 0, 0);
                        $pdf->SetFont('Times', 'B', 10);

                        $pdf->SetFillColor(255, 255, 255);
                        $pdf->SetTextColor(0, 0, 0);
                        $pdf->SetFont('Times', '', 8);
                        
                        $anno_creazione_fattura = $row_1['anno_creazione_fattura'];
                        $mese_creazione_fattura = $row_1['mese_creazione_fattura'];

                        //$azienda_creazione_preventivo = utf8_decode($row_cliente['ragione_sociale']);

                        $margine_x_codice_fattura = 140;
                        $margine_y_codice_fattura = 51;

                        $pdf->SetXY($margine_x_codice_fattura, $margine_y_codice_fattura);
                        //$pdf->Cell(36, 5, '' . $row_1['codice'] . SEPARATORE_FATTURA . $row_1['sezionale'], 0, 0, 'C', 0, 0);
                        $pdf->Cell(36, 5, '' . $row_1['codice_ricerca'] . '', 0, 0, 'C', 0, 0);

                        $pdf->SetXY($margine_x_codice_fattura + 38, $margine_y_codice_fattura);
                        $pdf->Cell(17, 5, '' . $row_1['data_creazione'] . '', 0, 0, 'C', 0, 0);

                        $pdf->SetFillColor(255, 255, 255);
                        $pdf->SetTextColor(0, 0, 0);
                        $pdf->SetFont('Times', '', 8);

                        $margine_x_pagamento_fattura = 3;
                        $margine_y_pagamento_fattura = 61;

                        $pdf->SetXY($margine_x_pagamento_fattura, $margine_y_pagamento_fattura);
                        $pdf->Cell(105, 5, '' . $row_1['pagamento'] . '', 0, 0, 'L', 0, 0);


                        $pdf->SetXY(9, 269);
                        $pdf->Cell(105, 5, '' . $row_1['pagamento'] . ' ' . $row_1['data_scadenza'] . '', 0, 0, 'L', 0, 0);


                        $pdf->SetXY($margine_x_pagamento_fattura + 110, $margine_y_pagamento_fattura);
                        $pdf->Cell(94, 5, '' . $row_1['iban_banca'] . '', 0, 0, 'C', 0, 0);

                        $pdf->SetFont('Times', 'B', 10);
                        
                        $pdf->SetXY($margine_x_pagamento_fattura + 119, $margine_y_pagamento_fattura+9);
                        $pdf->Cell(105, 5, '' . strtoupper($row_1['tipo']) . '', 0, 0, 'C', 0, 0);

                        $pdf->SetFont('Times', '', 8);
                        

                        //COD IVA IMPONIBILE IMPOSTA O DESCRIZIONE
                        $margine_x_iva_fattura = 3;
                        $margine_y_iva_fattura = 235;

                        $pdf->SetXY($margine_x_iva_fattura, $margine_y_iva_fattura);

                        $sql_importo_fattura_nuova = "SELECT `id_fattura`, 
                            SUM(`prezzo_prodotto` * `quantita`) AS imponibile_fattura_nuova, 
                            SUM(`prezzo_prodotto` * `quantita`*(1+(iva_prodotto/100))) AS importo_fattura_nuova, iva_prodotto 
                            FROM  `lista_fatture_dettaglio` 
                            WHERE  `id_fattura` ='" . $id_fattura . "' GROUP BY id_fattura, iva_prodotto";

                        $rs_importo_fattura_nuova = $dblink->get_results($sql_importo_fattura_nuova);
                        if(!empty($rs_importo_fattura_nuova)) {
                            $imponibile_fattura_nuova = 0;
                            $importo_fattura_nuova = 0;
                            //StampaSQL($sql_importo_fattura_nuova,'');
                            $margine_y_iva = 249.1;
                            foreach ($rs_importo_fattura_nuova as $row_importo_fattura_nuova) {
                                $pdf->SetXY($margine_x_iva_fattura, $margine_y_iva_fattura);
                                $pdf->Cell(34, 5, '' . number_format($row_importo_fattura_nuova['imponibile_fattura_nuova'], 2, ",", ".") . '', 0, 0, 'R', 0, 0);
                                $pdf->Cell(17, 5, '' . $row_importo_fattura_nuova['iva_prodotto'] . '', 0, 0, 'C', 0, 0);
                                $pdf->Cell(36, 5, '' . number_format($row_importo_fattura_nuova['importo_fattura_nuova'] - $row_importo_fattura_nuova['imponibile_fattura_nuova'], 2, ",", ".") . '', 0, 0, 'R', 0, 0);
                                //	$imponibile_fattura_nuova += $row_importo_fattura_nuova['imponibile_fattura_nuova'];
                                //	$importo_fattura_nuova += $row_importo_fattura_nuova['importo_fattura_nuova'];
                                $margine_y_iva_fattura = $margine_y_iva_fattura + 5;
                            }
                        }

                        $margine_x_totali_fattura = 91;
                        $margine_y_totali_fattura = 236;

                        $pdf->SetXY($margine_x_totali_fattura - 87, $margine_y_totali_fattura + 21.5);
                        $pdf->Cell(33, 5, '' . number_format($row_1['imponibile'], 2, ",", ".") . '', 0, 0, 'R', 0, 0);

                        $pdf->SetXY($margine_x_totali_fattura, $margine_y_totali_fattura);
                        $pdf->Cell(36, 5, '' . number_format($row_1['imponibile'], 2, ",", ".") . '', 0, 0, 'R', 0, 0);

                        $pdf->SetFont('Times', '', 12);
                        $pdf->SetXY($margine_x_totali_fattura + 60, $margine_y_totali_fattura + 21.5);
                        $pdf->Cell(55, 5, ' ' . number_format($row_1['importo'], 2, ",", ".") . '', 0, 0, 'R', 0, 0);

                        $pdf->SetFont('Times', '', 8);
                        $pdf->SetXY($margine_x_totali_fattura + 2, $margine_y_totali_fattura + 21.5);
                        $pdf->Cell(55, 5, '' . number_format($row_1['importo'], 2, ",", ".") . '', 0, 0, 'R', 0, 0);

                        $pdf->SetXY($margine_x_totali_fattura - 36, $margine_y_totali_fattura + 21.5);
                        $pdf->Cell(35, 5, '' . number_format($row_1['importo'] - $row_1['imponibile'], 2, ",", ".") . '', 0, 0, 'R', 0, 0);
                    }
                }

                $pdf->SetFont('Times', 'B', 10);

                $margine_x_dettaglio_fattura = 2;
                $margine_y_dettaglio_fattura = 78.6;

                $pdf->SetXY($margine_x_dettaglio_fattura, $margine_y_dettaglio_fattura);

                $pdf->SetFillColor(255, 255, 255);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->SetFont('Times', '', 8);
                $conta_record_dettaglio = 1;
                foreach ($rs as $row) {

                    //PARENTESI PER CICLO FOR RECORD MULTIPLOfor($a=1;$a<=4;$a++){

                    if ($conta_record_dettaglio % 2 == 0) {
                        $pdf->SetFillColor(255, 255, 255);
                    } else {
                        $pdf->SetFillColor(250, 250, 250);
                    }

                    $codice = str_replace("/", "-", $row['codice']);
                    $codice_originale = $row['codice'];
                    $sezionale_originale = $row['sezionale'];

                    $filename = PREFIX_FILE_PDF_FATTURA . $codice . "-" . $sezionale_originale . ".pdf";

                    //$pdf->SetTextColor(0,0,0);
                    //$pdf->SetFillColor(255,255,255);

                    $pdf->SetXY($margine_x_dettaglio_fattura, $margine_y_dettaglio_fattura);
                    $valore_lunghezza_riga = 205.5;
                    $pdf->Cell($valore_lunghezza_riga, 10, ' ', 1, 1, 'L', 1, 1);
                    $pdf->SetXY($margine_x_dettaglio_fattura, $margine_y_dettaglio_fattura);
                    $pdf->Cell(31, 5, '' . $conta_record_dettaglio . '', 0, 0, 'L', 0, 0);


                    $pdf->SetXY($margine_x_dettaglio_fattura + 31, $margine_y_dettaglio_fattura);
                    if(strlen($row['codice_preventivo'])>2){
                        $pdf->Cell(97, 5, 'Ordine ' . utf8_decode($row['codice_preventivo']) . ' - '.utf8_decode(mb_convert_encoding($row['nome_professionista'], "UTF-8", "HTML-ENTITIES")), 0, 0, 'L', 0, 0);
                    }else{
                        $pdf->Cell(97, 5, ''.utf8_decode($row['nome_professionista']), 0, 0, 'L', 0, 0);
                    }
                    

                    $pdf->SetXY($margine_x_dettaglio_fattura + 31, $margine_y_dettaglio_fattura + 5);
                    $pdf->Cell(97, 5, '' . strtoupper(utf8_decode(mb_convert_encoding($row['descrizione_fattura'], "UTF-8", "HTML-ENTITIES"))) . ' '.utf8_decode(mb_convert_encoding($row['descrizione_aggiuntiva'], "UTF-8", "HTML-ENTITIES")), 0, 0, 'L', 0, 0);

                    $pdf->SetXY($margine_x_dettaglio_fattura + 137.3, $margine_y_dettaglio_fattura + 5);
                    $pdf->Cell(20.5, 5, '' . strtoupper(utf8_decode($row['quantita'])) . '', 0, 0, 'C', 0, 0);

                    $pdf->SetXY($margine_x_dettaglio_fattura + 157.8, $margine_y_dettaglio_fattura + 5);
                    $pdf->Cell(18.5, 5, '' . number_format($row['prezzo_prodotto'], 2, ",", ".") . '', 0, 0, 'C', 0, 0);

                    $pdf->SetXY($margine_x_dettaglio_fattura + 176.3, $margine_y_dettaglio_fattura + 5);
                    $pdf->Cell(21.8, 5, '' . number_format($row['prezzo_prodotto'] * $row['quantita'], 2, ",", ".") . '', 0, 0, 'C', 0, 0);

                    $pdf->SetXY($margine_x_dettaglio_fattura + 198.1, $margine_y_dettaglio_fattura + 5);
                    $pdf->Cell(7.3, 5, '' . $row['iva_prodotto'] . '', 0, 0, 'C', 0, 0);

                    $margine_y_dettaglio_fattura = $margine_y_dettaglio_fattura + 10;
                    //$numero_ddt = $row['codice_bolla'];
                    //$numero_prev = $row['prev_codice_esterno'];
                    $conta_record_dettaglio++;

                    //PARENTESI PER CICLO FOR RECORD MULTIPLO}
                }

                if (strlen($note_fattura) > 2) {
                    $pdf->SetFont('Times', '', 8);
                    $pdf->SetFillColor(235, 235, 235);
                    $pdf->SetTextColor(0, 0, 0);
                    $pdf->SetFont('Times', 'I', 8);
                    $pdf->SetXY(20, 220);
                    $pdf->Cell(167, 8, '' . $note_fattura . '', 1, 1, 'L', 1, 1);
                }
            }
        }
        //stampo
        //$pdf->Output();
        
        if(!is_dir(BASE_ROOT . "media")){
            mkdir(BASE_ROOT . "media", 0777);
        }
        if(!is_dir(BASE_ROOT . "media/lista_fatture")){
            mkdir(BASE_ROOT . "media/lista_fatture", 0777);
        }
        
        if(!is_dir(BASE_ROOT . "media/lista_fatture/".$anno_creazione_fattura)){
            mkdir(BASE_ROOT . "media/lista_fatture/".$anno_creazione_fattura, 0777);
        }
        if(!is_dir(BASE_ROOT . "media/lista_fatture/".$anno_creazione_fattura."/".$mese_creazione_fattura)){
            @mkdir(BASE_ROOT . "media/lista_fatture/" . $anno_creazione_fattura . "/" . $mese_creazione_fattura . "", 0777);
        }
        
        if(file_exists(BASE_ROOT . "media/lista_fatture/".$anno_creazione_fattura . "/" . $mese_creazione_fattura . "/".$filename)){
            chmod(BASE_ROOT. "media/lista_fatture/".$anno_creazione_fattura . "/" . $mese_creazione_fattura . "/". $filename, 0777);
        }
        
        if(file_exists(BASE_ROOT . "media/lista_fatture/".$filename)){
            chmod(BASE_ROOT. "media/lista_fatture/". $filename, 0777);
        }
        
        $pdf->Output(BASE_ROOT . 'media/lista_fatture/' . $anno_creazione_fattura . "/" . $mese_creazione_fattura . "/". $filename, 'F');
        $pdf->Output(BASE_ROOT . 'media/lista_fatture/'. $filename, 'F');
        
        if($echo===true){
            $pdf->Output($filename, 'I');
        }
    } else {
        
    }
}

?>