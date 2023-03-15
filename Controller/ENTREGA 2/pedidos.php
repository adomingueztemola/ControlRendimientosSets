<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../include/connect_mvc.php";
include('../Models/Mdl_ConexionBD.php');
include('../Models/Mdl_Pedido.php');
include('../Models/Mdl_Static.php');
include('../Models/Mdl_Excepciones.php');

$debug = 0;
$idUser = $_SESSION['CREident'];

$obj_pedidos = new Pedido($debug, $idUser);

$ErrorLog = 'No se recibió ';
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}

switch ($_GET["op"]) {
    case "guardarproveedor":
        $proveedor = (isset($_POST['proveedor'])) ? trim($_POST['proveedor']) : '';
        $log = '';
        if ($proveedor == '') {
            $ErrorLog .= ' Proveedor,';
            $log = '1';
        }
        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_pedidos->errorBD($ErrorLog, 0);
        }
        $obj_pedidos->beginTransaction();
        #Inicia el registro del Pedido
        $datos = $obj_pedidos->initPedido();
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_pedidos->errorBD($e->getMessage(), 1);
        }
        #Inicia el registro del Proveedor
        $datos = $obj_pedidos->guardarProveedor($proveedor);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_pedidos->errorBD($e->getMessage(), 1);
        }
        $obj_pedidos->insertarCommit();
        echo '1|Proveedor Almacenado Correctamente.';
        break;
    case "guardarnumfactura":
        $numFactura = (isset($_POST['numFactura'])) ? trim($_POST['numFactura']) : '';
        $log = '';
        if ($numFactura == '') {
            $ErrorLog .= ' Número de Factura,';
            $log = '1';
        }
        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_pedidos->errorBD($ErrorLog, 0);
        }
        $datos = $obj_pedidos->guardarNumFactura($numFactura);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_pedidos->errorBD($e->getMessage(), 1);
        }
        echo '1|Número de Factura Almacenado Correctamente.';
        break;
    case "guardarfechafactura":
        $fecha = (isset($_POST['fecha'])) ? trim($_POST['fecha']) : '';
        $log = '';
        if ($fecha == '') {
            $ErrorLog .= ' Fecha de Factura,';
            $log = '1';
        }
        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_pedidos->errorBD($ErrorLog, 0);
        }
        $datos = $obj_pedidos->guardarFechaFact($fecha);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_pedidos->errorBD($e->getMessage(), 1);
        }
        echo '1|Fecha de Factura Almacenada Correctamente.';
        break;

    case "guardartc":
        $tc = (isset($_POST['tc'])) ? trim($_POST['tc']) : '';
        $log = '';
        if ($tc == '') {
            $ErrorLog .= 'Tasa de Cambio de la Factura en Pesos,';
            $log = '1';
        }
        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_pedidos->errorBD($ErrorLog, 0);
        }
        $tc = str_replace(',', '', $tc);
        $datos = $obj_pedidos->guardarTC($tc);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_pedidos->errorBD($e->getMessage(), 1);
        }
        echo '1|Tasa de Cambio Almacenada Correctamente.';

        break;
        /** OBJECT:  AGREGAR MP A LA LISTA DE PEDIDO  Script Date: 22/06/2022 **/

    case 'agregarmppedido':
        $materiaConcepto = (isset($_POST['materiaConcepto'])) ? trim($_POST['materiaConcepto']) : '';
        $precioPeso = (isset($_POST['precioPeso'])) ? trim($_POST['precioPeso']) : '';
        $precioUSD = (isset($_POST['precioUSD'])) ? trim($_POST['precioUSD']) : '';
        $totalCueros = (isset($_POST['totalCueros'])) ? trim($_POST['totalCueros']) : '';
        $areaProv = (isset($_POST['areaProv'])) ? trim($_POST['areaProv']) : '';
        $areaWB = (isset($_POST['areaWB'])) ? trim($_POST['areaWB']) : '';
        #VALIDACION DE DATOS
        Excepciones::validaLlenadoDatos(array(
            " Materia Prima" => $materiaConcepto,
            " Precio MXN" => $precioPeso,
            " Precio USD" => $precioUSD,
            " Total Cueros" => $totalCueros,
            " Área Proveedor" => $areaProv,
            " Área WB Promedio" => $areaWB,

        ), $obj_pedidos);
        $datos = $obj_pedidos->agregarMPPedido($materiaConcepto, $precioPeso, $precioUSD, $totalCueros, $areaProv, $areaWB);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_pedidos->errorBD($e->getMessage(), 1);
        }
        echo '1|Materia Prima Almacenado Correctamente.';

        break;
        /** OBJECT:  MANEJO DE MATERIA PRIMA EN PEDIDOS  Script Date: 22/06/2022 **/
    case 'guardarmp':
        $mp = (isset($_POST['mp'])) ? trim($_POST['mp']) : '';
        $log = '';
        if ($mp == '') {
            $ErrorLog .= 'Materia Prima,';
            $log = '1';
        }
        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_pedidos->errorBD($ErrorLog, 0);
        }
        $datos = $obj_pedidos->guardarMateriaPrima($mp);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_pedidos->errorBD($e->getMessage(), 1);
        }
        echo '1|Materia Prima Almacenada Correctamente.';
        break;
    case "finalizarpedido":
        $datos = $obj_pedidos->finalizarPedido();
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_pedidos->errorBD($e->getMessage(), 1);
        }
        $_SESSION['CRESuccessPedido'] = "Finalización de su Pedido Correctamente.";
        echo '1|Finalización de su Pedido Correctamente.';

        break;
    case "eliminarpedido":
        $datos = $obj_pedidos->eliminarPedido();
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_pedidos->errorBD($e->getMessage(), 1);
        }
        $_SESSION['CRESuccessPedido'] = "Eliminación de Pre-registro del Pedido Correctamente.";

        echo '1|Eliminación de Pre-registro del Pedido Correctamente.';

        break;
    case "consultarnumfact":
        $idProveedor = (isset($_POST['idProveedor'])) ? trim($_POST['idProveedor']) : '';
        $numFactura = (isset($_POST['numFactura'])) ? trim($_POST['numFactura']) : '';
        $log = '';
        if ($idProveedor == '') {
            $ErrorLog .= ' Proveedor,';
            $log = '1';
        }
        if ($numFactura == '') {
            $ErrorLog .= ' Número de Factura,';
            $log = '1';
        }
        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_pedidos->errorBD($ErrorLog, 0);
        }
        $datos = $obj_pedidos->busquedaFactura($idProveedor, $numFactura);
        if (!is_array($datos) and $datos != '') {
            echo '0|' . $datos;
        }
        $datos = $datos == "" ? array() : $datos;
        if (count($datos) > 0) {
            echo '0|Existe una factura registrada con el folio ' . $numFactura . '';
        } else {
            echo '1|Número de Factura correcto.';
        }

        break;
    case "cancelarpedido":
        $idPedido = (isset($_POST['idPedido'])) ? trim($_POST['idPedido']) : '';
        $motivoDeCancelacion = (isset($_POST['motivoDeCancelacion'])) ? trim($_POST['motivoDeCancelacion']) : '';
        $log = '';
        if ($idPedido == '') {
            $ErrorLog .= ' Pedido,';
            $log = '1';
        }
        if ($motivoDeCancelacion == '') {
            $ErrorLog .= ' Motivo de Cancelación,';
            $log = '1';
        }
        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_pedidos->errorBD($ErrorLog, 0);
        }
        $datos = $obj_pedidos->cancelarPedido($idPedido, $motivoDeCancelacion);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_pedidos->errorBD($e->getMessage(), 1);
        }
        echo '1|Cancelación Correcta de Pedido.';

        break;
    case "guardardetpedido":
        $idRendimiento = (!empty($_POST['idRendimiento'])) ? trim($_POST['idRendimiento']) : '';
        $pedido = (!empty($_POST['pedido'])) ? trim($_POST['pedido']) : '';
        $areaProv = (!empty($_POST['areaProv'])) ? trim($_POST['areaProv']) : '';
        $_1s = (isset($_POST['1s'])) ? trim($_POST['1s']) : '';
        $_2s = (isset($_POST['2s'])) ? trim($_POST['2s']) : '';
        $_3s = (isset($_POST['3s'])) ? trim($_POST['3s']) : '';
        $_4s = (isset($_POST['4s'])) ? trim($_POST['4s']) : '';
        $_20 = (isset($_POST['20'])) ? trim($_POST['20']) : '';

        $Total = (isset($_POST['Total'])) ? trim($_POST['Total']) : '';
        $tipoProceso =  (isset($_POST['tipoProceso'])) ? trim($_POST['tipoProceso']) : '';
        $log = '';
        if ($idRendimiento == '') {
            $ErrorLog .= ' Rendimiento,';
            $log = '1';
        }
        if ($pedido == '') {
            $ErrorLog .= ' pedido,';
            $log = '1';
        }
        if ($areaProv == '') {
            $ErrorLog .= ' Área de Proveedor en Lote,';
            $log = '1';
        }
        if ($_1s == '' and $tipoProceso == '2') {
            $ErrorLog .= ' 1s,';
            $log = '1';
        }
        if ($_2s == ''  and $tipoProceso == '2') {
            $ErrorLog .= ' 2s,';
            $log = '1';
        }
        if ($_3s == '' and $tipoProceso == '2') {
            $ErrorLog .= ' 3s,';
            $log = '1';
        }
        if ($_4s == ''  and $tipoProceso == '2') {
            $ErrorLog .= ' 4s,';
            $log = '1';
        }
        if ($_20 == ''  and $tipoProceso == '2') {
            $ErrorLog .= ' 20,';
            $log = '1';
        }
        if ($Total == '') {
            $ErrorLog .= ' Total,';
            $log = '1';
        }

        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_pedidos->errorBD($ErrorLog, 0);
        }
        $areaProv = str_replace(",", "", $areaProv);

        $datos = $obj_pedidos->registraDetPedido($idRendimiento, $pedido, $areaProv, $_1s, $_2s, $_3s, $_4s, $_20, $Total);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_pedidos->errorBD($e->getMessage(), 1);
        }
        echo '1|Pedido Seleccionado Correctamente.';

        break;
    case "eliminardetpedido":
        $id = (!empty($_POST['id'])) ? trim($_POST['id']) : '';
        $log = '';
        if ($id == '') {
            $ErrorLog .= ' Detallado de Rendimiento,';
            $log = '1';
        }
        $datos = $obj_pedidos->eliminarDetPedido($id);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_pedidos->errorBD($e->getMessage(), 1);
        }
        echo '1|Pedido Eliminado Correctamente.';

        break;
    case 'cancelardetpedido':
        $id = (!empty($_POST['id'])) ? trim($_POST['id']) : '';
        $log = '';
        if ($id == '') {
            $ErrorLog .= ' Detallado de Rendimiento,';
            $log = '1';
        }

        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_pedidos->errorBD($ErrorLog, 0);
        }
        $datos = $obj_pedidos->eliminarPedidoDelLote($id);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_pedidos->errorBD($e->getMessage(), 1);
        }
        echo '1|Seguimiento Eliminado Correctamente.';

        break;
    case "ajustepedido":
        $idPedido = (!empty($_POST['idPedido'])) ? trim($_POST['idPedido']) : '';
        $tipo = (!empty($_POST['tipo'])) ? trim($_POST['tipo']) : '';
        $cantidad = (!empty($_POST['cantidad'])) ? trim($_POST['cantidad']) : '0';
        $notaCredito = (!empty($_POST['notaCredito'])) ? trim($_POST['notaCredito']) : '0';
        $motivo = (!empty($_POST['motivo'])) ? trim($_POST['motivo']) : '';
        #VALIDACION DE DATOS
        Excepciones::validaLlenadoDatos(array(
            " Materia Prima" => $idPedido,
            " Tipo de Ajuste" => $tipo,
            " Cantidad" => $cantidad,
            " Nota de Crédito" => $notaCredito,
            " Motivo" => $motivo,
        ), $obj_pedidos);

        $obj_pedidos->beginTransaction();
        /******** Ingreso de Motivo de Excepcion********/
        $datos = $obj_pedidos->agregarMotivoDeExcepcion($idPedido, $tipo, $cantidad, $motivo, $notaCredito);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_pedidos->errorBD($e->getMessage(), 1);
        }
        $idEdicion = $datos[2];
        $lblOperacion = "";
        if ($tipo == '1') {
            $lblOperacion = "Aumento";
            /********** Creación de Pedido Extra ************/
            $datos = $obj_pedidos->aumentoPedido($idPedido, $idEdicion);
            try {
                Excepciones::validaMsjError($datos);
            } catch (Exception $e) {
                $obj_pedidos->errorBD($e->getMessage(), 1);
            }
        } else  if ($tipo == '2') {
            $lblOperacion = "Disminución";
            $datos = $obj_pedidos->disminucionPedido($idPedido, $idEdicion);
            try {
                Excepciones::validaMsjError($datos);
            } catch (Exception $e) {
                $obj_pedidos->errorBD($e->getMessage(), 1);
            }
        }
        $obj_pedidos->insertarCommit();
        echo "1|" . $lblOperacion . " Correcto.";
        break;
    case "clasificacionrecepcion":
        $id = (isset($_POST['id'])) ? trim($_POST['id']) : '';
        $_1s = (isset($_POST['_1s'])) ? trim($_POST['_1s']) : '';
        $_2s = (isset($_POST['_2s'])) ? trim($_POST['_2s']) : '';
        $_3s = (isset($_POST['_3s'])) ? trim($_POST['_3s']) : '';
        $_4s = (isset($_POST['_4s'])) ? trim($_POST['_4s']) : '';
        $_20 = (isset($_POST['_20'])) ? trim($_POST['_20']) : '';

        #VALIDACION DE DATOS
        Excepciones::validaLlenadoDatos(array(
            " Materia Prima" => $id,
            " 1s" => $_1s,
            " 2s" => $_2s,
            " 3s" => $_3s,
            " 4s" => $_4s,
            " 20" => $_20,

        ), $obj_pedidos);
        #VALIDAR NUMEROS POSITIVOS
        if($_1s<0){
            $obj_pedidos->errorBD("Verifica las clasificaciones deben de ser mayor o igual a 0.", 1);
        }
        if($_2s<0){
            $obj_pedidos->errorBD("Verifica las clasificaciones deben de ser mayor o igual a 0.", 1);
        }
        if($_3s<0){
            $obj_pedidos->errorBD("Verifica las clasificaciones deben de ser mayor o igual a 0.", 1);

        }
        if($_4s<0){
            $obj_pedidos->errorBD("Verifica las clasificaciones deben de ser mayor o igual a 0.", 1);

        }
        if($_20<0){
            $obj_pedidos->errorBD("Verifica las clasificaciones deben de ser mayor o igual a 0.", 1);

        }
        
        $datos = $obj_pedidos->clasificarMP($id, $_1s, $_2s, $_3s, $_4s, $_20);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_pedidos->errorBD($e->getMessage(), 1);
        }
        echo "1|Clasificación de Materia Prima Correcto.";

        break;
    case "eliminarmp":
        $id = (isset($_POST['id'])) ? trim($_POST['id']) : '';

        #VALIDACION DE DATOS
        Excepciones::validaLlenadoDatos(array(
            " Materia Prima" => $id

        ), $obj_pedidos);
        $datos = $obj_pedidos->eliminarMP($id);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_pedidos->errorBD($e->getMessage(), 1);
        }
        echo "1|Eliminación de Materia Prima Correcto.";

        break;
}
