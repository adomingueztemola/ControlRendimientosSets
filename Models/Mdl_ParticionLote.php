<?php
class ParticionLote extends Rendimiento
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

    public function getTransferenciaLotes($ident){
        $sql = "SELECT * FROM rendimientos r WHERE r.idRendimientoTransfer='$ident'";
        return  $this->consultarQuery($sql, "consultar Transferencias de Lote");
    }

    public function agregarParticion($idLote, $idPorgrama, $numParticion, $total_s, $_1s, $_2s, $_3s, $_4s, $_20){
        
    }
  
}
