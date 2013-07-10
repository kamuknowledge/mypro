<?php
/*
*================AllySoft Internal Use only========================

*-----------------------------------------------------------------*
* Copy Right Header Information*
*-----------------------------------------------------------------*
* Project	:	GetLinc
* File		:	SigninController.php 
* Module	:	Default Module
* Owner		:	Bharath
* Purpose	:	This class is used for common Profile operations for all user types
* Date		:	10/07/2013


* Modification History
* ----------------------------------------------------------------------------------------------------------------
* Dated		Version		Who		Description
* -----------------------------------------------------------------------------------------------------------------
* %D%		%T%			%W%
* -----------------------------------------------------------------------------------------------------------------

*===================================================================================================================
*/

class SigninController extends Zend_Controller_Action { 
	public $session;	// used for managing session with NAMESPACE portal
	public $error;		// used for managing session with NAMESPACE portalerror
	private $signin;			// used for creating an instance of model, Access is with in the class
	private $signindb;			// used for creating an instance of model, Access is with in the class

	/**
     * Purpose: Initiates sessions with Namespace 'portal' and 'portalerror' 
     * 			and creates an instance of the model class 'Application_Model_Users'
     *
     * Access is public
     *
     * @param	
     * 
     * @return  
     */
	
	public function init() { 
		
		//Assigning session
		$this->session = new Zend_Session_Namespace('MyClientPortal');
		$this->session->userId = '';
		$this->session->isLoggedIn = '';
		$this->session->displayName = '';
		$this->session->emailId = '';
		$this->signin = new Application_Model_Signin();
        $this->_helper->layout->setLayout('default/layout');
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
     * Purpose: User registration page 
     *
     * Access is public
     *
     * @param	
     * 
     * @return  
     */
	public function loginAction() {
		try{
			$this->view->title = "Login User";
			$params = $this->_getAllParams();	
			$this->_helper->layout->setLayout('default/empty_layout');
			$request = $this->getRequest();
			$Request_Values = $request->getPost();			
			if ($request->isPost()) {
				if(!$this->signin->login($params)) {
					// return 1;
				} else {
					//return 0;
				}
			}else{			
				//$this->view->countrieslist = $this->merchantdb->getCountriesList();
				//return 0;
			}
		} catch(Exception $e) {
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}

}