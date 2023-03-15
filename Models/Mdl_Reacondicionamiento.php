<?php
class Reacondicionamiento extends ConexionBD
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

    public function getDetPzasRecup($id)
    {
        $sql = "SELECT m.*, 
        ir._12 AS _12Stk,  ir._3 AS _3Stk, ir._6  AS _6Stk, ir._9 AS _9Stk 
        FROM materialesrecuperados m 
        INNER JOIN inventariorechazado ir ON m.idRendInicio=ir.idRendimiento

        WHERE m.id='$id'";
        return $this->consultarQuery($sql, "detalles de la recuperación", false);
    }

    public function getRecuperaciones($filtradoFecha = '1=1')
    {
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
        WHERE $filtradoFecha
        ORDER BY  mr.fechaReg DESC";
        return  $this->consultarQuery($sql, " Lotes Recuperados");
    }

    public function getLotesSetsDisponibles()
    {
        $sql = "SELECT r.* FROM rendimientos r 
        WHERE r.estado>='2' AND r.regTeseo='1' AND r.regOkNok='1'  AND r.tipoProceso='1'
        AND (r.lote0 IS NULL OR r.lote0 !='1')";
        return  $this->consultarQuery($sql, " Lotes con Inventario Disponible Empacado");
    }

    public function getRecuperacion($id){
        $sql = "SELECT * FROM materialesrecuperados 
        WHERE id='$id'";
        return  $this->consultarQuery($sql, " Detallado de Recuperacion", false);
    }

    public function getStkRecuperacion($idLote){
        $sql = "SELECT * FROM inventariorecuperado 
        WHERE idRendimiento='$idLote'";
        return  $this->consultarQuery($sql, " Detallado de Inventario de Recuperación", true);
    }

    public function registrarRecuperacion(
        $fecha,
        $idRendInicio,
        $trabajadorRecibio,
        $nTrabajadorRecibio,
        $idCatPrograma,
        $tipoLoteInicio,
        $nameLote,
        $observaciones,
        $defecto
    ) {
        $idUserReg = $this->idUserReg;
        $sql = "INSERT INTO materialesrecuperados (fechaInicio,fechaEntrega, idCatPrograma, idRendInicio, totalInicial, idRendRecup, 
                                                    totalRecuperacion, observaciones, estado, tipoRendInicio,nombreRendInicio, idCatDefectoRecupera,
                                                    porcPerdidaRecuperacion, fechaReg, idUserReg, noTrabajador, nombreCompletoTrabajador) 
        VALUES ('$fecha','', '$idCatPrograma', '$idRendInicio', '0', '0',
                 '0', '$observaciones', '1', '$tipoLoteInicio', '$nameLote', '$defecto', '0', 
                NOW(), '$idUserReg', '$trabajadorRecibio', '$nTrabajadorRecibio')";
        return  $this->runQuery($sql, "registro de material recuperado");
    }

    public function registrarFechaEntrega($fechaEntrega, $id)
    {
        $sql = "UPDATE materialesrecuperados SET fechaEntrega='$fechaEntrega' WHERE id='$id'";
        return  $this->runQuery($sql, "registro de fecha de Entrega");
    }

    public function registrarLoteRecup($loteRecup, $id)
    {
        $sql = "UPDATE materialesrecuperados SET idRendRecup='$loteRecup' WHERE id='$id'";
        return  $this->runQuery($sql, "registro de Lote de Recuperación");
    }

    public function registrarTotal($total, $id, $_12, $_3, $_9, $_6)
    {
        $sql = "UPDATE materialesrecuperados SET totalRecuperacion='$total', _12='$_12', _3='$_3',
        _9='$_9', _6='$_6' WHERE id='$id'";
        return  $this->runQuery($sql, "registro de Total de Recuperación");
    }

    public function agregarPzasRecuperadas($id)
    {
        $sql = "UPDATE inventariorecuperado i
        INNER JOIN materialesrecuperados m ON m.idRendRecup=i.idRendimiento
        SET i.pzasTotales= i.pzasTotales+m.totalRecuperacion,
        i._12=IFNULL(i._12,0)+m._12, i._3=IFNULL(i._3,0)+m._3, i._6=IFNULL(i._6,0)+m._6, 
        i._9=IFNULL(i._9,0)+m._9 WHERE m.id='$id'";
        return  $this->runQuery($sql, "traspaso a inventario");
    }

    public function agregarStkPzasRecuperadas($id){
        $idUserReg=$this->idUserReg;
        $sql = "INSERT INTO inventariorecuperado (idRendimiento, pzasTotales, fechaReg, idUserReg, _12, _3, _6,_9)
        SELECT m.idRendRecup, m.totalRecuperacion, NOW(), '$idUserReg', 
        m._12, m._3, m._6, m._9 
        FROM materialesrecuperados m WHERE  m.id='$id'";
        return  $this->runQuery($sql, "traspaso a inventario con creación de Stock");
    }

    public function disminuirScrap($id)
    {
        $sql = "UPDATE inventariorechazado i
        INNER JOIN rendimientos r ON i.idRendimiento=r.id
        INNER JOIN materialesrecuperados m ON m.idRendInicio=i.idRendimiento
        SET r.totalRech=r.totalRech-m.totalRecuperacion,  
        i.pzasTotales= i.pzasTotales-m.totalRecuperacion, i._12=IFNULL(i._12,0)-IFNULL(m._12,0),
        i._3=IFNULL(i._3,0)-IFNULL(m._3,0),  i._6=IFNULL(i._6,0)-IFNULL(m._6,0),  i._9=IFNULL(i._9,0)-IFNULL(m._9,0)
        WHERE m.id='$id'";
        return  $this->runQuery($sql, "disminución a inventario rechazado");
    }

    public function eliminarReacondicionamiento($id){
        $sql = "DELETE FROM materialesrecuperados 
        WHERE id='$id'";
        return  $this->runQuery($sql, "eliminar registro de Reacondicionamiento");
    }
}
