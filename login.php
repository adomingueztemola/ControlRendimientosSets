<?php
define('INCLUDE_CHECK', 1);
require_once('include/connect.php');
session_start();
$problemas = 0;
$devBug = 0;
if ($devBug != 1) {
	error_reporting(E_ALL);
} else {
	echo 'Contenido de POST:</br>';
	print_r($_POST);
	echo '</br></br>';
}

if (isset($_POST['usuario']) and isset($_POST['pass']) and $_POST['usuario'] != "" and $_POST['pass'] != "") {
	$usuario = $_POST['usuario'];
	$passwd  = md5($_POST['pass']);
	$sql = "SELECT * FROM segusuarios WHERE usuario='$usuario' AND pass='$passwd'";
	//----------------devBug------------------------------
	if ($devBug == 1) {
		$res = mysqli_query($link, $sql) or die("Error de consulta existe Usuario: " . mysqli_error($link) . '<br>SQL: ' . $sql);
		echo $sql . '<br>';
	} else {
		$res = mysqli_query($link, $sql) or die(problemas(++$problemas));
	}
	//-------------Finaliza devBug------------------------------

	$numRes = mysqli_num_rows($res);

	if ($numRes == 1) {
		$var = mysqli_fetch_array($res);
		if ($var['estatus'] != 1) {
			errorbd('Usuario deshabilitado.');
		} else {
			$nivel = $var['idNivel'];
			$userId = $var['id'];

			$sql = "SELECT usr.id, usr.nombre, usr.apellidos AS apPat, usr.estatus AS 'estatEmp', usr.genero, lvl.id AS 'idNivel', lvl.nombre AS 'nameNivel', lvl.estatus AS 'estatLvl', ars.link AS 'arLink',
					mnu.link AS 'mnuLink', sbmn.link AS 'sbmnLink',  ars.link AS 'envLink', usr.noEmpleado AS 'idEmpleado', dtlvl.idArea
					FROM segusuarios usr
					/*INNER JOIN empleados em ON usr.idEmpleado=em.id*/
					INNER JOIN segniveles lvl ON usr.idNivel = lvl.id
					INNER JOIN segdetnivel dtlvl ON lvl.id = dtlvl.idNivel
					INNER JOIN segareas ars ON dtlvl.idArea =  ars.id AND ars.estatus = '1'
					INNER JOIN segmenus mnu ON dtlvl.idMenu = mnu.id AND mnu.estatus = '1'
					LEFT JOIN segsubmenus sbmn ON dtlvl.idSubMenu = sbmn.id AND sbmn.estatus = '1'
					WHERE usr.id = '$userId' AND usr.estatus = '1'
					ORDER BY ars.orden DESC, mnu.orden, sbmn.orden ASC
					LIMIT 1";

			//----------------devBug------------------------------
			if ($devBug == 1) {
				$res1 = mysqli_query($link, $sql) or die("Error de consulta Nivel de Usuario: " . mysqli_error($link) . '<br>SQL: ' . $sql);
				echo $sql . '<br>';
				echo '<br>nivel:' . $nivel . '<br>';
			} else {
				$res1 = mysqli_query($link, $sql) or die(problemas(++$problemas));
			}
			//-------------Finaliza devBug------------------------------

			$cantPermisos = mysqli_num_rows($res1);
			unset($_SESSION['CREident']);
			unset($_SESSION['CREidNivel']);
			//----------------devBug------------------------------
			if ($devBug == 1) {
				echo '<br>$cantPermisos: ' . $cantPermisos . '<br>';
			}
			//-------------Finaliza devBug------------------------------

			if ($cantPermisos < 1) {
				errorbd('Su usuario no tiene Permisos Asignados.');
			} else {
				$dat = mysqli_fetch_array($res1);

				if ($dat['estatEmp'] != '1') {
					errorbd('El Empleado a sido deshabilitado.');
				} else {


					$_SESSION['CREident'] = $dat['id'];
					$_SESSION['CREidNivel'] = $dat['idNivel'];
					$_SESSION['CREnombreNivel'] = $dat['nameNivel'];
					$_SESSION['CREidAreaMenu'] = $dat['idArea'];

					$_SESSION['CREnombreUser'] = trim($dat['nombre']) . ' ' . trim($dat['apPat']);
					$_SESSION['CREnombreUserCto'] = trim($dat['nombre']);
					$_SESSION['CREgenero'] = $dat['genero'];
					$_SESSION['CREnoEmpleado'] = $dat['idEmpleado'];

					$link = $dat['envLink'];
					mysqli_free_result($res);


					//----------------devBug------------------------------
					if ($devBug == 1) {
						echo '<br>CREident: ' . $_SESSION['CREident'] . '<br>';
						echo '<br>CREidNivel: ' . $_SESSION['CREidNivel'] . '<br>';
						echo '<br>CREnombreNivel: ' . $_SESSION['CREnombreNivel'] . '<br>';
						echo '<br>CREnombreUser: ' . $_SESSION['CREnombreUser'] . '<br>';
						echo '<br>CREnombreUserCto: ' . $_SESSION['CREnombreUserCto'] . '<br>';
						echo '<br>CREgenero: ' . $_SESSION['CREgenero'] . '<br>';

						echo '<br>$link: ' . $link . '<br>';

						echo '<br>Ya paso todo OK. <br> Link: ' . $link . '<br>';
						print_r($_SESSION);
					} else {
						header('location: ' . $link);
					}
					//-------------Finaliza devBug------------------------------
				}
			
			}
		}
	} else {
		errorbd('El usuario o el password que ingreso son incorrectos por favor intente de nuevo.');
	}
} else {
	errorbd('Debes llenar todos los campos.');
}

function problemas($problemas)
{
	if ($problemas != 0) {
		$_SESSION['CREacceso'] = 'Lo sentimos, este sitio web está experimentando problemas..<br> Por favor notifica al Administrador.';
		header('location: index.php');
		exit(0);
	}
}

function errorbd($error)
{
	if ($GLOBALS['devBug'] == 0) {
		$_SESSION['CREacceso'] = $error;
		header('location: index.php');
	} else {
		echo 'Problema Detectado: ' . $error;
		exit(0);
	}
}
