DELIMITER $$
CREATE PROCEDURE calcularRendimientoFase2 (
		IN id_Rendimiento INT ( 11 ), IN idUserReg INT ( 11 ),
		 IN cambioPzas INT ( 11 )
		) BEGIN

	UPDATE rendimientos r 
	INNER JOIN (	SELECT r.id,
		/*@total_s:=r.total_s-IFNULL(r.piezasRechazadas,0),*/
		@diferenciaArea := r.areaWB - r.areaProveedorLote,
		@perdidaAreaWBCrust :=((
				r.areaCrust - r.areaWB 
				)/ r.areaWB 
		)* 100,
		@promedioAreaWB := r.areaWB / r.total_s,
		@porcDifAreaWB :=(
		@diferenciaArea / r.areaProveedorLote 
		)* 100,
		@areaPzasRechazo := r.piezasRechazadas * @promedioAreaWB,
		
        /*
        *SETS EMPACADOS SE CALCULAN CON EL REGISTRO DE LAS CAJAS EMPACADAS: SETS EMPACADOS EN LINEA DE PROCESO
        @setsEmpacados := if(r.tipoProceso='1', r.unidadesEmpacadas / conf.pzasEnSets, r.unidadesEmpacadas),
        */
		/*
        *SETS MARCADOS POR TESEO SE CALCULAN CON EL REGISTRO TEMPORAL DE TESEO: INFORMACION PRELIMINAR
		@setsCortadosTeseo := r.pzasCortadasTeseo / conf.pzasEnSets,
        */
        /*
        *SETS RECUPERADOS POR BITACORA DE RECUPERACION IMPLEMENTADAS
		@setsRecuperados := r.piezasRecuperadas / conf.pzasEnSets,
        */
		@areaCrustSet := r.areaCrust / r.setsEmpacados,
		@areaWBUnidad := r.areaWB / r.setsEmpacados,
		@costoWBUnit := @areaWBUnidad * dp.costoProm,
		@porcRecuperacion :=(
			r.setsRecuperados / r.setsEmpacados
		)* 100,
		
		@porcRecorteAcabado :=(
			(r.recorteAcabado/88)/r.areaCrust
		)* 100,
		@totalRecorte := r.porcRecorteWB + r.porcRecorteCrust+@porcRecorteAcabado,

		/*@pzasSetsRechazadas := r.pzasCortadasTeseo -(
			r.piezasRecuperadas + r.unidadesEmpacadas 
		),*/
	   /*@pzasSetsRechazadas := r.pzasCortadasTeseo -( r.unidadesEmpacadas),*/
	   /*@setsRechazados := @pzasSetsRechazadas / conf.pzasEnSets,*/
		@perdidaAreaCrustTeseo:=((r.areaFinal-r.areaCrust)/r.areaCrust)*100, 
		@yieldFinalReal:=(r.areaNeta_Prg/@areaWBUnidad)*100,
		@porcRechazo:=((r.pzasCortadasTeseo-r.totalEmp)/r.pzasCortadasTeseo)*100,
       /* @porcRechazo:=(r.setsRechazados/r.setsCortadosTeseo)*100,*/
		@perdidaAreaWBTerminado:=((r.areaFinal-r.areaWB)/r.areaWB)*100
	FROM
		rendimientos r
		INNER JOIN config_inventarios conf ON conf.estado = '1'
		INNER JOIN (
		SELECT
			dp.idRendimiento,
			AVG( p.precioUnitFactUsd ) AS costoProm 
		FROM
			detpedidos dp
			INNER JOIN pedidos p ON dp.idPedido = p.id 
		WHERE
			dp.idRendimiento = id_Rendimiento 
		GROUP BY
			dp.idRendimiento 
		) dp ON dp.idRendimiento = r.id 
	WHERE
		r.id = id_Rendimiento
	) t ON t.id=r.id
	SET r.diferenciaArea=@diferenciaArea, 
	    r.perdidaAreaWBCrust=@perdidaAreaWBCrust,
	    r.promedioAreaWB=@promedioAreaWB, 
		r.porcDifAreaWB=@porcDifAreaWB, 
		r.areaPzasRechazo=@areaPzasRechazo,
      	r.totalRecorte=@totalRecorte, 
		/*r.setsEmpacados=@setsEmpacados, */
		/*r.setsCortadosTeseo=@setsCortadosTeseo,*/
        /* r.setsRecuperados=@setsRecuperados, */
		r.areaCrustSet=@areaCrustSet, 
		r.areaWBUnidad=@areaWBUnidad,
        r.costoWBUnit=@costoWBUnit, 
		r.porcRecuperacion=@porcRecuperacion, 
		r.porcRecuperacionFinal=@porcRecuperacion,
       /*r.pzasSetsRechazadas=@pzasSetsRechazadas,*/
		/*r.setsRechazados=@setsRechazados, */
		r.perdidaAreaCrustTeseo=@perdidaAreaCrustTeseo,
        r.yieldFinalReal=@yieldFinalReal, 
		/*r.porcSetsRechazoInicial=@porcRechazo,*/
		r.porcFinalRechazo=@porcRechazo, 
		r.estado='4',
        r.areaWBTerminado=@perdidaAreaWBTerminado, 
		r.envioSolicitud='0',
		r.porcRecorteAcabado=@porcRecorteAcabado
		/*--r.total_ant_s=r.total_s,
		--r.total_s=@total_s*/


	WHERE r.id=id_Rendimiento AND r.regDatos='1';
	
	
	IF cambioPzas='1' THEN
	
		/*INSERT INTO inventariorecuperado (idRendimiento, pzasTotales,setsTotales,rezago, fechaReg, idUserReg)
		SELECT r.id, 0,0/conf.pzasEnSets,0%conf.pzasEnSets, NOW(), idUserReg FROM rendimientos r 
		 INNER JOIN config_inventarios conf ON conf.estado = '1'
					WHERE r.id=id_Rendimiento AND r.tipoProceso='1';*/

		INSERT INTO inventarioempacado (idRendimiento, pzasTotales,setsTotales,rezago, fechaReg, idUserReg, tipoProceso)
		SELECT r.id, totalEmp,totalEmp/conf.pzasEnSets,totalEmp%conf.pzasEnSets, NOW(), idUserReg, tipoProceso 
		FROM rendimientos r 
		INNER JOIN config_inventarios conf ON conf.estado = '1'
		WHERE r.id=id_Rendimiento;

		/*INSERT INTO inventariorechazado (idRendimiento, pzasTotales,setsTotales,rezago, fechaReg, idUserReg)
		SELECT r.id, pzasSetsRechazadas,pzasSetsRechazadas/conf.pzasEnSets,piezasRecuperadas%conf.pzasEnSets, NOW(), idUserReg FROM rendimientos r 
		INNER JOIN config_inventarios conf ON conf.estado = '1'
		WHERE r.id=id_Rendimiento AND r.tipoProceso='1';*/
	
END IF;	
END;
$$ 
DELIMITER ";"