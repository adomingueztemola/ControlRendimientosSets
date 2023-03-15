<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../../include/connect_mvc.php";
include('../../Models/Mdl_ConexionBD.php');
include('../../Models/Mdl_Rendimiento.php');
include('../../Models/Mdl_Venta.php');

include('../../assets/scripts/cadenas.php');
$debug = 0;
$idUser = $_SESSION['CREident'];
if ($debug == 1) {
    print_r($_GET);
    //  exit(0);
} else {
    error_reporting(0);
}
/*$id = (!empty($_GET['id'])  and $_GET['id'] != '') ? $_GET['id'] : '0';
if ($id == '0') {
    echo "<div class='alert alert-primary' role='alert'>
                No se encontró la Venta Iniciada, vuelve a intentarlo, si el problea persiste notifica al departamento de Sistemas.
           </div>";
    exit(0);
}*/
$obj_rendimiento = new Rendimiento($debug, $idUser);
$obj_ventas = new Venta($debug, $idUser);
$DataAbierto = $obj_ventas->getVentaAbiertaXUser();
$id = $DataAbierto[0]['id'];

?>
<div class="card-header border border-light p-2">
    <form id="formAddLote">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <input type="hidden" name="tipoLote" id="tipoLote" value="">

                <div class="input-group mb-3">
                    <label for="idRendimiento">Lotes Disponibles:</label>
                    <select id="idRendimiento" name="idRendimiento" onchange="limitesVenta()" class="form-control select2" style="width:90%;">
                        <option value="">Selecciona Lote</option>
                        <optgroup label="Lotes de Metros">
                            <?php
                            $DataLotes = $obj_rendimiento->getRendimientosConAlmacen($DataAbierto[0]['id'], "2");
                            $tipoLote = 1;
                            $count = 0;

                            foreach ($DataLotes as $key => $value) {

                                echo "<option data-proceso='{$DataLotes[$key]['tipoProceso']}' data-tipo='{$DataLotes[$key]['tipoLote']}' data-disponible='{$DataLotes[$key]['pzasTotales']}' 
                                  value='{$DataLotes[$key]['idRendimiento']}|{$DataLotes[$key]['id']}'>{$DataLotes[$key]['loteTemola']} ({$DataLotes[$key]['n_programa']}, {$DataLotes[$key]['n_materia']})</option>";
                            }
                            ?>
                        </optgroup>
                    </select>
                    <div class="input-group-append">
                        <div id="bloqueo-btn-2" style="display:none">
                            <button class="btn btn-TWM" type="button" disabled="">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            </button>

                        </div>
                        <div id="desbloqueo-btn-2">
                            <button type="submit" class="button btn btn-success btn-md" id="btn-enviounidades"><i class="fas fa-check"></i></button>
                        </div>
                    </div>
                </div>








            </div>
        </div>
        <div class="row">
        </div>
</div>
</form>
</div>
<div id="alert-error">

</div>
<div id="div-tabla-lote">

</div>
<!--<hr>
<div class="row mb-2">
    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-7">
    </div>
    <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5">
        <div id="bloqueo-btn-3" style="display:none">
            <button class="btn btn-TWM" type="button" disabled="">
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Espere...
            </button>

        </div>
        <div id="desbloqueo-btn-3">
            <button type="button" onclick="eliminarVenta()" class="button btn btn-danger">Eliminar Pre-Venta</button>
            <button type="button" onclick="finalizarVenta()" class="button btn btn-success">Finalizar</button>
        </div>
    </div>
</div>-->

<script src="../assets/scripts/clearData.js"></script>
<script src="../assets/scripts/calculoUnidadesXSets.js"></script>
<script src="../assets/libs/sweetalert2/dist/sweetalert2.all.min.js"></script>
<script>
    updateTable();

    function updateTable() {
        $('#div-tabla-lote').html('<div class="loading text-center"><img src="../assets/images/loading.gif" alt="loading" /><br/>Un momento, por favor...</div>');
        $('#div-tabla-lote').load('../templates/Ventas/cargaTablaLotesVentasMetros.php?id=<?= $id ?>');
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
    /*************** ELIMINAR PRE REGISTRO DE VENTA *********************/
    function eliminarVenta() {
        $.ajax({
            url: '../Controller/ventas.php?op=eliminarventa',
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    bloqueoBtn("bloqueo-btn-3", 2)
                    location.reload();
                } else if (resp[0] == 0) {
                    bloqueoBtn("bloqueo-btn-3", 2)

                    notificaBad(resp[1])
                }
            },
            beforeSend: function() {
                bloqueoBtn("bloqueo-btn-3", 2)

            }
        });

    }

    function esJson(str) {
        try {
            JSON.parse(str);
        } catch (e) {
            return false;
        }
        return true;
    }

    /*********************** FINALIZAR VENTA & DESCUENTO DE ALMACEN **********************/
 /*   function finalizarVenta() {
        $.ajax({
            url: '../Controller/ventas.php?op=finalizarventa',
            type: 'POST',
            success: function(json) {
                isJson = false
                if (esJson(json)) {
                    json_act = JSON.parse(json);
                    isJson = true
                } else {
                    resp = json.split('|')
                }
                console.log(json)

                if (isJson) {
                    table = "";
                    json_act.lotes.forEach(element => {
                        table += "<td>" + element + "</td>";
                    });
                    $("#alert-error").html(
                        `<div class="alert alert-danger" role="alert">
                            <b>${json_act.message}</b>
                            <table class="table table-bordered table-sm">
                            <tr>${table}</tr>
                            </table>
                        </div>`)
                    return 0;
                } else {
                    if (resp[0] == 1) {
                        bloqueoBtn("bloqueo-btn-3", 2)
                        location.reload();
                    } else if (resp[0] == 0) {
                        bloqueoBtn("bloqueo-btn-3", 2)
                        if (resp[1] != 3) {
                            notificaBad(resp[1]);
                        } else {
                            bloqueoBtn("bloqueo-btn-3", 2);
                            Swal.fire({
                                title: "Error",
                                text: resp[2],
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    }
                }

                /*else if (resp[0] == 3) { //Error de Validacion de distribucion
                                   json = JSON.stringify(resp[1]);
                                   console.table(json)
                               }*/
       /*     },
            beforeSend: function() {
                bloqueoBtn("bloqueo-btn-3", 2)

            }
        });
    }*/
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