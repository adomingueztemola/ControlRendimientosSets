<?php
require_once('../include/connect.php');
require_once('../assets/scripts/cadenas.php');
require_once('../assets/scripts/Thumb.php');
function guardarImg($file, $carpeta,$carpetaAlm, $fileName)
{
    $debug = 0;
    if ($file["error"] > 0) {
        return '0|Existe un Error en el archivo';
    } else {
        $cont = 0;
        $varError = 0;
        $contError = '';
        //------ Se valida que exista La Carpeta y si no se Crea-------------------------
        if (!file_exists($carpeta)) {
            mkdir($carpeta, 0777, true);
        }
        #=======================================================================================
  
            $archivo = $file['name'];
            $valores = explode(".", $archivo);
            $extension = $valores[count($valores) - 1];
            $extension=$extension=='jpg'?'jpeg':$extension;
            $fileNameExt = str_replace(" ", "_", $fileName) . '.' . $extension;
            $fileName = str_replace(" ", "_", $fileName);

            /*======================= PROCESO DE ALMACENAMIENTO DE ORIGINAL CON BAJA RESOLUCION ========================*/
            //-------------------------- GUARDA ARCHIVOS ORIGINALES  ------------------------------
            $urlOriginalConExtension = $carpeta . $fileNameExt;
            $urlOriginal = $carpeta . $fileName;
            $urlOriginalConExtensionAlm = $carpetaAlm . $fileNameExt;
            //-------------------------------------------------------------------------------------
            copy($file['tmp_name'], $urlOriginalConExtension); //guarda original
            $thumb = new Thumb();
            $thumb->loadImage($urlOriginalConExtension);
            $thumb->save($urlOriginal, 80, false);
            return '1|Se guardó la Imagen con éxito|'.$urlOriginalConExtensionAlm;
        
    }
}


function guardarPDF($file, $carpeta,$carpetaAlm, $fileName)
{
    $debug = 0;
    if ($file["error"] > 0) {
        return '0|Existe un Error en el archivo';
    } else {
        $cont = 0;
        $varError = 0;
        $contError = '';
        //------ Se valida que exista La Carpeta y si no se Crea-------------------------
        if (!file_exists($carpeta)) {
            mkdir($carpeta, 0777, true);
        }
        #=======================================================================================
  
            $archivo = $file['name'];
            $valores = explode(".", $archivo);
            $extension = $valores[count($valores) - 1];
            $fileNameExt = str_replace(" ", "_", $fileName) . '.' . $extension;
            $fileName = str_replace(" ", "_", $fileName);

            /*======================= PROCESO DE ALMACENAMIENTO DE ORIGINAL CON BAJA RESOLUCION ========================*/
            //-------------------------- GUARDA ARCHIVOS ORIGINALES  ------------------------------
            $urlOriginalConExtension = $carpeta . $fileNameExt;
            $urlOriginal = $carpeta . $fileName;
            $urlOriginalConExtensionAlm = $carpetaAlm . $fileNameExt;
            //-------------------------------------------------------------------------------------
            copy($file['tmp_name'], $urlOriginalConExtension); //guarda original
            return '1|Se guardó el PDF con éxito|'.$urlOriginalConExtensionAlm;
        
    }
}


function guardarMultPDF($ArrayFile, $carpeta, $carpetaAlm, $fileName, $indice=-1)
{
    $debug = 0;
    if($debug=='1'){
        print_r($ArrayFile);
        echo '<br>';
        print_r($carpeta);
        echo '<br>';
        print_r($carpetaAlm);
        echo '<br>';
        print_r($fileName);
    }
    for ($i = 0; $i < count($ArrayFile["error"]); $i++) {
        if ($ArrayFile["error"][$i] > 0) {
            return [0, "No se pudo almacenar la imagen, notifica a tu Administrador."];
        }
    }

    $cont = 0;
    $varError = 0;
    $contError = '';
    $ArrayRutas=[];
    //------ Se valida que exista La Carpeta y si no se Crea-------------------------
    if (!file_exists($carpeta)) {
        mkdir($carpeta, 0777, true);
    }
    #=======================================================================================
    if($indice<=-1){
        for ($i = 0; $i < count($ArrayFile["name"]); $i++) {
            $archivo = $ArrayFile['name'][$i];
            $valores = explode(".", $archivo);
            $extension = $valores[count($valores) - 1];
            $fileNameExt = str_replace(" ", "_", $fileName) . '.' . $extension;
            $fileName = str_replace(" ", "_", $fileName);

            /*======================= PROCESO DE ALMACENAMIENTO DE ORIGINAL CON BAJA RESOLUCION ========================*/
            //-------------------------- GUARDA ARCHIVOS ORIGINALES  ------------------------------
            $urlOriginalConExtension = $carpeta . $fileNameExt;
            $urlOriginal = $carpeta . $fileName;
            $urlOriginalConExtensionAlm = $carpetaAlm . $fileNameExt;
            //-------------------------------------------------------------------------------------
            copy($ArrayFile['tmp_name'][$i], $urlOriginalConExtension); //guarda original
    
            array_push($ArrayRutas,[$urlOriginalConExtensionAlm, $extension]);
        
        }
    
    }else{
        $archivo = $ArrayFile['name'][$indice];
        $valores = explode(".", $archivo);
        $extension = $valores[count($valores) - 1];
        $fileNameExt = str_replace(" ", "_", $fileName) . '.' . $extension;
        $fileName = str_replace(" ", "_", $fileName);

        /*======================= PROCESO DE ALMACENAMIENTO DE ORIGINAL CON BAJA RESOLUCION ========================*/
        //-------------------------- GUARDA ARCHIVOS ORIGINALES  ------------------------------
        $urlOriginalConExtension = $carpeta . $fileNameExt;
        $urlOriginal = $carpeta . $fileName;
        $urlOriginalConExtensionAlm = $carpetaAlm . $fileNameExt;
        //-------------------------------------------------------------------------------------
        copy($ArrayFile['tmp_name'][$indice], $urlOriginalConExtension); //guarda original

        $ArrayRutas=[$urlOriginalConExtensionAlm, $extension];
    }
 
    return [1, 'Se guardó Documento con éxito',$ArrayRutas];
}