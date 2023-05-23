<?php
session_start();
define('INCLUDE_CHECK', 1);
$idUser = $_SESSION['CREident'];
$nameUser = $_SESSION['CREnombreUserCto'];

$devBug = 0;
if ($devBug != 1) {
	error_reporting(0);
} else {
	echo 'Contenido de POST: ';
	var_dump($_POST);
	echo '<br>Contenido de SESSION: ';
	var_dump($_SESSION);
	echo '</br></br>';
	error_reporting(E_ALL);

}
#echo '<br>linea 14<br>';
class Seguridad
{
	public $pagina;
	public $nombrePag;
	public $detailPag;
	public $ident;
	public $idNivel;
	public $nombreNivel;
	public $nombreUser;
	public $idDetNivel;
	public $idMenu;
	public $idSubMenu;
	public $idSubMenu2;
	public $nombreSuc;
	public $area;
	public $linkArea;
	public $subArea;
	public $iconColor;
	public $autor;
	public $pyme;
	public $lng;

	#-----------------------  SEGURIDAD  ------------------------------
	public function Acceso()
	{
		//Validamos que existan las variables
		if (isset($_SESSION['CREident']) and $_SESSION['CREident'] >= 1 and isset($_SESSION['CREidNivel']) and $_SESSION['CREidNivel'] and isset($_SESSION['CREnombreNivel']) and isset($_SESSION['CREnombreUser']) >= 1) {
			require('../include/connect.php');

			$pag = identificaAccess();

			$this->ident = $_SESSION['CREident'];
			$this->idNivel = $_SESSION['CREidNivel'];
			$this->nombreNivel = $_SESSION['CREnombreNivel'];
			$this->pagina = $pag;
			$this->nombreUser = $_SESSION['CREnombreUser'];
			$idLevel = $_SESSION['CREidNivel'];
			$ident = $_SESSION['CREident'];
			$this->autor = 'TWM';
			$this->lng = 'es';
		} else {
			problemas('Se ha Bloqueado por Seguridad, por favor inténtalo de nuevo o notifica a tu Administrador... <br>Error: <b>SEG001</b>');
		}
		$sql = "SELECT
	  ars.link,
	  ars.error AS linkError,
	  IF(sbmns2.id IS NULL,IFNULL( sbmns.nombre, mns.nombre ),IFNULL( sbmns2.nombre, sbmns.nombre )) AS nameFile,
	  IF(sbmns2.id IS NULL,IFNULL( sbmns.descripcion, mns.descripcion ),IFNULL( sbmns2.descripcion, sbmns.descripcion )) AS descFile,
	  IFNULL( sbmns.estatus, mns.estatus ) AS estatusFile,
	  ars.id AS idArea,
	  mns.id AS idMenu,
	  sbmns.id AS idSubMenu,
	  sbmns2.id AS idSubMenu2 
  		FROM
	  segareas ars
	  INNER JOIN segmenus mns ON ars.id = mns.idArea
	  LEFT JOIN segsubmenus sbmns ON mns.id = sbmns.idSegMenu
	  LEFT JOIN segsubmenu2 sbmns2 ON sbmns.id = sbmns2.idSegSubMenu 
 	 WHERE
	  ( mns.link = '$pag' OR sbmns.link = '$pag' OR sbmns2.link = '$pag' ) 
 	 ORDER BY
	  ars.orden,
	  mns.orden,
	  sbmns.orden,
	  sbmns2.orden ASC 
	  LIMIT 1";

		//----------------devBug------------------------------
		if ($GLOBALS['devBug'] == 1) {
			$respLnk = mysqli_query($link, $sql) or die("<br><br>Error al consultar el Archivo en los Registros: " . mysqli_error($link) . '<br>SQL: ' . $sql);
			echo 'Validación de archivo en Registros: ' . $sql . '<br>';
		} else {
			$respLnk = mysqli_query($link, $sql) or die(problemas('Se ha Bloqueado por Seguridad, por favor inténtalo de nuevo o notifica a tu Administrador... <br>Error: <b>SEG002</b>'));
		}
		//-------------Finaliza devBug------------------------------

		$cantLnk = mysqli_num_rows($respLnk);
		//----------------devBug------------------------------
		if ($GLOBALS['devBug'] == 1) {
			echo '<br>$cantLnk: ' . $cantLnk . '<br>';
		}
		//-------------Finaliza devBug------------------------------

		if ($cantLnk == 1) {
			$lnk = mysqli_fetch_array($respLnk);
			$lnkError = $lnk['linkError'];
			$lnkNameFi = $lnk['nameFile'];
			$lnkDescFi = $lnk['descFile'];
			$lnkStatFi = $lnk['estatusFile'];
			$idAr = $lnk['idArea'];
			$idMn = $lnk['idMenu'];
			$idSm = $lnk['idSubMenu'];
			$idSm2 = $lnk['idSubMenu2'];

			$this->nombrePag = $lnkNameFi;
			$this->detailPag = $lnkDescFi;
			$this->linkArea = $lnk['link'];
			$this->idMenu = $lnk['idMenu'];
			$this->idSubMenu = $lnk['idSubMenu'];
			$this->idSubMenu2 = $lnk['idSubMenu2'];
			$this->area = $idAr;
			$this->subArea = $idMn;

			if ($lnkStatFi != '1') {
				negado($lnkError, 'La liga a la que deceas Acceder esta Deshabilitada.');
			}
			$busq = ($idSm == '' or $idSm == NULL) ? "dtlvl.idSubMenu IS NULL OR dtlvl.idSubMenu = ''" : "dtlvl.idSubMenu = '$idSm'";
			$busq2 = ($idSm2 == '' or $idSm2 == NULL) ? "dtlvl.idSubMenu2 IS NULL OR dtlvl.idSubMenu2 = ''" : "dtlvl.idSubMenu2 = '$idSm2'";
			$sql = "SELECT CONCAT(usr.nombre, ' ', usr.apellidos) AS fullName, dtlvl.id AS idDetLevel
								FROM segusuarios usr
							
								INNER JOIN segniveles lvl ON usr.idNivel = lvl.id ANd lvl.estatus = '1'
								INNER JOIN segdetnivel dtlvl ON usr.idNivel = dtlvl.idNivel
								WHERE usr.id = '$ident' AND usr.idNivel = '$idLevel'  AND usr.estatus = '1'
									AND dtlvl.idArea = '$idAr' AND dtlvl.idMenu = '$idMn' AND ($busq)  AND ($busq2)";

			//----------------devBug------------------------------
			if ($GLOBALS['devBug'] == 1) {
				echo '<br>-- Entro Con Debug -- <br><br>';

				$authLnk = mysqli_query($link, $sql) or die("<br><br>Error al validar el Usuario con el Detalle del Nivel: " . mysqli_error($link) . '<br>SQL: ' . $sql);
				echo 'Validación de Usuario con su Detalle de Nivel: ' . $sql . '<br>';
			} else {

				$authLnk = mysqli_query($link, $sql) or die(problemas('Se ha Bloqueado por Seguridad, por favor inténtalo de nuevo o notifica a tu Administrador... <br>Error: <b>SEG004</b>'));
			}
			//-------------Finaliza devBug------------------------------

			$cantAuth = mysqli_num_rows($authLnk);

			if ($cantAuth == 1) {
				$datAuth = mysqli_fetch_array($authLnk);
				$this->idDetNivel = $datAuth['idDetLevel'];

				//----------------devBug------------------------------
				if ($GLOBALS['devBug'] == 1) {
					echo '<br><br><hr><br>Se ha validado Correctamente al Usuario: <b>' . $datAuth['fullName'] . '</b> y se le identificó el idDetNivel en <b>' . $datAuth['idDetLevel'] . '</b>.<br><hr><br><br>';
				} //-------------Finaliza devBug------------------------------

			} elseif ($cantAuth > 1) {
				problemas('Se ha Bloqueado por Seguridad, por favor inténtalo de nuevo o notifica a tu Administrador... <br>Error: <b>SEG005</b>');
			} else {
				negado($lnkError, 'No tienes Acceso al link que intentaste ingresar.');
			}
		} else {
			problemas('El area donde deseas ingresar tiene problemas. Notifica a tu Administrador... <br>Error: <b>SEG003</b> ');
		}
	}  #--FIN SEGURIDAD--

	#-----------------------  CUSTOMIZACIÓN  ------------------------------
	public function customizerMobil()
	{
		$pyme = $this->pyme;

		$idUser = $this->ident;
		$cantPym = 1;

		if ($cantPym == 1) {
			$linkLog = ($this->linkArea != '') ? 'gerencia.php' : 'index.php';
			//----------------devBug------------------------------
			if ($GLOBALS['devBug'] == 1) {
				echo '<br>LinkArea: ' . $this->linkArea . '<br>';
				echo '<br>LinkLogo: ' . $linkLog . '<br>';
			}
			//-------------Finaliza devBug------------------------------

			return '
					<!-- Mobile Header -->
					<div class="wsmobileheader clearfix ">
						<a id="wsnavtoggle" class="wsanimated-arrow"><span></span></a>
						<span class="smllogo"><img src="../assets/menu/images/logo.png" width="80%" alt="" /></span>
					</div>
					<!-- Mobile Header -->
				';
		} else {
			problemas('Se ha Bloqueado por Seguridad, por favor inténtalo de nuevo o notifica a tu Administrador... <br>Error: <b>SEG007</b>');
		}
	}

	public function customizerDesktop()
	{
		$idUser = $this->ident;
		$pyme = $this->pyme;
		$cantPym = 1;

		if ($cantPym == 1) {
			$linkLog = ($this->linkArea != '') ? 'gerencia.php' : '../index.php';
			//----------------devBug------------------------------
			if ($GLOBALS['devBug'] == 1) {
				echo '<br>LinkArea: ' . $this->linkArea . '<br>';
				echo '<br>LinkLogo: ' . $linkLog . '<br>';
			}
			//-------------Finaliza devBug------------------------------

			return '
					<div class="desktoplogo"><a href="' . $linkLog . '"><img src="../assets/menu/images/logo.png" width="80%" alt=""></a></div>
				';
		} else {
			problemas('Se ha Bloqueado por Seguridad, por favor inténtalo de nuevo o notifica a tu Administrador... <br>Error: <b>SEG007</b>');
		}
	}
	#--FIN SEGURIDAD--

	#-----------------------  MENU DE AREAS DISPONIBLES  ------------------------------
	public function generaMenuUsuario()
	{
		require('../include/connect.php');

		$level = $this->idNivel;
		$area = $this->area;
		$pyme = $this->pyme;
		$sql = "SELECT
              DISTINCT(dtnvl.idArea) AS identArea, ars.*
          FROM segdetnivel dtnvl
          INNER JOIN segareas ars ON dtnvl.idArea = ars.id
          WHERE
          dtnvl.idNivel='$level'
          ORDER BY ars.orden DESC";
		//----------------devBug------------------------------
		if ($GLOBALS['devBug'] == 1) {
			$ars = mysqli_query($link, $sql) or die("<br><br>Error al Consultar las Áreas a las que puede Acceder el Usuario: " . mysqli_error($link) . '<br>SQL: ' . $sql);
			echo '<br>Validación de la Empresa y Sucursal del Usuario: ' . $sql . '<br>';
		} else {
			$ars = mysqli_query($link, $sql) or die(problemas('Se ha Bloqueado por Seguridad, por favor inténtalo de nuevo o notifica a tu Administrador... <br>Error: <b>SEG008</b>'));
		}
		//-------------Finaliza devBug------------------------------
		$cantArs = mysqli_num_rows($ars);
		if ($cantArs >= 1) {
			$arsLinks = '';
			while ($dat = mysqli_fetch_array($ars)) {
				$estatus = ($dat['identArea'] == $area) ? 'active' : '';
				$arsLinks .= '
							<li><a class="' . $estatus . '" href="../' . $dat['link'] . '"><i class="' . $dat['icono'] . '"></i> ' . $dat['nombre'] . '</a></li>';
			}
			$iconoImagen = ($_SESSION['CREgenero'] == 'Masculino') ? 'iconoH.png' : 'iconoM.png';
			return '
				<li aria-haspopup="true"><a href="#"><i class="fas fa-user-tie"></i> ' . $this->nombreUser . ' <span class="wsarrow"></span>
					</a>
					<ul class="sub-menu sub-session">

								<span class="with-arrow">
										<span class="bg-primary"></span>
								</span>

								<div class="d-flex no-block align-items-center p-15 bg-secondary text-white m-b-10">
										<div class="">
												<img src="../assets/images/users/' . $iconoImagen . '" alt="user" class="rounded-circle" width="60">
										</div>
										<div class="m-l-10">
												<h4 class="m-b-0"> ' . $this->nombreUser . ' </h4>
												<p class=" m-b-0"> ' . $this->nombreNivel . ' </p>
										</div>
								</div>

						' . $arsLinks . '
						<li><a href="../manuales-usuario.php" target="_blank"><span class=""><i class="fas fa-book"></i></span> Manuales de Usuarios</a></li>

						<div class="dropdown-divider"></div>
						<li><a href="../logout.php"><span class="text-danger"><i class="fas fa-sign-out-alt"></i></span> Cerrar Sesión</a></li>
					</ul>
				</li>';
		} else {
			problemas('Se ha Bloqueado por Seguridad, por favor inténtalo de nuevo o notifica a tu Administrador... <br>Error: <b>SEG007</b>');
		}
	}

	#-----------------------  MENU LATERAL  ------------------------------
	public function generaMenuLateral()
	{
		require('../include/connect.php');

		error_reporting(E_ALL);
		ini_set('display_errors', '1');

		$pagina = $this->pagina;
		$level = $this->idNivel;
		$area = $this->area;
		$idMenu = $this->idMenu;
		$idSubMenu = $this->idSubMenu;
		$idSubMenu2 = $this->idSubMenu2;

		$sql = "SELECT
							dtnvl.idMenu,
							dtnvl.idSubMenu,
							dtnvl.idSubMenu2 AS menuNivel3,
							mn.nombre AS menu,
							mn.descripcion AS menuDesc,
							mn.icono AS menuIco,
							mn.link AS menuLink,
							mn.tipo,
							mn.queryDatos,
							mn.urlContenido,
							mn.visible AS mnVisible,
							mn.usoLinkBase,
							sbmn.nombre AS sbmn,
							sbmn.descripcion AS sbmnDesc,
							sbmn.icono AS sbmnMenuIco,
							sbmn.link AS sbmnLink,
							sbmn.visible AS sbmnVisible,
							sbmn.estatus AS sbmnEstatus,
							sbmn2.nombre AS sbmn2,
							sbmn2.descripcion AS sbmn2Desc,
							sbmn2.icono AS sbmn2Ico,
							sbmn2.link AS sbmn2Link,
							sbmn2.visible AS sbmn2Visible,
							sbmn2.estatus AS sbmn2Estatus
            FROM
                segdetnivel dtnvl
						INNER JOIN segmenus mn ON dtnvl.idMenu = mn.id AND mn.estatus = 1
						LEFT JOIN segsubmenus sbmn ON dtnvl.idSubMenu = sbmn.id
						LEFT JOIN segsubmenu2 sbmn2 ON dtnvl.idSubMenu2 = sbmn2.id
            WHERE
                dtnvl.idNivel = '$level' AND dtnvl.idArea='$area'
            ORDER BY mn.orden, sbmn.orden, sbmn2.orden ASC";
		//----------------devBug------------------------------
		if ($GLOBALS['devBug'] == 1) {
			$res = mysqli_query($link, $sql) or die("Error de consultar Menu Lateral: " . mysqli_error($link) . '<br>SQL: ' . $sql);
			echo '<br>Menu de Usuario: ' . $sql . '<br>';
		} else {
			$res = mysqli_query($link, $sql) or die(problemas('Se ha Bloqueado por Seguridad, por favor inténtalo de nuevo o notifica a tu Administrador... <br>Error: <b>SEG010</b>'));
		}
		//-------------Finaliza devBug------------------------------

		$mnIdent = 0;
		$mnTipo = 0;
		$mnSub = 0;
		$mnSub2 = 0;
		$mnOpen = 0;
		$mnOpen2 = 0;
		$menuGen = '';

		//----------------devBug------------------------------
		if ($GLOBALS['devBug'] == 1) {
			echo '<br> Value idMenu: ' . $idMenu;
			echo '<br> Value idSubMenu: ' . $idSubMenu;
			echo '<br> Value idSubMenu2: ' . $idSubMenu2;
		}
		//-------------Finaliza devBug------------------------------

		while ($dat = mysqli_fetch_array($res)) {

			if ($dat['mnVisible'] == '1') {

				if ($dat['sbmnVisible'] == '0' or $dat['sbmn2Visible'] == '0' or $dat['sbmnEstatus'] == '0' or $dat['sbmn2Estatus'] == '0') {
					// No entra al Ciclo por que esta Oculto
					//debugConsole('No entro en el ID: '.$dat['idMenu'].','.$dat['idSubMenu'].','.$dat['menuNivel3']);

				} else {

					if ($mnIdent != $dat['idMenu']) {
						//Son distintos los Menu
						if ($mnOpen == 1) {
							//Cierra UL y Carga nuevo Menu
							if ($mnOpen2 == 1) {
								//Tiene UL2 Abierto :: Cierre de UL2
								$menuGen .= '
													</ul>
												</li>
									';
								$mnOpen2 = 0;
							}
							$menuGen .= '
												</ul>
											</li>
								';
							$mnOpen = 0;
						}

						/* ** GENERANDO POR TIPO ***/
						switch ($dat['tipo']) {
							case '1':
								// Genera Boton de Menu Simple
								$mnAct = ($idMenu == $dat['idMenu']) ? 'active' : '';
								if ($dat['menu'] == 'Inicio') {
									$menuGen .= '
										<li aria-haspopup="true"><a href="' . $dat['menuLink'] . '" class="' . $mnAct . ' menuhomeicon"><i class="' . $dat['menuIco'] . '"></i><span class="hometext"> ' . $dat['menu'] . '</span></a></li>';
								} else {
									$menuGen .= '
										<li aria-haspopup="true"><a href="' . $dat['menuLink'] . '" class="' . $mnAct . '"><i class="' . $dat['menuIco'] . '"></i> ' . $dat['menu'] . '</a></li>';
								}
								break;

							case '2':
								// Genera Boton de Menu y Deja abierto el registro de Submenus simples y en caso de tener un Tercer nivel tambien lo abre
								$mnAct = ($idMenu == $dat['idMenu']) ? 'active' : '';
								$mnLnk = (empty($dat['menuLink'])) ? '#' : $dat['menuLink'];
								$sbmnIco = (empty($dat['sbmnMenuIco'])) ? 'fas fa-angle-right' : $dat['sbmnMenuIco'];
								$sbmnAct = ($idSubMenu == $dat['idSubMenu']) ? 'active' : '';
								$sbmnLnk = (empty($dat['sbmnLink'])) ? '#' : $dat['sbmnLink'];
								$mnLnk = ($dat['usoLinkBase'] == '0') ? '#' : $mnLnk;

								$menuGen .= '
										<li aria-haspopup="true"><a href="' . $mnLnk . '" class="' . $sbmnAct . '"><i class="' . $dat['menuIco'] . '"></i> ' . $dat['menu'] . ' <span class="wsarrow"></span></a>
											<ul class="sub-menu">
												<li aria-haspopup="true"><a href="' . $sbmnLnk . '" class="' . $sbmnAct . '"><i class="' . $sbmnIco . '"></i> ' . $dat['sbmn'] . '</a>';
								$mnOpen = 1;
								$mnSub = $dat['idSubMenu'];

								if ($dat['menuNivel3'] >= 1) {

									$sbmnLnk2 = (empty($dat['sbmn2Link'])) ? '#' : $dat['sbmn2Link'];
									$sbmnIco2 = (empty($dat['sbmn2Ico'])) ? 'fas fa-angle-right' : $dat['sbmn2Ico'];
									$sbmnAct2 = ($idSubMenu2 == $dat['menuNivel3']) ? 'active' : '';
									$menuGen .= '
													<ul class="sub-menu">
														<li aria-haspopup="true"><a href="' . $sbmnLnk2 . '" class="' . $sbmnAct2 . '"><i class="' . $sbmnIco2 . '"></i>' . $dat['sbmn2'] . '</a></li>';
									$mnOpen2 = 1;
								}


								break;

							case '3':
								// Genera Boton de Menu con Submenu, el submenu trae imagen, titulo y descripcion
								$mnAct = '';
								$querySubLink = $dat['queryDatos'];
								//----------------devBug------------------------------
								if ($GLOBALS['devBug'] == 1) {
									$resQuery = mysqli_query($link, $querySubLink) or die("Error de consultar Query Cargada en Menu: " . mysqli_error($link) . '<br>SQL: ' . $querySubLink);
									echo '<br>Query Cargada en Menu: ' . $sql . '<br>';
								} else {
									$resQuery = mysqli_query($link, $querySubLink) or die(problemas('Se ha Bloqueado por Seguridad, por favor inténtalo de nuevo o notifica a tu Administrador... <br>Error: <b>SEG0100</b>'));
								}
								//-------------Finaliza devBug------------------------------

								$cantResQuery = mysqli_num_rows($resQuery);
								$subMenuCont = '';
								$activSub = 0;
								while ($qry = mysqli_fetch_array($resQuery)) {
									$tamañoGrid = ($cantResQuery <= 6) ? 2 : 3;
									$tamañoGrid = ($cantResQuery <= 4) ? 3 : $tamañoGrid;
									$tamañoGrid = ($cantResQuery <= 3) ? 4 : $tamañoGrid;

									$cadena_de_texto = $qry['link'];
									$coincidenciaUrl = strrpos($cadena_de_texto, $pagina);
									$activSub = ($coincidenciaUrl === true) ? 1 : $activSub;

									$title = (empty($qry['titulo'])) ? '' : '<h3 class="title"> ' . $qry['titulo'] . ' </h3>';
									$description = (empty($qry['descripcion'])) ? '' : '<p class="wsmwnutxt"> ' . $qry['descripcion'] . ' </p>';
									$subMenuCont .= '
										<div class="col-lg-' . $tamañoGrid . ' col-md-12 col-xs-12">
											<div class="text-center fluid-width-video-wrapper"><a href="' . $qry['link'] . '"><img src="../' . $qry['img'] . '" alt="" /></a> </div>
											' . $title . '
											' . $description . '
										</div>
										';
								}

								$mnAct = ($idMenu == $dat['idMenu'] or $activSub == 1) ? 'active' : $mnAct;
								$mnLnk = (empty($dat['menuLink'])) ? '#' : $dat['menuLink'];
								$mnLnk = ($dat['usoLinkBase'] == '0') ? '#' : $mnLnk;
								$menuGen .= '
									<li aria-haspopup="true"><a href="' . $mnLnk . '" class="' . $mnAct . '"><i class="' . $dat['menuIco'] . '"></i> ' . $dat['menu'] . ' <span class="wsarrow"></span></a>
										<div class="wsmegamenu clearfix ">
											<div class="container-fluid">
												<div class="row">
													' . $subMenuCont . '
												</div>
											</div>
										</div>
									</li>
									';
								break;

							case '4':
								// Genera Boton de Menu y Deja abierto el registro de Submenus simples y en caso de tener un Tercer nivel tambien lo abre
								$mnAct = ($idMenu == $dat['idMenu']) ? 'active' : '';
								$mnLnk = (empty($dat['menuLink'])) ? '#' : $dat['menuLink'];
								$sbmnIco = (empty($dat['sbmnMenuIco'])) ? 'fas fa-angle-right' : $dat['sbmnMenuIco'];
								$sbmnAct = ($idSubMenu == $dat['idSubMenu']) ? 'active' : '';
								$sbmnLnk = (empty($dat['sbmnLink'])) ? '#' : $dat['sbmnLink'];
								$mnLnk = ($dat['usoLinkBase'] == '0') ? '#' : $mnLnk;

								$menuGen .= '
										<li aria-haspopup="true"><a href="' . $mnLnk . '" class="' . $sbmnAct . '"><i class="' . $dat['menuIco'] . '"></i> ' . $dat['menu'] . ' <span class="wsarrow"></span></a>
										<div class="wsmegamenu clearfix">
											<div class="container-fluid">
												<div class="row">

													<ul class="col-lg-3 col-md-12 col-xs-12 link-list">
														<li class="title"><i class="' . $sbmnIco . '"></i> ' . $dat['sbmn'] . '</li>';
								$mnOpen = 1;
								$mnSub = $dat['idSubMenu'];

								if ($dat['menuNivel3'] >= 1) {
									$sbmnLnk2 = (empty($dat['sbmn2Link'])) ? '#' : $dat['sbmn2Link'];
									$sbmnIco2 = (empty($dat['sbmn2Ico'])) ? 'fas fa-angle-right' : $dat['sbmn2Ico'];
									$sbmnAct2 = ($idSubMenu2 == $dat['menuNivel3']) ? 'active' : '';
									$menuGen .= '
														<li><a href="' . $sbmnLnk2 . '" class="' . $sbmnAct2 . '"><i class="' . $sbmnIco2 . '"></i>' . $dat['sbmn2'] . '</a></li>';
									$mnOpen2 = 1;
								}

								break;

							case '5':
								// code...
								break;

							case '6':
								// code...
								break;

							default:
								$menuGen .= '';
								break;
						}
					} else {
						//Son iguales los idMenu
						if ($mnOpen == 1) {
							//Son iguales los idMenu y tiene UL abierto en Submenu
							if ($mnSub == $dat['idSubMenu']) {
								//Son iguales los idMenu, tiene UL abierto en Submenu y los Submenus son iguales
								if ($dat['menuNivel3'] >= 1) {
									//Son iguales los idMenu, tiene UL abierto en Submenu, los Submenus son iguales y tiene el Nivel3
									if ($mnOpen2 == 1) {
										//Son iguales los idMenu, tiene UL abierto en Submenu, los Submenus son iguales, tiene el Nivel3 y tiene Abierto el UL 3 :: Solo Incrusta nuevo Nivel3 cerrado
										$sbmnLnk2 = (empty($dat['sbmn2Link'])) ? '#' : $dat['sbmn2Link'];
										$sbmnIco2 = (empty($dat['sbmn2Ico'])) ? 'fas fa-angle-right' : $dat['sbmn2Ico'];
										$sbmnAct2 = ($idSubMenu2 == $dat['menuNivel3']) ? 'active' : '';
										$menuGen .= '
															<li aria-haspopup="true"><a href="' . $sbmnLnk2 . '" class="' . $sbmnAct2 . '"><i class="' . $sbmnIco2 . '"></i>' . $dat['sbmn2'] . '</a></li>';

										$mnSub2 = $dat['menuNivel3'];
									} else {
										// code...
									}
								} else {
									// code...
								}

								$mnSub = $dat['idSubMenu'];
							} else {
								//Son iguales los idMenu, tiene UL abierto en Submenu pero Submenus distintos :: Cierra UL y Carga nuevo Submenu
								if ($mnOpen2 == 1) {
									//Tiene UL2 Abierto :: Cierre de UL2
									$menuGen .= '
														</ul>
													</li>
										';
									$mnOpen2 = 0;
								}

								if ($dat['menuNivel3'] >= 1) {
									// Si tiene Nivel 3	:: Registro de Submenu Abierto con su TecerNivel
									$sbmnIco = (empty($dat['subMenuIco'])) ? 'fas fa-angle-right' : $dat['subMenuIco'];
									$sbmnAct = ($idSubMenu == $dat['idSubMenu']) ? 'active' : '';
									$sbmnLnk = (empty($dat['sbmnLink'])) ? '#' : $dat['sbmnLink'];

									$menuGen .= '
													<li aria-haspopup="true"><a href="' . $sbmnLnk . '" class="' . $sbmnAct . '"><i class="' . $sbmnIco . '"></i> ' . $dat['sbmn'] . '</a>';
									$mnSub = $dat['idSubMenu'];

									$sbmnLnk2 = (empty($dat['sbmn2Link'])) ? '#' : $dat['sbmn2Link'];
									$sbmnIco2 = (empty($dat['subMenuIco2'])) ? 'fas fa-angle-right' : $dat['subMenuIco2'];
									$sbmnAct2 = ($idSubMenu2 == $dat['menuNivel3']) ? 'active' : '';
									$menuGen .= '
													<ul class="sub-menu">
														<li aria-haspopup="true"><a href="' . $sbmnLnk2 . '" class="' . $sbmnAct2 . '"><i class="' . $sbmnIco2 . '"></i>' . $dat['sbmn2'] . '</a></li>';
									$mnOpen2 = 1;
									$mnSub2 = $dat['menuNivel3'];
								} else {
									// NO tiene Nivel 3	:: Registro de Submenu Directo(Cerrado) no requiere dependencias
									$sbmnIco = (empty($dat['subMenuIco'])) ? 'fas fa-angle-right' : $dat['subMenuIco'];
									$sbmnAct = ($idSubMenu == $dat['idSubMenu']) ? 'active' : '';
									$sbmnLnk = (empty($dat['sbmnLink'])) ? '#' : $dat['sbmnLink'];

									$menuGen .= '
													<li aria-haspopup="true"><a href="' . $sbmnLnk . '" class="' . $sbmnAct . '"><i class="' . $sbmnIco . '"></i> ' . $dat['sbmn'] . '</a></li>';
									$mnSub = $dat['idSubMenu'];
								}
							}
						}
					} // Termina ELSE
					$mnIdent = $dat['idMenu'];
					$mnTipo = $dat['tipo'];
				} // Fin de IF de visualización para subMenus
			} // Fin de IF de visualización
		}	// Termina WHILE

		if ($mnOpen2 == 1) {
			switch ($mnTipo) {
				case '2':
					$menuGen .= '
													</ul>
												</li>
												';
					break;

				case '4':
					$menuGen .= '
													</ul>
												';
					break;

				default:
					// code...
					break;
			}
		}

		if ($mnOpen == 1) {
			switch ($mnTipo) {
				case '2':
					$menuGen .= '
												</ul>
											</li>
											';
					break;

				case '4':
					$menuGen .= '
                        </div>
                      </div>
                    </div>
                  </li>
												';
					break;

				default:
					// code...
					break;
			}
		}


		return $menuGen;
	}

	#-----------------------  CREA HEADER CON MENU  ------------------------------
	public function creaHeaderConMenu()
	{
		echo '<header class="topbar">';
		$custom = $this->customizerMobil();
		echo $custom;

		echo '
							<div class="wsmainfull clearfix">
								<div class="wsmainwp clearfix">
			';

		$customDesk = $this->customizerDesktop();
		echo $customDesk;

		echo '

		              <!--Main Menu HTML Code-->
		              <nav class="wsmenu clearfix">
		                <ul class="wsmenu-list">';
		echo $this->generaMenuLateral();
		echo $this->generaMenuUsuario();
		echo '
		                </ul>
		              </nav>

		              <!--Menu HTML Code-->
		            </div>
		          </div>

		        </header>
			';
	}

	#-----------------------  CREA FOOTER  ------------------------------
	public function creaFooter()
	{
		$footer = '
			<footer class="footer text-center">
				    Todos los derechos reservados
					<a href="https://temola.com.mx/?lang=es">TWM </a>.
			</footer>
			';

		return $footer;
	}
}

function identificaAccess()
{
	$precad = explode('/', strip_tags($_SERVER["REQUEST_URI"]));
	$precantCad = COUNT($precad);
	$prenameLk = $precad[$precantCad - 1];
	//----------------devBug------------------------------
	if ($GLOBALS['devBug'] == 1) {
		echo 'uri: ' . $_SERVER["REQUEST_URI"] . '<br>';
		echo 'preCantCad: ' . $precantCad . '<br>';
		echo 'prelink: ' . $prenameLk . '<br><br>';
	}
	//-------------Finaliza devBug------------------------------

	$cad = explode('?', $prenameLk);
	$cantcad = COUNT($cad);
	$nameLk = $cad[0];

	//----------------devBug------------------------------
	if ($GLOBALS['devBug'] == 1) {
		echo 'cantCad: ' . $cantcad . '<br>';
		echo 'link: ' . $nameLk . '<br><br>';
	}
	//-------------Finaliza devBug------------------------------

	return $nameLk;
}

function problemas($msj)
{
	if ($GLOBALS['devBug'] == 1) {
		echo '<br><hr><br>MSJ ERROR: ' . $msj . '<br><br><hr><br>';
	} else {
		$_SESSION['CREacceso'] = $msj;
		header('location: ../index.php');
	}
	exit(0);
}

function negado($link, $msj)
{
	if ($GLOBALS['devBug'] == 1) {
		echo '<br><hr><br>*** MSJ ACCESO NEGADO: ' . $msj . '<br><br><hr><br>';
	} else {
		$_SESSION['CREmsjGralPlatform'] = $msj;
		header('location: ' . $link);
	}
	exit(0);
}

function debugConsole($data)
{
	$output = $data;
	if (is_array($output))
		$output = implode(',', $output);

	echo "<script>console.log( 'Debug Objects: " . $output . "' );</script>";
}
