<?php
class PruebaHide extends ConexionBD
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

    public function agregarPruebaHide($lote, $fecha, $hides){
        $convertCuerosHides=$hides/2;
        $sql="INSERT INTO pruebashides (idLote, hides, _1s, _2s, _3s, _4s, _20, total_s, idUserReg, fechaReg)
        
        
        ";
        return $this->runQuery($sql, "detalle de Prueba");

    }
}
?>