<?php

class Register {

    private $tag;
    private $response;

    function __construct($t, $r) {
        $this->tag = $t;
        $this->response = $r;
        include('/lib/DBFunctions.php');
        include('/lib/DIRFunctions.php');
    }

    // destructor
    function __destruct() {
        
    }

    function execute() {
        // Request type is Register new user
        $firstname = filter_input(INPUT_POST, 'firstname');
        $lastname = filter_input(INPUT_POST, 'lastname');
        $username = filter_input(INPUT_POST, 'username');
        $password = filter_input(INPUT_POST, 'password');
        $is_teacher = filter_input(INPUT_POST, 'is_teacher');
        $db = new DBFunctions();
         error_log("\nRegister Data: '$firstname' : '$lastname' : '$username' : '$password' ", 3, "whatup.txt");
        // check if user is already existed
        if ($db->isUserExisted($username)) {
            // user is already existed - error response
            $this->response["error"] = 2;
            $this->response["error_msg"] = "UAEX";
            echo json_encode($this->response);
        } else {
            // store user
            $user = $db->storeUser($firstname, $lastname, $username, $password, $is_teacher);
            if ($user) {
                // user stored successfully
                $this->response["success"] = 1;
                $this->response["user"]["username"] = $user["username"];
                $this->response["user"]["created_at"] = $user["created_at"];               
                $dir_handler = new DIRFunctions();
                $dir_handler->createDir($username);
                $dir_handler->createDir($username . "/camera");
                echo json_encode($this->response);
            } else {
                // user failed to store

                $this->response["error"] = 2;
                $this->response["error_msg"] = "Error occured in Registration";
                echo json_encode($this->response);
            }
        }
    }

}
