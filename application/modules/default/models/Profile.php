<?php
/*
*================AllySoft Internal Use only========================

*-----------------------------------------------------------------*
* Copy Right Header Information*
*-----------------------------------------------------------------*
* Project	:	GetLinc
* File		:	Categories.php 
* Module	:	Category Management Module
* Owner		:	RAM's 
* Purpose	:	This class is used for category management operations
* Date		:	08/05/2012


* Modification History
* ----------------------------------------------------------------------------------------------------------------
* Dated		Version		Who		Description
* -----------------------------------------------------------------------------------------------------------------
* %D%		%T%			%W%
* -----------------------------------------------------------------------------------------------------------------

*===================================================================================================================
*/

class Default_Model_Profile extends Application_Model_Validation {
	
	public $session;
	private $error;
	private $config;
	private $redirector;
	private $requestHandler;
	public $viewobj;
		
	/**
     * Purpose: Constructor sets sessions for portal and portalerror and config and returns session objects
     *
     * Access is public
     *
     * @param	
     * @return  
     */
	
	public function __construct(){
		//$this->merchantdb = new Application_Model_Merchantdb();
		$this->Profiledb=new Default_Model_Profiledb();
		
		
		//Assigning session
		$this->session = new Zend_Session_Namespace('MyPortal');
                

		//Assigning a config registry
		$this->config = Zend_Registry::get('config');
		
		//Checking for security enabled or not
		//if(!isset($this->session->securityqenabled)) {
			//$this->choosesecurityquestiontype();
		//}
		
		//Redirector handler
		$this->redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
		
		//Request Handler
		$this->requestHandler = Zend_Controller_Front::getInstance()->getRequest();
		
		//View Renderer
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
		$viewRenderer->initView();
		//Assigning renderer to access in the class
		$this->error = $viewRenderer->view;
		
		$this->viewobj= $viewRenderer->view;
	}
	
	public function edit_about_us(Array $params) {
		try{
			$about_us = trim($params['about_us']);
			$error = 0;
			if($about_us == '') {				// Validation for about us
            	$this->error->error_about_us = Error_about_us_empty;
            	$error = 1;
            }
            if($error == 1) {
            	$this->error->error_aboutus_values = $params;
            	$error = 0;
            	return false;
            }         
            /*
             * Validation ends here
             */
			$outpt = $this->Profiledb->updateAboutus($about_us);
			return $outpt;
		} catch(Exception $e) {
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	public function add_edit_experiance(Array $params) {
		try{
			$exp_id = trim($params['exp_id']);
			$company_name = trim($params['company_name']);
			$job_location = trim($params['job_location']);
			$job_title = trim($params['job_title']);
			$country_id = trim($params['country']);
			$industry_id = trim($params['industry']);
			$state_id = trim($params['state']);
			$from_month = trim($params['from_month']);
			$from_year = trim($params['from_year']);
			$to_month = trim($params['to_month']);
			$to_year = trim($params['to_year']);
			$present_working = trim($params['present_working']);
			$company_description = trim($params['description']);
			$error = 0;
			if($company_name == '') {				// Validation for about us
            	$this->error->error_company_name = Error_experiance_company_name;
            	$error = 1;
            }
			if($job_location == '') {				// Validation for about us
            	$this->error->error_location_name = Error_experiance_company_name;
            	$error = 1;
            }
			if($job_title == '') {				// Validation for about us
            	$this->error->error_position_name = Error_experiance_company_name;
            	$error = 1;
            }
			if($industry_id == '') {				// Validation for about us
            	$this->error->error_industry_name = Error_experiance_company_name;
            	$error = 1;
            }
            if($error == 1) {
            	$this->error->error_experiance_values = $params;
            	$error = 0;
            	return false;
            }
            /*
             * Validation ends here
             */
			$outpt = $this->Profiledb->createupdateExperiance($exp_id, $company_name, $job_location, $job_title, $country_id, $industry_id, $state_id, $from_month, $from_year, $to_month, $to_year, $present_working, $company_description);
			if($exp_id && $exp_id == "new") {
				$outpt = $outpt[0];
				$result = explode('#', $outpt['toutput']);
				return $result[1];
			} else {
				return $outpt;
			}
		} catch(Exception $e) {
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	public function add_edit_education(Array $params) {
		try{
			$edu_id = trim($params['edu_id']);
			$school_name = trim($params['school_name']);
			$degree = trim($params['degree']);
			$field_of_study = trim($params['field_of_study']);
			$from_month = trim($params['from_month']);
			$from_year = trim($params['from_year']);
			$to_month = trim($params['to_month']);
			$to_year = trim($params['to_year']);
			$school_description = trim($params['description']);
			$error = 0;
			if($school_name == '') {				// Validation for about us
            	$this->error->error_school_name = Error_experiance_company_name;
            	$error = 1;
            }
			if($degree == '') {				// Validation for about us
            	$this->error->error_degree_name = Error_experiance_company_name;
            	$error = 1;
            }
			if($field_of_study == '') {				// Validation for about us
            	$this->error->error_field_of_study_name = Error_experiance_company_name;
            	$error = 1;
            }
            if($error == 1) {
            	$this->error->error_education_values = $params;
            	$error = 0;
            	return false;
            }
            /*
             * Validation ends here
             */
			$outpt = $this->Profiledb->createupdateEducation($edu_id, $school_name, $degree, $field_of_study, $from_month, $from_year, $to_month, $to_year, $school_description);
			if($edu_id && $edu_id == "new") {
				$outpt = $outpt[0];
				$result = explode('#', $outpt['toutput']);
				return $result[1];
			} else {
				return $outpt;
			}
		} catch(Exception $e) {
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	public function edit_profile_info(Array $params) {
		try{
			$bmonth = trim($params['bmonth']);
			$bday = trim($params['bday']);
			$byear = trim($params['byear']);
			$marial_status = trim($params['marial_status']);
			$interests = trim($params['interests']);
			$phone_number = trim($params['phone_number']);
			$error = 0;
            if($error == 1) {
            	$this->error->error_profileinfo_values = $params;
            	$error = 0;
            	return false;
            }
            /*
             * Validation ends here
             */
			$outpt = $this->Profiledb->updatePersonalInfo($bmonth, $bday, $byear, $marial_status, $interests, $phone_number);
			return $outpt;
		} catch(Exception $e) {
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	public function edit_address_info(Array $params) {
		try{
			$home_address1 = trim($params['home_address1']);
			$home_address2 = trim($params['home_address2']);
			$home_city = trim($params['home_city']);
			$home_street = trim($params['home_street']);
			$home_postal_code = trim($params['home_postal_code']);
			$home_country_id = trim($params['home_country_id']);
			$home_state_id = trim($params['home_state_id']);
			$office_address1 = trim($params['office_address1']);
			$office_address2 = trim($params['office_address2']);
			$office_city = trim($params['office_city']);
			$office_street = trim($params['office_street']);
			$office_postal_code = trim($params['office_postal_code']);
			$office_country_id = trim($params['office_country_id']);
			$office_state_id = trim($params['office_state_id']);
			$error = 0;
            if($error == 1) {
            	$this->error->error_profileinfo_values = $params;
            	$error = 0;
            	return false;
            }
            /*
             * Validation ends here
             */
			$outpt = $this->Profiledb->updateAddressInfo($home_address1, $home_address2, $home_city, $home_street, $home_postal_code, $home_country_id, $home_state_id, $office_address1, $office_address2, $office_city, $office_street, $office_postal_code, $office_country_id, $office_state_id);
			return $outpt;
		} catch(Exception $e) {
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
}
?>