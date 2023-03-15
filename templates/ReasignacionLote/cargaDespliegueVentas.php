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
<label>Resumen de Ventas</label>
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive">
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Num. Factura</th>
                    <th>Num. PL</th>
                    <th>Sets</th>

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
                $Data = $obj_reasignacion->getDetReasignaVentas($traspaso);
                $count = 1;
                if (count($Data) > 0) {
                    foreach ($Data as $key => $value) {
                        $_1sV = formatoMil($Data[$key]["1sV"], 0);
                        $_2sV = formatoMil($Data[$key]["2sV"], 0);
                        $_3sV = formatoMil($Data[$key]["3sV"], 0);
                        $_4sV = formatoMil($Data[$key]["4sV"], 0);
                        $_20V = formatoMil($Data[$key]["_20V"], 0);
                        $total_sV = formatoMil($Data[$key]["total_sV"], 0);

                        echo "<tr>
                        <td>{$count}</td>
                        <td>{$Data[$key]['numFactura']}</td>
                        <td>{$Data[$key]['numPL']}</td>
                        <td>{$Data[$key]['sets']}</td>

                        <td>{$_1sV}-><b>{$Data[$key]['1s']}</b></td>
                        <td>{$_2sV}-><b>{$Data[$key]['2s']}</b></td>
                        <td>{$_3sV}-><b>{$Data[$key]['3s']}</b></td>
                        <td>{$_4sV}-><b>{$Data[$key]['4s']}</b></td>
                        <td>{$_20V}-><b>{$Data[$key]['_20']}</b></td>
                        <td>{$total_sV}-><b>{$Data[$key]['total_s']}</b></td>

                   </tr>";
                    }
                }else{
                    echo "<tr><td colspan='9' class='text-danger'>No hay ventas especificadas</td></tr>";
                }

                ?>
            </tbody>
        </table>
    </div>
</div>