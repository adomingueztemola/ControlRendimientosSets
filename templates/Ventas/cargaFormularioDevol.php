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
?>
<!--- REGISTRO DE DEVOLUCION-->
<form id="formInitDevolucion">
    <input type="hidden" name="idVenta" value="<?= $data ?>">
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <label for="rma" class="form-label required">Id RMA:</label>
            <input type="text" autocomplete="off" name="rma" id="rma" class="form-control" required>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
            <label for="fecha" class="form-label required">Fecha:</label>
            <input type="date" autocomplete="off" name="fecha" id="fecha" class="form-control" required>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 mt-4 pt-2">
            <div id="bloqueo-btnInit" style="display:none">
                <button class="btn btn-success btn-sm" type="button" disabled="">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                </button>

            </div>
            <div id="desbloqueo-btnInit">

                <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-arrow-circle-right"></i></button>
            </div>
        </div>
    </div>
</form>
<!--- REGISTRO DE PRODUCTO-->
<div id="carga-detalladodevol" hidden>
    <form id="formAddProgDevolucion">
        <div class="row mb-1 mt-1 card-header">
            <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                <label for="programa" class="form-label required">Programa:</label>
                <?php
                $Data = $obj_devolucion->getProgramasVentas($data);
                $Data = Excepciones::validaConsulta($Data);
                ?>
                <select name="programa" id="programa" style="width:100%" onchange=" calculoDeCantidad()" class="form-control select2">
                    <option value="">Selecciona Programa</option>
                    <?php
                    foreach ($Data as $key => $value) {
                        $f_cantidad= round($Data[$key]['cantidad'],2);
                        echo "<option data-cantidad='{$f_cantidad}' value='{$Data[$key]['id']}'>{$Data[$key]['prg_nombre']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                <label for="cant" class="form-label required">Cantidad:</label>
                <input type="number" step='0.01' min='0.01' name="cant" id="cant" class="form-control" required>
            </div>

            <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 mt-4 pt-2">
                <div id="bloqueo-btnDet" style="display:none">
                    <button class="btn btn-success btn-sm" type="button" disabled="">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    </button>

                </div>
                <div id="desbloqueo-btnDet">
                    <button class="btn btn-success btn-sm"><i class="fas fa-check"></i></button>
                </div>
            </div>
        </div>
    </form>
    <!--- LISTADO DE PRODUCTO-->
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="carga-listadevol">
        </div>
    </div>
</div>
<script src="../assets/scripts/clearData.js"></script>
<script>
    cargaListaDevol();
    // CARGA LISTA DE DEVOLUCION         
    function cargaListaDevol() {
        cargaContenido("carga-listadevol", "../templates/Ventas/cargaListaDevoluciones.php", '1')

    }
    // ENVIA FORMULARIO DE INICIO DE DEVOLUCION
    $("#formInitDevolucion").submit(function(e) {
        e.preventDefault();
        formData = $(this).serialize();
        $.ajax({
            url: '../Controller/devolucion.php?op=initdevolucion',
            data: formData,
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                    setTimeout(() => {
                        bloqueoBtn("bloqueo-btnInit", 2)
                        $("#carga-detalladodevol").prop("hidden", false);
                    }, 1000);


                } else if (resp[0] == 0) {
                    notificaBad(resp[1])
                    bloqueoBtn("bloqueo-btnInit", 2)


                }
            },
            beforeSend: function() {
                bloqueoBtn("bloqueo-btnInit", 1)
            }

        });
    });

    // ENVIA FORMULARIO DE DETALLADO DE DEVOLUCION
    $("#formAddProgDevolucion").submit(function(e) {
        e.preventDefault();
        formData = $(this).serialize();
        $.ajax({
            url: '../Controller/devolucion.php?op=detdevolucion',
            data: formData,
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                    setTimeout(() => {
                        bloqueoBtn("bloqueo-btnDet", 2)
                        clearForm("formAddProgDevolucion")
                        cargaListaDevol()

                    }, 1000);


                } else if (resp[0] == 0) {
                    notificaBad(resp[1])
                    bloqueoBtn("bloqueo-btnDet", 2)


                }
            },
            beforeSend: function() {
                bloqueoBtn("bloqueo-btnDet", 1)
            }

        });
    });
    // CALCULO DE PZAS PARA CALCULO
    function calculoDeCantidad() {
       cantidadPrograma= $("#programa option:selected").data("cantidad")
       $("#cant").val(cantidadPrograma)
       $("#cant").attr("max", cantidadPrograma)
    }
</script>