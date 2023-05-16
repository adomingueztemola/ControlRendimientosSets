<?php
$debug = 0;
session_start();
define('INCLUDE_CHECK', 1);
require_once "../../include/connect_mvc.php";
include('../../assets/scripts/cadenas.php');
$idUser = $_SESSION['CREident'];
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}

$date_start = !empty($_POST['date-start']) ? $_POST['date-start'] : date("01/m/Y");
$date_end = !empty($_POST['date-end']) ? $_POST['date-end'] :  date("t/m/Y");
$programa = !empty($_POST['programa']) ? $_POST['programa'] : '';

$date_start = date("Y-m-d", strtotime(str_replace("/", "-", $date_start)));
$date_end = date("Y-m-d", strtotime(str_replace("/", "-", $date_end)));


?>
<div class="row">
    <div class="col-lg-12 col-md-12 col-xs-12 col-sm-12 table-responsive">
        <table class="table table-sm" id="table-mediciones">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Lote</th>
                    <th scope="col">Programa</th>
                    <th scope="col">Lados Totales</th>
                    <th scope="col">Área Total Dm<sup>2</sup></th>
                    <th scope="col">Área Total Ft<sup>2</sup></th>
                    <th scope="col">Fecha Registro</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
</div>


<script src="../assets/extra-libs/datatables.net/js/jquery.dataTables.min-ESP.js"></script>
<script src="../assets/tablas/dataTable-Tarima.js"></script>
<script src="../dist/js/pages/datatable/datatable-basic.init.js"></script>