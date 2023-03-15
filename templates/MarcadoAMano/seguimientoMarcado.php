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
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}
$date_start = !empty($_POST['date-start']) ? $_POST['date-start'] : date("01/m/Y");
$date_end = !empty($_POST['date-end']) ? $_POST['date-end'] :  date("t/m/Y");
$proceso = !empty($_POST['proceso']) ? $_POST['proceso'] : '';
$programa = !empty($_POST['programa']) ? $_POST['programa'] : '';
$materia = !empty($_POST['materia']) ? $_POST['materia'] : '';
/***************** CASTEO DE FECHAS ****************** */

$date_start = date("Y-m-d", strtotime(str_replace("/", "-", $date_start)));
$date_end = date("Y-m-d", strtotime(str_replace("/", "-", $date_end)));

$filtradoFecha = "l.fechaConteo BETWEEN '$date_start' AND '$date_end'";
$filtradoProceso = $proceso != '' ? "l.idCatProceso='$proceso'" : "1=1";
$filtradoPrograma = $programa != '' ? "l.idCatPrograma='$programa'" : "1=1";
$filtradoMateria = $materia != '' ? "l.idCatMateriaPrima='$materia'" : "1=1";
$obj_programa = new Programa($debug, $idUser);

$obj_marcado = new MarcadoAMano($debug, $idUser);
$DataLoteSeguimiento = $obj_marcado->getLotesSeguimiento($filtradoFecha, $filtradoProceso, $filtradoMateria, $filtradoPrograma);
$DataLoteSeguimiento = $DataLoteSeguimiento == '' ? array() : $DataLoteSeguimiento;
if (!is_array($DataLoteSeguimiento)) {
    echo "<p class='text-danger'>Error, $DataLoteSeguimiento</p>";
    exit(0);
}

?>
<div class="row mb-2">
    <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10"> </div>
    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
        <button class="btn btn-TWM btn-block" data-toggle="modal" data-target="#modalAddLote"><i class="fas fa-plus"></i>Agregar Lote</button>
    </div>

</div>
<div class="row">
    <div class="col-lg-3 col-xl-3">
        <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
            <?php
            $count = 0;
            $idCargaDefault = 0;
            foreach ($DataLoteSeguimiento as $key => $value) {
                $active = $count == 0 ? 'active' : '';
                $idCargaDefault = $count == 0 ? $DataLoteSeguimiento[$key]['id'] : $idCargaDefault;
                $lblIcon = "";
                echo "<a class='nav-link $active border' id='v-pills-{$DataLoteSeguimiento[$key]['id']}-tab' data-toggle='pill' 
                   href='#v-pills-sgto' role='tab' aria-controls='v-pills-{$DataLoteSeguimiento[$key]['id']}' aria-selected='true' 
                   onclick='cargaContent({$DataLoteSeguimiento[$key]['id']})'>
                  Lote: {$DataLoteSeguimiento[$key]['n_lote']} Fecha: {$DataLoteSeguimiento[$key]['f_fecha']}</a>";
                $count++;
            }
            ?>
        </div>
    </div>
    <div class="col-lg-9 col-xl-9">
        <div class="tab-content" id="v-pills-tabContent">
            <div class="tab-pane fade show active" id="v-pills-sgto" role="tabpanel" aria-labelledby="v-pills-home-tab">

            </div>
        </div>
    </div>
</div>

<div id="info-sgto"></div>
<!-- MODAL DE REGISTRO DE LOTE A MARCAR -->
<div class="modal fade" id="modalAddLote" role="dialog" aria-labelledby="modalAddLoteLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content modalBlock">
            <div class="modal-header bg-TWM text-white">
                <h5 class="modal-title" id="modalAddLoteLabel">Agregar Lote a Marcar</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formAddLote">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                            <label for="fecha" class="form-label required">Fecha</label>
                            <input type="date" name="fecha" id="fecha" class="form-control" required>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                            <label for="nLote" class="form-label required">Lote</label>
                            <input type="text" name="nLote" id="nLote" autocomplete="off" class="form-control" required>
                        </div>
                    </div>
                    <!--<div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <label for="areaCrust" class="form-label required">Área Crust</label>
                            <input type="number" step="0.01" min="" name="areaCrust" id="areaCrust" autocomplete="off" class="form-control" required>
                        </div>
                    </div>-->
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <label for="programa" class="form-label required">Programa</label>
                            <select class="form-control select2" name="programa" style="width:100%" id="programa" required>
                                <option value="">Selecciona Programa</option>
                                <?php
                                $DataPrograma = $obj_marcado->getProgramaConVolante();
                                foreach ($DataPrograma as $key => $value) {
                                    echo "<option $selected value='{$DataPrograma[$key]['id']}'>{$DataPrograma[$key]['nombre']}</option>";
                                }
                                ?>
                            </select>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Agregar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="../assets/scripts/clearData.js"></script>
<script>
    <?php if (count($DataLoteSeguimiento) > 0) { ?>
        cargaContent(<?= $idCargaDefault ?>);

    <?php } else {
        echo ('$("#info-sgto").html("<div class=\'alert alert-secondary\' role=\'alert\'> Sin Lotes en Marco a mano, pendientes por seguir,</div>");');
    } ?>

    function cargaContent(id) {
        $("#v-pills-sgto").load("../templates/MarcadoAMano/cargaKardexMarcado.php?id=" + id);
        $("#info-sgto").html("");
    }

    /********** AGREGAR LOTE DE MARCADO A MANO ***********/
    $("#formAddLote").submit(function(e) {
        e.preventDefault();
        formData = $(this).serialize();
        $.ajax({
            url: '../Controller/marcadoMano.php?op=agregarlote',
            data: formData,
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    bloqueoModal(e, "modalBlock", 2)
                    $("#modalAddLote").modal("hide");
                    notificaSuc(resp[1])
                    setTimeout(() => {
                        update()
                    }, 1000);

                } else if (resp[0] == 0) {
                    notificaBad(resp[1])
                    bloqueoModal(e, "modalBlock", 2)


                }
            },
            beforeSend: function() {
                bloqueoModal(e, "modalBlock", 1)
            }

        });
    });
</script>