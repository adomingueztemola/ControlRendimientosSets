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
        $idUserReg= $this->idUserReg;
        $sql = "INSERT INTO lotesmediciones (idCatPrograma, loteTemola, areaTotalDM, areaTotalFT, areaTotalRd, ladosTotales, idUserReg, fechaReg) 
              VALUES ('$idCatPrograma', '$loteTemola', '$areaTotalDM', '$areaTotalFT', '$areaTotalRd', '$ladosTotales', '$idUserReg', NOW())";
        return $this->runQuery($sql, "agregar lote de medicion",1);
    }
    public function agregarDetalladoLote($query)
    {
        $sql = "INSERT INTO ladosmediciones (idLoteMedicion, numSerie, areaDM, areaFT,areaRedondFT, idCatSeleccion) 
        VALUES $query";
        return $this->runQuery($sql, "agregar lote de medicion", 1);
    }

    public function eliminarLoteMedido($id){
        $sql = " DELETE l, ld, paq, dpaq
        FROM lotesmediciones l
        LEFT JOIN ladosmediciones ld ON l.id=ld.idLoteMedicion
        LEFT JOIN detpaqueteslados dpaq ON ld.id=dpaq.idLadoMedicion
        LEFT JOIN paqueteslados paq ON dpaq.idPaqueteLado=paq.id
        WHERE l.id='$id'";
        return $this->runQuery($sql, "eliminación lote de medicion");
    }
    public function getReporteMedicion($filtradoFecha='1=1',  $filtradoPrograma='1=1'){
        $sql="SELECT l.*, cp.nombre AS nPrograma,
        CONCAT(su.nombre, ' ', su.apellidos) AS nUsuario,
        DATE_FORMAT(l.fechaReg,'%d/%m/%Y') AS f_fechaReg
        FROM lotesmediciones l
        INNER JOIN catprogramas cp ON l.idCatPrograma=cp.id
        LEFT JOIN segusuarios su ON l.idUserReg=su.id
        WHERE $filtradoFecha AND $filtradoPrograma";
        return  $this->consultarQuery($sql, "consultar Reporte de Medición");
    }
    public function getDetReporteMedicion($id){
        $sql="SELECT * FROM ladosmediciones lm
        WHERE lm.idLoteMedicion='$id'";
        return  $this->consultarQuery($sql, "consultar Detalle de Reporte de Medición");
    }
}
