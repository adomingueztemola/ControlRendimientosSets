var delay = (function () {
    var timer = 0;
    return function (callback, ms) {
      clearTimeout(timer);
      timer = setTimeout(callback, ms);
    };
  })();
  //====================SUMA AL STOCK DE PIEZAS RECUPERADAS=====================================
  $("#sets").on("keyup", () => {
    delay(function () {
        const pzasConSet=4;
        let unidades=pzasConSet*$("#sets").val();
        $("#unidades").val(unidades);
        $("#btn-enviounidades").attr("disabled", false);
        $(".aviso-sobrepase").html("");

        if ($("#disponibilidad").val()<unidades){
            $(".aviso-sobrepase").html("<br>Los set's sobrepasan a las unidades de los lotes.");
            $("#btn-enviounidades").attr("disabled", true);
        }
    }, 1000);
  });
  