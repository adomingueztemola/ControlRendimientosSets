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
$filtradoAnio= $anio!=''?"p.years='$anio'":"1=1";
$DataM2Cza= $obj_rendimiento->getM2AutCza($filtradoAnio);
$labels=[];
$ArrayData=array(["data"=>[],
                "label"=>"Total Producido",
                "borderColor"=> "#465003",
                "fill"=>false],

                ["data"=>[],
                "label"=>"Dif. Área comprada vs Medida",
                "borderColor"=> "#9e0c16",
                "fill"=>false],

                ["data"=>[],
                "label"=>"Dif. Area WB vs Crust Piel",
                "borderColor"=> "#0a5ea0",
                "fill"=>false],

                ["data"=>[],
                "label"=>"Dif. Area Crust vs Teseo Piel",
                "borderColor"=> "#614124",
                "fill"=>false],

                ["data"=>[],
                "label"=>"Total Dif.Area",
                "borderColor"=> "#ffd12e",
                "fill"=>false]);
foreach ($DataM2Cza as $key => $value) {
  array_push($labels,$DataM2Cza[$key]['semanaProduccion']);
  array_push($ArrayData[0]['data'], $DataM2Cza[$key]['areaComprada']);
  array_push($ArrayData[1]['data'], $DataM2Cza[$key]['difAreaCompradaMedida']);
  array_push($ArrayData[2]['data'], $DataM2Cza[$key]['difAreaCompVsCrust']);
  array_push($ArrayData[3]['data'], $DataM2Cza[$key]['difAreaCrustTeseo']);
  array_push($ArrayData[4]['data'], $DataM2Cza[$key]['totalDifArea']);

}

$json_encode= json_encode($ArrayData);
$str_semana= implode(',', $labels);
?>
<div height="600px">


<canvas id="m2autocza-chart" height="500vh" width="100%"></canvas>
</div>
<script>
    dataCza=<?=$json_encode?>;
    labelsCzaWeek=[<?=$str_semana?>];
</script>
<script src="../assets/libs/chart.js/dist/Chart.min.js"></script>
<script src="../assets/scripts/graficasInit.js"></script>
