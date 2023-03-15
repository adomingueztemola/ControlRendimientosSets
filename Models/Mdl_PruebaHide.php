<?php
class PruebaHide extends ConexionBD
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
    public function getPruebasHeads()
    {
        $sql = "SELECT p.*, r.loteTemola, 
        DATE_FORMAT(p.fecha,'%d/%m/%Y') AS fFecha,
        CONCAT(IFNULL(r.yearWeek,'0000'), '-Sem ', IFNULL(LPAD(r.semanaProduccion,2,0),'00')) AS semanaAnio
        FROM pruebashides p
        INNER JOIN rendimientos r ON r.id=p.idLote
        ORDER BY p.fecha";
        return  $this->consultarQuery($sql, "consultar Pruebas de Heads");
    }
    public function agregarPruebaHide($lote, $fecha, $hides)
    {
        $convertCuerosHides = $hides / 2;
        $idUserReg = $this->idUserReg;

        $sql = "INSERT INTO pruebashides (idLote, fecha, hides, 1s, 2s, 3s, 4s, _20, total_s, idUserReg, fechaReg, porcent)
        SELECT id, '$fecha', '$hides', 1s-(1s*('$convertCuerosHides'/total_s)), 2s-(2s*('$convertCuerosHides'/total_s)),  
        3s-(3s*('$convertCuerosHides'/total_s)), 4s-(4s*('$convertCuerosHides'/total_s)), 
        _20-(_20*('$convertCuerosHides'/total_s)), total_s-(total_s*('$convertCuerosHides'/total_s)),
        '$idUserReg', NOW(), '$convertCuerosHides'/total_s
        FROM rendimientos WHERE id='$lote'";
        return $this->runQuery($sql, "detalle de Prueba", true);
    }

    public function paseCuerosLote($idPruebaHide)
    {
        $sql = "UPDATE rendimientos r
          INNER JOIN pruebashides p ON r.id=p.idLote
          SET r.1s=p.1s, r.2s= p.2s, r.3s= p.3s, r.4s= p.4s, 
            r._20= p._20,  r.total_s= p.total_s, 
            r.areaWB= IFNULL(r.areaWB,0)-(IFNULL(r.areaWB,0)*p.porcent),
            p.areaWB= (IFNULL(r.areaWB,0)*p.porcent),
           
            r.areaCrust= IFNULL(r.areaCrust,0)-(IFNULL(r.areaCrust,0)*p.porcent),
            p.areaCrust= (IFNULL(r.areaCrust,0)*p.porcent),

            r.recorteAcabado= IFNULL(r.recorteAcabado,0)-(IFNULL(r.recorteAcabado,0)*p.porcent),
            p.recorteAcabado= (IFNULL(r.recorteAcabado,0)*p.porcent)

          WHERE p.id='$idPruebaHide'";
        return $this->runQuery($sql, "pase de Prueba a Rendimiento");
    }

    public function actualizacionMateriaPrima($idPruebaHide)
    {
        $sql = "UPDATE detpedidos dp
        INNER JOIN pedidos p ON dp.idPedido=p.id
        INNER JOIN pruebashides ph ON dp.idRendimiento=ph.idLote
        SET dp.1s=IFNULL(dp.1s,0)-(IFNULL(dp.1s,0)*ph.porcent), 
        dp.2s=IFNULL(dp.2s,0)-(IFNULL(dp.2s,0)*ph.porcent), 
        dp.3s=IFNULL(dp.3s,0)-(IFNULL(dp.3s,0)*ph.porcent), 
        dp.4s=IFNULL(dp.4s,0)-(IFNULL(dp.4s,0)*ph.porcent), 
        dp._20=IFNULL(dp._20,0)-(IFNULL(dp._20,0)*ph.porcent), 
        dp.total_s=IFNULL(dp.total_s,0)-(IFNULL(dp.total_s,0)*ph.porcent), 
        dp.areaProveedorLote=(IFNULL(dp.total_s,0)-(IFNULL(dp.total_s,0)*ph.porcent))*p.areaWBPromFact,
        ph.areaProveedorLote=IFNULL(ph.hides/2,0)*p.areaWBPromFact
        WHERE ph.id='$idPruebaHide'";
        return $this->runQuery($sql, "pase de Prueba de Materia Prima");
    }

    public function actualizacionVentas($idPruebaHide)
    {
        $sql = "UPDATE detventas dv
        INNER JOIN pruebashides ph ON dv.idRendimiento=ph.idLote
        SET 
        dv.1s=IFNULL(dv.1s,0)-(IFNULL(dv.1s,0)*ph.porcent), 
        dv.2s=IFNULL(dv.2s,0)-(IFNULL(dv.2s,0)*ph.porcent), 
        dv.3s=IFNULL(dv.3s,0)-(IFNULL(dv.3s,0)*ph.porcent), 
        dv.4s=IFNULL(dv.4s,0)-(IFNULL(dv.4s,0)*ph.porcent), 
        dv._20=IFNULL(dv._20,0)-(IFNULL(dv._20,0)*ph.porcent), 
        dv.total_s=IFNULL(dv.total_s,0)-(IFNULL(dv.total_s,0)*ph.porcent)
        WHERE ph.id='$idPruebaHide'";
        return $this->runQuery($sql, "pase de Prueba de Ventas");
    }
}
