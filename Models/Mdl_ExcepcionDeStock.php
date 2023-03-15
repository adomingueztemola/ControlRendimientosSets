<?php
class ExcepcionDeStock extends ConexionBD
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

    public function agregarExcepcion($idRendimiento, $pzasRecuperadas, $pzasEmpacadas, $motivoExcepcion)
    {
        $idUserReg = $this->idUserReg;
        $sql = "INSERT INTO excepcionesajustes (idRendimiento, descripcion,pzasEmpacadas,pzasRecuperadas, estado, idUserReg, fechaReg) 
        VALUES ('{$idRendimiento}', '{$motivoExcepcion}','$pzasEmpacadas','$pzasRecuperadas','1','{$idUserReg}',NOW())";
        return $this->ejecutarQuery($sql, "agregar Excepción");
    }



    public function validaExcepcionAbierta($id)
    {
        $sql = "SELECT e.*, DATE_FORMAT(e.fechaReg, '%d/%m/%Y') AS f_fechaReg,
        CONCAT(su.nombre,' ',su.apellidos) AS n_empleadoReg
        FROM excepcionesajustes e
        INNER JOIN segusuarios su ON e.idUserReg=su.id
        WHERE idRendimiento='$id' AND e.estado='1'";
        return  $this->ejecutarQuery($sql, "consultar Excepción", true);
    }

    public function cancelarExcepcion($id)
    {
        $idUserReg = $this->idUserReg;
        $sql = "UPDATE excepcionesajustes e
                INNER JOIN rendimientos r ON e.idRendimiento=r.id 
                SET e.idUserValida='$idUserReg', e.fechaValida=NOW(), e.estado='0', r.excepcion='0' 
                WHERE e.id='$id'";
        return $this->ejecutarQuery($sql, "cancelar Excepción");
    }

    public function crearSubLote($idRendimiento, $idExcepcion, $pzasTotales)
    {
        $idUserReg = $this->idUserReg;

        $sql = "INSERT INTO sublotesrecuperados ( idRendimiento, loteTemola, pzasTotales, idExcepcion, porcRechazoFinalAnt, 
        porcRecuperacionFinalAnt, idUserReg, fechaReg, setsEmpacados, superLote ) 
        SELECT r.id, CONCAT( r.loteTemola, '.', (r.cantRecuperacion )) AS n_sublote, '$pzasTotales',  '$idExcepcion',r.porcFinalRechazo,
        r.porcRecuperacionFinal,'$idUserReg',NOW(), '$pzasTotales'/conf.pzasEnSets, '0'
        FROM
            rendimientos r 
            INNER JOIN config_inventarios conf ON conf.estado='1'
        WHERE
            r.id = '$idRendimiento'";
        return  $this->ejecutarQuery($sql, "crear Detalle del SubLote");
    }

    public function crearSubLoteRecuperacion24($idRendimiento, $pzasTotales)
    {
        $idUserReg = $this->idUserReg;

        $sql = "INSERT INTO sublotesrecuperados ( idRendimiento, loteTemola, pzasTotales, idExcepcion, porcRechazoFinalAnt, 
        porcRecuperacionFinalAnt, idUserReg, fechaReg, setsEmpacados, superLote ) 
        SELECT r.id, CONCAT( r.loteTemola, '.', (r.cantRecuperacion )) AS n_sublote, '$pzasTotales',  '0',r.porcFinalRechazo,
        r.porcRecuperacionFinal,'$idUserReg',NOW(), '$pzasTotales'/conf.pzasEnSets, '0'
        FROM
            rendimientos r 
            INNER JOIN config_inventarios conf ON conf.estado='1'
        WHERE
            r.id = '$idRendimiento'";
        return  $this->ejecutarQuery($sql, "crear Detalle del SubLote", false, true);
    }

    public function guardarRecuperacion24h($idRendimiento, $idSubLote, $pzasEmpacadas, $pzasRecuperadas)
    {
        $idUserReg = $this->idUserReg;
        $sql = "INSERT INTO recuperacion24h (idRendimiento, idSubLote, unidadesEmp, setsEmp, unidadesRecu, setsRecu, idUserReg, fechaReg) 
              SELECT  '$idRendimiento', '$idSubLote', '$pzasEmpacadas','$pzasEmpacadas'/conf.pzasEnSets, '$pzasRecuperadas','$pzasRecuperadas'/conf.pzasEnSets, '$idUserReg', NOW() FROM 
              config_inventarios conf WHERE conf.estado='1'";
        return  $this->ejecutarQuery($sql, "crear Histórico de Recuperación");
    }

    public function aumentarInventarioEmp($idRendimiento, $pzasEmpacadas)
    {
        $sql = "UPDATE inventarioempacado ie
        INNER JOIN config_inventarios conf ON conf.estado='1'
        INNER JOIN rendimientos r ON ie.idRendimiento=r.id
        INNER JOIN (
		SELECT	dp.idRendimiento,	AVG( p.precioUnitFactUsd ) AS costoProm 
		FROM
			detpedidos dp	INNER JOIN pedidos p ON dp.idPedido = p.id 
		WHERE dp.idRendimiento = '$idRendimiento'
		GROUP BY
			dp.idRendimiento 
		) dp ON dp.idRendimiento = r.id
        SET ie.pzasTotales=ie.pzasTotales+'$pzasEmpacadas', 
           ie.setsTotales=(ie.pzasTotales+'$pzasEmpacadas')/conf.pzasEnSets,
		   ie.rezago= (ie.pzasTotales+'$pzasEmpacadas')%conf.pzasEnSets,
           r.totalEmp= r.totalEmp+'$pzasEmpacadas', 
           r.areaWBUnidad=r.areaWB/((r.totalEmp+'$pzasEmpacadas')/conf.pzasEnSets),
	       r.costoWBUnit=(r.areaWB/((r.totalEmp+'$pzasEmpacadas')/conf.pzasEnSets))*dp.costoProm, 
           r.cantRecuperacion=r.cantRecuperacion+1
        WHERE ie.idRendimiento='$idRendimiento'";
        return  $this->ejecutarQuery($sql, "aumento de lotes empacados");
    }

    public function actualizarSuperLote($id)
    {
        $idUserReg = $this->idUserReg;
        $sql = "UPDATE rendimientos r
        INNER JOIN vw_lotestotales vw ON r.id=vw.id
        INNER JOIN excepcionesajustes e ON r.id=e.idRendimiento
        SET r.porcFinalRechazo= ((vw.setsTotalRech-(e.pzasRecuperadas/4))/r.setsCortadosTeseo )*100,
            r.porcRecuperacionFinal= (((e.pzasRecuperadas/4)+vw.setsTotalRecu)/r.setsCortadosTeseo)*100, 
            r.excepcion=0, r.cantRecuperacion=IFNULL(r.cantRecuperacion,0)+1

        WHERE e.id='$id'";
        return  $this->ejecutarQuery($sql, "actualizar Detalle del Lote");
        /* r.cantRecuperacion=r.cantRecuperacion+1, r.setsEmpacados= r.setsEmpacados+e.setsEmpacados,
            r.piezasRecuperadas= r.piezasRecuperadas+e.pzasRecuperadas, r.setsRechazados= r.setsRechazados-FLOOR(e.pzasRecuperadas/4),
            r.setsRecuperados=  r.setsRecuperados+FLOOR(e.pzasRecuperadas/4)*/
    }

    public function aceptarExcepcion($id)
    {
        $idUserReg = $this->idUserReg;
        $sql = "UPDATE excepcionesajustes e
                INNER JOIN rendimientos r ON e.idRendimiento=r.id 
                SET e.idUserValida='$idUserReg', e.fechaValida=NOW(), e.estado='2', r.excepcion='0' 
                WHERE e.id='$id'";
        return $this->ejecutarQuery($sql, "aceptar Excepción");
    }

    public function agregarSuperLote($idRendimiento, $pzasEmpacadas)
    {
        $sql = "UPDATE sublotesrecuperados SET
                pzasTotales= pzasTotales+'$pzasEmpacadas'
                WHERE idRendimiento='$idRendimiento' AND superLote='1'";
        return $this->ejecutarQuery($sql, "agregar piezas al SuperLote");
    }


    public function getPeticionesDeExcepciones(
        $filtradoSemana = "1=1",
        $filtradoProceso = "1=1",
        $filtradoMateria = "1=1",
        $filtradoPrograma = "1=1"
    ) {
        $sql = "SELECT
        e.*,
        DATE_FORMAT( e.fechaReg, '%d/%m/%Y' ) AS f_fechaReg,
        r.loteTemola,
        vw.totalRecu, vw.totalEmp,
        CONCAT( su.nombre, ' ', su.apellidos ) AS n_empleadoReg,
        r.semanaProduccion,
        pr.nombre AS n_proceso,
        pr.codigo AS c_proceso,

        pg.nombre AS n_programa,
        mp.nombre AS n_materia
        FROM
            excepcionesajustes e
            INNER JOIN segusuarios su ON e.idUserReg = su.id
            INNER JOIN rendimientos r ON e.idRendimiento = r.id 
            INNER JOIN catprocesos pr ON r.idCatProceso=pr.id
            INNER JOIN catprogramas pg ON r.idCatPrograma=pg.id
            INNER JOIN catmateriasprimas mp ON r.idCatMateriaPrima=mp.id

            INNER JOIN vw_inventariolotes vw ON vw.id = r.id 
        WHERE
            e.estado = '1' AND $filtradoSemana AND $filtradoProceso AND $filtradoMateria AND $filtradoPrograma";
        return  $this->ejecutarQuery($sql, "consultar Peticiones de Excepción", true);
    }

    public function getDetExcepcion($id)
    {
        $sql = "SELECT
        e.*,
        r.setsCortadosTeseo,
        CONCAT(su.nombre, ' ', su.apellidos) AS n_empleadoReg,
        DATE_FORMAT(e.fechaReg, '%d/%m/%Y') AS f_fechaReg,
				r.pzasCortadasTeseo,
               
				inv.totalEmp,
				inv.totalRech, 
				inv.totalRecu, 
				inv.setsTotalEmp,
				inv.setsTotalRech,
				inv.setsTotalRecu,
                @totalExcRecu:=(inv.totalRecu+e.pzasRecuperadas) AS totalExcRecu,
                @setsExcRecu:=(e.pzasRecuperadas/conf.pzasEnSets) AS setsExcRecu,
                @setsExcConRecu:=(inv.setsTotalRecu+@setsExcRecu) AS setsExcConRecu,
                @totalExcRech:=inv.totalRech-e.pzasRecuperadas AS totalExcRech,
                @totalExcEmp:=inv.totalEmp+e.pzasEmpacadas AS totalExcEmp,
                @totalExcEmp/conf.pzasEnSets AS setsExcEmp,
                (@setsExcConRecu/ r.setsCortadosTeseo)*100 AS porcRecuperacionFinal,
                (e.pzasRecuperadas/ r.setsCortadosTeseo)*100 AS porcRecuperacion,
                (@totalExcRech/ r.setsCortadosTeseo)*100 AS porcFinalRechazo
                
                
            FROM
                excepcionesajustes e
                INNER JOIN segusuarios su ON e.idUserReg = su.id
                INNER JOIN rendimientos r ON  r.id= e.idRendimiento 
               
                INNER JOIN vw_inventariolotes inv ON inv.id = e.idRendimiento 
                INNER JOIN config_inventarios conf ON conf.estado='1'
            WHERE
                e.estado = '1' 
                AND e.id = '$id'";
        return  $this->ejecutarQuery($sql, "consultar Detalle de Excepción", true);
    }

    public function getDatosSubLote($id)
    {
        $sql = "SELECT CONCAT(r.loteTemola,'.',COUNT(s.id)+1) AS n_sublote, 
        e.pzasEmpacadas AS pzasTotales, 
        FLOOR(e.pzasEmpacadas/4) AS setsTotales, e.pzasEmpacadas%4 AS pzasSinSet
        FROM excepcionesajustes e
        LEFT JOIN sublotesrecuperados s ON s.idRendimiento= e.idRendimiento
        INNER JOIN rendimientos r ON e.idRendimiento=r.id
        WHERE e.id='$id'
        GROUP BY e.idRendimiento";
        return  $this->ejecutarQuery($sql, "consultar Detalle del SubLote", true);
    }

    public function getDetRendimientos($id)
    {
        $sql = "SELECT
        r.id, r.loteTemola, r.semanaProduccion,
        DATE_FORMAT( r.fechaEngrase, '%d/%m/%Y' ) AS f_fechaEngrase,
        DATE_FORMAT( r.fechaEmpaque, '%d/%m/%Y' ) f_fechaEmpaque,
        pr.nombre AS n_proceso,
        pg.nombre AS n_programa,
        mp.nombre AS n_materia,
        DATE_FORMAT( r.fechaReg, '%d/%m/%Y %H:%m' ) AS f_fechaReg,
        i.totalEmp,
        i.totalRech,
        i.totalRecu,
        i.setsTotalRecu,
        i.setsTotalRech,
        i.setsTotalEmp,
        i.rzgoEmp, i.rzgoRech, i.rzgoRecu
        FROM
            vw_inventariolotes i
            INNER JOIN rendimientos r ON r.id = i.id
            INNER JOIN catprocesos pr ON r.idCatProceso = pr.id
            INNER JOIN catprogramas pg ON r.idCatPrograma = pg.id
            INNER JOIN catmateriasprimas mp ON r.idCatMateriaPrima = mp.id 
        WHERE
        r.id = '$id'";
        return  $this->ejecutarQuery($sql, "consultar Detallado de Rendimiento", true);
    }

    public function getInventarioRecu($idRendimiento)
    {
        $sql = "SELECT * FROM inventariorecuperado ir
        WHERE idRendimiento='$idRendimiento'";
        return  $this->ejecutarQuery($sql, "consultar Inventario de Recuperación de Lote", true);
    }

    public function getTraspasosPend($idRendimiento)
    {
        $sql = "SELECT t.* FROM traspasos t
        WHERE t.idRendSalida='$idRendimiento' AND t.estado='1'";
        return  $this->ejecutarQuery($sql, "consultar Traspasos de Recuperación de Lote", true);
    }

    public function disminucionInventRech($idRendimiento, $cantidad)
    {
        $sql = "UPDATE inventariorechazado ir
        	INNER JOIN config_inventarios conf ON conf.estado='1'
        SET 
            ir.pzasTotales=ir.pzasTotales-'$cantidad', 
            ir.setsTotales= (ir.pzasTotales-'$cantidad')/conf.pzasEnSets,
			ir.rezago= (ir.pzasTotales-'$cantidad')%conf.pzasEnSets
        WHERE ir.idRendimiento='$idRendimiento'";
        return  $this->ejecutarQuery($sql, "ejecutar disminución de piezas de rechazo");
    }

    public function agregarInvEmpTrasp($idRendimiento)
    {
        $sql = "UPDATE inventarioempacado ie
        INNER JOIN traspasos tr ON tr.idRendEntrada= ie.idRendimiento
        INNER JOIN config_inventarios conf ON conf.estado='1'
        INNER JOIN rendimientos r ON tr.idRendEntrada = r.id

        SET ie.pzasTotales=ie.pzasTotales+tr.cantidad, tr.estado='2', 
           ie.setsTotales= (ie.pzasTotales+tr.cantidad)/conf.pzasEnSets,
		   ie.rezago= (ie.pzasTotales+tr.cantidad)%conf.pzasEnSets, r.totalEmp= r.totalEmp+tr.cantidad
        WHERE tr.idRendSalida='$idRendimiento' AND tr.estado='1'";
        return  $this->ejecutarQuery($sql, "ejecutar aumento de piezas de empaquetadas a lote de traspaso");
    }

    public function actualizarSubLotesTraspEntr($idRendimiento)
    {
        $sql = "UPDATE  traspasos tr 
        INNER JOIN sublotesrecuperados s_en ON tr.idRendEntrada= s_en.idRendimiento AND s_en.superLote='1' 
        INNER JOIN config_inventarios conf ON conf.estado='1'
        SET s_en.pzasTotales= s_en.pzasTotales+tr.cantidad, s_en.setsEmpacados=(s_en.pzasTotales+tr.cantidad)/conf.pzasEnSets, tr.cantFinalAlm=s_en.pzasTotales+tr.cantidad
        WHERE tr.idRendSalida='$idRendimiento' AND tr.estado='1'";
        return  $this->ejecutarQuery($sql, "actualizar lotes de traspaso de entrada");
    }

    public function actualizarSubLotesTraspSal($idRendimiento)
    {
        $sql = "UPDATE  traspasos tr 
        INNER JOIN sublotesrecuperados s_sal ON tr.idRendSalida= s_sal.idRendimiento AND s_sal.superLote='1' 
        INNER JOIN config_inventarios conf ON conf.estado='1'
        SET s_sal.pzasTotales= s_sal.pzasTotales-tr.cantidad, s_sal.setsEmpacados=(s_sal.pzasTotales-tr.cantidad)/conf.pzasEnSets, tr.cantFinalAlm=s_sal.pzasTotales-tr.cantidad
        WHERE tr.idRendSalida='$idRendimiento'";
        return  $this->ejecutarQuery($sql, "actualizar lotes de traspaso de salida");
    }
    public function actualizarPorcRecuperacion($idRendimiento, $pzasTotales, $pzasAnexadas)
    {
        $sql = "UPDATE rendimientos r
        SET r.porcRecuperacionFinal= ('$pzasTotales'/r.pzasCortadasTeseo)*100,
            r.totalRecu= r.totalRecu+'$pzasAnexadas'
        WHERE r.id='$idRendimiento'";
        return  $this->ejecutarQuery($sql, "ejecutar actualización de rendimiento en recuperación");
    }
    public function actualizarInventarioRecu($idRendimiento, $pzasParaInvRecuperacion)
    {
        $sql = "UPDATE inventariorecuperado ir
        INNER JOIN config_inventarios conf ON conf.estado='1'
        SET ir.pzasTotales='$pzasParaInvRecuperacion', 
           ir.setsTotales='$pzasParaInvRecuperacion'/conf.pzasEnSets,
		   ir.rezago= '$pzasParaInvRecuperacion'%conf.pzasEnSets
        WHERE ir.idRendimiento='$idRendimiento'";
        return  $this->ejecutarQuery($sql, "ejecutar actualización de piezas en recuperación");
    }

    public function getHistorial(
        $filtradoSemana = "1=1",
        $filtradoProceso = "1=1",
        $filtradoPrograma = "1=1",
        $filtradoMateria = "1=1",
        $filtradoEstado = "1=1"
    ) {
        $sql = "SELECT
        e.*,
        DATE_FORMAT( e.fechaReg, '%d/%m/%Y' ) AS f_fechaReg,
        r.loteTemola,
        i.setsTotalEmp AS setsEmpacadosAct,
        i.totalRecu AS piezasRecuperadasAct,
        CONCAT( su.nombre, ' ', su.apellidos ) AS n_empleadoReg,
        r.semanaProduccion, CONCAT( sv.nombre, ' ', sv.apellidos ) AS n_empleadoValid,
        DATE_FORMAT( e.fechaValida, '%d/%m/%Y' ) AS f_fechaValida,
        pr.nombre AS n_proceso,
        pr.codigo AS c_proceso,
        pg.nombre AS n_programa,
        mp.nombre AS n_materia 
        FROM
            excepcionesajustes e
            INNER JOIN segusuarios su ON e.idUserReg = su.id
            INNER JOIN segusuarios sv ON e.idUserValida = sv.id
            INNER JOIN vw_inventariolotes i ON e.idRendimiento = i.id 
						INNER JOIN rendimientos r ON i.id=r.id
            INNER JOIN catprocesos pr ON r.idCatProceso=pr.id
            INNER JOIN catprogramas pg ON r.idCatPrograma=pg.id
            INNER JOIN catmateriasprimas mp ON r.idCatMateriaPrima=mp.id
        WHERE
        e.estado != '1' AND $filtradoSemana AND $filtradoProceso AND $filtradoPrograma AND $filtradoMateria
        AND $filtradoEstado
        ORDER BY e.fechaValida DESC";
        return  $this->ejecutarQuery($sql, "consultar Historial de Rendimiento", true);
    }

    public function detalleLoteXExcepcion($id)
    {
        $sql = "SELECT sub.*, 
        FLOOR (sub.pzasTotales/conf.pzasEnSets) AS setsRecuperados, 
        (sub.pzasTotales%conf.pzasEnSets) AS pzasSinSet, 

        DATE_FORMAT(sub.fechaReg, '%d/%m/%Y %H:%m') AS f_fechaReg 
        FROM sublotesrecuperados sub
        INNER JOIN config_inventarios conf ON conf.estado='1'
        INNER JOIN excepcionesajustes e ON sub.idExcepcion= e.id
        WHERE e.id='$id'";
        return  $this->ejecutarQuery($sql, "consultar Historial de Rendimiento", true);
    }
    /***************************************/
    /*ACTUALIZA CUEROS DE LAS VENTAS PARA LIBERAR PIEZAS CON CUEROS*/
    /***************************************/
    public function actualizaCuerosVentas($idRendimiento)
    {
        $sql = "UPDATE ventas v 
        INNER JOIN detventas dv ON dv.idVenta=v.id
        INNER JOIN rendimientos r ON r.id= dv.idRendimiento
        INNER JOIN config_inventarios ci ON ci.estado='1'
        SET
        dv.1s= ROUND((r.1s/(r.totalEmp/ci.pzasEnSets))*(dv.sets)),
        dv.2s= ROUND((r.2s/(r.totalEmp/ci.pzasEnSets))*(dv.sets)),
        dv.3s= ROUND((r.3s/(r.totalEmp/ci.pzasEnSets))*(dv.sets)),
        dv.4s= ROUND((r.4s/(r.totalEmp/ci.pzasEnSets))*(dv.sets)),
        dv._20=ROUND((r._20/(r.totalEmp/ci.pzasEnSets))*(dv.sets)),
        dv.total_s=ROUND((r._20/(r.totalEmp/ci.pzasEnSets))*(dv.sets))+ROUND((r.4s/(r.totalEmp/ci.pzasEnSets))*(dv.sets))+
        ROUND((r.3s/(r.totalEmp/ci.pzasEnSets))*(dv.sets))+ROUND((r.2s/(r.totalEmp/ci.pzasEnSets))*(dv.sets))+
        ROUND((r.1s/(r.totalEmp/ci.pzasEnSets))*(dv.sets))
        WHERE dv.idRendimiento='{$idRendimiento}'";
        return $this->ejecutarQuery($sql, "Liberar Piezas de Cueros de Ventas");
    }
}
