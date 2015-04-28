<?php

require_once("../includes/functions.php");
session_start();

$errors = array();
$request_legal = "false";
$user_data = array();
$races = array();

$user_id = user_logged_in();

if ($user_id != false) {
    $races = get_races();
    $classes = get_classes();
    $request_legal = "true";

}else {
    // Set flag to false
    $request_legal = "false";
    $errors[] = "Gebruiker niet ingelogd.";
}

if ($request_legal === "true" && empty($errors)) {
    $return_message = array(
        "request_legal" => $request_legal,
        "data" => array(
            "races" => $races,
            "classes" => $classes
        )
    );
} else {
    $return_message = array(
        "request_legal" => $request_legal,
        "errors" => $errors
    );
}

// JSON encode our array
$return_data = json_encode($return_message);

// "Return" the json string to the client
echo $return_data;