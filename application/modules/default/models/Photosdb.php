<?php
/*
*================AllySoft Internal Use only========================

*-----------------------------------------------------------------*
* Copy Right Header Information*
*-----------------------------------------------------------------*
* Project	:	GetLinc
* File		:	Videosdb.php 
* Module	:	Internal Messaging Management Module
* Owner		:	RAM's 
* Purpose	:	This class is used for Internal Messaging management related database operations
* Date		:	08/05/2012


* Modification History
* ----------------------------------------------------------------------------------------------------------------
* Dated		Version		Who		Description
* -----------------------------------------------------------------------------------------------------------------
* %D%		%T%			%W%
* -----------------------------------------------------------------------------------------------------------------

*===================================================================================================================
*/

class Default_Model_Photosdb {
	
	public $session;
	private $error;
	public $viewobj;
	
	
	/**
     * Purpose: Constructor sets sessions for MyClientPortal and MyClientPortalerror
     * Access is limited to class and extended classes
     * @param   
     * @return  
     */
	
		
	public function __construct(){
		
		//Assigning session
		$this->session = new Zend_Session_Namespace('MyClientPortal');
		$this->error = new Zend_Session_Namespace('MyClientPortalerror');
		
		// DB Connection
		$this->db=Zend_Registry::get('db');
		
		// Calling config registry values
		$this->config = Zend_Registry::get('config');
	}
	
	

	
	/**
     * Purpose: Fetching list
     * Access is limited to class and extended classes
     *
     * @param	varchar	$cond Search condition
     * @return  object	Returns status message.	
     */
	
	public function getCategoriesList($searchWord=''){
		try {	
			// Example Method List

			//$query = "SELECT * FROM social_album";			
			//exit;			
			//$stmt = $this->db->query($query);			
			//return $stmt->fetchAll();
			$select= $this->db->select();
			if($searchWord !=''){
				//$select->where('album_description=?',trim($searchWord));
				 $select->where('album_description LIKE  "%' . trim($searchWord) . '%"');
            }
			$select->where('album_type_id =?',2);
			$select->from(array('c' => 'social_album') );
			//echo $select;
			$stmt = $this->db->query($select);			
			return $stmt->fetchAll();
		
		} catch(Exception $e) {
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	
	/**
     * Purpose: Fetching list
     * Access is limited to class and extended classes
     *
     * @param	varchar	$cond Search condition
     * @return  object	Returns status message.	
     */
	
	public function getVideosByCatId($user_id){
		try {	
			// Example Method List

		$query = "SELECT * FROM social_album_files saf JOIN social_album sa ON saf.album_id = sa.album_id WHERE saf.userid='".$user_id."'";			
			//exit;			
			$stmt 	= $this->db->query($query);			
			return $stmt->fetchAll();
		
		} catch(Exception $e) {
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	/**
     * Purpose: Fetching list
     * Access is limited to class and extended classes
     *
     * @param	varchar	$cond Search condition
     * @return  object	Returns status message.	
     */
	
	public function insertVideocategory($cat_name,$filename,$userid){
		try {	
			// Example Method List

		$query = "INSERT INTO social_album 
					(album_type_id,album_image,album_description,access_specifiers,createddatetime,statusid,createdby) 
					values(2,'".$filename."','".$cat_name."','public','".date('Y-m-d H:i:s')."',1,'".$userid."')";			
			//exit;			
			$stmt 	= $this->db->query($query);			
			//return $stmt->fetchAll();
		
		} catch(Exception $e) {
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
}
?>