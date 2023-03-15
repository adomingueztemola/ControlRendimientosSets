<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../include/connect_mvc.php";
include('../Models/Mdl_ConexionBD.php');
include('../Models/Mdl_ExcepcionDeStock.php');
include('../Models/Mdl_Static.php');
include('../Models/Mdl_Excepciones.php');

$debug = 0;
$idUser = $_SESSION['CREident'];

$obj_excepciones = new ExcepcionDeStock($debug, $idUser);

$ErrorLog = 'No se recibió';
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}

switch ($_GET["op"]) {
    case "enviarexcepcion":
        $pzasRecuperadas = (isset($_POST['pzasRecuperadas'])) ? trim($_POST['pzasRecuperadas']) : '';
        $pzasEmpacadas = (isset($_POST['pzasEmpacadas'])) ? trim($_POST['pzasEmpacadas']) : '';
        $motivoExcepcion = (isset($_POST['motivoExcepcion'])) ? trim($_POST['motivoExcepcion']) : '';
        $idRendimiento = (isset($_POST['idRendimiento'])) ? trim($_POST['idRendimiento']) : '';

        $log = '';
        if ($idRendimiento == '') {
            $ErrorLog .= ' Rendimiento,';
            $log = '1';
        }
        if ($pzasRecuperadas == '') {
            $ErrorLog .= ' Piezas Recuperadas,';
            $log = '1';
        }
        if ($pzasEmpacadas == '') {
            $ErrorLog .= ' Sets Empacados,';
            $log = '1';
        }
        if ($motivoExcepcion == '') {
            $ErrorLog .= ' Motivo de Excepción,';
            $log = '1';
        }
        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_excepciones->errorBD($ErrorLog, 0);
        }

        $obj_excepciones->beginTransaction();
        $datos = $obj_excepciones->agregarExcepcion($idRendimiento, $pzasRecuperadas, $pzasEmpacadas, $motivoExcepcion);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_excepciones->errorBD($e->getMessage(), 1);
        }
        $datos = Funciones::cambiarEstatus("rendimientos", '1', "excepcion", $idRendimiento, $obj_excepciones->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_excepcione->errorBD($e->getMessage(), 1);
        }
        $obj_excepciones->insertarCommit();
        echo '1|Su petición fue enviada correctamente.';
        break;
    case "cancelar":
        $id = (isset($_POST['id'])) ? trim($_POST['id']) : '';
        $log = '';
        if ($id == '') {
            $ErrorLog .= ' Excepción,';
            $log = '1';
        }
        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_excepciones->errorBD($ErrorLog, 0);
        }

        $datos = $obj_excepciones->cancelarExcepcion($id);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_excepciones->errorBD($e->getMessage(), 1);
        }
        echo '1|La excepción seleccionada fue rechazada correctamente.';
        break;
    case "aceptar":
        $idExcepcion = (isset($_POST['idExcepcion'])) ? trim($_POST['idExcepcion']) : '';
        $log = '';
        if ($idExcepcion == '') {
            $ErrorLog .= ' Excepción,';
            $log = '1';
        }
        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_excepciones->errorBD($ErrorLog, 0);
        }
        $obj_excepciones->beginTransaction();

        //Obtener datos de la excepcion
        $DatosExcepcion = $obj_excepciones->getDetExcepcion($idExcepcion);
        #Variables:
        $idRendimiento = $DatosExcepcion[0]['idRendimiento'];
        $pzasRecuperadas = $DatosExcepcion[0]['pzasRecuperadas'];
        $pzasEmpacadas = $DatosExcepcion[0]['pzasEmpacadas'];


        //1. VALIDACION DE INVENTARIO DE RECUPERACION ACTUAL VS TRASPASOS VS EMPACADOS
        $DataValida = $obj_excepciones->getInventarioRecu($idRendimiento);
        $DataValida = $DataValida == '' ? array() : $DataValida;
        if (!is_array($DataValida)) { //Mando Error Query
            echo "0|" . $DataValida;
            exit(0);
        }
        if (count($DataValida) <= 0) {
            echo "0|No se encontró el  inventario, notifica al departamento de Sistemas.";
        }
        $DataTraspasos = $obj_excepciones->getTraspasosPend($idRendimiento);
        $DataTraspasos = $DataTraspasos == '' ? array() : $DataTraspasos;
        if (!is_array($DataTraspasos)) { //Mando Error Query
            echo "0|" . $DataTraspasos;
            exit(0);
        }

        $total_traspasos = 0;
        foreach ($DataTraspasos as $key => $value) {
            $total_traspasos += $DataTraspasos[$key]['cantidad'];
        }
        $total_mover = $total_traspasos + $pzasEmpacadas;
        $total_inventario = $DataValida['0']['pzasTotales'] + $pzasRecuperadas;
        $totalAOcupar = $total_inventario - $total_mover;
        $pzasParaInvRecuperacion = ($DataValida['0']['pzasTotales'] - $total_mover) + $pzasRecuperadas;
        if ($totalAOcupar < 0) {
            echo "0|Cantidad Insuficiente en tu Inventario, Cantidad Actual: {$total_inventario}";
            exit(0);
        }
        //2. QUITAR DEL INVENTARIO DE RECHAZO EL TOTAL DE RECUPERADO AGREGADO
        $datos = $obj_excepciones->disminucionInventRech($idRendimiento, $pzasRecuperadas);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_excepciones->errorBD($e->getMessage(), 1);
        }
        //3. AGREGAR AL LOTE DE INVENTARIO DE EMPACADO EL TRASPASO
        if ($total_traspasos > 0) {

            $datos = $obj_excepciones->actualizarSubLotesTraspEntr($idRendimiento);
            try {
                Excepciones::validaMsjError($datos);
            } catch (Exception $e) {
                $obj_excepciones->errorBD($e->getMessage(), 1);
            }

            $datos = $obj_excepciones->agregarInvEmpTrasp($idRendimiento);
            try {
                Excepciones::validaMsjError($datos);
            } catch (Exception $e) {
                $obj_excepciones->errorBD($e->getMessage(), 1);
            }
        }
        //4. ACTUALIZAR RENDIMIENTO DE RECUPERACION FINAL
        if ($pzasRecuperadas > 0) {
            $datos = $obj_excepciones->actualizarPorcRecuperacion($idRendimiento, $total_inventario, $pzasRecuperadas);
            try {
                Excepciones::validaMsjError($datos);
            } catch (Exception $e) {
                $obj_excepciones->errorBD($e->getMessage(), 1);
            }
        }
        //5. ACTUALIZAR PIEZAS DE INVENTARIO DE RECUPERACION
        $datos = $obj_excepciones->actualizarInventarioRecu($idRendimiento, $pzasParaInvRecuperacion);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_excepciones->errorBD($e->getMessage(), 1);
        }
        //6. AGREGAR PIEZAS A INVENTARIO EMPACADO AQUI
        if ($pzasEmpacadas > 0) {
            $datos = $obj_excepciones->crearSubLote($idRendimiento, $idExcepcion, $pzasEmpacadas);
            try {
                Excepciones::validaMsjError($datos);
            } catch (Exception $e) {
                $obj_excepciones->errorBD($e->getMessage(), 1);
            }

            $datos = $obj_excepciones->aumentarInventarioEmp($idRendimiento, $pzasEmpacadas);
            try {

                Excepciones::validaMsjError($datos);
            } catch (Exception $e) {
                $obj_excepciones->errorBD($e->getMessage(), 1);
            }
        }
        //7. ACEPTAR EXCEPCION DEL LOTE
        $datos = $obj_excepciones->aceptarExcepcion($idExcepcion);
        try {

            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_excepciones->errorBD($e->getMessage(), 1);
        }
        $datos = $obj_excepciones->actualizaCuerosVentas($idRendimiento);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_excepciones->errorBD($e->getMessage(), 1);
        }
        $obj_excepciones->insertarCommit();
        echo '1|Su lote fue creado correctamente.';
        break;
    case "recuperacion24":
        $idRendimiento = (isset($_POST['idRendimiento'])) ? trim($_POST['idRendimiento']) : '';
        $pzasRecuperadas = (isset($_POST['pzasRecuperadas'])) ? trim($_POST['pzasRecuperadas']) : '';
        $pzasEmpacadas = (isset($_POST['pzasEmpacadas'])) ? trim($_POST['pzasEmpacadas']) : '';

        $log = '';
        if ($idRendimiento == '') {
            $ErrorLog .= ' Rendimiento,';
            $log = '1';
        }
        if ($pzasRecuperadas == '') {
            $ErrorLog .= ' piezas Recuperadas,';
            $log = '1';
        }
        if ($pzasEmpacadas == '') {
            $ErrorLog .= ' piezas Empacadas en el Lote,';
            $log = '1';
        }


        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_excepciones->errorBD($ErrorLog, 0);
        }
        $obj_excepciones->beginTransaction();
        //1. VALIDACION DE INVENTARIO DE RECUPERACION ACTUAL VS TRASPASOS VS EMPACADOS
        $DataValida = $obj_excepciones->getInventarioRecu($idRendimiento);
        $DataValida = $DataValida == '' ? array() : $DataValida;
        if (!is_array($DataValida)) { //Mando Error Query
            echo "0|" . $DataValida;
            exit(0);
        }
        if (count($DataValida) <= 0) {
            echo "0|No se encontró el  inventario, notifica al departamento de Sistemas.";
        }

        $DataTraspasos = $obj_excepciones->getTraspasosPend($idRendimiento);
        $DataTraspasos = $DataTraspasos == '' ? array() : $DataTraspasos;
        if (!is_array($DataTraspasos)) { //Mando Error Query
            echo "0|" . $DataTraspasos;
            exit(0);
        }

        $total_traspasos = 0;
        foreach ($DataTraspasos as $key => $value) {
            $total_traspasos += $DataTraspasos[$key]['cantidad'];
        }
        $total_mover = $total_traspasos + $pzasEmpacadas;
        $total_inventario = $DataValida['0']['pzasTotales'] + $pzasRecuperadas;
        $totalAOcupar = $total_inventario - $total_mover;
        $pzasParaInvRecuperacion = ($DataValida['0']['pzasTotales'] - $total_mover) + $pzasRecuperadas;
        if ($totalAOcupar < 0) {
            echo "0|Cantidad Insuficiente en tu Inventario, Cantidad Actual: {$total_inventario}";
            exit(0);
        }
        //2. QUITAR DEL INVENTARIO DE RECHAZO EL TOTAL DE RECUPERADO AGREGADO
        $datos = $obj_excepciones->disminucionInventRech($idRendimiento, $pzasRecuperadas);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_excepciones->errorBD($e->getMessage(), 1);
        }
        //3. AGREGAR AL LOTE DE INVENTARIO DE EMPACADO EL TRASPASO
        if ($total_traspasos > 0) {
            $datos = $obj_excepciones->actualizarSubLotesTraspEntr($idRendimiento);
            try {
                Excepciones::validaMsjError($datos);
            } catch (Exception $e) {
                $obj_excepciones->errorBD($e->getMessage(), 1);
            }

            $datos = $obj_excepciones->agregarInvEmpTrasp($idRendimiento);
            try {
                Excepciones::validaMsjError($datos);
            } catch (Exception $e) {
                $obj_excepciones->errorBD($e->getMessage(), 1);
            }
        }
        //4. ACTUALIZAR RENDIMIENTO DE RECUPERACION FINAL
        if ($pzasRecuperadas > 0) {
            $datos = $obj_excepciones->actualizarPorcRecuperacion($idRendimiento, $total_inventario, $pzasRecuperadas);
            try {
                Excepciones::validaMsjError($datos);
            } catch (Exception $e) {
                $obj_excepciones->errorBD($e->getMessage(), 1);
            }
        }
        //5. ACTUALIZAR PIEZAS DE INVENTARIO DE RECUPERACION
        $datos = $obj_excepciones->actualizarInventarioRecu($idRendimiento, $pzasParaInvRecuperacion);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_excepciones->errorBD($e->getMessage(), 1);
        }
        //6. AGREGAR PIEZAS A INVENTARIO EMPACADO AQUI
        $idSubLote = 0;

        if ($pzasEmpacadas > 0) {
            $datos = $obj_excepciones->crearSubLoteRecuperacion24($idRendimiento, $pzasEmpacadas);
            try {
                Excepciones::validaMsjError($datos);
            } catch (Exception $e) {
                $obj_excepciones->errorBD($e->getMessage(), 1);
            }
            $idSubLote = $datos[2];
            $datos = $obj_excepciones->aumentarInventarioEmp($idRendimiento, $pzasEmpacadas);
            try {
                Excepciones::validaMsjError($datos);
            } catch (Exception $e) {
                $obj_excepciones->errorBD($e->getMessage(), 1);
            }
        }


        $datos = $obj_excepciones->guardarRecuperacion24h($idRendimiento, $idSubLote, $pzasEmpacadas, $pzasRecuperadas);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_excepciones->errorBD($e->getMessage(), 1);
        }

        $datos = $obj_excepciones->actualizaCuerosVentas($idRendimiento);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_excepciones->errorBD($e->getMessage(), 1);
        }

        $obj_excepciones->insertarCommit();
        echo '1|Su Recuperación fue almacenada correctamente.';

        break;
}
