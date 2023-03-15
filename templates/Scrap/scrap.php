<?php
$debug = 0;
session_start();
define('INCLUDE_CHECK', 1);
require_once "../../include/connect_mvc.php";
include('../../assets/scripts/cadenas.php');
$idUser = $_SESSION['CREident'];
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}

$date_start = !empty($_POST['date-start']) ? $_POST['date-start'] : date("01/m/Y");
$date_end = !empty($_POST['date-end']) ? $_POST['date-end'] :  date("t/m/Y");
$programa = !empty($_POST['programa']) ? $_POST['programa'] : '';

$date_start = date("Y-m-d", strtotime(str_replace("/", "-", $date_start)));
$date_end = date("Y-m-d", strtotime(str_replace("/", "-", $date_end)));

$filtradoFecha = "t.fechaSalida BETWEEN '$date_start' AND '$date_end'";
$filtradoPrograma = $programa != '' ? "Find_In_Set('$programa',GROUP_CONCAT(DISTINCT cp.id))>0  " : "1=1";

$obj_tarimas = new Scrap($debug, $idUser);
$Data = $obj_tarimas->getTarimas($filtradoPrograma , $filtradoFecha );
$Data = Excepciones::validaConsulta($Data);
$arreglo = [];
foreach ($Data as  $value) {
    $arreglo['data'][] = $value;
}
?>
<div class="row">
    <div class="col-lg-12 col-md-12 col-xs-12 col-sm-12 table-responsive">
        <table class="table table-sm" id="table-tarimas">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Folio</th>
                    <th scope="col">Programa(s)</th>
                    <th scope="col">Semana(s)</th>
                    <th scope="col">Lote(s)</th>
                    <th scope="col">Fecha Salida</th>
                    <th scope="col">12:00</th>
                    <th scope="col">03:00</th>
                    <th scope="col">06:00</th>
                    <th scope="col">09:00</th>
                    <th scope="col">Total</th>
                    <th scope="col">Etiq.</th>

                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
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

        $.post("../templates/Scrap/detalleReporte.php", {
                ident: identif
            },
            function(respuesta) {
                $("#" + selector).html(respuesta);
            });

    }
</script>
<script src="../assets/extra-libs/datatables.net/js/jquery.dataTables.min-ESP.js"></script>
<script src="../assets/tablas/dataTable-Tarima.js"></script>
<script src="../dist/js/pages/datatable/datatable-basic.init.js"></script>