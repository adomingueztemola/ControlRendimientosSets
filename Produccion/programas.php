<?php
require_once 'seg.php';
$info = new Seguridad();

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
                    <div class="col-lg-4 col-md-4 col-md-4 col-sm-12 col-xs-12">
                        <div class="card border">
                            <div class="card-body" id="">
                                <form id="formAddPrograma">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label class="form-label required" for="programa">Ingresa Programa:</label>
                                            <input type="text" class="form-control Mayusculas" autocomplete="off" required name="programa" id="programa">
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label required" for="areaNeta">Ingresa Área Neta:</label>
                                            <input type="number" step="0.00001" min="0" class="form-control" autocomplete="off" required name="areaNeta" id="areaNeta">
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6"></div>
                                        <div class="col-md-6 text-rigth">
                                            <div id="bloqueo-btn-1" style="display:none">
                                                <button class="btn btn-TWM" type="button" disabled="">
                                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                    Espere...
                                                </button>

                                            </div>
                                            <div id="desbloqueo-btn-1">
                                                <button type="reset" class="button btn btn-danger">Cancelar</button>
                                                <button type="submit" class="button btn btn-success">Guardar</button>
                                            </div>
                                        </div>

                                    </div>
                                </form>


                            </div>

                        </div>
                    </div>
                    <div class="col-lg-8  col-md-8 col-sm-12 col-xs-12">
                        <div class="card border">
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col-lg-10  col-md-10 col-sm-10 col-xs-10 text-right"></div>
                                    <div class="col-lg-2  col-md-2 col-sm-2 col-xs-2 text-right">
                                        <button class="btn btn-TWM dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="verText">
                                            Ver Todos
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="javascript:void(0)" onclick="filtrado(1)">Activas</a>
                                            <a class="dropdown-item" href="javascript:void(0)" onclick="filtrado(0)">Inhabilitadas</a>

                                            <div role="separator" class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="javascript:void(0)" onclick="filtrado(-1)">Todos</a>

                                        </div>
                                    </div>

                                </div>
                                <div class="table-responsive" id="content-programas">

                                </div>
                            </div>

                        </div>

                    </div>
                </div>

            </div>

        </div>

        <!-- AREA DE EDICION DE LA AREA NETA DE C/ PROGRAMA -->
        <div class="modal" id="ModalEditPrograma" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-TWM text-white">
                        <h5 class="modal-title" id="title-edit"></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="formEditPrograma">
                        <div class="modal-body" id="bodyToEdit">
                            <input type="hidden" name="id" id="id">
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="form-label required" for="areaNetaEdit">Ingresa Área Neta:</label>
                                    <input type="number" step="0.00001" min="0" class="form-control" autocomplete="off" required name="areaNeta" id="areaNetaEdit">
                                </div>
                            </div>


                        </div>
                        <div class="modal-footer">
                            <div id="bloqueo-btn-2" style="display:none">
                                <button class="btn btn-TWM" type="button" disabled="">
                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    Espere...

                                </button>

                            </div>
                            <div id="desbloqueo-btn-2">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                                <button type="submit" class="button btn btn-success">Guardar Cambios</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>


</body>


<?= $info->creaFooter(); ?>

<?php include("../templates/libsJS.php"); ?>
<script src="../assets/extra-libs/datatables.net/js/jquery.dataTables.min-ESP.js"></script>
<script src="../assets/libs/toastr/build/toastr.min.js"></script>

<script>
    update()

    function update() {
        $('#content-programas').html('<div class="loading text-center"><img src="../assets/images/loading.gif" alt="loading" /><br/>Un momento, por favor...</div>');
        $('#content-programas').load('../templates/Extras/programas.php');


    }

    /********** ENVIO DE PROGRAMA ***********/
    $("#formAddPrograma").submit(function(e) {
        e.preventDefault();
        formData = $(this).serialize();
        $.ajax({
            url: '../Controller/programas.php?op=agregarprograma',
            data: formData,
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                    setTimeout(() => {
                        bloqueoBtn("bloqueo-btn-1", 2)
                        clearForm("formAddPrograma")
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
      /********** EDICION DE UN PROGRAMA ***********/
      $("#formEditPrograma").submit(function(e) {
        e.preventDefault();
        formData = $(this).serialize();
        $.ajax({
            url: '../Controller/programas.php?op=editarprograma',
            data: formData,
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                    setTimeout(() => {
                        bloqueoBtn("bloqueo-btn-2", 2)
                        clearForm("formEditPrograma")
                        setTimeout(() => {
                            $("#ModalEditPrograma").modal('hide');
                            update()

                        }, 1000);
                    }, 1000);


                } else if (resp[0] == 0) {
                    notificaBad(resp[1])
                    bloqueoBtn("bloqueo-btn-2", 2)


                }
            },
            beforeSend: function() {
                bloqueoBtn("bloqueo-btn-2", 1)
            }

        });
    });
    /********CAMBIA ESTATUS DE LAS PROGRAMAS**********/
    function cambiaEstatus(id, EstatusActual) {
        $.post("../Controller/programas.php?op=cambiaestatus", {
                id: id,
                estatus: EstatusActual
            },
            function(respuesta) {
                var resp = respuesta.split('|');
                if (resp[0] == 1) {

                    setTimeout(() => {
                        notificaSuc(resp[1]);
                        $("#divEstatus-" + id).html(resp[2])

                    }, 1000);
                } else {
                    notificaBad(resp[1]);

                }
            });
    }
     /*********************** FUNCION PARA CARGAR DATOS DE LA EDICION ******************************/
     function cargarEdicion(id) {
        $.ajax({
            type: 'POST',
            url: '../Controller/programas.php?op=detallado',
            data: {
                id: id
            },
            success: function(respuesta) {
                console.log(respuesta)
                resp = respuesta.split('|')
                if (resp[0] == '1') {
                    json = JSON.parse(resp[1])
                    $("#title-edit").text('Programa: ' + json.nombre)
                    $("#id").val(json.id)
                    $("#areaNetaEdit").val(json.areaNeta)

                } else {
                    notificaBad(resp[1]);
                }

            },
            beforeSend: function() {}
        });



    }
    /*********************** Filtrado *************************/
    function filtrado(idBusqueda) {
        //Casteo de titulos
        let title = '';
        switch (idBusqueda) {
            case 1:
                title = "Activos"
                break;
            case 0:
                title = "Inhabilitados"

                break;
            case -1:
                title = "Ver Todos"
                break;

        }
        $('#verText').html(title);

        $.ajax({
            type: 'POST',
            url: '../templates/Extras/programas.php',
            data: {
                id: idBusqueda
            },
            success: function(respuesta) {
                $('#content-programas').html(respuesta);

            },
            beforeSend: function() {
                $('#content-programas').html('<div class="loading text-center"><img src="../assets/images/loading.gif" alt="loading" /><br/>Un momento, por favor...</div>');
            }
        });
    }
</script>

</html>