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
$filtradoSemana = $semanaProduccion != '' ? "CONCAT(r.yearWeek, '-W', LPAD(r.semanaProduccion,2,'0'))='$semanaProduccion'" : "1=1";
$filtradoProceso = $proceso != '' ? "r.idCatProceso='$proceso'" : "1=1";
$filtradoPrograma = $programa != '' ? "r.idCatPrograma='$programa'" : "1=1";
$filtradoMateria = $materia != '' ? "r.idCatMateriaPrima='$materia'" : "1=1";
$DataRendimiento = $obj_inventario->getConsumoInterno($filtradoSemana, $filtradoProceso, $filtradoPrograma, $filtradoMateria);
?>

<div class="table-responsive">
    <table id="table-interno" class="table table-sm">
        <thead>
            <tr class="">
                <th>AÑO/SEMANA</th>
                <th>LOTE</th>
                <th>PROGRAMA</th>
                <th>PL'S RELACIONADOS</th>
                <th>CAJA COMPL.</th>
                <th>MIX</th>
                <th>TOTAL</th>

                <th>DETALLADO</th>

            </tr>
        </thead>
        <tbody>
            <?php
            $count = 0;
            foreach ($DataRendimiento as $key => $value) {
                $fCajas = formatoMil($value['cajasCompletas'], 0);
                $fMix = formatoMil($value['cajasInCompletas'], 0);
                $btn = "<button class='btn btn-success btn-xs' onclick='verDetallado({$DataRendimiento[$key]['id']})' data-toggle='modal' data-target='#detalladoModal'><i class='fas fa-box'></i> Detallado</button>";
                $fTotal= formatoMil($value["totalCajasVend"],0);
                echo "<tr>
                    <td>{$DataRendimiento[$key]['semanaAnio']}</td>
                    <td><b>{$DataRendimiento[$key]['loteTemola']}</b></td>
                    <td>{$DataRendimiento[$key]['nPrograma']}</td>
                    <td>{$DataRendimiento[$key]['totalpls']}</td>
                 
                    <td>{$fCajas}</td>
                    <td>{$fMix}</td>
                    <td>{$fTotal}</td>

                    <td>$btn</td>

                    </tr>";
            }



            ?>

        </tbody>

    </table>
</div>

<!-- Modal -->
<div class="modal fade" id="detalladoModal" role="dialog" aria-labelledby="detalladoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-TWM text-white">
                <h5 class="modal-title" id="detalladoModalLabel">Detallado de Cajas</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="div-detalladocajas">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
    $("#table-interno").DataTable({
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
    // FUNCION DE DETALLADO
    function verDetallado(id) {
        $.ajax({
            url: '../templates/Almacen/informeEmpaque.php',
            data: {id:id},
            type: 'POST',
            success: function(respuesta) {
                $('#div-detalladocajas').html(respuesta);


            },
            beforeSend: function() {
                $('#div-detalladocajas').html('<div class="loading text-center"><img src="../assets/images/loading.gif" alt="loading" /><br/>Un momento, por favor...</div>');


            }

        });
    }
</script>