<?php
include_once('../../config/connDB.php');
include_once(BASE_ROOT . 'config/confAccesso.php');
include_once(BASE_ROOT.'classi/webservice/client.php');

$moodle = new moodleWebService();

$referer = recupera_referer();

if(isset($_GET['fn'])){
    switch ($_GET['fn']) {
        
        case 'iscriviEsameUtente':
            $ok = true;
            $dblink->begin();
            
            $idCalendario = $_GET['idCalendario'];
            $idIscrizione = $_GET['idIscrizione'];
            $idProfessionista = $_GET['idProfessionista'];
            $sql_iscrivi_esame_utente = "INSERT INTO calendario (id, dataagg, scrittore, id_calendario_0, etichetta, id_professionista, stato, id_corso, id_iscrizione, oggetto, data, ora) 
            SELECT '', NOW(), '".$_SESSION['cognome_nome_utente']."', '".$idCalendario."', 'Iscrizione Esame', '".$idProfessionista."', 'Iscritto', id_corso, '".$idIscrizione."', oggetto, data, ora FROM calendario WHERE id='".$idCalendario."'";
            $ok = $ok && $dblink->query($sql_iscrivi_esame_utente);
            $lastId=$dblink->insert_id();
            
            $numero_iscritti_al_corso = $dblink->get_field("SELECT COUNT(*) AS conteggio FROM calendario WHERE id_calendario_0='".$idCalendario."' AND etichetta='Iscrizione Esame'");
            
            $sql_002 = "UPDATE calendario SET numerico_10 = '".$numero_iscritti_al_corso."' WHERE id='".$idCalendario."' AND etichetta LIKE 'Calendario Esami'";
            $ok = $ok && $dblink->query($sql_002);
            
            if($ok){
                $ok = 1;
                $dblink->commit();
            }else{
                $ok = 0;
                $dblink->rollback();
            }
            header("Location:".$referer."&ret=$ok");
        break;
        
        
        case 'abilitaUtente':
            $idUtenteMoodle = $_GET['idUtenteMoodle'];
            $return = $moodle->abilitaUtenteMoodle($idUtenteMoodle);
            if($return===true){
                $dblink->update("lista_password", array("stato"=>"Attivo"), array("id_moodle_user"=>$idUtenteMoodle));
                header("Location:$referer");
            }
        break;
        
        case 'disabilitaUtente':
            $idUtenteMoodle = $_GET['idUtenteMoodle'];
            $return = $moodle->disabilitaUtenteMoodle($idUtenteMoodle);
            if($return===true){
                $dblink->update("lista_password", array("stato"=>"Non Attivo"), array("id_moodle_user"=>$idUtenteMoodle));
                header("Location:$referer");
            }
        break;
        
        
        case 'disabilitaCorso':
                $idUtenteMoodle = $_GET['idUtenteMoodle'];
                $idCorso = $_GET['idCorso'];
                $idIscrizione = $_GET['idIscrizione'];
                 $return = $moodle->annullaCorsoMoodle($idUtenteMoodle, $idCorso);
                 if($return===true){
                    $dblink->update("lista_iscrizioni", array("stato"=>"Corso Disabilitato"), array("id"=>$idIscrizione));
                    header("Location:$referer");
                }
        break;
        
        case 'abilitaProrogaCorso':
                $idUtenteMoodle = $_GET['idUtenteMoodle'];
                $idCorso = $_GET['idCorso'];
                $idIscrizione = $_GET['idIscrizione'];
                $dataScadenza = $_GET['dataScadenza'];
                 $return = $moodle->prorogaCorsoMoodle($idUtenteMoodle, $idCorso, $dataScadenza);
                 if($return===true){
                    $dblink->update("lista_iscrizioni", array("stato"=>"In Attesa"), array("id"=>$idIscrizione));
                    header("Location:$referer");
                }
        break;
        
        case 'modificaScadenzaCorso':
                $idUtenteMoodle = $_GET['idUtenteMoodle'];
                $idCorso = $_GET['idCorso'];
                $NomeClasse = $_GET['NomeClasse'];
                $DataFineIscrizione = $_GET['DataFineIscrizione'];

                $return = $moodle->iscrizioneCorsoMoodle($idUtenteMoodle, $idCorso, $NomeClasse, $DataFineIscrizione);

            header("Location:$referer");
        break;
        
        case 'annullaAbbonamentoMoodle':
            $idFattura = $_GET['idFattura'];
            $idUtenteMoodle = $_GET['idUtenteMoodle'];
            $NomeClasse = $_GET['NomeClasse'];
            $idIscrizione = $_GET['idIscrizione'];

            $return = $moodle->annullaAbbonamentoMoodle($idUtenteMoodle, $NomeClasse);
            //array("id_fattura"=>$idFattura, "nome_classe" => $NomeClasse)
            if($return===true){
                $dblink->updateWhere("lista_iscrizioni", array("stato"=>"Abbonamento Disabilitato"), " id='".$idIscrizione."' AND (stato='Configurazione') AND id_utente_moodle='".$idUtenteMoodle."' AND abbonamento=1");
            }

            header("Location:$referer");
        break;
        
        case 'riabilitaAbbonamentoMoodle':
            $idFattura = $_GET['idFattura'];
            $idUtenteMoodle = $_GET['idUtenteMoodle'];
            $NomeClasse = $_GET['NomeClasse'];
            $DataFineIscrizione = $_GET['DataFineIscrizione'];
            $idIscrizione = $_GET['idIscrizione'];

            $return = $moodle->iscrizioneAbbonamentoMoodle($idUtenteMoodle, $NomeClasse, $DataFineIscrizione);
            
            $data_fine_iscrizione = GiraDataOra(str_replace("/", "-", $DataFineIscrizione));
            
            if($return===true){
                //$dblink->update("lista_iscrizioni", array("stato"=>"In Attesa"), array("id_fattura"=>$idFattura, "nome_classe" => $NomeClasse));
                $dblink->updateWhere("lista_iscrizioni", array("stato"=>"Configurazione"), " id='".$idIscrizione."' AND (stato='Abbonamento Disabilitato' OR stato='Configurazione') AND id_utente_moodle='".$idUtenteMoodle."' AND abbonamento=1");
                $dblink->updateWhere("lista_iscrizioni", array("data_fine_iscrizione"=>$data_fine_iscrizione, "stato"=>'In Attesa'), " id_fattura='".$idFattura."' AND (stato='In Corso' OR stato='In Attesa') AND id_utente_moodle='".$idUtenteMoodle."' AND abbonamento=1");
            }

            header("Location:$referer");
        break;
        
        case "aggiornaDataScadenzaIscrizione":
            salvaGenerale();
            
            if($_POST['txt_id'] > 0){
                
                $row = $dblink->get_row("SELECT id_utente_moodle, abbonamento FROM lista_iscrizioni WHERE id='".$_POST['txt_id']."'", true);
            
                $dblink->updateWhere("lista_iscrizioni", array("data_fine_iscrizione"=>GiraDataOra($_POST['data_fine_iscrizione']), "data_fine"=>GiraDataOra($_POST['data_fine_iscrizione'])), "id_utente_moodle='".$row['id_utente_moodle']."' AND abbonamento=1 AND lista_iscrizioni.data_inizio_iscrizione <= CURDATE() AND lista_iscrizioni.data_fine_iscrizione >= CURDATE()");
                
                if(!empty($_POST['id_classe'])){
                    $id_professionista = $dblink->get_field("SELECT id_professionista FROM lista_iscrizioni WHERE id = '".$_POST['txt_id']."'");
                    $nomeClasse = $dblink->get_field("SELECT nome FROM lista_classi WHERE id = '".$_POST['id_classe']."'");
                    $dblink->updateWhere("lista_iscrizioni", array("nome_classe" => $nomeClasse, "id_classe" => $_POST['id_classe']), "id_professionista='".$id_professionista."' AND stato NOT LIKE '%Scadu%' AND abbonamento = '1'");
                    //echo $dblink->get_query();
                }
            }
            
            $referer = $_POST['txt_referer'];
            header("Location:".$referer."");
        break;
        
        default:
            salvaGenerale();
            
            $referer = $_POST['txt_referer'];
            header("Location:".$referer."");
        break;
    }
}


?>
