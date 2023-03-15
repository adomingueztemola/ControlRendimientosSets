<?php
define('INCLUDE_CHECK', 1);
session_start();
require_once('../../include/connect_mvc.php');
include("../../Models/Mdl_ConexionBD.php");
include('../../assets/scripts/cadenas.php');

$idUser = $_SESSION['CREident'];
$nameUser = $_SESSION['CREnombreUser'];
setlocale(LC_TIME, 'es_ES.UTF-8');
$debug = 0;
$space = 1;
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}
$programa = !empty($_POST['programa']) ? $_POST['programa'] : '';
$filtradoPrograma = $programa != '' ? "r.idCatPrograma='$programa'" : "1=1";

$obj_empaque = new Empaque($debug, $idUser);
$Data = $obj_empaque->getReporteRemanente($filtradoPrograma);
$Data = Excepciones::validaConsulta($Data);
?>
<div class="row">

</div>
<div class="table-responsive">
    <table class="table table-sm" id="table-remanente">
        <thead>
            <tr>
                <th>#</th>
                <th>Cerrado</th>
                <th>Lote</th>
                <th>Programa</th>
                <th>12:00 Ini.</th>
                <th>03:00 Ini.</th>
                <th>06:00 Ini.</th>
                <th>09:00 Ini.</th>
                <th>Total Ini.</th>

                <th class='table-success'>12:00 Act.</th>
                <th class='table-success'>03:00 Act.</th>
                <th class='table-success'>06:00 Act.</th>
                <th class='table-success'>09:00 Act.</th>
                <th class='table-success'>Total Act.</th>

            </tr>
        </thead>
        <tbody>
            <?php
            $count = 1;
            $suma_12 = 0;
            $suma_3 = 0;
            $suma_6 = 0;
            $suma_9 = 0;
            $suma_total = 0;
            foreach ($Data as  $value) {
                $iconCerrado = $value['usoRemanente'] == '1' ? '<i class="fas fa-check text-success"></i> Cerrado' : '';
                $suma_12 =  $value['usoRemanente'] == '0'?$suma_12 + $value['_12Rem']:$suma_12;
                $suma_3 = $value['usoRemanente'] == '0'?$suma_3 + $value['_3Rem']:$suma_3;;
                $suma_6 = $value['usoRemanente'] == '0'?$suma_6 + $value['_6Rem']:$suma_6;;
                $suma_9 = $value['usoRemanente'] == '0'?$suma_9 + $value['_9Rem']:$suma_9;;
                $suma_total = $value['usoRemanente'] == '0'?$suma_total + $value['totalRem']:$suma_total;;

                $_12= formatoMil($value['_12'],0);
                $_6= formatoMil($value['_6'],0);
                $_3= formatoMil($value['_3'],0);
                $_9= formatoMil($value['_9'],0);
                $total= formatoMil($value['total'],0);

                $_12Rem= formatoMil($value['_12Rem'],0);
                $_6Rem= formatoMil($value['_6Rem'],0);
                $_3Rem= formatoMil($value['_3Rem'],0);
                $_9Rem= formatoMil($value['_9Rem'],0);
                $totalRem= formatoMil($value['totalRem'],0);
                echo "<tr>
                        <td>{$count}</td>
                        <td>{$iconCerrado}</td>
                        <td>{$value['loteTemola']}</td>
                        <td>{$value['nPrograma']}</td>
                        <td>{$_12}</td>
                        <td>{$_3}</td>
                        <td>{$_6}</td>
                        <td>{$_9}</td>
                        <td>{$total}</td>

                        <td class='table-success'>{$_12Rem}</td>
                        <td class='table-success'>{$_3Rem}</td>
                        <td class='table-success'>{$_6Rem}</td>
                        <td class='table-success'>{$_9Rem}</td>
                        <td class='table-success'>{$totalRem}</td>

                    </tr>";
                $count++;
            }


            ?>

        </tbody>
        <tfoot>
            <tr class="bg-TWM text-white">
                <td>Total en Línea</td>
                <td></td>
                <td></td>
                <td></td>

                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>

                <td><?= formatoMil($suma_12,0) ?></td>
                <td><?= formatoMil($suma_3,0) ?></td>
                <td><?= formatoMil($suma_6,0) ?></td>
                <td><?= formatoMil($suma_9,0) ?></td>
                <td><?= formatoMil($suma_total,0) ?></td>

            </tr>
        </tfoot>
    </table>
</div>

<script>
    $("#table-remanente").DataTable({
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
        }

    );
    $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('btn btn-TWM mr-1');
</script>