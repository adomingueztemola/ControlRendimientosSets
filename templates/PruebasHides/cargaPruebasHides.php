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
                        <th>Hides Descontados</th>
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
        ajax: {
            "url": "../Controller/pruebasHide.php?op=getpruebasregistradas",
            "type": "POST"
        },
    })
</script>