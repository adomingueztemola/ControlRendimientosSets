<?php
require_once 'seg.php';
$info = new Seguridad();
require_once('../include/connect_mvc.php');
include("../Models/Mdl_ConexionBD.php");
include("../Models/Mdl_Proceso.php");
include("../Models/Mdl_Programa.php");
include("../Models/Mdl_MateriaPrima.php");
include("../Models/Mdl_Rendimiento.php");
include("../Models/Mdl_Proveedor.php");
include("../Models/Mdl_TipoVenta.php");


$info->Acceso();
$idUser = $_SESSION['CREident'];
$nameUser = $_SESSION['CREnombreUser'];
setlocale(LC_TIME, 'es_ES.UTF-8');
$debug = 0;
$space = 1;
$obj_proceso = new ProcesoSecado($debug, $idUser);
$obj_programa = new Programa($debug, $idUser);
$obj_materia = new MateriaPrima($debug, $idUser);
$obj_rendimiento = new Rendimiento($debug, $idUser);
$obj_proveedor = new Proveedor($debug, $idUser);
$obj_tipo = new TipoVenta($debug, $idUser);


$data = (!empty($_GET['data']) and $_GET['data'] != '') ? trim($_GET['data']) : '';
$_configura = false;
if ($data == '') {
    // Carga de Rendimientos Sin Cerrar
    $DataRendimientoAbierto = $obj_rendimiento->getRendimientoAbierto("2");
    $DataRendimientoAbierto = $DataRendimientoAbierto == '' ? array() : $DataRendimientoAbierto;
    $_abierto = count($DataRendimientoAbierto) > 0 ? true : false;
    $disabledXEdicion = "";
} else {
    $DataRendimientoAbierto = $obj_rendimiento->getDetRendimientosEtiquetas($data);
    $DataRendimientoAbierto = $DataRendimientoAbierto == '' ? array() : $DataRendimientoAbierto;
    $_abierto = count($DataRendimientoAbierto) > 0 ? true : false;
    $_configura = count($DataRendimientoAbierto) > 0 ? true : false;
    $disabledXEdicion = "hidden";
}

//PARAMETROS PARA CARGA DEL FORMULARIO
$idCatProveedor = $_abierto ? $DataRendimientoAbierto[0]['idCatProveedor'] : '';
$semanaProduccion = $_abierto ? date("Y", strtotime($DataRendimientoAbierto[0]['fechaEngrase'])) . "-W" . $DataRendimientoAbierto[0]['semanaProduccion'] : '';
$fechaFinal = $_abierto ? $DataRendimientoAbierto[0]['fechaFinal'] : '';
$loteTemola = $_abierto ? $DataRendimientoAbierto[0]['loteTemola'] : '';
$idCatPrograma = $_abierto ? $DataRendimientoAbierto[0]['idCatPrograma'] : '';
$idCatMateriaPrima = $_abierto ? $DataRendimientoAbierto[0]['idCatMateriaPrima'] : '';
$_1s = $_abierto ? $DataRendimientoAbierto[0]['1s'] : '0';
$_2s = $_abierto ? $DataRendimientoAbierto[0]['2s'] : '0';
$_3s = $_abierto ? $DataRendimientoAbierto[0]['3s'] : '0';
$_4s = $_abierto ? $DataRendimientoAbierto[0]['4s'] : '0';
$total_s = $_abierto ? $DataRendimientoAbierto[0]['total_s'] : '0';
$idTipoVenta = $_abierto ? $DataRendimientoAbierto[0]['idTipoVenta'] : '0';


?>
<!DOCTYPE html>
<html dir="ltr" lang="<?= $info->lng; ?>">

<?php include("../templates/header.php"); ?>

<body>
    <div class="preloader">
        <div class="lds-ripple">
            <div class="lds-pos"></div>
            <div class="lds-pos"></div>
        </div>
    </div>
    <div id="main-wrapper">
        <?= $info->creaHeaderConMenu(); ?>
        <div class="page-wrapper">
            <div class="container-fluid">
                <?php include("../templates/namePage.php"); ?>
                <div class="row">
                    <div class="col-md-2 mb-2">
                        <?php if ($data != '' and $data != '0') { ?>
                            <a href="historialetiquetas.php" class="btn waves-effect waves-light btn-TWM"><i class="fas fa-arrow-left"></i></a>
                        <?php } ?>
                    </div>
                    <div class="col-md-8">
                    </div>
                    <div class="col-md-2 text-right mb-2">
                        <?php if ($data == '' or $data== '0') { ?>

                            <a class="button btn btn-outline-TWM btn-sm" href="historialetiquetas.php">Ir a Historial</a>
                        <?php } ?>

                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-lg-6">
                        <div class="card border">
                            <div class="card-body" id="">
                                <form id="formAddRendimiento">
                                    <div class="row">
                                        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                            <label class="form-label required" for="fechaFinal">Fecha Final:</label>
                                            <input class="form-control" type="date" name="fechaFinal" onchange="setSemanaInput('fechaFinal','semanaProduccion')" id="fechaFinal" value="<?= $fechaFinal ?>" required></input>

                                        </div>

                                        <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                                            <label class="form-label required" for="semanaProduccion">Semana de Producción:</label>
                                            <input class="form-control" type="week" value="<?= $semanaProduccion ?>" name="semanaProduccion" id="semanaProduccion" required></input>
                                        </div>

                                    </div>


                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <label class="form-label required" for="lote">Lote de Temola:</label>
                                            <span id="bloqueo-btn-res" style="display:none">
                                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>

                                            </span>
                                            <span id="desbloqueo-btn-res">
                                                <span id="resultbusq"></span>
                                            </span>

                                            <input class="form-control" type="text" name="lote" value="<?= $loteTemola ?>" id="lote" required></input>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <label class="form-label required" for="programa">Programa:</label>
                                            <select class="form-control select2" name="programa" style="width:100%" id="programa" required>
                                                <option value="">Selecciona Programa</option>
                                                <?php
                                                $DataPrograma = $obj_programa->getPrograma("p.estado='1'", "p.tipo='2'");
                                                foreach ($DataPrograma as $key => $value) {
                                                    $selected = $idCatPrograma == $DataPrograma[$key]['id'] ? 'selected' : '';
                                                    echo "<option $selected value='{$DataPrograma[$key]['id']}'>{$DataPrograma[$key]['nombre']}</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <label class="form-label required" for="materiaPrima">Materia Prima:</label>
                                            <select class="form-control select2" name="materiaPrima" style="width:100%" id="materiaPrima" required>
                                                <option value="">Selecciona Materia Prima</option>
                                                <?php
                                                $DataMateriaPrima = $obj_materia->getMaterias("mt.estado='1'");
                                                foreach ($DataMateriaPrima as $key => $value) {
                                                    $selected = $idCatMateriaPrima == $DataMateriaPrima[$key]['id'] ? 'selected' : '';

                                                    echo "<option $selected value='{$DataMateriaPrima[$key]['id']}'>{$DataMateriaPrima[$key]['nombre']}</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <label class="form-label required" for="proveedor">Proveedor:</label>
                                            <select class="form-control select2" name="proveedor" style="width:100%" id="proveedor" required>
                                                <option value="">Selecciona Proveedor</option>
                                                <?php
                                                $DataProveedor = $obj_proveedor->getProveedores("p.estado='1'");
                                                foreach ($DataProveedor as $key => $value) {
                                                    $selected = $idCatProveedor == $DataProveedor[$key]['id'] ? 'selected' : '';

                                                    echo "<option $selected value='{$DataProveedor[$key]['id']}'>{$DataProveedor[$key]['nombre']}</option>";
                                                }
                                                ?>
                                            </select>

                                        </div>
                                    </div>
                                    <div class="row mt-1">
                                        <?php
                                        $selectedCalzado = $idTipoVenta == '1' ? 'checked' : '';
                                        $selectedEtiquetas = $idTipoVenta == '2' ? 'checked' : '';
                                        ?>
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <label class="form-label required" for="venta">Venta:</label>
                                            <div class="form-check form-check-inline">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" <?= $selectedCalzado ?> class="custom-control-input" id="calzado" value="1" name="venta">
                                                    <label class="custom-control-label" for="calzado">Calzado</label>
                                                </div>
                                            </div>
                                            <div class="form-check form-check-inline">

                                                <div class="custom-control custom-radio">
                                                    <input type="radio" <?= $selectedEtiquetas ?> class="custom-control-input" id="etiqueta" value="2" name="venta">
                                                    <label class="custom-control-label" for="etiqueta">Etiquetas</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-12">
                                            <table class="table table-sm">
                                                <thead class="bg-TWM text-white">
                                                    <tr>
                                                        <th>1s</th>
                                                        <th>2s</th>
                                                        <th>3s</th>
                                                        <th>4s</th>
                                                        <th>Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <input type="number" name="1s" id="1s" class="form-control sumatoria_s" value="<?= $_1s ?>" step="0.01" min="0">
                                                        </td>
                                                        <td>
                                                            <input type="number" name="2s" id="2s" class="form-control sumatoria_s" value="<?= $_2s ?>" step="0.01" min="0">
                                                        </td>
                                                        <td>
                                                            <input type="number" name="3s" id="3s" class="form-control sumatoria_s" value="<?= $_3s ?>" step="0.01" min="0">
                                                        </td>
                                                        <td>
                                                            <input type="number" name="4s" id="4s" class="form-control sumatoria_s" value="<?= $_4s ?>" step="0.01" min="0">
                                                        </td>
                                                        <td>
                                                            <input type="number" name="total_s" id="Total" class="form-control" value="<?= $total_s ?>" step="0.01" min="0">
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row" id="btns-init">
                                        <div class="col-md-8"></div>
                                        <div class="col-md-4 text-rigth">
                                            <div id="bloqueo-btn-1" style="display:none">
                                                <button class="btn btn-TWM" type="button" disabled="">
                                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                    Espere...
                                                </button>

                                            </div>
                                            <div id="desbloqueo-btn-1">
                                                <button type="button" onclick="clearForm('formAddRendimiento')" class="button btn btn-danger">Limpiar</button>
                                                <button type="submit" class="button btn btn-success">Guardar</button>
                                            </div>
                                        </div>

                                    </div>



                                </form>

                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-6">
                        <div class="card border">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="content-Yield">

                                    </div>
                                </div>

                                <hr>
                                <div class="row" id="btns-finalizar" hidden>


                                    <div class="col-md-6"></div>
                                    <div class="col-md-6 text-right">
                                        <div id="bloqueo-btn-2" style="display:none">
                                            <button class="btn btn-TWM" type="button" disabled="">
                                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                Espere...
                                            </button>

                                        </div>
                                        <div id="desbloqueo-btn-2">
                                            <button type="button" <?= $disabledXEdicion ?> onclick="eliminarPreRegistro()" class="button btn btn-danger">Cancelar Pre-Registro</button>
                                            <button type="button" onclick="cierrePreRegistro(<?= $data ?>)" class="button btn btn-success">Finalizar</button>
                                        </div>
                                    </div>

                                </div>

                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
</body>


<?= $info->creaFooter(); ?>
<?php include("../templates/libsJS.php"); ?>
<script src="../assets/scripts/calculaSemanaProduccion.js"></script>
<script src="../assets/scripts/validaLoteEtiq.js"></script>

<script>
    <?php
    if (isset($_SESSION['CRESuccessRendimiento']) and $_SESSION['CRESuccessRendimiento'] != '') { ?>
        notificaSuc('<?= $_SESSION['CRESuccessRendimiento'] ?>');
    <?php
        unset($_SESSION['CRESuccessRendimiento']);
    }
    if (isset($_SESSION['CREErrorRendimientoEtq']) and $_SESSION['CREErrorRendimientoEtq'] != '') { ?>
        notificaBad('<?= $_SESSION['CREErrorRendimientoEtq'] ?>');
    <?php
        unset($_SESSION['CREErrorRendimientoEtq']);
    }
    if ($_abierto) { ?>
        $('#formAddRendimiento').find('input, textarea, button, select').attr('disabled', 'disabled');
        $("#btns-init").attr('hidden', true);
        $("#btns-finalizar").attr('hidden', false);
        $("#div-observaciones").attr('hidden', false);


    <?php } ?>

    <?php
    if (!$_configura) {
        echo "update();";
    } else {
        echo "update({$data});";
    }
    ?>

    function update(data = "") {
        if (data == '') {
            $('#content-Yield').html('<div class="loading text-center"><img src="../assets/images/loading.gif" alt="loading" /><br/>Un momento, por favor...</div>');
            $('#content-Yield').load('../templates/Rendimiento/cargaTablaEtiquetas.php');
        } else {
            $('#content-Yield').html('<div class="loading text-center"><img src="../assets/images/loading.gif" alt="loading" /><br/>Un momento, por favor...</div>');
            $('#content-Yield').load('../templates/Rendimiento/cargaTablaEtiquetas.php?data=' + data);
        }


    }


    /******************* SUMATORIA DE TOTALES 'S'*******************/
    $(".sumatoria_s").change(function() {
        let result = 0;
        $(".sumatoria_s").each(function() {
            result = parseFloat(result) + parseFloat($(this).val());
        });
        $("#Total").val(result);
    });

    /********** INICIO DE RENDIMIENTO ***********/
    $("#formAddRendimiento").submit(function(e) {
        e.preventDefault();
        formData = $(this).serialize();
        $.ajax({
            url: '../Controller/rendimiento.php?op=initrendimientoetiquetas',
            data: formData,
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                    setTimeout(() => {
                        bloqueoBtn("bloqueo-btn-1", 2)
                        update()
                        $('#formAddRendimiento').find('input, textarea, button, select').attr('disabled', 'disabled');

                        $("#btns-init").attr('hidden', true);
                        $("#btns-finalizar").attr('hidden', false);
                        //   $("#div-observaciones").attr('hidden', false);

                    }, 1000);


                } else if (resp[0] == 0) {
                    notificaBad(resp[1])
                    bloqueoBtn("bloqueo-btn-1", 2)


                }
            },
            beforeSend: function() {
                bloqueoBtn("bloqueo-btn-1", 1)
            }

        });
    });
    /********** ELIMINAR PRE REGISTRO  ***********/
    function eliminarPreRegistro() {
        $.ajax({
            url: '../Controller/rendimientoEtiquetas.php?op=eliminarrendimiento',
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    // notificaSuc(resp[1])
                    setTimeout(() => {
                        bloqueoBtn("bloqueo-btn-2", 2)
                        location.reload()
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
    }
    /********** CIERRE PRE REGISTRO  ***********/
    function cierrePreRegistro(edicion = '0') {
        log_result = validaCamposLlenos()
        if (log_result) {
            $.ajax({
                url: '../Controller/rendimientoEtiquetas.php?op=cierrerendimiento',
                data: {
                    edicion: edicion
                },
                type: 'POST',
                success: function(json) {
                    resp = json.split('|')
                    console.log(edicion);
                    if (resp[0] == 1) {
                        setTimeout(() => {
                            bloqueoBtn("bloqueo-btn-2", 2)

                            if (edicion == '0') {
                                location.reload()

                            } else {
                                window.location.href = "historialetiquetas.php";

                            }
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
        }
        /***************************GUARDAR OBSERVACIONES *******************************/
        function guardarObservaciones(input) {
            v_observaciones = $(input).val()
            $.ajax({
                url: '../Controller/rendimiento.php?op=guardarobservaciones',
                type: 'POST',
                data: {

                },
                success: function(json) {
                    resp = json.split('|')
                    if (resp[0] == 1) {
                        notificaSuc(resp[1])
                        setTimeout(() => {
                            bloqueoBtn("bloqueo-btn-2", 2)
                            location.reload()
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
        }

        /*  $.ajax({
              url: '../Controller/rendimiento.php?op=cierrerendimiento',
              type: 'POST',
              success: function(json) {
                  resp = json.split('|')
                  console.log(resp);
                  if (resp[0] == 1) {
                      notificaSuc(resp[1])
                      setTimeout(() => {
                          bloqueoBtn("bloqueo-btn-2", 2)
                          location.reload()
                      }, 1000);


                  } else if (resp[0] == 0) {
                      notificaBad(resp[1])
                      bloqueoBtn("bloqueo-btn-2", 2)


                  }
              },
              beforeSend: function() {
                  bloqueoBtn("bloqueo-btn-2", 1)
              }

          });*/
    }
</script>

</html>