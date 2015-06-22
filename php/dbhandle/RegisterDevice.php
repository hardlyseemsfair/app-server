<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RegisterDevice
 *
 * @author JACK
 */
class RegisterDevice {

    //put your code here
    private $tag;
    private $response;

    function __construct($t, $r) {
        $this->tag = $t;
        $this->response = $r;
        include('/lib/DBFunctions.php');
    }

// destructor
    function __destruct() {
        
    }

    function execute() {
        $username = filter_input(INPUT_POST, 'username');
        $deviceID = filter_input(INPUT_POST, 'gcmID');
        $db = new DBFunctions();       
        $result = $db->registerDevice($username, $deviceID);
        $groups = $db->getUserGroups($username);
        $this->response["groups"] = $groups;
        error_log("\nWHATS HAPPENING HERE response for success: " . json_encode($this->response), 3, "whatup.txt");
        $this->response["success"] = 1;
       
        echo json_encode($this->response);
    }
    


}
