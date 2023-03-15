<?php
$debug = 0;
session_start();
define('INCLUDE_CHECK', 1);
require_once "../../include/connect_mvc.php";
include('../../Models/Mdl_ConexionBD.php');
include('../../Models/Mdl_Venta.php');
include('../../assets/scripts/cadenas.php');

$idUser = $_SESSION['CREident'];
if ($debug == 1) {
    print_r($_GET);
    //  exit(0);
} else {
    error_reporting(0);
}
$id = !empty($_GET['id']) ? $_GET['id'] : '';
if ($id == '') {
    echo '<div class="alert alert-danger" role="alert">
    No se reconoció el detallado de venta, vuelve a intentarlo si el problema persiste notifica a Sistemas.</div>';
    exit(0);
}
$obj_ventas = new Venta($debug, $idUser);
$DataVentas = $obj_ventas->getClasifXVenta($id);

$_1s = $DataVentas[0]['1s'] != '' ? formatoMil($DataVentas[0]['1s'], 0) : '0';
$_2s = $DataVentas[0]['2s'] != '' ?  formatoMil($DataVentas[0]['2s'], 0) : '0';
$_3s = $DataVentas[0]['3s'] != '' ?  formatoMil($DataVentas[0]['3s'], 0) : '0';
$_4s = $DataVentas[0]['4s'] != '' ?  formatoMil($DataVentas[0]['4s'], 0) : '0';
$_20 = $DataVentas[0]['_20'] != '' ?  formatoMil($DataVentas[0]['_20'], 0) : '0';

$total_s = $DataVentas[0]['total_s'] != '' ?  formatoMil($DataVentas[0]['total_s'], 0) : '0';
$piezasRechazadas = $DataVentas[0]['piezasRechazadas'] != '' ?  formatoMil($DataVentas[0]['piezasRechazadas'], 0) : '0';

$readonly = $DataVentas[0]['tipoMateriaPrima'] == 2 ? 'readonly' : '';
$c_sumatoria_s = $DataVentas[0]['tipoMateriaPrima'] == 1 ? "sumatoria_s" : "";

$DataClasifVentas = $obj_ventas->getTotalClasifUtilizado($DataVentas[0]['idRendimiento']);

$t_1s = $DataClasifVentas['t_1s'] != '' ? formatoMil($DataVentas[0]['1sRend'] - $DataClasifVentas['t_1s'], 0) : '0';
$t_2s = $DataClasifVentas['t_2s'] != '' ?  formatoMil($DataVentas[0]['2sRend'] - $DataClasifVentas['t_2s'], 0) : '0';
$t_3s = $DataClasifVentas['t_3s'] != '' ?  formatoMil($DataVentas[0]['3sRend'] - $DataClasifVentas['t_3s'], 0) : '0';
$t_4s = $DataClasifVentas['t_4s'] != '' ?  formatoMil($DataVentas[0]['4sRend'] - $DataClasifVentas['t_4s'], 0) : '0';
$t_20 = $DataClasifVentas['t_20'] != '' ?  formatoMil($DataVentas[0]['_20Rend'] - $DataClasifVentas['t_20'], 0) : '0';

$t_total_s = $DataClasifVentas['t_total_s'] != '' ?  formatoMil($DataVentas[0]['totalRend'] - $DataClasifVentas['t_total_s'], 0) : '0';
?>

<div class="row mt-2">
    <div class="col-md-12">
        <table class="table table-sm">
            <tbody>
                <?php if ($DataVentas[0]['tipoMateriaPrima'] == 2) {
                ?>

                    <tr>
                        <td class="bg-TWM text-white">1s</td>
                        <td>
                            <input type="number" name="1s" id="1s" step="1" required value="<?= $_1s ?>" class="form-control sumatoria_s">
                        </td>
                        <td class="text-danger">
                            <?= $t_1s ?>

                        </td>
                       <!--  <td>
                            <?= $DataVentas[0]['1sRend'] ?>

                        </td>-->

                    </tr>
                    <tr>
                        <td class="bg-TWM text-white">2s</td>
                        <td>
                            <input type="number" name="2s" id="2s" step="1" required value="<?= $_2s ?>" class="form-control sumatoria_s">
                        </td>
                        <td class="text-danger">
                            <?= $t_2s ?>

                        </td>
                    <!--    <td>
                            <?= $DataVentas[0]['2sRend'] ?>
                        </td>-->

                    </tr>
                    <tr>
                        <td class="bg-TWM text-white">3s</td>
                        <td>
                            <input type="number" name="3s" id="3s" step="1" required value="<?= $_3s ?>" class="form-control sumatoria_s">
                        </td>
                        <td class="text-danger">
                            <?= $t_3s ?>

                        </td>
                        <!-- <td>
                            <?= $DataVentas[0]['3sRend'] ?>
                        </td>-->

                    </tr>
                    <tr>
                        <td class="bg-TWM text-white">4s</td>
                        <td>
                            <input type="number" name="4s" id="4s" step="1" required value="<?= $_4s ?>" class="form-control sumatoria_s">
                        </td>
                        <td class="text-danger">
                            <?= $t_4s ?>
                        </td>
                       <!--  <td>
                            <?= $DataVentas[0]['4sRend'] ?>
                        </td>-->

                    </tr>
                    <tr>
                        <td class="bg-TWM text-white">20</td>
                        <td>
                            <input type="number" name="_20" id="_20" step="1" required value="<?= $_20 ?>" class="form-control sumatoria_s">
                        </td>
                        <td class="text-danger">
                            <?= $t_20 ?>
                        </td>
                       <!--  <td>
                            <?= $DataVentas[0]['_20Rend'] ?>
                        </td>-->

                    </tr>
                <?php }
                ?>
                <tr>
                    <td class="bg-TWM text-white">Piezas Rechazadas</td>
                    <td class="text-danger" colspan="">
                    <input type="number" disabled value="<?= $piezasRechazadas ?>" class="form-control">
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <td class="bg-TWM text-white">Total</td>
                    <td>
                        <input type="number" <?= $readonly ?> name="Total" id="total_s" step="1" value="<?= $total_s ?>" required class="form-control <?= $c_sumatoria_s ?>">
                        <span id="aviso-valores" class="text-danger" hidden>El total sobrepasa a los <span id="cant-disponible"></span> disponibles de tu pedido.</span>
                    </td>
                    <td class="text-danger">
                        <?= $t_total_s ?>
                    </td>
                  <!--  <td>
                        <?= $DataVentas[0]['totalRend'] ?>
                    </td>-->

                </tr>
            </tbody>
        </table>

    </div>
</div>
<script>
    $(".sumatoria_s").change(function() {
        let result = 0;
        $(".sumatoria_s").each(function() {
            guardarClasif(parseFloat($(this).val()), $(this).prop("id"));
            result = parseFloat(result) + parseFloat($(this).val());
        });
        $("#total_s").val(result);
        guardarClasif(parseFloat($("#total_s").val()), "total_s");

    });

    function guardarClasif(cant, tipo) {
        $.ajax({
            url: '../Controller/ventas.php?op=guardarclasif',
            data: {
                codigo: tipo,
                cant: cant,
                id: <?= $id ?>
            },
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    // notificaSuc(resp[1])

                } else if (resp[0] == 0) {
                    notificaBad(resp[1])
                }
            },
            beforeSend: function() {}
        });

    }
</script>