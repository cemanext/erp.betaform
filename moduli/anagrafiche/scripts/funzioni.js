/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var BASE_URL_HOST = "http://"+window.location.hostname+"";

function scriviDentroListaPreventiviDettaglioTXT(selettore){
    
    var id = selettore.id;

    var temp = new Array();
    var valori = $("#"+id).find(':selected').data("options");
    temp = valori.split(":");

    $("#txt_"+temp[3]+"_prezzo_prodotto").val(temp[0]);
    $("#txt_"+temp[3]+"_quantita").val(temp[2]);
    
    $("#formDettaglioTabAnagrafica").submit();
}

$( document ).ready(function() {
    
    BASE_URL_HOST = "http://"+window.location.hostname+"";
    
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
    
    if($.urlParam('res')=="2" && $.urlParam('tab')=="azienda"){
        toastr.warning("Non è stata torvata nessuna azienda corrispondente alla Partita IVA inserita.");
        toastr.success("Una nuova azienda è stata creata, terminare la compilazione dei dati.","Nuova Azienda");
        pulisciRefereStorico();
    }
    
    if($.urlParam('res')=="3" && $.urlParam('tab')=="azienda"){
        toastr.warning("Il formato della P.IVA inserita è sbagliato!", "Partita IVA sbagliata");
        //toastr.success("Un nuovo professionista è stata creato, terminare la compilazione dei dati.","Nuovo Professionista");
        //toastr.success("Dati aggiornati!");
        pulisciRefereStorico();
    }
    
    if($.urlParam('res')=="2" && $.urlParam('tab')=="prof"){
        toastr.warning("Non è stato torvato nessun professinista corrispondente al CODICE FISCALE inserito.");
        toastr.success("Un nuovo professionista è stata creato, terminare la compilazione dei dati.","Nuovo Professionista");
        //toastr.success("Dati aggiornati!");
        pulisciRefereStorico();
    }
    
    if($.urlParam('res')=="3" && $.urlParam('tab')=="prof"){
        toastr.warning("Il formato CODICE FISCALE inserito è sbagliato!", "Codice Fiscale sbagliato");
        //toastr.success("Un nuovo professionista è stata creato, terminare la compilazione dei dati.","Nuovo Professionista");
        //toastr.success("Dati aggiornati!");
        pulisciRefereStorico();
    }
    
    if($.urlParam('res')=="1"){
        toastr.success("Dati aggiornati!","Salvato");
        //toastr.success("Dati aggiornati!");
        pulisciRefereStorico();
    }
    
    if($.urlParam('res')=="0"){
        toastr.error("Si è verificato un errore nell'aggironamento dei dati.");
        pulisciRefereStorico();
    }
    
    if($.urlParam('res')=="4"){
        toastr.success("Hai preso in carico questa richiesta!","Richiesta Presa in Carico");
        pulisciRefereStorico();
    }
    
    if($.urlParam('res')=="5"){
        toastr.success("Hai traferito questa richiesta correttamente!","Richiesta Trasferita");
        pulisciRefereStorico();
    }
    
    if($.urlParam('res')=="6"){
        toastr.success("Il preventivo è stato inviato al cliente!","Mail Inviata");
        pulisciRefereStorico();
    }
    
    if($.urlParam('tbl')=="calendario"){
        var nome = $("#copiaNome").val();
        var cognome = $("#copiaCognome").val();
        var codice_fiscale = $("#copiaCodiceFiscale").val();
        var telefono = $("#copiaTelefono").val();
        var email = $("#copiaEmail").val();
        var idProfessionista = $("#lista_professionisti_txt_id").val();
        
        if(idProfessionista=="0"){
            $("#lista_professionisti_txt_nome").val(nome);
            $("#lista_professionisti_txt_cognome").val(cognome);
            $("#lista_professionisti_txt_codice_fiscale").val(codice_fiscale.toUpperCase());
            $("#lista_professionisti_txt_telefono").val(telefono);
            $("#lista_professionisti_txt_email").val(email.toLowerCase());
        }
    }
    
    $("#calendario_txt_stato").on('change', function(){
        if($("#calendario_txt_stato").val()=="Richiamare"){
            var dataCalendario = $("#calendario_txt_data").val();
            var tmp = explode("-",dataCalendario);
            var currentdate = new Date(tmp[2]+'-'+tmp[1]+'-'+tmp[0]);
            currentdate.setDate(currentdate.getDate() + 1);
            var tomorrow = currentdate.toJSON().slice(0,10);
            var tmp2 = explode("-",tomorrow);
            $("#calendario_txt_data").val(tmp2[2]+'-'+tmp2[1]+'-'+tmp2[0]);
        }
    });
    
    //$("#lista_professionisti_txt_codice_fiscale").inputmask();
    
    /*$("#formDettaglioTabAnagrafica").on( "submit", function() {
        $(":input").inputmask();
    });*/
    
    $("#copiaValoriRichiestaInPartecipante").on( "click", function(event) {
        
        event.preventDefault();
        
        var nome = $("#copiaNome").val();
        var cognome = $("#copiaCognome").val();
        var codice_fiscale = $("#copiaCodiceFiscale").val();
        var telefono = $("#copiaTelefono").val();
        var email = $("#copiaEmail").val();
        
        $("#lista_professionisti_txt_nome").val(nome);
        $("#lista_professionisti_txt_cognome").val(cognome);
        $("#lista_professionisti_txt_codice_fiscale").val(codice_fiscale);
        $("#lista_professionisti_txt_telefono").val(telefono);
        $("#lista_professionisti_txt_email").val(email);
    });
    
    $('#myModalCodiceFiscale').on('shown.bs.modal', function () {
        $('#idFromCercaCodiceUtente #codice_utente').focus();
    });
    
    $("#cercaCodiceUtentePartecipante, #aggiungiCodiceUtentePartecipante").on( "click", function(event) {
        
        event.preventDefault();
        
        $("#myModalCodiceFiscale").modal({                    // wire up the actual modal functionality and show the dialog
            "backdrop"  : "static",
            "keyboard"  : true,
            "show"      : true                     // ensure the modal is shown immediately
        });
        
    });
    
    $("#myModalCodiceFiscale #okButton").on( "click", function(event) {
        
        event.preventDefault();
        
        var posting = jQuery.post( BASE_URL_HOST+"/moduli/anagrafiche/salva.php?fn=CercaSalvaProfessionista" , jQuery( "#idFromCercaCodiceUtente" ).serializeArray() );
        posting.done(function(data) {
            
            var str = data.replace(/^\s+|\s+$/g, '');
            var res = str.split(":");
            
            if(res[0] === "OK"){
                $("#myModalCodiceFiscale").modal('hide');     // dismiss the dialog
                location.reload();
            }else{
                toastr.warning("Non è stato torvato nessun professionista corrispondente al dato inserito.");
                //alert("Non è stato torvato nessun professionista corrispondente al dato inserito.");
                var nome = $("#copiaNome").val();
                var cognome = $("#copiaCognome").val();
                var codice_fiscale = res[1];
                var telefono = $("#copiaTelefono").val();
                var email = $("#copiaEmail").val();

                $("#lista_professionisti_txt_nome").val(nome);
                $("#lista_professionisti_txt_cognome").val(cognome);
                $("#lista_professionisti_txt_codice_fiscale").val(codice_fiscale);
                $("#lista_professionisti_txt_telefono").val(telefono);
                $("#lista_professionisti_txt_email").val(email);
                
                $("#myModalCodiceFiscale").modal('hide');     // dismiss the dialog
            }
            //alert( "Data Loaded: " + data );
        }).fail(function() {
            toastr.error("Errore nella ricerca del Codice Cliente o Codice Fiscale.");
            //alert("Errore nella ricerca del Codice Cliente o Codice Fiscale.");

            $("#myModalCodiceFiscale").modal('hide'); 
        });

    });
    
    $("#myModalCodiceFiscale #annullaButton").on( "click", function(event) {
        event.preventDefault();
        $("#myModalCodiceFiscale").modal('hide');     // dismiss the dialog
    });
    
    $('#myModalAggiungiAzienda').on('shown.bs.modal', function () {
        $('#idFromAggiungiAzienda #partita_iva').focus();
    });
    
    $("#aggiungiAzienda").on( "click", function(event) {
        
        event.preventDefault();
        
        $("#myModalAggiungiAzienda").modal({                    // wire up the actual modal functionality and show the dialog
            "backdrop"  : "static",
            "keyboard"  : true,
            "show"      : true                     // ensure the modal is shown immediately
        });
        
    });
    
    $("#myModalAggiungiAzienda #okButtonAggiungiAzienda").on( "click", function(event) {
        
        event.preventDefault();
        
        var posting = jQuery.post( BASE_URL_HOST+"/moduli/anagrafiche/salva.php?fn=aggiungiAzienda" , jQuery( "#idFromAggiungiAzienda" ).serializeArray() );
        posting.done(function(data) {
            
            var str = data.replace(/^\s+|\s+$/g, '');
            var res = str.split(":");
            
            if(res[0] === "OK"){
                $("#myModalAggiungiAzienda").modal('hide');     // dismiss the dialog
                location.href = urlReferer + "&res=1#tab_azienda";
                //location.reload();
            }else if(res[0] === "OK2"){
                //alert("Non è stata torvata nessuna azienda corrispondente alla Partita IVA inserita.\n\nUna nuova azienda è stata creata, terminare la compilazione dei dati.");
                $("#myModalAggiungiAzienda").modal('hide');     // dismiss the dialog
                location.href = urlReferer + "&res=2#tab_azienda";
            }else{
                toastr.alert("Si è verificato un problema, riprovare.","Errore");
                //alert("Non è stata torvata nessuna azienda corrispondente alla Partita IVA inserita.\n\nUna nuova azienda è stata creata, terminare la compilazione dei dati.");
                $("#myModalAggiungiAzienda").modal('hide');     // dismiss the dialog
                location.href = urlReferer + "&res=0#tab_azienda";
            }
            //alert( "Data Loaded: " + data );
        }).fail(function() {
            toastr.error("Errore nella ricerca della Partita IVA.");
            //alert("Errore nella ricerca del Codice Cliente o Codice Fiscale.");

            $("#myModalAggiungiAzienda").modal('hide');
            location.href = urlReferer + "&res=0#tab_azienda";
        });

    });
    
    $("#myModalAggiungiAzienda #annullaButtonAggiungiAzienda").on( "click", function(event) {
        event.preventDefault();
        $("#myModalAggiungiAzienda").modal('hide');     // dismiss the dialog
    });
    
    /*$("#myModal").on("hide", function() {    // remove the event listeners when the dialog is dismissed
        $("#myModal a.btn").off("click");
    });*/
    
    /*$("#myModal").on("hidden", function() {  // remove the actual elements from the DOM when fully hidden
        $("#myModal").remove();
    });*/
    
    $('#myModalCercaProfessionista').on('shown.bs.modal', function () {
        $('#idFromCercaProfessionista #cerca_professionista').focus();
    });
    
    $("#cercaProfessionista, #cambiaProfessionista").on( "click", function(event) {
        
        event.preventDefault();
        
        $("#myModalCercaProfessionista").modal({   // wire up the actual modal functionality and show the dialog
            "backdrop"  : "static",
            "keyboard"  : true,
            "show"      : true                     // ensure the modal is shown immediately
        });
        
    });
    
    var cercaProfessionista = new Bloodhound({
        datumTokenizer: function(d) { return d.tokens; },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: BASE_URL_HOST+'/moduli/anagrafiche/salva.php?fn=cercaProfessionista&key_search=%QUERY',
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
          $("#myModalCercaProfessionista input#id_professionista").val(data.id_professionista);
          $("#myModalCercaProfessionista input#codice_fiscale").val(data.codice_fiscale);
          //alert('Selection: ' + data.ragione_sociale);
      });
    
    $("#myModalCercaProfessionista #okButtonCercaProfessionista").on( "click", function(event) {
        
        //event.preventDefault();
        
        var posting = jQuery.post( BASE_URL_HOST+"/moduli/anagrafiche/salva.php?fn=aggiungiProfessionista" , jQuery( "#idFromCercaProfessionista" ).serializeArray() );
        posting.done(function(data) {
            
            var str = data.replace(/^\s+|\s+$/g, '');
            var res = str.split(":");
            
            if(res[0] === "OK"){
                $("#myModalCercaProfessionista input#id_professionista").val('');
                $("#myModalCercaProfessionista input#codice_fiscale").val('');
                $("#myModalCercaProfessionista input#cerca_professionista").val('');
                $("#myModalCercaProfessionista").modal('hide');     // dismiss the dialog
                location.href = urlReferer + "&res=1&tab=prof#tab_prof";
            }else if(res[0]==="KO2"){
                $("#myModalCercaProfessionista input#id_professionista").val('');
                $("#myModalCercaProfessionista input#codice_fiscale").val('');
                $("#myModalCercaProfessionista input#cerca_professionista").val('');
                $("#myModalCercaProfessionista").modal('hide');     // dismiss the dialog
                if($.urlParam('res')=="3" && $.urlParam('tab')=="prof"){
                    location.reload();
                }else{
                    location.href = urlReferer + "&res=3&tab=prof#tab_prof";
                }
            }else{
                //alert("Non è stata torvata nessuna azienda corrispondente alla Partita IVA inserita.\n\nUna nuova azienda è stata creata, terminare la compilazione dei dati.");
                $("#myModalCercaProfessionista input#id_professionista").val('');
                $("#myModalCercaProfessionista input#codice_fiscale").val('');
                $("#myModalCercaProfessionista input#cerca_professionista").val('');
                $("#myModalCercaProfessionista").modal('hide');     // dismiss the dialog
                location.href = urlReferer + "&res=2&tab=prof#tab_prof";
            }
            //alert( "Data Loaded: " + data );
        }).fail(function() {
            //alert("Errore nella ricerca del Codice Cliente o Codice Fiscale.");
            $("#myModalCercaProfessionista input#id_professionista").val('');
            $("#myModalCercaProfessionista input#codice_fiscale").val('');
            $("#myModalCercaProfessionista input#cerca_professionista").val('');
            $("#myModalCercaProfessionista").modal('hide'); 
            location.href = urlReferer + "&res=0&tab=prof#tab_prof";
        });

    });
    
    $("#myModalCercaProfessionista #annullaButtonCercaProfessionista").on( "click", function(event) {
        event.preventDefault();
        $("#myModalCercaProfessionista input#id_professionista").val('');
        $("#myModalCercaProfessionista input#codice_fiscale").val('');
        $("#myModalCercaProfessionista").modal('hide');     // dismiss the dialog
    });
    
    $('#myModalCercaAzienda').on('shown.bs.modal', function () {
        $('#idFromCercaAzienda #cerca_azienda').focus();
    });
    
    $("#cercaAzienda, #cercaAziendaAggiungi").on( "click", function(event) {
        
        event.preventDefault();
        
        $("#myModalCercaAzienda").modal({                    // wire up the actual modal functionality and show the dialog
            "backdrop"  : "static",
            "keyboard"  : true,
            "show"      : true                     // ensure the modal is shown immediately
        });
        
    });
    
    var cercaAziende = new Bloodhound({
        datumTokenizer: function(d) { return d.tokens; },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: BASE_URL_HOST+'/moduli/anagrafiche/salva.php?fn=cercaAzienda&key_search=%QUERY',
            wildcard: '%QUERY'
          }
      });
      
      cercaAziende.initialize();

      $('input#cerca_azienda').typeahead(null, {
        name: 'cerca_azienda',
        displayKey: function(data) {
            return data.ragione_sociale;        
        },
        limit : 100,
        source: cercaAziende.ttAdapter()
      }).bind('typeahead:select', function(ev, data) {
          $("#myModalCercaAzienda input#id_azienda").val(data.id_azienda);
          $("#myModalCercaAzienda input#partita_iva").val(data.partita_iva);
          //alert('Selection: ' + data.ragione_sociale);
      });
    
    $("#myModalCercaAzienda #okButtonCercaAzienda").on( "click", function(event) {
        
        event.preventDefault();
        
        var posting = jQuery.post( BASE_URL_HOST+"/moduli/anagrafiche/salva.php?fn=aggiungiAzienda" , jQuery( "#idFromCercaAzienda" ).serializeArray() );
        posting.done(function(data) {
            
            var str = data.replace(/^\s+|\s+$/g, '');
            var res = str.split(":");
            
            if(res[0] === "OK"){
                $("#myModalCercaAzienda input#id_azienda").val('');
                $("#myModalCercaAzienda input#partita_iva").val('');
                $("#myModalCercaAzienda").modal('hide');     // dismiss the dialog
                location.href = urlReferer + "&res=1&tab=azienda#tab_azienda";
            }else if(res[0]==="KO2"){
                $("#myModalCercaAzienda input#id_azienda").val('');
                $("#myModalCercaAzienda input#partita_iva").val('');
                $("#myModalCercaAzienda").modal('hide');     // dismiss the dialog
                toastr.warning("Il formato della P.IVA inserita è sbagliato!", "Partita IVA sbagliata");
                /*if($.urlParam('res')=="3" && $.urlParam('tab')=="azienda"){
                    location.reload();
                }else{
                    location.href = urlReferer + "&res=3&tab=azienda#tab_prof";
                }*/
            }else{
                //alert("Non è stata torvata nessuna azienda corrispondente alla Partita IVA inserita.\n\nUna nuova azienda è stata creata, terminare la compilazione dei dati.");
                $("#myModalCercaAzienda input#id_azienda").val('');
                $("#myModalCercaAzienda input#partita_iva").val('');
                $("#myModalCercaAzienda").modal('hide');     // dismiss the dialog
                location.href = urlReferer + "&res=2&tab=azienda#tab_azienda";
            }
            //alert( "Data Loaded: " + data );
        }).fail(function() {
            //alert("Errore nella ricerca del Codice Cliente o Codice Fiscale.");
            $("#myModalCercaAzienda input#id_azienda").val('');
                $("#myModalCercaAzienda input#partita_iva").val('');
            $("#myModalCercaAzienda").modal('hide'); 
            toastr.error("Si è verificato un errore nell'aggironamento dei dati.");
            location.href = urlReferer + "&res=0&tab=azienda#tab_azienda";
        });

    });
    
    $("#myModalCercaAzienda #annullaButtonCercaAzienda").on( "click", function(event) {
        event.preventDefault();
        $("#myModalCercaAzienda input#id_azienda").val('');
        $("#myModalCercaAzienda input#partita_iva").val('');
        $("#myModalCercaAzienda").modal('hide');     // dismiss the dialog
    });
    
    
    $("#trasferisciRichiesta").on( "click", function(event) {
        
        event.preventDefault();
        
        $("#myModalAssociaCommerciale").modal({          // wire up the actual modal functionality and show the dialog
            "backdrop"  : "static",
            "keyboard"  : true,
            "show"      : true                     // ensure the modal is shown immediately
        });
        
    }); 
    
    $("#myModalAssociaCommerciale #okButtonAssociaCommerciale").on( "click", function(event) {
        
        event.preventDefault();
        var posting = jQuery.post( BASE_URL_HOST+"/moduli/anagrafiche/salva.php?fn=trasferisciCommerciale" , jQuery( "#idFromCommerciale" ).serializeArray() );
        posting.done(function(data) {
            var tmp = data.split(':');
            $("#myModalAssociaCommerciale").modal('hide');     // dismiss the dialog
            location.href = BASE_URL_HOST+"/moduli/calendario/index.php?whrStato=&res=5";
            //alert( "Data Loaded: " + data );
        }).fail(function() {
            toastr.alert("Impossibile trasferire la richiesta!", "Errore");
            $("#myModalAssociaCommerciale").modal('hide'); 
        });
    });
    
    $("#myModalAssociaCommerciale #annullaButtonAssociaCommerciale").on( "click", function(event) {
        event.preventDefault(); 
        $("#myModalAssociaCommerciale #idCal").val('');
        $("#myModalAssociaCommerciale").modal('hide');     // dismiss the dialog
    });
    
    $('#myModalAssociaCommerciale').on('shown.bs.modal', function () {
        $('#idFromCommerciale #id_commerciale').focus();
    });
    
    
    $("#myModalPrendiInCarico #okButtonPrendiInCarico").on( "click", function(event) {
        event.preventDefault();
        var posting = jQuery.get( BASE_URL_HOST+"/moduli/anagrafiche/salva.php?fn=prendiInCaricoRichiesta" , jQuery( "#idFromPrendiInCaricoCommerciale" ).serializeArray() );
        posting.done(function(data) {
            var tmp = data.split(':');
            $("#myModalPrendiInCarico").modal('hide');
            location.href = urlReferer +"&res=4";
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
        $("#myModalPrendiInCarico .form-body #idAgenteOld").remove();
        $("#myModalPrendiInCarico .form-body #idAgenteNew").remove();
        $("#myModalPrendiInCarico").modal('hide');     // dismiss the dialog
    });
    
    $('#myModalAssociaProfessionista').on('shown.bs.modal', function () {
        $('#idFromAssociaProfessionista #associa_professionista').focus();
    });
    
    $("#associaProfessionista").on( "click", function(event) {
        
        event.preventDefault();
        
        $("#myModalAssociaProfessionista").modal({   // wire up the actual modal functionality and show the dialog
            "backdrop"  : "static",
            "keyboard"  : true,
            "show"      : true                     // ensure the modal is shown immediately
        });
        
    });
    
    var cercaProfessionista = new Bloodhound({
        datumTokenizer: function(d) { return d.tokens; },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: BASE_URL_HOST+'/moduli/anagrafiche/salva.php?fn=cercaProfessionista&key_search=%QUERY',
            wildcard: '%QUERY'
          }
      });
      
      cercaProfessionista.initialize();

      $('input#associa_professionista').typeahead(null, {
        name: 'associa_professionista',
        displayKey: function(data) {
            return data.ragione_sociale;        
        },
        limit : 100,
        source: cercaProfessionista.ttAdapter()
      }).bind('typeahead:select', function(ev, data) {
            $("#myModalAssociaProfessionista input#id_professionista").val(data.id_professionista);
            $("#myModalAssociaProfessionista input#codice_fiscale").val(data.codice_fiscale);
            //alert('Selection: ' + data.ragione_sociale);
          
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
            
            //alert('SaveId: '+saveId);

            $("#myModalAssociaProfessionista input#idIscrizioni").val(saveId);
          
      });
    
    $("#myModalAssociaProfessionista #okButtonAssociaProfessionista").on( "click", function(event) {
        
        //event.preventDefault();
        
        
        
        var posting = jQuery.post( BASE_URL_HOST+"/moduli/anagrafiche/salva.php?fn=cambiaProfessionistaIscrizione" , jQuery( "#idFromAssociaProfessionista" ).serializeArray() );
        posting.done(function(data) {
            
            var str = data.replace(/^\s+|\s+$/g, '');
            var res = str.split(":");
            
            if(res[0] === "OK"){
                $("#myModalAssociaProfessionista input#id_professionista").val('');
                $("#myModalAssociaProfessionista input#codice_fiscale").val('');
                $("#myModalAssociaProfessionista input#cerca_professionista").val('');
                $("#myModalAssociaProfessionista input#idIscrizioni").val('');
                $("#myModalAssociaProfessionista").modal('hide');     // dismiss the dialog
                location.href = urlReferer + "&res=1#tab_prof";
            }else{
                //alert("Non è stata torvata nessuna azienda corrispondente alla Partita IVA inserita.\n\nUna nuova azienda è stata creata, terminare la compilazione dei dati.");
                $("#myModalAssociaProfessionista input#id_professionista").val('');
                $("#myModalAssociaProfessionista input#codice_fiscale").val('');
                $("#myModalAssociaProfessionista input#cerca_professionista").val('');
                $("#myModalAssociaProfessionista input#idIscrizioni").val('');
                $("#myModalAssociaProfessionista").modal('hide');     // dismiss the dialog
                location.href = urlReferer + "&res=0#tab_prof";
            }
            //alert( "Data Loaded: " + data );
        }).fail(function() {
            //alert("Errore nella ricerca del Codice Cliente o Codice Fiscale.");
            $("#myModalAssociaProfessionista input#id_professionista").val('');
            $("#myModalAssociaProfessionista input#codice_fiscale").val('');
            $("#myModalAssociaProfessionista input#cerca_professionista").val('');
            $("#myModalAssociaProfessionista input#idIscrizioni").val('');
            $("#myModalAssociaProfessionista").modal('hide'); 
            location.href = urlReferer + "&res=0#tab_prof";
        });

    });
    
    $("#myModalAssociaProfessionista #annullaButtonAssociaProfessionista").on( "click", function(event) {
        event.preventDefault();
        $("#myModalAssociaProfessionista input#id_professionista").val('');
        $("#myModalAssociaProfessionista input#codice_fiscale").val('');
        $("#myModalAssociaProfessionista input#idIscrizioni").val('');
        $("#myModalAssociaProfessionista").modal('hide');     // dismiss the dialog
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
    
    ComponentsSelectProvAblo.init();
    ComponentsSelectProv.init();
    ComponentsSelectProfessione.init();
    ComponentsSelectAlbo.init();
    ComponentsSelectProdotto.init();
    ComponentsSelectFormaGiuridica.init();
    TableDatatablesAjaxProff.init();
    FormInputMask.init();
    TabelleAnagrafiche.init();
    ComponentsSelectProvvigione.init();
    ComponentsSelectTitolo.init();
});

function prendiInCaricoRichesta(idCalendario, idAgenteOld, idAgenteNew){
    
     $("#myModalPrendiInCarico .form-body").prepend("<input type=\"hidden\" name=\"idCal\" id=\"idCal\" value=\""+idCalendario+"\">");
     $("#myModalPrendiInCarico .form-body").prepend("<input type=\"hidden\" name=\"idAgenteOld\" id=\"idAgenteOld\" value=\""+idAgenteOld+"\">");
     $("#myModalPrendiInCarico .form-body").prepend("<input type=\"hidden\" name=\"idAgenteNew\" id=\"idAgenteNew\" value=\""+idAgenteNew+"\">");
    
    $("#myModalPrendiInCarico").modal({                    // wire up the actual modal functionality and show the dialog
      "backdrop"  : "static",
      "keyboard"  : true,
      "show"      : true                     // ensure the modal is shown immediately
    });
}

var FormInputMask = function () {
    
    var handleInputMasks = function () {
        $(":input").inputmask();
    }

    return {
        //main function to initiate the module
        init: function () {
            handleInputMasks();
        }
    };

}();

var ComponentsSelectProvAblo = function() {

    var handleSelectProvAblo = function() {

        // Set the "bootstrap" theme as the default theme for all Select2
        // widgets.
        //
        // @see https://github.com/select2/select2/issues/2927
        $.fn.select2.defaults.set("theme", "bootstrap");

        var placeholder = "Provincia Albo";

        $(".select_prov_ablo, .select_prov_ablo-multiple").select2({
            placeholder: placeholder,
            width: null
        });

        $(".select_prov_ablo-allow-clear").select2({
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
        $(".select_prov_ablo, .select_prov_ablo-multiple, .select_prov_ablo-allow-clear").on("select2:open", function() {
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
            handleSelectProvAblo();
        }
    };

}();


var ComponentsSelectProv = function() {

    var handleSelectProv = function() {

        // Set the "bootstrap" theme as the default theme for all Select2
        // widgets.
        //
        // @see https://github.com/select2/select2/issues/2927
        $.fn.select2.defaults.set("theme", "bootstrap");

        var placeholder = "Provincia";

        $(".select_prov, .select_prov-multiple").select2({
            placeholder: placeholder,
            width: null
        });

        $(".select_prov-allow-clear").select2({
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
        $(".select_prov, .select_prov-multiple, .select_prov-allow-clear").on("select2:open", function() {
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
            handleSelectProv();
        }
    };

}();

var ComponentsSelectProfessione = function() {

    var handleSelectProfessione = function() {

        // Set the "bootstrap" theme as the default theme for all Select2
        // widgets.
        //
        // @see https://github.com/select2/select2/issues/2927
        $.fn.select2.defaults.set("theme", "bootstrap");

        var placeholder = "Professione";

        $(".select_professione, .select_professione-multiple").select2({
            placeholder: placeholder,
            width: null
        });

        $(".select_professione-allow-clear").select2({
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
        $(".select_professione, .select_professione-multiple, .select_professione-allow-clear").on("select2:open", function() {
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
            handleSelectProfessione();
        }
    };

}();

var ComponentsSelectAlbo = function() {

    var handleSelectAlbo = function() {

        // Set the "bootstrap" theme as the default theme for all Select2
        // widgets.
        //
        // @see https://github.com/select2/select2/issues/2927
        $.fn.select2.defaults.set("theme", "bootstrap");

        var placeholder = "Tipo Albo";

        $(".select_albo, .select_albo-multiple").select2({
            placeholder: placeholder,
            width: null
        });

        $(".select_albo-allow-clear").select2({
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
        $(".select_albo, .select_albo-multiple, .select_albo-allow-clear").on("select2:open", function() {
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
            handleSelectAlbo();
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

        var placeholder = "Prodotto";

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

var ComponentsSelectFormaGiuridica = function() {

    var handleSelectFormaGiuridica = function() {

        // Set the "bootstrap" theme as the default theme for all Select2
        // widgets.
        //
        // @see https://github.com/select2/select2/issues/2927
        $.fn.select2.defaults.set("theme", "bootstrap");

        var placeholder = "Forma Giuridica";

        $(".select_forma_giuridica, .select_forma_giuridica-multiple").select2({
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
        $(".select_forma_giuridica, .select_forma_giuridica-multiple, .select_forma_giuridica-allow-clear").on("select2:open", function() {
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
            handleSelectFormaGiuridica();
        }
    };

}();

var ComponentsSelectProvvigione = function() {

    var handleSelectProvvigione = function() {

        // Set the "bootstrap" theme as the default theme for all Select2
        // widgets.
        //
        // @see https://github.com/select2/select2/issues/2927
        $.fn.select2.defaults.set("theme", "bootstrap");

        var placeholder = "Provvigione";

        $(".select_provvigione, .select_provvigione-multiple").select2({
            placeholder: placeholder,
            width: null
        });

        $(".select_provvigione-allow-clear").select2({
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
        $(".select_provvigione, .select_provvigione-multiple, .select_provvigione-allow-clear").on("select2:open", function() {
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
            handleSelectProvvigione();
        }
    };

}();

var ComponentsSelectTitolo = function() {

    var handleSelectTitolo = function() {

        // Set the "bootstrap" theme as the default theme for all Select2
        // widgets.
        //
        // @see https://github.com/select2/select2/issues/2927
        $.fn.select2.defaults.set("theme", "bootstrap");

        var placeholder = "Titolo";

        $(".select_titolo, .select_titolo-multiple").select2({
            placeholder: placeholder,
            width: null
        });

        $(".select_titolo-allow-clear").select2({
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
        $(".select_titolo, .select_titolo-multiple, .select_titolo-allow-clear").on("select2:open", function() {
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
            handleSelectTitolo();
        }
    };

}();

var TableDatatablesAjaxProff = function () {

    var handleTableAjaxProff = function () {
        var table = $('#datatable_ajax_search');

        var oTable = table.dataTable({

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
            responsive: false,

            "ajax": {
                    "url": BASE_URL_HOST+"/moduli/anagrafiche/scripts/server_processing.php?tbl="+$.urlParam('tbl'), // ajax source
            },

            //"ordering": true,
            "orderMulti": true,

            "order": [
                [3, 'asc']
            ],

            "lengthMenu": [
                [10, 25, 30, 50],
                [10, 25, 30, 50] // change per page values here
            ],
            // set the initial value
            "pageLength": 30,

            "dom": "<'row' <'col-md-12'B>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>", // horizobtal scrollable datatable

            "columnDefs": [
                {"className": "dt-center", "targets": "_all"},
                {"orderable": true, "targets": [ 3, 4, 5, 6, 7 ],},
                {"orderable": false, "targets": [ 0, 1, 2],}
            ],
            
            // Uncomment below line("dom" parameter) to fix the dropdown overflow issue in the datatable cells. The default datatable layout
            // setup uses scrollable div(table-scrollable) with overflow:auto to enable vertical scroll(see: assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js).
            // So when dropdowns used the scrollable div should be removed.
            //"dom": "<'row' <'col-md-12'T>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r>t<'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",
        });
    }

    var initTableAjax1 = function () {
        var table = $('#datatable_ajax');

        var oTable = table.dataTable({

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
            responsive: false,

            "ajax": {
                    "url": BASE_URL_HOST+"/moduli/anagrafiche/scripts/server_processing.php?tbl="+$.urlParam('tbl'), // ajax source
            },

            //"ordering": true,
            "orderMulti": true,

            "order": [
                [3, 'asc']
            ],

            "lengthMenu": [
                [10, 25, 30, 50],
                [10, 25, 30, 50] // change per page values here
            ],
            // set the initial value
            "pageLength": 30,

            "dom": "<'row' <'col-md-12'B>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>", // horizobtal scrollable datatable

            "columnDefs": [
                {"className": "dt-center", "targets": "_all"},
                {"orderable": false, "targets": [ 0, 1, 2],}
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

            //initPickers();
            handleTableAjaxProff();
            initTableAjax1();
        }

    };

}();

var TabelleAnagrafiche = function () {

  var initTableBase1 = function () {
      var table = $('#tabella_base1, #tabella_base2, #tabella_base3, #tabella_base4, #tabella_base5, #tabella_base6, #tabella_base7, #tabella_base8, #tabella_base9, #tabella_base10');

      var oTable = table.dataTable({
          // Internationalisation. For more info refer to http://datatables.net/manual/i18n

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
          responsive: false,

          "order": [
              [2, 'asc']
          ],

          "lengthMenu": [
              [10, 25, 30, 50],
              [10, 25, 30, 50] // change per page values here
          ],
          // set the initial value
          "pageLength": 30,

          "dom": "<'row' <'col-md-12'B>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>", // horizobtal scrollable datatable

          // Uncomment below line("dom" parameter) to fix the dropdown overflow issue in the datatable cells. The default datatable layout
          // setup uses scrollable div(table-scrollable) with overflow:auto to enable vertical scroll(see: assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js).
          // So when dropdowns used the scrollable div should be removed.
          //"dom": "<'row' <'col-md-12'T>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r>t<'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",
      });
  }
  
  


    return {

        //main function to initiate the module
        init: function () {

            if (!jQuery().dataTable) {
                return;
            }

            initTableBase1();
        }

    };

}();