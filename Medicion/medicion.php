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
                        <select name="lotes" id="selectlotes" class="custom-select custom-select-lg">
                            <option value="" select>Selecciona Un Lote</option>
                            <option value="">35469</option>
                            <option value="">35689</option>
                            <option value="">40356</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="card col-md-7">
                        <div class="card-body">
                            <table class="table table-hover  table-sm">
                                <thead class="thead-dark">
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Num. Serie</th>
                                        <th scope="col">Área Ft<sup>2</sup></th>
                                        <th scope="col">Área Dm<sup>2</sup></th>
                                        <th scope="col">Red.</th>
                                        <th scope="col">Seleccion</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><input type="checkbox"></td>
                                        <td>00001</td>
                                        <td>30.12</td>
                                        <td>15.1</td>
                                        <td>0.25</td>
                                        <td>
                                            <select class="custom-select" name="calidad" id="cali">
                                                <option selected value="">TR</option>
                                                <option value="">1S</option>
                                                <option value="">2S</option>
                                                <option value="">3S</option>
                                                <option value="">4S</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><input type="checkbox"></td>
                                        <td>00002</td>
                                        <td>23.5</td>
                                        <td>12.2</td>
                                        <td>0.25</td>
                                        <td>
                                            <select class="custom-select" name="calidad" id="cali">
                                                <option selected value="">TR</option>
                                                <option value="">1S</option>
                                                <option value="">2S</option>
                                                <option value="">3S</option>
                                                <option value="">4S</option>
                                            </select>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <button class="btn btn-success btn-lg offset-md-9">Crear Paquete</button>
                        </div>
                    </div>

                    <div class="col-md-5 ">
                        <div class="card">
                            <div class="card-header" style="background-color:#ee5a36;">
                                <h3 class="text-white">Paquetes</h3>
                            </div>
                            <div class="card-body">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Seleccion</th>
                                            <th scope="col">Área</th>
                                            <th scope="col">
                                                <button class="btn btn-primary" data-toggle="collapse" data-target
                                                ="#collapselados" role="button" aria-expanded="false" aria-controls="collapselados">
                                                    Lados
                                                </button>
                                                <div class="collapse" id="collapselados">
                                                    <div class="card" style="width: 18rem;">
                                                        <ul class="list-group list-group-flush">
                                                            <li class="list-group-item">12:00 = 123</li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>1S</td>
                                            <td>340</td>
                                            <td>
                                                Miedo a la IA
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div>
                                <button class="btn btn-danger btn-lg offset-md-9">Finalizar</button>
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