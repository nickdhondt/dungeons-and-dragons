<?php

require_once("../includes/functions.php");

$errors = array();
$data_acquired = "false";
$user_data = array();

$user_id = user_logged_in();

if ($user_id != false) {
    if (empty($_POST["data"])) {
        $data_acquired = "false";
        $errors[] = "Foute of geen data ontvangen.";
    } else {
        $data_acquired = "true";
        $post_data = json_decode($_POST["data"], true);

        $fields = array("user_id", "username", "permission_type");
        if ($post_data["user_id"] === "false") $user_data = user_data($user_id, $fields);
        else $user_data = user_data($post_data["user_id"], $fields);

        $permission_name = permission_name($user_data["permission_type"]);

        $user_data["permission_name"] = $permission_name;
    }
} else {
    $data_acquired = "false";
}

if ($data_acquired === "true" && empty($errors)) {
    $return_message = array(
        "request_accepted" => $data_acquired,
        "data" => $user_data
    );
} else {
    $return_message = array(
        "request_accepted" => $data_acquired,
        "errors" => $errors
    );
}

// JSON encode our array
$return_data = json_encode($return_message);

// "Return" the json string to the client
echo $return_data;