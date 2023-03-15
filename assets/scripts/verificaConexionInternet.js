window.addEventListener('online', function() {
    toastr.clear()
    toastr.options ={ "progressBar": true,
    "closeButton": true,
    "stack": 4,
    "positionClass": "toastr toast-top-right",
    "showMethod": "slideDown"}
    notificaSuc("Conexión a Internet Restablecida");
   
}, false);

window.addEventListener('offline', function() {
    toastr.options = {
        'closeButton': false,
        'debug': false,
        'newestOnTop': false,
        'progressBar': false,
        'positionClass': 'toastr toast-top-right',
        'preventDuplicates': false,
        'showDuration': '1000',
        'hideDuration': '1000',
        "onclick": null,
        'timeOut': '0',
        'extendedTimeOut': '0',
        'showEasing': 'swing',
        'hideEasing': 'linear',
        'showMethod': 'fadeIn',
        'hideMethod': 'fadeOut',

    }
    toastr.warning('Sin Conexión a Internet, verifica tu conexión', {containerId: "sinconexion"});



   
}, false);
