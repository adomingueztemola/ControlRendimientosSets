<?php
class Trabajadores extends ConexionBD
{
    protected $debug;
    private $idUserReg;

   


    public function __construct($debug, $idUserReg)
    {
        ConexionBD::initConexion();
        $this->debug = $debug;
        $this->idUserReg = $idUserReg;
    }

    public function __destruct()
    {
       ConexionBD::close();
    }
    public function getTrabajadoresDispTWM(){
        $sql="SELECT v.noTrabajador, CONCAT(v.nombres, ' ', v.apPat, ' ', v.apMat) AS nombreCompleto,
        v.nArea, v.idCatArea
        FROM vw_personaltwm v
        LEFT JOIN autorizapersonalreacond a ON a.noTrabajador COLLATE utf8_unicode_ci= v.noTrabajador COLLATE utf8_unicode_ci
        WHERE (v.idCatArea='9' OR v.idCatArea='8' OR v.idCatArea='15') AND a.id IS NULL
        ORDER BY v.nArea, v.nombres";
        return $this->consultarQuery($sql, "Personal de Áreas de Inspección");
    }

    public function getTrabajadoresAutorizadosTWM(){
        $sql="SELECT a.id, a.estado, v.noTrabajador, CONCAT(v.nombres, ' ', v.apPat, ' ', v.apMat) AS nombreCompleto,
        v.nArea, v.idCatArea, CONCAT(su.nombre, ' ', su.apellidos) AS str_usuario,
        DATE_FORMAT(a.fechaReg, '%d/%m/%Y %H:%i') AS f_fechaReg
        FROM autorizapersonalreacond a
        INNER JOIN vw_personaltwm v  ON a.noTrabajador COLLATE utf8_unicode_ci= v.noTrabajador COLLATE utf8_unicode_ci
        INNER JOIN segusuarios su ON a.idUserReg=su.id       
        ORDER BY v.nArea, v.nombres";
        return $this->consultarQuery($sql, "Personal Autorizado");
    }

    public function agregarPersonal($personal){
        $idUserReg= $this->idUserReg;
        $sql="INSERT INTO autorizapersonalreacond(noTrabajador, estado, fechaReg, idUserReg) 
              VALUES ('$personal','1',NOW(),'$idUserReg')";
        return $this->runQuery($sql, "ingresar Personal Autorizado.");

    }
}
?>