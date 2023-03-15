<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../../include/connect_mvc.php";
include('../../Models/Mdl_ConexionBD.php');
include('../../Models/Mdl_Solicitudes.php');
include('../../assets/scripts/cadenas.php');

$debug = 0;
$idUser = $_SESSION['CREident'];
$obj_solicitudes = new Solicitud($debug, $idUser);
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

$filtradoSemana = $semanaProduccion != '' ? "CONCAT(YEAR(r.fechaEmpaque), '-W',LPAD(r.semanaProduccion,2,0))='$semanaProduccion'" : "1=1";
$filtradoProceso = $proceso != '' ? "r.idCatProceso='$proceso'" : "1=1";
$filtradoPrograma = $programa != '' ? "r.idCatPrograma='$programa'" : "1=1";
$filtradoMateria = $materia != '' ? "r.idCatMateriaPrima='$materia'" : "1=1";

$DataSolicitud = $obj_solicitudes->getSolicitudesPendientes($filtradoSemana, $filtradoProceso, $filtradoMateria, $filtradoPrograma);
$arreglo = [];
foreach ($DataSolicitud as $key => $value) {
    $arreglo['data'][] = $value;
}
?>
<div class="alert alert-warning mt-2" role="alert">
    ¡Alerta! Al aceptar una Edición, autorizas la edición de los parametros de Rendimiento del Lote.
</div>

<div class="table-responsive">
    <table id="table-solicitudes" class="table table-sm">
        <thead>
            <tr class="">
                <th>#</th>
                <th>Fecha de Envío</th>
                <th>Semana Producción</th>
                <th>Lote Temola</th>
                <th>Programa</th>
                <th>Proceso</th>
                <th>Materia Prima</th>
                <th>Empleado Responsable</th>
                <th>Motivo</th>
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
      
        var selector = 'DIV' + element;
        var finicio = $('#fStart').val();
        var ffin = $('#fEnd').val();

        $.post("../templates/Ediciones/detalleDeRendimiento.php", {
                ident: identif
            },
            function(respuesta) {
                $("#" + selector).html(respuesta);
            });
    }

    /**************************** ACEPTAR SOLICITUD****************************/
    function aceptarEdicion(idSolicitud) {
        $.ajax({
            url: '../Controller/solicitudesEdicion.php?op=aceptar',
            data: {
                idSolicitud:idSolicitud
            },
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    bloqueoBtn("bloqueo-btn-2-" + idSolicitud, 2)
                    notificaSuc(resp[1])
                    setTimeout(() => {
                        update()

                    }, 1500);
                } else if (resp[0] == 0) {
                    bloqueoBtn("bloqueo-btn-2-" + idSolicitud, 2)

                    notificaBad(resp[1])
                }
            },
            beforeSend: function() {
                bloqueoBtn("bloqueo-btn-2-" + idSolicitud, 2)

            }
        });
    }
    /**************************** RECHAZAR SOLICITUD****************************/
    function rechazarEdicion(idSolicitud) {
        $.ajax({
            url: '../Controller/solicitudesEdicion.php?op=cancelar',
            data: {
                idSolicitud:idSolicitud
            },
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    bloqueoBtn("bloqueo-btn-2-" + idSolicitud, 2)
                    notificaSuc(resp[1])
                    setTimeout(() => {
                        update()

                    }, 1500);
                } else if (resp[0] == 0) {
                    bloqueoBtn("bloqueo-btn-2-" + idSolicitud, 2)

                    notificaBad(resp[1])
                }
            },
            beforeSend: function() {
                bloqueoBtn("bloqueo-btn-2-" + idSolicitud, 2)

            }
        });
    }
</script>

<script src="../assets/extra-libs/datatables.net/js/jquery.dataTables.min-ESP.js"></script>
<script src="../assets/tablas/dataTable-Solicitudes.js"></script>
<script src="../dist/js/pages/datatable/datatable-basic.init.js"></script>