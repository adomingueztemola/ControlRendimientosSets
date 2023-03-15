<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../../include/connect_mvc.php";
include('../../Models/Mdl_ConexionBD.php');
include('../../Models/Mdl_Rendimiento.php');
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
$date_start = !empty($_POST['date-start']) ? $_POST['date-start'] : date("01/m/Y");
$date_end = !empty($_POST['date-end']) ? $_POST['date-end'] :  date("t/m/Y");
$proceso = !empty($_POST['proceso']) ? $_POST['proceso'] : '';
$programa = !empty($_POST['programa']) ? $_POST['programa'] : '';
$materia = !empty($_POST['materia']) ? $_POST['materia'] : '';
/***************** CASTEO DE FECHAS ****************** */

$date_start = date("Y-m-d", strtotime(str_replace("/", "-", $date_start)));
$date_end = date("Y-m-d", strtotime(str_replace("/", "-", $date_end)));

$filtradoFecha = "r.fechaEmpaque BETWEEN '$date_start' AND '$date_end'";
$filtradoProceso = $proceso != '' ? "r.idCatProceso='$proceso'" : "1=1";
$filtradoPrograma = $programa != '' ? "r.idCatPrograma='$programa'" : "1=1";
$filtradoMateria = $materia != '' ? "r.idCatMateriaPrima='$materia'" : "1=1";


$DataRendimiento = $obj_rendimiento->getRendimientos(
    $filtradoFecha,
    $filtradoProceso,
    $filtradoPrograma,
    $filtradoMateria,
    "r.tipoProceso='1'",
    "r.estado='4'"
);
?>
<div class="table-responsive">
    <table id="table-pedidos" class="table table-sm  table-hover">
        <thead>
            <tr class="">
                <th>#</th>
                <th>Fecha de Engrase</th>
                <th>Semana</th>
                <th>Fecha de Empaque</th>

                <th>Lote</th>
                <th>Programa</th>
                <th>Proceso</th>
                <th>Materia Prima</th>
                <th>Área WB en Recibo (pie<sup>2</sup>)</th>
                <th>Diferencia Área (pie<sup>2</sup>)</th>
                <th>Promedio Área (WB)</th>
                <th>% Dif. Area WB</th>
                <th>Pzas. rechazadas</th>
                <th>Área (pie<sup>2</sup>) de pzas rech.</th>
                <th>Recorte WB %</th>
                <th>Recorte Crust %</th>
                <th>Total Recorte %</th>
                <th>Humedad</th>
                <th>Área Crust</th>
                <th>Recorte Acabado</th>
                <th>Recorte Acabado %</th>
                <th>Perdida de Área WB a Crust</th>
                <th>Quiebre</th>
                <th>Suavidad</th>
                <th>Área Final (Teseo)</th>
                <th>Perdida de Área de Crust a Teseo</th>
                <th>Yield Inicial Teseo</th>
                <th>Pzas. Cortadas Teseo</th>
                <th>Sets Cortados Teseo</th>
                <th>Yield Final REAL (WB)</th>
                <th>Sets rechazados</th>
                <th>% Sets rechazo inicial</th>
                <th>Piezas Recuperadas</th>
                <th>Sets Recuperados</th>
                <th>% de Recuperacion</th>
                <th>% final de rechazo</th>
                <th>Sets Empacados</th>
                <th>Área de Crust por Set</th>
                <th>Área WB Real por Set</th>
                <th>Costo de WB por Unidad</th>
                <th>Usuario Registro</th>
                <th>Fecha Registro</th>


            </tr>
        </thead>
        <tbody>
            <?php
            $count = 0;
            $suma_areaWB = 0;
            $suma_diferenciaWB = 0;
            $suma_promedioAreaWB = 0;
            $suma_porcDifAreaWB = 0;
            $suma_piezasRechazadas = 0;
            $suma_areaPzasRechazo = 0;

            $suma_porcRecorteWB = 0;
            $suma_porcRecorteCrust = 0;
            $suma_totalRecorte = 0;
            $suma_humedad = 0;
            $suma_areaCrust = 0;
            $suma_perdidaAreaWBCrust = 0;
            $suma_quiebre = 0;
            $suma_suavidad = 0;
            $suma_areaFinal = 0;
            $suma_perdidaAreaCrustTeseo = 0;
            $suma_yieldInicialTeseo = 0;
            $suma_pzasCortadasTeseo = 0;
            $suma_setsCortadosTeseo = 0;
            $suma_yieldFinalReal = 0;
            $suma_setsRechazados = 0;
            $suma_porcSetsRechazoInicial = 0;
            $suma_piezasRecuperadas = 0;
            $suma_setsRecuperados = 0;
            $suma_porcRecuperacion = 0;
            $suma_porcFinalRechazo = 0;
            $suma_setsEmpacados = 0;
            $suma_areaCrustSet = 0;
            $suma_areaWBUnidad = 0;
            $suma_costoUnidad = 0;
            $suma_recorteAcabado= 0;
            $suma_porcRecorteAcabado= 0;

            foreach ($DataRendimiento as $key => $value) {
                $count++;
                $suma_areaWB += $DataRendimiento[$key]['areaWB'];
                $suma_diferenciaWB += $DataRendimiento[$key]['diferenciaArea'];
                $suma_promedioAreaWB += $DataRendimiento[$key]['promedioAreaWB'];
                $suma_porcDifAreaWB += $DataRendimiento[$key]['porcDifAreaWB'];
                $suma_piezasRechazadas += $DataRendimiento[$key]['piezasRechazadas'];
                $suma_areaPzasRechazo += $DataRendimiento[$key]['areaPzasRechazo'];

                $suma_porcRecorteWB += $DataRendimiento[$key]['porcRecorteWB'];
                $suma_porcRecorteCrust += $DataRendimiento[$key]['porcRecorteCrust'];
                $suma_totalRecorte += $DataRendimiento[$key]['totalRecorte'];
                $suma_humedad += $DataRendimiento[$key]['humedad'];
                $suma_areaCrust += $DataRendimiento[$key]['areaCrust'];
                $suma_perdidaAreaWBCrust += $DataRendimiento[$key]['perdidaAreaWBCrust'];
                $suma_quiebre += $DataRendimiento[$key]['quiebre'];
                $suma_suavidad += $DataRendimiento[$key]['suavidad'];
                $suma_areaFinal += $DataRendimiento[$key]['areaFinal'];
                $suma_perdidaAreaCrustTeseo += $DataRendimiento[$key]['perdidaAreaCrustTeseo'];
                $suma_yieldInicialTeseo += $DataRendimiento[$key]['yieldInicialTeseo'];
                $suma_pzasCortadasTeseo += $DataRendimiento[$key]['pzasCortadasTeseo'];
                $suma_setsCortadosTeseo += $DataRendimiento[$key]['setsCortadosTeseo'];
                $suma_yieldFinalReal += $DataRendimiento[$key]['yieldFinalReal'];
                $suma_setsRechazados += $DataRendimiento[$key]['setsRechazados'];
                $suma_porcSetsRechazoInicial += $DataRendimiento[$key]['porcSetsRechazoInicial'];
                $suma_piezasRecuperadas += $DataRendimiento[$key]['piezasRecuperadas'];
                $suma_setsRecuperados += $DataRendimiento[$key]['setsRecuperados'];
                $suma_porcRecuperacion += $DataRendimiento[$key]['porcRecuperacionFinal'];
                $suma_porcFinalRechazo += $DataRendimiento[$key]['porcFinalRechazo'];
                $suma_setsEmpacados += $DataRendimiento[$key]['setsEmpacados'];
                $suma_areaCrustSet += $DataRendimiento[$key]['areaCrustSet'];
                $suma_areaWBUnidad += $DataRendimiento[$key]['areaWBUnidad'];
                $suma_costoUnidad += $DataRendimiento[$key]['costoWBUnit'];

                $suma_recorteAcabado+= $DataRendimiento[$key]['recorteAcabado'];
                $suma_porcRecorteAcabado+= $DataRendimiento[$key]['porcRecorteAcabado'];
                //Area de Espera de llenado
                $diferenciaArea = $DataRendimiento[$key]['estado'] == '2' ? '<i class="fas fa-spinner fa-pulse"></i>' : formatoMil($DataRendimiento[$key]['diferenciaArea']);
                $promedioArea = $DataRendimiento[$key]['estado'] == '2' ? '<i class="fas fa-spinner fa-pulse"></i>' : formatoMil($DataRendimiento[$key]['promedioAreaWB']);
                $porcDifAreaWB = $DataRendimiento[$key]['estado'] == '2' ? '<i class="fas fa-spinner fa-pulse"></i>' : formatoMil($DataRendimiento[$key]['porcDifAreaWB']);
                $costoWBUnit = $DataRendimiento[$key]['estado'] == '2' ? '<i class="fas fa-spinner fa-pulse"></i>' : formatoMil($DataRendimiento[$key]['costoWBUnit']);
                $perdidaAreaWBCrust = ($DataRendimiento[$key]['estado'] == '2' and $DataRendimiento[$key]['tipoMateriaPrima'] == '2') ? '<i class="fas fa-spinner"></i>' : formatoMil($DataRendimiento[$key]['perdidaAreaWBCrust']);

                //Mensaje de Piezas 
                $fto = formatoMil($DataRendimiento[$key]['piezasRechazadas']);
                $comentarios_rechazo = $DataRendimiento[$key]['piezasRechazadas'] > 0 ?
                    "<label data-toggle='popover' title='Comentarios del Rechazo' data-content='{$DataRendimiento[$key]['comentariosRechazo']}'>{$fto}</label>" : $fto;
                $btnSolicitud = "";
                $btnSolicitud = ($DataRendimiento[$key]['envioSolicitud'] != '1') ? '<button onclick="editarRendimiento(' . $DataRendimiento[$key]['id'] . ')"  data-toggle="modal" data-target="#ModalSolicitud" 
                class="button btn btn-xs btn-outline-info" title="Enviar Solicitud"><i class="fas fa-edit"></i></button>' : $btnSolicitud;
                $btnSolicitud = ($DataRendimiento[$key]['envioSolicitud'] == '2') ? '<button class="button btn btn-xs btn-info" title="Ir a la Edición" onclick="abrirEdicion(' . $DataRendimiento[$key]['id'] . ')"> <i class="fas fa-external-link-alt"></i></button>' : $btnSolicitud;
                $btnSolicitud = ($DataRendimiento[$key]['envioSolicitud'] == '1') ? '<i class="fas fa-spinner fa-pulse"></i> <small>Espera</small>' : $btnSolicitud;
            ?>
                <tr>
                    <td><?= $count ?></td>
                    <td><?= $DataRendimiento[$key]['f_fechaEngrase'] ?></td>
                    <td><?= $DataRendimiento[$key]['semanaProduccion'] ?></td>
                    <td><?= $DataRendimiento[$key]['f_fechaEmpaque'] ?></td>

                    <td><?= $DataRendimiento[$key]['loteTemola'] ?></td>
                    <td><small><?= $DataRendimiento[$key]['n_programa'] ?></small></td>
                    <td><small><?= $DataRendimiento[$key]['n_proceso'] ?></small></td>
                    <td><small><?= $DataRendimiento[$key]['n_materia'] ?></small></td>
                    <td><?= formatoMil($DataRendimiento[$key]['areaWB']) ?></td>
                    <td><?= $diferenciaArea ?></td>
                    <td><?= $promedioArea ?></td>
                    <td><?= $porcDifAreaWB ?></td>
                    <td><?= $comentarios_rechazo ?></td>
                    <td><?= formatoMil($DataRendimiento[$key]['areaPzasRechazo']) ?></td>
                    <td><?= formatoMil($DataRendimiento[$key]['porcRecorteWB']) ?></td>
                    <td><?= formatoMil($DataRendimiento[$key]['porcRecorteCrust']) ?></td>
                    <td><?= formatoMil($DataRendimiento[$key]['totalRecorte']) ?></td>
                    <td><?= formatoMil($DataRendimiento[$key]['humedad']) ?></td>
                    <td><?= formatoMil($DataRendimiento[$key]['areaCrust']) ?></td>
                    <td><?=formatoMil( $DataRendimiento[$key]['recorteAcabado'])?></td>
                    <td><?=formatoMil( $DataRendimiento[$key]['porcRecorteAcabado'])?>%</td>

                    <td><?= $perdidaAreaWBCrust ?></td>
                    <td><?= formatoMil($DataRendimiento[$key]['quiebre']) ?></td>
                    <td><?= formatoMil($DataRendimiento[$key]['suavidad']) ?></td>
                    <td><?= formatoMil($DataRendimiento[$key]['areaFinal']) ?></td>
                    <td><?= formatoMil($DataRendimiento[$key]['perdidaAreaCrustTeseo']) ?></td>
                    <td><?= formatoMil($DataRendimiento[$key]['yieldInicialTeseo']) ?></td>
                    <td><?= formatoMil($DataRendimiento[$key]['pzasCortadasTeseo']) ?></td>
                    <td><?= formatoMil($DataRendimiento[$key]['setsCortadosTeseo']) ?></td>
                    <td><?= formatoMil($DataRendimiento[$key]['yieldFinalReal']) ?></td>
                    <td><?= formatoMil($DataRendimiento[$key]['setsRechazados']) ?></td>
                    <td><?= formatoMil($DataRendimiento[$key]['porcSetsRechazoInicial']) ?></td>
                    <td><?= formatoMil($DataRendimiento[$key]['piezasRecuperadas']) ?></td>
                    <td><?= formatoMil($DataRendimiento[$key]['setsRecuperados']) ?></td>

                    <td><?= formatoMil($DataRendimiento[$key]['porcRecuperacionFinal']) ?></td>
                    <td><?= formatoMil($DataRendimiento[$key]['porcFinalRechazo']) ?></td>
                    <td><?= formatoMil($DataRendimiento[$key]['setsEmpacados']) ?></td>
                    <td><?= formatoMil($DataRendimiento[$key]['areaCrustSet']) ?></td>
                    <td><?= formatoMil($DataRendimiento[$key]['areaWBUnidad']) ?></td>
                    <td><?= $costoWBUnit ?></td>

                    <td><?= $DataRendimiento[$key]['str_usuario'] ?></td>
                    <td><?= $DataRendimiento[$key]['f_fechaReg'] ?></td>



                </tr>
            <?php


            }
            ?>

        </tbody>
        <tfoot>
            <tr class="bg-TWM text-white">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>Totales:</td>

                <td><?= formatoMil($suma_areaWB) ?></td>
                <td><?= formatoMil($suma_diferenciaWB) ?></td>
                <td><?= formatoMil($suma_promedioAreaWB) ?></td>
                <td><?= formatoMil($suma_porcDifAreaWB) ?></td>
                <td><?= formatoMil($suma_piezasRechazadas) ?></td>
                <td><?= formatoMil($suma_areaPzasRechazo) ?></td>
                <td><?= formatoMil($suma_porcRecorteWB) ?></td>
                <td><?= formatoMil($suma_porcRecorteCrust) ?></td>
                <td><?= formatoMil($suma_totalRecorte) ?></td>
                <td><?= formatoMil($suma_humedad > 0 ? $suma_humedad / $count : 0) ?></td>
                <td><?= formatoMil($suma_areaCrust) ?></td>
                <td><?= formatoMil($suma_recorteAcabado) ?></td>
                <td><?= formatoMil($suma_porcRecorteAcabado) ?></td>
                <td><?= formatoMil($suma_perdidaAreaWBCrust) ?></td>

                <td><?= formatoMil($suma_quiebre > 0 ? $suma_quiebre / $count : 0) ?></td>
                <td><?= formatoMil($suma_suavidad > 0 ? $suma_suavidad / $count : 0) ?></td>
                <td><?= formatoMil($suma_areaFinal) ?></td>
                <td><?= formatoMil($suma_perdidaAreaCrustTeseo) ?></td>
                <td><?= formatoMil($suma_yieldInicialTeseo) ?></td>
                <td><?= formatoMil($suma_pzasCortadasTeseo) ?></td>
                <td><?= formatoMil($suma_setsCortadosTeseo) ?></td>
                <td><?= formatoMil($suma_yieldFinalReal) ?></td>
                <td><?= formatoMil($suma_setsRechazados) ?></td>
                <td><?= formatoMil($suma_porcSetsRechazoInicial) ?></td>
                <td><?= formatoMil($suma_piezasRecuperadas) ?></td>
                <td><?= formatoMil($suma_setsRecuperados) ?></td>
                <td><?= formatoMil($suma_porcRecuperacion) ?></td>
                <td><?= formatoMil($suma_porcFinalRechazo) ?></td>
                <td><?= formatoMil($suma_setsEmpacados) ?></td>
                <td><?= formatoMil($suma_areaCrustSet) ?></td>
                <td><?= formatoMil($suma_areaWBUnidad) ?></td>
                <td><?= formatoMil($suma_costoUnidad > 0 ? $suma_costoUnidad / $count : 0) ?></td>

                <td></td>
                <td></td>




            </tr>
        </tfoot>
    </table>
</div>
<!-- Inicio Modal Editar Rendimiento -->
<div class="modal fade" id="ModalSolicitud" tabindex="-1" role="dialog" aria-labelledby="solicitudModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-TWM text-white">
                <h5 class="modal-title" id="solicitudModalLabel">Solicitud de Edición de Rendimiento</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formEdicionSolicitud">
                <div class="modal-body" id="solicitudModal-body">
                    <input type="hidden" name="idRendimiento" value="" id="idRendimientoSolicitud">
                    <div class="row">
                        <div class="col-md-12">
                            <label for="descripcionSolicitud">Motivo:</label>
                            <textarea name="descripcionSolicitud" id="descripcionSolicitud" cols="30" rows="3" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div id="bloqueo-btn-edit" style="display:none">
                        <button class="btn btn-TWM" type="button" disabled="">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Espere...
                        </button>

                    </div>
                    <div id="desbloqueo-btn-edit">
                        <button type="button" class="btn btn-light" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-success">Enviar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Fin Modal Editar Rendimiento -->

<script>
    $("#table-pedidos").DataTable({
            dom: 'Bfrltip',
            autoWidth: false,
            drawCallback: function() {
                $('[data-toggle="popover"]').popover();
            },
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

                },
                /* {
                                extend: 'pdf',
                                text: 'Archivo PDF',
                                exportOptions: {

                                },
                                orientation: "landscape",
                                pageSize: "TABLOID",
                                footer: true, 
                                customize : function(doc) {doc.pageMargins = [5, 5, 5,5 ]; },



                            }*/
                , {
                    extend: 'print',
                    text: 'Imprimir',
                    exportOptions: {

                    },
                    footer: true

                }
            ]
        }

    );
    $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('btn btn-TWM mr-1');
    /************************* Agregar ID de Rendimiento a la solicitud *****************************/
    function editarRendimiento(idRendimiento) {
        $("#idRendimientoSolicitud").val(idRendimiento)
    }
    /************************* Formulario de envio de solicitud *****************************/
    $("#formEdicionSolicitud").submit(function(e) {
        e.preventDefault();
        formData = $(this).serialize();
        $.ajax({
            url: '../Controller/solicitudesEdicion.php?op=enviarsolicitud',
            data: formData,
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                    bloqueoBtn("bloqueo-btn-edit", 2);
                    clearForm("formEdicionSolicitud");
                    $("#ModalSolicitud").modal('hide');
                    setTimeout(() => {
                        update()
                    }, 1000);
                } else if (resp[0] == 0) {
                    notificaBad(resp[1]);
                    bloqueoBtn("bloqueo-btn-edit", 2);


                }
            },
            beforeSend: function() {
                bloqueoBtn("bloqueo-btn-edit", 1);
            }

        });
    });
    /************************* Abrir Edicion *****************************/
    function abrirEdicion(id){
        $.ajax({
            url: '../Controller/solicitudesEdicion.php?op=abriredicion',
            data: {id:id},
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    location.href="registrorendimiento.php";

                } else if (resp[0] == 0) {
                    notificaBad(resp[1]);
                    bloqueoBtn("bloqueo-btn-edit", 2);


                }
            },
            beforeSend: function() {
                bloqueoBtn("bloqueo-btn-edit", 1);
            }

        });

    }
</script>