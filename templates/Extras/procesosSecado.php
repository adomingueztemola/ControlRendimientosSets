<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../../include/connect_mvc.php";
include('../../Models/Mdl_ConexionBD.php');
include('../../Models/Mdl_Proceso.php');
$debug = 0;
$idUser = $_SESSION['CREident'];
$obj_proceso = new ProcesoSecado($debug, $idUser);
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}
$id = isset($_POST['id']) ? $_POST['id'] : '';
$id = $id == '-1' ? '' : $id;
$filtradoEstatus = $id != '' ? 'pr.estado=' . $id . '' : '1=1';
$DataProceso = $obj_proceso->getProcesos($filtradoEstatus);

?>
<table id="table-materias" class="table table-sm">
    <thead>
        <tr class="bg-TWM text-white">
            <th>Código</th>
            <th>Proceso</th>
            <th>Tipo</th>

            <th>Usuario Registro</th>
            <th>Fecha Registro</th>
            <th>Estado</th>
            <th>Acción</th>


        </tr>
    </thead>
    <tbody>
        <?php
        $count = 0;
        foreach ($DataProceso as $key => $value) {
            $count++;
            $lbl_Tipo="";
            /***** CASTEO DE TIPOS ******/
            switch ($DataProceso[$key]['tipo']) {
                case '1':
                    # Set's
                    $lbl_Tipo="Set's";
                    break;

                case '2':
                    # Metros
                    $lbl_Tipo="m<sup>2</sup>";

                    break;

            }
            $btnEstatus = $DataProceso[$key]['estado'] == '1' ?
<<<EOD
            <button type="button" title='Activar' onclick="cambiaEstatus({$DataProceso[$key]['id']},{$DataProceso[$key]['estado']})" 
            class="btn btn-xs btn-outline-success"><i class=' fas fa-power-off'></i></button>
EOD
                : 
<<<EOD
            <button type="button" title='Desactivar' onclick="cambiaEstatus({$DataProceso[$key]['id']},{$DataProceso[$key]['estado']})" 
            class="btn btn-xs btn-outline-danger"><i class=' fas fa-power-off'></i></button>
EOD;
$btnAccion="<button class='btn btn-xs btn-primary' onclick='cargarEdicion({$DataProceso[$key]['id']})' data-toggle='modal' data-target='#ModalEditProceso'><i class=' fas fa-cogs' title='Edición'></i></button>";

        ?>
            <tr>
                <td><?= $DataProceso[$key]['codigo'] ?></td>
                <td><?= $DataProceso[$key]['nombre'] ?></td>
                <td><?= $lbl_Tipo ?></td>

                <td><?= $DataProceso[$key]['str_usuario'] ?></td>
                <td><?= $DataProceso[$key]['f_fechaReg'] ?></td>
                <td>
                    <div id="divEstatus-<?= $DataProceso[$key]['id'] ?>"><?= $btnEstatus ?></div>
                </td>
                <td>
                    <?=$btnAccion?>
                </td>


            </tr>
        <?php


        }
        ?>

    </tbody>
</table>
<script>
    $("#table-materias").DataTable();
</script>