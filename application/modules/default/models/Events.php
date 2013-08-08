<?php
/*
*================AllySoft Internal Use only========================

*-----------------------------------------------------------------*
* Copy Right Header Information*
*-----------------------------------------------------------------*
* Project	:	GetLinc
* File		:	Events.php 
* Module	:	Calendar Module
* Owner		:	Alok Pandey
* Purpose	:	This class is used for business logic and calendar functional operations
* Date		:	30/07/2013


* Modification History
* ----------------------------------------------------------------------------------------------------------------
* Dated		Version		Who		Description
* -----------------------------------------------------------------------------------------------------------------
* %D%		%T%			%W%
* -----------------------------------------------------------------------------------------------------------------

*===================================================================================================================
*/



//class Application_Model_Events extends Application_Model_Validation {
class Default_Model_Events extends Application_Model_Validation {
	
	public $session;
	private $error;
	private $config;
	private $redirector;
	private $requestHandler;
	public $viewobj;
	
	/**
     * Purpose: Constructor sets sessions for portal and portalerror and config and returns session objects
     *
     * Access is public
     *
     * @param	
     * @return  
     */
	
	public function __construct(){
	
		$this->Eventsdb=new Default_Model_Eventsdb();
		
		//Assigning session
		$this->session = new Zend_Session_Namespace('MyClientPortal');
                

		//Assigning a config registry
		$this->config = Zend_Registry::get('config');
		
		//Redirector handler
		$this->redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
		
		//Request Handler
		$this->requestHandler = Zend_Controller_Front::getInstance()->getRequest();
		
		
		
		//View Renderer
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
		$viewRenderer->initView();
		//Assigning renderer to access in the class
		$this->error = $viewRenderer->view;
		$this->viewobj= $viewRenderer->view;
	}
	
	/**
     * Purpose: create event procees logic
     *
     * Access is public
     *
     * @param	Array	$post array
     * 
     * @return  json array
     */
	
	public  function createEvent(Array $post) {
		try{
			/*echo "<pre>";
			print_r($post);
			exit;*/
			$data = array();
			$start_time = (isset($post['event_start_time']))?$post['event_start_time']:'00:00:00';
			$end_time = (isset($post['event_end_time']))?$post['event_end_time']:'23:30:00';
			$event_name = ($post['event_name']!="")?trim($post['event_name']):'';
			$event_venue = ($post['event_venue']!="")?trim($post['event_venue']):'';
			$event_address = ($post['event_address']!="")?trim($post['event_address']):'';
			$event_type = ($post['event_type']!="")?trim($post['event_type']):'';
			$event_start_date = ($post['event_start_date']!="")?trim($post['event_start_date']):'';
			$event_start_time = $start_time;
			$event_end_date = ($post['event_end_date']!="")?trim($post['event_end_date']):'';
			$event_end_time = $end_time;
			$allday = ($post['allday']==1)?trim($post['allday']):0;			
			$event_description = ($post['event_description']!="")?trim($post['event_description']):'';
			
			
			$error = 0;
			
            if($event_name == '') {			
            	$error = 1;
				$data = array('error'=>'Event title can not be empty.');
            	//return false;
            } 
			$start_date = date("Y-m-d",strtotime($event_start_date))." ".date("H:i:s",strtotime($event_start_time));
			$end_date = date("Y-m-d",strtotime($event_end_date))." ".date("H:i:s",strtotime($event_end_time));
			
			if(strtotime($start_date) > strtotime($end_date)) {				
				$error = 1;
				$data = array('error'=>'End date should be greater than start date.');
            	//return false;
            }
			
			if($error==0){
					$outpt = $this->Eventsdb->insertEvent($event_name, $event_venue, $event_address, $event_type, $start_date, $end_date, $allday, $event_description);	
					if($outpt){
						$data = array('success'=>'Event successfully created.');
					}
			}
			//print_r($data);die;
          	return $data;
		} catch(Exception $e) {
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	
	/**
     * Purpose: update event procees logic
     *
     * Access is public
     *
     * @param	Array	$post array
     * 
     * @return  json array
     */
	
	public  function updateEvent(Array $post) {
		try{
			/*echo "<pre>";
			print_r($post);
			exit;*/
			$data = array();
			$start_time = (isset($post['event_start_time']))?$post['event_start_time']:'00:00:00';
			$end_time = (isset($post['event_end_time']))?$post['event_end_time']:'23:30:00';
			$event_id = ($post['event_id']!="")?trim($post['event_id']):'';
			$event_name = ($post['event_name']!="")?trim($post['event_name']):'';
			$event_venue = ($post['event_venue']!="")?trim($post['event_venue']):'';
			$event_address = ($post['event_address']!="")?trim($post['event_address']):'';
			$event_type = ($post['event_type']!="")?trim($post['event_type']):'';
			$event_start_date = ($post['event_start_date']!="")?trim($post['event_start_date']):'';
			$event_start_time = $start_time;
			$event_end_date = ($post['event_end_date']!="")?trim($post['event_end_date']):'';
			$event_end_time = $end_time;
			$allday = ($post['allday']==1)?trim($post['allday']):0;			
			$event_description = ($post['event_description']!="")?trim($post['event_description']):'';
			
			$error = 0;
			
            if($event_name == '') {			
            	$error = 1;
				$data = array('error'=>'Event title can not be empty.');
            	//return false;
            } 
			$start_date = date("Y-m-d H:i:s",strtotime($event_start_date))." ".date("H:i:s",strtotime($event_start_time));
			$end_date = date("Y-m-d H:i:s",strtotime($event_end_date))." ".date("H:i:s",strtotime($event_end_time));
			if(strtotime($start_date) > strtotime($end_date)) {				
				$error = 1;
				$data = array('error'=>'End date should be greater than start date.');
            	//return false;
            }
			
			if($error==0){
					$outpt = $this->Eventsdb->updateEvent($event_name, $event_venue, $event_address, $event_type, $start_date, $end_date, $allday, $event_description,$event_id);	
					if($outpt){
						$data = array('success'=>'Event successfully updated.');
					}
			}
			//print_r($data);die;
          	return $data;
		} catch(Exception $e) {
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	/**
     * Purpose: update event drop logic
     *
     * Access is public
     *
     * @param	Array	$post array
     * 
     * @return  json array
     */
	
	public  function updateEventDrop(Array $post) {
		try{
			/*echo "<pre>";
			print_r($post);
			exit;*/
			$data = array();
			
			$allday = ($post['allday']==1)?trim($post['allday']):0;		
			$id = (isset($post['id']))?$post['id']:'';
			$start_date = date("Y-m-d H:i:s",strtotime($post['start']));
			$end_date = date("Y-m-d H:i:s",strtotime($post['end']));
			
			$outpt = $this->Eventsdb->updateEventDrop($start_date, $end_date, $allday, $id);	
			if($outpt){
				$data = array('success'=>'Event successfully updated.');
			}
			//print_r($data);die;
          	return $data;
		} catch(Exception $e) {
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
	/**
     * Purpose: update event drop logic
     *
     * Access is public
     *
     * @param	Array	$post array
     * 
     * @return  json array
     */
	
	public  function updateEventResize($end_date, $id) {
		try{
			/*echo "<pre>";
			print_r($post);
			exit;*/
			$data = array();
			
			$end_date = date("Y-m-d H:i:s",strtotime($end_date));
			
			$outpt = $this->Eventsdb->updateEventResize($end_date, $id);	
			if($outpt){
				$data = array('success'=>'Event successfully updated.');
			}
			//print_r($data);die;
          	return $data;
		} catch(Exception $e) {
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
}
?>