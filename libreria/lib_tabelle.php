<?php
/*
  @TODO AGGIUNGERE ALTRE VARIABILI DI CONFIGURAZIONE PER IL COLORE DEL PORTLET E PER L'ICONA. VEDERE SE FARE UN ALTRA FUNZIOEN DIVERSA DA BASE
  Link: http://keenthemes.com/preview/metronic/theme/admin_1/table_datatables_responsive.html
 */

function stampa_table_datatables_responsive($query, $titolo = '', $stile = '', $colore_tabella = COLORE_PRIMARIO, $showTotal = false) {
    global $dblink;

    $colore_tabella = strlen($colore_tabella) > 0 ? $colore_tabella : COLORE_PRIMARIO;
    $stile = strlen($stile) > 0 ? $stile : "tabella_base";

    echo '<div class="portlet box ' . $colore_tabella . '">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-cogs"></i>' . $titolo . '</div>
                <div class="tools"></div>
          </div>
          <div class="portlet-body">
            <table class="table table-striped table-bordered table-hover dt-responsive" width="100%" id="' . $stile . '">';

    $rows = $dblink->get_results($query);
    $fields = $dblink->list_fields($query);
    
    $headTable = "";
    foreach ($fields as $field) {
        if (strpos($field->name, "fa-") !== false) {
            $headTable .= "<td scope=\"col\" style=\"text-transform:uppercase; text-align:center;\"><i class=\"fa " . $field->name . " grey\"></i></td>\n";
        } else if (strtolower($field->name) == "selezione") {
            $headTable .= '<td scope=\"col\" style="text-align:center; vertical-align:middle;"><label class="mt-checkbox"><input name="txt_checkbox_all" id="txt_checkbox_all" type="checkbox"  value="'.count($rows).'"><span></span></label></td>';
        } else
            $headTable .= "<td scope=\"col\" style=\"text-transform:uppercase; text-align:center;\">" . pulisciCampoEtichetta($field->name) . "</td>\n";
    }
    echo "<thead><tr>$headTable</tr></thead>";
    
    if($showTotal){
        
        echo "<tfoot style=\"display: table-header-group;\">
            <tr>";
            $n = 0;
            foreach ($fields as $field) {
                echo "<th id=\"colTot".$n."\" style=\"text-align:center;\"></th>";
                $n++;
            }
        echo "</tr>
        </tfoot>";
        
    }
    
    $rowTable = "";
    $r = 0;
    foreach ($rows as $row) {
        $rowTable .= "<tr>\n";
        $c = 0; //resetto numero colonna a ogni nuova riga
        foreach ($row as $column) {
            if(strtolower($fields[$c]->name) == "stato" || strtolower($fields[$c]->name) == "nome") {
                //echo $column;
                if (strpos($column, "|") !== false) {
                    $tmpStato = explode("|", $column);
                    $classeColore = array($tmpStato[0]);
                    $column = $tmpStato[1];
                } else {
                    //$classeColore = $dblink->get_row("SELECT colore_sfondo, nome_alias FROM lista_richieste_stati WHERE nome='" . $dblink->filter($column) . "' AND colore_sfondo!=''");
                    $classeColore = false;
                }
            } else {
                $classeColore = false;
            }
            if ($classeColore != false) {
                $rowTable .= '<td style="text-align:center; vertical-align:middle;"><span class="badge bold bg-' . $classeColore[0] . ' bg-font-' . $classeColore[0] . '"> ' . (strlen($classeColore[1])>0 ? $classeColore[1] : $column) . ' </span></td>';
            } else {
                switch (strtolower($column)) {
                    case "pagata parziale":
                    case "fattura pagata parziale":
                        $rowTable .= '<td style="text-align:center; vertical-align:middle;"><span class="badge bold bg-red-pink bg-font-red-pink"> ' . $column . ' </span></td>';
                    break;

                    case "disponibile":
                    case "richiamare":
                    case "chiuso":
                    case "attivo":
                    case "attiva":
                    case "pagata":
                    case "lavorazione terminata":
                    case "si":
                    case "fatto":
                    case "completato":
                    case "ticket chiuso":
                        $rowTable .= '<td style="text-align:center; vertical-align:middle;"><span class="badge bold bg-green-jungle bg-font-green-jungle"> ' . $column . ' </span></td>';
                    break;

                    case "in attesa":
                    case "in corso":
                    case "in lavorazione":
                    case "in attesa di controllo":
                    case "in attesa di emissione":
                    case "chiusa in attesa di controllo":
                    case "risposta cliente":
                        $rowTable .= '<td style="text-align:center; vertical-align:middle;"><span class="badge bold bg-yellow-saffron bg-font-yellow-saffron"> ' . $column . ' </span></td>';
                    break;

                    case "negativo":
                    case "non disponibile":
                    case "non attivo":
                    case "non attiva":
                    case "annullata":
                    case "mai contattato":
                    case "non interessa":
                    case "non letto":
                    case "no":
                    case "non completato":
                    case "configurazione scaduta e disattivata":
                    case "scaduto e disattivato":
                    case "scaduto":
                    case "non letto":
                    case "abbonamento disabilitato":
                    case "corso disabilitato":
                    case "ticket nuovo":
                    case "nota di credito":
                        $rowTable .= '<td style="text-align:center; vertical-align:middle;"><span class="badge bold bg-red-thunderbird bg-font-red-thunderbird"> ' . $column . ' </span></td>';
                    break;

                    case "venduto":
                    case "terminata":
                    case "configurazione":
                    case "risposta operatore":
                        $rowTable .= '<td style="text-align:center; vertical-align:middle;"><span class="badge bold bg-blue-steel bg-font-blue-steel"> ' . $column . ' </span></td>';
                    break;

                    case "trasferito":
                        $rowTable .= '<td style="text-align:center; vertical-align:middle;"><span class="badge bold bg-purple-studio bg-font-purple-studio"> ' . $column . ' </span></td>';
                    break;

                    case "img":
                        $rowTable .= '<td style="text-align:center; vertical-align:middle;">' . $column . '</td>';
                    break;

                    default:
                        if(strlen($fields[$c]->orgname)>0) $nomeCampo = strtolower($fields[$c]->orgname);
                        else $nomeCampo = strtolower($fields[$c]->name);
                        if (strpos($nomeCampo, "data") !== false || $nomeCampo == "iniziato il") { //|| $nomeCampo == "in corso" || $nomeCampo == "validit&agrave;"
                            $rowTable .= '<td style="text-align:center; vertical-align:middle;">' . GiraDataOra($column) . '</td>';
                        }else if (strtolower($fields[$c]->name) == "selezione") {
                            $rowTable .= '<td style="text-align:center; vertical-align:middle;"><label class="mt-checkbox"><input name="txt_checkbox_' . $r . '" id="txt_checkbox_' . $r . '" type="checkbox"  value="'.$column.'"><span></span></label></td>';
                        } else {
                            $rowTable .= '<td style="text-align:center; vertical-align:middle;">' . $column . '</td>';
                        }
                    break;
                }
            }
            $c++; //incremento numero colonna
        }
        $rowTable .= "</tr>\n";
        $r++;
    }
    echo '<tbody>' . $rowTable . '</tbody>';
    echo '</table></div></div>';
}

/*
  @TODO AGGIUNGERE/DEFINIRE VARIABILI DI CONFIGURAZIONE
  Link: http://keenthemes.com/preview/metronic/theme/admin_1/table_static_basic.html
 */

function stampa_table_static_basic($query, $stile, $titolo, $colore_tabella = COLORE_PRIMARIO, $icona = 'fa fa-cogs') {
    global $dblink;

    $colore_tabella = strlen($colore_tabella) > 0 ? $colore_tabella : COLORE_PRIMARIO;
    $stile = strlen($stile) > 0 ? $stile : "tabella_base";

    echo '<div class="portlet box ' . $colore_tabella . '">
            <div class="portlet-title">
                <div class="caption">
                    <i class="' . $icona . '"></i>' . $titolo . '
                </div>
                <div class="tools"> </div>
            </div>
          <div class="portlet-body">
            <div class="table-scrollable">
                <table class="table table-striped table-bordered table-hover" id="'.$stile.'">';

    $rows = $dblink->get_results($query);
    $fields = $dblink->list_fields($query);
    
    $headTable = "";
    foreach ($fields as $field) {
        if (strpos($field->name, "fa-") !== false) {
            $headTable .= "<td scope=\"col\" style=\"text-transform:uppercase; text-align:center;\"><i class=\"fa " . $field->name . " grey\"></i></td>\n";
        } else if (strtolower($field->name) == "selezione") {
            $headTable .= '<td scope=\"col\" style="text-align:center; vertical-align:middle;"><label class="mt-checkbox"><input name="txt_checkbox_all" id="txt_checkbox_all" type="checkbox"  value="'.count($rows).'"><span></span></label></td>';
        } else
            $headTable .= "<td scope=\"col\" style=\"text-transform:uppercase; text-align:center;\">" . pulisciCampoEtichetta($field->name) . "</td>\n";
    }
    echo "<thead><tr>$headTable</tr></thead>";
    $r=0;
    $rowTable = "";
    foreach ($rows as $row) {
        $rowTable .= "<tr>\n";
        $c = 0;
        foreach ($row as $column) {
            if (strtolower($fields[$c]->name) == "stato" || strtolower($fields[$c]->name) == "nome") {
                //echo $column;
                if (strpos($column, "|") !== false) {
                    $tmpStato = explode("|", $column);
                    $classeColore = array($tmpStato[0]);
                    $column = $tmpStato[1];
                } else {
                    //$classeColore = $dblink->get_row("SELECT colore_sfondo, nome_alias FROM lista_richieste_stati WHERE nome='" . $dblink->filter($column) . "' AND colore_sfondo!=''");
                    $classeColore = false;
                }
            } else {
                $classeColore = false;
            }
            if ($classeColore != false) {
                $rowTable .= '<td style="text-align:center; vertical-align:middle;"><span class="badge bold bg-' . $classeColore[0] . ' bg-font-' . $classeColore[0] . '"> ' . (strlen($classeColore[1])>0 ? $classeColore[1] : $column) . ' </span></td>';
            } else {
                switch (strtolower($column)) {
                    case "pagata parziale":
                    case "fattura pagata parziale":
                        $rowTable .= '<td style="text-align:center; vertical-align:middle;"><span class="badge bold bg-red-pink bg-font-red-pink"> ' . $column . ' </span></td>';
                    break;

                    case "disponibile":
                    case "richiamare":
                    case "chiuso":
                    case "attivo":
                    case "attiva":
                    case "pagata":
                    case "lavorazione terminata":
                    case "si":
                    case "fatto":
                    case "completato":
                    case "ticket chiuso":
                        $rowTable .= '<td style="text-align:center; vertical-align:middle;"><span class="badge bold bg-green-jungle bg-font-green-jungle"> ' . $column . ' </span></td>';
                    break;

                    case "in attesa":
                    case "in corso":
                    case "in lavorazione":
                    case "in attesa di controllo":
                    case "in attesa di emissione":
                    case "chiusa in attesa di controllo":
                    case "risposta cliente":
                        $rowTable .= '<td style="text-align:center; vertical-align:middle;"><span class="badge bold bg-yellow-saffron bg-font-yellow-saffron"> ' . $column . ' </span></td>';
                    break;

                    case "negativo":
                    case "non disponibile":
                    case "non attivo":
                    case "non attiva":
                    case "annullata":
                    case "mai contattato":
                    case "non interessa":
                    case "non letto":
                    case "no":
                    case "non completato":
                    case "configurazione scaduta e disattivata":
                    case "scaduto e disattivato":
                    case "scaduto":
                    case "non letto":
                    case "abbonamento disabilitato":
                    case "corso disabilitato":
                    case "ticket nuovo":
                    case "nota di credito":
                        $rowTable .= '<td style="text-align:center; vertical-align:middle;"><span class="badge bold bg-red-thunderbird bg-font-red-thunderbird"> ' . $column . ' </span></td>';
                    break;

                    case "venduto":
                    case "terminata":
                    case "configurazione":
                    case "risposta operatore":
                        $rowTable .= '<td style="text-align:center; vertical-align:middle;"><span class="badge bold bg-blue-steel bg-font-blue-steel"> ' . $column . ' </span></td>';
                    break;

                    case "trasferito":
                        $rowTable .= '<td style="text-align:center; vertical-align:middle;"><span class="badge bold bg-purple-studio bg-font-purple-studio"> ' . $column . ' </span></td>';
                    break;

                    case "img":
                        $rowTable .= '<td style="text-align:center; vertical-align:middle;">' . $column . '</td>';
                    break;

                    default:
                        if(strlen($fields[$c]->orgname)>0) $nomeCampo = strtolower($fields[$c]->orgname);
                        else $nomeCampo = strtolower($fields[$c]->name);
                        if (strpos($nomeCampo, "data") !== false || $nomeCampo == "iniziato il") { //$nomeCampo == "in corso" || $nomeCampo == "validit&agrave;" ||
                            $rowTable .= '<td style="text-align:center; vertical-align:middle;">' . GiraDataOra($column) . '</td>';
                        }else if (strtolower($fields[$c]->name) == "nome_obiezione") {
                            $tmp = explode("|", $column);
                            $rowTable .=  '<td style="text-align:center; vertical-align:middle;">' . print_select2("SELECT id as valore, nome, $tmp[0] AS var_1, $tmp[1] AS var_2, id AS var_3 FROM lista_obiezioni WHERE stato='Attivo' ORDER BY nome ASC", "txt_" . $r . "_" . $nomeCampo, '', "scriviNomeObiezioneInCalendario", false, 'tooltips select_obiezione', 'data-container="body" data-placement="top" data-original-title="NOME OBIEZIONE"') . '</td>';
                        }else if (strtolower($fields[$c]->name) == "selezione") {
                            if($column>0){
                                $rowTable .= '<td style="text-align:center; vertical-align:middle;"><label class="mt-checkbox"><input name="txt_checkbox_' . $r . '" id="txt_checkbox_' . $r . '" type="checkbox"  value="'.$column.'"><span></span></label></td>';
                            }else{
                                $rowTable .= '<td style="text-align:center; vertical-align:middle;">&nbsp;</td>';
                            }
                        } else {
                            $rowTable .= '<td style="text-align:center; vertical-align:middle;">' . $column . '</td>';
                        }
                    break;
                }
            }
            $c++;
        }
        $rowTable .= "</tr>\n";
        $r++;
    }
    echo '<tbody>' . $rowTable . '</tbody>';
    echo '</table></div></div></div>';
}

/*
  @TODO AGGIUNGERE/DEFINIRE VARIABILI DI CONFIGURAZIONE
  VERSIONE STAMPA INPUT APERTI
  Link: http://keenthemes.com/preview/metronic/theme/admin_1/table_static_basic.html
 */

function stampa_table_static_basic_input($tabella, $query, $stile, $titolo, $colore_tabella = COLORE_PRIMARIO, $allReadonly = false, $record = 0) {
    global $dblink, $where_lista_password;

    $colore_tabella = (strlen($colore_tabella) > 0) ? $colore_tabella : COLORE_PRIMARIO;
    $stile = (strlen($stile) > 0) ? $stile : 'tabella_base';

    echo '<div class="portlet box ' . $colore_tabella . '">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-cogs"></i>' . $titolo . '
                </div>
                <div class="tools"></div>
            </div>
          <div class="portlet-body">
            <div class="table-scrollable">
                <table class="table table-striped table-bordered table-hover">';

    /*
     * CODICE DI PROVA DAVIDE PER USARE LA NUOVA CLASSE DB
     * NON ATTIVO PER PROBLEMA SUI CONCAT DI CROCCO.
     */
    $rows = $dblink->get_results($query, true);
    $fields = $dblink->list_fields($query);
    $headTable = "";
    foreach ($fields as $field) {
        if (strpos($field->name, "iterator") !== false) {
            //VOID
        }else if (strpos($field->name, "fa-") !== false) {
            $headTable .= "<td scope=\"col\" style=\"text-transform:uppercase; text-align:center;\"><i class=\"fa " . $field->name . " grey\"></i></td>\n";
        }else if ((strtolower($field->name) == "nome_prodotto" OR strtolower($field->name) == "prodotto") AND ( $tabella == "lista_preventivi_dettaglio" OR $tabella == "lista_fatture_dettaglio" OR $tabella == "lista_prodotti_dettaglio")) {
            $headTable .= "<td scope=\"col\" style=\"text-transform:uppercase; text-align:center; vertical-align:middle;\">Prodotto</td>";
        } else if (strtolower($field->name) == "id") {
            $headTable .= '<td style="text-align:center; vertical-align:middle; display: none;" scope="col">' . pulisciCampoEtichetta($field->name) . '</td>';
        } else if (strtolower($field->name) == "selezione") {
            $headTable .= '<td scope=\"col\" style="text-align:center; vertical-align:middle;"><label class="mt-checkbox"><input name="txt_checkbox_all" id="txt_checkbox_all" type="checkbox"  value="'.count($rows).'"><span></span></label></td>';
        } else {
            $headTable .= "<td scope=\"col\" style=\"text-transform:uppercase; text-align:center;\">" . pulisciCampoEtichetta($field->name) . "</td>\n";
        }
    }
    echo "<thead><tr>$headTable</tr></thead>";
    $id = 0;
    $rowTable = "";
    foreach ($rows as $row) {
        $rowTable .= "<tr>\n";
        $c = 0;
        foreach ($row as $column) {
            
            switch (strtolower($column)) {

                default:
                    $readonly = false;
                    $nome_colonna = $fields[$c]->orgname;
                    if (strlen($nome_colonna) < 1)
                        $nome_colonna = $fields[$c]->name;

                    if(strtolower($nome_colonna)=="iterator") continue;

                    if(strpos(strtolower($fields[$c]->name), "|")!==false){
                        $tmp = explode("|",$fields[$c]->name);
                        $readonly = $tmp[1]=="readonly" ? true : false;
                    }

                    switch (strtolower($nome_colonna)) {
                        case "id":
                            if(strtolower($fields[$c]->name)=="selezione"){
                                $rowTable .=  '<td style="text-align:center; vertical-align:middle;"><label class="mt-checkbox"><input name="txt_checkbox_' . $record . '" id="txt_checkbox_' . $record . '" type="checkbox"  value="' . $column . '"><span></span></label></td>';
                            }else{
                                $rowTable .=  '<td style="text-align:center; vertical-align:middle; display: none;"><input name="txt_' . $record . '_' . $nome_colonna . '" id="txt_' . $record . '_' . $nome_colonna . '" type="hidden" class="form-control" placeholder="' . $nome_colonna . '" value="' . $column . '" readonly></td>';
                            }
                        break;

                        case "selezione":
                            $rowTable .=  '<td style="text-align:center; vertical-align:middle;"><label class="mt-checkbox"><input name="txt_checkbox_' . $record . '" id="txt_checkbox_' . $record . '" type="checkbox"  value=""><span></span></label></td>';
                        break;

                        case "nome_prodotto":
                        case "id_prodotto":
                            if($tabella == "lista_preventivi_dettaglio" OR $tabella == "lista_fatture_dettaglio")
                                $rowTable .=  '<td style="text-align:center; vertical-align:middle;">' . print_select2("SELECT id as valore, nome, prezzo_pubblico AS var_1, iva AS var_2, if(quantita>0,quantita,1) AS var_3, $record AS var_4 FROM lista_prodotti WHERE stato='Attivo' ORDER BY nome ASC", "txt_" . $record . "_" . $nome_colonna, $column, "scriviDentroListaPreventiviDettaglioTXT", false, 'tooltips select_prodotto', 'data-container="body" data-placement="top" data-original-title="PRODOTTO"') . '</td>';
                            else if($tabella == "lista_prodotti_dettaglio")
                                $rowTable .=  '<td style="text-align:center; vertical-align:middle;">' . print_select2("SELECT id as valore, nome, prezzo_pubblico AS var_1, iva AS var_2, if(quantita>0,quantita,1) AS var_3, $record AS var_4 FROM lista_prodotti WHERE stato='Attivo' AND gruppo='CORSO' ORDER BY nome ASC", "txt_" . $record . "_" . $nome_colonna, $column, "scriviDentroListaPreventiviDettaglioTXT", false, 'tooltips select_prodotto', 'data-container="body" data-placement="top" data-original-title="PRODOTTO"') . '</td>';
                            else
                                $rowTable .=  '<td style="text-align:center; vertical-align:middle;"><input name="txt_' . $record . '_' . $nome_colonna . '" id="txt_' . $record . '_' . $nome_colonna . '" type="text" class="form-control" placeholder="' . $nome_colonna . '" value="' . $column . '"></td>';
                        break;
                        
                        case "nome_provvigione":
                        case "id_provvigione":
                            if($tabella == "lista_preventivi_dettaglio" OR $tabella == "lista_fatture_dettaglio")
                                $rowTable .=  '<td style="text-align:center; vertical-align:middle;">' . print_select2("SELECT id as valore, nome FROM lista_provvigioni WHERE stato='Attivo' ORDER BY nome ASC", "txt_" . $record . "_" . $nome_colonna, $column, "", false, 'tooltips select_provvigione-allow-clear', 'data-container="body" data-placement="top" data-original-title="PROVVIGIONE"') . '</td>';
                            else
                                $rowTable .=  '<td style="text-align:center; vertical-align:middle;"><input name="txt_' . $record . '_' . $nome_colonna . '" id="txt_' . $record . '_' . $nome_colonna . '" type="text" class="form-control" placeholder="' . $nome_colonna . '" value="' . $column . '"></td>';
                        break;
                        
                        case "docente":
                        case "id_docente":
                            if($tabella == "matrice_corsi_docenti" OR $tabella == "matrice_corsi_docenti")
                                $rowTable .=  '<td style="text-align:center; vertical-align:middle;">' . print_select2("SELECT id as valore, concat(cognome, ' ', nome) AS nome FROM lista_docenti WHERE 1 ORDER BY cognome, nome ASC", "txt_" . $record . "_" . $nome_colonna, $column, "", false, 'tooltips select_docente-allow-clear', 'data-container="body" data-placement="top" data-original-title="DOCENTE"') . '</td>';
                            else
                                $rowTable .=  '<td style="text-align:center; vertical-align:middle;"><input name="txt_' . $record . '_' . $nome_colonna . '" id="txt_' . $record . '_' . $nome_colonna . '" type="text" class="form-control" placeholder="' . $nome_colonna . '" value="' . $column . '"></td>';
                        break;
                        
                        case "risposta":
                            if($tabella == "lista_domande")
                                $rowTable .=  '<td style="text-align:center; vertical-align:middle;">' . print_select_static(array("0" => "Falso", "1" => "Vero"), "txt_" . $record . "_" . $nome_colonna, $column, "", false, 'tooltips select_risposta', 'data-container="body" data-placement="top" data-original-title="RISPOSTA"') . '</td>';
                            else
                                $rowTable .=  '<td style="text-align:center; vertical-align:middle;"><input name="txt_' . $record . '_' . $nome_colonna . '" id="txt_' . $record . '_' . $nome_colonna . '" type="text" class="form-control" placeholder="' . $nome_colonna . '" value="' . $column . '"></td>';
                        break;

                        case "nome_professionista":
                        case "id_professionista":
                            if($tabella == "lista_fatture_dettaglio"){
                                $tmp = explode("|",$column);
                                $idAzienda = $tmp[1];
                                $idProfessionista = $tmp[0];
                                if($idAzienda>0){
                                    $rowTable .=  '<td style="text-align:center; vertical-align:middle;">'. print_select2("SELECT id as valore, CONCAT(cognome,' ',nome) AS nome FROM lista_professionisti WHERE lista_professionisti.id IN (SELECT matrice_aziende_professionisti.id_professionista FROM matrice_aziende_professionisti WHERE id_azienda = '$idAzienda' ) ORDER BY nome ASC", "txt_" . $record . "_" . $nome_colonna, $idProfessionista, "", false, 'tooltips select_partecipante', 'data-container="body" data-placement="top" data-original-title="PARTECIPANTE"') . '</td>';
                                }else{
                                    $rowTable .=  '<td style="text-align:center; vertical-align:middle;">'. print_select2("SELECT id as valore, CONCAT(cognome,' ',nome) AS nome FROM lista_professionisti WHERE lista_professionisti.id = '$idProfessionista' ORDER BY nome ASC", "txt_" . $record . "_" . $nome_colonna, $idProfessionista, "", false, 'tooltips select_partecipante', 'data-container="body" data-placement="top" data-original-title="PARTECIPANTE"') . '</td>';
                                }
                            } else
                                $rowTable .=  '<td style="text-align:center; vertical-align:middle;"><input name="txt_' . $record . '_' . $nome_colonna . '" id="txt_' . $record . '_' . $nome_colonna . '" type="text" class="form-control" placeholder="' . $nome_colonna . '" value="' . $column . '"></td>';
                        break;

                        case "id_classe":
                        case "classe":
                            if($tabella == "lista_corsi_configurazioni" OR $tabella == "lista_corsi")
                                $rowTable .=  '<td style="text-align:center; vertical-align:middle;">' . print_select2("SELECT id as valore, nome FROM lista_classi WHERE stato='Attivo' ORDER BY nome ASC", "txt_" . $record . "_" . $nome_colonna, $column, "", false, 'tooltips select_classi', 'data-container="body" data-placement="top" data-original-title="CLASSI"') . '</td>';
                            else
                                $rowTable .=  '<td style="text-align:center; vertical-align:middle;"><input name="txt_' . $record . '_' . $nome_colonna . '" id="txt_' . $record . '_' . $nome_colonna . '" type="text" class="form-control" placeholder="' . $nome_colonna . '" value="' . $column . '"></td>';
                        break;
                        
                        
                        case "id_professione":
                        case "professione":
                            if($tabella == "lista_corsi_configurazioni")
                                $rowTable .=  '<td style="text-align:center; vertical-align:middle;">' . print_select2("SELECT nome as valore, nome FROM lista_professioni WHERE 1 ORDER BY nome ASC", "txt_" . $record . "_" . $nome_colonna, $column, "", false, 'tooltips select_classi', 'data-container="body" data-placement="top" data-original-title="PROFESSIONE"') . '</td>';
                            else
                                $rowTable .=  '<td style="text-align:center; vertical-align:middle;"><input name="txt_' . $record . '_' . $nome_colonna . '" id="txt_' . $record . '_' . $nome_colonna . '" type="text" class="form-control" placeholder="' . $nome_colonna . '" value="' . $column . '"></td>';
                        break;

                        case "id_agente":
                        case "commerciale":
                            if($tabella == "calendario")
                                $rowTable .=  '<td style="text-align:center; vertical-align:middle;">' . print_select2("SELECT id as valore, CONCAT(cognome, ' ', nome) AS nome FROM lista_password WHERE stato='Attivo' AND livello='commerciale' ".$where_lista_password." ORDER BY nome ASC", "txt_" . $record . "_" . $nome_colonna, $column, "", false, 'tooltips select_commerciale', 'data-container="body" data-placement="top" data-original-title="COMMERCIALE"') . '</td>';
                            else
                                $rowTable .=  '<td style="text-align:center; vertical-align:middle;"><input name="txt_' . $record . '_' . $nome_colonna . '" id="txt_' . $record . '_' . $nome_colonna . '" type="text" class="form-control" placeholder="' . $nome_colonna . '" value="' . $column . '"></td>';
                        break;

                        case "id_attestato":
                        case "attestato":
                            if($tabella == "lista_corsi_configurazioni" OR $tabella == "lista_corsi")
                                $rowTable .=  '<td style="text-align:center; vertical-align:middle;">' . print_select2("SELECT id as valore, nome FROM lista_attestati WHERE stato='Attivo' ORDER BY nome ASC", "txt_" . $record . "_" . $nome_colonna, $column, "", false, 'tooltips select_attestati', 'data-container="body" data-placement="top" data-original-title="ATTESTATI"') . '</td>';
                            else
                                $rowTable .=  '<td style="text-align:center; vertical-align:middle;"><input name="txt_' . $record . '_' . $nome_colonna . '" id="txt_' . $record . '_' . $nome_colonna . '" type="text" class="form-control" placeholder="' . $nome_colonna . '" value="' . $column . '"></td>';
                        break;

                        case "stato":
                            if($tabella == "lista_corsi_configurazioni" OR $tabella == "lista_corsi" OR $tabella == "lista_prodotti" OR $tabella == "lista_prodotti_dettaglio")
                                $rowTable .=  '<td style="text-align:center; vertical-align:middle;">' . print_select_static(array("Attivo"=>"Attivo|green-jungle", "Non Attivo"=>"Non Attivo|red-thunderbird"),"txt_" . $record . "_" . $nome_colonna,$column,"", false, "bs-select", 'data-container="body" data-placement="top" data-original-title="STATO"'). '</td>';
                            else
                                $rowTable .=  '<td style="text-align:center; vertical-align:middle;"><input name="txt_' . $record . '_' . $nome_colonna . '" id="txt_' . $record . '_' . $nome_colonna . '" type="text" class="form-control" placeholder="' . $nome_colonna . '" value="' . $column . '" readonly></td>';
                        break;

                        case "data_inizio":
                        case "data_fine":
                        //case (strpos('data',$nome_colonna)):
                            if($tabella == "lista_corsi_configurazioni" OR $tabella == "lista_corsi")
                                $rowTable .=  '<td style="text-align:center; vertical-align:middle;">' . print_input_date("txt_" . $record . "_" . $nome_colonna, GiraDataOra($column), $nome_colonna, $allReadonly, false) . '</td>';
                            else
                                $rowTable .=  '<td style="text-align:center; vertical-align:middle;">'.print_input("txt_" . $record . "_" . $nome_colonna, $column, $nome_colonna, $allReadonly, false).'</td>';
                        break;

                        case "elimina":
                            $rowTable .=  '<td style="text-align:center; vertical-align:middle;">' . $column . '</td>';
                        break;

                        default:
                            if (strpos(strtolower($column), "fa-")) {
                                $rowTable .=  '<td style="text-align:center; vertical-align:middle;">' . $column . '</td>';
                            } else {
                                $rowTable .=  '<td style="text-align:center; vertical-align:middle;">' . print_input("txt_" . $record . "_" . $nome_colonna, $column, $nome_colonna, $allReadonly, false) . '</td>';
                            }
                        break;
                    }
                break;
            }
            
            $c++;
        }
        
        $rowTable .= "</tr>\n";
        $id++;
        $record++;
    }
    
    echo '<tbody>' . $rowTable . '</tbody>';
    echo '</table></div></div></div>';
}

function creaListaAzioni($colonna_azione, $colore_primario) {

    $retStr = "<button class=\"btn btn-xs $colore_primario dropdown-toggle\" type=\"button\" data-toggle=\"dropdown\" aria-expanded=\"false\"> Azioni
           <i class=\"fa fa-angle-down\"></i>
       </button>
       <ul class=\"dropdown-menu\" role=\"menu\">";


    $line = explode("||", $colonna_azione);
    foreach ($line as $riga) {
        if (trim($riga) == "divider") {
            $retStr .= "<li class=\"$riga\"> </li>";
        } else {
            $link = "";
            $icona = "";
            $link = "";
            $valori = explode("|", $riga);
            foreach ($valori as $dato) {
                $tmp = explode(":", $dato);
                $key = trim($tmp[0]);
                $value = trim($tmp[1]);
                switch ($key) {
                    case "link":
                        $link = $value;
                        break;
                    case "icona":
                        $icona = $value;
                        break;
                    case "nome":
                        $nome = $value;
                        break;
                    default:
                        break;
                }
            }
            $retStr .= "<li><a href=\"$link\"><i class=\"$icona\"></i> $nome</a></li>";
        }
    }

    $retStr .= "</ul>";

    return $retStr;
}

function stampa_table_datatables_ajax($query, $ajaxTableId, $titolo = '', $stile = '', $colore_tabella = COLORE_PRIMARIO, $innerSearch = false ) {
    global $dblink;

    $colore_tabella = strlen($colore_tabella) > 0 ? $colore_tabella : COLORE_PRIMARIO;
    $stile = strlen($stile) > 0 ? $stile : "datatable_ajax";

    echo '<div class="portlet box ' . $colore_tabella . '">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-cogs"></i>' . $titolo . '</div>
            <div class="tools"></div>
        </div>
        <div class="portlet-body">
            <div class="table-container">
                <table class="table table-striped table-bordered table-hover dt-responsive" cellspacing="0" width="100%" id="'.$stile.'">';

    $rows = $dblink->get_results($query);
    $fields = $dblink->list_fields($query);
    $headTable = "";
    foreach ($fields as $field) {
        if (strpos($field->name, "fa-") !== false OR strpos($field->name, "Ass. Comm.") !== false) {
            $headTable .= "<th scope=\"col\" style=\"text-transform:uppercase; text-align:center;\"><i class=\"fa " . $field->name . " grey\"><!--<small> (".$field->orgname.")</small>--></i></th>\n";
        } else if (strtolower($field->name) == "selezione") {
            $headTable .= '<td scope=\"col\" style="text-align:center; vertical-align:middle;"><label class="mt-checkbox mt-checkbox-outline"><input name="txt_checkbox_all" id="txt_checkbox_all" type="checkbox"  value="'.count($rows).'"><span></span></label></td>';
        } else
            $headTable .= "<th scope=\"col\" style=\"text-transform:uppercase; text-align:center;\">" . pulisciCampoEtichetta($field->name) . "<!--<small> (".$field->orgname.")</small>--></th>\n";
    }
    echo "<thead> <tr role=\"row\" class=\"heading\"> $headTable </tr>";

    if($innerSearch){
        $headTableFilter = "";
        $colspan = 0;
        $firstCol = 0;
        foreach ($fields as $field) {
            if (strpos($field->name, "fa-") !== false OR strpos($field->name, "Ass. Comm.") !== false) {
                if($firstCol==0){
                    $headTableFilter .='<td style=\"text-align:center; vertical-align:middle;\"><button class="btn btn-sm green btn-outline filter-submit margin-bottom">
                        <i class="fa fa-search"></i></button></td>';
                    $firstCol++;
                }else $headTableFilter .='<td style=\"text-align:center; vertical-align:middle;\">&nbsp;</td>';
            } elseif (strtolower($field->name) == "stato" && !isset($_GET['whrStato'])) {
                $headTableFilter .='<td style=\"text-align:center; vertical-align:middle;\"><select name="order_stato" class="form-control form-filter input-sm">
                    <option value="">Select...</option>
                    <option value="Attivo">Attivo</option>
                    <option value="Non Attivo">Non Attivo</option>
                </select></td>';
            } elseif (strtolower($field->name) == "stato" && isset($_GET['whrStato'])) {
                $firstCol++;
                $idName = ($field->orgname!="" ? $field->orgname : $field->name);
                $headTableFilter .= "<td style=\"text-align:center; vertical-align:middle;\">". '<input type="text" class="form-control form-filter input-sm" name="order_'.strtolower($idName).'"></td>'."\n";
            } else {
                $firstCol++;
                $idName = ($field->orgname!="" ? $field->orgname : $field->name);
                $headTableFilter .= "<td style=\"text-align:center; vertical-align:middle;\">". '<input type="text" class="form-control form-filter input-sm" name="order_'.strtolower($idName).'"></td>'."\n";
            }
        }
        echo '<tr role="row" class="filter">
            '.$headTableFilter.'
        </tr>';
    }
    echo "</thead>";
    echo '<tbody></tbody>';
    echo '</table></div></div></div>';
}
?>
