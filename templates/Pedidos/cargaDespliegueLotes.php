<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../../include/connect_mvc.php";
include('../../Models/Mdl_ConexionBD.php');
include('../../Models/Mdl_Pedido.php');
include('../../assets/scripts/cadenas.php');

$debug = 0;
$idUser = $_SESSION['CREident'];
$obj_pedidos = new Pedido($debug, $idUser);
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}
$id = !empty($_POST['id']) ? $_POST['id'] : '';




$DataPedido = $obj_pedidos->getLotesXPedido($id);
$DataEtiquetas = $obj_pedidos->getEtiqXPedido($id);
?>
<div class="table-responsive">
    <table class="table table-sm table-bordered">
        <thead>
            <tr>
                <th>Lote Temola</th>
                <th>Proceso</th>
                <th>Materia Prima</th>
                <th>1s</th>
                <th>2s</th>
                <th>3s</th>
                <th>4s</th>
                <th>Total</th>
                <th>Unidade(s) Finales</th>
            </tr>
        </thead>
        <tbody>
            <?php
            echo "<tr><td colspan='13' class='text-center'><b>Lotes de M<sup>2</sup>/Set's</b></td></tr>";

            $DataPedido = !is_array($DataPedido) ? array() : $DataPedido;
            if (count($DataPedido) > 0) {
                foreach ($DataPedido as $key => $value) {
                    $unidades = ($DataPedido[$key]["setsEmpacados"] == '') ? formatoMil($DataPedido[$key]['areaFinal']) :
                        formatoMil($DataPedido[$key]["setsEmpacados"]);
                    $almacenPT = formatoMil($DataPedido[$key]['almacenPT']);
                    $_1s = formatoMil($DataPedido[$key]['1s']);
                    $_2s = formatoMil($DataPedido[$key]['2s']);
                    $_3s = formatoMil($DataPedido[$key]['3s']);
                    $_4s = formatoMil($DataPedido[$key]['4s']);
                    $total_s = formatoMil($DataPedido[$key]['total_s']);


                    echo "
               <tr>
                    <td>{$DataPedido[$key]['loteTemola']}</td>
                    <td>{$DataPedido[$key]['c_proceso']}</td>
                    <td>{$DataPedido[$key]['n_materia']}</td>
                    <td>{$_1s}</td>
                    <td>{$_2s}</td>
                    <td>{$_3s}</td>
                    <td>{$_4s}</td>
                    <td>{$total_s}</td>
                    <td>{$unidades}</td>
               </tr>
               ";
                }
            } else {
                echo "<tr>
                    <td colspan='13' class='text-center'>Sin Lotes Registrados</td>
                </tr>";
            }
            echo "<tr><td colspan='13' class='text-center'><b>Lotes de Etiquetas/Calzado</b></td></tr>";
            $DataEtiquetas = !is_array($DataEtiquetas) ? array() : $DataEtiquetas;
            if (count($DataEtiquetas) > 0) {
                foreach ($DataEtiquetas as $key => $value) {
                    $unidades = formatoMil($DataEtiquetas[$key]['areaFinal']);
                    $almacenPT = formatoMil($DataEtiquetas[$key]['almacenPT']);
                    $_1s = formatoMil($DataEtiquetas[$key]['1s']);
                    $_2s = formatoMil($DataEtiquetas[$key]['2s']);
                    $_3s = formatoMil($DataEtiquetas[$key]['3s']);
                    $_4s = formatoMil($DataEtiquetas[$key]['4s']);
                    $total_s = formatoMil($DataEtiquetas[$key]['total_s']);


                    echo "
           <tr>
                <td>{$DataEtiquetas[$key]['loteTemola']}</td>
                <td>-</td>
                <td>{$DataEtiquetas[$key]['n_materia']}</td>
                <td>{$_1s}</td>
                <td>{$_2s}</td>
                <td>{$_3s}</td>
                <td>{$_4s}</td>
                <td>{$total_s}</td>
                <td>{$unidades}</td>
           </tr>
           ";
                }
            } else {
                echo "<tr>
                <td colspan='13' class='text-center'>Sin Lotes Registrados</td>
            </tr>";
            }
            ?>


        </tbody>
    </table>
</div>