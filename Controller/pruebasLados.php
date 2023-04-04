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
        $fechaInit = date('Y-m-01');
        $fechaFinal = date('Y-m-t');

        $filtradoFecha = "p.fecha BETWEEN '$fechaInit' AND '$fechaFinal'";
        $Data = $obj_pruebas->getPruebasHeads($filtradoFecha);
        $Data = Excepciones::validaConsulta($Data);
        $response = array();
        $count = 1;
        foreach ($Data as $value) {
            array_push($response, [
                $value['semanaAnio'],
                $value['loteTemola'], $value['hides'], formatoMil($value['1s'] * 2, 0), formatoMil($value['2s'] * 2, 0),
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

        //CONSULTA DATOS DEL LOTE
        $Data = $obj_pruebas->getDetRendimientos($lote);
        $Data = Excepciones::validaConsulta($Data);
        $Data = $Data[0];
        $arraycueros = array();
        $arraycueros["1s"] = $Data['1s'] == '' ? '0' : [$Data['1s'], $Data['1s'] * 2];
        $arraycueros["2s"] = $Data['2s'] == '' ? '0' : [$Data['2s'], $Data['2s'] * 2];
        $arraycueros["3s"] = $Data['3s'] == '' ? '0' : [$Data['3s'], $Data['3s'] * 2];
        $arraycueros["4s"] = $Data['4s'] == '' ? '0' : [$Data['4s'], $Data['4s'] * 2];
        $arraycueros["_20"] = $Data['_20'] == '' ? '0' : [$Data['_20'], $Data['_20'] * 2];
        $arraycueros["total_s"] = $Data['total_s'] == '' ? '0' : [$Data['total_s'], $Data['total_s'] * 2];
        if ($arraycueros["total_s"][0] <= 0) {
            $obj_pruebas->errorBD("Revisa el total de lados incluidos en el lote, la cantidad es erronea", 0);
        }
        //PORCENTAJE A DESCONTAR
        $porcent = $hides / $arraycueros["total_s"][1];
        // echo $porcent . '%<br>';
        //CALCULO DE LADO->CUEROS ENTEROS DEL DESCUENTO
        $redondLCalc = function ($calculoReal, $porcent) {

            $calculoReal = $calculoReal - ($calculoReal * $porcent);

            $modCalculo = fmod($calculoReal, 1);
            $intCalculo = $calculoReal - $modCalculo;

            if ($modCalculo > 0.5) {
                $resp = $intCalculo + 1;
            } else if ($modCalculo <= 0.5) {
                $resp = $intCalculo;
            }
            $resp = $resp / 2;
            return $resp;
        };
        $arraycalculosobr = array();
        $arraycalculosobr["1s"] = ($redondLCalc($arraycueros["1s"][1], $porcent));
        $arraycalculosobr["2s"] = ($redondLCalc($arraycueros["2s"][1],  $porcent));
        $arraycalculosobr["3s"] = ($redondLCalc($arraycueros["3s"][1],  $porcent));
        $arraycalculosobr["4s"] = ($redondLCalc($arraycueros["4s"][1],  $porcent));
        $arraycalculosobr["_20"] = ($redondLCalc($arraycueros["_20"][1],  $porcent));
        $arraycalculosobr["total_s"] = ($redondLCalc($arraycueros["total_s"][1],  $porcent));

        // echo "El Calculo de sobra es: ";
        // print_r($arraycalculosobr);
        // echo "<br>";

        /* ->Seleccion de materia prima del lote muestral */
        $DataMP = $obj_pruebas->getMateriaPrimaXLote($lote);
        $DataMP = Excepciones::validaConsulta($DataMP);
        /* ->Valida que Lote tenga informacion de su materia prima */
        if (count($DataMP) <= 0) {
            $obj_pruebas->errorBD("Error, no se encuentra materia prima del lote, notifica a depto. de Sistemas", 1);
        }
        // echo "MATERIA PRIMA: ";
        // print_r($DataMP);
        // echo "<br>";
        $arraycalculosobrante_mp = array();
        foreach ($DataMP as $key => $value) {
            $arraycalculosobrante_mp[$key] = $value;
            $arraycalculosobrante_mp[$key]['total_s'] = ($redondLCalc(($value["total_s"] * 2), $porcent));
            $arraycalculosobrante_mp[$key]['4s'] = ($redondLCalc(($value["4s"] * 2), $porcent));
            $arraycalculosobrante_mp[$key]['3s'] = ($redondLCalc(($value["3s"] * 2), $porcent));
            $arraycalculosobrante_mp[$key]['2s'] = ($redondLCalc(($value["2s"] * 2), $porcent));
            $arraycalculosobrante_mp[$key]['1s'] = ($redondLCalc(($value["1s"] * 2), $porcent));
            $arraycalculosobrante_mp[$key]['_20'] = ($redondLCalc(($value["_20"] * 2), $porcent));
            $arraycalculosobrante_mp[$key]['areaProveedorLote'] = $value["areaWBPromFact"] *   $arraycalculosobrante_mp[$key]['total_s'];
            $areaProveedorLoteSobrante = $areaProveedorLoteSobrante + $arraycalculosobrante_mp[$key]['areaProveedorLote'];
            unset($arraycalculosobrante_mp[$key]['areaWBPromFact']);
        }
        // echo "CALCULO SOBRANTE MP: ";
        // print_r($arraycalculosobrante_mp);
        // echo "<br>";
        $obj_pruebas->beginTransaction();

        //disminucion de pedido en bd del padre, y en registro de rendimiento
        foreach ($arraycalculosobrante_mp as $value) {
            $datos = $obj_pruebas->disminucionMateriaPrima(
                $value['id'],
                $value['total_s'],
                $value['1s'],
                $value['2s'],
                $value['3s'],
                $value['4s'],
                $value['_20'],
                $value['areaProveedorLote']
            );
            try {
                Excepciones::validaMsjError($datos);
            } catch (Exception $e) {
                $obj_pruebas->errorBD($e->getMessage(), 1);
            }
        }
        //actualizacion de lote
        $datos =  $obj_pruebas->actualizaLoteMuestral(
            $lote,
            $arraycalculosobr["total_s"],
            $arraycalculosobr["1s"],
            $arraycalculosobr["2s"],
            $arraycalculosobr["3s"],
            $arraycalculosobr["4s"],
            $arraycalculosobr["_20"],
            $areaProveedorLoteSobrante
        );
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_pruebas->errorBD($e->getMessage(), 1);
        }
        //actualizacion de lote
        $datos =  $obj_pruebas->agregarRegistroPruebasHides(
            $lote,
            $fecha,
            $hides,
            $arraycalculosobr["total_s"],
            $arraycalculosobr["1s"],
            $arraycalculosobr["2s"],
            $arraycalculosobr["3s"],
            $arraycalculosobr["4s"],
            $arraycalculosobr["_20"],
            $porcent,
            $areaProveedorLoteSobrante
        );
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_pruebas->errorBD($e->getMessage(), 1);
        }

        #AGREGAR PRUEBAS DE HIDE 
        /* $datos = $obj_pruebas->agregarPruebaHide($lote, $fecha, $hides);
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
   ;*/
        $obj_pruebas->insertarCommit();
        echo '1|Prueba de Hides Almacenada Correctamente.';
        break;
}
