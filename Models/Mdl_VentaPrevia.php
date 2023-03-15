<?php
class VentaPrevia extends ConexionBD
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

    public function agregarPzasRequeridas($idDetVenta, $pzas)
    {
        $idUserReg = $this->idUserReg;
        $sql = "INSERT INTO detventasprevias (idDetVenta, pzasTotales, idUserReg, fechaReg) 
              VALUES ('$idDetVenta', '$pzas', '$idUserReg', NOW())";
        return $this->runQuery($sql, "agregar Piezas Requeridas");
    }

    public function actualizaPiezas($idDetVenta, $pzas)
    {
        $idUserReg = $this->idUserReg;
        $sql = "UPDATE  detventasprevias SET pzasTotales='$pzas', idUserReg='$idUserReg', fechaReg=NOW()
              WHERE idDetVenta='$idDetVenta'";
        return $this->runQuery($sql, "agregar Piezas Requeridas");
    }
    public function consultaRequesicionAbierta($idDetVenta)
    {
        $sql = "SELECT * FROM detventasprevias WHERE idDetVenta='$idDetVenta'";
        return $this->consultarQuery($sql, "consultar requision de piezas por venta.", false);
    }

    public function getRequerimientosPzas()
    {
        $sql = "SELECT SUM(dvp.pzasTotales-vw.totalEmp) AS pzasFaltantes, 
        r.loteTemola, cp.nombre AS n_programa, DATE_FORMAT(MIN(v.fechaFact),'%d/%m/%Y') AS fFechaFact
        FROM detventasprevias dvp
        INNER JOIN detventas dv ON dv.id=dvp.idDetVenta
        INNER JOIN ventas v ON dv.idVenta=v.id
        INNER JOIN vw_inventariolotes vw ON dv.idRendimiento=vw.id
        INNER JOIN rendimientos r ON dv.idRendimiento=r.id
        INNER JOIN catprogramas cp ON r.idCatPrograma=cp.id
        WHERE vw.totalEmp<dvp.pzasTotales AND   v.estado='3'
        GROUP BY dv.idRendimiento";
        return $this->consultarQuery($sql, "consultar requision de piezas.");
    }

    public function getVentasProgramadas($filtradoFecha='1=1'){
        $sql = "SELECT v.*, ctv.nombre AS n_tipoVenta,
        DATE_FORMAT(v.fechaFact,'%d/%m/%Y') AS fFechaFact,
        abs.totalAbastecidos, abs.totalRequeri
        FROM ventas v
        INNER JOIN cattiposventas ctv ON v.idTipoVenta=ctv.id
        LEFT JOIN (SELECT dvp.*, dv.idVenta, count(dv.idVenta) AS totalRequeri,
                sum(IF(vw.totalEmp>=dvp.pzasTotales, 1,0)) AS totalAbastecidos
                FROM detventasprevias dvp
                INNER JOIN detventas dv ON dv.id=dvp.idDetVenta
                INNER JOIN ventas v ON dv.idVenta=v.id
                INNER JOIN vw_inventariolotes vw ON dv.idRendimiento=vw.id
                INNER JOIN rendimientos r ON dv.idRendimiento=r.id
                INNER JOIN catprogramas cp ON r.idCatPrograma=cp.id
                GROUP BY dv.idVenta)abs ON abs.idVenta=v.id 
        WHERE v.estado='3' AND $filtradoFecha";
        return $this->consultarQuery($sql, "consultar requision de piezas.");
    }

    public function getRequerimientosPzasXVenta($id){
        $sql = "SELECT dvp.*, vw.totalEmp, dvp.pzasTotales-vw.totalEmp AS pzasFaltantes, 
        r.loteTemola, cp.nombre AS n_programa, DATE_FORMAT(v.fechaFact,'%d/%m/%Y') AS fFechaFact,
        dv.idVenta
        FROM detventasprevias dvp
        INNER JOIN detventas dv ON dv.id=dvp.idDetVenta
        INNER JOIN ventas v ON dv.idVenta=v.id
        INNER JOIN vw_inventariolotes vw ON dv.idRendimiento=vw.id
        INNER JOIN rendimientos r ON dv.idRendimiento=r.id
        INNER JOIN catprogramas cp ON r.idCatPrograma=cp.id
        WHERE dv.idVenta='$id'";
        return $this->consultarQuery($sql, "consultar requision de piezas.");
    }
}
