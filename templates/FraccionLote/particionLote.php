   <div class="row">
       <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5 card-header border">
           <form id="formParticion">

               <div class="row">
                   <div class="col-md-12">
                       <label for="lote" class="form-label required">LOTE PADRE</label>
                       <select class="form-control select2Form LotePadresFilter" style="width:100%" required name="lote" id="lote"></select>
                   </div>
               </div>
               <div class="row mt-2" id="c-detallado">
                   <div class="col-md-12 table-responsive">
                       <table class="table table-sm table-bordered">
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
               <hr>
               <div class="row">
                   <div class="col-md-6">
                       <label for="programa" class="form-label required">PROGRAMA</label>
                       <select class="form-control select2Form ProgramasFilter" required style="width:100%" name="programa" id="programa"></select>
                   </div>
                   <div class="col-md-3">
                       <label for="hides" class="form-label required">HIDES</label>
                       <input type="number" class="form-control focusCampo" required name="hides" id="hides" aria-label="">
                   </div>
                   <div class="col-md-3 mt-4 pt-1">
                       <div id='bloqueo-btn-1' style='display:none'>
                           <button class='btn btn-success' type='button' disabled=''>
                               <span class='spinner-border spinner-border-sm' role='status' aria-hidden='true'></span>
                           </button>
                       </div>
                       <div id='desbloqueo-btn-1'>
                           <button type="submit" class="button btn  btn-success">
                               <i class="fas fa-upload"></i>
                           </button>
                           <button type="reset" onclick="clearForm('formParticion')" class="button btn btn-danger"> <i class="fas fa-times"></i></button>

                       </div>
                   </div>
               </div>
           </form>
       </div>

       <div class="col-lg-7 col-md-7 col-sm-7 col-xs-7" id="content-partidas">

       </div>
   </div>
   <script src="../assets/scripts/selectFiltros.js"></script>
   <script src="../assets/scripts/basicFunctions.js"></script>
   <script src="../assets/scripts/clearDataSinSelect.js"></script>

   <script>
       mostrar_info()
       update("templates/FraccionLote/historialParticiones.php", "content-partidas", 1)
       //Envio de particion a base de datos
       $("#formParticion").submit(function(e) {
           e.preventDefault();
           formData = $(this).serialize();
           $.ajax({
               url: '../Controller/particionLotes.php?op=agregarparticion',
               data: formData,
               type: 'POST',
               success: function(response) {
                   resp = response.split('|')
                   if (resp[0] == 1) {
                       bloqueoBtn("bloqueo-btn-1", 2);
                       notificaSuc(resp[1]);
                       clearForm("formParticion")
                       update("templates/FraccionLote/historialParticiones.php", "content-partidas", 1)
                   } else if (resp[0] == 0) {
                       bloqueoBtn("bloqueo-btn-1", 2);
                       notificaBad(resp[1]);
                   }
               },
               beforeSend: function() {
                   bloqueoBtn("bloqueo-btn-1", 1)
               }

           });
       });
       //Visualizar Detallado de los hides por lote 
       function mostrar_info() {
           $('select#lote').on('change', function() {
               valor = $(this).val();
               $.ajax({
                       data: {
                           "ident": valor
                       },
                       type: "POST",
                       dataType: "json",
                       url: "../Controller/pruebasLados.php?op=detalleslote",
                       beforeSend: function() {
                           // setting a timeout
                           $("#_1s").text("")
                           $("#_2s").text("")
                           $("#_3s").html("<div class='spinner-border spinner-border-sm' role='status'><span class='sr-only'></span></div>")
                           $("#_4s").text("")
                           $("#_20").text("")
                           $("#total_s").text("")
                       },
                   })
                   .done(function(data, textStatus, jqXHR) {
                       if (data != null) {
                           $("#_1s").text(data["1s"] * 2)
                           $("#_2s").text(data["2s"] * 2)
                           $("#_3s").text(data["3s"] * 2)
                           $("#_4s").text(data["4s"] * 2)
                           $("#_20").text(data["_20"] * 2)
                           $("#total_s").text(data["total_s"] * 2)
                           $("#hides").prop("max", data["total_s"] * 2)
                       } else {
                           $("#_1s").text("0")
                           $("#_2s").text("0")
                           $("#_3s").text("0")
                           $("#_4s").text("0")
                           $("#_20").text("0")
                           $("#total_s").text("0")
                           $("#hides").prop("max", "0")
                       }

                   }).fail(function(jqXHR, textStatus, errorThrown) {

                   });
           });
       }
   </script>