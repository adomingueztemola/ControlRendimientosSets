var delay = (function () {
  var timer = 0;
  return function (callback, ms) {
    clearTimeout(timer);
    timer = setTimeout(callback, ms);
  };
})();
$("#numFactura").on("keyup", () => {
  delay(function () {
      let idProveedor= $("#proveedor").val();
      let numFactura= $("#numFactura").val();

    $.ajax({
      url: "../Controller/pedidos.php?op=consultarnumfact",
      data: {
          idProveedor:idProveedor,
          numFactura:numFactura,
      },
      type: "POST",
      success: function (json) {
        resp = json.split("|");
        if (resp[0] == 1) {
          $("#resultbusq").text(resp[1]);
          $("#resultbusq").removeClass('text-danger')
          $("#resultbusq").addClass('text-success')
          guardarNumFactura(numFactura)
          bloqueoBtn("bloqueo-btn-res", 2);
        } else if (resp[0] == 0) {
            $("#resultbusq").text(resp[1]);
            $("#resultbusq").removeClass('text-success')
            $("#resultbusq").addClass('text-danger')
            $("#numFactura").val("");
            bloqueoBtn("bloqueo-btn-res", 2);
        }
      },
      beforeSend: function () {
        bloqueoBtn("bloqueo-btn-res", 1);
      },
    });
  }, 1000);
});
