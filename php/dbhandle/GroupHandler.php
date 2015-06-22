<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GroupHandler
 *
 * @author p.armstrong
 */
class GroupHandler {

    //put your code here

    private $tag;
    private $response;

    function __construct($t, $r) {
        $this->tag = $t;
        $this->response = $r;
        include('/lib/DBFunctions.php');
        include('/lib/DIRFunctions.php');
        include('/pushhandle/PushHandler.php');
    }

// destructor
    function __destruct() {
        
    }

    function execute() {
        error_log("\nGroup class", 3, "whatup.txt");
        if ($this->tag == 'get_student_list') {
            $this->getStudentList();
        } else if ($this->tag == 'create_student_group') {
            $this->createStudentGroup();
        } else if ($this->tag == "get_group_members") {
            $this->getGroupMembers();
        } else if ($this->tag == 'delete_group') {
            $this->deleteGroup();
        }
    }

    function createStudentGroup() {
        $users = $_POST['members'];
        $groupname = filter_input(INPUT_POST, 'groupname');
        //Register groupname
        error_log("\nGroup '$groupname' info : " . print_r($users, TRUE), 3, "whatup.txt");
        $db = new DBFunctions();
        //Register groupname
        $result = $this->registerGroupName($db, $groupname);
        if ($result == false) {
            $this->response["success"] = 1;
            $this->response["error"] = 1;
            $this->response["error_msg"] = "Failed to register group. Name taken.";
            echo json_encode($this->response);
            return;
        }
        //Create the group tables and insert users
        $result = $db->createGroupTables($groupname, $users);
        //Create the group directory     
        if ($result == 0) {
            $dir_handler = new DIRFunctions();
            if ($dir_handler->createDir($groupname)) {
                $this->response["success"] = 1;
            } else {
                $this->response["error"] = 2;
                $this->response["error_msg"] = "Username deleted";
            }
        } else if ($result == 1) {
            $this->response["error"] = 1;
            $this->response["error_msg"] = "Failed to create group table";
        } else if ($result == 2) {
            $this->response["error"] = 1;
            $this->response["error_msg"] = "Failed to create group chat table";
        } else if ($result == 3) {
            $this->response["error"] = 1;
            $this->response["error_msg"] = "Error inserting users into table";
        } else if ($result == 4) {
            $this->response["error"] = 2;
            $this->response["error_msg"] = "Undefined error";
        }
        $pushMessage = array();
        $pushMessage['tag'] = "added_to_group";
        $pushMessage['group_name'] = $groupname;
        $pushHandler = new PushHandler($groupname);
        $pushHandler->push(json_encode($pushMessage));
        echo json_encode($this->response);
    }

    function registerGroupName($db, $groupname) {
        $result = $db->registerGroupName($groupname);
        return $result;
    }

    function getStudentList() {
        error_log("\nList func", 3, "whatup.txt");
        $db = new DBFunctions();
        $studentlist = $db->getStudentList();
        $this->response['success'] = 1;
        $this->response['students'] = $studentlist;
        echo json_encode($this->response);
    }
    
    function getGroupMembers(){
        error_log("\nGetting group members", 3, "whatup.txt");
        $db = new DBFunctions();
        $groupname = filter_input(INPUT_POST, 'group_name');
        $members = array();
        if($db->isGroup($groupname)){
            error_log("\nGroup exists: " . $groupname, 3, "whatup.txt");
            $membersUsername = $db->getGroupMembers($groupname);
            foreach($membersUsername as $username){
                $user = array();
                $name = explode(" ", $db->getUserRealName($username));
                $user[] = $name[0];
                $user[] = $name[1];
                $user[] = $username;
                array_push($members, $user);
            }
        }
        $this->response['success'] = 1;
        $this->response['group'] = $members;     
        echo json_encode($this->response);
    }

    function deleteGroup(){
        error_log("\nDeleting group ", 3, "whatup.txt");
        $db = new DBFunctions();
        $groupname = filter_input(INPUT_POST, 'group_name');
        $chatTable = $groupname ."_CHAT";
        error_log("\nDeleting tables  '$groupname' and '$chatTable' " , 3, "whatup.txt");
        $db->dropGroupTable($groupname);
        $db->dropGroupTable($chatTable);
        error_log("\nDeleting group entry ", 3, "whatup.txt");
        $db->deleteGroupEntry($groupname); 
        $this->response['success'] = 1;    
        echo json_encode($this->response);
        
    }
}
