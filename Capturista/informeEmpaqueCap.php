<?php
require_once 'seg.php';
$info = new Seguridad();
require_once('../include/connect_mvc.php');
include("../Models/Mdl_ConexionBD.php");
$info->Acceso();
$idUser = $_SESSION['CREident'];
$nameUser = $_SESSION['CREnombreUser'];
setlocale(LC_TIME, 'es_ES.UTF-8');
$debug = 1;
$space = 1;
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
                                <form id="filtrado">
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6 col-xs-12 col-sm-12">
                                            <label for="lotesClasificados">Lotes</label>
                                            <select id="lotesClasificados" onchange="visualizaEmpaque(this)" style="width:100%" id="lotesClasificados" class="form-control select2">
                                            </select>
                                        </div>
                                    </div>
                                </form>

                            </div>

                        </div>
                    </div>

                </div>


                <div class="row">
                    <div class="col-lg-12 col-md-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="card border">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-11"></div>
                                    <div class="col-md-1 text-right">
                                    </div>
                                </div>


                                <div class="row">
                                    <div class="col-md-12">
                                        <div id="content-inventario">
                                            <div class="alert alert-dark" role="alert">
                                                <div class="spinner-border spinner-border-sm text-light" role="status">
                                                    <span class="sr-only">Loading...</span>
                                                </div> Selecciona un lote para consultar su empaque.
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
    /* 
     *  LOTES CLASIFICADOS DE SELECT2
     */
    $('#lotesClasificados').select2({
        placeholder: 'Selecciona un lote registrados',
        ajax: {
            url: '../Controller/empaque.php?op=cargajsonlotes',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    palabraClave: params.term // search term
                };
            },
            processResults: function(data) {
                console.log(data)
                return {
                    results: data
                };
            },
            cache: true
        }
    });
    /*
     *CARGA DE REPORTE DE EMPAQUE
     */
    function visualizaEmpaque(selectEmpaque) {
        id = $(selectEmpaque).val()
        $.ajax({
            url: '../templates/Almacen/informeEmpaque.php',
            data: {
                id: id
            },
            type: 'POST',
            success: function(respuesta) {
                $('#content-inventario').html(respuesta);


            },
            beforeSend: function() {
                $('#content-inventario').html('<div class="loading text-center"><img src="../assets/images/loading.gif" alt="loading" /><br/>Un momento, por favor...</div>');


            }

        });
    }






    /*************** FILTRADO DE TABLA *********************/
    $("#filtrado").submit(function(e) {
        e.preventDefault();
        formData = $(this).serialize();
        $.ajax({
            url: '../templates/Almacen/informeEmpaque.php',
            data: formData,
            type: 'POST',
            success: function(respuesta) {
                $('#content-inventario').html(respuesta);


            },
            beforeSend: function() {
                $('#content-inventario').html('<div class="loading text-center"><img src="../assets/images/loading.gif" alt="loading" /><br/>Un momento, por favor...</div>');


            }

        });
    });
</script>

</html>