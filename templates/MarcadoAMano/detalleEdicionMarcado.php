<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../../include/connect_mvc.php";
include('../../Models/Mdl_ConexionBD.php');
include('../../Models/Mdl_MarcadoAMano.php');
include('../../assets/scripts/cadenas.php');
include("../../Models/Mdl_Programa.php");

$debug = 0;
$idUser = $_SESSION['CREident'];
if ($debug == 1) {
    print_r($_GET);
} else {
    error_reporting(0);
}
$data = !empty($_GET['data']) ? $_GET['data'] : '';
if ($data == '') {
    echo "<div class='alert alert-danger' role='alert'>
    No se encontró el detallado del lote, notifica al departamento de Sistemas.
  </div>";
    exit(0);
}
$obj_marcado = new MarcadoAMano($debug, $idUser);
$DataLotes = $obj_marcado->getDetMarcadoXLote($data);
?>
<input type="hidden" name="idLote" id="idLoteActualizar" value='<?= $DataLotes[0]['id'] ?>'>
<div class="row">
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
        <label for="fecha" class="form-label required">Fecha</label>
        <input type="date" name="fecha" id="fecha" class="form-control" value='<?= $DataLotes[0]['fecha'] ?>' required>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
        <label for="nLote" class="form-label required">Lote</label>
        <input type="text" name="nLote" id="nLote" autocomplete="off" value='<?= $DataLotes[0]['n_lote'] ?>' class="form-control" required>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <label for="fecha" class="form-label required">% Decremento</label>
        <div class="input-group mb-3">
            <input type="number" step="0.01" min="0" max="100" name="porcDecrement" value="<?= $DataLotes[0]["porcDecremento"] ?>" id="porcDecrement" autocomplete="off" class="form-control" required>
            <div class="input-group-append">
                <span class="input-group-text">%</span>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <label for="areaCrust" class="form-label required">Área Crust</label>
        <input type="number" step="0.01" min="" name="areaCrust" value='<?= $DataLotes[0]['areaCrust'] ?>' id="areaCrust" autocomplete="off" class="form-control" required>
    </div>
</div>
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <label for="programaEdit" class="form-label required">Programa</label>
        <select class="form-control select2" name="programa" style="width:100%" id="programaEdit" required>
            <option value="">Selecciona Programa</option>
            <?php
            $DataPrograma = $obj_marcado->getProgramaConVolante();
            foreach ($DataPrograma as $key => $value) {
                $selected = $DataLotes[0]['idCatPrograma'] == $DataPrograma[$key]['id'] ? 'selected' : '';
                echo "<option $selected value='{$DataPrograma[$key]['id']}'>{$DataPrograma[$key]['nombre']}</option>";
            }
            ?>
        </select>
    </div>
</div>
<script src="../assets/scripts/clearData.js"></script>