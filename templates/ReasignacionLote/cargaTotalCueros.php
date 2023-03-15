<?php

/*****************/
/*NOTAS: 
OP 1: CARGA CUEROS INICIALES DEL LOTE E INFORMACION DEL LOTE
OP 2: CARGA CUEROS FINALES DEL LOTE
*/
/*****************/
define('INCLUDE_CHECK', 1);
session_start();
require_once('../../include/connect_mvc.php');
include("../../Models/Mdl_ConexionBD.php");
include("../../Models/Mdl_ReasignacionLotesFracc.php");
include("../../Models/Mdl_Rendimiento.php");
include("../../Models/Mdl_Excepciones.php");
include('../../assets/scripts/cadenas.php');

setlocale(LC_TIME, 'es_ES.UTF-8');
$idUser = $_SESSION['CREident'];
$nameUser = $_SESSION['CREnombreUser'];
$debug = 0;
$space = 1;
$ident = !empty($_GET['ident']) ? $_GET['ident'] : '';
$traspaso = !empty($_GET['traspaso']) ? $_GET['traspaso'] : '';

$op = !empty($_GET['op']) ? $_GET['op'] : '';

if ($ident == '' or $op == '') {
    echo '<div class="alert alert-primary" role="alert">
            <b>¡NO EXISTE LOTE!</b> intentelo de nuevo si el problema persiste contacta al
            departamento de sistemas.
          </div>';
}

$obj_rendimiento = new Rendimiento($debug, $idUser);
$obj_reasignacion = new ReasignacionLotesFracc($debug, $idUser);

if ($op == '1') {
    $Data = $obj_rendimiento->getDetRendimientos($ident);
    $Data = Excepciones::validaConsulta($Data);
    $Data = $Data == '' ? array() : $Data;
    if (count($Data) <= 0) {
        echo '<div class="alert alert-primary" role="alert">
        <b>¡NO SE ENCONTRÓ LOTE!</b> intentelo de nuevo si el problema persiste contacta al
        departamento de sistemas.
      </div>';
    }
    $_1s = $Data['0']['1s'] == '' ? '0' : formatoMil($Data['0']['1s'],0);
    $_2s = $Data['0']['2s'] == '' ? '0' : formatoMil($Data['0']['2s'],0);
    $_3s = $Data['0']['3s'] == '' ? '0' : formatoMil($Data['0']['3s'],0);
    $_4s = $Data['0']['4s'] == '' ? '0' : formatoMil($Data['0']['4s'],0);
    $_20 = $Data['0']['_20'] == '' ? '0' : formatoMil($Data['0']['_20'],0);
    $total_s = $Data['0']['total_s'] == '' ? '0' : formatoMil($Data['0']['total_s'],0);
}elseif ($op == '2'){
    $Data = $obj_reasignacion->getTraspasoRendimiento($traspaso);
    $Data = Excepciones::validaConsulta($Data);
    $Data = $Data == '' ? array() : $Data;
    if (count($Data) <= 0) {
        echo '<div class="alert alert-primary" role="alert">
        <b>¡NO SE ENCONTRÓ LOTE!</b> intentelo de nuevo si el problema persiste contacta al
        departamento de sistemas.
      </div>';
    }
    $_1s = $Data['1s'] == '' ? '0' : $Data['1s'];
    $_2s = $Data['2s'] == '' ? '0' : $Data['2s'];
    $_3s = $Data['3s'] == '' ? '0' : $Data['3s'];
    $_4s = $Data['4s'] == '' ? '0' : $Data['4s'];
    $_20 = $Data['_20'] == '' ? '0' : $Data['_20'];
    $total_s = $Data['total_s'] == '' ? '0' : $Data['total_s'];
}
?>
<?php  if($op=='1'){?>
<div class="card">
    <div class="card-header">
        Datos del Lote
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                <p><b>Lote:</b> <?= $Data['0']['loteTemola'] ?></p>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                <p><b>Proceso:</b> <?= $Data['0']['c_proceso'] ?>-<?= $Data['0']['n_proceso'] ?></p>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                <p><b>Programa:</b> <?= $Data['0']['n_programa'] ?></p>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                <p><b>Materia Prima:</b> <?= $Data['0']['n_materia'] ?></p>
            </div>
        </div>


    </div>
</div>
<?php } ?>
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