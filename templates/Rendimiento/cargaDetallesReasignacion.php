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
        <label for="programaEdita">Selecciona Nuevo Programa:</label>
        <?php
        $DataPrograma = $obj_programa->getPrograma("p.estado='1'", "p.tipo='1'");
        $DataPrograma = Excepciones::validaConsulta($DataPrograma);
        ?>
        <select class="form-control select2" id="programaEdita" style="width:100%" name="programa">
            <option value="">Selecciona Programa</option>

            <?php
            foreach ($DataPrograma as $key => $value) {
                $f_AreaNeta = formatoMil($DataPrograma[$key]['areaNeta'], 2);
                $selected = $DataLote[0]['idCatPrograma'] == $DataPrograma[$key]['id'] ? 'selected' : '';
                echo "<option $selected value='{$DataPrograma[$key]['id']}'>{$DataPrograma[$key]['nombre']} (Área Neta: {$f_AreaNeta})</option>";
            }
            ?>
        </select>
    </div>
</div>
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <label for="procesoEdita">Selecciona Nuevo Proceso:</label>
        <?php
        $DataProceso = $obj_proceso->getProcesos("pr.estado='1'");
        $DataProceso = Excepciones::validaConsulta($DataProceso);
        ?>
        <select class="form-control select2" id="procesoEdita" style="width:100%" name="proceso">

            <option value="">Selecciona Proceso</option>
            <?php
            foreach ($DataProceso as $key => $value) {
                $selected = $DataLote[0]['idCatProceso'] == $DataProceso[$key]['id'] ? 'selected' : '';
                echo "<option $selected value='{$DataProceso[$key]['id']}'>{$DataProceso[$key]['codigo']} - {$DataProceso[$key]['nombre']}</option>";
            }
            ?>
        </select>
    </div>
</div>
</form>
<script src="../assets/scripts/clearData.js"></script>