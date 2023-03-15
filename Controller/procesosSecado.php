<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../include/connect_mvc.php";
include('../Models/Mdl_ConexionBD.php');
include('../Models/Mdl_Proceso.php');
include('../Models/Mdl_Static.php');
include('../Models/Mdl_Excepciones.php');

$debug = 0;
$idUser = $_SESSION['CREident'];

$obj_procesos = new ProcesoSecado($debug, $idUser);

$ErrorLog = 'No se recibió';
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}

switch ($_GET["op"]) {
    case "agregarproceso":
        $codigo = (isset($_POST['codigo'])) ? trim($_POST['codigo']) : '';
        $proceso = (isset($_POST['proceso'])) ? trim($_POST['proceso']) : '';
        $tipo = (isset($_POST['tipo'])) ? trim($_POST['tipo']) : '';

        $log = '';
        if ($codigo == '') {
            $ErrorLog .= ' código,';
            $log = '1';
        }
        if ($proceso == '') {
            $ErrorLog .= ' proceso de secado,';
            $log = '1';
        }
        if ($tipo == '') {
            $ErrorLog .= ' tipo,';
            $log = '1';
        }
        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_procesos->errorBD($ErrorLog, 0);
        }
        #Valida que Codigo no exista en el Catalogo
        $resultValidacion = Funciones::validarDatoTabla("catprocesos", "codigo", $codigo, $debug, $obj_procesos->getConexion());
        try {
            Excepciones::validaMsjError($resultValidacion);
        } catch (Exception $e) {
            $obj_procesos->errorBD($e->getMessage(), 1);
        }
        if ($resultValidacion[1] >= 1) {
            $obj_procesos->errorBD("Existe un código semejante, verifica tus datos.", 1);
        }

        $resultValidacion = Funciones::validarDatoTabla("catprocesos", "nombre", $proceso,  $debug, $obj_procesos->getConexion());
        try {
            Excepciones::validaMsjError($resultValidacion);
        } catch (Exception $e) {
            $obj_procesos->errorBD($e->getMessage(), 1);
        }
        if ($resultValidacion[1] >= 1) {
            $obj_procesos->errorBD("Existe un proceso con el mismo nombre, verifica tus datos.", 1);
        }
        $datos = $obj_procesos->agregarProceso($codigo, $proceso, $tipo);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_procesos->errorBD($e->getMessage(), 1);
        }
        echo '1|Proceso Almacenado Correctamente.';
        break;
    case "cambiaestatus":
        $id = (isset($_POST['id'])) ? $_POST['id'] : '0';
        $estatus = (isset($_POST['estatus'])) ? $_POST['estatus'] : '';
        $log = '';
        if ($id <= '0') {
            $ErrorLog .= 'Proceso de Secado,';
            $log = '1';
        }
        if ($estatus == '') {
            $ErrorLog .= ' estatus,';
            $log = '1';
        }
        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_procesos->errorBD($ErrorLog, 0);
        }
        $newEstatus = $estatus == '1' ? "0" : "1";

        $datos = Funciones::cambiarEstatus("catprocesos", $newEstatus, "estado", $id, $obj_procesos->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_procesos->errorBD($e->getMessage(), 1);
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
    case 'detallado':
        $id = (isset($_POST['id'])) ? $_POST['id'] : '0';
        $log = '';
        if ($id <= '0') {
            $ErrorLog .= 'Proceso de Secado,';
            $log = '1';
        }
        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_procesos->errorBD($ErrorLog, 0);
        }

        $datos = Funciones::obtenerDetallado('catprocesos', 'id', $id, $obj_procesos->getConexion(), $debug);
        /*   try{
            Excepciones::validaMsjError($datos);
        }catch (Exception $e){
            $obj_procesos->errorBD($e->getMessage(), 1);

        }*/
        $json_encode = json_encode($datos);
        echo  '1|' . $json_encode;

        break;
    case "editarproceso":
        $id = (isset($_POST['id'])) ? $_POST['id'] : '0';
        $tipo = (isset($_POST['tipo'])) ? $_POST['tipo'] : '';
        $log = '';
        if ($id <= '0') {
            $ErrorLog .= 'Proceso de Secado,';
            $log = '1';
        }
        if ($tipo == '') {
            $ErrorLog .= ' tipo de Venta,';
            $log = '1';
        }
        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_procesos->errorBD($ErrorLog, 0);
        }
        $datos = $obj_procesos->editarProceso($id, $tipo);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_procesos->errorBD($e->getMessage(), 1);
        }
        echo '1|Proceso Editado Correctamente.';

        break;

    }
