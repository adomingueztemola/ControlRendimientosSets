<?php
function eliminar_simbolos($string){

    $string = trim($string);

    $string = str_replace(
        array('á', 'à', 'ä', 'â', 'ª', 'À', 'Â', 'Ä'),
        array('a', 'a', 'a', 'a', 'a', 'Á', 'A', 'A'),
        $string
    );

    $string = str_replace(
        array('é', 'è', 'ë', 'ê', 'È', 'Ê', 'Ë'),
        array('e', 'e', 'e', 'e', 'É', 'E', 'E'),
        $string
    );

    $string = str_replace(
        array('í', 'ì', 'ï', 'î', 'Ì', 'Ï', 'Î'),
        array('i', 'i', 'i', 'i', 'Í', 'I', 'I'),
        $string
    );

    $string = str_replace(
        array('ó', 'ò', 'ö', 'ô', 'Ò', 'Ö', 'Ô'),
        array('o', 'o', 'o', 'o', 'Ó', 'O', 'O'),
        $string
    );

    $string = str_replace(
        array('ú', 'ù', 'ü', 'û', 'Ù', 'Û', 'Ü'),
        array('u', 'ú', 'u', 'u', 'Ú', 'U', 'U'),
        $string
    );

    $string = str_replace(
        array('ç', 'Ç'),
        array('c', 'C',),
        $string
    );

    $string = str_replace(
        array("\\", "¨", "º", "-", "~",
             "#", "@", "|", "!", "\"",
             "·", "$", "%", "&", "/",
             "(", ")", "?", "'", "¡",
             "¿", "[", "^", "<code>", "]",
             "+", "}", "{", "¨", "´",
             ">", "< ", ";", ",", ":",
             ".", " "),
        ' ',
        $string
    );
return $string;
}
function formatoMil($number, $decimal=2){
    return number_format($number,$decimal,'.',',');
}
function formatoFecha($option="3", $_today=true,$date=""){
    setlocale(LC_ALL,"es_ES");
    switch($option){
        case "4":
            return strftime("%A %d de %B del %Y");
        break;
        case "3":
            return strftime("%d de %B del %Y");
        break;
        case "2":
            if($date!=''){
             return strftime("%B del %Y", strtotime($date));
            }
             return strftime("%B del %Y");
        break;
        case "WY":
            return strftime("Semana %W del %Y");
        break;
        case "W":
            return strftime("%W");
        break;


    }


}
?>
