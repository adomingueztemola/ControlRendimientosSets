<?php
require_once 'seg.php';
$info = new Seguridad();
require_once "../include/connect_mvc.php";
include('../Models/Mdl_ConexionBD.php');
include('../Models/Mdl_Rendimiento.php');

include("../assets/scripts/cadenas.php");

$info->Acceso();
$idUser = $_SESSION['CREident'];
$nameUser = $_SESSION['CREnombreUser'];
setlocale(LC_TIME, 'es_ES.UTF-8');
$debug = 0;
$space = 1;
$obj_rendimiento = new Rendimiento($debug, $idUser);


?>
<!DOCTYPE html>
<html dir="ltr" lang="<?= $info->lng; ?>">

<?php include("../templates/header.php"); ?>
<link rel="stylesheet" type="text/css" href="../assets/extra-libs/c3/c3.min.css">

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
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-xs-12 col-sm-12">
                        <div class="card">
                            <div class="card-header bg-TWM text-white">
                                <h4>Gráfica de Comparativa de Proveedores</h4>
                            </div>
                            <div class="card-body" id="carga-grafica">

                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>




</body>


<?= $info->creaFooter(); ?>
<?php include("../templates/libsJS.php"); ?>

<script>
    updateGrafica();
    /* CARGA GRAFICA */
    function updateGrafica() {
        cargaContenido("carga-grafica", "../templates/Proveedores/cargaGraficaComparativas.php", '1')
    }
</script>

</html>