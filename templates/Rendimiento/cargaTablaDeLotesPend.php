<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once('../../include/connect_mvc.php');
include("../../Models/Mdl_ConexionBD.php");
include("../../Models/Mdl_Rendimiento.php");
include('../../assets/scripts/cadenas.php');
$debug = 0;
$idUser = $_SESSION['CREident'];
$nameUser = $_SESSION['CREnombreUser'];
setlocale(LC_TIME, 'es_ES.UTF-8');
$lote = !empty($_POST['lote']) ? $_POST['lote'] : '';
$filtradoLote= $lote!=''?"LOCATE('$lote', loteTemola) > 0":"1=1";
$obj_rendimiento = new Rendimiento($debug, $idUser);



?>


<table class="table table-sm table-hover">
    <thead>
        <tr>
            <th class="border-top-0">Lote Temola</th>
            <th class="border-top-0">Proceso</th>
            <th class="border-top-0">Materia Prima</th>
            <th class="border-top-0">Ir</th>
        </tr>
    </thead>
    <tbody>
        <?php
$Data_Rendimiento = $obj_rendimiento->getRendimientos("1=1", "1=1", "1=1", "1=1", $filtradoLote);
$Data_Rendimiento = !is_array($Data_Rendimiento) ? array() : $Data_Rendimiento;
        if (count($Data_Rendimiento) > 0) {
            foreach ($Data_Rendimiento as $key => $value) {
                $btn_ir = "<a href='seguimientolotes.php?data={$Data_Rendimiento[$key]['id']}' class='btn button btn-xs btn-primary'><i class='fas fa-external-link-alt'></i></a>";
                echo "
                                           <tr>
                                           <td class=''>{$Data_Rendimiento[$key]['loteTemola']}</td>
                                           <td class=''>{$Data_Rendimiento[$key]['c_proceso']}-{$Data_Rendimiento[$key]['n_proceso']}</td>
                                           <td class=''>{$Data_Rendimiento[$key]['n_materia']}</td>
                                           <td>{$btn_ir}</td>

                                           </tr>
                                           ";
            }
        } else {
            echo '<tr><td class="text-center" colspan="3">No hay Lotes Pendientes</td></tr>';
        }

        ?>


    </tbody>
</table>