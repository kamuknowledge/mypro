<?php
/*
*================AllySoft Internal Use only========================

*-----------------------------------------------------------------*
* Copy Right Header Information*
*-----------------------------------------------------------------*
* Project	:	GetLinc
* File		:	ProfileController.php 
* Module	:	Default Module
* Owner		:	RAM's 
* Purpose	:	This class is used for common Profile operations for all user types
* Date		:	08/05/2012


* Modification History
* ----------------------------------------------------------------------------------------------------------------
* Dated		Version		Who		Description
* -----------------------------------------------------------------------------------------------------------------
* %D%		%T%			%W%
* -----------------------------------------------------------------------------------------------------------------

*===================================================================================================================
*/

class ProfileController extends Zend_Controller_Action { 
	public $session;	// used for managing session with NAMESPACE portal
	public $error;		// used for managing session with NAMESPACE portalerror
	private $profile;		// used for creating an instance of model, Access is with in the class	
	private $profiledb;

	/**
     * Purpose: Initiates sessions with Namespace 'portal' and 'portalerror' 
     *
     * Access is public
     *
     * @param	
     * 
     * @return  
     */
	
	public function init() { 
		$this->profile = new Default_Model_Profile();
		$this->profiledb = new Default_Model_Profiledb();
		
		/* Check Login */
		if(!$this->profile->check_login()){ $this->_redirect('/');exit;}		
		
        $this->_helper->layout->setLayout('default/profile_layout');
		$this->view->headScript()->appendFile('http://malsup.github.com/jquery.form.js','text/javascript');
		//$this->setLayoutAction('store/layout');		
	}
	
    
	/**
     * Purpose: Index action
     *
     * Access is public
     *
     * @param	
     * 
     * @return  
     */
	
	public function indexAction() {
		try{			
			//echo "store/index/index";
			//exit; 
		}catch (Exception $e){
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	

	
	
	
	/**
     * Purpose: Index action
     *
     * Access is public
     *
     * @param	
     * 
     * @return  
     */
	
	public function viewAction() {
		try{
			//$this->_helper->layout->disableLayout();
			$UserDetails["profile"] = $this->profiledb->getProfileDetails();
			$UserDetails["address"] = $this->profiledb->getUserAddress();
			$UserDetails["education"] = $this->profiledb->getUserEducation();
			$UserDetails["experiance"] = $this->profiledb->getUserExperiance();
			$UserDetails["timezones"] = $this->profiledb->getTimezones();
			//print_r($UserDetails);
			$this->view->UserDetails = $UserDetails;
			//echo "store/index/index";
			//exit; 
		}catch (Exception $e){
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	
	
	
	
	
		/**
     * Purpose: Index action
     *
     * Access is public
     *
     * @param	
     * 
     * @return  
     */
	
	public function editAction() {
		try{			
			//echo "store/index/index";
			//exit; exit;
		}catch (Exception $e){
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	public function editaboutusAction() {
		try{
			$params = $this->_getAllParams();	
			$this->_helper->layout->setLayout('default/empty_layout');
			$request = $this->getRequest();
			$Request_Values = $request->getPost();			
			if ($request->isPost()) {
				if(!$this->profile->edit_about_us($params)) {
					// return 1;
					// redirect to current url
					
				} else {
					echo 1;
					exit;
				}
			}else{			
				//$this->view->countrieslist = $this->merchantdb->getCountriesList();
				//return 0;
			}
		}catch (Exception $e){
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	public function addeditexperianceAction() {
		try{
			$params = $this->_getAllParams();
			$this->_helper->layout->setLayout('default/empty_layout');
			$this->view->IndustryList = $this->profiledb->getIndustry();
			$this->view->CountryList = $this->profiledb->getCountry();
			if(isset($params["input_type"]) && $params["input_type"]) {
				// Display form with error message
				$this->view->UserDetails = array(array("input_type"=>"error", "experience_id" => $params["experiance_idd"]));
			} else {
				// Normal form display functionality for the first time
				$request = $this->getRequest();
				$Request_Values = $request->getPost();
				if ($request->isPost()) {
					if(isset($Request_Values["type"]) && $Request_Values["type"] == "edit") {
						$id = $Request_Values["id"];
						if($id)
						{
							if($id != "new") {
								$this->view->UserDetails = $this->profiledb->getUserExperiance($id);
								$country = isset($this->view->UserDetails[0]["country_id"]) ? $this->view->UserDetails[0]["country_id"] : "";
								$this->view->StateList = $this->profiledb->getState($country);
							} else {
								$this->view->UserDetails = array(array("experience_id"=>"new", "company_name"=>"", "job_location"=>"", "country_id"=>"", "job_title"=>"", "industry_id"=>"", "state_id"=>"", "from_month"=>"", "from_year"=>"", "to_month"=>"", "to_year"=>"", "present_working"=>"", "company_description"=>"", ""=>"", ""=>""));
							}
						} else {
							echo 1;
							exit;
						}
					}
				}else{			
					//$this->view->countrieslist = $this->merchantdb->getCountriesList();
					//return 0;
				}
			}
		}catch (Exception $e){
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	public function vieweditexperianceAction() {
		try{
			$params = $this->_getAllParams();
			$request = $this->getRequest();
			$Request_Values = $request->getPost();
			if ($request->isPost()) {
				$this->_helper->layout->setLayout('default/empty_layout');
				if(isset($Request_Values["type"]) && $Request_Values["type"] == "edit") {
					$experiance_id = $Request_Values["experiance_id"];
					$edit_exp_id = $Request_Values["exp_id"];
					if($experiance_id)
					{
						$results = $this->profiledb->getUserExperiance($experiance_id);
						$this->view->UserDetails = $results[0];
					} else if($edit_exp_id) {
						if($this->profile->add_edit_experiance($params)) {
							$results = $this->profiledb->getUserExperiance($edit_exp_id);
							$this->view->UserDetails = $results[0];
						} else {
							// Load form with error messages
							$this->view->UserDetails = array("error"=>1, "exp_id"=>$edit_exp_id);
						}
					}
				} else if(isset($Request_Values["type"]) && $Request_Values["type"] == "new") {
					$exp_id = $this->profile->add_edit_experiance($params);
					if($exp_id) {
						$results = $this->profiledb->getUserExperiance($exp_id);
						$results[0]["dis_type"] = "new";
						$this->view->UserDetails = $results[0];
					} else {
						// Load form with error messages
						$this->view->UserDetails = array("error"=>1, "exp_id"=>"new");
					}
				}
			}else{
				echo 0;exit;
			}
		}catch (Exception $e){
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	public function getstatesAction() {
		$request = $this->getRequest();
		$Request_Values = $request->getPost();
		$country_arr = "";
		if ($request->isPost() && isset($Request_Values["country"])) {
			$country_db_arr = $this->profiledb->getState($Request_Values["country"]);
			foreach($country_db_arr as $value) {
				$country_arr .= "<option value='".$value["state_id"]."'>".$value["name"]."</option>";
			}
		}
		echo $country_arr;
		exit;
	}
	
	public function addediteducationAction() {
		try{
			$params = $this->_getAllParams();
			$this->_helper->layout->setLayout('default/empty_layout');
			//$this->view->IndustryList = $this->profiledb->getIndustry();
			//$this->view->CountryList = $this->profiledb->getCountry();
			if(isset($params["input_type"]) && $params["input_type"]) {
				// Display form with error message
				$this->view->UserDetails = array(array("input_type"=>"error", "education_id" => $params["education_idd"]));
			} else {
				// Normal form display functionality for the first time
				$request = $this->getRequest();
				$Request_Values = $request->getPost();
				if ($request->isPost()) {
					if(isset($Request_Values["type"]) && $Request_Values["type"] == "edit") {
						$id = $Request_Values["id"];
						if($id)
						{
							if($id != "new") {
								$this->view->UserDetails = $this->profiledb->getUserEducation($id);
								//$country = isset($this->view->UserDetails[0]["country_id"]) ? $this->view->UserDetails[0]["country_id"] : "";
								//$this->view->StateList = $this->profiledb->getState($country);
							} else {
								$this->view->UserDetails = array(array("education_id"=>"new", "company_name"=>"", "job_location"=>"", "country_id"=>"", "job_title"=>"", "industry_id"=>"", "state_id"=>"", "from_month"=>"", "from_year"=>"", "to_month"=>"", "to_year"=>"", "present_working"=>"", "company_description"=>"", ""=>"", ""=>""));
							}
							// return 1;
							// redirect to current url
							
						} else {
							echo 1;
							exit;
						}
					}
				}else{			
					//$this->view->countrieslist = $this->merchantdb->getCountriesList();
					//return 0;
				}
			}
		}catch (Exception $e){
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	public function viewediteducationAction() {
		try{
			$params = $this->_getAllParams();
			$request = $this->getRequest();
			$Request_Values = $request->getPost();
			if ($request->isPost()) {
				$this->_helper->layout->setLayout('default/empty_layout');
				if(isset($Request_Values["type"]) && $Request_Values["type"] == "edit") {
					$education_id = $Request_Values["education_id"];
					$edit_edu_id = $Request_Values["edu_id"];
					if($education_id)
					{
						$results = $this->profiledb->getUserEducation($education_id);
						$this->view->UserDetails = $results[0];
					} else if($edit_edu_id) {
						if($this->profile->add_edit_education($params)) {
							$results = $this->profiledb->getUserEducation($edit_edu_id);
							$this->view->UserDetails = $results[0];
						} else {
							// Load form with error messages
							$this->view->UserDetails = array("error"=>1, "edu_id"=>$edit_edu_id);
						}
					}
				} else if(isset($Request_Values["type"]) && $Request_Values["type"] == "new") {
					$edu_id = $this->profile->add_edit_education($params);
					if($edu_id) {
						$results = $this->profiledb->getUserEducation($edu_id);
						$results[0]["dis_type"] = "new";
						$this->view->UserDetails = $results[0];
					} else {
						// Load form with error messages
						$this->view->UserDetails = array("error"=>1, "edu_id"=>"new");
					}
				}
			}else{
				echo 0;exit;
			}
		}catch (Exception $e){
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	public function addeditpersonalAction() {
		try{
			$params = $this->_getAllParams();	
			$this->_helper->layout->setLayout('default/empty_layout');
			$request = $this->getRequest();
			$Request_Values = $request->getPost();			
			if ($request->isPost()) {
				if(isset($Request_Values["type"])) {
					if($Request_Values["type"] == "editform") {
						$this->view->UserDetails = $this->profiledb->getProfileInfo();
						$this->view->UserDetails[] = array("display"=>"edit");
					} else if($Request_Values["type"] == "edit"){
						if(!$this->profile->edit_profile_info($params))
							$this->view->UserDetails[] = array("display"=>"edit");
						$this->view->UserDetails = $this->profiledb->getProfileInfo();
					} else {
						$this->view->UserDetails = $this->profiledb->getProfileInfo();
					}
				}
			}else{
				//$this->view->countrieslist = $this->merchantdb->getCountriesList();
				//return 0;
			}
		}catch (Exception $e){
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	public function addeditaddressAction() {
		try{
			$params = $this->_getAllParams();	
			$this->_helper->layout->setLayout('default/empty_layout');
			$request = $this->getRequest();
			$Request_Values = $request->getPost();
			$this->view->CountryList = $this->profiledb->getCountry();
			if ($request->isPost()) {
				if(isset($Request_Values["type"])) {
					if($Request_Values["type"] == "editform") {
						$this->view->UserDetails = $this->profiledb->getUserAddress();
						foreach($this->view->UserDetails as $value) {
							$country = isset($value["country_id"]) ? $value["country_id"] : "";
							if($value["address_type"] == "Home") {
								$this->view->HomeStateList = $this->profiledb->getState($country);
							} else if($value["address_type"] == "Office") {
								$office_country = isset($value["country_id"]) ? $value["country_id"] : "";
								$this->view->OfficeStateList = $this->profiledb->getState($country);
							}
						}
						$this->view->UserDetails[] = array("display"=>"edit");
					} else if($Request_Values["type"] == "edit"){
						if(!$this->profile->edit_address_info($params))
							$this->view->UserDetails[] = array("display"=>"edit");
						$this->view->UserDetails = $this->profiledb->getUserAddress();
						foreach($this->view->UserDetails as $value) {
							$country = isset($value["country_id"]) ? $value["country_id"] : "";
							if($value["address_type"] == "Home") {
								$this->view->HomeStateList = $this->profiledb->getState($country);
							} else if($value["address_type"] == "Office") {
								$office_country = isset($value["country_id"]) ? $value["country_id"] : "";
								$this->view->OfficeStateList = $this->profiledb->getState($country);
							}
						}
					} else {
						$this->view->UserDetails = $this->profiledb->getUserAddress();
						foreach($this->view->UserDetails as $value) {
							$country = isset($value["country_id"]) ? $value["country_id"] : "";
							if($value["address_type"] == "Home") {
								$this->view->HomeStateList = $this->profiledb->getState($country);
							} else if($value["address_type"] == "Office") {
								$office_country = isset($value["country_id"]) ? $value["country_id"] : "";
								$this->view->OfficeStateList = $this->profiledb->getState($country);
							}
						}
					}
				}
			}else{
				//$this->view->countrieslist = $this->merchantdb->getCountriesList();
				//return 0;
			}
		}catch (Exception $e){
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	public function editmembertitleAction() {
		try{
			$params = $this->_getAllParams();	
			$this->_helper->layout->setLayout('default/empty_layout');
			$request = $this->getRequest();
			$Request_Values = $request->getPost();			
			if ($request->isPost()) {
				if(!$this->profile->edit_member_title($params)) {
					$this->view->UserDetails = $this->profiledb->getTimezones();
				} else {
					echo 1;
					exit;
				}
			}else{			
				//$this->view->countrieslist = $this->merchantdb->getCountriesList();
				//return 0;
			}
		}catch (Exception $e){
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	public function uploadAction() {
		try{		
			$params = $this->_getAllParams();	
			$this->_helper->layout->setLayout('default/empty_layout');
			// Define a destination
			$targetFolder = $this->view->baseUrl().'/public/uploads'; // Relative to the root

			if (!empty($_FILES)) {
				$tempFile = $_FILES['avatar']['tmp_name'];
				$targetPath = $_SERVER['DOCUMENT_ROOT'] . $targetFolder;
				$targetFile = rtrim($targetPath,'/') . '/' . $_FILES['avatar']['name'];
				
				// Validate the file type
				$fileTypes = array('jpg','jpeg','gif','png'); // File extensions
				$fileParts = pathinfo($_FILES['avatar']['name']);
				
				if (in_array($fileParts['extension'],$fileTypes)) {
					move_uploaded_file($tempFile,$targetFile);
					echo '1|'.$_FILES['avatar']['name'];
				} else {
					echo 'Invalid file type.';
				}
			}
			exit;
			
		}catch (Exception $e){
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
}
?>