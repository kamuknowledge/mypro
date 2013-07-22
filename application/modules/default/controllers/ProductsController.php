<?php
/*
*================AllySoft Internal Use only========================

*-----------------------------------------------------------------*
* Copy Right Header Information*
*-----------------------------------------------------------------*
* Project	:	GetLinc
* File		:	ProductsController.php 
* Module	:	Default - Product Module
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

class ProductsController extends Zend_Controller_Action { 
	public $session;	// used for managing session with NAMESPACE portal
	public $error;		// used for managing session with NAMESPACE portalerror
	private $productss;		// used for creating an instance of model, Access is with in the class	
	private $productssdb;
	
	/**
     * Purpose: Initiates sessions with Namespace 'portal' and 'portalerror' 
     *
     * Access is public
     *
     * @param	
     * 
     * @return  
     */
	
	public function init() { 	
		$this->productss = new Default_Model_Productss();
		$this->productssdb = new Default_Model_Productssdb();
        $this->_helper->layout->setLayout('default/layout');
		//$this->setLayoutAction('default/layout');		
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
			$this->_redirect('default/products/list');
			exit;			
		}catch (Exception $e){
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
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
	
	public function listAction() {
		try{				
			$this->view->headTitle()->append('Products List');
			//$this->view->headTitle()->prepend('.:');

			//$this->view->headScript()->appendFile($this->view->baseUrl('public/default').'/js/jquery-1.2.6.pack.js','text/javascript'); //array('conditional' => 'lt IE 7')
			$this->view->headScript()->appendFile($this->view->baseUrl('public/default').'/js/developer_products.js','text/javascript'); 
			
			/*
			$this->headScript()->appendFile('/js/prototype.js')
								->appendScript($onloadScript);
			*/
								
			
			$params = $this->_getAllParams();
			$params['limit'] = 4;
			$this->view->limit = $params['limit'];
			$params['orderby'] = 'product_title';
			$params['ordertype'] = 'ASC';
			
			if(!isset($params['start'])){$params['start'] = '0';}
			if(isset($params['start'])){$this->view->start = $params['start'];}
			
			$this->view->id = $params['id'];
			
			//print_r($params);
			//Array ( [controller] => products [action] => list [id] => 6 [module] => default ) 
			
			/*
			$request = $this->getRequest();
			$Request_Values = $request->getGet();
			print_r($Request_Values);
			*/
			
			$ProductsList = $this->productssdb->getProductsList($params);			
			$this->view->ProductsList = $ProductsList;
			//exit;			
		}catch (Exception $e){
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
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
	
	public function listajaxAction() {
		try{
			$this->_helper->layout->disableLayout();
			$params = $this->_getAllParams();
			
			$params['limit'] = 4;
			$this->view->limit = $params['limit'];
			$params['orderby'] = 'product_title';
			$params['ordertype'] = 'ASC';
			
			if(isset($params['start'])){$params['start'] = $params['start']+$params['limit'];}
			if(isset($params['start'])){$this->view->start = $params['start'];}			
			
			//print_r($params);			
			$this->view->id = $params['id'];
			
			$ProductsList = $this->productssdb->getProductsList($params);			
			$this->view->ProductsList = $ProductsList;
			//exit;			
		}catch (Exception $e){
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
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
	
	public function viewAction() {
		try{
			$params = $this->_getAllParams();
			//Print_r($params);			
			$productdetails = $this->productssdb->getProductDetails($params);			
			$this->view->headTitle()->append($productdetails['product_title']);
			$this->view->productdetails = $productdetails;
			//exit;			
		}catch (Exception $e){
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	
}
?>