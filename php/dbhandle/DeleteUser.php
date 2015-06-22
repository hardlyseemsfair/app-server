<?php

class DeleteUser {

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
        $username = filter_input(INPUT_POST,'username');
        $tbl = filter_input(INPUT_POST,'table');
        $db = new DBFunctions();
        if ($db->isUserExisted($username)) {
            // user is already existed - error response
            if ($db->deleteUser($username, $tbl)) {
                $this->response["success"] = 1;
            } else {
                $this->response["error"] = 1;
                $this->response["error_msg"] = "Error deleteing user '$username'";
            }
        } else {
            $this->response["error"] = 1;
            $this->response["error_msg"] = "Error deleteing user '$username'";
        }
        echo json_encode($this->response);
    }

}
