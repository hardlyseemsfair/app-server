<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CommitChatMessage
 *
 * @author JACK
 */
class ChatHandler {

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

    function executeCommitMessage() {
        $username = filter_input(INPUT_POST, 'username');
        $chatgroup = filter_input(INPUT_POST, 'chatgroup');
        $chatmessage = filter_input(INPUT_POST, 'chatmessage');
        $tblname = $chatgroup . "_CHAT";
        error_log("\nWHATS HAPPENING HERE response for chatgroup: " . $tblname, 3, "whatup.txt");
        $db = new DBFunctions();        
        $result = $db->commitChatmessage($tblname, $username, $chatmessage);
        if ($result != 0) {
            $this->response["success"] = 1;
        } else {
            $this->response["error"] = 1;
            $this->response["error_msg"] = "Failed to insert message";
        }
        $pushMessage = array();  
        $pushMessage['tag'] = "message_update";
        $pushMessage['chatgroup'] = $chatgroup;
        $pushMessage['realname'] = $db->getUserRealName($username);
        $pushMessage['message'] = $chatmessage;
        $pushMessage['created_on'] = strtotime($result);
        $pushHandler = new PushHandler($chatgroup);
        $pushHandler->push(json_encode($pushMessage));
        echo json_encode($this->response);
    }

    function executeGetGroupMessages() {
        $username = filter_input(INPUT_POST, 'username');
        $db = new DBFunctions();
        $groups = $db->getUserGroups($username);
        foreach ($groups as $group) {
            $messages = $db->getGroupChatMessages($group . "_CHAT", $username);     
            $this->response[$group] = $messages;
        }
        $this->response["success"] = 1;
        echo(json_encode($this->response));
    }
    
    function pushMessageUpdate($chatgroup, $message){     
        $deviceIDs = $this->getRecipientArray($chatgroup);
        $gcmPush = new GCMPushMessage($deviceIDs);
        $gcmPush->send($message, $deviceIDs);
        
    }
    


}
