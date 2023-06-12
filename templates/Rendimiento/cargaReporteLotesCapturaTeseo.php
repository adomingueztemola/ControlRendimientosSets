<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once('../../include/connect_mvc.php');
include('../../assets/scripts/cadenas.php');

$idUser = $_SESSION['CREident'];
$nameUser = $_SESSION['CREnombreUser'];
setlocale(LC_TIME, 'es_ES.UTF-8');
$debug = 0;
$space = 1;
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}
/************************** VARIABLES DE FILTRADO *******************************/
$id = !empty($_POST['id']) ? $_POST['id'] : '';
$row = !empty($_POST['row']) ? $_POST['row'] : '';

$proceso = !empty($_POST['proceso']) ? $_POST['proceso'] : '';
$programa = !empty($_POST['programa']) ? $_POST['programa'] : '';
$materiaPrima = !empty($_POST['materia']) ? $_POST['materia'] : '';
$date_start = !empty($_POST['date-start']) ? $_POST['date-start'] : "";
$date_end = !empty($_POST['date-end']) ? $_POST['date-end'] :  "";
$idUser = $_SESSION['CREident'];
$nameUser = $_SESSION['CREnombreUser'];
$filtradoEstatus = $id == '1' ? '(r.tipoProceso="1" AND r.regEmpaque="1")' : '1=1';
$filtradoEstatus = $id == '2' ? '(r.tipoProceso="1" AND (r.regEmpaque!="1" OR r.regEmpaque IS NULL))' : $filtradoEstatus;
$filtradoEstatus = $id == '3' ? '(r.tipoProceso="2" AND  (r.regEmpaque!="1" OR r.regEmpaque IS NULL))' : $filtradoEstatus;

$filtradoEstatus = $id == '-1' ? '1=1' : $filtradoEstatus;
/***************** CASTEO DE FECHAS ****************** */
if ($date_start != '' and $date_end != '') {
    $date_start = date("Y-m-d", strtotime(str_replace("/", "-", $date_start)));
    $date_end = date("Y-m-d", strtotime(str_replace("/", "-", $date_end)));
} else {
    $date_start = date("Y-01-01");
    $date_end = date("Y-12-t");
}

$filtradoFecha = ($date_start != '' and $date_end != '') ?
    "r.fechaEngrase BETWEEN '$date_start' AND '$date_end'" : "1=1";
$filtradoProceso = $proceso != '' ? "r.idCatProceso='$proceso'" : "1=1";
$filtradoPrograma = $programa != '' ? "r.idCatPrograma='$programa'" : "1=1";
$filtradoMateria = $materiaPrima != '' ? "r.idCatMateriaPrima='$materiaPrima'" : "1=1";

$obj_rendimiento = new Rendimiento($debug, $idUser);
$Data = $obj_rendimiento->getLotesXCapturarTeseo($filtradoFecha, $filtradoProceso, $filtradoMateria, $filtradoPrograma, $filtradoEstatus);
$Data = Excepciones::validaConsulta($Data);
?>
<div class="table-responsive">
    <table class="table table-sm" id="table-reportelote">
        <thead>
            <tr>
                <th>#</th>
                <th>R.P.</th>
                <th>Fecha de Engrase</th>
                <th>Lote</th>
                <th class="table-info">Total de Hides</th>
                <th>Programa</th>
                <th>Área de Teseo®</th>
                <th>Yield</th>
                <th>12:00</th>
                <th>03:00</th>
                <th>06:00</th>
                <th>09:00</th>
                <th>Piezas de Teseo®</th>
                <th>Hides Rechazados</th>
                <th>Liberación</th>


            </tr>
        </thead>
        <tbody>
            <?php
            $count = 1;
            $dataedit = "";
            $rowData = "";
            foreach ($Data as $key => $value) {
                $remarkTable = ($row != '' and $value['id'] == $row) ? 'table-secondary' : '';
                $input = '<div class="input-group mb-3">
                <input type="number" class="form-control" step="0.01" id="inptDm' . $value['id'] . '" min="0" placeholder="" aria-label="" aria-describedby="basic-addon1">
                <div class="input-group-append">
                    <button class="btn btn-info" onclick="conversionArea(' . $value['id'] . ')" type="button"><i class="ti-control-shuffle "></i></button>
                </div>
            </div>';
                $popover = "data-container='body' data-toggle='popover' data-html='true' data-title='CONVERSIÓN DE DM<sup>2</sup> A FT<sup>2</sup>' data-placement='top' 
                data-content='$input'";
                $btnEdicion = $value["regTeseo"] == '1' ?
                    "<button class='btn btn-xs btn-outline-dark' data-toggle='modal' data-target='#edicionModal' onclick='getEnvioSolicitud({$value['id']})'><i class='fas fa-pencil-alt'></i></button>" :
                    "";
                $lblLiberacion = $value["regOkNok"] == '1' ?
                    "{$value["fFechaRegTeseo"]}" :
                    "<div id='bloqueo-btn-{$value['id']}' style='display: none;'>
                        <button class='btn btn-xs btn-success' type='button' disabled=''>
                            <span class='spinner-border spinner-border-sm' role='status' aria-hidden='true'></span>
                        </button>
                    </div>
                    <div id='desbloqueo-btn-{$value['id']}'>
                        <button onclick='guardarDataTeseo({$value['id']})' 
                        class='btn btn-xs btn-success'><i class='fas fa-box'></i></button>
                    </div>";
                //INPUT DE PIEZAS
                $inptPzasCortadasTeseo = ($value["regOkNok"] != '1' and $value['tipoProceso'] == '1') ?
                    "<input class='form-control form-control-sm' readonly value='{$value['pzasCortadasTeseo']}' id='totalSumatoria{$value['id']}' min='0' step='1' onchange='' id='teseo{$value['id']}' type='number'>"
                    : "<b>" . formatoMil($value['pzasCortadasTeseo'], 0) . "</b>";
                //INPUT DE AREA
                $inptAreaTeseo = ($value["regOkNok"] != '1' and $value['tipoProceso'] == '1') ?
                    "<div class='input-group mb-3'>
                        <input class='form-control form-control-sm' id='areaFt{$value['id']}' value='{$value['areaFinal']}' min='0' step='0.01' onchange='cambiarTeseo({$value['id']}, this, 2)' id='teseo{$value['id']}' type='number'>
                        <div class='input-group-append'>
                            <button type='button' $popover  class='btn btn-small btn-xs btn-dark'>ft<sup>2</sup></button>
                        </div>
                    </div>"
                    : "<b>" . formatoMil($value['areaFinal'], 2) . "</b>";
                //INPUT DE YIELD 
                $inptYieldTeseo = ($value["regOkNok"] != '1' and $value['tipoProceso'] == '1') ?
                    "<input class='form-control form-control-sm' value='{$value['yieldInicialTeseo']}' min='0' step='0.01' onchange='cambiarTeseo({$value['id']}, this, 3)' id='teseo{$value['id']}' type='number'>"
                    : "<b>" . formatoMil($value['yieldInicialTeseo'], 2) . "</b>";
                //INPUT DE 12:00 
                $inpt12Teseo = ($value["regOkNok"] != '1' and $value['tipoProceso'] == '1') ?
                    "<input class='form-control form-control-sm sumatoria{$value['id']}' value='{$value['_12Teseo']}' min='0' step='1' onchange='cambiarTeseo({$value['id']}, this, 4)' id='teseo{$value['id']}' type='number'>"
                    : "<b>" . formatoMil($value['_12Teseo'], 2) . "</b>";
                //INPUT DE 3:00 
                $inpt3Teseo = ($value["regOkNok"] != '1' and $value['tipoProceso'] == '1') ?
                    "<input class='form-control form-control-sm sumatoria{$value['id']}' value='{$value['_3Teseo']}' min='0' step='1' onchange='cambiarTeseo({$value['id']}, this, 5)' id='teseo{$value['id']}' type='number'>"
                    : "<b>" . formatoMil($value['_3Teseo'], 2) . "</b>";
                //INPUT DE 6:00 
                $inpt6Teseo = ($value["regOkNok"] != '1' and $value['tipoProceso'] == '1') ?
                    "<input class='form-control form-control-sm sumatoria{$value['id']}' value='{$value['_6Teseo']}' min='0' step='1' onchange='cambiarTeseo({$value['id']}, this, 6)' id='teseo{$value['id']}' type='number'>"
                    : "<b>" . formatoMil($value['_6Teseo'], 2) . "</b>";
                //INPUT DE 9:00 
                $inpt9Teseo = ($value["regOkNok"] != '1' and $value['tipoProceso'] == '1') ?
                    "<input class='form-control form-control-sm sumatoria{$value['id']}' value='{$value['_9Teseo']}' min='0' step='1' onchange='cambiarTeseo({$value['id']}, this, 7)' id='teseo{$value['id']}' type='number'>"
                    : "<b>" . formatoMil($value['_9Teseo'], 2) . "</b>";
                //INPUT DE HIDES RECHAZADOS
                $inptHideRechazo = ($value["regOkNok"] != '1' and $value['tipoProceso'] == '1') ?
                    "<input class='form-control form-control-sm' value='{$value['hideRechTeseo']}' min='0' step='1' onchange='cambiarTeseo({$value['id']}, this, 8)' id='teseo{$value['id']}' type='number'>"
                    : "<b>" . formatoMil($value['hideRechTeseo'], 2) . "</b>";

                $iconReprog = $value["reprogramado"] == '1' ? '<i class="fas fa-recycle text-success"></i>' : '';
                $totalHides = formatoMil($value["total_s"] * 2, 0);

                if ($row != $value['id']) {
                    $rowData .= "<tr class='$remarkTable'>
                    <td>{$btnEdicion} </td>
                    <td>{$iconReprog}</td>
                    <td>{$value['fFechaEngrase']}</td>
                    <td>{$value['loteTemola']}</td>
                    <td class='table-info'>{$totalHides}</td>
                    <td>{$value['nPrograma']}</td>
                    <td>$inptAreaTeseo</td>
                    <td>$inptYieldTeseo</td>
                    <td>$inpt12Teseo</td>
                    <td>$inpt3Teseo</td>
                    <td>$inpt6Teseo</td>
                    <td>$inpt9Teseo</td>
                    <td>$inptPzasCortadasTeseo</td>
                    <td>$inptHideRechazo</td>
                    <td>$lblLiberacion</td>

                </tr>";
                } else {
                    $dataedit = "<tr class='$remarkTable'>
                    <td>{$count}</td>
                    <td>{$iconReprog}</td>
                    <td>{$value['fFechaEngrase']}</td>
                    <td>{$value['loteTemola']}</td>
                    <td class='table-info'>{$totalHides}</td>
                    <td>{$value['nPrograma']}</td>
                    <td>$inptAreaTeseo</td>
                    <td>$inptYieldTeseo</td>
                    <td>$inpt12Teseo</td>
                    <td>$inpt3Teseo</td>
                    <td>$inpt6Teseo</td>
                    <td>$inpt9Teseo</td>
                    <td>$inptPzasCortadasTeseo</td>
                    <td>$inptHideRechazo</td>
                    <td>$lblLiberacion</td>

                </tr>";
                }
                $count++;
            }
            echo $dataedit . $rowData;
            ?>
        </tbody>
    </table>
</div>
<!-- INICIAL DE MODAL DE EDICION DE LOTES -->
<div class="modal fade" id="edicionModal" role="dialog" aria-labelledby="reasignarModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content solicModal-block">
            <div class="modal-header bg-TWM text-white">
                <h5 class="modal-title" id="reasignarModalLabel">Solicitud de Edición de Datos Teseo</h5>
                <button type="button" class="close text-white" onclick="limpiarForm()" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formSolicitudEdicion">
                <div class="modal-body">
                    <input type="hidden" name="id" id="id">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-sm table-bordered">
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="row">
                                                <div class="col-12">Área de Teseo®</div>
                                                <div class="col-12"><span class="text-info" id="edit-areaTeseo"></span></div>
                                            </div>
                                        </td>
                                        <td><input type="number" class="form-control focusCampo" step="0.01" min="0" name="areaTeseo" id="areaTeseo"></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="row">
                                                <div class="col-12">Yield</div>
                                                <div class="col-12"><span class="text-info" id="edit-yield"></span></div>
                                            </div>
                                        </td>
                                        <td><input type="number" class="form-control focusCampo" step="0.01" min="0" name="yield" id="yield"></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="row">
                                                <div class="col-12">12:00</div>
                                                <div class="col-12"><span class="text-info" id="edit-12"></span></div>
                                            </div>
                                        </td>
                                        <td><input type="number" class="form-control focusCampo" step="1" min="0" name="_12" id="_12"></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="row">
                                                <div class="col-12">03:00</div>
                                                <div class="col-12"><span class="text-info" id="edit-03"></span></div>
                                            </div>
                                        </td>
                                        <td><input type="number" class="form-control focusCampo" step="1" min="0" name="_3" id="_3"></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="row">
                                                <div class="col-12">06:00</div>
                                                <div class="col-12"><span class="text-info" id="edit-06"></span></div>
                                            </div>
                                        </td>
                                        <td><input type="number" class="form-control focusCampo" step="1" min="0" name="_6" id="_6"></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="row">
                                                <div class="col-12">09:00</div>
                                                <div class="col-12"><span class="text-info" id="edit-09"></span></div>
                                            </div>
                                        </td>
                                        <td><input type="number" class="form-control focusCampo" step="1" min="0" name="_9" id="_9"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <label class="form-label" for="motivo">Motivo (opcional)</label>
                            <textarea name="motivo" class="form-control" id="motivo" rows="5"></textarea>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" onclick="limpiarForm()" class="btn btn-light" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-success">Enviar Solicitud</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- FIN DE MODAL DE EDICION DE LOTES -->
<script src="../assets/scripts/clearDataSinSelect.js"></script>

<script>
    $(function() {
        $('[data-toggle="popover"]').popover({
            trigger: 'focus',
            html: true,
            sanitize: false
        })
    })

    function updateRemark(id) {
        $.post('../templates/Rendimiento/cargaReporteLotesCapturaTeseo.php', {
            row: id
        }, function(respuesta) {
            $("#content-lotes").html(respuesta);
        });

    }
    $("#table-reportelote").DataTable({
        "fnDrawCallback": function(oSettings) {
            $('[data-toggle="popover"]').popover({
                html: true,
                sanitize: false
            });
        },
        dom: 'Bfrltip',
        "columnDefs": [{
                "width": "10px",
                "targets": 0
            },
            {
                "width": "10px",
                "targets": 1
            },
            {
                "width": "130px",
                "targets": 6
            },
            {
                "width": "10px",
                "targets": 7
            }

        ],
        "aaSorting": [],
        buttons: [{
            extend: 'copy',
            text: 'Copiar Formato',
            exportOptions: {

            },
            footer: true
        }, {
            extend: 'excel',
            text: 'Excel',
            exportOptions: {

            },
            footer: true

        }, {
            extend: 'pdf',
            text: 'Archivo PDF',
            exportOptions: {

            },
            orientation: "landscape",
            footer: true

        }, {
            extend: 'print',
            text: 'Imprimir',
            exportOptions: {

            },
            footer: true

        }]
    });
    $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('btn btn-TWM mr-1');

    function cambiarTeseo(id, input, option) {
        $(input).val($(input).val() == '' ? '0' : $(input).val());
        value = parseFloat($(input).val());
        //VALIDAR QUE EL VALOR SEA MAYOR A 0
        if (value < 0) {
            notificaBad("Valor incorrecto, verifica la cantidad.")
            return 0;
        } else {
            //REVISAR SI CONTIENE LA CLASE .SUMATORIA
            if ($(input).hasClass("sumatoria" + id)) {
                //actualizar la suma recorriendo la clase
                let sumatoria = 0;
                $(".sumatoria" + id).each(function() {
                    valueSuma = $(this).val() == '' ? '0' : $(this).val();
                    sumatoria = parseInt(sumatoria) + parseInt(valueSuma);
                });
                $("#totalSumatoria" + id).val(sumatoria)
                almacenaDatos(1, id, sumatoria)

            }

            almacenaDatos(option, id, value)

        }

    }

    function almacenaDatos(option, id, value) {
        //SELECCION DE PETICION POR INPUT CAMBIADO
        switch (option) {
            case 1:
                request = "actualizarteseo"
                break;
            case 2:
                request = "actualizararea"
                break;
            case 3:
                request = "actualizaryield"
                break;
            case 4:
                request = "actualizar_12teseo"
                break;
            case 5:
                request = "actualizar_3teseo"
                break;
            case 6:
                request = "actualizar_6teseo"
                break;
            case 7:
                request = "actualizar_9teseo"
                break;
            case 8:
                request = "actualizar_hiderechazo"
                break;
        }
        $.ajax({
            url: '../Controller/empaque.php?op=' + request,
            data: {
                id: id,
                teseo: value
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
            beforeSend: function() {

            }

        });
    }

    function guardarDataTeseo(id) {
        $.ajax({
            url: '../Controller/empaque.php?op=cierredatateseo',
            data: {
                id: id,
            },
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                    bloqueoBtn("bloqueo-btn-" + id, 2);
                    updateRemark(id)

                } else if (resp[0] == 0) {
                    bloqueoBtn("bloqueo-btn-" + id, 2);

                    notificaBad(resp[1])


                }
            },
            beforeSend: function() {
                bloqueoBtn("bloqueo-btn-" + id, 1);

            }

        });
    }

    function conversionArea(id) {
        dminft = 0.1076391;
        inptDm = $("#inptDm" + id).val() == '' ? '0' : $("#inptDm" + id).val();
        converse = (dminft * inptDm).toFixed(2);
        $("#areaFt" + id).val(converse)
        cambiarTeseo(id, $("#areaFt" + id), 2)
    }

    function getEnvioSolicitud(id) {
        $.ajax({
            url: '../Controller/rendimiento.php?op=getdetallelote',
            data: {
                id: id
            },
            type: 'POST',
            async: false,
            dataType: "json",
            success: function(respuesta) {
                if (!respuesta.length) {
                    areaFinal = (respuesta.areaFinal == null ? '0.0' : Number(respuesta.areaFinal));
                    yieldFinal = (respuesta.yieldInicialTeseo == null ? '0.0' : Number(respuesta.yieldInicialTeseo));
                    _12Teseo = (respuesta._12Teseo == null ? '0.0' : Number(respuesta._12Teseo));
                    _3Teseo = (respuesta._3Teseo == null ? '0.0' : Number(respuesta._3Teseo));
                    _6Teseo = (respuesta._6Teseo == null ? '0.0' : Number(respuesta._6Teseo));
                    _9Teseo = (respuesta._9Teseo == null ? '0.0' : Number(respuesta._9Teseo));
                    $("#edit-areaTeseo").text(areaFinal.toLocaleString('es-MX'))
                    $("#edit-yield").text(yieldFinal.toLocaleString('es-MX') + "%")
                    $("#edit-12").text(_12Teseo.toLocaleString('es-MX'))
                    $("#edit-03").text(_3Teseo.toLocaleString('es-MX'))
                    $("#edit-06").text(_6Teseo.toLocaleString('es-MX'))
                    $("#edit-09").text(_9Teseo.toLocaleString('es-MX'))
                    $("#motivo").val("")

                    $("#id").val(id)
                    $("#areaTeseo").val(areaFinal)
                    $("#yield").val(yieldFinal)
                    $("#_12").val(_12Teseo)
                    $("#_3").val(_3Teseo)
                    $("#_6").val(_6Teseo)
                    $("#_9").val(_9Teseo)

                } else {}
            },


        });
    }
    $("#formSolicitudEdicion").submit(function(e) {
        e.preventDefault();
        formData = $(this).serialize();
        $.ajax({
            url: '../Controller/rendimiento.php?op=solicitudedicionteseo',
            data: formData,
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                    setTimeout(() => {
                        bloqueoModal(e, "solicModal-block", 2)
                        $('#edicionModal').modal('hide')
                        $('#formSolicitudEdicion').find('input,textarea, button, select').removeAttr('disabled');
                        update()
                    }, 1000);

                } else if (resp[0] == 0) {
                    notificaBad(resp[1])
                    bloqueoModal(e, "solicModal-block", 2)
                    $('#formSolicitudEdicion').find('input,textarea, button, select').removeAttr('disabled');


                }
            },
            beforeSend: function() {
                bloqueoModal(e, "solicModal-block", 1)
                $('#formSolicitudEdicion').find('input,textarea, button, select').attr('disabled', 'disabled');

            }

        });
    });
</script>