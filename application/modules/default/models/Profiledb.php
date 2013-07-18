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

class Default_Model_Profiledb extends Application_Model_DataBaseOperations {
	
	public $session;
	private $error;
	public $viewobj;
	public $userid;
	
	
	/**
     * Purpose: Constructor sets sessions for portal and portalerror
     *
     * Access is limited to class and extended classes
     *
     * @param   
     * @return  
     */
	
		
	public function __construct(){
		$this->session = new Zend_Session_Namespace('MyClientPortal');
		$this->error = new Zend_Session_Namespace('MyClientPortalerror');
		if(!$this->session->userid)
		{
			echo "Not logged in";
			exit;
		} else {
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
			parent::SetDatabaseConnection();
			/*$query = "SELECT u.* FROM apmusers u
						LEFT JOIN user_profile up ON up.userid = u.userid AND up.statusid = 1
						LEFT JOIN user_address ua ON ua.userid = u.userid AND ua.statusid = 1
						LEFT JOIN user_connections uc ON uc.userid = u.userid AND uc.statusid = 1
						LEFT JOIN user_education ue ON ue.userid = u.userid AND ue.statusid = 1
						LEFT JOIN user_experience uex ON uex.userid = u.userid AND uex.statusid = 1
						LEFT JOIN user_images ui ON ui.userid = u.userid AND ui.statusid = 1
						LEFT JOIN user_skills_set uss ON uss.userid = u.userid AND uss.statusid = 1
						WHERE u.userid = '".$this->userid."' AND u.statusid = 1";*/
			$query = "SELECT u.firstname, u.lastname, u.emailid, u.phonenumber, up.display_name, up.date_of_birth, up.gender, up.about_us, up.marital_status, up.interests, up.timezone_id, ui.image_path FROM apmusers u
						LEFT JOIN user_profile up ON up.userid = u.userid AND up.statusid = 1
						LEFT JOIN user_images ui ON ui.userid = u.userid AND ui.statusid = 1
						LEFT JOIN user_experience ue ON ue.userid = u.userid AND ue.experiance_id = (SELECT experiance_id FROM user_experience ue1 WHERE ue1.userid = u.userid AND ue1.statusid = 1)
						WHERE u.userid = '".$this->userid."' AND u.statusid = 1;";
			//exit;
			return Application_Model_Db::getResult($query);
		} catch(Exception $e) {
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	public function getUserAddress() {
		try {	
			parent::SetDatabaseConnection();
			$query = "SELECT ua.address_type, ua.address1, ua.address2, ua.city, ua.street, ua.postal_code, ua.country_id, ua.state_id FROM user_address ua WHERE ua.userid = '".$this->userid."' AND ua.statusid = 1;";
			//exit;
			return Application_Model_Db::getResult($query);
		} catch(Exception $e) {
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	public function getUserEducation() {
		try {	
			parent::SetDatabaseConnection();
			$query = "SELECT ue.* FROM user_education ue WHERE ue.userid = '".$this->userid."' AND ue.statusid = 1;";
			//exit;
			return Application_Model_Db::getResult($query);
		} catch(Exception $e) {
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	public function getUserExperiance() {
		try {	
			parent::SetDatabaseConnection();
			$query = "SELECT ue.* FROM user_experience ue WHERE ue.userid = '".$this->userid."' AND ue.statusid = 1;";
			//exit;
			return Application_Model_Db::getResult($query);
		} catch(Exception $e) {
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
}
?>