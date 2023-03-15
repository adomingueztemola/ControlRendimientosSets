<?php
define('INCLUDE_CHECK', 1);
session_start();
require_once('../../include/connect_mvc.php');
include("../../Models/Mdl_ConexionBD.php");
include("../../Models/Mdl_Inventario.php");
include('../../assets/scripts/cadenas.php');

$idUser = $_SESSION['CREident'];
$nameUser = $_SESSION['CREnombreUser'];
setlocale(LC_TIME, 'es_ES.UTF-8');
$debug = 0;
$space = 1;
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}
$obj_inventario = new Inventario($debug, $idUser);
/************************** VARIABLES DE FILTRADO *******************************/
$superlote = !empty($_POST['superlote']) ? $_POST['superlote'] : '';

/************************** FILTRADO *******************************/
$DataRendimiento = $obj_inventario->getTotalInventarios($superlote);
$f_totalEmp= formatoMil($DataRendimiento[0]['totalEmp']);
$f_setsTotalEmp= formatoMil($DataRendimiento[0]['setsTotalEmp']);

$f_totalRech= formatoMil($DataRendimiento[0]['totalRech']);
$f_setsTotalRech= formatoMil($DataRendimiento[0]['setsTotalRech']);

$f_totalRecu= formatoMil($DataRendimiento[0]['totalRecu']);
$f_setsTotalRecu= formatoMil($DataRendimiento[0]['setsTotalRecu']);
$hidden= $DataRendimiento[0]['tipoProceso']=='1'?'':'hidden';
?>

<div class="card border border-secondary mb-3">
  <div class="card-header">Detalle de Inventario Actual</div>
  <div class="card-body text-secondary">
   
        <table class="table table-sm">
            <tbody>
                <tr>
                    <td>Rechazados</td>
                    <td><?=$f_totalRech?></td>
                    <td <?=$hidden?>><?=$f_setsTotalRech?></td>

                </tr>
                <tr>
                    <td>Recuperados</td>
                    <td><?=$f_totalRecu?></td>
                    <td <?=$hidden?>><?=$f_setsTotalRecu?></td>

                </tr>
                <tr>
                    <td>Empacados</td>
                    <td><?=$f_totalEmp?></td>
                    <td <?=$hidden?>><?=$f_setsTotalEmp?></td>

                </tr>
            </tbody>

        </table>
  </div>
</div>