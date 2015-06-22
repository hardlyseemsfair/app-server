<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of NoteHandler
 *
 * @author p.armstrong
 */
class NoteHandler {
    //put your code here
    
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
    
    function execute(){
        if($this->tag == "commit_notes"){
            $this->commiteNotes();
        } else if ($this->tag == "get_notes"){
            $this->getNotes();
        }else if ($this->tag == "delete_note"){
            $this->deleteNote();
        }
        
    }
    
    function commiteNotes(){
        $db = new DBFunctions();
        //$notes = array();
        $notes = $_POST['notes'];
        error_log("\nNotes post array: " . print_r($notes, true), 3, "whatup.txt");
        foreach ($notes as $note){
            $cat = $note['category'];
            $title = $note['title'];
            $message = $note['message'];
            $metric = $note['metric'];
            $db->insertNote($cat, $title, $message, $metric);
        }
        $this->response['success'] = 1;    
        echo json_encode($this->response);
    }
    
    
    function getNotes(){
        $db = new DBFunctions();
        $notes = $db->getTeacherNotes();
        $this->response['notes'] = $notes;
        $this->response['success'] = 1;    
        echo json_encode($this->response);
    }
    
    function deleteNote(){
        $db = new DBFunctions();
        $id = filter_input(INPUT_POST, 'id');
        $notes = $db->deleteTeacherNote($id);
        $this->response['success'] = 1;    
        echo json_encode($this->response);
    }
}
