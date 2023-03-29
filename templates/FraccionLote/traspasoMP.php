<?php
require_once 'seg.php';
$info = new Seguridad();
require_once('../include/connect_mvc.php');
include("../Models/Mdl_ConexionBD.php");
include("../Models/Mdl_Proceso.php");
include("../Models/Mdl_Programa.php");
include("../Models/Mdl_MateriaPrima.php");
include("../Models/Mdl_ReasignacionLotesFracc.php");
include("../Models/Mdl_Excepciones.php");

$info->Acceso();
$idUser = $_SESSION['CREident'];
$nameUser = $_SESSION['CREnombreUser'];
setlocale(LC_TIME, 'es_ES.UTF-8');
$debug = 0;
$space = 1;
$obj_proceso = new ProcesoSecado($debug, $idUser);
$obj_programa = new Programa($debug, $idUser);
$obj_materia = new MateriaPrima($debug, $idUser);
$obj_rendimiento = new ReasignacionLotesFracc($debug, $idUser);
//Checar alguna reasignación abierta por el usuario
$DataRend = $obj_rendimiento->getTraspasoAbierto();
$DataRend = Excepciones::validaConsulta($DataRend);
$DataRend = $DataRend == '' ? array() : $DataRend;
$idRendimientoTransfer = count($DataRend) > 0 ? $DataRend['idRendimiento'] : '';
$pzasTraspaso = count($DataRend) > 0 ? $DataRend['pzasTraspaso'] : '';
$id = count($DataRend) > 0 ? $DataRend['id'] : '';
?>
<!DOCTYPE html>
<html dir="ltr" lang="<?= $info->lng; ?>">
<link href="../assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css" rel="stylesheet">

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

                <div class="row">
                    <div class="col-md-6 col-lg-6 col-xs-6 col-sm-6">
                        <div class="card border">
                            <div class="card-body" id="">
                                <div class="row">
                                    <div class="col-md-11 col-lg-11 col-sm-11 col-xs-11">
                                    </div>
                                    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                                        <div id="bloqueo-btn-4" style="display:none">
                                            <button class="btn btn-danger" type="button" disabled="">
                                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                            </button>
                                        </div>
                                        <div id="desbloqueo-btn-4">
                                            <button class="button btn btn-danger btn-xs" title="Cancelar Operación" onclick="cancelarTraspasoInit()">
                                                <i class="fas fa-power-off"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <label for="lote">Lote</label>
                                        <?php
                                        $Data = $obj_rendimiento->getRendimientos("1=1", "1=1", "1=1", "1=1", "1=1", "r.estado>0");
                                        $Data = Excepciones::validaConsulta($Data);
                                        ?>
                                        <select name="lote" class="form-control select2" onchange="updateTraspaso()" style="width:100%" id="lote">
                                            <option value="">Seleccionar lote con reasignación</option>
                                            <?php
                                            foreach ($Data as $key => $value) {
                                                $selected = $idRendimientoTransfer == $Data[$key]['id'] ? 'selected' : '';
                                                echo "<option $selected data-totals='{$Data[$key]['total_s']}' value='{$Data[$key]['id']}'>Lote: {$Data[$key]['loteTemola']} (Proceso: {$Data[$key]['c_proceso']} Programa: {$Data[$key]['n_programa']})</option>";
                                            }

                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="cargaTotalCueros">

                                    </div>
                                </div>
                                <form id="formAsignacion">
                                    <div class="row" hidden id="areaCargaPzasTraspasar">
                                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                            <label for="pzasTraspasar" class="form-label required">Piezas a traspasar</label>
                                            <input type="hidden" value="" id="idRendimiento" name="idRendimiento">
                                            <div class="input-group mb-3">
                                                <input type="number" class="form-control" value="<?= $pzasTraspaso ?>" required name="pzasTraspasar" id="pzasTraspasar" min="0" max="" step="1">
                                                <div class="input-group-append">
                                                    <div id="bloqueo-btn-1" style="display:none">
                                                        <button class="btn btn-success" type="button" disabled="">
                                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                        </button>
                                                    </div>
                                                    <div id="desbloqueo-btn-1">
                                                        <button class="btn btn-success" id="btnPzasTraspasar" type="submit"><i class="fas fa-check"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="cargaCuerosEstimados">

                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="cargaVentas">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="cargaPedidos">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="cargaDatosActuales">
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="col-md-6 col-lg-6 col-xs-6 col-sm-6">
                        <div class="card border">
                            <div class="card-body" id="carga-infoLoteNuevo">
                                <div class="alert alert-info" role="alert">
                                    Selecciona el lote origen, antes de configurar nuevo lote.
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
<script src="../assets/extra-libs/datatables.net/js/jquery.dataTables.min-ESP.js"></script>

<script>
    <?php
    if (isset($_SESSION['CRESuccessReasigna']) and $_SESSION['CRESuccessReasigna'] != '') { ?>
        notificaSuc('<?= $_SESSION['CRESuccessReasigna'] ?>')
    <?php
        unset($_SESSION['CRESuccessReasigna']);
    }
    if (isset($_SESSION['CREErrorReasigna']) and $_SESSION['CREErrorReasigna'] != '') { ?>
        notificaBad('<?= $_SESSION['CREErrorReasigna'] ?>')
    <?php
        unset($_SESSION['CREErrorReasigna']);
    }

    ?>
    /* CARGA CONTENIDO DE CONFIGURACION */
    function updateTraspaso() {
        ident = $("#lote").val();
        totals = $("#lote option:selected").data("totals");
        $("#idRendimiento").val(ident);
        if (ident == '') {
            $("#areaCargaPzasTraspasar").prop("hidden", true);
        } else {
            $("#areaCargaPzasTraspasar").prop("hidden", false);
            updateTotalCueros(ident, "1", "cargaTotalCueros");
            $("#pzasTraspasar").prop("max", totals);

        }
        $('#lote').select2('close');
    }
    /* $("#lote").on('change', function(e) {
      

     });*/
    <?php
    if (count($DataRend) > 0) { ?>
        $('#lote').val(<?= $idRendimientoTransfer ?>).trigger('change.select2');
        updateTotalCueros(<?= $idRendimientoTransfer ?>, '2', "cargaCuerosEstimados", <?= $id ?>);
        updateResumenVentas(<?= $id ?>, "cargaVentas");
        updateResumenPedidos(<?= $id ?>, "cargaPedidos");
        updateDatosNuevoLote(<?= $id ?>, "carga-infoLoteNuevo");
        updateDatosActualesLote(<?= $id ?>, "cargaDatosActuales");
        bloqueoInitTraspasos();

    <?php } ?>
    /********************/
    /* ACTUALIZA TOTAL DE CUEROS */
    function updateTotalCueros(ident, op, div, traspaso = 0) {
        cargaContenido(div, "../templates/ReasignacionLote/cargaTotalCueros.php?ident=" + ident + "&op=" + op + "&traspaso=" + traspaso, '1')
    }
    /* CARGA CUEROS VENTAS */
    function updateResumenVentas(traspaso, div) {
        cargaContenido(div, "../templates/ReasignacionLote/cargaDespliegueVentas.php?traspaso=" + traspaso, '1')
    }
    /* CARGA CUEROS PEDIDOS */
    function updateResumenPedidos(traspaso, div) {
        cargaContenido(div, "../templates/ReasignacionLote/cargaDesplieguePedidos.php?traspaso=" + traspaso, '1')
    }
    /* CARGA DATOS DEL NUEVO LOTE*/
    function updateDatosNuevoLote(traspaso, div) {
        cargaContenido(div, "../templates/ReasignacionLote/cargaNuevoLote.php?traspaso=" + traspaso, '1')
    }
    /* CARGA DATOS DEL ACTUALES*/
    function updateDatosActualesLote(traspaso, div) {
        cargaContenido(div, "../templates/ReasignacionLote/cargaDatosActuales.php?traspaso=" + traspaso, '1')
    }
    /* BLOQUEO DE DATOS */
    function bloqueoInitTraspasos() {
        $("#lote").prop("disabled", true);
        $("#pzasTraspasar").prop("disabled", true);
        $("#btnPzasTraspasar").prop("disabled", true);

    }
    /********************/
    /* INICIA ASIGNACION DE LOTE */
    $("#formAsignacion").submit(function(e) {
        e.preventDefault();
        formData = $(this).serialize();
        $.ajax({
            url: '../Controller/reasignacionLotesFracc.php?op=inittraspasar',
            data: formData,
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    ident = $("#lote").val()
                    updateTotalCueros(ident, '2', "cargaCuerosEstimados", resp[2]);
                    updateResumenVentas(resp[2], "cargaVentas");
                    updateResumenPedidos(resp[2], "cargaPedidos");
                    updateDatosNuevoLote(resp[2], "carga-infoLoteNuevo");
                    updateDatosActualesLote(resp[2], "cargaDatosActuales");

                    bloqueoInitTraspasos();
                    bloqueoBtn("bloqueo-btn-1", 2);
                    notificaSuc(resp[1]);
                } else if (resp[0] == 0) {
                    bloqueoBtn("bloqueo-btn-1", 2);
                    notificaBad(resp[1]);
                }
            },
            beforeSend: function() {
                bloqueoBtn("bloqueo-btn-1", 1)
            }

        });
    });
    /********************/
    function cancelarTraspasoInit() {
        $.ajax({
            url: '../Controller/reasignacionLotesFracc.php?op=cancelartraspasoabierto',
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    // notificaSuc(resp[1]);
                    setTimeout(() => {
                        bloqueoBtn("bloqueo-btn-4", 2)
                        location.reload();
                    }, 1000);

                } else if (resp[0] == 0) {
                    notificaBad(resp[1]);
                    bloqueoBtn("bloqueo-btn-4", 2)

                }
            },
            beforeSend: function() {
                bloqueoBtn("bloqueo-btn-4", 1)

            }

        });
    }
</script>

</html>