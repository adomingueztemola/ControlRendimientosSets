<?php
class Grosor extends ConexionBD
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

    public function getGrosor($filtradoEstatus = '1=1'){
        $sql="SELECT cg.*, CONCAT(su.nombre, ' ', su.apellidos) AS str_usuario,
        DATE_FORMAT(cg.fechaReg,'%d/%m/%Y %H:%i') AS f_fechaReg
        FROM catgrosores cg
        INNER JOIN segusuarios su ON su.id=cg.idUserReg
        WHERE $filtradoEstatus
        ORDER BY cg.nombre";
        return  $this->consultarQuery($sql, "consultar Grosores");
    }

    public function agregarGrosor($grosor){
        $idUserReg = $this->idUserReg;
        $sql = "INSERT INTO catgrosores (nombre,estado, idUserReg, fechaReg) 
        VALUES ('{$grosor}','1','{$idUserReg}',NOW())";
        return $this->ejecutarQuery($sql, "registrar Grosor");
    }
}
?>