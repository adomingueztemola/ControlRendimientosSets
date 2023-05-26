<?php
class Inventario extends ConexionBD
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

    /*****************************************
     * INVENTARIO TESEO
     *****************************************/

    public function getInventarioTeseo($filtradoSemana = '1=1', $filtradoProceso = '1=1', $filtradoPrograma = '1=1', $filtradoMateria = '1=1', $filtradoEstado = '1=1')
    {
        $sql = "SELECT
        CONCAT(IFNULL(rd.yearWeek, '0000'), '-SEM. ', LPAD(rd.semanaProduccion,2,'0')) AS semanaAnio,
        rd.loteTemola,
        rd.semanaProduccion,
        rd.pzasCortadasTeseo,
        rd.setsCortadosTeseo,
        pr.nombre AS n_proceso,
        pr.codigo AS c_proceso,
        pg.nombre AS n_programa,
        mp.nombre AS n_materia,
        rd._12Teseo, 
        rd._9Teseo,
        rd._3Teseo, 
        rd._6Teseo,
        areaFinal,
        yieldInicialTeseo
    FROM
        rendimientos rd 
        INNER JOIN catprocesos pr ON rd.idCatProceso = pr.id
        INNER JOIN catprogramas pg ON rd.idCatPrograma = pg.id
        INNER JOIN catmateriasprimas mp ON rd.idCatMateriaPrima = mp.id
        WHERE  rd.regTeseo='1' AND $filtradoSemana AND $filtradoProceso AND 
        $filtradoPrograma AND $filtradoMateria AND $filtradoEstado
        ORDER BY rd.yearWeek DESC,rd.semanaProduccion DESC";
        return  $this->ejecutarQuery($sql, "consultar Inventario de Teseo", true);
    }
    /*****************************************
     * INVENTARIO RECHAZO - SCRAP
     *****************************************/
    public function getInventarioRechazo($filtradoSemana = '1=1', $filtradoProceso = '1=1', $filtradoPrograma = '1=1', $filtradoMateria = '1=1', $filtradoEstado = '1=1')
    {
        $sql = "SELECT
        r.loteTemola,
        r.semanaProduccion,
        i.totalRech,
        i.setsTotalRech,
        r.porcFinalRechazo,
        r.porcSetsRechazoInicial,
        r.pzasCortadasTeseo,
        i.rzgoRech,
        ( r.porcFinalRechazo - r.porcSetsRechazoInicial ) AS difPorc,
        r.setsCortadosTeseo,
        pr.nombre AS n_proceso,
        pr.codigo AS c_proceso,
        pg.nombre AS n_programa,
        mp.nombre AS n_materia,
        i._12Scrap,
        i._3Scrap,
        i._6Scrap,
        i._9Scrap
        FROM
            vw_inventariolotes i
				INNER JOIN rendimientos r ON r.id= i.id
        INNER JOIN catprocesos pr ON r.idCatProceso = pr.id
        INNER JOIN catprogramas pg ON r.idCatPrograma = pg.id
        INNER JOIN catmateriasprimas mp ON r.idCatMateriaPrima = mp.id 
        WHERE
        i.totalRech >0 AND $filtradoSemana AND $filtradoProceso AND $filtradoPrograma 
                AND $filtradoMateria AND $filtradoEstado";
        return  $this->ejecutarQuery($sql, "consultar Inventario de Rechazo", true);
    }
    /*****************************************
     * INVENTARIO RECUPERACION
     *****************************************/
    public function getInventarioRecuperacion($filtradoSemana = '1=1', $filtradoProceso = '1=1', $filtradoPrograma = '1=1', $filtradoMateria = '1=1', $filtradoEstado = '1=1')
    {
        $sql = "SELECT
        r.id,
        r.loteTemola,
        r.semanaProduccion,
        i.setsTotalRecu AS setsInvRecu,
        i.totalRecu AS totalInvRecu,
        i.rzgoRecu,
        i._12Recu,
        i._3Recu,
        i._6Recu,
        i._9Recu,
        pr.nombre AS n_proceso,
        pr.codigo AS c_proceso,
        pg.nombre AS n_programa,
        mp.nombre AS n_materia,
        r.porcRecuperacionFinal,
        r.porcRecuperacion,
        r.cantRecuperacion,
        r.totalRecu,i.pzasLimitRecup, i.porcLimitRecup, r.totalRecu AS totalRecuperado,
        (i.totalRecuperado/i.pzasLimitRecup)*100 AS porcComplet,
        CONCAT(IFNULL(r.yearWeek, '0000'), '-SEM. ', LPAD(r.semanaProduccion,2,'0')) AS semanaAnio
        FROM
        rendimientos r
        INNER JOIN vw_inventariolotes i ON i.id= r.id
        INNER JOIN catprocesos pr ON r.idCatProceso = pr.id
        INNER JOIN catprogramas pg ON r.idCatPrograma = pg.id
        INNER JOIN catmateriasprimas mp ON r.idCatMateriaPrima = mp.id
        WHERE  $filtradoSemana AND $filtradoProceso 
        AND $filtradoPrograma AND $filtradoMateria AND $filtradoEstado
        AND i.totalRecu>0
        ORDER BY r.yearWeek DESC, r.semanaProduccion DESC";
        return  $this->ejecutarQuery($sql, "consultar Inventario de Recuperación", true);
    }
    /*****************************************
     * INVENTARIO EMPACADOS
     *****************************************/
    public function getInventarioSetsEmpacados(
        $filtradoSemana = '1=1',
        $filtradoProceso = '1=1',
        $filtradoPrograma = '1=1',
        $filtradoMateria = '1=1',
        $filtradoEstado = '1=1'
    ) {
        $sql = "SELECT
        r.loteTemola,
        r.semanaProduccion,
        i.setsTotalEmp AS setsTotalEmpInv,
        i.totalEmp AS totalEmpInv,
        i.rzgoEmp,
        pr.nombre AS n_proceso,
        pr.codigo AS c_proceso,
        pg.nombre AS n_programa,
        mp.nombre AS n_materia,
        r.porcRecuperacion,
		r.totalEmp, i.pzasLimitRecup, i.porcLimitRecup, totalRecuperado,
        (i.totalRecuperado/i.pzasLimitRecup)*100 AS porcComplet,
        CONCAT(IFNULL(r.yearWeek, '0000'), '-SEM. ', LPAD(r.semanaProduccion,2,'0')) AS semanaAnio

    FROM
        rendimientos r
				INNER JOIN vw_inventariolotes i ON i.id=r.id
        INNER JOIN catprocesos pr ON r.idCatProceso = pr.id
        INNER JOIN catprogramas pg ON r.idCatPrograma = pg.id
        INNER JOIN catmateriasprimas mp ON r.idCatMateriaPrima = mp.id
  
    WHERE
         $filtradoSemana AND $filtradoProceso AND $filtradoPrograma AND $filtradoMateria AND $filtradoEstado AND r.tipoProceso='1'
         ORDER BY r.yearWeek DESC, r.semanaProduccion DESC";
        return  $this->ejecutarQuery($sql, "consultar Inventario de Empaque", true);
    }
    /*****************************************
     * INVENTARIO GLOBALES
     *****************************************/
    public function getInventariosGlobales(
        $filtradoSemana = '1=1',
        $filtradoProceso = '1=1',
        $filtradoPrograma = '1=1',
        $filtradoMateria = '1=1',
        $tipoInventario = '1'
    ) {
        $sql = "SELECT
        r.semanaProduccion,
        r.loteTemola,
        r.areaFinal,
        r.setsCortadosTeseo,
        r.pzasCortadasTeseo,
        (r.pzasCortadasTeseo%4) AS rzgoTeseo,
        
        i.setsTotalRecu,
        i.totalRecu,
        i.rzgoRecu,
        
        i.setsTotalRech,
        i.totalRech,
        i.rzgoRech,
        
        (r.totalEmp-IFNULL(r.totalRecu,0))/4 AS setsTotalEmp,
        r.totalEmp-IFNULL(r.totalRecu,0) AS totalEmp,
        (r.totalEmp-IFNULL(r.totalRecu,0))%4 AS rzgoEmp,
				
		dv.ttlUnidades AS totalVend,
        dv.ttlSets AS setsTotalVend,
        dv.ttlRzgo AS rzgoVend,
        
        r.totalRecu AS totalRecuperado,
        (r.pzasCortadasTeseo+IFNULL(r.totalRecu,0)) AS totalPzasEsperadas,
       (i.totalEmp+i.totalRech+IFNULL(dv.ttlUnidades,0)) AS pzasTotalesInventario,
        pr.nombre AS n_proceso,
        pr.codigo AS c_proceso,
        pg.nombre AS n_programa,
        mp.nombre AS n_materia
				
        
    FROM
        vw_inventariolotes i
	    LEFT JOIN (SELECT dv.idRendimiento, SUM(dv.unidades) AS ttlUnidades, 
				   SUM(dv.sets) AS ttlSets, SUM(dv.sets)%conf.pzasEnSets AS ttlRzgo
				   FROM detventas dv 
				   INNER JOIN ventas v ON v.id= dv.idVenta AND v.estado='2'
                   INNER JOIN config_inventarios conf ON conf.estado='1'
				   GROUP BY dv.idRendimiento ) dv ON dv.idRendimiento= i.id
	      INNER JOIN rendimientos r ON r.id= i.id
          LEFT JOIN vw_trabajosrecuperacion mr ON mr.idRendimiento= i.id

        INNER JOIN catprocesos pr ON r.idCatProceso = pr.id
        INNER JOIN catprogramas pg ON r.idCatPrograma = pg.id
        INNER JOIN catmateriasprimas mp ON r.idCatMateriaPrima = mp.id
        WHERE  r.tipoProceso='$tipoInventario' AND $filtradoSemana AND $filtradoProceso AND $filtradoPrograma AND $filtradoMateria
        ORDER BY r.semanaProduccion DESC, YEAR(r.fechaEmpaque) DESC";
        return  $this->ejecutarQuery($sql, "consultar Inventario Global", true);
    }

    public function detalleLotesRecuperados($id)
    {
        $sql = "SELECT sub.*, FLOOR(sub.pzasTotales/ci.pzasEnSets) AS setsRecuperados,
        DATE_FORMAT(sub.fechaReg,'%d/%m/%Y') AS f_fechaReg, (sub.pzasTotales%ci.pzasEnSets) AS pzasSinSet
        FROM sublotesrecuperados sub
        INNER JOIN config_inventarios ci ON ci.estado='1'
        WHERE sub.idRendimiento='$id'
        ORDER BY sub.loteTemola DESC";
        return  $this->ejecutarQuery($sql, "consultar Detalle de Lotes Recuperados", true);
    }

    public function detalleLoteInicial($id)
    {
        $sql = "SELECT
        r.*,
        ( r.setsRecuperados * conf.pzasEnSets )% conf.pzasEnSets AS pzasSinSet,
        DATE_FORMAT( r.fechaEmpaque, '%d/%m/%Y' ) AS f_fechaEmpaque 
        FROM
            rendimientos r
            INNER JOIN config_inventarios conf ON conf.estado = '1' 
        WHERE
            r.id = '$id'";
        return  $this->ejecutarQuery($sql, "consultar Detalle de Lotes Inicial", true);
    }
    public function getSuperLotes()
    {
        $sql = "SELECT s.id, s.idRendimiento, r.loteTemola, 
        r.tipoProceso, mp.nombre AS n_materia, '1' AS tipoLote, s.pzasTotales
        FROM sublotesrecuperados s
        INNER JOIN rendimientos r ON r.id= s.idRendimiento
        INNER JOIN catmateriasprimas mp ON r.idCatMateriaPrima = mp.id

        WHERE superLote='1'
        ORDER BY  r.tipoProceso";
        return  $this->ejecutarQuery($sql, "consultar Lotes Generales", true);
    }

    public function getTrazabilidad($idRendimiento)
    {
        $sql = "SELECT kardex.*, CONCAT(su.nombre, ' ', su.apellidos) AS n_empleadoResp,
        DATE_FORMAT(kardex.fechaReg, '%d/%m/%Y %H:%i') AS f_fechaReg
         FROM 
        (#LoteSuper
        SELECT s.loteTemola, r.unidadesEmpacadas AS pzasTotales, 'Inicio' AS operacion, 
        s.fechaReg,  s.idUserReg 
        FROM sublotesrecuperados s
        INNER JOIN rendimientos r ON r.id=s.idRendimiento
        WHERE s.idRendimiento='$idRendimiento' AND s.superLote ='1'
        
        UNION ALL
        
        #Recuperacion 24 h a Recuperacion
        SELECT r.loteTemola, rec.unidadesRecu AS pzasTotales, 'Recuperación antes de las 24 hras' AS operacion, 
        rec.fechaReg,  rec.idUserReg
        FROM recuperacion24h rec
        INNER JOIN rendimientos r ON rec.idRendimiento=r.id
        WHERE r.id='$idRendimiento' AND rec.unidadesRecu >0 
        
        UNION ALL
        
        #Empaque 24 h a Recuperacion
        SELECT s.loteTemola, rec.unidadesEmp AS pzasTotales, 'Empacado antes de las 24 hras' AS operacion, 
        rec.fechaReg,  rec.idUserReg
        FROM recuperacion24h rec
        INNER JOIN sublotesrecuperados s ON rec.idSubLote=s.id
        WHERE s.idRendimiento='$idRendimiento' 
        
        UNION ALL

        #Trabajos de Recuperacion
        SELECT s.loteTemola, mr.totalRecuperacion AS pzasTotales, 'Trabajo de Recuperación' AS operacion, 
        mr.fechaReg,  mr.idUserReg
        FROM materialesrecuperados mr
        INNER JOIN sublotesrecuperados s ON mr.idRendRecup=s.idRendimiento AND s.superLote='1'
        WHERE s.idRendimiento='$idRendimiento'
        
        UNION ALL
        
        #Excepciones Empacadas
        SELECT s.loteTemola, s.pzasTotales, 'Excepción de Pzas. Empacadas' AS operacion, 
        e.fechaValida AS fechaReg,  e.idUserValida AS idUserReg
        FROM sublotesrecuperados s
        INNER JOIN excepcionesajustes e ON s.idExcepcion=e.id AND e.estado='2'
        WHERE s.idRendimiento='$idRendimiento' 
        
        UNION ALL
        #Excepciones Recuperadas
        SELECT r.loteTemola, e.pzasRecuperadas  AS pzasTotales, 
        'Excepción de Pzas. Recuperadas' AS operacion, e.fechaValida AS fechaReg,  e.idUserValida AS idUserReg
        FROM rendimientos r
        INNER JOIN excepcionesajustes e ON e.idRendimiento= r.id  AND e.estado='2'
        WHERE r.id='$idRendimiento'  
        
        UNION ALL
        #Traspaso de Entrada
        SELECT r.loteTemola, tr.cantidad AS pzasTotales, 'Traspaso de Entrada' AS operacion, tr.fechaReg,  tr.idUserReg 
        FROM rendimientos r
        INNER JOIN traspasos tr ON tr.idRendEntrada= r.id
        WHERE r.id='$idRendimiento' AND tr.estado='2'
        
        UNION ALL
        #Traspaso de Salida
        SELECT r.loteTemola, tr.cantidad AS pzasTotales, 'Traspaso de Salida' AS operacion, tr.fechaReg,  tr.idUserReg
        FROM rendimientos r
        INNER JOIN traspasos tr ON tr.idRendSalida= r.id
        WHERE r.id='$idRendimiento' AND tr.estado='2'
        
        UNION ALL
        #Ventas
        SELECT s.loteTemola, dvl.unidades AS pzasTotales, CONCAT('Venta Num. Factura ', IF(v.numFactura='', 'N/A',v.numFactura),' PL.',v.numPL) AS operacion, 
        v.fechaReg, v.idUserReg
        FROM detventas dv
        INNER JOIN detventaslotes dvl ON dvl.idDetVenta= dv.id
        INNER JOIN ventas v ON dv.idVenta= v.id  AND v.estado='2'
        INNER JOIN sublotesrecuperados s ON s.id= dvl.idSubLote
        WHERE dv.idRendimiento='$idRendimiento' ) kardex 
        LEFT JOIN segusuarios su ON kardex.idUserReg=su.id 
       
        ORDER BY kardex.fechaReg";
        return  $this->consultarQuery($sql, "consultar Trazabilidad de Lotes");
    }

    public function getTotalInventarios($idRendimiento)
    {
        $sql = "SELECT vw.*, r.tipoProceso FROM vw_inventariolotes vw
        INNER JOIN rendimientos r ON r.id = vw.id
        WHERE vw.id='$idRendimiento'";
        return  $this->ejecutarQuery($sql, "consultar Inventarios de Rendimiento", true);
    }

    public function getInventarioSupervisor($filtradoSemana, $filtradoProceso, $filtradoPrograma, $filtradoMateria)
    {
        $sql = "SELECT r.id, r.semanaProduccion,r.semanaProduccion, 
        CONCAT(IFNULL(r.yearWeek, '0000'), '-SEM. ', LPAD(r.semanaProduccion,2,'0')) AS semanaAnio,
         r.loteTemola, cp.nombre AS nPrograma,
        pv.nombre AS nProveedor, r.yieldInicialTeseo, r.pzasCortadasTeseo,
        (r.pzasCortadasTeseo/ci.pzasCajas) AS cajasTeseo,
        r.totalEmp, (r.totalEmp/ci.pzasCajas) AS cajasEmpaque,
        r.pzasSetsRechazadas, r.porcSetsRechazoInicial, 
        r.piezasRecuperadas, r.estado,
        vw.totalRech, IFNULL(vwd.totalUnidades,0) AS pzasVentas,
        GROUP_CONCAT(DISTINCT pv.nombre) AS proveedores

        FROM rendimientos r 
        INNER JOIN catprogramas cp ON r.idCatPrograma=cp.id
        INNER JOIN detpedidos dp ON r.id=dp.idRendimiento
        INNER JOIN pedidos p ON dp.idPedido=p.id
        INNER JOIN catproveedores pv ON p.idCatProveedor=pv.id
        INNER JOIN config_inventarios ci ON ci.estado='1'
        LEFT JOIN  vw_inventariolotes vw ON r.id=vw.id
        LEFT JOIN vw_detalladoventas vwd ON r.id=vwd.idRendimiento
        WHERE r.tipoProceso='1' AND $filtradoSemana AND $filtradoProceso AND $filtradoPrograma AND $filtradoMateria
        GROUP BY dp.idRendimiento 

        ORDER BY r.yearWeek DESC, r.semanaProduccion DESC ";
        return  $this->consultarQuery($sql, "consultar Inventario");
    }

    public function getInventarioCajas($filtradoSemana = '1=1', $filtradoProceso = '1=1', $filtradoPrograma = '1=1', $filtradoMateria = '1=1')
    {
        $sql = "SELECT r.id, r.loteTemola, r.fechaEngrase, r.fechaEmpaque, CONCAT(IFNULL(r.yearWeek, '0000'), '-SEM. ', LPAD(r.semanaProduccion,2,'0')) AS semanaAnio,
        r.semanaProduccion,
        DATE_FORMAT(r.fechaEngrase,'%d/%m/%Y') AS f_fechaEngrase, 
        DATE_FORMAT(r.fechaEmpaque,'%d/%m/%Y') AS f_fechaEmpaque,
        TIMESTAMPDIFF(DAY, r.fechaEmpaque, CURDATE()) AS antiguedad,
        cp.nombre AS nPrograma,
        i.pzasTotales, IFNULL(SUM(d.total),0)+IFNULL(mix.totalMix,0) AS cajas,
        (IFNULL(SUM(d.total),0)+IFNULL(mix.totalMix,0))/conf.pzasEnSets AS setscajas,
        COUNT(IF(d.total=400, d.id, NULL)) AS cajasCompletas,
				IFNULL(mix.cajasMix,0) AS cajasInCompletas, 
        IFNULL(IFNULL(SUM(d._12),0)+IFNULL(mix.s_12,0),0) AS s_12, IFNULL(IFNULL(SUM(d._3),0)+IFNULL(mix.s_3,0),0) AS s_3, 
				IFNULL(IFNULL(SUM(d._6),0)+IFNULL(mix.s_6,0),0) AS s_6,
        IFNULL(IFNULL(SUM(d._9),0)+IFNULL(mix.s_9,0),0) AS s_9
FROM rendimientos r
INNER JOIN catprogramas cp ON r.idCatPrograma=cp.id
LEFT JOIN inventarioempacado i ON r.id=i.idRendimiento
LEFT JOIN (SELECT a.*, SUM(a.cajasMix1) AS cajasMix FROM (SELECT  d.idLoteLbl, r.loteTemola, '1' AS cajasMix1,
	SUM(IF(d.idLote=d.idLoteLbl, d._12, 0)) AS s_12, SUM(IF(d.idLote=d.idLoteLbl, d._3, 0)) AS s_3, 
	SUM(IF(d.idLote=d.idLoteLbl, d._6, 0)) AS s_6, 	SUM(IF(d.idLote=d.idLoteLbl, d._9, 0)) AS s_9, 	
	SUM(IF(d.idLote=d.idLoteLbl, d.total, 0)) AS totalMix
	FROM detcajas d
	INNER JOIN rendimientos r ON d.idLoteLbl=r.id
	WHERE d.vendida IS NULL OR d.vendida!='1'
	GROUP BY d.idLoteLbl,  d.numCaja) a
	GROUP BY idLoteLbl
	) mix ON r.id=mix.idLoteLbl
LEFT JOIN detcajas d ON r.id=d.idLote AND (d.vendida IS NULL OR d.vendida!='1') AND d.remanente='0' 
	AND (d.idLoteLbl='' OR d.idLoteLbl='0' OR d.idLoteLbl IS NULL  ) 
    AND $filtradoSemana AND $filtradoProceso AND $filtradoPrograma AND $filtradoMateria
INNER JOIN config_inventarios conf ON conf.estado='1'
	WHERE mix.idLoteLbl IS NOT NULL OR d.id IS NOT NULL

GROUP BY r.id
ORDER BY r.yearWeek DESC, r.semanaProduccion DESC";
        return  $this->consultarQuery($sql, "consultar Inventario de Cajas");
    }
    public function getInventarioAgrupCajas()
    {
        $sql = "SELECT dc.idCatPrograma,  p.nombre AS nPrograma, COUNT(dc.idEmpaque) AS totalCajas
        FROM  (
                      SELECT e.fecha, d.idEmpaque, d.numCaja, sum(d.total) AS totalCajas, e.idCatPrograma,
                      p.nombre AS nPrograma	FROM detcajas d
                      INNER JOIN empaques e ON d.idEmpaque= e.id
                            INNER JOIN catprogramas p ON e.idCatPrograma=p.id
                      WHERE (d.vendida IS NULL OR d.vendida='0' OR d.vendida='') AND d.remanente='0'
                      GROUP BY d.idEmpaque, d.numCaja
                      HAVING sum(d.total)=400 
          ) dc 
        INNER JOIN catprogramas p ON dc.idCatPrograma=p.id
          GROUP BY dc.idCatPrograma";
        return  $this->consultarQuery($sql, "consultar Inventario de Cajas Por Programa");
    }


    public function getConsumoInterno($filtradoSemana = '1=1', $filtradoProceso = '1=1', $filtradoPrograma = '1=1', $filtradoMateria = '1=1')
    {
        $sql = "SELECT r.id, r.loteTemola, r.fechaEngrase, r.fechaEmpaque, CONCAT(IFNULL(r.yearWeek, '0000'), '-SEM. ', LPAD(r.semanaProduccion,2,'0')) AS semanaAnio,
    r.semanaProduccion,
    DATE_FORMAT(r.fechaEngrase,'%d/%m/%Y') AS f_fechaEngrase, 
    DATE_FORMAT(r.fechaEmpaque,'%d/%m/%Y') AS f_fechaEmpaque,
    TIMESTAMPDIFF(DAY, r.fechaEmpaque, CURDATE()) AS antiguedad,
    cp.nombre AS nPrograma, IFNULL(SUM(d.total),0)+IFNULL(mix.totalMix,0) AS cajas,
    (IFNULL(SUM(d.total),0)+IFNULL(mix.totalMix,0))/conf.pzasEnSets AS setscajas,
    COUNT(IF(d.total=400, d.id, NULL)) AS cajasCompletas,
            IFNULL(mix.cajasMix,0) AS cajasInCompletas,
            COUNT(IF(d.total=400, d.id, NULL))+ IFNULL(mix.cajasMix,0)  AS totalCajasVend,
    IFNULL(IFNULL(SUM(d._12),0)+IFNULL(mix.s_12,0),0) AS s_12, IFNULL(IFNULL(SUM(d._3),0)+IFNULL(mix.s_3,0),0) AS s_3, 
            IFNULL(IFNULL(SUM(d._6),0)+IFNULL(mix.s_6,0),0) AS s_6,
    IFNULL(IFNULL(SUM(d._9),0)+IFNULL(mix.s_9,0),0) AS s_9,
         mix.numsPL AS ventasMix,  GROUP_CONCAT(DISTINCT vnorm.numPL) AS ventasNorm,
         CONCAT(GROUP_CONCAT(DISTINCT vnorm.numPL),', ',IFNULL(mix.numsPL,' ')) AS totalpls
    FROM rendimientos r
    INNER JOIN catprogramas cp ON r.idCatPrograma=cp.id
    LEFT JOIN (SELECT a.*, SUM(a.cajasMix1) AS cajasMix 
    FROM (SELECT  d.idLoteLbl, r.loteTemola, '1' AS cajasMix1,
    SUM(IF(d.idLote=d.idLoteLbl, d._12, 0)) AS s_12, SUM(IF(d.idLote=d.idLoteLbl, d._3, 0)) AS s_3, 
    SUM(IF(d.idLote=d.idLoteLbl, d._6, 0)) AS s_6, 	SUM(IF(d.idLote=d.idLoteLbl, d._9, 0)) AS s_9, 	
    SUM(IF(d.idLote=d.idLoteLbl, d.total, 0)) AS totalMix, 	GROUP_CONCAT(DISTINCT v.numPL) AS numsPL
    FROM detcajas d
    INNER JOIN rendimientos r ON d.idLoteLbl=r.id
    INNER JOIN ventas v ON v.id=d.idVenta
    WHERE d.vendida ='1' AND d.interna AND $filtradoSemana AND $filtradoProceso AND $filtradoPrograma AND $filtradoMateria
    GROUP BY d.idLoteLbl,  d.numCaja) a
    GROUP BY idLoteLbl
    ) mix ON r.id=mix.idLoteLbl
    LEFT JOIN detcajas d ON r.id=d.idLote AND (d.vendida ='1') AND d.remanente='0' 
    AND (d.idLoteLbl='' OR d.idLoteLbl='0' OR d.idLoteLbl IS NULL  ) AND d.vendida='1' AND
    d.interna='1'  AND $filtradoSemana AND $filtradoProceso AND $filtradoPrograma AND $filtradoMateria
    LEFT JOIN ventas vnorm ON d.idVenta=vnorm.id 
    INNER JOIN config_inventarios conf ON conf.estado='1'
    WHERE mix.idLoteLbl IS NOT NULL OR d.id IS NOT NULL

    GROUP BY r.id
    ORDER BY r.yearWeek DESC, r.semanaProduccion DESC";
        return  $this->consultarQuery($sql, "consultar Consumo Interno de Cajas");
    }
}
