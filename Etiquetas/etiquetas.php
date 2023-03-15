<?php
require_once 'seg.php';
$info = new Seguridad();
include("../assets/scripts/cadenas.php");
require_once "../include/connect_mvc.php";
include('../Models/Mdl_ConexionBD.php');
include('../Models/Mdl_Rendimiento.php');
$info->Acceso();
$idUser = $_SESSION['CREident'];
$nameUser = $_SESSION['CREnombreUser'];
setlocale(LC_TIME, 'es_ES.UTF-8');
$debug = 0;
$space = 1;
$obj_rendimiento = new Rendimiento($debug, $idUser);
$numSemana = formatoFecha("W");
$DataLotes = $obj_rendimiento->getLoteXSemana($numSemana, "2");
$f_numLotes = formatoMil($DataLotes[0]['total'], 0);
$DataLotes = $obj_rendimiento->getPzasRechazadasXSemanaEtiq($numSemana, "2");
$f_pzasRechazadas = formatoMil($DataLotes[0]['total']);;
?>
<!DOCTYPE html>
<html dir="ltr" lang="<?= $info->lng; ?>">

<?php include("../templates/header.php"); ?>

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
                                            <i class="ti-calendar font-20 text-muted"></i>
                                            <p class="font-16 m-b-5 text-TWM">Semana de Producción</p>
                                        </div>
                                        <div class="ml-auto">
                                            <h1 class="font-light text-right"> <?= formatoFecha("W") ?>
                                            </h1>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">

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
                                            <i class="far fa-square font-20  text-muted"></i>
                                            <p class="font-16 m-b-5 text-TWM">Lotes de Etiquetas Registrados</p>
                                        </div>
                                        <div class="ml-auto">
                                            <h1 class="font-light text-right"><?= $f_numLotes ?></h1>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <?= formatoFecha("WY") ?>

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
                                            <i class="ti-close font-20 text-muted"></i>
                                            <p class="font-16 m-b-5 text-TWM">Piezas Rechazadas</p>
                                        </div>
                                        <div class="ml-auto">
                                            <h1 class="font-light text-right"><?= $f_pzasRechazadas ?></h1>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <?= formatoFecha("WY") ?>

                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Column -->

                </div>
                <div class="row">
                    <div class="col-md-12 col-lg-12">
                        <div class="card border">
                            <div class="card-body">

                                <div class="row mb-1">
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
                                </div>
                                <div id="contentGrafica">

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
    actualizarGrafica()

    function actualizarGrafica() {
        $('#contentGrafica').html('<div class="loading text-center"><img src="../assets/images/loading.gif" alt="loading" /><br/>Un momento, por favor...</div>');
        $('#contentGrafica').load('../templates/Rendimiento/Estadistica/grafica_lotes_etiquetas.php');


    }
    /*************** FILTRADO DE SET'S *********************/
    $(".filtrado").submit(function(e) {
        e.preventDefault();
        id = $(this).prop("id");
        switch (id) {
            case "filtrado-conteolotes":
                url = '../templates/Rendimiento/Estadistica/grafica_lotes_etiquetas.php'
                content = "contentGrafica"
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