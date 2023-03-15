<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once "../../include/connect_mvc.php";
include('../../Models/Mdl_ConexionBD.php');
include('../../Models/Mdl_MarcadoAMano.php');
include('../../Models/Mdl_PzasVolante.php');
include('../../Models/Mdl_Excepciones.php');

include('../../assets/scripts/cadenas.php');
$debug = 0;
$idUser = $_SESSION['CREident'];
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}

$date_start = !empty($_POST['date-start']) ? $_POST['date-start'] : "";
$date_end = !empty($_POST['date-end']) ? $_POST['date-end'] :  "";
$programa = !empty($_POST['programa']) ? $_POST['programa'] : '';

/***************** CASTEO DE FECHAS ****************** */
if ($date_start != '' and $date_end != '') {
    $date_start = date("Y-m-d", strtotime(str_replace("/", "-", $date_start)));
    $date_end = date("Y-m-d", strtotime(str_replace("/", "-", $date_end)));
}


$filtradoFecha = ($date_start != '' and $date_end != '') ? "l.fecha BETWEEN '$date_start' AND '$date_end'" : "1=1";
$filtradoPrograma = $programa != '' ? "l.idCatPrograma='$programa'" : "1=1";

$obj_marcado = new MarcadoAMano($debug, $idUser);
$obj_volante = new PzasVolante($debug, $idUser);
$DataLote = $obj_marcado->getLotesCerrados($filtradoFecha, $filtradoPrograma);

$arreglo = [];
foreach ($DataLote as $key => $value) {
    $arreglo['data'][] = $value;
}
?>
<div class="table-responsive">

    <table id="table-historial" class="table table-sm">
        <thead>
            <tr>
                <th>#</th>
                <th>Fecha</th>
                <th>Lote</th>
                <th>Programa</th>
                <th>Piezas Totales</th>
                <th>Yield</th>
                <th>Área Crust</th>
                <th>% Decremento</th>
                <th>Área Medida con Decremento </th>
                <th>Área de Piezas Calculadas</th>
                <th>Empleado Responsable</th>
                <th>Acciones</th>

            </tr>

        </thead>
        <tbody>
            <?php
            /*  $count=0;
            foreach ($DataLote as $key => $value) {
                $count++;
                $f_pzasTotales= formatoMil($DataLote[$key]['pzasTotales'], 0);
                $f_yield= formatoMil($DataLote[$key]['yield'], 2);
                $f_areaCrust= formatoMil($DataLote[$key]['areaCrust'], 2);
                $f_areaCrustDecremento= formatoMil($DataLote[$key]['areaCrustDecremento'], 2);
                $f_area= formatoMil($DataLote[$key]['area'], 2);

               echo "<tr>
                    <td>{$count}</td>
                    <td>{$DataLote[$key]['f_fecha']}</td>
                    <td>{$DataLote[$key]['n_lote']}</td>
                    <td>{$DataLote[$key]['n_programa']}</td>
                    <td>{$f_pzasTotales}</td>
                    <td>{$f_yield}</td>
                    <td>{$f_areaCrust}</td>
                    <td>{$f_areaCrustDecremento}</td>
                    <td>{$f_area}</td>
                    <td>{$DataLote[$key]['n_empleado']}</td>
                    <td><a target='_blank' href='../PDFReportes/Controller/tickets.php?op=getmarcado&data=".$DataLote[$key]['id']."' class='button btn btn-xs btn-danger'><i class='fas fa-download'></i></a></td>

               </tr>";
            }*/
            ?>
        </tbody>
    </table>
</div>
<!-- Modal -->
<div class="modal fade" id="modalCrust" tabindex="-1" role="dialog" aria-labelledby="modalCrustLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content block-Crust">
            <div class="modal-header bg-TWM text-white">
                <h5 class="modal-title" id="modalCrustLabel">Actualizar Información del Lote: <span id="txtLote"></span></h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formAddCrust">
                <div class="modal-body" id="cargaFormAddCrust">
                  
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-success">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    <?php
    $var = json_encode($arreglo);
    echo 'var datsJson = ' . $var . ';';
    ?>

    function ejecutandoCarga(identif, element) {

        var selector = 'DIV' + identif;
        var finicio = $('#fStart').val();
        var ffin = $('#fEnd').val();

        $.post("../templates/MarcadoAMano/detalleMarcado.php", {
                ident: identif
            },
            function(respuesta) {
                $("#" + selector).html(respuesta);
            });

    }
    /********** AGREGAR ID DE LOTE ***********/
    function agregarIdLote(id, n_lote){

        $("#txtLote").text(n_lote);
        $('#cargaFormAddCrust').load('../templates/MarcadoAMano/detalleEdicionMarcado.php?data='+id);

        $("#idLoteActualizar").val(id);

    }
    /********** AGREGAR AREA CRUST ***********/
    $("#formAddCrust").submit(function(e) {
        e.preventDefault();
        formData = $(this).serialize();
        $.ajax({
            url: '../Controller/marcadoMano.php?op=editardatos',
            data: formData,
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    bloqueoModal(e, 'block-Crust', 2)
                    notificaSuc(resp[1]);
                    $("#modalCrust").modal("hide");
                    setTimeout(() => {
                        update();
                    }, 1000);

                } else if (resp[0] == 0) {

                    bloqueoModal(e, 'block-Crust', 2)
                    notificaBad(resp[1])


                }
            },
            beforeSend: function() {
                bloqueoModal(e, 'block-Crust', 1)
            }

        });
    });
</script>
<script src="../assets/extra-libs/datatables.net/js/jquery.dataTables.min-ESP.js"></script>
<script src="../assets/tablas/dataTable-Marcado.js"></script>
<script src="../dist/js/pages/datatable/datatable-basic.init.js"></script>