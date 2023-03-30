//Filtro de Programas para Sets
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

//Filtro de Proceso
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

//Filtro de Programas para sets/metros
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
           hijoOpt.id = element.id ;
           hijoOpt.text = element.nombre;
           hijoOpt.element = HTMLOptionElement;
           childrenOpt.push(hijoOpt); //Agraga children OPT
         } else {
           //Agrega su hijito
           hijoOpt.id = element.id ;
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

//Filtro de Programas para sets/metros
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
         hijoOpt.id = element.id ;
         hijoOpt.text = element.loteTemola;
         hijoOpt.element = HTMLOptionElement;
         childrenOpt.push(hijoOpt); //Agraga children OPT
       } else {
         //Agrega su hijito
         hijoOpt.id = element.id ;
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

//Filtro de Semana de Lotes
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

// Filtro de lotes de sets en teseo/sin empacar
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
         hijoOpt.id = element.id ;
         hijoOpt.text = element.loteTemola;
         hijoOpt.element = HTMLOptionElement;
         childrenOpt.push(hijoOpt); //Agraga children OPT
       } else {
         //Agrega su hijito
         hijoOpt.id = element.id ;
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
// Filtro de lotes Abierto
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
         hijoOpt.id = element.id ;
         hijoOpt.text = element.loteTemola;
         hijoOpt.element = HTMLOptionElement;
         childrenOpt.push(hijoOpt); //Agraga children OPT
       } else {
         //Agrega su hijito
         hijoOpt.id = element.id ;
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

///CARGA EMPLEADOS
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
          hijoOpt.id = element.id ;
          hijoOpt.text = element.loteTemola;
          hijoOpt.element = HTMLOptionElement;
          childrenOpt.push(hijoOpt); //Agraga children OPT
        } else {
          //Agrega su hijito
          hijoOpt.id = element.id ;
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

