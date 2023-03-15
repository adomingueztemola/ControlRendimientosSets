<?php
class ProcesoProduccion extends ConexionBD
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
    public function agregarProceso($name)
    {
        $debug = $this->debug;
        $link = $this->link;
        $idUserReg = $this->idUserReg;

        $sql = "INSERT INTO catprocesosproduccion (nombre,estado, idUserReg, fechaReg) 
        VALUES ('{$name}','1','{$idUserReg}',NOW())";
        return $this->ejecutarQuery($sql, "registrar Materia Prima");

    }
    public function getProcesos($filtradoEstatus = '1=1')
    {
        $debug = $this->debug;
        $link = $this->link;
        $ArrayDatos = array();
        $sql = "SELECT p.*, DATE_FORMAT(p.fechaReg,'%d/%m/%Y %H:%i') AS f_fechaReg, 
        CONCAT(u.noEmpleado,'-',u.nombre,' ',u.apellidos) AS str_usuario
        FROM catprocesosproduccion p
        INNER JOIN segusuarios u ON p.idUserReg=u.id
        WHERE $filtradoEstatus
        ORDER BY p.nombre";
       return  $this->ejecutarQuery($sql, "consultar Materia Prima", true);
      
       
    }
    public function getRendimientoAreaXProceso($filtradoProceso='1=1'){
        $sql = "SELECT DISTINCT AVG(r.costoWBUnit) AS promCostoWBUnit, AVG(r.areaWBUnidad)  AS promAreaWBUnit
       FROM rendimientos r
       INNER JOIN detpedidos dp ON dp.idRendimiento=r.id
       INNER JOIN pedidos p ON dp.idPedido=p.id
       INNER JOIN catprocesos cp ON p.idCatProcesos=cp.id
       WHERE $filtradoProceso AND  r.estado='4' AND r.tipoProceso='1' LIMIT 15";
       //p.idCatProceso='7'
       return  $this->consultarQuery($sql, "consultar Rendimiento de Área por Proceso", false);
    }
}
