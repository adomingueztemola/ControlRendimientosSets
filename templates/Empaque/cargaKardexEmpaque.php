<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../../include/connect_mvc.php";
include('../../assets/scripts/cadenas.php');
$debug = 0;
$idUser = $_SESSION['CREident'];
if ($debug == 1) {
    print_r($_GET);
    //  exit(0);
} else {
    error_reporting(0);
}
$id = isset($_GET['id']) ? $_GET['id'] : '';
if ($id == '') {
    echo '<div class="alert alert-danger" role="alert">
            ¡Atención! No se encontró el lote, intentalo de nuevo, si el problema persiste consulta al departamento de sistemas.
           </div>';
    exit(0);
}
$obj_empaque = new Empaque($debug, $idUser); //Modelo de Empaque
/***************************** Datos del Empaque *******************************/
$DataEmpaque = $obj_empaque->getDetEmpaque($id);
$DataEmpaque = Excepciones::validaConsulta($DataEmpaque);
if (count($DataEmpaque) <= 0) {
    echo "<div class='alert alert-danger' role='alert'>
            <b>No se encontró el registro de empaque solicitado, notifica al departamento de Sistemas.</b>
          </div>";
    exit(0);
}
$idCatPrograma = $DataEmpaque['idCatPrograma'];
/***************** DATOS DE CAJA *************************/
$DataCaja = $obj_empaque->consultaLlenadoCaja($id);
$DataCaja = Excepciones::validaConsulta($DataCaja);
$numCaja = ($DataCaja['completed'] == '0' and $DataCaja['ultCajaLlenada'] > 0) ? $DataCaja['ultCajaLlenada'] : $DataCaja['cajaSiguiente'];
//VALORES DE CAJA EFECTUADOS
$_12cja = ($DataCaja['completed'] == '0' and $DataCaja['ultCajaLlenada'] > 0) ? $DataCaja['_12f'] : 100;
$_3cja = ($DataCaja['completed'] == '0' and $DataCaja['ultCajaLlenada'] > 0) ? $DataCaja['_3f'] : 100;
$_6cja = ($DataCaja['completed'] == '0' and $DataCaja['ultCajaLlenada'] > 0) ? $DataCaja['_6f'] : 100;
$_9cja = ($DataCaja['completed'] == '0' and $DataCaja['ultCajaLlenada'] > 0) ? $DataCaja['_9f'] : 100;
$completedCaja = (($DataCaja['completed'] == '1' or  $DataCaja['ultCajaLlenada'] == 0)) ? '1' : $DataCaja['completed'];
if ($debug == '1') {
    print_r("Caja 12.00: " . $_12cja);
    print_r("<br>");
    print_r("Caja 3.00: " . $_3cja);
    print_r("<br>");
    print_r("Caja 6.00: " . $_6cja);
    print_r("<br>");
    print_r("Caja 9.00: " . $_9cja);
    print_r("<br>");
    print_r("CAJA COMPLTEADA: " . $completedCaja);
}
/*********************************************************/
if ($completedCaja == '0') { ?>
    <div class="row">
        <div class="col-lg-12 col-md-12 col-xs-12 col-sm-12">
            <div class="alert alert-danger" role="alert">
                <b>¡Advertencia!</b> Se tiene actualmente una caja incompleta. Se necesita las siguiente piezas para cerrar.

            </div>
        </div>
    </div>
<?php }
?>

<form id="formAddDetCaja">
    <input type="hidden" id="_12Caja" value='<?= $_12cja ?>'>
    <input type="hidden" id="_3Caja" value='<?= $_3cja ?>'>
    <input type="hidden" id="_6Caja" value='<?= $_6cja ?>'>
    <input type="hidden" id="_9Caja" value='<?= $_9cja ?>'>
    <input type="hidden" name="completedCaja" value="<?= $completedCaja ?>">
    <div class="card-header bg-white">
        <input type="hidden" name="id" value="<?= $id ?>">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-xs-6 col-sm-6">
                <label for="lote" class="form-label required">Piezas Disponibles</label>
                <?php
                $Data = $obj_empaque->getLotesDisponibles($idCatPrograma);
                ?>
                <select name="lote" id="lote" onchange="cambioDeLote(this)" style="width:100%!important;" required class="color-prioridad custom-select form-control">
                    <option value="">Selecciona un Lote</option>
                    <?php
                    $tipoPieza = "";
                    foreach ($Data as  $value) {
                        $colorRemanente = ($value['aplicaRemanente'] == '1' and $value['tipoPieza'] == '1') ? 'text-danger' : '';
                        $colorRemanente = ($value['aplicaRemanente'] == '0' and $value['tipoPieza'] == '1') ? 'text-success' : $colorRemanente;
                        $circleRemanente = ($value['aplicaRemanente'] == '1' and $value['tipoPieza'] == '1') ? 'circle' : '';
                        $circleRemanente = ($value['aplicaRemanente'] == '0' and $value['tipoPieza'] == '1') ? 'circle' : $circleRemanente;
                        if ($tipoPieza != $value['tipoPieza']) {
                            $str_piezas = "";
                            switch ($value['tipoPieza']) {
                                case '1':
                                    $str_piezas = "LOTES REGISTRADOS";
                                    break;
                                case '2':
                                    $str_piezas = "REMANENTES DE LOTES";
                                    break;
                                case '3':
                                    $str_piezas = "PIEZAS DE RECUPERACIÓN";
                                    break;
                            }
                            if ($tipoPieza != '') {
                                echo '</optgroup>';
                            }
                            echo '<optgroup label="' . $str_piezas . '">';
                        }
                        $pza12 = $value['pza_12'] == '' ? 0 : $value['pza_12'];
                        $pza3 = $value['pza_3'] == '' ? 0 : $value['pza_3'];
                        $pza6 = $value['pza_6'] == '' ? 0 : $value['pza_6'];
                        $pza9 = $value['pza_9'] == '' ? 0 : $value['pza_9'];
                        $tipoPieza = $value['tipoPieza'];

                        /************** VALIDA QUE EL REMANENTE SEA CUMPLIDO CON LAS PIEZAS *********************/
                        if ($completedCaja == '0') {
                            $habilitadoParaRellenar = 1;
                            //  if ($tipoPieza > 1) {
                            echo "<br>Pzas 12: " . $pza12;
                            echo "<br>Pzas 3: " . $pza3;
                            echo "<br>Pzas 6: " . $pza6;
                            echo "<br>Pzas 9: " . $pza9;

                            if ($pza12 < $_12cja) {
                                $habilitadoParaRellenar = 0;
                            }
                            if ($pza3 < $_3cja) {
                                $habilitadoParaRellenar = 0;
                            }
                            if ($pza6 < $_6cja) {
                                $habilitadoParaRellenar = 0;
                            }
                            if ($pza9 < $_9cja) {
                                $habilitadoParaRellenar = 0;
                            }
                            /* } else if ($tipoPieza == 1) {
                                $totalCaja = $_12cja + $_9cja + $_3cja + $_6cja;
                                if ($value['remanenteAct'] < $totalCaja) {
                                    $habilitadoParaRellenar = 0;
                                }
                            }*/

                            $disabled = $habilitadoParaRellenar == '1' ? '' : 'disabled';
                            echo "<option  data-teseo='{$value['pzasCortadasTeseo']}' data-aplicaremanente='{$value['aplicaRemanente']}' data-style='{$colorRemanente}' data-flag='$circleRemanente' 
                            data-pza12='{$pza12}' data-pza3='{$pza3}' data-pasescrap='{$value['paseScrap']}' data-pza6='{$pza6}'  data-pza9='{$pza9}' data-remanenteact='{$value['remanenteAct']}'
                            value='{$value['id']}|{$value['tipoPieza']}|{$value['idDetCaja']}'>{$value['loteTemola']}</option>";
                        } else {
                            echo "<option data-pasescrap='{$value['paseScrap']}' data-teseo='{$value['pzasCortadasTeseo']}' data-aplicaremanente='{$value['aplicaRemanente']}' data-style='{$colorRemanente}' data-flag='$circleRemanente' 
                            data-pza12='{$pza12}' data-pza3='{$pza3}' data-pza6='{$pza6}' data-pza9='{$pza9}' data-remanenteact='{$value['remanenteAct']}'
                            value='{$value['id']}|{$value['tipoPieza']}|{$value['idDetCaja']}'>{$value['loteTemola']}</option>";
                        }
                    }

                    /****************************************************************************************/

                    ?>
                    </optgroup>
                </select>
            </div>
            <div class="col-lg-6 col-md-6 col-xs-6 col-sm-6 mt-4 pt-1">
                <input type="hidden" id="completedCaja" value="<?= $completedCaja ?>">
                <div class="row col-lg-12">
                    <div class="col-lg-4 d-flex no-block align-items-center">
                        <div id="div-caja" class="card-body">

                            <div class="d-flex no-block align-items-center">
                                <input type="hidden" name="caja" id="caja" value='<?= $numCaja ?>'>
                                <h4 class="text-danger">Num. Caja: <?= $numCaja ?></h4>
                            </div>
                        </div>
                        <div id="div-remanente" hidden>
                            <h4 class="text-danger">Pzas. Sobrantes: <span id="remanenteAct-txt"></span></h4>
                        </div>
                    </div>

                    <div class="col-lg-6">

                        <div class="card-body">
                            <div class="d-flex no-block align-items-center">
                                <img src="../assets/images/TESEO.jpg" width="50%" alt="" srcset="">
                                <div class="mx-4 text-rigth">
                                    <h2>
                                        <font style="vertical-align: inherit;">
                                            <font style="vertical-align: inherit;" id="teseo">0</font>
                                        </font>
                                    </h2>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="col-lg-2 d-flex no-block align-items-center">
                        <button class="btn-circle btn-md btn-primary" data-toggle="modal" data-target="#busquedaModal" type="button" onclick="desgloseLotesSeleccion()"><i class="fas fa-search"></i></button>
                    </div>

                </div>

                <!-- modal de boton de busqueda -->
                <div class="modal fade" data-keyboard="false" data-backdrop="static" id="busquedaModal" role="dialog" aria-labelledby="busquedaModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-TWM text-white">
                                <h5 class="modal-title" id="busquedaModalLabel">Detallado del Lote</h5>
                                <button type="button" onclick="limpiarGrafica()" class="close text-white" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body" id="div-DesgloseTeseo">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" onclick="limpiarGrafica()" data-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>


            </div>

        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="1" id="lote0" name="lote0">
                    <label class="form-check-label" for="lote0">
                        Caja Perteneciente de Lote 0
                    </label>
                </div>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-lg-12 col-md-12 col-xs-12 col-sm-12 table-responsive">
                <table class="table table-sm">
                    <thead class="bg-TWM text-white">
                        <tr>
                            <th>12:00</th>
                            <th>03:00</th>
                            <th>06:00</th>
                            <th>09:00</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <input id="pzas_12" type="number" readonly step="1" min="0" max="100" value="<?= $_12cja ?>" name="pzas_12" class="pzas form-control">
                            </td>
                            <td>
                                <input id="pzas_03" type="number" readonly step="1" min="0" max="100" value="<?= $_3cja ?>" name="pzas_03" class="pzas form-control">
                            </td>
                            <td>
                                <input id="pzas_06" type="number" readonly step="1" min="0" max="100" value="<?= $_6cja ?>" name="pzas_06" class="pzas form-control">
                            </td>
                            <td>
                                <input id="pzas_09" type="number" readonly step="1" min="0" max="100" value="<?= $_9cja ?>" name="pzas_09" class="pzas form-control">
                            </td>
                        </tr>
                        <tr class="text-danger text-center text-bold">
                            <td>Almacenado 12:00 -><b><span id="total_12">0</span></b></td>
                            <td>Almacenado 03:00 -><b><span id="total_3">0</span></b></td>
                            <td>Almacenado 06:00 -><b><span id="total_6">0</span></b></td>
                            <td>Almacenado 09:00 -><b><span id="total_9">0</span></b></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3 col-md-3 col-xs-3 col-sm-3" id="div-inptremanente">
                <div class="form-check form-check-inline">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" onchange="activarCajas(this)" id="remanente" value="1" name="remanente">
                        <label class="custom-control-label" for="remanente">Remanentes del Lote</label>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-xs-3 col-sm-3 text-left" hidden id="div-btndism">
                <div id="bloqueo-btn-D" style="display:none">
                    <button class="btn btn-TWM" type="button" disabled="">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Espere...
                    </button>

                </div>
                <div id="desbloqueo-btn-D">
                    <button class="btn btn-outline-danger btn-md" onclick="disminuirRecuperacion()" type="button"><i class="ti-reload"></i> Disminuir Recuperación</button>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-xs-3 col-sm-3 text-left">
                <div id="bloqueo-btn-R" style="display:none">
                    <button class="btn btn-TWM" type="button" disabled="">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Espere...
                    </button>

                </div>
                <div id="desbloqueo-btn-R">
                    <button class="btn btn-TWM btn-md" type="submit"><i class="fas fa-save"></i> Guardar</button>
                </div>
            </div>
        </div>
    </div>
</form>
<div class="row">
    <div class="col-lg-12 col-md-12 col-xs-12 col-sm-12 mt-2" id="content-detalle"></div>
</div>
<!--- INICIO MODAL DE INFORMACION DE EMPAQUE --->
<div class="modal" id="infoloteModal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content infoModal-block">
            <div class="modal-header bg-TWM text-white">
                <h5 class="modal-title">Información de Empaque Liberado: <span id="lbl-lote"></span></h5>
                <button type="button" class="close" data-dismiss="modal" onclick="cancelarTotalEmpaque()" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="content-infolote">

                </div>
            </div>
            <div class="modal-footer">
                <div id="bloqueo-btn-I" style="display:none">
                    <button class="btn btn-TWM" type="button" disabled="">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Espere...
                    </button>

                </div>
                <div id="desbloqueo-btn-I">
                    <button type="button" class="btn btn-light" onclick="cancelarTotalEmpaque()" data-dismiss="modal">Cancelar</button>
                    <button type="button" id="btnAceptarDatos" onclick="guardarTotalEmpaque()" data-lote="" data-empaque="" class="btn btn-success">Aceptar Datos</button>
                </div>

            </div>
        </div>
    </div>
</div>
<!--- FIN MODAL DE INFORMACION DE EMPAQUE --->
<script src="../assets/scripts/clearData.js"></script>
<script src="../assets/scripts/Empaque/cambioDeLoteNuevo.js"></script>
<script>
    update(<?= $id ?>);

    function abrirNuevoTab(url) {
        // Abrir nuevo tab
        var win = window.open(url, '_blank');
        // Cambiar el foco al nuevo tab (punto opcional)
        win.focus();
    }
    /********************* Cierre Automatico del Modal de Cierre ****************/
    $("#infoloteModal").on("hidden.bs.modal", function() {

    });
    /********************* Funciones para sacar etiqueta en el select2***********************/
    $(".color-prioridad").select2({
        minimumResultsForSearch: Infinity,
        templateResult: iconFormat,
        templateSelection: iconFormat,
        escapeMarkup: function(es) {
            return es;
        }
    });

    function iconFormat(ficon) {
        var originalOption = ficon.element;
        if (!ficon.id) {
            return ficon.text;
        }
        var $ficon = "<i class='fas fa-" + $(ficon.element).data('flag') + " " + $(ficon.element).data('style') + "'></i> " + ficon.text;
        return $ficon;
    }
    /************ Funciones de carga desglose Teseo ****************/
    function desgloseLotesSeleccion() {
        v_select = $("#lote").val();
        array_select = v_select.split("|");
        id = array_select['0'];
        cargaContenido("div-DesgloseTeseo", "../templates/Empaque/cargaDesgloseTeseo.php?id=" + id, '1')

    }
    /********** AGREGAR DETALLE DE CAJA ***********/
    $("#formAddDetCaja").submit(function(e) {
        e.preventDefault();
        formData = $(this).serialize();
        jsonData = $(this).serializeArray()
        $.ajax({
            url: '../Controller/empaque.php?op=agregardetalle',
            data: formData,
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                /*
                ESTRUCTURA DEL JSON
                0: STATUS
                1: MESSAGE
                2: CIERRE
                3: LOTE
                4: ACTIVACION DE ETIQUETA
                5: CONTENT DE SELECCION DE ETIQ
                */
                if (resp[0] == 1) { //SI EL CIERRE DE LA OPERACION FUE CORRECTO
                    notificaSuc(resp[1])

                    if (resp[2] == 0) { //NO HAY CIERRE DE LOTE
                        bloqueoBtn("bloqueo-btn-R", 2)
                        if (resp[4] == '1') { //HAY ACTIVACION DE LA ETIQUETA
                            seleccionarEtiqueta(resp[5])

                        } else {
                            cargaContent(<?= $id ?>);
                        }
                        //cargaContent(<?= $id ?>);
                    } else if (resp[2] == 1) { //HAY CIERRE DE LOTE
                        bloqueoBtn("bloqueo-btn-R", 2)
                        if (resp[4] == '1') { //HAY ACTIVACION DE LA ETIQUETA
                            seleccionarEtiqueta(resp[5], 1)
                            //cargaContent(<?= $id ?>);
                        } else {
                            cierreLote()
                        }
                        //  cargaContent(<?= $id ?>);
                    }
                } else {
                    bloqueoBtn("bloqueo-btn-R", 2)

                    notificaBad(resp[1])


                }
            },
            beforeSend: function() {
                bloqueoBtn("bloqueo-btn-R", 1)

            }

        });
    });
    /********** CARGA DETALLADO DE CAJAS ***********/
    function update(idEmpaque) {
        $('[data-toggle="popover"]').popover('hide');
        cargaContenido("content-detalle", "../templates/Empaque/cargaDetalleEmpaque.php?id=" + idEmpaque, '1')

    }
    /********** CARGA DETALLADO DE EMPAQUE DE LOTE ***********/
    function updateInfoLote(idEmpaque, idLote) {
        cargaContenido("content-infolote", "../templates/Empaque/cargaInfoCierreLote.php?id=" + idEmpaque + "&lote=" + idLote, '1')

    }

    /********** GUARDAR TOTAL EMPAQUE ***********/
    function guardarTotalEmpaque() {
        lote = $("#btnAceptarDatos").data('lote');
        empaque = $("#btnAceptarDatos").data('empaque');
        $.ajax({
            url: '../Controller/empaque.php?op=guardartotal',
            data: {
                idLote: lote,
                idEmpaque: empaque
            },
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                    $("#infoloteModal").modal('hide')
                    setTimeout(() => {
                        bloqueoBtn("bloqueo-btn-I", 2)
                        cargaContent(<?= $id ?>);
                    }, 1000);

                } else if (resp[0] == 0) {
                    bloqueoBtn("bloqueo-btn-I", 2)

                    notificaBad(resp[1])


                }
            },
            beforeSend: function() {
                bloqueoBtn("bloqueo-btn-I", 1)

            }

        });
    }


    function cancelarTotalEmpaque(idLote) {
        lote = $("#btnAceptarDatos").data('lote');
        empaque = $("#btnAceptarDatos").data('empaque');
        $.ajax({
            url: '../Controller/empaque.php?op=cancelartotal',
            data: {
                idLote: lote,
                idEmpaque: empaque
            },
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                    $("#infoloteModal").modal('hide')
                    setTimeout(() => {
                        bloqueoBtn("bloqueo-btn-I", 2)
                        cargaContent(<?= $id ?>);
                    }, 1000);

                } else if (resp[0] == 0) {
                    bloqueoBtn("bloqueo-btn-I", 2)

                    notificaBad(resp[1])


                }
            },
            beforeSend: function() {
                bloqueoBtn("bloqueo-btn-I", 1)

            }

        });
    }

    function seleccionarEtiqueta(str_message, cierre = 0) {
        cadena = str_message.replace(/%/g, ',')
        const myArr = JSON.parse("[" + cadena + "]");
        stringJson = "{";
        myArr.forEach(function(lotes, index) {
            stringJson += '"' + lotes[1] + '":' + lotes[0] + ",";
        });
        stringJson = stringJson.substring(0, stringJson.length - 1);
        stringJson += "}";
        jsonOptions = JSON.parse(stringJson);
        Swal.fire({
            title: 'INGRESA LOTE DE ETIQUETA DE CAJA DEL MIX',
            input: 'select',
            inputOptions: jsonOptions,
            showCancelButton: false,
            confirmButtonText: 'Ingresar',
            showLoaderOnConfirm: true,
            allowOutsideClick: false,
            preConfirm: (login) => {
                jsonData.label = login;
                $.ajax({
                    url: '../Controller/empaque.php?op=agregarlabel&label=' + login,
                    data: formData,
                    type: 'POST',
                    success: function(lbljson) {
                        resplbl = lbljson.split('|')
                        if (resplbl[0] == 1) {
                            notificaSuc(resplbl[1])
                            if (cierre == 1) {
                                cierreLote()
                            } else {
                                cargaContent(<?= $id ?>);
                            }
                            //   cargaContent(<?= $id ?>);

                        } else {
                            notificaBad(resplbl[1])

                        }
                    }
                })

            }
        })
    }

    function cierreLote() {
        let arr = $("#lote").val().split('|');
        updateInfoLote('<?= $id ?>', arr[0]);
        $("#btnAceptarDatos").data('lote', arr[0]);
        $("#btnAceptarDatos").data('empaque', '<?= $id ?>');

        $("#infoloteModal").modal("show");
    }

    function disminuirRecuperacion() {
        let arr = $("#lote").val().split('|');
        let idLote = arr[0];
        let paseScrap = $("#lote option:selected").data("pasescrap");
        let _12 = $("#pzas_12").val() == '' ? 0 : $("#pzas_12").val();
        let _6 = $("#pzas_06").val() == '' ? 0 : $("#pzas_06").val();
        let _3 = $("#pzas_03").val() == '' ? 0 : $("#pzas_03").val();
        let _9 = $("#pzas_09").val() == '' ? 0 : $("#pzas_09").val();
        $.ajax({
            url: '../Controller/empaque.php?op=disminuirrecuperacion',
            data: {
                paseScrap: paseScrap,
                idLote: idLote,
                _12: _12,
                _6: _6,
                _3: _3,
                _9: _9,
            },
            type: 'POST',
            success: function(resp) {
                resp = resp.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                    bloqueoBtn("bloqueo-btn-D", 2)

                    cargaContent(<?= $id ?>);


                } else {
                    notificaBad(resp[1])
                    bloqueoBtn("bloqueo-btn-D", 2)

                }
            },
            beforeSend: function() {
                bloqueoBtn("bloqueo-btn-D", 1)
            }
        })


    }
</script>