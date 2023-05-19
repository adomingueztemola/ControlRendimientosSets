<?php
$id = !empty($_POST['id']) ? $_POST['id'] : "";
?>

<div id="accordion2" class="accordion" role="tablist" aria-multiselectable="true">
    <div class="card">
        <div class="card-header" role="tab" id="headingOne">
            <h5 class="mb-0">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse1" aria-expanded="true" aria-controls="collapseOne">
                    Collapsible Group Item #1
                </a>
            </h5>
        </div>
        <div id="collapse1" class="collapse" role="tabpanel" aria-labelledby="headingOne">
            <div class="card-body">
                Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et.
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header" role="tab" id="headingTwo">
            <h5 class="mb-0">
                <a class="collapsed" data-toggle="collapse" data-parent="#accordion2" href="#collapse2" aria-expanded="false" aria-controls="collapseTwo">
                    Collapsible Group Item #2
                </a>
            </h5>
        </div>
        <div id="collapse2" class="collapse" role="tabpanel" aria-labelledby="headingTwo">
            <div class="card-body">
                Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et.
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header" role="tab" id="headingThree">
            <h5 class="mb-0">
                <a class="collapsed" data-toggle="collapse" data-parent="#accordion2" href="#collapse3" aria-expanded="false" aria-controls="collapseThree">
                    Collapsible Group Item #3
                </a>
            </h5>
        </div>
        <div id="collapse3" class="collapse" role="tabpanel" aria-labelledby="headingThree">
            <div class="card-body">
                Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et.
            </div>
        </div>
    </div>
</div>
<div>
    <button class="btn btn-info btn-lg offset-md-9">Imprimir Etiquetas</button>
</div>
<script>
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
                        <tr>
                            <td colspan='6'>Sin registro de lados en el lote</td>
                        </tr>
                    `
                $("#btn-agregar").prop("hidden", true)
            } else {
                count = 1;
                selecciones = getSelecciones();
                let doOptions = (selecciones, id) => {
                    var_return = "";
                    selecciones.forEach(element => {
                        selected = element.id == id ? "selected" : "";
                        var_return += `
                            <option ${selected} value="${element.id}">${element.nombre}</option>
                        `
                    });
                    return var_return
                };
                respuesta.forEach(element => {
                    options = doOptions(selecciones, element.idCatSeleccion)
                    areaDM = element.areaDM.toLocaleString('es-MX')
                    areaFT = element.areaFT.toLocaleString('es-MX')
                    areaRedondFT = parseFloat(element.areaRedondFT).toFixed(2).toLocaleString('es-MX')
                    tabla += `<tr id="trlado-${element.id}">
                    <td><input type="checkbox"  id="chck-${element.id}" 
                         name="lados[]" class="chckPack" value="${element.id}"></td>
                <td><label for="chck-${element.id}" class="form-label">${element.numSerie}</label></td>
                <td>${areaFT}</td>
                <td>${areaDM}</td>
                <td>${areaRedondFT}</td>
                <td>
                    <select class="custom-select" onchange="cambiarSeleccion(this)" 
                            data-id="${element.id}" name="calidad" id="cali">
                      ${options}
                    </select>
                </td></tr> `;
                    count++;
                });
                $("#btn-agregar").prop("hidden", false)

            }
            $("#tbody-lados").html(tabla);

        },


    });
</script>