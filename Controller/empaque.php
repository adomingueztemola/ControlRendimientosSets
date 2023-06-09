<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../include/connect_mvc.php";
include('../assets/scripts/cadenas.php');

$debug = 0;
$idUser = $_SESSION['CREident'];
$obj_empaque = new Empaque($debug, $idUser);

$ErrorLog = 'No se recibió';
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}

switch ($_GET["op"]) {
    case "select2empaque":
        $programa = (isset($_POST['programa'])) ? trim($_POST['programa']) : '';
        if (!isset($_POST['palabraClave'])) {
            $Data = $obj_empaque->getEmpaqueSelect2("e.idCatPrograma='$programa'");
            $Data = Excepciones::validaConsulta($Data);
        } else {
            $search = $_POST['palabraClave']; // Palabra a buscar
            $Data = $obj_empaque->getEmpaqueSelect2("e.idCatPrograma='$programa'", $search);
            $Data = Excepciones::validaConsulta($Data);
        }
        $response = array();

        // Leer la informacion
        foreach ($Data as $area) {
            $response[] = array(
                "id" => $area['id'],
                "text" => $area['fFecha']
            );
        }

        //Creamos el JSON
        $json_string = json_encode($response);
        echo $json_string;
        break;
    case "cargajsonlotes":
        if (!isset($_GET['palabraClave'])) {
            $Data = $obj_empaque->getLotes();
            $Data = Excepciones::validaConsulta($Data);
        } else {
            $search = $_GET['palabraClave']; // Palabra a buscar
            $Data = $obj_empaque->getLotes($search);
            $Data = Excepciones::validaConsulta($Data);
        }
        $response = array();

        // Leer la informacion
        foreach ($Data as $lote) {
            $response[] = array(
                "id" => $lote['id'],
                "text" => $lote['loteTemola']
            );
        }

        //Creamos el JSON
        $json_string = json_encode($response);
        echo $json_string;
        break;
        /**************************
         * AGREGAR FECHA & PROGRAMA DE INICIO DE EMPAQUE
         **************************/
    case "agregarempaque":
        $fecha = (isset($_POST['fecha'])) ? trim($_POST['fecha']) : '';
        $programa = (isset($_POST['programa'])) ? trim($_POST['programa']) : '';
        #VALIDACION DE DATOS
        Excepciones::validaLlenadoDatos(array(
            " Fecha" => $fecha,
            " Programa" => $programa,
        ), $obj_empaque);
        #CONSULTA QUE NO EXISTA UN EMPAQUE DEL MISMO DIA CON EL MISMO PROGRAMA
        $Data = $obj_empaque->validaExistenciaEmpaque($fecha, $programa);
        $Data = Excepciones::validaConsulta($Data);
        if (count($Data) > 0) {
            $obj_empaque->errorBD("Existe un empaque asociado a los mismos datos, verifica la información.", 0);
        }

        $datos = $obj_empaque->agregarEmpaque($fecha, $programa);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_empaque->errorBD($e->getMessage(), 0);
        }
        echo "1|Empaque Registrado Correctamente.";
        break;
        /**************************
         * AGREGAR DATOS DE PIEZAS TESEO DE MANERA TEMPORAL
         **************************/
    case "actualizarteseo":
        $id = (isset($_POST['id'])) ? trim($_POST['id']) : '';
        $teseo = (isset($_POST['teseo'])) ? trim($_POST['teseo']) : '';
        #VALIDACION DE DATOS
        Excepciones::validaLlenadoDatos(array(
            " Lote" => $id,
            " Cantidad de Teseo" => $teseo,
        ), $obj_empaque);
        $obj_empaque->beginTransaction();
        $datos = $obj_empaque->actualizarTeseo($id, $teseo, 'pzasCortadasTeseo');
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_empaque->errorBD($e->getMessage(), 0);
        }
        /*   $datos = $obj_empaque->cierreRegistroTeseo($id);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_empaque->errorBD($e->getMessage(), 0);
        }*/
        $obj_empaque->insertarCommit();
        echo "1|Cantidad de Teseo Registrada Correctamente.";
        break;
    case "cierredatateseo":
        $id = (isset($_POST['id'])) ? trim($_POST['id']) : '';
        #VALIDACION DE DATOS
        Excepciones::validaLlenadoDatos(array(
            " Lote" => $id,
        ), $obj_empaque);
        #VALIDACION DE DATOS LLENOS A LA HORA DE CERRAR LOTE
        $Data = $obj_empaque->getRendimiento($id);
        $Data = Excepciones::validaConsulta($Data);
        $areaFinal = $Data['areaFinal'];
        $_12Teseo = $Data['_12Teseo'];
        $_3Teseo = $Data['_3Teseo'];
        $_6Teseo = $Data['_6Teseo'];
        $_9Teseo = $Data['_9Teseo'];
        $pzasCortadasTeseo = $Data['pzasCortadasTeseo'];
        $yieldInicialTeseo = $Data['yieldInicialTeseo'];
        $log = '0';
        $errores = [];
        if (empty($areaFinal)) {
            $log = '1';
            array_push($errores, "Área Teseo");
        }
        if (empty($_12Teseo)) {
            $log = '1';
            array_push($errores, "12:00 Teseo");
        }
        if (empty($_3Teseo)) {
            $log = '1';
            array_push($errores, "03:00 Teseo");
        }
        if (empty($_6Teseo)) {
            $log = '1';
            array_push($errores, "06:00 Teseo");
        }
        if (empty($_9Teseo)) {
            $log = '1';
            array_push($errores, "09:00 Teseo");
        }
        if (empty($pzasCortadasTeseo)) {
            $log = '1';
            array_push($errores, "Piezas Cortadas en Teseo");
        }
        if (empty($yieldInicialTeseo)) {
            $log = '1';
            array_push($errores, "Rendimiento en Teseo");
        }
        if ($log == '1') {
            $strErrores = implode(', ', $errores);
            $obj_empaque->errorBD("Verifique $strErrores ante de guardar, si el error persiste notifica a departamento de sistemas", 0);
        }

        #EJECUCION DE CIERRE DE DATOS DE TESEO PARA EMPACAR
        $datos = $obj_empaque->cierreRegistroTeseo($id);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_empaque->errorBD($e->getMessage(), 0);
        }
        echo "1|Cierre de Datos de Teseo Correcto.";

        break;
        /**************************
         * AGREGAR DATOS DE AREA TESEO DE MANERA TEMPORAL
         **************************/
    case "actualizararea":
        $id = (isset($_POST['id'])) ? trim($_POST['id']) : '';
        $teseo = (isset($_POST['teseo'])) ? trim($_POST['teseo']) : '';
        #VALIDACION DE DATOS
        Excepciones::validaLlenadoDatos(array(
            " Lote" => $id,
            " Área de Teseo" => $teseo,
        ), $obj_empaque);
        $obj_empaque->beginTransaction();
        $datos = $obj_empaque->actualizarTeseo($id, $teseo,  'areaFinal');
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_empaque->errorBD($e->getMessage(), 0);
        }
        /* $datos = $obj_empaque->cierreRegistroTeseo($id);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_empaque->errorBD($e->getMessage(), 0);
        }*/
        $obj_empaque->insertarCommit();
        echo "1|Área de Teseo Registrada Correctamente.";
        break;
        /**************************
         * AGREGAR DATOS DE YIELD TESEO DE MANERA TEMPORAL
         **************************/
    case "actualizaryield":
        $id = (isset($_POST['id'])) ? trim($_POST['id']) : '';
        $teseo = (isset($_POST['teseo'])) ? trim($_POST['teseo']) : '';
        #VALIDACION DE DATOS
        Excepciones::validaLlenadoDatos(array(
            " Lote" => $id,
            " Área de Teseo" => $teseo,
        ), $obj_empaque);
        $obj_empaque->beginTransaction();
        $datos = $obj_empaque->actualizarTeseo($id, $teseo, 'yieldInicialTeseo');
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_empaque->errorBD($e->getMessage(), 0);
        }

        /*   $datos = $obj_empaque->cierreRegistroTeseo($id);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_empaque->errorBD($e->getMessage(), 0);
        }*/
        $obj_empaque->insertarCommit();

        echo "1|Yield de Teseo Registrado Correctamente.";
        break;
        /**************************
         * AGREGAR DATOS DE 12:00 TESEO DE MANERA TEMPORAL
         **************************/
    case "actualizar_12teseo":
        $id = (isset($_POST['id'])) ? trim($_POST['id']) : '';
        $teseo = (isset($_POST['teseo'])) ? trim($_POST['teseo']) : '';
        #VALIDACION DE DATOS
        Excepciones::validaLlenadoDatos(array(
            " Lote" => $id,
            " Área de Teseo" => $teseo,
        ), $obj_empaque);
        $obj_empaque->beginTransaction();
        $datos = $obj_empaque->actualizarTeseo($id, $teseo, '_12Teseo');
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_empaque->errorBD($e->getMessage(), 0);
        }

        /*   $datos = $obj_empaque->cierreRegistroTeseo($id);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_empaque->errorBD($e->getMessage(), 0);
        }*/
        $obj_empaque->insertarCommit();

        echo "1|Piezas 12:00 de Teseo Registrado Correctamente.";
        break;
        /**************************
         * AGREGAR DATOS DE 03:00 TESEO DE MANERA TEMPORAL
         **************************/
    case "actualizar_3teseo":
        $id = (isset($_POST['id'])) ? trim($_POST['id']) : '';
        $teseo = (isset($_POST['teseo'])) ? trim($_POST['teseo']) : '';
        #VALIDACION DE DATOS
        Excepciones::validaLlenadoDatos(array(
            " Lote" => $id,
            " Área de Teseo" => $teseo,
        ), $obj_empaque);
        $obj_empaque->beginTransaction();
        $datos = $obj_empaque->actualizarTeseo($id, $teseo, '_3Teseo');
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_empaque->errorBD($e->getMessage(), 0);
        }

        // $datos = $obj_empaque->cierreRegistroTeseo($id);
        // try {
        //     Excepciones::validaMsjError($datos);
        // } catch (Exception $e) {
        //     $obj_empaque->errorBD($e->getMessage(), 0);
        // }
        $obj_empaque->insertarCommit();

        echo "1|Piezas 03:00 de Teseo Registrado Correctamente.";
        break;
        /**************************
         * AGREGAR DATOS DE 06:00 TESEO DE MANERA TEMPORAL
         **************************/
    case "actualizar_6teseo":
        $id = (isset($_POST['id'])) ? trim($_POST['id']) : '';
        $teseo = (isset($_POST['teseo'])) ? trim($_POST['teseo']) : '';
        #VALIDACION DE DATOS
        Excepciones::validaLlenadoDatos(array(
            " Lote" => $id,
            " Área de Teseo" => $teseo,
        ), $obj_empaque);
        $obj_empaque->beginTransaction();
        $datos = $obj_empaque->actualizarTeseo($id, $teseo, '_6Teseo');
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_empaque->errorBD($e->getMessage(), 0);
        }

        // $datos = $obj_empaque->cierreRegistroTeseo($id);
        // try {
        //     Excepciones::validaMsjError($datos);
        // } catch (Exception $e) {
        //     $obj_empaque->errorBD($e->getMessage(), 0);
        // }
        $obj_empaque->insertarCommit();

        echo "1|Piezas 06:00 de Teseo Registrado Correctamente.";
        break;
        /**************************
         * AGREGAR DATOS DE 09:00 TESEO DE MANERA TEMPORAL
         **************************/
    case "actualizar_9teseo":
        $id = (isset($_POST['id'])) ? trim($_POST['id']) : '';
        $teseo = (isset($_POST['teseo'])) ? trim($_POST['teseo']) : '';
        #VALIDACION DE DATOS
        Excepciones::validaLlenadoDatos(array(
            " Lote" => $id,
            " Área de Teseo" => $teseo,
        ), $obj_empaque);
        $obj_empaque->beginTransaction();
        $datos = $obj_empaque->actualizarTeseo($id, $teseo, '_9Teseo');
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_empaque->errorBD($e->getMessage(), 0);
        }

        // $datos = $obj_empaque->cierreRegistroTeseo($id);
        // try {
        //     Excepciones::validaMsjError($datos);
        // } catch (Exception $e) {
        //     $obj_empaque->errorBD($e->getMessage(), 0);
        // }
        $obj_empaque->insertarCommit();

        echo "1|Piezas 09:00 de Teseo Registrado Correctamente.";
        break;
    case "actualizar_hiderechazo":
        $id = (isset($_POST['id'])) ? trim($_POST['id']) : '';
        $teseo = (isset($_POST['teseo'])) ? trim($_POST['teseo']) : '';
        #VALIDACION DE DATOS
        Excepciones::validaLlenadoDatos(array(
            " Lote" => $id,
            " Hide Rechazados de Teseo" => $teseo,
        ), $obj_empaque);
        $obj_empaque->beginTransaction();
        $datos = $obj_empaque->actualizarTeseo($id, $teseo, 'hideRechTeseo');
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_empaque->errorBD($e->getMessage(), 0);
        }

        // $datos = $obj_empaque->cierreRegistroTeseo($id);
        // try {
        //     Excepciones::validaMsjError($datos);
        // } catch (Exception $e) {
        //     $obj_empaque->errorBD($e->getMessage(), 0);
        // }
        $obj_empaque->insertarCommit();
        echo "1|Hide(s) Rechazados en Teseo Registrado Correctamente.";

        break;
        /**************************
         * AGREGAR DETALLADO DE CAJA
         **************************/
    case "agregardetalle":
        $id = (isset($_POST['id'])) ? trim($_POST['id']) : '';
        $lote = (isset($_POST['lote'])) ? trim($_POST['lote']) : '';
        $caja = (isset($_POST['caja'])) ? trim($_POST['caja']) : '';
        $pzas_12 = (isset($_POST['pzas_12'])) ? trim($_POST['pzas_12']) : '';
        $pzas_03 = (isset($_POST['pzas_03'])) ? trim($_POST['pzas_03']) : '';
        $pzas_06 = (isset($_POST['pzas_06'])) ? trim($_POST['pzas_06']) : '';
        $pzas_09 = (isset($_POST['pzas_09'])) ? trim($_POST['pzas_09']) : '';
        $remanente = (isset($_POST['remanente'])) ? trim($_POST['remanente']) : '0';
        $lote0 = (isset($_POST['lote0'])) ? trim($_POST['lote0']) : '0';
        $completedCaja = (isset($_POST['completedCaja'])) ? trim($_POST['completedCaja']) : '0'; //caja cerrada o abierta
        #VALORES UNICOS PARA REMANENTES 
        $pzas_12_rem = $remanente == '1' ? $pzas_12 : '0';
        $pzas_03_rem = $remanente == '1' ? $pzas_03 : '0';
        $pzas_06_rem = $remanente == '1' ? $pzas_06 : '0';
        $pzas_09_rem = $remanente == '1' ? $pzas_09 : '0';
        $jsonMixCaja = "";

        #VALIDACION DE DATOS
        Excepciones::validaLlenadoDatos(array(
            " Empaque" => $id,
            " Lote" => $lote,
            " Num. Caja" => $caja,
            " Piezas 12:00" => $pzas_12,
            " Piezas 03:00" => $pzas_03,
            " Piezas 06:00" => $pzas_06,
            " Piezas 09:00" => $pzas_09,

        ), $obj_empaque);

        $obj_empaque->beginTransaction();

        //DESGLOSE DE INFORMACION DEL LOTE
        $ArrayLote = explode('|', $lote);
        $lote = $ArrayLote[0];
        $tipoLote = $ArrayLote[1];
        $idDetCaja = $ArrayLote[2];
        //Sumar Total Piezas por Detallado
        $total = $pzas_12 + $pzas_03 + $pzas_06 + $pzas_09;
        if ($total <= 0 and $remanente != '1') {
            $obj_empaque->errorBD("Error, la caja registrada está vacía, válida tu información.", 1);
        }
        //PROCESO DE LOS DATOS
        $caja = $remanente == '1' ? '0' : $caja;
        //VALIDA QUE SEA EL CIERRE DEL LOTE POR CONTEO DE PZAS OK
        $cierre = $remanente;
        if ($tipoLote == '1') {
            $Data = $obj_empaque->getDatosTeseo($lote);
            $Data = Excepciones::validaConsulta($Data);
            $regDatos = $Data['regDatos'];
            //SI EL LOTE CIERRA EN SU TOTALIDAD DE PIEZAS
            if ($Data['pzasOk'] == $Data['pzasEmp'] + $total) {
                $cierre = '1';
            }
            //SI EL LOTE QUEDA VACIO POR QUE ES UN ADJUNTO DE PIEZAS
            if (($Data['_12OKAct'] - $pzas_12 == 0) &&
                ($Data['_3OKAct'] - $pzas_03 == 0) &&
                ($Data['_6OKAct'] - $pzas_06 == 0) &&
                ($Data['_9OKAct'] - $pzas_09 == 0)
            ) {
                $cierre = '1';
            }
            if ($remanente == '1') {
                //VERIFICA QUE EL REMANENTE NO SOBRE PASE LAS PZAS OK
                if ($Data['pzasOk'] < $Data['pzasEmp'] + $total) {
                    $obj_empaque->errorBD("El remanente capturado, excede el limite requerido.", 1);
                }
            }
        }
        //VALIDA PIEZAS EN TIEMPO REAL
        $_12Valida = 0;
        $_6Valida = 0;
        $_3Valida = 0;
        $_9Valida = 0;

        switch ($tipoLote) {
            case '1':
                $Data = $obj_empaque->getRendimiento($lote);
                $Data = Excepciones::validaConsulta($Data);
                $_12Valida = $Data['_12OKAct'] == '' ? '0' : $Data['_12OKAct'];
                $_6Valida = $Data['_6OKAct'] == '' ? '0' : $Data['_6OKAct'];
                $_3Valida = $Data['_3OKAct'] == '' ? '0' : $Data['_3OKAct'];
                $_9Valida = $Data['_9OKAct'] == '' ? '0' : $Data['_9OKAct'];
                break;
            case '2':
                $Data = $obj_empaque->getRemanenteXLote($lote);
                $Data = Excepciones::validaConsulta($Data);
                $_12Valida = $Data['_12Rem'] == '' ? '0' : $Data['_12Rem'];
                $_6Valida = $Data['_6Rem'] == '' ? '0' : $Data['_6Rem'];
                $_3Valida = $Data['_3Rem'] == '' ? '0' : $Data['_3Rem'];
                $_9Valida = $Data['_9Rem'] == '' ? '0' : $Data['_9Rem'];
                break;
            case '3':
                $Data = $obj_empaque->getStockRecuperacionXLote($lote);
                $Data = Excepciones::validaConsulta($Data);
                $_12Valida = $Data['_12'] == '' ? '0' : $Data['_12'];
                $_6Valida = $Data['_6'] == '' ? '0' : $Data['_6'];
                $_3Valida = $Data['_3'] == '' ? '0' : $Data['_3'];
                $_9Valida = $Data['_9'] == '' ? '0' : $Data['_9'];
                break;
        }
        $validaPzas = true;
        if (($_12Valida < $pzas_12) ||  ($_6Valida < $pzas_06) ||  ($_3Valida < $pzas_03) ||  ($_9Valida < $pzas_09)) {
            $validaPzas = false;
        }
        if (!$validaPzas) {
            $obj_empaque->errorBD("Sin suficientes piezas para solventar su caja", 1);
        }
        //REGISTRAMOS EL DETALLADO DE LA CAJA
        //Validar que la caja no vaya en 0
        if ($total > 0) {
            $datos = $obj_empaque->registraDetCaja(
                $id,
                $lote,
                $caja,
                $pzas_12,
                $pzas_03,
                $pzas_06,
                $pzas_09,
                $remanente,
                $tipoLote,
                $total,
                $pzas_12_rem,
                $pzas_03_rem,
                $pzas_06_rem,
                $pzas_09_rem,
                $lote0
            );
            try {
                Excepciones::validaMsjError($datos);
            } catch (Exception $e) {
                $obj_empaque->errorBD($e->getMessage(), 1);
            }
            //OBTENEMOS EL REGISTRO DEL DETALLADO DE LA CAJA
            $idNew = $datos['2'];
        }

        //SI ES EMPAQUE DE LOTE EN LINEA, QUITA PZAS OK DEL LOTE SOLO 
        //SI EL DETALLADO DE CAJA NO ES REMANENTE
        if ($tipoLote == '1' and  $remanente != '1') {
            $datos = $obj_empaque->actualizarUsoPzasOK($idNew);
            try {
                Excepciones::validaMsjError($datos);
            } catch (Exception $e) {
                $obj_empaque->errorBD($e->getMessage(), 1);
            }
            if ($regDatos == '1') {
                //AJUSTAR STOCK DE EMPAQUE EN CASO QUE YA SE HAYA CREAODO EL REGISTRO DE DATOS 
                $datos = $obj_empaque->actualizarStckEmpaque($idNew);
                try {
                    Excepciones::validaMsjError($datos);
                } catch (Exception $e) {
                    $obj_empaque->errorBD($e->getMessage(), 1);
                }
            }
        }
        //USA REMANENTE PARA QUE YA NO PUEDA SER USADOS
        if ($tipoLote == '2') {
            $datos = $obj_empaque->actualizarUsoRemanante($lote);
            try {
                Excepciones::validaMsjError($datos);
            } catch (Exception $e) {
                $obj_empaque->errorBD($e->getMessage(), 1);
            }
            $datos = $obj_empaque->actualizarPzasRemanante($idDetCaja, $idNew);
            try {
                Excepciones::validaMsjError($datos);
            } catch (Exception $e) {
                $obj_empaque->errorBD($e->getMessage(), 1);
            }
            //AJUSTAR STOCK DE EMPAQUE EN CASO QUE YA SE HAYA CREAODO EL REGISTRO DE DATOS 
            $datos = $obj_empaque->actualizarStckEmpaque($idNew);
            try {
                Excepciones::validaMsjError($datos);
            } catch (Exception $e) {
                $obj_empaque->errorBD($e->getMessage(), 1);
            }
            //AJUSTAR UNIDADES DE EMPAQUE EN DADO CASO QUE YA SE HAYA CERRADO EL LOTE EN EMPAQUE
            $datos = $obj_empaque->actualizarUnidadesEmpaque($idNew);
            try {
                Excepciones::validaMsjError($datos);
            } catch (Exception $e) {
                $obj_empaque->errorBD($e->getMessage(), 1);
            }
        }
        //DESCUENTA DE RECUPERACION
        if ($tipoLote == '3') {
            #DISMINUYE STOCK DE LA RECUPERACION
            $datos = $obj_empaque->disminuirInventarioRecuperacion($lote, $pzas_12, $pzas_03, $pzas_06, $pzas_09, $total);
            try {
                Excepciones::validaMsjError($datos);
            } catch (Exception $e) {
                $obj_empaque->errorBD($e->getMessage(), 1);
            }
            #ACTUALIZA KPI POR INGRESO DE MATERIA EMPACADA
            $datos = $obj_empaque->actualizaRendimiento($idNew);
            try {
                Excepciones::validaMsjError($datos);
            } catch (Exception $e) {
                $obj_empaque->errorBD($e->getMessage(), 1);
            }

            //AJUSTAR STOCK DE EMPAQUE EN CASO QUE YA SE HAYA CREAODO EL REGISTRO DE DATOS 
            $datos = $obj_empaque->actualizarStckEmpaque($idNew);
            try {
                Excepciones::validaMsjError($datos);
            } catch (Exception $e) {
                $obj_empaque->errorBD($e->getMessage(), 1);
            }
            //AJUSTAR UNIDADES DE EMPAQUE EN DADO CASO QUE YA SE HAYA CERRADO EL LOTE EN EMPAQUE
            if ($lote0 == '0') {
                $datos = $obj_empaque->actualizarUnidadesEmpaque($idNew);
                try {
                    Excepciones::validaMsjError($datos);
                } catch (Exception $e) {
                    $obj_empaque->errorBD($e->getMessage(), 1);
                }
            } else if ($lote0 == '1') {
                $datos = $obj_empaque->actualizarUnidadesLote0($idNew);
                try {
                    Excepciones::validaMsjError($datos);
                } catch (Exception $e) {
                    $obj_empaque->errorBD($e->getMessage(), 1);
                }
            }
        }
        //AJUSTAR KPIS DE RENDIMIENTO AL AJUSTAR CANTIDAD
        $datos = $obj_empaque->calcularRendimiento($lote);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_empaque->errorBD($e->getMessage(), 1);
        }
        $labelActive = '0';
        //Si la caja no esta completa validar que el ingreso solvente el cierrre & pedir la etiqueta
        if ($completedCaja == 0) {
            $Data = $obj_empaque->getDetCaja($id, $caja);
            $Data = Excepciones::validaConsulta($Data);
            $jsonMixCaja = "";
            $total = 0;
            foreach ($Data as $value) {
                if ($jsonMixCaja != '') {
                    $jsonMixCaja .= '%';
                }
                $jsonMixCaja = $jsonMixCaja . "[" . $value['loteTemola'] . ',' . $value['idLote'] . "]";
                $total = $total + $value["total"];
            }
            if ($total >= 400) {
                $labelActive = '1';
            }
        }

        $obj_empaque->insertarCommit();

        echo "1|Detalle de Empaque Agregado Correctamente.|" . $cierre . "|" . $lote . "|" . $labelActive . "|" . $jsonMixCaja;

        break;

    case "guardartotal":
        $idLote = (isset($_POST['idLote'])) ? trim($_POST['idLote']) : '';
        $idEmpaque = (isset($_POST['idEmpaque'])) ? trim($_POST['idEmpaque']) : '';
        #VALIDACION DE DATOS
        Excepciones::validaLlenadoDatos(array(
            " Lote" => $idLote,
            " Empaque" => $idEmpaque
        ), $obj_empaque);
        $obj_empaque->beginTransaction();
        #CONSULTA COMO QUEDA EL EMPAQUE
        /*$Data = $obj_empaque->getDatosTeseo($idLote);
        $Data = Excepciones::validaConsulta($Data);
        if ($Data['pzasCortadasTeseo'] - $Data['pzasEmp'] > 0) {
            $datos = $obj_empaque->agregarInventarioRechazo($idLote);
            try {
                Excepciones::validaMsjError($datos);
            } catch (Exception $e) {
                $obj_empaque->errorBD($e->getMessage(), 1);
            }
        }*/
        #CONSULTA ALMACENA STOCK DE SCRAP
        $Stk = $obj_empaque->getStkScrap($idLote);
        $Stk = Excepciones::validaConsulta($Stk);
        if (count($Stk) > 0) {        //Actualiza Stock de piezas scrap
            $datos = $obj_empaque->agregarPzasScrap($idLote);
            try {
                Excepciones::validaMsjError($datos);
            } catch (Exception $e) {
                $obj_empaque->errorBD($e->getMessage(), 1);
            }
        } else {                    //Inserta Stock de piezas scrap
            $datos = $obj_empaque->agregarStkPzasScrap($idLote);
            try {
                Excepciones::validaMsjError($datos);
            } catch (Exception $e) {
                $obj_empaque->errorBD($e->getMessage(), 1);
            }
        }
        $datos = $obj_empaque->guardarTotal($idLote, $idEmpaque);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_empaque->errorBD($e->getMessage(), 1);
        }
        $obj_empaque->insertarCommit();

        echo "1|Detalle de Total Agregado Correctamente.";
        break;

    case "cancelartotal":
        $idLote = (isset($_POST['idLote'])) ? trim($_POST['idLote']) : '';
        $idEmpaque = (isset($_POST['idEmpaque'])) ? trim($_POST['idEmpaque']) : '';
        #VALIDACION DE DATOS
        Excepciones::validaLlenadoDatos(array(
            " Lote" => $idLote,
            " Empaque" => $idEmpaque
        ), $obj_empaque);

        $datos = $obj_empaque->eliminarRemanenteLote($idLote, $idEmpaque);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_empaque->errorBD($e->getMessage(), 1);
        }
        echo "1|Cancelación de Remanente Correctamente.";
        break;
    case "cambiarcajainterna":
        $numCaja = (isset($_POST['numCaja'])) ? trim($_POST['numCaja']) : '';
        $interno = (isset($_POST['interno'])) ? trim($_POST['interno']) : '';
        $idEmpaque = (isset($_POST['idEmpaque'])) ? trim($_POST['idEmpaque']) : '';
        #VALIDACION DE DATOS
        Excepciones::validaLlenadoDatos(array(
            " Num. Caja" => $numCaja,
            " Interno" => $interno,
            " Empaque" => $idEmpaque

        ), $obj_empaque);
        //cambiar caja a caja interna
        $datos = $obj_empaque->actualizarInternoEnCaja($numCaja, $idEmpaque, $interno);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_empaque->errorBD($e->getMessage(), 1);
        }
        echo "1|Cambio Correcto de Trazabilidad de Caja.";
        break;
    case "agregarlabel":
        $label = (isset($_GET['label'])) ? trim($_GET['label']) : '';
        $id = (isset($_POST['id'])) ? trim($_POST['id']) : '';
        $caja = (isset($_POST['caja'])) ? trim($_POST['caja']) : '';
        #VALIDACION DE DATOS
        Excepciones::validaLlenadoDatos(array(
            " Num. Caja" => $caja,
            " Empaque" => $id,
            " Etiqueta" => $label
        ), $obj_empaque);
        //cambiar caja a caja interna
        $datos = $obj_empaque->ingresarLabelCaja($caja, $label, $id);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_empaque->errorBD($e->getMessage(), 1);
        }
        echo "1|Ingreso Correcto de Etiqueta de Caja.";


        break;
    case "reverseremanente":
        $paseScrap = (isset($_POST['paseScrap'])) ? trim($_POST['paseScrap']) : '0';
        $idLote = (isset($_POST['idLote'])) ? trim($_POST['idLote']) : '';
        $_12 = (isset($_POST['_12'])) ? trim($_POST['_12']) : '0';
        $_3 = (isset($_POST['_3'])) ? trim($_POST['_3']) : '0';
        $_6 = (isset($_POST['_6'])) ? trim($_POST['_6']) : '0';
        $_9 = (isset($_POST['_9'])) ? trim($_POST['_9']) : '0';
        #VALIDACION DE DATOS
        Excepciones::validaLlenadoDatos(array(
            " Lote" => $idLote,
        ), $obj_empaque);
        $obj_empaque->beginTransaction();
        if ($paseScrap == '0') { #Si el pase de scrap es 0 disminuir stock
            $datos = $obj_empaque->aumentoStckReverseRem($idLote, $_12, $_3, $_6, $_9);
            try {
                Excepciones::validaMsjError($datos);
            } catch (Exception $e) {
                $obj_empaque->errorBD($e->getMessage(), 1);
            }
        } else { #Si el pase de scrap es 1 aumentar scrap en lote 0 alm

            $datos = $obj_empaque->aumentoDetTarimaReverseRem($idLote, $_12, $_3, $_6, $_9);
            try {
                Excepciones::validaMsjError($datos);
            } catch (Exception $e) {
                $obj_empaque->errorBD($e->getMessage(), 1);
            }
        }
        #Guardar operacion de disminucion desde el empaque
        $datos = $obj_empaque->agregarDetReverse($idLote, $_12, $_3, $_6, $_9);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_empaque->errorBD($e->getMessage(), 1);
        }
        $obj_empaque->insertarCommit();
        echo "1|Reverse de remanente disminuido correctamente.";

        break;
    case "disminuirrecuperacion":
        $idLote = (isset($_POST['idLote'])) ? trim($_POST['idLote']) : '';
        $paseScrap = (isset($_POST['paseScrap'])) ? trim($_POST['paseScrap']) : '0';
        $_12 = (isset($_POST['_12'])) ? trim($_POST['_12']) : '0';
        $_3 = (isset($_POST['_3'])) ? trim($_POST['_3']) : '0';
        $_6 = (isset($_POST['_6'])) ? trim($_POST['_6']) : '0';
        $_9 = (isset($_POST['_9'])) ? trim($_POST['_9']) : '0';
        #VALIDACION DE DATOS
        Excepciones::validaLlenadoDatos(array(
            " Lote" => $idLote,
        ), $obj_empaque);

        $datos = $obj_empaque->disminuirStkRecu($idLote, $_12, $_3, $_6, $_9);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_empaque->errorBD($e->getMessage(), 1);
        }

        if ($paseScrap == '0') { #Si el pase de scrap es 0 disminuir stock
            $datos = $obj_empaque->aumentoStckReverseRem($idLote, $_12, $_3, $_6, $_9);
            try {
                Excepciones::validaMsjError($datos);
            } catch (Exception $e) {
                $obj_empaque->errorBD($e->getMessage(), 1);
            }
        } else { #Si el pase de scrap es 1 aumentar scrap en lote 0 alm

            $datos = $obj_empaque->aumentoDetTarimaReverseRem($idLote, $_12, $_3, $_6, $_9);
            try {
                Excepciones::validaMsjError($datos);
            } catch (Exception $e) {
                $obj_empaque->errorBD($e->getMessage(), 1);
            }
        }
        echo "1|Reverse de recuperación disminuido correctamente.";

        break;
    case "getcajasempacadas":
        $Data = $obj_empaque->getStockCajas();
        $Data = Excepciones::validaConsulta($Data);
        $response = array();
        foreach ($Data as $value) {
            array_push($response, [
                $value['loteTemola'], $value['nPrograma'],
                formatoMil($value['cantCaja'], 0)
            ]);
            $count++;
        }
        //Creamos el JSON
        $response = array("data" => $response);
        $json_string = json_encode($response);
        echo $json_string;
        break;
    case "cambiarlote0":
        $numCaja = (isset($_POST['numCaja'])) ? trim($_POST['numCaja']) : '';
        $lote0 = (isset($_POST['lote0'])) ? trim($_POST['lote0']) : '0';
        $idEmpaque = (isset($_POST['idEmpaque'])) ? trim($_POST['idEmpaque']) : '';
        #VALIDACION DE DATOS
        Excepciones::validaLlenadoDatos(array(
            " Num. Caja" => $numCaja,
            " Lote 0" => $lote0,
            " Empaque" => $idEmpaque

        ), $obj_empaque);
        $obj_empaque->beginTransaction();
        //cambiar caja a caja lote 0
        $datos = $obj_empaque->actualizarLote0EnCaja($numCaja, $idEmpaque, $lote0);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_empaque->errorBD($e->getMessage(), 1);
        }
        if ($lote0 == '1') {
            $datos = $obj_empaque->aumentarPzasLote0($numCaja, $idEmpaque, $lote0);
            try {
                Excepciones::validaMsjError($datos);
            } catch (Exception $e) {
                $obj_empaque->errorBD($e->getMessage(), 1);
            }
        } else {
            $datos = $obj_empaque->disminuirPzasLote0($numCaja, $idEmpaque, $lote0);
            try {
                Excepciones::validaMsjError($datos);
            } catch (Exception $e) {
                $obj_empaque->errorBD($e->getMessage(), 1);
            }
        }
        $obj_empaque->insertarCommit();
        echo "1|Cambio Correcto de Origen de Caja.";
        break;
    case "eliminarcaja":
        $numCaja = (isset($_POST['numCaja'])) ? trim($_POST['numCaja']) : '';
        $idError = (isset($_POST['idError'])) ? trim($_POST['idError']) : '0';
        $idEmpaque = (isset($_POST['idEmpaque'])) ? trim($_POST['idEmpaque']) : '';
        #VALIDACION DE DATOS
        Excepciones::validaLlenadoDatos(array(
            " Num. Caja" => $numCaja,
            " Error" => $idError,
            " Empaque" => $idEmpaque

        ), $obj_empaque);
        //CONSULTAR REGISTROS QUE CONFORMAN LAS CAJAS
        $DetCaja = $obj_empaque->getDetCaja($idEmpaque, $numCaja);
        $DetCaja = Excepciones::validaConsulta($DetCaja);
        $obj_empaque->beginTransaction();
        foreach ($DetCaja as $value) {
            //RECORRE EL CONTENIDO DE LA CAJA
            switch ($value['tipo']) {
                case '1':
                    # PIEZAS DE UN SOLO LOTE
                    /*1- PASAR PZAS A PZAS ACTUALES  */
                    /*2- QUITAR PIEZAS DE TOTAL DE EMPAQUE DE LOTE   */
                    /*3- QUITAR PIEZAS DE INVENTARIO DE LOTE   */
                    /*4- ABRIR REGISTRO DE EMPAQUE EN EL LOTE */
                    $datos = $obj_empaque->traspasarPzasCjaLote($value['id']);
                    try {
                        Excepciones::validaMsjError($datos);
                    } catch (Exception $e) {
                        $obj_empaque->errorBD($e->getMessage(), 1);
                    }

                    break;
                case '2':
                    # PIEZAS DE UN REMANENTE
                    /*1- PASAR PZAS A PZAS A REMANENTE DE LOTE  */
                    /*2- QUITAR PIEZAS DE TOTAL DE EMPAQUE DE LOTE   */
                    /*3- QUITAR PIEZAS DE INVENTARIO DE LOTE   */
                    /*4- ABRIR USO DE REMANENTE EN EL LOTE */
                    $datos = $obj_empaque->traspasarPzasRemLote($value['id']);
                    try {
                        Excepciones::validaMsjError($datos);
                    } catch (Exception $e) {
                        $obj_empaque->errorBD($e->getMessage(), 1);
                    }
                    break;
                case '3':
                    # PIEZAS DE RECUPERACION
                    /*1- PASAR PZAS A PZAS A INVENTARIO DE RECUPERACION DE LOTE  */
                    /*2- QUITAR PIEZAS DE TOTAL DE EMPAQUE DE LOTE   */
                    /*3- QUITAR PIEZAS DE INVENTARIO DE LOTE   */
                    /*4- ABRIR USO DE REMANENTE EN EL LOTE */
                    $datos = $obj_empaque->traspasarPzasRecuLote($value['id']);
                    try {
                        Excepciones::validaMsjError($datos);
                    } catch (Exception $e) {
                        $obj_empaque->errorBD($e->getMessage(), 1);
                    }
                    break;
                default:
                    # PIEZAS DE UN SOLO LOTE

                    break;
            }
            /*5- RECALCULAR LOTE  */
            $datos = $obj_empaque->calcularRendimiento($value['idLote']);
            try {
                Excepciones::validaMsjError($datos);
            } catch (Exception $e) {
                $obj_empaque->errorBD($e->getMessage(), 1);
            }
        }
        /*6- DEPURACION DE REGISTRO  */
        $datos = $obj_empaque->registroDepuracion($idEmpaque, $numCaja, $idError);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_empaque->errorBD($e->getMessage(), 1);
        }
        /*6- Reconteo de Cajas  */
        $datos = $obj_empaque->reconteoCajas($idEmpaque, $numCaja);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_empaque->errorBD($e->getMessage(), 1);
        }
        $obj_empaque->insertarCommit();
        echo "1|Eliminar Caja de Empaque Correcta.";
        break;
    case "gethistdepuracion":
        $date_start = isset($_POST['date_start']) ? $_POST['date_start'] : '';
        $date_end = isset($_POST['date_end']) ? $_POST['date_end'] : '';
        $programa = isset($_POST['programa']) ? $_POST['programa'] : '';

        $filtradoPrograma = $programa != '' ? 'r.idCatPrograma=' . $programa . '' : '1=1';
        if ($date_start != "" and $date_end != "") {
            $filtradoFecha = "DATE_FORMAT(e.fecha, '%Y-%m-%d') BETWEEN '$date_start' AND '$date_end'";
        } else {
            $filtradoFecha = "1=1";
        }
        $Data = $obj_empaque->getCajasDepuradas($filtradoPrograma, $filtradoFecha);
        $Data = Excepciones::validaConsulta($Data);
        $response = array();
        $count = 1;
        foreach ($Data as $value) {
            $_12 = $value['_12'] == '' ? '0' : formatoMil($value['_12'], 0);
            $_3 = $value['_3'] == '' ? '0' : formatoMil($value['_3'], 0);
            $_6 = $value['_6'] == '' ? '0' : formatoMil($value['_6'], 0);
            $_9 = $value['_9'] == '' ? '0' : formatoMil($value['_9'], 0);
            $total = $value['total'] == '' ? '0' : formatoMil($value['total'], 0);
            array_push($response, [
                $count,
                $value['loteTemola'],
                $value['nPrograma'],
                $value['f_fechaEmpaque'],
                $value['numCaja'],
                $value['nTipo'],
                $_12,
                $_3,
                $_6,
                $_9,
                $total,
                $value['f_fechaDepuracion'],
                $value['nUsuarioDep'],
                $value['nError']
            ]);
            $count++;
        }

        //Creamos el JSON
        $response = array("data" => $response);
        $json_string = json_encode($response);
        echo $json_string;
        break;
    case "cambioempaque":
        $numCaja = isset($_POST['numCaja']) ? $_POST['numCaja'] : '';
        $idEmpaque = isset($_POST['idEmpaque']) ? $_POST['idEmpaque'] : '';
        $idEmpaqueN = isset($_POST['idEmpaqueN']) ? $_POST['idEmpaqueN'] : '';
        #VALIDACION DE DATOS
        Excepciones::validaLlenadoDatos(array(
            " Num. Caja" => $numCaja,
            " Empaque" => $idEmpaque,
            " Empaque Nuevo" => $idEmpaqueN
        ), $obj_empaque);
        $DataCaja = $obj_empaque->consultaLlenadoCaja($idEmpaqueN);
        $DataCaja = Excepciones::validaConsulta($DataCaja);
        $cajaSiguiente = $DataCaja["cajaSiguiente"];
        $obj_empaque->beginTransaction();
       
        $datos = $obj_empaque->cambiarNumCaja($numCaja, $idEmpaque, $cajaSiguiente, $idEmpaqueN);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_empaque->errorBD($e->getMessage(), 1);
        }
        $datos = $obj_empaque->reconteoCajas($idEmpaque, $numCaja);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_empaque->errorBD($e->getMessage(), 1);
        }

        $obj_empaque->insertarCommit();
        echo "1|Se cambió empaque de caja correctamente.";
        break;
}
