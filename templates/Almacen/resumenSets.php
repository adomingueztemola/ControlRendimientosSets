<?php
define('INCLUDE_CHECK', 1);
session_start();
require_once('../../include/connect_mvc.php');
include('../../assets/scripts/cadenas.php');

$idUser = $_SESSION['CREident'];
$nameUser = $_SESSION['CREnombreUser'];
setlocale(LC_TIME, 'es_ES.UTF-8');
$debug = 0;
$space = 1;
if ($debug == 1) {
    print_r($_POST);
} else {
    error_reporting(0);
}
$obj_inventario = new Inventario($debug, $idUser);
$obj_empaque = new Empaque($debug, $idUser);
$obj_rendimiento = new Rendimiento($debug, $idUser);

$fechas = CalculoSemana::calculoSemana();
$f_fechaIni = date('d/m/Y', strtotime($fechas[0]));
$f_fechaFin = date('d/m/Y', strtotime($fechas[1]));

$filtradoFecha = "v.fechaFact BETWEEN '{$fechas[0]}' AND '{$fechas[1]}'";
$DataVenta = $obj_rendimiento->getVentasSetsInternos($filtradoFecha);
$DataVenta = Excepciones::validaConsulta($DataVenta);


$Data = $obj_inventario->getInventarioCajas("1=1", "1=1", "1=1", "1=1");
$Data = Excepciones::validaConsulta($Data);

$ArraySetsCajas = array();
$ArrayCajasProceso = array();

foreach ($Data as $value) {
    if ($value['semanaProduccion'] == '0') {
        array_push($ArrayCajasProceso, $value);
    } else {
        array_push($ArraySetsCajas, $value);
    }
}
$DataRem = $obj_empaque->getReporteRemanente("1=1");
$DataRem = Excepciones::validaConsulta($DataRem);

?>
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mt-2">
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>LOTE</th>
                    <th>PROGRAMA</th>
                    <th>SETS</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // SETS EN CAJA DE ALMACEN
                /*********************************************************/
                $sumaSetsCaja = 0;
                $sumaCajasProceso = 0;
                foreach ($ArraySetsCajas as $value) {
                    $sumaSetsCaja = $sumaSetsCaja + $value['setscajas'];
                    $sets = formatoMil($value['setscajas'], 0);
                    echo "<tr>
                        <td>{$value['loteTemola']}</td>
                        <td>{$value['nPrograma']}</td>
                        <td>{$sets}</td>

                    </tr>";
                }
                $f_sumaSetsCaja = formatoMil($sumaSetsCaja, 0);
                echo "<tr class='table-info'>
                    <td colspan='2' class='text-right'><b>SETS EN CAJAS</b></td>
                    <td><b>{$f_sumaSetsCaja}</b></td>
                </tr>";
                /*********************************************************/
                // SETS EN CAJA DE PROCESO 
                foreach ($ArrayCajasProceso as $value) {
                    $sumaCajasProceso = $sumaCajasProceso + $value['setscajas'];
                    $sets = formatoMil($value['setscajas'], 0);
                    echo "<tr>
                        <td>{$value['loteTemola']}</td>
                        <td>{$value['nPrograma']}</td>
                        <td>{$sets}</td>

                    </tr>";
                }
                $f_sumaCajasProceso = formatoMil($sumaCajasProceso, 0);
                echo "<tr class='table-info'>
                    <td colspan='2' class='text-right'><b>SETS EN CAJAS EN PROCESO</b></td>
                    <td><b>{$f_sumaCajasProceso}</b></td>
                </tr>";
                /*********************************************************/
                // SETS REMANENTES
                $sumaCajasRem=0;
                foreach ($DataRem as $value) {
                    $sumaCajasRem = $sumaCajasRem + $value['setscajas'];
                    $sets = formatoMil($value['setscajas'], 0);
                    if ($value['setscajas'] > 0) {
                        echo "<tr>
                        <td>{$value['loteTemola']}</td>
                        <td>{$value['nPrograma']}</td>
                        <td>{$sets}</td>
                        </tr>";
                    }
                }
                $f_sumaCajasRem = formatoMil($sumaCajasRem, 0);
                echo "<tr class='table-info'>
                    <td colspan='2' class='text-right'><b>SETS REMANENTES</b></td>
                    <td><b>{$f_sumaCajasRem}</b></td>
                </tr>";
                /*********************************************************/
                // SETS CONSUMO INTERNO
                foreach ($DataVenta as $value) {
                    $sumaConsumoInterno = $sumaConsumoInterno + $value['unidFacturadas'];
                    $sets = formatoMil($value['unidFacturadas'], 0);
                    echo "<tr>
                        <td>{$value['loteTemola']}</td>
                        <td>{$value['nPrograma']}</td>
                        <td>{$sets}</td>

                    </tr>";
                }
                $f_sumaConsumoInterno = formatoMil($sumaConsumoInterno, 0);
                echo "<tr class='table-info'>
                    <td class='text-right'><b>SETS CONSUMO INTERNO</b></td>
                    <td><b>{$f_fechaIni} AL {$f_fechaFin}</b></td>
                    <td><b>{$f_sumaConsumoInterno}</b></td>
                </tr>";
                /*********************************************************/
                $sumaExistencias=   $sumaConsumoInterno+  $sumaCajasRem +  $sumaCajasProceso + $sumaSetsCaja;
                $f_sumaExistencias= formatoMil($sumaExistencias, 0);
                echo "<tr class='table-info'>
                <td colspan='2' class='text-right'><b>EXISTENCIAS TOTALES SETS</b></td>
                <td><b>{$f_sumaExistencias}</b></td>
            </tr>";
                ?>
            </tbody>
        </table>
    </div>
</div>