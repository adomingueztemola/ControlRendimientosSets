<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../../include/connect_mvc.php";
$debug = 0;
$idUser = $_SESSION['CREident'];
$obj_personal = new Trabajadores($debug, $idUser);
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}
$id = isset($_POST['id']) ? $_POST['id'] : '';
$id = $id == '-1' ? '' : $id;
$filtradoEstatus = $id != '' ? 'mt.estado=' . $id . '' : '1=1';
$DataPersonal = $obj_personal->getTrabajadoresAutorizadosTWM($filtradoEstatus);

?>
<table id="table-materias" class="table table-sm">
    <thead>
        <tr class="bg-TWM text-white">
            <th>#</th>
            <th>No. Trabajador</th>
            <th>Nombre Completo</th>
            <th>Usuario Registro</th>
            <th>Fecha Registro</th>
            <th>Estado</th>


        </tr>
    </thead>
    <tbody>
        <?php
        $count = 0;
        foreach ($DataPersonal as $key => $value) {
            $count++;
            $btnEstatus = $DataPersonal[$key]['estado'] == '1' ?
                <<<EOD
            <button type="button" title='Activar' onclick="cambiaEstatus({$DataPersonal[$key]['id']},{$DataPersonal[$key]['estado']})" 
            class="btn btn-xs btn-outline-success"><i class=' fas fa-power-off'></i></button>
EOD
                :
                <<<EOD
            <button type="button" title='Desactivar' onclick="cambiaEstatus({$DataPersonal[$key]['id']},{$DataPersonal[$key]['estado']})" 
            class="btn btn-xs btn-outline-danger"><i class=' fas fa-power-off'></i></button>
EOD;
            $btnAccion = "<button class='btn btn-xs btn-primary' onclick='cargarEdicion({$DataPersonal[$key]['id']})' data-toggle='modal' data-target='#ModalEditMateria'><i class=' fas fa-cogs' title='Edición'></i></button>";

        ?>
            <tr>
                <td><?= $count ?></td>
                <td><?= $DataPersonal[$key]['noTrabajador'] ?></td>
                <td><?= $DataPersonal[$key]['nombreCompleto'] ?></td>
                <td><?= $DataPersonal[$key]['str_usuario'] ?></td>
                <td><?= $DataPersonal[$key]['f_fechaReg'] ?></td>
                <td>
                    <div id="divEstatus-<?= $DataPersonal[$key]['id'] ?>"><?= $btnEstatus ?></div>
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