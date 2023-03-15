<?php
class PzasVolante extends ConexionBD
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

    public function getPzasVolante($filtradoPrograma="1=1")
    {
        $sql = "SELECT apv.idCatPzaVolt, apv.idCatPrograma, apv.id, cpv.nombre AS nPzaVolante, apv.area FROM catpzasvolante cpv
        INNER JOIN areaxpzasvolt apv ON cpv.id= apv.idCatPzaVolt AND apv.estado='1'
        INNER JOIN catprogramas cp ON apv.idCatPrograma=cp.id
        WHERE cpv.estado='1' AND $filtradoPrograma
        ORDER BY cpv.orden";
        return  $this->ejecutarQuery($sql, "consultar Piezas del Volante", true);

    }


   
}
