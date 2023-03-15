<?php
class TrabajosRecupera extends ConexionBD
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


    public function getLotesCerrados()
    {
        $sql = "SELECT r.*, ir.pzasTotales FROM rendimientos r 
        INNER JOIN inventariorechazado ir ON r.id=ir.idRendimiento
        WHERE ir.pzasTotales>0 AND r.tipoProceso='1' AND r.estado>='2'";
        return  $this->consultarQuery($sql, " Lotes con Inventario");
    }

    public function getLotesSetsDisponibles()
    {
        $sql = "SELECT r.* FROM rendimientos r 
        WHERE r.estado>='2' AND r.regTeseo='1' AND r.regOkNok='1'  AND r.tipoProceso='1'
        AND (r.lote0 IS NULL OR r.lote0 !='1')";
        return  $this->consultarQuery($sql, " Lotes con Inventario Disponible Empacado");
    }

    public function getRecuperacionXLote($idRendimiento)
    {
        $sql = "SELECT * FROM (SELECT mr.id, mr.totalRecuperacion,
        mr.fechaEntrega, DATE_FORMAT(mr.fechaEntrega, '%d/%m/%Y') AS f_fecha,
        mr.idRendInicio, mr.idRendRecup, '2' AS tipo,
        rr.loteTemola AS loteTemolaRecup, ri.loteTemola AS loteTemolaInicial, 
        mr.tipoRendInicio, mr.porcPerdidaRecuperacion
        FROM materialesrecuperados mr
        INNER JOIN rendimientos rr ON mr.idRendRecup=rr.id
        INNER JOIN rendimientos ri ON mr.idRendInicio=ri.id
        WHERE mr.estado='2' AND 
                (idRendRecup='$idRendimiento')
        UNION
        
        SELECT mr.id, mr.totalRecuperacion,
        mr.fechaEntrega, DATE_FORMAT(mr.fechaEntrega, '%d/%m/%Y') AS f_fecha,
        mr.idRendInicio, mr.idRendRecup, '1' AS tipo,
        rr.loteTemola AS loteTemolaRecup, ri.loteTemola AS loteTemolaInicial, 
        mr.tipoRendInicio, mr.porcPerdidaRecuperacion
        FROM materialesrecuperados mr
        INNER JOIN rendimientos rr ON mr.idRendRecup=rr.id
        INNER JOIN rendimientos ri ON mr.idRendInicio=ri.id
        WHERE mr.estado='2' AND 
                (idRendInicio='$idRendimiento')) a
        ORDER BY a.fechaEntrega, a.tipo";
        return  $this->consultarQuery($sql, " Detalles de Recuperación del Lote");
    }

    public function getRecuperaciones($filtradoFecha='1=1', $manuales=false)
    {
        $filtradoXLSX= !$manuales?'1=1':'(mr.xlsx!="1" OR mr.xlsx IS NULL)';
        $sql = "SELECT mr.*, DATE_FORMAT(mr.fechaInicio,'%d/%m/%Y') AS f_fecha, 
        DATE_FORMAT(mr.fechaEntrega,'%d/%m/%Y') AS f_fechaFinal, 
        rrec.loteTemola AS nLoteRecup, rin.loteTemola AS nLoteInicial,  cp.nombre AS n_programa,
        dr.nombre AS n_defecto, ir.pzasTotales AS pzasDispRechazo, CONCAT('Total 12.00: ', mr._12,
        '<br>Total 03.00: ', mr._3,  '<br>Total 06.00: ', mr._6,  '<br>Total 09.00: ', mr._9) AS detPzas
        FROM materialesrecuperados mr
        INNER JOIN catprogramas cp ON mr.idCatPrograma=cp.id
        INNER JOIN inventariorechazado ir ON mr.idRendInicio=ir.idRendimiento
        LEFT JOIN rendimientos rrec ON mr.idRendRecup=rrec.id
        LEFT JOIN catdefectosrecuperacion dr ON mr.idCatDefectoRecupera=dr.id 
        LEFT JOIN rendimientos rin ON mr.idRendInicio=rin.id
        WHERE $filtradoXLSX AND $filtradoFecha
        ORDER BY  mr.fechaReg DESC";
        return  $this->consultarQuery($sql, " Lotes Recuperados");
    }

    public function getDefectos()
    {
        $sql = "SELECT *
        FROM catdefectosrecuperacion
        WHERE estado='1'";
        return  $this->consultarQuery($sql, " Defectos de Recuperación");
    }
    public function getTrabajadores(){
        $sql = "SELECT v.noTrabajador, CONCAT(v.nombres, ' ', v.apPat, ' ', v.apMat) AS nombreCompletoTrabajador, v.nArea
        FROM autorizapersonalreacond a
        INNER JOIN vw_personaltwm v ON v.noTrabajador COLLATE utf8_unicode_ci =a.noTrabajador COLLATE utf8_unicode_ci 
        WHERE v.estado='1' AND
        a.estado='1'";
        return  $this->consultarQuery($sql, " Empleados autorizados");
    }

    public function registrarRecuperacion(
        $fecha,
        $fechaEntrega,
        $idRendInicio,
        $trabajadorRecibio,
        $nTrabajadorRecibio,
        $idCatPrograma,
        $idRendRecuperado,
        $totalRecuperado,
        $tipoLoteInicio,
        $nameLote,
        $observaciones,
        $defecto
    ) {
        $idUserReg = $this->idUserReg;
        $sql = "INSERT INTO materialesrecuperados (fechaInicio,fechaEntrega, idCatPrograma, idRendInicio, totalInicial, idRendRecup, 
                                                    totalRecuperacion, observaciones, estado, tipoRendInicio,nombreRendInicio, idCatDefectoRecupera,
                                                    porcPerdidaRecuperacion, fechaReg, idUserReg, noTrabajador, nombreCompletoTrabajador, xlsx) 
        VALUES ('$fecha','$fechaEntrega', '$idCatPrograma', '$idRendInicio', '0', '$idRendRecuperado',
                 $totalRecuperado, '$observaciones', '1', '$tipoLoteInicio', '$nameLote', '$defecto', '0', 
                NOW(), '$idUserReg', '$trabajadorRecibio', '$nTrabajadorRecibio', '0')";
        return  $this->runQuery($sql, "registro de material recuperado", true);
    }

    public function disminucionInventarioRechazado($idMaterialRecuperado)
    {
        $sql = "UPDATE materialesrecuperados mr
                INNER JOIN config_inventarios conf ON conf.estado='1'
                INNER JOIN inventariorechazado ir ON mr.idRendInicio=ir.idRendimiento
                SET ir.pzasTotales= ir.pzasTotales-mr.totalRecuperacion, mr.estado='2', mr.cantFinRendInicio=ir.pzasTotales-mr.totalRecuperacion,
                ir.setsTotales= (ir.pzasTotales-mr.totalRecuperacion)/conf.pzasEnSets,  ir.rezago= (ir.pzasTotales-mr.totalRecuperacion)%conf.pzasEnSets
         WHERE mr.id='$idMaterialRecuperado'";
        return  $this->runQuery($sql, "actualización de inventario de salida");
    }

    public function aumentoInventarioEmpacado($idMaterialRecuperado)
    {
        $sql = "UPDATE materialesrecuperados mr
        INNER JOIN config_inventarios conf ON conf.estado='1'
        INNER JOIN inventarioempacado ie ON mr.idRendRecup=ie.idRendimiento
        INNER JOIN sublotesrecuperados s ON s.idRendimiento= mr.idRendRecup AND s.superLote='1'
        SET ie.pzasTotales= ie.pzasTotales+mr.totalRecuperacion, s.pzasTotales=s.pzasTotales+mr.totalRecuperacion,
        s.setsEmpacados= (s.pzasTotales+mr.totalRecuperacion)/conf.pzasEnSets, ie.setsTotales= (ie.pzasTotales+mr.totalRecuperacion)/conf.pzasEnSets, mr.estado='3', 
        mr.cantFinRendRecupera=ie.pzasTotales+mr.totalRecuperacion, ie.rezago= (ie.pzasTotales+mr.totalRecuperacion)%conf.pzasEnSets
        WHERE mr.id='$idMaterialRecuperado'";
        return  $this->runQuery($sql, "actualización de inventario de entrada");
    }

    public function actualizaRendimiento($idMaterialRecuperado)
    {
        $sql = "UPDATE materialesrecuperados mr
                INNER JOIN config_inventarios conf ON conf.estado='1'
                INNER JOIN rendimientos r ON mr.idRendRecup=r.id
                INNER JOIN (SELECT dp.idRendimiento, AVG( p.precioUnitFactUsd ) AS costoProm 
                            FROM detpedidos dp
                            INNER JOIN pedidos p ON dp.idPedido = p.id  
                            GROUP BY dp.idRendimiento) dp ON dp.idRendimiento = r.id 
        SET   r.totalEmp=(r.totalEmp+mr.totalRecuperacion), r.totalRecu=(r.totalRecu+mr.totalRecuperacion),
        r.areaCrustSet=r.areaCrust/((r.totalEmp+mr.totalRecuperacion)/conf.pzasEnSets),
        r.areaWBUnidad= r.areaWB/((r.totalEmp+mr.totalRecuperacion)/conf.pzasEnSets),
        r.costoWBUnit= (r.areaWB/((r.totalEmp+mr.totalRecuperacion)/conf.pzasEnSets))* dp.costoProm, mr.estado='4'
        WHERE mr.id='$idMaterialRecuperado'";
        return  $this->runQuery($sql, "actualización de datos de rendimiento");
    }

    public function actualizaRendRechazado($idMaterialRecuperado)
    {
        $sql = "UPDATE materialesrecuperados mr
        INNER JOIN config_inventarios conf ON conf.estado='1'
        INNER JOIN rendimientos r ON mr.idRendRecup=r.id
        INNER JOIN inventariorechazado ir ON mr.idRendRecup=ir.idRendimiento

        SET  
        r.porcFinalRechazo=(ir.setsTotales/r.setsCortadosTeseo)*100
        WHERE mr.id='$idMaterialRecuperado'";
        return  $this->runQuery($sql, "actualización de datos de rendimiento s/ piezas recuperadas");
    }
    /***************************************/
    /*ACTUALIZA CUEROS DE LAS VENTAS PARA LIBERAR PIEZAS CON CUEROS*/
    /***************************************/
    public function actualizaCuerosVentas($idRendimiento)
    {
        $sql = "UPDATE ventas v 
        INNER JOIN detventas dv ON dv.idVenta=v.id
        INNER JOIN rendimientos r ON r.id= dv.idRendimiento
        INNER JOIN config_inventarios ci ON ci.estado='1'
        SET
        dv.1s= ROUND((r.1s/(r.totalEmp/ci.pzasEnSets))*(dv.sets)),
        dv.2s= ROUND((r.2s/(r.totalEmp/ci.pzasEnSets))*(dv.sets)),
        dv.3s= ROUND((r.3s/(r.totalEmp/ci.pzasEnSets))*(dv.sets)),
        dv.4s= ROUND((r.4s/(r.totalEmp/ci.pzasEnSets))*(dv.sets)),
        dv._20=ROUND((r._20/(r.totalEmp/ci.pzasEnSets))*(dv.sets)),
        dv.total_s=ROUND((r._20/(r.totalEmp/ci.pzasEnSets))*(dv.sets))+ROUND((r.4s/(r.totalEmp/ci.pzasEnSets))*(dv.sets))+
        ROUND((r.3s/(r.totalEmp/ci.pzasEnSets))*(dv.sets))+ROUND((r.2s/(r.totalEmp/ci.pzasEnSets))*(dv.sets))+
        ROUND((r.1s/(r.totalEmp/ci.pzasEnSets))*(dv.sets))
        WHERE dv.idRendimiento='{$idRendimiento}'";
        return $this->ejecutarQuery($sql, "Liberar Piezas de Cueros de Ventas");
    }

    public function getPorcDismVentas($idRendimiento, $totalRecuperado)
    {
        $sql = "SELECT r.id, r.totalEmp, 
        r.totalEmp/ci.pzasEnSets AS setsTotalesEmp, 
        r.total_s, ('$totalRecuperado'/r.totalEmp)  AS porcAumento 
        FROM rendimientos r
        INNER JOIN config_inventarios ci ON ci.estado='1' 
        WHERE r.id='$idRendimiento'";
        return $this->consultarQuery($sql, "porcentaje a disminuir de ventas", false);
    }

    public function getCuerosDismVentas($idRendimiento)
    {
        $sql = "SELECT 
        dv.idRendimiento,(r.totalEmp/ci.pzasEnSets) AS setsEmpacadosTotales, 
        r.1s,
        ROUND((r.1s/(r.totalEmp/ci.pzasEnSets))*(dv.unidades/ci.pzasEnSets)) AS 1s_Calc,
        r.2s,
        ROUND((r.2s/(r.totalEmp/ci.pzasEnSets))*(dv.unidades/ci.pzasEnSets)) AS 2s_Calc,
        r.3s,
        ROUND((r.3s/(r.totalEmp/ci.pzasEnSets))*(dv.unidades/ci.pzasEnSets)) AS 3s_Calc,
        r.4s,
        ROUND((r.4s/(r.totalEmp/ci.pzasEnSets))*(dv.unidades/ci.pzasEnSets)) AS 4s_Calc,
        r._20,
        ROUND((r._20/(r.totalEmp/ci.pzasEnSets))*(dv.unidades/ci.pzasEnSets)) AS _20_Calc
        
        FROM ventas v 
        INNER JOIN detventas dv ON dv.idVenta=v.id
        INNER JOIN rendimientos r ON r.id= dv.idRendimiento
        INNER JOIN config_inventarios ci ON ci.estado='1'
        WHERE dv.idRendimiento='$idRendimiento'";
        return $this->consultarQuery($sql, "ventas del lote con disminución de cueros");
    }
    public function getLogsXLSX(){
        $sql="SELECT *, DATE_FORMAT(fechaReg, '%d/%m/%Y %H:%i') AS fFechaReg FROM logspzasreacond WHERE estado='0'";
        return $this->consultarQuery($sql, "errores en la carga de Excel");

    }
    public function getReacondXLSX(){
        $sql="";
        return $this->consultarQuery($sql, "errores en la carga de Excel");
    }
}
