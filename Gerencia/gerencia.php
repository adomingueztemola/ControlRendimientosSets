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
                    <!-- Column -->
                    <div class="col-lg-4 col-md-6">
                        <div class="card border border-TWM">
                            <div class="card-body">
                                <div class="d-flex flex-row">
                                    <div class="round align-self-center round-TWM"><i class="ti-calendar"></i></div>
                                    <div class="m-l-10 align-self-center">
                                        <h3> <?= formatoFecha("WY") ?></h3>
                                        <span class="text-muted">Semana de Producción</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Column -->
                    <!-- Column -->
                    <div class="col-lg-4 col-md-6">
                        <div class="card border border-TWM">
                            <div class="card-body">
                                <div class="d-flex flex-row">
                                    <div class="round align-self-center round-TWM"><i class="ti-money"></i></div>
                                    <div class="m-l-10 align-self-center">
                                        <h3><?= $ventas ?></h3>
                                        <span class="text-muted">Ventas Efectuadas en Semana</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Column -->
                    <!-- Column -->
                    <div class="col-lg-4 col-md-6">
                        <div class="card  border border-TWM">
                            <div class="card-body">
                                <div class="d-flex flex-row">
                                    <div class="round align-self-center round-TWM"><i class="ti-layers-alt"></i></div>
                                    <div class="m-l-10 align-self-center">
                                        <h3><?= $pedidos ?></h3>
                                        <span class="text-muted">Pedidos Realizados en Semana</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Column -->
                </div>

                <div class="row">
                    <div class="col-md-6 col-lg-6 col-sm-12 col-xs-12">
                        <div class="card border">
                            <div class="card-body" id="">
                                <div class="row mb-1">
                                    <div class="col-md-5">
                                        <form class="filtrado" id="filtrado-sets">
                                            <div class="input-group mb-3">
                                                <input type="number" placeholder="YYYY" name="anio" value="<?=date("Y")?>" step="1" min="2017" max="2100" class="form-control" placeholder="" aria-label="" aria-describedby="basic-addon1">
                                                <div class="input-group-append">
                                                    <button class="btn btn-info" type="submit"><i class="ti-search"></i></button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="col-md-6"></div>
                                    <div class="col-md-1 text-right">
                                        <button class="btn button btn-rounded btn-sm btn-light" onclick="setschar()" title="Actualizar Historial"> <i class="fas fa-history"></i></button>
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-xs-12 col-sm-12" id="carga-sets">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-6 col-sm-12 col-xs-12">
                        <div class="card border">
                            <div class="card-body" id="">
                                <div class="row mb-1">
                                    <div class="col-md-5">
                                        <form class="filtrado" id="filtrado-wb">
                                            <div class="input-group mb-3">
                                                <input type="number" placeholder="YYYY" value="<?=date("Y")?>" step="1" min="2017" name="anio"  max="2100" class="form-control" placeholder="" aria-label="" aria-describedby="basic-addon1">
                                                <div class="input-group-append">
                                                    <button class="btn btn-info" type="submit"><i class="ti-search"></i></button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="col-md-6"></div>
                                    <div class="col-md-1 text-right">
                                        <button class="btn button btn-rounded btn-sm btn-light" onclick="wbchar()" title="Actualizar Historial"> <i class="fas fa-history"></i></button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-xs-12 col-sm-12" id="carga-wb">

                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">

                    <div class="col-md-6 col-lg-6 col-sm-12 col-xs-12">
                        <div class="card border">
                            <div class="card-body" id="">
                                <div class="row mb-1">
                                    <div class="col-md-5">
                                        <form class="filtrado" id="filtrado-m2autocza">
                                            <div class="input-group mb-3">
                                                <input type="number" name="anio" placeholder="YYYY" value="<?=date("Y")?>" step="1" min="2017" max="2100" class="form-control" placeholder="" aria-label="" aria-describedby="basic-addon1">
                                                <div class="input-group-append">
                                                    <button class="btn btn-info" type="submit"><i class="ti-search"></i></button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="col-md-6"></div>

                                    <div class="col-md-1 text-right">
                                        <button class="btn button btn-rounded btn-sm btn-light" onclick="m2autocza()" title="Actualizar Historial"> <i class="fas fa-history"></i></button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-xs-12 col-sm-12" id="carga-cza">

                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-6 col-sm-12 col-xs-12">
                        <div class="card border">
                            <div class="card-body" id="">
                                <div class="row mb-1">
                                    <div class="col-md-5">
                                        <form class="filtrado" id="filtrado-m2autopiel">
                                            <div class="input-group mb-3">
                                                <input type="number" name="anio" placeholder="YYYY" value="<?=date("Y")?>" step="1" min="2017" max="2100" class="form-control" placeholder="" aria-label="" aria-describedby="basic-addon1">
                                                <div class="input-group-append">
                                                    <button class="btn btn-info" type="submit"><i class="ti-search"></i></button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="col-md-6"></div>
                                    <div class="col-md-1 text-right">
                                        <button class="btn button btn-rounded btn-sm btn-light" onclick="m2autopiel()" title="Actualizar Historial"> <i class="fas fa-history"></i></button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-xs-12 col-sm-12" id="carga-piel">

                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>



                </div>

                <div class="row">

                    <div class="col-md-6 col-lg-6 col-sm-12 col-xs-12">
                        <div class="card border">
                            <div class="card-body" id="">
                                <div class="row mb-1">
                                    <div class="col-md-5">
                                        <form class="filtrado" id="filtrado-calzado">
                                            <div class="input-group mb-3">
                                                <input type="number" name="anio" placeholder="YYYY" value="<?=date("Y")?>" step="1" min="2017" max="2100" class="form-control" placeholder="" aria-label="" aria-describedby="basic-addon1">
                                                <div class="input-group-append">
                                                    <button class="btn btn-info" type="submit"><i class="ti-search"></i></button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="col-md-6"></div>
                                    <div class="col-md-1 text-right">
                                        <button name="anio" class="btn button btn-rounded btn-sm btn-light" onclick="calzado()" title="Actualizar Historial"> <i class="fas fa-history"></i></button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-xs-12 col-sm-12" id="carga-calzado">

                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-6 col-sm-12 col-xs-12">
                        <div class="card border">
                            <div class="card-body" id="">
                                <div class="row mb-1">
                                    <div class="col-md-5">
                                        <form class="filtrado" id="filtrado-etiquetas">
                                            <div class="input-group mb-3">
                                                <input type="number" name="anio" placeholder="YYYY" value="<?=date("Y")?>" step="1" min="2017" max="2100" class="form-control" placeholder="" aria-label="" aria-describedby="basic-addon1">
                                                <div class="input-group-append">
                                                    <button class="btn btn-info" type="submit"><i class="ti-search"></i></button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="col-md-6"></div>
                                    <div class="col-md-1 text-right">
                                        <button class="btn button btn-rounded btn-sm btn-light" onclick="etiquetas()" title="Actualizar Historial"> <i class="fas fa-history"></i></button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-xs-12 col-sm-12" id="carga-etiquetas">

                                    </div>
                                </div>


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
    setschar()
    wbchar()
    m2autocza()
    m2autopiel()
    etiquetas()
    calzado()

    function setschar() {
        $('#carga-sets').html('<div class="loading text-center"><img src="../assets/images/loading.gif" alt="loading" /><br/>Un momento, por favor...</div>');
        $('#carga-sets').load('../templates/Rendimiento/Estadistica/grafica_sets.php');


    }

    function wbchar() {
        $('#carga-wb').html('<div class="loading text-center"><img src="../assets/images/loading.gif" alt="loading" /><br/>Un momento, por favor...</div>');
        $('#carga-wb').load('../templates/Rendimiento/Estadistica/grafica_wb.php');


    }

    function m2autocza() {
        $('#carga-cza').html('<div class="loading text-center"><img src="../assets/images/loading.gif" alt="loading" /><br/>Un momento, por favor...</div>');
        $('#carga-cza').load('../templates/Rendimiento/Estadistica/grafica_m2autocza.php');

    }

    function m2autopiel() {
        $('#carga-piel').html('<div class="loading text-center"><img src="../assets/images/loading.gif" alt="loading" /><br/>Un momento, por favor...</div>');
        $('#carga-piel').load('../templates/Rendimiento/Estadistica/grafica_m2autopiel.php');

    }

    function etiquetas() {
        $('#carga-etiquetas').html('<div class="loading text-center"><img src="../assets/images/loading.gif" alt="loading" /><br/>Un momento, por favor...</div>');
        $('#carga-etiquetas').load('../templates/Rendimiento/Estadistica/grafica_etiquetas.php');
    }

    function calzado() {
        $('#carga-calzado').html('<div class="loading text-center"><img src="../assets/images/loading.gif" alt="loading" /><br/>Un momento, por favor...</div>');
        $('#carga-calzado').load('../templates/Rendimiento/Estadistica/grafica_calzado.php');
    }

    /*************** FILTRADO DE SET'S *********************/
    $(".filtrado").submit(function(e) {
        e.preventDefault();
        id = $(this).prop("id");
        switch (id) {
            case "filtrado-sets":
                url='../templates/Rendimiento/Estadistica/grafica_sets.php'
                content="carga-sets"
                break;
            case "filtrado-wb":
                url='../templates/Rendimiento/Estadistica/grafica_wb.php'
                content="carga-wb"
                break;
            case "filtrado-m2autocza":
                url='../templates/Rendimiento/Estadistica/grafica_m2autocza.php'
                content="carga-cza"
                break;
            case "filtrado-m2autopiel":
                url='../templates/Rendimiento/Estadistica/grafica_m2autopiel.php'
                content="carga-piel"
                break;
            case "filtrado-etiquetas":
                url= '../templates/Rendimiento/Estadistica/grafica_etiquetas.php'
                content="carga-etiquetas"
                break;
            case "filtrado-calzado":
                url='../templates/Rendimiento/Estadistica/grafica_calzado.php'
                content="carga-calzado"
                break;
            default:
                url=url
                break;
        }
        formData = $(this).serialize();
        $.ajax({
            url: url,
            data: formData,
            type: 'POST',
            success: function(respuesta) {
                $('#'+content).html(respuesta);


            },
            beforeSend: function() {}

        });
    });
</script>

</html>