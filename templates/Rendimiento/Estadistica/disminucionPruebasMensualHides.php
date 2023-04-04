<?php setlocale(LC_TIME, 'es_ES.UTF-8'); ?>
<div class="row align-items-center bg-light mb-2">
    <div class="col-xs-12 col-md-6">
        <h3 class="m-b-0 font-light"><?=strftime("%B %Y")?></h3>
        <span class="font-14 text-muted">REPORTE DE DISMINUCIONES</span>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-sm table-hover display nowrap" id="table-disminucion">
                <thead>
                    <tr>
                        <th>Semana</th>
                        <th>Lote</th>
                        <th class="table-danger">(-) Hides</th>
                        <th>1s</th>
                        <th>2s</th>
                        <th>3s</th>
                        <th>4s</th>
                        <th>20</th>
                        <th>Total</th>

                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<script>
    $("#table-disminucion").DataTable({
        rowCallback: function(row, data) {
            $(row).find('td:eq(2)').addClass("table-danger");
        },
        ajax: {
            "url": "../Controller/pruebasLados.php?op=getpruebassemana",
            "type": "POST"
        },
    })
</script>