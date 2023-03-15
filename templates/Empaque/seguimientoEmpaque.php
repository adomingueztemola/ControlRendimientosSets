<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../../include/connect_mvc.php";

$debug = 0;
$idUser = $_SESSION['CREident'];
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}
$obj_programa = new Programa($debug, $idUser); //Modelo de Programa
$obj_empaque = new Empaque($debug, $idUser); //Modelo de Empaque

$DataEmpaque = $obj_empaque->getEmpaquesFecha();
$DataEmpaque = Excepciones::validaConsulta($DataEmpaque);
?>
<div class="row mb-2">
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
        <button class="btn btn-TWM btn-block" data-toggle="modal" data-target="#modalAddEmpaque"><i class="fas fa-box-open"></i> Iniciar Empaque</button>
    </div>
    <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">

    </div>

</div>
<div class="row" id="navEmpaque">
    <div class="col-lg-3 col-xl-3" style="height:400px; overflow-y: auto;">

        <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
            <?php
            $count = 0;
            $idCargaDefault = 0;
            foreach ($DataEmpaque as $value) {
                $active = $count == 0 ? 'active' : '';
                $idCargaDefault = $count == 0 ? $value['id'] : $idCargaDefault;
                $lblIcon = "";
                echo "<a class='nav-link $active border' id='v-pills-{$value['id']}-tab' data-toggle='pill' 
                   href='#v-pills-sgto' role='tab' aria-controls='v-pills-{$value['id']}' aria-selected='true' 
                   onclick='cargaContent({$value['id']})'>
                  Programa: {$value['nPrograma']} Fecha: {$value['f_fecha']}</a>";
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
<!-- MODAL DE REGISTRO DE EMPAQUE -->
<div class="modal fade" id="modalAddEmpaque" role="dialog" aria-labelledby="modalAddLoteLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content modalBlock">
            <div class="modal-header bg-TWM text-white">
                <h5 class="modal-title" id="modalAddLoteLabel">Agregar Empaque</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formAddEmpaque">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                            <label for="fecha" class="form-label required">Fecha</label>
                            <input type="date" name="fecha" id="fecha" class="form-control" value="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <label for="programa" class="form-label required">Programa</label>
                            <?php
                            $Data = $obj_programa->getPrograma("p.estado='1'", "p.tipo='1'");
                            $Data = Excepciones::validaConsulta($Data);
                            ?>
                            <select class="form-control select2" name="programa" style="width:100%" id="programa" required>
                                <option value="">Selecciona Programa</option>
                                <?php
                                foreach ($Data as $key => $value) {
                                    echo "<option $selected value='{$Data[$key]['id']}'>{$Data[$key]['nombre']}</option>";
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
<script src="../assets/libs/block-ui/jquery.blockUI.js"></script>

<script>
    <?php if (count($DataEmpaque) > 0) { ?>
        cargaContent(<?= $idCargaDefault ?>);
        $("#navEmpaque").prop("hidden", false)
    <?php } else {
        echo ('$("#info-sgto").html("<div class=\'alert alert-secondary\' role=\'alert\'> Sin Empaques Registrados, pendientes por seguir,</div>");
               $("#navEmpaque").prop(\'hidden\', true);');
    } ?>
 
    function cargaContent(id) {
        $("#v-pills-sgto").load("../templates/Empaque/cargaKardexEmpaque.php?id=" + id);
        $("#info-sgto").html("");
        bloqueoDiv("v-pills-sgto", 1)
        setTimeout(() => {
            bloqueoDiv("v-pills-sgto", 2)
        }, 1000);
    }

    /********** AGREGAR EMPAQUE ***********/
    $("#formAddEmpaque").submit(function(e) {
        e.preventDefault();
        formData = $(this).serialize();
        $.ajax({
            url: '../Controller/empaque.php?op=agregarempaque',
            data: formData,
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    bloqueoModal(e, "modalBlock", 2)
                    $("#modalAddEmpaque").modal("hide");
                    clearForm("formAddEmpaque")
                    notificaSuc(resp[1])
                    setTimeout(() => {
                        updateSeguimiento()
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