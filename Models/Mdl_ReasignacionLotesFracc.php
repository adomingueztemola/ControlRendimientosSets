<?php
class ReasignacionLotesFracc extends Rendimiento
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
    public function getMateriaPrimaXLote($ident)
    {
        $sql = "SELECT dp.*, p.areaWBPromFact
        FROM detpedidos dp
        INNER JOIN pedidos p ON dp.idPedido=p.id
        WHERE dp.idRendimiento='$ident'";
        return  $this->consultarQuery($sql, "consultar Materia Prima de Lote");
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

    public function agregarMateriaPrima($query)
    {
        $sql = "INSERT INTO detpedidos (idRendimiento, idPedido, total_s, 1s, 2s, 3s,4s, _20, areaProveedorLote,
        fechaReg, idUserReg, estado,  cantFinalPedido) VALUES 
        $query";
        return $this->runQuery($sql, "agregar de Materia Prima");
    }

    public function actualizaLote(
        $lote,
        $total_s,
        $_1s,
        $_2s,
        $_3s,
        $_4s,
        $_20,
        $areaProveedorLote
    ) {
        $sql = "UPDATE rendimientos SET 1s='$_1s', 2s='$_2s',
        3s='$_3s', 4s='$_4s', _20='$_20', total_s='$total_s', 
        areaProveedorLote='$areaProveedorLote' WHERE id='$lote'";
        return $this->runQuery($sql, "actualizar datos del lote");
    }

    public function registroTraspaso(
        $idLoteTx,
        $total_sTx,
        $_1sTx,
        $_2sTx,
        $_3sTx,
        $_4sTx,
        $_20Tx,
        $idLoteRx,
        $total_sRx,
        $_1sRx,
        $_2sRx,
        $_3sRx,
        $_4sRx,
        $_20Rx,
        $areaProveedorRx
    ) {
        $idUserReg = $this->idUserReg;
        $sql = "INSERT INTO reasignacionfracclotes (idLoteTx,total_sTx, 1sTx,2sTx,3sTx,4sTx,
        _20Tx,  idLoteRx, total_sRx, 1sRx,2sRx,3sRx,4sRx,_20Rx, areaProveedorRx,fechaReg, idUserReg) 
        VALUES('$idLoteTx', '$total_sTx','$_1sTx','$_2sTx', '$_3sTx', '$_4sTx', '$_20Tx',
        '$idLoteRx', '$total_sRx','$_1sRx','$_2sRx', '$_3sRx', '$_4sRx', '$_20Rx',  '$areaProveedorRx', NOW(),
        '$idUserReg')";
        return $this->runQuery($sql, "registro de Traspaso");
    }

    public function getTraspasosRegistrados(){
        $sql = "SELECT rf.*,tx.loteTemola AS nLoteTx,
        rx.loteTemola AS nLoteRx,
        DATE_FORMAT(rf.fechaReg,'%d/%m/%Y %H:%i') AS f_fechaReg
        FROM reasignacionfracclotes rf
        INNER JOIN rendimientos tx ON rf.idLoteTx=tx.id
        INNER JOIN rendimientos rx ON rf.idLoteRx=rx.id
        ORDER BY rf.fechaReg DESC";
        return  $this->consultarQuery($sql, "consultar Traspasos de Lotes");
    }
}
