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
$DataRendimientoAbierto = $obj_rendimiento->getRendimientoAbierto();
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
                    <div class="col-md-10">
                    </div>
                    <div class="col-md-2 text-right mb-2">
                        <a class="btn button btn-TWM" href="registroventadevoluc.php"><i class="far fa-money-bill-alt"></i> Agregar Venta</a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                        <div class="card border">
                            <div class="card-body" id="content-ventasdevoluc">

                            </div>
                        </div>

                    </div>


                </div>
                <!---- INICIO MODAL DE REGISTRO DE DEVOLUCION --->
                <div class="modal fade" data-backdrop="static" data-keyboard="false" id="ventaDevolModal" role="dialog" aria-labelledby="ventaDevolModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-TWM text-white">
                                <h5 class="modal-title" id="ventaDevolModalLabel">Registro de Venta de Devolución</h5>

                            </div>
                            <div class="modal-body" id="carga-formDevol">

                            </div>
                            <div class="modal-footer">
                                <div id="bloqueo-btnFin" style="display:none">
                                    <button class="btn btn-success btn-sm" type="button" disabled="">
                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    </button>
                                    <button class="btn btn-success btn-sm" type="button" disabled="">
                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                        Espera ...
                                    </button>

                                </div>
                                <div id="desbloqueo-btnFin">
                                    <button type="button" onclick="cancelarDevolucion()" class="btn btn-danger">Cancelar Venta</button>
                                    <button type="button" onclick="finalizaDevolucion()" class="btn btn-success">Guardar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!---- FIN  MODAL DE REGISTRO DE DEVOLUCION --->
            </div>
        </div>
</body>


<?= $info->creaFooter(); ?>
<?php include("../templates/libsJS.php"); ?>
<script src="../assets/scripts/calculaSemanaProduccion.js"></script>
<script src="../assets/scripts/validaLotePiel.js"></script>
<script src="../assets/extra-libs/datatables.net/js/jquery.dataTables.min-ESP.js"></script>

<script>
    update();
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

    if ($_abierto) { ?>
        $('#formAddProveedor').find('input, textarea, button, select').attr('disabled', 'disabled');
        $(".Disabled").attr("disabled", false)

    <?php } ?>

    function update() {
        $('#content-ventasdevoluc').html('<div class="loading text-center"><img src="../assets/images/loading.gif" alt="loading" /><br/>Un momento, por favor...</div>');
        $('#content-ventasdevoluc').load('../templates/Ventas/cargaVentasXDevolucion.php');


    }

    function cargaFormVtaXDevolucion(idVenta) {
        cargaContenido("carga-formDevol", "../templates/Ventas/cargaFormularioVtaxDevol.php?data=" + idVenta, '1')
    }
</script>

</html>