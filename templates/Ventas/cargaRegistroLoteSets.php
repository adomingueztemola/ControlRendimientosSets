<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../../include/connect_mvc.php";
include('../../assets/scripts/cadenas.php');

$debug = 0;
$idUser = $_SESSION['CREident'];
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}
$obj_rendimiento = new Rendimiento($debug, $idUser);
$obj_ventas = new Venta($debug, $idUser);
$DataAbierto = $obj_ventas->getVentaAbiertaXUser();
$id = $DataAbierto[0]['id'];
$idTipoVenta = $DataAbierto[0]['idTipoVenta'];
$interna = $idTipoVenta == '4' ? '1' : '0';

?>
<!---  --->
<div class="card-header border border-light p-2">
    <!-- <form id="formAddLote">-->
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

            <input type="hidden" name="tipoLote" id="tipoLote" value="">

            <div class="input-group mb-3">
                <label for="idRendimiento">Lotes Disponibles:</label>
                <select id="idRendimiento" name="idRendimiento" onchange="cargaDespliegueLotes(this)" class="form-control select2" style="width:100%;">
                    <option value="">Selecciona Lote</option>
                    <?php
                    $DataLotes = $obj_rendimiento->getCajasDeLotes($DataAbierto[0]['id'], '1', $interna);
                    $tipoLote = 1;
                    $count = 0;
                    $programaAnt = "";
                    foreach ($DataLotes as  $value) {

                        if ($count == 0) {
                            echo "<optgroup label='{$value['n_programa']}'>";
                        }
                        if ($value['n_programa'] != $programaAnt and $count > 0) {
                            echo "</optgroup><optgroup label='{$value['n_programa']}'>";
                        }
                        echo "<option data-proceso='{$value['tipoProceso']}' data-tipo='{$value['tipoLote']}' data-disponible='{$value['pzasTotales']}' 
                                  value='{$value['idRendimiento']}'>{$value['loteTemola']} ({$value['n_programa']}, {$value['n_materia']})</option>";
                        $programaAnt = $value['n_programa'];

                        $count++;
                    }
                    ?>
                    </optgroup>
                </select>
                <div class="input-group-append">
                    <!--<div id="bloqueo-btn-2" style="display:none">
                        <button class="btn btn-TWM" type="button" disabled="">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        </button>

                    </div>
                    <div id="desbloqueo-btn-2">
                        <button type="submit" class="button btn btn-success btn-md" id="btn-enviounidades"><i class="fas fa-check"></i></button>
                    </div>-->
                </div>
            </div>
        </div>
    </div>
    <!-- </form>-->
    <div class="row"> </div>
</div>
<form id="formDespliegueCajas">
    <input type="hidden" name="idVenta" value="<?= $id ?>">
    <!-------- Inicio de Despliegue de Cajas ------->
    <div class="row mt-1">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="despliegue-cajas">

        </div>
    </div>

</form>
<!-------- Fin de Despliegue de Cajas ------->

<div id="alert-error">

</div>
<div id="div-tabla-lote">

</div>

<!-- Modal Seleccion de Cajas -->
<div class="modal fade" id="cajasModal" tabindex="-1" role="dialog" aria-labelledby="cajasModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-TWM text-white">
                <h5 class="modal-title" id="cajasModalLabel">Detallado de Cajas</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                ...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>
<script src="../assets/scripts/clearData.js"></script>
<script src="../assets/scripts/calculoUnidadesXSets.js"></script>
<script src="../assets/libs/sweetalert2/dist/sweetalert2.all.min.js"></script>
<script>
    updateTable();

    function updateTable() {
        $('#div-tabla-lote').html('<div class="loading text-center"><img src="../assets/images/loading.gif" alt="loading" /><br/>Un momento, por favor...</div>');
        $('#div-tabla-lote').load('../templates/Ventas/cargaTablaLotesVentasSets.php?id=<?= $id ?>');
    }

    function cargaDespliegueLotes(select) {
        id = $(select).val();
        $('#despliegue-cajas').show()
        cargaContenido("despliegue-cajas", "../templates/Ventas/cargaCajasXVender.php?id=" + id, '1')
    }

    /*********************** USAR TODO EL LOTE ******************************/
    $("#usarTodo").change(function() {
        if ($(this).is(':checked')) {

        }
    });
    /*************** ALMACENAMIENTO DE DATOS GENERALES DE VENTAS*********************/
    $("#formAddLote").submit(function(e) {
        e.preventDefault();
        formData = $(this).serialize();
        $.ajax({
            url: '../Controller/ventas.php?op=agregarunidades',
            data: formData,
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                    setTimeout(() => {
                        bloqueoBtn("bloqueo-btn-2", 2)
                        clearForm("formAddLote")
                        updateTable()
                        $("#aviso-disponibilidad").attr("hidden", true);
                        $("#cant").attr({
                            "max": ""
                        });
                        $("#aviso-sobrepase").text("");
                    }, 1000);


                } else if (resp[0] == 0) {
                    notificaBad(resp[1])
                    bloqueoBtn("bloqueo-btn-2", 2)


                }
            },
            beforeSend: function() {
                bloqueoBtn("bloqueo-btn-2", 1)
            }

        });
    });

    /***************** ALMACENAMIENTO DE CAJAS ************************** */

    $("#formDespliegueCajas").submit(function(e) {
        e.preventDefault();
        formData = $(this).serialize();
        $.ajax({
            url: '../Controller/ventas.php?op=agregarcajas',
            data: formData,
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                    setTimeout(() => {
                        bloqueoBtn("bloqueo-btn-insert", 2)
                        cargaDespliegueLotes($("#idRendimiento"));
                        updateTable()

                        $("#aviso-disponibilidad").attr("hidden", true);

                    }, 1000);


                } else if (resp[0] == 0) {
                    notificaBad(resp[1])
                    bloqueoBtn("bloqueo-btn-insert", 2)


                }
            },
            beforeSend: function() {
                bloqueoBtn("bloqueo-btn-insert", 1)
            }

        });
    });






    /**************** LIMITE DE CANTIDAD DE LA VENTA*********************/
    function limitesVenta(select = "idRendimiento") {
        option = $("#idRendimiento option:selected").data('disponible');
        tipo = $("#idRendimiento option:selected").data('tipo');
        proceso = $("#idRendimiento option:selected").data('proceso');

        //Valida si es un proceso
        if (proceso == '2') {
            $("#sets").val(0)
            $("#unidades").attr("readonly", false);
            $("#div-sets").attr("hidden", true);
            $("#sets").attr("disabled", true);

        }



        $("#tipoLote").val(tipo);

        if (option > 0 && option != '') {
            $("#disponibilidad").val(option);
            $("#aviso-disponibilidad").attr("hidden", false);
            $("#aviso-disponibilidad").text("Disponibilidad de Unidades de hasta: " + Intl.NumberFormat("es-MX", {
                currency: "MXN",
            }).format(option) + "");
            $("#cant").attr('max', option);
        }


    }


    function esJson(str) {
        try {
            JSON.parse(str);
        } catch (e) {
            return false;
        }
        return true;
    }


    /*********************** GUARDAR ELEMENTOS DE ALMACEN PT **********************/
    function guardarAlmacenPT(input, idRendimiento) {
        cantidad = $(input).val();
        $.ajax({
            url: '../Controller/ventas.php?op=guardaralmacen',
            type: 'POST',
            data: {
                id: idRendimiento,
                cant: cantidad
            },
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])

                } else if (resp[0] == 0) {

                    notificaBad(resp[1])
                }
            },
            beforeSend: function() {

            }
        });

    }
</script>