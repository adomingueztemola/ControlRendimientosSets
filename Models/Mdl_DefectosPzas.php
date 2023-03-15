<?php
class DefectosPzas extends ConexionBD
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
    public function agregarDefectospzs($name)
    {
        $debug = $this->debug;
        $link = $this->link;
        $idUserReg = $this->idUserReg;

        $sql = "INSERT INTO catdefectosrecuperacion (nombre,estado, idUserReg, fechaReg) 
        VALUES ('{$name}','1','{$idUserReg}',NOW())";
        return $this->ejecutarQuery($sql, "registrar Materia Prima");

    }
    public function getDefectospzs($filtradoEstatus = '1=1')
    {
        $debug = $this->debug;
        $link = $this->link;
        $ArrayDatos = array();
        $sql = "SELECT p.*, DATE_FORMAT(p.fechaReg,'%d/%m/%Y %H:%i') AS f_fechaReg, 
        CONCAT(u.noEmpleado,'-',u.nombre,' ',u.apellidos) AS str_usuario
        FROM catdefectosrecuperacion p
        INNER JOIN segusuarios u ON p.idUserReg=u.id
        WHERE $filtradoEstatus
        ORDER BY p.nombre";
       return  $this->ejecutarQuery($sql, "consultar Defectospzs", true);
      
       
    }
    public function getRendimientoAreaXDefectopz($filtradoDefectopz='1=1'){
        $sql = "SELECT DISTINCT AVG(r.costoWBUnit) AS promCostoWBUnit, AVG(r.areaWBUnidad)  AS promAreaWBUnit
       FROM rendimientos r
       INNER JOIN detpedidos dp ON dp.idRendimiento=r.id
       INNER JOIN pedidos p ON dp.idPedido=p.id
       INNER JOIN catdefectosrecuperacion cp ON p.idCatDefectopz=cp.id
       WHERE $filtradoDefectopz AND  r.estado='4' AND r.tipoProceso='1' LIMIT 15";
       //p.idCatDefectopz='7'
       return  $this->consultarQuery($sql, "consultar Rendimiento de Defecto Pieza", false);
    }
}
