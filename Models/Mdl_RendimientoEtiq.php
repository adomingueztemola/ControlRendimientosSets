<?php
class RendimientoEtiqueta extends ConexionBD
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
    public function busquedaLoteEtiq($lote)
    {

        $sql = "SELECT r.* FROM rendimientosetiquetas r WHERE  loteTemola='$lote' AND estado>'0'";
        return  $this->ejecutarQuery($sql, "consultar Lote", true);
    }
    public function getPreRendimientoAbierto()
    {
        $idUserReg = $this->idUserReg;
        $sql = "SELECT r.* FROM rendimientosetiquetas r WHERE r.idUserReg='$idUserReg' AND r.estado='1'";
        return  $this->ejecutarQuery($sql, "consultar Rendimiento Pendiente por el Usuario", true);
    }

    public function initRendimiento($fechaEngrase, $lote, $programa, $materiaPrima, $multimateria)
    {
        $idUserReg = $this->idUserReg;
        $sql = "INSERT INTO rendimientosetiquetas (fechaEngrase,semanaProduccion,fechaFinal, 
        loteTemola, idCatPrograma, idCatMateriaPrima, estado, idUserReg, fechaReg,  multiMateria) 
        VALUES ('{$fechaEngrase}','0','','{$lote}','{$programa}',
        '{$materiaPrima}','1','{$idUserReg}',NOW(), '$multimateria')";
        return $this->ejecutarQuery($sql, "registrar Inicio de Rendimiento");
    }
    public function getLotesPreRegistradosEtiq()
    {
        $sql = "SELECT re.*, cm.nombre AS nMateria, cp.nombre AS nPrograma,
                CONCAT(su.nombre, ' ', su.apellidos) AS n_userRegistro,
                DATE_FORMAT(re.fechaReg, '%d/%m/%Y') AS f_fechaReg
        FROM rendimientosetiquetas re
        INNER JOIN catmateriasprimas cm ON re.idCatMateriaPrima=cm.id
        INNER JOIN catprogramas cp ON re.idCatPrograma=cp.id
        INNER JOIN segusuarios su ON re.idUserReg=su.id
        WHERE re.estado='1'";
        return  $this->consultarQuery($sql, "consultar Lote de Etiquetas.");
    }
}
