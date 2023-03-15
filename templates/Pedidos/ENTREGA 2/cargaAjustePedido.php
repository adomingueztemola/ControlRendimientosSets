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
?>
<div class="row">
    <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
        <div class="card-header">
            Cantidad de Cueros en Pedido: <?=formatoMil($DataDetPedido["totalCuerosFacturados"])?>
        </div>
    </div>
</div>
<div class="row">
    <input type="hidden" name="idPedido" id="idPedido" value="<?= $id ?>">
    <div class="col-lg-12 col-md-12 col-xs-12 col-sm-12">
        <div class="form-check form-check-inline">
            <input class="form-check-input" onchange="ocultarNotaCredito(this)" type="radio" name="tipo" id="aumento" value="1">
            <label class="form-check-label" for="aumento">Aumento</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" onchange="verNotaCredito(this)" type="radio" name="tipo" id="disminucion" value="2">
            <label class="form-check-label" for="disminucion">Disminución</label>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <label for="cantidad" class="form-label required">Cantidad</label>
        <input type="number" step="0.01" name="cantidad" id="cantidad" class="form-control" data-max="<?= $cuerosXUsar ?>" min='1' required>
    </div>
</div>

<div class="row" hidden id="divNotaCredito">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <label for="notaCredito">Nota de Crédito (Opcional)</label>
        <input disabled type="text" name="notaCredito" id="notaCredito" class="form-control">
    </div>
</div>

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <label for="motivo" class="form-label required">Motivo</label>
        <textarea name="motivo" id="" required cols="30" class="form-control" rows="5"></textarea>
    </div>
</div>