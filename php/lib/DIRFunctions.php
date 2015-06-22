<?php

class DIRFunctions {

    public function __construct() {
        require_once 'config.php';
    }

    public function __destruct() {
        
    }

    public function getFileNames($path) {
        $filenames = scandir($path);
        //unset($filenames[0]);
        //unset($filenames[1]);
        return $filenames;
    }

    public function numFiles($dir){
        $path = USER_ROOT_DIR . $dir;
        $f = $this->getFileNames($path);
        return count($f);
    }
    
    public function getFileData($dir) {
        $fileinfo = array();
        $files = $this->getFileNames($dir);
        if (false !== $files) {
            foreach ($files as $file) {
                $filestring =  $file . "::" . filesize($dir . "/" . $file) . "::" . filemtime($dir . "/" . $file);
                array_push($fileinfo, $filestring);
            }
            return $fileinfo;
        } else {
            return false;
        }
    }


//Get file sizes and return sizes as an array
    public function getFileSizes($filenames, $path) {
        $filesizes = array();
        foreach ($filenames as $filename) {
            array_push($filesizes, filesize($path . "/" . $filename));
        }
        return $filesizes;
    }

//Get file date last modified and return as array
    public function getFilesLastModified($filenames, $path) {
        $filedates = array();
        foreach ($filenames as $filename) {
            array_push($filedates, filemtime($path . "/" . $filename));
        }
        return $filedates;
    }

//provide the path as argument

    public function createDir($dirname) {
        $path = USER_ROOT_DIR . $dirname;
        if (!file_exists($path)) {
            //echo "Creating folder '$dirname' at " . $path;
            mkdir($path, 0777);
            if (file_exists($path)) {
                //echo "Directory '$dirname' created";
                return true;
            } else {
                //echo "Error creating directory '$dirname'";
                return false;
            }
        } else {
            return true;
        }
    }

}

?>
