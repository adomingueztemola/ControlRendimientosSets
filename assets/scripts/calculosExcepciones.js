var delay = (function () {
  var timer = 0;
  return function (callback, ms) {
    clearTimeout(timer);
    timer = setTimeout(callback, ms);
  };
})();
//====================SUMA AL STOCK DE PIEZAS RECUPERADAS=====================================
$("#pzasRecupExp").on("keyup", () => {
  delay(function () {
    let valorStock = $("#stk-pzasrecuperadas").val() == "" ? "0"  : $("#stk-pzasrecuperadas").val();
    let valorRecupera =$("#pzasRecupExp").val() == "" ? "0" : $("#pzasRecupExp").val();
    let result = parseInt(valorStock) + parseInt(valorRecupera);
    $("#calculo-stockRecupera").text(result);
    $("#txt-setsRecuperados").text(Math.floor(result / 4));
    $("#txt-pzasRestRecuperados").text(result % 4);
    $("#txt-stkRecuPreview").text(result);
    $("#setsEmpacados").attr("max", result);

  }, 1000);
});

//====================SUMA AL STOCK DE PIEZAS EMPACADAS=====================================
$("#setsEmpacados").on("keyup", () => {
  delay(function () {
    let valorStock=$("#stk-setsEmpacados").val() == "" ? "0" : $("#stk-setsEmpacados").val();
    let valorRecupera=$("#setsEmpacados").val() == "" ? "0" : $("#setsEmpacados").val();
    $("#calculo-stockEmpacados").text(
      parseInt(valorStock) + parseInt(valorRecupera)
    );
  }, 1000);
});

