<?php
$debug = 0;
session_start();
define('INCLUDE_CHECK', 1);
require_once "../../include/connect_mvc.php";
include('../../Models/Mdl_ConexionBD.php');
include('../../Models/Mdl_Venta.php');
include('../../assets/scripts/cadenas.php');
include('../../Models/Mdl_VentaPrevia.php');
include('../../Models/Mdl_Excepciones.php');

$idUser = $_SESSION['CREident'];
if ($debug == 1) {
    print_r($_GET);
    //  exit(0);
} else {
    error_reporting(0);
}
$id = !empty($_GET['id']) ? $_GET['id'] : '';
$idDetVenta = !empty($_GET['idDetVenta']) ? $_GET['idDetVenta'] : '';

if ($id == '' or $idDetVenta == '') {
    echo '<div class="alert alert-danger" role="alert">
    No se reconoció el detallado de venta, vuelve a intentarlo si el problema persiste notifica a Sistemas.</div>';
    exit(0);
}
$obj_ventas = new Venta($debug, $idUser);
$obj_ventasPrevias = new VentaPrevia($debug, $idUser);
$DataDetVenta = $obj_ventas->getDetVenta($idDetVenta);

?>
<input type="hidden" name="idRendimiento" value="<?= $id ?>">
<input type="hidden" name="idDetVenta" value="<?= $idDetVenta ?>">

<div class="row">
    <div class="col-md-6">
        <h6>Set's Seleccionados: <span id="totalSets">0.0</span></h6>
    </div>
    <div class="col-md-6">
        <h6>Unidades Seleccionadas: <span id="totalUnidades">0</span></h6>
    </div>
</div>
<div class="row mt-2">
    <div class="col-md-12">
        <table class="table table-sm">
            <thead>
                <th>Sub Lote Temola</th>
                <th>Piezas</th>
                <th>Sets</th>
                <th>Pzas. a ocupar</th>
            </thead>
            <tbody>
                <?php
                $DataSublote = $obj_ventas->getSubLotes($id, $idDetVenta);
                $totalPzasDisponibles=0;
                foreach ($DataSublote as $key => $value) {
                    $f_pzasTotales = formatoMil($DataSublote[$key]['pzasTotales'], 0);
                    $f_setsTotales = formatoMil($DataSublote[$key]['setsEmpacados'], 0);

                    echo "<tr>
                        <td>{$DataSublote[$key]['loteTemola']}</td>
                        <td>$f_pzasTotales</td>
                        <td>{$f_setsTotales}</td>
                        <td><input type='number' name='sublote_{$DataSublote[$key]['id']}' value='{$DataSublote[$key]['unidades']}' 
                        class='form-control sumatoria_sb' id='' min='0' step='1' max='{$DataSublote[$key]['pzasTotales']}'></td>
                    </tr>";
                    $totalPzasDisponibles+=$DataSublote[$key]['pzasTotales'];
                }
                ?>
            </tbody>
        </table>

    </div>
</div>
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="alert alert-warning" id="alert-valid-pzas" hidden role="alert">
            <b>Las piezas solicitadas pueden ser abastecidas con las que están disponibles.</b>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
        <?php
        $Data = $obj_ventasPrevias->consultaRequesicionAbierta($idDetVenta);
        $Data = Excepciones::validaConsulta($Data);
        $Data = $Data == '' ? array() : $Data;
        $_AbiertoRequi = count($Data) <= 0 ? false : true;
        $pzas = !$_AbiertoRequi ? '0' : $Data['pzasTotales'];
        ?>
        <label for="pzasRequeridas">Piezas Requeridas para la Venta</label>
        <input name="pzasRequeridas" step="1" id="pzasRequeridas" type="number" value="<?= $pzas ?>" onchange="agregarPiezas(this)" class="form-control" min="0">
        <span hidden id="badge-restante" class="badge badge-secondary"></span>

    </div>
</div>
<script>
    <?php if ($_AbiertoRequi) { ?>
        validacionPzas($("#pzasRequeridas"));
    <?php  } ?>
    $(".sumatoria_sb").change(function() {
        let result = 0;
        $(".sumatoria_sb").each(function() {
            value = $(this).val() == '' ? '0' : $(this).val();
            result = parseFloat(result) + parseFloat(value);
        });
        $("#totalUnidades").text(Intl.NumberFormat("es-MX", {
            currency: "MXN",
        }).format(result));
        $("#totalSets").text(Intl.NumberFormat("es-MX", {
            currency: "MXN",
        }).format(result / 4));
    });

    function validacionPzas(input) {
        let piezasRequeridas = parseFloat($(input).val());
        let piezasTotales = parseFloat("<?= $totalPzasDisponibles ?>");
        //console.log("Pzas Requeridas: ", piezasRequeridas);
        //console.log("Pzas Totales: ", piezasTotales);
        //Valida que no sea menor a la cantidad que existe
        let restante = parseFloat(piezasRequeridas - piezasTotales).toFixed(2);
        //console.log("Restantes: ", restante);
        $("#badge-restante").text("Requeridas: " + restante);
        $("#badge-restante").prop("hidden", false);
        if (piezasRequeridas <= piezasTotales) {
            mostrarElemento("alert-valid-pzas");
            return false;
        } else {
            ocultarElemento("alert-valid-pzas");
        }

        return true;
    }

    function agregarPiezas(input) {
        let piezasRequeridas = parseInt($(input).val());
        if (validacionPzas(input)) {
            $.ajax({
                url: '../Controller/ventasPrevias.php?op=requisionpzas',
                data: {
                    id: '<?= $idDetVenta ?>',
                    piezasRequeridas: piezasRequeridas
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
        } else {
            notificaBad("Verifica la cantidad de piezas requeridas.");
        }

    }
</script>