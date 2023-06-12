CREATE OR REPLACE VIEW vw_detalladocaja
AS

SELECT d.idEmpaque, r.loteTemola, d.idLote, r.pzasCortadasTeseo, 
SUM(case when d.tipo = 1  AND d.remanente='0' then d.total else 0 end) as sumPzasNorm,
SUM(case when d.tipo = 2  then d.total else 0 end) as sumPzasRemt,
SUM(case when d.tipo = 3  then d.total else 0 end) as sumPzasRecup,
SUM(case when d.tipo = 1 AND d.remanente='0' then d.total else 0 end)+ SUM(case when d.tipo = 2  then d.total else 0 end) AS pzasEmp,
SUM(case when d.tipo = 1 AND d.remanente='0' then d.total else 0 end)+ SUM(case when d.tipo = 2  then d.total else 0 end)+
SUM(case when d.tipo = 3  then d.total else 0 end) AS totalEmp,
SUM(case when d.tipo = 1  then d._12 else 0 end) as sumNorm12,
SUM(case when d.tipo = 1  then d._3 else 0 end) as sumNorm3,
SUM(case when d.tipo = 1  then d._6 else 0 end) as sumNorm6,
SUM(case when d.tipo = 1  then d._9 else 0 end) as sumNorm9,
r._12OK-SUM(case when d.tipo = 1  then d._12 else 0 end) as scrap12,
r._3OK-SUM(case when d.tipo = 1  then d._3 else 0 end) as scrap3,
r._6OK-SUM(case when d.tipo = 1  then d._6 else 0 end) as scrap6,
r._9OK-SUM(case when d.tipo = 1  then d._9 else 0 end) as scrap9,
(r._12OK-SUM(case when d.tipo = 1  then d._12 else 0 end))+(r._3OK-SUM(case when d.tipo = 1  then d._3 else 0 end))+
(r._6OK-SUM(case when d.tipo = 1  then d._6 else 0 end))+(r._9OK-SUM(case when d.tipo = 1  then d._9 else 0 end)) AS
totalScrap
FROM detcajas d
INNER JOIN rendimientos r ON d.idLote=r.id
GROUP BY d.idLote