<?php
$debug = 0;
session_start();
define('INCLUDE_CHECK', 1);
require_once "../../include/connect_mvc.php";
include('../../assets/scripts/cadenas.php');
$idUser = $_SESSION['CREident'];
if ($debug == 1) {
    print_r($_POST);
} else {
    error_reporting(0);
}
$date_start = !empty($_POST['date-start']) ? $_POST['date-start'] : "";
$date_end = !empty($_POST['date-end']) ? $_POST['date-end'] :  "";
$programa = !empty($_POST['programa']) ? $_POST['programa'] : '';

$date_start = $date_start != "" ? date("Y-m-d", strtotime(str_replace("/", "-", $date_start))) : $date_start;
$date_end = $date_end != "" ? date("Y-m-d", strtotime(str_replace("/", "-", $date_end))) : $date_end;
?>
<div class="row">
    <div class="col-lg-12 col-md-12 col-xs-12 col-sm-12 table-responsive">
        <table class="table table-sm display nowrap table-bordered" id="table-depuracion">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Lote</th>
                    <th scope="col">Programa</th>
                    <th scope="col">Fecha Empaque</th>
                    <th scope="col">#Caja</th>
                    <th scope="col">Tipo</th>
                    <th scope="col">12:00</th>
                    <th scope="col">03:00</th>
                    <th scope="col">06:00</th>
                    <th scope="col">09:00</th>
                    <th scope="col">Total Piezas</th>
                    <th scope="col">Fecha de Depuración</th>
                    <th scope="col">Usuario</th>
                    <th scope="col">Motivo</th>


                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
</div>

<script src="../assets/extra-libs/datatables.net/js/jquery.dataTables.min-ESP.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>
<script src="../dist/js/pages/datatable/datatable-basic.init.js"></script>
<script>
    var dt = $("#table-depuracion").DataTable({
        ajax: {
            "url": "../Controller/empaque.php?op=gethistdepuracion",
            "type": "POST",
            "data": {
                date_start: "<?= $date_start ?>",
                date_end: "<?= $date_end ?>",
                programa: "<?= $programa ?>"
            }
        },
        "aaSorting": [],
       


    })
</script>