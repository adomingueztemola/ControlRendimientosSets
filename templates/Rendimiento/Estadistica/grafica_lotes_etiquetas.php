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
$anio = (!empty($_POST['anio']) and $_POST['anio'] != '') ? $_POST['anio'] : date("Y");
$Data = $obj_rendimiento->getLotesEtiqXSemana($anio);
$labels = [];
$ArrayData = array();
foreach ($Data as $key => $value) {

  array_push($ArrayData, ["Sem.".$Data[$key]['semanaProduccion'], $Data[$key]['total']]);
}
if (count($ArrayData) > 0) {
  $json_encode = json_encode($ArrayData);
?>


  <div id="g-line" style="height:400px;"></div>
  <script>
    dataLine = <?= $json_encode ?>;
  </script>
  <script src="../assets/libs/echarts/dist/echarts-en.min.js"></script>
  <script src="../assets/scripts/graficasInitLineChart.js"></script>

<?php } else {
  echo '<div class="alert alert-primary" role="alert">
          No hay datos actualizados para ' . $anio . '
        </div>';
} ?>