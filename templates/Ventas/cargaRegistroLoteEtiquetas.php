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
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}
$id = (!empty($_POST['id'])  and $_POST['id'] != '') ? $_POST['id'] : '0';
$id = '1';
if ($id == '0') {
    echo "<div class='alert alert-primary' role='alert'>
                No se encontró la Venta Iniciada, vuelve a intentarlo, si el problea persiste notifica al departamento de Sistemas.
           </div>";
    exit(0);
}
$obj_rendimiento = new Rendimiento($debug, $idUser);
$obj_ventas = new Venta($debug, $idUser);
$DataAbierto = $obj_ventas->getVentaAbiertaXUser();
?>
<!---  --->
<div class="card-header border border-light p-2">
    <form id="formAddLote">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <input type="hidden" name="tipoLote" id="tipoLote" value="">
                <label for="idRendimiento">Lotes Disponibles:</label>
                <select id="idRendimiento" name="idRendimiento" onchange="limitesVenta()" class="form-control select2" style="width:100%;">
                    <option value="">Selecciona Lote</option>
                    <optgroup label="Lotes de Set's/Metros">
                        <?php
                        $DataLotes = $obj_rendimiento->getRendimientosConAlmacen($DataAbierto[0]['id']);
                        $tipoLote = 1;
                        $count = 0;

                        foreach ($DataLotes as $key => $value) {
                            if ($DataLotes[$key]['tipoLote'] == '2' and $count == 0) {
                                echo "</optgroup><optgroup label='Lotes de Etiquetas/Calzado'>";
                                $count++;
                            }
                            echo "<option data-tipo='{$DataLotes[$key]['tipoLote']}' data-disponible='{$DataLotes[$key]['almacenPT']}' 
                                  value='{$DataLotes[$key]['idRendimiento']}|{$DataLotes[$key]['idSubLote']}'>{$DataLotes[$key]['loteTemola']} ({$DataLotes[$key]['n_materia']})</option>";
                        }
                        ?>
                    </optgroup>
                </select>
            </div>
        </div>
        <div class="row">

            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                <label for="cant">Unidades: </label>
                <input class="form-control" step="0.01" min="0.01" type="number" name="cant" id="cant">
                <small id="aviso-disponibilidad" class="text-danger"></small>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 mt-4 pt-1">
                <div id="bloqueo-btn-2" style="display:none">
                    <button class="btn btn-TWM" type="button" disabled="">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    </button>

                </div>
                <div id="desbloqueo-btn-2">
                    <button type="submit" class="button btn btn-success btn-md"><i class="fas fa-check"></i>Agregar</button>
                </div>
            </div>
        </div>
    </form>
</div>
<div id="alert-error">

</div>
<div id="div-tabla-lote">

</div>
<hr>
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
</div>

<script src="../assets/scripts/clearData.js"></script>
<script>
    updateTable();

    function updateTable() {
        $('#div-tabla-lote').html('<div class="loading text-center"><img src="../assets/images/loading.gif" alt="loading" /><br/>Un momento, por favor...</div>');
        $('#div-tabla-lote').load('../templates/Ventas/cargaTablaLotesVentas.php');
    }
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
        $("#tipoLote").val(tipo);

        if (option > 0 && option != '') {
            $("#aviso-disponibilidad").text("Disponibilidad de Unidades de hasta: " + option + "");
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
    function finalizarVenta() {
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

                        notificaBad(resp[1])

                    }
                }

                /*else if (resp[0] == 3) { //Error de Validacion de distribucion
                                   json = JSON.stringify(resp[1]);
                                   console.table(json)
                               }*/
            },
            beforeSend: function() {
                bloqueoBtn("bloqueo-btn-3", 2)

            }
        });
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