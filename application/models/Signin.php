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
class Application_Model_Signin extends Application_Model_Signindb {
	
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
		$this->session = new Zend_Session_Namespace('MyPortal');
                

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
	
	public function loginAction(Array $params) {
		try{
			/*echo "<pre>";
			print_r($params);
			exit;*/
			$useremail = trim($params['email_id']);
			$password = trim($params['password']);
			$action = trim($params['sbt_login']); // current action $controller = $this->getRequest()->getControllerName(); $action = $this->getRequest()->getActionName();		

			//echo $this->validate_alphanumeric_space($firstname);
			/*
			 * Validation for firstname(Alpha numeric), lastname(Alpha Numeric), useremail(Email), phonenumber (Numeric)
			 * 				  username(Email), role(Numeric), action(alpha)
			 */
			
			
			$error = 0;
            
			if($useremail == '') {				//Validation for user email
            	$this->error->error_user_email = Error_login_user_email_empty;
            	$error = 1;
            	//return false;
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
          		
				//$password = make_password(8);
				//$password1=hash('sha256',$password);
				
				$outpt = $this->LoginUserdb($useremail, $password);
				$outpt = $outpt[0];
				$result = explode('#', $outpt['toutput']);
				
				if($result[0] == 1) {
					$this->session->success = Success_user_creation . ' with Login ID ' . $gender ;
					return true;
				} else {
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