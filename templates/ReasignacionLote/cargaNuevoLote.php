<?php

define('INCLUDE_CHECK', 1);
session_start();
require_once('../../include/connect_mvc.php');
include("../../Models/Mdl_ConexionBD.php");
include("../../Models/Mdl_ReasignacionLotesFracc.php");
include("../../Models/Mdl_Excepciones.php");
include("../../Models/Mdl_Programa.php");
include("../../Models/Mdl_Proceso.php");

include('../../assets/scripts/cadenas.php');

setlocale(LC_TIME, 'es_ES.UTF-8');
$idUser = $_SESSION['CREident'];
$nameUser = $_SESSION['CREnombreUser'];
$debug = 0;
$traspaso = !empty($_GET['traspaso']) ? $_GET['traspaso'] : '';
$obj_reasignacion = new ReasignacionLotesFracc($debug, $idUser);
$Data = $obj_reasignacion->getTraspasoRendimiento($traspaso);
$Data = Excepciones::validaConsulta($Data);
$Data = $Data == '' ? array() : $Data;
if (count($Data) <= 0) {
    echo '<div class="alert alert-primary" role="alert">
    <b>¡NO SE ENCONTRÓ LOTE NUEVO!</b>  contacta al
    departamento de sistemas.
  </div>';
}
$_1s = $Data['1sTransfer'] == '' ? '0' : $Data['1s'];
$_2s = $Data['2sTransfer'] == '' ? '0' : $Data['2sTransfer'];
$_3s = $Data['3sTransfer'] == '' ? '0' : $Data['3sTransfer'];
$_4s = $Data['4sTransfer'] == '' ? '0' : $Data['4sTransfer'];
$_20 = $Data['_20Transfer'] == '' ? '0' : $Data['_20Transfer'];
$total_s = $Data['total_sTransfer'] == '' ? '0' : $Data['total_sTransfer'];
$obj_proceso = new ProcesoSecado($debug, $idUser);
$obj_programa = new Programa($debug, $idUser);
//Data de Rendimiento
$idRendimientoTransfer = count($Data) > 0 ? $Data['idRendimientoTransfer'] : '';
$DataRend = $obj_reasignacion->getDetRendimiento($idRendimientoTransfer);
$DataRend = Excepciones::validaConsulta($DataRend);
$DataRend = $DataRend == '' ? array() : $DataRend;
$idCatPrograma = count($DataRend) > 0 ? $DataRend['idCatPrograma'] : '';
$idCatProceso = count($DataRend) > 0 ? $DataRend['idCatProceso'] : '';
$fechaEngrase = count($DataRend) > 0 ? $DataRend['fechaEngrase'] : '';

?>
<div class="card-header card-TWM"><b>Lote Nuevo a Designar: <?= $Data['loteTransfer'] ?></b></div>
<div class="table-responsive">
    <table class="table table-sm">
        <thead class="bg-TWM text-white">
            <tr>
                <th>1s</th>
                <th>2s</th>
                <th>3s</th>
                <th>4s</th>
                <th>20</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <input type="number" disabled name="1s" id="1s" class="form-control sumatoria_s" value="<?= $_1s ?>" step="0.01" min="0">
                </td>
                <td>
                    <input type="number" disabled name="2s" id="2s" class="form-control sumatoria_s" value="<?= $_2s ?>" step="0.01" min="0">
                </td>
                <td>
                    <input type="number" disabled name="3s" id="3s" class="form-control sumatoria_s" value="<?= $_3s ?>" step="0.01" min="0">
                </td>
                <td>
                    <input type="number" disabled name="4s" id="4s" class="form-control sumatoria_s" value="<?= $_4s ?>" step="0.01" min="0">
                </td>
                <td>
                    <input type="number" disabled name="20" id="20" class="form-control sumatoria_s" value="<?= $_20 ?>" step="0.01" min="0">
                </td>
                <td>
                    <input type="number" disabled name="total_s" id="Total" class="form-control" value="<?= $total_s ?>" step="0.01" min="0">
                </td>
            </tr>
        </tbody>
    </table>
</div>
<form id="formConfig">
    <input type="hidden" name="idTraspaso" value="<?= $traspaso ?>">
   
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
            <label for="proceso" class="form-label required">Proceso</label>
            <?php
            $Data = $obj_proceso->getProcesos("pr.estado='1'");
            $Data = Excepciones::validaConsulta($Data);
            ?>
            <select id="proceso" name="proceso" required class="form-control select2">
                <option value=''>Selecciona Proceso</option>
                <?php
                foreach ($Data as $key => $value) {
                    $selected = $Data[$key]['id'] == $idCatProceso ? 'selected' : '';
                    echo "<option $selected value='{$Data[$key]['id']}'>{$Data[$key]['codigo']} {$Data[$key]['nombre']}</option>";
                }

                ?>
            </select>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
            <label for="programa" class="form-label required">Programa</label>
            <?php
            $Data = $obj_programa->getPrograma("p.estado='1'", "p.tipo='1'");
            $Data = Excepciones::validaConsulta($Data);
            ?>
            <select id="programa" required name="programa" class="form-control select2">
                <option value=''>Selecciona Programa</option>
                <?php
                foreach ($Data as $key => $value) {
                    $selected = $Data[$key]['id'] == $idCatPrograma ? 'selected' : '';

                    echo "<option $selected value='{$Data[$key]['id']}'>{$Data[$key]['nombre']}</option>";
                }

                ?>
            </select>
        </div>
    </div>
    <hr>
    <div class="row m-2">
        <div class="col-lg-9 col-md-9 col-xs-9 col-sm-9">
        </div>
        <div class="col-lg-3 col-md-3 col-xs-3 col-sm-3">
            <div id="bloqueo-btn-2" style="display:none">
                <button class="btn btn-success" type="button" disabled="">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>

                </button>
            </div>
            <div id="desbloqueo-btn-2">
                <button class="button btn btn-md btn-success" type="submit"><i class="fas fa-check"></i>Aceptar</button>
            </div>
        </div>
    </div>
</form>
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="carga-detalleLote">

    </div>
</div>
<script src="../assets/scripts/clearData.js"></script>
<script>
    <?php if (count($DataRend) > 0) { ?>
        updateDatosNuevoLote('<?= $idRendimientoTransfer ?>', "carga-detalleLote");
        bloqueoSecondTraspasos()

    <?php } ?>
    /* CARGA DATOS DEL NUEVO LOTE*/
    function updateDatosNuevoLote(idRendimiento, div) {
        cargaContenido(div, "../templates/ReasignacionLote/cargaDatosInicialesLote.php?idrendimiento=" + idRendimiento + "&traspaso=<?= $traspaso ?>", '1')
    }
    /* BLOQUEO DE DATOS */
    function bloqueoSecondTraspasos() {
        $("#formConfig").find('input, textarea, button, select').prop("disabled", true);
    }
    /* CONFIGURACION DE NUEVO LOTE */
    $("#formConfig").submit(function(e) {
        e.preventDefault();
        formData = $(this).serialize();
        $.ajax({
            url: '../Controller/reasignacionLotesFracc.php?op=configlote',
            data: formData,
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    updateDatosNuevoLote(resp[2], "carga-detalleLote")
                    bloqueoSecondTraspasos()
                    bloqueoBtn("bloqueo-btn-2", 2)
                    notificaSuc(resp[1]);
                } else if (resp[0] == 0) {
                    notificaBad(resp[1]);
                    bloqueoBtn("bloqueo-btn-2", 2)
                }
            },
            beforeSend: function() {
                bloqueoBtn("bloqueo-btn-2", 1)
            }

        });
    });
    /********************/
 
</script>