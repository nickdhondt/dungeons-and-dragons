<?php

require_once("../includes/functions.php");
session_start();

$errors = array();
$request_legal = "false";

$user_id = user_logged_in();

if ($user_id != false) {
    $post_data = json_decode($_POST["data"], true);

    if (empty($post_data["user_id"])) {
        $errors[] = "Er is een fout opgetreden: \"geen user id ontvangen\"";
        $request_legal = "false";
    }

    $fields = array("permission_type");
    $user_data = user_data($user_id, $fields);

    if ($user_data["permission_type"] !== "1" && $post_data["user_id"] != $user_id) {
        $request_legal = "false";
        $errors[] = "De gebruiker is geen administrator.";
    } else {
        if ($post_data["add_exp_value"]) {
            $request_legal = "true";

            write_to_user_basic($post_data["user_id"], 11, $post_data["add_exp_value"]);
        } else {
            $request_legal = "false";
            $errors[] = "Geen exp data ontvangen.";
        }
    }
} else {
    $request_legal = "false";
    $errors[] = "De gebruiker is niet ingelogd";
}

if ($request_legal === "true" && empty($errors)) {
    $return_message = array(
        "request_legal" => $request_legal
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