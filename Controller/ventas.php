<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../include/connect_mvc.php";
include('../Models/Mdl_ConexionBD.php');
include('../Models/Mdl_Venta.php');
include('../Models/Mdl_Static.php');
include('../Models/Mdl_Excepciones.php');
$pzasEnSet = 4;
$debug = 0;
$idUser = $_SESSION['CREident'];

$obj_venta = new Venta($debug, $idUser);

$ErrorLog = 'No se recibió ';
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}

switch ($_GET["op"]) {
    case "initventa":
        $fechaFacturacion = (isset($_POST['fechaFacturacion'])) ? trim($_POST['fechaFacturacion']) : '';
        $numFactura = (isset($_POST['numFactura'])) ? trim($_POST['numFactura']) : '';
        $numPL = (isset($_POST['numPL'])) ? trim($_POST['numPL']) : '';
        $idTipoVenta =  (isset($_POST['idTipoVenta'])) ? trim($_POST['idTipoVenta']) : '';
        $requiereFact =  (isset($_POST['requiereFact'])) ? trim($_POST['requiereFact']) : '';

        $log = '';
        if ($fechaFacturacion == '') {
            $ErrorLog .= ' fecha de Facturación,';
            $log = '1';
        }
        if ($numFactura == '' and $requiereFact == '1') {
            $ErrorLog .= ' Número de Factura,';
            $log = '1';
        }
        if ($numPL == '') {
            $ErrorLog .= ' Número de PL,';
            $log = '1';
        }
        if ($idTipoVenta == '') {
            $ErrorLog .= ' Tipo de Venta,';
            $log = '1';
        }
        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_venta->errorBD($ErrorLog, 0);
        }
        $datos = $obj_venta->initVenta($fechaFacturacion, $numFactura, $numPL, $idTipoVenta);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_venta->errorBD($e->getMessage(), 1);
        }
        echo '1|Venta Iniciada Correctamente.';

        break;
    case "agregarunidades":
        $idRendimiento = (isset($_POST['idRendimiento'])) ? trim($_POST['idRendimiento']) : '';
        $unidades = (isset($_POST['unidades'])) ? trim($_POST['unidades']) : '0';
        $tipo = (isset($_POST['tipoLote'])) ? trim($_POST['tipoLote']) : '';
        $sets = (isset($_POST['sets'])) ? trim($_POST['sets']) : '0';

        $log = '';
        if ($idRendimiento == '') {
            $ErrorLog .= ' Lote Seleccionado,';
            $log = '1';
        }
        /*  if ($unidades == '' or $unidades <= 0) {
            $ErrorLog .= ' Unidades,';
            $log = '1';
        }*/

        if ($tipo == '' or $tipo <= 0) {
            $ErrorLog .= ' tipo de Lote Seleccionado,';
            $log = '1';
        }
        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_venta->errorBD($ErrorLog, 0);
        }

        $datos = $obj_venta->addLoteVenta($idRendimiento, $unidades, $sets, $tipo);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_venta->errorBD($e->getMessage(), 1);
        }

        echo '1|Se agregó Lote a la Venta Correctamente.';
        break;
    case "eliminardetventa":
        $id = (isset($_POST['id'])) ? trim($_POST['id']) : '';
        #VALIDACION DE DATOS
        Excepciones::validaLlenadoDatos(array(
            " Detalle de Venta" => $id,
        ), $obj_venta);

        $datos = $obj_venta->eliminarDetVenta($id);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_venta->errorBD($e->getMessage(), 1);
        }
        echo '1|Detalle de Venta Eliminada Correctamente.';

        break;

    case "eliminarventa":
        $obj_venta->beginTransaction();
        //Eliminar Apartado de las Cajas: Libera la caja
        $datos = $obj_venta->eliminarApartadoCajas();
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_venta->errorBD($e->getMessage(), 1);
        }
        //Eliminar Venta: Elimina Venta y Detallado
        $datos = $obj_venta->eliminarVenta();
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_venta->errorBD($e->getMessage(), 1);
        }

        $obj_venta->insertarCommit();
        $_SESSION['CRESuccessVenta'] = 'Venta Eliminada Correctamente.';
        echo '1|Venta Eliminada Correctamente.';

        break;
    case "finalizarventa":
        /*ACTUALIZACION: SE DEBERÁ VERIFICAR QUE EL USUARIO NO HAYA REQUERIDO MATERIAL*/
        /****************** VALIDAR PZAS REQUERIDAS******************************/
        $dataValidaRequi = $obj_venta->validarRequirimientoEnVenta();
        $dataValidaRequi = $dataValidaRequi == '' ? array() : $dataValidaRequi;
        if (!is_array($dataValidaRequi)) {
            $obj_venta->errorBD($dataValidaRequi, 0);
        }
        if (count($dataValidaRequi) <= 0) {
            $dataValidaRequi['totalAbastecidos'] = '0';
            $dataValidaRequi['totalRequeri'] = '0';
        }
        $_Requisicion = ($dataValidaRequi['totalAbastecidos'] < $dataValidaRequi['totalRequeri']) ? true : false;
        /*************** VALIDA QUE EXISTAN UNIDADES EN LAS VENTAS **********************/
        $datosUnidades = $obj_venta->consultaUnidadesVentas();
        $datosUnidades = Excepciones::validaConsulta($datosUnidades);
        $datosUnidades = $datosUnidades == '' ? array() : $datosUnidades;
        if (count($datosUnidades) <= 0 and  (count($dataValidaRequi) > 0 and ($dataValidaRequi['totalAbastecidos'] == $dataValidaRequi['totalRequeri']))) {
            $obj_venta->errorBD('3|Error, la venta es cero, introduce artículos/elimina preventa', 0);
        }
        $articulosVendidos = $datosUnidades["articulosVendidos"];

        //echo "Articulos Vendidos: ".$articulosVendidos;
        if (
            $articulosVendidos <= '0' and
            (count($dataValidaRequi) > 0 and ($dataValidaRequi['totalAbastecidos'] == $dataValidaRequi['totalRequeri']))
        ) {
            $obj_venta->errorBD('3|Error, la venta es cero, introduce artículos/elimina preventa', 0);
        }
        /****************** VALIDAR QUE TODAS LAS VENTAS TENGAN DISTRIBUCION DE LOTES 'S'******************************/
        $dataValida = $obj_venta->validarDistribuicionLotes();
        if (!is_array($dataValida)) {
            $obj_venta->errorBD($dataValida, 0);
        }

        /**********************************************************************/
        if (count($dataValida) > 0 and $dataValida[0]['lotesNoValidos'] != '' and !$_Requisicion) {
            //Se hace json de retorno de la validacion
            $ArrayLotesNoValidados = explode(",", $dataValida[0]['lotesNoValidos']);
            $ArrayResult = array(
                "codigo" => "0",
                "message" => "Existen Lotes sin distribución",
                "lotes" => $ArrayLotesNoValidados
            );
            $json = json_encode($ArrayResult);

            echo $json;
            exit(0);
        }
        if (!$_Requisicion) {
            /*     $datos = $obj_venta->disminuirSubLotes();
            try {
                Excepciones::validaMsjError($datos);
            } catch (Exception $e) {
                $obj_venta->errorBD($e->getMessage(), 1);
            }*/
            $datos = $obj_venta->disminuirInventarioEmpacado();
            try {
                Excepciones::validaMsjError($datos);
            } catch (Exception $e) {
                $obj_venta->errorBD($e->getMessage(), 1);
            }
            $datos = $obj_venta->finalizarVenta();
            try {
                Excepciones::validaMsjError($datos);
            } catch (Exception $e) {
                $obj_venta->errorBD($e->getMessage(), 1);
            }
        } else {
            $datos = $obj_venta->finalizarVenta('3');
            try {
                Excepciones::validaMsjError($datos);
            } catch (Exception $e) {
                $obj_venta->errorBD($e->getMessage(), 1);
            }
        }




        $_SESSION['CRESuccessVenta'] = 'Venta Finalizada Correctamente.';

        echo '1|Venta Finalizada Correctamente.';

        break;
    case "guardaralmacen":
        $id = (!empty($_POST['id'])) ? trim($_POST['id']) : '';
        $cant = (isset($_POST['cant'])) ? trim($_POST['cant']) : '';
        $log = '';
        if ($id == '') {
            $ErrorLog .= ' Lote,';
            $log = '1';
        }
        if ($cant == '') {
            $ErrorLog .= ' Cantidad,';
            $log = '1';
        }
        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_venta->errorBD($ErrorLog, 0);
        }
        $datos = $obj_venta->paseAlmacenPT($id, $cant);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_venta->errorBD($e->getMessage(), 1);
        }
        echo '1| Registro de Almacén PT Correcto.';

        break;
    case "editarventa":
        $fechaFacturacion = (isset($_POST['fechaFacturacion'])) ? trim($_POST['fechaFacturacion']) : '';
        $motivo = (isset($_POST['motivo'])) ? trim($_POST['motivo']) : '';
        $numFactura = (isset($_POST['numFactura'])) ? trim($_POST['numFactura']) : '';
        $numPL = (isset($_POST['numPL'])) ? trim($_POST['numPL']) : '';
        $idTipoVenta =  (isset($_POST['idTipoVenta'])) ? trim($_POST['idTipoVenta']) : '';
        $requiereFact =  (isset($_POST['requiereFact'])) ? trim($_POST['requiereFact']) : '';
        $id =  (isset($_POST['id'])) ? trim($_POST['id']) : '';
        $log = '';
        if ($fechaFacturacion == '') {
            $ErrorLog .= ' fecha de Facturación,';
            $log = '1';
        }
        if ($motivo == '') {
            $ErrorLog .= ' Motivo,';
            $log = '1';
        }
        if ($numFactura == '' and $requiereFact == '1') {
            $ErrorLog .= ' Número de Factura,';
            $log = '1';
        }
        if ($id == '') {
            $ErrorLog .= ' Venta,';
            $log = '1';
        }
        if ($numPL == '') {
            $ErrorLog .= ' Número de PL,';
            $log = '1';
        }
        if ($idTipoVenta == '') {
            $ErrorLog .= ' Tipo de Venta,';
            $log = '1';
        }
        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_venta->errorBD($ErrorLog, 0);
        }
        $obj_venta->beginTransaction();
        $datos = $obj_venta->controlVentaHistorico($id, $motivo);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_venta->errorBD($e->getMessage(), 1);
        }

        $datos = $obj_venta->editarVenta($id, $fechaFacturacion, $numFactura, $numPL, $idTipoVenta);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_venta->errorBD($e->getMessage(), 1);
        }
        $obj_venta->insertarCommit();

        echo '1|Edición de Datos de la Venta Correcto.';

        break;
    case "guardarclasif":
        $codigo = (isset($_POST['codigo'])) ? trim($_POST['codigo']) : '';
        $cant = (isset($_POST['cant'])) ? trim($_POST['cant']) : '';
        $id =  (isset($_POST['id'])) ? trim($_POST['id']) : '';
        $log = '';
        if ($codigo == '') {
            $ErrorLog .= ' clasificación,';
            $log = '1';
        }
        if ($cant == '') {
            $ErrorLog .= ' Cantidad,';
            $log = '1';
        }
        if ($id == '') {
            $ErrorLog .= ' Venta,';
            $log = '1';
        }

        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_venta->errorBD($ErrorLog, 0);
        }
        $obj_venta->beginTransaction();
        $datos = Funciones::edicionBasica("detventas", $codigo, $cant, 'id', $id, $obj_venta->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_venta->errorBD($e->getMessage(), 1);
        }
        if ($codigo == "total_s" and $cant > 0) {
            $datos = Funciones::edicionBasica("detventas", "distribuido", "1", 'id', $id, $obj_venta->getConexion(), $debug);
            try {
                Excepciones::validaMsjError($datos);
            } catch (Exception $e) {
                $obj_venta->errorBD($e->getMessage(), 1);
            }
        } else if ($codigo == "total_s" and $cant <= 0) {
            $datos = Funciones::edicionBasica("detventas", "distribuido", "0", 'id', $id, $obj_venta->getConexion(), $debug);
            try {
                Excepciones::validaMsjError($datos);
            } catch (Exception $e) {
                $obj_venta->errorBD($e->getMessage(), 1);
            }
        }

        $obj_venta->insertarCommit();
        echo '1|Clasificación asignada a la venta.';

        break;
    case "consultarventa":
        $numFactura = (isset($_POST['numFactura'])) ? trim($_POST['numFactura']) : '';
        $idVenta = (!empty($_POST['idVenta'])) ? trim($_POST['idVenta']) : '';

        $log = '';
        if ($numFactura == '') {
            $ErrorLog .= ' num. Factura,';
            $log = '1';
        }

        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_venta->errorBD($ErrorLog, 0);
        }
        $filtradoVenta = $idVenta != '' ? "v.id!='$idVenta'" : "1=1";
        $datos = $obj_venta->busquedaVenta($numFactura, $filtradoVenta);
        if (!is_array($datos) and $datos != '') {
            echo '0|' . $datos;
        }
        $datos = $datos == "" ? array() : $datos;
        if (count($datos) > 0) {
            echo '0|Existe una Factura con el folio ' . $numFactura . '';
        } else {
            echo '1|Número de Factura correcto.';
        }
        break;
    case "consultarpl":
        $numPL = (isset($_POST['numPL'])) ? trim($_POST['numPL']) : '';
        $idVenta = (!empty($_POST['idVenta'])) ? trim($_POST['idVenta']) : '';

        $log = '';
        if ($numPL == '') {
            $ErrorLog .= ' num. PL,';
            $log = '1';
        }

        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_venta->errorBD($ErrorLog, 0);
        }
        $filtradoVenta = $idVenta != '' ? "v.id!='$idVenta'" : "1=1";

        $datos = $obj_venta->busquedaPL($numPL, $filtradoVenta);
        if (!is_array($datos) and $datos != '') {
            echo '0|' . $datos;
        }
        $datos = $datos == "" ? array() : $datos;
        if (count($datos) > 0) {
            echo '0|Existe un Número PL registrado con el folio ' . $numPL . '';
        } else {
            echo '1|Número de PL correcto.';
        }
        break;
    case "agregarsublotes":
        $idRendimiento = (isset($_POST['idRendimiento'])) ? trim($_POST['idRendimiento']) : '';
        $idDetVenta = (isset($_POST['idDetVenta'])) ? trim($_POST['idDetVenta']) : '';
        $DataSubLotes = $obj_venta->getSubLotes($idRendimiento, $idDetVenta);
        $ArrayLotesXSeleccion = array();
        foreach ($DataSubLotes as $key => $value) {
            $strinVar = "sublote_";
            $strinVar = $strinVar . $DataSubLotes[$key]['id'];
            $$strinVar = (isset($_POST[$strinVar])) ? trim($_POST[$strinVar]) : '0';
            array_push($ArrayLotesXSeleccion, [$DataSubLotes[$key]['id'], $$strinVar]);
        }
        $query = "";

        if (count($ArrayLotesXSeleccion) > 0) {
            foreach ($ArrayLotesXSeleccion as $key => $value) {
                if ($ArrayLotesXSeleccion[$key][1] > 0) {
                    $totalSets = $ArrayLotesXSeleccion[$key][1] / $pzasEnSet;
                    $query .= "('{$ArrayLotesXSeleccion[$key][0]}', '{$ArrayLotesXSeleccion[$key][1]}','{$totalSets}', 
                                NOW(), '$idUser', '$idDetVenta'),";
                }
            }
        }
        //Eliminar sublotes 
        $datos = Funciones::eliminarRegistro("detventaslotes", $idDetVenta, "idDetVenta", $obj_venta->getConexion(), $debug);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_venta->errorBD($e->getMessage(), 1);
        }
        //Agregar nuevos sublotes
        if ($query != '') {
            $query = substr($query, 0, -1);
            $datos = $obj_venta->addSubLote($query);
            try {
                Excepciones::validaMsjError($datos);
            } catch (Exception $e) {
                $obj_venta->errorBD($e->getMessage(), 1);
            }
        }
        //Agregar cantidad al detalle de la venta
        $datos = $obj_venta->agregarTotalDetVenta($idDetVenta);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_venta->errorBD($e->getMessage(), 1);
        }
        #Recalculo de cueros en ventas
        $datos = $obj_venta->actualizaCuerosVentas();
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_venta->errorBD($e->getMessage(), 1);
        }
        echo "1|Se efectuaron los cambios de los lotes.";

        break;
    case "agregarcajas":
        $idLote = (isset($_POST['idLote'])) ? trim($_POST['idLote']) : '';
        $cajas = (isset($_POST['cajas'])) ? $_POST['cajas'] : '';
        $idVenta = (isset($_POST['idVenta'])) ? $_POST['idVenta'] : '';

        #VALIDACION DE DATOS
        Excepciones::validaLlenadoDatos(array(
            " Lote" => $idLote,
            " Cajas" => $cajas,
            " Ventas" => $idVenta
        ), $obj_venta);
        $obj_venta->beginTransaction();
        foreach ($cajas as  $value) {
            $Array = explode('|', $value);
            $numCaja = $Array[0];
            $idEmpaque = $Array[1];
            //APARTAR CAJAS SELECCIONADAS: vendidas & ID de la venta 
            $datos = $obj_venta->seleccionarCajasParaVenta($numCaja, $idEmpaque, $idVenta);
            try {
                Excepciones::validaMsjError($datos);
            } catch (Exception $e) {
                $obj_venta->errorBD($e->getMessage(), 1);
            }
        }
        //INSERTAR DETALLADO DE UNIDADES POR LOTE: idVenta del detallado de cajas
        $datos = $obj_venta->insertarDetalladoCajas($idVenta);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_venta->errorBD($e->getMessage(), 1);
        }

        $datos = $obj_venta->actualizaUnidadesEnVenta($idVenta);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_venta->errorBD($e->getMessage(), 1);
        }
        #Recalculo de cueros en ventas
        $datos = $obj_venta->actualizaCuerosVentas();
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_venta->errorBD($e->getMessage(), 1);
        }
        $obj_venta->insertarCommit();
        echo "1|Cajas agregadas correctamente.";
        break;
    case "deshabilitarcajas":
        $idVenta = (isset($_POST['idVenta'])) ? trim($_POST['idVenta']) : '';
        $noCaja = (isset($_POST['noCaja'])) ? $_POST['noCaja'] : '';
        $idEmpaque = (isset($_POST['idEmpaque'])) ? $_POST['idEmpaque'] : '';
        #VALIDACION DE DATOS
        Excepciones::validaLlenadoDatos(array(
            " Venta" => $idVenta,
            " Caja" => $noCaja,
            " Empaque" => $idEmpaque
        ), $obj_venta);
        $obj_venta->beginTransaction();
        ///Elimina Detallado Venta
        $datos = $obj_venta->eliminaDetalladoVenta($idVenta);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_venta->errorBD($e->getMessage(), 1);
        }
        ///Deshabilita Cajas
        $datos = $obj_venta->deshabilitaCajas($noCaja, $idEmpaque, $idVenta);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_venta->errorBD($e->getMessage(), 1);
        }
        ///Actualiza Unidades
        $datos = $obj_venta->actualizaUnidadesEnVenta($idVenta);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_venta->errorBD($e->getMessage(), 1);
        }
        #Recalculo de cueros en ventas
        $datos = $obj_venta->actualizaCuerosVentas();
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_venta->errorBD($e->getMessage(), 1);
        }
        $obj_venta->insertarCommit();
        echo "1|Cajas deshabilitadas correctamente.";
        break;
    case "actualizarmetros":
        $idDetVenta = (!empty($_POST['idDetVenta'])) ? trim($_POST['idDetVenta']) : '';
        $idLote = (!empty($_POST['idLote'])) ? $_POST['idLote'] : '';
        $value = (!empty($_POST['value'])) ? $_POST['value'] : '';
        #VALIDACION DE DATOS
        Excepciones::validaLlenadoDatos(array(
            " Detalle de Venta" => $idDetVenta,
            " Lote" => $idLote,
            " Metros" => $value
        ), $obj_venta);
        $obj_venta->beginTransaction();
        #Actualizacion de Metros
        $datos = $obj_venta->actualizaMetros($idDetVenta, $value);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_venta->errorBD($e->getMessage(), 1);
        }

        #Recalculo de cueros en ventas
        $datos = $obj_venta->actualizaCuerosVentas();
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_venta->errorBD($e->getMessage(), 1);
        }
        $obj_venta->insertarCommit();
        echo "1|Metros de venta actualizados correctamente.";
        break;
    case "detalladocaja":
        $idEmpaque = (isset($_POST['idEmpaque'])) ? trim($_POST['idEmpaque']) : '';
        $numCaja = (isset($_POST['numCaja'])) ? trim($_POST['numCaja']) : '';

        $Data = $obj_venta->getDetalleCaja($idEmpaque, $numCaja);
        $Data = Excepciones::validaConsulta($Data);

        //Creamos el JSON
        $json_string = json_encode($Data);
        echo $json_string;
        break;
    case "getventasxlote":
        $id = (isset($_POST['id'])) ? trim($_POST['id']) : '';
        $Data = $obj_venta->getVentaXLote($id);
        $Data = Excepciones::validaConsulta($Data);

        //Creamos el JSON
        $json_string = json_encode($Data);
        echo $json_string;
        break;
}
