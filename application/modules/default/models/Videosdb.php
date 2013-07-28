<?php
/*
*================AllySoft Internal Use only========================

*-----------------------------------------------------------------*
* Copy Right Header Information*
*-----------------------------------------------------------------*
* Project	:	GetLinc
* File		:	Videosdb.php 
* Module	:	Internal Messaging Management Module
* Owner		:	RAM's 
* Purpose	:	This class is used for Internal Messaging management related database operations
* Date		:	08/05/2012


* Modification History
* ----------------------------------------------------------------------------------------------------------------
* Dated		Version		Who		Description
* -----------------------------------------------------------------------------------------------------------------
* %D%		%T%			%W%
* -----------------------------------------------------------------------------------------------------------------

*===================================================================================================================
*/

class Default_Model_Videosdb {
	
	public $session;
	private $error;
	public $viewobj;
	
	
	/**
     * Purpose: Constructor sets sessions for MyClientPortal and MyClientPortalerror
     * Access is limited to class and extended classes
     * @param   
     * @return  
     */
	
		
	public function __construct(){
		
		//Assigning session
		$this->session = new Zend_Session_Namespace('MyClientPortal');
		$this->error = new Zend_Session_Namespace('MyClientPortalerror');
		
		// DB Connection
		$this->db=Zend_Registry::get('db');
		
		// Calling config registry values
		$this->config = Zend_Registry::get('config');
	}
	
	

	
	/**
     * Purpose: Fetching list
     * Access is limited to class and extended classes
     *
     * @param	varchar	$cond Search condition
     * @return  object	Returns status message.	
     */
	
	public function getCategoriesList(){
		try {	
			// Example Method List
			
			
		} catch(Exception $e) {
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	
}
?>