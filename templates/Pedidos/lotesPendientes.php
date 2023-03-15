<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../../include/connect_mvc.php";
include('../../Models/Mdl_ConexionBD.php');
include('../../Models/Mdl_Rendimiento.php');

include('../../assets/scripts/cadenas.php');

$debug = 0;
$idUser = $_SESSION['CREident'];
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}
$lote = !empty($_POST['lote']) ? $_POST['lote'] : '';
$filtradoLote= $lote!=''?"LOCATE('$lote', loteTemola) > 0":"1=1";
$obj_rendimiento = new Rendimiento($debug, $idUser);
$Data_Rendimiento = $obj_rendimiento->getRendimientos("1=1", "1=1", "1=1", "1=1", $filtradoLote);
foreach ($Data_Rendimiento as $key => $value) {
?>
    <div class="row">
        <div class="col-md-12">
            <div class="card border border-primary mb-3 cardLotes" id="cardLote-<?= $Data_Rendimiento[$key]['id'] ?>">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-10"> Lote Temola: <?= $Data_Rendimiento[$key]["loteTemola"] ?></div>
                        <div class="col-md-2 text-right">
                            <button type="button" title="Iniciar Captura" onclick="updateCargaRegtro(<?= $Data_Rendimiento[$key]['id'] ?>)" class="btn btn-sm btn-success"><i class="fas fa-pencil-alt"></i></button>
                        </div>
                    </div>


                </div>
                <div class="card-body card-text">
                    <div class="row mb-2">
                        <div class="col-md-4">
                            <p class="">
                                Fecha de Engrase:<br><?= $Data_Rendimiento[$key]['f_fechaEngrase'] ?>
                            </p>
                        </div>
                        <div class="col-md-4">
                            <p class="">
                                Fecha de Empaque:<br><?= $Data_Rendimiento[$key]['f_fechaEmpaque'] ?>
                            </p>
                        </div>
                        <div class="col-md-4">
                            <p class="">
                                Semana de Producción:<br><?= $Data_Rendimiento[$key]['semanaProduccion'] ?>
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <p class="">
                                Programa: <?= $Data_Rendimiento[$key]['n_programa'] ?>
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <p class="">
                                Proceso: <?= $Data_Rendimiento[$key]['n_proceso'] ?>
                            </p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <p class="card-text">
                                Materia Prima: <?= $Data_Rendimiento[$key]['n_materia'] ?>
                            </p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
<?php
}
?>