<?php
class Empaque extends ConexionBD
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
    public function getEmpaqueSelect2($filtradoPrograma='1=1',$busqId = ''){
        $filtradoID = $busqId == '' ? '1=1' : "ca.nombre LIKE '%$busqId%'";
        $sql="SELECT e.*, 
        DATE_FORMAT(e.fecha,'%d/%m/%Y') AS fFecha
        FROM empaques e
        WHERE $filtradoPrograma AND $filtradoID
        ORDER BY fecha DESC";
        return  $this->consultarQuery($sql, "consultar Empaque");

    }
    public function getDetRendimientos($id)
    {
        $sql = "SELECT r.*, DATE_FORMAT(r.fechaEngrase,'%d/%m/%Y') AS f_fechaEngrase,
                DATE_FORMAT(r.fechaEmpaque,'%d/%m/%Y') f_fechaEmpaque,
                pr.nombre AS n_proceso, pg.nombre AS n_programa, mp.nombre AS n_materia, pr.codigo  AS c_proceso,
                DATE_FORMAT(r.fechaReg, '%d/%m/%Y %H:%m') AS f_fechaReg, (r.perdidaAreaWBCrust+r.perdidaAreaCrustTeseo) AS totalDifArea, 
                precioUnitFactUsd
                FROM rendimientos r
                INNER JOIN catprocesos pr ON r.idCatProceso=pr.id
                INNER JOIN catprogramas pg ON r.idCatPrograma=pg.id
                INNER JOIN catmateriasprimas mp ON r.idCatMateriaPrima=mp.id
                INNER JOIN ( SELECT r.id, AVG(p.precioUnitFactUsd) precioUnitFactUsd, AVG(p.areaProvPie2) AS areaProvPie2,
                                    AVG(p.totalCuerosFacturados) AS totalCuerosFacturados,  r.yearWeek  AS years 
                        FROM detpedidos dp
                        INNER JOIN pedidos p ON p.id= dp.idPedido
                        INNER JOIN rendimientos r ON dp.idRendimiento = r.id  AND r.tipoProceso = '1'
                        WHERE dp.estado='2'
                        GROUP BY dp.idRendimiento) u ON r.id=u.id
                WHERE r.id='$id'";
        return  $this->consultarQuery($sql, "consultar Detallado de Rendimiento", false);
    }
    /*********************************************
     * INICIO DE EMPAQUE: PROGRAMA/FECHA
     *********************************************/
    /// AGREGAR EMPAQUE 
    public function agregarEmpaque($fecha, $programa)
    {
        $idUserReg = $this->idUserReg;
        $sql = "INSERT INTO empaques (idCatPrograma, fecha, idUserReg, fechaReg) 
           VALUES ('$programa', '$fecha', '$idUserReg', NOW())";
        return $this->runQuery($sql, "agregar Empaque");
    }
    /// EMPAQUES POR FECHA 
    public function getEmpaquesFecha()
    {
        $sql = "SELECT e.*, cp.nombre AS nPrograma,
        DATE_FORMAT(e.fecha,'%d/%m/%Y') AS f_fecha
        FROM empaques e
        INNER JOIN catprogramas cp ON e.idCatPrograma=cp.id
        ORDER BY e.fecha DESC";
        return $this->consultarQuery($sql, "consultar empaques.");
    }
    /// VALIDA EXISTENCIA EMPAQUE 
    public function validaExistenciaEmpaque($fecha, $programa)
    {
        $sql = "SELECT e.*
           FROM empaques e
           WHERE e.idCatPrograma='$programa' AND e.fecha='$fecha'";
        return $this->consultarQuery($sql, "consultar empaques por fecha y programa.");
    }
    /*********************************************
     * DETALLADO DE CAJAS ENTRANTES
     *********************************************/
    ///INGRESAR CARGA
    public function registraDetCaja(
        $id,
        $lote,
        $caja,
        $pzas_12,
        $pzas_03,
        $pzas_06,
        $pzas_09,
        $remanente,
        $tipoLote,
        $total,
        $pzas_12_rem,
        $pzas_03_rem,
        $pzas_06_rem,
        $pzas_09_rem,
        $lote0
    ) {
        $idUserReg = $this->idUserReg;
        $totalRem = 0;
        if ($remanente == '1') {
            $totalRem = $pzas_12 + $pzas_03 + $pzas_06 + $pzas_09;
        }
        $sql = "INSERT INTO  detcajas (numCaja, idEmpaque, tipo, idLote, remanente,
         fechaReg, idUserReg, _12, _3, _6, _9, total, usoRemanente, 
         _12Rem, _3Rem, _6Rem, _9Rem,_12Rev, _3Rev, _6Rev, _9Rev, totalRev, interna, totalRem, lote0, estado) 
         VALUE ('$caja', '$id', '$tipoLote', '$lote', '$remanente', NOW(),
         '$idUserReg', '$pzas_12', '$pzas_03', '$pzas_06', '$pzas_09', '$total', '0',
         '$pzas_12_rem', '$pzas_03_rem', '$pzas_06_rem', '$pzas_09_rem',
         '0','0','0','0','0', '0', '$totalRem', '$lote0', '1')";
        return $this->runQuery($sql, "registro de detalle de caja", true);
    }
    /// REVERSA DE LOTE QUE NO HA PASADO A ALMACEN 0
    public function aumentoStckReverseRem($idLote, $_12, $_3, $_6, $_9)
    {
        $total = $_12 + $_3 + $_6 + $_9;
        $sql = "UPDATE inventariorechazado i
        INNER JOIN rendimientos r ON r.id=i.idRendimiento
        SET 
        i._12=i._12+'$_12', i._3=i._3+'$_3', i._6=i._6+'$_6',i._9=i._9+'$_9',
        i.pzasTotales=i.pzasTotales+'$total',
        r.totalRech=IFNULL(r.totalRech,0)+'$total'
        WHERE i.idRendimiento='$idLote'";
        return $this->runQuery($sql, "aumento de stock por reverse de Remanente",);
    }
    /// REVERSA DE LOTE QUE HA PASADO A ALMACEN 0
    public function aumentoDetTarimaReverseRem($idLote, $_12, $_3, $_6, $_9)
    {
        $total = $_12 + $_3 + $_6 + $_9;
        $sql = "UPDATE dettarimas d
                INNER JOIN rendimientos r ON r.id=d.idLote
        SET 
        r.totalRech=IFNULL(r.totalRech,0)+'$total',
        d._12Scrap=d._12Scrap+'$_12', d._3Scrap=d._3Scrap+'$_3', d._6Scrap=d._6Scrap+'$_6',d._9Scrap=d._9Scrap+'$_9',
        d.totalScrap=d.totalScrap+'$total'  WHERE d.idLote='$idLote'";
        return $this->runQuery($sql, "aumento de detalle de tarima por reverse de Remanente",);
    }
    /// AGREGAR DETALLADO DE REVERSA DE LOTE 

    public function agregarDetReverse($idLote, $_12, $_3, $_6, $_9)
    {
        $idUserReg = $this->idUserReg;
        $total = $_12 + $_3 + $_6 + $_9;
        $sql = "UPDATE detcajas SET 
        _12Rev=_12Rev+'$_12', _3Rev=_3Rev+'$_3', _6Rev=_6Rev+'$_6', _9Rev=_9Rev+'$_9',
        _12Rem=_12Rem-'$_12', _3Rem=_3Rem-'$_3', _6Rem=_6Rem-'$_6', _9Rem=_9Rem-'$_9',
        totalRev=totalRev+'$total',  totalRem=totalRem-'$total', fechaRev=NOW(), idUserRev='$idUserReg',
        usoRemanente=IF(totalRem-'$total'<=0, '1', '0')
        WHERE idLote='$idLote' AND remanente='1'";
        return $this->runQuery($sql, "agregar detalle de reverse de Remanente",);
    }
    ///DISMINUIR STOCK DE RECUPERACION
    public function disminuirStkRecu($idLote, $_12, $_3, $_6, $_9)
    {
        $total = $_12 + $_3 + $_6 + $_9;
        $sql = "UPDATE inventariorecuperado SET 
        _12=_12-'$_12', _3=_3-'$_3', _6=_6-'$_6',_9=_9-'$_9',
        pzasTotales=pzasTotales-'$total', 
        _12Rev=IFNULL(_12Rev,0)+'$_12', 
        _3Rev=IFNULL(_3Rev,0)+'$_3', 
        _6Rev=IFNULL(_6Rev,0)+'$_6',
        _9Rev=IFNULL(_9Rev,0)+'$_9',
        totalRev=IFNULL(totalRev,0)+'$total' 
        WHERE idRendimiento='$idLote'";
        return $this->runQuery($sql, "disminución de recuperación",);
    }
    /// CONSULTA LOTES DISPONIBLES 
    public function getLotesDisponibles($programa)
    {
        $sql = "SELECT r.id, r.loteTemola, '1' AS tipoPieza,  _12OKAct AS pza_12, _3OKAct AS pza_3,
        _6OKAct AS pza_6, _9OKAct AS pza_9, '0' AS idDetCaja, 
        IF(r.pzasOk-IFNULL(d.pzasEmp,0)<400,'1','0') AS aplicaRemanente,
        r.pzasOk-IFNULL(d.pzasEmp,0) AS remanenteAct, r.pzasCortadasTeseo, r.paseScrap
        FROM
        rendimientos r
        LEFT JOIN vw_detalladocaja d ON d.idLote=r.id
        WHERE 
        r.regOkNok='1' 
        /*AND r.estado<>'4'*/
        AND (r.regEmpaque!='1' OR regEmpaque IS NULL )
        AND (d.idLote IS NULL OR d.pzasEmp<r.pzasCortadasTeseo) 
        
        AND r.idCatPrograma='$programa'
                
        UNION ALL
                        
        SELECT r.id, r.loteTemola, '2' AS tipoPieza, dc._12Rem, dc._3Rem, dc._6Rem,
        dc._9Rem, dc.id AS idDetCaja, '0' AS aplicaRemanente,
        '0' AS remanenteAct, r.pzasCortadasTeseo, r.paseScrap
        FROM detcajas dc 
        INNER JOIN rendimientos r ON dc.idLote=r.id
        WHERE dc.remanente='1' AND dc.total>0 AND dc.usoRemanente='0' AND r.idCatPrograma='$programa'
        
        UNION ALL
        
        SELECT r.id, r.loteTemola, '3' AS tipoPieza, 
        ir._12 AS _12f, 
        ir._3 AS _3f, 
        ir._6 AS _6f, 
        ir._9 AS _9f, 
        '0' AS idDetCaja ,  '0' AS aplicaRemanente,
        '0' AS remanenteAct, r.pzasCortadasTeseo, r.paseScrap
        FROM inventariorecuperado ir
        INNER JOIN rendimientos r ON ir.idRendimiento=r.id
        WHERE pzasTotales>0 AND r.idCatPrograma='$programa'";
        return $this->consultarQuery($sql, "consultar Lotes Disponibles.");
    }
    /// CONSULTA DETALLADO DE EMPAQUE
    public function getDetEmpaque($id)
    {
        $sql = "SELECT e.*, cp.nombre AS nPrograma,
        DATE_FORMAT(e.fecha,'%d/%m/%Y') AS f_fecha FROM
        empaques e 
        INNER JOIN catprogramas cp ON e.idCatPrograma=cp.id
        WHERE e.id='$id'
        ";
        return $this->consultarQuery($sql, "consultar Detalle de Empaque.", false);
    }
    /// CONSULTA DETALLADO DE EMPAQUE
    public function getDetalladoCajas($idEmpaque)
    {
        $sql = "SELECT d.*, r.loteTemola, lbl.loteTemola AS lblLote 
        FROM detcajas d
        INNER JOIN rendimientos r ON d.idLote=r.id
        LEFT JOIN rendimientos lbl ON d.idLoteLbl=lbl.id
        WHERE d.idEmpaque='$idEmpaque' AND d.remanente='0' AND (d.estado<>'0' OR d.estado IS NULL)
        ORDER BY d.numCaja";
        return $this->consultarQuery($sql, "consultar Detalle de Empaque.");
    }
    /// CONSULTA LLENADO DE CAJA
    public function consultaLlenadoCaja($idEmpaque)
    {
        $sql = "SELECT e.fecha, e.id AS idEmpaque, d.id AS idCaja,  IFNULL((d.numCaja),0) AS ultCajaLlenada, 
        IFNULL((d.numCaja),0)+1 AS cajaSiguiente, IFNULL(SUM(d.total),0) AS total,
        IF(SUM(d.total)=400, '1', '0') AS completed,
        IF(IFNULL(SUM(d._12),0)<100 OR IFNULL(SUM(d._12),0)=100,(100-IFNULL(SUM(d._12),0)), 100) AS _12f, 
        IF(IFNULL(SUM(d._3),0)<100 OR IFNULL(SUM(d._3),0)=100,(100-IFNULL(SUM(d._3),0)), 100) AS _3f, 
        IF(IFNULL(SUM(d._6),0)<100 OR IFNULL(SUM(d._6),0)=100,(100-IFNULL(SUM(d._6),0)), 100) AS _6f, 
        IF(IFNULL(SUM(d._9),0)<100 OR IFNULL(SUM(d._9),0)=100,(100-IFNULL(SUM(d._9),0)), 100) AS _9f
        FROM detcajas d
        INNER JOIN empaques e ON d.idEmpaque=e.id
        WHERE  d.idEmpaque='$idEmpaque' AND (d.estado<>'0' OR d.estado IS NULL)
        AND d.numCaja=(
         SELECT MAX(numCaja) 
         FROM detcajas 
         WHERE  (estado<>'0' OR estado IS NULL) AND idEmpaque='$idEmpaque')";
        return $this->consultarQuery($sql, "consultar Llenado de Caja.", false);
    }
    ///VISUALIZACION DE REMANENTES X EMPAQUE
    public function consultaRemanentexEmpaque($idEmpaque)
    {
        $sql = "SELECT d.*, r.loteTemola, r.paseScrap,
        IFNULL(uPzas_12,0) AS uPzas_12, IFNULL(uPzas_6,0) AS uPzas_6, 
        IFNULL(uPzas_9,0) AS uPzas_9, IFNULL(uPzas_3,0) AS uPzas_3

        FROM detcajas d
        INNER JOIN rendimientos r ON d.idLote=r.id
        LEFT JOIN (
        SELECT idLote, SUM(_12) AS uPzas_12,SUM(_6) AS uPzas_6,
        SUM(_9) AS uPzas_9,SUM(_3) AS uPzas_3
        FROM detcajas d 
        WHERE tipo='2'
        GROUP BY idLote
        ) uso ON uso.idLote=d.idLote
        WHERE d.idEmpaque='$idEmpaque' AND d.remanente='1' 
        ORDER BY d.numCaja";
        return $this->consultarQuery($sql, "consultar Remanentes del Empaque.");
    }
    /// CONSULTAR TOTAL DE EMPAQUE POR LOTE 
    public function consultaTotalEmpaqueXLote($idLote, $idEmpaque)
    {
        $sql = "SELECT d.idEmpaque, r.loteTemola, d.idLote,
            SUM(case when d.tipo = 1 AND d.remanente='0' AND d.idEmpaque='$idEmpaque' then d.total else 0 end) as sumPzasNorm,
            SUM(case when d.tipo = 1 AND d.remanente='1' AND d.idEmpaque='$idEmpaque' then d.total else 0 end) as remanente,
            SUM(case when d.tipo = 2 AND d.remanente='0' AND d.idEmpaque='$idEmpaque'  then d.total else 0 end) as sumPzasRemt,
            SUM(case when d.tipo = 3 AND d.remanente='0' AND d.idEmpaque='$idEmpaque'  then d.total else 0 end) as sumPzasRecup,
            SUM(case when d.tipo = 1 AND d.remanente='0' AND d.idEmpaque='$idEmpaque' then d.total else 0 end)+ 
            SUM(case when d.tipo = 2 AND d.remanente='0' AND d.idEmpaque='$idEmpaque' then d.total else 0 end) AS pzasEmp,
            SUM(case when d.tipo = 1 AND d.remanente='0' AND d.idEmpaque='$idEmpaque'  then d.total else 0 end)+ 
            SUM(case when d.tipo = 2 AND d.remanente='0' AND d.idEmpaque='$idEmpaque' then d.total else 0 end)+
            SUM(case when d.tipo = 3 AND d.remanente='0' AND d.idEmpaque='$idEmpaque'  then d.total else 0 end) AS totalEmp,
            r.pzasOk-(SUM(case when d.tipo = 1 AND d.remanente='0'  then d.total else 0 end)+ 
            SUM(case when d.tipo = 1 AND d.remanente='1'  then d.totalRem else 0 end)+
            SUM(case when d.tipo = 2  then d.total else 0 end)) AS totalScrap,
            SUM(case when d.tipo = 1  AND d.idEmpaque!='$idEmpaque' then d.total else 0 end) as sumParcLote
        FROM detcajas d
        INNER JOIN rendimientos r ON d.idLote=r.id
        WHERE d.idLote='$idLote' AND (d.estado<>'0' OR d.estado IS NULL)
        GROUP BY d.idLote";
        return $this->consultarQuery($sql, "consultar Remanentes del Empaque.", false);
    }
    /// CONSULTAR DATOS DEL LOTE 
    public function getDatosTeseo($idLote)
    {
        $sql = "SELECT r.*, IFNULL(d.pzasEmp,0) AS pzasEmp FROM
        rendimientos r
        LEFT JOIN vw_detalladocaja d ON r.id=d.idLote
        WHERE r.id='$idLote'";
        return $this->consultarQuery($sql, "consultar Datos del Lote.", false);
    }

    /// CAJAS COMPLETAS POR PROGRAMA
    public function getCajasCompletXPrograma($fecha)
    {
        $sql = "SELECT e.id, e.fecha, p.nombre AS nPrograma, COUNT(dc.idEmpaque) AS totalCajas, interna
        FROM empaques e
        INNER JOIN catprogramas p ON e.idCatPrograma=p.id
        INNER JOIN (
					SELECT d.idEmpaque, d.numCaja, sum(d.total) AS totalCajas, d.interna FROM detcajas d
					INNER JOIN empaques e ON d.idEmpaque= e.id
					WHERE e.fecha = '$fecha' AND  d.remanente='0' AND (d.estado<>'0' OR d.estado IS NULL)
					GROUP BY d.idEmpaque, d.numCaja, d.interna
					HAVING sum(d.total)=400 
        ) dc ON e.id = dc.idEmpaque
       GROUP BY dc.idEmpaque, dc.interna";
        return  $this->consultarQuery($sql, "consultar cajas completas del programa");
    }

    ///ACTUALIZAR DATOS DE TESEO
    public function actualizarTeseo($id, $teseo, $campo)
    {
        $sql = "UPDATE rendimientos SET $campo='$teseo'
                WHERE id='$id'";
        return $this->runQuery($sql, "actualizar Piezas de Teseo");
    }
    ///ACTUALIZA REGISTRO DE TESEO AL FINALIZAR 3 REGISTROS OBLIGATORIOS
    public function cierreRegistroTeseo($id)
    {
        $sql = "UPDATE rendimientos r
		INNER JOIN config_inventarios conf ON conf.estado = '1'
        SET 
        r.regTeseo=1,
        r.regOkNok=1,
        r.setsCortadosTeseo=r.pzasCortadasTeseo/conf.pzasEnSets,
        r.fechaRegTeseo= NOW(),
        r._12NOK='0',  r._3NOK='0',  r._6NOK='0',  r._9NOK='0',
        r._12OK=r._12Teseo,  r._3OK=r._3Teseo,  r._6OK=r._6Teseo,  r._9OK=r._9Teseo,
        r._12OKAct=r._12Teseo,  r._3OKAct=r._3Teseo,  r._6OKAct=r._6Teseo,  r._9OKAct=r._9Teseo,
        r.pzasOk=r.pzasCortadasTeseo, r.pzasNok=0
        WHERE r.id='$id'";
        return $this->runQuery($sql, "actualizar Registro Completo de Teseo");
    }
    ///ACTUALIZACION DE STOCK DE EMPAQUE
    public function actualizarStckEmpaque($idDetCaja)
    {
        $sql = "UPDATE inventarioempacado i
        INNER JOIN detcajas d ON d.idLote=i.idRendimiento 
        INNER JOIN config_inventarios conf ON conf.estado = '1'
        SET 
        i.pzasTotales= IFNULL(i.pzasTotales,0)+ IFNULL(d.total,0),
        i.setsTotales=(IFNULL(i.pzasTotales,0)+ IFNULL(d.total,0))/conf.pzasEnSets
        WHERE d.id='$idDetCaja'";
        return $this->runQuery($sql, "actualizar Registro de Piezas en Inventario");
    }
    ///ACTUALIZACION DE UNIDADES  DE EMPAQUE EN REGISTRO DE LOTE
    public function actualizarUnidadesEmpaque($idDetCaja)
    {
        $sql = "UPDATE rendimientos r
        INNER JOIN detcajas d ON d.idLote=r.id 
        INNER JOIN config_inventarios conf ON conf.estado = '1'
        SET 
        r.totalEmp= IFNULL(r.totalEmp,0)+ IFNULL(d.total,0),
        r.setsEmpacados=(IFNULL(r.totalEmp,0)+ IFNULL(d.total,0))/conf.pzasEnSets
        WHERE d.id='$idDetCaja'";
        return $this->runQuery($sql, "actualizar Registro de Piezas en Lote");
    }
    ///ACTUALIZACION DE UNIDADES  DE EMPAQUE EN REGISTRO DE LOTE
    public function actualizarUnidadesLote0($idDetCaja)
    {
        $sql = "UPDATE rendimientos r
         INNER JOIN detcajas d ON d.idLote=r.id 
         INNER JOIN config_inventarios conf ON conf.estado = '1'
         SET 
         r.totalLote0= IFNULL(r.totalLote0,0)+ IFNULL(d.total,0)
         WHERE d.id='$idDetCaja' AND r.regEmpaque='1'";
        return $this->runQuery($sql, "actualizar Registro de Piezas en Lote");
    }
    ///VER RENDIMIENTO Y DATOS DE TESEO
    public function getRendimiento($id)
    {
        $sql = "SELECT * FROM rendimientos r WHERE id='$id'";
        return  $this->consultarQuery($sql, " consultar lote a cerrar de Teseo ", false);
    }

    //CALCULAR RENDIMIENTO DEL LOTE
    public function calcularRendimiento($idRendimiento)
    {
        $idUserReg = $this->idUserReg;
        $sql = "CALL calcularRendimientoFase2('{$idRendimiento}','{$idUserReg}', '0')";
        return $this->ejecutarQuery($sql, "actualizar Rendimiento");
    }

    ///CERRAR EMPAQUE
    public function guardarTotal($idLote, $idEmpaque)
    {
        $sql = "UPDATE rendimientos r
        		INNER JOIN config_inventarios conf ON conf.estado = '1'
                INNER JOIN vw_detalladocaja t ON r.id=t.idLote
                SET r.totalEmp=t.totalEmp, r.unidadesEmpacadas=t.pzasEmp, r.regEmpaque='1',
                r._12OKAct= IFNULL(r._12OK,0)-(IFNULL(t.sumNorm12,0)+IFNULL(t.scrap12,0)),
                r._3OKAct= IFNULL(r._3OK,0)-(IFNULL(t.sumNorm3,0)+IFNULL(t.scrap3,0)), 
                r._6OKAct= IFNULL(r._6OK,0)-(IFNULL(t.sumNorm6,0)+IFNULL(t.scrap6,0)), 
                r._9OKAct= IFNULL(r._9OK,0)-(IFNULL(t.sumNorm9,0)+IFNULL(t.scrap9,0)),
                r.setsEmpacados= t.totalEmp/conf.pzasEnSets,
                r.setsRechazados= IFNULL(r.setsRechazados, 0)+(IFNULL(t.totalScrap,0)/conf.pzasEnSets),
                r.pzasSetsRechazadas= (IFNULL(r.pzasSetsRechazadas, 0)+IFNULL(t.totalScrap,0)),
                r.porcSetsRechazoInicial= ((IFNULL(r.setsRechazados, 0)+(IFNULL(t.totalScrap,0)/conf.pzasEnSets))/r.setsCortadosTeseo)*100,
                r.porcFinalRechazo= ((IFNULL(r.setsRechazados, 0)+(IFNULL(t.totalScrap,0)/conf.pzasEnSets))/r.setsCortadosTeseo)*100,
                r.totalRech= IFNULL(t.totalScrap,0)

                WHERE r.id='$idLote'";
        return $this->runQuery($sql, "registro de Totales");
    }
    /// ACTUALIZAR USO DE PIEZAS OK 
    public function actualizarUsoPzasOK($idDetCaja)
    {
        $sql = "UPDATE rendimientos r
                INNER JOIN detcajas d ON r.id=d.idLote
                SET r._12OKAct=IFNULL(r._12OKAct,0)-IFNULL(d._12,0), r._3OKAct=IFNULL(r._3OKAct,0)-IFNULL(d._3,0),
                r._6OKAct=IFNULL(r._6OKAct,0)-IFNULL(d._6,0),  r._9OKAct=IFNULL(r._9OKAct,0)-IFNULL(d._9,0),
                r.totalEmp=IFNULL(r.totalEmp,0)+IFNULL(d.total,0),
                r.unidadesEmpacadas=IFNULL(r.unidadesEmpacadas,0)+IFNULL(d.total,0),
                r.setsEmpacados=(IFNULL(r.totalEmp,0)+IFNULL(d.total,0))/4

                WHERE d.id='$idDetCaja'";
        return $this->runQuery($sql, "actualizar uso de Piezas OK");
    }
    /// MARCA USO INTERNO DE LA CAJA EMPACADA
    public function actualizarInternoEnCaja($numCaja, $idEmpaque, $interno)
    {
        $sql = "UPDATE detcajas
        SET interna='$interno'
        WHERE idEmpaque='$idEmpaque' AND numCaja='$numCaja'";
        return  $this->runQuery($sql, "actualización de estatus interno de Caja");
    }

    /*********************************************
     * PROCESO DE INGRESO DE  REMANENTE
     *********************************************/
    ///ACTUALIZAR PIEZAS REMANENTE
    public function actualizarPzasRemanante($idDetCaja, $idNewCaja)
    {
        $sql = "UPDATE detcajas d 
          INNER JOIN (SELECT * FROM detcajas WHERE id='$idNewCaja') nc ON nc.idLote=d.idLote 
          SET d._12Rem=d._12Rem-nc._12, d._3Rem=d._3Rem-nc._3, d._6Rem=d._6Rem-nc._6, d._9Rem=d._9Rem-nc._9,
            d.totalRem=d.totalRem-((d._12Rem-nc._12)+(d._3Rem-nc._3)+(d._6Rem-nc._6)+(d._9Rem-nc._9))
          WHERE d.id='$idDetCaja'";
        return $this->runQuery($sql, "actualizar Piezas de Remanente");
    }

    public function eliminarRemanenteLote($idLote, $idEmpaque)
    {
        $sql = "DELETE FROM detcajas WHERE 
          idLote='$idLote' AND idEmpaque='$idEmpaque' AND remanente='1'";
        return $this->runQuery($sql, "eliminar remanente de lote.");
    }
    /// ACTUALIZAR USO DE REMANENTE 
    public function actualizarUsoRemanante($id)
    {
        /* $sql = "UPDATE detcajas d 
          INNER JOIN (SELECT idLote, SUM(total) total FROM detcajas
                      WHERE tipo='2' GROUP BY idLote) r ON r.idLote=d.idLote
          INNER JOIN (SELECT idLote, total FROM detcajas
                      WHERE remanente='1') b ON r.idLote=b.idLote
          SET d.usoRemanente='1',  d.totalRem='0' WHERE 
          d.id='$id' AND b.total=r.total";*/
        $sql = "UPDATE detcajas d 
          INNER JOIN (SELECT idLote, SUM(total) total FROM detcajas
                      WHERE tipo='2' AND (estado<>'0' OR estado IS NULL) GROUP BY idLote
                      ) r ON r.idLote=d.idLote
           SET d.usoRemanente='1' 
           WHERE d.idLote='$id' AND  d.remanente='1' AND d.total = r.total";
        return $this->runQuery($sql, "actualizar uso de remanente");
    }
    ///ACTUALIZAR PIEZAS OK UTILIZADAS EN EL REMANENTE
    public function actualizarUsoPzasOKRemanente($idLote)
    {
        $sql = "UPDATE rendimientos r 
        INNER JOIN detcajas d ON r.id=d.idLote AND d.remanente='1'
        INNER JOIN vw_detalladocaja v ON v.idLote= r.id
        SET r.pzasOk= r.pzasOk-d.total, r._12OkAct=r._12OkAct-d._12,
        r._6OkAct=r._6OkAct-d._6, r._3OkAct=r._3OkAct-d._3,
        r._9OkAct=r._9OkAct-d._9
        WHERE r.id='$idLote'";
        return $this->runQuery($sql, "actualizar uso de remanente");
    }

    ///AGREGA ETIQUETA A LOS LOTES
    public function ingresarLabelCaja($caja, $label, $id)
    {
        $sql = "UPDATE detcajas d 
        SET d.idLoteLbl='$label' WHERE 
        d.idEmpaque='$id' AND numCaja='$caja'";
        return $this->runQuery($sql, "actualizar etiqueta de la caja");
    }

    ///TOTAL ALAMCENADO EN LA CAJA
    public function getDetCaja($idEmpaque, $numCaja)
    {
        $sql = "SELECT d.*, r.loteTemola, r.estado, r.regEmpaque 
                FROM detcajas d 
                INNER JOIN rendimientos r ON d.idLote=r.id
                WHERE  d.idEmpaque='$idEmpaque' AND numCaja='$numCaja'
                AND (d.estado<>'0' OR d.estado IS NULL)";
        return  $this->consultarQuery($sql, " Detallado de Caja del Mix");
    }
    /*********************************************
     * PROCESO DE USO DE EMPAQUE DE RECUPERADOS
     *********************************************/
    /// ACTUALIZA EL RENDIMIENTOS DE LAS PIEZAS RECUPERADAS EMPACADAS
    public function actualizaRendimiento($idDetCaja)
    {
        $sql = "UPDATE detcajas dc
            INNER JOIN config_inventarios conf ON conf.estado='1'
            INNER JOIN rendimientos r ON dc.idLote=r.id
            INNER JOIN (SELECT dp.idRendimiento, AVG( p.precioUnitFactUsd ) AS costoProm 
                        FROM detpedidos dp
                        INNER JOIN pedidos p ON dp.idPedido = p.id  
                        GROUP BY dp.idRendimiento) dp ON dp.idRendimiento = r.id 
    SET   r.totalRecu=(IFNULL(r.totalRecu,0)+dc.total),
    r.areaCrustSet=r.areaCrust/((IFNULL(r.totalEmp,0)+dc.total)/conf.pzasEnSets),
    r.areaWBUnidad= r.areaWB/((IFNULL(r.totalEmp,0)+dc.total)/conf.pzasEnSets),
    r.costoWBUnit= (r.areaWB/((IFNULL(r.totalEmp,0)+dc.total)/conf.pzasEnSets))* dp.costoProm,
    r.piezasRecuperadas= IFNULL(r.piezasRecuperadas,0)+dc.total, 
    r.setsRecuperados= (IFNULL(r.piezasRecuperadas,0)+dc.total)/conf.pzasEnSets
   /* r.setsRechazados= IFNULL(r.setsRechazados, 0)-(IFNULL(dc.total,0)/conf.pzasEnSets),
    r.pzasSetsRechazadas= (IFNULL(r.pzasSetsRechazadas, 0)-IFNULL(dc.total,0))*/
    WHERE dc.id='$idDetCaja'";
        return  $this->runQuery($sql, "actualización de datos de rendimiento");
    }
    /// ACTUALIZA EL STOCK DE LAS PIEZAS RECUPERADAS EMPACADAS
    public  function disminuirInventarioRecuperacion($idLote, $pzas_12, $pzas_03, $pzas_06, $pzas_09, $total)
    {
        $sql = "UPDATE inventariorecuperado SET _12=_12-'$pzas_12', _3=_3-'$pzas_03', _6=_6-'$pzas_06', _9=_9-'$pzas_09', 
        pzasTotales=pzasTotales-'$total'
        WHERE idRendimiento='$idLote'";
        return $this->runQuery($sql, "actualizar inventario de recuperación");
    }

    /*********************************************
     * PROCESO DE INGRESO DE SCRAP DE PIEZAS SIN EMPACAR Y SIN MARCAR EN REMANENTE
     *********************************************/
    ///CONSULTA STOCK EXISTENTE DE STOCK DE RECHAZO
    public function getStkScrap($idLote)
    {
        $sql = "SELECT * FROM inventariorechazado 
        WHERE idRendimiento='$idLote'";
        return  $this->consultarQuery($sql, " Detallado de Inventario de Rechazado", true);
    }
    ///MUESTRA LOS DATOS DE DETALLADO DE CAJA: EXCEPCION CON EL TRIGGER DE LA APLICACION
    public function getDetallado($idLote)
    {
        $sql = "SELECT v1.*, IFNULL(v2.total_12,0) AS total_12_RW,  
        IFNULL(v2.total_3,0) AS total_3_RW,
        IFNULL(v2.total_6,0) AS total_6_RW,
        IFNULL(v2.total_9,0) AS total_9_RW,
        IFNULL(v2.total,0) AS total_RW
        FROM vw_detalladocaja v1
        LEFT JOIN vw_detalladorecuperacion v2 ON v1.idLote=v2.idLote
        WHERE v1.idLote='$idLote'";
        return  $this->consultarQuery($sql, "", false);
    }

    ///AGREGAR INVENTARIO DE RECHAZO
    public function agregarPzasScrap($idLote)
    {
        ///CONSULTAR DETALLADO DE CAJA
        $Data = $this->getDetallado($idLote);
        Excepciones::validaConsulta($Data);
        if (count($Data) < 0) {
            return "Error, en la busqueda del detallado de las cajas.";
        }
        $scrap12 = $Data['scrap12'] == '' ? '0' : trim($Data['scrap12']);
        $scrap3 = $Data['scrap3'] == '' ? '0' : trim($Data['scrap3']);
        $scrap6 = $Data['scrap6'] == '' ? '0' : trim($Data['scrap6']);
        $scrap9 = $Data['scrap9'] == '' ? '0' : trim($Data['scrap9']);
        $totalScrap = $scrap12 + $scrap3 + $scrap6 + $scrap9;

        $total_12_RW = $Data['total_12_RW'] == '' ? '0' : trim($Data['total_12_RW']);
        $total_3_RW = $Data['total_3_RW'] == '' ? '0' : trim($Data['total_3_RW']);
        $total_6_RW = $Data['total_6_RW'] == '' ? '0' : trim($Data['total_6_RW']);
        $total_9_RW = $Data['total_9_RW'] == '' ? '0' : trim($Data['total_9_RW']);
        $totalRW = $Data['total_RW'] == '' ? '0' : trim($Data['total_RW']);
  
        $_12Act = $scrap12 - $total_12_RW;
        $_3Act = $scrap3 - $total_3_RW;
        $_6Act = $scrap6 - $total_6_RW;
        $_9Act = $scrap9 - $total_9_RW;

        $totalAct = $totalScrap - $totalRW;

        $sql = "UPDATE inventariorechazado i
        SET i.pzasTotales= '$totalAct',
        i._12='$_12Act', i._3='$_3Act', i._6='$_6Act', 
        i._9='$_9Act'
        WHERE i.idRendimiento='$idLote'";
        return  $this->runQuery($sql, "traspaso a inventario rechazado");
    }
    ///AGREGAR INVENTARIO DE RECHAZO & PZAS DE SCRAP COMO INICIALES
    public function agregarStkPzasScrap($idLote)
    {
        $idUserReg = $this->idUserReg;
        $sql = "INSERT INTO inventariorechazado 
          (idRendimiento, pzasTotales, fechaReg, idUserReg, _12, _6, _3, _9)
          SELECT idLote, IFNULL(scrap12,0)+IFNULL(scrap3,0)+IFNULL(scrap6,0)+IFNULL(scrap9,0),
          NOW(), '$idUserReg', scrap12, scrap6, scrap3, scrap9
          FROM vw_detalladocaja 
          WHERE idLote='$idLote'";
        return $this->runQuery($sql, "registro de Inventario de Rechazo");
    }
    /*********************************************
     * VISUALIZACION DE KPIS SOBRE EMPAQUE
     *********************************************/
    //DESGLOSE DE PIEZAS EMPACADAS
    public function getPzasOkCajas($filtradoFecha = '', $activaID = '')
    {
        //e.fecha='2022-12-21'
        $filtradoID = $activaID != '' ? "d.idLote='$activaID'" : '1=1';
        $filtradoFecha = $filtradoFecha == '' ? '1=1' : "r.semanaProduccion='$filtradoFecha' AND YEAR(r.fechaEmpaque)=YEAR(NOW())";
        $sql = "SELECT d.idLote, r.loteTemola,
        SUM(IF(d.remanente!='1', d._12, '0')) AS sum_12,
        SUM(IF(d.remanente!='1', d._3, '0')) AS sum_3, 
        SUM(IF(d.remanente!='1', d._6, '0')) AS sum_6,
        SUM(IF(d.remanente!='1', d._9, '0')) AS sum_9,
        
        SUM(IF(d.remanente='1', d._12, '0')) AS sumr_12,
        SUM(IF(d.remanente='1', d._3, '0')) AS sumr_3, 
        SUM(IF(d.remanente='1', d._6, '0')) AS sumr_6,
        SUM(IF(d.remanente='1', d._9, '0')) AS sumr_9,
        
        SUM(IF(d.remanente!='1', d._12, '0'))+SUM(IF(d.remanente!='1', d._3, '0'))+
        SUM(IF(d.remanente!='1', d._6, '0'))+SUM(IF(d.remanente!='1', d._9, '0')) AS pzasTotalEmp,
        
        SUM(IF(d.remanente='1', d._12, '0'))+SUM(IF(d.remanente='1', d._3, '0'))+
        SUM(IF(d.remanente='1', d._6, '0'))+SUM(IF(d.remanente='1', d._9, '0')) AS pzasTotalRem
        
        FROM detcajas d
        INNER JOIN rendimientos r ON d.idLote=r.id
        INNER JOIN empaques e ON d.idEmpaque=e.id
        WHERE $filtradoFecha  AND $filtradoID AND (d.estado<>'0' OR d.estado IS NULL)
        GROUP BY d.idLote";
        if ($activaID != '') {
            return $this->consultarQuery($sql, "Desglose de Piezas Empacadas", false);
        } else {
            return $this->consultarQuery($sql, "Desglose de Piezas Empacadas");
        }
    }
    //DESGLOSE DE LOTES REGISTRADOS EN LA SEMANA
    public function getLotesRegistradosSemana($semana)
    {
        $sql = "SELECT r.id,r.loteTemola,  DATE_FORMAT(r.fechaEmpaque,'%d/%m/%Y') AS f_fehaEmpaque,
        r.pzasCortadasTeseo, r.pzasOk, r.pzasNok, r.totalEmp
        FROM rendimientos r
        WHERE r.semanaProduccion='$semana' AND YEAR(r.fechaEmpaque)=YEAR(NOW())
        AND r.tipoProceso='1'";
        return $this->consultarQuery($sql, "Desglose de Lotes Registrados en la Semana");
    }
    //DESGLOSE DE LOTES CON PZAS NOK
    public function getLotesPzasNokAct($semana)
    {
        $sql = "SELECT r.id, r.loteTemola, ir.pzasTotales, ir._12, ir._6, ir._3,
        ir._9
        FROM rendimientos r
        INNER JOIN inventariorechazado ir ON r.id=ir.idRendimiento
        WHERE r.semanaProduccion='$semana' AND YEAR(r.fechaEmpaque)=YEAR(NOW())";
        return $this->consultarQuery($sql, "Desglose de Lotes Registrados en la Semana");
    }

    /**************************************************
     * REPRTE DE DATOS CAPTURADOS DESDE TESEO Y PZAS OK ACTUALES PARA UTILIZAR
     ************************************************/
    public function getDetLote($id)
    {
        $sql = "SELECT r.*
        FROM rendimientos r
        WHERE r.id='$id'";
        return $this->consultarQuery($sql, "Desglose de Lotes Seleccionado", false);
    }

    public function getLotes($busqId = '')
    {
        $filtradoID = $busqId == '' ? '1=1' : "r.loteTemola LIKE '%$busqId%'";
        $sql = "SELECT r.id, r.loteTemola 
        FROM rendimientos r 
        WHERE r.estado!='0' AND $filtradoID AND r.regTeseo='1'
        AND r.tipoProceso='1'
        ORDER BY r.loteTemola";
        return $this->consultarQuery($sql, "Desglose de Lotes");
    }
    ///CONSULTA DETALLADO DE CAJAS
    public function getDetalladoCaja($idLote)
    {

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
             MIN(IFNULL(r.regDatos,0)) AS regDatos,
             MIN(IFNULL(d.interna,0)) AS interna,
             d.lote0,

            lbl.loteTemola AS lblLote,
            
             SUBSTRING_INDEX(GROUP_CONCAT(d.idVenta ORDER BY d.total DESC),',',1) AS idVenta,
             SUBSTRING_INDEX(GROUP_CONCAT(d.numCaja ORDER BY d.total DESC),',',1) AS numCaja,
             IFNULL(idLoteLbl,d.idLote) AS idLote
                 FROM detcajas d 
                 INNER JOIN rendimientos r ON d.idLote=r.id 
                 LEFT JOIN rendimientos lbl ON d.idLoteLbl=lbl.id
                 WHERE (d.remanente!='1' OR d.remanente IS NULL) 
                 AND (d.estado<>'0' OR d.estado IS NULL)
                 GROUP BY d.idEmpaque, d.numCaja ORDER BY d.idLote) d
             INNER JOIN empaques e ON d.idEmpaque=e.id
             WHERE d.idLote='$idLote' 
             GROUP BY d.idEmpaque, d.numCaja";
        return $this->consultarQuery($sql, "detallado de cajas");
    }

    public function getReporteRemanente($filtradoPrograma = '1=1')
    {
        $sql = "SELECT r.loteTemola, dc.*,
        cp.nombre AS nPrograma, dc.totalRem/conf.pzasEnSets AS setscajas
        FROM detcajas dc
        INNER JOIN rendimientos r ON dc.idLote=r.id
        INNER JOIN catprogramas cp ON r.idCatPrograma=cp.id
        INNER JOIN config_inventarios conf ON conf.estado='1'
        WHERE dc.remanente='1' AND $filtradoPrograma
        ORDER BY dc.usoRemanente ASC";
        return $this->consultarQuery($sql, "consulta detallado de remanentes");
    }

    public function getStockRecuperacionXLote($idLote)
    {
        $sql = "SELECT * FROM inventariorecuperado
        WHERE idRendimiento='$idLote'";
        return $this->consultarQuery($sql, "consulta stock de recuperación", false);
    }

    public function getRemanenteXLote($idLote)
    {
        $sql = " SELECT dc.*
        FROM detcajas dc 
        WHERE dc.remanente='1' AND dc.total>0 AND dc.usoRemanente='0' AND dc.idLote='$idLote'";
        return $this->consultarQuery($sql, "consulta stock de recuperación", false);
    }

    public function getStockCajas()
    {
        $sql = "SELECT SUM(total) AS pzasDisponible,
        COUNT(numCaja) AS cantCaja, lote,nPrograma,
        r.loteTemola
        FROM 
        (SELECT SUM(d.total) AS total, d.numCaja, d.idEmpaque, GROUP_CONCAT(DISTINCT d.idLote) AS idslotes,  
        GROUP_CONCAT(DISTINCT r.loteTemola) AS nameslotes,  cp.nombre AS nPrograma, COUNT(d.id) AS cantCaja,
        IF(COUNT(d.id)>=2, d.idLoteLbl, d.idLote) AS lote
        FROM detcajas d
        INNER JOIN rendimientos r ON d.idLote=r.id
        INNER JOIN catprogramas cp ON r.idCatPrograma=cp.id
        WHERE (idVenta IS NULL OR idVenta='0') 
        AND remanente<>'1' AND (d.estado<>'0' OR d.estado IS NULL)
        GROUP BY idEmpaque, numCaja) a
        INNER JOIN rendimientos r ON a.lote=r.id
        GROUP BY a.lote
        ORDER BY a.nPrograma";
        return $this->consultarQuery($sql, "consulta stock de cajas");
    }

    public function actualizarLote0EnCaja($numCaja, $idEmpaque, $lote0)
    {
        $sql = "UPDATE detcajas d
        SET d.lote0='$lote0'
        WHERE d.idEmpaque='$idEmpaque' AND d.numCaja='$numCaja'";
        return $this->runQuery($sql, "registro de actualizar Lote 0 de la caja");
    }

    public function aumentarPzasLote0($numCaja, $idEmpaque, $lote0)
    {
        $sql = "UPDATE        
        detcajas d
        INNER JOIN rendimientos r ON d.idLote=r.id
        INNER JOIN (SELECT dp.idRendimiento, AVG( p.precioUnitFactUsd ) AS costoProm 
                        FROM detpedidos dp
                        INNER JOIN pedidos p ON dp.idPedido = p.id  
                        GROUP BY dp.idRendimiento) dp ON dp.idRendimiento = r.id 
        SET r.totalEmp=r.totalEmp-d.total,
        r.totalRecu=r.totalRecu-d.total,
        r.totalLote0=r.totalLote0+d.total,
        r.areaCrustSet=r.areaCrust/((IFNULL((r.totalEmp-d.total),0)+d.total)/4),
        r.areaWBUnidad= r.areaWB/((IFNULL((r.totalEmp-d.total),0)+d.total)/4),
        r.costoWBUnit= (r.areaWB/((IFNULL((r.totalEmp-d.total),0)+d.total)/4))* dp.costoProm,
        r.piezasRecuperadas= IFNULL(r.piezasRecuperadas,0)-d.total, 
        r.setsRecuperados= (IFNULL(r.piezasRecuperadas,0)-d.total)/4
        WHERE d.idEmpaque='$idEmpaque'  AND (d.estado<>'0' OR d.estado IS NULL)  AND d.numCaja='$numCaja' AND tipo='3'";
        return $this->runQuery($sql, "registro de Aumento Lote 0 de la caja");
    }

    public function disminuirPzasLote0($numCaja, $idEmpaque, $lote0)
    {
        $sql = "UPDATE        
        detcajas d
        INNER JOIN rendimientos r ON d.idLote=r.id
        INNER JOIN (SELECT dp.idRendimiento, AVG( p.precioUnitFactUsd ) AS costoProm 
                        FROM detpedidos dp
                        INNER JOIN pedidos p ON dp.idPedido = p.id  
                        GROUP BY dp.idRendimiento) dp ON dp.idRendimiento = r.id 
        SET r.totalEmp=r.totalEmp+d.total,
        r.totalRecu=r.totalRecu+d.total,
        r.totalLote0=r.totalLote0-d.total,
        r.areaCrustSet=r.areaCrust/((IFNULL((r.totalEmp+d.total),0)+d.total)/4),
        r.areaWBUnidad= r.areaWB/((IFNULL((r.totalEmp+d.total),0)+d.total)/4),
        r.costoWBUnit= (r.areaWB/((IFNULL((r.totalEmp+d.total),0)+d.total)/4))* dp.costoProm,
        r.piezasRecuperadas= IFNULL(r.piezasRecuperadas,0)+d.total, 
        r.setsRecuperados= (IFNULL(r.piezasRecuperadas,0)+d.total)/4
        WHERE d.idEmpaque='$idEmpaque' AND (d.estado<>'0' OR d.estado IS NULL) AND d.numCaja='$numCaja' AND tipo='3'";
        return $this->runQuery($sql, "registro de Disminución Lote 0 de la caja");
    }

    //Pase de pzas de caja a pzas disponible del lote
    public function traspasarPzasCjaLote($idCja)
    {
        $sql = "UPDATE detcajas d
        INNER JOIN rendimientos r ON d.idLote=r.id
        LEFT JOIN inventarioempacado i ON r.id=i.idRendimiento
        SET r._12OKAct=IFNULL(r._12OKAct, 0)+IFNULL(d._12,0),
        r._3OKAct=IFNULL(r._3OKAct, 0)+IFNULL(d._3,0), 
        r._6OKAct=IFNULL(r._6OKAct, 0)+IFNULL(d._6,0), 
        r._9OKAct=IFNULL(r._9OKAct, 0)+IFNULL(d._9,0),
        r.totalEmp=IFNULL(r.totalEmp, 0)-IFNULL(d.total,0),
        r.setsEmpacados=(IFNULL(r.totalEmp, 0)-IFNULL(d.total,0))/4,
        i.pzasTotales= IFNULL(i.pzasTotales, 0)-IFNULL(d.total,0),
        r.regEmpaque='0'
        WHERE d.id='$idCja'";
        return $this->runQuery($sql, "traspaso de Caja a Piezas Disponibles");
    }
    //Pase de pzas de caja de remanentes disponible del lote
    public function traspasarPzasRemLote($idCja)
    {
        $sql = "UPDATE detcajas d
        INNER JOIN detcajas rem ON d.idLote=rem.idLote AND rem.remanente='1'
        INNER JOIN rendimientos r ON d.idLote=r.id
        LEFT JOIN inventarioempacado i ON r.id=i.idRendimiento
        SET rem._12Rem=IFNULL(rem._12Rem, 0)+IFNULL(d._12,0),
        rem._3Rem=IFNULL(rem._3Rem, 0)+IFNULL(d._3,0), 
        rem._6Rem=IFNULL(rem._6Rem, 0)+IFNULL(d._6,0), 
        rem._9Rem=IFNULL(rem._9Rem, 0)+IFNULL(d._9,0),
        rem.totalRem=IFNULL(rem.totalRem, 0)+IFNULL(d.total,0),
        r.totalEmp=IFNULL(r.totalEmp, 0)-IFNULL(d.total,0),
        r.setsEmpacados=(IFNULL(r.totalEmp, 0)-IFNULL(d.total,0))/4,
        i.pzasTotales= IFNULL(i.pzasTotales, 0)-IFNULL(d.total,0),
        rem.usoRemanente='0'
        WHERE d.id='$idCja'";
        return $this->runQuery($sql, "traspaso de Caja de Remanente a Piezas Disponibles");
    }
    //Pase de pzas de caja de recuperacion disponible del lote
    public function traspasarPzasRecuLote($idCja)
    {
        $sql = "UPDATE detcajas d
         INNER JOIN inventariorecuperado rec ON d.idLote=rec.idRendimiento
         INNER JOIN rendimientos r ON d.idLote=r.id
         LEFT JOIN inventarioempacado i ON r.id=i.idRendimiento
         SET rec._12=IFNULL(rec._12, 0)+IFNULL(d._12,0),
         rec._3 =IFNULL(rec._3 , 0)+IFNULL(d._3,0), 
         rec._6=IFNULL(rec._6, 0)+IFNULL(d._6,0), 
         rec._9=IFNULL(rec._9, 0)+IFNULL(d._9,0),
         rec.pzasTotales=IFNULL(rec.pzasTotales, 0)+IFNULL(d.total,0),
         r.setsEmpacados=(IFNULL(r.totalEmp, 0)-IFNULL(d.total,0))/4,
         r.totalEmp=IFNULL(r.totalEmp, 0)-IFNULL(d.total,0),
         r.totalRecu=IFNULL(r.totalRecu, 0)-IFNULL(d.total,0),
         r.setsRecuperados=(IFNULL(r.totalRecu, 0)-IFNULL(d.total,0))/4,
         i.pzasTotales= IFNULL(i.pzasTotales, 0)-IFNULL(d.total,0)
         WHERE d.id='$idCja'";
        return $this->runQuery($sql, "traspaso de Caja de Recuperación a Piezas Disponibles");
    }

    public function registroDepuracion($idEmpaque, $numCaja, $idError)
    {
        $idUserReg= $this->idUserReg;
        $sql = "UPDATE detcajas d
        SET d.estado='0', d.idErrorLog='$idError', fechaDep=NOW(),
        idUserDep='$idUserReg'
        WHERE d.numCaja='$numCaja' AND d.idEmpaque='$idEmpaque'";
        return $this->runQuery($sql, "baja de caja en el empaque");
    }

    public function reconteoCajas($idEmpaque, $numCaja)
    {
        $sql = "UPDATE detcajas d
        SET d.numCaja=d.numCaja-'1'
        WHERE d.numCaja>'$numCaja' AND d.idEmpaque='$idEmpaque'";
        return $this->runQuery($sql, "reconteo Cajas");
    }

    public function getCajasDepuradas($filtradoPrograma='1=1', $filtradoFecha='1=1')
    {
        $sql = "SELECT r.loteTemola, dc.numCaja, dc.tipo, dc._12,
        dc._3, dc._6, dc._9, dc.total, e.fecha, DATE_FORMAT(e.fecha,'%d/%m/%Y') AS f_fechaEmpaque,
        DATE_FORMAT(dc.fechaDep,'%d/%m/%Y %H:%i') AS f_fechaDepuracion,
        CONCAT(su.nombre, ' ', su.apellidos) AS nUsuarioDep,
        CASE dc.idErrorLog
            WHEN '1' THEN
                'ERROR DE CAPTURA'
            WHEN '2' THEN
                'INVENTARIO OBSOLETO'
            WHEN '3' THEN
                'ERROR DE SISTEMA'
        END AS nError, cp.nombre AS nPrograma,
        CASE dc.tipo
            WHEN '1' THEN
                'EMPAQUE EN LINEA'
            WHEN '2' THEN
                'REMANENTES'
            WHEN '3' THEN
                'RECUPERACIÓN'
        END AS nTipo
        
        FROM detcajas dc
        INNER JOIN rendimientos r ON dc.idLote=r.id
        INNER JOIN catprogramas cp ON r.idCatPrograma=cp.id
        INNER JOIN empaques e ON dc.idEmpaque=e.id
        LEFT JOIN segusuarios su ON dc.idUserDep=su.id
        WHERE dc.estado='0' AND $filtradoFecha AND $filtradoPrograma
        ORDER BY  e.fecha DESC, dc.idEmpaque, dc.numCaja";
        return $this->consultarQuery($sql, "cajas depuradas");
    }

    public function cambiarNumCaja($numCaja, $idEmpaque, $cajaSiguiente, $idEmpaqueN){
        $sql="UPDATE detcajas 
        SET numCaja='$cajaSiguiente', idEmpaque='$idEmpaqueN'
        WHERE numCaja='$numCaja' AND idEmpaque='$idEmpaque'
        AND (estado<>'0' OR estado IS NULL)";
        return $this->runQuery($sql, "cambio de caja en el empaque");

    }
}
