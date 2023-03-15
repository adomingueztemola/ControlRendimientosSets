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
$Data = $obj_rendimiento->getPedidosVsVentas($anio);
$labels = [];
$ArrayData = array();
foreach ($Data as $key => $value) {

  array_push($ArrayData, [
    "y" => $Data[$key]['semana'],
    "a" => $Data[$key]['total_p'],
    "b" => $Data[$key]['total_v'],
  ]);
}
if (count($ArrayData) > 0) {
  $json_encode = json_encode($ArrayData);
?>


  <div id="morris-bar-chart"></div>
  <script>
    data = <?= $json_encode ?>;
  </script>
  <script src="../assets/libs/raphael/raphael.min.js"></script>
  <script src="../assets/libs/morris.js/morris.min.js"></script>
  <script src="../assets/scripts/graficasInitMorris.js"></script>
  
<?php } else {
  echo '<div class="alert alert-primary" role="alert">
          No hay datos actualizados para ' . $anio . '
        </div>';
} ?>