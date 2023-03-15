<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once('../../include/connect_mvc.php');
include("../../Models/Mdl_ConexionBD.php");
include("../../Models/Mdl_VentaXDevoluc.php");
include('../../assets/scripts/cadenas.php');
$debug = 0;
$idUser = $_SESSION['CREident'];
$nameUser = $_SESSION['CREnombreUser'];
setlocale(LC_TIME, 'es_ES.UTF-8');
$lote = !empty($_POST['lote']) ? $_POST['lote'] : '';
$obj_ventas = new VentaXDevoluc($debug, $idUser);
$DataLotes = $obj_ventas->getVentasCerradas();

$arreglo=[];
foreach ($DataLotes as $key => $value) {
    $arreglo['data'][] = $value;
}
?>
<div class="table-responsive">
    <table class="table table-sm" id="table-ventasdevoluc">
        <thead>
            <tr>
                <th>#</th>
                <th>Fecha</th>
                <th>Num. Factura</th>
                <th>Num. PL</th>
                <th>Tipo Venta</th>
                <th>Empleado Registro</th>
                <th>Fecha Registro</th>
            </tr>

        </thead>
        <tbody>
        </tbody>
    </table>

</div>

<script>
    <?php
    $var = json_encode($arreglo);
    echo 'var datsJson = ' . $var . ';';
    ?>

    function ejecutandoCarga(identif, element) {

        var selector = 'DIV' + identif;
        var finicio = $('#fStart').val();
        var ffin = $('#fEnd').val();

        $.post("../templates/Ventas/detalleVentasDevoluc.php", {
                ident: identif
            },
            function(respuesta) {
                $("#" + selector).html(respuesta);
            });

    }
</script>
<script src="../assets/extra-libs/datatables.net/js/jquery.dataTables.min-ESP.js"></script>
<script src="../assets/tablas/dataTable-VentasDevoluc.js"></script>
<script src="../dist/js/pages/datatable/datatable-basic.init.js"></script>