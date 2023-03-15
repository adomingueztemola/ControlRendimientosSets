<?php
require_once 'seg.php';
$info = new Seguridad();

$info->Acceso();
$idUser = $_SESSION['CREident'];
$nameUser = $_SESSION['CREnombreUser'];
setlocale(LC_TIME, 'es_ES.UTF-8');
$debug = 0;
$space = 1;
$data = (!empty($_GET['data']) and $_GET['data'] != '') ? $_GET['data'] : '';
?>
<!DOCTYPE html>
<html dir="ltr" lang="<?= $info->lng; ?>">

<?php include("../templates/header.php"); ?>
<link href="../assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css" rel="stylesheet">

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
                    <div class="col-lg-4 col-md-4 col-md-4 col-sm-12 col-xs-12">
                        <div class="card border">
                            <div class="card-header text-white bg-TWM">
                                <h4>Seguimiento de Lotes Pendientes</h4>
                            </div>
                            <div class="card-body" style="height:500px; overflow-y: scroll;">
                                <div class="row">
                                    <div class="col-md-7 col-lg-7 col-sm-7 col-xs-7">
                                        <form id="filtrado">
                                            <div class="input-group mb-3">
                                                <input type="text" name="lote" autocomplete="off" id="lote"  class="form-control" placeholder="" aria-label="" aria-describedby="basic-addon1">
                                                <div class="input-group-append">
                                                    <button class="btn btn-TWM" type="submit"><i class="ti-search"></i></button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="col-md-4 col-lg-4 col-sm-4 col-xs-4"></div>
                                    <div class="col-md-1 col-lg-1 col-sm-1 col-xs-1 text-left">
                                        <button name="anio" class="btn button btn-rounded btn-sm btn-light" onclick="updateLotes()" title="Actualizar Historial"> <i class="fas fa-history"></i></button>


                                    </div>

                                </div>

                                <div class="row">
                                    <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12" id="carga-lotesPendientes">

                                    </div>
                                </div>


                            </div>

                        </div>
                    </div>
                    <div class="col-lg-8  col-md-8 col-sm-12 col-xs-12">
                        <div class="card border">
                            <div class="card-body" id="carga-registro">


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
<script src="../assets/libs/toastr/build/toastr.min.js"></script>

<script>
    updateLotes()

    <?php
    if ($data != '' and $data != '0') {
        echo "updateCargaRegtro($data);";
    }
    ?>

    function updateLotes() {
        $('#carga-lotesPendientes').html('<div class="loading text-center"><img src="../assets/images/loading.gif" alt="loading" /><br/>Un momento, por favor...</div>');
        $('#carga-lotesPendientes').load('../templates/Pedidos/lotesPendientes.php');
        $("#lote").val("");
    }

    function updateCargaRegtro(idLote) {
        $.ajax({
            url: '../templates/Pedidos/registroLotes.php',
            data: {
                id: idLote
            },
            type: 'POST',
            success: function(json) {
                $(".cardLotes").removeClass("bg-light");
                $("#cardLote-" + idLote).addClass("bg-light");
                $('#carga-registro').html(json);

            },
            beforeSend: function() {
                $('#carga-registro').html('<div class="loading text-center"><img src="../assets/images/loading.gif" alt="loading" /><br/>Un momento, por favor...</div>');
            }
        });
    }

    /*************** FILTRADO DE SET'S *********************/
    $("#filtrado").submit(function(e) {
        e.preventDefault();
        formData = $(this).serialize();
        $.ajax({
            url: "../templates/Pedidos/lotesPendientes.php",
            data: formData,
            type: 'POST',
            success: function(respuesta) {
                $("#carga-lotesPendientes").html(respuesta);


            },
            beforeSend: function() {}

        });
    });
</script>

</html>