<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of gcm_sender
 *
 * @author JACK
 */
include("/lib/GCMPushMessage.php");
$apiKeyB = "AIzaSyDks90u35PooO0bUCdal8UPoA6B5byl4bE";
$apiKeyS = "AIzaSyDz2ovkzjtd0rX5iqUa6uEfNc-iUvaWcsU";
$an = new GCMPushMessage($apiKeyS);
$devices = $_POST["devicekey"];
$an->setDevices($devices);
$message = $_POST["message"];
$response = $an->send($message);
echo $response;


