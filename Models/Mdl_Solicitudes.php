<?php
class Solicitud extends ConexionBD
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

    public function agregarSolicitud($idRendimiento, $descripcionSolicitud)
    {
        $idUserReg = $this->idUserReg;
        $sql = "INSERT INTO solicitudeseditrend (descripcion, idRendimiento, estado, fechaReg, idUserReg) 
                          VALUES ('$descripcionSolicitud', '$idRendimiento', '1', NOW(), '$idUserReg')";
        return $this->ejecutarQuery($sql, "registrar Solicitud de Edición Rendimiento");
    }

    public function actualizarSolicRendi($idRendimiento)
    {
        $sql = "UPDATE rendimientos SET envioSolicitud='1' WHERE id='$idRendimiento'";
        return $this->ejecutarQuery($sql, "actualizar Solicitud de Edición");
    }

    public function getSolicitudesPendientes($filtradoSemana = "1=1", $filtradoProceso = "1=1", $filtradoMateria = "1=1", $filtradoPrograma = "1=1")
    {
        $sql = "SELECT DATE_FORMAT(s.fechaReg, '%d/%m/%Y') AS f_fechaReg, r.loteTemola, r.semanaProduccion,
        cp.nombre AS n_programa, cpr.nombre AS n_proceso, cm.nombre AS n_materia, s.descripcion, s.estado, s.id,
        CONCAT(su.nombre, ' ', su.apellidos) AS n_empleadoResp, s.idRendimiento
        FROM solicitudeseditrend s
        INNER JOIN rendimientos r ON s.idRendimiento=r.id
        INNER JOIN catprogramas cp ON r.idCatPrograma=cp.id
        INNER JOIN catprocesos cpr ON r.idCatProceso=cpr.id
        INNER JOIN catmateriasprimas cm ON r.idCatMateriaPrima=cm.id
        INNER JOIN segusuarios su ON s.idUserReg=su.id
        WHERE s.estado='1' AND $filtradoSemana AND $filtradoProceso AND $filtradoMateria AND $filtradoPrograma";
        return $this->ejecutarQuery($sql, "consulta de peticiones de edición pendientes", true);
    }

    public function aceptarSolicitud($idSolicitud)
    {
        $idUserReg = $this->idUserReg;
        $sql = "UPDATE solicitudeseditrend s 
        INNER JOIN rendimientos r ON s.idRendimiento=r.id
        SET s.estado='2', r.envioSolicitud='2', s.idUserValida='$idUserReg', s.fechaValida=NOW()
        WHERE s.id='$idSolicitud'";
        return $this->ejecutarQuery($sql, "aceptar Solicitud de Edición");
    }

    public function rechazarSolicitud($idSolicitud)
    {
        $idUserReg = $this->idUserReg;
        $sql = "UPDATE solicitudeseditrend s 
        INNER JOIN rendimientos r ON s.idRendimiento=r.id
        SET s.estado='0', r.envioSolicitud='0', s.idUserValida='$idUserReg', s.fechaValida=NOW()
        WHERE s.id='$idSolicitud'";
        return $this->ejecutarQuery($sql, "rechazar Solicitud de Edición");
    }

    public function validaCambioDePzas($idRendimiento)
    {
        $sql = "SELECT r.* FROM rendimientos r
        LEFT JOIN detventas dv ON dv.idRendimiento = r.id
        LEFT JOIN ventas v ON dv.idVenta=v.id AND v.estado='2'
        LEFT JOIN traspasos trSal ON trSal.idRendSalida=r.id AND trSal.estado='2'
        LEFT JOIN traspasos trEnt ON trEnt.idRendEntrada=r.id AND trEnt.estado='2'
        WHERE r.id='$idRendimiento' AND v.id IS NULL AND trSal.id IS NULL AND trEnt.id IS NULL ";
        return $this->ejecutarQuery($sql, "valida cambios de piezas en inventario", true);
    }

    public function abrirEdicion($id)
    {
        $idUserReg = $this->idUserReg;
        $sql = "UPDATE rendimientos r 
                SET r.estado='3', r.idUserRend='$idUserReg'
                WHERE r.id='$id'";
        return $this->ejecutarQuery($sql, "abrir Edición de Rendimiento");
    }


    public function limpiarInventarios($id)
    {
        $sql = "DELETE ie, irech, irec, sr FROM inventarioempacado ie 
        LEFT JOIN inventariorechazado irech ON irech.idRendimiento=ie.idRendimiento 
        LEFT JOIN inventariorecuperado irec ON irec.idRendimiento=ie.idRendimiento 
        INNER JOIN sublotesrecuperados sr ON sr.idRendimiento= ie.idRendimiento
        WHERE ie.idRendimiento='$id'";
        return $this->ejecutarQuery($sql, "limpieza de Inventario y Lotes");
    }
    public function getHistorial(
        $filtradoSemana = "1=1",
        $filtradoProceso = "1=1",
        $filtradoPrograma = "1=1",
        $filtradoMateria = "1=1",
        $filtradoEstado = "1=1"
    ) {
        $sql = "SELECT DATE_FORMAT(s.fechaValida,'%d/%m/%Y') AS f_fechaValida, 
        s.descripcion, DATE_FORMAT(s.fechaReg,'%d/%m/%Y') AS f_fechaReg,
        r.loteTemola, CONCAT(ur.nombre, ' ', ur.apellidos) AS n_empleadoEnvio,
        CONCAT(uv.nombre, ' ', uv.apellidos) AS n_empleadoValida,
        pg.nombre AS n_programa, pr.nombre AS n_proceso, mp.nombre AS n_materia,
        pr.codigo AS c_proceso, r.semanaProduccion, s.estado
        FROM solicitudeseditrend s
        INNER JOIN rendimientos r ON r.id=s.idRendimiento
        INNER JOIN segusuarios  ur ON s.idUserReg=ur.id
        INNER JOIN segusuarios  uv ON s.idUserValida=uv.id
        INNER JOIN catprocesos pr ON r.idCatProceso=pr.id
        INNER JOIN catprogramas pg ON r.idCatPrograma=pg.id
        INNER JOIN catmateriasprimas mp ON r.idCatMateriaPrima=mp.id
        WHERE s.estado!='1' AND $filtradoSemana AND $filtradoProceso AND $filtradoPrograma AND $filtradoMateria
        AND $filtradoEstado
        ORDER BY s.fechaValida DESC";
        return $this->ejecutarQuery($sql, "valida cambios de piezas en inventario", true);
    }

    public function getAllSolicitudes()
    {
        $sql = "SELECT s.id, s.descripcion, CONCAT(u.nombre,' ',u.apellidos) AS n_empleado, '1' AS tipo,
                        DATE_FORMAT(s.fechaReg, '%d/%m/%Y') AS f_fechaReg
        FROM solicitudeseditrend s 
        INNER JOIN segusuarios u ON s.idUserReg=u.id
        WHERE s.estado='1'
        UNION
        SELECT e.id, e.descripcion,  CONCAT(u.nombre,' ',u.apellidos) AS n_empleado, '2' AS tipo,
               DATE_FORMAT(e.fechaReg, '%d/%m/%Y') AS f_fechaReg
        FROM excepcionesajustes e 
        INNER JOIN segusuarios u ON e.idUserReg=u.id
        WHERE e.estado='1'";
        return $this->ejecutarQuery($sql, "consulta de bandeja de solicitudes", true);
    }
}
