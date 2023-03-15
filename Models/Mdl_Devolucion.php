<?php
class Devolucion extends ConexionBD
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

    /****************************************/
    /* CANCELACION DE VENTAS CON RETORNO A INVENTARIO DE EMPACADOS */
    /****************************************/

    public function devolucionAllEmpacados($id)
    {
        $sql = "UPDATE detventaslotes dvl
        INNER JOIN detventas v ON dvl.idDetVenta=v.id
      INNER JOIN sublotesrecuperados s ON dvl.idSubLote=s.id
      INNER JOIN inventarioempacado ie ON ie.idRendimiento=s.idRendimiento
      INNER JOIN config_inventarios conf ON conf.estado='1'
      SET s.pzasTotales=s.pzasTotales+dvl.unidades, s.setsEmpacados=(s.pzasTotales+dvl.unidades)/conf.pzasEnSets,
          ie.pzasTotales= ie.pzasTotales+dvl.unidades, ie.setsTotales=(ie.pzasTotales+dvl.unidades)/conf.pzasEnSets, dvl.devuelto='1',
          v.devuelto='1'
      WHERE v.idVenta='$id' AND  v.devuelto!='1'";
        return  $this->runQuery($sql, " Reintegro de Devolución en Pzas. Empacadas");
    }
    /****************************************/
    /* INICIALIZAR DEVOLUCION */
    /****************************************/
    public function initDevolucion($rma, $fecha, $idVenta)
    {
        $idUserReg = $this->idUserReg;
        $sql = "INSERT INTO devolucionesrma (idVenta, rma, fecha, estado, fechaReg, idUserReg) 
                            VALUES('$idVenta', '$rma', '$fecha', '1', NOW(), '$idUserReg')";
        return  $this->runQuery($sql, " Inicia Registro de Devolución");
    }
    /****************************************/
    /* AGREGA PZAS DETALLE DE DEVOLUCION */
    /****************************************/
    public function agregarDetDevolucion($programa, $cant)
    {
        $idUserReg = $this->idUserReg;
        $Data = $this->getDevolucionAbierta();
        $idDevolucion = $Data['id'];
        $sql = "INSERT INTO detdevolucionrma (idCatPrograma, cantidad, idDevolucion,fechaReg, idUserReg, restante, cantVendida ) 
                                        VALUES('$programa', '$cant', '$idDevolucion',  NOW(), '$idUserReg', '$cant', '0')";
        return  $this->runQuery($sql, " Registro de Detalle de Devolución");
    }
    /****************************************/
    /* ELIMINA PZAS DETALLE DE DEVOLUCION */
    /****************************************/
    public function eliminarDevolucion()
    {
        $idUserReg = $this->idUserReg;
        $sql = "DELETE d, dv FROM devolucionesrma d
                LEFT JOIN detdevolucionrma dv ON d.id=dv.idDevolucion
                WHERE d.idUserReg='$idUserReg' AND d.estado='1'";
        return  $this->runQuery($sql, " Eliminar Devolución de Venta");
    }

  
    /****************************************/
    /* DEVOLUCIONES POR VENTA */
    /****************************************/
    public function getDevolucionesXVenta($idVenta)
    {

        $sql = "SELECT d.*, v.numFactura, CONCAT(u.nombre, '', u.apellidos) AS n_usuarios,
                       DATE_FORMAT(d.fecha, '%d/%m/%Y') AS f_fecha
        FROM devolucionesrma d
        INNER JOIN ventas v ON d.idVenta=v.id
        INNER JOIN segusuarios u ON d.idUserReg=u.id
        WHERE d.idVenta='$idVenta'";
        return  $this->consultarQuery($sql, " Devoluciones de Venta");
    }
    /****************************************/
    /* DETALLE DE DEVOLUCIONES */
    /****************************************/
    public function getDetXDevolucion($idDevolucion)
    {
        $sql = "SELECT dv.*, cp.nombre AS n_programa FROM detdevolucionrma dv
        INNER JOIN devolucionesrma d ON dv.idDevolucion=d.id
        INNER JOIN catprogramas cp ON dv.idCatPrograma=cp.id
        WHERE dv.idDevolucion='$idDevolucion'";
        return  $this->consultarQuery($sql, " Detallado de Devolución de Venta");
    }


    public function getDetDevolucion($_editable = true)
    {
        if ($_editable) {
            $Data = $this->getDevolucionAbierta();
            $idDevolucion = $Data['id'];
        }
        $sql = "SELECT dv.*, cp.nombre AS n_programa FROM detdevolucionrma dv
        INNER JOIN catprogramas cp ON dv.idCatPrograma=cp.id
        WHERE dv.idDevolucion='$idDevolucion'";
        return  $this->consultarQuery($sql, " Detallado de la  Devolución de Venta");
    }


    /****************************************/
    /* DEVOLUCION ABIERTA POR USUARIO */
    /****************************************/
    public function getDevolucionAbierta()
    {
        $idUserReg = $this->idUserReg;
        $sql = "SELECT * FROM devolucionesrma d WHERE d.idUserReg='$idUserReg' AND d.estado='1'";
        return  $this->consultarQuery($sql, " Devolución de Venta", false);
    }
    /****************************************/
    /* DETALLADO DE LOTE POR VENTA */
    /****************************************/
    public function getLoteXVenta($id)
    {
        $sql = "SELECT r.loteTemola,
        cp.codigo AS c_proceso, cm.nombre AS n_materia, dv.unidades, dv.1s, dv.2s, dv.3s, dv.4s, dv.total_s,
        pr.nombre AS n_programa, dv.id
        FROM  detventas dv
        INNER JOIN rendimientos r ON dv.idRendimiento=r.id
        INNER JOIN catmateriasprimas cm ON r.idCatMateriaPrima=cm.id
        INNER JOIN catprocesos cp ON r.idCatProceso=cp.id
        INNER JOIN catprogramas pr ON pr.id = r.idCatPrograma
        WHERE dv.idVenta='$id' AND dv.devuelto!='1'";
        return  $this->consultarQuery($sql, " Lotes de un Pedido");
    }
    /****************************************/
    /* DESPLIEGUE DE DATOS DE VENTA CERRADAS APTAS PARA DEVOLVER */
    /****************************************/
    public function getVentasCerradas($filtradoFecha = '1=1', $filtradoTipo = '1=1')
    {
        $sql = "SELECT v.*, tv.nombre AS n_tipo,
                DATE_FORMAT(v.fechaFact, '%d-%m-%Y') AS f_fechaFact,
                DATE_FORMAT(v.fechaReg, '%d-%m-%Y %H:%i') AS f_fechaReg,
                CONCAT(u.nombre, ' ', u.apellidos) AS str_usuario,
                v.unidFact/conf.pzasEnSets AS _sets
        
        FROM ventas v
        INNER JOIN cattiposventas tv ON v.idTipoVenta=tv.id
        INNER JOIN segusuarios u ON v.idUserReg=u.id
        INNER JOIN config_inventarios conf ON conf.estado='1'

        WHERE v.estado='2' AND $filtradoFecha AND $filtradoTipo
        ORDER BY v.fechaFact DESC";
        return  $this->consultarQuery($sql, " Detallado de la Venta");
    }

    public function getProgramasVentas($idVenta)
    {
        $sql = "SELECT  pr.id, pr.nombre AS prg_nombre, SUM(dv.unidades) AS cantidad FROM detventas dv
        INNER JOIN rendimientos r ON dv.idRendimiento=r.id
        INNER JOIN catprogramas pr ON r.idCatPrograma	= pr.id
        WHERE dv.idVenta='$idVenta'
        GROUP BY r.idCatPrograma";
        return  $this->consultarQuery($sql, " Detallado de Programas de la Venta");
    }
}
