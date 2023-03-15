<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../../include/connect_mvc.php";
include('../../Models/Mdl_ConexionBD.php');
include('../../Models/Mdl_MarcadoAMano.php');
include('../../Models/Mdl_PzasVolante.php');
include('../../Models/Mdl_Excepciones.php');

include('../../assets/scripts/cadenas.php');
$debug = 0;
$idUser = $_SESSION['CREident'];
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}

$date_start = !empty($_POST['date-start']) ? $_POST['date-start'] : "";
$date_end = !empty($_POST['date-end']) ? $_POST['date-end'] :  "";
$programa = !empty($_POST['programa']) ? $_POST['programa'] : '';

/***************** CASTEO DE FECHAS ****************** */
if ($date_start != '' and $date_end != '') {
    $date_start = date("Y-m-d", strtotime(str_replace("/", "-", $date_start)));
    $date_end = date("Y-m-d", strtotime(str_replace("/", "-", $date_end)));
}


$filtradoFecha = ($date_start != '' and $date_end != '') ? "l.fecha BETWEEN '$date_start' AND '$date_end'" : "1=1";
$filtradoPrograma = $programa != '' ? "l.idCatPrograma='$programa'" : "1=1";

$obj_marcado = new MarcadoAMano($debug, $idUser);
$obj_volante = new PzasVolante($debug, $idUser);
$DataLote = $obj_marcado->getLotesAll($filtradoFecha, $filtradoPrograma);

$arreglo = [];
foreach ($DataLote as $key => $value) {
    $arreglo['data'][] = $value;
}
?>
<div class="table-responsive">

    <table id="table-historial" class="table table-sm">
        <thead>
            <tr>
                <th>#</th>
                <th>Fecha</th>
                <th>Lote</th>
                <th>Programa</th>
                <th>Piezas Totales</th>
                <th>Yield</th>
                <th>Área Crust</th>
                <th>% Decremento</th>
                <th>Área Medida con Decremento </th>
                <th>Área de Piezas Calculadas</th>
                <th>Estado</th>

                <th>Empleado Responsable</th>
                <th>Acciones</th>

            </tr>

        </thead>
        <tbody>
  
        </tbody>
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

        $.post("../templates/MarcadoAMano/detalleMarcado.php", {
                ident: identif
            },
            function(respuesta) {
                $("#" + selector).html(respuesta);
            });

    }

</script>
<script src="../assets/extra-libs/datatables.net/js/jquery.dataTables.min-ESP.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>
<script src="../assets/tablas/dataTable-MarcadoReal.js"></script>
