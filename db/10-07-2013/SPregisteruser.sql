DELIMITER $$

DROP PROCEDURE IF EXISTS `pro_my`.`SPregisteruser`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `SPregisteruser`(
IN ifirstname varchar(255),
IN ilastname varchar(255),
IN iemailid varchar(255),
IN ipassword varchar(255),
IN iphonenumber double(10,0),
IN igender varchar(255),
IN iaction varchar(255)
)
BEGIN

declare tactiondesc varchar(255);
declare tusercount int(5) default 0;
declare tuserid int(11);
declare tUserStatusIdActive int(11);
declare tRoleid int(11);
declare toutput varchar(255);

SELECT statusid into tUserStatusIdActive FROM apmmasterrecordsstate  where recordstate='Active';
SELECT roleid into tRoleid FROM apmmasterroles WHERE rolename='User';


select count(emailid) into tusercount from apmusers where userloginid = iemailid;

if tusercount <> 0 then
    set tactiondesc = concat('Unable to register user with email ', iemailid , ' as the email already exists.');
    set toutput = '0#Failed to register User';

else
        -- Inserting data into `apmusers` TABLE

        insert into apmusers(firstname, lastname, emailid, password, userloginid, phonenumber, createddatetime, statusid)
        values
        (ifirstname, ilastname, iemailid, ipassword, iemailid, iphonenumber, NOW(), tUserStatusIdActive);
        select LAST_INSERT_ID() into tuserid;



        -- Inserting data into `apmpasswordhistory` TABLE

        insert into apmpasswordhistory(userid, userpassword, createddatetime, statusid)
        values
        (tuserid,  ipassword, now(), tUserStatusIdActive);



        -- Inserting data into `apmuserrolemapping` TABLE

        insert into apmuserrolemapping(roleid, userid, createddatetime, statusid)
        values
        (tRoleid, tuserid, NOW(), tUserStatusIdActive);


        set toutput = concat('1#', 'User Registered successfully');

end if;


select toutput;

END$$

DELIMITER ;