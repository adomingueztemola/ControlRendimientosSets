//console.log(datsJson.data);
$(document).ready(function() {
    var dt = $('#table-pedidos').DataTable({
        "dom": 'Bfrltip',
        "data": datsJson.data,
     
        "columns": [{
                "class": 'details-control',
                "orderable": false,
                "data": null,
                "defaultContent": '<div class="text-center"><i class="fas fa-plus-circle text-TWM"></i> </div>',
            },
            {
                "data": "numFactura"
            },
           
            {
                "data": "f_fechaFactura"
            },
            {
                "data": "nameProveedor"
            },
            {
                "data": "n_materia"
            },
           
            {
                "data": null,
                render: function(data, type, row) {
                    totalCuerosFacturados= Intl.NumberFormat('es-MX',{currency:'MXN'}).format(data.totalCuerosFacturados);
                    return totalCuerosFacturados;
                }
            },  
            {
                "data": null,
                render: function(data, type, row) {
                    totalCuerosEntregados= Intl.NumberFormat('es-MX',{currency:'MXN'}).format(data.totalCuerosEntregados);
                    return totalCuerosEntregados;
                }
            }, 
            {
                "data": null,
                render: function(data, type, row) {
                    cuerosXUsar= Intl.NumberFormat('es-MX',{currency:'MXN'}).format(data.cuerosXUsar);
                    return cuerosXUsar;
                }
            },           
           
            {
                "data": null,
                render: function(data, type, row) {
                    return "<button class='btn btn-xs btn-info' onclick='cargaFormAjuste("+data.id+", \""+data.numFactura+"-"+data.n_materia+"\")' data-toggle='modal' data-target='#modalExcepcion'><i class='fas fa-search'></i></button>";
                }
            }


        ],




    });


    //Add event listener for opening and closing details
    var o = this;
    $('#table-pedidos tbody').on('click', 'td.details-control', function() {
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