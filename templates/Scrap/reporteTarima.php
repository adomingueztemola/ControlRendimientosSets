<?php
session_start();
$Data = $_SESSION['dataExcelScrap'];
?>
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <ul class="list-inline text-right">
            <li class="list-inline-item">
                <i class="fa fa-circle m-r-5 text-danger"></i>No Encontrado
            </li>
            <li class="list-inline-item">
                <i class="fa fa-circle m-r-5 text-warning"></i>Error Piezas
            </li>
            <li class="list-inline-item">
                <i class="fa fa-circle m-r-5 text-primary"></i>Error Sumatoria
            </li>
            <li class="list-inline-item">
                <i class="fa fa-circle m-r-5 text-info"></i>Error Lote
            </li>
        </ul>
    </div>

</div>
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Lote</th>
                    <th>12:00</th>
                    <th>03:00</th>
                    <th>06:00</th>
                    <th>09:00</th>
                    <th>Total</th>
                    <th>Estatus</th>

                </tr>
            </thead>
            <tbody>
                <?php
                $count = 1;
                foreach ($Data['val'] as $value) {
                    $lblEstatus = $value["estatus"] == '0' ? '<i class="fas fa-times text-danger"></i>' : '<i class="fas fa-check text-success"></i>';
                    $bgColor = $value["bgColor"];
                    echo "<tr class='{$bgColor}'>
                <td>{$count}</td>
                <td>{$value['lote']}</td>
                <td>{$value['_12']}</td>
                <td>{$value['_3']}</td>
                <td>{$value['_6']}</td>
                <td>{$value['_9']}</td>
                <td>{$value['total']}</td>
                <td>{$lblEstatus}</td>

            </tr>";
                    $count++;
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
<script>
    validaPaseDeReporte("<?= $Data['gral']['filasIncompletas'] ?>")

    function finalizarCarga() {
        json = <?php print_r(json_encode(["arrayReporte" => $Data['val']])) ?>;
        jsonFecha = {
            "fechaSalida": $("#fechaSalida").val()
        }
        jsonUnidos = Object.assign(json, jsonFecha);
        $.ajax({
            type: 'POST',
            url: '../Controller/scrap.php?op=disminuirstkrechz',
            data: jsonUnidos,
            success: function(respuesta) {
                var resp = respuesta.split('|');
                if (resp[0] == 1) {
                    abrirNuevoTab("../PDFReportes/Controller/EtiquetaTarima.php?op=gettarima&data=" + resp[2]);
                    cerrarModal("modalcarga");
                    bloqueoModal("", "block-modalScrap", 2)
                    setTimeout(() => {
                        update()
                    }, 1500);
                } else {
                    notificaBad(resp[1]);
                    //update()
                    bloqueoModal("", "block-modalScrap", 2)

                }
            },
            beforeSend: function() {
                bloqueoModal("", "block-modalScrap", 1)

            }
        });
    }
</script>