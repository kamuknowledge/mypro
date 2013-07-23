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

class Wall_IndexController extends Zend_Controller_Action { 
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
		/*echo "store/index/init";
		exit;  */
        $this->_helper->layout->setLayout('default/layout');
		//$this->setLayoutAction('store/layout');		
	}
	
    
	/**
     * Purpose: Index action shows user login screen
     *
     * Access is public
     *
     * @param	
     * 
     * @return  
     */
	
	public function indexAction() {
		try{			
                    // CSS Start
                   
                     $this->view->headLink()->setStylesheet($this->view->baseUrl('public/wall').'/css/facebox.css');
                     $this->view->headLink()->appendStylesheet($this->view->baseUrl('public/wall').'/css/tipsy.css');
                     $this->view->headLink()->appendStylesheet($this->view->baseUrl('public/wall').'/css/lightbox.css');
                     $this->view->headLink()->appendStylesheet($this->view->baseUrl('public/wall').'/css/wall.css');
                    // CSS End
                   
                    // javascript start
                     
                    $this->view->headScript()->appendFile($this->view->baseUrl('public/wall').'/js/jquery.wallform.js','text/javascript');
                    $this->view->headScript()->appendFile($this->view->baseUrl('public/wall').'/js/jquery.webcam.js','text/javascript');
                    $this->view->headScript()->appendFile($this->view->baseUrl('public/wall').'/js/jquery.color.js','text/javascript');
                    $this->view->headScript()->appendFile($this->view->baseUrl('public/wall').'/js/jquery.livequery.js','text/javascript');
                    $this->view->headScript()->appendFile($this->view->baseUrl('public/wall').'/js/jquery.timeago.js','text/javascript');
                    $this->view->headScript()->appendFile($this->view->baseUrl('public/wall').'/js/jquery.tipsy.js','text/javascript');
                    $this->view->headScript()->appendFile($this->view->baseUrl('public/wall').'/js/facebox.js','text/javascript');
                    $this->view->headScript()->appendFile($this->view->baseUrl('public/wall').'/js/wall.js','text/javascript');
                    
		}catch (Exception $e){
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}

}
?>