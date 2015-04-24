<?php

require_once("../includes/functions.php");

$errors = array();
$data_acquired = "false";
$user_data = array();

$user_id = user_logged_in();

// Check if the user is logged in, user_logged_in() return false if no user is logged in.
if ($user_id != false) {
    // Check if post data is received
    // Post data contains "false" or a user id. If it contains false, the user data will be sent based on the user id in the session.
    // If it contains a user id, the user data than will be sent back can (and probably will be) from another user.
    // The Admin can see other users data this way.
    if (empty($_POST["data"])) {
        // Set flag to false and make error message
        $data_acquired = "false";
        $errors[] = "Foute of geen data ontvangen.";
    } else {
        // Set flag to true
        $data_acquired = "true";
        // Decode the json (second parameter must be true!)
        $post_data = json_decode($_POST["data"], true);

        // Get the user data based on the user id.
        // posted user id => false => get user data based on session
        // posted user id => "id integer" => get user data based on the sent id
        $fields = array("user_id", "username", "permission_type");
        if ($post_data["user_id"] === "false") $user_data = user_data($user_id, $fields);
        else $user_data = user_data($post_data["user_id"], $fields);

        // Get the permission name (Gebruiker, Administrator, etc.)
        $permission_name = permission_name($user_data["permission_type"]);

        $user_data["permission_name"] = $permission_name;
    }
} else {
    // Set flag to false
    $data_acquired = "false";
}

// Prepare the return data
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