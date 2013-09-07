<?php
/*
*================AllySoft Internal Use only========================

*-----------------------------------------------------------------*
* Copy Right Header Information*
*-----------------------------------------------------------------*
* Project	:	GetLinc
* File		:	Headerlogindb.php 
* Module	:	Header Login Module
* Owner		:	RAM's 
* Purpose	:	This class is used for Header Login related database operations
* Date		:	08/05/2012


* Modification History
* ----------------------------------------------------------------------------------------------------------------
* Dated		Version		Who		Description
* -----------------------------------------------------------------------------------------------------------------
* %D%		%T%			%W%
* -----------------------------------------------------------------------------------------------------------------

*===================================================================================================================
*/

class Default_Model_Headerlogindb extends Application_Model_DataBaseOperations {
	
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
		$this->db=Zend_Registry::get('db');
	}
	
	
	
	
	/**
     * Purpose: Fetching product list
     *
     * Access is limited to class and extended classes
     *
     * @param	int		$istart Start value
     * @param	int		$ilimit Limit value for fetching result set
     * @param	varchar	$cond Search condition
     * @return  object	Returns status message.	
     */
	
	public function getUserProfileImage(){
		try {			
			if(isset($this->session->userid) && trim($this->session->userid)!=''){
				$query = "SELECT * FROM user_images WHERE is_primary=1 AND statusid=1 AND userid=".$this->session->userid;
			}			
			//exit;			
			$stmt = $this->db->query($query);			
			return $stmt->fetchAll();
			
		} catch(Exception $e) {
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
}
?>