<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../../include/connect_mvc.php";
include('../../assets/scripts/cadenas.php');
$debug = 0;
$idUser = $_SESSION['CREident'];
if ($debug == 1) {
    print_r($_GET);
    //  exit(0);
} else {
    error_reporting(0);
}
$id = isset($_GET['id']) ? $_GET['id'] : '';
if ($id == '') {
    echo '<div class="alert alert-danger" role="alert">
            ¡Atención! No se encontró el Lote, intentalo de nuevo, si el problema persiste consulta al departamento de sistemas.
           </div>';
    exit(0);
}
$obj_empaque = new Empaque($debug, $idUser); //Modelo de Empaque
$Data = $obj_empaque->getDetLote($id);
$Data = Excepciones::validaConsulta($Data);
$_12Teseo= formatoMil($Data['_12Teseo']);
$_3Teseo= formatoMil($Data['_3Teseo']);
$_6Teseo= formatoMil($Data['_6Teseo']);
$_9Teseo= formatoMil($Data['_9Teseo']);

$_12OKAct= formatoMil($Data['_12OKAct']);
$_3OKAct= formatoMil($Data['_3OKAct']);
$_6OKAct= formatoMil($Data['_6OKAct']);
$_9OKAct= formatoMil($Data['_9OKAct']);

?>
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <table class="table table-bordered table-sm">
            <thead>
                <tr class="bg-TWM text-white">
                    <th colspan="2" class="text-center">REPORTE TESEO</th>
                </tr>
                <tr>
                    <th>Piezas</th>
                    <th>Cantidad</th>
                </tr>
            </thead>
            <tbody>
                <?php
                echo "
                <tr>
                    <td>12:00</td>
                    <td>{$_12Teseo}</td>
                </tr>
                <tr>
                    <td>03:00</td>
                    <td>{$_3Teseo}</td>

                </tr>
                <tr>
                 <td>06:00</td>
                 <td>{$_6Teseo}</td>
                 </tr> 
                <tr> 
                    <td>09:00</td>
                    <td>{$_9Teseo}</td>

                </tr>";


                ?>

            </tbody>
        </table>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <table class="table table-bordered table-sm">
            <thead>
                <tr class="bg-TWM text-white">
                    <th colspan="2" class="text-center">REPORTE DE PIEZAS DISPONIBLES</th>
                </tr>
                <tr>
                    <th>Piezas</th>
                    <th>Cantidad</th>
                </tr>
            </thead>
            <tbody>
                <?php
                echo "
                <tr>
                    <td>12:00</td>
                    <td>{$_12OKAct}</td>
                </tr>
                <tr>
                    <td>03:00</td>
                    <td>{$_3OKAct}</td>

                </tr>
                <tr>
                 <td>06:00</td>
                 <td>{$_6OKAct}</td>
                 </tr> 
                <tr> 
                    <td>09:00</td>
                    <td>{$_9OKAct}</td>

                </tr>";


                ?>

            </tbody>
        </table>
    </div>
</div>