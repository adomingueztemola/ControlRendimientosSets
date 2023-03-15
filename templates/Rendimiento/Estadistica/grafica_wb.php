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
$filtradoAnio = $anio != '' ? "p.years='$anio'" : "1=1";
$DataWB = $obj_rendimiento->getWetBlue($filtradoAnio);
$labels = [];
$ArrayData = array(
  [
    "data" => [],
    "label" => "Área comprada (pie2)",
    "borderColor" => "#465003",
    "fill" => false
  ],

  [
    "data" => [],
    "label" => "Dif. Área comprada vs Medida	",
    "borderColor" => "#9e0c16",
    "fill" => false
  ],

  [
    "data" => [],
    "label" => "Recorte WB",
    "borderColor" => "#0a5ea0",
    "fill" => false
  ],

  [
    "data" => [],
    "label" => "Recorte Crust",
    "borderColor" => "#0042d0",
    "fill" => false
  ],

  [
    "data" => [],
    "label" => "Dif. Área WB Compra VS Crust",
    "borderColor" => "#ffd12e",
    "fill" => false
  ]
);
foreach ($DataWB as $key => $value) {
  array_push($labels, $DataWB[$key]['semanaProduccion']);
  array_push($ArrayData[0]['data'], $DataWB[$key]['areaComprada']);
  array_push($ArrayData[1]['data'], $DataWB[$key]['difAreaComprada']);
  array_push($ArrayData[2]['data'], $DataWB[$key]['recorteWB']);
  array_push($ArrayData[3]['data'], $DataWB[$key]['recorteCrust']);
  array_push($ArrayData[4]['data'], $DataWB[$key]['difAreaCompVsCrust']);
}

$json_encode = json_encode($ArrayData);
$str_semana = implode(',', $labels);
?>

<div height="600px">

  <canvas id="wb-chart" height="500vh" width="100%"></canvas>
</div>
<script>
  dataWB = <?= $json_encode ?>;
  labelsWbWeek = [<?= $str_semana ?>];
</script>
<script src="../assets/libs/chart.js/dist/Chart.min.js"></script>
<script src="../assets/scripts/graficasInit.js"></script>