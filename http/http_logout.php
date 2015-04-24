<?php

require_once "../includes/functions.php";

// Empty the session superglobal
session_unset();
session_destroy();

$response_message = array(
    "logged_out" => "true"
);

echo json_encode($response_message);