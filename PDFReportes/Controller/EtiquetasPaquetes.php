<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once('../../include/connect_mvc.php');
require_once('../../PDFReportes/Libs/fpdf/mc_table.php');
require_once('../../PDFReportes/Models/Mdl_general.php');
include('../../assets/scripts/cadenas.php');

$debug = 0;
$idUser = $_SESSION['CREident'];

if ($debug == '1') {
    print_r($_GET);
}
$opcion = $_GET["op"];
switch ($opcion) {
    case 'getetiquetas':
        $idLote = (!empty($_GET['data'])) ? $_GET['data'] : '';
        include('Ctrl_EtiquetasPaquetes.php');
        getEtiquetaScrap();
        break;
}
?>