var debug = "1";
function guardarValor(codigo, input, str = false) {
  let value_input = $(input).val();
  if (value_input != "" || codigo=="observaciones") {
    value_input = value_input.replace(",", "");
    if (!str) {
      value_input = parseFloat(value_input);
    }
    if (codigo == "pzasrechazadas" && value_input > 0) {
      $("#divCausaRechazo").attr("hidden", false);
    } else if (codigo == "pzasrechazadas" && value_input <= 0) {
      $("#divCausaRechazo").attr("hidden", true);
    }
    $.ajax({
      url: "../Controller/rendimientoEtiquetas.php?op=" + codigo + "",
      data: {
        value: value_input,
        edicion:edicion
      },
      type: "POST",
      success: function (json) {
        resp = json.split("|");
        if (resp[0] == 1) {
          //    notificaSuc(resp[1], "toastr toast-bottom-left");
        } else if (resp[0] == 0) {
          notificaBad(resp[1], "toastr toast-bottom-left");
        }
      },
      beforeSend: function () {},
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
    } else if (
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
      if($(this).val() < "0"){
        $(this).addClass("border");
        $(this).addClass("border-danger");
        log_result = false;

      }
    });
  return log_result;
}

/// FORMULAS DE RENDIMIENTO


function getPerdidaAreaWBTerminada() {
  let areaWB = parseFloat($("#areaWB").val().replace(",", ""));
  let areaFinal = parseFloat($("#areaFinal").val().replace(",", ""));
  //Validaciones de Nan
  if (areaWB == 0) {
    return 0.0;
  } else {
    return ((areaFinal - areaWB) / areaWB)*100;
  }
}

function getPromedioAreaWB() {
  let areaWB = parseFloat($("#areaWB").val().replace(",", ""));
  let Total = parseFloat($("#Total").val().replace(",", ""));
  //Validaciones de Nan
  if (Total == 0) {
    return 0.0;
  } else {
    return (areaWB / Total);
  }
}

function getAreaPzasRechazo() {
  let promedioAreaWB = parseFloat($("#promedioAreaWB").val().replace(",", ""));
  let piezasRechazadas = parseFloat($("#piezasRechazadas").val().replace(",", ""));
 
    return (promedioAreaWB * piezasRechazadas);
  
}




//Ejecucion de formulas
$(".PerdidaAreaWBTerminada").change(function () {
  let result = getPerdidaAreaWBTerminada().toFixed(2);
  let fto = new Intl.NumberFormat("es-MX").format(result);
  $("#perdidaWBTerminado").val(result);
  guardarValor("perdidawbterminado", $("#perdidaWBTerminado"));
});


$(".PromedioAreaWB").change(function () {
  let result = getPromedioAreaWB().toFixed(2);
  let fto = new Intl.NumberFormat("es-MX").format(result);
  $("#promedioAreaWB").val(result);
  guardarValor("promedioareawb", $("#promedioAreaWB"));
  AreaPzasRechazo();
});

$(".AreaPzasRechazo").change(function () {
  let result = getAreaPzasRechazo().toFixed(2);
  let fto = new Intl.NumberFormat("es-MX").format(result);
  $("#areaPzasRechazo").val(result);
  guardarValor("areapzasrechazo", $("#areaPzasRechazo"));
});

function AreaPzasRechazo(){
  let result = getAreaPzasRechazo().toFixed(2);
  let fto = new Intl.NumberFormat("es-MX").format(result);
  $("#areaPzasRechazo").val(result);
  guardarValor("areapzasrechazo", $("#areaPzasRechazo"));
  getAreaPzasRechazo();
}