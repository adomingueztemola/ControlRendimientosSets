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
$setsEmpacados = $_abierto ? $DataRendimientoAbierto[0]['setsEmpacados'] : '0.0';
$areaCrustSet = $_abierto ? $DataRendimientoAbierto[0]['areaCrustSet'] : '0.0';
$areaWBUnidad = $_abierto ? $DataRendimientoAbierto[0]['areaWBUnidad'] : '0.0';
$setsCortadosTeseo = $_abierto ? $DataRendimientoAbierto[0]['setsCortadosTeseo'] : '0.0';
$setsRecuperados = $_abierto ? $DataRendimientoAbierto[0]['setsRecuperados'] : '0.0';
$areaNeta = $_abierto ? $DataRendimientoAbierto[0]['areaNeta_Prg'] : '0.0';
$tipoProceso = $_abierto ? $DataRendimientoAbierto[0]['tipoProceso'] : '';
$tipoMateriaPrima = $_abierto ? $DataRendimientoAbierto[0]['tipoMateriaPrima'] : '';
$areaWBTerminado = $_abierto ? $DataRendimientoAbierto[0]['areaWBTerminado'] : '0.0';


$labelSetsEmpacado = $tipoProceso == '1' ? "Set's Empacados" : "M<sup>2</sup> Finales";
$labelAreaFinal = $tipoProceso == '1' ? "Área Final de Teseo" : "Área Final";
$labelPerdidaAreaCrust = $tipoProceso == '1' ? "Pérdida Área Crust a Teseo" : "Pérdida Área Crust a Terminado";
$labelPzasRechazadas= $tipoProceso == '1' ? "Piezas Rechazadas" : "M<sup>2</sup> Rechazados";
$labelAreaXCantFinal= $tipoProceso=='1'?"Área WB Real por Set":"Área WB Real por M<sup>2</sup>";

if (!$_abierto) {
    echo "<div style='height:365px;'>
            <div class='alert alert-dark' role='alert'>
                Para iniciar,  Registra Datos Generales del Rendimiento.
            </div>
          </div>";
    exit(0);
}

?>
<div class="" style="height:400px; overflow-y: scroll;">
    <table class="table table-sm">
        <input type="hidden" name="tipoProceso" id="tipoProceso" value="<?= $tipoProceso ?>">
        <tbody>
            <tr>
                <td class="bg-TWM text-white">
                    <label for="areaWBRecibida">Área WB en Recibo (pie<sup>2</sup>)</label>
                </td>
                <td>
                    <input class="form-control PerdidaWBCrust AreaWBXSet AreaWBaTerminado Validate Positivos" value="<?= $areaWB ?>" onchange="guardarValor('areawb', this)" type="number" step="0.001" name="areaWBRecibida" id="areaWBRecibida"></input>
                </td>

            </tr>
            <!--  <tr>
                <td class="bg-TWM text-white">
                    <label for="difArea">Diferencia Área (pie<sup>2</sup>)</label>
                </td>
                <td>
                    <input class="form-control Validate" value="<?= $diferenciaArea  ?>" disabled type="number" step="0.001" name="difArea" id="difArea"></input>
                </td>

            </tr>
            <tr>
                <td class="bg-TWM text-white">
                    <label for="promArea">Promedio Area (WB)</label>
                </td>
                <td>
                    <input class="form-control AreaPzasRechazo Validate" disabled value="<?= $promedioAreaWB ?>" type="number" step="0.001" name="promArea" id="promArea"></input>
                </td>

            </tr>
            <tr>
                <td class="bg-TWM text-white">
                    <label for="difArea">% Dif. Area WB</label>
                </td>
                <td>
                    <div class="input-group mb-3">
                        <input class="form-control Validate" disabled value="<?= $porcDifAreaWB ?>" type="number" step="0.001" name="difArea" id="difArea"></input>
                        <div class="input-group-append">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                </td>

            </tr>-->
            <tr>
                <td class="bg-TWM text-white">
                    <label for="piezasRechazadas"><?=$labelPzasRechazadas?></label>
                </td>
                <td>
                    <input class="form-control AreaPzasRechazo Validate Positivos" type="number" step="1" min="0" name="piezasRechazadas" value="<?= $piezasRechazadas ?>" onchange="guardarValor('pzasrechazadas', this)" id="piezasRechazadas"></input>
                </td>

            </tr>
            <tr id="divCausaRechazo" <?= $hidden_comentarios ?>>
                <td colspan="2">
                    <textarea class="form-control Validate" name="comentariosrechazo" onchange="guardarValor('comentariosrechazo', this, true)" id="comentariosrechazo" cols="30" rows="1" placeholder="Captura causa del rechazo en piezas"><?= $comentariosRechazo ?></textarea>
                </td>
            </tr>
            <!--  <tr>
                <td class="bg-TWM text-white">
                    <label for="areaPzasRechazadas">Área (pies<sup>2</sup>) de pzas rech.</label>
                </td>
                <td>
                    <input class="form-control Validate" type="number" step="0.001" name="areaPzasRechazadas" id="areaPzasRechazadas" disabled value="<?= $areaPzasRechazo ?>"></input>
                </td>
            </tr>-->
            <tr>
                <td class="bg-TWM text-white">
                    <label for="recorteWB">Recorte WB %</label>
                </td>
                <td>
                    <div class="input-group mb-3">
                        <input class="form-control TotalRecorte Validate" type="number" step="0.001" onchange="guardarValor('recortewb', this)" name="recorteWB" value="<?= $porcRecorteWB ?>" id="recorteWB"></input>
                        <div class="input-group-append">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="bg-TWM text-white">
                    <label for="recorteCrust">Recorte Crust %</label>
                </td>
                <td>
                    <div class="input-group mb-3">
                        <input class="form-control TotalRecorte Validate" type="number" step="0.001" onchange="guardarValor('recortecrust', this)" name="recorteCrust" value="<?= $porcRecorteCrust ?>" id="recorteCrust"></input>
                        <div class="input-group-append">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="bg-TWM text-white">
                    <label for="totalRecorte">Total Recorte %</label>
                </td>
                <td>
                    <div class="input-group mb-3">
                        <input class="form-control Validate" disabled type="number" step="0.001" value="<?= $totalRecorte ?>" name="totalRecorte" id="totalRecorte"></input>
                        <div class="input-group-append">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                </td>
            </tr>

            <tr>
                <td class="bg-TWM text-white">
                    <label for="humedad">Humedad</label>
                </td>
                <td>
                    <div class="input-group mb-3">
                        <input class="form-control Validate" type="number" step="0.001" name="humedad" value="<?= $humedad ?>" onchange="guardarValor('humedad', this)" id="humedad"></input>
                        <div class="input-group-append">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                </td>
            </tr>

            <tr>
                <td class="bg-TWM text-white">
                    <label for="areaCrust">Área Crust</label>
                </td>
                <td>
                    <input class="form-control PerdidaWBCrust PerdidaCrustTeseo AreaCrustXSet  Validate Positivos" type="number" step="0.001" name="areaCrust" value="<?= $areaCrust ?>" id="areaCrust" onchange="guardarValor('areacrust', this)"></input>
                </td>
            </tr>
            <?php
            $hidden_area = $tipoMateriaPrima == "1" ? "" : "hidden";
            ?>
            <tr <?= $hidden_area ?>>
                <td class="bg-TWM text-white">
                    <label for="perdidaWBCrust">Pérdida de Area WB a Crust</label>
                </td>
                <td>
                    <div class="input-group mb-3">
                        <input class="form-control Validate" disabled type="number" step="0.001" value="<?= $perdidaAreaWBCrust ?>" name="perdidaWBCrust" id="perdidaWBCrust"></input>
                        <div class="input-group-append">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="bg-TWM text-white">
                    <label for="quiebre">Quiebre</label>
                </td>
                <td>
                    <input class="form-control Validate" type="number" step="0.001" name="quiebre" id="quiebre" value="<?= $quiebre ?>" onchange="guardarValor('quiebre', this)"></input>
                </td>
            </tr>
            <tr>
                <td class="bg-TWM text-white">
                    <label for="suavidad">Suavidad</label>
                </td>
                <td>
                    <input class="form-control Validate" type="number" step="0.001" name="suavidad" id="suavidad" value="<?= $suavidad ?>" onchange="guardarValor('suavidad', this)"></input>
                </td>
            </tr>
            <tr>
                <td class="bg-TWM text-white">
                    <label for="areaFinalTeseo"><?= $labelAreaFinal ?></label>
                </td>
                <td>
                    <input class="form-control PerdidaCrustTeseo AreaWBaTerminado Validate Positivos" type="number" step="0.001" name="areaFinalTeseo" value="<?= $areaFinal ?>" id="areaFinalTeseo" onchange="guardarValor('areafinalteseo', this)"></input>
                </td>
            </tr>
            <tr>
                <td class="bg-TWM text-white">
                    <label for="perdidaCrustTeseo"><?= $labelPerdidaAreaCrust ?></label>
                </td>
                <td>
                    <div class="input-group mb-3">
                        <input class="form-control Validate" type="number" step="0.001" name="perdidaCrustTeseo" value="<?= $perdidaAreaCrustTeseo ?>" id="perdidaCrustTeseo" disabled></input>
                        <div class="input-group-append">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                </td>
            </tr>
            <!--- Area de solo para set's -->
            <tr class="identificadoresSoloSet">
                <td class="bg-TWM text-white">
                    <label for="yieldIniTeseo">Yield Inicial Teseo</label>
                </td>
                <td>
                    <div class="input-group mb-3">
                        <input class="form-control Validate" type="number" step="0.001" name="yieldIniTeseo" onchange="guardarValor('yieldinicialteseo', this)" value="<?= $yieldInicialTeseo ?>" id="yieldIniTeseo"></input>
                        <div class="input-group-append">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                </td>
            </tr>
            <tr class="identificadoresSoloSet">
                <td class="bg-TWM text-white">
                    <label for="pzasCutTeseo">Piezas Cortadas por Teseo</label>
                </td>
                <td>
                    <input class="form-control SetCutTeseo Validate Positivos" type="number" step="1" min="0" name="pzasCutTeseo" onchange="guardarValor('pzascortadasteseo', this)" value="<?= $pzasCortadasTeseo ?>" id="pzasCutTeseo"></input>
                </td>
            </tr>
            <tr class="identificadoresSoloSet">
                <td class="bg-TWM text-white">
                    <label for="setCutTeseo">Set's Cortados Teseo</label>
                </td>
                <td>
                    <input class="form-control SetsRechazados PorcRechazoIni PorcRecuperacion PorcFinRechazo Validate Positivos" type="number" step="0.001" disabled name="setCutTeseo" value="<?= $setsCortadosTeseo ?>" id="setCutTeseo"></input>
                </td>
            </tr>
            <tr class="identificadoresSoloSet">
                <td class="bg-TWM text-white">
                    <label for="yieldFinalReal">Yield Final Real (WB)</label>
                </td>
                <td>
                    <input type="hidden" id="areaNeta" value="<?= $areaNeta ?>">
                    <input class="form-control Validate" disabled type="number" step="0.001" name="yieldFinalReal" value="<?= $yieldFinalReal ?>" id="yieldFinalReal"></input>
                </td>
            </tr>
            <tr class="identificadoresSoloSet">
                <td class="bg-TWM text-white">
                    <label for="setsRechazados">Set's Rechazados</label>
                </td>
                <td>
                    <input class="form-control PorcRechazoIni PorcFinRechazo Validate Positivos" disabled type="number" step="0.001" name="setsRechazados" onchange="guardarValor('setsrechazados', this)" value="<?= $setsRechazados ?>" id="setsRechazados"></input>
                </td>
            </tr>
            <tr class="identificadoresSoloSet">
                <td class="bg-TWM text-white">
                    <label for="porcRechazoIni">% Set's rechazo inicial</label>
                </td>
                <td>
                    <div class="input-group mb-3">
                        <input class="form-control Validate" disabled type="number" step="0.001" name="porcRechazoIni" value="<?= $porcSetsRechazoInicial ?>" id="porcRechazoIni"></input>
                        <div class="input-group-append">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                </td>
            </tr>
            <tr class="identificadoresSoloSet">
                <td class="bg-TWM text-white">
                    <label for="pzasRecuperadas">Piezas Recuperadas</label>
                </td>
                <td>
                    <input class="form-control SetsRecuperados Validate Positivos" type="number" step="1" min="0" name="pzasRecuperadas" value="<?= $piezasRecuperadas ?>" onchange="guardarValor('piezasrecuperadas', this)" id="pzasRecuperadas"></input>
                </td>
            </tr>
            <tr class="identificadoresSoloSet">
                <td class="bg-TWM text-white">
                    <label for="setsRecuperados">Set's Recuperados</label>
                </td>
                <td>
                    <input class="form-control PorcRecuperacion Validate Positivos" type="number" step="0.001" disabled name="setsRecuperados" value="<?= $setsRecuperados ?>" id="setsRecuperados"></input>
                </td>
            </tr>
            <tr class="identificadoresSoloSet">
                <td class="bg-TWM text-white">
                    <label for="porcRecuperacion">Porcentaje de Recuperacion</label>
                </td>
                <td>
                    <div class="input-group mb-3">
                        <input class="form-control Validate" type="number" step="0.001" name="porcRecuperacion" value="<?= $porcRecuperacion ?>" disabled id="porcRecuperacion"></input>
                        <div class="input-group-append">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                </td>
            </tr>
            <tr class="identificadoresSoloSet">
                <td class="bg-TWM text-white">
                    <label for="porcFinRechazo">Porcentaje final de rechazo</label>
                </td>
                <td>
                    <div class="input-group mb-3">
                        <input class="form-control Validate" type="number" step="0.001" name="porcFinRechazo" value="<?= $porcFinalRechazo ?>" disabled id="porcFinRechazo"></input>
                        <div class="input-group-append">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                </td>
            </tr>
            <!--- Final de solo para set's -->

            <tr class="">
                <td class="bg-TWM text-white">
                    <label for="setsEmpacados"><?= $labelSetsEmpacado ?></label>
                </td>
                <td>
                    <input class="form-control SetsRechazados PorcRechazoIni AreaCrustXSet AreaWBXSet  Validate Positivos" type="number" step="0.001" min="0" name="setsEmpacados" value="<?= $setsEmpacados ?>" onchange="guardarValor('setsempacados', this)" id="setsEmpacados"></input>
                </td>
            </tr>

            <tr class="">
                <td class="bg-TWM text-white">
                    <label for="areaWBaTerminado">Porcentaje WB a Terminado</label>
                </td>
                <td>
                    <div class="input-group mb-3">

                        <input class="form-control" disabled type="number" step="0.001" min="0" name="areaWBaTerminado" value="<?= $areaWBTerminado ?>" id="areaWBaTerminado"></input>
                        <div class="input-group-append">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                </td>
            </tr>
            <!--- Area de solo para set's -->

            <tr class="identificadoresSoloSet">
                <td class="bg-TWM text-white">
                    <label for="areaCrustXSet">Área de Crust por Set</label>
                </td>
                <td>
                    <input class="form-control Validate Positivos" type="number" step="0.001" name="areaCrustXSet" disabled value="<?= $areaCrustSet ?>" id="areaCrustXSet"></input>
                </td>
            </tr>
            <tr class="">
                <td class="bg-TWM text-white">
                    <label for="areaWBXSet"><?=$labelAreaXCantFinal?></label>
                </td>
                <td>
                    <input class="form-control YieldFinalReal Validate Positivos" type="number" step="0.001" name="areaWBXSet" disabled value="<?= $areaWBUnidad ?>" id="areaWBXSet"></input>
                </td>
            </tr>
            <!--- Final de solo para set's -->

        </tbody>

    </table>
</div>
<script src="../assets/scripts/clearData.js"></script>
<script src="../assets/scripts/functionStorageRendimiento.js"></script>
<script>
    <?php
    if ($tipoProceso == 2) { ?>
        //  $(".identificadoresSoloSet").attr("hidden", true)
        $(".identificadoresSoloSet").remove();



    <?php
    }
    ?>
</script>