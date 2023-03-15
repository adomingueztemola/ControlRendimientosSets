<?php
if(!defined('INCLUDE_CHECK')) die('No se puede leer este archivo');
setlocale(LC_TIME, 'es_ES.UTF-8');
date_default_timezone_set('Mexico/General');

/* Database config */

$data = file_get_contents(__DIR__."../../../configscp.json");
$config = json_decode($data, true);
/* Database config   T*/
$db_host		= $config["localhost"];
$db_user		= $config["user"];
$db_pass		=  $config["pass"];
$db_database	= $config["bd"];

/* End config */

$link = mysqli_connect($db_host,$db_user,$db_pass,$db_database) or die('No se pudo realizar la conexion');
mysqli_select_db($link,$db_database);
mysqli_query($link, "SET names UTF8");
mysqli_query($link, "SET time_zone = '-06:00'");
mysqli_query($link, "SET lc_time_names = 'es_MX'");

?>
