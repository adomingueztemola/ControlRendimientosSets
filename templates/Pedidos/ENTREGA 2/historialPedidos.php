<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../../include/connect_mvc.php";
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
$filtradoProveedor = $proveedor != '' ? "p.idCatProveedor='$proveedor'" : "1=1";
$DataPedido = $obj_pedidos->getPedidos("p.estado!='1'", $filtradoFecha, $filtradoProveedor);
?>
<div class="table-responsive">
    <table id="table-pedidos" class="table table-sm">
        <thead>
            <tr class="">
                <th>#</th>
                <th>Núm. Fact.</th>
                <th>Proveedor</th>
                <th>Fecha Factura</th>
                <th>T.C.</th>
                <th>Estado</th>
                <th>Empleado Registro</th>
                <th>Fecha Registro</th>
                <th>Acciones</th>

            </tr>
        </thead>
        <tbody>
            <?php
            $count = 0;
          
            $suma_TC = 0;

            foreach ($DataPedido as $key => $value) {
                $count++;
               
                $suma_TC += $DataPedido[$key]['tc'];

                $btnLotes = "<button title='Ver Despliegue de Materia Prima' data-toggle='modal' data-target='#modalMP' onclick='cargaMP({$DataPedido[$key]['id']})' class='btn button btn-xs 
                 btn-outline-success'><i class='fas fa-boxes'></i></button>";
                $btnCancelar = ($DataPedido[$key]['estado'] != '0') ? "<button title='Cancelar Pedido' data-toggle='modal' data-target='#modalCancelar' onclick='cancelarPedido({$DataPedido[$key]['id']})' class='btn button btn-xs  btn-outline-danger'><i class='fas fa-times'></i></button>" : "";
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
                    <td>$<?= formatoMil($DataPedido[$key]['tc']) ?></td>
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

                <td>$<?= formatoMil($suma_TC > 0 ? $suma_TC / $count : 0) ?></td>
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