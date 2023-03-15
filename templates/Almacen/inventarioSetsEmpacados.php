<?php
define('INCLUDE_CHECK', 1);
session_start();
require_once('../../include/connect_mvc.php');
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
$filtradoEstado = $estado == '1' ? "r.rzgoEmp>0" : "1=1";
$filtradoEstado = $estado == '2' ? "r.rzgoEmp=0" : $filtradoEstado;
$DataRendimiento = $obj_inventario->getInventarioSetsEmpacados($filtradoSemana, $filtradoProceso, $filtradoPrograma, $filtradoMateria, $filtradoEstado);
?>

<div class="table-responsive">
    <table id="table-inventario" class="table table-sm display nowrap  table-hover table-bordered">
        <thead>
            <tr class="">
                <th>#</th>
                <th>Lote Temola</th>
                <th>Semana</th>
                <th>Proceso</th>
                <th>Programa</th>
                <th>Materia Prima</th>
                <th>Set's Empacados en Inv.</th>
                <th>Pzas Stock</th>
                <th>Pzas. Empacadas</th>
                <th class="table-danger"><i class="fas fa-lock"></i>Lim. Recuperación</th>
                <th class="table-danger"><i class="fas fa-lock"></i>Pzas. Lim. Recuperación</th>
                <th><i class="fas fa-unlock"></i>Pzas. Recuperadas</th>

            </tr>
        </thead>
        <tbody>
            <?php
            $count = 0;
            $s_piezasRecuperadas = 0;
            $s_porcRecuperacion = 0;
            $s_setsRecuperados = 0;
            $s_pzasSinSet = 0;
            $s_rzgoEmp = 0;
            foreach ($DataRendimiento as $key => $value) {
                $count++;
                $s_setsEmpacados += $DataRendimiento[$key]['setsTotalEmpInv'];
                $s_piezasEmpacadas += $DataRendimiento[$key]['totalEmpInv'];
                $s_rzgoEmp += $DataRendimiento[$key]['rzgoEmp'];
                $s_totales += $DataRendimiento[$key]['totalEmp'];


                $colorStck= $DataRendimiento[$key]['totalEmpInv']>0?"table-success":"table-danger";
                $setsEmpacados = formatoMil($DataRendimiento[$key]['setsTotalEmpInv'], 2);
                $piezasEmpacadas = formatoMil($DataRendimiento[$key]['totalEmpInv'], 0);
                $rzgoEmp = formatoMil($DataRendimiento[$key]['rzgoEmp'], 2);
                $totales = formatoMil($DataRendimiento[$key]['totalEmp'], 2);
                $f_porcLimitRecup = formatoMil($DataRendimiento[$key]['porcLimitRecup'], 2);
                $f_porcComplet = formatoMil($DataRendimiento[$key]['porcComplet'], 2);
                $f_pzasLimitRecup= formatoMil($DataRendimiento[$key]['pzasLimitRecup'], 2);
                $f_pzasLimitRecup= formatoMil($DataRendimiento[$key]['pzasLimitRecup'], 2);
                $pzasDisponibles= $DataRendimiento[$key]['pzasLimitRecup']-$DataRendimiento[$key]['totalRecuperado'];
                $f_pzasDisponibles= formatoMil($DataRendimiento[$key]['totalRecuperado'], 2);
                echo "<tr>
                    <td>$count</td>
                    <td>{$DataRendimiento[$key]['loteTemola']}</td>
                    <td>{$DataRendimiento[$key]['semanaAnio']}</td>
                    <td><small>{$DataRendimiento[$key]['c_proceso']}</small></td>
                    <td><small>{$DataRendimiento[$key]['n_programa']}</small></td>

                    <td><small>{$DataRendimiento[$key]['n_materia']}</small></td>
                    <td>{$setsEmpacados}</td>

                    <td class='{$colorStck}'>{$piezasEmpacadas}</td>
                    <td>{$totales}</td>
                    <td class='table-danger'>
                    {$f_porcLimitRecup}%
                    <div class='progress'>
                    <div class='progress-bar bg-success' role='progressbar' style='width: {$DataRendimiento[$key]['porcComplet']}%;' aria-valuenow='{$DataRendimiento[$key]['porcComplet']}' aria-valuemin='0' aria-valuemax='100'></div>
                  </div>
                    <small>% Completado: {$f_porcComplet}</small>
                    </td>
                    <td class='table-danger'>{$f_pzasLimitRecup}</td>
                    <td>{$f_pzasDisponibles}</td>
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
                <td><?= formatoMil($s_setsEmpacados) ?></td>

                <td><?= formatoMil($s_piezasEmpacadas) ?></td>
                <td><?= formatoMil($s_totales) ?></td>
                <td></td>
                <td></td>
                <td></td>
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