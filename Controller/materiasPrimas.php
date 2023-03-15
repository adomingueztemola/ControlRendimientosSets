<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../include/connect_mvc.php";
include('../Models/Mdl_ConexionBD.php');
include('../Models/Mdl_MateriaPrima.php');
include('../Models/Mdl_Static.php');
include('../Models/Mdl_Excepciones.php');

$debug = 0;
$idUser = $_SESSION['CREident'];

$obj_materias = new MateriaPrima($debug, $idUser);

$ErrorLog = 'No se recibió';
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}

switch ($_GET["op"]) {
    case "agregarmateria":
        $materiaPrima = (isset($_POST['materiaPrima'])) ? trim($_POST['materiaPrima']) : '';
        $tipo = (isset($_POST['tipo'])) ? trim($_POST['tipo']) : '';
        $mnd = (isset($_POST['mnd'])) ? trim($_POST['mnd']) : '';

        $log = '';
        if ($materiaPrima == '') {
            $ErrorLog .= ' materia Prima,';
            $log = '1';
        }
        if ($tipo == '') {
            $ErrorLog .= ' tipo de Materia Prima,';
            $log = '1';
        }
        if ($mnd == '') {
            $ErrorLog .= ' moneda de costo de Materia Prima,';
            $log = '1';
        }
        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_materias->errorBD($ErrorLog, 0);
        }
        #Valida que Producto no exista en el Catalogo
        $resultValidacion = Funciones::validarDatoTabla("catmateriasprimas", "nombre", $materiaPrima, $debug, $obj_materias->getConexion());
        try {
            Excepciones::validaMsjError($resultValidacion);
        } catch (Exception $e) {
            $obj_materias->errorBD($e->getMessage(), 1);
        }
        if ($resultValidacion[1] >= 1) {
            $obj_materias->errorBD("Existe una materia prima con el mismo nombre, verifica tus datos.", 1);
        }
        $datos = $obj_materias->agregarMateria($materiaPrima, $tipo, $mnd);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_materias->errorBD($e->getMessage(), 1);
        }
        echo '1|Materia Almacenada Correctamente.';
        break;
    case "cambiaestatus":
        $id = (isset($_POST['id'])) ? $_POST['id'] : '0';
        $estatus = (isset($_POST['estatus'])) ? $_POST['estatus'] : '';
        $log = '';
        if ($id <= '0') {
            $ErrorLog .= ' Materia Prima,';
            $log = '1';
        }
        if ($estatus == '') {
            $ErrorLog .= ' estatus,';
            $log = '1';
        }
        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_materias->errorBD($ErrorLog, 0);
        }
        $newEstatus = $estatus == '1' ? "0" : "1";

        $datos = Funciones::cambiarEstatus("catmateriasprimas", $newEstatus, "estado", $id, $obj_materias->getConexion(), $debug);
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

    case "detallado":
        $id = (isset($_POST['id'])) ? $_POST['id'] : '0';
        $log = '';
        if ($id <= '0') {
            $ErrorLog .= 'Materia Prima,';
            $log = '1';
        }
        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_procesos->errorBD($ErrorLog, 0);
        }

        $datos = Funciones::obtenerDetallado('catmateriasprimas', 'id', $id, $obj_materias->getConexion(), $debug);
        /*   try{
            Excepciones::validaMsjError($datos);
        }catch (Exception $e){
            $obj_procesos->errorBD($e->getMessage(), 1);

        }*/
        $json_encode = json_encode($datos);
        echo  '1|' . $json_encode;
        break;
    case "editarmateria":
        $id = (isset($_POST['id'])) ? $_POST['id'] : '0';
        $tipo = (isset($_POST['tipo'])) ? $_POST['tipo'] : '';
        $mnd = (isset($_POST['mnd'])) ? $_POST['mnd'] : '';

        $log = '';
        if ($id <= '0') {
            $ErrorLog .= 'Materia,';
            $log = '1';
        }
        if ($tipo == '') {
            $ErrorLog .= ' tipo de Materia,';
            $log = '1';
        }
        if ($mnd == '') {
            $ErrorLog .= ' moneda,';
            $log = '1';
        }
        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_materias->errorBD($ErrorLog, 0);
        }
        $datos = $obj_materias->editarMateria($id, $tipo, $mnd);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_materias->errorBD($e->getMessage(), 1);
        }
        echo '1|Materia Editada Correctamente.';

        break;
}
