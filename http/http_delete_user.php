<?php

require_once("../includes/functions.php");
session_start();

$request_legal = "false";

$user_id = user_logged_in();

if ($user_id != false) {
    $fields = array("permission_type");
    $user_data = user_data($user_id, $fields);

    if ($user_data["permission_type"] === "1") {
        if (!empty($_POST["data"])) {
            $post_data = json_decode($_POST["data"], true);

            if (user_id_exists($post_data["user_id"])) {
                $successful = delete_user($post_data["user_id"]);

                if ($successful) {
                    $request_legal = "true";
                } else {
                    $request_legal = "false";
                    $errors[] = "Er is een fout opgetreden. De sever meldt: \"" . $successful . "\"";
                }
            } else {
                $request_legal = "false";
                $errors[] = "Er is een fout opgetreden. Gebruiker bestaat niet.";
            }
        } else {
            $request_legal = "false";
            $errors[] = "verkeerde aanvraag.";
        }
    } else {
        $request_legal = "false";
        $errors[] = "Gebruiker geen administrator.";
    }
} else {
    $request_legal = "false";
    $errors[] = "Gebruiker niet ingelogd.";
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