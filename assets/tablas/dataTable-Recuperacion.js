//console.log(datsJson.data);
$(document).ready(function () {
  var dt = $("#table-inventario").DataTable({
    dom: "Bfrltip",
    rowCallback: function (row, data) {
      $($(row).find("td")[15]).addClass("table-danger");
      $($(row).find("td")[16]).addClass("table-danger");
    },
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
        data: "loteTemola",
      },
      {
        data: "semanaAnio",
      },
      {
        data: null,
        render: function (data, type, row) {
          return data.c_proceso;
        },
      },
      {
        data: null,
        render: function (data, type, row) {
          return "<small>" + data.n_programa + "</small>";
        },
      },
      {
        data: null,
        render: function (data, type, row) {
          return "<small>" + data.n_materia + "</small>";
        },
      },
      {
        data: null,
        render: function (data, type, row) {
          totalInvRecu = Intl.NumberFormat("es-MX", { currency: "MXN" }).format(
            data.totalInvRecu
          );
          return totalInvRecu;
        },
      },
      {
        data: null,
        render: function (data, type, row) {
          setsInvRecu = Intl.NumberFormat("es-MX", { currency: "MXN" }).format(
            data.setsInvRecu
          );
          return setsInvRecu;
        },
      },

      {
        data: null,
        render: function (data, type, row) {
          porcRecuperacion = Intl.NumberFormat("es-MX", {
            currency: "MXN",
          }).format(data.porcRecuperacion);
          return porcRecuperacion + "%";
        },
      },

      
      {
        data: null,
        render: function (data, type, row) {
          porcRecuperacionFinal = Intl.NumberFormat("es-MX", {
            currency: "MXN",
          }).format(data.porcRecuperacionFinal);
          return porcRecuperacionFinal + "%";
        },
      },

      {
        data: null,
        render: function (data, type, row) {
          _12Recu = Intl.NumberFormat("es-MX", {
            currency: "MXN",
          }).format(data._12Recu);
          return _12Recu;
        },
      },
      {
        data: null,
        render: function (data, type, row) {
          _3Recu = Intl.NumberFormat("es-MX", {
            currency: "MXN",
          }).format(data._3Recu);
          return _3Recu;
        },
      },
      {
        data: null,
        render: function (data, type, row) {
          _6Recu = Intl.NumberFormat("es-MX", {
            currency: "MXN",
          }).format(data._6Recu);
          return _6Recu;
        },
      },
      {
        data: null,
        render: function (data, type, row) {
          _9Recu = Intl.NumberFormat("es-MX", {
            currency: "MXN",
          }).format(data._9Recu);
          return _9Recu;
        },
      },


      {
        data: null,
        render: function (data, type, row) {
          totalRecuperado = Intl.NumberFormat("es-MX", {
            currency: "MXN",
          }).format(data.totalRecuperado);
          return totalRecuperado ;
        },
      },
      {
        data: null,
        render: function (data, type, row) {
          porcLimitRecup = Intl.NumberFormat("es-MX", {
            currency: "MXN",
          }).format(data.porcLimitRecup);
          progressBar=`<div class='progress'><div class='progress-bar bg-success wow animated progress-animated' 
          style='width: ${data.porcComplet}%; height:6px;' role='progressbar'></div><div>
          `;
          return porcLimitRecup + "%"+progressBar;
        },
      },
      {
        data: null,
        render: function (data, type, row) {
          pzasLimitRecup = Intl.NumberFormat("es-MX", {
            currency: "MXN",
          }).format(data.pzasLimitRecup);
          return pzasLimitRecup;
        },
      },
      {
        data: null,
        render: function (data, type, row) {
          disponible= parseInt(data.pzasLimitRecup-data.totalRecuperado)
          disponible = Intl.NumberFormat("es-MX", {
            currency: "MXN",
          }).format(disponible);
          return disponible;
        },
      },
      {
        data: null,
        render: function (data, type, row) {
          totalRecu = Intl.NumberFormat("es-MX", { currency: "MXN" }).format(
            data.totalRecu
          );
          return totalRecu;
        },
      },
    ],
  });

  //Add event listener for opening and closing details
  var o = this;
  $("#table-inventario tbody").on("click", "td.details-control", function () {
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
  $(
    ".buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel"
  ).addClass("btn btn-TWM mr-1");
}); // Cierre de document ready
