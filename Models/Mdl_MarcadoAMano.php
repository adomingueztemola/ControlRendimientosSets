<?php
class MarcadoAMano extends ConexionBD
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
    /************************************************************************/
    /* LISTA DE LOTES EN SEGUIMIENTO */
    /************************************************************************/

    public function getLotesSeguimiento($filtradoFecha = "1=1", $filtradoProceso = "1=1", $filtradoMateria = "1=1", $filtradoPrograma = "1=1")
    {
        $sql = "SELECT l.id, cp.nombre AS n_programa, 
        l.nombre AS n_lote, 
        DATE_FORMAT(l.fecha,'%d/%m/%Y') AS f_fecha
        FROM lotesteseo l
        INNER JOIN catprogramas cp ON l.idCatPrograma=cp.id
        WHERE l.estado='1'
        ORDER BY l.fecha DESC";
        return  $this->ejecutarQuery($sql, "consultar Lotes de Seguimiento", true);
    }

    /************************************************************************/
    /* DETALLADO DEL CORTE DE PZAS DE MARCADO MANUAL */
    /************************************************************************/
    public function getDetMarcadoXLote($id)
    {
        $sql = "SELECT l.id, cp.nombre AS n_programa,  l.pzasTotales, l.area, l.yield,
        l.areaCrust, l.fecha,
        l.nombre AS n_lote, l.idCatPrograma, 
        DATE_FORMAT(l.fecha,'%d/%m/%Y') AS f_fecha, l.areaCrustDecremento,
        CONCAT(u.nombre, ' ', u.apellidos) AS n_empleado,  DATE_FORMAT(l.fechaReg,'%d/%m/%Y') AS f_fechaReg,
        l.porcDecremento
        FROM lotesteseo l
        INNER JOIN catprogramas cp ON l.idCatPrograma=cp.id
        INNER JOIN segusuarios u ON l.idUserReg=u.id
        WHERE l.id='$id'";
        return  $this->ejecutarQuery($sql, "consultar Marcado del Lote", true);
    }
    /************************************************************************/
    /* PROGRAMAS REGISTRADOS CON VOLANTE Y AREA */
    /************************************************************************/
    public function getProgramaConVolante()
    {
        $sql = "SELECT DISTINCT cp.* FROM catprogramas cp
        INNER JOIN areaxpzasvolt apv ON apv.idCatPrograma = cp.id
        WHERE cp.estado='1'";
        return  $this->ejecutarQuery($sql, "consultar Marcado del Lote", true);
    }
    /************************************************************************/
    /* PROGRAMAS REGISTRADOS CON VOLANTE Y AREA */
    /************************************************************************/
    public function getMetricasConteoTeseo($idLote)
    {
        $sql = "SELECT ct.*, cpv.nombre FROM conteopzasteseo ct
        INNER JOIN areaxpzasvolt apv ON ct.idAreaPzaVol=apv.id
        INNER JOIN catpzasvolante cpv ON apv.idCatPzaVolt=cpv.id
        WHERE ct.idLoteTeseo='$idLote'
        ORDER BY cpv.orden ";
        return  $this->ejecutarQuery($sql, "consultar Marcado del Lote", true);
    }
    public function getRecuperacion($idLote)
    {
        $sql = "SELECT k.id, k.cantidad, k.idLote, cpv.nombre AS n_pzasVolante FROM kardexconteoteseo k
        INNER JOIN conteopzasteseo ct ON k.idConteoTeseo=ct.id
        INNER JOIN areaxpzasvolt apv ON ct.idAreaPzaVol=apv.id
        INNER JOIN catpzasvolante cpv ON apv.idCatPzaVolt=cpv.id
        WHERE k.idLote='$idLote' AND k.recuperacion='1'
        ORDER BY cpv.orden ";
        return  $this->ejecutarQuery($sql, "consultar Reuperación del Lote", true);
    }
    /************************************************************************/
    /* VER LOTES CERRADOS  */
    /************************************************************************/
    public function getLotesCerrados($filtradoFecha = '1=1', $filtradoPrograma = '1=1')
    {
        $sql = "SELECT l.id, l.nombre AS n_lote, DATE_FORMAT(l.fecha,'%d/%m/%Y') AS f_fecha,
        l.pzasTotales, l.area, l.areaCrust, l.areaCrustDecremento, l.yield,
        cp.nombre AS n_programa,  CONCAT(u.nombre, ' ', u.apellidos) AS n_empleado,  l.porcDecremento
        FROM lotesteseo l
        INNER JOIN catprogramas cp ON l.idCatPrograma=cp.id
        INNER JOIN segusuarios u ON l.idUserReg=u.id

        WHERE l.estado='2' AND $filtradoFecha AND $filtradoPrograma
        ORDER BY l.fecha DESC";
        return  $this->ejecutarQuery($sql, "consultar Lotes", true);
    }
    /************************************************************************/
    /* VER LOTES CERRADOS  */
    /************************************************************************/
    public function getLotesAll($filtradoFecha = '1=1', $filtradoPrograma = '1=1')
    {
        $sql = "SELECT l.id, l.nombre AS n_lote, DATE_FORMAT(l.fecha,'%d/%m/%Y') AS f_fecha,
        l.pzasTotales, l.area, l.areaCrust, l.areaCrustDecremento, l.yield,
        cp.nombre AS n_programa,  CONCAT(u.nombre, ' ', u.apellidos) AS n_empleado, l.estado, l.porcDecremento
        FROM lotesteseo l
        INNER JOIN catprogramas cp ON l.idCatPrograma=cp.id
        INNER JOIN segusuarios u ON l.idUserReg=u.id

        WHERE  $filtradoFecha AND $filtradoPrograma
        ORDER BY l.fecha DESC";
        return  $this->ejecutarQuery($sql, "consultar Lotes", true);
    }
    /************************************************************************/
    /* AGREGAR LOTE  */
    /************************************************************************/
    public function agregarLote($nLote, $programa, $fecha, $areaCrust)
    {
        $idUserReg = $this->idUserReg;
        $sql = "INSERT INTO lotesteseo (idCatPrograma, nombre, fecha, fechaReg, idUserReg, estado, pzasTotales, yield, area, areaCrust, areaCrustDecremento, cantRecuperado, porcDecremento) 
                                       VALUES ('$programa', '$nLote', '$fecha', NOW(), '$idUserReg', '1', '0', '0', '0', '$areaCrust', '$areaCrust'-('$areaCrust'*0.0584), 0, '5.84')";
        return $this->ejecutarQuery($sql, "agregar Lote", false, true);
    }
    /************************************************************************/
    /* DETALLADO DEL CORTE DE PZAS DE MARCADO MANUAL */
    /************************************************************************/
    public function crearContadores($idLote)
    {
        $idUserReg = $this->idUserReg;
        $sql = "INSERT INTO conteopzasteseo(idLoteTeseo, idAreaPzaVol, total, preliminar, fechaReg, idUserReg, estado, areaXPza, areaUnit, totalGlobal, cantRecuperado) 
        SELECT l.id, apv.id  AS idAreaPzaVol, '0', '0', NOW(), '$idUserReg', '1', '0', apv.area, '0', '0'  FROM lotesteseo l
            INNER JOIN areaxpzasvolt apv ON apv.idCatPrograma= l.idCatPrograma
            WHERE l.id='$idLote'";
        return $this->ejecutarQuery($sql, "agregar Contador de Lote");
    }
    /************************************************************************/
    /* DETALLADO DEL CORTE DE PZAS DE MARCADO MANUAL */
    /************************************************************************/
    public function agregarKardexCorte($lote, $value)
    {
        $idUserReg = $this->idUserReg;
        $sql = "INSERT INTO kardexconteoteseo (idLote, idConteoTeseo, cantidad, preliminarAct, idUserReg, fechaReg, recuperacion, decremento)
                SELECT idLoteTeseo, id, '$value', preliminar, '$idUserReg', NOW(), '0','0' FROM  conteopzasteseo WHERE id='$lote'";
        return $this->ejecutarQuery($sql, "sumar piezas Marcadas al  Contador de Lote");
    }
    public function agregarDecrKardexCorte($lote, $value)
    {
        $idUserReg = $this->idUserReg;
        $sql = "INSERT INTO kardexconteoteseo (idLote, idConteoTeseo, cantidad, preliminarAct, idUserReg, fechaReg, decremento, recuperacion)
                SELECT idLoteTeseo, id, '$value', preliminar, '$idUserReg', NOW(), '1', '0' FROM  conteopzasteseo WHERE id='$lote'";
        return $this->ejecutarQuery($sql, "sumar piezas Marcadas al  Contador de Lote");
    }
    public function agregarKardexRecuperacion($lote, $value)
    {
        $idUserReg = $this->idUserReg;
        $sql = "INSERT INTO kardexconteoteseo (idLote, idConteoTeseo, cantidad, preliminarAct, idUserReg, fechaReg, recuperacion, decremento)
                SELECT idLoteTeseo, id, '$value', preliminar, '$idUserReg', NOW(), '1', '0' FROM  conteopzasteseo WHERE id='$lote'";
        return $this->ejecutarQuery($sql, "sumar piezas Marcadas al  Contador de Lote");
    }
    public function editarContadorLote($id, $campo, $value)
    {
        $idUserReg = $this->idUserReg;
        $sql = "UPDATE conteopzasteseo SET 
                $campo='$value', totalGlobal= totalGlobal+'$value', fechaReg=NOW(), idUserReg='$idUserReg'
                WHERE id='$id'";
        return $this->ejecutarQuery($sql, "editar Contador de Lote");
    }

    public function cerrarMarcado($lote)
    {
        $idUserReg = $this->idUserReg;
        $sql = "UPDATE lotesteseo SET 
                estado='2', fechaReg=NOW(), idUserReg='$idUserReg'
                WHERE id='$lote'";
        return $this->ejecutarQuery($sql, "actualizar Cierre de Marcado de Lote");
    }

    public function agregarPiezasMarcadas($lote, $value)
    {
        $idUserReg = $this->idUserReg;
        $sql = "UPDATE conteopzasteseo c 
               INNER JOIN lotesteseo l ON l.id = c.idLoteTeseo
               INNER JOIN areaxpzasvolt apv ON c.idAreaPzaVol=apv.id
               SET 
                l.pzasTotales= l.pzasTotales+'$value', c.areaXPza=(c.preliminar+'$value')*apv.area,
                c.total=c.total-'$value',
                c.preliminar=c.preliminar+'$value', c.fechaReg=NOW(), c.idUserReg='$idUserReg'
                WHERE c.id='$lote'";
        return $this->ejecutarQuery($sql, "editar Preliminar de Lote");
    }

    public function agregarPiezasRecuperacion($lote, $value)
    {
        $idUserReg = $this->idUserReg;
        $sql = "UPDATE conteopzasteseo c 
               INNER JOIN lotesteseo l ON l.id = c.idLoteTeseo
               INNER JOIN areaxpzasvolt apv ON c.idAreaPzaVol=apv.id
               SET 
                l.cantRecuperado= l.cantRecuperado+'$value', 
                c.cantRecuperado=c.cantRecuperado+'$value',
                c.total=c.total-'$value',
                c.preliminar=c.preliminar+'$value', c.fechaReg=NOW(), c.idUserReg='$idUserReg'
                WHERE c.id='$lote'";
        return $this->ejecutarQuery($sql, "editar Recuperación de Lote");
    }
    public function quitarPiezasMarcadas($lote, $value)
    {
        $idUserReg = $this->idUserReg;
        $sql = "UPDATE conteopzasteseo c 
               INNER JOIN lotesteseo l ON l.id = c.idLoteTeseo
               INNER JOIN areaxpzasvolt apv ON c.idAreaPzaVol=apv.id
               SET 
                l.pzasTotales= l.pzasTotales-'$value', c.areaXPza=(c.preliminar-'$value')*apv.area,
                c.total=c.total+'$value',
                c.preliminar=c.preliminar-'$value', c.fechaReg=NOW(), c.idUserReg='$idUserReg'
                WHERE c.id='$lote'";
        return $this->ejecutarQuery($sql, "editar Preliminar de Lote");
    }
    public function actualizarAreaYieldLote($lote)
    {
        $idUserReg = $this->idUserReg;
        $sql = "UPDATE lotesteseo  l
        INNER JOIN (SELECT idLoteTeseo, SUM(areaXPza) AS totalArea
        FROM conteopzasteseo WHERE idLoteTeseo='$lote') c ON l.id = c.idLoteTeseo
        SET l.area =c.totalArea,  l.yield= (c.totalArea/l.areaCrustDecremento)*100,
        l.fechaReg=NOW(), l.idUserReg='$idUserReg'
         WHERE l.id='$lote'";
        return $this->ejecutarQuery($sql, "actualizar el Área de Lote");
    }

    public function agregarCrust($idLote, $areaCrust)
    {
        $idUserReg = $this->idUserReg;

        $sql = "UPDATE lotesteseo  l
        SET l.areaCrustDecremento= '$areaCrust'-('$areaCrust'*( l.porcDecremento/100)), l.areaCrust= '$areaCrust',
        l.yield= (l.area/('$areaCrust'-('$areaCrust'*( l.porcDecremento/100))))*100,
        l.fechaReg=NOW(), l.idUserReg='$idUserReg'
         WHERE l.id='$idLote'";
        return $this->ejecutarQuery($sql, "actualizar el Área de Crust");
    }

    public function editaLote($idLote, $nLote, $programa, $fecha, $porcDecrement)
    {
        $idUserReg = $this->idUserReg;

        $sql = "UPDATE lotesteseo  l
        SET l.nombre= '$nLote', l.idCatPrograma= '$programa',
        l.fecha= '$fecha', l.porcDecremento= '$porcDecrement',
        l.fechaReg=NOW(), l.idUserReg='$idUserReg'
         WHERE l.id='$idLote'";
        return $this->ejecutarQuery($sql, "actualizar el Datos del Lote");
    }

    public function actualizarPorcentaje($idLote, $porcDecrement)
    {
        $idUserReg = $this->idUserReg;
        $decimal_porcent = $porcDecrement / 100;
        $sql = "UPDATE lotesteseo  l
        SET l.areaCrustDecremento= areaCrust-(areaCrust*{$decimal_porcent}), l.porcDecremento= '$porcDecrement',
        l.yield= (l.area/(areaCrust-(areaCrust*{$decimal_porcent})))*100,
        l.fechaReg=NOW(), l.idUserReg='$idUserReg'
         WHERE l.id='$idLote'";
        return $this->ejecutarQuery($sql, "actualizar el Decremento de la Área Crust");
    }


    public function getKardexLote($idLote)
    {
        $sql = "SELECT k.id, k.cantidad, cpv.nombre AS n_volante,	
        DATE_FORMAT(k.fechaReg,'%d/%m/%Y %H:%i') AS f_fechaReg
        FROM kardexconteoteseo k
        INNER JOIN conteopzasteseo c ON k.idConteoTeseo=c.id
        INNER JOIN areaxpzasvolt apv ON c.idAreaPzaVol=apv.id
        INNER JOIN catpzasvolante cpv ON apv.idCatPzaVolt=cpv.id
        WHERE k.idLote='$idLote' AND decremento!='1' AND recuperacion!='1' ORDER BY k.fechaReg DESC";
        return  $this->ejecutarQuery($sql, "consultar Kardex Lote", true);
    }
    public function getDecrementoLote($idLote)
    {
        $sql = "SELECT k.id, k.cantidad, cpv.nombre AS n_volante,	
        DATE_FORMAT(k.fechaReg,'%d/%m/%Y %H:%i') AS f_fechaReg
        FROM kardexconteoteseo k
        INNER JOIN conteopzasteseo c ON k.idConteoTeseo=c.id
        INNER JOIN areaxpzasvolt apv ON c.idAreaPzaVol=apv.id
        INNER JOIN catpzasvolante cpv ON apv.idCatPzaVolt=cpv.id
        WHERE k.idLote='$idLote' AND decremento='1' AND recuperacion!='1' ORDER BY k.fechaReg DESC";
        return  $this->ejecutarQuery($sql, "consultar Kardex de Decremento de Lote", true);
    }


    public function getLotesMarcadoXMes($anio)
    {
        $sql = "SELECT COUNT(l.id) AS total, DATE_FORMAT(l.fecha,'%m-%Y') AS mesAnio FROM lotesteseo l
                WHERE YEAR(l.fecha)='$anio' AND l.estado='2'
                GROUP BY DATE_FORMAT(l.fecha,'%m-%Y') ";
        return  $this->ejecutarQuery($sql, "consultar lotes por mes", true);
    }
}
