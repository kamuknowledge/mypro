<?php
/*
*================AllySoft Internal Use only========================

*-----------------------------------------------------------------*
* Copy Right Header Information*
*-----------------------------------------------------------------*
* Project	:	GetLinc
* File		:	Friendsuggestion.php 
* Module	:	Chat Friend Suggestion Module
* Owner		:	RAM's 
* Purpose	:	This class is used for chat management operations
* Date		:	08/05/2012


* Modification History
* ----------------------------------------------------------------------------------------------------------------
* Dated		Version		Who		Description
* -----------------------------------------------------------------------------------------------------------------
* %D%		%T%			%W%
* -----------------------------------------------------------------------------------------------------------------

*===================================================================================================================
*/

class Default_Model_Friendsuggestion extends Default_Model_Friendsuggestiondb {
	
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
				
		$this->friendsuggestiondb=new Default_Model_Friendsuggestiondb();
		
		//Assigning session
		$this->session = new Zend_Session_Namespace('MyPortal');

		//Assigning a config registry
		$this->config = Zend_Registry::get('config');		
		
		//Redirector handler
		//$this->redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
		
		//Request Handler
		//$this->requestHandler = Zend_Controller_Front::getInstance()->getRequest();
		
		//View Renderer
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
		$viewRenderer->initView();
		//Assigning renderer to access in the class
		$this->error = $viewRenderer->view;		
		$this->viewobj= $viewRenderer->view;
	}
	
}
?>