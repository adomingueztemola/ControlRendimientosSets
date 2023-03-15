<?php
class Texto 
{
public static function  limpCaracteresConEsp($texto){
    return  preg_replace('([^A-Za-z0-9 ])', '', $texto);
}

public static function limpCaracteresSinEsp($texto){
    return preg_replace('([^A-Za-z0-9])', '', $texto);
}

}
