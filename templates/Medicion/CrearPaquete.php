<?php
$id = !empty($_POST['id']) ? $_POST['id'] : "";
?>
<div class="card-body" style="height:500px; overflow-y: scroll;">
    <div class="row mb-1">
        <div class="col-md-8"></div>
        <div class="col-md-4 text-right">
            <button id="btn-agregar" onclick="agregarPaquete()" class="btn btn-success btn-lg">Crear Paquete</button>
        </div>
    </div>
    <table class="table table-hover  table-sm">
        <thead class="thead-dark">
            <tr>
                <th scope="col">#</th>
                <th scope="col">Num. Serie</th>
                <th scope="col">Área Ft<sup>2</sup></th>
                <th scope="col">Área Dm<sup>2</sup></th>
                <th scope="col">Red.</th>
                <th scope="col">Seleccion</th>
            </tr>
        </thead>
        <tbody id="tbody-lados">
        </tbody>
    </table>
</div>
<script>
    $.ajax({
        url: '../Controller/medicion.php?op=getladosxlote',
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

    function getSelecciones() {
        options = {};
        $.ajax({
            url: '../Controller/medicion.php?op=getselecciones',
            data: {
                id: "<?= $id ?>"
            },
            type: 'POST',
            async: false,
            dataType: "json",
            success: function(respuesta) {
                options = respuesta

            },


        });
        return options
    }

    function cambiarSeleccion(select) {
        id = $(select).data("id")
        seleccion = $(select).val()

        $.ajax({
            type: 'POST',
            url: '../Controller/medicion.php?op=cambiarseleccion',
            data: {
                id: id,
                seleccion: seleccion
            },
            success: function(respuesta) {
                var resp = respuesta.split('|');
                if (resp[0] == 1) {
                    notificaSuc(resp[1])

                } else {
                    notificaBad(resp[1])

                }
            },
            beforeSend: function() {}
        });

    }

    function agregarPaquete() {
        ladosPack = [];
        $("input:checkbox[class=chckPack]:checked").each(function() {
            let value = $(this).val()
            ladosPack.push(value);
        });
        $.ajax({
            type: 'POST',
            url: '../Controller/medicion.php?op=agregarpaquete',
            data: {
                ladosPack: ladosPack,
                id: "<?= $id ?>"

            },
            success: function(respuesta) {
                var resp = respuesta.split('|');
                if (resp[0] == 1) {
                    notificaSuc(resp[1])
                    ladosPack.forEach(element => {
                        $("#trlado-" + element).remove()
                    });
                } else {
                    notificaBad(resp[1])

                }
            },
            beforeSend: function() {}
        });

    }
</script>