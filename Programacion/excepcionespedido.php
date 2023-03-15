<?php
require_once 'seg.php';
$info = new Seguridad();
require_once('../include/connect_mvc.php');
include("../Models/Mdl_ConexionBD.php");
include("../Models/Mdl_Programa.php");
include("../Models/Mdl_Proceso.php");
include("../Models/Mdl_MateriaPrima.php");
include("../Models/Mdl_Proveedor.php");

$info->Acceso();
$idUser = $_SESSION['CREident'];
$nameUser = $_SESSION['CREnombreUser'];
setlocale(LC_TIME, 'es_ES.UTF-8');
$debug = 1;
$space = 1;
$obj_programa = new Programa($debug, $idUser);
$obj_proceso = new ProcesoSecado($debug, $idUser);
$obj_materia = new MateriaPrima($debug, $idUser);
$obj_proveedor = new Proveedor($debug, $idUser);

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
                                        <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                            <label for="date-range">Búsqueda de por Rangos de Fechas: </label>
                                            <div class="input-daterange input-group" id="date-range">
                                                <input type="text" class="form-control" name="date-start" value="<?= date("01/m/Y") ?>">
                                                <div class="input-group-append">
                                                    <span class="input-group-text bg-TWM b-0 text-white">AL</span>
                                                </div>
                                                <input type="text" class="form-control" name="date-end" value="<?= date("t/m/Y") ?>">
                                            </div>
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
                                        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                            <label for="proveedor">Proveedor:</label>
                                            <select class="form-control select2" style="width:100%" name="proveedor" id="proveedor">
                                                <option value="">Todos los Proveedores</option>
                                                <?php
                                                $DataProveedor = $obj_proveedor->getProveedores("p.estado='1'");
                                                foreach ($DataProveedor as $key => $value) {
                                                    echo "<option value='{$DataProveedor[$key]['id']}'>{$DataProveedor[$key]['nombre']}</option>";
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
                                        <button class="btn button btn-rounded btn-sm btn-light" onclick="update()" title="Actualizar Historial"> <i class="fas fa-history"></i></button>
                                    </div>
                                </div>


                                <div class="row">
                                    <div class="col-md-12">
                                        <div id="content-pedidos"></div>

                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>




</body>
<!-- Modal de Ajuste de Pedidos -->
<div class="modal fade" id="modalExcepcion" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-TWM text-white">
                <h5 class="modal-title" id="modalExcepcionTitle">Ajuste de Pedido</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formAjustePedido">
                <div class="modal-body" id="cargaContentPedido">
                </div>
                <div class="modal-footer">
                    <div id="bloqueo-btn-1" style="display:none">
                        <button class="btn btn-TWM" type="button" disabled="">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Espere...
                        </button>

                    </div>
                    <div id="desbloqueo-btn-1">
                        <button type="button" class="btn btn-light" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-success">Guardar</button>
                    </div>

                </div>
            </form>

        </div>
    </div>
</div>
<!-- Modal de Clasificacion de Pedido -->
<div class="modal fade" id="modalClasificacion" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-TWM text-white">
                <h5 class="modal-title" id="modalClasificacionTitle"></h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formClasifRecuperacion">
                <div class="modal-body" id="cargaContentClasificacion">
                </div>
                <div class="modal-footer">
                    <div id="bloqueo-btn-2" style="display:none">
                        <button class="btn btn-TWM" type="button" disabled="">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Espere...
                        </button>

                    </div>
                    <div id="desbloqueo-btn-2">
                        <button type="button" class="btn btn-light" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-success">Guardar</button>
                    </div>

                </div>
            </form>

        </div>
    </div>
</div>
<?= $info->creaFooter(); ?>
<?php include("../templates/libsJS.php"); ?>

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
        $('#content-pedidos').html('<div class="loading text-center"><img src="../assets/images/loading.gif" alt="loading" /><br/>Un momento, por favor...</div>');
        $('#content-pedidos').load('../templates/Pedidos/excepcionesPedido.php');
        clearForm("filtrado");

    }

    function cargaFormAjuste(id, numFactura) {
        $('#cargaContentPedido').html('<div class="loading text-center"><img src="../assets/images/loading.gif" alt="loading" /><br/>Un momento, por favor...</div>');
        $('#cargaContentPedido').load('../templates/Pedidos/cargaAjustePedido.php?id=' + id);
        $('#modalExcepcionTitle').html(numFactura)
    }

    function cargaFormClasificacion(id, numFactura) {
        $('#cargaContentClasificacion').html('<div class="loading text-center"><img src="../assets/images/loading.gif" alt="loading" /><br/>Un momento, por favor...</div>');
        $('#cargaContentClasificacion').load('../templates/Pedidos/cargaClasificacionRecepcion.php?id=' + id);
        $('#modalClasificacionTitle').html(numFactura)
    }

    /*************** FILTRADO DE TABLA *********************/
    $("#filtrado").submit(function(e) {
        e.preventDefault();
        formData = $(this).serialize();
        $.ajax({
            url: '../templates/Pedidos/excepcionesPedido.php',
            data: formData,
            type: 'POST',
            success: function(respuesta) {
                $('#content-pedidos').html(respuesta);


            },
            beforeSend: function() {
                $('#content-pedidos').html('<div class="loading text-center"><img src="../assets/images/loading.gif" alt="loading" /><br/>Un momento, por favor...</div>');


            }

        });
    });
    /*************** Ver Seccion de nota de credito *********************/
    function verNotaCredito(radio) {
        if ($(radio).is(':checked')) {
            $("#divNotaCredito").prop('hidden', false);
            $("#notaCredito").prop('disabled', false);
            $("#cantidad").attr("max", $("#cantidad").data("max"));

        }
    }

    function ocultarNotaCredito(radio) {
        if ($(radio).is(':checked')) {
            $("#divNotaCredito").prop('hidden', true);
            $("#notaCredito").prop('disabled', true);
            $("#cantidad").removeAttr("max");


        }
    }
    /*************** FORMULARIO DE AJUSTES DE PEDIDOS *********************/
    $("#formAjustePedido").submit(function(e) {
        e.preventDefault();
        formData = $(this).serialize();
        $.ajax({
            url: '../Controller/pedidos.php?op=ajustepedido',
            data: formData,
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                    setTimeout(() => {
                        bloqueoBtn("bloqueo-btn-1", 2)
                        update()
                        $("#modalExcepcion").modal("hide");
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

    /*************** FORMULARIO DE CLASIFICACION *********************/
    $("#formClasifRecuperacion").submit(function(e) {
        e.preventDefault();
        formData = $(this).serialize();
        $.ajax({
            url: '../Controller/pedidos.php?op=clasificacionrecepcion',
            data: formData,
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                    setTimeout(() => {
                        bloqueoBtn("bloqueo-btn-2", 2)
                        update()
                        $("#modalClasificacion").modal("hide");
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
</script>

</html>