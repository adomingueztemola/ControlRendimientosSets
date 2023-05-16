function generateTable() {
  var result = [];
  var data = $("textarea[name=excel_data]").val().trim();
  var rows = data.split("\n");
  //Validar que el formato no este vacio
  if(rows.length<=1){
    notificaBad("Verifica que el reporte no este vacío");
    return 0
  }
  var table = $('<table class="table table-sm table-bordered" />');
  for (var y in rows) {
    var cells = rows[y].split("\t");
    var row = $("<tr />");
    //validar que las celdas sean 3
    if(cells.length>3){
        notificaBad("Verifica que el reporte tenga 3 columnas: <br>-Número de serie<br>-Fecha creación<br>-Área real");
        return 0
      }
    for (var x in cells) {
      row.append("<td>" + cells[x] + "</td>");
      if (y == "0" && x == "2") {
        row.append("<td>Área FT<sup>2</sup></td>");
        row.append("<td>Redondeo Área FT<sup>2</sup></td>");
        cells.push("Área FT2");
        cells.push("Redondeo Área FT2");
      }
      if (y > "0" && x == "2") {
        convert = convertDM2aFT2(cells[x]);
        row.append("<td>" + convert + "</td>");

        redondeo = redondeoArea(convert);
        row.append("<td>" + redondeo + "</td>");

        cells.push(convert);
        cells.push(redondeo);
      }
    }
    result.push(cells);
    table.append(row);
  }
  $("#reporte").val(result)
  // Insert into DOM
  $("#excel_table").html(table);
}
//Convertir Área de Teseo
function convertDM2aFT2(areaDM) {
  const unidadFt2 = 0.1076391041671;
  return (parseFloat(areaDM) * unidadFt2).toFixed(4);
}
//Redondeo de Área en Base a Criterios
function redondeoArea(areaFT) {
  entero = Math.floor(areaFT);
  console.log("Entero: "+entero);
  redondAreaFT = parseFloat(areaFT).toFixed(2);

  decimales = parseFloat(redondAreaFT - entero).toFixed(2);
  console.log(decimales);
  if (decimales <= 0.19) {
    result = entero;
  }
  if (decimales <= 0.39 && decimales >= 0.2) {
    result = entero + 0.25;
  }
  if (decimales <= 0.69 && decimales >= 0.4) {
    result = entero + 0.5;
  }
  if (decimales <= 0.9 && decimales >= 0.7) {
    result = entero + 0.75;
  }
  if (decimales <= 0.99 && decimales >= 0.91) {
    result = entero + 1;
  }
  return parseFloat(result).toFixed(2);
}
