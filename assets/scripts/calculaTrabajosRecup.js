/********************************************/
/*CALCULO DE PERDIDA DE TRABAJOS DE RECUPERACION*/
/********************************************/
function calculaPerdida(inputInicial, inputFinal) {
  valueTotalInicial =
    $("#" + inputInicial).val() == "" ? "0" : $("#" + inputInicial).val();
  valueTotalRecuperado =
    $("#" + inputFinal).val() == "" ? "0" : $("#" + inputFinal).val();

  valueTotalInicial = parseFloat(valueTotalInicial);
  valueTotalRecuperado = parseFloat(valueTotalRecuperado);

  totalPerdido = valueTotalInicial - valueTotalRecuperado;
  porcentajePerdida = Math.round((100 * totalPerdido) / valueTotalInicial, 2);
  $("#porcentaje-perdida").text(" " + porcentajePerdida + "%");
}
/********************************************/
/*CALCULO DE PERDIDA DE TRABAJOS DE RECUPERACION*/
/********************************************/
function actualizaRangos() {
  valueTotalInicial = parseFloat($("#totalInicial").val());
  //EVALUA LIMITE DE PZAS
  maxTotal = $("#totalInicial").prop("max");
  if (maxTotal < valueTotalInicial) {
    notificaBad("Las piezas máximas de recuperación son: "+maxTotal+"");
  }
  $("#totalRecuperado").prop("max", valueTotalInicial);
}

function cambiaLoteInicial() {
  if ($("#loteRegistrado").is(":checked")) {
    $("#nameLote").attr("hidden", true);
    $("#idRendInicio").next(".select2-container").attr("hidden", false);
    $("#lbl-RendInicio").addClass("required");
    $("#idRendInicio").prop("required", true);
    $("#totalInicial").attr('max', 0);

  } else if ($("#loteNoIdentificado").is(":checked")) {
    $("#nameLote").attr("hidden", false);
    $("#idRendInicio").next(".select2-container").attr("hidden", true);
    $("#lbl-RendInicio").removeClass("required");
    $("#idRendInicio").removeAttr("required");
    $("#totalInicial").attr('max', 10000);

  }
}
