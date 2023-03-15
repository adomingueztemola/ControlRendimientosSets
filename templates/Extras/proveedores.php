<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../../include/connect_mvc.php";
include('../../Models/Mdl_ConexionBD.php');
include('../../Models/Mdl_Proveedor.php');
$debug = 0;
$idUser = $_SESSION['CREident'];
$obj_prov = new Proveedor($debug, $idUser);
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}
$id=isset($_POST['id'])?$_POST['id']:'';
$id=$id=='-1'?'':$id;
$filtradoEstatus= $id!=''?'p.estado='.$id.'':'1=1';
$DataProveedores = $obj_prov->getProveedores($filtradoEstatus);

?>
<table id="table-proveedores" class="table table-sm">
    <thead>
        <tr class="bg-TWM text-white">
            <th>#</th>
            <th>Proveedor</th>
            <th>Usuario Registro</th>
            <th>Fecha Registro</th>
            <th>Estado</th>

        </tr>
    </thead>
    <tbody>
        <?php
        $count=0;
        foreach ($DataProveedores as $key => $value) {
            $count++;
            
            $btnAccion=$DataProveedores[$key]['estado']=='1'?
<<<EOD
            <button type="button" title='Activar' onclick="cambiaEstatus({$DataProveedores[$key]['id']},{$DataProveedores[$key]['estado']})" 
            class="btn btn-xs btn-outline-success"><i class=' fas fa-power-off'></i></button>
EOD
            :
<<<EOD
            <button type="button" title='Desactivar' onclick="cambiaEstatus({$DataProveedores[$key]['id']},{$DataProveedores[$key]['estado']})" 
            class="btn btn-xs btn-outline-danger"><i class=' fas fa-power-off'></i></button>
EOD;
        ?>
            <tr>
                <td><?=$count?></td>
                <td><?=$DataProveedores[$key]['nombre']?></td>
                <td><?=$DataProveedores[$key]['str_usuario']?></td>
                <td><?=$DataProveedores[$key]['f_fechaReg']?></td>
                <td><div id="divEstatus-<?=$DataProveedores[$key]['id']?>"><?=$btnAccion?></div></td>


            </tr>
        <?php


        }
        ?>

    </tbody>
</table>
<script>
    $("#table-proveedores").DataTable();
</script>
