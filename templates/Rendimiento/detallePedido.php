<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once('../../include/connect_mvc.php');
include("../../Models/Mdl_ConexionBD.php");
include("../../Models/Mdl_Rendimiento.php");
include('../../assets/scripts/cadenas.php');
$debug = 0;
$idUser = $_SESSION['CREident'];
$nameUser = $_SESSION['CREnombreUser'];
$ident = !empty($_POST['ident']) ? $_POST['ident'] : '';

$obj_rendimiento = new Rendimiento($debug, $idUser);
$DataPedidos = $obj_rendimiento->getPedidosXLote($ident);
$DataPedidos = $DataPedidos == '' ? array() : $DataPedidos;
if (!is_array($DataPedidos)) {
    echo "<p class='text-danger'>Error, $DataPedidos</p>";
    exit(0);
}
?>
<div>
    <div class="row">
        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
            <h4 class="text-TWM text-center">Relación de Abastecimiento de Materia Prima</h4>

        </div>
    </div>
</div>
<table class="table table-sm">
    <thead>
        <tr>
            <th>#</th>
            <th>Num. Factura</th>
            <th>Proveedor</th>
            <th>1s</th>
            <th>2s</th>
            <th>3s</th>
            <th>4s</th>
            <th>20</th>
            <th>Total</th>

        </tr>
    </thead>
    <tbody>
        <?php
        $count = 0;
        foreach ($DataPedidos as $key => $value) {
            if ($DataPedidos[$key]['tipoProceso'] == '2') {
                $f_total_s = formatoMil($DataPedidos[$key]['total_s'], 0);
                $count++;

                echo "<tr>
                <td>$count</td>
                <td>{$DataPedidos[$key]['numFactura']}</td>
                <td>{$DataPedidos[$key]['n_proveedor']}</td>
                <td>N/A</td>
                <td>N/A</td>
                <td>N/A</td>
                <td>N/A</td>
                <td>N/A</td>
    
                <td>{$f_total_s}</td>
    
    
              </tr>";
            } else {
                $f_1s = formatoMil($DataPedidos[$key]['1s'], 0);
                $f_2s = formatoMil($DataPedidos[$key]['2s'], 0);
                $f_3s = formatoMil($DataPedidos[$key]['3s'], 0);
                $f_4s = formatoMil($DataPedidos[$key]['4s'], 0);
                $f_4s = formatoMil($DataPedidos[$key]['4s'], 0);
                $f_20 = formatoMil($DataPedidos[$key]['_20'], 0);
                $f_total_s = formatoMil($DataPedidos[$key]['total_s'], 0);
                $count++;
                echo "<tr>
                <td>$count</td>
                <td>{$DataPedidos[$key]['numFactura']}</td>
                <td>{$DataPedidos[$key]['n_proveedor']}</td>
                <td>{$f_1s}</td>
                <td>{$f_2s}</td>
                <td>{$f_3s}</td>
                <td>{$f_4s}</td>
                <td>{$f_20}</td>
    
                <td>{$f_total_s}</td>
    
    
              </tr>";
            }
        }
        ?>
    </tbody>
</table>