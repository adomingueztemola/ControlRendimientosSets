<?php
require_once 'seg.php';
$info = new Seguridad();
include("../assets/scripts/cadenas.php");
require_once "../include/connect_mvc.php";
include('../Models/Mdl_ConexionBD.php');
include('../Models/Mdl_Rendimiento.php');
$info->Acceso();
$idUser = $_SESSION['CREident'];
$nameUser = $_SESSION['CREnombreUser'];
setlocale(LC_TIME, 'es_ES.UTF-8');
$debug = 0;
$space = 1;
$obj_rendimiento = new Rendimiento($debug, $idUser);
$numSemana = formatoFecha("W");
$DataLotes = $obj_rendimiento->getLoteXSemana($numSemana);
$f_numLotes = formatoMil($DataLotes[0]['total'], 0);
$DataLotes = $obj_rendimiento->getPzasRechazadasXSemana($numSemana);
$f_pzasRechazadas = formatoMil($DataLotes[0]['total']);
$DataLotes = $obj_rendimiento->getSetsEmpacadosXSemana($numSemana);
$f_setsEmpacados = formatoMil($DataLotes[0]['total']);
?>
<!DOCTYPE html>
<html dir="ltr" lang="<?= $info->lng; ?>">

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
                <div class="card-group">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="d-flex no-block align-items-center">
                                        <div>
                                            <i class="ti-calendar font-20 text-muted"></i>
                                            <p class="font-16 m-b-5 text-TWM">Semana de Producción</p>
                                        </div>
                                        <div class="ml-auto">
                                            <h1 class="font-light text-right"> <?= formatoFecha("W") ?>
                                            </h1>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">

                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- Column -->
                    <!-- Column -->
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="d-flex no-block align-items-center">
                                        <div>
                                            <i class="fas fa-dolly font-20  text-muted"></i>
                                            <p class="font-16 m-b-5 text-TWM">Lotes Registrados</p>
                                        </div>
                                        <div class="ml-auto">
                                            <h1 class="font-light text-right"><?= $f_numLotes ?></h1>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <?= formatoFecha("WY") ?>

                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Column -->
                    <!-- Column -->

                    <div class="card">
                        <button class="btn button" data-toggle="modal" data-target="#detalleModal">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="d-flex no-block align-items-center ">
                                            <div>
                                                <p class="font-16 m-b-5 text-TWM">
                                                    Pzas. Scrap</p>
                                            </div>
                                            <div class="ml-auto">
                                                <h1 class="font-light text-right"><?= $f_pzasRechazadas ?></h1>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-12">
                                        <?= formatoFecha("WY") ?>

                                    </div>
                                </div>
                            </div>
                        </button>

                    </div>
                    <!-- Column -->
                    <!-- Column -->
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="d-flex no-block align-items-center">
                                        <div>
                                            <i class="ti-dropbox font-20 text-muted"></i>
                                            <p class="font-16 m-b-5 text-TWM">Set's Empacados</p>
                                        </div>
                                        <div class="ml-auto">
                                            <h1 class="font-light text-right"><?= $f_setsEmpacados ?></h1>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <?= formatoFecha("WY") ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-9 col-lg-9 col-sm-12 col-xs-12">
                        <div class="card border">
                            <div class="card-body">

                                <div class="row mb-1">
                                    <div class="col-md-5">
                                        <form class="filtrado" id="filtrado-conteolotes">
                                            <div class="input-group mb-3">
                                                <input type="number" name="anio" placeholder="YYYY" value="<?= date("Y") ?>" step="1" min="2017" max="2100" class="form-control" placeholder="" aria-label="" aria-describedby="basic-addon1">
                                                <div class="input-group-append">
                                                    <button class="btn btn-info" type="submit"><i class="ti-search"></i></button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="col-md-6"></div>
                                    <div class="col-md-1 text-right">
                                        <button name="anio" class="btn button btn-rounded btn-sm btn-light" onclick="actualizarGrafica()" title="Actualizar Historial"> <i class="fas fa-history"></i></button>
                                    </div>
                                </div>
                                <div id="contentGrafica">

                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="col-md-3 col-lg-3 col-sm-12 col-xs-12">
                        <div class="card border">
                            <div class="card-header bg-TWM text-white">
                                <h6>Requerimientos de piezas para venta</h6>
                            </div>
                            <div class="card-body" style="height:450px; overflow: scroll;">
                                <div id="contentRequerimientos">
                                </div>

                            </div>
                        </div>
                    </div>
                </div>


            </div>

        </div>


        <div class="modal fade" id="detalleModal" tabindex="-1" role="dialog" aria-labelledby="detalleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-TWM text-white">
                        <h5 class="modal-title" id="detalleModalLabel">Detallado de Recuperación de la Semana</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Lote</th>
                                    <th class='text-center'>Pzas. Scrap Iniciales</th>
                                    <th class='text-center'>Pzas. Recuperadas</th>
                                    <th class='text-center'>Pzas. Actuales en Scrap</th>

                                </tr>
                            </thead>
                            <?php
                            $Data = $obj_rendimiento->getKardexRechazados($numSemana);
                            $Data = Excepciones::validaConsulta($Data);
                            if(count($Data)<=0){
                                echo "<tr><td colspan='4' class='text-center'>Sin Información Registradas</td></tr>";
                            }
                            foreach ($Data as $key => $value) {
                                $f_ScrapInicial= formatoMil($value['pzasIniciales']);
                                $f_Recuperadas= formatoMil($value['pzasRecuperadas']);
                                $f_PzasActuales= formatoMil($value['pzasActuales']);

                                echo "
                                <tr>
                                    <td>{$value['loteTemola']}</td>
                                    <td class='text-center'>{$f_ScrapInicial}</td>
                                    <td class='text-center'>{$f_Recuperadas}</td>
                                    <td class='text-center'>{$f_PzasActuales}</td>

                                </tr>
                                ";
                            }


                            ?>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

</body>


<?= $info->creaFooter(); ?>
<?php include("../templates/libsJS.php"); ?>

<script>
    actualizarGrafica()
    actualizarRequerimientos()
    /*********** ACTUALIZA GRAFICA***************/
    function actualizarGrafica() {
        $('#contentGrafica').html('<div class="loading text-center"><img src="../assets/images/loading.gif" alt="loading" /><br/>Un momento, por favor...</div>');
        $('#contentGrafica').load('../templates/Rendimiento/Estadistica/grafica_lotes_pieles.php');


    }
    /*********** ACTUALIZA LISTA DE REQUERIMIENTOS***************/
    function actualizarRequerimientos() {
        cargaContenido("contentRequerimientos", "../templates/Ventas/cargaRequerimientosDeVenta.php", '1')

    }
    /*************** FILTRADO DE SET'S *********************/
    $(".filtrado").submit(function(e) {
        e.preventDefault();
        id = $(this).prop("id");
        switch (id) {
            case "filtrado-conteolotes":
                url = '../templates/Rendimiento/Estadistica/grafica_lotes_pieles.php'
                content = "contentGrafica"
                break;

        }
        formData = $(this).serialize();
        $.ajax({
            url: url,
            data: formData,
            type: 'POST',
            success: function(respuesta) {
                $('#' + content).html(respuesta);


            },
            beforeSend: function() {}

        });
    });
</script>

</html>