<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../include/connect_mvc.php";
$debug = 0;
$idUser = $_SESSION['CREident'];


$obj_alertas = new Alerta($debug, $idUser);

$ErrorLog = 'No se recibió';
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}

switch ($_GET["op"]) {
    case "capturalotesprod":
        $datos = $obj_alertas->getLotesXCapturar();
        $datos = Excepciones::validaConsulta($datos);
        $datos = !is_array($datos) ? 'Error!' : $datos['total'];
        echo "1|{$datos}|reporteLotesCapturaProd.php";
        break;
    case "capturalotessup":
        $datos = $obj_alertas->getLotesXCapturar();
        $datos = Excepciones::validaConsulta($datos);
        $datos = !is_array($datos) ? 'Error!' : $datos['total'];
        echo "1|{$datos}|reporteLotesCapturaSup.php";
        break;
    case "errorlogs":
        $datos = $obj_alertas->getLogsActivos();
        $datos = Excepciones::validaConsulta($datos);
        $datos = !is_array($datos) ? 'Error!' : $datos['total'];
        echo "1|{$datos}|cargaExcelRecupera.php";
        break;
    case "errorlogssup":
        $datos = $obj_alertas->getLogsActivos();
        $datos = Excepciones::validaConsulta($datos);
        $datos = !is_array($datos) ? 'Error!' : $datos['total'];
        echo "1|{$datos}|cargaExcelRecuperaSup.php";
        break;
    case "ventasprogabast":
        $datos = $obj_alertas->getVentasAbast();
        $datos = Excepciones::validaConsulta($datos);
        $datos = !is_array($datos) ? 'Error!' : $datos['total'];
        echo "1|{$datos}|ventasprogramadas.php";
        break;
    case "solicitudesteseo":
        $datos = $obj_alertas->getSolicitudesTeseo();
        $datos = Excepciones::validaConsulta($datos);
        $datos = !is_array($datos) ? 'Error!' : $datos['total'];
        echo "1|{$datos}|solicitudesEdiciones.php";
        break;
}
