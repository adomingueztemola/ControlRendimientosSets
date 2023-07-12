<?php
session_start();
define('INCLUDE_CHECK', 1);
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
                <th>Usuario Usando</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<script>
    $("#table-reportelote").DataTable({
        ajax: {
            "url": "../Controller/rendimiento.php?op=getlotesocupados",
            "type": "POST"
        },
        'aoColumnDefs': [{
            'targets': 7,
            "bSortable": false,

            'searchable': false,
            "bSearchable": false,
            'orderable': false,
            'className': 'dt-body-center',
            'render': function(data, type, full, meta) {
                return '<button class="btn btn-danger btn-xs"  onclick="desconexionLote(\'' + data + '\')"><i class=" fas fa-unlink"></i></button>';
            }
        }],

    });

    function desconexionLote(id) {
        $.ajax({
            url: '../Controller/rendimiento.php?op=desconexionlote',
            data: {
                id: id            
            },
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                    update()

                } else if (resp[0] == 0) {
                    notificaBad(resp[1])


                }
            },
            beforeSend: function() {

            }

        });
    }
</script>