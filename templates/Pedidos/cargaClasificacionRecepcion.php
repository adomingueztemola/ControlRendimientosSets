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

$id = !empty($_GET['id']) ? $_GET['id'] : '';
if ($id == '') {
    echo "<div class='alert alert-warning' role='alert'>
        No se encontró el pedido, vuelve a intentarlo, si el problema persiste consulta al departamento de sistemas.
      </div>";
}
$DataDetPedido = $obj_pedidos->getDetMP($id);
$totalCuerosEntregados = $DataDetPedido["totalCuerosEntregados"];
?>
<input type="hidden" name="id" value="<?= $id ?>">

<input type="hidden" id="totalCuerosEntregados" value="<?= $totalCuerosEntregados ?>">
<div class="row">
    <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
        <div class="card-header">
            Cantidad de Cueros Recibidos: <?= formatoMil($DataDetPedido["totalCuerosEntregados"]) ?>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12 col-md-12 col-xs-12 col-sm-12">
        <table class="table table-sm table-bordered">
            <thead>
                <tr>
                    <th>1s</th>
                    <th>2s</th>
                    <th>3s</th>
                    <th>4s</th>
                    <th class="table-danger">20</th>

                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input class="form-control sumatoria_s clasifNormal" name="_1s" id="_1s" value="0" type="number" step='1'></td>
                    <td><input class="form-control sumatoria_s clasifNormal" name="_2s" id="_2s" value="0" type="number" step='1'></td>
                    <td><input class="form-control sumatoria_s clasifNormal" name="_3s" id="_3s" value="0" type="number" step='1'></td>
                    <td><input class="form-control sumatoria_s clasifNormal" name="_4s" id="_4s" value="0" type="number" step='1'></td>
                    <td class="table-danger"><input readonly class="form-control sumatoria_s scrap" name="_20" id="_20" value="0" type="number" step='1'></td>

                </tr>
            </tbody>
        </table>
        <div class="card-header">
            Cantidad de Cueros Clasificados: <span id="Total"><?= formatoMil($DataDetPedido["totalCuerosXUsar"]) ?></span>
        </div>
    </div>
</div>
<script>
    /******************* SUMATORIA DE TOTALES 'S'*******************/
    $(".sumatoria_s").change(function() {
        let result = 0;
        let resultclasif = 0;
        let totalCuerosEntregados = $("#totalCuerosEntregados").val();

        $(".sumatoria_s").each(function() {
            result = parseFloat(result) + parseFloat($(this).val());
            if ($(this).hasClass("clasifNormal")) {
                resultclasif = parseFloat(resultclasif) + parseFloat($(this).val());
            }
        });
        if(totalCuerosEntregados<resultclasif){
            notificaBad("Revise su clasificación, excede el total de cueros recibidos.");
        }
        $("#_20").val(totalCuerosEntregados - resultclasif);

        $("#Total").text(totalCuerosEntregados);
    });
</script>