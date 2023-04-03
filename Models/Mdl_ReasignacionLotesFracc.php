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

    public function actualizaLote( $lote,
    $total_s,
    $_1s,
    $_2s,
    $_3s,
    $_4s,
    $_20,
    $areaProveedorLote){
        $sql = "UPDATE rendimientos SET 1s=' $_1s', 2s=' $_2s',
        3s=' $_3s', 4s=' $_4s', _20=' $_20', total_s=' $total_s', 
        areaProveedorLote='$areaProveedorLote' WHERE id='$lote'";
        return $this->runQuery($sql, "actualizar datos del lote");
    }
}
