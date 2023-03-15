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

$obj_programa = new Programa($debug, $idUser);
$obj_proceso = new ProcesoSecado($debug, $idUser);
$obj_materia = new MateriaPrima($debug, $idUser);


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
                <?php include("../templates/namePage.php"); ?>

                <div class="row">
                    <div class="col-lg-12 col-md-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="card border">
                            <div class="card-body" id="">
                                <form id="filtrado">
                                    <div class="row">
                                        <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                            <label for="semana">Semana de Producción:</label>
                                            <input type="week" name="semanaProduccion" id="semana" class="form-control">

                                        </div>
                                        <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                            <label for="programa">Programa:</label>
                                            <select class="form-control select2" style="width:100%" name="programa" id="programa">
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
                                                $DataProceso = $obj_proceso->getProcesos("pr.estado='1'");
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
                                <div class="row">
                                    <div class="col-md-11"></div>
                                    <div class="col-md-1 text-right">
                                        <button class="btn button btn-rounded btn-sm btn-light" onclick="update()" title="Actualizar Historial"> <i class="fas fa-history"></i></button>
                                    </div>
                                </div>


                                <div class="row">
                                    <div class="col-md-12">
                                        <div id="content-edicion"></div>

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
    update()
    jQuery('#date-range').datepicker({
        toggleActive: true,
        format: 'dd/mm/yyyy',
        language: "es",
        todayHighlight: true



    });

    function update() {
        $('#content-edicion').html('<div class="loading text-center"><img src="../assets/images/loading.gif" alt="loading" /><br/>Un momento, por favor...</div>');
        $('#content-edicion').load('../templates/Ediciones/gestionEdicion.php');
        clearForm("filtrado");

    }
    /**************************** Cancelar Excepcion Enviada ***********************************/
    function cancelarExcepcion(idExcepcion) {
        $.ajax({
            url: '../Controller/excepciones.php?op=cancelar',
            data: {
                id: idExcepcion
            },
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                    setTimeout(() => {
                        bloqueoBtn("bloqueo-btn-2", 2)
                        update()
                    }, 1000);
                } else if (resp[0] == 0) {
                    bloqueoBtn("bloqueo-btn-2", 2)

                    notificaBad(resp[1])
                }
            },
            beforeSend: function() {
                bloqueoBtn("bloqueo-btn-2", 1)

            }
        });

    }
    /**************************** Aceptar Excepcion Enviada ***********************************/
    $("#formAceptarExcepcion").submit(function(e) {
        e.preventDefault();
        formData = $(this).serialize();
        $.ajax({
            url: '../Controller/excepciones.php?op=aceptar',
            data: formData,
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                    bloqueoBtn("bloqueo-btn-1", 2)
                    $("#ModalAceptarExcepciones").modal('hide');
                    setTimeout(() => {
                        update()
                    }, 1000);


                } else if (resp[0] == 0) {
                    notificaBad(resp[1])
                    bloqueoBtn("bloqueo-btn-1", 2)


                }
            },
            beforeSend: function() {
                bloqueoBtn("bloqueo-btn-1", 1)
            }

        });
    });
    /*************** FILTRADO DE TABLA *********************/
    $("#filtrado").submit(function(e) {
        e.preventDefault();
        formData = $(this).serialize();
        $.ajax({
            url: '../templates/Excepciones/excepciones.php',
            data: formData,
            type: 'POST',
            success: function(respuesta) {
                $('#content-excepciones').html(respuesta);


            },
            beforeSend: function() {}

        });
    });
</script>

</html>