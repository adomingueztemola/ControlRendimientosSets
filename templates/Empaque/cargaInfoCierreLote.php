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
$lote = isset($_GET['lote']) ? $_GET['lote'] : '';

if ($id == '' or $lote == '') {
    echo '<div class="alert alert-danger" role="alert">
            ¡Atención! No se encontró el lote, intentalo de nuevo, si el problema persiste consulta al departamento de sistemas.
           </div>';
    exit(0);
}
$obj_empaque = new Empaque($debug, $idUser); //Modelo de Empaque
/***************************** Datos del Empaque *******************************/
$DataEmpaque = $obj_empaque->consultaTotalEmpaqueXLote($lote, $id);
$DataEmpaque = Excepciones::validaConsulta($DataEmpaque);
if (count($DataEmpaque) <= 0) {
    echo "<div class='alert alert-danger' role='alert'>
            <b>No se encontró el registro de empaque solicitado, notifica al departamento de Sistemas.</b>
          </div>";
    exit(0);
}
$ftoEmpLinea = formatoMil($DataEmpaque['sumPzasNorm'], 0);
$ftoEmpRemt = formatoMil($DataEmpaque['sumPzasRemt'], 0);
$ftoEmpRecu = formatoMil($DataEmpaque['sumPzasRecup'], 0);
$ftoTotalEmp = formatoMil($DataEmpaque['totalEmp'], 0);
$ftoScrapLote = formatoMil($DataEmpaque['totalScrap'], 0);
$ftoTotalUtilizado = formatoMil($DataEmpaque['sumParcLote'], 0);
$ftoTotalRemanente = formatoMil($DataEmpaque['remanente'], 0);
?>
<div class="row">
    <div class="col-lg-12 col-md-12 col-xs-12 col-sm-12">
        <table class="table table-sm table-bordered table-striped">
            <tbody>
                <tr>
                    <td><b>Lote: </b></td>
                    <td><b><?= $DataEmpaque['loteTemola'] ?> </b></td>
                </tr>
                <tr>
                    <td>Empaque con Producción Línea: </td>
                    <td><?= $ftoEmpLinea ?></td>
                </tr>
                <tr>
                    <td>Empaque con Remanentes: </td>
                    <td><?= $ftoEmpRemt ?></td>
                </tr>
                <tr>
                    <td>Empaque con Recuperación: </td>
                    <td><?= $ftoEmpRecu ?></td>
                </tr>
                <tr>
                    <td><b>TOTAL GLOBAL DE PIEZAS EN EMPAQUE: </b></td>
                    <td><b><?= $ftoTotalEmp ?> </b></td>
                </tr>
                <tr>
                    <td><b>TOTAL GLOBAL UTILIZADO EN OTROS EMPAQUES: </b></td>
                    <td><b><?= $ftoTotalUtilizado ?> </b></td>
                </tr>
                <tr>
                    <td><b>TOTAL DE REMANENTE REGISTRADO: </b></td>
                    <td><b><?= $ftoTotalRemanente ?> </b></td>
                </tr>
                <tr class="table-danger">
                    <td><b>TOTAL DE SCRAP EN EL LOTE: </b></td>
                    <td><b><?= $ftoScrapLote ?> </b></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>