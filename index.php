<?php
session_start();
?>
<!DOCTYPE html>
<html dir="ltr" lang="es">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Control de Rendimiento">
    <meta name="author" content="ING. ANA KAREN DOMINGUEZ RIQUELME - TWA">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="assets/images/twm.ico">
    <title>Control de Rendimiento</title>

    <!-- Custom CSS -->
    <link href="dist/css/style.css" rel="stylesheet">
    <link href="assets/libs/toastr/build/toastr.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
</head>

<body>
    <div class="main-wrapper">
        <!-- ============================================================== -->
        <!-- Preloader - style you can find in spinners.css -->
        <!-- ============================================================== -->
        <div class="preloader">
            <div class="lds-ripple">
                <div class="lds-pos"></div>
                <div class="lds-pos"></div>
            </div>
        </div>
        <!-- ============================================================== -->
        <!-- Preloader - style you can find in spinners.css -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Login box.scss -->
        <!--
        <style>
          #imgLogo{
            width: 80%;
          }
          ============================================================== -->
        </style>
        <div class="auth-wrapper d-flex no-block justify-content-center align-items-center" style="background:url(assets/images/fondo5.jpg); background-size: cover;">
            <div class="auth-box on-sidebar">
                <div id="loginform">
                    <div class="logo">
                        <span class="db"><img src="assets/images/logo.png" alt="logo" style="width:65%"/></span>
                        <h3 class="m-b-20">Plataforma de Control de Rendimiento</h3>
                    </div>
                    <!-- Form -->
                    <div class="row">
                        <div class="col-12">
                            <form class="form-horizontal m-t-20" id="loginform" method="POST" action="login.php">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon1"><i class="ti-user"></i></span>
                                    </div>
                                    <input type="text" name="usuario" class="form-control form-control-lg" placeholder="Usuario" aria-label="Username" aria-describedby="basic-addon1">
                                </div>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon2"><i class="ti-pencil"></i></span>
                                    </div>
                                    <input type="password" name="pass" class="form-control form-control-lg" placeholder="Contraseña" aria-label="Password" aria-describedby="basic-addon1">
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <div class="custom-control custom-checkbox">
                                            <a href="javascript:void(0)" id="to-recover" class="text-dark float-right"><i class="fa fa-lock m-r-5"></i> Olvidó su contraseña</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group text-center">
                                    <div class="col-xs-12 p-b-20">
                                    <button class="btn btn-block btn-lg btn-TWM" type="submit"><i class="fas fa-arrow-right"></i> Entrar</button>
                                    </div>
                                </div>
                               
                            </form>
                        </div>
                    </div>
                </div>
                <div id="recoverform">
                    <div class="logo">
                        <span class="db"><img src="assets/images/logo.png" width="80%" alt="logo" /></span>
                        <h5 class="font-medium m-b-20 m-t-10">Recuperar Contraseña</h5>
                        <span>Comunícate al área de sistemas.</span>
                    </div>
                </div>
            </div>
        </div>
        <!-- ============================================================== -->
        <!-- Login box.scss -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Page wrapper scss in scafholding.scss -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Page wrapper scss in scafholding.scss -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Right Sidebar -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Right Sidebar -->
        <!-- ============================================================== -->
    </div>
    <!-- ============================================================== -->
    <!-- All Required js -->
    <!-- ============================================================== -->
    <script src="assets/libs/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap tether Core JavaScript -->
    <script src="assets/libs/popper.js/dist/umd/popper.min.js"></script>
    <script src="assets/libs/bootstrap/dist/js/bootstrap.min.js"></script>
    <!--Custom JavaScript -->
    <script src="assets/libs/toastr/build/toastr.min.js"></script>
    <!-- ============================================================== -->
    <!-- This page plugin js -->
    <!-- ============================================================== -->
    <script>
    $(document).ready(function(){
      <?php
      if (isset($_SESSION['CREacceso']) AND $_SESSION['CREacceso'] != '') {
        echo  "notificaBad('".$_SESSION['CREacceso']."');";
        unset($_SESSION['CREacceso']);
      }
      ?>
    });

    $('[data-toggle="tooltip"]').tooltip();
    $(".preloader").fadeOut();
    // ==============================================================
    // Login and Recover Password
    // ==============================================================
    $('#to-recover').on("click", function() {
        $("#loginform").slideUp();
        $("#recoverform").fadeIn();
    });
    $('#to-login').on("click", function() {
        $("#loginform").fadeIn();
        $("#recoverform").slideUp();
    });
    // ==============================================================
    // Oculta Capa Principal
    // ==============================================================
    $(".capaPrincipal").mouseenter(function(e){
      $(".capaPrincipal").css("opacity", "1");
    });
    $(".capaPrincipal").mouseleave(function(e){
      $(".capaPrincipal").css("opacity", "0.7");
    });
    // Success Type
    function notificaBad(mensaje){
      toastr.error(mensaje, 'Lo Sentimos!', {
        "progressBar": true,
        "closeButton": true
      });
    }

    </script>
</body>

</html>
