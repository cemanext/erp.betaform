/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var BASE_URL_HOST = location.protocol+"//"+window.location.hostname;

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
    
    $("#modifica_preventivo_dettaglio").submit();
}

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
var TableDatatablesResponsive = function () {

  var initab1_preventivi_home = function () {
      var table = $('#tab1_preventivi_home');

      var oTable = table.dataTable({
          // Internationalisation. For more info refer to http://datatables.net/manual/i18n
          /*"language": {
              "aria": {
                  "sortAscending": ": activate to sort column ascending",
                  "sortDescending": ": activate to sort column descending"
              },
              "emptyTable": "No data available in table",
              "info": "Showing _START_ to _END_ of _TOTAL_ entries",
              "infoEmpty": "No entries found",
              "infoFiltered": "(filtered1 from _MAX_ total entries)",
              "lengthMenu": "_MENU_ entries",
              "search": "Search:",
              "zeroRecords": "No matching records found"
          },*/

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
              [3, 'desc']
          ],

          "lengthMenu": [
                [10, 25, 30, 50, 100, 250, -1],
                [10, 25, 30, 50, 100, 250, 'Tutti'] // change per page values here
            ],
            // set the initial value
            "pageLength": 50,

          "columnDefs": [
                {"className": "dt-center", "targets": "_all"},
                {"orderable": false, "targets": [ 0, 1, 2],}
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

            if (!jQuery().dataTable) {
                return;
            }

            initab1_preventivi_home();
        }

    };

}();

var TableDatatablesAjaxCarrello = function () {

    var initTableAjaxCarrello = function () {
        var table = $('#datatable_ajax');

        var oTable = table.dataTable({
            
            //"bStateSave": true, 
            stateSave: true,

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
                "url": BASE_URL_HOST+"/moduli/carrello/scripts/server_processing.php?tbl="+$.urlParam('tbl'), // ajax source
            },

            "ordering": true,
            "order": [
                [2, 'desc'],
                [1, 'desc']
            ],

            "lengthMenu": [
                [10, 25, 30, 50, 100, 250, -1],
                [10, 25, 30, 50, 100, 250, 'Tutti'] // change per page values here
            ],
            // set the initial value
            "pageLength": 50,
            
            "columnDefs": [
                {"className": "dt-center", "targets": "_all"},
                {"orderable": false, "targets": [ 0],}
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
            initTableAjaxCarrello();
        }

    };

}();

$( document ).ready(function() {
    
    BASE_URL_HOST = location.protocol+"//"+window.location.hostname
    
    $("#cancellaRicarcaTabella").on( "click", function(event) {
        event.preventDefault(); 
        var table = $('#datatable_ajax').DataTable();
        table.state.clear();
        table.destroy();
        TableDatatablesAjaxCarrello.init();
    });
    
    ComponentsSelectProdotto.init();
    TableDatatablesResponsive.init();
    TableDatatablesAjaxCarrello.init();
});
