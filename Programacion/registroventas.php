<?php
require_once 'seg.php';
$info = new Seguridad();
require_once('../include/connect_mvc.php');
include("../Models/Mdl_ConexionBD.php");
include("../Models/Mdl_Venta.php");

include("../Models/Mdl_TipoVenta.php");
$info->Acceso();
$idUser = $_SESSION['CREident'];
$nameUser = $_SESSION['CREnombreUser'];
setlocale(LC_TIME, 'es_ES.UTF-8');
$debug = 0;
$space = 1;

$obj_tipoventa = new TipoVenta($debug, $idUser);
$obj_venta = new Venta($debug, $idUser);
$DataAbierto = $obj_venta->getVentaAbiertaXUser();
$DataAbierto = $DataAbierto == '' ? array() : $DataAbierto;
$_abierto = count($DataAbierto) > 0 ? true : false;
//PARAMETROS PARA CARGA DEL FORMULARIO
$fechaFact = $_abierto ? $DataAbierto[0]['fechaFact'] : '';
$numFact = $_abierto ? $DataAbierto[0]['numFactura'] : '';
$numPL = $_abierto ? $DataAbierto[0]['numPL'] : '';
$tipoVenta = $_abierto ? $DataAbierto[0]['idTipoVenta'] : '';
$disabled = $_abierto ? 'disabled' : '';
$hidden = $_abierto ? 'hidden' : '';
$notHidden = $_abierto ? '' : 'hidden';
?>
<!DOCTYPE html>
<html dir="ltr" lang="<?= $info->lng; ?>">

<?php include("../templates/header.php"); ?>
<link href="../assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="../assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
<link href="../assets/libs/sweetalert2/dist/sweetalert2.min.css" rel="stylesheet">

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
                <div class="row mb-2">
                    <div class="col-md-10"></div>
                    <div class="col-md-2 text-right">
                        <button class="btn btn-TWM btn-md" data-toggle="modal" data-target="#consultaModal">Consulta Ventas</button>
                    </div>

                </div>
                <div class="row">
                    <div class="col-md-9 col-lg-9">
                        <div class="card">
                            <div class="card-header bg-TWM">
                                <h5 class="m-b-0 text-white">Detallado de venta</h5>
                            </div>
                            <div class="card-body" id="carga-detallado">

                                <div class="alert alert-light" role="alert">
                                    Sin Artículos Registrados en la Venta
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-lg-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Resumen de la Venta</h5>
                                <hr>
                                <form id="formInitVenta">

                                    <input type="hidden" name="fact" id="fact">
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <label class="form-label required" for="fechaFacturacion">Fecha Facturación:</label>
                                            <input type="date" <?= $disabled ?> required class="form-control" value="<?= $fechaFact ?>" name="fechaFacturacion" id="fechaFacturacion"></input>
                                        </div>
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <label class="form-label required" class="form-label" for="idTipoVenta">Tipo de Venta:</label>
                                            <select required <?= $disabled ?> onchange="requerirFactura()" name="idTipoVenta" id="idTipoVenta" class="form-control select2" style="width:100%">
                                                <option value="">Selecciona Tipo de Venta</option>
                                                <?php
                                                $DataTipo = $obj_tipoventa->getTipos("tv.estado='1'", "tv.cargaVenta<='2'");
                                                foreach ($DataTipo as $key => $value) {
                                                    $selected = $DataTipo[$key]['id'] == $tipoVenta ? 'selected' : '';
                                                    echo "<option data-tipo='{$DataTipo[$key]['tipo']}' data-clasificaventa='{$DataTipo[$key]['cargaVenta']}'  $selected value='{$DataTipo[$key]['id']}'>{$DataTipo[$key]['nombre']}</option>";
                                                }
                                                ?>
                                            </select>

                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="div-factura">
                                            <label class="form-label required" for="numFactura">Núm. de Factura:</label>
                                            <span id="bloqueo-btn-res" style="display:none">
                                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>

                                            </span>
                                            <span id="desbloqueo-btn-res">
                                                <small id="resultbusq"></small>
                                            </span>

                                            <input required <?= $disabled ?> type="text" class="form-control" value="<?= $numFact ?>" name="numFactura" id="numFactura"></input>

                                        </div>

                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <label class="form-label required" for="numPL">Núm. de PL:</label>
                                            <span id="bloqueo-btn-res2" style="display:none">
                                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>

                                            </span>
                                            <span id="desbloqueo-btn-res2">
                                                <small id="resultbusq2"></small>
                                            </span>
                                            <input required <?= $disabled ?> type="text" class="form-control" value="<?= $numPL ?>" name="numPL" id="numPL"></input>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row" id="inicioVenta">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <div id="bloqueo-btn-1" style="display:none">
                                                <button class="btn btn-TWM" type="button" disabled="">
                                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                    Espere...
                                                </button>

                                            </div>
                                            <div id="desbloqueo-btn-1">
                                                <button type="button" <?= $hidden ?> onclick="clearForm('formPedidoLoteo')" class="button btn btn-danger">Limpiar</button>
                                                <button type="submit" <?= $hidden ?> class="button btn btn-success">Iniciar</button>
                                            </div>
                                        </div>
                                    </div>

                                </form>
                                <div class="row mb-2" id="segtoVenta" <?= $notHidden ?>>
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <div id="bloqueo-btn-fin" style="display:none">
                                            <button class="btn btn-TWM" type="button" disabled="">
                                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                Espere...
                                            </button>

                                        </div>
                                        <div id="desbloqueo-btn-fin">
                                            <button type="button" onclick="eliminarVenta()" class="button btn btn-dark"><i class="fas fa-ban"></i>Cancelar</button>
                                            <button type="button" onclick="finalizarVenta()" class="button btn btn-success"><i class="fa fa fa-shopping-cart"></i>Generar Venta</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!---  <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">For Any Support</h5>
                                <hr>
                                <h4><i class="ti-mobile"></i> 9998979695 (Toll Free)</h4> <small>Please contact with us if you have any questions. We are avalible 24h.</small>
                            </div>
                        </div>-->
                    </div>
                    <!-- Modal -->
                    <div class="modal fade" id="consultaModal" role="dialog" aria-labelledby="consultaModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-TWM text-white">
                                    <h5 class="modal-title" id="consultaModalLabel">Consulta Ventas del Lote</h5>
                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <select onchange="verVentas(this)" style="width:100%" class="TodosLotesFilter"></select>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-12">
                                            <table class="table table-sm table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Fecha</th>
                                                        <th>Num. Factura</th>
                                                        <th>P.L.</th>
                                                        <th>Cantidad</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tbody-ventas">
                                                    <tr>
                                                        <td colspan='4'><b>Sin resultados de la busqueda</b></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="reset" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
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

<script src="../assets/scripts/validaNumVenta.js"></script>
<script src="../assets/scripts/validaNumPL.js"></script>
<script src="../assets/scripts/selectFiltros.js"></script>

<script>
    <?php
    if (isset($_SESSION['CRESuccessVenta']) and $_SESSION['CRESuccessVenta'] != '') { ?>
        notificaSuc('<?= $_SESSION['CRESuccessVenta'] ?>')
    <?php
        unset($_SESSION['CRESuccessVenta']);
    }
    if (isset($_SESSION['CREErrorVenta']) and $_SESSION['CREErrorVenta'] != '') { ?>
        notificaBad('<?= $_SESSION['CREErrorVenta'] ?>')
    <?php
        unset($_SESSION['CREErrorVenta']);
    }
    if ($_abierto) {
        echo "updateRegistro()";
    }
    ?>

    function verVentas(select) {
        id = $(select).val()
        $.ajax({
            url: '../Controller/ventas.php?op=getventasxlote',
            data: {
                id: id
            },
            type: 'POST',
            async: false,
            dataType: "json",
            success: function(respuesta) {
                tabla = ""
                if (!respuesta.length) {
                    tabla = `<tr>
                            <td colspan='4'><b>Sin resultados de la busqueda</b></td>
                            </tr>`;
                } else {
                    contadorPack = 0;
                    respuesta.forEach(element => {
                        unidades = Number(parseFloat(element.unidades).toFixed(2)).toLocaleString('es-MX')
                        tabla += `<tr>
                            <td>${element.f_fechaFact}</td> 
                            <td>${element.numFactura}</td> 
                            <td>${element.numPL}</td> 
                            <td>${unidades}</td> 

                        </tr>`;
                    });

                }
                $("#tbody-ventas").html(tabla);

            },


        });
    }

    function updateRegistro() {
        //Tipo de Venta
        let clasificaVenta = $("#idTipoVenta option:selected").data('clasificaventa');
        if (clasificaVenta == '1') {
            $('#carga-detallado').html('<div class="loading text-center"><img src="../assets/images/loading.gif" alt="loading" /><br/>Un momento, por favor...</div>');
            $('#carga-detallado').load('../templates/Ventas/cargaRegistroLoteSets.php');
        }
        if (clasificaVenta == '2') {
            $('#carga-detallado').html('<div class="loading text-center"><img src="../assets/images/loading.gif" alt="loading" /><br/>Un momento, por favor...</div>');
            $('#carga-detallado').load('../templates/Ventas/cargaRegistroLoteMetros.php');
        }


    }
    /********************* OCULTAR BOTONES ***************************/
    function ocultarBotones(option) {
        switch (option) {
            case '0':
                $("#segtoVenta").prop("hidden", false);
                $("#inicioVenta").prop("hidden", true);

                break;

        }
    }
    /********************* REQUERIR FACTURAR ***************************/
    function requerirFactura(select) {
        data = $("#idTipoVenta option:selected").data("tipo");
        $("#fact").val(data);
        switch (data) {
            case 1:
                $("#div-factura").attr("hidden", false);
                $("#numFactura").prop("required", true);

                break;

            case 2:
                $("#div-factura").attr("hidden", true);
                $("#numFactura").removeAttr("required");


                break;
        }
    }
    /*************** ALMACENAMIENTO DE DATOS GENERALES DE VENTAS*********************/
    $("#formInitVenta").submit(function(e) {
        e.preventDefault();
        formData = $(this).serialize();
        $.ajax({
            url: '../Controller/ventas.php?op=initventa',
            data: formData,
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                    setTimeout(() => {
                        bloqueoBtn("bloqueo-btn-1", 2)
                        $('#formInitVenta').find('input, textarea, button, select').attr('disabled', 'disabled');
                        ocultarBotones('0')
                        updateRegistro()
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
    /*************** ELIMINAR PRE REGISTRO DE VENTA *********************/
    function eliminarVenta() {
        $.ajax({
            url: '../Controller/ventas.php?op=eliminarventa',
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    location.reload();
                } else if (resp[0] == 0) {
                    bloqueoBtn("bloqueo-btn-fin", 2)

                    notificaBad(resp[1])
                }
            },
            beforeSend: function() {
                bloqueoBtn("bloqueo-btn-fin", 1)

            }
        });

    }
    /*********************** FINALIZAR VENTA & DESCUENTO DE ALMACEN **********************/
    function finalizarVenta() {
        $.ajax({
            url: '../Controller/ventas.php?op=finalizarventa',
            type: 'POST',
            success: function(json) {
                isJson = false
                if (esJson(json)) {
                    json_act = JSON.parse(json);
                    isJson = true
                } else {
                    resp = json.split('|')
                }
                console.log(json)

                if (isJson) {
                    table = "";
                    json_act.lotes.forEach(element => {
                        table += "<td>" + element + "</td>";
                    });
                    $("#alert-error").html(
                        `<div class="alert alert-danger" role="alert">
                            <b>${json_act.message}</b>
                            <table class="table table-bordered table-sm">
                            <tr>${table}</tr>
                            </table>
                        </div>`)
                    return 0;
                } else {
                    if (resp[0] == 1) {
                        location.reload();
                    } else if (resp[0] == 0) {
                        bloqueoBtn("bloqueo-btn-fin", 2)
                        if (resp[1] != 3) {
                            notificaBad(resp[1]);
                        } else {
                            bloqueoBtn("bloqueo-btn-fin", 2);
                            Swal.fire({
                                title: "Error",
                                text: resp[2],
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }



                    } else if (resp[0] == 3) {


                        //   notificaBad(resp[1])

                    }
                }

                /*else if (resp[0] == 3) { //Error de Validacion de distribucion
                                   json = JSON.stringify(resp[1]);
                                   console.table(json)
                               }*/
            },
            beforeSend: function() {
                bloqueoBtn("bloqueo-btn-fin", 1)

            }
        });
    }
</script>

</html>