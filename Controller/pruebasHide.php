<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../include/connect_mvc.php";
include('../Models/Mdl_Static.php');

$debug = 1;
$idUser = $_SESSION['CREident'];

$obj_pzas = new PzasOKNOK($debug, $idUser);

$ErrorLog = 'No se recibió';
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}

switch ($_GET["op"]) {
    case "agregarpruebas":
        $lote = (isset($_POST['lote'])) ? trim($_POST['lote']) : '';
        $fecha = (isset($_POST['fecha'])) ? trim($_POST['fecha']) : '';
        $hides = (isset($_POST['hides'])) ? trim($_POST['hides']) : '';
        #VALIDACION DE DATOS
        Excepciones::validaLlenadoDatos(array(
            " Cantidad" => $hides,
            " Fecha" => $fecha,
            " Lote" => $lote,
        ), $obj_pzas);






        
        break;
}
?>