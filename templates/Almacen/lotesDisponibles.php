<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../../include/connect_mvc.php";
include('../../Models/Mdl_ConexionBD.php');
include('../../Models/Mdl_Rendimiento.php');
include('../../assets/scripts/cadenas.php');

$debug = 0;
$idUser = $_SESSION['CREident'];
$obj_rendimiento = new Rendimiento($debug, $idUser);
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}
$materiaPrima = !empty($_POST['materiaPrima']) ? $_POST['materiaPrima'] : "";
$procesos = !empty($_POST['procesos']) ? $_POST['procesos'] :  "";
$programas = !empty($_POST['programas']) ? $_POST['programas'] : '';
$tipo = !empty($_POST['tipo']) ? $_POST['tipo'] : '';

$filtradoTipo = "1=1";
$filtradoMateria = $materiaPrima != '' ? "r.idCatMateriaPrima='$materiaPrima'" : "1=1";
$filtradoProcesos = $procesos != '' ? "r.idCatProceso='$procesos'" : "1=1";
$filtradoPrograma = $programas != '' ? "r.idCatPrograma='$programas'" : "1=1";
$filtradoTipo = $tipo != '' ? "r.tipoProceso='$tipo'" : "1=1";




$DataLotes = $obj_rendimiento->getLotesDisponiblesVta($filtradoMateria, $filtradoProcesos, $filtradoPrograma, $filtradoTipo);
?>
<div class="table-responsive">
    <table id="table-pedidos" class="table table-sm">
        <thead>
            <tr class="">
                <th>#</th>
                <th>Tipo</th>
                <th>Semana Prod.</th>

                <th>Lote</th>
                <th>Programa</th>

                <th>Proceso de Secado</th>
                <th>Materia Prima</th>
                <th>Num. Factura</th>
                <th>Proveedor</th>
                <th>Usuario Registro</th>
                <th>Fecha Registro</th>

            </tr>
        </thead>
        <tbody>
            <?php
            $count = 0;
            $suma_unidades = 0;
            foreach ($DataLotes as $key => $value) {
                $count++;
                $suma_unidades += $DataLotes[$key]['almacenPT'];
                $lbl_tipo = $DataLotes[$key]['tipoProceso'] == '1' ? 'Sets' : 'M<sup>2</sup>';
            ?>
                <tr>
                    <td><?= $count ?></td>
                    <td><?= $lbl_tipo ?></td>
                    <td><?= $DataLotes[$key]['semanaProduccion'] ?></td>


                    <td><?= $DataLotes[$key]['loteTemola'] ?></td>
                    <td><?= $DataLotes[$key]['n_programa'] ?></td>
                    <td><?= $DataLotes[$key]['n_proceso'] ?></td>
                    <td><?= $DataLotes[$key]['n_materiaprima'] ?></td>
                    <td><?= $DataLotes[$key]['numFactura'] ?></td>
                    <td><?= $DataLotes[$key]['n_proveedor'] ?></td>

                    <td><?= $DataLotes[$key]['str_usuario'] ?></td>
                    <td><?= $DataLotes[$key]['f_fechaReg'] ?></td>



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
                <td>-</td>
                <td>-</td>
                <td>-</td>
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