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
		$this->profile = new Application_Model_Profile();
		$this->profiledb = new Application_Model_Profiledb();
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

}
?>