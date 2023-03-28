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
    case "select2semanalotes":
        if (!isset($_POST['palabraClave'])) {
            $Data = $obj_rendimiento->getSemanaSelect2();
            $Data = Excepciones::validaConsulta($Data);
        } else {
            $search = $_POST['palabraClave']; // Palabra a buscar
            $Data = $obj_rendimiento->getSemanaSelect2($search);
            $Data = Excepciones::validaConsulta($Data);
        }
        $response = array();
        // Leer la informacion
        foreach ($Data as $area) {
            $response[] = array(
                "id" => $area['id'],
                "text" => $area['text']
            );
        }

        //Creamos el JSON
        $json_string = json_encode($response);
        echo $json_string;
        break;
    case "select2lotessets":
        if (!isset($_POST['palabraClave'])) {
            $Data = $obj_rendimiento->getLotesTeseoSelect2();
            $Data = Excepciones::validaConsulta($Data);
        } else {
            $search = $_POST['palabraClave']; // Palabra a buscar
            $Data = $obj_rendimiento->getLotesTeseoSelect2($search);
            $Data = Excepciones::validaConsulta($Data);
        }
        $response = array();

        // // Leer la informacion
       /* foreach ($Data as $area) {
            $response[] = array(
                "id" => $area['id'],
                "text" => $area['loteTemola']
            );
        }*/

        //Creamos el JSON
        $json_string = json_encode($Data);
        echo $json_string;
        break;
    case "select2lotesprocesos":
        if (!isset($_POST['palabraClave'])) {
            $Data = $obj_rendimiento->getLotesProceso();
            $Data = Excepciones::validaConsulta($Data);
        } else {
            $search = $_POST['palabraClave']; // Palabra a buscar
            $Data = $obj_rendimiento->getLotesProceso($search);
            $Data = Excepciones::validaConsulta($Data);
        }
        $response = array();
        $json_string = json_encode($Data);
        echo $json_string;
        break;
        //**************** Registrar Inicio del Lote **************
    case "initRendimiento":
        $fechaEngrase = (!empty($_POST['fechaEngrase'])) ? trim($_POST['fechaEngrase']) : '';
        $proceso = (!empty($_POST['proceso'])) ? trim($_POST['proceso']) : '';
        $lote = (!empty($_POST['lote'])) ? trim($_POST['lote']) : '';
        $programa = (!empty($_POST['programa'])) ? trim($_POST['programa']) : '';
        $materiaPrima = (!empty($_POST['materiaPrima'])) ? trim($_POST['materiaPrima']) : '';
        $multimateria = (!empty($_POST['multimateria'])) ? trim($_POST['multimateria']) : '0';
        #VALIDACION DE DATOS
        Excepciones::validaLlenadoDatos(array(
            " fecha de Engrase" => $fechaEngrase,
            " Proceso" => $proceso,
            " Lote" => $lote,
            " Programa" => $programa,
            " Materia Prima" => $materiaPrima,
        ), $obj_rendimiento);
        #REGISTRO DE RENDIMIENTO
        $datos = $obj_rendimiento->initRendimiento(
            $fechaEngrase,
            $proceso,
            $lote,
            $programa,
            $materiaPrima,
            $multimateria
        );
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_rendimiento->errorBD($e->getMessage(), 1);
        }
        echo '1|Inicio de Rendimiento Almacenado Correctamente.';
        break;
        //****************SELECCIONAR LOTE PARA AGREGAR SUS KPIS ******************
    case "seleccionarlote":
        $lotePreRegistro = (isset($_POST['lotePreRegistro'])) ? trim($_POST['lotePreRegistro']) : '';
        #VALIDACION DE DATOS
        Excepciones::validaLlenadoDatos(array(
            " Lote" => $lotePreRegistro,
        ), $obj_rendimiento);

        $obj_rendimiento->beginTransaction();
        //Cambio de estatus del preregistro del lote para que se pueda agregar
        $datos = Funciones::cambiarEstatus("rendimientos", "3", "estado", $lotePreRegistro, $obj_rendimiento->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_rendimiento->errorBD($e->getMessage(), 1);
        }
        //Cambio de Fecha y Usuario que registra el rendimiento
        $datos = Funciones::edicionBasica("rendimientos", "idUserRend", $idUser, "id", $lotePreRegistro, $obj_rendimiento->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_materias->errorBD($e->getMessage(), 1);
        }
        $datos = Funciones::edicionBasica("rendimientos", "fechaRend", date("Y-m-d H:i:s"), "id", $lotePreRegistro, $obj_rendimiento->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_materias->errorBD($e->getMessage(), 1);
        }
        $obj_rendimiento->insertarCommit();
        echo '1|Lote Seleccionado para agregar Rendimiento.';
        break;
        //****************CONSULTAR LOTE ******************
    case "consultarlote":
        $lote = (isset($_POST['lote'])) ? trim($_POST['lote']) : '';
        #VALIDACION DE DATOS
        Excepciones::validaLlenadoDatos(array(
            " Lote" => $lote,
        ), $obj_rendimiento);

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
        //****************INICIAR LOTE DE ETIQUETAS ******************
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
        #VALIDACION DE DATOS
        Excepciones::validaLlenadoDatos(array(
            'fecha Final,' => $fechaFinal,
            'semana de Producción,' => $semanaProduccion,
            'lote,' => $lote,
            'programa,' => $programa,
            'materia Prima,' => $materiaPrima,
            'proveedor,' => $proveedor,
            'venta,' => $venta,

        ), $obj_rendimiento);
        #Valida Clasificacion de MP 
        $log = '';
        $ErrorLog = '';
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
        //****************CANCELAR RENDIMIENTO  ******************
    case 'cancelarrendimiento':
        $id = (!empty($_POST['id'])) ? trim($_POST['id']) : '';
        #VALIDACION DE DATOS
        Excepciones::validaLlenadoDatos(array(
            'rendimiento,' => $id
        ), $obj_rendimiento);
        $datos = $obj_rendimiento->cancelarRendimiento($id);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_rendimiento->errorBD($e->getMessage(), 1);
        }
        echo '1|El Rendimiento se ha eliminado Correctamente.';

        break;
        //****************ELIMINAR RENDIMIENTO  ******************
    case 'eliminarrendimiento':
        $id = (!empty($_POST['id'])) ? trim($_POST['id']) : '';
        #VALIDACION DE DATOS
        Excepciones::validaLlenadoDatos(array(
            'rendimiento,' => $id
        ), $obj_rendimiento);
        $obj_rendimiento->beginTransaction();
        //Eliminar Registro
        $datos = $obj_rendimiento->eliminarRendimiento($id);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_rendimiento->errorBD($e->getMessage(), 1);
        }
        $obj_rendimiento->insertarCommit();
        echo '1|El Pre-Registro de Rendimiento se ha eliminado Correctamente.';
        break;
        //****************ELIMINAR PRE REGISTRO DE RENDIMIENTO  ******************
    case 'eliminarprerendimiento':
        $id = (!empty($_POST['id'])) ? trim($_POST['id']) : '';
        #VALIDACION DE DATOS
        Excepciones::validaLlenadoDatos(array(
            'rendimiento,' => $id
        ), $obj_rendimiento);

        $obj_rendimiento->beginTransaction();
        //Pase de Producto 
        $datos = $obj_rendimiento->cancelacionPedidoEnLote($id);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_rendimiento->errorBD($e->getMessage(), 1);
        }
        //Eliminar Registro
        $datos = $obj_rendimiento->eliminarPreRendimiento($id);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_rendimiento->errorBD($e->getMessage(), 1);
        }
        $obj_rendimiento->insertarCommit();
        echo '1|El Pre-Registro de Rendimiento se ha eliminado Correctamente.';
        break;
        //****************ACTUALIZA STOCK DE MP  ******************
    case 'actualizarpedido':
        $idRendimiento = (!empty($_POST['idRendimiento'])) ? trim($_POST['idRendimiento']) : '';
        #VALIDACION DE DATOS
        Excepciones::validaLlenadoDatos(array(
            'rendimiento,' => $idRendimiento
        ), $obj_rendimiento);
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
        $_SESSION['CRESuccessRendimiento'] = "El Registro de Lote se almacenó Correctamente.";

        echo '1|Lote Actualizado Correctamente.';
        break;
        //**************** CIERRE DE RENDIMIENTO  ******************
    case 'cierrerendimiento':
        $cambioPzas = (isset($_POST['cambioPzas'])) ? trim($_POST['cambioPzas']) : '';
        #VALIDACION DE DATOS
        Excepciones::validaLlenadoDatos(array(
            ' Ajuste en Piezas,' => $cambioPzas
        ), $obj_rendimiento);

        $obj_rendimiento->beginTransaction();
        $DataRendimiento = $obj_rendimiento->getRendimientoAbierto();
        //Cambio de Estatus de Finalizacion de Registro de Datos del lote
        $datos = $obj_rendimiento->cambiaEstatusRegDatos($DataRendimiento[0]["id"]);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_rendimiento->errorBD($e->getMessage(), 1);
        }
        //Rendimiento
        $datos = $obj_rendimiento->calcularRendimiento($cambioPzas);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_rendimiento->errorBD($e->getMessage(), 1);
        }
        //Creacion del SuperLote
        /* if ($cambioPzas == '1') {
            $datos = $obj_rendimiento->creacionSuperLote(false, $DataRendimiento[0]["id"]);
            try {
                Excepciones::validaMsjError($datos);
            } catch (Exception $e) {
                $obj_rendimiento->errorBD($e->getMessage(), 1);
            }
        }*/


        $obj_rendimiento->insertarCommit();
        $_SESSION['CRESuccessRendimiento'] = "El Rendimiento del Lote se almacenó Correctamente.";
        echo '1|El Registro de Rendimiento se almacenó Correctamente.';
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
        echo '1| Registro de Cueros rechazados Correcto.';

        break;
    case "pzasreasig":
        $value = (!empty($_POST['value'])) ? trim($_POST['value']) : '';
        $log = '';

        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_rendimiento->errorBD($ErrorLog, 0);
        }
        $DatosAbiertos = $obj_rendimiento->getRendimientoAbierto();
        $datos = Funciones::edicionBasica("rendimientos", "cuerosReasig", $value, "id", $DatosAbiertos[0]['id'], $obj_rendimiento->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_materias->errorBD($e->getMessage(), 1);
        }
        echo '1| Registro de Cueros Asignados Correcto.';
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
        //Total de Recuperacion Inicio
        $datos = Funciones::edicionBasica("rendimientos", "totalRecu", $value, "id", $DatosAbiertos[0]['id'], $obj_rendimiento->getConexion(), $debug);
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
        $datos = Funciones::edicionBasica("rendimientos", "unidadesEmpacadas", $value, "id", $DatosAbiertos[0]['id'], $obj_rendimiento->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_materias->errorBD($e->getMessage(), 1);
        }
        //Total de Empaque Inicio
        $datos = Funciones::edicionBasica("rendimientos", "totalEmp", $value, "id", $DatosAbiertos[0]['id'], $obj_rendimiento->getConexion(), $debug);
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
        /* RECORTE DE ACABADO */
    case "recorteacabado":
        $value = (!empty($_POST['value'])) ? trim($_POST['value']) : '';
        $log = '';

        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_rendimiento->errorBD($ErrorLog, 0);
        }
        $DatosAbiertos = $obj_rendimiento->getRendimientoAbierto();
        $datos = Funciones::edicionBasica("rendimientos", "recorteAcabado", $value, "id", $DatosAbiertos[0]['id'], $obj_rendimiento->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_materias->errorBD($e->getMessage(), 1);
        }
        echo '1|Registro de Recorte de Acabado Correcto.';

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
    case "semanaproduccion":
        $value = (!empty($_POST['value'])) ? trim($_POST['value']) : '';
        #Array de Semana de Produccion
        $WeekYear = explode("-W", $value);

        $log = '';

        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_rendimiento->errorBD($ErrorLog, 0);
        }
        $DatosAbiertos = $obj_rendimiento->getRendimientoAbierto();
        $datos = Funciones::edicionBasica("rendimientos", "semanaProduccion", $WeekYear[1], "id", $DatosAbiertos[0]['id'], $obj_rendimiento->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_materias->errorBD($e->getMessage(), 1);
        }
        echo '1| Registro de Semana de Producción.';

        break;
    case "fechaempaque":
        $value = (!empty($_POST['value'])) ? trim($_POST['value']) : '';
        $log = '';

        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_rendimiento->errorBD($ErrorLog, 0);
        }
        $DatosAbiertos = $obj_rendimiento->getRendimientoAbierto();
        $datos = Funciones::edicionBasica("rendimientos", "fechaEmpaque", $value, "id", $DatosAbiertos[0]['id'], $obj_rendimiento->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_materias->errorBD($e->getMessage(), 1);
        }
        echo '1| Registro de Fecha de Empaque.';

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
    case "reasignaprograma":
        $idLote = (!empty($_POST['idLote'])) ? trim($_POST['idLote']) : '';
        $programa = (isset($_POST['programa'])) ? trim($_POST['programa']) : '';
        $proceso = (isset($_POST['proceso'])) ? trim($_POST['proceso']) : '';
        $option = (isset($_POST['option'])) ? trim($_POST['option']) : '0';

        Excepciones::validaLlenadoDatos(array(
            " Lote" => $idLote, " programa" => $programa,

        ), $obj_rendimiento);
        if ($option == '0') {
            Excepciones::validaLlenadoDatos(array(" Proceso" => $proceso), $obj_rendimiento);
        }
        $obj_rendimiento->beginTransaction();
        $datos = $obj_rendimiento->registroHistReasignacionPrograma($idLote, $programa, $proceso, $option);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_rendimiento->errorBD($e->getMessage(), 1);
        }
        if ($option == '0') {
            $datos = $obj_rendimiento->reasignacionProgramaALote($idLote, $programa, $proceso);
            try {
                Excepciones::validaMsjError($datos);
            } catch (Exception $e) {
                $obj_rendimiento->errorBD($e->getMessage(), 1);
            }
        } else {
            $datos = $obj_rendimiento->reasignacionProgramaALote($idLote, $programa);
            try {
                Excepciones::validaMsjError($datos);
            } catch (Exception $e) {
                $obj_rendimiento->errorBD($e->getMessage(), 1);
            }
        }
        $obj_rendimiento->insertarCommit();
        echo '1|Reasignación de Programa Correctamente.';

        /* $datos = $obj_rendimiento->recalcularPzasRecuperadas($id, $pzasRecuperadas);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_rendimiento->errorBD($e->getMessage(), 1);
        }
        echo '1|Sets Recuperados fueron Recalculados Correctamente.';*/
        break;
}
