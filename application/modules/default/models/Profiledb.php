<?php
/*
*================AllySoft Internal Use only========================

*-----------------------------------------------------------------*
* Copy Right Header Information*
*-----------------------------------------------------------------*
* Project	:	GetLinc
* File		:	Categoriesdb.php 
* Module	:	Category Management Module
* Owner		:	RAM's 
* Purpose	:	This class is used for category management related database operations
* Date		:	08/05/2012


* Modification History
* ----------------------------------------------------------------------------------------------------------------
* Dated		Version		Who		Description
* -----------------------------------------------------------------------------------------------------------------
* %D%		%T%			%W%
* -----------------------------------------------------------------------------------------------------------------

*===================================================================================================================
*/

class Default_Model_Profiledb  {
	
	public $session;
	private $error;
	public $viewobj;
	public $userid;
	
	
	/**
     * Purpose: Constructor sets sessions for portal and portalerror
     * Access is limited to class and extended classes
     *
     * @param   
     * @return  
     */
	
		
	public function __construct(){
		$this->session = new Zend_Session_Namespace('MyClientPortal');
		$this->error = new Zend_Session_Namespace('MyClientPortalerror');
		$this->db=Zend_Registry::get('db');
		
		/* Check Login */
		if($this->session->userid)
		{
			$this->userid = $this->session->userid;
		}
	}
	
	

	
	/**
     * Purpose: Fetching category list
     *
     * Access is limited to class and extended classes
     *
     * @param   int		$iuserid User Id
     * @param	int		$istart Start value
     * @param	int		$ilimit Limit value for fetching result set
     * @param	varchar	$cond Search condition
     * @return  object	Returns status message.	
     */
	
	public function getProfileDetails(){
		try {	
			if($this->userid) {
				$query = "SELECT u.userid, u.firstname, u.lastname, u.emailid, u.phonenumber, u.display_name, u.date_of_birth, u.gender, u.marital_status, u.timezone_id, u.profile_image, up.about_us, up.interests, ui.image_path, ue.job_title, ue.company_name, ued.degree, ued.school_name, mc.country as country_name
							FROM apmusers u
							LEFT JOIN user_profile up ON up.userid = u.userid AND up.statusid = 1
							LEFT JOIN user_images ui ON ui.userid = u.userid AND ui.statusid = 1
							LEFT JOIN user_experience ue ON ue.userid = u.userid AND ue.experience_id = (SELECT ue1.experience_id FROM user_experience ue1 WHERE ue1.userid = '94' AND ue1.present_working = 1 AND ue1.statusid = 1 ORDER BY ue1.to_year DESC, ue1.to_month DESC LIMIT 1)
							LEFT JOIN master_countries mc ON mc.country_id = ue.country_id AND mc.statusid = 1
							LEFT JOIN user_education ued ON ued.userid = u.userid AND ued.education_id = (SELECT ued1.education_id FROM user_education ued1 WHERE ued1.userid = u.userid AND ued1.statusid = 1 ORDER BY ued1.to_year DESC, ued1.to_month DESC LIMIT 1)
							WHERE u.userid = '".$this->userid."' AND u.statusid = 1;";
				$stmt = $this->db->query($query);			
				return $stmt->fetchAll();
			}
			return 0;
		} catch(Exception $e) {
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	public function getProfileInfo(){
		try {
			if($this->userid) {
				$query = "SELECT u.date_of_birth, u.marital_status, u.phonenumber, up.interests FROM apmusers u
				LEFT JOIN user_profile up ON up.userid = u.userid
				WHERE u.userid = '".$this->userid."' AND u.statusid = 1;";
				$stmt = $this->db->query($query);			
				return $stmt->fetchAll();
			}
			return 0;
		} catch(Exception $e) {
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	public function getUserAddress() {
		try {	
			if($this->userid) {
				$query = "SELECT ua.address_type, ua.address1, ua.address2, ua.city, ua.street, ua.postal_code, ua.country_id, ua.state_id, mc.country, ms.name 
				FROM user_address ua 
				LEFT JOIN master_countries mc ON mc.country_id = ua.country_id
				LEFT JOIN master_states ms ON ms.state_id = ua.state_id
				WHERE ua.userid = '".$this->userid."' AND ua.statusid = 1;";
				//exit;
				
				$stmt = $this->db->query($query);			
				return $stmt->fetchAll();
			}
			return 0;
		} catch(Exception $e) {
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	public function getUserEducation($id = "") {
		try {	
			if($this->userid) {
				$query = "SELECT ue.* FROM user_education ue WHERE ue.userid = '".$this->userid."' AND ue.statusid = 1";
				if($id)
					$query .= " AND ue.education_id = ".$id;
				else
					$query .= " ORDER BY ue.to_year DESC, ue.to_month DESC;";
				$stmt = $this->db->query($query);			
				return $stmt->fetchAll();
			}
			return 0;
		} catch(Exception $e) {
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	public function getUserSkills($id = "") {
		try {	
			if($this->userid) {
				$query = "SELECT us.* FROM user_skills_set us WHERE us.userid = '".$this->userid."' AND us.statusid = 1";
				if($id)
					$query .= " AND us.user_skills_set_id = ".$id;
				else
					$query .= " ORDER BY us.last_used DESC;";
				$stmt = $this->db->query($query);			
				return $stmt->fetchAll();
			}
			return 0;
		} catch(Exception $e) {
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	public function getUserExperiance($id = "") {
		try {	
			if($this->userid) {
				$query = "SELECT ue.* FROM user_experience ue WHERE ue.userid = '".$this->userid."' AND ue.statusid = 1";
				if($id)
					$query .= " AND ue.experience_id = ".$id;
				else
					$query .= " ORDER BY ue.present_working DESC, ue.to_year DESC, ue.to_month DESC;";
				$stmt = $this->db->query($query);
				return $stmt->fetchAll();
			}
			return 0;
		} catch(Exception $e) {
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	public function updateAboutus($about_us) {
		try {
			if($this->userid) {
				$rows = $this->db->fetchAll('SELECT * FROM user_profile WHERE userid = "'.$this->userid.'"');
				$numRows = sizeof($rows);
				if($numRows > 0) {
					$query = 'UPDATE user_profile SET about_us = "'.$about_us.'", statusid = 1, updateddatetime = NOW() WHERE userid = "'.$this->userid.'";';
					$stmt = $this->db->query($query);
				} else {
					$query = 'INSERT INTO user_profile (userid, about_us, createddatetime, statusid, createdby) VALUES ('.$this->userid.', "'.$about_us.'", NOW(), 1, '.$this->userid.');';
					$stmt = $this->db->query($query);
				}
				return 1;
			}
			return 0;
		} catch(Exception $e) {
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	public function updatePersonalInfo($bmonth, $bday, $byear, $marial_status, $interests, $phone_number) {
		try {
			if($this->userid) {
				$query = 'UPDATE user_profile SET interests = "'.$interests.'" WHERE userid = "'.$this->userid.'";';
				$stmt = $this->db->query($query);
				$query = 'UPDATE apmusers SET phonenumber = "'.$phone_number.'", date_of_birth = "'.$byear."-".$bmonth."-".$bday.'", marital_status = "'.$marial_status.'" WHERE userid = "'.$this->userid.'";';
				$stmt = $this->db->query($query);
				return 1;
			}
			return 0;
		} catch(Exception $e) {
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	public function createupdateExperiance($exp_id, $company_name, $job_location, $job_title, $country_id, $industry_id, $state_id, $from_month, $from_year, $to_month, $to_year, $present_working, $company_description) {
		try {
			if($this->userid) {
				if($exp_id != "" && $exp_id != "new") {
					$stmt = $this->db->query("CALL SPuser_experience_edit(?, ?, ? , ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", array($exp_id,$this->userid,$company_name,$job_title,$job_location,$industry_id,$from_year,$from_month,$to_year,$to_month,$present_working,$company_description,$country_id,$state_id,"Save"));
				} else {
					$stmt = $this->db->query("CALL SPuser_experience_add(?, ? , ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", array($this->userid,$company_name,$job_title,$job_location,$industry_id,$from_year,$from_month,$to_year,$to_month,$present_working,$company_description,$country_id,$state_id,"Save"));
				}
				return $stmt->fetchAll();
			}
			return 0;
		} catch(Exception $e) {
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	public function createupdateEducation($edu_id, $school_name, $degree, $field_of_study, $from_month, $from_year, $to_month, $to_year, $school_description) {
		try {
			if($this->userid) {
				if($edu_id != "" && $edu_id != "new") {
					$stmt = $this->db->query("CALL SPuser_education_edit(?, ?, ? , ?, ?, ?, ?, ?, ?, ?, ?)", array($edu_id,$this->userid,$school_name,$degree,$field_of_study,$school_description,$from_year,$from_month,$to_year,$to_month,"Save"));
				} else {
					$stmt = $this->db->query("CALL SPuser_education_add(?, ? , ?, ?, ?, ?, ?, ?, ?, ?)", array($this->userid,$school_name,$degree,$field_of_study,$school_description,$from_year,$from_month,$to_year,$to_month,"Create"));
				}
				return $stmt->fetchAll();
			}
			return 0;
		} catch(Exception $e) {
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	public function updateAddressInfo($home_address1, $home_address2, $home_city, $home_street, $home_postal_code, $home_country_id, $home_state_id, $office_address1, $office_address2, $office_city, $office_street, $office_postal_code, $office_country_id, $office_state_id) {
		try {
			if($this->userid) {
				$stmt = $this->db->query("CALL SPuser_address(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", array($this->userid, $home_address1, $home_address2, $home_city, $home_street, $home_postal_code, $home_country_id, $home_state_id, $office_address1, $office_address2, $office_city, $office_street, $office_postal_code, $office_country_id, $office_state_id,"Save"));
				return $stmt->fetchAll();
			}
			return 0;
		} catch(Exception $e) {
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	public function getIndustry() {
		$query = "SELECT ui.* FROM user_industry ui WHERE statusid = 1;";
		$stmt = $this->db->query($query);			
		return $stmt->fetchAll();
	}
	
	public function getCountry() {
		$query = "SELECT mc.* FROM master_countries mc WHERE statusid = 1;";
		$stmt = $this->db->query($query);			
		return $stmt->fetchAll();
	}
	
	public function getState($country_id = "") {
		if($country_id) {
			$query = "SELECT ms.* FROM master_states ms WHERE country_id = '".$country_id."' AND statusid = 1;";
			$stmt = $this->db->query($query);			
			return $stmt->fetchAll();
		}
		return FALSE;
	}
	
	public function getTimezones() {
		$query = "SELECT mt.* FROM master_timezones mt WHERE statusid = 1 ORDER BY timezone_location;";
		$stmt = $this->db->query($query);			
		return $stmt->fetchAll();
		return FALSE;
	}
	
	public function updateProfiletitle($fname, $lname, $gender, $timezone) {
		try {
			if($this->userid) {
				$query = 'UPDATE apmusers SET firstname = "'.$fname.'", lastname = "'.$lname.'", gender = "'.$gender.'", timezone_id = "'.$timezone.'" WHERE userid = "'.$this->userid.'";';
				$stmt = $this->db->query($query);
				return 1;
			}
			return 0;
		} catch(Exception $e) {
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	public function update_member_profile($filename="") {
		try {
			if($this->userid) {
				$query = "SELECT user_image_id FROM user_images WHERE userid = '".$this->userid."'";
				$stmt = $this->db->query($query);
				$result = $stmt->fetchAll();
				$image_id = (isset($result[0]["user_image_id"])) ? $result[0]["user_image_id"] : "";
				if($image_id) {
					$query = 'UPDATE user_images SET image_path = "'.$filename.'", statusid = 1 WHERE user_image_id = "'.$image_id.'";';
					$stmt = $this->db->query($query);
				} else {
					$query = 'INSERT INTO user_images (userid, image_path, is_primary, statusid, createddatetime) values("'.$this->userid.'", "'.$filename.'", 1, 1, NOW());';
					$stmt = $this->db->query($query);
				}
				return 1;
			}
			return 0;
		} catch(Exception $e) {
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	public function delete_user_experiance($id){
		try {
			if($this->userid && $id) {
				$query = "DELETE FROM user_experience WHERE experience_id = '".$id."';";
				$stmt = $this->db->query($query);
				return 1;
			}
			return 0;
		} catch(Exception $e) {
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	public function delete_user_education($id){
		try {
			if($this->userid && $id) {
				$query = "DELETE FROM user_education WHERE education_id = '".$id."';";
				$stmt = $this->db->query($query);
				return 1;
			}
			return 0;
		} catch(Exception $e) {
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	public function createSkillSet($params) {
		try {
			if($this->userid && $params) {
				$query = "INSERT INTO user_skills_set (userid,user_skill,skill_version,last_used,experience_years,experience_months,createddatetime,updateddatetime,statusid,createdby,lastupdatedby) VALUES ('".$this->userid."','".$params["user_skill"]."','".$params["skill_version"]."','".$params["last_used_val"]."','".$params["experience_years_val"]."','".$params["experience_months_val"]."',NOW(),NOW(),'1','".$this->userid."','".$this->userid."');";
				$stmt = $this->db->query($query);
				//lastInsertId
				return 1;
			}
			return 0;
		} catch(Exception $e) {
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	public function editSkillSet($id, $params) {
		try {
			if($this->userid && $params) {
				$query = "UPDATE user_skills_set SET user_skill = '".$params["user_skill"]."', skill_version = '".$params["skill_version"]."', last_used = '".$params["last_used_val"]."', experience_years = '".$params["experience_years_val"]."', experience_months = '".$params["experience_months_val"]."', updateddatetime = NOW(), lastupdatedby = '".$this->userid."' WHERE user_skills_set_id = '".$id."' AND userid = '".$this->userid."';";
				$stmt = $this->db->query($query);
				return 1;
			}
			return 0;
		} catch(Exception $e) {
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
}
?>