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
		$this->signup = new Application_Model_Signup();
        $this->_helper->layout->setLayout('default/layout');
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
					//$this->view->countrieslist = $this->signupdb->getCountriesList();
					//$this->_redirect('admin/merchant/register');
					
				} else {
					//return 0;
				}
			}else{			
				//$this->view->countrieslist = $this->merchantdb->getCountriesList();
				//return 0;
			}
			
		} catch(Exception $e) {
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}

}
?>