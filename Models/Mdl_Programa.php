<?php
class Programa extends ConexionBD
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
    public function getProgramasSetsSelect2($busqId = '')
    {
        $filtradoID = $busqId == '' ? '1=1' : "ca.nombre LIKE '%$busqId%'";

        $sql = "SELECT ca.*
        FROM catprogramas ca
        WHERE $filtradoID AND 
        ca.estado ='1' AND ca.tipo='1'";
        return  $this->consultarQuery($sql, "consultar Programas");
    }
    public function agregarPrograma($name, $areaNeta, $tipo)
    {
        $debug = $this->debug;
        $link = $this->link;
        $idUserReg = $this->idUserReg;

        $sql = "INSERT INTO catprogramas (nombre,areaNeta,tipo, estado, idUserReg, fechaReg) 
        VALUES ('{$name}','{$areaNeta}','$tipo','1','{$idUserReg}',NOW())";
        return $this->ejecutarQuery($sql, "registrar Programa");
    }
    public function editarPrograma($id, $areaNeta)
    {

        $idUserReg = $this->idUserReg;
        $sql = "UPDATE catprogramas SET areaNeta='$areaNeta',idUserReg='$idUserReg', fechaReg=NOW()
          WHERE id='$id'";
        return $this->ejecutarQuery($sql, "editar Programa");
    }
    public function getPrograma($filtradoEstatus = '1=1', $filtradoTipo = '1=1')
    {
        $debug = $this->debug;
        $link = $this->link;
        $ArrayDatos = array();
        $sql = "SELECT p.*, DATE_FORMAT(p.fechaReg,'%d/%m/%Y %H:%i') AS f_fechaReg, 
        CONCAT(u.nombre,' ',u.apellidos) AS str_usuario
        FROM catprogramas p
        INNER JOIN segusuarios u ON p.idUserReg=u.id
        WHERE $filtradoEstatus AND $filtradoTipo
        ORDER BY p.nombre";
        return  $this->ejecutarQuery($sql, "consultar Programas", true);
    }
}
