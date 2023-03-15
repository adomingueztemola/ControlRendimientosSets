<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../../include/connect_mvc.php";
include('../../Models/Mdl_ConexionBD.php');
include('../../Models/Mdl_Pedido.php');
include('../../assets/scripts/cadenas.php');

$debug = 0;
$idUser = $_SESSION['CREident'];
$obj_pedidos = new Pedido($debug, $idUser);
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}
$date_start = !empty($_POST['date-start']) ? $_POST['date-start'] :date("01/m/Y");
$date_end = !empty($_POST['date-end']) ? $_POST['date-end'] : date("t/m/Y");
$proveedor = !empty($_POST['proveedor']) ? $_POST['proveedor'] : '';
$pedidos = !empty($_POST['pedidos']) ? $_POST['pedidos'] : '';
/***************** CASTEO DE FECHAS ****************** */
if ($date_start != '' and $date_end != '') {
    $date_start = date("Y-m-d", strtotime(str_replace("/", "-", $date_start)));
    $date_end = date("Y-m-d", strtotime(str_replace("/", "-", $date_end)));
    $filtradoFecha = "p.fechaFactura BETWEEN '$date_start' AND '$date_end'";

}else{
    $filtradoFecha="1=1";
}

$filtradoPedidos = "1=1";
$filtradoPedidos = $pedidos == '1' ? "p.cuerosXUsar>0" : $filtradoPedidos;
$filtradoPedidos = $pedidos == '2' ? "p.cuerosXUsar<=0" : $filtradoPedidos;


$filtradoProveedor = $proveedor != '' ? "p.idCatProveedor='$proveedor'" : "1=1";




$DataPedido = $obj_pedidos->getPedidos("p.estado!='1'", $filtradoFecha, $filtradoProveedor, $filtradoPedidos);
?>
<div class="table-responsive">
    <table id="table-pedidos" class="table table-sm">
        <thead>
            <tr class="">
                <th>#</th>
                <th>Núm. Fact.</th>
                <th>Proveedor</th>
                <th>Fecha Factura</th>
                <th>Materia Prima</th>
                <th>Precio Unit. Fact. Prov. (pesos)</th>
                <th>T.C.</th>
                <th>Precio Unit. Fact. Prov. (USD)</th>
                <th>Total de Cueros Facturados</th>
                <th>Área Prov. Pie<sup>2</sup></th>
                <th>Área WB Prom. Fact. Prov.</th>
                <th>Cueros Disponibles</th>
                <th>Estado</th>
                <th>Empleado Registro</th>
                <th>Fecha Registro</th>
                <th>Acciones</th>

            </tr>
        </thead>
        <tbody>
            <?php
            $count = 0;
            $suma_PrecioUnitFactProvMXN = 0;
            $suma_PrecioUnitFactProvUSD = 0;
            $suma_TotalCuerosFacturados = 0;
            $suma_AreaPrvPie = 0;
            $suma_AreaWBProm = 0;
            $suma_TC = 0;

            foreach ($DataPedido as $key => $value) {
                $count++;
                $suma_PrecioUnitFactProvMXN += $DataPedido[$key]['precioUnitFactPesos'];
                $suma_PrecioUnitFactProvUSD += $DataPedido[$key]['precioUnitFactUsd'];
                $suma_TotalCuerosFacturados += $DataPedido[$key]['totalCuerosFacturados'];
                $suma_AreaPrvPie += $DataPedido[$key]['areaProvPie2'];
                $suma_AreaWBProm += $DataPedido[$key]['areaWBPromFact'];
                $suma_TC += $DataPedido[$key]['tc'];
                $suma_Cueros += $DataPedido[$key]['cuerosXUsar'];

                $colorDisp = $DataPedido[$key]['cuerosXUsar'] <= '0' ? 'table-danger' : '';
                $btnLotes = $DataPedido[$key]['_uso'] == '1' ? "<button title='Ver Despliegue de Lotes' data-toggle='modal' data-target='#modalLotes' onclick='cargaLotes({$DataPedido[$key]['id']})' class='btn button btn-xs 
                 btn-outline-success'><i class='fas fa-boxes'></i></button>" : "";
                $btnCancelar = ($DataPedido[$key]['_uso'] == '0' and $DataPedido[$key]['estado'] != '0') ? "<button title='Cancelar Pedido' data-toggle='modal' data-target='#modalCancelar' onclick='cancelarPedido({$DataPedido[$key]['id']})' class='btn button btn-xs  btn-outline-danger'><i class='fas fa-times'></i></button>" : "";
                $colorTable = $DataPedido[$key]['estado'] == '0' ? 'table-danger' : '';
                $estado = "N/A";
                switch ($DataPedido[$key]['estado']) {
                    case '2':
                        $estado = "<b>Almacenado</b>";
                        break;
                    case '0':
                        $estado = "<b  data-toggle='tooltip' data-placement='top' title='{$DataPedido[$key]['motivoCancelacion']}'>Cancelado</b>";

                        break;
                }
            ?>
                <tr class="<?= $colorTable ?>">
                    <td><?= $count ?></td>
                    <td><?= $DataPedido[$key]['numFactura'] ?></td>
                    <td><?= $DataPedido[$key]['nameProveedor'] ?></td>
                    <td><?= $DataPedido[$key]['f_fechaFactura'] ?></td>
                    <td><?= $DataPedido[$key]['n_materia'] ?></td>
                    <td>$<?= formatoMil($DataPedido[$key]['precioUnitFactPesos']) ?></td>
                    <td>$<?= formatoMil($DataPedido[$key]['tc']) ?></td>
                    <td><?= formatoMil($DataPedido[$key]['precioUnitFactUsd']) ?> USD</td>
                    <td><?= formatoMil($DataPedido[$key]['totalCuerosFacturados']) ?></td>
                    <td><?= formatoMil($DataPedido[$key]['areaProvPie2']) ?></td>
                    <td><?= formatoMil($DataPedido[$key]['areaWBPromFact']) ?></td>
                    <td class="<?= $colorDisp ?>"><?= formatoMil($DataPedido[$key]['cuerosXUsar']) ?></td>
                    <td><?= $estado ?></td>
                    <td><?= $DataPedido[$key]['str_usuario'] ?></td>
                    <td><?= $DataPedido[$key]['f_fechaReg'] ?></td>
                    <td><?= $btnLotes ?> <?= $btnCancelar ?></td>



                </tr>
            <?php


            }
            ?>

        </tbody>
        <tfoot>
            <tr class="bg-TWM text-white">
                <td>Totales:</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>

                <td>$<?= formatoMil($suma_PrecioUnitFactProvMXN > 0 ? $suma_PrecioUnitFactProvMXN / $count : 0) ?></td>
                <td>$<?= formatoMil($suma_TC > 0 ? $suma_TC / $count : 0) ?></td>
                <td><?= formatoMil($suma_PrecioUnitFactProvUSD) ?> USD</td>
                <td><?= formatoMil($suma_TotalCuerosFacturados) ?></td>
                <td><?= formatoMil($suma_AreaPrvPie) ?></td>
                <td><?= formatoMil($suma_AreaWBProm > 0 ? $suma_AreaWBProm / $count : 0) ?></td>
                <td><?= formatoMil($suma_Cueros) ?></td>
                <td>-</td>
                <td>-</td>

                <td>-</td>
                <td>-</td>




            </tr>
        </tfoot>
    </table>
</div>


<script>
    $("#table-pedidos").DataTable({
            dom: 'Bfrltip',
            "aaSorting": [],

            drawCallback: function() {
                $('[data-toggle="tooltip"]').tooltip();
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