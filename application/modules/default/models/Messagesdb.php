<?php
/*
*================AllySoft Internal Use only========================

*-----------------------------------------------------------------*
* Copy Right Header Information*
*-----------------------------------------------------------------*
* Project	:	GetLinc
* File		:	Messsagesdb.php 
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

class Default_Model_Messagesdb {
	
	public $session;
	private $error;
	public $viewobj;
	public $userid;
	
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
		
		/* Check Login */
		if($this->session->userid)
		{		
			$this->userid = $this->session->userid;
		}
	}
	
	

	
	/**
     * Purpose: Fetching list
     * Access is limited to class and extended classes
     *
     * @param	varchar	$cond Search condition
     * @return  object	Returns status message.	
     */
	
	public function getInboxMessages(){
		try{
		
		$query = "SELECT m.message_id, im.message_subject, im.message_body_content, m.createddatetime, u.firstname, u.display_name, u.emailid 
						FROM social_internal_messaging_users m
						LEFT JOIN social_internal_messaging im ON m.message_id = im.message_id AND im.statusid = 1
						LEFT JOIN apmusers u ON m.userid = u.userid 
						
						WHERE m.userid = '".$this->userid."' ";
		//echo $query;
		//exit;			
		$stmt = $this->db->query($query);			
		return $stmt->fetchAll();
		
			} catch(Exception $e) {
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	public function getSentMessages(){
		try{
			
			$query = "SELECT m.message_id, im.message_subject, im.message_body_content, m.createddatetime, u.firstname, u.display_name, u.emailid 
			FROM social_internal_messaging_users m
			LEFT JOIN social_internal_messaging im ON m.message_id = im.message_id AND im.statusid = 1
			LEFT JOIN apmusers u ON m.userid = u.userid 
			
			WHERE im.userid = '".$this->userid."' ";
			//echo $query;
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