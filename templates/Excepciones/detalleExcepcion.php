<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../../include/connect_mvc.php";
include('../../Models/Mdl_ConexionBD.php');
include('../../Models/Mdl_ExcepcionDeStock.php');
include('../../assets/scripts/cadenas.php');

$debug = 0;
$idUser = $_SESSION['CREident'];
$obj_excepciones = new ExcepcionDeStock($debug, $idUser);
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}
$ident = !empty($_POST['ident']) ? $_POST['ident'] : "";

$DataSubLote = $obj_excepciones->detalleLoteXExcepcion($ident);
$DataSubLote = $DataSubLote == '' ? array() : $DataSubLote;
if (!is_array($DataSubLote)) {
    echo "<p class='text-danger'>Error, $DataSubLote</p>";
    exit(0);
}
?>

<div class="table-responsive">
    <table class="table table-sm mt-1">
        <thead class="bg-TWM text-white">
            <tr>
                <th>#</th>
                <th>Lote de Recuperación</th>
                <th>Cantidad</th>
                <th>Set's Recuperados</th>
                <th>Piezas Sin Set Recuperadas</th>
                <th>Set's Empacados</th>
                <th>Fecha de Recuperación</th>

            </tr>
        </thead>
        <tbody>
            <?php
            $count = 0;
            if (count($DataSubLote) <= 0) {
                echo "<tr><td colspan='7' class='text-danger text-center'>No hay lotes de recuperación</td></tr>";
            } else {
                foreach ($DataSubLote as $key => $value) {
                    $count++;
                    echo "
                <tr>
                    <td>$count</td>
                    <td>{$DataSubLote[$key]['loteTemola']}</td>
                    <td>{$DataSubLote[$key]['pzasTotales']}</td>
                    <td>{$DataSubLote[$key]['setsRecuperados']}</td>
                    <td>{$DataSubLote[$key]['pzasSinSet']}</td>
                    <td>{$DataSubLote[$key]['setsEmpacados']}</td>
                    <td>{$DataSubLote[$key]['f_fechaReg']}</td>
                </tr>";
                }
            }
            ?>

        </tbody>
    </table>
</div>