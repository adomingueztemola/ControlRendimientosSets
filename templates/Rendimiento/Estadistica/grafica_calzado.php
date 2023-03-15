<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../../../include/connect_mvc.php";
include('../../../Models/Mdl_ConexionBD.php');
include('../../../Models/Mdl_Rendimiento.php');
$debug = 0;
$idUser = $_SESSION['CREident'];
$nameUser = $_SESSION['CREnombreUser'];
$obj_rendimiento = new Rendimiento($debug, $idUser);
$anio = (!empty($_POST['anio']) and $_POST['anio'] != '') ? $_POST['anio'] : '';
$filtradoAnio = $anio != '' ? "YEAR(r.fechaFinal)='$anio'" : "1=1";
$DataCalzado = $obj_rendimiento->getM2Calzado($filtradoAnio);

$labels = [];
$ArrayData = array(
  [
    "data" => [],
    "label" => "Total Producido",
    "borderColor" => "#465003",
    "fill" => false
  ],

  [
    "data" => [],
    "label" => "Dif. Área WB vs Crust	",
    "borderColor" => "#9e0c16",
    "fill" => false
  ],

  [
    "data" => [],
    "label" => "Total Dif.Area",
    "borderColor" => "#0a5ea0",
    "fill" => false
  ]
);
foreach ($DataCalzado as $key => $value) {
  array_push($labels, $DataCalzado[$key]['semanaProduccion']);
  array_push($ArrayData[0]['data'], $DataCalzado[$key]['totalProducido']);
  array_push($ArrayData[1]['data'], $DataCalzado[$key]['difAreaWBCrust']);
  array_push($ArrayData[2]['data'], $DataCalzado[$key]['totalDifArea']);
}

$json_encode = json_encode($ArrayData);
$str_semana = implode(',', $labels);
?>
<div height="600px">
  <canvas id="clz-chart" height="500vh" width="100%"></canvas>

</div>

<script>
  dataClz = <?= $json_encode ?>;
  labelsClzWeek = [<?= $str_semana ?>];
</script>
<script src="../assets/libs/chart.js/dist/Chart.min.js"></script>
<script src="../assets/scripts/graficasInit.js"></script>