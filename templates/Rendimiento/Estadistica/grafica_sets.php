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
$DataSet= $obj_rendimiento->getSets($filtradoAnio);
$labels=[];
$ArrayData=array(["data"=>[],
                "label"=>"Sets Cortados Teseo",
                "borderColor"=> "#465003",
                "fill"=>false],

                ["data"=>[],
                "label"=>"% Rechazo inicial",
                "borderColor"=> "#9e0c16",
                "fill"=>false],

                ["data"=>[],
                "label"=>"% Sets Recuperados",
                "borderColor"=> "#0a5ea0",
                "fill"=>false],

                ["data"=>[],
                "label"=>"% Final de Rechazo",
                "borderColor"=> "#0042d0",
                "fill"=>false],

                ["data"=>[],
                "label"=>"Sets Empacados",
                "borderColor"=> "#ffd12e",
                "fill"=>false],

                ["data"=>[],
                "label"=>"Área real de Crust por set (pie2)",
                "borderColor"=> "#875fa0",
                "fill"=>false],

                ["data"=>[],
                "label"=>"Área de WB real por set (pie2)",
                "borderColor"=> "#005f0f",
                "fill"=>false],

                ["data"=>[],
                "label"=>"Costo de WB por set",
                "borderColor"=> "#717978",
                "fill"=>false]);
foreach ($DataSet as $key => $value) {
  array_push($labels,$DataSet[$key]['semanaProduccion']);
  array_push($ArrayData[0]['data'], $DataSet[$key]['setsCortadosTeseo']);
  array_push($ArrayData[1]['data'], $DataSet[$key]['porcRechazoFinal']);
  array_push($ArrayData[2]['data'], $DataSet[$key]['porcSetsRecuperados']);
  array_push($ArrayData[3]['data'], $DataSet[$key]['porcFinalRechazo']);
  array_push($ArrayData[4]['data'], $DataSet[$key]['setsEmpacados']);
  array_push($ArrayData[5]['data'], $DataSet[$key]['areaRealCrustXSet']);
  array_push($ArrayData[6]['data'], $DataSet[$key]['areaWBXSet']);
  array_push($ArrayData[7]['data'], $DataSet[$key]['costoWBXSet']);


}

$json_encode= json_encode($ArrayData);
$str_semana= implode(',', $labels);
?>

<div height="600px">
<canvas id="sets-chart" height="500vh" width="100%"></canvas>

</div>
<script>
    dataSets=<?=$json_encode?>;
    labelsWeek=[<?=$str_semana?>];
</script>
<script src="../assets/libs/chart.js/dist/Chart.min.js"></script>
<script src="../assets/scripts/graficasInit.js"></script>
