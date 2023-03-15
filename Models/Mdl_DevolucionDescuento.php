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

    public function iniciarDevolucion($id)
    {
        $idUserReg = $this->idUserReg;
        $sql = "INSERT INTO devoluciones (idVenta, motivo, tipo, estado, fechaReg, idUserReg) 
                            VALUES('$id', '', '', '1', NOW(), '$idUserReg')";
        return  $this->runQuery($sql, "iniciar Devolución de Venta", true);
    }
    public function insertarDetDevolucion($idDevolucion, $idDetVenta, $tipoInventario)
    {
        $idUserReg = $this->idUserReg;
        $sql = "INSERT INTO detdevoluciones (idDevolucion, idDetVenta, idRendimiento, pzasDevolucion, pzasInvtAct, tipoInventario, estado, fechaReg, idUserReg) 
        SELECT d.id, v.id, v.idRendimiento, v.unidades, '0', '$tipoInventario','1', NOW(), '$idUserReg' FROM detventas v
        INNER JOIN devoluciones d ON d.idVenta= v.idVenta
        WHERE d.id='$idDevolucion' AND v.id='$idDetVenta'";
        return  $this->runQuery($sql, "ingresar Detallado de la  Devolución de Venta", true);
    }


    public function devolucionXLoteEmpacado($idDetVenta, $idDevolucion)
    {
        $sql = "UPDATE detventaslotes dvl
          INNER JOIN detventas v ON dvl.idDetVenta=v.id
        INNER JOIN sublotesrecuperados s ON dvl.idSubLote=s.id
        INNER JOIN inventarioempacado ie ON ie.idRendimiento=s.idRendimiento
        INNER JOIN config_inventarios conf ON conf.estado='1'
        SET s.pzasTotales=s.pzasTotales+dvl.unidades, s.setsEmpacados=(s.pzasTotales+dvl.unidades)/conf.pzasEnSets,
            ie.pzasTotales= ie.pzasTotales+dvl.unidades, ie.setsTotales=(ie.pzasTotales+dvl.unidades)/conf.pzasEnSets, dvl.devuelto='1',
            v.devuelto='1'
        WHERE dvl.idDetVenta='$idDetVenta'";
        return  $this->runQuery($sql, "agregar Reintegro de Devolución en Pzas. Empacadas");
    }

    public function marcarDevueltoLoteRechazo($idDetVenta, $idDevolucion)
    {
        $sql = "UPDATE detventaslotes dvl
          INNER JOIN detventas v ON dvl.idDetVenta=v.id
        INNER JOIN sublotesrecuperados s ON dvl.idSubLote=s.id
        INNER JOIN inventarioempacado ie ON ie.idRendimiento=s.idRendimiento
        INNER JOIN config_inventarios conf ON conf.estado='1'
        SET  dvl.devuelto='1'
        WHERE dvl.idDetVenta='$idDetVenta'";
        return  $this->runQuery($sql, "actualizar Lotes Devueltos a Invt. Rechazo");
    }

    public function devolucionReintegroRechazados($idDetVenta, $idDevolucion)
    {
        $sql = "UPDATE detdevoluciones dv
        INNER JOIN detventas v ON dv.idDetVenta=v.id
        INNER JOIN inventariorechazado ir ON ir.idRendimiento=dv.idRendimiento
        SET ir.pzasTotales=ir.pzasTotales+v.unidades, v.devuelto='1', dv.pzasInvtAct=ir.pzasTotales
        WHERE dv.id='$idDevolucion'
        ";
        return  $this->runQuery($sql, "agregar Reintegro de Devolución en Pzas. Rechazadas");
    }



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
        return  $this->runQuery($sql, "agregar Reintegro de Devolución en Pzas. Empacadas");
    }

    public function agregarInventarioRechazado($idDetDevolucion)
    {
        $idUserReg = $this->idUserReg;

        $sql = "INSERT INTO inventariorechazado (idRendimiento, pzasTotales, fechaReg, idUserReg, setsTotales, rezago)
                SELECT  idRendimiento, '0', NOW(), '$idUserReg', '0','0' FROM detdevoluciones WHERE id='$idDetDevolucion'";
        return  $this->runQuery($sql, "agregar Inventario de Rechazo");
    }

    public function actualizarTotalVenta($id){
        $sql = "UPDATE ventas v 
        LEFT JOIN (SELECT dv.idVenta, SUM(dv.unidades) AS total 
                           FROM detventas dv WHERE dv.idVenta='$id' AND dv.devuelto!='1') dv ON dv.idVenta= v.id
        SET v.unidFact = IFNULL(dv.total,0)
        WHERE v.id='$id'";
        return  $this->runQuery($sql, "actualizar unidades de la venta");
    }

    public function getDetDevolucion($_editable = true)
    {
        $idUserReg = $this->idUserReg;
        if ($_editable) {
            $Data = $this->getDevolucionAbierta();
            $idDevolucion = $Data['id'];
        }
        $sql = "SELECT dv.*, cp.nombre AS n_programa, dvta.unidades, r.loteTemola FROM detdevoluciones dv
        INNER JOIN detventas dvta ON dv.idDetVenta=dvta.id
        INNER JOIN rendimientos r ON dv.idRendimiento=r.id
        INNER JOIN catprogramas cp ON r.idCatPrograma=cp.id
        WHERE dv.idDevolucion='$idDevolucion'";
        return  $this->consultarQuery($sql, "consultar Detallado de la  Devolución de Venta");
    }

    public function getDevolucionAbierta()
    {
        $idUserReg = $this->idUserReg;
        $sql = "SELECT * FROM devoluciones d WHERE d.idUserReg='$idUserReg' AND d.estado='1'";
        return  $this->consultarQuery($sql, "consultar Devolución de Venta", false);
    }

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
        return  $this->consultarQuery($sql, "consultar Lotes de un Pedido");
    }

    public function validaInventarioRechazados($idDetDevolucion)
    {
        $sql = "SELECT dd.* FROM detdevoluciones dd
        INNER JOIN inventariorechazado ir ON dd.idRendimiento=ir.idRendimiento
        WHERE dd.id='$idDetDevolucion'";
        return  $this->consultarQuery($sql, "consultar Inventario Recuperado", false);
    }

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
        return  $this->ejecutarQuery($sql, "consultar Detallado de la Venta", true);
    }
}
