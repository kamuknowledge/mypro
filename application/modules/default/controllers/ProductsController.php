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
	public $billingaddress;
	private $productss;		// used for creating an instance of model, Access is with in the class	
	private $productssdb;
	
	/**
     * Purpose: Initiates sessions with Namespace 'MyClientPortal' and 'MyClientPortalerror' 
     * Access is public
     *
     * @param	
     * @return  
     */
	
	public function init() { 
		
		//Assigning session
		$this->session = new Zend_Session_Namespace('MyClientPortal');
		$this->error = new Zend_Session_Namespace('MyClientPortalerror');
		$this->billingaddress = new Zend_Session_Namespace('MyClientPortalBillingAddress');
		
		// Calling DB Operations and Validations Classes
		$this->productss = new Default_Model_Productss();
		$this->productssdb = new Default_Model_Productssdb();
		
		
		// Calling config registry values
		$this->config = Zend_Registry::get('config');
				
		// Setting Layout
        $this->_helper->layout->setLayout('default/layout');
		
		// Disable Layout
		//$this->setLayoutAction('default/layout');

		// Including JS
		$this->view->headScript()->appendFile($this->view->baseUrl('public/default/js/dev_store.js'),'text/javascript');

		// Including CSS
		$this->view->headLink()->setStylesheet($this->view->baseUrl('public/default/css/dev_store.css'));
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
			$this->_redirect('default/products/list');
			exit;			
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
			$this->view->headTitle()->append('Products List');		
			
			$params = $this->_getAllParams();
			$params['limit'] = 4;
			$this->view->limit = $params['limit'];
			$params['orderby'] = 'product_title';
			$params['ordertype'] = 'ASC';
			
			if(!isset($params['start'])){$params['start'] = '0';}
			if(isset($params['start'])){$this->view->start = $params['start'];}
			
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
     * Access is public
     *
     * @param	
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
     * Access is public
     *
     * @param	
     * @return  
     */
	
	public function viewAction() {
		try{
		
		
			$request = $this->getRequest();
			$Request_Values = $request->getPost();	
			//print_r($Request_Values);
			
			//if ($request->isPost() && isset($Request_Values['product_update_submit']) && $Request_Values['product_update_submit']=='Save')
			if ($request->isPost())
			{
				$product_price_id = $Request_Values['product_price_id'];
				$product_id = $Request_Values['product_id'];
				$action = '';
				$this->productss->insertViewCartProduct($product_id,$product_price_id,$action);
				$this->_redirect('/products/viewcart');
				exit;				
			}else{
		
				$params = $this->_getAllParams();
				$this->view->product_id = $params['id'];
				//Print_r($params);			
				$productdetails = $this->productssdb->getProductDetails($params);			
				$this->view->headTitle()->append($productdetails['product_title']);
				$this->view->productdetails = $productdetails;
				
				$ProductImageDetails = $this->productssdb->getProductImageDetails($params);
				$this->view->ProductImageDetails = $ProductImageDetails;
				
				$ProductReviewDetails = $this->productssdb->getProductReviewDetails($params);
				$this->view->ProductReviewDetails = $ProductReviewDetails;
				
				$ProductAttributesDetails = $this->productssdb->getProductAttributesDetails($params);
				$this->view->ProductAttributesDetails = $ProductAttributesDetails;
				
				$ProductPriceDetails = $this->productssdb->getProductPriceDetails($params);
				$this->view->ProductPriceDetails = $ProductPriceDetails;
			}
			
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
	
	public function viewcartAction() {
	try{			
			$ViewCartProductDetails = $this->productssdb->getViewCartProductDetails();
			$this->view->ViewCartProductDetails = $ViewCartProductDetails;			
			
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
	
	public function removeiteamAction() {
	try{			
			$this->_helper->layout->disableLayout();
			
			$params = $this->_getAllParams();
			$temp_cart_id = $params['temp_cart_id'];
			$removed = $this->productssdb->removeIteam($temp_cart_id);
			
			$ViewCartProductDetails = $this->productssdb->getViewCartProductDetails();
			$this->view->ViewCartProductDetails = $ViewCartProductDetails;
			
			
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
	
	public function viewcartupdateAction() {
	try{			
			$this->_helper->layout->disableLayout();
			$params = $this->_getAllParams();
			//print_r($params);exit;
			
			$temp_cart_id = $params['temp_cart_id'];
			$product_qty = $params['product_qty'];
			$updated = $this->productssdb->updateIteamQty($temp_cart_id,$product_qty);
			
			$ViewCartProductDetails = $this->productssdb->getViewCartProductDetails();
			//print_r($ViewCartProductDetails);exit;
			$this->view->ViewCartProductDetails = $ViewCartProductDetails;
			
			
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
	
	public function placeorderAction() {
	try{			
			$this->view->title = "Place Order";
			
			$params = $this->_getAllParams();
			$request = $this->getRequest();
			$Request_Values = $request->getPost();
			if ($request->isPost()) {
				
				$this->billingaddress->billing_first_name 		= $params['billing_first_name'];
				$this->billingaddress->billing_last_name 		= $params['billing_last_name'];
				$this->billingaddress->billing_email_address 	= $params['billing_email_address'];
				$this->billingaddress->billing_street_address 	= $params['billing_street_address'];
				$this->billingaddress->billing_landmark 		= $params['billing_landmark'];
				$this->billingaddress->billing_address_lane_2 	= $params['billing_address_lane_2'];
				$this->billingaddress->billing_city 			= $params['billing_city'];
				$this->billingaddress->billing_state 			= $params['billing_state'];
				$this->billingaddress->billing_pincode 			= $params['billing_pincode'];
				$this->billingaddress->billing_phone 			= $params['billing_phone'];
				
				$this->billingaddress->shipping_first_name 		= $params['shipping_first_name'];
				$this->billingaddress->shipping_last_name 		= $params['shipping_last_name'];
				$this->billingaddress->shipping_email_address 	= $params['shipping_email_address'];
				$this->billingaddress->shipping_street_address 	= $params['shipping_street_address'];
				$this->billingaddress->shipping_landmark 		= $params['shipping_landmark'];
				$this->billingaddress->shipping_address_lane_2 	= $params['shipping_address_lane_2'];
				$this->billingaddress->shipping_city 			= $params['shipping_city'];
				$this->billingaddress->shipping_state 			= $params['shipping_state'];
				$this->billingaddress->shipping_pincode 		= $params['shipping_pincode'];
				$this->billingaddress->shipping_phone 			= $params['shipping_phone'];
				$this->billingaddress->submit_billing_shipping 	= $params['submit_billing_shipping'];
				
				$this->_redirect('products/ordersummary/');
			}
		
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
	
	public function ordersummaryAction() {
	try{			
			$this->view->title = "Order Summary";
			
			$params = $this->_getAllParams();
			$request = $this->getRequest();
			$Request_Values = $request->getPost();
			if ($request->isPost()) {
				
				
			}
		
		}catch (Exception $e){
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	
}
?>