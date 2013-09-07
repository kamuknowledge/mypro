DROP PROCEDURE IF EXISTS `SPuser_education_add`;

DELIMITER $$


CREATE PROCEDURE `SPuser_education_add`(
IN iuserid int(11),
IN ischool_name varchar(255),
IN idegree varchar(255),
IN ispecialization varchar(255),
IN ieducation_notes varchar(11),
IN ifrom_year int(4),
IN ifrom_month int(2),
IN ito_year int(4),
IN ito_month int(2),
IN iaction varchar(255)
)
BEGIN

declare tcuruseraction varchar(255);
declare tactiondesc varchar(255);
declare teducation_id int(11);
declare tUserStatusIdActive int(11);
declare taactivityid int(11);
declare toutput varchar(255);

set tcuruseraction = 'Add Education';
set tactiondesc = '';

SELECT statusid into tUserStatusIdActive FROM apmmasterrecordsstate  where recordstate='Active';

			insert into user_education
			(userid,
			school_name,
			degree,
			specialization,
			education_notes,
			from_year,
			from_month,
			to_year,
			to_month,
			createddatetime,
			statusid)
			values
			(
			iuserid,
			ischool_name,
			idegree,
			ispecialization,
			ieducation_notes,
			ifrom_year,
			ifrom_month,
			ito_year,
			ito_month,
			NOW(),
			tUserStatusIdActive);

      select LAST_INSERT_ID() into teducation_id;


      set tactiondesc = concat('Education  created with title ', ischool_name, ' and education_id ', teducation_id , ' by admin ', iuserid);
      set toutput = concat('1#', 'Education Registered successfully');


      select FNapmwriteactivitylog(iuserid , tcuruseraction, iaction , tactiondesc) into taactivityid;

  
select toutput;

END $$

DELIMITER ;