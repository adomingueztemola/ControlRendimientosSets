<?php
define('INCLUDE_CHECK', 1);
session_start();
require_once('../../include/connect_mvc.php');
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
$filtradoEstado = $estado == '1' ? "r.rzgoRecu>0" : "1=1";
$filtradoEstado = $estado == '2' ? "r.rzgoRecu=0" : $filtradoEstado;
$DataRendimiento = $obj_inventario->getInventarioRecuperacion($filtradoSemana, $filtradoProceso, $filtradoPrograma, $filtradoMateria, $filtradoEstado);
$arreglo = [];
$s_piezasRecuperadas=0;
$s_setsRecuperadas=0;
$s_rzgoPiezas=0;
$s_recuperacionInicial=0;
$s_recuperacionFinal=0;
$s_lotesRecuperados=0;
$s_inventario=0;
$count=0;

$s_12=0;
$s_3=0;
$s_6=0;
$s_9=0;

foreach ($DataRendimiento as $key => $value) {
    $count++;
    $arreglo['data'][]= $value;
    $s_piezasRecuperadas+=$DataRendimiento[$key]['totalInvRecu'];
    $s_setsRecuperadas+=$DataRendimiento[$key]['setsInvRecu'];
    $s_rzgoPiezas+=$DataRendimiento[$key]['rzgoRecu'];
    $s_recuperacionInicial+=$DataRendimiento[$key]['porcRecuperacion'];
    $s_12+=$DataRendimiento[$key]['_12Recu'];
    $s_3+=$DataRendimiento[$key]['_3Recu'];
    $s_6+=$DataRendimiento[$key]['_6Recu'];
    $s_9+=$DataRendimiento[$key]['_9Recu'];
    $s_recuperacionFinal+=$DataRendimiento[$key]['porcRecuperacionFinal'];
    $s_lotesRecuperados+=$DataRendimiento[$key]['cantRecuperacion'];
    $s_inventario+=$DataRendimiento[$key]['totalRecu'];

}
?>

<div class="table-responsive">
    <table id="table-inventario" class="table table-sm display nowrap  table-hover table-bordered">
        <thead>
            <tr class="">
                <th>#</th>
                <th>Lote Temola</th>
                <th>Semana</th>
                <th>Proceso</th>
                <th>Programa</th>
                <th>Materia Prima</th>
                <th>Pzas. Recuperadas en Inv.</th>
                <th>Set's Recuperados en Inv.</th>
                <th>% de Recuperacion Inicial</th>
                <th>% de Recuperacion Final</th>
                <th>12.00</th>
                <th>03.00</th>
                <th>06.00</th>
                <th>09.00</th>
                <th>Pzas. Retrabajadas</th>
                <th class="table-danger"><i class="fas fa-lock"></i>Lim. Recuperación</th>
                <th class="table-danger"><i class="fas fa-lock"></i>Pzas. Lim. Recuperación</th>
                <th><i class="fas fa-unlock"></i>Pzas. Disponibles</th>
                <th>Total de Pzas. Recuperadas</th>

            </tr>
        </thead>
        <tbody>
        </tbody>
        <tfoot>
            <tr class="bg-TWM text-white">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td><?= formatoMil($s_piezasRecuperadas,0)?></td>
                <td><?= formatoMil($s_setsRecuperadas,0) ?></td>
                <td><?= formatoMil($count > 0 ? $s_recuperacionInicial / $count : '0.0') ?>%</td>   
                <td><?= formatoMil($count > 0 ? $s_recuperacionFinal / $count : '0.0') ?>%</td>
                <td><?= formatoMil($s_12,0)?></td>
                <td><?= formatoMil($s_3,0)?></td>
                <td><?= formatoMil($s_6,0)?></td>
                <td><?= formatoMil($s_9,0)?></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td><?= formatoMil($s_inventario,0) ?></td>

            </tr>
        </tfoot>
    </table>
</div>

<script>
    <?php
    $var = json_encode($arreglo);
    echo 'var datsJson = ' . $var . ';';
    ?>

    function ejecutandoCarga(identif, element) {

        var selector = 'DIV' + identif;
        var finicio = $('#fStart').val();
        var ffin = $('#fEnd').val();

        $.post("../templates/Almacen/detalleDeRecuperacion.php", {
                ident: identif
            },
            function(respuesta) {
                $("#" + selector).html(respuesta);
            });

    }
    
</script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>
<script src="../assets/extra-libs/datatables.net/js/jquery.dataTables.min-ESP.js"></script>
<script src="../assets/tablas/dataTable-Recuperacion.js"></script>
<script src="../dist/js/pages/datatable/datatable-basic.init.js"></script>