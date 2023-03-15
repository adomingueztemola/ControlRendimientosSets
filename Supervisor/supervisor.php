<?php
require_once 'seg.php';
$info = new Seguridad();
require_once "../include/connect_mvc.php";
include('../Models/Mdl_ConexionBD.php');

include("../assets/scripts/cadenas.php");

$info->Acceso();
$idUser = $_SESSION['CREident'];
$nameUser = $_SESSION['CREnombreUser'];
setlocale(LC_TIME, 'es_ES.UTF-8');
$debug = 0;
$space = 1;
$obj_rendimiento = new Rendimiento($debug, $idUser);
$DataKPIS = $obj_rendimiento->getKPISSemanaSup(date("W"));
$DataKPIS = Excepciones::validaConsulta($DataKPIS);
$totalEmp = $DataKPIS['totalEmp'] == '' ? '0' : formatoMil($DataKPIS['totalEmp'], 0);
$totalRech = $DataKPIS['totalRech'] == '' ? '0' : formatoMil($DataKPIS['totalRech'], 0);
$totalRecu = $DataKPIS['totalRecu'] == '' ? '0' : formatoMil($DataKPIS['totalRecu'], 0);
$totalLotes = $DataKPIS['totalLotes'] == '' ? '0' : formatoMil($DataKPIS['totalLotes'], 0);

?>
<!DOCTYPE html>
<html dir="ltr" lang="<?= $info->lng; ?>">

<?php include("../templates/header.php"); ?>
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
                    <div class="col-lg-4">
                        <div class="card bg-inverse text-white" onclick="actualizarLotes()" data-toggle="modal" data-target="#tableroLotesRegistrados">
                            <div class="card-body">
                                <div class="d-flex no-block align-items-center">
                                    <a href="JavaScript: void(0);"><i class="fas fa-calendar text-white" title="ETH"></i></a>
                                    <div class="m-l-15 m-t-10">
                                        <h5>Lotes Registrados Semana <?= date('W') ?>:</h5>
                                        <h4 class="font-medium m-b-0"><?= $totalLotes ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card bg-inverse text-white" onclick="actualizarCajasempacadas()" data-toggle="modal" data-target="#tableroCajasEmpacadas">
                            <div class="card-body">
                                <div class="d-flex no-block align-items-center">
                                    <a href="JavaScript: void(0);"><i class="fas fa-box text-white" title="ETH"></i></a>
                                    <div class="m-l-15 m-t-10">
                                        <h5>Piezas Empacadas Semana <?= date('W') ?>:</h5>
                                        <h4 class="font-medium m-b-0"><?= $totalEmp ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card bg-danger text-white" onclick="actualizarPiezasrechazadas()" data-toggle="modal" data-target="#tableroPiezasRechazadas">
                            <div class="card-body">
                                <div class="d-flex no-block align-items-center">
                                    <a href="JavaScript: void(0);"><i class="fas fa-times-circle text-white" title="ETH"></i></a>
                                    <div class="m-l-15 m-t-10">
                                        <h5>Piezas Rechazadas Semana <?= date('W') ?>:</h5>
                                        <h4 class="font-medium m-b-0"><?= $totalRech ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="row">
                    <div class="col-md-8 col-lg-8 col-sm-8 col-xs-8">
                        <!----- contenedor de card y boton de consulta --->
                        <div class="card">
                            <h5 class="card-header bg-TWM text-white">Tablero de Empaque</h5>
                            <div class="card-body">
                                <div class="row mb-1">
                                    <div class="col-md-11">
                                        <form class="filtrado" id="filtrado-conteocajas">
                                            <div class="input-group mb-3">
                                                <input type="date" name="fecha" value="<?= date("Y-m-d") ?>" class="form-control" aria-describedby="basic-addon1">
                                                <div class="input-group-append">
                                                    <button class="btn btn-info" type="submit"><i class="ti-search"></i></button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="col-md-1 text-right">
                                        <button name="anio" class="btn button btn-rounded btn-sm btn-light" onclick="actualizarTablero()" title="Actualizar Tablero"> <i class="fas fa-history"></i></button>
                                    </div>
                                </div>
                                <div class="row m-1">
                                    <div class="col-md-12">

                                        <div id="content-tableroCajas">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
                        <div class="card border">
                            <div class="card-header text-white bg-TWM">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <h4 class="card-title mb-0">Recuperación Semanal</h4>
                                    </div>

                                </div>
                            </div>
                            <div class="card-body">
                                <!--<div class="row mb-1">
                                    <div class="col-md-5">
                                        <form class="filtrado" id="filtrado-conteolotes">
                                            <div class="input-group mb-3">
                                                <input type="number" name="anio" placeholder="YYYY" value="<?= date("Y") ?>" step="1" min="2017" max="2100" class="form-control" placeholder="" aria-label="" aria-describedby="basic-addon1">
                                                <div class="input-group-append">
                                                    <button class="btn btn-info" type="submit"><i class="ti-search"></i></button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="col-md-6"></div>
                                    <div class="col-md-1 text-right">
                                        <button name="anio" class="btn button btn-rounded btn-sm btn-light" onclick="actualizarGrafica()" title="Actualizar Historial"> <i class="fas fa-history"></i></button>
                                    </div>
                                </div>-->
                                <div id="contentGrafica"></div>
                            </div>
                        </div>


                    </div>


                </div>
            </div>



        </div>
        <!-- MODAL DE TABLERO DE CAJAS-->
        <div class="modal fade" id="tableroCajasModal" tabindex="-1" role="dialog" aria-labelledby="tableroCajasModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-TWM text-white">
                        <h5 class="modal-title" id="tableroCajasModalLabel">Tablero de Empaque de Cajas</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>


        <!-- MODAL DE LOTES REGISTRADOS -->
        <div class="modal fade" id="tableroLotesRegistrados" tabindex="-1" role="dialog" aria-labelledby="tableroCajasModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-TWM text-white">
                        <h5 class="modal-title" id="tableroCajasModalLabel">Lotes Registrados</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div id="content-Lotes">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>



        <!-- MODAL DE CAJAS EMPACADAS -->
        <div class="modal fade" id="tableroCajasEmpacadas" tabindex="-1" role="dialog" aria-labelledby="tableroCajasModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-TWM text-white">
                        <h5 class="modal-title" id="tableroCajasModalLabel">Cajas Empacadas</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div id="content-Cajasempacadas">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL DE PIEZAS RECHAZADAS -->
        <div class="modal fade" id="tableroPiezasRechazadas" tabindex="-1" role="dialog" aria-labelledby="tableroCajasModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-TWM text-white">
                        <h5 class="modal-title" id="tableroCajasModalLabel">Piezas Rechazadas</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div id="content-Piezasrechazadas">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
</body>


<?= $info->creaFooter(); ?>
<?php include("../templates/libsJS.php"); ?>


<script>
    actualizarGrafica()
    actualizarTablero()
    
   
    /*********** ACTUALIZA GRAFICA***************/
    function actualizarGrafica() {
        $('#contentGrafica').html('<div class="loading text-center"><img src="../assets/images/loading.gif" alt="loading" /><br/>Un momento, por favor...</div>');
        $('#contentGrafica').load('../templates/Rendimiento/Estadistica/grafica_lotes_recuperados.php');


    }
    /*********** ACTUALIZA TABLERO ***************/
    function actualizarTablero() {
        cargaContenido("content-tableroCajas", "../templates/Rendimiento/Estadistica/cargaTableroCajas.php", '1')
        clearForm("filtrado-conteocajas")
    }
    /*************** TABLERO LOTES **************/
    function actualizarLotes() {
        cargaContenido("content-Lotes", "../templates/Rendimiento/Estadistica/detalladoLotes.php", '1')
        clearForm("filtrado-conteocajas")
    }
    /*************** TABLERO CAJAS **************/
    function actualizarCajasempacadas() {
        cargaContenido("content-Cajasempacadas", "../templates/Rendimiento/Estadistica/detalladoCajasempacadas.php", '1')
        clearForm("filtrado-conteocajas")
    }
    /*************** TABLERO PIEZAS RECHAZADAS **************/
    function actualizarPiezasrechazadas() {
        cargaContenido("content-Piezasrechazadas", "../templates/Rendimiento/Estadistica/detalladoPiezasrechazadas.php", '1')
        clearForm("filtrado-conteocajas")
    }



    /*************** FILTRADO DE SET'S *********************/
    $(".filtrado").submit(function(e) {
        e.preventDefault();
        id = $(this).prop("id");
        switch (id) {
            case "filtrado-conteolotes":
                url = '../templates/Rendimiento/Estadistica/grafica_lotes_recuperados.php'
                content = "contentGrafica"
                break;
            case "filtrado-conteocajas":
                url = '../templates/Rendimiento/Estadistica/cargaTableroCajas.php'
                content = "content-tableroCajas"
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