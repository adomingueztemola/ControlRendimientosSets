<?php
class Scrap extends ConexionBD
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

    public function getRendimientosDisponibles()
    {
        $sql = "SELECT * FROM rendimientos r 
        WHERE r.estado='4' AND (r.paseScrap='0' OR r.paseScrap IS NULL)";
        return  $this->consultarQuery($sql, "consultar Rendimientos Disponibles");
    }

    public function getStockRechDisponibles(){
        $sql = "SELECT i.* FROM rendimientos r 
        INNER JOIN inventariorechazado i ON i.idRendimiento=r.id
        WHERE r.estado='4' AND (r.paseScrap='0' OR r.paseScrap IS NULL)";
        return  $this->consultarQuery($sql, "consultar Stock de Rechazos de Rendimientos Disponibles");
    }
    public function agregarTarima($fechaSalida, $totalPzas, $_12, $_6, $_3, $_9){
        $idUserReg = $this->idUserReg;
        $sql = "INSERT INTO tarimasscrap (fechaSalida, totalPzas, _12, _6,_3, _9, idUserReg, fechaReg) 
                VALUES ('$fechaSalida', '$totalPzas','$_12', '$_6', '$_3','$_9', '$idUserReg', NOW() )";
        return $this->runQuery($sql, "agregar Tarima", true);
    }
    public function agregarLogReporte($idTarima, $idRendimiento, $_12, $_3, $_6, $_9, 
                                      $_12Scrap, $_3Scrap, $_6Scrap, $_9Scrap,$totalScrap, $total){
        $idUserReg = $this->idUserReg;
        $sql = "INSERT INTO dettarimas (idLote, _12, _3, _6, _9, total, _12Scrap, _6Scrap,_3Scrap, _9Scrap,
        totalScrap, _12Act, _6Act, _3Act, _9Act, totalAct, idTarima, fechaReg, idUserReg) 
        VALUES ('$idRendimiento','$_12','$_3','$_6','$_9','$total', '$_12Scrap','$_6Scrap','$_3Scrap','$_9Scrap', 
        '$totalScrap','0','0','0','0','0','$idTarima', NOW(), '$idUserReg')";
        return $this->runQuery($sql, "detalle de Tarima");
    }

    public function actualizarStkRech($idStk){
        $sql = "UPDATE inventariorechazado i 
        SET _12=0, _3=0, _6=0, _9=0, pzasTotales=0
        WHERE id='$idStk'";
        return $this->runQuery($sql, "actualizar Stock de Rechazo");
    }

    public function actualizarRendimiento($idRendimiento){
        $sql = "UPDATE rendimientos r
        SET paseScrap='1'
        WHERE id='$idRendimiento'";
        return $this->runQuery($sql, "actualizar Rendimiento");
    }

    public function getEtiquetaScrap($idTarima){
        $sql = "SELECT t.id, DATE_FORMAT(t.fechaSalida,'%d/%m/%Y') AS fFechaSalida,
        GROUP_CONCAT(DISTINCT cp.nombre) AS programas, LPAD(t.id,5,'0') AS folio,
        t.totalPzas, t._12, t._3, t._6, t._9,
        GROUP_CONCAT(DISTINCT LPAD(r.semanaProduccion,2,'0')) AS semanas,
        CONCAT(MIN(r.loteTemola), ' AL ', max(r.loteTemola)) AS lotes

        FROM tarimasscrap t
        INNER JOIN dettarimas dt ON t.id=dt.idTarima
        INNER JOIN rendimientos r ON dt.idLote=r.id
        INNER JOIN catprogramas cp ON r.idCatPrograma=cp.id
        WHERE t.id='$idTarima'
        GROUP BY dt.idTarima";
        return  $this->consultarQuery($sql, "consultar Datos de Etiqueta", false);
    }

    public function getTarimas($filtradoPrograma='1=1' , $filtradoFecha='1=1' ){
        $sql = "SELECT t.id, DATE_FORMAT(t.fechaSalida,'%d/%m/%Y') AS fFechaSalida,
        GROUP_CONCAT(DISTINCT cp.nombre) AS programas, LPAD(t.id,5,'0') AS folio,
        t.totalPzas, t._12, t._3, t._6, t._9,
        GROUP_CONCAT(DISTINCT LPAD(r.semanaProduccion,2,'0')) AS semanas,
        CONCAT(MIN(r.loteTemola), ' AL ', max(r.loteTemola)) AS lotes

        FROM tarimasscrap t
        INNER JOIN dettarimas dt ON t.id=dt.idTarima
        INNER JOIN rendimientos r ON dt.idLote=r.id
        INNER JOIN catprogramas cp ON r.idCatPrograma=cp.id
        WHERE $filtradoFecha 
        GROUP BY dt.idTarima
        HAVING $filtradoPrograma";
        return  $this->consultarQuery($sql, "consultar Datos de Tarimas");
    }

    public function getDetTarimas($ident){
        $sql = "SELECT dt.id, dt._12, dt._3, dt._6, dt._9, dt.total,
        r.semanaProduccion, cp.nombre AS nPrograma, r.loteTemola
        FROM dettarimas dt
        INNER JOIN rendimientos r ON dt.idLote=r.id
        INNER JOIN catprogramas cp ON r.idCatPrograma=cp.id
        WHERE dt.idTarima='$ident'";
        return  $this->consultarQuery($sql, "consultar Detalle de Tarimas");
    }
}
