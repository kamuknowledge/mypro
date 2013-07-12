DROP PROCEDURE IF EXISTS `SPuser_address_add`;

DELIMITER $$


CREATE PROCEDURE `SPuser_address_add`(
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

declare tcuruseraction varchar(255);
declare tactiondesc varchar(255);
declare taddress_id int(11);
declare tUserStatusIdActive int(11);
declare taactivityid int(11);
declare toutput varchar(255);

set tcuruseraction = 'Add Address';
set tactiondesc = '';

SELECT statusid into tUserStatusIdActive FROM apmmasterrecordsstate  where recordstate='Active';

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
      iaddress_type,
			iaddress1,
			iaddress2,
			icity,
			istreet,
			ipostal_code,
			icountry_id,
			istate_id,
			NOW(),
			tUserStatusIdActive);

      select LAST_INSERT_ID() into taddress_id;


      set tactiondesc = concat('Address  created with title ', iaddress_type, ' and address_id ', taddress_id , ' by admin ', iuserid);
      set toutput = concat('1#', 'Address Registered successfully');


      select FNapmwriteactivitylog(iuserid , tcuruseraction, iaction , tactiondesc) into taactivityid;

  
select toutput;

END $$

DELIMITER ;