<?php
class Venta extends ConexionBD
{
    protected $debug;
    private $idUserReg;


    public function __construct($debug, $idUserReg)
    {
        $this->initConexion();
        $this->debug = $debug;
        $this->idUserReg = $idUserReg;
    }

    public function __destruct()
    {
        $this->close();
    }

    public function busquedaVenta($numFactura, $filtradoVenta = "1=1")
    {

        $sql = "SELECT v.id FROM ventas v WHERE  numFactura='$numFactura' AND estado>'0' AND $filtradoVenta
                UNION
                SELECT v.id FROM ventasxdevoluc v WHERE  numFactura='$numFactura' AND estado>'0' AND $filtradoVenta";
        return  $this->ejecutarQuery($sql, "consultar Venta", true);
    }

    public function busquedaPL($numPL, $filtradoVenta = "1=1")
    {

        $sql = "SELECT v.id FROM ventas v WHERE  numPL='$numPL' AND estado>'0' AND $filtradoVenta
                UNION
                SELECT v.id FROM ventasxdevoluc v WHERE  numPL='$numPL' AND estado>'0' AND $filtradoVenta";
        return  $this->ejecutarQuery($sql, "consultar Venta", true);
    }

    public function consultaUnidadesVentas()
    {
        $idUserReg = $this->idUserReg;
        $sql = "SELECT v.id, SUM(dv.unidades) articulosVendidos FROM ventas v 
        INNER JOIN detventas dv ON v.id=dv.idVenta
        WHERE v.estado='1' AND v.idUserReg='$idUserReg'
        GROUP BY dv.idVenta";
        return $this->consultarQuery($sql, "Consultar Venta", false);
    }

    public function initVenta($fehaFacturacion, $numFactura, $numPL, $idTipoVenta)
    {
        $idUserReg = $this->idUserReg;
        $sql = "INSERT INTO ventas(fechaFact, numFactura, numPL, estado, fechaReg, idUserReg, idTipoVenta) VALUES
                ('$fehaFacturacion', '$numFactura', '$numPL', '1', NOW(), '$idUserReg', '$idTipoVenta')";
        return $this->ejecutarQuery($sql, "Iniciar Venta");
    }
    public function editarVenta($id, $fehaFacturacion, $numFactura, $numPL, $idTipoVenta)
    {
        $idUserReg = $this->idUserReg;
        $sql = "UPDATE ventas SET fechaFact='$fehaFacturacion', numFactura='$numFactura', numPL='$numPL',
         fechaReg=NOW(), idUserReg='$idUserReg', idTipoVenta='$idTipoVenta' WHERE id='$id' ";
        return $this->ejecutarQuery($sql, "Editar Venta");
    }
    public function addLoteVenta($idRendimiento, $cantidad, $sets,  $tipo)
    {
        $idUserReg = $this->idUserReg;
        $DataVenta = $this->getVentaAbiertaXUser();
        $idVenta = $DataVenta[0]['id'];
        $sql = "INSERT INTO detventas(idVenta, idRendimiento,unidades,sets, fechaReg, idUserReg, cantFinalAlm, tipoLote, distribuido, devuelto) VALUES
                ('$idVenta', '$idRendimiento', '$cantidad','$sets',  NOW(), '$idUserReg', '0', '$tipo', '0', '0')";
        return $this->ejecutarQuery($sql, "Agregar Lote a  Venta");
    }


    /********************************
     *PROCESO: ELIMINACION DE DETALLE DE VENTAS 
     ********************************/

    public function eliminarDetVenta($id)
    {
        $sql = "DELETE FROM detventas WHERE id='$id'";
        return $this->runQuery($sql, "Eliminar Detallado de la Venta");
    }

    /********************************
     *PROCESO: CANCELACION DE VENTAS 
     ********************************/
    public function eliminarVenta()
    {
        $DataVenta = $this->getVentaAbiertaXUser();
        $idVenta = $DataVenta[0]['id'];
        $sql = "DELETE v, dv FROM ventas v
                LEFT JOIN detventas dv ON dv.idVenta= v.id
                WHERE v.id='$idVenta'";
        return $this->runQuery($sql, "Eliminar Detallado de la Venta");
    }
    public function eliminarApartadoCajas()
    {
        $DataVenta = $this->getVentaAbiertaXUser();
        $idVenta = $DataVenta[0]['id'];
        $sql = "UPDATE detcajas 
        SET vendida='0', idVenta='' 
        WHERE idVenta='$idVenta'";
        return $this->ejecutarQuery($sql, "Quitar cajas de ventas");
    }


    public function finalizarVenta($estado = '2')
    {
        $DataVenta = $this->getVentaAbiertaXUser();
        $idVenta = $DataVenta[0]['id'];
        // if ($DataVenta[0]['tipoLote'] == '1') {

        $sql = "UPDATE ventas v 
            INNER JOIN detventas dv ON v.id=dv.idVenta
            INNER JOIN rendimientos r ON dv.idRendimiento= r.id
            SET v.estado='$estado', v.unidFact=( SELECT * FROM (SELECT SUM(unidades) FROM detventas WHERE idVenta='$idVenta' ) AS vtas), v.fechaReg= NOW()
            WHERE v.id='$idVenta' AND dv.tipoLote='1'";
        $this->ejecutarQuery($sql, "Finalizar Detallado de la Venta");

        //  } else if ($DataVenta[0]['tipoLote'] == '2') {

        /*  $sql = "UPDATE ventas v 
            INNER JOIN detventas dv ON v.id=dv.idVenta
            INNER JOIN rendimientosetiquetas r ON dv.idRendimiento= r.id
            SET v.estado='2', v.unidFact=( SELECT * FROM (SELECT SUM(unidades) FROM detventas WHERE idVenta='$idVenta' ) AS vtas), v.fechaReg= NOW(), 
            r.almacenPT=dv.cantFinalAlm, r.estado=IF(dv.cantFinalAlm=0,'4', r.estado)
            WHERE v.id='$idVenta'  AND dv.tipoLote='2'";*/
        return $this->ejecutarQuery($sql, "Finalizar Detallado de la Venta");
        // }
    }

    public function disminuirSubLotes()
    {
        $DataVenta = $this->getVentaAbiertaXUser();
        $idVenta = $DataVenta[0]['id'];
        $sql = "UPDATE sublotesrecuperados s 
        INNER JOIN detventaslotes dvl ON dvl.idSubLote=s.id
        INNER JOIN detventas dv ON dv.id=dvl.idDetVenta
        INNER JOIN config_inventarios conf ON conf.estado='1'
        SET
                s.pzasTotales= s.pzasTotales- dvl.unidades,
                s.setsEmpacados = (s.pzasTotales- dvl.unidades)/conf.pzasEnSets,
                dvl.cantFinalAlm= s.pzasTotales- dvl.unidades
        WHERE dv.idVenta='$idVenta'";
        return $this->ejecutarQuery($sql, "actualizar lotes");
    }

    public function disminuirInventarioEmpacado()
    {
        $DataVenta = $this->getVentaAbiertaXUser();
        $idVenta = $DataVenta[0]['id'];
        $sql = "UPDATE inventarioempacado e
          INNER JOIN  (
            SELECT dv.idRendimiento, SUM(dv.unidades) AS totalUnidades FROM detventas dv 
            WHERE dv.idVenta='$idVenta'
            GROUP BY dv.idRendimiento
        )dv ON dv.idRendimiento=e.idRendimiento
        INNER JOIN config_inventarios conf ON conf.estado='1'
        SET
        e.pzasTotales= e.pzasTotales-totalUnidades, e.setsTotales=(e.pzasTotales-totalUnidades)/conf.pzasEnSets
      ";
        return $this->ejecutarQuery($sql, "actualizar inventario");
    }

    public function validarRequirimientoEnVenta()
    {
        $idUserReg = $this->idUserReg;
        $sql = "SELECT abs.totalRequeri, abs.totalAbastecidos FROM detventasprevias dvp
        INNER JOIN detventas dv ON dvp.idDetVenta=dv.id
        INNER JOIN ventas v ON dv.idVenta=v.id
        LEFT JOIN (SELECT dvp.*, dv.idVenta, count(dv.idVenta) AS totalRequeri,
                sum(IF(vw.totalEmp>=dvp.pzasTotales, 1,0)) AS totalAbastecidos
                FROM detventasprevias dvp
                INNER JOIN detventas dv ON dv.id=dvp.idDetVenta
                INNER JOIN ventas v ON dv.idVenta=v.id
                INNER JOIN vw_inventariolotes vw ON dv.idRendimiento=vw.id
                INNER JOIN rendimientos r ON dv.idRendimiento=r.id
                INNER JOIN catprogramas cp ON r.idCatPrograma=cp.id
                GROUP BY dv.idVenta)abs ON abs.idVenta=v.id 
        WHERE v.idUserReg='$idUserReg' AND v.estado='1'";
        return  $this->consultarQuery($sql, "consultar Requerimientos en Ventas", false);
    }

    public function validarDistribuicionLotes()
    {
        $idUserReg = $this->idUserReg;

        $sql = "SELECT GROUP_CONCAT(r.loteTemola) AS lotesNoValidos FROM ventas v 
        INNER JOIN detventas dv ON v.id=dv.idVenta
        LEFT JOIN rendimientos r ON dv.idRendimiento=r.id 
        WHERE v.idUserReg='$idUserReg' AND v.estado='1' AND 
        (dv.distribuido='0' OR dv.distribuido IS NULL)
        GROUP BY v.id";
        return  $this->ejecutarQuery($sql, "consultar Venta Abierta", true);
    }

    public function addSubLote($query)
    {
        $sql = "INSERT INTO detventaslotes (idSubLote, unidades,sets, fechaReg, idUserReg, idDetVenta) VALUES 
              $query";
        return  $this->ejecutarQuery($sql, "agregar Sublotes");
    }

    public function agregarTotalDetVenta($idDetVenta)
    {
        $sql = "UPDATE detventas dv 
        INNER JOIN (SELECT dv.idDetVenta, SUM(dv.unidades) totalUnidades FROM 
        detventaslotes dv
        WHERE dv.idDetVenta='$idDetVenta'
        GROUP BY dv.idDetVenta) dvtotal ON dv.id=dvtotal.idDetVenta
        INNER JOIN config_inventarios conf ON conf.estado='1'
        SET dv.unidades=dvtotal.totalUnidades, dv.sets= dvtotal.totalUnidades/conf.pzasEnSets
        WHERE dv.id='$idDetVenta'";
        return  $this->ejecutarQuery($sql, "actualizar total de Venta");
    }

    public function getVentaAbiertaXUser()
    {
        $idUserReg = $this->idUserReg;
        $sql = "SELECT v.* FROM ventas v WHERE v.idUserReg='$idUserReg' AND v.estado='1'";
        return  $this->ejecutarQuery($sql, "consultar Venta Abierta", true);
    }

    public function getDetalladoVenta()
    {
        $DataVenta = $this->getVentaAbiertaXUser();
        $idVenta = $DataVenta[0]['id'];
        $sql = "SELECT dv.id, r.loteTemola, dv.unidades, dv.unidades/conf.pzasEnSets AS totalSets,
        (i.pzasTotales-dv.unidades) AS almacenPT , dv.distribuido, dv.idRendimiento, dv.idVenta
        FROM detventas dv 
        INNER JOIN inventarioempacado i ON i.idRendimiento= dv.idRendimiento
        INNER JOIN rendimientos r ON i.idRendimiento=r.id
        INNER JOIN config_inventarios conf ON conf.estado='1'
        WHERE  dv.idVenta='$idVenta'";
        return  $this->ejecutarQuery($sql, "consultar Detallado de la Venta", true);
    }

    public function paseAlmacenPT($idDetVenta, $cantidad)
    {

        $sql = "UPDATE detventas d SET d.cantFinalAlm='$cantidad' WHERE id='$idDetVenta' ";
        return $this->ejecutarQuery($sql, "actualizar Almacen PT del Detalle de Venta");
    }

    public function getVentasCerradas($filtradoFecha = '1=1', $filtradoTipo = '1=1')
    {
        $sql = "SELECT v.*, tv.nombre AS n_tipo,
                DATE_FORMAT(v.fechaFact, '%d-%m-%Y') AS f_fechaFact,
                DATE_FORMAT(v.fechaReg, '%d-%m-%Y %H:%i') AS f_fechaReg,
                CONCAT(u.nombre, ' ', u.apellidos) AS str_usuario,
                v.unidFact/conf.pzasEnSets AS _sets, IFNULL(dev.totalDevol, 0) AS totalDevol,
                dev.rmas, l.lotes
        
        FROM ventas v
        INNER JOIN cattiposventas tv ON v.idTipoVenta=tv.id
        INNER JOIN segusuarios u ON v.idUserReg=u.id
        INNER JOIN config_inventarios conf ON conf.estado='1'
        LEFT JOIN (SELECT idVenta, COUNT(id)  AS totalDevol, GROUP_CONCAT(CONCAT('RMA: ',rma)) AS rmas
						FROM devolucionesrma 
                        WHERE estado='2' GROUP BY idVenta) dev ON dev.idVenta=v.id
        LEFT JOIN (SELECT idVenta, GROUP_CONCAT(r.loteTemola) AS lotes FROM detventas dv
					INNER JOIN rendimientos r ON dv.idRendimiento=r.id
				GROUP BY idVenta) l ON l.idVenta=v.id				
        WHERE (v.estado='2' OR v.estado='0') AND $filtradoFecha AND $filtradoTipo
        ORDER BY v.fechaFact DESC";
        return  $this->ejecutarQuery($sql, "consultar Detallado de la Venta", true);
    }

    public function getLoteXVenta($id)
    {
        $sql = "SELECT r.loteTemola,
        cp.codigo AS c_proceso, cm.nombre AS n_materia, dv.unidades, dv.1s, dv.2s, dv.3s, dv.4s, dv.total_s,
        pr.nombre AS n_programa, dv.id, dv.devuelto
        FROM  detventas dv 
        INNER JOIN rendimientos r ON dv.idRendimiento=r.id
        INNER JOIN catmateriasprimas cm ON r.idCatMateriaPrima=cm.id
        INNER JOIN catprocesos cp ON r.idCatProceso=cp.id
        INNER JOIN catprogramas pr ON pr.id = r.idCatPrograma

        WHERE dv.idVenta='$id' AND dv.tipoLote='1'";
        return  $this->ejecutarQuery($sql, "consultar Lotes de un Pedido", true);
    }

    public function getEtiquetaXVenta($id)
    {
        $sql = "SELECT r.loteTemola, r.areaFinal, IFNULL(dv.cantFinalAlm,0) AS almacenPT,
           cm.nombre AS n_materia, dv.unidades
        FROM detventas dv
				INNER JOIN rendimientosetiquetas r ON dv.idRendimiento=r.id
        INNER JOIN catmateriasprimas cm ON r.idCatMateriaPrima=cm.id
        WHERE dv.idVenta='$id'  AND dv.tipoLote='2'";
        return  $this->ejecutarQuery($sql, "consultar Lotes de un Pedido", true);
    }

    public function getDetVentas($idVenta)
    {
        $sql = "SELECT v.*, cv.tipo, cv.nombre AS n_tipoVenta, DATE_FORMAT(v.fechaFact, '%d/%m/%Y') AS f_fechaFact FROM ventas v
        INNER JOIN cattiposventas cv ON v.idTipoVenta=cv.id
         WHERE v.id='$idVenta'";
        return  $this->ejecutarQuery($sql, "consultar Detalle de la Venta", true);
    }

    public function getVentasXMes($Anio_Mes)
    {
        $sql = "SELECT COUNT(v.id) totalVenta FROM ventas v
        WHERE v.estado>='2' AND DATE_FORMAT(v.fechaFact,'%Y-%m')='$Anio_Mes'";
        return  $this->ejecutarQuery($sql, "consultar Totales de Venta", true);
    }

    public function getClasifXVenta($id)
    {
        $sql = "SELECT dv.idRendimiento, dv.id, dv.1s, dv.2s, dv.3s, 
        dv.4s,dv._20, dv.total_s, r.tipoMateriaPrima, r.1s AS 1sRend, r.2s AS 2sRend, r.3s AS 3sRend,
        r.4s AS 4sRend,r._20 AS _20Rend, r.total_s AS totalRend, r.piezasRechazadas  
        FROM detventas dv
        INNER JOIN rendimientos r ON dv.idRendimiento=r.id
        WHERE dv.id='$id'";
        return  $this->ejecutarQuery($sql, "consultar Clasificación de la Venta", true);
    }

    public function getSubLotes($id, $idDetVenta)
    {
        $sql = "SELECT s.id, s.loteTemola, s.pzasTotales, s.setsEmpacados,
        IFNULL(dvl.unidades, 0) AS unidades
        FROM sublotesrecuperados s
        LEFT JOIN detventaslotes dvl ON dvl.idSubLote=s.id AND 
                dvl.idDetVenta='$idDetVenta'
        WHERE s.idRendimiento='$id' AND s.pzasTotales>0 
        ORDER BY s.loteTemola";
        return  $this->ejecutarQuery($sql, "consultar SubLotes del Rendimiento", true);
    }

    public function getDetVenta($idDetVenta)
    {
        $sql = "SELECT * FROM detventas WHERE id='$idDetVenta'";
        return  $this->ejecutarQuery($sql, "consultar Detallado de Venta", true);
    }

    public function getCorteVenta($filtradoMes)
    {
        $sql = "SELECT 
        dv.idRendimiento, r.loteTemola, v.fechaFact, 
        DAYOFWEEK(v.fechaFact) Num_DayOfWeek, 
        @operacion_days:=(4-DAYOFWEEK(v.fechaFact)) AS operacion_days, 
        @operInit_days:=IF(@operacion_days='1', -6,@operacion_days) operInit_days,
        @f_LimitInitWeek:=ADDDATE(v.fechaFact, INTERVAL @operInit_days DAY) f_LimitInitWeek, 
        ADDDATE(@f_LimitInitWeek, INTERVAL 6 DAY) f_LimitFinWeek, 
        DATE_FORMAT(ADDDATE(v.fechaFact, INTERVAL @operInit_days DAY), '%d/%m/%Y') format_LimitInitWeek, 
        DATE_FORMAT(ADDDATE(@f_LimitInitWeek, INTERVAL 6 DAY), '%d/%m/%Y') format_LimitFinWeek, 
        dv.tipoLote, 
        IF(cv.cargaVenta='1', CONCAT(FORMAT(SUM(dv.unidades)/conf.pzasEnSets, 2), ' sets'), CONCAT(FORMAT(SUM(dv.unidades),2), ' m<sup>2</sup>'))  sum_Unidades 
        FROM ventas v 
        INNER JOIN detventas dv ON v.id=dv.idVenta 
        INNER JOIN rendimientos r ON dv.idRendimiento=r.id
        INNER JOIN cattiposventas cv ON v.idTipoVenta=cv.id
        INNER JOIN config_inventarios conf ON conf.estado='1'
        WHERE v.estado='2' AND $filtradoMes
        GROUP BY f_LimitInitWeek,f_LimitFinWeek , dv.idRendimiento, dv.tipoLote ORDER BY r.loteTemola, f_LimitInitWeek ASC ";
        return  $this->ejecutarQuery($sql, "consultar Corte de Venta", true);
    }
    public function getTotalClasifUtilizado($id)
    {
        $sql = "SELECT IFNULL(idRendimiento, '$id') AS idRendimiento, IFNULL(SUM(dv.1s),0) AS t_1s, IFNULL(SUM(dv.2s),0) AS t_2s,
        IFNULL(SUM(dv.3s),0) AS t_3s, IFNULL(SUM(dv.4s),0) AS t_4s, IFNULL(SUM(dv._20),0) AS t_20, IFNULL(SUM(dv.total_s),0) AS t_total_s 
        FROM detventas dv
        INNER JOIN ventas v ON dv.idVenta=v.id
        WHERE dv.idRendimiento='$id' AND 
        v.estado='2'";
        return  $this->consultarQuery($sql, "consultar total de clasificación", false);
    }

    public function controlVentaHistorico($id, $motivo)
    {
        $idUserReg = $this->idUserReg;
        $sql = "INSERT INTO ctrledicionventas (motivo, idVenta, fechaFact, numFact, idTipoVenta, numPL, fechaReg, idUserReg)
                SELECT  '$motivo', id, fechaFact, numFactura, idTipoVenta, numPL, NOW(), '$idUserReg' FROM ventas WHERE id='$id'";
        return $this->ejecutarQuery($sql, "guardar almacenamiento de control de edición");
    }

    public function getEdicionesVentas($filtradoFecha = "1=1", $filtradoTipo = "1=1")
    {
        $sql = "SELECT ctrl.id, ctrl.motivo, v.numPL, v.numFactura,
        DATE_FORMAT(ctrl.fechaReg,'%d/%m/%Y %H:%i') AS f_fechaReg,
        CONCAT(u.nombre,' ',u.apellidos) AS str_usuario, ctv.nombre AS n_tipoventa,
        DATE_FORMAT(v.fechaFact,'%d/%m/%Y') AS f_fechaFact,  v.unidFact/conf.pzasEnSets AS _sets
        FROM ctrledicionventas ctrl 
        INNER JOIN ventas v ON ctrl.idVenta=v.id
        INNER JOIN cattiposventas ctv ON v.idTipoVenta=ctv.id
        INNER JOIN segusuarios u ON ctrl.idUserReg=u.id
        INNER JOIN config_inventarios conf ON conf.estado='1'
        WHERE $filtradoFecha AND $filtradoTipo
        ORDER BY ctrl.fechaReg DESC";
        return  $this->ejecutarQuery($sql, "consultar Ediciones de Venta", true);
    }

    public function getLotesXVender($filtradoFecha = "1=1", $filtradoMateria = "1=1", $filtradoPrograma = "1=1")
    {
        $sql = "SELECT r.loteTemola, r.semanaProduccion,
                DATE_FORMAT(r.fechaEmpaque, '%d/%m/%Y') as f_fechaEmpaque, 
                FORMAT(ie.pzasTotales,2) AS pzasTotalInventario,
                cm.nombre AS n_materia, cp.nombre AS n_programa,
                CONCAT(cpr.codigo, '-',cpr.nombre) AS n_proceso, r.tipoProceso
        FROM rendimientos r
        LEFT JOIN detventas dv ON r.id=dv.idRendimiento
        LEFT JOIN ventas v ON dv.idVenta=v.id AND v.estado!='0'
        INNER JOIN inventarioempacado ie ON r.id=ie.idRendimiento
        INNER JOIN catmateriasprimas cm ON r.idCatMateriaPrima=cm.id
        INNER JOIN catprogramas cp ON r.idCatPrograma=cp.id
        INNER JOIN catprocesos cpr ON cpr.id=r.idCatProceso
        WHERE dv.id IS NULL  AND r.estado='4' AND $filtradoFecha AND $filtradoMateria AND $filtradoPrograma
        ORDER BY r.semanaProduccion";
        return  $this->consultarQuery($sql, "consultar de Lotes por Vender");
    }
    /***************************************/
    /*ACTUALIZA CUEROS DE LAS VENTAS PARA LIBERAR PIEZAS CON CUEROS*/
    /***************************************/
    public function actualizaCuerosVentas()
    {
        $DataVenta = $this->getVentaAbiertaXUser();
        $idVenta = $DataVenta[0]['id'];

        $sql = "UPDATE ventas v 
        INNER JOIN detventas dv ON dv.idVenta=v.id
        INNER JOIN rendimientos r ON r.id= dv.idRendimiento
        INNER JOIN config_inventarios ci ON ci.estado='1'
        INNER JOIN cattiposventas ct ON v.idTipoVenta=ct.id
        SET
        dv.1s= ROUND((r.1s/(r.totalEmp/ci.pzasEnSets))*(dv.sets)),
        dv.2s= ROUND((r.2s/(r.totalEmp/ci.pzasEnSets))*(dv.sets)),
        dv.3s= ROUND((r.3s/(r.totalEmp/ci.pzasEnSets))*(dv.sets)),
        dv.4s= ROUND((r.4s/(r.totalEmp/ci.pzasEnSets))*(dv.sets)),
        dv._20=ROUND((r._20/(r.totalEmp/ci.pzasEnSets))*(dv.sets)),
        dv.total_s=IF(ct.cargaVenta='2',
				ROUND((r.total_s/(r.totalEmp))*(dv.unidades)),
        (ROUND((r._20/(r.totalEmp/ci.pzasEnSets))*(dv.sets))+ROUND((r.4s/(r.totalEmp/ci.pzasEnSets))*(dv.sets))+
        ROUND((r.3s/(r.totalEmp/ci.pzasEnSets))*(dv.sets))+ROUND((r.2s/(r.totalEmp/ci.pzasEnSets))*(dv.sets))+
        ROUND((r.1s/(r.totalEmp/ci.pzasEnSets))*(dv.sets)))),
        dv.distribuido='1'
        WHERE dv.idVenta='{$idVenta}'";
        return $this->ejecutarQuery($sql, "Liberar Piezas de Cueros de Ventas");
    }

    /*****************************************
     * PROCESO: VENTA DE CAJAS SELECCIONADAS
     ****************************************/
    ///CONSULTA DETALLADO DE CAJAS
    public function getDetalladoCaja($idLote, $idVenta, $usoInterno = '0')
    {
        $filtradoInterno = $usoInterno == '1' ? 'd.interna="1"' : "(d.interna IS NULL OR d.interna!='1')";
        $filtradoTotal = $usoInterno == '1' ? '1=1' : "d.totalPzas=400 ";

        $sql = "SELECT d.*, DATE_FORMAT(e.fecha,'%d/%m/%Y') AS fFechaEmpaque 
            FROM
            (SELECT 
            MAX(d.total) AS maxTotal, 
            SUM(d.total) AS totalPzas,
            GROUP_CONCAT(d.idLote ORDER BY d.total DESC) AS mixIds,
            GROUP_CONCAT(r.loteTemola ORDER BY d.total DESC) AS mixLotes,
            SUBSTRING_INDEX(GROUP_CONCAT(d.id ORDER BY d.total DESC),',',1) AS id,
            SUBSTRING_INDEX(GROUP_CONCAT(d.idLote ORDER BY d.total DESC),',',1) AS idLoteSize,
            SUBSTRING_INDEX(GROUP_CONCAT(d.idEmpaque ORDER BY d.total DESC),',',1) AS idEmpaque,
            SUBSTRING_INDEX(GROUP_CONCAT(d.vendida ORDER BY d.total DESC),',',1) AS vendida,
            SUBSTRING_INDEX(GROUP_CONCAT(d.idVenta ORDER BY d.total DESC),',',1) AS idVenta,
            SUBSTRING_INDEX(GROUP_CONCAT(d.numCaja ORDER BY d.total DESC),',',1) AS numCaja,
            IFNULL(idLoteLbl,d.idLote) AS idLote
                FROM detcajas d 
                INNER JOIN rendimientos r ON d.idLote=r.id 
                WHERE (d.remanente!='1' OR d.remanente IS NULL) AND ((d.vendida!='1' OR d.vendida IS NULL) OR (d.idVenta='$idVenta')) 
                AND r.estado='4' AND $filtradoInterno
                GROUP BY d.idEmpaque, d.numCaja ORDER BY d.idLote) d
            INNER JOIN empaques e ON d.idEmpaque=e.id
            WHERE d.idLote='$idLote' AND 
            ((d.vendida!='1' OR d.vendida IS NULL) OR (d.idVenta='$idVenta'))
            AND $filtradoTotal
            GROUP BY d.idEmpaque, d.numCaja";
        return $this->consultarQuery($sql, "consulta detallado de cajas");
    }
    ///DETALLADO DE CAJA
    public function getDetalleCaja($idEmpaque, $numCaja)
    {
        $sql = "SELECT d.*, r.loteTemola FROM detcajas d
        INNER JOIN rendimientos r ON d.idLote=r.id
        WHERE idEmpaque='$idEmpaque' AND numCaja='$numCaja'";
        return $this->consultarQuery($sql, "consulta detallado de cajas");
    }
    ///APARTA CAJAS PARA SELECCION DE VENTAS
    public function seleccionarCajasParaVenta($numCaja, $idEmpaque, $idVenta)
    {
        $sql = "UPDATE detcajas SET 
        vendida='1', idVenta='$idVenta'
        WHERE numcaja='$numCaja' AND idEmpaque='$idEmpaque'";
        return $this->runQuery($sql, "aparta cajas para ventas");
    }
    ///INSERTA DETALLADO DE CAJAS
    public function insertarDetalladoCajas($idVenta)
    {
        $idUserReg = $this->idUserReg;
        $sql = "INSERT detventas (idVenta, idRendimiento, unidades, sets, tipoLote, fechaReg, idUserReg)
        SELECT c.idVenta, c.idLote, SUM(c.total), SUM(c.total)/4, '1', NOW(), '$idUserReg' 
        FROM detcajas c  
        LEFT JOIN detventas d ON d.idVenta='$idVenta' AND c.idLote=d.idRendimiento
        WHERE d.id IS NULL AND c.idVenta='$idVenta' AND c.vendida ='1'
        GROUP BY c.idLote ";
        return $this->runQuery($sql, "aparta cajas para ventas");
    }
    ///ACTUALIZA DETALLADO DE CAJAS
    public function actualizaUnidadesEnVenta($idVenta)
    {
        $sql = "UPDATE detventas d
        INNER JOIN (SELECT c.idVenta, c.idLote, SUM(c.total) AS sumtotal, SUM(c.total)/4 AS sumset
        FROM detcajas c  
        INNER JOIN detventas d ON d.idVenta='$idVenta' AND c.idLote=d.idRendimiento
        WHERE c.idVenta='$idVenta' AND c.vendida ='1'
        GROUP BY c.idLote ) c ON c.idLote=d.idRendimiento AND c.idVenta=d.idVenta
        SET
        unidades= sumtotal, sets=sumset";
        return $this->runQuery($sql, "actualiza unidades de cajas para venta");
    }

    /*****************************************
     * PROCESO: DESHABILITA CAJAS SELECCIONADAS EN LA VENTA
     ****************************************/
    public function deshabilitaCajas($noCaja, $idEmpaque, $idVenta)
    {
        $sql = "UPDATE detcajas 
        SET idVenta='', vendida='0'
        WHERE numCaja='$noCaja' AND idEmpaque='$idEmpaque' AND idVenta='$idVenta'";
        return $this->runQuery($sql, "deshabilita unidades de cajas para venta");
    }
    public function eliminaDetalladoVenta($idVenta)
    {
        $sql = "DELETE dv FROM detventas dv
        INNER JOIN detcajas dc ON dc.idVenta= dv.idVenta AND dc.idLote=dv.idRendimiento
        AND dc.total= dv.unidades
        WHERE dv.idVenta='$idVenta'";
        return $this->runQuery($sql, "eliminar detallado de venta");
    }
    /*****************************************
     * PROCESO: ACTUALIZA METROS EN LA VENTA
     ****************************************/
    public function actualizaMetros($idDetVenta, $value)
    {
        $sql = "UPDATE detventas 
        SET unidades='$value'
        WHERE id='$idDetVenta'";
        return $this->runQuery($sql, "actualiza metros para venta");
    }
}
