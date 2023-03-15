<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../../include/connect_mvc.php";
include('../../assets/scripts/cadenas.php');
$debug = 0;
$idUser = $_SESSION['CREident'];
$obj_pedidos = new Pedido($debug, $idUser);
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}
$id = !empty($_POST['id']) ? $_POST['id'] : "";
$Data = $obj_pedidos->getListaMPXPedido($id);
$Data = Excepciones::validaConsulta($Data);
?>

<div id="accordion2" class="accordion" role="tablist" aria-multiselectable="true">
    <?php
    $count = '1';
    foreach ($Data as  $value) {
        $fPrecioUSD = formatoMil($value['precioUnitFactUsd']);
        $fPrecioMXN = formatoMil($value['precioUnitFactPesos']);
        $totalCueros = formatoMil($value['totalCuerosFacturados']);
        $areaWB = formatoMil($value['areaWBPromFact']);
        $areaProv = formatoMil($value['areaProvPie2']);

        $totalCuerosEntregados = formatoMil($value['totalCuerosEntregados']);
        $_1sInit = formatoMil($value['1sInit']);
        $_2sInit = formatoMil($value['2sInit']);
        $_3sInit = formatoMil($value['3sInit']);
        $_4sInit = formatoMil($value['4sInit']);
        $_20Init = formatoMil($value['_20Init']);

        $lbl_Estado = $value['estado'] == '1' ? 'En Proceso' : 'Clasificado';

    ?>
        <div class="card">
            <div class="card-header" role="tab" id="headingOne">
                <h5 class="mb-0">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse<?= $count ?>" aria-expanded="true" aria-controls="collapseOne">
                        <?= $count ?>.- <?= $value["nMateria"] ?>
                    </a>
                </h5>
            </div>
            <div id="collapse<?= $count ?>" class="collapse" role="tabpanel" aria-labelledby="headingOne">
                <div class="card-body">
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr class="bg-TWM text-white">
                                <th colspan="2">Información de Costos de Materia Prima</th>
                            </tr>
                            <tr>
                                <th>Precio Unit. MXN</th>
                                <th>Precio Unit. USD</th>
                            </tr>
                        </thead>
                        <tbody>
                            <td><?= $fPrecioMXN ?></td>
                            <td><?= $fPrecioUSD ?></td>

                        </tbody>
                    </table>
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr class="bg-TWM text-white">
                                <th colspan="3">Información de Áreas de Materia Prima</th>
                            </tr>
                            <tr>
                                <th>Total Cueros</th>
                                <th>Área Proveedor Pie<sup>2</sup></th>
                                <th>Área WB Promedio</th>
                            </tr>
                        </thead>
                        <tbody>
                            <td><?= $totalCueros ?></td>
                            <td><?= $areaWB ?></td>
                            <td><?= $areaProv ?></td>

                        </tbody>
                    </table>
                    
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr class="bg-TWM text-white">
                                <th colspan="5">Información de Recepción del Pedido</th>
                                <th><?= $lbl_Estado ?></th>
                            </tr>
                            <tr>
                                <th>Total Entregados</th>
                                <th>1s</th>
                                <th>2s</th>
                                <th>3s</th>
                                <th>4s</th>
                                <th>20</th>

                            </tr>

                        </thead>
                        <tbody>
                            <td><?= $totalCuerosEntregados ?></td>
                            <td><?= $_1sInit ?></td>
                            <td><?= $_2sInit ?></td>
                            <td><?= $_3sInit ?></td>
                            <td><?= $_4sInit ?></td>
                            <td><?= $_20Init ?></td>

                        </tbody>
                    </table>
                    <h5>Ajustes de la Recepción</h5>
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr class="bg-TWM text-white">
                                <th>#</th>
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th>Motivo</th>
                                <th>Cueros</th>
                                <th>Nota de Crédito</th>
                                <th>Empleado Responsable</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $count = 1;
                            $DataDetPedido= $obj_pedidos->getEdicionesPedidos($value['id']);
                            $DataDetPedido= Excepciones::validaConsulta($DataDetPedido);
                            if(count($DataDetPedido)<=0){
                                echo "<tr>
                                <td colspan='7' class='text-center'>Sin Ajustes de Recepción Registrado</td>
                                </tr>";
                            }
                            foreach ($DataDetPedido as $valueEdit) {
                                $lblTipo = $valueEdit['tipo'] == '1' ? "Aumento" : "Disminución";
                                $f_cueros = formatoMil($valueEdit['totalCueros'], 2);
                                $numNotaCredito = ($valueEdit['numNotaCredito'] == "0" or $valueEdit['numNotaCredito'] == "0") ? '<i>N/A</i>' : $valueEdit['numNotaCredito'];
                                $colorTipo = $valueEdit['tipo'] == '1' ? "text-success" : "text-danger";
                                echo "<tr>
                        <td>{$count}</td>
                        <td>{$valueEdit['f_fechaReg']}</td>
                        <td class='{$colorTipo}'>{$lblTipo}</td>
                        <td>{$valueEdit['descripcion']}</td>
                        <td>{$f_cueros}</td>
                        <td>{$numNotaCredito}</td>
                        <td>{$valueEdit['n_empleado']}</td>
                    </tr>";
                                $count++;
                            }


                            ?>


                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    <?php
        $count++;
    }
    ?>


</div>