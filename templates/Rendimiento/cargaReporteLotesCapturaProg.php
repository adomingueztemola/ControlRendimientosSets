<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once('../../include/connect_mvc.php');
include('../../assets/scripts/cadenas.php');

$idUser = $_SESSION['CREident'];
$nameUser = $_SESSION['CREnombreUser'];
setlocale(LC_TIME, 'es_ES.UTF-8');
$debug = 0;
$space = 1;
if ($debug == 1) {
    print_r($_POST);
    //  exit(0);
} else {
    error_reporting(0);
}
/************************** VARIABLES DE FILTRADO *******************************/
$id = !empty($_POST['id']) ? $_POST['id'] : '';
$proceso = !empty($_POST['proceso']) ? $_POST['proceso'] : '';
$programa = !empty($_POST['programa']) ? $_POST['programa'] : '';
$materiaPrima = !empty($_POST['materia']) ? $_POST['materia'] : '';
$date_start = !empty($_POST['date-start']) ? $_POST['date-start'] : "";
$date_end = !empty($_POST['date-end']) ? $_POST['date-end'] :  "";
$idUser = $_SESSION['CREident'];
$nameUser = $_SESSION['CREnombreUser'];
$filtradoEstatus = $id == '1' ? '(r.tipoProceso="1" AND r.regEmpaque="1")' : '1=1';
$filtradoEstatus = $id == '2' ? '(r.tipoProceso="1" AND (r.regEmpaque!="1" OR r.regEmpaque IS NULL))' : $filtradoEstatus;
$filtradoEstatus = $id == '3' ? '(r.tipoProceso="2" AND  (r.regEmpaque!="1" OR r.regEmpaque IS NULL))' : $filtradoEstatus;

$filtradoEstatus = $id == '-1' ? '1=1' : $filtradoEstatus;
/***************** CASTEO DE FECHAS ****************** */
if ($date_start != '' and $date_end != '') {
    $date_start = date("Y-m-d", strtotime(str_replace("/", "-", $date_start)));
    $date_end = date("Y-m-d", strtotime(str_replace("/", "-", $date_end)));
}

$filtradoFecha = ($date_start != '' and $date_end != '') ?
    "r.fechaEngrase BETWEEN '$date_start' AND '$date_end'" : "1=1";
$filtradoProceso = $proceso != '' ? "r.idCatProceso='$proceso'" : "1=1";
$filtradoPrograma = $programa != '' ? "r.idCatPrograma='$programa'" : "1=1";
$filtradoMateria = $materiaPrima != '' ? "r.idCatMateriaPrima='$materiaPrima'" : "1=1";

$obj_rendimiento = new Rendimiento($debug, $idUser);
$Data = $obj_rendimiento->getLotesXCapturar($filtradoFecha, $filtradoProceso, $filtradoMateria, $filtradoPrograma, $filtradoEstatus);
$Data = Excepciones::validaConsulta($Data);
?>
<div class="table-responsive">
    <table class="table table-sm" id="table-reportelote">
        <thead>
            <tr>
                <th>#</th>
                <th>Fecha de Engrase</th>
                <th>Lote</th>
                <th>Proceso</th>
                <th>Programa</th>
                <th>Materia Prima</th>
                <th>Atraso</th>


            </tr>
        </thead>
        <tbody>
            <?php
            $count = 1;
            foreach ($Data as $key => $value) {
                $lblLiberacion= $value["regEmpaque"]=='1'?
                "<i class='fas fa-check text-success'></i>":"<i class='fas fa-box text-succes'></i>";

                $inptPzasCortadasTeseo= ($value["regEmpaque"]!='1' AND $value['tipoProceso']=='1')?
                "<input class='form-control' value='{$value['pzasCortadasTeseo']}' min='0' step='1' onchange='cambiarTeseo({$value['id']}, this)' id='teseo{$value['id']}' type='number'>"
                :"N/A";

                $inptPzasCortadasTeseo=($value["regEmpaque"]=='1' AND $value['tipoProceso']=='1')?
                "<b>".formatoMil($value['pzasCortadasTeseo'], 0)."</b>": $inptPzasCortadasTeseo;
                echo "<tr>
                    <td>{$count}</td>
                    <td>{$value['fFechaEngrase']}</td>
                    <td>{$value['loteTemola']}</td>
                    <td>{$value['nProceso']}</td>
                    <td>{$value['nPrograma']}</td>
                    <td>{$value['nMateriaPrima']}</td>
                    <td>{$value['diasAtraso']} día(s)</td>

                </tr>";
                $count++;
            }
            ?>
        </tbody>
    </table>
</div>
<script>
    $("#table-reportelote").DataTable({});

    function cambiarTeseo(id, input) {
        valueTeseo = parseInt($(input).val());
        //VALIDAR QUE EL VALOR SEA MAYOR A 0
        if (valueTeseo < 0) {
            notificaBad("Valor incorrecto, verifica la cantidad.")
            return 0;
        } else {
            $.ajax({
                url: '../Controller/empaque.php?op=actualizarteseo',
                data: {
                    id: id,
                    teseo: valueTeseo
                },
                type: 'POST',
                success: function(json) {
                    resp = json.split('|')
                    if (resp[0] == 1) {
                        notificaSuc(resp[1])
                       

                    } else if (resp[0] == 0) {

                        notificaBad(resp[1])


                    }
                },
                beforeSend: function() {

                }

            });
        }

    }
</script>