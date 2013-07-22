<?php
/*
*================AllySoft Internal Use only========================

*-----------------------------------------------------------------*
* Copy Right Header Information*
*-----------------------------------------------------------------*
* Project	:	GetLinc
* File		:	Productssdb.php 
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

class Default_Model_Productssdb extends Application_Model_DataBaseOperations {
	
	public $session;
	private $error;
	public $db;
	public $viewobj;
	
	
	/**
     * Purpose: Constructor sets sessions for portal and portalerror
     *
     * Access is limited to class and extended classes
     *
     * @param   
     * @return  
     */
	
		
	public function __construct(){
		$this->session = new Zend_Session_Namespace('MyClientPortal');
		$this->error = new Zend_Session_Namespace('MyClientPortalerror');
		$this->db=Zend_Registry::get('db');
	}
	
	

	
	/**
     * Purpose: Fetching product list
     *
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
			
			$query = "select
					sp.product_id, sp.attributes_group_id, sp.merchant_id, sp.product_sku, sp.product_title, sp.product_small_description,
					(SELECT product_image FROM store_products_images spi
					where spi.product_thumbnail=1 AND spi.statusid=1 AND spi.product_id=sp.product_id) AS product_image,

					(select if(spp.product_discount_type='Amount' , (spp.product_price-spp.product_discount), (spp.product_price-(spp.product_price*product_discount)/100))
					from store_products_price spp where spp.product_id=sp.product_id ORDER BY product_price ASC LIMIT 0,1) AS product_price_details

					from store_products sp
					LEFT JOIN store_products_categories spc ON (sp.product_id=spc.product_id)
					where
					sp.statusid=1 
					AND spc.category_id = ".$params['id']." AND sp.statusid=1
					GROUP BY sp.product_id
					ORDER BY ".$params['orderby']." ".$params['ordertype']." LIMIT ".$start.", ".$params['limit']."";
			
			//exit;			
			$stmt = $this->db->query($query);			
			return $stmt->fetchAll();
			
		} catch(Exception $e) {
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	
	
	
	
	
	/**
     * Purpose: Fetching product Details
     *
     * Access is limited to class and extended classes
     *
     * @param	int		$istart Start value
     * @param	int		$ilimit Limit value for fetching result set
     * @param	varchar	$cond Search condition
     * @return  object	Returns status message.	
     */
	
	public function getProductDetails($params){
		try {	
			
			//$query = "select * from store_products where product_id=".$params['id'];
			
			$query = "SELECT
			sp.product_id, sp.attributes_group_id, sp.merchant_id, sp.brand_id, sp.product_sku, sp.product_title,
			sp.product_small_description, sp.product_meta_title, sp.product_meta_description,
			spd.description_id, spd.product_description
			FROM store_products sp
			LEFT JOIN store_products_description spd ON (sp.product_id=spd.product_id) where sp.product_id=".$params['id'];
			
			//exit;			
			$stmt = $this->db->query($query);			
			return $stmt->fetch();
			
		} catch(Exception $e) {
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	
	
	
	
	/**
     * Purpose: Fetching product Attribute Details
     *
     * Access is limited to class and extended classes
     *
     * @param	int		$istart Start value
     * @param	int		$ilimit Limit value for fetching result set
     * @param	varchar	$cond Search condition
     * @return  object	Returns status message.	
     */
	
	public function getProductAttributesDetails($params){
		try {	
			
			//$query = "select * from store_products where product_id=".$params['id'];
			
			$query = "SELECT
						spav.product_id, spast.attributes_group_id, spast.attributes_set_id, spast.attributes_set_title,
						spa.attribute_id, spa.attribute_title, spav.attribute_value
						FROM
						store_products_attributes spa,
						store_products_attributes_values spav,
						store_products_attributes_sets spast,
						store_products_attributes_sets_mapping  spasm
						where
						spa.attribute_id = spav.attribute_id
						AND spast.attributes_set_id = spasm.attributes_set_id
						AND spa.attribute_id = spasm.attribute_id
						AND spa.statusid=1
						AND spav.statusid=1
						AND spast.statusid=1
						AND spasm.statusid=1
						AND spav.product_id = ".$params['id'];
			
			//exit;			
			$stmt = $this->db->query($query);			
			return $stmt->fetchAll();
			
		} catch(Exception $e) {
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	
	
	
	
	/**
     * Purpose: Fetching product Image Details
     *
     * Access is limited to class and extended classes
     *
     * @param	int		$istart Start value
     * @param	int		$ilimit Limit value for fetching result set
     * @param	varchar	$cond Search condition
     * @return  object	Returns status message.	
     */
	
	public function getProductImageDetails($params){
		try {
			$query = "SELECT * FROM store_products_images spi WHERE statusid=1 AND spi.product_id = ".$params['id'];			
			//exit;			
			$stmt = $this->db->query($query);			
			return $stmt->fetchAll();
			
		} catch(Exception $e) {
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	
	
	
	/**
     * Purpose: Fetching product Image Details
     *
     * Access is limited to class and extended classes
     *
     * @param	int		$istart Start value
     * @param	int		$ilimit Limit value for fetching result set
     * @param	varchar	$cond Search condition
     * @return  object	Returns status message.	
     */
	
	public function getProductReviewDetails($params){
		try {
			$query = "SELECT *
						FROM
						store_products_reviews spr LEFT JOIN apmusers au ON (spr.userid = au.userid AND au.statusid=1)
						LEFT JOIN user_images ui ON (au.userid = ui.userid AND ui.statusid=1 AND is_primary=1)
						WHERE
						spr.userid = au.userid
						AND
						spr.statusid=1
						AND product_id = ".$params['id']."
						AND spr.userid=86";			
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