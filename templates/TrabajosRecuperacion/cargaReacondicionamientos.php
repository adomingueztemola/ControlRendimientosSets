<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once('../../include/connect_mvc.php');
include('../../assets/scripts/cadenas.php');

$debug = 0;
$idUser = $_SESSION['CREident'];
$obj_trabajos = new TrabajosRecupera($debug, $idUser);
$date_start = !empty($_POST['date-start']) ? $_POST['date-start'] : date("01/m/Y");
$date_end = !empty($_POST['date-end']) ? $_POST['date-end'] :  date("t/m/Y");
/***************** CASTEO DE FECHAS ****************** */
$date_start = date("Y-m-d", strtotime(str_replace("/", "-", $date_start)));
$date_end = date("Y-m-d", strtotime(str_replace("/", "-", $date_end)));
$filtradoFecha = "mr.fechaEntrega BETWEEN '$date_start' AND '$date_end'";

?>
<div class="table-responsive">
    <table class="table table-sm" id="table-reacond">
        <thead>
            <tr>
                <th>#</th>
                <th>Fecha</th>
                <th>Programa</th>
                <th>Lote</th>
                <th>Total</th>
                <th>Entrega</th>
                <th>Trabajador Recibió</th>
                <th>Reasignacion</th>
                <th>Observaciones</th>
                <th>Fila</th>

            </tr>
        </thead>
        <tbody>
            <?php
            $Data = $obj_trabajos->getRecuperaciones($filtradoFecha, false);
            $Data = Excepciones::validaConsulta($Data);
            $count = 0;
            foreach ($Data as $key => $value) {
                $count++;
                $fTotalInicial = formatoMil($value['totalRecuperacion'], 2);
                echo "<tr>
                    <td>{$count}</td>
                    <td>{$value['f_fecha']}</td>
                    <td>{$value['n_programa']}</td>
                    <td>{$value['nLoteInicial']}</td>
                    <td>{$fTotalInicial}</td>
                    <td>{$value['f_fechaFinal']}</td>
                    <td>{$value['nombreCompletoTrabajador']}</td>
                    <td>{$value['nLoteRecup']}</td>
                    <td>{$value['observaciones']}</td>
                    <td>{$value['rowXLSX']}</td>

                   </tr>";
            }


            ?>

        </tbody>
    </table>
</div>
<script>
    $("#table-reacond").DataTable({});
</script>