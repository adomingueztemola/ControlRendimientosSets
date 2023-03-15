<?php
class ConfiguracionGeneral
{
    public static function headerTickets()
    {
    }
    public static function configuraTicket($pdf, $logo, $nameSuc, $direccion, $asunto)
    {
        /*=========================================================================
            COLOCACION DEL LOGO DE LA EMPRESA Y TITULO DEL DOCUMENTO
        ===========================================================================*/
        $pdf->SetFont('Helvetica', 'B', 14);

        /*   $pdf->SetWidths([80]);
        $pdf->SetAligns('C');
        $textImage=$pdf->Image("../../".$logo, 25, 10,60);

        $pdf->Row([$textImage],0);
        $pdf->Row([$pdf->GetY()],0);*/
        $imagen = getimagesize("../../" . $logo);    //Sacamos la información
        $ancho = $imagen[0];              //Ancho
        $alto = $imagen[1];               //Alto
        /*   $pdf->SetWidths([80]);
        $pdf->Row([$ancho],0);
        $pdf->Row([$alto],0);*/
        if ($ancho > $alto and $ancho > 200) {
            $pdf->SetX(25);

            $medida = ' width="150"';
        } else {
            $pdf->SetX(35);

            $medida = ' height="75"';
        }
        $pdf->WriteHTML(<<<EOD
        <img src="../../{$logo}" $medida></img>
      
        EOD);
        if ($ancho > $alto and $ancho > 200) {
            $pdf->Ln(20);
        } else {
            $pdf->Ln(25);
        }
        $pdf->SetAligns('C');

        $pdf->SetWidths([80]);
        $pdf->Row([utf8_decode($nameSuc)], 0);
        $pdf->SetFont('Helvetica', '', 9);
        $pdf->SetWidths([80]);
        $pdf->Row([utf8_decode($direccion)], 0);
        $pdf->SetFont('Helvetica', 'B', 14);
        $pdf->Ln(3);

        $pdf->SetWidths([80]);
        $pdf->Row([utf8_decode($asunto)], 0);
    }

    public function configuraHoja($pdf, $logo, $nameSuc, $direccion, $asunto){

    }
    public static function configuraCabecera($pdf,$color){
        ConfiguracionGeneral::configText($pdf,array("0", "0", "0"), $color);
        $pdf->Rect("0", "0", "216", "10", "F");


    }

    public static function configText($pdf, $colorText, $color)
    {
        $pdf->SetFillColor($color[0], $color[1], $color[2]);
        $pdf->SetTextColor($colorText[0], $colorText[1], $colorText[2]);
    }
}
