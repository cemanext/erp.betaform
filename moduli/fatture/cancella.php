<?php
include_once('../../config/connDB.php');
include_once(BASE_ROOT.'config/confAccesso.php');

global $dblink;

$referer = recupera_referer();

//RECUPERO LA TABELLA
if(isset($_GET['tbl'])){
    $tabella = $_GET['tbl'];
}else{
    $tabella = "";
}

//RECUPERO L'ID
if(isset($_GET['id'])){
    $id = $_GET['id'];
    $where = "id='".$_GET['id']."'";
}else{
    $id = '';
    $where = "1";
}

if($tabella!=""){
    if($tabella == "lista_fatture"){
        $ok = $dblink->deleteWhere($tabella."_dettaglio", "id_fattura='".$id."'");
        $ok = $dblink->deleteWhere($tabella, $where, 1);
    }else{
        $ok = $dblink->deleteWhere($tabella, $where, 1);
    }
}else{
    $ok = true;
}
if($ok) header("Location:".$_SERVER['HTTP_REFERER']."");
       else echo "error deleteWhere";

?>