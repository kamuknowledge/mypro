DELIMITER $$

DROP PROCEDURE IF EXISTS `pro_my`.`SPuser_experience_add`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `SPuser_experience_add`(
IN iuserid int(11),
IN icompany_name varchar(255),
IN ijob_title varchar(255),
IN ijob_location varchar(255),
IN iindustry_id int(11),
IN ifrom_year int(4),
IN ifrom_month int(2),
IN ito_year int(4),
IN ito_month int(2),
IN ipresent_working int(11),
IN icompany_description text,
IN icountry_id int(11),
IN istate_id int(11),
IN iaction varchar(255)
)
BEGIN

declare tcuruseraction varchar(255);
declare tactiondesc varchar(255);
declare texperience_id int(11);
declare tUserStatusIdActive int(11);
declare taactivityid int(11);
declare toutput varchar(255);

set tcuruseraction = 'Add Experience';
set tactiondesc = '';

SELECT statusid into tUserStatusIdActive FROM apmmasterrecordsstate  where recordstate='Active';

			insert into user_experience
			(userid,
			company_name,
			job_title,
			job_location,
			industry_id,
			from_year,
			from_month,
			to_year,
			to_month,
			present_working,
			company_description,
			country_id,
      state_id,
			createddatetime,
			statusid)
			values
			(
			iuserid,
			icompany_name,
			ijob_title,
			ijob_location,
			iindustry_id,
			ifrom_year,
			ifrom_month,
			ito_year,
			ito_month,
			ipresent_working,
			icompany_description,
			icountry_id,
      istate_id,
			NOW(),
			tUserStatusIdActive);

      select LAST_INSERT_ID() into texperience_id;


      set tactiondesc = concat('Experience  created with title ', ijob_title, ' and experience_id ', texperience_id , ' by admin ', iuserid);
      set toutput = concat('1#', texperience_id, '#Experience Registered successfully');


      select FNapmwriteactivitylog(iuserid , tcuruseraction, iaction , tactiondesc) into taactivityid;

  
select toutput;

END$$

DELIMITER ;