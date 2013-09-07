DROP PROCEDURE IF EXISTS `SPuser_experience_edit`;


DELIMITER $$


CREATE PROCEDURE `SPuser_experience_edit`(
IN iexperience_id int(11),
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

Declare tuserStatusIdActive int(11);
Declare tuserStatusIdDeleted int(11);
Declare tactiondesc varchar(255);
Declare taactivityid int(11);
Declare tcuruseraction varchar(255);
Declare tmess varchar(255);
Declare tstatus int(11);


SELECT statusid into tuserStatusIdActive FROM apmmasterrecordsstate  where recordstate='Active';
SELECT statusid into tuserStatusIdDeleted FROM apmmasterrecordsstate  where recordstate='Deleted';


select statusid into tstatus from user_experience where experience_id = iexperience_id;


if tstatus <> tuserStatusIdDeleted then

      update user_experience set
      company_name           = icompany_name,
      job_title              = ijob_title,
      job_location           = ijob_location,
      industry_id            = iindustry_id,
      from_year              = ifrom_year,
      from_month             = ifrom_month,
      to_year                = ito_year,
      to_month               = ito_month,
      present_working        = ipresent_working,
      company_description    = icompany_description,
      country_id             = icountry_id,
      state_id               = istate_id
      where
      experience_id = iexperience_id
      AND userid = iuserid;

      set tcuruseraction = 'Edit Experience';
      set tactiondesc = concat('Experience details were updated for experience_id ',iexperience_id, ' by ', iuserid);



      select FNapmwriteactivitylog(iuserid , tcuruseraction, iaction , tactiondesc) into taactivityid;
      set tmess = '1#success';
      select tmess;

else
  set tmess = '2#failure';
  select tmess;
end if;

END $$

DELIMITER ;