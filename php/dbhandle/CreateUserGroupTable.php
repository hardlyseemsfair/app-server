<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CreateTable
 *
 * @author JACK
 */
class CreateUserGroupTable {

    private $tag;
    private $response;

    function __construct($t, $r) {
        $this->tag = $t;
        $this->response = $r;
        include('/lib/DBFunctions.php');
        include('/lib/DIRFunctions.php');
    }

    // destructor
    function __destruct() {
        
    }

    function execute() {
        //post returning error using arrays on filter input
        $users = $_POST['members'];
        $groupname = filter_input(INPUT_POST, 'groupname');
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
        echo json_encode($this->response);
    }

    function registerGroupName($db, $groupname) {
        $result = $db->registerGroupName($groupname);
        return $result;
    }

}
