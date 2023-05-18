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
<link href="../assets/libs/morris.js/morris.css" rel="stylesheet">

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
                            <div class="col-md-8">
                                <select name="lotes" id="selectlotes" class="custom-select custom-select-lg">
                                    <option value="" select>Selecciona Un Lote</option>
                                    <option value="">35469</option>
                                    <option value="">35689</option>
                                    <option value="">40356</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-success btn-lg">Seleccionar</button>
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
                        <div id="contentTabla1">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        arriba
    </div>

</body>


<?= $info->creaFooter(); ?>
<?php include("../templates/libsJS.php"); ?>

<script>
    cargapaquete()
    function cargapaquete() {
        $('#contentTabla1').html('<div class="loading text-center"><img src="../assets/images/loading.gif" alt="loading" /><br/>Un momento, por favor...</div>');
        $('#contentTabla1').load('../templates/Medicion/EtiquetasPaquetes.php');
    }
</script>

<script>
    crearpaquete()
    function crearpaquete() {
        $('#contentCrear').html('<div class="loading text-center"><img src="../assets/images/loading.gif" alt="loading" /><br/>Un momento, por favor...</div>');
        $('#contentCrear').load('../templates/Medicion/CrearPaquete.php');
    }
</script>


<script>
    actualizarGrafica()

    function actualizarGrafica() {
        $('#contentGrafica').html('<div class="loading text-center"><img src="../assets/images/loading.gif" alt="loading" /><br/>Un momento, por favor...</div>');
        $('#contentGrafica').load('../templates/Rendimiento/Estadistica/grafica_lotes_marcados.php');


    }
    /*************** FILTRADO DE SET'S *********************/
    $(".filtrado").submit(function (e) {
        e.preventDefault();
        id = $(this).prop("id");
        switch (id) {
            case "filtrado-conteolotes":
                url = '../templates/Rendimiento/Estadistica/grafica_lotes_marcados.php'
                content = "contentGrafica"
                break;

        }
        formData = $(this).serialize();
        $.ajax({
            url: url,
            data: formData,
            type: 'POST',
            success: function (respuesta) {
                $('#' + content).html(respuesta);


            },
            beforeSend: function () { }

        });
    });
</script>

</html>