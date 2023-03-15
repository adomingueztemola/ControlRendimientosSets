<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../../../include/connect_mvc.php";
include('../../../assets/scripts/cadenas.php');

$debug = 0;
$idUser = $_SESSION['CREident'];
$obj_cajas = new Empaque($debug, $idUser);
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}

$semana = date('W');
$Data = $obj_cajas -> getPzasOkCajas($semana);
$Data = Excepciones::validaConsulta($Data);
?>

<div class="table-responsive table-striped table-bordered">
    <table class="table table-sm">
        <thead>
            <tr>
                <th>#</th>
                <th>Lote</th>
                <th>12.00</th>
                <th>03.00</th>
                <th>06.00</th>
                <th>09.00</th>
                <th class="text-center">Total Empacados</th>
                <th class="">09.00 Rem.</th>
                <th class="">12.00 Rem.</th>
                <th class="">03.00 Rem.</th>
                <th class="">06.00 Rem.</th>
                <th class="text-center">Total Remanentes</th>
            </tr>
            
        </thead>


        <tbody>
        <?php
        $count=0;
        foreach ($Data as $key => $value) {
            $count++;
        ?>
            <tr>
                <td><?=$count?></td>
                <td><?=$Data[$key]['loteTemola']?></td>
                <td><?=formatoMil($Data[$key]['sum_12'],0)?></td>
                <td><?=formatoMil($Data[$key]['sum_3'],0)?></td>
                <td><?=formatoMil($Data[$key]['sum_6'],0)?></td>
                <td><?=formatoMil($Data[$key]['sum_9'],0)?></td>
                <td><?=formatoMil($Data[$key]['pzasTotalEmp'],0)?></td>
                <td><?=formatoMil($Data[$key]['sumr_12'],0)?></td>
                <td><?=formatoMil($Data[$key]['sumr_12'],0)?></td>
                <td><?=formatoMil($Data[$key]['sumr_6'],0)?></td>
                <td><?=formatoMil($Data[$key]['sumr_9'],0)?></td>
                <td><?=formatoMil($Data[$key]['pzasTotalRem'],0)?></td>


            </tr>
        <?php


        }
        ?>
        </tbody>
    </table>
</div>