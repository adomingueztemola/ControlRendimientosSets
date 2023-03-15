<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../../../include/connect_mvc.php";
include('../../../assets/scripts/cadenas.php');

$debug = 0;
$idUser = $_SESSION['CREident'];
$obj_empaque = new Empaque($debug, $idUser);
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}
$semana = date('W');
$Data = $obj_empaque -> getLotesRegistradosSemana($semana);
$Data = Excepciones::validaConsulta($Data);
?>

<div class="table-responsive">
    <table class="table table-sm table-striped table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Lote</th>
                <th>Fecha de Empaque</th>
                <th>Teseo</th>
                <th>Piezas OK</th>
                <th>Total Empaque</th>
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
                <td><?=$Data[$key]['f_fehaEmpaque']?></td>
                <td><?=formatoMil($Data[$key]['pzasCortadasTeseo'],0)?></td>
                <td><?=formatoMil($Data[$key]['pzasOk'],0)?></td>
                <td><?=formatoMil($Data[$key]['totalEmp'],0)?></td>


            </tr>
        <?php


        }
        ?>
        </tbody>
    </table>
</div>