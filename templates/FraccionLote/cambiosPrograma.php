   <div class="row">
       <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5 card-header border">
           <form id="formReasignacion">
            <input type="hidden" name="option" value="1">
               <div class="row">
                   <div class="col-md-12">
                       <label for="lote" class="form-label required">LOTE</label>
                       <select class="form-control select2Form LotesAbiertosFilter" style="width:100%" required name="lote" id="lote"></select>
                   </div>
               </div>
               <hr>
               <div class="row">
                   <div class="col-md-6">
                       <label for="programa" class="form-label required">PROGRAMA</label>
                       <select class="form-control select2Form ProgramasFilter" required style="width:100%" name="programa" id="programa"></select>
                   </div>
                   <div class="col-md-6">
                       <label for="procesos" class="form-label">PROCESO</label>
                       <select class="form-control select2Form ProcesosFilter" style="width:100%" name="proceso" id="procesos"></select>
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
                           <button type="submit" class="button btn  btn-success">
                               <i class="fas fa-upload"></i> Actualizar Cambios
                           </button>
                       </div>
                   </div>
               </div>
           </form>
       </div>

       <div class="col-lg-7 col-md-7 col-sm-7 col-xs-7" id="content-reasignacion">

       </div>
   </div>
   <script src="../assets/scripts/selectFiltros.js"></script>
   <script src="../assets/scripts/basicFunctions.js"></script>

   <script>
       mostrar_info(); 
       update('templates/FraccionLote/cargaReasignacionLotes.php', 'content-reasignacion', 1)
       /*************** FORMULARIO DE REASIGNACION DE PROGRAMA *********************/
       $("#formReasignacion").submit(function(e) {
           e.preventDefault();
           formData = $(this).serialize();
           $.ajax({
               url: '../Controller/rendimiento.php?op=reasignaprograma',
               data: formData,
               type: 'POST',
               success: function(json) {
                   resp = json.split('|')
                   if (resp[0] == 1) {
                       notificaSuc(resp[1])
                       bloqueoBtn("bloqueo-btn-1", 2);
                       clearForm("formReasignacion")

                       update('templates/FraccionLote/cargaReasignacionLotes.php', 'content-reasignacion', 1)


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
                       url: "../Controller/pruebasHide.php?op=detalleslote",
                       beforeSend: function() {},
                   })
                   .done(function(data, textStatus, jqXHR) {
                       if (data != null) {
                           $("#programa").select2("trigger", "select", {data:{id:data.idCatPrograma, text:data.n_programa}})
                           $("#procesos").select2("trigger", "select", {data:{id:data.idCatProceso, text:data.c_proceso+'-'+data.n_proceso}})

                       } else {

                       }

                   }).fail(function(jqXHR, textStatus, errorThrown) {

                   });
           });
       }
   </script>