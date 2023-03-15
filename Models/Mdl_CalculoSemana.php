<?php
class CalculoSemana extends ConexionBD
{
    protected $debug = 0;
    private $idUserReg;
    private $idSeccion;


    public function __construct($debug, $idUserReg, $idSeccion)
    {
        $this->initConexion();
        $this->debug = $debug;
        $this->idUserReg = $idUserReg;
        $this->idSeccion = $idSeccion;
    }

    public function __destruct()
    {
        $this->close();
    }


    public static function calculoSemana()
    {
        $isToday = date("w");
        if ($isToday != '4') {

            //SEMANA INICIA EL DIA JUEVES
            $fechaInicioProxSemana = new DateTime('last thursday');
            $f_fechaInicioProxSemana = $fechaInicioProxSemana->format('Y-m-d');
        } else {
            $f_fechaInicioProxSemana = date("Y-m-d");
        }
        //SEMANA TERMINA EL DIA MIERCOLES
        $isToday = date("w");
        if ($isToday != '3') {
            $fechaFinalProxSemana = new DateTime('next wednesday');
            $f_fechaFinalProxSemana = $fechaFinalProxSemana->format('Y-m-d');
        } else {
            $f_fechaFinalProxSemana = date("Y-m-d");
        }

        return [$f_fechaInicioProxSemana, $f_fechaFinalProxSemana];
    }

    public static function hourdiff($hour_1, $hour_2, $formated = false)
    {

        $h1_explode = explode(":", $hour_1);
        $h2_explode = explode(":", $hour_2);

        $h1_explode[0] = (int) $h1_explode[0];
        $h1_explode[1] = (int) $h1_explode[1];
        $h2_explode[0] = (int) $h2_explode[0];
        $h2_explode[1] = (int) $h2_explode[1];


        $h1_to_minutes = ($h1_explode[0] * 60) + $h1_explode[1];
        $h2_to_minutes = ($h2_explode[0] * 60) + $h2_explode[1];


        if ($h1_to_minutes > $h2_to_minutes) {
            $subtraction = $h1_to_minutes - $h2_to_minutes;
        } else {
            $subtraction = $h2_to_minutes - $h1_to_minutes;
        }

        $result = $subtraction / 60;

        if (is_float($result) && $formated) {

            $result = (string) $result;

            $result_explode = explode(".", $result);

            return $result_explode[0] . ":" . (($result_explode[1] * 60) / 10);
        } else {
            return $result;
        }
    }
    public static function formatoHora($fraccionHora)
    {
        $result = (string) $fraccionHora;
        $result_explode = explode(".", $result);
        $formatMinute= formatoMil((($result_explode[1] * 60) / 100),0);
        return $result_explode[0] . ":" .$formatMinute;
    }
    public static function convertirHorasASegundos($hora){
        $h1_explode = explode(":", $hora);
        $h1_explode[0] = (int) $h1_explode[0];
        $h1_explode[1] = (int) $h1_explode[1];
        $h1_to_minutes = ($h1_explode[0] * 3600) + ($h1_explode[1]*60);
        return $h1_to_minutes;
    }
}
