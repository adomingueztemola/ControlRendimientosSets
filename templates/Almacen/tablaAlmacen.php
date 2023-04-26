<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../../include/connect_mvc.php";

$debug = 0;
$idUser = $_SESSION['CREident'];
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}

?>
<div class="table-responsive">
    <table id="table-cajas" class="table table-sm">
        <thead>
            <tr class="">
                <th>Lote</th>
                <th>Programa</th>
                <th>Cajas</th>
            </tr>
        </thead>
     
    </table>
</div>


<script>
    $("#table-cajas").DataTable({
        ajax: {
            "url": "../Controller/empaque.php?op=getcajasempacadas",
            "type": "POST"
        },
        }

    );
</script>