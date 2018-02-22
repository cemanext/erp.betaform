<?php
include_once('../../config/connDB.php');
include_once(BASE_ROOT . 'config/confAccesso.php');

include_once(BASE_ROOT . 'moduli/preventivi/funzioni.php');

$browser = strpos($_SERVER['HTTP_USER_AGENT'], "iPhone");
if ($browser == true) {
    //echo 'Code You Want To Execute';
}

if (isset($_GET['idA'])) {
    $id_area = $_GET['idA'];
} else {
    $id_area = 0;
}

if (isset($_GET['idPrev'])) {

    creaPreventivoPDF($_GET['idPrev'], false);

    if (isset($_SESSION['email_utente'])) {
        $mitt = $_SESSION['email_utente'];
    } else {
        $mitt = MAIL_DA_INVIA_PREVENTIVO;
    }

    $sql = "SELECT codice FROM lista_preventivi WHERE id='" . $_GET['idPrev'] . "'";
    list($codice) = $dblink->get_row($sql);

    $n_progetto = str_replace("/", "-", $codice);
    $filename = PREFIX_FILE_PDF_PREVENTIVO . $_GET['idPrev'] . ".pdf";
    $filename_oggetto = PREFIX_MAIL_OGGETTO_INIVA_PREVENTIVO . $_GET['idPrev'] . "";

    $id_Preventivo = $_GET['idPrev'];

    $sql_prev = "SELECT email, id_calendario FROM lista_professionisti INNER JOIN lista_preventivi ON lista_professionisti.id=lista_preventivi.id_professionista WHERE lista_preventivi.id='" . $id_Preventivo . "'";
    list($emailDesti, $id_calendario) = $dblink->get_row($sql_prev);

//echo '<h1>$emailDesti = '.$emailDesti.'</h1>';

    if (strlen($emailDesti) <= 1) {

        $sql_prev = "SELECT id_calendario FROM lista_preventivi WHERE id='" . $id_Preventivo . "'";
        list($id_calendario) = $dblink->get_row($sql_prev);

//echo '<h1>$id_calendario = '.$id_calendario.'</h1>';

        $sql_prev_cal = "SELECT campo_5 FROM calendario WHERE id='" . $id_calendario . "'";
        list($emailDesti) = $dblink->get_row($sql_prev_cal);
    }

//echo '<h1>$emailDesti = '.$emailDesti.'</h1>';
    $dest = $emailDesti;
    $dest_cc = '';
    $dest_bcc = '';
    $ogg = MAIL_OGGETTO_INVIA_PREVENTIVO;
    $mess = MAIL_TESTO_INVIA_PREVENTIVO;
}
?>
<form action="salva.php?fn=inviaEmailPreventivo" method="post" enctype="multipart/form-data" class="form">
    <div class="modal-body">
        <h3 class="form-section">Invia Preventivo </h3>
        <div class="row" style="margin-bottom:10px;">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-addon" style="background-color: #fff;"><i class="fa fa-user font-grey-mint"></i></span>
                    <input name="mitt" id="mitt" type="text" class="form-control tooltips" placeholder="Mittente" value="<?php echo $mitt; ?>" data-container="body" data-placement="top" data-original-title="MITTENTE"></div></div>
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-addon" style="background-color: #fff;"><i class="fa fa-user font-grey-mint"></i></span>
                    <input name="dest" id="dest" type="text" class="form-control tooltips" placeholder="Destinatario" value="<?php echo $dest; ?>" data-container="body" data-placement="top" data-original-title="DESTINATARIO"></div></div>
        </div>
        <div class="row" style="margin-bottom:10px;">
            <div class="col-md-12">
                <div class="input-group">
                    <span class="input-group-addon" style="background-color: #fff;"><i class="fa fa-user font-grey-mint"></i></span>
                    <input name="dest_cc" id="dest_cc" type="text" class="form-control tooltips" placeholder="CC" value="<?php echo $dest_cc; ?>" data-container="body" data-placement="top" data-original-title="CC"></div></div>
        </div>
        <div class="row" style="margin-bottom:10px;">
            <div class="col-md-12">
                <div class="input-group">
                    <span class="input-group-addon" style="background-color: #fff;"><i class="fa fa-user font-grey-mint"></i></span>
                    <input name="ogg" id="ogg" type="text" class="form-control tooltips" placeholder="Oggetto" value="<?php echo $ogg . str_replace('_', ' ', str_replace('.pdf', '', $filename_oggetto)) . ''; ?>" data-container="body" data-placement="top" data-original-title="OGGETTO"></div></div>
        </div>
        <div class="row" style="margin-bottom:10px;">
            <div class="col-md-9">
                <div class="mt-checkbox-inline">
                    <label class="mt-checkbox font-blue-steel">
                        <input type="checkbox" id="fileDoc" name="fileDoc" value="<?php echo $filename; ?>"> <?php echo $filename; ?>
                        <input type="HIDDEN" VALUE="<?php echo $id_Preventivo; ?>" NAME="id_preventivo">
                        <span></span>
                    </label>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
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
                    <div class="btn-set pull-left">
                    </div>
                </div>
            </div>
            <div class="row" style="margin-bottom:10px;">
                <div class="form-group">
                    <div class="col-md-12">
                        <textarea id="mess" name="mess" class="wysihtml5 form-control" rows="6"><?php echo $mess; ?></textarea>
                    </div>
                </div>
            </div>

        </div>
        <div class="modal-footer">
            <button type="button" data-dismiss="modal" class="btn dark btn-outline">Annulla</button>
            <button type="submit" name="Invia" value="Invia" class="btn green">Invia</button>
        </div>
</form>
<script type="text/javascript">
    $(document).ready(function () {
        ComponentsEditors.init();
    });
</script>
