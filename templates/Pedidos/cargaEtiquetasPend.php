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


$obj_pedidos = new Pedido($debug, $idUser);


$filtradoEstado = "pd.id IS NULL";
$DataRendimiento = $obj_rendimiento->getRendimientosEtiquetas(
    "1=1",
    "1=1",
    "1=1",
    "r.estado>=2",
    $filtradoEstado
);
$DataRendimiento = !is_array($DataRendimiento) ? array() : $DataRendimiento;
if (count($DataRendimiento) > 0) {
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
                foreach ($DataRendimiento as $key => $value) {
                    $count++;
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

        </table>
    <?php
} else {
    echo "<p>Sin Lotes de Etiquetas/Calzado Pendientes.</p>";
} ?>

    </div>


    <script>
       $("#table-pedidos").DataTable();
        $('.dt-select2').select2();
        /***************** ELIMINAR EL RENDIMIENTO*********************/
        function guardarPedido(id) {
            idPedido = $("#pedido-" + id).val();
            $.post("../Controller/rendimientoEtiquetas.php?op=actualizarpedido", {
                    idPedido: idPedido,
                    id: id
                },
                function(respuesta) {
                    var resp = respuesta.split('|');
                    if (resp[0] == 1) {

                        setTimeout(() => {
                            notificaSuc(resp[1]);
                            cargaEtiquetas();

                        }, 1000);
                    } else {
                        notificaBad(resp[1]);

                    }
                });

        }
    </script>