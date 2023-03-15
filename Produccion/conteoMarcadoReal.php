<?php
require_once 'seg.php';
$info = new Seguridad();
require_once('../include/connect_mvc.php');
include("../Models/Mdl_ConexionBD.php");
include('../Models/Mdl_MarcadoAMano.php');


$info->Acceso();
$idUser = $_SESSION['CREident'];
$nameUser = $_SESSION['CREnombreUser'];
setlocale(LC_TIME, 'es_ES.UTF-8');
$debug = 0;
$space = 1;

$obj_marcado = new MarcadoAMano($debug, $idUser);
?>
<!DOCTYPE html>
<html dir="ltr" lang="<?= $info->lng; ?>">

<?php include("../templates/header.php"); ?>
<link href="../assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="../assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
<link href="../assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css" rel="stylesheet">

<link href="../dist/css/boton.css">
</link>
<style>
    .ir-arriba {
        display: none;
        position: fixed;
        bottom: 20px;
        right: 20px;
    }
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
                <?php include("../templates/namePage.php"); ?>
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="card border">
                            <div class="card-body" id="">
                                <form id="filtrado">
                                    <div class="row">
                                        <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                            <label for="date-range">Búsqueda de por Rangos de Fechas: </label>
                                            <div class="input-daterange input-group" id="date-range">
                                                <input type="text" autocomplete="off" class="form-control" name="date-start" value="">
                                                <div class="input-group-append">
                                                    <span class="input-group-text bg-TWM b-0 text-white">AL</span>
                                                </div>
                                                <input type="text" autocomplete="off" class="form-control" name="date-end" value="">
                                            </div>
                                        </div>

                                        <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                            <label for="programa">Programa:</label>
                                            <select class="form-control select2" style="width:100%" name="programa" id="programa">
                                                <option value="">Todos los Programas</option>
                                                <?php
                                                $DataPrograma = $obj_marcado->getProgramaConVolante();
                                                foreach ($DataPrograma as $key => $value) {
                                                    echo "<option value='{$DataPrograma[$key]['id']}'>{$DataPrograma[$key]['nombre']}</option>";
                                                }
                                                ?>
                                            </select>

                                        </div>

                                        <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 pt-4 mt-1">
                                            <button class="btn button btn-TWM"> Filtrar</button>
                                        </div>
                                    </div>
                                </form>

                            </div>

                        </div>
                    </div>

                </div>


                <div class="row">
                    <div class="col-lg-12 col-md-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="card border">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-11"></div>
                                    <div class="col-md-1 text-right mb-2">
                                        <button class="btn button btn-rounded btn-sm btn-light" onclick="update()" title="Actualizar Contenido"> <i class="fas fa-history"></i></button>
                                    </div>
                                </div>


                                <div class="row">
                                    <div class="col-md-12">
                                        <div id="content-historial"></div>

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
<script src="../assets/libs/block-ui/jquery.blockUI.js"></script>
<script src="../assets/libs/moment/moment.js"></script>
<script src="../assets/libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="../assets/libs/bootstrap-datepicker/dist/locales/bootstrap-datepicker.es.min.js"></script>
<script src="../assets/scripts/calculaSemanaProduccion.js"></script>

<script>
    update()
    jQuery('#date-range').datepicker({
        toggleActive: true,
        format: 'dd/mm/yyyy',
        language: "es",
        todayHighlight: true



    });


    $(window).scroll(function() {
        if ($(this).scrollTop() > 0) {
            $('.ir-arriba').slideDown(300);
        } else {
            $('.ir-arriba').slideUp(300);
        }
    });


    function update() {
        $('#content-historial').html('<div class="loading text-center"><img src="../assets/images/loading.gif" alt="loading" /><br/>Un momento, por favor...</div>');
        $('#content-historial').load('../templates/MarcadoAMano/conteoMarcadoReal.php');
        clearForm("filtrado");

    }

    /*************** FILTRADO DE TABLA *********************/
    $("#filtrado").submit(function(e) {
        e.preventDefault();
        formData = $(this).serialize();
        $.ajax({
            url: '../templates/MarcadoAMano/historialMarcado.php',
            data: formData,
            type: 'POST',
            success: function(respuesta) {
                $('#content-historial').html(respuesta);


            },
            beforeSend: function() {}

        });
    });
</script>

</html>