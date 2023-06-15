var debug = "1";
function guardarValor(codigo, input, str = false) {
  let value_input = $(input).val();
  if (value_input != "" && $(input).length) {
    value_input = value_input.replace(",", "");
    if (!str) {
      value_input = parseFloat(value_input);
    }
    if (codigo == "pzasrechazadas" && value_input > 0) {
      $("#divCausaRechazo").attr("hidden", false);
    } else if (codigo == "pzasrechazadas" && value_input <= 0) {
      $("#divCausaRechazo").attr("hidden", true);
    }
    if (codigo == "fechaempaque") {
      setSemanaInput("fechaEmpaque", "semanaProduccion");
      $("#semanaProduccion").change();
    }
    $.ajax({
      url: "../Controller/rendimiento.php?op=" + codigo + "",
      data: {
        value: value_input,
      },
      type: "POST",
      success: function (json) {
        resp = json.split("|");
        if (resp[0] == 1) {
          $("#success-" + codigo).attr("hidden", false);
          if(codigo=='setsempacados' && value_input>0 ){
            $("#btn-finalizarYield").prop("hidden", false);
          }
          else   if(codigo=='setsempacados' && value_input<=0 ){
            $("#btn-finalizarYield").prop("hidden", true);
          }
          if(validaCamposLlenos()){
            $("#btn-finalizarYield").prop("hidden", false);
          }else{
            $("#btn-finalizarYield").prop("hidden", true);

          }
        } else if (resp[0] == 0) {
          notificaBad(resp[1], "toastr toast-bottom-left");
          $("#success-" + codigo).attr("hidden", true);
        }
      },
      beforeSend: function () {
        $("#success-" + codigo).attr("hidden", true);
      },
    });
  }
}

///VALIDA CAMPOS LLENOS

function validaCamposLlenos() {
  log_result = true;

  $(".Validate").each(function () {
    str_id = $(this).prop("id");
    if (
      str_id == "comentariosrechazo" &&
      $("#piezasRechazadas").val() > 0 &&
      $(this).val() == ""
    ) {
      $(this).addClass("border");
      $(this).addClass("border-danger");
      log_result = false;
    } else if ($(this).val() == "" && str_id != "comentariosrechazo") {
      $(this).addClass("border");
      $(this).addClass("border-danger");
      log_result = false;
    }else if (Number($(this).val()) == "0" && str_id == "areaCrust") {
      $(this).addClass("border");
      $(this).addClass("border-danger");
      log_result = false;
    } else if (Number($(this).val()) == "0" && str_id == "areaWBRecibida") {
      $(this).addClass("border");
      $(this).addClass("border-danger");
      log_result = false;
    }  else if (
      str_id == "comentariosrechazo" &&
      $("#piezasRechazadas").val() > 0 &&
      $(this).val() != ""
    ) {
      $(this).removeClass("border");
      $(this).removeClass("border-danger");
    } else {
      $(this).removeClass("border");
      $(this).removeClass("border-danger");
    }
  });
  //Aprobacion de Positivos y negativos
  $(".Positivos").each(function () {
    if ($(this).val() < "0") {
      $(this).addClass("border");
      $(this).addClass("border-danger");
      log_result = false;
    }
  });

  return log_result;
}

/// FORMULAS DE RENDIMIENTO
function getAreaPzasRechazo() {
  if ($("#promArea").length && $("#piezasRechazadas").length) {
    let promAreaWB = parseFloat($("#promArea").val().replace(",", ""));
    let pzasRechazadas = parseFloat(
      $("#piezasRechazadas").val().replace(",", "")
    );
    return promAreaWB * pzasRechazadas;
  }
  return 0;
}

function getTotalRecorte() {
  if ($("#recorteWB").length && $("#recorteCrust").length) {
    let recorteWB = parseFloat($("#recorteWB").val().replace(",", ""));
    let recorteCrust = parseFloat($("#recorteCrust").val().replace(",", ""));
    return recorteWB + recorteCrust;
  }
  return 0;
}

function getPerdidaWBCrust() {
  if ($("#areaWBRecibida").length && $("#areaCrust").length) {
    let areaWBRecibida = parseFloat(
      $("#areaWBRecibida").val().replace(",", "")
    );
    let areaCrust = parseFloat($("#areaCrust").val().replace(",", ""));
    //Validaciones de Nan
    if (areaWBRecibida == 0) {
      return 0.0;
    } else {
      return ((areaCrust - areaWBRecibida) / areaWBRecibida) * 100;
    }
  }
  return 0;
}

function getPerdidaCrustTeseo() {
  if ($("#areaFinalTeseo").length && $("#areaCrust").length) {
    let areaFinalTeseo = parseFloat(
      $("#areaFinalTeseo").val().replace(",", "")
    );
    let areaCrust = parseFloat($("#areaCrust").val().replace(",", ""));
    /*console.table({
    "Area Final Teseo": areaFinalTeseo,
    "Area Crust": areaCrust,
    "Resta":(areaFinalTeseo - areaCrust),
    "Division":(areaFinalTeseo - areaCrust) / areaCrust,
    "Result": ((areaFinalTeseo - areaCrust) / areaCrust)*100
  })*/
    //Validaciones de Nan
    if (areaFinalTeseo == 0) {
      return 0.0;
    } else {
      return ((areaFinalTeseo - areaCrust) / areaCrust) * 100;
    }
  }
  return 0;
}

function getSetCutTeseo() {
  if ($("#pzasCutTeseo").length) {
    let pzasCutTeseo = parseFloat($("#pzasCutTeseo").val().replace(",", ""));
    /*if (globalThis.debug == "1") {
    console.table({ Piezas: pzasCutTeseo });
  }*/
    return pzasCutTeseo / 4;
  }
  return 0;
}

function getYieldFinalReal() {
  if ($("#areaNeta").length && $("#areaWBXSet").length) {
    let areaNeta = parseFloat($("#areaNeta").val().replace(",", ""));
    let areaWBXSet = parseFloat($("#areaWBXSet").val().replace(",", ""));
    //console.table({ areaNeta: areaNeta, "area WB x set": areaWBXSet });
    //Validaciones de Nan
    if (areaWBXSet == 0) {
      return 0.0;
    } else {
      return (areaNeta / areaWBXSet) * 100;
    }
  }
  return 0;
}

function getSetsRechazados() {
  if ($("#setCutTeseo").length && $("#setsEmpacados").length) {
    let setCutTeseo = parseFloat($("#setCutTeseo").val().replace(",", ""));
    let setsEmpacados = parseFloat($("#setsEmpacados").val().replace(",", ""));

    return setCutTeseo - setsEmpacados;
  }
  return 0;
}

function getPorcRechazoIni() {
  if ($("#setCutTeseo").length && $("#setsRechazados").length) {
    let setsRechazados = parseFloat(
      $("#setsRechazados").val().replace(",", "")
    );
    let setCutTeseo = parseFloat($("#setCutTeseo").val().replace(",", ""));
    //Validaciones de Nan
    if (setCutTeseo == 0) {
      return 0.0;
    } else {
      return (setsRechazados / setCutTeseo) * 100;
    }
  }
  return 0;
}
function getSetsRecuperados() {
  if ($("#pzasRecuperadas").length) {
    let pzasRecuperadas = parseFloat(
      $("#pzasRecuperadas").val().replace(",", "")
    );
    /*if (globalThis.debug == "1") {
    console.table({ "pzas Recuperados": pzasRecuperadas });
  }*/
    return pzasRecuperadas / 4;
  }
  return 0;
}

function getPorcRecuperacion() {
  if ($("#setCutTeseo").length && $("#setsRecuperados").length) {
    let setsRecuperados = parseFloat(
      $("#setsRecuperados").val().replace(",", "")
    );
    let setCutTeseo = parseFloat($("#setCutTeseo").val().replace(",", ""));
    /*console.table({
    "sets Recuperados": setsRecuperados,
    "sets Cortados Teseo": setCutTeseo,
    "Result":  (setsRecuperados / setCutTeseo)*100,

  });*/

    //Validaciones de Nan
    if (setCutTeseo == 0) {
      return 0.0;
    } else {
      return (setsRecuperados / setCutTeseo) * 100;
    }
  }
  return 0;
}

function getPorcFinRechazo() {
  if ($("#setCutTeseo").length && $("#setsRechazados").length) {
    let setsRechazados = parseFloat(
      $("#setsRechazados").val().replace(",", "")
    );
    let setCutTeseo = parseFloat($("#setCutTeseo").val().replace(",", ""));
    //Validaciones de Nan
    if (setCutTeseo == 0) {
      return 0.0;
    } else {
      return (setsRechazados / setCutTeseo) * 100;
    }
  }
  return 0;
}

function getAreaCrustXSet() {
  if ($("#setsEmpacados").length && $("#areaCrust").length) {
    let areaCrust = parseFloat($("#areaCrust").val().replace(",", ""));
    let setsEmpacados = parseFloat($("#setsEmpacados").val().replace(",", ""));
    /* console.table({
    "Area Crust X Set": areaCrust,
    "Sets Empacados": setsEmpacados,
  });*/
    //Validaciones de Nan
    if (setsEmpacados == 0) {
      return 0.0;
    } else {
      return areaCrust / setsEmpacados;
    }
  }
  return 0;
}

function getAreaWBXSet() {
  if ($("#setsEmpacados").length && $("#areaWBRecibida").length) {
    let areaWBRecibida = parseFloat(
      $("#areaWBRecibida").val().replace(",", "")
    );
    let setsEmpacados = parseFloat($("#setsEmpacados").val().replace(",", ""));
    //Validaciones de Nan
    if (setsEmpacados == 0) {
      return 0.0;
    } else {
      return areaWBRecibida / setsEmpacados;
    }
  }
  return 0;
}

function getAreaWBaTerminado() {
  if ($("#areaFinalTeseo").length && $("#areaWBRecibida").length) {
    let areaWBRecibida = parseFloat(
      $("#areaWBRecibida").val().replace(",", "")
    );
    let areaFinalTeseo = parseFloat(
      $("#areaFinalTeseo").val().replace(",", "")
    );
    //Validaciones de Nan
    if (areaWBRecibida == 0) {
      return 0.0;
    } else {
      return ((areaFinalTeseo - areaWBRecibida) / areaWBRecibida) * 100;
    }
  }
  return 0;
}

//Ejecucion de formulas
$(".AreaPzasRechazo").change(function () {
  let result = getAreaPzasRechazo().toFixed(2);
  let fto = new Intl.NumberFormat("es-MX").format(result);
  $("#areaPzasRechazadas").val(result);
  guardarValor("areapzasrechazadas", $("#areaPzasRechazadas"));
});

$(".TotalRecorte").change(function () {
  let result = getTotalRecorte().toFixed(2);
  let fto = new Intl.NumberFormat("es-MX").format(result);
  $("#totalRecorte").val(result);
  guardarValor("totalrecorte", $("#totalRecorte"));
});

$(".PerdidaWBCrust").change(function () {
  let result = getPerdidaWBCrust().toFixed(2);
  let fto = new Intl.NumberFormat("es-MX").format(result);
  $("#perdidaWBCrust").val(result);
  guardarValor("perdidawbcrust", $("#perdidaWBCrust"));
});

$(".PerdidaCrustTeseo").change(function () {
  let result = getPerdidaCrustTeseo().toFixed(2);
  let fto = new Intl.NumberFormat("es-MX").format(result);
  $("#perdidaCrustTeseo").val(result);
  guardarValor("perdidacrustteseo", $("#perdidaCrustTeseo"));
});

$(".SetCutTeseo").change(function () {
  let result = getSetCutTeseo().toFixed(0);
  let fto = new Intl.NumberFormat("es-MX").format(result);
  $("#setCutTeseo").val(result);
  //getPorcRecuperacion()
  $(".PorcRecuperacion").change();
  PorcFinRechazo();

  guardarValor("setcutteseo", $("#setCutTeseo"));
});
function YieldFinalReal() {
  let result = getYieldFinalReal().toFixed(2);
  //console.log(result);
  let fto = new Intl.NumberFormat("es-MX").format(result);
  $("#yieldFinalReal").val(result);
  guardarValor("yieldfinalreal", $("#yieldFinalReal"));
}

$(".SetsRechazados").change(function () {
  if ($("#tipoProceso").val() == "1") {
    let result = getSetsRechazados().toFixed(0);
    let fto = new Intl.NumberFormat("es-MX").format(result);
    $("#setsRechazados").val(result);
    if ($("#tipoProceso").val() == "1") {
      PorcFinRechazo();
    }

    guardarValor("setsrechazados", $("#setsRechazados"));
  }
});

$(".AreaWBaTerminado").change(function () {
  let result = getAreaWBaTerminado().toFixed(2);
  let fto = new Intl.NumberFormat("es-MX").format(result);
  $("#areaWBaTerminado").val(result);
  guardarValor("areawbterminado", $("#areaWBaTerminado"));
});

$(".PorcRechazoIni").change(function () {
  if ($("#tipoProceso").val() == "1") {
    let result = getPorcRechazoIni().toFixed(2);
    let fto = new Intl.NumberFormat("es-MX").format(result);
    $("#porcRechazoIni").val(result);
    guardarValor("porcrechazoini", $("#porcRechazoIni"));
  }
});

$(".SetsRecuperados").change(function () {
  let result = getSetsRecuperados().toFixed(2);
  let fto = new Intl.NumberFormat("es-MX").format(result);
  $("#setsRecuperados").val(result);
  //getPorcRecuperacion()
  $(".PorcRecuperacion").change();

  guardarValor("setsrecuperados", $("#setsRecuperados"));
});

$(".PorcRecuperacion").change(function PorcRecuperacion() {
  let result = getPorcRecuperacion().toFixed(2);
  let fto = new Intl.NumberFormat("es-MX").format(result);
  $("#porcRecuperacion").val(result);
  // console.log(result);
  guardarValor("porcrecuperacion", $("#porcRecuperacion"));
});

function PorcFinRechazo() {
  let result = getPorcFinRechazo().toFixed(2);
  let fto = new Intl.NumberFormat("es-MX").format(result);
  $("#porcFinRechazo").val(result);
  guardarValor("porcfinrechazo", $("#porcFinRechazo"));
}

$(".AreaCrustXSet").change(function () {
  let result = getAreaCrustXSet().toFixed(2);
  let fto = new Intl.NumberFormat("es-MX").format(result);
  $("#areaCrustXSet").val(result);
  guardarValor("areacrustxset", $("#areaCrustXSet"));
});

$(".AreaWBXSet").change(function () {
  let result = getAreaWBXSet().toFixed(2);
  let fto = new Intl.NumberFormat("es-MX").format(result);
  $("#areaWBXSet").val(result);
  if ($("#tipoProceso").val() == "1") {
    YieldFinalReal();
  }

  guardarValor("areawbxset", $("#areaWBXSet"));
});
