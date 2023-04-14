<?php
$debug = 0;
session_start();
define('INCLUDE_CHECK', 1);
require_once "../../include/connect_mvc.php";
$idUser = $_SESSION['CREident'];
$nameUser = $_SESSION['CREnombreUser'];
include('../../assets/scripts/cadenas.php');

$obj_empaque = new Empaque($debug, $idUser);
$id = !empty($_POST['id']) ? $_POST['id'] : '';
$Data = $obj_empaque->getDetalladoCaja($id);
$Data = Excepciones::validaConsulta($Data);
$DataLote = $obj_empaque->getDetRendimientos($id);
$DataLote = Excepciones::validaConsulta($DataLote);
if (count($Data) == '0') {
    echo '<div class="alert alert-danger" role="alert">
    ¡Sin cajas empacadas por el momento!
  </div>';
    exit(0);
}
?>
<div class="row mb-1">
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
        <h3><span class="label label-info label-rounded mb-2">CAJAS TOTALES: <span id="lbl_contadorCajas">0</span></span></h3>
        <h3><span class="label label-info label-rounded mb-2">PIEZAS TOTALES: <span id="lbl_contadorPzas">0</span></span></h3>

    </div>
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
        <table class="table table-sm table-bordered">
            <tbody>
                <tr>
                    <td>Programa </td>
                    <td><?= ($DataLote['n_programa']) ?></td>
                </tr>
                <tr>
                    <td>Precio Unit Proveedor </td>
                    <td><?= formatoMil($DataLote['precioUnitFactUsd'], 2) ?> USD.</td>
                </tr>
                <tr>
                    <td>Piezas Cortadas en Teseo </td>
                    <td><?= formatoMil($DataLote['pzasCortadasTeseo'], 0) ?></td>
                </tr>
            </tbody>

        </table>
    </div>


</div>
<div id="accordion2" class="accordion" role="tablist" aria-multiselectable="true">
    <?php
    $count = 1;
    $countCajas = 0;
    foreach ($Data as $value) {
        $ArrayMix = explode(',', $value['mixLotes']);
        $mixLbl = count($ArrayMix) >= 2 ? 'Mix: ' . $value['mixLotes'] : "<i class=''></i>";
        $regDatos = $value['regDatos'] == '1' ? '' : 'text-danger';
        $lblInterno = $value['interna'] == '1' ? '(Interna)' : '';
        $lblLabel = $value["lblLote"] != '' ? "<i class='fas fa-ticket-alt'></i> {$value["lblLote"]}" : '';
        $iconSales = $value['vendida'] == '1' ? '<i class="fas fa-shopping-cart"></i> Vendida ' . $lblInterno : $lblInterno;
    ?>
        <div class="card">
            <div class="card-header" role="tab" id="headingOne">
                <div class="row">
                    <div claas="col-lg-9 col-md-9 col-sm-9 col-xs-9">
                        <h5 class="mb-0">
                            <label class="<?= $regDatos ?>" for="chck-caja<?= $value['id'] ?>">#<?= $count ?> Fecha de Empaque: <?= $value['fFechaEmpaque'] ?>
                                <small><?= $mixLbl ?></small></label>
                        </h5>
                    </div>
                    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 px-1">
                        <?= $iconSales ?>
                    </div>
                    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1  px-1">
                        <?= $lblLabel ?>
                    </div>
                    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 text-right">
                        <h5 class="mb-0">
                            <a data-toggle="collapse" onclick="cargaDetCaja('<?= $value['numCaja'] ?>','<?= $value['idEmpaque'] ?>', '<?= $count ?>')" data-parent="#accordion2" href="#collapse<?= $value['id'] ?>" aria-expanded="true" aria-controls="collapseOne">
                                <i class="far fa-plus-square"></i>
                            </a>
                        </h5>
                    </div>
                </div>
            </div>
            <div id="collapse<?= $value['id'] ?>" class="collapse" role="tabpanel" aria-labelledby="headingOne">
                <div class="card-body">
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>Lote</th>
                                <th>12:00</th>
                                <th>03:00</th>
                                <th>06:00</th>
                                <th>09:00</th>
                            </tr>
                        </thead>
                        <tbody id="table-caja<?= $count ?>">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php
        $countCajas++;
        $count++;
    }
    $pzas = $countCajas * 400;
    ?>


</div>
<script>
    $("#lbl_contadorCajas").text("<?= $countCajas ?>")
    $("#lbl_contadorPzas").text("<?= formatoMil($pzas, 0) ?>")

    //Ver Detallado de Caja
    function cargaDetCaja(numCaja, idEmpaque, contador) {
        $.ajax({
            url: '../Controller/ventas.php?op=detalladocaja',
            data: {
                numCaja: numCaja,
                idEmpaque: idEmpaque
            },
            type: 'POST',
            dataType: "json",

            success: function(json) {
                $("#table-caja" + contador).html('')
                $.each(json, function(index, info) {
                    $("#table-caja" + contador).append(`
                        <tr>
                            <td>${info.loteTemola}</td>
                            <td>${info._12}</td>
                            <td>${info._3}</td>
                            <td>${info._6}</td>
                            <td>${info._9}</td>

                        </tr>
                    `);

                });
            },
            beforeSend: function() {
                loadingSmall("table-caja" + contador, '1');
            }

        });
    }
</script>