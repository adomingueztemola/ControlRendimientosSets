<?php
require_once 'seg.php';
$info = new Seguridad();
require_once('../include/connect_mvc.php');
include("../Models/Mdl_ConexionBD.php");
include("../Models/Mdl_Devolucion.php");
$info->Acceso();
$idUser = $_SESSION['CREident'];
$nameUser = $_SESSION['CREnombreUser'];
setlocale(LC_TIME, 'es_ES.UTF-8');
$debug = 0;
$space = 1;

$obj_devolucion = new Devolucion($debug, $idUser);
$DataVentas = $obj_devolucion->getVentasCerradas();

?>
<!DOCTYPE html>
<html dir="ltr" lang="<?= $info->lng; ?>">

<?php include("../templates/header.php"); ?>
<link href="../assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="../assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">

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
                    <div class="col-lg-12 col-md-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="card border">
                            <div class="card-body" id="">
                                <div class="row">
                                    <!----Inicio Numero de Factura ---->
                                    <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                        <label for="numFactura">Número de Factura: </label>
                                        <div class="input-group mb-3">
                                            <select name="" id="numFactura" style="width:70%" class="form-control select2">
                                                <option value="">Seleccionar Num. Factura</option>
                                                <?php
                                                foreach ($DataVentas as $key => $value) {
                                                    if ($DataVentas[$key]['numFactura'] != '') {
                                                        echo "<option value='{$DataVentas[$key]['id']}'>{$DataVentas[$key]['numFactura']}</option>";
                                                    }
                                                }
                                                ?>
                                            </select>
                                            <div class="input-group-append">
                                                <div id="bloqueo-btn-fact" style="display:none">
                                                    <button class="btn btn-TWM" type="button" disabled="">
                                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                    </button>

                                                </div>
                                                <div id="desbloqueo-btn-fact">
                                                    <button class="btn btn-success" type="button" onclick="busquedaFactura(1)"><i class="fas fa-check"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!---- Fin Numero de Factura ---->
                                    <!---- Inicio Numero de P.L. ---->
                                    <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                        <form id="formBusqPL">
                                            <label for="numPL">Número de P.L.: </label>
                                            <div class="input-group mb-3">
                                                <select name="" id="numPL" style="width:70%" class="form-control select2">
                                                    <option value="">Seleccionar Num. PL</option>
                                                    <?php
                                                    foreach ($DataVentas as $key => $value) {
                                                        if ($DataVentas[$key]['numPL'] != '') {
                                                            echo "<option value='{$DataVentas[$key]['id']}'>{$DataVentas[$key]['numPL']}</option>";
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                                <div class="input-group-append">
                                                    <div id="bloqueo-btn-pl" style="display:none">
                                                        <button class="btn btn-TWM" type="button" disabled="">
                                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                        </button>

                                                    </div>
                                                    <div id="desbloqueo-btn-pl">
                                                        <button class="btn btn-success" type="button" onclick="busquedaFactura(2)"><i class="fas fa-check"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <!---- Fin Numero de P.L. ---->
                                </div>
                            </div>

                        </div>
                    </div>

                </div>


                <div class="row">
                    <div class="col-lg-12 col-md-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="card border">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div id="content-seguimiento">
                                            <div class="alert alert-secondary" role="alert">
                                                    Selecciona un Num. de Factura o Num. PL para iniciar la busqueda de la venta.
                                            </div>
                                        </div>

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
<script src="../assets/libs/block-ui/jquery.blockUI.js"></script>
<script src="../assets/extra-libs/datatables.net/js/jquery.dataTables.min-ESP.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>
<script src="../assets/libs/moment/moment.js"></script>
<script src="../assets/libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="../assets/libs/bootstrap-datepicker/dist/locales/bootstrap-datepicker.es.min.js"></script>

<script>
    <?php
    if (isset($_SESSION['CRESuccessDevolucion']) and $_SESSION['CRESuccessDevolucion'] != '') { ?>
        notificaSuc('<?= $_SESSION['CRESuccessDevolucion'] ?>')
    <?php
        unset($_SESSION['CRESuccessDevolucion']);
    }
    if (isset($_SESSION['CREErrorDevolucion']) and $_SESSION['CREErrorDevolucion'] != '') { ?>
        notificaBad('<?= $_SESSION['CREErrorDevolucion'] ?>')
    <?php
        unset($_SESSION['CREErrorDevolucion']);
    }
    ?>

    function busquedaFactura(opcion) {
        switch (opcion) {
            case 1:
                numFactura = $("#numFactura").val();
                bloqueoBtn("bloqueo-btn-fact", 1)

                break;

            case 2:
                numFactura = $("#numPL").val();
                bloqueoBtn("bloqueo-btn-pl", 1)
                break;
        }
        setTimeout(() => {
            bloqueoBtn("bloqueo-btn-pl", 2)
            bloqueoBtn("bloqueo-btn-fact", 2)
        }, 1000);
        $("#content-seguimiento").load("../templates/Ventas/seguimientoDevolucion.php?data=" + numFactura);
    }

    function cargaDetalleVenta(numFactura) {
        $("#content-seguimiento").load("../templates/Ventas/seguimientoDevolucion.php?data=" + numFactura);

    }
</script>

</html>