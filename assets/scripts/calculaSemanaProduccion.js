
/// FUNCION PARA COLOCAR LOS VALORES EN LOS CAMPOS CORRESPONDIENTES
function setSemanaInput(ID_inputFecha, ID_inputSemana){
    let fecha= $("#"+ID_inputFecha).val();
    let Week_Year= calculaSemanaProduccion(fecha);
    let value_Week= Week_Year[1]+"-W"+pad(Week_Year[0], 2);
    $("#"+ID_inputSemana).val(value_Week);
}

function pad(n, width) {
   n = n + '';
   return n.length >= width ? n : 
       new Array(width - n.length + 1).join('0') + n;
}

/// FUNCION PARA DETERMINAR LA SEMANA
function calculaSemanaProduccion($fecha){
   if($fecha.match(/\//)){
      $fecha   =   $fecha.replace(/\//g,"-",$fecha); //Permite que se puedan ingresar formatos de fecha ustilizando el "/" o "-" como separador
   };
   
   $fecha   =   $fecha.split("-"); //Dividimos el string de fecha en trozos (dia,mes,año)
   $dia   =   eval($fecha[2]);
   $mes   =   eval($fecha[1]);
   $ano   =   eval($fecha[0]);
   
   if ($mes==1 || $mes==2){
      //Cálculos si el mes es Enero o Febrero
      $a   =   $ano-1;
      $b   =   Math.floor($a/4)-Math.floor($a/100)+Math.floor($a/400);
      $c   =   Math.floor(($a-1)/4)-Math.floor(($a-1)/100)+Math.floor(($a-1)/400);
      $s   =   $b-$c;
      $e   =   0;
      $f   =   $dia-1+(31*($mes-1));
   } else {
      //Calculos para los meses entre marzo y Diciembre
      $a   =   $ano;
      $b   =   Math.floor($a/4)-Math.floor($a/100)+Math.floor($a/400);
      $c   =   Math.floor(($a-1)/4)-Math.floor(($a-1)/100)+Math.floor(($a-1)/400);
      $s   =   $b-$c;
      $e   =   $s+1;
      $f   =   $dia+Math.floor(((153*($mes-3))+2)/5)+58+$s;
   };

   //Adicionalmente sumándole 1 a la variable $f se obtiene numero ordinal del dia de la fecha ingresada con referencia al año actual.

   //Estos cálculos se aplican a cualquier mes
   $g   =   ($a+$b)%7;
   $d   =   ($f+$g-$e)%7; //Adicionalmente esta variable nos indica el dia de la semana 0=Lunes, ... , 6=Domingo.
   $n   =   $f+3-$d;
   
   if ($n<0){
      //Si la variable n es menor a 0 se trata de una semana perteneciente al año anterior
      $semana   =   53-Math.floor(($g-$s)/5);
      $ano      =   $ano-1; 
   } else if ($n>(364+$s)) {
      //Si n es mayor a 364 + $s entonces la fecha corresponde a la primera semana del año siguiente.
      $semana   = 1;
      $ano   =   $ano+1;
   } else {
      //En cualquier otro caso es una semana del año actual.
      $semana   =   Math.floor($n/7)+1;
   };
   return [$semana,$ano]; //La función retorna una cadena de texto indicando la semana y el año correspondiente a la fecha ingresada   

}

