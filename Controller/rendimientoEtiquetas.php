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
        $datos = $obj_rendimiento->busquedaLoteEtiq($lote);
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



    case 'cancelarrendimiento':
        $id = (!empty($_POST['id'])) ? trim($_POST['id']) : '';
        $log = "";
        if ($id == '') {
            $ErrorLog .= ' rendimiento,';
            $log = '1';
        }
        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_rendimiento->errorBD($ErrorLog, 0);
        }
        $datos = $obj_rendimiento->cancelarRendimientoEtiqueta($id);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_rendimiento->errorBD($e->getMessage(), 1);
        }
        echo '1|El Rendimiento se ha eliminado Correctamente.';

        break;
    case 'eliminarrendimiento':
        $datos = $obj_rendimiento->eliminarRendimientoEtiquetas();
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_rendimiento->errorBD($e->getMessage(), 1);
        }
        $_SESSION['CRESuccessRendimientoEtq'] = "El Pre-Registro de Rendimiento se ha eliminado Correctamente.";

        echo '1|El Pre-Registro de Rendimiento se ha eliminado Correctamente.';
        break;
    case 'actualizarpedido':
        $id = (!empty($_POST['id'])) ? trim($_POST['id']) : '';
        $idPedido = (!empty($_POST['idPedido'])) ? trim($_POST['idPedido']) : '';

        $log = '';
        if ($id == '') {
            $ErrorLog .= ' Lote de Etiqueta,';
            $log = '1';
        }
        if ($idPedido == '') {
            $ErrorLog .= ' pedido,';
            $log = '1';
        }
        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_rendimiento->errorBD($ErrorLog, 0);
        }

        $datos = $obj_rendimiento->registrarPedidoEtiquetas($id, $idPedido);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_rendimiento->errorBD($e->getMessage(), 1);
        }
        echo '1|Lote Actualizado Correctamente.';
        break;

    case 'cierrerendimiento':
        $edicion = (!empty($_POST['edicion'])) ? trim($_POST['edicion']) : '0';
        if ($edicion == '0') {
            $DatosAbiertos = $obj_rendimiento->getRendimientoAbierto("2");
            $id =  $DatosAbiertos[0]['id'];
        } else {
            $id = $edicion;
        }
        $obj_rendimiento->beginTransaction();

        //DESCUENTO DE PIEZAS RECHAZADAS EN TOTAL S
       /* $datos = $obj_rendimiento->decrementoTotal_S($id);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_rendimiento->errorBD($e->getMessage(), 1);
        }*/
        //CIERRE DE CAPTURA
        $datos = Funciones::cambiarEstatus("rendimientosetiquetas", "2", "estado", $id, $obj_rendimiento->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_rendimiento->errorBD($e->getMessage(), 1);
        }

        $obj_rendimiento->insertarCommit();
        if ($edicion == '0') {
            $_SESSION['CRESuccessRendimiento'] = "El Registro de Rendimiento se almacenó Correctamente.";
        } else {
            $_SESSION['CRESuccessRendEdit'] = "Edición de Rendimiento se almacenó Correctamente.";
        }
        echo '1|El Pre-Registro de Rendimiento se almacenó Correctamente.';
        break;

    case 'areawb':
        $value = (!empty($_POST['value'])) ? trim($_POST['value']) : '';
        $edicion = (!empty($_POST['edicion'])) ? trim($_POST['edicion']) : '0';


        $log = '';

        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_rendimiento->errorBD($ErrorLog, 0);
        }
        if ($edicion == '0') {
            $DatosAbiertos = $obj_rendimiento->getRendimientoAbierto("2");
            $id =  $DatosAbiertos[0]['id'];
        } else {
            $id = $edicion;
        }
        $datos = Funciones::edicionBasica("rendimientosetiquetas", "areaWB", $value, "id", $id, $obj_rendimiento->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_materias->errorBD($e->getMessage(), 1);
        }
        echo '1| Registro de Área Wet Blue Correcto.';

        break;
    case 'pzasrechazadas':
        $value = (!empty($_POST['value'])) ? trim($_POST['value']) : '';
        $edicion = (!empty($_POST['edicion'])) ? trim($_POST['edicion']) : '0';


        $log = '';

        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_rendimiento->errorBD($ErrorLog, 0);
        }
        if ($edicion == '0') {
            $DatosAbiertos = $obj_rendimiento->getRendimientoAbierto("2");
            $id =  $DatosAbiertos[0]['id'];
        } else {
            $id = $edicion;
        }
        $datos = Funciones::edicionBasica("rendimientosetiquetas", "piezasRechazadas", $value, "id", $id, $obj_rendimiento->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_materias->errorBD($e->getMessage(), 1);
        }
        echo '1| Registro de Piezas rechazadas Correcto.';

        break;
    case 'comentariosrechazo':
        $value = (!empty($_POST['value'])) ? trim($_POST['value']) : '';
        $edicion = (!empty($_POST['edicion'])) ? trim($_POST['edicion']) : '0';


        $log = '';

        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_rendimiento->errorBD($ErrorLog, 0);
        }
        if ($edicion == '0') {
            $DatosAbiertos = $obj_rendimiento->getRendimientoAbierto("2");
            $id =  $DatosAbiertos[0]['id'];
        } else {
            $id = $edicion;
        }
        $datos = Funciones::edicionBasica("rendimientosetiquetas", "comentariosRechazo", $value, "id", $id, $obj_rendimiento->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_materias->errorBD($e->getMessage(), 1);
        }
        echo '1| Registro de Comentarios de las Piezas rechazadas Correcto.';

        break;

    case "areafinal":
        $value = (!empty($_POST['value'])) ? trim($_POST['value']) : '';
        $edicion = (!empty($_POST['edicion'])) ? trim($_POST['edicion']) : '0';


        $log = '';

        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_rendimiento->errorBD($ErrorLog, 0);
        }
        if ($edicion == '0') {
            $DatosAbiertos = $obj_rendimiento->getRendimientoAbierto("2");
            $id =  $DatosAbiertos[0]['id'];
        } else {
            $id = $edicion;
        }
        $datos = Funciones::edicionBasica("rendimientosetiquetas", "areaFinal", $value, "id", $id, $obj_rendimiento->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_materias->errorBD($e->getMessage(), 1);
        }
        echo '1| Registro de Área Final Correcto.';

        break;

    case "costoft2":
        $value = (!empty($_POST['value'])) ? trim($_POST['value']) : '';
        $edicion = (!empty($_POST['edicion'])) ? trim($_POST['edicion']) : '0';


        $log = '';

        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_rendimiento->errorBD($ErrorLog, 0);
        }
        if ($edicion == '0') {
            $DatosAbiertos = $obj_rendimiento->getRendimientoAbierto("2");
            $id =  $DatosAbiertos[0]['id'];
        } else {
            $id = $edicion;
        }
        $datos = Funciones::edicionBasica("rendimientosetiquetas", "costoXft2", $value, "id", $id, $obj_rendimiento->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_materias->errorBD($e->getMessage(), 1);
        }
        echo '1| Registro de Yield Inicial Teseo  Correcto.';

        break;
    case "perdidawbterminado":
        $value = (!empty($_POST['value'])) ? trim($_POST['value']) : '';
        $edicion = (!empty($_POST['edicion'])) ? trim($_POST['edicion']) : '0';


        $log = '';

        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_rendimiento->errorBD($ErrorLog, 0);
        }
        if ($edicion == '0') {
            $DatosAbiertos = $obj_rendimiento->getRendimientoAbierto("2");
            $id =  $DatosAbiertos[0]['id'];
        } else {
            $id = $edicion;
        }
        $datos = Funciones::edicionBasica("rendimientosetiquetas", "perdidaAreaWBTerm", $value, "id", $id, $obj_rendimiento->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_materias->errorBD($e->getMessage(), 1);
        }
        echo '1| Registro de perdida de WB a Terminado  Correcto.';

        break;


    case "promedioareawb":
        $value = (!empty($_POST['value'])) ? trim($_POST['value']) : '';
        $edicion = (!empty($_POST['edicion'])) ? trim($_POST['edicion']) : '0';


        $log = '';

        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_rendimiento->errorBD($ErrorLog, 0);
        }
        if ($edicion == '0') {
            $DatosAbiertos = $obj_rendimiento->getRendimientoAbierto("2");
            $id =  $DatosAbiertos[0]['id'];
        } else {
            $id = $edicion;
        }
        $datos = Funciones::edicionBasica("rendimientosetiquetas", "promedioAreaWB", $value, "id", $id, $obj_rendimiento->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_materias->errorBD($e->getMessage(), 1);
        }
        echo '1| Registro de promedio de Área WB Correcto.';


        break;

    case "areapzasrechazo":
        $value = (!empty($_POST['value'])) ? trim($_POST['value']) : '';
        $edicion = (!empty($_POST['edicion'])) ? trim($_POST['edicion']) : '0';


        $log = '';

        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_rendimiento->errorBD($ErrorLog, 0);
        }
        if ($edicion == '0') {
            $DatosAbiertos = $obj_rendimiento->getRendimientoAbierto("2");
            $id =  $DatosAbiertos[0]['id'];
        } else {
            $id = $edicion;
        }
        $datos = Funciones::edicionBasica("rendimientosetiquetas", "areaPzasRechazo", $value, "id", $id, $obj_rendimiento->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_materias->errorBD($e->getMessage(), 1);
        }
        echo '1| Registro de Area (pies2) de pzas rech.  Correcto.';
        break;
    case "observaciones":
        $value = (!empty($_POST['value'])) ? trim($_POST['value']) : '';
        $edicion = (!empty($_POST['edicion'])) ? trim($_POST['edicion']) : '0';


        $log = '';

        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_rendimiento->errorBD($ErrorLog, 0);
        }
        if ($edicion == '0') {
            $DatosAbiertos = $obj_rendimiento->getRendimientoAbierto("2");
            $id =  $DatosAbiertos[0]['id'];
        } else {
            $id = $edicion;
        }
        $datos = Funciones::edicionBasica("rendimientosetiquetas", "observaciones", $value, "id", $id, $obj_rendimiento->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_materias->errorBD($e->getMessage(), 1);
        }
        echo '1| Registro de Observaciones  Correcto.';

        break;
    case "getcharcomparative":
        $anio = (!empty($_POST['anio'])) ? trim($_POST['anio']) : '';
        $response = [
            ["x", "2015", "2014", "2013", "2012", "2011", "2010"],
            ["Calzado", 250, 150, 400, 100, 200, 30],
            ["Etiquetas", 350, 250, 500, 200, 340, 130]
        ];

        //Creamos el JSON
        $json_string = json_encode($response);
        echo $json_string;
        break;
}
