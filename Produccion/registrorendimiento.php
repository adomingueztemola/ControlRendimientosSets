<?php
require_once 'seg.php';
$info = new Seguridad();
require_once('../include/connect_mvc.php');
include("../Models/Mdl_ConexionBD.php");
include("../Models/Mdl_Proceso.php");
include("../Models/Mdl_Solicitudes.php");

$info->Acceso();
$idUser = $_SESSION['CREident'];
$nameUser = $_SESSION['CREnombreUser'];
setlocale(LC_TIME, 'es_ES.UTF-8');
$debug = 0;
$space = 1;
$obj_proceso = new ProcesoSecado($debug, $idUser);
$obj_programa = new Programa($debug, $idUser);
$obj_materia = new MateriaPrima($debug, $idUser);
$obj_rendimiento = new Rendimiento($debug, $idUser);

$DataRendimientoAbierto = $obj_rendimiento->getRendimientoAbierto();
$DataRendimientoAbierto = $DataRendimientoAbierto == '' ? array() : $DataRendimientoAbierto;
if (!is_array($DataRendimientoAbierto)) {
    echo "<p class='text-danger'>Error, $DataRendimientoAbierto</p>";
    exit(0);
}
$_abierto = count($DataRendimientoAbierto) <= 0 ? false : true;
$id = count($DataRendimientoAbierto) <= 0 ? '0' : $DataRendimientoAbierto['0']['id'];
//Validacion De Lote En Edicion
$obj_solicitudes = new Solicitud($debug, $idUser);
$DataValidaUsoDelLote= $obj_solicitudes->validaCambioDePzas($id);
$DataValidaUsoDelLote = $DataValidaUsoDelLote == '' ? array() : $DataValidaUsoDelLote;
if(!is_array($DataValidaUsoDelLote)){
    echo "<p class='text-danger'>Error, $DataValidaUsoDelLote</p>";
    exit(0);
}
$cambioPzas= count($DataValidaUsoDelLote)>0?'1':'0';

?>
<!DOCTYPE html>
<html dir="ltr" lang="<?= $info->lng; ?>">

<?php include("../templates/header.php"); ?>
<link href="../assets/libs/sweetalert2/dist/sweetalert2.min.css" rel="stylesheet">

<body>
    <div class="preloader">
        <div class="lds-ripple">
            <div class="lds-pos"></div>
            <div class="lds-pos"></div>
        </div>
    </div>
    <div id="main-wrapper">
        <?= $info->creaHeaderConMenu(); ?>
        <div class="page-wrapper">
            <div class="container-fluid">
                <?php include("../templates/namePage.php"); ?>
               
                <div class="row">
                    <div class="col-md-6 col-lg-6">
                        <div class="card border">
                            <div class="card-body" id="">
                                <form id="formAddRendimiento">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="lotePreRegistro">Lotes con Empaque Terminado</label>
                                            <div class="input-group mb-3">
                                                <select name="lotePreRegistro" id="lotePreRegistro" style="width:90%" onchange="cargaInfoLote()" class="form-control select2">
                                                    <option value="">Seleccionar Folio del Lote</option>
                                                    <?php
                                                    $DataLotesPendientes = $obj_rendimiento->getLotesPreRegistrados();
                                                    foreach ($DataLotesPendientes as $key => $value) {
                                                        $selected = $id == $DataLotesPendientes[$key]['id'] ? 'selected' : '';
                                                        echo "<option $selected value='{$DataLotesPendientes[$key]['id']}'>
                                                        Programa: {$DataLotesPendientes[$key]['n_programa']}/N° de Lote: {$DataLotesPendientes[$key]['loteTemola']}</option>";
                                                    }
                                                    ?>
                                                </select>
                                                <div class="input-group-append">
                                                    <div id="bloqueo-btn-1" style="display:none">
                                                        <button class="btn btn-success btn-md" type="button" disabled="">
                                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                        </button>

                                                    </div>
                                                    <div id="desbloqueo-btn-1">
                                                        <button type="submit" class="button btn btn-success btn-md"><i class="fas fa-check"></i></button>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <div id="info-lote">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-6">
                        <div class="card border">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="content-Yield">

                                    </div>
                                </div>
                                <hr>
                                <div class="row" id="btns-finalizar" hidden>


                                    <div class="col-md-6"></div>
                                    <div class="col-md-6 text-right">
                                        <div id="bloqueo-btn-2" style="display:none">
                                            <button class="btn btn-TWM" type="button" disabled="">
                                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                Espere...
                                            </button>

                                        </div>
                                        <div id="desbloqueo-btn-2">
                                            <button type="button" onclick="eliminarPreRegistro(<?=$id?>)" class="button btn btn-danger">Cancelar Pre-Registro</button>
                                            <button type="button" hidden id="btn-finalizarYield" onclick="cierrePreRegistro(<?=$cambioPzas?>)" class="button btn btn-success">Finalizar</button>
                                        </div>
                                    </div>

                                </div>

                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
</body>


<?= $info->creaFooter(); ?>
<?php include("../templates/libsJS.php"); ?>
<script src="../assets/scripts/calculaSemanaProduccion.js"></script>
<script src="../assets/scripts/validaLotePiel.js"></script>
<script src="../assets/libs/sweetalert2/dist/sweetalert2.all.min.js"></script>

<script>
    update()
    cargaInfoLote()
    <?php
    if (isset($_SESSION['CRESuccessRendimiento']) and $_SESSION['CRESuccessRendimiento'] != '') { ?>
        notificaSuc('<?= $_SESSION['CRESuccessRendimiento'] ?>')
    <?php
        unset($_SESSION['CRESuccessRendimiento']);
    }
    if (isset($_SESSION['CREErrorRendimiento']) and $_SESSION['CREErrorRendimiento'] != '') { ?>
        notificaBad('<?= $_SESSION['CREErrorRendimiento'] ?>')
    <?php
        unset($_SESSION['CREErrorRendimiento']);
    }
    if ($_abierto) {
        echo "$('#formAddRendimiento').find('input, textarea, button, select').attr('disabled', 'disabled');";
        echo "$('#btns-finalizar').removeAttr('hidden');";
    }
    ?>

    function update() {
        $('#content-Yield').html('<div class="loading text-center"><img src="../assets/images/loading.gif" alt="loading" /><br/>Un momento, por favor...</div>');
        $('#content-Yield').load('../templates/Rendimiento/cargaTablaRendimiento.php');


    }
    function cargaInfoLote(){

        id= $("#lotePreRegistro").val();
        $('#info-lote').html('<div class="loading text-center"><img src="../assets/images/loading.gif" alt="loading" /><br/>Un momento, por favor...</div>');
        $('#info-lote').load('../templates/Rendimiento/infoLote.php?idRendimiento='+id);
    
    }

    /********** INICIO DE RENDIMIENTO ***********/
    $("#formAddRendimiento").submit(function(e) {
        e.preventDefault();
        formData = $(this).serialize();
        $.ajax({
            url: '../Controller/rendimiento.php?op=seleccionarlote',
            data: formData,
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                    setTimeout(() => {
                        bloqueoBtn("bloqueo-btn-1", 2)
                      //  update()
                        $('#formAddRendimiento').find('input, textarea, button, select').attr('disabled', 'disabled');

                        $("#btns-init").attr('hidden', true);
                        $("#btns-finalizar").attr('hidden', false);
                        location.reload()

                    }, 1000);


                } else if (resp[0] == 0) {
                    notificaBad(resp[1])
                    bloqueoBtn("bloqueo-btn-1", 2)


                }
            },
            beforeSend: function() {
                bloqueoBtn("bloqueo-btn-1", 1)
            }

        });
    });
    /********** ELIMINAR PRE REGISTRO  ***********/
    function eliminarPreRegistro(id) {
        $.ajax({
            url: '../Controller/rendimiento.php?op=eliminarrendimiento',
            data:{id:id},
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    // notificaSuc(resp[1])
                    setTimeout(() => {
                        bloqueoBtn("bloqueo-btn-2", 2)
                        location.reload()
                    }, 1000);


                } else if (resp[0] == 0) {
                    notificaBad(resp[1])
                    bloqueoBtn("bloqueo-btn-2", 2)


                }
            },
            beforeSend: function() {
                bloqueoBtn("bloqueo-btn-2", 1)
            }

        });
    }
    /********** CIERRE PRE REGISTRO  ***********/
    function cierrePreRegistro(cambioPzas) {
        log_result = validaCamposLlenos()
        if (log_result) {
            $.ajax({
                url: '../Controller/rendimiento.php?op=cierrerendimiento',
                data: {cambioPzas:cambioPzas},
                type: 'POST',
                success: function(json) {
                    resp = json.split('|')
                    if (resp[0] == 1) {
                        setTimeout(() => {
                            bloqueoBtn("bloqueo-btn-2", 2)
                            location.reload()
                        }, 1000);


                    } else if (resp[0] == 0) {
                        notificaBad(resp[1])
                        bloqueoBtn("bloqueo-btn-2", 2)


                    }
                },
                beforeSend: function() {
                    bloqueoBtn("bloqueo-btn-2", 1)
                }

            });
        }

    }
</script>

</html>