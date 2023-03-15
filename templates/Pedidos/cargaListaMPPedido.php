<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../../include/connect_mvc.php";
include('../../assets/scripts/cadenas.php');

$debug = 0;
$idUser = $_SESSION['CREident'];
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}
$obj_pedidos = new Pedido($debug, $idUser);
$id = !empty($_GET['id']) ? $_GET['id'] : '';

?>
<div class="row mt-2">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <table class="table table-sm table-bordered">
            <thead>
                <tr>
                    <td>#</td>
                    <td>Materia Prima</td>
                    <td>Precio USD</td>
                    <td>Precio MXN</td>
                    <td>Total Cueros</td>
                    <td>Área Proveedor Pie<sup>2</sup></td>
                    <td>Área WB Promedio</td>
                    <td>Acciones</td>

                </tr>
            </thead>
            <tbody>
                <?php
                $Data = $obj_pedidos->getListaMPXPedido($id);
                $Data = Excepciones::validaConsulta($Data);
                $count = 1;
                if(count($Data)<=0){
                    echo "<tr>
                    <td colspan='8' class='text-center'>Sin Materia Prima Registrada</td>
                    </tr>";
                }
                foreach ($Data as  $value) {
                    $fPrecioUSD = formatoMil($value['precioUnitFactUsd']);
                    $fPrecioMXN = formatoMil($value['precioUnitFactPesos']);
                    $totalCueros = formatoMil($value['totalCuerosFacturados']);
                    $areaWB = formatoMil($value['areaWBPromFact']);
                    $areaProv = formatoMil($value['areaProvPie2']);

                    echo "<tr>
                        <td>{$count}</td>
                        <td><small>{$value['nMateria']}</small></td>
                        <td>{$fPrecioUSD}</td>
                        <td>{$fPrecioMXN}</td>
                        <td>{$totalCueros}</td>
                        <td>{$areaProv}</td>
                        <td>{$areaWB}</td>
                        <td class=''>
                        <div id='bloqueo-btn-mp{$value['id']}' style='display:none'>
                        <button class='btn btn-TWM' type='button' disabled='>
                            <span class='spinner-border spinner-border-sm' role='status' aria-hidden='true'></span>
                        </button>

                    </div>
                    <div id='desbloqueo-btn-mp{$value['id']}'>
                           <button class='btn btn-xs btn-danger' onclick='eliminarMP({$value['id']})'><i class='fas fa-trash-alt'></i></button>
                           </div>
                        </td>
                    </tr>";
                    $count++;
                }

                ?>

            </tbody>
        </table>
    </div>
</div>

<script>
    function eliminarMP(idMP) {
        $.ajax({
            url: '../Controller/pedidos.php?op=eliminarmp',
            data: {
                id: idMP
            },
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                    bloqueoBtn("bloqueo-btn-mp" + idMP, 2)
                    actualizarLista();


                } else if (resp[0] == 0) {
                    notificaBad(resp[1])
                    bloqueoBtn("bloqueo-btn-mp" + idMP, 2)


                }
            },
            beforeSend: function() {
                bloqueoBtn("bloqueo-btn-mp" + idMP, 1)
            }

        });
    }
</script>