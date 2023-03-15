<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../include/connect_mvc.php";
include('../Models/Mdl_ConexionBD.php');
include('../Models/Mdl_Static.php');
include('../Models/Mdl_Excepciones.php');
include('../Models/Mdl_Solicitudes.php');

$debug = 0;
$idUser = $_SESSION['CREident'];

$obj_solicitudes = new Solicitud($debug, $idUser);

$ErrorLog = 'No se recibió';
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}

switch ($_GET["op"]) {
    case "enviarsolicitud":
        $idRendimiento = (isset($_POST['idRendimiento'])) ? trim($_POST['idRendimiento']) : '';
        $descripcionSolicitud = (isset($_POST['descripcionSolicitud'])) ? trim($_POST['descripcionSolicitud']) : '';
        $log = '';
        if ($idRendimiento == '') {
            $ErrorLog .= 'Rendimiento,';
            $log = '1';
        }
        if ($descripcionSolicitud == '') {
            $ErrorLog .= 'Descripción Solicitud,';
            $log = '1';
        }
        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_solicitudes->errorBD($ErrorLog, 0);
        }
        $obj_solicitudes->beginTransaction();
        $datos = $obj_solicitudes->agregarSolicitud($idRendimiento, $descripcionSolicitud);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
             $obj_solicitudes->errorBD($e->getMessage(), 1);
        }

        $datos = $obj_solicitudes->actualizarSolicRendi($idRendimiento);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
             $obj_solicitudes->errorBD($e->getMessage(), 1);
        }

        $obj_solicitudes->insertarCommit();
        echo '1|La solicitud de edición se ha enviado correctamente.';
        break;
    case "aceptar":
        $idSolicitud = (isset($_POST['idSolicitud'])) ? trim($_POST['idSolicitud']) : '';
        $log = '';
        if ($idSolicitud == '') {
            $ErrorLog .= 'Solicitud,';
            $log = '1';
        }
        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_solicitudes->errorBD($ErrorLog, 0);
        }
        $datos = $obj_solicitudes->aceptarSolicitud($idSolicitud);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
             $obj_solicitudes->errorBD($e->getMessage(), 1);
        }
        echo '1|La solicitud de edición se ha aceptado correctamente.';
        break;
    case "cancelar":
        $idSolicitud = (isset($_POST['idSolicitud'])) ? trim($_POST['idSolicitud']) : '';
        $log = '';
        if ($idSolicitud == '') {
            $ErrorLog .= 'Solicitud,';
            $log = '1';
        }
        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_solicitudes->errorBD($ErrorLog, 0);
        }
        $datos = $obj_solicitudes->rechazarSolicitud($idSolicitud);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
             $obj_solicitudes->errorBD($e->getMessage(), 1);
        }
        echo '1|La solicitud de edición se ha rechazado correctamente.';
        break;
    case "abriredicion":
        $id = (isset($_POST['id'])) ? trim($_POST['id']) : '';
        $log = '';
        if ($id == '') {
            $ErrorLog .= 'Rendimiento,';
            $log = '1';
        }
        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_solicitudes->errorBD($ErrorLog, 0);
        }
        $obj_solicitudes->beginTransaction();
        $datos = $obj_solicitudes->abrirEdicion($id);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_solicitudes->errorBD($e->getMessage(), 1);
        }
        #Valida que los datos No Tengan Operaciones para eliminar Inventario y sublote
        $DataValidaUsoDelLote = $obj_solicitudes->validaCambioDePzas($id);
        $DataValidaUsoDelLote = $DataValidaUsoDelLote == '' ? array() : $DataValidaUsoDelLote;
        if (!is_array($DataValidaUsoDelLote)) {
            echo "0|Error, $DataValidaUsoDelLote";
        }

        if (count($DataValidaUsoDelLote) > 0) {
            $datos = $obj_solicitudes->limpiarInventarios($id);
            try {
                Excepciones::validaMsjError($datos);
            } catch (Exception $e) {
                $obj_solicitudes->errorBD($e->getMessage(), 1);
            }
        }
        $obj_solicitudes->insertarCommit();
        echo '1|La solicitud de edición se ha rechazado correctamente.';
        break;
}
