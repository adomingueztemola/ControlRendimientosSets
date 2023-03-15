<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../../include/connect_mvc.php";
include('../../Models/Mdl_ConexionBD.php');
include('../../Models/Mdl_Solicitudes.php');
include('../../assets/scripts/cadenas.php');

$debug = 0;
$idUser = $_SESSION['CREident'];
$obj_solicitud = new Solicitud($debug, $idUser);
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
$filtradoEstado = "1=1";
$filtradoEstado = $estado == '1' ? "s.estado='2'" : $filtradoEstado;
$filtradoEstado = $estado == '2' ? "s.estado='0'" : $filtradoEstado;


$DataHistorial = $obj_solicitud->getHistorial(
    $filtradoSemana,
    $filtradoProceso,
    $filtradoPrograma,
    $filtradoMateria,
    $filtradoEstado
);

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
                <th>Empleado Responsable</th>
                <th>Motivo</th>
                <th>Validado Por</th>
                <th>F. Validación</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $count = 0;
            foreach ($DataHistorial as $key => $value) {
                $count++;
                $icon_estado=$DataHistorial[$key]['estado']=='2'?
                            '<small><i class="fas fa-check text-success"></i>Aceptada</small>':
                            '<small><i class="fas fa-times text-danger"></i>Rechazada</small>';
                echo "<tr>
                    <td>$count</td>
                    <td>{$DataHistorial[$key]['f_fechaReg']}</td>
                    <td>{$DataHistorial[$key]['semanaProduccion']}</td>
                    <td><b>{$DataHistorial[$key]['loteTemola']}</b></td>
                    <td>{$DataHistorial[$key]['c_proceso']}</td>
                    <td><small>{$DataHistorial[$key]['n_programa']}</small></td>
                    <td><small>{$DataHistorial[$key]['n_materia']}</small></td>
                    <td>{$DataHistorial[$key]['n_empleadoEnvio']}</td>
                    <td><small>{$DataHistorial[$key]['descripcion']}</small></td>
                    <td>{$DataHistorial[$key]['n_empleadoValida']}</td>
                    <td>{$DataHistorial[$key]['f_fechaValida']}</td>
                    <td>{$icon_estado}</td>
               </tr>";
            }
            ?>
        </tbody>

    </table>
</div>


<script>
    $("#table-historial").DataTable({
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
</script>