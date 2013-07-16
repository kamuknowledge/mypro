DROP PROCEDURE IF EXISTS `SPgetcountries`;

DELIMITER $$


CREATE PROCEDURE `SPgetcountries`()
BEGIN

Declare tmoduleStatusIdActive int(11);

SELECT statusid into tmoduleStatusIdActive FROM apmmasterrecordsstate  where recordstate='Active';


select *
from master_countries c
where
c.statusid=tmoduleStatusIdActive;

END $$

DELIMITER ;