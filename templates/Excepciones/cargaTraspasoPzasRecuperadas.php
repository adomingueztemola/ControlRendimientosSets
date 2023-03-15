<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once('../../include/connect_mvc.php');
include("../../Models/Mdl_ConexionBD.php");
include("../../Models/Mdl_Rendimiento.php");
include("../../Models/Mdl_Traspaso.php");
include('../../assets/scripts/cadenas.php');
$debug = 0;
$idUser = $_SESSION['CREident'];
$nameUser = $_SESSION['CREnombreUser'];
$id = !empty($_GET['id']) ? $_GET['id'] : '';

//PARA SU FUNCIONAMIENTO DEBE DE TENER UNA VARIABLE $id; ID DEL LOTE
$obj_rendimiento = new Rendimiento($debug, $idUser);
$obj_traspaso = new Traspaso($debug, $idUser);
$DataLotes = $obj_rendimiento->getSuperLotes($id);
?>
<form id="formAgregarTraspaso">
    <div class="row card-header">
        <div class="col-md-5">
            <input type="hidden" name="loteSalida" value="<?= $id ?>">
            <select name="loteEntrada" id="loteEntrada" class="form-control select2" style="width:100%">
                <option value="">Lotes Disponibles</option>
                <?php
                foreach ($DataLotes as $key => $value) {
                    echo "<option value='{$DataLotes[$key]['id']}'>{$DataLotes[$key]['loteTemola']}</option>";
                }

                ?>
            </select>
        </div>
        <div class="col-md-4">
            <input type="number" tyle="width: 228px; height: 150px" name="cantTraspaso" id="cantTraspaso" class="form-control"></input>
        </div>
        <div class="col-md-3">
            <div id="bloqueo-btn-2" style="display:none">
                <button class="btn btn-TWM" type="button" disabled="">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                </button>

            </div>
            <div id="desbloqueo-btn-2">
                <button type="submit" class="btn btn-success btn-md"><i class="fas fa-check"></i></button>
            </div>
        </div>

    </div>
</form>
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <table class="table table-sm">
            <thead>
                <th>#</th>
                <th>Lote Temola</th>
                <th>Cantidad de Traspaso</th>
                <th>Acción</th>

            </thead>
            <tbody>
                <?php
                $DataTraspasos = $obj_traspaso->getTraspasosLote($id);
                $count = 0;
                if(count($DataTraspasos)>0){
                foreach ($DataTraspasos as $key => $value) {
                    $count++;
                    $f_cantidad = formatoMil($DataTraspasos[$key]['cantidad'], 0);
                    echo "<tr>
                            <td>{$count}</td>
                            <td>{$DataTraspasos[$key]['loteEntrada']}</td>
                            <td>{$f_cantidad}</td>
                            <td>
                            <div id='bloqueo-btn-2-{$DataTraspasos[$key]['id']}' style='display:none'>
                                <button class='btn btn-danger btn-xs' type='button' disabled=''>
                                    <span class='spinner-border spinner-border-sm' role='status' aria-hidden='true'></span>
                                </button>
                            </div>
                            <div id='desbloqueo-btn-2-{$DataTraspasos[$key]['id']}'>
                                <button type='button' class='btn btn-xs btn-danger' title='Eliminar Traspaso' onclick='eliminarTraspaso({$DataTraspasos[$key]['id']})'>
                                    <i class='fas fa-trash-alt'></i>
                                </button>
                            </div>
                            </td>
                        </tr>";
                }}else{
                    echo "<tr><td  colspan='4' class='text-muted text-center'>No hay Traspaso Registrados</td></tr>";
                }

                ?>

            </tbody>

        </table>
    </div>
</div>
<script src="../assets/scripts/clearData.js"></script>
<script>
    /**************************************** AGREGAR REGISTRO DE TRASPASO ****************************************************/
    $("#formAgregarTraspaso").submit(function(e) {
        e.preventDefault();
        formData = $(this).serialize();
        $.ajax({
            url: '../Controller/traspasos.php?op=agregartraspaso',
            data: formData,
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                    bloqueoBtn("bloqueo-btn-2", 2)
                    setTimeout(() => {
                        updateTraspasos()
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
    /**************************************** ELIMINAR REGISTRO DE TRASPASO ****************************************************/
    function eliminarTraspaso(id) {
        $.ajax({
            url: '../Controller/traspasos.php?op=eliminartraspaso',
            data: {
                id: id
            },
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                    setTimeout(() => {
                        updateTraspasos()
                        bloqueoBtn("bloqueo-btn-2-"+id, 2)

                    }, 1000);


                } else if (resp[0] == 0) {
                    notificaBad(resp[1])
                    bloqueoBtn("bloqueo-btn-2-"+id, 2)


                }
            },
            beforeSend: function() {
                bloqueoBtn("bloqueo-btn-2-"+id, 1)
            }

        });
    }
</script>