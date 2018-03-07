<?php

if(isset($_GET['mese']) && isset($_GET['anno'])){
    $mese = $_GET['mese'];
    $anno = $_GET['anno'];
}else{
    $mese = date("m", strtotime('-1 month'));
    if($mese == "01"){
        $anno = date('Y', strtotime('-1 years'));
    }else{
        $anno = date('Y');
    }
}

$fileName = "fatture_prima_nota_".$mese."_".$anno.".txt";

$rowsFatture = $dblink->get_results("SELECT * FROM lista_fatture WHERE YEAR(data_creazione) = '$anno' AND MONTH(data_creazione) = '$mese'");

$righe = "";

foreach ($rowsFatture as $rowFattura) {
        
    if(!empty($rowFattura)){
        
        $datiFatturazione = $dblink->get_row("SELECT * FROM lista_aziende WHERE id = '".$rowFattura['id_azienda']."'",true);
        $rsDatiPagamento = $dblink->get_results("SELECT data_creazione, entrate FROM lista_costi WHERE id_fattura = '".$rowFattura['id']."'");

        $partitaIVA = $datiFatturazione['partita_iva'];
        $codiceFiscale = $datiFatturazione['codice_fiscale'];
        
        if(strlen($datiFatturazione['partita_iva']) == 16){
            if(strlen($datiFatturazione['codice_fiscale'])<16){
                $codiceFiscale = $datiFatturazione['partita_iva'];
            }
            $partitaIVA = "";
        }
        $riga = "";
        /*
        $riga .= "00000";       // TRF-DITTA
        $riga .= "3";           // TRF-VERSIONE
        $riga .= "0";           // TRF-TARC
        
        //Dati cliente fornitore
        $riga .= "00000";       // TRF-COD-CLIFOR
        $riga .= aggiungiSpaziAllaStringa(html_entity_decode($datiFatturazione['ragione_sociale']." ".$datiFatturazione['forma_giuridica'],ENT_QUOTES), 32);  //TRF-RASO
        $riga .= aggiungiSpaziAllaStringa(html_entity_decode($datiFatturazione['indirizzo'],ENT_QUOTES), 30);  //TRF-IND
        $riga .= aggiungiSpaziAllaStringa($datiFatturazione['cap'], 5);  //TRF-CAP
        $riga .= aggiungiSpaziAllaStringa(html_entity_decode($datiFatturazione['citta'],ENT_QUOTES), 25);  //TRF-CITTA
        $riga .= aggiungiSpaziAllaStringa(html_entity_decode($datiFatturazione['provincia'],ENT_QUOTES), 2);  //TRF-PROV
        $riga .= aggiungiSpaziAllaStringa($codiceFiscale, 16);  //TRF-COFI
        $riga .= aggiungiSpaziAllaStringa($partitaIVA, 11);  //TRF-PIVA
        $riga .= aggiungiSpaziAllaStringa(verificaSePersonaFisica($datiFatturazione['codice_fiscale']), 1);  //TRF-PF
        if(verificaSePersonaFisica($datiFatturazione['codice_fiscale']) == "S"){
            $riga .= aggiungiSpaziAllaStringa(strpos($datiFatturazione['ragione_sociale'], " "), 2, true, "0", false);  //TRF-DIVIDE
        }else{
            $riga .= aggiungiSpaziAllaStringa("00", 2, true, "0", false);  //TRF-DIVIDE
        }
        $riga .= aggiungiSpaziAllaStringa("0000", 4, true, "0", false);  //TRF-PAESE
        $riga .= aggiungiSpaziAllaStringa("", 12, false);  //TRF-PIVA-ESTERO
        $riga .= aggiungiSpaziAllaStringa("", 20, false);  //TRF-COFI-ESTERO
        $riga .= aggiungiSpaziAllaStringa("", 1, false);  //TRF-SESSO
        $riga .= aggiungiSpaziAllaStringa("", 8, false, "0");  //TRF-DTNAS
        $riga .= aggiungiSpaziAllaStringa("", 25, false);  //TRF-COMNA
        $riga .= aggiungiSpaziAllaStringa("", 2, false);  //TRF-PRVNA
        $riga .= aggiungiSpaziAllaStringa("", 4, false);  //TRF-PREF
        $riga .= aggiungiSpaziAllaStringa("", 20, false);  //TRF-NTELE-NUM
        $riga .= aggiungiSpaziAllaStringa("", 4, false);  //TRF-FAX-PREF
        $riga .= aggiungiSpaziAllaStringa("", 9, false);  //TRF-FAX-NUM
        $riga .= aggiungiSpaziAllaStringa("", 7, false, "0");  //TRF-CFCONTO
        $riga .= aggiungiSpaziAllaStringa("", 4, false, "0");  //TRF-CFCODPAG
        $riga .= aggiungiSpaziAllaStringa("", 5, false, "0");  //TRF-CFBANCA
        $riga .= aggiungiSpaziAllaStringa("", 5, false, "0");  //TRF-CFAGENZIA
        $riga .= aggiungiSpaziAllaStringa("", 1, false, "0");  //TRF-CFINTERM
        if(strtolower($rowFattura['tipo'])=="fattura"){
            $riga .= aggiungiSpaziAllaStringa("001", 3, true, "0");  //TRF-CAUSALE
            $riga .= aggiungiSpaziAllaStringa("FATTURA DI VENDITA", 15, true);  //TRF-CAU-DES
        }else{
            $riga .= aggiungiSpaziAllaStringa("002", 3, true, "0");  //TRF-CAUSALE
            $riga .= aggiungiSpaziAllaStringa("NOTA DI CREDITO", 15, true);  //TRF-CAU-DES
        }
        $riga .= aggiungiSpaziAllaStringa("", 18, false);  //TRF-CAU-AGG
        $riga .= aggiungiSpaziAllaStringa("", 34, false);  //TRF-CAU-AGG-1
        $riga .= aggiungiSpaziAllaStringa("", 34, false);  //TRF-CAU-AGG-2
        $riga .= aggiungiSpaziAllaStringa("", 8, false, "0");  //TRF-DATA-REGISTRAZIONE
        $riga .= aggiungiSpaziAllaStringa(str_replace("-", "", GiraDataOra($rowFattura['data_creazione'])), 8, true, "0");  //TRF-DATA-DOC
        $riga .= aggiungiSpaziAllaStringa("", 8, false, "0");  //TRF-NUM-DOC-FOR
        $riga .= aggiungiSpaziAllaStringa($rowFattura['codice'], 5, true, "0", false);  //TRF-NDOC
        $riga .= aggiungiSpaziAllaStringa($rowFattura['sezionale'], 2, true, "0", false);  //TRF-SERIE
        $riga .= aggiungiSpaziAllaStringa("", 6, false, "0");  //TRF-EC-PARTITA
        $riga .= aggiungiSpaziAllaStringa("", 4, false, "0");  //TRF-EC-PARTITA-ANNO
        $riga .= aggiungiSpaziAllaStringa("", 3, false, "0");  //TRF-EC-COD-VAL
        $riga .= aggiungiSpaziAllaStringa("", 13, false, "0");  //TRF-EC-CAMBIO
        $riga .= aggiungiSpaziAllaStringa("", 8, false, "0");  //TRF-EC-DATA-CAMBIO
        $riga .= aggiungiSpaziAllaStringa("", 16, false, "0");  //TRF-EC-TOT-DOC-VAL
        $riga .= aggiungiSpaziAllaStringa("", 16, false, "0");  //TRF-EC-TOT-IVA-VAL
        $riga .= aggiungiSpaziAllaStringa("", 6, false, "0");  //TRF-PLAFOND
        
        //Dati Iva
        $riga .= aggiungiSpaziAllaStringa(str_replace(".","", sprintf('%0.2f', abs($rowFattura['imponibile']))), 12, true, "0", false);  //TRF-IMPONIB
        $riga .= aggiungiSpaziAllaStringa($rowFattura['iva'], 3, true, "0", false);  //TRF-ALIQ
        $riga .= aggiungiSpaziAllaStringa("", 3, false, "0");  //TRF-ALIQ-AGRICOLA
        $riga .= aggiungiSpaziAllaStringa("", 2, false, "0");  //TRF-IVA11
        $riga .= aggiungiSpaziAllaStringa(str_replace(".","", sprintf('%0.2f', abs($rowFattura['importo']-$rowFattura['imponibile']))), 11, true, "0", false);  //TRF-IMPOSTA
        
        //SPAZIO VUOTO CON 0
        $riga .= aggiungiSpaziAllaStringa("", 217, false, "0"); //SPAZIO VUOTO ???
        
        //Totale fattura
        $riga .= aggiungiSpaziAllaStringa(str_replace(".","", sprintf('%0.2f', abs($rowFattura['importo']))), 12, true, "0", false);  //TRF-TOT-FATT
        
        //Conti di ricavo/costo
        $riga .= aggiungiSpaziAllaStringa("5810005", 7, true, "0");  //TRF-CONTO-RIC
        $riga .= aggiungiSpaziAllaStringa(str_replace(".","", sprintf('%0.2f', abs($rowFattura['imponibile']))), 12, true, "0", false);  //TRF-IMP-RIC
        
        //SPAZIO VUOTO CON 0
        $riga .= aggiungiSpaziAllaStringa("", 133, false, "0"); //SPAZIO VUOTO ???
        
        //Dati eventuale pagamento fattura o movimenti diversi
        $riga .= aggiungiSpaziAllaStringa("", 3, false, "0");  //TRF-CAU-PAGAM
        $riga .= aggiungiSpaziAllaStringa("", 15, false);  //TRF-CAU-DES-PAGAM
        $riga .= aggiungiSpaziAllaStringa("", 34, false);  //TRF-CAU-AGG-1-PAGAM
        $riga .= aggiungiSpaziAllaStringa("", 34, false);  //TRF-CAU-AGG-2-PAGAM
        $riga .= aggiungiSpaziAllaStringa("", 7, false, "0");  //TRF-CONTO
        $riga .= aggiungiSpaziAllaStringa("", 1, false);  //TRF-DA
        $riga .= aggiungiSpaziAllaStringa("", 12, false, "0");  //TRF-IMPORTO
        $riga .= aggiungiSpaziAllaStringa("", 18, false);  //TRF-CAU-AGGIUNT
        $riga .= aggiungiSpaziAllaStringa("", 6, false, "0");  //TRF-EC-PARTITA-PAG
        $riga .= aggiungiSpaziAllaStringa("", 4, false, "0");  //TRF-EC-PARTITA-ANNO-PAG
        $riga .= aggiungiSpaziAllaStringa("", 16, false, "0");  //TRF-EC-IMP-VAL
        
        //SPAZIO VUOTO CON  " " (spazio)
        $riga .= aggiungiSpaziAllaStringa("", 5056, false); //SPAZIO VUOTO ???
        
        //Ratei e risconti
        $riga .= aggiungiSpaziAllaStringa("0", 1, true);  //TRF-RIFER-TAB
        $riga .= aggiungiSpaziAllaStringa("", 2, false, "0");  //TRF-IND-RIGA
        $riga .= aggiungiSpaziAllaStringa("", 8, false, "0");  //TRF-DT-INI
        $riga .= aggiungiSpaziAllaStringa("", 8, false, "0");  //TRF-DT-FIN
        
        //SPAZIO VUOTO CON  " " (spazio)
        $riga .= aggiungiSpaziAllaStringa("", 171, false); //SPAZIO VUOTO ???
        
        $riga .= aggiungiSpaziAllaStringa("", 6, false, "0");  //TRF-D0C6
        
        //Ulteriori dati cliente fornitore
        $riga .= aggiungiSpaziAllaStringa("", 1, false);  //TRF-AN-OMONIMI
        $riga .= aggiungiSpaziAllaStringa("", 1, false, "0");  //TRF-AN-TIPO-SOGG
        
        //Ulteriori dati eventuale pagamento fattura o movimenti diversi
        $riga .= aggiungiSpaziAllaStringa("", 2, false, "0");  //TRF-EC-PARTITA-SEZ-PAG
        
        //SPAZIO VUOTO CON  " " (spazio)
        $riga .= aggiungiSpaziAllaStringa("", 158, false); //SPAZIO VUOTO ???
        
        //Ulteriori dati gestione professionista per eventuale pagamento incasso fattura o dati fattura
        $riga .= aggiungiSpaziAllaStringa("", 7, false, "0");  //TRF-NUM-DOC-PAG-PROF
        $riga .= aggiungiSpaziAllaStringa("", 8, false, "0");  //TRF-DATA-DOC-PAG-PROF
        $riga .= aggiungiSpaziAllaStringa("", 12, false, "0");  //TRF-RIT-ACC
        $riga .= aggiungiSpaziAllaStringa("", 12, false, "0");  //TRF-RIT-PREV
        $riga .= aggiungiSpaziAllaStringa("", 12, false, "0");  //TRF-RIT-1
        $riga .= aggiungiSpaziAllaStringa("", 12, false, "0");  //TRF-RIT-2
        $riga .= aggiungiSpaziAllaStringa("", 12, false, "0");  //TRF-RIT-3
        $riga .= aggiungiSpaziAllaStringa("", 12, false, "0");  //TRF-RIT-4
        
        //Ulteriori dati per unità produttive ricavi
        $riga .= aggiungiSpaziAllaStringa("", 2, false, "0");  //TRF-UNICA-RICAVI
        
        //SPAZIO VUOTO CON  " " (spazio)
        $riga .= aggiungiSpaziAllaStringa("", 14, false); //SPAZIO VUOTO ???
        
        //Ulteriori dati per unità produttive pagamenti
        $riga .= aggiungiSpaziAllaStringa("", 2, false, "0");  //TRF-UNICA-PAGAM
        
        //SPAZIO VUOTO CON  " " (spazio)
        $riga .= aggiungiSpaziAllaStringa("", 158, false); //SPAZIO VUOTO ???
        
        //Ulteriori dati cliente fornitore
        $riga .= aggiungiSpaziAllaStringa("", 4, false);  //TRF-FAX-PREF-1
        $riga .= aggiungiSpaziAllaStringa("", 20, false);  //TRF-FAX-NUM-1
        $riga .= aggiungiSpaziAllaStringa("", 1, false);  //TRF-SOLO-CLIFOR
        $riga .= aggiungiSpaziAllaStringa("", 1, false);  //TRF-80-SEGUENTE
        
        //Ulteriori dati gestione professionista per eventuale incasso / pagamento fattura o dati fattura
        $riga .= aggiungiSpaziAllaStringa("", 7, false, "0");  //TRF-CONTO-RIT-ACC
        $riga .= aggiungiSpaziAllaStringa("", 7, false, "0");  //TRF-CONTO-RIT-PREV
        $riga .= aggiungiSpaziAllaStringa("", 7, false, "0");  //TRF-CONTO-RIT-1
        $riga .= aggiungiSpaziAllaStringa("", 7, false, "0");  //TRF-CONTO-RIT-2
        $riga .= aggiungiSpaziAllaStringa("", 7, false, "0");  //TRF-CONTO-RIT-3
        $riga .= aggiungiSpaziAllaStringa("", 7, false, "0");  //TRF-CONTO-RIT-4
        
        //Varie
        $riga .= aggiungiSpaziAllaStringa("", 1, false);  //TRF-DIFFERIMENTO-IVA
        $riga .= aggiungiSpaziAllaStringa("", 1, false);  //TRF-STORICO
        $riga .= aggiungiSpaziAllaStringa("", 8, false, "0");  //TRF-STORICO-DATA
        $riga .= aggiungiSpaziAllaStringa("", 3, false, "0");  //TRF-CAUS-ORI
        
        //Prima nota previsionale dati aggiuntivi
        $riga .= aggiungiSpaziAllaStringa("", 1, false);  //TRF-PREV-TIPOMOV
        $riga .= aggiungiSpaziAllaStringa("", 1, false);  //TRF-PREV-RATRIS
        $riga .= aggiungiSpaziAllaStringa("", 8, false, "0");  //TRF-PREV-DTCOMP-INI
        $riga .= aggiungiSpaziAllaStringa("", 8, false, "0");  //TRF-PREV-DTCOMP-FIN
        $riga .= aggiungiSpaziAllaStringa("", 1, false);  //TRF-PREV-FLAG-CONT
        
        //Varie
        $riga .= aggiungiSpaziAllaStringa("", 20, false);  //TRF-DIFFERIMENTO
        $riga .= aggiungiSpaziAllaStringa("", 2, false, "0");  //TRF-CAUS-PREST-ANA
        $riga .= aggiungiSpaziAllaStringa("", 1, false, "0");  //TRF-EC-TIPO-PAGA
        $riga .= aggiungiSpaziAllaStringa("", 7, false, "0");  //TRF-CONTO-IVA-VEN-ACQ
        $riga .= aggiungiSpaziAllaStringa("", 11, false, "0");  //TRF-PIVA-VECCHIA
        $riga .= aggiungiSpaziAllaStringa("", 12, false);  //TRF-PIVA-ESTERO-VECCHIA
        $riga .= aggiungiSpaziAllaStringa("", 32, false);  //TRF-RISERVATO
        $riga .= aggiungiSpaziAllaStringa("", 8, false, "0");  //TRF-DATA-IVA-AGVIAGGI
        $riga .= aggiungiSpaziAllaStringa("", 1, false);  //TRF-DATA-AGG-ANA-REC4
        $riga .= aggiungiSpaziAllaStringa("", 6, false, "0");  //TRF-RIF-IVA-NOTE-CRED
        $riga .= aggiungiSpaziAllaStringa("", 1, false);  //TRF-RIF-IVA-ANNO-PREC
        $riga .= aggiungiSpaziAllaStringa("", 2, false, "0");  //TRF-NATURA-GIURIDICA
        $riga .= aggiungiSpaziAllaStringa("", 1, false);  //TRF-STAMPA-ELENCO
        
        //Iva Editoria
        $riga .= aggiungiSpaziAllaStringa("", 3, false, "0");  //TRF-PREC-FORF
        
        //SPAZIO VUOTO CON 0
        $riga .= aggiungiSpaziAllaStringa("", 21, false); //SPAZIO VUOTO ???
        
        $riga .= aggiungiSpaziAllaStringa("", 1, false);  //TRF-SOLO-MOV-IVA
        $riga .= aggiungiSpaziAllaStringa("", 16, false);  //TRF-COFI-VECCHIO
        $riga .= aggiungiSpaziAllaStringa("", 1, false);  //TRF-USA-PIVA-VECCHIA
        $riga .= aggiungiSpaziAllaStringa("", 1, false);  //TRF-USA-PIVA-EST-VECCHIA
        $riga .= aggiungiSpaziAllaStringa("", 1, false);  //TRF-USA-COFI-VECCHIO
        $riga .= aggiungiSpaziAllaStringa("", 1, false);  //TRF-ESIGIBILITA-IVA
        $riga .= aggiungiSpaziAllaStringa("", 1, false);  //TRF-TIPO-MOV-RISCONTI
        $riga .= aggiungiSpaziAllaStringa("", 1, false);  //TRF-AGGIORNA-EC
        $riga .= aggiungiSpaziAllaStringa("", 1, false);  //TRF-BLACKLIST-ANAG
        $riga .= aggiungiSpaziAllaStringa("", 1, false);  //TRF-BLACKLIST-IVA
        $riga .= aggiungiSpaziAllaStringa("", 6, false, "0");  //TRF-BLACKLIST-IVA-ANA
        $riga .= aggiungiSpaziAllaStringa("", 20, false);  //TRF-CONTEA-ESTERO
        $riga .= aggiungiSpaziAllaStringa("", 1, false);  //TRF-ART21-ANAG
        $riga .= aggiungiSpaziAllaStringa("", 1, false);  //TRF-ART21-IVA
        $riga .= aggiungiSpaziAllaStringa("", 1, false);  //TRF-RIF-FATTURA
        $riga .= aggiungiSpaziAllaStringa("", 1, false);  //TRF-RISERVATO-B
        $riga .= aggiungiSpaziAllaStringa("", 1, false);  //TRF-MASTRO-CF
        $riga .= aggiungiSpaziAllaStringa("", 1, false);  //TRF-MOV-PRIVATO
        $riga .= aggiungiSpaziAllaStringa("", 1, false);  //TRF-SPESE-MEDICHE
        $riga .= aggiungiSpaziAllaStringa("", 2, false);  //FILLER
        
        $righe.=$riga."\r\n";
        
        */
        // Pagamento
        if(!empty($rsDatiPagamento)){
            $riga = "";
            $riga .= "00001";       // TRF-DITTA
            $riga .= "3";           // TRF-VERSIONE
            $riga .= "1";           // TRF-TARC

            //SPAZIO VUOTO CON " " (spazio)
            $riga .= aggiungiSpaziAllaStringa("", 2330, false); //SPAZIO VUOTO ???

            //Dati portafoglio

            $rowBancaPagamento = $dblink->get_row("SELECT * FROM lista_fatture_banche WHERE id = '".$rowFattura['id_fatture_banche']."'", true);

            $riga .= aggiungiSpaziAllaStringa("001", 3, true, "0");  //TFR-POR-CODPAG
            $riga .= aggiungiSpaziAllaStringa(substr($rowBancaPagamento['iban'], 5, 5), 5, true, "0");  //TFR-POR-BANCA
            $riga .= aggiungiSpaziAllaStringa(substr($rowBancaPagamento['iban'], 10, 5), 5, true, "0");  //TFR-POR-AGENZIA
            $riga .= aggiungiSpaziAllaStringa(mb_convert_encoding(htmlspecialchars_decode(html_entity_decode($rowBancaPagamento['nome'])), "UTF-8", "HTML-ENTITIES"), 30, true);  //TFR-POR-DESAGENZIA

            $riga .= aggiungiSpaziAllaStringa(count($rsDatiPagamento), 2, true, "0", false);  //TFR-POR-TOT-RATE
            $riga .= aggiungiSpaziAllaStringa(str_replace(".","", sprintf('%0.2f', abs($rowFattura['importo']-$rowFattura['imponibile']))), 12, true, "0", false);  //TFR-POR-TOT-DOC

            //Dettaglio effetti
            $numRata = 1;
            foreach ($rsDatiPagamento as $rowDatiPagamento) {
                $riga .= aggiungiSpaziAllaStringa($numRata, 2, true, "0", false);  //TFR-POR-NUM-RATA
                $riga .= aggiungiSpaziAllaStringa(str_replace("-", "", GiraDataOra($rowFattura['data_scadenza'])), 8, true, "0");  //TFR-POR-DATASCAD
                $riga .= aggiungiSpaziAllaStringa("2", 1, true, "0", false);  //TFR-POR-TIPOEFF
                $riga .= aggiungiSpaziAllaStringa(str_replace(".","", sprintf('%0.2f', abs($rowDatiPagamento['entrate']))), 12, true, "0", false);  //TFR-POR-IMPORTO-DEF
                $riga .= aggiungiSpaziAllaStringa("", 15, false, "0");  //TFR-POR-IMPORTO-EFFVAL
                $riga .= aggiungiSpaziAllaStringa("", 12, false, "0");  //TFR-POR-IMPORTO-BOLLI
                $riga .= aggiungiSpaziAllaStringa("", 15, false, "0");  //TFR-POR-IMPORTO-BOLLIVAL
                $riga .= aggiungiSpaziAllaStringa("N", 1, true);  //TFR-POR-FLAG
                $riga .= aggiungiSpaziAllaStringa("", 1, false);  //TFR-POR-TIPO-RD

                $numRata++;
            }

            //SPAZIO VUOTO CON " " (spazio)

            $riga .= aggiungiSpaziAllaStringa("", (7002-strlen($riga)+1), true); //SPAZIO VUOTO ???

            $righe.=$riga."\r\n";
        }
    }
    
}

Header('Content-type: text/txt; charset=utf-8');
Header('Content-Disposition: attachment; filename='.$fileName);
print($righe);

function aggiungiSpaziAllaStringa($testo, $numSpazi = 0 , $contaStringa = true, $carattere = " ",$dopo = true){
    
    $testo = pulisciRigaDiTesto($testo);
    
    if($contaStringa){
        $numPartenza = mb_strlen($testo, 'UTF-8');
    }else{
        $numPartenza = 0;
    }
    
    $spazi = "";
    
    if($numPartenza > $numSpazi){
        $testo = substr($testo, 0, $numSpazi);
    }else{
        for($i = $numPartenza; $i<$numSpazi; $i++){
            $spazi.=$carattere;
        }
    }
    if($dopo){
        return $testo.$spazi;
    }else{
        return $spazi.$testo;
    }
}

function verificaSePersonaFisica($codiceFiscale){
    global $dblink;
    
    $numRow = $dblink->num_rows("SELECT * FROM lista_professionisti WHERE codice_fiscale = '".$dblink->filter($codiceFiscale)."'");
    
    if($numRow > 0){
        return "S";
    }else{
        return "N";
    }
}

function pulisciRigaDiTesto($testo){
    
    $testo = str_replace("à", "a", $testo);
    $testo = str_replace("è", "e", $testo);
    $testo = str_replace("é", "e", $testo);
    $testo = str_replace("ù", "u", $testo);
    $testo = str_replace("ì", "i", $testo);
    //$testo = str_replace("&", "e", $testo);
    
    $testo = preg_replace('/[^A-Za-z0-9\. -_+&,]/', '', $testo);
    
    return trim($testo);
}
?>