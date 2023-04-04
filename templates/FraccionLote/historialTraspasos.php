<div class="row">
    <div class="col-md-12">
        <table class="table table-sm display nowrap" id="table-traspasos">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Fecha</th>
                    <th>Lote Transmisor</th>
                    <th>Lote Receptor</th>
                    <th>1s</th>
                    <th>2s</th>
                    <th>3s</th>
                    <th>4s</th>
                    <th>20</th>
                    <th>Total</th>
                    <th>Área Proveedor</th>

                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
</div>
<script>
     $("#table-traspasos").DataTable({
     
     ajax: {
         "url": "../Controller/reasignacionLotesFracc.php?op=gettraspasosregistrados",
         "type": "POST"
     },
 })
</script>