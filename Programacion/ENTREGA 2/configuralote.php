<?php
require_once 'seg.php';
$info = new Seguridad();
require_once('../include/connect_mvc.php');
include("../Models/Mdl_Proceso.php");

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

$id = !empty($_GET['id']) ? $_GET['id'] : '0'; // ID LOTE GESTION 
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
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <ul class="timeline timeline-left">
                                    <li class="timeline-inverted timeline-item">
                                        <div class="timeline-badge danger"><i class="fas fa-edit"></i></div>
                                        <div class="timeline-panel">
                                            <div class="timeline-heading">
                                                <h4 class="timeline-title">Creación del Lote</h4>
                                            </div>
                                            <div class="timeline-body" id="carga-creacion">
                                            </div>
                                        </div>
                                    </li>
                                    <li class="timeline-inverted timeline-item">
                                        <div class="timeline-badge danger"><i class="fas fa-th-large"></i></div>
                                        <div class="timeline-panel">
                                            <div class="timeline-heading">
                                                <h4 class="timeline-title">Detalles de Materia Prima</h4>
                                            </div>
                                            <div class="timeline-body" id="carga-pedido">
                                            </div>
                                        </div>
                                    </li>
                                    <li class="timeline-inverted timeline-item">
                                        <div class="timeline-badge danger"><i class="fas fa-minus-circle"></i></div>
                                        <div class="timeline-panel">
                                            <div class="timeline-heading">
                                                <h4 class="timeline-title">Baja de Materia Prima</h4>
                                            </div>
                                            <div class="timeline-body">
                                                <div class="card border">
                                                    <div class="card-head bg-TWM text-white p-1">
                                                        <h5>Registro de Bajas de Cuero</h5>
                                                    </div>
                                                    <div class="card-body" id="carga-bajamp">
                                                    </div>
                                                </div>


                                                <div id="carga-desglosebajamp"></div>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="timeline-inverted timeline-item">
                                        <div class="timeline-badge danger"><i class="fas fa-recycle"></i></div>
                                        <div class="timeline-panel">
                                            <div class="timeline-heading">
                                                <h4 class="timeline-title">Conversión de Unidades de Materia Prima</h4>
                                            </div>
                                            <div class="timeline-body">
                                                <div class="card border">
                                                    <div class="card-head bg-TWM text-white p-1">
                                                        <h5>Registro de Conversión de Cueros a Lados</h5>
                                                    </div>
                                                    <div class="card-body" id="carga-conversion">
                                                    </div>
                                                    <div class="card-body" id="carga-desglosecoversion">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="timeline-inverted timeline-item">
                                        <div class="timeline-badge danger"><i class="fas fa-minus-circle"></i></div>
                                        <div class="timeline-panel">
                                            <div class="timeline-heading">
                                                <h4 class="timeline-title">Baja de Lados</h4>
                                            </div>
                                            <div class="timeline-body">
                                                <div class="card border">
                                                    <div class="card-head bg-TWM text-white p-1">
                                                        <h5>Registro de Bajas de Cuero</h5>
                                                    </div>
                                                    <div class="card-body" id="carga-bajald">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
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
<script src="../assets/scripts/validaLotePiel.js"></script>
<script src="../assets/extra-libs/datatables.net/js/jquery.dataTables.min-ESP.js"></script>
<script src="../assets/libs/bootstrap-switch/dist/js/bootstrap-switch.min.js"></script>
<script>
    creacion();
    cargaBajaMP();
    cargaBajaLd();
    cargaDesgloseBajaMP();
    cargaConversion();

    function creacion() {
        cargaContenido("carga-creacion", "../templates/Rendimiento/GestionLote/formCreacionLote.php?id=<?= $id ?>", '1')

    }

    function cargaDatosPedido() {
        $('#carga-pedido').html('<div class="loading text-center"><img src="../assets/images/loading.gif" alt="loading" /><br/>Un momento, por favor...</div>');
        $('#carga-pedido').load('../templates/Pedidos/registroLotes.php');
    }

    function cargaBajaMP() {
        cargaContenido("carga-bajamp", "../templates/Rendimiento/GestionLote/formBajaMP.php?id=<?= $id ?>", '1')

    }

    function cargaBajaLd() {
        cargaContenido("carga-bajald", "../templates/Rendimiento/GestionLote/formBajaLd.php?id=<?= $id ?>", '1')

    }


    function cargaDesgloseBajaMP() {
        cargaContenido("carga-desglosebajamp", "../templates/Rendimiento/GestionLote/desgloseBajaMP.php?id=<?= $id ?>", '1')
    }

    

    function cargaConversion() {
        cargaContenido("carga-conversion", "../templates/Rendimiento/GestionLote/formConversion.php?id=<?= $id ?>", '1')
    }


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