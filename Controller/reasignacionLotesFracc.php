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

$obj_rendimiento = new ReasignacionLotesFracc($debug, $idUser);

$ErrorLog = 'No se recibió';
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}

switch ($_GET["op"]) {
    case "inittraspasar":
        $idRendimiento = (isset($_POST['idRendimiento'])) ? trim($_POST['idRendimiento']) : '';
        $pzasTraspasar = (isset($_POST['pzasTraspasar'])) ? trim($_POST['pzasTraspasar']) : '';
        Excepciones::validaLlenadoDatos(array(
            " Rendimiento" => $idRendimiento, " Piezas" => $pzasTraspasar
        ), $obj_rendimiento);
        /***** Consulta Rendimiento******/
        $Data = $obj_rendimiento->getDetRendimiento($idRendimiento);
        $Data = Excepciones::validaConsulta($Data);
        $Data = $Data == '' ? array() : $Data;
        if (count($Data) <= 0) {
            $obj_rendimiento->errorBD("No se encuentra Lote, notifica a Departamento de Sistemas.", 0);
        }
        $porcentaje = 0;
        /********************************/
        /***** Aplicacion de Regla de 3******/
        $total_s = $Data['total_s'];
        $_1s = $Data['1s'];
        $_2s = $Data['2s'];
        $_3s = $Data['3s'];
        $_4s = $Data['4s'];
        $_20 = $Data['_20'];

        $porcentaje = $pzasTraspasar / $total_s;
        $_1sPorc = $_1s * $porcentaje;
        $_1Rea = round($_1s - $_1sPorc);

        $_2sPorc = (int)$_2s * $porcentaje;
        $_2Rea = round($_2s - $_2sPorc);

        $_3sPorc = (int)$_3s * $porcentaje;
        $_3Rea = round($_3s - $_3sPorc);

        $_4sPorc = (int)$_4s * $porcentaje;
        $_4Rea = round($_4s - $_4sPorc);

        $_20Porc = (int)$_20 * $porcentaje;
        $_20Rea = round($_20 - $_20Porc);

        $totalPzasFinales = $total_s - $pzasTraspasar;
        if ($debug == '1') {
            echo "<br>Total: ", $total_s, "<br>";
            echo "Porcentaje: ", $porcentaje, "<br>";
        }
        if ($debug == '1') {
            echo "% 1s: ", $_1sPorc, "<br>";
            echo "Cl. 1s: ", $_1Rea, "<br>";

            echo "% 2s: ", $_2sPorc, "<br>";
            echo "Cl. 2s: ", $_2Rea, "<br>";

            echo "% 3s: ", $_3sPorc, "<br>";
            echo "Cl. 3s: ", $_3Rea, "<br>";

            echo "% 4s: ", $_4sPorc, "<br>";
            echo "Cl. 4s: ", $_4Rea, "<br>";

            echo "% 20: ", $_20Porc, "<br>";
            echo "Cl. 20: ", $_20Rea, "<br>";

            echo "Pzas Totales: ", $totalPzasFinales, "<br>";
        }

        /********************************/
        $obj_rendimiento->beginTransaction();
        /***** Iniciar Traspaso******/
        $datos = $obj_rendimiento->initTraspasar($idRendimiento, $pzasTraspasar, $porcentaje * 100, $_1Rea, $_2Rea, $_3Rea, $_4Rea, $_20Rea, $totalPzasFinales);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_rendimiento->errorBD($e->getMessage(), 1);
        }
        $idReasignacion = $datos['2'];
        /********************************/
        /***** Alinear Ventas******/
        $Data = $obj_rendimiento->getVentasXLote($idRendimiento);
        $Data = Excepciones::validaConsulta($Data);
        $Data = $Data == '' ? array() : $Data;
        $str_query = "";
        foreach ($Data as $key => $value) {
            $idVenta = $Data[$key]['idVenta'];
            /********* CALCULO DE CUEROS POR VENTA **********/
            $_1sVentas = $Data[$key]['1s'] * $porcentaje;
            $_1ReaV = round($Data[$key]['1s'] - $_1sVentas);

            $_2sVentas = (int)$Data[$key]['2s'] * $porcentaje;
            $_2ReaV = round($Data[$key]['2s'] - $_2sVentas);

            $_3sVentas = (int)$Data[$key]['3s'] * $porcentaje;
            $_3ReaV = round($Data[$key]['3s'] - $_3sVentas);

            $_4sVentas = (int)$Data[$key]['4s'] * $porcentaje;
            $_4ReaV = round($Data[$key]['4s'] - $_4sVentas);

            $_20Ventas = (int)$Data[$key]['_20'] * $porcentaje;
            $_20ReaV = round($Data[$key]['_20'] - $_20Ventas);
            $totalPzasFinalesV = $Data[$key]['total_s'] - ($Data[$key]['total_s'] * $porcentaje);

            if ($debug == '1') {
                echo "<br>% 1s: ", $_1sVentas, "<br>";
                echo "Cl. 1s: ", $_1ReaV, "<br>";

                echo "% 2s: ", $_2sVentas, "<br>";
                echo "Cl. 2s: ", $_2ReaV, "<br>";

                echo "% 3s: ", $_3sVentas, "<br>";
                echo "Cl. 3s: ", $_3ReaV, "<br>";

                echo "% 4s: ", $_4sVentas, "<br>";
                echo "Cl. 4s: ", $_4ReaV, "<br>";

                echo "% 20: ", $_20Ventas, "<br>";
                echo "Cl. 20: ", $_20ReaV, "<br>";

                echo "Pzas Totales Vendidas: ", $totalPzasFinalesV, "<br>";
            }
            $str_query .= "('$idReasignacion','$idVenta','1','$porcentaje', '$_1ReaV', '$_2ReaV', '$_3ReaV', '$_4ReaV','$_20ReaV', '$totalPzasFinalesV',
            '$idUser', NOW()),";
        }
        if ($str_query != '') {
            $str_query = substr($str_query, 0, -1);
            $datos = $obj_rendimiento->agregarCalculoVentas($str_query);
            try {
                Excepciones::validaMsjError($datos);
            } catch (Exception $e) {
                $obj_rendimiento->errorBD($e->getMessage(), 1);
            }
        }

        /********************************/
        /***** Alinear Pedidos******/
        $Data = $obj_rendimiento->getPedidosXLote($idRendimiento);
        $Data = Excepciones::validaConsulta($Data);
        $Data = $Data == '' ? array() : $Data;
        $str_query = "";
        $contPedido = 0;
        $sum_areaProvLote = 0;
        $sum_precioUnitFactUsd = 0;
        foreach ($Data as $key => $value) {
            $contPedido++;
            $idPedido = $Data[$key]['id'];
            /********* CALCULO DE CUEROS POR PEDIDO **********/
            $_1sPedido = $Data[$key]['1s'] * $porcentaje;
            $_1ReaP = round($Data[$key]['1s'] - $_1sPedido);

            $_2sPedido = (int)$Data[$key]['2s'] * $porcentaje;
            $_2ReaP = round($Data[$key]['2s'] - $_2sPedido);

            $_3sPedido = (int)$Data[$key]['3s'] * $porcentaje;
            $_3ReaP = round($Data[$key]['3s'] - $_3sPedido);

            $_4sPedido = (int)$Data[$key]['4s'] * $porcentaje;
            $_4ReaP = round($Data[$key]['4s'] - $_4sPedido);

            $_20Pedido = (int)$Data[$key]['_20'] * $porcentaje;
            $_20ReaP = round($Data[$key]['_20'] - $_20Pedido);
            $totalPzasFinalesP = $Data[$key]['total_s'] - ($Data[$key]['total_s'] * $porcentaje);

            $areaProvLote = $totalPzasFinalesP * $Data[$key]['areaWBPromFact'];
            $sum_areaProvLote += $areaProvLote;
            $sum_precioUnitFactUsd += $Data[$key]['precioUnitFactUsd'];

            if ($debug == '1') {
                echo "<br>% 1s: ", $_1sPedido, "<br>";
                echo "Cl. 1s: ", $_1ReaP, "<br>";

                echo "% 2s: ", $_2sPedido, "<br>";
                echo "Cl. 2s: ", $_2ReaP, "<br>";

                echo "% 3s: ", $_3sPedido, "<br>";
                echo "Cl. 3s: ", $_3ReaP, "<br>";

                echo "% 4s: ", $_4sPedido, "<br>";
                echo "Cl. 4s: ", $_4ReaP, "<br>";

                echo "% 20: ", $_20Pedido, "<br>";
                echo "Cl. 20: ", $_20ReaP, "<br>";

                echo "Pzas Totales Pedidos: ", $totalPzasFinalesP, "<br>";
                echo "Área de Prov Lote: ", $areaProvLote, "<br>";
            }
            $str_query .= "('$idReasignacion','$idPedido','1','$porcentaje', '$_1ReaP', '$_2ReaP', '$_3ReaP', '$_4ReaP','$_20ReaP', '$totalPzasFinalesP',
            '$idUser', NOW(), '$areaProvLote'),";
        }
        $str_query = substr($str_query, 0, -1);
        $datos = $obj_rendimiento->agregarCalculoPedidos($str_query);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_rendimiento->errorBD($e->getMessage(), 1);
        }
        /********************************/
        $promAreaLoteProveedor = $sum_areaProvLote / $contPedido;
        $promPrecioUnitFactUsd = $sum_precioUnitFactUsd / $contPedido;

        /********************************/
        /***** INICIO DE LOTE DE TRANSFERENCIA******/
        $Data = $obj_rendimiento->consultaNameRendimiento($idRendimiento);
        $Data = Excepciones::validaConsulta($Data);
        $Data = $Data == '' ? array() : $Data;
        if (count($Data) <= 0) {
            $obj_rendimiento->errorBD("No se encuentra Información del Nuevo Lote, notifica a Departamento de Sistemas.", 1);
        }
        $nLoteTemola = $Data['nLoteTemola'];
        $_1sTransfer = round($_1s * $porcentaje);
        $_2sTransfer = round($_2s * $porcentaje);
        $_3sTransfer = round($_3s * $porcentaje);
        $_4sTransfer = round($_4s * $porcentaje);
        $_20Transfer = round($_20 * $porcentaje);
        $totalPzasTransfer = round($total_s * $porcentaje);

        if ($debug == '1') {
            echo "<br>Nuevo Lote: ", $nLoteTemola, "<br>";

            echo "Cl. 1s: ", $_1sTransfer, "<br>";
            echo "Cl. 2s: ", $_2sTransfer, "<br>";
            echo "Cl. 3s: ", $_3sTransfer, "<br>";
            echo "Cl. 4s: ", $_4sTransfer, "<br>";
            echo "Cl. 20: ", $_20Transfer, "<br>";
            echo "Pzas Totales Transfer: ", $totalPzasTransfer, "<br>";
            echo "Precio Unit Fact USD: ", $promPrecioUnitFactUsd, "<br>";
        }
        /********************************/
        $datos = $obj_rendimiento->agregarInfoNuevoLote(
            $idReasignacion,
            $nLoteTemola,
            $_1sTransfer,
            $_2sTransfer,
            $_3sTransfer,
            $_4sTransfer,
            $_20Transfer,
            $totalPzasTransfer,
            $promAreaLoteProveedor,
            $promPrecioUnitFactUsd
        );
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_rendimiento->errorBD($e->getMessage(), 1);
        }
        $obj_rendimiento->insertarCommit();
        echo "1|Traspaso de Cueros Incializado.|" . $idReasignacion;
        break;
    case "configlote":
        $idTraspaso = (isset($_POST['idTraspaso'])) ? trim($_POST['idTraspaso']) : '';
        $proceso = (isset($_POST['proceso'])) ? trim($_POST['proceso']) : '';
        $programa = (isset($_POST['programa'])) ? trim($_POST['programa']) : '';

        Excepciones::validaLlenadoDatos(array(
            " Traspaso de Lote" => $idTraspaso, " Proceso" => $proceso,
            " Programa" => $programa
        ), $obj_rendimiento);
        /*****CONSULTA DE PROCESO******/
        $DataProceso = $obj_rendimiento->getDetProceso($proceso);
        $DataProceso = Excepciones::validaConsulta($DataProceso);
        $DataProceso = $DataProceso == '' ? array() : $DataProceso;
        if (count($DataProceso) <= 0) {
            $obj_rendimiento->errorBD("Error, no se encontró datos del proceso del lote", 0);
        }
        $tipoProceso = $DataProceso['tipo'];
        /*****CONSULTA DE PROGRAMA******/
        $DataPrograma = $obj_rendimiento->getDetPrograma($programa);
        $DataPrograma = Excepciones::validaConsulta($DataPrograma);
        $DataPrograma = $DataPrograma == '' ? array() : $DataPrograma;
        if (count($DataPrograma) <= 0) {
            $obj_rendimiento->errorBD("Error, no se encontró datos del programa del lote", 0);
        }
        $areaNeta = $DataPrograma['areaNeta'];
        /*****CONSULTA DE TRASPASO******/
        $Data = $obj_rendimiento->getTraspasoRendimiento($idTraspaso);
        $Data = Excepciones::validaConsulta($Data);
        $Data = $Data == '' ? array() : $Data;
        if (count($Data) <= 0) {
            $obj_rendimiento->errorBD("Error, no se encontró datos del traspaso del lote", 0);
        }
        $idRendimiento = $Data['idRendimiento'];
        $porcentaje = $Data['porcentaje'] / 100;
        $loteTransfer = $Data['loteTransfer'];
        $idTraspaso = $Data['id'];

        $_1s = $Data['1sTransfer'] == '' ? '0' : $Data['1sTransfer'];
        $_2s = $Data['2sTransfer'] == '' ? '0' : $Data['2sTransfer'];
        $_3s = $Data['3sTransfer'] == '' ? '0' : $Data['3sTransfer'];
        $_4s = $Data['4sTransfer'] == '' ? '0' : $Data['4sTransfer'];
        $_20 = $Data['_20Transfer'] == '' ? '0' : $Data['_20Transfer'];
        $total_s = $Data['total_sTransfer'] == '' ? '0' : $Data['total_sTransfer'];
        /********************************************/
        /*****CONSULTA DE ESTADO DE RENDIMIENTO ORIGEN******/
        /********************************************/
        $DataRend = $obj_rendimiento->getDetRendimiento($idRendimiento);
        $DataRend = Excepciones::validaConsulta($DataRend);
        $DataRend = $Data == '' ? array() : $DataRend;
        if (count($DataRend) <= 0) {
            $obj_rendimiento->errorBD("Error, no se encontró datos del lote", 0);
        }
        $estado = $DataRend['estado'];
        $idCatMateriaPrima = $DataRend['idCatMateriaPrima'];
        $tipoMateriaPrima = $DataRend['tipoMateriaPrima'];
        $multiMateria = $DataRend['multiMateria'];
        $fechaEngrase = $DataRend['fechaEngrase'];

        if ($estado == '4') {
            $areaCrustPorc = $DataRend['areaCrust'] * $porcentaje;
            $areaCrustDism = $DataRend['areaCrust'] - $areaCrustPorc;

            $areaFinalPorc = $DataRend['areaFinal'] * $porcentaje;
            $areaFinalDism = $DataRend['areaFinal'] - $areaFinalPorc;

            $areaWBPorc = $DataRend['areaWB'] * $porcentaje;
            $areaWBDism = $DataRend['areaWB'] - $areaFinalPorc;

            $perdidaAreaWBaCrustPorc = OperacionesLotes::perdidaAreaWBaCrust($areaCrustPorc, $areaWBPorc) * 100;
            $recorteAcabadoPorc = OperacionesLotes::calculaPorcValue($porcentaje, $DataRend['recorteAcabado']);


            $humedad = $DataRend['humedad'];
            $quiebre = $DataRend['quiebre'];
            $suavidad = $DataRend['suavidad'];

            $areaWBPorc = $DataRend['areaWB'] * $porcentaje;


            if ($debug == 1) {
                echo "<br>Porcentaje: ", $porcentaje;
                echo "<br>Area Crust Normal: ", $DataRend['areaCrust'];
                echo "<br>Area Crust Porc: ", $areaCrustPorc;
                echo "<br>Area Crust Dism: ", $areaCrustDism;
                echo "<br>*************************************";
                echo "<br>Area Teseo: ", $DataRend['areaFinal'];
                echo "<br>Area Final Porc: ", $areaFinalPorc;
                echo "<br>Area Final Dism: ", $areaFinalDism;
                echo "<br>*************************************";
                echo "<br>Area WB: ", $DataRend['areaWB'];
                echo "<br>Area WB Porc: ", $areaWBPorc;
                echo "<br>Area WB Dism: ", $areaWBDism;
                echo "<br>*************************************";
                echo "<br>Humedad: ", $humedad;
                echo "<br>Quiebre: ", $quiebre;
                echo "<br>Suavidad: ", $suavidad;
                echo "<br>*************************************";
            }
        } else {
            $areaCrustPorc = 0;
            $areaCrustDism = 0;

            $areaFinalPorc = 0;
            $areaFinalDism = 0;

            $areaWBPorc = 0;
            $areaWBDism = 0;

            $perdidaAreaWBaCrustPorc = 0;
            $recorteAcabadoPorc = 0;


            $humedad = 0;
            $quiebre = 0;
            $suavidad = 0;

            $areaWBPorc = 0;
        }
        $obj_rendimiento->beginTransaction();

        /************ INGRESAR LOTE DE TEMOLA ***************/
        $datos = $obj_rendimiento->agregarNuevoRendimiento(
            $loteTransfer,
            $idCatMateriaPrima,
            $tipoMateriaPrima,
            $fechaEngrase,
            $proceso,
            $tipoProceso,
            $programa,
            $areaNeta,
            $areaWBPorc,
            $areaCrustPorc,
            $areaFinalPorc,
            $humedad,
            $quiebre,
            $suavidad,
            $_1s,
            $_2s,
            $_3s,
            $_4s,
            $_20,
            $total_s,
            $idRendimiento,
            $perdidaAreaWBaCrustPorc,
            $recorteAcabadoPorc,
            $multiMateria,
        );
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_rendimiento->errorBD($e->getMessage(), 1);
        }
        $idNuevoRendimiento = $datos['2'];
        /************ ACTUALIZAR TRASPASOS ***************/
        $datos = $obj_rendimiento->agregarRendimientoTraspaso($idNuevoRendimiento, $idTraspaso);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_rendimiento->errorBD($e->getMessage(), 1);
        }
        /************ COPIA ESPEJO DE LOS PEDIDOS REALIZADOS ***************/
        $datos = $obj_rendimiento->copiaPedidosRendimiento($idTraspaso);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_rendimiento->errorBD($e->getMessage(), 1);
        }
        /************ ACTUALIZA DATOS DE PEDIDO EN NUEVO RENDIMIENTO ***************/
        $datos = $obj_rendimiento->registraPedidoLoteo($idNuevoRendimiento);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_rendimiento->errorBD($e->getMessage(), 1);
        }
        $obj_rendimiento->insertarCommit();
        echo '1|Se agregó lote y datos iniciales.|' . $idNuevoRendimiento;

        break;
    case "finalizartraspaso":
        $idTraspaso = (isset($_POST['idTraspaso'])) ? trim($_POST['idTraspaso']) : '';
        $idRendimiento = (isset($_POST['idRendimiento'])) ? trim($_POST['idRendimiento']) : '';

        Excepciones::validaLlenadoDatos(array(
            " Traspaso de Lote" => $idTraspaso,
            " Rendimiento" => $idRendimiento
        ), $obj_rendimiento);

        $obj_rendimiento->beginTransaction();
        /************ ACTUALIZA VENTAS DE RENDIMIENTO ORIGEN ***************/
        $datos = $obj_rendimiento->actualizacionCuerosVentas($idTraspaso);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_rendimiento->errorBD($e->getMessage(), 1);
        }
        /************ ACTUALIZA PEDIDOS DE RENDIMIENTO ORIGEN ***************/
        $datos = $obj_rendimiento->actualizacionCuerosPedidos($idTraspaso);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_rendimiento->errorBD($e->getMessage(), 1);
        }
        /***** Consulta Traspaso******/
        $DataTrasp = $obj_rendimiento->getTraspasoRendimiento($idTraspaso);
        $DataTrasp = Excepciones::validaConsulta($DataTrasp);
        $DataTrasp = $DataTrasp == '' ? array() : $DataTrasp;
        if (count($DataTrasp) <= 0) {
            $obj_rendimiento->errorBD("No se encuentra Traspaso, notifica a Departamento de Sistemas.", 0);
        }
        $idRendOrigen = $DataTrasp['idRendimiento'];
        $_1sRend= $DataTrasp['1s'];
        $_2sRend= $DataTrasp['2s'];
        $_3sRend= $DataTrasp['3s'];
        $_4sRend= $DataTrasp['4s'];
        $_20Rend= $DataTrasp['_20'];
        $total_sRend= $DataTrasp['total_s'];

        /***** Consulta Rendimiento******/
        $Data = $obj_rendimiento->getDetRendimiento($idRendOrigen);
        $Data = Excepciones::validaConsulta($Data);
        $Data = $Data == '' ? array() : $Data;
        if (count($Data) <= 0) {
            $obj_rendimiento->errorBD("No se encuentra Lote, notifica a Departamento de Sistemas.", 0);
        }

        $porcentaje = $DataTrasp['porcentaje'] / 100;
        $totalPzasFinales = $DataTrasp['total_s'];
        $precioUnitFactUsd = $DataTrasp['precioUnitFactUsd'];

        $areaWBRea = OperacionesLotes::calculaRestPorcValue($porcentaje, $Data['areaWB']);
        $areaCrustRea = OperacionesLotes::calculaRestPorcValue($porcentaje, $Data['areaCrust']);
        $areaFinalRea = OperacionesLotes::calculaRestPorcValue($porcentaje, $Data['areaFinal']);
        $diferenciaAreaRea = OperacionesLotes::diferenciaArea($areaFinalRea, $DataTrasp['areaProveedorLote']);
        $promedioAreaRea = OperacionesLotes::promedioArea($areaFinalRea, $totalPzasFinales);
        $porcDifAreaWB = OperacionesLotes::porcDifAreaWB($diferenciaAreaRea, $totalPzasFinales);
        $recorteAcabadoRea = OperacionesLotes::calculaRestPorcValue($porcentaje, $Data['recorteAcabado']);
        $porcRecorteAcabadoRea = OperacionesLotes::porcRecorteAcabado($areaCrustRea, $recorteAcabadoRea) * 100;
        $perdidaAreaWBaCrustRea = OperacionesLotes::perdidaAreaWBaCrust($areaCrustRea, $areaWBRea) * 100;
        $areaWBXSetRea = OperacionesLotes::areaXSet($areaWBRea, $Data['unidadesEmpacadas'], $Data['tipoProceso']);
        $areaCrustXSetRea = OperacionesLotes::areaXSet($areaCrustRea, $Data['unidadesEmpacadas'], $Data['tipoProceso']);
        $costoWBXUnidadRea = OperacionesLotes::costoWBXUnidad($areaWBXSetRea, $precioUnitFactUsd);
        $perdidaAreaCrustTeseoRea = OperacionesLotes::perdidaAreaCrustTeseo($areaCrustRea, $areaFinalRea);
        $yieldFinalRealRea = OperacionesLotes::yieldFinalReal($Data['areaNeta_Prg'], $areaWBXSetRea);
        if ($debug == '1') {
            echo "Set's Empacados: ", $Data['unidadesEmpacadas'];
            echo "Porcentaje: ", $porcentaje, "<br>";
            echo "Area Prov Lote: ", $DataTrasp['areaProveedorLote'], "<br>";

            echo "Área WB Rend: ", $Data['areaWB'], "<br>";
            echo "Área WB: ", $areaWBRea, "<br>";

            echo "Área Crust Rend: ", $Data['areaCrust'], "<br>";
            echo "Área Crust: ", $areaCrustRea, "<br>";

            echo "Área Final Rend: ", $Data['areaFinal'], "<br>";
            echo "Área Final: ", $areaFinalRea, "<br>";

            echo "Diferencia de Área Rend: ", $Data['diferenciaArea'], "<br>";
            echo "Diferencia de Área: ", $diferenciaAreaRea, "<br>";

            echo "Promedio de Área Rend: ", $Data['promedioAreaWB'], "<br>";
            echo "Promedio de Área: ", $promedioAreaRea, "<br>";

            echo "Recorte Acabado gr. Rend: ", $Data['recorteAcabado'], "<br>";
            echo "Recorte Acabado gr.: ", $recorteAcabadoRea, "<br>";

            echo "Porc. Recorte de Acabado Rend: ", $Data['porcRecorteAcabado'], "<br>";
            echo "Porc. Recorte de Acabado: ", $porcRecorteAcabadoRea, "<br>";

            echo "Perdida AreaWB a Crust Rend: ", $Data['perdidaAreaWBCrust'], "<br>";
            echo "Perdida AreaWB a Crust: ", $perdidaAreaWBaCrustRea, "<br>";

            echo "Area WB x Set Rend: ", $Data['areaWBUnidad'], "<br>";
            echo "Area WB x Set: ", $areaWBXSetRea, "<br>";

            echo "Area Crust x Set Rend: ", $Data['areaCrustSet'], "<br>";
            echo "Area Crust x Set: ", $areaCrustXSetRea, "<br>";

            echo "Costo Unidad por Set Rend: ", $Data['costoWBUnit'], "<br>";
            echo "Costo Unidad por Set: ", $costoWBXUnidadRea, "<br>";

            echo "Perdida de Area Crust a Teseo Rend: ", $Data['perdidaAreaCrustTeseo'], "<br>";
            echo "Perdida de Area Crust a Teseo: ", $perdidaAreaCrustTeseoRea, "<br>";

            echo "Yield Final Real Rend: ", $Data['yieldFinalReal'], "<br>";
            echo "Yield Final Real: ", $yieldFinalRealRea, "<br>";

            echo "1s: ", $_1sRend;
            echo "2s: ", $_2sRend;
            echo "3s: ", $_3sRend;
            echo "4s: ", $_4sRend;
            echo "20: ", $_20Rend;
            echo "Total s: ", $total_sRend;

        }
        /************ ACTUALIZA NUEVO RENDIMIENTO ***************/
        $datos = Funciones::cambiarEstatus("reasignacionfracclotes", "2", "estado", $idTraspaso, $obj_rendimiento->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_rendimiento->errorBD($e->getMessage(), 1);
        }
        /************ ACTUALIZA DATOS DE RENDIMIENTO ORIGEN ***************/
        $datos = $obj_rendimiento->actualizaDatosRendOrigen(
            $idRendOrigen,
            $areaWBRea,
            $areaCrustRea,
            $areaFinalRea,
            $diferenciaAreaRea,
            $promedioAreaRea,
            $recorteAcabadoRea,
            $porcRecorteAcabadoRea,
            $perdidaAreaWBaCrustRea,
            $areaWBXSetRea,
            $areaCrustXSetRea,
            $costoWBXUnidadRea,
            $perdidaAreaCrustTeseoRea,
            $yieldFinalRealRea,
            $_1sRend,
            $_2sRend,
            $_3sRend,
            $_4sRend,
            $_20Rend,
            $total_sRend
        );
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_rendimiento->errorBD($e->getMessage(), 1);
        }

        $obj_rendimiento->insertarCommit();
        $_SESSION['CRESuccessReasigna'] = 'Finalización de traspaso de lote correctamente.';
        echo '1|Finalización de traspaso de lote correctamente.';

        break;

    case "cancelartraspaso":
        $idTraspaso = (isset($_POST['idTraspaso'])) ? trim($_POST['idTraspaso']) : '';
        $idRendimiento = (isset($_POST['idRendimiento'])) ? trim($_POST['idRendimiento']) : '';

        Excepciones::validaLlenadoDatos(array(
            " Traspaso de Lote" => $idTraspaso,
            " Rendimiento" => $idRendimiento
        ), $obj_rendimiento);
        $obj_rendimiento->beginTransaction();
        /************ ELIMINA VENTAS DE RENDIMIENTO ORIGEN ***************/
        $datos = $obj_rendimiento->eliminaCuerosVentas($idTraspaso);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_rendimiento->errorBD($e->getMessage(), 1);
        }
        /************ ELIMINA PEDIDOS DE RENDIMIENTO ORIGEN ***************/
        $datos = $obj_rendimiento->eliminarCuerosPedidos($idTraspaso);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_rendimiento->errorBD($e->getMessage(), 1);
        }
        /************ ELIMINA NUEVO RENDIMIENTO ***************/
        $datos = $obj_rendimiento->eliminarRendimiento($idRendimiento);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_rendimiento->errorBD($e->getMessage(), 1);
        }
        /************ ELIMINA REASIGNACION  ***************/
        $datos = $obj_rendimiento->eliminarReasignacion($idTraspaso);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_rendimiento->errorBD($e->getMessage(), 1);
        }
        $obj_rendimiento->insertarCommit();
        $_SESSION['CRESuccessReasigna'] = 'Cancelación de  traspaso de lote correctamente.';
        echo '1|Cancelación de  traspaso de lote correctamente.';

        break;
    case "cancelartraspasoabierto":
        $DataTraspaso = $obj_rendimiento->getTraspasoAbierto();
        $DataTraspaso = Excepciones::validaConsulta($DataTraspaso);
        $DataTraspaso = $DataTraspaso == '' ? array() : $DataTraspaso;
        $idRendimiento = $DataTraspaso['idRendimiento'];
        $idTraspaso = $DataTraspaso['id'];
        Excepciones::validaLlenadoDatos(array(
            " Traspaso de Lote" => $idTraspaso,
            " Rendimiento" => $idRendimiento
        ), $obj_rendimiento);
        $obj_rendimiento->beginTransaction();
        /************ ELIMINA VENTAS DE RENDIMIENTO ORIGEN ***************/
        $datos = $obj_rendimiento->eliminaCuerosVentas($idTraspaso);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_rendimiento->errorBD($e->getMessage(), 1);
        }
        /************ ELIMINA PEDIDOS DE RENDIMIENTO ORIGEN ***************/
        $datos = $obj_rendimiento->eliminarCuerosPedidos($idTraspaso);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_rendimiento->errorBD($e->getMessage(), 1);
        }
  
        /************ ELIMINA REASIGNACION  ***************/
        $datos = $obj_rendimiento->eliminarReasignacion($idTraspaso);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_rendimiento->errorBD($e->getMessage(), 1);
        }
        $obj_rendimiento->insertarCommit();
        $_SESSION['CRESuccessReasigna'] = 'Cancelación de  traspaso de lote correctamente.';
        echo '1|Cancelación de  traspaso de lote correctamente.';
        break;
}
