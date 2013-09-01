<?php
/*
*================AllySoft Internal Use only========================

*-----------------------------------------------------------------*
* Copy Right Header Information*
*-----------------------------------------------------------------*
* Project	:	GetLinc
* File		:	Eventsdb.php 
* Module	:	Calendar Module
* Owner		:	Alok Pandey
* Purpose	:	This class is used for calendar and event related database operations
* Date		:	30/07/2013


* Modification History
* ----------------------------------------------------------------------------------------------------------------
* Dated		Version		Who		Description
* -----------------------------------------------------------------------------------------------------------------
* %D%		%T%			%W%
* -----------------------------------------------------------------------------------------------------------------

*===================================================================================================================
*/

class Default_Model_Eventsdb extends Application_Model_DataBaseOperations {
	
	public $session;
	private $error;
	public $viewobj;
	public $db;
	
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
	 * insert events into social_events table
     * @access is public
	 * @author Alok Pandey.
	 * @copyright GetLinc.com, Inc. 
	 * @license GetLinc.com, Inc.
	*/
	
	public function insertEvent($event_name, $event_venue, $event_address, $event_type, $start_date, $end_date, $allday, $event_description){
		try {
		 $uid = $this->session->userid;
		/*$data = array(
				'event_title'=>$event_name,
				'userid'=>$this->session->userid,
				'event_startdate'=>$start_date,
				'event_enddate'=>$end_date,
				'event_enddate'=>$end_date,
				'event_all_day'=>$allday,
				'event_location'=>$event_venue,
				'event_address'=>$event_address,
				'event_details'=>$event_description,
				'event_type'=>$event_type,
				'createddatetime'=>date("Y-m-d H:i:s"),
			);
			$result = $this->db->insert('social_events', $data);*/
			$res = $this->db->query("INSERT INTO `social_events` (userid, event_title, event_startdate,event_enddate,event_all_day,event_location,event_address,event_details,event_type,statusid,createddatetime) VALUES ('$uid', '$event_name', '$start_date','$end_date','$allday','$event_venue','$event_address','$event_description','$event_type',1,'".date("Y-m-d H:i:s")."')");
			return ($res)?1:0;
		} catch(Exception $e) {
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	/** 
	 * update events into social_events table
     * @access is public
	 * @author Alok Pandey.
	 * @copyright GetLinc.com, Inc. 
	 * @license GetLinc.com, Inc.
	*/
	
	public function updateEvent($event_name, $event_venue, $event_address, $event_type, $start_date, $end_date, $allday, $event_description,$update_id){
		try {
		 $uid = $this->session->userid;
		/*$data = array(
				'event_title'=>$event_name,
				'userid'=>$this->session->userid,
				'event_startdate'=>$start_date,
				'event_enddate'=>$end_date,
				'event_enddate'=>$end_date,
				'event_all_day'=>$allday,
				'event_location'=>$event_venue,
				'event_address'=>$event_address,
				'event_details'=>$event_description,
				'event_type'=>$event_type,
				'createddatetime'=>date("Y-m-d H:i:s"),
			);
			$result = $this->db->insert('social_events', $data);*/  
			$res = $this->db->query("update `social_events` set event_title='".$event_name."', event_startdate='".$start_date."',event_enddate='".$end_date."',event_all_day='".$allday."',event_location='".$event_venue."',event_address='".$event_address."',event_details='".$event_description."',event_type='".$event_type."',lastupdatedby='".date("Y-m-d H:i:s")."' where event_id='".$update_id."' and userid='".$uid."'");
			return ($res)?1:0;
		} catch(Exception $e) {
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	
	/** 
	 * update events into social_events table
     * @access is public
	 * @author Alok Pandey.
	 * @copyright GetLinc.com, Inc. 
	 * @license GetLinc.com, Inc.
	*/
	
	public function updateEventDrop($start_date, $end_date, $allday, $update_id){
		try {
		 //$uid = 86;//$this->session->userid;
		 $uid = $this->session->userid;
		/*$data = array(
				'event_title'=>$event_name,
				'userid'=>$this->session->userid,
				'event_startdate'=>$start_date,
				'event_enddate'=>$end_date,
				'event_enddate'=>$end_date,
				'event_all_day'=>$allday,
				'event_location'=>$event_venue,
				'event_address'=>$event_address,
				'event_details'=>$event_description,
				'event_type'=>$event_type,
				'createddatetime'=>date("Y-m-d H:i:s"),
			);
			$result = $this->db->insert('social_events', $data);*/  
			$res = $this->db->query("update `social_events` set event_startdate='".$start_date."',event_enddate='".$end_date."',event_all_day='".$allday."',lastupdatedby='".date("Y-m-d H:i:s")."' where event_id='".$update_id."' and userid='".$uid."'");
			return ($res)?1:0;
		} catch(Exception $e) {
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	/** 
	 * update events into social_events table
     * @access is public
	 * @author Alok Pandey.
	 * @copyright GetLinc.com, Inc. 
	 * @license GetLinc.com, Inc.
	*/
	
	public function updateEventResize($end_date, $update_id){
		try {
		//$uid = 86;//$this->session->userid;
		 $uid = $this->session->userid;
		/*$data = array(
				'event_title'=>$event_name,
				'userid'=>$this->session->userid,
				'event_startdate'=>$start_date,
				'event_enddate'=>$end_date,
				'event_enddate'=>$end_date,
				'event_all_day'=>$allday,
				'event_location'=>$event_venue,
				'event_address'=>$event_address,
				'event_details'=>$event_description,
				'event_type'=>$event_type,
				'createddatetime'=>date("Y-m-d H:i:s"),
			);
			$result = $this->db->insert('social_events', $data);*/  
			$res = $this->db->query("update `social_events` set event_enddate='".$end_date."',lastupdatedby='".date("Y-m-d H:i:s")."' where event_id='".$update_id."' and userid='".$uid."'");
			return ($res)?1:0;
		} catch(Exception $e) {
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	public function deleteEvent($update_id){
		try {
		//$uid = 86;//$this->session->userid;
			$uid = $this->session->userid; 
			$sql = "update `social_events` set statusid='3' where event_id='".$update_id."' and userid='".$uid."'";
			$res = $this->db->query("update `social_events` set statusid='3' where event_id='".$update_id."' and userid='".$uid."'");
			return ($res)?1:0;
		} catch(Exception $e) {
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	
	 /** 
	 * get all active events
     * @access is public
	 * @author Alok Pandey.
	 * @copyright GetLinc.com, Inc. 
	 * @license GetLinc.com, Inc.
	*/
	
	public function getEvents($start_date,$end_date) {
		try {	
			///parent::SetDatabaseConnection();
			$query = "SELECT * FROM social_events WHERE userid = '".$this->session->userid."' AND statusid = 1 AND event_startdate>='".$start_date."' AND event_enddate<='".$end_date."';";
			//echo $query;die;
			$stmt = $this->db->query($query);			
			return $stmt->fetchAll();
		} catch(Exception $e) {
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	/** 
	 * get event by event id
     * @access is public
	 * @author Alok Pandey.
	 * @copyright GetLinc.com, Inc. 
	 * @license GetLinc.com, Inc.
	*/
	
	public function getEvent($eventId) {
		try {	
			///parent::SetDatabaseConnection();
			$query = "SELECT * FROM social_events WHERE userid = '".$this->session->userid."' AND event_id='".$eventId."'";
			//echo $query;die;
			$stmt = $this->db->query($query);			
			return $stmt->fetchAll();
		} catch(Exception $e) {
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	

}
?>