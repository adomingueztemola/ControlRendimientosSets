<?php
define('INCLUDE_CHECK', 1);
require_once "../../include/connect_mvc.php";
include('../../Models/Mdl_ConexionBD.php');
include('../../Models/Mdl_VentaPrevia.php');
include('../../Models/Mdl_Excepciones.php');
include("../../assets/scripts/cadenas.php");
session_start();
setlocale(LC_TIME, 'es_ES.UTF-8');
$debug = 0;
$space = 1;
$idUser = $_SESSION['CREident'];
$nameUser = $_SESSION['CREnombreUser'];
$obj_ventaprevia = new VentaPrevia($debug, $idUser);
$Data = $obj_ventaprevia->getRequerimientosPzas();
$Data = Excepciones::validaConsulta($Data);
$Data = $Data == '' ? array() : $Data;
foreach ($Data as $key => $value) {
   $f_pzasFaltantes= formatoMil($Data[$key]['pzasFaltantes'],0);
?>
<div class="d-flex flex-row comment-row m-t-0 alert-primary mb-2 border border-primary p-1">

    <div class="comment-text w-100">
        <h6 class="font-medium">Piezas Solicitadas: <?=$f_pzasFaltantes?></h6>
        <span class="m-b-15 d-block">Lote: <?=$Data[$key]['loteTemola']?></span>
        <span class="m-b-15 d-block"><?=$Data[$key]['n_programa']?></span>
        <div class="comment-footer">
            <span class="font-medium float-right">F. de Venta <?=$Data[$key]['fFechaFact']?></span>
           
        </div>
    </div>
</div>
<?php
}
?>