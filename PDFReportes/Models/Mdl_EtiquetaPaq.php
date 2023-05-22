<?php


class PDFEtiquetas extends PDF_MC_Table
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
            $this->SetXY(72, 20);

            $medida = ' width="65"';
        } else {
            $this->SetXY(75, 30);
            $medida = ' height="75"';
        }
        $dir = __DIR__;
        $this->WriteHTML(<<<EOD
        <img src="{$dir}/../../{$logo}" $medida></img>
        EOD);
        $this->SetLineWidth(1);
        $this->SetDrawColor(238, 113, 24);
        $this->Rect(1, 1, 98, 33, "D"); //Marco exterior
        $this->Rect(1, 33, 98, 41, "D"); //Marco exterior
        $this->SetDrawColor(0, 0, 0);
        $this->Rect(2.5, 2.5, 95.10, 28.5, "D"); //Marco exterior

    }
    public function DatosPaquete(
        $programa,
        $totalLados,
        $noPaquete,
        $loteTemola
    ) {
        $this->SetY('3');
        $this->SetFont('Times', '', 11);
        $this->SetTextColor(0, 0, 0);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(0.2);

        $this->SetWidths([240]);
        $this->Row([
            utf8_decode("NOMBRE: " . $programa)
        ], 0);

        $this->SetWidths([50, 50]);
        $this->Row([
            utf8_decode("TOTAL DE LADOS: " . $totalLados),
            utf8_decode("No. PAQUETE: " . $noPaquete)
        ], 0);
        $this->SetWidths([240]);
        $this->Row([
            utf8_decode("LOTE: " . $loteTemola)
        ], 0);
        $this->SetWidths([240]);

        $this->Row([
            utf8_decode("ÁREA TOTAL (ft²):")
        ], 0);
    }

    public function insertCodeNormal($image,  $y, $x = 30)
    {
        $this->Image($image, 29, 23, 30, 7, 'PNG');
    }

    public function DetalleLados($arrayLado, $totalFt2)
    {
        $this->SetXY('5', "36");
        $this->SetFont('Times', '', 10);
        $this->SetWidths([20, 35, 35]);
        $this->Row([
            utf8_decode("#LADO"),
            utf8_decode("CLASIFICACIÓN"),
            utf8_decode("MEDIDA Ft²")
        ]);
        foreach ($arrayLado as  $value) {
            $this->SetX('5');

            $this->Row([
                utf8_decode($value[0]),
                utf8_decode($value[1]),
                utf8_decode($value[2])
            ],1,0,4);
        }
        $this->SetWidths([55, 35]);
        $this->SetX('5');

        $this->Row([
            utf8_decode("TOTAL FT²"),
            utf8_decode($totalFt2),
        ]);
    }
}
