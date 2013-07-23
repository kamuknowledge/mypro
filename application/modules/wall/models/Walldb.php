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

        return $query->count();
    }

    public function User_ID($username) {
         $obj = new Application_Model_DataBaseOperations();
        $this->db = $obj->GetDatabaseConnection();
        $username = mysql_real_escape_string($username);
        $query =  $this->db->query("SELECT uid FROM users WHERE username='$username' AND status='1'");
        if ($query->count() == 1) {
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
        $q = mysql_real_escape_string($_POST['searchword']);
        $query =  $this->db->query("select username,uid from users where username like '%$q%' order by uid LIMIT 5");
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
            $morequery = " and M.msg_id<'" . $lastid . "' ";
        // More Button End
        $obj = new Application_Model_DataBaseOperations();
        $this->db = $obj->GetDatabaseConnection();
        $query = $this->db->query("SELECT M.wall_message_id, M.userid, M.wall_message, M.createddatetime, U.email,M.uploads FROM user_wall_messages M, users U  WHERE U.status='1' AND M.uid_fk=U.uid and M.uid_fk='$uid' $morequery order by M.msg_id desc limit " . $this->perpage) or die(mysql_error());
        $data = $query->fetchAll();
        // while ($row = mysql_fetch_array($query))
        //  $data[] = $row;
        return $data;
    }

    // Total Updates   	
    public function Total_Updates($uid) {
        $obj = new Application_Model_DataBaseOperations();
        $this->db = $obj->GetDatabaseConnection();
        $query = $this->db->query("SELECT M.msg_id, M.uid_fk, M.message, M.created, U.username,M.uploads FROM messages M, users U  WHERE U.status='1' AND M.uid_fk=U.uid and M.uid_fk='$uid' $morequery order by M.msg_id ") or die(mysql_error());
        $data = $query->count();
        return $data;
    }

    // Friends_Updates   	
    public function Friends_Updates($uid, $lastid) {
        // More Button
        $obj = new Application_Model_DataBaseOperations();
        $this->db = $obj->GetDatabaseConnection();
        $morequery = "";
        if ($lastid)
            $morequery = " and M.msg_id<'" . $lastid . "' ";
        // More Button End

        $query =  $this->db->query("SELECT DISTINCT M.msg_id, M.uid_fk, M.message, M.created, U.username,M.uploads FROM messages M, users U, friends F  WHERE U.status='1' AND M.uid_fk=U.uid AND  M.uid_fk = F.friend_two AND F.friend_one='$uid' $morequery order by M.msg_id desc limit " . $this->perpage) or die(mysql_error());

        $data=$query->fetchAll();
        //while ($row = mysql_fetch_array($query))
         //   $data[] = $row;
        return $data;
    }

    //Total Friends Updates   	
    public function Total_Friends_Updates($uid) {

         $obj = new Application_Model_DataBaseOperations();
        $this->db = $obj->GetDatabaseConnection();
        $query =  $this->db->query("SELECT DISTINCT M.msg_id, M.uid_fk, M.message, M.created, U.username,M.uploads FROM messages M, users U, friends F  WHERE U.status='1' AND M.uid_fk=U.uid AND  M.uid_fk = F.friend_two AND F.friend_one='$uid' $morequery order by M.msg_id ") or die(mysql_error());

        $data =  $query->count();
        return $data;
    }

    //Comments
    public function Comments($msg_id, $second_count) {
         $obj = new Application_Model_DataBaseOperations();
        $this->db = $obj->GetDatabaseConnection();
        $query = '';
        if ($second_count)
            $query = "limit $second_count,2";
        $query =  $this->db->query("SELECT C.com_id, C.uid_fk, C.comment, C.created, U.username FROM comments C, users U WHERE U.status='1' AND C.uid_fk=U.uid and C.msg_id_fk='$msg_id' order by C.com_id asc $query") or die(mysql_error());
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
        $query = $this->db->query("SELECT profile_pic FROM `users` WHERE uid='$uid'") or die(mysql_error());
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
        $query =$this->db->query("SELECT email FROM `users` WHERE uid='$uid'") or die(mysql_error());
        $row = $query->fetchAll();
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
        $time = time();
        $ip = $_SERVER['REMOTE_ADDR'];
        $query = $this->db->query("SELECT msg_id,message FROM `messages` WHERE uid_fk='$uid' order by msg_id desc limit 1") or die(mysql_error());
        $result = $query->fetch($query);

        if ($update != $result['message']) {
            $uploads_array = explode(',', $uploads);
            $uploads = implode(',', array_unique($uploads_array));
            $query = $this->db->query("INSERT INTO `messages` (message, uid_fk, ip,created,uploads) VALUES ('$update', '$uid', '$ip','$time','$uploads')") or die(mysql_error());
            $newquery = $this->db->query("SELECT M.msg_id, M.uid_fk, M.message, M.created, U.username FROM messages M, users U where M.uid_fk=U.uid and M.uid_fk='$uid' order by M.msg_id desc limit 1 ");
            $result = $newquery->fetch();

            return $result;
        } else {
            return false;
        }
    }

    //Delete update
    public function Delete_Update($uid, $msg_id) {
        $obj = new Application_Model_DataBaseOperations();
        $this->db = $obj->GetDatabaseConnection();
        $query = $this->db->query("DELETE FROM `comments` WHERE msg_id_fk = '$msg_id' and uid_fk='$uid' ") or die(mysql_error());
        $query = $this->db->query("DELETE FROM `messages` WHERE msg_id = '$msg_id' and uid_fk='$uid'") or die(mysql_error());
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
        $query = $this->db->query("insert into user_uploads (image_path,uid_fk)values('$image' ,'$uid')") or die(mysql_error());
        $ids = $query->lastInsertId();
        return $ids;
    }

    //get Image Upload
    public function Get_Upload_Image($uid, $image) {
        $obj = new Application_Model_DataBaseOperations();
        $this->db = $obj->GetDatabaseConnection();
        if ($image) {
            $query = $this->db->query("select id,image_path from user_uploads where image_path='$image'") or die(mysql_error());
        } else {
            $query = $this->db->query("select id,image_path from user_uploads where uid_fk='$uid' order by id desc ") or die(mysql_error());
        }

        $result = $query->fetch();

        return $result;
    }

    //Id Image Upload
    public function Get_Upload_Image_Id($id) {
         $obj = new Application_Model_DataBaseOperations();
        $this->db = $obj->GetDatabaseConnection();
        $query = $this->db->query("select image_path from user_uploads where id='$id'") or die(mysql_error());
        $result = $query->fetch();

        return $result;
    }

    //Insert Comments
    public function Insert_Comment($uid, $msg_id, $comment) {
        $comment = mysql_real_escape_string($comment);
 $obj = new Application_Model_DataBaseOperations();
        $this->db = $obj->GetDatabaseConnection();
        $time = time();
        $ip = $_SERVER['REMOTE_ADDR'];
        $query = $this->db->query("SELECT com_id,comment FROM `comments` WHERE uid_fk='$uid' and msg_id_fk='$msg_id' order by com_id desc limit 1 ") or die(mysql_error());
        $result = $query->fetch();

        if ($comment != $result['comment']) {
            $query = $this->db->query("INSERT INTO `comments` (comment, uid_fk,msg_id_fk,ip,created) VALUES (N'$comment', '$uid','$msg_id', '$ip','$time')") or die(mysql_error());
            $newquery = $this->db->query("SELECT C.com_id, C.uid_fk, C.comment, C.msg_id_fk, C.created, U.username FROM comments C, users U where C.uid_fk=U.uid and C.uid_fk='$uid' and C.msg_id_fk='$msg_id' order by C.com_id desc limit 1 ");
            $result = $newquery->fetch();

            return $result;
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


        $q = $this->db->query("SELECT M.uid_fk FROM comments C, messages M WHERE C.msg_id_fk = M.msg_id AND C.com_id='$com_id'");
        $d = $q->fetch();
        $oid = $d['uid_fk'];

        if ($uid == $oid) {

            $query = $this->db->query("DELETE FROM `comments` WHERE com_id='$com_id'") or die(mysql_error());
            return true;
        } else {

            $query = $this->db->query("DELETE FROM `comments` WHERE uid_fk='$uid' and com_id='$com_id'") or die(mysql_error());
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



        $query =  $this->db->query("SELECT U.username, U.uid FROM users U, friends F WHERE U.status='1' AND U.uid=F.friend_two AND F.friend_one='$uid' AND F.role='fri' ORDER BY F.friend_id DESC LIMIT $con") or die(mysql_error());
       // while ($row = mysql_fetch_array($query))
           // $data[] = $row;
        $data=$query->fetchAll();
        return $data;
    }

    public function Friends_Check($uid, $fid) {
         $obj = new Application_Model_DataBaseOperations();
        $this->db = $obj->GetDatabaseConnection();
        $query = $this->db->query("SELECT role FROM friends WHERE friend_one='$uid' AND friend_two='$fid'") or die(mysql_error());
        $num = $query->fetch();
        return $num['role'];
    }

    public function Friends_Check_Count($uid, $fid) {
        $obj = new Application_Model_DataBaseOperations();
        $this->db = $obj->GetDatabaseConnection();
        $query = $this->db->query("SELECT friend_id FROM friends WHERE friend_one='$uid' AND friend_two='$fid'") or die(mysql_error());
        $num = $query->count();
        return $num;
    }

    // Add Friend
    public function Add_Friend($uid, $fid) {
         $obj = new Application_Model_DataBaseOperations();
        $this->db = $obj->GetDatabaseConnection();
        $fid = mysql_real_escape_string($fid);
        $q = $this->db->query("SELECT friend_id FROM friends WHERE friend_one='$uid' AND friend_two='$fid' AND role='fri'");
        if ($q->count() == 0) {
            $query = $this->db->query("INSERT INTO friends(friend_one,friend_two,role) VALUES ('$uid','$fid','fri')") or die(mysql_error());
            $query = $this->db->query("UPDATE users SET friend_count=friend_count+1 WHERE uid='$uid'") or die(mysql_error());
            return true;
        }
    }

    // Remove Friend
    public function Remove_Friend($uid, $fid) {
          $obj = new Application_Model_DataBaseOperations();
        $this->db = $obj->GetDatabaseConnection();
        $fid = mysql_real_escape_string($fid);
        $q =  $this->db->query("SELECT friend_id FROM friends WHERE friend_one='$uid' AND friend_two='$fid' AND role='fri'");
        if ($q->count() == 1) {
            $query = $this->db->query("DELETE FROM friends WHERE friend_one='$uid' AND friend_two='$fid'") or die(mysql_error());
            $query = $this->db->query("UPDATE users SET friend_count=friend_count-1 WHERE uid='$uid'") or die(mysql_error());
            return true;
        }
    }

}

?>
