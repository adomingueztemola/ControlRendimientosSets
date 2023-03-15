<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../include/connect_mvc.php";
include('../Models/Mdl_ConexionBD.php');
include('../Models/Mdl_VentaXDevoluc.php');

include('../Models/Mdl_Static.php');
include('../Models/Mdl_Excepciones.php');
$pzasEnSet = 4;
$debug = 0;
$idUser = $_SESSION['CREident'];

$obj_venta = new VentaXDevoluc($debug, $idUser);

$ErrorLog = 'No se recibió ';
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}

switch ($_GET["op"]) {
    case "initventa":
        $fechaFacturacion = (isset($_POST['fechaFacturacion'])) ? trim($_POST['fechaFacturacion']) : '';
        $numFactura = (isset($_POST['numFactura'])) ? trim($_POST['numFactura']) : '';
        $numPL = (isset($_POST['numPL'])) ? trim($_POST['numPL']) : '';
        $idTipoVenta =  (isset($_POST['idTipoVenta'])) ? trim($_POST['idTipoVenta']) : '';
        $requiereFact =  (isset($_POST['requiereFact'])) ? trim($_POST['requiereFact']) : '';

        $log = '';
        if ($fechaFacturacion == '') {
            $ErrorLog .= ' fecha de Facturación,';
            $log = '1';
        }
        if ($numFactura == '' and $requiereFact == '1') {
            $ErrorLog .= ' Número de Factura,';
            $log = '1';
        }
        if ($numPL == '') {
            $ErrorLog .= ' Número de PL,';
            $log = '1';
        }
        if ($idTipoVenta == '') {
            $ErrorLog .= ' Tipo de Venta,';
            $log = '1';
        }
        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_venta->errorBD($ErrorLog, 0);
        }
        $datos = $obj_venta->initVenta($fechaFacturacion, $numFactura, $numPL, $idTipoVenta);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_venta->errorBD($e->getMessage(), 1);
        }
        echo '1|Venta Iniciada Correctamente.';

        break;
    case "agregarunidades":
        $idDetDevolucion = (isset($_POST['idDetDevolucion'])) ? trim($_POST['idDetDevolucion']) : '';
        $idPrograma = (isset($_POST['idPrograma'])) ? trim($_POST['idPrograma']) : '';
        $cantidad = (isset($_POST['cantidad'])) ? trim($_POST['cantidad']) : '';
        $lote = (isset($_POST['lote'])) ? trim($_POST['lote']) : '';
        $idTipoPrograma = (isset($_POST['idTipoPrograma'])) ? trim($_POST['idTipoPrograma']) : '';
        Excepciones::validaLlenadoDatos(array(
            " Devolución" => $idDetDevolucion, " Programa" => $idPrograma, " cantidad" => $cantidad, " lote" => $lote,
            " tipo de programa" => $idTipoPrograma
        ), $obj_venta);
        $datos = $obj_venta->addDetVenta($idDetDevolucion, $idPrograma, $cantidad, $lote, $idTipoPrograma);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_venta->errorBD($e->getMessage(), 1);
        }
        echo '1|Se agregó Lote a la Venta Correctamente.';

        break;
    case "eliminardetventa":
        $id = (isset($_POST['id'])) ? trim($_POST['id']) : '';
        Excepciones::validaLlenadoDatos(array(" Detalle" => $id), $obj_venta);

        $datos = $obj_venta->eliminarDetVenta($id);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_venta->errorBD($e->getMessage(), 1);
        }
        echo '1|Detalle de Venta Eliminada Correctamente.';

        break;

    case "eliminarventa":
        $datos = $obj_venta->eliminarVenta();
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_venta->errorBD($e->getMessage(), 1);
        }
        $_SESSION['CRESuccessVenta'] = 'Venta Eliminada Correctamente.';
        echo '1|Venta Eliminada Correctamente.';

        break;
    case "finalizarventa":



        $datos = $obj_venta->finalizarVenta();
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_venta->errorBD($e->getMessage(), 1);
        }
        $_SESSION['CRESuccessVenta'] = 'Venta Finalizada Correctamente.';

        echo '1|Venta Finalizada Correctamente.';

        break;
        /* case "guardaralmacen":
        $id = (!empty($_POST['id'])) ? trim($_POST['id']) : '';
        $cant = (isset($_POST['cant'])) ? trim($_POST['cant']) : '';
        $log = '';
        if ($id == '') {
            $ErrorLog .= ' Lote,';
            $log = '1';
        }
        if ($cant == '') {
            $ErrorLog .= ' Cantidad,';
            $log = '1';
        }
        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_venta->errorBD($ErrorLog, 0);
        }
        $datos = $obj_venta->paseAlmacenPT($id, $cant);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_venta->errorBD($e->getMessage(), 1);
        }
        echo '1| Registro de Almacén PT Correcto.';

        break;
    case "editarventa":
        $fechaFacturacion = (isset($_POST['fechaFacturacion'])) ? trim($_POST['fechaFacturacion']) : '';
        $motivo = (isset($_POST['motivo'])) ? trim($_POST['motivo']) : '';
        $numFactura = (isset($_POST['numFactura'])) ? trim($_POST['numFactura']) : '';
        $numPL = (isset($_POST['numPL'])) ? trim($_POST['numPL']) : '';
        $idTipoVenta =  (isset($_POST['idTipoVenta'])) ? trim($_POST['idTipoVenta']) : '';
        $requiereFact =  (isset($_POST['requiereFact'])) ? trim($_POST['requiereFact']) : '';
        $id =  (isset($_POST['id'])) ? trim($_POST['id']) : '';
        $log = '';
        if ($fechaFacturacion == '') {
            $ErrorLog .= ' fecha de Facturación,';
            $log = '1';
        }
        if ($motivo == '') {
            $ErrorLog .= ' Motivo,';
            $log = '1';
        }
        if ($numFactura == '' and $requiereFact == '1') {
            $ErrorLog .= ' Número de Factura,';
            $log = '1';
        }
        if ($id == '') {
            $ErrorLog .= ' Venta,';
            $log = '1';
        }
        if ($numPL == '') {
            $ErrorLog .= ' Número de PL,';
            $log = '1';
        }
        if ($idTipoVenta == '') {
            $ErrorLog .= ' Tipo de Venta,';
            $log = '1';
        }
        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_venta->errorBD($ErrorLog, 0);
        }
        $obj_venta->beginTransaction();
        $datos = $obj_venta->controlVentaHistorico($id, $motivo);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_venta->errorBD($e->getMessage(), 1);
        }

        $datos = $obj_venta->editarVenta($id, $fechaFacturacion, $numFactura, $numPL, $idTipoVenta);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_venta->errorBD($e->getMessage(), 1);
        }
        $obj_venta->insertarCommit();

        echo '1|Edición de Datos de la Venta Correcto.';

        break;
    case "guardarclasif":
        $codigo = (isset($_POST['codigo'])) ? trim($_POST['codigo']) : '';
        $cant = (isset($_POST['cant'])) ? trim($_POST['cant']) : '';
        $id =  (isset($_POST['id'])) ? trim($_POST['id']) : '';
        $log = '';
        if ($codigo == '') {
            $ErrorLog .= ' clasificación,';
            $log = '1';
        }
        if ($cant == '') {
            $ErrorLog .= ' Cantidad,';
            $log = '1';
        }
        if ($id == '') {
            $ErrorLog .= ' Venta,';
            $log = '1';
        }

        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_venta->errorBD($ErrorLog, 0);
        }
        $obj_venta->beginTransaction();
        $datos = Funciones::edicionBasica("detventas", $codigo, $cant, 'id', $id, $obj_venta->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_venta->errorBD($e->getMessage(), 1);
        }
        if ($codigo == "total_s" and $cant > 0) {
            $datos = Funciones::edicionBasica("detventas", "distribuido", "1", 'id', $id, $obj_venta->getConexion(), $debug);
            try {
                Excepciones::validaMsjError($datos);
            } catch (Exception $e) {
                $obj_venta->errorBD($e->getMessage(), 1);
            }
        } else if ($codigo == "total_s" and $cant <= 0) {
            $datos = Funciones::edicionBasica("detventas", "distribuido", "0", 'id', $id, $obj_venta->getConexion(), $debug);
            try {
                Excepciones::validaMsjError($datos);
            } catch (Exception $e) {
                $obj_venta->errorBD($e->getMessage(), 1);
            }
        }

        $obj_venta->insertarCommit();
        echo '1|Clasificación asignada a la venta.';

        break;
    case "consultarventa":
        $numFactura = (isset($_POST['numFactura'])) ? trim($_POST['numFactura']) : '';
        $idVenta = (!empty($_POST['idVenta'])) ? trim($_POST['idVenta']) : '';

        $log = '';
        if ($numFactura == '') {
            $ErrorLog .= ' num. Factura,';
            $log = '1';
        }

        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_venta->errorBD($ErrorLog, 0);
        }
        $filtradoVenta = $idVenta != '' ? "v.id!='$idVenta'" : "1=1";
        $datos = $obj_venta->busquedaVenta($numFactura, $filtradoVenta);
        if (!is_array($datos) and $datos != '') {
            echo '0|' . $datos;
        }
        $datos = $datos == "" ? array() : $datos;
        if (count($datos) > 0) {
            echo '0|Existe una Factura con el folio ' . $numFactura . '';
        } else {
            echo '1|Número de Factura correcto.';
        }
        break;
    case "consultarpl":
        $numPL = (isset($_POST['numPL'])) ? trim($_POST['numPL']) : '';
        $idVenta = (!empty($_POST['idVenta'])) ? trim($_POST['idVenta']) : '';

        $log = '';
        if ($numPL == '') {
            $ErrorLog .= ' num. PL,';
            $log = '1';
        }

        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_venta->errorBD($ErrorLog, 0);
        }
        $filtradoVenta = $idVenta != '' ? "v.id!='$idVenta'" : "1=1";

        $datos = $obj_venta->busquedaPL($numPL, $filtradoVenta);
        if (!is_array($datos) and $datos != '') {
            echo '0|' . $datos;
        }
        $datos = $datos == "" ? array() : $datos;
        if (count($datos) > 0) {
            echo '0|Existe un Número PL registrado con el folio ' . $numPL . '';
        } else {
            echo '1|Número de PL correcto.';
        }
        break;
    case "agregarsublotes":
        $idRendimiento = (isset($_POST['idRendimiento'])) ? trim($_POST['idRendimiento']) : '';
        $idDetVenta = (isset($_POST['idDetVenta'])) ? trim($_POST['idDetVenta']) : '';
        $DataSubLotes = $obj_venta->getSubLotes($idRendimiento, $idDetVenta);
        $ArrayLotesXSeleccion = array();
        foreach ($DataSubLotes as $key => $value) {
            $strinVar = "sublote_";
            $strinVar = $strinVar . $DataSubLotes[$key]['id'];
            $$strinVar = (isset($_POST[$strinVar])) ? trim($_POST[$strinVar]) : '0';
            array_push($ArrayLotesXSeleccion, [$DataSubLotes[$key]['id'], $$strinVar]);
        }
        $query = "";

        if (count($ArrayLotesXSeleccion) > 0) {
            foreach ($ArrayLotesXSeleccion as $key => $value) {
                if ($ArrayLotesXSeleccion[$key][1] > 0) {
                    $totalSets = $ArrayLotesXSeleccion[$key][1] / $pzasEnSet;
                    $query .= "('{$ArrayLotesXSeleccion[$key][0]}', '{$ArrayLotesXSeleccion[$key][1]}','{$totalSets}', 
                                NOW(), '$idUser', '$idDetVenta'),";
                }
            }
        }
        //Eliminar sublotes 
        $datos = Funciones::eliminarRegistro("detventaslotes", $idDetVenta, "idDetVenta", $obj_venta->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_venta->errorBD($e->getMessage(), 1);
        }
        //Agregar nuevos sublotes
        if ($query != '') {
            $query = substr($query, 0, -1);
            $datos = $obj_venta->addSubLote($query);
            try {
                Excepciones::validaMsjError($datos);
            } catch (Exception $e) {
                $obj_venta->errorBD($e->getMessage(), 1);
            }
        }
        //Agregar cantidad al detalle de la venta
        $datos = $obj_venta->agregarTotalDetVenta($idDetVenta);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_venta->errorBD($e->getMessage(), 1);
        }
        echo "1|Se efectuaron los cambios de los lotes.";

        break;*/
}
