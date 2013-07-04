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
class Application_Model_Signupdb extends Application_Model_Validation {
	
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
	protected function saveUser($firstname, $lastname, $useremail, $phonenumber, $password, $gender){
		try {
			parent::SetDatabaseConnection();
			$password = hash('sha256',$password);
			//$query = "call SPapmcreateuser('" . $firstname . "', '" . $lastname . "', '" . $useremail . "', '" . $password . "', '" . $username . "', " . $phonenumber . "," . $role . ", '".$merchant_id."', '" . $action . "', " . $adminid . ")";
			//exit;
			return 1;
			//return Application_Model_Db::getResult($query); 
		} catch(Exception $e) {
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
}
?>