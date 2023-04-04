 <div class="row">
     <div class="col-md-4 col-lg-4 col-xs-4 col-sm-4 ">
         <form id="formAsignacion">
             <div class="card  border">
                 <div class="card-header" id="">
                     <div class="row">
                         <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                             <label for="lote" class="form-label required">LOTE TRANSMISOR</label>
                             <select class="form-control select2Form LotesAbiertosFilter"style="width:100%" required name="lotetransmisor" id="lotetransmisor"></select>
                         </div>
                     </div>
                     <div class="row mt-2">
                         <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                             <table class="table table-sm table-bordered" id="t-detalletrans">
                                 <thead class="table-dark">
                                     <tr>
                                         <th>1s</th>
                                         <th>2s</th>
                                         <th>3s</th>
                                         <th>4s</th>
                                         <th>20</th>

                                         <th>Total</th>

                                     </tr>
                                 </thead>
                                 <tbody>
                                     <tr>
                                         <td id="_1s">0</td>
                                         <td id="_2s">0</td>
                                         <td id="_3s">0</td>
                                         <td id="_4s">0</td>
                                         <td id="_20">0</td>
                                         <td id="total_s">0</td>

                                     </tr>
                                 </tbody>
                             </table>
                         </div>
                     </div>
                     <div class="row">
                         <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                             <label for="lote" class="form-label required">HIDES A TRANSMITIR</label>
                             <input type="number" step="1" class="form-control focusCampo" min='1' name="hides" id="hides">
                         </div>
                     </div>
                     <div class="row">
                         <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                             <label for="lote" class="form-label required">LOTE RECEPTOR</label>
                             <select class="form-control select2Form LotesAbiertosFilter" style="width:100%" required name="lotereceptor" id="lotereceptor"></select>
                         </div>
                     </div>
                     <div class="row mt-2">
                         <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                             <table class="table table-sm table-bordered" id="t-detallerecep">
                                 <thead class="table-dark">
                                     <tr>
                                         <th>1s</th>
                                         <th>2s</th>
                                         <th>3s</th>
                                         <th>4s</th>
                                         <th>20</th>

                                         <th>Total</th>

                                     </tr>
                                 </thead>
                                 <tbody>
                                     <tr>
                                         <td id="_1s">0</td>
                                         <td id="_2s">0</td>
                                         <td id="_3s">0</td>
                                         <td id="_4s">0</td>
                                         <td id="_20">0</td>
                                         <td id="total_s">0</td>

                                     </tr>
                                 </tbody>
                             </table>
                         </div>
                     </div>
                     <div class="row">
                         <div class="col-md-12 align-center mt-4 pt-1">
                             <div id='bloqueo-btn-1' style='display:none'>
                                 <button class='btn btn-success' type='button' disabled=''>
                                     <span class='spinner-border spinner-border-sm' role='status' aria-hidden='true'></span> Actualizando ...
                                 </button>
                             </div>
                             <div id='desbloqueo-btn-1'>
                                 <button type="submit" class="button btn  btn-success">Traspasar</button>
                                 <button type="reset" onclick="clearForm('formAsignacion')" class="button btn btn-danger">Cancelar</button>

                             </div>
                         </div>
                     </div>
                 </div>
             </div>
         </form>
     </div>

     <div class="col-md-8 col-lg-8 col-xs-8 col-sm-8">
         <div class="card border">
             <div class="card-body" id="c-detalladotraspasos">
             </div>

         </div>

     </div>

 </div>

 <script src="../assets/scripts/clearDataSinSelect.js"></script>

 <script src="../assets/scripts/selectFiltros.js"></script>
 <script>
     mostrar_info()
     update("templates/FraccionLote/historialTraspasos.php", "c-detalladotraspasos", 1)

     /*********************/
     $("#formAsignacion").submit(function(e) {
         e.preventDefault();
         formData = $(this).serialize();
         $.ajax({
             url: '../Controller/reasignacionLotesFracc.php?op=traspasar',
             data: formData,
             type: 'POST',
             success: function(response) {
                 resp = response.split('|')
                 if (resp[0] == 1) {
                     notificaSuc(resp[1])
                     bloqueoBtn("bloqueo-btn-1", 2);
                     clearForm("formAsignacion")
                     update("templates/FraccionLote/historialTraspasos.php", "c-detalladotraspasos", 1)
                 } else if (resp[0] == 0) {
                     notificaBad(resp[1])
                     bloqueoBtn("bloqueo-btn-1", 2);
                 }
             },
             beforeSend: function() {
                bloqueoBtn("bloqueo-btn-1", 1);

             }

         });
     });
     /********************/
     //Visualizar Detallado de los hides por lote 
     function mostrar_info() {
         $('select#lotetransmisor').on('change', function() {
             valor = $(this).val();
             $.ajax({
                     data: {
                         "ident": valor
                     },
                     type: "POST",
                     dataType: "json",
                     url: "../Controller/pruebasHide.php?op=detalleslote",
                     beforeSend: function() {
                         // setting a timeout
                         $("#t-detalletrans td#_1s").text("")
                         $("#t-detalletrans td#_2s").text("")
                         $("#t-detalletrans td#_3s").html("<div class='spinner-border spinner-border-sm' role='status'><span class='sr-only'></span></div>")
                         $("#t-detalletrans td#_4s").text("")
                         $("#t-detalletrans td#_20").text("")
                         $("#t-detalletrans td#total_s").text("")
                     },
                 })
                 .done(function(data, textStatus, jqXHR) {
                     if (data != null) {
                         $("#t-detalletrans td#_1s").text(data["1s"] * 2)
                         $("#t-detalletrans td#_2s").text(data["2s"] * 2)
                         $("#t-detalletrans td#_3s").text(data["3s"] * 2)
                         $("#t-detalletrans td#_4s").text(data["4s"] * 2)
                         $("#t-detalletrans td#_20").text(data["_20"] * 2)
                         $("#t-detalletrans td#total_s").text(data["total_s"] * 2)
                         $("#hides").prop("max", data["total_s"] * 2)
                     } else {
                         $("#t-detalletrans td#_1s").text("0")
                         $("#t-detalletrans td#_2s").text("0")
                         $("#t-detalletrans td#_3s").text("0")
                         $("#t-detalletrans td#_4s").text("0")
                         $("#t-detalletrans td#_20").text("0")
                         $("#t-detalletrans td#total_s").text("0")
                     }

                 }).fail(function(jqXHR, textStatus, errorThrown) {

                 });


         });

         $('select#lotereceptor').on('change', function() {
             valor = $(this).val();
             $.ajax({
                     data: {
                         "ident": valor
                     },
                     type: "POST",
                     dataType: "json",
                     url: "../Controller/pruebasHide.php?op=detalleslote",
                     beforeSend: function() {
                         // setting a timeout
                         $("#t-detallerecep #_1s").text("")
                         $("#t-detallerecep #_2s").text("")
                         $("#t-detallerecep #_3s").html("<div class='spinner-border spinner-border-sm' role='status'><span class='sr-only'></span></div>")
                         $("#t-detallerecep #_4s").text("")
                         $("#t-detallerecep #_20").text("")
                         $("#t-detallerecep #total_s").text("")
                     },
                 })
                 .done(function(data, textStatus, jqXHR) {
                     if (data != null) {
                         $("#t-detallerecep #_1s").text(data["1s"] * 2)
                         $("#t-detallerecep #_2s").text(data["2s"] * 2)
                         $("#t-detallerecep #_3s").text(data["3s"] * 2)
                         $("#t-detallerecep #_4s").text(data["4s"] * 2)
                         $("#t-detallerecep #_20").text(data["_20"] * 2)
                         $("#t-detallerecep #total_s").text(data["total_s"] * 2)
                     } else {
                         $("#t-detallerecep #_1s").text("0")
                         $("#t-detallerecep #_2s").text("0")
                         $("#t-detallerecep #_3s").text("0")
                         $("#t-detallerecep #_4s").text("0")
                         $("#t-detallerecep #_20").text("0")
                         $("#t-detallerecep #total_s").text("0")
                         $("#t-detallerecep #hides").prop("max", "0")
                     }

                 }).fail(function(jqXHR, textStatus, errorThrown) {

                 });


         });
     }
 </script>

 </html>