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
                                                <input type="text" class="form-control" autocomplete="off" name="date-start" value="<?= date("01/m/Y") ?>">
                                                <div class="input-group-append">
                                                    <span class="input-group-text bg-TWM b-0 text-white">AL</span>
                                                </div>
                                                <input type="text" class="form-control" autocomplete="off" name="date-end" value="<?= date("t/m/Y") ?>">
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                            <label for="programa">Programa:</label>
                                            <select class="form-control ProgramaCalzadoFilter" style="width:100%" name="programa" id="programa">
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

                    <div class="modal fade modalcarga" id="modalcarga" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="modaldecarga" aria-hidden="true">
                        <div class="modal-dialog modal-lg">

                            <div class="modal-content block-modalScrap">
                                <div class="modal-header bg-TWM">
                                    <h5 class="modal-title text-white">Carga de Reporte de Teseo en Excel</h5>
                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-lg-8 col-md-8 col-xs-8 col-sm-8">
                                            <form id="FormLecturaScrap">
                                                <label for="inputExcel">Reporte de Teseo</label>
                                                <div class="input-group mb-3">
                                                    <input type="file" class="form-control" id="inputExcel" name="file" accept=".xlsm">
                                                    <div class="input-group-append">
                                                        <div id="bloqueo-btn-lect" style="display:none">
                                                            <button class="btn btn-TWM" type="button" disabled="">
                                                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                            </button>
                                                        </div>
                                                        <div id="desbloqueo-btn-lect">
                                                            <button class="btn btn-md btn-success" type="button" onclick="lecturaReporte()"><i class="fas fa-eye"></i></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-xs-4 col-sm-4">
                                            <label for="folioLote">Folio de Lote </label>
                                            <input type="text" id="folioLote" name="folioLote" class="form-control">
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6 col-xs-6 col-sm-6">
                                            <label for="folioLote">Programa</label>
                                            <select class="form-control ProgramaCalzadoFilter" style="width:100%" name="programa" id="programaReporte">
                                            </select>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-xs-6 col-sm-6">
                                            <table class="table table-sm table-bordered">
                                                <tbody>
                                                    <tr>
                                                        <td>Área Total Dm<sup>2</sup></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Área Total Ft<sup>2</sup></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div id="contenedor" class="contenedor">

                                                    </div>
                                                </div>
                                            </div>


                                        </div>
                                    </div>

                                </div>

                                <div class="modal-footer">
                                    <button type="button" onclick="limpiarCargaExcel()" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                                    <button type="button" id="btn-save" onclick="finalizarCarga()" disabled class="btn btn-success">Guardar</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class=" offset-lg-11">
                        <button class="btn btn-TWM" type="button" data-toggle="modal" data-target=".modalcarga">Carga
                            Excel</button>
                    </div>
                </div>



                <br>
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="card border">
                            <div class="card-body">
                                <div class="row">
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

<script>
    update()

    jQuery('#date-range').datepicker({
        toggleActive: true,
        format: 'dd/mm/yyyy',
        language: "es",
        todayHighlight: true



    });
    /*********** ACTUALIZA LISTA DE LOTES***************/
    function update() {
        cargaContenido("content-lotes", "../templates/Medicion/medicion.php", '1')
        limpiarCargaExcel()

    }

    function limpiarCargaExcel() {
        clearForm("FormLecturaScrap");
        $("#fechaSalida").val("");
        $("#contenedor").html("");
    }

    function updateTabla() {
        cargaContenido("contenedor", "../templates/Scrap/reporteTarima.php", '1')

    }

    /*************** FILTRADO DE TABLA *********************/
    $("#filtrado").submit(function(e) {
        e.preventDefault();
        formData = $(this).serialize();
        $.ajax({
            url: '../templates/Scrap/scrap.php',
            data: formData,
            type: 'POST',
            success: function(respuesta) {
                $('#content-lotes').html(respuesta);


            },
            beforeSend: function() {
                $('#content-lotes').html(
                    '<div class="loading text-center"><img src="../assets/images/loading.gif" alt="loading" /><br/>Un momento, por favor...</div>'
                );


            }

        });
    });

    function lecturaReporte() {
        bloqueoBtn("bloqueo-btn-lect", 1)
        valueXLX = $("#inputExcel").val();
        inptXLX = document.getElementById('inputExcel')
        //VALIDA QUE SE HAYA CARGADO UN ARCHIVO
        if (inptXLX.files.length == 0) {
            notificaBad("<b>Asegúrese de cargar el archivo Excel correcto.</b><br> Si el error persiste contacte a su departamento de Sistemas.");
            return 0;
        }
        //DESGLOSE DE ARCHIVO CARGADO
        var file = inptXLX.files[0];
        if (file.type != "application/vnd.ms-excel.sheet.macroEnabled.12") {
            notificaBad("<b>Asegúrese de cargar el archivo Excel correcto.</b><br> Si el error persiste contacte a su departamento de Sistemas.");
            return 0;
        }
        //LECTURA DE ARCHIVO MACRO
        var formElement = document.getElementById("FormLecturaScrap");
        var form = new FormData(formElement);
        $.ajax({
            type: 'POST',
            url: '../Controller/scrap.php?op=lecturascrap',
            data: form,
            processData: false,
            contentType: false,
            success: function(respuesta) {
                var resp = respuesta.split('|');
                if (resp[0] == 1) {
                    setTimeout(() => {
                        notificaSuc(resp[1]);
                        bloqueoBtn("bloqueo-btn-lect", 2)

                    }, 1500);
                } else {
                    notificaBad(resp[1]);
                    bloqueoBtn("bloqueo-btn-lect", 2)


                }
                updateTabla()
            },
            beforeSend: function() {

            }
        });

    }

    function validaPaseDeReporte(incompletasEncontradas) {
        if (parseInt(incompletasEncontradas) > 0) {
            $("#btn-save").prop("disabled", true);

        } else {
            $("#btn-save").prop("disabled", false);

        }
    }
</script>

</html>