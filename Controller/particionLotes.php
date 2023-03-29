<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../include/connect_mvc.php";
include('../Models/Mdl_Static.php');

$debug = 1;
$idUser = $_SESSION['CREident'];

$obj_particion = new ParticionLote($debug, $idUser);

$ErrorLog = 'No se recibió';
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}

switch ($_GET["op"]) {
    case "agregarparticion":
        $lote = (isset($_POST['lote'])) ? trim($_POST['lote']) : '';
        $programa = (isset($_POST['programa'])) ? trim($_POST['programa']) : '';
        $hides = (isset($_POST['hides'])) ? trim($_POST['hides']) : '';
        #VALIDACION DE DATOS
        Excepciones::validaLlenadoDatos(array(
            " Lote" => $lote,
            " Programa" => $programa,
            " Hides" => $hides
        ), $obj_particion);
        $cueros= $hides/2;
        //Buscar # transferencia de los lotes
        

        //Buscar lote padre de la transferencia

        //Calculo del porcentaje de lados por calidades

        //llenado de tabla de bd: particiones

        //disminucion de pedido en bd del padre

        //ingreso del lote con el # consecutivo



        break;
}
