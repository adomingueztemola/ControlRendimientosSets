<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once('../../include/connect_mvc.php');
include("../../Models/Mdl_ConexionBD.php");
include("../../Models/Mdl_Rendimiento.php");
include('../../assets/scripts/cadenas.php');
$debug = 0;
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}
/************************** VARIABLES DE FILTRADO *******************************/
$proceso = !empty($_POST['procesos']) ? $_POST['procesos'] : '';
$programa = !empty($_POST['programas']) ? $_POST['programas'] : '';
$materiaPrima = !empty($_POST['materiaPrima']) ? $_POST['materiaPrima'] : '';
$tipo = !empty($_POST['tipo']) ? $_POST['tipo'] : '';

$idUser = $_SESSION['CREident'];
$nameUser = $_SESSION['CREnombreUser'];

$filtradoProceso = $proceso != '' ? "r.idCatProceso='$proceso'" : "1=1";
$filtradoPrograma = $programa != '' ? "r.idCatPrograma='$programa'" : "1=1";
$filtradoMateria = $materiaPrima != '' ? "r.idCatMateriaPrima='$materiaPrima'" : "1=1";
$filtradoEstado = $tipo  != '' ? "r.tipoProceso='$tipo'" : "1=1";


setlocale(LC_TIME, 'es_ES.UTF-8');
$obj_rendimiento = new Rendimiento($debug, $idUser);
$DataLotes = $obj_rendimiento->getLotesCapturados($filtradoProceso, $filtradoPrograma, $filtradoMateria, $filtradoEstado);

?>
<div class="table-responsive">
    <table class="table table-sm" id="table-preasignacion">
        <thead>
            <tr>
                <th>#</th>
                <th>Semana</th>
                <th>Fecha Engrase</th>
                <th>Fecha Empaque</th>
                <th>Tipo de Lote</th>

                <th>Lote</th>
                <th>Programa</th>
                <th>Proceso</th>
                <th>Materia Prima</th>
                <th>Área Neta</th>
                <th>Yield WB Real Final</th>

                <th>Empleado Registro</th>
                <th>Fecha Registro</th>
                <th>Acción</th>

            </tr>

        </thead>
        <tbody>
            <?php
            $count = 1;
            foreach ($DataLotes as $key => $value) {
                $f_areaNeta = $DataLotes[$key]['tipoProceso'] == '1' ? formatoMil($DataLotes[$key]['areaNeta_Prg'], 2) : '<i>N/A</i>';
                $f_yieldWB =  $DataLotes[$key]['tipoProceso'] == '1' ? formatoMil($DataLotes[$key]['yieldFinalReal'], 2) . '%' : '<i>N/A</i>';
                $txt_proceso = $DataLotes[$key]['tipoProceso'] == '1' ? "Set's" : "M<sup>2</sup>";
                echo "<tr>
                    <td>{$count}</td>
                    <td>{$DataLotes[$key]['semanaProduccion']}</td>
                    <td>{$DataLotes[$key]['f_fechaEngrase']}</td>
                    <td>{$DataLotes[$key]['f_fechaEmpaque']}</td>
                    <td>{$txt_proceso}</td>

                    <td>{$DataLotes[$key]['loteTemola']}</td>
                    <td>{$DataLotes[$key]['n_programa']}</td>
                    <td><small>{$DataLotes[$key]['c_proceso']}-{$DataLotes[$key]['n_proceso']}</small></td>
                    <td>{$DataLotes[$key]['n_materiaprima']}</td>
                    <td>{$f_areaNeta}</td>
                    <td>{$f_yieldWB}</td>
                    <td>{$DataLotes[$key]['n_usuarioRend']}</td>
                    <td>{$DataLotes[$key]['f_fechaRend']}</td>

                    <td><button class='btn btn-primary btn-xs' data-toggle='modal' data-target='#reasignarModal' 
                                onclick='cargaDatosLote({$DataLotes[$key]['id']}, \"{$DataLotes[$key]['loteTemola']}\")'><i class='fas fa-pencil-alt'></i></button></td>
                  </tr>";
                $count++;
            }
            ?>
        </tbody>
    </table>

</div>

<script>
    $('#table-preasignacion').DataTable({});

    function cargaDatosLote(idLote, nameLote) {
        $("#txt-nameLote").html(nameLote);
        $('#modalBodyReasignacion').html('<div class="loading text-center"><img src="../assets/images/loading.gif" alt="loading" /><br/>Un momento, por favor...</div>');
        $('#modalBodyReasignacion').load('../templates/Rendimiento/cargaDetallesReasignacion.php?data=' + idLote);
    }
</script>