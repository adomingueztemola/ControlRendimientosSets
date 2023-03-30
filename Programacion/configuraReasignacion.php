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
                    <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                        <div class="card border">
                            <div class="card-body" id="">
                                <ul class="nav nav-pills m-t-30 m-b-30">
                                    <li class=" nav-item"> <a href="#areaTrabajo" onclick='verModulo(1)' class="nav-link active" data-toggle="tab" aria-expanded="false"><i class="fas fa-chart-pie"></i>Particiones de Lotes</a> </li>
                                    <li class="nav-item"> <a href="#areaTrabajo" onclick='verModulo(2)' class="nav-link" data-toggle="tab" aria-expanded="false"><i class="fas fa-dolly-flatbed"></i>Traspasos de Materia Prima</a> </li>
                                    <li class="nav-item"> <a href="#areaTrabajo" onclick='verModulo(3)' class="nav-link" data-toggle="tab" aria-expanded="false"><i class="fas fa-history"></i>Cambios de Programa</a> </li>

                                </ul>
                                <div class="tab-content br-n pn">
                                    <div id="areaTrabajo" class="tab-pane active">

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
<script src="../assets/extra-libs/datatables.net/js/jquery.dataTables.min-ESP.js"></script>
<script src="../assets/scripts/clearData.js"></script>

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

    function verModulo(option) {
        switch (option) {
            case 1:
                update("templates/FraccionLote/particionLote.php", "areaTrabajo", 1)
                break;
            case 2:
                update("templates/FraccionLote/traspasoMP.php", "areaTrabajo", 1)
                break;
            case 3:
                update("templates/FraccionLote/cambiosPrograma.php", "areaTrabajo", 1)
                break;
            default:
                break;
        }
    }


































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