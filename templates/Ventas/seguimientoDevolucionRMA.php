<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../../include/connect_mvc.php";
include('../../Models/Mdl_ConexionBD.php');
include('../../Models/Mdl_Excepciones.php');

include('../../Models/Mdl_Venta.php');
include('../../Models/Mdl_Devolucion.php');
include('../../assets/scripts/cadenas.php');

$debug = 0;
$idUser = $_SESSION['CREident'];
if ($debug == 1) {
    print_r($_GET);
    //  exit(0);
} else {
    error_reporting(0);
}
$data = !empty($_GET['data']) ? $_GET['data'] : '';

$obj_ventas = new Venta($debug, $idUser);
$obj_devolucion = new Devolucion($debug, $idUser);
$DataVentas = $obj_ventas->getDetVentas($data);
$DataVentas = Excepciones::validaConsulta($DataVentas);
$numFactura = $DataVentas[0]['numFactura'] == '' ? 'N/A' : $DataVentas[0]['numFactura'];
?>
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title"><b>Información de Venta</b></h4>
            </div>
            <div class="card-body collapse show">
                <div class="row">
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">Num. Factura: <?= $numFactura ?></div>
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">Num. P.L.: <?= $DataVentas[0]['numPL'] ?></div>
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">Fecha de Venta: <?= $DataVentas[0]['f_fechaFact'] ?></div>
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">Tipo de Venta: <?= $DataVentas[0]['n_tipoVenta'] ?></div>
                </div>
            </div>

        </div>
    </div>
</div>
<div class="row mb-2">
    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8"></div>
    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
        <button class="btn button btn-info" data-toggle="modal" data-target="#devolucionModal" onclick="cargaDevolucion(<?= $data ?>)">Registrar Devolución</button>
    </div>
    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
        <div id="bloqueo-btn-canc" style="display:none">
            <button class="btn btn-danger" type="button" disabled="">
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>

                Espere...
            </button>

        </div>
        <div id="desbloqueo-btn-canc">
            <button class="btn button btn-danger" onclick="cancelacionVta(<?= $data ?>)"><i class="fas fa-power-off"></i> Cancelación Parcial</button>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <table class="table table-sm">
            <thead class="card-header">
                <tr>
                    <th>#</th>
                    <th>Lote</th>
                    <th>Programa</th>
                    <th>Cantidad</th>

                </tr>
            </thead>
            <tbody>
                <?php

                $DataDetDevolucion = $obj_devolucion->getLoteXVenta($data);
                $count = 0;
                $bloqueoBtn = count($DataDetDevolucion) > 0 ? '' : 'hidden';
                if (count($DataDetDevolucion) > 0) {
                    foreach ($DataDetDevolucion as $key => $value) {
                        $count++;
                        $f_cantidad = formatoMil($DataDetDevolucion[$key]['unidades'], 2);
                        echo "<tr>
                        <td>{$count}</td>
                        <td>{$DataDetDevolucion[$key]['loteTemola']}</td>
                        <td>{$DataDetDevolucion[$key]['n_programa']}</td>
                        <td>{$f_cantidad}</td>
                       
                    </tr>";
                    }
                } else {
                    echo "<tr>
                            <td colspan='6' class='text-center text-danger'>No hay registro de detalle de venta por devolver</td>
                        </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 card-header" id="carga-devolucioneshechas">

    </div>
</div>
<script src="../assets/scripts/clearData.js"></script>
<script>
     cargaContenido("carga-devolucioneshechas", "../templates/Ventas/cargaDevolucionesHechas.php?data=<?=$data?>", '1')
    $("#formAddDevolucion").submit(function(e) {
        e.preventDefault();
        formData = $(this).serialize();
        $.ajax({
            url: '../Controller/devolucion.php?op=devolverventa',
            data: formData,
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                    setTimeout(() => {
                        bloqueoBtn("bloqueo-btn-dev", 2)
                        clearForm("formAddDevolucion")
                        cargaDetalleVenta(<?= $data ?>)
                    }, 1000);


                } else if (resp[0] == 0) {
                    notificaBad(resp[1])
                    bloqueoBtn("bloqueo-btn-dev", 2)


                }
            },
            beforeSend: function() {
                bloqueoBtn("bloqueo-btn-dev", 1)
            }

        });
    });

    function cancelacionVta(id) {
        $.ajax({
            url: '../Controller/devolucion.php?op=cancelarventa',
            data: {
                id: id
            },
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    setTimeout(() => {
                        bloqueoBtn("bloqueo-btn-canc", 2)
                        location.reload(true);
                    }, 1000);


                } else if (resp[0] == 0) {
                    notificaBad(resp[1])
                    bloqueoBtn("bloqueo-btn-canc", 2)


                }
            },
            beforeSend: function() {
                bloqueoBtn("bloqueo-btn-canc", 1)
            }

        });

    }

    function cargaDevolucion(idVenta) {
        cargaContenido("carga-formDevol", "../templates/Ventas/cargaFormularioDevol.php?data=" + idVenta, '1')
    }
</script>