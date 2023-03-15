<?php
define('INCLUDE_CHECK', 1);
session_start();
require_once('../../include/connect_mvc.php');
include("../../Models/Mdl_ConexionBD.php");
include("../../Models/Mdl_Inventario.php");
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
$obj_inventario = new Inventario($debug, $idUser);
/************************** VARIABLES DE FILTRADO *******************************/
$proceso = !empty($_POST['proceso']) ? $_POST['proceso'] : '';
$programa = !empty($_POST['programa']) ? $_POST['programa'] : '';
$materia = !empty($_POST['materia']) ? $_POST['materia'] : '';
$estado = !empty($_POST['estado']) ? $_POST['estado'] : '';
$semanaProduccion = !empty($_POST['semanaProduccion']) ? $_POST['semanaProduccion'] : '';

/************************** FILTRADO *******************************/
$filtradoSemana = $semanaProduccion != '' ? "CONCAT(YEAR(r.fechaEmpaque), '-W', r.semanaProduccion)='$semanaProduccion'" : "1=1";
$filtradoProceso = $proceso != '' ? "r.idCatProceso='$proceso'" : "1=1";
$filtradoPrograma = $programa != '' ? "r.idCatPrograma='$programa'" : "1=1";
$filtradoMateria = $materia != '' ? "r.idCatMateriaPrima='$materia'" : "1=1";
$filtradoEstado = $estado == '1' ? "(r.porcFinalRechazo-r.porcSetsRechazoInicial)>0" : "1=1";
$filtradoEstado = $estado == '2' ? "(r.porcFinalRechazo-r.porcSetsRechazoInicial)<=0" : $filtradoEstado;
$DataRendimiento = $obj_inventario->getInventarioRechazo($filtradoSemana, $filtradoProceso, $filtradoPrograma, $filtradoMateria, $filtradoEstado);
?>

<div class="table-responsive">
    <table id="table-inventario" class="table table-sm display nowrap  table-hover table-bordered">
        <thead>
            <tr class="">
                <th>#</th>
                <th>Lote Temola</th>
                <th>SemanaProducción</th>
                <th>Proceso</th>
                <th>Programa</th>
                <th>Materia Prima</th>
                <th>12.00</th>
                <th>03.00</th>
                <th>06.00</th>
                <th>09.00</th>
                <th class="table-danger">Pzas. Rechazadas</th>

                <th>% Inicial de Rechazo</th>
                <th>% Final de Rechazo</th>
                <th>Diferencia de %</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $count = 0;
            $s_pzasSinSet = 0;
            $s_12 = 0;
            $s_3 = 0;
            $s_6 = 0;
            $s_9 = 0;
            foreach ($DataRendimiento as $key => $value) {
                $count++;
                $s_pzasRechazadas += $DataRendimiento[$key]['totalRech'];
                $s_setsTotalRech += $DataRendimiento[$key]['setsTotalRech'];
                $s_porcSetsRechazoInicial +=$DataRendimiento[$key]['porcSetsRechazoInicial'];
                $s_porcFinalRechazo +=$DataRendimiento[$key]['porcFinalRechazo'];
                $s_difPorc+=$DataRendimiento[$key]['difPorc'];
                $s_12+=$DataRendimiento[$key]['_12Scrap'];
                $s_3+=$DataRendimiento[$key]['_3Scrap'];
                $s_6+=$DataRendimiento[$key]['_6Scrap'];
                $s_9+=$DataRendimiento[$key]['_9Scrap'];


                $pzasRechazadas = formatoMil($DataRendimiento[$key]['totalRech'], 0);
                $setsTotalRech = formatoMil($DataRendimiento[$key]['setsTotalRech'], 0);
                $porcSetsRechazoInicial = formatoMil($DataRendimiento[$key]['porcSetsRechazoInicial'], 2);
                $porcFinalRechazo = formatoMil($DataRendimiento[$key]['porcFinalRechazo'], 2);
                $difPorc= $DataRendimiento[$key]['difPorc']==0? formatoMil($DataRendimiento[$key]['difPorc'], 2):$difPorc;
                $difPorc= $DataRendimiento[$key]['difPorc']<0? "<i class='fas fa-check text-success'></i>".formatoMil($DataRendimiento[$key]['difPorc'],2):$difPorc;
                $difPorc= $DataRendimiento[$key]['difPorc']>0? "<i class='fas fa-times text-danger'></i>".formatoMil($DataRendimiento[$key]['difPorc'],2):$difPorc;
                
                $_12= formatoMil($DataRendimiento[$key]['_12Scrap'],0);
                $_3= formatoMil($DataRendimiento[$key]['_3Scrap'],0);
                $_6= formatoMil($DataRendimiento[$key]['_6Scrap'],0);
                $_9= formatoMil($DataRendimiento[$key]['_9Scrap'],0);
                echo "<tr>
                    <td>$count</td>
                    <td>{$DataRendimiento[$key]['loteTemola']}</td>
                    <td>{$DataRendimiento[$key]['semanaProduccion']}</td>
                    <td><small>{$DataRendimiento[$key]['c_proceso']}</small></td>
                    <td><small>{$DataRendimiento[$key]['n_programa']}</small></td>

                    <td><small>{$DataRendimiento[$key]['n_materia']}</small></td>
                    <td >{$_12}</td>
                    <td >{$_3}</td>
                    <td >{$_6}</td>
                    <td >{$_9}</td>
                    <td class='table-danger'>{$pzasRechazadas}</td>


                    <td >{$porcSetsRechazoInicial}%</td>
                    <td>{$porcFinalRechazo}%</td> 
                    <td> $difPorc%</td>
                    </tr>";
            }



            ?>

        </tbody>
        <tfoot>
            <tr class="bg-TWM text-white">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td><?=formatoMil($s_12)?></td>
                <td><?=formatoMil($s_3)?></td>
                <td><?=formatoMil($s_6)?></td>
                <td><?=formatoMil($s_9)?></td>
                <td><?= formatoMil($s_pzasRechazadas) ?></td>

                <td><?=formatoMil($count>0?$s_porcSetsRechazoInicial/$count:'0.0')?>%</td>
                <td><?=formatoMil($count>0?$s_porcFinalRechazo/$count:'0.0')?>%</td>
                <td><?=formatoMil($count>0?$s_difPorc/$count:'0.0')?>%</td>


            </tr>
        </tfoot>
    </table>
</div>

<script>
    $("#table-inventario").DataTable({
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