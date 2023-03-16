DELIMITER $$
CREATE  PROCEDURE calculaPruebasHide (
		IN lotesito INT ( 11 ), IN fecha DATE,
		 IN hides INT ( 11 ), IN id_UserReg INT ( 11 )
		) BEGIN
SET @convertCuerosHides=hides/2;		

#DETALLADO DE PIEZAS ACTUALES EN RENDIMIENTOS
SELECT 1s INTO @1sL
FROM rendimientos
WHERE id=lotesito;

SELECT 2s INTO @2sL 
FROM rendimientos
WHERE id=lotesito;

SELECT 3s INTO @3sL
FROM rendimientos
WHERE id=lotesito;

SELECT 4s INTO @4sL
FROM rendimientos
WHERE id=lotesito;

SELECT _20 INTO @_20L
FROM rendimientos
WHERE id=lotesito;

SELECT total_s INTO @total_sL
FROM rendimientos
WHERE id=lotesito;

#Porcentaje disminuido de Materia Prima 
SET @porcent = @convertCuerosHides/@total_sL;

#Disminucion de Materia Prima en Multiplos
SET @mod_1s = (@1sL-(@1sL*@porcent))%1;
SET @int_1s = (@1sL-(@1sL*@porcent))-@mod_1s;
SELECT CASE
    WHEN  @mod_1s > 0.50 AND @1sL>0 THEN @int_1s+1
    WHEN @mod_1s <= 0.50 AND @1sL>0 THEN @int_1s+0.5
    WHEN @1sL=0 THEN 0
    END
    INTO @1s;

SET @mod_2s = (@2sL-(@2sL*@porcent))%1;
SET @int_2s = (@2sL-(@2sL*@porcent))-@mod_2s;
SELECT CASE
    WHEN  @mod_2s > 0.50 AND @2sL>0 THEN @int_2s+1
    WHEN @mod_2s <= 0.50 AND @2sL>0 THEN @int_2s+0.5
    WHEN @2sL=0 THEN 0

   END  INTO @2s;

SET @mod_3s = (@3sL-(@3sL*@porcent))%1;
SET @int_3s = (@3sL-(@3sL*@porcent))-@mod_3s;
SELECT CASE
    WHEN  @mod_3s > 0.50 AND @3sL>0 THEN @int_3s+1
    WHEN @mod_3s <= 0.50 AND @3sL>0 THEN @int_3s+0.5
    WHEN @3sL=0 THEN 0

    END INTO @3s;

SET @mod_4s = (@4sL-(@4sL*@porcent))%1;
SET @int_4s = (@4sL-(@4sL*@porcent))-@mod_4s;
SELECT CASE
    WHEN  @mod_4s > 0.50 AND @4sL>0 THEN @int_4s+1
    WHEN @mod_4s <= 0.50 AND @4sL>0 THEN @int_4s+0.5
    WHEN @4sL=0 THEN 0

    END INTO @4s;

SET @mod_20 = (@_20L-(@_20L*@porcent))%1;
SET @int_20 = (@_20L-(@_20L*@porcent))-@mod_20;
SELECT CASE
    WHEN  @mod_20 > 0.50 AND @_20L>0 THEN @int_20+1
    WHEN @mod_20 <= 0.50 AND @_20L>0 THEN @int_20+0.5
    WHEN @_20L=0 THEN 0

    END INTO @_20;

SET @mod_total_s = (@total_sL-(@total_sL*@porcent))%1;
SET @int_total_s = (@total_sL-(@total_sL*@porcent))-@mod_total_s;
SELECT CASE
    WHEN  @mod_total_s > 0.50 AND @total_sL>0 THEN @int_total_s+1
    WHEN @mod_total_s <= 0.50 AND @total_sL>0 THEN @int_total_s+0.5
    WHEN @total_sL=0 THEN 0
   END INTO @total_s;

INSERT INTO pruebashides (idLote, fecha, hides, 1s, 2s, 3s, 4s, _20, total_s, idUserReg, fechaReg, porcent)
VALUES (lotesito, fecha, hides, @1s, @2s, @3s, @4s, @_20, @total_s, id_UserReg, NOW(), @porcent);

#PASE DE HIDES A RENDIMIENTOS
SELECT LAST_INSERT_ID() INTO @idInsert;

UPDATE rendimientos r
          INNER JOIN pruebashides p ON r.id=p.idLote
          SET r.1s=@1s, r.2s= @2s, r.3s= @3s, r.4s= @4s, 
            r._20= @_20,  r.total_s= @total_s, 
            r.areaWB= IFNULL(r.areaWB,0)-(IFNULL(r.areaWB,0)*@porcent),
            p.areaWB= (IFNULL(r.areaWB,0)*@porcent),
            r.areaCrust= IFNULL(r.areaCrust,0)-(IFNULL(r.areaCrust,0)*@porcent),
            p.areaCrust= (IFNULL(r.areaCrust,0)*@porcent),
            r.recorteAcabado= IFNULL(r.recorteAcabado,0)-(IFNULL(r.recorteAcabado,0)*@porcent),
            p.recorteAcabado= (IFNULL(r.recorteAcabado,0)*@porcent)
          WHERE p.id= @idInsert;


#CALCULA CUEROS DE MATERIA PRIMA

UPDATE detpedidos dp
INNER JOIN pedidos p ON dp.idPedido=p.id
INNER JOIN pruebashides ph ON dp.idRendimiento=ph.idLote
SET 
   dp.1s=CASE
    WHEN  (IFNULL(dp.1s,0)-(IFNULL(dp.1s,0)*ph.porcent))%1 > 0.50 AND (IFNULL(dp.1s,0)-(IFNULL(dp.1s,0)*ph.porcent))>0 
    THEN (IFNULL(dp.1s,0)-(IFNULL(dp.1s,0)*ph.porcent))-((IFNULL(dp.1s,0)-(IFNULL(dp.1s,0)*ph.porcent))%1)+1

    WHEN  (IFNULL(dp.1s,0)-(IFNULL(dp.1s,0)*ph.porcent))%1 <= 0.50 AND (IFNULL(dp.1s,0)-(IFNULL(dp.1s,0)*ph.porcent))>0 
    THEN (IFNULL(dp.1s,0)-(IFNULL(dp.1s,0)*ph.porcent))-((IFNULL(dp.1s,0)-(IFNULL(dp.1s,0)*ph.porcent))%1)+0.5

    WHEN (IFNULL(dp.1s,0)-(IFNULL(dp.1s,0)*ph.porcent))=0 THEN 0
    END,

   dp.2s=CASE
    WHEN  (IFNULL(dp.2s,0)-(IFNULL(dp.2s,0)*ph.porcent))%1 > 0.50 AND (IFNULL(dp.2s,0)-(IFNULL(dp.2s,0)*ph.porcent))>0 
    THEN (IFNULL(dp.2s,0)-(IFNULL(dp.2s,0)*ph.porcent))-((IFNULL(dp.2s,0)-(IFNULL(dp.2s,0)*ph.porcent))%1)+1

    WHEN  (IFNULL(dp.2s,0)-(IFNULL(dp.2s,0)*ph.porcent))%1 <= 0.50 AND (IFNULL(dp.2s,0)-(IFNULL(dp.2s,0)*ph.porcent))>0 
    THEN (IFNULL(dp.2s,0)-(IFNULL(dp.2s,0)*ph.porcent))-((IFNULL(dp.2s,0)-(IFNULL(dp.2s,0)*ph.porcent))%1)+0.5

    WHEN (IFNULL(dp.2s,0)-(IFNULL(dp.2s,0)*ph.porcent))=0 THEN 0
    END,

   dp.3s=CASE
    WHEN  (IFNULL(dp.3s,0)-(IFNULL(dp.3s,0)*ph.porcent))%1 > 0.50 AND (IFNULL(dp.3s,0)-(IFNULL(dp.3s,0)*ph.porcent))>0 
    THEN (IFNULL(dp.3s,0)-(IFNULL(dp.3s,0)*ph.porcent))-((IFNULL(dp.3s,0)-(IFNULL(dp.3s,0)*ph.porcent))%1)+1

    WHEN  (IFNULL(dp.3s,0)-(IFNULL(dp.3s,0)*ph.porcent))%1 <= 0.50 AND (IFNULL(dp.3s,0)-(IFNULL(dp.3s,0)*ph.porcent))>0 
    THEN (IFNULL(dp.3s,0)-(IFNULL(dp.3s,0)*ph.porcent))-((IFNULL(dp.3s,0)-(IFNULL(dp.3s,0)*ph.porcent))%1)+0.5

    WHEN (IFNULL(dp.3s,0)-(IFNULL(dp.3s,0)*ph.porcent))=0 THEN 0
    END, 

   dp.4s=CASE
    WHEN  (IFNULL(dp.4s,0)-(IFNULL(dp.4s,0)*ph.porcent))%1 > 0.50 AND (IFNULL(dp.4s,0)-(IFNULL(dp.4s,0)*ph.porcent))>0 
    THEN (IFNULL(dp.4s,0)-(IFNULL(dp.4s,0)*ph.porcent))-((IFNULL(dp.4s,0)-(IFNULL(dp.4s,0)*ph.porcent))%1)+1

    WHEN  (IFNULL(dp.4s,0)-(IFNULL(dp.4s,0)*ph.porcent))%1 <= 0.50 AND (IFNULL(dp.4s,0)-(IFNULL(dp.4s,0)*ph.porcent))>0 
    THEN (IFNULL(dp.4s,0)-(IFNULL(dp.4s,0)*ph.porcent))-((IFNULL(dp.4s,0)-(IFNULL(dp.4s,0)*ph.porcent))%1)+0.5

    WHEN (IFNULL(dp.4s,0)-(IFNULL(dp.4s,0)*ph.porcent))=0 THEN 0
    END, 
   dp._20=CASE
    WHEN  (IFNULL(dp._20,0)-(IFNULL(dp._20,0)*ph.porcent))%1 > 0.50 AND (IFNULL(dp._20,0)-(IFNULL(dp._20,0)*ph.porcent))>0 
    THEN (IFNULL(dp._20,0)-(IFNULL(dp._20,0)*ph.porcent))-((IFNULL(dp._20,0)-(IFNULL(dp._20,0)*ph.porcent))%1)+1

    WHEN  (IFNULL(dp._20,0)-(IFNULL(dp._20,0)*ph.porcent))%1 <= 0.50 AND (IFNULL(dp._20,0)-(IFNULL(dp._20,0)*ph.porcent))>0 
    THEN (IFNULL(dp._20,0)-(IFNULL(dp._20,0)*ph.porcent))-((IFNULL(dp._20,0)-(IFNULL(dp._20,0)*ph.porcent))%1)+0.5

    WHEN (IFNULL(dp._20,0)-(IFNULL(dp._20,0)*ph.porcent))=0 THEN 0
    END, 
   dp.total_s=CASE
    WHEN  (IFNULL(dp.total_s,0)-(IFNULL(dp.total_s,0)*ph.porcent))%1 > 0.50 AND (IFNULL(dp.total_s,0)-(IFNULL(dp.total_s,0)*ph.porcent))>0 
    THEN (IFNULL(dp.total_s,0)-(IFNULL(dp.total_s,0)*ph.porcent))-((IFNULL(dp.total_s,0)-(IFNULL(dp.total_s,0)*ph.porcent))%1)+1

    WHEN  (IFNULL(dp.total_s,0)-(IFNULL(dp.total_s,0)*ph.porcent))%1 <= 0.50 AND (IFNULL(dp.total_s,0)-(IFNULL(dp.total_s,0)*ph.porcent))>0 
    THEN (IFNULL(dp.total_s,0)-(IFNULL(dp.total_s,0)*ph.porcent))-((IFNULL(dp.total_s,0)-(IFNULL(dp.total_s,0)*ph.porcent))%1)+0.5

    WHEN (IFNULL(dp.total_s,0)-(IFNULL(dp.total_s,0)*ph.porcent))=0 THEN 0
    END, 
   dp.areaProveedorLote=CASE
    WHEN  (IFNULL(dp.total_s,0)-(IFNULL(dp.total_s,0)*ph.porcent))%1 > 0.50 AND (IFNULL(dp.total_s,0)-(IFNULL(dp.total_s,0)*ph.porcent))>0 
    THEN ((IFNULL(dp.total_s,0)-(IFNULL(dp.total_s,0)*ph.porcent))-((IFNULL(dp.total_s,0)-(IFNULL(dp.total_s,0)*ph.porcent))%1)+1)*p.areaWBPromFact

    WHEN  (IFNULL(dp.total_s,0)-(IFNULL(dp.total_s,0)*ph.porcent))%1 <= 0.50 AND (IFNULL(dp.total_s,0)-(IFNULL(dp.total_s,0)*ph.porcent))>0 
    THEN ((IFNULL(dp.total_s,0)-(IFNULL(dp.total_s,0)*ph.porcent))-((IFNULL(dp.total_s,0)-(IFNULL(dp.total_s,0)*ph.porcent))%1)+0.5)*p.areaWBPromFact

    WHEN (IFNULL(dp.total_s,0)-(IFNULL(dp.total_s,0)*ph.porcent))=0 THEN 0
    END,
   ph.areaProveedorLote=IFNULL(ph.hides/2,0)*p.areaWBPromFact
WHERE ph.id= @idInsert;

#CALCULA CUEROS DE VENTAS
UPDATE detventas dp
INNER JOIN pruebashides ph ON dp.idRendimiento=ph.idLote
SET 
   dp.1s=CASE
    WHEN  (IFNULL(dp.1s,0)-(IFNULL(dp.1s,0)*ph.porcent))%1 > 0.50 AND (IFNULL(dp.1s,0)-(IFNULL(dp.1s,0)*ph.porcent))>0 
    THEN (IFNULL(dp.1s,0)-(IFNULL(dp.1s,0)*ph.porcent))-((IFNULL(dp.1s,0)-(IFNULL(dp.1s,0)*ph.porcent))%1)+1

    WHEN  (IFNULL(dp.1s,0)-(IFNULL(dp.1s,0)*ph.porcent))%1 <= 0.50 AND (IFNULL(dp.1s,0)-(IFNULL(dp.1s,0)*ph.porcent))>0 
    THEN (IFNULL(dp.1s,0)-(IFNULL(dp.1s,0)*ph.porcent))-((IFNULL(dp.1s,0)-(IFNULL(dp.1s,0)*ph.porcent))%1)+0.5

    WHEN (IFNULL(dp.1s,0)-(IFNULL(dp.1s,0)*ph.porcent))=0 THEN 0
    END,

   dp.2s=CASE
    WHEN  (IFNULL(dp.2s,0)-(IFNULL(dp.2s,0)*ph.porcent))%1 > 0.50 AND (IFNULL(dp.2s,0)-(IFNULL(dp.2s,0)*ph.porcent))>0 
    THEN (IFNULL(dp.2s,0)-(IFNULL(dp.2s,0)*ph.porcent))-((IFNULL(dp.2s,0)-(IFNULL(dp.2s,0)*ph.porcent))%1)+1

    WHEN  (IFNULL(dp.2s,0)-(IFNULL(dp.2s,0)*ph.porcent))%1 <= 0.50 AND (IFNULL(dp.2s,0)-(IFNULL(dp.2s,0)*ph.porcent))>0 
    THEN (IFNULL(dp.2s,0)-(IFNULL(dp.2s,0)*ph.porcent))-((IFNULL(dp.2s,0)-(IFNULL(dp.2s,0)*ph.porcent))%1)+0.5

    WHEN (IFNULL(dp.2s,0)-(IFNULL(dp.2s,0)*ph.porcent))=0 THEN 0
    END,

   dp.3s=CASE
    WHEN  (IFNULL(dp.3s,0)-(IFNULL(dp.3s,0)*ph.porcent))%1 > 0.50 AND (IFNULL(dp.3s,0)-(IFNULL(dp.3s,0)*ph.porcent))>0 
    THEN (IFNULL(dp.3s,0)-(IFNULL(dp.3s,0)*ph.porcent))-((IFNULL(dp.3s,0)-(IFNULL(dp.3s,0)*ph.porcent))%1)+1

    WHEN  (IFNULL(dp.3s,0)-(IFNULL(dp.3s,0)*ph.porcent))%1 <= 0.50 AND (IFNULL(dp.3s,0)-(IFNULL(dp.3s,0)*ph.porcent))>0 
    THEN (IFNULL(dp.3s,0)-(IFNULL(dp.3s,0)*ph.porcent))-((IFNULL(dp.3s,0)-(IFNULL(dp.3s,0)*ph.porcent))%1)+0.5

    WHEN (IFNULL(dp.3s,0)-(IFNULL(dp.3s,0)*ph.porcent))=0 THEN 0
    END, 

   dp.4s=CASE
    WHEN  (IFNULL(dp.4s,0)-(IFNULL(dp.4s,0)*ph.porcent))%1 > 0.50 AND (IFNULL(dp.4s,0)-(IFNULL(dp.4s,0)*ph.porcent))>0 
    THEN (IFNULL(dp.4s,0)-(IFNULL(dp.4s,0)*ph.porcent))-((IFNULL(dp.4s,0)-(IFNULL(dp.4s,0)*ph.porcent))%1)+1

    WHEN  (IFNULL(dp.4s,0)-(IFNULL(dp.4s,0)*ph.porcent))%1 <= 0.50 AND (IFNULL(dp.4s,0)-(IFNULL(dp.4s,0)*ph.porcent))>0 
    THEN (IFNULL(dp.4s,0)-(IFNULL(dp.4s,0)*ph.porcent))-((IFNULL(dp.4s,0)-(IFNULL(dp.4s,0)*ph.porcent))%1)+0.5

    WHEN (IFNULL(dp.4s,0)-(IFNULL(dp.4s,0)*ph.porcent))=0 THEN 0
    END, 
   dp._20=CASE
    WHEN  (IFNULL(dp._20,0)-(IFNULL(dp._20,0)*ph.porcent))%1 > 0.50 AND (IFNULL(dp._20,0)-(IFNULL(dp._20,0)*ph.porcent))>0 
    THEN (IFNULL(dp._20,0)-(IFNULL(dp._20,0)*ph.porcent))-((IFNULL(dp._20,0)-(IFNULL(dp._20,0)*ph.porcent))%1)+1

    WHEN  (IFNULL(dp._20,0)-(IFNULL(dp._20,0)*ph.porcent))%1 <= 0.50 AND (IFNULL(dp._20,0)-(IFNULL(dp._20,0)*ph.porcent))>0 
    THEN (IFNULL(dp._20,0)-(IFNULL(dp._20,0)*ph.porcent))-((IFNULL(dp._20,0)-(IFNULL(dp._20,0)*ph.porcent))%1)+0.5

    WHEN (IFNULL(dp._20,0)-(IFNULL(dp._20,0)*ph.porcent))=0 THEN 0
    END, 
   dp.total_s=CASE
    WHEN  (IFNULL(dp.total_s,0)-(IFNULL(dp.total_s,0)*ph.porcent))%1 > 0.50 AND (IFNULL(dp.total_s,0)-(IFNULL(dp.total_s,0)*ph.porcent))>0 
    THEN (IFNULL(dp.total_s,0)-(IFNULL(dp.total_s,0)*ph.porcent))-((IFNULL(dp.total_s,0)-(IFNULL(dp.total_s,0)*ph.porcent))%1)+1

    WHEN  (IFNULL(dp.total_s,0)-(IFNULL(dp.total_s,0)*ph.porcent))%1 <= 0.50 AND (IFNULL(dp.total_s,0)-(IFNULL(dp.total_s,0)*ph.porcent))>0 
    THEN (IFNULL(dp.total_s,0)-(IFNULL(dp.total_s,0)*ph.porcent))-((IFNULL(dp.total_s,0)-(IFNULL(dp.total_s,0)*ph.porcent))%1)+0.5
    WHEN (IFNULL(dp.total_s,0)-(IFNULL(dp.total_s,0)*ph.porcent))=0 THEN 0
    END
WHERE ph.id= @idInsert;
END;

$$ 
DELIMITER ";"