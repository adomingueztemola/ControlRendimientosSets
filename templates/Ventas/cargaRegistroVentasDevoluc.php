<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once('../../include/connect_mvc.php');
include("../../Models/Mdl_ConexionBD.php");
include("../../Models/Mdl_VentaXDevoluc.php");
include("../../Models/Mdl_TipoVenta.php");
include("../../Models/Mdl_Excepciones.php");
include('../../Models/Mdl_Programa.php');
include('../../assets/scripts/cadenas.php');


$idUser = $_SESSION['CREident'];
$nameUser = $_SESSION['CREnombreUser'];
setlocale(LC_TIME, 'es_ES.UTF-8');
$debug = 0;
$space = 1;

$obj_tipoventa = new TipoVenta($debug, $idUser);
$obj_ventaxdevol = new VentaXDevoluc($debug, $idUser);
$obj_programa = new Programa($debug, $idUser);

?>
<form id="formAddRegistro">

    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
            <label for="idDetDevolucion" class="form-label required">Detalle de Devolución</label>
            <?php
            $Data = $obj_ventaxdevol->getDevolucionDisponible();
            $Data = Excepciones::validaConsulta($Data);
            ?>
            <select name="idDetDevolucion" id="idDetDevolucion" required  onchange="calculoDeCantidad()" class="form-control select2">
                <option value="">Selecciona Detalle de Devolución</option>
                <?php
                foreach ($Data as $key => $value) {
                    $f_cantidad= round($Data[$key]['restante'],2);

                    echo "<option data-cantidad='{$f_cantidad}' value='{$Data[$key]['id']}'>RMA {$Data[$key]['rma']}: {$Data[$key]['p_nombre']}</option>";
                }
                ?>

            </select>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
            <input type="hidden" name="idTipoPrograma" id="idTipoPrograma">
            <label for="idPrograma" class="form-label required">Programa de Venta</label>
            <?php
            $Data = $obj_programa->getPrograma("p.estado='1'");
            $Data = Excepciones::validaConsulta($Data);
            ?>
            <select name="idPrograma" required id="idPrograma" onchange="cambiaTipo()" class="form-control select2">
                <option value="">Selecciona el nuevo programa</option>
                <?php
                foreach ($Data as $key => $value) {
                    echo "<option data-tipo='{$Data[$key]['tipo']}' value='{$Data[$key]['id']}'>{$Data[$key]['nombre']}</option>";
                }
                ?>

            </select>
        </div>

    </div>
    <div class="row">
        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5">
            <label for="cantidad" class="form-label required">Cantidad</label>
            <input type="number" required name="cantidad" id="cantidad" class="form-control">
        </div>

        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5">
            <label for="lote" class="form-label required">Lote</label>
            <input type="text" required name="lote" id="lote" class="form-control">
        </div>
        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 mt-4 pt-1">
            <div id='bloqueo-btnAdd' style='display:none'>
                <button class='btn btn-success btn-md' type='button' disabled=''>
                    <span class='spinner-border spinner-border-sm' role='status' aria-hidden='true'></span>
                </button>
            </div>
            <div id='desbloqueo-btnAdd'>
                <button class="btn btn-md btn-success " type="submit"><i class="fas fa-check"></i></button>
            </div>
        </div>
    </div>
</form>

<div class="row mt-2">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>RMA</th>
                        <th>Lote</th>
                        <th>Programa</th>
                        <th>Cantidad</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $Data = $obj_ventaxdevol->getDetVentaxDevoluc();
                    $Data = Excepciones::validaConsulta($Data);
                    $Data = $Data == '' ? array() : $Data;
                    $count = 0;
                    if(count($Data)<=0){
                        echo "<tr><td colspan='6' class='text-danger text-center'>Sin Registro de la venta</td></tr>";
                    }
                    foreach ($Data as $key => $value) {
                        $count++;
                        $f_cantidad = formatoMil($Data[$key]['unidades'], 2);
                        echo "<tr>
                            <td>{$count}</td>
                            <td>{$Data[$key]['rma']}</td>
                            <td>{$Data[$key]['loteTemola']}</td>
                            <td>{$Data[$key]['prg_nombre']}</td>
                            <td>{$f_cantidad}</td>
                            <td>
                                <div id='bloqueo-dlt{$Data[$key]['id']}' style='display:none'>
                                    <button class='btn btn-danger btn-xs' type='button' disabled=''>
                                        <span class='spinner-border spinner-border-sm' role='status' aria-hidden='true'></span>
                                    </button>
                                </div>
                                <div id='desbloqueo-dlt{$Data[$key]['id']}'>
                                    <button class='btn btn-danger btn-xs' title='Eliminar' onclick='eliminarDetVenta({$Data[$key]['id']})'><i class='fas fa-trash-alt'></i></button>
                                </div>
                            </td>
                       </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<hr>
<div class="row mb-2">
    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-7">
    </div>
    <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5">
        <div id="bloqueo-btn-3" style="display:none">
            <button class="btn btn-TWM" type="button" disabled="">
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Espere...
            </button>

        </div>
        <div id="desbloqueo-btn-3">
            <button type="button" onclick="eliminarVenta()" class="button btn btn-danger">Eliminar Pre-Venta</button>
            <button type="button" onclick="finalizarVenta()" class="button btn btn-success">Finalizar</button>
        </div>
    </div>
</div>
<script src="../assets/scripts/clearData.js"></script>
<script>
    /***********           Cambia Tipo de Programa                 ***********/
    function cambiaTipo() {
        tipo = $("#idPrograma option:selected").data("tipo");
        $("#idTipoPrograma").val(tipo);
    }
    /*********** Agregar Registro de detallado de devoluciones ******************/

    $("#formAddRegistro").submit(function(e) {
        e.preventDefault();
        formData = $(this).serialize();
        $.ajax({
            url: '../Controller/ventasXDevoluc.php?op=agregarunidades',
            data: formData,
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                    setTimeout(() => {
                        bloqueoBtn("bloqueo-btnAdd", 2)
                        clearForm("formAddRegistro")
                        updateRegistro()
                        $("#aviso-disponibilidad").attr("hidden", true);
                        $("#cant").attr({
                            "max": ""
                        });
                        $("#aviso-sobrepase").text("");
                    }, 1000);


                } else if (resp[0] == 0) {
                    notificaBad(resp[1])
                    bloqueoBtn("bloqueo-btnAdd", 2)


                }
            },
            beforeSend: function() {
                bloqueoBtn("bloqueo-btnAdd", 1)
            }

        });
    });

    function eliminarDetVenta(id) {
        $.ajax({
            url: '../Controller/ventasXDevoluc.php?op=eliminardetventa',
            data: {
                id: id
            },
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                    setTimeout(() => {
                        bloqueoBtn("bloqueo-dlt" + id, 2)
                        clearForm("formAddRegistro")
                        updateRegistro()
                        $("#aviso-disponibilidad").attr("hidden", true);
                        $("#cant").attr({
                            "max": ""
                        });
                        $("#aviso-sobrepase").text("");
                    }, 1000);


                } else if (resp[0] == 0) {
                    notificaBad(resp[1])
                    bloqueoBtn("bloqueo-dlt" + id, 2)


                }
            },
            beforeSend: function() {
                bloqueoBtn("bloqueo-dlt" + id, 1)
            }

        });
    }
    /*************** ELIMINAR PRE REGISTRO DE VENTA *********************/
    function eliminarVenta() {
        $.ajax({
            url: '../Controller/ventasXDevoluc.php?op=eliminarventa',
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    bloqueoBtn("bloqueo-btn-3", 2)
                    location.reload();
                } else if (resp[0] == 0) {
                    bloqueoBtn("bloqueo-btn-3", 2)

                    notificaBad(resp[1])
                }
            },
            beforeSend: function() {
                bloqueoBtn("bloqueo-btn-3", 1)

            }
        });

    }
    /*********************** FINALIZAR VENTA & DESCUENTO DE DEVOLUCION **********************/
    function finalizarVenta() {
        $.ajax({
            url: '../Controller/ventasXDevoluc.php?op=finalizarventa',
            type: 'POST',
            success: function(resp) {
                if (resp[0] == 1) {
                    bloqueoBtn("bloqueo-btn-3", 2)
                    location.reload();
                } else if (resp[0] == 0) {
                    bloqueoBtn("bloqueo-btn-3", 2)

                    notificaBad(resp[1])

                }
            },
            beforeSend: function() {
                bloqueoBtn("bloqueo-btn-3", 1)

            }
        });
    }

    // CALCULO DE PZAS PARA CALCULO
    function calculoDeCantidad() {
        cantidadPrograma = $("#idDetDevolucion option:selected").data("cantidad")
        $("#cantidad").val(cantidadPrograma)
        $("#cantidad").attr("max", cantidadPrograma)
    }
</script>