<?php

require_once("../includes/functions.php");
session_start();

$errors = array();
$basic_items = array();
$request_legal = "false";

$user_id = user_logged_in();

if ($user_id != false) {
    $fields = array("permission_type");
    $user_data = user_data($user_id, $fields);

    if ($user_data["permission_type"] !== "1") {
        $request_legal = "false";
        $errors[] = "De gebruiker is geen administrator";
    } else {
        $request_legal = "true";
        $basic_items = list_basic_skillitems();
    }
} else {
    $request_legal = "false";
    $errors[] = "De gebruiker is niet ingelogd";
}

if ($request_legal === "true" && empty($errors)) {
    $return_message = array(
        "request_legal" => $request_legal,
        "data" => $basic_items
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