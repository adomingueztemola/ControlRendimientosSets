<?php
require_once 'seg.php';
$info = new Seguridad();
require_once('../include/connect_mvc.php');

$info->Acceso();
$idUser = $_SESSION['CREident'];
$nameUser = $_SESSION['CREnombreUser'];
setlocale(LC_TIME, 'es_ES.UTF-8');
$debug = 1;
$space = 1;

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
                                    <li class="nav-item"> <a href="#areaTrabajo" onclick='verModulo(4)' class="nav-link" data-toggle="tab" aria-expanded="false"><i class="fas fa-flask"></i>Disminución por Pruebas</a> </li>

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
    verModulo(1)

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
            case 4:
                update("templates/FraccionLote/pruebasHides.php", "areaTrabajo", 1)
                break;
            default:
                break;
        }
    }
</script>

</html>