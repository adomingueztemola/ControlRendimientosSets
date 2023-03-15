/********** CAMBIO DE LOTE  ***********/
function cambioDeLote(selectLote) {
  v_select = $(selectLote).val();
  array_select = v_select.split("|");
  tipo = array_select[1];
  completedCaja = $("#completedCaja").val();
  
  $("#teseo").text($("#lote option:selected").data("teseo"));
  //console.log("Caja Complet. " + completedCaja);
  //Faltantes de la caja
  _12Cja = $("#_12Caja").val();
  _3Cja = $("#_3Caja").val();
  _6Cja = $("#_6Caja").val();
  _9Cja = $("#_9Caja").val();
  //Pzas Disponibles en el lote
  pza12 = parseInt($("#lote option:selected").data("pza12"));
  pza6 = parseInt($("#lote option:selected").data("pza6"));
  pza9 = parseInt($("#lote option:selected").data("pza9"));
  pza3 = parseInt($("#lote option:selected").data("pza3"));
  /*console.table({
    "pzas 12": pza12,
    "pzas 6": pza6,
    "pzas 9": pza9,
    "pzas 3": pza3,
  });*/

  //ESTANDAR DE PZAS QUE NO COMPLETAN UNA CAJA COMPLETA
  aplicaRemanente = $("#lote option:selected").data("aplicaremanente");
  //VERIFICAR QUE NO ESTE MARCADA LA CASILLA DE REMANENTES
  //console.log("Estatus de Remanente: " + $("#remanente").prop("checked"));
  if ($("#remanente").prop("checked")) {
    activarCajas($("#remanente"));
    return 0;
  }
  //BLOQUEO DE HERRAMIENTA DE REMANENTE EN CASO DE QUE NO SEA UN LOTE REGISTRADO
  if (tipo == "1") {
    $("#div-inptremanente").prop("hidden", false);
  } else {
    $("#div-inptremanente").prop("hidden", true);
  }

  //CASOS EFECTUADOS POR TIPO DE PIEZA A EMPACAR
  switch (tipo) {
    case "1":
    /*  if (aplicaRemanente == "1") {
        //DESBLOQUEAR POR SI SE ALINEARAN PIEZAS
        /*$(".pzas").prop("readonly", false);
        //ACTIVA EDICION DE PIEZAS EN INPUTS
        $("#pzas_12").val(0);
        $("#pzas_06").val(0);
        $("#pzas_09").val(0);
        $("#pzas_03").val(0);
        //CHECA EL BOTON DE REGISTRO DE REMANENTES/BLOQUEO DEL MISMO
        $("#remanente").prop("checked", true);
        activarCajas($("#remanente"));

        $("#remanente").prop("readonly", true);
        //LIBERA MAXIMOS DE LOS INPUTS DE PIEZAS
        $("#pzas_12").prop("max", "");
        $("#pzas_3").prop("max", "");
        $("#pzas_9").prop("max", "");
        $("#pzas_6").prop("max", "");*/
     // } else {
        //LIMITA MAXIMOS DE LOS INPUTS DE PIEZAS
        $("#pzas_12").prop("max", "100");
        $("#pzas_3").prop("max", "100");
        $("#pzas_9").prop("max", "100");
        $("#pzas_6").prop("max", "100");
        if (completedCaja == "1") {
          $(".pzas").prop("readonly", false);
          //NO HAY CAJAS POR TERMINAR DE LLENAR
          /*  $("#pzas_12").val(100);
          $("#pzas_06").val(100);
          $("#pzas_09").val(100);
          $("#pzas_03").val(100);*/
          pzas_12_tipo1 = pza12 > 100 ? 100 : pza12;
          pzas_6_tipo1 = pza6 > 100 ? 100 : pza6;
          pzas_9_tipo1 = pza9 > 100 ? 100 : pza9;
          pzas_3_tipo1 = pza3 > 100 ? 100 : pza3;
          $("#pzas_12").val(pzas_12_tipo1);
          $("#pzas_06").val(pzas_6_tipo1);
          $("#pzas_09").val(pzas_9_tipo1);
          $("#pzas_03").val(pzas_3_tipo1);
          //VERIFICAR QUE SALGA ALMENOS UNA CAJA CON TODO EL LOTE
          arrayPzas = {
            "#pzas_12": pzas_12_tipo1,
            "#pzas_03": pzas_3_tipo1,
            "#pzas_06": pzas_6_tipo1,
            "#pzas_09": pzas_9_tipo1,
          };
          estatusPzas = true;
          arrayMinus = recorridoPzas(arrayPzas);

          if (!estatusPzas) {
            //BLOQUEAR POR SI SE ALINEARAN PIEZAS
            arrayMinus.forEach((element) => {
              $(element).prop("readonly", true);
            });
          } else {
            //BLOQUEAR POR SI SE ALINEARAN PIEZAS
            $(".pzas").prop("readonly", true);
          }
        } else {
          $(".pzas").prop("readonly", false);

        }
     // }

      break;
    case "2": //REMANENTES REGISTRADOS
      //LIMITA MAXIMOS DE LOS INPUTS DE PIEZAS
      $("#pzas_12").prop("max", "100");
      $("#pzas_3").prop("max", "100");
      $("#pzas_9").prop("max", "100");
      $("#pzas_6").prop("max", "100");
      if (completedCaja == "1") {
        //NO HAY CAJAS POR TERMINAR DE LLENAR
        //FILTRADO DE PIEZAS
        pzas_12_tipo2 = pza12 > 100 ? 100 : pza12;
        pzas_6_tipo2 = pza6 > 100 ? 100 : pza6;
        pzas_9_tipo2 = pza9 > 100 ? 100 : pza9;
        pzas_3_tipo2 = pza3 > 100 ? 100 : pza3;
        $("#pzas_12").val(pzas_12_tipo2);
        $("#pzas_06").val(pzas_6_tipo2);
        $("#pzas_09").val(pzas_9_tipo2);
        $("#pzas_03").val(pzas_3_tipo2);
        //DESBLOQUEAR POR SI SE ALINEARAN PIEZAS
        $(".pzas").prop("readonly", false);
      } else {
        $(".pzas").prop("readonly", false);

      }
      break;
    case "3": //RECUPERADOS REGISTRADOS
      //LIMITA MAXIMOS DE LOS INPUTS DE PIEZAS
      $("#pzas_12").prop("max", "100");
      $("#pzas_3").prop("max", "100");
      $("#pzas_9").prop("max", "100");
      $("#pzas_6").prop("max", "100");
      if (completedCaja == "1") {
        //NO HAY CAJAS POR TERMINAR DE LLENAR
        //FILTRADO DE PIEZAS
        pzas_12_tipo2 = pza12 > 100 ? 100 : pza12;
        pzas_6_tipo2 = pza6 > 100 ? 100 : pza6;
        pzas_9_tipo2 = pza9 > 100 ? 100 : pza9;
        pzas_3_tipo2 = pza3 > 100 ? 100 : pza3;
        $("#pzas_12").val(pzas_12_tipo2);
        $("#pzas_06").val(pzas_6_tipo2);
        $("#pzas_09").val(pzas_9_tipo2);
        $("#pzas_03").val(pzas_3_tipo2);
        //DESBLOQUEAR POR SI SE ALINEARAN PIEZAS
        $(".pzas").prop("readonly", false);
      }else{
        $(".pzas").prop("readonly", false);

      }
      break;
    default:
      break;
  }
}

/********** BLOQUEA SELECTOR DE CAJA ***********/
function activarCajas(inptrem) {
  let result;
  //VALIDA QUE EL LOTE TENGA ALGUN SELECCIONADO
  if ($("#lote").val() == "") {
    notificaBad("Selecciona un lote, para iniciar el registro de remanente.");
    return 0; //finaliza funciòn de desactivacion de cajas para remanentes
  }
  /*******************************************/
  if ($(inptrem).prop("checked")) {
    result = true;
    //AGREGAR REMANENTE POR PIEZA A CADA CELDA
    pzas_12Rem = $("#lote option:selected").data("pza12");
    pzas_06Rem = $("#lote option:selected").data("pza6");
    pzas_09Rem = $("#lote option:selected").data("pza9");
    pzas_03Rem = $("#lote option:selected").data("pza3");
    //CAMBIA MINIMOS & MAXIMOS DE LAS CASILLAS
    $("#pzas_12").attr("min", "0");
    $("#pzas_12").attr("max", pzas_12Rem);

    $("#pzas_06").attr("min", "0");
    $("#pzas_06").attr("max", pzas_06Rem);

    $("#pzas_09").attr("min", "0");
    $("#pzas_09").attr("max", pzas_09Rem);

    $("#pzas_03").attr("min", "0");
    $("#pzas_03").attr("max", pzas_03Rem);
    //MUESTRA EL TOTAL QUE SE TIENE
   /* console.log(
      "Sobrante Actual: " + $("#lote option:selected").data("remanenteact")
    );
    console.log("Sobrante Detalle del Sobrante: ");
    console.table({
      "12.00": pzas_12Rem,
      "3.00": pzas_03Rem,
      "6.00": pzas_06Rem,
      "9.00": pzas_09Rem,
    });*/
    //RECORREMOS LAS CAJAS PARA PODER DETERMINAR SI LAS PIEZAS ESTAN NULAS
    arrayPzas = {
      "#pzas_12": pzas_12Rem,
      "#pzas_03": pzas_03Rem,
      "#pzas_06": pzas_06Rem,
      "#pzas_09": pzas_09Rem,
    };
    arrayMinus = recorridoPzas(arrayPzas);
    //BLOQUEAR POR SI SE ALINEARAN PIEZAS
    $(".pzas").prop("readonly", false);

    arrayMinus.forEach((element) => {
      $(element).prop("readonly", true);
    });

    $("#pzas_12").val(pzas_12Rem);
    $("#pzas_06").val(pzas_06Rem);
    $("#pzas_09").val(pzas_09Rem);
    $("#pzas_03").val(pzas_03Rem);

    $("#remanenteAct-txt").text(
      $("#lote option:selected").data("remanenteact")
    );
    //DESBLOQUEO DE INPUTS DE PIEZAS & PONE EL VALOR 0
    // $(".pzas").prop("readonly", false);
    // $(".pzas").val("0");
    //MUESTRA LA VENTANA DE RESTANTE EN STOCK
    $("#div-caja").prop("hidden", true);
    $("#div-remanente").prop("hidden", false);
  } else {
    result = false;
    cambioDeLote($("#lote"));
    $(".pzas").prop("readonly", true);

    //MUESTRA LA VENTANA DE RESTANTE EN STOCK
    $("#div-caja").prop("hidden", false);
    $("#div-remanente").prop("hidden", true);
  }
  return result;
}

/**************** RECORRE PIEZAS PARA ENCONTRAR PIEZAS NULAS**********************/
function recorridoPzas(arrayPzas) {
  arrayMinus = [];
  //RECTIFICA QUE LOS VALORES SON >100
  $.each(arrayPzas, function (index, value) {
    if (value <= 0) {
      estatusPzas = false;
      arrayMinus.push(index);
    }
  });
  return arrayMinus;
}
