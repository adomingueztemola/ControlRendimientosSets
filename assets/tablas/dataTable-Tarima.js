$(document).ready(function () {
  var dt = $("#table-tarimas").DataTable({
    dom: "Bfrltip",
    data: datsJson.data,
    rowCallback: function (Row, Data) {
      if (Data.multiMateria == "" || Data.multiMateria == "0") {
        $(Row).addClass("table-info");
      }
    },
    columns: [
      {
        class: "details-control",
        orderable: false,
        data: null,
        defaultContent:
          '<div class="text-center"><i class="fas fa-plus-circle text-TWM"></i> </div>',
      },
      {
        data: "folio",
      },
      {
        data: null,
        render: function (data, type, row) {
          return "<small>" + data.programas + "</small>";
        },
      },
      {
        data: null,
        render: function (data, type, row) {
          return "<small>" + data.semanas + "</small>";
        },
      },
      {
        data: null,
        render: function (data, type, row) {
          return "<small>" + data.lotes + "</small>";
        },
      },
      {
        data: "fFechaSalida",
      },
      {
        data: null,
        render: function (data, type, row) {
          return new Intl.NumberFormat('es-MX').format(data._12);
        },
      },
      {
        data: null,
        render: function (data, type, row) {
          return new Intl.NumberFormat('es-MX').format(data._3);
        },
      },
      {
        data: null,
        render: function (data, type, row) {
          return new Intl.NumberFormat('es-MX').format(data._6);
        },
      },
      {
        data: null,
        render: function (data, type, row) {
          return new Intl.NumberFormat('es-MX').format(data._9);
        },
      },
      {
        data: null,
        render: function (data, type, row) {
          return new Intl.NumberFormat('es-MX').format(data.totalPzas);
        },
      },
      {
        data: null,
        render: function (data, type, row) {
          return (
            "<button class='btn button btn-info btn-sm'  onclick='abrirNuevoTab(\"../PDFReportes/Controller/EtiquetaTarima.php?op=gettarima&data="+data.id+"\")  '  title='Imprimir Etiqueta de Tarima'><i class='fas fa-print'></i></button>"
          );
        },
      },
    ],
  });

  //Add event listener for opening and closing details
  var o = this;
  $("#table-tarimas tbody").on("click", "td.details-control", function () {
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
