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
$superlote = !empty($_POST['superlote']) ? $_POST['superlote'] : '';

/************************** FILTRADO *******************************/
$DataRendimiento = $obj_inventario->getTrazabilidad($superlote);

?>

<div class="table-responsive">
    <table id="table-inventario" class="table table-sm">
        <thead>
            <tr class="">
                <th>#</th>
                <th>Lote Temola</th>
                <th>Unidades</th>
                <th>Operación</th>
                <th>Fecha Registro</th>
                <th>Empleado Responsable</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $count = 0;
            //echo "<br>Conteo de Rendimiento: ", count($DataRendimiento);
            foreach ($DataRendimiento as $key => $value) {
                $count++;

                $f_unidades= formatoMil($DataRendimiento[$key]['pzasTotales'],2);
                echo "<tr>
                    <td>$count</td>
                    <td>{$DataRendimiento[$key]['loteTemola']}</td>
                    <td>{$f_unidades}</td>
                    <td>{$DataRendimiento[$key]['operacion']}</td>
                    <td>{$DataRendimiento[$key]['f_fechaReg']}</td>
                    <td>{$DataRendimiento[$key]['n_empleadoResp']}</td>
                   
                    </tr>";
            }



            ?>

        </tbody>
       
    </table>
</div>

<script>
    $("#table-inventario").DataTable({
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
</script>