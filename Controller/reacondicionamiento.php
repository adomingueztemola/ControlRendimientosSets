<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../include/connect_mvc.php";
include('../Models/Mdl_Static.php');

$debug = 0;
$idUser = $_SESSION['CREident'];
$obj_reacondicionamiento = new Reacondicionamiento($debug, $idUser);

$ErrorLog = 'No se recibió';
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}

switch ($_GET["op"]) {
    case "cargajsonrecuperacion":
        $id = (isset($_POST['id'])) ? trim($_POST['id']) : '';

        $Data = $obj_reacondicionamiento->getRecuperacion($id);
        $Data = Excepciones::validaConsulta($Data);

        //Creamos el JSON
        $json_string = json_encode($Data);
        echo $json_string;



        break;
    case "agregarrecuperacion":
        $fecha = (isset($_POST['fecha'])) ? $_POST['fecha'] : '';
        $idRendInicio = (isset($_POST['idRendInicio'])) ? $_POST['idRendInicio'] : '';
        $trabajadorRecibio = (isset($_POST['trabajadorRecibio'])) ? $_POST['trabajadorRecibio'] : '';
        $idCatPrograma = (isset($_POST['idCatPrograma'])) ? $_POST['idCatPrograma'] : '';
        $tipoLoteInicio = (isset($_POST['tipoLoteInicio'])) ? $_POST['tipoLoteInicio'] : '';
        $nameLote = (isset($_POST['nameLote'])) ? $_POST['nameLote'] : '';
        $defecto = (isset($_POST['defecto'])) ? $_POST['defecto'] : '';
        $observaciones = (isset($_POST['observaciones'])) ? $_POST['observaciones'] : '';

        Excepciones::validaLlenadoDatos(array(
            " Fecha" => $fecha,
            " Programa" => $idCatPrograma,
            " Tipo de Lote de Recuperación" => $tipoLoteInicio,
            " trabajador Recibio" => $trabajadorRecibio
        ), $obj_reacondicionamiento);

        if ($tipoLoteInicio == '1') {
            Excepciones::validaLlenadoDatos(array(
                " Lote de Inicial" => $idRendInicio,
            ), $obj_reacondicionamiento);
        }

        $obj_reacondicionamiento->beginTransaction();
        $ArrayDatosTrabajador = explode('|', $trabajadorRecibio);
        $datos = $obj_reacondicionamiento->registrarRecuperacion(
            $fecha,
            $idRendInicio,
            $ArrayDatosTrabajador[0],
            $ArrayDatosTrabajador[1],
            $idCatPrograma,
            $tipoLoteInicio,
            $nameLote,
            $observaciones,
            $defecto
        );
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_reacondicionamiento->errorBD($e->getMessage(), 1);
        }
        $obj_reacondicionamiento->insertarCommit();
        echo "1|Agregar Trabajo de Recuperación Correctamente.";
        break;
    case "agregarfechaentrega":
        $fechaEntrega = (isset($_POST['fechaEntrega'])) ? $_POST['fechaEntrega'] : '';
        $id = (isset($_POST['id'])) ? $_POST['id'] : '';
        Excepciones::validaLlenadoDatos(array(
            " Fecha" => $fechaEntrega,
            " Reacondicionamiento" => $id
        ), $obj_reacondicionamiento);
        $datos = $obj_reacondicionamiento->registrarFechaEntrega(
            $fechaEntrega,
            $id
        );
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_reacondicionamiento->errorBD($e->getMessage(), 1);
        }
        echo "1|Actualización de Fecha de Entrega Correctamente.";
        break;
    case "agregarloterecup":
        $loteRecup = (isset($_POST['loteRecup'])) ? $_POST['loteRecup'] : '';
        $id = (isset($_POST['id'])) ? $_POST['id'] : '';
        Excepciones::validaLlenadoDatos(array(
            " Lote" => $loteRecup,
            " Reacondicionamiento" => $id
        ), $obj_reacondicionamiento);
        $datos = $obj_reacondicionamiento->registrarLoteRecup(
            $loteRecup,
            $id
        );
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_reacondicionamiento->errorBD($e->getMessage(), 1);
        }
        echo "1|Actualización de Lote Recuperado Correctamente.";
        break;
    case "agregartotal":
        $total = (isset($_POST['total'])) ? $_POST['total'] : '';
        $id = (isset($_POST['id'])) ? $_POST['id'] : '';
        $_12 = (isset($_POST['_12'])) ? $_POST['_12'] : '';
        $_3 = (isset($_POST['_3'])) ? $_POST['_3'] : '';
        $_6 = (isset($_POST['_6'])) ? $_POST['_6'] : '';
        $_9 = (isset($_POST['_9'])) ? $_POST['_9'] : '';

        Excepciones::validaLlenadoDatos(array(
            " Total" => $total,
            " Reacondicionamiento" => $id,
        ), $obj_reacondicionamiento);
        $datos = $obj_reacondicionamiento->registrarTotal($total, $id, $_12, $_3, $_9, $_6);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_reacondicionamiento->errorBD($e->getMessage(), 1);
        }
        echo "1|Actualización de Total Correctamente.";
        break;
    case "cerrarrecuperacion":
        $id = (isset($_POST['id'])) ? $_POST['id'] : '';
        $idLote = (isset($_POST['idLote'])) ? $_POST['idLote'] : '';

        Excepciones::validaLlenadoDatos(array(
            " Reacondicionamiento" => $id,
            " Lote" => $idLote
        ), $obj_reacondicionamiento);
        $obj_reacondicionamiento->beginTransaction();
        /************ AGREGAR PIEZAS RECUPERADAS**************/
        //Verifica que exista inventario de piezas recuperadas
        $Stk = $obj_reacondicionamiento->getStkRecuperacion($idLote);
        $Stk = Excepciones::validaConsulta($Stk);
        if (count($Stk) > 0) {        //Actualiza Stock de piezas recuperadas
            $datos = $obj_reacondicionamiento->agregarPzasRecuperadas($id);
            try {
                Excepciones::validaMsjError($datos);
            } catch (Exception $e) {
                $obj_reacondicionamiento->errorBD($e->getMessage(), 1);
            }
        } else {                    //Inserta Stock de piezas recuperadas
            $datos = $obj_reacondicionamiento->agregarStkPzasRecuperadas($id);
            try {
                Excepciones::validaMsjError($datos);
            } catch (Exception $e) {
                $obj_reacondicionamiento->errorBD($e->getMessage(), 1);
            }
        }
        /************ QUITAR DE SCRAP **************/
        $datos = $obj_reacondicionamiento->disminuirScrap($id);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_reacondicionamiento->errorBD($e->getMessage(), 1);
        }
        /************ CAMBIAR ESTATUS **************/
        $datos = Funciones::cambiarEstatus("materialesrecuperados", '2', "estado", $id, $obj_reacondicionamiento->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_reacondicionamiento->errorBD($e->getMessage(), 1);
        }
        $obj_reacondicionamiento->insertarCommit();
        echo "1|Actualización de Piezas Recuperadas Correctamente.";

        break;
    case "eliminarrecuperacion":
        $id = (isset($_POST['id'])) ? $_POST['id'] : '';

        Excepciones::validaLlenadoDatos(array(
            " Reacondicionamiento" => $id,
        ), $obj_reacondicionamiento);
        /************ QUITAR DE SCRAP **************/
        $datos = $obj_reacondicionamiento->eliminarReacondicionamiento($id);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_reacondicionamiento->errorBD($e->getMessage(), 1);
        }
        echo "1|Eliminación de Registro de Reacondicionamiento Correcto.";
        break;
}
