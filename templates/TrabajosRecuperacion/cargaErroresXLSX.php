<?php
session_start();
define('INCLUDE_CHECK', 1);
require_once('../../include/connect_mvc.php');
$debug = 0;
$idUser = $_SESSION['CREident'];
$obj_trabajos = new TrabajosRecupera($debug, $idUser);
$Data = $obj_trabajos->getLogsXLSX();
foreach ($Data as $key => $value) {
    $ArrayDatos = explode('|', $value['datos']);
    $rowExcel = "<table class='table table-sm table-bordered'>
        <tbody>
        <tr>";
    foreach ($ArrayDatos as $keydatos => $valuedatos) {
        if ($keydatos == '0' or $keydatos == '4') {
            $valuedatos = date('d/m/Y', strtotime($valuedatos));
        }
        $rowExcel .= "<td><small>{$valuedatos}</small></td>";
    }
    $rowExcel .= "</tr></tbody></table>";
?>
    <div class="d-flex flex-row comment-row  border">
        <div class="comment-text w-100 p-0">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div id="bloqueo-btn-L<?= $value['id'] ?>" style="display:none">
                        <button class="btn btn-xs btn-TWM" type="button" disabled="">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        </button>

                    </div>
                    <div id="desbloqueo-btn-L<?= $value['id'] ?>">
                        <button class="button btn btn-xs btn-outline btn-danger" onclick="cerrarError(<?= $value['id'] ?>)"><i class="fas fa-trash-alt"></i></button>
                    </div>
                    <h6 class="font-medium text-danger">Error del: <?= $value['fFechaReg'] ?></h6>
                </div>

            </div>

            <span class="m-b-15 d-block"><?= $value['error'] ?></span>
            <span class="text-muted"><?= $rowExcel ?></span>
        </div>
    </div>
<?php
}
?>
<script>
    function cerrarError(id) {
        $.ajax({
            url: '../Controller/trabajosRecuperacion.php?op=cerrarerror',
            data: {
                id: id
            },
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                    setTimeout(() => {
                        bloqueoBtn("bloqueo-btn-L" + id, 2);
                        cargaErrores()
                    }, 1000);


                } else if (resp[0] == 0) {
                    notificaBad(resp[1])
                    bloqueoBtn("bloqueo-btn-L" + id, 2);
                }
            },
            beforeSend: function() {
                bloqueoBtn("bloqueo-btn-L" + id, 1);
            }

        });

    }
</script>