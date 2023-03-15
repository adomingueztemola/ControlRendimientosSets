$(document).ready(function () {
  var dt = $("#table-preregistro").DataTable({
    dom: "Bfrltip",
    data: datsJson.data,
    "rowCallback": function( Row, Data) {
      if(Data.multiMateria=='' || Data.multiMateria=='0'){
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
        data: "loteTemola",
      },
    
    
   
      {
        data: null,
        render: function (data, type, row) {
          return "<small>" + data.nPrograma + "</small>";
        },
      },
      {
        data: null,
        render: function (data, type, row) {
          return "<small>" + data.nMateria + "</small>";
        },
      }, 
      {
        data: "n_userRegistro",
      },
      {
        data: "f_fechaReg",
      },

      {
        data: null,
        render: function (data, type, row) {
          return "<button class='btn button btn-danger btn-sm' title='Eliminar Registro de Lote' onclick='eliminarPreRegistro("+data.id+")'><i class='fas fa-trash-alt'></i></button>";
        },
      },
    ],
  });

  //Add event listener for opening and closing details
  var o = this;
  $("#table-preregistro tbody").on("click", "td.details-control", function () {
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
