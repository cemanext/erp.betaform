<?php

//questo sarebbe il dettRecord
function Stampa_HTML_Dettaglio($tabella, $id) {
    global $dblink;

    switch ($tabella) {
        case 'lista_prodotti':
            echo '<div class="row"><div class="col-md-12 col-sm-12">';
            $sql_0001 = "SELECT 
                    CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"modifica.php?tbl=lista_prodotti&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',
                    CONCAT('<B>',`nome`,'</b>') AS 'Prodotto',
                    gruppo, tipologia, 
                    codice, 
                    `prezzo_pubblico` AS 'Prezzo €',
                    LEFT(SEC_TO_TIME(`tempo_1`),8) AS 'Durata',
                    `stato`
                    FROM lista_prodotti WHERE id=" . $id;
            stampa_table_static_basic($sql_0001, '', 'Prodotto / Corso', 'green-haze');
            echo '</div></div>';

            $sql_00001 = "SELECT gruppo, codice_esterno FROM lista_prodotti WHERE id='" . $id . "' LIMIT 1";
            list($categoria_gruppo, $id_moodle) = $dblink->get_row($sql_00001);
            
            //echo '<li>$categoria_gruppo = '.$categoria_gruppo.'</li>';
            
            $stile_form = "";
            
            switch ($categoria_gruppo) {
                case "ABBONAMENTO":
                    //STAMPO I CORSI E RELATIVI MODULI 
                    echo '<form method="POST" action="salva.php?idAbb=' . $id . '&fn=SalvaAbbonamentoDettaglio" style="border:0px solid red; padding:20px; '.$stile_form.'">';
                    echo '<div class="row"><div class="col-md-12 col-sm-12">';
                    $sql_0001 = "SELECT DISTINCT 
                            id,
                            id_prodotto AS 'nome_prodotto',
                            `stato` 
                            FROM `lista_prodotti_dettaglio` 
                            WHERE `id_prodotto_0`=" . $id . " 
                            AND gruppo='CORSO'
                            ORDER BY ordine ASC, id ASC";
                    stampa_table_static_basic_input('lista_prodotti_dettaglio', $sql_0001, '', 'STAMPO I CORSI E RELATIVI MODULI');
                    //stampa_table_static_basic($sql_0001, '', 'STAMPO I CORSI E RELATIVI MODULI', '');
                    echo '<center><a href="salva.php?tbl=lista_prodotti_dettaglio&idAbbonamento=' . $id . '&fn=inserisciProdottoDettaglioAbbonamento" class="btn btn-icon blue-steel">
                            Aggiungi Corso
                            <i class="fa fa-plus"></i>
                            </a>&nbsp;&nbsp;
                            <button id="salvaDettaglioAbbonamento" type="submit" class="btn btn-icon green-meadow"><i class="fa fa-save"></i> SALVA</button>
                            </center>';
                    echo '</div></div>';
                    echo "</form>\n";
                break;
                
                case "PACCHETTO":
                    //STAMPO I CORSI E RELATIVI MODULI 
                    echo '<form method="POST" action="salva.php?idAbb=' . $id . '&fn=SalvaPacchettoDettaglio" style="border:0px solid red; padding:20px; '.$stile_form.'">';
                    echo '<div class="row"><div class="col-md-12 col-sm-12">';
                    $sql_0001 = "SELECT DISTINCT 
                            id,
                            id_prodotto AS 'nome_prodotto',
                            `stato` 
                            FROM `lista_prodotti_dettaglio` 
                            WHERE `id_prodotto_0`=" . $id . " 
                            AND gruppo='CORSO'
                            ORDER BY ordine ASC, id ASC";
                    stampa_table_static_basic_input('lista_prodotti_dettaglio', $sql_0001, '', 'STAMPO I CORSI E RELATIVI MODULI');
                    //stampa_table_static_basic($sql_0001, '', 'STAMPO I CORSI E RELATIVI MODULI', '');
                    echo '<center><a href="salva.php?tbl=lista_prodotti_dettaglio&idAbbonamento=' . $id . '&fn=inserisciProdottoDettaglioPacchetto" class="btn btn-icon blue-steel">
                            Aggiungi Corso
                            <i class="fa fa-plus"></i>
                            </a>&nbsp;&nbsp;
                            <button id="salvaDettaglioPacchetto" type="submit" class="btn btn-icon green-meadow"><i class="fa fa-save"></i> SALVA</button>
                            </center>';
                    echo '</div></div>';
                    echo "</form>\n";
                break;

                case "CORSO":
                    //STAMPO DIRETTAMENTE MODULI 
                    echo '<div class="row"><div class="col-md-12 col-sm-12">';
                    $sql_0001 = "SELECT DISTINCT 
                             CONCAT('<a class=\"btn btn-circle btn-icon-only blue btn-outline\" href=\"modifica.php?tbl=lista_prodotti_dettaglio&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><i class=\"fa fa-edit\"></i></a>') AS 'fa-edit',
                             ordine AS 'N.',
                             CONCAT('<B>',`nome`,'</B>') AS 'Prodotto',
                            `descrizione`, codice, `numerico_1` AS 'Durata H', `stato` 
                            FROM `lista_prodotti_dettaglio` 
                            WHERE `id_prodotto`=" . $id . " 
                            AND gruppo='MODULO'
                            ORDER BY ordine ASC";
                    stampa_table_static_basic($sql_0001, '', 'STAMPO DIRETTAMENTE MODULI ', '');
                    echo '<center><a href="'.BASE_URL.'/libreria/automazioni/sinc_moodle.php?fn=lista_prodotti_dettaglio&id='.$id_moodle.'&idProd='.$id.'" class="btn yellow-gold">
                            Aggiorna Corso
                            <i class="fa fa-refresh"></i>
                            </a></center>';
                    echo '</div></div>';
                    echo '</div></div>';
                break;
                    
                default:
                    //echo 'Chiamate CEMA NEXT !';
                break;
            }

            break;

        case 'lista_aziende':
            echo '<div class="row"><div class="col-md-12 col-sm-12">';
            stampa_bootstrap_form_horizontal($tabella, $id, 'Dettaglio Anagrafica Modifica');
            echo '</div></div>';

            echo '<div class="row">';
            echo '<div class="col-md-6 col-sm-6">';
            $sql_0002 = "SELECT DISTINCT CONCAT('<a href=\"modifica.php?tbl=lista_professionisti&id=',lista_professionisti.id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><button type=\"button\" class=\"btn blue btn-warning mt-ladda-btn ladda-button btn-circle btn-icon-only\"><i class=\"fa fa-edit\"></i></button></a>') AS 'fa-edit',
                    cognome, nome, email FROM lista_professionisti INNER JOIN matrice_aziende_professionisti
                    ON lista_professionisti.id = matrice_aziende_professionisti.id_professionista
                    WHERE matrice_aziende_professionisti.id_azienda = " . $id;
            //echo $sql_0002;
            stampa_table_static_basic($sql_0002, '', 'Lista Professionisti', 'yellow');
            echo '<a href="salva.php?tbl=lista_aziende&id=' . $id . '&fn=inserisciProfessionista" class="btn green-meadow">
                    Aggiungi Professionista
                    <i class="fa fa-plus"></i>
                    </a>';
            echo '</div>';
            echo '<div class="col-md-6 col-sm-6">';
            $sql_0002 = "SELECT CONCAT('<a href=\"modifica.php?tbl=lista_indirizzi&id=',id,'\" title=\"MODIFICA\" alt=\"MODIFICA\"><button type=\"button\" class=\"btn blue btn-warning mt-ladda-btn ladda-button btn-circle btn-icon-only\"><i class=\"fa fa-edit\"></i></button></a>') AS 'fa-edit',
                    indirizzo, citta, provincia, "
                    . "IF(tipo='Predefinito', tipo, CONCAT('<a href=\"salva.php?tbl=lista_indirizzi&id=',id,'&id_azienda=',id_azienda,'&fn=settaPredefinito\" title=\"SETTA A PREDEFINITO\" alt=\"SETTA A PREDEFINITO\"><button type=\"button\" class=\"btn blue btn-warning mt-ladda-btn ladda-button btn-circle btn-icon-only\"><i class=\"fa fa-check\"></i></button></a>')) AS 'fa-check'"
                    . " FROM lista_indirizzi WHERE id_azienda = $id ORDER BY tipo DESC, id ASC";
            stampa_table_static_basic($sql_0002, '', 'Lista Indirizzi', 'green');
            echo '<a href="salva.php?tbl=lista_aziende&id=' . $id . '&fn=inserisciIndirizzo" class="btn green-meadow">
                    Aggiungi Indirizzo
                    <i class="fa fa-plus"></i>
                    </a>';
            echo '</div>';
            echo '</div>';
            break;

        case 'lista_professionisti':
            $sql_0001 = "SELECT cognome, nome FROM lista_professionisti WHERE id = " . $id;
            stampa_table_static_basic($sql_0001, '', 'Dettaglio -> lista_professionisti', '');
            break;

        default:
            $campi_visualizzati = "";
            $campi     =    $dblink->list_fields("SELECT * FROM ".$tabella."");
            foreach ($campi as $nome_colonna) {
                 $campi_visualizzati.= "`".$nome_colonna->name."`, ";
            }
            $campi_visualizzati = substr($campi_visualizzati, 0, -2);
            stampa_table_static_basic("SELECT DISTINCT $campi_visualizzati FROM " . $tabella . " WHERE id = " . $id . "", '', 'Dettaglio', '');
            break;
    }
    return;
}

?>
