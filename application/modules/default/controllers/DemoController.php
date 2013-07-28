<?php
/*
*================AllySoft Internal Use only========================

*-----------------------------------------------------------------*
* Copy Right Header Information*
*-----------------------------------------------------------------*
* Project	:	GetLinc
* File		:	DemoController.php 
* Module	:	Default Module
* Owner		:	RAM's 
* Purpose	:	This class is used for common Events operations for all user types
* Date		:	08/05/2012


* Modification History
* ----------------------------------------------------------------------------------------------------------------
* Dated		Version		Who		Description
* -----------------------------------------------------------------------------------------------------------------
* %D%		%T%			%W%
* -----------------------------------------------------------------------------------------------------------------

*===================================================================================================================
*/

class DemoController extends Zend_Controller_Action { 
	public $session;	// used for managing session with NAMESPACE MyPortal
	public $error;		// used for managing session with NAMESPACE MyPortalerror
	private $demo;		// used for creating an instance of model, Access is with in the class	
	private $demodb;

	/**
     * Purpose: Initiates sessions with Namespace 'MyPortal' and 'MyPortalerror'
     * Access is public
     *
     * @param	
     * @return  
     */
	
	public function init() { 
		//Assigning session
		$this->session = new Zend_Session_Namespace('MyPortal');
		$this->error = new Zend_Session_Namespace('MyPortalerror');
		
		// Calling DB Operations and Validations Classes
		$this->demo = new Default_Model_Demo();
		$this->demodb = new Default_Model_Demodb();
		
		// Setting Layout
        $this->_helper->layout->setLayout('default/layout');
		
		// Disable Layout
		//$this->setLayoutAction('store/layout');

		// Calling config registry values
		$this->config = Zend_Registry::get('config');

		// Including JS
		$this->view->headScript()->appendFile($this->view->baseUrl('public/default/js/dev_demo.js'),'text/javascript');

		// Including CSS
		$this->view->headLink()->setStylesheet($this->view->baseUrl('public/default/css/dev_demo.css'));
		
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
			// Code
			
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
	
	public function listAction() {
		try{			
			// Code
			
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
	
	public function addAction() {
		try{			
			// Code
			
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
			// Code
			
		}catch (Exception $e){
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}

}
?>