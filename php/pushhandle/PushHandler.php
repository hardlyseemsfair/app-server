<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PushHandler
 *
 * @author NAPOLEON
 */
class PushHandler {
    //put your code here
    
    var $dir;
    
    function __construct($dir) {
        $this-> dir = $dir;    
        //include('/lib/DBFunctions.php');
        include('/lib/GCMPushMessage.php');
    }

    function __destruct() {
        
    }
    
    
    
    
    function push($message){
        $deviceIDs = $this->getIntendedDevices($this->dir);
        $gcmPush = new GCMPushMessage($deviceIDs);
        $gcmPush->send($message, $deviceIDs);
    }
    
    function getIntendedDevices($workingDIR){
        error_log("\nPushHandler looking at dir " . $workingDIR, 3, "whatup.txt");
        $db = new DBFunctions();
        if($db->isUserExisted($workingDIR)){
            error_log("\nSingle user proceeding ", 3, "whatup.txt");
            $db = new DBFunctions();
            $deviceID = $db->getDeviceIDs($workingDIR);
            return $deviceID;
        } else {
            error_log("\nCopying / Moving file pushing getting array", 3, "whatup.txt");
            return $this->getRecipientArray($workingDIR);
        }
    }
    
    function getRecipientArray($workingDIR){
        $db = new DBFunctions();
        $users = $db->getGroupMembers($workingDIR);
        if($users != false){
            $deviceIDs = $db->getDeviceIDs($users);
            return $deviceIDs;
        } else {
            echo "No result";
        }
    }
}
