<?php
class Rendimiento extends ConexionBD
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
    public function getSemanaSelect2($busqId = '')
    {
        $filtradoID = $busqId == '' ? '1=1' : "r.loteTemola LIKE '%$busqId%'";

        $sql = "SELECT DISTINCT CONCAT(r.yearWeek, '-W', LPAD(r.semanaProduccion,2,0)) AS id,
        CONCAT(r.yearWeek, '-Sem ', LPAD(r.semanaProduccion,2,0)) AS text
        FROM rendimientos r
        WHERE $filtradoID";
        return  $this->consultarQuery($sql, "consultar lotes");
    }
    public function getLotesOpen($busqId = '')
    {
        $filtradoID = $busqId == '' ? '1=1' : "r.loteTemola LIKE '%$busqId%'";

        $sql = "SELECT r.*, cp.nombre AS nPrograma
        FROM rendimientos r
        INNER JOIN catprogramas cp ON r.idCatPrograma=cp.id
        WHERE $filtradoID AND 
        (r.regEmpaque IS NULL OR r.regEmpaque <>'1') AND
        (r.regTeseo IS NULL OR r.regTeseo <>'1') AND
        (r.regDatos IS NULL OR r.regDatos <>'1') AND
        (r.regOkNok IS NULL OR r.regOkNok <>'1') AND 
         r.estado BETWEEN 1 AND 2
        ORDER BY cp.nombre, CAST(r.loteTemola AS UNSIGNED)";
        return  $this->consultarQuery($sql, "consultar lotes");
    }
    public function getLotesPadresSelect2($busqId = '')
    {
        $filtradoID = $busqId == '' ? '1=1' : "r.loteTemola LIKE '%$busqId%'";

        $sql = "SELECT r.*, cp.nombre AS nPrograma
        FROM rendimientos r
        INNER JOIN catprogramas cp ON r.idCatPrograma=cp.id
        WHERE $filtradoID AND 
        (r.regEmpaque IS NULL OR r.regEmpaque <>'1') AND
        (r.regTeseo IS NULL OR r.regTeseo <>'1') AND
        (r.regDatos IS NULL OR r.regDatos <>'1') AND
        (r.regOkNok IS NULL OR r.regOkNok <>'1')
        AND (r.idRendimientoTransfer IS NULL OR r.idRendimientoTransfer='')
        AND r.total_s>0 AND
         r.estado BETWEEN 1 AND 2
        ORDER BY cp.nombre, CAST(r.loteTemola AS UNSIGNED)";
        return  $this->consultarQuery($sql, "consultar lotes");
    }
    public function getLotesTeseoSelect2($busqId = '')
    {
        $filtradoID = $busqId == '' ? '1=1' : "r.loteTemola LIKE '%$busqId%'";

        $sql = "SELECT r.*, cp.nombre AS nPrograma
        FROM rendimientos r
        INNER JOIN catprogramas cp ON r.idCatPrograma=cp.id
        WHERE $filtradoID AND r.tipoProceso='1' AND
        r.regEmpaque IS NULL OR r.regEmpaque !='1'
        ORDER BY cp.nombre, CAST(r.loteTemola AS UNSIGNED)";
        return  $this->consultarQuery($sql, "consultar lotes");
    }

    public function getLotesFinalesSelect2($busqId = '')
    {
        $filtradoID = $busqId == '' ? '1=1' : "r.loteTemola LIKE '%$busqId%'";

        $sql = "SELECT r.*, cp.nombre AS nPrograma,
        IF(r.regEmpaque ='1' OR r.tipoProceso='2','text-success','text-secondary') AS color
        FROM rendimientos r
        INNER JOIN catprogramas cp ON r.idCatPrograma=cp.id
        WHERE $filtradoID AND 
        -- ((r.regEmpaque ='1' AND r.regTeseo ='1' AND 
        --  r.regOkNok ='1') OR tipoProceso='2') AND
         r.estado ='2'
        ORDER BY cp.nombre, CAST(r.loteTemola AS UNSIGNED)";
        return  $this->consultarQuery($sql, "consultar lotes");
    }
    public function getLotesTodosSelect2($busqId = '')
    {
        $filtradoID = $busqId == '' ? '1=1' : "r.loteTemola LIKE '%$busqId%'";

        $sql = "SELECT r.*, cp.nombre AS nPrograma
        FROM rendimientos r
        INNER JOIN catprogramas cp ON r.idCatPrograma=cp.id
        WHERE $filtradoID AND 
        -- ((r.regEmpaque ='1' AND r.regTeseo ='1' AND 
        --  r.regOkNok ='1') OR tipoProceso='2') AND
         r.estado <>'0'
        ORDER BY cp.nombre, CAST(r.loteTemola AS UNSIGNED)";
        return  $this->consultarQuery($sql, "consultar lotes");
    }

    public function getLotesProceso($busqId = '')
    {
        $filtradoID = $busqId == '' ? '1=1' : "r.loteTemola LIKE '%$busqId%'";
        $sql = "SELECT r.*, cp.nombre AS nPrograma
        FROM rendimientos r
        INNER JOIN catprogramas cp ON r.idCatPrograma=cp.id
        WHERE $filtradoID  AND r.estado<>'0'
        ORDER BY cp.nombre, CAST(r.loteTemola AS UNSIGNED)";
        return  $this->consultarQuery($sql, "consultar lotes");
    }
    /***********************************
     * PROCESO: INGRESO DE LOTE A SISTEMA
     *********************************/
    //Consulta de Lote en Edición (carnaza o piel/etiquetas) en el sistema  
    public function getRendimientoAbierto($tipo = '1')
    {
        $idUserReg = $this->idUserReg;
        $table = $tipo == '1' ? 'rendimientos' : 'rendimientosetiquetas';
        $sql = $tipo == '1' ? "SELECT r.* FROM $table r WHERE r.idUserRend='$idUserReg' AND r.estado='3'" :
            "SELECT r.* FROM $table r WHERE r.idUserReg='$idUserReg' AND r.estado='1'";
        return  $this->ejecutarQuery($sql, "consultar Rendimiento Pendiente por el Usuario", true);
    }
    //Consulta de Lote en Edición (carnaza o piel) en el sistema  

    public function getPreRendimientoAbierto()
    {
        $idUserReg = $this->idUserReg;
        $sql = "SELECT r.* FROM rendimientos r WHERE r.idUserReg='$idUserReg' AND r.estado='1'";
        return  $this->ejecutarQuery($sql, "consultar Rendimiento Pendiente por el Usuario", true);
    }
    //Busqueda de folio de lote (carnaza o piel) en el sistema  
    public function busquedaLote($lote)
    {

        $sql = "SELECT r.* FROM rendimientos r WHERE  loteTemola='$lote' AND estado>'0'";
        return  $this->ejecutarQuery($sql, "consultar Lote", true);
    }
    //Busqueda de folio de lote (etiquetas) en el sistema  
    public function busquedaLoteEtiq($lote)
    {

        $sql = "SELECT r.* FROM rendimientosetiquetas r WHERE  loteTemola='$lote' AND estado>'0'";
        return  $this->ejecutarQuery($sql, "consultar Lote", true);
    }
    //Agregar Lote (carnaza o piel) desde programacion
    public function initRendimiento(
        $fechaEngrase,
        $proceso,
        $lote,
        $programa,
        $materiaPrima,
        $multimateria
    ) {
        $idUserReg = $this->idUserReg;
        $DataProceso = $this->getTipoProceso($proceso);
        $DataPrograma = $this->getAreaNeta($programa);
        $DataMateria = $this->getTipoMateria($materiaPrima);
        $AreaNeta = $DataPrograma[0]['areaNeta'] == '' ? '0.00' : $DataPrograma[0]['areaNeta'];
        $sql = "INSERT INTO rendimientos (fechaEngrase,semanaProduccion,fechaEmpaque, idCatProceso,
        loteTemola, idCatPrograma, idCatMateriaPrima, estado, idUserReg, fechaReg, tipoProceso, areaNeta_Prg, tipoMateriaPrima, cantRecuperacion, multiMateria) 
        VALUES ('{$fechaEngrase}','0','','{$proceso}','{$lote}','{$programa}',
        '{$materiaPrima}','1','{$idUserReg}',NOW(), '{$DataProceso[0]["tipo"]}', '{$AreaNeta}', '{$DataMateria[0]['tipo']}', '0','$multimateria')";
        return $this->ejecutarQuery($sql, "registrar Inicio de Rendimiento");
    }
    //Agregar Lote (etiquetas) desde programacion
   
    //Consultar Lotes (carnaza o piel) Pre Registrados
    public function getLotesPreRegistrados()
    {
        $sql = "SELECT
                r.id,
                r.loteTemola,
                DATE_FORMAT( r.fechaEmpaque, '%d/%m/%Y' ) AS f_fechaEmpaque,
                DATE_FORMAT( r.fechaEngrase, '%d/%m/%Y' ) AS f_fechaEngrase,
                cp.nombre AS n_proceso,
                cp.codigo AS c_proceso,

                pr.nombre AS n_programa,
                mt.nombre AS n_materiaPrima,
                CONCAT( u.nombre, ' ', u.apellidos ) AS n_userRegistro,
                DATE_FORMAT( r.fechaReg, '%d/%m/%Y' ) AS f_fechaReg,
                r.semanaProduccion, r.multiMateria
            FROM
                rendimientos r
                INNER JOIN catmateriasprimas mt ON mt.id = r.idCatMateriaPrima
                INNER JOIN catprocesos cp ON cp.id = r.idCatProceso
                INNER JOIN catprogramas pr ON pr.id = r.idCatPrograma
                INNER JOIN segusuarios u ON u.id = r.idUserReg 
            WHERE
             (r.estado = '2' OR r.estado='3') AND (r.regEmpaque='1' OR r.tipoProceso='2')";
        return  $this->ejecutarQuery($sql, "consultar Lote Registrado", true);
    }
    //Consultar Lotes (carnaza o piel) Pre Registrados
    public function getLotesPreRegistradosProg()
    {
        $sql = "SELECT
                 r.id,
                 r.loteTemola,
                 DATE_FORMAT( r.fechaEmpaque, '%d/%m/%Y' ) AS f_fechaEmpaque,
                 DATE_FORMAT( r.fechaEngrase, '%d/%m/%Y' ) AS f_fechaEngrase,
                 cp.nombre AS n_proceso,
                 cp.codigo AS c_proceso,
 
                 pr.nombre AS n_programa,
                 mt.nombre AS n_materiaPrima,
                 CONCAT( u.nombre, ' ', u.apellidos ) AS n_userRegistro,
                 DATE_FORMAT( r.fechaReg, '%d/%m/%Y' ) AS f_fechaReg,
                 r.semanaProduccion, r.multiMateria
             FROM
                 rendimientos r
                 INNER JOIN catmateriasprimas mt ON mt.id = r.idCatMateriaPrima
                 INNER JOIN catprocesos cp ON cp.id = r.idCatProceso
                 INNER JOIN catprogramas pr ON pr.id = r.idCatPrograma
                 INNER JOIN segusuarios u ON u.id = r.idUserReg 
             WHERE
              (r.estado = '2' OR r.estado='3') AND ((r.tipoProceso='1' AND (r.regTeseo!='1' OR r.regTeseo IS NULL)) 
              OR (r.tipoProceso='2' AND (r.regDatos!='1' OR  r.regDatos IS NULL)))";
        return  $this->ejecutarQuery($sql, "consultar Lote Registrado", true);
    }
    //Pedidos usados para abastecer el lote  (carnaza o piel)
    public function getPedidosXLote($id)
    {
        $sql = "SELECT dp.id, dp.idRendimiento, dp.total_s, dp.1s, dp.2s, 
        dp.3s, dp.4s, dp._20, cp.nombre AS n_proveedor, p.numFactura, r.tipoProceso
        FROM detpedidos dp
        INNER JOIN pedidos p ON dp.idPedido=p.id 
        INNER JOIN catproveedores cp ON p.idCatProveedor=cp.id
        INNER JOIN rendimientos r ON r.id=dp.idRendimiento
        WHERE dp.idRendimiento='$id'";

        return  $this->ejecutarQuery($sql, "consultar Pedidos del Lote Registrado", true);
    }
    //Cierre de Preregistro de Lote (carnaza o piel)
    public function eliminarRendimiento($id)
    {
        $idUserReg = $this->idUserReg;

        $sql = "UPDATE  rendimientos set estado=2, fechaReg=NOW(), idUserRend='{$idUserReg}' WHERE  id='{$id}'";
        return $this->ejecutarQuery($sql, "eliminar Preregistro de Rendimiento");
    }
    //PASE DE LOTE A ALMACEN
    public function paseAlmacenPT($_abierto = true, $idRendimiento = '0', $cantidad = '0')
    {
        $idUserReg = $this->idUserReg;
        if ($_abierto) {
            $datosAbierto = $this->getRendimientoAbierto();
            $idRendimiento = $datosAbierto[0]['id'];
        }
        $sql = "CALL agregarLoteAInventarios('{$idRendimiento}','{$idUserReg}')";
        return $this->ejecutarQuery($sql, "actualizar Inventarios");
    }
    //CAMBIO DE REGISTRO DE DATOS DEL LOTE
    public function cambiaEstatusRegDatos($id)
    {
        $sql = "UPDATE  rendimientos SET regDatos='1', yearWeek=YEAR(NOW()) WHERE id='$id'";
        return $this->ejecutarQuery($sql, "actualizar Registro de Datos de Rendimiento");
    }
    //CALCULAR RENDIMIENTO DEL LOTE
    public function calcularRendimiento($cambioPzas, $_abierto = true, $idRendimiento = '0')
    {
        $idUserReg = $this->idUserReg;
        if ($_abierto) {
            $datosAbierto = $this->getRendimientoAbierto();
            $idRendimiento = $datosAbierto[0]['id'];
        }
        $sql = "CALL calcularRendimientoFase2('{$idRendimiento}','{$idUserReg}', '{$cambioPzas}')";
        return $this->ejecutarQuery($sql, "actualizar Rendimiento");
    }

    //CREACION DE SUPERLOTE
    public function creacionSuperLote($_abierto = true, $idRendimiento = '0')
    {
        $idUserReg = $this->idUserReg;
        if ($_abierto) {
            $datosAbierto = $this->getRendimientoAbierto();
            $idRendimiento = $datosAbierto[0]['id'];
        }
        $sql = "INSERT INTO sublotesrecuperados (idRendimiento, loteTemola, pzasTotales, idExcepcion, porcRechazoFinalAnt, 
                                               porcRecuperacionFinalAnt, setsEmpacados, idUserReg, fechaReg, superLote)
              SELECT r.id, r.loteTemola, r.unidadesEmpacadas, '0', r.porcFinalRechazo, r.porcRecuperacion,  r.unidadesEmpacadas/conf.pzasEnSets,
                    '$idUserReg', NOW(), '1'
              FROM rendimientos  r
              INNER JOIN config_inventarios conf ON conf.estado='1'
              WHERE r.id='$idRendimiento'";
        return $this->ejecutarQuery($sql, "actualizar Super Lote");
    }

    /***********************************
     * PROCESO: ASIGNACION DE MP A LOTE (carnaza o piel)
     *********************************/
    //Registrar Cueros al Lote (carnaza o piel)
    public function registraPedidoLoteo($idRendimiento)
    {
        $idUserReg = $this->idUserReg;
        $sql = "UPDATE rendimientos  r 
        INNER JOIN (SELECT dt.idRendimiento, SUM(dt.total_s) AS total_s, SUM(dt.1s) AS 1s, SUM(dt.2s) AS 2s,
            SUM(dt.3s) AS 3s, SUM(dt.4s) AS 4s, SUM(dt._20) AS _20,SUM(dt.areaProveedorLote) AS areaProveedorLote, AVG(p.precioUnitFactUsd) AS precioUnitFactUsd
            FROM detpedidos dt 
            INNER JOIN pedidos p ON dt.idPedido=p.id
            WHERE dt.idRendimiento='$idRendimiento' AND dt.estado='1'
            GROUP BY dt.idRendimiento) p ON p.idRendimiento = r.id
            SET  r.areaProveedorLote=p.areaProveedorLote, r.1s=p.1s,
                                        r.diferenciaArea=IFNULL(areaWB- p.areaProveedorLote, 0),  r.promedioAreaWB=IFNULL(areaWB/p.total_s,0),
                                        r.porcDifAreaWB= IFNULL(((areaWB- p.areaProveedorLote)/ p.areaProveedorLote)*100,0), r.estado='2',
                                        r._20=p._20,
                                        r.4s=p.4s, r.areaPzasRechazo=(areaWB/p.total_s)*piezasRechazadas,
                                        r.2s=p.2s, r.3s=p.3s, r.total_s=p.total_s, r.costoWBUnit=p.precioUnitFactUsd*r.areaWBUnidad, 
                                        r.perdidaAreaWBCrust=IF(r.tipoMateriaPrima='2', 
                                        ((r.areaCrust- p.areaProveedorLote)/ p.areaProveedorLote)*100,r.perdidaAreaWBCrust)
                                        WHERE r.id='$idRendimiento'";
        return $this->ejecutarQuery($sql, "actualizar Pedido del Lote");
    }
    //Actualiza MP del Stock
    public function actualizaPedidosUsados($idRendimiento)
    {
        $sql = "UPDATE detpedidos dt
        INNER JOIN pedidos p ON dt.idPedido=p.id
        INNER JOIN rendimientos r ON r.id= dt.idRendimiento
        SET p.cuerosXUsar=p.cuerosXUsar-dt.total_s,  dt.cantFinalPedido=p.cuerosXUsar-dt.total_s, dt.estado='2'
        WHERE dt.idRendimiento='$idRendimiento'";
        return $this->ejecutarQuery($sql, "actualizar Información del Pedido del Lote");
    }
    //Registrar Cueros al Lote (etiquetas) =======NO SE USA =======
    public function registrarPedidoEtiquetas($id, $idPedido)
    {
        $sql = "UPDATE rendimientosetiquetas re
        INNER JOIN pedidos p ON p.id='$idPedido'
        SET re.idPedido='$idPedido', p.cuerosXUsar=p.cuerosXUsar-re.total_s, re.estado='3', 
        re.cantFinalPedido=(p.cuerosXUsar-re.total_s)
         WHERE re.id='$id'";
        return $this->ejecutarQuery($sql, "actualizar Pedido");
    }

    /***********************************
     * PROCESO: CANCELACION DE INGRESO DE LOTE A SISTEMA
     *********************************/
    //Cancelacion de la MP Usada de un Lote (carnaza o piel) a Cueros por Usar
    public function cancelacionPedidoEnLote($id)
    {
        $sql = "UPDATE pedidos p 
        INNER JOIN detpedidos dp ON dp.idPedido = p.id
        SET p.cuerosXUsar= dp.total_s + p.cuerosXUsar, dp.estado='0'
        WHERE dp.idRendimiento='$id'";
        return  $this->ejecutarQuery($sql, "Cancelación de Materia Prima a Utilizar");
    }
    //Eliminar Ingreso de Lote (etiquetas)
    public function eliminarRendimientoEtiquetas()
    {
        $datosAbierto = $this->getRendimientoAbierto('2');
        $id = $datosAbierto[0]['id'];
        $sql = "DELETE FROM rendimientosetiquetas WHERE  id='{$id}'";
        return $this->ejecutarQuery($sql, "eliminar Preregistro de Rendimiento");
    }
    //Cancela Ingreso de Lote (etiquetas) Checar cual se esta duplicando
    public function cancelarRendimientoEtiqueta($id)
    {
        $sql = "UPDATE  rendimientosetiquetas SET estado='0' WHERE  id='{$id}'";
        return $this->ejecutarQuery($sql, "cancelar Rendimiento");
    }
    //Eliminar Ingreso de Lote  (carnaza o piel)
    public function eliminarPreRendimiento($id)
    {
        $idUserReg = $this->idUserReg;

        $sql = "UPDATE  rendimientos set estado=0, fechaReg=NOW(), idUserReg='{$idUserReg}' WHERE  id='{$id}'";
        return $this->ejecutarQuery($sql, "eliminar Preregistro de Rendimiento");
    }
    //Eliminar Ingreso de Lote  (carnaza o piel) checar uso hay repeticion
    public function cancelarRendimiento($id)
    {
        $sql = "UPDATE  rendimientos SET estado='0' WHERE  id='{$id}'";
        return $this->ejecutarQuery($sql, "cancelar Rendimiento");
    }
    /***********************************
     * PROCESO: RECHAZO DE CUEROS EN EL LOTE
     *********************************/
    //Decremento de Piezas Rechazadas en Cueros
    public function decrementoTotal_S($id)
    {
        $sql = "UPDATE rendimientosetiquetas r
            SET r.total_ant_s=r.total_s, r.total_s=r.total_s-IFNULL(r.piezasRechazadas,0)
            WHERE r.id='$id'";
        return $this->ejecutarQuery($sql, "decremento de cueros rechazados");
    }
    /***********************************
     * CONSULTA DE ALMACENES DE LOTES (carnaza y piel)
     *********************************/
    //Consulta de Existencia de Sublotes
    public function getLotesEnAlmacen($filtradoMateria = "1=1", $filtradoProcesos = "1=1", $filtradoPrograma = "1=1", $filtradoTipo = "1=1")
    {
        $sql = "SELECT s.loteTemola, r.id, s.pzasTotales, (pr.nombre) AS n_proceso,
        (pg.nombre) AS n_programa, (mp.nombre) AS n_materiaprima,  p.numFactura,
         CONCAT(u.noEmpleado, '-', u.nombre, ' ', u.apellidos) AS str_usuario,
         DATE_FORMAT(r.fechaReg, '%d/%m/%Y %H:%m') AS f_fechaReg, r.tipoProceso,
         cp.nombre AS n_proveedor, r.semanaProduccion
             FROM sublotesrecuperados s 
                         INNER JOIN rendimientos r ON s.idRendimiento=r.id
             INNER JOIN catprocesos pr ON r.idCatProceso=pr.id
             INNER JOIN catprogramas pg ON r.idCatPrograma=pg.id
             INNER JOIN catmateriasprimas mp ON r.idCatMateriaPrima=mp.id
             INNER JOIN detpedidos dp ON r.id=dp.idRendimiento
             INNER JOIN pedidos p ON dp.idPedido=p.id 
             INNER JOIN catproveedores cp ON p.idCatProveedor= cp.id
             INNER JOIN segusuarios u ON r.idUserReg=u.id
             WHERE r.estado>='4' AND $filtradoMateria AND $filtradoProcesos AND $filtradoPrograma AND $filtradoTipo
             ORDER BY YEAR(r.fechaEmpaque) DESC, r.semanaProduccion DESC, r.tipoProceso";
        return  $this->ejecutarQuery($sql, "consultar Lotes en  Almacen", true);
    }


    /***********************************
     * CONSULTA DE ESPECIFICACIONES DE LOTE
     *********************************/
    //proceso
    public function getTipoProceso($idProceso)
    {
        $idUserReg = $this->idUserReg;
        $sql = "SELECT pr.* FROM catprocesos pr WHERE pr.id='$idProceso'";
        return  $this->ejecutarQuery($sql, "consultar Tipo de Proceso", true);
    }
    //tipo de materia
    public function getTipoMateria($idMateria)
    {
        $idUserReg = $this->idUserReg;
        $sql = "SELECT m.* FROM catmateriasprimas m WHERE m.id='$idMateria'";
        return  $this->ejecutarQuery($sql, "consultar Tipo de Materia Prima", true);
    }
    //area neta
    public function getAreaNeta($idPrograma)
    {

        $sql = "SELECT pr.* FROM catprogramas pr WHERE pr.id='$idPrograma'";
        return  $this->ejecutarQuery($sql, "consultar Área Neta del Programa", true);
    }
    //consulta rendimientos (carnaza o piel)
    public function getRendimientos(
        $filtradoFecha = "1=1",
        $filtradoProceso = "1=1",
        $filtradoPrograma = "1=1",
        $filtradoMateria = "1=1",
        $filtradoLote = "1=1",
        $filtradoEstatus = "r.estado='2'",
        $filtradoSemana = '1=1',
        $filtradoProveedor = "1=1",
        $filtradoMaximos = "1=1"
    ) {
        $sql = "SELECT r.*, DATE_FORMAT(r.fechaEngrase,'%d/%m/%Y') AS f_fechaEngrase,
        DATE_FORMAT(r.fechaEmpaque,'%d/%m/%Y') f_fechaEmpaque,
        CONCAT(IFNULL(r.yearWeek, '0000'), '-SEM. ', LPAD(r.semanaProduccion,2,'0')) AS semanaAnio,
        pr.nombre AS n_proceso, pr.codigo AS c_proceso, pg.nombre AS n_programa, mp.nombre AS n_materia,
        CONCAT(u.nombre, ' ', u.apellidos) AS str_usuario,
        DATE_FORMAT(r.fechaReg, '%d/%m/%Y %H:%m') AS f_fechaReg, (r.perdidaAreaWBCrust+r.perdidaAreaCrustTeseo) AS totalDifArea,
        GROUP_CONCAT(DISTINCT pv.nombre) AS proveedores

        FROM rendimientos r

        LEFT JOIN detpedidos dp ON r.id=dp.idRendimiento
        LEFT JOIN pedidos p ON dp.idPedido=p.id
        LEFT JOIN catproveedores pv ON p.idCatProveedor=pv.id

        INNER JOIN catprocesos pr ON r.idCatProceso=pr.id
        INNER JOIN catprogramas pg ON r.idCatPrograma=pg.id
        INNER JOIN catmateriasprimas mp ON r.idCatMateriaPrima=mp.id
        LEFT JOIN segusuarios u ON r.idUserRend=u.id
        WHERE $filtradoEstatus  AND $filtradoFecha AND $filtradoMateria AND $filtradoPrograma AND $filtradoProceso AND $filtradoLote
        AND $filtradoSemana AND $filtradoMaximos 
  
        GROUP BY dp.idRendimiento
        HAVING $filtradoProveedor
        ORDER BY r.yearWeek DESC, r.semanaProduccion DESC";
        return  $this->ejecutarQuery($sql, "consultar Rendimientos Almacenados", true);
    }
    //GET RENDIMIENTOS ETIQUETAS
    public function getRendimientosEtiquetas(
        $filtradoFecha = "1=1",
        $filtradoPrograma = "1=1",
        $filtradoMateria = "1=1",
        $filtradoEstatus = "r.estado='2'",
        $filtradoEstado = "1=1",
        $filtradoArea = "1=1",
        $filtradoTV = "1=1"
    ) {
        $sql = "SELECT r.*, DATE_FORMAT(r.fechaFinal,'%d/%m/%Y') AS f_fechaFinal,
        pg.nombre AS n_programa, mp.nombre AS n_materia,
        CONCAT(u.nombre, ' ', u.apellidos) AS str_usuario,
        DATE_FORMAT(r.fechaReg, '%d/%m/%Y %H:%m') AS f_fechaReg, p.nombre AS n_proveedor,
        pd.numFactura,
        IF(r.idTipoVenta='1', 'CALZADO', 'ETIQUETAS') AS n_tipoventa
        FROM rendimientosetiquetas r
        INNER JOIN catprogramas pg ON r.idCatPrograma=pg.id
        INNER JOIN catmateriasprimas mp ON r.idCatMateriaPrima=mp.id
        INNER JOIN catproveedores p ON r.idCatProveedor=p.id
        LEFT JOIN pedidos pd ON r.idPedido=pd.id
        INNER JOIN segusuarios u ON r.idUserReg=u.id
        WHERE $filtradoEstatus  AND $filtradoFecha AND $filtradoMateria AND $filtradoPrograma AND $filtradoEstado AND $filtradoArea
        AND $filtradoTV
        ORDER BY r.fechaFinal DESC, r.loteTemola";
        return  $this->ejecutarQuery($sql, "consultar Rendimientos de Etiqueta Almacenados", true);
    }
    //consulta detalle de lotes (carnaza o piel)
    public function getDetRendimientos($id)
    {
        $sql = "SELECT r.*, DATE_FORMAT(r.fechaEngrase,'%d/%m/%Y') AS f_fechaEngrase,
        DATE_FORMAT(r.fechaEmpaque,'%d/%m/%Y') f_fechaEmpaque,
        pr.nombre AS n_proceso, pg.nombre AS n_programa, mp.nombre AS n_materia, pr.codigo  AS c_proceso,
        CONCAT(u.noEmpleado, '-', u.nombre, ' ', u.apellidos) AS str_usuario,
        DATE_FORMAT(r.fechaReg, '%d/%m/%Y %H:%m') AS f_fechaReg, (r.perdidaAreaWBCrust+r.perdidaAreaCrustTeseo) AS totalDifArea,
        GROUP_CONCAT(DISTINCT pv.nombre) AS proveedores, IFNULL(pr.sum_hides,0) AS total_pruebas

        FROM rendimientos r

        
        LEFT JOIN detpedidos dp ON r.id=dp.idRendimiento
        LEFT JOIN pedidos p ON dp.idPedido=p.id
        LEFT JOIN catproveedores pv ON p.idCatProveedor=pv.id
        LEFT JOIN (SELECT p.idLote, SUM(p.hides) AS sum_hides 
                                            FROM pruebashides p
                                            GROUP BY p.idLote) pr ON pr.idLote=r.id
        INNER JOIN catprocesos pr ON r.idCatProceso=pr.id
        INNER JOIN catprogramas pg ON r.idCatPrograma=pg.id
        INNER JOIN catmateriasprimas mp ON r.idCatMateriaPrima=mp.id
        INNER JOIN segusuarios u ON r.idUserReg=u.id
                WHERE r.id='$id'";
        return  $this->ejecutarQuery($sql, "consultar Detallado de Rendimiento", true);
    }
    //consulta detalle de lotes (etiquetas)
    public function getDetRendimientosEtiquetas($id)
    {
        $sql = "SELECT r.*, DATE_FORMAT(r.fechaFinal,'%d/%m/%Y') AS f_fechaFinal,
                 pg.nombre AS n_programa, mp.nombre AS n_materia,
                CONCAT(u.noEmpleado, '-', u.nombre, ' ', u.apellidos) AS str_usuario,
                DATE_FORMAT(r.fechaReg, '%d/%m/%Y %H:%m') AS f_fechaReg
                FROM rendimientosetiquetas r
                INNER JOIN catprogramas pg ON r.idCatPrograma=pg.id
                INNER JOIN catmateriasprimas mp ON r.idCatMateriaPrima=mp.id
                INNER JOIN segusuarios u ON r.idUserReg=u.id
                WHERE r.id='$id'";
        return  $this->ejecutarQuery($sql, "consultar Detallado de Rendimiento", true);
    }
    //consulta superlote (verificar su existencia)
    public function getSuperLotes($id)
    {
        $sql = "SELECT r.id, r.loteTemola  FROM rendimientos r WHERE r.tipoProceso='1' AND r.estado='4' AND r.id!='$id' ";
        return  $this->ejecutarQuery($sql, "consultar Lotes Registrados", true);
    }

    /***********************************
     * CONSULTA DE ESPECIFICACIONES DE LOTE (carnaza y piel) PARA VENTA
     *********************************/
    //Lotes Disponibles no vinculadas a ID venta
    public function getRendimientosConAlmacen($idVenta, $tipo = '1')
    {

        $sql = "SELECT r.loteTemola, ie.idRendimiento, ie.pzasTotales, cm.nombre AS n_materia, '1' AS tipoLote,
        r.tipoProceso,  (pg.nombre) AS n_programa
        FROM inventarioempacado ie
        INNER JOIN rendimientos r ON r.id= ie.idRendimiento
        INNER JOIN catmateriasprimas cm ON r.idCatMateriaPrima=cm.id
        INNER JOIN catprogramas pg ON r.idCatPrograma=pg.id

        LEFT JOIN detventas dv ON dv.idRendimiento= r.id AND dv.idVenta='$idVenta'
        WHERE ie.pzasTotales>0 AND r.tipoProceso='$tipo' AND dv.id IS NULL AND r.estado>='4'
        ORDER BY r.tipoProceso, r.loteTemola";
        return  $this->ejecutarQuery($sql, "consultar Lotes Disponibles", true);
    }
    //Consulta de cajas para venta de sets
    public function getCajasDeLotes($idVenta, $tipo = '1', $usoInterno = '0')
    {
        $filtradoInterno = $usoInterno == '1' ? 'd.interna="1"' : "(d.interna IS NULL OR d.interna!='1')";
        $havingTotal = $usoInterno == '1' ? "1=1" : "SUM(d.total)>=400";

        $sql = "SELECT r.loteTemola, d.idLote AS idRendimiento, SUM(d.total) AS pzasTotales, d.numCaja,
        cm.nombre AS n_materia, '1' AS tipoLote,
        r.tipoProceso,  (pg.nombre) AS n_programa
        FROM detcajas d
        INNER JOIN rendimientos r ON r.id= d.idLote
        INNER JOIN catmateriasprimas cm ON r.idCatMateriaPrima=cm.id
        INNER JOIN catprogramas pg ON r.idCatPrograma=pg.id
        WHERE 
         r.tipoProceso='$tipo' AND r.estado>='4' AND 
        $filtradoInterno
        GROUP BY d.idLote
        HAVING  $havingTotal
        ORDER BY pg.nombre, r.loteTemola";
        return  $this->consultarQuery($sql, "consultar Lotes Disponibles");
    }
    //Lotes Disponibles no vinculadas a ventas
    public function getLotesDisponiblesVta($filtradoMateria, $filtradoProcesos, $filtradoPrograma, $filtradoTipo)
    {
        $sql = "SELECT r.loteTemola, r.id, r.almacenPT, (pr.nombre) AS n_proceso,
        (pg.nombre) AS n_programa, (mp.nombre) AS n_materiaprima,  p.numFactura,
        CONCAT(u.noEmpleado, '-', u.nombre, ' ', u.apellidos) AS str_usuario,
        DATE_FORMAT(r.fechaReg, '%d/%m/%Y %H:%m') AS f_fechaReg, r.tipoProceso, r.semanaProduccion,
        cp.nombre AS n_proveedor
            FROM rendimientos r
            INNER JOIN catprocesos pr ON r.idCatProceso=pr.id
            INNER JOIN catprogramas pg ON r.idCatPrograma=pg.id
            INNER JOIN catmateriasprimas mp ON r.idCatMateriaPrima=mp.id
            INNER JOIN detpedidos dp ON r.id=dp.idRendimiento
            INNER JOIN pedidos p ON dp.idPedido=p.id 
            INNER JOIN catproveedores cp ON p.idCatProveedor= cp.id
            INNER JOIN segusuarios u ON r.idUserReg=u.id
            WHERE (r.estado='3' AND r.almacenPT=0) AND $filtradoMateria AND $filtradoProcesos AND $filtradoPrograma AND $filtradoTipo
            ORDER BY YEAR(r.fechaEmpaque) DESC, r.semanaProduccion DESC
            ";
        return  $this->ejecutarQuery($sql, "consultar Lotes en  Almacen", true);
    }



    public function getTotalAlmacen()
    {
        $sql = "SELECT SUM(r.almacenPT) totalAlmacen FROM rendimientos r
        WHERE r.estado>='2'";
        return  $this->ejecutarQuery($sql, "consultar Total de Almacén", true);
    }


    public function getRendimientosParaRecalculo(
        $filtradoFecha = '1=1',
        $filtradoProceso = '1=1',
        $filtradoMateria = '1=1',
        $filtradoPrograma = '1=1',
        $filtradoEmpaques = '1=1'
    ) {
        $dateNOW = date('Y-m-d H:i:s');
        $sql = "SELECT  vw.*,
        r.loteTemola,
        r.semanaProduccion,
        r.totalEmp AS pzasTotalEmp,
        r.totalEmp/conf.pzasEnSets AS setsTotalFinEmp,

        mp.nombre AS n_materia,
        pr.nombre AS n_programa,
        p.codigo AS c_proceso,
        p.nombre AS n_proceso,
        DATE_FORMAT( r.fechaEmpaque, '%d/%m/%Y' ) AS f_fechaEmpaque,
        DATE_FORMAT( r.fechaEngrase, '%d/%m/%Y' ) AS f_fechaEngrase,
        r.setsCortadosTeseo AS setsCortadosTeseo,
        r.pzasCortadasTeseo AS pzasCortadasTeseo,
        @fechaVigencia:=ADDDATE(r.fechaRend, INTERVAL conf.timerRecalculoHras HOUR) AS fechaVigencia,
        IF(@fechaVigencia<'$dateNOW', 0, 1) AS _vigente,
        r.fechaReg, r.excepcion, 
        
        DATE_FORMAT( ADDDATE(r.fechaRend, INTERVAL conf.timerRecalculoHras HOUR), '%d/%m/%Y %H:%i' ) AS f_fechaVigencia
       
        
    FROM
        rendimientos r
        INNER JOIN vw_inventariolotes vw ON vw.id= r.id
        INNER JOIN config_inventarios conf ON conf.estado='1'
        INNER JOIN catmateriasprimas mp ON r.idCatMateriaPrima = mp.id
        INNER JOIN catprocesos p ON r.idCatProceso = p.id
        INNER JOIN catprogramas pr ON r.idCatPrograma = pr.id 
        WHERE r.tipoProceso='1' AND $filtradoFecha  AND $filtradoProceso AND $filtradoMateria AND $filtradoPrograma
        AND $filtradoEmpaques 
    ORDER BY
        r.semanaProduccion DESC,
        r.fechaEmpaque DESC";
        return  $this->ejecutarQuery($sql, "consultar Rendimientos para Recalculo de Sets Empacados", true);
    }
    public function recalcularRendimiento($id, $setsEmpacados)
    {
        $sql = "UPDATE rendimientos r
        LEFT JOIN detpedidos dp ON dp.idRendimiento= r.id
        LEFT JOIN pedidos p ON dp.idPedido=p.id
        SET setsEmpAnt=setsEmpacados, setsEmpacados='$setsEmpacados',
        areaCrustSet=IF('$setsEmpacados'>'0',areaCrust/'$setsEmpacados', 0), areaWBUnidad=IF('$setsEmpacados'>'0',areaWB/'$setsEmpacados', 0), 
        setsRechazados=setsCortadosTeseo-'$setsEmpacados', porcFinalRechazo=((setsCortadosTeseo-'$setsEmpacados')/setsCortadosTeseo)*100,
        costoWBUnit=IF(p.precioUnitFactUsd IS NOT NULL, IF('$setsEmpacados'>'0',areaWB/'$setsEmpacados', 0)/p.precioUnitFactUsd, 0), 
        porcSetsRechazoInicial= ((setsCortadosTeseo-'$setsEmpacados')/setsCortadosTeseo)*100, recalculo='1', yieldFinalReal=areaNeta_Prg/IF('$setsEmpacados'>'0',areaWB/'$setsEmpacados', 0)
        WHERE r.id='$id'";
        return $this->ejecutarQuery($sql, "recalcular Rendimiento");
    }

    public function recalcularPzasRecuperadas($id, $pzasRecuperadas)
    {
        $sql = "UPDATE rendimientos r
        LEFT JOIN detpedidos dp ON dp.idRendimiento= r.id
        LEFT JOIN pedidos p ON dp.idPedido=p.id
        SET pzasRcdasAnt=piezasRecuperadas, piezasRecuperadas='$pzasRecuperadas',
        setsRecuperados='$pzasRecuperadas'/4, porcRecuperacion=('$pzasRecuperadas'/4)/setsCortadosTeseo
        WHERE r.id='$id'";
        return $this->ejecutarQuery($sql, "recalcular Recuperación");
    }



    //Ventas  & Pedidos Efectuados
    public function getVentasvsPedidosSemana($semana, $year)
    {
        $sql = "SELECT (SELECT COUNT(v.id) AS TotalVtas FROM ventas v
        WHERE WEEKOFYEAR(v.fechaFact)='$semana' AND YEAR(v.fechaFact)='$year') AS TotalVtas,
        (SELECT COUNT(p.id) AS TotalPedido FROM pedidos p
        WHERE WEEKOFYEAR(p.fechaFactura)='$semana' AND YEAR(p.fechaFactura)='$year') AS TotalPedido";
        return  $this->ejecutarQuery($sql, "consultar ventas y pedidos de la semana", true);
    }

    public function getPedidosVsVentas($year)
    {
        $sql = "SELECT SUM(IF(tipo='p', total, 0)) total_p, semana, SUM(IF(tipo='v', total, 0)) total_v  FROM  (SELECT COUNT(v.id) AS total, WEEKOFYEAR(v.fechaFact) AS semana, 'v' AS tipo
        FROM ventas v
        WHERE YEAR(v.fechaFact)='$year' AND v.estado='2'
        GROUP BY semana
        
        UNION
        
        SELECT COUNT(p.id) AS total, WEEKOFYEAR(p.fechaFactura) AS semana, 'p' AS tipo
        FROM pedidos p
        WHERE YEAR(p.fechaFactura)='$year' AND p.estado='2'
        GROUP BY semana) L
        GROUP BY semana
        ORDER BY semana
        
        ";
        return  $this->ejecutarQuery($sql, "consultar ventas y pedidos de la semana", true);
    }
    //Total de Lotes de Etiquetas por Semana
    public function getLotesEtiqXSemana($anio)
    {
        $sql = "SELECT semanaProduccion, COUNT(re.id) AS total FROM rendimientosetiquetas re
        WHERE re.estado='2' AND YEAR(re.fechaFinal)='$anio'
        GROUP BY re.semanaProduccion
        ORDER BY re.semanaProduccion";
        return  $this->ejecutarQuery($sql, "consultar Lotes Registrado de la semana", true);
    }
    //Total de Lotes de Piel por Semana
    public function getLotesPielXSemana($anio)
    {
        $sql = "SELECT semanaProduccion, COUNT(re.id) AS total FROM rendimientos re
          WHERE re.estado>='3' AND YEAR(re.fechaEmpaque)='$anio'
          GROUP BY re.semanaProduccion
          ORDER BY re.semanaProduccion";
        return  $this->ejecutarQuery($sql, "consultar Lotes Registrado de la semana", true);
    }


    public function getLotesCapturados($filtradoProceso = '1=1', $filtradoPrograma = '1=1', $filtradoMateria = '1=1', $filtradoEstado = '1=1')
    {
        $sql = "SELECT r.id, r.fechaEngrase, r.semanaProduccion, r.fechaEmpaque, r.idCatProceso, 
        cp.nombre AS n_programa, pr.nombre AS n_proceso, r.loteTemola, 
        DATE_FORMAT(r.fechaEmpaque, '%d/%m/%Y') AS f_fechaEmpaque, 
        DATE_FORMAT(r.fechaEngrase, '%d/%m/%Y') AS f_fechaEngrase,
        DATE_FORMAT(r.fechaRend, '%d/%m/%Y') AS f_fechaRend,
        CONCAT(u.nombre,' ',u.apellidos) AS n_usuarioRend, mp.nombre AS n_materiaprima, r.yieldFinalReal, 
        r.areaNeta_Prg, r.tipoProceso, pr.codigo AS c_proceso
        FROM rendimientos r
        INNER JOIN catprogramas cp ON cp.id=r.idCatPrograma
        INNER JOIN catprocesos pr ON pr.id=r.idCatProceso
        INNER JOIN catmateriasprimas mp ON mp.id=r.idCatMateriaPrima
        LEFT JOIN segusuarios u ON u.id= r.idUserRend
        WHERE r.estado<>'0' AND $filtradoProceso AND $filtradoPrograma AND $filtradoMateria AND $filtradoEstado
        ORDER BY r.fechaEngrase DESC";
        return  $this->consultarQuery($sql, "consultar Lotes Capturados para Reasignación");
    }

    public function reasignacionProgramaALote($idLote, $programa, $proceso = '')
    {
        if ($proceso != '') {
            $sql = "UPDATE rendimientos r 
            INNER JOIN catprogramas cp ON cp.id='$programa'
            INNER JOIN catprocesos cpr ON cpr.id='$proceso'
    
            SET r.idCatPrograma='$programa', r.areaNeta_Prg=cp.areaNeta, r.yieldFinalReal=(cp.areaNeta/r.areaWBUnidad)*100,
                r.idCatProceso='$proceso', r.tipoProceso=cpr.tipo, r.reprogramado='1'        
            WHERE r.id='$idLote'";
        } else {
            $sql = "UPDATE rendimientos r 
            INNER JOIN catprogramas cp ON cp.id='$programa'   
            SET r.idCatPrograma='$programa', r.areaNeta_Prg=cp.areaNeta, r.yieldFinalReal=(cp.areaNeta/r.areaWBUnidad)*100,
            r.reprogramado='1'    
            WHERE r.id='$idLote'";
        }

        return  $this->runQuery($sql, "cambio de Programa al Lote");
    }
    public function registroHistReasignacionPrograma($idLote, $programa, $proceso, $option)
    {
        $idUserReg = $this->idUserReg;
        $sql = "INSERT INTO reasignacionprograma(idRendimiento, idProgramaAnt, idProgramaAct,idProcesoAnt, idProcesoAct, idUserReg, fechaReg, tipo)
        SELECT id, idCatPrograma, '$programa',idCatProceso, '$proceso', '$idUserReg', NOW(), '$option' FROM rendimientos WHERE id='$idLote'";
        return  $this->runQuery($sql, "registrar de historial de cambio de programa del lote");
    }

    public function getLotesCueros($filtradoFecha = "1=1", $filtradoProceso = "1=1", $filtradoPrograma = "1=1", $filtradoMateria = "1=1")
    {
        $sql = "SELECT r.fechaEngrase, DATE_FORMAT(r.fechaEngrase,'%d/%m/%Y') AS f_fechaEngrase,
        r.fechaEmpaque, DATE_FORMAT(r.fechaEmpaque,'%d/%m/%Y') AS f_fechaEmpaque, 
        r.semanaProduccion, r.loteTemola, r.1s, r.2s, r.3s, r.4s, r.total_s,
        CONCAT(cp.codigo, '-', cp.nombre) AS n_proceso, cp.codigo AS c_proceso,
        cprg.nombre AS n_programa, r.piezasRechazadas,
        cmp.nombre AS n_materiaprima
        
        FROM 
        rendimientos r
        INNER JOIN catprocesos cp ON r.idCatProceso=cp.id
        INNER JOIN catprogramas cprg ON r.idCatProceso=cprg.id
        INNER JOIN catmateriasprimas cmp ON r.idCatMateriaPrima=cmp.id
        WHERE r.estado='4' AND $filtradoFecha AND $filtradoProceso AND $filtradoPrograma AND $filtradoMateria
        ORDER BY r.fechaEmpaque DESC";
        return  $this->consultarQuery($sql, "consultar Cueros de Lotes");
    }

    public function getVentasSetsInternos($filtradoFecha)
    {
        $sql = "SELECT v.id, tv.nombre AS n_tipoVenta, v.numPL, v.numFactura, 
        v.fechaFact, DATE_FORMAT(v.fechaFact, '%d/%m/%Y') AS f_fechaFact, cp.nombre AS nPrograma,
        r.loteTemola, r.semanaProduccion, dv.total_s AS tCuerosUsados,
        r.setsCortadosTeseo, (r.unidadesEmpacadas/ci.pzasEnSets) AS setsEmpacados, r.areaWB,
        (r.unidadesEmpacadas+IFNULL(r.piezasRecuperadas,0))/ci.pzasEnSets AS setsEmpacadosRecu,
        dv.unidades/ci.pzasEnSets AS unidFacturadas,
        vwi.setsTotalEmp AS setsActuales,  
        r.areaWB/((r.unidadesEmpacadas+IFNULL(r.piezasRecuperadas,0))/ci.pzasEnSets) AS areaWBXSetsRecu,
        r.areaWB/(r.unidadesEmpacadas/ci.pzasEnSets) AS areaWBXSets
        FROM ventas v  
            INNER JOIN cattiposventas tv ON v.idTipoVenta=tv.id
            INNER JOIN detventas dv ON v.id=dv.idVenta
            INNER JOIN rendimientos r ON dv.idRendimiento=r.id
            INNER JOIN config_inventarios ci ON ci.estado='1'
            LEFT JOIN vw_alltrabajosrecuperacion vw ON r.id=vw.idRendimiento
            INNER JOIN vw_inventariolotes vwi ON r.id=vwi.id
            INNER JOIN catprogramas cp ON r.idCatPrograma=cp.id

         WHERE v.estado='2'  AND $filtradoFecha AND
                        tv.cargaVenta='1' AND tv.id='4'
        ORDER BY  r.semanaProduccion DESC, r.loteTemola DESC, v.fechaFact DESC";
        return  $this->consultarQuery($sql, "consultar Ventas de Sets Internos");

        //v.fechaFact BETWEEN '2022-09-01' AND '2022-09-30'
    }
    public function getVentasSetsGenerales($filtradoFecha, $filtradoPrograma = "1=1")
    {
        $sql = "SELECT v.id, tv.nombre AS n_tipoVenta, v.numPL, v.numFactura, 
        v.fechaFact, DATE_FORMAT(v.fechaFact, '%d/%m/%Y') AS f_fechaFact, cp.nombre AS nPrograma,
        r.loteTemola, r.semanaProduccion, dv.total_s AS tCuerosUsados,
        r.setsCortadosTeseo, (r.unidadesEmpacadas/ci.pzasEnSets) AS setsEmpacados, r.areaWB,
        (r.unidadesEmpacadas+IFNULL(r.piezasRecuperadas,0))/ci.pzasEnSets AS setsEmpacadosRecu,
        dv.unidades/ci.pzasEnSets AS unidFacturadas,
        vwi.setsTotalEmp AS setsActuales,  
        r.areaWB/((r.unidadesEmpacadas+IFNULL(r.piezasRecuperadas,0))/ci.pzasEnSets) AS areaWBXSetsRecu,
        r.areaWB/(r.unidadesEmpacadas/ci.pzasEnSets) AS areaWBXSets
        FROM ventas v  
            INNER JOIN cattiposventas tv ON v.idTipoVenta=tv.id
            INNER JOIN detventas dv ON v.id=dv.idVenta
            INNER JOIN rendimientos r ON dv.idRendimiento=r.id
            INNER JOIN config_inventarios ci ON ci.estado='1'
            LEFT JOIN vw_alltrabajosrecuperacion vw ON r.id=vw.idRendimiento
            INNER JOIN vw_inventariolotes vwi ON r.id=vwi.id
            INNER JOIN catprogramas cp ON r.idCatPrograma=cp.id

         WHERE v.estado='2'  AND $filtradoFecha AND
                        tv.cargaVenta='1' AND tv.id!='4' AND $filtradoPrograma
        ORDER BY  r.semanaProduccion DESC, r.loteTemola DESC, v.fechaFact DESC
        ";
        return  $this->consultarQuery($sql, "consultar Ventas de Sets Internos");

        //v.fechaFact BETWEEN '2022-09-01' AND '2022-09-30'
    }


    public function getLotesSemana($dateWeek)
    {
        $sql = "SELECT r.id, r.loteTemola, r.semanaProduccion, cp.nombre AS nProceso, cpr.nombre AS nPrograma
        FROM rendimientos r
        INNER JOIN catprocesos cp ON r.idCatProceso=cp.id
        INNER JOIN catprogramas cpr ON r.idCatPrograma=cpr.id
        WHERE r.estado='4' AND cp.tipo='1' AND CONCAT(r.semanaProduccion,'-',YEAR(r.fechaEmpaque))='$dateWeek'";
        return  $this->consultarQuery($sql, "consultar Lotes");
    }
    public function getLotesXCapturar($filtradoFecha = '1=1', $filtradoProceso = '1=1', $filtradoMateria = '1=1', $filtradoPrograma = '1=1')
    {
        $sql = "SELECT r.*, cp.nombre AS nProceso, cpr.nombre AS nPrograma,
        cmp.nombre AS nMateriaPrima, DATE_FORMAT(r.fechaEngrase, '%d/%m/%Y') AS fFechaEngrase,
        IF(DATEDIFF(NOW(), DATE_ADD(r.fechaEngrase,INTERVAL 7 DAY))<0,0,DATEDIFF(NOW(), DATE_ADD(r.fechaEngrase,INTERVAL 7 DAY))) 
         AS diasAtraso
        FROM rendimientos r 
        INNER JOIN catprocesos cp ON r.idCatProceso=cp.id
        INNER JOIN catprogramas cpr ON r.idCatPrograma=cpr.id
        INNER JOIN catmateriasprimas cmp ON r.idCatMateriaPrima=cmp.id
        WHERE  
         r.estado='2' AND $filtradoFecha AND $filtradoProceso AND $filtradoMateria AND $filtradoPrograma AND r.tipoProceso='1'
        ORDER BY r.fechaEngrase DESC";
        return  $this->consultarQuery($sql, "consultar Lotes");
    }
    public function getLotesXCapturarTeseo($filtradoFecha = '1=1', $filtradoProceso = '1=1', $filtradoMateria = '1=1', $filtradoPrograma = '1=1')
    {
        $sql = "SELECT r.*, cp.nombre AS nProceso, cpr.nombre AS nPrograma,
        cmp.nombre AS nMateriaPrima, DATE_FORMAT(r.fechaEngrase, '%d/%m/%Y') AS fFechaEngrase,
        IF(DATEDIFF(NOW(), DATE_ADD(r.fechaEngrase,INTERVAL 7 DAY))<0,0,DATEDIFF(NOW(), DATE_ADD(r.fechaEngrase,INTERVAL 7 DAY))) 
        AS diasAtraso, DATE_FORMAT(r.fechaRegTeseo, '%d/%m/%y %H:%i') AS fFechaRegTeseo
        FROM rendimientos r 
        INNER JOIN catprocesos cp ON r.idCatProceso=cp.id
        INNER JOIN catprogramas cpr ON r.idCatPrograma=cpr.id
        INNER JOIN catmateriasprimas cmp ON r.idCatMateriaPrima=cmp.id
        WHERE  
         $filtradoFecha AND $filtradoProceso AND $filtradoMateria AND $filtradoPrograma AND r.tipoProceso='1'
         AND r.estado!='0' AND r.tipoProceso='1'
        ORDER BY r.regTeseo, r.fechaEngrase DESC";
        return  $this->consultarQuery($sql, "consultar Lotes");
    }



    /*************************************** 
     * CONSULTA DE CONSTRUCCION DE FINANCIERO
     ***************************************/
    //CONSULTA DE SEMANAS DE RENDIMIENTO
    public function getSemanasRendimiento($filtradoFecha = "1=1", $filtrado_semanaEtiq = "1=1", $filtradoFechaEtq = "1=1", $filtradoEstatus = "r.estado>='4'")
    {
        $sql = "SELECT * FROM (SELECT DISTINCT r.semanaProduccion, r.yearWeek FROM rendimientos r
        WHERE $filtradoEstatus AND  $filtradoFecha
        UNION
        SELECT DISTINCT r.semanaProduccion, YEAR(r.fechaFinal) AS yearWeek FROM rendimientosetiquetas r
        WHERE  r.estado>='2'   AND $filtrado_semanaEtiq
        ) semana 
        WHERE semanaProduccion IS NOT NULL 
        ORDER BY yearWeek, semanaProduccion ASC";
        return  $this->ejecutarQuery($sql, "consultar Rendimientos Almacenados", true);
    }
    //REPORTE DE METROS CUADRADOS AUTOMOTRIZ MATERIA PRIMA CARNAZA
    public function getM2AutCza($filtradoAnio = "1=1")
    {

        /*   ((
             IFNULL( SUM( r.areaCrust ), 0 )- (
                 IFNULL( p.areaProvPie2, 0 )/(
                 IFNULL( p.totalCuerosFacturados, 0 )) * IFNULL( SUM( r.total_s ), 0 )))/(
             (
             IFNULL( p.areaProvPie2, 0 )/ IFNULL( p.totalCuerosFacturados, 0 )) * IFNULL( SUM( r.total_s ), 0 )))* 100 AS difAreaCompVsCrust,
     IFNULL( SUM( r.perdidaAreaCrustTeseo ), 0 ) */
        $sql = "SELECT
     r.semanaProduccion,IFNULL(SUM( IFNULL(total_desc_s,r.total_s)), 0 ) AS total_s,
     IFNULL( SUM( r.areaFinal ), 0 ) AS areaComprada,
     IFNULL( SUM( r.areaWB ), 0 ) AS areaWB,
     IFNULL( AVG( r.areaFinal ), 0 ) AS promComprada,
     (
         IFNULL( SUM( r.diferenciaArea ), 0 )/((
             IFNULL( p.areaProvPie2, 0 )/ IFNULL( p.totalCuerosFacturados, 0 ))* IFNULL( SUM( r.total_s ), 0 )))* 100 AS difAreaCompradaMedida,
  
  
            ( (IFNULL( SUM( r.areaFinal ), 0 )- IFNULL( SUM( r.areaCrust), 0 ))/ IFNULL( SUM( r.areaCrust ), 0 ))*100
  
  
   AS difAreaCrustTeseo,
   ((
             IFNULL( SUM( r.areaCrust ), 0 )- (
                 IFNULL( p.areaProvPie2, 0 )/(
                 IFNULL( p.totalCuerosFacturados, 0 )) * IFNULL( SUM( r.total_s ), 0 )))/(
             (
             IFNULL( p.areaProvPie2, 0 )/ IFNULL( p.totalCuerosFacturados, 0 )) * IFNULL( SUM( r.total_s ), 0 )))* 100 AS difAreaCompVsCrust,
             ( (IFNULL( SUM( r.areaFinal ), 0 )- IFNULL( SUM( r.areaCrust), 0 ))/ IFNULL( SUM( r.areaCrust ), 0 ))*100
  +
  
  ((
            IFNULL( SUM( r.areaCrust ), 0 )- (
                IFNULL( p.areaProvPie2, 0 )/(
                IFNULL( p.totalCuerosFacturados, 0 )) * IFNULL( SUM( r.total_s ), 0 )))/(
            (
            IFNULL( p.areaProvPie2, 0 )/ IFNULL( p.totalCuerosFacturados, 0 )) * IFNULL( SUM( r.total_s ), 0 )))* 100 AS totalDifArea 
            
 FROM
     rendimientos AS r
     INNER JOIN (
     SELECT
       years,
         semanaProduccion,
         SUM( areaProvPie2 ) AS areaProvPie2,
         AVG( precioUnitFactUsd ) AS precioUnitFactUsd,
         SUM( totalCuerosFacturados ) AS totalCuerosFacturados 
     FROM
         (
             SELECT
             r.semanaProduccion,
             AVG(p.precioUnitFactUsd) precioUnitFactUsd,

            AVG(p.areaProvPie2) AS areaProvPie2,
             AVG(p.totalCuerosFacturados) AS totalCuerosFacturados,
             r.yearWeek  AS years 
         FROM
             detpedidos dp
             INNER JOIN pedidos p ON p.id= dp.idPedido
             INNER JOIN rendimientos r ON dp.idRendimiento = r.id
             AND r.estado >= '4' 
             AND r.tipoProceso = '2'
             AND r.tipoMateriaPrima = '1' 
             WHERE dp.estado='2'

             GROUP BY dp.idRendimiento
         ) total 
     GROUP BY
         years, semanaProduccion 
     ) p ON p.semanaProduccion = r.semanaProduccion AND p.years= YEAR(r.fechaEmpaque)
 WHERE
     r.estado >= '4' 
     AND r.tipoProceso = '2' 
     AND r.tipoMateriaPrima = '1' AND $filtradoAnio
 GROUP BY
     r.semanaProduccion";
        return  $this->ejecutarQuery($sql, "consultar reporte de M2 Automotriz Carnaza", true);
    }

    //REPORTE DE METROS CUADRADOS AUTOMOTRIZ MATERIA PRIMA PIEL
    public function getM2AutPiel($filtradoAnio = "1=1")
    {
        $sql = "SELECT
     r.semanaProduccion, IFNULL( AVG( r.areaFinal ), 0 ) AS promComprada,
     IFNULL( SUM( r.areaWB ), 0 ) AS areaWB,
     IFNULL( SUM( r.areaFinal ), 0 ) AS areaComprada,IFNULL(SUM( IFNULL(total_desc_s,r.total_s)), 0 ) AS total_s,
     (
         IFNULL( SUM( r.diferenciaArea ), 0 )/((
             IFNULL( p.areaProvPie2, 0 )/ IFNULL( p.totalCuerosFacturados, 0 ))* IFNULL( SUM( r.total_s ), 0 )))* 100 AS difAreaCompradaMedida,
    
             ( (IFNULL( SUM( r.areaFinal ), 0 )- IFNULL( SUM( r.areaCrust), 0 ))/ IFNULL( SUM( r.areaCrust ), 0 ))*100
  
  
  AS difAreaCrustTeseo,
  ((
            IFNULL( SUM( r.areaCrust ), 0 )- (
                IFNULL( p.areaProvPie2, 0 )/(
                IFNULL( p.totalCuerosFacturados, 0 )) * IFNULL( SUM( r.total_s ), 0 )))/(
            (
            IFNULL( p.areaProvPie2, 0 )/ IFNULL( p.totalCuerosFacturados, 0 )) * IFNULL( SUM( r.total_s ), 0 )))* 100 AS difAreaCompVsCrust,

     ( (IFNULL( SUM( r.areaFinal ), 0 )- IFNULL( SUM( r.areaCrust), 0 ))/ IFNULL( SUM( r.areaCrust ), 0 ))*100
  +

  ((
            IFNULL( SUM( r.areaCrust ), 0 )- (
                IFNULL( p.areaProvPie2, 0 )/(
                IFNULL( p.totalCuerosFacturados, 0 )) * IFNULL( SUM( r.total_s ), 0 )))/(
            (
            IFNULL( p.areaProvPie2, 0 )/ IFNULL( p.totalCuerosFacturados, 0 )) * IFNULL( SUM( r.total_s ), 0 )))* 100 AS totalDifArea 
 FROM
     rendimientos AS r
     INNER JOIN (
     SELECT
       years,
         semanaProduccion,
         SUM( areaProvPie2 ) AS areaProvPie2,
         AVG( precioUnitFactUsd ) AS precioUnitFactUsd,
         SUM( totalCuerosFacturados ) AS totalCuerosFacturados 
     FROM
         (
             SELECT
             r.semanaProduccion,
            AVG(p.precioUnitFactUsd) precioUnitFactUsd,
            AVG(p.areaProvPie2) AS areaProvPie2,
             AVG(p.totalCuerosFacturados) AS totalCuerosFacturados,
             r.yearWeek  AS years 
         FROM
             detpedidos dp
             INNER JOIN pedidos p ON p.id= dp.idPedido
             INNER JOIN rendimientos r ON dp.idRendimiento = r.id
             AND r.estado >= '4' 
             AND r.tipoProceso = '2'
             AND r.tipoMateriaPrima = '2' 
             WHERE dp.estado='2'

             GROUP BY dp.idRendimiento

         ) total 
     GROUP BY
         years, semanaProduccion 
     ) p ON p.semanaProduccion = r.semanaProduccion AND p.years= YEAR(r.fechaEmpaque)
 WHERE
     r.estado >= '4' 
     AND r.tipoProceso = '2' 
     AND r.tipoMateriaPrima = '2' 
     AND $filtradoAnio
 GROUP BY
     r.semanaProduccion";

        return  $this->ejecutarQuery($sql, "consultar reporte de M2 Automotriz Piel", true);
    }
    public function getTodosLotes($filtradoAnio = "1=1")
    {
        $sql = "SELECT
     r.semanaProduccion,
     IFNULL( SUM( r.areaFinal ), 0 ) AS areaComprada,
     (
         IFNULL( SUM( r.diferenciaArea ), 0 )/((
             IFNULL( p.areaProvPie2, 0 )/ IFNULL( p.totalCuerosFacturados, 0 ))* IFNULL( SUM( r.total_s ), 0 )))* 100 AS difAreaCompradaMedida,
    
             ( (IFNULL( SUM( r.areaFinal ), 0 )- IFNULL( SUM( r.areaCrust), 0 ))/ IFNULL( SUM( r.areaCrust ), 0 ))*100
  
  
  AS difAreaCrustTeseo,
  ((
            IFNULL( SUM( r.areaCrust ), 0 )- (
                IFNULL( p.areaProvPie2, 0 )/(
                IFNULL( p.totalCuerosFacturados, 0 )) * IFNULL( SUM( r.total_s ), 0 )))/(
            (
            IFNULL( p.areaProvPie2, 0 )/ IFNULL( p.totalCuerosFacturados, 0 )) * IFNULL( SUM( r.total_s ), 0 )))* 100 AS difAreaCompVsCrust,

     ( (IFNULL( SUM( r.areaFinal ), 0 )- IFNULL( SUM( r.areaCrust), 0 ))/ IFNULL( SUM( r.areaCrust ), 0 ))*100
  +

  ((
            IFNULL( SUM( r.areaCrust ), 0 )- (
                IFNULL( p.areaProvPie2, 0 )/(
                IFNULL( p.totalCuerosFacturados, 0 )) * IFNULL( SUM( r.total_s ), 0 )))/(
            (
            IFNULL( p.areaProvPie2, 0 )/ IFNULL( p.totalCuerosFacturados, 0 )) * IFNULL( SUM( r.total_s ), 0 )))* 100 AS totalDifArea 
 FROM
     rendimientos AS r
     INNER JOIN (
     SELECT
       years,
         semanaProduccion,
         SUM( areaProvPie2 ) AS areaProvPie2,
         AVG( precioUnitFactUsd ) AS precioUnitFactUsd,
         SUM( totalCuerosFacturados ) AS totalCuerosFacturados 
     FROM
         (
             SELECT
             r.semanaProduccion,
            AVG(p.precioUnitFactUsd) precioUnitFactUsd,
            AVG(p.areaProvPie2) AS areaProvPie2,
             AVG(p.totalCuerosFacturados) AS totalCuerosFacturados,
             r.yearWeek  AS years 
         FROM
             detpedidos dp
             INNER JOIN pedidos p ON p.id= dp.idPedido
             INNER JOIN rendimientos r ON dp.idRendimiento = r.id
             AND r.estado >= '4' 
             
             WHERE dp.estado='2'

             GROUP BY dp.idRendimiento

         ) total 
     GROUP BY
         years, semanaProduccion 
     ) p ON p.semanaProduccion = r.semanaProduccion AND p.years= YEAR(r.fechaEmpaque)
 WHERE
     r.estado >= '4' 
   
     AND $filtradoAnio
 GROUP BY
     r.semanaProduccion";

        return  $this->ejecutarQuery($sql, "consultar reporte de M2 Automotriz Piel", true);
    }
    //REPORTE DE METROS CUADRADOS CALZADO
    public function getM2Calzado($filtradoAnio = "1=1")
    {
        $sql = "SELECT r.semanaProduccion, 
        IFNULL(SUM(r.areaFinal),0) AS totalProducido, 
        IFNULL(AVG(r.areaFinal),0) AS promProducido, 
        IFNULL(SUM(r.areaWB),0) AS totalWB, 
        IFNULL( SUM( r.total_s ), 0 ) AS total_s,
     ((IFNULL(AVG(r.areaFinal),0)-IFNULL(AVG(r.areaWB),0))
      /IFNULL(AVG(r.areaWB),0))*100 AS difAreaWBCrust, 
     ((IFNULL(AVG(r.areaFinal),0)-IFNULL(AVG(r.areaWB),0))
      /IFNULL(AVG(r.areaWB),0))*100 AS totalDifArea, YEAR(r.fechaFinal) AS years
     FROM rendimientosetiquetas AS r 
     WHERE r.estado>='2' AND r.idTipoVenta='1' AND $filtradoAnio
     GROUP BY years, r.semanaProduccion";

        return  $this->ejecutarQuery($sql, "consultar reporte de M2 Calzado", true);
    }

    //REPORTE DE METROS CUADRADOS ETIQUETAS
    public function getM2Etiquetas($tipo_materiaPrima, $filtradoAnio = "1=1")
    {
        $filtradoTipo = "cm.tipo='$tipo_materiaPrima'";
        $sql = "SELECT r.semanaProduccion, IFNULL(SUM(r.areaFinal),0) AS totalProducido, 
            IFNULL(SUM(r.areaWB),0) AS totalWB, 
          IFNULL( SUM( r.total_s ), 0 ) AS total_s,
     ((IFNULL(AVG(r.areaFinal),0)-IFNULL(AVG(r.areaWB),0))
      /IFNULL(AVG(r.areaWB),0))*100 AS difAreaWBCrust, 
     ((IFNULL(AVG(r.areaFinal),0)-IFNULL(AVG(r.areaWB),0))
      /IFNULL(AVG(r.areaWB),0))*100 AS totalDifArea, YEAR(r.fechaFinal) AS years
     FROM rendimientosetiquetas AS r 
     INNER JOIN catmateriasprimas cm ON r.idCatMateriaPrima=cm.id
     WHERE r.estado>='2' AND r.idTipoVenta='2' AND $filtradoAnio
     AND $filtradoTipo
     GROUP BY years, r.semanaProduccion";

        return  $this->ejecutarQuery($sql, "consultar reporte de M2 Etiquetas", true);
    }

    //REPORTE DE WET BLUE
    public function getWetBlue($filtradoAnio = "1=1")
    {
        $sql = "SELECT
     r.semanaProduccion, p.years,
     p.totalCuerosFacturados,
     (p.areaProvPie2/ p.totalCuerosFacturados) * IFNULL(SUM(r.total_s),0) AS areaComprada,
     ( SUM(r.diferenciaArea)/ ((p.areaProvPie2/ p.totalCuerosFacturados) * IFNULL(SUM(r.total_s), 0 )) )* 100 AS difAreaComprada,
     IFNULL( AVG( r.porcRecorteWB ), 0 ) AS recorteWB,
     IFNULL( AVG( r.porcRecorteCrust ), 0 ) AS recorteCrust,
     (SUM(r.areaCrust)-((p.areaProvPie2/ p.totalCuerosFacturados) * SUM(r.total_s)))/((p.areaProvPie2/ p.totalCuerosFacturados) * SUM(r.total_s)) *100 AS difAreaCompVsCrust 
 FROM
     rendimientos AS r
     INNER JOIN (
         SELECT years, semanaProduccion, SUM( areaProvPie2 ) AS areaProvPie2,  AVG( precioUnitFactUsd ) AS precioUnitFactUsd,
                       SUM( totalCuerosFacturados ) AS totalCuerosFacturados 
         FROM  (SELECT r.semanaProduccion, AVG(p.precioUnitFactUsd) precioUnitFactUsd, MAX(p.areaProvPie2) AS areaProvPie2,
                                   MAX(p.totalCuerosFacturados) AS totalCuerosFacturados,  r.yearWeek  AS years 
                       FROM detpedidos dp
                       INNER JOIN pedidos p ON p.id= dp.idPedido
                       INNER JOIN rendimientos r ON dp.idRendimiento = r.id AND r.estado >= '4' AND r.tipoProceso = '1'
                       WHERE dp.estado='2'

                       GROUP BY dp.idRendimiento) total 
         GROUP BY years, semanaProduccion ) p ON p.semanaProduccion = r.semanaProduccion AND YEAR(r.fechaEmpaque) = p.years
 WHERE
     r.estado >= '4' 
     AND r.tipoProceso = '1'  AND $filtradoAnio
 GROUP BY
     p.years, r.semanaProduccion";

        return  $this->ejecutarQuery($sql, "consultar reporte de Wet Blue", true);
    }
    //REPORTE DE SETS 
    public function getSets($filtradoAnio = '1=1')
    {
        $sql = "SELECT 
        p.years, r.semanaProduccion, 
      IFNULL(SUM( r.setsCortadosTeseo ), 0) AS setsCortadosTeseo,
      IFNULL(SUM( r.totalLote0 ), 0) AS setsRecuMas,
      IFNULL(SUM(r.totalRecu),0)/conf.pzasEnSets AS setsRecuperados,
      IFNULL(SUM(r.totalEmp),0)/conf.pzasEnSets AS setsEmpacados,
      IFNULL(SUM(r.setsRechazados),0) AS setsRechazados,
      (IFNULL(SUM(r.setsRechazados),0)/IFNULL(SUM( r.setsCortadosTeseo ), 0))*100 AS porcRechazoInicial,
      ((IFNULL(SUM(r.totalRecu),0)/conf.pzasEnSets)/IFNULL(SUM( r.setsCortadosTeseo ), 0))* 100 AS porcSetsRecuperados,
      ((IFNULL(SUM(r.totalRech),0)/conf.pzasEnSets)/IFNULL(SUM( r.setsCortadosTeseo ), 0))* 100 AS porcFinalRechazo,
      SUM( r.areaCrust) / (IFNULL(SUM(r.totalEmp),0)/conf.pzasEnSets) AS areaRealCrustXSet,
      SUM( r.areaWB)/(SUM(r.totalEmp)/conf.pzasEnSets) AS areaWBXSet,
      SUM( r.areaWB)/(SUM(r.pzasCortadasTeseo)/conf.pzasEnSets) AS areaWBXSetTeseo,
      IFNULL(SUM(r.totalRech),0)/conf.pzasEnSets AS setsRechazoFinales,
      p.precioUnitFactUsd,
      (SUM( r.areaWB)/(SUM(r.totalEmp)/conf.pzasEnSets))*p.precioUnitFactUsd AS costoWBXSet,
      (
          IFNULL( SUM( r.setsRechazados ), 0 )/(
              IFNULL( SUM( r.pzasCortadasTeseo ), 0 )/ 4 
          ))* 100 AS porcRechazoFinal,
          (IFNULL( SUM( r.diferenciaArea ), 0 )/((
     IFNULL( p.areaProvPie2, 0 )/ IFNULL( p.totalCuerosFacturados, 0 ))* IFNULL( SUM( r.total_s ), 0 )))* 100 AS difAreaCompradaMedida,
     ((IFNULL( SUM( r.areaFinal ), 0 )- IFNULL( SUM( r.areaCrust), 0 ))/ IFNULL( SUM( r.areaCrust ), 0 ))*100 AS difAreaCrustTeseo,
   ((IFNULL( SUM( r.areaCrust ), 0 )- (IFNULL( p.areaProvPie2, 0 )/( IFNULL( p.totalCuerosFacturados, 0 )) * IFNULL( SUM( r.total_s ), 0 )))/(
  ( IFNULL( p.areaProvPie2, 0 )/ IFNULL( p.totalCuerosFacturados, 0 )) * IFNULL( SUM( r.total_s ), 0 )))* 100 AS difAreaCompVsCrust,
  ( (IFNULL( SUM( r.areaFinal ), 0 )- IFNULL( SUM( r.areaCrust), 0 ))/ IFNULL( SUM( r.areaCrust ), 0 ))*100
   +(( IFNULL( SUM( r.areaCrust ), 0 )- (IFNULL( p.areaProvPie2, 0 )/(IFNULL( p.totalCuerosFacturados, 0 )) * IFNULL( SUM( r.total_s ), 0 )))/(
  (IFNULL( p.areaProvPie2, 0 )/ IFNULL( p.totalCuerosFacturados, 0 )) * IFNULL( SUM( r.total_s ), 0 )))* 100 AS totalDifArea 
    FROM
    rendimientos AS r
    INNER JOIN config_inventarios conf ON conf.estado='1'
    INNER JOIN vw_inventariolotes i ON i.id=r.id
     INNER JOIN (
        SELECT years, semanaProduccion, SUM( areaProvPie2 ) AS areaProvPie2, AVG( precioUnitFactUsd ) AS precioUnitFactUsd,
                        SUM( totalCuerosFacturados ) AS totalCuerosFacturados 
        FROM  (SELECT r.semanaProduccion, AVG(p.precioUnitFactUsd) precioUnitFactUsd, AVG(p.areaProvPie2) AS areaProvPie2,
                                    AVG(p.totalCuerosFacturados) AS totalCuerosFacturados,  r.yearWeek  AS years 
                        FROM detpedidos dp
                        INNER JOIN pedidos p ON p.id= dp.idPedido
                        INNER JOIN rendimientos r ON dp.idRendimiento = r.id AND r.estado >= '4' AND r.tipoProceso = '1'
                        WHERE dp.estado='2'

                        GROUP BY dp.idRendimiento) total 
        GROUP BY years, semanaProduccion
                    ) p ON p.semanaProduccion = r.semanaProduccion AND YEAR(r.fechaEmpaque) = p.years
                    WHERE
      r.estado >= '4' 
      AND r.tipoProceso = '1'  AND $filtradoAnio
  GROUP BY
    p.years,  r.semanaProduccion";
        return  $this->ejecutarQuery($sql, "consultar reporte de set's", true);
    }



    /*************************************** 
     * ESTADISTICA DE RENDIMIENTOS DE LOTES
     ***************************************/
    //Total de Recuperacion por Año
    public function getRecuperacionAnio($anio)
    {
        $sql = "SELECT r.semanaProduccion, 
         SUM(mr.totalRecuperacion) AS total
         FROM materialesrecuperados mr
         INNER JOIN rendimientos r ON mr.idRendRecup=r.id
         WHERE YEAR(r.fechaEmpaque)='$anio'
         GROUP BY r.semanaProduccion";
        return  $this->consultarQuery($sql, "consultar Recuperación por Año");
    }
    //KPIS Semana para Supervision
    public function getKPISSemanaSup($week)
    {
        $sql = "SELECT COUNT(r.id) AS totalLotes, 
        SUM(r.totalEmp) AS totalEmp,
        SUM(mr.totalRecuperacion) AS totalRecu,
        SUM(ir.pzasTotales) AS totalRech
        FROM
        rendimientos r 
        INNER JOIN config_inventarios conf ON conf.estado='1'
        LEFT JOIN (SELECT idRendRecup, SUM(totalRecuperacion) AS totalRecuperacion
            FROM materialesrecuperados GROUP BY idRendRecup
        ) mr ON mr.idRendRecup=r.id
        LEFT JOIN inventariorechazado ir ON ir.idRendimiento=r.id
        WHERE r.semanaProduccion='$week' AND YEAR(r.fechaEngrase)= YEAR(NOW())";
        return  $this->consultarQuery($sql, "consultar KPIS Semana Supervisor", false);
    }
    //Recuperacion semanal    
    public function getRecuperacionSemanal($week)
    {
        $sql = "SELECT r.id AS idLote, IFNULL(mr.total,0) AS totalRecuperado,IFNULL(e.total,0) AS totalEmpacado, 
        r.loteTemola
        FROM rendimientos r
        LEFT JOIN
        (SELECT d.idLote, SUM(d.total) AS total FROM detcajas d 
        WHERE d.tipo='3'
        GROUP BY d.idLote
        ) e ON e.idLote=r.id
        LEFT JOIN 
        (SELECT mr.idRendRecup AS idLote, SUM(mr.totalRecuperacion) AS total
         FROM materialesrecuperados mr
         WHERE mr.estado='4'
         GROUP BY mr.idRendRecup) mr ON mr.idLote=r.id 
        WHERE YEAR(r.fechaEmpaque)=YEAR(NOW()) AND 
        r.semanaProduccion='$week'";
        return  $this->consultarQuery($sql, "consultar Recuperación por Semana");
    }
    //Auditorias de Unidades Existentes ====Exclusivo para Sistemas =======
    public function getAuditoriaUnidades($filtradoFecha)
    {
        $sql = "SELECT r.id,r.semanaProduccion, r.loteTemola, cp.codigo AS nProceso, cpr.nombre AS nPrograma,
        r.unidadesEmpacadas AS unitReal, r.totalEmp AS unitGeneral,
        r.totalEmp-r.unidadesEmpacadas AS realRecuperado,
        ((r.totalEmp-r.unidadesEmpacadas)*100)/r.unidadesEmpacadas AS crecimiento
        FROM rendimientos r
        INNER JOIN  catprocesos cp ON r.idCatProceso=cp.id
        INNER JOIN catprogramas cpr ON r.idCatPrograma=cpr.id
        WHERE $filtradoFecha
        AND cp.tipo='1' AND r.estado='4'";
        return  $this->consultarQuery($sql, "consultar Auditorías");
    }
    //Consulta numero de lote por semana
    public function getLoteXSemana($idSemana, $option = "1")
    {
        $table = $option == '1' ? "rendimientos" : "rendimientosetiquetas";
        $estatus = $option == '1' ? "4" : "4";

        $sql = "SELECT COUNT(r.id) AS total FROM $table r 
        WHERE r.semanaProduccion ='$idSemana' AND r.estado='$estatus'";
        return  $this->ejecutarQuery($sql, "consultar Total de Lotes en Almacén", true);
    }
    //Consulta numero de piezas rechazadas por semana (carnaza & piel)
    public function getPzasRechazadasXSemana($idSemana)
    {
        $sql = "SELECT SUM(ir.pzasTotales) AS total FROM 
        inventariorechazado ir 
        INNER JOIN rendimientos r ON ir.idRendimiento=r.id
        WHERE r.semanaProduccion ='$idSemana' 
        AND YEAR (r.fechaEmpaque)= YEAR(NOW())
        AND r.estado='4' AND r.tipoProceso='1'";
        return  $this->ejecutarQuery($sql, "consultar Total de Piezas Rechazadas en Almacén", true);
    }
    //Consulta numero de piezas rechazadas por semana (etiquetas)
    public function getPzasRechazadasXSemanaEtiq($idSemana)
    {
        $sql = "SELECT SUM(r.piezasRechazadas) AS total FROM rendimientosetiquetas r 
        WHERE r.semanaProduccion ='$idSemana' AND r.estado='4' AND 	r.tipoProceso='1'";
        return  $this->ejecutarQuery($sql, "consultar Total de Piezas Rechazadas en Almacén", true);
    }
    //Desglose de Lotes Rechazados  (carnaza & piel)
    public function getKardexRechazados($idSemana)
    {
        $sql = "SELECT ir.idRendimiento, r.semanaProduccion, r.loteTemola, r.pzasSetsRechazadas AS pzasIniciales, 
        mr.total AS pzasRecuperadas,
        ir.pzasTotales AS pzasActuales, cp.nombre AS nPrograma
        FROM 
        inventariorechazado ir 
        INNER JOIN rendimientos r ON ir.idRendimiento=r.id
        INNER JOIN catprogramas cp ON r.idCatPrograma=cp.id
        INNER JOIN (
        SELECT mr.idRendRecup, SUM(totalRecuperacion) AS total 
        FROM materialesrecuperados mr 
        GROUP BY mr.idRendRecup
        ) mr ON ir.idRendimiento=mr.idRendRecup
        WHERE r.semanaProduccion ='$idSemana' 
        AND YEAR (r.fechaEmpaque)= YEAR(NOW())
        AND r.estado='4'";
        return  $this->consultarQuery($sql, "consultar Detallado de Piezas Rechazadas");
    }
    //Desglose de Lotes Empacados (carnaza & piel)
    public function getSetsEmpacadosXSemana($idSemana)
    {
        $sql = "SELECT SUM(r.totalEmp)/4 AS total FROM rendimientos r 
        WHERE r.semanaProduccion ='$idSemana' 
        AND YEAR (r.fechaEmpaque)= YEAR(NOW())
        AND r.estado='4' AND r.tipoProceso='1'";
        return  $this->ejecutarQuery($sql, "consultar Total de Set's  Empacados en Almacén", true);
    }
    //Tendencias de humedad, suavidad, quiebre de los rendimientos
    public function getTendenciasHumSuabQuieb($anio, $filtrado)
    {
        $sql = "SELECT 
           r.semanaProduccion,	
           CONCAT(r.semanaProduccion, '-', YEAR(r.fechaEmpaque)) AS f_semana,
           AVG(r.humedad) AS promHumedad, 	
           AVG(r.quiebre) AS promQuiebre,
           AVG(r.suavidad) AS promSuavidad,
                       p.areaProvPie2/p.totalCuerosFacturados AS areaWBProm,
           (SUM(r.areaCrust)-((p.areaProvPie2/p.totalCuerosFacturados)*r.total_s))/((p.areaProvPie2/p.totalCuerosFacturados)*r.total_s) AS promWBCrust
       FROM rendimientos r
                INNER JOIN (
         SELECT years, semanaProduccion, SUM( areaProvPie2 ) AS areaProvPie2, AVG( precioUnitFactUsd ) AS precioUnitFactUsd,
                SUM( totalCuerosFacturados ) AS totalCuerosFacturados 
         FROM  (SELECT r.semanaProduccion, AVG(p.precioUnitFactUsd) precioUnitFactUsd, AVG(p.areaProvPie2) AS areaProvPie2,
                        AVG(p.totalCuerosFacturados) AS totalCuerosFacturados,  r.yearWeek  AS years 
                FROM detpedidos dp
                INNER JOIN pedidos p ON p.id= dp.idPedido
                INNER JOIN rendimientos r ON dp.idRendimiento = r.id AND r.estado >= '4' AND $filtrado 
                WHERE dp.estado='2'
                         GROUP BY dp.idRendimiento) total 
         GROUP BY years, semanaProduccion
                     ) p ON p.semanaProduccion = r.semanaProduccion AND YEAR(r.fechaEmpaque) = p.years
       WHERE YEAR(r.fechaEmpaque)='$anio' AND $filtrado
       GROUP BY r.semanaProduccion
       ORDER BY r.semanaProduccion ASC";
        return  $this->consultarQuery($sql, "consultar Tendencias");
    }

    public function resetDatosLote($_abierto=true)
    {
        if ($_abierto) {
            $datosAbierto = $this->getRendimientoAbierto();
            $idRendimiento = $datosAbierto[0]['id'];
        }
        $sql = "UPDATE rendimientos r 
        SET fechaEmpaque='', semanaProduccion='',
        areaWB='0', piezasRechazadas='0', 
        piezasRecuperadas='0',
        porcRecorteWB='0',
        porcRecorteCrust='0',
        humedad='0',
        areaCrust='0',
        recorteAcabado='0',
        quiebre='0',
        suavidad='0',
        comentariosRechazo='',
        piezasRechazadas='0'
        WHERE r.id='$idRendimiento'";
        return  $this->runQuery($sql, "resetear datos de lotes");
    }
    public function insertarSolicitudTeseo($id, $areaTeseo, $yield, $_12, $_3, $_6, $_9, 
    $pzasCortadasTeseo,$setsCortadosTeseo, $motivo){
        $idUserReg= $this->idUserReg;
        $sql = "INSERT INTO edicionesteseo (idLote, _12Teseo, _3Teseo, _6Teseo, _9Teseo, yieldFinalReal,
                setsCortadosTeseo, pzasCortadasTeseo, areaFinal, fechaEnvio, idUserEnvio, motivo, estado)
                VALUES ('$id', '$_12', '$_3', '$_6', '$_9','$yield', '$setsCortadosTeseo', 
                '$pzasCortadasTeseo', '$areaTeseo', NOW(), '$idUserReg', '$motivo', '1' )";
        return  $this->runQuery($sql, "envio de solicitud de edicion de datos");
    }

    public function getLotesEnRegistro(){
        $sql = "SELECT r.id, DATE_FORMAT(r.fechaEngrase,'%d/%m/%Y') AS f_fechaEngrase,
        r.loteTemola, cp.nombre AS nProceso, cp.codigo AS cProceso,
        CONCAT(cp.codigo, '-', cp.nombre) AS cProcesoCompleto, 
        cpr.nombre AS nPrograma, cm.nombre AS nMateriaPrima,
        IFNULL(CONCAT(su.nombre, ' ', su.apellidos),'n/a') AS nUsuarioRend
        
        FROM rendimientos r
        INNER JOIN catprocesos cp ON r.idCatProceso=cp.id
        INNER JOIN catprogramas cpr ON r.idCatPrograma=cpr.id
        INNER JOIN catmateriasprimas cm ON r.idCatMateriaPrima=cm.id
        LEFT JOIN segusuarios su ON r.idUserRend=su.id
        WHERE r.estado='3'";
        return  $this->consultarQuery($sql, "consultar Lotes en Registro");
    }
    public function registrarAreaWB($id, $areaWBOrig){
        $sql="UPDATE rendimientos SET 
        areaWBOrig='$areaWBOrig', areaWB=IF(tipoProceso='2',
        ('$areaWBOrig'/total_s)*(IFNULL(total_desc_s,total_s)), '$areaWBOrig')
        WHERE id='$id'";
        return  $this->runQuery($sql, "envio de solicitud de edicion de datos");
    }
    public function registrarMPRechazadas($id, $mp){
        $sql="UPDATE rendimientos SET 
        piezasRechazadas='$mp', 
        total_desc_s=IF(tipoProceso='2',total_s+IFNULL(cuerosReasig,0)-'$mp', total_s),
        areaWB=IF(tipoProceso='2',(areaWBOrig/total_s)*(total_s+IFNULL(cuerosReasig,0)-'$mp'), areaWB)
        WHERE id='$id'";
        return  $this->runQuery($sql, "registro de disminucion de materia prima.");
    }
    public function registrarMPReasignadas($id, $mp){
        $sql="UPDATE rendimientos SET 
        cuerosReasig='$mp', 
        total_desc_s=IF(tipoProceso='2',total_s-IFNULL(piezasRechazadas,0)+'$mp', total_s),
        areaWB=IF(tipoProceso='2',(areaWBOrig/total_s)*(total_s-piezasRechazadas+'$mp'), areaWB)
        WHERE id='$id'";
        return  $this->runQuery($sql, "registro de aumento de materia prima.");
    }
    /*************************************** 
     * CALCULO OPERACIONAL
     ***************************************/
    public function lastWeekDay($week, $year)
    {
        $timestamp = mktime(0, 0, 0, 1, 1, $year) + ($week * 7 * 24 * 60 * 60);
        return date('Y-m-d', $timestamp);
    }

    public function firstWeekDay($week, $year)
    {
        $timestamp = mktime(0, 0, 0, 1, 1, $year) + ($week * 7 * 24 * 60 * 60);
        $monday = $timestamp - 86400 * (date('N', $timestamp) - 1);
        $fecha = date("Y-m-d", strtotime(date('Y-m-d', $monday) . "- 4 days"));
        // return date('Y-m-d', $monday);
        return $fecha;
    }

    public function endWeekDay($week, $year)
    {
        $timestamp = mktime(0, 0, 0, 1, 1, $year) + ($week * 7 * 24 * 60 * 60);
        $monday = $timestamp - 86400 * (date('N', $timestamp) - 1);
        $fecha = date("Y-m-d", strtotime(date('Y-m-d', $monday) . "+ 3 days"));
        // return date('Y-m-d', $monday);
        return $fecha;
    }

}
