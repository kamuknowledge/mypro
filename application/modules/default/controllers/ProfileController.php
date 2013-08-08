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
		/*echo "store/index/init";
		exit;  */		
		$this->profile = new Default_Model_Profile();
		$this->profiledb = new Default_Model_Profiledb();
        $this->_helper->layout->setLayout('default/profile_layout');
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
}
?>