<?php
define('INCLUDE_CHECK', 1);
session_start();
require_once('../../include/connect_mvc.php');
include("../../Models/Mdl_ConexionBD.php");
include("../../Models/Mdl_Inventario.php");
include('../../assets/scripts/cadenas.php');

$idUser = $_SESSION['CREident'];
$nameUser = $_SESSION['CREnombreUser'];
setlocale(LC_TIME, 'es_ES.UTF-8');
$debug = 0;
$space = 1;
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}
$obj_inventario = new Inventario($debug, $idUser);
/************************** VARIABLES DE FILTRADO *******************************/
$proceso = !empty($_POST['proceso']) ? $_POST['proceso'] : '';
$programa = !empty($_POST['programa']) ? $_POST['programa'] : '';
$materia = !empty($_POST['materia']) ? $_POST['materia'] : '';
$estado = !empty($_POST['estado']) ? $_POST['estado'] : '';
$semanaProduccion = !empty($_POST['semanaProduccion']) ? $_POST['semanaProduccion'] : '';

/************************** FILTRADO *******************************/
$filtradoSemana = $semanaProduccion != '' ? "CONCAT(YEAR(r.fechaEmpaque), '-W', r.semanaProduccion)='$semanaProduccion'" : "1=1";
$filtradoProceso = $proceso != '' ? "r.idCatProceso='$proceso'" : "1=1";
$filtradoPrograma = $programa != '' ? "r.idCatPrograma='$programa'" : "1=1";
$filtradoMateria = $materia != '' ? "r.idCatMateriaPrima='$materia'" : "1=1";
$DataRendimiento = $obj_inventario->getInventariosGlobales($filtradoSemana, $filtradoProceso, $filtradoPrograma, $filtradoMateria, "2");
?>

<div class="table-responsive">
    <table id="table-inventario" class="table table-sm">
        <thead>
            <tr class="">
                <th>#</th>
                <th>Lote Temola</th>
                <th>SemanaProducción</th>
                <th>Proceso</th>
                <th>Programa</th>
                <th>Materia Prima</th>

                <th class="table-info">Área Final</th>

                <th class="table-success">M<sup>2</sup> Empacadas</th>
                <th class="table-primary">M<sup>2</sup> Vendidos</th>

            </tr>
        </thead>
        <tbody>
            <?php
            $count = 0;

            $s_areaFinal = 0;
            $s_totalEmp = 0;
           

            foreach ($DataRendimiento as $key => $value) {
                $count++;
                $areaFinal= formatoMil($DataRendimiento[$key]['areaFinal'],2);
                $totalEmp= formatoMil($DataRendimiento[$key]['totalEmp'],2);
                $totalVend= formatoMil($DataRendimiento[$key]['totalVend'],2);

                $s_areaFinal += $DataRendimiento[$key]['areaFinal'];
                $s_totalEmp += $DataRendimiento[$key]['totalEmp'];
                $s_totalVend += $DataRendimiento[$key]['totalVend'];

                echo "<tr>
                    <td>$count</td>
                    <td><b>{$DataRendimiento[$key]['loteTemola']}</b></td>
                    <td>{$DataRendimiento[$key]['semanaProduccion']}</td>
                    <td><small>{$DataRendimiento[$key]['c_proceso']}</small></td>
                    <td><small>{$DataRendimiento[$key]['n_programa']}</small></td>
                    <td><small>{$DataRendimiento[$key]['n_materia']}</small></td>

                    <td class='table-info'>{$areaFinal}</td>
                    <td class='table-success'>{$totalEmp}</td>
                    <td class='table-primary'>{$totalVend}</td>


                    </tr>";
            }



            ?>

        </tbody>
        <tfoot>
            <tr class="bg-TWM text-white">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td><?= formatoMil($s_areaFinal, 0) ?></td>
                <td><?= formatoMil($s_totalEmp, 0) ?></td>
                <td><?= formatoMil($s_totalVend, 0) ?></td>


            </tr>
        </tfoot>
    </table>
</div>

<script>
    $("#table-inventario").DataTable({
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
        }

    );
    $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('btn btn-TWM mr-1');
</script>