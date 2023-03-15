<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../../include/connect_mvc.php";
include('../../Models/Mdl_ConexionBD.php');
include('../../Models/Mdl_Rendimiento.php');
include('../../assets/scripts/cadenas.php');
include('../../Models/Mdl_Pedido.php');

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
$proceso = !empty($_POST['proceso']) ? $_POST['proceso'] : '';
$programa = !empty($_POST['programa']) ? $_POST['programa'] : '';
$materia = !empty($_POST['materia']) ? $_POST['materia'] : '';
$estado = !empty($_POST['estado']) ? $_POST['estado'] : '';

$obj_pedidos = new Pedido($debug, $idUser);

/***************** CASTEO DE FECHAS ****************** */

$date_start = date("Y-m-d", strtotime(str_replace("/", "-", $date_start)));
$date_end = date("Y-m-d", strtotime(str_replace("/", "-", $date_end)));
$filtradoEstado="1=1";
$filtradoFecha = "r.fechaFinal BETWEEN '$date_start' AND '$date_end'";
$filtradoProceso = $proceso != '' ? "r.idCatProceso='$proceso'" : "1=1";
$filtradoPrograma = $programa != '' ? "r.idCatPrograma='$programa'" : "1=1";
$filtradoMateria = $materia != '' ? "r.idCatMateriaPrima='$materia'" : "1=1";

$filtradoEstado= $estado=='1'?"pd.id IS NOT NULL":$filtradoEstado;

$filtradoEstado= $estado=='2'?"pd.id IS NULL":$filtradoEstado;
$filtradoEstado= $estado==''?"1=1":$filtradoEstado;
$DataRendimiento = $obj_rendimiento->getRendimientosEtiquetas(
    $filtradoFecha,
    $filtradoPrograma,
    $filtradoMateria,
    "r.estado>=2",
    $filtradoEstado
);
?>
<div class="table-responsive">
    <table id="table-pedidos" class="table table-sm">
        <thead>
            <tr class="">
                <th>#</th>
                <th>Fecha Final</th>
                <th>Semana</th>
                <th>Lote</th>
                <th>1s</th>
                <th>2s</th>
                <th>3s</th>
                <th>4s</th>
                <th>Total</th>
                <th>Programa</th>
                <th>Materia Prima</th>
                <th>Proveedor</th>
                <th>Tipo de Venta</th>

                <th>Num. Fact Pedido</th>
                <th>Acción</th>


            </tr>
        </thead>
        <tbody>
            <?php
            $count = 0;
            $suma_areaWB = 0;
            $suma_promedioWB = 0;
            $suma_pzasRechazo = 0;
            $suma_areaPzasRechazo = 0;
            $suma_areaFinal = 0;
            $suma_perdidaAreaWBTerm = 0;
            $suma_costoXft2 = 0;
            $suma_1s = 0;
            $suma_2s = 0;
            $suma_3s = 0;
            $suma_4s = 0;
            $suma_total = 0;
            foreach ($DataRendimiento as $key => $value) {
                $count++;

                //Area de Espera de llenado
                $promedioAreaWB = formatoMil($DataRendimiento[$key]['promedioAreaWB']);
                $suma_areaWB += $DataRendimiento[$key]['areaWB'];
                $suma_promedioWB += $DataRendimiento[$key]['promedioAreaWB'];
                $suma_pzasRechazo += $DataRendimiento[$key]['piezasRechazadas'];
                $suma_areaPzasRechazo += $DataRendimiento[$key]['areaPzasRechazo'];
                $suma_areaFinal += $DataRendimiento[$key]['areaFinal'];
                $suma_perdidaAreaWBTerm += $DataRendimiento[$key]['perdidaAreaWBTerm'];
                $suma_costoXft2 += $DataRendimiento[$key]['costoXft2'];
                $suma_1s += $DataRendimiento[$key]['1s'];
                $suma_2s += $DataRendimiento[$key]['2s'];
                $suma_3s += $DataRendimiento[$key]['3s'];
                $suma_4s += $DataRendimiento[$key]['4s'];
                $suma_total += $DataRendimiento[$key]['total_s'];

                //Mensaje de Piezas 
                $fto = formatoMil($DataRendimiento[$key]['piezasRechazadas']);
                $comentarios_rechazo = $DataRendimiento[$key]['piezasRechazadas'] > 0 ?
                    "<label data-toggle='popover' title='Comentarios del Rechazo' data-content='{$DataRendimiento[$key]['comentariosRechazo']}'>{$fto}</label>" : $fto;
                $btnEditar = $DataRendimiento[$key]['estado'] == '2' ? '<button title="Guardar Pedido" onclick="guardarPedido(' . $DataRendimiento[$key]['id'] . ')"
                                                                         class="button btn btn-xs btn-outline-primary"><i class="fas fa-unlock-alt"></i></button>' : '';
                $c_pedidos = "";
                $DataPedidos = $obj_pedidos->getPedidosDisp($DataRendimiento[$key]['idCatMateriaPrima']);
                foreach ($DataPedidos as $i => $value) {
                    if ($DataPedidos[$i]['idCatProveedor'] == $DataRendimiento[$key]['idCatProveedor'] AND $DataPedidos[$i]['cuerosXUsar']>=$DataRendimiento[$key]['total_s'] AND 
                        $DataPedidos[$i]['idCatMateriaPrima'] == $DataRendimiento[$key]['idCatMateriaPrima']) {
                        $c_pedidos .= "<option data-disponibles='{$DataPedidos[$i]['cuerosXUsar']}' value='{$DataPedidos[$i]['id']}'>{$DataPedidos[$i]['numFactura']} - {$DataPedidos[$i]['nameProveedor']}</option>";
                    }
                }


                $numFactPedido = $DataRendimiento[$key]['estado'] == '2' ? ' <select name="pedido" id="pedido-' . $DataRendimiento[$key]['id'] . '" class="form-control dt-select2" required style="width:100%">
                <option value="">Selecciona Factura del Pedido</option>' . $c_pedidos . "</select>" : $DataRendimiento[$key]['numFactura'];

            ?>
                <tr class="<?= $colorTable ?>">
                    <td><?= $count ?></td>
                    <td><?= $DataRendimiento[$key]['f_fechaFinal'] ?></td>
                    <td><?= $DataRendimiento[$key]['semanaProduccion'] ?></td>

                    <td><?= $DataRendimiento[$key]['loteTemola'] ?></td>
                    <td><?= formatoMil($DataRendimiento[$key]['1s']) ?></td>
                    <td><?= formatoMil($DataRendimiento[$key]['2s']) ?></td>
                    <td><?= formatoMil($DataRendimiento[$key]['3s']) ?></td>
                    <td><?= formatoMil($DataRendimiento[$key]['4s']) ?></td>
                    <td><?= formatoMil($DataRendimiento[$key]['total_s']) ?></td>

                    <td><small><?= $DataRendimiento[$key]['n_programa'] ?></small></td>
                    <td><small><?= $DataRendimiento[$key]['n_materia'] ?></small></td>
                    <td><?= $DataRendimiento[$key]['n_proveedor'] ?></td>

                    <td><?= $DataRendimiento[$key]['n_tipoventa'] ?></td>
                    <td> <?= $numFactPedido ?></td>

                    <td><?= $btnEditar ?></td>



                </tr>
            <?php


            }
            ?>

        </tbody>
        <tfoot>
            <tr class="bg-TWM text-white">
                <td>Totales:</td>

                <td></td>
                <td></td>
                <td></td>

                <td><?= formatoMil($suma_1s) ?></td>
                <td><?= formatoMil($suma_2s) ?></td>
                <td><?= formatoMil($suma_3s) ?></td>
                <td><?= formatoMil($suma_4s) ?></td>
                <td><?= formatoMil($suma_total) ?></td>

                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>




            </tr>
        </tfoot>
    </table>
</div>


<script>
    $("#table-pedidos").DataTable({
            dom: 'Bfrltip',
            autoWidth: false,
            drawCallback: function() {
                $('.dt-select2').select2();
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