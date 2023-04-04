<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../include/connect_mvc.php";
include('../Models/Mdl_Static.php');
include('../assets/scripts/cadenas.php');

$debug = 0;
$idUser = $_SESSION['CREident'];

$obj_pruebas = new PruebaHide($debug, $idUser);

$ErrorLog = 'No se recibió';
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}

switch ($_GET["op"]) {
    case "getpruebasregistradas":
        $Data = $obj_pruebas->getPruebasHeads();
        $Data = Excepciones::validaConsulta($Data);
        $response = array();
        $count = 1;
        foreach ($Data as $value) {
            array_push($response, [
                $count,  $value['semanaAnio'], $value['fFecha'],
                $value['loteTemola'], formatoMil($value['1s'] * 2, 2), formatoMil($value['2s'] * 2, 2),
                formatoMil($value['3s'] * 2, 2),   formatoMil($value['4s'] * 2, 2), formatoMil($value['_20'] * 2, 2),
                formatoMil($value['total_s'] * 2, 2),
                $value['hides'], formatoMil($value['porcent'] * 100, 2) . '%'
            ]);
            $count++;
        }
        //Creamos el JSON
        $response = array("data" => $response);
        $json_string = json_encode($response);
        echo $json_string;
        break;
    case "getpruebassemana":
        $fechaInit= date('Y-m-01');
        $fechaFinal= date('Y-m-t');

        $filtradoFecha= "p.fecha BETWEEN '$fechaInit' AND '$fechaFinal'";
        $Data = $obj_pruebas->getPruebasHeads($filtradoFecha);
        $Data = Excepciones::validaConsulta($Data);
        $response = array();
        $count = 1;
        foreach ($Data as $value) {
            array_push($response, [
                $value['semanaAnio'], 
                $value['loteTemola'],$value['hides'], formatoMil($value['1s'] * 2, 0), formatoMil($value['2s'] * 2, 0),
                formatoMil($value['3s'] * 2, 0),   formatoMil($value['4s'] * 2, 0), formatoMil($value['_20'] * 2, 0),
                formatoMil($value['total_s'] * 2, 0)
            ]);
            $count++;
        }
        //Creamos el JSON
        $response = array("data" => $response);
        $json_string = json_encode($response);
        echo $json_string;
        break;
    case "detalleslote":
        $ident = (isset($_POST['ident'])) ? trim($_POST['ident']) : '';
        $Data = $obj_pruebas->getDetRendimientos($ident);
        $Data = Excepciones::validaConsulta($Data);
        $json_string = json_encode($Data[0]);
        echo $json_string;
        break;
    case "agregarpruebas":
        $lote = (isset($_POST['lote'])) ? trim($_POST['lote']) : '';
        $fecha = (isset($_POST['fecha'])) ? trim($_POST['fecha']) : '';
        $hides = (isset($_POST['hides'])) ? trim($_POST['hides']) : '';
        #VALIDACION DE DATOS
        Excepciones::validaLlenadoDatos(array(
            " Cantidad" => $hides,
            " Fecha" => $fecha,
            " Lote" => $lote,
        ), $obj_pruebas);
        $obj_pruebas->beginTransaction();
        
        #AGREGAR PRUEBAS DE HIDE 
        $datos = $obj_pruebas->agregarPruebaHide($lote, $fecha, $hides);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_pruebas->errorBD($e->getMessage(), 1);
        }

        #RECALCULAR RENDIMIENTO                                                                
        $datos = $obj_pruebas->calcularRendimientoEnPrueba($lote);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_pruebas->errorBD($e->getMessage(), 1);
        }
        $obj_pruebas->insertarCommit();
        echo '1|Prueba de Hides Almacenada Correctamente.';
        break;
}
