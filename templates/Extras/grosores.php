<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../../include/connect_mvc.php";
$debug = 0;
$idUser = $_SESSION['CREident'];
$obj_grosor = new Grosor($debug, $idUser);
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}
$id=isset($_POST['id'])?$_POST['id']:'';
$id=$id=='-1'?'':$id;
$filtradoEstatus= $id!=''?'cg.estado='.$id.'':'1=1';
$DataGrosor = $obj_grosor->getGrosor($filtradoEstatus);
?>
<table id="table-grosores" class="table table-sm">
    <thead>
        <tr class="bg-TWM text-white">
            <th>#</th>
            <th>Grosores</th>
            <th>Usuario Registro</th>
            <th>Fecha Registro</th>
            <th>Estado</th>

        </tr>
    </thead>
    <tbody>
        <?php
        $count=0;
        foreach ($DataGrosor as $key => $value) {
            $count++;
            
            $btnAccion=$DataGrosor[$key]['estado']=='1'?
<<<EOD
            <button type="button" title='Activar' onclick="cambiaEstatus({$DataGrosor[$key]['id']},{$DataGrosor[$key]['estado']})" 
            class="btn btn-xs btn-outline-success"><i class=' fas fa-power-off'></i></button>
EOD
            :
<<<EOD
            <button type="button" title='Desactivar' onclick="cambiaEstatus({$DataGrosor[$key]['id']},{$DataGrosor[$key]['estado']})" 
            class="btn btn-xs btn-outline-danger"><i class=' fas fa-power-off'></i></button>
EOD;
        ?>
            <tr>
                <td><?=$count?></td>
                <td><?=$DataGrosor[$key]['nombre']?></td>
                <td><?=$DataGrosor[$key]['str_usuario']?></td>
                <td><?=$DataGrosor[$key]['f_fechaReg']?></td>
                <td><div id="divEstatus-<?=$DataGrosor[$key]['id']?>"><?=$btnAccion?></div></td>


            </tr>
        <?php


        }
        ?>

    </tbody>
</table>
<script>
    $("#table-grosores").DataTable();
</script>
