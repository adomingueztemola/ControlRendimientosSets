SELECT
    d.idEmpaque AS idEmpaque,
    r.loteTemola AS loteTemola,
    d.idLote AS idLote,
    r.pzasCortadasTeseo AS pzasCortadasTeseo,
    sum( ( case  when (  (d.tipo = 1) and (d.remanente = '0') ) then d.total else 0 end ) ) AS sumPzasNorm,
    sum( ( case  when (d.tipo = 2) then d.total else 0 end ) ) AS sumPzasRemt,
    sum(( case when (d.tipo = 3) then d.total    else 0 end ) ) AS sumPzasRecup,
    (sum(( case when ( (d.tipo = 1) and (d.remanente = '0') ) then d.total else 0 end )) + 
    sum((case when (d.tipo = 2) then d.total else 0 end )) ) AS pzasEmp,
    (( sum(( case when ((d.tipo = 1) and (d.remanente = '0')) then d.total  else 0 end ) ) + 
    sum(( case when (d.tipo = 2) then d.total else 0 end ) )) + 
    sum((case when (d.tipo = 3) then d.total  else 0 end) ) ) AS totalEmp,
    sum(( case when (d.tipo = 1) then d._12 else 0 end)) AS sumNorm12,
    sum((case when (d.tipo = 1) then d._3 else 0 end )) AS sumNorm3,
    sum((case when (d.tipo = 1) then d._6 else 0 end )) AS sumNorm6,
    sum((case when (d.tipo = 1) then d._9 else 0 end ) ) AS sumNorm9,
   (r._12OK - sum( ( case  when (d.tipo = 1) then d._12 else 0 end ) ) ) AS scrap12,
   ( r._3OK - sum( (  case when (d.tipo = 1) then d._3 else 0 end) ) ) AS scrap3,
   ( r._6OK - sum((case when (d.tipo = 1) then d._6  else 0 end )) ) AS scrap6,
   ( r._9OK - sum((case  when (d.tipo = 1) then d._9 else 0 end))) AS scrap9,
   ((( (r._12OK - sum(( case when (d.tipo = 1) then d._12  else 0 end ) )  ) 
   + ( r._3OK - sum((case when (d.tipo = 1) then d._3 else 0 end) )) ) + 
   ( r._6OK - sum((case when (d.tipo = 1) then d._6 else 0 end )))) + 
   ( r._9OK - sum( ( case when (d.tipo = 1) thend._9  else 0 end))) ) AS totalScrap
from
    detcajas d
      inner join rendimientosr on d.idLote =r.id

group by
   d.idLote