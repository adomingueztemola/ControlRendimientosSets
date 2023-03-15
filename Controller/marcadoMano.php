<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../include/connect_mvc.php";
include('../Models/Mdl_ConexionBD.php');
include('../Models/Mdl_MarcadoAMano.php');
include('../Models/Mdl_Static.php');
include('../Models/Mdl_Excepciones.php');

$debug = 0;
$idUser = $_SESSION['CREident'];
$obj_marcado = new MarcadoAMano($debug, $idUser);

$ErrorLog = 'No se recibió';
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}

switch ($_GET["op"]) {
    case "agregarlote":
        $nLote = (isset($_POST['nLote'])) ? trim($_POST['nLote']) : '';
        $programa = (isset($_POST['programa'])) ? trim($_POST['programa']) : '';
        $fecha = (isset($_POST['fecha'])) ? trim($_POST['fecha']) : '';
        // $areaCrust = (isset($_POST['areaCrust'])) ? trim($_POST['areaCrust']) : '';

        $log = '';
        if ($nLote == '') {
            $ErrorLog .= ' Lote,';
            $log = '1';
        }
        if ($programa == '') {
            $ErrorLog .= ' Programa,';
            $log = '1';
        }
        if ($fecha == '') {
            $ErrorLog .= ' Fecha,';
            $log = '1';
        }
        /* if ($areaCrust == '') {
            $ErrorLog .= ' Área Crust,';
            $log = '1';
        }*/
        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_marcado->errorBD($ErrorLog, 0);
        }
        #Valida Lote en el Marcado Manual
        $resultValidacion = Funciones::validarDatoTabla("lotesteseo", "nombre", $nLote, $debug, $obj_marcado->getConexion());
        try {
            Excepciones::validaMsjError($resultValidacion);
        } catch (Exception $e) {
            $obj_marcado->errorBD($e->getMessage(), 1);
        }
        if ($resultValidacion[1] >= 1) {
            $obj_marcado->errorBD("Existe un lote en Marcado con el mismo nombre, verifica tus datos.", 1);
        }
        $obj_marcado->beginTransaction();
        $datos = $obj_marcado->agregarLote($nLote, $programa, $fecha, '0');
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_marcado->errorBD($e->getMessage(), 1);
        }
        $idLote = $datos[2];
        $datos = $obj_marcado->crearContadores($idLote);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_marcado->errorBD($e->getMessage(), 1);
        }
        $obj_marcado->insertarCommit();
        echo '1|Lote Almacenado Correctamente.';
        break;
    case "cantfinal":
        $lote = (isset($_POST['lote'])) ? trim($_POST['lote']) : '';
        $value = (!empty($_POST['value'])) ? trim($_POST['value']) : '0';
        Excepciones::validaLlenadoDatos(array(" Lote" => $lote, " conteo" => $value), $obj_marcado);
        $datos = $obj_marcado->editarContadorLote($lote, "total", $value);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_marcado->errorBD($e->getMessage(), 1);
        }
        echo '1|Conteo Almacenado Correctamente.';
        break;
    case "corte":
        $lote = (isset($_POST['lote'])) ? trim($_POST['lote']) : '';
        $idLote = (isset($_POST['idLote'])) ? trim($_POST['idLote']) : '';

        $value = (!empty($_POST['value'])) ? trim($_POST['value']) : '0';
        Excepciones::validaLlenadoDatos(array(" Marcado" => $lote, " Lote" => $idLote, " conteo" => $value), $obj_marcado);
        $obj_marcado->beginTransaction();
        $datos = $obj_marcado->agregarPiezasMarcadas($lote, $value);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_marcado->errorBD($e->getMessage(), 1);
        }

        $datos = $obj_marcado->agregarKardexCorte($lote, $value);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_marcado->errorBD($e->getMessage(), 1);
        }

        $datos = $obj_marcado->actualizarAreaYieldLote($idLote);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_marcado->errorBD($e->getMessage(), 1);
        }
        $obj_marcado->insertarCommit();
        echo '1|Se agregó Piezas al Marcado Correctamente.';
        break;
    case "decremento":
        $lote = (isset($_POST['lote'])) ? trim($_POST['lote']) : '';
        $idLote = (isset($_POST['idLote'])) ? trim($_POST['idLote']) : '';
        $value = (!empty($_POST['value'])) ? trim($_POST['value']) : '0';

        Excepciones::validaLlenadoDatos(array(" Marcardo" => $lote, " Lote" => $idLote, " conteo" => $value), $obj_marcado);
        $obj_marcado->beginTransaction();
        $datos = $obj_marcado->quitarPiezasMarcadas($lote, $value);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_marcado->errorBD($e->getMessage(), 1);
        }

        $datos = $obj_marcado->agregarDecrKardexCorte($lote, $value);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_marcado->errorBD($e->getMessage(), 1);
        }

        $datos = $obj_marcado->actualizarAreaYieldLote($idLote);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_marcado->errorBD($e->getMessage(), 1);
        }
        $obj_marcado->insertarCommit();
        echo '1|Se quitó Piezas al Marcado Correctamente.';
        break;
    case "cerrarmarcado":
        $idLote = (isset($_POST['idLote'])) ? trim($_POST['idLote']) : '';
        Excepciones::validaLlenadoDatos(array(" Lote" => $idLote), $obj_marcado);
        $datos = $obj_marcado->cerrarMarcado($idLote);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_marcado->errorBD($e->getMessage(), 1);
        }
        echo '1|Cierre Marcado Correctamente.';
        break;
    case "agregarcrust":
        $idLote = (isset($_POST['idLote'])) ? trim($_POST['idLote']) : '';
      
        $areaCrust = (isset($_POST['areaCrust'])) ? trim($_POST['areaCrust']) : '';
        Excepciones::validaLlenadoDatos(array(" Lote" => $idLote, 
                                              " Área Crust" => $areaCrust), $obj_marcado);
        $obj_marcado->beginTransaction();
      

        $datos = $obj_marcado->agregarCrust($idLote, $areaCrust);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_marcado->errorBD($e->getMessage(), 1);
        }

        $obj_marcado->insertarCommit();
        echo '1|Área Crust del  lote agregada correctamente.';

        break;
        case "editardatos":
            $idLote = (isset($_POST['idLote'])) ? trim($_POST['idLote']) : '';
            $nLote = (isset($_POST['nLote'])) ? trim($_POST['nLote']) : '';
            $programa = (isset($_POST['programa'])) ? trim($_POST['programa']) : '';
            $fecha = (isset($_POST['fecha'])) ? trim($_POST['fecha']) : '';
            $areaCrust = (isset($_POST['areaCrust'])) ? trim($_POST['areaCrust']) : '';
            $porcDecrement =(isset($_POST['porcDecrement'])) ? trim($_POST['porcDecrement']) : '';
            Excepciones::validaLlenadoDatos(array(" Lote" => $idLote, " Nombre de Lote" => $nLote, 
                                                  " Programa" => $programa, " Fecha" => $fecha, 
                                                  " Área Crust" => $areaCrust, " Decremento" => $porcDecrement), $obj_marcado);
            $obj_marcado->beginTransaction();
            $datos = $obj_marcado->editaLote($idLote, $nLote, $programa, $fecha, $porcDecrement);
            try {
                Excepciones::validaMsjError($datos);
            } catch (Exception $e) {
                $obj_marcado->errorBD($e->getMessage(), 1);
            }
    
            $datos = $obj_marcado->agregarCrust($idLote, $areaCrust);
            try {
                Excepciones::validaMsjError($datos);
            } catch (Exception $e) {
                $obj_marcado->errorBD($e->getMessage(), 1);
            }
    
            $obj_marcado->insertarCommit();
            echo '1|Se edito datos del lote correctamente.';
    
            break;
    case "ajuste":
        $lote = (isset($_POST['lote'])) ? trim($_POST['lote']) : '';
        $idLote = (isset($_POST['idLote'])) ? trim($_POST['idLote']) : '';

        $value = (!empty($_POST['value'])) ? trim($_POST['value']) : '0';
        Excepciones::validaLlenadoDatos(array(" Marcado" => $lote, " Lote" => $idLote, " conteo" => $value), $obj_marcado);
        $obj_marcado->beginTransaction();
        $datos = $obj_marcado->agregarPiezasRecuperacion($lote, $value);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_marcado->errorBD($e->getMessage(), 1);
        }

        $datos = $obj_marcado->agregarKardexRecuperacion($lote, $value);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_marcado->errorBD($e->getMessage(), 1);
        }
        $obj_marcado->insertarCommit();
        echo '1|Se agregó Piezas de Recuperación Correctamente.';

        break;
    case "actualizarporcentaje":
        $idLote = (isset($_POST['idLote'])) ? trim($_POST['idLote']) : '';
      
        $porcDecrement = (isset($_POST['porcDecrement'])) ? trim($_POST['porcDecrement']) : '';
        Excepciones::validaLlenadoDatos(array(" Lote" => $idLote, 
                                              " Porcentaje de Decremento" => $porcDecrement), $obj_marcado);
        $obj_marcado->beginTransaction();
      

        $datos = $obj_marcado->actualizarPorcentaje($idLote, $porcDecrement);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_marcado->errorBD($e->getMessage(), 1);
        }

        $obj_marcado->insertarCommit();
        echo '1|Área Crust del  lote agregada correctamente.';
        break;
}
