<?php
require_once 'seg.php';
$info = new Seguridad();
require_once('../include/connect_mvc.php');
include("../Models/Mdl_ConexionBD.php");
include("../Models/Mdl_VentaXDevoluc.php");

include("../Models/Mdl_TipoVenta.php");
$info->Acceso();
$idUser = $_SESSION['CREident'];
$nameUser = $_SESSION['CREnombreUser'];
setlocale(LC_TIME, 'es_ES.UTF-8');
$debug = 0;
$space = 1;

$obj_tipoventa = new TipoVenta($debug, $idUser);
$obj_venta = new VentaXDevoluc($debug, $idUser);
$DataAbierto = $obj_venta->getVentaAbierta();
$DataAbierto = $DataAbierto == '' ? array() : $DataAbierto;
$_abierto = count($DataAbierto) > 0 ? true : false;
//PARAMETROS PARA CARGA DEL FORMULARIO
$fechaFact = $_abierto ? $DataAbierto['fechaFact'] : '';
$numFact = $_abierto ? $DataAbierto['numFactura'] : '';
$numPL = $_abierto ? $DataAbierto['numPL'] : '';
$tipoVenta = $_abierto ? $DataAbierto['idTipoVenta'] : '';
$disabled = $_abierto ? 'disabled' : '';
$hidden = $_abierto ? 'hidden' : '';

?>
<!DOCTYPE html>
<html dir="ltr" lang="<?= $info->lng; ?>">

<?php include("../templates/header.php"); ?>
<link href="../assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="../assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">

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
                <div class="row mb-2">
                        <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                            <a type="button" id="back_to_inbox" href="ventasdedevolucion.php" class="btn btn-TWM font-18 m-r-10 mb-2 mt-0 text-right "><i class="text-white mdi mdi-arrow-left"></i></a>
                        </div>
                        <div class="col-lg-10 col-md-10 col-sm-12 col-xs-12"></div>
                       
                    </div>
                <div class="row">
                    <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5">
                        <div class="card border">
                            <form id="formInitVenta">

                                <div class="card-body" id="">
                                    <input type="hidden" name="fact" id="fact">
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                            <label class="form-label required" for="fechaFacturacion">Fecha Facturación:</label>
                                            <input type="date" <?= $disabled ?> required class="form-control" value="<?= $fechaFact ?>" name="fechaFacturacion" id="fechaFacturacion"></input>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                            <label class="form-label required" class="form-label" for="idTipoVenta">Tipo de Venta:</label>
                                            <select required <?= $disabled ?> onchange="requerirFactura()" name="idTipoVenta" id="idTipoVenta" class="form-control select2" style="width:100%">
                                                <option value="">Selecciona Tipo de Venta</option>
                                                <?php
                                                $DataTipo = $obj_tipoventa->getTipos("tv.estado='1'");
                                                foreach ($DataTipo as $key => $value) {
                                                    $selected = $DataTipo[$key]['id'] == $tipoVenta ? 'selected' : '';
                                                    echo "<option data-tipo='{$DataTipo[$key]['tipo']}' data-clasificaventa='{$DataTipo[$key]['cargaVenta']}'  $selected value='{$DataTipo[$key]['id']}'>{$DataTipo[$key]['nombre']}</option>";
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

                                            <input required <?= $disabled ?> type="text" class="form-control" value="<?= $numFact ?>" name="numFactura" id="numFactura"></input>

                                        </div>

                                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                            <label class="form-label required" for="numPL">Núm. de PL:</label>
                                            <span id="bloqueo-btn-res2" style="display:none">
                                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>

                                            </span>
                                            <span id="desbloqueo-btn-res2">
                                                <small id="resultbusq2"></small>
                                            </span>
                                            <input required <?= $disabled ?> type="text" class="form-control" value="<?= $numPL ?>" name="numPL" id="numPL"></input>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                            <div id="bloqueo-btn-1" style="display:none">
                                                <button class="btn btn-TWM" type="button" disabled="">
                                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                    Espere...
                                                </button>

                                            </div>
                                            <div id="desbloqueo-btn-1">
                                                <button type="button" <?= $hidden ?> onclick="clearForm('formInitVenta')" class="button btn btn-danger">Limpiar</button>
                                                <button type="submit" <?= $hidden ?> class="button btn btn-success">Iniciar</button>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </form>

                        </div>
                    </div>

                    <div class="col-lg-7 col-md-7 col-md-7 col-sm-7 col-xs-7">
                        <div class="card border">
                            <div class="card-body" id="carga-detallado">
                                <div class="alert alert-info" role="alert">
                                    <i class="fas fa-info-circle"></i> Registra Datos de la Venta para Iniciar.
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

<script src="../assets/scripts/validaNumVenta.js"></script>
<script src="../assets/scripts/validaNumPL.js"></script>

<script>
    <?php
    if (isset($_SESSION['CRESuccessVenta']) and $_SESSION['CRESuccessVenta'] != '') { ?>
        notificaSuc('<?= $_SESSION['CRESuccessVenta'] ?>')
    <?php
        unset($_SESSION['CRESuccessVenta']);
    }
    if (isset($_SESSION['CREErrorVenta']) and $_SESSION['CREErrorVenta'] != '') { ?>
        notificaBad('<?= $_SESSION['CREErrorVenta'] ?>')
    <?php
        unset($_SESSION['CREErrorVenta']);
    }
    if ($_abierto) {
        echo "updateRegistro()";
    }
    ?>

    function updateRegistro() {
        //Tipo de Venta
        let clasificaVenta = $("#idTipoVenta option:selected").data('clasificaventa');

        $('#carga-detallado').html('<div class="loading text-center"><img src="../assets/images/loading.gif" alt="loading" /><br/>Un momento, por favor...</div>');
        $('#carga-detallado').load('../templates/Ventas/cargaRegistroVentasDevoluc.php');



    }
    /********************* REQUERIR FACTURAR ***************************/
    function requerirFactura(select) {
        data = $("#idTipoVenta option:selected").data("tipo");
        $("#fact").val(data);
        switch (data) {
            case 1:
                $("#div-factura").attr("hidden", false);
                $("#numFactura").prop("required", true);

                break;

            case 2:
                $("#div-factura").attr("hidden", true);
                $("#numFactura").removeAttr("required");


                break;
        }
    }
    /*************** ALMACENAMIENTO DE DATOS GENERALES DE VENTAS*********************/
    $("#formInitVenta").submit(function(e) {
        e.preventDefault();
        formData = $(this).serialize();
        $.ajax({
            url: '../Controller/ventasXDevoluc.php?op=initventa',
            data: formData,
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                    setTimeout(() => {
                        bloqueoBtn("bloqueo-btn-1", 2)
                        $('#formInitVenta').find('input, textarea, button, select').attr('disabled', 'disabled');
                        updateRegistro()
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
</script>

</html>