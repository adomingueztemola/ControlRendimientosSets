/*************************************************
 * 1. OPCIONES DE PROGRAMA DE SETS
/*************************************************/
$(".ProgramaSetsFilter").select2({
  placeholder: "Selecciona un programa",
  allowClear: true,

  ajax: {
    url: "../Controller/programas.php?op=select2programassets",
    type: "post",
    dataType: "json",
    delay: 250,
    data: function (params) {
      return {
        palabraClave: params.term, // search term
      };
    },
    processResults: function (response) {
      return {
        results: response,
      };
    },
    cache: true,
  },
});

/*************************************************
 * 2. OPCIONES DE PROCESOS DE SECADO
/*************************************************/
$(".ProcesosFilter").select2({
  placeholder: "Selecciona un proceso",
  allowClear: true,

  ajax: {
    url: "../Controller/procesosSecado.php?op=select2procesos",
    type: "post",
    dataType: "json",
    delay: 250,
    data: function (params) {
      return {
        palabraClave: params.term, // search term
      };
    },
    processResults: function (response) {
      return {
        results: response,
      };
    },
    cache: true,
  },
});

/*************************************************
 * 3. OPCIONES DE PROGRAMAS GENERALES
/*************************************************/
$(".ProgramasFilter").select2({
  placeholder: "Selecciona un programa",
  allowClear: true,

  ajax: {
    url: "../Controller/programas.php?op=select2programas",
    type: "post",
    dataType: "json",
    delay: 250,
    data: function (params) {
      return {
        palabraClave: params.term, // search term
      };
    },
    processResults: function (data) {
      //Recorre JSON para generar option group de areas
      textOpt = "";
      jsonOpt = [];
      childrenOpt = [];
      data.forEach((element) => {
        hijoOpt = {};
        if (textOpt != element.nTipo) {
          //Agrega a jsonOpt
          if (textOpt != "" && childrenOpt.length > 0) {
            jsonOpt.push({
              text: textOpt,
              children: childrenOpt,
              element: HTMLOptGroupElement,
            });
          }
          /*********************/
          childrenOpt = [];
          //Agrega su hijito
          hijoOpt.id = element.id;
          hijoOpt.text = element.nombre;
          hijoOpt.element = HTMLOptionElement;
          childrenOpt.push(hijoOpt); //Agraga children OPT
        } else {
          //Agrega su hijito
          hijoOpt.id = element.id;
          hijoOpt.text = element.nombre;
          hijoOpt.element = HTMLOptionElement;
          childrenOpt.push(hijoOpt); //Agraga children OPT
        }
        textOpt = element.nTipo;
      });
      //Agrega a jsonOpt
      if (textOpt != "" && childrenOpt.length > 0) {
        jsonOpt.push({
          text: textOpt,
          children: childrenOpt,
          element: HTMLOptGroupElement,
        });
      }
      return {
        results: jsonOpt,
      };
    },
    cache: true,
  },
});

/*************************************************
 * 4. OPCIONES DE LOTES PADRES
/*************************************************/
$(".LotePadresFilter").select2({
  placeholder: "Selecciona un lote",
  allowClear: true,

  ajax: {
    url: "../Controller/rendimiento.php?op=select2lotespadres",
    type: "post",
    dataType: "json",
    delay: 250,
    data: function (params) {
      return {
        palabraClave: params.term, // search term
      };
    },
    processResults: function (data) {
      //Recorre JSON para generar option group de areas
      textOpt = "";
      jsonOpt = [];
      childrenOpt = [];
      data.forEach((element) => {
        hijoOpt = {};
        if (textOpt != element.nPrograma) {
          //Agrega a jsonOpt
          if (textOpt != "" && childrenOpt.length > 0) {
            jsonOpt.push({
              text: textOpt,
              children: childrenOpt,
              element: HTMLOptGroupElement,
            });
          }
          /*********************/
          childrenOpt = [];
          //Agrega su hijito
          hijoOpt.id = element.id;
          hijoOpt.text = element.loteTemola;
          hijoOpt.element = HTMLOptionElement;
          childrenOpt.push(hijoOpt); //Agraga children OPT
        } else {
          //Agrega su hijito
          hijoOpt.id = element.id;
          hijoOpt.text = element.loteTemola;
          hijoOpt.element = HTMLOptionElement;
          childrenOpt.push(hijoOpt); //Agraga children OPT
        }
        textOpt = element.nPrograma;
      });
      //Agrega a jsonOpt
      if (textOpt != "" && childrenOpt.length > 0) {
        jsonOpt.push({
          text: textOpt,
          children: childrenOpt,
          element: HTMLOptGroupElement,
        });
      }
      return {
        results: jsonOpt,
      };
    },
    cache: true,
  },
});

/*************************************************
 * 5. OPCIONES DE SEMANAS DE PRODUCCION
/*************************************************/
$(".SemanaLotesFilter").select2({
  placeholder: "Selecciona una semana",
  allowClear: true,

  ajax: {
    url: "../Controller/rendimiento.php?op=select2semanalotes",
    type: "post",
    dataType: "json",
    delay: 250,
    data: function (params) {
      return {
        palabraClave: params.term, // search term
      };
    },
    processResults: function (response) {
      return {
        results: response,
      };
    },
    cache: true,
  },
});

/*************************************************
 * 6. OPCIONES DE LOTES PARA TESEO
/*************************************************/
$(".LoteTeseoFilter").select2({
  placeholder: "Selecciona un lote",
  allowClear: true,

  ajax: {
    url: "../Controller/rendimiento.php?op=select2lotessets",
    type: "post",
    dataType: "json",
    delay: 250,
    data: function (params) {
      return {
        palabraClave: params.term, // search term
      };
    },
    processResults: function (data) {
      //Recorre JSON para generar option group de areas
      textOpt = "";
      jsonOpt = [];
      childrenOpt = [];
      data.forEach((element) => {
        hijoOpt = {};
        if (textOpt != element.nPrograma) {
          //Agrega a jsonOpt
          if (textOpt != "" && childrenOpt.length > 0) {
            jsonOpt.push({
              text: textOpt,
              children: childrenOpt,
              element: HTMLOptGroupElement,
            });
          }
          /*********************/
          childrenOpt = [];
          //Agrega su hijito
          hijoOpt.id = element.id;
          hijoOpt.text = element.loteTemola;
          hijoOpt.element = HTMLOptionElement;
          childrenOpt.push(hijoOpt); //Agraga children OPT
        } else {
          //Agrega su hijito
          hijoOpt.id = element.id;
          hijoOpt.text = element.loteTemola;
          hijoOpt.element = HTMLOptionElement;
          childrenOpt.push(hijoOpt); //Agraga children OPT
        }
        textOpt = element.nPrograma;
      });
      //Agrega a jsonOpt
      if (textOpt != "" && childrenOpt.length > 0) {
        jsonOpt.push({
          text: textOpt,
          children: childrenOpt,
          element: HTMLOptGroupElement,
        });
      }
      return {
        results: jsonOpt,
      };
    },
    cache: true,
  },
});
/*************************************************
 * 7. OPCIONES DE LOTES ABIERTOS PARA PODER MODIFICAR
/*************************************************/
$(".LotesAbiertosFilter").select2({
  placeholder: "Selecciona un lote",
  allowClear: true,

  ajax: {
    url: "../Controller/rendimiento.php?op=select2lotesopen",
    type: "post",
    dataType: "json",
    delay: 250,
    data: function (params) {
      return {
        palabraClave: params.term, // search term
      };
    },
    processResults: function (data) {
      //Recorre JSON para generar option group de areas
      textOpt = "";
      jsonOpt = [];
      childrenOpt = [];
      data.forEach((element) => {
        hijoOpt = {};
        if (textOpt != element.nPrograma) {
          //Agrega a jsonOpt
          if (textOpt != "" && childrenOpt.length > 0) {
            jsonOpt.push({
              text: textOpt,
              children: childrenOpt,
              element: HTMLOptGroupElement,
            });
          }
          /*********************/
          childrenOpt = [];
          //Agrega su hijito
          hijoOpt.id = element.id;
          hijoOpt.text = element.loteTemola;
          hijoOpt.element = HTMLOptionElement;
          childrenOpt.push(hijoOpt); //Agraga children OPT
        } else {
          //Agrega su hijito
          hijoOpt.id = element.id;
          hijoOpt.text = element.loteTemola;
          hijoOpt.element = HTMLOptionElement;
          childrenOpt.push(hijoOpt); //Agraga children OPT
        }
        textOpt = element.nPrograma;
      });
      //Agrega a jsonOpt
      if (textOpt != "" && childrenOpt.length > 0) {
        jsonOpt.push({
          text: textOpt,
          children: childrenOpt,
          element: HTMLOptGroupElement,
        });
      }
      return {
        results: jsonOpt,
      };
    },
    cache: true,
  },
});


/*************************************************
 * 8. OPCIONES DE LOTES EN PROCESO DE EMPAQUE
/*************************************************/
$(".LotesProceso").select2({
  placeholder: "Selecciona un lote",
  ajax: {
    url: "../Controller/rendimiento.php?op=select2lotesprocesos",
    dataType: "json",
    type: "post",

    delay: 250,
    data: function (params) {
      return {
        palabraClave: params.term, // search term
      };
    },
    processResults: function (data) {
      //Recorre JSON para generar option group de areas
      textOpt = "";
      jsonOpt = [];
      childrenOpt = [];
      data.forEach((element) => {
        hijoOpt = {};
        if (textOpt != element.nPrograma) {
          //Agrega a jsonOpt
          if (textOpt != "" && childrenOpt.length > 0) {
            jsonOpt.push({
              text: textOpt,
              children: childrenOpt,
              element: HTMLOptGroupElement,
            });
          }
          /*********************/
          childrenOpt = [];
          //Agrega su hijito
          hijoOpt.id = element.id;
          hijoOpt.text = element.loteTemola;
          hijoOpt.element = HTMLOptionElement;
          childrenOpt.push(hijoOpt); //Agraga children OPT
        } else {
          //Agrega su hijito
          hijoOpt.id = element.id;
          hijoOpt.text = element.loteTemola;
          hijoOpt.element = HTMLOptionElement;
          childrenOpt.push(hijoOpt); //Agraga children OPT
        }
        textOpt = element.nPrograma;
      });
      //Agrega a jsonOpt
      if (textOpt != "" && childrenOpt.length > 0) {
        jsonOpt.push({
          text: textOpt,
          children: childrenOpt,
          element: HTMLOptGroupElement,
        });
      }
      return {
        results: jsonOpt,
      };
    },
    cache: true,
  },
});
/*************************************************
 * 9. OPCIONES DE LOTES EN LISTOS PARA REGISTRAR DATOS FINALES
/*************************************************/
function formatLote (state) {

  if (!state.id) {
    return state.text;
  }
  var $state = $(
    '<span><i class="fas fa-circle '+state.color+'"></i> '+state.text+'</span>'
  );
  return $state;
};

$(".LotesFinales").select2({
  placeholder: "Selecciona un lote",
  allowClear: true,
  templateResult: formatLote,

  ajax: {
    url: "../Controller/rendimiento.php?op=select2lotesfinales",
    dataType: "json",
    type: "post",
    data: function (params) {
      return {
        palabraClave: params.term, // search term
      };
    },
    processResults: function (data) {
      //Recorre JSON para generar option group de areas
      textOpt = "";
      jsonOpt = [];
      childrenOpt = [];
      data.forEach((element) => {
        hijoOpt = {};
        if (textOpt != element.nPrograma) {
          //Agrega a jsonOpt
          if (textOpt != "" && childrenOpt.length > 0) {
            jsonOpt.push({
              text: textOpt,
              children: childrenOpt,
              element: HTMLOptGroupElement,
            });
          }
          /*********************/
          childrenOpt = [];
          //Agrega su hijito
          hijoOpt.id = element.id;
          hijoOpt.text = element.loteTemola;
          hijoOpt.element = HTMLOptionElement;
          hijoOpt.color=element.color;
          childrenOpt.push(hijoOpt); //Agraga children OPT
        } else {
          //Agrega su hijito
          hijoOpt.id = element.id;
          hijoOpt.text = element.loteTemola;
          hijoOpt.element = HTMLOptionElement;
          hijoOpt.color=element.color;
          childrenOpt.push(hijoOpt); //Agraga children OPT
        }
        textOpt = element.nPrograma;
      });
      //Agrega a jsonOpt
      if (textOpt != "" && childrenOpt.length > 0) {
        jsonOpt.push({
          text: textOpt,
          children: childrenOpt,
          element: HTMLOptGroupElement,
        });
      }
      return {
        results: jsonOpt,
      };
    },
    cache: true,
  },
});

/*******************************************************
 * 10. OPCIONES DE MATERIA PRIMA
 *******************************************************/
$(".MateriaPrimaFilter").select2({
  placeholder: "Selecciona una materia prima",
  allowClear: true,

  ajax: {
    url: "../Controller/materiasPrimas.php?op=select2materiaprima",
    type: "post",
    dataType: "json",
    delay: 250,
    data: function (params) {
      return {
        palabraClave: params.term, // search term
      };
    }, processResults: function (response) {
      return {
        results: response,
      };
    },
    cache: true,}
  
});


/*******************************************************
 * 11. OPCIONES DE PROVEEDORES
 *******************************************************/
$(".ProveedorFilter").select2({
  placeholder: "Selecciona un proveedor",
  allowClear: true,

  ajax: {
    url: "../Controller/proveedores.php?op=select2proveedores",
    type: "post",
    dataType: "json",
    delay: 250,
    data: function (params) {
      return {
        palabraClave: params.term, // search term
      };
    },
    processResults: function (response) {
      return {
        results: response,
      };
    },
    cache: true,
  },
});

/*******************************************************
 * 12. OPCIONES DE PROGRAMA SOLO PARA PIEL AUTO
 *******************************************************/
//Filtro de Programas para Sets
$(".ProgramaPielFilter").select2({
  placeholder: "Selecciona un programa",
  allowClear: true,

  ajax: {
    url: "../Controller/programas.php?op=select2programaspiel",
    type: "post",
    dataType: "json",
    delay: 250,
    data: function (params) {
      return {
        palabraClave: params.term, // search term
      };
    },
    processResults: function (response) {
      return {
        results: response,
      };
    },
    cache: true,
  },
});

/*******************************************************
 * 13. OPCIONES DE PROGRAMA SOLO PARA ETIQUETAS
 *******************************************************/
//Filtro de Programas para Sets
$(".ProgramaEtiqFilter").select2({
  placeholder: "Selecciona un programa",
  allowClear: true,

  ajax: {
    url: "../Controller/programas.php?op=select2programasetiq",
    type: "post",
    dataType: "json",
    delay: 250,
    data: function (params) {
      return {
        palabraClave: params.term, // search term
      };
    },
    processResults: function (response) {
      return {
        results: response,
      };
    },
    cache: true,
  },
});

/*******************************************************
 * 14. OPCIONES DE PROGRAMA SOLO PARA CALZADO
 *******************************************************/
//Filtro de Programas para Sets
$(".ProgramaCalzadoFilter").select2({
  placeholder: "Selecciona un programa",
  allowClear: true,

  ajax: {
    url: "../Controller/programas.php?op=select2programascalz",
    type: "post",
    dataType: "json",
    delay: 250,
    data: function (params) {
      return {
        palabraClave: params.term, // search term
      };
    },
    processResults: function (response) {
      return {
        results: response,
      };
    },
    cache: true,
  },
});

/*******************************************************
 * 14. OPCIONES DE LOTES DE LADOS PENDIENTES POR EMPAQUETAR
 *******************************************************/
$(".loteMedidoFilter").select2({
  placeholder: "Selecciona un lote de medido",
  allowClear: true,

  ajax: {
    url: "../Controller/medicion.php?op=select2lotes",
    dataType: "json",
    type: "post",
    data: function (params) {
      return {
        palabraClave: params.term, // search term
      };
    },
    processResults: function (data) {
      //Recorre JSON para generar option group de areas
      textOpt = "";
      jsonOpt = [];
      childrenOpt = [];
      data.forEach((element) => {
        hijoOpt = {};
        if (textOpt != element.nPrograma) {
          //Agrega a jsonOpt
          if (textOpt != "" && childrenOpt.length > 0) {
            jsonOpt.push({
              text: textOpt,
              children: childrenOpt,
              element: HTMLOptGroupElement,
            });
          }
          /*********************/
          childrenOpt = [];
          //Agrega su hijito
          hijoOpt.id = element.id;
          hijoOpt.text = element.loteTemola;
          hijoOpt.element = HTMLOptionElement;
          childrenOpt.push(hijoOpt); //Agraga children OPT
        } else {
          //Agrega su hijito
          hijoOpt.id = element.id;
          hijoOpt.text = element.loteTemola;
          hijoOpt.element = HTMLOptionElement;
          childrenOpt.push(hijoOpt); //Agraga children OPT
        }
        textOpt = element.nPrograma;
      });
      //Agrega a jsonOpt
      if (textOpt != "" && childrenOpt.length > 0) {
        jsonOpt.push({
          text: textOpt,
          children: childrenOpt,
          element: HTMLOptGroupElement,
        });
      }
      return {
        results: jsonOpt,
      };
    },
    cache: true,
  },
 
});

$(".GrosorFilter").select2({
  placeholder: "Selecciona un Grosor",
  allowClear: true,

  ajax: {
    url: "../Controller/medicion.php?op=select2grosor",
    type: "post",
    dataType: "json",
    delay: 250,
    data: function (params) {
      return {
        palabraClave: params.term, // search term
      };
    },
    processResults: function (response) {
      return {
        results: response,
      };
    },
    cache: true,
  },
});

$(".ProgramaMedidoFilter").select2({
  placeholder: "Selecciona un programa",
  allowClear: true,

  ajax: {
    url: "../Controller/programas.php?op=select2programasmedido",
    type: "post",
    dataType: "json",
    delay: 250,
    data: function (params) {
      return {
        palabraClave: params.term, // search term
      };
    },
    processResults: function (response) {
      return {
        results: response,
      };
    },
    cache: true,
  },
});