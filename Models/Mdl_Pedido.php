<?php
class Pedido extends ConexionBD
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
    public function guardarProveedor($id, $_abierto = true)
    {
        $DataPedido = $this->getPedidoAbiertoXUser();
        if (!is_array($DataPedido)) {
            return $DataPedido;
        }
        $idPedido = $DataPedido["0"]["id"];
        $sql = "UPDATE pedidos SET idCatProveedor='$id' WHERE id='$idPedido'";
        return $this->ejecutarQuery($sql, "actualizar Proveedor del Pedido");
    }
    public function guardarNumFactura($numFactura)
    {
        $DataPedido = $this->getPedidoAbiertoXUser();
        if (!is_array($DataPedido)) {
            return $DataPedido;
        }
        $idPedido = $DataPedido["0"]["id"];
        $sql = "UPDATE pedidos SET numFactura='$numFactura' WHERE id='$idPedido'";
        return $this->ejecutarQuery($sql, "actualizar Num. Factura del Pedido");
    }

    public function guardarFechaFact($fecha)
    {
        $DataPedido = $this->getPedidoAbiertoXUser();
        if (!is_array($DataPedido)) {
            return $DataPedido;
        }
        $idPedido = $DataPedido["0"]["id"];
        $sql = "UPDATE pedidos SET fechaFactura='$fecha' WHERE id='$idPedido'";
        return $this->ejecutarQuery($sql, "actualizar Fecha de Factura del Pedido");
    }
    public function guardarPrecioUnitPesos($precio)
    {
        $DataPedido = $this->getPedidoAbiertoXUser();
        if (!is_array($DataPedido)) {
            return $DataPedido;
        }
        $idPedido = $DataPedido["0"]["id"];
        $sql = "UPDATE pedidos SET precioUnitFactPesos='$precio' WHERE id='$idPedido'";
        return $this->ejecutarQuery($sql, "actualizar Precio Unitario en Pesos de Factura del Pedido");
    }
    public function guardarTC($tc)
    {
        $DataPedido = $this->getPedidoAbiertoXUser();
        if (!is_array($DataPedido)) {
            return $DataPedido;
        }
        $idPedido = $DataPedido["0"]["id"];
        $sql = "UPDATE pedidos SET tc='$tc' WHERE id='$idPedido'";
        return $this->ejecutarQuery($sql, "actualizar Tasa de Cambio del Pedido");
    }

    public function guardarPrecioUnitUSD($usd)
    {
        $DataPedido = $this->getPedidoAbiertoXUser();
        if (!is_array($DataPedido)) {
            return $DataPedido;
        }
        $idPedido = $DataPedido["0"]["id"];
        $sql = "UPDATE pedidos SET precioUnitFactUsd='$usd' WHERE id='$idPedido'";
        return $this->ejecutarQuery($sql, "actualizar Precio Unitario en USD del Pedido");
    }

    public function guardarCueroFacturado($total)
    {
        $DataPedido = $this->getPedidoAbiertoXUser();
        if (!is_array($DataPedido)) {
            return $DataPedido;
        }
        $idPedido = $DataPedido["0"]["id"];
        $sql = "UPDATE pedidos SET totalCuerosFacturados='$total' WHERE id='$idPedido'";
        return $this->ejecutarQuery($sql, "actualizar Total de Cueros Facturados del Pedido");
    }
    public function guardarAreaProv($area)
    {
        $DataPedido = $this->getPedidoAbiertoXUser();
        if (!is_array($DataPedido)) {
            return $DataPedido;
        }
        $idPedido = $DataPedido["0"]["id"];
        $sql = "UPDATE pedidos SET areaProvPie2='$area' WHERE id='$idPedido'";
        return $this->ejecutarQuery($sql, "actualizar Total de Cueros Facturados del Pedido");
    }
    public function guardarAreaWB($wb)
    {
        $DataPedido = $this->getPedidoAbiertoXUser();
        if (!is_array($DataPedido)) {
            return $DataPedido;
        }
        $idPedido = $DataPedido["0"]["id"];
        $sql = "UPDATE pedidos SET areaWBPromFact='$wb' WHERE id='$idPedido'";
        return $this->ejecutarQuery($sql, "actualizar Área WB Promedio Facturadas del Pedido");
    }
    /** OBJECT:  ALMACENAMIENTO DE MATERIA PRIMA EN PEDIDO  Script Date: 22/06/2022 **/

    public function guardarMateriaPrima($mp){
        $DataPedido = $this->getPedidoAbiertoXUser();
        if (!is_array($DataPedido)) {
            return $DataPedido;
        }
        $idPedido = $DataPedido["0"]["id"];
        $sql = "UPDATE pedidos SET idCatMateriaPrima='$mp' WHERE id='$idPedido'";
        return $this->ejecutarQuery($sql, "actualizar Materia Prima del Pedido");
    }

    public function finalizarPedido()
    {
        $DataPedido = $this->getPedidoAbiertoXUser();
        if (!is_array($DataPedido)) {
            return $DataPedido;
        }
        if($DataPedido["0"]["numFactura"]==""){
            return "No existe # de Factura, ingresa nuevamente el # de Factura, si el error persiste comunicate con el depto. Sistema";
        }
        $idPedido = $DataPedido["0"]["id"];
        $sql = "UPDATE pedidos SET estado='2', cuerosXUsar=totalCuerosFacturados, 
        totalCuerosEntregados=totalCuerosFacturados,
        areaWBPromFact=areaProvPie2/totalCuerosFacturados 
        WHERE id='$idPedido'";
        return $this->ejecutarQuery($sql, "Finalizar Pedido");
    }

    public function eliminarPedido()
    {
        $DataPedido = $this->getPedidoAbiertoXUser();
        if (!is_array($DataPedido)) {
            return $DataPedido;
        }
        $idPedido = $DataPedido["0"]["id"];
        $sql = "DELETE FROM  pedidos WHERE id='$idPedido'";
        return $this->ejecutarQuery($sql, "Eliminar Pre-registro del  Pedido");
    }
    public function initPedido()
    {
        $idUserReg = $this->idUserReg;

        $sql = "INSERT INTO pedidos (estado, idUserReg, fechaReg) 
        VALUES ('1','{$idUserReg}',NOW())";
        return $this->ejecutarQuery($sql, "registrar Pedido");
    }
    public function cancelarPedido($idPedido, $motivoDeCancelacion)
    {
        $idUserReg = $this->idUserReg;

        $sql = "UPDATE pedidos SET  motivoCancelacion='$motivoDeCancelacion', 
                idUserCancela='$idUserReg', fechaCancela=NOW(), estado='0' WHERE id='$idPedido'";
        return $this->ejecutarQuery($sql, "cancelar Pedido");
    }

    public function registraDetPedido($idRendimiento, $pedido, $areaProv, $_1s, $_2s, $_3s, $_4s,$_20, $Total)
    {
        $idUserReg = $this->idUserReg;

        $sql = "INSERT INTO detpedidos (idRendimiento, idPedido, areaProveedorLote, 1s, 2s, 3s, 4s, _20, total_s, idUserReg, fechaReg, estado ) 
        VALUES ('$idRendimiento','$pedido','$areaProv','$_1s', '$_2s', '$_3s', '$_4s','$_20','$Total', '$idUserReg', NOW(), '1')";
        return $this->ejecutarQuery($sql, "registrar  Pedido en el Lote");
    }

    public function eliminarDetPedido($id)
    {
        $sql = "DELETE FROM detpedidos WHERE id='$id' ";
        return $this->ejecutarQuery($sql, "eliminar  Pedido en el Lote");
    }
    public function eliminarPedidoDelLote($id)
    {
        $sql = "DELETE dp, r 
        FROM rendimientos r 
        LEFT JOIN detpedidos dp ON dp.idRendimiento=r.id 
        WHERE r.id='$id' AND r.estado='1'";
        return $this->ejecutarQuery($sql, "eliminar información del Lote");
    }

    public function agregarMotivoDeExcepcion($idPedido, $tipo, $cantidad, $motivo, $notaCredito){
        $idUserReg = $this->idUserReg;
        $sql = "INSERT INTO edicionespedidos(tipo, idPedido, totalCueros, descripcion, numNotaCredito, fechaReg, idUserReg) 
        VALUES('$tipo','$idPedido','$cantidad', '$motivo', '$notaCredito', NOW(), '$idUserReg')";
        return  $this->ejecutarQuery($sql, "ingresar Excepción de Pedido", false, true);
    }

    
    public function aumentoPedido($idPedido, $idEdicion){
        $idUserReg = $this->idUserReg;
        $sql="UPDATE pedidos p INNER JOIN edicionespedidos e ON e.idPedido= p.id AND e.id='$idEdicion'
        SET e.cuerosXUsarAnt=p.cuerosXUsar, p.cuerosXUsar= p.cuerosXUsar+e.totalCueros, 
            p.totalCuerosEntregados= p.totalCuerosEntregados+e.totalCueros
        WHERE p.id='$idPedido'";
        /*$sql = "INSERT INTO pedidos(idCatProveedor, numFactura, precioUnitFact, ) 
                SELECT p.idCatProveedor, CONCAT(p.numFactura, '-Extras') FROM edicionespedidos  e
                INNER JOIN pedidos p ON p.id= e.idPedido
                WHERE id='$idEdicion'";*/
        return  $this->ejecutarQuery($sql, "aumento de Pedido");
    }

    
    public function disminucionPedido($idPedido, $idEdicion){
        $idUserReg = $this->idUserReg;
        $sql = "UPDATE pedidos p INNER JOIN edicionespedidos e ON e.idPedido= p.id AND e.id='$idEdicion'
                SET e.cuerosXUsarAnt=p.cuerosXUsar, p.cuerosXUsar= p.cuerosXUsar-e.totalCueros, 
                p.totalCuerosEntregados= p.totalCuerosEntregados-e.totalCueros
                WHERE p.id='$idPedido'";
        return  $this->ejecutarQuery($sql, "disminución de Pedido");
    }

    public function getPedidoAbiertoXUser()
    {
        $idUserReg = $this->idUserReg;
        $sql = "SELECT p.*, cm.tipo AS tipoMatPrima FROM pedidos p 
                LEFT JOIN catmateriasprimas cm ON p.idCatMateriaPrima=cm.id
                 WHERE p.idUserReg='$idUserReg' AND p.estado='1'";
        return  $this->ejecutarQuery($sql, "consultar Pedido Abierto", true);
    }

   

    public function getLotesXPedido($id)
    {
        $sql = "SELECT r.loteTemola, r.areaFinal, r.setsEmpacados, 
        cp.codigo AS c_proceso, cm.nombre AS n_materia, dt.1s, dt.2s, dt.3s, dt.4s, dt.total_s
        FROM detpedidos dt 
		INNER JOIN rendimientos r ON r.id=dt.idRendimiento
        INNER JOIN catmateriasprimas cm ON r.idCatMateriaPrima=cm.id
        INNER JOIN catprocesos cp ON r.idCatProceso=cp.id
        INNER JOIN pedidos p ON dt.idPedido=p.id
        WHERE p.id='$id' AND r.estado>='2' AND dt.estado='2'";
        return  $this->ejecutarQuery($sql, "consultar Lotes de un Pedido", true);
    }
    public function getEtiqXPedido($id)
    {
        $sql = "SELECT r.loteTemola, r.areaFinal, IFNULL(r.almacenPT,0) AS almacenPT,
         cm.nombre AS n_materia, r.1s, r.2s, r.3s, r.4s, r.total_s
        FROM rendimientosetiquetas r 
        INNER JOIN catmateriasprimas cm ON r.idCatMateriaPrima=cm.id
        INNER JOIN pedidos p ON r.idPedido=p.id
        WHERE p.id='$id' AND r.estado>='3'";
        return  $this->ejecutarQuery($sql, "consultar Lotes de Etiquetas de un Pedido", true);
    }

    public function busquedaFactura($idProveedor, $numFactura)
    {
        $idUserReg = $this->idUserReg;

        $sql = "SELECT p.* FROM pedidos p WHERE numFactura='$numFactura' AND idCatProveedor='$idProveedor' 
        AND(
        (estado='1' AND idUserReg!='$idUserReg') OR (estado='2'))";
        return  $this->ejecutarQuery($sql, "consultar Factura de Proveedor", true);
    }

    public function getPedidos($filtradoEstatus = '1=1', $filtradoFecha = "1=1", $filtradoProveedor = "1=1", $filtradoPedidos="1=1", $filtradoMateria='1=1')
    {

        $sql = "SELECT p.*, DATE_FORMAT(p.fechaReg,'%d/%m/%Y %H:%i') AS f_fechaReg, 
        DATE_FORMAT(p.fechaFactura,'%d/%m/%Y') AS f_fechaFactura,
        CONCAT(u.nombre,' ',u.apellidos) AS str_usuario,
        cp.nombre AS nameProveedor, IF((r.id IS NULL AND re.id IS NULL),0,1) AS _uso,
        cm.nombre AS n_materia
        FROM pedidos p
        LEFT JOIN detpedidos r ON p.id= r.idPedido
        INNER JOIN segusuarios u ON p.idUserReg=u.id
        INNER JOIN catproveedores cp ON p.idCatProveedor= cp.id
        LEFT JOIN catmateriasprimas cm ON p.idCatMateriaPrima=cm.id
        LEFT JOIN rendimientosetiquetas re ON p.id=re.idPedido
        WHERE $filtradoEstatus AND $filtradoFecha AND $filtradoProveedor AND $filtradoPedidos AND $filtradoMateria
        GROUP BY p.id
        ORDER BY p.fechaFactura DESC";
        return  $this->ejecutarQuery($sql, "consultar Pedidos", true);
    }

    public function getPedidosDisp($idMateriaPrima)
    {

        $sql = "SELECT p.*, cp.nombre AS nameProveedor, mp.nombre AS nameMateria FROM pedidos p 
        INNER JOIN catproveedores cp ON p.idCatProveedor= cp.id
        INNER JOIN catmateriasprimas mp ON p.idCatMateriaPrima=mp.id
        WHERE p.estado='2' AND p.cuerosXUsar>0 AND p.idCatMateriaPrima='$idMateriaPrima'";
        return  $this->ejecutarQuery($sql, "consultar Pedidos Disponibles", true);
    }

    public function getPedidosMatPDisp($idRendimiento)
    {

        $sql = "SELECT p.*, cp.nombre AS nameProveedor, mp.nombre AS n_materiaPrima,mp.tipo  FROM pedidos p 
                INNER JOIN rendimientos r ON p.idCatMateriaPrima= r.idCatMateriaPrima AND r.id='$idRendimiento'
                INNER JOIN catmateriasprimas mp ON mp.id= p.idCatMateriaPrima
                INNER JOIN catproveedores cp ON p.idCatProveedor= cp.id
                WHERE p.estado='2' AND p.cuerosXUsar>0";
        return  $this->ejecutarQuery($sql, "consultar Pedidos Disponibles", true);
    } 
    /****TODOS LOS PEDIDOS CON CUEROS DISPONIBLES ***/
    public function getPedidosDispSinMat(){
        $sql = "SELECT p.*, cp.nombre AS nameProveedor, mp.nombre AS n_materiaPrima, mp.tipo FROM pedidos p 
        INNER JOIN catproveedores cp ON p.idCatProveedor= cp.id
        INNER JOIN catmateriasprimas mp ON p.idCatMateriaPrima=mp.id
        WHERE p.estado='2' AND p.cuerosXUsar>0";
        return  $this->ejecutarQuery($sql, "consultar Pedidos Disponibles", true);
    }
    public function getCuerosDisponibles()
    {
        $sql = "SELECT SUM(p.cuerosXUsar) totalCueros FROM pedidos p
        WHERE p.estado>='2'";
        return  $this->ejecutarQuery($sql, "consultar Total de Cueros Disponibles", true);
    }

    public function getDetPedidoEnLote($id)
    {
        $sql = "SELECT dp.id, p.numFactura, dp.total_s, dp.1s, dp.2s, dp.3s, dp.4s, dp._20, 
                dp.areaProveedorLote FROM detpedidos dp 
        INNER JOIN pedidos p ON dp.idPedido=p.id
        WHERE dp.idRendimiento='$id' AND dp.estado='1' ";
        return  $this->ejecutarQuery($sql, "consultar Pedidos Asignados a lotes", true);
    }

    public function getPedido($id){
        $sql = "SELECT p.*, mp.tipo AS tipoMateriaPrima
                FROM pedidos p 
                INNER JOIN catmateriasprimas mp ON mp.id= p.idCatMateriaPrima
                WHERE p.id='$id' ";
        return  $this->ejecutarQuery($sql, "consultar Pedido", true);
    }

    /*************** Desglose de cambios *****************/
    public function getEdicionesPedidos($id){
        $sql = "SELECT e.id, e.descripcion, e.tipo, e.totalCueros, e.numNotaCredito, 
        DATE_FORMAT(e.fechaReg,'%d/%m/%Y %H:%i') AS f_fechaReg, e.cuerosXUsarAnt,
        CONCAT(su.nombre, ' ', su.apellidos) AS n_empleado
        FROM 
        edicionespedidos e 
        INNER JOIN segusuarios su ON e.idUserReg=su.id
        WHERE e.idPedido='$id' ORDER BY e.fechaReg DESC";
        return  $this->ejecutarQuery($sql, "consultar Ediciones de Pedido", true);
    }
}
