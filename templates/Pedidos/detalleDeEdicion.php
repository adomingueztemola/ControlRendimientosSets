<?php
session_start();
$debug = 0;
define('INCLUDE_CHECK', 1);
require_once "../../include/connect_mvc.php";
include('../../assets/scripts/cadenas.php');

$idUser = $_SESSION['CREident'];
$obj_pedidos = new Pedido($debug, $idUser);
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}

$id = !empty($_POST['ident']) ? $_POST['ident'] : '';
if ($id == '') {
    echo "<div class='alert alert-warning' role='alert'>
      No se encontró el pedido, vuelve a intentarlo, si el problema persiste consulta al departamento de sistemas.
    </div>";
}
$DataDetPedido = $obj_pedidos->getEdicionesPedidos($id);

?>
<table class="table table-sm" id="table-edicion<?= $id ?>">
    <thead>
        <tr class="bg-TWM text-white">
            <th>#</th>
            <th>Fecha</th>
            <th>Tipo</th>
            <th>Motivo</th>
            <th>Cueros</th>
            <th>Nota de Crédito</th>
            <th>Empleado Responsable</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $count = 1;
        foreach ($DataDetPedido as $key => $value) {
            $lblTipo = $DataDetPedido[$key]['tipo'] == '1' ? "Aumento" : "Disminución";
            $f_cueros = formatoMil($DataDetPedido[$key]['totalCueros'], 2);
            $numNotaCredito = ($DataDetPedido[$key]['numNotaCredito'] == "0" or $DataDetPedido[$key]['numNotaCredito'] == "0") ? '<i>N/A</i>' : $DataDetPedido[$key]['numNotaCredito'];
            $colorTipo = $DataDetPedido[$key]['tipo'] == '1' ? "text-success" : "text-danger";
            echo "<tr>
                        <td>{$count}</td>
                        <td>{$DataDetPedido[$key]['f_fechaReg']}</td>
                        <td class='{$colorTipo}'>{$lblTipo}</td>
                        <td>{$DataDetPedido[$key]['descripcion']}</td>
                        <td>{$f_cueros}</td>
                        <td>{$numNotaCredito}</td>
                        <td>{$DataDetPedido[$key]['n_empleado']}</td>
                    </tr>";
            $count++;
        }


        ?>


    </tbody>
</table>
<script>
    $("#table-edicion<?= $id ?>").DataTable({});
</script>