<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../../include/connect_mvc.php";
include('../../Models/Mdl_ConexionBD.php');
include('../../Models/Mdl_Rendimiento.php');
include('../../assets/scripts/cadenas.php');

$debug = 0;
$idUser = $_SESSION['CREident'];
$ident = !empty($_POST['ident']) ? $_POST['ident'] : '';

$obj_rendimiento = new Rendimiento($debug, $idUser);
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}
$DataRendimiento = $obj_rendimiento->getDetRendimientos($ident);
$DataRendimiento = $DataRendimiento == '' ? array() : $DataRendimiento;
if (!is_array($DataRendimiento)) {
    echo "<p class='text-danger'>Error, $DataRendimiento</p>";
    exit(0);
}
//IDENTIFICACION DE TIPO DE PROCESO PARA EL MANEJO DE LOS DATOS
$tipoProceso = $DataRendimiento[0]['tipoProceso'];
$labelPzasRechazadas = $tipoProceso == '1' ? "Piezas Rechazadas" : "M<sup>2</sup> Rechazados";
$labelAreaFinal = $tipoProceso == '1' ? "Área Final de Teseo" : "Área Final";
?>
<table class="table table-sm">
    <tbody>
        <tr>
            <td class="bg-TWM text-white">
                Área WB en Recibo (pie<sup>2</sup>)
            </td>
            <td>
                <?= formatoMil($DataRendimiento[0]['areaWB']) ?>
            </td>
            <td class="bg-TWM text-white">
                Diferencia Área (pie<sup>2</sup>)
            </td>
            <td>
                <?= formatoMil($DataRendimiento[0]['diferenciaArea']) ?>
            </td>
            <td class="bg-TWM text-white">
                Promedio Área (WB)
            </td>
            <td>
                <?= formatoMil($DataRendimiento[0]['promedioAreaWB']) ?>
            </td>
        </tr>
        <tr>
            <td class="bg-TWM text-white">
                % Dif. Area WB
            </td>
            <td>
                <?= formatoMil($DataRendimiento[0]['porcDifAreaWB']) ?>
            </td>
            <td class="bg-TWM text-white">
                Área comprada Proveedor del lote pie<sup>2</sup>
            </td>
            <td colspan="2">
                <?= $DataRendimiento[0]['areaProveedorLote'] ?>
            </td>
        </tr>
        <tr>
            <td class="bg-TWM text-white">
                <?= $labelPzasRechazadas ?>
            </td>
            <td>
                <?= formatoMil($DataRendimiento[0]['piezasRechazadas']) ?>
            </td>
            <td class="bg-TWM text-white">
                Comentarios de Rechazo
            </td>
            <td colspan="3">
                <?= $DataRendimiento[0]['comentariosRechazo'] ?>
            </td>
        </tr>
        <tr>
            <td class="bg-TWM text-white">
                Recorte WB %
            </td>
            <td>
                <?= formatoMil($DataRendimiento[0]['porcRecorteWB']) ?>%
            </td>
            <td class="bg-TWM text-white">
                Recorte Crust %
            </td>
            <td>
                <?= formatoMil($DataRendimiento[0]['porcRecorteCrust']) ?>%
            </td>
            <td class="bg-TWM text-white">
                Total Recorte %
            </td>
            <td>
                <?= formatoMil($DataRendimiento[0]['totalRecorte']) ?>%
            </td>
        </tr>
        <tr>
            <td class="bg-TWM text-white">
                Humedad
            </td>
            <td>
                <?= formatoMil($DataRendimiento[0]['humedad']) ?>%
            </td>
            <td class="bg-TWM text-white">
                Área Crust
            </td>
            <td>
                <?= formatoMil($DataRendimiento[0]['areaCrust']) ?>
            </td>
            <td class="bg-TWM text-white">
                Perdida de Área WB a Crust
            </td>
            <td>
                <?= formatoMil($DataRendimiento[0]['perdidaAreaWBCrust']) ?>
            </td>
        </tr>
        <?php
        $hidden_area = $tipoMateriaPrima == "1" ? "" : "hidden";
        ?>
        <tr>
            <td class="bg-TWM text-white">
                Quiebre
            </td>
            <td>
                <?= formatoMil($DataRendimiento[0]['quiebre']) ?>
            </td>
            <td class="bg-TWM text-white">
                Suavidad
            </td>
            <td>
                <?= formatoMil($DataRendimiento[0]['suavidad']) ?>
            </td>
            <td class="bg-TWM text-white">
                <?= $labelAreaFinal ?>
            </td>
            <td>
                <?= formatoMil($DataRendimiento[0]['areaFinal']) ?>
            </td>
        </tr>

        <!--- Area de solo para set's -->
        <tr class="identificadoresSoloSet">
            <td class="bg-TWM text-white">
                Perdida Área Crust a Teseo
            </td>
            <td>
                <?= formatoMil($DataRendimiento[0]['perdidaAreaCrustTeseo']) ?>
            </td>

            <td class="bg-TWM text-white">
                Yield Inicial Teseo
            </td>
            <td>
                <?= formatoMil($DataRendimiento[0]['yieldInicialTeseo']) ?>
            </td>

            <td class="bg-TWM text-white">
                Piezas Cortadas por Teseo
            </td>
            <td>
                <?= formatoMil($DataRendimiento[0]['pzasCortadasTeseo']) ?>
            </td>
        </tr>


        <tr class="identificadoresSoloSet">
            <td class="bg-TWM text-white">
                Sets Cortados Teseo
            </td>
            <td>
                <?= formatoMil($DataRendimiento[0]['setsCortadosTeseo']) ?>
            </td>
            <td class="bg-TWM text-white">
                Yield Final Real (WB)
            </td>
            <td>
                <?= formatoMil($DataRendimiento[0]['yieldFinalReal']) ?>%
            </td>
            <td class="bg-TWM text-white">
                Piezas Recuperadas
            </td>
            <td>
                <?= formatoMil($DataRendimiento[0]['piezasRecuperadas']) ?>
            </td>


        </tr>

        <tr class="identificadoresSoloSet">
            <td class="bg-TWM text-white">
                Sets Recuperadas
            </td>
            <td>
                <?= formatoMil($DataRendimiento[0]['setsRecuperados']) ?>
            </td>
            <td class="bg-TWM text-white">
                % Recuperación
            </td>
            <td>
                <?= formatoMil($DataRendimiento[0]['porcRecuperacion']) ?>%

            </td>
            <td class="bg-TWM text-white">
                % final de rechazo
            </td>
            <td>
                <?= formatoMil($DataRendimiento[0]['porcFinalRechazo']) ?>%
            </td>


        </tr>
        <tr class="identificadoresSoloSet">
            <td class="bg-TWM text-white">
                Sets Empacados
            </td>
            <td>
                <?= formatoMil($DataRendimiento[0]['setsEmpacados']) ?>
            </td>
            <td class="bg-TWM text-white">
                Área de Crust por Set
            </td>
            <td>
                <?= formatoMil($DataRendimiento[0]['areaCrustSet']) ?>
            </td>
            <td class="bg-TWM text-white">
                Área de WB por Set
            </td>
            <td>
                <?= formatoMil($DataRendimiento[0]['areaWBUnidad']) ?>
            </td>
        </tr>
        <tr class="identificadoresSoloSet">
            <td class="bg-TWM text-white">
                Costo de WB por Unidad (set o M2)
            </td>
            <td colspan="4">
                <?= formatoMil($DataRendimiento[0]['costoWBUnit']) ?> USD
            </td>
        </tr>

    </tbody>

</table>