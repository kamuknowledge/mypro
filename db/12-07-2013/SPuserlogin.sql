DELIMITER $$

DROP PROCEDURE IF EXISTS `pro_my`.`SPuserlogin`$$
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

DELIMITER ;