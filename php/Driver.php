<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Driver {

    
    private $tag;
    private $response;

    function __construct($t, $r) {
        $this->tag = $t;
        $this->response = $r;
    }

    // destructor
    function __destruct() {
        
    }

    function execute() {
        switch ($this->tag) {
            case "login" : include('/access/Login.php');
                $login = new Login($this->tag, $this->response);
                $login->execute();
                break;
            case "register" : include('/access/Register.php');
                $register = new Register($this->tag, $this->response);
                $register->execute();
                break;
            case "delete" : include('/dbhandle/DeleteUser.php');
                $deleteuser = new DeleteUser($this->tag, $this->response);
                $deleteuser->execute();
                break;
            case "drop_table" : include('/dbhandle/DropCustomTable.php');
                $dropusertable = new DropCustomTable($this->tag, $this->response);
                $dropusertable->execute();
                break;
            case "get_dir_file_list" : include('/filehandle/FileHandler.php');
                $filecheck = new FileHandler($this->tag, $this->response);
                $filecheck->executeFileCollection();
                break;
            case "file_upload" : include('/filehandle/FileHandler.php');
                $fileupload = new FileHandler($this->tag, $this->response);
                $fileupload->executeFileUpload();
                break;
            case "file_delete" : include('/filehandle/FileHandler.php');
                $fileupload = new FileHandler($this->tag, $this->response);
                $fileupload->executeDeleteFile();
                break;
            case "user_help_request" : include('/dbhandle/HelpRequestHandler.php');
                $helprequest = new HelpRequestHandler($this->tag, $this->response);
                $helprequest->execute();
                break;
            case "get_help_messages" : include('/dbhandle/HelpRequestHandler.php');
                $helprequest = new HelpRequestHandler($this->tag, $this->response);
                $helprequest->execute();
                break;
            case "register_device" : include('/dbhandle/RegisterDevice.php');
                $registerdevice = new RegisterDevice($this->tag, $this->response);
                $registerdevice->execute();
                break;
             case "commit_message" : include('/dbhandle/ChatHandler.php');
                $commitchatmessage = new ChatHandler($this->tag, $this->response);
                $commitchatmessage->executeCommitMessage();
                break;
             case "get_group_message" : include('/dbhandle/ChatHandler.php');
                $commitchatmessage = new ChatHandler($this->tag, $this->response);
                $commitchatmessage->executeGetGroupMessages();
                break;
            case "rename_file" : include('/filehandle/FileHandler.php');
                $fileRename = new FileHandler($this->tag, $this->response);
                $fileRename->renameFile();
                break;
            case "move_copy_server_file" : include('/filehandle/FileHandler.php');
                $fileMoveCopy = new FileHandler($this->tag, $this->response);
                $fileMoveCopy->executeFileMoveCopy();
                break;
            case "get_student_list" : include('/dbhandle/GroupHandler.php');
                $groupHandler = new GroupHandler($this->tag, $this->response);
                $groupHandler->execute();
                break;
            case "create_student_group" : include('/dbhandle/GroupHandler.php');
                $groupHandler = new GroupHandler($this->tag, $this->response);
                $groupHandler->execute();
                break;
            case "get_group_members" : include('/dbhandle/GroupHandler.php');
                $groupHandler = new GroupHandler($this->tag, $this->response);
                $groupHandler->execute();
                break;
            case "delete_group" : include('/dbhandle/GroupHandler.php');
                $groupHandler = new GroupHandler($this->tag, $this->response);
                $groupHandler->execute();
                break;
           
            case "get_notes" : include('/dbhandle/NoteHandler.php');
                $noteHandler = new NoteHandler($this->tag, $this->response);
                $noteHandler->execute();
                break;
            case "delete_note" : include('/dbhandle/NoteHandler.php');
                $noteHandler = new NoteHandler($this->tag, $this->response);
                $noteHandler->execute();
                break;
             case "commit_notes" : 
                    include('/dbhandle/NoteHandler.php');
                    $noteHandler = new NoteHandler($this->tag, $this->response);
                    $noteHandler->execute();
                    break;
            case "commit_help_changes":    
                    include('/dbhandle/HelpRequestHandler.php');
                    $helpRequestHandler = new HelpRequestHandler($this->tag, $this->response);
                    $helpRequestHandler->execute();
                break;
            case "delete_help":    
                    include('/dbhandle/HelpRequestHandler.php');
                    $helpRequestHandler = new HelpRequestHandler($this->tag, $this->response);
                    $helpRequestHandler->execute();
                break;
            default : echo 'malformed request. go away';
        }
    }

}
