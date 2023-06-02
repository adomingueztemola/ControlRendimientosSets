<?php
session_start();

define('INCLUDE_CHECK', 1);
require_once('../../include/connect_mvc.php');
include("../../Models/Mdl_ConexionBD.php");
include("../../Models/Mdl_Rendimiento.php");
include('../../assets/scripts/cadenas.php');
include("../../Models/Mdl_Solicitudes.php");

$debug = 0;
$idUser = $_SESSION['CREident'];
$nameUser = $_SESSION['CREnombreUser'];
setlocale(LC_TIME, 'es_ES.UTF-8');
$obj_rendimiento = new Rendimiento($debug, $idUser);
$DataRendimientoAbierto = $obj_rendimiento->getRendimientoAbierto();
$DataRendimientoAbierto = $DataRendimientoAbierto == '' ? array() : $DataRendimientoAbierto;
$_abierto = count($DataRendimientoAbierto) > 0 ? true : false;
//IDENTIFICACION DE TIPO DE PROCESO PARA EL MANEJO DE LOS DATOS
$tipoProceso = $_abierto ? $DataRendimientoAbierto[0]['tipoProceso'] : 0;
//PARAMETROS PARA CARGA DEL FORMULARIO

$areaWB = $_abierto ? $DataRendimientoAbierto[0]['areaWB'] : '0.0';
$diferenciaArea  = $_abierto ? $DataRendimientoAbierto[0]['diferenciaArea'] : '0.0';
$promedioAreaWB = $_abierto ? $DataRendimientoAbierto[0]['promedioAreaWB'] : '0.0';
$porcDifAreaWB = $_abierto ? $DataRendimientoAbierto[0]['porcDifAreaWB'] : '0.0';
$piezasRechazadas = $_abierto ? $DataRendimientoAbierto[0]['piezasRechazadas'] : '0.0';
$comentariosRechazo = $_abierto ? $DataRendimientoAbierto[0]['comentariosRechazo'] : '';
$hidden_comentarios = ($_abierto and $DataRendimientoAbierto[0]['piezasRechazadas']) > 0 ? '' : 'hidden';
$areaPzasRechazo = $_abierto ? $DataRendimientoAbierto[0]['areaPzasRechazo'] : '0.0';
$porcRecorteWB = $_abierto ? $DataRendimientoAbierto[0]['porcRecorteWB'] : '0.0';
$porcRecorteCrust = $_abierto ? $DataRendimientoAbierto[0]['porcRecorteCrust'] : '0.0';
$totalRecorte = $_abierto ? $DataRendimientoAbierto[0]['totalRecorte'] : '0.0';
$humedad = $_abierto ? $DataRendimientoAbierto[0]['humedad'] : '0.0';
$areaCrust = $_abierto ? $DataRendimientoAbierto[0]['areaCrust'] : '0.0';
$perdidaAreaWBCrust = $_abierto ? $DataRendimientoAbierto[0]['perdidaAreaWBCrust'] : '0.0';
$quiebre = $_abierto ? $DataRendimientoAbierto[0]['quiebre'] : '0.0';
$suavidad = $_abierto ? $DataRendimientoAbierto[0]['suavidad'] : '0.0';
$areaFinal = $_abierto ? $DataRendimientoAbierto[0]['areaFinal'] : '0.0';
$perdidaAreaCrustTeseo = $_abierto ? $DataRendimientoAbierto[0]['perdidaAreaCrustTeseo'] : '0.0';
$yieldInicialTeseo = $_abierto ? $DataRendimientoAbierto[0]['yieldInicialTeseo'] : '0.0';
$pzasCortadasTeseo = $_abierto ? $DataRendimientoAbierto[0]['pzasCortadasTeseo'] : '0.0';
$yieldFinalReal = $_abierto ? $DataRendimientoAbierto[0]['yieldFinalReal'] : '0.0';
$porcSetsRechazoInicial = $_abierto ? $DataRendimientoAbierto[0]['porcSetsRechazoInicial'] : '0.0';
$setsRechazados = $_abierto ? $DataRendimientoAbierto[0]['setsRechazados'] : '0.0';
$piezasRecuperadas = $_abierto ? $DataRendimientoAbierto[0]['piezasRecuperadas'] : '0.0';
$porcRecuperacion = $_abierto ? $DataRendimientoAbierto[0]['porcRecuperacion'] : '0.0';
$piezasRecuperadas = $_abierto ? $DataRendimientoAbierto[0]['piezasRecuperadas'] : '0.0';
$porcFinalRechazo = $_abierto ? $DataRendimientoAbierto[0]['porcFinalRechazo'] : '0.0';
$unidadesEmpacadas = $_abierto ? $DataRendimientoAbierto[0]['unidadesEmpacadas'] : '0.0';
$areaCrustSet = $_abierto ? $DataRendimientoAbierto[0]['areaCrustSet'] : '0.0';
$areaWBUnidad = $_abierto ? $DataRendimientoAbierto[0]['areaWBUnidad'] : '0.0';
$setsCortadosTeseo = $_abierto ? $DataRendimientoAbierto[0]['setsCortadosTeseo'] : '0.0';
$setsRecuperados = $_abierto ? $DataRendimientoAbierto[0]['setsRecuperados'] : '0.0';
$areaNeta = $_abierto ? $DataRendimientoAbierto[0]['areaNeta_Prg'] : '0.0';
$tipoProceso = $_abierto ? $DataRendimientoAbierto[0]['tipoProceso'] : '';
$tipoMateriaPrima = $_abierto ? $DataRendimientoAbierto[0]['tipoMateriaPrima'] : '';
$areaWBTerminado = $_abierto ? $DataRendimientoAbierto[0]['areaWBTerminado'] : '0.0';
$recorteAcabado = $_abierto ? $DataRendimientoAbierto[0]['recorteAcabado'] : '0.0';
$piezasReasig = $_abierto ? $DataRendimientoAbierto[0]['cuerosReasig'] : '0.0';


$semanaProduccion = $_abierto ? $DataRendimientoAbierto[0]['semanaProduccion'] : '';

$fechaEmpaque = $_abierto ? $DataRendimientoAbierto[0]['fechaEmpaque'] : '';
$ArrayFechaEmpaque = explode('-', $fechaEmpaque);
if (count($ArrayFechaEmpaque) > 0) {
    $semanaProduccion = $ArrayFechaEmpaque["0"] . "-W" . str_pad($semanaProduccion, 2, "0", STR_PAD_LEFT);
} else {
    $semanaProduccion = "";
}

// $labelSetsEmpacado = $tipoProceso == '1' ? "Piezas Totales Empacadas" : "M<sup>2</sup> Finales";
// $labelAreaFinal = $tipoProceso == '1' ? "Área Final de Teseo" : "Área Final";
// $labelPerdidaAreaCrust = $tipoProceso == '1' ? "Pérdida Área Crust a Teseo" : "Pérdida Área Crust a Terminado";
// $labelPzasRechazadas = $tipoProceso == '1' ? "Hides Rechazados" : "Hides Rechazados";
// $labelAreaXCantFinal = $tipoProceso == '1' ? "Área WB Real por Set" : "Área WB Real por M<sup>2</sup>";

if (!$_abierto) {
    echo "<div style='height:365px;'>
            <div class='alert alert-dark' role='alert'>
                Para iniciar,  Registra Datos Generales del Rendimiento.
            </div>
          </div>";
    exit(0);
}

//Validacion De Lote En Edicion
$obj_solicitudes = new Solicitud($debug, $idUser);
$DataValidaUsoDelLote = $obj_solicitudes->validaCambioDePzas($DataRendimientoAbierto[0]['id']);
$DataValidaUsoDelLote = $DataValidaUsoDelLote == '' ? array() : $DataValidaUsoDelLote;
if (!is_array($DataValidaUsoDelLote)) {
    echo "<p class='text-danger'>Error, $DataValidaUsoDelLote</p>";
    exit(0);
}
$disabledLoteUso = "";
if (count($DataValidaUsoDelLote) <= 0) {
    $disabledLoteUso = "disabled";
}
?>
<div class="">
    <table class="table table-sm">
        <input type="hidden" name="tipoProceso" id="tipoProceso" value="<?= $tipoProceso ?>">
        <tbody>
            <tr>
                <td class="bg-TWM text-white">
                    <label for="fechaEmpaque">Fecha Empaque</label>
                </td>
                <td>
                    <div class="row">
                        <div class="col-lg-11 col-md-11 col-sm-11 col-xs-11">
                            <input class="form-control Validate" type="date" onchange="guardarValor('fechaempaque', this, true);" name="fechaEmpaque" value="<?= $fechaEmpaque ?>" id="fechaEmpaque" required></input>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1" hidden id="success-fechaempaque">
                            <i class="fas fa-check text-success"></i>
                        </div>

                </td>
            </tr>

            <tr>
                <td class="bg-TWM text-white">
                    <label for="semanaProduccion">Semana de Producción:</label>
                </td>
                <td>
                    <div class="row">
                        <div class="col-lg-11 col-md-11 col-sm-11 col-xs-11">
                            <input class="form-control Validate" type="week" value="<?= $semanaProduccion ?>" onchange="guardarValor('semanaproduccion', this, true)" name="semanaProduccion" id="semanaProduccion" required></input>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1" hidden id="success-semanaproduccion">
                            <i class="fas fa-check text-success"></i>
                        </div>
                </td>
            </tr>

            <tr>
                <td class="bg-TWM text-white">
                    <label for="areaWBRecibida">Área WB en Recibo (pie<sup>2</sup>)</label>
                </td>
                <td>
                    <div class="row">
                        <div class="col-lg-11 col-md-11 col-sm-11 col-xs-11">
                            <input class="form-control Validate Positivos" value="<?= $areaWB ?>" onchange="guardarValor('areawb', this)" type="number" step="0.001" name="areaWBRecibida" id="areaWBRecibida"></input>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1" hidden id="success-areawb">
                            <i class="fas fa-check text-success"></i>
                        </div>

                    </div>
                </td>

            </tr>

            <tr>
                <td class="bg-TWM text-white">
                    <label for="piezasRechazadas">Hides Rechazados</label>
                </td>
                <td>
                    <div class="row">
                        <div class="col-lg-11 col-md-11 col-sm-11 col-xs-11">
                            <input class="form-control" type="number" step="1" min="0" name="piezasRechazadas" value="<?= $piezasRechazadas * 2 ?>" onchange="guardarValor('pzasrechazadas', this)" id="piezasRechazadas"></input>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1" hidden id="success-pzasrechazadas">
                            <i class="fas fa-check text-success"></i>
                        </div>

                    </div>
                </td>

            </tr>

            <tr id="divCausaRechazo" <?= $hidden_comentarios ?>>
                <td colspan="2">
                    <div class="row">
                        <div class="col-lg-11 col-md-11 col-sm-11 col-xs-11">
                            <textarea class="form-control Validate" name="comentariosrechazo" onchange="guardarValor('comentariosrechazo', this, true)" id="comentariosrechazo" cols="30" rows="1" placeholder="Captura causa del rechazo en piezas"><?= $comentariosRechazo ?></textarea>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1" hidden id="success-comentariosrechazo">
                            <i class="fas fa-check text-success"></i>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="bg-TWM text-white">
                    <label for="piezasReasig">Hides Re-asignados</label>
                </td>
                <td>
                    <div class="row">
                        <div class="col-lg-11 col-md-11 col-sm-11 col-xs-11">
                            <input class="form-control" type="number" step="1" min="0" name="piezasReasig" value="<?= $piezasReasig * 2 ?>" onchange="guardarValor('pzasreasig', this)" id="piezasReasig"></input>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1" hidden id="success-pzasreasig">
                            <i class="fas fa-check text-success"></i>
                        </div>

                    </div>
                </td>

            </tr>
            <tr>
                <td class="bg-TWM text-white">
                    <label for="recorteWB">Recorte WB %</label>
                </td>
                <td>
                    <div class="row">
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                            <label for="pesoCorteWB">Peso Corte</label>
                            <input type="number" class="form-control" name="" id="pesoCorteWB" step="0.01">
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                            <label for="pesoRaspadoWB">Peso Raspado</label>
                            <input type="number" class="form-control" name="" id="pesoRaspadoWB" step="0.01">
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 pt-4 mt-1 item-center">
                            <button class="btn btn-success btn-md" onclick="getPorcRecorteWB()" type="button">=</button>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                            <label for="pesoRaspadoWB">Recorte WB %</label>
                            <div class="input-group mb-3">
                                <input class="form-control Validate" type="number" step="0.01" readonly name="recorteWB" value="<?= $porcRecorteWB ?>" id="recorteWB"></input>
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
                <td class="bg-TWM text-white">
                    <label for="recorteCrust">Recorte Crust %</label>
                </td>
                <td>
                    <div class="row">
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                            <label for="pesoCorteWB">Peso "Recortar"</label>
                            <input type="number" class="form-control" name="" id="recortar" step="0.01">
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                            <label for="pesoRaspadoWB">Peso "Pesar"</label>
                            <input type="number" class="form-control" name="" id="pesar" step="0.01">
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
                <td class="bg-TWM text-white">
                    <label for="humedad">Humedad</label>
                </td>
                <td>
                    <div class="row">
                        <div class="col-lg-11 col-md-11 col-sm-11 col-xs-11">
                            <div class="input-group mb-3">
                                <input class="form-control Validate" type="number" step="0.01" name="humedad" value="<?= $humedad ?>" onchange="guardarValor('humedad', this)" id="humedad"></input>
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
                <td class="bg-TWM text-white">
                    <label for="areaCrust">Área Crust</label>
                </td>
                <td>
                    <div class="row">
                        <div class="col-lg-11 col-md-11 col-sm-11 col-xs-11">
                            <input class="form-control Validate Positivos" type="number" step="0.01" name="areaCrust" value="<?= $areaCrust ?>" id="areaCrust" onchange="guardarValor('areacrust', this)"></input>
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
                <td class="bg-TWM text-white">
                    <label for="recorteAcabado">Recorte Acabado Gr.</label>
                </td>
                <td>
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                            <div class="input-group mb-3">
                                <input class="form-control" type="number" step="0.01" name="" id="sumRecorteAcab"></input>
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


            <?php
            $hidden_area = $tipoMateriaPrima == "1" ? "" : "hidden";
            ?>

            <tr>
                <td class="bg-TWM text-white">
                    <label for="quiebre">Quiebre</label>
                </td>
                <td>
                    <div class="row">
                        <div class="col-lg-11 col-md-11 col-sm-11 col-xs-11">
                            <input class="form-control Validate" type="number" step="0.01" name="quiebre" id="quiebre" value="<?= $quiebre ?>" onchange="guardarValor('quiebre', this)"></input>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1" hidden id="success-quiebre">
                            <i class="fas fa-check text-success"></i>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="bg-TWM text-white">
                    <label for="suavidad">Suavidad</label>
                </td>
                <td>
                    <div class="row">
                        <div class="col-lg-11 col-md-11 col-sm-11 col-xs-11">
                            <input class="form-control Validate" type="number" step="0.01" name="suavidad" id="suavidad" value="<?= $suavidad ?>" onchange="guardarValor('suavidad', this)"></input>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1" hidden id="success-suavidad">
                            <i class="fas fa-check text-success"></i>
                        </div>
                    </div>
                </td>
            </tr>
            <tr class="identificadoresSoloMts">
                <td class="bg-TWM text-white">
                    <label for="areaDmTeseo">Área Dm<sup>2</sup> Final</label>
                </td>
                <td>
                    <div class="row">
                        <div class="col-11">
                            <div class="input-group mb-3">
                                <input class="form-control" type="number" step="0.01" name="" id="areaDMFinal"></input>
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
                <td class="bg-TWM text-white">
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
                <td class="bg-TWM text-white">
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
<script src="../assets/scripts/clearDataSinSelect.js"></script>
<script src="../assets/scripts/calculaSemanaProduccion.js"></script>
<script src="../assets/scripts/functionStorageRendimiento.js"></script>

<script>
    cargaData()

    function cargaData() {
        $.ajax({
            url: '../Controller/rendimiento.php?op=getloteabierto',
            type: 'POST',
            async: false,
            dataType: "json",
            success: function(respuesta) {
                console.log(respuesta);
                if (respuesta.tipoProceso == '1') {
                    //Activar-Desactivar fecha de empaque
                    if (respuesta.regEmpaque == '1') {
                        $("#fechaEmpaque").prop("disabled", false)
                        $("#semanaProduccion").prop("disabled", false)

                    } else {
                        $("#fechaEmpaque").prop("disabled", true)
                        $("#semanaProduccion").prop("disabled", true)
                    }
                    //Confirmar que los datos esten completos para el cierre

                    //Pre rellenado de datos cargados
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
                    //Comentarios de Rechazo
                    piezasRechazadas = respuesta.piezasRechazadas == null ? '0.0' : respuesta.piezasRechazadas;
                    if (piezasRechazadas > 0) {
                        $("#divCausaRechazo").prop("hidden", false);
                    } else {
                        $("#divCausaRechazo").prop("hidden", true);
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
                if (validaCamposLlenos()) {
                    $("#btn-finalizarYield").prop("hidden", false);
                } else {
                    $("#btn-finalizarYield").prop("hidden", true);

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
</script>