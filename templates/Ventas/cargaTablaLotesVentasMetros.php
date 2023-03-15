<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../../include/connect_mvc.php";
include('../../Models/Mdl_ConexionBD.php');
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
$id = (!empty($_GET['id'])  and $_GET['id'] != '') ? $_GET['id'] : '0';
if ($id == '0') {
    echo "<div class='alert alert-primary' role='alert'>
                No se encontró la Venta Iniciada, vuelve a intentarlo, si el problema persiste notifica al departamento de Sistemas.
           </div>";
    exit(0);
}
$obj_venta = new Venta($debug, $idUser);
?>
<table class="mt-4 table table-sm">
    <thead>
        <tr>
            <th>#</th>
            <th>Lote Temola</th>
            <th>Metros a Vender</th>
            <th>Metros en Almacén PT</th>
            <th>Acción</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $DataVenta = $obj_venta->getDetalladoVenta();
        $count = 0;
        $suma_unidades = 0;
        $suma_sets = 0;
        foreach ($DataVenta as $key => $value) {
            $count++;
            $suma_unidades += $DataVenta[$key]['unidades'];
            $btnAccion = "<button class='btn button btn-danger btn-xs' onclick='eliminarDetVenta({$DataVenta[$key]['id']})'><i class='fas fa-trash-alt'></i></button>";
            $colorDist = ($DataVenta[$key]['distribuido'] == "" or $DataVenta[$key]['distribuido'] == "0") ? "table-danger" : "table-success";

            $colorBtnDist = ($DataVenta[$key]['distribuido'] == "" or $DataVenta[$key]['distribuido'] == "0") ? "btn-dark" : "btn-success";

            $btnClasif =  "<button title='Clasificación de Cueros' class='btn button  $colorBtnDist btn-xs' onclick=\"clasifLoteVta({$DataVenta[$key]['id']},'{$DataVenta[$key]['loteTemola']}')\" data-toggle='modal' data-target='#clasificaModal'><i class='fas fa-chart-pie'></i></button>";
            $btnLote =  "<button title='Clasificación de Lotes' class='btn button btn-info btn-xs' onclick=\"seleccionarLote({$DataVenta[$key]['idRendimiento']},'{$DataVenta[$key]['loteTemola']}', '{$DataVenta[$key]['id']}')\" data-toggle='modal' data-target='#seleccionarModal'><i class='fas fa-boxes'></i></button>";

            $unidades = formatoMil($DataVenta[$key]['unidades']);
            $sets = formatoMil($DataVenta[$key]['totalSets']);
            $almacen = $DataVenta[$key]['almacenPT'] == '' ? '0' : formatoMil($DataVenta[$key]['almacenPT'], 2);
            echo "<tr class=' $colorDist'>
            <td>{$count}</td>
            <td>{$DataVenta[$key]['loteTemola']}</td>
            <td>
                <input class='form-control' onchange='cambiarMetros(this,{$DataVenta[$key]['idRendimiento']},{$DataVenta[$key]['id']})' value='{$DataVenta[$key]['unidades']}'>
            </td>
            <td>{$almacen}</td>   
            <td>{$btnAccion} {$btnClasif} </td>
            </tr>";
        }
        ?>
    </tbody>

    <tfoot>
        <tr class="bg-TWM text-white">
            <td colspan="2"></td>
            <td colspan="1" class="text-center">Total de Metros: <?= formatoMil($suma_unidades) ?></td>

            <td colspan="2"></td>


        </tr>
    </tfoot>
</table>
<!-- Modal -->
<div class="modal fade" id="clasificaModal" tabindex="-1" data-backdrop="static" data-keyboard="false" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-TWM text-white">
                <h5 class="modal-title" id="modalTiteClasifica"></h5>
                <button type="button" class="close" data-dismiss="modal" onclick="cierreClasifica()" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="contentClasifica">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" onclick="cierreClasifica()" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="seleccionarModal" tabindex="-1" data-backdrop="static" data-keyboard="false" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-TWM text-white">
                <h5 class="modal-title" id="modalTiletSeleccion"></h5>
                <button type="button" class="close" data-dismiss="modal" onclick="cierreClasifica()" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formAgregarSubLote">
                <div class="modal-body" id="contentSeleccion">

                </div>
                <div class="modal-footer">
                    <div id="bloqueo-btn-3" style="display:none">
                        <button class="btn btn-TWM" type="button" disabled="">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Espere...
                        </button>

                    </div>
                    <div id="desbloqueo-btn-3">
                        <button type="button" class="btn btn-light" onclick="cierreClasifica()" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-success">Guardar Loteo</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function cierreClasifica() {
        setTimeout(() => {
            updateTable();


        }, 500);
    }
    /********************** CAMBIAR METROS DE LOTE VENDIDOS ***************************/
    function cambiarMetros(input, idLote, idDetVenta){
        $.ajax({
            url: '../Controller/ventas.php?op=actualizarmetros',
            data: {
                idDetVenta: idDetVenta,
                idLote:idLote,
                value:$(input).val()
            },
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                    updateTable()
                    updateRegistro()
                } else if (resp[0] == 0) {
                    notificaBad(resp[1])
                }
            },
            beforeSend: function() {}
        });

    }
    /********************** Eliminar Detallado de Venta ***************************/
    function eliminarDetVenta(id) {
        $.ajax({
            url: '../Controller/ventas.php?op=eliminardetventa',
            data: {
                id: id
            },
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                    updateTable()
                    updateRegistro()
                } else if (resp[0] == 0) {
                    notificaBad(resp[1])
                }
            },
            beforeSend: function() {}
        });
    }

    /************************* Carga datos de la Clasificacion del Lote al Vender************************/
    function clasifLoteVta(id, lote) {
        $("#contentClasifica").load("../templates/Ventas/cargaClasifVenta.php?id=" + id);
        $("#modalTiteClasifica").text("Lote: " + lote);
    }

    /************************* Carga datos de la Seleccion del SubLote************************/
    function seleccionarLote(id, lote, idDetVenta) {
        $("#contentSeleccion").load("../templates/Ventas/cargaSeleccionVentaMetros.php?id=" + id + "&idDetVenta=" + idDetVenta);
        $("#modalTiletSeleccion").text("Lote: " + lote);
    }

    /************************* Agregar Subloteo a la venta  ************************/
    $("#formAgregarSubLote").submit(function(e) {
        e.preventDefault();
        formData = $(this).serialize();
        $.ajax({
            url: '../Controller/ventas.php?op=agregarsublotes',
            data: formData,
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                    $("#seleccionarModal").modal("hide")
                    setTimeout(() => {
                        bloqueoBtn("bloqueo-btn-3", 2)
                        updateTable();
                    }, 1000);

                } else if (resp[0] == 0) {
                    notificaBad(resp[1])
                    bloqueoBtn("bloqueo-btn-3", 2)


                }
            },
            beforeSend: function() {
                bloqueoBtn("bloqueo-btn-3", 1)
            }

        });
    });
</script>