<?php
require_once 'seg.php';
$info = new Seguridad();
require_once('../include/connect_mvc.php');
include("../Models/Mdl_ConexionBD.php");
include("../Models/Mdl_Proceso.php");
include("../Models/Mdl_Programa.php");
include("../Models/Mdl_MateriaPrima.php");
include("../Models/Mdl_Rendimiento.php");

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

// Carga de Rendimientos Sin Cerrar
$DataRendimientoAbierto = $obj_rendimiento->getPreRendimientoAbierto();
$DataRendimientoAbierto = $DataRendimientoAbierto == '' ? array() : $DataRendimientoAbierto;
$_abierto = count($DataRendimientoAbierto) > 0 ? true : false;
//PARAMETROS PARA CARGA DEL FORMULARIO
$fechaEngrase = $_abierto ? $DataRendimientoAbierto[0]['fechaEngrase'] : '';
$semanaProduccion = $_abierto ? date("Y", strtotime($DataRendimientoAbierto[0]['fechaEngrase'])) . "-W" . $DataRendimientoAbierto[0]['semanaProduccion'] : '';
$fechaEmpaque = $_abierto ? $DataRendimientoAbierto[0]['fechaEmpaque'] : '';
$idCatProceso = $_abierto ? $DataRendimientoAbierto[0]['idCatProceso'] : '';
$loteTemola = $_abierto ? $DataRendimientoAbierto[0]['loteTemola'] : '';
$idCatPrograma = $_abierto ? $DataRendimientoAbierto[0]['idCatPrograma'] : '';
$idCatMateriaPrima = $_abierto ? $DataRendimientoAbierto[0]['idCatMateriaPrima'] : '';
$multiMateria = $_abierto ? $DataRendimientoAbierto[0]['multiMateria'] : '0';
$checkMultiMateria = $multiMateria=='0'?'':'checked';
$disabledMultiMateria = $_abierto?'disabled':'';

?>
<!DOCTYPE html>
<html dir="ltr" lang="<?= $info->lng; ?>">
<link href="../assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="../assets/libs/bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.min.css">


<?php include("../templates/header.php"); ?>

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

                <!--- Inicio de Modal Para Captura de Lotes --->
                <div class="card border">
                    <div class="card-body" id="content-lotes">
                        <form id="formAddRendimiento">
                            <fieldset class="border p-2">
                                <legend class="text-TWM font-medium">Datos Identificativos del Lote</legend>
                                <div class="row">
                                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                        <label class="form-label required" for="fechaEngrase">Fecha de Engrase:</label>
                                        <input class="form-control" type="date" name="fechaEngrase" id="fechaEngrase" value="<?= $fechaEngrase ?>" required></input>

                                    </div>
                                    <!--- 
                                   /** OBJECT:  Cambio de Fecha de Empaque para producción  Script Date: 20/06/2022 **/
                                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                        <label class="form-label required" for="fechaEmpaque">Fecha de Empaque:</label>
                                        <input class="form-control" type="date" onchange="setSemanaInput('fechaEmpaque','semanaProduccion')" name="fechaEmpaque" value="<?= $fechaEmpaque ?>" id="fechaEmpaque" required></input>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                        <label class="form-label required" for="semanaProduccion">Semana de Producción:</label>
                                        <input class="form-control" type="week" value="<?= $semanaProduccion ?>" name="semanaProduccion" id="semanaProduccion" required></input>
                                    </div>-->

                                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                        <label class="form-label required" for="proceso">Proceso:</label>
                                        <select class="form-control select2" name="proceso" style="width:100%" id="proceso" required>
                                            <option value="">Selecciona Proceso</option>
                                            <?php
                                            $DataProceso = $obj_proceso->getProcesos("pr.estado='1'");
                                            foreach ($DataProceso as $key => $value) {
                                                $selected = $idCatProceso == $DataProceso[$key]['id'] ? 'selected' : '';
                                                echo "<option $selected value='{$DataProceso[$key]['id']}'>
                                                             {$DataProceso[$key]['codigo']} - {$DataProceso[$key]['nombre']}
                                                          </option>";
                                            }
                                            ?>
                                        </select>

                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <label class="form-label required" for="lote">Lote de Temola:</label>
                                        <span id="bloqueo-btn-res" style="display:none">
                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>

                                        </span>
                                        <span id="desbloqueo-btn-res">
                                            <span id="resultbusq"></span>
                                        </span>

                                        <input class="form-control" type="text" name="lote" value="<?= $loteTemola ?>" id="lote" required></input>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <label class="form-label required" for="programa">Programa:</label>
                                        <select class="form-control ProgramasFilter" name="programa" style="width:100%" id="programa" required>
                                            <!-- <option value="">Selecciona Programa</option>
                                            <?php
                                            $DataPrograma = $obj_programa->getPrograma("p.estado='1'", "(p.tipo='1' or p.tipo='3')");
                                            foreach ($DataPrograma as $key => $value) {
                                                $selected = $idCatPrograma == $DataPrograma[$key]['id'] ? 'selected' : '';
                                                $AreaNeta = $DataPrograma[$key]['areaNeta'] == '' ? '0.00' : $DataPrograma[$key]['areaNeta'];
                                                echo "<option $selected value='{$DataPrograma[$key]['id']}'>{$DataPrograma[$key]['nombre']} (Área Neta: {$AreaNeta})</option>";
                                            }
                                            ?> -->
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <label class="form-label required" for="materiaPrima">Materia Prima:</label>
                                        <select class="form-control select2" name="materiaPrima" style="width:100%" id="materiaPrima" required>
                                            <option value="">Selecciona Materia Prima</option>
                                            <?php
                                            $DataMateriaPrima = $obj_materia->getMaterias("mt.estado='1'");
                                            foreach ($DataMateriaPrima as $key => $value) {
                                                $selected = $idCatMateriaPrima == $DataMateriaPrima[$key]['id'] ? 'selected' : '';

                                                echo "<option $selected value='{$DataMateriaPrima[$key]['id']}'>{$DataMateriaPrima[$key]['nombre']}</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="bt-switch mt-1">
                                    <input type="checkbox" <?=$checkMultiMateria?> value='1'  name='multimateria' <?=$disabledMultiMateria?> id='multimateria' data-size="small" />
                                    <label for="multimateria">Lote Multi Materia Prima</label>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-lg-10 col-md-10 col-xs-6 col-sm-8"></div>
                                    <div class="col-lg-2 col-md-2 col-xs-6 col-sm-8">
                                        <div id="bloqueo-btn-1" style="display:none">
                                            <button class="btn btn-TWM" type="button" disabled="">
                                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                Espere...
                                            </button>

                                        </div>
                                        <div id="desbloqueo-btn-1">
                                            <button type="button" onclick="clearForm('formAddRendimiento')" class="button btn btn-danger">Limpiar</button>
                                            <button type="submit" id="btn-initloteo" disabled class="button btn btn-success">Guardar</button>

                                        </div>
                                    </div>
                                </div>
                            </fieldset>

                            <!--- Inicio de  Datos de Pedido-->
                            <fieldset class="border p-2">
                                <legend class="text-TWM font-medium">Datos de Pedido</legend>
                                <div id="content-pedido">
                                    <div class="alert alert-info" role="alert">
                                        <i class="ti-alert"></i>Carga los Datos Identificativos del Lote para proceder.
                                    </div>

                                </div>

                            </fieldset>
                            <!--- Fin de  Datos de Pedido-->

                    </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

</body>
<?= $info->creaFooter(); ?>
<?php include("../templates/libsJS.php"); ?>
<script src="../assets/scripts/validaLotePiel.js"></script>
<script src="../assets/extra-libs/datatables.net/js/jquery.dataTables.min-ESP.js"></script>
<script src="../assets/libs/bootstrap-switch/dist/js/bootstrap-switch.min.js"></script>
<script src="../assets/scripts/selectFiltros.js"></script>
<script>
    $(".bt-switch input[type='checkbox'], .bt-switch input[type='radio']").bootstrapSwitch();
    var radioswitch = function() {
        var bt = function() {
            $(".radio-switch").on("switch-change", function() {
                $(".radio-switch").bootstrapSwitch("toggleRadioState")
            }), $(".radio-switch").on("switch-change", function() {
                $(".radio-switch").bootstrapSwitch("toggleRadioStateAllowUncheck")
            }), $(".radio-switch").on("switch-change", function() {
                $(".radio-switch").bootstrapSwitch("toggleRadioStateAllowUncheck", !1)
            })
        };
        return {
            init: function() {
                bt()
            }
        }
    }();

    function cargaDatosPedido() {
        $('#content-pedido').html('<div class="loading text-center"><img src="../assets/images/loading.gif" alt="loading" /><br/>Un momento, por favor...</div>');
        $('#content-pedido').load('../templates/Pedidos/registroLotes.php');


    }
    <?php
    if ($_abierto) {
        echo "
            $('#formAddRendimiento').find('input,textarea, button, select, checkbox').attr('disabled', true);
            $('#formAddRendimiento').find('button').attr('hidden', 'hidden');
            cargaDatosPedido();
            
            
            ";
    }
    ?>
    /********** INICIO DE RENDIMIENTO ***********/
    $("#formAddRendimiento").submit(function(e) {
        e.preventDefault();
        formData = $(this).serialize();
        $.ajax({
            url: '../Controller/rendimiento.php?op=initRendimiento',
            data: formData,
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                    setTimeout(() => {
                        bloqueoBtn("bloqueo-btn-1", 2)
                        cargaDatosPedido()
                        $('#formAddRendimiento').find('input,textarea, button, select').attr('disabled', 'disabled');
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
</script>