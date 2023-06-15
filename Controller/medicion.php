<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../include/connect_mvc.php";
include('../assets/scripts/cadenas.php');
include('../Models/Mdl_Static.php');

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
        $grosor = (isset($_POST['grosor'])) ? trim($_POST['grosor']) : '';

        $folioLote = (isset($_POST['folioLote'])) ? trim($_POST['folioLote']) : '';
        Excepciones::validaLlenadoDatos(array(
            " Programa" => $programa,
            " Grosor" => $grosor,
            " Folio de Lote" => $folioLote
        ), $obj_medido);
        #Valida que Producto no exista en el Catalogo
        $resultValidacion = Funciones::validarDatoTabla("lotesmediciones", "loteTemola", $folioLote, $debug, $obj_medido->getConexion());
        try {
            Excepciones::validaMsjError($resultValidacion);
        } catch (Exception $e) {
            $obj_medido->errorBD($e->getMessage(), 1);
        }
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
                '{$value[3]}', '{$value[4]}', '1'),";
            }
            return substr($query, 0, -1);
        };

        $arrayAreas = $funcTotalArea($reporte);
        $obj_medido->beginTransaction();
        /* -> agregar lote  */
        $datos = $obj_medido->agregarLoteMedido($folioLote, $programa, $arrayAreas[0], $arrayAreas[1], $arrayAreas[2], $ladosTotales, $grosor);
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
        if ($date_start != "" and $date_end != "") {
            $filtradoFecha = "DATE_FORMAT(l.fechaReg, '%Y-%m-%d') BETWEEN '$date_start' AND '$date_end'";
        } else {
            $filtradoFecha = "1=1";
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
            $dif = formatoMil($value['areaTotalRd'] - $value['areaTotalFT'], 2);
            array_push($response, [
                $value['id'],
                $value['loteTemola'],
                $value['nPrograma'],
                $value['nGrosor'],
                $ladosTotales,
                $areaTotalDM,
                $areaTotalFT,
                $areaTotalRd,
                $dif,
                $value['id'] . '|' .  $value['loteTemola'],
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
        echo "1|Eliminación Correcta del Lote: " . $loteTemola;
        break;
    case "select2lotes":
        if (!isset($_POST['palabraClave'])) {
            $Data = $obj_medido->getLotesSelect2();
            $Data = Excepciones::validaConsulta($Data);
        } else {
            $search = $_POST['palabraClave']; // Palabra a buscar
            $Data = $obj_medido->getLotesSelect2($search);
            $Data = Excepciones::validaConsulta($Data);
        }
        //Creamos el JSON
        $json_string = json_encode($Data);
        echo $json_string;
        break;
    case "select2grosor":
        if (!isset($_POST['palabraClave'])) {
            $Data = $obj_medido->getGrosorSelect2();
            $Data = Excepciones::validaConsulta($Data);
        } else {
            $search = $_POST['palabraClave']; // Palabra a buscar
            $Data = $obj_medido->getGrosorSelect2($search);
            $Data = Excepciones::validaConsulta($Data);
        }
        //Creamos el JSON
        $json_string = json_encode($Data);
        echo $json_string;
        break;
    case "getladosxlote":
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        $Data = $obj_medido->getLadosDisp($id);
        $Data = Excepciones::validaConsulta($Data);
        $json_string = json_encode($Data);
        echo $json_string;
        break;
    case "getselecciones":
        $Data = $obj_medido->getSelecciones();
        $Data = Excepciones::validaConsulta($Data);
        $json_string = json_encode($Data);
        echo $json_string;
        break;
    case "cambiarseleccion":
        $id = (isset($_POST['id'])) ? $_POST['id'] : '';
        $seleccion = (isset($_POST['seleccion'])) ? $_POST['seleccion'] : '';

        Excepciones::validaLlenadoDatos(array(
            " Lado" => $id,
            " Seleccion" => $seleccion
        ), $obj_medido);
        $datos = $obj_medido->cambiarSeleccionLado($id, $seleccion);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_medido->errorBD($e->getMessage(), 1);
        }
        echo "1|Selección Almacenada Correctamente";

        break;
    case "agregarpaquete":
        $ladosPack = (isset($_POST['ladosPack'])) ? $_POST['ladosPack'] : '';
        $id = (isset($_POST['id'])) ? $_POST['id'] : '';

        Excepciones::validaLlenadoDatos(array(
            " Lados" => $ladosPack,
            " Lote" => $id
        ), $obj_medido);
        $totalLados = count($ladosPack);
        if ($totalLados <= 0) {
            $obj_medido->errorBD("Selecciona Lados para agregar el paquete", 0);
        }
        //Consulta datos de los lados
        $Data = $obj_medido->getDetalleLados($ladosPack);
        $Data = Excepciones::validaConsulta($Data);
        $areaTotalDM = 0;
        $areaTotalFT = 0;
        $areaTotalRd = 0;
        foreach ($Data as $value) {
            $areaTotalDM += $value["areaDM"];
            $areaTotalFT += $value["areaFT"];
            $areaTotalRd += $value["areaRedondFT"];
        }
        //Consulta numero de paquete a realizar
        $Data = $obj_medido->getNumPaquete($id);
        $Data = Excepciones::validaConsulta($Data);
        $abierto = $Data["abierto"];
        $numPaquete = $Data["numPaquete"];

        $obj_medido->beginTransaction();
        //Crear Paquete 
        $datos = $obj_medido->agregarPaquete($id, $numPaquete, $areaTotalDM, $areaTotalFT, $areaTotalRd, $totalLados);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_medido->errorBD($e->getMessage(), 1);
        }
        $idPaq = $datos[2];
        //Crear detallado de paquete
        $count = 1;
        foreach ($ladosPack as $value) {
            $datos = $obj_medido->agregarDetPaquete($value, $idPaq, $count);
            try {
                Excepciones::validaMsjError($datos);
            } catch (Exception $e) {
                $obj_medido->errorBD($e->getMessage(), 1);
            }
            $count++;
        }
        //cierre de paquete abierto
        if ($abierto == '1') {
            $datos = $obj_medido->eliminarNumPaqDlt($id);
            try {
                Excepciones::validaMsjError($datos);
            } catch (Exception $e) {
                $obj_medido->errorBD($e->getMessage(), 1);
            }
        }
        $obj_medido->insertarCommit();
        echo "1|Paquete Almacenado Correctamente";
        break;
    case "getpaquetesxlote":
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        $Data = $obj_medido->getPaquetesXLote($id);
        $Data = Excepciones::validaConsulta($Data);
        $json_string = json_encode($Data);
        echo $json_string;
        break;
    case "getdetpaquete":
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        $Data = $obj_medido->getDetPaquete($id);
        $Data = Excepciones::validaConsulta($Data);
        $json_string = json_encode($Data);
        echo $json_string;
        break;
    case "eliminarpaquete":
        $id = (isset($_POST['id'])) ? $_POST['id'] : '';
        $idLoteMedido = (isset($_POST['idLoteMedido'])) ? $_POST['idLoteMedido'] : '';
        Excepciones::validaLlenadoDatos(array(
            " Paquete" => $id,
            " Lote Medido" => $idLoteMedido
        ), $obj_medido);
        $obj_medido->beginTransaction();
        // 1. Verifica que haya lados en paquete
        $DataPaq = $obj_medido->getPaquetesXLote($idLoteMedido);
        $DataPaq = Excepciones::validaConsulta($DataPaq);
        $countPaq = count($DataPaq);
        $_paqpend = $countPaq - 1 > 0 ? true : false;
        // 2. Si hay lados ingresar el paquete pendiente por surtir en lote
        if ($_paqpend) {
            $datos = $obj_medido->ingresarNumPaqDlt($id);
            try {
                Excepciones::validaMsjError($datos);
            } catch (Exception $e) {
                $obj_medido->errorBD($e->getMessage(), 1);
            }
        }
        // 3. Si ya no hay paquete quitar pendientes de lote 
        else {
            $datos = $obj_medido->eliminarNumPaqDlt($idLoteMedido);
            try {
                Excepciones::validaMsjError($datos);
            } catch (Exception $e) {
                $obj_medido->errorBD($e->getMessage(), 1);
            }
        }

        $datos = $obj_medido->eliminarLadosPaq($id);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_medido->errorBD($e->getMessage(), 1);
        }
        $datos = $obj_medido->eliminarPaquete($id);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_medido->errorBD($e->getMessage(), 1);
        }

        $obj_medido->insertarCommit();
        echo "1|Paquete Eliminado Correctamente";

        break;
    case "eliminartodospaquetes":
        $id = (isset($_POST['id'])) ? $_POST['id'] : '';
        Excepciones::validaLlenadoDatos(array(
            " Lote" => $id,
        ), $obj_medido);
        $obj_medido->beginTransaction();

        $datos = $obj_medido->devolverLadosLotes($id);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_medido->errorBD($e->getMessage(), 1);
        }

        $datos = $obj_medido->eliminarAllPaq($id);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_medido->errorBD($e->getMessage(), 1);
        }

        $datos = $obj_medido->eliminarNumPaqDlt($id);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_medido->errorBD($e->getMessage(), 1);
        }
        $obj_medido->insertarCommit();

        echo "1|Paquetes Eliminados Correctamente";


        break;

    case "getpaqabierto":
        $id = isset($_POST['id']) ? $_POST['id'] : '';

        $Data = $obj_medido->getDetLote($id);
        $Data = Excepciones::validaConsulta($Data);
        $json_string = json_encode($Data);
        echo $json_string;
        break;
    case "gethistemedicion":
        $date_start = isset($_POST['date_start']) ? $_POST['date_start'] : '';
        $date_end = isset($_POST['date_end']) ? $_POST['date_end'] : '';
        $programa = isset($_POST['programa']) ? $_POST['programa'] : '';

        $filtradoPrograma = $programa != '' ? 'l.idCatPrograma=' . $programa . '' : '1=1';
        if ($date_start != "" and $date_end != "") {
            $filtradoFecha = "DATE_FORMAT(l.fechaReg, '%Y-%m-%d') BETWEEN '$date_start' AND '$date_end'";
        } else {
            $filtradoFecha = "1=1";
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
            $dif = formatoMil($value['areaTotalRd'] - $value['areaTotalFT'], 2);
            array_push($response, [
                $value['id'],
                $value['loteTemola'],
                $value['nPrograma'],
                $value['nGrosor'],
                $ladosTotales,
                $areaTotalDM,
                $areaTotalFT,
                $areaTotalRd,
                $dif
            ]);
            $count++;
        }

        //Creamos el JSON
        $response = array("data" => $response);
        $json_string = json_encode($response);
        echo $json_string;
        break;
}
