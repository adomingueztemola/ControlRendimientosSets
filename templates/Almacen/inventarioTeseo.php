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
$filtradoSemana= $semanaProduccion!=''?"CONCAT(YEAR(r.fechaEmpaque), '-W', r.semanaProduccion)='$semanaProduccion'":"1=1";
$filtradoProceso = $proceso != '' ? "r.idCatProceso='$proceso'" : "1=1";
$filtradoPrograma = $programa != '' ? "r.idCatPrograma='$programa'" : "1=1";
$filtradoMateria = $materia != '' ? "r.idCatMateriaPrima='$materia'" : "1=1";
$filtradoEstado = $estado == '1' ? "r.rzgoTeseo>0" : "1=1";
$filtradoEstado = $estado == '2' ? "r.rzgoTeseo=0" : $filtradoEstado;
$DataRendimiento = $obj_inventario->getInventarioTeseo($filtradoSemana,$filtradoProceso, $filtradoPrograma, $filtradoMateria, $filtradoEstado);
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
                <th>Pzas Cortadas en Teseo</th>
                <th>Set's Cortados en Teseo</th>
                <th>12:00</th>
                <th>03:00</th>
                <th>06:00</th>
                <th>09:00</th>

                <th>Área</th>
                <th>Yield</th>

            </tr>
        </thead>
        <tbody>
            <?php
                $count=0;
                $s_pzasCortadas=0;
                $s_setsCortadas=0;
                $s_area=0;
                $s_yield=0;
                $s_12=0;
                $s_3=0;
                $s_6=0;
                $s_9=0;
                foreach ($DataRendimiento as $key => $value) {
                    $count++;
                    $s_pzasCortadas+=$DataRendimiento[$key]['pzasCortadasTeseo'];
                    $s_setsCortadas+=$DataRendimiento[$key]['setsCortadosTeseo'];
                    $s_area+=$DataRendimiento[$key]['areaFinal'];
                    $s_yield+=$DataRendimiento[$key]['yieldInicialTeseo'];
                    $s_12+=$DataRendimiento[$key]['_12Teseo'];
                    $s_3+=$DataRendimiento[$key]['_3Teseo'];
                    $s_6+=$DataRendimiento[$key]['_6Teseo'];
                    $s_9+=$DataRendimiento[$key]['_9Teseo'];

                    $pzasCortados= formatoMil($DataRendimiento[$key]['pzasCortadasTeseo'],0);
                    $setsCortados= formatoMil($DataRendimiento[$key]['setsCortadosTeseo'],0);
                    $diferencia= formatoMil($DataRendimiento[$key]['areaFinal'],0);
                    $yield= formatoMil($DataRendimiento[$key]['yieldInicialTeseo'],0);
                    
                    $_12= formatoMil($DataRendimiento[$key]['_12Teseo'],0);
                    $_3= formatoMil($DataRendimiento[$key]['_3Teseo'],0);
                    $_6= formatoMil($DataRendimiento[$key]['_6Teseo'],0);
                    $_9= formatoMil($DataRendimiento[$key]['_9Teseo'],0);

                    echo "<tr>
                    <td>$count</td>
                    <td>{$DataRendimiento[$key]['loteTemola']}</td>
                    <td>{$DataRendimiento[$key]['semanaProduccion']}</td>
                    <td><small>{$DataRendimiento[$key]['c_proceso']}</small></td>
                    <td><small>{$DataRendimiento[$key]['n_programa']}</small></td>

                    <td><small>{$DataRendimiento[$key]['n_materia']}</small></td>
                    <td>{$pzasCortados}</td>
                    <td>{$setsCortados}</td>
                    <td>{$_12}</td>
                    <td>{$_3}</td>
                    <td>{$_6}</td>
                    <td>{$_9}</td>

                    <td>{$diferencia}</td>
                    <td>{$yield}%</td>


                    
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
                <td><?=formatoMil($s_pzasCortadas)?></td>
                <td><?=formatoMil($s_setsCortadas)?></td>
                <td><?=formatoMil($s_12,0)?></td>
                <td><?=formatoMil($s_3,0)?></td>
                <td><?=formatoMil($s_6,0)?></td>
                <td><?=formatoMil($s_9,0)?></td>
                <td><?=formatoMil($s_area)?></td>
                
                <td><?=formatoMil($s_yield/$count)?>%</td>


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