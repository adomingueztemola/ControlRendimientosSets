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
                    <div class="col-lg-12 col-md-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="card border">
                            <div class="card-body" id="">
                                <form id="filtrado">
                                    <div class="row">
                                        <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                            <label for="date-range">Búsqueda de por Rangos de Fechas: </label>
                                            <div class="input-daterange input-group" id="date-range">
                                                <input type="text" class="form-control" name="date-start" value="<?= date("01/m/Y") ?>">
                                                <div class="input-group-append">
                                                    <span class="input-group-text bg-TWM b-0 text-white">AL</span>
                                                </div>
                                                <input type="text" class="form-control" name="date-end" value="<?= date("t/m/Y") ?>">
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                            <label for="programa">Programa:</label>
                                            <select class="form-control select2" style="width:100%" name="programa" id="programa">
                                                <option value="">Todos los Programas</option>
                                                <?php
                                                $DataPrograma = $obj_programa->getPrograma("p.estado='1'", "p.tipo='1'");
                                                foreach ($DataPrograma as $key => $value) {
                                                    echo "<option value='{$DataPrograma[$key]['id']}'>{$DataPrograma[$key]['nombre']}</option>";
                                                }
                                                ?>
                                            </select>

                                        </div>

                                        <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12">
                                            <label for="proceso">Proceso:</label>
                                            <select class="form-control select2" style="width:100%" name="proceso" id="proceso">
                                                <option value="">-</option>
                                                <?php
                                                $DataProceso = $obj_proceso->getProcesos("pr.estado='1'");
                                                foreach ($DataProceso as $key => $value) {
                                                    echo "<option value='{$DataProceso[$key]['id']}'>{$DataProceso[$key]['codigo']}</option>";
                                                }
                                                ?>
                                            </select>

                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                            <label for="materia">Materia Prima:</label>
                                            <select class="form-control select2" style="width:100%" name="materia" id="materia">
                                                <option value="">Todos las Materias Primas</option>
                                                <?php
                                                $DataMateria = $obj_materia->getMaterias("mt.estado='1'");
                                                foreach ($DataMateria as $key => $value) {
                                                    echo "<option value='{$DataMateria[$key]['id']}'>{$DataMateria[$key]['nombre']}</option>";
                                                }
                                                ?>
                                            </select>

                                        </div>
                                        <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 pt-4 mt-1">
                                            <button class="btn button btn-TWM"> Filtrar</button>
                                        </div>
                                    </div>
                                </form>

                            </div>

                        </div>
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


    function update() {
        $('#content-lotes').html('<div class="loading text-center"><img src="../assets/images/loading.gif" alt="loading" /><br/>Un momento, por favor...</div>');
        $('#content-lotes').load('../templates/Rendimiento/cargaControlAreas.php');


    }
    /*************** FILTRADO DE TABLA *********************/
    $("#filtrado").submit(function(e) {
        e.preventDefault();
        formData = $(this).serialize();
        $.ajax({
            url: '../templates/Rendimiento/cargaControlAreas.php',
            data: formData,
            type: 'POST',
            success: function(respuesta) {
                $('#content-lotes').html(respuesta);


            },
            beforeSend: function() {}

        });
    });
</script>

</html>