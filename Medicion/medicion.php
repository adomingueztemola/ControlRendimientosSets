<?php
require_once 'seg.php';
$info = new Seguridad();
require_once "../include/connect_mvc.php";
include('../Models/Mdl_ConexionBD.php');

include("../assets/scripts/cadenas.php");

$info->Acceso();
$idUser = $_SESSION['CREident'];
$nameUser = $_SESSION['CREnombreUser'];
setlocale(LC_TIME, 'es_ES.UTF-8');
$debug = 0;
$space = 1;

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

            <div class="container-fluid">

                <div class="card">
                    <div class="card-header" style="background-color:#ee5a36;">
                        <h3 class="text-white">Lotes:</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-10">
                                <select name="lotes" onchange="verLados()" id="selectlotes" style="width:100%" class="custom-select custom-select-lg loteMedidoFilter">
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">

                    <div class="card col-md-7">
                        <div id="contentCrear">
                        </div>
                    </div>

                    <div class="col-md-5 ">
                        <div class="card">

                            <div class="card-header" style="background-color:#ee5a36;">
                                <div class="row">
                                    <div class="col-md-4">
                                        <h3 class="text-white">Paquetes</h3>
                                    </div>
                                    <div class="col-md-8" hidden id="div-abierto">
                                        <h4 class="text-white"> # Abierto: <span id="numAbierto">N/A</span></h4>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="contentPaquetes">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <?= $info->creaFooter(); ?>

</body>
<?php include("../templates/libsJS.php"); ?>
<script src="../assets/scripts/selectFiltros.js"></script>


<script>
    // cargaPaquete()
    verLados()
    $('#tbody-lados').find('.accordionPaq').remove()

    function cargaPaquete() {
        loteMedido = $("#selectlotes").val();
        $.ajax({
            url: '../templates/Medicion/EtiquetasPaquetes.php',
            data: {
                id: loteMedido
            },
            type: 'POST',
            success: function(respuesta) {
                $('#contentPaquetes').html(respuesta);
                $('#tbody-lados').find('.accordionPaq').remove()

            },
            beforeSend: function() {
                $('#contentPaquetes').html('<div class="loading text-center"><img src="../assets/images/loading.gif" alt="loading" /><br/>Un momento, por favor...</div>');

            }

        });

    }

    function verLados(actPaq = true) {
        loteMedido = $("#selectlotes").val();
        $.ajax({
            url: '../templates/Medicion/CrearPaquete.php',
            data: {
                id: loteMedido
            },
            type: 'POST',
            success: function(respuesta) {
                $('#contentCrear').html(respuesta);
            },
            beforeSend: function() {
                $('#contentCrear').html('<div class="loading text-center"><img src="../assets/images/loading.gif" alt="loading" /><br/>Un momento, por favor...</div>');

            }

        });
        if (actPaq) {
            cargaPaquete()
            $("#tbody-lados.accordionPaq").remove()

        }

    }

    function consultaPaqAbierto(id) {
        $.ajax({
            url: '../Controller/medicion.php?op=getpaqabierto',
            data: {
                id: id
            },
            type: 'POST',
            async: false,
            dataType: "json",
            success: function(respuesta) {
                if (Object.keys(respuesta).length > 0) {
                    if (respuesta.paqDelete == '1') {
                        $("#numAbierto").text(respuesta.numPaqDlt);
                        $("#div-abierto").prop("hidden", false)
                        $(".btn-dltpaq").prop("hidden", true)

                    }else{
                        $("#div-abierto").prop("hidden", true)
                        $(".btn-dltpaq").prop("hidden", false)

                    }

                } else if(id!=''){
                    notificaBad("Error al consultar el lote, notifica a sistemas.")
                }

            },
        });
    }
</script>


</html>