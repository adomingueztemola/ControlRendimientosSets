<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../include/connect_mvc.php";
include('../Models/Mdl_ConexionBD.php');
include('../Models/Mdl_Rendimiento.php');
include('../Models/Mdl_Static.php');
include('../Models/Mdl_Excepciones.php');

$debug = 0;
$idUser = $_SESSION['CREident'];

$obj_rendimiento = new Rendimiento($debug, $idUser);

$ErrorLog = 'No se recibió';
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}

switch ($_GET["op"]) {
    case "initRendimiento":
        $fechaEngrase = (!empty($_POST['fechaEngrase'])) ? trim($_POST['fechaEngrase']) : '';
        $semanaProduccion = (!empty($_POST['semanaProduccion'])) ? trim($_POST['semanaProduccion']) : '';
        $fechaEmpaque = (!empty($_POST['fechaEmpaque'])) ? trim($_POST['fechaEmpaque']) : '';
        $proceso = (!empty($_POST['proceso'])) ? trim($_POST['proceso']) : '';
        $lote = (!empty($_POST['lote'])) ? trim($_POST['lote']) : '';
        $programa = (!empty($_POST['programa'])) ? trim($_POST['programa']) : '';
        $materiaPrima = (!empty($_POST['materiaPrima'])) ? trim($_POST['materiaPrima']) : '';

        $log = '';
        if ($fechaEngrase == '') {
            $ErrorLog .= ' fecha de Engrase,';
            $log = '1';
        }
        if ($semanaProduccion == '') {
            $ErrorLog .= ' semana de Producción,';
            $log = '1';
        }
        if ($fechaEmpaque == '') {
            $ErrorLog .= ' fecha de Empaque,';
            $log = '1';
        }
        if ($proceso == '') {
            $ErrorLog .= ' proceso,';
            $log = '1';
        }
        if ($lote == '') {
            $ErrorLog .= ' lote,';
            $log = '1';
        }
        if ($programa == '') {
            $ErrorLog .= ' programa,';
            $log = '1';
        }
        if ($materiaPrima == '') {
            $ErrorLog .= ' materia Prima,';
            $log = '1';
        }
        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_rendimiento->errorBD($ErrorLog, 0);
        }
        #Array de Semana de Produccion
        $WeekYear = explode("-W", $semanaProduccion);

        $datos = $obj_rendimiento->initRendimiento(
            $fechaEngrase,
            $WeekYear[1],
            $fechaEmpaque,
            $proceso,
            $lote,
            $programa,
            $materiaPrima
        );
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_rendimiento->errorBD($e->getMessage(), 1);
        }
        echo '1|Inicio de Rendimiento Almacenado Correctamente.';
        break;
    case "consultarlote":
        $lote = (isset($_POST['lote'])) ? trim($_POST['lote']) : '';
        $log = '';
        if ($lote == '') {
            $ErrorLog .= ' Lote,';
            $log = '1';
        }

        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_rendimiento->errorBD($ErrorLog, 0);
        }
        $datos = $obj_rendimiento->busquedaLote($lote);
        if (!is_array($datos) and $datos != '') {
            echo '0|' . $datos;
        }
        $datos = $datos == "" ? array() : $datos;
        if (count($datos) > 0) {
            echo '0|Existe un lote registrado con el folio ' . $lote . '';
        } else {
            echo '1|Número de Lote correcto.';
        }

        break;

    case 'initrendimientoetiquetas':
        $fechaFinal = (!empty($_POST['fechaFinal'])) ? trim($_POST['fechaFinal']) : '';
        $semanaProduccion = (!empty($_POST['semanaProduccion'])) ? trim($_POST['semanaProduccion']) : '';
        $lote = (!empty($_POST['lote'])) ? trim($_POST['lote']) : '';
        $programa = (!empty($_POST['programa'])) ? trim($_POST['programa']) : '';
        $materiaPrima = (!empty($_POST['materiaPrima'])) ? trim($_POST['materiaPrima']) : '';
        $_1s = (isset($_POST['1s'])) ? trim($_POST['1s']) : '';
        $_2s = (isset($_POST['2s'])) ? trim($_POST['2s']) : '';
        $_3s = (isset($_POST['3s'])) ? trim($_POST['3s']) : '';
        $_4s = (isset($_POST['4s'])) ? trim($_POST['4s']) : '';
        $total_s = (isset($_POST['total_s'])) ? trim($_POST['total_s']) : '';
        $proveedor = (isset($_POST['proveedor'])) ? trim($_POST['proveedor']) : '';
        $venta = (isset($_POST['venta'])) ? trim($_POST['venta']) : '';

        $log = '';
        if ($fechaFinal == '') {
            $ErrorLog .= ' fecha Final,';
            $log = '1';
        }
        if ($semanaProduccion == '') {
            $ErrorLog .= ' semana de Producción,';
            $log = '1';
        }

        if ($lote == '') {
            $ErrorLog .= ' lote,';
            $log = '1';
        }
        if ($programa == '') {
            $ErrorLog .= ' programa,';
            $log = '1';
        }
        if ($materiaPrima == '') {
            $ErrorLog .= ' materia Prima,';
            $log = '1';
        }

        if ($_1s == '' or $_1s < '0') {
            $ErrorLog .= ' 1s,';
            $log = '1';
        }
        if ($_2s == '' or $_2s < '0') {
            $ErrorLog .= ' 2s,';
            $log = '1';
        }
        if ($_3s == '' or $_3s < '0') {
            $ErrorLog .= ' 3s,';
            $log = '1';
        }
        if ($_4s == '' or $_4s < '0') {
            $ErrorLog .= ' 4s,';
            $log = '1';
        }
        if ($total_s == '' or $total_s < '0') {
            $ErrorLog .= ' total,';
            $log = '1';
        }
        if ($proveedor == '') {
            $ErrorLog .= ' proveedor,';

            $log = '1';
        }
        if ($venta == '') {
            $ErrorLog .= ' venta,';
            $log = '1';
        }
        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_rendimiento->errorBD($ErrorLog, 0);
        }
        #Array de Semana de Produccion
        $WeekYear = explode("-W", $semanaProduccion);
        $datos = $obj_rendimiento->initRendimientoEtiquetas(
            $fechaFinal,
            $WeekYear[1],
            $lote,
            $programa,
            $materiaPrima,
            $_1s,
            $_2s,
            $_3s,
            $_4s,
            $total_s,
            $proveedor,
            $venta
        );
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_rendimiento->errorBD($e->getMessage(), 1);
        }
        echo '1|Inicio de Rendimiento Almacenado Correctamente.';
        break;
    case 'cancelarrendimiento':
        $id = (!empty($_POST['id'])) ? trim($_POST['id']) : '';
        if ($id == '') {
            $ErrorLog .= ' rendimiento,';
            $log = '1';
        }
        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_rendimiento->errorBD($ErrorLog, 0);
        }
        $datos = $obj_rendimiento->cancelarRendimiento($id);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_rendimiento->errorBD($e->getMessage(), 1);
        }
        echo '1|El Rendimiento se ha eliminado Correctamente.';

        break;
    case 'eliminarrendimiento':
        $datos = $obj_rendimiento->eliminarRendimiento();
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_rendimiento->errorBD($e->getMessage(), 1);
        }
        $_SESSION['CRESuccessRendimiento'] = "El Pre-Registro de Rendimiento se ha eliminado Correctamente.";

        echo '1|El Pre-Registro de Rendimiento se ha eliminado Correctamente.';
        break;
    case 'actualizarpedido':
        $idRendimiento = (!empty($_POST['idRendimiento'])) ? trim($_POST['idRendimiento']) : '';

        $log = '';
        if ($idRendimiento == '') {
            $ErrorLog .= ' Rendimiento,';
            $log = '1';
        }

        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_rendimiento->errorBD($ErrorLog, 0);
        }

        $obj_rendimiento->beginTransaction();

        //Registra formulas desglosadas del promedio del loteo
        $datos = $obj_rendimiento->registraPedidoLoteo($idRendimiento);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_rendimiento->errorBD($e->getMessage(), 1);
        }

        //Actualiza los datos de los pedidos descontados
        $datos = $obj_rendimiento->actualizaPedidosUsados($idRendimiento);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_rendimiento->errorBD($e->getMessage(), 1);
        }
        $obj_rendimiento->insertarCommit();
        echo '1|Lote Actualizado Correctamente.';
        break;

    case 'cierrerendimiento':
        $obj_rendimiento->beginTransaction();
        $DataRendimiento = $obj_rendimiento->getRendimientoAbierto();
        //Pase a Almacen 
        $datos = $obj_rendimiento->paseAlmacenPT();
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_rendimiento->errorBD($e->getMessage(), 1);
        }
        //CIERRE DE CAPTURA
        $datos = Funciones::cambiarEstatus("rendimientos", "2", "estado", $DataRendimiento[0]["id"], $obj_rendimiento->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_rendimiento->errorBD($e->getMessage(), 1);
        }

        $obj_rendimiento->insertarCommit();
        $_SESSION['CRESuccessRendimiento'] = "El Pre-Registro de Rendimiento se almacenó Correctamente.";
        echo '1|El Pre-Registro de Rendimiento se almacenó Correctamente.';
        break;

    case 'areawb':
        $value = (!empty($_POST['value'])) ? trim($_POST['value']) : '';
        $log = '';

        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_rendimiento->errorBD($ErrorLog, 0);
        }
        $DatosAbiertos = $obj_rendimiento->getRendimientoAbierto();
        $datos = Funciones::edicionBasica("rendimientos", "areaWB", $value, "id", $DatosAbiertos[0]['id'], $obj_rendimiento->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_materias->errorBD($e->getMessage(), 1);
        }
        echo '1| Registro de Área WB en Recibo (pie2) Correcto.';

        break;
    case 'pzasrechazadas':
        $value = (!empty($_POST['value'])) ? trim($_POST['value']) : '';
        $log = '';

        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_rendimiento->errorBD($ErrorLog, 0);
        }
        $DatosAbiertos = $obj_rendimiento->getRendimientoAbierto();
        $datos = Funciones::edicionBasica("rendimientos", "piezasRechazadas", $value, "id", $DatosAbiertos[0]['id'], $obj_rendimiento->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_materias->errorBD($e->getMessage(), 1);
        }
        echo '1| Registro de Piezas rechazadas Correcto.';

        break;
    case 'comentariosrechazo':
        $value = (!empty($_POST['value'])) ? trim($_POST['value']) : '';
        $log = '';

        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_rendimiento->errorBD($ErrorLog, 0);
        }
        $DatosAbiertos = $obj_rendimiento->getRendimientoAbierto();
        $datos = Funciones::edicionBasica("rendimientos", "comentariosRechazo", $value, "id", $DatosAbiertos[0]['id'], $obj_rendimiento->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_materias->errorBD($e->getMessage(), 1);
        }
        echo '1| Registro de Comentarios de las Piezas rechazadas Correcto.';

        break;
    case 'recortewb':
        $value = (!empty($_POST['value'])) ? trim($_POST['value']) : '';
        $log = '';

        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_rendimiento->errorBD($ErrorLog, 0);
        }
        $DatosAbiertos = $obj_rendimiento->getRendimientoAbierto();
        $datos = Funciones::edicionBasica("rendimientos", "porcRecorteWB", $value, "id", $DatosAbiertos[0]['id'], $obj_rendimiento->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_materias->errorBD($e->getMessage(), 1);
        }
        echo '1| Registro de Recorte WB %  Correcto.';

        break;
    case "recortecrust":
        $value = (!empty($_POST['value'])) ? trim($_POST['value']) : '';
        $log = '';

        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_rendimiento->errorBD($ErrorLog, 0);
        }
        $DatosAbiertos = $obj_rendimiento->getRendimientoAbierto();
        $datos = Funciones::edicionBasica("rendimientos", "porcRecorteCrust", $value, "id", $DatosAbiertos[0]['id'], $obj_rendimiento->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_materias->errorBD($e->getMessage(), 1);
        }
        echo '1| Registro de Recorte Crust %  Correcto.';

        break;
    case "humedad":
        $value = (!empty($_POST['value'])) ? trim($_POST['value']) : '';
        $log = '';

        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_rendimiento->errorBD($ErrorLog, 0);
        }
        $DatosAbiertos = $obj_rendimiento->getRendimientoAbierto();
        $datos = Funciones::edicionBasica("rendimientos", "humedad", $value, "id", $DatosAbiertos[0]['id'], $obj_rendimiento->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_materias->errorBD($e->getMessage(), 1);
        }
        echo '1| Registro de Humedad  Correcto.';

        break;

    case "areacrust":
        $value = (!empty($_POST['value'])) ? trim($_POST['value']) : '';
        $log = '';

        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_rendimiento->errorBD($ErrorLog, 0);
        }
        $DatosAbiertos = $obj_rendimiento->getRendimientoAbierto();
        $datos = Funciones::edicionBasica("rendimientos", "areaCrust", $value, "id", $DatosAbiertos[0]['id'], $obj_rendimiento->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_materias->errorBD($e->getMessage(), 1);
        }
        echo '1| Registro de Área Crust  Correcto.';

        break;
    case "quiebre":
        $value = (!empty($_POST['value'])) ? trim($_POST['value']) : '';
        $log = '';

        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_rendimiento->errorBD($ErrorLog, 0);
        }
        $DatosAbiertos = $obj_rendimiento->getRendimientoAbierto();
        $datos = Funciones::edicionBasica("rendimientos", "quiebre", $value, "id", $DatosAbiertos[0]['id'], $obj_rendimiento->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_materias->errorBD($e->getMessage(), 1);
        }
        echo '1| Registro de Quiebre  Correcto.';

        break;
    case "suavidad":
        $value = (!empty($_POST['value'])) ? trim($_POST['value']) : '';
        $log = '';

        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_rendimiento->errorBD($ErrorLog, 0);
        }
        $DatosAbiertos = $obj_rendimiento->getRendimientoAbierto();
        $datos = Funciones::edicionBasica("rendimientos", "suavidad", $value, "id", $DatosAbiertos[0]['id'], $obj_rendimiento->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_materias->errorBD($e->getMessage(), 1);
        }
        echo '1| Registro de Suavidad  Correcto.';

        break;
    case "areafinalteseo":
        $value = (!empty($_POST['value'])) ? trim($_POST['value']) : '';
        $log = '';

        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_rendimiento->errorBD($ErrorLog, 0);
        }
        $DatosAbiertos = $obj_rendimiento->getRendimientoAbierto();
        $datos = Funciones::edicionBasica("rendimientos", "areaFinal", $value, "id", $DatosAbiertos[0]['id'], $obj_rendimiento->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_materias->errorBD($e->getMessage(), 1);
        }
        echo '1| Registro de Área Final (Teseo)  Correcto.';

        break;

    case "yieldinicialteseo":
        $value = (!empty($_POST['value'])) ? trim($_POST['value']) : '';
        $log = '';

        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_rendimiento->errorBD($ErrorLog, 0);
        }
        $DatosAbiertos = $obj_rendimiento->getRendimientoAbierto();
        $datos = Funciones::edicionBasica("rendimientos", "yieldInicialTeseo", $value, "id", $DatosAbiertos[0]['id'], $obj_rendimiento->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_materias->errorBD($e->getMessage(), 1);
        }
        echo '1| Registro de Yield Inicial Teseo  Correcto.';

        break;
    case "pzascortadasteseo":
        $value = (!empty($_POST['value'])) ? trim($_POST['value']) : '';
        $log = '';

        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_rendimiento->errorBD($ErrorLog, 0);
        }
        $DatosAbiertos = $obj_rendimiento->getRendimientoAbierto();
        $datos = Funciones::edicionBasica("rendimientos", "pzasCortadasTeseo", $value, "id", $DatosAbiertos[0]['id'], $obj_rendimiento->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_materias->errorBD($e->getMessage(), 1);
        }
        echo '1| Registro de Piezas Cortadas por Teseo Correcto.';

        break;
    case "pzascortadasteseo":
        $value = (!empty($_POST['value'])) ? trim($_POST['value']) : '';
        $log = '';

        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_rendimiento->errorBD($ErrorLog, 0);
        }
        $DatosAbiertos = $obj_rendimiento->getRendimientoAbierto();
        $datos = Funciones::edicionBasica("rendimientos", "pzasCortadasTeseo", $value, "id", $DatosAbiertos[0]['id'], $obj_rendimiento->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_materias->errorBD($e->getMessage(), 1);
        }
        echo '1| Registro de Piezas Cortadas por Teseo Correcto.';

        break;
    case "setscortadosteseo":
        $value = (!empty($_POST['value'])) ? trim($_POST['value']) : '';
        $log = '';

        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_rendimiento->errorBD($ErrorLog, 0);
        }
        $DatosAbiertos = $obj_rendimiento->getRendimientoAbierto();
        $datos = Funciones::edicionBasica("rendimientos", "setsCortadosTeseo", $value, "id", $DatosAbiertos[0]['id'], $obj_rendimiento->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_materias->errorBD($e->getMessage(), 1);
        }
        echo '1| Registro de Sets Cortados por Teseo Correcto.';

        break;
    case "setsrechazados":
        $value = (!empty($_POST['value'])) ? trim($_POST['value']) : '';
        $log = '';

        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_rendimiento->errorBD($ErrorLog, 0);
        }
        $DatosAbiertos = $obj_rendimiento->getRendimientoAbierto();
        $datos = Funciones::edicionBasica("rendimientos", "setsRechazados", $value, "id", $DatosAbiertos[0]['id'], $obj_rendimiento->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_materias->errorBD($e->getMessage(), 1);
        }
        echo '1| Registro de Sets Rechazados por Teseo Correcto.';

        break;
    case "piezasrecuperadas":
        $value = (!empty($_POST['value'])) ? trim($_POST['value']) : '';
        $log = '';

        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_rendimiento->errorBD($ErrorLog, 0);
        }
        $DatosAbiertos = $obj_rendimiento->getRendimientoAbierto();
        $datos = Funciones::edicionBasica("rendimientos", "piezasRecuperadas", $value, "id", $DatosAbiertos[0]['id'], $obj_rendimiento->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_materias->errorBD($e->getMessage(), 1);
        }
        echo '1| Registro de Piezas Recuperadas por Teseo Correcto.';

        break;
    case "setsrecuperados":
        $value = (!empty($_POST['value'])) ? trim($_POST['value']) : '';
        $log = '';

        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_rendimiento->errorBD($ErrorLog, 0);
        }
        $DatosAbiertos = $obj_rendimiento->getRendimientoAbierto();
        $datos = Funciones::edicionBasica("rendimientos", "setsRecuperados", $value, "id", $DatosAbiertos[0]['id'], $obj_rendimiento->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_materias->errorBD($e->getMessage(), 1);
        }
        echo '1| Registro de Sets Recuperados por Teseo Correcto.';

        break;
    case "setsempacados":
        $value = (!empty($_POST['value'])) ? trim($_POST['value']) : '';
        $log = '';

        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_rendimiento->errorBD($ErrorLog, 0);
        }
        $DatosAbiertos = $obj_rendimiento->getRendimientoAbierto();
        $datos = Funciones::edicionBasica("rendimientos", "setsEmpacados", $value, "id", $DatosAbiertos[0]['id'], $obj_rendimiento->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_materias->errorBD($e->getMessage(), 1);
        }
        echo '1| Registro de Sets Empacados Correcto.';

        break;
    case "areapzasrechazadas":
        $value = (!empty($_POST['value'])) ? trim($_POST['value']) : '';
        $log = '';

        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_rendimiento->errorBD($ErrorLog, 0);
        }
        $DatosAbiertos = $obj_rendimiento->getRendimientoAbierto();
        $datos = Funciones::edicionBasica("rendimientos", "areaPzasRechazo", $value, "id", $DatosAbiertos[0]['id'], $obj_rendimiento->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_materias->errorBD($e->getMessage(), 1);
        }
        echo '1| Registro de Área de Piezas Rechazadas Correcto.';

        break;
    case "totalrecorte":
        $value = (!empty($_POST['value'])) ? trim($_POST['value']) : '';
        $log = '';

        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_rendimiento->errorBD($ErrorLog, 0);
        }
        $DatosAbiertos = $obj_rendimiento->getRendimientoAbierto();
        $datos = Funciones::edicionBasica("rendimientos", "totalRecorte", $value, "id", $DatosAbiertos[0]['id'], $obj_rendimiento->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_materias->errorBD($e->getMessage(), 1);
        }
        echo '1| Registro de % Total Recorte Correcto.';

        break;
    case "perdidawbcrust":
        $value = (!empty($_POST['value'])) ? trim($_POST['value']) : '';
        $log = '';

        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_rendimiento->errorBD($ErrorLog, 0);
        }
        $DatosAbiertos = $obj_rendimiento->getRendimientoAbierto();
        $datos = Funciones::edicionBasica("rendimientos", "perdidaAreaWBCrust", $value, "id", $DatosAbiertos[0]['id'], $obj_rendimiento->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_materias->errorBD($e->getMessage(), 1);
        }
        echo '1| Registro de perdida Area WB a Crust Correcto.';

        break;

    case "perdidacrustteseo":
        $value = (!empty($_POST['value'])) ? trim($_POST['value']) : '';
        $log = '';

        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_rendimiento->errorBD($ErrorLog, 0);
        }
        $DatosAbiertos = $obj_rendimiento->getRendimientoAbierto();
        $datos = Funciones::edicionBasica("rendimientos", "perdidaAreaCrustTeseo", $value, "id", $DatosAbiertos[0]['id'], $obj_rendimiento->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_materias->errorBD($e->getMessage(), 1);
        }
        echo '1| Registro de perdida Area de Crust a Teseo Correcto.';

        break;
    case "setcutteseo":
        $value = (!empty($_POST['value'])) ? trim($_POST['value']) : '';
        $log = '';

        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_rendimiento->errorBD($ErrorLog, 0);
        }
        $DatosAbiertos = $obj_rendimiento->getRendimientoAbierto();
        $datos = Funciones::edicionBasica("rendimientos", "setsCortadosTeseo", $value, "id", $DatosAbiertos[0]['id'], $obj_rendimiento->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_materias->errorBD($e->getMessage(), 1);
        }
        echo '1| Registro de sets Cortados Teseo Correcto.';

        break;
    case "yieldfinalreal":
        $value = (!empty($_POST['value'])) ? trim($_POST['value']) : '';
        $log = '';

        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_rendimiento->errorBD($ErrorLog, 0);
        }
        $DatosAbiertos = $obj_rendimiento->getRendimientoAbierto();
        $datos = Funciones::edicionBasica("rendimientos", "yieldFinalReal", $value, "id", $DatosAbiertos[0]['id'], $obj_rendimiento->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_rendimiento->errorBD($e->getMessage(), 1);
        }
        echo '1| Registro de Yield Final Real Correcto.';

        break;
    case "setsrechazados":
        $value = (!empty($_POST['value'])) ? trim($_POST['value']) : '';
        $log = '';

        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_rendimiento->errorBD($ErrorLog, 0);
        }
        $DatosAbiertos = $obj_rendimiento->getRendimientoAbierto();
        $datos = Funciones::edicionBasica("rendimientos", "setsRechazados", $value, "id", $DatosAbiertos[0]['id'], $obj_rendimiento->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_materias->errorBD($e->getMessage(), 1);
        }
        echo '1| Registro de Sets Rechazados Correcto.';

        break;
    case "porcrechazoini":
        $value = (!empty($_POST['value'])) ? trim($_POST['value']) : '';
        $log = '';

        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_rendimiento->errorBD($ErrorLog, 0);
        }
        $DatosAbiertos = $obj_rendimiento->getRendimientoAbierto();
        $datos = Funciones::edicionBasica("rendimientos", "porcSetsRechazoInicial", $value, "id", $DatosAbiertos[0]['id'], $obj_rendimiento->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_materias->errorBD($e->getMessage(), 1);
        }
        echo '1| Registro de porcentaje de Sets Rechazo Inicial Correcto.';

        break;
    case "setsRecuperados":
        $value = (!empty($_POST['value'])) ? trim($_POST['value']) : '';
        $log = '';

        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_rendimiento->errorBD($ErrorLog, 0);
        }
        $DatosAbiertos = $obj_rendimiento->getRendimientoAbierto();
        $datos = Funciones::edicionBasica("rendimientos", "setsRecuperados", $value, "id", $DatosAbiertos[0]['id'], $obj_rendimiento->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_materias->errorBD($e->getMessage(), 1);
        }
        echo '1| Registro de Sets Recuperados Correcto.';

        break;
    case "porcrecuperacion":
        $value = (!empty($_POST['value'])) ? trim($_POST['value']) : '';
        $log = '';

        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_rendimiento->errorBD($ErrorLog, 0);
        }
        $DatosAbiertos = $obj_rendimiento->getRendimientoAbierto();
        $datos = Funciones::edicionBasica("rendimientos", "porcRecuperacion", $value, "id", $DatosAbiertos[0]['id'], $obj_rendimiento->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_materias->errorBD($e->getMessage(), 1);
        }
        echo '1| Registro de Porcentaje de Recuperación Correcto.';

        break;
    case "porcfinrechazo":
        $value = (!empty($_POST['value'])) ? trim($_POST['value']) : '';
        $log = '';

        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_rendimiento->errorBD($ErrorLog, 0);
        }
        $DatosAbiertos = $obj_rendimiento->getRendimientoAbierto();
        $datos = Funciones::edicionBasica("rendimientos", "porcFinalRechazo", $value, "id", $DatosAbiertos[0]['id'], $obj_rendimiento->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_materias->errorBD($e->getMessage(), 1);
        }
        echo '1| Registro de Porcentaje Final Rechazo Correcto.';

        break;
    case "areacrustxset":
        $value = (!empty($_POST['value'])) ? trim($_POST['value']) : '';
        $log = '';

        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_rendimiento->errorBD($ErrorLog, 0);
        }
        $DatosAbiertos = $obj_rendimiento->getRendimientoAbierto();
        $datos = Funciones::edicionBasica("rendimientos", "areaCrustSet", $value, "id", $DatosAbiertos[0]['id'], $obj_rendimiento->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_materias->errorBD($e->getMessage(), 1);
        }
        echo '1| Registro de Área Crust por Set Correcto.';

        break;
    case "areawbxset":
        $value = (!empty($_POST['value'])) ? trim($_POST['value']) : '';
        $log = '';

        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_rendimiento->errorBD($ErrorLog, 0);
        }
        $DatosAbiertos = $obj_rendimiento->getRendimientoAbierto();
        $datos = Funciones::edicionBasica("rendimientos", "areaWBUnidad", $value, "id", $DatosAbiertos[0]['id'], $obj_rendimiento->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_materias->errorBD($e->getMessage(), 1);
        }
        echo '1| Registro de Área WB por Set Correcto.';

        break;
    case "areawbterminado":
        $value = (!empty($_POST['value'])) ? trim($_POST['value']) : '';
        $log = '';

        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_rendimiento->errorBD($ErrorLog, 0);
        }
        $DatosAbiertos = $obj_rendimiento->getRendimientoAbierto();
        $datos = Funciones::edicionBasica("rendimientos", "areaWBTerminado", $value, "id", $DatosAbiertos[0]['id'], $obj_rendimiento->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_materias->errorBD($e->getMessage(), 1);
        }
        echo '1| Registro de Área WB A Terminado.';

        break;


    case "recalcular":
        $id = (!empty($_POST['id'])) ? trim($_POST['id']) : '';
        $setEmpacados = (isset($_POST['setEmpacados'])) ? trim($_POST['setEmpacados']) : '';

        $log = '';
        if ($id == '') {
            $ErrorLog .= ' Lote,';
            $log = '1';
        }
        if ($setEmpacados == '') {
            $ErrorLog .= ' set Empacados,';
            $log = '1';
        }
        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_rendimiento->errorBD($ErrorLog, 0);
        }
        $datos = $obj_rendimiento->recalcularRendimiento($id, $setEmpacados);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_rendimiento->errorBD($e->getMessage(), 1);
        }
        echo '1|Sets Empacados Recalculados Correctamente.';
        break;

    case "recalcularpzasrecup":
        $id = (!empty($_POST['id'])) ? trim($_POST['id']) : '';
        $pzasRecuperadas = (isset($_POST['pzasRecuperadas'])) ? trim($_POST['pzasRecuperadas']) : '';

        $log = '';
        if ($id == '') {
            $ErrorLog .= ' Lote,';
            $log = '1';
        }
        if ($pzasRecuperadas == '') {
            $ErrorLog .= ' set Empacados,';
            $log = '1';
        }
        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_rendimiento->errorBD($ErrorLog, 0);
        }
        $datos = $obj_rendimiento->recalcularPzasRecuperadas($id, $pzasRecuperadas);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_rendimiento->errorBD($e->getMessage(), 1);
        }
        echo '1|Sets Recuperados fueron Recalculados Correctamente.';
        break;
}
