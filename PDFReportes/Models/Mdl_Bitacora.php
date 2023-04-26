<?php


class PDFBitacora extends PDF_MC_Table
{
    public function init_Hoja($color)
    {
        ConfiguracionGeneral::configText($this, [0, 0, 0], [255, 255, 255]);
        ConfiguracionGeneral::configuraCabecera($this, $color);
    }

    public function datos_empresa($logo, $nameEmpresa)
    {
        //Insertar Logo
        $imagen = getimagesize(__DIR__ . "/../../" . $logo);    //Sacamos la información
        $ancho = $imagen[0];              //Ancho
        $alto = $imagen[1];               //Alto
        if ($ancho > $alto and $ancho > 200) {
            $this->SetX(155);

            $medida = ' width="150"';
        } else {
            $this->SetX(35);
            $medida = ' height="75"';
        }
        $dir = __DIR__;
        $this->WriteHTML(<<<EOD
        <img src="{$dir}/../../{$logo}" $medida></img>
        EOD);
        // //Nombre del documento
        $this->SetXY("10", "15");
        $this->SetFont('Helvetica', 'B', 16);
        $this->SetTextColor(0, 0, 0);
        $this->SetWidths([200]);
        $this->Row([utf8_decode("TEMOLA WRAPPING MATERIALS S.A. DE C.V.")], 0);
        $this->Ln(4);
        $this->SetTextColor(255, 0, 0);
        $this->Row([utf8_decode("Stock de Cajas en Sets")], 0);
        $this->Ln(4);
        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Helvetica', '', 12);
        $this->Row([utf8_decode("Fecha de Impresión: ".date("d/m/Y H:i"))], 0);
        $this->Ln(4);

    }
    public function contenidoCajas($arrayCajas){
        $this->SetFont('Helvetica', '', 12);

        $this->SetWidths([32, 70, 30,30, 35]);
        $this->Row([utf8_decode("FOLIO LOTE"),
        utf8_decode("PROGRAMA"),  utf8_decode("CANT. CAJAS"),
        utf8_decode("CANT. FISICA"), utf8_decode("CANT. SALIDA")],1);
        foreach ($arrayCajas as $value) {
            $this->Row([utf8_decode($value['loteTemola']),
            utf8_decode($value['nPrograma']),  utf8_decode($value['cantCaja']),
            utf8_decode(""), utf8_decode("")],1, 0,10);
        }


    }

    public function getFirmaVoBo(){
        $this->SetY(-40);
        $this->Line(100, 80, 100, 80);
        $this->SetWidths([100,100]);
        $this->SetAligns('C');
        $this->Row([utf8_decode("______________________________________"),
        utf8_decode("____________________________")], 0);
        $this->Row([utf8_decode("NOMBRE/FIRMA ÁREA DE PROGRAMACIÓN"),
        utf8_decode("NOMBRE/FIRMA ÁREA DE SET'S")], 0);
        

    }
   
}
