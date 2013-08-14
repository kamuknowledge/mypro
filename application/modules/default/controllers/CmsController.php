<?php
/*
*================AllySoft Internal Use only========================

*-----------------------------------------------------------------*
* Copy Right Header Information*
*-----------------------------------------------------------------*
* Project	:	GetLinc
* File		:	CmsController.php 
* Module	:	Default - CMS Module
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

class CmsController extends Zend_Controller_Action { 
	public $session;	// used for managing session with NAMESPACE portal
	public $error;		// used for managing session with NAMESPACE portalerror
	private $cms;		// used for creating an instance of model, Access is with in the class	
	private $cmsdb;
	
	/**
     * Purpose: Initiates sessions with Namespace 'portal' and 'portalerror' 
     * Access is public
     *
     * @param	
     * @return  
     */
	
	public function init() { 			
		$this->cms = new Default_Model_Cms();
		$this->cmsdb = new Default_Model_Cmsdb();
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
			$params = $this->_getAllParams();
			//print_r($params);
			$id=$params['id'];

			//cms/index/id/1 - about us
			//cms/index/id/2 - contact us	
			
			$content_array = $this->cmsdb->getContent($id);
			$this->view->content_array = $content_array;
			
		}catch (Exception $e){
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
}
?>