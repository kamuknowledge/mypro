
ALTER TABLE `user_experience` ADD `industry_id` INT( 11 ) NOT NULL AFTER `job_location` ;



ALTER TABLE `user_experience` ADD CONSTRAINT `FK_user_experience_3` FOREIGN KEY `FK_user_experience_3` (`industry_id`)
    REFERENCES `user_industry` (`industry_id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT;
	
