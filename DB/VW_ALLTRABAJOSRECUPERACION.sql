CREATE OR REPLACE VIEW vw_alltrabajosrecuperacion
AS

select 
`mr`.`idRendRecup` AS `idRendimiento`,
sum(`mr`.`totalRecuperacion`) AS `totalRecuperado` 
from 
`materialesrecuperados` `mr` 
where ((`mr`.`estado` = '4') 
and ((`mr`.`idRendInicio` <> `mr`.`idRendRecup`) 
or isnull(`mr`.`idRendInicio`))) 
group by `mr`.`idRendRecup`