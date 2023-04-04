<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="table-responsive">
            <table class="table table-sm table-hover display nowrap" id="table-pruebas">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Semana Producción</th>
                        <th>Fecha</th>
                        <th>Lote</th>
                        <th>1s</th>
                        <th>2s</th>
                        <th>3s</th>
                        <th>4s</th>
                        <th>20</th>
                        <th>Total</th>

                        <th class="table-danger">(-) HIDES</th>
                        <th>Porcentaje Descontado</th>

                    </tr>
                </thead>

            </table>
        </div>
    </div>
</div>
<script>
    //Declaracion de Tabla
    $("#table-pruebas").DataTable({
        rowCallback: function(row, data) {
            $(row).find('td:eq(10)').addClass("table-danger");
        },
        ajax: {
            "url": "../Controller/pruebasLados.php?op=getpruebasregistradas",
            "type": "POST"
        },
    })
</script>