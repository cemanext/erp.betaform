<?php
include_once('../../config/connDB.php');
include_once(BASE_ROOT.'config/confAccesso.php');

$referer = recupera_referer();


if(isset($_GET['fn'])){
    switch ($_GET['fn']) {
        case "salvaGenerale":
        default:
            $ok = true;
            $ok = salvaGenerale();
            
            if($_POST['txt_id'] > 0){
                if(!empty($_POST['id_fatture_banche'])){
                    $nomeBanca = $dblink->get_field("SELECT nome FROM lista_fatture_banche WHERE id = '".$_POST['id_fatture_banche']."'");
                    $ok = $ok && $dblink->update("lista_costi",array("nome_banca" => $nomeBanca),array("id" => $_POST['txt_id']));
                }
            }
            
            $referer = $_POST['txt_referer'];
            header("Location:".$referer."");
        break;
    }
}
?>