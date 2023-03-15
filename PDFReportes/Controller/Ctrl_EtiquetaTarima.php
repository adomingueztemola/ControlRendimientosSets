<?php
include_once('../../Models/Mdl_ConexionBD.php');
include_once('../../Models/Mdl_Scrap.php');
include_once('../../assets/scripts/cadenas.php');

include('../Models/Mdl_ScrapEtiqueta.php');
include("../Models/barcode.php");
function getEtiquetaScrap()
{
    $debug = $GLOBALS['debug'];
    $idTarima = $GLOBALS['idTarima'];

    $idUserReg = $GLOBALS['idUser'];
    $obj_scrap = new Scrap($debug, $idUserReg);
    $DataScrap = $obj_scrap->getEtiquetaScrap($idTarima);


    $ArrayColor = explode(",", "238,90,54");
    $obj_docto = new PDFMarcado('L', 'mm', "letter");
    $obj_docto->AliasNbPages();
    $obj_docto->AddPage();
    $obj_docto->SetFont('Helvetica', 'B', 10);
    $obj_docto->datos_empresa("assets/images/logo.jpg", "TEMOLA WRAPPING MATERIALS S.A. DE C.V.", [
        "", ""
    ]);
    barcode('../../doctos/tarimas/' . $DataScrap['folio'] . '.png',  $DataScrap['folio'], 10, 'horizontal', 'code128');
    $obj_docto->insertCodeNormal('../../doctos/tarimas/' . $DataScrap['folio'] . '.png', 25, 23);
    $obj_docto->Ln(10);
    $obj_docto->RowDatosTarima($DataScrap['folio'], $DataScrap['fFechaSalida'], $DataScrap['programas']);
    $obj_docto->Ln(20);

    $obj_docto->RowDatosContent(formatoMil($DataScrap['totalPzas'],0), formatoMil($DataScrap['_12'],0), 
    formatoMil($DataScrap['_3'],0), formatoMil($DataScrap['_9'],0), formatoMil($DataScrap['_6'],0));
    $obj_docto->Ln(20);
    $obj_docto->RowDetalleLotes( $DataScrap['semanas'],$DataScrap['lotes'] );
    $factual = strtotime(date("d-m-Y", time()));
   // $obj_docto->datos_empresa("assets/images/logo.jpg", "TEMOLA WRAPPING MATERIALS S.A. DE C.V.", [
   /* $obj_docto->init_Hoja($ArrayColor);


    $obj_docto->Ln("5");
    $obj_docto->datos_emision("Marcado Manual", $DataScrap[0]['n_lote'], $DataScrap[0]['f_fecha'], $DataScrap[0]['n_programa']);
    $obj_docto->Ln("5");
    $obj_docto->datos_empresa("assets/images/logo.jpg", "TEMOLA WRAPPING MATERIALS S.A. DE C.V.", [
        "", ""
    ]);
    $obj_docto->Ln("5");
    $obj_docto->datos_cliente("", $DataScrap[0]['n_empleado']);

    //DESGLOSE DE CANTIDAD DE PRODUCTOS 
    $obj_docto->Ln("5");

    $obj_docto->tabla_desglose(
        [utf8_decode("DATOS GENERALES")],
        $DataMarcado,
        $ArrayColor
    );
    $DataVolante = $obj_marcado->getMetricasConteoTeseo($idMarcado);
    $obj_docto->desglosePzas($DataVolante);
    /* foreach ($DataVolante as $key => $value) {
        echo "  <tr><td><b>{$DataVolante[$key]["nombre"]}:</b> {$DataVolante[$key]["preliminar"]}</td></tr>";
    }*/

  /*  $obj_docto->Ln("5");

    $DataVolante = $obj_marcado->getRecuperacion($idMarcado);
    if (count($DataVolante) > 0) {
        $obj_docto->desglosePzasRecuperacion($DataVolante);
    }
*/
    $obj_docto->Output();
}


function getPDFCotizacion($idCotizacion)
{
    /* $debug = $GLOBALS['debug'];
    $idSuc = $GLOBALS['idSuc'];

    $idUserReg = $GLOBALS['idUserReg'];
    $obj_cotizacion = new Cotizacion($debug, $idUserReg, $idSuc);
    $DataCotizacion = $obj_cotizacion->getCotizacion($idCotizacion);
    $DataDetCotizacion = $obj_cotizacion->getDetalleCotizacion($idCotizacion);

    if ($DataCotizacion['notas'] == '') {
        $let = 'A MESES SIN INTERES DE PERIODO DE MAS DE UN AÑO';
    }

    $ArrayColor = explode(",", $DataCotizacion['rgb']);
    $obj_docto = new PDFCotizacion('P', 'mm', "letter");
    $obj_docto->AliasNbPages();
    $obj_docto->AddPage();

    $obj_docto->init_Hoja($ArrayColor);
    if ($DataCotizacion['estatus'] == '0') {
        $obj_docto->cancelacion(utf8_decode('Cotización Cancelada'));
    } else  if ($DataCotizacion['lbl_vigencia'] == 'Expirada') {
        $obj_docto->cancelacion(utf8_decode('Cotización Expirada'));
    }
    $folio =  $DataCotizacion['folio'];
    $obj_docto->Ln("5");
    $obj_docto->datos_emision("Cotización", $folio, $DataCotizacion['f_fechaEmision'], $DataCotizacion['f_fechaVigencia']);
    $obj_docto->Ln("5");
    $obj_docto->datos_empresa($DataCotizacion['logo'], $DataCotizacion['n_empresa'], [
        $DataCotizacion['rfc'], $DataCotizacion['direccion']
    ]);
    $obj_docto->Ln("5");
    $obj_docto->datos_cliente($DataCotizacion['nameCliente'], $DataCotizacion['n_empleado']);

    //DESGLOSE DE CANTIDAD DE PRODUCTOS 
    $obj_docto->Ln("10");
    $obj_docto->tabla_desglose(
        [utf8_decode("PRODUCTO"), utf8_decode("CANTIDAD"), utf8_decode("P. UNITARIO"), utf8_decode("SUBTOTAL")],
        $DataDetCotizacion,
        $ArrayColor
    );
    $obj_docto->Ln("5");

    $obj_docto->total_cotizacion($DataCotizacion['total']);
    $obj_docto->Ln("5");
    $obj_docto->notas_remitente($let);
    $carpeta = __DIR__ . "/../../doctos/cotizaciones";
    if (!file_exists($carpeta)) {
        mkdir($carpeta, 0777, true);
    }
    $nombre = "$carpeta/$folio.pdf";

    $obj_docto->Output('F', $nombre);
    return $nombre;*/
}
