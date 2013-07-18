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
class Default_Model_Signin extends Default_Model_Signindb {
	
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
		//Assigning session
		$this->session = new Zend_Session_Namespace('MyClientPortal');
        $this->signinDb=new Default_Model_Signindb();        
		//Assigning a config registry
		$this->config = Zend_Registry::get('config');
		
		//Redirector handler
		$this->redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
		
		//Request Handler
		$this->requestHandler = Zend_Controller_Front::getInstance()->getRequest();
		
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
}