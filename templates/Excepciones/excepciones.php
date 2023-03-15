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

$filtradoSemana = $semanaProduccion != '' ? "CONCAT(YEAR(r.fechaEmpaque), '-W',LPAD(r.semanaProduccion,2,0))='$semanaProduccion'" : "1=1";
$filtradoProceso = $proceso != '' ? "r.idCatProceso='$proceso'" : "1=1";
$filtradoPrograma = $programa != '' ? "r.idCatPrograma='$programa'" : "1=1";
$filtradoMateria = $materia != '' ? "r.idCatMateriaPrima='$materia'" : "1=1";

$DataExcepcion = $obj_excepciones->getPeticionesDeExcepciones($filtradoSemana, $filtradoProceso, $filtradoMateria, $filtradoPrograma);
?>
<div class="alert alert-warning mt-2" role="alert">
    ¡Alerta! Al aceptar una Excepción se actualizará de manera inmediata las piezas de su Inventario.
</div>

<div class="table-responsive">
    <table id="table-pedidos" class="table table-sm">
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
                <th>Pzas. Recuperadas</th>
                <th class="table-success">Pzas. Recuperadas Actuales</th>
                <th>Pzas. Empacadas</th>
                <th class="table-success">Pzas. Empacados Actuales</th>

                <th>Acción</th>


            </tr>
        </thead>
        <tbody>
            <?php
            $count = 0;
            foreach ($DataExcepcion as $key => $value) {
                $pzasRecuperadas = formatoMil($DataExcepcion[$key]['pzasRecuperadas']);
                $pzasRecuperadasAct = formatoMil($DataExcepcion[$key]['totalRecu']);
                $setsEmpacados = formatoMil($DataExcepcion[$key]['pzasEmpacadas']);
                $setsEmpacadosAct = formatoMil($DataExcepcion[$key]['totalEmp']);
                $btnAccion = "
                <div id='bloqueo-btn-2' style='display:none'>
                <button class='btn btn-TWM btn-xs' type='button' disabled=''>
                    <span class='spinner-border spinner-border-sm' role='status' aria-hidden='true'></span>
                    <span class='sr-only'>Loading...</span>
                 </button>

            </div>
            <div id='desbloqueo-btn-2'>
                <button class='button btn btn-xs btn-success' data-toggle='modal' data-target='#ModalAceptarExcepciones' 
                onclick='cargaPrecalculo({$DataExcepcion[$key]['id']})'><i class='fas fa-check'></i></button>
                <button class='button btn btn-xs btn-danger' onclick='cancelarExcepcion({$DataExcepcion[$key]['id']})'><i class='fas fa-times'></i></button>
            </div>
                ";
                $count++;
                echo <<<EOD
<tr>
    <td>{$count}</td>
    <td>{$DataExcepcion[$key]['f_fechaReg']}</td>
    <td>{$DataExcepcion[$key]['semanaProduccion']}</td>

    <td>{$DataExcepcion[$key]['loteTemola']}</td>

    <td><small>{$DataExcepcion[$key]['n_programa']}</small></td>
    <td><small>{$DataExcepcion[$key]['c_proceso']}-{$DataExcepcion[$key]['n_proceso']}</small></td>
    <td><small>{$DataExcepcion[$key]['n_materia']}</small></td>

    <td>{$DataExcepcion[$key]['n_empleadoReg']}</td>
    <td>{$DataExcepcion[$key]['descripcion']}</td>
    <td>{$pzasRecuperadas}</td>
    <td class="table-success">{$pzasRecuperadasAct}</td>
    <td>{$setsEmpacados}</td>
    <td class="table-success">{$setsEmpacadosAct}</td>
    <td>{$btnAccion}</td>
</tr>
EOD;
            }


            ?>


        </tbody>

    </table>
</div>


<script>
    $("#table-pedidos").DataTable({
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

    /**************************** Precalculo de Inventario/ Datos de Recuperacion ***********************************/
    function cargaPrecalculo(idExcepcion) {
        $("#bodyModalExcepciones").load("../templates/Excepciones/cargaPrecalculoExcepcion.php?id=" + idExcepcion);
    }
</script>