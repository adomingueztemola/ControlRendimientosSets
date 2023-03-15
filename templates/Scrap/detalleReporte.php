<?php
$debug = 0;
session_start();
define('INCLUDE_CHECK', 1);
require_once "../../include/connect_mvc.php";
include('../../assets/scripts/cadenas.php');
$idUser = $_SESSION['CREident'];
if ($debug == 1) {
    print_r($_GET);
    //  exit(0);
} else {
    error_reporting(0);
}
$ident = !empty($_POST['ident']) ? $_POST['ident'] : '';
$obj_tarimas = new Scrap($debug, $idUser);
$Data = $obj_tarimas->getDetTarimas($ident);
$Data = Excepciones::validaConsulta($Data);

?>

<div class="row">
    <div class="col-lg-12 col-md-12 col-xs-12 col-sm-12">
        <table class="table table-sm table-hover table-dark">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Lote</th>
                    <th>Programa</th>
                    <th>12:00</th>
                    <th>03:00</th>
                    <th>06:00</th>
                    <th>09:00</th>
                    <th>Total</th>

                </tr>
            </thead>
            <?php
            $count = 1;
            foreach ($Data as  $value) {
                $f_12= formatoMil($value['_12'],0);
                $f_3= formatoMil($value['_3'],0);
                $f_6= formatoMil($value['_6'],0);
                $f_9= formatoMil($value['_9'],0);
                $f_total= formatoMil($value['total'],0);

                echo "
                   <tr>
                        <td>{$count}</td>
                        <td>{$value['loteTemola']}</td>
                        <td>{$value['nPrograma']}</td>
                        <td>{$f_12}</td>
                        <td>{$f_3}</td>
                        <td>{$f_6}</td>
                        <td>{$f_9}</td>
                        <td>{$f_total}</td>
                   </tr>
                   ";
                $count++;
            }
            ?>
        </table>
    </div>
</div>