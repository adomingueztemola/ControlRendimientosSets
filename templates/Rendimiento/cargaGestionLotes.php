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
$lote = !empty($_POST['lote']) ? $_POST['lote'] : '';
$obj_rendimiento = new Rendimiento($debug, $idUser);
$DataLotes = $obj_rendimiento->getLotesPreRegistradosProg();

$arreglo=[];
foreach ($DataLotes as $key => $value) {
    $arreglo['data'][] = $value;
}
?>
<div class="table-responsive">
    <table class="table table-sm" id="table-preregistro">
        <thead>
            <tr>
                <th>#</th>

                <th>Fecha Engrase</th>

                <th>Lote Temola</th>
                <th>Proceso</th>
                <th>Programa</th>

                <th>Materia Prima</th>
                <th>Empleado Registro</th>
                <th>Fecha Registro</th>
                <th>Acción</th>

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

        $.post("../templates/Rendimiento/detallePedido.php", {
                ident: identif
            },
            function(respuesta) {
                $("#" + selector).html(respuesta);
            });

    }
</script>
<script src="../assets/extra-libs/datatables.net/js/jquery.dataTables.min-ESP.js"></script>
<script src="../assets/tablas/dataTable-Pedido.js"></script>
<script src="../dist/js/pages/datatable/datatable-basic.init.js"></script>