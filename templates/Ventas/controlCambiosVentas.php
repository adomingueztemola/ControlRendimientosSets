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

$filtradoFecha = "DATE_FORMAT(ctrl.fechaReg, '%Y-%m-%d') BETWEEN '$date_start' AND '$date_end'";
$filtradoTipo = $tipo != '' ? "v.idTipoVenta='$tipo'" : "1=1";



$DataEdicion = $obj_ventas->getEdicionesVentas($filtradoFecha, $filtradoTipo);
?>
<div class="table-responsive">
    <table id="table-cambios" class="table table-sm">
        <thead>
            <tr class="">
                <th>#</th>
                <th>Núm. Fact.</th>
                <th>Núm. PL.</th>
                <th>Fecha Facturación</th>
                <th>Tipo de Venta</th>
                <th>Set's Facturados</th>
                <th>Motivo</th>
                <th>Empleado Edición</th>
                <th>Fecha Edición</th>

            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($DataEdicion as $key => $value) {
                $count++;
                $numPL= $DataEdicion[$key]['numPL']==""?"N/A":$DataEdicion[$key]['numPL'];
                $numFactura= $DataEdicion[$key]['numFactura']==""?"N/A":$DataEdicion[$key]['numFactura'];

            ?>
                <tr class="<?= $colorTable ?>">
                    <td><?= $count ?></td>
                    <td><?= $numFactura ?></td>
                    <td><?= $numPL ?></td>
                    <td><?= $DataEdicion[$key]['f_fechaFact'] ?></td>
                    <td><?= $DataEdicion[$key]['n_tipoventa'] ?></td>
                    <td><?= formatoMil($DataEdicion[$key]['_sets']) ?></td>

                    <td><?= $DataEdicion[$key]['motivo']?></td>
    
                    <td><?= $DataEdicion[$key]['str_usuario'] ?></td>
                    <td><?= $DataEdicion[$key]['f_fechaReg'] ?></td>



                </tr>
            <?php


            }
            ?>

        </tbody>

    </table>
</div>


<script>
    $("#table-cambios").DataTable({
            dom: 'Bfrltip',
            "aaSorting": [],

            drawCallback: function() {
                $('[data-toggle="tooltip"]').tooltip();
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