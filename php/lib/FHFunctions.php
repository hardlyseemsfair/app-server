<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class FHFunctions {

    public function __construct() {
        require_once 'config.php';
    }

    public function __destruct() {
        
    }

    public function fileFromDevice($username, $dir, $file) {
        $path;
        if ($dir == 'camera') {
            $path = USER_ROOT_DIR . $username . "/camera";
        } else {
            $path = USER_ROOT_DIR . $dir;
        }
        //$path = USER_ROOT_DIR . $dir;
        error_log("Uploading to '$path'");
        if (empty($file)) {
            error_log("$file IS EMPTY");
            return false;
        }
        $filename = $file['file']['name'];
        if (is_uploaded_file($file['file']['tmp_name'])) {
            $tmpname = $file['file']['tmp_name'];
        } else {
            error_log("'$filename' not a file");
            return false;
        }
        if (move_uploaded_file($tmpname, $path . "/" . $filename)) {
            return filesize($path . "/" . $filename);
        } else {
            return false;
        }
    }
    
    
    public function deleteFile($filename, $dir){
       $filepath = USER_ROOT_DIR . $dir . "/" . $filename;
        //$fh = fopen($filepath);
        error_log("\nWHATS HAPPENING HERE trying to delete: " . $filepath , 3, "whatup.txt");
        return unlink($filepath);
    }
    
    
    public function renameFile($newFilename, $dir, $oldFilename){
        $oldfilepath = USER_ROOT_DIR . $dir . "/" . $oldFilename;
        $newfilepath = USER_ROOT_DIR . $dir . "/" . $newFilename;
        return rename($oldfilepath, $newfilepath);
    }
    
    public function moveFile($filename, $sourceDIR, $destDIR){
        $sourcepath = USER_ROOT_DIR . $sourceDIR . "/" . $filename;
        $destpath = USER_ROOT_DIR . $destDIR . "/" . $filename;
        if(!file_exists($destpath)){
            return rename($sourcepath, $destpath);
        } else {
            return "FileAlreadyExists";
        }
    }
    
    public function copyFile($filename,$sourceDIR, $destDIR){
        $sourcepath = USER_ROOT_DIR . $sourceDIR . "/" . $filename;
        $destpath = USER_ROOT_DIR . $destDIR . "/" . $filename;
        if(!file_exists($destpath)){
            return copy($sourcepath, $destpath);
        } else {
            return "FileAlreadyExists";
        }
    }

}
