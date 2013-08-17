<?php
/*
*================AllySoft Internal Use only========================

*-----------------------------------------------------------------*
* Copy Right Header Information*
*-----------------------------------------------------------------*
* Project	:	GetLinc
* File		:	BrandsController.php 
* Module	:	Default Module
* Owner		:	RAM's 
* Purpose	:	This class is used for common Brands operations for all user types
* Date		:	08/05/2012


* Modification History
* ----------------------------------------------------------------------------------------------------------------
* Dated		Version		Who		Description
* -----------------------------------------------------------------------------------------------------------------
* %D%		%T%			%W%
* -----------------------------------------------------------------------------------------------------------------

*===================================================================================================================
*/

class BrandsController extends Zend_Controller_Action { 
	public $session;	// used for managing session with NAMESPACE portal
	public $error;		// used for managing session with NAMESPACE portalerror
	private $brands;			// used for creating an instance of model, Access is with in the class
	private $brandsdb;	// used for creating an instance of model, Access is with in the class	

	/**
     * Purpose: Initiates sessions with Namespace 'portal' and 'portalerror' 
     * 			and creates an instance of the model class 'Application_Model_Users'
     *
     * Access is public
     *
     * @param	
     * @return  
     */
	
	public function init() { 
		/*echo "store/index/init";
		exit;  */  

		$this->brands = new Default_Model_Brands();
		$this->brandsdb = new Default_Model_Brandsdb();
		
		$this->_helper->layout->setLayout('default/layout');
	}
	
    
	/**
     * Purpose: Index action
     * Access is public
     *
     * @param	
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
     * Purpose: Index action
     * Access is public
     *
     * @param	
     * @return  
     */
	
	public function listAction() {
		try{			
			
			//echo "hi";exit;
		}catch (Exception $e){
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	

	
}
?>