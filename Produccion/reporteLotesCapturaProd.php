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
$obj_programa = new Programa($debug, $idUser);
$obj_proceso = new ProcesoSecado($debug, $idUser);
$obj_materia = new MateriaPrima($debug, $idUser);
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
                                        <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                            <label for="date-range">Búsqueda de por Rangos de Fechas: </label>
                                            <div class="input-daterange input-group" id="date-range">
                                                <input type="text" class="form-control" name="date-start" value="">
                                                <div class="input-group-append">
                                                    <span class="input-group-text bg-TWM b-0 text-white">AL</span>
                                                </div>
                                                <input type="text" class="form-control" name="date-end" value="">
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
                                                $DataProceso = $obj_proceso->getProcesos("pr.estado='1'", "pr.tipo='1'");
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
                    <div class="col-lg-12 col-md-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="card border">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-11"></div>
                                    <div class="col-md-1 text-right">
                                        <button class="btn button btn-rounded btn-sm btn-light" onclick="update()" title="Actualizar Historial"> <i class="fas fa-history"></i></button>
                                    </div>
                                </div>


                                <div class="row">
                                    <div class="col-md-12">
                                        <ul class="nav nav-pills m-t-30 m-b-30">
                                            <li class=" nav-item"> <a href="javascript(0)" onclick="filtrado(-1)" class="nav-link active" id="nav-todo" data-toggle="tab" aria-expanded="false">Todo</a> </li>
                                            <li class="nav-item"> <a href="javascript(0)" onclick="filtrado(1)" class="nav-link" id="nav-empacados" data-toggle="tab" aria-expanded="false">Lotes Empacados</a> </li>
                                            <li class="nav-item"> <a href="javascript(0)" onclick="filtrado(2)" class="nav-link" id="nav-proceso" data-toggle="tab" aria-expanded="true">Lotes En Proceso</a> </li>
                                            <li class="nav-item"> <a href="javascript(0)" onclick="filtrado(3)" class="nav-link" id="nav-metros" data-toggle="tab" aria-expanded="true">Lotes de Metros</a> </li>

                                        </ul>
                                        <div id="content-lotes"></div>

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
    update()

    jQuery('#date-range').datepicker({
        toggleActive: true,
        format: 'dd/mm/yyyy',
        language: "es",
        todayHighlight: true



    });
    /*********** ACTUALIZA LISTA DE LOTES***************/
    function update() {
        cargaContenido("content-lotes", "../templates/Rendimiento/cargaReporteLotesCapturaProd.php", '1')

    }


    /*************** FILTRADO DE TABLA *********************/
    $("#filtrado").submit(function(e) {
        e.preventDefault();
        formData = $(this).serialize();
        $.ajax({
            url: '../templates/Rendimiento/cargaReporteLotesCapturaProd.php',
            data: formData,
            type: 'POST',
            success: function(respuesta) {
                $('#content-lotes').html(respuesta);


            },
            beforeSend: function() {
                $('#content-lotes').html('<div class="loading text-center"><img src="../assets/images/loading.gif" alt="loading" /><br/>Un momento, por favor...</div>');


            }

        });
    });

    /*********************** Filtrado *************************/
    function filtrado(idBusqueda) {
        //Casteo de titulos
        let title = '';
        switch (idBusqueda) {
            case 1:
                $("#nav-empacados").addClass("active")
                $("#nav-metros").removeClass("active")
                $("#nav-proceso").removeClass("active")
                $("#nav-todo").removeClass("active")

                break;
            case 2:
                $("#nav-proceso").addClass("active")
                $("#nav-empacados").removeClass("active")
                $("#nav-metros").removeClass("active")
                $("#nav-todo").removeClass("active")

                break;
            case 3:
                $("#nav-metros").addClass("active")
                $("#nav-proceso").removeClass("active")
                $("#nav-empacados").removeClass("active")
                $("#nav-todo").removeClass("active")

                break;
            case -1:
                $("#nav-todo").addClass("active")
                $("#nav-empacados").removeClass("active")
                $("#nav-proceso").removeClass("active")
                $("#nav-metros").removeClass("active")

                break;

        }

        $.ajax({
            type: 'POST',
            url: '../templates/Rendimiento/cargaReporteLotesCapturaProd.php',
            data: {
                id: idBusqueda
            },
            success: function(respuesta) {
                $('#content-lotes').html(respuesta);

            },
            beforeSend: function() {
                $('#content-lotes').html('<div class="loading text-center"><img src="../assets/images/loading.gif" alt="loading" /><br/>Un momento, por favor...</div>');
            }
        });
    }
</script>

</html>