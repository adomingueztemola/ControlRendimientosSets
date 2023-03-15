<?php
require_once 'seg.php';
$info = new Seguridad();
require_once('../include/connect_mvc.php');
include("../Models/Mdl_ConexionBD.php");
include("../Models/Mdl_Programa.php");
include("../Models/Mdl_Proceso.php");
include("../Models/Mdl_MateriaPrima.php");

$info->Acceso();
$idUser = $_SESSION['CREident'];
$nameUser = $_SESSION['CREnombreUser'];
setlocale(LC_TIME, 'es_ES.UTF-8');
$debug = 0;
$space = 1;

$AccessBitacora = (isset($_COOKIE["AccessBitacora"])) ? trim($_COOKIE["AccessBitacora"]) : '0';

?>
<!DOCTYPE html>
<html dir="ltr" lang="<?= $info->lng; ?>">

<?php include("../templates/header.php"); ?>
<link href="../assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="../assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
<link href="../dist/css/boton.css">
<link rel="stylesheet" type="text/css" href="../assets/libs/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.min.css">
<link rel="stylesheet" type="text/css" href="../assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">

</link>
<style>
    .ir-arriba {
        display: none;
        position: fixed;
        bottom: 20px;
        right: 20px;
    }
</style>

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
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-11"></div>
                                    <div class="col-md-1 text-right mb-2">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-xs-12 col-sm-12" id="carga-content">

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
<script src="../assets/libs/moment/moment.js"></script>
<script src="../assets/libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="../assets/libs/bootstrap-datepicker/dist/locales/bootstrap-datepicker.es.min.js"></script>
<script src="../assets/libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="../assets/libs/bootstrap-datepicker/dist/locales/bootstrap-datepicker.es.min.js"></script>
<script>
    <?php
    if (isset($_SESSION['CRESuccessReacond']) and $_SESSION['CRESuccessReacond'] != '') { ?>
        notificaSuc('<?= $_SESSION['CRESuccessReacond'] ?>')
    <?php
        unset($_SESSION['CRESuccessReacond']);
    }
    if (isset($_SESSION['CREErrorReacond']) and $_SESSION['CREErrorReacond'] != '') { ?>
        notificaBad('<?= $_SESSION['CREErrorReacond'] ?>')
    <?php
        unset($_SESSION['CREErrorReacond']);
    }
    ?>
    <?php
    if ($AccessBitacora == '0') {
        echo "updateLogin();";
    } else if ($AccessBitacora == '1') {
        echo "updateReacond();";
    }
    ?>
    /********ACTUALIZAR LOGIN ********/
    function updateLogin() {
        cargaContenido("carga-content", "../templates/Reacondicionamiento/loginAcceso.php", '1')

    }
    /********ACTUALIZAR TRABAJOS DE RECUPERACION ********/
    function updateReacond() {
        cargaContenido("carga-content", "../templates/Reacondicionamiento/inicialRecuperacion.php", '1')
    }
</script>

</html>