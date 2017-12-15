<?php

if(isset($_GET['mese']) && isset($_GET['anno']) && isset($_GET['sezionale']) && isset($_GET['stato'])){
    $mese = $_GET['mese'];
    $anno = $_GET['anno'];
    $sezionale = $_GET['sezionale'];
    $stato = $_GET['stato'];
}else{
    $sezionale = "00";
    $stato = "In Attesa";
    $mese = date("m", strtotime('-1 month'));
    if($mese == "01"){
        $anno = date('Y', strtotime('-1 years'));
    }else{
        $anno = date('Y');
    }
}

$fileName = "fatture_".str_replace(" ", "-", $stato)."_".$mese."_".$anno."_sezionale_".$sezionale.".xml";

$rowsFatture = $dblink->get_results("SELECT * FROM lista_fatture WHERE YEAR(data_creazione) = '$anno' AND MONTH(data_creazione) = '$mese' AND sezionale='".$sezionale."' AND (stato LIKE '".$stato."')");

$xml = new SimpleXMLElement('<EasyfattDocuments/>');

//$easyfattDocuments = $xml->addChild('EasyfattDocuments');
$xml->addAttribute("AppVersion", '2');
$xml->addAttribute("Creator", 'CEMA NEXT Srl');
$xml->addAttribute("CreatorUrl", 'http://www.cemanext.it');
$xml->addAttribute("xmlns:xsi", 'http://www.w3.org/2001/XMLSchema-instance');
$xml->addAttribute("xsi:noNamespaceSchemaLocation", ERP_DOMAIN_NAME);

$company = $xml->addChild('Company');
$company->addChild('Name', 'Beta Formazione srl');
$company->addChild('Address', 'Via Piratello, 66/68');
$company->addChild('Postcode', '48022');
$company->addChild('City', 'Lugo');
$company->addChild('Province', 'RA');
$company->addChild('FiscalCode', '02322490398');
$company->addChild('VatCode', '02322490398');
$company->addChild('Tel', '0545 916279');
$company->addChild('Fax', '0545 030139');
$company->addChild('Email', 'amministrazione@betaformazione.com');
$company->addChild('HomePage', WP_DOMAIN_NAME);

$documents = $xml->addChild('Documents');


foreach ($rowsFatture as $rowFattura) {
        
    if(!empty($rowFattura)){
        $document = $documents->addChild('Document');
        
        $datiFatturazione = $dblink->get_row("SELECT * FROM lista_aziende WHERE id = '".$rowFattura['id_azienda']."'",true);
        $rsDatiPagamento = $dblink->get_results("SELECT data_creazione, entrate FROM lista_costi WHERE id_fattura = '".$rowFattura['id']."'");

        $document->addChild('CustomerCode', "_XXX_REPLACE_VOID_XXX_");
        $document->addChild('CustomerWebLogin',"_XXX_REPLACE_VOID_XXX_");
        $document->addChild('CustomerName', mb_convert_encoding(htmlspecialchars(html_entity_decode($datiFatturazione['ragione_sociale']." ".$datiFatturazione['forma_giuridica'])), "UTF-8", "HTML-ENTITIES"));
        $document->addChild('CustomerAddress', mb_convert_encoding(htmlspecialchars(html_entity_decode($datiFatturazione['indirizzo'])), "UTF-8", "HTML-ENTITIES"));
        $document->addChild('CustomerPostcode', $datiFatturazione['cap']);
        $document->addChild('CustomerCity', mb_convert_encoding(htmlspecialchars(html_entity_decode($datiFatturazione['citta'])), "UTF-8", "HTML-ENTITIES"));
        $document->addChild('CustomerProvince', mb_convert_encoding(htmlspecialchars(html_entity_decode($datiFatturazione['provincia'])), "UTF-8", "HTML-ENTITIES"));
        $document->addChild('CustomerCountry', mb_convert_encoding(htmlspecialchars(html_entity_decode($datiFatturazione['nazione'])), "UTF-8", "HTML-ENTITIES"));
        $document->addChild('CustomerFiscalCode', $datiFatturazione['partita_iva']);
        $document->addChild('CustomerCellPhone', $datiFatturazione['cellulare']);
        $document->addChild('CustomerEmail', mb_convert_encoding(htmlspecialchars(html_entity_decode($datiFatturazione['email'])), "UTF-8", "HTML-ENTITIES"));
        $document->addChild('DocumentType', "I");
        $document->addChild('Date', $rowFattura['data_creazione']);
        $document->addChild('Number', $rowFattura['codice']);
        $document->addChild('Numbering', "_XXX_REPLACE_VOID_XXX_");
        $document->addChild('CostDescription', "_XXX_REPLACE_VOID_XXX_");
        $document->addChild('CostVatCode', "_XXX_REPLACE_VOID_XXX_");
        $document->addChild('CostAmount', "_XXX_REPLACE_VOID_XXX_");
        $document->addChild('ContribDescription', "_XXX_REPLACE_VOID_XXX_");
        $document->addChild('ContribPerc', "_XXX_REPLACE_VOID_XXX_");
        $document->addChild('ContribSubjectToWithholdingTax', "_XXX_REPLACE_VOID_XXX_");
        $document->addChild('ContribAmount', "_XXX_REPLACE_VOID_XXX_");
        $document->addChild('ContribVatCode', "_XXX_REPLACE_VOID_XXX_");
        $document->addChild('TotalWithoutTax', $rowFattura['imponibile']);
        $document->addChild('VatAmount', ($rowFattura['importo']-$rowFattura['imponibile']));
        $document->addChild('WithholdingTaxAmount', "_XXX_REPLACE_VOID_XXX_");
        $document->addChild('WithholdingTaxAmountB', "_XXX_REPLACE_VOID_XXX_");
        $document->addChild('WithholdingTaxNameB', "_XXX_REPLACE_VOID_XXX_");
        $document->addChild('Total', $rowFattura['importo']);
        $document->addChild('PriceList', 'Listino 1');
        $document->addChild('PricesIncludeVat', 'false');
        $document->addChild('WithholdingTaxPerc', "_XXX_REPLACE_VOID_XXX_");
        $document->addChild('WithholdingTaxPerc2', "_XXX_REPLACE_VOID_XXX_");
        $document->addChild('PaymentName', 'Bonifico vista fattura');
        $document->addChild('PaymentBank', "_XXX_REPLACE_VOID_XXX_");
        $payments = $document->addChild('Payments');
        if($rowFattura['stato'] == "In Attesa"){
            $payment = $payments->addChild('Payment');
            $payment->addChild('Advance', 'false');
            $payment->addChild('Date', "_XXX_REPLACE_VOID_XXX_");
            $payment->addChild('Amount', "_XXX_REPLACE_VOID_XXX_");
            $payment->addChild('Paid', 'false');
        }else{
            foreach ($rsDatiPagamento as $rowDatiPagamento) {
                $payment = $payments->addChild('Payment');
                $payment->addChild('Advance', 'false');
                $payment->addChild('Date', $rowDatiPagamento['data_creazione']);
                $payment->addChild('Amount', $rowDatiPagamento['entrate']);
                $payment->addChild('Paid', 'true');
            }
        }
        $document->addChild('InternalComment', "_XXX_REPLACE_VOID_XXX_");
        $document->addChild('CustomField1', "_XXX_REPLACE_VOID_XXX_");
        $document->addChild('CustomField2', "_XXX_REPLACE_VOID_XXX_");
        $document->addChild('CustomField3', "_XXX_REPLACE_VOID_XXX_");
        $document->addChild('CustomField4', "_XXX_REPLACE_VOID_XXX_");
        $document->addChild('FootNotes', "_XXX_REPLACE_VOID_XXX_");
        $document->addChild('SalesAgent', "_XXX_REPLACE_VOID_XXX_");
        $document->addChild('DelayedVat','false');
        $rows = $document->addChild('Rows');

        $rsDettaglioFattura = $dblink->get_results("SELECT * FROM lista_fatture_dettaglio WHERE id_fattura = '".$rowFattura['id']."'");

        foreach ($rsDettaglioFattura as $rowDettaglioFattura) {
            $row = $rows->addChild('Row');
            $row->addChild('Code', "_XXX_REPLACE_VOID_XXX_");
            $row->addChild('Description', $rowDettaglioFattura['prezzo_prodotto']);
            $row->addChild('Qty', $rowDettaglioFattura['quantita']);
            $row->addChild('Um', 'nr');
            $row->addChild('Price', $rowDettaglioFattura['prezzo_prodotto']);
            $row->addChild('Discounts', "_XXX_REPLACE_VOID_XXX_");
            $VatCode = $row->addChild('VatCode', $rowDettaglioFattura['iva_prodotto']);
            $VatCode->addAttribute("Perc", $rowDettaglioFattura['iva_prodotto']);
            $VatCode->addAttribute("Class", 'Imponibile');
            $VatCode->addAttribute("Description", 'Aliquota '.$rowDettaglioFattura['iva_prodotto'].'%');
            $row->addChild('Total', $rowDettaglioFattura['prezzo_prodotto']);
            $row->addChild('Stock', 'false');
            $row->addChild('Notes', "_XXX_REPLACE_VOID_XXX_");
        }
    }
}

Header('Content-type: text/xml; charset=utf-8');
Header('Content-Disposition: attachment; filename='.$fileName);
$tmp = $xml->asXML();
print(str_replace(">_XXX_REPLACE_VOID_XXX_<", "><", $tmp));

?>
