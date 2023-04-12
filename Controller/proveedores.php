<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../include/connect_mvc.php";
include('../Models/Mdl_ConexionBD.php');
include('../Models/Mdl_Proveedor.php');
include('../Models/Mdl_Static.php');
include('../Models/Mdl_Excepciones.php');

$debug =10;
$idUser = $_SESSION['CREident'];

$obj_proveedor= new Proveedor($debug, $idUser);

$ErrorLog = 'No se recibió';
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}

switch ($_GET["op"]) {
    case "select2proveedores":
        if (!isset($_POST['palabraClave'])) {
            $Data = $obj_proveedor->getProveedoresSelect2();
            $Data = Excepciones::validaConsulta($Data);
        } else {
            $search = $_POST['palabraClave']; // Palabra a buscar
            $Data = $obj_proveedor->getProveedoresSelect2($search);
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
    case "agregarprov":
       $proveedor = (isset($_POST['proveedor'])) ? trim($_POST['proveedor']) : '';
        $log = '';
        if ($proveedor == '') {
            $ErrorLog .= 'Proveedor,';
            $log = '1';
        }       
        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_proveedor->errorBD($ErrorLog, 0);
        }
        #Valida que Producto no exista en el Catalogo
        $resultValidacion=Funciones::validarDatoTabla("catproveedores", "nombre", $materiaPrima, $debug,$obj_proveedor->getConexion());
        try{
            Excepciones::validaMsjError($resultValidacion);
        }catch (Exception $e){
            $obj_proveedor->errorBD($e->getMessage(), 1);

        }
        if($resultValidacion[1]>=1){
            $obj_proveedor->errorBD("Existe un proveedor con el mismo nombre, verifica tus datos.",1);
        }
        $datos = $obj_proveedor->agregarProveedor($proveedor);
        try{
            Excepciones::validaMsjError($datos);
        }catch (Exception $e){
            $obj_proveedor->errorBD($e->getMessage(), 1);

        }
        echo '1|Proveedor Almacenado Correctamente.';
    break;
    case "cambiaestatus":
        $id = (isset($_POST['id'])) ? $_POST['id'] : '0';
        $estatus = (isset($_POST['estatus'])) ? $_POST['estatus'] : '';
        $log = '';
        if ($id <= '0') {
            $ErrorLog .= ' Proveedores,';
            $log = '1';
        }
        if ($estatus == '') {
            $ErrorLog .= ' estatus,';
            $log = '1';
        }
        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_proveedor->errorBD($ErrorLog, 0);
        }
        $newEstatus=$estatus=='1'?"0":"1";

        $datos=Funciones::cambiarEstatus("catproveedores", $newEstatus, "estado",$id,  $obj_proveedor->getConexion(), $debug);
        try{
            Excepciones::validaMsjError($datos);
        }catch (Exception $e){
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
        echo "1|Estatus Cambiado Correctamente.|".$iconEstatus;
        break;
  
}
