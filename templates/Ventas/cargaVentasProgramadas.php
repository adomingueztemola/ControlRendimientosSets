<?php
define('INCLUDE_CHECK', 1);
require_once "../../include/connect_mvc.php";
include('../../Models/Mdl_ConexionBD.php');
include('../../Models/Mdl_VentaPrevia.php');
include('../../Models/Mdl_Excepciones.php');
include("../../assets/scripts/cadenas.php");
session_start();
setlocale(LC_TIME, 'es_ES.UTF-8');
$debug = 0;
$space = 1;
$idUser = $_SESSION['CREident'];
$nameUser = $_SESSION['CREnombreUser'];
$date_start = !empty($_POST['date-start']) ? $_POST['date-start'] : "";
$date_end = !empty($_POST['date-end']) ? $_POST['date-end'] :  "";
/***************** CASTEO DE FECHAS ****************** */
if($date_end!='' AND $date_start!=''){
    $date_start = date("Y-m-d", strtotime(str_replace("/", "-", $date_start)));
    $date_end = date("Y-m-d", strtotime(str_replace("/", "-", $date_end)));
    $filtradoFecha = "v.fechaFact BETWEEN '$date_start' AND '$date_end'";
}else{
    $filtradoFecha='1=1';
}


$obj_ventaprevia = new VentaPrevia($debug, $idUser);
$Data = $obj_ventaprevia->getVentasProgramadas($filtradoFecha);
$Data = Excepciones::validaConsulta($Data);
$Data = $Data == '' ? array() : $Data;
?>
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="table-responsive">
            <table class="table table-sm" id="table-ventas">
                <thead>
                    <tr>
                        <td>#</td>
                        <td>Num. Factura</td>
                        <td>Num. PL</td>
                        <td>Fecha de Factura</td>
                        <td>Tipo de Venta</td>
                        <td>Abastecida</td>
                        <td>Acción</td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $count = 1;
                    foreach ($Data as $key => $value) {
                        $lbl_numFactura = $Data[$key]['numFactura'] == '' ? 'N/A' : $Data[$key]['numFactura'];
                        $badge_abastecidas = "";
                        $estatus_abastecidas = "0";
                        #Abastecidas
                        $badge_abastecidas = $Data[$key]['totalAbastecidos'] == '0' ? '<span class="badge badge-danger">No Abastecida</span>' : $badge_abastecidas;
                        $badge_abastecidas = $Data[$key]['totalAbastecidos'] == $Data[$key]['totalRequeri'] ? '<span class="badge badge-success">Abastecida</span>' : $badge_abastecidas;
                        $badge_abastecidas = ($Data[$key]['totalAbastecidos'] < $Data[$key]['totalRequeri']
                            and $Data[$key]['totalAbastecidos'] > '0')
                            ? '<span class="badge badge-dark">En Proceso</span>' : $badge_abastecidas;
                        #Estatus De Abastecimiento
                        $estatus_abastecidas = $Data[$key]['totalAbastecidos'] == '0' ? '0' : $estatus_abastecidas;
                        $estatus_abastecidas = $Data[$key]['totalAbastecidos'] == $Data[$key]['totalRequeri'] ? '1' : $estatus_abastecidas;
                        $estatus_abastecidas = ($Data[$key]['totalAbastecidos'] < $Data[$key]['totalRequeri']
                            and $Data[$key]['totalAbastecidos'] > '0')
                            ? '0' : $estatus_abastecidas;

                        $btn_ver = "<button title='Ver Requerimientos de Piezas' 
                        onclick='updateRequerimientos({$Data[$key]['id']})' data-toggle='modal' 
                        data-target='#detailsModal' class='btn btn-info btn-xs'>
                        <i class='fas fa-align-justify'></i></button>";
                        $btn_ir = $estatus_abastecidas == '1' ?
                            "<button title='Cerrar Venta' onclick='abrirVenta({$Data[$key]['id']})' class='btn btn-success btn-xs'>
                                <i class=' fas fa-arrow-right'></i></button>" : "";
                        echo "<tr>
                            <td>{$count}</td>
                            <td>{$lbl_numFactura}</td>
                            <td>{$Data[$key]['numPL']}</td>
                            <td>{$Data[$key]['fFechaFact']}</td>
                            <td>{$Data[$key]['n_tipoVenta']}</td>
                            <td>{$badge_abastecidas}</td>
                            <td>{$btn_ver} {$btn_ir}</td>
                            </tr>";
                        $count++;
                    }


                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- Modal de Detalles-->
<div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" aria-labelledby="detailsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-TWM text-white">
                <h5 class="modal-title" id="detailsModalLabel">Requerimiento de Piezas</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="content-requerimientos">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
    $("#table-ventas").DataTable({})

    function updateRequerimientos(idVenta) {
        cargaContenido("content-requerimientos", "../templates/Ventas/cargaDetalleRequerimientos.php?id=" + idVenta, '1')

    }

    function abrirVenta(id) {
        $.ajax({
            url: '../Controller/ventasPrevias.php?op=abrirventa',
            type: 'POST',
            data: {
                id: id
            },
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    // Abrir nuevo tab
                    var win = window.open("registroventas.php", '_blank');
                    // Cambiar el foco al nuevo tab (punto opcional)
                    win.focus();
                } else if (resp[0] == 0) {

                    notificaBad(resp[1])
                }
            },
            beforeSend: function() {

            }
        });

    }
</script>