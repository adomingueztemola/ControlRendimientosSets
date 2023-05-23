<?php
$debug = 0;
session_start();
define('INCLUDE_CHECK', 1);
require_once "../../include/connect_mvc.php";
include('../../assets/scripts/cadenas.php');
$idUser = $_SESSION['CREident'];
if ($debug == 1) {
    print_r($_POST);
} else {
    error_reporting(0);
}
$date_start = !empty($_POST['date-start']) ? $_POST['date-start'] : "";
$date_end = !empty($_POST['date-end']) ? $_POST['date-end'] :  "";
$programa = !empty($_POST['programa']) ? $_POST['programa'] : '';

$date_start = $date_start != "" ? date("Y-m-d", strtotime(str_replace("/", "-", $date_start))) : $date_start;
$date_end = $date_end != "" ? date("Y-m-d", strtotime(str_replace("/", "-", $date_end))) : $date_end;
?>
<style>
    td.details-control {
        background: url('../assets/images/details_open.png') no-repeat center center;
        cursor: pointer;
    }

    tr.details td.details-control {
        background: url('../assets/images/details_close.png') no-repeat center center;
    }
</style>
<div class="row">
    <div class="col-lg-12 col-md-12 col-xs-12 col-sm-12 table-responsive">
        <table class="table table-sm" id="table-mediciones">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Lote</th>
                    <th scope="col">Programa</th>
                    <th scope="col">Lados Totales</th>
                    <th scope="col">Área Total Dm<sup>2</sup></th>
                    <th scope="col">Área Total Ft<sup>2</sup></th>
                    <th scope="col">Área Total Red. Ft<sup>2</sup></th>
                    <th scope="col">Dif. Área Ft<sup>2</sup></th>
                    <th scope="col">Acción</th>

                    <th scope="col">Fecha Registro</th>
                    <th scope="col">Usuario Registro</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
</div>

<script src="../assets/extra-libs/datatables.net/js/jquery.dataTables.min-ESP.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>
<script src="../dist/js/pages/datatable/datatable-basic.init.js"></script>
<script>
    var dt = $("#table-mediciones").DataTable({
        ajax: {
            "url": "../Controller/medicion.php?op=getreportemedicion",
            "type": "POST",
            "data": {
                date_start: "<?= $date_start ?>",
                date_end: "<?= $date_end ?>",
                programa: "<?= $programa ?>"
            }
        },
        "aaSorting": [],
        'aoColumnDefs': [{
                targets: 0,
                class: 'details-control',
                orderable: false,
                data: null,
                defaultContent: '',
            },
            {
                'targets': 8,
                "bSortable": false,

                'searchable': false,
                "bSearchable": false,
                'orderable': false,
                'className': 'dt-body-center',
                'render': function(data, type, full, meta) {
                    // checked = array[1] == '0' ? '' : 'checked';
                    return '<button class="btn btn-danger btn-xs"  onclick="eliminarLote(\'' + data + '\')"><i class="fas fa-trash-alt"></i></button>';
                }
            }
        ],


    })
    // Array to track the ids of the details displayed rows
    var detailRows = [];
    $('#table-mediciones tbody').on('click', 'tr td.details-control', function() {
        var tr = $(this).closest('tr');
        var row = dt.row(tr);
        var idx = detailRows.indexOf(tr.attr('id'));

        if (row.child.isShown()) {
            tr.removeClass('details');
            row.child.hide();

            // Remove from the 'open' array
            detailRows.splice(idx, 1);
        } else {
            tr.addClass('details');
            row.child(format(row.data())).show();
            $("#reporte-medicion").DataTable({
                dom: 'Bfrltip',
                "aaSorting": [],
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

                }]

            })
            $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('btn btn-TWM mr-1');
            // Add to the 'open' array
            if (idx === -1) {
                detailRows.push(tr.attr('id'));
            }
        }
    });

    function format(d) {
        $.ajax({
            url: '../Controller/medicion.php?op=getdetreporte',
            data: {
                id: d[0]
            },
            type: 'POST',
            async: false,
            dataType: "json",
            success: function(respuesta) {
                tabla = `
                <div class="row">
                <div class="col-9">
                <table class="table table-striped table-sm display nowrap table-secondary" id="reporte-medicion">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Número de serie</th>
                            <th>Área real DM<sup>2</sup></th>
                            <th>Área real FT<sup>2</sup></th>
                            <th>Redondeo</th>
                        </tr>
                    </thead>
                    <tbody>`;
                if (!respuesta.length) {
                    tabla += `
                        <tr>
                            <td colspan='4'>Sin registro de lados en el lote</td>
                        </tr>
                    `
                } else {
                    count = 1;

                    respuesta.forEach(element => {
                        areaDM = element.areaDM.toLocaleString('es-MX')
                        areaFT = element.areaFT.toLocaleString('es-MX')
                        areaRedondFT = parseFloat(element.areaRedondFT).toFixed(2).toLocaleString('es-MX')
                        tabla += `
                        <tr>
                            <td>${count}</td>
                            <td>${element.numSerie}</td>
                            <td>${areaDM}</td>
                            <td>${areaFT}</td>
                            <td>${areaRedondFT}</td>

                        </tr> `;
                        count++;
                    });
                }

            },


        });
        return tabla + "</tbody></table></div></div>";
    }

    function eliminarLote(info) {
        array = info.split('|')
        Swal.fire({
            title: "¿Está seguro de la eliminación del lote " + array[1] + "?",
            text: "Al eliminar su lote se eliminará todo paquete perteneneciente",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "green",
            confirmButtonText: "Acepto",
            cancelButtonText: "Cancelar",
            closeOnConfirm: false
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    type: 'POST',
                    url: '../Controller/medicion.php?op=eliminarlotemedicion',
                    data: {
                        id: array[0],
                        loteTemola: array[1]
                    },
                    success: function(respuesta) {
                        var resp = respuesta.split('|');
                        if (resp[0] == 1) {
                            Swal.fire("Eliminado", resp[1], "success");
                            setTimeout(() => {
                                update()
                            }, 1000);
                        } else {
                            notificaBad(resp[1])

                        }
                    },
                    beforeSend: function() {
                    }
                });

            }

        })
    }
</script>