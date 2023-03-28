<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../include/connect_mvc.php";
include('../Models/Mdl_ConexionBD.php');
include('../Models/Mdl_Programa.php');
include('../Models/Mdl_Static.php');
include('../Models/Mdl_Excepciones.php');

$debug = 0;
$idUser = $_SESSION['CREident'];

$obj_programa = new Programa($debug, $idUser);

$ErrorLog = 'No se recibió';
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}

switch ($_GET["op"]) {
    case "select2programassets":
        if (!isset($_POST['palabraClave'])) {
            $Data = $obj_programa->getProgramasSetsSelect2("ca.tipo='1'");
            $Data = Excepciones::validaConsulta($Data);
        } else {
            $search = $_POST['palabraClave']; // Palabra a buscar
            $Data = $obj_programa->getProgramasSetsSelect2("ca.tipo='1'",$search);
            $Data = Excepciones::validaConsulta($Data);
        }
        $response = array();

        // Leer la informacion
        foreach ($Data as $area) {
            $response[] = array(
                "id" => $area['id'],
                "text" => $area['nombre']
            );
        }

        //Creamos el JSON
        $json_string = json_encode($response);
        echo $json_string;
        break;
    case "select2programas":
        if (!isset($_POST['palabraClave'])) {
            $Data = $obj_programa->getProgramasSetsSelect2("ca.tipo<>'2'");
            $Data = Excepciones::validaConsulta($Data);
        } else {
            $search = $_POST['palabraClave']; // Palabra a buscar
            $Data = $obj_programa->getProgramasSetsSelect2("ca.tipo<>'2'",$search);
            $Data = Excepciones::validaConsulta($Data);
        }

           //Creamos el JSON
        $json_string = json_encode($Data);
        echo $json_string;
        break;
    case "agregarprograma":
        $programa = (isset($_POST['programa'])) ? trim($_POST['programa']) : '';
        $areaNeta = (isset($_POST['areaNeta'])) ? trim($_POST['areaNeta']) : '';
        $tipo = (isset($_POST['tipo'])) ? trim($_POST['tipo']) : '';
        $log = '';
        if ($programa == '') {
            $ErrorLog .= ' Programa,';
            $log = '1';
        }
        if ($areaNeta == '') {
            $ErrorLog .= ' Área Neta,';
            $log = '1';
        }
        if ($tipo == '') {
            $ErrorLog .= ' Tipo,';
            $log = '1';
        }
        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_programa->errorBD($ErrorLog, 0);
        }
        #Valida que Producto no exista en el Catalogo
        $resultValidacion = Funciones::validarDatoTabla("catprogramas", "nombre", $materiaPrima, $debug, $obj_programa->getConexion());
        try {
            Excepciones::validaMsjError($resultValidacion);
        } catch (Exception $e) {
            $obj_programa->errorBD($e->getMessage(), 1);
        }
        if ($resultValidacion[1] >= 1) {
            $obj_programa->errorBD("Existe un programa con el mismo nombre, verifica tus datos.", 1);
        }
        $datos = $obj_programa->agregarPrograma($programa, $areaNeta, $tipo);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_programa->errorBD($e->getMessage(), 1);
        }
        echo '1|Programa Almacenada Correctamente.';
        break;
    case "agregarprogramaetq":
        $programa = (isset($_POST['programa'])) ? trim($_POST['programa']) : '';
        $areaNeta = 0;
        $tipo = '2';
        $log = '';
        if ($programa == '') {
            $ErrorLog .= ' Programa,';
            $log = '1';
        }
        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_programa->errorBD($ErrorLog, 0);
        }
        #Valida que Producto no exista en el Catalogo
        $resultValidacion = Funciones::validarDatoTabla("catprogramas", "nombre", $materiaPrima, $debug, $obj_programa->getConexion());
        try {
            Excepciones::validaMsjError($resultValidacion);
        } catch (Exception $e) {
            $obj_programa->errorBD($e->getMessage(), 1);
        }
        if ($resultValidacion[1] >= 1) {
            $obj_programa->errorBD("Existe un programa con el mismo nombre, verifica tus datos.", 1);
        }
        $datos = $obj_programa->agregarPrograma($programa, $areaNeta, $tipo);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_programa->errorBD($e->getMessage(), 1);
        }
        echo '1|Programa Almacenada Correctamente.';
        break;
    case "cambiaestatus":
        $id = (isset($_POST['id'])) ? $_POST['id'] : '0';
        $estatus = (isset($_POST['estatus'])) ? $_POST['estatus'] : '';
        $log = '';
        if ($id <= '0') {
            $ErrorLog .= ' Programa,';
            $log = '1';
        }
        if ($estatus == '') {
            $ErrorLog .= ' estatus,';
            $log = '1';
        }
        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_programa->errorBD($ErrorLog, 0);
        }
        $newEstatus = $estatus == '1' ? "0" : "1";

        $datos = Funciones::cambiarEstatus("catprogramas", $newEstatus, "estado", $id, $obj_programa->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_programa->errorBD($e->getMessage(), 1);
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
            $ErrorLog .= 'Programa,';
            $log = '1';
        }
        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_procesos->errorBD($ErrorLog, 0);
        }

        $datos = Funciones::obtenerDetallado('catprogramas', 'id', $id, $obj_programa->getConexion(), $debug);
        /*   try{
                Excepciones::validaMsjError($datos);
            }catch (Exception $e){
                $obj_procesos->errorBD($e->getMessage(), 1);
    
            }*/
        $json_encode = json_encode($datos);
        echo  '1|' . $json_encode;

        break;
    case "editarprograma":
        $id = (isset($_POST['id'])) ? $_POST['id'] : '0';
        $areaNeta = (isset($_POST['areaNeta'])) ? $_POST['areaNeta'] : '';
        $log = '';
        if ($id <= '0') {
            $ErrorLog .= 'Programa,';
            $log = '1';
        }
        if ($areaNeta == '') {
            $ErrorLog .= ' Área Neta,';
            $log = '1';
        }
        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_programa->errorBD($ErrorLog, 0);
        }
        $datos = $obj_programa->editarPrograma($id, $areaNeta);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_programa->errorBD($e->getMessage(), 1);
        }
        echo '1|Proceso Editado Correctamente.';

        break;
}
