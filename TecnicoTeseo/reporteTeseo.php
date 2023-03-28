<?php
require_once 'seg.php';
$info = new Seguridad();
require_once('../include/connect_mvc.php');
include("../Models/Mdl_Proceso.php");
$info->Acceso();
$idUser = $_SESSION['CREident'];
$nameUser = $_SESSION['CREnombreUser'];
setlocale(LC_TIME, 'es_ES.UTF-8');
$debug = 0;
$space = 1;
$obj_programa = new Programa($debug, $idUser);
$obj_proceso = new ProcesoSecado($debug, $idUser);
$obj_materia = new MateriaPrima($debug, $idUser);
?>
<!DOCTYPE html>
<html dir="ltr" lang="<?= $info->lng; ?>">

<?php include("../templates/header.php"); ?>
<link href="../assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="../assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
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
                                                <input type="text" autocomplete="off" class="form-control" name="date-start" value="<?=date("01/01/Y")?>">
                                                <div class="input-group-append">
                                                    <span class="input-group-text bg-TWM b-0 text-white">AL</span>
                                                </div>
                                                <input type="text" autocomplete="off" class="form-control" name="date-end" value="<?=date("t/12/Y")?>">
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                            <label for="programa">Programa:</label>
                                            <select class="form-control select2" style="width:100%" name="programa" id="programaFilter">
                                                <option value="">Todos los Programas</option>
                                                <?php
                                                $DataPrograma = $obj_programa->getPrograma("p.estado='1'", "p.tipo='1'");
                                                foreach ($DataPrograma as $key => $value) {
                                                    echo "<option value='{$DataPrograma[$key]['id']}'>{$DataPrograma[$key]['nombre']}</option>";
                                                }
                                                ?>
                                            </select>

                                        </div>
                                        <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12">
                                            <label for="proceso">Proceso:</label>
                                            <select class="form-control select2" style="width:100%" name="proceso" id="proceso">
                                                <option value="">-</option>
                                                <?php
                                                $DataProceso = $obj_proceso->getProcesos("pr.estado='1'", "pr.tipo='1'");
                                                foreach ($DataProceso as $key => $value) {
                                                    echo "<option value='{$DataProceso[$key]['id']}'>{$DataProceso[$key]['codigo']}</option>";
                                                }
                                                ?>
                                            </select>

                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                            <label for="materia">Materia Prima:</label>
                                            <select class="form-control select2" style="width:100%" name="materia" id="materia">
                                                <option value="">Todos las Materias Primas</option>
                                                <?php
                                                $DataMateria = $obj_materia->getMaterias("mt.estado='1'");
                                                foreach ($DataMateria as $key => $value) {
                                                    echo "<option value='{$DataMateria[$key]['id']}'>{$DataMateria[$key]['nombre']}</option>";
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
                                <!-- BOTON DE CAMBIO DE PROGRAMA DE LOTE REASIGNACION                    -->
                                <div class="row mb-2">
                                    <div class="col-md-11">
                                        <button class="btn btn-md btn-dark" data-toggle="modal" data-target="#reasignarModal"><i class="far fa-sun"></i>Reasignar Programa</button>
                                    </div>
                                    <div class="col-md-1 text-right">
                                        <button class="btn button btn-rounded btn-sm btn-light" onclick="update()" title="Actualizar Historial"> <i class="fas fa-history"></i></button>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div id="content-lotes">

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>

        <!-- INICIAL DE MODAL DE REASIGNAR PROGRAMA -->
        <div class="modal fade" id="reasignarModal" role="dialog" aria-labelledby="reasignarModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content conteoModal-block">
                    <div class="modal-header bg-TWM text-white">
                        <h5 class="modal-title" id="reasignarModalLabel">Reasignar Programa a Lote Teseo</h5>
                        <button type="button" class="close text-white" onclick="limpiarForm()"  data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="formChangeProgram">
                        <div class="modal-body">
                            <div class="row">
                                <input type="hidden" name="option" value="1">
                                <div class="col-md-12">
                                    <label class="form-label" for="lote">Lote</label>
                                    <select name="idLote" id="lote" style="width:100%" class="control-form LoteTeseoFilter select2Form"></select>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label" for="programa">Programa</label>
                                    <select name="programa" id="programa" style="width:100%" class="control-form ProgramaSetsFilter select2Form"></select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" onclick="limpiarForm()" class="btn btn-light" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-success">Modificar Lote</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- FIN DE MODAL DE REASIGNAR PROGRAMA -->

    </div>

</body>



<?= $info->creaFooter(); ?>
<?php include("../templates/libsJS.php"); ?>
<script src="../assets/libs/block-ui/jquery.blockUI.js"></script>
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
        cargaContenido("content-lotes", "../templates/Rendimiento/cargaReporteLotesCapturaTeseo.php", '1')

    }

    function limpiarForm(){
        clearForm("formChangeProgram")

    }


    /*************** FILTRADO DE TABLA *********************/
    $("#filtrado").submit(function(e) {
        e.preventDefault();
        formData = $(this).serialize();
        $.ajax({
            url: '../templates/Rendimiento/cargaReporteLotesCapturaTeseo.php',
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

    /**************************************** CAMBIO DE PROGRAMA ****************************************************/
    $("#formChangeProgram").submit(function(e) {
        e.preventDefault();
        formData = $(this).serialize();
        $.ajax({
            url: '../Controller/rendimiento.php?op=reasignaprograma',
            data: formData,
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                    bloqueoModal(e, "conteoModal-block", 2)
                    clearForm("formChangeProgram")
                    $("#reasignarModal").modal('hide');
                    setTimeout(() => {
                        update()
                    }, 1000);


                } else if (resp[0] == 0) {
                    notificaBad(resp[1])
                    bloqueoModal(e, "conteoModal-block", 2)


                }
            },
            beforeSend: function() {
                bloqueoModal(e, "conteoModal-block", 1)
            }

        });
    });
</script>

</html>