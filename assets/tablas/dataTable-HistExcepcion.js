//console.log(datsJson.data);
$(document).ready(function () {
  var dt = $("#table-historial").DataTable({
    dom: "Bfrltip",
    data: datsJson.data,
    buttons: [
      {
        extend: "copy",
        text: "Copiar Formato",
        exportOptions: {},
        footer: true,
      },
      {
        extend: "excel",
        text: "Excel",
        exportOptions: {},
        footer: true,
      },
      {
        extend: "pdf",
        text: "Archivo PDF",
        exportOptions: {},
        orientation: "landscape",
        footer: true,
      },
      {
        extend: "print",
        text: "Imprimir",
        exportOptions: {},
        footer: true,
      },
    ],
    columns: [
      {
        class: "details-control",
        orderable: false,
        data: null,
        defaultContent:
          '<div class="text-center"><i class="fas fa-plus-circle text-TWM"></i> </div>',
      },
      {
        data: "f_fechaReg",
      },
      {
        data: "semanaProduccion",
      },
      {
        data: "loteTemola",
      },
      {
        data: "c_proceso",
      },
      {
        data: null,
        render: function (data, type, row) {
          return "<small>"+data.n_programa+"</small>";
        },
      },
      {
        data: null,
        render: function (data, type, row) {
          return "<small>"+data.n_materia+"</small>";
        },
      },
      {
        data: null,
        render: function (data, type, row) {
          pzasRecuperadas = Intl.NumberFormat("es-MX", {
            currency: "MXN",
          }).format(data.pzasRecuperadas);
          return pzasRecuperadas;
        },
      },
      {
        data: null,
        render: function (data, type, row) {
          setsEmpacados = Intl.NumberFormat("es-MX", {
            currency: "MXN",
          }).format(data.pzasEmpacadas);
          return setsEmpacados;
        },
      },
      {
        data: "n_empleadoReg",
      },

      {
        data: "descripcion",
      },
      {
        data: "n_empleadoValid",
      },
      {
        data: "f_fechaValida",
      },
      {
        data: null,
        render: function (data, type, row) {
          lblEstado = "";
          switch (data.estado) {
            case "0":
              lblEstado = "<i class='fas fa-times text-danger'></i>Cancelada";
              break;
            case "2":
              lblEstado = "<i class='fas fa-check text-success'></i>Aceptada";
              break;
          }
          return lblEstado;
        },
      },
    ],
  });

  //Add event listener for opening and closing details
  var o = this;
  $("#table-historial tbody").on("click", "td.details-control", function () {
    var tr = $(this).closest("tr");
    var row = dt.row(tr);

    if (row.child.isShown()) {
      // This row is already open - close it
      row.child.hide();
      tr.removeClass("shown");
    } else {
      // Open this row
      row.child(lanzaLoading(row.data())).show();
      tr.addClass("shown");
      row.child(cargaContenido(row.data()));
      //alert("hola");
    }
  });

  var lanzaLoading = function (d) {
    //alert(d.idPyme + " La varGlob ");
    return (
      '<div id="DIV' +
      d.id +
      '" class="col-lg-12 ">' +
      '<div class="spinner-border" text-center role="status"><span class="sr-only">Loading...</span></div>' +
      "</div>"
    );
  };

  var cargaContenido = function (d) {
    ejecutandoCarga(d.id);
  };

}); // Cierre de document ready
