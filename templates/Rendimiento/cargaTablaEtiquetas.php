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
$data = (!empty($_GET['data']) and $_GET['data'] != '') ? trim($_GET['data']) : '';
$_configura = false;
if ($data == '') {
    // Carga de Rendimientos Sin Cerrar
    $DataRendimientoAbierto = $obj_rendimiento->getRendimientoAbierto("2");
    $DataRendimientoAbierto = $DataRendimientoAbierto == '' ? array() : $DataRendimientoAbierto;
    $_abierto = count($DataRendimientoAbierto) > 0 ? true : false;
} else {
    $DataRendimientoAbierto = $obj_rendimiento->getDetRendimientosEtiquetas($data);
    $DataRendimientoAbierto = $DataRendimientoAbierto == '' ? array() : $DataRendimientoAbierto;
    $_abierto = count($DataRendimientoAbierto) > 0 ? true : false;
    $_configura = count($DataRendimientoAbierto) > 0 ? true : false;
}

//PARAMETROS PARA CARGA DEL FORMULARIO
$areaWB = $_abierto ? $DataRendimientoAbierto[0]['areaWB'] : '0.0';
$promedioAreaWB = $_abierto ? $DataRendimientoAbierto[0]['promedioAreaWB'] : '0.0';
$piezasRechazadas = $_abierto ? $DataRendimientoAbierto[0]['piezasRechazadas'] : '0.0';
$hidden_comentarios = ($_abierto and $DataRendimientoAbierto[0]['piezasRechazadas']) > 0 ? '' : 'hidden';
$areaFinal = $_abierto ? $DataRendimientoAbierto[0]['areaFinal'] : '0.0';
$perdidaAreaWBTerm = $_abierto ? $DataRendimientoAbierto[0]['perdidaAreaWBTerm'] : '0.0';
$costoXft2 = $_abierto ? $DataRendimientoAbierto[0]['costoXft2'] : '0.0';
$comentariosRechazo = $_abierto ? $DataRendimientoAbierto[0]['comentariosRechazo'] : '';
$areaPzasRechazo = $_abierto ? $DataRendimientoAbierto[0]['areaPzasRechazo'] : '0.0';
$observaciones = $_abierto ? $DataRendimientoAbierto[0]['observaciones'] : '0.0';

if (!$_abierto) {
    echo "<div style='height:365px;'>
            <div class='alert alert-dark' role='alert'>
                Para iniciar,  Registra Datos Generales del Rendimiento.
            </div>
          </div>";
    exit(0);
}

?>
<div class="" style="height:350px; overflow-y: scroll;">
    <table class="table table-sm">

        <tbody>
            <tr>
                <td class="bg-TWM text-white">
                    <label for="areaWB">Área Wet Blue</label>
                </td>
                <td>
                    <input class="form-control PromedioAreaWB PerdidaAreaWBTerminada Validate Positivos" value="<?= $areaWB ?>" onchange="guardarValor('areawb', this)" type="number" step="0.001" min="0" name="areaWB" id="areaWB"></input>
                </td>

            </tr>
            <!---PROMEDIO DE AREA WB--->
            <tr>
                <td class="bg-TWM text-white">
                    <label for="promedioAreaWB">Promedio Área (WB)</label>
                </td>
                <td>
                    <input class="form-control Validate" value="<?= $promedioAreaWB ?>" disabled type="number" step="0.001" name="promedioAreaWB" id="promedioAreaWB"></input>
                </td>
            </tr>
            <tr>
                <td class="bg-TWM text-white">
                    <label for="piezasRechazadas">Piezas rechazadas</label>
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
            <!---PROMEDIO DE AREA WB--->
            <tr>
                <td class="bg-TWM text-white">
                    <label for="areaPzasRechazo">Área (pies<sup>2</sup>) de pzas rech. </label>
                </td>
                <td>
                    <input class="form-control Validate Positivos" value="<?= $areaPzasRechazo ?>" disabled type="number" step="0.001" min="0" name="areaPzasRechazo" id="areaPzasRechazo"></input>
                </td>
            </tr>

            <tr>
                <td class="bg-TWM text-white">
                    <label for="areaFinal">Área Final</label>
                </td>
                <td>
                    <input class="form-control PerdidaAreaWBTerminada Validate Positivos" type="number" step="0.001" min="0" name="areaFinal" value="<?= $areaFinal ?>" id="areaFinal" onchange="guardarValor('areafinal', this)"></input>
                </td>
            </tr>

            <tr>
                <td class="bg-TWM text-white">
                    <label for="perdidaWBTerminado">Pérdida de Area WB a Terminado</label>
                </td>
                <td>
                    <div class="input-group mb-3">
                        <input class="form-control Validate" disabled type="number" step="0.001" min="0" value="<?= $perdidaAreaWBTerm ?>" name="perdidaWBTerminado" id="perdidaWBTerminado"></input>
                        <div class="input-group-append">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                </td>
            </tr>

            <tr>
                <td class="bg-TWM text-white">
                    <label for="costoft2">Costo por Ft<sup>2</sup></label>
                </td>
                <td>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">$</span>
                        </div>
                        <input class="form-control PerdidaCrustTeseo Validate Positivos" min="0" type="number" step="0.001" name="costoft2" value="<?= $costoXft2 ?>" id="costoft2" onchange="guardarValor('costoft2', this)"></input>

                    </div>
                </td>
            </tr>

        </tbody>

    </table>
</div>
<div class="row" id="div-observaciones">
    <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
        <label for="observaciones"> Observaciones</label>
        <textarea name="observaciones" id="observaciones" cols="30" rows="5" onchange="guardarValor('observaciones', this, true)" class="form-control"><?= $observaciones ?></textarea>
    </div>
</div>

<script src="../assets/scripts/clearDataSinSelect.js"></script>
<script src="../assets/scripts/functionStorageRendimientoEtiquetas.js"></script>
<script>
    <?php
    if (!$_configura) {
        echo "edicion=0;";
    } else {
        echo "edicion={$data};";
    }

    ?>
</script>