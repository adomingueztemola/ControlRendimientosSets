<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../include/connect_mvc.php";
include('../Models/Mdl_Static.php');
include('../assets/scripts/cadenas.php');

$debug = 0;
$idUser = $_SESSION['CREident'];

$obj_solicitudes = new Solicitud($debug, $idUser);

$ErrorLog = 'No se recibió';
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}

switch ($_GET["op"]) {
    case "getsolicitudesteseo":
        $Data = $obj_solicitudes->getSolicitudesEdicion();
        $Data = Excepciones::validaConsulta($Data);
        $json_string = json_encode($Data);
        echo $json_string;
        break;
    case "gethistsolicitud":
        $date_start = isset($_POST['date_start']) ? $_POST['date_start'] : '';
        $date_end = isset($_POST['date_end']) ? $_POST['date_end'] : '';
        $programa = isset($_POST['programa']) ? $_POST['programa'] : '';

        $filtradoPrograma = $programa != '' ? 'r.idCatPrograma=' . $programa . '' : '1=1';
        if ($date_start != "" and $date_end != "") {
            $filtradoFecha = "DATE_FORMAT(e.fechaEnvio, '%Y-%m-%d') BETWEEN '$date_start' AND '$date_end'";
        } else {
            $filtradoFecha = "1=1";
        }

        $Data = $obj_solicitudes->getSolicitudesEdicion(true, $filtradoFecha, $filtradoPrograma);
        $Data = Excepciones::validaConsulta($Data);
        $response = array();
        $count = 1;
        foreach ($Data as $value) {
            $_12Teseo = $value['_12Teseo'] == '' ? '0' : formatoMil($value['_12Teseo'], 0);
            $_3Teseo = $value['_3Teseo'] == '' ? '0' : formatoMil($value['_3Teseo'], 0);
            $_6Teseo = $value['_6Teseo'] == '' ? '0' : formatoMil($value['_6Teseo'], 0);
            $_9Teseo = $value['_9Teseo'] == '' ? '0' : formatoMil($value['_9Teseo'], 0);
            $pzasCortadasTeseo = $value['pzasCortadasTeseo'] == '' ? '0' : formatoMil($value['pzasCortadasTeseo'], 0);

            $yieldFinalReal = $value['yieldFinalReal'] == '' ? '0' : formatoMil($value['yieldFinalReal'], 2);
            $areaFinal = $value['areaFinal'] == '' ? '0' : formatoMil($value['areaFinal'], 2);
            $motivo=$value['motivo'] == '' ? 'N/A' : $value['motivo'];
            $motivo=
            array_push($response, [
                $count,
                $value['loteTemola'],
                $value['nPrograma'],
                $_12Teseo,
                $_3Teseo, 
                $_6Teseo, 
                $_9Teseo,
                $pzasCortadasTeseo,
                $yieldFinalReal."%",
                $areaFinal,
                $value["n_usuario"],
                $value["f_fechaEnvio"],
                $value["n_usuarioAtend"],
                $value["f_fechaAceptacion"],
                $value['estado'],
                $value['motivo']
            ]);
            $count++;
        }

        //Creamos el JSON
        $response = array("data" => $response);
        $json_string = json_encode($response);
        echo $json_string;

        break;
    case "getdetsolicitud":
        $id = (isset($_POST['id'])) ? trim($_POST['id']) : '';
        $Data = $obj_solicitudes->getDetSolicitud($id);
        $Data = Excepciones::validaConsulta($Data);
        $json_string = json_encode($Data);
        echo $json_string;
        break;
    case "enviarsolicitud":
        $idRendimiento = (isset($_POST['idRendimiento'])) ? trim($_POST['idRendimiento']) : '';
        $descripcionSolicitud = (isset($_POST['descripcionSolicitud'])) ? trim($_POST['descripcionSolicitud']) : '';
        $log = '';
        if ($idRendimiento == '') {
            $ErrorLog .= 'Rendimiento,';
            $log = '1';
        }
        if ($descripcionSolicitud == '') {
            $ErrorLog .= 'Descripción Solicitud,';
            $log = '1';
        }
        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_solicitudes->errorBD($ErrorLog, 0);
        }
        $obj_solicitudes->beginTransaction();
        $datos = $obj_solicitudes->agregarSolicitud($idRendimiento, $descripcionSolicitud);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_solicitudes->errorBD($e->getMessage(), 1);
        }

        $datos = $obj_solicitudes->actualizarSolicRendi($idRendimiento);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_solicitudes->errorBD($e->getMessage(), 1);
        }

        $obj_solicitudes->insertarCommit();
        echo '1|La solicitud de edición se ha enviado correctamente.';
        break;
    case "aceptar":
        $idSolicitud = (isset($_POST['idSolicitud'])) ? trim($_POST['idSolicitud']) : '';
        $log = '';
        if ($idSolicitud == '') {
            $ErrorLog .= 'Solicitud,';
            $log = '1';
        }
        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_solicitudes->errorBD($ErrorLog, 0);
        }
        $datos = $obj_solicitudes->aceptarSolicitud($idSolicitud);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_solicitudes->errorBD($e->getMessage(), 1);
        }
        echo '1|La solicitud de edición se ha aceptado correctamente.';
        break;
    case "cancelar":
        $idSolicitud = (isset($_POST['idSolicitud'])) ? trim($_POST['idSolicitud']) : '';
        $log = '';
        if ($idSolicitud == '') {
            $ErrorLog .= 'Solicitud,';
            $log = '1';
        }
        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_solicitudes->errorBD($ErrorLog, 0);
        }
        $datos = $obj_solicitudes->rechazarSolicitud($idSolicitud);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_solicitudes->errorBD($e->getMessage(), 1);
        }
        echo '1|La solicitud de edición se ha rechazado correctamente.';
        break;
    case "abriredicion":
        $id = (isset($_POST['id'])) ? trim($_POST['id']) : '';
        $log = '';
        if ($id == '') {
            $ErrorLog .= 'Rendimiento,';
            $log = '1';
        }
        if ($log == '1') {
            $ErrorLog .= ' intentalo de nuevo.';
            $obj_solicitudes->errorBD($ErrorLog, 0);
        }
        $obj_solicitudes->beginTransaction();
        $datos = $obj_solicitudes->abrirEdicion($id);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_solicitudes->errorBD($e->getMessage(), 1);
        }
        #Valida que los datos No Tengan Operaciones para eliminar Inventario y sublote
        $DataValidaUsoDelLote = $obj_solicitudes->validaCambioDePzas($id);
        $DataValidaUsoDelLote = $DataValidaUsoDelLote == '' ? array() : $DataValidaUsoDelLote;
        if (!is_array($DataValidaUsoDelLote)) {
            echo "0|Error, $DataValidaUsoDelLote";
        }

        if (count($DataValidaUsoDelLote) > 0) {
            $datos = $obj_solicitudes->limpiarInventarios($id);
            try {
                Excepciones::validaMsjError($datos);
            } catch (Exception $e) {
                $obj_solicitudes->errorBD($e->getMessage(), 1);
            }
        }
        $obj_solicitudes->insertarCommit();
        echo '1|La solicitud de edición se ha rechazado correctamente.';
        break;
    case "rechazarsolicitud":
        $id = (isset($_POST['id'])) ? trim($_POST['id']) : '';
        #VALIDACION DE DATOS
        Excepciones::validaLlenadoDatos(array(
            " Lote" => $id,
        ), $obj_solicitudes);
        $datos = $obj_solicitudes->rechazarSolicitudTeseo($id);
        try {
            Excepciones::validaMsjError($datos);
        } catch (Exception $e) {
            $obj_solicitudes->errorBD($e->getMessage(), 0);
        }
        echo '1|Solicitud Rechazada Correctamente.';

        break;
    case "aceptarsolicitud":
        $id = (isset($_POST['id'])) ? trim($_POST['id']) : '';
        $tipo = (isset($_POST['tipo'])) ? trim($_POST['tipo']) : '';
        $destino = (isset($_POST['destino'])) ? trim($_POST['destino']) : '';

        #VALIDACION DE DATOS
        Excepciones::validaLlenadoDatos(array(
            " Solicitud" => $id,
            " Tipo" => $tipo
        ), $obj_solicitudes);
        $Data = $obj_solicitudes->getDetSolicitud($id);
        $Data = Excepciones::validaConsulta($Data);
        $obj_solicitudes->beginTransaction();
        #TIPOS DE EDICIONES
        switch ($tipo) {
            case '1':
                # modificar datos solo de areas 
                $datos = $obj_solicitudes->setLoteTeseo($id);
                try {
                    Excepciones::validaMsjError($datos);
                } catch (Exception $e) {
                    $obj_solicitudes->errorBD($e->getMessage(), 1);
                }
                $datos = $obj_solicitudes->calcularRendimiento(0, false, $Data['idLote']);
                try {
                    Excepciones::validaMsjError($datos);
                } catch (Exception $e) {
                    $obj_solicitudes->errorBD($e->getMessage(), 1);
                }
                break;
            case '2':
                # movimiento positivo de las piezas de lote en proceso de empaque
                $datos = $obj_solicitudes->sumPzasOK(
                    $Data['idLote'],
                    $Data['dif_12'],
                    $Data['dif_3'],
                    $Data['dif_6'],
                    $Data['dif_9']
                );
                try {
                    Excepciones::validaMsjError($datos);
                } catch (Exception $e) {
                    $obj_solicitudes->errorBD($e->getMessage(), 1);
                }

                $datos = $obj_solicitudes->setLoteTeseo($id);
                try {
                    Excepciones::validaMsjError($datos);
                } catch (Exception $e) {
                    $obj_solicitudes->errorBD($e->getMessage(), 1);
                }
                $datos = $obj_solicitudes->setPzasLoteTeseo($id);
                try {
                    Excepciones::validaMsjError($datos);
                } catch (Exception $e) {
                    $obj_solicitudes->errorBD($e->getMessage(), 1);
                }
                $datos = $obj_solicitudes->calcularRendimiento(0, false, $Data['idLote']);
                try {
                    Excepciones::validaMsjError($datos);
                } catch (Exception $e) {
                    $obj_solicitudes->errorBD($e->getMessage(), 1);
                }
                break;
            case "3":
                # movimiento positivo de las piezas de lote en proceso de empaque finalizado
                Excepciones::validaLlenadoDatos(array(
                    " Destino" => $destino
                ), $obj_solicitudes);


                if ($destino == '1') {
                    //Sumar Dif. a Scrap 
                    $datos = $obj_solicitudes->sumScrap(
                        $Data['idLote'],
                        $Data['sum_12'],
                        $Data['sum_3'],
                        $Data['sum_6'],
                        $Data['sum_9']
                    );
                    try {
                        Excepciones::validaMsjError($datos);
                    } catch (Exception $e) {
                        $obj_solicitudes->errorBD($e->getMessage(), 1);
                    }
                } else if ($destino == '2') {
                    //Sumar Dif. a Pzas OK
                    $datos = $obj_solicitudes->sumPzasOK(
                        $Data['idLote'],
                        $Data['dif_12'],
                        $Data['dif_3'],
                        $Data['dif_6'],
                        $Data['dif_9']
                    );
                    try {
                        Excepciones::validaMsjError($datos);
                    } catch (Exception $e) {
                        $obj_solicitudes->errorBD($e->getMessage(), 1);
                    }
                    $datos = Funciones::cambiarEstatus("rendimientos", "0", "regEmpaque", $Data['idLote'],  $obj_solicitudes->getConexion(), $debug);
                    try {
                        Excepciones::validaMsjError($datos);
                    } catch (Exception $e) {
                        $obj_proceso->errorBD($e->getMessage(), 1);
                    }
                }
                $datos = $obj_solicitudes->setPzasLoteTeseo($id);
                try {
                    Excepciones::validaMsjError($datos);
                } catch (Exception $e) {
                    $obj_solicitudes->errorBD($e->getMessage(), 1);
                }

                $datos = $obj_solicitudes->setLoteTeseo($id);
                try {
                    Excepciones::validaMsjError($datos);
                } catch (Exception $e) {
                    $obj_solicitudes->errorBD($e->getMessage(), 1);
                }
                break;
            case "4":
                $datos = $obj_solicitudes->sumPzasOK(
                    $Data['idLote'],
                    $Data['dif_12'],
                    $Data['dif_3'],
                    $Data['dif_6'],
                    $Data['dif_9']
                );
                try {
                    Excepciones::validaMsjError($datos);
                } catch (Exception $e) {
                    $obj_solicitudes->errorBD($e->getMessage(), 1);
                }

                $datos = $obj_solicitudes->setPzasLoteTeseo($id);
                try {
                    Excepciones::validaMsjError($datos);
                } catch (Exception $e) {
                    $obj_solicitudes->errorBD($e->getMessage(), 1);
                }

                $datos = $obj_solicitudes->setLoteTeseo($id);
                try {
                    Excepciones::validaMsjError($datos);
                } catch (Exception $e) {
                    $obj_solicitudes->errorBD($e->getMessage(), 1);
                }
                //OBSERVAR SI LAS PIEZAS QUEDARAN EN CERO
                if (
                    $Data['dif_12'] == 0 and
                    $Data['dif_3'] == 0 and
                    $Data['dif_6'] == 0 and
                    $Data['dif_9'] == 0
                ) {
                    $datos = $obj_solicitudes->guardarTotal($Data['idLote']);
                    try {
                        Excepciones::validaMsjError($datos);
                    } catch (Exception $e) {
                        $obj_solicitudes->errorBD($e->getMessage(), 1);
                    }
                }
                break;
        }
        $obj_solicitudes->insertarCommit();
        echo '1|Solicitud Aceptada Correctamente.';
        break;
    case "getedicionesxlote":
        $id = (isset($_POST['id'])) ? trim($_POST['id']) : '';
        $Data= $obj_solicitudes->getEdicionesXLote($id);
        $Data= Excepciones::validaConsulta($Data);
       
        $json_string = json_encode($Data);
        echo $json_string;

        break;
}
