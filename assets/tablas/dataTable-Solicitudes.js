//console.log(datsJson.data);
$(document).ready(function () {
  var dt = $("#table-solicitudes").DataTable({
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
        data: "n_programa",
      },
      {
        data: "n_proceso",
      },
      {
        data: "n_materia",
      },
      {
        data: "n_empleadoResp",
      },
      {
        data: "descripcion",
      },

      {
        data: null,
        render: function (data, type, row) {
          return ` <div id='bloqueo-btn-2-${data.id}' style='display:none'>
                    <button class='btn btn-TWM btn-xs' type='button' disabled=''>
                        <span class='spinner-border spinner-border-sm' role='status' aria-hidden='true'></span>
                        <span class='sr-only'>Loading...</span>
                        </button>
                   </div>
               <div id='desbloqueo-btn-2-${data.id}'>
                <button class="btn btn-xs btn-success" title="Aceptar Edición" onclick="aceptarEdicion(${data.id})"><i class="fas fa-check"></i></button>
                <button class="btn btn-xs btn-danger" title="Cancelar Edición" onclick="rechazarEdicion(${data.id})" ><i class="fas fa-times"></i></button>
               </div>`;
        },
      },
    ],
  });

  //Add event listener for opening and closing details
  var o = this;
  $("#table-solicitudes tbody").on("click", "td.details-control", function () {
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
    ejecutandoCarga(d.idRendimiento, d.id);
  };
  $(
    ".buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel"
  ).addClass("btn btn-TWM mr-1");
}); // Cierre de document ready
