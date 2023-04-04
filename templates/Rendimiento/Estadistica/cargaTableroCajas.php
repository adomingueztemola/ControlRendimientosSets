<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once('../../../include/connect_mvc.php');
include('../../../assets/scripts/cadenas.php');

$debug = 0;
$idUser = $_SESSION['CREident'];
$nameUser = $_SESSION['CREnombreUser'];
setlocale(LC_TIME, 'es_ES.UTF-8');
$obj_empaque = new Empaque($debug, $idUser);
$fecha = (!empty($_POST['fecha']) and $_POST['fecha'] != '') ? $_POST['fecha'] : date('Y-m-d');
$Data = $obj_empaque->getCajasCompletXPrograma($fecha);
$Data = Excepciones::validaConsulta($Data);
$totalCajas=0;
?>
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-sm  table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Programa</th>
                        <th>Uso</th>

                        <th>Cantidad</th>
                    </tr>

                </thead>


                <tbody>
                    <?php
                    if (count($Data) <= 0) {
                        echo "<tr>
                    <td colspan='3' class='text-center'>Sin Registro de Caja Localizado</td>
                </tr>";
                    } else {
                        foreach ($Data as  $value) {
                            $fTotalCajas = formatoMil($value['totalCajas'], 0);
                            $cajasGrafico = str_repeat('<input size="1" class="bg-success" disabled>', $value['totalCajas']);
                            $lblInterno = $value['interna'] == '1' ? 'Interna' : '-';
                            $totalCajas=$totalCajas+$value['totalCajas'];
                    ?>
                            <tr>
                                <!-- primer resultado-->
                                <th><?= $value['nPrograma'] ?>
                                    <?= $cajasGrafico ?>
                                </th>

                                <td>
                                    <?= $lblInterno ?>
                                </td>
                                <td>
                                    <?= $fTotalCajas ?>
                                </td>

                            </tr>

                    <?php
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card text-white bg-success" >
            <div class="card-header text-center"><h5>Total de Empaque del día: <?=formatoMil($totalCajas, 0)?></h5></div>
        </div>
    </div>
</div>