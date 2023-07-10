<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../include/connect_mvc.php";
include('../Models/Mdl_Static.php');

$debug = 10;
$idUser = $_SESSION['CREident'];

$obj_grosor = new Grosor($debug, $idUser);

$ErrorLog = 'No se recibió';
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}

switch ($_GET["op"]) {

    case "agregargros":
        $grosor = (isset($_POST['grosor'])) ? trim($_POST['grosor']) : '';
        $log = '';
        Excepciones::validaLlenadoDatos(array(
            " Grosor" => $grosor
        ), $obj_grosor);
        #Valida que Grosor no exista en el Catalogo
        $resultValidacion = Funciones::validarDatoTabla("catgrosores", "nombre", $materiaPrima, $debug, $obj_grosor->getConexion());
        try {
            Excepciones::validaMsjError($resultValidacion);
        } catch (Exception $e) {
            $obj_grosor->errorBD($e->getMessage(), 1);
        }
        if ($resultValidacion[1] >= 1) {
            $obj_grosor->errorBD("Existe un grosor con el mismo nombre, verifica tus datos.", 1);
        }

        $datos = $obj_grosor->agregarGrosor($grosor);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_grosor->errorBD($e->getMessage(), 1);
        }
        echo '1|Grosor Almacenado Correctamente.';
        break;
    case "cambiaestatus":
        $id = (isset($_POST['id'])) ? $_POST['id'] : '0';
        $estatus = (isset($_POST['estatus'])) ? $_POST['estatus'] : '';
        $log = '';
        if ($id <= '0') {
            $ErrorLog .= ' Grosor,';
            $log = '1';
        }
        if ($estatus == '') {
            $ErrorLog .= ' estatus,';
            $log = '1';
        }
        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_grosor->errorBD($ErrorLog, 0);
        }
        $newEstatus = $estatus == '1' ? "0" : "1";

        $datos = Funciones::cambiarEstatus("catgrosores", $newEstatus, "estado", $id,  $obj_grosor->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_materias->errorBD($e->getMessage(), 1);
        }
        $iconEstatus = ($newEstatus == 1) ?
            <<<EOD
        <button type="button" title='Activar' onclick="cambiaEstatus({$id}, {$newEstatus})" 
        class="btn btn-xs btn-outline-success"><i class=' fas fa-power-off'></i></button>
        EOD
            :
            <<<EOD
        <button type="button" title='Desactivar' onclick="cambiaEstatus({$id}, {$newEstatus})" 
        class="btn btn-xs btn-outline-danger"><i class=' fas fa-power-off'></i></button>
        EOD;
        echo "1|Estatus Cambiado Correctamente.|" . $iconEstatus;
        break;
}
