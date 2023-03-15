<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../../include/connect_mvc.php";
include('../../assets/scripts/cadenas.php');
$debug = 0;
$idUser = $_SESSION['CREident'];
if ($debug == 1) {
    print_r($_POST);
} else {
    error_reporting(0);
}
$date_start = !empty($_POST['date-start']) ? $_POST['date-start'] : date("01/m/Y 00:00:00");
$date_end = !empty($_POST['date-end']) ? $_POST['date-end'] :  date("t/m/Y 23:59:00");
/***************** CASTEO DE FECHAS ****************** */
$date_start = date("Y-m-d 00:00:00", strtotime(str_replace("/", "-", $date_start)));
$date_end = date("Y-m-d 23:59:00", strtotime(str_replace("/", "-", $date_end)));
$filtradoFecha = "mr.fechaReg BETWEEN '$date_start' AND '$date_end'";
$obj_trabajos = new Reacondicionamiento($debug, $idUser);
?>
<div class="table-responsive">
    <table class="mt-4 table table-sm" id="table-faltantes">
        <thead>
            <tr>
                <th>#</th>
                <th>Fecha</th>
                <th>Tipo de Lote</th>
                <th>Lote Retrabajado</th>
                <th>Defecto</th>
                <th>Programa</th>
                <th>Trabajador Recibió</th>
                <th>Observaciones</th>

                <th>Entrega</th>
                <th>Reasignación</th>
                <th>Total Recuperación</th>
                <th>Acción</th>

            </tr>
        </thead>
        <tbody>
            <?php
            $DataRecuperadas = $obj_trabajos->getRecuperaciones($filtradoFecha);
            $count = 0;
            $DataLotes = $obj_trabajos->getLotesSetsDisponibles();
            $DataLotes = Excepciones::validaConsulta($DataLotes);
            foreach ($DataRecuperadas as $key => $value) {
                $count++;
                $lblTipoLote = $DataRecuperadas[$key]['tipoRendInicio'] == '1' ? "Lote Registrado" : "Lote No Identificado";
                $lblLoteTrabajado = $DataRecuperadas[$key]['tipoRendInicio'] == '1' ? $DataRecuperadas[$key]['nLoteInicial'] : $DataRecuperadas[$key]['nombreRendInicio'];
                $f_TotalInicial = formatoMil($DataRecuperadas[$key]['totalInicial'], 0);
                $f_TotalRecuperado = formatoMil($DataRecuperadas[$key]['totalRecuperacion'], 0);
                $lblDefecto = $DataRecuperadas[$key]['n_defecto'] != '' ? $DataRecuperadas[$key]['n_defecto'] : "<i>N/A</i>";
                $lblPorc = formatoMil($DataRecuperadas[$key]['porcPerdidaRecuperacion'], 2) . '%';
                $lbl_fechaEntrega = $value['estado'] == '1' ? "<input type='date' value='{$value['fechaEntrega']}' onchange='cambiaFecha(this, {$value['id']})' class='form-control' id='fEntrega-{$value['id']}'>" : $DataRecuperadas[$key]['f_fechaFinal'];
                $lbl_total = $value['estado'] == '1' ?
                    "<button class='btn btn-xs btn-primary' onclick='cargaDetalladoPzas({$value['id']}, \"{$value['nLoteInicial']}\",  \"{$value['pzasDispRechazo']}\")' data-toggle='modal' data-target='#detPzasModal'><i class='fas fa-keyboard'></i></button>"
                    : "<span data-toggle='tooltip' data-placement='top' data-html='true' title='{$value['detPzas']}'>$f_TotalRecuperado</span>";


                $select = '<select name="idRendRecuperado" onchange="cambiaLote(this, ' . $value['id'] . ')" style="width:100%" id="idRendRecuperado-' . $value['id'] . '" class="form-control select2">
                <option value="">Selecciona Lote</option>';
                foreach ($DataLotes as $keyLte => $valueLte) {
                    $selected = $valueLte['id'] == $value['idRendRecup'] ? 'selected' : '';
                    $select .= "<option $selected value='{$valueLte['id']}'>{$valueLte['loteTemola']}</option>";
                }

                $select .= ' </select>';
                $lbl_LoteRecup = $value['estado'] == '1' ? $select : $DataRecuperadas[$key]['nLoteRecup'];
                $btn = $value['estado'] == '1' ? "<button class='btn btn-xs btn-primary' title='Cerrar Registro de Recuperación' onclick='obtieneDataRecuperacion({$DataRecuperadas[$key]['id']})'>
                        <i class='fas fa-lock'></i> </button>" : "<i class='fas fa-lock'></i>";

                $btnDe = $value['estado'] == '1' ? "<button class='btn btn-xs btn-danger' title='Eliminar Registro' onclick='eliminarRecuperacion({$DataRecuperadas[$key]['id']})'>
                <i class='fas fa-trash-alt'></i> </button>" : "";
                echo "<tr>
                <td>{$count}</td>
                <td>{$DataRecuperadas[$key]['f_fecha']}</td>
                <td>{$lblTipoLote}</td>
                <td>{$lblLoteTrabajado}</td>
                <td>{$lblDefecto}</td>
                <td>{$DataRecuperadas[$key]['n_programa']}</td>
                <td>{$DataRecuperadas[$key]['nombreCompletoTrabajador']}</td>
                <td>{$DataRecuperadas[$key]['observaciones']}</td>

                <td>{$lbl_fechaEntrega}</td>

                <td>{$lbl_LoteRecup}</td>
                <td>{$lbl_total}</td>
                <td class='text-center'>
                <div id='bloqueo-btn-fin{$value['id']}' style='display:none'>
                    <button class='btn btn-primary btn-xs' type='button' disabled=''>
                        <span class='spinner-border spinner-border-sm' role='status' aria-hidden='true'></span>
                    </button>
                </div>
                <div id='desbloqueo-btn-fin{$value['id']}'> $btnDe $btn</div></td>
            </tr>";
            }

            ?>
        </tbody>
    </table>
</div>
<div class="modal fade" id="detPzasModal" role="dialog" aria-labelledby="detPzasModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content block-detallado">
            <div class="modal-header bg-TWM text-white">
                <h5 class="modal-title" id="detPzasModalLabel">Detallado de Piezas: <span id="loteTemola-txt"></span></h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formDetPza">
                <div class="modal-body" id="carga-detpzas">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cerrar</button>
                    <button type="submit" id="btn-guardarpzas" class="btn btn-success">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>




<script src="../assets/scripts/clearData.js"></script>
<script>
    $("#table-faltantes").DataTable({
        drawCallback: function(settings, json) {
            $('[data-toggle="tooltip"]').tooltip();
        },
    });
    /*************CAMBIA FECHA ******************/
    function cambiaFecha(input, id) {
        $.ajax({
            url: '../Controller/reacondicionamiento.php?op=agregarfechaentrega',
            data: {
                fechaEntrega: $(input).val(),
                id: id
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
    /*************CAMBIA LOTE ******************/
    function cambiaLote(input, id) {
        $.ajax({
            url: '../Controller/reacondicionamiento.php?op=agregarloterecup',
            data: {
                loteRecup: $(input).val(),
                id: id
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
    /*************CAMBIA TOTAL ******************/
    $("#formDetPza").submit(function(e) {
        e.preventDefault();
        formData = $(this).serialize();
        $.ajax({
            url: '../Controller/reacondicionamiento.php?op=agregartotal',
            data: formData,
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                    bloqueoModal(e, 'block-detallado', 2)
                    setTimeout(() => {
                        cerrarModal("detPzasModal")

                    }, 1000);


                } else if (resp[0] == 0) {
                    bloqueoModal(e, 'block-detallado', 2)

                    notificaBad(resp[1])


                }
            },
            beforeSend: function() {
                bloqueoModal(e, 'block-detallado', 1)

            }

        });
    });
    //Consulta campos validos para aplicar la recuperacion
    function obtieneDataRecuperacion(id) {
        bloqueoBtn("bloqueo-btn-fin" + id, 1)
        $.ajax({
            type: "post",
            url: "../Controller/reacondicionamiento.php?op=cargajsonrecuperacion",
            dataType: "json",
            data: {
                id: id
            },
            success: function(data) {
                formatted = {
                    totalRecuperacion: data.totalRecuperacion,
                    idRendRecup: data.idRendRecup,
                    idRendInicio: data.idRendInicio,
                };
                //Valida que los datos esten completos
                ErrorLog = "Registre ";
                StatusLog = 0;
                if (formatted.idRendRecup == '' || formatted.idRendRecup == '0') {
                    ErrorLog += "Lote de Recuperación, ";
                    StatusLog = 1;
                }
                if (formatted.totalRecuperacion == '' || formatted.totalRecuperacion <= '0') {
                    ErrorLog += "Total de Piezas en Recuperación, ";
                    StatusLog = 1;
                }
                if (StatusLog == 1) {
                    notificaBad(ErrorLog)
                    return 0;
                } else {
                    finalizaReacond(id, data.idRendRecup);
                }
            }
        });

    }
    /*************FINALIZA REACONDICIONAMIENTO ******************/
    function finalizaReacond(id, idLote) {
        $.ajax({
            url: '../Controller/reacondicionamiento.php?op=cerrarrecuperacion',
            data: {
                id: id,
                idLote: idLote
            },
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                    update()
                } else if (resp[0] == 0) {
                    bloqueoBtn("bloqueo-btn-fin" + id, 2)
                    notificaBad(resp[1])
                }
            },
            beforeSend: function() {
                bloqueoBtn("bloqueo-btn-fin" + id, 1)
            }

        });
    }

    function eliminarRecuperacion(id) {
        $.ajax({
            url: '../Controller/reacondicionamiento.php?op=eliminarrecuperacion',
            data: {
                id: id            },
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                    update()
                } else if (resp[0] == 0) {
                    bloqueoBtn("bloqueo-btn-fin" + id, 2)
                    notificaBad(resp[1])
                }
            },
            beforeSend: function() {
                bloqueoBtn("bloqueo-btn-fin" + id, 1)
            }

        });
    }

    function cargaDetalladoPzas(id, loteTemola, stk) {
        cargaContenido("carga-detpzas", "../templates/Reacondicionamiento/cargaDetPzasRecup.php?id=" + id + "&stk=" + stk, '1');
        $("#loteTemola-txt").text(loteTemola);
    }
</script>