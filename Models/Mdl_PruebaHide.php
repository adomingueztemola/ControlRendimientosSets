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
    public function getPruebasHeads($filtradoFecha='1=1')
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
 
}
