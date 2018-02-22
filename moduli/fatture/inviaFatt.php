<?php
include_once('../../config/connDB.php');
include_once(BASE_ROOT . 'config/confAccesso.php');

$browser = strpos($_SERVER['HTTP_USER_AGENT'], "iPhone");
if ($browser == true) {
    //echo 'Code You Want To Execute';
}

if (isset($_GET['idA'])) {
    $id_area = $_GET['idA'];
} else {
    $id_area = 0;
}

if (isset($_GET['idFatt'])) {
    if (strlen($_SESSION['passwd_email_utente']) > 2 && strpos($_SESSION['email_utente'], DOMINIO_MAIL_AUTENTICATE) > 0) {
        $mitt = $_SESSION['email_utente'];
    } else {
        $mitt = MAIL_DA_INVIA_FATTURA;
    }

    creaFatturaPDF($_GET['idFatt'], false);

    $sql = "SELECT * FROM lista_fatture WHERE id='" . $_GET['idFatt'] . "'";
    $row = $dblink->get_row($sql, true);

    $n_progetto = str_replace("/", "-", $row['codice']);
    $filename = PREFIX_FILE_PDF_FATTURA . $n_progetto . "-" . $row['sezionale'] . ".pdf";
    $filename_oggetto = PREFIX_MAIL_OGGETTO_INIVA_FATTURA . $row['codice'] . SEPARATORE_FATTURA . $row['sezionale'] . "";
    $causale = $row['causale'];

    $id_Fattura = $_GET['idFatt'];

    $sql_prev = "SELECT email FROM lista_aziende INNER JOIN lista_fatture ON lista_aziende.id=lista_fatture.id_azienda WHERE lista_fatture.id='" . $id_Fattura . "'";
    $emailDesti = $dblink->get_field($sql_prev);

    $dest = $emailDesti;
    $dest_cc = '';
    $dest_bcc = '';
    $ogg = MAIL_OGGETTO_INVIA_FATTURA;
    $mess = MAIL_TESTO_INVIA_FATTURA;
}
?>
<form action="salva.php?fn=inviaEmailFattura" method="post" enctype="multipart/form-data" class="form">
    <div class="modal-body">
        <h3 class="form-section">Invia Fattura </h3>
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
                        <input type="checkbox" id="fileDoc" name="fileDoc" value="<?php echo $filename; ?>" checked> <?php echo $filename; ?>
                        <input type="HIDDEN" VALUE="<?php echo $id_fattura; ?>" NAME="id_fattura">
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
