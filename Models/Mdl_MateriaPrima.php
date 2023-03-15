<?php
class MateriaPrima extends ConexionBD
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
    public function agregarMateria($name, $tipo, $mnd)
    {

        $idUserReg = $this->idUserReg;

        $sql = "INSERT INTO catmateriasprimas (nombre,tipo, estado, idUserReg, fechaReg, mnd) 
        VALUES ('{$name}','$tipo','1','{$idUserReg}',NOW(), '$mnd')";
        return $this->ejecutarQuery($sql, "registrar Materia Prima");
    }
    public function editarMateria($id, $tipo, $mnd)
    {
        $idUserReg = $this->idUserReg;

        $sql = "UPDATE catmateriasprimas SET tipo='$tipo', idUserReg='$idUserReg', fechaReg=NOW(), mnd='$mnd'
                 WHERE id='$id'";
        return $this->ejecutarQuery($sql, "editar Materia Prima");
    }
    public function getMaterias($filtradoEstatus = '1=1')
    {
        $debug = $this->debug;
        $link = $this->link;
        $ArrayDatos = array();
        $sql = "SELECT mt.*, DATE_FORMAT(mt.fechaReg,'%d/%m/%Y %H:%i') AS f_fechaReg, 
        CONCAT(u.nombre,' ',u.apellidos) AS str_usuario
        FROM catmateriasprimas mt
        INNER JOIN segusuarios u ON mt.idUserReg=u.id
        WHERE $filtradoEstatus
        ORDER BY mt.nombre";
        return  $this->ejecutarQuery($sql, "consultar Materia Prima", true);
    }
}
