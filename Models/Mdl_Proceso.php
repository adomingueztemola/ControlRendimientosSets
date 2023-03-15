<?php
class ProcesoSecado extends ConexionBD
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
    public function agregarProceso($codigo, $name, $tipo)
    {
        $debug = $this->debug;
        $link = $this->link;
        $idUserReg = $this->idUserReg;

        $sql = "INSERT INTO catprocesos (codigo,nombre,estado, tipo, idUserReg, fechaReg) 
        VALUES ('{$codigo}','{$name}','1','{$tipo}','{$idUserReg}',NOW())";
        return $this->ejecutarQuery($sql, "registrar Proceso");
    }
    public function getProcesos($filtradoEstatus = '1=1', $filtradoTipo="1=1")
    {
        $debug = $this->debug;
        $link = $this->link;
        $ArrayDatos = array();
        $sql = "SELECT pr.*, DATE_FORMAT(pr.fechaReg,'%d/%m/%Y %H:%i') AS f_fechaReg, 
        CONCAT(u.nombre,' ', u.apellidos) AS str_usuario
        FROM catprocesos pr
        INNER JOIN segusuarios u ON pr.idUserReg=u.id
        WHERE $filtradoEstatus AND $filtradoTipo
        ORDER BY pr.codigo";
        
        return  $this->ejecutarQuery($sql, "consultar Procesos", true);


    }
    public function editarProceso($id, $tipo){
        $debug = $this->debug;
        $link = $this->link;
        $idUserReg = $this->idUserReg;
        $sql = "UPDATE catprocesos SET tipo='$tipo',idUserReg='$idUserReg', fechaReg=NOW()
          WHERE id='$id'";
        return $this->ejecutarQuery($sql, "editar Proceso");
    }
}
?>