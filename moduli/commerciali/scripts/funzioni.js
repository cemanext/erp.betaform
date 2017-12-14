/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var BASE_URL_HOST = "http://"+window.location.hostname+"";

$(document).ready(function() {
    
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
    
    if($.urlParam('res')=="0"){
        toastr.error("Si è verificato un errore nella presa in carico della richiesta.","Errore nella Presa in Carico");
    }
    
    if($.urlParam('res')=="4"){
        toastr.success("Le richieste sono state trasferite ad altro agente!","Trasferimento Richiesta");
    }
    
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
        
            var posting = jQuery.post( BASE_URL_HOST+"/moduli/commerciali/salva.php?fn=associaCommerciale" , jQuery( "#idFromCommerciale" ).serializeArray() );
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
    
    ComponentsSelectCommerciale.init();
    ComponentsSelectProdotto.init();
    ComponentsSelectCampagna.init();
    TableDatatablesAjaxCommerciali.init();
    TabelleCommerciali.init();
    TabelleCommercialiHome.init();
    
});

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

var TableDatatablesAjaxCommerciali = function () {

    var initTableAjaxCommerciali1 = function () {
        var table = $('#datatable_ajax_1');

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
                "url": BASE_URL_HOST+"/moduli/commerciali/scripts/server_processing.php?tbl=calendario&fn=tabella1&id="+$.urlParam('id'), // ajax source
            },

            "ordering": true,
            "order": [
                [2, 'asc']
            ],

            "lengthMenu": [
                [10, 25, 30, 50],
                [10, 25, 30, 50] // change per page values here
            ],
            // set the initial value
            "pageLength": 30,
            
            "columnDefs": [
                {"className": "dt-center", "targets": "_all"},
                {"orderable": true, "targets": [ 1, 2, 3, 4, 5, 6, 7 ],},
                {"orderable": false, "targets": [ 0, 8, 9, 10],}
            ],

            "dom": "<'row' <'col-md-12'B>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>", // horizobtal scrollable datatable

            // Uncomment below line("dom" parameter) to fix the dropdown overflow issue in the datatable cells. The default datatable layout
            // setup uses scrollable div(table-scrollable) with overflow:auto to enable vertical scroll(see: assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js).
            // So when dropdowns used the scrollable div should be removed.
            //"dom": "<'row' <'col-md-12'T>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r>t<'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",
        });
    }

    var initTableAjaxCommerciali2 = function () {
        var table = $('#datatable_ajax_2');

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
                "url": BASE_URL_HOST+"/moduli/commerciali/scripts/server_processing.php?tbl=calendario&fn=tabella2&id="+$.urlParam('id'), // ajax source
            },

            "ordering": true,
            "order": [
                [2, 'asc']
            ],

            "lengthMenu": [
                [10, 25, 30, 50],
                [10, 25, 30, 50] // change per page values here
            ],
            // set the initial value
            "pageLength": 10,
            
            "columnDefs": [
                {"className": "dt-center", "targets": "_all"},
                {"orderable": true, "targets": [ 1, 2, 3, 4, 5, 6, 7 ]},
                {"orderable": false, "targets": [ 0, 8, 9]}
            ],

            "dom": "<'row' <'col-md-12'B>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>", // horizobtal scrollable datatable

            // Uncomment below line("dom" parameter) to fix the dropdown overflow issue in the datatable cells. The default datatable layout
            // setup uses scrollable div(table-scrollable) with overflow:auto to enable vertical scroll(see: assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js).
            // So when dropdowns used the scrollable div should be removed.
            //"dom": "<'row' <'col-md-12'T>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r>t<'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",
        });
    }
    
    var initTableAjaxCommerciali3 = function () {
        var table = $('#datatable_ajax_3');

        var oTable = table.dataTable({
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
            responsive: false,

            "ajax": {
                "url": BASE_URL_HOST+"/moduli/commerciali/scripts/server_processing.php?tbl=lista_preventivi&fn=tabella3&id="+$.urlParam('id'), // ajax source
            },

            "ordering": true,
            "order": [
                [2, 'asc']
            ],

            "lengthMenu": [
                [10, 25, 30, 50],
                [10, 25, 30, 50] // change per page values here
            ],
            // set the initial value
            "pageLength": 10,
            
            "columnDefs": [
                {"className": "dt-center", "targets": "_all"},
                {"orderable": true, "targets": [ 2, 3, 4 ]},
                {"orderable": false, "targets": [ 0, 1]}
            ],

            "dom": "<'row' <'col-md-12'B>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>", // horizobtal scrollable datatable

            // Uncomment below line("dom" parameter) to fix the dropdown overflow issue in the datatable cells. The default datatable layout
            // setup uses scrollable div(table-scrollable) with overflow:auto to enable vertical scroll(see: assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js).
            // So when dropdowns used the scrollable div should be removed.
            //"dom": "<'row' <'col-md-12'T>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r>t<'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",
        });
    }
    
    var initTableAjaxCommerciali4 = function () {
        var table = $('#datatable_ajax_4');

        var oTable = table.dataTable({
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
            responsive: false,

            "ajax": {
                "url": BASE_URL_HOST+"/moduli/commerciali/scripts/server_processing.php?tbl=lista_fatture&fn=tabella4&id="+$.urlParam('id'), // ajax source
            },

            "ordering": true,
            "order": [
                [2, 'asc']
            ],

            "lengthMenu": [
                [10, 25, 30, 50],
                [10, 25, 30, 50] // change per page values here
            ],
            // set the initial value
            "pageLength": 10,
            
            "columnDefs": [
                {"className": "dt-center", "targets": "_all"},
                {"orderable": true, "targets": [ 2, 3, 4 ]},
                {"orderable": false, "targets": [ 0, 1]}
            ],

            "dom": "<'row' <'col-md-12'B>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>", // horizobtal scrollable datatable

            // Uncomment below line("dom" parameter) to fix the dropdown overflow issue in the datatable cells. The default datatable layout
            // setup uses scrollable div(table-scrollable) with overflow:auto to enable vertical scroll(see: assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js).
            // So when dropdowns used the scrollable div should be removed.
            //"dom": "<'row' <'col-md-12'T>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r>t<'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",
        });
    }
    
    var initTableAjaxCommerciali = function () {
        var table = $('#datatable_ajax');

        var oTable = table.dataTable({
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
            responsive: false,

            "ajax": {
                "url": BASE_URL_HOST+"/moduli/commerciali/scripts/server_processing.php?tbl="+$.urlParam('tbl')+"&fn=default", // ajax source
            },

            "ordering": true,
            "order": [
                [2, 'asc']
            ],

            "lengthMenu": [
                [10, 25, 30, 50],
                [10, 25, 30, 50] // change per page values here
            ],
            // set the initial value
            "pageLength": 10,
            
            "columnDefs": [
                {"className": "dt-center", "targets": "_all"}
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
            initTableAjaxCommerciali();
            initTableAjaxCommerciali1();
            initTableAjaxCommerciali2();
            initTableAjaxCommerciali3();
            initTableAjaxCommerciali4();
        }

    };

}();

var TabelleCommerciali = function () {

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


var TabelleCommercialiHome = function () {

  var initTableBaseHome = function () {
      var table = $('#tabella_base_home');

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
              [7, 'desc']
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

            initTableBaseHome();
        }

    };

}();
