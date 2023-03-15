<?php
class Traspaso extends ConexionBD
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
   
    public function getTraspasosLote($idRendimiento){
        $sql="SELECT tr.id, r.loteTemola AS loteEntrada, tr.cantidad
        FROM traspasos tr
        INNER JOIN rendimientos r ON r.id= tr.idRendEntrada
        WHERE tr.estado='1'";
        return $this->ejecutarQuery($sql, "consultar Traspasos", true);

    }

    public function agregarTraspaso($idRendEntrada, $idRendSalida, $cantidad){
        $idUserReg = $this->idUserReg;
        $sql="INSERT INTO traspasos (idRendEntrada, idRendSalida, cantidad, idUserReg, fechaReg, estado)
              VALUES ('$idRendEntrada', '$idRendSalida', '$cantidad', '$idUserReg', NOW(), '1')";
        return $this->ejecutarQuery($sql, "registrar Traspaso");

    }

    public function eliminarTraspaso($id){
        $sql="DELETE FROM traspasos WHERE id='$id'";
        return $this->ejecutarQuery($sql, "eliminar Traspaso");
    }
    
}
