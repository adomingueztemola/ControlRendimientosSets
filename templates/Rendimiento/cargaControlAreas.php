<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once('../../include/connect_mvc.php');
include("../../Models/Mdl_ConexionBD.php");
include("../../Models/Mdl_Rendimiento.php");
include("../../Models/Mdl_Excepciones.php");

include('../../assets/scripts/cadenas.php');
$debug = 0;
$idUser = $_SESSION['CREident'];
$nameUser = $_SESSION['CREnombreUser'];
setlocale(LC_TIME, 'es_ES.UTF-8');
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}
$date_start = !empty($_POST['date-start']) ? $_POST['date-start'] : date("01/m/Y");
$date_end = !empty($_POST['date-end']) ? $_POST['date-end'] :  date("t/m/Y");
$proceso = !empty($_POST['proceso']) ? $_POST['proceso'] : '';
$programa = !empty($_POST['programa']) ? $_POST['programa'] : '';
$materia = !empty($_POST['materia']) ? $_POST['materia'] : '';
/***************** CASTEO DE FECHAS ****************** */

$date_start = date("Y-m-d", strtotime(str_replace("/", "-", $date_start)));
$date_end = date("Y-m-d", strtotime(str_replace("/", "-", $date_end)));

$filtradoFecha = "r.fechaEmpaque BETWEEN '$date_start' AND '$date_end'";
$filtradoProceso = $proceso != '' ? "r.idCatProceso='$proceso'" : "1=1";
$filtradoPrograma = $programa != '' ? "r.idCatPrograma='$programa'" : "1=1";
$filtradoMateria = $materia != '' ? "r.idCatMateriaPrima='$materia'" : "1=1";

$obj_rendimiento = new Rendimiento($debug, $idUser);
$DataLotes = $obj_rendimiento->getRendimientos($filtradoFecha, $filtradoProceso, $filtradoPrograma,
$filtradoProceso, $filtradoMateria, "r.estado='4'");
$DataLotes = Excepciones::validaConsulta($DataLotes);

?>
<div class="table-responsive">
    <table class="table table-sm" id="table-control">
        <thead>
            <tr>
                <th>#</th>

                <th>Semana de Producción</th>
                <th>Fecha de Engrase</th>
                <th>Fecha de Empaque</th>

                <th>Lote</th>
                <th>Proceso</th>
                <th>Programa</th>

                <th>Materia Prima</th>
                <th>Área WB</th>
                <th>Área Crust</th>

                <th>Perdida Área WB a Crust</th>
                <th>Perdida Área Crust a Teseo</th>
            </tr>

        </thead>
        <tbody>
            <?php
            $count = 1;
            foreach ($DataLotes as $key => $value) {
                $f_perdidaAreaCrustTeseo = formatoMil($DataLotes[$key]['perdidaAreaCrustTeseo'], 2);
                $f_perdidaAreaWBCrust = formatoMil($DataLotes[$key]['perdidaAreaWBCrust'], 2);
                $f_areaWB = formatoMil($DataLotes[$key]['areaWB'], 2);
                $f_areaCrust = formatoMil($DataLotes[$key]['areaCrust'], 2);

                echo "<tr>
                    <td>{$count}</td>
                    <td>{$DataLotes[$key]['semanaProduccion']}</td>
                    <td>{$DataLotes[$key]['f_fechaEngrase']}</td>
                    <td>{$DataLotes[$key]['f_fechaEmpaque']}</td>

                    <td><b>{$DataLotes[$key]['loteTemola']}</b></td>
                    <td>{$DataLotes[$key]['c_proceso']}</td>
                    <td>{$DataLotes[$key]['n_programa']}</td>
                    <td>{$DataLotes[$key]['n_materia']}</td>
                    <td>{$f_areaWB}</td>
                    <td>{$f_areaCrust}</td>
                    <td>{$f_perdidaAreaWBCrust}</td>
                    <td>{$f_perdidaAreaCrustTeseo}</td>

               </tr>";
                $count++;
            }

            ?>

        </tbody>
    </table>

</div>
<script>
    $("#table-control").DataTable({});
</script>