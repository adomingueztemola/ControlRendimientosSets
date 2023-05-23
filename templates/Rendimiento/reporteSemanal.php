<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../../include/connect_mvc.php";
include('../../Models/Mdl_ConexionBD.php');
include('../../Models/Mdl_Rendimiento.php');
include('../../assets/scripts/cadenas.php');
//VISUALIZACION DE DATOS PARA DEVS
$debug = 0;
if ($debug == 1) {
    print_r($_POST);
} else {
    error_reporting(0);
}

$idUser = $_SESSION['CREident'];
$obj_rendimiento = new Rendimiento($debug, $idUser);
// FILTRADOS DE DATOS
$date_start = (!empty($_POST['date-start']) and $_POST['date-start'] != '') ? $_POST['date-start'] : "";
$date_end = (!empty($_POST['date-end']) and $_POST['date-end'] != '') ? $_POST['date-end'] : "";
//Limpieza de Semana
if ($date_start != '' and $date_end != '') {
    $ArrayStart = explode("-W", $date_start);
    $ArrayEnd = explode("-W", $date_end);
    $filtrado_semana = "YEARWEEK(r.fechaEmpaque) BETWEEN '{$ArrayStart[0]}{$ArrayStart[1]}' AND '{$ArrayEnd[0]}{$ArrayEnd[1]}'";
    $filtrado_semanaEtiq = "YEARWEEK(r.fechaFinal) BETWEEN '{$ArrayStart[0]}{$ArrayStart[1]}' AND '{$ArrayEnd[0]}{$ArrayEnd[1]}'";

    $filtrado_Etq = "1=1";
} else {
    $date = date('Y-m');
    $dateComplete = date('d-m-Y');
    $fechaInicio = $obj_rendimiento->firstWeekDay(date('W', strtotime($dateComplete)), date('Y'));
    $fechaFin = $obj_rendimiento->endWeekDay(date('W', strtotime($dateComplete)), date('Y'));

    // echo "Fecha Fin: " . $fechaFin;
    if (strtotime($fechaInicio) < strtotime($fechaFin)) {
        $dateInit = date('Y-m', strtotime($fechaInicio));
        $dateFin = date('Y-m', strtotime($fechaFin));
    }
    $filtrado_semana = "DATE_FORMAT(r.fechaEmpaque,'%Y-%m') BETWEEN '{$dateInit}' AND '{$dateFin}'";
    $filtrado_semanaEtiq = "DATE_FORMAT(r.fechaFinal,'%Y-%m') BETWEEN '{$dateInit}' AND '{$dateFin}'";
}

$DataSemana = $obj_rendimiento->getSemanasRendimiento($filtrado_semana, $filtrado_semanaEtiq, $filtrado_Etq);
//print_r($DataSemana);
/*$anioStart = $ArrayStart[0] == '' ? date('Y') : $ArrayStart[0];
$anioEnd = $ArrayEnd[0] == '' ? date('Y') : $ArrayEnd[0];*/
$anioStart = $ArrayStart[0] == '' ? date('Y', strtotime($fechaInicio)) : $ArrayStart[0];
$anioEnd = $ArrayEnd[0] == '' ? date('Y', strtotime($fechaFin)) : $ArrayEnd[0];
?>

<?php
/*echo "<br>p.years BETWEEN '{$anioStart}' AND '{$anioEnd}'";
echo "<br>";*/
$DataWB = $obj_rendimiento->getWetBlue("p.years BETWEEN '{$anioStart}' AND '{$anioEnd}'");
$debug = '0';

?>
<div class="table-responsive">
    <table id="table-reporte" class="table table-sm">
        <thead>
            <tr class="bg-TWM text-white">
                <th>Núm. Semana</th>

                <?php
                $count = 1;
                foreach ($DataSemana as $key => $value) {
                    $count++;

                    echo "<th>{$DataSemana[$key]['semanaProduccion']}</th>";
                }

                ?>
                <th>Acumulado</th>

            </tr>
        </thead>
        <tbody>
            <?php
            if (count($DataSemana) > 0) {
            ?>
                <!--Area de Wet Blue --->
                <tr>
                    <td class="text-TWM" colspan="<?= $count ?>"><a data-toggle="collapse" href=".c_WB">WET BLUE</a></td>
                    <?php
                    echo "" . rellenoTabla($text = $count - 1);
                    ?>
                    <td>
                        <span class="btn button btn-sm" data-toggle="collapse" href=".c_WB">
                            <i class="fas fa-chevron-circle-down"></i>
                        </span>
                    </td>
                </tr>

                <tr class="c_WB collapse">
                    <td>Área comprada (pie<sup>2</sup>) Utilizada</td>
                    <?php
                    $totalResult = 0;
                    foreach ($DataSemana as $key => $value) {
                        $result = getRecorrerData($DataSemana[$key]['semanaProduccion'], "areaComprada", $DataWB);
                        $result = $result == '' ? '0' : $result;
                        $totalResult += $result;

                        echo "<td>" . formatoMil($result) . "</td>";
                    }
                    ?>
                    <td><?= formatoMil($totalResult) ?></td>


                </tr>
                <tr class="c_WB collapse">
                    <td>Dif. Área comprada vs Medida</td>

                    <?php
                    $totalResult = 0;
                    foreach ($DataSemana as $key => $value) {
                        $result = getRecorrerData($DataSemana[$key]['semanaProduccion'], "difAreaComprada", $DataWB);
                        $result = $result == '' ? '0' : $result;
                        $totalResult += $result;

                        echo "<td>" . formatoMil($result) . "%</td>";
                    }
                    ?>
                    <td><?= formatoMil($totalResult) ?>%</td>

                </tr>
                <tr class="c_WB collapse">
                    <td>Recorte WB</td>
                    <?php
                    $totalResult = 0;
                    $counti = 0;
                    foreach ($DataSemana as $key => $value) {
                        $result = getRecorrerData($DataSemana[$key]['semanaProduccion'], "recorteWB", $DataWB);
                        $result = $result == '' ? '0' : $result;
                        $totalResult += $result;
                        $counti++;
                        echo "<td>" . formatoMil($result) . "%</td>";
                    }
                    ?>
                    <td><?= formatoMil($counti < 0 ? '0.00' : $totalResult / $counti) ?>%</td>

                </tr>
                <tr class="c_WB collapse">
                    <td>Recorte Crust</td>
                    <?php
                    $totalResult = 0;
                    $counti = 0;
                    foreach ($DataSemana as $key => $value) {
                        $result = getRecorrerData($DataSemana[$key]['semanaProduccion'], "recorteCrust", $DataWB);
                        $result = $result == '' ? '0' : $result;
                        $totalResult += $result;

                        echo "<td>" . formatoMil($result) . "%</td>";
                        $counti++;
                    }
                    ?>
                    <td><?= formatoMil($counti < 0 ? '0.00' : $totalResult / $counti) ?>%</td>
                </tr>
                <tr class="c_WB collapse">
                    <td>Dif. Área WB Compra VS Crust</td>
                    <?php
                    $totalResult = 0;
                    foreach ($DataSemana as $key => $value) {
                        $result = getRecorrerData($DataSemana[$key]['semanaProduccion'], "difAreaCompVsCrust", $DataWB);
                        $result = $result == '' ? '0' : $result;
                        $totalResult += $result;

                        echo "<td>" . formatoMil($result) . "%</td>";
                    }
                    ?>
                    <td><?= formatoMil($totalResult) ?>%</td>

                </tr>
                <!-- Fin de Apartado de WB-->
                <?php
                $DataSets = $obj_rendimiento->getSets("p.years BETWEEN '{$anioStart}' AND '{$anioEnd}'");

                ?>
                <!--inicio de Set's-->
                <tr>
                    <td class="text-TWM" colspan="<?= $count ?>">
                        <a data-toggle="collapse" href=".c_Sets">SETS</a>
                    </td>
                    <?php
                    echo "" . rellenoTabla($text = $count - 1);
                    ?>
                    <td>
                        <span class="btn button btn-sm" data-toggle="collapse" href=".c_Sets">
                            <i class="fas fa-chevron-circle-down"></i>
                        </span>
                    </td>

                </tr>
                <tr class="c_Sets collapse">

                    <td>Sets Cortados Teseo</td>
                    <?php
                    $totalResult = 0;
                    foreach ($DataSemana as $key => $value) {
                        $result = getRecorrerData($DataSemana[$key]['semanaProduccion'], "setsCortadosTeseo", $DataSets);
                        $result = $result == '' ? '0' : $result;
                        $totalResult += $result;

                        echo "<td>" . formatoMil($result) . "</td>";
                    }
                    ?>
                    <td><?= formatoMil($totalResult) ?></td>
                </tr>
                <tr class="c_Sets collapse table-danger">
                    <td> <i class="fas fa-exclamation-triangle text-danger"></i> Sets Recuperación Masiva</td>
                    <?php
                    $totalResult = 0;
                    foreach ($DataSemana as $key => $value) {
                        $result = getRecorrerData($DataSemana[$key]['semanaProduccion'], "setsRecuMas", $DataSets);
                        $result = $result == '' ? '0' : $result;
                        $totalResult += $result/4;
                        echo "<td>" . formatoMil($result/4,0) . "</td>";
                    }
                    ?>
                    <td><?= formatoMil($totalResult) ?></td>
                </tr>
                <tr class="c_Sets collapse">

                    <td>% Rechazo inicial</td>
                    <?php
                    $totalResult = 0;
                    $counti = 0;
                    foreach ($DataSemana as $key => $value) {
                        $result = getRecorrerData($DataSemana[$key]['semanaProduccion'], "porcRechazoInicial", $DataSets);
                        $result = $result == '' ? '0' : $result;
                        $totalResult += $result;
                        $counti++;
                        echo "<td>" . formatoMil($result) . "%</td>";
                    }
                    ?>
                    <td><?= formatoMil($counti < 0 ? '0.00' : $totalResult / $counti) ?>%</td>
                </tr>
                <tr class="c_Sets collapse bg-TWM text-white">
                    <td>Set's Rechazados Iniciales</td>
                    <?php
                    $totalResult = 0;
                    $counti = 0;
                    foreach ($DataSemana as $key => $value) {
                        $result = getRecorrerData($DataSemana[$key]['semanaProduccion'], "setsRechazados", $DataSets);
                        $result = $result == '' ? '0' : $result;
                        $totalResult += $result;
                        $counti++;

                        echo "<td>" . formatoMil($result) . "</td>";
                    }
                    ?>
                    <td><?= formatoMil($totalResult) ?></td>
                </tr>

                <tr class="c_Sets collapse">

                    <td>% Sets Recuperados</td>
                    <?php
                    $totalResult = 0;
                    foreach ($DataSemana as $key => $value) {
                        $result = getRecorrerData($DataSemana[$key]['semanaProduccion'], "porcSetsRecuperados", $DataSets);
                        $result = $result == '' ? '0' : $result;
                        $totalResult += $result;

                        echo "<td>" . formatoMil($result) . "%</td>";
                    }
                    ?>
                    <td><?= formatoMil($totalResult) ?>%</td>
                </tr>
                <tr class="c_Sets collapse">
                    <td>% Final de Rechazo</td>
                    <?php
                    $totalResult = 0;
                    $counti = 0;
                    foreach ($DataSemana as $key => $value) {
                        $result = getRecorrerData($DataSemana[$key]['semanaProduccion'], "porcFinalRechazo", $DataSets);
                        $result = $result == '' ? '0' : $result;
                        $totalResult += $result;
                        $counti++;

                        echo "<td>" . formatoMil($result) . "%</td>";
                    }
                    ?>
                    <td><?= formatoMil($counti < 0 ? '0.00' : $totalResult / $counti) ?>%</td>
                </tr>
                <tr class="c_Sets collapse bg-TWM text-white">
                    <td>Set's Rechazados Finales</td>
                    <?php
                    $totalResult = 0;
                    $counti = 0;
                    foreach ($DataSemana as $key => $value) {
                        $result = getRecorrerData($DataSemana[$key]['semanaProduccion'], "setsRechazoFinales", $DataSets);
                        $result = $result == '' ? '0' : $result;
                        $totalResult += $result;
                        $counti++;

                        echo "<td>" . formatoMil($result) . "</td>";
                    }
                    ?>
                    <td><?= formatoMil($totalResult) ?></td>
                </tr>
                <tr class="c_Sets collapse">
                    <td>Sets Empacados</td>
                    <?php
                    $totalResult = 0;
                    foreach ($DataSemana as $key => $value) {
                        $result = getRecorrerData($DataSemana[$key]['semanaProduccion'], "setsEmpacados", $DataSets);
                        $result = $result == '' ? '0' : $result;
                        $totalResult += $result;

                        echo "<td>" . formatoMil($result) . "</td>";
                    }
                    ?>
                    <td><?= formatoMil($totalResult) ?></td>
                </tr>
                <tr class="c_Sets collapse">
                    <td>Área real de Crust por set (pie<sup>2</sup>)</td>
                    <?php
                    $totalResult = 0;
                    foreach ($DataSemana as $key => $value) {
                        $result = getRecorrerData($DataSemana[$key]['semanaProduccion'], "areaRealCrustXSet", $DataSets);
                        $result = $result == '' ? '0' : $result;
                        $totalResult += $result;

                        echo "<td>" . formatoMil($result) . "</td>";
                    }
                    ?>
                    <td><?= formatoMil($totalResult) ?></td>
                </tr>

                <tr class="c_Sets collapse">
                    <td>Área de WB real por set (pie<sup>2</sup>)</td>
                    <?php
                    $totalResult = 0;
                    foreach ($DataSemana as $key => $value) {
                        $result = getRecorrerData($DataSemana[$key]['semanaProduccion'], "areaWBXSet", $DataSets);
                        $result = $result == '' ? '0' : $result;
                        $totalResult += $result;

                        echo "<td>" . formatoMil($result) . "</td>";
                    }
                    ?>
                    <td><?= formatoMil($totalResult) ?></td>
                </tr>
                <tr class="c_Sets collapse">
                    <td>Costo de WB por set</td>
                    <?php
                    $totalResult = 0;
                    $counti = 0;
                    foreach ($DataSemana as $key => $value) {
                        $result = getRecorrerData($DataSemana[$key]['semanaProduccion'], "costoWBXSet", $DataSets);
                        $result = $result == '' ? '0' : $result;
                        $totalResult += $result;
                        $counti++;

                        echo "<td>" . formatoMil($result) . " USD</td>";
                    }
                    ?>
                    <td><?= formatoMil($counti < 0 ? '0.00' : $totalResult / $counti) ?>USD</td>
                </tr>
                <tr class="c_Sets  collapse">
                    <td>Dif. Área comprada vs Medida</td>
                    <?php
                    $totalResult = 0;
                    $dif_Comprada = 0;
                    foreach ($DataSemana as $key => $value) {
                        $result = getRecorrerData($DataSemana[$key]['semanaProduccion'], "difAreaCompradaMedida", $DataSets);
                        $result = $result == '' ? '0' : $result;
                        $totalResult += $result;

                        echo "<td>" . formatoMil($result) . "%</td>";
                    }
                    ?>
                    <td><?= formatoMil($totalResult) ?>%</td>
                </tr>
                <tr class="c_Sets  collapse">
                    <td>Dif. Area WB vs Crust Piel</td>

                    <?php
                    $totalResult = 0;
                    $dif_Crust = 0;

                    foreach ($DataSemana as $key => $value) {
                        $result = getRecorrerData($DataSemana[$key]['semanaProduccion'], "difAreaCompVsCrust", $DataSets);
                        $result = $result == '' ? '0' : $result;
                        $totalResult += $result;

                        echo "<td>" . formatoMil($result) . "%</td>";
                    }

                    ?>
                    <td><?= formatoMil($totalResult) ?>%</td>
                </tr>
                <tr class="c_Sets  collapse">
                    <td>Dif. Area Crust vs Teseo Piel</td>

                    <?php
                    $totalResult = 0;
                    $dif_Crust = 0;

                    foreach ($DataSemana as $key => $value) {
                        $result = getRecorrerData($DataSemana[$key]['semanaProduccion'], "difAreaCrustTeseo", $DataSets);
                        $result = $result == '' ? '0' : $result;
                        $totalResult += $result;

                        echo "<td>" . formatoMil($result) . "%</td>";
                    }

                    ?>
                    <td><?= formatoMil($totalResult) ?>%</td>
                </tr>
                <tr class="c_Sets  collapse">
                    <td>Total Dif.Area </td>
                    <?php
                    $totalResult = 0;
                    $dif_Crust = 0;

                    foreach ($DataSemana as $key => $value) {
                        $result = getRecorrerData($DataSemana[$key]['semanaProduccion'], "totalDifArea", $DataSets);
                        $result = $result == '' ? '0' : $result;
                        $totalResult += $result;

                        echo "<td>" . formatoMil($result) . "%</td>";
                    }

                    ?>
                    <td><?= formatoMil($totalResult) ?>%</td>

                </tr>
                <!-- Fin de Set's-->
                <!-- Inicio de AUTO CZA-->
                <?php
                $DataM2Cza = $obj_rendimiento->getM2AutCza("p.years BETWEEN '{$anioStart}' AND '{$anioEnd}'");

                ?>
                <tr>
                    <td class="text-TWM" colspan="<?= $count ?>">
                        <a data-toggle="collapse" href=".c_AutoCZA">
                            M<sup>2</sup> AUTO CZA</a>

                    </td>
                    <?php
                    echo "" . rellenoTabla($text = $count - 1);
                    ?>
                    <td>
                        <span class="btn button btn-sm" data-toggle="collapse" href=".c_AutoCZA">

                            <i class="fas fa-chevron-circle-down"></i>
                        </span>
                    </td>

                </tr>
                <tr class="c_AutoCZA collapse">
                    <td>Crupones Totales</td>
                    <?php
                    $totalResult = 0;
                    $dif_Comprada = 0;
                    foreach ($DataSemana as $key => $value) {
                        $result = getRecorrerData($DataSemana[$key]['semanaProduccion'], "total_s", $DataM2Cza);
                        $result = $result == '' ? '0' : $result;
                        $totalResult += $result;

                        echo "<td>" . formatoMil($result, 0) . "</td>";
                    }
                    ?>
                    <td><?= formatoMil($totalResult, 0) ?></td>


                </tr>
                <tr class="c_AutoCZA collapse">
                    <td>Promedio de Área Producido (FT.<sup>2</sup>)</td>
                    <?php
                    $totalResult = 0;
                    foreach ($DataSemana as $key => $value) {
                        $result = getRecorrerData($DataSemana[$key]['semanaProduccion'], "promComprada", $DataM2Cza);
                        $result = $result == '' ? '0' : $result;
                        $totalResult += $result;

                        echo "<td>" . formatoMil($result) . "</td>";
                    }
                    ?>
                    <td><?= formatoMil($totalResult) ?></td>
                </tr>
                <tr class="c_AutoCZA collapse table-secondary">
                    <td>Área WB Producida (FT.<sup>2</sup>)</td>
                    <?php
                    $totalResult = 0;
                    foreach ($DataSemana as $key => $value) {
                        $result = getRecorrerData($DataSemana[$key]['semanaProduccion'], "areaWB", $DataM2Cza);
                        $result = $result == '' ? '0' : $result;
                        $totalResult += $result;

                        echo "<td>" . formatoMil($result) . "</td>";
                    }
                    ?>
                    <td><?= formatoMil($totalResult) ?></td>
                </tr>

                <tr class="c_AutoCZA collapse table-secondary">
                    <td>FT<sup>2</sup>/Unid.</td>
                    <?php
                    $totalResult = 0;
                    $dif_Comprada = 0;
                    foreach ($DataSemana as $key => $value) {
                        $total = getRecorrerData($DataSemana[$key]['semanaProduccion'], "total_s", $DataM2Cza);
                        $total = $total == '' ? '0' : $total;
                        $totalResult += $total;

                        $areaComprada = getRecorrerData($DataSemana[$key]['semanaProduccion'], "areaWB", $DataM2Cza);
                        $areaComprada = $areaComprada == '' ? '0' : $areaComprada;
                        $totalArea += is_nan($areaComprada / $total) ? '0' : $areaComprada / $total;

                        echo "<td>" . formatoMil(is_nan($areaComprada / $total) ? '0' : $areaComprada / $total, 2) . "</td>";
                    }   ?>

                    <td><?= formatoMil($totalArea, 2) ?></td>


                </tr>
                <tr class="c_AutoCZA collapse">
                    <td>Área Total Producida (FT.<sup>2</sup>)</td>
                    <?php
                    $totalResult = 0;
                    foreach ($DataSemana as $key => $value) {
                        $result = getRecorrerData($DataSemana[$key]['semanaProduccion'], "areaComprada", $DataM2Cza);
                        $result = $result == '' ? '0' : $result;
                        $totalResult += $result;

                        echo "<td>" . formatoMil($result) . "</td>";
                    }
                    ?>
                    <td><?= formatoMil($totalResult) ?></td>
                </tr>

                <tr class="c_AutoCZA collapse">
                    <td>FT<sup>2</sup>/Unid.</td>
                    <?php
                    $totalResult = 0;
                    $dif_Comprada = 0;
                    foreach ($DataSemana as $key => $value) {
                        $total = getRecorrerData($DataSemana[$key]['semanaProduccion'], "total_s", $DataM2Cza);
                        $total = $total == '' ? '0' : $total;
                        $totalResult += $total;

                        $areaComprada = getRecorrerData($DataSemana[$key]['semanaProduccion'], "areaComprada", $DataM2Cza);
                        $areaComprada = $areaComprada == '' ? '0' : $areaComprada;
                        $totalArea += is_nan($areaComprada / $total) ? '0' : $areaComprada / $total;

                        echo "<td>" . formatoMil(is_nan($areaComprada / $total) ? '0' : $areaComprada / $total, 2) . "</td>";
                    }   ?>

                    <td><?= formatoMil($totalArea, 2) ?></td>


                </tr>
                <tr class="c_AutoCZA collapse">
                    <td>Dif. Área comprada vs Medida</td>
                    <?php
                    $totalResult = 0;
                    $dif_Comprada = 0;
                    foreach ($DataSemana as $key => $value) {
                        $result = getRecorrerData($DataSemana[$key]['semanaProduccion'], "difAreaCompradaMedida", $DataM2Cza);
                        $result = $result == '' ? '0' : $result;
                        $totalResult += $result;

                        echo "<td>" . formatoMil($result) . "%</td>";
                    }
                    ?>
                    <td><?= formatoMil($totalResult) ?>%</td>
                </tr>
                <tr class="c_AutoCZA collapse">
                    <td>Dif. Area WB vs Crust Piel</td>

                    <?php
                    $totalResult = 0;
                    $dif_Crust = 0;

                    foreach ($DataSemana as $key => $value) {
                        $result = getRecorrerData($DataSemana[$key]['semanaProduccion'], "difAreaCompVsCrust", $DataM2Cza);
                        $result = $result == '' ? '0' : $result;
                        $totalResult += $result;

                        echo "<td>" . formatoMil($result) . "%</td>";
                    }

                    ?>
                    <td><?= formatoMil($totalResult) ?>%</td>
                </tr>
                <tr class="c_AutoCZA collapse">
                    <td>Dif. Area Crust vs Teseo Piel</td>

                    <?php
                    $totalResult = 0;
                    $dif_Crust = 0;

                    foreach ($DataSemana as $key => $value) {
                        $result = getRecorrerData($DataSemana[$key]['semanaProduccion'], "difAreaCrustTeseo", $DataM2Cza);
                        $result = $result == '' ? '0' : $result;
                        $totalResult += $result;

                        echo "<td>" . formatoMil($result) . "%</td>";
                    }

                    ?>
                    <td><?= formatoMil($totalResult) ?>%</td>
                </tr>
                <tr class="c_AutoCZA collapse">
                    <td>Total Dif.Area </td>
                    <?php
                    $totalResult = 0;
                    $dif_Crust = 0;

                    foreach ($DataSemana as $key => $value) {
                        $result = getRecorrerData($DataSemana[$key]['semanaProduccion'], "totalDifArea", $DataM2Cza);
                        $result = $result == '' ? '0' : $result;
                        $totalResult += $result;

                        echo "<td>" . formatoMil($result) . "%</td>";
                    }

                    ?>
                    <td><?= formatoMil($totalResult) ?>%</td>

                </tr>
                <!-- Fin de AUTO CZA-->
                <!-- Inicio de AUTO Piel-->
                <?php
                $DataM2Piel = $obj_rendimiento->getM2AutPiel("p.years BETWEEN '{$anioStart}' AND '{$anioEnd}'");

                ?>
                <tr>
                    <td class="text-TWM" colspan="<?= $count ?>">
                        <a data-toggle="collapse" href=".c_AutoCZAPiel">M<sup>2</sup> AUTO Piel </a>
                    </td>
                    <?php
                    echo "" . rellenoTabla($text = $count - 1);
                    ?>
                    <td>
                        <span class="btn button btn-sm" data-toggle="collapse" data-target=".c_AutoCZAPiel">

                            <i class="fas fa-chevron-circle-down"></i>
                        </span>
                    </td>

                </tr>
                <tr class="c_AutoCZAPiel collapse">
                    <td>Lados Totales</td>
                    <?php
                    $totalResult = 0;
                    $dif_Comprada = 0;
                    foreach ($DataSemana as $key => $value) {
                        $result = getRecorrerData($DataSemana[$key]['semanaProduccion'], "total_s", $DataM2Piel);
                        $result = $result == '' ? '0' : $result;
                        $totalResult += $result;

                        echo "<td>" . formatoMil($result * 2, 0) . "</td>";
                    }
                    ?>
                    <td><?= formatoMil($totalResult * 2, 0) ?></td>


                </tr>
                <tr class="c_AutoCZAPiel collapse">

                    <td>Promedio de Área Producido (FT.<sup>2</sup>)</td>
                    <?php
                    $totalResult = 0;
                    foreach ($DataSemana as $key => $value) {
                        $result = getRecorrerData($DataSemana[$key]['semanaProduccion'], "promComprada", $DataM2Piel);
                        $result = $result == '' ? '0' : $result;
                        $totalResult += $result;

                        echo "<td>" . formatoMil($result) . "</td>";
                    }
                    ?>
                    <td><?= formatoMil($totalResult) ?></td>
                </tr>
                <tr class="c_AutoCZAPiel collapse table-secondary">

                    <td>Área WB Producida (FT.<sup>2</sup>)</td>
                    <?php
                    $totalResult = 0;
                    foreach ($DataSemana as $key => $value) {
                        $result = getRecorrerData($DataSemana[$key]['semanaProduccion'], "areaWB", $DataM2Piel);
                        $result = $result == '' ? '0' : $result;
                        $totalResult += $result;

                        echo "<td>" . formatoMil($result) . "</td>";
                    }
                    ?>
                    <td><?= formatoMil($totalResult) ?></td>
                </tr>

                <tr class="c_AutoCZAPiel collapse table-secondary">
                    <td>FT<sup>2</sup>/Unid.</td>
                    <?php
                    $totalResult = 0;
                    $dif_Comprada = 0;
                    foreach ($DataSemana as $key => $value) {
                        $total = getRecorrerData($DataSemana[$key]['semanaProduccion'], "total_s", $DataM2Piel);
                        $total = $total == '' ? '0' : $total * 2;
                        $totalResult += $total * 2;

                        $areaComprada = getRecorrerData($DataSemana[$key]['semanaProduccion'], "areaWB", $DataM2Piel);
                        $areaComprada = $areaComprada == '' ? '0' : $areaComprada;
                        $totalArea += is_nan($areaComprada / $total) ? '0' : $areaComprada / $total;

                        echo "<td>" . formatoMil(is_nan($areaComprada / $total) ? '0' : $areaComprada / $total, 2) . "</td>";
                    }   ?>

                    <td><?= formatoMil($totalArea, 2) ?></td>


                </tr>
                <tr class="c_AutoCZAPiel collapse">

                    <td>Área Total Producida (FT.<sup>2</sup>)</td>
                    <?php
                    $totalResult = 0;
                    foreach ($DataSemana as $key => $value) {
                        $result = getRecorrerData($DataSemana[$key]['semanaProduccion'], "areaComprada", $DataM2Piel);
                        $result = $result == '' ? '0' : $result;
                        $totalResult += $result;

                        echo "<td>" . formatoMil($result) . "</td>";
                    }
                    ?>
                    <td><?= formatoMil($totalResult) ?></td>
                </tr>
                <tr class="c_AutoCZAPiel collapse">
                    <td>FT<sup>2</sup>/Unid.</td>
                    <?php
                    $totalResult = 0;
                    $dif_Comprada = 0;
                    foreach ($DataSemana as $key => $value) {
                        $total = getRecorrerData($DataSemana[$key]['semanaProduccion'], "total_s", $DataM2Piel);
                        $total = $total == '' ? '0' : $total * 2;
                        $totalResult += $total * 2;

                        $areaComprada = getRecorrerData($DataSemana[$key]['semanaProduccion'], "areaComprada", $DataM2Piel);
                        $areaComprada = $areaComprada == '' ? '0' : $areaComprada;
                        $totalArea += is_nan($areaComprada / $total) ? '0' : $areaComprada / $total;

                        echo "<td>" . formatoMil(is_nan($areaComprada / $total) ? '0' : $areaComprada / $total, 2) . "</td>";
                    }   ?>

                    <td><?= formatoMil($totalArea, 2) ?></td>


                </tr>
                <tr class="c_AutoCZAPiel collapse">
                    <td>Dif. Área comprada vs Medida</td>
                    <?php
                    $totalResult = 0;
                    $dif_Comprada = 0;
                    foreach ($DataSemana as $key => $value) {
                        $result = getRecorrerData($DataSemana[$key]['semanaProduccion'], "difAreaCompradaMedida", $DataM2Piel);
                        $result = $result == '' ? '0' : $result;
                        $totalResult += $result;

                        echo "<td>" . formatoMil($result) . "%</td>";
                    }
                    ?>
                    <td><?= formatoMil($totalResult) ?>%</td>
                </tr>
                <tr class="c_AutoCZAPiel collapse">
                    <td>Dif. Area WB vs Crust Piel</td>

                    <?php
                    $totalResult = 0;
                    $dif_Crust = 0;

                    foreach ($DataSemana as $key => $value) {
                        $result = getRecorrerData($DataSemana[$key]['semanaProduccion'], "difAreaCompVsCrust", $DataM2Piel);
                        $result = $result == '' ? '0' : $result;
                        $totalResult += $result;

                        echo "<td>" . formatoMil($result) . "%</td>";
                    }

                    ?>
                    <td><?= formatoMil($totalResult) ?>%</td>
                </tr>
                <tr class="c_AutoCZAPiel collapse">
                    <td>Dif. Área Crust vs Teseo Piel</td>
                    <?php
                    $totalResult = 0;
                    $dif_Crust = 0;

                    foreach ($DataSemana as $key => $value) {
                        $result = getRecorrerData($DataSemana[$key]['semanaProduccion'], "difAreaCrustTeseo", $DataM2Piel);
                        $result = $result == '' ? '0' : $result;
                        $totalResult += $result;

                        echo "<td>" . formatoMil($result) . "%</td>";
                    }

                    ?>
                    <td><?= formatoMil($totalResult) ?>%</td>
                </tr>
                <tr class="c_AutoCZAPiel collapse">
                    <td>Total Dif.Area </td>
                    <?php
                    $totalResult = 0;
                    $dif_Crust = 0;

                    foreach ($DataSemana as $key => $value) {
                        $result = getRecorrerData($DataSemana[$key]['semanaProduccion'], "totalDifArea", $DataM2Piel);
                        $result = $result == '' ? '0' : $result;
                        $totalResult += $result;

                        echo "<td>" . formatoMil($result) . "%</td>";
                    }

                    ?>
                    <td><?= formatoMil($totalResult) ?>%</td>

                </tr>

                <!-- Inicio de CALZADO Piel-->
                <?php
                $DataCalzado = $obj_rendimiento->getM2Calzado("YEAR(r.fechaFinal) BETWEEN '{$anioStart}' AND '{$anioEnd}'");

                ?>
                <tr>
                    <td class="text-TWM" colspan="<?= $count ?>">
                        <a data-toggle="collapse" href=".c_Calzado">M<sup>2</sup> Calzado </a>
                    </td>
                    <?php
                    echo "" . rellenoTabla($text = $count - 1);
                    ?>
                    <td>
                        <span class="btn button btn-sm" data-toggle="collapse" data-target=".c_Calzado">

                            <i class="fas fa-chevron-circle-down"></i>
                        </span>
                    </td>

                </tr>
                <tr class="c_Calzado collapse">
                    <td>Lados Totales</td>
                    <?php
                    $totalResult = 0;
                    $dif_Comprada = 0;
                    foreach ($DataSemana as $key => $value) {
                        $result = getRecorrerData($DataSemana[$key]['semanaProduccion'], "total_s", $DataCalzado);
                        $result = $result == '' ? '0' : $result;
                        $totalResult += $result;

                        echo "<td>" . formatoMil($result * 2, 0) . "</td>";
                    }
                    ?>
                    <td><?= formatoMil($totalResult * 2, 0) ?></td>


                </tr>
                <tr class="c_Calzado collapse">
                    <td>Promedio de Área Producida (FT.<sup>2</sup>)</td>
                    <?php
                    $totalResult = 0;
                    foreach ($DataSemana as $key => $value) {
                        $result = getRecorrerData($DataSemana[$key]['semanaProduccion'], "promProducido", $DataCalzado);
                        $result = $result == '' ? '0' : $result;
                        $totalResult += $result;

                        echo "<td>" . formatoMil($result) . "</td>";
                    }
                    ?>
                    <td><?= formatoMil($totalResult) ?></td>
                </tr>
                <tr class="c_Calzado collapse table-secondary">
                    <td>Área WB Producida (FT.<sup>2</sup>)</td>
                    <?php
                    $totalResult = 0;
                    foreach ($DataSemana as $key => $value) {
                        $result = getRecorrerData($DataSemana[$key]['semanaProduccion'], "totalWB", $DataCalzado);
                        $result = $result == '' ? '0' : $result;
                        $totalResult += $result;

                        echo "<td>" . formatoMil($result) . "</td>";
                    }
                    ?>
                    <td><?= formatoMil($totalResult) ?></td>
                </tr>
                <tr class="c_Calzado collapse table-secondary">
                    <td>FT<sup>2</sup>/Unid.</td>
                    <?php
                    $totalResult = 0;
                    $dif_Comprada = 0;
                    foreach ($DataSemana as $key => $value) {
                        $total = getRecorrerData($DataSemana[$key]['semanaProduccion'], "total_s", $DataCalzado);
                        $total = $total == '' ? '0' : $total * 2;
                        $totalResult += $total * 2;

                        $areaComprada = getRecorrerData($DataSemana[$key]['semanaProduccion'], "totalWB", $DataCalzado);
                        $areaComprada = $areaComprada == '' ? '0' : $areaComprada;
                        $totalArea += is_nan($areaComprada / $total) ? '0' : $areaComprada / $total;

                        echo "<td>" . formatoMil(is_nan($areaComprada / $total) ? '0' : $areaComprada / $total, 2) . "</td>";
                    }   ?>

                    <td><?= formatoMil($totalArea, 2) ?></td>


                </tr>
                <tr class="c_Calzado collapse">
                    <td>Área Total Producida (FT.<sup>2</sup>)</td>
                    <?php
                    $totalResult = 0;
                    foreach ($DataSemana as $key => $value) {
                        $result = getRecorrerData($DataSemana[$key]['semanaProduccion'], "totalProducido", $DataCalzado);
                        $result = $result == '' ? '0' : $result;
                        $totalResult += $result;

                        echo "<td>" . formatoMil($result) . "</td>";
                    }
                    ?>
                    <td><?= formatoMil($totalResult) ?></td>
                </tr>
                <tr class="c_Calzado collapse">
                    <td>FT<sup>2</sup>/Unid.</td>
                    <?php
                    $totalResult = 0;
                    $dif_Comprada = 0;
                    foreach ($DataSemana as $key => $value) {
                        $total = getRecorrerData($DataSemana[$key]['semanaProduccion'], "total_s", $DataCalzado);
                        $total = $total == '' ? '0' : $total * 2;
                        $totalResult += $total * 2;

                        $areaComprada = getRecorrerData($DataSemana[$key]['semanaProduccion'], "totalProducido", $DataCalzado);
                        $areaComprada = $areaComprada == '' ? '0' : $areaComprada;
                        $totalArea += is_nan($areaComprada / $total) ? '0' : $areaComprada / $total;

                        echo "<td>" . formatoMil(is_nan($areaComprada / $total) ? '0' : $areaComprada / $total, 2) . "</td>";
                    }   ?>

                    <td><?= formatoMil($totalArea, 2) ?></td>


                </tr>
                <tr class="c_Calzado collapse">
                    <td>Dif. Área WB vs Crust</td>
                    <?php
                    $totalResult = 0;
                    $dif_Comprada = 0;
                    foreach ($DataSemana as $key => $value) {
                        $result = getRecorrerData($DataSemana[$key]['semanaProduccion'], "difAreaWBCrust", $DataCalzado);
                        $result = $result == '' ? '0' : $result;
                        $totalResult += $result;

                        echo "<td>" . formatoMil($result) . "%</td>";
                    }
                    ?>
                    <td><?= formatoMil($totalResult) ?>%</td>
                </tr>
                <tr class="c_Calzado collapse">
                    <td>Total Dif.Area </td>
                    <?php
                    $totalResult = 0;
                    $dif_Crust = 0;

                    foreach ($DataSemana as $key => $value) {
                        $result = getRecorrerData($DataSemana[$key]['semanaProduccion'], "totalDifArea", $DataCalzado);
                        $result = $result == '' ? '0' : $result;
                        $totalResult += $result;

                        echo "<td>" . formatoMil($result) . "%</td>";
                    }

                    ?>
                    <td><?= formatoMil($totalResult) ?>%</td>

                </tr>

                <!-- Fin  de CALZADO Piel-->

                <!-- Inicio de Etiquetas Piel-->
                <?php
                $DataEtiquetas = $obj_rendimiento->getM2Etiquetas('2', "YEAR(r.fechaFinal) BETWEEN '{$anioStart}' AND '{$anioEnd}'");

                ?>
                <tr>
                    <td class="text-TWM" colspan="<?= $count ?>">
                        <a data-toggle="collapse" href=".c_EtiquetasPiel">M<sup>2</sup> Etiquetas Piel</a>
                    </td>
                    <?php
                    echo "" . rellenoTabla($text = $count - 1);
                    ?>
                    <td>
                        <span class="btn button btn-sm" data-toggle="collapse" data-target=".c_EtiquetasPiel">

                            <i class="fas fa-chevron-circle-down"></i>
                        </span>
                    </td>

                </tr>
                <tr class="c_EtiquetasPiel collapse">
                    <td>Lados Totales</td>
                    <?php
                    $totalResult = 0;
                    $dif_Comprada = 0;
                    foreach ($DataSemana as $key => $value) {
                        $result = getRecorrerData($DataSemana[$key]['semanaProduccion'], "total_s", $DataEtiquetas);
                        $result = $result == '' ? '0' : $result;
                        $totalResult += $result;

                        echo "<td>" . formatoMil($result * 2, 0) . "</td>";
                    }
                    ?>
                    <td><?= formatoMil($totalResult * 2, 0) ?></td>


                </tr>
                <tr class="c_EtiquetasPiel collapse table-secondary">
                    <td>Área WB Total (FT.<sup>2</sup>)</td>
                    <?php
                    $totalResult = 0;
                    foreach ($DataSemana as $key => $value) {
                        $result = getRecorrerData($DataSemana[$key]['semanaProduccion'], "totalWB", $DataEtiquetas);
                        $result = $result == '' ? '0' : $result;
                        $totalResult += $result;

                        echo "<td>" . formatoMil($result) . "</td>";
                    }
                    ?>
                    <td><?= formatoMil($totalResult) ?></td>
                </tr>
                <tr class="c_EtiquetasPiel collapse table-secondary">
                    <td>FT<sup>2</sup>/Unid. (Área WB)</td>
                    <?php
                    $totalResult = 0;
                    $dif_Comprada = 0;
                    foreach ($DataSemana as $key => $value) {
                        $total = getRecorrerData($DataSemana[$key]['semanaProduccion'], "total_s", $DataEtiquetas);
                        $total = $total == '' ? '0' : $total * 2;
                        $totalResult += $total * 2;

                        $areaComprada = getRecorrerData($DataSemana[$key]['semanaProduccion'], "totalWB", $DataEtiquetas);
                        $areaComprada = $areaComprada == '' ? '0' : $areaComprada;
                        $totalArea += is_nan($areaComprada / $total) ? '0' : $areaComprada / $total;

                        echo "<td>" . formatoMil(is_nan($areaComprada / $total) ? '0' : $areaComprada / $total, 2) . "</td>";
                    }   ?>

                    <td><?= formatoMil($totalArea, 2) ?></td>


                </tr>
                <tr class="c_EtiquetasPiel collapse">
                    <td>Total Producido (FT.<sup>2</sup>)</td>
                    <?php
                    $totalResult = 0;
                    foreach ($DataSemana as $key => $value) {
                        $result = getRecorrerData($DataSemana[$key]['semanaProduccion'], "totalProducido", $DataEtiquetas);
                        $result = $result == '' ? '0' : $result;
                        $totalResult += $result;

                        echo "<td>" . formatoMil($result) . "</td>";
                    }
                    ?>
                    <td><?= formatoMil($totalResult) ?></td>
                </tr>
                <tr class="c_EtiquetasPiel collapse">
                    <td>FT<sup>2</sup>/Unid.</td>
                    <?php
                    $totalResult = 0;
                    $dif_Comprada = 0;
                    foreach ($DataSemana as $key => $value) {
                        $total = getRecorrerData($DataSemana[$key]['semanaProduccion'], "total_s", $DataEtiquetas);
                        $total = $total == '' ? '0' : $total * 2;
                        $totalResult += $total * 2;

                        $areaComprada = getRecorrerData($DataSemana[$key]['semanaProduccion'], "totalProducido", $DataEtiquetas);
                        $areaComprada = $areaComprada == '' ? '0' : $areaComprada;
                        $totalArea += is_nan($areaComprada / $total) ? '0' : $areaComprada / $total;

                        echo "<td>" . formatoMil(is_nan($areaComprada / $total) ? '0' : $areaComprada / $total, 2) . "</td>";
                    }   ?>

                    <td><?= formatoMil($totalArea, 2) ?></td>


                </tr>
                <tr class="c_EtiquetasPiel collapse">
                    <td>Dif. Área WB vs Crust</td>
                    <?php
                    $totalResult = 0;
                    $dif_Comprada = 0;
                    foreach ($DataSemana as $key => $value) {
                        $result = getRecorrerData($DataSemana[$key]['semanaProduccion'], "difAreaWBCrust", $DataEtiquetas);
                        $result = $result == '' ? '0' : $result;
                        $totalResult += $result;

                        echo "<td>" . formatoMil($result) . "%</td>";
                    }
                    ?>
                    <td><?= formatoMil($totalResult) ?>%</td>
                </tr>
                <tr class="c_EtiquetasPiel collapse">
                    <td>Total Dif.Area </td>
                    <?php
                    $totalResult = 0;
                    $dif_Crust = 0;

                    foreach ($DataSemana as $key => $value) {
                        $result = getRecorrerData($DataSemana[$key]['semanaProduccion'], "totalDifArea", $DataEtiquetas);
                        $result = $result == '' ? '0' : $result;
                        $totalResult += $result;

                        echo "<td>" . formatoMil($result) . "%</td>";
                    }

                    ?>
                    <td><?= formatoMil($totalResult) ?>%</td>

                </tr>


                <!-- Fin  de Etiquetas Piel-->
                <!-- Inicio de Etiquetas Carnaza-->
                <?php
                $DataEtiquetas = $obj_rendimiento->getM2Etiquetas('1', "YEAR(r.fechaFinal) BETWEEN '{$anioStart}' AND '{$anioEnd}'");

                ?>
                <tr>
                    <td class="text-TWM" colspan="<?= $count ?>">
                        <a data-toggle="collapse" href=".c_EtiquetasCza">M<sup>2</sup> Etiquetas Carnaza</a>
                    </td>
                    <?php
                    echo "" . rellenoTabla($text = $count - 1);
                    ?>
                    <td>
                        <span class="btn button btn-sm" data-toggle="collapse" data-target=".c_EtiquetasCza">

                            <i class="fas fa-chevron-circle-down"></i>
                        </span>
                    </td>

                </tr>
                <tr class="c_EtiquetasCza collapse">
                    <td>Crupones Totales</td>
                    <?php
                    $totalResult = 0;
                    $dif_Comprada = 0;
                    foreach ($DataSemana as $key => $value) {
                        $result = getRecorrerData($DataSemana[$key]['semanaProduccion'], "total_s", $DataEtiquetas);
                        $result = $result == '' ? '0' : $result;
                        $totalResult += $result;

                        echo "<td>" . formatoMil($result, 0) . "</td>";
                    }
                    ?>
                    <td><?= formatoMil($totalResult, 0) ?></td>


                </tr>
                <tr class="c_EtiquetasCza collapse table-secondary">
                    <td>Área WB Total (FT.<sup>2</sup>)</td>
                    <?php
                    $totalResult = 0;
                    foreach ($DataSemana as $key => $value) {
                        $result = getRecorrerData($DataSemana[$key]['semanaProduccion'], "totalWB", $DataEtiquetas);
                        $result = $result == '' ? '0' : $result;
                        $totalResult += $result;

                        echo "<td>" . formatoMil($result) . "</td>";
                    }
                    ?>
                    <td><?= formatoMil($totalResult) ?></td>
                </tr>

                <tr class="c_EtiquetasCza collapse table-secondary">
                    <td>FT<sup>2</sup>/Unid. (Área WB)</td>
                    <?php
                    $totalResult = 0;
                    $dif_Comprada = 0;
                    $totalArea = 0;
                    foreach ($DataSemana as $key => $value) {
                        $total = getRecorrerData($DataSemana[$key]['semanaProduccion'], "total_s", $DataEtiquetas);
                        $total = $total == '' ? '0' : $total;
                        $totalResult += $total;

                        $areaComprada = getRecorrerData($DataSemana[$key]['semanaProduccion'], "totalWB", $DataEtiquetas);
                        $areaComprada = $areaComprada == '' ? '0' : $areaComprada;
                        $totalArea += is_nan($areaComprada / $total) ? '0' : $areaComprada / $total;

                        echo "<td>" . formatoMil(is_nan($areaComprada / $total) ? '0' : $areaComprada / $total, 2) . "</td>";
                    }   ?>

                    <td><?= formatoMil($totalArea, 2) ?></td>


                </tr>
                <tr class="c_EtiquetasCza collapse">
                    <td>Total Producido (FT.<sup>2</sup>)</td>
                    <?php
                    $totalResult = 0;
                    foreach ($DataSemana as $key => $value) {
                        $result = getRecorrerData($DataSemana[$key]['semanaProduccion'], "totalProducido", $DataEtiquetas);
                        $result = $result == '' ? '0' : $result;
                        $totalResult += $result;

                        echo "<td>" . formatoMil($result) . "</td>";
                    }
                    ?>
                    <td><?= formatoMil($totalResult) ?></td>
                </tr>

                <tr class="c_EtiquetasCza collapse">
                    <td>FT<sup>2</sup>/Unid.</td>
                    <?php
                    $totalResult = 0;
                    foreach ($DataSemana as $key => $value) {
                        $total = getRecorrerData($DataSemana[$key]['semanaProduccion'], "total_s", $DataEtiquetas);
                        $total = $total == '' ? '0' : $total * 2;
                        $totalResult += $total;

                        $areaComprada = getRecorrerData($DataSemana[$key]['semanaProduccion'], "totalProducido", $DataEtiquetas);
                        $areaComprada = $areaComprada == '' ? '0' : $areaComprada;
                        $totalArea += is_nan($areaComprada / $total) ? '0' : $areaComprada / $total;

                        echo "<td>" . formatoMil(is_nan($areaComprada / $total) ? '0' : $areaComprada / $total, 2) . "</td>";
                    }   ?>

                    <td><?= formatoMil($totalArea, 2) ?></td>


                </tr>
                <tr class="c_EtiquetasCza collapse">
                    <td>Dif. Área WB vs Crust</td>
                    <?php
                    $totalResult = 0;
                    $dif_Comprada = 0;
                    foreach ($DataSemana as $key => $value) {
                        $result = getRecorrerData($DataSemana[$key]['semanaProduccion'], "difAreaWBCrust", $DataEtiquetas);
                        $result = $result == '' ? '0' : $result;
                        $totalResult += $result;

                        echo "<td>" . formatoMil($result) . "%</td>";
                    }
                    ?>
                    <td><?= formatoMil($totalResult) ?>%</td>
                </tr>
                <tr class="c_EtiquetasCza collapse">
                    <td>Total Dif.Area </td>
                    <?php
                    $totalResult = 0;
                    $dif_Crust = 0;

                    foreach ($DataSemana as $key => $value) {
                        $result = getRecorrerData($DataSemana[$key]['semanaProduccion'], "totalDifArea", $DataEtiquetas);
                        $result = $result == '' ? '0' : $result;
                        $totalResult += $result;

                        echo "<td>" . formatoMil($result) . "%</td>";
                    }

                    ?>
                    <td><?= formatoMil($totalResult) ?>%</td>

                </tr>

                <!-- Fin  de Etiquetas Carnaza-->
            <?php } ?>
        </tbody>
    </table>
</div>
<script>
    $("#table-reporte").DataTable({
        paging: false,
        bInfo: false,
        ordering: false,

        columnDefs: [{
            targets: "_all",
            sortable: false
        }]
    })
</script>

<?php
function getRecorrerData($semana, $codigo, $Array_Rendimientos)
{
    $total = 0;
    if ($codigo == "diferenciaArea") {
    }
    /*echo "<br>Semana".$semana."<br>";
    echo "Codigo".$codigo."<br>";*/
    /* print_r($Array_Rendimientos);
    echo "<br>";*/

    foreach ($Array_Rendimientos as $key => $value) {
        if ($semana == $Array_Rendimientos[$key]['semanaProduccion']) {
            $total += $Array_Rendimientos[$key][$codigo];
        }
    }
    //echo "<br>Value:".$total."<br>";

    return $total;
}
function rellenoTabla($count)
{
    $result = "";
    while ($count > 0) {
        $result .= "<td style='display: none'></td>";
        $count--;
    }
    return $result;
}
?>