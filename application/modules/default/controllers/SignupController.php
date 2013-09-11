<?php
/*
*================AllySoft Internal Use only========================

*-----------------------------------------------------------------*
* Copy Right Header Information*
*-----------------------------------------------------------------*
* Project	:	GetLinc
* File		:	SignupController.php 
* Module	:	Default Module
* Owner		:	RAM's 
* Purpose	:	This class is used for common Profile operations for all user types
* Date		:	02/07/2012


* Modification History
* ----------------------------------------------------------------------------------------------------------------
* Dated		Version		Who		Description
* -----------------------------------------------------------------------------------------------------------------
* %D%		%T%			%W%
* -----------------------------------------------------------------------------------------------------------------

*===================================================================================================================
*/

class SignupController extends Zend_Controller_Action { 
	public $session;	// used for managing session with NAMESPACE portal
	public $error;		// used for managing session with NAMESPACE portalerror
	private $signup;			// used for creating an instance of model, Access is with in the class
	private $signupdb;			// used for creating an instance of model, Access is with in the class

	/**
     * Purpose: Initiates sessions with Namespace 'portal' and 'portalerror' 
     * 			and creates an instance of the model class 'Application_Model_Users'
     *
     * Access is public
     *
     * @param	
     * 
     * @return  
     */
	
	public function init() { 
		/*echo "store/index/init";
		exit;  */
		
		//Assigning session
		$this->session = new Zend_Session_Namespace('MyClientPortal');
				
		$this->signup = new Default_Model_Signup();
        $this->_helper->layout->setLayout('default/layout');
		
		
		$this->view->headLink()->setStylesheet($this->view->baseUrl('public/default/css/dev_profile.css'));
		
		$this->view->headScript()->appendFile($this->view->baseUrl('public/default').'/js/dev_profile.js','text/javascript');
		//$this->setLayoutAction('store/layout');		
	}
	
    
	/**
     * Purpose: Index action
     *
     * Access is public
     *
     * @param	
     * 
     * @return  
     */
	
	public function indexAction() {
		try{			
			//echo "store/index/index";
			//exit; 
		}catch (Exception $e){
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	

	
	
	/**
     * Purpose: User registration page 
     *
     * Access is public
     *
     * @param	
     * 
     * @return  
     */
	
	public function registerAction() {
		try{
			$this->view->title = "Create User";
			$params = $this->_getAllParams();	
			$this->_helper->layout->setLayout('default/empty_layout');
			$request = $this->getRequest();
			$Request_Values = $request->getPost();
			if ($request->isPost()) {
				if(!$this->signup -> createUser($params)) {
					echo "Registration Failed! Try Again.";
				} else {
					$toemail = $params['email_id'];
					$mailbody = "Thanks for registration with Getlinc. We will keep you update.";
					$emailsubject = "Registered with Getlinc.";
					Application_Model_DataBaseOperations::insertEmailQueue($toemail,$mailbody,$emailsubject);
					echo $this->session->success;
					echo "Successfully Registered. Please login with your account.";
					exit;
				}
			}else{
				echo "Data not received from Origin Place.";exit;
			}
			
		} catch(Exception $e) {
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
			exit;
		}
	}
	/**
     * Purpose: User forgot page 
     *
     * Access is public
     *
     * @param	
     * 
     * @return  
     */
	public function forgotAction() {
		try{
			$this->view->title = "Forgot Password";
			$params = $this->_getAllParams();	
			$this->_helper->layout->setLayout('default/empty_layout');
			$request = $this->getRequest();
			$Request_Values = $request->getPost();
			if ($request->isPost()) {
				$params["password"] = $this->rand_string(8);
				if(!$this->signup -> forgotPassword($params)) {
					echo "Emailid not exist in our system. Please register as a new user.";
				} else {
					$toemail = $params['email_id'];
					$mailbody = "Your password has been auto generted. \n New Password: ".$params["password"];
					$emailsubject = "Registered with Getlinc.";
					Application_Model_DataBaseOperations::insertEmailQueue($toemail,$mailbody,$emailsubject);
					echo $this->session->success;
					exit;
				}
			}else{
				echo "Data not received from Origin Place.";exit;
			}
			
		} catch(Exception $e) {
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
			exit;
		}
	}
	
	function rand_string( $length ) {
		$chars = "abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ123456789";	
		$size = strlen( $chars );
		for( $i = 0; $i < $length; $i++ ) {
			$str .= $chars[ rand( 0, $size - 1 ) ];
		}
		return $str;
	}
}
?>