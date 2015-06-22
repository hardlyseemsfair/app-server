<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of HelpRequestHandler
 *
 * @author JACK
 */
class HelpRequestHandler {

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

        if ($this->tag == 'user_help_request') {
            $this->addHelpRequestEntry();
        } else if ($this->tag == 'get_help_messages') {
            //teacher polling help messages
            $this->getHelpMessages();
        } else if ($this->tag == 'commit_help_changes'){
            //update table data
            $this->commiteHelp();
        } else if ($this->tag == 'delete_help'){
            $this->deleteHelpMessage();
        } else {
//un authorized access to class
        }
    }

    function addHelpRequestEntry() {
        $db = new DBFunctions();
        $username = filter_input(INPUT_POST, 'username');
        $filename = filter_input(INPUT_POST, 'filename');
        $rating = filter_input(INPUT_POST, 'rating');
        $message = filter_input(INPUT_POST, 'message');
        //would be good to catch this being empty - todo later
        $realname = $this->getUserActualName($db, $username);
        if (!$db->setHelpRequest($realname, $filename, $rating, $message)) {
            $this->response["error"] = 0;
            $this->response["error_msg"] = "Help request failed";
        } else {
            $this->response["success"] = 1;
        }
        $help = $db->getLastHelpEntry();
        if ($help != false) {
            include('/pushhandle/PushHandler.php');
            $helpMessage['tag'] = 'new_help_message';
            $helpMessage['note'] = 'help message added to table';
            $helpMessage['student_name'] = $help['name'];
            $helpMessage['filename'] = $help['filename'];
            $helpMessage['rating'] = $help['rating'];
            $helpMessage['helpmessage'] = $help['message'];
            $helpMessage['id'] = $help['id'];
            $helpMessage['viewed'] = $help['viewed'];
            $pushHandler = new PushHandler($db->getTeacher());
            $pushHandler->push(json_encode($helpMessage));
        }
        echo json_encode($this->response);
    }

    function getUserActualName($db, $username) {
        $name = $db->getUserRealName($username);
        if ($name != false) {
            return $name;
        } else {
            return false;
        }
    }

    function getHelpMessages() {
        $db = new DBFunctions();
        $helpMessages = $db->getHelpEntries();
        if($helpMessages != false){
            $this->response['help_messages'] = $helpMessages;
            $this->response['success'] = 1;
        } else {
            $this->response['success'] = 0;
            $this->response['error'] = 1;
            $this->response['error_msg'] = "No values in help entry";
        }
        echo json_encode($this->response);
    }

    function commiteHelp(){
        $db = new DBFunctions();
        //$notes = array();
        $help = $_POST['help'];
        error_log("\nHelp post array: " . print_r($notes, true), 3, "whatup.txt");
        foreach ($help as $h){
            $id = $h['id'];
            $db->updateHelpViewed($id);
        }
        $this->response['success'] = 1;    
        echo json_encode($this->response);
    }
    
    function deleteHelpMessage(){
        $db = new DBFunctions();
        $id = filter_input(INPUT_POST, 'id');
        $db->deleteHelpMessage($id);
        $this->response['success'] = 1;    
        echo json_encode($this->response);
    }
}
