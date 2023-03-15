<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once('../../include/connect_mvc.php');
include("../../Models/Mdl_ConexionBD.php");
include("../../Models/Mdl_Rendimiento.php");
include('../../assets/scripts/cadenas.php');
$debug = 0;
$idUser = $_SESSION['CREident'];
$nameUser = $_SESSION['CREnombreUser'];
setlocale(LC_TIME, 'es_ES.UTF-8');

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


if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}
$obj_rendimiento = new Rendimiento($debug, $idUser);
$DataLotes = $obj_rendimiento->getLotesCueros($filtradoFecha, $filtradoProceso, $filtradoPrograma, $filtradoMateria );
?>
<div class="table-responsive">
    <table class="table table-sm" id="table-cueros">
        <thead>
            <tr>
                <th>#</th>

                <th>F. Engrase</th>
                <th>F. Empaque</th>
                <th>Sem. de Prod.</th>

                <th>Lote Temola</th>
                <th>Proceso</th>
                <th>Programa</th>

                <th>Materia Prima</th>
                <th>1s</th>
                <th>2s</th>
                <th>4s</th>
                <th>20</th>
                <th>Pzas. Rechaz.</th>

                <th>Total</th>
            </tr>

        </thead>
        <tbody>
            <?php
            $count=0;
                foreach ($DataLotes as $key => $value) {
                    $count++;
                    $f_piezasRechazadas= formatoMil($DataLotes[$key]['piezasRechazadas'],2);
                   echo "
                        <tr>
                            <td>{$count}</td>
                            <td>{$DataLotes[$key]['f_fechaEngrase']}</td>
                            <td>{$DataLotes[$key]['f_fechaEmpaque']}</td>
                            <td>{$DataLotes[$key]['semanaProduccion']}</td>

                            <td>{$DataLotes[$key]['loteTemola']}</td>
                            <td><small>{$DataLotes[$key]['c_proceso']}</small></td>
                            <td><small>{$DataLotes[$key]['n_materiaprima']}</small></td>
                            <td><small>{$DataLotes[$key]['n_programa']}</small></td>
                            <td>{$DataLotes[$key]['1s']}</td>
                            <td>{$DataLotes[$key]['2s']}</td>
                            <td>{$DataLotes[$key]['3s']}</td>
                            <td>{$DataLotes[$key]['4s']}</td>
                            <td>{$f_piezasRechazadas}</td>

                            <td>{$DataLotes[$key]['total_s']}</td>

                        </tr>
                   
                   ";

                }
            ?>
        </tbody>
    </table>

</div>

<script>
    $("#table-cueros").DataTable({});
</script>
