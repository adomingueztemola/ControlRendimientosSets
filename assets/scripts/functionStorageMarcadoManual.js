function pulsar(e, code, lote, loteAct, input) {
  if (e.keyCode === 13 && !e.shiftKey) {
    e.preventDefault();
    value = $(input).val();
    switch (code) {
      case "cantfinal":
        totalAContar = parseFloat(value);

        if (totalAContar < 0) {
          notificaBad("Verifica tu piezas totales a marcar");
        } else {
          $.ajax({
            url: "../Controller/marcadoMano.php?op=" + code,
            data: { lote: lote, value: value },
            type: "POST",
            success: function (json) {
              resp = json.split("|");
              if (resp[0] == 1) {
                notificaSuc(resp[1]);
                setTimeout(() => {
                  cargaContent(loteAct);
                }, 1000);
              } else if (resp[0] == 0) {
                notificaBad(resp[1]);
              }
            },
            beforeSend: function () {},
          });
        }
        break;
      case "corte":
        limit = parseFloat($(input).data("limit"));
        preliminar = parseFloat($(input).data("preliminar"));
        totalAContar = parseFloat(value);
        idLote = parseFloat($(input).data("lote"));

        if (totalAContar < 0 || limit < totalAContar) {
          notificaBad("Verifica tu piezas totales a marcar");
        } else {
          $.ajax({
            url: "../Controller/marcadoMano.php?op=" + code,
            data: { lote: lote, value: value, idLote: idLote },
            type: "POST",
            success: function (json) {
              resp = json.split("|");
              if (resp[0] == 1) {
                notificaSuc(resp[1]);
                setTimeout(() => {
                  cargaContent(loteAct);
                }, 1000);
              } else if (resp[0] == 0) {
                notificaBad(resp[1]);
              }
            },
            beforeSend: function () {},
          });
        }

        break;
      case "decremento":
        limit = parseFloat($(input).data("limit"));
        preliminar = parseFloat($(input).data("preliminar"));
        totalAContar = parseFloat(value);
        idLote = parseFloat($(input).data("lote"));

        if (totalAContar < 0 || preliminar < totalAContar) {
          notificaBad("Verifica tu piezas totales a quitar");
        } else {
          $.ajax({
            url: "../Controller/marcadoMano.php?op=" + code,
            data: { lote: lote, value: value, idLote: idLote },
            type: "POST",
            success: function (json) {
              resp = json.split("|");
              if (resp[0] == 1) {
                notificaSuc(resp[1]);
                setTimeout(() => {
                  cargaContent(loteAct);
                }, 1000);
              } else if (resp[0] == 0) {
                notificaBad(resp[1]);
              }
            },
            beforeSend: function () {},
          });
        }

        break;
      case "ajuste":
        totalAContar = parseFloat(value);
        idLote = parseFloat($(input).data("lote"));

        if (totalAContar < 0) {
          notificaBad("Verifica tu piezas totales a marcar.");
        } else {
          $.ajax({
            url: "../Controller/marcadoMano.php?op=" + code,
            data: { lote: lote, value: value, idLote: idLote },
            type: "POST",
            success: function (json) {
              resp = json.split("|");
              if (resp[0] == 1) {
                notificaSuc(resp[1]);
                setTimeout(() => {
                  cargaContent(loteAct);
                }, 1000);
              } else if (resp[0] == 0) {
                notificaBad(resp[1]);
              }
            },
            beforeSend: function () {},
          });
        }

        break;
      default:
        break;
    }
  }
}
