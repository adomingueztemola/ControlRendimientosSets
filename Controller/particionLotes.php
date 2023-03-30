<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../include/connect_mvc.php";
include('../Models/Mdl_Static.php');
include('../assets/scripts/cadenas.php');

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
    case "getreprogregistradas":
        $Data = $obj_particion->getReprogramacionLotes();
        $Data = Excepciones::validaConsulta($Data);
        $response = array();
        $count = 1;

        foreach ($Data as $value) {
            array_push($response, [
                $count,  $value['loteTemola'],
                formatoMil($value['1s'], 2), formatoMil($value['2s'], 2),
                formatoMil($value['3s'], 2),   formatoMil($value['4s'], 2), formatoMil($value['_20'], 2),
                formatoMil($value['total_s'], 2), formatoMil($value['areaProveedorLote'], 2), $value['lotePadre']
            ]);

            $count++;
        }
        //Creamos el JSON
        $response = array("data" => $response);
        $json_string = json_encode($response);
        echo $json_string;
        break;
    case "getparticionesregistradas":
        $Data = $obj_particion->getParticiones();
        $Data = Excepciones::validaConsulta($Data);
        $response = array();
        $count = 1;

        foreach ($Data as $value) {
            array_push($response, [
                $count,  $value['loteTemola'],
                formatoMil($value['1s'], 2), formatoMil($value['2s'], 2),
                formatoMil($value['3s'], 2),   formatoMil($value['4s'], 2), formatoMil($value['_20'], 2),
                formatoMil($value['total_s'], 2), formatoMil($value['areaProveedorLote'], 2), $value['lotePadre']
            ]);

            $count++;
        }
        //Creamos el JSON
        $response = array("data" => $response);
        $json_string = json_encode($response);
        echo $json_string;
        break;
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
        $cueros = $hides / 2;
        //Buscar # transferencia de los lotes
        $Data = $obj_particion->getTransferenciaLotes($lote);
        $Data = Excepciones::validaConsulta($Data);
        $numParticion = count($Data) + 1;
        // echo "Transferencia =>" . $numTransferencia;
        //Buscar lote padre de la transferencia
        $Data = $obj_particion->getDetRendimientos($lote);
        $Data = Excepciones::validaConsulta($Data);
        $Data = $Data[0];
        $total_s = $Data['total_s'] == '' ? '0' : $Data['total_s'] * 2;
        $arraycueros = array();
        $arraycueros["1s"] = $Data['1s'] == '' ? '0' : [$Data['1s'], $Data['1s'] * 2];
        $arraycueros["2s"] = $Data['2s'] == '' ? '0' : [$Data['2s'], $Data['2s'] * 2];
        $arraycueros["3s"] = $Data['3s'] == '' ? '0' : [$Data['3s'], $Data['3s'] * 2];
        $arraycueros["4s"] = $Data['4s'] == '' ? '0' : [$Data['4s'], $Data['4s'] * 2];
        $arraycueros["_20"] = $Data['_20'] == '' ? '0' : [$Data['_20'], $Data['_20'] * 2];
        $arraycueros["total_s"] = $Data['total_s'] == '' ? '0' : [$Data['total_s'], $Data['total_s'] * 2];
        // echo "Cueros identificados:";
        // echo "<br>";
        // print_r($arraycueros);
        // echo "<br>";
        //Valida que el total sea mayor a 0
        if ($arraycueros["total_s"][0] <= 0) {
            $obj_particion->errorBD("Revisa el total de lados incluidos en el lote, la cantidad es erronea", 0);
        }
        //Calculo del porcentaje de lados por calidades
        $arraycalculonew = array();
        $arraycalculosobrante = array();

        $porcent = $hides / $arraycueros["total_s"][1];
        // echo "Porcentaje =>" . $porcent;
        // echo "<br>";
        $redondLCalc = function ($calculoReal, $porcent) {
            $calculoReal = $calculoReal * $porcent;

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

        $arraycalculonew["1s"] = ($redondLCalc($arraycueros["1s"][1], $porcent));
        $arraycalculonew["2s"] = ($redondLCalc($arraycueros["2s"][1],  $porcent));
        $arraycalculonew["3s"] = ($redondLCalc($arraycueros["3s"][1],  $porcent));
        $arraycalculonew["4s"] = ($redondLCalc($arraycueros["4s"][1],  $porcent));
        $arraycalculonew["_20"] = ($redondLCalc($arraycueros["_20"][1],  $porcent));
        $arraycalculonew["total_s"] = ($redondLCalc($arraycueros["total_s"][1],  $porcent));
        // print_r($arraycalculonew);
        // echo "<br>";

        $arraycalculosobrante["1s"] = ($arraycueros["1s"][0] - $arraycalculonew['1s']);
        $arraycalculosobrante["2s"] = ($arraycueros["2s"][0] - $arraycalculonew['2s']);
        $arraycalculosobrante["3s"] = ($arraycueros["3s"][0] - $arraycalculonew['3s']);
        $arraycalculosobrante["4s"] = ($arraycueros["4s"][0] - $arraycalculonew['4s']);
        $arraycalculosobrante["_20"] = ($arraycueros["_20"][0] - $arraycalculonew['_20']);
        $arraycalculosobrante["total_s"] = ($arraycueros["total_s"][0] - $arraycalculonew['total_s']);
        // print_r($arraycalculosobrante);
        // echo "<br>";
        $fconv = function ($value) {
            return $value / 2;
        };
        $arrayConversionNew = array_map($fconv, $arraycalculonew);
        // print_r($arrayConversionNew);
        $arrayConversionSobrante = array_map($fconv, $arraycalculosobrante);
        // print_r($arrayConversionSobrante);
        /* ->Seleccion de materia prima del lote padre */
        $DataMP = $obj_particion->getMateriaPrimaXLote($lote);
        $DataMP = Excepciones::validaConsulta($DataMP);
        /* ->Valida que Lote tenga informacion de su materia prima */
        if (count($DataMP) <= 0) {
            $obj_particion->errorBD("Error, no se encuentra materia prima del lote, notifica a depto. de Sistemas", 1);
        }
        $arraycalculonew_mp = array();
        $arraycalculosobrante_mp = array();
        $obj_particion->beginTransaction();
        //ingreso del lote con el # consecutivo
        $datos = $obj_particion->agregarLoteConsecutivo(
            $lote,
            $programa,
            $numParticion,
            $arraycalculonew["total_s"],
            $arraycalculonew["1s"],
            $arraycalculonew["2s"],
            $arraycalculonew["3s"],
            $arraycalculonew["4s"],
            $arraycalculonew["_20"]
        );
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_particion->errorBD($e->getMessage(), 1);
        }
        $idInsert = $datos[2];
        //ingreso de materia prima del lote con el # consecutivo
        foreach ($DataMP as $key => $value) {
            $arraycalculonew_mp[$key] = $value;
            $arraycalculonew_mp[$key]['idRendimiento'] = $idInsert;
            $arraycalculonew_mp[$key]['total_s'] = $redondLCalc($arraycalculonew_mp[$key]['total_s'] * 2, $porcent);
            $arraycalculonew_mp[$key]['4s'] = $redondLCalc($arraycalculonew_mp[$key]['4s'] * 2, $porcent);
            $arraycalculonew_mp[$key]['3s'] = $redondLCalc($arraycalculonew_mp[$key]['3s'] * 2, $porcent);
            $arraycalculonew_mp[$key]['2s'] = $redondLCalc($arraycalculonew_mp[$key]['2s'] * 2, $porcent);
            $arraycalculonew_mp[$key]['1s'] = $redondLCalc($arraycalculonew_mp[$key]['1s'] * 2, $porcent);
            $arraycalculonew_mp[$key]['_20'] = $redondLCalc($arraycalculonew_mp[$key]['_20'] * 2, $porcent);
            $arraycalculonew_mp[$key]['areaProveedorLote'] = $arraycalculonew_mp[$key]['areaWBPromFact'] * $arraycalculonew_mp[$key]['total_s'];
            unset($arraycalculonew_mp[$key]['areaWBPromFact']);
        }
        // echo "Materia Prima Para el Nuevo";
        // echo "<br>";
        // print_r($arraycalculonew_mp);
        // echo "<br>";
        $areaProveedorLoteSobrante = 0;
        foreach ($DataMP as $key => $value) {
            $arraycalculosobrante_mp[$key] = $value;
            $arraycalculosobrante_mp[$key]['total_s'] = $value["total_s"] - $arraycalculonew_mp[$key]['total_s'];
            $arraycalculosobrante_mp[$key]['4s'] = $value["4s"] - $arraycalculonew_mp[$key]['4s'];
            $arraycalculosobrante_mp[$key]['3s'] = $value["3s"] - $arraycalculonew_mp[$key]['3s'];
            $arraycalculosobrante_mp[$key]['2s'] = $value["2s"] - $arraycalculonew_mp[$key]['2s'];
            $arraycalculosobrante_mp[$key]['1s'] = $value["1s"] - $arraycalculonew_mp[$key]['1s'];
            $arraycalculosobrante_mp[$key]['_20'] = $value["_20"] - $arraycalculonew_mp[$key]['_20'];
            $arraycalculosobrante_mp[$key]['areaProveedorLote'] = $value["areaWBPromFact"] *   $arraycalculosobrante_mp[$key]['total_s'];
            $areaProveedorLoteSobrante = $areaProveedorLoteSobrante + $arraycalculosobrante_mp[$key]['areaProveedorLote'];
            unset($arraycalculosobrante_mp[$key]['areaWBPromFact']);
        }
        // echo "Materia Prima Para el Padre";
        // echo "<br>";
        // print_r($arraycalculosobrante_mp);
        // echo "<br>";
        $query = "";
        foreach ($arraycalculonew_mp as $value) {
            $query .= "('{$value['idRendimiento']}', '{$value['idPedido']}', '{$value['total_s']}',
            '{$value['4s']}', '{$value['3s']}', '{$value['2s']}', '{$value['1s']}', '{$value['_20']}', '{$value['areaProveedorLote']}',
            NOW(), '$idUser', '2','{$value['cantFinalPedido']}' ),";
        }
        $query = substr($query, 0, -1);

        $datos =  $obj_particion->agregarMateriaPrimaNuevLote($query);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_particion->errorBD($e->getMessage(), 1);
        }
        //disminucion de pedido en bd del padre, y en registro de rendimiento
        foreach ($arraycalculosobrante_mp as $value) {
            $obj_particion->disminucionMateriaPrima(
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
        //actualizacion de lote
        $datos =  $obj_particion->actualizaLotePadre(
            $lote,
            $arraycalculosobrante["total_s"],
            $arraycalculosobrante["1s"],
            $arraycalculosobrante["2s"],
            $arraycalculosobrante["3s"],
            $arraycalculosobrante["4s"],
            $arraycalculosobrante["_20"],
            $areaProveedorLoteSobrante
        );
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_particion->errorBD($e->getMessage(), 1);
        }

        //llenado de tabla de bd: particiones
        $datos =  $obj_particion->agregarParticion(
            $idInsert,
            $lote,
            $programa,
            $numParticion,
            $arraycalculonew["total_s"],
            $arraycalculonew["1s"],
            $arraycalculonew["2s"],
            $arraycalculonew["3s"],
            $arraycalculonew["4s"],
            $arraycalculonew["_20"],
            $arraycueros["total_s"][0],
            $arraycueros["1s"][0],
            $arraycueros["2s"][0],
            $arraycueros["3s"][0],
            $arraycueros["4s"][0],
            $arraycueros["_20"][0]
        );
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_particion->errorBD($e->getMessage(), 1);
        }
        $obj_particion->insertarCommit();
        echo "1|Partición realizada satisfactoriamente";
        break;
}
