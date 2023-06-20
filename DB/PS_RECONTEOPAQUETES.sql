DELIMITER $$
CREATE PROCEDURE reconteoPaquetes (
		IN id_Lote INT ( 11 )
		) BEGIN
		SET @a = 0;
		UPDATE paqueteslados SET numPaquete=@a:=@a+1
        WHERE idLoteMedido=id_Lote;
		END
		
$$