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

    public function agregarLoteMedido($loteTemola, $idCatPrograma, $areaTotalDM, $areaTotalFT, $areaTotalRd, $ladosTotales, $grosor)
    {
        $idUserReg = $this->idUserReg;
        $sql = "INSERT INTO lotesmediciones (idCatPrograma, loteTemola, areaTotalDM, areaTotalFT, areaTotalRd, ladosTotales, idUserReg, fechaReg, idCatGrosor) 
              VALUES ('$idCatPrograma', '$loteTemola', '$areaTotalDM', '$areaTotalFT', '$areaTotalRd', '$ladosTotales', '$idUserReg', NOW(), '$grosor')";
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
        $sql = " DELETE l, ld, paq
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
        DATE_FORMAT(l.fechaReg,'%d/%m/%Y') AS f_fechaReg,
        cg.nombre AS nGrosor
        FROM lotesmediciones l
        INNER JOIN catprogramas cp ON l.idCatPrograma=cp.id
        LEFT JOIN catgrosores cg ON l.idCatGrosor=cg.id
        LEFT JOIN segusuarios su ON l.idUserReg=su.id
        WHERE $filtradoFecha AND $filtradoPrograma
        ORDER BY CAST(l.loteTemola AS unsigned) DESC ";
        return  $this->consultarQuery($sql, "consultar Reporte de Medición");
    }
    public function getLadosDisp($id)
    {
        $sql = "SELECT lm.* FROM ladosmediciones lm
        WHERE lm.idLoteMedicion='$id' 
        AND (lm.idPaquete IS NULL OR lm.idPaquete=0 OR lm.idPaquete='')
        ORDER BY CAST(lm.numSerie AS unsigned) DESC";
        return  $this->consultarQuery($sql, "consultar Detalle de Reporte de Medición");
    }
    public function getDetReporteMedicion($id)
    {
        $sql = "SELECT * FROM ladosmediciones lm
        WHERE lm.idLoteMedicion='$id'";
        return  $this->consultarQuery($sql, "consultar Detalle de Reporte de Medición");
    }
    
    public function getGrosorSelect2($busqId = ''){
        $filtradoID = $busqId == '' ? '1=1' : "nombre LIKE '%$busqId%'";

        $sql = "SELECT  *, nombre AS 'text' FROM catgrosores
        WHERE estado='1'
        AND $filtradoID";
        return  $this->consultarQuery($sql, "consultar grosores");
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
        $sql = "SELECT 
         IF(lm.id IS NULL,0, 1) AS abierto,
        IF(lm.id IS NOT NULL,lm.numPaqDlt, 
        IFNULL(count(p.id),0)+1) AS numPaquete
        FROM paqueteslados p
        LEFT JOIN lotesmediciones lm ON p.idLoteMedido=lm.id 
        AND lm.paqDelete='1' 
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
        $sql = "SELECT p.*, lm.loteTemola, CONCAT(cp.nombre, ' ',IFNULL(cg.nombre, '')) AS nPrograma
        FROM paqueteslados p
        INNER JOIN lotesmediciones lm ON p.idLoteMedido=lm.id
        INNER JOIN catprogramas cp ON lm.idCatPrograma=cp.id
        LEFT JOIN catgrosores cg ON lm.idCatGrosor=cg.id

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
        $sql="SELECT lm.*,  cs.nombre AS nSeleccion  FROM ladosmediciones lm
        INNER JOIN catselecciones cs ON lm.idCatSeleccion=cs.id
        WHERE (lm.idPaquete<>'' AND lm.idPaquete IS NOT NULL)
        AND lm.idLoteMedicion='$idLote'
        ORDER BY lm.idPaquete, CAST(lm.numLado AS unsigned)";
        return  $this->consultarQuery($sql, "consultar Lados Con Paquete");
    }
    public function getNumPaquetesXLote($idLote, $paq){
        $sql = "SELECT p.*, lm.loteTemola, CONCAT(cp.nombre, ' ',cg.nombre) AS nPrograma
        FROM paqueteslados p
        INNER JOIN lotesmediciones lm ON p.idLoteMedido=lm.id
        LEFT JOIN catgrosores cg ON lm.idCatGrosor=cg.id

        INNER JOIN catprogramas cp ON lm.idCatPrograma=cp.id
        WHERE p.idLoteMedido='$idLote' AND p.numPaquete='$paq'";
        return  $this->consultarQuery($sql, "consultar Paquetes por Lote & Número", false);
    }

    public function ingresarNumPaqDlt($id){
        $sql="UPDATE paqueteslados p
        INNER JOIN lotesmediciones lm ON p.idLoteMedido=lm.id
            SET lm.paqDelete='1', lm.numPaqDlt=p.numPaquete
         WHERE p.id='$id'";
        return $this->runQuery($sql, "ingresa paquete eliminado como pendiente");
    }

    public function eliminarNumPaqDlt($id){
        $sql="UPDATE lotesmediciones lm 
            SET lm.paqDelete='0', lm.numPaqDlt=''
         WHERE lm.id='$id'";
        return $this->runQuery($sql, "eliminar paquete como pendiente");
    }

    public function eliminarAllPaq($id){
        $sql="DELETE FROM 
        paqueteslados
        WHERE idLoteMedido='$id'";
        return $this->runQuery($sql, "eliminar paquetes del lote");
    }

    public function devolverLadosLotes($id){
        $sql="UPDATE paqueteslados p
        INNER JOIN ladosmediciones lm ON p.id=lm.idPaquete
            SET lm.idPaquete='', lm.numLado=''
         WHERE p.idLoteMedido='$id'";
        return $this->runQuery($sql, "eliminar lados de los paquetes del lote");
    }

    public function getDetLote($id){
        $sql = "SELECT lm.*
        FROM lotesmediciones lm
        WHERE lm.id='$id'";
        return  $this->consultarQuery($sql, "consultar Detalle del Lote", false);
    }

} 
