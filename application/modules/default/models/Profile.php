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
	
}
?>