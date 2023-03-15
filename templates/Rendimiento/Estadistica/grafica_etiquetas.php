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
$anio= (!empty($_POST['anio']) AND $_POST['anio']!='' )?$_POST['anio']:'';
$filtradoAnio= $anio!=''?"YEAR(r.fechaFinal)='$anio'":"1=1";

$DataEtiquetas= $obj_rendimiento->getM2Etiquetas($filtradoAnio);

$labels=[];
$ArrayData=array(["data"=>[],
                "label"=>"Total Producido",
                "borderColor"=> "#465003",
                "fill"=>false],

                ["data"=>[],
                "label"=>"Dif. Área WB vs Crust	",
                "borderColor"=> "#9e0c16",
                "fill"=>false],

                ["data"=>[],
                "label"=>"Total Dif.Area",
                "borderColor"=> "#0a5ea0",
                "fill"=>false]);
foreach ($DataEtiquetas as $key => $value) {
  array_push($labels,$DataEtiquetas[$key]['semanaProduccion']);
  array_push($ArrayData[0]['data'], $DataEtiquetas[$key]['totalProducido']);
  array_push($ArrayData[1]['data'], $DataEtiquetas[$key]['difAreaWBCrust']);
  array_push($ArrayData[2]['data'], $DataEtiquetas[$key]['totalDifArea']);
}

$json_encode= json_encode($ArrayData);
$str_semana= implode(',', $labels);
?>

<div height="600px">

<canvas id="etq-chart" height="500vh" width="100%"></canvas>
</div>
<script>
    dataEtiquetas=<?=$json_encode?>;
    labelsEtqWeek=[<?=$str_semana?>];
</script>
<script src="../assets/libs/chart.js/dist/Chart.min.js"></script>
<script src="../assets/scripts/graficasInit.js"></script>
