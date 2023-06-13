<?php
class Alerta extends ConexionBD
{
    //private $link;
    protected $debug;
    private $idUserReg;
    private $noEmpleado;


    public function __construct($debug, $idUser)
    {
        $this->link = conectar::conexion();
        $this->debug = $debug;
        $this->idUserReg = $idUser;
    }

    public function __destruct()
    {
        $this->link->close();
    }

    public function getLotesXCapturar()
    {
        $sql = "SELECT COUNT(r.id) AS total
        FROM rendimientos r 
        WHERE  (r.regDatos IS NULL OR r.regDatos!='1') AND (r.regEmpaque='1' OR r.tipoProceso='2')";
        return  $this->consultarQuery($sql, "consultar Lotes por Capturar", false);
    }
    public function getLogsActivos()
    {
        $sql = "SELECT COUNT(id) AS total FROM logspzasreacond WHERE estado='0'";
        return  $this->consultarQuery($sql, "consultar Logs", false);
    }

    public function getVentasAbast()
    {
        $sql = "SELECT count(v.id) AS total
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
        WHERE v.estado='3' ";
        return  $this->consultarQuery($sql, "consultar Ventas Abastecidas.", false);
    }

    public function getSolicitudesTeseo(){
        $sql = "SELECT COUNT(r.id) AS total
        FROM edicionesteseo r 
        WHERE  r.estado='1'";
        return  $this->consultarQuery($sql, "consultar Lotes por Capturar", false);
    }
}
