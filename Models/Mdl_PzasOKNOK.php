<?php
class PzasOKNOK extends ConexionBD
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
    /**
     * CARGA DE LOTES PARA CLASIFICACION
     * ESTOS METODOS SON USADOS PARA CLASIFICAR LAS PIEZAS
     **/
    public function getLotesXClasificar($term)
    {
        $filtradoBusq = $term == '' ? '1=1' : "r.loteTemola LIKE '%$term%'";
        $sql = "SELECT r.id AS value, r.loteTemola AS 'label', cp.nombre AS programa,
        DATE_FORMAT(r.fechaEngrase, '%d/%m/%Y') AS f_fechaEngrase,
        r.total_s AS cueros, IFNULL(r._12OK,0) AS _12OK, IFNULL(r._3OK,0) AS _3OK, 
        IFNULL(r._6OK,0) AS _6OK, IFNULL(r._9OK,0) AS _9OK, 
        IFNULL(r._12NOK,0) AS _12NOK, IFNULL(r._3NOK,0) AS _3NOK, 
        IFNULL(r._6NOK,0) AS _6NOK, IFNULL(r._9NOK,0) AS _9NOK, 
        IFNULL(r.pzasOk,0) AS pzasOk, IFNULL(r.pzasNok,0) AS pzasNok,
        IFNULL(r.pzasCortadasTeseo,0) AS pzasCortadasTeseo,
        IFNULL(r._12Teseo,0) AS _12Teseo, IFNULL(r._3Teseo,0) AS _3Teseo, 
        IFNULL(r._6Teseo,0) AS _6Teseo, IFNULL(r._9Teseo,0) AS _9Teseo
        FROM rendimientos r
        INNER JOIN catprogramas cp ON r.idCatPrograma=cp.id 
        WHERE r.regTeseo='1' AND (r.regOkNok!='1' 
        OR r.regOkNok IS NULL) AND r.tipoProceso='1' AND r.estado='2'
        AND $filtradoBusq ";
        return  $this->consultarQuery($sql, "consultar Lotes", true);
    }
    public function getLotesBusqClasificados($busqId = '')
    {
        $filtradoID = $busqId == '' ? '1=1' : "r.loteTemola LIKE '%$busqId%'";
        $sql = "SELECT r.id,  r.loteTemola, cp.nombre AS programa,
        DATE_FORMAT(r.fechaEngrase, '%d/%m/%Y') AS f_fechaEngrase,
        r.total_s AS cueros, IFNULL(r._12OK,0) AS _12OK, IFNULL(r._3OK,0) AS _3OK, 
        IFNULL(r._6OK,0) AS _6OK, IFNULL(r._9OK,0) AS _9OK, 
        IFNULL(r._12NOK,0) AS _12NOK, IFNULL(r._3NOK,0) AS _3NOK, 
        IFNULL(r._6NOK,0) AS _6NOK, IFNULL(r._9NOK,0) AS _9NOK, 
        IFNULL(r.pzasOk,0) AS pzasOk, IFNULL(r.pzasNok,0) AS pzasNok,
        IFNULL(r.pzasCortadasTeseo,0) AS pzasCortadasTeseo
        FROM rendimientos r
        INNER JOIN catprogramas cp ON r.idCatPrograma=cp.id 
        WHERE r.regTeseo='1' AND r.regOkNok='1' AND $filtradoID
        AND r.tipoProceso='1'";
        return  $this->consultarQuery($sql, "consultar Lotes Clasificados");
    }

    public function getLotesClasificados($busqId = '')
    {
        $filtradoID = $busqId == '' ? '1=1' : "r.id='$busqId'";
        $sql = "SELECT r.id,  r.loteTemola, cp.nombre AS programa,
        DATE_FORMAT(r.fechaEngrase, '%d/%m/%Y') AS f_fechaEngrase,
        r.total_s AS cueros, IFNULL(r._12OK,0) AS _12OK, IFNULL(r._3OK,0) AS _3OK, 
        IFNULL(r._6OK,0) AS _6OK, IFNULL(r._9OK,0) AS _9OK, 
        IFNULL(r._12NOK,0) AS _12NOK, IFNULL(r._3NOK,0) AS _3NOK, 
        IFNULL(r._6NOK,0) AS _6NOK, IFNULL(r._9NOK,0) AS _9NOK, 
        IFNULL(r.pzasOk,0) AS pzasOk, IFNULL(r.pzasNok,0) AS pzasNok,
        IFNULL(r.pzasCortadasTeseo,0) AS pzasCortadasTeseo
        FROM rendimientos r
        INNER JOIN catprogramas cp ON r.idCatPrograma=cp.id 
        WHERE r.regTeseo='1' AND r.regOkNok='1' AND $filtradoID
        AND r.tipoProceso='1'";
        return  $this->consultarQuery($sql, "consultar Lotes Clasificados");
    }

    public function actualizaEstatusClasif($id)
    {
        $sql = "UPDATE rendimientos  r
        INNER JOIN config_inventarios conf ON conf.estado = '1'
        SET 
        r.regOkNok='1', 
        r.setsRechazados= IFNULL(r.pzasNok,0)/conf.pzasEnSets,
        r.pzasSetsRechazadas= IFNULL(r.pzasNok,0),
        r.porcSetsRechazoInicial= ((IFNULL(r.pzasNok,0)/conf.pzasEnSets)/r.setsCortadosTeseo)*100
        WHERE r.id='$id'";
        return  $this->runQuery($sql, "finalización de conteo");
    }
    public function registrarCantidad($campo, $value, $id, $pzasNok, $pzasOk, $activaCampoAct = '0')
    {
        $campoAct = $activaCampoAct == '1' ? $campo . 'Act="' . $value . '",' : "";
        $sql = "UPDATE rendimientos SET 
        $campo='$value',  $campoAct 
        pzasNok= '$pzasNok', pzasOk=pzasCortadasTeseo-'$pzasNok'
        WHERE id='$id'";
        return  $this->runQuery($sql, "registro de cantidad de piezas");
    }

    public function paseNOKScrap($id)
    {
        $idUserReg = $this->idUserReg;
        $sql = "INSERT INTO inventariorechazado (idRendimiento, pzasTotales, fechaReg, idUserReg, _12, _6,_3,_9)
        SELECT id, pzasNok, NOW(),'$idUserReg', _12NOK, _3NOK, _6NOK,  _9NOK 
        FROM rendimientos r WHERE id='$id'";
        return  $this->runQuery($sql, "registro de piezas a Scrap");
    }

    public function actualizaPzasOk( $campo, $campoOK, $value, $id){
        $nCampoOk= $campoOK.'OK';
        $nCampoTeseo= $campoOK.'Teseo';
        $nCampoNOk= $campoOK.'NOK';
        $nCampoOkAct= $campoOK.'OKAct';

        $sql = "UPDATE rendimientos SET 
        $nCampoOk= $nCampoTeseo - $nCampoNOk,
        $nCampoOkAct= $nCampoTeseo - $nCampoNOk

        WHERE id='$id'";
        return  $this->runQuery($sql, "registro de cantidad de piezas OK");
    }
}
