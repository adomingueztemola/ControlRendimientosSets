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
$DataVP = $obj_rendimiento->getVentasvsPedidosSemana(formatoFecha("W"), date('Y'));
$DataVP = !is_array($DataVP) ? array() : $DataVP;
$ventas = formatoMil($DataVP[0]['TotalVtas']);
$pedidos = formatoMil($DataVP[0]['TotalPedido']);

?>
<!DOCTYPE html>
<html dir="ltr" lang="<?= $info->lng; ?>">

<?php include("../templates/header.php"); ?>
<style>

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
            <div class="container-fluid" style="background:url(../assets/images/fondocalidad.jpg);  background-size: cover;">
                <div class="row mt-5 justify-content-center">
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-2 col-md-2 col-xs-2 col-sm-2"></div>
                                    <div class="col-lg-8 col-md-8 col-xs-8 col-sm-8 text-center">
                                        <a class="btn btn-md btn-TWM" href="../doctos/Plantillas/PLANTILLA DE BAJAS DE SCRAP EN SETS rev 1.0.xlsm" download="PLANTILLA DE BAJAS DE SCRAP EN SETS rev 1.0.xlsm"><i class="fas fa-file"></i> Reporte de Excel</a>
                                    </div>
                                    <div class="col-lg-2 col-md-2 col-xs-2 col-sm-2"></div>

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

<script>

</script>

</html>