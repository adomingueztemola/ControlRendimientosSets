<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../include/connect_mvc.php";
include('../Models/Mdl_ConexionBD.php');
include('../Models/Mdl_Traspaso.php');
include('../Models/Mdl_Static.php');
include('../Models/Mdl_Excepciones.php');

$debug =0;
$idUser = $_SESSION['CREident'];

$obj_traspaso= new Traspaso($debug, $idUser);

$ErrorLog = 'No se recibió';
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}

switch ($_GET["op"]) {
   case "agregartraspaso":
    $loteEntrada = (isset($_POST['loteEntrada'])) ? trim($_POST['loteEntrada']) : '';
    $cantTraspaso = (isset($_POST['cantTraspaso'])) ? trim($_POST['cantTraspaso']) : '';
    $loteSalida = (isset($_POST['loteSalida'])) ? trim($_POST['loteSalida']) : '';
        $log = '';
        if ($loteEntrada == '') {
            $ErrorLog .= 'Lote de Entrada,';
            $log = '1';
        }  
        if ($loteSalida == '') {
            $ErrorLog .= 'Lote de Salida,';
            $log = '1';
        } 
        if ($cantTraspaso == '') {
            $ErrorLog .= 'cantidad,';
            $log = '1';
        }       
        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_traspaso->errorBD($ErrorLog, 0);
        }
        $datos = $obj_traspaso->agregarTraspaso($loteEntrada, $loteSalida, $cantTraspaso);
        try{
            Excepciones::validaMsjError($datos);
        }catch (Exception $e){
            $obj_traspaso->errorBD($e->getMessage(), 1);

        }
        echo '1|Traspaso Almacenado Correctamente.';
    break;

    case "eliminartraspaso":
        $id = (isset($_POST['id'])) ? trim($_POST['id']) : '';
        $log = '';
        if ($id == '') {
            $ErrorLog .= 'Traspaso,';
            $log = '1';
        }        
        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_traspaso->errorBD($ErrorLog, 0);
        }
        $datos = $obj_traspaso->eliminarTraspaso($id);
        try{
            Excepciones::validaMsjError($datos);
        }catch (Exception $e){
            $obj_traspaso->errorBD($e->getMessage(), 1);

        }
        echo '1|Traspaso Eliminado Correctamente.';




        break;
}
