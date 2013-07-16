

-- apmusers TABLE Changes

ALTER TABLE  `apmusers` ADD  `date_of_birth` DATE NOT NULL AFTER  `phonenumber` ,
ADD  `gender` ENUM(  'Male',  'Female' ) NOT NULL DEFAULT  'Male' AFTER  `date_of_birth` ,
ADD  `marital_status` ENUM(  'Single',  'Married' ) NOT NULL DEFAULT  'Single' AFTER  `gender` ,
ADD  `timezone_id` INT( 11 ) NOT NULL AFTER  `marital_status`;


ALTER TABLE  `apmusers` ADD  `display_name` VARCHAR( 255 ) NOT NULL AFTER  `lastname`;


update apmusers set timezone_id=1;


ALTER TABLE `apmusers` ADD CONSTRAINT `FK_apmusers_2` FOREIGN KEY `FK_apmusers_2` (`timezone_id`)
    REFERENCES `master_timezones` (`timezone_id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT;
	

	
-- user_profile TABLE Changes


ALTER TABLE `user_profile` DROP FOREIGN KEY `FK_user_profile_5`;


ALTER TABLE `user_profile` DROP COLUMN `display_name`,
 DROP COLUMN `date_of_birth`,
 DROP COLUMN `gender`,
 DROP COLUMN `marital_status`,
 DROP COLUMN `timezone_id`;
 
 