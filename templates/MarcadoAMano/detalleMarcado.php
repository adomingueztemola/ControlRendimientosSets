<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once('../../include/connect_mvc.php');
include("../../Models/Mdl_ConexionBD.php");
include("../../Models/Mdl_MarcadoAMano.php");
include('../../assets/scripts/cadenas.php');
$debug = 0;
$idUser = $_SESSION['CREident'];
$nameUser = $_SESSION['CREnombreUser'];
$ident = !empty($_POST['ident']) ? $_POST['ident'] : '';

$obj_marcado = new MarcadoAMano($debug, $idUser);
$DataRecuperacion = $obj_marcado->getRecuperacion($ident);
$DataConteo = $obj_marcado->getMetricasConteoTeseo($ident);
$tableConteo = "";
$tableHeader = "";

foreach ($DataConteo as $key => $value) {
    $f_preliminar = formatoMil($DataConteo[$key]['preliminar'], 0);
    $tableConteo .= "<td>{$f_preliminar}</td>";
    $tableHeader .= "<td><b>{$DataConteo[$key]['nombre']}</b></td>";
}
?>
<table class="table table-sm">
    <thead class="table-secondary">
        <tr>
            <th colspan="4" class="text-center">PIEZAS CONTADOS EN MARCADO A MANO</th>
        </tr>
        <tr>
            <?= $tableHeader ?>
        </tr>
    </thead>
    <tbody>
        <tr>
            <?= $tableConteo ?>
        </tr>
        <tr>

            <?php
            $DataConteo = $obj_marcado->getRecuperacion($ident);
            $tableConteo = "";
            $tableHeader = "";
            if (count($DataConteo) > 0) {
                foreach ($DataConteo as $key => $value) {
                    $f_total = formatoMil($DataConteo[$key]['cantidad'], 0);
                    $tableConteo .= "<td>{$f_total}</td>";
                    $tableHeader .= "<td><b>{$DataConteo[$key]['n_pzasVolante']}</b></td>";
                }
            ?>
        <tr>
            <th colspan="4" class="text-center table-danger">PIEZAS RECHAZADAS EN MARCADO A MANO</th>
        </tr>
        <tr class="table-secondary">
            <?= $tableHeader ?>
        </tr>

        <tr>
            <?= $tableConteo ?>
        </tr>

    <?php
            }

    ?>

    </tbody>
</table>