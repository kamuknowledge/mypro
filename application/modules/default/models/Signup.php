<?php
/*
*================AllySoft Internal Use only========================

*-----------------------------------------------------------------*
* Copy Right Header Information*
*-----------------------------------------------------------------*
* Project	:	GetLinc
* File		:	Users.php 
* Module	:	User Management Module
* Owner		:	RAM's 
* Purpose	:	This class is used for user management operations
* Date		:	08/05/2012


* Modification History
* ----------------------------------------------------------------------------------------------------------------
* Dated		Version		Who		Description
* -----------------------------------------------------------------------------------------------------------------
* %D%		%T%			%W%
* -----------------------------------------------------------------------------------------------------------------

*===================================================================================================================
*/



//class Application_Model_Users extends Application_Model_Validation {
class Default_Model_Signup extends Application_Model_Validation {
	
	public $session;
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
	
		$this->Signupdb=new Default_Model_Signupdb();
		
		//Assigning session
		$this->session = new Zend_Session_Namespace('MyClientPortal');
                

		//Assigning a config registry
		$this->config = Zend_Registry::get('config');
		
		//Redirector handler
		$this->redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
		
		//Request Handler
		$this->requestHandler = Zend_Controller_Front::getInstance()->getRequest();
		
		$this->view->headStyle()->appendFile($this->view->baseUrl('public/default').'/css/dev_profile.css','text/stylesheet');   
		$this->view->headScript()->appendFile($this->view->baseUrl('public/default').'/js/dev_profile.js','text/javascript');
		
		//View Renderer
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
		$viewRenderer->initView();
		//Assigning renderer to access in the class
		$this->error = $viewRenderer->view;
		$this->viewobj= $viewRenderer->view;
	}
	
	/**
     * Purpose: Creates user and also used for updation of his profile and returns an a boolean value of status
     *
     * Access is public
     *
     * @param	Array	$params Create user parameters
     * 
     * @return  object	Returns a boolean of status.
     */
	
	public  function createUser(Array $params) {
		try{
			/*echo "<pre>";
			print_r($params);
			exit;*/
			$firstname = trim($params['first_name']);
			$lastname = trim($params['last_name']);
			$useremail = trim($params['email_id']);
			$phonenumber = trim($params['mobile']);
			$password = trim($params['password']);
			$gender = trim($params['gender']);	
			$action = trim($params['reg_submit']);			

			//echo $this->validate_alphanumeric_space($firstname);
			/*
			 * Validation for firstname(Alpha numeric), lastname(Alpha Numeric), useremail(Email), phonenumber (Numeric)
			 * 				  username(Email), role(Numeric), action(alpha)
			 */
			
			
			$error = 0;
			
			/*print_r($this->error->error_createuser_values);
			exit;*/
            if($firstname == '') {				//Validation for firstname
            	$this->error->error_createuser_firstname = Error_Create_users_firstname_empty;
            	$error = 1;
            	//return false;
            } else if(!$this->validate_alphanumeric_space($firstname)) {
            	$params['firstname'] = '';
            	$this->error->error_createuser_firstname = Error_Create_users_firstname;
            	$error = 1;
            	//return false;
            } else if(strlen($firstname) >20) {
            	$this->error->error_createuser_firstname = Error_name_field;
            	$error = 1;
            } else if(strlen($firstname) < 3) {
            	$this->error->error_createuser_firstname = Error_name_field_min;
            	$error = 1;
            }
            
			if($lastname == '') {				//Validation for lastname
            	$this->error->error_createuser_lastname = Error_Create_users_lastname_empty;
            	$error = 1;
            	//return false;
            } else if(!$this->validate_alphanumeric_space($lastname)) {
            	$params['lastname'] = '';
            	$this->error->error_createuser_lastname = Error_Create_users_lastname;
            	$error = 1;
            	//return false;
            } else if(strlen($lastname) >20) {
            	$this->error->error_createuser_lastname = Error_name_field;
            	$error = 1;
            } else if(strlen($lastname) < 3) {
            	$this->error->error_createuser_lastname = Error_name_field_min;
            	$error = 1;
            }
            
			if($useremail == '') {				//Validation for user email
            	$this->error->error_createuser_email = Error_Create_users_email_empty;
            	$error = 1;
            	//return false;
            } else if(!$this->validate_email_dot_underscore($useremail)) {
            	$params['useremail'] = '';
            	$this->error->error_createuser_email = Error_Create_users_email;
            	$error = 1;
            	//return false;
            } else if(strlen($useremail) >50) {
            	$this->error->error_createuser_email = Error_email_field;
            	$error = 1;
            } else if(strlen($useremail) < 3) {
            	$this->error->error_createuser_email = Error_name_field_min;
            	$error = 1;
            }
            
			if($phonenumber == '') {			//Validation for phone number
            	$this->error->error_createuser_phonenumber = Error_Create_users_phone_empty;
            	$error = 1;
            	//return false;
            } else if(!(int) $phonenumber || substr($phonenumber, 0, 1) == 0) {
            	$this->error->error_createuser_phonenumber = Error_Create_users_phone;
            	$error = 1;
            	//return false;
            } else if(!$this->validate_numeric($phonenumber)) {
            	$params['phonenumber'] = '';
            	$this->error->error_createuser_phonenumber = Error_Create_users_phone;
            	$error = 1;
            	//return false;
            } else if(strlen($phonenumber) >10) {
            	$this->error->error_createuser_phonenumber = Error_phone_field_max;
            	$error = 1;
            } else if(strlen($phonenumber) <10) {
            	$this->error->error_createuser_phonenumber = Error_phone_field_min;
            	$error = 1;
            }
			
			if($password == '') {				//Validation for new password
            	$this->error->error_createuser_password = Error_signup_user_password_empty;
            	$error = 1;
            } /*else if(!$this->validate_alphanumeric_password($password)) {
            	$params['password'] = '';
            	$this->error->error_createuser_password = Error_signup_user_password;
            	$error = 1;
            }*/ else if(strlen($password) > 16) {
            	$this->error->error_createuser_password = Error_signup_password_field_max;
            	$error = 1;
            } else if(strlen($password) < 8) {
            	$this->error->error_createuser_password = Error_signup_password_field_min;
            	$error = 1;
            }
            
            if($error == 1) {
            	$this->error->error_createuser_values = $params;
            	$error = 0;
            	return false;
            }
          
            /*
             * Validation ends here
             */
          		
				//$password = make_password(8);
				//$password1=hash('sha256',$password);
				
				$outpt = $this->Signupdb->saveUser($firstname, $lastname, $useremail,$phonenumber, $password, $gender, $action);				
				$outpt = $outpt[0];
				$result = explode('#', $outpt['toutput']);
				
				if($result[0] == 1) {
					/*
					$toname = urlencode($firstname) . ' ' . urlencode($lastname);
					$this->sendmail(urldecode($useremail), $firstname, $lastname, $result[1], NULL, $gender);
					//$this->sendmailtemplate($useremail, $toname, User_Mail_Loginid, $username);				 	
				 	sleep(4);
				 	$this->sendmail(urldecode($useremail), $firstname, $lastname, $result[1], $password);					
					//$this->sendmailtemplate($useremail, $toname, User_Mail_Password, $password);*/
					$this->session->success = Success_user_creation . ' with Login ID ' . $useremail ;
					return true;
				} else {
					$this->error->error_createuser_db_value = $result[1];
					$this->error->error_createuser_values = $params;
					$this->error->error = Failure_user_creation . ' with Login ID ' . $useremail ;
					return false;
				}
			
		} catch(Exception $e) {
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
}
?>