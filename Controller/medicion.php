<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../include/connect_mvc.php";
include('../assets/scripts/cadenas.php');

$debug = 0;
$idUser = $_SESSION['CREident'];
$obj_medido = new Medido($debug, $idUser);
$ErrorLog = 'No se recibió';
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}
switch ($_GET["op"]) {
    case "agregarreporte":
        $reporte = (isset($_POST['reporte'])) ? ($_POST['reporte']) : array();
        $programa = (isset($_POST['programa'])) ? trim($_POST['programa']) : '';
        $folioLote = (isset($_POST['folioLote'])) ? trim($_POST['folioLote']) : '';
        Excepciones::validaLlenadoDatos(array(
            " Programa" => $programa,
            " Folio de Lote" => $folioLote
        ), $obj_medido);
        $reporte = json_decode($reporte, true);
        if (count($reporte) <= 0) {
            $obj_medido->errorBD("Error, Reporte de Teseo sin datos, notifica al departamento de Sistemas.", 1);
        }
        $ladosTotales = count($reporte);
        /********FUNCION PARA OBTENER EL AREA TOTAL DEL LOS LADOS *********/
        $funcTotalArea = function ($reporte) {
            $sumAreaDm = 0;
            $sumAreaFt = 0;
            $sumAreaRd = 0;
            foreach ($reporte as $value) {
                $sumAreaDm += $value[2];
                $sumAreaFt += $value[3];
                $sumAreaRd += $value[4];
            }
            return [$sumAreaDm, $sumAreaFt, $sumAreaRd];
        };
        /********FUNCION PARA OBTENER DETALLADO DE LOS LADOS EN SQL *********/
        $funcQuery = function ($reporte, $idLote) {
            $query = "";
            foreach ($reporte as $value) {
                $query .= "('$idLote', '{$value[0]}', '{$value[2]}',
                '{$value[3]}', '{$value[4]}', '0'),";
            }
            return substr($query, 0, -1);
        };

        $arrayAreas = $funcTotalArea($reporte);
        $obj_medido->beginTransaction();
        /* -> agregar lote  */
        $datos = $obj_medido->agregarLoteMedido($folioLote, $programa, $arrayAreas[0], $arrayAreas[1], $arrayAreas[2], $ladosTotales);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_medido->errorBD($e->getMessage(), 1);
        }
        $ident = $datos[2];
        /* -> agregar agregar detallado de lados del lote  */
        $datos = $obj_medido->agregarDetalladoLote($funcQuery($reporte, $ident));
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_medido->errorBD($e->getMessage(), 1);
        }
        $obj_medido->insertarCommit();
        echo "1|Reporte Almacenado Correctamente";

        break;
    case "getreportemedicion":
        $date_start = isset($_POST['date_start']) ? $_POST['date_start'] : '';
        $date_end = isset($_POST['date_end']) ? $_POST['date_end'] : '';
        $programa = isset($_POST['programa']) ? $_POST['programa'] : '';

        $filtradoPrograma = $programa != '' ? 'l.idCatPrograma=' . $programa . '' : '1=1';
        if($date_start!="" AND $date_end!=""){
            $filtradoFecha = "DATE_FORMAT(l.fechaReg, '%Y-%m-%d') BETWEEN '$date_start' AND '$date_end'";
        }else{
            $filtradoFecha="1=1";
        }

        $Data = $obj_medido->getReporteMedicion($filtradoFecha,  $filtradoPrograma);
        $Data = Excepciones::validaConsulta($Data);
        $response = array();
        $count = 1;
        foreach ($Data as $value) {
            $ladosTotales = $value['ladosTotales'] == '' ? '0' : $value['ladosTotales'];
            $areaTotalDM = $value['areaTotalDM'] == '' ? '0' : formatoMil($value['areaTotalDM'], 12);
            $areaTotalFT = $value['areaTotalFT'] == '' ? '0' : formatoMil($value['areaTotalFT'], 12);
            $areaTotalRd = $value['areaTotalRd'] == '' ? '0' : formatoMil($value['areaTotalRd'], 2);
            $dif= formatoMil($value['areaTotalRd']-$value['areaTotalFT'], 2);
            array_push($response, [
                $value['id'],
                $value['loteTemola'],
                $value['nPrograma'],
                $ladosTotales,
                $areaTotalDM,
                $areaTotalFT,
                $areaTotalRd,
                $dif,
                $value['id'].'|'.  $value['loteTemola'],
                $value['f_fechaReg'],
                $value['nUsuario']
            ]);
            $count++;
        }

        //Creamos el JSON
        $response = array("data" => $response);
        $json_string = json_encode($response);
        echo $json_string;
        break;
    case "getdetreporte":
        $id = isset($_POST['id']) ? $_POST['id'] : '';

        $Data = $obj_medido->getDetReporteMedicion($id);
        $Data = Excepciones::validaConsulta($Data);      
        $json_string = json_encode($Data);
        echo $json_string;
        break;
    case "eliminarlotemedicion":
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        $loteTemola = isset($_POST['loteTemola']) ? $_POST['loteTemola'] : '';

        Excepciones::validaLlenadoDatos(array(
            " Lote" => $id,
            " Folio de Lote" => $loteTemola,
        ), $obj_medido);
        $datos = $obj_medido->eliminarLoteMedido($id);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_medido->errorBD($e->getMessage(), 1);
        }
        echo "1|Eliminación Correcta del Lote: ".$loteTemola;
        break;
}
