<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../../include/connect_mvc.php";
$debug = 0;
$idUser = $_SESSION['CREident'];
$obj_proc = new ProcesoProduccion($debug, $idUser);
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}
$id=isset($_POST['id'])?$_POST['id']:'';
$id=$id=='-1'?'':$id;
$filtradoEstatus= $id!=''?'p.estado='.$id.'':'1=1';
$DataProcesos = $obj_proc->getProcesos($filtradoEstatus);

?>
<table id="table-procesos" class="table table-sm">
    <thead>
        <tr class="bg-TWM text-white">
            <th>#</th>
            <th>Proceso</th>
            <th>Usuario Registro</th>
            <th>Fecha Registro</th>
            <th>Estado</th>

        </tr>
    </thead>
    <tbody>
        <?php
        $count=0;
        foreach ($DataProcesos as $key => $value) {
            $count++;
            
            $btnAccion=$DataProcesos[$key]['estado']=='1'?
<<<EOD
            <button type="button" title='Activar' onclick="cambiaEstatus({$DataProcesos[$key]['id']},{$DataProcesos[$key]['estado']})" 
            class="btn btn-xs btn-outline-success"><i class=' fas fa-power-off'></i></button>
EOD
            :
<<<EOD
            <button type="button" title='Desactivar' onclick="cambiaEstatus({$DataProcesos[$key]['id']},{$DataProcesos[$key]['estado']})" 
            class="btn btn-xs btn-outline-danger"><i class=' fas fa-power-off'></i></button>
EOD;
        ?>
            <tr>
                <td><?=$count?></td>
                <td><?=$DataProcesos[$key]['nombre']?></td>
                <td><?=$DataProcesos[$key]['str_usuario']?></td>
                <td><?=$DataProcesos[$key]['f_fechaReg']?></td>
                <td><div id="divEstatus-<?=$DataProcesos[$key]['id']?>"><?=$btnAccion?></div></td>


            </tr>
        <?php


        }
        ?>

    </tbody>
</table>
<script>
    $("#table-procesos").DataTable();
</script>
