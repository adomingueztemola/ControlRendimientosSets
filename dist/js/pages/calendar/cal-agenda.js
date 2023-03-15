! function($) {
    "use strict";

    var CalendarApp = function() {
        this.$body = $("body")
        this.$calendar = $('#calendar'),
            this.$event = ('#calendar-events div.calendar-events'),
            this.$categoryForm = $('#add-new-event form'),
            this.$extEvents = $('#calendar-events'),
            this.$modal = $('#my-event'),
            this.$saveCategoryBtn = $('.save-category'),
            this.$calendarObj = null
    };


    /* on drop */

    /* on click on event */
    CalendarApp.prototype.onEventClick = function(calEvent, jsEvent, view) {
            // console.log(calEvent);
            $("#sucursales_edicion").select2();
            $('#EditarEvento #descripcion').val(calEvent.descripcion);
            $('#EditarEvento #nombre').val(calEvent.title);
            $('#EditarEvento #fecha').val(moment(calEvent.start._i).format('YYYY-MM-DDTHH:mm:ss'));
            $('#EditarEvento #id').val(calEvent.id);
            //------------------------------SELECT MULTIPLE-----------------------//
            var value_select = calEvent.sucursalesPart;
            $("option").attr("selected", false);
            if (value_select == "ALL") {
                $("#ALL").attr("selected", true);
                $('#EditarEvento #sucursales_edicion').trigger('change');

            } else {
                var array = value_select.split(',');
                for (var i = 0; array.length - 1 >= i; i++) {
                    $("#option_suc_" + array[i]).attr("selected", true);
                }
                $('#EditarEvento #sucursales_edicion').attr('readonly', true);
                $('#EditarEvento #sucursales_edicion').trigger('change');

            }




            //--------------------------------------------------------------------
            var check = moment(calEvent.start, "DD-MM-YYYY HH:mm:ss").format('YYYY-MM-DD HH:mm:ss');
            var today = moment(new Date(), "DD-MM-YYYY HH:mm:ss").format('YYYY-MM-DD HH:mm:ss');
            //console.log("Fecha: "+check);
            // console.log(check <= today);
            if (check <= today) {
                $('#EditarEvento #fecha').attr('readonly', true);
                $('#EditarEvento #nombre').attr('readonly', true);
                $('#EditarEvento #descripcion').attr('readonly', true);
                $('#EditarEvento #sucursales_edicion').attr('disabled', true);
                $('#EditarEvento #EliminarChk').hide();
                $('#EditarEvento #EditarBtn').hide();
                $('#EditarEvento #EliminarBtn').hide();

            } else {
                $('#EditarEvento #fecha').attr('readonly', false);
                $('#EditarEvento #fecha').attr('readonly', false);
                $('#EditarEvento #nombre').attr('readonly', false);
                $('#EditarEvento #descripcion').attr('readonly', false);
                $('#EditarEvento #sucursales_edicion').attr('disabled', false);
                $('#EditarEvento #EliminarChk').show();
                $('#EditarEvento #EliminarBtn').show();
                $('#EditarEvento #EditarBtn').show();
                $('#EditarEvento #EditarBtn').attr('disabled', false);
            }
            //  $('#EditarSimulacro #masdetalles').attr('href', "detallesSimulacros.php?simulacro=" + calEvent.id);

            $('#EditarEvento').modal('show');
        },
        /* on select */
        CalendarApp.prototype.onSelect = function(start, end, allDay) {
            var check = moment(start).format('YYYY-MM-DD');
            var today = moment(new Date()).format('YYYY-MM-DD');

            // si el inicio de evento ocurre hoy o en el futuro mostramos el modal
            if (check >= today) {

                // éste es el código que tenías originalmente en el select
                $('#modalEvento #fecha').val(moment(start._i).format('YYYY-MM-DDTHH:mm:ss'));
                //$('#modalEvento #fecha').attr("readonly", true);
                //  $('#modalEvento #end').val(moment(end).format('YYYY-MM-DD'));
                $('#modalEvento').modal('show');
            }
            // si no, mostramos una alerta de error
            else {
                // alert("No se pueden crear eventos en el pasado!");
            }
            $this.$calendarObj.fullCalendar('unselect');
        }

    /* Initializing */
    CalendarApp.prototype.init = function() {
            //  this.enableDrag();
            /*  Initialize the calendar  */
            var date = new Date();
            var d = date.getDate();
            var m = date.getMonth();
            var y = date.getFullYear();
            var form = '';
            var today = new Date($.now());

            var defaultEvents = datsJson.data;

            var $this = this;
            $this.$calendarObj = $this.$calendar.fullCalendar({
                slotDuration: '00:15:00',
                /* If we want to split day time each 15minutes */
                minTime: '08:00:00',
                maxTime: '19:00:00',
                defaultView: 'month',
                handleWindowResize: true,

                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay'
                },
                events: defaultEvents,
                editable: false,
                // droppable: true, // this allows things to be dropped onto the calendar !!!
                eventLimit: true, // allow "more" link when too many events
                selectable: true,
                // drop: function(date) { $this.onDrop($(this), date); },
                select: function(start, end, allDay) { $this.onSelect(start, end, allDay); },
                eventClick: function(calEvent, jsEvent, view) { $this.onEventClick(calEvent, jsEvent, view); }

            });

            //on new event
            this.$saveCategoryBtn.on('click', function() {
                console.log($this.$categoryForm);
                var categoryName = $this.$categoryForm.find("input[name='category-name']").val();
                var categoryColor = $this.$categoryForm.find("select[name='category-color']").val();
                if (categoryName !== null && categoryName.length != 0) {
                    $this.$extEvents.append('<div class="calendar-events m-b-20" data-class="bg-' + categoryColor + '" style="position: relative;"><i class="fa fa-circle text-' + categoryColor + ' m-r-10" ></i>' + categoryName + '</div>')
                        //$this.enableDrag();
                }

            });
        },

        //init CalendarApp
        $.CalendarApp = new CalendarApp, $.CalendarApp.Constructor = CalendarApp

}(window.jQuery),

//initializing CalendarApp
$(window).on('load', function() {

    $.CalendarApp.init()
});

$.CalendarApp.init();