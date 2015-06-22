<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class DBFunctions {

    private $db;

//put your code here
// constructor
    function __construct() {
        require_once 'DBConnect.php';
// connect to database
        $this->db = new DBConnect();
        $this->db->connect();
    }

// destructor
    function __destruct() {
        
    }

    /**
     * Create group based tables
     */
    public function createGroupTables($tblname, $users) {
//echo "Creating table ...";
        $chat_table = $tblname . '_CHAT';
        if (!$this->createGroupTable($tblname)) {
            return 1;
        }
        if (!$this->createGroupChatTable($chat_table)) {
            return 2;
        }
//echo "Query executed";
//echo "table '$tblname' created";
        if (is_array($users)) {
            foreach ($users as $user) {
                $result = mysql_query("INSERT INTO " . $tblname . "(id, username) VALUES(NULL, '$user')") or die(mysql_error());
                if (!$result) {
                    return 3;
//echo "Error in insertion";
                }
            }
        } else {
            $result = mysql_query("INSERT INTO " . $tblname . "(id, username) VALUES(NULL, '$users')") or die(mysql_error());
            if (!$result) {
                return 3;
//echo "Error in insertion";
            }
        }
        return 0;
    }

    public function deleteGroupEntry($groupname) {
        mysql_query("DELETE FROM user_created_groups WHERE group_name = '$groupname'");
    }

    public function registerGroupName($groupname) {
        mysql_select_db("web_server");
        $result = mysql_query("INSERT INTO user_created_groups(group_name) VALUES('$groupname')") or die(error_log(mysql_error()));
        return $result;
    }

    public function createGroupTable($tblname) {
        $create_table = "CREATE table " . $tblname . "("
                . "id int(11) primary key auto_increment,"
                . "username varchar(20) REFERENCES users(username));";
        return mysql_query($create_table) or die(mysql_error());
    }

    public function createGroupChatTable($tblname) {
        $create_table = "CREATE table " . $tblname . "("
                . "id int(11) primary key auto_increment,"
                . "username varchar(20) REFERENCES users(username),"
                . "message varchar(200),"
                . "created_on timestamp);";
        return mysql_query($create_table) or die(mysql_error());
    }

//    public function registerUsersToGroup($tblname, $users){
//        foreach ($users as $username){
//            $result = mysql_query("INSERT INTO '$tblname'(username) VALUES('$username')") or die(mysql_error());
//            if(!$result){
//                return false;
//            }
//        }
//        return true;
//    }

    public function deleteTeacherNote($id) {
        mysql_query("DELETE FROM teacher_notes WHERE id = '$id'");
    }
    
    public function deleteHelpMessage($id){
         mysql_query("DELETE FROM help_entries WHERE id = '$id'");
    }

    public function dropGroupTable($tblname) {
        $result = mysql_query("DROP TABLE " . $tblname) or die(mysql_error());
        if (!$result) {
            return false;
        }
        return true;
    }

    /**
     * Storing new user
     * returns user details
     */
    public function storeUser($firstname, $lastname, $username, $password, $is_teacher) {
        $result = mysql_query("INSERT INTO users(firstname, lastname, username, password, is_teacher, created_at) VALUES('$firstname', '$lastname', '$username', '$password', '$is_teacher',  NOW())") or die(mysql_error());
// check for successful store
        if ($result) {
// get user details 
//deprecated $uid = mysql_insert_id(); // last inserted id
            $result = mysql_query("SELECT * FROM users");
// return user details
            return mysql_fetch_array($result);
        } else {
            return false;
        }
    }

    /**
     * Get user by email and password
     */
    public function getUserByUsernameAndPassword($username, $password) {
        $result = mysql_query("SELECT * FROM users WHERE username = '$username'") or die(mysql_error());
// check for result 
        $no_of_rows = mysql_num_rows($result);
        if ($no_of_rows > 0) {
            $result = mysql_fetch_array($result);
            $check_password = $result['password'];
// check for password equality
            if ($check_password == $password) {
// user authentication details are correct
                return $result;
            }
        } else {
// user not found
            return false;
        }
    }

    public function getLastHelpEntry() {
        $result = mysql_query("SELECT * FROM help_entries ORDER BY id DESC LIMIT 1") or die(mysql_error());
        $no_of_rows = mysql_num_rows($result);
        if ($no_of_rows > 0) {
            $result = mysql_fetch_array($result);
            $help = array();
            $help['id'] = $result['id'];
            $help['name'] = $result['name'];
            $help['filename'] = $result['filename'];
            $help['rating'] = $result['rating'];
            $help['message'] = $result['message'];
            $help['viewed'] = $result['viewed'];
            return $help;
        }
        return false;
    }

    public function getHelpEntries() {
        $result = mysql_query("SELECT * FROM help_entries") or die(mysql_error());
        $no_of_rows = mysql_num_rows($result);
        if ($no_of_rows > 0) {
            $helpMessages = array();
            while ($row = mysql_fetch_array($result)) {
                $help = array();
                $help[] = $row['id'];
                $help[] = $row['name'];
                $help[] = $row['filename'];
                $help[] = $row['rating'];
                $help[] = $row['message'];
                $help[] = $row['viewed'];
                array_push($helpMessages, $help);
            }
            return $helpMessages;
        } else {
            return false;
        }
    }

    public function getTeacher() {
        $result = mysql_query("SELECT * FROM users WHERE is_teacher = 1") or die(mysql_error());
        $no_of_rows = mysql_num_rows($result);
        if ($no_of_rows > 0) {
            if ($no_of_rows == 1) {
                $result = mysql_fetch_array($result);
                return $result['username'];
            } else {
                $teachers = array();
                while ($row = mysql_fetch_array($result)) {
                    $teachers[] = $row['username'];
                }
                return $teachers;
            }
        }
    }

    public function getUserGroups($username) {
        $groups = array();
        //Get list of user created groups
        $result = mysql_query("SELECT * from user_created_groups LIMIT 0,100") or die(mysql_error());
        //loop through results
        while ($row = mysql_fetch_assoc($result)) {
            //if the entry isnt false, select * from that group table where username = username
            if ($row != false) {
                $userexistsresult = mysql_query("SELECT * from " . $row["group_name"] . " WHERE username = '$username'") or die(mysql_error());
                //If that gave a row result user exists in that group
                if (mysql_num_rows($userexistsresult) > 0) {
                    array_push($groups, $row["group_name"]);
                }
            }
        }
        return $groups;
    }

    public function getUserRealName($username) {
        $result = mysql_query("SELECT * FROM users WHERE username = '$username'") or die(mysql_error());
// check for result 
        $no_of_rows = mysql_num_rows($result);
        if ($no_of_rows > 0) {
            $result = mysql_fetch_array($result);
            $firstname = $result['firstname'];
            $lastname = $result['lastname'];
            return $firstname . " " . $lastname;
        } else {
// user not found
            return false;
        }
    }

    public function getTeacherNotes() {
        $result = mysql_query("SELECT * FROM teacher_notes") or die(mysql_error());
        $no_of_rows = mysql_num_rows($result);
        $notes = array();
        if ($no_of_rows > 0) {
            while ($row = mysql_fetch_array($result)) {
                $note = array();
                $note[] = $row['category'];
                $note[] = $row['title'];
                $note[] = $row['content'];
                $note[] = $row['metric'];
                $note[] = $row['id'];
                array_push($notes, $note);
            }
            return $notes;
        } else {
            return false;
        }
    }

    public function getStudentList() {
        $result = mysql_query("SELECT * FROM users WHERE is_teacher != 1") or die(mysql_error());
        $no_of_rows = mysql_num_rows($result);
        $students = array();
        if ($no_of_rows > 0) {
            while ($row = mysql_fetch_array($result)) {
                $student = array();
                $student[] = $row['firstname'];
                $student[] = $row['lastname'];
                $student[] = $row['username'];
                array_push($students, $student);
            }
            return $students;
        } else {
            return false;
        }
    }

    public function getGroupChatMessages($tblname, $username) {
        $group = array();
        $result = mysql_query("SELECT * from " . $tblname . " LIMIT 0,100") or die(mysql_error());
        if ($result == false) {
            return 0;
        }
        $i = 0;
        while ($row = mysql_fetch_array($result)) {
            //if the entry isnt false, select * from that group table where username = username
            if ($row != false) {
                $realname = $this->getUserRealName($row['username']);
                //$message = $realname . "::" . $row['message'] . "::" . $this->getDateAsTimestamp($row['created_on']);
                $group[$i]['realname'] = $realname;
                $group[$i]['message'] = $row['message'];
                $group[$i]['created_on'] = $this->getDateAsTimestamp($row['created_on']);
                $i++;
            }
        }
        error_log("\nGroup Messages JSON : " . json_encode($group), 3, "whatup.txt");
        return $group;
    }

    public function insertNote($cat, $title, $message, $metric) {
        $result = mysql_query("INSERT INTO teacher_notes(category, title, content, metric) VALUES('$cat', '$title', '$message', '$metric')") or die(mysql_error());
// check for successful store
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function setHelpRequest($name, $filename, $rating, $message) {
        $result = mysql_query("INSERT INTO help_entries(name, filename, rating, message, created_at) VALUES('$name', '$filename', '$rating', '$message', NOW())") or die(mysql_error());
// check for successful store
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function registerDevice($username, $deviceID) {
        //error_log("\nWHATS HAPPENING HERE : " . $result, 3, "whatup.txt");
        // is the user already registered with this device?
        $result = mysql_query("SELECT * FROM registered_devices WHERE device_id = '$deviceID' AND username = '$username'");
        $no_of_rows = mysql_num_rows($result);
        //if there was a result the device is already registered to the user, return
        if ($no_of_rows > 0) {
            return 0;
        }
        //If not is the device registered to a user?
        $result = mysql_query("SELECT * FROM registered_devices WHERE device_id = '$deviceID'");
        $no_of_rows = mysql_num_rows($result);
        //if there was a result, delete that row
        if ($no_of_rows > 0) {
            mysql_query("DELETE FROM registered_devices WHERE device_id = '$deviceID'");
        }
        //Is the user registered to a device? If so delete that row
        $result = mysql_query("SELECT * FROM registered_devices WHERE username = '$username'");
        $no_of_rows = mysql_num_rows($result);
        //if there was a result, delete that row
        if ($no_of_rows > 0) {
            mysql_query("DELETE FROM registered_devices WHERE username = '$username'");
        }
        //Register the device and user
        mysql_query("INSERT INTO registered_devices(device_id, username) VALUES('$deviceID', '$username')");
        return 0;
    }

    /**
     * Check user is existed or not
     */
    public function isUserExisted($username) {
        $result = mysql_query("SELECT username from users WHERE username = '$username'");
        $no_of_rows = mysql_num_rows($result);
        if ($no_of_rows > 0) {
// user existed 
            return true;
        } else {
// user not existed
            return false;
        }
    }

    public function isGroup($workingDIR) {
        $result = mysql_query("SELECT group_name from user_created_groups WHERE group_name = '$workingDIR'");
        $no_of_rows = mysql_num_rows($result);
        if ($no_of_rows > 0) {
// user existed 
            return true;
        } else {
// user not existed
            return false;
        }
    }

    public function deleteUser($username, $tbl) {
        $result = mysql_query("DELETE from " . $tbl . " WHERE username = '" . $username . "'") or die(mysql_error());
        if ($result) {
//echo "'$username' succesfully deleted from '$tbl'";
            return true;
        } else {
//echo "Error";
            return false;
        }
    }

    public function commitChatmessage($tblname, $username, $message) {
        $result = mysql_query("SELECT NOW() as 'now'");
        $row = mysql_fetch_array($result);
        $now = $row['now'];
        error_log("\nWHAT TIME IS IT : " . $now, 3, "whatup.txt");
        $result = mysql_query("INSERT INTO " . $tblname . "(username, message, created_on) VALUES('$username', '$message', '$now')") or die(mysql_error());
        if ($result) {
            return $now;
        } else {
            return 0;
        }
    }

    public function getDeviceIDs($users) {
        if (!is_array($users)) {
            $result = mysql_query("SELECT device_id FROM registered_devices WHERE username = '" . $users . "'");
            $value = mysql_fetch_row($result);
            return $value[0];
        } else {
            //error_log("\nSetting up result as array for : " . print_r($users, true), 3, "whatup.txt");
            $result = array();
            foreach ($users as $user) {
                $row = mysql_query("SELECT device_id FROM registered_devices WHERE username = '" . $user . "'");
                $value = mysql_fetch_row($row);
                //error_log("\nCommitting value for '$user' device id: '$value[0]' " . print_r($users, true), 3, "whatup.txt");
                if ($value[0] != null) {
                    $result[] = $value[0];
                    error_log("\nValue set" . print_r($users, true), 3, "whatup.txt");
                } else {
                    error_log("\nValue null no set " . print_r($users, true), 3, "whatup.txt");
                }
            }
            error_log("\nDevice IDs to send to : " . print_r($result, true), 3, "whatup.txt");
            return $result;
        }
    }

    public function getGroupMembers($group) {
        $result = mysql_query("SELECT * from " . $group . "") or die(mysql_error());
        $no_of_rows = mysql_num_rows($result);
        if ($no_of_rows > 0) {
            // user existed 
            $groupmembers = array();
            while ($row = mysql_fetch_assoc($result)) {
                //build array of usernames
                if ($row != false) {
                    $groupmembers[] = $row['username'];
                }
            }
            return $groupmembers;
        } else {
// not valid chatgroup / no members
            return false;
        }
    }
    
    public function updateHelpViewed($id){
        $result = mysql_query("UPDATE help_entries SET viewed WHERE id='$id'") or die(mysql_error());
    }

    public function getDateAsTimestamp($date) {
        $timestamp = strtotime($date);
        return $timestamp;
    }

}
