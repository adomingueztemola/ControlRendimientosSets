<?php
require_once 'seg.php';
$info = new Seguridad();
require_once('../include/connect_mvc.php');
include("../Models/Mdl_ConexionBD.php");
include("../Models/Mdl_Proveedor.php");
include("../Models/Mdl_Pedido.php");
include("../Models/Mdl_MateriaPrima.php");

session_start();

$info->Acceso();
$idUser = $_SESSION['CREident'];
$nameUser = $_SESSION['CREnombreUser'];
setlocale(LC_TIME, 'es_ES.UTF-8');
$debug = 0;
$space = 1;

$obj_proveedor = new Proveedor($debug, $idUser);
$obj_pedido = new Pedido($debug, $idUser);
$obj_materia = new MateriaPrima($debug, $idUser);
$DataAbierto = $obj_pedido->getPedidoAbiertoXUser();
$DataAbierto = $DataAbierto == '' ? array() : $DataAbierto;
$_abierto = count($DataAbierto) > 0 ? true : false;
//PARAMETROS PARA CARGA DEL FORMULARIO
$idCatProveedor = $_abierto ? $DataAbierto[0]['idCatProveedor'] : '';
$numFactura = $_abierto ? $DataAbierto[0]["numFactura"] : '';
$fechaFactura = $_abierto ? $DataAbierto[0]["fechaFactura"] : '';
/*$precioUnitFactPesos = $_abierto ? number_format($DataAbierto[0]["precioUnitFactPesos"], 2, ".", ",") : '';
$tc = $_abierto ? number_format($DataAbierto[0]["tc"], 2, ".", ",") : '20.00';
$precioUnitFactUsd = $_abierto ? number_format($DataAbierto[0]["precioUnitFactUsd"], 2, ".", ",") : '';
$totalCuerosFacturados = $_abierto ? number_format($DataAbierto[0]["totalCuerosFacturados"], 2, ".", ",") : '';
$areaProvPie2 = $_abierto ? number_format($DataAbierto[0]["areaProvPie2"], 2, ".", ",") : '';
$areaWBPromFact = $_abierto ? number_format($DataAbierto[0]["areaWBPromFact"], 2, ".", ",") : '';*/

$precioUnitFactPesos = $_abierto ? round($DataAbierto[0]["precioUnitFactPesos"],2) : '';
$tc = $_abierto ? round($DataAbierto[0]["tc"],2) : '20.00';
$precioUnitFactUsd = $_abierto ? round($DataAbierto[0]["precioUnitFactUsd"],2) : '';
$totalCuerosFacturados = $_abierto ? round($DataAbierto[0]["totalCuerosFacturados"],2) : '';
$areaProvPie2 = $_abierto ? round($DataAbierto[0]["areaProvPie2"],2) : '';
$areaWBPromFact = $_abierto ?round($DataAbierto[0]["areaWBPromFact"],2) : '';
$idCatMateriaPrima = $_abierto ? round($DataAbierto[0]["idCatMateriaPrima"],2) : '0';
$tipo = $_abierto ? round($DataAbierto[0]["tipoMatPrima"],2) : '0';

$disabled = $_abierto ? '' : 'disabled';

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
                    <div class="col-lg-12 col-md-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="card border">
                            <div class="card-body" id="">
                                <div class="row">
                                    <div class="col-md-10">
                                    </div>
                                    <div class="col-md-2 text-right">
                                        <a class="button btn btn-outline-TWM btn-sm" href="historialpedidos.php">Ir a Historial</a>
                                    </div>
                                </div>
                                <fieldset class="border p-2">
                                    <legend class="text-TWM font-medium">Datos de Factura</legend>
                                    <form id="formAddProveedor">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="form-label required" for="proveedor">Selecciona Proveedor:</label>
                                                <div class="input-group mb-3">
                                                    <select class="form-control select2" id="proveedor" name="proveedor" style="width: 90%;">
                                                        <option>Seleccionar Proveedor</option>
                                                        <?php
                                                        $DataProv = $obj_proveedor->getProveedores("p.estado='1'");
                                                        foreach ($DataProv as $key => $value) {
                                                            $selected = $idCatProveedor == $DataProv[$key]['id'] ? 'selected' : '';
                                                            echo "<option $selected value='{$DataProv[$key]['id']}'>{$DataProv[$key]['nombre']}</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                    <div class="input-group-append">
                                                        <div id="bloqueo-btn-prov" style="display:none">
                                                            <button class="btn btn-success" type="button" disabled="">
                                                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                            </button>

                                                        </div>
                                                        <div id="desbloqueo-btn-prov">
                                                            <button class="btn button btn-success" type="submit"><i class="fas fa-check"></i></button>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </form>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="form-label required" for="numFactura">Ingresa el Número de Factura: </label>
                                            <span id="bloqueo-btn-res" style="display:none">
                                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>

                                            </span>
                                            <span id="desbloqueo-btn-res">
                                                <span id="resultbusq"></span>
                                            </span>
                                            <input type="text" <?= $disabled ?> class="form-control Disabled" name="numFactura" value="<?= $numFactura ?>" id="numFactura">

                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label required" for="fechaFactura">Ingresa la Fecha de Facturación: </label>
                                            <input type="date" <?= $disabled ?> class="form-control Disabled" name="fechaFactura" id="fechaFactura" onchange="guardarFechaFact(this)" value="<?= $fechaFactura ?>">

                                        </div>
                                    </div>
                                </fieldset>
                                <?php /** OBJECT:  DESGLOSE DE MATERIA PRIMA POR PEDIDO  Script Date: 22/06/2022 **/ ?>
                                <fieldset class="border p-2 mt-3">
                                    <legend class="text-TWM font-medium">Desglose de Materia Prima</legend>
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                            <label for="materiaConcepto">Materia Prima:</label>
                                            <select name="materiaConcepto" <?= $disabled ?> id="materiaConcepto" onchange="guardarMateriaPrima(this)" class="form-control select2 Disabled" style="width:100%">
                                                <option>Selecciona Materia Prima</option>
                                                <?php
                                                $DataMateria = $obj_materia->getMaterias("mt.estado='1'");
                                                foreach ($DataMateria as $key => $value) {
                                                    $selected = $idCatMateriaPrima == $DataMateria[$key]["id"] ? 'selected' : '';
                                                    echo "<option data-tipo='{$DataMateria[$key]["mnd"]}' $selected value='{$DataMateria[$key]["id"]}'>{$DataMateria[$key]["nombre"]}</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>

                                    </div>

                                </fieldset>



                                <fieldset class="border p-2 mt-3">
                                    <legend class="text-TWM font-medium">Datos de Precio de Materia Prima</legend>
                                    <div class="alert alert-warning" role="alert">
                                        ¡Atención! El desglose de tasa de cambio del costo del pedido está relacionado con la Materia Prima. <b>
                                            Para cualquier configuración extra comunicate al departamento de sistemas.</b>
                                    </div>
                                    <!----- DIV DE COSTO USD PARA CZA---->
                                    <div class="row" id="div-costoCza" hidden>
                                        <div class="col-md-4">
                                            <label class="form-label required" for="precioPesoCza">Precio unitario Factura Proveedor (pesos): </label>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">$</span>
                                                </div>
                                                <input type="number" <?= $disabled ?>  autocomplete="off" step="0.01" min="0" class="form-control Disabled" value="<?= $precioUnitFactPesos ?>" onchange="guardarPrecioUnitPesos(this)" name="precioPeso" id="precioPesoCza">

                                            </div>


                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label required" for="tcCza">T.C.: </label>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">$</span>
                                                </div>
                                                <input type="number" <?= $disabled ?>  autocomplete="off" step="0.01" min="0"  class="form-control Disabled" name="tc" id="tcCza" value="<?= $tc ?>" onchange="guardarTC(this)">

                                            </div>

                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label required" for="precioUSDCza">Precio unitario Factura Proveedor (USD): </label>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">USD</span>
                                                </div>
                                                <input type="number" <?= $disabled ?> autocomplete="off" step="0.01" min="0"  class="form-control Disabled" name="precioUSD" value="<?= $precioUnitFactUsd ?>" onchange="guardarPrecioUnitUSD(this)" id="precioUSDCza">

                                            </div>

                                        </div>


                                    </div>
                                    <!----- DIV DE COSTO MXN PARA PIEL---->

                                    <div class="row" id="div-costoPiel" hidden>
                                        <div class="col-md-4">
                                            <label class="form-label required" for="precioUSDPiel">Precio unitario Factura Proveedor (USD): </label>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">USD</span>
                                                </div>
                                                <input type="number" <?= $disabled ?> autocomplete="off" step="0.01" min="0"  class="form-control Disabled" name="precioUSD" value="<?= $precioUnitFactUsd ?>" onchange="guardarPrecioUnitUSD(this)" id="precioUSDPiel">

                                            </div>

                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label required" for="tcPiel">T.C.: </label>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">$</span>
                                                </div>
                                                <input type="number" <?= $disabled ?> autocomplete="off" step="0.01" min="0"  class="form-control Disabled" name="tc" id="tcPiel" value="<?= $tc ?>" onchange="guardarTC(this)">

                                            </div>

                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label required" for="precioPesoPiel">Precio unitario Factura Proveedor (pesos): </label>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">$</span>
                                                </div>
                                                <input type="number" <?= $disabled ?> autocomplete="off" step="0.01" min="0"  class="form-control Disabled" value="<?= $precioUnitFactPesos ?>" onchange="guardarPrecioUnitPesos(this)" name="precioPeso" id="precioPesoPiel">

                                            </div>


                                        </div>

                                    </div>

                                </fieldset>
                                <fieldset class="border p-2 mt-3">
                                    <legend class="text-TWM font-medium">Datos de Área de Materia Prima</legend>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="form-label required" for="totalCueros">Total Cueros Facturados: </label>
                                            <input type="number" <?= $disabled ?> autocomplete="off" step="1" min="0"  class="form-control Disabled" name="totalCueros" id="totalCueros" value="<?= $totalCuerosFacturados ?>" onchange="guardarCueros(this)">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label required" for="areaProv">Área Proveedor Pie<sup>2</sup>: </label>
                                            <div class="input-group mb-3">

                                                <input type="number" <?= $disabled ?> autocomplete="off" step="0.01" min="0"  class="form-control Disabled" name="areaProv" id="areaProv" value="<?= $areaProvPie2 ?>" onchange="guardarAreaProv(this)">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">Pie <sup>2</sup></span>
                                                </div>
                                            </div>


                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label required" for="areaWB">Área WB Promedio Factura Proveedor: </label> : </label>
                                            <input type="number" step="0.01" min="0"  autocomplete="off" disabled class="form-control" name="areaWB" id="areaWB" value="<?= $areaWBPromFact ?>">
                                        </div>
                                    </div>
                                </fieldset>

                                <hr>
                                <div class="row">
                                    <div class="col-md-9"></div>
                                    <div class="col-md-3 text-rigth">
                                        <div id="bloqueo-btn-1" style="display:none">
                                            <button class="btn btn-TWM" type="button" disabled="">
                                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                Espere...
                                            </button>

                                        </div>
                                        <div id="desbloqueo-btn-1">
                                            <button type="button" onclick="eliminarPedido()" class="button btn btn-danger">Cancelar Pedido</button>
                                            <button type="button" onclick="finalizarPedido()" class="button btn btn-success">Guardar</button>
                                        </div>
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
<script src="../assets/libs/toastr/build/toastr.min.js"></script>
<script src="../assets/scripts/validaNumFactura.js"></script>
<script src="../assets/scripts/calculaTasaCambio.js"></script>
<script>
    <?php
    if (isset($_SESSION['CRESuccessPedido']) and $_SESSION['CRESuccessPedido'] != '') { ?>
        notificaSuc('<?= $_SESSION['CRESuccessPedido'] ?>')
    <?php
        unset($_SESSION['CRESuccessPedido']);
    }
    if (isset($_SESSION['CREErrorPedido']) and $_SESSION['CREErrorPedido'] != '') { ?>
        notificaBad('<?= $_SESSION['CREErrorPedido'] ?>')
    <?php
        unset($_SESSION['CREErrorPedido']);
    }

    if ($_abierto) { ?>
        $('#formAddProveedor').find('input, textarea, button, select').attr('disabled', 'disabled');
        $(".Disabled").attr("disabled", false)

    <?php } ?>


    habilitaTipoCambio(<?= $tipo ?>);
    /*************** ALMACENAMIENTO DEL PROVEEEDOR*********************/
    $("#formAddProveedor").submit(function(e) {
        e.preventDefault();
        formData = $(this).serialize();
        $.ajax({
            url: '../Controller/pedidos.php?op=guardarproveedor',
            data: formData,
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                    $(".Disabled").attr("disabled", false)

                    setTimeout(() => {
                        bloqueoBtn("bloqueo-btn-prov", 2)
                        $('#formAddProveedor').find('input, textarea, button, select').attr('disabled', 'disabled');
                    }, 1000);


                } else if (resp[0] == 0) {
                    notificaBad(resp[1])
                    bloqueoBtn("bloqueo-btn-prov", 2)


                }
            },
            beforeSend: function() {
                bloqueoBtn("bloqueo-btn-prov", 1)
            }

        });
    });
    /*************** ALMACENAMIENTO DEL NUMERO DE FACTURA*********************/
    function guardarNumFactura(numFactura) {

        $.ajax({
            url: '../Controller/pedidos.php?op=guardarnumfactura',
            data: {
                numFactura: numFactura
            },
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])



                } else if (resp[0] == 0) {
                    notificaBad(resp[1])
                }
            },
            beforeSend: function() {}

        });


    }
    /*************** ALMACENAMIENTO DEL FECHA DE FACTURA*********************/
    function guardarFechaFact(inp_fecha) {
        let fecha = $(inp_fecha).val()
        $.ajax({
            url: '../Controller/pedidos.php?op=guardarfechafactura',
            data: {
                fecha: fecha
            },
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])



                } else if (resp[0] == 0) {
                    notificaBad(resp[1])
                }
            },
            beforeSend: function() {}

        });

    }
    /*************** ALMACENAMIENTO DEL PRECIO UNITARIO EN PESOS DE FACTURA*********************/
    function guardarPrecioUnitPesos(inp_pesos, activaCambio = '1') {
        let preciounitpesos = $(inp_pesos).val()
        $.ajax({
            url: '../Controller/pedidos.php?op=guardarpreciounitpesos',
            data: {
                preciounitpesos: preciounitpesos
            },
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                    /************** Calculo de Tasa de Cambio******************/
                    tipo = $("#materiaConcepto option:selected").data("tipo");
                    if (tipo == "1") {
                        calculaUSDCza()
                        if (activaCambio == '1') {
                            guardarPrecioUnitUSD($("#precioUSDCza"))
                        }

                    } else {
                        calculaUSDPiel()
                        if (activaCambio == '1') {
                            guardarPrecioUnitUSD($("#precioUSDPiel"))
                        }

                    }
                } else if (resp[0] == 0) {
                    notificaBad(resp[1])
                }
            },
            beforeSend: function() {}
        });

    }
    /*************** ALMACENAMIENTO DEL T.C DE FACTURA*********************/
    function guardarTC(inp_tc) {
        let tc = $(inp_tc).val()
        $.ajax({
            url: '../Controller/pedidos.php?op=guardartc',
            data: {
                tc: tc
            },
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                    /************** Calculo de Tasa de Cambio******************/
                    tipo = $("#materiaConcepto option:selected").data("tipo");
                    if (tipo == "1") {
                        calculaUSDCza()
                        guardarPrecioUnitUSD($("#precioUSDCza"))

                    } else {
                        calculaUSDPiel()
                        guardarPrecioUnitUSD($("#precioUSDPiel"))

                    }
                } else if (resp[0] == 0) {
                    notificaBad(resp[1])
                }
            },
            beforeSend: function() {}
        });

    }
    /*************** ALMACENAMIENTO DEL PRECIO UNITARIO USD DE FACTURA*********************/
    function guardarPrecioUnitUSD(inp_usd) {
        let usd = $(inp_usd).val()
        $.ajax({
            url: '../Controller/pedidos.php?op=guardarpreciounitusd',
            data: {
                usd: usd
            },
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                    /************** Calculo de Tasa de Cambio******************/
                    tipo = $("#materiaConcepto option:selected").data("tipo");
                    if (tipo == "1") {
                        calculaUSDCza()
                        guardarPrecioUnitPesos($("#precioPesoCza"), "0")
                    } else {
                        calculaUSDPiel()
                        guardarPrecioUnitPesos($("#precioPesoPiel"), "0")
                    }
                } else if (resp[0] == 0) {
                    notificaBad(resp[1])
                }
            },
            beforeSend: function() {}
        });

    }
    /*************** ALMACENAMIENTO DE GUARDAR TOTAL DE CUEROS  DE FACTURA*********************/

    function guardarCueros(inp_cuero) {
        let cuero = $(inp_cuero).val()
        $.ajax({
            url: '../Controller/pedidos.php?op=guardarcuerofacturado',
            data: {
                cuero: cuero
            },
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    calculaAreaPromedio()
                    notificaSuc(resp[1])
                } else if (resp[0] == 0) {
                    notificaBad(resp[1])
                }
            },
            beforeSend: function() {}
        });
    }
    /*************** ALMACENAMIENTO DE GUARDAR AREA  DE FACTURA*********************/
    function guardarAreaProv(inp_area) {
        let area = $(inp_area).val()
        $.ajax({
            url: '../Controller/pedidos.php?op=guardarareaprov',
            data: {
                area: area
            },
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    calculaAreaPromedio()
                    notificaSuc(resp[1])
                } else if (resp[0] == 0) {
                    notificaBad(resp[1])
                }
            },
            beforeSend: function() {}
        });
    }
    /*************** ALMACENAMIENTO DE GUARDAR AREA WB DE FACTURA*********************/
    function guardarAreaWB(inp_WB) {
        let wb = inp_WB
        $.ajax({
            url: '../Controller/pedidos.php?op=guardarareawb',
            data: {
                wb: wb
            },
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                } else if (resp[0] == 0) {
                    notificaBad(resp[1])
                }
            },
            beforeSend: function() {}
        });
    }
    /*************** ALMACENAMIENTO DE FINALIZAR PEDIDO DE FACTURA*********************/
    function finalizarPedido() {
        $.ajax({
            url: '../Controller/pedidos.php?op=finalizarpedido',

            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    //notificaSuc(resp[1])
                    bloqueoBtn("bloqueo-btn-1", 2)

                    location.reload()
                } else if (resp[0] == 0) {
                    notificaBad(resp[1])
                    bloqueoBtn("bloqueo-btn-1", 2)

                }
            },
            beforeSend: function() {
                bloqueoBtn("bloqueo-btn-1", 1)

            }
        });
    }

    /*************** ALMACENAMIENTO DE MATERIA PRIMA DEL PEDIDO*********************/
    function guardarMateriaPrima(inp_mp) {
        let mp = $(inp_mp).val()
        $.ajax({
            url: '../Controller/pedidos.php?op=guardarmp',
            data: {
                mp: mp
            },
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                    tipo = $("#materiaConcepto option:selected").data("tipo");
                    habilitaTipoCambio(tipo);
                } else if (resp[0] == 0) {
                    notificaBad(resp[1])
                }
            },
            beforeSend: function() {}
        });
    }



    function habilitaTipoCambio(tipo) {
        switch (tipo) {
            case 1: //PESOS
                $("#div-costoCza").prop('hidden', false);
                $("#div-costoPiel").prop("hidden", true);

                break;

            case 2: //DOLARES
                $("#div-costoPiel").prop('hidden', false);
                $("#div-costoCza").prop("hidden", true);
                break;
        }

    }


    /*************** ELIMINAR PEDIDO DE FACTURA*********************/
    function eliminarPedido() {
        $.ajax({
            url: '../Controller/pedidos.php?op=eliminarpedido',

            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    //notificaSuc(resp[1])
                    bloqueoBtn("bloqueo-btn-1", 2)

                    location.reload()
                } else if (resp[0] == 0) {
                    notificaBad(resp[1])
                    bloqueoBtn("bloqueo-btn-1", 2)

                }
            },
            beforeSend: function() {
                bloqueoBtn("bloqueo-btn-1", 1)

            }
        });
    }
    /*************** CALCULA ÁREA PROMEDIO *********************/
    function calculaAreaPromedio() {
        let totalCueros = $("#totalCueros").val().replace(",", "")
        let areaProv = $("#areaProv").val().replace(",", "")
        inp_areaWB= $("#areaWB");
        if (totalCueros == 0) {
            inp_areaWB.val(0);
            result=0;    

        }else{
            result = (areaProv / totalCueros).toFixed(2)

        }
        guardarAreaWB(result)
        let fto = new Intl.NumberFormat("es-MX").format(result);

        //   $("#areaWB").val(fto);

        $("#areaWB").val(result);
    }
</script>

</html>