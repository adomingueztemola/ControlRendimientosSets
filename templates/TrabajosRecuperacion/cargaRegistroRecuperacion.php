<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once('../../include/connect_mvc.php');
$debug = 0;
$idUser = $_SESSION['CREident'];
$obj_trabajos = new TrabajosRecupera($debug, $idUser);
$obj_programas = new Programa($debug, $idUser);
?>
<div class="row">
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <label class="form-label required" for="fecha">Fecha Inicio</label>
        <input type="date" class="form-control" required value="<?= date("Y-m-d") ?>" name="fecha" id="fecha">
    </div>
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <label class="form-label required" for="fechaEntrega">Fecha Entrega</label>
        <input type="date" class="form-control" required value="<?= date("Y-m-d") ?>" name="fechaEntrega" id="fechaEntrega">
    </div>
</div>
<div class="row  m-2 card-header">
    <div class="col-md-12">
        <div class="form-check form-check-inline">
            <input class="form-check-input" onchange="cambiaLoteInicial()" type="radio" checked name="tipoLoteInicio" id="loteRegistrado" value="1">
            <label class="form-check-label" for="loteRegistrado">Lote Registrado</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" onchange="cambiaLoteInicial()" type="radio" name="tipoLoteInicio" id="loteNoIdentificado" value="2">
            <label class="form-check-label" for="loteNoIdentificado">Lote No Identificado</label>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <label class="form-label required" id="lbl-RendInicio" for="idRendInicio">Lote Retrabajado</label>
        <?php
        $Data = $obj_trabajos->getLotesCerrados();
        $Data = Excepciones::validaConsulta($Data);
        ?>
        <select name="idRendInicio" style="width:100%" id="idRendInicio" required onchange="seleccionRendInicio(this)" class="form-control select2">
            <option value="">Selecciona Lote</option>
            <?php
            foreach ($Data as $key => $value) {
                echo "<option data-programa='{$Data[$key]['idCatPrograma']}' data-pzas='{$Data[$key]['pzasTotales']}' 
                value='{$Data[$key]['id']}'>{$Data[$key]['loteTemola']}</option>";
            }

            ?>
        </select>
        <input type="text" name="nameLote" id="nameLote" hidden class="form-control">
    </div>
    <div class="col-md-6">
        <label class="form-label required" for="totalRecuperado">Total</label>
        <input type="number" name="totalRecuperado" step="1" min="1" onchange="" id="totalRecuperado" class="form-control">
        <!--actualizaRangos();calculaPerdida('totalInicial', 'totalRecuperado');-->
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <label class="form-label required" for="idCatPrograma">Programa</label>
        <?php
        $Data = $obj_programas->getPrograma("p.estado='1'");
        $Data = Excepciones::validaConsulta($Data);
        ?>
        <select name="idCatPrograma" style="width:100%" id="idCatPrograma" class="form-control select2">
            <option value="">Selecciona Programa</option>

            <?php
            foreach ($Data as $key => $value) {
                echo "<option value='{$Data[$key]['id']}'>{$Data[$key]['nombre']}</option>";
            }

            ?>
        </select>
    </div>
</div>
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <label class="form-label required" for="idRendRecuperado">Lote Recuperado</label>
        <?php
        $Data = $obj_trabajos->getLotesSetsDisponibles();
        $Data = Excepciones::validaConsulta($Data);
        ?>
        <select name="idRendRecuperado" style="width:100%" id="idRendRecuperado" class="form-control select2">
            <option value="">Selecciona Lote</option>
            <?php
            foreach ($Data as $key => $value) {
                echo "<option value='{$Data[$key]['id']}'>{$Data[$key]['loteTemola']}</option>";
            }

            ?>
        </select>
    </div>
    <!-- <div class="col-md-6">
        <label class="form-label required" for="totalRecuperado">Total Recuperación</label>
        <input type="number" step="1" min="1" onchange="calculaPerdida('totalInicial', 'totalRecuperado')" name="totalRecuperado" id="totalRecuperado" class="form-control">
        <small id="subtitle-perdida" class="badge badge-danger badge-danger form-text text-white float-left">Pérdida</small>
        <small id="porcentaje-perdida"></small>
    </div>-->
</div>
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <label for="trabajadorRecibio">Trabajador Recibió</label>
        <?php
        $Data = $obj_trabajos->getTrabajadores();
        $Data = Excepciones::validaConsulta($Data);
        ?>
        <input type="hidden" name="nombreTrabajador" id="nombreTrabajador">
        <select name="trabajadorRecibio" style="width:100%" id="trabajadorRecibio" onchage="cambiarNombreTrabajador(this)" class="form-control select2">
            <option value="">Selecciona Trabajador</option>
            <option value="99">CALIDAD</option>
            <?php
            foreach ($Data as $key => $value) {
                echo "<option value='{$Data[$key]['noTrabajador']}|{$Data[$key]['nombreCompletoTrabajador']}'>{$Data[$key]['noTrabajador']} - {$Data[$key]['nombreCompletoTrabajador']}</option>";
            }

            ?>
        </select>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <label class="form-label" for="defecto">Defecto (Opcional)</label>
        <?php
        $Data = $obj_trabajos->getDefectos();
        $Data = Excepciones::validaConsulta($Data);
        ?>
        <select name="defecto" style="width:100%" id="defecto" class="form-control select2">
            <option value="">Selecciona Defecto</option>
            <?php
            foreach ($Data as $key => $value) {
                echo "<option value='{$Data[$key]['id']}'>{$Data[$key]['nombre']}</option>";
            }

            ?>
        </select>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <label class="form-label" for="observaciones">Observaciones</label>
        <textarea name="observaciones" id="observaciones" rows="5" class="form-control"></textarea>
    </div>
</div>
<script src="../assets/scripts/calculaTrabajosRecup.js"></script>
<script src="../assets/scripts/clearData.js"></script>
<script>

</script>