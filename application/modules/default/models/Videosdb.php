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

class Default_Model_Videosdb {
	
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
			$select->where('album_type_id =?',4);
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
	
	public function getVideosByCatId($user_id,$video_cat_id='',$searchWord=''){
		try {	
			// Example Method List
		$userid     = $this->session->userid; // Get login userid
		//echo $query = "SELECT * FROM social_album_files saf JOIN social_album sa ON saf.album_id = sa.album_id WHERE saf.userid='".$user_id."'";
			$select= $this->db->select();
			$select->from(array('saf' => 'social_album_files'),array('file_title','file_path','album_id','file_id','file_path','updateddatetime','createddatetime') );
			$select->joinLeft(array('sa' => 'social_album'),
                    'saf.album_id = sa.album_id',array('album_description','album_image'));
			$select->where('saf.userid =?',$user_id);
			if($video_cat_id != ''){
				$select->where('saf.album_id =?',$video_cat_id);
			}
			if($searchWord !=''){
				//$select->where('album_description=?',trim($searchWord));
				 $select->where('saf.file_title LIKE  "%' . trim($searchWord) . '%"');
            }	
			$select->order(array('saf.file_id DESC'));			
			//echo $select;
				//		exit;			
			$stmt 	= $this->db->query($select);			
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
					values(4,'".$filename."','".$cat_name."','public','".date('Y-m-d H:i:s')."',1,'".$userid."')";			
			//exit;			
			$stmt 	= $this->db->query($query);			
			//return $stmt->fetchAll();
		
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
	
	public function insertVideo($cat_id,$vname,$vpath,$userid){
		try {	
			// Example Method List

		$query = "INSERT INTO social_album_files (album_id,userid,file_title,file_path,file_access_specifiers,createddatetime,statusid,createdby) 
					values('".$cat_id."','".$userid."','".$vname."','".$vpath."','public','".date('Y-m-d H:i:s')."',1,'".$userid."')";			
			//echo $query; exit;			
			$stmt 	= $this->db->query($query);			
			//return $stmt->fetchAll();
		
		} catch(Exception $e) {
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	/**
     * Purpose: Delete video file
     * Access is limited to class and extended classes
     *
     * @param	intiger $file_id	
    */
	
	public function deleteVideos($file_id){
		try {	
		//$dbss->delete('social_album_files', array('file_id = ?' => $file_id));
		$dbss	= "DELETE FROM social_album_files WHERE file_id ='".$file_id."'";	
		//echo $dbss;exit;
		$this->db->query($dbss);
		} catch(Exception $e) {
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
}
?>