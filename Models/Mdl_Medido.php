<?php
class Medido extends ConexionBD
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

    public function agregarLoteMedido($loteTemola, $idCatPrograma, $areaTotalDM, $areaTotalFT, $areaTotalRd, $ladosTotales)
    {
        $idUserReg = $this->idUserReg;
        $sql = "INSERT INTO lotesmediciones (idCatPrograma, loteTemola, areaTotalDM, areaTotalFT, areaTotalRd, ladosTotales, idUserReg, fechaReg) 
              VALUES ('$idCatPrograma', '$loteTemola', '$areaTotalDM', '$areaTotalFT', '$areaTotalRd', '$ladosTotales', '$idUserReg', NOW())";
        return $this->runQuery($sql, "agregar lote de medicion", 1);
    }
    public function agregarDetalladoLote($query)
    {
        $sql = "INSERT INTO ladosmediciones (idLoteMedicion, numSerie, areaDM, areaFT,areaRedondFT, idCatSeleccion) 
        VALUES $query";
        return $this->runQuery($sql, "agregar lote de medicion", 1);
    }

    public function eliminarLoteMedido($id)
    {
        $sql = " DELETE l, ld, paq, dpaq
        FROM lotesmediciones l
        LEFT JOIN ladosmediciones ld ON l.id=ld.idLoteMedicion
        LEFT JOIN paqueteslados paq ON paq.idLoteMedido=l.id
        WHERE l.id='$id'";
        return $this->runQuery($sql, "eliminación lote de medicion");
    }
    public function getReporteMedicion($filtradoFecha = '1=1',  $filtradoPrograma = '1=1')
    {
        $sql = "SELECT l.*, cp.nombre AS nPrograma,
        CONCAT(su.nombre, ' ', su.apellidos) AS nUsuario,
        DATE_FORMAT(l.fechaReg,'%d/%m/%Y') AS f_fechaReg
        FROM lotesmediciones l
        INNER JOIN catprogramas cp ON l.idCatPrograma=cp.id
        LEFT JOIN segusuarios su ON l.idUserReg=su.id
        WHERE $filtradoFecha AND $filtradoPrograma
        ORDER BY CAST(l.loteTemola AS unsigned) DESC ";
        return  $this->consultarQuery($sql, "consultar Reporte de Medición");
    }
    public function getLadosDisp($id)
    {
        $sql = "SELECT lm.* FROM ladosmediciones lm
        WHERE lm.idLoteMedicion='$id' AND (lm.idPaquete IS NULL OR lm.idPaquete=0 OR lm.idPaquete='')";
        return  $this->consultarQuery($sql, "consultar Detalle de Reporte de Medición");
    }
    public function getDetReporteMedicion($id)
    {
        $sql = "SELECT * FROM ladosmediciones lm
        WHERE lm.idLoteMedicion='$id'";
        return  $this->consultarQuery($sql, "consultar Detalle de Reporte de Medición");
    }
    public function getLotesSelect2($busqId = '')
    {
        $filtradoID = $busqId == '' ? '1=1' : "lm.loteTemola LIKE '%$busqId%'";

        $sql = "SELECT DISTINCT lm.*, cp.nombre AS nPrograma 
        FROM ladosmediciones ld
        INNER JOIN lotesmediciones lm ON ld.idLoteMedicion=lm.id
        INNER JOIN catprogramas cp ON lm.idCatPrograma=cp.id
        LEFT JOIN detpaqueteslados dp ON ld.id=dp.idLadoMedicion
        WHERE dp.idLadoMedicion IS NULL
        AND $filtradoID";
        return  $this->consultarQuery($sql, "consultar lotes");
    }

    public function getSelecciones()
    {
        $sql = "SELECT *
        FROM catselecciones
        WHERE estado='1'";
        return  $this->consultarQuery($sql, "consultar selecciones");
    }

    public function cambiarSeleccionLado($id, $seleccion)
    {
        $sql = "UPDATE ladosmediciones
        SET idCatSeleccion='$seleccion'
        WHERE id='$id'";
        return $this->runQuery($sql, "cambio de selección de lado");
    }

    public function getDetalleLados($ids = [])
    {
        $str_ids = implode(',', $ids);
        $filtradosIds = $ids == "" ? "1=1" : "id IN($str_ids)";
        $sql = "SELECT *
        FROM ladosmediciones
        WHERE  $filtradosIds";
        return  $this->consultarQuery($sql, "consultar Detalles Lados");
    }

    public function getNumPaquete($id)
    {
        $sql = "SELECT IFNULL(count(p.id),0)+1 AS numPaquete FROM paqueteslados p
        WHERE p.idLoteMedido='$id'";
        return  $this->consultarQuery($sql, "consultar Num de Paquete", false);
    }
    public function agregarPaquete(
        $id,
        $numPaquete,
        $areaTotalDM,
        $areaTotalFT,
        $areaTotalRd,
        $totalLados
    ) {
        $idUserReg = $this->idUserReg;
        $sql = "INSERT INTO paqueteslados (idLoteMedido, numPaquete, areaTotalDM,
        areaTotalFT, areaTotalRd, fechaReg, idUserReg, totalLados)
        VALUES('$id', '$numPaquete', '$areaTotalDM', '$areaTotalFT', '$areaTotalRd',NOW(), '$idUserReg',
        '$totalLados')";
        return $this->runQuery($sql, "agregar de Paquete", 1);
    }
    public function agregarDetPaquete($id, $idPaquete, $numLado)
    {
        $sql = "UPDATE ladosmediciones 
        SET idPaquete= '$idPaquete', numLado='$numLado'
        WHERE id='$id'";
        return $this->runQuery($sql, "agregar Lado al Paquete");
    }

    public function getPaquetesXLote($id){
        $sql = "SELECT p.*, lm.loteTemola, cp.nombre AS nPrograma
        FROM paqueteslados p
        INNER JOIN lotesmediciones lm ON p.idLoteMedido=lm.id
        INNER JOIN catprogramas cp ON lm.idCatPrograma=cp.id
        WHERE p.idLoteMedido='$id'
        ORDER BY CAST(numPaquete AS unsigned) ";
        return  $this->consultarQuery($sql, "consultar Paquetes por Lote");
    }

    public function getDetPaquete($id){
        $sql = "SELECT lm.*, cs.nombre AS nSeleccion 
        FROM ladosmediciones lm
        INNER JOIN catselecciones cs ON lm.idCatSeleccion=cs.id
        WHERE lm.idPaquete='$id'";
        return  $this->consultarQuery($sql, "consultar Detallado de Paquete");
    }

    public function eliminarLadosPaq($id){
        $sql = "UPDATE ladosmediciones 
        SET idPaquete= '0', numLado='0'
        WHERE idPaquete='$id'";
        return $this->runQuery($sql, "eliminar Lados del Paquete");
    }
    public function eliminarPaquete($id){
        $sql = "DELETE FROM
                paqueteslados
                WHERE id='$id'";
        return $this->runQuery($sql, "eliminar del Paquete");
    }

    public function reconteoPaquetes($id){
        $sql="CALL reconteoPaquetes('$id')";
        return $this->runQuery($sql, "reconteo del Paquete");

    }

    public function getLadosConPaquete($idLote){
        $sql="SELECT * FROM ladosmediciones lm
        WHERE (lm.idPaquete<>'' AND lm.idPaquete IS NOT NULL)
        AND lm.idLoteMedicion='$idLote'
        ORDER BY lm.idPaquete, CAST(lm.numLado AS unsigned)";
        return  $this->consultarQuery($sql, "consultar Lados Con Paquete");

    }
}
