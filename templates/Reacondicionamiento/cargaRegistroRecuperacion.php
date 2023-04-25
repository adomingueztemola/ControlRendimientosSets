<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once('../../include/connect_mvc.php');
$debug = 0;
$idUser = $_SESSION['CREident'];
$obj_trabajos = new TrabajosRecupera($debug, $idUser);
$obj_programas = new Programa($debug, $idUser);
?>
<div class="row mb-2">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <label class="form-label required" for="fecha">Fecha Inicio</label>
        <input type="date" class="form-control" required value="<?= date("Y-m-d") ?>" name="fecha" id="fecha">
    </div>
   
</div>
<div class="row  card-header">
    <div class="col-md-12">
       <b>Si el lote a recuperar no se encuentra en lista, verifica procedencia y notifica a Sistemas.</b> 
        <input type="hidden" name="tipoLoteInicio" id="loteRegistrado" value="1">
       <!--- <div class="form-check form-check-inline">
            <input class="form-check-input" onchange="cambiaLoteInicial()" type="radio" checked name="tipoLoteInicio" id="loteRegistrado" value="1">
            <label class="form-check-label" for="loteRegistrado">Lote Registrado</label>
        </div>-->
      <!---  <div class="form-check form-check-inline">
            <input class="form-check-input" onchange="cambiaLoteInicial()" type="radio" name="tipoLoteInicio" id="loteNoIdentificado" value="2">
            <label class="form-check-label" for="loteNoIdentificado">Lote No Identificado</label>
        </div>-->
    </div>
</div>
<div class="row">
    <div class="col-md-12">
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
<script src="../assets/scripts/clearData.js"></script>
