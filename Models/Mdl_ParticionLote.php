<?php
class ParticionLote extends Rendimiento
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

    public function getTransferenciaLotes($ident)
    {
        $sql = "SELECT * FROM rendimientos r WHERE r.idRendimientoTransfer='$ident'";
        return  $this->consultarQuery($sql, "consultar Transferencias de Lote");
    }
    public function agregarLoteConsecutivo($idLote, $idPrograma, $numParticion, $total_s, $_1s, $_2s, $_3s, $_4s, $_20)
    {
        $sql = "INSERT INTO rendimientos (idRendimientoTransfer, fechaEngrase, idCatProceso, loteTemola, idCatPrograma, idCatMateriaPrima, tipoMateriaPrima, 
        estado, idUserReg, fechaReg, tipoProceso, areaProveedorLote, 1s, 2s, 3s, 4s, _20, total_s)
        SELECT r.id, r.fechaEngrase, r.idCatProceso, CONCAT(r.loteTemola, '.$numParticion') AS loteTemola, r.idCatPrograma, r.idCatMateriaPrima,
            r.tipoMateriaPrima, '2' AS estado, '' AS idUserReg, NOW(),
            CASE 
                WHEN cp.tipo='1' THEN
                    '1'
            WHEN cp.tipo='3' THEN
                    '2'
            END AS tipoProceso, ('$total_s'*AVG(p.areaWBPromFact)) AS areaProveedorLote,
            '$_1s' AS 1s, '$_2s' AS 2s, '$_3s' AS 3s,'$_4s' AS 4s, '$_20' AS _20, '$total_s' AS total_s 

            FROM rendimientos r
            INNER JOIN catprogramas cp ON cp.id='$idPrograma'
            INNER JOIN detpedidos dp ON r.id=dp.idRendimiento
            INNER JOIN pedidos p ON dp.idPedido=p.id
            WHERE r.id='$idLote'
            GROUP BY dp.idRendimiento";
        return $this->runQuery($sql, "agregar Lote Consecutivo", true);
    }
    public function agregarMateriaPrimaNuevLote($query)
    {
        $sql = "INSERT INTO detpedidos (idRendimiento, idPedido, total_s, 1s, 2s, 3s,4s, _20, areaProveedorLote,
        fechaReg, idUserReg, estado,  cantFinalPedido) VALUES 
        $query";
        return $this->runQuery($sql, "disminución de Materia Prima");
    }
    public function disminucionMateriaPrima(
        $id,
        $total_s,
        $_1s,
        $_2s,
        $_3s,
        $_4s,
        $_20,
        $areaProveedorLote
    ) {
        $sql = "UPDATE detpedidos SET 
        total_s='$total_s',  1s='$_1s', 2s='$_2s', 3s='$_3s', 4s='$_4s',
        _20='$_20', areaProveedorLote='$areaProveedorLote'
        WHERE id='$id'";
        return $this->runQuery($sql, "disminución de Materia Prima");
    }

    public function agregarParticion(
        $idLote,
        $idLoteTransfer,
        $idPrograma,
        $numParticion,
        $total_s,
        $_1s,
        $_2s,
        $_3s,
        $_4s,
        $_20,
        $total_sAnt,
        $_1sAnt,
        $_2sAnt,
        $_3sAnt,
        $_4sAnt,
        $_20Ant
    ) {
        $idUserReg = $this->idUserReg;

        $sql = "INSERT INTO particioneslotes (idLote,idLoteTransfer,idPrograma, numParticion, total_s, 1s, 2s, 3s, 4s, _20,
        total_sAnt, 1sAnt, 2sAnt,3sAnt, 4sAnt, _20Ant, fechaReg, idUserReg) 
        VALUES('$idLote','$idLoteTransfer', '$idPrograma', '$numParticion', '$total_s', '$_1s', '$_2s', '$_3s',
        '$_4s', '$_20', '$total_sAnt', '$_1sAnt', '$_2sAnt', '$_3sAnt', '$_4sAnt', '$_20Ant', NOW(), '$idUserReg' )";
        return $this->runQuery($sql, "agregar Partición");
    }

    public function getMateriaPrimaXLote($ident)
    {
        $sql = "SELECT dp.*, p.areaWBPromFact
        FROM detpedidos dp
        INNER JOIN pedidos p ON dp.idPedido=p.id
        WHERE dp.idRendimiento='$ident'";
        return  $this->consultarQuery($sql, "consultar Materia Prima de Lote");
    }


    public function getParticiones()
    {
        $sql = "SELECT p.*, cp.nombre AS nPrograma, r.loteTemola,
        rt.loteTemola AS lotePadre, r.areaProveedorLote
        FROM particioneslotes p
        INNER JOIN rendimientos r ON p.idLote=r.id
        INNER JOIN rendimientos rt ON p.idLoteTransfer=rt.id
        INNER JOIN catprogramas cp ON p.idPrograma=cp.id
        ORDER BY CAST(r.loteTemola AS UNSIGNED) DESC";
        return  $this->consultarQuery($sql, "consultar Transferencias de Lote");
    }

    public function getReprogramacionLotes()
    {
        $sql = "SELECT rp.*, cpi.nombre AS programaAnt,
        cpa.nombre AS programaAct,
        CONCAT(cpri.codigo, '-', cpri.nombre) AS procesoAnt, 
        CONCAT(cpra.codigo, '-',cpra.nombre) AS procesoAct, 
        CASE 
            WHEN rp.tipo='1' THEN
                'PROGRAMACIÓN'
            WHEN rp.tipo='2' THEN
                'SETS'
        END AS s_tipo,
        r.loteTemola, DATE_FORMAT(rp.fechaReg, '%d/%m/%Y %H:%i') AS f_fechaReg
        FROM reasignacionprograma rp 
        INNER JOIN catprogramas cpi ON rp.idProgramaAnt = cpi.id
        INNER JOIN catprogramas cpa ON rp.idProgramaAct = cpa.id
        INNER JOIN catprocesos cpri ON rp.idProcesoAnt = cpri.id
        INNER JOIN catprocesos cpra ON rp.idProcesoAct = cpra.id
        INNER JOIN rendimientos r ON rp.idRendimiento=r.id
        ORDER BY rp.fechaReg DESC";
        return  $this->consultarQuery($sql, "consultar Reprogramación de Lotes");
    }
    public function actualizaLotePadre(
        $lote,
        $total_s,
        $_1s,
        $_2s,
        $_3s,
        $_4s,
        $_20,
        $areaProveedorLote
    ) {
        $sql = "UPDATE rendimientos SET 1s=' $_1s', 2s=' $_2s',
        3s=' $_3s', 4s=' $_4s', _20=' $_20', total_s=' $total_s', 
        areaProveedorLote='$areaProveedorLote' WHERE id='$lote'";
        return $this->runQuery($sql, "actualizar datos del lote");
    }

 
}
