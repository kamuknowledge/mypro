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

class VideosController extends Zend_Controller_Action { 
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
		$this->videos = new Default_Model_Videos();
		$this->Videosdb = new Default_Model_Videosdb();
		
		/* Check Login */
		if(!$this->videos->check_login()){ $this->_redirect('/');exit;}
		
		
		// Setting Layout
        $this->_helper->layout->setLayout('default/layout');
		
		// Disable Layout
		//$this->setLayoutAction('store/layout');

		// Calling config registry values
		$this->config = Zend_Registry::get('config');	

		// Including JS
		$this->view->headScript()->appendFile($this->view->baseUrl('public/default/js/dev_videos.js'),'text/javascript');

		// Including CSS
		$this->view->headLink()->setStylesheet($this->view->baseUrl('public/default/css/dev_videos.css'));		
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
	
	public function categoriesAction() {
		try{	
		
			$userid     = $this->session->userid; 
			$this->view->title = ":: Video Categories";
			$CategoriesList = $this->Videosdb->getCategoriesList();		
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
			$CatList = $this->Videosdb->getCategoriesList($searchWord);		
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
			/*$video_cat_id = $this->_getParam('cat_id');       
          	$userid     = $this->session->userid; // Get login userid
			//print_r($params);
			$CategoriesList = $this->Videosdb->getCategoriesList();	//Get all active category list
			$this->view->CategoriesList = $CategoriesList; 			//Pass to view file  all active category list
			$video_list	=	$this->Videosdb->getVideosByCatId($userid,$video_cat_id);//Pass the login userid for get category user videos
			//echo "<pre>";print_r($video_list);exit;
			$this->view->video_list	=	$video_list;	// Pass the data to iew file.
			*/
			$userid     = $this->session->userid;
			$params = $this->_getAllParams();
			//$video_cat_id = $params['cat_id'];
			$params['limit'] = 2;
			$this->view->limit = $params['limit'];
			$params['orderby'] = 'file_title';
			$params['ordertype'] = 'ASC';
			
			if(!isset($params['start'])){$params['start'] = '0';}
			if(isset($params['start'])){$this->view->start = $params['start'];}
			
			$this->view->cat_id = $params['cat_id'];
			$video_list	=	$this->Videosdb->getVideosByCatId($userid,$params);//Pass the login userid for get category user videos
			//echo "<pre>";print_r($video_list);exit;
			$this->view->video_list	=	$video_list;
			$CategoriesList = $this->Videosdb->getCategoriesList();	//Get all active category list
			$this->view->CategoriesList = $CategoriesList; 			//Pass to view file  all active category list
			
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
	
	public function listajaxAction() {
		try{	
			/*$video_cat_id = $this->_getParam('cat_id');       
          	$userid     = $this->session->userid; // Get login userid
			//print_r($params);
			$CategoriesList = $this->Videosdb->getCategoriesList();	//Get all active category list
			$this->view->CategoriesList = $CategoriesList; 			//Pass to view file  all active category list
			$video_list	=	$this->Videosdb->getVideosByCatId($userid,$video_cat_id);//Pass the login userid for get category user videos
			//echo "<pre>";print_r($video_list);exit;
			$this->view->video_list	=	$video_list;	// Pass the data to iew file.
			*/
			$this->_helper->layout->disableLayout(); // Set default layout		
			$userid     = $this->session->userid;
			$params = $this->_getAllParams();
			//$video_cat_id = $params['cat_id'];
			$params['limit'] = 2;
			$this->view->limit = $params['limit'];
			$params['orderby'] = 'file_title';
			$params['ordertype'] = 'ASC';
			
			if(isset($params['start'])){$params['start'] = $params['start']+$params['limit'];}
			if(isset($params['start'])){$this->view->start = $params['start'];}
			
			$this->view->cat_id = $params['cat_id'];
			$video_list	=	$this->Videosdb->getVideosByCatId($userid,$params);//Pass the login userid for get category user videos
			//echo "<pre>";print_r($video_list);exit;
			$this->view->video_list	=	$video_list;
			$CategoriesList = $this->Videosdb->getCategoriesList();	//Get all active category list
			$this->view->CategoriesList = $CategoriesList; 			//Pass to view file  all active category list
			
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
	
	public function searchvideoAction() {
		try{
			$this->_helper->layout->disableLayout(); // Set default layout		
			//$video_cat_id = $this->_getParam('cat_id');       
			$searchWord = $this->_getParam('search_word')?$this->_getParam('search_word'):'';    
          	$userid     = $this->session->userid; // Get login userid
			
			$params = $this->_getAllParams();
			//$video_cat_id = $params['cat_id'];
			$params['limit'] = 2;
			$this->view->limit = $params['limit'];
			$params['orderby'] = 'file_title';
			$params['ordertype'] = 'ASC';
			
			if(isset($params['start'])){$params['start'] = 0;}
			if(isset($params['start'])){$this->view->start = $params['start'];}
			
			$this->view->cat_id = $params['cat_id'];
			$video_list	=	$this->Videosdb->getVideosByCatId($userid,$params,$searchWord);//Pass the login userid for get category user videos
			//echo "<pre>";print_r($video_list);exit;
			$this->view->searchvideo	=	$video_list;	// Pass the data to iew file.
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
						$LogoSet = $this->Videosdb->insertVideocategory($params['cat_name'],$filename, $this->session->userid);
					}
				}
		$this->_redirect('videos/categories');

			  
			// code
			//echo "test";exit;
			
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
	
	public function addnewvideoAction() {
		try{	
			$this->_helper->layout->disableLayout();
			$params = $this->_getAllParams(); 
			//echo "<pre>";print_r($params);//exit;			
			$LogoSet = $this->Videosdb->insertVideo($params['video_category'],$params['video_name'],$params['video_path'], $this->session->userid);
			$this->_redirect('videos/list/cat_id/'.$params['video_category']);
		}catch (Exception $e){
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	/**
     * Purpose: delete video action
     * Access is public
     * @param	intiger fileid
     * @return  
     */
	
	public function deletevideoAction() {
		try{
			$this->_helper->layout->disableLayout(); // Set default layout		
			$cat_id = $this->_getParam('cat_id');       
			$file_id = $this->_getParam('file_id');       
			$video_list	=	$this->Videosdb->deleteVideos($file_id);
			//echo "<pre>";print_r($video_list);exit;
			$this->_redirect('videos/list/cat_id/'.$cat_id);
		}catch (Exception $e){
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	/**
     * Purpose: Edit videos
     * Access 	Public
     * @param	intiger video_id
     * @return  array
     */
	
	public function editvideoAction() { 
		try{	
			$this->_helper->layout->disableLayout();
			$videoId = $this->_getParam('video_id'); 
			$video_data	=	$this->Videosdb->getVideosByVideoId($this->session->userid,$videoId);
			//echo $video_data['file_title'];exit;
			//echo "<pre>";print_r($video_data);exit;
			$this->view->video_data	=	$video_data;	// Pass the data to iew file.
			//$this->view->video_data	=	$video_data;	// Pass the data to iew file.
			exit;
			//$this->_redirect('videos/categories');
			// code
			//echo "test";exit;
			
		}catch (Exception $e){
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
}
?>