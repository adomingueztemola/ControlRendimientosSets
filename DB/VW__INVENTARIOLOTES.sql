CREATE OR REPLACE VIEW vw_inventariolotes
AS

SELECT 
r.id, ie.id AS idInvEmp, IFNULL(ie.pzasTotales,0) AS totalEmp, 
IFNULL(ie.setsTotales,0) AS setsTotalEmp, IFNULL(ie.rezago,0) AS rzgoEmp,
IFNULL(ir.id,0) AS idRech, IFNULL(ir.pzasTotales,0) AS totalRech, 
IFNULL(ir.setsTotales,0) AS setsTotalRech,IFNULL(ir.rezago,0) AS rzgoRech,
IFNULL(irecu.id,0) AS idInvRecu, IFNULL(irecu.pzasTotales,0) AS totalRecu, 
IFNULL(irecu.setsTotales,0) AS setsTotalRecu, IFNULL(irecu.rezago,0) AS rzgoRecu,	
IFNULL(vw.totalRecuperado,0) AS totalRecuperado, r.pzasCortadasTeseo,
ROUND(r.pzasCortadasTeseo*(conf.porcLimitRecup/100)) AS pzasLimitRecup, conf.porcLimitRecup,
ir._12 AS _12Scrap, ir._3 AS _3Scrap, ir._6 AS _6Scrap, ir._9 AS _9Scrap,
irecu._12 AS _12Recu, irecu._3 AS _3Recu, irecu._6 AS _6Recu, irecu._9 AS _9Recu
FROM rendimientos  r
LEFT JOIN inventarioempacado ie ON ie.idRendimiento= r.id
LEFT JOIN inventariorechazado ir ON ir.idRendimiento= r.id
LEFT JOIN inventariorecuperado irecu ON irecu.idRendimiento= r.id
LEFT JOIN vw_alltrabajosrecuperacion vw ON vw.idRendimiento=r.id
INNER JOIN config_inventarios conf ON conf.estado='1'
