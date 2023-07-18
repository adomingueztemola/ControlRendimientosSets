<?php
require_once 'seg.php';
$info = new Seguridad();
require_once('../include/connect_mvc.php');


$info->Acceso();
$idUser = $_SESSION['CREident'];
$nameUser = $_SESSION['CREnombreUser'];
setlocale(LC_TIME, 'es_ES.UTF-8');
$debug = 0;
$space = 1;
$data = (!empty($_GET['data']) and $_GET['data'] != '') ? trim($_GET['data']) : '';
$_configura = false;





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
                <?php include("../templates/namePage.php"); ?>
                <div class="row">
                    <div class="col-md-6 col-lg-6">
                        <div class="card border">
                            <div class="card-body" id="">
                                <form id="formAddRendimiento">
                                    <div class="row">
                                        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                            <label class="form-label required" for="finalDate">Fecha Final:</label>
                                            <input class="form-control" type="date" name="fechaFinal" onchange="setSemanaInput('finalDate','productionWeek')" id="finalDate" value="" required></input>

                                        </div>

                                        <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                                            <label class="form-label required" for="productionWeek">Semana de Producción:</label>
                                            <input class="form-control" type="week" value="" name="semanaProduccion" id="productionWeek" required></input>
                                        </div>

                                    </div>


                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <label class="form-label required" for="lot">Lote de Temola:</label>
                                            <span id="bloqueo-btn-res" style="display:none">
                                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>

                                            </span>
                                            <span id="desbloqueo-btn-res">
                                                <span id="resultbusq"></span>
                                            </span>

                                            <input class="form-control" type="text" name="lote" value="" id="lot" required></input>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <label class="form-label required" for="program">Programa:</label>
                                            <select class="form-control ProgramasEtiqCalzFilter" name="programa" style="width:100%" id="program" required>

                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <label class="form-label required" for="rawMaterial">Materia Prima:</label>
                                            <select class="form-control MateriaPrimaFilter" name="materiaPrima" style="width:100%" id="rawMaterial" required>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <label class="form-label required" for="provider">Proveedor:</label>
                                            <select class="form-control ProveedorFilter" name="proveedor" style="width:100%" id="provider" required>
                                            </select>

                                        </div>
                                    </div>

                                    <div class="row mt-2">
                                        <div class="col-md-12">
                                            <table class="table table-sm">
                                                <thead class="bg-TWM text-white">
                                                    <tr>
                                                        <th>1s</th>
                                                        <th>2s</th>
                                                        <th>3s</th>
                                                        <th>4s</th>
                                                        <th>Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <input type="number" name="1s" id="1s" class="form-control sumatoria_s focusCampo" value="" step="0.01" min="0">
                                                        </td>
                                                        <td>
                                                            <input type="number" name="2s" id="2s" class="form-control sumatoria_s focusCampo" value="" step="0.01" min="0">
                                                        </td>
                                                        <td>
                                                            <input type="number" name="3s" id="3s" class="form-control sumatoria_s focusCampo" value="" step="0.01" min="0">
                                                        </td>
                                                        <td>
                                                            <input type="number" name="4s" id="4s" class="form-control sumatoria_s focusCampo" value="" step="0.01" min="0">
                                                        </td>
                                                        <td>
                                                            <input type="number" name="total_s" readonly id="Total" class="form-control" value="" step="0.01" min="0">
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row" id="btns-init">
                                        <div class="col-md-8"></div>
                                        <div class="col-md-4 text-rigth">
                                            <div id="bloqueo-btn-1" style="display:none">
                                                <button class="btn btn-TWM" type="button" disabled="">
                                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                    Espere...
                                                </button>

                                            </div>
                                            <div id="desbloqueo-btn-1">
                                                <button type="button" onclick="clearForm('formAddRendimiento')" class="button btn btn-danger">Limpiar</button>
                                                <button type="submit" class="button btn btn-success">Guardar</button>
                                            </div>
                                        </div>

                                    </div>



                                </form>

                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-6">
                        <div class="card border">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="div-dataLot">
                                        <div class="" style="height:350px; overflow-y: scroll;">
                                            <table class="table table-sm">

                                                <tbody>
                                                    <tr>
                                                        <td class="bg-TWM text-white">
                                                            <label for="areaWB">Área Wet Blue</label>
                                                        </td>
                                                        <td>
                                                            <input class="form-control PromedioAreaWB PerdidaAreaWBTerminada Validate Positivos" value="" onchange="guardarValor('areawb', this)" type="number" step="0.001" min="0" name="areaWB" id="areaWB"></input>
                                                        </td>

                                                    </tr>
                                                    <!---PROMEDIO DE AREA WB--->
                                                    <tr>
                                                        <td class="bg-TWM text-white">
                                                            <label for="avgAreaWB">Promedio Área (WB)</label>
                                                        </td>
                                                        <td>
                                                            <input class="form-control Validate" value="" disabled type="number" step="0.001" name="promedioAreaWB" id="avgAreaWB"></input>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="bg-TWM text-white">
                                                            <label for="rejectedRawMt">Piezas rechazadas</label>
                                                        </td>
                                                        <td>
                                                            <input class="form-control AreaPzasRechazo Validate Positivos" type="number" step="1" min="0" name="piezasRechazadas" value="" onchange="guardarValor('pzasrechazadas', this)" id="rejectedRawMt"></input>
                                                        </td>

                                                    </tr>
                                                    <tr id="divCausesRejection">
                                                        <td colspan="2">
                                                            <textarea class="form-control Validate" id="commentsRej" onchange="guardarValor('comentariosrechazo', this, true)" id="comentariosrechazo" cols="30" rows="1" placeholder="Captura causa del rechazo en piezas"></textarea>
                                                        </td>
                                                    </tr>
                                                    <!---PROMEDIO DE AREA WB--->
                                                    <tr>
                                                        <td class="bg-TWM text-white">
                                                            <label for="rejectedRawMtArea">Área (pies<sup>2</sup>) de pzas rech. </label>
                                                        </td>
                                                        <td>
                                                            <input class="form-control Validate Positivos" value="" disabled type="number" step="0.001" min="0" name="areaPzasRechazo" id="rejectedRawMtArea"></input>
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td class="bg-TWM text-white">
                                                            <label for="areaFinal">Área Final</label>
                                                        </td>
                                                        <td>
                                                            <input class="form-control PerdidaAreaWBTerminada Validate Positivos" type="number" step="0.001" min="0" name="areaFinal" value="" id="areaFinal" onchange="guardarValor('areafinal', this)"></input>
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td class="bg-TWM text-white">
                                                            <label for="lostWBfinished">Pérdida de Area WB a Terminado</label>
                                                        </td>
                                                        <td>
                                                            <div class="input-group mb-3">
                                                                <input class="form-control Validate" disabled type="number" step="0.001" min="0" value="" name="perdidaWBTerminado" id="lostWBfinished"></input>
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text">%</span>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td class="bg-TWM text-white">
                                                            <label for="costft2">Costo por Ft<sup>2</sup></label>
                                                        </td>
                                                        <td>
                                                            <div class="input-group mb-3">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text">$</span>
                                                                </div>
                                                                <input class="form-control PerdidaCrustTeseo Validate Positivos" min="0" type="number" step="0.001" name="costoft2" value="" id="costft2" onchange="guardarValor('costoft2', this)"></input>

                                                            </div>
                                                        </td>
                                                    </tr>

                                                </tbody>

                                            </table>
                                        </div>
                                        <div class="row" id="div-observaciones">
                                            <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
                                                <label for="observations"> Observaciones</label>
                                                <textarea name="observaciones" id="observations" cols="30" rows="5" onchange="guardarValor('observaciones', this, true)" class="form-control"></textarea>
                                            </div>
                                        </div>
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
                                            <button type="button" onclick="eliminarPreRegistro()" class="button btn btn-danger">Cancelar Pre-Registro</button>
                                            <button type="button" onclick="finishRegister()" class="button btn btn-success">Finalizar</button>
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
<script src="../assets/scripts/clearDataSinSelect.js"></script>
<script src="../assets/scripts/calculaSemanaProduccion.js"></script>
<script src="../assets/scripts/validaLoteEtiq.js"></script>
<script src="../assets/scripts/selectFiltros.js"></script>
<script src="../assets/scripts/functionStorageRendimientoEtiquetas.js"></script>
<script>
    edicion = 0;
    getEditInput()
    /******************* SUMATORIA DE TOTALES 'S'*******************/
    $(".sumatoria_s").change(function() {
        let result = 0;
        $(".sumatoria_s").each(function() {
            val= $(this).val()==''?0:parseFloat($(this).val())
            result = parseFloat(result) + parseFloat(val);
        });
        $("#Total").val(result);
    });

    //Cargar datos de etiquetas
    function getEditInput() {
        $.ajax({
            url: '../Controller/lotesEtiqCalzado.php?op=geteditxuser',
            type: 'GET',
            async: false,
            dataType: "json",
            success: function(result) {
                if (!$.isEmptyObject(result)) {
                    edicion = result.id;
                    $("#lot").val(result.loteTemola)
                    $("#finalDate").val(result.fechaFinal)
                    $("#productionWeek").val(result.yearWeek + "-W" + result.semanaProduccion)
                    // Carga Programa 
                    var option = new Option(result.nPrograma, result.idCatPrograma, true, true);
                    $("#program").append(option).trigger('change');
                    $("#program").trigger({
                        type: 'select2:select',
                        params: {
                            data: result.idCatPrograma
                        }
                    });
                    // Carga Materia Prima
                    var option = new Option(result.nMateriaPrima, result.idCatMateriaPrima, true, true);
                    $("#rawMaterial").append(option).trigger('change');
                    $("#rawMaterial").trigger({
                        type: 'select2:select',
                        params: {
                            data: result.idCatMateriaPrima
                        }
                    });
                    // Carga Proveedor
                    var option = new Option(result.nProveedor, result.idProveedor, true, true);
                    $("#provider").append(option).trigger('change');
                    $("#provider").trigger({
                        type: 'select2:select',
                        params: {
                            data: result.idProveedor
                        }
                    });
                    //Distribucion Materia Prima
                    $("#1s").val(result["1s"] == '' ? 0 : Number(result["1s"]).toFixed(2));
                    $("#2s").val(result["2s"] == '' ? 0 : Number(result["2s"]).toFixed(2));
                    $("#3s").val(result["3s"] == '' ? 0 : Number(result["3s"]).toFixed(2));
                    $("#4s").val(result["4s"] == '' ? 0 : Number(result["4s"]).toFixed(2));
                    $("#Total").val(result["total_s"] == '' ? 0 : Number(result["total_s"]).toFixed(2));
                    $("#btns-init").attr('hidden', true);
                    $("#btns-finalizar").attr('hidden', false);
                    $("#div-observaciones").attr('hidden', false);
                    $('#formAddRendimiento').find('input, textarea, button, select').attr('disabled', 'disabled');
                    //Atributos de Lote
                    $("#areaWB").val(result["areaWB"] == '' ? 0 : Number(result["areaWB"]).toFixed(2));
                    $("#avgAreaWB").val(result["promedioAreaWB"] == '' ? 0 : Number(result["promedioAreaWB"]).toFixed(2));
                    $("#rejectedRawMt").val(result["piezasRechazadas"] == '' ? 0 : Number(result["piezasRechazadas"]).toFixed(2));
                    $("#rejectedRawMtArea").val(result["areaPzasRechazo"] == '' ? 0 : Number(result["areaPzasRechazo"]).toFixed(2));
                    $("#areaFinal").val(result["areaFinal"] == '' ? 0 : Number(result["areaFinal"]).toFixed(2));
                    $("#lostWBfinished").val(result["perdidaAreaWBTerm"] == '' ? 0 : Number(result["perdidaAreaWBTerm"]).toFixed(2));
                    $("#costft2").val(result["costoXft2"] == '' ? 0 : Number(result["costoXft2"]).toFixed(2));
                    $("#commentsRej").text(result["comentariosRechazo"]);
                    $("#observations").text(result["observaciones"]);
                    $("#div-dataLot").prop("hidden", false)
                    //Comentarios por Rechazo
                    if (result["piezasRechazadas"] > 0) {
                        $("#divCausesRejection").prop("hidden", false)
                    } else {
                        $("#divCausesRejection").prop("hidden", true)

                    }
                } else {
                    $("#div-dataLot").prop("hidden", true)
                }

            },


        });
    }



    /********** INICIO DE RENDIMIENTO ***********/
    $("#formAddRendimiento").submit(function(e) {
        e.preventDefault();
        formData = $(this).serialize();
        $.ajax({
            url: '../Controller/lotesEtiqCalzado.php?op=initrendimiento',
            data: formData,
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                    setTimeout(() => {
                        bloqueoBtn("bloqueo-btn-1", 2)
                        getEditInput()

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
    function eliminarPreRegistro() {
        $.ajax({
            url: '../Controller/rendimientoEtiquetas.php?op=eliminarrendimiento',
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
    function finishRegister() {
      
        log_result = validaCamposLlenos()
        if (log_result) {
            $.ajax({
                url: '../Controller/lotesEtiqCalzado.php?op=finishregister',
                data: {
                    id: edicion
                },
                type: 'POST',
                success: function(json) {
                    resp = json.split('|')
                    if (resp[0] == 1) {
                        notificaSuc(resp[1])
                        bloqueoBtn("bloqueo-btn-2", 2)


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
        /***************************GUARDAR OBSERVACIONES *******************************/
        function guardarObservaciones(input) {
            v_observaciones = $(input).val()
            $.ajax({
                url: '../Controller/rendimiento.php?op=guardarobservaciones',
                type: 'POST',
                data: {

                },
                success: function(json) {
                    resp = json.split('|')
                    if (resp[0] == 1) {
                        notificaSuc(resp[1])
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