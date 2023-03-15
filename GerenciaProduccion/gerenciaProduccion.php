<?php
require_once 'seg.php';
$info = new Seguridad();
require_once "../include/connect_mvc.php";
include('../Models/Mdl_ConexionBD.php');
include('../Models/Mdl_Rendimiento.php');

include("../assets/scripts/cadenas.php");

$info->Acceso();
$idUser = $_SESSION['CREident'];
$nameUser = $_SESSION['CREnombreUser'];
setlocale(LC_TIME, 'es_ES.UTF-8');
$debug = 0;
$space = 1;
$obj_rendimiento = new Rendimiento($debug, $idUser);
$DataVP = $obj_rendimiento->getVentasvsPedidosSemana(formatoFecha("W"), date('Y'));
$DataVP = !is_array($DataVP) ? array() : $DataVP;
$ventas = formatoMil($DataVP[0]['TotalVtas']);
$pedidos = formatoMil($DataVP[0]['TotalPedido']);

?>
<!DOCTYPE html>
<html dir="ltr" lang="<?= $info->lng; ?>">

<?php include("../templates/header.php"); ?>
<style>

</style>
<link rel="stylesheet" type="text/css" href="../assets/extra-libs/c3/c3.min.css">


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
                <div class="row">
                    <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                        <div class="card">
                            <div class="card-header text-white bg-TWM">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <h4 class="card-title mb-0">Tendencia Semanal de Humedad, Suavidad, Quiebre, Área WB a Crust</h4>
                                    </div>

                                </div>
                            </div>
                            <div class="card-body">
                                <form class="filtrado" id="filtrado-tendencias">

                                    <div class="row mb-1">

                                        <div class="col-md-5">
                                            <input type="number" name="anio" placeholder="YYYY" value="<?= date("Y") ?>" step="1" min="2017" max="2100" class="form-control" placeholder="" aria-label="" aria-describedby="basic-addon1">


                                        </div>
                                        <div class="col-md-6">
                                            <div class="input-group mb-3">
                                                <select class="form-control" name="tipo">
                                                    <option value="">Todo</option>
                                                    <option value="1">SETS</option>
                                                    <option value="3">M<sup>2</sup> AUTO CZA</option>
                                                    <option value="4">M<sup>2</sup> AUTO Piel</option>
                                                    <!--  <option value="5">M<sup>2</sup> Calzado</option>
                                                    <option value="6">M<sup>2</sup> Etiquetas</option>-->

                                                </select>
                                                <div class="input-group-append">
                                                    <button class="btn btn-info" type="submit"><i class="ti-search"></i></button>
                                                </div>
                                            </div>


                                        </div>
                                        <div class="col-md-1 text-right">
                                            <button name="anio" class="btn button btn-rounded btn-sm btn-light" onclick="cargaTendencias()" title="Actualizar Historial"> <i class="fas fa-history"></i></button>
                                        </div>
                                    </div>
                                </form>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div id="carga-tendencias">
                                        </div>

                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                        <div class="card">
                            <div class="card-header text-white bg-TWM">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <h4 class="card-title mb-0">Bandejas de Solicitudes de Lotes</h4>
                                    </div>

                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-11"> </div>
                                    <div class="col-md-1 text-right mb-2">
                                        <button name="anio" class="btn button btn-rounded btn-sm btn-light" onclick="cargaBandeja()" title="Actualizar Historial"> <i class="fas fa-history"></i></button>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12" id="carga-bandeja" style="height:500px; overflow-y: scroll;"> </div>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            </div>




</body>


<?= $info->creaFooter(); ?>
<?php include("../templates/libsJS.php"); ?>

<script>
    cargaTendencias();
    cargaBandeja();

    function cargaTendencias() {
        $('#carga-tendencias').html('<div class="loading text-center"><img src="../assets/images/loading.gif" alt="loading" /><br/>Un momento, por favor...</div>');
        $('#carga-tendencias').load('../templates/Rendimiento/Estadistica/cargaTendenciaHumSuavQuieb.php');
        clearForm("filtrado-tendencias")

    }

    function cargaBandeja() {
        $('#carga-bandeja').html('<div class="loading text-center"><img src="../assets/images/loading.gif" alt="loading" /><br/>Un momento, por favor...</div>');
        $('#carga-bandeja').load('../templates/Rendimiento/Estadistica/cargaBandejaSolicitudes.php');

    }


    $(".filtrado").submit(function(e) {
        e.preventDefault();
        id = $(this).prop("id");
        switch (id) {
            case "filtrado-tendencias":
                url = '../templates/Rendimiento/Estadistica/cargaTendenciaHumSuavQuieb.php'
                content = "carga-tendencias"
                break;

            default:
                url = url
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
</script>

</html>