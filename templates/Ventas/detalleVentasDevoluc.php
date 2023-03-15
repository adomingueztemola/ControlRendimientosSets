<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../../include/connect_mvc.php";
include('../../Models/Mdl_ConexionBD.php');
include('../../Models/Mdl_Excepciones.php');

include('../../Models/Mdl_VentaXDevoluc.php');
include('../../assets/scripts/cadenas.php');

$debug = 0;
$idUser = $_SESSION['CREident'];
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}
$ident = !empty($_POST['ident']) ? $_POST['ident'] : '';
$obj_ventas = new VentaXDevoluc($debug, $idUser);
$Data = $obj_ventas->getDetVentas($ident);
$Data = Excepciones::validaConsulta($Data);
$Data = $Data == '' ? array() : $Data;

?>
<h4>Detallado de Venta</h4>
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="table-responsive">
            <table class="table table-sm">
                <thead class="bg-TWM text-white">
                    <tr>
                        <th>#</th>
                        <th>RMA</th>
                        <th>Lote</th>
                        <th>Programa</th>
                        <th>Cantidad</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $count = 0;
                    foreach ($Data as $key => $value) {
                        $count++;
                        $f_cantidad = formatoMil($Data[$key]['unidades']);
                        echo "<tr>
                        <td>{$count}</td>
                        <td>{$Data[$key]['rma']}</td>
                        <td>{$Data[$key]['loteTemola']}</td>
                        <td>{$Data[$key]['prg_nombre']}</td>
                        <td>{$f_cantidad}</td>
                    </tr>";
                    }
                    ?>
                </tbody>
            </table>

        </div>
    </div>
</div>