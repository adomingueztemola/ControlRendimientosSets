<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once '../PHPExcel/Classes/PHPExcel.php';
require_once "archivos.php";

require_once "../include/connect_mvc.php";

$debug = 1;
$idUser = $_SESSION['CREident'];

$obj_scrap = new Scrap($debug, $idUser);
$obj_LeerXLS = new LecturaXLSScrap($debug, $idUser);

$ErrorLog = 'No se recibió';
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}
switch ($_GET["op"]) {
    case "lecturareporte":
        if ($_FILES['file']['name'] != null) {
            if ($_FILES["file"]["error"] > 0) {
                $obj_scrap->errorBD('No se pudo cargar tu archivo, por favor Intentalo nuevamente.', 0);
            }
            if ($debug == 1) {
                echo "<br><hr><br>PROPIEDADES DE XLS:";
                echo "Tipo: " . $_FILES['file']['type'] . "<br>";
                echo "Tamaño: " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
                echo "Carpeta temporal: " . $_FILES['file']['tmp_name'] . " <br>";
                echo '<br><br>';
            }
            try {
                $dataExcel = $obj_LeerXLS->leerXLS($_FILES['file']['tmp_name']);
                if ($dataExcel['error'] != 0) {
                    $obj_scrap->errorBD('Tuvimos problemas al leer el archivo: ' . $dataExcel['msj'], 0);
                }
            } catch (Exception $e) {
                $obj_scrap->errorBD('Tuvimos problemas al leer el archivo: ' . $e->getMessage(), 0);
            }
            if ($dataExcel['gral']['filasIncompletas'] <= 0) {
                $date = date("YmdHis");
                $respuesta = guardarPDF($_FILES['file'], "../doctos/Exceles/" . $idUser . "/", "doctos/Exceles/" . $idUser . "/", "Excel_Scrap_" . $idUser);
                $ArrayRespuestaImg = explode('|', $respuesta);
                if ($ArrayRespuestaImg['0'] == '0') {
                    errorBD("El Archivo tuvo problemas al almacenarse, notifica a tu Administrador.", 0);
                }
                $_SESSION['dataExcelScrap'] = $dataExcel;
                $_SESSION['dataExcelScrap']['gral']['doctoserver'] = $ArrayRespuestaImg['2'];
                echo '1|Alineación alistada correctamente.';
            } else {
                $_SESSION['dataExcelScrap'] = $dataExcel;

                $obj_scrap->errorBD('Formato de Alineación Incorrecto.', 0);
            }
        }
        break;
    
        $arrayReporte = (isset($_POST['arrayReporte'])) ? ($_POST['arrayReporte']) : '';
        $fechaSalida = (isset($_POST['fechaSalida'])) ? ($_POST['fechaSalida']) : '';

        Excepciones::validaLlenadoDatos(array(
            " Alineación" => $arrayReporte,
            " Fecha Salida" => $fechaSalida
        ), $obj_scrap);

        if (count($arrayReporte) <= 0) {
            $obj_scrap->errorBD("Error, Reporte de Baja Scrap sin datos, notifica al departamento de Sistemas.", 1);
        }
        //DATOS SUMATORIAS DEL REPORTE
        $suma_12 = 0;
        $suma_3 = 0;
        $suma_6 = 0;
        $suma_9 = 0;
        $pzas_totales = 0;
        foreach ($arrayReporte as $value) {
            $suma_12 += $value['_12'];
            $suma_3 += $value['_3'];
            $suma_6 += $value['_6'];
            $suma_9 += $value['_9'];
            $pzas_totales += $value['total'];
        }
        $obj_scrap->insertarCommit();
        //AGREGAR TARIMA DATOS PRELIMINARES
        $datos = $obj_scrap->agregarTarima(
            $fechaSalida,
            $pzas_totales,
            $suma_12,
            $suma_6,
            $suma_3,
            $suma_9
        );
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_scrap->errorBD($e->getMessage(), 1);
        }
        $idTarima = $datos[2];
        //CICLO QUE INGRESA LOG DE BAJA/DISMINUCION A CEROS DE INVENTARIOS DE SCRAP
        foreach ($arrayReporte as $value) {
            //aGREGAR DETALLADO DE TARIMA 
            $datos = $obj_scrap->agregarLogReporte(
                $idTarima,
                $value['idRendimiento'],
                $value['_12'],
                $value['_3'],
                $value['_6'],
                $value['_9'],
                $value['_12Scrap'],
                $value['_3Scrap'],
                $value['_6Scrap'],
                $value['_9Scrap'],
                $value['totalScrap'],
                $value['total']
            );
            try {
                Excepciones::validaMsjError($datos);
            } catch (Exception $e) {
                $obj_scrap->errorBD($e->getMessage(), 1);
            }
            //ACTUALIZAR STOCK DE RECHAZOS   
            $datos = $obj_scrap->actualizarStkRech($value['idStk']);
            try {
                Excepciones::validaMsjError($datos);
            } catch (Exception $e) {
                $obj_scrap->errorBD($e->getMessage(), 1);
            }
            //ACTUALIZAR ESTADO DE SCRAP DE LOTE    
            $datos = $obj_scrap->actualizarRendimiento($value['idRendimiento']);
            try {
                Excepciones::validaMsjError($datos);
            } catch (Exception $e) {
                $obj_scrap->errorBD($e->getMessage(), 1);
            }
        }
        $obj_scrap->beginTransaction();
        echo '1|Reporte Almacenado Correctamente.|'.$idTarima;
        break;
}
