<?php
include_once('../../Models/Mdl_ConexionBD.php');
include_once('../../Models/Mdl_MarcadoAMano.php');
include('../Models/Mdl_MarcadoDocto.php');

function getDoctoMarcado()
{
    $debug = $GLOBALS['debug'];
    $idMarcado = $GLOBALS['idMarcado'];

    $idUserReg = $GLOBALS['idUser'];
    $obj_marcado = new MarcadoAMano($debug, $idUserReg);
    $DataMarcado = $obj_marcado->getDetMarcadoXLote($idMarcado);


    $ArrayColor = explode(",", "238,90,54");
    $obj_docto = new PDFMarcado('P', 'mm', "letter");
    $obj_docto->AliasNbPages();
    $obj_docto->AddPage();

    $factual = strtotime(date("d-m-Y", time()));

    $obj_docto->init_Hoja($ArrayColor);


    $obj_docto->Ln("5");
    $obj_docto->datos_emision("Marcado Manual", $DataMarcado[0]['n_lote'], $DataMarcado[0]['f_fecha'], $DataMarcado[0]['n_programa']);
    $obj_docto->Ln("5");
    $obj_docto->datos_empresa("assets/images/logo.jpg", "TEMOLA WRAPPING MATERIALS S.A. DE C.V.", [
        "", ""
    ]);
    $obj_docto->Ln("5");
    $obj_docto->datos_cliente("", $DataMarcado[0]['n_empleado']);

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

    $obj_docto->Ln("5");

    $DataVolante = $obj_marcado->getRecuperacion($idMarcado);
    if (count($DataVolante) > 0) {
        $obj_docto->desglosePzasRecuperacion($DataVolante);
    }

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
