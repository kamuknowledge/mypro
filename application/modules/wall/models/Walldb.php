<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Walldb
 *
 * @author KRISHNA
 */
class Wall_Model_Walldb {

    //put your code here
    public $perpage = 10; // Uploads perpage
    public $db;

    public function Login_Check($value, $type) {
         $obj = new Application_Model_DataBaseOperations();
        $this->db = $obj->GetDatabaseConnection();
        $username_email = mysql_real_escape_string($value);
        if (type) {
            $query = $this->db->query("SELECT uid FROM users WHERE username='$username_email' ");
        } else {
            $query = $this->db->query("SELECT uid FROM users WHERE email='$username_email' ");
        }
        $result=$query->fetchAll();
        $data=  sizeof($result);
        return $data;
    }

    public function User_ID($username) {
         $obj = new Application_Model_DataBaseOperations();
        $this->db = $obj->GetDatabaseConnection();
        $username = mysql_real_escape_string($username);
        $query =  $this->db->query("SELECT userid FROM apmusers WHERE email='$username' AND status='1'");
        $result=$query->fetchAll();
        $data=  sizeof($result);
        if ($data == 1) {
            $row = $query->fetch();
            return $row['uid'];
        } else {
            return false;
        }
    }

    public function User_Details($uid) {
        $obj = new Application_Model_DataBaseOperations();
        $this->db = $obj->GetDatabaseConnection();
        $username = mysql_real_escape_string($uid);
        $query = $this->db->query("SELECT uid,username,email,friend_count FROM users WHERE uid='$uid' AND status='1'");
        $data = $query->fetchAll();
        return $data;
    }

    // User Search   	
    public function User_Search($searchword) {
         $obj = new Application_Model_DataBaseOperations();
        $this->db = $obj->GetDatabaseConnection();
        $q = mysql_real_escape_string($searchword);
        $query =  $this->db->query("select concat(AU.firstname,' ',AU.lastname) as username,AU.userid,AU.profile_image,UI.image_path
				from apmusers AU
				LEFT JOIN user_images UI ON UI.userid = AU.userid
	
		where firstname like '%$q%' OR lastname like '%$q%' order by userid LIMIT 5");
        //echo "select concat(firstname,' ',lastname) as username,userid from apmusers where firstname like '%$q%' order by userid LIMIT 5";exit;
        //while ($row = mysql_fetch_array($query))
         //   $data[] = $row;
        $data=$query->fetchAll();
        return $data;
    }

    // Updates   	
    public function Updates($uid, $lastid) {
        // More Button
        $morequery = "";
        if ($lastid)
            $morequery = " and M.wall_message_id<'" . $lastid . "' ";
        // More Button End
        $obj = new Application_Model_DataBaseOperations();
        $this->db = $obj->GetDatabaseConnection();
        $query = $this->db->query("SELECT M.wall_message_id, M.userid, M.wall_message, M.createddatetime, U.emailid,M.wall_uploads FROM user_wall_messages M, apmusers U,  WHERE U.statusid='1' AND M.userid=U.userid and M.userid='$uid' $morequery order by M.wall_message_id desc limit " . $this->perpage);
        $data = $query->fetchAll();
        // while ($row = mysql_fetch_array($query))
        //  $data[] = $row;
        return $data;
    }

    // Total Updates   	
    public function Total_Updates($uid) {
        $obj = new Application_Model_DataBaseOperations();
        $this->db = $obj->GetDatabaseConnection();
        $query = $this->db->query("SELECT M.wall_message_id, M.userid, M.wall_message, M.createddatetime, U.emailid,M.wall_uploads FROM user_wall_messages M, apmusers U  WHERE U.statusid='1' AND M.userid=U.userid and M.userid='$uid' $morequery order by M.wall_message_id ");
        $result=$query->fetchAll();
        $data = sizeof($result);
        return $data;
    }

    // Friends_Updates   	
    public function Friends_Updates($uid, $lastid) {
        // More Button
        $obj = new Application_Model_DataBaseOperations();
        $this->db = $obj->GetDatabaseConnection();
        $morequery = "";
        if ($lastid)
            $morequery = " and UM.wall_message_id<'" . $lastid . "' ";
        // More Button End
//echo "SELECT DISTINCT M.wall_message_id, M.userid, M.wall_message, M.createddatetime, U.firstname,U.lastname,M.wall_uploads FROM user_wall_messages M, apmusers U, user_connections F  WHERE U.statusid='1' AND M.userid=U.userid AND  M.userid = F.friend_id AND F.userid='$uid' $morequery order by M.wall_message_id desc limit " . $this->perpage;
       // $query =  $this->db->query("SELECT DISTINCT M.wall_message_id, M.userid, M.wall_message, M.createddatetime, U.firstname,U.lastname,M.wall_uploads FROM user_wall_messages M, apmusers U, user_connections F  WHERE U.statusid='1' AND M.userid=U.userid AND  M.userid = F.friend_id AND F.userid='$uid' $morequery order by M.wall_message_id desc limit " . $this->perpage);
        $query =  $this->db->query("SELECT AU.firstname,AU.lastname,AU.profile_image,UI.image_path,
		UM.wall_message_id, UM.userid, UM.wall_message, UM.createddatetime,UM.wall_uploads
		FROM `user_wall_messages` as UM LEFT JOIN apmusers as AU on AU.userid=UM.userid 
		LEFT JOIN user_connections UC ON UM.userid = UC.friend_id
		LEFT JOIN user_images UI ON AU.userid = UI.userid
		WHERE UM.statusid='1' AND UI.is_primary='1' AND UM.userid='".$uid."' $morequery order by UM.wall_message_id desc limit " . $this->perpage);
   
        $data=$query->fetchAll();
		return $data;
    }

    //Total Friends Updates   	
    public function Total_Friends_Updates($uid) {

         $obj = new Application_Model_DataBaseOperations();
        $this->db = $obj->GetDatabaseConnection();
       // $query =  $this->db->query("SELECT DISTINCT M.wall_message_id, M.userid, M.wall_message, M.createddatetime, U.emailid,M.wall_uploads FROM user_wall_messages M, apmusers U, user_connections F  WHERE U.statusid='1' AND M.userid=U.userid AND  M.userid = F.friend_id AND F.userid='$uid' order by M.wall_message_id ");
        $query =  $this->db->query("SELECT * FROM `user_wall_messages` as UM LEFT JOIN apmusers as AU on AU.userid=UM.userid 
		LEFT JOIN user_connections UC ON UM.userid = UC.friend_id
		WHERE UM.statusid='1' AND UM.userid='".$uid."' ");
		$result=$query->fetchAll();
        $data = sizeof($result);
        return $data;
        
    }

    //Comments
    public function Comments($msg_id, $second_count) {
        
       
        
         $obj = new Application_Model_DataBaseOperations();
        $this->db = $obj->GetDatabaseConnection();
       //echo $second_count;
         $select =$this->db->select("c.comment_id,")
             ->from(array('c' => 'user_wall_message_comments'),
                    array('comment_id', 'userid','wall_comment','createddatetime'))
             ->joinLeft(array('u' => 'apmusers'),
                    'c.userid = u.userid',array('emailid','firstname','lastname'))
                ->joinLeft(array('ui' => 'user_images'),
                    'c.userid = ui.userid',array('image_path'))
                ->where("u.statusid=?",1)
				->where('ui.is_primary',1)
                ->where('c.wall_message_id=?',$msg_id)
                ->order(array('c.comment_id ASC'));
                 if($second_count!=0)
                $select->limit(2,$second_count);
                 //echo $select;
       // exit;
        $query=$this->db->query($select);
        //$query =  $this->db->query("SELECT C.comment_id, C.userid, C.wall_comment, C.createddatetime, U.email,UI.image_path FROM user_wall_message_comments C, apmusers U,user_image UI WHERE U.statusid='1' AND C.userid=U.userid and C.userid=UI.userid and  C.wall_message_id='$msg_id' order by C.comment_id asc $query");
        //while ($row = mysql_fetch_array($query))
           // $data[] = $row;
        
        $data=$query->fetchAll();
        
        if (!empty($data)) {
            return $data;
        }
    }

    //Avatar Image
    //From database
    public function Profile_Pic($uid) {
        $obj = new Application_Model_DataBaseOperations();
        $this->db = $obj->GetDatabaseConnection();
        $query = $this->db->query("SELECT profile_pic FROM `users` WHERE uid='$uid'");
        $row = $query->fetchAll();
        if (!empty($row['profile_pic'])) {
            $profile_pic_path = $base_url . 'profile_pic/';
            $data = $profile_pic_path . $row['profile_pic'];
            return $data;
        } else {
            $data = "icons/default.jpg";
            return $data;
        }
    }

    //  Gravatar Image
    public function Gravatar($uid) {
         $obj = new Application_Model_DataBaseOperations();
        $this->db = $obj->GetDatabaseConnection();
        $query =$this->db->query("SELECT email FROM `apmusers` WHERE userid='$uid'");
        $row = $query->fetch();
        if (!empty($row)) {
            $email = $row['email'];
            $lowercase = strtolower($email);
            $imagecode = md5($lowercase);
            $data = "http://www.gravatar.com/avatar.php?gravatar_id=$imagecode";
            return $data;
        } else {
            $data = "default.jpg";
            return $data;
        }
    }

    //Insert Update
    public function Insert_Update($uid, $update, $uploads) {
        $obj = new Application_Model_DataBaseOperations();
        $this->db = $obj->GetDatabaseConnection();
        $update = mysql_real_escape_string($update);
        $time = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'];
        //$query = $this->db->query("SELECT wall_message_id,wall_message FROM `user_wall_messages` WHERE userid='$uid' order by wall_message_id desc limit 1");
        //$result = $query->fetch($query);

        //if ($update != $result['message']) {
            $uploads_array = explode(',', $uploads);
            $uploads = implode(',', array_unique($uploads_array));
			//echo "INSERT INTO `user_wall_messages` (wall_message, userid, user_ip,createddatetime,statusid) VALUES ('$update', '$uid', '$ip','$time','1'";
            $query = $this->db->query("INSERT INTO `user_wall_messages` (wall_message, userid, user_ip,createddatetime,statusid,wall_uploads) VALUES ('$update', '$uid', '$ip','$time','1','$uploads')");
            
           // $newquery = $this->db->query("SELECT M.msg_id, M.uid_fk, M.message, M.created, U.username FROM messages M, users U where M.uid_fk=U.uid and M.uid_fk='$uid' order by M.msg_id desc limit 1 ");
            
            $select =$this->db->select("m.wall_message_id,")
             ->from(array('m' => 'user_wall_messages'),
                    array('wall_message_id', 'userid','wall_message','createddatetime','wall_uploads'))
             ->joinLeft(array('u' => 'apmusers'),
                    'm.userid = u.userid',array('emailid','firstname','lastname'))
                ->joinLeft(array('ui' => 'user_images'),
                    'm.userid = ui.userid',array('image_path'))
				->where("ui.is_primary", 1)
                ->where("u.statusid=?",1)
                    ->where('m.userid=?',$uid)
                ->order(array('m.wall_message_id DESC'))
                 ->limitPage(0,1);
                // echo $select;
        //exit;
        $selquery=$this->db->query($select);
        
        
            $result = $selquery->fetch();
        return $result;
       /* } else {
            return false;
        }*/
    }

    //Delete update
    public function Delete_Message($uid, $msg_id) {
        $obj = new Application_Model_DataBaseOperations();
        $this->db = $obj->GetDatabaseConnection();
        $query = $this->db->query("DELETE FROM `user_wall_message_comments` WHERE wall_message_id = '$msg_id' and userid='$uid' ");
        $query = $this->db->query("DELETE FROM `user_wall_messages` WHERE wall_message_id = '$msg_id' and userid='$uid'");
        return true;
    }

    //Image Upload
    public function Image_Upload($uid, $image) {
        //Base64 encoding
        $path = "uploads/";
        $img_src = $path . $image;
        $imgbinary = fread(fopen($img_src, "r"), filesize($img_src));
        $img_base = base64_encode($imgbinary);
        $ids = 0;
        $obj = new Application_Model_DataBaseOperations();
        $this->db = $obj->GetDatabaseConnection();
        $query = $this->db->query("insert into user_uploads (image_path,uid_fk)values('$image' ,'$uid')");
        $ids = $query->lastInsertId();
        return $ids;
    }

    //get Image Upload
    public function Get_Upload_Image($uid, $image) {
        $obj = new Application_Model_DataBaseOperations();
        $this->db = $obj->GetDatabaseConnection();
        if ($image) {
            $query = $this->db->query("select id,image_path from user_wall_uploads where image_path='$image'");
        } else {
            $query = $this->db->query("select id,image_path from user_wall_uploads where uid_fk='$uid' order by id desc ");
        }

        $result = $query->fetch();

        return $result;
    }

    //Id Image Upload
    public function Get_Upload_Image_Id($id) {
         $obj = new Application_Model_DataBaseOperations();
        $this->db = $obj->GetDatabaseConnection();
        $query = $this->db->query("select image_path,wall_upload_id,userid from user_wall_uploads where wall_upload_id='$id'");
        $result = $query->fetch();

        return $result;
    }

    //Insert Comments
    public function Insert_Comment($uid, $msg_id, $comment) {
        $obj = new Application_Model_DataBaseOperations();
         $this->db = $obj->GetDatabaseConnection();
        $comment = $comment;
 
       
        $time = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'];
        $query = $this->db->query("SELECT comment_id,wall_comment FROM `user_wall_message_comments` WHERE userid='$uid' and wall_message_id='$msg_id' order by comment_id desc limit 1 ");
       
        $result = $query->fetch();

        if ($comment != $result['wall_comment']) {
            $query = $this->db->query("INSERT INTO `user_wall_message_comments` (wall_comment, userid,wall_message_id,user_ip,createddatetime,statusid) VALUES ('$comment', '$uid','$msg_id', '$ip','$time','1')");
            //$newquery = $this->db->query("SELECT C.com_id, C.uid_fk, C.comment, C.msg_id_fk, C.created, U.username FROM comments C, users U where C.uid_fk=U.uid and C.uid_fk='$uid' and C.msg_id_fk='$msg_id' order by C.com_id desc limit 1 ");
            //$result = $newquery->fetch();
             
       //echo $second_count;
         $select =$this->db->select("c.comment_id,")
             ->from(array('c' => 'user_wall_message_comments'),
                    array('comment_id', 'userid','wall_comment','createddatetime'))
             ->joinLeft(array('u' => 'apmusers'),
                    'c.userid = u.userid',array('emailid','firstname','lastname'))
                ->joinLeft(array('ui' => 'user_images'),
                    'c.userid = ui.userid',array('image_path'))
                ->where("u.statusid=?",1)
                    ->where('c.wall_message_id=?',$msg_id)
                ->order(array('c.comment_id DESC'))
                 ->limitPage(0,1);
                // echo $select;
        //exit;
        $query=$this->db->query($select);
        //$query =  $this->db->query("SELECT C.comment_id, C.userid, C.wall_comment, C.createddatetime, U.email,UI.image_path FROM user_wall_message_comments C, apmusers U,user_image UI WHERE U.statusid='1' AND C.userid=U.userid and C.userid=UI.userid and  C.wall_message_id='$msg_id' order by C.comment_id asc $query");
        //while ($row = mysql_fetch_array($query))
           // $data[] = $row;
        $data=$query->fetch();
		//echo "vvv";print_r($data);
            return $data;
        } else {
            return false;
        }
    }

    //Delete Comments
    public function Delete_Comment($uid, $com_id) {
        $obj = new Application_Model_DataBaseOperations();
        $this->db = $obj->GetDatabaseConnection();
        $uid = mysql_real_escape_string($uid);
        $com_id = mysql_real_escape_string($com_id);


        $q = $this->db->query("SELECT M.userid FROM user_wall_message_comments C, user_wall_messages M WHERE C.wall_message_id = M.wall_message_id AND C.comment_id='$com_id'");
        $d = $q->fetch();
        $oid = $d['userid'];

        if ($uid == $oid) {
			$query = $this->db->query("DELETE FROM `user_wall_message_comments` WHERE comment_id = '$com_id' ");
            return true;
        } else {
			$query = $this->db->query("DELETE FROM `user_wall_message_comments` WHERE comment_id = '$com_id' and userid='$uid' ");
            return true;
        }
    }

    //Friends List
    public function Friends_List($uid, $page, $offset, $rowsPerPage) {
         $obj = new Application_Model_DataBaseOperations();
        $this->db = $obj->GetDatabaseConnection();
        $uid = mysql_real_escape_string($uid);
        $page = mysql_real_escape_string($page);
        $offset = mysql_real_escape_string($offset);
        $rowsPerPage = mysql_real_escape_string($rowsPerPage);
        
        if ($page)
            $con = $offset . "," . $rowsPerPage;
        else
            $con = $rowsPerPage;



        $query =  $this->db->query("SELECT U.username, U.uid FROM users U, friends F WHERE U.status='1' AND U.uid=F.friend_two AND F.friend_one='$uid' AND F.role='fri' ORDER BY F.friend_id DESC LIMIT $con");
       // while ($row = mysql_fetch_array($query))
           // $data[] = $row;
        $data=$query->fetchAll();
        return $data;
    }

    public function Friends_Check($uid, $fid) {
         $obj = new Application_Model_DataBaseOperations();
        $this->db = $obj->GetDatabaseConnection();
        $query = $this->db->query("SELECT role FROM friends WHERE friend_one='$uid' AND friend_two='$fid'");
        $num = $query->fetch();
        return $num['role'];
    }

    public function Friends_Check_Count($uid, $fid) {
        $obj = new Application_Model_DataBaseOperations();
        $this->db = $obj->GetDatabaseConnection();
        $query = $this->db->query("SELECT friend_id FROM friends WHERE friend_one='$uid' AND friend_two='$fid'");
       $result=$query->fetchAll();
        $data = sizeof($result);
        return $data;
    }

    // Add Friend
    public function Add_Friend($uid, $fid) {
         $obj = new Application_Model_DataBaseOperations();
        $this->db = $obj->GetDatabaseConnection();
        $fid = mysql_real_escape_string($fid);
        $q = $this->db->query("SELECT friend_id FROM friends WHERE friend_one='$uid' AND friend_two='$fid' AND role='fri'");
         $result= $q->fetchAll();
        $data = sizeof($result);
      
        if ($data == 0) {
            $query = $this->db->query("INSERT INTO friends(friend_one,friend_two,role) VALUES ('$uid','$fid','fri')");
            $query = $this->db->query("UPDATE users SET friend_count=friend_count+1 WHERE uid='$uid'");
            return true;
        }
    }

    // Remove Friend
    public function Remove_Friend($uid, $fid) {
          $obj = new Application_Model_DataBaseOperations();
        $this->db = $obj->GetDatabaseConnection();
        $fid = mysql_real_escape_string($fid);
        $q =  $this->db->query("SELECT friend_id FROM friends WHERE friend_one='$uid' AND friend_two='$fid' AND role='fri'");
        $result= $q->fetchAll();
        $data = sizeof($result);
        if ($data == 1) {
            $query = $this->db->query("DELETE FROM friends WHERE friend_one='$uid' AND friend_two='$fid'");
            $query = $this->db->query("UPDATE users SET friend_count=friend_count-1 WHERE uid='$uid'");
            return true;
        }
    }
	
	//Insert Update
    public function Insert_User_Wall($file_name,$username) {
        $obj = new Application_Model_DataBaseOperations();
        $this->db = $obj->GetDatabaseConnection();
        //$update = mysql_real_escape_string($update);
        $time = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'];
        
            $uploads_array = explode(',', $uploads);
            $uploads = implode(',', array_unique($uploads_array));
			//echo "INSERT INTO `user_wall_uploads` (image_path, userid,createddatetime,statusid) VALUES ('$file_name', '$username', '$time','1')";
            $query = $this->db->query("INSERT INTO `user_wall_uploads` (image_path, userid, createddatetime,statusid) VALUES ('$file_name', '$username', '$time','1')");
           $id= $this->db->lastInsertId() ;
           /* $select =$this->db->select("wall_upload_id,")
             ->from(array('m' => 'user_wall_uploads'),
                    array('image_path', 'userid','statusid','createddatetime','wall_upload_id'))
                 ->where("statusid=?",1)
                    ->where('userid=?',$username)
                    ->where('wall_upload_id=?',$id);
                 //echo $select;
				 
        //exit;
        $selquery=$this->db->query($select);
        $result = $selquery->fetch();
        return $result;*/
		return $id;
	
	}
	
}

?>
