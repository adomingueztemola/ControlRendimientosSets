<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../include/connect_mvc.php";
include('../Models/Mdl_ConexionBD.php');
include('../Models/Mdl_VentaPrevia.php');
include('../Models/Mdl_Static.php');
include('../Models/Mdl_Excepciones.php');
$debug = 0;
$idUser = $_SESSION['CREident'];

$obj_venta = new VentaPrevia($debug, $idUser);

$ErrorLog = 'No se recibió ';
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}

switch ($_GET["op"]) {
    case "requisionpzas":
        $id = (isset($_POST['id'])) ? trim($_POST['id']) : '';
        $piezasRequeridas = (isset($_POST['piezasRequeridas'])) ? trim($_POST['piezasRequeridas']) : '';
        Excepciones::validaLlenadoDatos(array(
            " Detalle de Ventas" => $id,
            " Piezas Requeridas" => $piezasRequeridas,
        ), $obj_venta);
        $Data = $obj_venta->consultaRequesicionAbierta($id);
        $Data = Excepciones::validaConsulta($Data);
        $Data = $Data == '' ? array() : $Data;
        if (count($Data) <= 0) {
            $datos = $obj_venta->agregarPzasRequeridas($id, $piezasRequeridas);
            try {
                Excepciones::validaMsjError($datos);
            } catch (Exception $e) {
                $obj_venta->errorBD($e->getMessage(), 0);
            }
        } else {
            $datos = $obj_venta->actualizaPiezas($id, $piezasRequeridas);
            try {
                Excepciones::validaMsjError($datos);
            } catch (Exception $e) {
                $obj_venta->errorBD($e->getMessage(), 0);
            }
        }

        echo '1|Piezas Requeridas Almacenadas Correctamente.';
        break;
    case "abrirventa":
        $id = (isset($_POST['id'])) ? trim($_POST['id']) : '';
        Excepciones::validaLlenadoDatos(array(" Detalle de Ventas" => $id), $obj_venta);
        $datos = Funciones::cambiarEstatus("ventas", '1', "estado", $id, $obj_venta->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_venta->errorBD($e->getMessage(), 1);
        }
        echo '1|Venta Abierta Correctamente.';
        break;
}
