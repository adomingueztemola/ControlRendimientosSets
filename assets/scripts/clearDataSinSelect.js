function clearForm(idFormulario) {
  $("#" + idFormulario)[0].reset();
  $("#" + idFormulario + " .select2")
    .val("")
    .trigger("change");
    $("#" + idFormulario + " .select2Form")
    .val("")
    .trigger("change");
  $("#resultbusq").text("");
  $("#resultbusq2").text("");
}
//Formato de Moneda
$(".Money").maskMoney();

//Formato de Mayusculas con solo CSS
$(".Mayusculas").css("text-transform", "uppercase");
//Evento para Convertir en Mayuscualas
$(".Mayusculas").on("keyup", (event) => {
  $(event.target).val($(event.target).val().toUpperCase());
});

//FUNCION PARA ELIMINAR VARIABLES GET'S
function BorrarHistorial(n_variable, n_archivo) {
  history.pushState({ data: true }, n_variable, n_archivo);
}

//FUNCION PARA CERRRAR MODAL
function cerrarModal(n_modal) {
  $("#" + n_modal).modal("hide");
}
//FUNCION PARA CARGa CONTENIDO

function cargaContenido(idDiv, n_archivo, folder = "0") {
  str_separator = "../".repeat(folder);
  $("#" + idDiv).html(
    '<div class="loading text-center"><img src="' +
      str_separator +
      'assets/images/loading.gif" alt="loading" /><br/>Un momento, por favor...</div>'
  );
  $("#" + idDiv).load(n_archivo);
}

function cargaContenidosm(idDiv, n_archivo, folder = "0") {
  str_separator = "../".repeat(folder);
  $("#" + idDiv).html(
    '<div class="loading text-center"><img src="' +
      str_separator +
      'assets/images/loadingsm.gif" alt="loading" /><br/>Un momento, por favor...</div>'
  );
  $("#" + idDiv).load(n_archivo);
}

//loading small
function loadingSmall(idDiv, folder = "0") {
  str_separator = "../".repeat(folder);
  $("#" + idDiv).html(
    '<div class="loading text-center"><img src="' +
      str_separator +
      'assets/images/loading-small.gif" alt="loading" width="70"></div>'
  );
}
function modelMatcher(params, data) {
  data.parentText = data.parentText || "";

  // Regresa el objeto data si no hay nada a comparar
  if ($.trim(params.term) === "") {
    return data;
  }
  // Busqueda recursiva para las opciones nodo
  if (data.children && data.children.length > 0) {
    // Copia el objeto data si hay nodos
    // Es requerido ya que se modifica el objeto si no hay coincidencias
    var match = $.extend(true, {}, data);

    // Verifica cada nodo de la opcion
    for (var c = data.children.length - 1; c >= 0; c--) {
      var child = data.children[c];
      child.parentText += data.parentText + " " + data.text;

      var matches = modelMatcher(params, child);

      // Si no hay coincidencia, elimina el objeto en el arreglo
      if (matches == null) {
        match.children.splice(c, 1);
      }
    }

    // Si hay coincidencia regresa el nuevo objeto
    if (match.children.length > 0) {
      return match;
    }

    // Si no hubo nodos coincidentes, verifica el objeto siguiente
    return modelMatcher(params, match);
  }

  // Convierte la información a comparar
  var original = (data.parentText + " " + data.text).toUpperCase();
  var term = params.term.toUpperCase();

  // Verifica si el texto contiene el termino
  if (original.indexOf(term) > -1) {
    return data;
  }

  // Si no contiene el termino no regresa nada
  return null;
}
//Eliminar elementos con ID
function desaparecerElementos(n_id, time_second) {
  setTimeout(() => {
    $("#" + n_id).remove();
  }, time_second);
}
//Mostrar elementos con ID
function mostrarElemento(n_id) {
  $("#" + n_id).prop("hidden", false);
}
//Ocultar elementos con ID
function ocultarElemento(n_id) {
  $("#" + n_id).prop("hidden", true);
}

$(".focusCampo").focus(function () {
  $(this).css("background-color", "#FFFFCC");
  if ($(this).val() == 0) {
    $(this).val("");
  }
});

$(".focusCampo").blur(function () {
  $(this).css("background-color", "transparent");
  if ($(this).val() == "") {
    $(this).val("0");
  }
});
//Validar si la cadena es un json
function is_json(str) {
  try {
      JSON.parse(str);
  } catch (e) {
      return false;
  }
  return true;
}
//Abrir Pestaña
function abrirNuevoTab(url) {
  // Abrir nuevo tab
  var win = window.open(url, "_blank");
  // Cambiar el foco al nuevo tab (punto opcional)
  win.focus();
}

function update(url, id, folder = 0) {
  str_separator = "../".repeat(folder);
  $("#" + id).html(
    '<div class="loading text-center"><img src="' +
      str_separator +
      'assets/images/loading.gif" alt="loading" /><br/>Un momento, por favor...</div>'
  );
  $("#" + id).load(str_separator + url);
}