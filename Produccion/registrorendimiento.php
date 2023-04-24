<?php
require_once 'seg.php';
$info = new Seguridad();
require_once('../include/connect_mvc.php');
include("../Models/Mdl_ConexionBD.php");
include("../Models/Mdl_Proceso.php");
include("../Models/Mdl_Solicitudes.php");

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

$DataRendimientoAbierto = $obj_rendimiento->getRendimientoAbierto();
$DataRendimientoAbierto = $DataRendimientoAbierto == '' ? array() : $DataRendimientoAbierto;
if (!is_array($DataRendimientoAbierto)) {
    echo "<p class='text-danger'>Error, $DataRendimientoAbierto</p>";
    exit(0);
}
$_abierto = count($DataRendimientoAbierto) <= 0 ? false : true;
$id = count($DataRendimientoAbierto) <= 0 ? '0' : $DataRendimientoAbierto['0']['id'];
$loteTemola = count($DataRendimientoAbierto) <= 0 ? '0' : $DataRendimientoAbierto['0']['loteTemola'];

//Validacion De Lote En Edicion
$obj_solicitudes = new Solicitud($debug, $idUser);
$DataValidaUsoDelLote = $obj_solicitudes->validaCambioDePzas($id);
$DataValidaUsoDelLote = $DataValidaUsoDelLote == '' ? array() : $DataValidaUsoDelLote;
if (!is_array($DataValidaUsoDelLote)) {
    echo "<p class='text-danger'>Error, $DataValidaUsoDelLote</p>";
    exit(0);
}
$cambioPzas = count($DataValidaUsoDelLote) > 0 ? '1' : '0';

?>
<!DOCTYPE html>
<html dir="ltr" lang="<?= $info->lng; ?>">

<?php include("../templates/header.php"); ?>
<link href="../assets/libs/sweetalert2/dist/sweetalert2.min.css" rel="stylesheet">

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
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header bg-TWM text-white">
                                <h4><i class="fas fa-edit"></i> Editar Datos de Finales del Lote</h4>
                            </div>
                            <div class="card-body">
                                <form id="formAddRendimiento">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="lotePreRegistro" class="form-label required">Lotes listos para finalizar</label>
                                            <div class="input-group mb-3">
                                                <select name="lotePreRegistro" required id="lotePreRegistro" style="width:50%" class="form-control LotesFinales">
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row pt-2">
                                        <div class="col-md-2"></div>
                                        <div class="col-md-8">
                                            <div id="info-lote">
                                                <table class="table table-sm table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <td colspan="2" class="text-center">
                                                                <b>Asegúrese de que los datos actuales del lote son correctos.</b>
                                                            </td>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>FOLIO DEL LOTE</td>
                                                            <td id="c-lote"></td>
                                                        </tr>
                                                        <tr class="table-warning">
                                                            <td>PROGRAMA</td>
                                                            <td id="c-programa"></td>
                                                        </tr>
                                                        <tr>
                                                            <td>PROCESO</td>
                                                            <td id="c-proceso"></td>
                                                        </tr>
                                                        <tr>
                                                            <td>PROVEEDORES</td>
                                                            <td id="c-proveedores"></td>
                                                        </tr>
                                                        <tr class="table-danger">
                                                            <td><i class="fas fa-minus"></i> HIDES TOMADOS PARA PRUEBAS</td>
                                                            <td id="c-pruebas"></td>
                                                        </tr>
                                                        <tr class="table-warning">
                                                            <td>HIDES TOTALES</td>
                                                            <td id="c-hides"></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="col-md-2"></div>

                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-md-10">
                                            <div class="form-check">
                                                <input class="form-check-input" required type="checkbox" value="1" id="verificacion" name="verificacion">
                                                <label class="form-check-label form-label required" for="verificacion">
                                                    Confirmo que la información actual es correcta y actual.
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="input-group-append">
                                                <div id="bloqueo-btn-1" style="display:none">
                                                    <button class="btn btn-success btn-sm" type="button" disabled="">
                                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                    </button>
                                                    <button class="btn btn-success btn-sm" type="button" disabled="">
                                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                        Cargando ...
                                                    </button>
                                                
                                                </div>
                                                <div id="desbloqueo-btn-1">
                                                    <button type="submit" class="button btn btn-success btn-md"><i class="fas fa-check"></i> Siguiente</button>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </form>



                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="col-md-12 col-lg-12">

                                            <div class="row">
                                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="content-Yield">

                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row" id="btns-finalizar" hidden>


                                                <div class="col-md-6"></div>
                                                <div class="col-md-6 text-right">
                                                    <div id="bloqueo-btn-2" style="display:none">
                                                        <button class="btn btn-TWM" type="button" disabled="">
                                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                            Espere...
                                                        </button>

                                                    </div>
                                                    <div id="desbloqueo-btn-2">
                                                        <button type="button" onclick="eliminarPreRegistro(<?= $id ?>)" class="button btn btn-danger">Cancelar Pre-Registro</button>
                                                        <button type="button" hidden id="btn-finalizarYield" onclick="cierrePreRegistro(<?= $cambioPzas ?>)" class="button btn btn-success">Finalizar</button>
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
            </div>
        </div>
    </div>
</body>

<?= $info->creaFooter(); ?>
<?php include("../templates/libsJS.php"); ?>
<script src="../assets/scripts/calculaSemanaProduccion.js"></script>
<script src="../assets/scripts/validaLotePiel.js"></script>
<script src="../assets/scripts/selectFiltros.js"></script>
<script src="../assets/libs/sweetalert2/dist/sweetalert2.all.min.js"></script>

<script>
    mostrarInfo()

    update()
    <?php
    if (isset($_SESSION['CRESuccessRendimiento']) and $_SESSION['CRESuccessRendimiento'] != '') { ?>
        notificaSuc('<?= $_SESSION['CRESuccessRendimiento'] ?>')
    <?php
        unset($_SESSION['CRESuccessRendimiento']);
    }
    if (isset($_SESSION['CREErrorRendimiento']) and $_SESSION['CREErrorRendimiento'] != '') { ?>
        notificaBad('<?= $_SESSION['CREErrorRendimiento'] ?>')
    <?php
        unset($_SESSION['CREErrorRendimiento']);
    }
    if ($_abierto) {
        echo "$('#formAddRendimiento').find('input, textarea, button, select').attr('disabled', 'disabled');";
        echo "$('#formAddRendimiento').find('button').prop('hidden', true);";
        echo "$('#formAddRendimiento').find('input:checkbox, label.form-check-label').prop('hidden', true);";
        echo "$('#btns-finalizar').removeAttr('hidden');";
        echo "$('select#lotePreRegistro').select2('trigger','select', {
            data: {
                id: String('" . $id . "'),
                text: String('" . $loteTemola . "')
            }
        });  
        $('select#lotePreRegistro').trigger('change.select2'); // Notify only Select2 of changes
";
    }
    ?>

    function update() {
        $('#content-Yield').html('<div class="loading text-center"><img src="../assets/images/loading.gif" alt="loading" /><br/>Un momento, por favor...</div>');
        $('#content-Yield').load('../templates/Rendimiento/cargaTablaRendimiento.php');


    }

    function mostrarInfo() {
        $('select#lotePreRegistro').on('change', function() {
            valor = $(this).val();
            $.ajax({
                    data: {
                        "ident": valor
                    },
                    type: "POST",
                    dataType: "json",
                    url: "../Controller/pruebasLados.php?op=detalleslote",
                    beforeSend: function() {
                        // setting a timeout
                        // $("#_1s").text("")
                        // $("#_2s").text("")
                        // $("#_3s").html("<div class='spinner-border spinner-border-sm' role='status'><span class='sr-only'></span></div>")
                        // $("#_4s").text("")
                        // $("#_20").text("")
                        // $("#total_s").text("")
                    },
                })
                .done(function(data, textStatus, jqXHR) {
                    if (data != null) {
                        $("#c-lote").text(data["loteTemola"])
                        $("#c-programa").text(data["n_programa"])
                        $("#c-proceso").text(data["n_proceso"] + " (" + data["c_proceso"] + ")")
                        $("#c-proveedores").text(data["proveedores"])
                        $("#c-pruebas").text(data["total_pruebas"] * 2)
                        $("#c-hides").text(data["total_s"] * 2)



                    } else {
                        // $("#_1s").text("0")
                        // $("#_2s").text("0")
                        // $("#_3s").text("0")
                        // $("#_4s").text("0")
                        // $("#_20").text("0")
                        // $("#total_s").text("0")
                        // $("#hides").prop("max", "0")
                    }

                }).fail(function(jqXHR, textStatus, errorThrown) {

                });
        });


    }

    /********** INICIO DE RENDIMIENTO ***********/
    $("#formAddRendimiento").submit(function(e) {
        e.preventDefault();
        formData = $(this).serialize();
        $.ajax({
            url: '../Controller/rendimiento.php?op=seleccionarlote',
            data: formData,
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                    setTimeout(() => {
                        bloqueoBtn("bloqueo-btn-1", 2)
                        //  update()
                        $('#formAddRendimiento').find('input, textarea, button, select').attr('disabled', 'disabled');

                        $("#btns-init").attr('hidden', true);
                        $("#btns-finalizar").attr('hidden', false);
                        location.reload()

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
    /********** ELIMINAR PRE REGISTRO  ***********/
    function eliminarPreRegistro(id) {
        $.ajax({
            url: '../Controller/rendimiento.php?op=eliminarrendimiento',
            data: {
                id: id
            },
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    // notificaSuc(resp[1])
                    setTimeout(() => {
                        bloqueoBtn("bloqueo-btn-2", 2)
                        location.reload()
                    }, 1000);


                } else if (resp[0] == 0) {
                    notificaBad(resp[1])
                    bloqueoBtn("bloqueo-btn-2", 2)


                }
            },
            beforeSend: function() {
                bloqueoBtn("bloqueo-btn-2", 1)
            }

        });
    }
    /********** CIERRE PRE REGISTRO  ***********/
    function cierrePreRegistro(cambioPzas) {
        log_result = validaCamposLlenos()
        if (log_result) {
            $.ajax({
                url: '../Controller/rendimiento.php?op=cierrerendimiento',
                data: {
                    cambioPzas: cambioPzas
                },
                type: 'POST',
                success: function(json) {
                    resp = json.split('|')
                    if (resp[0] == 1) {
                        setTimeout(() => {
                            bloqueoBtn("bloqueo-btn-2", 2)
                            location.reload()
                        }, 1000);


                    } else if (resp[0] == 0) {
                        notificaBad(resp[1])
                        bloqueoBtn("bloqueo-btn-2", 2)


                    }
                },
                beforeSend: function() {
                    bloqueoBtn("bloqueo-btn-2", 1)
                }

            });
        }

    }
</script>

</html>