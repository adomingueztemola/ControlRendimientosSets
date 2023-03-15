<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../../include/connect_mvc.php";
include('../../Models/Mdl_ConexionBD.php');
include('../../Models/Mdl_MarcadoAMano.php');
include('../../Models/Mdl_PzasVolante.php');
include('../../Models/Mdl_Excepciones.php');

include('../../assets/scripts/cadenas.php');
$debug = 0;
$idUser = $_SESSION['CREident'];
if ($debug == 1) {
    print_r($_GET);
    //  exit(0);
} else {
    error_reporting(0);
}
$id = isset($_GET['id']) ? $_GET['id'] : '';
if ($id == '') {
    echo '<div class="alert alert-danger" role="alert">
            ¡Atención! No se encontró el lote, intentalo de nuevo, si el problema persiste consulta al departamento de sistemas.
           </div>';
    exit(0);
}
$obj_marcado = new MarcadoAMano($debug, $idUser);
$obj_volante = new PzasVolante($debug, $idUser);
$DataLote = $obj_marcado->getDetMarcadoXLote($id);

?>
<style>
    .sinbordefondo {
        background-color: #e5e7b1;
        border: 0;
    }

    .bgverde {
        background-color: #33FF80;
        border: 0;
    }
</style>
<form id="formAddPorcDecrem">
    <div class="row">
        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5">
            <label for="porcDecrement">Porcentaje de Decremento</label>
            <input type="hidden" name="idLote" value="<?= $id ?>">
            <div class="input-group mb-3">
                <input type="number" step="0.01" min="0" max="100" name="porcDecrement" value="<?= $DataLote[0]["porcDecremento"] ?>" id="porcDecrement" autocomplete="off" class="form-control" required>
                <div class="input-group-append">
                    <span class="input-group-text">%</span>
                </div>
                <div class="input-group-append">
                    <div id="bloqueo-btn-D" style="display:none">
                        <button class="btn btn-TWM" type="button" disabled="">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Espere...
                        </button>

                    </div>
                    <div id="desbloqueo-btn-D">
                        <button class="btn btn-success" type="submit"><i class="fas fa-check"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<div class="row">
    <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
        <div class="table-responsive">
            <table id="demo-foo-addrow" class="table table-bordered table-hover contact-list table-sm">
                <thead>
                    <tr class="footable-header">
                        <th class="">Fecha de Corte: <?= date("d/m/Y") ?></th>
                        <th colspan="2">Programa: <?= $DataLote[0]["n_programa"] ?> </th>
                        <th>Lote: <?= $DataLote[0]["n_lote"] ?></th>

                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <?php
                        /************************************************************************/
                        /* Datos de las piezas del volante*/
                        /************************************************************************/
                        $DataVolante = $obj_volante->getPzasVolante("apv.idCatPrograma='{$DataLote[0]['idCatPrograma']}'");
                        $conteoTotalPzas = 0;
                        foreach ($DataVolante as $key => $value) {
                            echo "
                        <td>
                            <p><b>{$DataVolante[$key]['nPzaVolante']}</b>
                           Área: {$DataVolante[$key]['area']} ft<sup>2</sup></p>
                        </td>
                   ";
                            $conteoTotalPzas++;
                        }
                        ?>
                    </tr>
                    <tr>
                        <td colspan="<?= $conteoTotalPzas ?>" class="text-center bg-TWM text-white">Conteo Parcial de Piezas</td>
                    </tr>
                    <tr>
                        <?php
                        /************************************************************************/
                        /* Línea de Cantidad final a llegar */
                        /************************************************************************/
                        $DataVolante = $obj_marcado->getMetricasConteoTeseo($id);
                        foreach ($DataVolante as $key => $value) {
                            $idContador = $DataVolante[$key]["id"];
                            echo "<td><input class='form-control sinbordefondo'  value='{$DataVolante[$key]["total"]}' onkeypress='pulsar(event, \"cantfinal\", \"{$idContador}\", \"{$id}\", this)' type='number' step='1' min='1'></input></td>";
                        }
                        ?>
                    </tr>
                    <tr>
                        <td colspan="<?= $conteoTotalPzas ?>" class="text-center bg-TWM text-white">Total de Marcado Manual Registrado</td>
                    </tr>
                    <tr class="bg-success">
                        <?php
                        /************************************************************************/
                        /* Línea de total de Marcado Registrado */
                        /************************************************************************/
                        $DataVolante = $obj_marcado->getMetricasConteoTeseo($id);
                        foreach ($DataVolante as $key => $value) {
                            $idContador = $DataVolante[$key]["id"];
                            echo "<td><input class='form-control'   value='{$DataVolante[$key]["preliminar"]}' disabled type='number' step='1' min='1'></input></td>";
                        }
                        ?>
                    </tr>
                    <tr>
                        <td colspan="<?= $conteoTotalPzas ?>" class="text-center bg-TWM text-white">Registro de Corte de Piezas Marcadas</td>
                    </tr>
                    <tr>
                        <td colspan="3">Historial de Registro de Corte de Piezas </td>
                        <td>
                            <span class="btn button btn-sm" data-toggle="collapse" href=".histregistro">
                                <i class="fas fa-chevron-circle-down"></i>
                            </span>
                        </td>
                    </tr>
                    <tr class="histregistro collapse">

                        <td>#</td>
                        <td>Pza. de Volante</td>
                        <td>Cantidad</td>
                        <td>Fecha de Registro</td>
                    </tr>

                    <?php
                    $DataVolante = $obj_marcado->getKardexLote($id);
                    $count = 1;
                    if (count($DataVolante) > 0) {
                        foreach ($DataVolante as $key => $value) {
                            echo " <tr class='histregistro collapse'>
                                    <td>{$count}</td>
                                    <td><b>{$DataVolante[$key]['n_volante']}</b></td>
                                    <td>{$DataVolante[$key]['cantidad']}</td>
                                    <td>{$DataVolante[$key]['f_fechaReg']}</td>
                                    </tr>
                                  ";
                            $count++;
                        }
                    } else {
                        echo " <tr  class='histregistro collapse'>
                        <td colspan='4' class='text-center text-danger'>No hay registro de marcado en el Lote.</td>
                        </tr>";
                    }

                    ?>


                    </tr>
                    <?php
                    /************************************************************************/
                    /* Línea de registro de corte */
                    /************************************************************************/
                    $DataVolante = $obj_marcado->getMetricasConteoTeseo($id);
                    foreach ($DataVolante as $key => $value) {
                        $idContador = $DataVolante[$key]["id"];
                        echo "<td><input class='input form-control' data-limit='{$DataVolante[$key]["total"]}' data-lote='{$id}' data-preliminar='{$DataVolante[$key]["preliminar"]}' style='border: 1px solid #82898c;' onkeypress='pulsar(event, \"corte\", \"{$idContador}\", \"{$id}\", this)' type='number' step='1' min='1'></input></td>";
                    }

                    ?>
                    </tr>
                    <td colspan="<?= $conteoTotalPzas ?>" class="text-center">Decremento de Piezas por Error</td>
                    <tr>
                        <td colspan="3">Historial de Registro de Decremento de Pzas
                        </td>
                        <td>
                            <span class="btn button btn-sm" data-toggle="collapse" href=".histregistrodecr">
                                <i class="fas fa-chevron-circle-down"></i>
                            </span>
                        </td>
                    </tr>
                    <tr class="histregistrodecr collapse">

                        <td>#</td>
                        <td>Pza. de Volante</td>
                        <td>Cantidad</td>
                        <td>Fecha de Registro</td>
                    </tr>

                    <?php
                    $DataVolante = $obj_marcado->getDecrementoLote($id);
                    $count = 1;
                    if (count($DataVolante) > 0) {
                        foreach ($DataVolante as $key => $value) {
                            echo " <tr class='histregistrodecr collapse'>
                                        <td>{$count}</td>
                                        <td><b>{$DataVolante[$key]['n_volante']}</b></td>
                                        <td>{$DataVolante[$key]['cantidad']}</td>
                                        <td>{$DataVolante[$key]['f_fechaReg']}</td>
                                        </tr>
                                      ";
                            $count++;
                        }
                    } else {
                        echo " <tr class='histregistrodecr collapse'>
                                        <td colspan='4' class='text-center text-danger'>No hay registro de decremento en el Lote.</td>
                                </tr>";
                    }


                    ?>


                    </tr>
                    <tr class="table-danger">
                        <?php
                        /************************************************************************/
                        /* Línea de decremento de corte */
                        /************************************************************************/
                        $DataVolante = $obj_marcado->getMetricasConteoTeseo($id);
                        foreach ($DataVolante as $key => $value) {
                            $idContador = $DataVolante[$key]["id"];
                            echo "<td><input class='input form-control' data-limit='{$DataVolante[$key]["total"]}' data-lote='{$id}' data-preliminar='{$DataVolante[$key]["preliminar"]}' style='border: 1px solid #82898c;' onkeypress='pulsar(event, \"decremento\", \"{$idContador}\", \"{$id}\", this)' type='number' step='1' min='1'></input></td>";
                        }

                        ?>
                    </tr>
                    <td colspan="<?= $conteoTotalPzas ?>" class="text-center">Ajuste de Piezas por Recuperación</td>
                    <tr class="">
                        <?php
                        /************************************************************************/
                        /* Línea de ajuste de piezas en el corte */
                        /************************************************************************/
                        $DataVolante = $obj_marcado->getMetricasConteoTeseo($id);
                        foreach ($DataVolante as $key => $value) {
                            $idContador = $DataVolante[$key]["id"];
                            echo "<td><input class='input form-control' data-limit='{$DataVolante[$key]["total"]}' data-lote='{$id}' data-preliminar='{$DataVolante[$key]["preliminar"]}' style='border: 1px solid #82898c;' onkeypress='pulsar(event, \"ajuste\", \"{$idContador}\", \"{$id}\", this)' type='number' step='1' min='1'></input></td>";
                        }

                        ?>
                    </tr>
            </table>
        </div>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
        <table id="demo-foo-addrow" class="table table-bordered table-hover contact-list table-sm">
            <thead>
                <tr>
                    <td>Datos Generales</td>
                </tr>
            </thead>
            <tbody>

                <?php
                /************************************************************************/
                /* Línea de registro de corte */
                /************************************************************************/
                $DataVolante = $obj_marcado->getMetricasConteoTeseo($id);
                foreach ($DataVolante as $key => $value) {
                    echo "  <tr><td><b>{$DataVolante[$key]["nombre"]}:</b> {$DataVolante[$key]["preliminar"]}</td></tr>";
                }
                $DataLote = $obj_marcado->getDetMarcadoXLote($id);
                $DataLote = Excepciones::validaConsulta($DataLote);
                $f_porcentaje= formatoMil($DataLote[0]["porcDecremento"],2);
                ?>
                <tr>
                    <td>Piezas Totales: <?= formatoMil($DataLote[0]['pzasTotales'], 0) ?></td>
                </tr>
                <tr>
                    <td>Yield: <?= formatoMil($DataLote[0]['yield'], 2) ?>%</td>
                </tr>
                <tr>
                    <td>Área Crust: <?= formatoMil($DataLote[0]['areaCrust'], 2) ?> ft<sup>2</sup></td>
                </tr>
                <tr>
                    <td>Área Medida con Decremento <?=$f_porcentaje?>%: <?= formatoMil($DataLote[0]['areaCrustDecremento'], 2) ?> ft<sup>2</sup></td>
                </tr>
                <tr>
                    <td>Área: <?= formatoMil($DataLote[0]['area'], 2) ?> ft<sup>2</sup></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<div class="row mb-2">
    <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
        <form id="formAddCrust">
            <input type="hidden" name="idLote" value="<?= $id ?>">
            <label for="areaCrust" class="form-label required">Área Crust</label>
            <div class="input-group mb-3">
                <input type="number" step="0.01" min="" name="areaCrust" value="<?= $DataLote[0]["areaCrust"] ?>" id="areaCrust" autocomplete="off" class="form-control" required>
                <div class="input-group-append">
                    <div id="bloqueo-btn-C" style="display:none">
                        <button class="btn btn-TWM" type="button" disabled="">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Espere...
                        </button>

                    </div>
                    <div id="desbloqueo-btn-C">
                        <button class="btn btn-success" type="submit"><i class="fas fa-check"></i></button>
                    </div>
                </div>
            </div>
        </form>

    </div>
</div>
<div class="row">
    <div class="col-md-10 col-lg-10 col-xs-10 col-sm-10"></div>
    <div class="col-md-2 col-lg-2 col-xs-2 col-sm-2">
        <div id="bloqueo-btn-M" style="display:none">
            <button class="btn btn-TWM" type="button" disabled="">
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Espere...
            </button>

        </div>
        <div id="desbloqueo-btn-M">
            <button class="button btn btn-md btn-success" onclick="cerrarMarcado(<?= $id ?>)">Cerrar Marcado</button>
        </div>
    </div>

</div>
<script src="../assets/scripts/functionStorageMarcadoManual.js"></script>
<script>
    function abrirNuevoTab(url) {
        // Abrir nuevo tab
        var win = window.open(url, '_blank');
        // Cambiar el foco al nuevo tab (punto opcional)
        win.focus();
    }
    /********** AGREGAR LOTE DE MARCADO A MANO ***********/
    $("#formAddCrust").submit(function(e) {
        e.preventDefault();
        formData = $(this).serialize();
        $.ajax({
            url: '../Controller/marcadoMano.php?op=agregarcrust',
            data: formData,
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                    setTimeout(() => {
                        bloqueoBtn("bloqueo-btn-C", 2)
                        cargaContent(<?= $id ?>);
                    }, 1000);

                } else if (resp[0] == 0) {
                    bloqueoBtn("bloqueo-btn-C", 2)

                    notificaBad(resp[1])


                }
            },
            beforeSend: function() {
                bloqueoBtn("bloqueo-btn-C", 1)

            }

        });
    });

    /********** AGREGAR LOTE DE MARCADO A MANO ***********/
    $("#formAddPorcDecrem").submit(function(e) {
        e.preventDefault();
        formData = $(this).serialize();
        $.ajax({
            url: '../Controller/marcadoMano.php?op=actualizarporcentaje',
            data: formData,
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                    setTimeout(() => {
                        bloqueoBtn("bloqueo-btn-D", 2)
                        cargaContent(<?= $id ?>);
                    }, 1000);

                } else if (resp[0] == 0) {
                    bloqueoBtn("bloqueo-btn-D", 2)

                    notificaBad(resp[1])


                }
            },
            beforeSend: function() {
                bloqueoBtn("bloqueo-btn-D", 1)

            }

        });
    });

    function cerrarMarcado(idLote) {
        $.ajax({
            url: "../Controller/marcadoMano.php?op=cerrarmarcado",
            data: {
                idLote: idLote
            },
            type: "POST",
            success: function(json) {
                resp = json.split("|");
                if (resp[0] == 1) {
                    notificaSuc(resp[1]);
                    setTimeout(() => {
                        bloqueoBtn("bloqueo-btn-M", 2)
                        abrirNuevoTab("../PDFReportes/Controller/tickets.php?op=getmarcado&data=" + idLote)
                        update()
                    }, 1000);
                } else if (resp[0] == 0) {
                    bloqueoBtn("bloqueo-btn-M", 2)

                    notificaBad(resp[1]);
                }
            },
            beforeSend: function() {
                bloqueoBtn("bloqueo-btn-M", 1)

            },
        });

    }
</script>