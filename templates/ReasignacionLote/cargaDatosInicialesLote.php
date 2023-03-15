<?php

define('INCLUDE_CHECK', 1);
session_start();
require_once('../../include/connect_mvc.php');
include("../../Models/Mdl_ConexionBD.php");
include("../../Models/Mdl_ReasignacionLotesFracc.php");
include("../../Models/Mdl_Excepciones.php");


include('../../assets/scripts/cadenas.php');

setlocale(LC_TIME, 'es_ES.UTF-8');
$idUser = $_SESSION['CREident'];
$nameUser = $_SESSION['CREnombreUser'];
$debug = 0;
$idrendimiento = !empty($_GET['idrendimiento']) ? $_GET['idrendimiento'] : '';
$traspaso = !empty($_GET['traspaso']) ? $_GET['traspaso'] : '';

$obj_reasignacion = new ReasignacionLotesFracc($debug, $idUser);
$Data = $obj_reasignacion->getDetRendimiento($idrendimiento);
$Data = Excepciones::validaConsulta($Data);
$Data = $Data == '' ? array() : $Data;
if (count($Data) <= 0) {
    echo '<div class="alert alert-primary" role="alert">
    <b>¡NO SE ENCONTRÓ LOTE NUEVO!</b>  contacta al
    departamento de sistemas.
  </div>';
    exit(0);
}
$f_areaWB = formatoMil($Data['areaWB'], 2);
$f_areaCrust = formatoMil($Data['areaCrust'], 2);
$f_areaFinal = formatoMil($Data['areaFinal'], 2);
$f_quiebre = formatoMil($Data['quiebre'], 2);
$f_humedad = formatoMil($Data['humedad'], 2);
$f_suavidad = formatoMil($Data['suavidad'], 2);
$f_areaProvedor = formatoMil($Data['areaProveedorLote'], 2);
$f_recorteAcabado= formatoMil($Data['recorteAcabado'], 2);
?>
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <table class="table table-sm">
            <tbody>
                <tr>
                    <td class="bg-TWM text-white">Materia Prima</td>
                    <td><?= $Data['nMateria'] ?></td>
                </tr>
                <tr>
                    <td class="bg-TWM text-white">Área WB</td>
                    <td><?= $f_areaWB ?></td>
                </tr>
                <tr>
                    <td class="bg-TWM text-white">Área Crust</td>
                    <td><?= $f_areaCrust ?></td>
                </tr>
                <tr>
                    <td class="bg-TWM text-white">Recorte de Acabado</td>
                    <td><?= $f_recorteAcabado ?></td>
                </tr>
                <tr>
                    <td class="bg-TWM text-white">Área Final Teseo</td>
                    <td><?= $f_areaFinal ?></td>
                </tr>
              
                <tr>
                    <td class="bg-TWM text-white">Quiebre</td>
                    <td><?= $f_quiebre ?></td>
                </tr>
                <tr>
                    <td class="bg-TWM text-white">Humedad</td>
                    <td><?= $f_humedad ?></td>
                </tr>
                <tr>
                    <td class="bg-TWM text-white">Suavidad</td>
                    <td><?= $f_suavidad ?></td>
                </tr>
                <tr>
                    <td class="bg-TWM text-white">Área Comprada Proveedor del Lote pie<sup>2</sup></td>
                    <td><?= $f_areaProvedor ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<div class="alert alert-warning" role="alert">
    Antes de aceptar verifica que los datos sea proporcional a la nueva asignación de cueros.
</div>
<hr>
<div class="row p-2">
    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8"></div>
    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
        <div id="bloqueo-btn-3" style="display:none">
            <button class="btn btn-success" type="button" disabled="">
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>

            </button>
        </div>
        <div id="desbloqueo-btn-3">
            <button class="button btn btn-danger btn-md" onclick="cancelarTraspaso('<?= $traspaso ?>', '<?= $idrendimiento ?>')">Cancelar</button>
            <button class="button btn btn-success btn-md" onclick="finalizarTraspaso('<?= $traspaso ?>', '<?= $idrendimiento ?>')">Finalizar</button>
        </div>
    </div>

</div>
<script>
    function finalizarTraspaso(id, idRendimiento) {
        $.ajax({
            url: '../Controller/reasignacionLotesFracc.php?op=finalizartraspaso',
            data: {
                idTraspaso: id,
                idRendimiento: idRendimiento
            },
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    // notificaSuc(resp[1]);
                    setTimeout(() => {
                        bloqueoBtn("bloqueo-btn-3", 2)
                        location.reload();
                    }, 1000);
                } else if (resp[0] == 0) {
                    notificaBad(resp[1]);
                    bloqueoBtn("bloqueo-btn-3", 2)

                }
            },
            beforeSend: function() {
                bloqueoBtn("bloqueo-btn-3", 1)
            }

        });
    }

    function cancelarTraspaso(id, idRendimiento) {
        $.ajax({
            url: '../Controller/reasignacionLotesFracc.php?op=cancelartraspaso',
            data: {
                idTraspaso: id,
                idRendimiento: idRendimiento
            },
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    // notificaSuc(resp[1]);
                    setTimeout(() => {
                        bloqueoBtn("bloqueo-btn-3", 2)
                        location.reload();
                    }, 1000);

                } else if (resp[0] == 0) {
                    notificaBad(resp[1]);
                    bloqueoBtn("bloqueo-btn-3", 2)

                }
            },
            beforeSend: function() {
                bloqueoBtn("bloqueo-btn-3", 1)

            }

        });
    }
</script>