<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../../include/connect_mvc.php";
include('../../Models/Mdl_ConexionBD.php');
include('../../Models/Mdl_Excepciones.php');

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
$obj_devolucion = new Devolucion($debug, $idUser);
$Data = $obj_devolucion->getDevolucionesXVenta($data);
$Data = Excepciones::validaConsulta($Data);
$Data = $Data == '' ? array() : $Data;
foreach ($Data as $key => $value) {
    $colorTarj = $Data[$key]['estado'] == '0' ? 'border border-danger' : '';

?>
    <div class="col-md-12 single-note-item all-category note-social">
        <div class="card card-body <?=$colorTarj ?>">
            <div class="row">
                <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
                    <span class="side-stick"></span>
                    <h5 class="note-title  mb-0"><b>Devolución <?= $Data[$key]['rma'] ?>:</b> Venta con Factura <?= $Data[$key]['numFactura'] ?></h5>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                    <?php
                    if ($Data[$key]['estado'] == '2') {
                    ?>
                        <button class="btn btn-xs btn-danger" onclick="cambiaEstatusDevolucion(<?= $Data[$key]['id'] ?>)"><i class="fas fa-times"></i></button>
                    <?php
                    }else{
                        echo "<p class='text-danger'>Cancelada</p>";
                    }
                    ?>
                </div>
            </div>

            <p class="note-date font-12 text-muted"><?= $Data[$key]['f_fecha'] ?></p>
            <p>
                <button class="btn btn-TWM" type="button" data-toggle="collapse" data-target="#listaArticulos<?= $Data[$key]['id'] ?>" aria-expanded="false" aria-controls="listaArticulos<?= $Data[$key]['id'] ?>">
                    Detallado de Devolución
                </button>
            </p>
            <div class="collapse" id="listaArticulos<?= $Data[$key]['id'] ?>">
                <div class="card card-body">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Programa</th>
                                <th>Cantidad</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $Detalle = $obj_devolucion->getDetXDevolucion($Data[$key]['id']);
                            $Detalle = Excepciones::validaConsulta($Detalle);
                            $Detalle = $Detalle == '' ? array() : $Detalle;
                            $count = 0;
                            foreach ($Detalle as $key2 => $value) {
                                $count++;
                                $f_cantidad = formatoMil($Detalle[$key2]['cantidad'], 2);
                                echo "<tr>
                                    <td>{$count}</td>
                                    <td>{$Detalle[$key2]['n_programa']}</td>
                                    <td>{$f_cantidad}</td>
                                </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php
} ?>
<script>
    function cambiaEstatusDevolucion(id) {
        $.ajax({
            url: '../Controller/devolucion.php?op=cambiarestatusdevol',
            data: {
                id: id
            },
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    setTimeout(() => {
                        cargaContenido("carga-devolucioneshechas", "../templates/Ventas/cargaDevolucionesHechas.php?data=<?= $data ?>", '1')

                    }, 1000);


                } else if (resp[0] == 0) {
                    notificaBad(resp[1])


                }
            },
            beforeSend: function() {}

        });
    }
</script>