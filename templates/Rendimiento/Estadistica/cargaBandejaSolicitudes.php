<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once('../../../include/connect_mvc.php');
include("../../../Models/Mdl_ConexionBD.php");
include("../../../Models/Mdl_Solicitudes.php");
include('../../../assets/scripts/cadenas.php');
$debug = 0;
$idUser = $_SESSION['CREident'];
$nameUser = $_SESSION['CREnombreUser'];
setlocale(LC_TIME, 'es_ES.UTF-8');
$obj_solicitudes = new Solicitud($debug, $idUser);
$DataSolicitudes = $obj_solicitudes->getAllSolicitudes();
?>
<div class="comment-widgets">
    <div class="">
        <?php foreach ($DataSolicitudes as $key => $value) {
            $lbl_tipo = $DataSolicitudes[$key]["tipo"] == "1" ? "Edición" : "Excepción";
            echo "
                <div class='row'>
                <div class='col-md-12'>
                    <div class='p-2'>
                        <img src='../assets/images/message.png' alt='user' width='50' class='rounded-circle'>
                    </div>
                    <div class='comment-text w-100'>
                        <h6 class='font-medium'>{$DataSolicitudes[$key]["n_empleado"]}</h6>
                        <span class='m-b-15 d-block'>{$DataSolicitudes[$key]["descripcion"]}</span>
                        <div class='comment-footer'>
                            <span class='text-muted float-right'>{$DataSolicitudes[$key]["f_fechaReg"]}</span>
                            <span class='label label-rounded label-primary'>{$lbl_tipo}</span>
                        
                        </div>
                    </div>
                    </div>
                </div>";
        } ?>

    </div>

</div>