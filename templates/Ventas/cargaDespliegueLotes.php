<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../../include/connect_mvc.php";
include('../../Models/Mdl_ConexionBD.php');
include('../../Models/Mdl_Venta.php');
include('../../assets/scripts/cadenas.php');

$debug = 0;
$idUser = $_SESSION['CREident'];
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}
$id = !empty($_POST['id']) ? $_POST['id'] : '';

$obj_ventas = new Venta($debug, $idUser);

$DataVentas = $obj_ventas->getLoteXVenta($id);
?>
<div class="table-responsive">

    <table class="table table-sm table-bordered">
        <thead>
            <tr>
                <th>Lote Temola</th>
                <th>Proceso</th>
                <th>Programa</th>
                <th>Materia Prima</th>
                <th>1s</th>
                <th>2s</th>
                <th>3s</th>
                <th>4s</th>
                <th>Total</th>

                <th>Unidade(s)</th>
                <th>Devuelto</th>

            </tr>
        </thead>
        <tbody>
          
            <?php
            $DataVentas = !is_array($DataVentas) ? array() : $DataVentas;
            if (count($DataVentas) > 0) {
                foreach ($DataVentas as $key => $value) {
                    $unidades = formatoMil($DataVentas[$key]["unidades"]);
                    $lbl_devuelto= $DataVentas[$key]["devuelto"]=='1'?'<i class="fas fa-check text-success"></i>':'<i class="fas fa-times text-danger"></i>';
                    $color_devuelto= $DataVentas[$key]["devuelto"]=='1'?'table-danger':'';

                    echo "
               <tr class='{$color_devuelto}'>
                    <td>{$DataVentas[$key]['loteTemola']}</td>
                    <td>{$DataVentas[$key]['c_proceso']}</td>
                    <td>{$DataVentas[$key]['n_programa']}</td>

                    <td>{$DataVentas[$key]['n_materia']}</td>
                    <td>".formatoMil($DataVentas[$key]['1s'])."</td>
                    <td>".formatoMil($DataVentas[$key]['2s'])."</td>
                    <td>".formatoMil($DataVentas[$key]['3s'])."</td>
                    <td>".formatoMil($DataVentas[$key]['4s'])."</td>
                    <td>".formatoMil($DataVentas[$key]['total_s'])."</td>
                    <td>{$unidades}</td>
                    <td>{$lbl_devuelto}</td>

               </tr>
               ";
                }
            } else {
                echo "<tr>
                    <td colspan='13' class='text-center'>Sin Lotes Registrados</td>
                </tr>";
            }
            ?>
           
          
        </tbody>
    </table>
</div>