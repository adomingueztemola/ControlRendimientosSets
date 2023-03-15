<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../../include/connect_mvc.php";
$debug = 0;
$idUser = $_SESSION['CREident'];
$obj_defec = new DefectosMP($debug, $idUser);
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}
$id=isset($_POST['id'])?$_POST['id']:'';
$id=$id=='-1'?'':$id;
$filtradoEstatus= $id!=''?'p.estado='.$id.'':'1=1';
$DataDefectos = $obj_defec->getDefectos($filtradoEstatus);

?>
<table id="table-defectos" class="table table-sm">
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
        foreach ($DataDefectos as $key => $value) {
            $count++;
            
            $btnAccion=$DataDefectos[$key]['estado']=='1'?
<<<EOD
            <button type="button" title='Activar' onclick="cambiaEstatus({$DataDefectos[$key]['id']},{$DataDefectos[$key]['estado']})" 
            class="btn btn-xs btn-outline-success"><i class=' fas fa-power-off'></i></button>
EOD
            :
<<<EOD
            <button type="button" title='Desactivar' onclick="cambiaEstatus({$DataDefectos[$key]['id']},{$DataDefectos[$key]['estado']})" 
            class="btn btn-xs btn-outline-danger"><i class=' fas fa-power-off'></i></button>
EOD;
        ?>
            <tr>
                <td><?=$count?></td>
                <td><?=$DataDefectos[$key]['nombre']?></td>
                <td><?=$DataDefectos[$key]['str_usuario']?></td>
                <td><?=$DataDefectos[$key]['f_fechaReg']?></td>
                <td><div id="divEstatus-<?=$DataDefectos[$key]['id']?>"><?=$btnAccion?></div></td>


            </tr>
        <?php


        }
        ?>

    </tbody>
</table>
<script>
    $("#table-defectos").DataTable();
</script>
