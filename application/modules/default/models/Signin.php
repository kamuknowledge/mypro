<?php
/*
*================AllySoft Internal Use only========================

*-----------------------------------------------------------------*
* Copy Right Header Information*
*-----------------------------------------------------------------*
* Project	:	GetLinc
* File		:	Signin.php 
* Module	:	User Signin Module
* Owner		:	Bharath
* Purpose	:	This class is used for user management operations
* Date		:	10/07/2013


* Modification History
* ----------------------------------------------------------------------------------------------------------------
* Dated		Version		Who		Description
* -----------------------------------------------------------------------------------------------------------------
* %D%		%T%			%W%
* -----------------------------------------------------------------------------------------------------------------

*===================================================================================================================
*/



//class Application_Model_Users extends Application_Model_Validation {
class Default_Model_Signin extends Application_Model_Validation {
	
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
		
		$this->signinDb=new Default_Model_Signindb();
		
		//Assigning session
		$this->session = new Zend_Session_Namespace('MyClientPortal');
                
		//Assigning a config registry
		$this->config = Zend_Registry::get('config');
		
		//Redirector handler
		$this->redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
		
		//Request Handler
		$this->requestHandler = Zend_Controller_Front::getInstance()->getRequest();
		
		/*$this->view->headLink()->setStylesheet($this->view->baseUrl('public/default').'/css/dev_profile.css','text/stylesheet');    
		$this->view->headScript()->appendFile($this->view->baseUrl('public/default').'/js/dev_profile.js','text/javascript');*/
		
		//View Renderer
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
		$viewRenderer->initView();
		//Assigning renderer to access in the class
		$this->error = $viewRenderer->view;
		$this->viewobj= $viewRenderer->view;
		$this->db=Zend_Registry::get('db');
	}
	
	/**
     * Purpose: User login
     *
     * Access is public
     *
     * @param	Array	$params user parameters
     * 
     * @return  object	Returns a boolean of status.
     */
	
	public function login(Array $params) {
		try{
			$useremail = trim($params['email_id']);
			$password = trim($params['password']);
			$action = "Login"; // current action $controller = $this->getRequest()->getControllerName(); $action = $this->getRequest()->getActionName();		
			$error = 0;
			if($useremail == '') {				//Validation for user email
            	$this->error->error_user_email = Error_login_user_email_empty;
            	$error = 1;
            }
			if($password == '') {				//Validation for new password
            	$this->error->error_user_password = Error_login_password_empty;
            	$error = 1;
            }
            if($error == 1) {
            	$this->error->error_loginuser_values = $params;
            	$error = 0;
            	return false;
            }         
            /*
             * Validation ends here
             */
			 
			$outpt = $this->signinDb->LoginUserdb($useremail, $password, $action);
			//print_r($outpt);exit;
			$outpt = $outpt[0];
			$result = explode('#', $outpt['omess']);				
			if($result[0] == 1) {
				$this->session->isSignedIn = 1;
				$this->session->role = $outpt['role'] ;
				$this->session->roleid = $outpt['roleid'] ;
				$this->session->userid = $outpt['userid'] ;				
				$this->session->email = $outpt['email'] ;
				$this->session->name = $outpt['name'] ;				
				//$this->session->success = Title_Successful_login . ' with Login ID ' . $gender ;
				return true;
			} else {				
				$this->error->error_loginuser_db_value = $result[1];
				$this->error->error_loginuser_values = $params;
				$this->error->error = Failure_user_creation . ' with Login ID ' . $useremail ;
				return false;
			}
		} catch(Exception $e) {
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	public  function changePassword(Array $params) {
		try{
			$old_pwd = trim($params['old_pwd']);
			$new_pwd = trim($params['new_pwd']);
			$action = trim($params['change_pwd_submit']);
			
			$error = 0;
			
			if($old_pwd == '') {				//Validation for old password
            	$this->error->error_old_password = Error_signup_user_password_empty;
            	$error = 1;
            } else if(strlen($old_pwd) > 16) {
            	$this->error->error_old_password = Error_signup_password_field_max;
            	$error = 1;
            } else if(strlen($old_pwd) < 8) {
            	$this->error->error_old_password = Error_signup_password_field_min;
            	$error = 1;
            }
			
			if($new_pwd == '') {				//Validation for new password
            	$this->error->error_new_password = Error_signup_user_password_empty;
            	$error = 1;
            } else if(strlen($new_pwd) > 16) {
            	$this->error->error_new_password = Error_signup_password_field_max;
            	$error = 1;
            } else if(strlen($new_pwd) < 8) {
            	$this->error->error_new_password = Error_signup_password_field_min;
            	$error = 1;
            }
            
            if($error == 1) {
            	$this->error->error_change_values = $params;
            	$error = 0;
            	return false;
            }
          
            /*
             * Validation ends here
             */
				
			$outpt = $this->signinDb->updatePassword($old_pwd, $new_pwd, $action);
			
			if($outpt == 1) {
				$this->session->success = "New password has been updated successfully.";
				return true;
			} else {
				$this->error->error_change_values = $params;
				$this->error->error = "Invalid Password." ;
				return false;
			}
			
		} catch(Exception $e) {
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
}