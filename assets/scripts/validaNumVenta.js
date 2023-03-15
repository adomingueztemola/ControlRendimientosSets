var delay = (function () {
  var timer = 0;
  return function (callback, ms) {
    clearTimeout(timer);
    timer = setTimeout(callback, ms);
  };
})();
$("#numFactura").on("keyup", () => {
  delay(function () {
      let numFactura= $("#numFactura").val();
      let idVentaEdita= $("#idVentaEdita").val();

      idVentaEdita= idVentaEdita===undefined?'0':idVentaEdita;

    $.ajax({
      url: "../Controller/ventas.php?op=consultarventa",
      data: {
          numFactura:numFactura,
          idVenta:idVentaEdita

      },
      type: "POST",
      success: function (json) {
        resp = json.split("|");
        if (resp[0] == 1) {
          $("#resultbusq").text(resp[1]);
          $("#resultbusq").removeClass('text-danger')
          $("#resultbusq").addClass('text-success')
          bloqueoBtn("bloqueo-btn-res", 2);
        } else if (resp[0] == 0) {
            $("#resultbusq").text(resp[1]);
            $("#resultbusq").removeClass('text-success')
            $("#resultbusq").addClass('text-danger')
            bloqueoBtn("bloqueo-btn-res", 2);
        }
      },
      beforeSend: function () {
        bloqueoBtn("bloqueo-btn-res", 1);
      },
    });
  }, 1000);
});
