<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../include/connect_mvc.php";
include('../Models/Mdl_ConexionBD.php');
include('../Models/Mdl_ReasignacionLotesFracc.php');
include('../Models/Mdl_Static.php');
include('../Models/Mdl_Excepciones.php');
include('../Models/Mdl_OperacionLotes.php');

$debug = 0;
$idUser = $_SESSION['CREident'];

$obj_reasignacion = new ReasignacionLotesFracc($debug, $idUser);
$ErrorLog = 'No se recibió';
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}

switch ($_GET["op"]) {
    case "gettraspasosregistrados":
        
        break;
    case "traspasar":
        $lotetransmisor = (isset($_POST['lotetransmisor'])) ? trim($_POST['lotetransmisor']) : '';
        $hides = (isset($_POST['hides'])) ? trim($_POST['hides']) : '';
        $lotereceptor = (isset($_POST['lotereceptor'])) ? trim($_POST['lotereceptor']) : '';
        //Valida
        Excepciones::validaLlenadoDatos(array(
            " Lote Transmisor" => $lotetransmisor, " Hides" => $hides,
            " Lote Receptor" => $lotereceptor,
        ), $obj_reasignacion);
        /************** Buscar lote transmisor *****************/
        $Data = $obj_reasignacion->getDetRendimientos($lotetransmisor);
        $Data = Excepciones::validaConsulta($Data);
        $Data = $Data[0];
        $total_stransmisor = $Data['total_s'] == '' ? '0' : $Data['total_s'] * 2;
        $arraycuerostransmisor = array();
        $arraycuerostransmisor["1s"] = $Data['1s'] == '' ? '0' : [$Data['1s'], $Data['1s'] * 2];
        $arraycuerostransmisor["2s"] = $Data['2s'] == '' ? '0' : [$Data['2s'], $Data['2s'] * 2];
        $arraycuerostransmisor["3s"] = $Data['3s'] == '' ? '0' : [$Data['3s'], $Data['3s'] * 2];
        $arraycuerostransmisor["4s"] = $Data['4s'] == '' ? '0' : [$Data['4s'], $Data['4s'] * 2];
        $arraycuerostransmisor["_20"] = $Data['_20'] == '' ? '0' : [$Data['_20'], $Data['_20'] * 2];
        $arraycuerostransmisor["total_s"] = $Data['total_s'] == '' ? '0' : [$Data['total_s'], $Data['total_s'] * 2];
        //Valida que el total transmisor sea mayor a 0
        if ($arraycuerostransmisor["total_s"][0] <= 0) {
            $obj_reasignacion->errorBD("Revisa el total de lados incluidos en el lote transmitor, la cantidad es erronea", 0);
        }
        // echo "Datos del Lote Transmisor. <br>";
        // print_r($arraycuerostransmisor);
        // echo "<br>";
        /************** Buscar lote receptor *****************/
        $Data = $obj_reasignacion->getDetRendimientos($lotereceptor);
        $Data = Excepciones::validaConsulta($Data);
        $Data = $Data[0];
        $total_sreceptor = $Data['total_s'] == '' ? '0' : $Data['total_s'] * 2;
        $arraycuerosreceptor = array();
        $arraycuerosreceptor["1s"] = $Data['1s'] == '' ? '0' : [$Data['1s'], $Data['1s'] * 2];
        $arraycuerosreceptor["2s"] = $Data['2s'] == '' ? '0' : [$Data['2s'], $Data['2s'] * 2];
        $arraycuerosreceptor["3s"] = $Data['3s'] == '' ? '0' : [$Data['3s'], $Data['3s'] * 2];
        $arraycuerosreceptor["4s"] = $Data['4s'] == '' ? '0' : [$Data['4s'], $Data['4s'] * 2];
        $arraycuerosreceptor["_20"] = $Data['_20'] == '' ? '0' : [$Data['_20'], $Data['_20'] * 2];
        $arraycuerosreceptor["total_s"] = $Data['total_s'] == '' ? '0' : [$Data['total_s'], $Data['total_s'] * 2];
        $arraycuerosreceptor["areaProveedorLote"] = $Data['areaProveedorLote'];

        //Valida que el total transmisor sea mayor a 0
        if ($arraycuerosreceptor["total_s"][0] <= 0) {
            $obj_reasignacion->errorBD("Revisa el total de lados incluidos en el lote receptor, la cantidad es erronea", 0);
        }
        /************** Porcentajes a cambiar *****************/
        $porcentaument = $hides / $arraycuerosreceptor["total_s"][1];
        $porcentdismin = $hides / $arraycuerostransmisor["total_s"][1];
        /************** Redondeo a Calculo *****************/
        $redondAumCalc = function ($calculoReal, $porcent) {
            $calculoReal = $calculoReal * (1 + $porcent);
            $modCalculo = $calculoReal % 1;
            $intCalculo = $calculoReal - $modCalculo;

            if ($modCalculo > 0.5) {
                $resp = $intCalculo + 1;
            } else if ($modCalculo <= 0.5) {
                $resp = $intCalculo;
            }
            $resp = $resp / 2;
            return $resp;
        };
        $redondDismCalc = function ($calculoReal, $porcent) {
            $calculoReal = $calculoReal - ($calculoReal * ($porcent));
            $modCalculo = $calculoReal % 1;
            $intCalculo = $calculoReal - $modCalculo;

            if ($modCalculo > 0.5) {
                $resp = $intCalculo + 1;
            } else if ($modCalculo <= 0.5) {
                $resp = $intCalculo;
            }
            $resp = $resp / 2;
            return $resp;
        };
        $arraycalculoaumento = array();

        $arraycalculoaumento["1s"] = ($redondAumCalc($arraycuerosreceptor["1s"][1], $porcentaument));
        $arraycalculoaumento["2s"] = ($redondAumCalc($arraycuerosreceptor["2s"][1],  $porcentaument));
        $arraycalculoaumento["3s"] = ($redondAumCalc($arraycuerosreceptor["3s"][1],  $porcentaument));
        $arraycalculoaumento["4s"] = ($redondAumCalc($arraycuerosreceptor["4s"][1],  $porcentaument));
        $arraycalculoaumento["_20"] = ($redondAumCalc($arraycuerosreceptor["_20"][1],  $porcentaument));
        $arraycalculoaumento["total_s"] = ($redondAumCalc($arraycuerosreceptor["total_s"][1],  $porcentaument));
        // echo "Calculo de Aumento<br>";
        // print_r($arraycalculoaumento);
        // echo "<br>";

        $arraycalculodism = array();

        $arraycalculodism["1s"] = ($redondDismCalc($arraycuerostransmisor["1s"][1], $porcentdismin));
        $arraycalculodism["2s"] = ($redondDismCalc($arraycuerostransmisor["2s"][1],  $porcentdismin));
        $arraycalculodism["3s"] = ($redondDismCalc($arraycuerostransmisor["3s"][1],  $porcentdismin));
        $arraycalculodism["4s"] = ($redondDismCalc($arraycuerostransmisor["4s"][1],  $porcentdismin));
        $arraycalculodism["_20"] = ($redondDismCalc($arraycuerostransmisor["_20"][1],  $porcentdismin));
        $arraycalculodism["total_s"] = ($redondDismCalc($arraycuerostransmisor["total_s"][1],  $porcentdismin));
        // echo "Calculo de Disminucion<br>";
        // print_r($arraycalculodism);
        // echo "<br>";

        /*->Disminucion de materia prima de lote transmisor*/
        $DataMP = $obj_reasignacion->getMateriaPrimaXLote($lotetransmisor);
        $DataMP = Excepciones::validaConsulta($DataMP);
        /* ->Valida que Lote tenga informacion de su materia prima */
        if (count($DataMP) <= 0) {
            $obj_reasignacion->errorBD("Error, no se encuentra materia prima del lote, notifica a depto. de Sistemas", 1);
        }
        // echo "Detalle de Pedido: <br>";
        // print_r($DataMP);
        $arraycalculosobrante_mp = array();
        $arraycalculopase_mp = array();
        $areaProveedorLoteAum = 0;
        $areaProveedorLoteSobrante = 0;
        foreach ($DataMP as $key => $value) {

            /*->Nuevo Registro de MP para lote receptor*/
            $arraycalculopase_mp[$key] = $value;
            $arraycalculopase_mp[$key]['idRendimiento'] = $lotereceptor;
            $arraycalculopase_mp[$key]['total_s'] = $redondDismCalc(($value["total_s"] * 2), $porcentdismin);
            $arraycalculopase_mp[$key]['4s'] = $redondDismCalc(($value["4s"] * 2), $porcentdismin);
            $arraycalculopase_mp[$key]['3s'] =  $redondDismCalc(($value["3s"] * 2), $porcentdismin);
            $arraycalculopase_mp[$key]['2s'] = $redondDismCalc(($value["2s"] * 2), $porcentdismin);
            $arraycalculopase_mp[$key]['1s'] =  $redondDismCalc(($value["1s"] * 2), $porcentdismin);
            $arraycalculopase_mp[$key]['_20'] =  $redondDismCalc(($value["_20"] * 2), $porcentdismin);
            $arraycalculopase_mp[$key]['areaProveedorLote'] = $value["areaWBPromFact"] *   $arraycalculopase_mp[$key]['total_s'];
            $areaProveedorLoteAum = $areaProveedorLoteAum + $arraycalculopase_mp[$key]['areaProveedorLote'];
            unset($arraycalculopase_mp[$key]['areaWBPromFact']);
            unset($arraycalculopase_mp[$key]['id']);

            $arraycalculosobrante_mp[$key] = $value;
            $arraycalculosobrante_mp[$key]['total_s'] = $value["total_s"] -  $arraycalculopase_mp[$key]['total_s'];
            $arraycalculosobrante_mp[$key]['4s'] = $value["4s"] -  $arraycalculopase_mp[$key]['4s'];
            $arraycalculosobrante_mp[$key]['3s'] = $value["3s"] - $arraycalculopase_mp[$key]['3s'];
            $arraycalculosobrante_mp[$key]['2s'] = $value["2s"] - $arraycalculopase_mp[$key]['2s'];
            $arraycalculosobrante_mp[$key]['1s'] = $value["1s"] - $arraycalculopase_mp[$key]['1s'];
            $arraycalculosobrante_mp[$key]['_20'] = $value["_20"] - $arraycalculopase_mp[$key]['_20'];
            $arraycalculosobrante_mp[$key]['areaProveedorLote'] = $value["areaWBPromFact"] *   $arraycalculosobrante_mp[$key]['total_s'];
            $areaProveedorLoteSobrante = $areaProveedorLoteSobrante + $arraycalculosobrante_mp[$key]['areaProveedorLote'];
            unset($arraycalculosobrante_mp[$key]['areaWBPromFact']);
        }
        // echo "<br>";
        // echo "Calculo de Materia Prima Sobrante<br>";
        // print_r($arraycalculosobrante_mp);
        // echo "<br>";
        // echo "Calculo de Materia Prima Pase<br>";
        // print_r($arraycalculopase_mp);
        // echo "<br>";
        $obj_reasignacion->beginTransaction();
        //disminucion de pedido en bd del lote trasnsmisor, y en registro de rendimiento
        foreach ($arraycalculosobrante_mp as $value) {
            $obj_reasignacion->disminucionMateriaPrima(
                $value['id'],
                $value['total_s'],
                $value['1s'],
                $value['2s'],
                $value['3s'],
                $value['4s'],
                $value['_20'],
                $value['areaProveedorLote']
            );
        }
        //aumento de pedido en bd del lote receptor, y en registro de rendimiento
        $query = "";
        foreach ($arraycalculopase_mp as $value) {
            $query .= "('{$value['idRendimiento']}', '{$value['idPedido']}', '{$value['total_s']}',
            '{$value['4s']}', '{$value['3s']}', '{$value['2s']}', '{$value['1s']}', '{$value['_20']}', '{$value['areaProveedorLote']}',
            NOW(), '$idUser', '2','{$value['cantFinalPedido']}' ),";
        }
        $query = substr($query, 0, -1);
        $datos =  $obj_reasignacion->agregarMateriaPrima($query);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_reasignacion->errorBD($e->getMessage(), 1);
        }
        //actualizacion del lote transmisor
        $datos =  $obj_reasignacion->actualizaLote(
            $lotetransmisor,
            $arraycalculodism["total_s"],
            $arraycalculodism["1s"],
            $arraycalculodism["2s"],
            $arraycalculodism["3s"],
            $arraycalculodism["4s"],
            $arraycalculodism["_20"],
            $areaProveedorLoteSobrante
        );
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_particion->errorBD($e->getMessage(), 1);
        }
        //actualizacion del lote receptor 
        $datos =  $obj_reasignacion->actualizaLote(
            $lotereceptor,
            $arraycuerosreceptor['total_s'][0] + $arraycalculoaumento["total_s"],
            $arraycuerosreceptor['1s'][0] + $arraycalculoaumento["1s"],
            $arraycuerosreceptor['2s'][0] + $arraycalculoaumento["2s"],
            $arraycuerosreceptor['3s'][0] + $arraycalculoaumento["3s"],
            $arraycuerosreceptor['4s'][0] + $arraycalculoaumento["4s"],
            $arraycuerosreceptor['_20'][0] + $arraycalculoaumento["_20"],
            $arraycuerosreceptor['areaProveedorLote'] + $areaProveedorLoteAum
        );
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_reasignacion->errorBD($e->getMessage(), 1);
        }
        //registro de operacion
        $datos =  $obj_reasignacion->registroTraspaso(
            $lotetransmisor,
            $arraycalculodism["total_s"],
            $arraycalculodism["1s"],
            $arraycalculodism["2s"],
            $arraycalculodism["3s"],
            $arraycalculodism["4s"],
            $arraycalculodism["_20"],
            $lotereceptor,
            $arraycuerosreceptor['total_s'][0] + $arraycalculoaumento["total_s"],
            $arraycuerosreceptor['1s'][0] + $arraycalculoaumento["1s"],
            $arraycuerosreceptor['2s'][0] + $arraycalculoaumento["2s"],
            $arraycuerosreceptor['3s'][0] + $arraycalculoaumento["3s"],
            $arraycuerosreceptor['4s'][0] + $arraycalculoaumento["4s"],
            $arraycuerosreceptor['_20'][0] + $arraycalculoaumento["_20"],
            $arraycuerosreceptor['areaProveedorLote'] + $areaProveedorLoteAum
        );
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_reasignacion->errorBD($e->getMessage(), 1);
        }

        echo "1|Traspaso de Hides Realizado Satisfactoriamente.";
        break;
}
