    <div class="row">
        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5">
            <form id="formParticion">

                <div class="row">
                    <div class="col-md-12">
                        <label for="lote">Lote Padre</label>
                        <select class="form-control LotesProceso" name="lote" id="lote"></select>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <label for="programa">PROGRAMA</label>
                        <select class="form-control ProgramasFilter" style="width:100%" name="programa" id="programa"></select>
                    </div>
                    <div class="col-md-3">
                        <label for="hides">HIDES</label>
                        <input type="number" class="form-control" name="hides" id="hides" aria-label="">
                    </div>
                    <div class="col-md-3 mt-4">
                        <button type="submit" class="btn btn-success mt-1"><i class="fas fa-upload"></i></button>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-7" id="content-partidas">

        </div>
    </div>
    <script src="../assets/scripts/selectFiltros.js"></script>
    <script>
        update("templates/FraccionLote/historialParticiones.php", "content-partidas", 1)
        $("#formParticion").submit(function(e) {
            e.preventDefault();
            formData = $(this).serialize();
            $.ajax({
                url: '../Controller/particionLotes.php?op=agregarparticion',
                data: formData,
                type: 'POST',
                success: function(response) {
                    resp = response.split('|')
                    if (resp[0] == 1) {
                        notificaSuc(resp[1]);
                    } else if (resp[0] == 0) {
                        bloqueoBtn("bloqueo-btn-1", 2);
                        notificaBad(resp[1]);
                        update("templates/FraccionLote/historialPartidas.php", "content-partidas", 1)
                    }
                },
                beforeSend: function() {
                    bloqueoBtn("bloqueo-btn-1", 1)
                }

            });
        });
    </script>