<?php
define('INCLUDE_CHECK', 1);
session_start();
require_once('../../include/connect_mvc.php');
include("../../Models/Mdl_ConexionBD.php");
include("../../Models/Mdl_Pedido.php");
include('../../assets/scripts/cadenas.php');

$idUser = $_SESSION['CREident'];
$nameUser = $_SESSION['CREnombreUser'];
setlocale(LC_TIME, 'es_ES.UTF-8');
$debug = 0;
$space = 1;
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}
$obj_pedido = new Pedido($debug, $idUser);
/************************** VARIABLES DE FILTRADO *******************************/
$proceso = !empty($_POST['proceso']) ? $_POST['proceso'] : '';
$programa = !empty($_POST['programa']) ? $_POST['programa'] : '';
$materia = !empty($_POST['materia']) ? $_POST['materia'] : '';
$proveedor = !empty($_POST['proveedor']) ? $_POST['proveedor'] : '';

$date_start = !empty($_POST['date-start']) ? $_POST['date-start'] :date("01/m/Y");
$date_end = !empty($_POST['date-end']) ? $_POST['date-end'] : date("t/m/Y");
/***************** CASTEO DE FECHAS ****************** */
if ($date_start != '' and $date_end != '') {
    $date_start = date("Y-m-d", strtotime(str_replace("/", "-", $date_start)));
    $date_end = date("Y-m-d", strtotime(str_replace("/", "-", $date_end)));
    $filtradoFecha = "p.fechaFactura BETWEEN '$date_start' AND '$date_end'";

}else{
    $filtradoFecha="1=1";
}

/************************** FILTRADO *******************************/
$filtradoMateria = $materia != '' ? "p.idCatMateriaPrima='$materia'" : "1=1";
$filtradoProveedor = $proveedor != '' ? "p.idCatProveedor='$proveedor'" : "1=1";

$DataPedido = $obj_pedido->getPedidos("(p.estado!='1' AND p.estado>0)",$filtradoFecha, $filtradoProveedor, "1=1",$filtradoMateria);

$arreglo = [];
$s_piezasRecuperadas="";
$s_setsRecuperadas="";
$s_rzgoPiezas="";
$s_recuperacionInicial="";
$s_recuperacionFinal="";
$s_lotesRecuperados="";
$count=0;
foreach ($DataPedido as $key => $value) {
    $count++;
    $arreglo['data'][] = $value;
   
}
?>

<div class="table-responsive">
    <table id="table-pedidos" class="table table-sm">
        <thead>
            <tr class="">
                <th>#</th>
                <th>Num. Factura</th>
                <th>Fecha de Facturacion</th>
                <th>Proveedor</th>

                <th>Materia Prima</th>
                <th>Cueros Facturados</th>
                <th>Cueros Entregados</th>
                <th>Cueros Disponibles</th>

                <th>Acción</th>

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

        $.post("../templates/Pedidos/detalleDeEdicion.php", {
                ident: identif
            },
            function(respuesta) {
                $("#" + selector).html(respuesta);
            });

    }

</script>

<script src="../assets/extra-libs/datatables.net/js/jquery.dataTables.min-ESP.js"></script>
<script src="../assets/tablas/dataTable-ExcepcionesPedidos.js"></script>
<script src="../dist/js/pages/datatable/datatable-basic.init.js"></script>