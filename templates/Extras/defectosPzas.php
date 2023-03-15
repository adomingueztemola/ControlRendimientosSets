<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../../include/connect_mvc.php";
$debug = 0;
$idUser = $_SESSION['CREident'];
$obj_defectopz = new DefectosPzas($debug, $idUser);
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}
$id=isset($_POST['id'])?$_POST['id']:'';
$id=$id=='-1'?'':$id;
$filtradoEstatus= $id!=''?'p.estado='.$id.'':'1=1';
$DataDefectospzs = $obj_defectopz->getDefectospzs($filtradoEstatus);

?>
<table id="table-defectospzs" class="table table-sm">
    <thead>
        <tr class="bg-TWM text-white">
            <th>#</th>
            <th>Defecto</th>
            <th>Usuario Registro</th>
            <th>Fecha Registro</th>
            <th>Estado</th>

        </tr>
    </thead>
    <tbody>
        <?php
        $count=0;
        foreach ($DataDefectospzs as $key => $value) {
            $count++;
            
            $btnAccion=$DataDefectospzs[$key]['estado']=='1'?
<<<EOD
            <button type="button" title='Activar' onclick="cambiaEstatus({$DataDefectospzs[$key]['id']},{$DataDefectospzs[$key]['estado']})" 
            class="btn btn-xs btn-outline-success"><i class=' fas fa-power-off'></i></button>
EOD
            :
<<<EOD
            <button type="button" title='Desactivar' onclick="cambiaEstatus({$DataDefectospzs[$key]['id']},{$DataDefectospzs[$key]['estado']})" 
            class="btn btn-xs btn-outline-danger"><i class=' fas fa-power-off'></i></button>
EOD;
        ?>
            <tr>
                <td><?=$count?></td>
                <td><?=$DataDefectospzs[$key]['nombre']?></td>
                <td><?=$DataDefectospzs[$key]['str_usuario']?></td>
                <td><?=$DataDefectospzs[$key]['f_fechaReg']?></td>
                <td><div id="divEstatus-<?=$DataDefectospzs[$key]['id']?>"><?=$btnAccion?></div></td>


            </tr>
        <?php


        }
        ?>

    </tbody>
</table>
<script>
    $("#table-defectospzs").DataTable();
</script>
