<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../../include/connect_mvc.php";
include('../../assets/scripts/cadenas.php');

$debug = 0;
$idUser = $_SESSION['CREident'];
$obj_rendimiento = new Rendimiento($debug, $idUser);
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}
$date_start = !empty($_POST['date-start']) ? $_POST['date-start'] : "";
$date_end = !empty($_POST['date-end']) ? $_POST['date-end'] :  "";
$proceso = !empty($_POST['proceso']) ? $_POST['proceso'] : '';
$programa = !empty($_POST['programa']) ? $_POST['programa'] : '';
$materia = !empty($_POST['materia']) ? $_POST['materia'] : '';
$empaques = !empty($_POST['empaques']) ? $_POST['empaques'] : '';

/***************** CASTEO DE FECHAS ****************** */
if ($date_start != '' and $date_end != '') {
    $date_start = date("Y-m-d", strtotime(str_replace("/", "-", $date_start)));
    $date_end = date("Y-m-d", strtotime(str_replace("/", "-", $date_end)));
}


$filtradoFecha = ($date_start != '' and $date_end != '') ? "r.fechaEmpaque BETWEEN '$date_start' AND '$date_end'" : "1=1";
$filtradoProceso = $proceso != '' ? "r.idCatProceso='$proceso'" : "1=1";
$filtradoPrograma = $programa != '' ? "r.idCatPrograma='$programa'" : "1=1";
$filtradoMateria = $materia != '' ? "r.idCatMateriaPrima='$materia'" : "1=1";

$filtradoEmpaques = "1=1";
$filtradoEmpaques = $empaques == '1' ? "vw.totalEmp>0" : "1=1";
$filtradoEmpaques = $empaques == '2' ? "vw.totalEmp<=0" : "1=1";

$DataRendimiento = $obj_rendimiento->getRendimientosParaRecalculo($filtradoFecha, $filtradoProceso, $filtradoMateria, $filtradoPrograma, $filtradoEmpaques);
?>
<div class="table-responsive">
    <table id="table-pedidos" class="table table-sm">
        <thead>
            <tr class="">
                <th>#</th>
                <th>Vigencia</th>
                <th>Fecha de Engrase</th>
                <th>Semana</th>
                <th>Fecha de Empaque</th>
                <th>Lote</th>
                <th>Programa</th>
                <th>Proceso</th>
                <th>Materia Prima</th>

                <th>Pzas. Cortadas Teseo</th>
                <th>Set's Cortados Teseo</th>

                <th>Pzas. Rechazadas</th>
                <th>Set's Rechazados</th>
                <th>Rezago Rechazados</th>

                <th>Pzas. Recuperadas</th>
                <th>Set's Recuperadas</th>
                <th>Rezago Recuperadas</th>


                <th>Pzas. Empacadas</th>
                <th>Set's Empacados</th>

            </tr>
        </thead>
        <tbody>
            <?php
            $count = 0;
            foreach ($DataRendimiento as $key => $value) {
                $count++;
                //Mensaje de Piezas 
                $btnRecalcular = '<button onclick="recalcular(' . $DataRendimiento[$key]['id'] . ')"  class="button btn btn-xs btn-outline-success"><i class="far fa-clipboard"></i></button>';
                //CANDADO DE 24 HORAS DE RECALCULO
                $_vigente =  $DataRendimiento[$key]['_vigente'];
                $iconVigente = $_vigente == '1' ? "<button class='btn button btn-xs btn-info' 
                                                        data-toggle='modal' data-target='#ModalRecuperacion' title='Agregar Recuperación antes de las 24 horas' 
                                                        onclick='cargaRecuperacion24({$DataRendimiento[$key]['id']})'>
                                                        <i class='fas fa-lock-open'></i></button>" :
                    "<button type='button' data-toggle='modal' data-target='#ModalExcepciones' title='Enviar Excepción' 
                         onclick='cargaExcepcion({$DataRendimiento[$key]['id']})'
                         class='btn button btn-danger btn-xs'><i class='fas fa-lock'></i></button>";
                $iconVigente = $DataRendimiento[$key]['excepcion'] == '1' ? "<button type='button' data-toggle='modal' data-target='#ModalExcepciones' title='Detalles de la Petición de Excepción' 
                onclick='cargaExcepcion({$DataRendimiento[$key]['id']})' class='btn button btn-danger btn-xs'><i class='far fa-clock'></i></button>" : $iconVigente;

                $colortable = $_vigente == '1' ? "" : "table-danger";
            ?>
                <tr class="<?= $colortable ?>">
                    <td><?= $count ?></td>
                    <td style="width: 250px;"><?= $iconVigente ?><?= $DataRendimiento[$key]['f_fechaVigencia'] ?></td>
                    <td><?= $DataRendimiento[$key]['f_fechaEngrase'] ?></td>
                    <td><?= $DataRendimiento[$key]['semanaProduccion'] ?></td>
                    <td><?= $DataRendimiento[$key]['f_fechaEmpaque'] ?></td>
                    <td><?= $DataRendimiento[$key]['loteTemola'] ?></td>
                    <td><small><?= $DataRendimiento[$key]['n_programa'] ?></small></td>
                    <td><small><?= $DataRendimiento[$key]['c_proceso'] ?></small></td>
                    <td><small><?= $DataRendimiento[$key]['n_materia'] ?></small></td>

                    <td><?= formatoMil($DataRendimiento[$key]['pzasCortadasTeseo'], 2) ?></td>
                    <td><?= formatoMil($DataRendimiento[$key]['setsCortadosTeseo'], 2) ?></td>

                    <td><?= formatoMil($DataRendimiento[$key]['totalRech'], 2) ?></td>
                    <td><?= formatoMil($DataRendimiento[$key]['setsTotalRech'], 2) ?></td>
                    <td><?= formatoMil($DataRendimiento[$key]['rzgoRech'], 2) ?></td>

                    <td><?= formatoMil($DataRendimiento[$key]['totalRecu'], 2) ?></td>
                    <td><?= formatoMil($DataRendimiento[$key]['setsTotalRecu'], 2) ?></td>
                    <td><?= formatoMil($DataRendimiento[$key]['rzgoRecu'], 2) ?></td>

                    <td><?= formatoMil($DataRendimiento[$key]['pzasTotalEmp'], 2) ?></td>
                    <td><?= formatoMil($DataRendimiento[$key]['setsTotalFinEmp'], 2) ?></td>

                </tr>
            <?php


            }
            ?>

        </tbody>

    </table>
</div>
<!---- Modal de envio de Excepciones --->
<div class="modal" id="ModalExcepciones" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header text-white bg-TWM">
                <h5 class="modal-title">Excepciones de Ajuste de Inventario</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formExcepcion">
                <div class="modal-body" id="bodyModalExcepciones">


                </div>
                <div class="modal-footer">
                    <div id="bloqueo-btn-1" style="display:none">
                        <button class="btn btn-TWM" type="button" disabled="">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Espere...
                        </button>

                    </div>
                    <div id="desbloqueo-btn-1">
                        <button type="submit" id="btn-envioExcepcion" class="btn btn-success">Enviar Excepción</button>
                        <button type="button" class="btn btn-light" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!---- Modal de Recuperacion Antes de las 24 horas --->
<div class="modal" id="ModalRecuperacion" data-backdrop="static" data-keyboard="false" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header text-white bg-TWM">
                <h5 class="modal-title">Recuperación del Lote antes de las 24 horas</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formRecuperacion">
                <div class="modal-body" id="bodyModalRecuperacion">


                </div>
                <div class="modal-footer">
                    <div id="bloqueo-btn-3" style="display:none">
                        <button class="btn btn-TWM" type="button" disabled="">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Espere...
                        </button>

                    </div>
                    <div id="desbloqueo-btn-3">
                        <button type="submit" id="btn-envioExcepcion" class="btn btn-success">Recuperar</button>
                        <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    $("#table-pedidos").DataTable({
            dom: 'Bfrltip',
            autoWidth: false,
            "aaSorting": [],
            drawCallback: function() {
                $('[data-toggle="popover"]').popover();
            }
        }

    );


    /************************** CARGA DE MODULO DE SOLICITUDES ************************************/
    function cargaExcepcion(id) {
        $("#bodyModalExcepciones").load("../templates/Excepciones/cargaFormExcepciones.php?id=" + id);
    }

    function cargaRecuperacion24(id) {
        $("#bodyModalRecuperacion").load("../templates/Excepciones/cargaFormRecuperacion.php?id=" + id);

    }
    /************************** RECALCULO ENTRE EL RANGO DE 24 HORAS************************************/
    function recalcular(id) {
        var nEmpaque = $("#recalculo-" + id).val();
        var idRendimiento = id;

        if ($("#recalculo-" + id).attr("max") < nEmpaque && $("#recalculo-" + id).attr("max") != '') {
            notificaBad("Los Set's Empaquetados sobrepasan los Set's Cortados");
            return 0;
        } else {
            $.ajax({
                url: '../Controller/rendimiento.php?op=recalcular',
                data: {
                    id: idRendimiento,
                    setEmpacados: nEmpaque
                },
                type: 'POST',
                success: function(json) {
                    resp = json.split('|')
                    if (resp[0] == 1) {
                        //  bloqueoBtn("bloqueo-btn-3", 2)
                        notificaSuc(resp[1])
                        setTimeout(() => {
                            update()

                        }, 1500);
                    } else if (resp[0] == 0) {
                        // bloqueoBtn("bloqueo-btn-3", 2)

                        notificaBad(resp[1])
                    }
                },
                beforeSend: function() {
                    //bloqueoBtn("bloqueo-btn-3", 2)

                }
            });
        }


    }
    /************************** RECALCULO DE PIEZAS RECUPERADAS ENTRE EL RANGO DE 24 HORAS************************************/
    function recalcularPzasRecup(id) {
        var nRecuperadas = $("#recalculoRecup-" + id).val();
        var idRendimiento = id;

        if ($("#recalculoRecup-" + id).attr("max") < nRecuperadas && $("#recalculo-" + id).attr("max") != '') {
            notificaBad("Los Set's Rechazados sobrepasan los Set's Cortados");
            return 0;
        } else {
            $.ajax({
                url: '../Controller/rendimiento.php?op=recalcularpzasrecup',
                data: {
                    id: idRendimiento,
                    pzasRecuperadas: nRecuperadas
                },
                type: 'POST',
                success: function(json) {
                    resp = json.split('|')
                    if (resp[0] == 1) {
                        //  bloqueoBtn("bloqueo-btn-4", 2)
                        notificaSuc(resp[1])
                        setTimeout(() => {
                            update()

                        }, 1500);
                    } else if (resp[0] == 0) {
                        //bloqueoBtn("bloqueo-btn-3", 2)

                        notificaBad(resp[1])
                    }
                },
                beforeSend: function() {
                    //bloqueoBtn("bloqueo-btn-3", 2)

                }
            });
        }


    }
    /**************************************** ENVIO DE EXCEPCION DE AJUSTE DE INVENTARIO ****************************************************/
    $("#formExcepcion").submit(function(e) {
        e.preventDefault();
        formData = $(this).serialize();
        $.ajax({
            url: '../Controller/excepciones.php?op=enviarexcepcion',
            data: formData,
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                    bloqueoBtn("bloqueo-btn-1", 2)
                    clearForm("formExcepcion")
                    $("#ModalExcepciones").modal('hide');
                    setTimeout(() => {

                        update()
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
    /**************************************** RECUPERACION DE 24 HORAS AJUSTE DE INVENTARIO ****************************************************/
    $("#formRecuperacion").submit(function(e) {
        e.preventDefault();
        formData = $(this).serialize();
        $.ajax({
            url: '../Controller/excepciones.php?op=recuperacion24',
            data: formData,
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                    bloqueoBtn("bloqueo-btn-3", 2)
                    $("#ModalRecuperacion").modal('hide');
                    setTimeout(() => {
                        update()
                    }, 1000);


                } else if (resp[0] == 0) {
                    notificaBad(resp[1])
                    bloqueoBtn("bloqueo-btn-3", 2)


                }
            },
            beforeSend: function() {
                bloqueoBtn("bloqueo-btn-3", 1)
            }

        });
    });
</script>