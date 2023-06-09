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
$_abierto = count($DataRendimientoAbierto) > 0 ? true : false;
//PARAMETROS PARA CARGA DEL FORMULARIO
$fechaEngrase = $_abierto ? $DataRendimientoAbierto[0]['fechaEngrase'] : '';
$semanaProduccion = $_abierto ? date("Y", strtotime($DataRendimientoAbierto[0]['fechaEngrase'])) . "-W" . $DataRendimientoAbierto[0]['semanaProduccion'] : '';
$fechaEmpaque = $_abierto ? $DataRendimientoAbierto[0]['fechaEmpaque'] : '';
$idCatProceso = $_abierto ? $DataRendimientoAbierto[0]['idCatProceso'] : '';
$loteTemola = $_abierto ? $DataRendimientoAbierto[0]['loteTemola'] : '';
$idCatPrograma = $_abierto ? $DataRendimientoAbierto[0]['idCatPrograma'] : '';
$idCatMateriaPrima = $_abierto ? $DataRendimientoAbierto[0]['idCatMateriaPrima'] : '';

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
                    <div class="col-md-4 col-lg-4 col-xs-6 col-sm-6">
                        <div class="card border">
                            <div class="card-header">
                                <h4>Registro de Hides para Prueba</h4>
                            </div>
                            <form id="formPruebas">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                            <label for="lote" class="form-label required"> Lote: </label>
                                            <select name="lote" style="width:100%" required class="form-control select2Form LotesProceso" id="lote"></select>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">1s</th>
                                                        <th class="text-center">2s</th>
                                                        <th class="text-center">3s</th>
                                                        <th class="text-center">4s</th>
                                                        <th class="text-center">20</th>
                                                        <th class="text-center">Total</th>

                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td class="table-active text-center" id="_1s">0</td>
                                                        <td class="table-active text-center" id="_2s">0</td>
                                                        <td class="table-active text-center" id="_3s">0</td>
                                                        <td class="table-active text-center" id="_4s">0</td>
                                                        <td class="table-active text-center" id="_20">0</td>
                                                        <td class="table-active text-center" id="total_s">0</td>
                                                    </tr>
                                                </tbody>

                                            </table>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                            <label for="fecha" class="form-label required"> Fecha de Prueba: </label>
                                            <input type="date" required class="form-control" name="fecha" id="fecha">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                            <label class="form-label required text-danger" for="lote"> Hides a Descontar: </label>
                                            <input type="number" required step="1" min="1" class="form-control focusCampo" name="hides" id="hides">
                                        </div>
                                    </div>

                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6"></div>
                                        <div class="col-md-6 text-rigth">
                                            <div id="bloqueo-btn-1" style="display:none">
                                                <button class="btn btn-TWM" type="button" disabled="">
                                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                    Espere...
                                                </button>

                                            </div>
                                            <div id="desbloqueo-btn-1">
                                                <button type="reset" onclick="clearForm('formPruebas')" class="button btn btn-danger">Cancelar</button>
                                                <button type="submit" class="button btn btn-success">Guardar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>
                    <div class="col-md-8 col-lg-8 col-xs-6 col-sm-6">
                        <div class="card border">
                            <div class="card-body" id="content-pruebas">

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
<script src="../assets/scripts/selectFiltros.js"></script>
<script>
    update('templates/PruebasHides/cargaPruebasHides.php', 'content-pruebas', 1);
    mostrar_info()
    /********** ALMACENAR PRUEBA ***********/
    $("#formPruebas").submit(function(e) {
        e.preventDefault();
        formData = $(this).serialize();
        $.ajax({
            url: '../Controller/pruebasHide.php?op=agregarpruebas',
            data: formData,
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                    setTimeout(() => {
                        bloqueoBtn("bloqueo-btn-1", 2)
                        update('templates/PruebasHides/cargaPruebasHides.php', 'content-pruebas', 1);
                        clearForm("formPruebas")
                    }, 1000);

                } else if (resp[0] == 0) {
                    notificaBad(resp[1])
                    bloqueoBtn("bloqueo-btn-1", 2)


                }
            },
            beforeSend: function() {
                bloqueoBtn("bloqueo-btn-1", 1)
            }

        });
    });

    function mostrar_info() {
        $('select#lote').on('change', function() {
            valor = $(this).val();
            $.ajax({
                    data: {
                        "ident": valor
                    },
                    type: "POST",
                    dataType: "json",
                    url: "../Controller/pruebasHide.php?op=detalleslote",
                    beforeSend: function() {
                        // setting a timeout
                        $("#_1s").text("")
                        $("#_2s").text("")
                        $("#_3s").html("<div class='spinner-border spinner-border-sm' role='status'><span class='sr-only'></span></div>")
                        $("#_4s").text("")
                        $("#_20").text("")
                        $("#total_s").text("")
                    },
                })
                .done(function(data, textStatus, jqXHR) {
                    if (data != null) {
                        $("#_1s").text(data["1s"] * 2)
                        $("#_2s").text(data["2s"] * 2)
                        $("#_3s").text(data["3s"] * 2)
                        $("#_4s").text(data["4s"] * 2)
                        $("#_20").text(data["_20"] * 2)
                        $("#total_s").text(data["total_s"] * 2)
                        $("#hides").prop("max", data["total_s"] * 2)
                    }else{
                        $("#_1s").text("0")
                        $("#_2s").text("0")
                        $("#_3s").text("0")
                        $("#_4s").text("0")
                        $("#_20").text("0")
                        $("#total_s").text("0")
                        $("#hides").prop("max", "0")
                    }


                }).fail(function(jqXHR, textStatus, errorThrown) {

                });
        });
    }
</script>

</html>