<?php

/*
STAMPA FORM DA TABELLA CON DEFINIZIONE ARRAY
link:http://keenthemes.com/preview/metronic/theme/admin_1/form_controls.html
*/
function stampa_bootstrap_form_horizontal($tabella,$id,$titolo,$action="".BASE_URL."/libreria/salva.php"){
    global $dblink, $table_listaAziende, $table_listaProfessionisti, $table_listaProfessioni,
            $table_listaPassword, $table_calendario, $table_calendario,$table_listaTickets, $table_listaTicketsDettaglio, $table_listaPreventivi, $table_listaPreventiviDettaglio,
            $table_listaAlbiProfessionali, $table_listaRichiesteStati, $table_listaTicketStati, $table_listaCampagne, $table_listaProdotti,
            $table_listaCorsi, $table_listaCorsiDettaglio,
            $table_listaIscrizioni, $table_listaCommerciali, $table_calendarioEsami, $table_calendarioEsamiIscrizioni,
            $table_listaIscrizioniPartecipanti, $table_listaCosti, $table_listaFatture,$table_listaFattureDettaglio, $table_documentiAttestati,
            $table_listaProdottiTipologie, $table_listaProdottiCategorie, $table_listaProdottiGruppi, $table_listaTemplateEmail,
            $table_listaPasswordUtenti, $table_listaDocenti, $table_listaAule, $table_listaCorsiConfigurazioni,
            $table_listaProvvigioni;

   if($action==""){
       $action="".BASE_URL."/libreria/salva.php";
   }

   $stile = 'singola';
   $stile = 'doppia';
   //$stile = 'tripla';

    if(isset($id) && $id!=""){
        $where = "id='".$id."'";
        $arrayReturn['id']=$id;
        $arrayReturn['tbl']=$tabella;
    }else{
        $where = "1";
        $tabella="";
    }

    switch($tabella){
        case 'lista_provvigioni':
            $arrayReturn['tabella'] = "lista_provvigioni";
            $arrayReturn['tbl'] = "lista_provvigioni";
            $arrayReturn = get_campi_tabella($table_listaProvvigioni['modifica'], $arrayReturn);
            $arrayReturn['where'] = " `id` =".$id;
            $arrayReturn['titolo'] = 'Modifica Partner';
        break;
        
        case 'lista_corsi_configurazioni':
            $arrayReturn['tabella'] = "lista_corsi_configurazioni";
            $arrayReturn['tbl'] = "lista_corsi_configurazioni";
            $arrayReturn = get_campi_tabella($table_listaCorsiConfigurazioni['modifica'], $arrayReturn);
            $arrayReturn['where'] = " `id` =".$id;
            $arrayReturn['titolo'] = 'Modifica Configurazione';
        break;
        
        case 'lista_aule':
            $arrayReturn['tabella'] = "lista_aule";
            $arrayReturn['tbl'] = "lista_aule";
            $arrayReturn = get_campi_tabella($table_listaAule['modifica'], $arrayReturn);
            $arrayReturn['where'] = " `id` =".$id;
            $arrayReturn['titolo'] = 'Modifica Aula';
        break;
        
        case 'lista_docenti':
            $arrayReturn['tabella'] = "lista_docenti";
            $arrayReturn['tbl'] = "lista_docenti";
            $arrayReturn = get_campi_tabella($table_listaDocenti['modifica'], $arrayReturn);
            $arrayReturn['where'] = " `id` =".$id;
            $arrayReturn['titolo'] = 'Modifica Docente';
        break;
        
        case 'lista_password_utenti':
            $arrayReturn['tabella'] = "lista_password";
            $arrayReturn['tbl'] = "lista_password";
            $arrayReturn = get_campi_tabella($table_listaPasswordUtenti['modifica'], $arrayReturn);
            $arrayReturn['where'] = " `id` =".$id;
            $arrayReturn['titolo'] = 'Modifica Utenti Moodle';
        break;

        case 'lista_template_email':
            $arrayReturn['tabella'] = "lista_template_email";
            $arrayReturn['tbl'] = "lista_template_email";
            $arrayReturn = get_campi_tabella($table_listaTemplateEmail['modifica'], $arrayReturn);
            $arrayReturn['where'] = " `id` =".$id;
            $arrayReturn['titolo'] = 'Modifica Template Email';
        break;

        case 'lista_prodotti_gruppi':
            $arrayReturn['tabella'] = "lista_prodotti_gruppi";
            $arrayReturn['tbl'] = "lista_prodotti_gruppi";
            $arrayReturn = get_campi_tabella($table_listaProdottiGruppi['modifica'], $arrayReturn);
            $arrayReturn['where'] = " `id` =".$id;
            $arrayReturn['titolo'] = 'Modifica Gruppo Prodotto';
        break;

        case 'lista_prodotti_tipologie':
            $arrayReturn['tabella'] = "lista_prodotti_tipologie";
            $arrayReturn['tbl'] = "lista_prodotti_tipologie";
            $arrayReturn = get_campi_tabella($table_listaProdottiTipologie['modifica'], $arrayReturn);
            $arrayReturn['where'] = " `id` =".$id;
            $arrayReturn['titolo'] = 'Modifica Tipologia Prodotto';
        break;

        case 'lista_prodotti_categorie':
            $arrayReturn['tabella'] = "lista_prodotti_categorie";
            $arrayReturn['tbl'] = "lista_prodotti_categorie";
            $arrayReturn = get_campi_tabella($table_listaProdottiCategorie['modifica'], $arrayReturn);
            $arrayReturn['where'] = " `id` =".$id;
            $arrayReturn['titolo'] = 'Modifica Categoria Prodotto';
        break;

        case 'lista_attestati':
            $arrayReturn['tabella'] = "lista_attestati";
            $arrayReturn['tbl'] = "lista_attestati";
            $arrayReturn = get_campi_tabella($table_documentiAttestati['modifica'], $arrayReturn);
            $arrayReturn['where'] = " `id` =".$id;
            $arrayReturn['titolo'] = 'Modifica Attestato';
        break;

        case 'lista_fatture':
            $arrayReturn['tabella'] = "lista_fatture";
            $arrayReturn['tbl'] = "lista_fatture";
            $arrayReturn = get_campi_tabella($table_listaFatture['modifica'], $arrayReturn);
            $arrayReturn['where'] = " `id` =".$id;
            $arrayReturn['titolo'] = 'Modifica Fatture';
        break;
		
		case 'lista_fatture_dettaglio':
            $arrayReturn['tabella'] = "lista_fatture_dettaglio";
            $arrayReturn['tbl'] = "lista_fatture_dettaglio";
            $arrayReturn = get_campi_tabella($table_listaFattureDettaglio['modifica'], $arrayReturn);
            $arrayReturn['where'] = " `id` =".$id;
            $arrayReturn['titolo'] = 'Modifica Fattura Dettaglio';
        break;

        case 'lista_costi':
            $arrayReturn['tabella'] = "lista_costi";
            $arrayReturn['tbl'] = "lista_costi";
            $arrayReturn = get_campi_tabella($table_listaCosti['modifica'], $arrayReturn);
            $arrayReturn['where'] = " `id` =".$id;
            $arrayReturn['titolo'] = 'Modifica Entrate / Uscite';
        break;

         case 'calendario_iscrizioni':
            $arrayReturn['tabella'] = "calendario";
            $arrayReturn['tbl'] = "calendario";
            $arrayReturn = get_campi_tabella($table_calendarioEsamiIscrizioni['modifica'], $arrayReturn);
            $arrayReturn['where'] = " `id` =".$id;
            $arrayReturn['titolo'] = 'Modifica Iscrizione Esame';
        break;

        case 'calendario_esami':
            $arrayReturn['tabella'] = "calendario";
            $arrayReturn['tbl'] = "calendario";
            $arrayReturn = get_campi_tabella($table_calendarioEsami['modifica'], $arrayReturn);
            $arrayReturn['where'] = " `id` =".$id;
            $arrayReturn['titolo'] = 'Modifica Esame';
        break;

        case 'lista_iscrizioni':
            $arrayReturn['tabella'] = "lista_iscrizioni";
            $arrayReturn = get_campi_tabella($table_listaIscrizioni['modifica'], $arrayReturn);
            $arrayReturn['where'] = " `id` =".$id;
            $arrayReturn['titolo'] = 'Modifica Iscrizione';
        break;

        case 'lista_iscrizioni_partecipanti':
            $arrayReturn['tabella'] = "lista_iscrizioni";
            $arrayReturn['tbl'] = "lista_iscrizioni";
            $arrayReturn = get_campi_tabella($table_listaIscrizioniPartecipanti['modifica'], $arrayReturn);
            $arrayReturn['where'] = " `id` =".$id;
            $arrayReturn['titolo'] = 'Modifica Iscrizione';
        break;

         case 'lista_password_commerciali':
            $arrayReturn['tabella'] = "lista_password";
            $arrayReturn['tbl'] = "lista_password";
            $arrayReturn = get_campi_tabella($table_listaCommerciali['modifica'], $arrayReturn);
            $arrayReturn['where'] = " `id` =".$id;
            $arrayReturn['titolo'] = 'Modifica Commerciali';
        break;

        case 'lista_corsi':
            $arrayReturn['tabella'] = "lista_corsi";
            $arrayReturn = get_campi_tabella($table_listaCorsi['modifica'], $arrayReturn);
            $arrayReturn['where'] = " `id` =".$id;
            $arrayReturn['titolo'] = 'Modifica Corso';
        break;

        case 'lista_corsi_dettaglio':
            $arrayReturn['tabella'] = "lista_corsi_dettaglio";
            $arrayReturn = get_campi_tabella($table_listaCorsiDettaglio['modifica'], $arrayReturn);
            $arrayReturn['where'] = " `id` =".$id;
            $arrayReturn['titolo'] = 'Modifica Durata Corso';
        break;

         case 'lista_prodotti':
            $arrayReturn['tabella'] = "lista_prodotti";
            $arrayReturn = get_campi_tabella($table_listaProdotti['modifica'], $arrayReturn);
            $arrayReturn['where'] = " `id` =".$id;
            $arrayReturn['titolo'] = 'Modifica Prodotto';
        break;

        case 'lista_preventivi_dettaglio':
            $arrayReturn['tabella'] = "lista_preventivi_dettaglio";
            $arrayReturn = get_campi_tabella($table_listaPreventiviDettaglio['modifica'], $arrayReturn);
            $arrayReturn['where'] = " `id` =".$id;
            $arrayReturn['titolo'] = $titolo;
        break;

        case 'lista_preventivi':
            $arrayReturn['tabella'] = "lista_preventivi";
            $arrayReturn = get_campi_tabella($table_listaPreventivi['modifica'], $arrayReturn);
            $arrayReturn['where'] = " `id` =".$id;
            $arrayReturn['titolo'] = $titolo;
        break;

        case 'lista_aziende':
            $arrayReturn['tabella'] = "lista_aziende";
            $arrayReturn = get_campi_tabella($table_listaAziende['modifica'], $arrayReturn);
            $arrayReturn['where'] = " `id` =".$id;
            $arrayReturn['titolo'] = $titolo;
        break;

        case 'lista_professionisti':
            $arrayReturn['tabella'] = "lista_professionisti";
            $arrayReturn = get_campi_tabella($table_listaProfessionisti['modifica'], $arrayReturn);
            $arrayReturn['where'] = " `id` =".$id;
            $arrayReturn['titolo'] = "Modifica Professionista";
        break;

        case 'lista_professioni':
            $arrayReturn['tabella'] = "lista_professioni";
            $arrayReturn = get_campi_tabella($table_listaProfessioni['modifica'], $arrayReturn);
            $arrayReturn['where'] = " `id` =".$id;
            $arrayReturn['titolo'] = "Modifica Professione";
        break;

        case 'lista_albi_professionali':
            $arrayReturn['tabella'] = "lista_albi_professionali";
            $arrayReturn = get_campi_tabella($table_listaAlbiProfessionali['modifica'], $arrayReturn);
            $arrayReturn['where'] = " `id` =".$id;
            $arrayReturn['titolo'] = "Modifica Albo Professionale";
        break;

        case 'lista_password':
            $arrayReturn['tabella'] = "lista_password";
            $arrayReturn = get_campi_tabella($table_listaPassword['modifica'], $arrayReturn);
            $arrayReturn['where'] = " `id` =".$id;
            $arrayReturn['titolo'] = "Modifica Password";
        break;

        case 'calendario':
            $arrayReturn['tabella'] = "calendario";
            $arrayReturn = get_campi_tabella($table_calendario['modifica'], $arrayReturn);
            $arrayReturn['where'] = " `id` =".$id;
            $arrayReturn['titolo'] = "Modifica Richieste";
        break;

        case 'lista_ticket':
            $arrayReturn['tabella'] = "lista_ticket";
            $arrayReturn = get_campi_tabella($table_listaTickets['modifica'], $arrayReturn);
            $arrayReturn['where'] = " `id` =".$id;
            $arrayReturn['titolo'] = "Ticket";
        break;
        case 'lista_ticket_dettaglio':
            $arrayReturn['tabella'] = "lista_ticket_dettaglio";
            $arrayReturn = get_campi_tabella($table_listaTicketsDettaglio['modifica'], $arrayReturn);
            $arrayReturn['where'] = " `id` =".$id;
            $arrayReturn['titolo'] = "Ticket Dettaglio";
        break;

        case 'lista_richieste_stati':
            $arrayReturn['tabella'] = "lista_richieste_stati";
            $arrayReturn = get_campi_tabella($table_listaRichiesteStati['modifica'], $arrayReturn);
            $arrayReturn['where'] = " `id` =".$id;
            $arrayReturn['titolo'] = "Modifica Stati Richieste";
        break;

        case 'lista_ticket_stati':
            $arrayReturn['tabella'] = "lista_ticket_stati";
            $arrayReturn = get_campi_tabella($table_listaTicketStati['modifica'], $arrayReturn);
            $arrayReturn['where'] = " `id` =".$id;
            $arrayReturn['titolo'] = "Modifica Stati Ticket";
        break;

        case 'lista_campagne':
            $arrayReturn['tabella'] = "lista_campagne";
            $arrayReturn = get_campi_tabella($table_listaCampagne['modifica'], $arrayReturn);
            $arrayReturn['where'] = " `id` =".$id;
            $arrayReturn['titolo'] = "Modifica Campagna";
        break;

        default:
            $campi_visualizzati = "";
            $campi_etichette = "";
            $conta_colonne      = 	$dblink->list_fields("SELECT * FROM ".$tabella."");
            $arrayReturn['campi_tipo'] = "";
            $campi_non_editabili = "";

                foreach($conta_colonne as $colonna){
                    $nome_colonna = $colonna->name;
                    $campi_visualizzati.= "`".$nome_colonna."`, ";
                    $campi_etichette.= "".ucfirst(pulisciCampoEtichetta($nome_colonna)).",";

                    $pos = strpos(strtolower($nome_colonna), "id_");
                    $tipo = $colonna->type;

                    switch (strtolower($tipo)){
                        case "252":
                        case "blob":
                            $arrayReturn['campi_tipo'].="text,";
                            break;
                        case "3":
                        case "16":
                        case "1":
                        case "2":
                        case "9":
                        case "8":
                        case "4":
                        case "5":
                        case "246":
                        case "int":
                        case "real":
                            $arrayReturn['campi_tipo'].="numerico,";
                        break;
                        case "12":
                        case "datetime":
                            $arrayReturn['campi_tipo'].="dataora,";
                        break;
                        case "10":
                        case "date":
                            $arrayReturn['campi_tipo'].="data,";
                        break;
                        case "11":
                        case "time":
                            $arrayReturn['campi_tipo'].="ora,";
                        break;
                        case "254":
                        case "253":
                        case "string":
                        default:
                            $arrayReturn['campi_tipo'].="input,";
                        break;
                    }
                }

            $arrayReturn['tabella'] = $tabella;
            $campi_visualizzati = substr($campi_visualizzati, 0, -2);
            $campi_etichette = substr($campi_etichette, 0, -1);
            $arrayReturn['campi_visualizzati'] = $campi_visualizzati;
            $arrayReturn['campi_non_editabili'] = "id, dataagg, scrittore, codice".$campi_non_editabili;
            //print_r(explode(",",$campi_etichette));
            $arrayReturn['campi_etichette'] = explode(",",$campi_etichette);
            $arrayReturn['campi_etichette'] = $arrayReturn['campi_etichette'];

            //print_r($arrayReturn['campi_etichette']);
            //array_push($arrayReturn['campi_etichette'], $campi_etichette);
            //questo è quello sopra $arrayReturn['campi_etichette'] = array($campi_etichette);
            $arrayReturn['where'] = " `id` =".$id;
            $arrayReturn['titolo'] = $titolo;
        break;
    }


    $arrayReturn['referer'] = $_SERVER['HTTP_REFERER'];

    $arrayReturn['save'] = false;
    $arrayReturn['save'] = true;
    $arrayReturn['num_campi'] = $dblink->num_fields("SELECT ".$arrayReturn['campi_visualizzati']." FROM ".$arrayReturn['tabella']." LIMIT 1");
    $arrayReturn['sql'] = "SELECT ".$arrayReturn['campi_visualizzati']." FROM ".$arrayReturn['tabella']." WHERE ".$arrayReturn['where'];

    $row = $dblink->get_row($arrayReturn['sql']);
?>
        <!-- INIZIO FORM-->
        <!-- BEGIN SAMPLE FORM PORTLET-->
        <style>
            .form .form-bordered .form-group>div {
                padding: 10px ! important;
            }
            </style>
        <div class="portlet box <?= COLORE_PRIMARIO ?>">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-settings font-light"></i>
                    <span class="caption-subject font-light uppercase"><?=$arrayReturn['titolo']?></span>
                </div>
            </div>
            <div class="portlet-body form">
                <form class="form-horizontal form-bordered" enctype="multipart/form-data" role="form" action="<?=$action?>" method="POST">
                    <input type="HIDDEN" id="txt_tbl" name="txt_tbl" value="<?=$arrayReturn['tbl']?>">
                    <input type="HIDDEN" id="txt_id" name="txt_id" value="<?=$arrayReturn['id']?>">
                    <input type="HIDDEN" id="txt_where" name="txt_where" value="<?=$arrayReturn['where']?>">
                    <input type="HIDDEN" id="txt_tabella" name="txt_tabella" value="<?=$arrayReturn['tabella']?>">
                    <input type="HIDDEN" id="txt_referer" name="txt_referer" value="<?=$arrayReturn['referer']?>">
                    <input type="HIDDEN" id="txt_num_campi" name="txt_num_campi" value="<?=$arrayReturn['num_campi']?>">
                    <div class="form-body">
    <?php
    //inputTendina($label, $idInput, $nameInput, $sql);
    $arrayCampi = explode(",", $arrayReturn['campi_visualizzati']);
    foreach ($arrayCampi as $key => $value) {
        $arrayCampi[$key] = trim(str_replace("`", "", $value));
    }
    $arrayCampiNonEditabili = explode(",", $arrayReturn['campi_non_editabili']);

    foreach ($arrayCampiNonEditabili as $key => $value) {
        $arrayCampiNonEditabili[$key] = trim(str_replace("`", "", $value));
    }

    $arrayTipoCampi = explode(",", $arrayReturn['campi_tipo']);

    if($stile=="singola"){
        $colNum = 3;
        $inputColNum = 9;
    }else if($stile=="doppia"){
        $colNum = 2;
        $inputColNum = 4;
    }else{
        $colNum = 1;
        $inputColNum = 3;
    }

    $count = 1;
    echo "<div class=\"form-group\">";
    foreach ($arrayCampi as $key => $campo) {

        if(trim($arrayTipoCampi[$key])=="hidden"){
            print_hidden($campo,$row[$key]);
            continue;
        }


        if(($stile=="tripla" && $count==4) || ($stile=="doppia" && $count==3) || ($stile=="singola" && $count==2)){
            echo "</div><div class=\"form-group\">";
            $count = 1;
        }
        //cro-> aggiunto controllo tipo campi vuoto
        if(strlen(trim($arrayTipoCampi[$key]))<=0){
            $arrayTipoCampi[$key] = 'input';
        }
        switch (trim($arrayTipoCampi[$key])) {
            default:
            case "input":
                echo "<label class=\"col-md-$colNum control-label\">".$arrayReturn['campi_etichette'][$key]."</label>
                        <div class=\"col-md-$inputColNum\">";
                        print_input($campo,$row[$key],$arrayReturn['campi_etichette'][$key],in_array($campo, $arrayCampiNonEditabili));
                    echo "</div>";
            break;

            case "hidden":
                echo "<label class=\"col-md-$colNum control-label\">".$arrayReturn['campi_etichette'][$key]."</label>
                        <div class=\"col-md-$inputColNum\">";
                        print_hidden($campo,$row[$key],$arrayReturn['campi_etichette'][$key],in_array($campo, $arrayCampiNonEditabili));
                    echo "</div>";
            break;

            case "dataora":
                echo "<label class=\"col-md-$colNum control-label\">".$arrayReturn['campi_etichette'][$key]."</label>
                        <div class=\"col-md-$inputColNum\">";
                        print_input_data_ora($campo, GiraDataOra($row[$key]),$arrayReturn['campi_etichette'][$key],in_array($campo, $arrayCampiNonEditabili));
                    echo "</div>";
            break;

            case "data":
                echo "<label class=\"col-md-$colNum control-label\">".$arrayReturn['campi_etichette'][$key]."</label>
                        <div class=\"col-md-$inputColNum\">";
                        print_input_date($campo, GiraDataOra($row[$key]),$arrayReturn['campi_etichette'][$key],in_array($campo, $arrayCampiNonEditabili));
                    echo "</div>";
            break;

            case "ora":
                echo "<label class=\"col-md-$colNum control-label\">".$arrayReturn['campi_etichette'][$key]."</label>
                        <div class=\"col-md-$inputColNum\">";
                        print_input_ora($campo, $row[$key],$arrayReturn['campi_etichette'][$key],in_array($campo, $arrayCampiNonEditabili));
                    echo "</div>";
            break;

            case "text":
                echo "<label class=\"col-md-$colNum control-label\">".$arrayReturn['campi_etichette'][$key]."</label>
                        <div class=\"col-md-$inputColNum\">
                            <textarea id=\"$campo\" name=\"$campo\" class=\"form-control input-sm\" rows=\"3\" ".(in_array($campo, $arrayCampiNonEditabili) ? "readonly" : "").">$row[$key]</textarea>
                        </div>";
            break;

            case "htmlarea":
                echo "<label class=\"col-md-$colNum control-label\">".$arrayReturn['campi_etichette'][$key]."</label>
                        <div class=\"col-md-$inputColNum\">
                            <textarea id=\"$campo\" name=\"$campo\" class=\"form-control wysihtml5 input-sm\" rows=\"10\" ".(in_array($campo, $arrayCampiNonEditabili) ? "readonly" : "").">$row[$key]</textarea>
                        </div>";
            break;

            case "password":
                echo "<label class=\"col-md-$colNum control-label\">".$arrayReturn['campi_etichette'][$key]."</label>
                        <div class=\"col-md-$inputColNum\">
                            <div class=\"input-group input-group-sm\">
                                <input id=\"$campo\" name=\"$campo\" type=\"password\" value=\"$row[$key]\" class=\"form-control input-sm\" placeholder=\"".$arrayReturn['campi_etichette'][$key]."\" ".(in_array($campo, $arrayCampiNonEditabili) ? "readonly" : "").">
                                    <!--TODO creare variabili tema campo_stile_icona_background per sostituire (background-color: #fff;) e campo_colore_icona per sostituire (font-grey-mint)-->
                                    <span class=\"input-group-addon\" style=\"background-color: #fff;\">
                                    <i class=\"fa fa-user font-grey-mint\"></i>
                                </span>
                            </div>
                        </div>";
            break;

            case "indirizzo":
                echo "<label class=\"col-md-$colNum control-label\">".$arrayReturn['campi_etichette'][$key]."</label>
                        <div class=\"col-md-$inputColNum\">
                            <div class=\"input-group input-group-sm\">
                                <span class=\"input-group-addon\" style=\"background-color: #fff;\">
                                    <i class=\"fa fa-map-marker font-grey-mint\"></i>
                                </span>
                                <input id=\"$campo\" name=\"$campo\" type=\"text\" value=\"$row[$key]\" class=\"form-control input-sm\" placeholder=\"".$arrayReturn['campi_etichette'][$key]."\" ".(in_array($campo, $arrayCampiNonEditabili) ? "readonly" : "").">
                            </div>
                        </div>";
            break;

            case "telefono":
                echo "<label class=\"col-md-$colNum control-label\">".$arrayReturn['campi_etichette'][$key]."</label>
                        <div class=\"col-md-$inputColNum\">
                            <div class=\"input-group input-group-sm\">
                                <span class=\"input-group-addon\" style=\"background-color: #fff;\">
                                    <i class=\"fa fa-phone-square font-grey-mint\"></i>
                                </span>
                                <input id=\"$campo\" name=\"$campo\" type=\"tel\" value=\"$row[$key]\" class=\"form-control input-sm\" placeholder=\"".$arrayReturn['campi_etichette'][$key]."\" ".(in_array($campo, $arrayCampiNonEditabili) ? "readonly" : "").">
                                <span class=\"input-group-btn\"><a href=\"tel:$row[$key]\" class=\"btn grey-mint\" target=\"_blank\"> Chiama <i class=\"fa fa-external-link\"></i></a></span>
                            </div>
                        </div>";
            break;

            case "cellulare":
                echo "<label class=\"col-md-$colNum control-label\">".$arrayReturn['campi_etichette'][$key]."</label>
                        <div class=\"col-md-$inputColNum\">
                            <div class=\"input-group input-group-sm\">
                                <span class=\"input-group-addon\" style=\"background-color: #fff;\">
                                    <i class=\"fa fa-mobile font-grey-mint\"></i>
                                </span>
                                <input id=\"$campo\" name=\"$campo\" type=\"tel\" value=\"$row[$key]\" class=\"form-control input-sm\" placeholder=\"".$arrayReturn['campi_etichette'][$key]."\" ".(in_array($campo, $arrayCampiNonEditabili) ? "readonly" : "").">
                                <span class=\"input-group-btn\"><a href=\"tel:$row[$key]\" class=\"btn grey-mint\" target=\"_blank\"> Chiama <i class=\"fa fa-external-link\"></i></a></span>
                            </div>
                        </div>";
            break;

            case "fax":
                echo "<label class=\"col-md-$colNum control-label\">".$arrayReturn['campi_etichette'][$key]."</label>
                        <div class=\"col-md-$inputColNum\">
                            <div class=\"input-group input-group-sm\">
                                <span class=\"input-group-addon\" style=\"background-color: #fff;\">
                                    <i class=\"fa fa-fax font-grey-mint\"></i>
                                </span>
                                <input id=\"$campo\" name=\"$campo\" type=\"text\" value=\"$row[$key]\" class=\"form-control input-sm\" placeholder=\"".$arrayReturn['campi_etichette'][$key]."\" ".(in_array($campo, $arrayCampiNonEditabili) ? "readonly" : "")."> </div>
                        </div>";
            break;

            case "email":
                echo "<label class=\"col-md-$colNum control-label\">".$arrayReturn['campi_etichette'][$key]."</label>
                        <div class=\"col-md-$inputColNum\">
                            <div class=\"input-group input-group-sm\">
                                <span class=\"input-group-addon\" style=\"background-color: #fff;\">
                                    <i class=\"fa fa-envelope font-grey-mint\"></i>
                                </span>
                                <input id=\"$campo\" name=\"$campo\" type=\"email\" value=\"$row[$key]\" class=\"form-control input-sm\" placeholder=\"".$arrayReturn['campi_etichette'][$key]."\" ".(in_array($campo, $arrayCampiNonEditabili) ? "readonly" : "").">
                                <span class=\"input-group-btn\"><a href=\"mailto:$row[$key]\" class=\"btn grey-mint\" target=\"_blank\"> Invia <i class=\"fa fa-external-link\"></i></a></span>
                            </div>
                        </div>";
            break;

            case "web":
                echo "<label class=\"col-md-$colNum control-label\">".$arrayReturn['campi_etichette'][$key]."</label>
                        <div class=\"col-md-$inputColNum\">
                            <div class=\"input-group input-group-sm\">
                                <span class=\"input-group-addon\" style=\"background-color: #fff;\">
                                    <i class=\"fa fa-globe font-grey-mint\"></i>
                                </span>
                                <input id=\"$campo\" name=\"$campo\" type=\"url\" value=\"$row[$key]\" class=\"form-control input-sm\" placeholder=\"".$arrayReturn['campi_etichette'][$key]."\" ".(in_array($campo, $arrayCampiNonEditabili) ? "readonly" : "").">
                                <span class=\"input-group-btn\"><a href=\"$row[$key]\" class=\"btn grey-mint\" target=\"_blank\"> Vedi <i class=\"fa fa-external-link\"></i></a></span>
                            </div>
                        </div>";
            break;

            case "radio":
                echo "<label class=\"col-md-$colNum control-label\">".$arrayReturn['campi_etichette'][$key]."</label>
                        <div class=\"col-md-$inputColNum\"><div class=\"mt-radio-inline\">";
                    foreach ($arrayReturn['campi_radio'][$campo] as $value) {
                        echo "<label class=\"mt-radio\">
                                <input type=\"radio\" name=\"$campo.$value\" id=\"$campo.$value\" value=\"$value\" ".(($value==$row[$key]) ? "checked=\"\"" : "")." ".(in_array($campo, $arrayCampiNonEditabili) ? "disabled=\"\"" : "")."> $value
                                <span></span>
                            </label>";
                    }
                echo "</div>";
            break;

            case "select_static":
                if(in_array($campo, $arrayCampiNonEditabili)){
                    echo "<label class=\"col-md-$colNum control-label\">".$arrayReturn['campi_etichette'][$key]."</label>
                            <div class=\"col-md-$inputColNum\">";
                                print_input($campo,$row[$key],$arrayReturn['campi_etichette'][$key],true);
                            echo "</div>";
                }else{
                    echo '<label class="col-md-'.$colNum.' control-label">'.$arrayReturn['campi_etichette'][$key].'</label>
                        <div class="col-md-'.$inputColNum.'">';
                        print_select_static($arrayReturn['campi_select'][$campo],$campo,$row[$key]);
                        echo '</div>';
                }
            break;

            case "select2":
                if(in_array($campo, $arrayCampiNonEditabili)){
                    echo "<label class=\"col-md-$colNum control-label\">".$arrayReturn['campi_etichette'][$key]."</label>
                            <div class=\"col-md-$inputColNum\">";
                                print_input($campo,$row[$key],$arrayReturn['campi_etichette'][$key],true);
                        echo "</div>";
                }else{
                    echo '<label class="col-md-'.$colNum.' control-label">'.$arrayReturn['campi_etichette'][$key].'</label>
                        <div class="col-md-'.$inputColNum.'">';
                        if(!empty($arrayReturn['ajax'])){
                            print_select2($arrayReturn['campi_select'][$campo],$campo,$row[$key],$arrayReturn['ajax'][$campo]);
                        }else{
                            print_select2($arrayReturn['campi_select'][$campo],$campo,$row[$key]);
                        }
                        echo '</div>';
                }
            break;
            
            case "select-cancella":
                if(in_array($campo, $arrayCampiNonEditabili)){
                    echo "<label class=\"col-md-$colNum control-label\">".$arrayReturn['campi_etichette'][$key]."</label>
                            <div class=\"col-md-$inputColNum\">";
                                print_input($campo,$row[$key],$arrayReturn['campi_etichette'][$key],true);
                        echo "</div>";
                }else{
                    echo '<label class="col-md-'.$colNum.' control-label">'.$arrayReturn['campi_etichette'][$key].'</label>
                        <div class="col-md-'.$inputColNum.'">';
                        if(!empty($arrayReturn['ajax'])){
                            print_select2($arrayReturn['campi_select'][$campo],$campo,$row[$key],$arrayReturn['ajax'][$campo],true,"select2-allow-clear");
                        }else{
                            print_select2($arrayReturn['campi_select'][$campo],$campo,$row[$key], "",true,"select2-allow-clear");
                        }
                        echo '</div>';
                }
            break;

            case "bs-select":
                if(in_array($campo, $arrayCampiNonEditabili)){
                    echo "<label class=\"col-md-$colNum control-label\">".$arrayReturn['campi_etichette'][$key]."</label>
                            <div class=\"col-md-$inputColNum\">";
                                print_input($campo,$row[$key],$arrayReturn['campi_etichette'][$key],true);
                            echo "</div>";
                }else{
                    echo '<label class="col-md-'.$colNum.' control-label">'.$arrayReturn['campi_etichette'][$key].'</label>
                        <div class="col-md-'.$inputColNum.'">';
                        print_bs_select($arrayReturn['campi_select'][$campo],$campo,$row[$key]);
                        echo'</div>';
                }
            break;
            
            case "file":
                echo "<label class=\"col-md-$colNum control-label\">".$arrayReturn['campi_etichette'][$key]."</label>
                        <div class=\"col-md-$inputColNum\">";
                        print_hidden($campo."_directoryFile",$arrayReturn['campi_file'][$campo],true);
                        print_input_file($campo,$row[$key],$arrayReturn['campi_etichette'][$key],in_array($campo, $arrayCampiNonEditabili));
                echo "</div>";
            break;
        }
        $count++;
    }

    echo "</div>";
    ?>
    <?php if($arrayReturn['save']){ ?>
                    <div class="form-actions right">
                        <div class="row">
                            <div class="col-md-offset-3 col-md-9">
                                <button type="submit" class="btn btn-circle btn-lg green-jungle"><i class="fa fa-check"></i> Salva</button>
                            </div>
                        </div>
                    </div>
    <?php } ?>
                </form>
            </div>
        </div>
        <!-- END SAMPLE FORM PORTLET-->
        <!-- FINE FORM-->
<?php
    //ECHO '<H1>Fine Stampa_HTML_Form_Campi_Modificabili</H1>';
    return;
}

function print_input($nomeInput,$valoreSelezionato,$placeHolder="",$readonly=false,$echo = true){
    $ret = "<input type=\"text\" id=\"$nomeInput\" name=\"$nomeInput\" value=\"$valoreSelezionato\" class=\"form-control input-sm\" placeholder=\"".$placeHolder."\" ".($readonly ? "readonly" : "").">";
    if($echo)  echo $ret;
    else return $ret;
}

function print_input_file($nomeInput,$valoreSelezionato,$placeHolder="",$readonly=false,$echo = true){
    $ret = "<input type=\"file\" id=\"$nomeInput\" name=\"$nomeInput\" value=\"$valoreSelezionato\" class=\"form-control input-sm\" placeholder=\"".$placeHolder."\" ".($readonly ? "readonly" : "").">";
    if($echo)  echo $ret;
    else return $ret;
}

function print_hidden($nomeInput,$valoreSelezionato,$echo=true){
    $ret = "<input type=\"hidden\" id=\"$nomeInput\" name=\"$nomeInput\" value=\"$valoreSelezionato\">";
    if($echo)  echo $ret;
    else return $ret;
}

function print_input_data_ora($nomeInput,$valoreSelezionato,$placeHolder="",$readonly=false,$echo = true){
    $ret = "<input type=\"text\" id=\"$nomeInput\" name=\"$nomeInput\" value=\"".($valoreSelezionato=="00-00-0000" OR $valoreSelezionato=="--" ? "" : $valoreSelezionato)."\" class=\"form-control input-sm ".($readonly ? "" : "datetime-picker")."\" placeholder=\"".$placeHolder."\" ".($readonly ? "readonly" : "").">";
    if($echo)  echo $ret;
    else return $ret;
}

function print_input_date($nomeInput,$valoreSelezionato,$placeHolder="",$readonly=false,$echo = true,$dateFormat = "dd-mm-yyyy"){
    $ret = "<input type=\"text\" id=\"$nomeInput\" name=\"$nomeInput\" value=\"".$valoreSelezionato."\" class=\"form-control input-sm ".($readonly ? "" : "date-picker")."\" placeholder=\"".$placeHolder."\" data-date-format=\"$dateFormat\" ".($readonly ? "readonly" : "").">";
    if($echo)  echo $ret;
    else return $ret;
}

function print_input_date_between($nomeInput,$valoreSelezionato,$placeHolder="",$readonly=false,$echo = true){
    $ret = "<input type=\"text\" id=\"$nomeInput\" name=\"$nomeInput\" value=\"".$valoreSelezionato."\" class=\"form-control input-sm ".($readonly ? "" : "date-picker-range")."\" placeholder=\"".$placeHolder."\" ".($readonly ? "readonly" : "").">";
    if($echo)  echo $ret;
    else return $ret;
}

function print_input_ora($nomeInput,$valoreSelezionato,$placeHolder="",$readonly=false,$echo = true){
    $ret = "<input type=\"text\" id=\"$nomeInput\" name=\"$nomeInput\" value=\"".$valoreSelezionato."\" class=\"form-control input-sm ".($readonly ? "" : "timepicker timepicker-24-ora-inizio")."\" placeholder=\"".$placeHolder."\" ".($readonly ? "readonly" : "").">";
    if($echo)  echo $ret;
    else return $ret;
}

function print_select_static($arrayValore,$nomeSelect,$valoreSelezionato="",$ajaxFunction = "", $echo = true, $classi="select2", $extra_data="", $nomeSelezionare = "Selezionare..."){
    global $dblink;
    $select= '<select class="form-control input-sm '.$classi.'" '.$extra_data.' id="'.$nomeSelect.'" name="'.$nomeSelect.'" '.($ajaxFunction!="" ? "onchange=\"".$ajaxFunction."(this);\"" : "").'>';
    $i = 0;
    if($valoreSelezionato==="") $select.= '<option  id="'.$nomeSelect.$i.'" name="'.$nomeSelect.$i.'" value="">'.$nomeSelezionare.'</option>';
    foreach ($arrayValore as $valore => $nome) {
        $colore = "";
        if(strpos($nome,"|")!==false){
            $tmp = explode("|", $nome);
            $nome = $tmp[0];
            $colore = $tmp[1];
        }
        $i++;
        $varAjax = "";
        if($ajaxFunction!=""){
            while(++$a){
                if(isset($arrayValore['var_'.$a])){
                    $varAjax.="".$arrayValore['var_'.$a].":";
                }else{ break; }
            }
        }
        $varAjax = substr($varAjax, 0, -1);
        if(strlen($nome)>40){
            $nomeOption = substr($nome,0,40)."...";
        }else{
            $nomeOption = $nome;
        }
        if(strlen($colore)>0) $colore = 'data-content="<span class=\'label bg-'.$tmp[1].' bg-font-'.$tmp[1].' bold\'> '.$nomeOption.' </span>"';
        $select.= '<option '.($ajaxFunction!="" ? "data-options=\"".$varAjax."\"" : "").' id="'.$nomeSelect.$i.'" name="'.$nomeSelect.$i.'" value="'.$valore.'" '.(($valore==$valoreSelezionato) ? "selected" : "").' '.$colore.'>'.$nomeOption.'</option>';
    }
    $select.= '</select>';

    if($echo){
        echo $select;
    }else{
        return $select;
    }
}

function print_bs_select($sql,$nomeSelect,$valoreSelezionato="",$ajaxFunction = "", $echo = true, $classi="bs-select", $extra_data=""){
    global $dblink;
    $select= '<select class="form-control '.$classi.'" '.$extra_data.' data-show-subtext="true" id="'.$nomeSelect.'" name="'.$nomeSelect.'" '.($ajaxFunction!="" ? "onchange=\"".$ajaxFunction."(this);\"" : "").'>';
    $res = $dblink->get_results($sql);
    $i = 0;
    if($valoreSelezionato==="") $select.= '<option  id="'.$nomeSelect.$i.'" name="'.$nomeSelect.$i.'" value="">Selezionare...</option>';
    else{
        if(!in_array_r($valoreSelezionato, $res)) $select.= '<option  id="'.$nomeSelect.$i.'" name="'.$nomeSelect.$i.'" value="'.$valoreSelezionato.'">'.$valoreSelezionato.'</option>';
    }
    foreach ($res as $row2) {
        $i++;
        $a=0;
        $varAjax = "";
        if($ajaxFunction!=""){
            while(++$a){
                if(isset($row2['var_'.$a])){
                    $varAjax.="".$row2['var_'.$a].":";
                }else{ break; }
            }
        }
        $varAjax = substr($varAjax, 0, -1);
        if(strlen($row2['nome'])>40){
            $nomeOption = substr($row2['nome'],0,40)."...";
        }else{
            $nomeOption = $row2['nome'];
        }
        $select.= '<option '.($ajaxFunction!="" ? "data-options=\"".$varAjax."\"" : "").' id="'.$nomeSelect.$i.'" name="'.$nomeSelect.$i.'" value="'.$row2['valore'].'" '.(($row2['valore']==$valoreSelezionato) ? "selected" : "").' data-content="<span class=\'label bg-'.$row2['colore'].' bg-font-'.$row2['colore'].' bold\'> '.$nomeOption.' </span>" >'.$nomeOption.'</option>';
    }
    $select.= '</select>';

    if($echo){
        echo $select;
    }else{
        return $select;
    }
}

function print_select2($sql,$nomeSelect,$valoreSelezionato="",$ajaxFunction = "", $echo = true, $classi="select2", $extra_data=""){
    global $dblink;
    $select= '<select class="form-control input-sm '.$classi.'" '.$extra_data.' id="'.$nomeSelect.'" name="'.$nomeSelect.'" '.($ajaxFunction!="" ? "onchange=\"".$ajaxFunction."(this);\"" : "").'>';
    $res = $dblink->get_results($sql);
    $i = 0;
    //if($valoreSelezionato==="") {
    $select.= '<option  id="'.$nomeSelect.$i.'" name="'.$nomeSelect.$i.'" value="">Selezionare...</option>';
    //}else{
    if(!in_array_r($valoreSelezionato, $res)){
        $select.= '<option  id="'.$nomeSelect.$i.'" name="'.$nomeSelect.$i.'" value="'.$valoreSelezionato.'">'.$valoreSelezionato.'</option>';
    }
    //}
    foreach ($res as $row2) {
        $i++;
        $a=0;
        $varAjax = "";
        if($ajaxFunction!=""){
            while(++$a){
                if(isset($row2['var_'.$a])){
                    $varAjax.="".$row2['var_'.$a].":";
                }else{ break; }
            }
        }
        $varAjax = substr($varAjax, 0, -1);
        if(strlen($row2['nome'])>40 && strpos($_SERVER['REQUEST_URI'], "anagrafiche/dettaglio_tab.php")>1){
            $nomeOption = substr($row2['nome'],0,40)."...";
        }else{
            $nomeOption = $row2['nome'];
        }
        $select.= '<option '.($ajaxFunction!="" ? "data-options=\"".$varAjax."\"" : "").' id="'.$nomeSelect.$i.'" name="'.$nomeSelect.$i.'" value="'.$row2['valore'].'" '.(($row2['valore']==$valoreSelezionato) ? "selected" : "").' title="'.$row2['nome'].'">'.$nomeOption.'</option>';
    }

    $select.= '</select>';

    if($echo){
        echo $select;
    }else{
        return $select;
    }

}

function print_multi_select($sql,$nomeSelect,$valoreSelezionato="",$ajaxFunction = "", $echo = true, $classi="mt-multiselect", $extra_data=""){
    global $dblink;
    $select= '<select class="form-control '.$classi.'" multiple="multiple" data-label="left" data-select-all="true" data-width="100%"  data-height="300" data-filter="true" data-action-dropdownhide="true" '.$extra_data.' id="'.$nomeSelect.'" name="'.$nomeSelect.'" '.($ajaxFunction!="" ? "onchange=\"".$ajaxFunction."(this);\"" : "").'>';
    $res = $dblink->get_results($sql);
    $i = 0;
    //if($valoreSelezionato==="") {
    //$select.= '<option  id="'.$nomeSelect.$i.'" name="'.$nomeSelect.$i.'" value="">Selezionare...</option>';
    //}else{
    /*if(!in_array_r($valoreSelezionato, $res)){
        $select.= '<option  id="'.$nomeSelect.$i.'" name="'.$nomeSelect.$i.'" value="'.$valoreSelezionato.'">'.$valoreSelezionato.'</option>';
    }*/
    //}
    foreach ($res as $row2) {
        $i++;
        $a=0;
        $varAjax = "";
        if($ajaxFunction!=""){
            while(++$a){
                if(isset($row2['var_'.$a])){
                    $varAjax.="".$row2['var_'.$a].":";
                }else{ break; }
            }
        }
        $varAjax = substr($varAjax, 0, -1);
        if(strlen($row2['nome'])>40 && strpos($_SERVER['REQUEST_URI'], "anagrafiche/dettaglio_tab.php")>1){
            $nomeOption = substr($row2['nome'],0,40)."...";
        }else{
            $nomeOption = $row2['nome'];
        }
        $select.= '<option '.($ajaxFunction!="" ? "data-options=\"".$varAjax."\"" : "").' id="'.$nomeSelect.$i.'" name="'.$nomeSelect.$i.'" value="'.$row2['valore'].'" '.(in_array($row2['valore'], $valoreSelezionato) ? "selected=\"selected\"" : "").' title="'.$row2['nome'].'">'.$nomeOption.'</option>';
    }

    $select.= '</select>';

    if($echo){
        echo $select;
    }else{
        return $select;
    }

}
        
function get_campi_tabella($dati, $ret = array()){

    $ret['campi_visualizzati'] = "";
    $ret['campi_non_editabili'] = "";
    $ret['campi_tipo'] = "";
    $ret['campi_etichette'] = array();

    foreach ($dati as $value) {
       foreach ($value as $key => $val) {
           switch ($key) {
                case "campo":
                    $ret['campi_visualizzati'].= "`$val`,";
                break;

                case "tipo":
                    $ret['campi_tipo'].= "$val,";
                break;

                case "etichetta":
                    array_push($ret['campi_etichette'], $val);
                break;

                case "readonly":
                    if($val==true){ $ret['campi_non_editabili'].= $value['campo'].","; }
                break;

                case "like":
                    if($val){
                        $ret['slk'][$value['campo']]['LIKE'] = "LIKE";
                        $ret['slk'][$value['campo']]['% LIKE'] = "% LIKE";
                        $ret['slk'][$value['campo']]['LIKE %'] = "LIKE %";
                        $ret['slk'][$value['campo']]['% LIKE %'] = "% LIKE %";
                        if(empty($ret['slk_default'][$value['campo']])) $ret['slk_default'][$value['campo']] = "% LIKE %";
                    }
                break;

                case "uguale":
                    if($val){
                        $ret['slk'][$value['campo']]['='] = "=";
                        $ret['slk'][$value['campo']]['!='] = "!=";
                        if(empty($ret['slk_default'][$value['campo']])) $ret['slk_default'][$value['campo']] = "=";
                    }
                break;

                case "maggiore":
                    if($val){
                        $ret['slk'][$value['campo']]['>='] = ">=";
                        $ret['slk'][$value['campo']]['>'] = ">";
                        $ret['slk'][$value['campo']]['<='] = "<=";
                        $ret['slk'][$value['campo']]['<'] = "<";
                        if(empty($ret['slk_default'][$value['campo']]=="")) $ret['slk_default'][$value['campo']] = ">=";
                    }
                break;

                case "attivo":
                    $ret['chk'][$value['campo']] = $val;
                break;

                case "default":
                    $ret['default'][$value['campo']] = $val;
                break;

                case "sql":
                    $ret['campi_select'][$value['campo']] = $val;
                break;
            
                case "dir":
                    $ret['campi_file'][$value['campo']] = $val;
                break;

                case "ajax":
                    $ret['ajax'][$value['campo']] = $val;
                break;

                default:
                break;
           }
       }
    }

    $ret['campi_visualizzati'] = substr($ret['campi_visualizzati'], 0, -1);
    $ret['campi_tipo'] = substr($ret['campi_tipo'], 0, -1);
    $ret['campi_non_editabili'] = substr($ret['campi_non_editabili'], 0, -1);

    return $ret;
 }


 /*
 STAMPA FORM CON TAB - DA FARE DINAMICA
 link: dettaglio_tab.php
 */
 function stampa_form_tab_custom(){
?>
   <!-- BEGIN PROFILE CONTENT -->
   <form class="form" enctype="multipart/form-data" role="form" action="<?=BASE_URL?>/libreria/salva.php" method="POST">
   <div class="profile-content">
       <div class="row">
           <div class="col-md-12">
               <div class="portlet light bg-inverse">
                   <div class="portlet-title tabbable-line">
                       <div class="caption caption-lg">
                           <i class="icon-globe theme-font hide"></i>
                           <span class="caption-subject font-blue-madison bold uppercase"><?php echo $wContatti_azienda;?></span>
                           <span class="caption-helper"></span>
                       </div>
                       <ul class="nav nav-tabs">
                           <li class="active">
                               <a href="#tab_1_1" data-toggle="tab">Dati Nominativo</a>
                           </li>
                           <li>
                               <a href="#tab_1_2" data-toggle="tab">Mappa</a>
                           </li>
                           <li class="dropdown">
                               <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown"> Azioni
                                   <i class="fa fa-angle-down"></i>
                               </a>
                               <ul class="dropdown-menu pull-right" role="menu">
                                   <li>
                                       <a href="#tab_invia_email_presentazione" tabindex="-1" data-toggle="tab"><i class="fa fa-envelope font-grey-mint"></i> Invia Email </a>
                                   </li>
                                   <!--<li>
                                       <a href="#tab_invia_documentazione" tabindex="-1" data-toggle="tab"><i class="fa fa-file-pdf-o font-grey-mint"></i> Invia Documentazione </a>
                                   </li>-->
                               </ul>
                           </li>
                       </ul>
                   </div>
                   <div class="portlet-body form">
                       <div class="tab-content">
                           <!-- PERSONAL INFO TAB -->
                           <div class="tab-pane active" id="tab_1_1">
                             <!-- <form role="form" enctype="multipart/form-data" id="formContatti" name="formProcesso" action="wContatti.php" method="POST"> -->
                             <div class="row" style="margin-bottom:10px;">
                                     <div class="col-md-5">
                                       <label class="col-md-4 control-label">Last Call: </label>
                                       <div class="col-md-8">
                                           <span class="label label-success"> <?php echo $wContatti_dataagg; ?> </span>
                                       </div>
                                     </div>
                                     <div class="col-md-4">
                                         <label class="col-md-3 control-label">Agente: </label>
                                         <div class="col-md-9">
                                             <span class="label label-info"> <?php echo $wContatti_cognome_nome_agente;?> </span>
                                         </div>
                                     </div>
                                   <div class="col-md-3">
                                         <label class="col-md-3 control-label">Stato: </label>
                                         <div class="col-md-9">
                                             <span class="label label-default"> <?php echo $wContatti_stato;?> </span>
                                         </div>
                                     </div>


                             </div>
                               <div class="row" style="margin-bottom:10px;">
                                   <div class="col-md-6">
                                       <input name="wContatti_azienda" id="wContatti_azienda" type="text" class="form-control tooltips" placeholder="Azienda" value="<?php echo $wContatti_azienda;?>" data-container="body" data-placement="top" data-original-title="AZIENDA"> </div>
                                   <div class="col-md-6">
                                       <input name="wContatti_partita_iva" id="wContatti_partita_iva" type="text" class="form-control tooltips" placeholder="P.Iva" value="<?php echo $wContatti_partita_iva;?>" data-container="body" data-placement="top" data-original-title="PARTITA IVA"> </div>
                               </div>

                               <div class="row" style="margin-bottom:10px;">
                                   <div class="col-md-4">
                                       <input name="wContatti_cognome" id="wContatti_cognome" type="text" class="form-control tooltips" placeholder="Cognome" value="<?php echo $wContatti_cognome;?>" data-container="body" data-placement="top" data-original-title="COGNOME"> </div>
                                   <div class="col-md-4">
                                       <input name="wContatti_nome" id="wContatti_nome" type="text" class="form-control tooltips" placeholder="Nome" value="<?php echo $wContatti_nome;?>" data-container="body" data-placement="top" data-original-title="NOME"> </div>
                                   <div class="col-md-4">
                                       <input name="wContatti_codice_fiscale" id="wContatti_codice_fiscale" type="text" class="form-control tooltips" placeholder="Codice Fiscale" value="<?php echo $wContatti_codice_fiscale;?>" data-container="body" data-placement="top" data-original-title="CODICE FISCALE"> </div>
                               </div>

                               <div class="row" style="margin-bottom:10px;">
                                   <div class="col-md-4">
                                       <input name="wContatti_indirizzo" id="wContatti_indirizzo" type="text" class="form-control tooltips" placeholder="Indirizzo" value="<?php echo $wContatti_indirizzo;?>" data-container="body" data-placement="top" data-original-title="INDIRIZZO"> </div>
                                   <div class="col-md-2">
                                       <input name="wContatti_cap" id="wContatti_cap" type="text" class="form-control tooltips" placeholder="CAP" value="<?php echo $wContatti_cap;?>" data-container="body" data-placement="top" data-original-title="CAP"> </div>
                                   <div class="col-md-2">
                                       <input name="wContatti_citta" id="wContatti_citta" type="text" class="form-control tooltips" placeholder="Cittá" value="<?php echo $wContatti_citta;?>" data-container="body" data-placement="top" data-original-title="CITTÁ"> </div>
                                   <div class="col-md-2">
                                       <input name="wContatti_provincia" id="wContatti_provincia" type="text" class="form-control tooltips" placeholder="Provincia" value="<?php echo $wContatti_provincia;?>" data-container="body" data-placement="top" data-original-title="PROVINCIA"> </div>
                                   <div class="col-md-2">
                                       <input name="wContatti_nazione" id="wContatti_nazione" type="text" class="form-control tooltips" placeholder="Nazione" value="<?php echo $wContatti_nazione;?>" data-container="body" data-placement="top" data-original-title="NAZIONE"> </div>
                               </div>

                               <div class="row" style="margin-bottom:10px;">
                                   <div class="col-md-4">
                                     <div class="input-group">
                                       <span class="input-group-addon" style="background-color: #fff;"><i class="fa fa-phone-square font-grey-mint"></i></span>
                                       <input name="wContatti_telefono" id="wContatti_telefono" type="text" class="form-control tooltips" placeholder="Telefono" value="<?php echo $wContatti_telefono;?>" data-container="body" data-placement="top" data-original-title="TELEFONO"></div></div>
                                   <div class="col-md-4">
                                       <input name="wContatti_fax" id="wContatti_fax" type="text" class="form-control tooltips" placeholder="Fax" value="<?php echo $wContatti_fax;?>" data-container="body" data-placement="top" data-original-title="FAX"> </div>
                                   <div class="col-md-4">
                                       <input name="wContatti_cellulare" id="wContatti_cellulare" type="text" class="form-control tooltips" placeholder="Cellulare" value="<?php echo $wContatti_cellulare;?>" data-container="body" data-placement="top" data-original-title="CELLULARE"> </div>
                               </div>

                               <div class="row" style="margin-bottom:10px;">
                                   <div class="col-md-6">
                                     <div class="input-group">
                                       <span class="input-group-addon" style="background-color: #fff;"><i class="fa fa-globe font-grey-mint"></i></span>
                                         <input name="wContatti_web" id="wContatti_web" type="text" class="form-control tooltips" placeholder="Sito Web" value="<?php echo $wContatti_web;?>" data-container="body" data-placement="top" data-original-title="SITO WEB">
                                         <span class="input-group-btn"><a href="http://<?php echo $wContatti_web;?>" class="btn grey-mint" target="_blank"> Vedi <i class="fa fa-external-link"></i></a>
                                   </span>
                                 </div></div>
                                   <div class="col-md-6">
                                     <!-- INIZIO /btn-group -->
                                         <div class="input-group">
                                           <span class="input-group-addon" style="background-color: #fff;"><i class="fa fa-envelope font-grey-mint"></i></span>
                                                 <input name="wContatti_email" id="wContatti_email" type="text" class="form-control tooltips" placeholder="Email" value="<?php echo $wContatti_email;?>" data-container="body" data-placement="top" data-original-title="EMAIL">
                                               <!--<div class="input-group-btn">
                                                   <button type="button" class="btn grey-mint dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Azioni
                                                       <i class="fa fa-angle-down"></i>
                                                   </button>
                                                   <ul class="dropdown-menu pull-right">
                                                       <li>
                                                           <a href="tab#tab_invia_email_presentazione"><i class="fa fa-envelope font-grey-mint"></i> Invia Email di Presentazione</a>
                                                       </li>
                                                       <li>
                                                           <a href="#tab_invia_documentazione"><i class="fa fa-file-pdf-o font-grey-mint"></i> Invia Documentazione </a>
                                                       </li>

                                                   </ul>
                                               </div>-->

                                           </div>
                                           <!-- FINE /btn-group -->
                                     </div>
                               </div>
                               <div class="row" style="margin-bottom:10px;">
                                   <div class="col-md-6">
                                       <input name="wContatti_settore" id="wContatti_settore" type="text" class="form-control tooltips" placeholder="Settore" value="<?php echo $wContatti_settore;?>" data-container="body" data-placement="top" data-original-title="SETTORE"></div>
                                   <div class="col-md-6">
                                       <input name="wContatti_categoria" id="wContatti_categoria" type="text" class="form-control tooltips" placeholder="Categoria" value="<?php echo $wContatti_categoria;?>" data-container="body" data-placement="top" data-original-title="CATEGORIA"> </div>
                               </div>

                           </div>
                           <!-- END PERSONAL INFO TAB -->
                           <!-- CHANGE AVATAR TAB -->
                           <div class="tab-pane" id="tab_1_2">


                           </div>
                           <!-- END CHANGE AVATAR TAB -->
                           <!-- TAB tab_invia_email_presentazione -->
                           <div class="tab-pane" id="tab_invia_email_presentazione">
                             <!-- <h3 class="form-section">Invia Email </h3> -->
                             <div class="row" style="margin-bottom:10px;">
                                 <div class="col-md-6">
                                   <div class="input-group">
                                     <span class="input-group-addon" style="background-color: #fff;"><i class="fa fa-user font-grey-mint"></i></span>
                                     <input name="mitt" id="mitt" type="text" class="form-control tooltips" placeholder="Mittente" value="<?php echo $mitt;?>" data-container="body" data-placement="top" data-original-title="MITTENTE"></div></div>
                                     <div class="col-md-6">
                                       <div class="input-group">
                                         <span class="input-group-addon" style="background-color: #fff;"><i class="fa fa-user font-grey-mint"></i></span>
                                         <input name="dest" id="dest" type="text" class="form-control tooltips" placeholder="Destinatario" value="<?php echo $dest;?>" data-container="body" data-placement="top" data-original-title="DESTINATARIO"></div></div>
                             </div>
                             <div class="row" style="margin-bottom:10px;">
                                 <div class="col-md-12">
                                   <div class="input-group">
                                     <span class="input-group-addon" style="background-color: #fff;"><i class="fa fa-user font-grey-mint"></i></span>
                                     <input name="dest_cc" id="dest_cc" type="text" class="form-control tooltips" placeholder="CC" value="<?php echo $dest_cc;?>" data-container="body" data-placement="top" data-original-title="CC"></div></div>
                             </div>
                             <div class="row" style="margin-bottom:10px;">
                                 <div class="col-md-12">
                                   <div class="input-group">
                                     <span class="input-group-addon" style="background-color: #fff;"><i class="fa fa-user font-grey-mint"></i></span>
                                     <input name="ogg" id="ogg" type="text" class="form-control tooltips" placeholder="Oggetto" value="<?php echo $ogg.str_replace('_',' ',str_replace('.pdf','',$filename)); ?>" data-container="body" data-placement="top" data-original-title="OGGETTO"></div></div>
                             </div>
                             <div class="row" style="margin-bottom:10px;">
                               <div class="col-md-9">
                                   <div class="mt-checkbox-inline">
                                       <label class="mt-checkbox font-blue-steel">
                                           <input type="checkbox" id="allegato_presentazione" name="allegato_presentazione" value="allegato_presentazione" checked> Presentazione
                                           <span></span>
                                       </label>
                                       <label class="mt-checkbox font-blue-steel">
                                           <input type="checkbox" id="allegato_scheda_prodotto" name="allegato_scheda_prodotto" value="allegato_scheda_prodotto"> Scheda Prodotto
                                           <span></span>
                                       </label>
                                       <label class="mt-checkbox font-blue-steel">
                                           <input type="checkbox" id="allegato_offerta" name="allegato_offerta" value="allegato_offerta"> Offerta
                                           <span></span>
                                       </label>
                                   </div>
                                 </div>
                                 <div class="col-md-3">
                                   <div class="form-group">
                                       <!--<label for="allegato_1">Seleziona Allegato</label>
                                       <input type="file" id="allegato_1">
                                       <p class="help-block"> some help text here. </p>-->
                                       <div class="btn-set pull-left">
                                         <div class="fileinput fileinput-new" data-provides="fileinput">
                                             <span class="btn btn-primary btn-file btn-sm">
                                                 <span class="fileinput-new"> Seleziona Allegato </span>
                                                 <span class="fileinput-exists"> Cambia </span>
                                                 <input type="file" name="documentoAllegato1">
                                                 </span>
                                             <span class="fileinput-filename"> </span> &nbsp;
                                             <a href="javascript:;" class="close fileinput-exists" data-dismiss="fileinput"> </a>
                                         </div>
                                       </div>
                                   </div>
                                 </div>
                             </div>
                               <div class="row" style="margin-bottom:10px;">
                                     <div class="form-group">
                                         <div class="col-md-12">
                                               <!--<textarea id="editor" name="editor" name="content" data-provide="markdown" rows="10"><?php echo $mess;?></textarea>
                                             <div name="summernote" id="summernote_1"> </div>-->
                                             <textarea id="mess" name="mess" class="wysihtml5 form-control" rows="6"><?php echo $mess;?></textarea>
                                         </div>
                                     </div>
                             </div>

                           </div>
                       </div>

                       <?php if($arrayReturn['save']){ ?>
                                       <div class="form-actions right">
                                           <div class="row">
                                               <div class="col-md-offset-3 col-md-9">
                                                   <button <?php echo $stile_bottone_salva; ?> type="submit" class="btn btn-circle btn-lg green-jungle"><i class="fa fa-check"></i> Salva</button>
                                               </div>
                                           </div>
                                       </div>
                       <?php } ?>

                        <input type="HIDDEN" name="wContatti_id_contatto" id="wContatti_id_contatto" value="<?php echo $idContatto; ?>" class="form-control" />
                        <input type="HIDDEN" name="wContatti_codice" id="wContatti_codice" value="<?php echo $wContatti_codice; ?>" class="form-control" />
                        <input type="HIDDEN" name="wContatti_scrittore" id="wContatti_scrittore" value="<?php echo $_SESSION['cognome_nome_utente']; ?>" class="form-control" />
                        <input type="HIDDEN" name="wIdCommessaTLM" id="wIdCommessaTLM" value="<?php echo $_SESSION['idCommessaTLM']; ?>" class="form-control" />
                        <input type="HIDDEN" name="wIdProcessoTLM" id="wIdProcessoTLM" value="<?php echo $_SESSION['idProcessoTLM']; ?>" class="form-control" />
                        <!-- fine form qui  </form>-->
                   </div>
               </div>
           </div>

       </div>
   </div>
   </form>
   <!-- END PROFILE CONTENT -->
<?php
 }

 function creaSQLesporta(){
    $arrayCampi = $_POST;
    $nome_id = $arrayCampi['txt_id'];
    $nome_tabella = $arrayCampi['txt_tbl'];
    $nome_where = $arrayCampi['txt_where'];
    $nome_referer = $arrayCampi['txt_referer'];

    $conto = 0;

    $tuttiCampi = array();
    $arrayTXT = array();
    $arrayCHK = array();
    $arraySLK = array();
    $arrayETK = array();

    foreach ($arrayCampi as $key => $value) {
        $pos_2 = strpos($key, "chk_");
        if($pos_2 !== false){
            $arrayCHK[$key] = true;
        }
        $pos_3 = strpos($key, "slk_");
        if($pos_3 !== false){
            $arraySLK[$key] = $value;
            unset($arrayCampi[$key]);
        }
        $pos_4 = strpos($key, "etk_");
        if($pos_4 !== false){
            $arrayETK[$key] = $value;
            unset($arrayCampi[$key]);
        }
        $pos_5 = strpos($key, "inner_select_");
        if($pos_5 !== false){
            if(!empty($arrayCampi[str_replace('inner_select_','chk_',$key)])){
                unset($arrayCampi[str_replace('inner_select_','chk_',$key)]);
            }else{
                unset($arrayCampi[$key]);
            }
        }
    }

    $campi_visualizzati = "";
    $campi_where = "";
    
    foreach ($arrayCampi as $key => $value) {
        $arrayCampi[$key] = trim(str_replace("`", "", $value));
        $pos = strpos($key, "txt_");
        $pos_2 = strpos($key, "chk_");
        $pos_3 = strpos($key, "inner_select_");
        if($pos !== false) {
        }elseif($pos_2 !== false && $pos_3 === false){
            switch (str_replace('chk_','',$key)) {
                case "id_azienda":
                    $campi_visualizzati .= "(SELECT CONCAT(lista_aziende.ragione_sociale,' ',lista_aziende.forma_giuridica) AS nome FROM lista_aziende WHERE lista_aziende.id=".$nome_tabella.".".str_replace('chk_','',$key).")  AS '".$arrayETK[str_replace('chk_','etk_',$key)]."', ";
                break;
                
                case "id_professionista":
                    $campi_visualizzati .= "(SELECT CONCAT(lista_professionisti.cognome,' ',lista_professionisti.nome) AS nome FROM lista_professionisti WHERE lista_professionisti.id=".$nome_tabella.".".str_replace('chk_','',$key).")  AS '".$arrayETK[str_replace('chk_','etk_',$key)]."', ";
                break;
                
                case "id_agente":
                    $campi_visualizzati .= "(SELECT CONCAT(lista_password.cognome,' ',lista_password.nome) AS nome FROM lista_password WHERE lista_password.id=".$nome_tabella.".".str_replace('chk_','',$key).")  AS '".$arrayETK[str_replace('chk_','etk_',$key)]."', ";
                break;
            
                case "id_campagna":
                    $campi_visualizzati .= "(SELECT lista_campagne.nome FROM lista_campagne WHERE lista_campagne.id=".$nome_tabella.".".str_replace('chk_','',$key).")  AS '".$arrayETK[str_replace('chk_','etk_',$key)]."', ";
                break;
                
                case "id_tipo_marketing":
                    $campi_visualizzati .= "(SELECT lista_tipo_marketing.nome FROM lista_tipo_marketing WHERE lista_tipo_marketing.id=".$nome_tabella.".".str_replace('chk_','',$key).")  AS '".$arrayETK[str_replace('chk_','etk_',$key)]."', ";
                break;
                
                case "id_prodotto":
                    $campi_visualizzati .= "(SELECT lista_prodotti.nome FROM lista_prodotti WHERE lista_prodotti.id=".$nome_tabella.".".str_replace('chk_','',$key).")  AS '".$arrayETK[str_replace('chk_','etk_',$key)]."', ";
                break;
                
                case "id_classe":
                    $campi_visualizzati .= "(SELECT lista_classi.nome FROM lista_classi WHERE lista_classi.id=".$nome_tabella.".".str_replace('chk_','',$key).")  AS '".$arrayETK[str_replace('chk_','etk_',$key)]."', ";
                break;
                
                default:
                    $campi_visualizzati .= str_replace('chk_','',$key)." AS '".$arrayETK[str_replace('chk_','etk_',$key)]."', ";
                break;
            }
        }elseif($pos_3 !== false){
            switch ($nome_tabella) {
                case "lista_iscrizioni":
                case "lista_preventivi":
                case "lista_ordini":
                    $campi_visualizzati .= $value." AS '".$arrayETK[str_replace('inner_select_','etk_',$key)]."', ";
                    
                    /*$campi_visualizzati .= "(SELECT email AS email_professionista FROM lista_professionisti WHERE lista_professionisti.id=lista_preventivi.id_professionista)  AS email_professionista, ";
                    $campi_visualizzati .= "(SELECT CONCAT(lista_aziende.indirizzo,' ',lista_aziende.cap,' ',lista_aziende.citta,' (',lista_aziende.provincia,')') AS Indirizzo FROM lista_aziende WHERE lista_aziende.id=lista_preventivi.id_azienda)  AS indirizzo_professionista, ";
                    $campi_visualizzati .= "CONCAT((SELECT GROUP_CONCAT(lista_preventivi_dettaglio.nome_prodotto,' (', lista_preventivi_dettaglio.codice_prodotto ,')' SEPARATOR '<br>') FROM lista_preventivi_dettaglio WHERE lista_preventivi_dettaglio.id_preventivo = lista_preventivi.id)) AS elenco_prodotti, ";
                    $campi_visualizzati .= "(SELECT professione AS email_professionista FROM lista_professionisti WHERE lista_professionisti.id=lista_preventivi.id_professionista)  AS professione, ";
                    $campi_visualizzati .= "(SELECT provincia_albo AS email_professionista FROM lista_professionisti WHERE lista_professionisti.id=lista_preventivi.id_professionista)  AS provincia_albo, ";
                    $campi_visualizzati .= "(SELECT numero_albo AS email_professionista FROM lista_professionisti WHERE lista_professionisti.id=lista_preventivi.id_professionista)  AS numero_albo, ";*/
                break;
                
                default:
                break;
            }
        }else{
            if(strlen($value)>0 && isset($arrayCHK['chk_'.$key])){
                switch ($arraySLK['slk_'.$key]) {
                    case "IN":
                    case "NOT IN":
                        $campi_where .= ' AND '.$key." ".$arraySLK['slk_'.$key]." ".($value)." ";
                    break;
                    
                    case "ESCLUDI":
                    case "BETWEEN":
                        if($arraySLK['slk_'.$key]=="BETWEEN"){
                            $data_in = before(' al ', $value);
                            $data_out = after(' al ', $value);
                            if($data_in == $data_out){
                                $campi_where .= ' AND '.$key." = '".GiraDataOra($data_in)."' ";
                            }else{
                                $campi_where .= ' AND '.$key." ".$arraySLK['slk_'.$key]." '".GiraDataOra($data_in)."' AND '".GiraDataOra($data_out)."' ";
                            }
                        }
                    break;
                    
                    case "=":
                    case "!=":
                    case ">=":
                    case "<=":
                    case "<":
                    case ">":
                    case "LIKE":
                        switch ($key) {
                            case "dataagg":
                            case "datainsert":
                            case "data":
                            case "data_inizio":
                            case "data_fine":
                                $campi_where .= ' AND '.$key." ".$arraySLK['slk_'.$key]." '".GiraDataOra($value)."' ";
                            break;

                            default:
                                $campi_where .= ' AND '.$key." ".$arraySLK['slk_'.$key]." '".$value."' ";
                            break;
                        }

                    break;

                    case "% LIKE":
                         $campi_where .= ' AND '.$key." LIKE '%".$value."' ";
                    break;

                    case "LIKE %":
                         $campi_where .= ' AND '.$key." LIKE '".$value."%' ";
                    break;

                    case "% LIKE %":
                    default:
                         $campi_where .= ' AND '.$key." LIKE '%".$value."%' ";
                    break;
                }
                //$campi_where .= ' AND '.$key." ".$arraySLK['slk_'.$key]." '%".$value."%' ";
            }
        }
        $tuttiCampi[$key] = trim(str_replace("`", "", $value));
     }

    $campi_visualizzati_perfetti = substr($campi_visualizzati,0,-2);
    $campi_where_perfetti = substr($campi_where,0,-1);
    $sql_0001 = "SELECT  ".$campi_visualizzati_perfetti." FROM ".$nome_tabella." WHERE 1 ".$campi_where_perfetti;

    return $sql_0001;
 }
?>
