<?php
class OperacionesLotes extends Exception
{
    public static function calculaRestPorcValue($porcentaje, $value)
    {
        $porc_value = $value * $porcentaje;
        $value_rea = $value - $porc_value;
        return $value_rea;
    }
    public static function diferenciaArea($areaWB, $areaProveedorLote)
    {
        return $areaWB - $areaProveedorLote;
    }

    public static function promedioArea($areaWB, $total_s)
    {
        return $areaWB / $total_s;
    }

    public static function porcDifAreaWB($diferenciaArea, $areaProveedorLote)
    {
        return $diferenciaArea / $areaProveedorLote;
    }
    public static function porcRecorteAcabado($areaCrust, $recorteAcabado)
    {
        return ($recorteAcabado / 88) / $areaCrust;
    }

    public static function perdidaAreaWBaCrust($areaWB, $areaCrust)
    {
        return ($areaCrust - $areaWB) / $areaWB;
    }

    public static function  areaXSet($area, $setsEmpacados, $tipoProceso)
    {
        if ($tipoProceso == '1') {
            return $area / ($setsEmpacados / 4);
        } else {
            return $area / $setsEmpacados;
        }
    }

    public static function costoWBXUnidad($areaWBXSet, $precioUnitFactUsd){
        return $areaWBXSet*$precioUnitFactUsd;
    }
    public static function perdidaAreaCrustTeseo($areaCrust, $areaFinal){
        return ($areaFinal-$areaCrust)/$areaCrust;
    }
    public static function yieldFinalReal($areaNeta, $areaWBXSet){
        return $areaNeta/$areaWBXSet;
    }

    public  static function calculaPorcValue($porcentaje, $value){
        $porc_value = $value * $porcentaje;
        return $porc_value;
    }
}
