DROP PROCEDURE IF EXISTS `SPuser_address_edit`;

DELIMITER $$


CREATE PROCEDURE `SPuser_address_edit`(
IN iaddress_id int(11),
IN iuserid int(11),
IN iaddress_type varchar(255),
IN iaddress1 varchar(255),
IN iaddress2 varchar(255),
IN icity varchar(255),
IN istreet varchar(255),
IN ipostal_code varchar(255),
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


select statusid into tstatus from user_address where user_address_id = iaddress_id;


if tstatus <> tuserStatusIdDeleted then

      update user_address set
      address_type   = iaddress_type,
      address1       = iaddress1,
      address2       = iaddress2,
      city           = icity,
      street         = istreet,
      postal_code    = ipostal_code,
      country_id     = icountry_id,
      state_id       = istate_id
      where
      user_address_id = iaddress_id
      AND userid = iuserid;

      set tcuruseraction = 'Edit Address';
      set tactiondesc = concat('Address details were updated for user_address_id ',iaddress_id, ' by ', iuserid);



      select FNapmwriteactivitylog(iuserid , tcuruseraction, iaction , tactiondesc) into taactivityid;
      set tmess = '1#success';
      select tmess;

else
  set tmess = '2#failure';
  select tmess;
end if;

END $$

DELIMITER ;