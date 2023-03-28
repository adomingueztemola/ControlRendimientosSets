<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../../include/connect_mvc.php";
include('../../Models/Mdl_ConexionBD.php');
include('../../Models/Mdl_Programa.php');
include('../../assets/scripts/cadenas.php');

$debug = 0;
$idUser = $_SESSION['CREident'];
$obj_programas = new Programa($debug, $idUser);
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}
$id=isset($_POST['id'])?$_POST['id']:'';
$id=$id=='-1'?'':$id;
$filtradoEstatus= $id!=''?'p.estado='.$id.'':'1=1';
$filtradoTipo="(p.tipo='1' or p.tipo='3')";

$DataProgramas = $obj_programas->getPrograma($filtradoEstatus, $filtradoTipo);

?>
<table id="table-programas" class="table table-sm">
    <thead>
        <tr class="bg-TWM text-white">
            <th>#</th>
            <th>Tipo</th>
            <th>Programa</th>
            <th>Área Neta <small>sf/set</small></th>
            <th>Usuario Registro</th>
            <th>Fecha Registro</th>
            <th>Estado</th>


        </tr>
    </thead>
    <tbody>
        <?php
        $count=0;
        foreach ($DataProgramas as $key => $value) {
            $count++;
            
            $btnEstatus=$DataProgramas[$key]['estado']=='1'?
<<<EOD
            <button type="button" title='Activar' onclick="cambiaEstatus({$DataProgramas[$key]['id']},{$DataProgramas[$key]['estado']})" 
            class="btn btn-xs btn-outline-success"><i class=' fas fa-power-off'></i></button>
EOD
            :
<<<EOD
            <button type="button" title='Desactivar' onclick="cambiaEstatus({$DataProgramas[$key]['id']},{$DataProgramas[$key]['estado']})" 
            class="btn btn-xs btn-outline-danger"><i class=' fas fa-power-off'></i></button>
EOD;
$lblTipo="";
$lblTipo=$DataProgramas[$key]['tipo']=='1'?"Set's" : $lblTipo;
$lblTipo=$DataProgramas[$key]['tipo']=='3'?"M<sup>2</sup>" : $lblTipo;

        ?>
            <tr>
                <td><?=$count?></td>
                <td><?=$lblTipo?></td>
                <td><?=$DataProgramas[$key]['nombre']?></td>
                <td><?=formatoMil($DataProgramas[$key]['areaNeta'],5)?></td>
                <td><?=$DataProgramas[$key]['str_usuario']?></td>
                <td><?=$DataProgramas[$key]['f_fechaReg']?></td>
                <td><div id="divEstatus-<?=$DataProgramas[$key]['id']?>"><?=$btnEstatus?></div></td>

            </tr>
        <?php


        }
        ?>

    </tbody>
</table>

<script>
    $("#table-programas").DataTable();
</script>
