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
                <?php include("../templates/namePage.php"); ?>
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-4 col-xl-3">
                                <!-- Nav tabs -->
                                <div class="nav flex-column nav-pills" id="v-pills-solicitudes" role="tablist" aria-orientation="vertical">
                                </div>
                            </div>
                            <div class="col-lg-8 col-xl-9">
                                <div class="tab-content" id="v-pills-tabContent">
                                    <input type="hidden" name="id" id="id">
                                    <div class="tab-pane fade show active" id="v-pills-home" role="tabpanel" aria-labelledby="v-pills-home-tab">.
                                        <div class="row">
                                            <div class="col-12">
                                                <table class="table table-sm table-bordered">
                                                    <tbody>
                                                        <tr>
                                                            <td>Lote a Modificar</td>
                                                            <td>
                                                                <div class="row">
                                                                    <div class="col-10">
                                                                        <input type="text" id="lote" class="form-control" disabled>
                                                                    </div>
                                                                    <div class="col-1 mt-2">
                                                                        <span id="estatus-lote"></span>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td><input type="text" id="programa" class="form-control" disabled></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Fecha de Envío</td>
                                                            <td><input type="date" id="fechaEnvio" class="form-control" disabled></td>
                                                            <td rowspan="6">
                                                                <span><b>Motivo:</b></span>
                                                                <span id="motivo"></span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>Usuario</td>
                                                            <td><input type="text" id="nUsuario" class="form-control" disabled></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2" class="text-center">
                                                                <h4>Información sobre áreas del lote</h4>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <div class="row">
                                                                    <div class="col-12">Área de Teseo®</div>
                                                                    <div class="col-12"><span class="text-info" id="edit-areaTeseo"></span></div>
                                                                </div>

                                                            </td>
                                                            <td><input type="text" id="areaTeseo" class="form-control" disabled></td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <div class="row">
                                                                    <div class="col-12">Yield</div>
                                                                    <div class="col-12"><span class="text-info" id="edit-yield"></span></div>
                                                                </div>
                                                            </td>
                                                            <td><input type="text" id="yield" class="form-control" disabled></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2" class="text-center">
                                                                <h4>Movimiento de Piezas del Lote</h4>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <div class="row">
                                                                    <div class="col-12">12:00</div>
                                                                    <div class="col-12"><span class="text-info" id="edit-12"></span></div>
                                                                </div>
                                                            </td>
                                                            <td><input type="number" id="_12" class="form-control" disabled></td>

                                                            <td><input type="number" id="_12Dif" class="form-control" disabled></td>
                                                        </tr>

                                                        <tr>
                                                            <td>
                                                                <div class="row">
                                                                    <div class="col-12">03:00</div>
                                                                    <div class="col-12"><span class="text-info" id="edit-03"></span></div>
                                                                </div>
                                                            </td>
                                                            <td><input type="number" id="_3" class="form-control" disabled></td>

                                                            <td><input type="number" id="_3Dif" class="form-control" disabled></td>
                                                        </tr>

                                                        <tr>
                                                            <td>
                                                                <div class="row">
                                                                    <div class="col-12">06:00</div>
                                                                    <div class="col-12"><span class="text-info" id="edit-06"></span></div>
                                                                </div>
                                                            </td>
                                                            <td><input type="number" id="_6" class="form-control" disabled></td>

                                                            <td><input type="number" id="_6Dif" class="form-control" disabled></td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <div class="row">
                                                                    <div class="col-12">09:00</div>
                                                                    <div class="col-12"><span class="text-info" id="edit-09"></span></div>
                                                                </div>
                                                            </td>
                                                            <td><input type="number" id="_9" class="form-control" disabled></td>

                                                            <td><input type="number" id="_9Dif" class="form-control" disabled></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <hr>
                                        <div class="row">
                                            <div class="col-8"></div>
                                            <div class="col-4">
                                                <div id="bloqueo-btn-1" style="display:none">
                                                    <button class="btn btn-TWM btn-lg" type="button" disabled="">
                                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                        Espere...
                                                    </button>

                                                </div>
                                                <div id="desbloqueo-btn-1">
                                                    <button class="btn btn-danger btn-md" onclick="rechazarSolicitud()">Rechazar</button>
                                                    <button id="btn-aceptar" class="btn btn-success btn-md">Aceptar</button>
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
    <?= $info->creaFooter(); ?>

</body>

</html>
<?php include("../templates/libsJS.php"); ?>
<script src="../assets/scripts/selectFiltros.js"></script>
<script>
    getData()

    function getData() {
        $.ajax({
            url: '../Controller/solicitudesEdicion.php?op=getsolicitudesteseo',
            type: 'POST',
            async: false,
            dataType: "json",
            success: function(respuesta) {
                count = 1;
                identAct = 0;
                respuesta.forEach(element => {
                    active = count == 1 ? "active" : "";
                    identAct = count == 1 ? element.id : identAct;
                    $("#v-pills-solicitudes").append(`<a class="nav-link ${active}" id="v-pills-${element.id}-tab" 
                    data-toggle="pill" onclick="getInfoSolic(${element.id})" href="#v-pills-home" role="tab" 
                    aria-controls="v-pills-${element.id}" aria-selected="true">${element.f_fechaEnvio}: ${element.loteTemola}</a>`)
                    count++;
                });
                getInfoSolic(identAct)
            },


        });
    }

    function getInfoSolic(id) {
        $.ajax({
            url: '../Controller/solicitudesEdicion.php?op=getdetsolicitud',
            type: 'POST',
            async: false,
            data: {
                id: id
            },
            dataType: "json",
            success: function(respuesta) {
                $("#lote").val(respuesta.loteTemola)
                $("#id").val(respuesta.id)

                switch (respuesta.estado) {
                    case '2':
                        $("#estatus-lote").html("<i class='fas fa-unlock-alt text-success'></i>")
                        break;
                    case '4':
                        $("#estatus-lote").html("<i class='fas fa-lock text-danger'></i>")
                        break;
                }
                $("#programa").val(respuesta.nPrograma)
                $("#fechaEnvio").val(respuesta.fechaEnvio)
                $("#nUsuario").val(respuesta.n_usuario)
                $("#areaTeseo").val(respuesta.areaFinal)
                $("#yield").val(respuesta.yieldFinalReal)
                $("#_12").val(respuesta._12Teseo)
                $("#_12Dif").val(respuesta.dif_12)
                result_12 = validaDif($("#_12Dif"))

                $("#_3").val(respuesta._3Teseo)
                $("#_3Dif").val(respuesta.dif_3)
                result_3 = validaDif($("#_3Dif"))

                $("#_6").val(respuesta._6Teseo)
                $("#_6Dif").val(respuesta.dif_6)
                result_6 = validaDif($("#_6Dif"))

                $("#_9").val(respuesta._9Teseo)
                $("#_9Dif").val(respuesta.dif_9)
                result_9 = validaDif($("#_9Dif"))

                $("#motivo").text(respuesta.motivo)
                $("#edit-09").text(Number(respuesta._9TeseoLte).toLocaleString('es-MX'))
                $("#edit-03").text(Number(respuesta._3TeseoLte).toLocaleString('es-MX'))
                $("#edit-12").text(Number(respuesta._12TeseoLte).toLocaleString('es-MX'))
                $("#edit-06").text(Number(respuesta._6TeseoLte).toLocaleString('es-MX'))
                $("#edit-areaTeseo").text(Number(respuesta.areaFinalLte).toLocaleString('es-MX'))
                $("#edit-yield").text(Number(respuesta.yieldInicialTeseoLte).toLocaleString('es-MX'))

                if (result_12 && result_3 && result_6 && result_9) {
                    $("#btn-aceptar").prop("hidden", false)
                } else {
                    $("#btn-aceptar").prop("hidden", true)

                }

            },


        });
    }

    function validaDif(input) {
        value = $(input).val()
        _return = true;
        if (value < 0) {
            $(input).css("background-color", "#F3A2A2");
            _return = false;

        }
        if (value >= 0) {
            $(input).css("background-color", "#A2F3DA");

        }

        return _return

    }

    function rechazarSolicitud() {
        id = $("#id").val()
        $.ajax({
            url: '../Controller/solicitudesEdicion.php?op=rechazarsolicitud',
            data: {
                id: id
            },
            type: 'POST',
            success: function(respuesta) {
                var resp = respuesta.split('|');
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                    bloqueoBtn("bloqueo-btn-1", 2)
                    nextTab=$("#v-pills-"+id+"-tab").next("a")
                    arrayTitle= nextTab.attr("id").split(',');
                    $("#v-pills-"+id+"-tab").remove()
                    nextTab.addClass('active');
                    getInfoSolic(arrayTitle[2])

                } else {
                    notificaBad(resp[1])
                    bloqueoBtn("bloqueo-btn-1", 2)

                }
            },
            beforeSend: function() {
                bloqueoBtn("bloqueo-btn-1", 1)

            }


        });
    }
</script>