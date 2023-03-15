<?php
require_once 'seg.php';
$info = new Seguridad();
require_once('../include/connect_mvc.php');
include("../Models/Mdl_ConexionBD.php");
include("../Models/Mdl_Proveedor.php");
$info->Acceso();
$idUser = $_SESSION['CREident'];
$nameUser = $_SESSION['CREnombreUser'];
setlocale(LC_TIME, 'es_ES.UTF-8');
$debug = 0;
$space = 1;

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
                                        <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                            <label for="proveedor">Proveedor:</label>
                                            <select class="form-control select2" style="width:100%" name="proveedor">
                                                <option value="">Todos los Proveedores</option>
                                                <?php
                                                $DataProveedor = $obj_proveedor->getProveedores("p.estado='1'");
                                                foreach ($DataProveedor as $key => $value) {
                                                    echo "<option value='{$DataProveedor[$key]['id']}'>{$DataProveedor[$key]['nombre']}</option>";
                                                }
                                                ?>
                                            </select>

                                        </div>
                                       <!-- <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                            <label for="pedidos">Tipo de Pedido:</label>
                                            <select class="form-control select2" style="width:100%" name="pedidos">
                                                <option value="">Todos los Pedidos</option>
                                                <option value="1">Con Unidades Disponibles</option>
                                                <option value="2">Sin Unidades Disponibles</option>
                                            </select>

                                        </div>-->
                                        <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 pt-4 mt-1">
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
                                        <div id="content-historial"></div>

                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>
        <!-- Modal -->
        <div class="modal fade" id="modalMP" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog  modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-TWM text-white">
                        <h5 class="modal-title" id="exampleModalLabel">Despliegue de Lotes por Pedido</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="div-despliegueMP">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Cancelación-->
        <div class="modal fade" id="modalCancelar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-TWM text-white">
                        <h5 class="modal-title" id="exampleModalLabel">Cancelación de Pedido</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="formCancelarPedido">
                        <div class="modal-body">
                            <input type="hidden" id="idPedidoCancelar" name="idPedido">
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="form-label required">Motivo de Cancelación del Pedido</label>
                                    <textarea required class="form-control" name="motivoDeCancelacion" id="" cols="5" rows="5"></textarea>
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
                                <button type="button" data-dismiss="modal" class="button btn btn-light">Cerrar</button>
                                <button type="submit" class="button btn btn-success">Editar</button>
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

<script>
    update()
    jQuery('#date-range').datepicker({
        toggleActive: true,
        format: 'dd/mm/yyyy',
        language: "es",
        todayHighlight: true



    });

    function update() {
        $('#content-historial').html('<div class="loading text-center"><img src="../assets/images/loading.gif" alt="loading" /><br/>Un momento, por favor...</div>');
        $('#content-historial').load('../templates/Pedidos/historialPedidos.php');
        clearForm("filtrado");

    }
    /***************** CARGA DESPLIEGUE DE LOTES*********************/
    function cargaLotes(idPedido) {
        $.ajax({
            url: '../templates/Pedidos/cargaDespliegueLotes.php',
            data: {
                id: idPedido
            },
            type: 'POST',
            success: function(respuesta) {
                $('#div-despliegue').html(respuesta);


            },
            beforeSend: function() {}

        });

    }
    /***************** CARGA DESPLIEGUE DE MP*********************/
    function cargaMP(idPedido) {
        $.ajax({
            url: '../templates/Pedidos/cargaDespliegueMP.php',
            data: {
                id: idPedido
            },
            type: 'POST',
            success: function(respuesta) {
                $('#div-despliegueMP').html(respuesta);


            },
            beforeSend: function() {
                $('#content-despliegueMP').html('<div class="loading text-center"><img src="../assets/images/loading.gif" alt="loading" /><br/>Un momento, por favor...</div>');

            }

        });

    }

    /*************** FILTRADO DE TABLA *********************/
    $("#filtrado").submit(function(e) {
        e.preventDefault();
        formData = $(this).serialize();
        $.ajax({
            url: '../templates/Pedidos/historialPedidos.php',
            data: formData,
            type: 'POST',
            success: function(respuesta) {
                $('#content-historial').html(respuesta);


            },
            beforeSend: function() {}

        });
    });

    /********** EDICION DE UNA VENTA ***********/
    $("#formCancelarPedido").submit(function(e) {
        e.preventDefault();
        formData = $(this).serialize();
        $.ajax({
            url: '../Controller/pedidos.php?op=cancelarpedido',
            data: formData,
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                    setTimeout(() => {
                        bloqueoBtn("bloqueo-btn-2", 2)
                        setTimeout(() => {
                            $("#modalCancelar").modal('hide');
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
    /***************** CANCELAR PEDIDO ****************/
    function cancelarPedido(id){
        $("#idPedidoCancelar").val(id);
    }
</script>

</html>