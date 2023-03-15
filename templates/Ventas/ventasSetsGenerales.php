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
$programa = !empty($_POST['programa']) ? $_POST['programa'] : '';

/***************** CASTEO DE FECHAS ****************** */

$date_start = date("Y-m-d", strtotime(str_replace("/", "-", $date_start)));
$date_end = date("Y-m-d", strtotime(str_replace("/", "-", $date_end)));

$filtradoFecha = "v.fechaFact BETWEEN '$date_start' AND '$date_end'";
$filtradoPrograma = $programa != '' ? "r.idCatPrograma='$programa'" : "1=1";

$Data = $obj_rendimiento->getVentasSetsGenerales($filtradoFecha, $filtradoPrograma);
?>
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="table-responsive">

            <table class="table table-sm" id="table-ventas">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Semana</th>
                        <th>Tipo de Venta</th>
                        <th>Fecha de Facturación</th>
                        <th>Num. PL</th>
                        <th>Num. Factura</th>

                        <th>Lote Temola</th>
                        <th>Programa</th>
                        <th>Set's Facturadas</th>
                        <th>Cueros Facturados</th>
                        <th>Set's Cortados Teseo</th>
                        <th>Área WB Real</th>
                        <th>Set's Totales Empacados</th>
                        <th class="table-success">Área WB X Set</th>
                        <th>Set's Totales Empacados+ Recuperación</th>
                        <th class="table-info">Área WB X Set</th>
                        <th>Set's Actuales</th>

                    </tr>
                </thead>
                <tbody>
                    <?php
                    $count = 0;
                    $suma_setsEmpacados = 0;
                    $suma_setsEmpacadosRecu = 0;
                    $suma_AreaRealWB = 0;

                    foreach ($Data as $key => $value) {
                        $count++;
                        $f_UnidadesFacturadas = formatoMil($Data[$key]['unidFacturadas'], 2);
                        $f_AreaRealWB = formatoMil($Data[$key]['areaWB'], 2);
                        $f_SetsEmpacados = formatoMil($Data[$key]['setsEmpacados'], 2);
                        $f_SetsEmpacadosRecu = formatoMil($Data[$key]['setsEmpacadosRecu'], 2);
                        $f_areaWBXSetsRecu = formatoMil($Data[$key]['areaWBXSetsRecu'], 2);
                        $f_areaWBXSets = formatoMil($Data[$key]['areaWBXSets'], 2);
                        $f_cuerosUsados = formatoMil($Data[$key]['tCuerosUsados'], 2);
                        $f_teseo = formatoMil($Data[$key]['setsCortadosTeseo'], 2);
                        $f_SetsActuales = formatoMil($Data[$key]['setsActuales'], 2);

                        $suma_AreaRealWB += $Data[$key]['areaWB'];
                        $suma_setsEmpacados += $Data[$key]['setsEmpacados'];
                        $suma_setsEmpacadosRecu += $Data[$key]['setsEmpacadosRecu'];

                        echo "<tr>
                        <td>$count</td>
                        <td>{$Data[$key]['semanaProduccion']}</td>
                        <td>{$Data[$key]['n_tipoVenta']}</td>
                        <td>{$Data[$key]['f_fechaFact']}</td>
                        <td>{$Data[$key]['numPL']}</td>
                        <td>{$Data[$key]['numFactura']}</td>

                        <td>{$Data[$key]['loteTemola']}</td>
                        <td>{$Data[$key]['nPrograma']}</td>
                        <td>{$f_UnidadesFacturadas}</td>
                        <td>{$f_cuerosUsados}</td>
                        <td>{$f_teseo}</td>

                        <td>{$f_AreaRealWB}</td>
                        <td>{$f_SetsEmpacados}</td>
                        <td class='table-success'>{$f_areaWBXSets}</td>
                        <td>{$f_SetsEmpacadosRecu}</td>
                        <td class='table-info'>{$f_areaWBXSetsRecu}</td>
                        <td>{$f_SetsActuales}</td>


                    </tr>";
                    }
                    $promAreaWB = $suma_setsEmpacados > 0 ? $suma_AreaRealWB / $suma_setsEmpacados : 0;
                    $promAreaWBRecu = $suma_setsEmpacadosRecu > 0 ? $suma_AreaRealWB / $suma_setsEmpacadosRecu : 0;

                    ?>

                </tbody>
                <tfoot>
                    <tr class="bg-TWM text-white">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>

                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>Valores Promedio:</td>
                        <td><?= formatoMil($suma_AreaRealWB, 2) ?></td>
                        <td><?= formatoMil($suma_setsEmpacados, 2) ?></td>
                        <td><?= formatoMil($promAreaWB, 2) ?></td>
                        <td><?= formatoMil($suma_setsEmpacadosRecu, 2) ?></td>
                        <td><?= formatoMil($promAreaWBRecu, 2) ?></td>
                        <td></td>


                    </tr>
                </tfoot>

            </table>
        </div>
    </div>
</div>
<script>
    $("#table-ventas").DataTable({
            dom: 'Bfrltip',
            scrollX: true,

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