<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../../include/connect_mvc.php";
include('../../Models/Mdl_ConexionBD.php');
include('../../Models/Mdl_Rendimiento.php');
include('../../assets/scripts/cadenas.php');

$debug = 0;
$idUser = $_SESSION['CREident'];
$obj_rendimiento = new Rendimiento($debug, $idUser);
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}
$date_start = !empty($_POST['date-start']) ? $_POST['date-start'] : date("01/m/Y");
$date_end = !empty($_POST['date-end']) ? $_POST['date-end'] :  date("t/m/Y");

/***************** CASTEO DE FECHAS ****************** */

$date_start = date("Y-m-d", strtotime(str_replace("/", "-", $date_start)));
$date_end = date("Y-m-d", strtotime(str_replace("/", "-", $date_end)));

$filtradoFecha = "r.fechaEmpaque BETWEEN '$date_start' AND '$date_end'";

$Data = $obj_rendimiento->getAuditoriaUnidades($filtradoFecha);
?>
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <table class="table table-sm" id="table-ventas">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Semana</th>
                    <th>Lote</th>
                    <th>Proceso</th>
                    <th>Programa</th>
                    <th>Unidades Reales</th>
                    <th>Recuperación</th>
                    <th>Unidades Generales</th>
                    <th>% Crecimiento</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $count=0;

                foreach ($Data as $key => $value) {
                    $count++;
                    $f_unitReal= formatoMil($Data[$key]['unitReal'], 2);
                    $f_unitGeneral= formatoMil($Data[$key]['unitGeneral'], 2);
                    $f_realRecuperado= formatoMil($Data[$key]['realRecuperado'], 2);
                    $f_crecimiento= formatoMil($Data[$key]['crecimiento'], 2);

                    echo "<tr>
                        <td>$count</td>
                        <td>{$Data[$key]['semanaProduccion']}</td>
                        <td>{$Data[$key]['loteTemola']}</td>
                        <td>{$Data[$key]['nProceso']}</td>
                        <td>{$Data[$key]['nPrograma']}</td>
                        <td>{$f_unitReal}</td>
                        <td>{$f_realRecuperado}</td>
                        <td>{$f_unitGeneral}</td>
                        <td>{$f_crecimiento}%</td>
                       


                    </tr>";
                }
              

                ?>

            </tbody>
         

        </table>
    </div>
</div>
<script>
    $("#table-ventas").DataTable({
            dom: 'Bfrltip',
            autoWidth: false,
            drawCallback: function() {
                $('[data-toggle="popover"]').popover();
            },
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

                },
                /* {
                                extend: 'pdf',
                                text: 'Archivo PDF',
                                exportOptions: {

                                },
                                orientation: "landscape",
                                pageSize: "TABLOID",
                                footer: true, 
                                customize : function(doc) {doc.pageMargins = [5, 5, 5,5 ]; },



                            }*/
                , {
                    extend: 'print',
                    text: 'Imprimir',
                    exportOptions: {

                    },
                    footer: true

                }
            ],
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
    $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('btn btn-TWM mr-1');
</script>