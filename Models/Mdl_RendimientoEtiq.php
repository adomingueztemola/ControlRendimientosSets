<?php
class RendimientoEtiq extends ConexionBD
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
    public function getEditXUser()
    {
        $idUserReg = $this->idUserReg;
        $sql = "SELECT r.*, cp.nombre AS nPrograma, 
        cm.nombre AS nMateriaPrima, pr.nombre AS nProveedor
        FROM rendimientosetiquetas r 
        INNER JOIN catprogramas cp ON r.idCatPrograma=cp.id
        INNER JOIN catmateriasprimas cm ON r.idCatMateriaPrima=cm.id
        INNER JOIN catproveedores pr ON r.idCatProveedor=pr.id
        WHERE r.idUserReg='$idUserReg' AND r.estado='1'";
        return  $this->consultarQuery($sql, "consultar Rendimiento Pendiente por el Usuario",false);
    }

    public function registerNewLot(
        $fechaFinal,
        $semanaProduccion,
        $yearWeek,
        $lote,
        $programa,
        $materiaPrima,
        $_1s,
        $_2s,
        $_3s,
        $_4s,
        $total_s,
        $proveedor
    ) {
        $idUserReg = $this->idUserReg;
        $Data= $this->getProgramType($programa);
        $Data= Excepciones::validaConsulta($Data);
        if(count($Data)<=0){
            $this->errorBD("No se encuentra tipo de venta del lote, notifica a sistemas.", 0);
        }
        else{
            $venta=$Data['tipoVenta'];
        }
        $sql = "INSERT INTO rendimientosetiquetas (semanaProduccion,yearWeek,fechaFinal,
        loteTemola, idCatPrograma, idCatMateriaPrima, estado, idUserReg, fechaReg, 1s, 2s, 3s,4s, total_s, idCatProveedor, idTipoVenta) 
        VALUES ('{$semanaProduccion}','{$yearWeek}','{$fechaFinal}','{$lote}','{$programa}',
        '{$materiaPrima}','1','{$idUserReg}',NOW(), '$_1s', '$_2s', '$_3s', '$_4s', '$total_s', '$proveedor', '$venta')";
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

    private function getProgramType($id){
        $sql = "SELECT *,
        CASE
            WHEN tipo = '2' THEN '2'
            WHEN tipo = '4' THEN '1'
        END AS tipoVenta
         FROM catprogramas WHERE id='$id'";
        return  $this->consultarQuery($sql, "consultar Tipo de Programa de Venta",false);
    }
}
