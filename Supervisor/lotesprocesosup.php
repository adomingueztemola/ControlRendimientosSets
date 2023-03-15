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
                  
                </div>
                <div class="row">
                    <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                        <div class="card border">
                            <div class="card-body" id="content-lotes">

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
        $('#content-lotes').html('<div class="loading text-center"><img src="../assets/images/loading.gif" alt="loading" /><br/>Un momento, por favor...</div>');
        $('#content-lotes').load('../templates/Rendimiento/cargaGestionLotesGerencia.php');


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
                        update()
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
    /********** ELIMINAR PRE REGISTRO  ***********/
    function eliminarPreRegistro(id) {
        $.ajax({
            url: '../Controller/rendimiento.php?op=eliminarprerendimiento',
            data: {
                id: id
            },
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    // notificaSuc(resp[1])
                    setTimeout(() => {
                        bloqueoBtn("bloqueo-btn-2", 2)
                        location.reload()
                    }, 1000);


                } else if (resp[0] == 0) {
                    notificaBad(resp[1])
                    bloqueoBtn("bloqueo-btn-2", 2)


                }
            },
            beforeSend: function() {
                bloqueoBtn("bloqueo-btn-2", 1)
            }

        });
    }
    /********** CIERRE PRE REGISTRO  ***********/
    function cierrePreRegistro() {
        log_result = validaCamposLlenos()
        if (log_result) {
            $.ajax({
                url: '../Controller/rendimiento.php?op=cierrerendimiento',
                type: 'POST',
                success: function(json) {
                    resp = json.split('|')
                    if (resp[0] == 1) {
                        notificaSuc(resp[1])
                        setTimeout(() => {
                            bloqueoBtn("bloqueo-btn-2", 2)
                            location.reload()
                        }, 1000);


                    } else if (resp[0] == 0) {
                        notificaBad(resp[1])
                        bloqueoBtn("bloqueo-btn-2", 2)


                    }
                },
                beforeSend: function() {
                    bloqueoBtn("bloqueo-btn-2", 1)
                }

            });
        }

    }
</script>

</html>