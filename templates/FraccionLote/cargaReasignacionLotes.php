<div class="table-responsive">
    <table class="table table-sm display nowrap table-bordered" id="table-reprog">
        <thead>
            <tr>
                <th>#</th>
                <th>Fecha</th>
                <th>Lote</th>
                <th>Programa Actual</th>
                <th>Programa Anterior</th>
                <th>Proceso Actual</th>
                <th>Proceso Anterior</th>
                <th>Tipo</th>

            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
<script>
    $("#table-reprog").DataTable({
     
        ajax: {
            "url": "../Controller/particionLotes.php?op=getreprogregistradas",
            "type": "POST"
        },
    })
</script>