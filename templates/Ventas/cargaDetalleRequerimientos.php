<?php
define('INCLUDE_CHECK', 1);
require_once "../../include/connect_mvc.php";
include('../../Models/Mdl_ConexionBD.php');
include('../../Models/Mdl_VentaPrevia.php');
include('../../Models/Mdl_Excepciones.php');
include("../../assets/scripts/cadenas.php");
session_start();
setlocale(LC_TIME, 'es_ES.UTF-8');
$debug = 0;
$space = 1;
$idUser = $_SESSION['CREident'];
$nameUser = $_SESSION['CREnombreUser'];
$obj_ventaprevia = new VentaPrevia($debug, $idUser);
$id = (isset($_GET['id'])) ? trim($_GET['id']) : '';
$Data = $obj_ventaprevia->getRequerimientosPzasXVenta($id);
$Data = Excepciones::validaConsulta($Data);
$Data = $Data == '' ? array() : $Data;
?>
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Lote</th>
                    <th>Pzas. Requeridas</th>
                    <th>Pzas. Existencia</th>
                    <th>Pzas. Faltantes</th>

                </tr>
            </thead>
            <tbody>
                <?php
                $count = 1;
                foreach ($Data as $key => $value) {
                    $f_pzasTotales = formatoMil($Data[$key]['pzasTotales'], 2);
                    $f_totalEmp = formatoMil($Data[$key]['totalEmp'], 2);
                    $f_pzasFaltantes = formatoMil($Data[$key]['pzasFaltantes'], 2);
                    $colorTable=$Data[$key]['pzasFaltantes']=='0'?'table-success':'';
                    echo "<tr class='$colorTable'>
                            <td>{$count}</td>
                            <td>{$Data[$key]['loteTemola']}</td>
                            <td>{$f_pzasTotales}</td>
                            <td>{$f_totalEmp}</td>
                            <td>{$f_pzasFaltantes}</td>

                        </tr>";
                    $count++;
                }

                ?>
            </tbody>
        </table>
    </div>
</div>