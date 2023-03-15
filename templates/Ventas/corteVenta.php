<?php
session_start();
define('INCLUDE_CHECK', 1);
setlocale(LC_TIME, 'es_ES.UTF-8');
require_once "../../include/connect_mvc.php";
include('../../Models/Mdl_ConexionBD.php');
include('../../Models/Mdl_Venta.php');
include('../../assets/scripts/cadenas.php');
$debug = 0;
$idUser = $_SESSION['CREident'];
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}
$mesFacturacion = (!empty($_POST['mesFacturacion'])  and $_POST['mesFacturacion'] != '') ? $_POST['mesFacturacion'] : date("Y-m");
$filtradoMes = $mesFacturacion != '' ? "DATE_FORMAT(v.fechaFact, '%Y-%m')='$mesFacturacion'" : '';

//Objeto de Venta
$obj_venta = new Venta($debug, $idUser);
$DataCorteMes = $obj_venta->getCorteVenta($filtradoMes);
$formatMonthDate = formatoFecha(2, false, $mesFacturacion);
$Array_RangoSemanal = [];
$ArrayFechasInit = [];
foreach ($DataCorteMes as $key => $value) {
    if (!in_array($DataCorteMes[$key]['format_LimitInitWeek'] . ' - ' . $DataCorteMes[$key]['format_LimitFinWeek'], $Array_RangoSemanal)) {
        array_push($Array_RangoSemanal, $DataCorteMes[$key]['format_LimitInitWeek'] . ' - ' . $DataCorteMes[$key]['format_LimitFinWeek']);
        array_push($ArrayFechasInit, $DataCorteMes[$key]['f_LimitInitWeek']);
    }
}
$SemanaLenght = count($Array_RangoSemanal);
?>
<div class="table-responsive">
    <table id="table-corte" class="table table-sm">
        <thead>
            <tr>
                <th colspan="<?= $SemanaLenght + 1 ?>" class="bg-TWM text-white text-center"><?= $formatMonthDate ?></th>
            </tr>
            <tr>
                <th class="bg-TWM text-white text-center">Lote Temola</th>
                <?php
                foreach ($Array_RangoSemanal as $key => $value) {
                    echo "<th class='bg-TWM text-white text-center'>Sem: {$Array_RangoSemanal[$key]}</th>";
                }
                ?>
            </tr>

        </thead>
        <tbody>
            <?php
            $loteTemolaAnt = "";
            $contentCelda = array();
            $conteo;
            if (count($DataCorteMes) > 0) {
                foreach ($DataCorteMes as $key => $value) {
                    $posicion_celda = array_search($DataCorteMes[$key]['f_LimitInitWeek'], $ArrayFechasInit) + 1;
                    if ($loteTemolaAnt != $DataCorteMes[$key]['loteTemola'] and $loteTemolaAnt != '') {
                        $contador_celdas = 1;
                        while ($contador_celdas < ($SemanaLenght+1)) {
                            if (array_key_exists($contador_celdas, $contentCelda)) {
                                echo ($contentCelda[$contador_celdas]);
                            } else {
                                echo "<td class='text-center'>0.0</td>";
                            }

                            $contador_celdas++;
                        }
                        $contentCelda = array();
                        echo "</tr><tr>
                             <td class='text-center'> {$DataCorteMes[$key]['loteTemola']}</td>";
                    } else if ($loteTemolaAnt == '') {
                        echo "<td class='text-center'> {$DataCorteMes[$key]['loteTemola']}</td>";
                    }
                 $contentCelda=($contentCelda+array("$posicion_celda" => "<td class='text-center table-success'> {$DataCorteMes[$key]['sum_Unidades']}</td>"));
                   // $contentCelda = array_merge($contentCelda, ["$posicion_celda" => "<td class='text-center table-success'> {$DataCorteMes[$key]['sum_Unidades']}</td>"]);
                    $loteTemolaAnt = $DataCorteMes[$key]['loteTemola'];
                }
                $contador_celdas = 1;
                while ($contador_celdas < ($SemanaLenght+1)) {
                    if (array_key_exists($contador_celdas, $contentCelda)) {
                        echo ($contentCelda[$contador_celdas]);
                    } else {
                        echo "<td class='text-center'>0.0</td>";
                    }

                    $contador_celdas++;
                }
            } else {
                echo "<tr><td class='text-center text-danger'> No hay Ventas dentro de este Mes</td></tr>";
            }

            ?>

        </tbody>
    </table>
</div>
<script>
    $("#table-corte").DataTable({
        dom: 'Bfrltip',
        "aaSorting": [],
        buttons: [{
            extend: 'copy',
            text: 'Copiar Formato',
            exportOptions: {

            },
            footer: true
        }, {
            extend: 'excel',
            text: 'Excel',
            exportOptions: {

            },
            footer: true

        }, {
            extend: 'pdf',
            text: 'Archivo PDF',
            exportOptions: {

            },
            orientation: "landscape",
            footer: true

        }, {
            extend: 'print',
            text: 'Imprimir',
            exportOptions: {

            },
            footer: true

        }]
    });
    $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('btn btn-TWM mr-1');
</script>