<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../../include/connect_mvc.php";
include('../../Models/Mdl_ConexionBD.php');
include('../../Models/Mdl_Venta.php');
include('../../assets/scripts/cadenas.php');

$debug = 0;
$idUser = $_SESSION['CREident'];
$obj_ventas = new Venta($debug, $idUser);
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}
$date_start = !empty($_POST['date-start']) ? $_POST['date-start'] : date("01/m/Y");
$date_end = !empty($_POST['date-end']) ? $_POST['date-end'] :  date("t/m/Y");
$tipo = !empty($_POST['tipo']) ? $_POST['tipo'] : '';

/***************** CASTEO DE FECHAS ****************** */

$date_start = date("Y-m-d", strtotime(str_replace("/", "-", $date_start)));
$date_end = date("Y-m-d", strtotime(str_replace("/", "-", $date_end)));

$filtradoFecha = "v.fechaFact BETWEEN '$date_start' AND '$date_end'";
$filtradoTipo = $tipo != '' ? "v.idTipoVenta='$tipo'" : "1=1";




$DataVentas = $obj_ventas->getVentasCerradas($filtradoFecha, $filtradoTipo);
?>
<div class="table-responsive">
    <table id="table-pedidos" class="table table-sm">
        <thead>
            <tr class="">
                <th>#</th>
                <th>Fecha Factura</th>
                <th>Núm. Fact.</th>
                <th>Núm. PL</th>
                <th>Unidades Facturadas</th>
                <th>Set's Facturados</th>
                <th>Tipo de Venta</th>
                <th>Devoluc.</th>
                <th>Lotes</th>

                <th>Usuario Registro</th>
                <th>Fecha Registro</th>
                <th>Acciones</th>

            </tr>
        </thead>
        <tbody>
            <?php
            $count = 0;
            $suma_UnidadesFact = 0;
            $suma_SetsFact = 0;
            $suma_dev = 0;

            foreach ($DataVentas as $key => $value) {
                $count++;
                $suma_UnidadesFact += $DataVentas[$key]['unidFact'];
                $suma_SetsFact += $DataVentas[$key]['_sets'];
                $suma_dev  += $DataVentas[$key]['totalDevol'];
                $colorTable = $DataVentas[$key]['estado'] == '2' ? '' : 'table-danger';
                $hiddenTable = $DataVentas[$key]['estado'] == '2' ? '' : 'hidden';

                $btnLotes = "<button title='Ver Despliegue de Ventas' data-toggle='modal' data-target='#modalLotes' onclick='cargaLotes({$DataVentas[$key]['id']})' class='btn button btn-xs  btn-outline-success'><i class='fas fa-boxes'></i></button>";
                $btnEdicion = "<button $hiddenTable title='Editar Datos de Facturación de Ventas' data-toggle='modal' data-target='#modalEdicion' onclick='cargaEdicion({$DataVentas[$key]['id']})' class='btn button btn-xs  btn-outline-primary'><i class='fas fa-pencil-alt'></i></button>";
                $sets= $value['idTipoVenta']!='3'?formatoMil($DataVentas[$key]['_sets']):'<span class="text-muted">N/A</span>';
            ?>
                <tr class="<?= $colorTable ?>">
                    <td><?= $count ?></td>
                    <td><?= $DataVentas[$key]['f_fechaFact'] ?></td>

                    <td><?= $DataVentas[$key]['numFactura'] ?></td>
                    <td><?= $DataVentas[$key]['numPL'] ?></td>
                    <td><?= formatoMil($DataVentas[$key]['unidFact']) ?></td>
                    <td><?= $sets ?></td>

                    <td><?= $DataVentas[$key]['n_tipo'] ?></td>
                    <td  data-toggle="tooltip" data-placement="top" title="<?= $DataVentas[$key]['rmas'] ?>"><?= $DataVentas[$key]['totalDevol'] ?></td>
                    <td><?= $DataVentas[$key]['lotes'] ?></td>

                    <td><?= $DataVentas[$key]['str_usuario'] ?></td>
                    <td><?= $DataVentas[$key]['f_fechaReg'] ?></td>
                    <td> <?= $btnEdicion ?> <?= $btnLotes ?> </td>



                </tr>
            <?php


            }
            ?>

        </tbody>
        <tfoot>
            <tr class="bg-TWM text-white">
                <td>Totales:</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td><?= formatoMil($suma_UnidadesFact) ?></td>
                <td><?= formatoMil($suma_SetsFact) ?></td>


                <td>-</td>

                <td><?= formatoMil($suma_dev,0) ?></td>

                <td>-</td>
                <td>-</td>

                <td>-</td>
                <td>-</td>




            </tr>
        </tfoot>
    </table>
</div>


<script>
    $("#table-pedidos").DataTable({
            dom: 'Bfrltip',
            "aaSorting": [],
            drawCallback: function(settings, json) {
                $('[data-toggle="tooltip"]').tooltip('update');
                //$("#list-of-product tbody tr > td").tooltip('hide');
            },
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
        }

    );
    $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('btn btn-TWM mr-1');
</script>