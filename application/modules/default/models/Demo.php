<?php
/*
*================AllySoft Internal Use only========================

*-----------------------------------------------------------------*
* Copy Right Header Information*
*-----------------------------------------------------------------*
* Project	:	GetLinc
* File		:	Demo.php 
* Module	:	User Management Module
* Owner		:	RAM's 
* Purpose	:	This class is used for user messages related database operations
* Date		:	26/07/2013

* Modification History
* ----------------------------------------------------------------------------------------------------------------
* Dated		Version		Who		Description
* -----------------------------------------------------------------------------------------------------------------
* %D%		%T%			%W%
* -----------------------------------------------------------------------------------------------------------------

*===================================================================================================================
*/

class Default_Model_Demo extends Application_Model_Validation{
	
	public $session;
	private $error;
	private $demodb;	        
	
	/**
     * Purpose: Constructor sets sessions for MyPortal and MyPortalerror
     * Access is limited to class and extended classes
     * @param   
     * @return  
     */
	
		
	public function __construct(){
	
		//Assigning session
		$this->session = new Zend_Session_Namespace('MyPortal');
		$this->error = new Zend_Session_Namespace('MyPortalerror');
		
		
		// Calling config registry values
		$this->config = Zend_Registry::get('config');
		
		
		// Calling DB Operations
		$this->messagesdb=new Default_Model_Demodb();
	}
}	
?>	