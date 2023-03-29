<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../include/connect_mvc.php";
include('../Models/Mdl_Static.php');

$debug = 0;
$idUser = $_SESSION['CREident'];

$obj_particion = new ParticionLote($debug, $idUser);

$ErrorLog = 'No se recibió';
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}

switch ($_GET["op"]) {
    case "agregarparticion":
        $lote = (isset($_POST['lote'])) ? trim($_POST['lote']) : '';
        $programa = (isset($_POST['programa'])) ? trim($_POST['programa']) : '';
        $hides = (isset($_POST['hides'])) ? trim($_POST['hides']) : '';
        #VALIDACION DE DATOS
        Excepciones::validaLlenadoDatos(array(
            " Lote" => $lote,
            " Programa" => $programa,
            " Hides" => $hides
        ), $obj_particion);
        $cueros= $hides/2;
        //Buscar # transferencia de los lotes
        $Data= $obj_particion->getTransferenciaLotes($lote);
        $Data= Excepciones::validaConsulta($Data);
        $numTransferencia = count($Data)+1;
        echo "Transferencia =>".$numTransferencia;
        //Buscar lote padre de la transferencia
        $Data= $obj_particion->getDetRendimientos($lote);
        $Data= Excepciones::validaConsulta($Data);
        $Data= $Data[0];
        $total_s=$Data['total_s'] ==''?'0': $Data['total_s']*2;
        $arraycueros=array();
        $arraycueros["1s"]=$Data['1s'] ==''?'0': $Data['1s']*2;
        $arraycueros["2s"]=$Data['2s'] ==''?'0': $Data['2s']*2;
        $arraycueros["3s"]=$Data['3s'] ==''?'0': $Data['3s']*2;
        $arraycueros["4s"]=$Data['4s'] ==''?'0': $Data['4s']*2;
        $_20=$Data['_20'] ==''?'0': $Data['_20']*2;
        //Valida que el total sea mayor a 0
        if($total_s<=0){
            $obj_particion->errorBD("Revisa el total de lados incluidos en el lote, la cantidad es erronea",0);
        }
        //Calculo del porcentaje de lados por calidades
        $arraycalculonew=array();
        $arraycalculosobrante=array();

        $porcent=$hides/$total_s;
        echo "Porcentaje =>".$porcent;
        echo "<br>";
        $redondLCalc=function ($calculoReal){
            $modCalculo=$calculoReal%1;
            $intCalculo =$calculoReal-$modCalculo;
           if($modCalculo>0.5){
               $resp= $intCalculo+1;     
           }else if($modCalculo<=0.5){
            $resp= $intCalculo;     
    
           }
           return $resp;
        };

        $arraycalculonew["1s"]=( $redondLCalc($_1s*$porcent));
        $arraycalculonew["2s"]=( $redondLCalc($_2s*$porcent));
        $arraycalculonew["3s"]=( $redondLCalc($_3s*$porcent));
        $arraycalculonew["4s"]=( $redondLCalc($_4s*$porcent));
        $arraycalculonew["_20"]=( $redondLCalc($_20*$porcent));
        $arraycalculonew["total_s"]=( $redondLCalc($total_s*$porcent));
        print_r($arraycalculonew);
        echo "<br>";

        $arraycalculosobrante["1s"]=$_1s-$arraycalculonew['1s'];
        $arraycalculosobrante["2s"]=$_2s-$arraycalculonew['2s'];
        $arraycalculosobrante["3s"]=$_3s-$arraycalculonew['3s'];
        $arraycalculosobrante["4s"]=$_4s-$arraycalculonew['4s'];
        $arraycalculosobrante["_20"]=$_20-$arraycalculonew['_20'];
        $arraycalculosobrante["total_s"]=$total_s-$arraycalculonew['total_s'];
        print_r($arraycalculosobrante);
        echo "<br>";
        $fconv= function ($value){
            return $value/2;
        };
        $arrayConversionNew= array_map( $fconv, $arraycalculonew);
        print_r($arrayConversionNew);
        $arrayConversionSobrante= array_map( $fconv, $arraycalculosobrante);
        print_r($arrayConversionSobrante);
        //llenado de tabla de bd: particiones

        //disminucion de pedido en bd del padre

        //ingreso del lote con el # consecutivo



        break;
}


function conversionHidesCueros($value){
    return $value/2;
}
