<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once('../../include/connect_mvc.php');
include('../../assets/scripts/cadenas.php');

$debug = 0;
$idUser = $_SESSION['CREident'];
$id = !empty($_GET['id']) ? $_GET['id'] : '';
$stk = !empty($_GET['stk']) ? $_GET['stk'] : '0';
/***************CONSULTAR INFORMACION DEL LOTE INGRESADO***********/
$obj_reacondicionamiento = new Reacondicionamiento($debug, $idUser);
$Data = $obj_reacondicionamiento->getDetPzasRecup($id);
$Data = Excepciones::validaConsulta($Data);
$_12 = $Data['_12'] == '' ? '0' : ($Data['_12']);
$_3 = $Data['_3'] == '' ? '0' : ($Data['_3']);
$_6 = $Data['_6'] == '' ? '0' : ($Data['_6']);
$_9 = $Data['_9'] == '' ? '0' : ($Data['_9']);
$totalRecuperacion = $Data['totalRecuperacion'] == '' ? '0' : formatoMil($Data['totalRecuperacion'],0);

$_12Stk = $Data['_12Stk'] == '' ? '0' : ($Data['_12Stk']);
$_3Stk = $Data['_3Stk'] == '' ? '0' : ($Data['_3Stk']);
$_6Stk = $Data['_6Stk'] == '' ? '0' : ($Data['_6Stk']);
$_9Stk = $Data['_9Stk'] == '' ? '0' : ($Data['_9Stk']);

$f_12Stk = $Data['_12Stk'] == '' ? '0' : formatoMil($Data['_12Stk'],0);
$f_3Stk = $Data['_3Stk'] == '' ? '0' : formatoMil($Data['_3Stk'],0);
$f_6Stk = $Data['_6Stk'] == '' ? '0' : formatoMil($Data['_6Stk'],0);
$f_9Stk = $Data['_9Stk'] == '' ? '0' : formatoMil($Data['_9Stk'],0);
?>
<div class="row">
    <div class="col-lg-6 col-md-6 col-xs-6 col-sm-6">
        <h5>Total:<span id="total"><?= $totalRecuperacion ?></span></h5>
    </div>
    <div class="col-lg-6 col-md-6 col-xs-6 col-sm-6">
        <h5>En Scrap: <?= formatoMil($stk,0) ?></h5>
    </div>
</div>
<input type="hidden" name="total" id="total-inp" value="<?=$totalRecuperacion?>">
<input type="hidden" name="id" value="<?= $id ?>">

<div class="row">
    <div class="col-lg-12 col-md-12 col-xs-12 col-sm-12">
        <div class="alert alert-danger" role="alert" hidden id="alert-exceso">
            Exceso de Unidades, verifica tus unidades.
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12 col-md-12 col-xs-12 col-sm-12">
        <table class="table-responsive">
            <thead>
                <tr>
                    <th colspan='2'>Piezas</th>
                    <th>Stock</th>

                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>12:00</td>
                    <td><input class="form-control focusCampo sumatoria" type="number" value="<?= $_12 ?>" step="1" min="0" max="<?=$_12Stk?>" name="_12"></td>
                    <td><?=$f_12Stk?></td>
                </tr>
                <tr>
                    <td>03:00</td>
                    <td><input class="form-control focusCampo sumatoria" type="number"  value="<?= $_3 ?>" step="1" min="0" max="<?=$_3Stk?>" name="_3"></td>
                    <td><?=$f_3Stk?></td>
                </tr>
                <tr>
                    <td>06:00</td>
                    <td><input class="form-control focusCampo sumatoria" type="number" value="<?= $_6 ?>" step="1" min="0" max="<?=$_6Stk?>" name="_6"></td>
                    <td><?=$f_6Stk?></td>
                </tr>
                <tr>
                    <td>09:00</td>
                    <td><input class="form-control focusCampo sumatoria" type="number" value="<?= $_9 ?>" step="1" min="0" max="<?=$_9Stk?>" name="_9"></td>
                    <td><?=$f_9Stk?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<script src='../assets/scripts/clearData.js'></script>
<script>
    //Recorrer los inputs para validar la cantidad de los inputs
    $(".sumatoria").change(function() {
        let result = 0;
        $(".sumatoria").each(function() {
            if(parseInt($(this).prop("max"))<parseInt($(this).val())){
                notificaBad("Error, cantidad excede cantidad en el stock. Verifique su cantidad");
            }
            result = parseFloat(result) + parseFloat($(this).val());
        });
        verificaTotales(result)
        $("#total").text(result);
        $("#total-inp").val(result);

    });

    function verificaTotales(conteo) {
        conteo = parseInt(conteo);
        stk = parseInt(<?= $stk ?>);
        if (stk < conteo) {
            $("#alert-exceso").prop('hidden', false);
            $("#btn-guardarpzas").prop('hidden', true);
        } else {
            $("#alert-exceso").prop('hidden', true);
            $("#btn-guardarpzas").prop('hidden', false);
        }
    }
</script>