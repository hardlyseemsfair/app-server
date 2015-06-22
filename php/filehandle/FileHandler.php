<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FileHandler
 *
 * @author JACK
 */
class FileHandler {

    private $tag;
    private $response;
    private $type;

    function __construct($t, $r) {
        $this->tag = $t;
        $this->response = $r;
        $this->type = filter_input(INPUT_POST, 'type');
        include('Config.php');
        include('/lib/DIRFunctions.php');
        include('/lib/FHFunctions.php');
    }

    // destructor
    function __destruct() {
        
    }

    function executeFileCollection() {
        $username = filter_input(INPUT_POST, 'username');
        $workingDIR = filter_input(INPUT_POST, 'dir');
        if ($workingDIR == 'camera') {
            $path = USER_ROOT_DIR . $username . "/" . $workingDIR;
        } else {
            $path = USER_ROOT_DIR . $workingDIR;
        }
        error_log("\nWHATS HAPPENING HERE looking at path: " . $path, 3, "whatup.txt");
        $dir_handler = new DIRFunctions();
        $data = $dir_handler->getFileData($path);
        if ($data != false) {
            $this->response['success'] = 1;
            $this->response[$workingDIR] = $data;
        } else {
            $this->response["success"] = 0;
            $this->response["error_msg"] = "No files to process";
            $this->response['num_files'] = 0;
        }
        echo json_encode($this->response);
    }

    function executeFileUpload() {
        $filehandler = new FHFunctions();
        $username = filter_input(INPUT_POST, 'username');
        $workingDIR = filter_input(INPUT_POST, 'dir');
        $filename = "";
        $filesize = 0;
        if (!isset($_FILES)) {
            error_log("FILES not set");
            $this->response['error'] = 1;
            $this->response['error_msg'] = "FILES var not set";
        } else {
            if ($_FILES['file']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES;
                error_log("Processing file " . $file["file"]["name"]);
                $filename = $file["file"]["name"];
                $filesize = $filehandler->fileFromDevice($username, $workingDIR, $file);
                if (!($filesize === false || $filesize === 0)) {
                    $this->response['success'] = 1;
                }
            } else {
                error_log("\nWHATS HAPPENING HERE file upload error: " . $_FILES['file']['error'], 3, "whatup.txt");
                $this->response['error'] = 1;
                $this->response['error_msg'] = "Failed to upload file";
            }
        }
        //push the relevant info to push
        //if the file is uploaded to camera it can only be done one the users device
        //so no push requirement
        require_once '/lib/DBFunctions.php';
        if ($workingDIR != 'camera' && $filename != "") {
            include('/pushhandle/PushHandler.php');
            $message['tag'] = 'server_file_changed';
            $message['note'] = 'fileuploaded to server, sync devices';
            $message['filename'] = $filename;
            $message['workingDIR'] = $workingDIR;
            $message['filesize'] = $filesize;
            $pushHandler = new PushHandler($workingDIR);
            $pushHandler->push(json_encode($message));
        }
        echo json_encode($this->response);
    }

    public function executeDeleteFile() {
        $filehandler = new FHFunctions();
        $filename = filter_input(INPUT_POST, 'filename');
        $workingDIR = filter_input(INPUT_POST, 'dir');
        $username = filter_input(INPUT_POST, 'username');
        if ($workingDIR == "Camera") {
            $workingDIR = $username . "/" . $workingDIR;
        }
        error_log("\nWHATS FileHandler trying to delete: " . $workingDIR, 3, "whatup.txt");
        if ($filehandler->deleteFile($filename, $workingDIR) == false) {
            $this->response['error'] = 1;
            $this->response['error_msg'] = "Failed to delete file";
        } else {
            $this->response['success'] = 1;
        }
        require_once '/lib/DBFunctions.php';
        $db = new DBFunctions();
        if ($db->isGroup($workingDIR)) {
            include('/pushhandle/PushHandler.php');
            $message['filename'] = $filename;
            $message['workingDIR'] = $workingDIR;
            $message['tag'] = "delete_file";
            $message['note'] = 'group copy deleted, delete local device copy';
            $pushHandler = new PushHandler($workingDIR);
            $pushHandler->push(json_encode($message));
        }
        echo json_encode($this->response);
    }

    public function renameFile() {
        $fhfunctions = new FHFunctions();
        error_log("\nRenaming server file", 3, "whatup.txt");
        $newFilename = filter_input(INPUT_POST, 'new_filename');
        $workingDIR = filter_input(INPUT_POST, 'workingDIR');
        if ($workingDIR == 'camera') {
            $workingDIR = filter_input(INPUT_POST, 'username') . "/" . $workingDIR;
        }
        $oldFilename = filter_input(INPUT_POST, 'old_filename');
        error_log("\nRenaming server file '$oldFilename' to '$newFilename'", 3, "whatup.txt");
        if ($fhfunctions->renameFile($newFilename, $workingDIR, $oldFilename)) {
            $this->response['success'] = 1;
        } else {
            $this->response['error'] = 1;
            $this->response['error_msg'] = "Failed to rename file";
        }
        require_once '/lib/DBFunctions.php';
        $db = new DBFunctions();
        if ($db->isGroup($workingDIR)) {
            error_log("\nRenaming commiting push", 3, "whatup.txt");
            include('/pushhandle/PushHandler.php');
            $message['new_filename'] = $newFilename;
            $message['old_filename'] = $oldFilename;
            $message['workingDIR'] = $workingDIR;
            $message['tag'] = "rename_file";
            $message['note'] = 'rename group files when renamed';
            $pushHandler = new PushHandler($workingDIR);
            $pushHandler->push(json_encode($message));
        }
        echo json_encode($this->response);
    }

    public function executeFileMoveCopy() {
        $filehandler = new FHFunctions();
        $filename = filter_input(INPUT_POST, "filename");
        $sourceDIR = filter_input(INPUT_POST, "sourceDIR");
        $destDIR = filter_input(INPUT_POST, "destDIR");
        $mask = filter_input(INPUT_POST, "mask");
        $res;
        $this->response['success'] = 1;
        if ($mask == "file_moved") {
            $res = $filehandler->moveFile($filename, $sourceDIR, $destDIR);
            if ($res == false) {
                $this->response['success'] = 0;
                $this->response['error'] = 1;
                $this->response['error_msg'] = "Failed to move file";
            } else if ($res == "FileAlreadyExists") {
                $this->response['success'] = 0;
                $this->response['error'] = 1;
                $this->response['error_msg'] = "FileAlreadyExists";
            }
        } else if ($mask == "file_copy") {
            $res = $filehandler->copyFile($filename, $sourceDIR, $destDIR);
            if ($res == false) {
                $this->response['error'] = 1;
                $this->response['error_msg'] = "Failed to copy file";
                $this->response['success'] = 0;
            } else if ($res == "FileAlreadyExists") {
                $this->response['error'] = 1;
                $this->response['error_msg'] = "FileAlreadyExists";
                $this->response['success'] = 0;
            }
        } else {
            $this->response['error'] = 1;
            $this->response['error_msg'] = "UnseenError";
            $this->response['success'] = 0;
        }
        error_log("\nCopying / Moving file result: " . $res, 3, "whatup.txt");
        if ($res != false ) {
            error_log("\nCopying / Moving file pushing " . $destDIR, 3, "whatup.txt");
            require_once '/lib/DBFunctions.php';
            include('/pushhandle/PushHandler.php');
            $message['tag'] = 'server_file_changed';
            $message['note'] = 'file moved on server, sync devices';
            $message['filename'] = $filename;
            $message['workingDIR'] = $destDIR;
            $message['filesize'] = -1;
            $pushHandler = new PushHandler($destDIR);
            $pushHandler->push(json_encode($message));
        }
        echo json_encode($this->response);
    }

}
