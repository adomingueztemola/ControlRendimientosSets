<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../include/connect_mvc.php";
include('../Models/Mdl_ConexionBD.php');
include('../Models/Mdl_TipoVenta.php');
include('../Models/Mdl_Static.php');
include('../Models/Mdl_Excepciones.php');

$debug = 0;
$idUser = $_SESSION['CREident'];

$obj_tipo = new TipoVenta($debug, $idUser);

$ErrorLog = 'No se recibió';
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}

switch ($_GET["op"]) {
    case "agregartipo":
        $tipo = (isset($_POST['tipo'])) ? trim($_POST['tipo']) : '';
        $fact = (isset($_POST['fact'])) ? trim($_POST['fact']) : '';
        $cargaVenta = (isset($_POST['cargaVenta'])) ? trim($_POST['cargaVenta']) : '';

        $log = '';
        if ($tipo == '') {
            $ErrorLog .= ' Tipo de Venta,';
            $log = '1';
        }
        if ($fact == '') {
            $ErrorLog .= ' Tipo de Factura,';
            $log = '1';
        }
        if ($cargaVenta == '') {
            $ErrorLog .= ' Carga Venta,';
            $log = '1';
        }
        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_tipo->errorBD($ErrorLog, 0);
        }
        #Valida que Producto no exista en el Catalogo
        $resultValidacion = Funciones::validarDatoTabla("cattiposventas", "nombre", $tipo, $debug, $obj_tipo->getConexion());
        try {
            Excepciones::validaMsjError($resultValidacion);
        } catch (Exception $e) {
            $obj_tipo->errorBD($e->getMessage(), 1);
        }
        if ($resultValidacion[1] >= 1) {
            $obj_tipo->errorBD("Existe un tipo de venta con el mismo nombre, verifica tus datos.", 1);
        }
        $datos = $obj_tipo->agregarTipoVenta($tipo, $fact, $cargaVenta);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_tipo->errorBD($e->getMessage(), 1);
        }
        echo '1|Tipo de Venta Almacenado Correctamente.';
        break;
    case "cambiaestatus":
        $id = (isset($_POST['id'])) ? $_POST['id'] : '0';
        $estatus = (isset($_POST['estatus'])) ? $_POST['estatus'] : '';
        $log = '';
        if ($id <= '0') {
            $ErrorLog .= ' Tipo de Venta,';
            $log = '1';
        }
        if ($estatus == '') {
            $ErrorLog .= ' estatus,';
            $log = '1';
        }
        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_tipo->errorBD($ErrorLog, 0);
        }
        $newEstatus = $estatus == '1' ? "0" : "1";

        $datos = Funciones::cambiarEstatus("cattiposventas", $newEstatus, "estado", $id, $obj_tipo->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_tipo->errorBD($e->getMessage(), 1);
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
            $ErrorLog .= 'Tipo de Venta,';
            $log = '1';
        }
        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_procesos->errorBD($ErrorLog, 0);
        }

        $datos = Funciones::obtenerDetallado('cattiposventas', 'id', $id, $obj_tipo->getConexion(), $debug);
        /*   try{
                Excepciones::validaMsjError($datos);
            }catch (Exception $e){
                $obj_procesos->errorBD($e->getMessage(), 1);
    
            }*/
        $json_encode = json_encode($datos);
        echo  '1|' . $json_encode;

        break;
    case "editartipo":
        $id = (isset($_POST['id'])) ? $_POST['id'] : '0';
        $tipo = (isset($_POST['fact'])) ? $_POST['fact'] : '';
        $cargaVenta = (isset($_POST['cargaVenta'])) ? trim($_POST['cargaVenta']) : '';

        $log = '';
        if ($id <= '0') {
            $ErrorLog .= 'Tipo de Venta,';
            $log = '1';
        }
        if ($tipo == '') {
            $ErrorLog .= ' modo de Factura,';
            $log = '1';
        }
        if ($cargaVenta == '') {
            $ErrorLog .= ' Carga Venta,';
            $log = '1';
        }
        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_tipo->errorBD($ErrorLog, 0);
        }
        $datos = $obj_tipo->editarTipo($id, $tipo, $cargaVenta);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_tipo->errorBD($e->getMessage(), 1);
        }
        echo '1|Tipo de Venta Editado Correctamente.';

        break;
}
