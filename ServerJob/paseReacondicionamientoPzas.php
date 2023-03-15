<?php
define('INCLUDE_CHECK', 1);
require_once __DIR__ . "/../config.php";
require_once  SITE_ROOT . '/PHPExcel/Classes/PHPExcel.php';
require_once SITE_ROOT . "/include/connect_mvc.php";
include(SITE_ROOT . '/Models/Mdl_ConexionBD.php');
include(SITE_ROOT . '/ModelsExcel/Mdl_LecturaXLS.php');
include(SITE_ROOT . '/ModelsExcel/Mdl_LecturaXLSReacond.php');
include(SITE_ROOT . '/Models/Mdl_Excepciones.php');

$debug = 0;
$idUser = 99;
error_reporting(E_ERROR | E_WARNING | E_PARSE);

$ruta = SITE_ROOT . "/doctos/MaterialReacondicionado";
$archivo = 'BitacoraMaterialReacondicionado.xlsx';
$rutaCompleta = $ruta . "/" . $archivo;
//VALIDAR QUE ARCHIVO EXISTA EN SERVIDOR
if (!file_exists($rutaCompleta)) {
    echo 'El archivo  "' . $name . '"  no existe.';
} else {
    $obj_LeerXLS = new LecturaXLSReacond($ruta . "/" . $archivo, $debug, $idUser);
    $dataExcel = $obj_LeerXLS->leerXLSReacond();
}
