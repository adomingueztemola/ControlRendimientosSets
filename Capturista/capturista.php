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

$obj_programa = new Programa($debug, $idUser);


?>
<!DOCTYPE html>
<html dir="ltr" lang="<?= $info->lng; ?>">

<?php include("../templates/header.php"); ?>
<link href="../assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="../assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
<link href="../dist/css/boton.css">
<link rel="stylesheet" type="text/css" href="../assets/libs/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.min.css">
<link href="../assets/libs/sweetalert2/dist/sweetalert2.min.css" rel="stylesheet">

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
                                        <button class="btn button btn-rounded btn-sm btn-light" onclick="updateSeguimiento()" title="Actualizar Contenido"> <i class="fas fa-history"></i></button>
                                    </div>
                                    
                                </div>


                                <div class="row">
                                    <div class="col-md-12">
                                        <div id="content-seguimiento"></div>
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
<script src="../assets/libs/sweetalert2/dist/sweetalert2.all.min.js"></script>

<script>
    updateSeguimiento()



    $(window).scroll(function() {
        if ($(this).scrollTop() > 0) {
            $('.ir-arriba').slideDown(300);
        } else {
            $('.ir-arriba').slideUp(300);
        }
    });


    function updateSeguimiento() {
        $('#content-seguimiento').html('<div class="loading text-center"><img src="../assets/images/loading.gif" alt="loading" /><br/>Un momento, por favor...</div>');
        $('#content-seguimiento').load('../templates/Empaque/seguimientoEmpaque.php');

    }

    
    /*************** FILTRADO DE TABLA *********************/
   /* $("#filtrado").submit(function(e) {
        e.preventDefault();
        formData = $(this).serialize();
        $.ajax({
            url: '../templates/LotesOPF/seguimientoOPF.php',
            data: formData,
            type: 'POST',
            success: function(respuesta) {
                $('#content-seguimiento').html(respuesta);


            },
            beforeSend: function() {}

        });
    });*/
</script>

</html>