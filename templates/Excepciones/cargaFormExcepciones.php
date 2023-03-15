<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once('../../include/connect_mvc.php');
include("../../Models/Mdl_ConexionBD.php");
include("../../Models/Mdl_Rendimiento.php");
include("../../Models/Mdl_ExcepcionDeStock.php");
include('../../assets/scripts/cadenas.php');
$debug = 0;
$idUser = $_SESSION['CREident'];
$nameUser = $_SESSION['CREnombreUser'];
$id = !empty($_GET['id']) ? $_GET['id'] : '';

if ($id == '') {
    echo "<div class='alert alert-danger' role='alert'>
            No se recibó datos del lote, favor de volverlo a intentar si el problema persiste notifica al departamento de Sistemas.
        </div>";
    exit(0);
}

$obj_rendimiento = new Rendimiento($debug, $idUser);
$obj_excepciones = new ExcepcionDeStock($debug, $idUser);

$Data_Rendimiento = $obj_excepciones->getDetRendimientos($id);
//Valida existencia de excepcion abierta
$Data_Excepcion = $obj_excepciones->validaExcepcionAbierta($id);
$Data_Excepcion = $Data_Excepcion == '' ? array() : $Data_Excepcion;
if (!is_array($Data_Excepcion)) {
    echo "<p class='text-danger'>Error, $Data_Excepcion</p>";
    exit(0);
}
$disabled = "";
$setsEmpacados = 0;
$piezasRecuperadas = 0;
$motivo = "";
$_excepcion = false;
$pzasEmpacadas=0;
if (count($Data_Excepcion) > 0) {
    $disabled = "disabled";
    $pzasEmpacadas = formatoMil($Data_Excepcion[0]['pzasEmpacadas'],0);
    $piezasRecuperadas = formatoMil($Data_Excepcion[0]['pzasRecuperadas'], 0);
    $motivo =  $Data_Excepcion[0]['descripcion'];
    
    $_excepcion = true;
?>
    <div class="row">
        <div class="col-md-6">
            <i>Fecha de Envío: <?= $Data_Excepcion[0]['f_fechaReg'] ?></i>
        </div>
        <div class="col-md-6">
            <i>Envío: <?= $Data_Excepcion[0]['n_empleadoReg'] ?></i>

        </div>
    </div>

<?php
}
$r_pzasRecuperados = $piezasRecuperadas + $Data_Rendimiento[0]['totalRecu'];
$r_pzasEmpacadas = $pzasEmpacadas + $Data_Rendimiento[0]['totalEmp'];

?>
<input type="hidden" name="idRendimiento" value="<?= $id ?>">
<div class="row">
    <div class="col-md-12">
        <div class="card border border-primary">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6"> Lote Temola: <?= $Data_Rendimiento[0]["loteTemola"] ?></div>
                    <div class="col-md-6">
                        <p class="">
                            Semana de Producción: <?= $Data_Rendimiento[0]['semanaProduccion'] ?>
                        </p>
                    </div>
                </div>


            </div>
        </div>
    </div>
</div>
<!------- INICIO TABLA DE RECUPERACION ------->
<div class="row">
    <div class="col-md-12">
        <table class="table table-sm">
            <tbody>
                <tr>
                    <td><i>Piezas en Inventario de Rechazo</i></td>
                    <td><?= formatoMil($Data_Rendimiento[0]['totalRech'], 0) ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<div class="row">
    <div class="table-responsive">
        <table class="table table-sm">
            <thead class="bg-TWM text-white">
                <tr>
                    <th>Agregar de Pzas. Recuperadas</th>
                    <th></th>
                    <th>Pzas. Recuperadas</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                    <input type="number" <?= $disabled ?> name="pzasRecuperadas" max="<?= $Data_Rendimiento[0]['totalRech'] ?>" min="0" step="1" style="width: 150px;" id="pzasRecupExp" class="form-control" value="<?= $piezasRecuperadas ?>"></input>
                    </td>
                    <td>+</td>
                    <td>
                    <p><?= formatoMil($Data_Rendimiento[0]['totalRecu'], 0) ?></p>
                        <input type="hidden" id="stk-pzasrecuperadas" value="<?= $Data_Rendimiento[0]['totalRecu'] ?>">

                    </td>
                    <td><label id="calculo-stockRecupera"><?= formatoMil($r_pzasRecuperados, 0) ?></label></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<!--- CALCULO DE SETS EN STOCK --->
<div class="row">
    <div class="col-md-12">
        <p class="text-danger" id="calculo-setsRecuperados">Se tienen <span id="txt-setsRecuperados">
                <?= formatoMil($Data_Rendimiento['0']['setsTotalRecu'], 0) ?></span> Set's Recuperados, con
            <span id="txt-pzasRestRecuperados"><?= formatoMil($Data_Rendimiento['0']['rzgoRecu'], 0) ?></span> pieza(s).
        </p>
    </div>
</div>
<!------- FIN TABLA DE RECUPERACION ------->
<div class="row">
    <div class="col-md-12">
        <table class="table table-sm">
            <tbody>
                <tr>
                    <td><i>Piezas en Inventario de Recuperación</i></td>
                    <td><label id="txt-stkRecuPreview"><?= formatoMil($Data_Rendimiento[0]['totalRecu'], 0) ?></label></td>

                </tr>

            </tbody>
        </table>
    </div>
</div>
<!------- INICIO TABLA DE RECUPERACION ------->
<div class="row">
    <div class="table-responsive">
        <table class="table table-sm">
            <thead class="bg-TWM text-white">
                <tr>
                    <th>Recalculo de Piezas Empacadas</th>
                    <th></th>
                    <th>Total Empacadas</th>
                    <th>Total</th>

                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <input type="number" <?= $disabled ?> name="pzasEmpacadas" max="<?= $Data_Rendimiento[0]['totalEmp'] ?>" min="0" step="1" style="width: 150px;" id="setsEmpacados" class="form-control" value="<?= $pzasEmpacadas ?>"></input>
                    </td>
                    <td>+</td>
                    <td>
                        <p><?= formatoMil(FLOOR($Data_Rendimiento[0]['totalEmp']), 0) ?></p>
                        <input type="hidden" id="stk-setsEmpacados" value="<?= $Data_Rendimiento[0]['totalEmp'] ?>">


                    </td>
                    <td><label id="calculo-stockEmpacados"><?= formatoMil($r_pzasEmpacadas, 0) ?></label></td>

                </tr>
            </tbody>
        </table>
    </div>
</div>
<!--- CALCULO DE SETS EN STOCK --->

<!------- FIN TABLA DE RECUPERACION ------->
<div class="row">
    <div class="col-md-12">
        <label for="motivoExcepcion">Motivo que originó la excepción</label>
        <textarea class="form-control" <?= $disabled ?> name="motivoExcepcion" id="motivoExcepcion" cols="30" rows="5"><?= $motivo ?></textarea>
    </div>
</div>
<script src="../assets/scripts/calculosExcepciones.js"></script>
<script>
    <?php if ($_excepcion) { ?>
        $("#btn-envioExcepcion").attr("hidden", true);
    <?php } else { ?>
        $("#btn-envioExcepcion").attr("hidden", false);
    <?php    } ?>
</script>