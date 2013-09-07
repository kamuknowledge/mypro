DROP PROCEDURE IF EXISTS `SPuser_education_edit`;

DELIMITER $$


CREATE PROCEDURE `SPuser_education_edit`(
IN ieducation_id int(11),
IN iuserid int(11),
IN ischool_name varchar(255),
IN idegree varchar(255),
IN ispecialization varchar(255),
IN ieducation_notes varchar(255),
IN ifrom_year int(4),
IN ifrom_month int(2),
IN ito_year int(4),
IN ito_month int(2),
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


select statusid into tstatus from user_education where education_id = ieducation_id;


if tstatus <> tuserStatusIdDeleted then

      update user_education set
      school_name           = ischool_name,
      degree                = idegree,
      specialization        = ispecialization,
      education_notes       = ieducation_notes,
      from_year             = ifrom_year,
      from_month            = ifrom_month,
      to_year               = ito_year,
      to_month              = ito_month
      where
      education_id = ieducation_id
      AND userid = iuserid;

      set tcuruseraction = 'Edit Education';
      set tactiondesc = concat('Education details were updated for education_id ',ieducation_id, ' by ', iuserid);



      select FNapmwriteactivitylog(iuserid , tcuruseraction, iaction , tactiondesc) into taactivityid;
      set tmess = '1#success';
      select tmess;

else
  set tmess = '2#failure';
  select tmess;
end if;

END $$

DELIMITER ;