<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../../include/connect_mvc.php";
include('../../Models/Mdl_ConexionBD.php');
include('../../Models/Mdl_Venta.php');
include('../../assets/scripts/cadenas.php');
include("../../Models/Mdl_TipoVenta.php");

$debug = 0;
$idUser = $_SESSION['CREident'];
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}
$id = !empty($_POST['id']) ? $_POST['id'] : '';

$obj_ventas = new Venta($debug, $idUser);
$obj_tipo = new TipoVenta($debug, $idUser);

$DataVentas = $obj_ventas->getLoteXVenta($id);
?>

<?php
$DataVenta = $obj_ventas->getDetVentas($id);
?>
<input type="hidden" name="id" id="idVentaEdita" value="<?= $id ?>">
<div class="row">
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <label class="form-label required" for="fechaFacturacion">Fecha Facturación:</label>
        <input type="date" <?= $disabled ?> required class="form-control" value="<?= $DataVenta[0]['fechaFact'] ?>" name="fechaFacturacion" id="fechaFacturacion"></input>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <label class="form-label required" class="form-label" for="idTipoVenta">Tipo de Venta:</label>
        <select required <?= $disabled ?> onchange="requerirFactura()" name="idTipoVenta" id="idTipoVenta" class="form-control select2" style="width:100%">
            <option value="">Selecciona Tipo de Venta</option>
            <?php
            $DataTipo = $obj_tipo->getTipos("tv.estado='1'");
            foreach ($DataTipo as $key => $value) {
                $selected = $DataTipo[$key]['id'] == $DataVenta[0]['idTipoVenta'] ? 'selected' : '';
                echo "<option data-tipo='{$DataTipo[$key]['tipo']}'  $selected value='{$DataTipo[$key]['id']}'>{$DataTipo[$key]['nombre']}</option>";
            }
            ?>
        </select>

    </div>
</div>

<div class="row">

    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12" id="div-factura">
        <label class="form-label required" for="numFactura">Núm. de Factura:</label>
        <span id="bloqueo-btn-res" style="display:none">
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>

        </span>
        <span id="desbloqueo-btn-res">
            <small id="resultbusq"></small>
        </span>

        <input required <?= $disabled ?> type="text" class="form-control" value="<?= $DataVenta[0]['numFactura'] ?>" data-factura="<?= $DataVenta[0]['numFactura'] ?>" name="numFactura" id="numFactura"></input>

    </div>

    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <label class="form-label required" for="numPL">Núm. de PL:</label>
        <span id="bloqueo-btn-res2" style="display:none">
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>

        </span>
        <span id="desbloqueo-btn-res2">
            <small id="resultbusq2"></small>
        </span>
        <input required <?= $disabled ?> type="text" class="form-control" value="<?= $DataVenta[0]['numPL'] ?>" name="numPL" id="numPL"></input>
    </div>
</div>
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <label for="motivo" class="form-label required">Motivo de Cambio</label>
        <textarea name="motivo" id="motivo" class="form-control" required rows="5"></textarea>
    </div>
</div>
<!--- INICIO DETALLADO DE VENTAS--->
<div class="card-panel mt-2">
    <div class="card-heading  bg-light">
        <div class="row">
            <div class="col-lg-11 col-md-11 col-xs-11 col-sm-11">
                <h4 class="card-title">Detallado de la Venta</h4>
            </div>
            <div class="col-lg-1 col-md-1 col-xs-1 col-sm-1">
                <i class="btn  ti-arrow-circle-down" data-toggle="collapse" href="#content-detallado" aria-expanded="true" aria-controls="content-detallado"></i>
            </div>
        </div>

    </div>
    <div class="card-body collapse" id="content-detallado">
        <div class="table-responsive">
            <table class="table table-sm table-bordered">
                <thead>
                    <tr>
                        <th>Lote Temola</th>
                        <th>Proceso</th>
                        <th>Materia Prima</th>
                        <th>1s</th>
                        <th>2s</th>
                        <th>3s</th>
                        <th>4s</th>
                        <th>Total</th>
                        <th>Unidade(s)</th>
                        <th>Almacén PT</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="13" class="text-center"><b>Lotes de Set's/M<sup>2</sup></b></td>
                    </tr>
                    <?php
                    $DataVentas = !is_array($DataVentas) ? array() : $DataVentas;
                    if (count($DataVentas) > 0) {
                        foreach ($DataVentas as $key => $value) {
                            $unidades = formatoMil($DataVentas[$key]["unidades"]);
                            $almacenPT = formatoMil($DataVentas[$key]['almacenPT']);


                            $almacenPT = formatoMil($DataVentas[$key]['almacenPT']);

                            echo "
                        <tr>
                                <td>{$DataVentas[$key]['loteTemola']}</td>
                                <td>{$DataVentas[$key]['c_proceso']}</td>
                                <td>{$DataVentas[$key]['n_materia']}</td>
                                <td>" . formatoMil($DataVentas[$key]['1s']) . "</td>
                                <td>" . formatoMil($DataVentas[$key]['2s']) . "</td>
                                <td>" . formatoMil($DataVentas[$key]['3s']) . "</td>
                                <td>" . formatoMil($DataVentas[$key]['4s']) . "</td>
                                <td>" . formatoMil($DataVentas[$key]['total_s']) . "</td>
                                <td>{$unidades}</td>

                                <td>{$almacenPT}</td>
                        </tr>
                        ";
                        }
                    } else {
                        echo "<tr>
                        <td colspan='13' class='text-center'>Sin Lotes Registrados</td>
                        </tr>";
                    }
                    ?>
                    <tr>
                        <td colspan="13" class="text-center"><b>Lotes de Etiquetas/Calzado</b></td>
                    </tr>
                    <?php
                    $DataVentas = $obj_ventas->getEtiquetaXVenta($id);

                    $DataVentas = !is_array($DataVentas) ? array() : $DataVentas;
                    if (count($DataVentas) > 0) {
                        foreach ($DataVentas as $key => $value) {
                            $unidades = formatoMil($DataVentas[$key]["unidades"]);
                            $almacenPT = formatoMil($DataVentas[$key]['almacenPT']);


                            $almacenPT = formatoMil($DataVentas[$key]['almacenPT']);

                            echo "
                            <tr>
                                    <td>{$DataVentas[$key]['loteTemola']}</td>
                                    <td>-</td>
                                    <td>{$DataVentas[$key]['n_materia']}</td>
                                    <td>" . formatoMil($DataVentas[$key]['1s']) . "</td>
                                    <td>" . formatoMil($DataVentas[$key]['2s']) . "</td>
                                    <td>" . formatoMil($DataVentas[$key]['3s']) . "</td>
                                    <td>" . formatoMil($DataVentas[$key]['4s']) . "</td>
                                    <td>" . formatoMil($DataVentas[$key]['total_s']) . "</td>
                                    <td>{$unidades}</td>

                                    <td>{$almacenPT}</td>
                            </tr>
                            ";
                        }
                    } else {
                        echo "<tr>
                                <td colspan='13' class='text-center'>Sin Lotes Registrados</td>
                            </tr>";
                    }
                    ?>

                </tbody>
            </table>
        </div>
    </div>
</div>
<!--- FIN DETALLADO DE VENTAS--->

<script src="../assets/scripts/clearData.js"></script>
<script src="../assets/scripts/validaNumVenta.js"></script>
<script src="../assets/scripts/validaNumPL.js"></script>

<script>
    <?php if ($DataVenta[0]['tipo'] == 2) {
        echo "$('#div-factura').attr('hidden', true); $('#numFactura').removeAttr('required');";
     } ?>
    /********************* REQUERIR FACTURAR ***************************/
    function requerirFactura(select) {
        data = $("#idTipoVenta option:selected").data("tipo");
        $("#fact").val(data);
        switch (data) {
            case 1:
                $("#div-factura").attr("hidden", false);
                $("#numFactura").prop("required", true);
                $("#numFactura").val( $("#numFactura").data("factura"));
                break;

            case 2:
                $("#div-factura").attr("hidden", true);
                $("#numFactura").removeAttr("required");
                $("#numFactura").val("");


                break;
        }
    }
</script>