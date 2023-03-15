<?php
require_once 'seg.php';
$info = new Seguridad();

$info->Acceso();
$idUser = $_SESSION['CREident'];
$nameUser = $_SESSION['CREnombreUser'];
setlocale(LC_TIME, 'es_ES.UTF-8');
$debug = 0;
$space = 1;
require_once "../include/connect_mvc.php";
include('../Models/Mdl_ConexionBD.php');
include('../Models/Mdl_Rendimiento.php');
include('../Models/Mdl_Pedido.php');
include("../Models/Mdl_Venta.php");

include("../assets/scripts/cadenas.php");
$obj_rendimiento = new Rendimiento($debug, $idUser);
$obj_pedido = new Pedido($debug, $idUser);
$DataCuero = $obj_pedido->getCuerosDisponibles();
$f_cueros = formatoMil($DataCuero["0"]['totalCueros']);
$obj_rendimiento = new Rendimiento($debug, $idUser);
$Data_Rendimiento = $obj_rendimiento->getRendimientos();
$f_lote = formatoMil(count($Data_Rendimiento), 0);
$Data_Rendimiento = $obj_rendimiento->getTotalAlmacen();
$f_almacen = formatoMil($Data_Rendimiento['0']['totalAlmacen']);

$obj_venta = new Venta($debug, $idUser);
$DataVenta = $obj_venta->getVentasXMes(date("Y-m"));
$f_venta = formatoMil($DataVenta['0']['totalVenta'], 0);
?>
<!DOCTYPE html>
<html dir="ltr" lang="<?= $info->lng; ?>">

<?php include("../templates/header.php"); ?>
<link href="../assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css" rel="stylesheet">
<link href="../assets/libs/morris.js/morris.css" rel="stylesheet">

<body>
    <div class="preloader">
        <div class="lds-ripple">
            <div class="lds-pos"></div>
            <div class="lds-pos"></div>
        </div>
    </div>
    <div id="main-wrapper">
        <?= $info->creaHeaderConMenu(); ?>
        <div class="page-wrapper">
            <div class="container-fluid">
               
            </div>
        </div>

    </div>




</body>



</body>
<?= $info->creaFooter(); ?>

<?php include("../templates/libsJS.php"); ?>

<script src="../assets/extra-libs/datatables.net/js/jquery.dataTables.min-ESP.js"></script>

<script>
    cargaEtiquetas();
    pedidovsventaschar();
    cargaLotesPendientes();

    function cargaEtiquetas() {
        $('#carga-etiquetas').html('<div class="loading text-center"><img src="../assets/images/loading.gif" alt="loading" /><br/>Un momento, por favor...</div>');
        $('#carga-etiquetas').load('../templates/Pedidos/cargaEtiquetasPend.php');


    }

    function pedidovsventaschar() {
        $('#carga-ventasvspedidos').html('<div class="loading text-center"><img src="../assets/images/loading.gif" alt="loading" /><br/>Un momento, por favor...</div>');
        $('#carga-ventasvspedidos').load('../templates/Rendimiento/Estadistica/grafica_ventasvspedidos.php');


    }

    function cargaLotesPendientes() {
        $('#carga-cargapendlotes').html('<div class="loading text-center"><img src="../assets/images/loading.gif" alt="loading" /><br/>Un momento, por favor...</div>');
        $('#carga-cargapendlotes').load('../templates/Rendimiento/cargaTablaDeLotesPend.php');
        $("#lote").val("");

    }

    /*************** FILTRADO DE SET'S *********************/
    $(".filtrado").submit(function(e) {
        e.preventDefault();
        id = $(this).prop("id");
        switch (id) {
            case "filtrado-ventped":
                url = '../templates/Rendimiento/Estadistica/grafica_ventasvspedidos.php'
                content = "carga-ventasvspedidos"
                break;

        }
        formData = $(this).serialize();
        $.ajax({
            url: url,
            data: formData,
            type: 'POST',
            success: function(respuesta) {
                $('#' + content).html(respuesta);


            },
            beforeSend: function() {}

        });
    });

    /*************** FILTRADO DE SET'S *********************/
    $("#filtrado").submit(function(e) {
        e.preventDefault();
        formData = $(this).serialize();
        $.ajax({
            url: "../templates/Rendimiento/cargaTablaDeLotesPend.php",
            data: formData,
            type: 'POST',
            success: function(respuesta) {
                $("#carga-cargapendlotes").html(respuesta);


            },
            beforeSend: function() {}

        });
    });
</script>

</html>