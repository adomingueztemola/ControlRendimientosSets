<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../include/connect_mvc.php";
include('../Models/Mdl_Static.php');

$debug = 0;
$idUser = $_SESSION['CREident'];

$obj_pzas = new PzasOKNOK($debug, $idUser);

$ErrorLog = 'No se recibió';
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}

switch ($_GET["op"]) {
    case "cargajsonpzasoknok":
        if (!isset($_GET['palabraClave'])) {
            $Data = $obj_pzas->getLotesBusqClasificados();
            $Data = Excepciones::validaConsulta($Data);
        } else {
            $search = $_GET['palabraClave']; // Palabra a buscar
            $Data = $obj_pzas->getLotesBusqClasificados($search);
            $Data = Excepciones::validaConsulta($Data);
        }
        $response = array();

        // Leer la informacion
        foreach ($Data as $lote) {
            $response[] = array(
                "id" => $lote['id'],
                "text" => $lote['loteTemola']
            );
        }

        //Creamos el JSON
        $json_string = json_encode($response);
        echo $json_string;

        break;
    case "cargajsonlotes":
        $term = (isset($_POST['term'])) ? trim($_POST['term']) : '';

        $Data = $obj_pzas->getLotesXClasificar($term);
        $Data = Excepciones::validaConsulta($Data);

        //Creamos el JSON
        $json_string = json_encode($Data);
        echo $json_string;
        break;
    case "piezas":
        $codigo = (isset($_POST['codigo'])) ? trim($_POST['codigo']) : '';
        $value = (isset($_POST['value'])) ? trim($_POST['value']) : '';
        $id = (isset($_POST['id'])) ? trim($_POST['id']) : '';
        $pzasOk = (isset($_POST['pzasOk'])) ? trim($_POST['pzasOk']) : '';
        $pzasNok = (isset($_POST['pzasNok'])) ? trim($_POST['pzasNok']) : '';
        #VALIDACION DE DATOS
        Excepciones::validaLlenadoDatos(array(
            " Cantidad" => $value,
            " Tipo de Pieza" => $codigo,
            " Lote" => $id,
            " Piezas No OK" => $pzasNok,
            " Piezas OK" => $pzasOk
        ), $obj_pzas);
        $activaCampoAct = '0';
        switch ($codigo) {
            case '_12NOK':
                $campo = "_12NOK";
                $campoOK="_12";
                break;
            case '_3NOK':
                $campo = "_3NOK";
                $campoOK="_3";
                break;
            case '_9NOK':
                $campo = "_9NOK";
                $campoOK="_9";

                break;
            case '_6NOK':
                $campo = "_6NOK";
                $campoOK="_6";
                break;
            case '_6OK':
                $campo = "_6OK";
                $activaCampoAct = '1';
                break;
            case '_12OK':
                $campo = "_12OK";
                $activaCampoAct = '1';
                break;
            case '_9OK':
                $campo = "_9OK";
                $activaCampoAct = '1';
                break;
            case '_3OK':
                $campo = "_3OK";
                $activaCampoAct = '1';
                break;
            default:
                $obj_pzas->errorBD("No coincide el código, solicita ayuda al Departamento de Sistemas.", 0);
                break;
        }
        #Registramos nuevo valor de la pieza
        $datos = $obj_pzas->registrarCantidad(
            $campo,
            $value,
            $id,
            $pzasNok,
            $pzasOk,
            $activaCampoAct
        );
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_pzas->errorBD($e->getMessage(), 1);
        }
        #Actualizamos datos de piezas Ok Actual
        $datos = $obj_pzas->actualizaPzasOk(
            $campo,
            $campoOK,
            $value,
            $id
        );
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_pzas->errorBD($e->getMessage(), 1);
        } 
        echo "1|Piezas Agregadas Correctamente.";

        break;
    case "cerrarclasificacion":
        $id = (isset($_POST['id'])) ? trim($_POST['id']) : '';
        #VALIDACION DE DATOS
        Excepciones::validaLlenadoDatos(array(
            " Lote" => $id,
        ), $obj_pzas);
        $obj_pzas->beginTransaction();
        //Pasamos las piezas NOK a Scrap
        $datos = $obj_pzas->paseNOKScrap($id);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_pzas->errorBD($e->getMessage(), 1);
        }
        //Cambiamos la actualizacion de Estatus
        $datos = $obj_pzas->actualizaEstatusClasif($id);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_pzas->errorBD($e->getMessage(), 1);
        }
        $obj_pzas->insertarCommit();

        echo "1|Clasificación Cerrada Correctamente.";

        break;
}
