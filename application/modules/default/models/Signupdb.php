<?php
/*
*================AllySoft Internal Use only========================

*-----------------------------------------------------------------*
* Copy Right Header Information*
*-----------------------------------------------------------------*
* Project	:	GetLinc
* File		:	Userdb.php 
* Module	:	User Management Module
* Owner		:	RAM's 
* Purpose	:	This class is used for user management related database operations
* Date		:	08/05/2012


* Modification History
* ----------------------------------------------------------------------------------------------------------------
* Dated		Version		Who		Description
* -----------------------------------------------------------------------------------------------------------------
* %D%		%T%			%W%
* -----------------------------------------------------------------------------------------------------------------

*===================================================================================================================
*/

//class Application_Model_Userdb extends Application_Model_DataBaseOperations {
class Default_Model_Signupdb {
	
	public $session;
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
		$this->session = new Zend_Session_Namespace('MyPortal');
		$this->error = new Zend_Session_Namespace('MyPortalerror');
		$this->db=Zend_Registry::get('db');
	}
	
	
	/**
     * Purpose: Creates user and returns status of the user creation process 
     *
     * Access is limited to class and extended classes
     *
     * @param   varchar $ifirstname First name
     * @param   varchar $ilastname Last name
     * @param	int 	$iusertype User type id
     * @param	varchar	$iemailid Email id
     * @param	int 	$irole Role id
     * @param	int		$istatus Status Id
     * @param	int		$icreator User Creator Id
     * @param	varchar $icreatoraction User saving action
     * @param	boolean	$iupdate Flag for updation of user records
     * @return  object	Returns status message.	
     */
	public function saveUser($firstname, $lastname, $useremail, $phonenumber, $password, $gender, $action){
		try {
			//parent::SetDatabaseConnection();
			$password = hash('sha256',$password);
			
			//$query = "call SPregisteruser('" . $firstname . "', '" . $lastname . "', '" . $useremail . "', '" . $password . "', '" . $phonenumber . "', '" . $gender . "','" . $action . "')";
			//return Application_Model_Db::getResult($query);
			
			$stmt = $this->db->query("CALL SPregisteruser(?, ?, ? , ?, ?, ?, ?)", array($firstname,$lastname,$useremail,$password,$phonenumber,$gender,$action));			
			return $stmt->fetchAll();			
			
		} catch(Exception $e) {
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	public function updatePassword($useremail, $password, $action){
		try {
			$rows = $this->db->fetchAll('SELECT * FROM apmusers WHERE emailid = "'.$useremail.'"');
			$numRows = sizeof($rows);
			if($numRows > 0) {
				$password = hash('sha256',$password);
				$query = 'UPDATE apmusers SET password = "'.$password.'", updateddatetime = NOW() WHERE emailid = "'.$useremail.'";';
				$stmt = $this->db->query($query);
				return 1;
			}
			return 0;
		} catch(Exception $e) {
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
}
?>