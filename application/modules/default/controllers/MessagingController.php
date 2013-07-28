<?php
/*
*================AllySoft Internal Use only========================

*-----------------------------------------------------------------*
* Copy Right Header Information*
*-----------------------------------------------------------------*
* Project	:	GetLinc
* File		:	Messaging.php 
* Module	:	Default Module
* Owner		:	RAM's 
* Purpose	:	This class is used for internal messaging operations for all user types
* Date		:	26/07/2013

* Modification History
* ----------------------------------------------------------------------------------------------------------------
* Dated		Version		Who		Description
* -----------------------------------------------------------------------------------------------------------------
* %D%		%T%			%W%
* -----------------------------------------------------------------------------------------------------------------

*===================================================================================================================
*/

class MessagingController extends Zend_Controller_Action { 
	public $session;	// used for managing session with NAMESPACE portal
	public $error;		// used for managing session with NAMESPACE portalerror
	private $message;		// used for creating an instance of model, Access is with in the class	
	private $messagesdb;
	
	
	/**
     * Purpose: Initiates sessions with Namespace 'portal' and 'portalerror' 
     * Access is public
	 
     * @param	
     * @return  
     */
	
	public function init() {
	
		//Assigning session
		$this->session = new Zend_Session_Namespace('MyPortal');
		$this->error = new Zend_Session_Namespace('MyPortalerror');
		
		// Calling DB Operations and Validations Classes
		$this->message = new Default_Model_Messages();
		$this->messagesdb = new Default_Model_Messagesdb();
		
		// Setting Layout
        $this->_helper->layout->setLayout('default/layout');
		
		// Disable Layout
		//$this->setLayoutAction('store/layout');

		// Calling config registry values
		$this->config = Zend_Registry::get('config');		
	}
	
    
	
	/**
     * Purpose: Index action
     * Access is public
     *
     * @param	
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
     * Access is public
     *
     * @param	
     * @return  
     */
	
	public function inboxAction() {
		try{	
			$InboxDetails["inbox"] = $this->messagesdb->getInboxMessages();			
			
			$this->view->InboxDetails = $InboxDetails;
			//echo "store/index/index";
			//exit; 
			//echo "hi";exit;		
		}catch (Exception $e){
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	
	
	
	
	/**
     * Purpose: Index action
     * Access is public
     *
     * @param	
     * @return  
     */
	
	public function composeAction() {
		try{	
			// code

			
		}catch (Exception $e){
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	
	
	
	
	/**
     * Purpose: Index action
     * Access is public
     *
     * @param	
     * @return  
     */
	
	public function viewAction() {
		try{	
			// code

			
		}catch (Exception $e){
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}

}
?>