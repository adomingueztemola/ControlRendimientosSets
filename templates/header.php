<?php
$str_space = str_repeat("../", $space);
?>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?= $info->detailPag; ?>">
    <meta name="author" content="<?= $info->nombrePag; ?>">
    <!-- Favicon icon -->
    <link rel="icon" type="image/ico" href="<?= $str_space ?>assets/images/twm.ico">
    <title><?= $info->nombrePag; ?></title>

    <!-- Vendor -->
    <link rel="stylesheet" href="<?= $str_space ?>assets/libs/bootstrap/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="<?= $str_space ?>dist/css/all.css">
    <link rel="stylesheet" href="<?= $str_space ?>assets/libs/select2/dist/css/select2.css">

    <link href="<?= $str_space ?>dist/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= $str_space ?>assets/offline-theme-chrome-indicator.css">

    <link id="effect" rel="stylesheet" type="text/css" media="all" href="<?= $str_space ?>assets/menu/webslidemenu/dropdown-effects/fade-down.css" />
    <link rel="stylesheet" type="text/css" media="all" href="<?= $str_space ?>assets/menu/webslidemenu/webslidemenu-TWM.css" />
    <link id="theme" rel="stylesheet" type="text/css" media="all" href="<?= $str_space ?>assets/menu/webslidemenu/color-skins/grd-TWM.css" />
    <link rel="stylesheet" type="text/css" href="<?= $str_space ?>assets/libs/toastr/build/toastr.css">
    <script src="../assets/offline.min.js"></script>
    <script>
        Offline.options = {
            // to check the connection status immediatly on page load.
            checkOnLoad: false,

            // to monitor AJAX requests to check connection.
            interceptRequests: true,

            // to automatically retest periodically when the connection is down (set to false to disable).
            reconnect: {
                // delay time in seconds to wait before rechecking.
                initialDelay: 3,

                // wait time in seconds between retries.
                delay: 10
            },

            // to store and attempt to remake requests which failed while the connection was down.
            requests: true
        };
        var run = function() {
            if (Offline.state === 'up')
                Offline.check();
        }
        setInterval(run, 1000);
    </script>
</head>