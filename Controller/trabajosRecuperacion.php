<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../include/connect_mvc.php";
include('../Models/Mdl_ConexionBD.php');
include('../Models/Mdl_TrabajosRecupera.php');
include('../Models/Mdl_Static.php');
include('../Models/Mdl_Excepciones.php');

$debug = 0;
$idUser = $_SESSION['CREident'];

$obj_trab = new TrabajosRecupera($debug, $idUser);

$ErrorLog = 'No se recibió';
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}

switch ($_GET["op"]) {
    case "agregarrecuperacion":
        $fecha = (isset($_POST['fecha'])) ? $_POST['fecha'] : '';
        $fechaEntrega = (isset($_POST['fechaEntrega'])) ? $_POST['fechaEntrega'] : '';
        $idRendInicio = (isset($_POST['idRendInicio'])) ? $_POST['idRendInicio'] : '';
        $trabajadorRecibio = (isset($_POST['trabajadorRecibio'])) ? $_POST['trabajadorRecibio'] : '';
        $idCatPrograma = (isset($_POST['idCatPrograma'])) ? $_POST['idCatPrograma'] : '';
        $idRendRecuperado = (isset($_POST['idRendRecuperado'])) ? $_POST['idRendRecuperado'] : '';
        $totalRecuperado = (isset($_POST['totalRecuperado'])) ? $_POST['totalRecuperado'] : '';
        $tipoLoteInicio = (isset($_POST['tipoLoteInicio'])) ? $_POST['tipoLoteInicio'] : '';
        $nameLote = (isset($_POST['nameLote'])) ? $_POST['nameLote'] : '';
        $defecto = (isset($_POST['defecto'])) ? $_POST['defecto'] : '';
        $observaciones = (isset($_POST['observaciones'])) ? $_POST['observaciones'] : '';

        Excepciones::validaLlenadoDatos(array(
            " Fecha" => $fecha,
            " total Recuperado" => $totalRecuperado,
            " Programa" => $idCatPrograma,
            " Lote de Recuperación" => $idRendRecuperado,
            " Tipo de Lote de Recuperación" => $tipoLoteInicio,
            " fecha entrega" => $fechaEntrega,
            " trabajador Recibio" => $trabajadorRecibio
        ), $obj_trab);

        if ($tipoLoteInicio == '1') {
            Excepciones::validaLlenadoDatos(array(
                " Lote de Inicial" => $idRendInicio,
            ), $obj_trab);
        }

        $obj_trab->beginTransaction();
        $ArrayDatosTrabajador = explode('|', $trabajadorRecibio);
        $datos = $obj_trab->registrarRecuperacion(
            $fecha,
            $fechaEntrega,
            $idRendInicio,
            $ArrayDatosTrabajador[0],
            $ArrayDatosTrabajador[1],

            $idCatPrograma,
            $idRendRecuperado,
            $totalRecuperado,
            $tipoLoteInicio,
            $nameLote,
            $observaciones,
            $defecto
        );
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_trab->errorBD($e->getMessage(), 1);
        }

        $idMaterialRecuperado = $datos[2];

        if ($tipoLoteInicio == '1' and $idRendInicio != '') {
            $datos = $obj_trab->disminucionInventarioRechazado($idMaterialRecuperado);
            try {
                Excepciones::validaMsjError($datos);
            } catch (Exception $e) {
                $obj_trab->errorBD($e->getMessage(), 1);
            }
        }

        $datos = $obj_trab->aumentoInventarioEmpacado($idMaterialRecuperado);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_trab->errorBD($e->getMessage(), 1);
        }

        $datos = $obj_trab->actualizaRendimiento($idMaterialRecuperado);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_trab->errorBD($e->getMessage(), 1);
        }
        #Recalculo de cueros en ventas
        /* $datos = $obj_trab->getPorcDismVentas($idRendRecuperado, $totalRecuperado);
        $datos = Excepciones::validaConsulta($datos);
        $datos = $datos == '' ? array() : $datos;
        if (count($datos) <= 0) {
            $obj_trab->errorBD("Error, lote de recuperación no encontrado, notifica al departamento de sistemas.", 1);
        }
        $total_s = $datos['total_s'];
        $setsTotalesEmp = $datos['setsTotalesEmp'];
        print_r($datos);
        echo "<br>";
        echo "Total S: ", $total_s;
        echo "<br>";
        echo "Sets Totales Emp: ", $setsTotalesEmp;
        echo "<br>";
        $factorComun= $setsTotalesEmp/$total_s;
        echo "Factor Comun: ", $factorComun;
        echo "<br>";
        $datosVentas = $obj_trab->getCuerosDismVentas($idRendRecuperado);
        print_r($datosVentas);*/

        $datos = $obj_trab->actualizaCuerosVentas($idRendRecuperado);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_trab->errorBD($e->getMessage(), 1);
        }

        if ($tipoLoteInicio == '1' and $idRendInicio != '') {
            $datos = $obj_trab->actualizaRendRechazado($idMaterialRecuperado);
            try {
                Excepciones::validaMsjError($datos);
            } catch (Exception $e) {
                $obj_trab->errorBD($e->getMessage(), 1);
            }
        }
        $obj_trab->insertarCommit();
        echo "1|Agregar Trabajo de Recuperación Correctamente.";

        break;

    case "cerrarerror":
        $id = (isset($_POST['id'])) ? $_POST['id'] : '';
        Excepciones::validaLlenadoDatos(array(
            " Error" => $id
        ), $obj_trab);
        $datos = Funciones::cambiarEstatus("logspzasreacond", '3', "estado", $id, $obj_trab->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_trab->errorBD($e->getMessage(), 1);
        }
        echo "1|Linea Rechazada Correctamente.";

        break;
}
