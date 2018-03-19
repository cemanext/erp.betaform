<?php

$ok = true;
$sqlCk1 = 'SELECT UNIX_TIMESTAMP(prossima_esecuzione) FROM lista_cron WHERE id = "1"';
$rowCk1 = $dblink->get_row($sqlCk1, true);

if($rowCk1['prossima_esecuzione'] <= time()) {
    
    $dataOrder = array(
        'dataagg'=> date("Y-m-d h:i:s"),
        'scrittore' => 'cron_webservice',
        'ultima_esecuzione'=> date("Y-m-d H:i:s"),
        'prossima_esecuzione'=> date("Y-m-d H:i:s",time()+300)
    );
    $ok = $ok && $dblink->updateWhere('lista_cron', $dataOrder, "id='1'");

    if($ok) $cronErp->executeCron(time());
    
}

if(!$ok){
    $log->log_all_errors('cron_webservice.php: impossibile salvare il TIME sul DB'.print_r($dataOrder,true),'ERROR');
}

// generate empty picture http://www.nexen.net/articles/dossier/16997-une_image_vide_sans_gd.php
/*$hex = "47494638396101000100800000ffffff00000021f90401000000002c00000000010001000002024401003b";
$img = '';
$t = strlen($hex) / 2;
for($i = 0; $i < $t; $i++) 
    $img .= chr(hexdec(substr($hex, $i * 2, 2) ));
header('Last-Modified: Fri, 01 Jan 1999 00:00 GMT', true, 200);
header('Content-Length: '.strlen($img));
header('Content-Type: image/gif');
echo $img;*/
?>
