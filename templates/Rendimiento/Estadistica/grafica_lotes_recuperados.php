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
$week = (!empty($_POST['week']) and $_POST['week'] != '') ? $_POST['week'] : date('W');

$Data = $obj_rendimiento->getRecuperacionSemanal($week);
$Data = $Data == '' ? array() : $Data;
if (!is_array($Data)) {
  echo "<p class='text-danger'>Error, $Data</p>";
  exit(0);
}

#ARRAY DE LAS TENDENCIAS
$array_semanas = ["'Recuperado'"];
$array_recuperacion = ["'Empacado'"];
$array_lotes = ["'Lotes'"];

foreach ($Data as $value) {
  array_push($array_semanas, $value["totalRecuperado"]);
  array_push($array_recuperacion, $value["totalEmpacado"]);
  array_push($array_lotes, $value["loteTemola"]);

}

$str_semanas = implode(",", $array_semanas);
$str_recuperacion = implode(",", $array_recuperacion);
$str_lotes = implode(",", $array_lotes);

?>
<div id="rotated-axis"></div>
<script src="../assets/extra-libs/c3/d3.min.js"></script>
<script src="../assets/extra-libs/c3/c3.min.js"></script>
<script src="../assets/scripts/graficaComparativaRecup.js"></script>
<script>
  $(function() {
    var a = c3.generate({
      bindto: "#rotated-axis",
      size: {
        height: 400
      },
      color: {
        pattern: ["#ee5a36", "#000000"]
      },
      data: {
        x: "Lotes",
        columns: [
          [<?= $str_lotes ?>],
          [<?= $str_semanas ?>],
          [<?= $str_recuperacion ?>],
        ],
        types: {
          Recuperado: "bar"
        }
      },
      axis: {
        x: {
            type: 'category',
            tick: {
                rotate: 75,
                multiline: false
            },
            height: 130
        },
        rotated: !0
      },
      grid: {
        y: {
          show: !0
        }
      }
    });
  });
</script>