<?php
require_once 'seg.php';
$info = new Seguridad();
require_once('../include/connect_mvc.php');
include("../Models/Mdl_ConexionBD.php");
include("../Models/Mdl_Programa.php");
include("../Models/Mdl_Proceso.php");
include("../Models/Mdl_MateriaPrima.php");

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
<link href="../dist/css/boton.css">
<link rel="stylesheet" type="text/css" href="../assets/libs/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.min.css">
<link rel="stylesheet" type="text/css" href="../assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">

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
                <div class="card">
                    <div class="card-body">
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
                                    <button class="btn button btn-rounded btn-sm btn-light" onclick="update()" title="Actualizar Historial"> <i class="fas fa-history"></i></button>
                                </div>

                            </div>
                        </form>

                        <div class="row mb-2 mt-2">
                            <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10"></div>
                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 text-right">
                                <button type="button" class="btn btn-TWM" data-toggle="modal" onclick="cargaContenido('content-formRecupera', '../templates/Reacondicionamiento/cargaRegistroRecuperacion.php','1')" data-target="#recuperaModal">
                                    Registro Recuperación
                                </button>
                            </div>

                        </div>
                        <div class="table-responsive" id="content-recuperacion">

                        </div>

                        <div class="modal fade" id="recuperaModal" role="dialog" aria-labelledby="recuperaModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content block-recuperacion">
                                    <div class="modal-header bg-TWM text-white">
                                        <h5 class="modal-title" id="recuperaModalLabel">Nueva Recuperación de Piezas</h5>
                                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form id="formAddRecuperacion">

                                        <div class="modal-body" id="content-formRecupera">

                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-light" data-dismiss="modal">Salir</button>
                                            <button type="submit" class="button btn btn-success">Guardar</button>
                                        </div>
                                    </form>

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
<script src="../assets/extra-libs/datatables.net/js/jquery.dataTables.min-ESP.js"></script>
<script src="../assets/libs/moment/moment.js"></script>
<script src="../assets/libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="../assets/libs/bootstrap-datepicker/dist/locales/bootstrap-datepicker.es.min.js"></script>
<script src="../assets/libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="../assets/libs/bootstrap-datepicker/dist/locales/bootstrap-datepicker.es.min.js"></script>
<script>
    update()
    jQuery('#date-range').datepicker({
        toggleActive: true,
        format: 'dd/mm/yyyy',
        language: "es",
        todayHighlight: true
    });

    function update() {
        $('#content-recuperacion').html('<div class="loading text-center"><img src="../assets/images/loading.gif" alt="loading" /><br/>Un momento, por favor...</div>');
        $('#content-recuperacion').load('../templates/Reacondicionamiento/trabajosRecuperacion.php');
        clearForm('filtrado');
    }

    /********** ENVIO DE RECUPERACION ***********/
    $("#formAddRecuperacion").submit(function(e) {
        e.preventDefault();
        formData = $(this).serialize();
        $.ajax({
            url: '../Controller/reacondicionamiento.php?op=agregarrecuperacion',
            data: formData,
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                    bloqueoModal(e, 'block-recuperacion', 2)
                    setTimeout(() => {
                        cerrarModal("recuperaModal")
                        update()
                    }, 1000);


                } else if (resp[0] == 0) {
                    bloqueoModal(e, 'block-recuperacion', 2)

                    notificaBad(resp[1])


                }
            },
            beforeSend: function() {
                bloqueoModal(e, 'block-recuperacion', 1)

            }

        });
    });

    function seleccionRendInicio(select2) {
        programa = $('#idRendInicio option:selected').data('programa');
        $('#idCatPrograma').val(programa).trigger('change.select2');
        //Agregar limites de piezas recuperadas
        pzas = $('#idRendInicio option:selected').data('pzas');

        $("#totalRecuperado").attr('max', pzas);
    }

    function cargaFormRecuperacion() {
        $('#content-formRecupera').html('<div class="loading text-center"><img src="../assets/images/loading.gif" alt="loading" /><br/>Un momento, por favor...</div>');
        $('#content-formRecupera').load('../templates/Reacondicionamiento/cargaRegistroRecuperacion.php');
    }
    /*************** FILTRADO DE TABLA *********************/
    $("#filtrado").submit(function(e) {
        e.preventDefault();
        formData = $(this).serialize();
        $.ajax({
            url: '../templates/Reacondicionamiento/trabajosRecuperacion.php',
            data: formData,
            type: 'POST',
            success: function(respuesta) {
                $('#content-recuperacion').html(respuesta);


            },
            beforeSend: function() {}

        });
    });
</script>

</html>