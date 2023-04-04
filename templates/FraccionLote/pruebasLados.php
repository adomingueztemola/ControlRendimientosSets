<div class="row">
    <div class="col-md-4 col-lg-4 col-xs-6 col-sm-6">
        <div class="card border">
            <form id="formPruebas">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                            <label for="lote" class="form-label required"> LOTE: </label>
                            <select name="lote" style="width:100%" required class="form-control select2Form LotesProceso" id="lote"></select>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th class="text-center table-dark">1s</th>
                                        <th class="text-center table-dark">2s</th>
                                        <th class="text-center table-dark">3s</th>
                                        <th class="text-center table-dark">4s</th>
                                        <th class="text-center table-dark">20</th>
                                        <th class="text-center table-dark">Total</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="table-active text-center" id="_1s">0</td>
                                        <td class="table-active text-center" id="_2s">0</td>
                                        <td class="table-active text-center" id="_3s">0</td>
                                        <td class="table-active text-center" id="_4s">0</td>
                                        <td class="table-active text-center" id="_20">0</td>
                                        <td class="table-active text-center" id="total_s">0</td>
                                    </tr>
                                </tbody>

                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                            <label for="fecha" class="form-label required"> FECHA DE PRUEBA: </label>
                            <input type="date" required class="form-control" name="fecha" id="fecha">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                            <label class="form-label required text-danger" for="lote"> HIDES A DESCONTAR: </label>
                            <input type="number" required step="1" min="1" class="form-control focusCampo" name="hides" id="hides">
                        </div>
                    </div>

                    <hr>
                    <div class="row">
                        <div class="col-md-6"></div>
                        <div class="col-md-6 text-rigth">
                            <div id="bloqueo-btn-1" style="display:none">
                                <button class="btn btn-TWM" type="button" disabled="">
                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    Espere...
                                </button>

                            </div>
                            <div id="desbloqueo-btn-1">
                                <button type="submit" class="button btn btn-success">Guardar</button>
                                <button type="reset" onclick="clearForm('formPruebas')" class="button btn btn-danger">Cancelar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

    </div>
    <div class="col-md-8 col-lg-8 col-xs-6 col-sm-6">
        <div class="card border">
            <div class="card-body" id="content-pruebas">

            </div>
        </div>
    </div>

</div>
<script src="../assets/scripts/selectFiltros.js"></script>
<script src="../assets/scripts/clearDataSinSelect.js"></script>

<script>
    update('templates/PruebasLados/cargaPruebasLados.php', 'content-pruebas', 1);
    mostrar_info()
    /********** ALMACENAR PRUEBA ***********/
    $("#formPruebas").submit(function(e) {
        e.preventDefault();
        formData = $(this).serialize();
        $.ajax({
            url: '../Controller/pruebasLados.php?op=agregarpruebas',
            data: formData,
            type: 'POST',
            success: function(json) {
                resp = json.split('|')
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                    setTimeout(() => {
                        bloqueoBtn("bloqueo-btn-1", 2)
                        update('templates/PruebasLados/cargaPruebasLados.php', 'content-pruebas', 1);
                        clearForm("formPruebas")
                    }, 1000);

                } else if (resp[0] == 0) {
                    notificaBad(resp[1])
                    bloqueoBtn("bloqueo-btn-1", 2)


                }
            },
            beforeSend: function() {
                bloqueoBtn("bloqueo-btn-1", 1)
            }

        });
    });

    function mostrar_info() {
        $('select#lote').on('change', function() {
            valor = $(this).val();
            $.ajax({
                    data: {
                        "ident": valor
                    },
                    type: "POST",
                    dataType: "json",
                    url: "../Controller/pruebasLados.php?op=detalleslote",
                    beforeSend: function() {
                        // setting a timeout
                        $("#_1s").text("")
                        $("#_2s").text("")
                        $("#_3s").html("<div class='spinner-border spinner-border-sm' role='status'><span class='sr-only'></span></div>")
                        $("#_4s").text("")
                        $("#_20").text("")
                        $("#total_s").text("")
                    },
                })
                .done(function(data, textStatus, jqXHR) {
                    if (data != null) {
                        $("#_1s").text(data["1s"] * 2)
                        $("#_2s").text(data["2s"] * 2)
                        $("#_3s").text(data["3s"] * 2)
                        $("#_4s").text(data["4s"] * 2)
                        $("#_20").text(data["_20"] * 2)
                        $("#total_s").text(data["total_s"] * 2)
                        $("#hides").prop("max", data["total_s"] * 2)
                    } else {
                        $("#_1s").text("0")
                        $("#_2s").text("0")
                        $("#_3s").text("0")
                        $("#_4s").text("0")
                        $("#_20").text("0")
                        $("#total_s").text("0")
                        $("#hides").prop("max", "0")
                    }


                }).fail(function(jqXHR, textStatus, errorThrown) {

                });
        });
    }
</script>