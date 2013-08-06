<?php
/*
*================AllySoft Internal Use only========================

*-----------------------------------------------------------------*
* Copy Right Header Information*
*-----------------------------------------------------------------*
* Project	:	GetLinc
* File		:	EventsController.php 
* Module	:	Default Module
* Owner		:	RAM's 
* Purpose	:	This class is used for common Events operations for all user types
* Date		:	08/05/2012


* Modification History
* ----------------------------------------------------------------------------------------------------------------
* Dated		Version		Who		Description
* -----------------------------------------------------------------------------------------------------------------
* %D%		%T%			%W%
* -----------------------------------------------------------------------------------------------------------------

*===================================================================================================================
*/

class EventsController extends Zend_Controller_Action { 
	public $session;	// used for managing session with NAMESPACE portal
	public $error;		// used for managing session with NAMESPACE portalerror
	private $events;			// used for creating an instance of model, Access is with in the class
	private $eventsdb;	// used for creating an instance of model, Access is with in the class	

	/**
     * Purpose: Initiates sessions with Namespace 'portal' and 'portalerror' 
     * 			and creates an instance of the model class 'Application_Model_Users'
     *
     * Access is public
     *
     * @param	
     * 
     * @return  
     */
	
	public function init() { 
		/*echo "store/index/init";
		exit;  */
        $this->_helper->layout->setLayout('default/calendar');
		//$this->setLayoutAction('store/layout');	
		$this->events = new Default_Model_Events();
		$this->eventsdb = new Default_Model_Eventsdb();
		$this->view->headLink()->setStylesheet($this->view->baseUrl('public/default/css/dev_events.css'));
		
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
			//echo "store/index/index";
			//exit; 
		}catch (Exception $e){
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	
    /** 
	 * Loading a calendar data and  rendering the data in calendar view
     * @access is public
	 * @author Alok Pandey.
	 * @copyright GetLinc.com, Inc. 
	 * @license GetLinc.com, Inc.
	*/
	public function calendarAction(){
		try{			
			$request = $this->getRequest();
			$Values = $request->getPost();

			/*$Request_Values = $request->getPost();
			if ($request->isPost()) {
				if(!$this->signup -> createUser($params)) {
					echo "Registration Failed! Try Again.";
				} else {
					echo $this->session->success;
					echo "Successfully Registered. Please login with your account.";
					exit;
				}
			}else{			
				echo "Data not received from Origin Place.";exit;
			}*/

			
			
		}catch (Exception $e){
			Application_Model_Logging::lwrite($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	 /** 
	 * Process a create event form using an aja call
     * @access is public
	 * @author Alok Pandey.
	 * @copyright GetLinc.com, Inc. 
	 * @license GetLinc.com, Inc.
	*/
	public function processAction(){
		if ($this->getRequest()->isXmlHttpRequest()) {
			if ($this->getRequest()->isPost()) {
				$data = array();
				$this->_helper->layout->disableLayout();
				$post = $this->getRequest()->getPost();
				if(isset($post['event_id']) && !empty($post['event_id'])){
					$dataCreateEvent = $this->events -> updateEvent($post);
				} else {
					$dataCreateEvent = $this->events -> createEvent($post);
				}
				if($dataCreateEvent['error']) {
					$error_data = $dataCreateEvent;
					echo json_encode($error_data);
					die;
				} else {
					$data = array('success'=>'Event successfully created.');
					echo json_encode($data);
					die;
				}
			}	
			
		}
	
	}
	
	 /** 
	 * load events in calendar between start and end date
     * @access is public
	 * @author Alok Pandey.
	 * @copyright GetLinc.com, Inc. 
	 * @license GetLinc.com, Inc.
	*/
	public function loadAction(){
		if ($this->getRequest()->isXmlHttpRequest()) {
			if ($this->getRequest()->isPost()) {
				$data = array();
				$this->_helper->layout->disableLayout();
				$post = $this->getRequest()->getPost();
				$start_date = $post['start'];
				$end_date = $post['end'];
				$events = $this->eventsdb -> getEvents($start_date,$end_date);
				if($events){
					foreach($events as $key=>$value) {
						$data[] = array(
						'id'=>$value['event_id'],
						'title'=>$value['event_title'],
						'allDay'=>($value['event_all_day']==1)?TRUE:FALSE,
						'start'=>$value['event_startdate'],
						'end_date'=>$value['event_enddate'],
						'description'=>$value['event_details'],
						'event_type'=>$value['event_type'],
						'event_location'=>$value['event_location'],
						'event_address'=>$value['event_address'],
						'editable'=>true,
						'color'=>'#69131E'
						
						);
					}
				}
				echo json_encode($data);
				die;
			}	
			
		}
	
	}
	
	
}
?>