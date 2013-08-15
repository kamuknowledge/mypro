<?php
/*
*================AllySoft Internal Use only========================

*-----------------------------------------------------------------*
* Copy Right Header Information*
*-----------------------------------------------------------------*
* Project	:	GetLinc
* File		:	Videos.php 
* Module	:	Default Module
* Owner		:	RAM's 
* Purpose	:	This class is used for internal messaging operations for all user types
* Date		:	26/07/2013

* Modification History
* ----------------------------------------------------------------------------------------------------------------
* Dated		Version		Who		Description
* -----------------------------------------------------------------------------------------------------------------
* %D%		%T%			%W%
* -----------------------------------------------------------------------------------------------------------------

*===================================================================================================================
*/

class PhotosController extends Zend_Controller_Action { 
	public $session;	// used for managing session with NAMESPACE portal
	public $error;		// used for managing session with NAMESPACE portalerror
	private $message;		// used for creating an instance of model, Access is with in the class	
	private $messagesdb;
	
	
	/**
     * Purpose: Initiates sessions with Namespace 'portal' and 'portalerror' 
     * Access is public
	 
     * @param	
     * @return  
     */
	
	public function init() {
	
		//Assigning session
		$this->session = new Zend_Session_Namespace('MyClientPortal');
		$this->error = new Zend_Session_Namespace('MyPortalerror');
		
		// Calling DB Operations and Validations Classes
		$this->photos = new Default_Model_Photos();
		$this->Photosdb = new Default_Model_Photosdb();
		
		/* Check Login */
		if(!$this->photos->check_login()){ $this->_redirect('/');exit;}
		
		
		// Setting Layout
        $this->_helper->layout->setLayout('default/layout');
		
		// Disable Layout
		//$this->setLayoutAction('store/layout');

		// Calling config registry values
		$this->config = Zend_Registry::get('config');	

		// Including JS
		$this->view->headScript()->appendFile($this->view->baseUrl('public/default/js/dev_photos.js'),'text/javascript');

		// Including CSS
		$this->view->headLink()->setStylesheet($this->view->baseUrl('public/default/css/dev_photos.css'));		
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
     * Purpose: Get category list for category page
     * Access is public
     *
     * @param	
     * @return  array
     */
	
	public function photocategoriesAction() {
		try{	
		
			$userid     = $this->session->userid; 
			$this->view->title = ":: Video Categories";
			$CategoriesList = $this->Photosdb->getCategoriesList();		
			//echo "<pre>";print_r($CategoriesList);exit;
			$this->view->CategoriesList = $CategoriesList;
			
		}catch (Exception $e){
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	/**
     * Purpose: Get category list for category page
     * Access is public
     *
     * @param	
     * @return  array
     */
	
	public function searchcategoriesAction() {
		try{	
			$this->_helper->layout->disableLayout(); // Set default layout
			//$params = $this->_getAllParams();
			$searchWord = $this->_getParam('search_word');
			
			$userid     = $this->session->userid; 
			$this->view->title = ":: Video Categories";
			//exit;
			$CatList = $this->Photosdb->getCategoriesList($searchWord);		
			//echo "<pre>";print_r($CatList);exit;
			$this->view->searchcategories = $CatList;
			
		}catch (Exception $e){
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	/**
     * Purpose: Add category action
     * Access is public
     * @param	
     * @return  
     */
	
	public function listAction() {
		try{	
			// code
			//echo "test";exit;
			//echo "my cat id".$this->cat_id;
			$request = $this->getRequest();
              $dataGet  = $this->getRequest()->getParam('id',null);       
          

			
			$userid     = $this->session->userid; // Get login userid
			$params = $this->_getAllParams();
			//print_r($params);
			$CategoriesList = $this->Photosdb->getCategoriesList();	//Get all active category list
			$this->view->CategoriesList = $CategoriesList; 			//Pass to view file  all active category list
			$video_list	=	$this->Photosdb->getVideosByCatId($userid);//Pass the login userid for get category user videos
			//echo "<pre>";print_r($video_list);exit;
			$this->view->video_list	=	$video_list;	// Pass the data to iew file.
		}catch (Exception $e){
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	
	
	
	/**
     * Purpose: Add category action
     * Access is public
     * @param	
     * @return  
     */
	
	public function addcategoryAction() {
		try{	
			$this->_helper->layout->disableLayout();
			$params = $this->_getAllParams(); 
//echo "<pre>";print_r($params);exit;			
			$upload = new Zend_File_Transfer();                                    
				
				//$upload = new Zend_File_Transfer_Adapter_Http();                                
				$files = $upload->getFileInfo();
				foreach ($files as $file => $info) {
					if($upload->isValid($file)){
						$filename = time().$info['name'];
						$upload->addFilter('Rename', APPLICATION_PATH.'/../public/uploads/video_category_images/'.$filename, 1);
						$upload->receive($file);
						$LogoSet = $this->Photosdb->insertVideocategory($params['cat_name'],$filename, $this->session->userid);
					}
				}
		$this->_redirect('photos/photocategories');

			  
			// code
			//echo "test";exit;
			
		}catch (Exception $e){
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	

}
?>