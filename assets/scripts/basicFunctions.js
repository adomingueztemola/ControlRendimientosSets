function bloqueoModal(event, modal, opcion) {

    if (modal == '') {
        modal = 'bloquear-modal';
    }
    if(event!=''){
        var block_ele = event.target.closest("." + modal);

    }else{
        var block_ele = $("." + modal);

    }

    // si la variable 'no' es 1 bloquea el modal
    if (opcion == 1) {

        $(block_ele).block({

            message: 'Espere un momento...',
            css: {
                border: 0,
                hover: 'wait',
                padding: 0,
                cursor: 'wait',
                color: '#848789',
                backgroundColor: 'transparent'
            },

            overlayCSS: {
                backgroundColor: '#A4A7A9',
                opacity: 0.8,

            }

        });
    } else { // si la variable 'no' es diferente de 1 desbloquea el modal
        $(block_ele).unblock();
    }

}
function bloqueoDiv(idDiv, opcion) {

  
    var block_ele = $("#"+idDiv);

    // si la variable 'no' es 1 bloquea el modal
    if (opcion == 1) {

        $(block_ele).block({

            message: 'Espere un momento...',
            css: {
                border: 0,
                hover: 'wait',
                padding: 0,
                cursor: 'wait',
                color: '#848789',
                backgroundColor: 'transparent'
            },

            overlayCSS: {
                backgroundColor: '#A4A7A9',
                opacity: 0.8,

            }

        });
    } else { // si la variable 'no' es diferente de 1 desbloquea el modal
        $(block_ele).unblock();
    }

}

$('.phone').blur(function(e) {
    var x = e.target.value.replace(/\D/g, '').match(/(\d{3})(\d{3})(\d{4})/);
    e.target.value = '(' + x[1] + ') ' + x[2] + '-' + x[3];
});

$('.MaskTelefono').keydown(function(e) {
    var key = e.which || e.charCode || e.keyCode || 0;
    $phone = $(this);

    // Don't let them remove the starting '('
    if ($phone.val().length === 1 && (key === 8 || key === 46)) {
        $phone.val('(');
        return false;
    }
    // Reset if they highlight and type over first char.
    else if ($phone.val().charAt(0) !== '(') {
        $phone.val('(' + $phone.val());
    }

    // Auto-format- do not expose the mask as the user begins to type
    if (key !== 8 && key !== 9) {
        if ($phone.val().length === 4) {
            $phone.val($phone.val() + ')');
        }
        if ($phone.val().length === 5) {
            $phone.val($phone.val() + ' ');
        }
        if ($phone.val().length === 9) {
            $phone.val($phone.val() + '-');
        }
    }

    // Allow numeric (and tab, backspace, delete) keys only
    return (key == 8 ||
        key == 9 ||
        key == 46 ||
        (key >= 48 && key <= 57) ||
        (key >= 96 && key <= 105));
}).bind('focus click', function() {
    $phone = $(this);

    if ($phone.val().length === 0) {
        $phone.val('(');
    } else {
        var val = $phone.val();
        $phone.val('').val(val); // Ensure cursor remains at the end
    }
}).blur(function() {
    $phone = $(this);

    if ($phone.val() === '(') {
        $phone.val('');
    }
});


function notificaSuc(cont,_position="toastr toast-top-right" ) {
    toastr.success(cont, 'Excelente!', {
        "progressBar": true,
        "closeButton": true,
        "stack": 4,
        "positionClass": _position,
        "showMethod": "slideDown"


    });
}

function notificaBad(cont, _position="toastr toast-top-right") {
    toastr.error(cont, 'Lo Sentimos!', {
        "progressBar": true,
        "closeButton": true,
        "stack": 4,
        "positionClass": _position,
        "showMethod": "slideDown"
        
    });
}

function bloqueoBtn(boton, no) {
    // verifica si hay un valor en la variable boton, si no le coloca una por default llamada "bloquear-btn"
    if (boton == '') {
        boton = 'bloquear-btn';
    }
    // si la variable 'no' es 1 oculta el elemento y muestra el espinner
    if (no == 1) {
        $("#" + boton).show();//show
        $("#des" + boton).prop("hidden", true);//hide
       // console.log( $("#des" + boton).find("button"));
       $("#des" + boton).find("button").prop("disabled", true);//disabled
    } else {
        // si la variable 'no' es 2 muestra el elemento y oculta el espinner
        $("#" + boton).hide();//hide
        $("#des" + boton).prop("hidden", false);//show
       $("#des" + boton).find("button").prop("disabled", false);//undisabled
    }

}

function limpiaCadena(dat, id) {
    //alert(id);
    dat = getCadenaLimpia(dat);
    $("#" + id).val(dat);
}

function getCadenaLimpia(cadena) {
    // Definimos los caracteres que queremos eliminar
    var specialChars = "\'!\"¬@#$^&%*()[]\/{}|:<>?¿¡";

    // Los eliminamos todos
    for (var i = 0; i < specialChars.length; i++) {
        cadena = cadena.replace(new RegExp("\\" + specialChars[i], 'gi'), '');
        cadena = cadena.replace(/[ÄÁáäà]/gi, "a");
        cadena = cadena.replace(/[ËÉëé]/gi, "e");
        cadena = cadena.replace(/[ÏÍïí]/gi, "i");
        cadena = cadena.replace(/[ÖÓöó]/gi, "o");
        cadena = cadena.replace(/[ÜÚüú]/gi, "u");
        cadena = cadena.replace(/ñ/gi, "n");
    }

    // Lo queremos devolver limpio en minusculas
    //cadena = cadena.toLowerCase();

    // Quitamos espacios y los sustituimos por _ porque nos gusta mas asi
    //cadena = cadena.replace(/ /g,"_");

    /* Quitamos acentos y "ñ". Fijate en que va sin comillas el primer parametro
    cadena = cadena.replace(/á/gi,"a");
    cadena = cadena.replace(/é/gi,"e");
    cadena = cadena.replace(/í/gi,"i");
    cadena = cadena.replace(/ó/gi,"o");
    cadena = cadena.replace(/ú/gi,"u");
    cadena = cadena.replace(/ñ/gi,"n");*/
    return cadena;
}

function soloNumeros(cadena, id) {
    var newCadena = cadena.replace(/[^0-9]/g, '');
    //alert(newCadena);
    $("#" + id).val(newCadena);
}

function cambiaMayusculas(cadena, id) {
    var newCadena = cadena.toUpperCase();
    //alert(newCadena);
    $("#" + id).val(newCadena);
}