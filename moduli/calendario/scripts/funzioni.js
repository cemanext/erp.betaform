/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var BASE_URL_HOST = location.protocol+"//"+window.location.hostname+"";

function scriviNomeObiezioneInCalendario(selettore){
    
    var id = selettore.id;

    var temp = new Array();
    var valori = $("#"+id).find(':selected').data("options");
    temp = valori.split(":");

    $("#txt_id_calendario").val(temp[0]);
    $("#txt_id_preventivo").val(temp[1]);
    $("#txt_id_obiezione").val(temp[2]);
    
    $("#formSalvaNomeObiezioneInCalendario").submit();
}

$(document).ready(function() {
    
    BASE_URL_HOST = location.protocol+"//"+window.location.hostname+"";
    
    toastr.options = {
        "closeButton": false,
        "debug": false,
        "newestOnTop": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": 3000,
        "extendedTimeOut": 0,
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut",
        "tapToDismiss": false
    }
    
    var urlReferer = get_referer();
    
    if($.urlParam('res')=="0"){
        toastr.error("Si è verificato un errore nella presa in carico della richiesta.","Errore nella Presa in Carico");
        pulisciRefereStorico();
    }
    
    if($.urlParam('res')=="4"){
        toastr.success("Le richieste selezionate sono state associate!","Richiesta Associate");
        pulisciRefereStorico();
    }
    
    if($.urlParam('res')=="5"){
        toastr.success("Hai traferito questa rischiesta correttamente!","Richiesta Trasferita");
        pulisciRefereStorico();
    }
    
    $('#myModal').on('shown.bs.modal', function () {
        $('#idFromCommerciale #id_commerciale').focus();
    });
    
    $("#myModal #okButton").on( "click", function(event) {
        event.preventDefault();
        var posting = jQuery.post( BASE_URL_HOST+"/moduli/calendario/salva.php?fn=inserisciCommerciale" , jQuery( "#idFromCommerciale" ).serializeArray() );
        posting.done(function(data) {
            $("#myModal").modal('hide');     // dismiss the dialog
            location.reload();
            //alert( "Data Loaded: " + data );
        }).fail(function() {
            alert("Impossibile associare agente");
            $("#myModal").modal('hide'); 
        });
        //alert("button pressed");   // just as an example...
        
    });
    
    $("#myModal #annullaButton").on( "click", function(event) {
        event.preventDefault();
        $("#myModal .form-body #txt_id_calendario").remove();
        $("#myModal .form-body #txt_id_professionista").remove();
        $("#myModal").modal('hide');     // dismiss the dialog
    });
    
    $("#myModalPrendiInCarico #okButtonPrendiInCarico").on( "click", function(event) {
        event.preventDefault();
        var posting = jQuery.get( BASE_URL_HOST+"/moduli/calendario/salva.php?fn=prendiInCaricoRichiesta" , jQuery( "#idFromPrendiInCaricoCommerciale" ).serializeArray() );
        posting.done(function(data) {
            $("#myModalPrendiInCarico").modal('hide');     // dismiss the dialog
            location.reload();
            //alert( "Data Loaded: " + data );
        }).fail(function() {
            alert("Impossibile associare agente");
            $("#myModalPrendiInCarico").modal('hide'); 
        });
        //alert("button pressed");   // just as an example...
        
    });
    
    $("#myModalPrendiInCarico #annullaButtonPrendiInCarico").on( "click", function(event) {
        event.preventDefault();
        $("#myModalPrendiInCarico .form-body #id").remove();
        $("#myModalPrendiInCarico .form-body #idProf").remove();
        $("#myModalPrendiInCarico .form-body #idAgenteOld").remove();
        $("#myModalPrendiInCarico .form-body #idAgenteNew").remove();
        $("#myModalPrendiInCarico").modal('hide');     // dismiss the dialog
    });
    
    $("#cercaProfessionista").on( "click", function(event) {
        
        event.preventDefault();
        
        $("#formNuovaRichiestaCalendario input#calendario_txt_id_professionista").val("0");
        $("#formNuovaRichiestaCalendario input#calendario_txt_campo_3").val("");
        $("#formNuovaRichiestaCalendario input#calendario_txt_campo_1").val("");
        $("#formNuovaRichiestaCalendario input#calendario_txt_campo_2").val("");
        $("#formNuovaRichiestaCalendario input#calendario_txt_campo_4").val("");
        $("#formNuovaRichiestaCalendario input#calendario_txt_campo_5").val("");
        $("#myModalCercaProfessionista input#cerca_professionista").val('');
        $("#formNuovaRichiestaCalendario div#alertNuovaRichiesta").empty();
        
        $("#myModalCercaProfessionista").modal({   // wire up the actual modal functionality and show the dialog
            "backdrop"  : "static",
            "keyboard"  : true,
            "show"      : true                     // ensure the modal is shown immediately
        });
        
    });
    
    $('#myModalCercaProfessionista').on('shown.bs.modal', function () {
        $('#myModalCercaProfessionista #cerca_professionista').focus();
    });
    
    var alertDivOpen = '<div id="alertContentNuovaRichiesta">\n';
    var alertDivClose = '</div>\n';
    
    var cercaProfessionista = new Bloodhound({
        datumTokenizer: function(d) { return d.tokens; },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: BASE_URL_HOST+'/moduli/calendario/salva.php?fn=cercaProfessionista&key_search=%QUERY',
            wildcard: '%QUERY'
          }
      });
      
      cercaProfessionista.initialize();

      $('input#cerca_professionista').typeahead(null, {
        name: 'cerca_professionista',
        displayKey: function(data) {
            return data.ragione_sociale;        
        },
        limit : 100,
        source: cercaProfessionista.ttAdapter()
      }).bind('typeahead:select', function(ev, data) {
            $("#formNuovaRichiestaCalendario div#alertNuovaRichiesta").empty();
            var alertDivData = '';
            var richiestaTrovata = false;
            $.each(data.avviso, function(key, element) {
                richiestaTrovata = true;
                //alert('COMMERCIALE SESSIONE: '+data.id_agente + ' => ' + 'ID AGENTE: ' + element.id_agente);
                if(data.id_agente == element.id_agente || !data.id_agente){
                    alertDivData = alertDivData + '<div class="note note-warning">\n';
                }else{
                    alertDivData = alertDivData + '<div class="note note-danger">\n';
                }
                alertDivData = alertDivData + '<h4 class="block">Richiesta aperta n.' + (key+1) + '</h3>\n';
                alertDivData = alertDivData + '<p>\n';
                alertDivData = alertDivData + '<b>Richiesta:</b> ' + element.oggetto + '&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;\n';
                alertDivData = alertDivData + '<b>Data:</b> ' + element.datainsert + '&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;\n';
                alertDivData = alertDivData + '<b>Agente associato:</b> ' + element.destinatario + '&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;\n';
                alertDivData = alertDivData + '<b>Stato:</b> ' + element.stato + '\n';
                alertDivData = alertDivData + '</p>\n';
                if(data.livello_agente=="commerciale"){
                    if(data.id_agente == element.id_agente || !data.id_agente){
                        alertDivData = alertDivData + '<p style="text-align: right;">\n';
                        alertDivData = alertDivData + '<a class="btn yellow-gold" href="../anagrafiche/dettaglio_tab.php?tbl=lista_professionisti&id='+data.id_professionista+'"> Gestisci Richiesta </a>\n';
                        alertDivData = alertDivData + '</p>\n';
                    }else{
                        alertDivData = alertDivData + '<p style="text-align: right;">\n';
                        alertDivData = alertDivData + '<a class="btn purple-studio" href="salva.php?fn=prendiInCaricoRichiesta&id='+element.id+'&idAgenteNew='+data.id_agente+'&idAgenteOld='+element.id_agente+'&idProf='+data.id_professionista+'"> Prendi in Carico </a>\n';
                        alertDivData = alertDivData + '</p>\n';
                    }
                }else{
                    alertDivData = alertDivData + '<p style="text-align: right;">\n';
                    alertDivData = alertDivData + '<a class="btn yellow-gold" href="../anagrafiche/dettaglio_tab.php?tbl=lista_professionisti&id='+data.id_professionista+'"> Gestisci Richiesta </a>\n';
                    alertDivData = alertDivData + '</p>\n';
                }
                alertDivData = alertDivData + '</div>\n';
            });
            
            if(data.id_professionista > 0 && !richiestaTrovata){
                alertDivData = alertDivData + '<div class="note note-info">\n';
                alertDivData = alertDivData + '<h4 class="block">Professionista Trovato: ' + data.nome + ' ' + data.cognome + '</h3>\n';
                alertDivData = alertDivData + '<p>\n';
                alertDivData = alertDivData + '<b>Attenzione:</b> procedendo con l\'inserimento della richiesta sarà associata a questo professionista.\n';
                alertDivData = alertDivData + '<p>\n';
                alertDivData = alertDivData + '<b>Nome e Cognome:</b> ' + data.nome + ' ' + data.cognome + '&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;\n';
                alertDivData = alertDivData + '<b>C.F.:</b> ' + data.codice_fiscale + '\n';
                alertDivData = alertDivData + '</p>\n';
                alertDivData = alertDivData + '</p>\n';
                alertDivData = alertDivData + '</div>\n';
            }
            
            //alert('AVVISO: '+print_r(data.avviso[0]));
            $("#formNuovaRichiestaCalendario input#calendario_txt_id_professionista").val(data.id_professionista);
            $("#formNuovaRichiestaCalendario input#calendario_txt_campo_3").val(data.codice_fiscale);
            $("#formNuovaRichiestaCalendario input#calendario_txt_campo_1").val(data.nome);
            $("#formNuovaRichiestaCalendario input#calendario_txt_campo_2").val(data.cognome);
            $("#formNuovaRichiestaCalendario input#calendario_txt_campo_4").val(data.telefono);
            $("#formNuovaRichiestaCalendario input#calendario_txt_campo_5").val(data.email);
            if(alertDivData.length > 1){
                $("#formNuovaRichiestaCalendario div#alertNuovaRichiesta").append(alertDivOpen+alertDivData+alertDivClose);
            }
            //alert('Selection: ' + data.ragione_sociale);
      });
    
    $("#myModalCercaProfessionista #okButtonCercaProfessionista").on( "click", function(event) {
        event.preventDefault();
        var codice_fiscale = $("#myModalCercaProfessionista input#cerca_professionista").val();
        var id_professionista = $("#formNuovaRichiestaCalendario input#calendario_txt_id_professionista").val();
        var alertDivData = "";
        if(id_professionista<=0){
            if(controllaCF(codice_fiscale)){
                $("#formNuovaRichiestaCalendario input#calendario_txt_campo_3").val(codice_fiscale);
                    alertDivData = alertDivData + '<div class="note note-success">\n';
                    alertDivData = alertDivData + '<h4 class="block">Inserimento nuova richiesta!</h3>\n';
                    alertDivData = alertDivData + '<p>\n';
                    alertDivData = alertDivData + '<b>Inserimento nuovo richiesta:</b> procedendo con l\'inserimento della richiesta si inserirà in automatico un nuovo professionista.\n';
                    alertDivData = alertDivData + '<p>\n';
                    alertDivData = alertDivData + '<b>C.F.:</b> ' + codice_fiscale + '\n';
                    alertDivData = alertDivData + '</p>\n';
                    alertDivData = alertDivData + '</p>\n';
                    alertDivData = alertDivData + '</div>\n';
                if(alertDivData.length > 1){
                    $("#formNuovaRichiestaCalendario div#alertNuovaRichiesta").append(alertDivOpen+alertDivData+alertDivClose);
                }
            }else{
                toastr.warning("Il codice fiscale inserito "+codice_fiscale+" non è corretto.","Formato Codice Fiscale inesatto!");
            }
        }
        $("#myModalCercaProfessionista input#cerca_professionista").val('');
        $("#myModalCercaProfessionista").modal('hide');
    });
    
    $("#myModalCercaProfessionista #annullaButtonCercaProfessionista, #cancellaProfessionista").on( "click", function(event) {
        event.preventDefault();
        $("#formNuovaRichiestaCalendario input#calendario_txt_id_professionista").val("0");
        $("#formNuovaRichiestaCalendario input#calendario_txt_campo_3").val("");
        $("#formNuovaRichiestaCalendario input#calendario_txt_campo_1").val("");
        $("#formNuovaRichiestaCalendario input#calendario_txt_campo_2").val("");
        $("#formNuovaRichiestaCalendario input#calendario_txt_campo_4").val("");
        $("#formNuovaRichiestaCalendario input#calendario_txt_campo_5").val("");
        $("#myModalCercaProfessionista input#cerca_professionista").val('');
        $("#formNuovaRichiestaCalendario div#alertNuovaRichiesta").empty();
        $("#myModalCercaProfessionista").modal('hide');     // dismiss the dialog
    });
    
    /*$("#myModal").on("hide", function() {    // remove the event listeners when the dialog is dismissed
        $("#myModal a.btn").off("click");
    });*/
    
    /*$("#myModal").on("hidden", function() {  // remove the actual elements from the DOM when fully hidden
        $("#myModal").remove();
    });*/
    
    $("#chiudiNegativo").on( "click", function(event) {
        
        event.preventDefault();
        
        $("#myModalChiudiNegativo").modal({          // wire up the actual modal functionality and show the dialog
            "backdrop"  : "static",
            "keyboard"  : true,
            "show"      : true                     // ensure the modal is shown immediately
        });
        
    }); 
    
    $("#myModalChiudiNegativo #okButtonChiudiNegativo").on( "click", function(event) {
        event.preventDefault();
        
        var saveId = "";
        var numCheck = ($('input:checkbox').length-1);
        for (i = 0; i < numCheck; i++) { 
            if ($('#txt_checkbox_'+i+'').is(':checked')) {
                if(saveId.length > 0){
                    saveId = saveId+":"+$('#txt_checkbox_'+i+'').val();
                }else{
                    saveId = saveId+$('#txt_checkbox_'+i+'').val();
                }
            }
        }
        
        if(saveId.length > 0){
        
            var valoreObiezione = $("#idFromChiudiNegativo #idObiezione").val();
            $("#idFromChiudiNegativo #idCal").val(saveId);
        
            if(valoreObiezione > 0){
                var posting = jQuery.post( BASE_URL_HOST+"/moduli/calendario/salva.php?fn=chiudiNegativo" , jQuery( "#idFromChiudiNegativo" ).serializeArray() );
                posting.done(function(data) {
                    var tmp = data.split(':');
                    $("#myModalChiudiNegativo").modal('hide');     // dismiss the dialog
                    location.href = urlReferer + "&res=1";
                    //alert( "Data Loaded: " + data );
                }).fail(function() {
                    toastr.alert("Impossibile trasferire la richiesta!", "Errore");
                    $("#myModalChiudiNegativo").modal('hide'); 
                });
            }else{
                alert('Valore obiezione obbligatorio!');    
            }
        }else{
            toastr.warning("Nessuna richiesta selezionata!", "Selezionare richieste");
            $("#myModalChiudiNegativo #idCal").val('');
            $("#myModalChiudiNegativo").modal('hide'); 
        }
    });
    
    $("#myModalChiudiNegativo #annullaButtonChiudiNegativo").on( "click", function(event) {
        event.preventDefault(); 
        $("#myModalChiudiNegativo #idCal").val('');
        $("#myModalChiudiNegativo").modal('hide');     // dismiss the dialog
    });
    
    $('#myModalChiudiNegativo').on('shown.bs.modal', function () {
        $('#idFromChiudiNegativo #idObiezione').focus();
    });
     
    
    $('#myModalAssociaCommerciale').on('shown.bs.modal', function () {
        $('#idFromCommercialeMultiplo #id_commerciale').focus();
    });
    
    $("#associaCommerciale").on( "click", function(event) {
        
        event.preventDefault();
        
        $("#myModalAssociaCommerciale").modal({          // wire up the actual modal functionality and show the dialog
            "backdrop"  : "static",
            "keyboard"  : true,
            "show"      : true                     // ensure the modal is shown immediately
        });
        
    }); 
    
    $("#myModalAssociaCommerciale #okButton").on( "click", function(event) {
        
        event.preventDefault();
        var saveId = "";
        var numCheck = ($('input:checkbox').length-1);
        for (i = 0; i < numCheck; i++) { 
            if ($('#txt_checkbox_'+i+'').is(':checked')) {
                if(saveId.length > 0){
                    saveId = saveId+":"+$('#txt_checkbox_'+i+'').val();
                }else{
                    saveId = saveId+$('#txt_checkbox_'+i+'').val();
                }
            }
        }
        
        if(saveId.length > 0){
            
            $("#myModalAssociaCommerciale #idCal").val(saveId);
        
            var posting = jQuery.post( BASE_URL_HOST+"/moduli/calendario/salva.php?fn=associaCommerciale" , jQuery( "#idFromCommercialeMultiplo" ).serializeArray() );
            posting.done(function(data) {

                var str = data.replace(/^\s+|\s+$/g, '');
                var res = str.split(":");
                
                if(res[0] === "OK"){
                    $("#myModalAssociaCommerciale #idCal").val('');
                    $("#myModalAssociaCommerciale").modal('hide');     // dismiss the dialog
                    window.location.href=urlReferer+"&res=4";
                }else if(res[0] === "KO2"){
                    toastr.warning("Nessun commerciale e/o richiesta selezionata!", "Selezionare richieste");
                    $("#myModalAssociaCommerciale #idCal").val('');
                    $("#myModalAssociaCommerciale").modal('hide'); 
                }else{
                    toastr.warning("Non è stato possibile completare il processo.", "Attenzione");
                    //alert("Non è stato torvato nessun professionista corrispondente al dato inserito.");
                    $("#myModalAssociaCommerciale #idCal").val('');

                    $("#myModalAssociaCommerciale").modal('hide');     // dismiss the dialog
                }
            }).fail(function() {
                toastr.error("Errore nel processo di associazione del commerciale", "Errore");
                $("#myModalAssociaCommerciale #idCal").val('');
                $("#myModalAssociaCommerciale").modal('hide'); 
            });
        }else{
            toastr.warning("Nessuna richiesta selezionata!", "Selezionare richieste");
            $("#myModalAssociaCommerciale #idCal").val('');
            $("#myModalAssociaCommerciale").modal('hide'); 
        }
    });
    
    $("#myModalAssociaCommerciale #annullaButton").on( "click", function(event) {
        event.preventDefault(); 
        $("#myModalAssociaCommerciale #idCal").val('');
        $("#myModalAssociaCommerciale").modal('hide');     // dismiss the dialog
    });
    
    $('#txt_checkbox_all').change(function(){
        var numCheck = ($('input:checkbox').length-1);
        for (i = 0; i < numCheck; i++) { 
            if ($('#txt_checkbox_'+i+'').is(':checked')) {
                $('#txt_checkbox_'+i+'').prop('checked',false);
            } else {
                $('#txt_checkbox_'+i+'').prop('checked',true);
            }
        }
    });
    
    $('#myModalAssociaProdotti').on('shown.bs.modal', function () {
        $('#idFromProdottoMultiplo #id_prodotto').focus();
    });
    
    $("#associaProdotti").on( "click", function(event) {
        
        event.preventDefault();
        
        $("#myModalAssociaProdotti").modal({          // wire up the actual modal functionality and show the dialog
            "backdrop"  : "static",
            "keyboard"  : true,
            "show"      : true                     // ensure the modal is shown immediately
        });
        
    }); 
    
    $("#myModalAssociaProdotti #okButton").on( "click", function(event) {
        
        event.preventDefault();
        var saveId = "";
        var numCheck = ($('input:checkbox').length-1);
        for (i = 0; i < numCheck; i++) { 
            if ($('#txt_checkbox_'+i+'').is(':checked')) {
                if(saveId.length > 0){
                    saveId = saveId+":"+$('#txt_checkbox_'+i+'').val();
                }else{
                    saveId = saveId+$('#txt_checkbox_'+i+'').val();
                }
            }
        }
        
        if(saveId.length > 0){
            
            $("#myModalAssociaProdotti #idCal").val(saveId);
        
            var posting = jQuery.post( BASE_URL_HOST+"/moduli/calendario/salva.php?fn=associaProdotti" , jQuery( "#idFromProdottoMultiplo" ).serializeArray() );
            posting.done(function(data) {

                var str = data.replace(/^\s+|\s+$/g, '');
                var res = str.split(":");
                
                if(res[0] === "OK"){
                    $("#myModalAssociaProdotti #idCal").val('');
                    $("#myModalAssociaProdotti").modal('hide');     // dismiss the dialog
                    window.location.href=urlReferer+"&res=4";
                }else if(res[0] === "KO2"){
                    toastr.warning("Nessun prodotto e/o richiesta selezionata!", "Selezionare richieste");
                    $("#myModalAssociaProdotti #idCal").val('');
                    $("#myModalAssociaProdotti").modal('hide'); 
                }else{
                    toastr.warning("Non è stato possibile completare il processo.", "Attenzione");
                    //alert("Non è stato torvato nessun professionista corrispondente al dato inserito.");
                    $("#myModalAssociaProdotti #idCal").val('');

                    $("#myModalAssociaProdotti").modal('hide');     // dismiss the dialog
                }
            }).fail(function() {
                toastr.error("Errore nel processo di associazione del prodotto", "Errore");
                $("#myModalAssociaProdotti #idCal").val('');
                $("#myModalAssociaProdotti").modal('hide'); 
            });
        }else{
            toastr.warning("Nessuna richiesta selezionata!", "Selezionare richieste");
            $("#myModalAssociaProdotti #idCal").val('');
            $("#myModalAssociaProdotti").modal('hide'); 
        }
    });
    
    $("#myModalAssociaProdotti #annullaButton").on( "click", function(event) {
        event.preventDefault(); 
        $("#myModalAssociaProdotti #idCal").val('');
        $("#myModalAssociaProdotti").modal('hide');     // dismiss the dialog
    });
    
    $("#cancellaRicarcaTabella").on( "click", function(event) {
        event.preventDefault(); 
        var table = $('#datatable_ajax').DataTable();
        table.state.clear();
        table.destroy();
        TableDatatablesAjaxCalendario.init();
    });
    
    ComponentsSelectCommerciale.init();
    ComponentsSelectProdotto.init();
    ComponentsSelectCampagna.init();
    ComponentsMultiselectCampagneHome.init();
    TableDatatablesAjaxCalendario.init();
    FormValCalendario.init();
});

function associaCommercialeARichesta(idCalendario, idProfessionista){
    
     $("#myModal .form-body").prepend("<input type=\"hidden\" name=\"txt_id_calendario\" id=\"txt_id_calendario\" value=\""+idCalendario+"\">");
     $("#myModal .form-body").prepend("<input type=\"hidden\" name=\"txt_id_professionista\" id=\"txt_id_professionista\" value=\""+idProfessionista+"\">");
    
    $("#myModal").modal({                    // wire up the actual modal functionality and show the dialog
      "backdrop"  : "static",
      "keyboard"  : true,
      "show"      : true                     // ensure the modal is shown immediately
    });
}

function prendiInCaricoRichesta(idCalendario, idProfessionista, idAgenteOld, idAgenteNew){
    
     $("#myModalPrendiInCarico .form-body").prepend("<input type=\"hidden\" name=\"id\" id=\"id\" value=\""+idCalendario+"\">");
     $("#myModalPrendiInCarico .form-body").prepend("<input type=\"hidden\" name=\"idProf\" id=\"idProf\" value=\""+idProfessionista+"\">");
     $("#myModalPrendiInCarico .form-body").prepend("<input type=\"hidden\" name=\"idAgenteOld\" id=\"idAgenteOld\" value=\""+idAgenteOld+"\">");
     $("#myModalPrendiInCarico .form-body").prepend("<input type=\"hidden\" name=\"idAgenteNew\" id=\"idAgenteNew\" value=\""+idAgenteNew+"\">");
    
    $("#myModalPrendiInCarico").modal({                    // wire up the actual modal functionality and show the dialog
      "backdrop"  : "static",
      "keyboard"  : true,
      "show"      : true                     // ensure the modal is shown immediately
    });
}

var TableDatatablesAjaxCalendario = function () {

        var initTableAjax1 = function () {
            
            //$.fn.dataTable.moment('DD-MM-YYYY');
            
            var table = $('#datatable_ajax');

            if($.urlParam('whrStato') == "a7d7ab5bee5f267d23e0ff28a162bafb" || $.urlParam('whrStato') == "c41dc3146e903da07fcdf8a00dd9d446"){
                var orderNum = 6;
            }else{
                var orderNum = 7;
            }

            oTable = table.dataTable({
                // Internationalisation. For more info refer to http://datatables.net/manual/i18n

                "processing": true,
                "serverSide": true,

                // Or you can use remote translation file
                "language": {
                   url: '//cdn.datatables.net/plug-ins/1.10.12/i18n/Italian.json'
                },

                // setup buttons extentension: http://datatables.net/extensions/buttons/
                buttons: [
                  { extend: 'print', className: 'btn white btn-outline' },
                  { extend: 'copy', className: 'btn white btn-outline' },
                  { extend: 'pdf', className: 'btn white btn-outline' },
                  { extend: 'excel', className: 'btn white btn-outline ' },
                  { extend: 'csv', className: 'btn white btn-outline ' }
                ],

                // setup responsive extension: http://datatables.net/extensions/responsive/
                "responsive": false,
                "stateSave": true,

                "ajax": {
                    "url": BASE_URL_HOST+"/moduli/calendario/scripts/server_processing.php?whrStato="+$.urlParam('whrStato'), // ajax source
                },

                //"ordering": true,
                "orderMulti": true,
                
                "order": [
                    [orderNum, 'asc'],
                    [(orderNum+1), 'asc'],
                ],

                "lengthMenu": [
                    [10, 25, 30, 50, 100, 250, -1],
                    [10, 25, 30, 50, 100, 250, 'Tutti'] // change per page values here
                ],
                // set the initial value
                "pageLength": 50,

                "dom": "<'row' <'col-md-12'B>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>", // horizobtal scrollable datatable

                "columnDefs": [
                    {"className": "dt-center", "targets": "_all"},
                    {"orderable": false, "targets": [ 0],}
                ],

                // Uncomment below line("dom" parameter) to fix the dropdown overflow issue in the datatable cells. The default datatable layout
                // setup uses scrollable div(table-scrollable) with overflow:auto to enable vertical scroll(see: assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js).
                // So when dropdowns used the scrollable div should be removed.
                //"dom": "<'row' <'col-md-12'T>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r>t<'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",
            });
        }

        return {

            //main function to initiate the module
            init: function () {

                initTableAjax1();
            }
        };

    }();

var ComponentsSelectCommerciale = function() {

    var handleSelectCommerciale = function() {

        // Set the "bootstrap" theme as the default theme for all Select2
        // widgets.
        //
        // @see https://github.com/select2/select2/issues/2927
        $.fn.select2.defaults.set("theme", "bootstrap");

        var placeholder = "Seleziona Commerciale";

        $(".select_commerciale, .select_commerciale-multiple").select2({
            placeholder: placeholder,
            width: null
        });

        $(".select_commerciale-allow-clear").select2({
            allowClear: true,
            placeholder: placeholder,
            width: null
        });

        $("button[data-select2-open]").click(function() {
            $("#" + $(this).data("select2-open")).select2("open");
        });

        $(":checkbox").on("click", function() {
            $(this).parent().nextAll("select").prop("disabled", !this.checked);
        });

        // copy Bootstrap validation states to Select2 dropdown
        //
        // add .has-waring, .has-error, .has-succes to the Select2 dropdown
        // (was #select2-drop in Select2 v3.x, in Select2 v4 can be selected via
        // body > .select2-container) if _any_ of the opened Select2's parents
        // has one of these forementioned classes (YUCK! ;-))
        $(".select_commerciale, .select_commerciale-multiple, .select_commerciale-allow-clear").on("select2:open", function() {
            if ($(this).parents("[class*='has-']").length) {
                var classNames = $(this).parents("[class*='has-']")[0].className.split(/\s+/);

                for (var i = 0; i < classNames.length; ++i) {
                    if (classNames[i].match("has-")) {
                        $("body > .select2-container").addClass(classNames[i]);
                    }
                }
            }
        });

        $(".js-btn-set-scaling-classes").on("click", function() {
            $("#select2-multiple-input-sm, #select2-single-input-sm").next(".select2-container--bootstrap").addClass("input-sm");
            $("#select2-multiple-input-lg, #select2-single-input-lg").next(".select2-container--bootstrap").addClass("input-lg");
            $(this).removeClass("btn-primary btn-outline").prop("disabled", true);
        });
    }

    return {
        //main function to initiate the module
        init: function() {
            handleSelectCommerciale();
        }
    };

}();

var ComponentsSelectProdotto = function() {

    var handleSelectProdotto = function() {

        // Set the "bootstrap" theme as the default theme for all Select2
        // widgets.
        //
        // @see https://github.com/select2/select2/issues/2927
        $.fn.select2.defaults.set("theme", "bootstrap");

        var placeholder = "Seleziona Prodotto";

        $(".select_prodotto, .select_prodotto-multiple").select2({
            placeholder: placeholder,
            width: null
        });

        $(".select_prodotto-allow-clear").select2({
            allowClear: true,
            placeholder: placeholder,
            width: null
        });

        $("button[data-select2-open]").click(function() {
            $("#" + $(this).data("select2-open")).select2("open");
        });

        $(":checkbox").on("click", function() {
            $(this).parent().nextAll("select").prop("disabled", !this.checked);
        });

        // copy Bootstrap validation states to Select2 dropdown
        //
        // add .has-waring, .has-error, .has-succes to the Select2 dropdown
        // (was #select2-drop in Select2 v3.x, in Select2 v4 can be selected via
        // body > .select2-container) if _any_ of the opened Select2's parents
        // has one of these forementioned classes (YUCK! ;-))
        $(".select_prodotto, .select_prodotto-multiple, .select_prodotto-allow-clear").on("select2:open", function() {
            if ($(this).parents("[class*='has-']").length) {
                var classNames = $(this).parents("[class*='has-']")[0].className.split(/\s+/);

                for (var i = 0; i < classNames.length; ++i) {
                    if (classNames[i].match("has-")) {
                        $("body > .select2-container").addClass(classNames[i]);
                    }
                }
            }
        });

        $(".js-btn-set-scaling-classes").on("click", function() {
            $("#select2-multiple-input-sm, #select2-single-input-sm").next(".select2-container--bootstrap").addClass("input-sm");
            $("#select2-multiple-input-lg, #select2-single-input-lg").next(".select2-container--bootstrap").addClass("input-lg");
            $(this).removeClass("btn-primary btn-outline").prop("disabled", true);
        });
    }

    return {
        //main function to initiate the module
        init: function() {
            handleSelectProdotto();
        }
    };

}();

var FormValCalendario = function () {
            
    // validation using icons
    var handleValCalendario = function() {
        // for more info visit the official plugin documentation: 
            // http://docs.jquery.com/Plugins/Validation

            var form2 = $('#formNuovaRichiestaCalendario');
            var error2 = $('.alert-danger', form2);
            var success2 = $('.alert-success', form2);

            form2.validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "",  // validate all fields including form hidden input
                rules: {
                    calendario_txt_campo_1: {
                        minlength: 2,
                        required: true
                    },
                    calendario_txt_campo_2: {
                        minlength: 2,
                        required: true
                    },
                    calendario_txt_campo_5: {
                        required: true,
                        email: true
                    },
                    calendario_txt_campo_4: {
                        required: false,
                        number: true
                    },
                },

                invalidHandler: function (event, validator) { //display error alert on form submit              
                    success2.hide();
                    error2.show();
                    App.scrollTo(error2, -200);
                },

                errorPlacement: function (error, element) { // render error placement for each input type
                    var icon = $(element).parent('.input-icon').children('i');
                    icon.removeClass('fa-check').addClass("fa-warning");  
                    icon.attr("data-original-title", error.text()).tooltip({'container': 'body'});
                },

                highlight: function (element) { // hightlight error inputs
                    $(element)
                        .closest('.form-group').removeClass("has-success").addClass('has-error'); // set error class to the control group   
                },

                unhighlight: function (element) { // revert the change done by hightlight

                },

                success: function (label, element) {
                    var icon = $(element).parent('.input-icon').children('i');
                    $(element).closest('.form-group').removeClass('has-error').addClass('has-success'); // set success class to the control group
                    icon.removeClass("fa-warning").addClass("fa-check");
                },

                submitHandler: function (form) {
                    success2.show();
                    error2.hide();
                    form.submit(); // submit the form
                }
            });


    }

    return {
        //main function to initiate the module
        init: function () {

            handleValCalendario();

        }

    };

}();

var ComponentsSelectCampagna = function() {

    var handleSelectCampagna = function() {

        // Set the "bootstrap" theme as the default theme for all Select2
        // widgets.
        //
        // @see https://github.com/select2/select2/issues/2927
        $.fn.select2.defaults.set("theme", "bootstrap");

        var placeholder = "Seleziona Campagna";

        $(".select_campagna, .select_campagna-multiple").select2({
            placeholder: placeholder,
            width: null
        });

        $(".select_campagna-allow-clear").select2({
            allowClear: true,
            placeholder: placeholder,
            width: null
        });

        $("button[data-select2-open]").click(function() {
            $("#" + $(this).data("select2-open")).select2("open");
        });

        $(":checkbox").on("click", function() {
            $(this).parent().nextAll("select").prop("disabled", !this.checked);
        });

        // copy Bootstrap validation states to Select2 dropdown
        //
        // add .has-waring, .has-error, .has-succes to the Select2 dropdown
        // (was #select2-drop in Select2 v3.x, in Select2 v4 can be selected via
        // body > .select2-container) if _any_ of the opened Select2's parents
        // has one of these forementioned classes (YUCK! ;-))
        $(".select_campagna, .select_campagna-multiple, .select_campagna-allow-clear").on("select2:open", function() {
            if ($(this).parents("[class*='has-']").length) {
                var classNames = $(this).parents("[class*='has-']")[0].className.split(/\s+/);

                for (var i = 0; i < classNames.length; ++i) {
                    if (classNames[i].match("has-")) {
                        $("body > .select2-container").addClass(classNames[i]);
                    }
                }
            }
        });

        $(".js-btn-set-scaling-classes").on("click", function() {
            $("#select2-multiple-input-sm, #select2-single-input-sm").next(".select2-container--bootstrap").addClass("input-sm");
            $("#select2-multiple-input-lg, #select2-single-input-lg").next(".select2-container--bootstrap").addClass("input-lg");
            $(this).removeClass("btn-primary btn-outline").prop("disabled", true);
        });
    }

    return {
        //main function to initiate the module
        init: function() {
            handleSelectCampagna();
        }
    };

}();

var ComponentsMultiselectCampagneHome = function () {

    return {
        //main function to initiate the module
        init: function () {
                $('.mt-multiselect').each(function(){
                        var btn_class = $(this).attr('class');
                        var clickable_groups = ($(this).data('clickable-groups')) ? $(this).data('clickable-groups') : false ;
                        var collapse_groups = ($(this).data('collapse-groups')) ? $(this).data('collapse-groups') : false ;
                        var drop_right = ($(this).data('drop-right')) ? $(this).data('drop-right') : false ;
                        var drop_up = ($(this).data('drop-up')) ? $(this).data('drop-up') : false ;
                        var select_all = ($(this).data('select-all')) ? $(this).data('select-all') : false ;
                        var width = ($(this).data('width')) ? $(this).data('width') : '' ;
                        var height = ($(this).data('height')) ? $(this).data('height') : '' ;
                        var filter = ($(this).data('filter')) ? $(this).data('filter') : false ;
                        var noneText = ($(this).data('none-selected')) ? $(this).data('none-selected') : 'Nessuna dato selezionato' ;

                        // advanced functions
                        var onchange_function = function(option, checked, select) {
                        alert('Changed option ' + $(option).val() + '.');
                    }
                    var dropdownshow_function = function(event) {
                        alert('Dropdown shown.');
                    }
                    var dropdownhide_function = function(event) {
                        document.formIntervallo.submit();
                    }

                    // init advanced functions
                    var onchange = ($(this).data('action-onchange') == true) ? onchange_function : '';
                    var dropdownshow = ($(this).data('action-dropdownshow') == true) ? dropdownshow_function : '';
                    var dropdownhide = ($(this).data('action-dropdownhide') == true) ? dropdownhide_function : '';

                    // template functions
                    // init variables
                    var li_template;
                    if ($(this).attr('multiple')){
                        li_template = '<li class="mt-checkbox-list"><a href="javascript:void(0);"><label class="mt-checkbox"> <span></span></label></a></li>';
                        } else {
                                li_template = '<li><a href="javascript:void(0);"><label></label></a></li>';
                        }

                    // init multiselect
                        $(this).multiselect({
                                enableClickableOptGroups: clickable_groups,
                                enableCollapsibleOptGroups: collapse_groups,
                                disableIfEmpty: true,
                                enableCaseInsensitiveFiltering: true,
                                enableFullValueFiltering: false,
                                enableFiltering: filter,
                                includeSelectAllOption: select_all,
                                dropRight: drop_right,
                                buttonWidth: width,
                                maxHeight: height,
                                onChange: onchange,
                                onDropdownShow: dropdownshow,
                                onDropdownHide: dropdownhide,
                                buttonClass: btn_class,
                                nonSelectedText: noneText,
                                //optionClass: function(element) { return "mt-checkbox"; },
                                //optionLabel: function(element) { console.log(element); return $(element).html() + '<span></span>'; },
                                /*templates: {
                                li: li_template,
                            }*/
                        });   
                });
        }
    };

}();
