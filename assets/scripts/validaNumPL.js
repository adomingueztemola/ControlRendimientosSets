var delay = (function () {
  var timer = 0;
  return function (callback, ms) {
    clearTimeout(timer);
    timer = setTimeout(callback, ms);
  };
})();
$("#numPL").on("keyup", () => {
  delay(function () {
      let numPL= $("#numPL").val();
      let idVentaEdita= $("#idVentaEdita").val();
      idVentaEdita= idVentaEdita===undefined?'0':idVentaEdita;
    $.ajax({
      url: "../Controller/ventas.php?op=consultarpl",
      data: {
          numPL:numPL,
          idVenta:idVentaEdita

      },
      type: "POST",
      success: function (json) {
        resp = json.split("|");
        if (resp[0] == 1) {
          $("#resultbusq2").text(resp[1]);
          $("#resultbusq2").removeClass('text-danger')
          $("#resultbusq2").addClass('text-success')
          bloqueoBtn("bloqueo-btn-res2", 2);
        } else if (resp[0] == 0) {
            $("#resultbusq2").text(resp[1]);
            $("#resultbusq2").removeClass('text-success')
            $("#resultbusq2").addClass('text-danger')
            bloqueoBtn("bloqueo-btn-res2", 2);
        }
      },
      beforeSend: function () {
        bloqueoBtn("bloqueo-btn-res2", 1);
      },
    });
  }, 1000);
});
