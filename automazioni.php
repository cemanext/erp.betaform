<?php

if(isset($_POST)){
    $_POST['referer'] = !empty($_POST['referer']) ? $_POST['referer'] : $_SERVER['HTTP_REFERER'];
    require_once $_SERVER['DOCUMENT_ROOT'].'/libreria/automazioni/salvaRichiesta.php';
}else if(isset($_GET) && $_GET['fn']!=""){
    switch ($_GET['fn']) {
        case "contaAccessi":
            require_once $_SERVER['DOCUMENT_ROOT'].'/libreria/automazioni/contaAccessi.php';
        break;

        default:
        break;
    }
}else{
    header("Location: ".$_SERVER['HTTP_REFERER']);
}

?>
