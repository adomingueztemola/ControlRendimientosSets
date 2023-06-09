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
<link href="../assets/libs/sweetalert2/dist/sweetalert2.min.css" rel="stylesheet">

<style>
    .contenedor {
        height: 370px;
        width: auto;
        border: 1px solid #eee;
        padding: 5px;
        overflow-y: auto;
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
                                            <label for="date-range">Búsqueda por Rangos de Fechas: </label>
                                            <div class="input-daterange input-group" id="date-range">
                                                <input type="text" class="form-control" autocomplete="off" name="date-start" value="">
                                                <div class="input-group-append">
                                                    <span class="input-group-text bg-TWM b-0 text-white">AL</span>
                                                </div>
                                                <input type="text" class="form-control" autocomplete="off" name="date-end" value="">
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                            <label for="programa">Programa:</label>
                                            <select class="form-control ProgramaMedidoFilter" style="width:100%" name="programa" id="programa">
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

                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class=" offset-lg-11">
                        <button class="btn btn-TWM" type="button" data-toggle="modal" data-target=".modalcarga">Carga Excel</button>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="card border">
                            <div class="card-body">
                                <div class="row mb-1">
                                    <div class="col-md-11"></div>
                                    <div class="col-md-1 text-right">
                                        <button class="btn button btn-rounded btn-sm btn-light" onclick="update()" title="Actualizar Historial"> <i class="fas fa-history"></i></button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div id="content-lotes"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal fade modalcarga" id="modalcarga" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="modaldecarga" aria-hidden="true">
                    <div class="modal-dialog modal-lg">

                        <div class="modal-content blockModal">
                            <div class="modal-header bg-TWM">
                                <h5 class="modal-title text-white">Carga de Reporte de Teseo en Excel</h5>
                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form id="formcarga">
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-lg-3 col-md-3 col-xs-3 col-sm-3">
                                            <label for="folioLote" class="form-label required">Folio de Lote </label>
                                            <input type="text" id="folioLote" name="folioLote" autocomplete="off" required class="form-control select2Form">
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-xs-3 col-sm-3">
                                            <label for="grosor" class="form-label required">Grosor </label>
                                            <select class="form-control GrosorFilter select2Form" required style="width:100%" name="grosor" id="grosorReporte">
                                            </select>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-xs-6 col-sm-6">
                                            <label for="programaReporte" class="form-label required">Programa</label>
                                            <select class="form-control ProgramaMedidoFilter select2Form" required style="width:100%" name="programa" id="programaReporte">
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row m-1">
                                        <div class="col-md-8">
                                            <label>Pegue los datos de Excel aquí:</label>
                                            <button type="button" onclick="limpiarReporte()" class="btn btn-xs btn-danger">Limpiar</button>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <textarea id="excel_data" class="form-control"></textarea><br>
                                            <input type="button" onclick="javascript:generateTable()" value="Generar tabla" />
                                        </div>
                                    </div>
                                    <br>
                                    <p>Los datos de la tabla aparecerán a continuación.</p>
                                    <hr>
                                    <div id="excel_table"></div>
                                    <input type="hidden" name="reporte" id="reporte">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" onclick="limpiarFormCarga()" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                                    <button type="submit" id="btn-save" disabled class="btn btn-success">Guardar</button>
                                </div>
                            </form>
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
<script src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>
<script src="../assets/libs/moment/moment.js"></script>
<script src="../assets/libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="../assets/libs/bootstrap-datepicker/dist/locales/bootstrap-datepicker.es.min.js"></script>
<script src="../assets/scripts/selectFiltros.js"></script>
<script src="../assets/libs/block-ui/jquery.blockUI.js"></script>
<script src="../assets/scripts/Medicion/lecturaReporte.js"></script>
<script src="../assets/libs/sweetalert2/dist/sweetalert2.all.min.js"></script>
<script>
    update()

    jQuery('#date-range').datepicker({
        toggleActive: true,
        format: 'dd/mm/yyyy',
        language: "es",
        todayHighlight: true



    });

    $("#formcarga").submit(function(e) {
        e.preventDefault();
        formData = $(this).serialize();
        $.ajax({
            type: 'POST',
            url: '../Controller/medicion.php?op=agregarreporte',
            data: formData,
            success: function(respuesta) {
                var resp = respuesta.split('|');
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                    bloqueoModal(e, "blockModal", 2)
                    $('#modalcarga').modal('hide');
                    setTimeout(() => {
                        update()
                    }, 1000);
                    limpiarFormCarga()
                } else {
                    notificaBad(resp[1])
                    bloqueoModal(e, "blockModal", 2)

                }
            },
            beforeSend: function() {
                bloqueoModal(e, "blockModal", 1)
            }
        });

    })

    /*********** ACTUALIZA LISTA DE LOTES***************/
    function update() {
        cargaContenido("content-lotes", "../templates/Medicion/medicion.php", '1')

    }

    /*************** FILTRADO DE TABLA *********************/
    $("#filtrado").submit(function(e) {
        e.preventDefault();
        formData = $(this).serialize();
        $.ajax({
            url: '../templates/Medicion/medicion.php',
            data: formData,
            type: 'POST',
            success: function(respuesta) {
                $('#content-lotes').html(respuesta);


            },
            beforeSend: function() {
                $('#content-lotes').html('<div class="loading text-center"><img src="../assets/images/loading.gif" alt="loading" /><br/>Un momento, por favor...</div>');


            }

        });
    });
    /*************** LIMPIAR REPORTE *********************/
    function limpiarReporte() {
        $("#excel_data").val("")
    }
    /*************** LIMPIAR FORMULARIO DE CARGA *********************/
    function limpiarFormCarga() {
        $("#excel_table").html("");
        clearForm("formcarga")
    }
</script>

</html>