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
                <div class="card-group">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="d-flex no-block align-items-center">
                                        <div>
                                            <i class=" fas fa-object-ungroup font-20 text-muted"></i>
                                            <p class="font-16 m-b-5 text-TWM">Cueros Disponibles</p>
                                        </div>
                                        <div class="ml-auto">
                                            <h1 class="font-light text-right"><?= $f_cueros ?></h1>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <?= formatoFecha("2") ?>

                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- Column -->
                    <!-- Column -->
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="d-flex no-block align-items-center">
                                        <div>
                                            <i class="fas fa-dolly font-20  text-muted"></i>
                                            <p class="font-16 m-b-5 text-TWM">Lotes Pendientes</p>
                                        </div>
                                        <div class="ml-auto">
                                            <h1 class="font-light text-right"><?= $f_lote ?></h1>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <?= formatoFecha("2") ?>

                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Column -->
                    <!-- Column -->
                    <!-- OBJECT: Desactivar Almacen PT    Script Date: 20/06/2022 
                    Desactivación de la Tarjeta de Almacen PT por estructura-->
                    <!-- <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="d-flex no-block align-items-center">
                                        <div>
                                            <i class="fas fa-warehouse font-20 text-muted"></i>
                                            <p class="font-16 m-b-5 text-TWM">Almacén PT</p>
                                        </div>
                                        <div class="ml-auto">
                                            <h1 class="font-light text-right"><?= $f_almacen ?></h1>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <?= formatoFecha("2") ?>

                                </div>
                            </div>
                        </div>
                    </div>-->
                    <!-- Column -->
                    <!-- Column -->
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="d-flex no-block align-items-center">
                                        <div>
                                            <i class="mdi mdi-poll font-20 text-muted"></i>
                                            <p class="font-16 m-b-5 text-TWM">Ventas</p>
                                        </div>
                                        <div class="ml-auto">
                                            <h1 class="font-light text-right"><?= $f_venta ?></h1>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <?= formatoFecha("2") ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

                        <div class="card">
                            <div class="card-header text-white bg-TWM">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <h4 class="card-title mb-0">Ventas vs Pedidos Anuales</h4>
                                    </div>

                                </div>
                            </div>
                            <div class="card-body">

                                <div class="row mb-1">
                                    <div class="col-md-5">
                                        <form class="filtrado" id="filtrado-ventped">
                                            <div class="input-group mb-3">
                                                <input type="number" name="anio" placeholder="YYYY" value="<?= date("Y") ?>" step="1" min="2017" max="2100" class="form-control" placeholder="" aria-label="" aria-describedby="basic-addon1">
                                                <div class="input-group-append">
                                                    <button class="btn btn-info" type="submit"><i class="ti-search"></i></button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="col-md-6">

                                        <ul class="list-inline text-right">
                                            <li class="list-inline-item">
                                                <h5><i class="fa fa-circle m-r-5 text-inverse"></i>Ventas</h5>
                                            </li>
                                            <li class="list-inline-item">
                                                <h5><i class="fa fa-circle m-r-5 text-danger"></i>Pedidos</h5>
                                            </li>

                                        </ul>
                                    </div>
                                    <div class="col-md-1 text-right">
                                        <button name="anio" class="btn button btn-rounded btn-sm btn-light" onclick="pedidovsventaschar()" title="Actualizar Historial"> <i class="fas fa-history"></i></button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div id="carga-ventasvspedidos">
                                        </div>

                                    </div>
                                </div>

                            </div>


                        </div>
                    </div>


                    <!---<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <div class="card">
                            <div class="card-header text-white bg-TWM">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <h4 class="card-title mb-0">Seguimiento de Lotes Pendientes</h4>
                                    </div>

                                </div>
                            </div>
                            <div class="card-body" style="height:440px; overflow-y: scroll;">
                                <div class="row">
                                    <div class="col-md-5">
                                        <form id="filtrado">
                                            <div class="input-group mb-3">
                                                <input type="text" autocomplete="off" name="lote" id="lote" class="form-control" placeholder="" aria-label="" aria-describedby="basic-addon1">
                                                <div class="input-group-append">
                                                    <button class="btn btn-TWM" type="submit"><i class="ti-search"></i></button>
                                                </div>
                                            </div>
                                        </form>

                                    </div>
                                    <div class="col-md-6"></div>
                                    <div class="col-md-1 text-right">
                                        <button name="anio" class="btn button btn-rounded btn-sm btn-light" onclick="cargaLotesPendientes()" title="Actualizar Historial"> <i class="fas fa-history"></i></button>
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="table-responsive" id="carga-cargapendlotes"> </div>

                                    </div>

                                </div>
                            </div>
                        </div>


                    </div>-->

                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="card">
                            <div class="card-header text-white bg-TWM">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <h4 class="card-title mb-0">Registro de Pedidos de Lotes de Etiquetas</h4>
                                    </div>

                                </div>
                            </div>
                            <div class="card-body" style="height:400px; overflow-y: scroll;">

                                <div class="row">
                                    <div class="col-md-11"></div>
                                    <div class="col-md-1 text-right pb-2">
                                        <button name="anio" class="btn button btn-rounded btn-sm btn-light" onclick="cargaEtiquetas()" title="Actualizar Historial"> <i class="fas fa-history"></i></button>
                                    </div>

                                </div>
                                <div id="carga-etiquetas">

                                </div>

                            </div>
                        </div>
                    </div>


                </div>
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