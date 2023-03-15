<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../../include/connect_mvc.php";
include('../../Models/Mdl_ConexionBD.php');
include('../../Models/Mdl_Venta.php');
include('../../assets/scripts/cadenas.php');
$debug = 0;
$idUser = $_SESSION['CREident'];
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}
$obj_venta = new Venta($debug, $idUser);

$date_start = !empty($_POST['date-start']) ? $_POST['date-start'] : "";
$date_end = !empty($_POST['date-end']) ? $_POST['date-end'] :  "";

$programa = !empty($_POST['programa']) ? $_POST['programa'] : '';
$materia = !empty($_POST['materia']) ? $_POST['materia'] : '';

/***************** CASTEO DE FECHAS ****************** */
if ($date_start != '' and $date_end != '') {
    $date_start = date("Y-m-d", strtotime(str_replace("/", "-", $date_start)));
    $date_end = date("Y-m-d", strtotime(str_replace("/", "-", $date_end)));
}


$filtradoFecha = ($date_start != '' and $date_end != '') ? "r.fechaEmpaque BETWEEN '$date_start' AND '$date_end'" : '1=1';
$filtradoPrograma = $programa != '' ? "r.idCatPrograma='$programa'" : "1=1";
$filtradoMateria = $materia != '' ? "r.idCatMateriaPrima='$materia'" : "1=1";


?>
<table class="mt-4 table table-sm" id="table-faltantes">
    <thead>
        <tr>
            <th>#</th>
            <th>Semana</th>
            <th>Tipo de Lote</th>
            <th>Lote Temola</th>
            <th>Materia Prima</th>
            <th>Programa</th>
            <th>Proceso</th>
            <th>Fecha de Empaque</th>
            <th>Total de Inventario</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $DataVenta = $obj_venta->getLotesXVender($filtradoFecha, $filtradoMateria, $filtradoPrograma);
        $count = 0;
        $suma_unidades = 0;
        $suma_sets = 0;
        foreach ($DataVenta as $key => $value) {
            $count++;
            $lblTipoLote = $DataVenta[$key]['tipoProceso'] == '1' ? "Set's" : "M<sup>2</sup>";

            echo "<tr>
                <td>{$count}</td>
                <td>{$DataVenta[$key]['semanaProduccion']}</td>
                <td>{$lblTipoLote}</td>

                <td><b>{$DataVenta[$key]['loteTemola']}</b></td>
                <td><small>{$DataVenta[$key]['n_materia']}</small></td>
                <td>{$DataVenta[$key]['n_programa']}</td>
                <td><small>{$DataVenta[$key]['n_proceso']}</small></td>
                <td>{$DataVenta[$key]['f_fechaEmpaque']}</td>
                <td>{$DataVenta[$key]['pzasTotalInventario']}</td> 
            </tr>";
        }
        ?>
    </tbody>
</table>

<script>
    $("#table-faltantes").DataTable({});
</script>