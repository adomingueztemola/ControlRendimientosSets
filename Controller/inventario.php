<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../include/connect_mvc.php";
include('../assets/scripts/cadenas.php');
include('../Models/Mdl_Static.php');

$debug = 0;
$idUser = $_SESSION['CREident'];
$obj_inv = new Inventario($debug, $idUser);
$ErrorLog = 'No se recibió';
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}
switch ($_GET["op"]) {
    case "conteoxprograma":
        $Data = $obj_inv->getInventarioAgrupCajas();
        $Data = Excepciones::validaConsulta($Data);
        $json_string = json_encode($Data);
        echo $json_string;
        break;
}
