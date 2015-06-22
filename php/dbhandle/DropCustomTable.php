<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DropCustomTable
 *
 * @author JACK
 */
class DropCustomTable {
  
      private $tag;
    private $response;

    function __construct($t, $r) {
        $this->tag = $t;
        $this->response = $r;
        include('../lib/DBFunctions.php');
    }

    // destructor
    function __destruct() {
        
    }

    function execute() {
    $tblname = filter_input(INPUT_POST,'table_name');
    $db = new DB_Functions();
    if ($db->dropGroupTable($tblname)) {
        echo "Table '$tblname' dropped";
        $this->response["success"] = 1;
    } else {
        $this->response["error"] = 1;
        $this->response["error_msg"] = "Error deleting table '$tblname'";
    }
    echo json_encode($this->response);
    }

    
}
