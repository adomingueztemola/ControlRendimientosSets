<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../../include/connect_mvc.php";

include('../../Models/Mdl_Excepciones.php');

include('../../assets/scripts/cadenas.php');

$debug = 1;
$idUser = $_SESSION['CREident'];
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}







?>

<div class="table-responsive">

    <table id="table-historial" class="table table-sm">
        <thead>
            <tr class="bg-TWM text-white">
                <th>#</th>
                <th>Lote Temola</th>
                <th>Programa</th>
                <th>12.00</th>
                <th>03.00</th>
                <th>06.00</th>
                <th>09.00</th>
                <th>Total</th>
              

            </tr>

        </thead>

        <tbody>
             <?php
            $count=0;
            $DataLote = $value;
            
            
           foreach ($DataLote as $key => $value) {
                $count++;
               
                

               echo"<tr>
                    <td>{$count}</td>
                    <td>{$DataLote[$key]['idTeseo']}</td>
                    <td>{$DataLote[$key]['_12']}</td>
                    <td>{$DataLote[$key]['_6']}</td>
                    <td>{$DataLote[$key]['_3']}</td>
                    <td>{$DataLote[$key]['_9']}</td>
                    <td>{$DataLote[$key]['_Pzastotales']}</td>
                  

               </tr>";
            }
            ?>
        </tbody>



    </table>
</div>
<script src="../assets/extra-libs/datatables.net/js/jquery.dataTables.min-ESP.js"></script>
<script src="../dist/js/pages/datatable/datatable-basic.init.js"></script>
<script>
   $("#table-historial").DataTable({});
</script>
