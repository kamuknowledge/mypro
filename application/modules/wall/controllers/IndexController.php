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

        /* Validations Layer Class */
        $this->wall = new Wall_Model_Wall();

        /* Check Login */
        //if(!$this->wall->check_login()){ $this->_redirect('/');exit;}

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
           // $this->view->headLink()->appendStylesheet($this->view->baseUrl('public/wall') . '/css/lightbox.css');
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
		if ($this->_request->isXmlHttpRequest()) {
                $this->_helper->layout->disableLayout();
            }
            $lastid = $this->_getParam('lastid', '0');
            $profile_uid = $this->_getParam('userid');
            $uid = $this->session->userid?$this->session->userid:86;
            if ($lastid == '')
               $lastid = 0;

            if ($profile_uid) {
			
                $updatesarray = $this->wall->Updates($profile_uid, $lastid);
                $total = $this->wall->Total_Updates($profile_uid);
            } else { 
                $updatesarray = $this->wall->Friends_Updates($uid, $lastid);
                $total = $this->wall->Total_Friends_Updates($uid);
            }
			//echo "<pre>";print_r($updatesarray);exit;
			//echo sizeof($updatesarray);exit;
            // if ($gravatar)
            //     $session_face = $this->wall->Gravatar($uid);
            // else
            //     $session_face = $this->wall->Profile_Pic($uid);
            $this->view->updatesarray = $updatesarray;
            $this->view->sess_uid = $uid;
		 	$this->view->perpage = $this->perpage;//exit;
           $this->view->total = $total;
		   $this->view->UserProfileImage = $this->session->UserProfileImage;
        } catch (Exception $e) {
            Application_Model_Logging::lwrite($e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    public function ajaxmessageAction() {
        try {

            if ($this->_request->isXmlHttpRequest()) {
                $this->_helper->layout->disableLayout();
            }
            $update = $this->_getParam('update');
            $uploads = $this->_getParam('uploads');
            if (isset($update)) {
                $update = mysql_real_escape_string($update);
                $uploads = $uploads;
                $uid = $this->session->userid?$this->session->userid:86;
                $data = $this->wall->Insert_Update($uid, $update, $uploads);
				//echo "<pre>";print_r($data);
                $this->view->data = $data;
            }
			$this->view->UserProfileImage = $this->session->UserProfileImage;
        } catch (Exception $e) {
            Application_Model_Logging::lwrite($e->getMessage());
            throw new Exception($e->getMessage());
        }
    }
	public function getuploadimagesAction() {
        try {

            if ($this->_request->isXmlHttpRequest()) {
                
            }
			$this->_helper->layout->disableLayout();
            $id = $this->_getParam('id');
            	
            if (isset($id)) {
                 $uid = $this->session->userid?$this->session->userid:86;
                $data = $this->wall->Get_Upload_Image_Id($id);
				//echo "<pre>";print_r($data);
                $this->view->imgpathdata = $data;
            }
        } catch (Exception $e) {
            Application_Model_Logging::lwrite($e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    public function loadcommentsAction() {
        try {
            if ($this->_request->isXmlHttpRequest()) {
                $this->_helper->layout->disableLayout();
            }
            $msg_id = $this->_getParam("msg_id");
            $msg_uid = $this->_getParam("msg_uid");
            $second_count = 0;
            $commentsarray = $this->wall->Comments($msg_id, 0);
            $x = $this->_getParam("x", 1);
            // echo "<pre>";print_r($commentsarray);
            $comment_count = 0;
            if ($x) {
                $comment_count = count($commentsarray);

                $second_count = $comment_count - 2;

                if ($comment_count > 2) {

                    $commentsarray = $this->wall->Comments($msg_id, $second_count);
                }
            }

           //echo "<pre>"; print_r($commentsarray);

            $this->view->comment_count = $comment_count;
            $this->view->commentsarray = $commentsarray;
            //$this->view->userid=$this->session->userid;
            //$this->view->userid = 86;
            $this->view->userid = $this->session->userid?$this->session->userid:86;
            $this->view->msg_id = $msg_id;
            $this->view->msg_uid = $msg_uid;
			/* User profile image*/
			$this->view->UserProfileImage = $this->session->UserProfileImage;
			
        } catch (Exception $e) {
            Application_Model_Logging::lwrite($e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    public function ajaxcommentsAction() {
        try {
            if ($this->_request->isXmlHttpRequest()) {
                $this->_helper->layout->disableLayout();
            }
            $msg_id = $this->_getParam("msg_id");
            $comment = $this->_getParam("comment");
            $second_count = 0;
            $ip = $_SERVER['REMOTE_ADDR'];

            //$uid=$this->session->userid;
            $uid = $this->session->userid?$this->session->userid:86;
            $cdata = $this->wall->Insert_Comment($uid, $msg_id, $comment, $ip);
			//echo "<pre>";print_R($cdata);
            $this->view->cdata = $cdata;
            $this->view->userid = $this->session->userid;
            $this->view->msg_id = $msg_id;
			$this->view->UserProfileImage = $this->session->UserProfileImage;
        } catch (Exception $e) {
            Application_Model_Logging::lwrite($e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    public function webcamimageajaxAction() {
        try {
		 if ($this->_request->isXmlHttpRequest()) {
                $this->_helper->layout->disableLayout();
            }
            $invalid = "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";
$xyz="       iVBORw0KGgoAAAANSUhEUgAAAUAAAADwCAYAAABxLb1rAAADJUlEQVR4nO3UMQEAAAiAMPuX1hgebAm4mAWImu8AgC8GCGQZIJBlgECWAQJZBghkGSCQZYBAlgECWQYIZBkgkGWAQJYBAlkGCGQZIJBlgECWAQJZBghkGSCQZYBAlgECWQYIZBkgkGWAQJYBAlkGCGQZIJBlgECWAQJZBghkGSCQZYBAlgECWQYIZBkgkGWAQJYBAlkGCGQZIJBlgECWAQJZBghkGSCQZYBAlgECWQYIZBkgkGWAQJYBAlkGCGQZIJBlgECWAQJZBghkGSCQZYBAlgECWQYIZBkgkGWAQJYBAlkGCGQZIJBlgECWAQJZBghkGSCQZYBAlgECWQYIZBkgkGWAQJYBAlkGCGQZIJBlgECWAQJZBghkGSCQZYBAlgECWQYIZBkgkGWAQJYBAlkGCGQZIJBlgECWAQJZBghkGSCQZYBAlgECWQYIZBkgkGWAQJYBAlkGCGQZIJBlgECWAQJZBghkGSCQZYBAlgECWQYIZBkgkGWAQJYBAlkGCGQZIJBlgECWAQJZBghkGSCQZYBAlgECWQYIZBkgkGWAQJYBAlkGCGQZIJBlgECWAQJZBghkGSCQZYBAlgECWQYIZBkgkGWAQJYBAlkGCGQZIJBlgECWAQJZBghkGSCQZYBAlgECWQYIZBkgkGWAQJYBAlkGCGQZIJBlgECWAQJZBghkGSCQZYBAlgECWQYIZBkgkGWAQJYBAlkGCGQZIJBlgECWAQJZBghkGSCQZYBAlgECWQYIZBkgkGWAQJYBAlkGCGQZIJBlgECWAQJZBghkGSCQZYBAlgECWQYIZBkgkGWAQJYBAlkGCGQZIJBlgECWAQJZBghkGSCQZYBAlgECWQYIZBkgkGWAQJYBAlkGCGQZIJBlgECWAQJZBghkGSCQZYBAlgECWQYIZBkgkGWAQJYBAlkGCGQZIJBlgECWAQJZBghkGSCQZYBAlgECWQYIZBkgkGWAQJYBAlkGCGQZIJBlgECWAQJZBghkGSCQZYBAlgECWQYIZBkgkGWAQJYBAlkGCGQZIJBlgECWAQJZBghkGSCQZYBAlgECWQYIZBkgkHVa/ZYGT9KYyQAAAABJRU5ErkJggg==";
         

		$params = $this->_getAllParams();
//echo "<pre>";print_r($params);
//print_r($_POST);
//exit;		
		$uid = $this->session->userid?$this->session->userid:86;
		//web cam image upload
			if ($params['type'] == "pixel") 
				{
					$image = $params['image'];
					$filter_image = str_replace("data:image/png;base64,", "", $image);
					// input is in format 1,2,3...|1,2,3...|...
					if($filter_image == $invalid)
						{
							$im = "";
							echo "false";
						}
					else
					{
							$im = imagecreatetruecolor(320, 240);
						foreach (explode("|", $params['image']) as $y => $csv) {
							foreach (explode(";", $csv) as $x => $color) {
								imagesetpixel($im, $x, $y, $color);
							}
						}
					}
				} else {
					// input is in format: data:image/png;base64,...
					$image = $params['image'];
					$filter_image = str_replace("data:image/png;base64,", "", $image);
					if($filter_image == $invalid)
						{
							$im = "";
							echo "false";
						}
					else
						$im = imagecreatefrompng($params['image']);
				}
				$LogoSet='';
				if($im)
					{
				$filename=time()."_".$uid.".jpg";

				//$data=$Wall->Image_Upload($uid,$filename);
				imagejpeg($im);
				Imagejpeg($im, APPLICATION_PATH."/../public/uploads/webcam_wall_images/".$filename);
				$LogoSet = $this->wall->Insert_User_Wall($filename, $uid);
				}	
				$this->view->camdata = $LogoSet;
				//$this->view->camdata = '';
		
		
			/*
            $upload = new Zend_File_Transfer();                                    
				
				//$upload = new Zend_File_Transfer_Adapter_Http();                                
				$files = $upload->getFileInfo();
				foreach ($files as $file => $info) {
					if($upload->isValid($file)){
						$filename = time().$info['name'];
						$upload->addFilter('Rename', APPLICATION_PATH.'/../public/uploads/webcam_wall_images/'.$filename, 1);
						$upload->receive($file);
						$LogoSet = $this->wall->Insert_User_Wall_webcam($filename, $uid);
					}
				}//echo $filename."----".$uid;exit;
				//echo "<pre>";print_r($LogoSet);exit;
		$this->view->imgdata = $LogoSet;	*/


		
        } catch (Exception $e) {
            Application_Model_Logging::lwrite($e->getMessage());
            throw new Exception($e->getMessage());
        }
    }
	public function webcamimageloadajaxAction() {
        try {
		 if ($this->_request->isXmlHttpRequest()) {
                $this->_helper->layout->disableLayout();
            }
			//$params = $this->_getAllParams();
			$id = $this->_getParam("webcam");
			$data = $this->wall->Get_Upload_Image_Id($id);

			//echo "<pre>";print_r($params);die();
			$this->view->camdata = $data;
			
			} catch (Exception $e) {
            Application_Model_Logging::lwrite($e->getMessage());
            throw new Exception($e->getMessage());
        }
    }
	 public function deletemessageAction() {
        try {

            if ($this->_request->isXmlHttpRequest()) {
                $this->_helper->layout->disableLayout();
            }
            $msg_id = $this->_getParam('msg_id');
			$uid = $this->session->userid?$this->session->userid:86;
            if (isset($msg_id)) {
                $msg_id = mysql_real_escape_string($msg_id);
                $this->wall->Delete_Message($uid, $msg_id);
			}
        } catch (Exception $e) {
            Application_Model_Logging::lwrite($e->getMessage());
            throw new Exception($e->getMessage());
        }
    }
	
	public function deletecommentAction() {
        try {

            if ($this->_request->isXmlHttpRequest()) {
                $this->_helper->layout->disableLayout();
            }
            $com_id = $this->_getParam('com_id');
			$uid = $this->session->userid?$this->session->userid:86;
            if (isset($com_id)) {
                $com_id = mysql_real_escape_string($com_id);
                $this->wall->Delete_Comment($uid, $com_id);
			}
        } catch (Exception $e) {
            Application_Model_Logging::lwrite($e->getMessage());
            throw new Exception($e->getMessage());
        }
    }
	public function imageajaxAction() {
        try {
		             
			$this->_helper->layout->disableLayout();
            $uid = $this->session->userid?$this->session->userid:86;
            $upload = new Zend_File_Transfer();                                    
				
				//$upload = new Zend_File_Transfer_Adapter_Http();                                
				$LogoSet='';
				$files = $upload->getFileInfo();
				foreach ($files as $file => $info) {
					if($upload->isValid($file)){
						$filename = time().$info['name'];
						$upload->addFilter('Rename', APPLICATION_PATH.'/../public/uploads/wall_images/'.$filename, 1);
						$upload->receive($file);
						$LogoSet = $this->wall->Insert_User_Wall($filename, $uid);
					}
				}//echo $filename."----".$uid;exit;
				//echo "<pre>";print_r($LogoSet);exit;
				$this->view->imgdata = $LogoSet;
        } catch (Exception $e) {
            Application_Model_Logging::lwrite($e->getMessage());
            throw new Exception($e->getMessage());
        }
    }
	public function searchfriendAction() {
        try {
            if ($this->_request->isXmlHttpRequest()) {
                $this->_helper->layout->disableLayout();
            }
            $searchword = $this->_getParam("searchword");
            $uid = $this->session->userid?$this->session->userid:86;
            $userdata = $this->wall->User_Search($searchword);
			//echo "<pre>";print_R($userdata);exit;
            $this->view->userdata = $userdata;
        } catch (Exception $e) {
            Application_Model_Logging::lwrite($e->getMessage());
            throw new Exception($e->getMessage());
        }
    }
	public function profileAction($id) {
        try {
            if ($this->_request->isXmlHttpRequest()) {
                $this->_helper->layout->disableLayout();
            }
            echo $id = $this->_getParam("id");die();
            $uid = $this->session->userid?$this->session->userid:86;
            $userdata = $this->wall->User_Search($searchword);
			//echo "<pre>";print_R($userdata);exit;
            $this->view->userdata = $userdata;
        } catch (Exception $e) {
            Application_Model_Logging::lwrite($e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

}

?>