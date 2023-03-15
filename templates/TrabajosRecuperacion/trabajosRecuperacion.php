<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../../include/connect_mvc.php";
include('../../assets/scripts/cadenas.php');
$debug = 0;
$idUser = $_SESSION['CREident'];
if ($debug == 1) {
    print_r($_POST);
} else {
    error_reporting(0);
}
$date_start = !empty($_POST['date-start']) ? $_POST['date-start'] : date("01/m/Y");
$date_end = !empty($_POST['date-end']) ? $_POST['date-end'] :  date("t/m/Y");
/***************** CASTEO DE FECHAS ****************** */
$date_start = date("Y-m-d", strtotime(str_replace("/", "-", $date_start)));
$date_end = date("Y-m-d", strtotime(str_replace("/", "-", $date_end)));
$filtradoFecha = "mr.fechaEntrega BETWEEN '$date_start' AND '$date_end'";
$obj_trabajos = new TrabajosRecupera($debug, $idUser);
?>
<table class="mt-4 table table-sm" id="table-faltantes">
    <thead>
        <tr>
            <th>#</th>
            <th>Fecha</th>
            <th>Tipo de Lote</th>
            <th>Lote Retrabajado</th>
            <th>Defecto</th>
            <th>Programa</th>
            <th>Entrega</th>

            <th>Trabajador Recibió</th>
            <th>Reasignación</th>
            <th>Total Recuperación</th>
            <th>Observaciones</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $DataRecuperadas = $obj_trabajos->getRecuperaciones($filtradoFecha);
        $count = 0;
        foreach ($DataRecuperadas as $key => $value) {
            $count++;
            $lblTipoLote = $DataRecuperadas[$key]['tipoRendInicio'] == '1' ? "Lote Registrado" : "Lote No Identificado";
            $lblLoteTrabajado = $DataRecuperadas[$key]['tipoRendInicio'] == '1' ? $DataRecuperadas[$key]['nLoteInicial'] : $DataRecuperadas[$key]['nombreRendInicio'];
            $f_TotalInicial = formatoMil($DataRecuperadas[$key]['totalInicial'], 0);
            $f_TotalRecuperado = formatoMil($DataRecuperadas[$key]['totalRecuperacion'], 0);
            $lblDefecto = $DataRecuperadas[$key]['n_defecto'] != '' ? $DataRecuperadas[$key]['n_defecto'] : "<i>N/A</i>";
            $lblPorc = formatoMil($DataRecuperadas[$key]['porcPerdidaRecuperacion'], 2) . '%';

            echo "<tr>
                <td>{$count}</td>
                <td>{$DataRecuperadas[$key]['f_fecha']}</td>
                <td>{$lblTipoLote}</td>
                <td>{$lblLoteTrabajado}</td>
                <td>{$lblDefecto}</td>
                <td>{$DataRecuperadas[$key]['n_programa']}</td>
                <td>{$DataRecuperadas[$key]['f_fechaFinal']}</td>
                <td>{$DataRecuperadas[$key]['nombreCompletoTrabajador']}</td>

                <td>{$DataRecuperadas[$key]['nLoteRecup']}</td>
                <td>{$f_TotalRecuperado}</td>

                <td>{$DataRecuperadas[$key]['observaciones']}</td>

            </tr>";
        }

        ?>
    </tbody>
</table>

<script>
    $("#table-faltantes").DataTable({});
</script>