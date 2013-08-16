<?php
/*
*================AllySoft Internal Use only========================

*-----------------------------------------------------------------*
* Copy Right Header Information*
*-----------------------------------------------------------------*
* Project	:	GetLinc
* File		:	Headermenudb.php 
* Module	:	Header Menu Module
* Owner		:	RAM's 
* Purpose	:	This class is used for Header Menu related database operations
* Date		:	08/05/2012


* Modification History
* ----------------------------------------------------------------------------------------------------------------
* Dated		Version		Who		Description
* -----------------------------------------------------------------------------------------------------------------
* %D%		%T%			%W%
* -----------------------------------------------------------------------------------------------------------------

*===================================================================================================================
*/

class Default_Model_Headermenudb extends Application_Model_DataBaseOperations {
	
	public $session;
	private $error;
	public $viewobj;
	
	
	/**
     * Purpose: Constructor sets sessions for portal and portalerror
     *
     * Access is limited to class and extended classes
     *
     * @param   
     * @return  
     */
	
		
	public function __construct(){
		$this->session = new Zend_Session_Namespace('MyClientPortal');
		$this->error = new Zend_Session_Namespace('MyClientPortalerror');
		$this->sessionid = new Zend_Session_Namespace('MyClientPortalId');
		
		// DB Connection
		$this->db=Zend_Registry::get('db');
	}
	
	
	
	/**
     * Purpose: Fetching view cart count
     * Access is limited to class and extended classes
     *
     * @param	varchar	$cond Search condition
     * @return  object	Returns status message.	
     */
	
	public function getCartCount(){
		try {
			$query = "SELECT count(temp_cart_id) as cart_count FROM store_users_temp_cart sutc WHERE sutc.statusid=1 AND (sutc.userid=".$this->session->userid." OR sutc.user_session_id = '".$this->sessionid->session_id."')";
			//exit;			
			$stmt = $this->db->query($query);			
			return $stmt->fetch();
			
		} catch(Exception $e) {
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
}
?>