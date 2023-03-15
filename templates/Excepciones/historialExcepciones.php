<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../../include/connect_mvc.php";
include('../../Models/Mdl_ConexionBD.php');
include('../../Models/Mdl_ExcepcionDeStock.php');
include('../../assets/scripts/cadenas.php');

$debug = 0;
$idUser = $_SESSION['CREident'];
$obj_excepciones = new ExcepcionDeStock($debug, $idUser);
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}
$proceso = !empty($_POST['proceso']) ? $_POST['proceso'] : '';
$programa = !empty($_POST['programa']) ? $_POST['programa'] : '';
$materia = !empty($_POST['materia']) ? $_POST['materia'] : '';
$semanaProduccion = !empty($_POST['semanaProduccion']) ? $_POST['semanaProduccion'] : '';
$estado = !empty($_POST['estado']) ? $_POST['estado'] : '';

$filtradoSemana = $semanaProduccion != '' ? "CONCAT(YEAR(r.fechaEmpaque), '-W', LPAD(r.semanaProduccion,2,0))='$semanaProduccion'" : "1=1";
$filtradoProceso = $proceso != '' ? "r.idCatProceso='$proceso'" : "1=1";
$filtradoPrograma = $programa != '' ? "r.idCatPrograma='$programa'" : "1=1";
$filtradoMateria = $materia != '' ? "r.idCatMateriaPrima='$materia'" : "1=1";
/******** FILTRADO DE ESTATUS********/
$filtradoEstado="1=1";
$filtradoEstado=$estado=='1'?"e.estado='2'":$filtradoEstado;
$filtradoEstado=$estado=='2'?"e.estado='0'":$filtradoEstado;


$DataExcepcion = $obj_excepciones->getHistorial($filtradoSemana, $filtradoProceso, $filtradoPrograma, 
                                                $filtradoMateria, $filtradoEstado);
$arreglo = [];
foreach ($DataExcepcion as $key => $value) {
    $arreglo['data'][] = $value;
}
?>
<div class="table-responsive">
    <table id="table-historial" class="table table-sm">
        <thead>
            <tr class="">
                <th>#</th>
                <th>F. Envío</th>
                <th>Sem. Producción</th>
                <th>Lote Temola</th>
                <th>Proceso</th>
                <th>Programa</th>
                <th>Mat. Prima</th>
                <th>Pzas. Recuperadas</th>
                <th>Pzas. Empacadas</th>

                <th>Empleado Responsable</th>
                <th>Motivo</th>
                <th>Validado Por</th>
                <th>F. Validación</th>

                <th>Estado</th>
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

        $.post("../templates/Excepciones/detalleExcepcion.php", {
                ident: identif
            },
            function(respuesta) {
                $("#" + selector).html(respuesta);
            });

    }
    /* $("#table-pedidos").DataTable({
            dom: 'Bfrltip',
            autoWidth: false,
            drawCallback: function() {
                $('[data-toggle="popover"]').popover();
            },

            columnDefs: [{
                    "width": "5%"
                },
                {
                    "width": "20%"
                },
                {
                    "width": "40%"
                },
                {
                    "width": "50%"
                },
                {
                    "width": "100%"
                }
            ]
        }

    );
*/
</script>
<script src="../assets/extra-libs/datatables.net/js/jquery.dataTables.min-ESP.js"></script>
<script src="../assets/tablas/dataTable-HistExcepcion.js"></script>
<script src="../dist/js/pages/datatable/datatable-basic.init.js"></script>