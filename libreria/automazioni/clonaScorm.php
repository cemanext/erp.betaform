<?php
include_once('../../config/connDB.php');

if (isset($_POST) && !empty($_POST) && !empty($_POST['idSorgente'])
    && !empty($_POST['idDestinatario']) && !empty($_POST['idLastScorm']) && !empty($_POST['idCorso'])) {
    //OPERAZIONE
    $ok = true;
    $idCorso = $_POST['idCorso'];
    $idInstance = $_POST['idLastScorm'];
    $idSorgente = $_POST['idSorgente'];
    $idDestinatario = $_POST['idDestinatario'];
    
    $sql_0001 = "SELECT id, id_modulo, instance FROM lista_corsi_dettaglio WHERE id_corso_moodle='$idCorso' AND id_modulo = '".$idInstance."' ORDER BY ordine ASC ";
    $row_0001 = $dblink->get_row($sql_0001, true);

    $sql_0002 = "SELECT id_modulo, instance, modname FROM lista_corsi_dettaglio WHERE id_corso_moodle='$idCorso' AND id <= '".$row_0001['id']."' ORDER BY ordine DESC";
    $rs_0002 = $dblink->get_results($sql_0002);
    
    $saveScorm = array();
    $saveScorm['quiz'] = array();
    $saveScorm['scorm'] = array();
    foreach($rs_0002 AS $row_0002){
        //if($row_0002['modname']=="quiz") exit; //scorm
        $saveScorm[$row_0002['modname']]['instance'][] = $row_0002['instance'];
        $saveScorm[$row_0002['modname']]['itemid'][] = $row_0002['id_modulo'];
    }
    
    //print_r($saveScorm);
    
    foreach($saveScorm['scorm']['instance'] as $key => $value){
        $ok = $ok && duplicateWhereWhithReplace(MOODLE_DB_NAME.".mdl_scorm_scoes_track","userid = '".$idSorgente."' AND scormid = '".$value."'", '', '' , array("userid" => $idDestinatario), $value);
        //echo $dblink->get_query();
        //echo "<br>";
    }
    
    //echo "<hr>";
    echo "<h1>Processo Completato !</h1>";
    
    /*foreach($saveScorm['quiz']['instance'] as $key => $value){
        duplicateWhereWhithReplace(MOODLE_DB_NAME.".mdl_quiz_attempts","userid = '".$idSorgente."' AND quiz = '".$value."'", '', '' , array("userid" => $idDestinatario), $value);
        //echo $dblink->get_query();
        //echo "<br>";
    }*/
    
    
} else {
    
   /* 
    $sql = "SELECT lista_iscrizioni.id, lista_iscrizioni.id_classe, lista_iscrizioni.id_professionista, lista_iscrizioni.id_utente_moodle, lista_iscrizioni.id_corso, lista_corsi.id_corso_moodle FROM lista_iscrizioni JOIN lista_corsi ON (lista_corsi.id = lista_iscrizioni.id_corso) WHERE lista_iscrizioni.stato LIKE 'In Corso' LIMIT 2000 ";
    $tuttiCorsi = $dblink->get_results($sql);
    foreach($tuttiCorsi as $row){
        $id_classe = $row['id_classe'];
        $id_corso = $row['id_corso'];
        $id_professionista = $row['id_professionista'];
        $id_corso_moodle = $row['id_corso_moodle'];
        $id_utente_moodle = $row['id_utente_moodle'];
        
        $sql2 = "SELECT 
                    c.instance AS instance
            FROM 
            	".MOODLE_DB_NAME.".mdl_course_modules_completion AS q
            INNER JOIN 
            	".MOODLE_DB_NAME.".mdl_course_modules AS c
            ON
            (q.coursemoduleid = c.id)
            WHERE
              q.userid = '".$id_utente_moodle."'
              AND
        	  c.course = '".$id_corso_moodle."'
        	  AND
        	  q.completionstate = '1'
            ORDER BY c.section DESC LIMIT 1
            ";
        $row2 = $dblink->get_row($sql2, true);
        
        if(!empty($row2)){
            $instance = $row2['instance'];
            $controllo = beta_check_scorm_track_erp($id_utente_moodle, $instance, $id_corso_moodle);

            if(!$controllo){

                $id_utente_sorgente = $dblink->get_field("SELECT id_utente_moodle FROM lista_iscrizioni WHERE stato_completamento LIKE 'Completato' AND id_classe = '$id_classe' AND id_corso = '$id_corso' ORDER BY data_completamento DESC ");

                $insert = array(
                    "id_iscrizione" => $row['id'],
                    "id_corso" => $id_corso,
                    "id_professionista" => $id_professionista,
                    "id_corso_moodle" => $id_corso_moodle,
                    "id_utente_moodle" => $id_utente_moodle,
                    "instance" => $instance,
                    "stato" => "Da Recuperare",
                    "id_utente_source" => $id_utente_sorgente,
                    "id_classe" => $id_classe,
                );

                $dblink->insert("lista_iscrizioni_recupera", $insert);
            }
        }
    }
    
    */
    ?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
    <!--<![endif]-->
    <!-- BEGIN HEAD -->
    <head>
        <meta charset="utf-8" />
        <title><?php echo $site_name; ?> | INDEXRECORD</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <meta content="" name="description" />
        <meta content="" name="author" />
        <!-- BEGIN GLOBAL MANDATORY STYLES -->
        <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
        <link href="<?= BASE_URL ?>/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <link href="<?= BASE_URL ?>/assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
        <link href="<?= BASE_URL ?>/assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?= BASE_URL ?>/assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
        <!-- END GLOBAL MANDATORY STYLES -->
        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <link href="<?= BASE_URL ?>/assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css" rel="stylesheet" type="text/css" />
        <link href="<?= BASE_URL ?>/assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
        <link href="<?= BASE_URL ?>/assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="<?= BASE_URL ?>/assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?= BASE_URL ?>/assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />
        <link href="<?= BASE_URL ?>/assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?= BASE_URL ?>/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" type="text/css" />
        <link href="<?= BASE_URL ?>/assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />
        <link href="<?= BASE_URL ?>/assets/global/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.css" rel="stylesheet" type="text/css" />
        <link href="<?= BASE_URL ?>/assets/global/plugins/bootstrap-toastr/toastr.min.css" rel="stylesheet" type="text/css">
        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN THEME GLOBAL STYLES -->
        <link href="<?= BASE_URL ?>/assets/global/css/components.min.css" rel="stylesheet" id="style_components" type="text/css" />
        <link href="<?= BASE_URL ?>/assets/global/css/plugins.min.css" rel="stylesheet" type="text/css" />
        <!-- END THEME GLOBAL STYLES -->
        <!-- BEGIN PAGE LEVEL STYLES -->
        <link href="<?= BASE_URL ?>/assets/apps/css/todo-2.min.css" rel="stylesheet" type="text/css" />
        <!-- END PAGE LEVEL STYLES -->
        <!-- BEGIN THEME LAYOUT STYLES -->
        <link href="<?= BASE_URL ?>/assets/layouts/layout/css/layout.min.css" rel="stylesheet" type="text/css" />
        <link href="<?= BASE_URL ?>/assets/layouts/layout/css/themes/darkblue.min.css" rel="stylesheet" type="text/css" id="style_color" />
        <link href="<?= BASE_URL ?>/assets/layouts/layout/css/custom.min.css" rel="stylesheet" type="text/css" />
        <!-- END THEME LAYOUT STYLES -->
        <link rel="shortcut icon" href="favicon.ico" />
        <?php if(filter_input(INPUT_GET, 'tbl')!="lista_prodotti"){ ?>
        <style type="text/css">
            .dataTables_extended_wrapper {
                margin-top: 0px !important;
            }
            .dataTables_extended_wrapper .table.dataTable {
                margin: 0px 0!important;
            }
        </style>
        <?php } ?> 
    </head>

    <?php
    // fPer velocizzare la pagina
    flush();
    ?>

    <!-- END HEAD -->
    <body class="page-header-fixed page-sidebar-closed-hide-logo page-content-white page-sidebar-fixed">
    <form action="clonaScorm.php" method="POST" enctype="multipart/form-data" class="form">
        <div class="modal-body">
            <div class="row" style="margin-bottom:10px;">
                <div class="col-md-12"><h3 class="form-section">CLONA SCORM E TEST</h3></div>
            </div>
            <div class="row" style="margin-bottom:10px;">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-addon" style="background-color: #fff;"><i class="fa fa-clone font-grey-mint"></i></span>
                        <input name="idSorgente" id="idSorgente" type="text" class="form-control tooltips" placeholder="ID Utente da cui clonare" value="" data-container="body" data-placement="top" data-original-title="ID Utente Moodle da cui clonare"></div>
                </div>
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-addon" style="background-color: #fff;"><i class="fa fa-clipboard font-grey-mint"></i></span>
                        <input name="idDestinatario" id="idDestinatario" type="text" class="form-control tooltips" placeholder="ID Utente destinatario" value="" data-container="body" data-placement="top" data-original-title="ID Moodle Utente destinatario"></div>
                </div>
            </div>
            <div class="row" style="margin-bottom:10px;">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-addon" style="background-color: #fff;"><i class="fa fa-users font-grey-mint"></i></span>
                        <input name="idCorso" id="idCorso" type="text" class="form-control tooltips" placeholder="ID Corso Moodle" value="" data-container="body" data-placement="top" data-original-title="ID Corso Moodle"></div>
                </div>
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-addon" style="background-color: #fff;"><i class="fa fa-users font-grey-mint"></i></span>
                        <input name="idLastScorm" id="idLastScorm" type="text" class="form-control tooltips" placeholder="ID Ultimo SCORM da Attivare" value="" data-container="body" data-placement="top" data-original-title="ID Ultimo SCORM da Attivare"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" name="Invia" value="Invia" class="btn green">Clona</button>
            </div>
        </div>
    </form>
    </body>
</html>
    <?php
}

function duplicateWhereWhithReplace( $table, $where = "", $limit = '', $orderby = '', $replace = array(), $instance = '')
{
    global $dblink;
    
    $ok = true;
    
    $sql = "SELECT * FROM ". $table ."";
    if( !empty( $where ) )
    {
        $sql .= ' WHERE '. $where;   
    }
    if( !empty( $orderby ) )
    {
        $sql .= ' ORDER BY '. $orderby;
    }
    if( !empty( $limit ) )
    {
        $sql .= ' LIMIT '. $limit;
    }
    //echo $sql = str_replace("betaform_tenant_dev.", "betaform_tenant.", $sql);
    //echo "<br>";
    $res = $dblink->get_results($sql);
    //$first = true;
    if($table == "".MOODLE_DB_NAME.".mdl_quiz_attempts"){
        //$dblink->deleteWhere($table, "userid = '".$replace['userid']."' AND quiz = '".$instance."'");
        //echo $dblink->get_query();
        //echo "<br><br>";
    }else{
        if(!$dblink->num_rows("SELECT * FROM $table WHERE userid = '".$replace['userid']."' AND scormid = '".$instance."' AND element = 'cmi.core.lesson_status' AND value = 'completed'")){
            $ok = $ok && $dblink->deleteWhere($table, "userid = '".$replace['userid']."' AND scormid = '".$instance."'");
            //echo $dblink->get_query();
            //echo "<br><br>";
        }
    }

    foreach ($res as $value) {
        foreach($value as $key => $row) {
            switch ($key) {
                case "id":
                case "uniqueid":
                    //nothing
                break;
                default:
                    if(array_key_exists($key, $replace)){
                        $insert[$key] = $dblink->filter($replace[$key]);
                    }else{
                        $insert[$key]=$dblink->filter($row);
                    }
                break;
            }

        }
        
        
        
        /*$sql .= " ON DUPLICATE KEY UPDATE ";
        foreach ($insert as $key => $value) {
            $sql .= $key." = '".$dblink->filter($value)."', ";
        }
        $sql = substr($sql, 0, -2);*/
       
        
        if($table == "".MOODLE_DB_NAME.".mdl_quiz_attempts"){
            /*$uniqueVuota = ottieni_unique_id();
            
            $insert['uniqueid'] = $uniqueVuota;
            $sql = crea_sql_insert($table, $insert);
            echo $sql;
            echo "<br><br>";
            $dblink->query($sql);*/
            /*$lastid = $dblink->lastid();
            $dblink->update($table, array("uniqueid" => $lastid), array("id" => $lastid));
            echo $dblink->get_query();
            echo "<br><br>";*/
        }else{
            $sql = crea_sql_insert($table, $insert);
            //echo $sql;
            //echo "<br><br>";
            $ok = $ok && $dblink->query($sql);
        }
    }
    return $ok;
}

function ottieni_unique_id($idnuovo = 1){
    global $dblink;
    
    $rs_uniqueIDMax = $dblink->get_row("SELECT uniqueid FROM ".MOODLE_DB_NAME.".mdl_quiz_attempts WHERE uniqueid = '$idnuovo' ORDER BY uniqueid ASC", true);
    
    if(empty($rs_uniqueIDMax)){
        echo "RET: ".$idnuovo;
        echo "<br>";
        return $idnuovo;
    }else{
        echo "CICLO: ".$idnuovo = $idnuovo+1;
        echo "<br>";
        return ottieni_unique_id($idnuovo);
    }
}

function crea_sql_insert( $table, $variables = array() )
    {
        
        //Make sure the array isn't empty
        if( empty( $variables ) )
        {
            return false;
        }
        
        $sql = "INSERT INTO ". $table;
        $fields = array();
        $values = array();
        foreach( $variables as $field => $value )
        {
            $fields[] = $field;
            if(strpos($value, "CONCAT(")!==false || $value === "NOW()") {
                $values[] = "$value";
            }else{
                $values[] = "'".$value."'";
            }
        }
        $fields = ' (' . implode(', ', $fields) . ')';
        $values = '('. implode(', ', $values) .')';
        
        $sql .= $fields .' VALUES '. $values;
        return $sql;
        
    }
    
    
    function beta_check_scorm_track_erp($idUtente, $idInstance, $idCorso){
    	global $dblink;
    	
    	$sql = "SELECT 
				*
            FROM 
            	".MOODLE_DB_NAME.".mdl_scorm_scoes_track
            WHERE
              userid = '".$idUtente."'
        	AND element = 'cmi.core.lesson_status'
        	AND scormid = '".$idInstance."'
            ORDER BY id DESC
            ";
    	//echo "<br>";
    	
        $retScorm = $dblink->get_row($sql, true);
        
        if(!empty($retScorm)){
            if($retScorm['value'] == 'completed'){
                return true;
            }else{
                return false;
            }
        }else{
        	
            $sql2 = "SELECT 
				*
            FROM 
            	".MOODLE_DB_NAME.".mdl_scorm
            WHERE
              id = '".$idInstance."'
              AND
              course = '".$idCorso."'
            ORDER BY id DESC
            ";
            //echo "<br>";
            $retScorm2 = $dblink->get_row($sql2, true);

            $sqlQuiz = "SELECT attempts, sumgrades FROM ".MOODLE_DB_NAME.".mdl_quiz WHERE id = '".$idInstance."'";
            //echo "<br>";
            $quizAttemps = $dblink->get_row($sqlQuiz, true);

            if(!empty($retScorm2) && empty($quizAttemps)){
                return false;
            }else{

                $sql3 = "SELECT 
                                c.id AS itemid,
                q.attempt,
                q.quiz
                    FROM 
                ".MOODLE_DB_NAME.".mdl_quiz_attempts AS q
                INNER JOIN 
                ".MOODLE_DB_NAME.".mdl_course_modules AS c
                    ON
                (q.quiz = c.instance)
                    WHERE
                q.userid = '".$idUtente."'
                AND q.state = 'finished'
                AND c.course = '".$idCorso."' ";
                if($quizAttemps['attempts'] != 0){
                        $sql3.= "AND q.attempt <= '".$quizAttemps['attempts']."'";
                }
                $sql3.= " AND q.sumgrades >= '".(($quizAttemps['sumgrades']*80)/100)."'
                AND q.quiz = '".$idInstance."'
                ORDER BY q.id DESC
                ";

                //echo $sql3;

                $retScorm3 = $dblink->get_row($sql3, true);

                //print_r($retScorm3);

                if(!empty($retScorm3)){
                    return true; //quiz
                }else{
                    return false;
                }
        		
            }
        }
        
    }