CREATE OR REPLACE VIEW vw_detalladoventas
AS
SELECT r.id AS idRendimiento, SUM(dv.unidades) AS totalUnidades 
FROM detventas dv 
INNER JOIN  rendimientos r ON dv.idRendimiento=r.id
GROUP BY dv.idRendimiento