<?php
require_once 'seg.php';
$info = new Seguridad();
require_once('../include/connect_mvc.php');
include("../Models/Mdl_ConexionBD.php");
include("../Models/Mdl_Inventario.php");

$info->Acceso();
$idUser = $_SESSION['CREident'];
$nameUser = $_SESSION['CREnombreUser'];
setlocale(LC_TIME, 'es_ES.UTF-8');
$debug = 0;
$space = 1;
$obj_inventario = new Inventario($debug, $idUser);
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
                    <div class="col-lg-4 col-md-4 col-md-4 col-sm-4 col-xs-4">
                        <div class="card border">
                            <div class="card-body" id="">
                                <div class="row">
                                    <div class="col-md-12">
                                        <form id="filtrado">
                                            <div class="input-group mb-3">
                                                <label for="superlote">Lote a localizar</label>
                                                <select name="superlote" id="superlote" style="width:85%" class="form-control select2">
                                                    <option value="">Selecciona Lote</option>
                                                    <optgroup label="Lotes de Set's">
                                                        <?php
                                                        $DataLotes = $obj_inventario->getSuperLotes();
                                                        $tipoLote = 1;
                                                        $count = 0;

                                                        foreach ($DataLotes as $key => $value) {
                                                            if ($DataLotes[$key]['tipoProceso'] == '2' and $count == 0) {
                                                                echo "</optgroup><optgroup label='Lotes de Metros'>";
                                                                $count++;
                                                            }
                                                            echo "<option data-proceso='{$DataLotes[$key]['tipoProceso']}' data-tipo='{$DataLotes[$key]['tipoLote']}' data-disponible='{$DataLotes[$key]['pzasTotales']}' 
                                                                 value='{$DataLotes[$key]['idRendimiento']}'>{$DataLotes[$key]['loteTemola']} ({$DataLotes[$key]['n_materia']})</option>";
                                                        }


                                                        ?>

                                                </select>
                                                <div class="input-group-append">
                                                    <button class="btn btn-TWM" type="submit"><i class="ti-search"></i></button>
                                                </div>
                                            </div>
                                        </form>

                                    </div>

                                </div>
                                <div class="row">
                                    <div class="col-md-12" id="info-inventarios">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8 col-md-8 col-md-8 col-sm-8 col-xs-8">
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
                                        <div id="content-inventario"></div>

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

    function update() {
        $('#content-inventario').html('<div class="loading text-center"><img src="../assets/images/loading.gif" alt="loading" /><br/>Un momento, por favor...</div>');
        $('#content-inventario').load('../templates/Almacen/kardexTrazabilidad.php');
        clearForm("filtrado");

    }


    /*************** FILTRADO DE TABLA *********************/
    $("#filtrado").submit(function(e) {
        e.preventDefault();
        formData = $(this).serialize();
        $.ajax({
            url: '../templates/Almacen/kardexTrazabilidad.php',
            data: formData,
            type: 'POST',
            success: function(respuesta) {
                $('#content-inventario').html(respuesta);


            },
            beforeSend: function() {
                $('#content-inventario').html('<div class="loading text-center"><img src="../assets/images/loading.gif" alt="loading" /><br/>Un momento, por favor...</div>');


            }

        });
        $.ajax({
            url: '../templates/Almacen/infoInventarios.php',
            data: formData,
            type: 'POST',
            success: function(respuesta) {
                $('#info-inventarios').html(respuesta);


            },
            beforeSend: function() {
                $('#info-inventarios').html('<div class="loading text-center"><img src="../assets/images/loading.gif" alt="loading" /><br/>Un momento, por favor...</div>');


            }

        });
    });
</script>

</html>