<?php
include_once('../../Models/Mdl_ConexionBD.php');
include_once('../../Models/Mdl_Medido.php');
include_once('../../assets/scripts/cadenas.php');

include('../Models/Mdl_EtiquetaPaq.php');
include("../Models/barcode.php");
function getEtiquetaScrap()
{
    $debug = $GLOBALS['debug'];
    $idLote = $GLOBALS['idLote'];

    $idUserReg = $GLOBALS['idUser'];
    $obj_medido = new Medido($debug, $idUserReg);
    $Data= $obj_medido->getPaquetesXLote($idLote);
    $Data= Excepciones::validaConsulta($Data);

    $ArrayColor = explode(",", "238,90,54");
    $obj_docto = new PDFEtiquetas('P', 'mm', ["75","100"], true);
    $obj_docto->SetAutoPageBreak(true,3); 

   $obj_docto->AliasNbPages();
    $obj_docto->AddPage();
    foreach ($Data as $key=>$value) {
        $areaTotalRd= formatoMil($value['areaTotalRd'],2);
        $totalLados= formatoMil($value['totalLados'],0);
        $DataDet= $obj_medido->getDetPaquete( $value['id']);
        $DataDet= Excepciones::validaConsulta($DataDet);
        $ArrayLados=[];
        foreach ($DataDet as $det) {
            $areaRedondFT= formatoMil($det['areaRedondFT'],2);
           array_push($ArrayLados, [$det["numLado"],$det["nSeleccion"], $areaRedondFT ]);
        }
        getEtiqueta($obj_docto, $value['id'],  $areaTotalRd, $value['nPrograma'], $value['numPaquete'],

        $value['loteTemola'],
        $totalLados, $ArrayLados );
        if($key+1<count($Data)){
            $obj_docto->AddPage();

        }

    }
    $obj_docto->Output();
}

function getEtiqueta($obj_docto, $idPaquete,$areaTotal, $nPrograma, $numPaquete, 
    $loteTemola, $totalLados,
    $arrayLados ){
    $obj_docto->SetFont('Helvetica', 'B', 10);
    $obj_docto->datos_empresa("assets/images/logo.jpg", "TEMOLA WRAPPING MATERIALS S.A. DE C.V.", [
        "", "" ]);
    $obj_docto->DatosPaquete($nPrograma, $totalLados, $numPaquete, $loteTemola);
    barcode('../../doctos/medido/Paquete' . $idPaquete . '.png',  $areaTotal, 10, 'horizontal', 'code128');
    $obj_docto->insertCodeNormal('../../doctos/medido/Paquete' . $idPaquete . '.png', "30");
    $obj_docto->DetalleLados($arrayLados, $areaTotal);
}



