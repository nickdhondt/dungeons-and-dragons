<?php

require_once("../includes/functions.php");
session_start();

$request_legal = "false";
$errors = array();
$user_list = array();

$user_id = user_logged_in();

if ($user_id != false) {
    $fields = array("permission_type");
    $user_data = user_data($user_id, $fields);

    if ($user_data["permission_type"] === "1") {
        $request_legal = "true";
        $user_list = get_user_list();
    } else {
        $request_legal = "false";
        $errors[] = "Gebruiker geen administrator.";
    }
} else {
    $request_legal = "false";
}

if ($request_legal === "true" && empty($errors)) {
    $return_message = array(
        "request_legal" => $request_legal,
        "data" => $user_list
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