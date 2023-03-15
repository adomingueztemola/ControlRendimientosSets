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
$obj_devolucion = new Devolucion($debug, $idUser);
$Data = $obj_devolucion->getDetDevolucion();
$Data = Excepciones::validaConsulta($Data);
$Data= $Data==''?array():$Data;
?>

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Programa</th>
                    <th>Cantidad</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if(count($Data)<=0){
                    echo "<tr><td colspan='3' class='text-danger text-center'>Sin Registro Encontrados</td></tr>";
                }
                $count = 0;
                foreach ($Data as $key => $value) {
                    $count++;
                    $f_cantidad = formatoMil($Data[$key]['cantidad']);
                    $btnEliminar = "  <div id='bloqueo-btndet{$Data[$key]['id']}' style='display:none'>
                    <button class='btn btn-xs btn-TWM' type='button' disabled=''>
                        <span class='spinner-border spinner-border-sm' role='status' aria-hidden='true'></span>
                    </button>
                </div>
                <div id='desbloqueo-btndet{$Data[$key]['id']}'>
                    <button class='btn btn-xs btn-danger' title='Eliminar Artículo' onclick='eliminarDetDevolucion({$Data[$key]['id']})'>
                                            <i class='fas fa-trash-alt'></i></button>";
                    echo "<tr>
                        <td>{$btnEliminar}</td>
                        <td>{$Data[$key]['n_programa']}</td>
                        <td>{$f_cantidad}</td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
<script>
    function eliminarDetDevolucion(id) {
        $.ajax({
            url: '../Controller/devolucion.php?op=eliminardetdevolucion',
            data: {
                id: id
            },
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                    setTimeout(() => {
                        bloqueoBtn("bloqueo-btndet" + id, 2)
                        cargaListaDevol()
                    }, 1000);


                } else if (resp[0] == 0) {
                    notificaBad(resp[1])
                    bloqueoBtn("bloqueo-btndet" + id, 2)


                }
            },
            beforeSend: function() {
                bloqueoBtn("bloqueo-btndet" + id, 1)
            }

        });

    }
</script>