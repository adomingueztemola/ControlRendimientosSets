<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../include/connect_mvc.php";
include('../Models/Mdl_ConexionBD.php');
include('../Models/Mdl_Devolucion.php');
include('../Models/Mdl_Static.php');
include('../Models/Mdl_Excepciones.php');

$debug = 0;
$idUser = $_SESSION['CREident'];

$obj_devolucion = new Devolucion($debug, $idUser);

$ErrorLog = 'No se recibió';
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}

switch ($_GET["op"]) {
    case "initdevolucion":
        $rma = (isset($_POST['rma'])) ? $_POST['rma'] : '';
        $fecha = (isset($_POST['fecha'])) ? $_POST['fecha'] : '';
        $idVenta = (isset($_POST['idVenta'])) ? $_POST['idVenta'] : '';
        Excepciones::validaLlenadoDatos(array(
            " Venta" => $idVenta, " Fecha" => $fecha, " RMA" => $rma
        ), $obj_devolucion);

        $datos = $obj_devolucion->initDevolucion($rma, $fecha, $idVenta);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_devolucion->errorBD($e->getMessage(), 1);
        }
        echo "1|Inicializa Devolución Correctamente.";
        break;
    case "detdevolucion":
        $cant = (isset($_POST['cant'])) ? $_POST['cant'] : '';
        $programa = (isset($_POST['programa'])) ? $_POST['programa'] : '';

        Excepciones::validaLlenadoDatos(array(
            " Programa" => $programa, " Cantidad" => $cant
        ), $obj_devolucion);

        $datos = $obj_devolucion->agregarDetDevolucion($programa, $cant);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_devolucion->errorBD($e->getMessage(), 1);
        }
        echo "1|Registro de Detalle de Devolución Correctamente.";

        break;
    case "cancelarventa":
        $id = (isset($_POST['id'])) ? $_POST['id'] : '';
        Excepciones::validaLlenadoDatos(array(
            " Venta" => $id
        ), $obj_devolucion);
        $obj_devolucion->beginTransaction();

        $datos = $obj_devolucion->devolucionAllEmpacados($id);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_devolucion->errorBD($e->getMessage(), 1);
        }
        $datos = Funciones::cambiarEstatus("ventas", '0', "estado", $id, $obj_devolucion->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_devolucion->errorBD($e->getMessage(), 1);
        }
        $obj_devolucion->insertarCommit();
        $_SESSION['CRESuccessDevolucion'] = "Cancelación de Venta Correcta";
        echo "1|Cancelación de Venta Correcta";
        break;
    case "eliminardetdevolucion":
        $id = (isset($_POST['id'])) ? $_POST['id'] : '';
        Excepciones::validaLlenadoDatos(array(
            " Detalle de Devolución" => $id
        ), $obj_devolucion);

        $datos = Funciones::eliminarRegistro("detdevolucionrma", $id, "id", $obj_devolucion->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_devolucion->errorBD($e->getMessage(), 1);
        }
        echo "1|Eliminación del Detalle de Devolución Correctamente.";

        break;
    case "findevolucion":
        $Data= $obj_devolucion->getDevolucionAbierta();
        $idDevolucion= $Data["id"];
        $datos = Funciones::cambiarEstatus("devolucionesrma", '2', "estado", $idDevolucion, $obj_devolucion->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_devolucion->errorBD($e->getMessage(), 1);
        }
        echo "1|Finalización de la Devolución Correctamente.";
        break;
    case "cancelardevolucion":
        $Data= $obj_devolucion->getDevolucionAbierta();
        $idDevolucion= $Data["id"];
        $datos = $obj_devolucion->eliminarDevolucion($id);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_devolucion->errorBD($e->getMessage(), 1);
        }
        echo "1|Cancelación de la Devolución Correctamente.";
        break;
    case "cambiarestatusdevol":
        $id = (isset($_POST['id'])) ? $_POST['id'] : '';
        Excepciones::validaLlenadoDatos(array(
            " Devolución" => $id
        ), $obj_devolucion);

        $datos = Funciones::cambiarEstatus("devolucionesrma", '0', "estado", $id, $obj_devolucion->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_devolucion->errorBD($e->getMessage(), 1);
        }
        echo "1|Cancelación de la Devolución Correctamente.";
        break;
}
