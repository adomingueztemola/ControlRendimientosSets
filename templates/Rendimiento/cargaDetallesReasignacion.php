<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once('../../include/connect_mvc.php');
include("../../Models/Mdl_ConexionBD.php");
include("../../Models/Mdl_MateriaPrima.php");
include("../../Models/Mdl_Proceso.php");
include("../../Models/Mdl_Programa.php");
include("../../Models/Mdl_Rendimiento.php");
include("../../Models/Mdl_Excepciones.php");

include('../../assets/scripts/cadenas.php');

$idUser = $_SESSION['CREident'];
$nameUser = $_SESSION['CREnombreUser'];
$debug = 0;
$space = 1;

$obj_programa = new Programa($debug, $idUser);
$obj_proceso = new ProcesoSecado($debug, $idUser);

$obj_rendimiento = new Rendimiento($debug, $idUser);
$data = !empty($_GET['data']) ? $_GET['data'] : '';
if ($data == '') {
    echo "<div class='alert alert-danger' role='alert'>
    No se encontró el detallado del lote, notifica al departamento de Sistemas.
  </div>";
    exit(0);
}
$DataLote = $obj_rendimiento->getDetRendimientos($data);
?>


<div class="alert alert-info" role="alert">
    <i class=" fas fa-info-circle"></i> El cambio de Programa afectará directamente al Yield Final.
</div>
<input type="hidden" name="idLote" value="<?= $data ?>">
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <label for="programaEdita" class="form-label required">Selecciona Nuevo Programa:</label>
       
        <select class="form-control ProgramasFilter" id="programaEdita" required style="width:100%" name="programa">
        </select>
    </div>
</div>
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <label for="procesoEdita" class="form-label required">Selecciona Nuevo Proceso:</label>
        <select class="form-control ProcesosFilter" id="procesoEdita" required style="width:100%" name="proceso">
        </select>
    </div>
</div>
</form>
<script src="../assets/scripts/selectFiltros.js"></script>
<script src="../assets/scripts/clearDataSinSelect.js"></script>
