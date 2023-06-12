CREATE OR REPLACE VIEW vw_detalladorecuperacion
AS
SELECT  idRendInicio AS idLote, SUM(IFNULL(_12,0)) AS total_12,
SUM(IFNULL(_3,0)) AS total_3,
SUM(IFNULL(_6,0)) AS total_6,
SUM(IFNULL(_9,0)) AS total_9,
SUM(IFNULL(totalRecuperacion,0)) AS total

FROM
materialesrecuperados
WHERE estado='2'
GROUP BY idRendInicio