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
    case "devolverventa":
        $id = (isset($_POST['id'])) ? $_POST['id'] : '';
        $devoluciones = (isset($_POST['devoluciones'])) ? $_POST['devoluciones'] : array();
        Excepciones::validaLlenadoDatos(array(
            " Venta" => $id,
            " Devoluciones" => $devoluciones
        ), $obj_devolucion);
        $obj_devolucion->beginTransaction();
        if (count($devoluciones) > 0) {
            $datos = $obj_devolucion->iniciarDevolucion($id);
            try {
                Excepciones::validaMsjError($datos);
            } catch (Exception $e) {
                $obj_devolucion->errorBD($e->getMessage(), 1);
            }
            $idDevolucion = $datos[2];

            foreach ($devoluciones as  $value) {
                $tipoInventario = (isset($_POST['inventario-' . $value])) ? $_POST['inventario-' . $value] : '';
                Excepciones::validaLlenadoDatos(array(
                    " Tipo de Inventario" => $tipoInventario
                ), $obj_devolucion);
                //Opciones de reintegro
                $datos = $obj_devolucion->insertarDetDevolucion($idDevolucion, $value, $tipoInventario);
                try {
                    Excepciones::validaMsjError($datos);
                } catch (Exception $e) {
                    $obj_devolucion->errorBD($e->getMessage(), 1);
                }
                $idDetDevolucion = $datos[2];
                switch ($tipoInventario) {
                    case '1':
                        $datos = $obj_devolucion->devolucionXLoteEmpacado($value, $idDetDevolucion);
                        try {
                            Excepciones::validaMsjError($datos);
                        } catch (Exception $e) {
                            $obj_devolucion->errorBD($e->getMessage(), 1);
                        }
                        break;
                    case '2':
                        $DataValida = $obj_devolucion->validaInventarioRechazados($idDetDevolucion);
                        if (count($DataValida) <= 0) {
                            $datos = $obj_devolucion->agregarInventarioRechazado($idDetDevolucion);
                            try {
                                Excepciones::validaMsjError($datos);
                            } catch (Exception $e) {
                                $obj_devolucion->errorBD($e->getMessage(), 1);
                            }
                        }
                        
                        $datos = $obj_devolucion->devolucionReintegroRechazados($value, $idDetDevolucion);
                        try {
                            Excepciones::validaMsjError($datos);
                        } catch (Exception $e) {
                            $obj_devolucion->errorBD($e->getMessage(), 1);
                        }

                        $datos = $obj_devolucion->marcarDevueltoLoteRechazo($value, $idDetDevolucion);
                        try {
                            Excepciones::validaMsjError($datos);
                        } catch (Exception $e) {
                            $obj_devolucion->errorBD($e->getMessage(), 1);
                        }

                        break;
                }
            }
            $datos = $obj_devolucion->actualizarTotalVenta($id);
            try {
                Excepciones::validaMsjError($datos);
            } catch (Exception $e) {
                $obj_devolucion->errorBD($e->getMessage(), 1);
            }
            $obj_devolucion->insertarCommit();
            echo "1|Devolución de Venta Ejecutada Correctamente.";
        } else {
            $obj_devolucion->errorBD("Selecciona al menos un artículo para iniciar la devolución.", 0);
        }
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
}
