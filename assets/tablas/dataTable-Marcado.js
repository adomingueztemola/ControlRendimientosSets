//console.log(datsJson.data);
$(document).ready(function() {
    var dt = $('#table-historial').DataTable({
        "dom": 'Bfrltip',
        "data": datsJson.data,
        buttons: [{
            extend: 'copy',
            text: 'Copiar Formato',
            exportOptions: {

            },
            footer: true
        }, {
            extend: 'excel',
            text: 'Excel',
            exportOptions: {

            },
            footer: true

        }, {
            extend: 'pdf',
            text: 'Archivo PDF',
            exportOptions: {

            },
            orientation: "landscape",
            footer: true

        }, {
            extend: 'print',
            text: 'Imprimir',
            exportOptions: {

            },
            footer: true

        }],
        "columns": [{
                "class": 'details-control',
                "orderable": false,
                "data": null,
                "defaultContent": '<div class="text-center"><i class="fas fa-plus-circle text-TWM"></i> </div>',
            },
            {
                "data": "f_fecha"
            },
            {
                "data": "n_lote"
            },
            {
                "data": "n_programa"
            },
           
            {
                "data": null,
                render: function(data, type, row) {
                    pzasTotales= Intl.NumberFormat('es-MX',{currency:'MXN'}).format(data.pzasTotales);
                    return pzasTotales;
                }
            },
            {
                "data": null,
                render: function(data, type, row) {
                    _yield= Intl.NumberFormat('es-MX',{currency:'MXN'}).format(data.yield);
                    return _yield+"%";
                }
            },
            {
                "data": null,
                render: function(data, type, row) {
                    areaCrust= Intl.NumberFormat('es-MX',{currency:'MXN'}).format(data.areaCrust);
                    return areaCrust+" ft<sup>2</sup>";
                }
            },
            {
                "data": null,
                render: function(data, type, row) {
                    porcDecremento= Intl.NumberFormat('es-MX',{currency:'MXN'}).format(data.porcDecremento);
                    return porcDecremento+"%";
                }
            },
            {
                "data": null,
                render: function(data, type, row) {
                    areaCrustDecremento= Intl.NumberFormat('es-MX',{currency:'MXN'}).format(data.areaCrustDecremento);
                    return areaCrustDecremento+" ft<sup>2</sup>";
                }
            },
            {
                "data": null,
                render: function(data, type, row) {
                    area= Intl.NumberFormat('es-MX',{currency:'MXN'}).format(data.area);
                    return area+" ft<sup>2</sup>";
                }
            },
            {
                "data": "n_empleado"
            },
            {
                "data": null,
                render: function(data, type, row) {
                    return `
                    <button class="button btn btn-info btn-xs" data-toggle="modal" data-target="#modalCrust" onclick="agregarIdLote(${data.id}, '${data.n_lote}')"><i class=' fas fa-paste'></i></button>
                    <a target='_blank' href='../PDFReportes/Controller/tickets.php?op=getmarcado&data=${data.id}' class='button btn btn-xs btn-danger'><i class='fas fa-download'></i></a>`;
                }
            }
           

        ],




    });


    //Add event listener for opening and closing details
    var o = this;
    $('#table-historial tbody').on('click', 'td.details-control', function() {
        var tr = $(this).closest('tr');
        var row = dt.row(tr);

        if (row.child.isShown()) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        } else {
            // Open this row
            row.child(lanzaLoading(row.data())).show();
            tr.addClass('shown');
            row.child(cargaContenido(row.data()));
            //alert("hola");
        }
    });


    var lanzaLoading = function(d) {
        //alert(d.idPyme + " La varGlob ");
        return '<div id="DIV' + d.id + '" class="col-lg-12 ">' +
            '<div class="spinner-border" text-center role="status"><span class="sr-only">Loading...</span></div>' +
            '</div>';
    };

    var cargaContenido = function(d) {
        ejecutandoCarga(d.id);
    };
    $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('btn btn-TWM mr-1');
}); // Cierre de document ready