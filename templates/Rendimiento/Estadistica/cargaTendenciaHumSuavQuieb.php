<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../../../include/connect_mvc.php";
include('../../../Models/Mdl_ConexionBD.php');
include('../../../Models/Mdl_Rendimiento.php');
include('../../../assets/scripts/cadenas.php');

$debug = 0;
$idUser = $_SESSION['CREident'];
$nameUser = $_SESSION['CREnombreUser'];
$obj_rendimiento = new Rendimiento($debug, $idUser);
$anio = (!empty($_POST['anio']) and $_POST['anio'] != '') ? $_POST['anio'] : date('Y');
$tipo = (!empty($_POST['tipo']) and $_POST['tipo'] != '') ? $_POST['tipo'] : "0";

switch ($tipo) {
    case '1':
        $DataArea = $obj_rendimiento->getWetBlue(" YEAR(r.fechaEmpaque)='$anio'");
        $DataTendencias = $obj_rendimiento->getTendenciasHumSuabQuieb($anio, 'r.tipoProceso = "1"');

        break;
    case '2':
        $DataArea = $obj_rendimiento->getWetBlue(" YEAR(r.fechaEmpaque)='$anio'");
        break;
    case '3':
        $DataArea = $obj_rendimiento->getM2AutCza(" YEAR(r.fechaEmpaque)='$anio'");
        $DataTendencias = $obj_rendimiento->getTendenciasHumSuabQuieb($anio,  " r.tipoProceso = '2' 
        AND r.tipoMateriaPrima = '1' ");

        break;
    case '4':
        $DataArea = $obj_rendimiento->getM2AutPiel(" YEAR(r.fechaEmpaque)='$anio'");
        $DataTendencias = $obj_rendimiento->getTendenciasHumSuabQuieb($anio,  " r.tipoProceso = '2' 
        AND r.tipoMateriaPrima = '2' ");
        break;
    
    default:
        $DataArea = $obj_rendimiento->getTodosLotes(" YEAR(r.fechaEmpaque)='$anio'");
        $DataTendencias = $obj_rendimiento->getTendenciasHumSuabQuieb($anio,  "1=1");
        break;
}
$DataTendencias = $DataTendencias == '' ? array() : $DataTendencias;
if (!is_array($DataTendencias)) {
    echo "<p class='text-danger'>Error, $DataTendencias</p>";
    exit(0);
}


#ARRAY DE LAS TENDENCIAS
$array_semanas = ["'x'"];
$array_suavidad = ["'Suavidad'"];
$array_quiebre = ["'Quiebre'"];
$array_humedad = ["'Humedad'"];
$array_area = ["'Perdida Área WB a Crust'"];

foreach ($DataTendencias as $key => $value) {

    array_push($array_semanas, $DataTendencias[$key]["semanaProduccion"]);
    array_push($array_quiebre, formatoMil($DataTendencias[$key]["promQuiebre"]));
    array_push($array_humedad, formatoMil($DataTendencias[$key]["promHumedad"]));
    array_push($array_suavidad, formatoMil($DataTendencias[$key]["promSuavidad"]));
    //busqueda de Area por Semana
   $indexArea= getAreaXSemana($DataArea, $DataTendencias[$key]["semanaProduccion"]);
   if($indexArea!=''){
    array_push($array_area, formatoMil($DataArea[$indexArea]["difAreaCompVsCrust"]));

   }else{
    array_push($array_area, formatoMil('0'));

   }
}
/*foreach ($DataArea as $key2 => $value) {
    array_push($array_area, formatoMil($DataArea[$key2]["difAreaCompVsCrust"]));
}*/
$str_semanas = implode(",", $array_semanas);
$str_quiebre = implode(",", $array_quiebre);
$str_suavidad = implode(",", $array_suavidad);
$str_humedad = implode(",", $array_humedad);
$str_area = implode(",", $array_area);
function getAreaXSemana($ArrayArea, $semana){
    $found_key = array_search($semana, array_column($ArrayArea, 'semanaProduccion'));
    return $found_key;

}
?>
<div id="axis-timezone"></div>
<script src="../assets/extra-libs/c3/d3.min.js"></script>

<script src="../assets/extra-libs/c3/c3.min.js"></script>
<script>
    $(function() {
        var t = c3.generate({
            bindto: "#axis-timezone",
            size: {
                height: 400
            },
            color: {
                pattern: ["#FF5733", "#735E59", "#87DA6F", "#2E8FDC"]
            },
            data: {
                x: "x",
                xFormat: "%Y",
                columns: [
                    [<?= $str_semanas ?>],
                    [<?= $str_quiebre ?>],
                    [<?= $str_humedad ?>],
                    [<?= $str_suavidad ?>],
                    [<?= $str_area ?>]


                ]
            },
            axis: {
                x: {
                    /*  type: "timeseries",
                      localtime: !1,
                      tick: {
                             format: "%Y-%m-%d %H:%M:%S"
                      }*/
                }
            },
            grid: {
                y: {
                    show: !0
                }
            }
        });
    });
</script>