<?php

require_once(BASE_ROOT . 'classi/fpdf/fpdf.php');

class PrevPDF extends FPDF {

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

function nuovoCodicePreventivo($idPreventivo, $codSezionale) {
    global $dblink;
    
    //calcolo il numero del preventivo
    $sql_numero_preventivo = "SELECT `id`, YEAR(CURDATE()) AS anno, sezionale, codice AS numero_preventivo 
	FROM lista_preventivi 
	WHERE 1
	AND YEAR(data_creazione) = YEAR(CURDATE())
	AND lista_preventivi.sezionale = '" . $codSezionale . "'
	AND codice>0
	ORDER BY codice DESC LIMIT 1";
    $rs_numero_preventivo = $dblink->get_results($sql_numero_preventivo);
    //echo '<li>$sql_numero_preventivo = '.$sql_numero_preventivo.'</li>';
    if (!empty($rs_numero_preventivo)) {
        foreach ($rs_numero_preventivo as $row_numero_preventivo) {

            if ($row_numero_preventivo['anno'] == date("Y")) {
                $numero_preventivo_nuovo = $row_numero_preventivo['numero_preventivo'] + 1;
                $sezionale_preventivo_nuovo = $codSezionale;

                //$preventivo_nuovo = ''.$anno_preventivo_nuovo.$mese_preventivo_nuovo.'-'.$numero_preventivo_nuovo.'/'.$sezionale_preventivo_nuovo;
                $preventivo_nuovo = $numero_preventivo_nuovo;
            } else {
                $numero_preventivo_nuovo = "1";
                $sezionale_preventivo_nuovo = $codSezionale;

                //$preventivo_nuovo = ''.$anno_preventivo_nuovo.$mese_preventivo_nuovo.'-'.$numero_preventivo_nuovo.'/'.$sezionale_preventivo_nuovo;
                $preventivo_nuovo = $numero_preventivo_nuovo;
            }
        }
    }else{
        $numero_preventivo_nuovo = "1";
        $sezionale_preventivo_nuovo = $codSezionale;

        //$preventivo_nuovo = ''.$anno_preventivo_nuovo.$mese_preventivo_nuovo.'-'.$numero_preventivo_nuovo.'/'.$sezionale_preventivo_nuovo;
        $preventivo_nuovo = $numero_preventivo_nuovo;
    }

    //echo '<li>$preventivo_nuovo = '.$preventivo_nuovo.'</li>';
    //echo '<li>$sezionale_preventivo_nuovo = '.$sezionale_preventivo_nuovo.'</li>';

    return $preventivo_nuovo;
}

function creaPreventivoPDF($idPrev, $echo = false) {
    global $dblink, $id_area;

    if (isset($idPrev)) {
        $id_preventivo = $idPrev;
        
        $imponibile_preventivo_nuovo = 0;
        $importo_preventivo_nuovo = 0;

        $sql_importo_preventivo_nuovo = "SELECT SUM(`prezzo_prodotto` * `quantita`) AS imponibile_preventivo_nuovo, SUM(`prezzo_prodotto` * `quantita`*(1+(iva_prodotto/100))) AS importo_preventivo_nuovo, iva_prodotto FROM  `lista_preventivi_dettaglio` WHERE  `id_preventivo` ='" . $id_preventivo . "' GROUP BY id_preventivo, iva_prodotto";
        $rs_importo_preventivo_nuovo = $dblink->get_results($sql_importo_preventivo_nuovo);

        if(!empty($rs_importo_preventivo_nuovo)) {
            foreach($rs_importo_preventivo_nuovo as $row_importo_preventivo_nuovo) {
                $imponibile_preventivo_nuovo += $row_importo_preventivo_nuovo['imponibile_preventivo_nuovo'];
                $importo_preventivo_nuovo += $row_importo_preventivo_nuovo['importo_preventivo_nuovo'];
            }
        }

        $queryTot_3 = "SELECT DISTINCT
        lista_preventivi.*,
        lista_preventivi.id AS prev_ide_interno,
        lista_preventivi.codice_esterno AS prev_codice_esterno,
        lista_preventivi.data_esterna AS prev_data_esterna,


        lista_preventivi_dettaglio.nome_prodotto,
        lista_preventivi_dettaglio.codice_prodotto,
        lista_preventivi_dettaglio.prezzo_prodotto,
        lista_preventivi_dettaglio.iva_prodotto,
        lista_preventivi_dettaglio.quantita

        FROM lista_preventivi INNER JOIN lista_preventivi_dettaglio ON lista_preventivi.id=lista_preventivi_dettaglio.id_preventivo

        WHERE lista_preventivi.id='$id_preventivo'";
        //ECHO '<LI>$queryTot_3 = '.$queryTot_3.'</LI>';


        $sql_paginazione = "SELECT DISTINCT
        lista_preventivi.*,
        lista_preventivi.id AS prev_ide_interno,
        lista_preventivi.codice_esterno AS prev_codice_esterno,
        lista_preventivi.data_esterna AS prev_data_esterna,


        lista_preventivi_dettaglio.nome_prodotto,
        lista_preventivi_dettaglio.codice_prodotto,
        lista_preventivi_dettaglio.prezzo_prodotto,
        lista_preventivi_dettaglio.iva_prodotto,
        lista_preventivi_dettaglio.quantita

        FROM lista_preventivi INNER JOIN lista_preventivi_dettaglio ON lista_preventivi.id=lista_preventivi_dettaglio.id_preventivo

        WHERE lista_preventivi.id='$id_preventivo' AND tipo_prodotto='Prodotto'
        ORDER BY id, id_prodotto
        ";
        $conteggio_paginazione = $dblink->num_rows($sql_paginazione);
        $totale = $dblink->num_rows($queryTot_3) + ($conteggio_paginazione);

        $pageSize = 12;
        $pagina = 1;
        $begin = ($pagina - 1) * $pageSize;
        $countPages = ceil($totale / $pageSize);

        $html = '';
        
        $pdf = new PrevPDF();
        $pdf->AliasNbPages();
        //$pdf->SetAutoPageBreak(true,10);


        for ($begin_a = 1; $begin_a <= $countPages; $begin_a++) {

            if ($begin_a > 1) {
                $begin_b = ($begin_a - 1) * $pageSize;
            } else {
                $begin_b = 0;
            }

            $sql = "SELECT DISTINCT
            lista_preventivi.*,
            lista_preventivi.id AS prev_ide_interno,
            lista_preventivi.codice_esterno AS prev_codice_esterno,
            lista_preventivi.data_esterna AS prev_data_esterna,


            lista_preventivi_dettaglio.nome_prodotto,
            lista_preventivi_dettaglio.codice_prodotto,
            lista_preventivi_dettaglio.prezzo_prodotto,
            lista_preventivi_dettaglio.iva_prodotto,
            lista_preventivi_dettaglio.quantita

            FROM lista_preventivi INNER JOIN lista_preventivi_dettaglio ON lista_preventivi.id=lista_preventivi_dettaglio.id_preventivo

            WHERE lista_preventivi.id='$id_preventivo' LIMIT $begin_b,$pageSize";
            $num_rs = $dblink->num_rows($sql);
            $rs = $dblink->get_results($sql);


            if ($num_rs > 0) {
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
                    $pdf->Image(BASE_ROOT.'moduli/preventivi/carta_intestata_preventivo.jpg', 0, 0, 210);
                }


                $margine_x = 104;
                $margine_y = 40;

                //DATI CLIENTE
                $sql_calendario = "SELECT lista_preventivi.* FROM lista_preventivi
                                    WHERE lista_preventivi.id='" . $id_preventivo . "'";
                $rowCalendario = $dblink->get_row($sql_calendario,true);
                
                if($rowCalendario['id_professionista']<=0){
                    $sql_cliente = "SELECT calendario.id, calendario.nome, calendario.cognome FROM calendario INNER JOIN lista_preventivi
                    ON calendario.id=lista_preventivi.id_calendario WHERE lista_preventivi.id='" . $id_preventivo . "'";
                }else if($rowCalendario['id_azienda']<=0){
                    $sql_cliente = "SELECT lista_professionisti.id, lista_professionisti.cognome,lista_professionisti.nome,
                        lista_professionisti.codice_fiscale, lista_professionisti.codice
                        FROM lista_professionisti INNER JOIN lista_preventivi
                    ON lista_professionisti.id=lista_preventivi.id_professionista WHERE lista_preventivi.id='" . $id_preventivo . "'";
                }else{
                    
                    $sql_cliente = "SELECT lista_professionisti.cognome,lista_professionisti.nome, lista_professionisti.codice, lista_aziende.*
                        FROM lista_professionisti INNER JOIN lista_preventivi
                        INNER JOIN lista_aziende
                    ON lista_professionisti.id=lista_preventivi.id_professionista WHERE lista_preventivi.id='" . $id_preventivo . "'"
                            . " AND lista_aziende.id='".$rowCalendario['id_azienda']."'";
                    
                    //$sql_cliente = "SELECT lista_aziende.* FROM lista_aziende INNER JOIN lista_preventivi
                    //ON lista_aziende.id=lista_preventivi.id_azienda WHERE lista_preventivi.id='".$id_preventivo."'";
                }
                $rs_cliente = $dblink->get_results($sql_cliente);
                if(!empty($rs_cliente)) {
                    foreach ($rs_cliente as $row_cliente) {

                        $pdf->SetFillColor(255, 255, 255);
                        $pdf->SetTextColor(0, 0, 0);
                        $pdf->SetFont('Times', '', 8);

                        $margine_x_azienda = 125;
                        $margine_y_azienda = 10;

                        if(!empty($row_cliente['cognome'])){
                            $pdf->SetXY($margine_x_azienda + 5, $margine_y_azienda);
                            if (strlen($row_cliente['cognome']) > 30) {
                                $pdf->SetFont('Times', '', 8);
                            } else {
                                $pdf->SetFont('Times', 'B', 12);
                            }
                            $pdf->Cell(50, 5, '' . utf8_decode($row_cliente['cognome']). '', 0, 0, 'L', 0, 0);
                            $margine_y_azienda = $margine_y_azienda + 4;
                        }
                        
                        $pdf->SetFont('Times', '', 12);
                        
                        if(!empty($row_cliente['nome'])){
                            $pdf->SetXY($margine_x_azienda + 5, $margine_y_azienda);
                            $pdf->Cell(80, 5, '' . $row_cliente['nome'] . '', 0, 0, 'L', 0, 0);
                            $margine_y_azienda = $margine_y_azienda + 4;

                            $pdf->SetXY($margine_x_azienda + 5, $margine_y_azienda);
                        }
                        //$pdf->Cell(80, 5, '' . $row_cliente['cap'] . ' ' . $row_cliente['citta'] . ' (' . $row_cliente['provincia'] . ')', 0, 0, L, 0, 0);
                        $margine_y_azienda = $margine_y_azienda + 10;


                        if(!empty($row_cliente['ragione_sociale'])){
                            $pdf->SetXY($margine_x_azienda + 5, $margine_y_azienda);
                            if (strlen($row_cliente['ragione_sociale']) > 30) {
                                $pdf->SetFont('Times', '', 6);
                            } else {
                                $pdf->SetFont('Times', 'B', 8);
                            }
                            $pdf->Cell(50, 5, '' . utf8_decode($row_cliente['ragione_sociale']) . '', 0, 0, 'L', 0, 0);
                        }
                        $margine_y_azienda = $margine_y_azienda + 4;

                        $pdf->SetFont('Times', '', 8);
                        
                        if(!empty($row_cliente['indirizzo'])){
                            $pdf->SetXY($margine_x_azienda + 5, $margine_y_azienda);
                            $pdf->Cell(80, 5, '' . $row_cliente['indirizzo'] . '', 0, 0, 'L', 0, 0);
                        }
                        $margine_y_azienda = $margine_y_azienda + 4;

                        if(!empty($row_cliente['cap'])){
                            $pdf->SetXY($margine_x_azienda + 5, $margine_y_azienda);
                            $pdf->Cell(80, 5, '' . $row_cliente['cap'] . ' ' . $row_cliente['citta'] . ' (' . $row_cliente['provincia'] . ')', 0, 0, 'L', 0, 0);
                        }
                        $margine_y_azienda = $margine_y_azienda + 4;

                        $margine_x_codicefiscale = 53;
                        $margine_y_codicefiscale = 51;

                        $pdf->SetXY(3, $margine_y_codicefiscale);
                        $pdf->Cell(15, 5, '' . $row_cliente['codice'] . '', 0, 0, 'C', 0, 0);
                        
                        if(isset($row_cliente['codice_fiscale'])){
                            $pdf->SetXY($margine_x_codicefiscale + 5, $margine_y_codicefiscale);
                            $pdf->Cell(45, 5, '' . $row_cliente['codice_fiscale'] . '', 0, 0, 'C', 0, 0);
                        }
                        
                        if(isset($row_cliente['partita_iva'])){
                            $pdf->SetXY($margine_x_codicefiscale + 53, $margine_y_codicefiscale);
                            $pdf->Cell(30, 5, '' . $row_cliente['partita_iva'] . '', 0, 0, 'C', 0, 0);
                        }

                        if(!empty($row_cliente['ragione_sociale'])){
                            $azienda_creazione_preventivo = utf8_decode($row_cliente['ragione_sociale']);
                        
                            $oldmask = umask(0);
                            if(!is_dir(BASE_ROOT . "media")){
                                mkdir(BASE_ROOT . "media", 0777);
                            }
                            if(!is_dir(BASE_ROOT . "media/Anagrafiche")){
                                mkdir(BASE_ROOT . "media/Anagrafiche", 0777);
                            }
                            if(!is_dir(BASE_ROOT . "media/Anagrafiche/Clienti")){
                                mkdir(BASE_ROOT . "media/Anagrafiche/Clienti", 0777);
                            }
                            if(!is_dir(BASE_ROOT . "media/Anagrafiche/Clienti/".$azienda_creazione_preventivo)){
                                mkdir(BASE_ROOT . "media/Anagrafiche/Clienti/" . $azienda_creazione_preventivo . "", 0777);
                            }
                            umask($oldmask);
                        }
                        
                        //OGGETTO PREVENTIVO
                        //$codice_preventivo = $row_cliente['codice'];
                        //$imponibile_preventivo = $row_cliente['imponibile'];
                    }
                } else {
                    
                }
                $pdf->SetFont('Times', '', 8);
                //DATI PREVENTIVO/ORDINE
                $margine_x = 14;
                $margine_y = 40;
                $pdf->SetXY($margine_x, $margine_y);
                $sql_1 = "SELECT *, YEAR(data_creazione) AS anno_creazione_preventivo,
                IF(MONTH(data_creazione)<=9,CONCAT('0',MONTH(data_creazione)),MONTH(data_creazione)) AS mese_creazione_preventivo, CONCAT(IF(DAY(data_creazione)<=9,CONCAT('0',DAY(data_creazione)),DAY(data_creazione)),'/',IF(MONTH(data_creazione)<=9,CONCAT('0',MONTH(data_creazione)),MONTH(data_creazione)),'/',YEAR(data_creazione)) AS data_creazione, DATE(data_scadenza) AS data_scadenza_1, CONCAT(IF(DAY(data_scadenza)<=9,CONCAT('0',DAY(data_scadenza)),DAY(data_scadenza)),'/',IF(MONTH(data_scadenza)<=9,CONCAT('0',MONTH(data_scadenza)),MONTH(data_scadenza)),'/',YEAR(data_scadenza)) AS data_scadenza
                FROM lista_preventivi WHERE id='" . $id_preventivo . "'";
                $rs_1 = $dblink->get_results($sql_1);
                if (!empty($rs_1)) {
                    foreach ($rs_1 as $row_1) {
                        $note_preventivo = strip_tags($row_1['campo_1']);
                        $pdf->SetFillColor(255, 255, 255);
                        $pdf->SetTextColor(0, 0, 0);
                        $pdf->SetFont('Times', 'B', 10);

                        $pdf->SetFillColor(255, 255, 255);
                        $pdf->SetTextColor(0, 0, 0);
                        $pdf->SetFont('Times', '', 8);
                        
                        //  $pdf->SetXY($margine_x,$margine_y);
                        //  $pdf->Cell(90, 9, ''. $row_1['codice'].' del '.$row_1['data_creazione'] , 1, 0, L, 0,0);
                        //  $margine_y = $margine_y + 9;
                        
                        $anno_creazione_preventivo = $row_1['anno_creazione_preventivo'];
                        $mese_creazione_preventivo = $row_1['mese_creazione_preventivo'];

                        //$azienda_creazione_preventivo = utf8_decode($row_cliente['ragione_sociale']);

                        $margine_x_codice_preventivo = 140;
                        $margine_y_codice_preventivo = 51;

                        $pdf->SetXY($margine_x_codice_preventivo, $margine_y_codice_preventivo);
                        if($row_1['codice']==="xxx"){
                            $pdf->Cell(36, 5, '' . $id_preventivo, 0, 0, 'C', 0, 0);
                        }else{
                            $pdf->Cell(36, 5, '' . $row_1['codice'] . '/' . $row_1['sezionale'], 0, 0, 'C', 0, 0);
                        }

                        $pdf->SetXY($margine_x_codice_preventivo + 38, $margine_y_codice_preventivo);
                        $pdf->Cell(17, 5, '' . $row_1['data_creazione'] . '', 0, 0, 'C', 0, 0);

                        $oldmask = umask(0);
                        if(!is_dir(BASE_ROOT . "media")){
                            mkdir(BASE_ROOT . "media", 0777);
                        }
                        if(!is_dir(BASE_ROOT . "media/Preventivi")){
                            mkdir(BASE_ROOT . "media/Preventivi", 0777);
                        }
                        if(!is_dir(BASE_ROOT . "media/Preventivi/Attivi")){
                            mkdir(BASE_ROOT . "media/Preventivi/Attivi", 0777);
                        }
                        if(!is_dir(BASE_ROOT . "media/Preventivi/Attivi/". $anno_creazione_preventivo)){
                            mkdir(BASE_ROOT . "media/Preventivi/Attivi/". $anno_creazione_preventivo, 0777);
                        }
                        if(!is_dir(BASE_ROOT . "media/Preventivi/Attivi/".$anno_creazione_preventivo. "/" . $mese_creazione_preventivo)){
                            mkdir(BASE_ROOT . "media/Preventivi/Attivi/" . $anno_creazione_preventivo . "/" . $mese_creazione_preventivo . "", 0777);
                        }
                        umask($oldmask);

                        $pdf->SetFillColor(255, 255, 255);
                        $pdf->SetTextColor(0, 0, 0);
                        $pdf->SetFont('Times', '', 8);

                        $margine_x_pagamento_preventivo = 3;
                        $margine_y_pagamento_preventivo = 61;

                        if(isset($row_1['pagamento'])){
                        
                            $pdf->SetXY($margine_x_pagamento_preventivo, $margine_y_pagamento_preventivo);
                            $pdf->Cell(105, 5, '' . $row_1['pagamento'] . '', 0, 0, 'L', 0, 0);


                            $pdf->SetXY(9, 269);
                            $pdf->Cell(105, 5, '' . $row_1['pagamento'] . ' ' . $row_1['data_scadenza'] . '', 0, 0, 'L', 0, 0);


                            $pdf->SetXY($margine_x_pagamento_preventivo + 110, $margine_y_pagamento_preventivo);
                            $pdf->Cell(94, 5, '' . $row_1['iban_banca'] . '', 0, 0, 'C', 0, 0);
                        
                        }
                        
                        $pdf->SetFont('Times', 'B', 10);
                        
                        $pdf->SetXY($margine_x_pagamento_preventivo + 119, $margine_y_pagamento_preventivo+9);
                        $pdf->Cell(105, 5, 'PREVENTIVO', 0, 0, 'C', 0, 0);

                        $pdf->SetFont('Times', '', 8);
                        
                        //COD IVA IMPONIBILE IMPOSTA O DESCRIZIONE
                        $margine_x_iva_preventivo = 3;
                        $margine_y_iva_preventivo = 235;

                        $pdf->SetXY($margine_x_iva_preventivo, $margine_y_iva_preventivo);

                        $sql_importo_preventivo_nuovo = "SELECT `id_preventivo`,
			SUM(`prezzo_prodotto` * `quantita`) AS imponibile_preventivo_nuovo,
			SUM(`prezzo_prodotto` * `quantita`*(1+(iva_prodotto/100))) AS importo_preventivo_nuovo, iva_prodotto
			FROM  `lista_preventivi_dettaglio`
			WHERE  `id_preventivo` ='" . $id_preventivo . "' GROUP BY id_preventivo, iva_prodotto";

                        $rs_importo_preventivo_nuovo = $dblink->get_results($sql_importo_preventivo_nuovo);
                        if (!empty($rs_importo_preventivo_nuovo)) {
                            $imponibile_preventivo_nuovo = 0;
                            $importo_preventivo_nuovo = 0;
                            //StampaSQL($sql_importo_preventivo_nuovo,'');
                            $margine_y_iva = 249.1;
                            foreach ($rs_importo_preventivo_nuovo as $row_importo_preventivo_nuovo) {
                                $pdf->SetXY($margine_x_iva_preventivo, $margine_y_iva_preventivo);
                                $pdf->Cell(34, 5, '' . number_format($row_importo_preventivo_nuovo['imponibile_preventivo_nuovo'], 2, ",", ".") . '', 0, 0, 'R', 0, 0);
                                $pdf->Cell(17, 5, '' . $row_importo_preventivo_nuovo['iva_prodotto'] . '', 0, 0, 'C', 0, 0);
                                $pdf->Cell(36, 5, '' . number_format($row_importo_preventivo_nuovo['importo_preventivo_nuovo'] - $row_importo_preventivo_nuovo['imponibile_preventivo_nuovo'], 2, ",", ".") . '', 0, 0, 'R', 0, 0);
                                //	$imponibile_preventivo_nuovo += $row_importo_preventivo_nuovo['imponibile_preventivo_nuovo'];
                                //	$importo_preventivo_nuovo += $row_importo_preventivo_nuovo['importo_preventivo_nuovo'];
                                $margine_y_iva_preventivo = $margine_y_iva_preventivo + 5;
                            }
                        } else {
                            
                        }

                        $margine_x_totali_preventivo = 91;
                        $margine_y_totali_preventivo = 236;

                        $pdf->SetXY($margine_x_totali_preventivo - 87, $margine_y_totali_preventivo + 21.5);
                        $pdf->Cell(33, 5, '' . number_format($row_1['imponibile'], 2, ",", ".") . '', 0, 0, 'R', 0, 0);

                        $pdf->SetXY($margine_x_totali_preventivo, $margine_y_totali_preventivo);
                        $pdf->Cell(36, 5, '' . number_format($row_1['imponibile'], 2, ",", ".") . '', 0, 0, 'R', 0, 0);

                        $pdf->SetFont('Times', '', 12);
                        $pdf->SetXY($margine_x_totali_preventivo + 60, $margine_y_totali_preventivo + 21.5);
                        $pdf->Cell(55, 5, '' . number_format($row_1['importo'], 2, ",", ".") . '', 0, 0, 'R', 0, 0);

                        $pdf->SetFont('Times', '', 8);
                        $pdf->SetXY($margine_x_totali_preventivo + 2, $margine_y_totali_preventivo + 21.5);
                        $pdf->Cell(55, 5, '' . number_format($row_1['importo'], 2, ",", ".") . '', 0, 0, 'R', 0, 0);

                        $pdf->SetXY($margine_x_totali_preventivo - 36, $margine_y_totali_preventivo + 21.5);
                        $pdf->Cell(35, 5, '' . number_format($row_1['importo'] - $row_1['imponibile'], 2, ",", ".") . '', 0, 0, 'R', 0, 0);
                    }
                } else {
                    
                }

                $pdf->SetFont('Times', 'B', 10);

                $margine_x_dettaglio_preventivo = 2;
                $margine_y_dettaglio_preventivo = 78.6;

                $pdf->SetXY($margine_x_dettaglio_preventivo, $margine_y_dettaglio_preventivo);

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

                    //$filename = "BetaFormazione_Ordine_" . $codice . "-" . $sezionale_originale . ".pdf";
                    $filename = PREFIX_FILE_PDF_PREVENTIVO . $id_preventivo . ".pdf";

                    //$pdf->SetTextColor(0,0,0);
                    //$pdf->SetFillColor(255,255,255);

                    $pdf->SetXY($margine_x_dettaglio_preventivo, $margine_y_dettaglio_preventivo);
                    $valore_lunghezza_riga = 205.5;
                    $pdf->Cell($valore_lunghezza_riga, 10, ' ', 1, 1, 'L', 1, 1);
                    $pdf->SetXY($margine_x_dettaglio_preventivo, $margine_y_dettaglio_preventivo);
                    $pdf->Cell(31, 5, '' . $conta_record_dettaglio . '', 0, 0, 'L', 0, 0);


                    $pdf->SetXY($margine_x_dettaglio_preventivo + 31, $margine_y_dettaglio_preventivo);
                    $pdf->Cell(97, 5, 'Ordine ' . utf8_decode($row['sezionale']) . '/' . $row['codice'], 0, 0, 'L', 0, 0);

                    $pdf->SetXY($margine_x_dettaglio_preventivo + 31, $margine_y_dettaglio_preventivo + 5);
                    $pdf->Cell(97, 5, '' . strtoupper(utf8_decode($row['nome_prodotto'])) . '', 0, 0, 'L', 0, 0);

                    $pdf->SetXY($margine_x_dettaglio_preventivo + 137.3, $margine_y_dettaglio_preventivo + 5);
                    $pdf->Cell(20.5, 5, '' . strtoupper(utf8_decode($row['quantita'])) . '', 0, 0, 'C', 0, 0);

                    $pdf->SetXY($margine_x_dettaglio_preventivo + 157.8, $margine_y_dettaglio_preventivo + 5);
                    $pdf->Cell(18.5, 5, '' . number_format($row['prezzo_prodotto'], 2, ",", ".") . '', 0, 0, 'C', 0, 0);

                    $pdf->SetXY($margine_x_dettaglio_preventivo + 176.3, $margine_y_dettaglio_preventivo + 5);
                    $pdf->Cell(21.8, 5, '' . number_format($row['prezzo_prodotto'] * $row['quantita'], 2, ",", ".") . '', 0, 0, 'C', 0, 0);

                    $pdf->SetXY($margine_x_dettaglio_preventivo + 198.1, $margine_y_dettaglio_preventivo + 5);
                    $pdf->Cell(7.3, 5, '' . $row['iva_prodotto'] . '', 0, 0, 'C', 0, 0);

                    $margine_y_dettaglio_preventivo = $margine_y_dettaglio_preventivo + 10;
                    //$numero_ddt = $row['codice_bolla'];
                    //$numero_prev = $row['prev_codice_esterno'];
                    $conta_record_dettaglio++;

                    //PARENTESI PER CICLO FOR RECORD MULTIPLO}
                }

                if (strlen($note_preventivo) > 200000000000000000000000000000000000000) {
                    $pdf->SetFont('Times', '', 8);
                    $pdf->SetFillColor(255, 255, 255);
                    $pdf->SetTextColor(0, 0, 0);
                    $pdf->SetFont('Times', 'I', 8);
                    $pdf->SetXY(20, 220);
                    $pdf->Cell(167, 8, '' . $note_preventivo . '', 1, 0, 'L', 0, 0);
                }
            }
        }
        //stampo
        //$pdf->Output();
        
        if(!is_dir(BASE_ROOT . "media")){
            mkdir(BASE_ROOT . "media", 0777);
        }
        if(!is_dir(BASE_ROOT . "media/lista_preventivi")){
            mkdir(BASE_ROOT . "media/lista_preventivi", 0777);
        }
        
        $pdf->Output(BASE_ROOT . 'media/lista_preventivi/' . $filename, 'F');
        //$pdf->Output(BASE_ROOT."media/Fatture/Attive/".$anno_creazione_preventivo."/".$mese_creazione_preventivo."/".$filename, F);
        //$pdf->Output(BASE_ROOT."media/Anagrafiche/Clienti/".$azienda_creazione_preventivo."/".$filename, F);
        if($echo===true){
            $pdf->Output($filename, 'I');
        }
    } else {
        
    }
}


function nuovoCodicePreventivoWeb($codSezionale){
    global $dblink;
    //calcolo il numero del preventivo
    $sql_numero_preventivo = "SELECT `id`, YEAR(CURDATE()) AS anno, sezionale, codice AS numero_preventivo 
	FROM lista_preventivi 
	WHERE 1
	AND YEAR(data_creazione) = YEAR(CURDATE())
	AND lista_preventivi.sezionale = '" . $codSezionale . "'
	AND codice>0
	ORDER BY codice DESC LIMIT 1";
    $rs_numero_preventivo = $dblink->get_results($sql_numero_preventivo);
    //echo '<li>$sql_numero_preventivo = '.$sql_numero_preventivo.'</li>';
    if (!empty($rs_numero_preventivo)) {
            foreach ($rs_numero_preventivo as $row_numero_preventivo) {

                if ($row_numero_preventivo['anno'] == date("Y")) {
                    $numero_preventivo_nuovo = $row_numero_preventivo['numero_preventivo'] + 1;
                    $sezionale_preventivo_nuovo = $codSezionale;

                    //$preventivo_nuovo = ''.$anno_preventivo_nuovo.$mese_preventivo_nuovo.'-'.$numero_preventivo_nuovo.'/'.$sezionale_preventivo_nuovo;
                    $preventivo_nuovo = $numero_preventivo_nuovo;
                } else {
                    $numero_preventivo_nuovo = "1";
                    $sezionale_preventivo_nuovo = $codSezionale;

                    //$preventivo_nuovo = ''.$anno_preventivo_nuovo.$mese_preventivo_nuovo.'-'.$numero_preventivo_nuovo.'/'.$sezionale_preventivo_nuovo;
                    $preventivo_nuovo = $numero_preventivo_nuovo;
                }
            }
    } else {
        $numero_preventivo_nuovo = "1";
        $sezionale_preventivo_nuovo = $codSezionale;

        //$preventivo_nuovo = ''.$anno_preventivo_nuovo.$mese_preventivo_nuovo.'-'.$numero_preventivo_nuovo.'/'.$sezionale_preventivo_nuovo;
        $preventivo_nuovo = $numero_preventivo_nuovo;
    }

    //echo '<li>$preventivo_nuovo = '.$preventivo_nuovo.'</li>';
    //echo '<li>$sezionale_preventivo_nuovo = '.$sezionale_preventivo_nuovo.'</li>';

    return $preventivo_nuovo;
}
?>