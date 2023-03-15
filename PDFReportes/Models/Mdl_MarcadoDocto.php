<?php


class PDFMarcado extends PDF_MC_Table
{
    public function init_Hoja($color)
    {
        ConfiguracionGeneral::configText($this, [0, 0, 0], [255, 255, 255]);
        ConfiguracionGeneral::configuraCabecera($this, $color);
    }
    public function datos_emision($titulo, $lote, $fecha, $programa)
    {
        ConfiguracionGeneral::configText($this, [0, 0, 0], [255, 255, 255]);
        //Title
        $this->SetFont('Helvetica', 'B', 24);
        $this->SetWidths([90]);
        $this->Row([utf8_decode($titulo)], 0);
        //Barra de Subtitle
        $this->SetFont('Helvetica', 'B', 10);
        $this->SetXY("90", "12");
        $this->SetWidths([40, 40, 40]);
        $this->Row([
            utf8_decode("FECHA INGRESO"),
            utf8_decode("PROGRAMA"),
            utf8_decode("LOTE")
        ], 0);
        $this->SetFont('Helvetica', '', 10);

        $this->SetXY("90", "16");
        $this->Row([
            utf8_decode($fecha),
            utf8_decode($programa),
            utf8_decode($lote)
        ], 0);
        $this->Line(10, 25, 205, 25);
    }
    public function datos_empresa($logo, $nameEmpresa, $ArrayDatosEmpresa)
    {
        $this->SetX("10");

        //Insertar Logo
        $imagen = getimagesize(__DIR__ . "/../../" . $logo);    //Sacamos la información
        $ancho = $imagen[0];              //Ancho
        $alto = $imagen[1];               //Alto
        if ($ancho > $alto and $ancho > 200) {
            $this->SetX(25);

            $medida = ' width="150"';
        } else {
            $this->SetX(35);
            $medida = ' height="75"';
        }
        $dir = __DIR__;
        $this->WriteHTML(<<<EOD
        <img src="{$dir}/../../{$logo}" $medida></img>
        EOD);

        //Nombre de La Empresa 
        $this->SetXY("100", "30");
        $this->SetFont('Helvetica', 'B', 14);

        $this->SetWidths([100]);
        $this->Row([utf8_decode($nameEmpresa)], 0);
        //Datos de La Empresa 
        /*    $this->SetFont('Helvetica', '', 9);
        $this->SetXY("100", "35");
        $this->SetWidths([110]);

        foreach ($ArrayDatosEmpresa as $value) {
            $this->SetX("100");

            $this->Row([utf8_decode($value)], 0);
        }*/
    }
    public function datos_cliente($nameCliente, $empleado)
    {
        $this->SetX("10");
        $this->SetWidths([90, 90]);
        $this->SetFont('Helvetica', 'B', 12);
        $this->Row(["", utf8_decode("Empleado Emisor")], 0);
        $this->SetFont('Helvetica', '', 12);
        $this->Row(["", utf8_decode($empleado)], 0);
    }

    public function Footer_Cotizacion()
    {
        // Go to 1.5 cm from bottom
        $this->SetY(-10);
        // Select Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        // Print centered page number
        $this->Cell(0, 2, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }

    public function cancelacion($label)
    {
        $this->SetFont('Arial', 'B', 50);
        $this->SetTextColor(255, 192, 203);
        $this->RotatedText(35, 190, $label, 45);
    }

    function RotatedText($x, $y, $txt, $angle)
    {
        //Text rotated around its origin
        $this->Rotate($angle, $x, $y);
        $this->Text($x, $y, $txt);
        $this->Rotate(0);
    }

    public function tabla_desglose($ArrayLabelTable, $ArrayMarcado, $color)
    {
        $this->SetX("10");
        ConfiguracionGeneral::configText($this, array("255", "255", "255"), $color);
        $this->SetFont('Helvetica', '', 12);
        $this->SetWidths([195]);
        $this->SetAligns("C");
        $this->Row($ArrayLabelTable, 0, 1);
        ConfiguracionGeneral::configText($this, array("0", "0", "0"), array("255", "255", "255"));

        $this->SetWidths([100, 90]);
        $this->SetAligns(["L", "L"]);

        $this->Row([
            utf8_decode("Piezas Totales: "),
            utf8_decode(formatoMil($ArrayMarcado['0']['pzasTotales'], 0))
        ], 0);
        $this->Row([
            utf8_decode("Yield: "),
            utf8_decode(formatoMil($ArrayMarcado['0']['yield'], 2) . '%')
        ], 0);
        $this->Row([
            utf8_decode("Área Crust: "),
            utf8_decode(formatoMil($ArrayMarcado['0']['areaCrust'], 2) . ' ft2')
        ], 0);
        $this->Row([
            utf8_decode("Área Medida con Decremento ".$ArrayMarcado['0']['porcDecremento']."%: "),
            utf8_decode(formatoMil($ArrayMarcado['0']['areaCrustDecremento'], 2) . ' ft2')
        ], 0);
        $this->Row([
            utf8_decode("Área de Piezas Calculadas: "),
            utf8_decode(formatoMil($ArrayMarcado['0']['area'], 2) . ' ft2')
        ], 0);
    }

    public function desglosePzas($ArrayConteo)
    {
        $this->SetWidths([48.75, 48.75, 48.75, 48.75]);
        $this->SetAligns(["L", "L", "L", "L"]);
        $Array_TipoPzas = [];
        $Array_TotalPzas = [];
        foreach ($ArrayConteo as $key => $value) {
            array_push($Array_TipoPzas, $ArrayConteo[$key]['nombre']);
            array_push($Array_TotalPzas, formatoMil($ArrayConteo[$key]['preliminar']));
        }
        $this->Row($Array_TipoPzas);
        $this->Row($Array_TotalPzas);
    }

    public function desglosePzasRecuperacion($ArrayConteo)
    {
        ConfiguracionGeneral::configText($this, array("255", "255", "255"), array("255", "0", "0"));
        $this->SetFont('Helvetica', '', 12);
        $this->SetWidths([195]);
        $this->SetAligns("C");
        $this->Row(["DESGLOSE DE PIEZAS RECUPERADAS"], 0, 1);

        ConfiguracionGeneral::configText($this, array("0", "0", "0"), array("255", "255", "255"));

        $this->SetWidths([48.75, 48.75, 48.75, 48.75]);
        $this->SetAligns(["L", "L", "L", "L"]);
        $Array_TipoPzas = [];
        $Array_TotalPzas = [];
        foreach ($ArrayConteo as $key => $value) {
            array_push($Array_TipoPzas, $ArrayConteo[$key]['n_pzasVolante']);
            array_push($Array_TotalPzas, formatoMil($ArrayConteo[$key]['cantidad']));
        }
        $this->Row($Array_TipoPzas);
        $this->Row($Array_TotalPzas);
    }
}
