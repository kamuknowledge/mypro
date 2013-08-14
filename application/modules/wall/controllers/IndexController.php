<?php
/*
 * ================AllySoft Internal Use only========================

 * -----------------------------------------------------------------*
 * Copy Right Header Information*
 * -----------------------------------------------------------------*
 * Project	:	GetLinc
 * File		:	IndexController.php 
 * Module	:	Default Module
 * Owner		:	RAM's 
 * Purpose	:	This class is used for common user operations for all user types
 * Date		:	08/05/2012


 * Modification History
 * ----------------------------------------------------------------------------------------------------------------
 * Dated		Version		Who		Description
 * -----------------------------------------------------------------------------------------------------------------
 * %D%		%T%			%W%
 * -----------------------------------------------------------------------------------------------------------------

 * ===================================================================================================================
 */

class Wall_IndexController extends Zend_Controller_Action {

    public $session; // used for managing session with NAMESPACE portal
    public $error;  // used for managing session with NAMESPACE portalerror
    private $wall;  // used for creating an instance of model, Access is with in the class	
    public $perpage = 2;

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
		
		/* Set Layout */
        $this->_helper->layout->setLayout('default/layout');
		
		/* Validations Layer Class*/
        $this->wall = new Wall_Model_Wall();
		
		/* Check Login */
		if(!$this->wall->check_login()){ $this->_redirect('/');exit;}
		
		/* Sessions */
        $this->session = new Zend_Session_Namespace('MyClientPortal');

        //$this->setLayoutAction('store/layout');		
    }

    /**
     * Purpose: Index action shows user login screen
     * Access is public
     *
     * @param
     * @return  
     */
    public function indexAction() {
        try {
            // CSS Start

            $this->view->headLink()->setStylesheet($this->view->baseUrl('public/wall') . '/css/facebox.css');
            $this->view->headLink()->appendStylesheet($this->view->baseUrl('public/wall') . '/css/tipsy.css');
            $this->view->headLink()->appendStylesheet($this->view->baseUrl('public/wall') . '/css/lightbox.css');
            $this->view->headLink()->appendStylesheet($this->view->baseUrl('public/wall') . '/css/wall.css');
            // CSS End
            // javascript start

            $this->view->headScript()->appendFile($this->view->baseUrl('public/wall') . '/js/jquery.wallform.js', 'text/javascript');
            $this->view->headScript()->appendFile($this->view->baseUrl('public/wall') . '/js/jquery.webcam.js', 'text/javascript');
            $this->view->headScript()->appendFile($this->view->baseUrl('public/wall') . '/js/jquery.color.js', 'text/javascript');
            $this->view->headScript()->appendFile($this->view->baseUrl('public/wall') . '/js/jquery.livequery.js', 'text/javascript');
            $this->view->headScript()->appendFile($this->view->baseUrl('public/wall') . '/js/jquery.timeago.js', 'text/javascript');
            $this->view->headScript()->appendFile($this->view->baseUrl('public/wall') . '/js/jquery.tipsy.js', 'text/javascript');
            $this->view->headScript()->appendFile($this->view->baseUrl('public/wall') . '/js/facebox.js', 'text/javascript');
            $this->view->headScript()->appendFile($this->view->baseUrl('public/wall') . '/js/wall.js', 'text/javascript');
        } catch (Exception $e) {
            Application_Model_Logging::lwrite($e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    public function loadmessageAction() {
        try {
            $lastid = $this->_getParam('lastid', '0');
            $profile_uid = $this->_getParam('userid');
            $uid = 86;
            if ($lastid == '')
                $lastid = 0;

            if ($profile_uid) {
                $updatesarray = $this->wall->Updates($profile_uid, $lastid);
                $total = $this->wall->Total_Updates($profile_uid);
            } else {
                $updatesarray = $this->wall->Friends_Updates($uid, $lastid);
                $total = $this->wall->Total_Friends_Updates($uid);
            }


            // if ($gravatar)
            //     $session_face = $this->wall->Gravatar($uid);
            // else
            //     $session_face = $this->wall->Profile_Pic($uid);
            $this->view->updatesarray = $updatesarray;
            $this->view->sess_id = $uid;
            $this->view->perpage = $this->perpage;
            $this->view->total = $total;
        } catch (Exception $e) {
            Application_Model_Logging::lwrite($e->getMessage());
            throw new Exception($e->getMessage());
        }
    }
    
    public function ajaxmessageAction() {
        try {
             
            if($this->_request->isXmlHttpRequest()){
               $this->_helper->layout->disableLayout();
            }
            $update=$this->_getParam('update');
            $uploads=$this->_getParam('uploads');
           if(isset($update))
                {
                $update=mysql_real_escape_string($update);
                $uploads=$uploads;
                $uid=86;
                $data=$this->wall->Insert_Update($uid,$update,$uploads);
                $this->view->data=$data;
                }


           
        } catch (Exception $e) {
            Application_Model_Logging::lwrite($e->getMessage());
            throw new Exception($e->getMessage());
        }
    }
    

    public function loadcommentsAction() {
        try {
            if($this->_request->isXmlHttpRequest()){
               $this->_helper->layout->disableLayout();
            }
            $msg_id = $this->_getParam("msg_id");
            $msg_uid = $this->_getParam("msg_uid");
            $second_count=0;
            $commentsarray = $this->wall->Comments($msg_id, 0);
            $x = $this->_getParam("x",1);
           // echo "<pre>";print_r($commentsarray);
            $comment_count=0;
            if ($x) {
                $comment_count = count($commentsarray);
               
                 $second_count = $comment_count - 2;
                   
                if ($comment_count > 2) { 
                    
                    $commentsarray = $this->wall->Comments($msg_id, $second_count);
                   
                }
            }
           
            //print_r($commentsarray);
           
            $this->view->comment_count = $comment_count;
            $this->view->commentsarray = $commentsarray;
            //$this->view->userid=$this->session->userid;
            $this->view->userid=86;
            $this->view->msg_id=$msg_id;
            $this->view->msg_uid=$msg_uid;
        } catch (Exception $e) {
            Application_Model_Logging::lwrite($e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    public function ajaxcommentsAction() {
        try {
            if($this->_request->isXmlHttpRequest()){
               $this->_helper->layout->disableLayout();
            }
            $msg_id = $this->_getParam("msg_id");
            $comment = $this->_getParam("comment");
            $second_count=0;
            $ip=$_SERVER['REMOTE_ADDR'];
           
           //$uid=$this->session->userid;
            $uid=86;
            $cdata=$this->wall->Insert_Comment($uid,$msg_id,$comment,$ip);
            $this->view->cdata = $cdata;
            $this->view->userid=$this->session->userid;
            $this->view->msg_id=$msg_id;
           
        } catch (Exception $e) {
            Application_Model_Logging::lwrite($e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

}
?>