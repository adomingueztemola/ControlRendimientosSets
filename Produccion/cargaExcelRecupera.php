<?php
require_once 'seg.php';
$info = new Seguridad();
require_once('../include/connect_mvc.php');

$info->Acceso();
$idUser = $_SESSION['CREident'];
$nameUser = $_SESSION['CREnombreUser'];
setlocale(LC_TIME, 'es_ES.UTF-8');
$debug = 0;
$space = 1;
?>
<!DOCTYPE html>
<html dir="ltr" lang="<?= $info->lng; ?>">
<?php include("../templates/header.php"); ?>
<link href="../assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="../assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">

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
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <?php include("../templates/namePage.php"); ?>
                    </div>

                </div>
                <div class="row mb-2">
                    <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                        <a href="trabajosrecuperacion.php" class="btn btn-md btn-TWM"><i class="fas fa-arrow-left"></i></a>
                    </div>
                    <div class="col-lg-10 col-md-10 col-sm-12 col-xs-12"></div>
                </div>
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="card border">
                            <div class="card-header bg-danger text-white">
                                <h4><i class="fas fa-times"></i> Errores Reportados en la Carga</h4>
                            </div>
                            <div class="card-body  m-0 p-2">
                                <div class="row m-0 p-0">
                                    <div class="col-lg-12 col-md-12 col-xs-12 col-sm-12  m-0 p-0" style="height:250px; overflow-y: scroll;" id="contentErroresReportados"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="card border">
                            <div class="card-header">
                                <form id="filtrado">

                                    <div class="row p-0 mb-1">

                                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                            <label for="date-range">Búsqueda de por Rangos de Fechas: </label>
                                            <div class="input-daterange input-group" id="date-range">
                                                <input type="text" class="form-control" name="date-start" value="<?= date("01/m/Y") ?>">
                                                <div class="input-group-append">
                                                    <span class="input-group-text bg-TWM b-0 text-white">AL</span>
                                                </div>
                                                <input type="text" class="form-control" name="date-end" value="<?= date("t/m/Y") ?>">
                                            </div>
                                        </div>
                                        <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 pt-4 mt-1">
                                            <button class="btn button btn-TWM"> Filtrar</button>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12"></div>
                                        <div class="col-md-1 text-right">
                                            <button class="btn button btn-rounded btn-sm btn-light" onclick="cargaReacondicionamiento()" title="Actualizar Historial"> <i class="fas fa-history"></i></button>
                                        </div>
                                    </div>
                                </form>

                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="contentReacondicionamientos">
                                    </div>
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

<script src="../assets/extra-libs/datatables.net/js/jquery.dataTables.min-ESP.js"></script>
<script src="../assets/libs/moment/moment.js"></script>
<script src="../assets/libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="../assets/libs/bootstrap-datepicker/dist/locales/bootstrap-datepicker.es.min.js"></script>

<script>
    cargaErrores();
    cargaReacondicionamiento();
    jQuery('#date-range').datepicker({
        toggleActive: true,
        format: 'dd/mm/yyyy',
        language: "es",
        todayHighlight: true
    });

    function cargaErrores() {
        cargaContenido("contentErroresReportados", "../templates/TrabajosRecuperacion/cargaErroresXLSX.php", '1')
    }

    function cargaReacondicionamiento() {
        cargaContenido("contentReacondicionamientos", "../templates/TrabajosRecuperacion/cargaReacondicionamientos.php", '1');
        clearForm('filtrado');
    }

    /*************** FILTRADO DE TABLA *********************/
    $("#filtrado").submit(function(e) {
        e.preventDefault();
        formData = $(this).serialize();
        $.ajax({
            url: '../templates/TrabajosRecuperacion/cargaReacondicionamientos.php',
            data: formData,
            type: 'POST',
            success: function(respuesta) {
                $('#contentReacondicionamientos').html(respuesta);


            },
            beforeSend: function() {}

        });
    });
</script>

</html>