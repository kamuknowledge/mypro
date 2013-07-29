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
}

?>
