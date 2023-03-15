<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../../include/connect_mvc.php";
include('../../assets/scripts/cadenas.php');

$debug = 0;
$idUser = $_SESSION['CREident'];
$obj_inventario = new Inventario($debug, $idUser);
$obj_recuperacion = new TrabajosRecupera($debug, $idUser);
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}
$ident = !empty($_POST['ident']) ? $_POST['ident'] : "";
$DataLoteInicial = $obj_inventario->detalleLoteInicial($ident);
$DataSubLote = $obj_inventario->detalleLotesRecuperados($ident);
$DataSubLote = $DataSubLote == '' ? array() : $DataSubLote;
if (!is_array($DataSubLote)) {
    echo "<p class='text-danger'>Error, $DataSubLote</p>";
    exit(0);
}
?>
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card card-border border">
            <div class="card-header bg-TWM text-white">
                <h5>Material Retrabajado</h5>
            </div>
            <div class="card-body">
                <?php
                $Data = $obj_recuperacion->getRecuperacionXLote($ident);
                $Data = Excepciones::validaConsulta($Data);
                $Data= $Data==''?array():$Data;
                ?>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <td>#</td>
                                <td>Fecha</td>
                                <td>Lote Origen</td>
                                <td>Lote Asignado</td>
                                <td>Pzas Asig.</td>
                                <td>Pérdida</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $count = 1;
                            if(count($Data)<=0){
                                echo "<tr>
                                <td colspan='5' class='text-danger text-center'>Sin información de Recuperación</td>
                                </tr>";
                            }
                            foreach ($Data as $key => $value) {
                                $f_totalRecuperacion = formatoMil($Data[$key]['totalRecuperacion'], 0);
                                $f_porcPerdidaRecuperacion =   formatoMil($Data[$key]['porcPerdidaRecuperacion'], 0);
                                echo "<tr>
                                <td>{$count}</td>
                                <td>{$Data[$key]['f_fecha']}</td>
                                <td>{$Data[$key]['loteTemolaInicial']}</td>
                                <td>{$Data[$key]['loteTemolaRecup']}</td>
                                <td>{$f_totalRecuperacion}</td>
                                <td>
                                <span class='badge badge-primary'>$f_porcPerdidaRecuperacion%</span>
                                </td>
                               </tr>";
                                $count++;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>