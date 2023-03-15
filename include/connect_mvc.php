<?php
if (!defined('INCLUDE_CHECK')) die('No se puede leer este archivo');
setlocale(LC_TIME, 'es_ES.UTF-8');
date_default_timezone_set('Mexico/General');
/**
 * 
 */
class conectar
{

	public static function conexion()
	{
		$data = file_get_contents(__DIR__ . "../../../configscp.json");
		$config = json_decode($data, true);
		$link = new mysqli($config["localhost"], $config["user"], $config["pass"], $config["bd"]);

		if ($link->connect_error) {
			die('Error de Conexión (' . $link->connect_errno . ') ' . $link->connect_error);
		}
		/* change character set to utf8 */
		if (!$link->set_charset("utf8")) {
			printf("Error al Cargar caracter de utf8: %s\n", $link->error);
		}
		$link->query("SET lc_time_names = 'es_ES'");
		//$link->query("SET time_zone = '-06:00'");

		return $link;
	}
}
function autoloader($nombreClase)
{
	$nombreClase = ucfirst($nombreClase);
	$directorio = "../Models/Mdl_{$nombreClase}.php";
	if (file_exists($directorio)) {
		require_once($directorio);
	} else if (file_exists("../" . $directorio)) {
		require_once("../" . $directorio);
	} else if (file_exists("../../" . $directorio)) {
		require_once("../../" . $directorio);
	} else {
		die("El archivo Mdl_{$nombreClase}.php no se ha podido encontrar.");
	}
}

spl_autoload_register('autoloader');
