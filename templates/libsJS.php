 <?php
  $str_space = str_repeat("../", $space);
  $idNivel = $_SESSION['CREidAreaMenu'];
  ?>
 <script>
   nivel = "<?= $idNivel ?>"
 </script>
 <script>
   folder = "<?= $space ?>"
 </script>
 <?php
  /*$add = "";
$add = str_repeat("../", $folder);
$idNivel=$_SESSION['SGGidAreaMenu'];*/
  ?>
 <script src="<?= $str_space ?>assets/libs/jquery/dist/jquery.min.js"></script>
 <script src="<?= $str_space ?>assets/libs/popper.js/dist/umd/popper.min.js"></script>
 <script src="<?= $str_space ?>assets/libs/bootstrap/dist/js/bootstrap.min.js"></script>
 <script type="text/javascript" src="<?= $str_space ?>assets/menu/webslidemenu/webslidemenu.js"></script>
 <script src="<?= $str_space ?>dist/js/app.min.js"></script>
 <script src="<?= $str_space ?>dist/js/app-style-switcher.horizontal.js"></script>
 <script src="<?= $str_space ?>assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js"></script>
 <script src="<?= $str_space ?>assets/extra-libs/sparkline/sparkline.js"></script>
 <script src="<?= $str_space ?>dist/js/waves.js"></script>
 <script src="<?= $str_space ?>dist/js/sidebarmenu.js"></script>
 <script src="<?= $str_space ?>dist/js/custom.js"></script>
 <script src="<?= $str_space ?>assets/libs/toastr/build/toastr.min.js"></script>
 <script src="<?= $str_space ?>assets/scripts/notificaciones.js"></script>
 <script src="<?= $str_space ?>assets/libs/block-ui/jquery.blockUI.js"></script>

 <script src="<?= $str_space ?>assets/scripts/basicFunctions.js"></script>
 <script src="<?= $str_space ?>dist/js/jquery.maskMoney.min.js"></script>
 <script src="<?= $str_space ?>assets/libs/select2/dist/js/select2.js"></script>
 <script src="<?= $str_space ?>assets/scripts/clearData.js"></script>
 <script src="<?= $str_space ?>assets/scripts/verificaConexionInternet.js"></script>
 <script src="<?= $str_space ?>/assets/offline.min.js"></script>
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
     requests: false
   };
   var run = function() {
     if (Offline.state === 'up') {

       check = Offline.check();
      

     }



   }
   run
   setInterval(run, 725);
 </script>