<?php
class ReasignacionLotesFracc extends ConexionBD
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
    /******** INICIAR TRASPASO *******/
    public function initTraspasar(
        $idRendimiento,
        $totalPzas,
        $porcentaje,
        $_1Rea,
        $_2Rea,
        $_3Rea,
        $_4Rea,
        $_20Rea,
        $total_sRea
    ) {
        $idUserReg = $this->idUserReg;
        $sql = "INSERT INTO reasignacionfracclotes (idRendimiento, pzasTraspaso, estado, porcentaje,
        1s, 2s, 3s, 4s, _20, total_s, idUserReg, fechaReg) VALUES 
        ('$idRendimiento','$totalPzas','1','$porcentaje', '$_1Rea', '$_2Rea', '$_3Rea', '$_4Rea','$_20Rea', '$total_sRea',
        '$idUserReg', NOW())";
        return $this->runQuery($sql, "Iniciar Traspaso", true);
    }
    /******** ALMACENAR CALCULO DE VENTAS *******/
    public function agregarCalculoVentas($str_query)
    {

        $sql = "INSERT INTO reasignacuerosventas (idReAsignaLote,idVenta, estado, porcentaje,
        1s, 2s, 3s, 4s, _20, total_s, idUserReg, fechaReg) VALUES $str_query";
        return $this->runQuery($sql, "Agregar Calculo de Ventas");
    }
    /******** ALMACENAR CALCULO DE VENTAS *******/
    public function agregarCalculoPedidos($str_query)
    {

        $sql = "INSERT INTO reasignacuerospedidos (idReAsignaLote,idDetPedido, estado, porcentaje,
        1s, 2s, 3s, 4s, _20, total_s, idUserReg, fechaReg, areaProveedorLote) VALUES $str_query";
        return $this->runQuery($sql, "Agregar Calculo de Pedidos");
    }
    /******** ALMACENAR CALCULO DE VENTAS *******/
    public function agregarInfoNuevoLote(
        $idReAsignaLote,
        $loteTemolaTransfer,
        $_1sTransfer,
        $_2sTransfer,
        $_3sTransfer,
        $_4sTransfer,
        $_20Transfer,
        $total_sTransfer,
        $promAreaLoteProveedor,
        $promPrecioUnitFactUsd
    ) {
        $sql = "UPDATE reasignacionfracclotes SET loteTransfer='$loteTemolaTransfer', 1sTransfer='$_1sTransfer',
                2sTransfer='$_2sTransfer', 3sTransfer='$_3sTransfer', 4sTransfer='$_4sTransfer', _20Transfer='$_20Transfer',
                total_sTransfer='$total_sTransfer', areaProveedorLote='$promAreaLoteProveedor', precioUnitFactUsd='$promPrecioUnitFactUsd' WHERE id='$idReAsignaLote'";
        return $this->runQuery($sql, "Agregar Información del Nuevo Lote");
    }
    /******** INGRESAR LOTE NUEVO DE RENDIMIENTO *******/
    public function agregarNuevoRendimiento(
        $loteTemola,
        $idCatMateriaPrima,
        $tipoMateriaPrima,
        $fechaEngrase,
        $proceso,
        $tipoProceso,
        $programa,
        $areaNeta,
        $areaWBCalculada,
        $areaCrustCalculada,
        $areaTeseoCalculada,
        $humedad,
        $quiebre,
        $suavidad,
        $_1s,
        $_2s,
        $_3s,
        $_4s,
        $_20,
        $total_s,
        $idRendimiento,
        $perdidaAreaWBaCrustPorc,
        $recorteAcabadoPorc,
        $multiMateria

    ) {
        $idUserReg = $this->idUserReg;
        $sql = "INSERT INTO rendimientos (loteTemola,fechaEngrase,idCatMateriaPrima, tipoMateriaPrima, idCatProceso,
        tipoProceso, idCatPrograma, areaWB, areaCrust, areaFinal, humedad, quiebre, suavidad, 
        1s, 2s, 3s, 4s, _20, total_s, total_ant_s, estado, idUserReg, fechaReg, areaNeta_Prg, idRendimientoTransfer, perdidaAreaWBCrust, recorteAcabado, multiMateria) 
        VALUES ('$loteTemola','$fechaEngrase','$idCatMateriaPrima','$tipoMateriaPrima','$proceso','$tipoProceso','$programa','$areaWBCalculada',
        '$areaCrustCalculada', '$areaTeseoCalculada', '$humedad', '$quiebre', '$suavidad','$_1s', '$_2s', '$_3s','$_4s', '$_20', '$total_s', 
        '$total_s', '1', '$idUserReg', NOW(), '$areaNeta', '$idRendimiento', '$perdidaAreaWBaCrustPorc', '$recorteAcabadoPorc', '$multiMateria')";
        return $this->runQuery($sql, "Agregar Nuevo Lote", true);
    }
    /******** INGRESAR LOTE NUEVO DE RENDIMIENTO AL TRASPASO *******/
    public function agregarRendimientoTraspaso($idNuevoRendimiento, $idTraspaso)
    {
        $sql = "UPDATE reasignacionfracclotes SET idRendimientoTransfer='$idNuevoRendimiento' 
                 WHERE id='$idTraspaso'";
        return $this->runQuery($sql, "actualizar Nuevo Lote en la Reasignación", true);
    }
    /******** ACTUALIZA DATOS DE RENDIMIENTO SOBRE EL PEDIDO *******/
    public function registraPedidoLoteo($idRendimiento)
    {
        $sql = "UPDATE rendimientos  r 
        INNER JOIN (SELECT dt.idRendimiento, SUM(dt.total_s) AS total_s, SUM(dt.1s) AS 1s, SUM(dt.2s) AS 2s,
            SUM(dt.3s) AS 3s, SUM(dt.4s) AS 4s, SUM(dt._20) AS _20,SUM(dt.areaProveedorLote) AS areaProveedorLote, AVG(p.precioUnitFactUsd) AS precioUnitFactUsd
            FROM detpedidos dt 
            INNER JOIN pedidos p ON dt.idPedido=p.id
            WHERE dt.idRendimiento='$idRendimiento' AND dt.estado>='1'
            GROUP BY dt.idRendimiento) p ON p.idRendimiento = r.id
            SET  r.areaProveedorLote=p.areaProveedorLote, r.1s=p.1s,
                 r.diferenciaArea=IFNULL(areaWB- p.areaProveedorLote, 0),  r.promedioAreaWB=IFNULL(areaWB/p.total_s,0),
                 r.porcDifAreaWB= IFNULL(((areaWB- p.areaProveedorLote)/ p.areaProveedorLote)*100,0), r.estado='2',
                 r._20=p._20,r.4s=p.4s, r.areaPzasRechazo=(areaWB/p.total_s)*piezasRechazadas,
                 r.2s=p.2s, r.3s=p.3s, r.total_s=p.total_s, r.costoWBUnit=p.precioUnitFactUsd*r.areaWBUnidad, 
                 r.perdidaAreaWBCrust=IF(r.tipoMateriaPrima='2', 
                 ((r.areaCrust- p.areaProveedorLote)/ p.areaProveedorLote)*100,r.perdidaAreaWBCrust)
            WHERE r.id='$idRendimiento'";
        return $this->ejecutarQuery($sql, "actualizar Pedido del Lote");
    }
    /******** REPLICA DE REGISTRO DE PEDIDOS PARA RENDIMIENTO NUEVO *******/
    public function copiaPedidosRendimiento($idTraspaso)
    {
        $idUserReg = $this->idUserReg;
        $sql = "INSERT INTO detpedidos (idRendimiento, idPedido, total_s, 1s, 2s, 3s,4s, _20, areaProveedorLote,
                                            fechaReg, idUserReg, estado, cantFinalPedido)
        SELECT r.idRendimientoTransfer, dp.idPedido, r.total_sTransfer,  r.1sTransfer, r.2sTransfer, r.3sTransfer,
        r.4sTransfer, r._20Transfer,
        r.total_sTransfer*p.areaWBPromFact AS areaProveedorLote, NOW(), '$idUserReg', '2', dp.cantFinalPedido
        FROM reasignacionfracclotes r 
        INNER JOIN detpedidos dp ON dp.idRendimiento=r.idRendimiento	
        INNER JOIN pedidos p ON dp.idPedido=p.id
        WHERE r.id='$idTraspaso'";
        return $this->runQuery($sql, "Pedido del Nuevo Lote");
    }
    /******** AJUSTE DE REAL DE PEDIDO *******/
    public function ajusteRealPedido($idRendimientoOrigen)
    {
        $sql = "UPDATE detpedidos dp 
        INNER JOIN reasignacionfracclotes r ON r.idRendimiento= dp.idRendimiento
        INNER JOIN pedidos p ON dp.idPedido=p.id
        SET
        dp.total_s=r.total_s, dp.1s=r.1s, dp.2s=r.2s, dp.3s=r.3s, dp.4s=r.4s, dp._20=r._20,
        dp.areaProveedorLote=p.areaWBPromFact*r.total_s
        WHERE ='$idRendimientoOrigen'";
        return $this->runQuery($sql, "actualizar Pedido de Lote");
    }
    /******** ACTUALIZA CUEROS EN VENTAS DE LOTE DE ORIGEN*******/
    public function actualizacionCuerosVentas($idTraspaso)
    {
        $sql = "UPDATE reasignacuerosventas r
        INNER JOIN ventas v ON v.id=r.idVenta
        INNER JOIN detventas dv ON v.id=dv.idVenta
        SET dv.1s=r.1s, dv.2s=r.2s, dv.3s= r.3s, dv.4s=r.4s, dv._20= r._20,
            dv.total_s=r.total_s
        WHERE r.idReAsignaLote='$idTraspaso'";
        return $this->ejecutarQuery($sql, "actualizar Cueros del Pedido del Lote Origen");
    }

    /******** ACTUALIZA CUEROS EN PEDIDOS DE LOTE DE ORIGEN*******/
    public function actualizacionCuerosPedidos($idTraspaso)
    {
        $sql = "UPDATE reasignacuerospedidos r
        INNER JOIN detpedidos dp ON dp.id=r.idDetPedido
        SET dp.1s=r.1s, dp.2s=r.2s, dp.3s= r.3s, dp.4s=r.4s, dp._20= r._20,
            dp.total_s=r.total_s, dp.areaProveedorLote= r.areaProveedorLote
        WHERE r.idReAsignaLote='$idTraspaso'";
        return $this->ejecutarQuery($sql, "actualizar Cueros del Pedido del Lote Origen");
    }
    /******** ACTUALIZA DATOS DEL LOTE DE ORIGEN*******/
    public function actualizaDatosRendOrigen(
        $idRendOrigen,
        $areaWBRea,
        $areaCrustRea,
        $areaFinalRea,
        $diferenciaAreaRea,
        $promedioAreaRea,
        $recorteAcabadoRea,
        $porcRecorteAcabadoRea,
        $perdidaAreaWBaCrustRea,
        $areaWBXSetRea,
        $areaCrustXSetRea,
        $costoWBXUnidadRea,
        $perdidaAreaCrustTeseoRea,
        $yieldFinalRealRea,
        $_1sRend,
        $_2sRend,
        $_3sRend,
        $_4sRend,
        $_20Rend,
        $total_sRend
    ) {
        $sql = "UPDATE rendimientos 
        SET areaWB='$areaWBRea', areaCrust='$areaCrustRea',
        diferenciaArea='$diferenciaAreaRea', promedioAreaWB='$promedioAreaRea', recorteAcabado='$recorteAcabadoRea',
        porcRecorteAcabado='$porcRecorteAcabadoRea', perdidaAreaWBCrust='$perdidaAreaWBaCrustRea', costoWBUnit='$costoWBXUnidadRea',
        areaWBUnidad='$areaWBXSetRea', areaCrustSet='$areaCrustXSetRea', areaFinal='$areaFinalRea', 
        perdidaAreaCrustTeseo='$perdidaAreaCrustTeseoRea', yieldFinalReal='$yieldFinalRealRea', 1s='$_1sRend', 2s='$_2sRend',
        3s='$_3sRend',  4s='$_4sRend', _20='$_20Rend', total_s='$total_sRend', total_ant_s='$total_sRend'
        WHERE id='$idRendOrigen'";
        return $this->ejecutarQuery($sql, "actualizar Rendimientos del Lote Origen");
    }
    /******** ACTUALIZA CUEROS EN VENTAS DE LOTE DE ORIGEN*******/
    public function eliminaCuerosVentas($idTraspaso)
    {
        $sql = "DELETE FROM reasignacuerosventas 
        WHERE idReAsignaLote='$idTraspaso'";
        return $this->ejecutarQuery($sql, "eliminar Cueros del Ventas del Lote Origen");
    }
    /******** ACTUALIZA CUEROS EN VENTAS DE LOTE DE ORIGEN*******/
    public function eliminarCuerosPedidos($idTraspaso)
    {
        $sql = "DELETE FROM reasignacuerospedidos 
            WHERE idReAsignaLote='$idTraspaso'";
        return $this->ejecutarQuery($sql, "eliminar Cueros del Pedido del Lote Origen");
    }
    /******** ELIMINAR RENDIMIENTO NUEVO *******/
    public function eliminarRendimiento($idRendimiento)
    {
        $sql = "DELETE FROM rendimientos 
        WHERE id='$idRendimiento'";
        return $this->ejecutarQuery($sql, "eliminar Nuevo Lote Registrado");
    }
    /******** ELIMINAR REASIGNACION *******/
    public function eliminarReasignacion($idTraspaso)
    {
        $sql = "DELETE FROM reasignacionfracclotes 
        WHERE id='$idTraspaso'";
        return $this->ejecutarQuery($sql, "eliminar Reasignación");
    }
    /******** CONSULTA NOMBRE DE RENDIMIENTO A TRANSFERIR *******/
    public function consultaNameRendimiento($id)
    {
        $sql = "SELECT CONCAT(rp.loteTemola,'.',COUNT(r.id)+1) AS nLoteTemola 
        FROM rendimientos r 
        INNER JOIN rendimientos rp ON rp.id='$id'
        WHERE r.idRendimientoTransfer='$id'";
        return  $this->consultarQuery($sql, "consultar Nombre del Nuevo Lote.", false);
    }
    /******** CONSULTA TRASPASO DE RENDIMIENTO *******/
    public function getDetRendimiento($id)
    {
        $sql = "SELECT r.*, cm.nombre AS nMateria FROM rendimientos r
                LEFT JOIN catmateriasprimas cm ON r.idCatMateriaPrima=cm.id
                WHERE r.id='$id'";
        return  $this->consultarQuery($sql, "consultar Detalle de Lote", false);
    }
    /******** CONSULTA TRASPASO DE RENDIMIENTO *******/
    public function getTraspasoRendimiento($id)
    {
        $sql = "SELECT * FROM reasignacionfracclotes WHERE id='$id'";
        return  $this->consultarQuery($sql, "consultar Detalle de Lote.", false);
    }
    /******** CONSULTA VENTAS DE RENDIMIENTO *******/
    public function getVentasXLote($id)
    {
        $sql = "SELECT dv.*, v.numFactura, v.numPL FROM ventas v
        INNER JOIN detventas dv ON v.id=dv.idVenta  
        WHERE dv.idRendimiento='$id'";
        return  $this->consultarQuery($sql, "consultar Ventas Registradas.");
    }
    /**************** CONSULTA CALCULO DE LAS VENTAS ***************/
    public function getDetReasignaVentas($id)
    {
        $sql = "SELECT r.*, v.numPL, v.numFactura,  dv.1s AS 1sV, dv.2s AS 2sV, dv.3s AS 3sV, 
                dv.4s AS 4sV, dv._20 AS _20V, dv.total_s AS total_sV, dv.sets
        FROM reasignacuerosventas r
        INNER JOIN reasignacionfracclotes rl ON r.idReAsignaLote=rl.id
        INNER JOIN ventas v ON r.idVenta=v.id
        INNER JOIN detventas dv ON r.idVenta=dv.idVenta AND rl.idRendimiento=dv.idRendimiento

        WHERE r.idReAsignaLote='$id'";
        return  $this->consultarQuery($sql, "consultar Ventas Registradas.");
    }
    /**************** CONSULTA CALCULO DE LOS PEDIDOS ***************/
    public function getDetReasignaPedidos($id)
    {
        $sql = "SELECT r.*, p.numFactura, dp.1s AS 1sP, dp.2s AS 2sP, dp.3s AS 3sP, 
            dp.4s AS 4sP, dp._20 AS _20P, dp.total_s AS total_sP
            FROM
            reasignacuerospedidos r
            INNER JOIN detpedidos dp ON r.idDetPedido=dp.id
            INNER JOIN pedidos p ON dp.idPedido=p.id
            WHERE r.idReAsignaLote = '$id'";
        return  $this->consultarQuery($sql, "consultar Pedidos Registradas.");
    }
    /**************** CONSULTA PROCESO ***************/
    public function getDetProceso($id)
    {
        $sql = "SELECT * FROM catprocesos cp WHERE cp.id='$id'";
        return  $this->consultarQuery($sql, "consultar Ventas Registradas", false);
    }
    /**************** CONSULTA PROGRAMA ***************/
    public function getDetPrograma($id)
    {
        $sql = "SELECT * FROM catprogramas cp WHERE cp.id='$id'";
        return  $this->consultarQuery($sql, "consultar Ventas Registradas", false);
    }

    public function getPedidosXLote($id)
    {
        $sql = "SELECT dp.*, p.areaWBPromFact, p.precioUnitFactUsd FROM detpedidos dp
        INNER JOIN pedidos p ON dp.idPedido=p.id 
        WHERE dp.idRendimiento='$id' AND dp.estado='2'";
        return  $this->consultarQuery($sql, "consultar Pedidos por Lote");
    }
    /**************** CONSULTA RENDIMIENTOS ORIGEN  ***************/
    public function getRendimientos(
        $filtradoFecha = "1=1",
        $filtradoProceso = "1=1",
        $filtradoPrograma = "1=1",
        $filtradoMateria = "1=1",
        $filtradoLote = "1=1",
        $filtradoEstatus = "r.estado='2'"
    ) {
        $sql = "SELECT r.*, DATE_FORMAT(r.fechaEngrase,'%d/%m/%Y') AS f_fechaEngrase,
        DATE_FORMAT(r.fechaEmpaque,'%d/%m/%Y') f_fechaEmpaque,
        pr.nombre AS n_proceso, pr.codigo AS c_proceso, pg.nombre AS n_programa, mp.nombre AS n_materia,
        CONCAT(u.nombre, ' ', u.apellidos) AS str_usuario,
        DATE_FORMAT(r.fechaReg, '%d/%m/%Y %H:%m') AS f_fechaReg, (r.perdidaAreaWBCrust+r.perdidaAreaCrustTeseo) AS totalDifArea
        FROM rendimientos r
        INNER JOIN catprocesos pr ON r.idCatProceso=pr.id
        INNER JOIN catprogramas pg ON r.idCatPrograma=pg.id
        INNER JOIN catmateriasprimas mp ON r.idCatMateriaPrima=mp.id
        LEFT JOIN segusuarios u ON r.idUserRend=u.id
        WHERE $filtradoEstatus  AND $filtradoFecha AND $filtradoMateria AND $filtradoPrograma AND $filtradoProceso AND $filtradoLote
        AND (r.idRendimientoTransfer IS NULL OR r.idRendimientoTransfer='')
        ORDER BY r.semanaProduccion DESC, r.loteTemola";
        return  $this->consultarQuery($sql, "consultar Rendimientos Almacenados", true);
    }
    /**************** CONSULTA REASIGNACION ABIERTA  ***************/
    public function getTraspasoAbierto(){
        $idUserReg= $this->idUserReg;
        $sql="SELECT * FROM reasignacionfracclotes 
                     WHERE idUserReg='$idUserReg' AND estado='1'";
        return  $this->consultarQuery($sql, "consultar Traspaso Abierto", false);

    }
}
