function doSearch(id)

{

    const tableReg = document.getElementById('datos'+id);

    const searchText = document.getElementById('searchTerm').value.toLowerCase();

    let total = 0;
    let rowspan=false;
    let rowsContadas=0;
    let jsonComponentMix=[];
    // Recorremos todas las filas con contenido de la tabla

    for (let i = 1; i < tableReg.rows.length; i++) {
       /* console.log( "TENGO LA ROW: ");
        console.log( tableReg.rows[i]);
        console.log( "ESTOY BUSCANDO: ");
        console.log(tableReg.rows[i].getElementsByClassName('haveRowSpan').length);*/
        colspan=false;

        //Checa si existe un rowsapn dentro del row
        if (tableReg.rows[i].getElementsByClassName('haveRowSpan').length) {
            rowspan=true;
            rowsContadas=0;
            jsonComponentMix=[];

        }
        if(rowsContadas=='3'){
            rowspan=false;
        }
        let found = false;
      

        const cellsOfRow = tableReg.rows[i].getElementsByTagName('td');
        let j=''
        if(!rowspan){
            j='1'
        }else if(rowspan && rowsContadas>0){
            j='0'
        }else{
            j='1'
        }
        // Recorremos todas las celdas

           /* console.log("Celda: ");
            console.log(cellsOfRow[j]);*/
            if(cellsOfRow[j]!== undefined){
               
                const compareWith = cellsOfRow[j].innerHTML.toLowerCase();

                // Buscamos el texto en el contenido de la celda
    
                if (searchText.length == 0 || compareWith.indexOf(searchText) > -1) {
    
                    found = true;
    
                    total++;
    
                }
                if(rowspan){
                   jsonComponentMix.push({"id":i,
                                          "found":found,
                                           "lote":compareWith});
                   rowsContadas++;

                }
            }else{
                colspan=true;
                if(rowspan){
                    jsonComponentMix.push({"id":i,
                    "found":false,
                    "lote":""});
                    rowsContadas++;
                }
            }
            console.log("Contador: "+rowsContadas)
           console.log(jsonComponentMix)

        
        if (found && !rowspan) {
            tableReg.rows[i].style.display = '';

        } else  if (!found && !rowspan){
            tableReg.rows[i].style.display = 'none';
        }
        if (rowspan && rowsContadas==3){
            result= checkFoundRowspan(jsonComponentMix)
            if(result){
                $.each(jsonComponentMix, function(i, item) {
                    tableReg.rows[item.id].style.display = '';

                });
            }else{
                $.each(jsonComponentMix, function(i, item) {
                    tableReg.rows[item.id].style.display = 'none';

                });
            }
        }

    }



    // mostramos las coincidencias


    const td=document.getElementById("lblCoincidencias");

    td.classList.remove("hide", "red");

    if (searchText == "") {

        td.classList.add("hide");

    } else if (total) {

        td.innerHTML="Se ha encontrado "+total+" coincidencia"+((total>1)?"s":"");

    } else {

        td.classList.add("red");

        td.innerHTML="No se han encontrado coincidencias";

    }

}

function checkFoundRowspan(json){
    check=false
    $.each(json, function(i, item) {
        if(item.found){
            check=true 
        }
    });
    return check
}