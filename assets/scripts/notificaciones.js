conn();
setInterval("conn()", 20000);
function conn() {
  totalNotifications = 0;
  contentGralAdd = "";
  var jsonSelect = [{}];
  $("#message-notifications").html("");

  $("#loading-notifications").html(
    `<div class="spinner-border spinner-border-sm" role="status"> </div>`
  );

  var jsonProd = [
    {
      label: "Lotes por Capturar para Venta",
      codigo: "capturalotesprod",
    },
   
  ];
  var jsonSup = [
    {
      label: "Solicitudes de Cambios Teseo",
      codigo: "solicitudesteseo",
    },

  ];
  var jsonProg = [
    {
      label: "Ventas Prog. Abastecidas",
      codigo: "ventasprogabast",
    },
  ];

  switch (nivel) {
    case "4":
      jsonSelect = jsonProd;
      break;

    case "9":
      jsonSelect = jsonSup;
      break;

    case "3":
      jsonSelect = jsonProg;
      break;
  }

  for (x of jsonSelect) {
    consultar(x.codigo, x.label);
  }
  setTimeout(() => {
    $("#loading-notifications").html("");
  }, 3000);
}

function doNotificacion(dato, href, label) {
  var d = new Date();

  hour = d.getHours();
  minutes = d.getMinutes();
  if (minutes >= 0 && minutes <= 9) {
    minutes = "0" + minutes;
  }
  contentAdd = `<a href="${href}" class="message-item">
        <span class="btn btn-danger btn-circle">
            ${dato}
        </span>
        <div class="mail-contnet">
            <h5 class="message-title">${label}</h5>
            <span class="mail-desc">Notificado a las ${hour}:${minutes}</span>
        </div>
    </a>`;
  return contentAdd;
}
function consultar(codigo, label) {
  let add = "";
  add = "../".repeat(folder);

  $.post(add + "Controller/alertas.php?op=" + codigo, {}, function (respuesta) {
    var arrayRespuesta = respuesta.split("|");
    if (arrayRespuesta[1] > 0) {
      contentGralAdd = doNotificacion(
        arrayRespuesta[1],
        arrayRespuesta[2],
        label
      );
      totalNotifications = parseInt(totalNotifications) + 1;
      $("#total-notifications").text(totalNotifications);
      $("#total-menu").text(totalNotifications);
      $("#message-notifications").append(contentGralAdd);
    }
  });
}
function abrirDropdown() {
  if ($("#dropdownAlerta").data("toggle") == "dropdown") {
    $("#dropdownAlerta").data("toggle", "");
    $("#dropdownContent").removeClass("show");
  } else {
    $("#dropdownAlerta").data("toggle", "dropdown");
    $("#dropdownContent").addClass("show");
  }
}
