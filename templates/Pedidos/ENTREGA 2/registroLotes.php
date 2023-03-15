<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../../include/connect_mvc.php";
include('../../assets/scripts/cadenas.php');
$debug = 0;
$idUser = $_SESSION['CREident'];
$obj_pedidos = new Pedido($debug, $idUser);
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}
$obj_rendimiento = new Rendimiento($debug, $idUser);
$DataRendimientoAbierto = $obj_rendimiento->getPreRendimientoAbierto();
$DataRendimientoAbierto = Excepciones::validaConsulta($DataRendimientoAbierto);
if (!is_array($DataRendimientoAbierto)) {
    echo "<p class='text-danger'>Error, $DataRendimientoAbierto</p>";
    exit(0);
}
$id = $DataRendimientoAbierto[0]['id'];
$DataLote = $obj_rendimiento->getDetRendimientos($id);
$DataLote = Excepciones::validaConsulta($DataLote);
$obj_pedidos = new Pedido($debug, $idUser);
$DataPedidoLote = $obj_pedidos->getDetPedidoEnLote($id);

?>
<div class="row">
    <div class="col-md-12">
        <div class="card-header">
            <div class="row">
                <div class="col-md-12"> Lote Temola: <?= $DataLote[0]['loteTemola'] ?></div>
            </div>


        </div>
    </div>
</div>
<div class="border border-TWM p-2 card-header">
    <form id="formPedidoLoteo">
        <input type="hidden" name="idRendimiento" value="<?= $id ?>">
        <div class="row">
            <div class="col-md-6">
                <label for="pedido" class="form-label required">Selecciona Pedido</label>
                <select name="pedido" onchange="AreaProveedor()" id="pedido" class="form-control select2" required style="width:100%">
                    <option value="">Selecciona Factura del Pedido</option>
                    <?php
                    $DataPedidos = $obj_pedidos->getPedidosMatPDisp($id);
                    foreach ($DataPedidos as $key => $value) {
                        echo "<option data-tipo='{$DataPedidos[$key]['tipo']}' data-areaprom='{$DataPedidos[$key]['areaWBPromFact']}' 
                                      data-disponibles='{$DataPedidos[$key]['cuerosXUsar']}' data-c1s='{$DataPedidos[$key]['1s']}' 
                                      data-c2s='{$DataPedidos[$key]['2s']}' data-c3s='{$DataPedidos[$key]['3s']}'
                                      data-c4s='{$DataPedidos[$key]['4s']}'  data-c20='{$DataPedidos[$key]['_20']}'value='{$DataPedidos[$key]['id']}'>
                    {$DataPedidos[$key]['numFactura']} - {$DataPedidos[$key]['nProveedor']}  MAT PRIMA: {$DataPedidos[$key]['nMateria']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-6">
                <label for="areaProv" class="form-label required">Área Proveedor del Lote</label>
                <input type="text" readonly name="areaProv" id="areaProv" required class="form-control Money">
            </div>
        </div>
        <input type="hidden" name="tipoProceso" value="<?= $DataLote[0]['tipoMateriaPrima'] ?>">
        <div class="row mt-2">
            <div class="col-md-12">
                <table class="table table-sm">
                    <tbody>
                        <?php
                        if ($DataLote[0]['tipoMateriaPrima'] == 2) {
                        ?>
                            <tr class="space-piel">
                                <td class="bg-TWM text-white">1s</td>
                                <td>
                                    <input type="number" name="1s" id="1s" step="1" required value="0" class="form-control sumatoria_s">
                                </td>
                            </tr>
                            <tr class="space-piel">
                                <td class="bg-TWM text-white">2s</td>
                                <td>
                                    <input type="number" name="2s" id="2s" step="1" required value="0" class="form-control sumatoria_s">
                                </td>
                            </tr>
                            <tr class="space-piel">
                                <td class="bg-TWM text-white">3s</td>
                                <td>
                                    <input type="number" name="3s" id="3s" step="1" required value="0" class="form-control sumatoria_s">
                                </td>
                            </tr>
                            <tr class="space-piel">
                                <td class="bg-TWM text-white">4s</td>
                                <td>
                                    <input type="number" name="4s" id="4s" step="1" required value="0" class="form-control sumatoria_s">
                                </td>
                            </tr>
                            <tr class="space-piel">
                                <td class="bg-TWM text-white">20 (Rechazo)</td>
                                <td>
                                    <input type="number" name="20" id="20" step="1" required value="0" class="form-control sumatoria_s">
                                </td>
                            </tr>
                        <?php } ?>

                        <tr>
                            <td class="bg-TWM text-white">Total</td>
                            <td>
                                <input type="number" onchange="AreaProveedor()" name="Total" id="Total" step="1" value="0" required class="form-control">
                                <span id="aviso-valores" class="text-danger" hidden>El total sobrepasa a los <span id="cant-disponible"></span> disponibles de tu pedido.</span>
                            </td>
                        </tr>
                    </tbody>
                </table>

            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-9"></div>
            <div class="col-md-3 text-rigth">
                <div id="bloqueo-btn-3" style="display:none">
                    <button class="btn btn-TWM" type="button" disabled="">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Espere...
                    </button>

                </div>
                <div id="desbloqueo-btn-3">
                    <button type="button" onclick="clearForm('formPedidoLoteo')" class="button btn btn-danger">Cancelar</button>
                    <button type="submit" disabled id="btn-guardar-pedido" class="button btn btn-success">Guardar</button>
                </div>
            </div>

        </div>
    </form>
</div>

<div class="row mt-2">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                    <th>#</th>
                    <th>N° Factura Pedido</th>
                    <th>Materia Prima</th>
                    <th>1s</th>
                    <th>2s</th>
                    <th>3s</th>
                    <th>4s</th>
                    <th>20</th>
                    <th>Total</th>
                    <th>Área Proveedor del Lote</th>
                    <th>Acción</th>
                </thead>
                <tbody>
                    <?php
                    $coun = 0;

                    foreach ($DataPedidoLote as $key => $value) {
                        $suma_1s += $DataPedidoLote[$key]['1s'];
                        $suma_2s += $DataPedidoLote[$key]['2s'];
                        $suma_3s += $DataPedidoLote[$key]['3s'];
                        $suma_4s += $DataPedidoLote[$key]['4s'];
                        $suma_20 += $DataPedidoLote[$key]['_20'];
                        $suma_total_s += $DataPedidoLote[$key]['total_s'];
                        $suma_area += $DataPedidoLote[$key]['areaProveedorLote'];
                        $_1s = formatoMil($DataPedidoLote[$key]['1s']);
                        $_2s = formatoMil($DataPedidoLote[$key]['2s']);
                        $_3s = formatoMil($DataPedidoLote[$key]['3s']);
                        $_4s = formatoMil($DataPedidoLote[$key]['4s']);
                        $_20 = formatoMil($DataPedidoLote[$key]['_20']);
                        $total_s = formatoMil($DataPedidoLote[$key]['total_s']);
                        $areaProveedorLote = formatoMil($DataPedidoLote[$key]['areaProveedorLote']);
                        $btnEliminar = "<button type='button' class='btn btn-danger btn-xs' onclick='eliminarPedidoLote({$DataPedidoLote[$key]['id']})'><i class='fas fa-trash-alt'></i></button>";
                        $count++;
                        echo "<tr>
                       <td>{$count}</td>
                       <td>{$DataPedidoLote[$key]['numFactura']}</td>
                       <td>{$DataPedidoLote[$key]['nMateria']}</td>

                       <td>{$_1s}</td>
                       <td>{$_2s}</td>
                       <td>{$_3s}</td>
                       <td>{$_4s}</td>
                       <td>{$_20}</td>
                       <td>{$total_s}</td>
                       <td>{$areaProveedorLote}</td>
                       <td>{$btnEliminar}</td>
                       </tr>";
                    }

                    ?>
                </tbody>
                <tfoot>
                    <tr class="bg-TWM text-white">
                        <td colspan="2">Total:</td>
                        <td></td>
                        <td><?= formatoMil($suma_1s) ?></td>
                        <td><?= formatoMil($suma_2s) ?></td>
                        <td><?= formatoMil($suma_3s) ?></td>
                        <td><?= formatoMil($suma_4s) ?></td>
                        <td><?= formatoMil($suma_20) ?></td>
                        <td><?= formatoMil($suma_total_s) ?></td>
                        <td><?= $count > 0 ? formatoMil($suma_area) : formatoMil(0) ?></td>
                        <td></td>
                    </tr>
                </tfoot>

            </table>
        </div>


    </div>
</div>
<hr>
<div class="row">
    <div class="col-md-8 col-lg-8 col-xs-12 col-sm-12"></div>
    <div class="col-md-4 text-rigth">
        <div id="bloqueo-btn-2" style="display:none">
            <button class="btn btn-TWM" type="button" disabled="">
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Espere...
            </button>

        </div>
        <div id="desbloqueo-btn-2">
            <button type="button" onclick="cancelarDetallado(<?= $id ?>)" class="button btn btn-danger">Cancelar Detallado</button>
            <button type="button" onclick="finalizarDetallado(<?= $id ?>)" class="button btn btn-success">Finalizar Seguimiento</button>
        </div>
    </div>

</div>

<script src="../assets/scripts/clearData.js"></script>
<script>
    <?php
    if ($DataLote[0]['multiMateria'] == '1') {
        echo " $('#pedido').on('change', function() {
            var a = $('#pedido option:selected' ).data('tipo');
            if(a=='2'){
                $('.space-piel').prop('hidden', false);
                $('.sumatoria_s').val(0)
                $('#Total').val(0);
            }else if(a=='1'){
                $('.space-piel').prop('hidden', true);
                $('.sumatoria_s').val(0)
                $('#Total').val(0);

            }
          });";
    }
    ?>

    $(".sumatoria_s").change(function() {
        let result = 0;
        $(".sumatoria_s").each(function() {
            result = parseFloat(result) + parseFloat($(this).val());
        });
        verificaTotales(result)
        $("#Total").val(result);
    });


    /*************** ALMACENAMIENTO DEL RENDIMIENTO*********************/
    $("#formPedidoLoteo").submit(function(e) {
        e.preventDefault();
        formData = $(this).serialize();
        $.ajax({
            url: '../Controller/pedidos.php?op=guardardetpedido',
            data: formData,
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                    setTimeout(() => {
                        bloqueoBtn("bloqueo-btn-3", 2)
                        cargaDatosPedido()
                    }, 1000);


                } else if (resp[0] == 0) {
                    notificaBad(resp[1])
                    bloqueoBtn("bloqueo-btn-3", 2)


                }
            },
            beforeSend: function() {
                bloqueoBtn("bloqueo-btn-3", 1)
            }

        });
    });
    /*********************** LIMITES TOTALES ***************************/
    function limitesTotales(select = "pedido") {
        //id= $(select).prop("id")
        option = $("#" + select + " option:selected").data('disponibles')
        return option
    }
    /*********************** VERIFICA LOS LOTES ***************************/
    function verificaTotales(value) {
        limite = limitesTotales(select = "pedido");
        if (limite < value) {
            $("#aviso-valores").attr("hidden", false)
            $("#cant-disponible").text(limite)
            $("#btn-guardar-pedido").attr('disabled', true);
        } else {
            $("#aviso-valores").attr("hidden", true)
            option = $("#pedido option:selected").data('areaprom');
            //console.log(option)
            if (option != '' && option != undefined) {
                result = parseFloat(option) * parseFloat(value);
                $("#areaProv").val(result.toFixed(2));
                $("#btn-guardar-pedido").attr('disabled', false);

            } else {
                $("#areaProv").val((0).toFixed(2));

            }


        }
    }
    /****************** CALCULA EL PROMEDIO DE AREA *********************/
    function AreaProveedor() {
        value = $("#Total").val();
        limite = limitesTotales(select = "pedido");
        if (limite < value) {
            $("#aviso-valores").attr("hidden", false)
            $("#cant-disponible").text(limite)
            $("#btn-guardar-pedido").attr('disabled', true);
        } else {
            $("#aviso-valores").attr("hidden", true)
            option = $("#pedido option:selected").data('areaprom');
            //CAMBIA STOCK DE LAS CLASIFICACIONES
            $("#1s").prop("max", $("#pedido option:selected").data('c1s'));
            $("#2s").prop("max", $("#pedido option:selected").data('c2s'));
            $("#3s").prop("max", $("#pedido option:selected").data('c3s'));
            $("#4s").prop("max", $("#pedido option:selected").data('c4s'));
            $("#_20").prop("max", $("#pedido option:selected").data('c20'));
            if (option != '' && option != undefined) {
                result = parseFloat(option) * parseFloat(value);
                $("#areaProv").val(result.toFixed(2));
                $("#btn-guardar-pedido").attr('disabled', false);

            } else {
                $("#areaProv").val((0).toFixed(2));

            }
        }



    }

    /************************ELIMINAR DETALLADO DE PEDIDOS**************************/
    function eliminarPedidoLote(idDetPedido) {
        $.ajax({
            url: '../Controller/pedidos.php?op=eliminardetpedido',
            data: {
                id: idDetPedido
            },
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])

                    setTimeout(() => {
                        cargaDatosPedido()
                    }, 1000);


                } else if (resp[0] == 0) {
                    notificaBad(resp[1])


                }
            },
            beforeSend: function() {
            }

        });
    }

    /*******************FINALIZAR DETALLADO DE PEDIDO DEL LOTE*************************/
    function finalizarDetallado(idRendimiento) {
        $.ajax({
            url: '../Controller/rendimiento.php?op=actualizarpedido',
            data: {
                idRendimiento: idRendimiento
            },
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                    setTimeout(() => {
                        bloqueoBtn("bloqueo-btn-2", 2)
                        <?php
                        unset($_GET['data']);
                        ?>
                        location.href = 'gestionlotes.php';


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
    }

    /*******************CANCELAR DETALLADO DE PEDIDO DEL LOTE*************************/
    function cancelarDetallado(idRendimiento) {
        $.ajax({
            url: '../Controller/pedidos.php?op=cancelardetpedido',
            data: {
                id: idRendimiento
            },
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                    setTimeout(() => {
                        bloqueoBtn("bloqueo-btn-2", 2)
                        location.reload();
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
    }
</script>