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
$filtradoSemana = $semanaProduccion != '' ? "CONCAT(r.yearWeek, '-W', LPAD(r.semanaProduccion,2,'0'))='$semanaProduccion'" : "1=1";
$filtradoProceso = $proceso != '' ? "r.idCatProceso='$proceso'" : "1=1";
$filtradoPrograma = $programa != '' ? "r.idCatPrograma='$programa'" : "1=1";
$filtradoMateria = $materia != '' ? "r.idCatMateriaPrima='$materia'" : "1=1";
$DataRendimiento = $obj_inventario->getInventarioSupervisor($filtradoSemana, $filtradoProceso, $filtradoPrograma, $filtradoMateria);
?>

<div class="table-responsive">
    <table id="table-inventario" class="table table-sm">
        <thead>
            <tr class="">
                <th>AÑO/SEMANA</th>
                <th>LOTE</th>
                <th>PROGRAMA</th>
                <th>PROVEEDOR</th>
                <th>YIELD</th>
                <th>TESEO</th>
                <th>CAJAS</th>
                <th>EMPAQUE</th>
                <th class="table-secondary">RECOVERY</th>
                <th>CAJAS</th>
                <th class="table-danger">DIF</th>
                <th class="table-danger">%</th>
                <th class="table-danger">SCRAP ACT</th>
                <th class="table-warning">REWORK</th>
                <th class="table-warning">MOTIVO</th>
                <th>VENTAS</th>

            </tr>
        </thead>
        <tbody>
            <?php
            $count = 0;
            foreach ($DataRendimiento as $key => $value) {
                $fYieldTeseo = formatoMil($value['yieldInicialTeseo'], 2);
                $fPzasTeseo = formatoMil($value['pzasCortadasTeseo'], 0);
                $fCjTeseo = formatoMil($value['cajasTeseo'], 2);
                $fPzasEmpaque = formatoMil($value['totalEmp'], 0);
                $fCjEmpaque = formatoMil($value['cajasEmpaque'], 2);
                $fPzasSetsRechazadas = formatoMil($value['pzasSetsRechazadas'], 0);
                $fPorcSetsRechazoInicial = formatoMil($value['porcSetsRechazoInicial'], 2);
                $fPzasRech = formatoMil($value['totalRech'], 0);
                $fPzasRecuperadas = formatoMil($value['piezasRecuperadas'], 0);
                $fPzasVentas = formatoMil($value['pzasVentas'], 0);

                echo "<tr>
                    <td>{$DataRendimiento[$key]['semanaAnio']}</td>
                    <td><b>{$DataRendimiento[$key]['loteTemola']}</b></td>
                    <td>{$DataRendimiento[$key]['nPrograma']}</td>
                    <td>{$DataRendimiento[$key]['proveedores']}</td>
                    <td>{$fYieldTeseo}%</td>
                    <td>{$fPzasTeseo}</td>
                    <td>{$fCjTeseo}</td>
                    <td><b>{$fPzasEmpaque}</b></td>
                    <td class='table-secondary'>{$fPzasRecuperadas}</td>
                    <td>{$fCjEmpaque}</td>
                    <td class='table-danger'>{$fPzasSetsRechazadas}</td>
                    <td class='table-danger'>{$fPorcSetsRechazoInicial}%</td>
                    <td class='table-danger'>{$fPzasRech}</td>

                    <td class='table-warning'></td>
                    <td class='table-warning'></td>
                    <td><b>{$fPzasVentas}</b></td>
                    </tr>";
            }



            ?>

        </tbody>
        <!-- <tfoot>
            <tr class="bg-TWM text-white">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td><?= formatoMil($s_pzasCortadasTeseo, 0) ?></td>
                <td><?= formatoMil($s_setsCortadosTeseo, 0) ?></td>
                <td><?= formatoMil($s_pzasSinSetCortadas, 0) ?></td>


                <td><?= formatoMil($s_setsRecuperados, 0) ?></td>
                <td><?= formatoMil($s_piezasRecuperadas, 0) ?></td>
                <td><?= formatoMil($s_rzgoRecu, 0) ?></td>

                <td><?= formatoMil($s_setsRechazados, 0) ?></td>
                <td><?= formatoMil($s_pzasRechazadas, 0) ?></td>
                <td><?= formatoMil($s_rzgoRech, 0) ?></td>

                <td><?= formatoMil($s_pzasEmpacadas, 0) ?></td>
                <td><?= formatoMil($s_setsEmpacados, 0) ?></td>
                <td><?= formatoMil($s_rzgoEmp, 0) ?></td>

                <td><?= formatoMil($s_pzasTrabjRecuperadas, 0) ?></td>


                <td><?= formatoMil($s_pzasVnd, 0) ?></td>
                <td><?= formatoMil($s_setsVnd, 0) ?></td>
                <td><?= formatoMil($s_rzgoVnd, 0) ?></td>
                <td><?= formatoMil($s_pzasEsperadas, 0) ?></td>

                <td><?= formatoMil($s_pzasTotalAprobadas, 0) ?></td>

            </tr>
        </tfoot>-->
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