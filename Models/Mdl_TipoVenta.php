<?php
class TipoVenta extends ConexionBD
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
    public function agregarTipoVenta($name, $tipo,  $cargaVenta)
    {
        $debug = $this->debug;
        $link = $this->link;
        $idUserReg = $this->idUserReg;

        $sql = "INSERT INTO cattiposventas (nombre, tipo,estado, idUserReg, fechaReg, cargaVenta) 
        VALUES ('{$name}','{$tipo}','1','{$idUserReg}',NOW(), '$cargaVenta')";
        return $this->ejecutarQuery($sql, "registrar Tipos de Ventas");

    }
    public function editarTipo($id, $tipo, $cargaVenta){
        $idUserReg = $this->idUserReg;
        $sql = "UPDATE cattiposventas SET tipo='$tipo',
        cargaVenta='$cargaVenta',
        idUserReg='$idUserReg', fechaReg=NOW()
          WHERE id='$id'";
        return $this->ejecutarQuery($sql, "editar Tipo");

    }
    public function getTipos($filtradoEstatus = '1=1', $filtradoTipo="1=1")
    {
        $debug = $this->debug;
        $link = $this->link;
        $ArrayDatos = array();
        $sql = "SELECT tv.*, DATE_FORMAT(tv.fechaReg,'%d/%m/%Y %H:%i') AS f_fechaReg, 
        CONCAT(u.noEmpleado,'-',u.nombre,' ',u.apellidos) AS str_usuario
        FROM cattiposventas tv
        INNER JOIN segusuarios u ON tv.idUserReg=u.id
        WHERE $filtradoEstatus AND $filtradoTipo
        ORDER BY tv.nombre";
       return  $this->ejecutarQuery($sql, "consultar Materia Prima", true);
      
       
    }

}
