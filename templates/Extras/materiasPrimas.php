<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../../include/connect_mvc.php";
include('../../Models/Mdl_ConexionBD.php');
include('../../Models/Mdl_MateriaPrima.php');
$debug = 0;
$idUser = $_SESSION['CREident'];
$obj_materias = new MateriaPrima($debug, $idUser);
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}
$id = isset($_POST['id']) ? $_POST['id'] : '';
$id = $id == '-1' ? '' : $id;
$filtradoEstatus = $id != '' ? 'mt.estado=' . $id . '' : '1=1';
$DataMaterias = $obj_materias->getMaterias($filtradoEstatus);

?>
<table id="table-materias" class="table table-sm">
    <thead>
        <tr class="bg-TWM text-white">
            <th>#</th>
            <th>Materia Prima</th>
            <th>Tipo</th>
            <th>Moneda</th>

            <th>Usuario Registro</th>
            <th>Fecha Registro</th>
            <th>Estado</th>
            <th>Acción</th>


        </tr>
    </thead>
    <tbody>
        <?php
        $count = 0;
        foreach ($DataMaterias as $key => $value) {
            $count++;
            $lbl_Tipo = "S/R";
            switch ($DataMaterias[$key]["tipo"]) {
                case '1':
                    # Carnaza...
                    $lbl_Tipo = "Carnaza";
                    break;

                case '2':
                    # Piel...
                    $lbl_Tipo = "Piel";
                    break;
            }
            //casteo de datos de moneda}
            $lbl_Mnd="";
            switch ($DataMaterias[$key]["mnd"]) {
                case '1':
                    # MXN
                    $lbl_Mnd="MXN";
                    break;

                case '2':
                    # USD
                    $lbl_Mnd="USD";

                    break;
            }
            $btnEstatus = $DataMaterias[$key]['estado'] == '1' ?
                <<<EOD
            <button type="button" title='Activar' onclick="cambiaEstatus({$DataMaterias[$key]['id']},{$DataMaterias[$key]['estado']})" 
            class="btn btn-xs btn-outline-success"><i class=' fas fa-power-off'></i></button>
EOD
                :
                <<<EOD
            <button type="button" title='Desactivar' onclick="cambiaEstatus({$DataMaterias[$key]['id']},{$DataMaterias[$key]['estado']})" 
            class="btn btn-xs btn-outline-danger"><i class=' fas fa-power-off'></i></button>
EOD;
            $btnAccion = "<button class='btn btn-xs btn-primary' onclick='cargarEdicion({$DataMaterias[$key]['id']})' data-toggle='modal' data-target='#ModalEditMateria'><i class=' fas fa-cogs' title='Edición'></i></button>";

        ?>
            <tr>
                <td><?= $count ?></td>
                <td><?= $DataMaterias[$key]['nombre'] ?></td>
                <td><?= $lbl_Tipo ?></td>
                <td><?= $lbl_Mnd ?></td>

                <td><?= $DataMaterias[$key]['str_usuario'] ?></td>
                <td><?= $DataMaterias[$key]['f_fechaReg'] ?></td>
                <td>
                    <div id="divEstatus-<?= $DataMaterias[$key]['id'] ?>"><?= $btnEstatus ?></div>
                </td>
                <td><?= $btnAccion ?></td>


            </tr>
        <?php


        }
        ?>

    </tbody>
</table>
<script>
    $("#table-materias").DataTable();
</script>