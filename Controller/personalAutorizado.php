<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../include/connect_mvc.php";
include('../Models/Mdl_ConexionBD.php');
include('../Models/Mdl_Static.php');

$debug = 0;
$idUser = $_SESSION['CREident'];

$obj_personal = new Trabajadores($debug, $idUser);

$ErrorLog = 'No se recibió';
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}

switch ($_GET["op"]) {
    case "agregarpersonal":
        $personal = (isset($_POST['personal'])) ? $_POST['personal'] : '';
        Excepciones::validaLlenadoDatos(array(" Personal" => $personal), $obj_personal);
        $datos = $obj_personal->agregarPersonal($personal);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_personal->errorBD($e->getMessage(), 1);
        }
        echo '1|Empleado Agregado Correctamente.';
        break;
    case "cambiaestatus":
        $id = (isset($_POST['id'])) ? $_POST['id'] : '0';
        $estatus = (isset($_POST['estatus'])) ? $_POST['estatus'] : '';
        $log = '';
        if ($id <= '0') {
            $ErrorLog .= ' Trabajador Autorizado,';
            $log = '1';
        }
        if ($estatus == '') {
            $ErrorLog .= ' estatus,';
            $log = '1';
        }
        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_personal->errorBD($ErrorLog, 0);
        }
        $newEstatus = $estatus == '1' ? "0" : "1";

        $datos = Funciones::cambiarEstatus("autorizapersonalreacond", $newEstatus, "estado", $id, $obj_personal->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_personal->errorBD($e->getMessage(), 1);
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
