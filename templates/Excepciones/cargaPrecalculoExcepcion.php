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

//Valida existencia de excepcion abierta
$Data_Excepcion = $obj_excepciones->getDetExcepcion($id);
$Data_Excepcion = $Data_Excepcion == '' ? array() : $Data_Excepcion;
if (!is_array($Data_Excepcion)) {
    echo "<p class='text-danger'>Error, $Data_Excepcion</p>";
    exit(0);
}
$idRendimiento=$Data_Excepcion[0]['idRendimiento'];
$Data_Rendimiento = $obj_rendimiento->getDetRendimientos($Data_Excepcion[0]['idRendimiento']);
$setsRecuperados = $Data_Excepcion[0]['setsExcConRecu'];
$pzasRechazadas = $Data_Excepcion[0]['totalExcRech'];
$pzasEmpacadas = $Data_Excepcion[0]['totalExcEmp'];
$setsEmpacadas = $Data_Excepcion[0]['setsExcEmp'];

$Data_NuevoLote = $obj_excepciones->getDatosSubLote($id);

?>
<div class="row">
    <div class="col-md-6">
        <i>Fecha de Envío: <?= $Data_Excepcion[0]['f_fechaReg'] ?></i>
    </div>
    <div class="col-md-6">
        <i>Envío: <?= $Data_Excepcion[0]['n_empleadoReg'] ?></i>

    </div>
</div>

<input type="hidden" name="idExcepcion" value="<?= $id ?>">
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
    <div class="table-responsive">
        <table class="table table-sm">
            <tbody>
                <tr class="">
                    <td> <b>PIEZAS CORTADAS EN TESEO</b> </td>
                    <td><b><?= formatoMil($Data_Excepcion[0]['pzasCortadasTeseo'], 0) ?></b></td>
                </tr>

                <tr>
                    <td> PIEZAS RECUPERADAS </td>
                    <td><?= formatoMil($Data_Excepcion[0]['totalExcRecu'], 0) ?></td>
                </tr>

                <tr>
                    <td> SET'S RECUPERADOS TOTALES</td>
                    <td><?= formatoMil($setsRecuperados, 2) ?></td>
                </tr>
                <tr class="table-danger">
                    <td> PIEZAS RECHAZADAS </td>
                    <td><?= formatoMil($pzasRechazadas, 0) ?></td>
                </tr>
                <tr class="">
                    <td> <b>SETS CORTADOS EN TESEO </b></td>
                    <td><b><?= formatoMil($Data_Excepcion[0]['setsCortadosTeseo'], 0) ?></b></td>
                </tr>
                <tr>
                    <td><b>PIEZAS EMPACADAS</b></td>
                    <td><?= formatoMil($pzasEmpacadas, 0) ?></td>
                </tr>
                <tr>
                    <td><b>SET'S EMPACADAS</b></td>
                    <td><?= formatoMil($setsEmpacadas, 2) ?></td>
                </tr>

            </tbody>
        </table>
    </div>
</div>
<!------- FIN TABLA DE RECUPERACION ------->
<!--- ESTADISTICA DE PORCENTAJE ---->
<div class="row">
    <div class="table-responsive">
        <table class="table table-sm">
            <tr class="bg-TWM text-white">
                <td colspan="2" class="text-center">% de Recuperación de la Solicitud: <?= formatoMil($Data_Excepcion[0]['porcRecuperacion']) ?>%</td>
            </tr>
            <tr class="bg-TWM text-white">
                <td>% de Rechazo Final: <?= formatoMil($Data_Excepcion[0]['porcFinalRechazo']) ?>%</td>
                <td>% de Recuperación Final: <?= formatoMil($Data_Excepcion[0]['porcRecuperacionFinal']) ?>%</td>
            </tr>
        </table>
    </div>
</div>
<!--- FIN DE ESTADISTICA DE PORCENTAJE ---->
<!------- INICIO TRASPASOS A LOTES---->
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <p class="text-TWM"><b>Envío de Piezas Recuperadas a Lotes</b></p>
    </div>
</div>
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="carga-traspasos">
    </div>
</div>
<!------- FIN TRASPASOS A LOTES---->

<div class="row" id="div-nuevoLote">
    <div class="col-md-12">
        <label>Lote de Recuperacion a Crear:</label>
        <table class="table table-sm">
            <thead>
                <tr>
                    <th class="text-center">Lote Temola</th>
                    <th class="text-center">Piezas Totales</th>
                    <th class="text-center">Sets Totales</th>
                    <th class="text-center">Piezas Sin Set</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center"><?= $Data_NuevoLote[0]['n_sublote'] ?></td>
                    <td class="text-center"><?= formatoMil($Data_NuevoLote[0]['pzasTotales'], 0) ?></td>
                    <td class="text-center"><?= formatoMil($Data_NuevoLote[0]['setsTotales'], 0) ?></td>
                    <td class="text-center"><?= formatoMil($Data_NuevoLote[0]['pzasSinSet'], 0) ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
    <?php
        if($Data_Excepcion[0]['pzasEmpacadas']<=0){
            echo "$('#div-nuevoLote').attr('hidden', true);";
        }
    ?>
    updateTraspasos();
    function updateTraspasos() {
        $("#carga-traspasos").load("../templates/Excepciones/cargaTraspasoPzasRecuperadas.php?id=<?= $idRendimiento ?>");
    }
</script>