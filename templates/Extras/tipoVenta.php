<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../../include/connect_mvc.php";
include('../../Models/Mdl_ConexionBD.php');
include('../../Models/Mdl_TipoVenta.php');
$debug = 0;
$idUser = $_SESSION['CREident'];
$obj_tipo = new TipoVenta($debug, $idUser);
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}
$id = isset($_POST['id']) ? $_POST['id'] : '';
$id = $id == '-1' ? '' : $id;
$filtradoEstatus = $id != '' ? 'tv.estado=' . $id . '' : '1=1';
$DataTipos = $obj_tipo->getTipos($filtradoEstatus);

?>
<table id="table-tipo" class="table table-sm">
    <thead>
        <tr class="bg-TWM text-white">
            <th>#</th>
            <th>Tipo de Venta</th>
            <th>Para Vender</th>

            <th>Factura</th>

            <th>Usuario Registro</th>
            <th>Fecha Registro</th>
            <th>Estado</th>
            <th>Acción</th>

        </tr>
    </thead>
    <tbody>
        <?php
        $count = 0;
        foreach ($DataTipos as $key => $value) {
            $count++;

            $btnEstatus = $DataTipos[$key]['estado'] == '1' ?
                <<<EOD
            <button type="button" title='Activar' onclick="cambiaEstatus({$DataTipos[$key]['id']},{$DataTipos[$key]['estado']})" 
            class="btn btn-xs btn-outline-success"><i class=' fas fa-power-off'></i></button>
EOD
                :
                <<<EOD
            <button type="button" title='Desactivar' onclick="cambiaEstatus({$DataTipos[$key]['id']},{$DataTipos[$key]['estado']})" 
            class="btn btn-xs btn-outline-danger"><i class=' fas fa-power-off'></i></button>
EOD;
            $btnAccion = "<button class='btn btn-xs btn-primary' onclick='cargarEdicion({$DataTipos[$key]['id']})' data-toggle='modal' data-target='#ModalEditVenta'><i class=' fas fa-cogs' title='Edición'></i></button>";

            $lbl_factura = "S/R";
            switch ($DataTipos[$key]['tipo']) {
                case '1':
                    # con factura
                    $lbl_factura = "Requiere Num. Factura.";
                    break;

                case '2':
                    # sin factura
                    $lbl_factura = "Num. Factura no obligatorio.";
                    break;
            }

            $lbl_cargaVenta = "S/R";
            switch ($DataTipos[$key]['cargaVenta']) {
                case '1':
                    # Piel
                    $lbl_cargaVenta = "Set's";
                    break;

                case '2':
                    # Metros
                    $lbl_cargaVenta = "M<sup>2</sup>";
                    break;
                case '3':
                    # Etiquetas
                    $lbl_cargaVenta = "Etiquetas/Calzado";
                    break;
            }
        ?>
            <tr>
                <td><?= $count ?></td>
                <td><?= $DataTipos[$key]['nombre'] ?></td>
                <td><?= $lbl_cargaVenta ?></td>
                <td><?= $lbl_factura ?></td>

                <td><?= $DataTipos[$key]['str_usuario'] ?></td>
                <td><?= $DataTipos[$key]['f_fechaReg'] ?></td>
                <td>
                    <div id="divEstatus-<?= $DataTipos[$key]['id'] ?>"><?= $btnEstatus ?></div>
                </td>
                <td><?= $btnAccion ?></td>


            </tr>
        <?php


        }
        ?>

    </tbody>
</table>
<script>
    $("#table-tipo").DataTable();
</script>