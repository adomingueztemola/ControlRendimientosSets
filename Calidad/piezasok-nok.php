<?php
require_once 'seg.php';
$info = new Seguridad();
require_once "../include/connect_mvc.php";
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
<link rel="stylesheet" href="https://code.jquery.com/ui/1.10.1/themes/base/jquery-ui.css" />
<link rel="stylesheet" href="../dist/css/boton.css">

<style>
    .inputnok:focus {
        border-color: #FF2401;
        box-shadow: 0px 1px 1px rgba(0, 0, 0, 0) inset, 0px 0px 8px rgb(255, 0, 0);
    }
</style>

<style>
    .inputok:focus {
        border-color: #00FA00;
        box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.075) inset, 0px 0px 8px rgba(2, 255, 5, 1);
    }
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
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12" id="content-seleccionlote">
                                <div class="row border">
                                    <div class="col-lg-12 col-md-12 col-xs-12 col-sm-12">
                                        <label class="form-label">Lote:</label>
                                        <div class="input-group mb-3">
                                            <input autofocus id="autocomplete" class="form-control" title="Lotes Disponibles">
                                        </div>

                                    </div>
                                </div>
                                <div class="row border p-1">
                                    <div class="col-lg-12 col-md-12 col-xs-12 col-sm-12">
                                        <b>Usuario: <?= $nameUser ?></b>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <div class="row border">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <p> <b>Programa: <span id="lote-programa"></span></b></p>
                                        <p> <b>Fecha Engrase: <span id="lote-fechaEngrase"></span></b></p>
                                        <p> <b>Cueros: <span id="lote-cueros"></span></b></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>


                        <div class="row">
                            <div class="col-lg-8">
                                <input type="hidden" id="lote-id">
                                <input type="hidden" id="lote-teseo">
                                <input type="hidden" id="lote-pzasok">
                                <input type="hidden" id="lote-pzasnok">

                                <div class="container text-center">
                                    <div class="row">
                                        <div class="h2 col-lg-3">
                                            <label for="form-label">12.00</label>
                                        </div>
                                        <div class="col-lg-3 col-md-3 input-group mb-3"><input id="_12Teseo" readonly type="number" step="1" data-tipo="_12" min="0" class="form-control border border-info"></div>

                                        <div class="col-lg-3 col-md-3 input-group mb-3"><input id="_12NOK" type="number" step="1" data-tipo="_12" min="0" class="inputnok  sumatoria_s focusCampo form-control border border-danger"></div>
                                        <div class="col-lg-3 col-md-3 input-group mb-3"><input id="_12OK" readonly type="number" step="1" min="0" class="inputok _12 sumatoria_s focusCampo form-control border border-success"></div>

                                    </div>
                                </div>

                                <div class="container text-center">
                                    <div class="row">
                                        <div class="h2 col-lg-3">
                                            <label for="form-label">03.00</label>
                                        </div>
                                        <div class="col-lg-3 col-md-3 input-group mb-3"><input id="_3Teseo" readonly type="number" step="1" data-tipo="_3" min="0" class="form-control border border-info"></div>
                                        <div class="col-lg-3 col-md-3 input-group mb-3"><input id="_3NOK" type="number" step="1" min="0" data-tipo="_3" class="inputnok focusCampo sumatoria_s form-control border border-danger"></div>
                                        <div class="col-lg-3 col-md-3 input-group mb-3"><input id="_3OK" readonly type="number" step="1" min="0" class="inputok _3 focusCampo sumatoria_s form-control border border-success"></div>

                                    </div>
                                </div>

                                <div class="container text-center">
                                    <div class="row">
                                        <div class="h2 col-lg-3">
                                            <label for="form-label">06.00</label>
                                        </div>
                                        <div class="col-lg-3 col-md-3 input-group mb-3"><input id="_6Teseo" readonly type="number" step="1" data-tipo="_6" min="0" class="form-control border border-info"></div>
                                        <div class="col-lg-3 col-md-3 input-group mb-3"><input id="_6NOK" type="number" step="1" min="0" data-tipo="_6" class="inputnok focusCampo sumatoria_s form-control border border-danger"></div>
                                        <div class="col-lg-3 col-md-3 input-group mb-3"><input id="_6OK" readonly type="number" step="1" min="0" class="inputok _6 focusCampo sumatoria_s form-control border border-success"></div>

                                    </div>
                                </div>

                                <div class="container text-center">
                                    <div class="row">
                                        <div class="h2 col-lg-3">
                                            <label for="form-label">09.00</label>
                                        </div>
                                        <div class="col-lg-3 col-md-3 input-group mb-3"><input id="_9Teseo" readonly type="number" step="1" data-tipo="_9" min="0" class="form-control border border-info"></div>
                                        <div class="col-lg-3 col-md-3 input-group mb-3"><input id="_9NOK" type="number" step="1" min="0" data-tipo="_9" class="inputnok focusCampo sumatoria_s form-control border border-danger"></div>
                                        <div class="col-lg-3 col-md-3 input-group mb-3"><input id="_9OK" readonly type="number" step="1" min="0" class="inputok _9 focusCampo sumatoria_s form-control border border-success"></div>

                                    </div>
                                </div>
                                <div class="container text-center">
                                    <div class="h2 col-lg-4">
                                        <label for="form-label">Total: <span id="Total">0</span></label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="col-lg-12" style="height:95px">
                                    <div class="card-body mb-0">
                                        <div class="d-flex no-block align-items-center">
                                            <img src="../assets/images/TESEO.jpg" width="50%" alt="" srcset="">
                                            <div class="mx-5 text-rigth">
                                                <h2>
                                                    <font style="vertical-align: inherit;">
                                                        <font style="vertical-align: inherit;" id="teseo">0</font>
                                                    </font>
                                                </h2>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-12 bg-danger" style="height:95px">
                                    <div class="card-body mb-0">
                                        <div class="d-flex no-block align-items-center">
                                            <div class="text-white">
                                                <h2>
                                                    <font style="vertical-align: inherit;">
                                                        <font style="vertical-align: inherit;" id="pzasNok">0</font>
                                                    </font>
                                                </h2>
                                                <h6>
                                                    <font style="center-align: inherit;">
                                                        <font style="vertical-align: inherit;">NOK</font>
                                                    </font>
                                                </h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-12 bg-success">
                                    <div class="card-body" style="height:95px">
                                        <div class="d-flex no-block align-items-center">
                                            <div class="text-white">
                                                <h2>
                                                    <font style="vertical-align: inherit;">
                                                        <font style="vertical-align: inherit;" id="pzasOk">0</font>
                                                    </font>
                                                </h2>
                                                <h6>
                                                    <font style="vertical-align: inherit;">
                                                        <font style="vertical-align: inherit;">OK</font>
                                                    </font>
                                                </h6>
                                            </div>
                                            <div class="ml-auto">
                                                <span class="text-white display-6"><i class=""></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-lg-8">
                            </div>
                            <div class="col-lg-2">
                                <button class="btn-circle btn-lg btn-primary" data-toggle="modal" data-target="#busquedaModal" type="button" onclick=""><i class="fas fa-search"></i></button>
                            </div>
                            <div class="col-lg-2">
                                <div id="bloqueo-btn-1" style="display: none;">
                                    <div class="spinner-border text-danger" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                </div>
                                <div id="desbloqueo-btn-1">
                                    <button class="btn-circle btn-lg btn-TWM" type="button" onclick="cierraClasifPiezas()"><i class="fas fa-paper-plane"></i></button>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" data-keyboard="false" data-backdrop="static" id="busquedaModal" role="dialog" aria-labelledby="busquedaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-TWM text-white">
                    <h5 class="modal-title" id="busquedaModalLabel">Búsqueda de Lotes</h5>
                    <button type="button" onclick="limpiarGrafica()" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-xs-12 col-sm-12">
                            <label for="lotesClasificados">Lotes Clasificados</label>

                            <select id="lotesClasificados" onchange="cargaGraficaLlenado(this)" style="width:100%" id="lotesClasificados" class="form-control select2">
                            </select>
                        </div>
                    </div>
                    <div class="row" id="content-grafica"></div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" onclick="limpiarGrafica()" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</body>


<?= $info->creaFooter(); ?>
<script src="https://code.jquery.com/jquery-1.12.0.js"></script>
<?php include("../templates/libsJS.php"); ?>
<script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>
<script>
    /* 
     *  LOTES CLASIFICADOS DE SELECT2
     */
    $('#lotesClasificados').select2({
        placeholder: 'Selecciona un lote registrados',
        ajax: {
            url: '../Controller/pzasOKNOK.php?op=cargajsonpzasoknok',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    palabraClave: params.term // search term
                };
            },
            processResults: function(data) {
                return {
                    results: data
                };
            },
            cache: true
        }
    });
    /* 
     *  CARGA DE GRAFICA PARA VISUALIZACION DEL LLENADO DE LOTE PZAS PK, NOK
     */
    function cargaGraficaLlenado(select) {
        let id_select = $(select).val()
        if (id_select != '') {
            $('#content-grafica').html('<div class="loading text-center"><img src="../assets/images/loading.gif" alt="loading" /><br/>Un momento, por favor...</div>');
            $('#content-grafica').load('../templates/PzasOKNOK/graficaLlenadoLote.php?id=' + id_select);
        }
    }

    function limpiarGrafica() {
        $('#lotesClasificados').val('').trigger('change.select2');
        $('#content-grafica').html('')
    }
    $("#autocomplete").autocomplete({
        source: function(request, response) {
            $.ajax({
                type: "post",
                url: "../Controller/pzasOKNOK.php?op=cargajsonlotes",
                dataType: "json",
                data: {
                    term: request.term
                },
                success: function(data) {
                    // Al recibir los datos devueltos, los formateamos
                    // de modo que el Autocomplete entienda cual es el valor (`value`)
                    // y cual es el texto (`label`) para cada una de las opciones
                    var formatted = [];
                    $.each(data, function(index, info) {
                        formatted.push({
                            label: info.label,
                            value: info.value,
                            programa: info.programa,
                            f_fechaEngrase: info.f_fechaEngrase,
                            cueros: info.cueros,
                            _12OK: info._12OK,
                            _3OK: info._3OK,
                            _6OK: info._6OK,
                            _9OK: info._9OK,
                            _12NOK: info._12NOK,
                            _3NOK: info._3NOK,
                            _6NOK: info._6NOK,
                            _9NOK: info._9NOK,
                            pzasOk: info.pzasOk,
                            pzasNok: info.pzasNok,
                            teseo: info.pzasCortadasTeseo,
                            _12Teseo: info._12Teseo,
                            _3Teseo: info._3Teseo,
                            _6Teseo: info._6Teseo,
                            _9Teseo: info._9Teseo,


                        });
                    });
                    // Le pasamos los datos formateados al Autocomplete
                    response(formatted);

                }
            });
        },
        focus: function(event, ui) {
            $("#autocomplete").val(ui.item.label);
            return false;
        },
        select: function(event, ui) {
            $("#autocomplete").val(ui.item.label);
            $("#lote-id").val(ui.item.value);
            $("#lote-programa").html(ui.item.programa);
            $("#lote-fechaEngrase").html(ui.item.f_fechaEngrase);
            $("#lote-cueros").html(ui.item.cueros);

            $("#_12NOK").val(parseInt(ui.item._12NOK));
            $("#_12OK").val(parseInt(ui.item._12OK));
            $("#_12Teseo").val(parseInt(ui.item._12Teseo));

            $("#_3NOK").val(parseInt(ui.item._3NOK));
            $("#_3OK").val(parseInt(ui.item._3OK));
            $("#_3Teseo").val(parseInt(ui.item._3Teseo));

            $("#_6NOK").val(parseInt(ui.item._6NOK));
            $("#_6OK").val(parseInt(ui.item._6OK));
            $("#_6Teseo").val(parseInt(ui.item._6Teseo));

            $("#_9NOK").val(parseInt(ui.item._9NOK));
            $("#_9OK").val(parseInt(ui.item._9OK));
            $("#_9Teseo").val(parseInt(ui.item._9Teseo));

            $("#pzasOk").text(parseInt(ui.item.pzasOk));
            $("#pzasNok").text(parseInt(ui.item.pzasNok));

            $("#teseo").text(parseInt(ui.item.teseo));

            $("#lote-teseo").val(ui.item.teseo)

            $("#lote-pzasok").val(ui.item.pzasOk)
            $("#lote-pzasnok").val(ui.item.pzasNok)

            $("#Total").text(parseInt(ui.item.teseo));

            return false;
        },

        minLength: 2,
        delay: 500,

    });
    /*Validamos que la sumatoria de ambas piezas no exceda
     *piezas cortadas por scrap*/
    $(".sumatoria_s").change(function() {
        if ($("#lote-id").val() != '') {
            $(this).val($(this).val() == '' ? '0' : $(this).val())
            json = recorreIngresoPzas();
            if (json.result) {
                $("#Total").text(json.total);
                $("#pzasNok").text(json.resultnok);
                $("#pzasOk").text(json.resultok);

                $("#lote-pzasok").val(json.resultok)
                $("#lote-pzasnok").val(json.resultnok)

                updateCantidad($(this))
            } else {
                return 0;
            }
        }
    });
    //Funcion que recorre los inputs de las piezas
    function recorreIngresoPzas() {
        let result = 0;
        let resultok = 0;
        let resultnok = 0;
        let resultBoolean = true;
        $(".sumatoria_s").each(function() {
            result = parseInt(result) + parseInt($(this).val());
            if ($(this).hasClass("inputnok")) {
                classSearch = $(this).data("tipo");
                inptTeseo=  $("#"+classSearch+"Teseo");
                $("." + "inputok" + "." + classSearch).val(parseInt(inptTeseo.val()) - parseInt($(this).val()));
                resultnok += parseInt($(this).val())

            } else if ($(this).hasClass("inputok")) {
                resultok += parseInt($(this).val())
            }
        });
        if (result > $("#lote-teseo").val()) {
            notificaBad("Error, piezas exceden al total cortado de Teseo");
            resultBoolean = false;
        }
        resultjson = {
            "result": resultBoolean,
            "resultok": resultok,
            "resultnok": resultnok,
            "total": result,

        };
        return resultjson;
    }
    //Funcion de cambio de piezas ok, nok
    function updateCantidad(inp) {
        let n_id = $(inp).prop('id');
        let value = $(inp).val();
        let id = $("#lote-id").val();
        let pzasOk = $("#lote-pzasok").val();
        let pzasNok = $("#lote-pzasnok").val();

        $.ajax({
            url: '../Controller/pzasOKNOK.php?op=piezas',
            data: {
                id: id,
                codigo: n_id,
                value: value,
                pzasNok: pzasNok,
                pzasOk: pzasOk
            },
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                } else if (resp[0] == 0) {
                    notificaBad(resp[1])


                }
            },
            beforeSend: function() {}

        });
    }

    //Cierra Conteo de Piezas
    function cierraClasifPiezas() {
        //Verificamos que se hayan puesto todas las piezas cortadas por Teseo
        let pzasOk = parseInt($("#lote-pzasok").val());
        let pzasNok = parseInt($("#lote-pzasnok").val());
        let id = $("#lote-id").val();

        let total = parseInt(pzasOk) + parseInt(pzasNok);
        if (total < $("#lote-teseo").val()) {
            notificaBad("Error, piezas exceden al total cortado de Teseo");
            return 0;
        } else {
            //Peticion para almacenar datos
            $.ajax({
                url: '../Controller/pzasOKNOK.php?op=cerrarclasificacion',
                data: {
                    id: id,

                },
                type: 'POST',
                success: function(json) {
                    resp = json.split('|')
                    if (resp[0] == 1) {
                        notificaSuc(resp[1])
                        limpiarPanelControl()
                    } else if (resp[0] == 0) {
                        notificaBad(resp[1])


                    }
                    bloqueoBtn("bloqueo-btn-1", 2)

                },
                beforeSend: function() {
                    bloqueoBtn("bloqueo-btn-1", 1)
                }

            });
        }
    }

    //Limpiar panel de control
    function limpiarPanelControl() {
        $("#autocomplete").val('');
        $("#lote-id").val('');
        $("#lote-programa").html('');
        $("#lote-fechaEngrase").html('');
        $("#lote-cueros").html('');

        $("#_12NOK").val(parseInt('0'));
        $("#_12OK").val(parseInt('0'));
        $("#_12Teseo").val(parseInt('0'));

        $("#_3NOK").val(parseInt('0'));
        $("#_3OK").val(parseInt('0'));
        $("#_3Teseo").val(parseInt('0'));

        $("#_6NOK").val(parseInt('0'));
        $("#_6OK").val(parseInt('0'));
        $("#_6Teseo").val(parseInt('0'));

        $("#_9NOK").val(parseInt('0'));
        $("#_9OK").val(parseInt('0'));
        $("#_9Teseo").val(parseInt('0'));

        $("#pzasOk").text(parseInt('0'));
        $("#pzasNok").text(parseInt('0'));

        $("#teseo").text(parseInt('0'));
        $("#lote-teseo").val('0')

        $("#lote-pzasok").val('0')
        $("#lote-pzasnok").val('0')

        $("#Total").text('0')
    }
</script>

</html>