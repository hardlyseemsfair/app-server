<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Login {

    private $tag;
    private $response;

    function __construct($t, $r) {
 //       $this->setIncludePath();
        $this->tag = $t;
        $this->response = $r;
        include("/lib/DBFunctions.php");
    }

    // destructor
    function __destruct() {
        
    }

    function execute() {
        // Request type is check Login
        $username = filter_input(INPUT_POST, 'username');
        $password = filter_input(INPUT_POST, 'password');
        $db = new DBFunctions();
        // check for user
        $user = $db->getUserByUsernameAndPassword($username, $password);
        if ($user != false) {
            // user found
            // echo json with success = 1
            $this->response["success"] = 1;
            //$response["uid"] = $user["u_id"];
            //$response["user"]["name"] = $user["name"];
            $this->response["user"]["username"] = $user["username"];
            $this->response["user"]["is_teacher"] = $user["is_teacher"];
            $this->response["user"]["created_at"] = $user["created_at"];
            //echo("Login Succesful");
            echo json_encode($this->response);
        } else {
            // user not found
            // echo json with error = 1
            $this->response["error"] = 2;
            $this->response["error_msg"] = "Incorrect username or password!";
            echo json_encode($this->response);
        }
    }

//    function setIncludePath(){
//        $serverroot = filter_input(INPUT_SERVER, 'DOCUMENT ROOT');
//        set_include_path($serverroot);
//    }
}
