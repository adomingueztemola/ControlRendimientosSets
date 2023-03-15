<?php
define('INCLUDE_CHECK', 1);
session_start();
require_once('../../include/connect_mvc.php');
include("../../Models/Mdl_ConexionBD.php");
include("../../Models/Mdl_Rendimiento.php");
include('../../assets/scripts/cadenas.php');

$idUser = $_SESSION['CREident'];
$nameUser = $_SESSION['CREnombreUser'];
setlocale(LC_TIME, 'es_ES.UTF-8');
$debug = 0;
$space = 1;
if ($debug == 1) {
    print_r($_GET);
    //  exit(0);
} else {
    error_reporting(0);
}
$obj_lote = new Rendimiento($debug, $idUser);
/************************** VARIABLES DE FILTRADO *******************************/
$idRendimiento = !empty($_GET['idRendimiento']) ? $_GET['idRendimiento'] : '';
if ($idRendimiento == '') {
    exit(0);
}

$DataRendimiento = $obj_lote->getDetRendimientos($idRendimiento);
$pzasCortadasTeseo = formatoMil($DataRendimiento[0]["pzasCortadasTeseo"], 0);
$areaFinalTeseo = formatoMil($DataRendimiento[0]["areaFinal"], 0);
$yieldInicialTeseo = formatoMil($DataRendimiento[0]["yieldInicialTeseo"], 0);

$_12OK = formatoMil($DataRendimiento[0]['_12OK']);
$_3OK = formatoMil($DataRendimiento[0]['_3OK']);
$_6OK = formatoMil($DataRendimiento[0]['_6OK']);
$_9OK = formatoMil($DataRendimiento[0]['_9OK']);

$_12NOK = formatoMil($DataRendimiento[0]['_12NOK']);
$_3NOK = formatoMil($DataRendimiento[0]['_3NOK']);
$_6NOK = formatoMil($DataRendimiento[0]['_6NOK']);
$_9NOK = formatoMil($DataRendimiento[0]['_9NOK']);
?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <ul class="timeline timeline-left">
                    <li class="timeline-inverted timeline-item">
                        <div class="timeline-badge danger">
                            <i class="fas fa-edit"></i>
                        </div>
                        <div class="timeline-panel">
                            <div class="timeline-heading">
                                <h4 class="timeline-title">Datos Generales de Lote</h4>
                            </div>
                            <div class="timeline-body">
                                <!----------- INFO GENERAL DEL LOTE --------->
                                <div class="card border border-secondary">
                                    <div class="card-body text-secondary">
                                        <table class="table table-sm">
                                            <tbody>
                                                <tr>
                                                    <td>Fecha de Engrase</td>
                                                    <td><?= $DataRendimiento[0]["f_fechaEngrase"] ?></td>

                                                </tr>
                                                <tr>
                                                    <td>Programa</td>
                                                    <td><?= $DataRendimiento[0]["n_programa"] ?></td>

                                                </tr>
                                                <tr>
                                                    <td>Proceso de Secado</td>
                                                    <td><?= $DataRendimiento[0]["c_proceso"] ?> - <?= $DataRendimiento[0]["n_proceso"] ?></td>

                                                </tr>
                                                <tr>
                                                    <td>Materia Prima</td>
                                                    <td><?= $DataRendimiento[0]["n_materia"] ?></td>

                                                </tr>
                                            </tbody>

                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    <?php if($DataRendimiento[0]['tipoProceso']=='1'){?>
                    <li class="timeline-inverted timeline-item">
                        <div class="timeline-badge danger">
                            <i class="fas fa-cut"></i>
                        </div>
                        <div class="timeline-panel">
                            <div class="timeline-heading">
                                <h4 class="timeline-title"></h4>
                            </div>
                            <div class="timeline-body">
                                <div class="card-body mb-0 bordered">
                                    <div class="d-flex no-block align-items-center">
                                        <img src="../assets/images/TESEO.jpg" width="30%" alt="" srcset="">
                                        <div class="mx-3 text-rigth">
                                            <h2>
                                                <font style="vertical-align: inherit;">
                                                    <font style="vertical-align: inherit;">Piezas: <?= $pzasCortadasTeseo ?></font>
                                                </font>
                                            </h2>
                                            <h6><b>Área de Teseo: <?= $areaFinalTeseo ?></b></h6>
                                            <h6><b>Yield de Teseo: <?= $yieldInicialTeseo ?>%</b></h6>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="timeline-inverted timeline-item">
                        <div class="timeline-badge danger">
                            <i class=" fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="timeline-panel">
                            <div class="timeline-heading">
                                <h4 class="timeline-title">Piezas OK/NOK</h4>
                            </div>
                            <div class="timeline-body">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th colspan="2">Detallado de Piezas</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>12:00</td>
                                            <td>
                                                <span class="text-success">OK: <?= $_12OK ?></span><br>
                                                <span class="text-danger">NOK: <?= $_12NOK ?></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>03:00</td>
                                            <td>
                                                <span class="text-success">OK: <?= $_3OK ?></span><br>
                                                <span class="text-danger">NOK: <?= $_3NOK ?></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>06:00</td>
                                            <td>
                                                <span class="text-success">OK: <?= $_6OK ?></span><br>
                                                <span class="text-danger">NOK: <?= $_6NOK ?></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>09:00</td>
                                            <td>
                                                <span class="text-success">OK: <?= $_9OK ?></span><br>
                                                <span class="text-danger">NOK: <?= $_9NOK ?></span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </li>
                    <li class="timeline-inverted timeline-item">
                        <div class="timeline-badge danger">
                            <i class="fab fa-dropbox"></i>
                        </div>
                        <div class="timeline-panel">
                            <div class="timeline-heading">
                                <h4 class="timeline-title">Empaque</h4>
                            </div>
                            <div class="timeline-body">
                                <?php
                                $obj_cajas = new Empaque($debug, $idUser);
                                $Data = $obj_cajas->getPzasOkCajas('', $idRendimiento);
                                $Data = Excepciones::validaConsulta($Data);
                                $_12 = formatoMil($Data['sum_12']);
                                $_3 = formatoMil($Data['sum_3']);
                                $_6 = formatoMil($Data['sum_6']);
                                $_9 = formatoMil($Data['sum_9']);
                                $totalEmp = formatoMil($Data['pzasTotalEmp']);

                                ?>
                                <div class="table-responsive">
                                    <table class="table table-sm  table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th colspan="2">Detallado de Empaque</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>12.00</td>
                                                <td><?= $_12 ?></td>

                                            </tr>
                                            <tr>
                                                <td>03.00</td>
                                                <td><?= $_3 ?></td>

                                            </tr>
                                            <tr>
                                                <td>06.00</td>
                                                <td><?= $_6 ?></td>

                                            </tr>
                                            <tr>
                                                <td>09.00</td>
                                                <td><?= $_9 ?></td>

                                            </tr>
                                            <tr>
                                                <td>Total Empacados</td>
                                                <td><?= $totalEmp ?></td>
                                            </tr>

                                        </tbody>
                                    </table>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-sm  table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th colspan="2">Detallado de Remanentes</th>
                                            </tr>
                                        </thead>
                                        <?php
                                        $_12 = formatoMil($Data['sumr_12']);
                                        $_3 = formatoMil($Data['sumr_12']);
                                        $_6 = formatoMil($Data['sumr_6']);
                                        $_9 = formatoMil($Data['sumr_9']);
                                        $totalRem = formatoMil($Data['pzasTotalRem']);
                                        ?>
                                        <tbody>
                                        <tr>
                                                <td>12.00</td>
                                                <td><?= $_12 ?></td>

                                            </tr>
                                            <tr>
                                                <td>03.00</td>
                                                <td><?= $_3 ?></td>

                                            </tr>
                                            <tr>
                                                <td>06.00</td>
                                                <td><?= $_6 ?></td>

                                            </tr>
                                            <tr>
                                                <td>09.00</td>
                                                <td><?= $_9 ?></td>

                                            </tr>
                                            <tr>
                                                <td>Total Remanente</td>
                                                <td><?= $totalRem ?></td>
                                            </tr>

                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
                    </li>
                    <?php }?>

                </ul>
            </div>
        </div>
    </div>
</div>