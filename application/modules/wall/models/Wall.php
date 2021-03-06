<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Wall
 *
 * @author KRISHNA
 */
class Wall_Model_Wall extends Application_Model_Validation {

    public $walldb;

    //put your code here
    public function Updates($uid, $lastid) {
        try {
                $this->walldb = new Wall_Model_Walldb();
                $result = $this->walldb->Updates($uid, $lastid);
                return $result;
        } catch (Exception $e) {
            Application_Model_Logging::lwrite($e->getMessage());
            throw new Exception($e->getMessage());
        }
    }
     public function Total_Updates($uid) {
        try {
                $this->walldb = new Wall_Model_Walldb();
                $result = $this->walldb->Total_Updates($uid);
                return $result;
        } catch (Exception $e) {
            Application_Model_Logging::lwrite($e->getMessage());
            throw new Exception($e->getMessage());
        }
    }
    public function Friends_Updates($uid,$lastid) {
        try {
                $this->walldb = new Wall_Model_Walldb();
                $result = $this->walldb->Friends_Updates($uid,$lastid);
                return $result;
        } catch (Exception $e) {
            Application_Model_Logging::lwrite($e->getMessage());
            throw new Exception($e->getMessage());
        }
    }
      public function Total_Friends_Updates($uid) {
        try {
                $this->walldb = new Wall_Model_Walldb();
                $result = $this->walldb->Total_Friends_Updates($uid);
                return $result;
        } catch (Exception $e) {
            Application_Model_Logging::lwrite($e->getMessage());
            throw new Exception($e->getMessage());
        }
    }
      public function Comments($msg_id,$second_count) {
        try {
                
                $this->walldb = new Wall_Model_Walldb();
                $result = $this->walldb->Comments($msg_id,$second_count);               
                return $result;
        } catch (Exception $e) {
            Application_Model_Logging::lwrite($e->getMessage());
            throw new Exception($e->getMessage());
        }
    }
     public function Insert_Comment($uid, $msg_id, $comment) {
        try {
           
                $this->walldb = new Wall_Model_Walldb();
                $result = $this->walldb->Insert_Comment($uid, $msg_id, $comment);
                return $result;
        } catch (Exception $e) {
            Application_Model_Logging::lwrite($e->getMessage());
            throw new Exception($e->getMessage());
        }
    }
    
    public function Insert_Update($uid, $update, $uploads) {
        try {
           
                $this->walldb = new Wall_Model_Walldb();
                $result = $this->walldb->Insert_Update($uid, $update, $uploads);
                return $result;
        } catch (Exception $e) {
            Application_Model_Logging::lwrite($e->getMessage());
            throw new Exception($e->getMessage());
        }
    }
	
	public function Delete_Message($uid, $msg_id) {
        try {
           
                $this->walldb = new Wall_Model_Walldb();
                $result = $this->walldb->Delete_Message($uid, $msg_id);
                return $result;
        } catch (Exception $e) {
            Application_Model_Logging::lwrite($e->getMessage());
            throw new Exception($e->getMessage());
        }
    }
	public function Delete_Comment($uid, $com_id) {
        try {
           
                $this->walldb = new Wall_Model_Walldb();
                $result = $this->walldb->Delete_Comment($uid, $com_id);
                return $result;
        } catch (Exception $e) {
            Application_Model_Logging::lwrite($e->getMessage());
            throw new Exception($e->getMessage());
        }
    }
	
	public function User_Search($searchword) {
        try {
           
                $this->walldb = new Wall_Model_Walldb();
                $result = $this->walldb->User_Search($searchword);
                return $result;
        } catch (Exception $e) {
            Application_Model_Logging::lwrite($e->getMessage());
            throw new Exception($e->getMessage());
        }
    }
	
	public function Insert_User_Wall($file_name,$username) {
        try {
            //echo $file_name."----".$username;exit;
                $this->walldb = new Wall_Model_Walldb();
                $result = $this->walldb->Insert_User_Wall($file_name,$username);
                return $result;
        } catch (Exception $e) {
            Application_Model_Logging::lwrite($e->getMessage());
            throw new Exception($e->getMessage());
        }
    }
	public function Get_Upload_Image_Id($id) {
        try {
            //echo $file_name."----".$username;exit;
                $this->walldb = new Wall_Model_Walldb();
                $result = $this->walldb->Get_Upload_Image_Id($id);
                return $result;
        } catch (Exception $e) {
            Application_Model_Logging::lwrite($e->getMessage());
            throw new Exception($e->getMessage());
        }
    }
	
}

?>
