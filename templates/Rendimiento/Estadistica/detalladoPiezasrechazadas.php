<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../../../include/connect_mvc.php";
include('../../../assets/scripts/cadenas.php');

$debug = 0;
$idUser = $_SESSION['CREident'];
$obj_pzasnok = new Empaque($debug, $idUser);
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}
$semana = date('W');
$Data = $obj_pzasnok -> getLotesPzasNokAct($semana);
$Data = Excepciones::validaConsulta($Data);
?>

<div class="table-responsive table-striped table-bordered">
    <table class="table table-sm">
        <thead>
            <tr>
                <td>#</td>
                <td>Lote</td>
                <td>Pzas NOK</td>
                <td>12.00</td>
                <td>03.00</td>
                <td>06.00</td>
                <td>09.00</td>
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
                <td><?=formatoMil($Data[$key]['pzasTotales'],0)?></td>
                <td><?=formatoMil($Data[$key]['_12'],0)?></td>
                <td><?=formatoMil($Data[$key]['_6'],0)?></td>
                <td><?=formatoMil($Data[$key]['_3'],0)?></td>
                <td><?=formatoMil($Data[$key]['_9'],0)?></td>


            </tr>
        <?php


        }
        ?>
        </tbody>
    </table>
</div>