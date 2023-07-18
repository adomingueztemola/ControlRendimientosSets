<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../include/connect_mvc.php";
include('../Models/Mdl_Static.php');

$debug = 0;
$idUser = $_SESSION['CREident'];

$obj_rendimiento = new RendimientoEtiq($debug, $idUser);

switch ($_GET["op"]) {
        //****************INICIAR LOTE DE ETIQUETAS ******************
    case 'initrendimiento':
        $fechaFinal = (!empty($_POST['fechaFinal'])) ? trim($_POST['fechaFinal']) : '';
        $semanaProduccion = (!empty($_POST['semanaProduccion'])) ? trim($_POST['semanaProduccion']) : '';
        $lote = (!empty($_POST['lote'])) ? trim($_POST['lote']) : '';
        $programa = (!empty($_POST['programa'])) ? trim($_POST['programa']) : '';
        $materiaPrima = (!empty($_POST['materiaPrima'])) ? trim($_POST['materiaPrima']) : '';
        $_1s = (isset($_POST['1s'])) ? trim($_POST['1s']) : '0';
        $_2s = (isset($_POST['2s'])) ? trim($_POST['2s']) : '0';
        $_3s = (isset($_POST['3s'])) ? trim($_POST['3s']) : '0';
        $_4s = (isset($_POST['4s'])) ? trim($_POST['4s']) : '0';
        $total_s = (isset($_POST['total_s'])) ? trim($_POST['total_s']) : '';
        $proveedor = (isset($_POST['proveedor'])) ? trim($_POST['proveedor']) : '';
        #VALIDACION DE DATOS
        Excepciones::validaLlenadoDatos(array(
            'fecha Final,' => $fechaFinal,
            'semana de Producción,' => $semanaProduccion,
            'lote,' => $lote,
            'programa,' => $programa,
            'materia Prima,' => $materiaPrima,
            'proveedor,' => $proveedor,

        ), $obj_rendimiento);

        #Array de Semana de Produccion
        $WeekYear = explode("-W", $semanaProduccion);

        $datos = $obj_rendimiento->registerNewLot(
            $fechaFinal,
            $WeekYear[1],
            $WeekYear[0],
            $lote,
            $programa,
            $materiaPrima,
            $_1s,
            $_2s,
            $_3s,
            $_4s,
            $total_s,
            $proveedor
        );
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_rendimiento->errorBD($e->getMessage(), 1);
        }
        echo '1|Inicio de Rendimiento Almacenado Correctamente.';
        break;
    case "geteditxuser":
        $Data = $obj_rendimiento->getEditXUser();
        $Data = Excepciones::validaConsulta($Data);
        $json_string = json_encode($Data);
        echo $json_string;
        break;
    case 'finishregister':
        $id = (!empty($_POST['id'])) ? trim($_POST['id']) : '0';
        if ($id == '0') {
            $DatosAbiertos = $obj_rendimiento->getEditXUser();
            $id =  $DatosAbiertos['id'];
        } else {
            $id = $edicion;
        }
        $datos = Funciones::cambiarEstatus("rendimientosetiquetas", "2", "estado", $id, $obj_rendimiento->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_rendimiento->errorBD($e->getMessage(), 1);
        }
        echo '1|El lote se almacenó Correctamente.';
        break;
}
