<?php
session_start();

define('INCLUDE_CHECK', 1);
require_once('../../include/connect_mvc.php');
include("../../Models/Mdl_ConexionBD.php");
include("../../Models/Mdl_Rendimiento.php");
include('../../assets/scripts/cadenas.php');

$debug = 0;
$idUser = $_SESSION['CREident'];
$nameUser = $_SESSION['CREnombreUser'];
setlocale(LC_TIME, 'es_ES.UTF-8');

?>
<link href="../assets/libs/sweetalert2/dist/sweetalert2.min.css" rel="stylesheet">

<div id="content-detallado">
    <div class="row mb-2">
        <div class="col-10">
        </div>
        <div class="col-2">
            <button class="btn btn-md btn-danger" onclick="resetearDatos()" id="btn-reset"><i class="fas fa-redo"></i> Resetear Datos</button>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <table class="table table-sm">
                <input type="hidden" name="tipoProceso" id="tipoProceso" value="">
                <tbody>
                    <tr>
                        <td class="bg-success text-dark">
                            <label for="fechaEmpaque">Fecha Empaque</label>
                        </td>
                        <td>
                            <div class="row">
                                <div class="col-lg-11 col-md-11 col-sm-11 col-xs-11">
                                    <input class="form-control Validate" type="date" onchange="guardarValor('fechaempaque', this, true);" name="fechaEmpaque" value="" id="fechaEmpaque" required></input>
                                </div>
                                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1" hidden id="success-fechaempaque">
                                    <i class="fas fa-check text-success"></i>
                                </div>

                        </td>
                    </tr>

                    <tr>
                        <td class="bg-success text-dark">
                            <label for="semanaProduccion">Semana de Producción:</label>
                        </td>
                        <td>
                            <div class="row">
                                <div class="col-lg-11 col-md-11 col-sm-11 col-xs-11">
                                    <input class="form-control Validate" type="week" value="" onchange="guardarValor('semanaproduccion', this, true)" name="semanaProduccion" id="semanaProduccion" required></input>
                                </div>
                                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1" hidden id="success-semanaproduccion">
                                    <i class="fas fa-check text-success"></i>
                                </div>
                        </td>
                    </tr>

                    <tr>
                        <td class="text-dark" style="background:#ffa0bd">
                            <label for="areaWBRecibida">Área WB en Recibo (pie<sup>2</sup>)</label>
                        </td>
                        <td>
                            <div class="row">
                                <div class="col-lg-11 col-md-11 col-sm-11 col-xs-11">
                                    <input class="form-control Validate Positivos focusCampo" value="" onchange="guardarValor('areawb', this)" type="number" step="0.001" name="areaWBRecibida" id="areaWBRecibida"></input>
                                </div>
                                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1" hidden id="success-areawb">
                                    <i class="fas fa-check text-success"></i>
                                </div>

                            </div>
                        </td>

                    </tr>

                    <tr>
                        <td class="bg-success text-dark">
                            <label for="piezasRechazadas">Hides Rechazados</label>
                        </td>
                        <td>
                            <div class="row">
                                <div class="col-lg-11 col-md-11 col-sm-11 col-xs-11">
                                    <input class="form-control focusCampo" type="number" step="1" min="0" name="piezasRechazadas" value="" onchange="guardarValor('pzasrechazadas', this)" id="piezasRechazadas"></input>
                                </div>
                                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1" hidden id="success-pzasrechazadas">
                                    <i class="fas fa-check text-success"></i>
                                </div>

                            </div>
                        </td>

                    </tr>

                    <tr id="divCausaRechazo">
                        <td colspan="2">
                            <div class="row">
                                <div class="col-lg-11 col-md-11 col-sm-11 col-xs-11">
                                    <textarea class="form-control Validate" name="comentariosrechazo" onchange="guardarValor('comentariosrechazo', this, true)" id="comentariosrechazo" cols="30" rows="1" placeholder="Captura causa del rechazo en piezas"></textarea>
                                </div>
                                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1" hidden id="success-comentariosrechazo">
                                    <i class="fas fa-check text-success"></i>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="bg-success text-dark">
                            <label for="piezasReasig">Hides Re-asignados</label>
                        </td>
                        <td>
                            <div class="row">
                                <div class="col-lg-11 col-md-11 col-sm-11 col-xs-11">
                                    <input class="form-control focusCampo" type="number" step="1" min="0" name="piezasReasig" value="" onchange="guardarValor('pzasreasig', this)" id="piezasReasig"></input>
                                </div>
                                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1" hidden id="success-pzasreasig">
                                    <i class="fas fa-check text-success"></i>
                                </div>

                            </div>
                        </td>

                    </tr>
                    <tr>
                        <td class="">
                            <label for="recorteWB">Recorte WB %</label>
                        </td>
                        <td>
                            <div class="row">
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                    <label for="pesoCorteWB">Peso Corte</label>
                                    <input type="number" class="form-control focusCampo" name="" id="pesoCorteWB" step="0.01">
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                    <label for="pesoRaspadoWB">Peso Raspado</label>
                                    <input type="number" class="form-control focusCampo" name="" id="pesoRaspadoWB" step="0.01">
                                </div>
                                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 pt-4 mt-1 item-center">
                                    <button class="btn btn-success btn-md" onclick="getPorcRecorteWB()" type="button">=</button>
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                    <label for="pesoRaspadoWB">Recorte WB %</label>
                                    <div class="input-group mb-3">
                                        <input class="form-control Validate" type="number" step="0.01" readonly name="recorteWB" value="" id="recorteWB"></input>
                                        <div class="input-group-append">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 pt-4 mt-1" hidden id="success-recortewb">
                                    <i class="fas fa-check text-success"></i>
                                </div>
                            </div>

                        </td>
                    </tr>
                    <tr>
                        <td class="text-dark" style="background:#ffa0bd">
                            <label for="recorteCrust">Recorte Crust %</label>
                        </td>
                        <td>
                            <div class="row">
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                    <label for="pesoCorteWB">Peso "Recortar"</label>
                                    <input type="number" class="form-control focusCampo" name="" id="recortar" step="0.01">
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                    <label for="pesoRaspadoWB">Peso "Pesar"</label>
                                    <input type="number" class="form-control focusCampo" name="" id="pesar" step="0.01">
                                </div>
                                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 pt-4 mt-1 item-center">
                                    <button class="btn btn-success btn-md" onclick="getPorcRecorteCrust()" type="button">=</button>
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                    <label for="pesoRaspadoWB">Recorte Crust %</label>
                                    <div class="input-group mb-3">
                                        <input class="form-control  Validate" type="number" step="0.01" readonly name="recorteCrust" value="<?= $porcRecorteCrust ?>" id="recorteCrust"></input>
                                        <div class="input-group-append">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1  pt-4 mt-1" hidden id="success-recortecrust">
                                    <i class="fas fa-check text-success"></i>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="bg-warning  text-dark">
                            <label for="humedad">Humedad</label>
                        </td>
                        <td>
                            <div class="row">
                                <div class="col-lg-11 col-md-11 col-sm-11 col-xs-11">
                                    <div class="input-group mb-3">
                                        <input class="form-control Validate focusCampo" type="number" step="0.01" name="humedad" value="<?= $humedad ?>" onchange="guardarValor('humedad', this)" id="humedad"></input>
                                        <div class="input-group-append">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1" hidden id="success-humedad">
                                    <i class="fas fa-check text-success"></i>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="bg-success text-dark">
                            <label for="areaCrust">Área Crust</label>
                        </td>
                        <td>
                            <div class="row">
                                <div class="col-lg-11 col-md-11 col-sm-11 col-xs-11">
                                    <input class="form-control Validate Positivos focusCampo" type="number" step="0.01" name="areaCrust" value="<?= $areaCrust ?>" id="areaCrust" onchange="guardarValor('areacrust', this)"></input>
                                </div>
                                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1" hidden id="success-areacrust">
                                    <i class="fas fa-check text-success"></i>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <!--- INICIO RECORTE DE ACABADO GRABADO --->
                    <!---IMPLEMENTACION DEL 24/AGOSTO/2022  BY: ANA KAREN DOMINGUEZ --->

                    <tr>
                        <td class="bg-success  text-dark">
                            <label for="recorteAcabado">Recorte Acabado Gr.</label>
                        </td>
                        <td>
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                    <div class="input-group mb-3">
                                        <input class="form-control focusCampo" type="number" step="0.01" name="" id="sumRecorteAcab"></input>
                                        <div class="input-group-append">
                                            <div id="bloqueo-btn-ra" style="display:none">
                                                <button class="btn btn-success btn-md" type="button" disabled="">
                                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                </button>

                                            </div>
                                            <div id="desbloqueo-btn-ra">
                                                <button class="btn btn-success btn-md" onclick="sumarRecorteAcab()" type="button"><b>+</b></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5">
                                    <input class="form-control  Validate" type="number" step="0.01" readonly name="recorteAcabado" value="<?= $recorteAcabado ?>" id="recorteAcabado"></input>
                                </div>
                                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1" hidden id="success-recorteacabado">
                                    <i class="fas fa-check text-success"></i>
                                </div>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td class="bg-warning  text-dark">
                            <label for="quiebre">Quiebre</label>
                        </td>
                        <td>
                            <div class="row">
                                <div class="col-lg-11 col-md-11 col-sm-11 col-xs-11">
                                    <input class="form-control Validate focusCampo" type="number" step="0.01" name="quiebre" id="quiebre" value="<?= $quiebre ?>" onchange="guardarValor('quiebre', this)"></input>
                                </div>
                                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1" hidden id="success-quiebre">
                                    <i class="fas fa-check text-success"></i>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="bg-warning  text-dark">
                            <label for="suavidad">Suavidad</label>
                        </td>
                        <td>
                            <div class="row">
                                <div class="col-lg-11 col-md-11 col-sm-11 col-xs-11">
                                    <input class="form-control Validate focusCampo" type="number" step="0.01" name="suavidad" id="suavidad" value="<?= $suavidad ?>" onchange="guardarValor('suavidad', this)"></input>
                                </div>
                                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1" hidden id="success-suavidad">
                                    <i class="fas fa-check text-success"></i>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr class="identificadoresSoloMts">
                        <td class="bg-success  text-dark">
                            <label for="areaDmTeseo">Área Dm<sup>2</sup> Final</label>
                        </td>
                        <td>
                            <div class="row">
                                <div class="col-11">
                                    <div class="input-group mb-3">
                                        <input class="form-control focusCampo" type="number" step="0.01" name="" id="areaDMFinal"></input>
                                        <div class="input-group-append">
                                            <div id="bloqueo-btn-dm" style="display:none">
                                                <button class="btn btn-success btn-md" type="button" disabled="">
                                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                </button>

                                            </div>
                                            <div id="desbloqueo-btn-dm">
                                                <button class="btn btn-success btn-md" onclick="convertAreaFinalDM()" type="button"><i class="fas fa-exchange-alt"></i></button>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </td>
                    </tr>
                    <tr class="identificadoresSoloMts">
                        <td class="bg-success  text-dark">
                            <label id="lbl-areaFinalTeseo" for="areaFinalTeseo"></label>
                        </td>
                        <td>
                            <div class="row">
                                <div class="col-lg-11 col-md-11 col-sm-11 col-xs-11">
                                    <input class="form-control  Validate Positivos" type="number" step="0.01" name="areaFinalTeseo" id="areaFinalTeseo" readonly></input>
                                </div>
                                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1" hidden id="success-areafinalteseo">
                                    <i class="fas fa-check text-success"></i>
                                </div>
                            </div>
                        </td>
                    </tr>


                    <!--- Final de solo para set's -->

                    <tr class="identificadoresSoloMts">
                        <td class="bg-success  text-dark">
                            <label id="lbl-setsEmpacados" for="setsEmpacados"></label>
                        </td>
                        <td>
                            <div class="row">
                                <div class="col-lg-11 col-md-11 col-sm-11 col-xs-11">
                                    <input class="form-control Validate Positivos" type="number" step="1" min="0" name="unidadesEmpacadas" readonly id="unidadesEmpacadas"></input>
                                </div>
                                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1" hidden id="success-setsempacados">
                                    <i class="fas fa-check text-success"></i>
                                </div>
                            </div>
                        </td>
                    </tr>



                </tbody>

            </table>
        </div>
    </div>
</div>
<script src="../assets/scripts/clearDataSinSelect.js"></script>
<script src="../assets/scripts/calculaSemanaProduccion.js"></script>
<script src="../assets/scripts/functionStorageRendimiento.js"></script>
<script src="../assets/libs/sweetalert2/dist/sweetalert2.all.min.js"></script>

<script>
    cargaData()

    function cargaData() {
        $.ajax({
            url: '../Controller/rendimiento.php?op=getloteabierto',
            type: 'POST',
            async: false,
            dataType: "json",
            success: function(respuesta) {
                if (respuesta == null) {
                    $("#content-detallado").html("");
                    $("#content-detallado").html(`<div style='height:365px;'>
                        <div class='alert alert-dark' role='alert'>
                            Para iniciar,  Registra Datos Generales del Rendimiento.
                        </div>
                    </div>`);

                } else {
                    if (respuesta.tipoProceso == '1') {
                        //Activar-Desactivar fecha de empaque
                        if (respuesta.regEmpaque == '1') {
                            $("#fechaEmpaque").prop("disabled", false)
                            $("#semanaProduccion").prop("disabled", false)

                        } else {
                            $("#fechaEmpaque").prop("disabled", true)
                            $("#semanaProduccion").prop("disabled", true)
                        }
                        $(".identificadoresSoloMts").remove();

                    }
                    if (respuesta.tipoProceso == '2') {
                        $("#lbl-setsEmpacados").html("Área M<sup>2</sup> Final")
                        $("#lbl-areaFinalTeseo").html("Área Ft<sup>2</sup> Final")
                        $("#areaFinalTeseo").val(respuesta.areaFinal == null ? '0.0' : respuesta.areaFinal)
                        $("#unidadesEmpacadas").val(respuesta.unidadesEmpacadas == null ? '0.0' : respuesta.unidadesEmpacadas)
                        $(".identificadoresSoloSet").remove();

                        if ($("#unidadesEmpacadas").val() > 0 && $("#unidadesEmpacadas").val() != '') {
                            $("#btn-finalizarYield").prop("hidden", false);
                        }
                    }
                    //Pre rellenado de datos cargados
                    $("#fechaEmpaque").val(respuesta.fechaEmpaque == null ? '' : respuesta.fechaEmpaque)
                    $("#semanaProduccion").val(respuesta.semanaProduccion == null ? '' : respuesta.yearWeek + "-W" + respuesta.semanaProduccion)

                    $("#areaWBRecibida").val(respuesta.areaWB == null ? '0.0' : respuesta.areaWB)
                    $("#piezasRechazadas").val(respuesta.piezasRechazadas == null ? '0' : respuesta.piezasRechazadas * 2)
                    $("#piezasReasig").val(respuesta.piezasRecuperadas == null ? '0' : respuesta.piezasRecuperadas * 2)
                    $("#recorteWB").val(respuesta.porcRecorteWB == null ? '0.0' : respuesta.porcRecorteWB)
                    $("#recorteCrust").val(respuesta.porcRecorteCrust == null ? '0.0' : respuesta.porcRecorteCrust)
                    $("#humedad").val(respuesta.humedad == null ? '0.0' : respuesta.humedad)
                    $("#areaCrust").val(respuesta.areaCrust == null ? '0.0' : respuesta.areaCrust)
                    $("#recorteAcabado").val(respuesta.recorteAcabado == null ? '0.0' : respuesta.recorteAcabado)
                    $("#quiebre").val(respuesta.quiebre == null ? '0.0' : respuesta.quiebre)
                    $("#suavidad").val(respuesta.suavidad == null ? '0.0' : respuesta.suavidad)
                    $("#tipoProceso").val(respuesta.tipoProceso == null ? '0' : respuesta.tipoProceso)
                    $("#comentariosrechazo").text(respuesta.comentariosRechazo == null ? '' : respuesta.comentariosRechazo)
                    //Comentarios de Rechazo
                    piezasRechazadas = respuesta.piezasRechazadas == null ? '0.0' : respuesta.piezasRechazadas;
                    if (piezasRechazadas > 0) {
                        $("#divCausaRechazo").prop("hidden", false);
                    } else {
                        $("#divCausaRechazo").prop("hidden", true);
                    }
                    if (validaCamposLlenos()) {
                        $("#btn-finalizarYield").prop("hidden", false);
                    } else {
                        $("#btn-finalizarYield").prop("hidden", true);

                    }
                }
            },


        });
    }



    function getPorcRecorteWB() {
        pesoRaspadoWB = parseFloat($("#pesoRaspadoWB").val());
        pesoCorteWB = parseFloat($("#pesoCorteWB").val());
        $("#recorteWB").val(((pesoCorteWB / pesoRaspadoWB) * 100).toFixed(2))
        guardarValor('recortewb', $("#recorteWB"))
    }

    function getPorcRecorteCrust() {
        recortar = parseFloat($("#recortar").val());
        pesar = parseFloat($("#pesar").val());
        $("#recorteCrust").val(((recortar / pesar) * 100).toFixed(2))
        guardarValor('recortecrust', $("#recorteCrust"))
    }

    function sumarRecorteAcab(sumRecorteAcab) {
        v_sumRecorteAcab = parseFloat($("#sumRecorteAcab").val())
        recorteAcabado = parseFloat($("#recorteAcabado").val())

        $.ajax({
            url: '../Controller/rendimiento.php?op=recorteacabado',
            data: {
                value: v_sumRecorteAcab
            },
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                    bloqueoBtn("bloqueo-btn-ra", 2)
                    // console.log(parseFloat(recorteAcabado) + parseFloat(v_sumRecorteAcab));
                    $("#recorteAcabado").val(parseFloat(recorteAcabado) + parseFloat(v_sumRecorteAcab))
                    $("#sumRecorteAcab").val(0)

                } else if (resp[0] == 0) {
                    notificaBad(resp[1])
                    bloqueoBtn("bloqueo-btn-ra", 2)


                }
            },
            beforeSend: function() {
                bloqueoBtn("bloqueo-btn-ra", 1)
            }

        });
    }

    function convertAreaFinalDM() {
        areaDMFinal = parseFloat($("#areaDMFinal").val())
        const param = parseFloat(9.29)
        $("#areaFinalTeseo").val(parseFloat(areaDMFinal / param).toFixed(2))
        $("#unidadesEmpacadas").val(parseFloat(areaDMFinal / 100).toFixed(2))
        guardarValor('areafinalteseo', $("#areaFinalTeseo"))
        guardarValor('setsempacados', $("#unidadesEmpacadas"))

    }

    function resetearDatos() {
        Swal.fire({
            title: '¿Estás seguro de borrar el avance?',
            text: "Los datos del lote se eliminarán permanentemente.",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: 'green',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Cancelar'

        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: '../Controller/rendimiento.php?op=resetdatoslote',
                    type: 'POST',
                    success: function(json) {
                        resp = json.split('|')
                        if (resp[0] == 1) {
                            Swal.fire(
                                'Reseteo Correcto',
                                resp[1],
                                'success'
                            )
                            cargaData()
                        } else if (resp[0] == 0) {
                            notificaBad(resp[1])


                        }
                    },
                    beforeSend: function() {
                    }

                });






            }
        })
    }
</script>