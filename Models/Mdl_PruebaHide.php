<?php
class PruebaHide extends Rendimiento
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
    public function getPruebasHeads($filtradoFecha = '1=1')
    {
        $sql = "SELECT p.*, r.loteTemola, 
        DATE_FORMAT(p.fecha,'%d/%m/%Y') AS fFecha,
        CONCAT(IFNULL(r.yearWeek,'0000'), '-Sem ', IFNULL(LPAD(r.semanaProduccion,2,0),'00')) AS semanaAnio
        FROM pruebashides p
        INNER JOIN rendimientos r ON r.id=p.idLote
        WHERE $filtradoFecha
        ORDER BY p.fecha";
        return  $this->consultarQuery($sql, "consultar Pruebas de Heads");
    }
    public function agregarPruebaHide($lote, $fecha, $hides)
    {
        $idUserReg = $this->idUserReg;
        $sql = "CALL calculaPruebasHide('$lote', '$fecha', '$hides', '$idUserReg')";
        return $this->runQuery($sql, "detalle de Prueba");
    }
    //CALCULAR RENDIMIENTO DEL LOTE
    public function calcularRendimientoEnPrueba($idLote)
    {
        $idUserReg = $this->idUserReg;
        $sql = "CALL calcularRendimientoFase2('{$idLote}','{$idUserReg}', '0')";
        return $this->ejecutarQuery($sql, "actualizar Rendimiento");
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
    public function actualizaLoteMuestral(
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
    public function agregarRegistroPruebasHides(
        $idLote,
        $fecha,
        $hides,
        $total_s,
        $_1s,
        $_2s,
        $_3s,
        $_4s,
        $_20,
        $porcent,
        $areaProveedorLoteSobrante

    ) {
        $idUserReg = $this->idUserReg;
        $sql = "INSERT INTO pruebashides (idLote, fecha, hides, 1s, 2s, 3s, 4s, _20, 
        total_s, idUserReg, fechaReg, porcent, areaProveedorLote)
        VALUES ('$idLote', '$fecha', '$hides',  '$_1s',  '$_2s','$_3s','$_4s','$_20', 
        '$total_s', '$idUserReg', NOW(), '$porcent', '$areaProveedorLoteSobrante');";
        return $this->runQuery($sql, "actualizar registro de traspaso");
    }
}
