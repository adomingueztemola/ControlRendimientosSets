<div class="table-responsive">
    <div class="row">
        <div class="col-md-12">
            <table class="table table-sm display nowrap" id="table-particiones">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Lote</th>
                        <th>1s</th>
                        <th>2s</th>
                        <th>3s</th>
                        <th>4s</th>
                        <th>20</th>

                        <th>Total</th>
                        <th>Área Proveedor</th>
                        <th>Lote Inicial</th>

                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    $("#table-particiones").DataTable({
     
        ajax: {
            "url": "../Controller/particionLotes.php?op=getparticionesregistradas",
            "type": "POST"
        },
    })
</script>