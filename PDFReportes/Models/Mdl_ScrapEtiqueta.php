<?php


class PDFMarcado extends PDF_MC_Table
{
    public function init_Hoja($color)
    {
        ConfiguracionGeneral::configText($this, [0, 0, 0], [255, 255, 255]);
        ConfiguracionGeneral::configuraCabecera($this, $color);
    }

    public function datos_empresa($logo, $nameEmpresa, $ArrayDatosEmpresa)
    {
        //Insertar Logo
        $imagen = getimagesize(__DIR__ . "/../../" . $logo);    //Sacamos la información
        $ancho = $imagen[0];              //Ancho
        $alto = $imagen[1];               //Alto
        if ($ancho > $alto and $ancho > 200) {
            $this->SetX(210);

            $medida = ' width="150"';
        } else {
            $this->SetX(35);
            $medida = ' height="75"';
        }
        $dir = __DIR__;
        $this->WriteHTML(<<<EOD
        <img src="{$dir}/../../{$logo}" $medida></img>
        EOD);
        //Nombre del documento
        $this->SetXY("115", "30");
        $this->SetFont('Helvetica', 'B', 50);
        $this->SetTextColor(255, 0, 0);
        $this->SetWidths([100]);
        $this->Row([utf8_decode("SCRAP")], 0);
        $this->SetLineWidth(4);
        $this->SetDrawColor(255, 0, 0);
        $this->Rect(5, 5, 270, 205, "D"); //Marco exterior

    }
    public function RowDatosTarima($folio, $fechaSalida, $programa)
    {
        $this->SetX('6');
        $this->SetFont('Helvetica', 'B', 15);
        $this->SetTextColor(0, 0, 0);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(0.2);

        $this->SetWidths([90, 60, 90]);
        $this->Row([
            utf8_decode("N° SERIAL: " . $folio), utf8_decode("FECHA DE SALIDA"),
            utf8_decode("PROGRAMA(S)")
        ], 0);
        $this->SetFont('Helvetica', '', 15);
        $this->SetX('5');
        $this->Row([
            utf8_decode(""), utf8_decode($fechaSalida),
            utf8_decode($programa)
        ], 0);
    }
    public function RowDatosContent($piezasTotal, $_12, $_3, $_9, $_6)
    {
        $this->SetFont('Helvetica', 'B', 40);
        $this->SetX(50);
        $this->SetWidths([260]);
        $this->Row([utf8_decode("PIEZAS RECHAZADAS: " . $piezasTotal)], 0);
        $this->SetWidths([100, 90, 90]);
        $this->Ln(10);

        $this->SetFont('Helvetica', '', 25);
        $this->SetWidths([65, 65, 65,65]);
        $this->SetAligns(['c', 'c', 'c','c']);

        $this->Row([utf8_decode("12:00"),utf8_decode("03:00"), utf8_decode("06:00"), utf8_decode("09:00")], 1,0,10);
        $this->Row([utf8_decode($_12),utf8_decode($_3), utf8_decode($_6), utf8_decode($_9)], 1,0,10);

    }

    public function RowDetalleLotes($semanas, $lotes){
        $this->SetWidths([130,130]);
        $this->Row([utf8_decode("SEMANA(S)"),utf8_decode("LOTE(S)")], 1,0,10);
        $this->Row([utf8_decode($semanas),utf8_decode($lotes)], 1,0,10);

    }
    public function insertCodeNormal($image,  $y, $x = 30)
    {
        $this->Image($image, 8, 53, 50, 15, 'PNG');
    }
}
