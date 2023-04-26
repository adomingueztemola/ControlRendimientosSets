<?php
include_once('../../Models/Mdl_ConexionBD.php');
include_once('../../assets/scripts/cadenas.php');
include('../Models/Mdl_Bitacora.php');

function getBitacoraAlmacen()
{
    $debug = $GLOBALS['debug'];
    $idUserReg = $GLOBALS['idUser'];
    $obj_empaque= new Empaque($idUserReg, $debug);
    $obj_docto = new PDFBitacora('P', 'mm', "letter");
    $obj_docto->AliasNbPages();
    $obj_docto->AddPage();
    $obj_docto->SetFont('Helvetica', 'B', 10);
    $obj_docto->datos_empresa("assets/images/logo.jpg", "TEMOLA WRAPPING MATERIALS S.A. DE C.V.", [
        "", ""
    ]);
    $obj_docto->Ln(7);
    $Data=$obj_empaque->getStockCajas();
    $Data= Excepciones::validaConsulta($Data);

    $obj_docto->contenidoCajas($Data);
  
    $obj_docto->Output();
}


