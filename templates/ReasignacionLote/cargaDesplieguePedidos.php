<?php

define('INCLUDE_CHECK', 1);
session_start();
require_once('../../include/connect_mvc.php');
include("../../Models/Mdl_ConexionBD.php");
include("../../Models/Mdl_ReasignacionLotesFracc.php");
include("../../Models/Mdl_Excepciones.php");
include('../../assets/scripts/cadenas.php');

setlocale(LC_TIME, 'es_ES.UTF-8');
$idUser = $_SESSION['CREident'];
$nameUser = $_SESSION['CREnombreUser'];
$debug = 0;
$traspaso = !empty($_GET['traspaso']) ? $_GET['traspaso'] : '';
$obj_reasignacion = new ReasignacionLotesFracc($debug, $idUser);

?>
<label>Resumen de Pedidos</label>
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive">
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Num. Factura</th>
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
                $Data = $obj_reasignacion->getDetReasignaPedidos($traspaso);
                $count = 1;
                foreach ($Data as $key => $value) {
                    $_1sP = formatoMil($Data[$key]["1sP"], 0);
                    $_2sP = formatoMil($Data[$key]["2sP"], 0);
                    $_3sP = formatoMil($Data[$key]["3sP"], 0);
                    $_4sP = formatoMil($Data[$key]["4sP"], 0);
                    $_20P = formatoMil($Data[$key]["_20P"], 0);
                    $total_sP = formatoMil($Data[$key]["total_sP"], 0);

                    echo "<tr>
                        <td>{$count}</td>
                        <td>{$Data[$key]['numFactura']}</td>
                        <td>{$_1sP}-><b>{$Data[$key]['1s']}</b></td>
                        <td>{$_2sP}-><b>{$Data[$key]['2s']}</b></td>
                        <td>{$_3sP}-><b>{$Data[$key]['3s']}</b></td>
                        <td>{$_4sP}-><b>{$Data[$key]['4s']}</b></td>
                        <td>{$_20P}-><b>{$Data[$key]['_20']}</b></td>
                        <td>{$total_sP}-><b>{$Data[$key]['total_s']}</b></td>

                   </tr>";
                }

                ?>
            </tbody>
        </table>
    </div>
</div>