<?php
/*
*================AllySoft Internal Use only========================

*-----------------------------------------------------------------*
* Copy Right Header Information*
*-----------------------------------------------------------------*
* Project	:	GetLinc
* File		:	Productss.php 
* Module	:	Product Management Module
* Owner		:	RAM's 
* Purpose	:	This class is used for product management operations
* Date		:	08/05/2012


* Modification History
* ----------------------------------------------------------------------------------------------------------------
* Dated		Version		Who		Description
* -----------------------------------------------------------------------------------------------------------------
* %D%		%T%			%W%
* -----------------------------------------------------------------------------------------------------------------

*===================================================================================================================
*/

class Default_Model_Productss extends Application_Model_Validation {
	
	public $session;
	public $sessionid;
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
		
		//Assigning session
		$this->session = new Zend_Session_Namespace('MyClientPortal');
		$this->error = new Zend_Session_Namespace('MyClientPortalerror');
		
		$this->sessionid = new Zend_Session_Namespace('MyClientPortalId');
		if(!isset($this->sessionid->session_id) && trim($this->sessionid->session_id)==''){
			$this->sessionid->session_id = time().get_rand_id(8);
		}
		//echo "##".$this->sessionid->session_id."##";exit;
		
		
		// Calling DB Operations
		$this->productssdb=new Default_Model_Productssdb();
		
		
		// Calling config registry values
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
	
	
	
	
	
	/**
     * Purpose: Creates product images
     *
     * Access is public
     *
     * @param	Array	$params Create product image parameters
     * 
     * @return  object	Returns a boolean of status.
     */
	
	public  function insertViewCartProduct($product_id,$product_price_id,$action) {
		try{				
			$ProductViewCartSet = $this->productssdb->insertViewCartProduct($product_id, $product_price_id, $action, $this->session->userid, $this->sessionid->session_id);
			//print_r($ProductViewCartSet);exit;
			return false;				
		} catch(Exception $e) {
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
}
?>