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
			$data = array(
				'event_title'=>$event_name,
				'userid'=>'',
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
			$result = $this->db->insert('social_events', $data);
			
		} catch(Exception $e) {
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	

}
?>