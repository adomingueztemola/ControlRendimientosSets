<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../include/connect_mvc.php";
include('../Models/Mdl_Static.php');

$debug =10;
$idUser = $_SESSION['CREident'];

$obj_proceso= new ProcesoProduccion($debug, $idUser);

$ErrorLog = 'No se recibió';
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}

switch ($_GET["op"]) {
    case "agregarproc":
       $proceso = (isset($_POST['proceso'])) ? trim($_POST['proceso']) : '';
        $log = '';
        if ($proceso == '') {
            $ErrorLog .= 'Proceso,';
            $log = '1';
        }       
        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_proceso->errorBD($ErrorLog, 0);
        }
        #Valida que Producto no exista en el Catalogo
        $resultValidacion=Funciones::validarDatoTabla("catprocesosproduccion", "nombre", $proceso, $debug,$obj_proceso->getConexion());
        try{
            Excepciones::validaMsjError($resultValidacion);
        }catch (Exception $e){
            $obj_proceso->errorBD($e->getMessage(), 1);

        }
        if($resultValidacion[1]>=1){
            $obj_proceso->errorBD("Existe un proceso con el mismo nombre, verifica tus datos.",1);
        }
        $datos = $obj_proceso->agregarProceso($proceso);
        try{
            Excepciones::validaMsjError($datos);
        }catch (Exception $e){
            $obj_proceso->errorBD($e->getMessage(), 1);

        }
        echo '1|Proceso Almacenado Correctamente.';
    break;
    case "cambiaestatus":
        $id = (isset($_POST['id'])) ? $_POST['id'] : '0';
        $estatus = (isset($_POST['estatus'])) ? $_POST['estatus'] : '';
        $log = '';
        if ($id <= '0') {
            $ErrorLog .= ' Procesos,';
            $log = '1';
        }
        if ($estatus == '') {
            $ErrorLog .= ' estatus,';
            $log = '1';
        }
        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_proceso->errorBD($ErrorLog, 0);
        }
        $newEstatus=$estatus=='1'?"0":"1";

        $datos=Funciones::cambiarEstatus("catprocesosproduccion", $newEstatus, "estado",$id,  $obj_proceso->getConexion(), $debug);
        try{
            Excepciones::validaMsjError($datos);
        }catch (Exception $e){
            $obj_proceso->errorBD($e->getMessage(), 1);

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
