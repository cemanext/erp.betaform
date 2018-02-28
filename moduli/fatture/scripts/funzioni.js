/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var BASE_URL_HOST = location.protocol+"//"+window.location.hostname+"";

function scriviDentroListaPreventiviDettaglio(){

    var temp = new Array();
    var valori = $("#nome_prodotto").find(':selected').data("options");
    temp = valori.split(":");

    $("#prezzo_prodotto").val(temp[0]);
    $("#iva_prodotto").val(temp[1]);
    $("#quantita").val(temp[2]);
}


function scriviDentroListaPreventiviDettaglioTXT(selettore){

    var id = selettore.id;

    var temp = new Array();
    var valori = $("#"+id).find(':selected').data("options");
    temp = valori.split(":");

    $("#txt_"+temp[3]+"_prezzo_prodotto").val(temp[0]);
    $("#txt_"+temp[3]+"_iva_prodotto").val(temp[1]);
    $("#txt_"+temp[3]+"_quantita").val(temp[2]);
}

var ComponentsDateSelect = function () {

    // private functions & variables
    var initComponentsDateSelect = function() {

        // init datepicker
        $('.data-creazione-emissione').datepicker({
            rtl: App.isRTL(),
            language: "it",
            format: 'dd-mm-yyyy',
            //startDate: '-3d',
            autoclose: true
        });

        // init datepicker
        $('.data-scadenza-emissione').datepicker({
            rtl: App.isRTL(),
            language: "it",
            format: 'dd-mm-yyyy',
            //startDate: '-3d',
            autoclose: true
        });

        // init select2
        $("#tipoBanca").select2({
            placeholder: 'Seleziona Banca',
            minimumResultsForSearch: Infinity
        });
        $("#tipoPagamento").select2({
            placeholder: 'Seleziona Pagamento',
            minimumResultsForSearch: Infinity
        });
    }



    // public functions
    return {

        //main function
        init: function () {
            initComponentsDateSelect();

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

var ComponentsSelectPartecipante = function() {

    var handleSelectPartecipante = function() {

        // Set the "bootstrap" theme as the default theme for all Select2
        // widgets.
        //
        // @see https://github.com/select2/select2/issues/2927
        $.fn.select2.defaults.set("theme", "bootstrap");

        var placeholder = "Partecipante";

        $(".select_partecipante, .select_partecipante-multiple").select2({
            placeholder: placeholder,
            width: null
        });

        $(".select_partecipante-allow-clear").select2({
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
        $(".select_partecipante, .select_partecipante-multiple, .select_partecipante-allow-clear").on("select2:open", function() {
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
            handleSelectPartecipante();
        }
    };

}();

var TableDatatablesFattureResponsive = function () {

  var initab1_fatture_home = function () {
      var table = $('#tab1_fatture_home, #tab2_fatture_home, #tab3_fatture_home');

      var oTable = table.dataTable({

          // Or you can use remote translation file
          "language": {
             url: '//cdn.datatables.net/plug-ins/1.10.12/i18n/Italian.json'
          },

          // setup buttons extentension: http://datatables.net/extensions/buttons/
          buttons: [
              { extend: 'pdf', className: 'btn primary btn-outline' },
              { extend: 'excel', className: 'btn primary btn-outline' }
          ],

          // setup responsive extension: http://datatables.net/extensions/responsive/
          responsive: {
              details: {

              }
          },

          "order": [
              [2, 'asc']
          ],

          "lengthMenu": [
                [10, 25, 30, 50, 100, 250, -1],
                [10, 25, 30, 50, 100, 250, 'Tutti'] // change per page values here
            ],
            // set the initial value
            "pageLength": 50,

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

            initab1_fatture_home();
        }

    };

}();

var TableDatatablesAjaxFatture = function () {

    var initTableAjaxFatture = function () {
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
              { extend: 'print', className: 'btn white btn-outline', exportOptions:{
                    columns: ':visible'
              } },
              { extend: 'copy', className: 'btn white btn-outline', exportOptions:{
                    columns: ':visible'
              }  },
              { extend: 'pdf', className: 'btn white btn-outline', exportOptions:{
                    columns: ':visible'
              }  },
              { extend: 'excel', className: 'btn white btn-outline ', exportOptions:{
                    columns: ':visible'
              }  },
              { extend: 'csv', className: 'btn white btn-outline ', exportOptions:{
                    columns: ':visible'
              }  }
            ],

            // setup responsive extension: http://datatables.net/extensions/responsive/
            responsive: false,
            "stateSave": true,

            "ajax": {
                "url": BASE_URL_HOST+"/moduli/fatture/scripts/server_processing.php?tbl="+$.urlParam('tbl')+"&whr_state="+$.urlParam('whr_state'), // ajax source
            },

            /*"ordering": true,*/
            "orderMulti": true,
            "order": [
                [5, 'asc'],
                [2, 'asc']
            ],

            "lengthMenu": [
                [10, 25, 30, 50, 100, 250, -1],
                [10, 25, 30, 50, 100, 250, 'Tutti'] // change per page values here
            ],
            // set the initial value
            "pageLength": 50,
            
            "columnDefs": [
                {"className": "dt-center", "targets": "_all"},
                {"orderable": false, "targets": [ 0, 1],},
                {
                // Sort column 5 (last_update) using data from column 6 (date_sort).
                    targets: [5],
                    orderData: [6]
                },
                {
                    targets: [6],
                    visible: false,
                    searchable: false
                }
            ],

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

            //initPickers();
            initTableAjaxFatture();
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

$( document ).ready(function() {
    
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
    
    if($.urlParam('res')=="1"){
        toastr.success("Dati aggiornati!","Salvato");
        //toastr.success("Dati aggiornati!");
    }
    
    if($.urlParam('res')=="0"){
        toastr.error("Si è verificato un errore nell'aggironamento dei dati.");
    }
    
    if($.urlParam('res')=="3"){
        toastr.success("Utente creato in lista password!","Utente Creato");
        //toastr.success("Dati aggiornati!");
    }
    
    if($.urlParam('res')=="4"){
        toastr.success("Abbonamento attivato correttamente!","Abbonamento Attivato");
    }
    
    if($.urlParam('res')=="5"){
        toastr.error("Abbonamento NON attivato!","Abbonamento Errore");
    }
    
    if($.urlParam('res')=="6"){
        toastr.success("Fatture inviate correttamente !","Invio Multiplo Fatture");
    }
    
    if($.urlParam('res')=="7"){
        toastr.success("Fatture Emesse Correttamente !","Emissione Multiplo Fatture");
    }
    
    if($.urlParam('res')=="8"){
        toastr.success("Nota di Credito Salvata Correttamente","Nota di Credito");
    }
    
    $('#txt_checkbox_all').change(function(){
        var numCheck = $('#txt_checkbox_all').val();
        for (i = 0; i < numCheck; i++) { 
            if ($('#txt_checkbox_'+i+'').is(':checked')) {
                $('#txt_checkbox_'+i+'').prop('checked',false);
            } else {
                $('#txt_checkbox_'+i+'').prop('checked',true);
            }
        }
    });
    
    $("#cancellaRicarcaTabella").on( "click", function(event) {
        event.preventDefault(); 
        var table = $('#datatable_ajax').DataTable();
        table.state.clear();
        table.destroy();
        TableDatatablesAjaxFatture.init();
    });
    
    ComponentsDateSelect.init();
    ComponentsSelectProdotto.init();
    ComponentsSelectPartecipante.init();
    TableDatatablesFattureResponsive.init();
    TableDatatablesAjaxFatture.init();
    ComponentsSelectProvvigione.init();
    
    $('#ajax').on('hidden.bs.modal', function () {
        ComponentsEditors.destroy();
    });
});
