<?php
/*
*================AllySoft Internal Use only========================

*-----------------------------------------------------------------*
* Copy Right Header Information*
*-----------------------------------------------------------------*
* Project	:	GetLinc
* File		:	Signindb.php 
* Module	:	User Signin Module
* Owner		:	Bharath 
* Purpose	:	This class is used for user management related database operations
* Date		:	10/07/2013


* Modification History
* ----------------------------------------------------------------------------------------------------------------
* Dated		Version		Who		Description
* -----------------------------------------------------------------------------------------------------------------
* %D%		%T%			%W%
* -----------------------------------------------------------------------------------------------------------------

*===================================================================================================================
*/

//class Application_Model_Userdb extends Application_Model_DataBaseOperations {

class Default_Model_Signindb {
	
	public $session;
	public $db;
	private $error;
	
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
		$this->error = new Zend_Session_Namespace('MyPortalerror');
		$this->db=Zend_Registry::get('db');
		//print_r($this->db);
	}
	
	
	/**
     * Purpose: Creates user and returns status of the user creation process 
     *
     * Access is limited to class and extended classes
     *
     * @param   varchar $useremail User email
     * @param   varchar $password Password
     * @param	varchar action action
     * @return  object	Returns status message.	
     */
	public function LoginUserdb($useremail, $password, $action){
		try {
			//sparent::SetDatabaseConnection();
			 $password = hash('sha256',$password);
			
			
			//$query = "call SPuserlogin('" . $useremail . "', '" . $password . "', '5', '" . $action . "')";
			//return Application_Model_Db::getResult($query);
			//echo "<pre>";print_r($this->db);
			//exit;
			$stmt = $this->db->query("CALL SPuserlogin(?, ?, ? , ?)", array($useremail,$password,'5',$action));			
			return $stmt->fetchAll();
			
		} catch(Exception $e) {
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
}
?>