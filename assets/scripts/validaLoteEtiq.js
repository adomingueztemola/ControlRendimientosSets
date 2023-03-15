var delay = (function () {
  var timer = 0;
  return function (callback, ms) {
    clearTimeout(timer);
    timer = setTimeout(callback, ms);
  };
})();
$("#lote").on("keyup", () => {
  delay(function () {
      let lote= $("#lote").val();

    $.ajax({
      url: "../Controller/rendimientoEtiquetas.php?op=consultarlote",
      data: {
          lote:lote
      },
      type: "POST",
      success: function (json) {
        resp = json.split("|");
        if (resp[0] == 1) {
          $("#resultbusq").text(resp[1]);
          $("#resultbusq").removeClass('text-danger')
          $("#resultbusq").addClass('text-success')
          $("#btn-initloteo").attr('disabled', false);

          bloqueoBtn("bloqueo-btn-res", 2);
        } else if (resp[0] == 0) {
            $("#resultbusq").text(resp[1]);
            $("#resultbusq").removeClass('text-success')
            $("#resultbusq").addClass('text-danger')
            bloqueoBtn("bloqueo-btn-res", 2);
            $("#btn-initloteo").attr('disabled', true);

        }
      },
      beforeSend: function () {
        bloqueoBtn("bloqueo-btn-res", 1);
      },
    });
  }, 1000);
});
