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
$inicioAnio=date("Y-01-01");
$finAnio=date("Y-12-t");

$filtradoFecha = ($date_start != '' and $date_end != '') ?
    "r.fechaEngrase BETWEEN '$date_start' AND '$date_end'" : "r.fechaEngrase BETWEEEN '$inicioAnio' AND '$finAnio'";
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
                <th>Teseo®</th>
                <th>Piezas OK</th>
                <th>Piezas NOK</th>
                <th>Ud. Empacadas</th>

                <th>Atraso</th>
                <th>Liberación</th>


            </tr>
        </thead>
        <tbody>
            <?php
            $count = 1;
            foreach ($Data as $key => $value) {
                $lblLiberacion= $value["regEmpaque"]=='1'?
                "<i class='fas fa-check text-success'></i>":"<i class='fas fa-box text-succes'></i>";
                $f_pzasTeseo= formatoMil($value['pzasCortadasTeseo'], 0);
                $f_pzasOk= formatoMil($value['pzasOk'], 0);
                $f_pzasNok= formatoMil($value['pzasNok'], 0);
                $f_unidadesEmp= formatoMil($value['unidadesEmpacadas'], 0);

                echo "<tr>
                    <td>{$count}</td>
                    <td>{$value['fFechaEngrase']}</td>
                    <td>{$value['loteTemola']}</td>
                    <td>{$value['nProceso']}</td>
                    <td>{$value['nPrograma']}</td>
                    <td>{$value['nMateriaPrima']}</td>
                    <td>{$f_pzasTeseo}</td>
                    <td>{$f_pzasOk}</td>
                    <td>{$f_pzasNok}</td>
                    <td>{$f_unidadesEmp}</td>
                    <td>{$value['diasAtraso']} día(s)</td>
                    <td>$lblLiberacion</td>

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