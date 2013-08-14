<?php
/*
*================AllySoft Internal Use only========================

*-----------------------------------------------------------------*
* Copy Right Header Information*
*-----------------------------------------------------------------*
* Project	:	GetLinc
* File		:	IndexController.php 
* Module	:	Default Module
* Owner		:	RAM's 
* Purpose	:	This class is used for common user operations for all user types
* Date		:	08/05/2012


* Modification History
* ----------------------------------------------------------------------------------------------------------------
* Dated		Version		Who		Description
* -----------------------------------------------------------------------------------------------------------------
* %D%		%T%			%W%
* -----------------------------------------------------------------------------------------------------------------

*===================================================================================================================
*/

class IndexController extends Zend_Controller_Action { 
	public $session;	// used for managing session with NAMESPACE portal
	public $error;		// used for managing session with NAMESPACE portalerror
	private $users;		// used for creating an instance of model, Access is with in the class	

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
	
		// Calling DB Operations and Validations Classes
		$this->index = new Default_Model_index();
		$this->indexdb = new Default_Model_indexdb();
		
		
        $this->_helper->layout->setLayout('default/layout');				
	}
	
    
	/**
     * Purpose: Index action shows user login screen
     * Access is public
     *
     * @param	
     * @return  
     */
	
	public function indexAction() {
		try{			
			
			$ProductsList = $this->indexdb->getProductsList($params);
			//print_r($ProductsList);
			
			$this->view->ProductsList = $ProductsList;
			
		}catch (Exception $e){
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	
	
	
	/**
     * Purpose: Index action shows user login screen
     * Access is public
     *
     * @param	
     * @return  
     */
	
	public function homebannerAction() {
		try{			
			
			
			
		}catch (Exception $e){
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}

}
?>