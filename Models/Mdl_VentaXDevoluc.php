<?php
class VentaXDevoluc extends ConexionBD
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
    public function initVenta($fehaFacturacion, $numFactura, $numPL, $idTipoVenta)
    {
        $idUserReg = $this->idUserReg;
        $sql = "INSERT INTO ventasxdevoluc(fechaFact, numFactura, numPL, estado, fechaReg, idUserReg, idTipoVenta) VALUES
                ('$fehaFacturacion', '$numFactura', '$numPL', '1', NOW(), '$idUserReg', '$idTipoVenta')";
        return $this->runQuery($sql, "Iniciar Venta");
    }

    public function addDetVenta($idDetDevolucion, $idPrograma, $cantidad, $lote, $idTipoPrograma)
    {
        $idUserReg = $this->idUserReg;
        $Data = $this->getVentaAbierta();
        $idVenta = $Data['id'];
        $sql = "INSERT INTO detventasxdevoluc (idVentaXDevoluc, loteTemola, unidades, 
                                                tipoLote, fechaReg, idUserReg, idCatPrograma, idDetDevolucRMA) 
                            VALUES ('$idVenta','$lote','$cantidad','$idTipoPrograma',NOW(),'$idUserReg','$idPrograma', '$idDetDevolucion') ";
        return $this->runQuery($sql, "agregar Producto de  Venta");
    }

    public function eliminarDetVenta($id)
    {
        $sql = "DELETE FROM detventasxdevoluc WHERE id='$id'";
        return $this->runQuery($sql, "eliminar el detalle de la Venta");
    }
    public function getDevolucionDisponible()
    {
        $sql = "SELECT dr.*, p.nombre AS p_nombre, d.rma FROM detdevolucionrma dr
        INNER JOIN devolucionesrma d ON dr.idDevolucion=d.id AND d.estado='2'
        INNER JOIN catprogramas p ON dr.idCatPrograma=p.id
        WHERE restante >0";
        return  $this->consultarQuery($sql, " Devolución Disponible");
    }

    public function eliminarVenta()
    {
        $DataVenta = $this->getVentaAbierta();
        $idVenta = $DataVenta['id'];
        $sql = "DELETE v, dv FROM ventasxdevoluc v
                LEFT JOIN detventasxdevoluc dv ON dv.idVentaXDevoluc= v.id
                WHERE v.id='$idVenta'";
        return $this->ejecutarQuery($sql, "Eliminar la Venta");
    }

    public function finalizarVenta(){
        $idUserReg = $this->idUserReg;
        $sql = "UPDATE ventasxdevoluc v
                        INNER JOIN (SELECT idVentaXDevoluc AS idVenta, SUM(unidades) AS totalUnids
                                          FROM detventasxdevoluc GROUP BY idVentaXDevoluc ) t ON t.idVenta = v.id 
                        INNER JOIN detventasxdevoluc dv ON dv.idVentaXDevoluc= v.id
                        INNER JOIN detdevolucionrma dr  ON dv.idDetDevolucRMA=dr.id   
                        SET v.estado='2', dr.cantVendida= dr.cantVendida+ dv.unidades, v.unidFact= t.totalUnids,
                        dr.restante= dr.restante- dv.unidades
                        WHERE v.estado='1' AND v.idUserReg='$idUserReg'";
        return $this->ejecutarQuery($sql, "Finalizar la Venta");
    }

    public function getDetVentaxDevoluc()
    {
        $idUserReg = $this->idUserReg;
        $sql = "SELECT dv.*, cp.nombre AS prg_nombre, dvl.rma FROM detventasxdevoluc dv
        INNER JOIN ventasxdevoluc v ON dv.idVentaXDevoluc=v.id
        INNER JOIN catprogramas cp ON dv.idCatPrograma=cp.id
				INNER JOIN detdevolucionrma ddv ON dv.idDetDevolucRMA=ddv.id
				INNER JOIN devolucionesrma dvl ON ddv.idDevolucion=dvl.id
        WHERE v.estado='1' AND v.idUserReg='$idUserReg'";
        return  $this->consultarQuery($sql, " Detalle de Ventas");
    }

    public function getVentaAbierta()
    {
        $idUserReg = $this->idUserReg;
        $sql = "SELECT v.* FROM ventasxdevoluc v 
               WHERE v.idUserReg='$idUserReg' AND v.estado='1'";
        return  $this->consultarQuery($sql, " Venta Abierta", false);
    }

    public function getVentasCerradas(){
        $sql = "SELECT v.*, CONCAT(u.nombre, ' ', u.apellidos) AS n_userRegistro,
        ct.nombre AS n_tipoVenta, DATE_FORMAT(v.fechaFact, '%d/%m/%Y') AS f_fechaFact,
        DATE_FORMAT(v.fechaReg, '%d/%m/%Y %H:%i') AS f_fechaReg
        FROM ventasxdevoluc v
        INNER JOIN segusuarios u ON v.idUserReg= u.id
        INNER JOIN cattiposventas ct ON v.idTipoVenta=ct.id
        WHERE v.estado='2'";
        return  $this->consultarQuery($sql, " Ventas Cerradas");
    }

    public function getDetVentas($idVenta){
        $sql = " SELECT dv.*, cp.nombre AS prg_nombre, dvl.rma FROM detventasxdevoluc dv
        INNER JOIN ventasxdevoluc v ON dv.idVentaXDevoluc=v.id
        INNER JOIN catprogramas cp ON dv.idCatPrograma=cp.id
				INNER JOIN detdevolucionrma ddv ON dv.idDetDevolucRMA=ddv.id
				INNER JOIN devolucionesrma dvl ON ddv.idDevolucion=dvl.id
        WHERE dv.idVentaXDevoluc='$idVenta'";
        return  $this->consultarQuery($sql, " Detallado de Ventas Cerradas");
    }
}
