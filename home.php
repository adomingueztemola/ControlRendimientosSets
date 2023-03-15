<?php
require_once 'seg.php';
$info = new Seguridad();
require_once('include/connect.php');
$info->Acceso();
$idUser = $_SESSION['SMAident'];
$nameUser = $_SESSION['SMAnombreUserCto'];
$sucursal = $_SESSION['SMAidSuc'];
$pyme = $_SESSION['SMApyme'];
?>
<!DOCTYPE html>
<html dir="ltr" lang="<?= $info->lng; ?>">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?= $info->detailPag; ?>">
    <meta name="author" content="<?= $info->nombrePag; ?>">
    <!-- Favicon icon -->
    <link rel="icon" type="image/ico" href="assets/images/Esmarla.ico">
    <title><?= $info->nombrePag; ?></title>

    <!-- Vendor -->
    <link rel="stylesheet" href="assets/libs/bootstrap/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="dist/css/all.css">
    <!-- Vendor -->

    <!-- Custom CSS -->


    <!-- Custom CSS -->
    <link href="dist/css/style.css" rel="stylesheet">

    <!--Main Menu File-->
    <link id="effect" rel="stylesheet" type="text/css" media="all" href="assets/menu/webslidemenu/dropdown-effects/fade-down.css" />
    <link rel="stylesheet" type="text/css" media="all" href="assets/menu/webslidemenu/webslidemenu-Esmarla.css" />

    <link id="theme" rel="stylesheet" type="text/css" media="all" href="assets/menu/webslidemenu/color-skins/grd-Esmarla.css" />

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>

<![endif]-->
</head>

<body>
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
    <!-- Main wrapper - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <div id="main-wrapper">
        <!-- ============================================================== -->
        <!-- Topbar header - style you can find in pages.scss -->
        <!-- ============================================================== -->
        <?= $info->creaHeaderConMenu(); ?>

        <!-- ============================================================== -->
        <!-- End Topbar header -->
        <!-- ==========================


        <div class="ml-auto">
            <select class="custom-select border-0 text-muted">
                <option value="0" selected="">August 2018</option>
                <option value="1">May 2018</option>
                <option value="2">March 2018</option>
                <option value="3">June 2018</option>
            </select>
        </div>

        ==================================== -->

        <!-- ============================================================== -->
        <!-- Page wrapper  -->
        <!-- ============================================================== -->
        <div class="page-wrapper">
            <!-- ============================================================== -->
            <!-- Container fluid  -->
            <!-- ============================================================== -->
            <div class="container-fluid">

                <!-- ============================================================== -->
                <!-- Comienzan Cards superiores -->
                <!-- ============================================================== -->

                <!-- ============================================================== -->
                <!-- End Container fluid  -->
                <!-- ============================================================== -->
                <!-- ============================================================== -->
                <!-- footer -->
                <!-- ============================================================== -->
                <!-- ============================================================== -->
                <!-- End footer -->
                <!-- ============================================================== -->
            </div>
            <!-- ============================================================== -->
            <!-- End Page wrapper  -->
            <!-- ============================================================== -->
        </div>
        <!-- ============================================================== -->
        <!-- End Wrapper -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <script src="assets/libs/jquery/dist/jquery.min.js"></script>
        <!-- Bootstrap tether Core JavaScript -->
        <script src="assets/libs/popper.js/dist/umd/popper.min.js"></script>
        <script src="assets/libs/bootstrap/dist/js/bootstrap.min.js"></script>
        <!-- Menu -->
        <script type="text/javascript" src="assets/menu/webslidemenu/webslidemenu.js"></script>
        <!-- apps -->
        <script src="dist/js/app.min.js"></script>
        <script src="dist/js/app.init.horizontalEquinox.js"></script>
        <script src="dist/js/app-style-switcher.horizontal.js"></script>
        <script src="dist/js/app-style-switcher.js"></script>
        <!-- slimscrollbar scrollbar JavaScript -->
        <script src="assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js"></script>
        <script src="assets/extra-libs/sparkline/sparkline.js"></script>
        <!--Wave Effects -->
        <script src="dist/js/waves.js"></script>
        <!--Menu sidebar -->
        <script src="dist/js/sidebarmenu.js"></script>
        <!--Custom JavaScript -->
        <script src="dist/js/custom.js"></script>
        <!--This page JavaScript -->
        <!--chartis chart-->
        <script src="assets/libs/chartist/dist/chartist.min.js"></script>
        <script src="assets/libs/chartist-plugin-tooltips/dist/chartist-plugin-tooltip.min.js"></script>
        <script src="assets/libs/echarts/dist/echarts-en.min.js"></script>
        <!--c3 charts -->


</body>

<!-- ============================================================== -->
<!-- All Jquery -->


</html>