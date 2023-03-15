<?php
$debug = 0;
session_start();
define('INCLUDE_CHECK', 1);
require_once "../../include/connect_mvc.php";
include('../../assets/scripts/cadenas.php');
$idUser = $_SESSION['CREident'];
if ($debug == 1) {
    print_r($_GET);
    //  exit(0);
} else {
    error_reporting(0);
}
$id = !empty($_GET['id']) ? $_GET['id'] : '';
$obj_ventas = new Venta($debug, $idUser);
$DataAbierto = $obj_ventas->getVentaAbiertaXUser();
$idVenta = $DataAbierto[0]['id'];
$idTipoVenta = $DataAbierto[0]['idTipoVenta'];
$interna= $idTipoVenta=='4'?'1':'0';

$Data = $obj_ventas->getDetalladoCaja($id, $idVenta,$interna);
$Data = Excepciones::validaConsulta($Data);
?>
<div class="row mb-1">
    <div class="col-lg-11 col-md-11 col-sm-11 col-xs-11">
        <h4><span class="label label-info label-rounded mb-2">CAJAS SELECCIONADAS: <span id="lbl_contadorCajas">0</span></span></h4>

    </div>
    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 text-right">
        <button type="button" onclick="$('#despliegue-cajas').hide()" class="button btn btn-xs btn-info"><i class="fas fa-minus"></i></button>
    </div>

</div>

<input type="hidden" name="idLote" value="<?= $id ?>">
<div id="accordion2" class="accordion" role="tablist" aria-multiselectable="true">
    <?php
    $count = 1;
    $countCajas = 0;
    foreach ($Data as $value) {
        $checked = $value['idVenta'] == $idVenta ? 'checked' : '';
        $ArrayMix=explode(',', $value['mixLotes']);
        $mixLbl= count($ArrayMix)>=2?'Mix: '.$value['mixLotes']:"<i class=''></i>";
        if ($value['idVenta'] == $idVenta) {
            $countCajas++;
        }
    ?>
        <div class="card">
            <div class="card-header" role="tab" id="headingOne">
                <div class="row">
                    <div claas="col-lg-11 col-md-11 col-sm-11 col-xs-11">
                        <h5 class="mb-0">
                            <?php
                            if ($value['idVenta'] == $idVenta) { ?>
                                <span id="bloqueo-btn-deshab" style="display:none">
                                    <button class="btn btn-success" type="button" disabled="">
                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    </button>

                                </span>
                                <span id="desbloqueo-btn-deshab">
                                    <button type="button" class="button btn btn-xs btn-light" onclick="deshabilitarCaja('<?= $value['numCaja'] ?>|<?= $value['idEmpaque'] ?>', '<?= $idVenta ?>')"><i class="fas fa-minus"></i></button>
                                </span>
                            <?php
                            } else {
                            ?>
                                <input type="checkbox" <?= $checked ?> class="contadorCajas mx-2" name="cajas[]" value="<?= $value['numCaja'] ?>|<?= $value['idEmpaque'] ?>" id="chck-caja<?= $value['id'] ?>">
                            <?php } ?>
                            <label for="chck-caja<?= $value['id'] ?>">#<?= $count ?> Fecha de Empaque: <?= $value['fFechaEmpaque'] ?> 
                            <small><?=$mixLbl?></small></label>
                        </h5>
                    </div>
                    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 text-right">
                        <h5 class="mb-0">
                            <a data-toggle="collapse" onclick="cargaDetCaja('<?= $value['numCaja'] ?>','<?= $value['idEmpaque'] ?>', '<?=$count?>')" data-parent="#accordion2" href="#collapse<?= $value['id'] ?>" aria-expanded="true" aria-controls="collapseOne">
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
                        <tbody id="table-caja<?=$count?>">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php
        $count++;
    }
    ?>


</div>
<hr>
<div class="row mt-1">
    <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10"></div>
    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
        <div id="bloqueo-btn-insert" style="display:none">
            <button class="btn btn-success" type="button" disabled="">
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Espere...
            </button>

        </div>
        <div id="desbloqueo-btn-insert">
            <button type="sumbit" class="button btn btn-success btn-md">Insertar</button>
        </div>
    </div>

</div>
<script>
    $("#lbl_contadorCajas").text("<?= $countCajas ?>")
    // Comprobar cuando cambia un checkbox
    $('.contadorCajas').on('change', function() {
        sumCajas = 0;
        $('.contadorCajas:checked').each(function() {
            sumCajas++;
        });
        $("#lbl_contadorCajas").text(sumCajas);
    });
    //Deshabilitar Caja de la Venta
    function deshabilitarCaja(value, idVenta) {
        $.ajax({
            url: '../Controller/ventas.php?op=deshabilitarcajas',
            data: {
                noCaja: value.split('|')[0],
                idEmpaque: value.split('|')[1],
                idVenta: idVenta
            },
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                    updateTable()
                    cargaDespliegueLotes($("#idRendimiento"));

                } else if (resp[0] == 0) {
                    notificaBad(resp[1])


                }
            },
            beforeSend: function() {
                bloqueoBtn("bloqueo-btn-1", 1)
            }

        });
    }
    //Ver Detallado de Caja
    function cargaDetCaja(numCaja, idEmpaque, contador){
        $.ajax({
            url: '../Controller/ventas.php?op=detalladocaja',
            data: {
                numCaja: numCaja,
                idEmpaque:idEmpaque
            },
            type: 'POST',
            dataType: "json",

            success: function(json) {
                $("#table-caja"+contador).html('')
               $.each(json, function(index, info) {
                    $("#table-caja"+contador).append(`
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
                loadingSmall("table-caja"+contador, '1');
            }

        });
    }
</script>