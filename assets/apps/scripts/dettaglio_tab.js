/**
jektypro custom javascript
**/
var JektyPro = function () {

    // private functions & variables

    var _initComponents = function() {

        // init datepicker
        $('#dataRichiamo').datepicker({
            rtl: App.isRTL(),
            language: "it",
            format: 'yyyy-mm-dd',
            //startDate: '-3d',
            orientation: "bottom",
            autoclose: true
        });

        // init datepicker
        $('.timepicker-data-messaggio').datepicker({
            rtl: App.isRTL(),
            language: "it",
            format: 'yyyy-mm-dd',
            //startDate: '-3d',
            autoclose: true
        });

        $('.timepicker-data-costo').datepicker({
            rtl: App.isRTL(),
            language: "it",
            format: 'yyyy-mm-dd',
            //startDate: '-3d',
            autoclose: true
        });

        $('.timepicker-data-scadenza-costo').datepicker({
            rtl: App.isRTL(),
            language: "it",
            format: 'yyyy-mm-dd',
            //startDate: '-3d',
            autoclose: true
        });

        // init select2 sottoStato
        $("#sottoStato").select2({
            placeholder: 'Seleziona Stato',
            minimumResultsForSearch: Infinity
        });
        $("#prioritaProcesso").select2({
            placeholder: 'Seleziona Priorita',
            minimumResultsForSearch: Infinity
        });
    }



    // public functions
    return {

        //main function
        init: function () {
            _initComponents();
            //_handleProjectListMenu();

            //App.addResizeHandler(function(){
            //    _handleProjectListMenu();
            //});
        }

    };

}();

var ComponentsBootstrapSelect = function () {

    var handleBootstrapSelect = function() {
        $('.bs-select').selectpicker({
            iconBase: 'fa',
            tickIcon: 'fa-check'
        });
    }

    return {
        //main function to initiate the module
        init: function () {
            handleBootstrapSelect();
        }
    };

}();

var ComponentsDateTimePickers = function () {

    var handleDatePickers = function () {

        if (jQuery().datepicker) {
            $('.date-picker').datepicker({
                rtl: App.isRTL(),
                orientation: "left",
                autoclose: true
            });
            //$('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
        }

        /* Workaround to restrict daterange past date select: http://stackoverflow.com/questions/11933173/how-to-restrict-the-selectable-date-ranges-in-bootstrap-datepicker */

        // Workaround to fix datepicker position on window scroll
        $( document ).scroll(function(){
            $('#form_modal2 .date-picker').datepicker('place'); //#modal is the id of the modal
        });
    }
    var handleTimePickers = function () {

        if (jQuery().timepicker) {
            $('.timepicker-default').timepicker({
                autoclose: true,
                showSeconds: true,
                minuteStep: 1
            });

            $('.timepicker-no-seconds').timepicker({
                autoclose: true,
                minuteStep: 5
            });

            $('.timepicker-24-ora-inizio').timepicker({
                autoclose: true,
                minuteStep: 5,
                showSeconds: false,
                showMeridian: false
            });
            $('.timepicker-24-ora-fine').timepicker({
                autoclose: true,
                minuteStep: 5,
                showSeconds: false,
                showMeridian: false
            });

            // handle input group button click
            $('.timepicker').parent('.input-group').on('click', '.input-group-btn', function(e){
                e.preventDefault();
                $(this).parent('.input-group').find('.timepicker').timepicker('showWidget');
            });

            // Workaround to fix timepicker position on window scroll
            $( document ).scroll(function(){
                $('#form_modal4 .timepicker-default, #form_modal4 .timepicker-no-seconds, #form_modal4 .timepicker-24-ora-inizio .timepicker-24-ora-fine').timepicker('place'); //#modal is the id of the modal
            });
        }
    }

    return {
        //main function to initiate the module
        init: function () {
            handleDatePickers();
            handleTimePickers();
            //handleDatetimePicker();
            //handleDateRangePickers();
            //handleClockfaceTimePickers();
        }
    };

}();

var ComponentsEditors = function () {

    var handleWysihtml5 = function () {
        if (!jQuery().wysihtml5) {
            return;
        }

        if ($('.wysihtml5').size() > 0) {
            $('.wysihtml5').wysihtml5({
                "stylesheets": ["/assets/global/plugins/bootstrap-wysihtml5/wysiwyg-color.css"]
            });
        }
    }
    
    var handleWysihtml5Destroy = function () {
        //$('#summernote_1').summernote({height: 300});
        //API:
        //var sHTML = $('#summernote_1').code(); // get code
        //$('.wysihtml5').destroy();
        $(".wysihtml5-toolbar").remove();
        $(".wysihtml5-sandbox").remove();
        $("input[name='_wysihtml5_mode']").remove();
        $(".wysihtml5").show();
        //$('#summernote_1').destroy(); // destroy
    }

    var handleSummernote = function () {
        $('#summernote_1').summernote({height: 300});
        //API:
        //var sHTML = $('#summernote_1').code(); // get code
        //$('#summernote_1').destroy(); // destroy
    }

    return {
        //main function to initiate the module
        init: function () {
            handleWysihtml5();
            handleSummernote();
        },
        
        destroy: function() {
            handleWysihtml5Destroy();
        }
    };

}();


if (App.isAngularJsApp() === false) {
    jQuery(document).ready(function() {
        JektyPro.init();
        ComponentsBootstrapSelect.init();
        ComponentsDateTimePickers.init();
        ComponentsEditors.init();
    });
}
