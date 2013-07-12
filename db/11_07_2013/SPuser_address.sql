DROP PROCEDURE IF EXISTS `SPuser_address`;

DELIMITER $$


CREATE PROCEDURE `SPuser_address`(
IN iuserid int(11),
IN ihome_address1 varchar(255),
IN ihome_address2 varchar(255),
IN ihome_city varchar(255),
IN ihome_street varchar(255),
IN ihome_postal_code varchar(255),
IN ihome_country_id int(11),
IN ihome_state_id int(11),
IN ioffice_address1 varchar(255),
IN ioffice_address2 varchar(255),
IN ioffice_city varchar(255),
IN ioffice_street varchar(255),
IN ioffice_postal_code varchar(255),
IN ioffice_country_id int(11),
IN ioffice_state_id int(11),
IN iaction varchar(255)
)
BEGIN

declare tcuruseraction varchar(255);
declare tactiondesc varchar(255);
declare taddress_id int(11);
declare tUserStatusIdActive int(11);
declare taactivityid int(11);
declare toutput varchar(255);

declare thome_address_count int(11);
declare toffice_address_count int(11);

declare thomeaddress_id int(11);
declare tofficeaddress_id int(11);

declare tuser_home_address_id int(11);
declare tuser_office_address_id int(11);




set tactiondesc = '';

SELECT statusid into tUserStatusIdActive FROM apmmasterrecordsstate  where recordstate='Active';

SELECT count(user_address_id) into thome_address_count FROM user_address
where address_type = 'Home' AND statusid = tUserStatusIdActive ORDER BY user_address_id DESC LIMIT 0,1;



  if thome_address_count=0 then

			insert into user_address
			(userid,
			address_type,
			address1,
			address2,
			city,
			street,
			postal_code,
			country_id,
      state_id,
			createddatetime,
			statusid)
			values
			(
			iuserid,
      'Home',
			ihome_address1,
			ihome_address2,
			ihome_city,
			ihome_street,
			ihome_postal_code,
			ihome_country_id,
			ihome_state_id,
			NOW(),
			tUserStatusIdActive);

      select LAST_INSERT_ID() into thomeaddress_id;

      set tcuruseraction = 'Add Address';
      set tactiondesc = concat('Home address  created with ', ihome_address1, ' and address_id ', thomeaddress_id , ' by admin ', iuserid);
      select FNapmwriteactivitylog(iuserid , tcuruseraction, iaction , tactiondesc) into taactivityid;

  else

      SELECT user_address_id into tuser_home_address_id FROM user_address
        where address_type = 'Home' AND statusid = tUserStatusIdActive ORDER BY user_address_id DESC LIMIT 0,1;

      update user_address set
      address1       = ihome_address1,
      address2       = ihome_address2,
      city           = ihome_city,
      street         = ihome_street,
      postal_code    = ihome_postal_code,
      country_id     = ihome_country_id,
      state_id       = ihome_state_id
      where
      user_address_id = tuser_home_address_id AND address_type = 'Home'
      AND userid = iuserid;

      set tcuruseraction = 'Edit Address';
      set tactiondesc = concat('Address details were updated for user_address_id ',tuser_home_address_id, ' by ', iuserid);
      select FNapmwriteactivitylog(iuserid , tcuruseraction, iaction , tactiondesc) into taactivityid;


  end if;







SELECT count(user_address_id) into toffice_address_count FROM user_address
where address_type = 'Office' AND statusid = tUserStatusIdActive;



  if toffice_address_count=0 then

			insert into user_address
			(userid,
			address_type,
			address1,
			address2,
			city,
			street,
			postal_code,
			country_id,
      state_id,
			createddatetime,
			statusid)
			values
			(
			iuserid,
      'Office',
			ioffice_address1,
			ioffice_address2,
			ioffice_city,
			ioffice_street,
			ioffice_postal_code,
			ioffice_country_id,
			ioffice_state_id,
			NOW(),
			tUserStatusIdActive);

      select LAST_INSERT_ID() into tofficeaddress_id;

      set tcuruseraction = 'Add Address';
      set tactiondesc = concat('Home address  created with ', ioffice_address1, ' and address_id ', tofficeaddress_id , ' by admin ', iuserid);
      select FNapmwriteactivitylog(iuserid , tcuruseraction, iaction , tactiondesc) into taactivityid;

  else

      SELECT user_address_id into tuser_office_address_id FROM user_address
        where address_type = 'Office' AND statusid = tUserStatusIdActive;

      update user_address set
      address1       = ioffice_address1,
      address2       = ioffice_address2,
      city           = ioffice_city,
      street         = ioffice_street,
      postal_code    = ioffice_postal_code,
      country_id     = ioffice_country_id,
      state_id       = ioffice_state_id
      where
      user_address_id = tuser_office_address_id AND address_type = 'Office'
      AND userid = iuserid;

      set tcuruseraction = 'Edit Address';
      set tactiondesc = concat('Office Address details were updated for user_address_id ',tuser_office_address_id, ' by ', iuserid);
      select FNapmwriteactivitylog(iuserid , tcuruseraction, iaction , tactiondesc) into taactivityid;


  end if;







set toutput = concat('1#', 'Address Registered successfully');

  
select toutput;

END $$

DELIMITER ;