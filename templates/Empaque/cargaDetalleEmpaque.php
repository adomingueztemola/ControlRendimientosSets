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
            ¡Atención! No se encontró el Empaque, intentalo de nuevo, si el problema persiste consulta al departamento de sistemas.
           </div>';
    exit(0);
}
$obj_empaque = new Empaque($debug, $idUser); //Modelo de Empaque
/***************************** Datos del Detalle de Empaque *******************************/
$DataDetallado = $obj_empaque->getDetalladoCajas($id);
$DataDetallado = Excepciones::validaConsulta($DataDetallado);
if (count($DataDetallado) <= 0) {
    echo "<div class='alert alert-danger' role='alert'>
            <b>No se encontró el registro de empaque solicitado, notifica al departamento de Sistemas.</b>
          </div>";
    exit(0);
}
?>
<div class="row">
    <div class="col-lg-6 col-md-6 col-xs-6 col-sm-6">

    </div>
    <div class="col-lg-4 col-md-4 col-xs-4 col-sm-4">
        <p id="lblCoincidencias" class="p-2"></p>

    </div>
    <div class="col-lg-2 col-md-2 col-xs-2 col-sm-2  mb-1">
        <form>
            Búsqueda Lote: <input id="searchTerm" autocomplete="off" class="form-control" type="text" onkeyup="doSearch(<?= $id ?>)" />
        </form>

    </div>
</div>
<div class="row" >
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="overflow-y: scroll;  height:500px;">
        <div class="table-responsive-sm">
            <table class="table table-sm table-bordered table-striped" id="datos<?= $id ?>">
                <thead>
                    <tr>
                        <th># Caja</th>
                        <th>Lote</th>
                        <th>12:00</th>
                        <th>03:00</th>
                        <th>06:00</th>
                        <th>09:00</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $cajaAnt = "";
                    $pzasXCaja = 0;
                    $contadorLote = 0;
                    foreach ($DataDetallado as $value) {
                        $colorTable = ($value['tipo'] == '3' AND $value['lote0']!='1')? 'table-warning' : '';
                        $colorTable = ($value['tipo'] == '3' AND $value['lote0']=='1') ? 'table-danger' : $colorTable;

                        $checkedLote0 = $value['lote0'] == '1' ? 'checked' : '';
                        $checkedInterno = $value['interna'] == '1' ? 'checked' : '';
                        $lblInterno = $value['interna'] == '1' ? '(Interna)' : '';
                        $lblLabel = $value["lblLote"] != '' ? "<i class='fas fa-ticket-alt'></i> {$value["lblLote"]}" : '';
                        $iconSales = $value['vendida'] == '1' ? '<i class="fas fa-shopping-cart"></i> Vendida ' . $lblInterno : "<input type='checkbox' $checkedInterno class='' onclick='cambiaCajaInterna(this,{$value['numCaja']}, {$value['idEmpaque']})' id='interno{$value['id']}' value='1' name='interno{$value['id']}'>
                        <label class='' for='interno'>Interna</label>";
                        $inputLote0= $value['tipo']=='3'?"<input type='checkbox' $checkedLote0 class='' onclick='cambiaLote0(this,{$value['numCaja']}, {$value['idEmpaque']})' id='lote0{$value['id']}' value='1' name='lote0{$value['id']}'><label class='text-danger' for='lote0{$value['id']}'>Lote 0</label>":"";
                        if ($cajaAnt != $value['numCaja']) {
                            if ($contadorLote > 0 and $contadorLote < 3) {
                                echo "<tr><td colspan='5' class='text-center noSearch'></td></tr>";
                            }
                            $contadorLote = 0;

                            if ($cajaAnt != '' and $pzasXCaja < 400) {

                                echo "<tr class='table-danger'>
                        <td colspan='6' class='text-center'>Faltan Piezas por Terminar</td>
   
                        </tr>";
                            }
                            // $pzasXCaja=0;
                            $pzasXCaja = $value['total'];
                            if ($pzasXCaja < 400) {
                                $rowspan = "rowspan='3'";
                                $classIdentRow = "haveRowSpan";
                            } else {
                                $rowspan = "";
                                $classIdentRow = "";
                            }
                            echo "<tr >
                    <td  class='$classIdentRow' $rowspan>
                        <div class='row'>
                            <div class='col-lg-2 col-md-2 col-sm-2 col-xs-2'>
                                {$value['numCaja']}
                            </div>
                            <div class='col-lg-3 col-md-3 col-sm-3 col-xs-3'>
                            $inputLote0
                            </div>
                            <div class='col-lg-2 col-md-2 col-sm-2 col-xs-2'>
                            $lblLabel
                            </div>
                            <div class='col-lg-5 col-md-5 col-sm-5 col-xs-5'>
                            $iconSales
                            </div>
                        </div>       
                    </td>
                    <td  class='{$colorTable} tdpzas{$value['numCaja']}{$value['idEmpaque']}'>{$value['loteTemola']}</td>
                    <td  class='{$colorTable} tdpzas{$value['numCaja']}{$value['idEmpaque']}'>{$value['_12']}</td>
                    <td  class='{$colorTable} tdpzas{$value['numCaja']}{$value['idEmpaque']}'>{$value['_3']}</td>
                    <td  class='{$colorTable} tdpzas{$value['numCaja']}{$value['idEmpaque']}'>{$value['_6']}</td>
                    <td  class='{$colorTable} tdpzas{$value['numCaja']}{$value['idEmpaque']}'>{$value['_9']}</td>

                    </tr>";
                        } else {

                            echo "<tr>
                    <td  class='{$colorTable}'>{$value['loteTemola']}</td>
                    <td  class='{$colorTable}'>{$value['_12']}</td>
                    <td  class='{$colorTable}'>{$value['_3']}</td>
                    <td  class='{$colorTable}'>{$value['_6']}</td>
                    <td  class='{$colorTable}'>{$value['_9']}</td>

                    </tr>";
                            $contadorLote++;
                        }
                        $pzasXCaja = $pzasXCaja + $value['total'];

                        $cajaAnt = $value['numCaja'];
                    }
                    if ($contadorLote > 0 and $contadorLote < 3) {
                        echo "<tr><td colspan='5' class='text-center noSearch'></td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header bg-TWM text-white">
                <h5>Detallado de Remanentes</h5>
            </div>
            <div class="card-body border">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Lote</th>
                                <th>12:00</th>
                                <th>03:00</th>
                                <th>06:00</th>
                                <th>09:00</th>
                                <th class="text-center">Detallado de Uso</th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $Data = $obj_empaque->consultaRemanentexEmpaque($id);
                            $Data = Excepciones::validaConsulta($Data);
                            if (count($Data) <= 0) {
                                echo "<tr class=''>
                            <td colspan='6' class='text-center'>Sin Registro de Remanentes</td>
                            </tr>";
                            }
                            foreach ($Data as $value) {
                                $iconRemanente = ($value['usoRemanente'] == '' or $value['usoRemanente'] == '0') ? "fas fa-search text-danger" : 'fas fa-search text-success';
                                $iconColor = ($value['usoRemanente'] == '' or $value['usoRemanente'] == '0') ? "btn-outline-danger" : 'btn-outline-success';

                                $btnReverse = ($value['usoRemanente'] == '' or $value['usoRemanente'] == '0')
                                    ? "<button data-toggle='modal' data-target='#remanenteModal' data-doce='{$value['_12Rem']}' 
                                data-tres='{$value['_3Rem']}' data-seis='{$value['_6Rem']}' data-nueve='{$value['_9Rem']}'
                                data-nlote='{$value['loteTemola']}' data-pasescrap='{$value['paseScrap']}' data-id='{$value['idLote']}'
                                onclick='cargaDatosRemanente(this)' class='btn btn-danger btn-xs'><i class='ti-reload'></i></button>" : '';

                                $lblUsoDelRem = "<b>Piezas de 12.00:</b> {$value['_12Rem']} <br>
                            <b>Piezas de 03.00:</b> {$value['_3Rem']} <br>
                            <b>Piezas de 06.00:</b> {$value['_6Rem']} <br>
                            <b>Piezas de 09.00:</b> {$value['_9Rem']} <br>";
                                $usoRemanente = <<<EOD
                                <button class='btn {$iconColor} btn-xs btn-light' data-container="body" data-toggle="popover" title="PIEZAS DISPONIBLES" animation:true, delay: {hide: 100}
                                data-placement="top" data-content="{$lblUsoDelRem}" data-html="true">
                                <i class="$iconRemanente"></i></button>
                                EOD;
                                echo "<tr>
                                <td>{$value['loteTemola']}</td>
                                <td>{$value['_12']}</td>
                                <td>{$value['_3']}</td>
                                <td>{$value['_6']}</td>
                                <td>{$value['_9']}</td>
                                <td  class=''>$usoRemanente $btnReverse</td>
                            </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="remanenteModal" role="dialog" aria-labelledby="remanenteModal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content BlockModalRever">
            <div class="modal-header bg-TWM text-white">
                <h5 class="modal-title" id="remanenteModalLabel">Eliminación de Remanente Registrado<span id="nLote"></span></h5>
                <button type="button" onclick="limpiar()" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formReverseRemanente">
                <input type="hidden" name="idLote" id="rvr-idLote">
                <input type="hidden" name="paseScrap" id="rvr-paseScrap">

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-danger" hidden id="alert-trasp" role="alert">
                                <i class="fas fa-dolly-flatbed"></i> El Scrap del Lote fue traspasado a Almacén
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Piezas</th>
                                    <th>Actual</th>
                                    <th>Disminución</th>
                                    <th>Total</th>

                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>12:00</td>
                                    <td>
                                        <label id="_12Act"></label>
                                        <input type="hidden" id="inpt_12Act">

                                    </td>
                                    <td class="table-danger"><input step='1' min='0' value="0" type="number" class="form-control focusCampo disminuir" data-pzas="12" name="_12" id="_12Dim"></td>
                                    <td class="table-success"><input disabled type="number" class="form-control" name="" id="_12Total"></td>
                                </tr>
                                <tr>
                                    <td>03:00</td>
                                    <td>
                                        <label id="_3Act"></label>
                                        <input type="hidden" id="inpt_3Act">
                                    </td>
                                    <td class="table-danger"><input step='1' min='0' value="0" type="number" class="form-control focusCampo disminuir" data-pzas="3" name="_3" id="_3Dim"></td>
                                    <td class="table-success"><input disabled type="number" class="form-control" name="" id="_3Total"></td>
                                </tr>
                                <tr>
                                    <td>06:00</td>
                                    <td>
                                        <label id="_6Act"></label>
                                        <input type="hidden" id="inpt_6Act">
                                    </td>
                                    <td class="table-danger"><input step='1' min='0' value="0" type="number" class="form-control focusCampo disminuir" data-pzas="6" name="_6" id="_6Dim"></td>
                                    <td class="table-success"><input disabled type="number" class="form-control" name="" id="_6Total"></td>
                                </tr>
                                <tr>
                                    <td>09:00</td>
                                    <td>
                                        <label id="_9Act"></label>
                                        <input type="hidden" id="inpt_9Act">
                                    </td>
                                    <td class="table-danger"><input step='1' min='0' value="0" type="number" class="form-control focusCampo disminuir" data-pzas="9" name="_9" id="_9Dim"></td>
                                    <td class="table-success"><input disabled type="number" class="form-control" name="" id="_9Total"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="limpiar()" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Disminuir Remanente</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="../assets/scripts/Empaque/buscarLotes.js"></script>
<script src="../assets/scripts/clearDataSinSelect.js"></script>

<script>
    $(function() {
        $('[data-toggle="popover"]').popover()

    })

    function limpiar() {
        clearForm("formReverseRemanente")
    }
    //Datos de Remanente para empezar a disminuir
    function cargaDatosRemanente(inp) {

        let p_12 = $(inp).data('doce')
        let p_3 = $(inp).data('tres')
        let p_9 = $(inp).data('nueve')
        let p_6 = $(inp).data('seis')
        $("#nLote").text(" #" + $(inp).data('nlote'));
        $("#rvr-idLote").val($(inp).data('id'));
        $("#rvr-paseScrap").val($(inp).data('pasescrap'));

        //PIEZAS ACTUALES
        $("#_12Act").text(p_12)
        $("#_3Act").text(p_3)
        $("#_9Act").text(p_9)
        $("#_6Act").text(p_6)
        $("#inpt_12Act").val(p_12)
        $("#inpt_3Act").val(p_3)
        $("#inpt_9Act").val(p_9)
        $("#inpt_6Act").val(p_6)
        //PIEZAS tOTAL
        $("#_12Total").val(p_12)
        $("#_3Total").val(p_3)
        $("#_9Total").val(p_9)
        $("#_6Total").val(p_6)

        if ($(inp).data('pasescrap') == '1') {
            $("#alert-trasp").prop("hidden", false)
        } else {
            $("#alert-trasp").prop("hidden", true)
        }
    }
    //Operacion en movimiento de piezas
    $('.disminuir').on('change', function() {
        valueDism = $(this).val();
        tipoPzas = $(this).data("pzas");
        totalAct = $("#inpt_" + tipoPzas + "Act").val()
        resta = totalAct - valueDism
        $("#_" + tipoPzas + "Total").val(resta)
    })
    //Ejecucion de movimiento de piezas
    $("#formReverseRemanente").submit(function(e) {
        e.preventDefault();
        formData = $(this).serialize();
        $.ajax({
            url: '../Controller/empaque.php?op=reverseremanente',
            data: formData,
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                    bloqueoModal(e, "BlockModalRever", 2)
                    clearForm("formReverseRemanente")
                    cerrarModal("remanenteModal")
                    setTimeout(() => {

                        cargaContent('<?= $id ?>')
                    }, 1000);


                } else if (resp[0] == 0) {
                    notificaBad(resp[1])
                    bloqueoModal(e, "BlockModalRever", 2)


                }
            },
            beforeSend: function() {
                bloqueoModal(e, "BlockModalRever", 1)
            }

        });
    });





    /******* CAMBIA CAJA PARA USO EXTERNO*******/
    function cambiaCajaInterna(checkbox, numCaja, idEmpaque) {
        interno = $(checkbox).prop('checked') ? '1' : '0';
        $.ajax({
            url: '../Controller/empaque.php?op=cambiarcajainterna',
            data: {
                numCaja: numCaja,
                interno: interno,
                idEmpaque: idEmpaque
            },
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                    setTimeout(() => {
                        bloqueoBtn("bloqueo-btn-2", 2)
                        clearForm("formEditPrograma")
                        setTimeout(() => {
                            $("#ModalEditPrograma").modal('hide');
                            update()

                        }, 1000);
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
    
    /******* CAMBIA CAJA DE MATERIAL RECUPERADO*******/
    function cambiaLote0(checkbox, numCaja, idEmpaque) {
        lote0 = $(checkbox).prop('checked') ? '1' : '0';
        $.ajax({
            url: '../Controller/empaque.php?op=cambiarlote0',
            data: {
                numCaja: numCaja,
                lote0: lote0,
                idEmpaque: idEmpaque
            },
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                  if(lote0=='1'){
                    $(".tdpzas"+numCaja+idEmpaque).addClass("table-danger")

                  }else{
                    $(".tdpzas"+numCaja+idEmpaque).removeClass("table-danger")

                  }


                } else if (resp[0] == 0) {
                    notificaBad(resp[1])


                }
            },
            beforeSend: function() {
            }

        });

    }
</script>