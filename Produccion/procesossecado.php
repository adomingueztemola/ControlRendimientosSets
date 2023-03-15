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
                                <form id="formAddProceso">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label class="form-label required" for="codigo">Ingresa Código del Proceso:</label>
                                            <input type="text" class="form-control Mayusculas" autocomplete="off" required name="codigo" id="codigo">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label class="form-label required" for="proceso">Ingresa Nombre del Proceso:</label>
                                            <input type="text" class="form-control Mayusculas" autocomplete="off" required name="proceso" id="proceso">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="form-label required">Selecciona el tipo de proceso:</label>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" id="set" name="tipo" value="1" required>
                                            <label class="custom-control-label" for="set">Para Set's</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" id="metros" name="tipo" value="2" required>
                                            <label class="custom-control-label" for="metros">Para m<sup>2</sup></label>
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
                                <div class="table-responsive" id="content-proceso">

                                </div>
                            </div>

                        </div>

                    </div>
                </div>

            </div>

        </div>

        <!-- AREA DE EDICION DE LOS TIPOS DE SECADO -->
        <div class="modal" id="ModalEditProceso" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-TWM text-white">
                        <h5 class="modal-title" id="title-edit"></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="formEditProceso">
                        <div class="modal-body" id="bodyToEdit">
                            <input type="hidden" name="id" id="id">
                            <label class="form-label required">Selecciona el tipo de proceso:</label>
                            <div class="custom-control custom-radio">
                                <input type="radio" class="custom-control-input" id="setEdit" name="tipo" value="1" required>
                                <label class="custom-control-label" for="setEdit">Para Set's</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input type="radio" class="custom-control-input" id="metrosEdit" name="tipo" value="2" required>
                                <label class="custom-control-label" for="metrosEdit">Para m<sup>2</sup></label>
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
        $('#content-proceso').html('<div class="loading text-center"><img src="../assets/images/loading.gif" alt="loading" /><br/>Un momento, por favor...</div>');
        $('#content-proceso').load('../templates/Extras/procesosSecado.php');


    }

    /********** ENVIO DE UN PROCESO ***********/
    $("#formAddProceso").submit(function(e) {
        e.preventDefault();
        formData = $(this).serialize();
        $.ajax({
            url: '../Controller/procesosSecado.php?op=agregarproceso',
            data: formData,
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                    setTimeout(() => {
                        bloqueoBtn("bloqueo-btn-1", 2)
                        clearForm("formAddProceso")
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
    /********** EDICION DE UN PROCESO ***********/
    $("#formEditProceso").submit(function(e) {
        e.preventDefault();
        formData = $(this).serialize();
        $.ajax({
            url: '../Controller/procesosSecado.php?op=editarproceso',
            data: formData,
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                    setTimeout(() => {
                        bloqueoBtn("bloqueo-btn-2", 2)
                        clearForm("formEditProceso")
                        setTimeout(() => {
                            $("#ModalEditProceso").modal('hide');
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
    /********CAMBIA ESTATUS DE PROCESO**********/
    function cambiaEstatus(id, EstatusActual) {
        $.post("../Controller/procesosSecado.php?op=cambiaestatus", {
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
    /*********************** Filtrado *************************/
    function filtrado(idBusqueda) {
        //Casteo de titulos
        let title = '';
        switch (idBusqueda) {
            case 1:
                title = "Activas"
                break;
            case 0:
                title = "Inhabilitadas"

                break;
            case -1:
                title = "Ver Todos"
                break;

        }
        $('#verText').html(title);

        $.ajax({
            type: 'POST',
            url: '../templates/Extras/procesosSecado.php',
            data: {
                id: idBusqueda
            },
            success: function(respuesta) {
                $('#content-proceso').html(respuesta);

            },
            beforeSend: function() {
                $('#content-proceso').html('<div class="loading text-center"><img src="../assets/images/loading.gif" alt="loading" /><br/>Un momento, por favor...</div>');
            }
        });
    }
    /*********************** FUNCION PARA CARGAR DATOS DE LA EDICION ******************************/
    function cargarEdicion(id) {
        $.ajax({
            type: 'POST',
            url: '../Controller/procesosSecado.php?op=detallado',
            data: {
                id: id
            },
            success: function(respuesta) {
                console.log(respuesta)
                resp = respuesta.split('|')
                if (resp[0] == '1') {
                    json = JSON.parse(resp[1])
                    $("#title-edit").text('Código ' + json.codigo + ': ' + json.nombre)
                    $("#id").val(json.id)
                    if (json.tipo == '1') {
                        $("#setEdit").prop('checked', true);
                        $("#metrosEdit").prop('checked', false);

                    } else if (json.tipo == '2') {
                        $("#metrosEdit").prop('checked', true);
                        $("#setEdit").prop('checked', false);


                    }


                } else {
                    notificaBad(resp[1]);
                }

            },
            beforeSend: function() {}
        });



    }
</script>

</html>