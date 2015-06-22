<?php

include '/php/Driver.php';

$tag = filter_input(INPUT_POST, 'tag');
$response = array("tag" => $tag, "success" => 0, "error" => 0);

$driver = new Driver($tag, $response);
$driver->execute();


?>

