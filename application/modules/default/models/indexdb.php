<?php
/*
*================AllySoft Internal Use only========================

*-----------------------------------------------------------------*
* Copy Right Header Information*
*-----------------------------------------------------------------*
* Project	:	GetLinc
* File		:	indexdb.php 
* Module	:	Product Management Module
* Owner		:	RAM's 
* Purpose	:	This class is used for product management related database operations
* Date		:	08/05/2012


* Modification History
* ----------------------------------------------------------------------------------------------------------------
* Dated		Version		Who		Description
* -----------------------------------------------------------------------------------------------------------------
* %D%		%T%			%W%
* -----------------------------------------------------------------------------------------------------------------

*===================================================================================================================
*/

class Default_Model_Indexdb {
	
	public $session;
	private $error;
	public $db;
	public $viewobj;
	
	
	/**
     * Purpose: Constructor sets sessions for portal and portalerror
     * Access is limited to class and extended classes
     *
     * @param   
     * @return  
     */
	
		
	public function __construct(){
		
		//Assigning session
		$this->session = new Zend_Session_Namespace('MyClientPortal');
		$this->error = new Zend_Session_Namespace('MyClientPortalerror');		
		
		// DB Connection
		$this->db=Zend_Registry::get('db');
	}
	
	

	
	/**
     * Purpose: Fetching product list
     * Access is limited to class and extended classes
     *
     * @param	int		$istart Start value
     * @param	int		$ilimit Limit value for fetching result set
     * @param	varchar	$cond Search condition
     * @return  object	Returns status message.	
     */
	
	public function getProductsList($params){
		try {	
			if(trim($params['start'])==''){ $start = 0; }else{ $start = $params['start'];}
			if(trim($params['limit'])==''){ $limit = 8; }else{ $limit = $params['limit'];}
			if(trim($params['orderby'])==''){ $orderby = 'sp.product_id'; }else{ $limit = $params['orderby'];}
			if(trim($params['ordertype'])==''){ $ordertype = 'DESC'; }else{ $limit = $params['ordertype'];}
			
			$query = "select
					sp.product_id, sp.show_in_homepage,sp.attributes_group_id, sp.merchant_id, sp.product_sku, sp.product_title, sp.product_small_description,
					(SELECT product_image FROM store_products_images spi
					where spi.product_thumbnail=1 AND spi.statusid=1 AND spi.product_id=sp.product_id) AS product_image,
					
					(select spp.product_discount
					from store_products_price spp where spp.product_id=sp.product_id ORDER BY product_price ASC LIMIT 0,1) AS product_discount_details,
					
					(select spp.product_price
					from store_products_price spp where spp.product_id=sp.product_id ORDER BY product_price ASC LIMIT 0,1) AS product_price,
					
					
					(select if(spp.product_discount_type='Amount' , (spp.product_price-spp.product_discount), (spp.product_price-(spp.product_price*product_discount)/100))
					from store_products_price spp where spp.product_id=sp.product_id ORDER BY product_price ASC LIMIT 0,1) AS product_price_details

					from store_products sp
					
					where
					sp.statusid=1 
					
					
					ORDER BY ".$orderby." ".$ordertype." LIMIT ".$start.", ".$limit."";
					
					// AND spc.category_id = ".$params['id']." AND sp.statusid=1
			
			//exit;			
			$stmt = $this->db->query($query);			
			return $stmt->fetchAll();
			
		} catch(Exception $e) {
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	
}
?>