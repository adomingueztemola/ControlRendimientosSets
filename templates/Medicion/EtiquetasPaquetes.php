<?php
$id = !empty($_POST['id']) ? $_POST['id'] : "";
?>
<div class="row mb-1">
    <div class="col-md-7"></div>
    <div class="col-md-5">
        <div class="input-group mb-3">
            <input type="number" class="form-control" id="numPaquete" placeholder="#Paquete" aria-label="" aria-describedby="basic-addon1">
            <div class="input-group-append">
                <button class="btn btn-TWM" onclick="panelImpresion()" type="button"><i class="fas fa-print"></i></button>
            </div>
        </div>
    </div>
</div>
<div class="row mb-1">
    <div class="col-6">
        <div class="card text-white bg-TWM mb-3" style="max-width: 18rem;">
            <div class="card-header">
                <h4>Paquetes: <span id="cont-paq">0</span></h4>
            </div>
        </div>
    </div>
    <div class="col-6">
        <a id="impresionEtiq" href="../PDFReportes/Controller/EtiquetasPaquetes.php?op=getetiquetas&data=<?= $id ?>" target="_blank" class="button btn btn-info text-white btn-lg"><i class="fas fa-print"></i> Todas las Etiquetas</a>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div id="accordion-paquetes" class="accordion" role="tablist" aria-multiselectable="true">
        </div>

    </div>
</div>
<script>
    verData()

    function verData() {
        $.ajax({
            url: '../Controller/medicion.php?op=getpaquetesxlote',
            data: {
                id: "<?= $id ?>"
            },
            type: 'POST',
            async: false,
            dataType: "json",
            success: function(respuesta) {
                tabla = ""
                if (!respuesta.length) {
                    tabla += `
                    <div class="mt-1 alert alert-info" role="alert">
                       Sin paquetes registrados.
                    </div>
                    `
                    $("#cont-paq").text("0");

                    $("#impresionEtiq").prop("hidden", true)
                } else {
                    contadorPack = 0;
                    respuesta.forEach(element => {
                        areaTotalRd = parseFloat(element.areaTotalRd).toFixed(2).toLocaleString('es-MX')
                        totalLados = parseFloat(element.totalLados).toFixed(0).toLocaleString('es-MX')
                        tabla += ` <div class="card" id="cardAccordion-${element.id}">
                        <div class="card-header" role="tab" id="headingOne">
                            <h5 class="mb-0">
                                    <div class="row">
                                        <div class="col-10">
                                        <a data-toggle="collapse" data-parent="#accordion-paquetes" onclick="verDetPaquete(${element.id})" href="#collapse${element.id}" aria-expanded="true" aria-controls="collapseOne">
                                        Paquete #${element.numPaquete}  *  Lote: ${element.loteTemola} 
                                        <br>
                                        Área Red. Ft<sup>2</sup>: ${areaTotalRd}
                                        <br>
                                        Total Lados: ${totalLados}
                                        </a>
                                        </div>
                                        <div class="col-2">
                                        <div id="bloqueo-btnDlt-${element.id}" style="display:none">
                                            <button class="btn button btn-xs btn-outline-danger" type="button" disabled="">
                                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                            </button>
                                        </div>
                                        <div id="desbloqueo-btnDlt-${element.id}">
                                            <button onclick="eliminarPaquete(${element.id}, ${element.idLoteMedido})" class="btn button btn-xs btn-outline-danger"><i class="fas fa-trash-alt"></i></button>    
                                        </div>
                                        </div>
                                    </div>

                            </h5>
                        </div>
                        <div id="collapse${element.id}" class="collapse" role="tabpanel" aria-labelledby="headingOne">
                            <div class="card-body p-0 m-0" id="card-body${element.id}">
                            </div>
                        </div>
                    </div>`;
                        $("#impresionEtiq").prop("hidden", false)
                        contadorPack++
                    });
                    $("#btn-agregar").prop("hidden", false)
                    contadorPack = parseFloat(contadorPack).toFixed(0).toLocaleString('es-MX')
                    $("#cont-paq").text(contadorPack);

                }
                $("#accordion-paquetes").html(tabla);

            },


        });
    }

    function panelImpresion(){
        numPaquete= $("#numPaquete").val()
        if(numPaquete<=0 || numPaquete==''){
            notificaBad("Selecciona un número de paquete");
        }else{
            window.open("../PDFReportes/Controller/EtiquetasPaquetes.php?op=getetiqueta&paq="+numPaquete+"&data=<?= $id ?>");

        }
    }

    function verDetPaquete(id) {
        $.ajax({
            url: '../Controller/medicion.php?op=getdetpaquete',
            data: {
                id: id
            },
            type: 'POST',
            async: false,
            dataType: "json",
            success: function(respuesta) {
                tabla = ""
                if (!respuesta.length) {
                    tabla += `
                    <div class="alert alert-danger" role="alert">
                        No hay registro del detallado de los lados
                    </div> `
                } else {
                    tabla = `<div class="row">
                            <div class="col-12">
                                <table class="table table-sm table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="text-center">#LADO</th>
                                            <th class="text-center">CLASIFICACIÓN</th>
                                            <th class="text-center">MEDIDA Ft<sup>2</sup></th>
                                        </tr>
                                    </thead>
                                    <tbody>`;
                    totalFt2 = 0;
                    respuesta.forEach(element => {
                        areaRedondFT = parseFloat(element.areaRedondFT).toFixed(2).toLocaleString('es-MX')
                        totalFt2 += parseFloat(element.areaRedondFT);
                        tabla += `<tr>
                                    <td class="text-center">${element.numLado}</td>
                                    <td class="text-center">${element.nSeleccion}</td>
                                    <td class="text-center">${areaRedondFT}</td>
                                  </tr> `;

                    });
                    tabla += `<tr>
                                    <td colspan="2" class="text-center">TOTAL FT<sup>2</sup></td>
                                    <td class="text-center">${totalFt2.toFixed(2).toLocaleString('es-MX')}</td>
                                  </tr> `;
                    tabla += `</tbody>
                                </table>
                            </div>
                        </div>`;
                }
                $("#card-body" + id).html(tabla);

            },


        });

    }

    function eliminarPaquete(id, idLoteMedido) {
        $.ajax({
            type: 'POST',
            url: '../Controller/medicion.php?op=eliminarpaquete',
            data: {
                id: id,
                idLoteMedido: idLoteMedido
            },
            success: function(respuesta) {
                var resp = respuesta.split('|');
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                    bloqueoBtn("bloqueo-btnDlt-" + id, 2)
                    $("#cardAccordion-" + id).remove();
                    verLados(false)
                    verData()
                } else {
                    notificaBad(resp[1])
                    bloqueoBtn("bloqueo-btnDlt-" + id, 2)

                }
            },
            beforeSend: function() {
                bloqueoBtn("bloqueo-btnDlt-" + id, 1)

            }
        });
    }
</script>