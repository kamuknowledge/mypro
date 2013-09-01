-- phpMyAdmin SQL Dump
-- version 4.0.4.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Sep 01, 2013 at 03:29 PM
-- Server version: 5.6.11
-- PHP Version: 5.5.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `pro_my`
--
CREATE DATABASE IF NOT EXISTS `pro_my` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `pro_my`;

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `SPapmaddemail`(
IN iemailfrom varchar(255),
IN iemailto varchar(255),
IN iemailsubject text,
IN iemailbody blob,
IN ireferenceid BIGINT(18)

)
BEGIN





Declare tstatusid int(11);
Declare tappid int(11) default 0;
Declare toutput varchar(255);

SELECT statusid into tstatusid FROM apmmasterrecordsstate  where recordstate='Active';
-- select appid into tappid from apmmasterapps where appname = iappname and statusid = tstatusid;

-- if tappid <> 0 then

  if ireferenceid = '' or ireferenceid is Null then

  insert into apmmailqueue(emailfrom, emailto, emailsubject, body,createddatetime, statusid,mailstatus)
  values
  (iemailfrom, iemailto, iemailsubject, iemailbody, now(), tstatusid,1);

  else

  insert into apmmailqueue(emailfrom, emailto, emailsubject, body, referenceid, createddatetime, statusid,mailstatus)
  values
  (iemailfrom, iemailto, iemailsubject, iemailbody, ireferenceid,  now(), tstatusid,1);


  end if;
  set toutput = '1#successfully added';
  select toutput;

-- else

  -- set toutput = '0#app doesnot exists';
  -- select toutput;

-- end if;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPapmchangepassword`(
IN iuserid int(11),
IN ioldpassword varchar(255),
IN inewpassword varchar(255),
IN ifirstflag int(11),
IN iisadminreset int(11),
IN ipasswordlimit int(11),
IN icreateraction varchar(255),
IN iadminpassword int(11),
OUT omess varchar(255))
BEGIN


Declare tuserStatusIdActive int(11);
Declare tuserStatusIdInactive int(11);
Declare topassword varchar(255);
Declare tnpassword varchar(255);
Declare tvaliduserid int(11);
Declare tpasscnt int(11);
Declare tmaxpass int(11) default 0;
Declare tactiondesc varchar(320);
Declare taactivityid int(11);
Declare tbothflags int(11) default 0;
Declare tflagreset int(11);
Declare tRowsCount int(11) default 0;
Declare tuseraction varchar(255);
Declare tvaliduserpass int(11);





SELECT statusid into tuserStatusIdActive FROM apmmasterrecordsstate  where recordstate='Active';
SELECT statusid into tuserStatusIdInactive FROM apmmasterrecordsstate  where recordstate='Inactive';


set tmaxpass = ipasswordlimit;

-- iisadminreset = 0 for change password
-- iisadminreset = 1 for forgot password and first login
-- iisadminreset = 2 for admin reset

if iisadminreset = 0 then

  set tbothflags = 0;
  set tuseraction = 'Change Password';

elseif iisadminreset = 1 then

  set tbothflags = 0;
  if ifirstflag = 1 then
    set tuseraction = 'Forgot Password';
  else
    set tuseraction = 'First Login';
  end if;

elseif iisadminreset = 2 then

  set tbothflags = 2;
  set tuseraction = 'Reset Password';

end if;


set topassword = sha2(ioldpassword, 256);
set tnpassword = sha2(inewpassword, 256);

if iisadminreset = 2 then

  update apmpasswordhistory set statusid = tuserStatusIdInactive where userid = iuserid;

  insert into apmpasswordhistory(userid, userpassword, createddatetime, statusid)
    values
    (iuserid, tnpassword,now(),tuserStatusIdActive);

    select row_count() into tRowsCount;

    if tRowsCount > 0 then

      update apmusers set `password` = tnpassword, statusid =  tuserStatusIdActive  where userid = iuserid;

      set tactiondesc = concat('Admin with userid ', iadminpassword, ' has updated password for userid ',iuserid);

      select FNapmwriteactivitylog(iuserid , tuseraction, icreateraction , tactiondesc) into taactivityid;

      select FNapmsetfirstpassflag(iuserid , 1, 0 , 0) into tflagreset;
      set omess = '1#Successfully updated password.';

    else

      set tactiondesc = concat('Admin with userid ', iadminpassword, ' has tried to updated password for userid ',iuserid);

      select FNapmwriteactivitylog(iuserid , tuseraction, icreateraction , tactiondesc) into taactivityid;


      set omess = '0#Failed to update password';
    end if;

else
  select isfirstpass into tvaliduserpass from apmusers where userid = iuserid and statusid = tuserStatusIdActive;
   if ifirstflag = 0 and iisadminreset = 1 and tvaliduserpass = 0 then

          set omess = '4# User already activated his account.';
   elseif iisadminreset = 0 and tvaliduserpass = 1 then

          set omess = '5# Flag was reseted, logout and change the password.';
   else
    if iisadminreset = 1 then

      if ifirstflag = 1 then
        select distinct a.userid into tvaliduserid from apmusers a, apmpasswordhistory b where a.userid = iuserid and a.userid = b.userid
        and a.statusid = b.statusid and a.statusid = tuserStatusIdActive;
      else
 -- select "Here";
        select distinct a.userid into tvaliduserid from apmusers a, apmpasswordhistory b where a.userid = iuserid and a.userid = b.userid
        and a.password = topassword and a.statusid = b.statusid and a.statusid = tuserStatusIdActive;
      end if;

    else

      select distinct a.userid into tvaliduserid from apmusers a, apmpasswordhistory b where a.userid = iuserid and a.userid = b.userid
      and a.password = topassword and a.statusid = b.statusid and a.statusid = tuserStatusIdActive;

    end if;

--  select distinct a.userid into tvaliduserid from apmusers a, apmpasswordhistory b where a.userid = iuserid and a.userid = b.userid
--  and a.password = topassword and a.statusid = b.statusid and a.statusid = tuserStatusIdActive;

  if tvaliduserid != 0 then



    select count(userpass) into tpasscnt
    from (select userpassword as userpass from apmpasswordhistory where
    userid = tvaliduserid ORDER BY psid DESC LIMIT 0,tmaxpass) as obj where userpass=tnpassword;

    if tpasscnt = 0 then

      update apmusers set password = tnpassword where userid = tvaliduserid and statusid=tuserStatusIdActive;

      select row_count() into tRowsCount;

      if tRowsCount > 0 then

        update apmpasswordhistory set statusid = tuserStatusIdInactive where userid = iuserid;

        insert into apmpasswordhistory(userid, userpassword, createddatetime, statusid)
        values
        (tvaliduserid, tnpassword,now(),tuserStatusIdActive);

        set tactiondesc = concat('User has updated his password with userid ',tvaliduserid);
        select FNapmwriteactivitylog(iuserid , tuseraction, icreateraction , tactiondesc) into taactivityid;

        if iisadminreset = 1 then

          select FNapmsetfirstpassflag(iuserid , ifirstflag, tbothflags , 0) into tflagreset;

        end if;

       set omess = '1#Successfully updated password.';

      else
        set tactiondesc = concat('User has tried to update his password with userid ',tvaliduserid);
        select FNapmwriteactivitylog(iuserid , tuseraction, icreateraction , tactiondesc) into taactivityid;

       set omess = '0#Failed to update password.';


      end if;




  else

    set tactiondesc = concat('Password repetition limit exceeded with userid ',tvaliduserid);
    select FNapmwriteactivitylog(iuserid , tuseraction, icreateraction , tactiondesc) into taactivityid;

     set omess = '2#Password already has been used. Please choose another.';

    end if;

  else
-- select tfirstpass;

       set omess = '3# Invalid Old Password.';

  end if;
end if;

end if;


END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPapmchangeuserstatus`(
IN iuserid int(11),
IN ilockstatus int(1),
IN iunlockstatus int(1),
IN ideletestatus int(1),
IN iaction varchar(255),
IN iadminuserid int(11),
OUT omess varchar(255)
)
BEGIN




DECLARE tactivestatus int(11);
DECLARE tlockstatus int(11);
DECLARE tdeletestatus int(11);
DECLARE taactivityid int(11);
DECLARE tuseraction varchar(255);
DECLARE tactiondesc varchar(255);
DECLARE tusername varchar(255);
DECLARE temail varchar(255);

SELECT statusid into tactivestatus FROM apmmasterrecordsstate  where recordstate='Active';
SELECT statusid into tlockstatus FROM apmmasterrecordsstate  where recordstate='Locked';
SELECT statusid into tdeletestatus FROM apmmasterrecordsstate  where recordstate='Deleted';


select concat(firstname, ' ', lastname) into tusername from apmusers where userid = iuserid;
select emailid into temail from apmusers where userid = iuserid;

if ilockstatus = 1 and iunlockstatus = 0 and ideletestatus = 0 then
  set tuseraction = 'Lock user';

  update apmusers set statusid = tlockstatus where userid = iuserid;

  set tactiondesc = concat('User with userid ',iuserid, ' was Locked by admin with userid ', iadminuserid);
  select FNapmwriteactivitylog(iuserid , tuseraction, iaction , tactiondesc) into taactivityid;


elseif ilockstatus = 0 and iunlockstatus = 1 and ideletestatus = 0 then

  set tuseraction = 'Unlock user';

  update apmusers set statusid = tactivestatus, passcounter = 0 where userid = iuserid;

  set tactiondesc = concat('User with userid ',iuserid, ' was Activated by admin with userid ', iadminuserid);
  select FNapmwriteactivitylog(iuserid , tuseraction, iaction , tactiondesc) into taactivityid;


elseif ilockstatus = 0 and iunlockstatus = 0 and ideletestatus = 1 then

  set tuseraction = 'Delete user';

  update apmusers set statusid = tdeletestatus, deleteddatetime = now() where userid = iuserid;


  set tactiondesc = concat('User with userid ',iuserid, ' was Deleted by admin with userid ', iadminuserid);
  select FNapmwriteactivitylog(iuserid , tuseraction, iaction , tactiondesc) into taactivityid;


end if;

  set omess = concat('1#', tusername, '#', temail);
  select omess;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPapmchecksecurityqa`(
IN iaction varchar(255),
IN iuserid int(11),
IN isecureid int(11),
IN ianswer varchar(255),
IN imaxanshits int(11),
IN iforgot int(1),
OUT omess varchar(255))
BEGIN


Declare tQuestionStatusIdActive int(11);
Declare tUserStatusIdLocked int(11);
Declare taactivityid int(11);
Declare tactiondesc varchar(300);
Declare trowcount int(11) default 0;
Declare tcount int(11);
Declare twrongseccount int(11) default 0;
Declare tCountlog int(11);
Declare tCheckstatus int(11);
Declare tuseraction varchar(255);


if iforgot = 1 then
  set tuseraction = 'Forgot Password';
elseif iforgot = 0 then
  set tuseraction = 'Answer security questions';
end if;


SELECT statusid into tQuestionStatusIdActive FROM apmmasterrecordsstate  where recordstate='Active';
SELECT statusid into tUserStatusIdLocked FROM apmmasterrecordsstate  where recordstate='Locked';



if iforgot = 1 then

  select count(answerid) into trowcount from apmsecurityqa where answerid = isecureid and answer = sha2(ianswer, 256) and userid = iuserid;


else

  select count(answerid) into trowcount from apmsecurityqa where securityquestionid = isecureid and answer = sha2(ianswer, 256) and userid = iuserid;

end if;

  if trowcount <> 0 then

    if iforgot = 1 then

      set tactiondesc = concat('User applied for forgotpassword with userid ',iuserid, ' and questionid ',isecureid);
      set omess = concat(trowcount, '#Successfully set password');

    elseif iforgot = 0 then

      set tactiondesc = concat('User has logged in with userid ',iuserid, ' and questionid ',isecureid);
      set omess = concat(trowcount, '#Successfully logged in');

    end if;

    select FNapmwriteactivitylog(iuserid , tuseraction, iaction , tactiondesc) into taactivityid;

  else
          select statusid into tCheckstatus from apmusers where userid = iuserid;
    if tCheckstatus = tUserStatusIdLocked then
      set omess = '2#User has been locked. Please contact local administrator.';
    else
      if imaxanshits <> 0 then
          select seccounter into tCountlog from apmusers where userid = iuserid and statusid = tQuestionStatusIdActive;

          if tCountlog >= (imaxanshits - 1) then
            update apmusers set statusid = tUserStatusIdLocked where userid = iuserid;
            update apmusers set seccounter = 0 where userid = iuserid;

           set tactiondesc = concat('User with userid ',iuserid, ' was locked for entering wrong answer ',imaxanshits, ' times');
           select FNapmwriteactivitylog(iuserid , tuseraction, iaction , tactiondesc) into taactivityid;

           set omess = '2#User has been locked. Please contact local administrator.';

          else
            set twrongseccount = tCountlog + 1;
            update apmusers set seccounter = twrongseccount where userid = iuserid;

           set tactiondesc = concat('User with userid ',iuserid, ' has entered wrong answer ',twrongseccount, ' times');
           select FNapmwriteactivitylog(iuserid , tuseraction, iaction , tactiondesc) into taactivityid;

            set omess = '0#Invalid answer.';

          end if;
      else
          set tactiondesc = concat('Invalid security answer for userid ',iuserid);
          select FNapmwriteactivitylog(iuserid , tuseraction, iaction , tactiondesc) into taactivityid;
          set omess = concat(trowcount, '#Invalid answer');
          set omess = '0#Invalid answer.';
      end if;

    end if;

  end if;







END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPapmcheckuser`(
IN iusername varchar(255),
IN ipassword varchar(255),
IN iaction varchar(255),
IN icontrollername varchar(255),
IN imodulename varchar(255),
IN imaxpasshits int(11),
IN iuserid int(11))
BEGIN



Declare tUserStatusIdActive int(11);
Declare tUserStatusIdLocked int(11);
Declare tUserStatusIdDeleted int(11);
Declare tUserId int(11) default 0;
Declare taactivityid int(11);
Declare tactiondesc varchar(300);
Declare tUserpassId int(11) default 0;
Declare tpassexpiry int(11);
Declare tCountlog int(11);
Declare tStatusCheck int(11);
Declare twrongpasscount int(11) default 0;
Declare opassexpiry int(11);
Declare omess varchar(255);
Declare tuseraction varchar(255);
Declare tusername varchar(255);
Declare troleid int(11);

SELECT statusid into tUserStatusIdActive FROM apmmasterrecordsstate  where recordstate='Active';
SELECT statusid into tUserStatusIdLocked FROM apmmasterrecordsstate  where recordstate='Locked';
SELECT statusid into tUserStatusIdDeleted FROM apmmasterrecordsstate  where recordstate='Deleted';
SELECT roleid into troleid FROM apmmasterroles where rolename = 'User';


set tuseraction = 'Login';


if iuserid is not NULL then

  select count(d.firstname) as cnt
  from apmmasterroles a, apmuserrolemapping c, apmusers d
  where d.userid=iuserid and d.statusid=tUserStatusIdActive
   and
   c.userid = d.userid
   and c.statusid = d.statusid
   and a.roleid=c.roleid;
   set tactiondesc = concat('User activity is checked with ',iuserid);
   select FNapmwriteactivitylog(iuserid , tuseraction, iaction , tactiondesc) into taactivityid;

else


  select count(au.userid) into tUserId from apmusers au, apmuserrolemapping aurm
  where
  au.userid = aurm.userid
  AND roleid != troleid
  AND userloginid = iusername;

  if tUserId = 0 then

     set opassexpiry = 0;
     set omess = '3#User Name is Not matching';
     select opassexpiry, omess;

  else

     select userid into tUserId from apmusers where userloginid = iusername;
     -- select count(userid) into tUserpassId from apmusers where userloginid = iusername and password = sha2(ipassword,256) and statusid = tUserStatusIdActive;
     -- select concat(firstname, ' ', lastname) into tusername from apmusers where userloginid = iusername and password = sha2(ipassword,256) and statusid = tUserStatusIdActive;
     select count(userid) into tUserpassId from apmusers where userloginid = iusername and password = ipassword and statusid = tUserStatusIdActive;
     select concat(firstname, ' ', lastname) into tusername from apmusers where userloginid = iusername and password = ipassword and statusid = tUserStatusIdActive;

     if tUserpassId = 0 then

        select statusid into tStatusCheck from apmusers where userloginid = iusername;

        if tStatusCheck = tUserStatusIdLocked then

            set opassexpiry = 0;
            set omess = '2#User has been locked. Please contact local administrator.';
            set tactiondesc = concat('User has been already locked for ',tUserId);
            select FNapmwriteuseractivitylog(tUserId , tuseraction, iaction ,icontrollername ,imodulename, tactiondesc) into taactivityid;
            select opassexpiry, omess;

        elseif tStatusCheck = tUserStatusIdDeleted then

           set opassexpiry = 0;
           set omess = '4#User no longer exists.';
           set tactiondesc = concat('Trying to login for deleted user with ',tUserId);
           select FNapmwriteuseractivitylog(tUserId , tuseraction, iaction ,icontrollername ,imodulename, tactiondesc) into taactivityid;
           select opassexpiry, omess;

        else

        if imaxpasshits <> 0 then

          select passcounter into tCountlog from apmusers where userid = tUserId and statusid = tUserStatusIdActive;

          if tCountlog >= (imaxpasshits - 1) then

            update apmusers set statusid = tUserStatusIdLocked, passcounter = 0 where userid = tUserId;

            set tactiondesc = concat('Invalid password for ',tUserId);
            select FNapmwriteuseractivitylog(tUserId , tuseraction, iaction ,icontrollername ,imodulename, tactiondesc) into taactivityid;
            set opassexpiry = 0;
            set omess = '2#User has been locked. Please contact local administrator.';
            set tactiondesc = concat('User has been locked for ',tUserId);
            select FNapmwriteuseractivitylog(tUserId , tuseraction, iaction ,icontrollername ,imodulename, tactiondesc) into taactivityid;
            select opassexpiry, omess;

          else

            set twrongpasscount = tCountlog + 1;
            update apmusers set passcounter = twrongpasscount where userid = tUserId;

            set tactiondesc = concat('Invalid password for ',tUserId);
            select FNapmwriteuseractivitylog(tUserId , tuseraction, iaction ,icontrollername ,imodulename, tactiondesc) into taactivityid;
            set opassexpiry = 0;
            set omess = '0#Invalid Password entered.';
            select opassexpiry, omess;

          end if;

        else

          set tactiondesc = concat('Invalid password for ',tUserId);
          select FNapmwriteuseractivitylog(tUserId , tuseraction, iaction ,icontrollername ,imodulename, tactiondesc) into taactivityid;
          set opassexpiry = 0;
          set omess = '0#Invalid Password.';
          select opassexpiry, omess;

        end if; 

      end if; 

     else

       select DATEDIFF(now(), (select updateddatetime from apmpasswordhistory where userid=tUserId and statusid = tUserStatusIdActive order by updateddatetime desc limit 0,1)) into tpassexpiry ;
        
        update apmusers set passcounter = 0, seccounter = 0 where userid = tUserId;

       set tactiondesc = concat('User succefully logged in with ',tUserId);

       select FNapmwriteuseractivitylog(tUserId , tuseraction, iaction ,icontrollername ,imodulename, tactiondesc) into taactivityid;

        set opassexpiry = tpassexpiry;

       set omess = '1#User succefully logged in.';

select a.rolename as role, a.roleid as roleid, a.priority as priority,
       d.userid as userid, count(d.firstname) as cnt, d.isfirstpass as isfirstpass, d.emailid as email, d.issecured as issecured,
       concat(d.firstname, ' ', d.lastname) as name, opassexpiry, omess
       from apmmasterroles a, apmuserrolemapping c, apmusers d
       where
       d.userid=tUserId
       and d.statusid= tUserStatusIdActive
       and c.userid = d.userid and a.roleid=c.roleid;



     end if;



  end if;

end if;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPapmcheckuserexistance`(
IN iusername varchar(255),
IN iuseraction varchar(255)
)
BEGIN



Declare tUserStatusIdActive int(11);
Declare tUserStatusIdLocked int(11);
Declare tUserStatusIdDeleted int(11);
Declare tuserid int(11) default 0;
Declare tactiondesc varchar(255);
Declare tuseraction varchar(255);
Declare taactivityid int(11);

SELECT statusid into tUserStatusIdActive FROM apmmasterrecordsstate  where recordstate='Active';
SELECT statusid into tUserStatusIdLocked FROM apmmasterrecordsstate  where recordstate='Locked';
SELECT statusid into tUserStatusIdDeleted FROM apmmasterrecordsstate  where recordstate='Deleted';



select userid into tuserid from apmusers where userloginid = iusername and statusid = tUserStatusIdActive;

if tuserid = 0 then

  if iuseraction is null then

    select userid, count(userid) as cnt from apmusers where userloginid = iusername;

  else

    select userid, count(userid) as cnt from apmusers where userloginid = iusername and statusid = tUserStatusIdActive;

  end if;

else

  set tuseraction = 'Forgot Password';
  select userid, firstname, lastname, count(userid) as cnt, issecured, emailid from apmusers where userloginid = iusername and statusid = tUserStatusIdActive;
  set tactiondesc = concat('forgot user password for the id ',tuserid);
  select FNapmwriteactivitylog(tuserid , tuseraction, iuseraction , tactiondesc) into taactivityid;

end if;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPapmcountofusers`(
IN iusername varchar(255),
IN ifirstname varchar(255),
IN ilastname varchar(255),
IN irole int(11),
IN iuserid int(11)
)
BEGIN


Declare tuserStatusIdDeleted int(11);
declare tcond text;

SELECT statusid into tuserStatusIdDeleted FROM apmmasterrecordsstate  where recordstate='Deleted';

set tcond = '';

if iusername <> '0' then

  set tcond = concat(tcond," and  a.userloginid like '%", iusername , "%'");

end if;

if ifirstname <> '0' then

  set tcond = concat(tcond," and a.firstname like '%", ifirstname , "%'");

end if;

if ilastname <> '0' then

  set tcond = concat(tcond," and a.lastname like '%", ilastname , "%'");

end if;

if irole <> 0 then

  set tcond = concat(tcond," and d.roleid =", irole);

end if;



  set @tstatement=concat("select count(a.userid) as tusercount from apmusers a, apmmasterroles c, apmmasterrecordsstate b, apmuserrolemapping d
  where a.statusid !=", tuserStatusIdDeleted," and a. userid = d.userid and c.roleid = d.roleid and a.statusid = b.statusid
  and a.userid <> ",iuserid , tcond);



PREPARE stmt_name FROM @tstatement;
EXECUTE stmt_name;


END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPapmcreateuser`(
IN ifirstname varchar(255),
IN ilastname varchar(255),
IN iemailid varchar(255),
IN ipassword varchar(255),
IN iusername varchar(255),
IN iphonenumber double(10,0),
IN iuserrole int(11),
IN imerchant_id int(11),
IN iaction varchar(255),
IN iadmin int(11)
)
BEGIN





declare tcountcheck int(11) default 0;
declare tcuruseraction varchar(255);
declare tactiondesc varchar(255);
declare tusercount int(5) default 0;
declare tsuccess int(11);
declare tcount int(11);
declare tuserid int(11);
declare tUserStatusIdActive int(11);
declare taactivityid int(11);
declare toutput varchar(255);
declare tappid int(11);
declare trolename varchar(100);




set tcuruseraction = 'Add User';
set tactiondesc = '';


SELECT statusid into tUserStatusIdActive FROM apmmasterrecordsstate  where recordstate='Active';

-- select appid into tappid from apmmasterapps where appname = iapp and statusid = tUserStatusIdActive;

-- select a.usertypeid into tcount from apmmasterusertypes a, apmmasterroles b where a.usertypeid = b.usertypeid and b.roleid = iuserrole;

select count(emailid) into tusercount from apmusers where userloginid = iusername;

if tusercount <> 0 then
    set tactiondesc = concat('Unable to create user with email ', iemailid , ' as the email already exists.');
    set toutput = '0#Failed to register User';

else
        /*insert into apmusers(firstname, lastname, emailid, password, userloginid, phonenumber, createddatetime, statusid)
        values
        (ifirstname, ilastname, iemailid, SHA2(ipassword, 256), iusername, iphonenumber, NOW(), tUserStatusIdActive);
        */

        insert into apmusers(firstname, lastname, emailid, password, userloginid, phonenumber, createddatetime, statusid)
        values
        (ifirstname, ilastname, iemailid, ipassword, iusername, iphonenumber, NOW(), tUserStatusIdActive);
        select LAST_INSERT_ID() into tuserid;

        /*
        insert into apmpasswordhistory(userid, userpassword, createddatetime, statusid)
        values
        (tuserid,  SHA2(ipassword, 256), now(), tUserStatusIdActive);
        */

        insert into apmpasswordhistory(userid, userpassword, createddatetime, statusid)
        values
        (tuserid,  ipassword, now(), tUserStatusIdActive);

        insert into apmuserrolemapping(roleid, userid, createddatetime, statusid)
        values
        (iuserrole, tuserid, NOW(), tUserStatusIdActive);
        select rolename into trolename from apmmasterroles where roleid = iuserrole;

        if imerchant_id<>'' then
            insert into store_merchants_users(merchant_id, userid, createddatetime, statusid)
            values
            (imerchant_id, tuserid, NOW(), tUserStatusIdActive);
        end if;

    


          set tactiondesc = concat('User created with email ', iemailid, ' and userid ', iusername , ' by admin ', iadmin);
          set toutput = concat('1#', trolename, '#User Registered successfully');
  
  
  
  

end if;



select FNapmwriteactivitylog(iadmin , tcuruseraction, iaction , tactiondesc) into taactivityid;

  
select toutput;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPapmfetchemailtemplate`(
IN itemplatename varchar(255),
IN iresellername varchar(255),
IN iappname varchar(255)
)
BEGIN

Declare ttemplateactive int(11);
declare tresellerid int(11);
declare tappid int(11);



-- select resellerid into tresellerid from apmresellers where resellername=iresellername;
-- SELECT appid into tappid FROM apmmasterapps where appname=iappname;
select statusid into ttemplateactive from apmmasterrecordsstate where recordstate = 'Active';

select emailtemplateid, emailtemplatename, emailcontent, emailsubject, emailfrom from apmemailtemplate where emailtemplatename = itemplatename and statusid =ttemplateactive;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPapmfetchsecurityquestions`(IN iuserid int(11))
BEGIN


Declare tQuestionStatusIdActive int(11);
Declare tsecqnid int(11) default 0;

SELECT statusid into tQuestionStatusIdActive FROM apmmasterrecordsstate  where recordstate='Active';

if iuserid is null then

  SELECT securityquestionid, securityquestion FROM apmmastersecurityquestions where statusid=tQuestionStatusIdActive;

else

  select securityquestionid into tsecqnid from apmsecurityqa where userid=iuserid and statusid=tQuestionStatusIdActive;

  if tsecqnid > 0 then

    select a.securityquestionid, a.securityquestion
    from apmmastersecurityquestions a, apmsecurityqa b
    where b.userid=iuserid and a.statusid=b.statusid and
    a.securityquestionid = b.securityquestionid and b.statusid = tQuestionStatusIdActive;

  else

    select answerid as securityquestionid, securityquestion from apmsecurityqa where userid=iuserid and statusid=tQuestionStatusIdActive;

  end if;

end if;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPapmgetcontrollers`(
IN imoduleid int(11)
)
BEGIN


Declare tmoduleStatusIdActive int(11);

SELECT statusid into tmoduleStatusIdActive FROM apmmasterrecordsstate  where recordstate='Active';

-- select controllerid, controllername from apmmastercontrollers where moduleid = imoduleid AND statusid=tmoduleStatusIdActive;
select c.controllerid, c.controllername, m.moduleid, m.modulename
from apmmastercontrollers c, apmmastermodules m
where
-- moduleid = imoduleid
c.moduleid = m.moduleid
AND
c.statusid=tmoduleStatusIdActive;


END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPapmgetmailqueue`()
BEGIN

Declare tmoduleStatusIdActive int(11);
Declare tmoduleStatusIdemailnotsent int(11);

SELECT statusid into tmoduleStatusIdActive FROM apmmasterrecordsstate  where recordstate='Active';
SELECT mailstatusid into tmoduleStatusIdemailnotsent FROM apmmastermailstatus  where mailstate='emailnotsent';


select * from apmmailqueue where statusid=tmoduleStatusIdActive and mailstatus=tmoduleStatusIdemailnotsent limit 0,9;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPapmgetmodules`()
BEGIN

Declare tmoduleStatusIdActive int(11);

SELECT statusid into tmoduleStatusIdActive FROM apmmasterrecordsstate  where recordstate='Active';

select moduleid, modulename from apmmastermodules where statusid=tmoduleStatusIdActive;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPapmgetrolprivileges`()
BEGIN


Declare tusertypeStatusIdActive int(11);

SELECT statusid into tusertypeStatusIdActive FROM apmmasterrecordsstate  where recordstate='Active';

select a.rolename, b.modulename, c.controllername, d.actionname from apmmasterroles a, apmmastermodules b, apmmastercontrollers c, apmmasteractions d,
apmmasterroleprivileges e where e.roleid = a.roleid and e.actionid=d.actionid and c.controllerid = d.controllerid and c.moduleid = b.moduleid
and e.statusid = tusertypeStatusIdActive
order by a.rolename, b.modulename, c.controllername, d.actionname;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPapmgetuserdetails`(IN iuserid int(11))
BEGIN



Declare tUserStatusIdActive int(11);

SELECT statusid into tUserStatusIdActive FROM apmmasterrecordsstate  where recordstate='Active';

  select a.rolename as role, d.firstname as firstname, d.lastname as lastname, d.emailid as username, d.phonenumber as phonenumber, d.userloginid as userloginid, a.roleid as roleid, a.priority as priority,
  -- b.usertypename as usertype, b.usertypeid as usertypeid,
     d.userid as userid, count(d.firstname) as cnt, d.isfirstpass as isfirstpass, d.issecured as issecured,
     concat(d.firstname, ' ', d.lastname) as name, d.statusid as stat
     from apmmasterroles a, apmuserrolemapping c, apmusers d
     where d.userid=iuserid
     and c.userid = d.userid and a.roleid=c.roleid;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPapmgetusers`(
IN iuserid int(11),
IN istart int(11),
IN ilimit int(11),
IN iusername varchar(255),
IN ifirstname varchar(255),
IN ilastname varchar(255),
IN irole int(11)
 )
BEGIN


Declare tuserStatusIdActive int(11);
Declare tuserStatusIdDeleted int(11);
Declare tusertype int(11);
declare tcond text;

SELECT statusid into tuserStatusIdActive FROM apmmasterrecordsstate  where recordstate='Active';
SELECT statusid into tuserStatusIdDeleted FROM apmmasterrecordsstate  where recordstate='Deleted';
-- select a.usertypeid into tusertype from apmmasterroles a, apmuserrolemapping b where b.userid = iuserid and a.roleid = b.roleid;


set tcond = '';

if iusername <> '0' then

  set tcond = concat(tcond," and  a.userloginid like '%", iusername , "%'");

end if;

if ifirstname <> '0' then

  set tcond = concat(tcond," and a.firstname like '%", ifirstname , "%'");

end if;

if ilastname <> '0' then

  set tcond = concat(tcond," and a.lastname like '%", ilastname , "%'");

end if;

if irole <> 0 then

  set tcond = concat(tcond," and d.roleid =", irole);

end if;

/*
set @tstatement=concat("select a.lastname as lname ,a.firstname as fname, a.userid as userid, a.emailid as email, a.userloginid as userloginid, b.recordstate as status,
 c.rolename as role from apmusers a, apmmasterroles c, apmmasterrecordsstate b, apmuserrolemapping d
where a.statusid !=", tuserStatusIdDeleted," and a. userid =d.userid and c.roleid = d.roleid
and a.statusid = b.statusid and a.userid !=", iuserid, tcond ," order by a.lastname, a.firstname limit ", istart,",", ilimit);


set @tstatement=concat("select a.lastname as lname ,a.firstname as fname, a.userid as userid, a.emailid as email, a.userloginid as userloginid, b.recordstate as status,
 c.rolename as role from apmusers a, apmmasterroles c, apmmasterrecordsstate b, apmuserrolemapping d
where a.statusid !=", tuserStatusIdDeleted," and a. userid =d.userid and c.roleid = d.roleid
and a.statusid = b.statusid and a.userid !=", iuserid, tcond ," order by a.lastname, a.firstname  ");
*/

set @tstatement=concat("select a.lastname as lname ,a.firstname as fname, a.userid as userid, a.emailid as email, a.userloginid as userloginid, b.recordstate as status,
 c.rolename as role from apmusers a, apmmasterroles c, apmmasterrecordsstate b, apmuserrolemapping d
where a.statusid !=", tuserStatusIdDeleted," and a. userid =d.userid and c.roleid = d.roleid
and a.statusid = b.statusid ", tcond ," order by a.lastname, a.firstname  ");




PREPARE stmt_name FROM @tstatement;
EXECUTE stmt_name;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPapmgetusertypesroles`()
BEGIN


Declare tusertypeStatusIdActive int(11);

SELECT statusid into tusertypeStatusIdActive FROM apmmasterrecordsstate  where recordstate='Active';


select
b.rolename as role,
b.priority as priority,
b.roleid as roleid
from apmmasterroles b where
b.statusid = tusertypeStatusIdActive order by b.priority;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPapmsavesecurityqueston`(
IN iquestion varchar(255),
IN ianswer varchar(255),
IN iuserid int(11),
IN iaction varchar(255),
IN iisupdate INT(1),
IN iadminuserid int(11),
OUT omess varchar(255))
BEGIN

Declare tQuestionStatusIdActive int(11);
Declare tflagreset int(11);
Declare taactivityid int(11);
Declare tactiondesc varchar(300);
Declare trowcount int(11) default 0;
Declare tcount int(11);
Declare tcuruseraction varchar(255);
Declare tfqaid int(11);
Declare tusername varchar(500);
Declare tsecure int(1);



SELECT statusid into tQuestionStatusIdActive FROM apmmasterrecordsstate  where recordstate='Active';
select concat(firstname, ' ', lastname) into tusername from apmusers where userid = iuserid;


if iisupdate = 2 then

  set tcuruseraction = 'Reset Security';
  select FNapmsetfirstpassflag(iuserid , 0, 1 , 0) into trowcount;

  set tactiondesc = concat('Admin with userid ', iadminuserid, ' resets security questions for userid ',iuserid);
  select FNapmwriteactivitylog(iuserid , tcuruseraction, iaction , tactiondesc) into taactivityid;

  set omess = concat('1#', tusername);



elseif iisupdate = 1 then

  set tcuruseraction = 'Update security questions';
  select issecured into tsecure from apmusers where userid = iuserid;
  if tsecure = 0 then
    set omess = concat('0#Security questions are updated successfully for ' , tusername);
  else

    select answerid into tfqaid from apmsecurityqa where userid = iuserid and statusid = tQuestionStatusIdActive limit 0,1;


    update apmsecurityqa set securityquestion = iquestion, answer = sha2(ianswer, 256) where answerid = tfqaid and userid = iuserid;

    select row_count() into trowcount;

    set tactiondesc = concat('Security questions were updated for userid ',iuserid);
    select FNapmwriteactivitylog(iuserid , tcuruseraction, iaction , tactiondesc) into taactivityid;


    if trowcount <> 0 then
      set omess = concat('1#Security questions are updated successfully for ' , tusername);
    else
      set omess = concat('1#Unable to update security questions ', tusername);
    end if;

  end if;

elseif iisupdate = 0 then
  set tcuruseraction = 'Register security questions';
  select count(userid) into tcount from apmsecurityqa where userid = iuserid and statusid = tQuestionStatusIdActive;

  if tcount = 0 then
    select FNapmsetfirstpassflag(iuserid , 0, 1 , 1) into tflagreset;
    insert into apmsecurityqa(userid, securityquestion, answer, createddatetime, statusid)
    values
    (iuserid, iquestion,sha2(ianswer, 256),now(),tQuestionStatusIdActive);
    select row_count() into trowcount;

    set tactiondesc = concat('Security questions were added for userid ',iuserid);
    select FNapmwriteactivitylog(iuserid , tcuruseraction, iaction , tactiondesc) into taactivityid;



    if trowcount <> 0 then
      set omess = concat(trowcount, '#Security questions are added successfully for ', tusername);
    else
      set omess = concat(trowcount, '#Unable to add security questions for ', tusername);
    end if;
  else
     set omess = concat('0#User already added security questions for ', tusername);
  end if;

end if;






END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPapmupdateuserdetails`(
IN iuserid int(11),
IN ifirstname varchar(255),
IN ilastname varchar(255),
IN iemail varchar(255),
IN iphonenumber double(10,0),
IN irole int(11),
IN iapp varchar(255),
IN iaction varchar(255),
IN iadmin int(11)
)
BEGIN

Declare tuserStatusIdActive int(11);
Declare tuserStatusIdDeleted int(11);
Declare tappid int(11);
Declare trowcount int(11);
Declare tactiondesc varchar(255);
Declare taactivityid int(11);
Declare tcuruseraction varchar(255);
Declare tmess varchar(255);
Declare tstatus int(11);


SELECT statusid into tuserStatusIdActive FROM apmmasterrecordsstate  where recordstate='Active';
SELECT statusid into tuserStatusIdDeleted FROM apmmasterrecordsstate  where recordstate='Deleted';
-- select appid into tappid from apmmasterapps where appname = iapp and statusid = tuserStatusIdActive;

select statusid into tstatus from apmusers where userid = iuserid;
if tstatus <> tuserStatusIdDeleted then

-- update apmusers set firstname = ifirstname, lastname = ilastname, emailid = iemail, phonenumber = iphonenumber where userid = iuserid;

update apmusers set firstname = ifirstname, lastname = ilastname, phonenumber = iphonenumber where userid = iuserid;


if iadmin <> 0 then

  -- update apmuserrolemapping set roleid = irole where userid = iuserid;
  set tcuruseraction = 'Edit user';
  set tactiondesc = concat('Details were updated for userid ',iuserid, ' by ', iadmin);

else

  set tcuruseraction = 'update personal info';
  set tactiondesc = concat('Details were updated by ',iuserid);

end if;

select FNapmwriteactivitylog(iadmin , tcuruseraction, iaction , tactiondesc) into taactivityid;
set tmess = '1#success';
select tmess;

else
  set tmess = '2#failure';
  select tmess;
end if;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPattributeadd`(
IN iattribute_title varchar(255),
IN iattribute_field_type varchar(255),
IN iattribute_data_type varchar(255),
IN iattribute_values varchar(2555),
IN iaction varchar(255),
IN iadmin int(11)
)
BEGIN


declare tcountcheck int(11) default 0;
declare tcuruseraction varchar(255);
declare tactiondesc varchar(255);
declare tattributecount int(5) default 0;
declare tsuccess int(11);
declare tcount int(11);
declare tattributeid int(11);
declare tUserStatusIdActive int(11);
declare taactivityid int(11);
declare toutput varchar(255);



set tcuruseraction = 'Add Attribute';
set tactiondesc = '';


SELECT statusid into tUserStatusIdActive FROM apmmasterrecordsstate  where recordstate='Active';



select count(attribute_id) into tattributecount from store_products_attributes where attribute_title = iattribute_title;

if tattributecount <> 0 then
    set tactiondesc = concat('Unable to create attribute with ', iattribute_title , ' as the attribute title already exists.');
    set toutput = '0#Failed to register attribute';

else


			insert into store_products_attributes
			(attribute_title,
			attribute_field_type,
			attribute_data_type,
      attribute_values,
			createddatetime,
			statusid)
			values
			(
			iattribute_title,
			iattribute_field_type,
			iattribute_data_type,
      iattribute_values,
			NOW(),
			tUserStatusIdActive);

      select LAST_INSERT_ID() into tattributeid;


      set tactiondesc = concat('Attribute created with title ', iattribute_title, ' and attribute_id ', tattributeid , ' by admin ', iadmin);
      set toutput = concat('1#',tattributeid,'#', iattribute_title, '#Attribute Registered successfully');



end if;


      select FNapmwriteactivitylog(iadmin , tcuruseraction, iaction , tactiondesc) into taactivityid;


select toutput;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPattributechangestatus`(
IN iattribute_id int(11),
IN ilockstatus int(1),
IN iunlockstatus int(1),
IN ideletestatus int(1),
IN iaction varchar(255),
IN iadminid int(11),
OUT omess varchar(255)
)
BEGIN


DECLARE tactivestatus int(11);
DECLARE tlockstatus int(11);
DECLARE tdeletestatus int(11);
DECLARE taactivityid int(11);
DECLARE tcategoryaction varchar(255);
DECLARE tactiondesc varchar(255);
DECLARE tattribute_title varchar(255);



SELECT statusid into tactivestatus FROM apmmasterrecordsstate  where recordstate='Active';
SELECT statusid into tlockstatus FROM apmmasterrecordsstate  where recordstate='Locked';
SELECT statusid into tdeletestatus FROM apmmasterrecordsstate  where recordstate='Deleted';

select attribute_title into tattribute_title from store_products_attributes where attribute_id = iattribute_id;


if ilockstatus = 1 and iunlockstatus = 0 and ideletestatus = 0 then
  set tcategoryaction = 'Lock attribute';

  update store_products_attributes set statusid = tlockstatus where attribute_id = iattribute_id;

  set tactiondesc = concat('Attribute with attribute_id ',iattribute_id, ' was Locked by admin with adminid ', iadminid);
  select FNapmwriteactivitylog(iadminid , tcategoryaction, iaction , tactiondesc) into taactivityid;


elseif ilockstatus = 0 and iunlockstatus = 1 and ideletestatus = 0 then

  set tcategoryaction = 'Unlock attribute';

  update store_products_attributes set statusid = tactivestatus where attribute_id = iattribute_id;

  set tactiondesc = concat('Attribute with attribute_id ',iattribute_id, ' was Activated by admin with adminid ', iadminid);
  select FNapmwriteactivitylog(iadminid , tcategoryaction, iaction , tactiondesc) into taactivityid;


elseif ilockstatus = 0 and iunlockstatus = 0 and ideletestatus = 1 then

  set tcategoryaction = 'Delete attribute';

  update store_products_attributes set statusid = tdeletestatus, deleteddatetime = now() where attribute_id = iattribute_id;


  set tactiondesc = concat('Attribute with attribute_id ',iattribute_id, ' was Deleted by admin with adminid ', iadminid);
  select FNapmwriteactivitylog(iadminid , tcategoryaction, iaction , tactiondesc) into taactivityid;


end if;

  set omess = concat('1#', tattribute_title, '#', '');
  select omess;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPattributedetails`(
IN iattribute_id int(11)
)
BEGIN

set @tstatement=concat("SELECT * FROM store_products_attributes  where attribute_id = ", iattribute_id);
-- select @tstatement;

PREPARE stmt_name FROM @tstatement;
EXECUTE stmt_name;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPattributeedit`(
IN iattribute_id int(11),
IN iattribute_title varchar(255),
IN iattribute_field_type varchar(255),
IN iattribute_data_type varchar(255),
IN iattribute_values varchar(2555),
IN iaction varchar(255),
IN iadmin int(11)
)
BEGIN

Declare tuserStatusIdActive int(11);
Declare tuserStatusIdDeleted int(11);
Declare trowcount int(11);
Declare tactiondesc varchar(255);
Declare taactivityid int(11);
Declare tcuruseraction varchar(255);
Declare tmess varchar(255);
Declare tstatus int(11);


SELECT statusid into tuserStatusIdActive FROM apmmasterrecordsstate  where recordstate='Active';
SELECT statusid into tuserStatusIdDeleted FROM apmmasterrecordsstate  where recordstate='Deleted';


select statusid into tstatus from store_products_attributes where attribute_id = iattribute_id;
if tstatus <> tuserStatusIdDeleted then

      update store_products_attributes set
      attribute_title         = iattribute_title,
      attribute_field_type    = iattribute_field_type,
      attribute_data_type     = iattribute_data_type,
      attribute_values        = iattribute_values
      where attribute_id      = iattribute_id;

      set tcuruseraction = 'Edit attribute';
      set tactiondesc = concat('Details were updated for attribute_id ',iattribute_id, ' by ', iadmin);



      select FNapmwriteactivitylog(iadmin , tcuruseraction, iaction , tactiondesc) into taactivityid;
      set tmess = '1#success';
      select tmess;

else
  set tmess = '2#failure';
  select tmess;
end if;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPattributegroupadd`(
IN iattributes_group_title varchar(255),
IN iaction varchar(255),
IN iadmin int(11)
)
BEGIN


declare tcountcheck int(11) default 0;
declare tcuruseraction varchar(255);
declare tactiondesc varchar(255);
declare tattributegroupcount int(5) default 0;
declare tsuccess int(11);
declare tcount int(11);
declare tattributegroupid int(11);
declare tUserStatusIdActive int(11);
declare taactivityid int(11);
declare toutput varchar(255);



set tcuruseraction = 'Add Attribute Group';
set tactiondesc = '';


SELECT statusid into tUserStatusIdActive FROM apmmasterrecordsstate  where recordstate='Active';



select count(attributes_group_id) into tattributegroupcount from store_products_attributes_groups where attributes_group_title = iattributes_group_title;

if tattributegroupcount <> 0 then
    set tactiondesc = concat('Unable to create attribute group with ', iattributes_group_title , ' as the attribute group title already exists.');
    set toutput = '0#Failed to register attribute group';

else


			insert into store_products_attributes_groups
			(attributes_group_title,
			createddatetime,
			statusid)
			values
			(
			iattributes_group_title,
			NOW(),
			tUserStatusIdActive);

      select LAST_INSERT_ID() into tattributegroupid;


      set tactiondesc = concat('Attribute group created with title ', iattributes_group_title, ' and attributes_group_id ', tattributegroupid , ' by admin ', iadmin);
      set toutput = concat('1#',tattributegroupid,'#', iattributes_group_title, '#Attribute Group Registered successfully');



end if;


      select FNapmwriteactivitylog(iadmin , tcuruseraction, iaction , tactiondesc) into taactivityid;


select toutput;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPattributegroupchangestatus`(
IN iattributes_group_id int(11),
IN ilockstatus int(1),
IN iunlockstatus int(1),
IN ideletestatus int(1),
IN iaction varchar(255),
IN iadminid int(11),
OUT omess varchar(255)
)
BEGIN


DECLARE tactivestatus int(11);
DECLARE tlockstatus int(11);
DECLARE tdeletestatus int(11);
DECLARE taactivityid int(11);
DECLARE tattributeaction varchar(255);
DECLARE tactiondesc varchar(255);
DECLARE tattributes_group_title varchar(255);



SELECT statusid into tactivestatus FROM apmmasterrecordsstate  where recordstate='Active';
SELECT statusid into tlockstatus FROM apmmasterrecordsstate  where recordstate='Locked';
SELECT statusid into tdeletestatus FROM apmmasterrecordsstate  where recordstate='Deleted';

select attributes_group_title into tattributes_group_title from store_products_attributes_groups where attributes_group_id = iattributes_group_id;


if ilockstatus = 1 and iunlockstatus = 0 and ideletestatus = 0 then
  set tattributeaction = 'Lock attribute group';

  update store_products_attributes_groups set statusid = tlockstatus where attributes_group_id = iattributes_group_id;

  set tactiondesc = concat('Attribute group with attributes_group_id ',iattributes_group_id, ' was Locked by admin with adminid ', iadminid);
  select FNapmwriteactivitylog(iadminid , tattributeaction, iaction , tactiondesc) into taactivityid;


elseif ilockstatus = 0 and iunlockstatus = 1 and ideletestatus = 0 then

  set tattributeaction = 'Unlock attribute group';

  update store_products_attributes_groups set statusid = tactivestatus where attributes_group_id = iattributes_group_id;

  set tactiondesc = concat('Attribute group with attributes_group_id ',iattributes_group_id, ' was Activated by admin with adminid ', iadminid);
  select FNapmwriteactivitylog(iadminid , tattributeaction, iaction , tactiondesc) into taactivityid;


elseif ilockstatus = 0 and iunlockstatus = 0 and ideletestatus = 1 then

  set tattributeaction = 'Delete attribute group';

  update store_products_attributes_groups set statusid = tdeletestatus, deleteddatetime = now() where attributes_group_id = iattributes_group_id;


  set tactiondesc = concat('Attribute group with attributes_group_id ',iattributes_group_id, ' was Deleted by admin with adminid ', iadminid);
  select FNapmwriteactivitylog(iadminid , tattributeaction, iaction , tactiondesc) into taactivityid;


end if;

  set omess = concat('1#', tattributes_group_title, '#', '');
  select omess;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPattributegroupdetails`(
IN iattribute_group_id int(11)
)
BEGIN

set @tstatement=concat("SELECT * FROM store_products_attributes_groups  where attributes_group_id = ", iattribute_group_id);
-- select @tstatement;

PREPARE stmt_name FROM @tstatement;
EXECUTE stmt_name;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPattributegroupedit`(
IN iattributes_group_id int(11),
IN iattributes_group_title varchar(255),
IN iaction varchar(255),
IN iadmin int(11)
)
BEGIN

Declare tuserStatusIdActive int(11);
Declare tuserStatusIdDeleted int(11);
Declare trowcount int(11);
Declare tactiondesc varchar(255);
Declare taactivityid int(11);
Declare tcuruseraction varchar(255);
Declare tmess varchar(255);
Declare tstatus int(11);


SELECT statusid into tuserStatusIdActive FROM apmmasterrecordsstate  where recordstate='Active';
SELECT statusid into tuserStatusIdDeleted FROM apmmasterrecordsstate  where recordstate='Deleted';


select statusid into tstatus from store_products_attributes_groups where attributes_group_id = iattributes_group_id;
if tstatus <> tuserStatusIdDeleted then

      update store_products_attributes_groups set
      attributes_group_title         = iattributes_group_title
      where attributes_group_id      = iattributes_group_id;

      set tcuruseraction = 'Edit attribute group';
      set tactiondesc = concat('Details were updated for attributes_group_id ',iattributes_group_id, ' by ', iadmin);



      select FNapmwriteactivitylog(iadmin , tcuruseraction, iaction , tactiondesc) into taactivityid;
      set tmess = '1#success';
      select tmess;

else
  set tmess = '2#failure';
  select tmess;
end if;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPattributegrouplist`(
IN iattribute_group_title varchar(255),
IN istart int(11),
IN ilimit int(11)
)
BEGIN


Declare tuserStatusIdActive int(11);
Declare tuserStatusIdDeleted int(11);
declare tcond text;

SELECT statusid into tuserStatusIdActive FROM apmmasterrecordsstate  where recordstate='Active';
SELECT statusid into tuserStatusIdDeleted FROM apmmasterrecordsstate  where recordstate='Deleted';


set tcond = '';

if iattribute_group_title <> '' then

  set tcond = concat(tcond," and  a.attributes_group_title like '%", iattribute_group_title , "%'");

end if;




set @tstatement=concat("select * from store_products_attributes_groups a, apmmasterrecordsstate b
where
a.statusid !=", tuserStatusIdDeleted," and a.statusid = b.statusid order by a.attributes_group_title ");


-- select @tstatement;

PREPARE stmt_name FROM @tstatement;
EXECUTE stmt_name;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPattributegroupmapsave`(
IN iattributes_group_id int(11),
IN iattributes_group_map text,
out omessage varchar(255)
)
BEGIN

Declare tuserStatusIdActive int(11);
SELECT statusid into tuserStatusIdActive FROM apmmasterrecordsstate  where recordstate='Active';

delete from store_products_attributes_sets_mapping where attributes_set_id IN (select attributes_set_id from store_products_attributes_sets where attributes_group_id = iattributes_group_id);

SELECT LENGTH(iattributes_group_map) - LENGTH(REPLACE(iattributes_group_map, '-', '')) into @iattributes_id_set_count;
-- select @iattributes_id_set_count;

WHILE @iattributes_id_set_count > 0 DO

      select sfSPLIT_STR(iattributes_group_map,'-',@iattributes_id_set_count) into @iattributes_id_set_each;

      -- select @iattributes_id_set_each;

      select sfSPLIT_STR(@iattributes_id_set_each,'#',1) into @iattributes_id_set_each_id;
      select sfSPLIT_STR(@iattributes_id_set_each,'#',2) into @iattributes_id_set_each_attribute_id;
      -- select @iattributes_id_set_each_id,'----',@iattributes_id_set_each_attribute_id;




          SELECT LENGTH(@iattributes_id_set_each_attribute_id) - LENGTH(REPLACE(@iattributes_id_set_each_attribute_id, ',', '')) into @iattributes_id_set_each_attribute_id_count;
          WHILE @iattributes_id_set_each_attribute_id_count > 0 DO

              select sfSPLIT_STR(@iattributes_id_set_each_attribute_id,',',@iattributes_id_set_each_attribute_id_count) into @iattributes_id_set_each_attribute_id_each;
              -- select @iattributes_id_set_each_attribute_id_each;

              insert into store_products_attributes_sets_mapping
              (attribute_id, attributes_set_id, createddatetime, statusid)
              values(@iattributes_id_set_each_attribute_id_each,@iattributes_id_set_each_id,NOW(),tUserStatusIdActive);


          SET @iattributes_id_set_each_attribute_id_count = @iattributes_id_set_each_attribute_id_count - 1;
          END WHILE;



      SET @iattributes_id_set_count = @iattributes_id_set_count - 1;
END WHILE;


set omessage = 'Updated successfully';

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPattributelist`(
IN iattribute_title varchar(255),
IN istart int(11),
IN ilimit int(11)
)
BEGIN


Declare tuserStatusIdActive int(11);
Declare tuserStatusIdDeleted int(11);
declare tcond text;

SELECT statusid into tuserStatusIdActive FROM apmmasterrecordsstate  where recordstate='Active';
SELECT statusid into tuserStatusIdDeleted FROM apmmasterrecordsstate  where recordstate='Deleted';


set tcond = '';

if iattribute_title <> '' then

  set tcond = concat(tcond," and  a.attribute_title like '%", iattribute_title , "%'");

end if;




-- set @tstatement=concat("select *, (select category_name from store_categories where category_id=c.parent_category_id) as parent_category_name from store_categories c, apmmasterrecordsstate b
-- where
-- c.statusid !=", tuserStatusIdDeleted," and c.statusid = b.statusid order by c.category_name limit ", istart,",", ilimit);


set @tstatement=concat("select * from store_products_attributes a, apmmasterrecordsstate b
where
a.statusid !=", tuserStatusIdDeleted," and a.statusid = b.statusid order by a.attribute_title ");


-- select @tstatement;

PREPARE stmt_name FROM @tstatement;
EXECUTE stmt_name;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPattributesetschangestatus`(
IN iattributes_set_id int(11),
IN ilockstatus int(1),
IN iunlockstatus int(1),
IN ideletestatus int(1),
IN iaction varchar(255),
IN iadminid int(11),
OUT omess varchar(255)
)
BEGIN


DECLARE tactivestatus int(11);
DECLARE tlockstatus int(11);
DECLARE tdeletestatus int(11);
DECLARE taactivityid int(11);
DECLARE tattributesetsaction varchar(255);
DECLARE tactiondesc varchar(255);
DECLARE tattributes_set_title varchar(255);



SELECT statusid into tactivestatus FROM apmmasterrecordsstate  where recordstate='Active';
SELECT statusid into tlockstatus FROM apmmasterrecordsstate  where recordstate='Locked';
SELECT statusid into tdeletestatus FROM apmmasterrecordsstate  where recordstate='Deleted';

select attributes_set_title into tattributes_set_title from store_products_attributes_sets where attributes_set_id = iattributes_set_id;


if ilockstatus = 1 and iunlockstatus = 0 and ideletestatus = 0 then
  set tattributesetsaction = 'Lock attributesets';

  update store_products_attributes_sets set statusid = tlockstatus where attributes_set_id = iattributes_set_id;

  set tactiondesc = concat('Attributesets with attributes_set_id ',iattributes_set_id, ' was Locked by admin with adminid ', iadminid);
  select FNapmwriteactivitylog(iadminid , tattributesetsaction, iaction , tactiondesc) into taactivityid;


elseif ilockstatus = 0 and iunlockstatus = 1 and ideletestatus = 0 then

  set tattributesetsaction = 'Unlock attributesets';

  update store_products_attributes_sets set statusid = tactivestatus where attributes_set_id = iattributes_set_id;

  set tactiondesc = concat('Attributesets with attributes_set_id ',iattributes_set_id, ' was Activated by admin with adminid ', iadminid);
  select FNapmwriteactivitylog(iadminid , tattributesetsaction, iaction , tactiondesc) into taactivityid;


elseif ilockstatus = 0 and iunlockstatus = 0 and ideletestatus = 1 then

  set tattributesetsaction = 'Delete attributesets';

  update store_products_attributes_sets set statusid = tdeletestatus, deleteddatetime = now() where attributes_set_id = iattributes_set_id;


  set tactiondesc = concat('Attributesets with attributes_set_id ',iattributes_set_id, ' was Deleted by admin with adminid ', iadminid);
  select FNapmwriteactivitylog(iadminid , tattributesetsaction, iaction , tactiondesc) into taactivityid;


end if;

  set omess = concat('1#', tattributes_set_title, '#', '');
  select omess;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPattributesetscount`(
IN iattributes_set_title varchar(255)
)
BEGIN


Declare tuserStatusIdDeleted int(11);
declare tcond text;

SELECT statusid into tuserStatusIdDeleted FROM apmmasterrecordsstate  where recordstate='Deleted';

set tcond = '';

if iattributes_set_title <> '' then

  set tcond = concat(tcond," and  ats.attributes_set_title like '%", iattributes_set_title , "%'");

end if;


  set @tstatement=concat("select count(ats.attributes_set_id) as tattributesetscount from store_products_attributes_sets ats, apmmasterrecordsstate b
  where ats.statusid !=", tuserStatusIdDeleted," and ats.statusid = b.statusid  ", tcond);



PREPARE stmt_name FROM @tstatement;
EXECUTE stmt_name;


END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPattributesetsdetails`(
IN iattributes_set_id int(11)
)
BEGIN

set @tstatement=concat("SELECT * FROM store_products_attributes_sets  where attributes_set_id = ", iattributes_set_id);
-- select @tstatement;

PREPARE stmt_name FROM @tstatement;
EXECUTE stmt_name;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPattributesetsedit`(
IN iattributes_set_id int(11),
IN iattributes_group_id int(11),
IN iattributes_set_title varchar(255),
IN iattribute_ids_string varchar(10000),
IN iaction varchar(255),
IN iadmin int(11)
)
BEGIN

Declare tuserStatusIdActive int(11);
Declare tuserStatusIdDeleted int(11);
Declare trowcount int(11);
Declare tactiondesc varchar(255);
Declare taactivityid int(11);
Declare tcuruseraction varchar(255);
Declare tmess varchar(255);
Declare tstatus int(11);


SELECT statusid into tuserStatusIdActive FROM apmmasterrecordsstate  where recordstate='Active';
SELECT statusid into tuserStatusIdDeleted FROM apmmasterrecordsstate  where recordstate='Deleted';


select statusid into tstatus from store_products_attributes_sets where attributes_set_id = iattributes_set_id;
if tstatus <> tuserStatusIdDeleted then

      update store_products_attributes_sets set
      attributes_set_title         = iattributes_set_title,
      attributes_group_id         = iattributes_group_id
      where attributes_set_id      = iattributes_set_id;

      /*
      delete from store_products_attributes_sets_mapping where attributes_set_id = iattributes_set_id;



            SELECT LENGTH(iattribute_ids_string) - LENGTH(REPLACE(iattribute_ids_string, '#', '')) into @iattributes_id;
            WHILE @iattributes_id > 0 DO

                select sfSPLIT_STR(iattribute_ids_string,'#',@iattributes_id) into @iattributes_id_in;

                insert into store_products_attributes_sets_mapping
                (attribute_id, attributes_set_id, createddatetime, statusid)
                values(@iattributes_id_in,iattributes_set_id,NOW(),tUserStatusIdActive);

                SET @iattributes_id = @iattributes_id - 1;
            END WHILE;
            */

      set tcuruseraction = 'Edit attributesets';
      set tactiondesc = concat('Details were updated for attributes_set_id ',iattributes_set_id, ' by ', iadmin);



      select FNapmwriteactivitylog(iadmin , tcuruseraction, iaction , tactiondesc) into taactivityid;
      set tmess = '1#success';
      select tmess;

else
  set tmess = '2#failure';
  select tmess;
end if;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPattributesetslist`(
IN iattributes_set_title varchar(255),
IN istart int(11),
IN ilimit int(11)
)
BEGIN


Declare tuserStatusIdActive int(11);
Declare tuserStatusIdDeleted int(11);
declare tcond text;

SELECT statusid into tuserStatusIdActive FROM apmmasterrecordsstate  where recordstate='Active';
SELECT statusid into tuserStatusIdDeleted FROM apmmasterrecordsstate  where recordstate='Deleted';


set tcond = '';

if iattributes_set_title <> '' then

  set tcond = concat(tcond," and  ats.attributes_set_title like '%", iattributes_set_title , "%'");

end if;



set @tstatement=concat("select * from
store_products_attributes_sets ats, store_products_attributes_groups atg, apmmasterrecordsstate b

where
ats.statusid !=", tuserStatusIdDeleted," and ats.statusid = b.statusid
and ats.attributes_group_id = atg.attributes_group_id order by atg.attributes_group_title, ats.attributes_set_title ");


-- select @tstatement;

PREPARE stmt_name FROM @tstatement;
EXECUTE stmt_name;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPattributesetslistactive`()
BEGIN


Declare tuserStatusIdActive int(11);

declare tcond text;

SELECT statusid into tuserStatusIdActive FROM apmmasterrecordsstate  where recordstate='Active';



set @tstatement=concat("select * from store_products_attributes a
where
a.statusid =", tuserStatusIdActive," order by a.attribute_title ");


-- select @tstatement;

PREPARE stmt_name FROM @tstatement;
EXECUTE stmt_name;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPattributesetsmappingList`(
IN iattributes_set_id int(11)
)
BEGIN


Declare tuserStatusIdActive int(11);

declare tcond text;

SELECT statusid into tuserStatusIdActive FROM apmmasterrecordsstate  where recordstate='Active';


set @tstatement=concat("SELECT attribute_id FROM store_products_attributes_sets_mapping asm
where
asm.statusid =", tuserStatusIdActive," AND attributes_set_id=", iattributes_set_id, " order by asm.attributes_sets_mapping_id ");


-- select @tstatement;

PREPARE stmt_name FROM @tstatement;
EXECUTE stmt_name;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPattributestesadd`(
IN iattributes_group_id int(11),
IN iattributes_set_title varchar(255),
IN iattributes_ids varchar(10000),
IN iaction varchar(255),
IN iadmin int(11)
)
BEGIN


declare tcountcheck int(11) default 0;
declare tcuruseraction varchar(255);
declare tactiondesc varchar(255);
declare tattributesetscount int(5) default 0;
declare tsuccess int(11);
declare tcount int(11);
declare tattributes_set_id int(11);
declare tUserStatusIdActive int(11);
declare taactivityid int(11);
declare toutput varchar(255);



set tcuruseraction = 'Add Attributesets';
set tactiondesc = '';


SELECT statusid into tUserStatusIdActive FROM apmmasterrecordsstate  where recordstate='Active';



select count(attributes_set_id) into tattributesetscount from store_products_attributes_sets
where attributes_set_title = iattributes_set_title;

if tattributesetscount <> 0 then
    set tactiondesc = concat('Unable to create attributesets with ', iattributes_set_title , ' as the attributesets title already exists.');
    set toutput = '0#Failed to register attributesets';

else


			insert into store_products_attributes_sets
			(attributes_set_title,
      attributes_group_id,
			createddatetime,
			statusid)
			values
			(
			iattributes_set_title,
      iattributes_group_id,
			NOW(),
			tUserStatusIdActive);

      select LAST_INSERT_ID() into tattributes_set_id;

                       /*
                       SELECT LENGTH(iattributes_ids) - LENGTH(REPLACE(iattributes_ids, '#', '')) into @iattributes_id;
                       WHILE @iattributes_id > 0 DO

										      select sfSPLIT_STR(iattributes_ids,'#',@iattributes_id) into @iattributes_id_in;

                          insert into store_products_attributes_sets_mapping
                          (attribute_id, attributes_set_id, createddatetime, statusid)
                          values(@iattributes_id_in,tattributes_set_id,NOW(),tUserStatusIdActive);

											    SET @iattributes_id = @iattributes_id - 1;
										   END WHILE;
                       */


      set tactiondesc = concat('Attributesets created with title ', iattributes_set_title, ' and attributes_set_id ', tattributes_set_id , ' by admin ', iadmin);
      set toutput = concat('1#',tattributes_set_id,'#', iattributes_set_title, '#Attriburesets Registered successfully');



end if;


      select FNapmwriteactivitylog(iadmin , tcuruseraction, iaction , tactiondesc) into taactivityid;

  
select toutput;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPcategoryadd`(
IN iparent_category_id varchar(255),
IN icategory_name varchar(255),
IN icategory_meta_title varchar(255),
IN icategory_meta_description varchar(255),
IN iaction varchar(255),
IN iadmin int(11)
)
BEGIN


declare tcountcheck int(11) default 0;
declare tcuruseraction varchar(255);
declare tactiondesc varchar(255);
declare tcategorycount int(5) default 0;
declare tsuccess int(11);
declare tcount int(11);
declare tcategoryid int(11);
declare tUserStatusIdActive int(11);
declare taactivityid int(11);
declare toutput varchar(255);



set tcuruseraction = 'Add Category';
set tactiondesc = '';


SELECT statusid into tUserStatusIdActive FROM apmmasterrecordsstate  where recordstate='Active';



select count(category_id) into tcategorycount from store_categories where category_name = icategory_name and parent_category_id = iparent_category_id;

if tcategorycount <> 0 then
    set tactiondesc = concat('Unable to create category with ', icategory_name , ' as the category title already exists.');
    set toutput = '0#Failed to register category';

else


			insert into store_categories
			(parent_category_id,
			category_name,
			category_meta_title,
			category_meta_description,
			createddatetime,
			statusid)
			values
			(
			iparent_category_id,
			icategory_name,
			icategory_meta_title,
			icategory_meta_description,
			NOW(),
			tUserStatusIdActive);

      select LAST_INSERT_ID() into tcategoryid;


      set tactiondesc = concat('Category created with title ', icategory_name, ' and category_id ', tcategoryid , ' by admin ', iadmin);
      set toutput = concat('1#',tcategoryid,'#', icategory_name, '#Category Registered successfully');



end if;


      select FNapmwriteactivitylog(iadmin , tcuruseraction, iaction , tactiondesc) into taactivityid;

  
select toutput;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPcategoryaddimage`(
IN icategory_id int(11),
IN icategory_image varchar(255),
IN iaction varchar(255),
IN iadmin int(11)
)
BEGIN



declare tcuruseraction varchar(255);
declare tactiondesc varchar(255);
declare tUserStatusIdActive int(11);
declare taactivityid int(11);
declare toutput varchar(255);
declare tcategory_id int(11);
declare tcategory_name varchar(255);
declare tcategory_image_id int(11);



set tcuruseraction = 'Add Category Image';
set tactiondesc = '';


SELECT statusid into tUserStatusIdActive FROM apmmasterrecordsstate  where recordstate='Active';



select category_id,category_name into tcategory_id, tcategory_name from store_categories where category_id = icategory_id and statusid = tUserStatusIdActive;

if tcategory_id = '' then
    set tactiondesc = concat('Unable to create category image - ', icategory_id , ' category not in active status.');
    set toutput = '0#Failed to create category image';

else


			insert into store_categories_images
			(category_id,
			category_image,
			category_image_title,
			createddatetime,
			statusid)
			values
			(
			icategory_id,
			icategory_image,
			tcategory_name,
			NOW(),
			tUserStatusIdActive);

      select LAST_INSERT_ID() into tcategory_image_id;


      set tactiondesc = concat('Category image created with title ', icategory_image, ' and category_image_id ', tcategory_image_id , ' by admin ', iadmin);
      set toutput = concat('1#',tcategory_image_id,'#', tcategory_name, '#Category image created successfully');



end if;


      select FNapmwriteactivitylog(iadmin , tcuruseraction, iaction , tactiondesc) into taactivityid;

  
select toutput;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPcategorychangestatus`(
IN icategory_id int(11),
IN ilockstatus int(1),
IN iunlockstatus int(1),
IN ideletestatus int(1),
IN iaction varchar(255),
IN iadminid int(11),
OUT omess varchar(255)
)
BEGIN


DECLARE tactivestatus int(11);
DECLARE tlockstatus int(11);
DECLARE tdeletestatus int(11);
DECLARE taactivityid int(11);
DECLARE tcategoryaction varchar(255);
DECLARE tactiondesc varchar(255);
DECLARE tcategory_name varchar(255);



SELECT statusid into tactivestatus FROM apmmasterrecordsstate  where recordstate='Active';
SELECT statusid into tlockstatus FROM apmmasterrecordsstate  where recordstate='Locked';
SELECT statusid into tdeletestatus FROM apmmasterrecordsstate  where recordstate='Deleted';

select category_name into tcategory_name from store_categories where category_id = icategory_id;


if ilockstatus = 1 and iunlockstatus = 0 and ideletestatus = 0 then
  set tcategoryaction = 'Lock category';

  update store_categories set statusid = tlockstatus where category_id = icategory_id;

  set tactiondesc = concat('Category with category_id ',icategory_id, ' was Locked by admin with adminid ', iadminid);
  select FNapmwriteactivitylog(iadminid , tcategoryaction, iaction , tactiondesc) into taactivityid;


elseif ilockstatus = 0 and iunlockstatus = 1 and ideletestatus = 0 then

  set tcategoryaction = 'Unlock category';

  update store_categories set statusid = tactivestatus where category_id = icategory_id;

  set tactiondesc = concat('Category with category_id ',icategory_id, ' was Activated by admin with adminid ', iadminid);
  select FNapmwriteactivitylog(iadminid , tcategoryaction, iaction , tactiondesc) into taactivityid;


elseif ilockstatus = 0 and iunlockstatus = 0 and ideletestatus = 1 then

  set tcategoryaction = 'Delete category';

  update store_categories set statusid = tdeletestatus, deleteddatetime = now() where category_id = icategory_id;


  set tactiondesc = concat('Category with category_id ',icategory_id, ' was Deleted by admin with adminid ', iadminid);
  select FNapmwriteactivitylog(iadminid , tcategoryaction, iaction , tactiondesc) into taactivityid;


end if;

  set omess = concat('1#', tcategory_name, '#', '');
  select omess;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPcategorycount`(
IN icategory_name varchar(255)
)
BEGIN


Declare tuserStatusIdDeleted int(11);
declare tcond text;

SELECT statusid into tuserStatusIdDeleted FROM apmmasterrecordsstate  where recordstate='Deleted';

set tcond = '';

if icategory_name <> '' then

  set tcond = concat(tcond," and  c.category_name like '%", icategory_name , "%'");

end if;


  set @tstatement=concat("select count(c.category_id) as tcategorycount from store_categories c, apmmasterrecordsstate b
  where c.statusid !=", tuserStatusIdDeleted," and c.statusid = b.statusid  ", tcond);



PREPARE stmt_name FROM @tstatement;
EXECUTE stmt_name;


END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPcategorydeleteimage`(
IN icategory_id int(11),
IN icategory_image_id int(11),
IN iaction varchar(255),
IN iadmin int(11)
)
BEGIN



declare tcuruseraction varchar(255);
declare tactiondesc varchar(255);
declare tUserStatusIdActive int(11);
Declare tuserStatusIdDeleted int(11);
declare taactivityid int(11);
declare toutput varchar(255);
declare tcategory_id int(11);
declare tcategory_name varchar(255);
declare tcategory_image_id int(11);



set tcuruseraction = 'Delete Category Image';
set tactiondesc = '';


SELECT statusid into tUserStatusIdActive FROM apmmasterrecordsstate  where recordstate='Active';
SELECT statusid into tuserStatusIdDeleted FROM apmmasterrecordsstate  where recordstate='Deleted';



select category_image_id into tcategory_image_id from store_categories_images where category_image_id = icategory_image_id and statusid = tUserStatusIdActive;

if tcategory_image_id = '' then
    set tactiondesc = concat('Unable to delete category image - ', icategory_image_id , ' category image not in active status.');
    set toutput = '0#Failed to delete category image';

else


		  update store_categories_images set statusid = tuserStatusIdDeleted where category_image_id = icategory_image_id;


      set tactiondesc = concat('Category image deleted ', ' and category_image_id ', tcategory_image_id , ' by admin ', iadmin);
      set toutput = concat('1#',tcategory_image_id,'#', 'Category ID:', icategory_id, '#Category image deleted successfully');



end if;


      select FNapmwriteactivitylog(iadmin , tcuruseraction, iaction , tactiondesc) into taactivityid;

  
select toutput;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPcategorydetails`(
IN icategory_id int(11)
)
BEGIN

set @tstatement=concat("SELECT * FROM store_categories  where category_id = ", icategory_id);
-- select @tstatement;

PREPARE stmt_name FROM @tstatement;
EXECUTE stmt_name;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPcategorydetailsimages`(
IN icategory_id int(11)
)
BEGIN

Declare tuserStatusIdActive int(11);

SELECT statusid into tUserStatusIdActive FROM apmmasterrecordsstate  where recordstate='Active';

set @tstatement=concat("SELECT * FROM store_categories_images  where category_id = ", icategory_id, ' AND statusid=',tUserStatusIdActive);
-- select @tstatement;

PREPARE stmt_name FROM @tstatement;
EXECUTE stmt_name;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPcategoryedit`(
IN icategory_id int(11),
IN iparent_category_id varchar(255),
IN icategory_name varchar(255),
IN icategory_meta_title varchar(255),
IN icategory_meta_description varchar(255),
IN iaction varchar(255),
IN iadmin int(11)
)
BEGIN

Declare tuserStatusIdActive int(11);
Declare tuserStatusIdDeleted int(11);
Declare trowcount int(11);
Declare tactiondesc varchar(255);
Declare taactivityid int(11);
Declare tcuruseraction varchar(255);
Declare tmess varchar(255);
Declare tstatus int(11);


SELECT statusid into tuserStatusIdActive FROM apmmasterrecordsstate  where recordstate='Active';
SELECT statusid into tuserStatusIdDeleted FROM apmmasterrecordsstate  where recordstate='Deleted';


select statusid into tstatus from store_categories where category_id = icategory_id;
if tstatus <> tuserStatusIdDeleted then

      update store_categories set
      parent_category_id         = iparent_category_id,
      category_name              = icategory_name,
      category_meta_title        = icategory_meta_title,
      category_meta_description  = icategory_meta_description
      where category_id          = icategory_id;

      set tcuruseraction = 'Edit category';
      set tactiondesc = concat('Details were updated for category_id ',icategory_id, ' by ', iadmin);



      select FNapmwriteactivitylog(iadmin , tcuruseraction, iaction , tactiondesc) into taactivityid;
      set tmess = '1#success';
      select tmess;

else
  set tmess = '2#failure';
  select tmess;
end if;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPcategorylist`(
IN icategory_name varchar(255),
IN istart int(11),
IN ilimit int(11)
)
BEGIN


Declare tuserStatusIdActive int(11);
Declare tuserStatusIdDeleted int(11);
declare tcond text;

SELECT statusid into tuserStatusIdActive FROM apmmasterrecordsstate  where recordstate='Active';
SELECT statusid into tuserStatusIdDeleted FROM apmmasterrecordsstate  where recordstate='Deleted';


set tcond = '';

if icategory_name <> '' then

  set tcond = concat(tcond," and  c.category_name like '%", icategory_name , "%'");

end if;




-- set @tstatement=concat("select *, (select category_name from store_categories where category_id=c.parent_category_id) as parent_category_name from store_categories c, apmmasterrecordsstate b
-- where
-- c.statusid !=", tuserStatusIdDeleted," and c.statusid = b.statusid order by c.category_name limit ", istart,",", ilimit);


set @tstatement=concat("select *,
(select category_name from store_categories where category_id=c.parent_category_id) as parent_category_name ,
(select category_image from store_categories_images where category_id=c.category_id AND statusid=",tuserStatusIdActive,") as category_images
from
store_categories c, apmmasterrecordsstate b
where
c.statusid !=", tuserStatusIdDeleted," and c.statusid = b.statusid order by c.parent_category_id,c.category_name ASC ");


-- select @tstatement;

PREPARE stmt_name FROM @tstatement;
EXECUTE stmt_name;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPcategorylistparent`()
BEGIN


Declare tuserStatusIdActive int(11);
Declare tuserStatusIdDeleted int(11);

SELECT statusid into tuserStatusIdActive FROM apmmasterrecordsstate  where recordstate='Active';
SELECT statusid into tuserStatusIdDeleted FROM apmmasterrecordsstate  where recordstate='Deleted';



set @tstatement=concat("select * from store_categories c, apmmasterrecordsstate b
where
c.statusid !=", tuserStatusIdDeleted," and parent_category_id=0 and c.statusid = b.statusid order by c.category_name");


-- select @tstatement;

PREPARE stmt_name FROM @tstatement;
EXECUTE stmt_name;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPcheckapmuserexists`(In iuserid int(11))
BEGIN



SELECT count(userid) as cnt FROM apmusers where userid= iuserid;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPgetcountries`()
BEGIN

Declare tmoduleStatusIdActive int(11);

SELECT statusid into tmoduleStatusIdActive FROM apmmasterrecordsstate  where recordstate='Active';


select *
from master_countries c
where
c.statusid=tmoduleStatusIdActive;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPmerchantadd`(
IN imerchant_title varchar(255),
IN imerchant_email varchar(255),
IN imerchant_mobile varchar(255),
IN imerchant_phone varchar(255),
IN imerchant_fax varchar(255),
IN imerchant_city varchar(255),
IN imerchant_state varchar(255),
IN imerchant_country int(11),
IN imerchant_address1 varchar(5000),
IN imerchant_address2 varchar(5000),
IN imerchant_postcode varchar(255),
IN imerchant_description varchar(255),
IN iaction varchar(255),
IN iadmin int(11)
)
BEGIN


declare tcountcheck int(11) default 0;
declare tcuruseraction varchar(255);
declare tactiondesc varchar(255);
declare tmerchantcount int(5) default 0;
declare tsuccess int(11);
declare tcount int(11);
declare tmerchantid int(11);
declare tUserStatusIdActive int(11);
declare taactivityid int(11);
declare toutput varchar(255);



set tcuruseraction = 'Add Merchant';
set tactiondesc = '';


SELECT statusid into tUserStatusIdActive FROM apmmasterrecordsstate  where recordstate='Active';



select count(merchant_id) into tmerchantcount from store_merchants where merchant_title = imerchant_title;

if tmerchantcount <> 0 then
    set tactiondesc = concat('Unable to create merchant with ', imerchant_title , ' as the merchant title already exists.');
    set toutput = '0#Failed to register Merchant';

else


			insert into store_merchants
			(merchant_title,
			merchant_email,
			merchant_mobile,
			merchant_phone,
			merchant_fax,
			merchant_city,
			merchant_state,
			merchant_country,
			merchant_address1,
			merchant_address2,
			merchant_postcode,
			merchant_description,
			createddatetime,
			statusid)
			values
			(
			imerchant_title,		
			imerchant_email,
			imerchant_mobile,
			imerchant_phone,
			imerchant_fax,
			imerchant_city,
			imerchant_state,
			imerchant_country,
			imerchant_address1,
			imerchant_address2,
			imerchant_postcode,
			imerchant_description,
			NOW(),
			tUserStatusIdActive);

      select LAST_INSERT_ID() into tmerchantid;


      set tactiondesc = concat('Merchant created with title ', imerchant_title, ' and merchantid ', tmerchantid , ' by admin ', iadmin);
      set toutput = concat('1#', tmerchantid, '#Merchant Registered successfully');



end if;


      select FNapmwriteactivitylog(iadmin , tcuruseraction, iaction , tactiondesc) into taactivityid;

  
select toutput;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPmerchantaddimage`(
IN imerchant_id int(11),
IN imerchant_image varchar(255),
IN iaction varchar(255),
IN iadmin int(11)
)
BEGIN



declare tcuruseraction varchar(255);
declare tactiondesc varchar(255);
declare tUserStatusIdActive int(11);
declare taactivityid int(11);
declare toutput varchar(255);
declare tmerchant_id int(11);
declare tmerchant_title varchar(255);
declare tmerchant_image_id int(11);



set tcuruseraction = 'Add Merchant Image';
set tactiondesc = '';


SELECT statusid into tUserStatusIdActive FROM apmmasterrecordsstate  where recordstate='Active';



select merchant_id,merchant_title into tmerchant_id, tmerchant_title from store_merchants where merchant_id = imerchant_id and statusid = tUserStatusIdActive;

if tmerchant_id = '' then
    set tactiondesc = concat('Unable to create merchant image - ', imerchant_id , ' merchant not in active status.');
    set toutput = '0#Failed to create merchant image';

else


			insert into store_merchants_images
			(merchant_id,
			merchant_image,
			merchant_image_title,
			createddatetime,
			statusid)
			values
			(
			imerchant_id,
			imerchant_image,
			tmerchant_title,
			NOW(),
			tUserStatusIdActive);

      select LAST_INSERT_ID() into tmerchant_image_id;


      set tactiondesc = concat('Merchant image created with title ', imerchant_image, ' and merchant_image_id ', tmerchant_image_id , ' by admin ', iadmin);
      set toutput = concat('1#',tmerchant_image_id,'#', tmerchant_title, '#Merchant image created successfully');



end if;


      select FNapmwriteactivitylog(iadmin , tcuruseraction, iaction , tactiondesc) into taactivityid;

  
select toutput;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPmerchantchangestatus`(
IN imerchant_id int(11),
IN ilockstatus int(1),
IN iunlockstatus int(1),
IN ideletestatus int(1),
IN iaction varchar(255),
IN iadminid int(11),
OUT omess varchar(255)
)
BEGIN




DECLARE tactivestatus int(11);
DECLARE tlockstatus int(11);
DECLARE tdeletestatus int(11);
DECLARE taactivityid int(11);
DECLARE tuseraction varchar(255);
DECLARE tactiondesc varchar(255);
DECLARE tmerchant_title varchar(255);
DECLARE tmerchant_email varchar(255);


SELECT statusid into tactivestatus FROM apmmasterrecordsstate  where recordstate='Active';
SELECT statusid into tlockstatus FROM apmmasterrecordsstate  where recordstate='Locked';
SELECT statusid into tdeletestatus FROM apmmasterrecordsstate  where recordstate='Deleted';

select merchant_title into tmerchant_title from store_merchants where merchant_id = imerchant_id;
select merchant_email into tmerchant_email from store_merchants where merchant_id = imerchant_id;


if ilockstatus = 1 and iunlockstatus = 0 and ideletestatus = 0 then
  set tuseraction = 'Lock merchant';

  update store_merchants set statusid = tlockstatus where merchant_id = imerchant_id;

  set tactiondesc = concat('Merchant with merchant_id ',imerchant_id, ' was Locked by admin with adminid ', iadminid);
  select FNapmwriteactivitylog(iadminid , tuseraction, iaction , tactiondesc) into taactivityid;


elseif ilockstatus = 0 and iunlockstatus = 1 and ideletestatus = 0 then

  set tuseraction = 'Unlock merchant';

  update store_merchants set statusid = tactivestatus where merchant_id = imerchant_id;

  set tactiondesc = concat('Merchant with merchant_id ',imerchant_id, ' was Activated by admin with adminid ', iadminid);
  select FNapmwriteactivitylog(iadminid , tuseraction, iaction , tactiondesc) into taactivityid;


elseif ilockstatus = 0 and iunlockstatus = 0 and ideletestatus = 1 then

  set tuseraction = 'Delete merchant';

  update store_merchants set statusid = tdeletestatus, deleteddatetime = now() where merchant_id = imerchant_id;


  set tactiondesc = concat('Merchant with merchant_id ',imerchant_id, ' was Deleted by admin with adminid ', iadminid);
  select FNapmwriteactivitylog(iadminid , tuseraction, iaction , tactiondesc) into taactivityid;


end if;

  set omess = concat('1#', tmerchant_title, '#', tmerchant_email);
  select omess;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPmerchantdeleteimage`(
IN imerchant_id int(11),
IN imerchant_image_id int(11),
IN iaction varchar(255),
IN iadmin int(11)
)
BEGIN



declare tcuruseraction varchar(255);
declare tactiondesc varchar(255);
declare tUserStatusIdActive int(11);
Declare tuserStatusIdDeleted int(11);
declare taactivityid int(11);
declare toutput varchar(255);
declare tmerchant_id int(11);
declare tmerchant_image_id int(11);



set tcuruseraction = 'Delete Merchant Image';
set tactiondesc = '';


SELECT statusid into tUserStatusIdActive FROM apmmasterrecordsstate  where recordstate='Active';
SELECT statusid into tuserStatusIdDeleted FROM apmmasterrecordsstate  where recordstate='Deleted';



select merchant_image_id into tmerchant_image_id from store_merchants_images where merchant_image_id = imerchant_image_id and statusid = tUserStatusIdActive;

if tmerchant_image_id = '' then
    set tactiondesc = concat('Unable to delete merchant image - ', imerchant_image_id , ' merchant image not in active status.');
    set toutput = '0#Failed to delete merchant image';

else


		  update store_merchants_images set statusid = tuserStatusIdDeleted where merchant_image_id = imerchant_image_id;


      set tactiondesc = concat('Merchant image deleted ', ' and category_image_id ', tmerchant_image_id , ' by admin ', iadmin);
      set toutput = concat('1#',tmerchant_image_id,'#', 'Merchant ID:', imerchant_id, '#Merchant image deleted successfully');



end if;


      select FNapmwriteactivitylog(iadmin , tcuruseraction, iaction , tactiondesc) into taactivityid;

  
select toutput;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPmerchantdetails`(
IN imerchant_id int(11)
)
BEGIN

set @tstatement=concat("SELECT * FROM store_merchants where merchant_id = ", imerchant_id);
-- select @tstatement;

PREPARE stmt_name FROM @tstatement;
EXECUTE stmt_name;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPmerchantdetailsimages`(
IN imerchant_id int(11)
)
BEGIN

Declare tuserStatusIdActive int(11);

SELECT statusid into tUserStatusIdActive FROM apmmasterrecordsstate  where recordstate='Active';

set @tstatement=concat("SELECT * FROM store_merchants_images  where merchant_id = ", imerchant_id, ' AND statusid=',tUserStatusIdActive);
-- select @tstatement;

PREPARE stmt_name FROM @tstatement;
EXECUTE stmt_name;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPmerchantedit`(
IN imerchant_id int(11),
IN imerchant_title varchar(255),
IN imerchant_email varchar(255),
IN imerchant_mobile varchar(255),
IN imerchant_phone varchar(255),
IN imerchant_fax varchar(255),
IN imerchant_city varchar(255),
IN imerchant_state varchar(255),
IN imerchant_country int(11),
IN imerchant_address1 varchar(5000),
IN imerchant_address2 varchar(5000),
IN imerchant_postcode varchar(255),
IN imerchant_description varchar(255),
IN iaction varchar(255),
IN iadmin int(11)
)
BEGIN

Declare tuserStatusIdActive int(11);
Declare tuserStatusIdDeleted int(11);
Declare tappid int(11);
Declare trowcount int(11);
Declare tactiondesc varchar(255);
Declare taactivityid int(11);
Declare tcuruseraction varchar(255);
Declare tmess varchar(255);
Declare tstatus int(11);


SELECT statusid into tuserStatusIdActive FROM apmmasterrecordsstate  where recordstate='Active';
SELECT statusid into tuserStatusIdDeleted FROM apmmasterrecordsstate  where recordstate='Deleted';


select statusid into tstatus from store_merchants where merchant_id = imerchant_id;
if tstatus <> tuserStatusIdDeleted then

      update store_merchants set
      merchant_title        = imerchant_title,
      merchant_email        = imerchant_email,
      merchant_mobile       = imerchant_mobile,
      merchant_phone        = imerchant_phone,
      merchant_fax          = imerchant_fax,
      merchant_city         = imerchant_city,
      merchant_state        = imerchant_state,
      merchant_country      = imerchant_country,
      merchant_address1     = imerchant_address1,
      merchant_address2     = imerchant_address2,
      merchant_postcode     = imerchant_postcode,
      merchant_description    = imerchant_description
      where merchant_id     = imerchant_id;

      set tcuruseraction = 'Edit Merchant';
      set tactiondesc = concat('Details were updated for Merchant Id ',imerchant_id, ' by ', iadmin);



      select FNapmwriteactivitylog(iadmin , tcuruseraction, iaction , tactiondesc) into taactivityid;
      set tmess = '1#success';
      select tmess;

else
  set tmess = '2#failure';
  select tmess;
end if;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPmerchantscount`(
IN imerchant_title varchar(255),
IN imerchant_email varchar(255),
IN imerchant_mobile varchar(255)
)
BEGIN


Declare tuserStatusIdDeleted int(11);
declare tcond text;

SELECT statusid into tuserStatusIdDeleted FROM apmmasterrecordsstate  where recordstate='Deleted';

set tcond = '';

if imerchant_title <> '0' then

  set tcond = concat(tcond," and  m.merchant_title like '%", imerchant_title , "%'");

end if;

if imerchant_email <> '0' then

  set tcond = concat(tcond," and m.merchant_email like '%", imerchant_email , "%'");

end if;

if imerchant_mobile <> '0' then

  set tcond = concat(tcond," and m.merchant_mobile like '%", imerchant_mobile , "%'");

end if;




  set @tstatement=concat("select count(m.merchant_id) as tmerchantcount from store_merchants m, apmmasterrecordsstate b
  where m.statusid !=", tuserStatusIdDeleted," and m.statusid = b.statusid  ", tcond);



PREPARE stmt_name FROM @tstatement;
EXECUTE stmt_name;


END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPmerchantslist`(
IN istart int(11),
IN ilimit int(11),
IN imerchant_title varchar(255),
IN imerchant_email varchar(255),
IN imerchant_mobile varchar(255)
)
BEGIN


Declare tuserStatusIdActive int(11);
Declare tuserStatusIdDeleted int(11);
declare tcond text;

SELECT statusid into tuserStatusIdActive FROM apmmasterrecordsstate  where recordstate='Active';
SELECT statusid into tuserStatusIdDeleted FROM apmmasterrecordsstate  where recordstate='Deleted';


set tcond = '';

if imerchant_title <> '' then

  set tcond = concat(tcond," and  m.merchant_title like '%", imerchant_title , "%'");

end if;

if imerchant_email <> '' then

  set tcond = concat(tcond," and m.merchant_email like '%", imerchant_email , "%'");

end if;

if imerchant_mobile <> '' then

  set tcond = concat(tcond," and m.merchant_mobile like '%", imerchant_mobile , "%'");

end if;



-- set @tstatement=concat("select * from store_merchants m, apmmasterrecordsstate b
-- where
-- m.statusid !=", tuserStatusIdDeleted," and m.statusid = b.statusid order by m.merchant_title limit ", istart,",", ilimit);

set @tstatement=concat("select m.merchant_id, m.merchant_title, m.merchant_email, m.merchant_mobile, m.merchant_phone, m.merchant_fax, m.merchant_city, m.merchant_state, m.merchant_country, m.merchant_address1, m.merchant_address2, m.merchant_postcode, m.statusid, (select merchant_image from store_merchants_images where merchant_id=m.merchant_id AND statusid=",tuserStatusIdActive,") as merchant_images FROM store_merchants m, apmmasterrecordsstate b
where
m.statusid !=", tuserStatusIdDeleted," and m.statusid = b.statusid order by m.merchant_title  ");

-- select @tstatement;

PREPARE stmt_name FROM @tstatement;
EXECUTE stmt_name;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPmerchantslistactive`()
BEGIN


Declare tuserStatusIdActive int(11);

declare tcond text;

SELECT statusid into tuserStatusIdActive FROM apmmasterrecordsstate  where recordstate='Active';



set @tstatement=concat("select m.merchant_id, m.merchant_title, m.merchant_email from store_merchants m
where
m.statusid =", tuserStatusIdActive," order by m.merchant_title ");


-- select @tstatement;

PREPARE stmt_name FROM @tstatement;
EXECUTE stmt_name;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPproductadd`(
IN iproduct_title varchar(255),
IN iproduct_sku varchar(255),
IN iproduct_meta_title varchar(255),
IN iproduct_meta_description varchar(255),
IN iproduct_small_description varchar(5000),
IN iproduct_description text,
IN iattributes_group_id int(11),
IN imerchant_id int(11),
IN ibrand_id int(11),
IN iaction varchar(255),
IN iadmin int(11)
)
BEGIN


declare tcountcheck int(11) default 0;
declare tcuruseraction varchar(255);
declare tactiondesc varchar(255);
declare tproductcount int(5) default 0;
declare tsuccess int(11);
declare tcount int(11);
declare tproductid int(11);
declare tUserStatusIdActive int(11);
declare taactivityid int(11);
declare toutput varchar(255);



set tcuruseraction = 'Add Product';
set tactiondesc = '';


SELECT statusid into tUserStatusIdActive FROM apmmasterrecordsstate  where recordstate='Active';



select count(product_id) into tproductcount from store_products where product_title = iproduct_title;

if tproductcount <> 0 then
    set tactiondesc = concat('Unable to create product with ', iproduct_title , ' as the product title already exists.');
    set toutput = '0#Failed to register Product';

else


			insert into store_products
			(product_sku,
			product_title,
			product_small_description,
			product_meta_title,
			product_meta_description,
      attributes_group_id,
      merchant_id,
      brand_id,
			createddatetime,
			statusid)
			values
			(
			iproduct_sku,		
			iproduct_title,
			iproduct_small_description,
			iproduct_meta_title,
			iproduct_meta_description,
      iattributes_group_id,
      imerchant_id,
      ibrand_id,
			NOW(),
			tUserStatusIdActive);

      select LAST_INSERT_ID() into tproductid;

      if tproductid<>'' then
      insert into store_products_description
			(product_description,
			product_id,
			createddatetime,
			statusid)
			values
			(
			iproduct_description,		
			tproductid,
			NOW(),
			tUserStatusIdActive);
      end if;


      set tactiondesc = concat('Procudt created with title ', iproduct_title, ' and product_id ', tproductid , ' by admin ', iadmin);
      set toutput = concat('1#', tproductid, '#Product Registered successfully');



end if;


      select FNapmwriteactivitylog(iadmin , tcuruseraction, iaction , tactiondesc) into taactivityid;

  
select toutput;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPproductaddattributevalues`(
IN iproduct_id int(11),
IN iattribute_key_str text,
In iattribute_value_str text,
In iattribute_value_id_str text,
IN iaction varchar(255),
IN iadmin int(11)
)
BEGIN


declare tcountcheck int(11) default 0;
declare tcuruseraction varchar(255);
declare tactiondesc varchar(255);
declare tproductcount int(5) default 0;
declare tproducttitle  varchar(255);
declare tsuccess int(11);
declare tcount int(11);
declare tproductid int(11);
declare tUserStatusIdActive int(11);
declare taactivityid int(11);
declare toutput varchar(255);
declare tattribute_value_id int(11);
declare tiattribute_value_str_in MEDIUMTEXT;




set tcuruseraction = 'Add Product Attribute Values';
set tactiondesc = '';


SELECT statusid into tUserStatusIdActive FROM apmmasterrecordsstate  where recordstate='Active';

select product_title into tproducttitle from store_products where product_id = iproduct_id;

select count(product_id) into tproductcount from store_products where product_id = iproduct_id;

if tproductcount = 0 then
    set tactiondesc = concat('Unable to create product attribute values with ', tproducttitle , ' as the product not in active status.');
    set toutput = '0#Failed to add Product prices';

else




            SELECT LENGTH(iattribute_key_str) - LENGTH(REPLACE(iattribute_key_str, '@#@#@', '')) into @iattribute_key_str_count;
            WHILE @iattribute_key_str_count > 0 DO

                select sfSPLIT_STR(iattribute_key_str,'@#@#@',@iattribute_key_str_count) into @iattribute_key_str_in;
                select sfSPLIT_STR(iattribute_value_str,'@#@#@',@iattribute_key_str_count) into tiattribute_value_str_in;
                select sfSPLIT_STR(iattribute_value_id_str,'@#@#@',@iattribute_key_str_count) into @iattribute_value_id_str_in;

                if @iattribute_value_id_str_in <> '' then

                    update store_products_attributes_values set
                    attribute_value = tiattribute_value_str_in
                    where
                    attribute_id = @iattribute_key_str_in
                    AND product_id = iproduct_id
                    AND attribute_value_id = @iattribute_value_id_str_in;

                    set tactiondesc = concat('Product attribute value updated with product_price_id ', @iattribute_value_id_str_in, ' by admin ', iadmin);
                    select FNapmwriteactivitylog(iadmin , tcuruseraction, iaction , tactiondesc) into taactivityid;

                else

                    if @iattribute_key_str_in <> '' AND tiattribute_value_str_in <> '' then
                        insert into store_products_attributes_values (attribute_id, attribute_value, product_id, createddatetime, statusid)
                        values(@iattribute_key_str_in,tiattribute_value_str_in,iproduct_id, NOW(), tUserStatusIdActive);

                        select LAST_INSERT_ID() into tattribute_value_id;

                        set tactiondesc = concat('Product attribute value created with product_price_id', tattribute_value_id , '  by admin ', iadmin);
                        select FNapmwriteactivitylog(iadmin , tcuruseraction, iaction , tactiondesc) into taactivityid;
                    end if;


                end if;


                SET @iattribute_key_str_count = @iattribute_key_str_count - 1;

            END WHILE;

            -- set toutput = 'Success';



end if;


select toutput;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPproductaddcategories`(
IN iproduct_id int(11),
IN iproduct_categories varchar(25555),
IN iaction varchar(255),
IN iadmin int(11)
)
BEGIN


declare tcountcheck int(11) default 0;
declare tcuruseraction varchar(255);
declare tactiondesc varchar(255);
declare tproductcount int(5) default 0;
declare tproducttitle  varchar(255);
declare tsuccess int(11);
declare tcount int(11);
declare tproductid int(11);
declare tUserStatusIdActive int(11);
declare taactivityid int(11);
declare toutput varchar(255);
declare tproduct_category_id int(11);



set tcuruseraction = 'Add Product Categories';
set tactiondesc = '';


SELECT statusid into tUserStatusIdActive FROM apmmasterrecordsstate  where recordstate='Active';

select product_title into tproducttitle from store_products where product_id = iproduct_id;

select count(product_id) into tproductcount from store_products where product_id = iproduct_id;

if tproductcount = 0 then
    set tactiondesc = concat('Unable to create product categories with ', tproducttitle , ' as the product not in active status.');
    set toutput = '0#Failed to add Product categories';

else


            delete from store_products_categories where product_id = iproduct_id;

            SELECT LENGTH(iproduct_categories) - LENGTH(REPLACE(iproduct_categories, '#', '')) into @iproduct_categories_count;
            WHILE @iproduct_categories_count > 0 DO

                select sfSPLIT_STR(iproduct_categories,'#',@iproduct_categories_count) into @iproduct_categories_in;

                    if @iproduct_categories_in <> '' then

                        insert into store_products_categories (product_id, category_id, createddatetime, statusid)
                        values(iproduct_id,@iproduct_categories_in, NOW(), tUserStatusIdActive);

                        select LAST_INSERT_ID() into tproduct_category_id;

                        set tactiondesc = concat('Product categories created with title ', tproducttitle, ' and product_category_id ', tproduct_category_id , ' by admin ', iadmin);
                        select FNapmwriteactivitylog(iadmin , tcuruseraction, iaction , tactiondesc) into taactivityid;

                    end if;


                SET @iproduct_categories_count = @iproduct_categories_count - 1;

            END WHILE;

            -- set toutput = 'Success';



end if;


select toutput;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPproductaddimages`(
IN iproduct_id int(11),
IN iproduct_images varchar(2555),
IN iaction varchar(255),
IN iadmin int(11)
)
BEGIN


declare tcountcheck int(11) default 0;
declare tcuruseraction varchar(255);
declare tactiondesc varchar(255);
declare tproductcount int(5) default 0;
declare tproducttitle  varchar(2555);
declare tsuccess int(11);
declare tcount int(11);
declare tproductid int(11);
declare tUserStatusIdActive int(11);
declare taactivityid int(11);
declare toutput varchar(255);
declare tproduct_image_id int(11);



set tcuruseraction = 'Add Product Images';
set tactiondesc = '';


SELECT statusid into tUserStatusIdActive FROM apmmasterrecordsstate  where recordstate='Active';

select product_title into tproducttitle from store_products where product_id = iproduct_id;

select count(product_id) into tproductcount from store_products where product_id = iproduct_id;

if tproductcount = 0 then
    set tactiondesc = concat('Unable to create product images with ', tproducttitle , ' as the product not in active status.');
    set toutput = '0#Failed to add Product images';

else




            SELECT LENGTH(iproduct_images) - LENGTH(REPLACE(iproduct_images, '#', '')) into @iproduct_images_count;
            WHILE @iproduct_images_count > 0 DO

                select sfSPLIT_STR(iproduct_images,'#',@iproduct_images_count) into @iproduct_images_in;

                -- select @iproduct_images_in;

                insert into store_products_images
                (product_id, product_image, product_image_title, createddatetime, statusid)
                values(iproduct_id,@iproduct_images_in,tproducttitle,NOW(),tUserStatusIdActive);


                SET @iproduct_images_count = @iproduct_images_count - 1;


                select LAST_INSERT_ID() into tproduct_image_id;
                set tactiondesc = concat('Product image created with title ', tproducttitle, ' and product_image_id ', tproduct_image_id , ' by admin ', iadmin);
                select FNapmwriteactivitylog(iadmin , tcuruseraction, iaction , tactiondesc) into taactivityid;
            END WHILE;

            -- set toutput = 'Success';



end if;





select toutput;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPproductaddprices`(
IN iproduct_id int(11),
IN iproduct_price_id varchar(2555),
In iproduct_price_description varchar(2555),
IN iproduct_price varchar(2555),
IN iproduct_discount varchar(2555),
IN iproduct_discount_type varchar(2555),
IN iproduct_stock varchar(2555),
IN idiscount_start_date varchar(2555),
IN idiscount_end_date varchar(2555),
IN iaction varchar(255),
IN iadmin int(11)
)
BEGIN


declare tcountcheck int(11) default 0;
declare tcuruseraction varchar(255);
declare tactiondesc varchar(255);
declare tproductcount int(5) default 0;
declare tproducttitle  varchar(255);
declare tsuccess int(11);
declare tcount int(11);
declare tproductid int(11);
declare tUserStatusIdActive int(11);
declare taactivityid int(11);
declare toutput varchar(255);
declare tproduct_price_id int(11);



set tcuruseraction = 'Add Product Prices';
set tactiondesc = '';


SELECT statusid into tUserStatusIdActive FROM apmmasterrecordsstate  where recordstate='Active';

select product_title into tproducttitle from store_products where product_id = iproduct_id;

select count(product_id) into tproductcount from store_products where product_id = iproduct_id;

if tproductcount = 0 then
    set tactiondesc = concat('Unable to create product prices with ', tproducttitle , ' as the product not in active status.');
    set toutput = '0#Failed to add Product prices';

else




            SELECT LENGTH(iproduct_price) - LENGTH(REPLACE(iproduct_price, '#', '')) into @iproduct_price_count;
            WHILE @iproduct_price_count > 0 DO

                select sfSPLIT_STR(iproduct_price,'#',@iproduct_price_count) into @iproduct_price_in;
                select sfSPLIT_STR(iproduct_price_description,'#',@iproduct_price_count) into @iproduct_price_description_in;
                select sfSPLIT_STR(iproduct_price_id,'#',@iproduct_price_count) into @iproduct_price_id_in;
                select sfSPLIT_STR(iproduct_discount,'#',@iproduct_price_count) into @iproduct_discount_in;
                select sfSPLIT_STR(iproduct_discount_type,'#',@iproduct_price_count) into @iproduct_discount_type_in;
                select sfSPLIT_STR(iproduct_stock,'#',@iproduct_price_count) into @iproduct_stock_in;
                select sfSPLIT_STR(idiscount_start_date,'#',@iproduct_price_count) into @idiscount_start_date_in;
                select sfSPLIT_STR(idiscount_end_date,'#',@iproduct_price_count) into @idiscount_end_date_in;

                if @iproduct_price_id_in <> '' then

                    update store_products_price set
                    product_price_description = @iproduct_price_description_in,
                    product_price = @iproduct_price_in,
                    product_discount = @iproduct_discount_in,
                    product_discount_type = @iproduct_discount_type_in,
                    product_stock = @iproduct_stock_in,
                    discount_start_date = @idiscount_start_date_in,
                    discount_end_date =  @idiscount_end_date_in
                    where product_price_id = @iproduct_price_id_in;

                    set tactiondesc = concat('Product price updated with title ', @iproduct_price_description_in, ' and product_price_id ', @iproduct_price_id_in , ' by admin ', iadmin);
                    select FNapmwriteactivitylog(iadmin , tcuruseraction, iaction , tactiondesc) into taactivityid;

                else

                    if @iproduct_price_in <> '' then
                        insert into store_products_price (product_id, product_price_description, product_price, product_discount, product_discount_type, product_stock, discount_start_date, discount_end_date, createddatetime, statusid)
                        values(iproduct_id,@iproduct_price_description_in,@iproduct_price_in, @iproduct_discount_in, @iproduct_discount_type_in, @iproduct_stock_in, @idiscount_start_date_in, @idiscount_end_date_in, NOW(), tUserStatusIdActive);

                        select LAST_INSERT_ID() into tproduct_price_id;

                        set tactiondesc = concat('Product price created with title ', @iproduct_price_description_in, ' and product_price_id ', tproduct_price_id , ' by admin ', iadmin);
                        select FNapmwriteactivitylog(iadmin , tcuruseraction, iaction , tactiondesc) into taactivityid;
                    end if;

                end if;


                SET @iproduct_price_count = @iproduct_price_count - 1;

            END WHILE;

            -- set toutput = 'Success';



end if;


select toutput;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPproductchangestatus`(
IN iproduct_id int(11),
IN ilockstatus int(1),
IN iunlockstatus int(1),
IN ideletestatus int(1),
IN iaction varchar(255),
IN iadminid int(11),
OUT omess varchar(255)
)
BEGIN




DECLARE tactivestatus int(11);
DECLARE tlockstatus int(11);
DECLARE tdeletestatus int(11);
DECLARE taactivityid int(11);
DECLARE tuseraction varchar(255);
DECLARE tactiondesc varchar(255);
DECLARE tproduct_title varchar(255);
-- DECLARE tmerchant_email varchar(255);


SELECT statusid into tactivestatus FROM apmmasterrecordsstate  where recordstate='Active';
SELECT statusid into tlockstatus FROM apmmasterrecordsstate  where recordstate='Locked';
SELECT statusid into tdeletestatus FROM apmmasterrecordsstate  where recordstate='Deleted';

select product_title into tproduct_title from store_products where product_id = iproduct_id;
-- select merchant_email into tmerchant_email from store_merchants where merchant_id = imerchant_id;


if ilockstatus = 1 and iunlockstatus = 0 and ideletestatus = 0 then
  set tuseraction = 'Lock product';

  update store_products set statusid = tlockstatus where product_id = iproduct_id;

  set tactiondesc = concat('Product with product_id ',iproduct_id, ' was Locked by admin with adminid ', iadminid);
  select FNapmwriteactivitylog(iadminid , tuseraction, iaction , tactiondesc) into taactivityid;


elseif ilockstatus = 0 and iunlockstatus = 1 and ideletestatus = 0 then

  set tuseraction = 'Unlock product';

  update store_products set statusid = tactivestatus where product_id = iproduct_id;

  set tactiondesc = concat('Product with product_id ',iproduct_id, ' was Activated by admin with adminid ', iadminid);
  select FNapmwriteactivitylog(iadminid , tuseraction, iaction , tactiondesc) into taactivityid;


elseif ilockstatus = 0 and iunlockstatus = 0 and ideletestatus = 1 then

  set tuseraction = 'Delete product';

  update store_products set statusid = tdeletestatus, deleteddatetime = now() where product_id = iproduct_id;


  set tactiondesc = concat('Product with product_id ',iproduct_id, ' was Deleted by admin with adminid ', iadminid);
  select FNapmwriteactivitylog(iadminid , tuseraction, iaction , tactiondesc) into taactivityid;


end if;

  set omess = concat('1#', tproduct_title);
  select omess;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPproducthomepageimage`(
IN iproduct_id int(11),
IN iproduct_imagehome_radio varchar(255),
IN iproduct_imagethumbnail_radio varchar(255),
IN iaction varchar(255),
IN iadmin int(11)
)
BEGIN

declare toutput varchar(255);

  update store_products_images set home_page_image=0, product_thumbnail=0 where product_id = iproduct_id;

  update store_products_images set home_page_image=1 where product_image_id = iproduct_imagehome_radio;

  update store_products_images set product_thumbnail=1 where product_image_id = iproduct_imagethumbnail_radio;

  set toutput = 'Success';

select toutput;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPproductimagechangestatus`(
IN iproduct_image_id int(11),
IN ilockstatus int(1),
IN iunlockstatus int(1),
IN ideletestatus int(1),
IN iaction varchar(255),
IN iadminid int(11),
OUT omess varchar(255)
)
BEGIN




DECLARE tactivestatus int(11);
DECLARE tlockstatus int(11);
DECLARE tdeletestatus int(11);
DECLARE taactivityid int(11);
DECLARE tuseraction varchar(255);
DECLARE tactiondesc varchar(255);
DECLARE tproduct_image_title varchar(255);
-- DECLARE tmerchant_email varchar(255);


SELECT statusid into tactivestatus FROM apmmasterrecordsstate  where recordstate='Active';
SELECT statusid into tlockstatus FROM apmmasterrecordsstate  where recordstate='Locked';
SELECT statusid into tdeletestatus FROM apmmasterrecordsstate  where recordstate='Deleted';

select product_image_title into tproduct_image_title from store_products_images where product_image_id = iproduct_image_id;
-- select merchant_email into tmerchant_email from store_merchants where merchant_id = imerchant_id;


if ilockstatus = 1 and iunlockstatus = 0 and ideletestatus = 0 then
  set tuseraction = 'Lock product image';

  update store_products_images set statusid = tlockstatus where product_image_id = iproduct_image_id;

  set tactiondesc = concat('Product image with product_image_id ',iproduct_image_id, ' was Locked by admin with adminid ', iadminid);
  select FNapmwriteactivitylog(iadminid , tuseraction, iaction , tactiondesc) into taactivityid;


elseif ilockstatus = 0 and iunlockstatus = 1 and ideletestatus = 0 then

  set tuseraction = 'Unlock product image';

  update store_products_images set statusid = tactivestatus where product_image_id = iproduct_image_id;

  set tactiondesc = concat('Product image with product_image_id ',iproduct_image_id, ' was Activated by admin with adminid ', iadminid);
  select FNapmwriteactivitylog(iadminid , tuseraction, iaction , tactiondesc) into taactivityid;


elseif ilockstatus = 0 and iunlockstatus = 0 and ideletestatus = 1 then

  set tuseraction = 'Delete product image';

  update store_products_images set statusid = tdeletestatus, deleteddatetime = now() where product_image_id = iproduct_image_id;


  set tactiondesc = concat('Product image with product_image_id ',iproduct_image_id, ' was Deleted by admin with adminid ', iadminid);
  select FNapmwriteactivitylog(iadminid , tuseraction, iaction , tactiondesc) into taactivityid;


end if;

  set omess = concat('1#', tproduct_image_title);
  select omess;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPproductscount`(
IN iproduct_title varchar(255)
)
BEGIN


Declare tuserStatusIdDeleted int(11);
declare tcond text;

SELECT statusid into tuserStatusIdDeleted FROM apmmasterrecordsstate  where recordstate='Deleted';

set tcond = '';

if iproduct_title <> '' then

  set tcond = concat(tcond," and  p.product_title like '%", iproduct_title , "%'");

end if;


  set @tstatement=concat("select count(p.product_id) as tproductcount from store_products p, apmmasterrecordsstate b
  where p.statusid !=", tuserStatusIdDeleted," and p.statusid = b.statusid  ", tcond);



PREPARE stmt_name FROM @tstatement;
EXECUTE stmt_name;


END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPproductslist`(
IN iproduct_title varchar(255),
IN istart int(11),
IN ilimit int(11)
)
BEGIN


Declare tuserStatusIdActive int(11);
Declare tuserStatusIdDeleted int(11);
declare tcond text;

SELECT statusid into tuserStatusIdActive FROM apmmasterrecordsstate  where recordstate='Active';
SELECT statusid into tuserStatusIdDeleted FROM apmmasterrecordsstate  where recordstate='Deleted';


set tcond = '';

if iproduct_title <> '' then

  set tcond = concat(tcond," and  p.product_title like '%", iproduct_title , "%'");

end if;




set @tstatement=concat("select * from store_products p, apmmasterrecordsstate b
where
p.statusid !=", tuserStatusIdDeleted," and p.statusid = b.statusid ", tcond," order by p.product_title limit ", istart,",", ilimit);




-- select @tstatement;

PREPARE stmt_name FROM @tstatement;
EXECUTE stmt_name;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPproductupdate`(
IN iproduct_id varchar(255),
IN iproduct_title varchar(255),
IN iproduct_sku varchar(255),
IN iproduct_meta_title varchar(255),
IN iproduct_meta_description varchar(255),
IN iproduct_small_description varchar(5000),
IN iproduct_description text,
IN iproduct_merchant_id int(11),
IN iproduct_brand_id int(11),
IN iaction varchar(255),
IN iadmin int(11)
)
BEGIN


declare tcountcheck int(11) default 0;
declare tcuruseraction varchar(255);
declare tactiondesc varchar(255);
declare tproduct_id int(5) default 0;
declare tproduct_count int(5) default 0;
declare tsuccess int(11);
declare tcount int(11);
declare tproductid int(11);
declare tUserStatusIdActive int(11);
declare taactivityid int(11);
declare toutput varchar(255);



set tcuruseraction = 'Update Product';
set tactiondesc = '';


SELECT statusid into tUserStatusIdActive FROM apmmasterrecordsstate  where recordstate='Active';



select count(product_id) into tproduct_count from store_products where product_title = iproduct_title AND product_id != iproduct_id;

if tproduct_count > 0 then
    set tactiondesc = concat('Unable to update product with ', iproduct_title , ' as the product title already exists.');
    set toutput = '0#Failed to update Product';

else


			update store_products
			set product_sku = iproduct_sku,
			product_title = iproduct_title,
			product_small_description = iproduct_small_description,
			product_meta_title = iproduct_meta_title,
			product_meta_description = iproduct_meta_description,
      merchant_id = iproduct_merchant_id,
      brand_id = iproduct_brand_id
      where
      product_id = iproduct_id;


      update store_products_description
			set product_description = iproduct_description
			where
      product_id = iproduct_id;


      set tactiondesc = concat('Procudt updated with title ', iproduct_title, ' and product_id ', iproduct_id , ' by admin ', iadmin);
      set toutput = concat('1#', iproduct_id, '#Product Updated successfully');



end if;


      select FNapmwriteactivitylog(iadmin , tcuruseraction, iaction , tactiondesc) into taactivityid;

  
select toutput;

END$$

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
    set toutput = '0#Emailid already exists.';

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

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPuserlogin`(
IN iusername varchar(255),
IN ipassword varchar(255),
IN imaxpasshits int(11),
IN iaction varchar(255))
BEGIN



Declare tUserStatusIdActive int(11);
Declare tUserStatusIdLocked int(11);
Declare tUserStatusIdDeleted int(11);
Declare tUserId int(11) default 0;
Declare taactivityid int(11);
Declare tactiondesc varchar(300);
Declare tUserpassId int(11) default 0;
Declare tpassexpiry int(11);
Declare tCountlog int(11);
Declare tStatusCheck int(11);
Declare twrongpasscount int(11) default 0;
Declare opassexpiry int(11);
Declare omess varchar(255);
Declare tuseraction varchar(255);
Declare tusername varchar(255);
Declare troleid int(11);

SELECT statusid into tUserStatusIdActive FROM apmmasterrecordsstate  where recordstate='Active';
SELECT statusid into tUserStatusIdLocked FROM apmmasterrecordsstate  where recordstate='Locked';
SELECT statusid into tUserStatusIdDeleted FROM apmmasterrecordsstate  where recordstate='Deleted';
SELECT roleid into troleid FROM apmmasterroles where rolename = 'User';


set tuseraction = 'Login';





  select count(au.userid) into tUserId from apmusers au, apmuserrolemapping aurm
  where
  au.userid = aurm.userid
  AND roleid = troleid
  AND userloginid = iusername;

  if tUserId = 0 then

     set opassexpiry = 0;
     set omess = '3#Emailid is Not matching';
     select opassexpiry, omess;

  else

     select userid into tUserId from apmusers where userloginid = iusername;
     select count(userid) into tUserpassId from apmusers where userloginid = iusername and password = ipassword and statusid = tUserStatusIdActive;
     select concat(firstname, ' ', lastname) into tusername from apmusers where userloginid = iusername and password = ipassword and statusid = tUserStatusIdActive;

     if tUserpassId = 0 then

        select statusid into tStatusCheck from apmusers where userloginid = iusername;

        if tStatusCheck = tUserStatusIdLocked then

            set opassexpiry = 0;
            set omess = '2#User has been locked. Please contact local administrator.';
            set tactiondesc = concat('User has been already locked for ',tUserId);
            select FNapmwriteactivitylog(tUserId , tuseraction, iaction , tactiondesc) into taactivityid;
            select opassexpiry, omess;

        elseif tStatusCheck = tUserStatusIdDeleted then

           set opassexpiry = 0;
           set omess = '4#User no longer exists.';
           set tactiondesc = concat('Trying to login for deleted user with ',tUserId);
           select FNapmwriteactivitylog(tUserId , tuseraction, iaction , tactiondesc) into taactivityid;
           select opassexpiry, omess;

        else

        if imaxpasshits <> 0 then

          select passcounter into tCountlog from apmusers where userid = tUserId and statusid = tUserStatusIdActive;

          if tCountlog >= (imaxpasshits - 1) then

            update apmusers set statusid = tUserStatusIdLocked, passcounter = 0 where userid = tUserId;

            set tactiondesc = concat('Invalid password for ',tUserId);
            select FNapmwriteactivitylog(tUserId , tuseraction, iaction , tactiondesc) into taactivityid;
            set opassexpiry = 0;
            set omess = '2#User has been locked. Please contact local administrator.';
            set tactiondesc = concat('User has been locked for ',tUserId);
            select FNapmwriteactivitylog(tUserId , tuseraction, iaction , tactiondesc) into taactivityid;
            select opassexpiry, omess;

          else

            set twrongpasscount = tCountlog + 1;
            update apmusers set passcounter = twrongpasscount where userid = tUserId;

            set tactiondesc = concat('Invalid password for ',tUserId);
            select FNapmwriteactivitylog(tUserId , tuseraction, iaction , tactiondesc) into taactivityid;
            set opassexpiry = 0;
            set omess = '0#Invalid Password entered.';
            select opassexpiry, omess;

          end if;

        else

          set tactiondesc = concat('Invalid password for ',tUserId);
          select FNapmwriteactivitylog(tUserId , tuseraction, iaction , tactiondesc) into taactivityid;
          set opassexpiry = 0;
          set omess = '0#Invalid Password.';
          select opassexpiry, omess;

        end if; 

      end if; 

     else

       select DATEDIFF(now(), (select updateddatetime from apmpasswordhistory where userid=tUserId and statusid = tUserStatusIdActive order by updateddatetime desc limit 0,1)) into tpassexpiry ;
        
        update apmusers set passcounter = 0, seccounter = 0 where userid = tUserId;

       set tactiondesc = concat('User succefully logged in with ',tUserId);

       select FNapmwriteactivitylog(tUserId , tuseraction, iaction , tactiondesc) into taactivityid;

        set opassexpiry = tpassexpiry;

       set omess = '1#User succefully logged in.';

        select a.rolename as role, a.roleid as roleid, a.priority as priority,
       d.userid as userid, count(d.firstname) as cnt, d.isfirstpass as isfirstpass, d.emailid as email, d.issecured as issecured,
       concat(d.firstname, ' ', d.lastname) as name, opassexpiry, omess
       from apmmasterroles a, apmuserrolemapping c, apmusers d
       where
       d.userid=tUserId
       and d.statusid= tUserStatusIdActive
       and c.userid = d.userid and a.roleid=c.roleid;



     end if;



  end if;


END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPuser_address`(
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

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPuser_education_add`(
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

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPuser_education_edit`(
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

END$$

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
      set toutput = concat('1#', 'Experience Registered successfully');


      select FNapmwriteactivitylog(iuserid , tcuruseraction, iaction , tactiondesc) into taactivityid;

  
select toutput;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SPuser_experience_edit`(
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

END$$

--
-- Functions
--
CREATE DEFINER=`root`@`localhost` FUNCTION `FNapmsetfirstpassflag`(
iuserid int(11),
iflagvalue INT(11),
isecflag int(11),
isecflagvalue int(11)) RETURNS int(11)
BEGIN



declare tcuruser varchar(255);
declare tUserStatusIdActive int(11);
Declare tUserStatusIdLocked int(11);
Declare tUserStatusIdDeleted int(11);





SELECT SUBSTRING_INDEX(USER(),_utf8'@',1) into tcuruser;


SELECT statusid into tUserStatusIdActive FROM apmmasterrecordsstate  where recordstate='Active';
SELECT statusid into tUserStatusIdLocked FROM apmmasterrecordsstate  where recordstate='Locked';
SELECT statusid into tUserStatusIdDeleted FROM apmmasterrecordsstate  where recordstate='Deleted';







if isecflag = 2 then

  update apmusers set isfirstpass = iflagvalue where userid = iuserid and statusid = tUserStatusIdActive;

  update apmsecurityqa set statusid = tUserStatusIdDeleted, deleteddatetime = now() where userid = iuserid and statusid = tUserStatusIdActive;

  update apmusers set issecured = isecflagvalue where userid = iuserid and statusid = tUserStatusIdActive;

elseif isecflag = 1 then

  update apmusers set issecured = isecflagvalue where userid = iuserid and statusid = tUserStatusIdActive;
  update apmsecurityqa set statusid = tUserStatusIdDeleted, deleteddatetime = now() where userid = iuserid and statusid = tUserStatusIdActive;



elseif isecflag = 0 then

  update apmusers set isfirstpass = iflagvalue
  where userid = iuserid and statusid = tUserStatusIdActive;

end if;

return isecflag;

END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `FNapmwriteactivitylog`(
iuserid int(11),
iuseraction varchar(255),
iactionname varchar(255),
iactiondesc varchar(255)
) RETURNS int(11)
BEGIN


declare tUserStatusIdActive int(11);
declare tactionid int(11);
declare tinserted int(11);
declare tuseractionid int(11);


SELECT statusid into tUserStatusIdActive FROM apmmasterrecordsstate  where recordstate='Active';
select actionid into tactionid from apmmasteractions where actionname = iactionname and statusid =tUserStatusIdActive;

select useractionid into tuseractionid from apmmasteruseractions where useraction = iuseraction and statusid =tUserStatusIdActive;

  insert into apmuseractivitylog(userid, useractionid, actionid, actiondesc, createddatetime,statusid)
         values
         (iuserid, tuseractionid, tactionid, iactiondesc, NOW(),tUserStatusIdActive);

  select LAST_INSERT_ID() into tinserted;

return tinserted;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `FNapmwriteuseractivitylog`(
iuserid int(11),
iuseraction varchar(255),
iactionname varchar(255),
icontrollername varchar(255),
imodulename varchar(255),
iactiondesc varchar(255)
) RETURNS int(11)
BEGIN


declare tUserStatusIdActive int(11);
declare tactionid int(11);
declare tinserted int(11);
declare tuseractionid int(11);


SELECT statusid into tUserStatusIdActive FROM apmmasterrecordsstate  where recordstate='Active';

select a.actionid into tactionid from apmmasteractions a, apmmastermodules m, apmmastercontrollers c
where a.actionname = iactionname and a.controllerid = c.controllerid and m.modulename = imodulename
and c.controllername = icontrollername and c.moduleid = m.moduleid and a.statusid =tUserStatusIdActive;

select useractionid into tuseractionid from apmmasteruseractions where useraction = iuseraction and statusid =tUserStatusIdActive;

  insert into apmuseractivitylog(userid, useractionid, actionid, actiondesc, createddatetime,statusid)
         values
         (iuserid, tuseractionid, tactionid, iactiondesc, NOW(),tUserStatusIdActive);

  select LAST_INSERT_ID() into tinserted;

return tinserted;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `FNdeleteexpiredsessions`(iTime int(11)) RETURNS int(11)
BEGIN





declare tDeletedCount int(11) default 0;

if iTime is not null
then
DELETE FROM apmsessiondata
            WHERE
                sessexpire < iTime;
select row_count() into tDeletedCount;
end if;
return tDeletedCount;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `FNdestroysession`(iSessionId varchar(32)) RETURNS int(11)
BEGIN


declare tRowsEffected int(11) default 0;

if iSessionId is not null
then
DELETE FROM apmsessiondata
            WHERE
                sessid = iSessionId;
select row_count() into tRowsEffected;
end if;
return tRowsEffected;

END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `FNgetactivesessions`() RETURNS int(11)
BEGIN




 
 declare tCount int(11) default 0;

SELECT
                COUNT(sessid) into tCount
            FROM apmsessiondata;
return tCount;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `FNreadsessiondata`(iSessionId varchar(32),iTime int(11),iHttpAgent varchar(32)) RETURNS blob
BEGIN

declare tSessionData blob;
SELECT
                sessdata into tSessionData
            FROM
                apmsessiondata
            WHERE
                sessid = iSessionId AND
                sessexpire > iTime AND
                sesshttpuseragent = iHttpAgent limit 1;
return tSessionData;

END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `FNwritesessiondata`(iSessionId varchar(32),iTime int(11),iSessionData blob,iHttpAgent varchar(32)) RETURNS int(11)
BEGIN






 declare tRowsCount int(11) default 0;

 INSERT INTO
                apmsessiondata (
                    sessid,
                    sesshttpuseragent,
                    sessdata,
                    sessexpire,
                    CreatedDateTime
                )
            VALUES (
                iSessionId,
               iHttpAgent,
               iSessionData,
              iTime,
              now()
            )
            ON DUPLICATE KEY UPDATE
                sessdata = iSessionData,
                sessexpire = iTime;
 select row_count() into tRowsCount;
return tRowsCount;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `sfSPLIT_STR`(
  x text,
  delim VARCHAR(12),
  pos INT
) RETURNS mediumtext CHARSET latin1
RETURN REPLACE(SUBSTRING(SUBSTRING_INDEX(x, delim, pos),
       LENGTH(SUBSTRING_INDEX(x, delim, pos -1)) + 1),
       delim, '')$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `user_wall_messages`
--

CREATE TABLE IF NOT EXISTS `user_wall_messages` (
  `wall_message_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `wall_message` varchar(255) NOT NULL,
  `userid` int(11) NOT NULL,
  `user_ip` varchar(45) NOT NULL,
  `wall_uploads` varchar(255) NOT NULL,
  `createddatetime` datetime NOT NULL,
  `updateddatetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleteddatetime` datetime NOT NULL,
  `statusid` int(11) NOT NULL,
  `createdby` varchar(255) NOT NULL,
  `lastupdatedby` varchar(255) NOT NULL,
  PRIMARY KEY (`wall_message_id`),
  KEY `FK_user_wall_messages_1` (`userid`),
  KEY `FK_user_wall_messages_2` (`statusid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `user_wall_messages`
--

INSERT INTO `user_wall_messages` (`wall_message_id`, `wall_message`, `userid`, `user_ip`, `wall_uploads`, `createddatetime`, `updateddatetime`, `deleteddatetime`, `statusid`, `createdby`, `lastupdatedby`) VALUES
(1, 'My little blog http://9lessons.info', 86, '127.0.0.1', '', '2013-07-24 00:00:00', '2013-07-24 16:34:32', '0000-00-00 00:00:00', 1, '', ''),
(3, 'hello', 86, '::1', '', '0000-00-00 00:00:00', '2013-08-04 15:24:08', '0000-00-00 00:00:00', 1, '', ''),
(4, 'hello', 86, '::1', '', '0000-00-00 00:00:00', '2013-08-04 15:24:54', '0000-00-00 00:00:00', 1, '', ''),
(5, 'ancdsdad', 86, '::1', '', '0000-00-00 00:00:00', '2013-08-04 15:45:24', '0000-00-00 00:00:00', 1, '', ''),
(6, 'anbbczmc', 86, '::1', '', '0000-00-00 00:00:00', '2013-08-04 15:48:53', '0000-00-00 00:00:00', 1, '', ''),
(7, 'heloo', 86, '::1', '', '0000-00-00 00:00:00', '2013-08-04 17:48:35', '0000-00-00 00:00:00', 1, '', '');

-- --------------------------------------------------------

--
-- Table structure for table `user_wall_message_comments`
--

CREATE TABLE IF NOT EXISTS `user_wall_message_comments` (
  `comment_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `wall_comment` varchar(255) NOT NULL,
  `wall_message_id` bigint(20) NOT NULL,
  `userid` int(11) NOT NULL,
  `user_ip` varchar(45) NOT NULL,
  `createddatetime` datetime NOT NULL,
  `updateddatetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleteddatetime` datetime NOT NULL,
  `statusid` int(11) NOT NULL,
  `createdby` varchar(255) NOT NULL,
  `lastupdatedby` varchar(255) NOT NULL,
  PRIMARY KEY (`comment_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `user_wall_message_comments`
--

INSERT INTO `user_wall_message_comments` (`comment_id`, `wall_comment`, `wall_message_id`, `userid`, `user_ip`, `createddatetime`, `updateddatetime`, `deleteddatetime`, `statusid`, `createdby`, `lastupdatedby`) VALUES
(1, 'Hello this is sample text', 1, 86, '127.0.0.1', '2013-07-29 00:00:00', '2013-07-29 18:03:02', '0000-00-00 00:00:00', 1, '', ''),
(2, 'This is Next sample text', 1, 86, '127.0.0.1', '2013-07-29 00:00:00', '2013-07-29 18:03:56', '0000-00-00 00:00:00', 1, '', ''),
(3, 'This is Test Comment', 1, 86, '127.0.0.1', '2013-07-29 00:00:00', '2013-07-29 18:04:35', '0000-00-00 00:00:00', 1, '', ''),
(4, 'hello', 1, 86, '::1', '0000-00-00 00:00:00', '2013-07-29 20:40:57', '0000-00-00 00:00:00', 1, '', ''),
(5, '', 1, 86, '::1', '0000-00-00 00:00:00', '2013-07-29 20:43:08', '0000-00-00 00:00:00', 1, '', ''),
(6, 'Nice Metting you sir', 1, 86, '::1', '0000-00-00 00:00:00', '2013-07-29 20:54:16', '0000-00-00 00:00:00', 1, '', '');

-- --------------------------------------------------------

--
-- Table structure for table `user_wall_uploads`
--

CREATE TABLE IF NOT EXISTS `user_wall_uploads` (
  `wall_upload_id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `createddatetime` datetime NOT NULL,
  `updateddatetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleteddatetime` datetime NOT NULL,
  `statusid` int(11) NOT NULL,
  `createdby` varchar(255) NOT NULL,
  `lastupdatedby` varchar(255) NOT NULL,
  PRIMARY KEY (`wall_upload_id`),
  KEY `FK_user_wall_uploads_1` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `user_wall_messages`
--
ALTER TABLE `user_wall_messages`
  ADD CONSTRAINT `FK_user_wall_messages_1` FOREIGN KEY (`userid`) REFERENCES `apmusers` (`userid`),
  ADD CONSTRAINT `FK_user_wall_messages_2` FOREIGN KEY (`statusid`) REFERENCES `apmmasterrecordsstate` (`statusid`);

--
-- Constraints for table `user_wall_uploads`
--
ALTER TABLE `user_wall_uploads`
  ADD CONSTRAINT `FK_user_wall_uploads_1` FOREIGN KEY (`userid`) REFERENCES `apmusers` (`userid`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
